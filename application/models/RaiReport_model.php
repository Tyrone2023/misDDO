<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Storage and source model for the RAI report module.
 * CodeIgniter 3.1.13 / MySQL.
 *
 * Reads appointed applicants from the RQA tables and stores report headers and
 * report-only applicant rows in hris_rai_reports / hris_rai_report_applicants.
 */
class RaiReport_model extends CI_Model
{
    private $recommendations = 'hris_rqa_recommendation';
    private $applications = 'hris_applications';
    private $applicants = 'hris_applicant';
    private $vacancies = 'hris_jobvacancy';
    private $positions = 'hris_positions';
    private $salary_schedules = 'hris_appointment_salary_schedules';
    private $salary_rates = 'hris_appointment_salary_rates';
    private $reports = 'hris_rai_reports';
    private $report_applicants = 'hris_rai_report_applicants';

    private $report_fields = array(
        'report_month',
        'report_year',
        'agency_name',
        'division_name',
        'csc_resolution_no',
        'csc_resolution_date',
        'certifying_officer',
        'certifying_position',
        'appointing_officer',
        'appointing_position',
        'remarks'
    );

    private $date_fields = array(
        'csc_resolution_date'
    );

    private $applicant_fields = array(
        'source_type',
        'source_id',
        'classification_key',
        'classification_label',
        'date_issued',
        'last_name',
        'first_name',
        'name_extension',
        'middle_name',
        'sex',
        'pwd',
        'position_level',
        'position_title',
        'item_number',
        'salary_grade',
        'salary_step',
        'monthly_salary',
        'employment_status',
        'employment_period_from',
        'employment_period_to',
        'nature_of_appointment',
        'publication_from',
        'publication_to',
        'publication_mode',
        'appointment_identification_no',
        'csc_action',
        'csc_action_date',
        'csc_release_date'
    );

