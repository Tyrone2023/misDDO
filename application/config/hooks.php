<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	https://codeigniter.com/user_guide/general/hooks.html
|
*/

$config['enable_hooks'] = TRUE;

$hook['pre_controller'] = array(
    'class'    => 'MaintenanceMode',
    'function' => 'check_maintenance',
    'filename' => 'MaintenanceMode.php',
    'filepath' => 'hooks',
);

// Runs after the controller is constructed so the session (and therefore
// flashdata) is available, but before any controller method executes.
$hook['post_controller_constructor'] = array(
    array(
        'class'    => 'UploadSizeGuard',
        'function' => 'check_post_size',
        'filename' => 'UploadSizeGuard.php',
        'filepath' => 'hooks',
    ),
    // Field Encoder logins are limited to secretariat/scores; most Pages methods
    // carry no role check of their own, so the restriction is enforced here.
    array(
        'class'    => 'FieldEncoderGuard',
        'function' => 'check_access',
        'filename' => 'FieldEncoderGuard.php',
        'filepath' => 'hooks',
    ),
    // Verifier logins are limited to secretariat/disqualified and the documents
    // issued for those applicants; most Pages methods carry no role check of
    // their own, so the restriction is enforced here.
    array(
        'class'    => 'VerifierGuard',
        'function' => 'check_access',
        'filename' => 'VerifierGuard.php',
        'filepath' => 'hooks',
    ),
    // Audit trail catch-all. begin() runs here because it is the last moment
    // the device cookie can still be set; it registers a shutdown function to
    // write the row, since redirect() exits before post_controller would run.
    array(
        'class'    => 'AuditRequest',
        'function' => 'begin',
        'filename' => 'AuditRequest.php',
        'filepath' => 'hooks',
    ),
);
