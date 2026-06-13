<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class System_settings extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper(array('url', 'form'));
        $this->load->library(array('session', 'upload'));
    }

    private function render_page($view, $data = array())
    {
        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view($view, $data);
        $this->load->view('templates/modal');
        $this->load->view('templates/footer');
    }

    private function get_biometric_upload_path()
    {
        return FCPATH . 'uploads/biometric_logs/';
    }

    private function ensure_biometric_upload_path()
    {
        $upload_path = $this->get_biometric_upload_path();

        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0777, true);
        }

        return $upload_path;
    }

    private function resolve_staff_idnumber_from_raw($employee_no = null, $id_number = null, $card_no = null, $employee_name = null)
    {
        $employee_no = trim((string)$employee_no);
        $id_number   = trim((string)$id_number);
        $card_no     = trim((string)$card_no);
        $employee_name = trim((string)$employee_name);

        if ($id_number !== '') {
            $staff = $this->db
                ->select('IDNumber')
                ->from('hris_staff')
                ->where('IDNumber', $id_number)
                ->limit(1)
                ->get()
                ->row();

            if ($staff) {
                return $staff->IDNumber;
            }
        }

        if ($employee_no !== '') {
            $staff = $this->db
                ->select('IDNumber')
                ->from('hris_staff')
                ->where('employeeNo', $employee_no)
                ->limit(1)
                ->get()
                ->row();

            if ($staff) {
                return $staff->IDNumber;
            }
        }

        $this->db->select('staff_idnumber');
        $this->db->from('biometric_employee_map');
        $this->db->where('is_active', 1);

        $has_identifier = false;
        $this->db->group_start();

        if ($employee_no !== '') {
            $this->db->or_where('biometric_no', $employee_no);
            $has_identifier = true;
        }

        if ($id_number !== '') {
            $this->db->or_where('biometric_id_number', $id_number);
            $has_identifier = true;
        }

        if ($card_no !== '') {
            $this->db->or_where('biometric_card_no', $card_no);
            $has_identifier = true;
        }

        if ($employee_name !== '') {
            $this->db->or_where('biometric_name', $employee_name);
            $has_identifier = true;
        }

        $this->db->group_end();

        if ($has_identifier) {
            $mapped = $this->db->limit(1)->get()->row();
            if ($mapped && !empty($mapped->staff_idnumber)) {
                return $mapped->staff_idnumber;
            }
        }

        return null;
    }

    private function random_time_only($start_time, $end_time)
    {
        $start = strtotime($start_time);
        $end = strtotime($end_time);

        if ($start === false || $end === false || $end < $start) {
            return null;
        }

        return date('H:i:s', mt_rand($start, $end));
    }

    private function random_time_for_slot($slot)
    {
        switch ($slot) {
            case 'morning_in':
                return $this->random_time_only('07:00:00', '07:30:00');
            case 'morning_out':
                return $this->random_time_only('12:00:00', '12:30:00');
            case 'afternoon_in':
                return $this->random_time_only('12:30:00', '13:00:00');
            case 'afternoon_out':
                return $this->random_time_only('17:00:00', '19:00:00');
            default:
                return null;
        }
    }

    private function ensure_manual_upload_id()
    {
        $existing = $this->db->select('id')
            ->from('biometric_uploads')
            ->where('original_filename', 'MANUAL_ENTRY')
            ->where('stored_filename', 'MANUAL_ENTRY')
            ->limit(1)
            ->get()
            ->row();

        if ($existing && !empty($existing->id)) {
            return (int)$existing->id;
        }

        $this->db->insert('biometric_uploads', array(
            'original_filename' => 'MANUAL_ENTRY',
            'stored_filename'   => 'MANUAL_ENTRY',
            'file_path'         => 'manual',
            'file_size_kb'      => 0,
            'uploaded_at'       => date('Y-m-d H:i:s')
        ));

        return (int)$this->db->insert_id();
    }



    private function is_friday_or_weekend($date)
    {
        $timestamp = strtotime($date);
        if ($timestamp === false) {
            return true;
        }

        $day_num = (int)date('N', $timestamp); // 1=Mon ... 6=Sat 7=Sun
        return in_array($day_num, array(6, 7), true); // ONLY Sat & Sun
    }

    private function has_travel_on_date($staff_idnumber, $date)
    {
        $travel_requests = $this->db->query("
            SELECT id, IDNumber, purpose, status, inclusive_date, date_start, date_end
            FROM travel_requests
            WHERE IDNumber = ?
              AND status = 'Approved'
        ", array($staff_idnumber))->result_array();

        if (empty($travel_requests)) {
            return false;
        }

        foreach ($travel_requests as $travel) {
            $covered_dates = $this->extract_travel_dates_from_row($travel);
            if (in_array($date, $covered_dates, true)) {
                return true;
            }
        }

        return false;
    }

    private function has_holiday_on_date($staff_idnumber, $date)
    {
        $holiday = $this->db->query("
            SELECT id
            FROM biometric_day_events
            WHERE event_date = ?
              AND (
                    staff_idnumber = ?
                    OR staff_idnumber = 'ALL'
                  )
              AND (
                    LOWER(COALESCE(event_type, '')) LIKE '%holiday%'
                    OR LOWER(COALESCE(event_title, '')) LIKE '%holiday%'
                  )
            LIMIT 1
        ", array($date, $staff_idnumber))->row();

        return !empty($holiday);
    }

    private function should_skip_generated_log_date($staff_idnumber, $date)
    {
        if ($this->is_friday_or_weekend($date)) {
            return true;
        }

        if ($this->has_travel_on_date($staff_idnumber, $date)) {
            return true;
        }

        if ($this->has_holiday_on_date($staff_idnumber, $date)) {
            return true;
        }

        if ($this->has_day_event_on_date($staff_idnumber, $date)) {
            return true;
        }

        return false;
    }

    private function get_day_slot_values_from_raw_logs($staff_idnumber, $date)
    {
        $logs = $this->db->query("
            SELECT TIME(log_datetime) AS log_time
            FROM biometric_raw_logs
            WHERE mapped_staff_idnumber = ?
              AND DATE(log_datetime) = ?
              AND match_status = 'matched'
              AND log_datetime IS NOT NULL
            ORDER BY log_datetime ASC
        ", array($staff_idnumber, $date))->result();

        $morning_in_candidates = array();
        $morning_out_candidates = array();
        $afternoon_in_candidates = array();
        $afternoon_out_candidates = array();

        foreach ($logs as $log) {
            $time = $log->log_time;

            if ($time >= '04:00:00' && $time <= '11:59:59') {
                $morning_in_candidates[] = $time;
            }

            if ($time >= '12:00:00' && $time <= '12:59:59') {
                $morning_out_candidates[] = $time;
            }

            if ($time >= '12:01:00' && $time <= '14:59:59') {
                $afternoon_in_candidates[] = $time;
            }

            if ($time >= '15:00:00' && $time <= '23:59:59') {
                $afternoon_out_candidates[] = $time;
            }
        }

        $morning_in = !empty($morning_in_candidates) ? min($morning_in_candidates) : null;
        $morning_out = !empty($morning_out_candidates) ? max($morning_out_candidates) : null;
        $afternoon_in = !empty($afternoon_in_candidates) ? min($afternoon_in_candidates) : null;
        $afternoon_out = !empty($afternoon_out_candidates) ? max($afternoon_out_candidates) : null;

        if (!empty($morning_out) && !empty($afternoon_in) && $morning_out > $afternoon_in) {
            $earlier = min($morning_out, $afternoon_in);
            $later = max($morning_out, $afternoon_in);
            $morning_out = $earlier;
            $afternoon_in = $later;
        }

        if (!empty($afternoon_in) && !empty($afternoon_out) && $afternoon_in > $afternoon_out) {
            $afternoon_in = null;
        }

        if (!empty($morning_out) && !empty($afternoon_in) && $morning_out > $afternoon_in) {
            $morning_out = null;
        }

        return array(
            'morning_in' => $morning_in,
            'morning_out' => $morning_out,
            'afternoon_in' => $afternoon_in,
            'afternoon_out' => $afternoon_out
        );
    }

    private function rebuild_dtr_from_raw_logs_for_date($staff_idnumber, $date)
    {
        $slots = $this->get_day_slot_values_from_raw_logs($staff_idnumber, $date);

        $existing = $this->db->where('staff_idnumber', $staff_idnumber)
            ->where('dtr_date', $date)
            ->get('biometric_dtr')
            ->row();

        $has_any = !empty($slots['morning_in']) || !empty($slots['morning_out']) || !empty($slots['afternoon_in']) || !empty($slots['afternoon_out']);

        $save_data = array(
            'staff_idnumber'   => $staff_idnumber,
            'dtr_date'         => $date,
            'morning_in'       => $slots['morning_in'],
            'morning_out'      => $slots['morning_out'],
            'afternoon_in'     => $slots['afternoon_in'],
            'afternoon_out'    => $slots['afternoon_out'],
            'source_upload_id' => $this->ensure_manual_upload_id(),
            'updated_at'       => date('Y-m-d H:i:s')
        );

        if ($existing) {
            if ($has_any) {
                $this->db->where('id', $existing->id)->update('biometric_dtr', $save_data);
            } else {
                $this->db->where('id', $existing->id)->delete('biometric_dtr');
            }
        } elseif ($has_any) {
            $save_data['created_at'] = date('Y-m-d H:i:s');
            $this->db->insert('biometric_dtr', $save_data);
        }

        return $slots;
    }

    private function insert_manual_raw_log_for_staff($staff, $log_datetime, $remarks = 'Manual entry', $location_id = 'MANUAL')
    {
        $duplicate = $this->db->query("
            SELECT id
            FROM biometric_raw_logs
            WHERE mapped_staff_idnumber = ?
            AND log_datetime = ?
            AND match_status = 'matched'
            LIMIT 1
        ", array($staff->IDNumber, $log_datetime))->row();

        if ($duplicate) {
            return false;
        }

        $insert_data = array(
            'upload_id'             => $this->ensure_manual_upload_id(),
            'mapped_staff_idnumber' => $staff->IDNumber,
            'match_status'          => 'matched',
            'department'            => isset($staff->Department) ? $staff->Department : null,
            'employee_name'         => trim($staff->LastName . ', ' . $staff->FirstName . ' ' . $staff->MiddleName),
            'employee_no'           => isset($staff->employeeNo) ? $staff->employeeNo : null,
            'log_datetime'          => $log_datetime,
            'location_id'           => $location_id,
            'id_number'             => $staff->IDNumber,
            'verify_code'           => 'MANUAL',
            'card_no'               => $remarks !== '' ? $remarks : 'Manual entry',
            'created_at'            => date('Y-m-d H:i:s')
        );

        $this->db->insert('biometric_raw_logs', $insert_data);
        return $this->db->affected_rows() > 0;
    }


    public function upload_biometric_logs()
    {
        $page = 'upload_biometric_logs';

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $data['title'] = 'Upload Biometric Logs';
        $this->render_page('pages/' . $page, $data);
    }


    private function has_day_event_on_date($staff_idnumber, $date)
    {
        $event = $this->db->query("
            SELECT id, event_type, event_title
            FROM biometric_day_events
            WHERE event_date = ?
            AND (
                    staff_idnumber = ?
                    OR staff_idnumber = 'ALL'
                )
            LIMIT 1
        ", array($date, $staff_idnumber))->row();

        return !empty($event);
    }


    public function test_upload_path()
    {
        $upload_path = $this->get_biometric_upload_path();

        echo '<pre>';
        echo 'FCPATH: ' . FCPATH . PHP_EOL;
        echo 'UPLOAD PATH: ' . $upload_path . PHP_EOL;
        echo 'EXISTS: ' . (is_dir($upload_path) ? 'YES' : 'NO') . PHP_EOL;
        echo 'WRITABLE: ' . (is_writable($upload_path) ? 'YES' : 'NO') . PHP_EOL;
        echo '</pre>';
    }
    private function biometric_raw_log_exists($mapped_staff_idnumber, $employee_no, $id_number, $card_no, $log_datetime)
    {
        if (empty($log_datetime)) {
            return false;
        }

        $this->db->from('biometric_raw_logs');
        $this->db->where('log_datetime', $log_datetime);

        $has_identifier = false;
        $this->db->group_start();

        if (!empty($mapped_staff_idnumber)) {
            $this->db->or_where('mapped_staff_idnumber', $mapped_staff_idnumber);
            $has_identifier = true;
        }

        if (!empty($employee_no)) {
            $this->db->or_where('employee_no', $employee_no);
            $has_identifier = true;
        }

        if (!empty($id_number)) {
            $this->db->or_where('id_number', $id_number);
            $has_identifier = true;
        }

        if (!empty($card_no)) {
            $this->db->or_where('card_no', $card_no);
            $has_identifier = true;
        }

        $this->db->group_end();

        if (!$has_identifier) {
            return false;
        }

        return $this->db->count_all_results() > 0;
    }
  public function upload_biometric_logs_submit()
    {
        $upload_path = $this->ensure_biometric_upload_path();

        if (empty($_FILES['biometric_file']['name'])) {
            $this->session->set_flashdata('danger', 'Please select a file to upload.');
            redirect(base_url('System_settings/upload_biometric_logs'));
            return;
        }

        $original_name = $_FILES['biometric_file']['name'];
        $ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));

        if ($ext !== 'csv') {
            $this->session->set_flashdata('danger', 'Only CSV files are allowed.');
            redirect(base_url('System_settings/upload_biometric_logs'));
            return;
        }

        $config['upload_path']      = $upload_path;
        $config['allowed_types']    = '*';
        $config['max_size']         = 51200;
        $config['encrypt_name']     = true;
        $config['file_ext_tolower'] = true;
        $config['remove_spaces']    = false;
        $config['detect_mime']      = false;
        $config['mod_mime_fix']     = false;

        $this->upload->initialize($config);

        if (!$this->upload->do_upload('biometric_file')) {
            $this->session->set_flashdata('danger', $this->upload->display_errors('', ''));
            redirect(base_url('System_settings/upload_biometric_logs'));
            return;
        }

        $fileData = $this->upload->data();

        $insertUpload = array(
            'original_filename' => $fileData['client_name'],
            'stored_filename'   => $fileData['file_name'],
            'file_path'         => 'uploads/biometric_logs/' . $fileData['file_name'],
            'file_size_kb'      => $fileData['file_size'],
            'uploaded_at'       => date('Y-m-d H:i:s')
        );

        $this->db->insert('biometric_uploads', $insertUpload);
        $upload_id = $this->db->insert_id();

        $full_path = $upload_path . $fileData['file_name'];

        if (($handle = fopen($full_path, 'r')) === false) {
            $this->session->set_flashdata('danger', 'Unable to read the uploaded CSV file.');
            redirect(base_url('System_settings/upload_biometric_logs'));
            return;
        }

        $row_index = 0;
        $insertRows = array();
        $matched_count = 0;
        $unmapped_count = 0;
        $duplicate_count = 0;

        // for duplicate checking inside the same uploaded file
        $seen_rows = array();

        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
            if ($row_index === 0) {
                $row_index++;
                continue;
            }

            $department  = isset($row[0]) ? trim($row[0]) : null;
            $name        = isset($row[1]) ? trim($row[1]) : null;
            $employee_no = isset($row[2]) ? trim($row[2]) : null;
            $datetime    = isset($row[3]) ? trim($row[3]) : null;
            $location_id = isset($row[4]) ? trim($row[4]) : null;
            $id_number   = isset($row[5]) ? trim($row[5]) : null;
            $verify_code = isset($row[6]) ? trim($row[6]) : null;
            $card_no     = isset($row[7]) ? trim($row[7]) : null;

            if ($employee_no === '' && $datetime === '' && $name === '') {
                $row_index++;
                continue;
            }

            $log_datetime = null;
            if ($datetime !== '') {
                $timestamp = strtotime($datetime);
                if ($timestamp !== false) {
                    $log_datetime = date('Y-m-d H:i:s', $timestamp);
                }
            }

            $mapped_staff_idnumber = $this->resolve_staff_idnumber_from_raw($employee_no, $id_number, $card_no, $name);
            $match_status = !empty($mapped_staff_idnumber) ? 'matched' : 'unmapped';

            // duplicate key within the same CSV
            $duplicate_key = implode('|', array(
                (string)$mapped_staff_idnumber,
                (string)$employee_no,
                (string)$id_number,
                (string)$card_no,
                (string)$log_datetime
            ));

            if (isset($seen_rows[$duplicate_key])) {
                $duplicate_count++;
                $row_index++;
                continue;
            }

            $seen_rows[$duplicate_key] = true;

            // duplicate check against existing database records
            if ($this->biometric_raw_log_exists($mapped_staff_idnumber, $employee_no, $id_number, $card_no, $log_datetime)) {
                $duplicate_count++;
                $row_index++;
                continue;
            }

            if ($match_status === 'matched') {
                $matched_count++;
            } else {
                $unmapped_count++;
            }

            $insertRows[] = array(
                'upload_id'             => $upload_id,
                'mapped_staff_idnumber' => $mapped_staff_idnumber,
                'match_status'          => $match_status,
                'department'            => $department,
                'employee_name'         => $name,
                'employee_no'           => $employee_no,
                'log_datetime'          => $log_datetime,
                'location_id'           => $location_id,
                'id_number'             => $id_number,
                'verify_code'           => $verify_code,
                'card_no'               => $card_no,
                'created_at'            => date('Y-m-d H:i:s')
            );

            $row_index++;
        }

        fclose($handle);

        if (!empty($insertRows)) {
            $this->db->insert_batch('biometric_raw_logs', $insertRows);
        }

        $this->session->set_flashdata(
            'success',
            'CSV file uploaded successfully. Matched: ' . $matched_count .
            ' | Unmapped: ' . $unmapped_count .
            ' | Duplicates skipped: ' . $duplicate_count
        );

        redirect(base_url('System_settings/upload_biometric_logs'));
    }

    public function biometric_employee_mapping()
    {
        $data['title'] = 'Biometric Employee Mapping';

        $data['unmapped_logs'] = $this->db->query("
            SELECT 
                employee_no,
                id_number,
                employee_name,
                card_no,
                department,
                COUNT(*) AS total_logs
            FROM biometric_raw_logs
            WHERE match_status = 'unmapped'
            GROUP BY employee_no, id_number, employee_name, card_no, department
            ORDER BY employee_name ASC
        ")->result();

        $data['staff_list'] = $this->db->query("
            SELECT * 
            FROM hris_staff 
            WHERE d_id = 18
            ORDER BY LastName ASC, FirstName ASC
        ")->result();

        $this->render_page('pages/biometric_employee_mapping', $data);
    }

    public function save_biometric_employee_mapping()
    {
        $staff_idnumber = trim($this->input->post('staff_idnumber'));
        $biometric_no = trim($this->input->post('biometric_no'));
        $biometric_id_number = trim($this->input->post('biometric_id_number'));
        $biometric_card_no = trim($this->input->post('biometric_card_no'));
        $biometric_name = trim($this->input->post('biometric_name'));

        if ($staff_idnumber === '') {
            $this->session->set_flashdata('danger', 'Please select a staff member.');
            redirect(base_url('System_settings/biometric_employee_mapping'));
            return;
        }

        $existing = $this->db->where('staff_idnumber', $staff_idnumber)
            ->get('biometric_employee_map')
            ->row();

        $mapping_data = array(
            'staff_idnumber'       => $staff_idnumber,
            'biometric_no'         => $biometric_no !== '' ? $biometric_no : null,
            'biometric_id_number'  => $biometric_id_number !== '' ? $biometric_id_number : null,
            'biometric_card_no'    => $biometric_card_no !== '' ? $biometric_card_no : null,
            'biometric_name'       => $biometric_name !== '' ? $biometric_name : null,
            'is_active'            => 1,
            'updated_at'           => date('Y-m-d H:i:s')
        );

        if ($existing) {
            $this->db->where('id', $existing->id)->update('biometric_employee_map', $mapping_data);
        } else {
            $mapping_data['created_at'] = date('Y-m-d H:i:s');
            $this->db->insert('biometric_employee_map', $mapping_data);
        }

        $this->db->set('mapped_staff_idnumber', $staff_idnumber);
        $this->db->set('match_status', 'matched');

        $has_identifier = false;
        $this->db->group_start();

        if ($biometric_id_number !== '') {
            $this->db->or_where('id_number', $biometric_id_number);
            $has_identifier = true;
        }

        if ($biometric_no !== '') {
            $this->db->or_where('employee_no', $biometric_no);
            $has_identifier = true;
        }

        if ($biometric_card_no !== '') {
            $this->db->or_where('card_no', $biometric_card_no);
            $has_identifier = true;
        }

        if ($biometric_name !== '') {
            $this->db->or_where('employee_name', $biometric_name);
            $has_identifier = true;
        }

        $this->db->group_end();

        if ($has_identifier) {
            $this->db->update('biometric_raw_logs');
        }

        $this->session->set_flashdata('success', 'Biometric mapping saved successfully.');
        redirect(base_url('System_settings/biometric_employee_mapping'));
    }

    public function process_biometric_dtr()
    {
        $data['title'] = 'Process Biometric DTR';
        $data['uploads'] = $this->db->order_by('id', 'DESC')->get('biometric_uploads')->result();

        $this->render_page('pages/process_biometric_dtr', $data);
    }

    public function run_biometric_dtr_process()
    {
        $upload_id = (int)$this->input->post('upload_id');

        if (empty($upload_id)) {
            $this->session->set_flashdata('danger', 'Please select an uploaded biometric file.');
            redirect(base_url('System_settings/process_biometric_dtr'));
            return;
        }

        $this->db->where('source_upload_id', $upload_id)->delete('biometric_dtr');

        $logs = $this->db->query("
            SELECT 
                mapped_staff_idnumber,
                DATE(log_datetime) AS log_date,
                TIME(log_datetime) AS log_time
            FROM biometric_raw_logs
            WHERE upload_id = ?
              AND match_status = 'matched'
              AND mapped_staff_idnumber IS NOT NULL
              AND log_datetime IS NOT NULL
            ORDER BY mapped_staff_idnumber, log_datetime ASC
        ", array($upload_id))->result();

        if (empty($logs)) {
            $this->session->set_flashdata('danger', 'No matched biometric logs found for the selected upload.');
            redirect(base_url('System_settings/process_biometric_dtr'));
            return;
        }

        $grouped = array();

        foreach ($logs as $log) {
            $staff = $log->mapped_staff_idnumber;
            $date = $log->log_date;
            $time = $log->log_time;

            if (!isset($grouped[$staff])) {
                $grouped[$staff] = array();
            }

            if (!isset($grouped[$staff][$date])) {
                $grouped[$staff][$date] = array(
                    'morning_in_candidates' => array(),
                    'morning_out_candidates' => array(),
                    'afternoon_in_candidates' => array(),
                    'afternoon_out_candidates' => array(),
                );
            }

            if ($time >= '04:00:00' && $time <= '11:59:59') {
                $grouped[$staff][$date]['morning_in_candidates'][] = $time;
            }

            if ($time >= '12:00:00' && $time <= '12:59:59') {
                $grouped[$staff][$date]['morning_out_candidates'][] = $time;
            }

            if ($time >= '12:01:00' && $time <= '14:59:59') {
                $grouped[$staff][$date]['afternoon_in_candidates'][] = $time;
            }

            if ($time >= '15:00:00' && $time <= '23:59:59') {
                $grouped[$staff][$date]['afternoon_out_candidates'][] = $time;
            }
        }

        $processed_count = 0;

        foreach ($grouped as $staff_idnumber => $dates) {
            foreach ($dates as $dtr_date => $times) {
                $morning_in = !empty($times['morning_in_candidates']) ? min($times['morning_in_candidates']) : null;
                $morning_out = !empty($times['morning_out_candidates']) ? max($times['morning_out_candidates']) : null;
                $afternoon_in = !empty($times['afternoon_in_candidates']) ? min($times['afternoon_in_candidates']) : null;
                $afternoon_out = !empty($times['afternoon_out_candidates']) ? max($times['afternoon_out_candidates']) : null;

                if (!empty($morning_out) && !empty($afternoon_in) && $morning_out > $afternoon_in) {
                    $earlier = min($morning_out, $afternoon_in);
                    $later = max($morning_out, $afternoon_in);
                    $morning_out = $earlier;
                    $afternoon_in = $later;
                }

                if (!empty($afternoon_in) && !empty($afternoon_out) && $afternoon_in > $afternoon_out) {
                    $afternoon_in = null;
                }

                if (!empty($morning_out) && !empty($afternoon_in) && $morning_out > $afternoon_in) {
                    $morning_out = null;
                }

                $existing = $this->db->where('staff_idnumber', $staff_idnumber)
                    ->where('dtr_date', $dtr_date)
                    ->get('biometric_dtr')
                    ->row();

                $save_data = array(
                    'staff_idnumber' => $staff_idnumber,
                    'dtr_date' => $dtr_date,
                    'morning_in' => $morning_in,
                    'morning_out' => $morning_out,
                    'afternoon_in' => $afternoon_in,
                    'afternoon_out' => $afternoon_out,
                    'source_upload_id' => $upload_id,
                    'updated_at' => date('Y-m-d H:i:s')
                );

                if ($existing) {
                    $this->db->where('id', $existing->id)->update('biometric_dtr', $save_data);
                } else {
                    $save_data['created_at'] = date('Y-m-d H:i:s');
                    $this->db->insert('biometric_dtr', $save_data);
                }

                $processed_count++;
            }
        }

        $this->session->set_flashdata('success', 'DTR processing completed. Total DTR rows processed: ' . $processed_count);
        redirect(base_url('System_settings/process_biometric_dtr'));
    }

    public function biometric_dtr_viewer($staff_idnumber = null, $year = null, $month = null)
    {
        $data['title'] = 'Biometric DTR Viewer';

        $year = $year ?: date('Y');
        $month = $month ?: date('n');

        $data['selected_staff_idnumber'] = $staff_idnumber;
        $data['selected_year'] = (int)$year;
        $data['selected_month'] = (int)$month;

        $data['staff_list'] = $this->db->query("
            SELECT IDNumber, employeeNo, FirstName, MiddleName, LastName, Department
            FROM hris_staff
            WHERE d_id = 18
            ORDER BY LastName ASC, FirstName ASC
        ")->result();

        $data['staff_info'] = null;
        $data['dtr_rows'] = array();

        if (!empty($staff_idnumber)) {
            $data['staff_info'] = $this->db
                ->where('IDNumber', $staff_idnumber)
                ->where('d_id', 18)
                ->get('hris_staff')
                ->row();

            $days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);
            $rows = array();

            $existing_dtr = $this->db->query("
                SELECT dtr_date, morning_in, morning_out, afternoon_in, afternoon_out
                FROM biometric_dtr
                WHERE staff_idnumber = ?
                AND YEAR(dtr_date) = ?
                AND MONTH(dtr_date) = ?
            ", array($staff_idnumber, $year, $month))->result_array();

            $indexed = array();
            foreach ($existing_dtr as $row) {
                $indexed[$row['dtr_date']] = $row;
            }

            $event_rows = $this->db->query("
                SELECT event_date, event_type, event_title, remarks
                FROM biometric_day_events
                WHERE staff_idnumber = ?
                AND YEAR(event_date) = ?
                AND MONTH(event_date) = ?
            ", array($staff_idnumber, $year, $month))->result_array();

            $event_index = array();
            foreach ($event_rows as $ev) {
                $event_index[$ev['event_date']] = $ev;
            }

            $travel_requests = $this->db->query("
                SELECT id, IDNumber, purpose, status, inclusive_date, date_start, date_end
                FROM travel_requests
                WHERE IDNumber = ?
                AND status = 'Approved'
                ORDER BY id ASC
            ", array($staff_idnumber))->result_array();

            $travel_map = $this->build_travel_map_for_month($travel_requests, $year, $month);

            for ($day = 1; $day <= $days_in_month; $day++) {
                $date = sprintf('%04d-%02d-%02d', $year, $month, $day);
                $day_num = (int)date('N', strtotime($date));
                $is_weekend = in_array($day_num, array(6, 7), true);
                $event = isset($event_index[$date]) ? $event_index[$date] : null;
                $travel = isset($travel_map[$date]) ? $travel_map[$date] : null;

                $rows[] = array(
                    'date' => $date,
                    'day_name' => date('D', strtotime($date)),
                    'is_weekend' => $is_weekend,
                    'morning_in' => isset($indexed[$date]) ? $indexed[$date]['morning_in'] : null,
                    'morning_out' => isset($indexed[$date]) ? $indexed[$date]['morning_out'] : null,
                    'afternoon_in' => isset($indexed[$date]) ? $indexed[$date]['afternoon_in'] : null,
                    'afternoon_out' => isset($indexed[$date]) ? $indexed[$date]['afternoon_out'] : null,
                    'event' => $event,
                    'travel' => $travel,
                );
            }

            $data['dtr_rows'] = $rows;
        }

        $this->render_page('pages/biometric_dtr_viewer', $data);
    }

    public function my_dtr($year = null, $month = null)
    {
        $data['title'] = 'My DTR';

        $year = $year ?: date('Y');
        $month = $month ?: date('n');

        $staff_idnumber = $this->session->userdata('username');

        if (empty($staff_idnumber)) {
            show_error('Unable to determine logged-in employee.');
            return;
        }

        $staff_info = $this->db->where('IDNumber', $staff_idnumber)->get('hris_staff')->row();

        if (!$staff_info) {
            show_error('Employee record not found.');
            return;
        }

        $days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        $existing_dtr = $this->db->query("
            SELECT dtr_date, morning_in, morning_out, afternoon_in, afternoon_out
            FROM biometric_dtr
            WHERE staff_idnumber = ?
            AND YEAR(dtr_date) = ?
            AND MONTH(dtr_date) = ?
        ", array($staff_idnumber, $year, $month))->result_array();

        $indexed = array();
        foreach ($existing_dtr as $row) {
            $indexed[$row['dtr_date']] = $row;
        }

        $event_rows = $this->db->query("
            SELECT event_date, event_type, event_title, remarks
            FROM biometric_day_events
            WHERE staff_idnumber = ?
            AND YEAR(event_date) = ?
            AND MONTH(event_date) = ?
        ", array($staff_idnumber, $year, $month))->result_array();

        $event_index = array();
        foreach ($event_rows as $ev) {
            $event_index[$ev['event_date']] = $ev;
        }

        $rows = array();
        for ($day = 1; $day <= $days_in_month; $day++) {
            $date = sprintf('%04d-%02d-%02d', $year, $month, $day);

            $rows[] = array(
                'date' => $date,
                'day_name' => date('D', strtotime($date)),
                'morning_in' => isset($indexed[$date]) ? $indexed[$date]['morning_in'] : null,
                'morning_out' => isset($indexed[$date]) ? $indexed[$date]['morning_out'] : null,
                'afternoon_in' => isset($indexed[$date]) ? $indexed[$date]['afternoon_in'] : null,
                'afternoon_out' => isset($indexed[$date]) ? $indexed[$date]['afternoon_out'] : null,
                'morning_status' => null,
                'afternoon_status' => null,
                'remarks' => isset($event_index[$date]) ? $event_index[$date]['remarks'] : null,
                'event' => isset($event_index[$date]) ? $event_index[$date] : null,
            );
        }

        $data['staff_info'] = $staff_info;
        $data['dtr_rows'] = $rows;
        $data['selected_year'] = (int)$year;
        $data['selected_month'] = (int)$month;

        $this->render_page('pages/my_dtr', $data);
    }


    public function print_my_dtr($year = null, $month = null)
    {
        $year = $year ?: date('Y');
        $month = $month ?: date('n');

        $staff_idnumber = $this->session->userdata('username');

        if (empty($staff_idnumber)) {
            show_error('Unable to determine logged-in employee.');
            return;
        }

        $staff_info = $this->db->where('IDNumber', $staff_idnumber)->get('hris_staff')->row();

        if (!$staff_info) {
            show_error('Employee record not found.');
            return;
        }

        $immediate_supervisor = trim($this->input->post('immediate_supervisor'));
        $immediate_supervisor_position = trim($this->input->post('immediate_supervisor_position'));

        if ($immediate_supervisor === '') {
            $immediate_supervisor = '____________________________';
        }

        if ($immediate_supervisor_position === '') {
            $immediate_supervisor_position = 'Immediate Supervisor';
        }

        $days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        $existing_dtr = $this->db->query("
            SELECT dtr_date, morning_in, morning_out, afternoon_in, afternoon_out
            FROM biometric_dtr
            WHERE staff_idnumber = ?
            AND YEAR(dtr_date) = ?
            AND MONTH(dtr_date) = ?
        ", array($staff_idnumber, $year, $month))->result_array();

        $indexed = array();
        foreach ($existing_dtr as $row) {
            $indexed[$row['dtr_date']] = $row;
        }

        $event_rows = $this->db->query("
            SELECT event_date, event_type, event_title, remarks
            FROM biometric_day_events
            WHERE staff_idnumber = ?
            AND YEAR(event_date) = ?
            AND MONTH(event_date) = ?
        ", array($staff_idnumber, $year, $month))->result_array();

        $event_index = array();
        foreach ($event_rows as $ev) {
            $event_index[$ev['event_date']] = $ev;
        }

        $travel_requests = $this->db->query("
            SELECT id, IDNumber, purpose, status, inclusive_date, date_start, date_end
            FROM travel_requests
            WHERE IDNumber = ?
            AND status = 'Approved'
            ORDER BY id ASC
        ", array($staff_idnumber))->result_array();

        $travel_map = $this->build_travel_map_for_month($travel_requests, $year, $month);

        $rows = array();
        $hash_source = $staff_idnumber . '|' . $year . '|' . $month . '|' . $immediate_supervisor . '|' . $immediate_supervisor_position;

        for ($day = 1; $day <= 31; $day++) {
            if ($day <= $days_in_month) {
                $date = sprintf('%04d-%02d-%02d', $year, $month, $day);

                $morning_in = isset($indexed[$date]) ? $indexed[$date]['morning_in'] : null;
                $morning_out = isset($indexed[$date]) ? $indexed[$date]['morning_out'] : null;
                $afternoon_in = isset($indexed[$date]) ? $indexed[$date]['afternoon_in'] : null;
                $afternoon_out = isset($indexed[$date]) ? $indexed[$date]['afternoon_out'] : null;
                $travel_info = isset($travel_map[$date]) ? $travel_map[$date] : null;
                $event_info = isset($event_index[$date]) ? $event_index[$date] : null;
                $day_of_week = date('w', strtotime($date));
                $remarks = isset($event_index[$date]) ? $event_index[$date]['remarks'] : null;
            } else {
                $date = null;
                $morning_in = null;
                $morning_out = null;
                $afternoon_in = null;
                $afternoon_out = null;
                $travel_info = null;
                $event_info = null;
                $day_of_week = null;
                $remarks = null;
            }

            $rows[] = array(
                'date' => $date,
                'day' => $day,
                'day_of_week' => $day_of_week,
                'morning_in' => $morning_in,
                'morning_out' => $morning_out,
                'afternoon_in' => $afternoon_in,
                'afternoon_out' => $afternoon_out,
                'travel' => $travel_info,
                'morning_status' => null,
                'afternoon_status' => null,
                'remarks' => $remarks,
                'event' => $event_info
            );

            $hash_source .= '|'
                . $day . '|'
                . $date . '|'
                . $morning_in . '|'
                . $morning_out . '|'
                . $afternoon_in . '|'
                . $afternoon_out . '|'
                . ($travel_info ? $travel_info['title'] : '')
                . ($event_info ? (!empty($event_info['event_title']) ? $event_info['event_title'] : $event_info['event_type']) : '');
        }

        $generated_at = date('Y-m-d H:i:s');
        $verification_token = hash('sha256', $hash_source . '|' . $generated_at . '|' . mt_rand());

        $this->db->insert('biometric_dtr_print_logs', array(
            'verification_token' => $verification_token,
            'staff_idnumber' => $staff_idnumber,
            'dtr_year' => (int)$year,
            'dtr_month' => (int)$month,
            'immediate_supervisor' => $immediate_supervisor,
            'immediate_supervisor_position' => $immediate_supervisor_position,
            'generated_at' => $generated_at
        ));

        $verify_url = base_url('System_settings/verify_dtr/' . $verification_token);

        $data['staff_info'] = $staff_info;
        $data['dtr_rows'] = $rows;
        $data['selected_year'] = (int)$year;
        $data['selected_month'] = (int)$month;
        $data['generated_at'] = $generated_at;
        $data['verification_token'] = $verification_token;
        $data['verify_url'] = $verify_url;
        $data['immediate_supervisor'] = $immediate_supervisor;
        $data['immediate_supervisor_position'] = $immediate_supervisor_position;

        $this->load->view('pages/print_my_dtr', $data);
    }

    private function parse_travel_request_date($date_string)
    {
        if (empty($date_string)) {
            return null;
        }

        $date_string = trim($date_string);

        $formats = array(
            'Y-m-d',
            'm/d/Y',
            'n/d/Y',
            'm-d-Y',
            'n-j-Y',
            'd/m/Y',
            'd-m-Y',
            'F d, Y',
            'F j, Y',
            'M d, Y',
            'M j, Y'
        );

        foreach ($formats as $format) {
            $dt = DateTime::createFromFormat($format, $date_string);
            if ($dt && $dt->format($format) === $date_string) {
                return $dt->format('Y-m-d');
            }
        }

        $timestamp = strtotime($date_string);
        if ($timestamp !== false) {
            return date('Y-m-d', $timestamp);
        }

        return null;
    }

    public function verify_dtr($token = null)
    {
        if (!$this->session->userdata('username')) {
            redirect(base_url('login'));
            return;
        }

        if ($this->session->userdata('position') !== 'Admin') {
            show_error('Unauthorized access. Admin only.', 403);
            return;
        }

        if (!$token) {
            show_error('Missing verification token.');
            return;
        }

        $print_log = $this->db->where('verification_token', $token)
                            ->get('biometric_dtr_print_logs')
                            ->row();

        if (!$print_log) {
            $data = array(
                'is_valid' => false,
                'print_log' => null,
                'staff_info' => null,
                'dtr_rows' => array()
            );

            $this->load->view('pages/verify_dtr', $data);
            return;
        }

        $staff_info = $this->db->where('IDNumber', $print_log->staff_idnumber)
                            ->get('hris_staff')
                            ->row();

        $year = (int)$print_log->dtr_year;
        $month = (int)$print_log->dtr_month;
        $days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        $existing_dtr = $this->db->query("
            SELECT dtr_date, morning_in, morning_out, afternoon_in, afternoon_out
            FROM biometric_dtr
            WHERE staff_idnumber = ?
            AND YEAR(dtr_date) = ?
            AND MONTH(dtr_date) = ?
        ", array($print_log->staff_idnumber, $year, $month))->result_array();

        $indexed = array();
        foreach ($existing_dtr as $row) {
            $indexed[$row['dtr_date']] = $row;
        }

        $event_rows = $this->db->query("
            SELECT event_date, event_type, event_title, remarks
            FROM biometric_day_events
            WHERE staff_idnumber = ?
            AND YEAR(event_date) = ?
            AND MONTH(event_date) = ?
        ", array($print_log->staff_idnumber, $year, $month))->result_array();

        $event_index = array();
        foreach ($event_rows as $ev) {
            $event_index[$ev['event_date']] = $ev;
        }

        $travel_requests = $this->db->query("
            SELECT id, IDNumber, purpose, status, inclusive_date, date_start, date_end
            FROM travel_requests
            WHERE IDNumber = ?
            AND status = 'Approved'
            ORDER BY id ASC
        ", array($print_log->staff_idnumber))->result_array();

        $travel_map = $this->build_travel_map_for_month($travel_requests, $year, $month);

        $rows = array();

        for ($day = 1; $day <= 31; $day++) {
            if ($day <= $days_in_month) {
                $date = sprintf('%04d-%02d-%02d', $year, $month, $day);

                $morning_in = isset($indexed[$date]) ? $indexed[$date]['morning_in'] : null;
                $morning_out = isset($indexed[$date]) ? $indexed[$date]['morning_out'] : null;
                $afternoon_in = isset($indexed[$date]) ? $indexed[$date]['afternoon_in'] : null;
                $afternoon_out = isset($indexed[$date]) ? $indexed[$date]['afternoon_out'] : null;
                $travel_info = isset($travel_map[$date]) ? $travel_map[$date] : null;
                $event_info = isset($event_index[$date]) ? $event_index[$date] : null;
                $day_of_week = date('w', strtotime($date));
                $remarks = isset($event_index[$date]) ? $event_index[$date]['remarks'] : null;
            } else {
                $date = null;
                $morning_in = null;
                $morning_out = null;
                $afternoon_in = null;
                $afternoon_out = null;
                $travel_info = null;
                $event_info = null;
                $day_of_week = null;
                $remarks = null;
            }

            $rows[] = array(
                'date' => $date,
                'day' => $day,
                'day_of_week' => $day_of_week,
                'morning_in' => $morning_in,
                'morning_out' => $morning_out,
                'afternoon_in' => $afternoon_in,
                'afternoon_out' => $afternoon_out,
                'travel' => $travel_info,
                'event' => $event_info,
                'morning_status' => null,
                'afternoon_status' => null,
                'remarks' => $remarks
            );
        }

        $data = array(
            'is_valid' => true,
            'print_log' => $print_log,
            'staff_info' => $staff_info,
            'dtr_rows' => $rows
        );

        $this->load->view('pages/verify_dtr', $data);
    }

    private function build_travel_map_for_month($travel_requests, $year, $month)
    {
        $month_start = sprintf('%04d-%02d-01', $year, $month);
        $month_end = date('Y-m-t', strtotime($month_start));

        $travel_map = array();

        foreach ($travel_requests as $travel) {
            $travel_title = trim(isset($travel['purpose']) ? $travel['purpose'] : '');

            if ($travel_title === '') {
                continue;
            }

            $covered_dates = $this->extract_travel_dates_from_row($travel);

            if (empty($covered_dates)) {
                continue;
            }

            $filtered_dates = array();
            foreach ($covered_dates as $date_key) {
                if ($date_key >= $month_start && $date_key <= $month_end) {
                    $filtered_dates[] = $date_key;
                }
            }

            if (empty($filtered_dates)) {
                continue;
            }

            sort($filtered_dates);
            $rowspan = count($filtered_dates);

            foreach ($filtered_dates as $index => $date_key) {
                $travel_map[$date_key] = array(
                    'title' => $travel_title,
                    'is_start' => ($index === 0),
                    'rowspan' => $rowspan
                );
            }
        }

        return $travel_map;
    }

    private function extract_travel_dates_from_row($travel)
    {
        $dates = array();

        $inclusive_date = isset($travel['inclusive_date']) ? trim((string)$travel['inclusive_date']) : '';

        if ($inclusive_date !== '') {
            $dates = $this->expand_inclusive_date_string($inclusive_date);
        }

        if (!empty($dates)) {
            return $dates;
        }

        $travel_start = $this->parse_travel_request_date(isset($travel['date_start']) ? $travel['date_start'] : null);
        $travel_end   = $this->parse_travel_request_date(isset($travel['date_end']) ? $travel['date_end'] : null);

        if (empty($travel_start) || empty($travel_end)) {
            return array();
        }

        if ($travel_start > $travel_end) {
            $tmp = $travel_start;
            $travel_start = $travel_end;
            $travel_end = $tmp;
        }

        $covered_dates = array();
        $current = strtotime($travel_start);
        $end = strtotime($travel_end);

        while ($current <= $end) {
            $covered_dates[] = date('Y-m-d', $current);
            $current = strtotime('+1 day', $current);
        }

        return $covered_dates;
    }

    private function expand_inclusive_date_string($inclusive_date)
    {
        $inclusive_date = trim((string)$inclusive_date);

        if ($inclusive_date === '') {
            return array();
        }

        if (!preg_match('/^([A-Za-z]+)\s+(.+),\s*(\d{4})$/', $inclusive_date, $matches)) {
            return array();
        }

        $month_name = trim($matches[1]);
        $range_part = trim($matches[2]);
        $year = (int)$matches[3];

        $month = (int)date('m', strtotime($month_name . ' 1'));
        if ($month < 1 || $month > 12) {
            return array();
        }

        $segments = preg_split('/\s*&\s*/', $range_part);
        $dates = array();

        foreach ($segments as $segment) {
            $segment = trim($segment);

            if ($segment === '') {
                continue;
            }

            if (preg_match('/^(\d{1,2})\s*-\s*(\d{1,2})$/', $segment, $m)) {
                $start_day = (int)$m[1];
                $end_day = (int)$m[2];

                if ($start_day > $end_day) {
                    $tmp = $start_day;
                    $start_day = $end_day;
                    $end_day = $tmp;
                }

                for ($d = $start_day; $d <= $end_day; $d++) {
                    if (checkdate($month, $d, $year)) {
                        $dates[] = sprintf('%04d-%02d-%02d', $year, $month, $d);
                    }
                }
            } elseif (preg_match('/^\d{1,2}$/', $segment)) {
                $day = (int)$segment;
                if (checkdate($month, $day, $year)) {
                    $dates[] = sprintf('%04d-%02d-%02d', $year, $month, $day);
                }
            }
        }

        return array_values(array_unique($dates));
    }

    public function manual_timelog()
    {
            // 🔐 Must be logged in
            if (!$this->session->userdata('username')) {
                redirect(base_url('login'));
                return;
            }

            // 🔐 Admin only
            if ($this->session->userdata('position') !== 'Admin') {
                show_error('Unauthorized access. Admins only.', 403);
                return;
            }

            $data['title'] = 'Manual Timelog Entry';

            $data['employees'] = $this->db->query("
                SELECT 
                    IDNumber,
                    employeeNo,
                    FirstName,
                    MiddleName,
                    LastName,
                    Department
                FROM hris_staff
                WHERE d_id = 18
                ORDER BY LastName ASC, FirstName ASC
            ")->result();

            $this->render_page('pages/manual_timelog', $data);
    }



    public function manual_timelog_list()
    {
        $data['title'] = 'Manual Timelog List';

        $data['logs'] = $this->db->query("
            SELECT 
                brl.id,
                brl.mapped_staff_idnumber,
                brl.employee_name,
                brl.employee_no,
                brl.department,
                brl.log_datetime,
                brl.location_id,
                brl.card_no
            FROM biometric_raw_logs brl
            WHERE brl.location_id = 'MANUAL'
            ORDER BY brl.log_datetime DESC
            LIMIT 500
        ")->result();

        $this->render_page('pages/manual_timelog_list', $data);
    }

 public function generate_missing_dtr_logs()
{
    $staff_idnumber = trim($this->input->post('staff_idnumber'));
    $year = (int)$this->input->post('year');
    $month = (int)$this->input->post('month');

    if ($staff_idnumber === '' || empty($year) || empty($month)) {
        $this->session->set_flashdata('danger', 'Please select employee, year, and month.');
        redirect(base_url('System_settings/manual_timelog'));
        return;
    }

    $staff = $this->db->where('IDNumber', $staff_idnumber)->get('hris_staff')->row();

    if (!$staff) {
        $this->session->set_flashdata('danger', 'Employee not found.');
        redirect(base_url('System_settings/manual_timelog'));
        return;
    }

    $days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);

    $inserted_count = 0;
    $processed_dates = 0;
    $skipped_dates = 0;

    $skipped_weekend = 0;
    $skipped_travel = 0;
    $skipped_holiday = 0;
    $skipped_event = 0;

    $slot_labels = array(
        'morning_in'    => 'Generated missing Morning IN',
        'morning_out'   => 'Generated missing Morning OUT',
        'afternoon_in'  => 'Generated missing Afternoon IN',
        'afternoon_out' => 'Generated missing Afternoon OUT',
    );

    for ($day = 1; $day <= $days_in_month; $day++) {
        $date = sprintf('%04d-%02d-%02d', $year, $month, $day);

        if ($this->is_friday_or_weekend($date)) {
            $skipped_dates++;
            $skipped_weekend++;
            continue;
        }

        if ($this->has_travel_on_date($staff_idnumber, $date)) {
            $skipped_dates++;
            $skipped_travel++;
            continue;
        }

        if ($this->has_holiday_on_date($staff_idnumber, $date)) {
            $skipped_dates++;
            $skipped_holiday++;
            continue;
        }

        if ($this->has_day_event_on_date($staff_idnumber, $date)) {
            $skipped_dates++;
            $skipped_event++;
            continue;
        }

        // get existing grouped slots from raw logs first
        $slots = $this->get_day_slot_values_from_raw_logs($staff_idnumber, $date);

        $date_inserted = false;

        foreach ($slot_labels as $slot => $label) {
            // skip if slot already exists
            if (!empty($slots[$slot])) {
                continue;
            }

            $random_time = $this->random_time_for_slot($slot);
            if (empty($random_time)) {
                continue;
            }

            $log_datetime = $date . ' ' . $random_time;

            $ok = $this->insert_manual_raw_log_for_staff(
                $staff,
                $log_datetime,
                $label,
                'MANUAL-GENERATED'
            );

            if ($ok) {
                $inserted_count++;
                $date_inserted = true;

                log_message(
                    'error',
                    'GENERATED RAW LOG INSERTED | staff=' . $staff_idnumber .
                    ' | date=' . $date .
                    ' | slot=' . $slot .
                    ' | datetime=' . $log_datetime
                );
            } else {
                log_message(
                    'error',
                    'GENERATED RAW LOG SKIPPED/DUPLICATE | staff=' . $staff_idnumber .
                    ' | date=' . $date .
                    ' | slot=' . $slot .
                    ' | datetime=' . $log_datetime
                );
            }
        }

        // always rebuild once after processing the date
        $rebuilt_slots = $this->rebuild_dtr_from_raw_logs_for_date($staff_idnumber, $date);

        if (
            !empty($rebuilt_slots['morning_in']) ||
            !empty($rebuilt_slots['morning_out']) ||
            !empty($rebuilt_slots['afternoon_in']) ||
            !empty($rebuilt_slots['afternoon_out'])
        ) {
            $processed_dates++;
        }

        log_message(
            'error',
            'DTR REBUILT | staff=' . $staff_idnumber .
            ' | date=' . $date .
            ' | morning_in=' . ($rebuilt_slots['morning_in'] ?: '') .
            ' | morning_out=' . ($rebuilt_slots['morning_out'] ?: '') .
            ' | afternoon_in=' . ($rebuilt_slots['afternoon_in'] ?: '') .
            ' | afternoon_out=' . ($rebuilt_slots['afternoon_out'] ?: '')
        );
    }

    $this->session->set_flashdata(
        'success',
        'Missing logs generation finished. Inserted punches: ' . $inserted_count .
        ' | Dates rebuilt: ' . $processed_dates .
        ' | Skipped total: ' . $skipped_dates .
        ' | Weekend: ' . $skipped_weekend .
        ' | Travel: ' . $skipped_travel .
        ' | Holiday: ' . $skipped_holiday .
        ' | Event: ' . $skipped_event
    );

    redirect(base_url('System_settings/manual_timelog'));
}

    public function add_specific_dtr_log()
    {
        $staff_idnumber = trim($this->input->post('staff_idnumber'));
        $dtr_date = trim($this->input->post('dtr_date'));
        $slot = trim($this->input->post('slot'));

        $allowed_slots = array('morning_in', 'morning_out', 'afternoon_in', 'afternoon_out');

        if ($staff_idnumber === '' || $dtr_date === '' || !in_array($slot, $allowed_slots, true)) {
            $this->session->set_flashdata('danger', 'Please select employee, date, and valid slot.');
            redirect(base_url('System_settings/manual_timelog'));
            return;
        }

        $staff = $this->db->where('IDNumber', $staff_idnumber)->get('hris_staff')->row();
        if (!$staff) {
            $this->session->set_flashdata('danger', 'Employee not found.');
            redirect(base_url('System_settings/manual_timelog'));
            return;
        }

        if ($this->should_skip_generated_log_date($staff_idnumber, $dtr_date)) {
            $this->session->set_flashdata('danger', 'Selected date is skipped because it is Friday, weekend, holiday, or has approved travel.');
            redirect(base_url('System_settings/manual_timelog'));
            return;
        }

        $existing_slots = $this->get_day_slot_values_from_raw_logs($staff_idnumber, $dtr_date);

        if (!empty($existing_slots[$slot])) {
            $this->session->set_flashdata('danger', 'The selected slot already has a value: ' . $existing_slots[$slot]);
            redirect(base_url('System_settings/manual_timelog'));
            return;
        }

        $random_time = $this->random_time_for_slot($slot);

        if (!$random_time) {
            $this->session->set_flashdata('danger', 'Unable to generate random time for selected slot.');
            redirect(base_url('System_settings/manual_timelog'));
            return;
        }

        $inserted = $this->insert_manual_raw_log_for_staff(
            $staff,
            $dtr_date . ' ' . $random_time,
            'Generated missing ' . strtoupper(str_replace('_', ' ', $slot)),
            'MANUAL-GENERATED'
        );

        if (!$inserted) {
            $this->session->set_flashdata('danger', 'A duplicate manual raw log already exists for that exact timestamp.');
            redirect(base_url('System_settings/manual_timelog'));
            return;
        }

        $this->rebuild_dtr_from_raw_logs_for_date($staff_idnumber, $dtr_date);

        $this->session->set_flashdata('success', 'Log added successfully for ' . $dtr_date . ' (' . $slot . ').');
        redirect(base_url('System_settings/manual_timelog'));
    }

    public function delete_specific_dtr_log()
    {
        $staff_idnumber = trim($this->input->post('staff_idnumber'));
        $dtr_date = trim($this->input->post('dtr_date'));
        $slot = trim($this->input->post('slot'));

        $allowed_slots = array('morning_in', 'morning_out', 'afternoon_in', 'afternoon_out');

        if ($staff_idnumber === '' || $dtr_date === '' || !in_array($slot, $allowed_slots, true)) {
            $this->session->set_flashdata('danger', 'Please select employee, date, and valid slot.');
            redirect(base_url('System_settings/manual_timelog'));
            return;
        }

        switch ($slot) {
            case 'morning_in':
                $where_time = "TIME(log_datetime) >= '04:00:00' AND TIME(log_datetime) <= '11:59:59'";
                $order_by = 'log_datetime ASC';
                break;
            case 'morning_out':
                $where_time = "TIME(log_datetime) >= '12:00:00' AND TIME(log_datetime) <= '12:59:59'";
                $order_by = 'log_datetime DESC';
                break;
            case 'afternoon_in':
                $where_time = "TIME(log_datetime) >= '12:01:00' AND TIME(log_datetime) <= '14:59:59'";
                $order_by = 'log_datetime ASC';
                break;
            case 'afternoon_out':
                $where_time = "TIME(log_datetime) >= '15:00:00' AND TIME(log_datetime) <= '23:59:59'";
                $order_by = 'log_datetime DESC';
                break;
            default:
                $this->session->set_flashdata('danger', 'Invalid slot selected.');
                redirect(base_url('System_settings/manual_timelog'));
                return;
        }

        $manual_log = $this->db->query("
            SELECT id
            FROM biometric_raw_logs
            WHERE mapped_staff_idnumber = ?
              AND DATE(log_datetime) = ?
              AND match_status = 'matched'
              AND location_id IN ('MANUAL', 'MANUAL-GENERATED')
              AND {$where_time}
            ORDER BY {$order_by}
            LIMIT 1
        ", array($staff_idnumber, $dtr_date))->row();

        if (!$manual_log) {
            $this->session->set_flashdata('danger', 'No matching manual raw log found for the selected employee, date, and slot.');
            redirect(base_url('System_settings/manual_timelog'));
            return;
        }

        $this->db->where('id', $manual_log->id)->delete('biometric_raw_logs');
        $this->rebuild_dtr_from_raw_logs_for_date($staff_idnumber, $dtr_date);

        $this->session->set_flashdata('success', 'Log deleted successfully for ' . $dtr_date . ' (' . $slot . ').');
        redirect(base_url('System_settings/manual_timelog'));
    }

    public function process_logs()
    {
        if (!$this->session->userdata('logged_in')) {
            redirect(base_url('Login'));
            return;
        }

        ini_set('max_execution_time', 300);
        ini_set('memory_limit', '512M');

        $inserted = 0;
        $updated  = 0;
        $skipped  = 0;

        $mappings = $this->db->query("
            SELECT *
            FROM biometric_employee_map
            WHERE is_active = 1
            ORDER BY staff_idnumber ASC
        ")->result_array();

        if (empty($mappings)) {
            $this->session->set_flashdata('danger', 'No active biometric mappings found.');
            redirect(base_url('System_settings/biometric_employee_mapping'));
            return;
        }

        foreach ($mappings as $map) {
            $staff_idnumber      = isset($map['staff_idnumber']) ? trim($map['staff_idnumber']) : '';
            $biometric_no        = isset($map['biometric_no']) ? trim($map['biometric_no']) : '';
            $biometric_id_number = isset($map['biometric_id_number']) ? trim($map['biometric_id_number']) : '';
            $biometric_card_no   = isset($map['biometric_card_no']) ? trim($map['biometric_card_no']) : '';
            $biometric_name      = isset($map['biometric_name']) ? trim($map['biometric_name']) : '';

            if ($staff_idnumber === '') {
                $skipped++;
                continue;
            }

            $this->db->select('log_datetime');
            $this->db->from('biometric_raw_logs');
            $this->db->where('match_status', 'matched');
            $this->db->where('mapped_staff_idnumber', $staff_idnumber);
            $this->db->where('log_datetime IS NOT NULL', null, false);

            $has_identifier = false;
            $this->db->group_start();

            if ($biometric_no !== '') {
                $this->db->or_where('employee_no', $biometric_no);
                $has_identifier = true;
            }

            if ($biometric_id_number !== '') {
                $this->db->or_where('id_number', $biometric_id_number);
                $has_identifier = true;
            }

            if ($biometric_card_no !== '') {
                $this->db->or_where('card_no', $biometric_card_no);
                $has_identifier = true;
            }

            if ($biometric_name !== '') {
                $this->db->or_where('employee_name', $biometric_name);
                $has_identifier = true;
            }

            $this->db->group_end();
            $this->db->order_by('log_datetime', 'ASC');

            if (!$has_identifier) {
                $skipped++;
                continue;
            }

            $logs = $this->db->get()->result_array();

            if (empty($logs)) {
                $skipped++;
                continue;
            }

            $grouped = array();

            foreach ($logs as $row) {
                if (empty($row['log_datetime'])) {
                    continue;
                }

                $ts = strtotime($row['log_datetime']);
                if (!$ts) {
                    continue;
                }

                $log_date = date('Y-m-d', $ts);
                $log_time = date('H:i:s', $ts);

                $day_num = date('N', strtotime($log_date));
                if ($day_num == 6 || $day_num == 7) {
                    continue;
                }

                if (!isset($grouped[$log_date])) {
                    $grouped[$log_date] = array(
                        'morning_in_candidates' => array(),
                        'morning_out_candidates' => array(),
                        'afternoon_in_candidates' => array(),
                        'afternoon_out_candidates' => array(),
                    );
                }

                if ($log_time >= '04:00:00' && $log_time <= '11:59:59') {
                    $grouped[$log_date]['morning_in_candidates'][] = $log_time;
                }

                if ($log_time >= '12:00:00' && $log_time <= '12:59:59') {
                    $grouped[$log_date]['morning_out_candidates'][] = $log_time;
                }

                if ($log_time >= '12:01:00' && $log_time <= '14:59:59') {
                    $grouped[$log_date]['afternoon_in_candidates'][] = $log_time;
                }

                if ($log_time >= '15:00:00' && $log_time <= '23:59:59') {
                    $grouped[$log_date]['afternoon_out_candidates'][] = $log_time;
                }
            }

            foreach ($grouped as $log_date => $times) {
                $morning_in    = !empty($times['morning_in_candidates']) ? min($times['morning_in_candidates']) : null;
                $morning_out   = !empty($times['morning_out_candidates']) ? max($times['morning_out_candidates']) : null;
                $afternoon_in  = !empty($times['afternoon_in_candidates']) ? min($times['afternoon_in_candidates']) : null;
                $afternoon_out = !empty($times['afternoon_out_candidates']) ? max($times['afternoon_out_candidates']) : null;

                if (!empty($morning_out) && !empty($afternoon_in) && $morning_out > $afternoon_in) {
                    $earlier = min($morning_out, $afternoon_in);
                    $later   = max($morning_out, $afternoon_in);
                    $morning_out = $earlier;
                    $afternoon_in = $later;
                }

                if (!empty($afternoon_in) && !empty($afternoon_out) && $afternoon_in > $afternoon_out) {
                    $afternoon_in = null;
                }

                if (!empty($morning_out) && !empty($afternoon_in) && $morning_out > $afternoon_in) {
                    $morning_out = null;
                }

                $existing = $this->db->where('staff_idnumber', $staff_idnumber)
                    ->where('dtr_date', $log_date)
                    ->get('biometric_dtr')
                    ->row();

                $data = array(
                    'staff_idnumber'   => $staff_idnumber,
                    'dtr_date'         => $log_date,
                    'morning_in'       => $morning_in,
                    'morning_out'      => $morning_out,
                    'afternoon_in'     => $afternoon_in,
                    'afternoon_out'    => $afternoon_out,
                    'source_upload_id' => null,
                    'updated_at'       => date('Y-m-d H:i:s')
                );

                if ($existing) {
                    $this->db->where('id', $existing->id)->update('biometric_dtr', $data);
                    $updated++;
                } else {
                    $data['created_at'] = date('Y-m-d H:i:s');
                    $this->db->insert('biometric_dtr', $data);
                    $inserted++;
                }
            }
        }

        $this->session->set_flashdata(
            'success',
            'Process Logs completed. Inserted: ' . $inserted . ' | Updated: ' . $updated . ' | Skipped: ' . $skipped
        );

        redirect(base_url('System_settings/biometric_dtr_viewer'));
    }

    public function save_manual_day_event()
{
    $staff_idnumber = trim($this->input->post('staff_idnumber'));
    $event_date     = trim($this->input->post('event_date'));
    $event_type     = trim($this->input->post('event_type'));
    $event_title    = trim($this->input->post('event_title'));
    $remarks        = trim($this->input->post('remarks'));

    if ($staff_idnumber === '' || $event_date === '' || $event_type === '') {
        $this->session->set_flashdata('danger', 'Please complete all required fields.');
        redirect(base_url('System_settings/manual_timelog'));
        return;
    }

    $final_title = $event_title !== '' ? $event_title : $event_type;

    // APPLY TO ALL LISTED EMPLOYEES
    if ($staff_idnumber === 'ALL') {
        $employees = $this->db->query("
            SELECT IDNumber
            FROM hris_staff
            WHERE d_id = 18
            ORDER BY LastName ASC, FirstName ASC
        ")->result();

        if (empty($employees)) {
            $this->session->set_flashdata('danger', 'No employees found under d_id = 18.');
            redirect(base_url('System_settings/manual_timelog'));
            return;
        }

        $insert_count = 0;
        $skip_count = 0;

        foreach ($employees as $emp) {
            $existing = $this->db->where('staff_idnumber', $emp->IDNumber)
                ->where('event_date', $event_date)
                ->where('event_type', $event_type)
                ->get('biometric_day_events')
                ->row();

            if ($existing) {
                $skip_count++;
                continue;
            }

            $this->db->insert('biometric_day_events', array(
                'staff_idnumber' => $emp->IDNumber,
                'event_date'     => $event_date,
                'event_type'     => $event_type,
                'event_title'    => $final_title,
                'remarks'        => $remarks,
                'is_whole_day'   => 1,
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s')
            ));

            $insert_count++;
        }

        $this->session->set_flashdata(
            'success',
            'Day event applied to all listed employees. Added: ' . $insert_count . ' | Skipped existing: ' . $skip_count
        );
        redirect(base_url('System_settings/manual_timelog'));
        return;
    }

    // APPLY TO SINGLE EMPLOYEE
    $staff = $this->db->where('IDNumber', $staff_idnumber)->get('hris_staff')->row();
    if (!$staff) {
        $this->session->set_flashdata('danger', 'Employee record not found.');
        redirect(base_url('System_settings/manual_timelog'));
        return;
    }

    $existing = $this->db->where('staff_idnumber', $staff_idnumber)
        ->where('event_date', $event_date)
        ->where('event_type', $event_type)
        ->get('biometric_day_events')
        ->row();

    if ($existing) {
        $this->session->set_flashdata('danger', 'This event already exists for the selected employee and date.');
        redirect(base_url('System_settings/manual_timelog'));
        return;
    }

    $this->db->insert('biometric_day_events', array(
        'staff_idnumber' => $staff_idnumber,
        'event_date'     => $event_date,
        'event_type'     => $event_type,
        'event_title'    => $final_title,
        'remarks'        => $remarks,
        'is_whole_day'   => 1,
        'created_at'     => date('Y-m-d H:i:s'),
        'updated_at'     => date('Y-m-d H:i:s')
    ));

    $this->session->set_flashdata('success', 'Manual day event saved successfully.');
    redirect(base_url('System_settings/manual_timelog'));
}

    public function late_undertime_summary($year = null, $month = null)
    {
        if (!$this->session->userdata('username')) {
            redirect(base_url('login'));
            return;
        }

        if ($this->session->userdata('position') !== 'Admin') {
            show_error('Unauthorized access. Admin only.', 403);
            return;
        }

        $data['title'] = 'Late / Undertime Summary';

        $year  = $year ?: $this->input->get('year');
        $month = $month ?: $this->input->get('month');

        $year  = !empty($year) ? (int)$year : (int)date('Y');
        $month = !empty($month) ? (int)$month : (int)date('n');

        $department = trim((string)$this->input->get('department'));

        $sql = "
            SELECT 
                s.IDNumber,
                s.LastName,
                s.FirstName,
                s.MiddleName,
                s.Department,

                COUNT(CASE 
                    WHEN d.morning_in IS NOT NULL AND d.morning_in <> ''
                    AND d.morning_out IS NOT NULL AND d.morning_out <> ''
                    AND TIME(d.morning_in) >= '08:01:00'
                    THEN 1 END) AS late_am_days,

                SUM(CASE 
                    WHEN d.morning_in IS NOT NULL AND d.morning_in <> ''
                    AND d.morning_out IS NOT NULL AND d.morning_out <> ''
                    AND TIME(d.morning_in) >= '08:01:00'
                    THEN FLOOR((TIME_TO_SEC(TIME(d.morning_in)) - TIME_TO_SEC('08:00:00')) / 60)
                    ELSE 0 END) AS late_am_minutes,

                COUNT(CASE 
                    WHEN d.afternoon_in IS NOT NULL AND d.afternoon_in <> ''
                    AND d.afternoon_out IS NOT NULL AND d.afternoon_out <> ''
                    AND TIME(d.afternoon_in) >= '13:01:00'
                    THEN 1 END) AS late_pm_days,

                SUM(CASE 
                    WHEN d.afternoon_in IS NOT NULL AND d.afternoon_in <> ''
                    AND d.afternoon_out IS NOT NULL AND d.afternoon_out <> ''
                    AND TIME(d.afternoon_in) >= '13:01:00'
                    THEN FLOOR((TIME_TO_SEC(TIME(d.afternoon_in)) - TIME_TO_SEC('13:00:00')) / 60)
                    ELSE 0 END) AS late_pm_minutes,

                COUNT(CASE 
                    WHEN d.morning_in IS NOT NULL AND d.morning_in <> ''
                    AND d.morning_out IS NOT NULL AND d.morning_out <> ''
                    AND TIME(d.morning_out) < '12:00:00'
                    THEN 1 END) AS undertime_am_days,

                SUM(CASE 
                    WHEN d.morning_in IS NOT NULL AND d.morning_in <> ''
                    AND d.morning_out IS NOT NULL AND d.morning_out <> ''
                    AND TIME(d.morning_out) < '12:00:00'
                    THEN FLOOR((TIME_TO_SEC('12:00:00') - TIME_TO_SEC(TIME(d.morning_out))) / 60)
                    ELSE 0 END) AS undertime_am_minutes,

                COUNT(CASE 
                    WHEN d.afternoon_in IS NOT NULL AND d.afternoon_in <> ''
                    AND d.afternoon_out IS NOT NULL AND d.afternoon_out <> ''
                    AND TIME(d.afternoon_out) < '17:00:00'
                    THEN 1 END) AS undertime_pm_days,

                SUM(CASE 
                    WHEN d.afternoon_in IS NOT NULL AND d.afternoon_in <> ''
                    AND d.afternoon_out IS NOT NULL AND d.afternoon_out <> ''
                    AND TIME(d.afternoon_out) < '17:00:00'
                    THEN FLOOR((TIME_TO_SEC('17:00:00') - TIME_TO_SEC(TIME(d.afternoon_out))) / 60)
                    ELSE 0 END) AS undertime_pm_minutes,

                COUNT(CASE 
                    WHEN (
                        (d.morning_in IS NOT NULL AND d.morning_in <> '' AND (d.morning_out IS NULL OR d.morning_out = ''))
                        OR
                        ((d.morning_in IS NULL OR d.morning_in = '') AND d.morning_out IS NOT NULL AND d.morning_out <> '')
                        OR
                        (d.afternoon_in IS NOT NULL AND d.afternoon_in <> '' AND (d.afternoon_out IS NULL OR d.afternoon_out = ''))
                        OR
                        ((d.afternoon_in IS NULL OR d.afternoon_in = '') AND d.afternoon_out IS NOT NULL AND d.afternoon_out <> '')
                    )
                    THEN 1 END) AS incomplete_log_days

            FROM hris_staff s
            LEFT JOIN biometric_dtr d 
                ON d.staff_idnumber = s.IDNumber
            AND YEAR(d.dtr_date) = ?
            AND MONTH(d.dtr_date) = ?
            AND DAYOFWEEK(d.dtr_date) NOT IN (1, 7)

            WHERE s.d_id = 18
        ";

        $params = array($year, $month);

        if ($department !== '') {
            $sql .= " AND s.Department = ? ";
            $params[] = $department;
        }

        $sql .= "
            GROUP BY s.IDNumber, s.LastName, s.FirstName, s.MiddleName, s.Department
            HAVING 
                late_am_days > 0
                OR late_pm_days > 0
                OR undertime_am_days > 0
                OR undertime_pm_days > 0
                OR incomplete_log_days > 0
            ORDER BY s.LastName ASC, s.FirstName ASC
        ";

        $data['summary'] = $this->db->query($sql, $params)->result();
        $data['selected_year'] = $year;
        $data['selected_month'] = $month;
        $data['selected_department'] = $department;

        $data['departments'] = $this->db
            ->select('Department')
            ->from('hris_staff')
            ->where('d_id', 18)
            ->where('Department IS NOT NULL', null, false)
            ->where('Department <>', '')
            ->group_by('Department')
            ->order_by('Department', 'ASC')
            ->get()
            ->result();

        $this->render_page('pages/late_undertime_summary', $data);
    }

}
