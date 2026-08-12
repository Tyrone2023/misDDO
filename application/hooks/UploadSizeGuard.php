<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Catch uploads that are larger than post_max_size.
 *
 * When a POST body exceeds post_max_size, PHP discards it: $_POST and $_FILES
 * both arrive empty and no warning reaches the application. The upload handlers
 * then read $this->input->post('jobID') as NULL and redirect to a URL with empty
 * segments, so the user lands on a broken page with no idea what went wrong.
 *
 * Detect the condition up front and send them back where they came from with a
 * message that says what actually happened.
 */
class UploadSizeGuard
{
    public function check_post_size()
    {
        if (strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? '')) !== 'POST') {
            return;
        }

        $limit = $this->post_max_bytes();
        if ($limit <= 0) {
            return;
        }

        $length = (int) ($_SERVER['CONTENT_LENGTH'] ?? 0);
        if ($length <= $limit) {
            return;
        }

        $CI =& get_instance();

        $message = sprintf(
            'That upload is %s, which is over the %s limit for this site. '
            . 'Please scan at a lower resolution, or compress or split the PDF, then try again.',
            $this->human($length),
            $this->human($limit)
        );

        if (isset($CI->session)) {
            $CI->session->set_flashdata('danger', $message);
        }

        // The POST body is gone, so there is nothing to recover - send the user
        // back to the form they submitted from.
        $referer = (string) ($_SERVER['HTTP_REFERER'] ?? '');
        $target  = ($referer !== '' && strpos($referer, base_url()) === 0) ? $referer : base_url();

        header('Location: ' . $target, TRUE, 302);
        exit;
    }

    private function post_max_bytes()
    {
        $value = trim((string) ini_get('post_max_size'));
        if ($value === '') {
            return 0;
        }

        $number = (int) $value;
        switch (strtolower(substr($value, -1))) {
            case 'g':
                return $number * 1024 * 1024 * 1024;
            case 'm':
                return $number * 1024 * 1024;
            case 'k':
                return $number * 1024;
            default:
                return $number;
        }
    }

    private function human($bytes)
    {
        return number_format($bytes / (1024 * 1024), 1) . ' MB';
    }
}
