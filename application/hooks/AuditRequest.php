<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * The catch-all half of the audit trail.
 *
 * Audit_model::log() records *what* a known action meant; this hook records
 * that a state-changing request happened at all, so an endpoint nobody
 * instrumented still leaves a trail. Between them nothing a user does to a
 * record goes unrecorded.
 *
 * Two entry points:
 *   begin()  - post_controller_constructor. Runs before any output, which is
 *              the only moment the device cookie can still be set.
 *   finish() - registered as a shutdown function rather than a post_controller
 *              hook, because almost every write endpoint in this project ends
 *              in redirect(), and redirect() calls exit() - post_controller
 *              would never run for exactly the requests that matter most.
 */
class AuditRequest
{
    /** Written already? A shutdown function must not double-fire. */
    private static $done = false;

    /**
     * Verbs that mean a request changed something. Matched as whole words
     * against the controller method only - this project links deletes and
     * status flips as plain anchors, so GET cannot simply be ignored, but
     * `registered_profile` must not read as "register" either.
     */
    private static $write_words = [
        'insert', 'update', 'delete', 'remove', 'add', 'edit', 'save', 'set',
        'upload', 'apply', 'submit', 'create', 'store', 'archive', 'close',
        'open_jv', 'approve', 'deny', 'grant', 'revert', 'validate',
        'disqualify', 'qualify', 'endorse', 'assign', 'unassign', 'rate',
        'lock', 'unlock', 'change', 'cancel', 'restore', 'import',
        'notify', 'confirm', 'reset',
    ];

    /** Never recorded: value-free noise, or requests with no state behind them. */
    private static $skip_uri = [
        'assets/', 'uploads/', 'downloads/', 'resources/',
        'fetch_', 'get_', 'load_', 'search_', 'list_', 'check_',
        'datatable', 'autocomplete', 'select2', 'dropdown',
    ];

    /** Kept out of the recorded payload entirely. */
    private static $redact = [
        'password', 'passwd', 'pass', 'upass', 'used_pass', 'h_upass',
        'confirm_password', 'new_password', 'old_password', 'repeat_password',
        'g-recaptcha-response', 'csrf_test_name', 'secret_key', 'token',
    ];

    /**
     * Prime the audit context while headers can still be sent, then arrange
     * for the request to be recorded however it ends - redirect included.
     */
    public function begin()
    {
        $CI =& get_instance();

        if (!isset($CI->Audit)) {
            return;
        }

        try {
            // Sets the long-lived device cookie if this browser has none yet.
            $CI->Audit->device_id();
        } catch (Exception $e) {
            log_message('error', 'AuditRequest begin failed: ' . $e->getMessage());
        }

        register_shutdown_function([$this, 'finish']);
    }

    /** Record the request, if it was the kind that changes something. */
    public function finish()
    {
        if (self::$done) {
            return;
        }
        self::$done = true;

        try {
            $CI =& get_instance();

            if (!isset($CI->Audit) || !isset($CI->db)) {
                return;
            }

            // Cron and CLI tasks act for no user on no device; they have their
            // own logs, and netting them here would bury the human trail.
            if (is_cli()) {
                return;
            }

            $uri    = $this->uri_string($CI);
            $method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'CLI'));

            if (!$this->is_state_changing($uri, $method)) {
                return;
            }

            // An action that described itself properly needs no generic row.
            if ($CI->Audit->has_specific_entry()) {
                return;
            }

            $upload = $this->uploaded_files();

