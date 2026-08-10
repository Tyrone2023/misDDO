<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Storage layer for the outgoing mail queue (see mail_queue.sql).
 *
 * The only thing worth knowing about this class is how claiming works. Two
 * cron runs can overlap - a run that is slow because SMTP is slow is still
 * running when the next one starts two minutes later - so a row is claimed
 * with an UPDATE that only matches rows nobody else has taken. Whoever wins
 * the UPDATE owns the row; the loser's UPDATE matches nothing and it moves on.
 * Selecting first and updating afterwards would send some messages twice.
 *
 * Every time value is generated in PHP rather than with MySQL's NOW(), so the
 * queue keeps one clock even when PHP and MySQL disagree about the timezone.
 */
class Mail_queue_model extends CI_Model
{
	const TABLE = 'mail_queue';

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	// ------------------------------------------------------------------

	/**
	 * Add one message to the queue.
	 *
	 * @param	array	$message	to_email, subject, body_html and any other
	 *					column of the table
	 * @return	int	the new row id, or 0 if the insert failed
	 */
	public function enqueue(array $message)
	{
		$now = date('Y-m-d H:i:s');

		$row = array(
			'to_email'     => $message['to_email'],
			'to_name'      => isset($message['to_name']) ? $message['to_name'] : NULL,
			'from_email'   => $message['from_email'],
			'from_name'    => isset($message['from_name']) ? $message['from_name'] : NULL,
			'reply_to'     => isset($message['reply_to']) ? $message['reply_to'] : NULL,
			'subject'      => $message['subject'],
			'body_html'    => $message['body_html'],
			'body_text'    => isset($message['body_text']) ? $message['body_text'] : NULL,
			'category'     => isset($message['category']) ? $message['category'] : 'general',
			'payload'      => isset($message['payload']) ? $message['payload'] : NULL,
			'is_sensitive' => empty($message['is_sensitive']) ? 0 : 1,
			'status'       => 'pending',
			'attempts'     => 0,
			'max_attempts' => isset($message['max_attempts']) ? (int) $message['max_attempts'] : 5,
			'available_at' => isset($message['available_at']) ? $message['available_at'] : $now,
			'created_at'   => $now,
		);

		if ( ! $this->db->insert(self::TABLE, $row))
		{
			return 0;
		}

		return (int) $this->db->insert_id();
	}

	// ------------------------------------------------------------------

	/**
	 * Take ownership of up to $limit messages that are due to be sent.
	 *
	 * @param	int	$limit	how many to claim
	 * @param	string	$worker	identifies this run in mail_queue.locked_by
	 * @return	array	the claimed rows, oldest first
	 */
	public function claim($limit, $worker)
	{
		$limit = max(1, (int) $limit);
		$now   = date('Y-m-d H:i:s');
		$table = $this->db->escape_str(self::TABLE);

		// Claim in one statement so a concurrent worker cannot take the same
		// rows: the WHERE only matches messages that are still unclaimed, and
		// MySQL applies it under a row lock.
		$sql = 'UPDATE `'.$table.'`
			SET `status` = \'sending\', `locked_at` = ?, `locked_by` = ?
			WHERE `status` = \'pending\' AND `available_at` <= ?
			ORDER BY `id` ASC
			LIMIT '.$limit;

		$this->db->query($sql, array($now, $worker, $now));

		if ((int) $this->db->affected_rows() === 0)
		{
			return array();
		}

		// locked_by is unique per run, so this reads back exactly what the
		// UPDATE above just took and nothing another worker holds.
		return $this->db
			->where('status', 'sending')
			->where('locked_by', $worker)
			->order_by('id', 'ASC')
			->get(self::TABLE)
			->result_array();
	}

	// ------------------------------------------------------------------

	/**
	 * Return messages stuck in 'sending' to the queue.
	 *
	 * A worker that is killed mid-batch - PHP timeout, host reboot, cron job
	 * cancelled - leaves its rows claimed forever. Anything held longer than a
	 * batch could possibly take is assumed orphaned.
	 *
	 * @param	int	$ttl_seconds	how long a lock may legitimately be held
	 * @return	int	number of messages released
	 */
	public function release_stale_locks($ttl_seconds)
	{
		$cutoff = date('Y-m-d H:i:s', time() - max(60, (int) $ttl_seconds));

		$this->db->where('status', 'sending')
			->where('locked_at <', $cutoff)
			->update(self::TABLE, array(
				'status'    => 'pending',
				'locked_at' => NULL,
				'locked_by' => NULL,
			));

		return (int) $this->db->affected_rows();
	}

