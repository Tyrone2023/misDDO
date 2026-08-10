<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Outgoing mail for the public self-service flows (password reset).
 *
 * Mail is never sent from inside the request that asked for it. The message is
 * rendered and written to the `mail_queue` table, and a cron job running every
 * two minutes (Mailqueue::run) delivers it over SMTP. That is what makes the
 * flow reliable: queueing a row cannot fail because the mail server is slow,
 * unreachable, or rejecting logins, and a message that could not go out on the
 * first try is retried on a growing schedule instead of being lost.
 *
 * When SMTP is healthy there is no visible delay: after queueing, the request
 * makes one short best-effort delivery attempt (mail_queue_inline). If it
 * works the mail arrives immediately; if it does not, the message simply stays
 * in the queue and cron takes it. Either way the page the visitor sees says the
 * same thing, so a slow mail server never turns into an error on screen.
 *
 * Anything that must only happen once the mail is genuinely delivered - the
 * password reset writes the new password hash, and must not write it if the
 * account holder is never going to receive it - travels with the message in
 * mail_queue.payload and is applied by whoever delivers it.
 *
 * A developer machine with no SMTP credentials at all still works: the message
 * is written to application/logs/dev_emails/ and a preview link is returned.
 * That fallback is now only reached when there is genuinely nothing to send
 * with, not merely because the request came from localhost.
 */

if ( ! function_exists('mis_mail_is_local'))
{
	/**
	 * TRUE when the current request is being served from a developer machine.
	 *
	 * Based on the host name rather than ENVIRONMENT, because the live server
	 * also runs with ENVIRONMENT = development. Returns FALSE under CLI, where
	 * there is no host name and the cron worker should behave like production.
	 *
	 * @return	bool
	 */
	function mis_mail_is_local()
	{
		$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';

		if ($host === '')
		{
			$host = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : '';
		}

		$host = strtolower(trim($host));
		$host = preg_replace('/:\d+$/', '', $host);          // drop :8080 / :443
		$host = trim($host, '[]');                           // drop [::1] brackets

		if ($host === '')
		{
			return FALSE;
		}

		if (in_array($host, array('localhost', '127.0.0.1', '::1'), TRUE))
		{
			return TRUE;
		}

		return (bool) preg_match('/\.(local|localhost|test)$/', $host);
	}
}

if ( ! function_exists('mis_mail_config'))
{
	/**
	 * The SMTP settings to send with.
	 *
	 * config/email.php as shipped, with config/email_local.php layered on top
	 * when there is one.
	 *
	 * The override file is read on a local host, and under CLI so that the
	 * cron worker sends through the same mail server the web request queued
	 * against - otherwise a developer machine would queue mail against its own
	 * test mailbox and then quietly deliver it through the live one. It is
	 * git-ignored and has to be created by hand, so it is only ever present on
	 * a machine where somebody deliberately put it.
	 *
	 * @return	array
	 */
	function mis_mail_config()
	{
		$CI =& get_instance();

		// Loaded into its own section: merging it into the main config array
		// would overwrite the site-wide 'charset' with the mail one.
		$CI->load->config('email', TRUE, TRUE);
		$settings = $CI->config->item('email');

		if ( ! is_array($settings))
		{
			// Somebody loaded email.php unsectioned earlier in the request, so
			// its keys are sitting in the main config array instead.
			$settings = array();

			foreach (array('protocol', 'smtp_host', 'smtp_user', 'smtp_pass', 'smtp_port',
				'smtp_crypto', 'smtp_timeout', 'mailtype', 'charset', 'newline', 'crlf',
				'wordwrap', 'smtp_from_email', 'smtp_from_name') as $key)
			{
				$value = $CI->config->item($key);

				if ($value !== NULL)
				{
					$settings[$key] = $value;
				}
			}
		}

		if ((mis_mail_is_local() OR is_cli()) && file_exists($file = APPPATH.'config/email_local.php'))
		{
			$config = array();
			include $file;

			// A file that was copied from the example but never filled in is
			// treated as absent, so it cannot break local sending.
			if (is_array($config) && ! empty($config['smtp_host']) && ! empty($config['smtp_user']))
			{
				$settings = array_merge($settings, $config);
			}
		}

		return $settings;
	}
}

