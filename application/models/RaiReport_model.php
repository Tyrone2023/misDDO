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
    private $plantilla = 'hris_plantilla';
    private $hires = 'hris_hire';
    private $hire_statuses = 'hris_hire_stat';
    private $reports = 'hris_rai_reports';
    private $report_applicants = 'hris_rai_report_applicants';

    private $report_fields = array(
        'report_month',
        'report_year',
        'nature_of_appointment',
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
                `nature_of_appointment` VARCHAR(60) DEFAULT NULL,
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

        // Some installations already have the wider, audit-ready RAI table.
        // Add only the report-level field introduced by this screen and map
        // the wider table's officer/remarks columns in report_storage_row().
        if (!$this->db->field_exists('nature_of_appointment', $this->reports)) {
            $this->db->query(
                "ALTER TABLE `{$this->reports}` "
                . "ADD COLUMN `nature_of_appointment` VARCHAR(60) DEFAULT NULL AFTER `report_year`"
            );
        }

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
        $query = $this->db
            ->select('r.*, (SELECT COUNT(a.id) FROM ' . $this->report_applicants . ' a WHERE a.report_id = r.id) AS applicant_count', false)
            ->from($this->reports . ' r');

        if ($this->db->field_exists('deleted_at', $this->reports)) {
            $query->where('r.deleted_at IS NULL', null, false);
        }
        if ($this->db->field_exists('status', $this->reports)) {
            $query->where('r.status !=', 'deleted');
        }

        $rows = $query
            ->order_by('r.report_year', 'DESC')
            ->order_by('r.report_month', 'DESC')
            ->order_by('r.id', 'DESC')
            ->get()
            ->result_array();

        foreach ($rows as &$row) {
            $row = $this->normalize_report($row);
        }
        unset($row);
        return $rows;
    }

    public function get_report($id)
    {
        $row = $this->db
            ->where('id', (int) $id)
            ->limit(1)
            ->get($this->reports)
            ->row_array();

        return empty($row) ? array() : $this->normalize_report($row);
    }

    public function create_report($data, $created_by = 0)
    {
        $row = $this->report_storage_row($this->clean_report($data));
        $user_id = $created_by > 0 ? (int) $created_by : 0;

        if ($this->db->field_exists('scope_key', $this->reports)) {
            $period = sprintf('%04d-%02d', (int) $data['report_year'], (int) $data['report_month']);
            $row['scope_key'] = 'rai:' . ($user_id ?: 'system') . ':' . $period . ':' . substr(sha1(uniqid('', true)), 0, 10);
        }
        if ($this->db->field_exists('report_title', $this->reports)) {
            $row['report_title'] = 'Report on Appointments Issued (RAI)';
        }
        if ($this->db->field_exists('form_name', $this->reports)) {
            $row['form_name'] = 'Enhanced CS Form No. 2';
        }
        if ($this->db->field_exists('form_revision', $this->reports)) {
            $row['form_revision'] = 'Revised 2025';
        }
        if ($this->db->field_exists('checklist_json', $this->reports)) {
            $row['checklist_json'] = $this->default_checklist_json();
        }
        $row['created_by'] = $user_id;
        if ($this->db->field_exists('updated_by', $this->reports)) {
            $row['updated_by'] = $user_id;
        }
        $row['created_at'] = date('Y-m-d H:i:s');
        $row['updated_at'] = date('Y-m-d H:i:s');
        $this->db->insert($this->reports, $row);
        return (int) $this->db->insert_id();
    }

    public function update_report($id, $data)
    {
        $row = $this->report_storage_row($this->clean_report($data));
        if ($this->db->field_exists('updated_by', $this->reports)) {
            $user_id = (int) $this->session->userdata('id');
            if ($user_id > 0) {
                $row['updated_by'] = $user_id;
            }
        }
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
        $rows = $this->db
            ->where('report_id', (int) $report_id)
            ->order_by('sequence', 'ASC')
            ->order_by('id', 'ASC')
            ->get($this->report_applicants)
            ->result_array();

        // Older saved rows may predate the SG/step/source mapping. Preserve
        // report edits, but fill any blank cell from its authoritative RQA
        // appointment event so reopening an old report benefits immediately.
        foreach ($rows as &$row) {
            if (($row['source_type'] ?? '') !== 'rqa_recommendation' || empty($row['source_id'])) {
                continue;
            }
            $source = $this->get_appointed_applicant((int) $row['source_id']);
            if (empty($source)) {
                continue;
            }
            foreach ($this->applicant_fields as $field) {
                if ((!isset($row[$field]) || trim((string) $row[$field]) === '')
                    && isset($source[$field]) && trim((string) $source[$field]) !== '') {
                    $row[$field] = $source[$field];
                }
            }
        }
        unset($row);
        return $rows;
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

    /**
     * The project has used both a compact RAI table and a wider audit-ready
     * table. Keep one UI contract while writing to the columns that exist.
     */
    private function report_storage_row($row)
    {
        $aliases = array(
            'certifying_officer' => array('certifying_officer', 'certifying_officer_name'),
            'certifying_position' => array('certifying_position', 'certifying_officer_title'),
            'appointing_officer' => array('appointing_officer', 'appointing_officer_name'),
            'appointing_position' => array('appointing_position', 'appointing_officer_title'),
            'remarks' => array('remarks', 'remarks_comments')
        );

        $stored = array();
        foreach ($row as $field => $value) {
            $candidates = isset($aliases[$field]) ? $aliases[$field] : array($field);
            foreach ($candidates as $column) {
                if ($this->db->field_exists($column, $this->reports)) {
                    $stored[$column] = $value;
                    break;
                }
            }
        }
        return $stored;
    }

    /** Return the same field names to every controller/view on either schema. */
    private function normalize_report($row)
    {
        $aliases = array(
            'certifying_officer' => array('certifying_officer', 'certifying_officer_name'),
            'certifying_position' => array('certifying_position', 'certifying_officer_title'),
            'appointing_officer' => array('appointing_officer', 'appointing_officer_name'),
            'appointing_position' => array('appointing_position', 'appointing_officer_title'),
            'remarks' => array('remarks', 'remarks_comments')
        );

        foreach ($aliases as $output => $candidates) {
            $value = '';
            foreach ($candidates as $column) {
                if (isset($row[$column]) && $row[$column] !== null && trim((string) $row[$column]) !== '') {
                    $value = (string) $row[$column];
                    break;
                }
            }
            $row[$output] = $value;
        }
        if (!isset($row['nature_of_appointment']) || $row['nature_of_appointment'] === null) {
            $row['nature_of_appointment'] = '';
        }
        return $row;
    }

    public function nature_options()
    {
        return array(
            'Original' => 'Original',
            'Promotion' => 'Promotion',
            'Transfer' => 'Transfer',
            'Reemployment' => 'Reemployment',
            'Reappointment' => 'Reappointment',
            'Reinstatement' => 'Reinstatement',
            'Demotion' => 'Demotion',
            'Renewal' => 'Renewal',
            'Reclassification' => 'Reclassification',
            'Other' => 'Other'
        );
    }

    private function default_checklist_json()
    {
        return json_encode(array(
            'items' => array(
            array('code' => 'appointment_form', 'label' => 'APPOINTMENT FORM (CS Form No. 33-B, Revised 2025) - Certified True Copy of appointment form', 'hrmo_status' => '', 'csc_status' => '', 'note' => ''),
            array('code' => 'plantilla_casual', 'label' => 'PLANTILLA OF CASUAL APPOINTMENT (CS Form No. 34-B, Revised 2025)- Certified True Copy for CSC', 'hrmo_status' => '', 'csc_status' => '', 'note' => ''),
            array('code' => 'personal_data_sheet', 'label' => 'PERSONAL DATA SHEET (CS Form No. 212, Revised 2025)', 'hrmo_status' => '', 'csc_status' => '', 'note' => ''),
            array('code' => 'proof_of_eligibility', 'label' => "PROOF OF ELIGIBILITY - Report of rating/license/certificate of admission to the Bar/ certificate of eligibility/ eligibility card (original copy, authenticated copy, certified true copy, photocopy, scanned copy, or site/screen capture of eligibility using CSC's CSEVS, PRC's LERIS, or SC's Lawyer's List)", 'hrmo_status' => '', 'csc_status' => '', 'note' => ''),
            array('code' => 'position_description_form', 'label' => 'POSITION DESCRIPTION FORM (DBM-CSC Form No. 1, Revised 2017)', 'hrmo_status' => '', 'csc_status' => '', 'note' => ''),
            array('code' => 'panunumpa', 'label' => 'PANUNUMPA SA KATUNGKULAN (SS Porma Blg. 32, Narebisa 2025)', 'hrmo_status' => '', 'csc_status' => '', 'note' => ''),
            array('code' => 'assumption_to_duty', 'label' => 'CERTIFICATE OF ASSUMPTION TO DUTY (CS Form No. 4, Revised 2025)', 'hrmo_status' => '', 'csc_status' => '', 'note' => '')
            ),
            'csc_receiving_officer' => '',
            'csc_receiving_position' => 'CSC FO Receiving Officer'
        ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /** Return a complete, bounded Page 2 checklist payload. */
    public function normalize_checklist($value = null)
    {
        $defaults = json_decode($this->default_checklist_json(), true);
        $decoded = $value;
        if (is_string($decoded)) {
            $decoded = json_decode($decoded, true);
        }
        if (!is_array($decoded)) {
            return $defaults;
        }

        // Earlier reports stored the requirements as a top-level array.
        $source_items = isset($decoded['items']) && is_array($decoded['items'])
            ? $decoded['items']
            : (isset($decoded[0]) && is_array($decoded[0]) ? $decoded : array());

        foreach ($defaults['items'] as $index => &$item) {
            $source = array();
            foreach ($source_items as $candidate) {
                if (is_array($candidate) && isset($candidate['code']) && $candidate['code'] === $item['code']) {
                    $source = $candidate;
                    break;
                }
            }
            if (empty($source) && isset($source_items[$index]) && is_array($source_items[$index])) {
                $source = $source_items[$index];
            }
            foreach (array('hrmo_status', 'csc_status') as $field) {
                $item[$field] = mb_substr(trim((string) ($source[$field] ?? '')), 0, 50);
            }
            $item['note'] = mb_substr(trim((string) ($source['note'] ?? '')), 0, 500);
        }
        unset($item);

        $defaults['csc_receiving_officer'] = mb_substr(
            trim((string) ($decoded['csc_receiving_officer'] ?? '')),
            0,
            150
        );
        $position = trim((string) ($decoded['csc_receiving_position'] ?? ''));
        $defaults['csc_receiving_position'] = mb_substr(
            $position !== '' ? $position : 'CSC FO Receiving Officer',
            0,
            150
        );
        return $defaults;
    }

    public function checklist_defaults()
    {
        return $this->normalize_checklist();
    }

    public function save_checklist($report_id, $value)
    {
        if (!$this->db->field_exists('checklist_json', $this->reports)) {
            return false;
        }
        $payload = $this->normalize_checklist($value);
        $this->db->where('id', (int) $report_id)->update($this->reports, array(
            'checklist_json' => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'updated_at' => date('Y-m-d H:i:s')
        ));
        return $this->db->affected_rows() >= 0;
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

    /** Defaults taken from the active division settings and official RAI form. */
    public function get_report_defaults()
    {
        $defaults = array(
            'report_month' => (int) date('n'),
            'report_year' => (int) date('Y'),
            'nature_of_appointment' => 'Original',
            'agency_name' => 'Department of Education',
            'division_name' => 'Davao De Oro',
            'csc_resolution_no' => '2100919',
            'csc_resolution_date' => '2021-11-19',
            'certifying_officer' => '',
            'certifying_position' => '',
            'appointing_officer' => '',
            'appointing_position' => '',
            'remarks' => ''
        );

        // Existing MISDDO settings are authoritative for agency/signatories.
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
        $agency = $this->first_setting($settings, array('agency', 'agency_name', 'AgencyName', 'agencyName', 'SchoolName'));
        $division = $this->first_setting($settings, array('division', 'division_name', 'DivisionName', 'divisionName'));
        if ($agency !== '') $defaults['agency_name'] = $agency;
        if ($division !== '') $defaults['division_name'] = $division;
        $defaults['certifying_officer'] = $this->first_setting($settings, array('sigSR', 'personnelCertification'));
        $defaults['certifying_position'] = $this->first_setting($settings, array('sigSRPosition', 'personnelCertPostion'));
        $defaults['appointing_officer'] = $this->first_setting($settings, array('sds'));
        $defaults['appointing_position'] = $this->first_setting($settings, array('sdsPosition'));
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

        $first = $this->field($row, array('FirstName'));
        $last = $this->field($row, array('LastName'));
        $middle = $this->field($row, array('MiddleName'));
        $extension = $this->field($row, array('NameExtn'));
        if (preg_match('#^(n/?a|none|-+|\.)$#i', $extension)) {
            $extension = '';
        }

        $pwd = '';
        $disability = $this->field($row, array('disability'));
        if ($disability === '1') $pwd = 'N';
        if ($disability === '2') $pwd = 'Y';

        $sex = strtolower($this->field($row, array('Sex')));
        $sex = in_array($sex, array('m', 'male'), true) ? 'M'
            : (in_array($sex, array('f', 'female'), true) ? 'F' : '');

        $issued = substr((string) $row['appointment_issued_at'], 0, 10);
        $position = $this->field($row, array('position_title'));
        $item = $this->field($row, array('item_number', 'vacancy_item_number', 'profile_item_number'));
        $nature = $this->field($row, array('nature_of_appointment', 'hire_nature'));
        $salary = $this->salary_values_from_row($row);

        $employment = $this->field($row, array('hire_employment_status', 'employment_type', 'profile_employment_status'));
        $employment = trim((string) preg_replace('/\s+Position$/i', '', $employment));

        $position_level = $this->normalize_position_level($this->field($row, array('profile_position_level')));
        if ($position_level === '' && in_array((int) ($row['position_group'] ?? 0), array(1, 2, 3), true)) {
            $position_level = '2nd';
        }

        $publication_from = $this->source_date($this->field($row, array('hire_publication_from')));
        $publication_to = $this->source_date($this->field($row, array('hire_publication_to')));
        $publication_mode = '';
        if ($this->field($row, array('hire_publication_mode')) === '1') {
            $publication_mode = 'CSC JOB PORTAL';
        }

        $classification = $this->classification_for($employment, $nature);

        return array(
            'source_type' => 'rqa_recommendation',
            'source_id' => (string) $recommendation_id,
            'source_event_key' => 'rqa_recommendation:' . $recommendation_id,
            'classification_key' => $classification['key'],
            'classification_label' => $classification['label'],
            'date_issued' => $issued,
            'last_name' => $last,
            'first_name' => $first,
            'name_extension' => $extension,
            'middle_name' => $middle,
            'sex' => $sex,
            'pwd' => $pwd,
            'position_level' => $position_level,
            'position_title' => $position,
            'item_number' => $item,
            'salary_grade' => $salary['grade'],
            'salary_step' => $salary['step'],
            'monthly_salary' => $salary['monthly'],
            'employment_status' => $employment,
            'employment_period_from' => '',
            'employment_period_to' => '',
            'nature_of_appointment' => $nature,
            'publication_from' => $publication_from,
            'publication_to' => $publication_to,
            'publication_mode' => $publication_mode,
            'appointment_identification_no' => '',
            'csc_action' => '',
            'csc_action_date' => '',
            'csc_release_date' => '',
            'source_name' => trim($last . ', ' . $first . ' ' . $middle),
            'source_note' => 'Imported from the appointed RQA record and matching HRIS position, plantilla, hire, and salary-schedule columns.'
        );
    }

    private function salary_values_from_row($row)
    {
        $grade = 0;
        foreach (array('hire_salary_grade', 'plantilla_salary_grade', 'profile_salary_grade', 'position_sg') as $field) {
            $candidate = isset($row[$field]) && is_numeric($row[$field]) ? (int) $row[$field] : 0;
            if ($candidate >= 1 && $candidate <= 33) {
                $grade = $candidate;
                break;
            }
        }

        $step = 0;
        foreach (array('hire_salary_step', 'plantilla_salary_step', 'profile_salary_step') as $field) {
            $candidate = isset($row[$field]) && is_numeric($row[$field]) ? (int) $row[$field] : 0;
            if ($candidate >= 1 && $candidate <= 8) {
                $step = $candidate;
                break;
            }
        }
        if ($grade > 0 && $step < 1) {
            $step = 1;
        }

        $monthly = '';
        foreach (array('hire_monthly_salary') as $field) {
            if (isset($row[$field]) && is_numeric($row[$field]) && (float) $row[$field] > 0) {
                $monthly = (string) $row[$field];
                break;
            }
        }
        if ($monthly === '' && $grade > 0 && $step > 0) {
            $monthly = $this->lookup_salary_rate($grade, $step);
        }
        if ($monthly === '' && isset($row['plantilla_annual_salary']) && is_numeric($row['plantilla_annual_salary'])
            && (float) $row['plantilla_annual_salary'] > 0) {
            $monthly = (string) round((float) $row['plantilla_annual_salary'] / 12, 2);
        }
        if ($monthly === '' && isset($row['profile_monthly_salary']) && is_numeric($row['profile_monthly_salary'])
            && (float) $row['profile_monthly_salary'] > 0) {
            $monthly = (string) $row['profile_monthly_salary'];
        }

        return array(
            'grade' => $grade > 0 ? (string) $grade : '',
            'step' => $step > 0 ? (string) $step : '',
            'monthly' => $monthly
        );
    }

    private function normalize_position_level($value)
    {
        $value = strtolower(trim((string) $value));
        if ($value === '') return '';
        if (preg_match('/(^|\D)(2|second)(\D|$)/', $value)) return '2nd';
        if (preg_match('/(^|\D)(1|first)(\D|$)/', $value)) return '1st';
        return '';
    }

    private function source_date($value)
    {
        $value = trim((string) $value);
        if ($value === '' || $value === '0000-00-00') return '';
        if (preg_match('/^\d{4}-\d{2}-\d{2}/', $value)) return substr($value, 0, 10);
        $time = strtotime($value);
        return $time ? date('Y-m-d', $time) : '';
    }

    private function classification_for($employment, $nature)
    {
        $employment = trim((string) $employment);
        $nature = trim((string) $nature);
        $label = trim(implode(' — ', array_filter(array($employment, $nature), function ($value) {
            return $value !== '';
        })));
        if ($label === '') {
            return array('key' => 'unclassified', 'label' => 'Unclassified');
        }
        $key = strtolower((string) preg_replace('/[^a-z0-9]+/i', '_', $label));
        return array('key' => trim($key, '_'), 'label' => $label);
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
        $salary = $this->salary_values_from_row($row);
        return array(
            'id' => $id,
            'recId' => $id,
            'source_type' => 'rqa_recommendation',
            'source_event_key' => 'rqa_recommendation:' . $id,
            'name' => $name !== '' ? $name : 'Applicant #' . (int) $row['appID'],
            'position' => $this->field($row, array('position_title')),
            'itemNumber' => $this->field($row, array('item_number', 'vacancy_item_number', 'profile_item_number')),
            'dateIssued' => substr((string) $row['appointment_issued_at'], 0, 10),
            'natureOfAppointment' => $this->field($row, array('nature_of_appointment', 'hire_nature')),
            'salaryGrade' => $salary['grade'],
            'salaryStep' => $salary['step'],
            'missingData' => $this->field($row, array('nature_of_appointment', 'hire_nature')) === ''
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
                'v.jobTitle AS position_title',
                'p.FirstName AS FirstName',
                'p.MiddleName AS MiddleName',
                'p.LastName AS LastName',
                'p.NameExtn AS NameExtn',
                'p.Sex AS Sex'
            );
            $select[] = $this->optional_column($this->recommendations, 'item_number', 'r', 'item_number');
            $select[] = $this->optional_column($this->recommendations, 'nature_of_appointment', 'r', 'nature_of_appointment');
            $select[] = "TRIM(CONCAT_WS(' ', p.FirstName, p.MiddleName, p.LastName, p.NameExtn)) AS applicant_name";
            $select[] = $this->optional_column($this->vacancies, 'empType', 'v', 'employment_type');
            $select[] = $this->optional_column($this->vacancies, 'itemNo', 'v', 'vacancy_item_number');
            $select[] = $this->optional_column($this->vacancies, 'position', 'v', 'position_group');
            $select[] = $this->optional_column($this->applicants, 'itemNo', 'p', 'profile_item_number');
            $select[] = $this->optional_column($this->applicants, 'sgNo', 'p', 'profile_salary_grade');
            $select[] = $this->optional_column($this->applicants, 'stepNo', 'p', 'profile_salary_step');
            $select[] = $this->optional_column($this->applicants, 'actualSalary', 'p', 'profile_monthly_salary');
            $select[] = $this->optional_column($this->applicants, 'empStatus', 'p', 'profile_employment_status');
            $select[] = $this->optional_column($this->applicants, 'csLevel', 'p', 'profile_position_level');
            $select[] = $this->optional_column($this->applicants, 'disability', 'p', 'disability');

            // Prefer the position_id relation, with title matching retained for
            // older vacancies whose position_id was never populated.
            if ($this->db->field_exists('sg', $this->positions) && $this->db->field_exists('title', $this->positions)) {
                $select[] = 'COALESCE(NULLIF(pos_id.`sg`, 0), NULLIF(pos_title.`sg`, 0), 0) AS position_sg';
            } else {
                $select[] = "'' AS `position_sg`";
            }

            if ($this->db->field_exists('sg', $this->positions) && $this->db->field_exists('title', $this->positions)) {
                if ($this->db->field_exists('position_id', $this->vacancies)) {
                    $this->db->join($this->positions . ' pos_id', 'pos_id.id = v.position_id', 'left');
                } else {
                    $this->db->join($this->positions . ' pos_id', '1 = 0', 'left', false);
                }
                $this->db->join($this->positions . ' pos_title', 'pos_title.title = v.jobTitle', 'left');
            }

            if ($this->db->table_exists($this->plantilla)
                && $this->db->field_exists('itemNo', $this->plantilla)
                && $this->db->field_exists('item_number', $this->recommendations)) {
                $this->db->join($this->plantilla . ' pl', 'pl.itemNo = r.item_number', 'left');
                $select[] = $this->optional_column($this->plantilla, 'sg', 'pl', 'plantilla_salary_grade');
                $select[] = $this->optional_column($this->plantilla, 'step', 'pl', 'plantilla_salary_step');
                $select[] = $this->optional_column($this->plantilla, 'authAnnualSalary', 'pl', 'plantilla_annual_salary');
            } else {
                $select[] = "'' AS `plantilla_salary_grade`";
                $select[] = "'' AS `plantilla_salary_step`";
                $select[] = "'' AS `plantilla_annual_salary`";
            }

            if ($this->db->table_exists($this->hires)
                && $this->db->field_exists('appID', $this->hires)) {
                $hire_join = 'h.id = (SELECT MAX(h2.id) FROM `' . $this->hires . '` h2 WHERE h2.appID = r.appID)';
                $this->db->join($this->hires . ' h', $hire_join, 'left', false);
                $select[] = $this->optional_column($this->hires, 'sg', 'h', 'hire_salary_grade');
                $select[] = $this->optional_column($this->hires, 'step', 'h', 'hire_salary_step');
                $select[] = $this->optional_column($this->hires, 'salary', 'h', 'hire_monthly_salary');
                $select[] = $this->optional_column($this->hires, 'date_pub_from', 'h', 'hire_publication_from');
                $select[] = $this->optional_column($this->hires, 'date_pub_to', 'h', 'hire_publication_to');
                $select[] = $this->optional_column($this->hires, 'pub_at', 'h', 'hire_publication_mode');

                if ($this->db->table_exists($this->hire_statuses)
                    && $this->db->field_exists('description', $this->hire_statuses)) {
                    $this->db->join($this->hire_statuses . ' hire_emp', 'hire_emp.id = h.emp_stat', 'left');
                    $this->db->join($this->hire_statuses . ' hire_nature', 'hire_nature.id = h.appointment', 'left');
                    $select[] = 'hire_emp.description AS hire_employment_status';
                    $select[] = 'hire_nature.description AS hire_nature';
                } else {
                    $select[] = "'' AS `hire_employment_status`";
                    $select[] = "'' AS `hire_nature`";
                }
            } else {
                $select[] = "'' AS `hire_salary_grade`";
                $select[] = "'' AS `hire_salary_step`";
                $select[] = "'' AS `hire_monthly_salary`";
                $select[] = "'' AS `hire_publication_from`";
                $select[] = "'' AS `hire_publication_to`";
                $select[] = "'' AS `hire_publication_mode`";
                $select[] = "'' AS `hire_employment_status`";
                $select[] = "'' AS `hire_nature`";
            }

            $this->db->select(implode(', ', $select), false);
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
