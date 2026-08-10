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

		// ?retry=1 resets available_at on all pending rows so they are picked
		// up immediately, bypassing the backoff wait. Useful after fixing SMTP
		// credentials so stuck messages go out right away instead of waiting.
		if ((string) $this->input->get('retry') === '1')
		{
			$this->db->where('status', 'pending')
				->update($this->Mail_queue_model::TABLE, array(
					'available_at' => date('Y-m-d H:i:s'),
				));
		}

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
	 * Show the queue status and the cron URL + token as an HTML dashboard.
	 *
	 * Accessible over HTTP (unlike the other commands) so it can be visited in
	 * a browser. The token itself is the secret; this page just displays it so
	 * it can be copied into Hostinger's cron job setup.
	 */
	public function key()
	{
		$counts      = $this->Mail_queue_model->status_counts();
		$token       = mis_mailqueue_token();
		$run_url     = site_url('mailqueue/run').'?token='.$token;
		$retry_url   = site_url('mailqueue/run').'?token='.$token.'&retry=1';
		$auto_refresh = (int) $this->input->get('refresh');

		// Pagination.
		$per_page = 20;
		$page     = max(1, (int) $this->input->get('page'));
		$offset   = ($page - 1) * $per_page;
		$total    = $this->Mail_queue_model->count_all();
		$recent   = $this->Mail_queue_model->recent($per_page, $offset);
		$pages    = max(1, (int) ceil($total / $per_page));

		$status_colors = array(
			'pending' => '#f59e0b',
			'sending' => '#3b82f6',
			'sent'    => '#16a34a',
			'failed'  => '#dc2626',
			'bounced' => '#7c3aed',
		);

		$status_bg = array(
			'pending' => '#fef3c7',
			'sending' => '#dbeafe',
			'sent'    => '#dcfce7',
			'failed'  => '#fee2e2',
			'bounced' => '#ede9fe',
		);

		$total = array_sum($counts);
		$has_actionable = ($counts['pending'] > 0 || $counts['failed'] > 0);

		$this->output->set_content_type('text/html', 'utf-8');

		if ($auto_refresh > 0)
		{
			$this->output->set_header('Refresh: '.max(5, min(300, $auto_refresh)));
		}
		?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Mail Queue - DepEd DDO MIS</title>
<style>
	:root { --border:#e5e7eb; --text:#1f2937; --muted:#6b7280; --bg:#f9fafb; }
	* { box-sizing:border-box; margin:0; padding:0; }
	body { font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif; background:var(--bg); color:var(--text); padding:24px 16px; }
	.container { max-width:960px; margin:0 auto; }
	.header { display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; flex-wrap:wrap; gap:12px; }
	.header h1 { font-size:24px; font-weight:700; }
	.header .actions { display:flex; gap:8px; flex-wrap:wrap; }
	.btn { display:inline-block; padding:8px 16px; font-size:14px; font-weight:500; border-radius:6px; text-decoration:none; border:1px solid transparent; cursor:pointer; transition:all .15s; }
	.btn-blue { background:#2563eb; color:#fff; }
	.btn-blue:hover { background:#1d4ed8; }
	.btn-amber { background:#f59e0b; color:#fff; }
	.btn-amber:hover { background:#d97706; }
	.btn-gray { background:#fff; color:var(--text); border-color:var(--border); }
	.btn-gray:hover { background:#f3f4f6; }
	.btn-sm { padding:5px 10px; font-size:12px; }

	.stats { display:grid; grid-template-columns:repeat(5,1fr); gap:12px; margin-bottom:28px; }
	.stat-card { background:#fff; border:1px solid var(--border); border-radius:8px; padding:16px; text-align:center; }
	.stat-card .num { font-size:28px; font-weight:700; line-height:1; }
	.stat-card .label { font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.05em; color:var(--muted); margin-top:6px; }

	.section { background:#fff; border:1px solid var(--border); border-radius:8px; margin-bottom:20px; overflow:hidden; }
	.section-header { padding:14px 18px; border-bottom:1px solid var(--border); font-weight:600; font-size:15px; display:flex; justify-content:space-between; align-items:center; }
	.section-header .hint { font-weight:400; font-size:12px; color:var(--muted); }
	.section-body { padding:16px 18px; }

	.cron-box { background:#1e293b; color:#e2e8f0; padding:14px 16px; border-radius:6px; font-family:"SF Mono",Menlo,Consolas,monospace; font-size:13px; overflow-x:auto; white-space:pre; line-height:1.6; }
	.copy-btn { float:right; }

	table { width:100%; border-collapse:collapse; font-size:13px; }
	th { text-align:left; padding:10px 12px; font-weight:600; font-size:11px; text-transform:uppercase; letter-spacing:.04em; color:var(--muted); border-bottom:1px solid var(--border); }
	td { padding:10px 12px; border-bottom:1px solid #f3f4f6; vertical-align:top; }
	tr:last-child td { border-bottom:0; }
	tr:hover { background:#f9fafb; }

	.badge { display:inline-block; padding:2px 8px; border-radius:12px; font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.03em; }
	.error-cell { max-width:380px; line-height:1.5; }
	.error-summary { font-size:13px; font-weight:600; margin-bottom:2px; }
	.error-detail { font-size:12px; color:#6b7280; line-height:1.5; }
	.error-raw { margin-top:6px; padding:8px 10px; background:#f9fafb; border:1px solid #e5e7eb; border-radius:4px; font-family:"SF Mono",Menlo,Consolas,monospace; font-size:10px; color:#6b7280; word-break:break-word; line-height:1.5; max-height:100px; overflow-y:auto; }
	.error-raw.collapsed { display:none; }
	.error-raw-toggle { font-size:11px; color:#2563eb; cursor:pointer; margin-top:4px; display:inline-block; user-select:none; }
	.error-raw-toggle:hover { text-decoration:underline; }
	.mono { font-family:"SF Mono",Menlo,Consolas,monospace; }
	.muted { color:var(--muted); }
	.empty { text-align:center; padding:32px; color:var(--muted); font-size:14px; }
	.pagination { display:flex; gap:4px; padding:14px 18px; border-top:1px solid var(--border); align-items:center; flex-wrap:wrap; }
	.pg-btn { display:inline-block; padding:6px 12px; font-size:13px; border:1px solid var(--border); border-radius:4px; text-decoration:none; color:var(--text); background:#fff; }
	.pg-btn:hover { background:#f3f4f6; }
	.pg-active { background:#2563eb; color:#fff; border-color:#2563eb; }
	.pg-ellipsis { padding:6px 4px; color:var(--muted); font-size:13px; }
	@media(max-width:640px){ .stats{grid-template-columns:repeat(2,1fr);} .error-cell{max-width:200px;} }
</style>
</head>
<body>
<div class="container">

	<div class="header">
		<h1>Mail Queue</h1>
		<div class="actions">
			<a class="btn btn-blue btn-sm" href="<?php echo htmlspecialchars($run_url); ?>" target="_blank">Run Now</a>
			<a class="btn btn-amber btn-sm" href="<?php echo htmlspecialchars($retry_url); ?>" target="_blank">Retry All</a>
			<a class="btn btn-gray btn-sm" href="<?php echo site_url('mailqueue/key'); ?>">Refresh</a>
			<a class="btn btn-gray btn-sm" href="<?php echo site_url('mailqueue/key?refresh=30'); ?>">Auto 30s</a>
		</div>
	</div>

	<!-- Stats -->
	<div class="stats">
		<?php foreach (array('pending','sending','sent','failed','bounced') as $s): ?>
		<div class="stat-card" style="border-top:3px solid <?php echo $status_colors[$s]; ?>">
			<div class="num" style="color:<?php echo $status_colors[$s]; ?>"><?php echo (int) $counts[$s]; ?></div>
			<div class="label"><?php echo ucfirst($s); ?></div>
		</div>
		<?php endforeach; ?>
	</div>

	<?php if ($has_actionable): ?>
	<div style="background:#fffbeb;border:1px solid #fde68a;border-radius:8px;padding:12px 16px;margin-bottom:20px;font-size:13px;color:#92400e;">
		<strong><?php echo $counts['pending']; ?> pending</strong><?php if ($counts['failed'] > 0): ?> and <strong><?php echo $counts['failed']; ?> failed</strong><?php endif; ?> message(s).
		<a href="<?php echo htmlspecialchars($retry_url); ?>" target="_blank" style="color:#92400e;font-weight:600;text-decoration:underline;">Retry all &rarr;</a>
	</div>
	<?php endif; ?>

	<!-- Recent Messages -->
	<div class="section">
		<div class="section-header">
			Recent Messages
			<span class="hint"><?php echo $total; ?> total &middot; page <?php echo $page; ?> of <?php echo $pages; ?></span>
		</div>
		<?php if ($recent === array()): ?>
		<div class="empty">No messages yet.</div>
		<?php else: ?>
		<table>
			<thead>
				<tr>
					<th>#</th>
					<th>Status</th>
					<th>Recipient</th>
					<th>Subject</th>
					<th>Attempts</th>
					<th>Created</th>
					<th>Details</th>
				</tr>
			</thead>
			<tbody>
			<?php foreach ($recent as $row):
				$color = isset($status_colors[$row['status']]) ? $status_colors[$row['status']] : '#6b7280';
				$bg    = isset($status_bg[$row['status']]) ? $status_bg[$row['status']] : '#f3f4f6';
				$detail = '';
				if ($row['status'] === 'sent')
				{
					$detail = '<span class="muted">'.htmlspecialchars($this->_manila_time($row['sent_at'])).'</span>';
				}
				elseif ($row['status'] === 'pending' && empty($row['last_error']))
				{
					$detail = '<span class="muted">Waiting for next cron run</span>';
				}
				else
				{
					// Failed, bounced, or pending-with-error: show conversational explanation.
					$explained = $this->_explain_error((string) $row['last_error']);
					$sev_color = $explained['severity'] === 'error' ? '#dc2626' : ($explained['severity'] === 'warning' ? '#f59e0b' : '#3b82f6');
					$raw_short = $this->_shorten((string) $row['last_error'], 400);
					$has_raw = (trim($raw_short) !== '');

					$detail = '<div class="error-cell">'
						.'<div class="error-summary" style="color:'.$sev_color.'">'.htmlspecialchars($explained['summary']).'</div>'
						.'<div class="error-detail">'.htmlspecialchars($explained['detail']).'</div>';
					if ($has_raw)
					{
						$detail .= '<span class="error-raw-toggle" onclick="var e=this.nextElementSibling;e.classList.toggle(\'collapsed\');this.textContent=e.classList.contains(\'collapsed\')?\'Show raw\':\'Hide raw\';">Show raw</span>'
							.'<div class="error-raw collapsed">'.htmlspecialchars($raw_short).'</div>';
					}
					$detail .= '</div>';
				}
			?>
			<tr>
				<td class="mono"><?php echo (int) $row['id']; ?></td>
				<td><span class="badge" style="background:<?php echo $bg; ?>;color:<?php echo $color; ?>"><?php echo htmlspecialchars($row['status']); ?></span></td>
				<td class="mono"><?php echo htmlspecialchars($this->_shorten($row['to_email'], 30)); ?></td>
				<td><?php echo htmlspecialchars($this->_shorten($row['subject'], 35)); ?></td>
				<td class="mono"><?php echo (int) $row['attempts']; ?>/<?php echo (int) 5; ?></td>
				<td class="muted"><?php echo htmlspecialchars($this->_manila_time($row['created_at'])); ?></td>
				<td><?php echo $detail; ?></td>
			</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		<?php endif; ?>

		<?php if ($pages > 1): ?>
		<div class="pagination">
			<?php
			$range = 2; // pages on each side of current
			$start_page = max(1, $page - $range);
			$end_page   = min($pages, $page + $range);

			// Previous
			if ($page > 1):
			?>
			<a class="pg-btn" href="<?php echo site_url('mailqueue/key?page='.($page - 1)); ?>">&laquo; Prev</a>
			<?php endif;

			// First page + ellipsis
			if ($start_page > 1):
			?>
			<a class="pg-btn" href="<?php echo site_url('mailqueue/key?page=1'); ?>">1</a>
			<?php if ($start_page > 2): ?>
			<span class="pg-ellipsis">&hellip;</span>
			<?php endif;
			endif;

			// Page numbers
			for ($i = $start_page; $i <= $end_page; $i++):
			?>
			<a class="pg-btn <?php echo ($i === $page) ? 'pg-active' : ''; ?>" href="<?php echo site_url('mailqueue/key?page='.$i); ?>"><?php echo $i; ?></a>
			<?php endfor;

			// Last page + ellipsis
			if ($end_page < $pages):
			if ($end_page < $pages - 1): ?>
			<span class="pg-ellipsis">&hellip;</span>
			<?php endif; ?>
			<a class="pg-btn" href="<?php echo site_url('mailqueue/key?page='.$pages); ?>"><?php echo $pages; ?></a>
			<?php endif;

			// Next
			if ($page < $pages):
			?>
			<a class="pg-btn" href="<?php echo site_url('mailqueue/key?page='.($page + 1)); ?>">Next &raquo;</a>
			<?php endif; ?>
		</div>
		<?php endif; ?>
	</div>

</div>
</body>
</html>
		<?php
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
	 * Convert a UTC datetime string to Manila time (Asia/Manila, UTC+8).
	 *
	 * The database stores times generated by PHP's date('Y-m-d H:i:s'), which
	 * uses whatever PHP's timezone is set to. On the live server that is UTC,
	 * so stored times are UTC. This shifts them to Asia/Manila for display.
	 *
	 * @param	string	$utc_time	e.g. "2026-08-10 22:37:07"
	 * @return	string			e.g. "2026-08-11 06:37 AM"
	 */
	private function _manila_time($utc_time)
	{
		$utc_time = trim((string) $utc_time);
		if ($utc_time === '' || $utc_time === '0000-00-00 00:00:00')
		{
			return '—';
		}

		$ts = strtotime($utc_time.' UTC');
		if ($ts === FALSE)
		{
			return $utc_time;
		}

		return date('M j, Y g:i A', $ts + 28800); // +8 hours
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

	/**
	 * Turn raw SMTP debug output into a plain-English explanation a developer
	 * can act on, without having to parse SMTP codes themselves.
	 *
	 * Returns an array with:
	 *   'summary'  - one-line conversational message (what went wrong)
	 *   'detail'   - the fix or next step (what to do about it)
	 *   'severity' - 'error', 'warning', or 'info'
	 *
	 * @param	string	$raw	the raw last_error from mail_queue
	 * @return	array
	 */
	private function _explain_error($raw)
	{
		$raw = strip_tags((string) $raw);
		$raw_lower = strtolower($raw);

		// No error at all — waiting to be sent.
		if (trim($raw) === '')
		{
			return array(
				'summary' => 'Waiting in the queue',
				'detail'  => 'This message has not been attempted yet. It will be picked up on the next cron run.',
				'severity'=> 'info',
			);
		}

		// 535 — wrong SMTP password / username.
		if (strpos($raw, '535') !== FALSE && stripos($raw, 'auth') !== FALSE)
		{
			return array(
				'summary' => 'Wrong SMTP password',
				'detail'  => 'The mail server refused the login. Update smtp_pass in application/config/email.php, then click "Retry All".',
				'severity'=> 'error',
			);
		}

		// 550 No Such User Here — recipient doesn't exist on the receiving server.
		if (strpos($raw, '550') !== FALSE && stripos($raw, 'no such user') !== FALSE)
		{
			$domain = '';
			if (preg_match('/to:\s*([^\s]+@([^\s]+))/i', $raw, $m))
			{
				$domain = $m[2];
			}

			$detail = 'The email address does not exist or the mailbox is closed.';

			if ($domain !== '' && checkdnsrr($domain, 'MX') === FALSE)
			{
				$detail .= ' '.$domain.' has no MX record — check Email Routing in cPanel.';
			}

			return array(
				'summary' => 'Recipient does not exist',
				'detail'  => $detail,
				'severity'=> 'error',
			);
		}

		// 550 — recipient address rejected (access denied, etc.).
		if (strpos($raw, '550') !== FALSE && stripos($raw, 'access denied') !== FALSE)
		{
			return array(
				'summary' => 'Blocked by recipient server',
				'detail'  => 'The recipient server refused us (often Microsoft 365 / DepEd). This is their policy, not a code issue.',
				'severity'=> 'error',
			);
		}

		// 550 — max defers and failures per hour (cPanel throttle).
		if (strpos($raw, '550') !== FALSE && stripos($raw, 'max defers') !== FALSE)
		{
			return array(
				'summary' => 'Hourly send limit hit',
				'detail'  => 'Our mail server throttled us — too many failures this hour. Raise "Max defers/failures per hour" in WHM.',
				'severity'=> 'warning',
			);
		}

		// 421 — temporary deferral.
		if (strpos($raw, '421') !== FALSE)
		{
			return array(
				'summary' => 'Temporarily deferred',
				'detail'  => 'The recipient server asked us to try later. The queue will retry automatically.',
				'severity'=> 'warning',
			);
		}

		// Connection / timeout issues.
		if (stripos($raw, 'timeout') !== FALSE || stripos($raw, 'connection refused') !== FALSE || stripos($raw, 'unable to connect') !== FALSE)
		{
			return array(
				'summary' => 'Cannot reach mail server',
				'detail'  => 'SMTP connection failed. Check smtp_host and smtp_port in email.php, and that port 465 is open.',
				'severity'=> 'error',
			);
		}

		// SSL/TLS issues.
		if (stripos($raw, 'ssl') !== FALSE || stripos($raw, 'tls') !== FALSE || stripos($raw, 'certificate') !== FALSE)
		{
			return array(
				'summary' => 'SSL/TLS error',
				'detail'  => 'Encrypted connection failed. Check smtp_crypto in email.php ("ssl" for port 465, "tls" for 587).',
				'severity'=> 'error',
			);
		}

		// Generic "Unable to send email" fallback.
		if (stripos($raw, 'unable to send') !== FALSE)
		{
			return array(
				'summary' => 'Send failed',
				'detail'  => 'The mail server rejected the message. Check raw output below for the SMTP reply code.',
				'severity'=> 'error',
			);
		}

		// Unknown — show what we have.
		return array(
			'summary' => 'Delivery failed',
			'detail'  => 'Unrecognized error. Check the raw output below.',
			'severity'=> 'error',
		);
	}
}
