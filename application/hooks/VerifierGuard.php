<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Keep "Verifier" logins inside the disqualified applicant list.
 *
 * A Verifier is a limited account whose entire purpose is reviewing the
 * disqualified applicant list and the documents issued for those applicants.
 * Most Pages controller methods carry no role check of their own, so the
 * restriction is enforced here in one place: anything outside the short
 * whitelist below is a 403.
 *
 * Only requests from a logged-in Verifier are inspected; every other role
 * falls straight through untouched.
 */
class VerifierGuard
{
    /** controller class (lowercase) => allowed methods, or TRUE for all of them. */
    private $allowed = array(
        'secretariatqualification' => array('disqualified', 'document'),
        'pages' => array('view', 'log_in', 'logout', 'lock', 'lock_user_screen', 'pass_change', 'ma', 'pdf', 'pdfv2', 'pdf_staff', 'registered_profile'),
        'page'  => array('systemhelp'),
    );

    public function check_access()
    {
        $CI =& get_instance();

        if (!isset($CI->session) || $CI->session->userdata('position') !== 'Verifier') {
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
            'Verifier accounts may only review the disqualified applicant list.',
            403,
            'Forbidden'
        );
        exit;
    }
}