    private $applicant_date_fields = array(
        'date_issued',
        'employment_period_from',
        'employment_period_to',
        'publication_from',
        'publication_to',
        'csc_action_date',
        'csc_release_date'
    );

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->ensure_tables();
    }

    /**
     * Create the RAI report tables on first use so no manual migration step is
     * required during deployment.
     */
    public function ensure_tables()
    {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `{$this->reports}` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `report_month` TINYINT UNSIGNED NOT NULL,
                `report_year` SMALLINT UNSIGNED NOT NULL,
                `agency_name` VARCHAR(255) DEFAULT NULL,
                `division_name` VARCHAR(255) DEFAULT NULL,
                `csc_resolution_no` VARCHAR(100) DEFAULT NULL,
                `csc_resolution_date` DATE DEFAULT NULL,
                `certifying_officer` VARCHAR(255) DEFAULT NULL,
                `certifying_position` VARCHAR(255) DEFAULT NULL,
                `appointing_officer` VARCHAR(255) DEFAULT NULL,
                `appointing_position` VARCHAR(255) DEFAULT NULL,
                `remarks` TEXT DEFAULT NULL,
                `created_by` INT UNSIGNED DEFAULT NULL,
                `created_at` DATETIME DEFAULT NULL,
                `updated_at` DATETIME DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `idx_period` (`report_year`, `report_month`),
                KEY `idx_created_by` (`created_by`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");

        $this->db->query("
            CREATE TABLE IF NOT EXISTS `{$this->report_applicants}` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `report_id` INT UNSIGNED NOT NULL,
                `sequence` INT UNSIGNED NOT NULL DEFAULT 0,
                `source_type` VARCHAR(30) NOT NULL DEFAULT 'manual',
                `source_id` INT UNSIGNED DEFAULT NULL,
                `classification_key` VARCHAR(60) DEFAULT NULL,
                `classification_label` VARCHAR(120) DEFAULT NULL,
                `date_issued` DATE DEFAULT NULL,
                `last_name` VARCHAR(150) DEFAULT NULL,
                `first_name` VARCHAR(150) DEFAULT NULL,
                `name_extension` VARCHAR(50) DEFAULT NULL,
                `middle_name` VARCHAR(150) DEFAULT NULL,
                `sex` VARCHAR(10) DEFAULT NULL,
                `pwd` VARCHAR(10) DEFAULT NULL,
                `position_level` VARCHAR(30) DEFAULT NULL,
                `position_title` VARCHAR(255) DEFAULT NULL,
                `item_number` VARCHAR(100) DEFAULT NULL,
                `salary_grade` VARCHAR(30) DEFAULT NULL,
                `salary_step` VARCHAR(30) DEFAULT NULL,
                `monthly_salary` DECIMAL(12,2) DEFAULT NULL,
                `employment_status` VARCHAR(60) DEFAULT NULL,
                `employment_period_from` DATE DEFAULT NULL,
                `employment_period_to` DATE DEFAULT NULL,
                `nature_of_appointment` VARCHAR(60) DEFAULT NULL,
                `publication_from` DATE DEFAULT NULL,
                `publication_to` DATE DEFAULT NULL,
                `publication_mode` VARCHAR(60) DEFAULT NULL,
                `appointment_identification_no` VARCHAR(100) DEFAULT NULL,
                `csc_action` VARCHAR(20) DEFAULT NULL,
                `csc_action_date` DATE DEFAULT NULL,
                `csc_release_date` DATE DEFAULT NULL,
                `created_at` DATETIME DEFAULT NULL,
                `updated_at` DATETIME DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `idx_report` (`report_id`),
                KEY `idx_source` (`source_type`, `source_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");
    }

    public function list_reports()
    {
        return $this->db
            ->select('r.*, (SELECT COUNT(a.id) FROM ' . $this->report_applicants . ' a WHERE a.report_id = r.id) AS applicant_count', false)
            ->from($this->reports . ' r')
            ->order_by('r.report_year', 'DESC')
            ->order_by('r.report_month', 'DESC')
            ->order_by('r.id', 'DESC')
            ->get()
            ->result_array();
    }

    public function get_report($id)
    {
        return $this->db
            ->where('id', (int) $id)
            ->limit(1)
            ->get($this->reports)
            ->row_array();
    }

    public function create_report($data, $created_by = 0)
    {
        $row = $this->clean_report($data);
        $row['created_by'] = $created_by > 0 ? (int) $created_by : null;
        $row['created_at'] = date('Y-m-d H:i:s');
        $row['updated_at'] = date('Y-m-d H:i:s');
        $this->db->insert($this->reports, $row);
        return (int) $this->db->insert_id();
    }

    public function update_report($id, $data)
    {
        $row = $this->clean_report($data);
        $row['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('id', (int) $id)->update($this->reports, $row);
        return $this->db->affected_rows() >= 0;
    }

    public function delete_report($id)
    {
        $this->db->trans_start();
        $this->db->where('report_id', (int) $id)->delete($this->report_applicants);
        $this->db->where('id', (int) $id)->delete($this->reports);
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    public function get_applicants($report_id)
    {
        return $this->db
            ->where('report_id', (int) $report_id)
            ->order_by('sequence', 'ASC')
            ->order_by('id', 'ASC')
            ->get($this->report_applicants)
            ->result_array();
    }

    public function save_applicants($report_id, $items)
    {
        $this->db->trans_start();
        $this->db->where('report_id', (int) $report_id)->delete($this->report_applicants);

        $sequence = 1;
        foreach ($items as $item) {
            $row = $this->clean_applicant($item);
            $row['report_id'] = (int) $report_id;
            $row['sequence'] = $sequence++;
            $row['created_at'] = date('Y-m-d H:i:s');
            $row['updated_at'] = date('Y-m-d H:i:s');
            $this->db->insert($this->report_applicants, $row);
        }

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    private function clean_report($input)
    {
        $row = array();
        foreach ($this->report_fields as $field) {
            $value = isset($input[$field]) && is_scalar($input[$field])
                ? trim((string) $input[$field])
                : '';

            if ($field === 'report_month' || $field === 'report_year') {
                $row[$field] = (int) $value;
                continue;
            }

            if (in_array($field, $this->date_fields, true)) {
                $row[$field] = $value !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)
                    ? $value
                    : null;
                continue;
            }

            $row[$field] = mb_substr($value, 0, 500);
        }
        return $row;
    }

    private function clean_applicant($input)
    {
        $row = array();
        foreach ($this->applicant_fields as $field) {
            $value = isset($input[$field]) && is_scalar($input[$field])
                ? trim((string) $input[$field])
                : '';

            if ($field === 'source_id') {
                $row[$field] = $value !== '' && ctype_digit($value) ? (int) $value : null;
                continue;
            }

            if (in_array($field, $this->applicant_date_fields, true)) {
                $row[$field] = $value !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)
                    ? $value
                    : null;
                continue;
            }

            if ($field === 'monthly_salary') {
                $row[$field] = $value !== '' && is_numeric($value) ? (float) $value : null;
                continue;
            }

            $row[$field] = mb_substr($value, 0, 255);
        }
        return $row;
    }

    /** Default values for the report header. No historic workbook text is copied. */
    public function get_report_defaults()
    {
        $defaults = array(
            'report_month' => (int) date('n'),
            'report_year' => (int) date('Y'),
            'agency_name' => '',
            'division_name' => '',
            'csc_resolution_no' => '',
            'csc_resolution_date' => '',
            'certifying_officer' => '',
            'certifying_position' => '',
            'appointing_officer' => '',
            'appointing_position' => '',
            'remarks' => ''
        );

        // Existing MISDDO settings, when available. Do not guess signatories.
        $this->load->model('SettingsModel');
        if (!method_exists($this->SettingsModel, 'get_mis_settings')) {
            return $defaults;
        }

        $settings = $this->SettingsModel->get_mis_settings();
        if (is_array($settings) && isset($settings[0])) {
            $settings = $settings[0];
        }
        if (is_object($settings)) {
            $settings = (array) $settings;
        }
        if (!is_array($settings)) {
            return $defaults;
        }

        // Safe optional hints only. Actual MISDDO settings may use other keys.
        $defaults['agency_name'] = $this->first_setting($settings, array('agency_name', 'AgencyName', 'agencyName', 'SchoolName'));
        $defaults['division_name'] = $this->first_setting($settings, array('division_name', 'DivisionName', 'divisionName'));
        return $defaults;
    }

    private function first_setting($settings, $keys)
    {
        foreach ($keys as $key) {
            if (isset($settings[$key]) && is_scalar($settings[$key]) && trim((string) $settings[$key]) !== '') {
                return trim((string) $settings[$key]);
            }
        }
        return '';
    }

    /**
     * Paginated candidate search. Only issued/appointed recommendation events.
     * Returns ['rows' => ..., 'total' => ...].
     */
    public function search_appointed_applicants($filters = array())
    {
        $search = mb_substr(trim((string) (isset($filters['search']) ? $filters['search'] : '')), 0, 100);
        $month = (int) (isset($filters['month']) ? $filters['month'] : 0);
        $year = (int) (isset($filters['year']) ? $filters['year'] : 0);
        $limit = max(1, min(100, (int) (isset($filters['limit']) ? $filters['limit'] : 20)));
        $offset = max(0, (int) (isset($filters['offset']) ? $filters['offset'] : 0));

        if ($month < 1 || $month > 12) $month = 0;
        if ($year < 2000 || $year > 2100) $year = 0;

        $this->candidate_query($search, $month, $year, false);
        $total = (int) $this->db->count_all_results();

        $this->candidate_query($search, $month, $year, true);
        $this->db->order_by('r.appointment_issued_at', 'DESC');
        $this->db->order_by('r.id', 'DESC');
        $rows = $this->db->limit($limit, $offset)->get()->result_array();

        $result = array();
        foreach ($rows as $row) {
            $result[] = $this->candidate_summary($row);
        }
        return array('rows' => $result, 'total' => $total);
    }

    /**
     * Fetch all available source fields for one eligible appointment event.
     * Unavailable official RAI values are intentionally returned as blanks.
     */
    public function get_appointed_applicant($recommendation_id)
    {
        $recommendation_id = (int) $recommendation_id;
        if ($recommendation_id < 1) return null;

        $this->candidate_query('', 0, 0, true);
        $row = $this->db->where('r.id', $recommendation_id)->limit(1)->get()->row_array();
        if (empty($row)) return null;

        // ID-based profile link is authoritative. No name-based linking.
        // Profile values are suggestions for this report, not master-data edits.
        $profile = array();
        if (!empty($row['applicant_id'])) {
            $profile = $this->db->where('id', (int) $row['applicant_id'])
                ->limit(1)->get($this->applicants)->row_array();
        }
        if (!is_array($profile)) $profile = array();

        $first = $this->field($profile, array('FirstName'));
        $last = $this->field($profile, array('LastName'));
        $middle = $this->field($profile, array('MiddleName'));
        $extension = $this->field($profile, array('NameExtn'));

        $pwd = '';
        $disability = $this->field($profile, array('disability'));
        if ($disability === '1') $pwd = 'N';
        if ($disability === '2') $pwd = 'Y';

        $sex = strtolower($this->field($profile, array('Sex')));
        $sex = in_array($sex, array('m', 'male'), true) ? 'M'
            : (in_array($sex, array('f', 'female'), true) ? 'F' : '');

        $issued = substr((string) $row['appointment_issued_at'], 0, 10);
        $position = isset($row['position_title']) ? trim((string) $row['position_title']) : '';
        $item = isset($row['item_number']) ? trim((string) $row['item_number']) : '';
        $nature = isset($row['nature_of_appointment']) ? trim((string) $row['nature_of_appointment']) : '';

        // Salary grade comes from hris_positions matched by job title.
        // Step defaults to 1 for new appointments. Monthly salary is looked
        // up from the active salary schedule for that SG + step.
        $salary_grade = '';
        $salary_step = '1';
        $monthly_salary = '';

        $position_sg = isset($row['position_sg']) ? (int) $row['position_sg'] : 0;
        if ($position_sg > 0) {
            $salary_grade = (string) $position_sg;

            // Try to fetch the monthly salary from the active schedule, step 1.
            $rate = $this->lookup_salary_rate($position_sg, 1);
            if ($rate !== '') {
                $monthly_salary = $rate;
            }
        }

        return array(
            'source_type' => 'rqa_recommendation',
            'source_id' => (string) $recommendation_id,
            'source_event_key' => 'rqa_recommendation:' . $recommendation_id,
            'classification_key' => '',
            'classification_label' => '',
            'date_issued' => $issued,
            'last_name' => $last,
            'first_name' => $first,
            'name_extension' => $extension,
            'middle_name' => $middle,
            'sex' => $sex,
            'pwd' => $pwd,
            'position_level' => '',
            'position_title' => $position,
            'item_number' => $item,
            'salary_grade' => $salary_grade,
            'salary_step' => $salary_step,
            'monthly_salary' => $monthly_salary,
            'employment_status' => isset($row['employment_type']) ? trim((string) $row['employment_type']) : '',
            'employment_period_from' => '',
            'employment_period_to' => '',
            'nature_of_appointment' => $nature,
            'publication_from' => '',
            'publication_to' => '',
            'publication_mode' => 'CSC WEBSITE',
            'appointment_identification_no' => '',
            'csc_action' => '',
            'csc_action_date' => '',
            'csc_release_date' => '',
            'source_name' => trim($last . ', ' . $first . ' ' . $middle),
            'source_note' => 'Imported from appointed RQA event; verify suggested values.'
        );
    }

    /**
     * Look up the monthly salary for a given SG + step from the active
     * salary schedule. Returns '' when no active schedule or no matching
     * rate row exists.
     */
    private function lookup_salary_rate($sg, $step)
    {
        $sg = (int) $sg;
        $step = (int) $step;
        if ($sg < 1 || $step < 1) return '';

        if (!$this->db->table_exists($this->salary_schedules) || !$this->db->table_exists($this->salary_rates)) {
            return '';
        }

        $schedule = $this->db
            ->where('is_active', 1)
            ->limit(1)
            ->get($this->salary_schedules)
            ->row_array();

        if (empty($schedule) || empty($schedule['id'])) {
            return '';
        }

        $rate = $this->db
            ->where('schedule_id', (int) $schedule['id'])
            ->where('sg', $sg)
            ->where('step', $step)
            ->limit(1)
            ->get($this->salary_rates)
            ->row_array();

        if (empty($rate) || !isset($rate['monthly_salary'])) {
            return '';
        }

        return (string) $rate['monthly_salary'];
    }

    private function field($row, $keys)
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $row) && $row[$key] !== null) {
                return trim((string) $row[$key]);
            }
        }
        return '';
    }

    private function candidate_summary($row)
    {
        $id = (int) $row['recommendation_id'];
        $name = trim((string) (isset($row['applicant_name']) ? $row['applicant_name'] : ''));
        $sg = isset($row['position_sg']) ? (int) $row['position_sg'] : 0;
        return array(
            'id' => $id,
            'recId' => $id,
            'source_type' => 'rqa_recommendation',
            'source_event_key' => 'rqa_recommendation:' . $id,
            'name' => $name !== '' ? $name : 'Applicant #' . (int) $row['appID'],
            'position' => $this->field($row, array('position_title')),
            'itemNumber' => $this->field($row, array('item_number')),
            'dateIssued' => substr((string) $row['appointment_issued_at'], 0, 10),
            'natureOfAppointment' => $this->field($row, array('nature_of_appointment')),
            'salaryGrade' => $sg > 0 ? (string) $sg : '',
            'salaryStep' => $sg > 0 ? '1' : '',
            'missingData' => $this->field($row, array('nature_of_appointment')) === ''
        );
    }

    /** Shared query builder for the list and individual-record lookups. */
    private function candidate_query($search, $month, $year, $select_fields)
    {
        $this->db->reset_query();
        $this->db->from($this->recommendations . ' r');
        $this->db->join($this->applications . ' a', 'a.appID = r.appID', 'inner');
        $this->db->join($this->vacancies . ' v', 'v.jobID = r.jobID', 'inner');
        $this->db->join($this->applicants . ' p', 'p.id = a.applicant_id', 'left');

        // Select only verified base columns; optional columns use schema guards.
        if ($select_fields) {
            $select = array(
                'r.id AS recommendation_id',
                'r.appID AS appID',
                'a.applicant_id AS applicant_id',
                'r.appointment_issued_at AS appointment_issued_at',
                'v.jobTitle AS position_title'
            );
            $select[] = $this->optional_column($this->recommendations, 'item_number', 'r', 'item_number');
            $select[] = $this->optional_column($this->recommendations, 'nature_of_appointment', 'r', 'nature_of_appointment');
            $select[] = "TRIM(CONCAT_WS(' ', p.FirstName, p.MiddleName, p.LastName, p.NameExtn)) AS applicant_name";
            $select[] = $this->optional_column($this->vacancies, 'empType', 'v', 'employment_type');
            // Salary grade from hris_positions matched by job title
            if ($this->db->field_exists('sg', $this->positions) && $this->db->field_exists('title', $this->positions)) {
                $select[] = 'pos.`sg` AS position_sg';
            } else {
                $select[] = "'' AS `position_sg`";
            }
            $this->db->select(implode(', ', $select), false);

            // Left join hris_positions on title = jobTitle to get the salary grade
            if ($this->db->field_exists('sg', $this->positions) && $this->db->field_exists('title', $this->positions)) {
                $this->db->join($this->positions . ' pos', 'pos.title = v.jobTitle', 'left');
            }
        }

        $this->db->where('r.status', 'appointed');
        $this->db->where('r.appointment_issued_at IS NOT NULL', null, false);
        $this->db->where('r.appointment_issued_at >', '1000-01-01 00:00:00');

        if ($year) {
            $this->db->where('r.appointment_issued_at >=', sprintf('%04d-01-01 00:00:00', $year));
            $this->db->where('r.appointment_issued_at <', sprintf('%04d-01-01 00:00:00', $year + 1));
        }
        if ($month) {
            $this->db->where('MONTH(r.appointment_issued_at) = ' . (int) $month, null, false);
        }

        if ($search !== '') {
            $this->db->group_start();
            $this->db->like('v.jobTitle', $search);
            if ($this->db->field_exists('item_number', $this->recommendations)) {
                $this->db->or_like('r.item_number', $search);
            }
            $this->db->or_like('p.FirstName', $search);
            $this->db->or_like('p.MiddleName', $search);
            $this->db->or_like('p.LastName', $search);
            if (ctype_digit($search)) {
                $this->db->or_where('r.id', (int) $search);
            }
            $this->db->group_end();
        }
    }

    /** Optional columns cannot break the prototype when a deployment lacks them. */
    private function optional_column($table, $column, $alias, $output)
    {
        if ($this->db->field_exists($column, $table)) {
            return $alias . '.`' . $column . '` AS `' . $output . '`';
        }
        return "'' AS `" . $output . '`';
    }
}
