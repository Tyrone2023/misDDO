<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Personnel account maintenance for the HR "Manage Users" page (hrusers).
 *
 * The username is not only a login: many tables record *who did something*
 * by copying the actor's username as text (track `res` columns, legacy `at`
 * and `mis_logs` logs, vacancy postings, leave approvals...), and applicant
 * accounts are tied to their records by `empEmail` = username. Renaming the
 * account therefore has to rewrite those columns in the same transaction, or
 * the person's history stops following them.
 *
 * `hris_audit_trail` is deliberately NOT rewritten: it is append-only (DB
 * triggers) and hash-chained, and each row already carries `users.id` in
 * `user_id`, which does not change. The rename itself is written there as a
 * new row (old -> new) so the old name stays resolvable.
 *
 * Columns that store `users.id` (rating eval_id*, etc.) need nothing.
 */
class User_account_model extends CI_Model
{
    /**
     * Every column that stores a login username as text.
     *
     *  - label:  shown on the edit page / result message.
     *  - mixed:  the column also holds numeric ids (users.id, applicant id,
     *            school id). A purely numeric username is then ambiguous, so
     *            the column is skipped for such a rename and reported.
     *
     * Add new username-bearing columns here; missing tables/columns are
     * skipped, so the list may name tables that only exist on some installs.
     */
    const USERNAME_COLUMNS = array(
        // Account cross-references
        array('table' => 'users',                        'column' => 'user_id',               'label' => 'Linked accounts (users.user_id)', 'mixed' => true),

        // Applicant records (applicant username = e-mail)
        array('table' => 'hris_applicant',               'column' => 'empEmail',              'label' => 'Applicant profile'),
        array('table' => 'hris_applications',            'column' => 'empEmail',              'label' => 'Applications'),
        array('table' => 'hris_applications_track',      'column' => 'empEmail',              'label' => 'Application tracking (applicant)'),

        // Activity / tracking logs
        array('table' => 'hris_applications_track',      'column' => 'res',                   'label' => 'Application tracking (acted by)', 'mixed' => true),
        array('table' => 'hris_application_inquiry',     'column' => 'res',                   'label' => 'Application inquiries'),
        array('table' => 'at',                           'column' => 'res_user',              'label' => 'Activity log (at)'),
        array('table' => 'mis_logs',                     'column' => 'username',              'label' => 'Login history (mis_logs)'),
        array('table' => 'system_feedback',              'column' => 'system_user',           'label' => 'System feedback'),
        array('table' => 'sgod_aip_track',               'column' => 'res',                   'label' => 'AIP tracking', 'mixed' => true),
        array('table' => 'sgod_aip_submit',              'column' => 'res',                   'label' => 'AIP submissions', 'mixed' => true),
        array('table' => 'sgod_aip_request',             'column' => 'deny_by',               'label' => 'AIP requests (denied by)'),
        array('table' => 'sgod_memo',                    'column' => 'added_by',              'label' => 'SGOD memos'),

        // Authorship
        array('table' => 'hris_staff',                   'column' => 'updatedBy',              'label' => 'Personnel records (updated by)'),
        array('table' => 'hris_exams',                   'column' => 'created_by_username',    'label' => 'Exams (created by)'),
        array('table' => 'hris_jobvacancy',              'column' => 'postedBy',              'label' => 'Job vacancies (posted by)'),
        array('table' => 'hris_jobvacancy',              'column' => 'announcement_by',       'label' => 'Job vacancies (announced by)'),
        array('table' => 'hris_rqa_posts',               'column' => 'posted_by',             'label' => 'RQA postings'),
        array('table' => 'hris_position_criteria',       'column' => 'updated_by',            'label' => 'Position criteria'),
        array('table' => 'epmloyee_superior',            'column' => 'approver',              'label' => 'Employee approver'),

        // Leave module
        array('table' => 'leave_applications',           'column' => 'certified_by_username', 'label' => 'Leave (certified by)'),
        array('table' => 'leave_applications',           'column' => 'rejected_by_username',  'label' => 'Leave (rejected by)'),
        array('table' => 'leave_applications',           'column' => 'deducted_by',           'label' => 'Leave (deducted by)'),
        array('table' => 'leave_applications',           'column' => 'restored_by',           'label' => 'Leave (restored by)'),
        array('table' => 'leave_entitlements',           'column' => 'approved_by',           'label' => 'Leave entitlements (approved by)'),
        array('table' => 'leave_entitlements',           'column' => 'action_by',             'label' => 'Leave entitlements (action by)'),
        array('table' => 'leave_entitlements',           'column' => 'created_by',            'label' => 'Leave entitlements (created by)'),
        array('table' => 'leave_balances',               'column' => 'updated_by',            'label' => 'Leave balances'),
        array('table' => 'leave_balance_upload_batches', 'column' => 'uploaded_by',           'label' => 'Leave balance uploads'),
        array('table' => 'leave_admin_audit',            'column' => 'performed_by',          'label' => 'Leave admin audit'),
        array('table' => 'leave_monthly_credit_logs',    'column' => 'processed_by',          'label' => 'Leave monthly credits'),
        array('table' => 'leave_credit_ledger',          'column' => 'performed_by',          'label' => 'Leave credit ledger'),
    );

