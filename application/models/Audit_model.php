<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Detailed audit trail for the recruitment / rating workflow.
 *
 * Writes one row per meaningful action into `hris_audit_trail`, snapshotting the
 * actor's identity (username, first/last name, position) so the log stays
 * readable even if the user record later changes. Autoloaded as `Audit`, so it
 * is reachable from both controllers and models (`$this->Audit->log(...)`).
 */
class Audit_model extends CI_Model
{
    /**
     * Field set a new row's hash is taken over. Bump when the sealed columns
     * change, and extend hash_payload() rather than editing the old version.
     */
    const HASH_VERSION = 2;

    /** Cookie holding the long-lived device id (the practical MAC stand-in). */
    const DEVICE_COOKIE = 'mis_did';

    /** Cookie holding the browser fingerprint hash written by device_print.js. */
    const PRINT_COOKIE = 'mis_dfp';

    /** Per-request cache of resolved actor rows, keyed by user id. */
    private $actor_cache = [];

    /** Per-request cache of built application logs, keyed by app id. */
    private $log_cache = [];

    /** Per-request cache of resolved record owners, keyed by identifier. */
    private $subject_cache = [];

    /** Resolved request context (ip, device, user agent...), built once. */
    private $ctx = null;

    /** Id shared by every trail row written during this one request. */
    private $request_id = null;

    /** Device id for this request, once decided. */
    private $device_id = null;

    /** Hash of the last row this request wrote, for the chain. */
    private $chain_tail = null;

    /** True once a purpose-written entry has described this request. */
    private $specific_logged = false;

    /** ARP lookups are cached per address for the life of the request. */
    private static $mac_cache = [];

    /** ensure_schema() only has to run once per request. */
    private static $schema_ready = false;

