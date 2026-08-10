<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Reads delivery failures back out of the sending mailbox over IMAP.
 *
 * This exists because `sent` in the mail queue does not mean "the recipient
 * got it". It means our own mail server accepted the message for relay. What
 * happens next - the recipient's server refusing it, a mailbox that does not
 * exist, a tenant that blocks outside senders - comes back minutes or hours
 * later as a bounce addressed to the sending mailbox, where nobody was
 * looking. That is how a message that was never delivered ends up sitting in
 * the queue marked `sent`.
 *
 * Only enough IMAP is implemented to log in, search for bounces and read
 * them; nothing is ever deleted or modified, and messages are fetched with
 * BODY.PEEK so they are not even marked as read.
 */
class Mail_bounces
{
	/** @var resource|null */
	private $socket = NULL;

	/** @var int incrementing IMAP command tag */
	private $tag = 0;

	/** @var string */
	private $error = '';

	// ------------------------------------------------------------------

	/**
	 * Connect and log in.
	 *
	 * @param	array	$config	host, port, user, pass, timeout
	 * @return	bool
	 */
	public function connect(array $config)
	{
		$host    = isset($config['host']) ? $config['host'] : '';
		$port    = isset($config['port']) ? (int) $config['port'] : 993;
		$timeout = isset($config['timeout']) ? (int) $config['timeout'] : 20;

		if ($host === '' OR empty($config['user']))
		{
			$this->error = 'No mailbox is configured to read bounces from.';
			return FALSE;
		}

		$errno = 0;
		$errstr = '';

		// Port 993 is implicit TLS; anything else is assumed plain.
		$target = (($port === 993) ? 'ssl://' : '').$host.':'.$port;
		$socket = @stream_socket_client($target, $errno, $errstr, $timeout);

		if ( ! is_resource($socket))
		{
			$this->error = 'Could not reach '.$host.':'.$port.' - '.$errstr;
			return FALSE;
		}

		stream_set_timeout($socket, $timeout);
		$this->socket = $socket;

		fgets($this->socket, 8192);   // server greeting

		$reply = $this->command('LOGIN '.$this->quote($config['user']).' '.$this->quote($config['pass']));

		if ( ! $this->is_ok($reply))
		{
			$this->error = 'The mailbox refused the login. Check the same credentials the queue sends with.';
			$this->disconnect();

			return FALSE;
		}

		$reply = $this->command('SELECT INBOX');

		if ( ! $this->is_ok($reply))
		{
			$this->error = 'Could not open the INBOX.';
			$this->disconnect();

			return FALSE;
		}

		return TRUE;
	}

	// ------------------------------------------------------------------

	/**
	 * Every bounce received in the last $days days.
	 *
	 * @param	int	$days
	 * @return	array	each entry: recipient, reason, permanent, date
	 */
	public function fetch($days = 7)
	{
		if ( ! is_resource($this->socket))
		{
			return array();
		}

		// Bounces are sent by the local mail system, so this is both cheap and
		// precise - no guessing from subject lines.
		$since = date('d-M-Y', time() - (max(1, (int) $days) * 86400));
		$reply = $this->command('SEARCH SINCE '.$since.' FROM "Mailer-Daemon"');

		if ( ! preg_match('/\*\s+SEARCH([0-9 ]*)/', $reply, $match))
		{
			return array();
		}

		$ids = array_filter(array_map('intval', preg_split('/\s+/', trim($match[1]))));

		if ($ids === array())
		{
			return array();
		}

		// Newest first, and capped: a mailbox nobody has emptied in months
		// should not turn one cron run into a long IMAP session.
		$ids = array_slice(array_reverse($ids), 0, 200);

		$bounces = array();

		foreach ($ids as $id)
		{
			// The delivery-status part carries the machine readable verdict and
			// sits near the top, so there is no need to pull whole messages.
			$reply = $this->command('FETCH '.$id.' (BODY.PEEK[TEXT]<0.4000>)');
			$parsed = $this->parse($reply);

			if ($parsed !== NULL)
			{
				$bounces[] = $parsed;
			}
		}

		return $bounces;
	}

	// ------------------------------------------------------------------

