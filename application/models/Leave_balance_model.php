<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Leave_balance_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function get_employee_balances($employee_id, $year)
    {
        $row = $this->db
            ->select('
                staff_idnumber AS employee_id,
                COALESCE(NULLIF(balance_year, 0), ' . (int)$year . ') AS balance_year,
                vl_balance,
                sl_balance,
                spl_balance,
                COALESCE(forced_leave_balance, fl_balance, 0) AS forced_leave_balance,
                COALESCE(solo_parent_balance, 0) AS solo_parent_balance,
                wellness_balance,
                COALESCE(coc_balance_hours, coc_balance, 0) AS coc_balance_hours,
                COALESCE(vsc_balance_days, 0) AS vsc_balance_days
            ', false)
            ->from('leave_balances')
            ->where('staff_idnumber', $employee_id)
            ->order_by('balance_year', 'DESC')
            ->order_by('updated_at', 'DESC')
            ->limit(1)
            ->get()
            ->row_array();

        if ($row) {
            return $row;
        }

        return array(
            'employee_id' => $employee_id,
            'balance_year' => (int)$year,
            'vl_balance' => 0,
            'sl_balance' => 0,
            'spl_balance' => 3,
            'forced_leave_balance' => 0,
            'solo_parent_balance' => 0,
            'wellness_balance' => 0,
            'coc_balance_hours' => 0,
            'vsc_balance_days' => 0,
        );
    }

    public function ensure_year_balance($employee_id, $year)
    {
        $existing = $this->db->where('employee_id', $employee_id)
            ->where('balance_year', (int)$year)
            ->get('employee_leave_balances')
            ->row_array();

        if ($existing) {
            return $existing;
        }

        $data = array(
            'employee_id' => $employee_id,
            'balance_year' => (int)$year,
            'vl_balance' => 0.00,
            'sl_balance' => 0.00,
            'spl_balance' => 3.00,
            'forced_leave_availed' => 0.00,
            'solo_parent_balance' => 0.00,
            'wellness_balance' => 0.00,
            'coc_balance_hours' => 0.00,
            'vsc_balance_days' => 0.00,
        );

        $this->db->insert('employee_leave_balances', $data);

        return $this->get_employee_balances($employee_id, $year);
    }

    public function adjust_balance($employee_id, $year, $field, $delta)
    {
        $this->ensure_year_balance($employee_id, $year);

        $this->db->set($field, "{$field} + " . (float)$delta, false)
            ->where('employee_id', $employee_id)
            ->where('balance_year', (int)$year)
            ->update('employee_leave_balances');
    }

    public function run_monthly_accrual($month_start)
    {
        $month = date('Y-m', strtotime($month_start));
        $year = (int)date('Y', strtotime($month_start));

        $profiles = $this->db->where('leave_profile_code', 'NON_TEACHING_CSC')
            ->where('is_active', 1)
            ->get('employee_leave_profiles')
            ->result_array();

        $count = 0;

        foreach ($profiles as $profile) {
            $employee_id = $profile['employee_id'];
            $this->ensure_year_balance($employee_id, $year);

            $remark = 'Monthly VL/SL accrual for ' . $month;

            $already = $this->db
                ->where('employee_id', $employee_id)
                ->where('source_module', 'MONTHLY_ACCRUAL')
                ->like('remarks', $month)
                ->count_all_results('leave_ledgers');

            if ($already > 0) {
                continue;
            }

            $this->adjust_balance($employee_id, $year, 'vl_balance', 1.25);
            $this->adjust_balance($employee_id, $year, 'sl_balance', 1.25);

            $this->db->insert_batch('leave_ledgers', array(
                array(
                    'employee_id' => $employee_id,
                    'txn_date' => date('Y-m-d H:i:s'),
                    'source_module' => 'MONTHLY_ACCRUAL',
                    'leave_type_code' => 'VL',
                    'balance_bucket' => 'vl_balance',
                    'txn_type' => 'ACCRUAL',
                    'quantity' => 1.25,
                    'unit' => 'DAY',
                    'remarks' => $remark,
                ),
                array(
                    'employee_id' => $employee_id,
                    'txn_date' => date('Y-m-d H:i:s'),
                    'source_module' => 'MONTHLY_ACCRUAL',
                    'leave_type_code' => 'SL',
                    'balance_bucket' => 'sl_balance',
                    'txn_type' => 'ACCRUAL',
                    'quantity' => 1.25,
                    'unit' => 'DAY',
                    'remarks' => $remark,
                ),
            ));

            $count++;
        }

        return array('count' => $count);
    }

    public function run_annual_reset($year)
    {
        $profiles = $this->db->where('is_active', 1)
            ->get('employee_leave_profiles')
            ->result_array();

        $count = 0;

        foreach ($profiles as $profile) {
            $employee_id = $profile['employee_id'];
            $this->ensure_year_balance($employee_id, $year);

            $this->db->where('employee_id', $employee_id)
                ->where('balance_year', (int)$year)
                ->update('employee_leave_balances', array(
                    'spl_balance' => 3.00,
                    'forced_leave_availed' => 0.00,
                    'solo_parent_balance' => 0.00,
                    'wellness_balance' => 5.00,
                ));

            $count++;
        }

        return array('count' => $count);
    }
    public function get_latest_balance($staff_idnumber)
    {
        return $this->db
            ->where('staff_idnumber', $staff_idnumber)
            ->order_by('balance_year', 'DESC')
            ->order_by('updated_at', 'DESC')
            ->limit(1)
            ->get('leave_balances')
            ->row();
    }
}