<?php defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| SMTP CREDENTIALS
| -------------------------------------------------------------------------
|
| Used by the mail queue worker (Mailqueue::run) and by anything else that
| loads CodeIgniter's Email library. Nothing sends mail directly from a web
| request any more - see application/config/mail_queue.php.
|
| Every setting can be overridden from the environment, so credentials can be
| rotated on the live server - and the whole thing pointed somewhere else for
| testing - without editing this file. The literals are the fallback for
| machines with no environment set up.
|
| To check the credentials without sending anything:
|   php index.php mailqueue check
*/

$config['protocol']     = 'smtp';
$config['smtp_host']    = getenv('SRMS_SMTP_HOST') ?: 'mail.depedddo-mis.com';
$config['smtp_user']    = getenv('SRMS_SMTP_USER') !== FALSE ? getenv('SRMS_SMTP_USER') : 'ddorecruitmentsystem@depedddo-mis.com';
$config['smtp_pass']    = getenv('SRMS_SMTP_PASS') !== FALSE ? getenv('SRMS_SMTP_PASS') : '@Moth34board';
$config['smtp_port']    = (int) (getenv('SRMS_SMTP_PORT') ?: 465);

// '' for a plain connection, 'ssl' for port 465, 'tls' for port 587.
$config['smtp_crypto']  = getenv('SRMS_SMTP_CRYPTO') !== FALSE ? getenv('SRMS_SMTP_CRYPTO') : 'ssl';

$config['smtp_timeout'] = 10;
$config['mailtype']     = 'html';
$config['charset']      = 'utf-8';
$config['newline']      = "\r\n";
$config['crlf']         = "\r\n";
$config['wordwrap']     = true;

/*
| Envelope defaults. The From address has to be a mailbox that belongs to
| smtp_user's domain or the server will reject the message as a relay attempt.
*/
$config['smtp_from_email'] = $config['smtp_user'];
$config['smtp_from_name']  = 'DepEd Davao de Oro MIS';
