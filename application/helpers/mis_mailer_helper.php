<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Shared mail sender for the public self-service flows (password reset).
 *
 * Live servers keep using application/config/email.php exactly as before, so
 * nothing about production delivery changes. A local machine (XAMPP) normally
 * has no working MTA, which used to make the whole flow untestable, so when the
 * request comes from localhost the helper instead:
 *
 *   1. uses application/config/email_local.php when the developer created it
 *      from email_local.php.example and put real SMTP credentials in it - the
 *      message is really delivered, or
 *   2. captures the message to application/logs/dev_emails/ and hands back a
 *      preview URL, so the flow can be walked end to end with no mail server.
 *
 * The local config is only ever read on a local host, so it can never change
 * how the production server sends mail.
 */

if ( ! function_exists('mis_mail_is_local'))
{
	/**
	 * TRUE when the current request is being served from a developer machine.
	 * Deliberately based on the host name rather than ENVIRONMENT, because the
	 * live server also runs with ENVIRONMENT = development.
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

if ( ! function_exists('mis_mail_local_overrides'))
{
	/**
	 * Local-only SMTP overrides, or NULL when there are none. NULL means the
	 * helper falls back to capturing the message to disk.
	 */
	function mis_mail_local_overrides()
	{
		if ( ! mis_mail_is_local())
		{
			return NULL;
		}

		$file = APPPATH.'config/email_local.php';

		if ( ! file_exists($file))
		{
			return NULL;
		}

		$config = array();
		include $file;

		// An override file that has been copied but not filled in yet is
		// treated as "no overrides" so capture mode still kicks in.
		if ( ! is_array($config) OR empty($config['smtp_host']) OR empty($config['smtp_user']))
		{
			return NULL;
		}

		return $config;
	}
}

if ( ! function_exists('mis_mail_capture'))
{
	/**
	 * Writes the message to application/logs/dev_emails/ and returns the token
	 * used to read it back through Pages::mail_preview().
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

if ( ! function_exists('mis_send_html_mail'))
{
	/**
	 * Sends one HTML message.
	 *
	 * @return array sent        TRUE when delivered or captured
	 *               captured    TRUE when written to disk instead of sent
	 *               preview_url readable URL, capture mode only
	 *               error       mailer debug output when sending failed
	 */
	function mis_send_html_mail($to, $subject, $html_message, $alt_message = '', $from_email = 'no-reply@depeddavor.com', $from_name = 'DepEd Davao de Oro MIS')
	{
		$result = array(
			'sent'        => FALSE,
			'captured'    => FALSE,
			'preview_url' => '',
			'error'       => '',
		);

		$CI =& get_instance();
		$overrides = mis_mail_local_overrides();

		if (mis_mail_is_local() && $overrides === NULL)
		{
			$token = mis_mail_capture($to, $subject, $html_message);

			if ($token === '')
			{
				$result['error'] = 'Unable to write the local email preview to application/logs/dev_emails/.';
				return $result;
			}

			$result['sent']        = TRUE;
			$result['captured']    = TRUE;
			$result['preview_url'] = base_url().'Pages/mail_preview/'.$token;

			log_message('info', 'Local mail capture for '.$to.' -> '.$token.'.html');

			return $result;
		}

		$CI->load->config('email', FALSE, TRUE);
		$CI->load->library('email');
		$CI->email->clear(TRUE);

		if (is_array($overrides))
		{
			$CI->email->initialize($overrides);
		}

		$CI->email->set_mailtype('html');
		$CI->email->from($from_email, $from_name)
			->to($to)
			->subject($subject)
			->message($html_message);

		if ($alt_message !== '')
		{
			$CI->email->set_alt_message($alt_message);
		}

		// A dead SMTP host raises warnings that would render a 500 page in the
		// middle of the request and kill the redirect that follows. send() still
		// returns FALSE, so the caller can report the failure properly.
		$result['sent'] = (bool) @$CI->email->send(FALSE);

		if ( ! $result['sent'])
		{
			$result['error'] = $CI->email->print_debugger(array('headers'));
			log_message('error', 'Mail send failed for '.$to.' - '.$subject);
		}

		$CI->email->clear(TRUE);

		return $result;
	}
}
