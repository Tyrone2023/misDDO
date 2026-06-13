<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Leave_dtr_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function apply_approved_leave_to_dtr($application)
    {
        if (empty($application) || empty($application['staff_idnumber'])) return false;
        if (empty($application['date_from']) || empty($application['date_to'])) return false;

        $staff_idnumber = $application['staff_idnumber'];
        $leave_code     = strtoupper($application['leave_type_code']);
        $date_from      = $application['date_from'];
        $date_to        = $application['date_to'];
        $half_day       = isset($application['half_day']) ? strtoupper($application['half_day']) : 'NONE';

        $period = new DatePeriod(
            new DateTime($date_from),
            new DateInterval('P1D'),
            (new DateTime($date_to))->modify('+1 day')
        );

        foreach ($period as $dt) {
            $dtr_date = $dt->format('Y-m-d');
            if ((int)$dt->format('N') >= 6) continue;

            $existing = $this->db->where('staff_idnumber', $staff_idnumber)
                ->where('dtr_date', $dtr_date)
                ->get('biometric_dtr')
                ->row_array();

            $payload = $this->build_leave_payload($leave_code, $half_day);
            $payload['updated_at'] = date('Y-m-d H:i:s');

            if ($existing) {
                $this->db->where('id', $existing['id'])->update('biometric_dtr', $payload);
            } else {
                $payload['staff_idnumber'] = $staff_idnumber;
                $payload['dtr_date'] = $dtr_date;
                $payload['created_at'] = date('Y-m-d H:i:s');
                $this->db->insert('biometric_dtr', $payload);
            }
        }
        return true;
    }

    private function build_leave_payload($leave_code, $half_day = 'NONE')
    {
        $payload = array(
            'morning_status'   => $leave_code,
            'afternoon_status' => $leave_code,
            'remarks'          => $leave_code
        );

        if ($half_day === 'AM') {
            $payload['afternoon_status'] = null;
        } elseif ($half_day === 'PM') {
            $payload['morning_status'] = null;
        }

        return $payload;
    }
}