	/**
	 * Pull the recipient and the reason out of one bounce.
	 *
	 * Exim's delivery-status part is the reliable bit: `Final-Recipient` and
	 * `Diagnostic-Code`. Some bounces - the ones the local server generates
	 * before it ever contacts anyone, such as a domain over its failure quota -
	 * have no Diagnostic-Code, so the human readable line is used instead.
	 *
	 * @param	string	$body
	 * @return	array|null
	 */
	private function parse($body)
	{
		if ( ! preg_match('/Final-Recipient:\s*rfc822;\s*([^\s<>]+@[^\s<>]+)/i', $body, $m))
		{
			return NULL;
		}

		$recipient = strtolower(trim($m[1]));
		$reason    = '';

		if (preg_match('/Diagnostic-Code:\s*smtp;\s*(.+?)(?:\r?\n(?![ \t])|$)/is', $body, $d))
		{
			$reason = $d[1];
		}
		elseif (preg_match('/^\s*'.preg_quote($recipient, '/').'\s*\r?\n\s*(.+?)(?:\r?\n\s*\r?\n|$)/im', $body, $d))
		{
			$reason = $d[1];
		}

		$reason = trim(preg_replace('/\s+/', ' ', $reason));

		if ($reason === '')
		{
			$reason = 'The message was returned by the mail system without a reason.';
		}

		// Our own mail server throwing the message away because the domain has
		// used up its hourly failure quota. Exim words this as a permanent
		// error, but it says nothing whatsoever about the address - these are
		// usually perfectly good addresses caught in the blast radius of a few
		// bad ones. Calling them out separately matters: treating them as dead
		// addresses would be exactly the wrong conclusion.
		$throttled = (bool) preg_match('/exceeded the max (defers and failures|emails)/i', $reason);

		// "This is a permanent error" is Exim's own wording; a 5xx diagnostic
		// says the same thing. Either way the address will not start working
		// on its own.
		$permanent = ( ! $throttled) && (preg_match('/permanent error/i', $body) OR preg_match('/\b5\.\d\.\d\b|\b5\d\d\b/', $reason));

		$date = '';

		if (preg_match('/Arrival-Date:\s*(.+)/i', $body, $dd))
		{
			$stamp = strtotime(trim($dd[1]));
			$date  = $stamp ? date('Y-m-d H:i:s', $stamp) : '';
		}

		return array(
			'recipient' => $recipient,
			'reason'    => (strlen($reason) > 900) ? substr($reason, 0, 900).'...' : $reason,
			'permanent' => (bool) $permanent,
			'throttled' => $throttled,
			'date'      => $date,
		);
	}

	// ------------------------------------------------------------------

	/**
	 * @return	string	why the last call failed
	 */
	public function last_error()
	{
		return $this->error;
	}

	/**
	 * @return	void
	 */
	public function disconnect()
	{
		if (is_resource($this->socket))
		{
			@$this->command('LOGOUT');
			@fclose($this->socket);
		}

		$this->socket = NULL;
	}

	// ------------------------------------------------------------------

	/**
	 * Send one IMAP command and read the whole tagged response.
	 *
	 * @param	string	$command
	 * @return	string
	 */
	private function command($command)
	{
		$tag = 'q'.(++$this->tag);

		fwrite($this->socket, $tag.' '.$command."\r\n");

		$reply = '';

		while (($line = fgets($this->socket, 16384)) !== FALSE)
		{
			$reply .= $line;

			if (preg_match('/^'.$tag.' (OK|NO|BAD)/', $line))
			{
				break;
			}

			$meta = stream_get_meta_data($this->socket);

			if ( ! empty($meta['timed_out']))
			{
				break;
			}
		}

		return $reply;
	}

	/**
	 * @param	string	$reply
	 * @return	bool
	 */
	private function is_ok($reply)
	{
		return (bool) preg_match('/^q\d+ OK/m', $reply);
	}

	/**
	 * @param	string	$value
	 * @return	string
	 */
	private function quote($value)
	{
		return '"'.str_replace(array('\\', '"'), array('\\\\', '\"'), (string) $value).'"';
	}
}