            $CI->Audit->log($upload ? 'upload_document' : 'request', [
                'entity_type' => $upload ? 'document' : 'request',
                'entity_id'   => $upload ? $this->document_key($uri) : $uri,
                'field'       => $upload ? $this->document_key($uri) : null,
                'description' => $this->describe($CI, $uri, $method, $upload),
                'meta'        => $this->payload(),
                'new_value'   => $upload ? json_encode($upload) : null,
                // Both routing styles used by the write endpoints: the rating
                // pages carry ids in the URI, the upload forms post them.
                'applicant_id' => $this->segment($CI, 3) ?? $this->posted('id'),
                'job_id'       => $this->numeric_segment($CI, 4) ?? $this->posted_int('jobID'),
                'app_id'       => $this->numeric_segment($CI, 6) ?? $this->posted_int('appID'),
            ]);
        } catch (Exception $e) {
            log_message('error', 'AuditRequest finish failed: ' . $e->getMessage());
        } catch (Error $e) {
            log_message('error', 'AuditRequest finish error: ' . $e->getMessage());
        }
    }

    /* ------------------------------------------------------------------ */

    /**
     * A POST always changes something. A GET only counts when its route reads
     * like an action - this project links deletes and status flips as plain
     * anchors, so GET cannot simply be ignored.
     */
    private function is_state_changing($uri, $method)
    {
        $lower = strtolower($uri);

        foreach (self::$skip_uri as $skip) {
            if (strpos($lower, $skip) !== false) {
                return false;
            }
        }

        if ($method !== 'GET' && $method !== 'HEAD') {
            return true;
        }

        // Only the controller method carries the verb; the segments after it
        // are ids, and the one before it is the controller name.
        $segments = explode('/', trim($lower, '/'));
        $action   = $segments[1] ?? '';

        if ($action === '') {
            return false;
        }

        foreach (self::$write_words as $word) {
            if (preg_match('/(^|_)' . preg_quote($word, '/') . '(_|$)/', $action)) {
                return true;
            }
        }

        return false;
    }

    /** One readable sentence naming what happened and who did it. */
    private function describe($CI, $uri, $method, array $upload = [])
    {
        $who  = trim((string) ($CI->session->username ?? ''));
        $role = trim((string) ($CI->session->position ?? ''));
        $by   = $who !== ''
            ? $who . ($role !== '' ? ' (' . $role . ')' : '')
            : 'an unauthenticated visitor';

        if ($upload) {
            return 'Uploaded ' . $this->document_label($uri) . ': '
                . implode(', ', $upload) . ' - by ' . $by
                . ' via ' . $method . ' ' . $uri . '.';
        }

        return $method . ' ' . $uri . ' by ' . $by . '.';
    }

    /** Names of the files that actually arrived on this request. */
    private function uploaded_files()
    {
        $names = [];

        foreach ($_FILES as $field => $f) {
            foreach ((array) ($f['name'] ?? []) as $name) {
                $name = trim((string) $name);
                if ($name !== '') {
                    $names[] = $field . '=' . $name;
                }
            }
        }

        return $names;
    }

    /**
     * The document a route deals in, read off the endpoint name.
     * `pages/update_pdsfile` -> `pdsfile`, which is also the column it lands in
     * for most of these forms.
     */
    private function document_key($uri)
    {
        $parts = explode('/', trim((string) $uri, '/'));
        $method = strtolower(end($parts) ?: '');

        $key = preg_replace('/^(update|insert|upload|add|save)_/', '', $method);

        return $key !== '' ? $key : $method;
    }

    /** Same thing, worded for a human reading the timeline. */
    private function document_label($uri)
    {
        $key = $this->document_key($uri);

        $known = [
            'pdsfile'     => 'Personal Data Sheet',
            'efile'       => 'eligibility document',
            'efile_none'  => 'eligibility document',
            'aefile'      => 'additional eligibility document',
            'aldfile'     => 'awards / leadership document',
            'wefile'      => 'work experience document',
            'letfile'     => 'LET / licensure document',
            'tscfile'     => 'transcript of records',
            'tcfile'      => 'training certificate',
            'apfile'      => 'application letter',
            'apfile_rploi'=> 'letter of intent',
            'oafile'      => 'omnibus affidavit',
            'omni'        => 'omnibus certification',
            'outfile'     => 'outstanding accomplishment document',
            'ipcrffile'   => 'IPCRF',
            'voters'      => "voter's certificate",
            'master_file' => "master's degree document",
            'doctor_file' => 'doctorate document',
            'eligibility' => 'eligibility document',
        ];

        $key = preg_replace('/_staff$/', '', $key);

        return $known[$key] ?? ('document (' . $key . ')');
    }

    private function posted($key)
    {
        $value = isset($_POST[$key]) ? trim((string) $_POST[$key]) : '';

        return $value !== '' ? $value : null;
    }

    private function posted_int($key)
    {
        $value = $this->posted($key);

        return ($value !== null && ctype_digit($value)) ? (int) $value : null;
    }

    /** The posted values, with secrets stripped and long values trimmed. */
    private function payload()
    {
        if (empty($_POST)) {
            return null;
        }

        $out = [];

        foreach ($_POST as $key => $value) {
            if (in_array(strtolower((string) $key), self::$redact, true)) {
                $out[$key] = '[redacted]';
                continue;
            }

            if (is_array($value)) {
                $value = json_encode($value);
            }

            $value = (string) $value;
            $out[$key] = mb_strlen($value) > 200 ? mb_substr($value, 0, 197) . '...' : $value;
        }

        return $out;
    }

    private function uri_string($CI)
    {
        $uri = isset($CI->uri) ? trim((string) $CI->uri->uri_string(), '/') : '';

        return $uri !== '' ? $uri : trim((string) ($_SERVER['REQUEST_URI'] ?? ''), '/');
    }

    private function segment($CI, $n)
    {
        if (!isset($CI->uri)) {
            return null;
        }

        $value = trim((string) $CI->uri->segment($n));

        return $value !== '' ? $value : null;
    }

    private function numeric_segment($CI, $n)
    {
        $value = $this->segment($CI, $n);

        return ($value !== null && ctype_digit($value)) ? (int) $value : null;
    }
}
