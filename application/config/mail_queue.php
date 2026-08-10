<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| OUTGOING MAIL QUEUE
| -------------------------------------------------------------------------
|
| Settings for the `mail_queue` table (see mail_queue.sql) and the worker that
| drains it, Mailqueue::run, which cron calls every two minutes.
|
| SMTP credentials themselves live in config/email.php, not here.
*/

/*
| How many messages one cron run sends. The run holds a single authenticated
| SMTP connection open for the whole batch, so this is cheap; it is capped only
| so a run always finishes well inside the two minute window.
*/
$config['mail_queue_batch'] = 25;

/*
| How many times a message is retried before it is marked 'failed' and left
| alone for someone to look at.
*/
$config['mail_queue_max_attempts'] = 5;

/*
| Seconds to wait before each retry. The last value is reused once the list runs
| out. A mail server that is down for an hour therefore costs a handful of
| attempts rather than thirty.
*/
$config['mail_queue_backoff'] = array(120, 600, 1800, 3600);

/*
| Seconds after which a message still marked 'sending' is assumed to belong to
| a worker that died (host reboot, PHP timeout) and is put back in the queue.
| Must comfortably exceed the longest a batch can take.
*/
$config['mail_queue_lock_ttl'] = 900;

/*
| Days to keep rows that were sent successfully, for auditing "did the reset
| email actually go out". Pruned by the same cron run. 0 disables pruning.
*/
$config['mail_queue_retention_days'] = 30;

/*
| Try to deliver a message once, immediately, in the request that queued it.
| When the mail server is healthy the recipient gets the mail in a second or
| two instead of waiting for the next cron tick; when it is not, the attempt
| fails quietly and cron picks the message up as usual. The user-facing result
| never depends on it.
|
| Set to FALSE to make every message go strictly through cron - worth doing if
| the mail server is slow enough that the inline attempt is noticeable.
*/
$config['mail_queue_inline'] = TRUE;

/*
| Seconds the inline attempt above may spend talking to the mail server. Kept
| shorter than config/email.php's smtp_timeout so a stalled server cannot hold
| up the page the visitor is waiting on.
*/
$config['mail_queue_inline_timeout'] = 5;

/*
| -------------------------------------------------------------------------
| BOUNCE READING
| -------------------------------------------------------------------------
|
| `sent` in the queue only means our own mail server accepted the message for
| relay. A recipient that refuses it does so afterwards, and says so in a
| bounce sent back to the sending mailbox - which is why a message that was
| never delivered can sit in the queue marked `sent`.
|
| `php index.php mailqueue bounces` logs into that mailbox over IMAP, reads
| the bounces and marks the matching messages `bounced` with the reason. It
| only ever reads: nothing is deleted, and messages are not even marked read.
|
| Host and mailbox default to the SMTP ones from config/email.php.
*/
$config['mail_queue_imap_host'] = getenv('SRMS_IMAP_HOST') ?: '';
$config['mail_queue_imap_port'] = (int) (getenv('SRMS_IMAP_PORT') ?: 993);

/*
| How far back to read. Bounces can take hours to come back - a recipient
| server that defers before giving up can take a day or two - so this wants to
| be comfortably longer than the gap between runs.
*/
$config['mail_queue_bounce_days'] = 7;

/*
| Shared secret for triggering the worker over HTTP:
|
|     https://your-site/mailqueue/run?token=...
|
| Only needed on hosts that cannot run a real CLI cron job. Leave empty to
| refuse HTTP triggering entirely, which is the safer default; the CLI entry
| point (php index.php mailqueue run) never uses a token.
|
| Set it through the environment rather than by editing this file.
*/
$config['mail_queue_cron_token'] = getenv('SRMS_CRON_TOKEN') ?: '';
