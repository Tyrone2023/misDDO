-- --------------------------------------------------------
-- Outgoing mail queue
--
-- Nothing is sent from inside a web request any more. A page that needs to
-- send mail renders the message and INSERTs it here, which is fast and cannot
-- fail because of a slow or unreachable SMTP server. A cron job running every
-- two minutes (Mailqueue::run) drains the table over SMTP.
--
-- Run once per database:
--   mysql -u root -p depedddomis_db1 < mail_queue.sql
-- --------------------------------------------------------

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE IF NOT EXISTS `mail_queue` (
  `id`            int(11) NOT NULL AUTO_INCREMENT,

  -- Envelope. The message is stored fully rendered: the worker never needs a
  -- base_url or a session, which it does not have when run from cron.
  `to_email`      varchar(255) NOT NULL,
  `to_name`       varchar(255) DEFAULT NULL,
  `from_email`    varchar(255) NOT NULL,
  `from_name`     varchar(255) DEFAULT NULL,
  `reply_to`      varchar(255) DEFAULT NULL,
  `subject`       varchar(255) NOT NULL,
  `body_html`     mediumtext NOT NULL,
  `body_text`     mediumtext DEFAULT NULL,

  -- What this message is for, e.g. 'password_reset'. Only used for reporting.
  `category`      varchar(64) NOT NULL DEFAULT 'general',

  -- Work to carry out once the message is really delivered, as JSON. A
  -- password reset carries the new hash here instead of writing it at request
  -- time, so an account is never left with a password that only the server
  -- knows because the mail could not be delivered.
  `payload`       text DEFAULT NULL,

  -- 1 when the body contains a secret (a temporary password). The body is
  -- replaced with a placeholder as soon as the message is sent.
  `is_sensitive`  tinyint(1) NOT NULL DEFAULT 0,

  `status`        enum('pending','sending','sent','failed') NOT NULL DEFAULT 'pending',
  `attempts`      int(11) NOT NULL DEFAULT 0,
  `max_attempts`  int(11) NOT NULL DEFAULT 5,

  -- Not picked up before this time. Pushed forward on every failure so a dead
  -- mail server is retried with a growing gap instead of every two minutes.
  `available_at`  datetime NOT NULL,

  -- Set while a worker holds the row, so two overlapping cron runs cannot send
  -- the same message twice. A lock older than the configured TTL is reclaimed.
  `locked_at`     datetime DEFAULT NULL,
  `locked_by`     varchar(64) DEFAULT NULL,

  `last_error`    text DEFAULT NULL,
  `created_at`    datetime NOT NULL,
  `sent_at`       datetime DEFAULT NULL,

  PRIMARY KEY (`id`),
  KEY `idx_claim` (`status`, `available_at`),
  KEY `idx_status_created` (`status`, `created_at`),
  KEY `idx_to_email` (`to_email`),
  KEY `idx_category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
