<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Makes $this->load->database() survive the short connection refusals we get
 * from the host.
 *
 * Every so often the MySQL socket answers a new connection with
 * "mysqli::real_connect(): (HY000/2002): Operation not permitted", which used
 * to dump a raw PHP warning plus CodeIgniter's database error page on top of
 * whatever page the user was on. The server accepts connections again a moment
 * later, so we retry a couple of times before giving up and we keep the failed
 * attempts out of the response.
 */
class MY_Loader extends CI_Loader
{
	/**
	 * How many times to try connecting before showing an error.
	 *
	 * @var	int
	 */
	const DB_ATTEMPTS = 3;

	/**
	 * Microseconds to wait after the first failure. Doubled on each retry.
	 *
	 * @var	int
	 */
	const DB_RETRY_DELAY = 150000;

	/**
	 * Seconds to spend on the whole thing before giving up.
	 *
	 * A refused connection comes back instantly, so the retries cost almost
	 * nothing. A server that hangs instead of refusing hits the driver's
	 * 10 second connect timeout, and we don't want to sit through that three
	 * times over while holding a PHP worker.
	 *
	 * @var	float
	 */
	const DB_TIME_BUDGET = 12.0;

	/**
	 * Query builder flag read from the database config file.
	 *
	 * @var	bool|null
	 */
	private $_db_query_builder = NULL;

	// --------------------------------------------------------------------

	/**
	 * Database Loader
	 *
	 * Same contract as CI_Loader::database(), except a connection failure is
	 * retried before it reaches the user.
	 *
	 * @param	mixed	$params		Database configuration options
	 * @param	bool	$return 	Whether to return the database object
	 * @param	bool	$query_builder	Whether to enable Query Builder
	 *					(overrides the config setting)
	 * @return	object|bool	Database object if $return is set to TRUE,
	 *					FALSE on failure, CI_Loader instance in any other case
	 */
	public function database($params = '', $return = FALSE, $query_builder = NULL)
	{
		$CI =& get_instance();

		// Already connected - the same short circuit CI_Loader uses.
		if ($return === FALSE && $query_builder === NULL && isset($CI->db) && is_object($CI->db) && ! empty($CI->db->conn_id))
		{
			return FALSE;
		}

		$config = $this->_db_settings($params);

		// Settings we can't resolve ourselves, a DSN string for instance.
		// Nothing to retry with, so let CodeIgniter handle it as usual.
		if ($config === FALSE)
		{
			return parent::database($params, $return, $query_builder);
		}

		if ($query_builder === NULL)
		{
			$query_builder = $this->_db_query_builder;
		}

		$db_debug = ! empty($config['db_debug']);
		$hostname = $config['hostname'];
		$alternate = $this->_db_alternate_host($hostname);
		$deadline = microtime(TRUE) + self::DB_TIME_BUDGET;
		$delay = self::DB_RETRY_DELAY;
		$connected = FALSE;
		$db = FALSE;

		// Connect quietly: a failed attempt we're about to retry has no
		// business printing anything into the page.
		$config['db_debug'] = FALSE;

		for ($attempt = 1; $attempt <= self::DB_ATTEMPTS; $attempt++)
		{
			// A refused TCP connect is the usual cause, so the second attempt
			// goes through the local socket instead (or the other way round).
			// The last attempt is made against the configured host again.
			$config['hostname'] = ($attempt === 2 && $alternate !== NULL) ? $alternate : $hostname;

			$db = $this->_db_connect($config, $query_builder);

			if (is_object($db) && ! empty($db->conn_id))
			{
				$connected = TRUE;
				break;
			}

			if ($attempt < self::DB_ATTEMPTS && microtime(TRUE) >= $deadline)
			{
				log_message('error', 'Gave up connecting to the database after '.$attempt.' attempts, out of time.');
				break;
			}

			if ($attempt < self::DB_ATTEMPTS)
			{
				usleep($delay);
				$delay *= 2;
			}
		}

		if ($connected)
		{
			// Hand back a connection that reports query errors exactly as
			// configured; only the connecting was done quietly.
			$db->db_debug = $db_debug;

			if ($attempt > 1)
			{
				log_message('error', 'Database connection recovered on attempt '.$attempt.' of '.self::DB_ATTEMPTS.'.');
			}

			if ($return === TRUE)
			{
				return $db;
			}

			$CI->db = $db;
			return $this;
		}

		log_message('error', 'Unable to connect to the database.');

		// Nothing in this system runs without the database, so stop on a page
		// that says so rather than letting the request carry on with a dead
		// connection and break further down in a much more confusing way.
		$this->_db_unavailable();
	}

