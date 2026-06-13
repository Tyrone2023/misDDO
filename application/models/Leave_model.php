<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Leave_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('Leave_signatory_model');
    }

    public function count_working_days($date_from, $date_to, $half_day = 'NONE')
    {
        $start = new DateTime($date_from);
        $end = new DateTime($date_to);
        $end->modify('+1 day');

        $period = new DatePeriod($start, new DateInterval('P1D'), $end);

        $days = 0;

        foreach ($period as $dt) {
            $day_of_week = (int)$dt->format('N');
            if ($day_of_week >= 6) {
                continue;
            }
            $days++;
        }

        if ($half_day === 'AM' || $half_day === 'PM') {
            return $days > 0 ? 0.5 : 0;
        }

        return (float)$days;
    }

    public function create_application($data, $half_day = 'NONE', $commutation = 'REQUESTED')
    {
        $insert_data = $data;
        $insert_data['time_from'] = null;
        $insert_data['time_to'] = null;

        // 🔧 Get employee info
        $staff = $this->db
            ->where('IDNumber', $data['staff_idnumber'])
            ->get('hris_staff')
            ->row();

        $leave_days = (int)($data['working_days'] ?? 0);

        $certify = '';
        $recommend = '';
        $approve = '';

        if ($staff) {
            $department = strtolower(trim((string)($staff->Department ?? '')));
            $position   = strtolower(trim((string)($staff->empPosition ?? '')));
            $school_id  = trim((string)($staff->schoolID ?? ''));

            // School Head
            if (
                strpos($position, 'principal') !== false ||
                strpos($position, 'school head') !== false ||
                strpos($position, 'head teacher') !== false
            ) {
                $certify   = 'admin officer v';
                $recommend = 'asds';
                $approve   = 'sds';
            }

            // School Personnel
            elseif (!empty($school_id)) {
                if ($leave_days <= 60) {
                    $certify   = 'admin officer v';
                    $recommend = 'school';
                    $approve   = 'asds';
                } else {
                    $certify   = 'admin officer v';
                    $recommend = 'asds';
                    $approve   = 'sds';
                }
            }

            // OSDS
            elseif (
                strpos($department, 'osds') !== false ||
                strpos($department, 'office of the schools division superintendent') !== false
            ) {
                $certify   = 'human resource admin';
                $recommend = 'admin officer v';
                $approve   = 'asds';
            }

            // CID
            elseif (
                strpos($department, 'cid') !== false ||
                strpos($department, 'curriculum') !== false
            ) {
                $certify   = 'admin officer v';
                $recommend = 'cid';
                $approve   = 'asds';
            }

            // SGOD
            elseif (
                strpos($department, 'sgod') !== false ||
                strpos($department, 'school governance') !== false
            ) {
                $certify   = 'admin officer v';
                $recommend = 'sgod';
                $approve   = 'asds';
            }

            // fallback
            else {
                $certify   = 'human resource admin';
                $recommend = 'admin officer v';
                $approve   = 'asds';
            }
        }

        $insert_data['certify_by_position']   = $certify;
        $insert_data['recommend_by_position'] = $recommend;
        $insert_data['approve_by_position']   = $approve;

        $this->db->insert('leave_applications', $insert_data);
        $leave_id = $this->db->insert_id();

        if (!$leave_id) {
            return 0;
        }

        return $leave_id;
    }

    public function approve_application($id, $approver)
    {
        $application = $this->db->where('id', $id)
            ->get('leave_applications')
            ->row_array();

        if (!$application) {
            return array(
                'success' => false,
                'message' => 'Leave application not found.'
            );
        }

        $this->db->where('id', $id)
            ->update('leave_applications', array(
                'status'      => 'APPROVED',
                'approved_by' => $approver,
                'approved_at' => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s')
            ));

        $application = $this->db->where('id', $id)
            ->get('leave_applications')
            ->row_array();

        return array(
            'success' => true,
            'application' => $application
        );
    }

    public function get_signatory_queue($logged_idnumber, $logged_position)
    {
        $logged_position = strtolower(trim((string)$logged_position));

        $this->db->select('
            leave_applications.*,
            hris_staff.LastName,
            hris_staff.FirstName,
            hris_staff.MiddleName,
            hris_staff.Department,
            hris_staff.empPosition,
            hris_staff.schoolID
        ');
        $this->db->from('leave_applications');
        $this->db->join('hris_staff', 'hris_staff.IDNumber = leave_applications.staff_idnumber', 'left');

        if ($logged_position === 'human resource admin') {

            $this->db->where_in('leave_applications.status', array('DRAFT', 'PENDING'));

            $this->db->group_start();
                $this->db->where('LOWER(leave_applications.certify_by_position)', 'human resource admin');

                // fallback for old wellness applications with blank certifier
                $this->db->or_group_start();
                    $this->db->where('UPPER(leave_applications.leave_type_code)', 'WELLNESS');
                    $this->db->group_start();
                        $this->db->where('leave_applications.certify_by_position IS NULL', null, false);
                        $this->db->or_where('leave_applications.certify_by_position', '');
                    $this->db->group_end();
                $this->db->group_end();
            $this->db->group_end();

        } elseif ($logged_position === 'admin officer v') {

            $this->db->group_start();

                $this->db->group_start();
                    $this->db->where_in('leave_applications.status', array('DRAFT', 'PENDING'));
                    $this->db->where('LOWER(leave_applications.certify_by_position)', 'admin officer v');
                $this->db->group_end();

                $this->db->or_group_start();
                    $this->db->where('leave_applications.status', 'CERTIFIED');
                    $this->db->where('LOWER(leave_applications.recommend_by_position)', 'admin officer v');
                $this->db->group_end();

            $this->db->group_end();

        } elseif ($logged_position === 'asds') {

            $this->db->group_start();

                $this->db->group_start();
                    $this->db->where('leave_applications.status', 'CERTIFIED');
                    $this->db->where('LOWER(leave_applications.recommend_by_position)', 'asds');
                $this->db->group_end();

                $this->db->or_group_start();
                    $this->db->where('leave_applications.status', 'RECOMMENDED');
                    $this->db->where('LOWER(leave_applications.approve_by_position)', 'asds');
                $this->db->group_end();

            $this->db->group_end();

        } elseif ($logged_position === 'sds') {

            $this->db->group_start();

                $this->db->group_start();
                    $this->db->where('leave_applications.status', 'CERTIFIED');
                    $this->db->where('LOWER(leave_applications.recommend_by_position)', 'sds');
                $this->db->group_end();

                $this->db->or_group_start();
                    $this->db->where('leave_applications.status', 'RECOMMENDED');
                    $this->db->where('LOWER(leave_applications.approve_by_position)', 'sds');
                $this->db->group_end();

            $this->db->group_end();

        } elseif (in_array($logged_position, array('cid', 'sgod', 'school'), true)) {

            $this->db->where('leave_applications.status', 'CERTIFIED');
            $this->db->where('LOWER(leave_applications.recommend_by_position)', $logged_position);

        } else {
            $this->db->where('1 = 0');
        }

        $this->db->order_by('leave_applications.id', 'DESC');

        return $this->db->get()->result();
    }

    public function update_status_after_signatory_action($leave_id)
    {
        $rows = $this->db->where('leave_application_id', $leave_id)
            ->get('leave_application_signatories')
            ->result_array();

        if (empty($rows)) {
            return;
        }

        $has_certified = false;
        $has_recommended = false;
        $has_approved = false;

        foreach ($rows as $row) {
            if ($row['signatory_role'] === 'HR_CERTIFICATION' && $row['action_status'] === 'CERTIFIED') {
                $has_certified = true;
            }

            if ($row['signatory_role'] === 'RECOMMENDING_AUTHORITY' && $row['action_status'] === 'RECOMMENDED') {
                $has_recommended = true;
            }

            if ($row['signatory_role'] === 'APPROVING_AUTHORITY' && $row['action_status'] === 'APPROVED') {
                $has_approved = true;
            }
        }

        $new_status = 'PENDING';

        if ($has_certified && !$has_recommended) {
            $new_status = 'CERTIFIED';
        }

        if ($has_certified && $has_recommended && !$has_approved) {
            $new_status = 'RECOMMENDED';
        }

        if ($has_certified && $has_recommended && $has_approved) {
            $new_status = 'APPROVED';
        }

        $this->db->where('id', $leave_id)
            ->update('leave_applications', array(
                'status' => $new_status,
                'updated_at' => date('Y-m-d H:i:s')
            ));
    }

    public function reject_application($id, $approver, $reason = '')
    {
        $this->db->where('id', $id)
            ->update('leave_applications', array(
                'status' => 'REJECTED',
                'approved_by' => $approver,
                'rejection_reason' => $reason,
                'updated_at' => date('Y-m-d H:i:s')
            ));
    }

    public function get_application_admin_view($id)
    {
        $this->db->select("
            leave_applications.*,
            emp.LastName AS emp_lastname,
            emp.FirstName AS emp_firstname,
            emp.MiddleName AS emp_middlename,
            emp.Department AS emp_department,
            emp.empPosition AS emp_position,
            emp.schoolID AS emp_school_id,
            emp.sgNo AS emp_sgNo,
            emp.stepNo AS emp_stepNo,
            emp.esig AS emp_esig,
            payroll_salary.salary AS salary,
            uc.username AS certifier_username,
            sc.LastName AS cert_lastname,
            sc.FirstName AS cert_firstname,
            sc.MiddleName AS cert_middlename,
            sc.empPosition AS cert_position,
            sc.esig AS cert_esig,
            ur.username AS recommender_username,
            sr.LastName AS rec_lastname,
            sr.FirstName AS rec_firstname,
            sr.MiddleName AS rec_middlename,
            sr.empPosition AS rec_position,
            sr.esig AS rec_esig,
            ua.username AS approver_username,
            sa.LastName AS app_lastname,
            sa.FirstName AS app_firstname,
            sa.MiddleName AS app_middlename,
            sa.empPosition AS app_position,
            sa.esig AS app_esig
        ", false);

        $this->db->from('leave_applications');
        $this->db->join('hris_staff emp', 'emp.IDNumber = leave_applications.staff_idnumber', 'left');
        $this->db->join('payroll_salary', 'payroll_salary.sgNo = emp.sgNo AND payroll_salary.stepNo = emp.stepNo', 'left');
        $this->db->join('users uc', 'uc.id = leave_applications.certified_by', 'left');
        $this->db->join('hris_staff sc', 'sc.IDNumber = uc.username', 'left');
        $this->db->join('users ur', 'ur.id = leave_applications.recommended_by', 'left');
        $this->db->join('hris_staff sr', 'sr.IDNumber = ur.username', 'left');
        $this->db->join('users ua', 'ua.id = leave_applications.approved_by', 'left');
        $this->db->join('hris_staff sa', 'sa.IDNumber = ua.username', 'left');

        $this->db->where('leave_applications.id', (int)$id);
        $this->db->limit(1);

        return $this->db->get()->row();
    }

    public function get_application_for_view($id, $staff_idnumber)
    {
        $this->db->select("
            leave_applications.*,
            emp.LastName AS emp_lastname,
            emp.FirstName AS emp_firstname,
            emp.MiddleName AS emp_middlename,
            emp.Department AS emp_department,
            emp.empPosition AS emp_position,
            emp.schoolID AS emp_school_id,
            emp.sgNo AS emp_sgNo,
            emp.stepNo AS emp_stepNo,
            emp.esig AS emp_esig,
            payroll_salary.salary AS salary,
            uc.username AS certifier_username,
            sc.LastName AS cert_lastname,
            sc.FirstName AS cert_firstname,
            sc.MiddleName AS cert_middlename,
            sc.empPosition AS cert_position,
            sc.esig AS cert_esig,
            ur.username AS recommender_username,
            sr.LastName AS rec_lastname,
            sr.FirstName AS rec_firstname,
            sr.MiddleName AS rec_middlename,
            sr.empPosition AS rec_position,
            sr.esig AS rec_esig,
            ua.username AS approver_username,
            sa.LastName AS app_lastname,
            sa.FirstName AS app_firstname,
            sa.MiddleName AS app_middlename,
            sa.empPosition AS app_position,
            sa.esig AS app_esig
        ", false);

        $this->db->from('leave_applications');
        $this->db->join('hris_staff emp', 'emp.IDNumber = leave_applications.staff_idnumber', 'left');
        $this->db->join('payroll_salary', 'payroll_salary.sgNo = emp.sgNo AND payroll_salary.stepNo = emp.stepNo', 'left');
        $this->db->join('users uc', 'uc.id = leave_applications.certified_by', 'left');
        $this->db->join('hris_staff sc', 'sc.IDNumber = uc.username', 'left');
        $this->db->join('users ur', 'ur.id = leave_applications.recommended_by', 'left');
        $this->db->join('hris_staff sr', 'sr.IDNumber = ur.username', 'left');
        $this->db->join('users ua', 'ua.id = leave_applications.approved_by', 'left');
        $this->db->join('hris_staff sa', 'sa.IDNumber = ua.username', 'left');

        $this->db->where('leave_applications.id', (int)$id);
        $this->db->where('leave_applications.staff_idnumber', trim((string)$staff_idnumber));
        $this->db->limit(1);

        return $this->db->get()->row();
    }

}