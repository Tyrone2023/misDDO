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
}
