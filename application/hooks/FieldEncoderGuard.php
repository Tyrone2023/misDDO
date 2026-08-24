<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Keep "Field Encoder" logins inside score encoding.
 *
 * A Field Encoder is a limited account a Secretariat creates (secretariat/encoders)
 * whose entire purpose is encoding Interview / Written Examination scores. Most
 * Pages controller methods carry no role check of their own, so the restriction is
 * enforced here in one place: anything outside the short whitelist below is a 403.
 *
 * Only requests from a logged-in Field Encoder are inspected; every other role
 * falls straight through untouched.
 */
class FieldEncoderGuard
{
    /** controller class (lowercase) => allowed methods, or TRUE for all of them. */
    private $allowed = array(
        'secretariatscores' => true,
        'pages' => array('view', 'log_in', 'logout', 'lock', 'lock_user_screen', 'pass_change'),
        'page'  => array('systemhelp'),
    );

    public function check_access()
    {
        $CI =& get_instance();

        if (!isset($CI->session) || $CI->session->userdata('position') !== 'Field Encoder') {
            return;
        }

        $class  = strtolower((string) $CI->router->fetch_class());
        $method = strtolower((string) $CI->router->fetch_method());

        if (!array_key_exists($class, $this->allowed)) {
            $this->deny();
        }

        $methods = $this->allowed[$class];
        if ($methods !== true && !in_array($method, $methods, true)) {
            $this->deny();
        }
    }

    private function deny()
    {
        show_error(
            'Field Encoder accounts may only encode Interview and Written Examination scores.',
            403,
            'Forbidden'
        );
        exit;
    }
}