if ( ! function_exists('mis_mail_is_configured'))
{
	/**
	 * TRUE when there is something to send with.
	 *
	 * 'mail' and 'sendmail' need no credentials; SMTP needs at least a host.
	 *
	 * @param	array|null	$settings	defaults to mis_mail_config()
	 * @return	bool
	 */
	function mis_mail_is_configured($settings = NULL)
	{
		$settings = is_array($settings) ? $settings : mis_mail_config();
		$protocol = isset($settings['protocol']) ? strtolower($settings['protocol']) : 'mail';

		if ($protocol !== 'smtp')
		{
			return TRUE;
		}

		return ( ! empty($settings['smtp_host']));
	}
}

if ( ! function_exists('mis_mail_capture'))
{
	/**
	 * Writes the message to application/logs/dev_emails/ and returns the token
	 * used to read it back through Pages::mail_preview().
	 *
	 * @param	string	$to
	 * @param	string	$subject
	 * @param	string	$html_message
	 * @return	string	the token, or '' if it could not be written
	 */
	function mis_mail_capture($to, $subject, $html_message)
	{
		$dir = APPPATH.'logs/dev_emails/';

		if ( ! is_dir($dir) && ! @mkdir($dir, 0755, TRUE) && ! is_dir($dir))
		{
			return '';
		}

		$token = date('Ymd-His').'-'.bin2hex(random_bytes(4));
		$header = '<!-- Captured locally on '.date('Y-m-d H:i:s').' -->'."\n"
			.'<!-- To: '.$to.' -->'."\n"
			.'<!-- Subject: '.$subject.' -->'."\n";

		if (@file_put_contents($dir.$token.'.html', $header.$html_message) === FALSE)
		{
			return '';
		}

		return $token;
	}
}

if ( ! function_exists('mis_mail_deliver'))
{
	/**
	 * Hand one queued message to the mail server. This is the only place in
	 * the system that actually talks SMTP.
	 *
	 * @param	array	$row		a mail_queue row
	 * @param	array	$options	keepalive  hold the SMTP connection open for
	 *					           the next message in a batch
	 *					timeout    override smtp_timeout, seconds
	 * @return	array	sent   TRUE when the server accepted the message
	 *			error  the mailer's debug output when it did not
	 */
	function mis_mail_deliver(array $row, array $options = array())
	{
		$result = array('sent' => FALSE, 'error' => '');

		$CI =& get_instance();
		$settings = mis_mail_config();

		if ( ! mis_mail_is_configured($settings))
		{
			$result['error'] = 'No mail server is configured: set smtp_host in application/config/email.php.';
			return $result;
		}

		if ( ! empty($options['timeout']))
		{
			$settings['smtp_timeout'] = (int) $options['timeout'];
		}

		// Reusing one authenticated connection for a whole batch saves a TLS
		// handshake and a login per message, which is most of the cost of
		// sending a short mail.
		$settings['smtp_keepalive'] = ! empty($options['keepalive']);
		$settings['mailtype']       = 'html';

		$CI->load->library('email');
		$CI->email->initialize($settings);

		$from_email = ! empty($row['from_email'])
			? $row['from_email']
			: (isset($settings['smtp_from_email']) ? $settings['smtp_from_email'] : $settings['smtp_user']);

		$from_name = ! empty($row['from_name'])
			? $row['from_name']
			: (isset($settings['smtp_from_name']) ? $settings['smtp_from_name'] : '');

		$CI->email->from($from_email, $from_name)
			->to($row['to_email'])
			->subject($row['subject'])
			->message($row['body_html']);

		if ( ! empty($row['reply_to']))
		{
			$CI->email->reply_to($row['reply_to']);
		}

		if ( ! empty($row['body_text']))
		{
			$CI->email->set_alt_message($row['body_text']);
		}

		// A dead or refusing SMTP host raises PHP warnings that CodeIgniter
		// prints straight into the response, which would wreck the page - and
		// under cron would fill the log with noise. send() still returns FALSE,
		// so the failure is reported properly either way.
		$result['sent'] = (bool) @$CI->email->send(FALSE);

		if ( ! $result['sent'])
		{
			$result['error'] = $CI->email->print_debugger(array('headers'));
		}

		$CI->email->clear(TRUE);

		return $result;
	}
}