	// ------------------------------------------------------------------

	/**
	 * Mark a message delivered.
	 *
	 * A message flagged sensitive carries a temporary password in its body, so
	 * the body is thrown away here: it has served its purpose and there is no
	 * reason to leave a readable password sitting in the database.
	 *
	 * @param	int	$id
	 * @param	bool	$is_sensitive
	 * @return	void
	 */
	public function mark_sent($id, $is_sensitive = FALSE)
	{
		$update = array(
			'status'     => 'sent',
			'sent_at'    => date('Y-m-d H:i:s'),
			'last_error' => NULL,
			'locked_at'  => NULL,
			'locked_by'  => NULL,
		);

		if ($is_sensitive)
		{
			$update['body_html'] = '<!-- delivered; body discarded because it contained a temporary password -->';
			$update['body_text'] = NULL;
			$update['payload']   = NULL;
		}

		$this->db->where('id', (int) $id)->update(self::TABLE, $update);
	}

	// ------------------------------------------------------------------

	/**
	 * Record a failed attempt and schedule the retry.
	 *
	 * @param	array	$row		the message as claimed
	 * @param	string	$error		what the mail server said
	 * @param	array	$backoff	seconds to wait, per attempt number
	 * @return	string	'pending' if it will be retried, 'failed' if it ran out
	 *			of attempts, 'rejected' if the address is refused
	 */
	public function mark_attempt_failed(array $row, $error, array $backoff)
	{
		$attempts = (int) $row['attempts'] + 1;
		$max      = (int) $row['max_attempts'];

		// An address the mail server refuses outright will be refused just as
		// firmly in an hour's time, so there is nothing to wait for. Retrying
		// it only buries the real problem under four more identical failures.
		$rejected = $this->_is_permanent_failure($error);
		$give_up  = ($rejected OR $attempts >= $max);

		$update = array(
			'attempts'   => $attempts,
			'last_error' => $this->_trim_error($error),
			'locked_at'  => NULL,
			'locked_by'  => NULL,
			'status'     => $give_up ? 'failed' : 'pending',
		);

		if ( ! $give_up)
		{
			// Attempt 1 waits backoff[0], attempt 2 waits backoff[1], and so
			// on; once the list is exhausted the last value repeats.
			$index = min($attempts - 1, count($backoff) - 1);
			$wait  = ($index >= 0 && isset($backoff[$index])) ? (int) $backoff[$index] : 600;

			$update['available_at'] = date('Y-m-d H:i:s', time() + max(30, $wait));
		}

		$this->db->where('id', (int) $row['id'])->update(self::TABLE, $update);

		return $rejected ? 'rejected' : $update['status'];
	}

	// ------------------------------------------------------------------

	/**
	 * Did the mail server refuse the address itself, rather than have a bad
	 * moment?
	 *
	 * Looks at the reply to RCPT TO, which CodeIgniter's debugger writes as
	 * "to: <code> <text>". A 5xx there means the recipient does not exist or
	 * is not accepted; 4xx is temporary and gets retried as normal.
	 *
	 * Deliberately ignores 5xx replies from any other stage. A rejected login
	 * (535) is somebody's else's problem to fix, and once they have fixed it
	 * the queued mail should go out on its own rather than needing a manual
	 * retry, so those keep their attempts.
	 *
	 * @param	string	$error	the mailer's debug output
	 * @return	bool
	 */
	private function _is_permanent_failure($error)
	{
		$error = strip_tags((string) $error);

		return (bool) preg_match('/\bto:\s*5\d\d\b/i', $error);
	}

	// ------------------------------------------------------------------

