<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Leave_admin extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library(array('session'));
        $this->load->helper(array('url', 'form'));
        $this->load->model('Leave_model');
        $this->load->model('Leave_signatory_model');
        $this->load->model('Leave_dtr_model');
    }

    public function index()
    {
        redirect('leave_admin/my_queue');
    }

    public function my_queue()
    {
        $this->guard();

        $username = strtolower(trim((string)$this->session->userdata('username')));
        $position = strtolower(trim((string)$this->session->userdata('position')));

        $q = trim((string)$this->input->get('q', true));

        $page = (int)$this->input->get('page');
        $page = ($page > 0) ? $page : 1;

        $limit  = 10;
        $offset = ($page - 1) * $limit;

        $apply_filters = function () use ($username, $position, $q) {
            $this->db->from('leave_applications la');
            $this->db->join('hris_staff hs', 'hs.IDNumber = la.staff_idnumber', 'left');

            if ($username === 'aov' || $position === 'admin officer v') {

                $this->db
                    ->where('la.status', 'PENDING')
                    ->where("LOWER(TRIM(la.certify_by_position)) = 'admin officer v'", null, false);

            } elseif ($username === 'aoiv' || $position === 'human resource admin' || $position === 'admin officer iv') {

                $this->db
                    ->where_in('la.status', array('DRAFT', 'PENDING'))
                    ->where("LOWER(TRIM(la.certify_by_position)) != 'admin officer v'", null, false);

            } elseif ($position === 'school' || is_numeric($username)) {

                $this->db
                    ->where('la.status', 'CERTIFIED')
                    ->where("LOWER(TRIM(la.recommend_by_position)) = 'school head'", null, false)
                    ->where('hs.schoolID', $username);

            } elseif ($position === 'cid') {

                $this->db
                    ->where('la.status', 'CERTIFIED')
                    ->where("LOWER(TRIM(la.recommend_by_position)) = 'cid chief'", null, false);

            } elseif ($position === 'sgod') {

                $this->db
                    ->where('la.status', 'CERTIFIED')
                    ->where("LOWER(TRIM(la.recommend_by_position)) = 'sgod chief'", null, false);

            } elseif ($position === 'asds') {

                $this->db
                    ->where('la.status', 'RECOMMENDED')
                    ->where("LOWER(TRIM(la.approve_by_position)) = 'asds'", null, false);

            } elseif ($position === 'sds') {

                $this->db
                    ->where('la.status', 'RECOMMENDED')
                    ->where("LOWER(TRIM(la.approve_by_position)) = 'sds'", null, false);

            } else {
                $this->db->where('1 = 0', null, false);
            }

            if ($q !== '') {
                $this->db->group_start();
                $this->db->like('la.staff_idnumber', $q);
                $this->db->or_like('la.leave_type_code', $q);
                $this->db->or_like('la.status', $q);
                $this->db->or_like('hs.FirstName', $q);
                $this->db->or_like('hs.MiddleName', $q);
                $this->db->or_like('hs.LastName', $q);
                $this->db->or_like('hs.Department', $q);
                $this->db->or_like('hs.empPosition', $q);
                $this->db->group_end();
            }
        };

        $apply_filters();
        $total_rows = $this->db->count_all_results();

        $apply_filters();

        $this->db->select("
            la.*,
            hs.FirstName,
            hs.MiddleName,
            hs.LastName,
            hs.Department,
            hs.empPosition,
            hs.schoolID
        ");

        $this->db->order_by('la.created_at', 'DESC');
        $this->db->limit($limit, $offset);

        $applications = $this->db->get()->result();

        $this->load->library('pagination');

        if ($this->input->get('page') === null) {
            $_GET['page'] = '1';
        }

        $config['base_url'] = base_url('leave_admin/my_queue');
        $config['total_rows'] = $total_rows;
        $config['per_page'] = $limit;
        $config['page_query_string'] = true;
        $config['query_string_segment'] = 'page';
        $config['reuse_query_string'] = true;
        $config['use_page_numbers'] = true;

        $config['full_tag_open'] = '<ul class="pagination pagination-rounded justify-content-end">';
        $config['full_tag_close'] = '</ul>';
        $config['attributes'] = array('class' => 'page-link');

        $config['first_link'] = 'First';
        $config['last_link'] = 'Last';
        $config['next_link'] = '&raquo;';
        $config['prev_link'] = '&laquo;';

        $config['first_tag_open'] = '<li class="page-item">';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li class="page-item">';
        $config['last_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li class="page-item">';
        $config['next_tag_close'] = '</li>';
        $config['prev_tag_open'] = '<li class="page-item">';
        $config['prev_tag_close'] = '</li>';
        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="page-item active"><span class="page-link">';
        $config['cur_tag_close'] = '</span></li>';

        $this->pagination->initialize($config);

        $data = array();
        $data['applications'] = $applications;
        $data['pagination']   = $this->pagination->create_links();
        $data['q']            = $q;
        $data['total_rows']   = $total_rows;
        $data['limit']        = $limit;
        $data['offset']       = $offset;

        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('leave/admin_queue', $data);
        $this->load->view('templates/footer');
    }

    public function certify($id)
    {
        if (!$this->session->userdata('username')) {
            redirect(base_url('login'));
            return;
        }

        $logged_position = strtolower(trim((string)$this->session->userdata('position')));
        $user_id         = (int)$this->session->userdata('user_id');

        if (!in_array($logged_position, array('human resource admin', 'admin officer iv'))) {
            show_error('Unauthorized access.', 403);
            return;
        }

        $application = $this->db->where('id', (int)$id)->get('leave_applications')->row();

        if (!$application) {
            $this->session->set_flashdata('error', 'Leave application not found.');
            redirect(base_url('leave_admin/my_queue'));
            return;
        }

        if (!in_array($application->status, array('DRAFT', 'PENDING'))) {
            $this->session->set_flashdata('error', 'Only draft or pending applications can be certified.');
            redirect(base_url('leave_admin/my_queue'));
            return;
        }

        $now = date('Y-m-d H:i:s');

        $this->db->trans_start();

        $this->db->where('id', (int)$id)->update('leave_applications', array(
            'status'       => 'CERTIFIED',
            'certified_by' => $user_id ?: null,
            'certified_at' => $now,
            'updated_at'   => $now
        ));

        $this->db->where('leave_application_id', (int)$id)
                ->where('signatory_role', 'HR_CERTIFICATION')
                ->update('leave_application_signatories', array(
                    'action_status' => 'CERTIFIED',
                    'signed_at'     => $now
                ));

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            $this->session->set_flashdata('error', 'Failed to certify leave application.');
        } else {
            $this->session->set_flashdata('success', 'Leave application certified successfully.');
        }

        redirect(base_url('leave_admin/my_queue'));
    }

    public function recommend($id)
    {
        if (!$this->session->userdata('username')) {
            redirect(base_url('login'));
            return;
        }

        $logged_position = strtolower(trim((string)$this->session->userdata('position')));
        $user_id         = (int)$this->session->userdata('user_id');

        if ($logged_position !== 'admin officer v') {
            show_error('Unauthorized access.', 403);
            return;
        }

        $application = $this->db->where('id', (int)$id)->get('leave_applications')->row();

        if (!$application) {
            $this->session->set_flashdata('error', 'Leave application not found.');
            redirect(base_url('leave_admin/my_queue'));
            return;
        }

        if ($application->status !== 'CERTIFIED') {
            $this->session->set_flashdata('error', 'Only certified applications can be recommended.');
            redirect(base_url('leave_admin/my_queue'));
            return;
        }

        $now = date('Y-m-d H:i:s');

        $this->db->trans_start();

        $this->db->where('id', (int)$id)->update('leave_applications', array(
            'status'         => 'RECOMMENDED',
            'recommended_by' => $user_id ?: null,
            'recommended_at' => $now,
            'updated_at'     => $now
        ));

        $this->db->where('leave_application_id', (int)$id)
                ->where('signatory_role', 'RECOMMENDING_AUTHORITY')
                ->update('leave_application_signatories', array(
                    'action_status' => 'RECOMMENDED',
                    'signed_at'     => $now
                ));

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            $this->session->set_flashdata('error', 'Failed to recommend leave application.');
        } else {
            $this->session->set_flashdata('success', 'Leave application recommended successfully.');
        }

        redirect(base_url('leave_admin/my_queue'));
    }

    // public function approve($id)
    // {
    //     if (!$this->session->userdata('username')) {
    //         redirect(base_url('login'));
    //         return;
    //     }

    //     $logged_position = strtolower(trim((string)$this->session->userdata('position')));
    //     $user_id         = (int)$this->session->userdata('user_id');

    //     if (!in_array($logged_position, array('asds', 'sds', 'cid', 'sgod'))) {
    //         show_error('Unauthorized access.', 403);
    //         return;
    //     }

    //     $application = $this->db->where('id', (int)$id)->get('leave_applications')->row();

    //     if (!$application) {
    //         $this->session->set_flashdata('error', 'Leave application not found.');
    //         redirect(base_url('leave_admin/my_queue'));
    //         return;
    //     }

    //     if ($application->status !== 'RECOMMENDED') {
    //         $this->session->set_flashdata('error', 'Only recommended applications can be approved.');
    //         redirect(base_url('leave_admin/my_queue'));
    //         return;
    //     }

    //     $now = date('Y-m-d H:i:s');

    //     $this->db->trans_start();

    //     $leave = $this->db
    //         ->where('id', $id)
    //         ->get('leave_applications')
    //         ->row();

    //     if (!$leave) {
    //         $this->session->set_flashdata('error', 'Leave application not found.');
    //         redirect('leave_admin/my_queue');
    //         return;
    //     }

    //     $deduct = $this->deduct_entitlement_credit($leave);

    //     if ($deduct !== true) {
    //         $this->session->set_flashdata('error', $deduct);
    //         redirect('leave_admin/my_queue');
    //         return;
    //     }


    //     $this->db->where('id', (int)$id)->update('leave_applications', array(
    //         'status'      => 'APPROVED',
    //         'approved_by' => $user_id ?: null,
    //         'approved_at' => $now,
    //         'updated_at'  => $now
    //     ));

    //     $this->db->where('leave_application_id', (int)$id)
    //             ->where('signatory_role', 'APPROVING_AUTHORITY')
    //             ->update('leave_application_signatories', array(
    //                 'action_status' => 'APPROVED',
    //                 'signed_at'     => $now
    //             ));

    //     $this->db->trans_complete();

    //     if ($this->db->trans_status() === false) {
    //         $this->session->set_flashdata('error', 'Failed to approve leave application.');
    //     } else {
    //         $this->session->set_flashdata('success', 'Leave application approved successfully.');
    //     }

    //     redirect(base_url('leave_admin/my_queue'));
    // }
    public function approve($id)
    {
        $this->guard();

        $id   = (int)$id;
        $now  = date('Y-m-d H:i:s');
        $user = $this->session->userdata('username');

        $this->db->trans_begin();

        // 1. Lock leave application row
        $leave = $this->db
            ->query("SELECT * FROM leave_applications WHERE id = ? FOR UPDATE", array($id))
            ->row();

        if (!$leave) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('error', 'Leave application not found.');
            redirect('leave_admin/my_queue');
            return;
        }

        // 2. Prevent duplicate / invalid approval
        if ($leave->status === 'APPROVED') {
            $this->db->trans_rollback();
            $this->session->set_flashdata('error', 'This leave application is already approved.');
            redirect('leave_admin/my_queue');
            return;
        }

        if ($leave->status !== 'RECOMMENDED') {
            $this->db->trans_rollback();
            $this->session->set_flashdata('error', 'This leave is not ready for approval.');
            redirect('leave_admin/my_queue');
            return;
        }

        // 3. Deduct entitlement safely if entitlement-based leave
        $deduct = $this->deduct_entitlement_credit_locked($leave);

        if ($deduct !== true) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('error', $deduct);
            redirect('leave_admin/my_queue');
            return;
        }

        // 4. Approve leave
        $this->db->where('id', $id)->update('leave_applications', array(
            'status'      => 'APPROVED',
            'approved_by' => $user,
            'approved_at' => $now,
            'updated_at'  => $now
        ));

        // 5. Audit
        $this->db->insert('leave_admin_audit', array(
            'action'         => 'APPROVE LEAVE',
            'reference_id'   => $id,
            'staff_idnumber' => $leave->staff_idnumber,
            'performed_by'   => $user,
            'ip_address'     => $this->input->ip_address(),
            'hostname'       => gethostbyaddr($this->input->ip_address()),
            'user_agent'     => $this->input->user_agent(),
            'created_at'     => $now
        ));

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('error', 'Approval failed. No changes were saved.');
            redirect('leave_admin/my_queue');
            return;
        }

        $this->db->trans_commit();

        $this->session->set_flashdata('success', 'Leave approved successfully.');
        redirect('leave_admin/my_queue');
    }

    public function reject($id)
    {
        if (!$this->session->userdata('username')) {
            redirect(base_url('login'));
            return;
        }

        $logged_position = strtolower(trim((string)$this->session->userdata('position')));
        $user_id         = (int)$this->session->userdata('user_id');
        $reason          = trim((string)$this->input->post('rejection_reason'));

        if (empty($reason)) {
            $this->session->set_flashdata('error', 'Rejection reason is required.');
            redirect(base_url('leave_admin/my_queue'));
            return;
        }

        if (!in_array($logged_position, array(
            'human resource admin',
            'admin officer iv',
            'admin officer v',
            'asds',
            'sds',
            'cid',
            'sgod'
        ))) {
            show_error('Unauthorized access.', 403);
            return;
        }

        $application = $this->db->where('id', (int)$id)->get('leave_applications')->row();

        if (!$application) {
            $this->session->set_flashdata('error', 'Leave application not found.');
            redirect(base_url('leave_admin/my_queue'));
            return;
        }

        $now = date('Y-m-d H:i:s');

        $this->db->trans_start();

        $this->db->where('id', (int)$id)->update('leave_applications', array(
            'status'           => 'REJECTED',
            'rejection_reason' => $reason,
            'rejected_by'      => $user_id ?: null,
            'rejected_at'      => $now,
            'updated_at'       => $now
        ));

        $signatory_role = null;

        if (in_array($logged_position, array('human resource admin', 'admin officer iv'))) {
            $signatory_role = 'HR_CERTIFICATION';
        } elseif ($logged_position === 'admin officer v') {
            $signatory_role = 'RECOMMENDING_AUTHORITY';
        } elseif (in_array($logged_position, array('asds', 'sds', 'cid', 'sgod'))) {
            $signatory_role = 'APPROVING_AUTHORITY';
        }

        if ($signatory_role) {
            $this->db->where('leave_application_id', (int)$id)
                    ->where('signatory_role', $signatory_role)
                    ->update('leave_application_signatories', array(
                        'action_status' => 'REJECTED',
                        'signed_at'     => $now
                    ));
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            $this->session->set_flashdata('error', 'Failed to reject leave application.');
        } else {
            $this->session->set_flashdata('success', 'Leave application rejected.');
        }

        redirect(base_url('leave_admin/my_queue'));
    }

    public function recall($id)
    {
        if (!$this->session->userdata('username')) {
            redirect(base_url('login'));
            return;
        }

        $logged_position = trim((string)$this->session->userdata('position'));

        // ✅ Only SDS can recall
        if ($logged_position !== 'sds') {
            show_error('Unauthorized access.', 403);
            return;
        }

        $application = $this->db
            ->where('id', (int)$id)
            ->get('leave_applications')
            ->row();

        if (!$application) {
            $this->session->set_flashdata('error', 'Leave application not found.');
            redirect(base_url('leave_admin/my_queue'));
            return;
        }

        // ✅ Only APPROVED
        if ($application->status !== 'APPROVED') {
            $this->session->set_flashdata('error', 'Only approved leave can be recalled.');
            redirect(base_url('leave_admin/my_queue'));
            return;
        }

        // ✅ Only Forced Leave
        if ($application->leave_type !== 'FL') {
            $this->session->set_flashdata('error', 'Only Forced Leave can be recalled.');
            redirect(base_url('leave_admin/my_queue'));
            return;
        }

        // 🔥 Compute remaining days
        $today = date('Y-m-d');

        $used_days = 0;

        if ($today > $application->date_from) {
            $used_days = (strtotime($today) - strtotime($application->date_from)) / (60*60*24);
            $used_days = floor($used_days);
            $used_days = min($used_days, $application->working_days);
        }

        $remaining_days = $application->working_days - $used_days;

        // ✅ Restore credits (simplified)
        if ($remaining_days > 0) {
            $this->restore_leave_credits(
                $application->staff_idnumber,
                $remaining_days
            );
        }

        // ✅ Update status
        $this->db->where('id', (int)$id)->update('leave_applications', array(
            'status' => 'RECALLED',
            'updated_at' => date('Y-m-d H:i:s')
        ));

        $this->session->set_flashdata('success', 'Leave recalled and credits restored.');
        redirect(base_url('leave_admin/my_queue'));
    }

    private function restore_leave_credits($staff_idnumber, $days)
    {
        // 🔧 adjust table/field based on your system
        $this->db->set('vl_balance', 'vl_balance + ' . (float)$days, false);
        $this->db->where('staff_idnumber', $staff_idnumber);
        $this->db->update('leave_credits');
    }
    public function view_application($id)
    {
        if (!$this->session->userdata('username')) {
            redirect(base_url('login'));
            return;
        }

        $application = $this->Leave_model->get_application_admin_view((int)$id);

        if (!$application) {
            show_error('Leave application not found.', 404);
            return;
        }

        $data['title'] = 'View Leave Application';
        $data['application'] = $application;

        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('leave/view_application', $data);
        $this->load->view('templates/modal');
        $this->load->view('templates/footer');
    }

    public function print_preview($id)
    {
        if (!$this->session->userdata('username')) {
            redirect(base_url('login'));
            return;
        }

        $application = $this->Leave_model->get_application_admin_view((int)$id);

        if (!$application) {
            show_error('Leave application not found.', 404);
            return;
        }
        $this->load->model('Leave_signatory_model');

        $signatories_raw = $this->Leave_signatory_model->get_signatories($id);

        $signatories = [];
        foreach ($signatories_raw as $s) {
            $signatories[$s['signatory_role']] = $s;
        }
        $data['title'] = 'Leave Form Print Preview';
        $data['application'] = $application;
        $data['signatories'] = $signatories;
        $this->load->view('leave/print_preview', $data);
    }


    private function get_entitlement_leave_types()
    {
        return array(
            'MATERNITY',
            'PATERNITY',
            'SOLO_PARENT',
            'VAWC',
            'WELLNESS',
            'REHABILITATION',
            'CALAMITY',
            'PERSONAL'
        );
    }

    private function deduct_entitlement_credit_locked($leave)
    {
        $now  = date('Y-m-d H:i:s');
        $user = $this->session->userdata('username');

        if (isset($leave->deduction_status) && $leave->deduction_status === 'DEDUCTED') {
            return true;
        }

        $leave_type = strtoupper(trim((string)$leave->leave_type_code));

        $map = array(
            'VACATION'    => 'vl_balance',
            'VL'          => 'vl_balance',
            'SICK'        => 'sl_balance',
            'SL'          => 'sl_balance',
            'SPL'         => 'spl_balance',
            'FORCED'      => 'forced_leave_balance',
            'FL'          => 'forced_leave_balance',
            'SOLO_PARENT' => 'solo_parent_balance',
            'WELLNESS'    => 'wellness_balance',
            'COC'         => 'coc_balance_hours'
        );

        if (!isset($map[$leave_type])) {
            return true;
        }

        $field = $map[$leave_type];

        $deducted_value = 0;

        if ($leave_type === 'COC') {
            $deducted_value = isset($leave->working_hours) ? (float)$leave->working_hours : 0;
        } else {
            $deducted_value = isset($leave->working_days) ? (float)$leave->working_days : 0;
        }

        if ($deducted_value <= 0) {
            $deducted_value = isset($leave->days_applied) ? (float)$leave->days_applied : 0;
        }

        if ($deducted_value <= 0) {
            $this->session->set_flashdata('error', 'Invalid deduction value.');
            return false;
        }

        $balance_year = date('Y', strtotime($leave->date_from ?? $now));

        $balance = $this->db
            ->query(
                "SELECT * FROM leave_balances 
                WHERE staff_idnumber = ? 
                AND COALESCE(NULLIF(balance_year,0), ?) = ?
                ORDER BY balance_year DESC, id DESC
                LIMIT 1
                FOR UPDATE",
                array($leave->staff_idnumber, $balance_year, $balance_year)
            )
            ->row();

        if (!$balance) {
            $this->session->set_flashdata('error', 'No leave balance record found.');
            return false;
        }

        $before = isset($balance->$field) ? (float)$balance->$field : 0;

        if ($before < $deducted_value) {
            $this->session->set_flashdata('error', 'Insufficient leave balance for ' . $leave_type . '.');
            return false;
        }

        $after = $before - $deducted_value;

        $this->db
            ->where('id', $balance->id)
            ->update('leave_balances', array(
                $field       => $after,
                'updated_at' => $now,
                'updated_by' => $user
            ));

        $this->db->insert('leave_credit_ledger', array(
            'leave_application_id' => $leave->id,
            'staff_idnumber'      => $leave->staff_idnumber,
            'balance_year'        => $balance_year,
            'leave_type_code'     => $leave_type,
            'transaction_type'    => 'DEDUCT',
            'quantity'            => $deducted_value,
            'balance_before'      => $before,
            'balance_after'       => $after,
            'reference_table'     => 'leave_applications',
            'reference_id'        => $leave->id,
            'remarks'             => 'Auto deduction on approval',
            'performed_by'        => $user,
            'created_at'          => $now,
            'ip_address'          => $this->input->ip_address(),
            'hostname'            => gethostbyaddr($this->input->ip_address()),
            'user_agent'          => $this->input->user_agent()
        ));

        $this->db
            ->where('id', $leave->id)
            ->update('leave_applications', array(
                'deduction_status' => 'DEDUCTED',
                'deducted_at'      => $now,
                'deducted_by'      => $user
            ));

        return $this->db->trans_status();
    }

    private function log_leave_ledger($data)
    {
        $this->db->insert('leave_credit_ledger', array(
            'staff_idnumber' => $data['staff_idnumber'],
            'leave_type_code'=> $data['leave_type_code'],
            'transaction_type'=> $data['transaction_type'],
            'quantity'       => $data['quantity'],
            'reference_table'=> $data['reference_table'] ?? null,
            'reference_id'   => $data['reference_id'] ?? null,
            'remarks'        => $data['remarks'] ?? null,
            'performed_by'   => $this->session->userdata('username'),
            'ip_address'     => $this->input->ip_address(),
            'hostname'       => gethostbyaddr($this->input->ip_address()),
            'user_agent'     => $this->input->user_agent(),
            'created_at'     => date('Y-m-d H:i:s')
        ));
    }

    private function restore_entitlement_credit_locked($leave, $reason = 'Credit restored', $convert_forced_to_vl = false)
    {
        $now  = date('Y-m-d H:i:s');
        $user = $this->session->userdata('username');

        if ($leave->deduction_status !== 'DEDUCTED') {
            return true;
        }

        if (isset($leave->restore_status) && $leave->restore_status === 'RESTORED') {
            return true;
        }

        $map = array(
            'VACATION'     => 'vl_balance',
            'VL'           => 'vl_balance',

            'SICK'         => 'sl_balance',
            'SL'           => 'sl_balance',

            'SPL'          => 'spl_balance',
            'SPECIAL'      => 'spl_balance',

            'FORCED'       => 'fl_balance',
            'FL'           => 'fl_balance',

            'SOLO_PARENT'  => 'solo_parent_balance',
            'WELLNESS'     => 'wellness_balance',
            'COC'          => 'coc_balance_hours',
            'VSC'          => 'vsc_balance_days'
        );

        $leave_type = strtoupper($leave->leave_type_code);

        if (!isset($map[$leave_type])) {
            return true;
        }

        $original_field = $map[$leave_type];

        // SDS recall rule:
        // recalled forced leave becomes regular vacation leave
        $restore_field = $original_field;

        if ($convert_forced_to_vl === true && $leave_type === 'FORCED') {
            $restore_field = 'vl_balance';
        }

        $ledger = $this->db->query("
            SELECT * FROM leave_credit_ledger
            WHERE leave_application_id = ?
            AND action_type = 'DEDUCT'
            ORDER BY id DESC
            LIMIT 1
            FOR UPDATE
        ", array($leave->id))->row();

        if (!$ledger) {
            return 'No deduction ledger found. Cannot restore credits safely.';
        }

        $balance = $this->db->query("
            SELECT * FROM leave_balances
            WHERE staff_idnumber = ?
            AND balance_year = ?
            LIMIT 1
            FOR UPDATE
        ", array($leave->staff_idnumber, $ledger->balance_year))->row();

        if (!$balance) {
            return 'No leave balance found for restoration.';
        }

        $restore_value = (float)$ledger->deducted_value;
        $current       = (float)$balance->$restore_field;
        $new_balance   = $current + $restore_value;

        $this->db->where('id', $balance->id)->update('leave_balances', array(
            $restore_field => $new_balance,
            'updated_at'   => $now,
            'updated_by'   => $user
        ));

        $this->db->insert('leave_credit_ledger', array(
            'leave_application_id' => $leave->id,
            'staff_idnumber'       => $leave->staff_idnumber,
            'leave_type_code'      => $leave_type,
            'balance_year'         => $balance->balance_year,
            'deducted_value'       => $restore_value,
            'balance_field'        => $restore_field,
            'balance_before'       => $current,
            'balance_after'        => $new_balance,
            'action_type'          => 'RESTORE',
            'created_by'           => $user,
            'created_at'           => $now,
            'remarks'              => $reason
        ));

        $this->db->where('id', $leave->id)->update('leave_applications', array(
            'restore_status'  => 'RESTORED',
            'restored_at'     => $now,
            'restored_by'     => $user,
            'restore_remarks' => $reason
        ));

        return true;
    }
    
    public function recall_approved($id)
    {
        $this->guard();

        $id   = (int)$id;
        $now  = date('Y-m-d H:i:s');
        $user = $this->session->userdata('username');

        $this->db->trans_begin();

        $leave = $this->db->query("
            SELECT * FROM leave_applications
            WHERE id = ?
            FOR UPDATE
        ", array($id))->row();

        if (!$leave) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('error', 'Leave application not found.');
            redirect('leave_admin/my_queue');
            return;
        }

        if ($leave->status !== 'APPROVED') {
            $this->db->trans_rollback();
            $this->session->set_flashdata('error', 'Only approved leave can be recalled.');
            redirect('leave_admin/my_queue');
            return;
        }

        $restore = $this->restore_entitlement_credit_locked(
            $leave,
            'Credit restored due to SDS recall. Forced Leave converted to Vacation Leave when applicable.',
            true
        );

        if ($restore !== true) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('error', $restore);
            redirect('leave_admin/my_queue');
            return;
        }

        $this->db->where('id', $id)->update('leave_applications', array(
            'status'     => 'RECALLED',
            'updated_at' => $now
        ));

        $this->db->insert('leave_admin_audit', array(
            'action'         => 'RECALL APPROVED LEAVE',
            'reference_id'   => $id,
            'staff_idnumber' => $leave->staff_idnumber,
            'performed_by'   => $user,
            'ip_address'     => $this->input->ip_address(),
            'hostname'       => gethostbyaddr($this->input->ip_address()),
            'user_agent'     => $this->input->user_agent(),
            'created_at'     => $now
        ));

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('error', 'Recall failed.');
            redirect('leave_admin/my_queue');
            return;
        }

        $this->db->trans_commit();

        $this->session->set_flashdata('success', 'Leave recalled and credits restored.');
        redirect('leave_admin/my_queue');
    }
    public function cancel_approved($id)
    {
        $this->guard();

        $id   = (int)$id;
        $now  = date('Y-m-d H:i:s');
        $user = $this->session->userdata('username');

        $this->db->trans_begin();

        $leave = $this->db->query("
            SELECT * FROM leave_applications
            WHERE id = ?
            FOR UPDATE
        ", array($id))->row();

        if (!$leave) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('error', 'Leave application not found.');
            redirect('leave_admin/my_queue');
            return;
        }

        if ($leave->status !== 'APPROVED') {
            $this->db->trans_rollback();
            $this->session->set_flashdata('error', 'Only approved leave can be cancelled.');
            redirect('leave_admin/my_queue');
            return;
        }

        $restore = $this->restore_entitlement_credit_locked($leave, 'Credit restored due to cancelled approved leave.');

        if ($restore !== true) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('error', $restore);
            redirect('leave_admin/my_queue');
            return;
        }

        $this->db->where('id', $id)->update('leave_applications', array(
            'status'     => 'CANCELLED',
            'updated_at' => $now
        ));

        $this->db->insert('leave_admin_audit', array(
            'action'         => 'CANCEL APPROVED LEAVE',
            'reference_id'   => $id,
            'staff_idnumber' => $leave->staff_idnumber,
            'performed_by'   => $user,
            'ip_address'     => $this->input->ip_address(),
            'hostname'       => gethostbyaddr($this->input->ip_address()),
            'user_agent'     => $this->input->user_agent(),
            'created_at'     => $now
        ));

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('error', 'Cancellation failed.');
            redirect('leave_admin/my_queue');
            return;
        }

        $this->db->trans_commit();

        $this->session->set_flashdata('success', 'Approved leave cancelled and credits restored.');
        redirect('leave_admin/my_queue');
    }
    
    private function guard()
    {
        if (!$this->session->userdata('username')) {
            redirect(base_url('login'));
            exit;
        }
    }

}