if ( ! function_exists('mis_mail_apply_payload'))
{
	/**
	 * Carry out the work a message was carrying, now that it has been
	 * delivered.
	 *
	 * A password reset is the reason this exists. The new password is written
	 * here rather than when the reset was requested, so an account is never
	 * left with a password that only the server knows because the mail could
	 * not be delivered - the account holder keeps the password they have until
	 * the replacement is genuinely on its way to them.
	 *
	 * @param	array	$row	a delivered mail_queue row
	 * @return	string	'' on success, otherwise what went wrong
	 */
	function mis_mail_apply_payload(array $row)
	{
		if (empty($row['payload']))
		{
			return '';
		}

		$payload = json_decode($row['payload'], TRUE);

		if ( ! is_array($payload) || empty($payload['type']))
		{
			return 'Unreadable payload on mail_queue row '.$row['id'].'.';
		}

		$CI =& get_instance();
		$CI->load->database();

		switch ($payload['type'])
		{
			case 'password_reset':

				if (empty($payload['username']) || empty($payload['password_hash']))
				{
					return 'Incomplete password_reset payload on mail_queue row '.$row['id'].'.';
				}

				$CI->db->where('username', $payload['username'])
					->update('users', array('password' => $payload['password_hash']));

				if ((int) $CI->db->affected_rows() === 0)
				{
					// Nothing changed either because the account is gone or
					// because this row was somehow delivered twice and the
					// hash is already in place. Neither is worth retrying.
					log_message('error', 'Mail queue: password_reset payload for '.$payload['username']
						.' updated no rows (mail_queue #'.$row['id'].').');
				}
				else
				{
					log_message('info', 'Mail queue: password reset applied for '.$payload['username'].'.');
				}

				return '';

			default:
				return 'Unknown payload type "'.$payload['type'].'" on mail_queue row '.$row['id'].'.';
		}
	}
}

if ( ! function_exists('mis_queue_html_mail'))
{
	/**
	 * Queue one HTML message, and try once to deliver it straight away.
	 *
	 * This is what callers use. It returns as soon as the message is safely in
	 * the database; delivery is the queue's problem from then on.
	 *
	 * @param	array	$message	to_email, subject, body_html, and optionally
	 *					to_name, body_text, from_email, from_name,
	 *					reply_to, category, payload (array),
	 *					is_sensitive
	 * @return	array	queued      TRUE when the message is safely stored
	 *			delivered   TRUE when it also went out immediately
	 *			id          mail_queue row id
	 *			captured    TRUE when written to disk instead (no SMTP)
	 *			preview_url readable URL, capture mode only
	 *			error       why it could not be queued
	 */
	function mis_queue_html_mail(array $message)
	{
		$result = array(
			'queued'      => FALSE,
			'delivered'   => FALSE,
			'id'          => 0,
			'captured'    => FALSE,
			'preview_url' => '',
			'error'       => '',
		);

		$CI =& get_instance();
		$CI->load->config('mail_queue', FALSE, TRUE);

		$settings = mis_mail_config();

		// No mail server at all - a fresh developer machine. Write the message
		// out so the flow can still be walked end to end, and run the payload,
		// since a captured message is one the developer can actually read.
		if ( ! mis_mail_is_configured($settings))
		{
			$token = mis_mail_capture($message['to_email'], $message['subject'], $message['body_html']);

			if ($token === '')
			{
				$result['error'] = 'No mail server is configured and the local preview could not be written to application/logs/dev_emails/.';
				return $result;
			}

			$result['queued']      = TRUE;
			$result['delivered']   = TRUE;
			$result['captured']    = TRUE;
			$result['preview_url'] = base_url().'Pages/mail_preview/'.$token;

			$payload_error = mis_mail_apply_payload(array(
				'id'      => 0,
				'payload' => empty($message['payload']) ? NULL : json_encode($message['payload']),
			));

			if ($payload_error !== '')
			{
				log_message('error', 'Mail capture: '.$payload_error);
			}

			log_message('info', 'Local mail capture for '.$message['to_email'].' -> '.$token.'.html');

			return $result;
		}

		$CI->load->model('Mail_queue_model');

		$row = $message;
		$row['payload'] = empty($message['payload']) ? NULL : json_encode($message['payload']);
		$row['max_attempts'] = (int) $CI->config->item('mail_queue_max_attempts');

		if ($row['max_attempts'] < 1)
		{
			$row['max_attempts'] = 5;
		}

		if (empty($row['from_email']))
		{
			$row['from_email'] = isset($settings['smtp_from_email'])
				? $settings['smtp_from_email']
				: $settings['smtp_user'];
			$row['from_name'] = isset($settings['smtp_from_name']) ? $settings['smtp_from_name'] : '';
		}

		$id = $CI->Mail_queue_model->enqueue($row);

		if ($id === 0)
		{
			$result['error'] = 'The message could not be written to the mail queue.';
			log_message('error', 'Mail queue: enqueue failed for '.$message['to_email'].' - '.$message['subject']);
			return $result;
		}

		$result['queued'] = TRUE;
		$result['id']     = $id;

		log_message('info', 'Mail queued #'.$id.' for '.$message['to_email'].' ('.$row['category'].')');

		if ($CI->config->item('mail_queue_inline'))
		{
			$result['delivered'] = mis_mail_try_inline($id);
		}

		return $result;
	}
}

