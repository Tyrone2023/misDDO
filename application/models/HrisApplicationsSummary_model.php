<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class HrisApplicationsSummary_model extends CI_Model
{
    private $jobTypes = array(
        1  => 'Elementary',
        2  => 'Secondary',
        3  => 'Junior High School',
        4  => 'Senior High School',
        5  => 'Kindergarten',
        6  => 'IPED Elementary',
        7  => 'IPED Secondary',
        8  => 'IPED Junior High School',
        9  => 'IPED Senior High School',
        10 => 'SNED',
    );

    public function get_school_years()
    {
        $years = array();

        $q1 = $this->db->select('DISTINCT sy AS school_year', false)
            ->from('hris_jobvacancy')
            ->where('sy <>', '')
            ->order_by('sy', 'DESC')
            ->get()
            ->result_array();

        $q2 = $this->db->select('DISTINCT app_year AS school_year', false)
            ->from('hris_applications')
            ->where('app_year <>', '')
            ->order_by('app_year', 'DESC')
            ->get()
            ->result_array();

        foreach ($q1 as $row) {
            $years[] = $row['school_year'];
        }

        foreach ($q2 as $row) {
            $years[] = $row['school_year'];
        }

        $years = array_unique($years);
        rsort($years);

        return $years;
    }

    public function get_latest_sy()
    {
        $years = $this->get_school_years();
        return !empty($years) ? $years[0] : '';
    }

    public function get_job_titles($sy = '')
    {
        $this->db->distinct();
        $this->db->select('jobTitle');
        $this->db->from('hris_jobvacancy');
        $this->db->where('jobTitle <>', '');

        if (!empty($sy)) {
            $this->db->where('sy', $sy);
        }

        $this->db->order_by('jobTitle', 'ASC');

        $rows = $this->db->get()->result_array();

        $titles = array();
        foreach ($rows as $row) {
            $titles[] = trim($row['jobTitle']);
        }

        return $titles;
    }

    public function get_summary_by_position($sy = '', $jobTitle = '')
    {
        $this->db->select("
            j.jobTitle,
            j.job_type,

            COUNT(a.appID) AS total_applicants,

            SUM(
                CASE 
                    WHEN LOWER(TRIM(a.appStatus)) = 'application submitted'
                    THEN 1 ELSE 0
                END
            ) AS application_submitted,

            SUM(
                CASE 
                    WHEN LOWER(TRIM(a.appStatus)) = 'validated'
                         AND IFNULL(a.dq, 0) <> 2
                    THEN 1 ELSE 0
                END
            ) AS validated,

            SUM(
                CASE 
                    WHEN LOWER(TRIM(a.appStatus)) IN ('endorsed for rating','endorsed')
                         AND IFNULL(a.dq, 0) <> 2
                    THEN 1 ELSE 0
                END
            ) AS endorsed_for_rating,

            SUM(
                CASE 
                    WHEN LOWER(TRIM(a.appStatus)) = 'rated'
                         AND IFNULL(a.dq, 0) <> 2
                    THEN 1 ELSE 0
                END
            ) AS rated,

            SUM(
                CASE 
                    WHEN LOWER(TRIM(a.appStatus)) = 'confirmed'
                         AND IFNULL(a.dq, 0) <> 2
                    THEN 1 ELSE 0
                END
            ) AS confirmed,

            SUM(
                CASE 
                    WHEN a.dq = 2
                    THEN 1 ELSE 0
                END
            ) AS dq_count
        ", false);

        $this->db->from('hris_jobvacancy j');
        $this->db->join('hris_applications a', 'a.jobID = j.jobID', 'left');

        if (!empty($sy)) {
            $this->db->where('j.sy', $sy);
            $this->db->group_start();
                $this->db->where('a.app_year', $sy);
                $this->db->or_where('a.app_year IS NULL', null, false);
                $this->db->or_where('a.app_year', '');
            $this->db->group_end();
        }

        if (!empty($jobTitle)) {
            $this->db->where('j.jobTitle', $jobTitle);
        }

        $this->db->group_by(array('j.jobTitle', 'j.job_type'));
        $this->db->order_by('j.jobTitle', 'ASC');
        $this->db->order_by('j.job_type', 'ASC');

        $rows = $this->db->get()->result_array();

        foreach ($rows as &$row) {
            $jobTitleText = trim((string)$row['jobTitle']);
            $jobType      = (int)$row['job_type'];

            $jobtype_label = isset($this->jobTypes[$jobType]) ? $this->jobTypes[$jobType] : '';

            if ($jobTitleText !== '' && $jobtype_label !== '') {
                $row['position_jobtype'] = $jobTitleText . ' - ' . $jobtype_label;
            } elseif ($jobTitleText !== '') {
                $row['position_jobtype'] = $jobTitleText;
            } else {
                $row['position_jobtype'] = $jobtype_label !== '' ? $jobtype_label : 'Unspecified';
            }
        }

        return $rows;
    }
}