    /** Accounts the page never lists or edits: they are not personnel. */
    const NON_PERSONNEL = array('reg', 'School', 'School Account', 'user');

    /** Positions only an Admin / Super Admin may hold, grant or edit. */
    const PRIVILEGED = array('Admin', 'Super Admin');

    /**
     * Usernames the code compares against literally. Renaming one of these
     * accounts silently drops the special access tied to the name.
     */
    const CODE_REFERENCED = array('aov', 'aoiv', 'asdsv2', 'Cyanne19', 'Cyanne25');

    private $present = null;

    public function personnel($include_privileged)
    {
        $this->db->select('id, username, position, fname, mname, lname, user_id')
            ->from('users')
            ->where_not_in('position', self::NON_PERSONNEL);

        if (!$include_privileged) {
            $this->db->where_not_in('position', self::PRIVILEGED);
        }

        return $this->db->order_by('position', 'asc')->order_by('lname', 'asc')->get()->result();
    }

    public function applicants()
    {
        return $this->db->select('id, username, position, fname, mname, lname, user_id')
            ->from('users')
            ->where_in('position', array('reg', 'user'))
            ->order_by('id', 'desc')
            ->get()->result();
    }

    public function get($id)
    {
        return $this->db->get_where('users', array('id' => (int) $id))->row();
    }

    /** Positions that may be assigned: the personnel positions in use, plus the account's own. */
    public function assignable_positions($include_privileged, $current = null)
    {
        $rows = $this->db->distinct()->select('position')->from('users')
            ->where_not_in('position', self::NON_PERSONNEL)
            ->where('position <>', '')
            ->order_by('position', 'asc')
            ->get()->result();

        $out = array();
        foreach ($rows as $r) {
            if (!$include_privileged && in_array($r->position, self::PRIVILEGED, true)) {
                continue;
            }
            $out[] = $r->position;
        }

        if ($current !== null && $current !== '' && !in_array($current, $out, true)) {
            $out[] = $current;
        }

        return $out;
    }

    public function username_taken($username, $except_id)
    {
        // users.username is *_ci: this also catches case-only clashes, which
        // would make login (it requires exactly one match) fail for both.
        return $this->db->where('username', $username)
            ->where('id <>', (int) $except_id)
            ->count_all_results('users') > 0;
    }

    /** Registry entries whose table and column exist on this database. */
    public function columns()
    {
        if ($this->present === null) {
            $this->present = array();
            foreach (self::USERNAME_COLUMNS as $c) {
                if ($this->db->table_exists($c['table']) && $this->db->field_exists($c['column'], $c['table'])) {
                    $this->present[] = $c + array('mixed' => false);
                }
            }
        }

        return $this->present;
    }

    private $ambiguous = array();