if ( ! function_exists('mis_mail_try_inline'))
{
	/**
	 * One short delivery attempt inside the request that queued the message,
	 * so a healthy mail server delivers in seconds instead of on the next cron
	 * tick.
	 *
	 * Strictly best effort. A failure here is not recorded as an attempt and
	 * costs the message nothing: the row goes straight back to 'pending' and
	 * cron picks it up with a clean slate.
	 *
	 * @param	int	$id	mail_queue row id
	 * @return	bool	TRUE when the mail server accepted the message
	 */
	function mis_mail_try_inline($id)
	{
		$CI =& get_instance();
		$CI->load->model('Mail_queue_model');

		$worker = 'inline-'.getmypid().'-'.bin2hex(random_bytes(3));
		$row    = $CI->Mail_queue_model->claim_one($id, $worker);

		if ($row === NULL)
		{
			return FALSE;
		}

		$timeout = (int) $CI->config->item('mail_queue_inline_timeout');
		$sent    = mis_mail_deliver($row, array('timeout' => ($timeout > 0 ? $timeout : 5)));

		if ( ! $sent['sent'])
		{
			$CI->Mail_queue_model->release($id);
			log_message('debug', 'Mail queue: inline attempt for #'.$id.' did not go through, left for cron.');

			return FALSE;
		}

		$payload_error = mis_mail_apply_payload($row);

		if ($payload_error !== '')
		{
			log_message('error', 'Mail queue: '.$payload_error);
		}

		$CI->Mail_queue_model->mark_sent($id, ! empty($row['is_sensitive']));
		log_message('info', 'Mail queue: #'.$id.' delivered inline to '.$row['to_email']);

		return TRUE;
	}
}

if ( ! function_exists('mis_send_html_mail'))
{
	/**
	 * Older entry point, kept so existing callers keep working. Queues the
	 * message like everything else - it no longer blocks on the mail server.
	 *
	 * 'sent' means "accepted for delivery", which is what a caller can act on;
	 * check 'delivered' to find out whether it has actually gone out yet.
	 *
	 * @param	string	$to
	 * @param	string	$subject
	 * @param	string	$html_message
	 * @param	string	$alt_message
	 * @param	string	$from_email
	 * @param	string	$from_name
	 * @return	array	see mis_queue_html_mail()
	 */
	function mis_send_html_mail($to, $subject, $html_message, $alt_message = '', $from_email = '', $from_name = '')
	{
		$result = mis_queue_html_mail(array(
			'to_email'   => $to,
			'subject'    => $subject,
			'body_html'  => $html_message,
			'body_text'  => $alt_message,
			'from_email' => $from_email,
			'from_name'  => $from_name,
		));

		$result['sent'] = $result['queued'];

		return $result;
	}
}

if ( ! function_exists('mis_mailqueue_token'))
{
	/**
	 * The deterministic HTTP-trigger token for the mail queue worker.
	 *
	 * Derived from the configured secret (mail_queue_token_secret) plus the
	 * database name, so it is stable across requests and needs no environment
	 * variable or .htaccess line. The same approach as the srms-college email
	 * queue: whoever knows this token can trigger mailqueue/run over HTTP, and
	 * it is shown by mailqueue/key to a logged-in admin.
	 *
	 * @return	string	40-char hex token
	 */
	function mis_mailqueue_token()
	{
		$CI =& get_instance();
		$secret = (string) $CI->config->item('mail_queue_token_secret');
		$dbName = isset($CI->db) ? (string) $CI->db->database : '';

		return substr(hash('sha256', 'mis-mail-queue|'.$secret.'|'.$dbName), 0, 40);
	}
}
