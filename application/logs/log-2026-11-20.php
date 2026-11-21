<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2026-11-20 21:04:01 --> Query error: Table 'depedddomis_db.mail_queue' doesn't exist - Invalid query: UPDATE `mail_queue` SET `status` = 'pending', `locked_at` = NULL, `locked_by` = NULL
WHERE `status` = 'sending'
AND `locked_at` < '2026-11-20 20:49:01'
ERROR - 2026-11-20 22:56:06 --> Mail queue: ampojerrickvin2020@gmail.con was rejected by the mail server (mail_queue #628); not retrying.
