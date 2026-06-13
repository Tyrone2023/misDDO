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
    }

    public function index()
    {
        if (!$this->session->userdata('username')) {
            redirect(base_url('login'));
            return;
        }

        $staff_idnumber = $this->session->userdata('username');
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

        $staff_idnumber = trim($this->session->userdata('username'));
        $staff_info = $this->db->where('IDNumber', $staff_idnumber)->get('hris_staff')->row();

        if (!$staff_info) {
            show_error('Employee record not found.');
            return;
        }

        $leave_type_code = trim((string)$this->input->post('leave_type_code'));
        $date_from       = trim((string)$this->input->post('date_from'));
        $date_to         = trim((string)$this->input->post('date_to'));
        $reason          = trim((string)$this->input->post('reason'));
        $half_day        = trim((string)$this->input->post('half_day'));
        $commutation     = trim((string)$this->input->post('commutation'));


        if ($leave_type_code === '' || $date_from === '' || $date_to === '') {
            $this->session->set_flashdata('error', 'Please complete the required fields.');
            redirect('leave');
            return;
        }

        if (strtotime($date_from) === false || strtotime($date_to) === false || $date_from > $date_to) {
            $this->session->set_flashdata('error', 'Invalid leave date range.');
            redirect('leave');
            return;
        }

        $attachment_path = null;
        $attachment_count = 0;

        if (!empty($_FILES['attachment_pdf']['name'])) {
            $upload_path = FCPATH . 'uploads/leave_attachments/';
            if (!is_dir($upload_path)) {
                mkdir($upload_path, 0777, true);
            }

            $config['upload_path'] = $upload_path;
            $config['allowed_types'] = 'pdf';
            $config['max_size'] = 10240; // 10 MB
            $config['encrypt_name'] = true;

            $this->upload->initialize($config);

            if (!$this->upload->do_upload('attachment_pdf')) {
                $this->session->set_flashdata('error', $this->upload->display_errors('', ''));
                redirect('leave');
                return;
            }

            $file_data = $this->upload->data();
            $attachment_path = 'uploads/leave_attachments/' . $file_data['file_name'];
            $attachment_count = 1;
        }

        $working_days = $this->Leave_model->count_working_days($date_from, $date_to, $half_day);

        $is_teaching = (strcasecmp(trim((string)$staff_info->payCat), 'TEACHING') === 0);

        $data = array(
            'staff_idnumber'   => $staff_idnumber,
            'employee_id'      => !empty($staff_info->employeeNo) ? (int)$staff_info->employeeNo : 0,
            'leave_type_code'  => $leave_type_code,
            'personnel_scope'  => $is_teaching ? 'TEACHING' : 'NON_TEACHING',
            'date_filed'       => date('Y-m-d'),
            'date_from'        => $date_from,
            'date_to'          => $date_to,
            'is_half_day'      => ($half_day === 'AM' || $half_day === 'PM') ? 1 : 0,
            'working_days'     => $working_days,
            'working_hours'    => 0.00,
            'reason'           => $reason,
            'status'           => 'PENDING',
            'attachment_count' => $attachment_count,
            'attachment_path'  => $attachment_path,
            'created_at'       => date('Y-m-d H:i:s'),
            'updated_at'       => date('Y-m-d H:i:s')
        );

        $leave_id = $this->Leave_model->create_application($data, $half_day, $commutation);

        if (!$leave_id) {
            $this->session->set_flashdata('error', 'Failed to save leave application.');
            redirect('leave');
            return;
        }

        $this->session->set_flashdata('success', 'Leave application submitted successfully.');
        redirect('leave/my_applications');
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
            redirect(base_url('login'));
            return;
        }

        $staff_idnumber = $this->session->userdata('username');
        $year = date('Y');

        $data['title'] = 'Leave Balances';
        $data['balances'] = $this->Leave_balance_model->get_employee_balances($staff_idnumber, $year);

        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('leave/balance', $data);
        $this->load->view('templates/modal');
        $this->load->view('templates/footer');
    }
    private function resolve_signatories($staff, $leave_days)
    {
        $department = strtolower(trim((string)($staff->Department ?? '')));
        $emp_position = strtolower(trim((string)($staff->empPosition ?? '')));
        $school_id = trim((string)($staff->schoolID ?? ''));

        $certify = '';
        $recommend = '';
        $approve = '';

        // School Head: regardless of days
        if (
            strpos($emp_position, 'principal') !== false ||
            strpos($emp_position, 'school head') !== false ||
            strpos($emp_position, 'head teacher') !== false
        ) {
            $certify = 'admin officer v';
            $recommend = 'asds';
            $approve = 'sds';
        }

        // School personnel
        elseif (!empty($school_id)) {
            if ((int)$leave_days <= 60) {
                $certify = 'admin officer v';
                $recommend = 'school';
                $approve = 'asds';
            } else {
                $certify = 'admin officer v';
                $recommend = 'asds';
                $approve = 'sds';
            }
        }

        // Division OSDS
        elseif (strpos($department, 'osds') !== false || strpos($department, 'office of the schools division superintendent') !== false) {
            $certify = 'human resource admin';
            $recommend = 'admin officer v';
            $approve = 'asds';
        }

        // Division CID
        elseif (strpos($department, 'cid') !== false || strpos($department, 'curriculum') !== false) {
            $certify = 'admin officer v';
            $recommend = 'cid';
            $approve = 'asds';
        }

        // Division SGOD
        elseif (strpos($department, 'sgod') !== false || strpos($department, 'school governance') !== false) {
            $certify = 'admin officer v';
            $recommend = 'sgod';
            $approve = 'asds';
        }

        // Fallback
        else {
            $certify = 'human resource admin';
            $recommend = 'admin officer v';
            $approve = 'asds';
        }

        return [
            'certify_by_position' => $certify,
            'recommend_by_position' => $recommend,
            'approve_by_position' => $approve,
        ];
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

        $staff_idnumber = trim((string)$this->session->userdata('username'));

        $application = $this->Leave_model->get_application_for_view((int)$id, $staff_idnumber);

        if (!$application) {
            show_error('Leave application not found or access denied.', 404);
            return;
        }

        $this->load->model('Leave_signatory_model');
        $signatories_raw = $this->Leave_signatory_model->get_signatories((int)$id);
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
    
}