<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Leave_balance_admin extends CI_Controller
{
    private $allowed_positions = array('admin officer iv', 'human resource admin' ,'sds');

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library(array('session', 'upload'));
        $this->load->helper(array('url', 'form'));
        $this->load->model('Leave_balance_admin_model');
        $this->load->model('Leave_eligibility_model');
    }

    public function index()
    {
        $this->guard();
        $this->guard_hr_admin();

        $data['title'] = 'Bulk Leave Balance Upload';
        $data['recent_uploads'] = array();
        $data['controlled_leave_types'] = $this->controlled_leave_types();

        $data['latest_balances'] = $this->db
            ->select('
                lb.*,
                hs.FirstName,
                hs.MiddleName,
                hs.LastName,
                hs.NameExtn,
                hs.empPosition
            ')
            ->from('leave_balances lb')
            ->join('hris_staff hs', 'hs.IDNumber = lb.staff_idnumber', 'left')
            ->order_by('lb.updated_at', 'DESC')
            ->limit(50)
            ->get()
            ->result();

        $this->load->view('templates/head');
        $this->load->view('templates/header');

        // IMPORTANT: make sure this is the patched Excel upload view
        $this->load->view('leave/admin_balance_upload', $data);

        $this->load->view('templates/modal');
        $this->load->view('templates/footer');
    }

    public function upload_balances()
    {
        $this->guard();

        if (empty($_FILES['balance_file']['name'])) {
            $this->session->set_flashdata('error', 'Please upload a CSV file.');
            redirect('leave_balance_admin');
            return;
        }

        $upload_path = FCPATH . 'uploads/leave_balance_imports/';
        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0777, true);
        }

        $config = array(
            'upload_path'   => $upload_path,
            'allowed_types' => 'csv',
            'max_size'      => 5120,
            'encrypt_name'  => true,
        );

        $this->upload->initialize($config);
        if (!$this->upload->do_upload('balance_file')) {
            $this->session->set_flashdata('error', $this->upload->display_errors('', ''));
            redirect('leave_balance_admin');
            return;
        }

        $file = $this->upload->data();
        $year = (int)$this->input->post('balance_year');
        if ($year <= 0) {
            $year = (int)date('Y');
        }

        $result = $this->Leave_balance_admin_model->import_balances_from_csv($file['full_path'], $year, (int)$this->session->userdata('user_id'));

        $this->session->set_flashdata($result['success'] ? 'success' : 'error', $result['message']);
        redirect('leave_balance_admin');
    }

    public function privileges()
    {
        $this->guard();
        $this->guard_sds_only();

        $data['title'] = 'Leave Privileges';

        $data['pending_entitlements'] = $this->db
            ->select('e.*, s.FirstName, s.MiddleName, s.LastName, s.Department, s.empPosition')
            ->from('leave_entitlements e')
            ->join('hris_staff s', 's.IDNumber = e.staff_idnumber', 'left')
            ->where('e.status', 'PENDING')
            ->order_by('e.created_at', 'DESC')
            ->get()
            ->result();

        $data['processed_entitlements'] = $this->db
            ->select('e.*, s.FirstName, s.MiddleName, s.LastName, s.Department, s.empPosition')
            ->from('leave_entitlements e')
            ->join('hris_staff s', 's.IDNumber = e.staff_idnumber', 'left')
            ->where_in('e.status', array('APPROVED', 'DENIED'))
            ->order_by('e.approved_at', 'DESC')
            ->limit(100)
            ->get()
            ->result();

        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('leave/admin_privileges', $data);
        $this->load->view('templates/modal');
        $this->load->view('templates/footer');
    }

    public function save_privilege()
    {
        $this->guard();

        $staff_idnumber  = trim((string)$this->input->post('staff_idnumber'));
        $leave_type_code = strtoupper(trim((string)$this->input->post('leave_type_code')));
        $status          = strtoupper(trim((string)$this->input->post('status')));
        $remarks         = trim((string)$this->input->post('remarks'));

        if ($staff_idnumber === '' || $leave_type_code === '' || !in_array($status, array('APPROVED', 'DENIED', 'PENDING'), true)) {
            $this->session->set_flashdata('error', 'Please complete the privilege form.');
            redirect('leave_balance_admin/privileges?staff_idnumber=' . rawurlencode($staff_idnumber));
            return;
        }

        $saved = $this->Leave_eligibility_model->save_or_update_privilege(array(
            'staff_idnumber' => $staff_idnumber,
            'leave_type_code' => $leave_type_code,
            'status' => $status,
            'remarks' => $remarks,
        ), (int)$this->session->userdata('user_id'));

        $this->session->set_flashdata($saved ? 'success' : 'error', $saved ? 'Privilege setting saved.' : 'Privilege setting could not be saved.');
        redirect('leave_balance_admin/privileges?staff_idnumber=' . rawurlencode($staff_idnumber));
    }

    public function upload_history()
    {
        $this->guard();
        $this->guard_hr_admin();

        $filters = array(
            'date_from' => trim((string)$this->input->get('date_from')),
            'date_to'   => trim((string)$this->input->get('date_to')),
            'status'    => trim((string)$this->input->get('status')),
        );

        $this->db
            ->select('*')
            ->from('leave_balance_upload_batches');

        if ($filters['date_from'] !== '') {
            $this->db->where('DATE(uploaded_at) >=', $filters['date_from']);
        }

        if ($filters['date_to'] !== '') {
            $this->db->where('DATE(uploaded_at) <=', $filters['date_to']);
        }

        if ($filters['status'] !== '') {
            $this->db->where('status', $filters['status']);
        }

        $data['title'] = 'Upload History';
        $data['filters'] = $filters;
        $data['logs'] = $this->db
            ->order_by('id', 'DESC')
            ->limit(200)
            ->get()
            ->result();

        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('leave/admin_upload_history', $data);
        $this->load->view('templates/modal');
        $this->load->view('templates/footer');
    }

    public function privilege_history()
    {
        $this->guard();


        $filters = array(
            'staff_idnumber' => trim((string)$this->input->get('staff_idnumber')),
            'leave_type_code'=> trim((string)$this->input->get('leave_type_code')),
            'action'         => trim((string)$this->input->get('action')),
            'date_from'      => trim((string)$this->input->get('date_from')),
            'date_to'        => trim((string)$this->input->get('date_to')),
        );

        $data['title'] = 'Privilege History';
        $data['filters'] = $filters;
        $data['controlled_leave_types'] = $this->controlled_leave_types();
        $data['logs'] = $this->Leave_eligibility_model->get_privilege_logs($filters, 200);

        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('leave/admin_privilege_history', $data);
        $this->load->view('templates/modal');
        $this->load->view('templates/footer');
    }

    private function controlled_leave_types()
    {
        return array(
            'ML' => 'Maternity Leave',
            'PL' => 'Paternity Leave',
            'SOLO_PARENT' => 'Solo Parent Leave',
            'STUDY' => 'Study Leave',
            'VAWC' => 'VAWC Leave',
            'REHAB' => 'Rehabilitation Leave',
            'SLBW' => 'Special Leave Benefits for Women',
            'SEL' => 'Special Emergency Leave',
            'ADOPTION' => 'Adoption Leave',
        );
    }

    private function guard()
    {
        if (!$this->session->userdata('username')) {
            redirect(base_url('login'));
            exit;
        }

        $position = strtolower(trim((string)$this->session->userdata('position')));
        if (!in_array($position, $this->allowed_positions, true)) {
            show_error('Unauthorized access.', 403);
            exit;
        }
    }

    private function guard_sds_only()
    {
        $position = strtolower(trim((string)$this->session->userdata('position')));

        if ($position !== 'sds') {
            show_error('Access denied. SDS only.', 403);
            exit;
        }
    }

    public function upload()
    {


    
        $this->guard();

        $data['title'] = 'Upload Leave Balances';

        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('leave/upload', $data);
        $this->load->view('templates/modal');
        $this->load->view('templates/footer');
    }

    public function process_upload()
    {
        $this->guard();
        $this->guard_hr_admin();

        $user = $this->session->userdata('username');
        $now  = date('Y-m-d H:i:s');

        if (!isset($_FILES['excel_file']) || $_FILES['excel_file']['error'] !== UPLOAD_ERR_OK) {
            $this->session->set_flashdata('error', 'Upload failed. No valid Excel file received.');
            redirect('leave_balance_admin/index');
            return;
        }

        $upload_path = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'leave_balances' . DIRECTORY_SEPARATOR;

        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0777, true);
        }

        $orig_name = $_FILES['excel_file']['name'];
        $ext = strtolower(pathinfo($orig_name, PATHINFO_EXTENSION));

        if (!in_array($ext, array('xls', 'xlsx'), true)) {
            $this->session->set_flashdata('error', 'Invalid file type. Please upload .xls or .xlsx only.');
            redirect('leave_balance_admin/index');
            return;
        }

        $stored_name = date('YmdHis') . '_' . uniqid() . '.' . $ext;
        $file_path = $upload_path . $stored_name;

        if (!move_uploaded_file($_FILES['excel_file']['tmp_name'], $file_path)) {
            $this->session->set_flashdata('error', 'Unable to move uploaded file.');
            redirect('leave_balance_admin/index');
            return;
        }

        require_once FCPATH . 'vendor/autoload.php';

        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file_path);
        } catch (Exception $e) {
            $this->session->set_flashdata('error', 'Unable to read Excel: ' . $e->getMessage());
            redirect('leave_balance_admin/index');
            return;
        }

        $this->db->insert('leave_balance_upload_batches', array(
            'original_filename' => $orig_name,
            'stored_filename'   => $stored_name,
            'uploaded_by'       => $user,
            'uploaded_at'       => $now,
            'ip_address'        => $this->input->ip_address(),
            'user_agent'        => $this->input->user_agent(),
            'hostname'          => gethostbyaddr($this->input->ip_address()),
            'status'            => 'PROCESSING'
        ));

        $batch_id = $this->db->insert_id();

        $sheet = $spreadsheet->getSheetByName('leave_credit_upload');
        if (!$sheet) {
            $sheet = $spreadsheet->getSheet(0);
        }

        $rows = $sheet->toArray(null, true, true, true);

        $headers = array();
        $headerRow = 0;

        for ($h = 1; $h <= min(10, count($rows)); $h++) {
            $temp = array();

            foreach ($rows[$h] as $col => $value) {
                $header = strtolower(trim((string)$value));
                $header = str_replace(array(' ', '-', '.'), '_', $header);
                $temp[$col] = $header;
            }

            if (in_array('staff_idnumber', $temp, true)) {
                $headers = $temp;
                $headerRow = $h;
                break;
            }
        }

        if ($headerRow == 0) {
            $this->db->where('id', $batch_id)->update('leave_balance_upload_batches', array(
                'status' => 'FAILED',
                'message' => 'Header row not found.'
            ));

            $this->session->set_flashdata('error', 'Header row not found. Expected staff_idnumber.');
            redirect('leave_balance_admin/index');
            return;
        }

        $inserted = 0;
        $updated = 0;
        $skipped = 0;
        $total = 0;

        $this->db->trans_begin();

        for ($i = $headerRow + 1; $i <= count($rows); $i++) {
            $row = $rows[$i];

            $staff_idnumber = $this->_excel_value($row, $headers, array('staff_idnumber', 'employee_id', 'idnumber'));

            if ($staff_idnumber === '') {
                $skipped++;
                continue;
            }

            $total++;

            $balance_year = $this->_excel_value($row, $headers, array('balance_year', 'year', 'upload_year'));
            if ($balance_year === '') {
                $balance_year = date('Y');
            }

            $data = array(
                'staff_idnumber'       => $staff_idnumber,
                'balance_year'         => (int)$balance_year,
                'vl_balance'           => $this->_excel_num($row, $headers, array('vl_balance', 'vacation_leave')),
                'sl_balance'           => $this->_excel_num($row, $headers, array('sl_balance', 'sick_leave')),
                'spl_balance'          => $this->_excel_num($row, $headers, array('spl_balance', 'special_privilege_leave')),
                'fl_balance' => $this->_excel_num($row, $headers, array('forced_leave_balance', 'fl_balance')),                
                'solo_parent_balance'  => $this->_excel_num($row, $headers, array('solo_parent_balance', 'solo_parent_leave')),
                'wellness_balance'     => $this->_excel_num($row, $headers, array('wellness_balance', 'wellness_leave')),
                'coc_balance_hours'    => $this->_excel_num($row, $headers, array('coc_balance_hours', 'coc_hours')),
                'vsc_balance_days'     => $this->_excel_num($row, $headers, array('vsc_balance_days', 'vsc_days')),

                'source_file'          => $orig_name,
                'upload_batch_id'      => $batch_id,
                'updated_at'           => $now,
                'updated_by'           => $user
            );

            $existing = $this->db
                ->where('staff_idnumber', $staff_idnumber)
                ->where('balance_year', (int)$balance_year)
                ->get('leave_balances')
                ->row();

            if ($existing) {
                $this->db->where('id', $existing->id)->update('leave_balances', $data);
                $updated++;
            } else {
                $data['created_at'] = $now;
                $this->db->insert('leave_balances', $data);
                $inserted++;
            }
                $balance_id = $existing ? $existing->id : $this->db->insert_id();

                $before_values = array(
                    'VL'          => $existing ? (float)$existing->vl_balance : 0,
                    'SL'          => $existing ? (float)$existing->sl_balance : 0,
                    'SPL'         => $existing ? (float)$existing->spl_balance : 0,
                    'FL'          => $existing ? (float)$existing->fl_balance : 0,
                    'SOLO_PARENT' => $existing ? (float)$existing->solo_parent_balance : 0,
                    'WELLNESS'    => $existing ? (float)$existing->wellness_balance : 0,
                    'COC'         => $existing ? (float)$existing->coc_balance_hours : 0,
                    'VSC'         => $existing ? (float)$existing->vsc_balance_days : 0,
                );

                $after_values = array(
                    'VL'          => (float)$data['vl_balance'],
                    'SL'          => (float)$data['sl_balance'],
                    'SPL'         => (float)$data['spl_balance'],
                    'FL'          => (float)$data['fl_balance'],
                    'SOLO_PARENT' => (float)$data['solo_parent_balance'],
                    'WELLNESS'    => (float)$data['wellness_balance'],
                    'COC'         => (float)$data['coc_balance_hours'],
                    'VSC'         => (float)$data['vsc_balance_days'],
                );

                foreach ($after_values as $type => $after) {
                    $before = isset($before_values[$type]) ? $before_values[$type] : 0;

                    $this->_insert_balance_import_ledger(
                        $staff_idnumber,
                        (int)$balance_year,
                        $type,
                        $before,
                        $after,
                        $balance_id,
                        $batch_id,
                        $orig_name
                    );
                }
            $db_error = $this->db->error();
            if (!empty($db_error['message'])) {
                $this->db->trans_rollback();
                echo '<pre>';
                echo "DB ERROR ON ROW {$i}\n\n";
                print_r($db_error);
                echo "\nLAST QUERY:\n";
                echo $this->db->last_query();
                exit;
            }
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('error', 'Database transaction failed.');
            redirect('leave_balance_admin/index');
            return;
        }

        $this->db->trans_commit();

        $this->db->where('id', $batch_id)->update('leave_balance_upload_batches', array(
            'total_rows'    => $total,
            'inserted_rows' => $inserted,
            'updated_rows'  => $updated,
            'skipped_rows'  => $skipped,
            'status'        => 'COMPLETED',
            'message'       => 'Upload completed.'
        ));

        $this->session->set_flashdata(
            'success',
            "Upload complete. Inserted: {$inserted}, Updated: {$updated}, Skipped: {$skipped}."
        );

        redirect('leave_balance_admin/index');
    }

    private function _find_excel_header_row($rows, $requiredHeaders)
    {
        $max = min(10, count($rows));

        for ($i = 1; $i <= $max; $i++) {
            if (!isset($rows[$i])) {
                continue;
            }

            $headers = array();

            foreach ($rows[$i] as $col => $value) {
                $headers[$col] = $this->_normalize_excel_header($value);
            }

            foreach ($requiredHeaders as $required) {
                if (in_array($required, $headers, true)) {
                    return array(
                        'row'     => $i,
                        'headers' => $headers
                    );
                }
            }
        }

        return false;
    }

    private function _normalize_excel_header($value)
    {
        $value = strtolower(trim((string)$value));
        $value = str_replace(array(' ', '-', '.', '/', '\\'), '_', $value);
        $value = preg_replace('/_+/', '_', $value);
        return trim($value, '_');
    }

    private function _excel_value($row, $headers, $possibleNames)
    {
        foreach ($headers as $col => $header) {
            $header = strtolower(trim((string)$header));
            $header = str_replace(array(' ', '-', '.'), '_', $header);

            if (in_array($header, $possibleNames, true)) {
                return isset($row[$col]) ? trim((string)$row[$col]) : '';
            }
        }

        return '';
    }

    private function _excel_num($row, $headers, $possibleNames)
    {
        $value = $this->_excel_value($row, $headers, $possibleNames);

        if ($value === '') {
            return 0;
        }

        $value = str_replace(',', '', $value);

        return is_numeric($value) ? (float)$value : 0;
    }

    private function _excel_date($value)
    {
        $value = trim((string)$value);

        if ($value === '') {
            return null;
        }

        if (is_numeric($value)) {
            try {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('Y-m-d');
            } catch (Exception $e) {
                return null;
            }
        }

        $time = strtotime($value);

        if ($time === false) {
            return null;
        }

        return date('Y-m-d', $time);
    }

    private function _excel_row_is_empty($row)
    {
        foreach ($row as $value) {
            if (trim((string)$value) !== '') {
                return false;
            }
        }

        return true;
    }

    private function _filter_existing_columns($table, $data)
    {
        $fields = $this->db->list_fields($table);
        $clean  = array();

        foreach ($data as $key => $value) {
            if (in_array($key, $fields, true)) {
                $clean[$key] = $value;
            }
        }

        return $clean;
    }

    private function _row_is_empty($row)
    {
        foreach ($row as $value) {
            if (trim((string)$value) !== '') {
                return false;
            }
        }

        return true;
    }

    private function expire_coc_logs()
    {
        $now = date('Y-m-d H:i:s');
        $this->log_leave_ledger(array(
            'staff_idnumber' => $row->staff_idnumber,
            'leave_type_code'=> 'COC',
            'transaction_type'=> 'EXPIRY',
            'quantity'       => $row->coc_hours,
            'reference_table'=> 'leave_coc_logs',
            'reference_id'   => $row->id,
            'remarks'        => 'COC expired after 1 year'
        ));
        $this->db
            ->where('status', 'ACTIVE')
            ->where('expiry_date <', date('Y-m-d'))
            ->update('leave_coc_logs', array(
                'status' => 'EXPIRED',
                'updated_at' => $now
            ));
    }

    private function recompute_coc_balance($staff_idnumber)
    {
        $row = $this->db->select_sum('coc_hours')
            ->where('staff_idnumber', $staff_idnumber)
            ->where('status', 'ACTIVE')
            ->get('leave_coc_logs')
            ->row();

        $active_coc = $row && $row->coc_hours ? (float)$row->coc_hours : 0;

        $this->db
            ->where('staff_idnumber', $staff_idnumber)
            ->update('leave_balances', array(
                'coc_balance' => $active_coc,
                'updated_at' => date('Y-m-d H:i:s')
            ));
    }

    public function entitlements()
    {
        $this->guard();
        $this->guard_hr_admin();

        $data['title'] = 'Leave Entitlements';

        $data['entitlements'] = $this->db
            ->select('e.*, s.FirstName, s.LastName')
            ->from('leave_entitlements e')
            ->join('hris_staff s', 's.IDNumber = e.staff_idnumber', 'left')
            ->order_by('e.id', 'DESC')
            ->get()
            ->result();

        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('leave/admin_privileges', $data);
        $this->load->view('templates/modal');
        $this->load->view('templates/footer');
    }

    public function approve_entitlement($id)
    {
        $this->guard();
        $this->guard_hr_admin();

        $now  = date('Y-m-d H:i:s');
        $user = $this->session->userdata('username');

        // Get record FIRST (before update)
        $entitlement = $this->db
            ->where('id', $id)
            ->get('leave_entitlements')
            ->row();

        if (!$entitlement) {
            $this->session->set_flashdata('error', 'Entitlement request not found.');
            redirect('leave_balance_admin/privileges');
            return;
        }

        if ($entitlement->status !== 'PENDING') {
            $this->session->set_flashdata('error', 'This request has already been processed.');
            redirect('leave_balance_admin/privileges');
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | CAP VALIDATION
        |--------------------------------------------------------------------------
        */
        $caps = $this->get_entitlement_caps();
        $type = strtoupper($entitlement->leave_type_code);

        if (isset($caps[$type]) && $caps[$type] !== null) {

            if ((float)$entitlement->credits > (float)$caps[$type]) {

                $this->session->set_flashdata(
                    'error',
                    $type . ' exceeds allowed cap of ' . $caps[$type] . ' days.'
                );

                redirect('leave_balance_admin/privileges');
                return;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | APPROVE
        |--------------------------------------------------------------------------
        */
        $this->db->trans_start();

        $this->db->where('id', $id)->update('leave_entitlements', [
            'status'      => 'APPROVED',
            'approved_by' => $user,
            'approved_at' => $now,
            'action_by'   => $user,
            'action_at'   => $now
        ]);

        $ent = $this->db
            ->where('id', $id)
            ->get('leave_entitlements')
            ->row();

        if ($ent && strtoupper($ent->leave_type_code) === 'WELLNESS') {

            $staff_idnumber = $ent->staff_idnumber;
            $credits = (float)$ent->credits;

            // Make sure leave_balances row exists
            $balance = $this->db
                ->where('staff_idnumber', $staff_idnumber)
                ->get('leave_balances')
                ->row();

            if (!$balance) {
                $this->db->insert('leave_balances', array(
                    'staff_idnumber' => $staff_idnumber,
                    'wellness_balance' => $credits,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ));
            } else {
                $this->db->set('wellness_balance', 'COALESCE(wellness_balance, 0) + ' . $credits, false);
                $this->db->set('updated_at', date('Y-m-d H:i:s'));
                $this->db->where('staff_idnumber', $staff_idnumber);
                $this->db->update('leave_balances');
            }
        }



        // Audit
        $this->db->insert('leave_admin_audit', [
            'action'         => 'APPROVE ENTITLEMENT',
            'reference_id'   => $id,
            'staff_idnumber' => $entitlement->staff_idnumber,
            'performed_by'   => $user,
            'ip_address'     => $this->input->ip_address(),
            'hostname'       => gethostbyaddr($this->input->ip_address()),
            'user_agent'     => $this->input->user_agent(),
            'created_at'     => $now
        ]);

        if ($this->db->trans_status() === false) {

            $this->db->trans_rollback();

            $this->session->set_flashdata('error', 'Approval failed.');
            redirect('leave_balance_admin/privileges');
            return;
        }

        $this->db->trans_commit();

        $this->session->set_flashdata('success', 'Entitlement approved successfully.');
        redirect('leave_balance_admin/privileges');
    }

    public function deny_entitlement($id)
    {
        $this->guard();
        $this->guard_hr_admin();

        $now = date('Y-m-d H:i:s');
        $user = $this->session->userdata('username');

        $this->db->where('id', $id)->update('leave_entitlements', [
            'status' => 'DENIED',
            'approved_by' => $user,
            'approved_at' => $now
        ]);

        // Audit
        $this->db->insert('leave_admin_audit', [
            'action' => 'DENY ENTITLEMENT',
            'reference_id' => $id,
            'performed_by' => $user,
            'ip_address' => $this->input->ip_address(),
            'hostname' => gethostbyaddr($this->input->ip_address()),
            'user_agent' => $this->input->user_agent(),
            'created_at' => $now
        ]);

        redirect('leave_balance_admin/entitlements');
        $entitlement = $this->db
            ->where('id', $id)
            ->get('leave_entitlements')
            ->row();

        if (!$entitlement) {
            $this->session->set_flashdata('error', 'Entitlement request not found.');
            redirect('leave_balance_admin/privileges');
            return;
        }

        if ($entitlement->status !== 'PENDING') {
            $this->session->set_flashdata('error', 'This request has already been processed.');
            redirect('leave_balance_admin/privileges');
            return;
        }
    }

    private function guard_hr_admin()
    {
        if ($this->session->userdata('position') !== 'Human Resource Admin') {
            show_error('Unauthorized', 403);
            exit;
        }
    }

    public function run_monthly_credits()
    {
        $this->guard();
        $this->guard_hr_admin();

        $process_month = date('Y-m');
        $now = date('Y-m-d H:i:s');
        $user = $this->session->userdata('username');

        $rules = $this->db
            ->where('status', 'ACTIVE')
            ->get('leave_monthly_credit_rules')
            ->result();

        $processed = 0;
        $skipped = 0;

        foreach ($rules as $rule) {
            $exists = $this->db
                ->where('staff_idnumber', $rule->staff_idnumber)
                ->where('process_month', $process_month)
                ->get('leave_monthly_credit_logs')
                ->row();

            if ($exists) {
                $skipped++;
                continue;
            }

            $balance = $this->db
                ->where('staff_idnumber', $rule->staff_idnumber)
                ->get('leave_balances')
                ->row();

            if ($balance) {
                $this->db
                    ->set('vl_balance', 'vl_balance + ' . (float)$rule->vl_monthly, false)
                    ->set('sl_balance', 'sl_balance + ' . (float)$rule->sl_monthly, false)
                    ->set('updated_by', $user)
                    ->set('updated_at', $now)
                    ->where('staff_idnumber', $rule->staff_idnumber)
                    ->update('leave_balances');
            } else {
                $this->db->insert('leave_balances', array(
                    'staff_idnumber' => $rule->staff_idnumber,
                    'vl_balance' => (float)$rule->vl_monthly,
                    'sl_balance' => (float)$rule->sl_monthly,
                    'spl_balance' => 0,
                    'fl_balance' => 0,
                    'wellness_balance' => 0,
                    'coc_balance' => 0,
                    'updated_by' => $user,
                    'updated_at' => $now,
                    'created_at' => $now
                ));
            }

            $this->db->insert('leave_monthly_credit_logs', array(
                'staff_idnumber' => $rule->staff_idnumber,
                'process_month' => $process_month,
                'vl_added' => (float)$rule->vl_monthly,
                'sl_added' => (float)$rule->sl_monthly,
                'processed_by' => $user,
                'ip_address' => $this->input->ip_address(),
                'hostname' => gethostbyaddr($this->input->ip_address()),
                'user_agent' => $this->input->user_agent(),
                'created_at' => $now
            ));

            $this->db
                ->where('id', $rule->id)
                ->update('leave_monthly_credit_rules', array(
                    'last_processed_month' => $process_month,
                    'updated_at' => $now
                ));

            $processed++;
        }

        $this->session->set_flashdata(
            'success',
            'Monthly credits processed. Processed: ' . $processed . '. Skipped duplicate: ' . $skipped . '.'
        );

        redirect('leave_balance_admin/privileges');
    }

    public function process_entitlement_action()
    {
        $this->guard();
        $this->guard_sds_only();

        $id = (int)$this->input->post('id');
        $action = strtoupper(trim((string)$this->input->post('action')));
        $reason = trim((string)$this->input->post('action_reason'));

        if (!$id || !in_array($action, array('APPROVED', 'DENIED'), true) || $reason === '') {
            $this->session->set_flashdata('error', 'Invalid entitlement action or missing reason.');
            redirect('leave_balance_admin/privileges');
            return;
        }

        $entitlement = $this->db
            ->where('id', $id)
            ->get('leave_entitlements')
            ->row();

        if (!$entitlement) {
            $this->session->set_flashdata('error', 'Entitlement request not found.');
            redirect('leave_balance_admin/privileges');
            return;
        }

        if ($entitlement->status !== 'PENDING') {
            $this->session->set_flashdata('error', 'This request has already been processed.');
            redirect('leave_balance_admin/privileges');
            return;
        }

        if ($action === 'APPROVED') {
                $caps = $this->get_entitlement_caps();
                $leave_type = strtoupper($entitlement->leave_type_code);

                if (array_key_exists($leave_type, $caps) && $caps[$leave_type] !== null) {
                    $max_allowed = (float)$caps[$leave_type];
                    $requested = (float)$entitlement->credits;

                    if ($requested > $max_allowed) {
                        $this->session->set_flashdata(
                            'error',
                            'Cannot approve. ' . $leave_type . ' is capped at ' . $max_allowed . ' day(s). Requested: ' . $requested
                        );
                        redirect('leave_balance_admin/privileges');
                        return;
                    }
                }
            }

        $now = date('Y-m-d H:i:s');
        $user = $this->session->userdata('username');

        $this->db->trans_start();

        $this->db->where('id', $id)->update('leave_entitlements', array(
            'status'        => $action,
            'action_reason' => $reason,
            'action_by'     => $user,
            'action_at'     => $now,
            'approved_by'   => $user,
            'approved_at'   => $now
        ));

            if ($action === 'APPROVED') {

                $this->log_leave_ledger(array(
                    'staff_idnumber' => $entitlement->staff_idnumber,
                    'leave_type_code'=> $entitlement->leave_type_code,
                    'transaction_type'=> 'CREDIT',
                    'quantity'       => (float)$entitlement->credits,
                    'reference_table'=> 'leave_entitlements',
                    'reference_id'   => $id,
                    'remarks'        => 'Entitlement approved by HR'
                ));
            }



            if ($action === 'APPROVED') {

                $leave_type = strtoupper($entitlement->leave_type_code);
                $credits = (float)$entitlement->credits;

                $balance_field = null;

                switch ($leave_type) {
                    case 'PATERNITY':
                    case 'SOLO_PARENT':
                    case 'WELLNESS':
                    case 'PERSONAL':
                    case 'MATERNITY':
                    case 'VAWC':
                    case 'REHABILITATION':
                    case 'CALAMITY':
                        $balance_field = strtolower($leave_type) . '_balance';
                        break;
                }

                if ($balance_field) {

                    // Ensure column exists in leave_balances
                    $exists = $this->db
                        ->where('staff_idnumber', $entitlement->staff_idnumber)
                        ->get('leave_balances')
                        ->row();

                    if ($exists) {
                        $this->db->set($balance_field, "$balance_field + $credits", false)
                            ->set('updated_by', $user)
                            ->set('updated_at', $now)
                            ->where('staff_idnumber', $entitlement->staff_idnumber)
                            ->update('leave_balances');
                    } else {
                        // Create new balance row if missing
                        $this->db->insert('leave_balances', array(
                            'staff_idnumber' => $entitlement->staff_idnumber,
                            $balance_field => $credits,
                            'created_at' => $now,
                            'updated_at' => $now,
                            'updated_by' => $user
                        ));
                    }
                }
            }



        $this->db->insert('leave_admin_audit', array(
            'action'         => $action . ' ENTITLEMENT',
            'reference_id'   => $id,
            'staff_idnumber' => $entitlement->staff_idnumber,
            'performed_by'   => $user,
            'ip_address'     => $this->input->ip_address(),
            'hostname'       => gethostbyaddr($this->input->ip_address()),
            'user_agent'     => $this->input->user_agent(),
            'created_at'     => $now
        ));

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            $this->session->set_flashdata('error', 'Failed to process entitlement request.');
            redirect('leave_balance_admin/privileges');
            return;
        }

        $this->session->set_flashdata('success', 'Entitlement request ' . strtolower($action) . ' successfully.');
        redirect('leave_balance_admin/privileges');
    }

    private function get_entitlement_caps()
    {
        return array(
            'MATERNITY'      => 105,
            'PATERNITY'      => 7,
            'SOLO_PARENT'    => 7,
            'VAWC'           => 10,
            'WELLNESS'       => 5,
            'CALAMITY'       => 5,
            'PERSONAL'       => 3,

            // Manual review: no hard cap here because duration depends on supporting documents.
            'REHABILITATION' => null
        );
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

    public function ledger($staff_idnumber = null)
    {
        $this->guard();
        $this->guard_hr_admin();

        $staff_idnumber = trim((string)$staff_idnumber);

        if ($staff_idnumber === '') {
            $staff_idnumber = trim((string)$this->input->get('staff_idnumber'));
        }

        $data['staff_idnumber'] = $staff_idnumber;
        $data['employee'] = null;
        $data['balances'] = array();
        $data['ledger'] = array();

        if ($staff_idnumber !== '') {
            $data['employee'] = $this->db
                ->where('IDNumber', $staff_idnumber)
                ->get('hris_staff')
                ->row();

            $data['balances'] = $this->db
                ->where('staff_idnumber', $staff_idnumber)
                ->order_by('balance_year', 'DESC')
                ->get('leave_balances')
                ->result();

            $page  = (int)$this->input->get('page');
            $page  = $page > 0 ? $page : 1;

            $limit  = 5;
            $offset = ($page - 1) * $limit;

            $data['current_page'] = $page;

            $data['ledger_total'] = $this->db
                ->where('staff_idnumber', $staff_idnumber)
                ->count_all_results('leave_credit_ledger');

            $data['total_pages'] = ceil($data['ledger_total'] / $limit);

            $data['ledger'] = $this->db
                ->where('staff_idnumber', $staff_idnumber)
                ->order_by('created_at', 'DESC')
                ->order_by('id', 'DESC')
                ->limit($limit, $offset)
                ->get('leave_credit_ledger')
                ->result();
        }

        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('leave/ledger', $data);
        $this->load->view('templates/footer');
    }

    public function adjust_balance()
    {
        $this->guard();
        $this->guard_hr_admin();

        $staff_idnumber = trim((string)$this->input->post('staff_idnumber'));
        $balance_year   = (int)$this->input->post('balance_year');
        $leave_type     = strtoupper(trim((string)$this->input->post('leave_type_code')));
        $action         = strtoupper(trim((string)$this->input->post('action')));
        $quantity       = (float)$this->input->post('quantity');
        $remarks        = trim((string)$this->input->post('remarks'));

        if ($staff_idnumber === '' || $balance_year <= 0 || $leave_type === '' || $quantity < 0) {
            $this->session->set_flashdata('error', 'Please complete all adjustment fields.');
            redirect('leave_balance_admin');
            return;
        }

        $column_map = array(
            'VL'          => 'vl_balance',
            'SL'          => 'sl_balance',
            'SPL'         => 'spl_balance',
            'FL'          => 'forced_leave_balance',
            'SOLO_PARENT' => 'solo_parent_balance',
            'WELLNESS'    => 'wellness_balance',
            'COC'         => 'coc_balance_hours',
            'VSC'         => 'vsc_balance_days'
        );

        if (!isset($column_map[$leave_type])) {
            $this->session->set_flashdata('error', 'Invalid leave type selected.');
            redirect('leave_balance_admin');
            return;
        }

        if (!in_array($action, array('ADD', 'SUBTRACT', 'SET'), true)) {
            $this->session->set_flashdata('error', 'Invalid adjustment action.');
            redirect('leave_balance_admin');
            return;
        }

        $field = $column_map[$leave_type];
        $user  = $this->session->userdata('username');
        $now   = date('Y-m-d H:i:s');

        $this->db->trans_begin();

        $balance = $this->db
            ->where('staff_idnumber', $staff_idnumber)
            ->where('balance_year', $balance_year)
            ->get('leave_balances')
            ->row();

        if (!$balance) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('error', 'No leave balance record found for this employee/year.');
            redirect('leave_balance_admin');
            return;
        }

        $before = isset($balance->$field) ? (float)$balance->$field : 0;
        $after  = $before;

        if ($action === 'ADD') {
            $after = $before + $quantity;
            $transaction_type = 'ADD';
        } elseif ($action === 'SUBTRACT') {
            $after = $before - $quantity;
            $transaction_type = 'DEDUCT';
        } else {
            $after = $quantity;
            $transaction_type = 'ADJUST';
        }

        if ($after < 0) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('error', 'Balance cannot be negative.');
            redirect('leave_balance_admin');
            return;
        }

        $this->db
            ->where('id', $balance->id)
            ->update('leave_balances', array(
                $field       => $after,
                'updated_at' => $now,
                'updated_by' => $user
            ));

        $ledger_remarks = $action . ' ' . $leave_type . ' from ' . $before . ' to ' . $after;

        if ($remarks !== '') {
            $ledger_remarks .= '. Reason: ' . $remarks;
        }

        $this->db->insert('leave_credit_ledger', array(
            'staff_idnumber'   => $staff_idnumber,
            'balance_year'     => $balance_year,
            'leave_type_code'  => $leave_type,
            'transaction_type' => $transaction_type,
            'quantity'         => $quantity,
            'balance_before'   => $before,
            'balance_after'    => $after,
            'reference_table'  => 'leave_balances',
            'reference_id'     => $balance->id,
            'remarks'          => $ledger_remarks,
            'performed_by'     => $user,
            'created_at'       => $now,
            'ip_address'       => $this->input->ip_address(),
            'hostname'         => gethostbyaddr($this->input->ip_address()),
            'user_agent'       => $this->input->user_agent()
        ));

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('error', 'Adjustment failed.');
            redirect('leave_balance_admin');
            return;
        }

        $this->db->trans_commit();

        $this->session->set_flashdata('success', 'Leave balance adjusted successfully.');
        redirect('leave_balance_admin/ledger');
        return;
    }

    private function _insert_balance_import_ledger($staff_idnumber, $balance_year, $leave_type, $before, $after, $balance_id, $batch_id, $source_file)
    {
        $difference = (float)$after - (float)$before;

        if ($difference == 0) {
            return;
        }

        $transaction_type = $difference > 0 ? 'IMPORT_ADD' : 'IMPORT_DEDUCT';

        $this->db->insert('leave_credit_ledger', array(
            'staff_idnumber'   => $staff_idnumber,
            'leave_type_code'  => $leave_type,
            'transaction_type' => $transaction_type,
            'quantity'         => abs($difference),
            'reference_table'  => 'leave_balances',
            'reference_id'     => $balance_id,
            'remarks'          => 'Balance import from ' . $source_file . ' Batch ID: ' . $batch_id,
            'performed_by'     => $this->session->userdata('username'),
            'ip_address'       => $this->input->ip_address(),
            'hostname'         => gethostbyaddr($this->input->ip_address()),
            'created_at'       => date('Y-m-d H:i:s')
        ));
    }




}