    /** Never diffed: noise, or secrets that must not reach the trail. */
    private static $never_diff = [
        'password', 'passwd', 'pass', 'upass', 'used_pass', 'h_upass',
        'updated_at', 'created_at', 'lastupdate', 'last_update',
    ];

    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Manila');
    }

    /**
     * Resolve the acting user's identity snapshot from the users table.
     * Falls back to whatever the session carries when the row is missing.
     */
    private function resolve_actor($user_id = null)
    {
        $id = $user_id !== null ? $user_id : ($this->session->id ?? $this->session->userdata('id'));

        if (!empty($id) && array_key_exists((int) $id, $this->actor_cache)) {
            return $this->actor_cache[(int) $id];
        }

        $actor = null;
        if (!empty($id)) {
            $actor = $this->db
                ->select('id, username, fname, mname, lname, position')
                ->where('id', $id)
                ->get('users')
                ->row();
            $this->actor_cache[(int) $id] = $actor;
        }

        return $actor;
    }

    /**
     * Record an audit event.
     *
     * @param string $action e.g. submit_application, upload_document,
     *                       update_document, delete_document, validate,
     *                       disqualify, status_change, rate
     * @param array  $o      optional overrides: user_id, entity_type, entity_id,
     *                       app_id, applicant_id, job_id, field, description
     */
    public function log($action, array $o = [])
    {
        $this->ensure_schema();

        $actor = $this->resolve_actor($o['user_id'] ?? null);
        $ctx   = $this->context();

        $data = [
            'created_at'   => date('Y-m-d H:i:s'),
            'user_id'      => $o['user_id'] ?? ($actor->id ?? ($this->session->id ?? null)),
            'username'     => $actor->username ?? ($this->session->username ?? null),
            'fname'        => $actor->fname ?? null,
            'lname'        => $actor->lname ?? null,
            'position'     => $actor->position ?? ($this->session->position ?? null),
            'action'       => (string) $action,
            'entity_type'  => $o['entity_type'] ?? null,
            'entity_id'    => isset($o['entity_id']) ? (string) $o['entity_id'] : null,
            'entity_table' => $o['entity_table'] ?? null,
            'app_id'       => isset($o['app_id']) && $o['app_id'] !== '' ? (int) $o['app_id'] : null,
            'applicant_id' => isset($o['applicant_id']) ? (string) $o['applicant_id'] : null,
            'job_id'       => isset($o['job_id']) && $o['job_id'] !== '' ? (int) $o['job_id'] : null,
            'field'        => $o['field'] ?? null,
            'description'  => isset($o['description']) ? mb_substr((string) $o['description'], 0, 4000) : null,
            'old_value'    => isset($o['old_value']) ? mb_substr((string) $o['old_value'], 0, 4000) : null,
            'new_value'    => isset($o['new_value']) ? mb_substr((string) $o['new_value'], 0, 4000) : null,
            'meta'         => isset($o['meta'])
                ? mb_substr(is_string($o['meta']) ? $o['meta'] : json_encode($o['meta']), 0, 4000)
                : null,
        ];

        // Who is answerable for this row, spelled out.
        $data['responsible'] = $this->responsible_name($actor);

        // Whose record it was. Defaults to the applicant the action names;
        // `subject_id` may be overridden where that is not the record owner.
        $subject = isset($o['subject_id']) ? (string) $o['subject_id'] : $data['applicant_id'];

        $data['subject_id']   = $subject !== null && $subject !== '' ? (string) $subject : null;
        $data['subject_name'] = $o['subject_name'] ?? $this->subject_name($subject);

        // The accountability distinction that matters on review: did the
        // applicant change their own record, or did staff change it for them?
        $data['acted_on_self'] = ($data['subject_id'] !== null && $data['user_id'] !== null)
            ? (int) ((string) $data['subject_id'] === (string) ($this->session->c_id ?? ''))
            : null;

        // Who, from where, on which device - attached to every row without the
        // ~40 existing call sites having to know about it.
        $data += $ctx;

        // The AuditRequest net only writes when nothing else described this
        // request, so a well-described action is never logged twice.
        if ($data['action'] !== 'request') {
            $this->specific_logged = true;
        }

        // Auditing must never break the action it is recording.
        try {
            return $this->db->insert('hris_audit_trail', $this->seal($data));
        } catch (Exception $e) {
            log_message('error', 'Audit log failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Latest person who rated a given component of an application.
     * Returns a users-shaped row (username, fname, mname, lname) or null.
     */
    public function last_rater($app_id, $field)
    {
        if (empty($app_id) || $field === null || $field === '') {
            return null;
        }

        return $this->db
            ->select('user_id, username, fname, lname, created_at')
            ->from('hris_audit_trail')
            ->where('app_id', (int) $app_id)
            ->where('field', $field)
            ->where('action', 'rate')
            ->order_by('id', 'desc')
            ->limit(1)
            ->get()
            ->row();
    }

    /** Format a users-shaped row as "Lastname, Firstname (username)". */
    private function format_name($row)
    {
        if (!$row) {
            return '';
        }
        $name = trim(trim((string) ($row->lname ?? '')) . ', ' . trim((string) ($row->fname ?? '')));
        $name = trim($name, ', ');
        if (!empty($row->username)) {
            $name .= ' (' . $row->username . ')';
        }
        return trim($name);
    }

    /**
     * Human-readable "Lastname, Firstname (username)" for the latest rater of a
     * component. Falls back to a user id (eval_id) for pre-audit scores.
     */
    public function rater_name($app_id, $field, $fallback_user_id = null)
    {
        $row = $this->last_rater($app_id, $field);

        if (!$row && !empty($fallback_user_id)) {
            $row = $this->db
                ->select('id as user_id, username, fname, lname')
                ->where('id', $fallback_user_id)
                ->get('users')
                ->row();
        }

        return $this->format_name($row);
    }

    /**
     * Every distinct person who has encoded a rating for a component, oldest
     * first (one entry per user, keeping their most recent name snapshot).
     * Falls back to a single user id (eval_id) when the audit trail has nothing
     * yet. Returns an array of "Lastname, Firstname (username)" strings.
     */
    public function raters($app_id, $field, $fallback_user_id = null)
    {
        $names = [];

        if (!empty($app_id) && $field !== null && $field !== '') {
            $rows = $this->db
                ->select('user_id, username, fname, lname')
                ->from('hris_audit_trail')
                ->where('app_id', (int) $app_id)
                ->where('field', $field)
                ->where('action', 'rate')
                ->order_by('id', 'asc')
                ->get()
                ->result();

            // De-duplicate by user (keep first-rated ordering, latest snapshot).
            $byUser = [];
            foreach ($rows as $r) {
                $key = ($r->user_id !== null && $r->user_id !== '')
                    ? 'u' . $r->user_id
                    : 'n' . ($r->username ?? '');
                $byUser[$key] = $r;
            }
            foreach ($byUser as $r) {
                $name = $this->format_name($r);
                if ($name !== '') {
                    $names[] = $name;
                }
            }
        }

        if (empty($names) && !empty($fallback_user_id)) {
            $u = $this->db
                ->select('id as user_id, username, fname, lname')
                ->where('id', $fallback_user_id)
                ->get('users')
                ->row();
            $name = $this->format_name($u);
            if ($name !== '') {
                $names[] = $name;
            }
        }

        return $names;
    }

    /* --------------------------------------------------------------------
     * Consolidated activity log for one application
     * ------------------------------------------------------------------ */

    /**
     * Every log entry recorded for a single application, newest first.
     *
     * Merges the two stores the recruitment workflow writes to:
     *   - `hris_applications_track` — the applicant-facing status timeline
     *     (submitted / validated / endorsed / rated) plus remarks & comments.
     *   - `hris_audit_trail`        — the detailed per-action trail (who
     *     encoded which rating, document uploads, status changes).
     *
     * Rows are normalised to a common shape so the tracking modal can render
     * them as one stream:
     *   ts, when, date, time, source, action, label, detail, actor, actor_role,
     *   icon, tone
     *
     * @param int|string      $app_id       hris_applications.appID
     * @param int|string|null $applicant_id used to pick up legacy track rows
     *                                      written before app_id was stored
     * @param int|string|null $job_id       scopes those legacy rows to this job
     */
    public function application_log($app_id, $applicant_id = null, $job_id = null)
    {
        // The button and the modal are rendered separately; build the log once.
        $cacheKey = (int) $app_id . '|' . $applicant_id . '|' . $job_id;
        if (array_key_exists($cacheKey, $this->log_cache)) {
            return $this->log_cache[$cacheKey];
        }

        $entries = [];

        $entries = array_merge($entries, $this->track_entries($app_id, $applicant_id, $job_id));
        $entries = array_merge($entries, $this->audit_entries($app_id, $applicant_id));

        $this->attach_actor_names($entries);

        // Latest on top, oldest at the bottom. Equal timestamps fall back to the
        // row id so same-second events still read in the order they happened.
        usort($entries, static function ($a, $b) {
            if ($a['ts'] === $b['ts']) {
                return $b['seq'] - $a['seq'];
            }
            return ($b['ts'] < $a['ts']) ? -1 : 1;
        });

        $this->log_cache[$cacheKey] = $entries;

        return $entries;
    }

    /** Normalised rows from the applicant-facing status timeline. */
    private function track_entries($app_id, $applicant_id = null, $job_id = null)
    {
        $app_id = (int) $app_id;
        if ($app_id <= 0) {
            return [];
        }

        $this->db->from('hris_applications_track')->group_start()->where('app_id', $app_id);

        // Older rows were written without app_id; recover them by applicant+job.
        if (!empty($applicant_id) && !empty($job_id)) {
            $this->db->or_group_start()
                ->where('applicant_id', $applicant_id)
                ->where('jobID', (int) $job_id)
                ->where('app_id', 0)
                ->group_end();
        }

        $rows = $this->db->group_end()->order_by('trackID', 'desc')->get()->result();

        $entries = [];
        foreach ($rows as $r) {
            $text  = trim((string) ($r->appStatus ?? ''));
            $meta  = $this->classify_status($text);
            $note  = trim((string) ($r->note ?? ''));
            $stamp = trim(($r->dateSubmitted ?? '') . ' ' . ($r->timeSubmitted ?? ''));

            $entries[] = [
                'ts'         => $this->to_timestamp($stamp, $r->dateSubmitted ?? null),
                'when'       => $stamp,
                'date'       => $r->dateSubmitted ?? '',
                'time'       => $r->timeSubmitted ?? '',
                'seq'        => (int) $r->trackID,
                'source'     => 'track',
                'action'     => $meta['action'],
                'label'      => $meta['label'],
                'detail'     => $meta['detail'] ? $text : '',
                'note'       => $note,
                'actor_key'  => trim((string) ($r->res ?? '')),
                'actor'      => trim((string) ($r->res ?? '')),
                'actor_role' => '',
                'icon'       => $meta['icon'],
                'tone'       => $meta['tone'],
                // The status timeline records no origin of its own.
                'ip'         => '',
                'mac'        => '',
                'device'     => '',
                'sealed'     => false,
                'responsible' => '',
                'subject'     => '',
                'on_self'     => null,
            ];
        }

        return $entries;
    }

    /**
     * Normalised rows from the detailed audit trail.
     *
     * Picks up two kinds of row: those tied to this application, and the
     * applicant-level ones written without an app_id — trainings, work
     * experience and profile attachments belong to the applicant rather than
     * to a single application, but they are what every application of theirs
     * is evaluated on, so they belong in this log too.
     */
    private function audit_entries($app_id, $applicant_id = null)
    {
        $app_id = (int) $app_id;
        if ($app_id <= 0) {
            return [];
        }

        $this->db->from('hris_audit_trail')->group_start()->where('app_id', $app_id);

        if (!empty($applicant_id)) {
            $this->db->or_group_start()
                ->where('app_id IS NULL', null, false)
                ->where('applicant_id', (string) $applicant_id)
                ->group_end();
        }

        $rows = $this->db->group_end()->order_by('id', 'desc')->get()->result();

        $entries = [];
        foreach ($rows as $r) {
            $meta = $this->classify_action((string) $r->action);
            $name = trim(trim((string) ($r->lname ?? '')) . ', ' . trim((string) ($r->fname ?? '')));
            $name = trim($name, ', ');
            if ($name === '') {
                $name = (string) ($r->username ?? '');
            } elseif (!empty($r->username)) {
                $name .= ' (' . $r->username . ')';
            }

            $entries[] = [
                'ts'         => $this->to_timestamp($r->created_at),
                'when'       => (string) $r->created_at,
                'date'       => date('Y-m-d', $this->to_timestamp($r->created_at)),
                'time'       => date('h:i:s a', $this->to_timestamp($r->created_at)),
                'seq'        => (int) $r->id,
                'source'     => 'audit',
                'action'     => $meta['action'],
                'label'      => $meta['label'],
                'detail'     => (string) ($r->description ?? ''),
                'note'       => '',
                'actor_key'  => '',
                'actor'      => $name,
                'actor_role' => (string) ($r->position ?? ''),
                'icon'       => $meta['icon'],
                'tone'       => $meta['tone'],
                // Forensic half: where the action came from, so an entry can
                // be tied to a machine and not only to an account.
                'ip'         => (string) ($r->ip_address ?? ''),
                'mac'        => (string) ($r->mac_address ?? ''),
                'device'     => (string) ($r->device_id ?? ''),
                'sealed'     => !empty($r->row_hash),
                // Accountability: who is answerable, and whose record it was.
                // Rows predating these columns cannot be backfilled - the table
                // is append-only - so the owner is resolved live for those. One
                // timeline covers a single applicant, and the lookup is cached,
                // so this stays a single query however long the list is.
                'responsible' => (string) ($r->responsible ?? ''),
                'subject'     => (string) ($r->subject_name
                    ?? $this->subject_name($r->applicant_id ?? null)),
                'on_self'     => isset($r->acted_on_self) ? (int) $r->acted_on_self : null,
            ];
        }

        return $entries;
    }

    /**
     * Track rows only store `res` (a username, or an email for applicants).
     * Resolve those to real names/roles in one query.
     */
    private function attach_actor_names(array &$entries)
    {
        $keys = [];
        foreach ($entries as $e) {
            if ($e['source'] === 'track' && $e['actor_key'] !== '') {
                $keys[$e['actor_key']] = true;
            }
        }
        if (empty($keys)) {
            return;
        }

        $users = $this->db
            ->select('username, fname, lname, position')
            ->where_in('username', array_keys($keys))
            ->get('users')
            ->result();

        $map = [];
        foreach ($users as $u) {
            $name = trim(trim((string) $u->lname) . ', ' . trim((string) $u->fname));
            $name = trim($name, ', ');
            $map[$u->username] = [
                'name' => $name !== '' ? $name . ' (' . $u->username . ')' : $u->username,
                'role' => (string) $u->position,
            ];
        }

        foreach ($entries as &$e) {
            if ($e['source'] !== 'track' || $e['actor_key'] === '') {
                continue;
            }
            if (isset($map[$e['actor_key']])) {
                $e['actor']      = $map[$e['actor_key']]['name'];
                $e['actor_role'] = $map[$e['actor_key']]['role'];
            } elseif (strpos($e['actor_key'], '@') !== false) {
                $e['actor_role'] = 'Applicant';
            }
        }
        unset($e);
    }

    /** Best-effort parse of the mixed date formats both tables use. */
    private function to_timestamp($value, $date_only_fallback = null)
    {
        $ts = !empty($value) ? strtotime((string) $value) : false;

        if ($ts === false && !empty($date_only_fallback)) {
            $ts = strtotime((string) $date_only_fallback);
        }

        return $ts === false ? 0 : $ts;
    }

    /** Icon / tone / wording for an audit-trail action slug. */
    private function classify_action($action)
    {
        $map = [
            'submit_application'   => ['Application Submitted', 'mdi-send', 'info'],
            'update_document'      => ['Document Updated', 'mdi-file-replace-outline', 'warning'],
            'delete_document'      => ['Document Removed', 'mdi-file-remove-outline', 'danger'],
            'upload_document'      => ['Document Uploaded', 'mdi-file-upload-outline', 'info'],
            'validate'             => ['Validated', 'mdi-check-decagram', 'success'],
            'qualify'              => ['Marked Qualified', 'mdi-account-check-outline', 'success'],
            'disqualify'           => ['Disqualified', 'mdi-close-octagon-outline', 'danger'],
            'revert_qualification' => ['Qualification Reverted', 'mdi-undo-variant', 'danger'],
            'endorse'              => ['Endorsed for Rating', 'mdi-share-outline', 'purple'],
            'status_change'        => ['Status Changed', 'mdi-swap-horizontal', 'warning'],
            'rate'                 => ['Rating Encoded', 'mdi-notebook-outline', 'primary'],
            'retention_request'    => ['Retention Requested', 'mdi-file-clock-outline', 'info'],
            'retention_grant'      => ['Retention Granted', 'mdi-file-restore', 'success'],
            'retention_deny'       => ['Retention Denied', 'mdi-close-circle-outline', 'danger'],
            'retention_release'    => ['Retained Scores Released', 'mdi-lock-open-variant-outline', 'warning'],
            'retention_delete'     => ['Retention Request Deleted', 'mdi-delete-outline', 'danger'],
            'add_training'         => ['Training Added', 'mdi-school-outline', 'info'],
            'update_training'      => ['Training Updated', 'mdi-pencil-outline', 'warning'],
            'delete_training'      => ['Training Removed', 'mdi-delete-outline', 'danger'],
            'add_experience'       => ['Work Experience Added', 'mdi-briefcase-outline', 'info'],
            'update_experience'    => ['Work Experience Updated', 'mdi-pencil-outline', 'warning'],
            'delete_experience'    => ['Work Experience Removed', 'mdi-delete-outline', 'danger'],
            'update_eligibility'   => ['Eligibility Updated', 'mdi-certificate-outline', 'warning'],
            'update_experience_details' => ['Work Experience Details Edited', 'mdi-briefcase-edit-outline', 'warning'],
            'update_training_details'   => ['Training Details Edited', 'mdi-pencil-box-outline', 'warning'],
            'register'             => ['Applicant Registered', 'mdi-account-plus-outline', 'info'],
            'update_profile'       => ['Profile Updated', 'mdi-account-edit-outline', 'warning'],
            'apply'                => ['Applied to Vacancy', 'mdi-send-check-outline', 'info'],
            'add_attachment'       => ['Attachment Added', 'mdi-paperclip', 'info'],
            'replace_attachment'   => ['Attachment Replaced', 'mdi-file-replace-outline', 'warning'],
            'remove_attachment'    => ['Attachment Removed', 'mdi-paperclip-off', 'danger'],
            'upload_failed'        => ['Attachment Upload Failed', 'mdi-alert-outline', 'danger'],
            'blocked'              => ['Blocked by Lock', 'mdi-lock-alert-outline', 'danger'],
            'login'                => ['Signed In', 'mdi-login-variant', 'secondary'],
            'login_failed'         => ['Failed Sign-In', 'mdi-account-alert-outline', 'danger'],
            'logout'               => ['Signed Out', 'mdi-logout-variant', 'secondary'],
            'request'              => ['Request', 'mdi-web', 'secondary'],
        ];

        $key = strtolower(trim((string) $action));
        if (isset($map[$key])) {
            return [
                'action' => $key,
                'label'  => $map[$key][0],
                'icon'   => $map[$key][1],
                'tone'   => $map[$key][2],
            ];
        }

        return [
            'action' => $key !== '' ? $key : 'activity',
            'label'  => ucwords(str_replace('_', ' ', $key !== '' ? $key : 'activity')),
            'icon'   => 'mdi-circle-medium',
            'tone'   => 'secondary',
        ];
    }

    /**
     * The track table stores free text in `appStatus` — workflow milestones for
     * system-written rows, and remarks/queries for user-written ones. Read the
     * text to give each row a sensible heading, icon and colour. `detail` says
     * whether the raw text is worth repeating under the heading.
     */
    private function classify_status($text)
    {
        $t = strtolower(trim($text));

        $t = rtrim($t, '.');

        // Exact matches only — the same column also holds applicant messages,
        // and those routinely contain words like "confirm" or "validate".
        $known = [
            'application submitted'                     => ['Application Submitted', 'mdi-send', 'info'],
            'edited application'                        => ['Application Edited', 'mdi-file-document-edit-outline', 'warning'],
            'validated'                                 => ['Validated', 'mdi-check-decagram', 'success'],
            'the submitted documents has been validated'=> ['Validated', 'mdi-check-decagram', 'success'],
            'cancelled validation'                      => ['Validation Cancelled', 'mdi-undo-variant', 'warning'],
            'endorse for rating'                        => ['Endorsed for Rating', 'mdi-share-outline', 'purple'],
            'endorsed for rating'                       => ['Endorsed for Rating', 'mdi-share-outline', 'purple'],
            'rated'                                     => ['Rated', 'mdi-star-outline', 'purple'],
            'confirmed'                                 => ['Confirmed by Applicant', 'mdi-thumb-up-outline', 'success'],
        ];

        if (isset($known[$t])) {
            return [
                'action' => 'status',
                'label'  => $known[$t][0],
                'icon'   => $known[$t][1],
                'tone'   => $known[$t][2],
                'detail' => false,
            ];
        }

        // "The education rating has been encoded", "Demo rating has been
        // encoded", … — keep the text so the reader sees which component.
        if ($t !== '' && substr($t, -strlen('rating has been encoded')) === 'rating has been encoded') {
            return [
                'action' => 'status',
                'label'  => 'Rating Encoded',
                'icon'   => 'mdi-notebook-outline',
                'tone'   => 'primary',
                'detail' => true,
            ];
        }

        // Anything left is free text the applicant or a staff member typed.
        return ['action' => 'remark', 'label' => 'Remarks / Comment', 'icon' => 'mdi-comment-text-outline', 'tone' => 'secondary', 'detail' => true];
    }

    /* ====================================================================
     * Accountability - who is answerable, and whose record it was
     * ================================================================== */

    /**
     * The accountable person, written out in full so a row reads on its own:
     * "Lastname, Firstname M. (username) - Position".
     *
     * The trail already snapshots the parts separately; this is the assembled
     * form, so reviewing a row - or exporting one into a report - needs no
     * join and no reconstruction. Snapshotted like the parts are, so it keeps
     * saying who was answerable even after the user record changes.
     */
    private function responsible_name($actor)
    {
        $username = $actor->username ?? ($this->session->username ?? '');
        $position = $actor->position ?? ($this->session->position ?? '');

        $last  = trim((string) ($actor->lname ?? ''));
        $first = trim((string) ($actor->fname ?? ''));
        $mid   = trim((string) ($actor->mname ?? ''));

        $name = trim($last . ', ' . $first, ' ,');

        if ($name !== '' && $mid !== '') {
            $name .= ' ' . mb_strtoupper(mb_substr($mid, 0, 1)) . '.';
        }

        if ($name === '') {
            $name = trim((string) $username);
        } elseif ($username !== '') {
            $name .= ' (' . $username . ')';
        }

        if ($name === '') {
            // No session at all: a public form, or a cron acting for nobody.
            return is_cli() ? 'System (scheduled task)' : 'Unauthenticated visitor';
        }

        return $this->clip($position !== '' ? $name . ' - ' . $position : $name, 255);
    }

    /**
     * The person whose record an action touched.
     *
     * `applicant_id` carries three different identifier shapes across this
     * project - an hris_applicant id, a record_no, and an hris_staff IDNumber -
     * so a bare number in the trail says nothing on its own. Resolve it once
     * and store the name beside it.
     */
    public function subject_name($subject_id)
    {
        $subject_id = trim((string) $subject_id);

        if ($subject_id === '') {
            return null;
        }

        if (array_key_exists($subject_id, $this->subject_cache)) {
            return $this->subject_cache[$subject_id];
        }

        $this->subject_cache[$subject_id] = null;

        $debug = $this->db->db_debug;
        $this->db->db_debug = false;

        try {
            $row = null;

            if (ctype_digit($subject_id)) {
                $row = $this->db->select('FirstName, MiddleName, LastName')
                    ->where('id', (int) $subject_id)->limit(1)
                    ->get('hris_applicant')->row();
            }

            if (!$row) {
                $row = $this->db->select('FirstName, MiddleName, LastName')
                    ->where('record_no', $subject_id)->limit(1)
                    ->get('hris_applicant')->row();
            }

            // Staff 201 records share the trail but live in their own id space.
            if (!$row) {
                $row = $this->db->select('FirstName, MiddleName, LastName')
                    ->where('IDNumber', $subject_id)->limit(1)
                    ->get('hris_staff')->row();
            }

            if ($row) {
                $name = trim(trim((string) $row->LastName) . ', ' . trim((string) $row->FirstName), ' ,');
                $mid  = trim((string) $row->MiddleName);

                if ($name !== '' && $mid !== '') {
                    $name .= ' ' . mb_strtoupper(mb_substr($mid, 0, 1)) . '.';
                }

                $this->subject_cache[$subject_id] = $this->clip($name, 255);
            }
        } catch (Exception $e) {
            log_message('error', 'Audit subject lookup failed: ' . $e->getMessage());
        }

        $this->db->db_debug = $debug;

        return $this->subject_cache[$subject_id];
    }

    /**
     * Has this request already been described by a purpose-written entry?
     * The catch-all net asks before writing its generic row.
     */
    public function has_specific_entry()
    {
        return $this->specific_logged;
    }

    /* ====================================================================
     * Schema
     * ================================================================== */

    /**
     * Widen `hris_audit_trail` for the forensic columns, once per request.
     *
     * Follows the project's ensure_table() rule: additive only. Nothing is
     * dropped, truncated or recreated, so running it on every page load is
     * safe and the columns stay permanently available afterwards.
     */
    public function ensure_schema()
    {
        if (self::$schema_ready) {
            return;
        }
        self::$schema_ready = true;

        $debug = $this->db->db_debug;
        $this->db->db_debug = false;

        try {
            // idx_subject is the last thing this method creates before the
            // triggers, so its presence means everything above it succeeded.
            // The usual case then costs one lightweight query, not nine.
            $ready = $this->db->query(
                "select 1 from information_schema.STATISTICS
                  where TABLE_SCHEMA = database()
                    and TABLE_NAME = 'hris_audit_trail'
                    and INDEX_NAME = 'idx_subject'
                  limit 1");

            if ($ready && $ready->num_rows() > 0) {
                $this->db->db_debug = $debug;
                return;
            }

            $this->Common->ensure_columns('hris_audit_trail', array(
                // Who / where from
                'ip_address'         => 'varchar(45) null',
                'forwarded_for'      => 'varchar(255) null',
                'mac_address'        => 'varchar(64) null',
                'device_id'          => 'char(36) null',
                'device_fingerprint' => 'varchar(64) null',
                'user_agent'         => 'varchar(255) null',
                'session_id'         => 'varchar(64) null',
                // What request it belonged to
                'request_id'         => 'char(32) null',
                'method'             => 'varchar(8) null',
                'uri'                => 'varchar(255) null',
                'referer'            => 'varchar(255) null',
                // What actually changed
                'entity_table'       => 'varchar(64) null',
                'old_value'          => 'text null',
                'new_value'          => 'text null',
                'meta'               => 'text null',
                // Accountability, denormalised so a row reads on its own
                'responsible'        => 'varchar(255) null',
                'subject_id'         => 'varchar(191) null',
                'subject_name'       => 'varchar(255) null',
                'acted_on_self'      => 'tinyint(1) null',
                // Tamper evidence
                'prev_hash'          => 'char(64) null',
                'row_hash'           => 'char(64) null',
                'hash_version'       => 'tinyint unsigned null',
            ));

            // Detailed trails outgrow varchar(500) quickly - a profile edit
            // naming every changed field is routinely longer than that.
            $col = $this->db->query(
                "select DATA_TYPE from information_schema.COLUMNS
                  where TABLE_SCHEMA = database()
                    and TABLE_NAME = 'hris_audit_trail'
                    and COLUMN_NAME = 'description'")->row();

            if ($col && strtolower($col->DATA_TYPE) === 'varchar') {
                $this->db->query("alter table `hris_audit_trail` modify `description` text null");
            }

            $this->ensure_index('hris_audit_trail', 'idx_device', '(`device_id`)');
            $this->ensure_index('hris_audit_trail', 'idx_request', '(`request_id`)');
            $this->ensure_index('hris_audit_trail', 'idx_ip', '(`ip_address`)');
            $this->ensure_index('hris_audit_trail', 'idx_entity', '(`entity_type`,`entity_id`)');
            $this->ensure_index('hris_audit_trail', 'idx_responsible', '(`responsible`)');
            $this->ensure_index('hris_audit_trail', 'idx_subject', '(`subject_id`)');

            $this->ensure_append_only();
        } catch (Exception $e) {
            log_message('error', 'Audit ensure_schema failed: ' . $e->getMessage());
        }

        $this->db->db_debug = $debug;
    }

    /**
     * Make the trail append-only in the engine itself.
     *
     * The hash chain proves a row was altered; these two triggers stop it
     * happening at all, including from a SQL client holding the application's
     * own credentials. Nothing in this project updates or deletes an audit
     * row, so they cost nothing in normal operation.
     *
     * Needs the TRIGGER privilege. Where the hosting account lacks it this
     * quietly does nothing and the hash chain remains the safeguard.
     */
    private function ensure_append_only()
    {
        $existing = $this->db->query(
            "select TRIGGER_NAME from information_schema.TRIGGERS
              where TRIGGER_SCHEMA = database()
                and EVENT_OBJECT_TABLE = 'hris_audit_trail'");

        $have = array();
        if ($existing) {
            foreach ($existing->result() as $row) {
                $have[$row->TRIGGER_NAME] = true;
            }
        }

        if (!isset($have['hris_audit_trail_no_update'])) {
            $this->db->query(
                "create trigger `hris_audit_trail_no_update`
                   before update on `hris_audit_trail`
                   for each row
                   signal sqlstate '45000'
                     set message_text = 'hris_audit_trail is append-only: rows cannot be modified.'");
        }

        if (!isset($have['hris_audit_trail_no_delete'])) {
            $this->db->query(
                "create trigger `hris_audit_trail_no_delete`
                   before delete on `hris_audit_trail`
                   for each row
                   signal sqlstate '45000'
                     set message_text = 'hris_audit_trail is append-only: rows cannot be deleted.'");
        }
    }

    /** Add an index only when it is missing. */
    private function ensure_index($table, $name, $definition)
    {
        $q = $this->db->query(
            "select 1 from information_schema.STATISTICS
              where TABLE_SCHEMA = database() and TABLE_NAME = ? and INDEX_NAME = ?
              limit 1",
            array($table, $name));

        if (!$q || $q->num_rows() < 1) {
            $this->db->query("alter table `" . $table . "` add index `" . $name . "` " . $definition);
        }
    }

    /* ====================================================================
     * Request context - the "who, from where, on what device" half
     * ================================================================== */

    /**
     * Everything about the current request that a trail entry should carry.
     * Resolved once and reused, so a request writing twenty rows costs one
     * ARP lookup and one cookie decision.
     */
    public function context()
    {
        if ($this->ctx !== null) {
            return $this->ctx;
        }

        $ip = $this->client_ip();

        $this->ctx = array(
            'request_id'         => $this->request_id(),
            'ip_address'         => $ip,
            'forwarded_for'      => $this->forwarded_chain(),
            'mac_address'        => $this->mac_address($ip),
            'device_id'          => $this->device_id() ?: null,
            'device_fingerprint' => $this->device_fingerprint(),
            'user_agent'         => $this->clip((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 255),
            'session_id'         => $this->clip((string) @session_id(), 64),
            'method'             => $this->clip((string) ($_SERVER['REQUEST_METHOD'] ?? 'CLI'), 8),
            'uri'                => $this->clip($this->current_uri(), 255),
            'referer'            => $this->clip((string) ($_SERVER['HTTP_REFERER'] ?? ''), 255),
        );

        return $this->ctx;
    }

    /** One id shared by every row written during this HTTP request. */
    private function request_id()
    {
        if ($this->request_id === null) {
            try {
                $this->request_id = bin2hex(random_bytes(16));
            } catch (Exception $e) {
                $this->request_id = md5(uniqid('', true));
            }
        }

        return $this->request_id;
    }

    /**
     * The caller's real address. Hostinger (and any CDN in front of it) puts
     * the browser behind a proxy, so REMOTE_ADDR alone would record the proxy
     * on every row. Prefer the proxy headers, but only when they hold a valid,
     * non-private address - they are client-supplied and trivially spoofed, so
     * the raw chain is kept separately in `forwarded_for` for comparison.
     */
    private function client_ip()
    {
        $candidates = array();

        if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            $candidates[] = $_SERVER['HTTP_CF_CONNECTING_IP'];
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            foreach (explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']) as $hop) {
                $candidates[] = $hop;
            }
        }
        if (!empty($_SERVER['HTTP_X_REAL_IP'])) {
            $candidates[] = $_SERVER['HTTP_X_REAL_IP'];
        }

        foreach ($candidates as $candidate) {
            $candidate = trim($candidate);
            if (filter_var($candidate, FILTER_VALIDATE_IP,
                    FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                return $candidate;
            }
        }

        $remote = trim((string) ($_SERVER['REMOTE_ADDR'] ?? ''));

        return $remote !== '' ? $this->clip($remote, 45) : null;
    }

    /** The raw proxy headers, kept verbatim so a spoofed hop stays visible. */
    private function forwarded_chain()
    {
        $parts = array();

        foreach (array('HTTP_X_FORWARDED_FOR', 'HTTP_CF_CONNECTING_IP', 'HTTP_X_REAL_IP') as $key) {
            if (!empty($_SERVER[$key])) {
                $parts[] = str_replace('HTTP_', '', $key) . '=' . $_SERVER[$key];
            }
        }

        if (!empty($_SERVER['REMOTE_ADDR'])) {
            $parts[] = 'REMOTE_ADDR=' . $_SERVER['REMOTE_ADDR'];
        }

        return $parts ? $this->clip(implode('; ', $parts), 255) : null;
    }

    /**
     * Hardware MAC of the calling device, when it is knowable.
     *
     * HTTP carries no MAC address - it is stripped at the first router hop -
     * so this can only ever succeed while the browser sits on the same LAN
     * segment as the server, by reading the kernel's ARP cache. That covers
     * an on-premise/XAMPP deployment; on shared hosting it correctly returns
     * NULL and `device_id` carries the device identity instead.
     *
     * Best effort throughout: a missing arp binary, a disabled exec() or an
     * unresolvable address must never slow down or break the audited action.
     */
    private function mac_address($ip)
    {
        $ip = trim((string) $ip);

        if ($ip === '' || filter_var($ip, FILTER_VALIDATE_IP) === false) {
            return null;
        }

        if (array_key_exists($ip, self::$mac_cache)) {
            return self::$mac_cache[$ip];
        }

        self::$mac_cache[$ip] = null;

        // Only a same-segment address can possibly be in the ARP cache.
        $isPrivate = filter_var($ip, FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false;

        if (!$isPrivate) {
            return null;
        }

        if (!function_exists('exec')) {
            return null;
        }

        $disabled = array_map('trim', explode(',', (string) ini_get('disable_functions')));
        if (in_array('exec', $disabled, true)) {
            return null;
        }

        try {
            $out  = array();
            $safe = escapeshellarg($ip);

            // arp(8) on both Linux and macOS; ip(8) where arp is absent.
            @exec('arp -n ' . $safe . ' 2>/dev/null', $out);
            if (empty($out)) {
                @exec('ip neigh show ' . $safe . ' 2>/dev/null', $out);
            }

            $text = implode(' ', (array) $out);

            if (preg_match('/([0-9a-f]{1,2}(?::[0-9a-f]{1,2}){5})/i', $text, $m)) {
                // macOS prints octets unpadded (a:b:c:...); normalise.
                $octets = array_map(static function ($o) {
                    return strtolower(str_pad($o, 2, '0', STR_PAD_LEFT));
                }, explode(':', $m[1]));

                self::$mac_cache[$ip] = implode(':', $octets);
            }
        } catch (Exception $e) {
            // Leave it NULL - never let a lookup break the audited action.
        }

        return self::$mac_cache[$ip];
    }

    /**
     * A stable per-device id, kept in a long-lived cookie.
     *
     * This is the identifier that actually works in production, where a MAC
     * address is unobtainable: it survives logout, follows the browser rather
     * than the account, and ties a run of actions to one physical device.
     *
     * The cookie can only be set before output starts, so the AuditRequest
     * hook primes it at post_controller_constructor.
     */
    public function device_id()
    {
        if ($this->device_id !== null) {
            return $this->device_id;
        }

        // No browser, no device: a CLI/cron actor must not mint a new id on
        // every run, which would otherwise fill the column with singletons.
        if (is_cli()) {
            return $this->device_id = false;
        }

        $existing = isset($_COOKIE[self::DEVICE_COOKIE])
            ? (string) $_COOKIE[self::DEVICE_COOKIE]
            : '';

        if (preg_match('/^[0-9a-f]{36}$/', $existing)) {
            return $this->device_id = $existing;
        }

        try {
            $this->device_id = substr(bin2hex(random_bytes(20)), 0, 36);
        } catch (Exception $e) {
            $this->device_id = substr(md5(uniqid('', true)) . md5(mt_rand()), 0, 36);
        }

        if (!headers_sent()) {
            $secure = (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off')
                || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');

            @setcookie(self::DEVICE_COOKIE, $this->device_id, array(
                'expires'  => time() + (10 * 365 * 24 * 3600),
                'path'     => '/',
                'secure'   => $secure,
                'httponly' => true,
                'samesite' => 'Lax',
            ));

            $_COOKIE[self::DEVICE_COOKIE] = $this->device_id;
        }

        return $this->device_id;
    }

    /**
     * Browser fingerprint hash posted by the device-print script.
     *
     * Re-links a device whose cookie was cleared: the cookie is the primary
     * identifier, this is the corroborating one.
     */
    private function device_fingerprint()
    {
        $fp = isset($_COOKIE[self::PRINT_COOKIE]) ? (string) $_COOKIE[self::PRINT_COOKIE] : '';

        return preg_match('/^[0-9a-f]{16,64}$/', $fp) ? $fp : null;
    }

    /** URI of the current request, safe to call before the router is up. */
    private function current_uri()
    {
        $uri = (string) ($_SERVER['REQUEST_URI'] ?? '');

        if ($uri === '' && isset($this->uri)) {
            $uri = (string) $this->uri->uri_string();
        }

        return $uri;
    }

    /** Trim to a column width without splitting a multibyte character. */
    private function clip($value, $length)
    {
        $value = (string) $value;

        if ($value === '') {
            return null;
        }

        return mb_substr($value, 0, $length);
    }

    /* ====================================================================
     * Before / after change tracking
     * ================================================================== */

    /**
     * Read a row as it stands right now, for comparison after the write.
     * Returns an associative array, or null when the row is gone.
     */
    public function snapshot($table, $pk_col, $pk_val)
    {
        if ($table === '' || $pk_col === '' || $pk_val === null || $pk_val === '') {
            return null;
        }

        $debug = $this->db->db_debug;
        $this->db->db_debug = false;

        $row = null;
        try {
            $row = $this->db->where($pk_col, $pk_val)->limit(1)->get($table)->row_array();
        } catch (Exception $e) {
            log_message('error', 'Audit snapshot failed: ' . $e->getMessage());
        }

        $this->db->db_debug = $debug;

        return $row ?: null;
    }

    /**
     * Fields whose value actually moved, as field => [old, new].
     *
     * Loose comparison on the string form, so a column re-saved with the same
     * value - the common case when a whole form is posted back - does not
     * manufacture a change.
     */
    public function diff($before, $after, array $ignore = array())
    {
        $before = (array) $before;
        $after  = (array) $after;

        $ignore = array_map('strtolower', array_merge($ignore, self::$never_diff));

        $changed = array();

        foreach ($after as $field => $new) {
            if (in_array(strtolower((string) $field), $ignore, true)) {
                continue;
            }

            // A field absent from the snapshot is a new column, not a change.
            if (!array_key_exists($field, $before)) {
                continue;
            }

            $old = $before[$field];

            if ((string) $old === (string) $new) {
                continue;
            }

            // '' and NULL mean the same thing to every form in this project.
            if (($old === null || $old === '') && ($new === null || $new === '')) {
                continue;
            }

            $changed[$field] = array($old, $new);
        }

        return $changed;
    }

    /**
     * Log a set of field changes as one entry.
     *
     * `old_value` / `new_value` carry the machine-readable JSON, `description`
     * the sentence a reviewer reads. Returns false without writing when
     * nothing actually changed, so a no-op save leaves no noise in the trail.
     *
     * @param string $action  action slug, e.g. update_profile
     * @param array  $changed field => [old, new], from diff()
     * @param array  $o       the usual log() overrides, plus `label` (what the
     *                        record is called) and `fields` (pretty names)
     */
    public function log_changes($action, array $changed, array $o = array())
    {
        if (empty($changed)) {
            return false;
        }

        $labels = isset($o['fields']) && is_array($o['fields']) ? $o['fields'] : array();
        $what   = isset($o['label']) ? trim((string) $o['label']) : '';

        $parts = array();
        foreach ($changed as $field => $pair) {
            $name = $labels[$field] ?? $this->humanise($field);
            $parts[] = $name . ': "' . $this->short((string) $pair[0]) . '" to "'
                . $this->short((string) $pair[1]) . '"';
        }

        $description = isset($o['description'])
            ? (string) $o['description']
            : 'Changed ' . count($changed) . ' field'
                . (count($changed) === 1 ? '' : 's')
                . ($what !== '' ? ' on ' . $what : '') . ' - ' . implode('; ', $parts) . '.';

        unset($o['fields'], $o['label']);

        $o['description'] = $description;
        $o['old_value']   = json_encode(array_map(static function ($p) { return $p[0]; }, $changed));
        $o['new_value']   = json_encode(array_map(static function ($p) { return $p[1]; }, $changed));

        if (!isset($o['field'])) {
            $o['field'] = $this->clip(implode(',', array_keys($changed)), 100);
        }

        return $this->log($action, $o);
    }

    /**
     * Snapshot, write, diff and log in one call - the shortest safe path for
     * an existing update. `$writer` performs the actual update and its return
     * value is passed back to the caller unchanged.
     */
    public function tracked_update($table, $pk_col, $pk_val, callable $writer, $action, array $o = array())
    {
        $before = $this->snapshot($table, $pk_col, $pk_val);
        $result = $writer();
        $after  = $this->snapshot($table, $pk_col, $pk_val);

        $o += array(
            'entity_table' => $table,
            'entity_id'    => $pk_val,
        );

        $this->log_changes($action, $this->diff($before, $after), $o);

        return $result;
    }

    /** field_name / FieldName -> "Field Name". */
    private function humanise($field)
    {
        $field = preg_replace('/([a-z])([A-Z])/', '$1 $2', (string) $field);
        $field = str_replace('_', ' ', $field);

        return ucfirst(trim(preg_replace('/\s+/', ' ', $field)));
    }

    /** Keep a single value short enough that a description stays readable. */
    private function short($value)
    {
        $value = trim(preg_replace('/\s+/', ' ', (string) $value));

        if ($value === '') {
            return '(empty)';
        }

        return mb_strlen($value) > 120 ? mb_substr($value, 0, 117) . '...' : $value;
    }

    /* ====================================================================
     * Tamper evidence
     * ================================================================== */

    /**
     * Hash-chain one row onto the one before it.
     *
     * Each row stores the previous row's hash and its own hash over
     * (payload + previous hash). Editing any stored field breaks that row's
     * own hash; deleting a row leaves the next row's `prev_hash` pointing at
     * nothing. Neither is repairable without recomputing the whole chain,
     * which is what makes the trail evidence rather than just a list.
     */
    private function seal(array $data)
    {
        $prev = $this->chain_tail;

        if ($prev === null) {
            $debug = $this->db->db_debug;
            $this->db->db_debug = false;

            try {
                $last = $this->db->select('row_hash')
                    ->order_by('id', 'desc')->limit(1)
                    ->get('hris_audit_trail')->row();
                $prev = $last->row_hash ?? null;
            } catch (Exception $e) {
                $prev = null;
            }

            $this->db->db_debug = $debug;
        }

        if (!$prev) {
            $prev = str_repeat('0', 64);
        }

        $data['hash_version'] = self::HASH_VERSION;
        $data['prev_hash']    = $prev;
        $data['row_hash']     = hash('sha256',
            $this->hash_payload($data, self::HASH_VERSION) . '|' . $prev);

        $this->chain_tail = $data['row_hash'];

        return $data;
    }

    /**
     * The exact string a row's hash is taken over.
     *
     * Versioned, because the sealed set grows as the trail records more:
     * rows written under an earlier version must keep verifying against the
     * field list they were actually sealed with, or widening the schema would
     * read as tampering. Version 1 predates the responsible / subject columns.
     */
    private function hash_payload($r, $version)
    {
        $r = (array) $r;

        $fields = array(
            'created_at', 'user_id', 'username', 'action', 'entity_type',
            'entity_id', 'app_id', 'applicant_id', 'job_id', 'field',
            'description', 'old_value', 'new_value',
            'ip_address', 'mac_address', 'device_id',
        );

        if ((int) $version >= 2) {
            $fields[] = 'responsible';
            $fields[] = 'subject_id';
            $fields[] = 'subject_name';
        }

        $parts = array();
        foreach ($fields as $field) {
            $parts[] = (string) ($r[$field] ?? '');
        }

        return implode('|', $parts);
    }

    /**
     * Re-walk the chain and report the rows that no longer verify.
     *
     * `altered` - the stored hash disagrees with the stored content.
     * `orphaned` - the row it was chained onto is no longer in the table.
     *
     * @param int $limit how many of the most recent rows to check
     */
    public function verify_chain($limit = 5000)
    {
        $rows = $this->db->select('id, created_at, user_id, username, action, entity_type, entity_id,'
                . ' app_id, applicant_id, job_id, field, description, old_value, new_value,'
                . ' ip_address, mac_address, device_id, responsible, subject_id, subject_name,'
                . ' hash_version, prev_hash, row_hash')
            ->where('row_hash is not null', null, false)
            ->order_by('id', 'desc')
            ->limit((int) $limit)
            ->get('hris_audit_trail')
            ->result_array();

        $known = array();
        foreach ($rows as $r) {
            $known[$r['row_hash']] = true;
        }

        $altered  = array();
        $orphaned = array();

        foreach ($rows as $r) {
            // Rows sealed before the column was added carry no version.
            $version = (int) ($r['hash_version'] ?? 1) ?: 1;
            $payload = $this->hash_payload($r, $version);

            if (hash('sha256', $payload . '|' . $r['prev_hash']) !== $r['row_hash']) {
                $altered[] = (int) $r['id'];
            }

            $genesis = ($r['prev_hash'] === str_repeat('0', 64));
            if (!$genesis && !isset($known[$r['prev_hash']])) {
                $orphaned[] = (int) $r['id'];
            }
        }

        // The oldest row in the window legitimately chains onto one outside it.
        if ($orphaned && $rows) {
            $oldest = (int) $rows[count($rows) - 1]['id'];
            $orphaned = array_values(array_filter($orphaned, static function ($id) use ($oldest) {
                return $id !== $oldest;
            }));
        }

        return array(
            'checked'  => count($rows),
            'altered'  => $altered,
            'orphaned' => $orphaned,
            'intact'   => empty($altered) && empty($orphaned),
        );
    }
}
