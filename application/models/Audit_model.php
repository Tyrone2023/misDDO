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
    /** Per-request cache of resolved actor rows, keyed by user id. */
    private $actor_cache = [];

    /** Per-request cache of built application logs, keyed by app id. */
    private $log_cache = [];

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
        $actor = $this->resolve_actor($o['user_id'] ?? null);

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
            'app_id'       => isset($o['app_id']) && $o['app_id'] !== '' ? (int) $o['app_id'] : null,
            'applicant_id' => isset($o['applicant_id']) ? (string) $o['applicant_id'] : null,
            'job_id'       => isset($o['job_id']) && $o['job_id'] !== '' ? (int) $o['job_id'] : null,
            'field'        => $o['field'] ?? null,
            'description'  => isset($o['description']) ? mb_substr((string) $o['description'], 0, 500) : null,
        ];

        // Auditing must never break the action it is recording.
        try {
            return $this->db->insert('hris_audit_trail', $data);
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
}
