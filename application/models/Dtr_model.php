<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dtr_model extends CI_Model
{
    public function create_batch($filename, $imported_by = null)
    {
        $this->db->insert('biometric_import_batches', [
            'filename'    => $filename,
            'imported_by' => $imported_by
        ]);
        return $this->db->insert_id();
    }

    public function update_batch_stats($batch_id, $stats)
    {
        $this->db->where('id', $batch_id)->update('biometric_import_batches', $stats);
    }

    public function insert_raw_log($data)
    {
        return $this->db->insert('biometric_import_logs', $data);
    }

    public function get_imported_logs($limit = 300)
    {
        return $this->db
            ->order_by('log_datetime', 'DESC')
            ->limit($limit)
            ->get('biometric_import_logs')
            ->result();
    }

    public function get_employee_by_user_id($user_id)
    {
        return $this->db
            ->where('user_id', $user_id)
            ->get('employees')
            ->row();
    }

    public function get_employee_by_id($employee_id)
    {
        return $this->db
            ->where('id', $employee_id)
            ->get('employees')
            ->row();
    }

    public function get_employee_by_bio_id($bio_id)
    {
        return $this->db
            ->where('Biometric_ID', $bio_id)
            ->or_where('biometric_id', $bio_id)
            ->get('employees')
            ->row();
    }

    public function get_month_logs($bio_id, $year, $month)
    {
        $start = sprintf('%04d-%02d-01', $year, $month);
        $end   = date('Y-m-t', strtotime($start));

        return $this->db
            ->where('employee_bio_id', $bio_id)
            ->where('log_date >=', $start)
            ->where('log_date <=', $end)
            ->order_by('log_datetime', 'ASC')
            ->get('biometric_import_logs')
            ->result();
    }

    public function clear_month_dtr($employee_id, $year, $month)
    {
        $start = sprintf('%04d-%02d-01', $year, $month);
        $end   = date('Y-m-t', strtotime($start));

        $this->db->where('employee_id', $employee_id);
        $this->db->where('work_date >=', $start);
        $this->db->where('work_date <=', $end);
        $this->db->delete('dtr_daily');
    }

    public function save_daily_dtr($data)
    {
        $existing = $this->db
            ->where('employee_id', $data['employee_id'])
            ->where('work_date', $data['work_date'])
            ->get('dtr_daily')
            ->row();

        if ($existing) {
            $this->db
                ->where('id', $existing->id)
                ->update('dtr_daily', $data);
            return $existing->id;
        }

        $this->db->insert('dtr_daily', $data);
        return $this->db->insert_id();
    }

    public function get_monthly_dtr($employee_id, $year, $month)
    {
        return $this->db
            ->where('employee_id', $employee_id)
            ->where('YEAR(work_date) =', (int)$year, false)
            ->where('MONTH(work_date) =', (int)$month, false)
            ->order_by('work_date', 'ASC')
            ->get('dtr_daily')
            ->result();
    }

    public function get_approved_travels($employee_id, $year, $month)
    {
        $monthStart = sprintf('%04d-%02d-01', $year, $month);
        $monthEnd   = date('Y-m-t', strtotime($monthStart));

        $rows = $this->db
            ->where('employee_id', $employee_id)
            ->where('status', 'Approved')
            ->get('travel_requests')
            ->result();

        $filtered = [];
        foreach ($rows as $row) {
            $dates = $this->expand_inclusive_dates($row->inclusive_date);
            foreach ($dates as $date) {
                if ($date >= $monthStart && $date <= $monthEnd) {
                    $filtered[] = $row;
                    break;
                }
            }
        }

        return $filtered;
    }

    public function build_travel_map($travels, $year, $month)
    {
        $monthStart = sprintf('%04d-%02d-01', $year, $month);
        $monthEnd   = date('Y-m-t', strtotime($monthStart));

        $map = [];

        foreach ($travels as $travel) {
            $dates = $this->expand_inclusive_dates($travel->inclusive_date);

            foreach ($dates as $date) {
                if ($date < $monthStart || $date > $monthEnd) {
                    continue;
                }

                $map[$date] = [
                    'purpose'     => $travel->purpose,
                    'destination' => isset($travel->destination) ? $travel->destination : '',
                    'travel_id'   => $travel->id
                ];
            }
        }

        return $map;
    }

    public function expand_inclusive_dates($inclusiveDate)
    {
        $inclusiveDate = trim((string)$inclusiveDate);
        if ($inclusiveDate === '') {
            return [];
        }

        $dates = [];

        if (!preg_match('/^([A-Za-z]+)\s+(.+),\s*(\d{4})$/', $inclusiveDate, $matches)) {
            return [];
        }

        $monthName = $matches[1];
        $rangePart = trim($matches[2]);
        $year      = (int)$matches[3];

        $month = (int)date('m', strtotime($monthName . ' 1'));

        $segments = preg_split('/\s*&\s*/', $rangePart);

        foreach ($segments as $segment) {
            $segment = trim($segment);

            if (preg_match('/^(\d{1,2})\s*-\s*(\d{1,2})$/', $segment, $m)) {
                $startDay = (int)$m[1];
                $endDay   = (int)$m[2];

                for ($d = $startDay; $d <= $endDay; $d++) {
                    $dates[] = sprintf('%04d-%02d-%02d', $year, $month, $d);
                }
            } elseif (preg_match('/^\d{1,2}$/', $segment)) {
                $day = (int)$segment;
                $dates[] = sprintf('%04d-%02d-%02d', $year, $month, $day);
            }
        }

        return $dates;
    }

    public function summarize_logs_by_day($logs)
    {
        $grouped = [];
        foreach ($logs as $log) {
            $grouped[$log->log_date][] = $log->log_time;
        }

        $result = [];
        foreach ($grouped as $date => $times) {
            sort($times);

            $morning_in = null;
            $morning_out = null;
            $afternoon_in = null;
            $afternoon_out = null;

            foreach ($times as $time) {
                if ($time >= '04:00:00' && $time <= '11:59:59') {
                    if ($morning_in === null) {
                        $morning_in = $time;
                    }
                }

                if ($time >= '12:00:00' && $time <= '12:59:59') {
                    $morning_out = $time;
                }

                if ($time >= '12:01:00' && $time <= '23:59:59') {
                    if ($afternoon_in === null) {
                        $afternoon_in = $time;
                    }
                    $afternoon_out = $time;
                }
            }

            $result[$date] = [
                'morning_in'    => $morning_in,
                'morning_out'   => $morning_out,
                'afternoon_in'  => $afternoon_in,
                'afternoon_out' => $afternoon_out
            ];
        }

        return $result;
    }

    public function create_verification($employee_id, $year, $month, $generated_by = null)
    {
        $code = 'DTR-' . $employee_id . '-' . $year . sprintf('%02d', $month) . '-' . strtoupper(substr(md5(uniqid('', true)), 0, 8));
        $hash = hash('sha256', $code . '|' . $employee_id . '|' . $year . '|' . $month);

        $this->db->insert('dtr_print_verifications', [
            'verification_code' => $code,
            'employee_id'       => $employee_id,
            'year'              => $year,
            'month'             => $month,
            'generated_at'      => date('Y-m-d H:i:s'),
            'generated_by'      => $generated_by,
            'hash_value'        => $hash
        ]);

        return $code;
    }

    public function get_verification($code)
    {
        return $this->db
            ->where('verification_code', $code)
            ->get('dtr_print_verifications')
            ->row();
    }
}