	// --------------------------------------------------------------------

	/**
	 * Attempt a single connection, without printing anything
	 *
	 * mysqli::real_connect() raises an E_WARNING that CodeIgniter's error
	 * handler prints straight into the page. It goes to the log instead.
	 *
	 * @param	array	$config		Resolved connection settings
	 * @param	bool	$query_builder	Whether to enable Query Builder
	 * @return	object|bool
	 */
	private function _db_connect($config, $query_builder)
	{
		set_error_handler(function ($severity, $message) {
			log_message('error', 'Database connection: '.$message);
			return TRUE;
		});

		try
		{
			$db = parent::database($config, TRUE, $query_builder);
		}
		catch (Exception $e)
		{
			log_message('error', 'Database connection: '.$e->getMessage());
			$db = FALSE;
		}

		restore_error_handler();

		return $db;
	}

	// --------------------------------------------------------------------

	/**
	 * Resolve the settings CodeIgniter would connect with
	 *
	 * Mirrors what DB() does with the config file, so we can retry with the
	 * same settings while overriding db_debug.
	 *
	 * @param	mixed	$params	Whatever was passed to database()
	 * @return	array|bool	Connection settings, or FALSE if we can't tell
	 */
	private function _db_settings($params)
	{
		if (is_array($params))
		{
			return empty($params['hostname']) ? FALSE : $params;
		}

		// A group name is all we handle here; a DSN string is CI's job.
		if ( ! is_string($params) OR ($params !== '' && ! preg_match('/^[a-z0-9_]+$/i', $params)))
		{
			return FALSE;
		}

		$db = array();
		$active_group = NULL;
		$query_builder = NULL;

		if (file_exists($file = APPPATH.'config/'.ENVIRONMENT.'/database.php'))
		{
			include($file);
		}
		elseif (file_exists($file = APPPATH.'config/database.php'))
		{
			include($file);
		}
		else
		{
			return FALSE;
		}

		$group = ($params !== '') ? $params : $active_group;

		if ($group === NULL OR ! isset($db[$group]) OR ! is_array($db[$group]) OR empty($db[$group]['hostname']))
		{
			return FALSE;
		}

		$this->_db_query_builder = $query_builder;

		return $db[$group];
	}

	// --------------------------------------------------------------------

	/**
	 * The other way of reaching a local MySQL server
	 *
	 * "localhost" goes through the unix socket while "127.0.0.1" opens a TCP
	 * connection, and it is nearly always the TCP one that gets refused. Any
	 * other hostname is a real server we shouldn't second-guess.
	 *
	 * @param	string	$hostname
	 * @return	string|null
	 */
	private function _db_alternate_host($hostname)
	{
		if ($hostname === '127.0.0.1' OR $hostname === '::1')
		{
			return 'localhost';
		}

		return ($hostname === 'localhost') ? '127.0.0.1' : NULL;
	}

	// --------------------------------------------------------------------

	/**
	 * Show the "service unavailable" page and stop
	 *
	 * @return	void
	 */
	private function _db_unavailable()
	{
		$exceptions =& load_class('Exceptions', 'core');
		$lang =& load_class('Lang', 'core');
		$lang->load('db');

		echo $exceptions->show_error(
			$lang->line('db_error_heading'),
			$lang->line('db_unable_to_connect'),
			'error_db',
			503
		);

		exit(EXIT_DATABASE);
	}
}
