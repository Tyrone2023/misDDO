<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * The worker that drains the outgoing mail queue.
 *
 * Normally run from cron every two minutes:
 *
 *   *\/2 * * * * /usr/bin/php /path/to/site/index.php mailqueue run >/dev/null 2>&1
 *
 * On hosting that offers no CLI cron, the same run can be triggered over HTTP
 * with the shared secret from config/mail_queue.php:
 *
 *   https://your-site/mailqueue/run?token=...
 *
 * Other things it can do, all from the command line:
 *
 *   php index.php mailqueue status                 counts and the recent tail
 *   php index.php mailqueue test you@example.com   queue a message to yourself
 *   php index.php mailqueue check                  test the SMTP login only
 *   php index.php mailqueue retry [id]             requeue failed messages
 *
 * Overlapping runs are safe. Messages are claimed with an atomic UPDATE, so a
 * run that is still going when the next one starts simply keeps the rows it
 * has and the new run works on the rest.
 */
class Mailqueue extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();

		$this->load->config('mail_queue', FALSE, TRUE);
		$this->load->helper('mis_mailer');
		$this->load->model('Mail_queue_model');
	}

	// ------------------------------------------------------------------

	/**
	 * Send everything that is due. This is what cron calls.
	 *
	 * @return	void
	 */
	public function run()
	{
		if ( ! $this->_authorise())
		{
			return;
		}

		$started = microtime(TRUE);

		// Rows a previous worker claimed and never finished - it was killed
		// mid-batch - would otherwise sit in 'sending' forever.
		$released = $this->Mail_queue_model->release_stale_locks(
			(int) $this->config->item('mail_queue_lock_ttl')
		);

		if ($released > 0)
		{
			log_message('error', 'Mail queue: released '.$released.' message(s) from a worker that did not finish.');
		}

		$batch = (int) $this->config->item('mail_queue_batch');
		$batch = ($batch > 0) ? $batch : 25;

		$worker = gethostname().'-'.getmypid().'-'.bin2hex(random_bytes(3));
		$rows   = $this->Mail_queue_model->claim($batch, $worker);

		$sent = 0;
		$retry = 0;
		$failed = 0;

		if ($rows !== array() && ! mis_mail_is_configured())
		{
			// Nothing to send with. Put the batch straight back rather than
			// burning an attempt on each message.
			foreach ($rows as $row)
			{
				$this->Mail_queue_model->release($row['id']);
			}

			$this->_report('No mail server is configured; '.count($rows).' message(s) left queued.', TRUE);
			return;
		}

		$backoff = $this->config->item('mail_queue_backoff');
		$backoff = is_array($backoff) && $backoff !== array() ? $backoff : array(120, 600, 1800, 3600);

		foreach ($rows as $row)
		{
			// One authenticated SMTP connection is held open across the batch.
			$result = mis_mail_deliver($row, array('keepalive' => TRUE));

			if ($result['sent'])
			{
				$payload_error = mis_mail_apply_payload($row);

				if ($payload_error !== '')
				{
					log_message('error', 'Mail queue: '.$payload_error);
				}

				$this->Mail_queue_model->mark_sent($row['id'], ! empty($row['is_sensitive']));
				$sent++;

				$this->_line('sent    #'.$row['id'].'  '.$row['to_email'].'  '.$row['subject']);
				continue;
			}

			$status = $this->Mail_queue_model->mark_attempt_failed($row, $result['error'], $backoff);

			if ($status === 'rejected')
			{
				// The address itself was refused - a typo, a closed mailbox,
				// or a domain this mail server cannot deliver to. Retrying
				// would change nothing.
				$failed++;
				log_message('error', 'Mail queue: '.$row['to_email'].' was rejected by the mail server '
					.'(mail_queue #'.$row['id'].'); not retrying.');
				$this->_line('REJECTED #'.$row['id'].'  '.$row['to_email'].'  (address refused, not retried)');
			}
			elseif ($status === 'failed')
			{
				$failed++;
				log_message('error', 'Mail queue: giving up on #'.$row['id'].' to '.$row['to_email']
					.' after '.((int) $row['attempts'] + 1).' attempts.');
				$this->_line('FAILED  #'.$row['id'].'  '.$row['to_email'].'  (gave up)');
			}
			else
			{
				$retry++;
				$this->_line('retry   #'.$row['id'].'  '.$row['to_email'].'  (attempt '.((int) $row['attempts'] + 1).')');
			}
		}

		$pruned = $this->Mail_queue_model->prune_sent(
			(int) $this->config->item('mail_queue_retention_days')
		);

		$summary = sprintf(
			'Mail queue run: %d sent, %d to retry, %d failed, %d pruned in %.2fs.',
			$sent, $retry, $failed, $pruned, microtime(TRUE) - $started
		);

		// A run with nothing to do happens 720 times a day; only say something
		// when something actually happened.
		if ($sent > 0 OR $retry > 0 OR $failed > 0)
		{
			log_message('info', $summary);
		}

		$this->_report($summary);
	}

	// ------------------------------------------------------------------

	/**
	 * Counts per status plus the tail of the queue. CLI only.
	 *
	 * @return	void
	 */
	public function status()
	{
		if ( ! $this->_authorise())
		{
			return;
		}

		$counts = $this->Mail_queue_model->status_counts();

		$this->_line('Mail queue: '.$counts['pending'].' pending, '.$counts['sending'].' sending, '
			.$counts['sent'].' sent, '.$counts['failed'].' failed, '.$counts['bounced'].' bounced.');
		$this->_line('');

		foreach ($this->Mail_queue_model->recent(15) as $row)
		{
			$this->_line(sprintf(
				'#%-5s %-9s %-34s %-28s %s',
				$row['id'],
				$row['status'],
				$this->_shorten($row['to_email'], 34),
				$this->_shorten($row['subject'], 28),
				($row['status'] === 'sent') ? $row['sent_at'] : $this->_shorten((string) $row['last_error'], 60)
			));
		}
	}

	// ------------------------------------------------------------------

	/**
	 * Queue a plain test message, to prove the whole path works. CLI only.
	 *
	 * The address comes from the MAILQUEUE_TO environment variable, because
	 * CodeIgniter builds its URI out of the command line arguments and its
	 * permitted_uri_chars filter rejects the "@" before this method is ever
	 * reached. A percent-encoded argument works too, for anyone who prefers it.
	 *
	 *   MAILQUEUE_TO=you@example.com php index.php mailqueue test
	 *   php index.php mailqueue test you%40example.com
	 *
	 * @param	string	$to	the address to send to, percent-encoded
	 * @return	void
	 */
	public function test($to = '')
	{
		if ( ! $this->_authorise())
		{
			return;
		}

		$to = trim(rawurldecode($to));

		if ($to === '')
		{
			$to = trim((string) getenv('MAILQUEUE_TO'));
		}

		if ($to === '' OR ! filter_var($to, FILTER_VALIDATE_EMAIL))
		{
			$this->_line('Usage: MAILQUEUE_TO=you@example.com php index.php mailqueue test');
			$this->_line('   or: php index.php mailqueue test you%40example.com');
			return;
		}

		$stamp = date('Y-m-d H:i:s');

		$result = mis_queue_html_mail(array(
			'to_email'  => $to,
			'subject'   => 'Mail queue test - '.$stamp,
			'body_html' => '<p>This is a test message from the DepEd Davao de Oro MIS mail queue.</p>'
				.'<p>Queued at '.$stamp.'. If you are reading it, outgoing mail works.</p>',
			'body_text' => 'Test message from the DepEd Davao de Oro MIS mail queue, queued at '.$stamp.'.',
			'category'  => 'test',
		));

		if ( ! $result['queued'])
		{
			$this->_line('Could not queue the message: '.$result['error']);
			return;
		}

		if ( ! empty($result['captured']))
		{
			$this->_line('No SMTP configured, so the message was written to application/logs/dev_emails/ instead.');
			return;
		}

		$this->_line('Queued as #'.$result['id'].'.');
		$this->_line($result['delivered']
			? 'Delivered immediately - SMTP is working.'
			: 'Not delivered yet. Run "php index.php mailqueue run" to see why, or wait for cron.');
	}

	// ------------------------------------------------------------------

	/**
	 * Log in to the mail server and hang up, without sending anything.
	 *
	 * This separates "the credentials are wrong" from "the message is bad",
	 * which is otherwise slow to work out from the queue alone. CLI only.
	 *
	 * @return	void
	 */
	public function check()
	{
		if ( ! $this->_authorise())
		{
			return;
		}

		$settings = mis_mail_config();

		if (empty($settings['smtp_host']))
		{
			$this->_line('No SMTP host is configured in application/config/email.php.');
			return;
		}

		$this->_line('Host: '.$settings['smtp_host'].':'.$settings['smtp_port']
			.' ('.$settings['smtp_crypto'].'), user '.$settings['smtp_user']);

		$host = ($settings['smtp_crypto'] === 'ssl' ? 'ssl://' : '').$settings['smtp_host'];
		$errno = 0;
		$errstr = '';

		$socket = @fsockopen($host, (int) $settings['smtp_port'], $errno, $errstr,
			(int) $settings['smtp_timeout']);

		if ( ! is_resource($socket))
		{
			$this->_line('FAILED to connect: '.$errstr.' ('.$errno.')');
			$this->_line('The mail server is unreachable from this machine - firewall, wrong host, or wrong port.');
			return;
		}

		stream_set_timeout($socket, (int) $settings['smtp_timeout']);
		$this->_line('connect  '.trim($this->_smtp_read($socket)));

		$this->_line('EHLO     '.trim($this->_smtp_say($socket, 'EHLO '.gethostname())));

		if ($settings['smtp_crypto'] === 'tls')
		{
			$this->_smtp_say($socket, 'STARTTLS');
			@stream_socket_enable_crypto($socket, TRUE, STREAM_CRYPTO_METHOD_TLS_CLIENT);
			$this->_smtp_say($socket, 'EHLO '.gethostname());
		}

		$auth = base64_encode("\0".$settings['smtp_user']."\0".$settings['smtp_pass']);
		$reply = trim($this->_smtp_say($socket, 'AUTH PLAIN '.$auth));

		$this->_line('AUTH     '.$reply);

		$this->_smtp_say($socket, 'QUIT');
		fclose($socket);

		if (strpos($reply, '235') === 0)
		{
			$this->_line('');
			$this->_line('OK - the mail server accepted these credentials.');
			return;
		}

		$this->_line('');
		$this->_line('The mail server REJECTED these credentials.');
		$this->_line('Nothing in the queue can go out until smtp_user / smtp_pass in');
		$this->_line('application/config/email.php match a real mailbox on '.$settings['smtp_host'].',');
		$this->_line('or SRMS_SMTP_PASS is set in the environment to the right password.');
	}

	// ------------------------------------------------------------------

	/**
	 * Read delivery failures back out of the sending mailbox and record them
	 * against the messages they belong to. CLI only.
	 *
	 * Run this whenever mail is "sent" but not arriving. It is the only way to
	 * see what the recipient's mail server actually said, because that verdict
	 * arrives long after our own server accepted the message.
	 *
	 * @return	void
	 */
	public function bounces()
	{
		if ( ! $this->_authorise())
		{
			return;
		}

		$settings = mis_mail_config();

		$host = (string) $this->config->item('mail_queue_imap_host');
		$host = ($host !== '') ? $host : (isset($settings['smtp_host']) ? $settings['smtp_host'] : '');

		$this->load->library('Mail_bounces');

		$connected = $this->mail_bounces->connect(array(
			'host' => $host,
			'port' => (int) $this->config->item('mail_queue_imap_port'),
			'user' => isset($settings['smtp_user']) ? $settings['smtp_user'] : '',
			'pass' => isset($settings['smtp_pass']) ? $settings['smtp_pass'] : '',
		));

		if ( ! $connected)
		{
			$this->_line('Could not read the mailbox: '.$this->mail_bounces->last_error());
			return;
		}

		$found = $this->mail_bounces->fetch((int) $this->config->item('mail_queue_bounce_days'));
		$this->mail_bounces->disconnect();

		if ($found === array())
		{
			$this->_line('No delivery failures in the mailbox.');
			return;
		}

		$matched = 0;
		$seen = array();
		$throttled = 0;

		foreach ($found as $bounce)
		{
			if ( ! empty($bounce['throttled']))
			{
				$throttled++;
			}

			$id = $this->Mail_queue_model->mark_bounced(
				$bounce['recipient'], $bounce['reason'], $bounce['date']
			);

			if ($id > 0)
			{
				$matched++;
				log_message('error', 'Mail queue: #'.$id.' to '.$bounce['recipient']
					.' bounced - '.$bounce['reason']);
			}

			// One dead address usually produces several bounces; report it once.
			if (isset($seen[$bounce['recipient']]))
			{
				continue;
			}

			$seen[$bounce['recipient']] = TRUE;

			if ( ! empty($bounce['throttled']))
			{
				$label = 'THROTTLED';
			}
			elseif ($bounce['permanent'])
			{
				$label = 'PERMANENT';
			}
			else
			{
				$label = 'temporary';
			}

			$this->_line($label.' '.$bounce['recipient']);
			$this->_line('          '.$this->_shorten($bounce['reason'], 150));
		}

		$this->_line('');
		$this->_line(count($found).' bounce(s) read, '.count($seen).' distinct address(es), '
			.$matched.' matched to queued messages.');

		if ($throttled > 0)
		{
			$this->_line('');
			$this->_line('*** '.$throttled.' of those were thrown away by our OWN mail server, not the');
			$this->_line('*** recipient: the domain hit its hourly quota of failed deliveries and');
			$this->_line('*** everything after that is discarded - including good addresses.');
			$this->_line('*** Raise "Max defers and failures per hour" for the domain in WHM, and');
			$this->_line('*** stop sending to the addresses that are failing. See MAIL_QUEUE.md.');
		}
	}

	// ------------------------------------------------------------------

	/**
	 * Put failed messages back in the queue. CLI only.
	 *
	 * @param	int	$id	one message, or 0 for all of them
	 * @return	void
	 */
	public function retry($id = 0)
	{
		if ( ! $this->_authorise())
		{
			return;
		}

		$count = $this->Mail_queue_model->retry_failed((int) $id);

		$this->_line($count.' message(s) put back in the queue.');
	}

	// ------------------------------------------------------------------

	/**
	 * Show the queue status and, for a logged-in admin, the cron URL + token.
	 *
	 * Accessible over HTTP (unlike the other commands) so it can be visited in
	 * a browser. A non-logged-in visitor gets only the queue counts; a logged-in
	 * admin who passes ?show_cron=1 also gets the ready-to-paste cron command.
	 */
	public function key()
	{
		$counts = $this->Mail_queue_model->status_counts();

		$out = "DepEd DDO MIS Mail Queue\n"
			."=========================\n"
			."Queue: pending=".$counts['pending']
			."  sending=".$counts['sending']
			."  sent=".$counts['sent']
			."  failed=".$counts['failed']
			."  bounced=".$counts['bounced']."\n";

		// Only reveal the cron URL to a logged-in user.
		$username = (string) $this->session->userdata('username');
		$position = (string) $this->session->userdata('position');

		if ($username !== '' && ! in_array(strtolower($position), array('school', 'student', 'applicant'), TRUE))
		{
			$token  = mis_mailqueue_token();
			$cron   = '*/2 * * * * curl -s "'.site_url('mailqueue/run').'?token='.$token.'" > /dev/null 2>&1';
			$explicit = (string) $this->config->item('mail_queue_cron_token');

			$out .= "\nCron command (hPanel > Advanced > Cron Jobs, every 2 minutes):\n\n"
				."  ".$cron."\n";

			if ($explicit !== '')
			{
				$out .= "\n(Note: SRMS_CRON_TOKEN is set in the environment and overrides the derived token.)\n";
			}
		}

		$this->output->set_content_type('text/plain')->set_output($out);
	}

	// ------------------------------------------------------------------

	/**
	 * Who is allowed to run this.
	 *
	 * CLI is trusted outright. Over HTTP, the token in the URL must match the
	 * derived token (mis_mailqueue_token) or the explicit override from
	 * SRMS_CRON_TOKEN. Only run() is exposed over HTTP; the other commands
	 * print queue contents and are refused.
	 *
	 * @return	bool
	 */
	private function _authorise()
	{
		if (is_cli())
		{
			return TRUE;
		}

		// Only run() and key() are safe to expose; the rest print queue contents.
		$method = strtolower((string) $this->router->fetch_method());
		if ($method !== 'run' && $method !== 'key')
		{
			show_404();
			return FALSE;
		}

		// key() has its own auth (logged-in admin), so let it through.
		if ($method === 'key')
		{
			return TRUE;
		}

		// An explicit env override wins; otherwise the derived token is used.
		$explicit = (string) $this->config->item('mail_queue_cron_token');
		$expected = ($explicit !== '') ? $explicit : mis_mailqueue_token();
		$given    = (string) $this->input->get_post('token');

		if ($expected === '' OR ! hash_equals($expected, $given))
		{
			log_message('error', 'Mail queue: refused an HTTP run from '.$this->input->ip_address());
			$this->output->set_status_header(403)
				->set_content_type('text/plain', 'utf-8')
				->set_output("Forbidden\n");

			return FALSE;
		}

		return TRUE;
	}

	// ------------------------------------------------------------------

	/**
	 * Send one SMTP command and return the reply. Used by check() only.
	 *
	 * @param	resource	$socket
	 * @param	string		$command
	 * @return	string
	 */
	private function _smtp_say($socket, $command)
	{
		fwrite($socket, $command."\r\n");

		return $this->_smtp_read($socket);
	}

	/**
	 * Read one complete SMTP reply, continuation lines included.
	 *
	 * @param	resource	$socket
	 * @return	string
	 */
	private function _smtp_read($socket)
	{
		$reply = '';

		while (($line = fgets($socket, 512)) !== FALSE)
		{
			$reply .= $line;

			// "250-..." is a continuation, "250 ..." ends the reply.
			if (isset($line[3]) && $line[3] === ' ')
			{
				break;
			}
		}

		return $reply;
	}

	// ------------------------------------------------------------------

	/**
	 * One line of output, on the terminal under CLI and in the response body
	 * when triggered over HTTP.
	 *
	 * @param	string	$text
	 * @return	void
	 */
	private function _line($text)
	{
		echo $text."\n";
	}

	/**
	 * Finish a cron run.
	 *
	 * @param	string	$summary
	 * @param	bool	$is_problem
	 * @return	void
	 */
	private function _report($summary, $is_problem = FALSE)
	{
		if ($is_problem)
		{
			log_message('error', 'Mail queue: '.$summary);
		}

		if ( ! is_cli())
		{
			$this->output->set_content_type('text/plain', 'utf-8');
		}

		$this->_line($summary);
	}

	/**
	 * @param	string	$text
	 * @param	int	$length
	 * @return	string
	 */
	private function _shorten($text, $length)
	{
		$text = trim(preg_replace('/\s+/', ' ', (string) $text));

		return (strlen($text) > $length) ? substr($text, 0, $length - 3).'...' : $text;
	}
}
