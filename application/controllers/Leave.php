<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Leave extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library(array('session', 'upload'));
        $this->load->helper(array('url', 'form'));
        $this->load->model('Leave_model');
        $this->load->model('Leave_balance_model');
        $this->load->model('Leave_signatory_model');
        // Optional model for controlled leave eligibility.
        if (file_exists(APPPATH . 'models/Leave_eligibility_model.php')) {
            $this->load->model('Leave_eligibility_model');
        }
    }

    public function index()
    {
        if (!$this->session->userdata('username')) {
            redirect(base_url('login'));
            return;
        }

        $staff_idnumber = trim((string)$this->session->userdata('username'));
        $year = date('Y');

        $staff_info = $this->db->where('IDNumber', $staff_idnumber)->get('hris_staff')->row();

        if (!$staff_info) {
            show_error('Employee record not found.');
            return;
        }

        $data['title'] = 'Leave Application';
        $data['staff_info'] = $staff_info;
        $data['balances'] = $this->Leave_balance_model->get_employee_balances($staff_idnumber, $year);
        $data['my_applications'] = $this->db
            ->where('staff_idnumber', $staff_idnumber)
            ->order_by('id', 'DESC')
            ->get('leave_applications')
            ->result();

        $data['leave_types'] = array(
            'VL' => 'Vacation Leave',
            'SL' => 'Sick Leave',
            'FL' => 'Forced Leave',
            'SPL' => 'Special Privilege Leave',
            'ML' => 'Maternity Leave',
            'PL' => 'Paternity Leave',
            'WELLNESS' => 'Wellness Leave',
            'SOLO_PARENT' => 'Solo Parent Leave',
            'STUDY' => 'Study Leave',
            'VAWC' => 'VAWC Leave',
            'REHAB' => 'Rehabilitation Leave',
            'SLBW' => 'Special Leave Benefits for Women',
            'SEL' => 'Special Emergency Leave',
            'ADOPTION' => 'Adoption Leave',
            'LWOP' => 'Leave Without Pay',
            'CTO_USE' => 'CTO',
            'VSC_USE' => 'Service Credit Use'
        );

        $data['upload_note'] = 'Attachment: PDF only, maximum 2 MB.';
        $data['controlled_leave_types'] = $this->get_controlled_leave_types();

        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('leave/index', $data);
        $this->load->view('templates/modal');
        $this->load->view('templates/footer');
    }

    public function store()
    {
        if (!$this->session->userdata('username')) {
            redirect(base_url('login'));
            return;
        }

        $staff_idnumber  = $this->session->userdata('username');
        $leave_type_code = strtoupper(trim((string)$this->input->post('leave_type_code')));
        $date_from       = trim((string)$this->input->post('date_from'));
        $date_to         = trim((string)$this->input->post('date_to'));
        $date_filed      = date('Y-m-d');
        $is_emergency    = (int)$this->input->post('is_emergency');

        if ($leave_type_code === '') {
            $this->session->set_flashdata('error', 'Please select leave type.');
            redirect(base_url('leave'));
            return;
        }

        if ($date_from === '' || $date_to === '') {
            $this->session->set_flashdata('error', 'Date From and Date To are required.');
            redirect(base_url('leave'));
            return;
        }

        if (strtotime($date_from) > strtotime($date_to)) {
            $this->session->set_flashdata('error', 'Date From cannot be later than Date To.');
            redirect(base_url('leave'));
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Compute working days
        |--------------------------------------------------------------------------
        */
        $working_days = 0;
        $from = new DateTime($date_from);
        $to   = new DateTime($date_to);
        $to->modify('+1 day');

        $period = new DatePeriod($from, new DateInterval('P1D'), $to);

        foreach ($period as $dt) {
            $day = (int)$dt->format('N');

            if ($day < 6) {
                $working_days++;
            }
        }

        if ($working_days <= 0) {
            $this->session->set_flashdata('error', 'Selected date range has no valid working days.');
            redirect(base_url('leave'));
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Filing rule lookup
        |--------------------------------------------------------------------------
        | Supports both your system codes and full policy codes.
        |--------------------------------------------------------------------------
        */
        $rule_lookup = array($leave_type_code);

        if ($leave_type_code === 'VL') {
            $rule_lookup[] = 'VACATION';
        } elseif ($leave_type_code === 'SL') {
            $rule_lookup[] = 'SICK';
        } elseif ($leave_type_code === 'FL') {
            $rule_lookup[] = 'FORCED';
        }

        $filing_rule = $this->db
            ->where_in('leave_type_code', $rule_lookup)
            ->limit(1)
            ->get('leave_filing_rules')
            ->row();

        if ($filing_rule) {

            /*
            |--------------------------------------------------------------------------
            | Advance filing validation
            |--------------------------------------------------------------------------
            */
            if ((int)$filing_rule->advance_days_required > 0) {

                $allow_emergency = (int)$filing_rule->allow_emergency_override === 1;
                $emergency_used  = $allow_emergency && $is_emergency === 1;

                if (!$emergency_used) {

                    $advance_from = new DateTime($date_filed);
                    $advance_to   = new DateTime($date_from);

                    $advance_from->setTime(0,0,0);
                    $advance_to->setTime(0,0,0);

                    $advance_days = (int)$advance_from
                        ->diff($advance_to)
                        ->format('%r%a');

                    if ($advance_days < (int)$filing_rule->advance_days_required) {

                        $this->session->set_flashdata(
                            'error',
                            'This leave type must be filed at least '
                            . (int)$filing_rule->advance_days_required
                            . ' days before the leave date.'
                        );

                        redirect(base_url('leave'));
                        return;
                    }
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Retroactive filing validation
            |--------------------------------------------------------------------------
            */
            if (
                strtotime($date_from) < strtotime($date_filed)
                && (int)$filing_rule->allow_retroactive === 0
            ) {
                $this->session->set_flashdata(
                    'error',
                    'Retroactive filing is not allowed for this leave type.'
                );
                redirect(base_url('leave'));
                return;
            }

            /*
            |--------------------------------------------------------------------------
            | Attachment rule validation
            |--------------------------------------------------------------------------
            */
            if (!empty($filing_rule->requires_attachment_over_days)) {
                $attachment_uploaded = !empty($_FILES['attachment_pdf']['name']);

                if (
                    $working_days > (int)$filing_rule->requires_attachment_over_days
                    && !$attachment_uploaded
                ) {
                    $this->session->set_flashdata(
                        'error',
                        'Attachment is required for leave exceeding '
                        . (int)$filing_rule->requires_attachment_over_days
                        . ' days.'
                    );
                    redirect(base_url('leave'));
                    return;
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Entitlement / Balance validation
        |--------------------------------------------------------------------------
        */
        $validation = $this->validate_leave_credit_before_application(
            $staff_idnumber,
            $leave_type_code,
            $working_days
        );

        if ($validation !== true) {
            $this->session->set_flashdata('error', $validation);
            redirect(base_url('leave'));
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Detail fields
        |--------------------------------------------------------------------------
        */
        $vacation_scope = null;
        $vacation_abroad_specify = null;
        $sick_scope = null;
        $sick_illness_specify = null;
        $study_purpose = null;
        $study_other_specify = null;
        $others_label = null;

        if ($leave_type_code === 'VL' || $leave_type_code === 'SPL') {
            $vacation_scope = $this->input->post('vacation_scope', true);
            $vacation_abroad_specify = trim((string)$this->input->post('vacation_abroad_specify', true));
        } elseif ($leave_type_code === 'SL') {
            $sick_scope = $this->input->post('sick_scope', true);
            $sick_illness_specify = trim((string)$this->input->post('sick_illness_specify', true));
        } elseif ($leave_type_code === 'STUDY') {
            $study_purpose = $this->input->post('study_purpose', true);
            $study_other_specify = trim((string)$this->input->post('study_other_specify', true));
        } elseif (in_array($leave_type_code, array('COC', 'WELLNESS', 'PERSONAL', 'PATERNITY', 'SOLO_PARENT'), true)) {
            $others_label = $leave_type_code;
        }

        /*
        |--------------------------------------------------------------------------
        | Strict details validation
        |--------------------------------------------------------------------------
        */
        if (($leave_type_code === 'VL' || $leave_type_code === 'SPL') && empty($vacation_scope)) {
            $this->session->set_flashdata('error', 'Please select vacation details.');
            redirect(base_url('leave'));
            return;
        }

        if ($vacation_scope === 'ABROAD' && empty($vacation_abroad_specify)) {
            $this->session->set_flashdata('error', 'Please specify destination for abroad leave.');
            redirect(base_url('leave'));
            return;
        }

        if ($leave_type_code === 'SL') {
            if (empty($sick_scope) || empty($sick_illness_specify)) {
                $this->session->set_flashdata('error', 'Please complete sick leave details.');
                redirect(base_url('leave'));
                return;
            }
        }

        if ($leave_type_code === 'STUDY') {
            if (empty($study_purpose)) {
                $this->session->set_flashdata('error', 'Please select study leave purpose.');
                redirect(base_url('leave'));
                return;
            }

            if ($study_purpose === 'OTHER' && empty($study_other_specify)) {
                $this->session->set_flashdata('error', 'Please specify study purpose.');
                redirect(base_url('leave'));
                return;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Prevent duplicate active application
        |--------------------------------------------------------------------------
        */
        $duplicate = $this->db
            ->where('staff_idnumber', $staff_idnumber)
            ->where('leave_type_code', $leave_type_code)
            ->where('date_from', $date_from)
            ->where('date_to', $date_to)
            ->where_in('status', array('PENDING', 'CERTIFIED', 'RECOMMENDED'))
            ->get('leave_applications')
            ->row();

        if ($duplicate) {
            $this->session->set_flashdata('error', 'You already have an active leave application with the same leave type and date range.');
            redirect(base_url('leave'));
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Attachment upload
        |--------------------------------------------------------------------------
        */
        $attachment_path = null;

        if (!empty($_FILES['attachment_pdf']['name'])) {
            $upload_path = FCPATH . 'uploads/leave_attachments/';

            if (!is_dir($upload_path)) {
                mkdir($upload_path, 0777, true);
            }

            $config['upload_path']   = $upload_path;
            $config['allowed_types'] = 'pdf';
            $config['max_size']      = 2048;
            $config['encrypt_name']  = true;

            $this->load->library('upload');
            $this->upload->initialize($config);

            if ($this->upload->do_upload('attachment_pdf')) {
                $fileData = $this->upload->data();
                $attachment_path = 'uploads/leave_attachments/' . $fileData['file_name'];
            } else {
                $this->session->set_flashdata('error', $this->upload->display_errors('', ''));
                redirect(base_url('leave'));
                return;
            }
        }

        $now = date('Y-m-d H:i:s');

        $data = array(
            'staff_idnumber' => $staff_idnumber,
            'leave_type_code' => $leave_type_code,
            'date_filed' => $date_filed,
            'date_from'  => $date_from,
            'date_to'    => $date_to,
            'working_days' => $working_days,
            'is_emergency' => $is_emergency,
            'others_label' => $others_label,
            'vacation_scope' => $vacation_scope,
            'vacation_abroad_specify' => $vacation_abroad_specify,
            'sick_scope' => $sick_scope,
            'sick_illness_specify' => $sick_illness_specify,
            'study_purpose' => $study_purpose,
            'study_other_specify' => $study_other_specify,
            'attachment_path' => $attachment_path,
            'attachment_count' => $attachment_path ? 1 : 0,
            'status' => 'PENDING',
            'created_at' => $now,
            'updated_at' => $now
        );

        $this->db->trans_start();
            /*
            |--------------------------------------------------------------------------
            | Determine Personnel Scope & Routing
            |--------------------------------------------------------------------------
            */

            $staff = $this->db
                ->where('IDNumber', $staff_idnumber)
                ->get('hris_staff')
                ->row();

            $personnel_scope = 'OSDS';

            if ($staff) {

                $department = strtolower(trim((string)$staff->Department));
                $paycat     = strtolower(trim((string)$staff->payCat));

                // SCHOOL PERSONNEL
                if (
                    $paycat === 'teaching' ||
                    strpos($department, 'school') !== false ||
                    strpos($department, 'high school') !== false ||
                    strpos($department, 'elementary') !== false
                ) {

                    $personnel_scope = 'SCHOOL';

                } elseif (
                    strpos($department, 'cid') !== false
                ) {

                    $personnel_scope = 'CID';

                } elseif (
                    strpos($department, 'sgod') !== false
                ) {

                    $personnel_scope = 'SGOD';
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Determine Signatories
            |--------------------------------------------------------------------------
            */

            $days = (float)$working_days;

            $certifier   = 'Admin Officer IV';
            $recommender = 'Admin Officer V';
            $approver    = 'ASDS';

            if ($personnel_scope === 'SCHOOL') {

                $certifier = 'Admin Officer V';

                if ($days <= 60) {

                    $recommender = 'School Head';
                    $approver    = 'ASDS';

                } else {

                    $recommender = 'ASDS';
                    $approver    = 'SDS';
                }

            } elseif ($personnel_scope === 'CID') {

                $certifier   = 'Admin Officer IV';
                $recommender = 'CID Chief';
                $approver    = 'ASDS';

            } elseif ($personnel_scope === 'SGOD') {

                $certifier   = 'Admin Officer IV';
                $recommender = 'SGOD Chief';
                $approver    = 'ASDS';
            }

            /*
            |--------------------------------------------------------------------------
            | Attach Routing To Insert Payload
            |--------------------------------------------------------------------------
            */

            $data['personnel_scope']      = $personnel_scope;
            $data['certify_by_position']  = $certifier;
            $data['recommend_by_position'] = $recommender;
            $data['approve_by_position']  = $approver;
        $this->db->insert('leave_applications', $data);
        if (!$this->db->affected_rows()) {
            die($this->db->error()['message']);
        }
        $application_id = $this->db->insert_id();

        $this->db->insert('leave_admin_audit', array(
            'action' => 'SUBMIT LEAVE APPLICATION',
            'reference_id' => $application_id,
            'staff_idnumber' => $staff_idnumber,
            'performed_by' => $staff_idnumber,
            'ip_address' => $this->input->ip_address(),
            'hostname' => gethostbyaddr($this->input->ip_address()),
            'user_agent' => $this->input->user_agent(),
            'created_at' => $now
        ));

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            $this->session->set_flashdata('error', 'Leave application failed. Please try again.');
            redirect(base_url('leave'));
            return;
        }

        $this->session->set_flashdata('success', 'Leave application submitted successfully.');
        redirect(base_url('leave/my_applications'));
    }

    public function my_applications()
    {
        if (!$this->session->userdata('username')) {
            redirect(base_url('login'));
            return;
        }

        $staff_idnumber = $this->session->userdata('username');

        $data['title'] = 'My Leave Applications';
        $data['applications'] = $this->db
            ->where('staff_idnumber', $staff_idnumber)
            ->order_by('id', 'DESC')
            ->get('leave_applications')
            ->result();

        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('leave/my_applications', $data);
        $this->load->view('templates/modal');
        $this->load->view('templates/footer');
    }

    public function balance()
    {
        if (!$this->session->userdata('username')) {
            redirect('login');
            return;
        }

        $staff_idnumber = $this->session->userdata('username');

        $data['title'] = 'Leave Balances';

        $balances = $this->db
            ->where('staff_idnumber', $staff_idnumber)
            ->order_by('updated_at', 'DESC')
            ->get('leave_balances')
            ->row_array();

        if (!$balances) {
            $balances = array();
        }

        $data['entitlements'] = $this->db
            ->where('staff_idnumber', $staff_idnumber)
            ->where('status', 'APPROVED')
            ->get('leave_entitlements')
            ->result();

        foreach ($data['entitlements'] as $e) {
            $remaining = (float)$e->credits - (float)$e->used;

            if ($remaining < 0) {
                $remaining = 0;
            }

            switch (strtoupper($e->leave_type_code)) {
                case 'SOLO_PARENT':
                    $balances['solo_parent_balance'] = $remaining;
                    break;

                case 'WELLNESS':
                    $balances['wellness_balance'] = $remaining;
                    break;
            }
        }

        $data['balances'] = $balances;

        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('leave/balance', $data);
        $this->load->view('templates/modal');
        $this->load->view('templates/footer');
    }

    public function view_application($id)
    {
        if (!$this->session->userdata('username')) {
            redirect(base_url('login'));
            return;
        }

        $staff_idnumber = trim((string)$this->session->userdata('username'));
        $application = $this->Leave_model->get_application_for_view((int)$id, $staff_idnumber);

        if (!$application) {
            show_error('Leave application not found or access denied.', 404);
            return;
        }

        $attachment_path = !empty($application->attachment_path) ? (string)$application->attachment_path : '';
        $data['title'] = 'View Leave Application';
        $data['application'] = $application;
        $data['attachment_url'] = $attachment_path !== '' ? base_url($attachment_path) : '';
        $data['is_pdf_attachment'] = ($attachment_path !== '' && strtolower(pathinfo($attachment_path, PATHINFO_EXTENSION)) === 'pdf');

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

        $staff_idnumber = trim((string)$this->session->userdata('username'));
        $application = $this->Leave_model->get_application_for_view((int)$id, $staff_idnumber);

        if (!$application) {
            show_error('Leave application not found or access denied.', 404);
            return;
        }

        $signatories_raw = $this->Leave_signatory_model->get_signatories((int)$id);
        if (empty($signatories_raw)) {
            $staff_snapshot = $this->Leave_signatory_model->get_staff($staff_idnumber);
            if ($staff_snapshot) {
                $resolved_signatories = $this->Leave_signatory_model->resolve_signatories($staff_snapshot, (float)($application->working_days ?? 0));
                $this->Leave_signatory_model->snapshot_signatories((int)$id, $resolved_signatories);
                $signatories_raw = $this->Leave_signatory_model->get_signatories((int)$id);
            }
        }

        $signatories = array();
        foreach ($signatories_raw as $row) {
            if (!empty($row['signatory_role'])) {
                $signatories[$row['signatory_role']] = $row;
            }
        }

        $data['title'] = 'Leave Form Print Preview';
        $data['application'] = $application;
        $data['signatories'] = $signatories;

        $this->load->view('leave/print_preview', $data);
    }

    private function get_controlled_leave_types()
    {
        return array('ML', 'PL', 'SOLO_PARENT', 'STUDY', 'VAWC', 'REHAB', 'SLBW', 'SEL', 'ADOPTION');
    }

    private function is_controlled_leave_type($leave_type_code)
    {
        return in_array(strtoupper(trim((string)$leave_type_code)), $this->get_controlled_leave_types(), true);
    }

    private function get_non_mandatory_leaves()
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

    private function validate_leave_credit_before_application($staff_idnumber, $leave_type_code, $days_requested)
    {
        $leave_type_code = strtoupper(trim((string)$leave_type_code));
        $days_requested  = (float)$days_requested;

        $non_mandatory = $this->get_non_mandatory_leaves();

        // Non-mandatory leaves must have approved HR entitlement first
        if (in_array($leave_type_code, $non_mandatory, true)) {
            $entitlement = $this->db
                ->where('staff_idnumber', $staff_idnumber)
                ->where('leave_type_code', $leave_type_code)
                ->where('status', 'APPROVED')
                ->get('leave_entitlements')
                ->row();

            if (!$entitlement) {
                return 'No approved entitlement found for this leave type. Please request leave privilege first.';
            }

            $available = (float)$entitlement->credits - (float)$entitlement->used;

            if ($available <= 0) {
                return 'No remaining approved credits for this leave type.';
            }

            if ($days_requested > $available) {
                return 'Requested days exceed available approved credits. Available: ' . number_format($available, 2);
            }

            return true;
        }

        /*
        |--------------------------------------------------------------------------
        | Leave types that DO NOT require leave_balances
        |--------------------------------------------------------------------------
        */
        $privilege_based = array(
            'MATERNITY',
            'PATERNITY',
            'SOLO_PARENT',
            'VAWC',
            'REHABILITATION',
            'STUDY',
            'SPECIAL'
        );

        if (in_array($leave_type_code, $privilege_based, true)) {
            return true;
        }

        /*
        |--------------------------------------------------------------------------
        | Balance-based leave types
        |--------------------------------------------------------------------------
        */
        $balance = $this->Leave_balance_model->get_latest_balance($staff_idnumber);

        if (!$balance) {
            return 'No leave balance record found. Please contact HR.';
        }

        $available = null;

        if ($leave_type_code === 'VL' || $leave_type_code === 'VACATION') {
            $available = isset($balance->vl_balance) ? (float)$balance->vl_balance : 0;
        } elseif ($leave_type_code === 'SL' || $leave_type_code === 'SICK') {
            $available = isset($balance->sl_balance) ? (float)$balance->sl_balance : 0;
        } elseif ($leave_type_code === 'SPL') {
            $available = isset($balance->spl_balance) ? (float)$balance->spl_balance : 0;
        } elseif ($leave_type_code === 'FORCED' || $leave_type_code === 'FL') {
            $available = isset($balance->forced_leave_balance)
                ? (float)$balance->forced_leave_balance
                : (isset($balance->fl_balance) ? (float)$balance->fl_balance : 0);
        } elseif ($leave_type_code === 'WELLNESS') {
            $available = isset($balance->wellness_balance) ? (float)$balance->wellness_balance : 0;
        } elseif ($leave_type_code === 'COC') {
            $available = isset($balance->coc_balance_hours)
                ? (float)$balance->coc_balance_hours
                : (isset($balance->coc_balance) ? (float)$balance->coc_balance : 0);
        }

        if ($available !== null && $days_requested > $available) {
            return 'Insufficient leave balance. Available: ' . number_format($available, 2);
        }

        return true;
    }

    private function validate_entitlement($staff_idnumber, $leave_type_code, $days_requested)
    {
        $non_mandatory = $this->get_non_mandatory_leaves();

        if (!in_array($leave_type_code, $non_mandatory)) {
            return true;
        }

        $entitlement = $this->db
            ->where('staff_idnumber', $staff_idnumber)
            ->where('leave_type_code', $leave_type_code)
            ->where('status', 'APPROVED')
            ->get('leave_entitlements')
            ->row();

        if (!$entitlement) {
            return 'No approved entitlement for this leave type.';
        }

        $available = (float)$entitlement->credits - (float)$entitlement->used;

        if ($available <= 0) {
            return 'No remaining credits for this leave type.';
        }

        if ($days_requested > $available) {
            return 'Requested days exceed available entitlement (' . $available . ').';
        }

        return true;
    }

    public function request_entitlement()
    {
        if (!$this->session->userdata('username')) {
            redirect('login');
            return;
        }

        $staff_idnumber = $this->session->userdata('username');

        if (!$staff_idnumber) {
            redirect('login');
            return;
        }

        $leave_type_code = strtoupper(trim((string)$this->input->post('leave_type_code')));
        $leave_subtype   = strtoupper(trim((string)$this->input->post('leave_subtype')));
        $remarks         = trim((string)$this->input->post('remarks'));
        $now             = date('Y-m-d H:i:s');

        /*
        |--------------------------------------------------------------------------
        | Employee Info
        |--------------------------------------------------------------------------
        */
        $employee = $this->db
            ->where('IDNumber', $staff_idnumber)
            ->get('hris_staff')
            ->row();

        if (!$employee) {
            $this->session->set_flashdata('error', 'Employee record not found.');
            redirect('leave');
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Prevent Duplicate Pending Request
        |--------------------------------------------------------------------------
        */
        $existing = $this->db
            ->where('staff_idnumber', $staff_idnumber)
            ->where('leave_type_code', $leave_type_code)
            ->where_in('status', array('PENDING', 'APPROVED'))
            ->get('leave_entitlements')
            ->row();

        if ($existing) {

            // Allow reapplication if fully consumed
            $remaining = (float)$existing->credits - (float)$existing->used;

            if ($remaining > 0) {

                $this->session->set_flashdata(
                    'error',
                    'You still have available approved credits for this leave type.'
                );

                redirect('leave');
                return;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Default Credits
        |--------------------------------------------------------------------------
        */
        $credits = 0;

        /*
        |--------------------------------------------------------------------------
        | Leave Type Rules
        |--------------------------------------------------------------------------
        */
        switch ($leave_type_code) {

            case 'MATERNITY':

                $maternity_days = array(
                    'LIVE_CHILDBIRTH'             => 105,
                    'LIVE_CHILDBIRTH_SOLO_PARENT' => 120,
                    'MISCARRIAGE'                 => 60
                );

                if (!isset($maternity_days[$leave_subtype])) {

                    $this->session->set_flashdata(
                        'error',
                        'Please select a valid maternity leave type.'
                    );

                    redirect('leave');
                    return;
                }

                $credits = (float)$maternity_days[$leave_subtype];
                break;

            case 'PATERNITY':
                $credits = 7;
                break;

            case 'SOLO_PARENT':
                $credits = 7;
                break;

            case 'VAWC':
                $credits = 10;
                break;

            case 'WELLNESS':
                $credits = 5;
                break;

            case 'REHABILITATION':
                $credits = 180;
                break;

            case 'SPECIAL':
                $credits = 3;
                break;

            default:

                $this->session->set_flashdata(
                    'error',
                    'Invalid leave entitlement type.'
                );

                redirect('leave');
                return;
        }

        /*
        |--------------------------------------------------------------------------
        | Upload Attachment
        |--------------------------------------------------------------------------
        */
        $attachment_path = null;

        if (!empty($_FILES['attachment']['name'])) {

            $config['upload_path']   = './uploads/leave_entitlements/';
            $config['allowed_types'] = 'pdf|jpg|jpeg|png';
            $config['max_size']      = 2048;
            $config['encrypt_name']  = true;

            if (!is_dir($config['upload_path'])) {
                mkdir($config['upload_path'], 0777, true);
            }

            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('attachment')) {

                $this->session->set_flashdata(
                    'error',
                    strip_tags($this->upload->display_errors())
                );

                redirect('leave');
                return;
            }

            $upload_data = $this->upload->data();
            $attachment_path = 'uploads/leave_entitlements/' . $upload_data['file_name'];
        }

        /*
        |--------------------------------------------------------------------------
        | Save Entitlement Request
        |--------------------------------------------------------------------------
        */
        $insert = array(
            'staff_idnumber' => $staff_idnumber,
            'leave_type_code' => $leave_type_code,
            'leave_subtype' => $leave_subtype,
            'credits' => $credits,
            'used' => 0,
            'remarks' => $remarks,
            'attachment_path' => $attachment_path,
            'status' => 'PENDING',
            'created_at' => $now,
            'updated_at' => $now
        );

        $this->db->insert('leave_entitlements', $insert);

        /*
        |--------------------------------------------------------------------------
        | Success
        |--------------------------------------------------------------------------
        */
        $this->session->set_flashdata(
            'success',
            'Leave entitlement request submitted successfully.'
        );

        redirect('leave');
    }

    public function get_filing_rule($leave_type_code)
    {
        return $this->db
            ->where('leave_type_code', strtoupper($leave_type_code))
            ->get('leave_filing_rules')
            ->row();
    }

    private function count_working_days_between($start, $end)
    {
        $count = 0;

        $current = strtotime($start);
        $endDate = strtotime($end);

        while ($current <= $endDate) {

            $day = date('N', $current);

            // 1 = Monday, 7 = Sunday
            if ($day < 6) {
                $count++;
            }

            $current = strtotime('+1 day', $current);
        }

        return $count;
    }

    
}
