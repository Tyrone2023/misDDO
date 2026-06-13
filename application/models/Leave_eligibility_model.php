<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Leave_eligibility_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function is_employee_allowed($staff_idnumber, $leave_type_code)
    {
        $row = $this->db
            ->where('staff_idnumber', trim((string)$staff_idnumber))
            ->where('leave_type_code', strtoupper(trim((string)$leave_type_code)))
            ->where('status', 'APPROVED')
            ->get('leave_employee_privileges')
            ->row();

        return !empty($row);
    }

    public function get_employee_privileges($staff_idnumber)
    {
        return $this->db
            ->select('lep.*, u.username AS approved_by_username')
            ->from('leave_employee_privileges lep')
            ->join('users u', 'u.id = lep.approved_by', 'left')
            ->where('lep.staff_idnumber', trim((string)$staff_idnumber))
            ->order_by('lep.leave_type_code', 'ASC')
            ->get()
            ->result();
    }

    public function save_or_update_privilege($data, $acted_by = null)
    {
        $staff_idnumber  = trim((string)$data['staff_idnumber']);
        $leave_type_code = strtoupper(trim((string)$data['leave_type_code']));
        $status          = strtoupper(trim((string)$data['status']));
        $remarks         = isset($data['remarks']) ? trim((string)$data['remarks']) : '';
        $now             = date('Y-m-d H:i:s');

        $payload = array(
            'staff_idnumber'  => $staff_idnumber,
            'leave_type_code' => $leave_type_code,
            'status'          => $status,
            'approved_by'     => $acted_by ?: (isset($data['approved_by']) ? (int)$data['approved_by'] : null),
            'approved_at'     => isset($data['approved_at']) && !empty($data['approved_at']) ? $data['approved_at'] : $now,
            'remarks'         => $remarks,
            'updated_at'      => $now,
        );

        $key = array(
            'staff_idnumber'  => $staff_idnumber,
            'leave_type_code' => $leave_type_code,
        );

        $this->db->trans_start();

        $existing = $this->db->where($key)->get('leave_employee_privileges')->row();
        if ($existing) {
            $this->db->where('id', (int)$existing->id)->update('leave_employee_privileges', $payload);
            $privilege_id = (int)$existing->id;
        } else {
            $payload['created_at'] = $now;
            $this->db->insert('leave_employee_privileges', $payload);
            $privilege_id = (int)$this->db->insert_id();
        }

        $this->db->insert('leave_privilege_logs', array(
            'privilege_id'     => $privilege_id,
            'staff_idnumber'   => $staff_idnumber,
            'leave_type_code'  => $leave_type_code,
            'action'           => $status,
            'remarks'          => $remarks,
            'acted_by'         => $acted_by ?: (isset($data['approved_by']) ? (int)$data['approved_by'] : null),
            'created_at'       => $now,
        ));

        $this->db->trans_complete();

        return $this->db->trans_status() ? $privilege_id : 0;
    }

    public function get_privilege_logs($filters = array(), $limit = 100)
    {
        $this->db->select('lpl.*, u.username AS acted_by_username, hs.FirstName, hs.LastName')
            ->from('leave_privilege_logs lpl')
            ->join('users u', 'u.id = lpl.acted_by', 'left')
            ->join('hris_staff hs', 'hs.IDNumber = lpl.staff_idnumber', 'left');

        if (!empty($filters['staff_idnumber'])) {
            $this->db->where('lpl.staff_idnumber', trim((string)$filters['staff_idnumber']));
        }
        if (!empty($filters['leave_type_code'])) {
            $this->db->where('lpl.leave_type_code', strtoupper(trim((string)$filters['leave_type_code'])));
        }
        if (!empty($filters['action'])) {
            $this->db->where('lpl.action', strtoupper(trim((string)$filters['action'])));
        }
        if (!empty($filters['date_from'])) {
            $this->db->where('DATE(lpl.created_at) >=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $this->db->where('DATE(lpl.created_at) <=', $filters['date_to']);
        }

        return $this->db->order_by('lpl.id', 'DESC')
            ->limit((int)$limit)
            ->get()
            ->result();
    }
}
