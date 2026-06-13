<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Leave_balance_admin_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function import_balances_from_csv($file_path, $year, $uploaded_by = null)
    {
        if (!is_file($file_path)) {
            return array('success' => false, 'message' => 'Uploaded file not found.');
        }

        $handle = fopen($file_path, 'r');
        if (!$handle) {
            return array('success' => false, 'message' => 'Unable to open CSV file.');
        }

        $header = fgetcsv($handle);
        if (!$header) {
            fclose($handle);
            return array('success' => false, 'message' => 'CSV is empty.');
        }

        $map = array();
        foreach ($header as $idx => $name) {
            $map[strtolower(trim((string)$name))] = $idx;
        }

        $required = array('staff_idnumber', 'leave_type_code', 'balance');
        foreach ($required as $req) {
            if (!array_key_exists($req, $map)) {
                fclose($handle);
                return array('success' => false, 'message' => 'Missing required CSV column: ' . $req);
            }
        }

        $processed = 0;
        $skipped = 0;
        $now = date('Y-m-d H:i:s');

        $this->db->trans_start();
        while (($row = fgetcsv($handle)) !== false) {
            $staff_idnumber = trim((string)$row[$map['staff_idnumber']]);
            $leave_type_code = strtoupper(trim((string)$row[$map['leave_type_code']]));
            $balance = isset($row[$map['balance']]) ? (float)$row[$map['balance']] : 0;

            if ($staff_idnumber === '' || $leave_type_code === '') {
                $skipped++;
                continue;
            }

            $existing = $this->db
                ->where('staff_idnumber', $staff_idnumber)
                ->where('leave_type_code', $leave_type_code)
                ->where('balance_year', (int)$year)
                ->get('leave_balances')
                ->row();

            $payload = array(
                'staff_idnumber' => $staff_idnumber,
                'leave_type_code' => $leave_type_code,
                'balance_year' => (int)$year,
                'balance' => $balance,
                'updated_at' => $now,
            );

            if ($existing) {
                $this->db->where('id', (int)$existing->id)->update('leave_balances', $payload);
            } else {
                $payload['created_at'] = $now;
                $this->db->insert('leave_balances', $payload);
            }
            $processed++;
        }
        fclose($handle);

        $this->db->insert('leave_balance_import_logs', array(
            'file_name' => basename($file_path),
            'balance_year' => (int)$year,
            'rows_processed' => $processed,
            'rows_skipped' => $skipped,
            'uploaded_by' => $uploaded_by,
            'created_at' => $now,
        ));

        $this->db->trans_complete();

        return array(
            'success' => $this->db->trans_status(),
            'message' => $this->db->trans_status()
                ? 'Bulk balance upload completed. Processed: ' . $processed . ', skipped: ' . $skipped . '.'
                : 'Bulk balance upload failed.',
        );
    }

    public function get_recent_imports($limit = 10)
    {
        return $this->db->select('lbil.*, u.username AS uploaded_by_username')
            ->from('leave_balance_import_logs lbil')
            ->join('users u', 'u.id = lbil.uploaded_by', 'left')
            ->order_by('lbil.id', 'DESC')
            ->limit((int)$limit)
            ->get()
            ->result();
    }

    public function get_import_logs($filters = array(), $limit = 200)
    {
        $this->db->select('lbil.*, u.username AS uploaded_by_username')
            ->from('leave_balance_import_logs lbil')
            ->join('users u', 'u.id = lbil.uploaded_by', 'left');

        if (!empty($filters['balance_year'])) {
            $this->db->where('lbil.balance_year', (int)$filters['balance_year']);
        }
        if (!empty($filters['date_from'])) {
            $this->db->where('DATE(lbil.created_at) >=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $this->db->where('DATE(lbil.created_at) <=', $filters['date_to']);
        }

        return $this->db->order_by('lbil.id', 'DESC')
            ->limit((int)$limit)
            ->get()
            ->result();
    }
}