    /**
     * "Mixed" columns also hold numeric ids (users.id, applicant id, school
     * id). A numeric username that equals one of those ids cannot be told
     * apart from it there, so those columns are left alone for it. Numeric
     * usernames that collide with nothing (e.g. 7-digit ID numbers) are safe.
     */
    public function skip_mixed($username)
    {
        $username = (string) $username;
        if (!ctype_digit($username)) {
            return false;
        }
        if (isset($this->ambiguous[$username])) {
            return $this->ambiguous[$username];
        }

        $hit = $this->db->where('id', (int) $username)->count_all_results('users') > 0
            || ($this->db->table_exists('hris_applicant')
                && $this->db->where('id', (int) $username)->count_all_results('hris_applicant') > 0)
            || ($this->db->table_exists('schools')
                && $this->db->where('schoolID', $username)->count_all_results('schools') > 0);

        return $this->ambiguous[$username] = $hit;
    }

    /**
     * Rows that reference $username, per registry column.
     * $exclude_user_id keeps the account's own users.user_id out of the count.
     */
    public function linked_counts($username, $exclude_user_id = null)
    {
        $out = array();

        foreach ($this->columns() as $c) {
            if ($c['mixed'] && $this->skip_mixed($username)) {
                $out[] = $c + array('rows' => null);
                continue;
            }

            $this->db->from($c['table'])->where($c['column'], $username);
            if ($c['table'] === 'users' && $exclude_user_id) {
                $this->db->where('id <>', (int) $exclude_user_id);
            }

            $out[] = $c + array('rows' => (int) $this->db->count_all_results());
        }

        return $out;
    }

    /** Previous usernames, read back from the rename entries in the audit trail. */
    public function username_history($id)
    {
        if (!$this->db->table_exists('hris_audit_trail')) {
            return array();
        }

        return $this->db->select('created_at, old_value, new_value, responsible, username')
            ->from('hris_audit_trail')
            ->where('action', 'rename_username')
            ->where('entity_type', 'user_account')
            ->where('entity_id', (string) (int) $id)
            ->order_by('id', 'desc')
            ->get()->result();
    }