	/**
	 * Delete successfully sent messages older than $days.
	 *
	 * Only 'sent' rows are pruned. A 'failed' one is a problem somebody still
	 * needs to see, so it stays until it is dealt with by hand.
	 *
	 * @param	int	$days	0 keeps everything
	 * @return	int	rows deleted
	 */
	public function prune_sent($days)
	{
		$days = (int) $days;

		if ($days <= 0)
		{
			return 0;
		}

		$this->db->where('status', 'sent')
			->where('sent_at <', date('Y-m-d H:i:s', time() - ($days * 86400)))
			->delete(self::TABLE);

		return (int) $this->db->affected_rows();
	}

	// ------------------------------------------------------------------

	/**
	 * Put a single message back at the front of the queue.
	 *
	 * Used by the inline attempt in the mailer helper: it claims one row, and
	 * if the send does not work out the row goes straight back so the next
	 * cron run can try it, with no failed attempt recorded against it.
	 *
	 * @param	int	$id
	 * @return	void
	 */
	public function release($id)
	{
		$this->db->where('id', (int) $id)
			->where('status', 'sending')
			->update(self::TABLE, array(
				'status'    => 'pending',
				'locked_at' => NULL,
				'locked_by' => NULL,
			));
	}

	// ------------------------------------------------------------------

	/**
	 * Claim one specific message, if it is still available.
	 *
	 * @param	int	$id
	 * @param	string	$worker
	 * @return	array|null	the row, or NULL if somebody else has it
	 */
	public function claim_one($id, $worker)
	{
		$now = date('Y-m-d H:i:s');

		$this->db->where('id', (int) $id)
			->where('status', 'pending')
			->update(self::TABLE, array(
				'status'    => 'sending',
				'locked_at' => $now,
				'locked_by' => $worker,
			));

		if ((int) $this->db->affected_rows() === 0)
		{
			return NULL;
		}

		$row = $this->db->where('id', (int) $id)->get(self::TABLE)->row_array();

		return $row ? $row : NULL;
	}

	// ------------------------------------------------------------------

	/**
	 * Counts per status, for the cron summary and for eyeballing the queue.
	 *
	 * @return	array	status => count, always with all four keys present
	 */
	public function status_counts()
	{
		$counts = array('pending' => 0, 'sending' => 0, 'sent' => 0, 'failed' => 0);

		$rows = $this->db->select('status, COUNT(*) AS total', FALSE)
			->group_by('status')
			->get(self::TABLE)
			->result_array();

		foreach ($rows as $row)
		{
			$counts[$row['status']] = (int) $row['total'];
		}

		return $counts;
	}

	// ------------------------------------------------------------------

	/**
	 * The most recent messages, newest first, for the cron summary.
	 *
	 * @param	int	$limit
	 * @param	string	$status	'' for any status
	 * @return	array
	 */
	public function recent($limit = 20, $status = '')
	{
		if ($status !== '')
		{
			$this->db->where('status', $status);
		}

		return $this->db
			->select('id, to_email, subject, category, status, attempts, available_at, created_at, sent_at, last_error')
			->order_by('id', 'DESC')
			->limit(max(1, (int) $limit))
			->get(self::TABLE)
			->result_array();
	}

	// ------------------------------------------------------------------

	/**
	 * Move failed messages back into the queue so they are tried again.
	 *
	 * @param	int	$id	0 for every failed message
	 * @return	int	rows requeued
	 */
	public function retry_failed($id = 0)
	{
		$this->db->where('status', 'failed');

		if ((int) $id > 0)
		{
			$this->db->where('id', (int) $id);
		}

		$this->db->update(self::TABLE, array(
			'status'       => 'pending',
			'attempts'     => 0,
			'available_at' => date('Y-m-d H:i:s'),
			'locked_at'    => NULL,
			'locked_by'    => NULL,
		));

		return (int) $this->db->affected_rows();
	}

	// ------------------------------------------------------------------

	/**
	 * SMTP debug output is long and full of markup; keep it readable.
	 *
	 * @param	string	$error
	 * @return	string
	 */
	private function _trim_error($error)
	{
		$error = trim(preg_replace('/\s+/', ' ', strip_tags((string) $error)));

		return (strlen($error) > 1000) ? substr($error, 0, 1000).'...' : $error;
	}
}