    /**
     * Apply an edit. $changes may hold: username, position, fname, mname,
     * lname, password (plain). Returns array('ok' => bool, 'error' => string,
     * 'moved' => [label => rows], 'skipped' => [labels]).
     */
    public function save($id, array $changes)
    {
        $id = (int) $id;

        $this->db->trans_begin();

        // Lock the row: two admins renaming at once must not interleave.
        $user = $this->db->query('SELECT * FROM users WHERE id = ? FOR UPDATE', array($id))->row();
        if (!$user) {
            $this->db->trans_rollback();
            return array('ok' => false, 'error' => 'Account not found.');
        }

        $old   = (string) $user->username;
        $new   = isset($changes['username']) ? (string) $changes['username'] : $old;
        $moved = array();
        $skipped = array();

        $data = array();
        foreach (array('position', 'fname', 'mname', 'lname') as $f) {
            if (isset($changes[$f]) && (string) $changes[$f] !== (string) $user->$f) {
                $data[$f] = (string) $changes[$f];
            }
        }
        if (isset($changes['password']) && $changes['password'] !== '') {
            $data['password'] = password_hash($changes['password'], PASSWORD_DEFAULT);
        }

        $renaming = ($new !== $old);

        if ($renaming) {
            if ($this->username_taken($new, $id)) {
                $this->db->trans_rollback();
                return array('ok' => false, 'error' => 'The username "' . $new . '" is already used by another account.');
            }

            $data['username'] = $new;

            // The account's own user_id usually mirrors its username; on its
            // own row that is unambiguous even when the username is numeric.
            if ((string) $user->user_id === $old) {
                $data['user_id'] = $new;
            }

            foreach ($this->columns() as $c) {
                if ($c['mixed'] && $this->skip_mixed($old)) {
                    $skipped[] = $c['label'];
                    continue;
                }

                $this->db->where($c['column'], $old);
                if ($c['table'] === 'users') {
                    // The account's own row is handled through $data below.
                    $this->db->where('id <>', $id);
                }
                $this->db->update($c['table'], array($c['column'] => $new));
                $n = $this->db->affected_rows();

                if ($this->db->error()['code']) {
                    $err = $this->db->error()['message'];
                    $this->db->trans_rollback();
                    return array('ok' => false, 'error' => 'Could not update ' . $c['table'] . '.' . $c['column'] . ': ' . $err);
                }

                if ($n > 0) {
                    $moved[$c['label']] = (isset($moved[$c['label']]) ? $moved[$c['label']] : 0) + $n;
                }
            }
        }

        if (!empty($data)) {
            $this->db->where('id', $id)->update('users', $data);
            if ($this->db->error()['code']) {
                $err = $this->db->error()['message'];
                $this->db->trans_rollback();
                return array('ok' => false, 'error' => 'Could not update the account: ' . $err);
            }
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return array('ok' => false, 'error' => 'The change was rolled back because a database error occurred.');
        }

        $this->db->trans_commit();

        // Renaming yourself: the session must carry the new name before
        // anything else is logged, or new rows land under the old one.
        if ((int) $this->session->userdata('id') === $id) {
            $fresh = $this->get($id);
            $this->session->set_userdata(array(
                'username' => $fresh->username,
                'user_id'  => $fresh->user_id,
                'c_id'     => $fresh->user_id,
                'user'     => $fresh->fname . ' ' . $fresh->mname . ' ' . $fresh->lname,
            ));
        }

        // Logged after commit: Audit->log() may run DDL (ensure_schema),
        // which would implicitly commit a still-open transaction.
        $this->log($user, $data, $old, $new, $moved, $skipped);

        return array('ok' => true, 'error' => '', 'moved' => $moved, 'skipped' => $skipped,
                     'renamed' => $renaming, 'old' => $old, 'new' => $new, 'changed' => array_keys($data));
    }

    private function log($user, array $data, $old, $new, array $moved, array $skipped)
    {
        $name = trim($user->fname . ' ' . $user->lname);
        $base = array(
            'entity_type'  => 'user_account',
            'entity_table' => 'users',
            'entity_id'    => (int) $user->id,
            'subject_id'   => (string) $user->id,
            'subject_name' => $name !== '' ? $name : $old,
        );

        if ($old !== $new) {
            $this->Audit->log('rename_username', $base + array(
                'field'       => 'username',
                'old_value'   => $old,
                'new_value'   => $new,
                'description' => 'Renamed account #' . $user->id . ' from "' . $old . '" to "' . $new . '"; '
                    . array_sum($moved) . ' linked record(s) moved to the new username.',
                'meta'        => array('moved' => $moved, 'skipped' => $skipped),
            ));
        }

        if (isset($data['position'])) {
            $this->Audit->log('change_position', $base + array(
                'field'       => 'position',
                'old_value'   => $user->position,
                'new_value'   => $data['position'],
                'description' => 'Changed position of "' . $new . '" from "' . $user->position . '" to "' . $data['position'] . '".',
            ));
        }

        if (isset($data['password'])) {
            // Never the hash or the password itself.
            $this->Audit->log('reset_password', $base + array(
                'field'       => 'password',
                'description' => 'Reset the password of "' . $new . '".',
            ));
        }

        $names = array_intersect(array_keys($data), array('fname', 'mname', 'lname'));
        if (!empty($names)) {
            $before = array();
            $after  = array();
            foreach ($names as $f) {
                $before[$f] = $user->$f;
                $after[$f]  = $data[$f];
            }
            $this->Audit->log('update_account_name', $base + array(
                'field'       => 'name',
                'old_value'   => json_encode($before),
                'new_value'   => json_encode($after),
                'description' => 'Updated the name on account "' . $new . '".',
            ));
        }

        // Legacy activity log, kept for the existing reports that read it.
        $this->Page_model->insert_at('Updated user account', (int) $user->id);
    }
}
