<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

/**
 * Stores office-authored appointment templates and merges appointed-applicant
 * data into them without recreating their Excel/Word layout in HTML.
 */
class Appointment_document_model extends CI_Model
{
    private $templateTable = 'hris_appointment_templates';
    private $editTable = 'hris_appointment_report_edits';
    private $salaryScheduleTable = 'hris_appointment_salary_schedules';
    private $salaryRateTable = 'hris_appointment_salary_rates';
    private $guideSalaryFile = 'NBC 601 (01012026).xlsx';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function position_groups()
    {
        return [
            1 => 'Teaching',
            2 => 'School Administration',
            3 => 'Related Teaching',
            4 => 'Non-Teaching',
        ];
    }

    public function natures($includeAll = false)
    {
        $natures = [
            'Original' => 'Original',
            'Promotion' => 'Promotion',
            'Transfer' => 'Transfer',
            'Reemployment' => 'Reemployment',
            'Reappointment' => 'Reappointment',
            'Reinstatement' => 'Reinstatement',
            'Demotion' => 'Demotion',
            'Renewal' => 'Renewal',
        ];

        return $includeAll ? ['ALL' => 'All Natures'] + $natures : $natures;
    }

    public function document_types()
    {
        return [
            'appointment' => 'Appointment (CS Form No. 33-B)',
            'assumption' => 'Certification of Assumption to Duty',
            'assignment' => 'Assignment Order',
        ];
    }

    public function placeholders()
    {
        return [
            'APPLICANT_NAME' => 'Complete applicant name',
            'APPLICANT_NAME_UPPER' => 'Complete applicant name in uppercase',
            'FIRST_NAME' => 'First name',
            'MIDDLE_NAME' => 'Middle name',
            'MIDDLE_INITIAL' => 'Middle initial',
            'LAST_NAME' => 'Last name',
            'NAME_EXTENSION' => 'Name extension',
            'PREFIX' => 'Mr./Ms./Mrs.',
            'POSITION_TITLE' => 'Position title',
            'POSITION_GROUP' => 'Position group',
            'NATURE_OF_APPOINTMENT' => 'Original, Promotion, Reemployment, etc.',
            'EMPLOYMENT_STATUS' => 'Permanent, Temporary, etc.',
            'ITEM_NUMBER' => 'Plantilla/item number',
            'SALARY_GRADE' => 'Salary grade',
            'STEP' => 'Salary step',
            'MONTHLY_SALARY' => 'Monthly salary amount',
            'MONTHLY_SALARY_WORDS' => 'Monthly salary in words',
            'SCHOOL_NAME' => 'Assigned school',
            'DISTRICT' => 'School district',
            'COMPLETE_ADDRESS' => 'Applicant complete address',
            'MUNICIPALITY' => 'Applicant municipality/city',
            'PROVINCE' => 'Applicant province',
            'CONTACT_NUMBER' => 'Applicant contact number',
            'EMAIL' => 'Applicant email',
            'DATE_HIRED' => 'Date hired/assumption date',
            'DATE_ISSUED' => 'Appointment issuance date',
            'VACANCY_DESCRIPTION' => 'Vacancy description',
            'DEPARTMENT' => 'Office/department/unit',
            'REGION' => 'Regional office name',
            'DIVISION' => 'Schools division name',
            'DIVISION_ADDRESS' => 'Schools division address',
            'AGENCY' => 'Agency name',
            'SDS_NAME' => 'Schools Division Superintendent',
            'SDS_POSITION' => 'SDS position title',
            'HRMO_NAME' => 'Highest-ranking HRMO',
            'HRMO_POSITION' => 'HRMO position title',
            'ASDS_NAME' => 'Assistant SDS / HRMPSB Chairperson',
            'DATE_SIGNING' => 'Date of signing (today)',
            'VICE' => 'Previous incumbent/vice value',
            'PAGE' => 'Plantilla page',
        ];
    }

    public function ensure_schema()
    {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `{$this->templateTable}` (
              `id` INT(11) NOT NULL AUTO_INCREMENT,
              `position_group` TINYINT(3) NOT NULL,
              `nature_of_appointment` VARCHAR(40) NOT NULL,
              `document_type` VARCHAR(30) NOT NULL,
              `original_name` VARCHAR(255) NOT NULL,
              `stored_path` VARCHAR(500) NOT NULL,
              `extension` VARCHAR(10) NOT NULL,
              `file_size` INT(11) DEFAULT NULL,
              `is_guide` TINYINT(1) NOT NULL DEFAULT 0,
              `uploaded_by` INT(11) DEFAULT NULL,
              `created_at` DATETIME DEFAULT NULL,
              `updated_at` DATETIME DEFAULT NULL,
              PRIMARY KEY (`id`),
              UNIQUE KEY `uniq_appointment_template` (`position_group`, `nature_of_appointment`, `document_type`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8
        ");

        // Corrections made on the print page, kept so the document can be
        // reopened and reprinted exactly as last edited.
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `{$this->editTable}` (
              `id` INT(11) NOT NULL AUTO_INCREMENT,
              `rec_key` VARCHAR(40) NOT NULL,
              `document_type` VARCHAR(30) NOT NULL,
              `nature_of_appointment` VARCHAR(40) NOT NULL,
              `template_id` INT(11) DEFAULT NULL,
              `kind` VARCHAR(20) NOT NULL,
              `style` MEDIUMTEXT,
              `pages` LONGTEXT NOT NULL,
              `saved_by` INT(11) DEFAULT NULL,
              `created_at` DATETIME DEFAULT NULL,
              `updated_at` DATETIME DEFAULT NULL,
              PRIMARY KEY (`id`),
              UNIQUE KEY `uniq_appointment_report_edit` (`rec_key`, `document_type`, `nature_of_appointment`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8
        ");

        // Monthly salary schedule (SG x Step) used for MONTHLY_SALARY. Only
        // the active schedule is read when a report is generated.
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `{$this->salaryScheduleTable}` (
              `id` INT(11) NOT NULL AUTO_INCREMENT,
              `title` VARCHAR(150) NOT NULL,
              `effectivity_date` DATE DEFAULT NULL,
              `source_name` VARCHAR(255) DEFAULT NULL,
              `is_active` TINYINT(1) NOT NULL DEFAULT 0,
              `created_by` INT(11) DEFAULT NULL,
              `created_at` DATETIME DEFAULT NULL,
              `updated_at` DATETIME DEFAULT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8
        ");
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `{$this->salaryRateTable}` (
              `id` INT(11) NOT NULL AUTO_INCREMENT,
              `schedule_id` INT(11) NOT NULL,
              `sg` TINYINT(3) NOT NULL,
              `step` TINYINT(3) NOT NULL,
              `monthly_salary` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
              PRIMARY KEY (`id`),
              UNIQUE KEY `uniq_appointment_salary_rate` (`schedule_id`, `sg`, `step`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8
        ");

        $this->seed_guide_templates();
        $this->seed_guide_salary_schedule();
    }

    public function saved_report($recKey, $documentType, $nature)
    {
        return $this->db->where([
            'rec_key' => (string) $recKey,
            'document_type' => (string) $documentType,
            'nature_of_appointment' => (string) $nature,
        ])->get($this->editTable)->row();
    }

    /** Saved edit as the preview structure the print view renders. */
    public function saved_report_preview($saved)
    {
        $pages = json_decode((string) $saved->pages, true);
        if (!is_array($pages) || empty($pages)) {
            return null;
        }
        return ['kind' => (string) $saved->kind, 'style' => (string) $saved->style, 'pages' => $pages];
    }

    /**
     * A saved (edited) appointment keeps the SG/Step and compensation it was
     * saved with. This rewrites only those texts — "(SG n STEP n)", the
     * amount in words, and "(P00,000.00)" — so the saved copy follows the
     * SG/Step selected now; every other correction is left as saved.
     */
    public function apply_salary_to_preview(array $preview, $sg, $step, $monthly)
    {
        if ((int) $sg < 1 || empty($preview['pages'])) {
            return $preview;
        }
        $sgText = '(SG ' . (int) $sg . ' STEP ' . max(1, (int) $step) . ')';
        foreach ($preview['pages'] as $i => $page) {
            $html = (string) ($page['html'] ?? '');
            $html = preg_replace('/\(\s*SG\s*\d+(?:\s*STEP\s*\d+)?\s*\)/i', $sgText, $html);
            if ($monthly > 0 && preg_match('/\(\s*P\s*([\d,]+\.\d{2})\s*\)/', $html, $m)) {
                $oldAmount = (float) str_replace(',', '', $m[1]);
                $html = str_replace($m[0], '(P' . number_format($monthly, 2) . ')', $html);
                $oldWords = $this->number_to_words((int) round($oldAmount));
                if ($oldAmount > 0 && $oldWords !== '') {
                    $html = str_ireplace($oldWords, $this->number_to_words((int) round($monthly)), $html);
                }
            }
            $preview['pages'][$i]['html'] = $html;
        }
        return $preview;
    }

    public function save_report($recKey, $documentType, $nature, $templateId, $kind, $style, array $pages, $userId)
    {
        $key = [
            'rec_key' => (string) $recKey,
            'document_type' => (string) $documentType,
            'nature_of_appointment' => (string) $nature,
        ];
        $clean = [];
        foreach ($pages as $page) {
            $clean[] = [
                'html' => $this->clean_saved_html((string) ($page['html'] ?? '')),
                'width' => isset($page['width']) && (float) $page['width'] > 0 ? round((float) $page['width'], 2) : null,
            ];
        }
        $data = array_merge($key, [
            'template_id' => $templateId ? (int) $templateId : null,
            'kind' => $kind === 'docx' ? 'docx' : 'spreadsheet',
            // The sheet CSS never needs "<"; dropping it means the stored
            // stylesheet can never close its own <style> element.
            'style' => str_replace('<', '', (string) $style),
            'pages' => json_encode($clean),
            'saved_by' => $userId ?: null,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $existing = $this->saved_report($recKey, $documentType, $nature);
        if (!empty($existing)) {
            return $this->db->where('id', (int) $existing->id)->update($this->editTable, $data);
        }
        $data['created_at'] = $data['updated_at'];
        return $this->db->insert($this->editTable, $data);
    }

    public function delete_saved_report($recKey, $documentType, $nature)
    {
        return $this->db->where([
            'rec_key' => (string) $recKey,
            'document_type' => (string) $documentType,
            'nature_of_appointment' => (string) $nature,
        ])->delete($this->editTable);
    }

    /**
     * The edited page is stored as markup and printed back as-is, so anything
     * that could run script is removed: script-type elements, event handlers,
     * and javascript: links. Embedded data: images (letterheads) are kept.
     */
    private function clean_saved_html($html)
    {
        if (trim($html) === '') {
            return '';
        }
        $dom = new DOMDocument();
        $previous = libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="utf-8"?><div id="rp-saved-root">' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);
        $xpath = new DOMXPath($dom);
        foreach ($xpath->query('//script|//iframe|//object|//embed|//link|//meta|//style|//form|//base') as $node) {
            $node->parentNode->removeChild($node);
        }
        foreach ($xpath->query('//@*') as $attr) {
            $name = strtolower($attr->nodeName);
            $value = strtolower(preg_replace('/[\s\x00-\x1f]+/', '', (string) $attr->nodeValue));
            if (strpos($name, 'on') === 0 || $name === 'contenteditable' || $name === 'srcdoc'
                || (in_array($name, ['href', 'src', 'xlink:href', 'action', 'formaction'], true)
                    && (strpos($value, 'javascript:') === 0 || strpos($value, 'vbscript:') === 0
                        || (strpos($value, 'data:') === 0 && strpos($value, 'data:image/') !== 0)))) {
                $attr->ownerElement->removeAttributeNode($attr);
            }
        }
        $root = $dom->getElementById('rp-saved-root');
        if (!$root) {
            return '';
        }
        $out = '';
        foreach ($root->childNodes as $child) {
            $out .= $dom->saveHTML($child);
        }
        return $out;
    }

    private function seed_guide_templates()
    {
        $guideDir = FCPATH . 'resources/guide/appointment guide/';
        $seeds = [
            [4, 'Original', 'appointment', 'AOII (PERM-ORIG) PAPAS, NOVE JEAN (1).xls'],
            [4, 'Reemployment', 'appointment', 'AOII (PERM-REEM) POMOY, LEOVY MAE.xls'],
            [4, 'Promotion', 'appointment', 'AOII (PROM) ALBIOS, GRACE.xls'],
            [4, 'ALL', 'assumption', 'NEW FORMAT -AOII.docx'],
            [4, 'ALL', 'assignment', 'PERMANENT-NEW-ADAS III.docx'],
        ];

        foreach ($seeds as $seed) {
            list($group, $nature, $type, $name) = $seed;
            if (!is_file($guideDir . $name)) {
                continue;
            }
            $exists = $this->db->where([
                'position_group' => $group,
                'nature_of_appointment' => $nature,
                'document_type' => $type,
            ])->get($this->templateTable)->row();
            if (!empty($exists)) {
                continue;
            }
            $this->db->insert($this->templateTable, [
                'position_group' => $group,
                'nature_of_appointment' => $nature,
                'document_type' => $type,
                'original_name' => $name,
                'stored_path' => 'guide/' . $name,
                'extension' => strtolower(pathinfo($name, PATHINFO_EXTENSION)),
                'file_size' => filesize($guideDir . $name),
                'is_guide' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    public function all_templates()
    {
        return $this->db
            ->order_by('position_group', 'ASC')
            ->order_by('nature_of_appointment', 'ASC')
            ->order_by('document_type', 'ASC')
            ->get($this->templateTable)
            ->result();
    }

    public function find_template($positionGroup, $nature, $documentType)
    {
        $exact = $this->db->where([
            'position_group' => (int) $positionGroup,
            'nature_of_appointment' => $nature,
            'document_type' => $documentType,
        ])->get($this->templateTable)->row();
        if (!empty($exact)) {
            return $exact;
        }

        return $this->db->where([
            'position_group' => (int) $positionGroup,
            'nature_of_appointment' => 'ALL',
            'document_type' => $documentType,
        ])->get($this->templateTable)->row();
    }

    public function save_template($positionGroup, $nature, $documentType, array $file, $userId)
    {
        $key = [
            'position_group' => (int) $positionGroup,
            'nature_of_appointment' => $nature,
            'document_type' => $documentType,
        ];
        $existing = $this->db->where($key)->get($this->templateTable)->row();
        $data = array_merge($key, $file, [
            'is_guide' => 0,
            'uploaded_by' => $userId ?: null,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        if (!empty($existing)) {
            $this->db->where('id', $existing->id)->update($this->templateTable, $data);
            return $existing;
        }

        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert($this->templateTable, $data);
        return null;
    }

    public function template_by_id($id)
    {
        return $this->db->where('id', (int) $id)->get($this->templateTable)->row();
    }

    public function delete_template($id)
    {
        return $this->db->where('id', (int) $id)->delete($this->templateTable);
    }

    public function source_path($template)
    {
        $stored = (string) ($template->stored_path ?? '');
        if (strpos($stored, 'guide/') === 0) {
            return FCPATH . 'resources/guide/appointment guide/' . substr($stored, 6);
        }
        return FCPATH . 'uploads/appointment_templates/' . basename($stored);
    }

    /* ------------------------------------------------------------------
     * Monthly salary schedule
     * ------------------------------------------------------------------ */

    public function salary_schedules()
    {
        return $this->db
            ->order_by('is_active', 'DESC')
            ->order_by('effectivity_date', 'DESC')
            ->order_by('id', 'DESC')
            ->get($this->salaryScheduleTable)
            ->result();
    }

    public function salary_schedule_by_id($id)
    {
        return $this->db->where('id', (int) $id)->get($this->salaryScheduleTable)->row();
    }

    public function active_salary_schedule()
    {
        return $this->db->where('is_active', 1)->order_by('id', 'DESC')->get($this->salaryScheduleTable)->row();
    }

    /** Rates of one schedule as [sg][step] => monthly salary. */
    public function salary_rates($scheduleId)
    {
        $rates = [];
        $rows = $this->db->where('schedule_id', (int) $scheduleId)->get($this->salaryRateTable)->result();
        foreach ($rows as $rate) {
            $rates[(int) $rate->sg][(int) $rate->step] = (float) $rate->monthly_salary;
        }
        return $rates;
    }

    public function scheduled_monthly_salary($sg, $step)
    {
        $schedule = $this->active_salary_schedule();
        if (empty($schedule) || (int) $sg < 1 || (int) $step < 1) {
            return 0;
        }
        $rate = $this->db->select('monthly_salary')->where([
            'schedule_id' => (int) $schedule->id,
            'sg' => (int) $sg,
            'step' => (int) $step,
        ])->get($this->salaryRateTable)->row();
        return !empty($rate) ? (float) $rate->monthly_salary : 0;
    }

    /**
     * Creates ($id = 0) or updates a schedule. Rates are upserted per SG/Step;
     * a cell left blank on the setup grid removes only that one rate.
     */
    public function save_salary_schedule($id, array $header, array $rates, $userId, $activate = false)
    {
        $now = date('Y-m-d H:i:s');
        $data = [
            'title' => $header['title'],
            'effectivity_date' => !empty($header['effectivity_date']) ? $header['effectivity_date'] : null,
            'updated_at' => $now,
        ];

        $isNew = (int) $id < 1;
        $this->db->trans_start();
        if (!$isNew) {
            $this->db->where('id', (int) $id)->update($this->salaryScheduleTable, $data);
        } else {
            $data['source_name'] = $header['source_name'] ?? null;
            $data['is_active'] = 0;
            $data['created_by'] = $userId ?: null;
            $data['created_at'] = $now;
            $this->db->insert($this->salaryScheduleTable, $data);
            $id = (int) $this->db->insert_id();
        }

        $this->save_salary_rates($id, $rates, $isNew);
        if ($activate) {
            $this->activate_salary_schedule($id);
        }
        $this->db->trans_complete();

        return $this->db->trans_status() ? (int) $id : 0;
    }

    /**
     * Upserts rates as [sg][step] => amount; a blank or zero amount removes
     * that one rate. Used by uploads and by the grid's cell autosave.
     */
    public function save_salary_rates($id, array $rates, $skipDeletes = false)
    {
        $values = [];
        foreach ($rates as $sg => $steps) {
            foreach ((array) $steps as $step => $amount) {
                $sg = (int) $sg;
                $step = (int) $step;
                if ($sg < 1 || $sg > 33 || $step < 1 || $step > 8) {
                    continue;
                }
                if ($amount === null || $amount === '' || (float) $amount <= 0) {
                    if (!$skipDeletes) {
                        $this->db->where(['schedule_id' => (int) $id, 'sg' => $sg, 'step' => $step])->delete($this->salaryRateTable);
                    }
                    continue;
                }
                $values[] = '(' . (int) $id . ',' . $sg . ',' . $step . ',' . $this->db->escape(round((float) $amount, 2)) . ')';
            }
        }
        if (!empty($values)) {
            $this->db->query(
                "INSERT INTO `{$this->salaryRateTable}` (`schedule_id`, `sg`, `step`, `monthly_salary`) VALUES "
                . implode(',', $values)
                . ' ON DUPLICATE KEY UPDATE `monthly_salary` = VALUES(`monthly_salary`)'
            );
        }
        $this->db->where('id', (int) $id)->update($this->salaryScheduleTable, ['updated_at' => date('Y-m-d H:i:s')]);
    }

    public function activate_salary_schedule($id)
    {
        $this->db->where('id !=', (int) $id)->update($this->salaryScheduleTable, ['is_active' => 0]);
        $this->db->where('id', (int) $id)->update($this->salaryScheduleTable, ['is_active' => 1, 'updated_at' => date('Y-m-d H:i:s')]);
    }

    /** Only an inactive schedule may be removed, so reports always have one. */
    public function delete_salary_schedule($id)
    {
        $schedule = $this->salary_schedule_by_id($id);
        if (empty($schedule) || (int) $schedule->is_active === 1) {
            return false;
        }
        $this->db->trans_start();
        $this->db->where('schedule_id', (int) $id)->delete($this->salaryRateTable);
        $this->db->where('id', (int) $id)->delete($this->salaryScheduleTable);
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    /**
     * Reads a DBM/NBC "Monthly Salary Schedule" workbook: one row per SG
     * (SG number in the SG column) with Steps 1-8 under a "1 2 ... 8" header
     * row. The annual-salary rows beneath each SG have no SG and are skipped.
     */
    public function parse_salary_schedule($path, $extension)
    {
        if (!class_exists('PhpOffice\\PhpSpreadsheet\\IOFactory')) {
            require_once FCPATH . 'vendor/autoload.php';
        }
        $reader = IOFactory::createReader(strtolower($extension) === 'xls' ? 'Xls' : 'Xlsx');
        $reader->setReadDataOnly(true);
        $book = $reader->load($path);
        $rows = $book->getSheet(0)->toArray(null, true, false, false);
        $book->disconnectWorksheets();

        $title = '';
        $effectivity = '';
        $stepCols = [];
        $sgCol = null;
        $rates = [];
        foreach ($rows as $index => $row) {
            if (empty($stepCols)) {
                foreach ($row as $col => $cell) {
                    $text = trim((string) $cell);
                    if ($title === '' && preg_match('/\b(NBC|EO|E\.O\.)\s*(NO\.?\s*)?\d+/i', $text)) {
                        $title = $text;
                    }
                    if ($effectivity === '' && preg_match('/effective\s+(.+)$/i', $text, $m) && strtotime($m[1])) {
                        $effectivity = date('Y-m-d', strtotime($m[1]));
                    }
                    // Some schedules repeat SG on the right edge; the left one is used.
                    if ($sgCol === null && strcasecmp($text, 'SG') === 0) {
                        $sgCol = $col;
                    }
                }
                // Step header: the columns holding 1, 2, ... 8 in sequence.
                $found = [];
                foreach ($row as $col => $cell) {
                    if (is_numeric($cell) && (int) $cell === count($found) + 1 && (float) $cell === (float) (int) $cell) {
                        $found[(int) $cell] = $col;
                    }
                }
                if (count($found) >= 8) {
                    $stepCols = array_slice($found, 0, 8, true);
                }
                continue;
            }

            $sg = $row[$sgCol ?? 0] ?? null;
            if (!is_numeric($sg) || (int) $sg < 1 || (int) $sg > 33) {
                continue;
            }
            foreach ($stepCols as $step => $col) {
                $amount = $row[$col] ?? null;
                if (is_string($amount)) {
                    $amount = str_replace([',', ' '], '', $amount);
                }
                if (is_numeric($amount) && (float) $amount > 0) {
                    $rates[(int) $sg][(int) $step] = round((float) $amount, 2);
                }
            }
        }

        return ['title' => $title, 'effectivity_date' => $effectivity, 'rates' => $rates];
    }

    /** Registers the bundled NBC schedule once, when no schedule exists yet. */
    private function seed_guide_salary_schedule()
    {
        if ($this->db->count_all($this->salaryScheduleTable) > 0) {
            return;
        }
        $path = FCPATH . 'resources/guide/appointment guide/' . $this->guideSalaryFile;
        if (!is_file($path)) {
            return;
        }
        try {
            $parsed = $this->parse_salary_schedule($path, 'xlsx');
        } catch (\Throwable $e) {
            log_message('error', 'Salary schedule seed failed: ' . $e->getMessage());
            return;
        }
        if (empty($parsed['rates'])) {
            return;
        }
        $this->save_salary_schedule(0, [
            'title' => $parsed['title'] !== '' ? $parsed['title'] : 'Monthly Salary Schedule',
            'effectivity_date' => $parsed['effectivity_date'],
            'source_name' => $this->guideSalaryFile,
        ], $parsed['rates'], null, true);
    }

    /**
     * SG/Step and monthly salary for an appointee. SG comes from the plantilla
     * item, then the position record; an SG/Step picked on the reports page
     * (salary_grade_override / salary_step_override) replaces both. The active
     * salary schedule supplies the amount, with plantilla/payroll fallbacks.
     * sg = 0 means the grade is unknown and has to be selected.
     */
    public function resolve_salary_grade($row)
    {
        $plantilla = $this->db->where('itemNo', (string) ($row->item_number ?? ''))->get('hris_plantilla')->row();
        $sg = !empty($plantilla) ? (int) $plantilla->sg : 0;
        $step = !empty($plantilla) ? (int) $plantilla->step : 0;
        $plantillaMonthly = !empty($plantilla) && (float) $plantilla->authAnnualSalary > 0
            ? ((float) $plantilla->authAnnualSalary / 12)
            : 0;
        if ($sg < 1 && !empty($row->position_id)) {
            $position = $this->db->select('sg')->where('id', (int) $row->position_id)->get('hris_positions')->row();
            $sg = !empty($position) ? (int) $position->sg : 0;
        }
        if ($sg < 1 && !empty($row->jobTitle)) {
            $position = $this->db->select('sg')->where('title', trim((string) $row->jobTitle))->get('hris_positions')->row();
            $sg = !empty($position) ? (int) $position->sg : 0;
        }
        if ($sg < 1 || $sg > 33) {
            $sg = 0;
        }
        if ($step < 1 && $sg > 0) {
            $step = 1;
        }
        $autoSg = $sg;
        $autoStep = $step;

        $overrideSg = (int) ($row->salary_grade_override ?? 0);
        if ($overrideSg >= 1 && $overrideSg <= 33) {
            $overrideStep = (int) ($row->salary_step_override ?? 0);
            $overrideStep = $overrideStep >= 1 && $overrideStep <= 8 ? $overrideStep : 1;
            // The plantilla rate belongs to its own grade/step only.
            if ($overrideSg !== $sg || $overrideStep !== $step) {
                $plantillaMonthly = 0;
            }
            $sg = $overrideSg;
            $step = $overrideStep;
        }

        $monthlySalary = $sg > 0 ? $this->scheduled_monthly_salary($sg, $step) : 0;
        if ($monthlySalary <= 0) {
            $monthlySalary = $plantillaMonthly;
        }
        if ($monthlySalary <= 0 && $sg > 0 && $this->db->table_exists('payroll_salary')) {
            $this->db->where('sgNo', (string) $sg)->where('stepNo', (string) $step);
            if ($this->db->field_exists('sgYear', 'payroll_salary')) {
                $this->db->order_by('sgYear', 'DESC');
            }
            $salaryRow = $this->db->get('payroll_salary')->row();
            $monthlySalary = !empty($salaryRow) ? (float) ($salaryRow->salary ?? 0) : 0;
        }

        return [
            'sg' => $sg,
            'step' => $step,
            'monthly' => $monthlySalary,
            'autoSg' => $autoSg,
            'autoStep' => $autoStep,
        ];
    }

    /** Monthly salary in words, as printed on the appointment. */
    public function salary_in_words($amount)
    {
        return $amount > 0 ? $this->number_to_words((int) round($amount)) : '';
    }

    public function values_for_row($row)
    {
        $groupNames = $this->position_groups();
        $first = trim((string) ($row->FirstName ?? ''));
        $middle = trim((string) ($row->MiddleName ?? ''));
        $last = trim((string) ($row->LastName ?? ''));
        $extension = trim((string) ($row->NameExtn ?? ''));
        // "N/A", "NA", "-" mean no suffix, not part of the printed name.
        if (preg_match('#^(n/?a|none|-+|\.)$#i', $extension)) {
            $extension = '';
        }
        $middleInitial = $middle !== '' ? mb_strtoupper(mb_substr($middle, 0, 1, 'UTF-8'), 'UTF-8') . '.' : '';
        $nameParts = array_filter([$first, $middleInitial, $last, $extension], function ($v) { return $v !== ''; });
        $fullName = implode(' ', $nameParts);
        if ($fullName === '') {
            $fullName = trim((string) ($row->rec_name ?? ''));
        }

        $prefix = trim((string) ($row->prefix ?? ''));
        if ($prefix === '') {
            $prefix = strcasecmp((string) ($row->Sex ?? ''), 'Male') === 0 ? 'Mr.' : 'Ms.';
        }

        $addressParts = [];
        foreach (['resHouseNo', 'resStreet', 'resVillage', 'brgy', 'resCity', 'resProvince', 'resZipCode'] as $field) {
            $value = trim((string) ($row->{$field} ?? ''));
            if ($value !== '') {
                $addressParts[] = $value;
            }
        }

        $grade = $this->resolve_salary_grade($row);
        $sg = $grade['sg'];
        $step = $grade['step'];
        $monthlySalary = $grade['monthly'];

        $school = null;
        if (!empty($row->school_id)) {
            $school = $this->db->where('recID', (int) $row->school_id)->get('schools')->row();
        }
        $settings = $this->db->where('settingsID', 1)->get('mis_settings')->row();
        $signatories = $this->division_signatories($settings);

        $nature = trim((string) ($row->nature_of_appointment ?? ''));
        $employmentStatus = preg_replace('/\s+Position$/i', '', trim((string) ($row->empType ?? '')));
        $issuedDate = trim((string) ($row->appointment_issued_at ?? ''));
        $issuedDate = $issuedDate !== '' ? substr($issuedDate, 0, 10) : '';

        $values = [
            'APPLICANT_NAME' => $fullName,
            'APPLICANT_NAME_UPPER' => mb_strtoupper($fullName, 'UTF-8'),
            'FIRST_NAME' => $first,
            'MIDDLE_NAME' => $middle,
            'MIDDLE_INITIAL' => $middleInitial,
            'LAST_NAME' => $last,
            'NAME_EXTENSION' => $extension,
            'PREFIX' => $prefix,
            'POSITION_TITLE' => trim((string) ($row->jobTitle ?? '')),
            'POSITION_GROUP' => $groupNames[(int) ($row->position_group ?? 0)] ?? '',
            'NATURE_OF_APPOINTMENT' => $nature,
            'EMPLOYMENT_STATUS' => $employmentStatus,
            'ITEM_NUMBER' => trim((string) ($row->item_number ?? '')),
            'SALARY_GRADE' => $sg > 0 ? (string) $sg : '',
            'STEP' => $step > 0 ? (string) $step : '',
            'MONTHLY_SALARY' => $monthlySalary > 0 ? number_format($monthlySalary, 2) : '',
            'MONTHLY_SALARY_WORDS' => $monthlySalary > 0 ? $this->number_to_words((int) round($monthlySalary)) : '',
            'SCHOOL_NAME' => trim((string) ($row->school_name ?? '')),
            'DISTRICT' => trim((string) ($school->district ?? '')),
            'COMPLETE_ADDRESS' => implode(', ', $addressParts),
            'MUNICIPALITY' => trim((string) ($row->resCity ?? '')),
            'PROVINCE' => trim((string) ($row->resProvince ?? '')),
            'CONTACT_NUMBER' => trim((string) ($row->contactNo ?? '')),
            'EMAIL' => trim((string) ($row->empEmail ?? '')),
            'DATE_HIRED' => trim((string) ($row->date_hired ?? '')),
            'DATE_ISSUED' => $issuedDate,
            'VACANCY_DESCRIPTION' => trim((string) ($row->vacancy_description ?? '')),
            'DEPARTMENT' => trim((string) ($row->department ?? '')),
            'REGION' => trim((string) ($settings->Region ?? '')),
            'DIVISION' => trim((string) ($settings->division ?? '')),
            'DIVISION_ADDRESS' => trim((string) ($settings->divAddress ?? '')),
            'AGENCY' => $signatories['AGENCY'],
            'SDS_NAME' => $signatories['SDS_NAME'],
            'SDS_POSITION' => $signatories['SDS_POSITION'],
            'HRMO_NAME' => $signatories['HRMO_NAME'],
            'HRMO_POSITION' => $signatories['HRMO_POSITION'],
            'ASDS_NAME' => $signatories['ASDS_NAME'],
            'DATE_SIGNING' => date('F j, Y'),
            'VICE' => 'NEW ITEM',
            'PAGE' => '',
        ];

        $wrapped = [];
        foreach ($values as $key => $value) {
            $wrapped['{{' . $key . '}}'] = (string) $value;
        }
        return $wrapped;
    }

    /**
     * SDS / HRMO names and titles for the signature blocks. mis_settings
     * columns win when present; otherwise the user account carrying the
     * role (users.username is the staff IDNumber), then the staff list
     * itself by position title.
     */
    private function division_signatories($settings)
    {
        $setting = function ($field) use ($settings) {
            return trim((string) ($settings->{$field} ?? ''));
        };

        // The signing accounts (users.position) come first so the names match
        // the e-signatures drawn over them; settings/staff are the fallback.
        $sdsName = $this->user_signatory('sds')['name'];
        $sdsPosition = $setting('sdsPosition');
        $hrmoName = $this->user_signatory('Human Resource Admin')['name'];
        $hrmoPosition = $setting('sigSRPosition');
        $agency = $setting('agency');
        if ($sdsName === '') {
            $sdsName = $setting('sds');
        }
        if ($hrmoName === '') {
            $hrmoName = $setting('sigSR');
        }

        if ($sdsName === '') {
            $staff = $this->staff_by_user_position('sds');
            if (empty($staff)) {
                $staff = $this->db->where('empPosition', 'Schools Division Superintendent')
                    ->where('currentStatus', 'Active')
                    ->get('hris_staff')
                    ->row();
            }
            $sdsName = $this->staff_name($staff);
            if ($sdsPosition === '' && !empty($staff)) {
                $sdsPosition = trim((string) ($staff->empPosition ?? ''));
            }
        }
        if ($sdsName !== '' && $sdsPosition === '') {
            $sdsPosition = 'Schools Division Superintendent';
        }

        if ($hrmoName === '') {
            $staff = $this->staff_by_user_position('HRMO');
            if (empty($staff)) {
                $staff = $this->db->where('currentStatus', 'Active')
                    ->group_start()
                        ->like('empPosition', 'Human Resource')
                        ->or_where('empPosition', 'Administrative Officer V')
                    ->group_end()
                    ->get('hris_staff')
                    ->row();
            }
            $hrmoName = $this->staff_name($staff);
            if ($hrmoPosition === '' && !empty($staff)) {
                $hrmoPosition = trim((string) ($staff->empPosition ?? ''));
            }
        }
        if ($hrmoName !== '' && $hrmoPosition === '') {
            $hrmoPosition = 'Administrative Officer V';
        }

        return [
            'SDS_NAME' => $sdsName,
            'SDS_POSITION' => $sdsPosition,
            'HRMO_NAME' => $hrmoName,
            'HRMO_POSITION' => $hrmoPosition,
            'ASDS_NAME' => $this->user_signatory('asst_sds')['name'],
            'AGENCY' => $agency !== '' ? $agency : 'Department of Education',
        ];
    }

    /**
     * Name and e-signature file of the account holding a signing role. The
     * signature is the one maintained under Pages/esignature (uploads/esig).
     * An account with a signature on file is preferred.
     */
    private function user_signatory($userPosition)
    {
        static $cache = [];
        if (isset($cache[$userPosition])) {
            return $cache[$userPosition];
        }
        $hasEsig = $this->db->field_exists('esig', 'users');
        $this->db->where('position', $userPosition)->where('status', 1);
        if ($hasEsig) {
            $this->db->order_by("COALESCE(esig, '') = ''", 'ASC', false);
        }
        $user = $this->db->order_by('id', 'DESC')->limit(1)->get('users')->row();

        $name = '';
        $esig = '';
        if (!empty($user)) {
            $first = trim((string) $user->fname);
            $middle = trim((string) $user->mname);
            $mi = $middle !== '' ? mb_substr($middle, 0, 1, 'UTF-8') . '.' : '';
            $name = mb_strtoupper(trim(implode(' ', array_filter([$first, $mi, trim((string) $user->lname)]))), 'UTF-8');
            $file = $hasEsig ? basename(trim((string) $user->esig)) : '';
            if ($file !== '' && is_file(FCPATH . 'uploads/esig/' . $file)) {
                $esig = FCPATH . 'uploads/esig/' . $file;
            }
        }
        return $cache[$userPosition] = ['name' => $name, 'esig' => $esig];
    }

    private function staff_by_user_position($userPosition)
    {
        $user = $this->db->select('username')
            ->where('position', $userPosition)
            ->get('users')
            ->row();
        if (empty($user) || trim((string) $user->username) === '') {
            return null;
        }
        return $this->db->where('IDNumber', $user->username)->get('hris_staff')->row();
    }

    private function staff_name($staff)
    {
        if (empty($staff)) {
            return '';
        }
        $first = trim((string) ($staff->FirstName ?? ''));
        $middle = trim((string) ($staff->MiddleName ?? ''));
        $last = trim((string) ($staff->LastName ?? ''));
        $ext = trim((string) ($staff->NameExtn ?? ''));
        $mi = $middle !== '' ? mb_strtoupper(mb_substr($middle, 0, 1, 'UTF-8'), 'UTF-8') . '.' : '';
        return mb_strtoupper(trim(implode(' ', array_filter([$first, $mi, $last, $ext]))), 'UTF-8');
    }

    public function generate($template, $row)
    {
        $source = $this->source_path($template);
        if (!is_file($source)) {
            throw new RuntimeException('The selected template file is missing.');
        }

        $extension = strtolower((string) $template->extension);
        $output = $this->temp_output_path($extension);
        $values = $this->values_for_row($row);

        if (in_array($extension, ['xls', 'xlsx'], true)) {
            $this->merge_spreadsheet($source, $output, $values, (string) $template->document_type);
        } elseif ($extension === 'docx') {
            $this->merge_docx($source, $output, $values, (string) $template->document_type);
        } else {
            throw new RuntimeException('This template type cannot be generated. Upload an XLS, XLSX, or DOCX file.');
        }

        return $output;
    }

    /**
     * Working path for a merged document. The system temp directory is the
     * user's private folder on macOS/XAMPP, which the Apache user cannot
     * write to, so the merge is written under uploads/ like the rest of the
     * project. The file is removed as soon as it has been sent or rendered.
     */
    private function temp_output_path($extension)
    {
        $name = 'appt_' . bin2hex(random_bytes(8)) . '.' . strtolower((string) $extension);
        $dir = FCPATH . 'uploads/appointment_tmp/';
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
            @chmod($dir, 0777);
        }
        if (is_dir($dir) && is_writable($dir)) {
            $this->protect_temp_dir($dir);
            $this->purge_temp_dir($dir);
            return $dir . $name;
        }
        // Last resort: the system temp directory, if this server can use it.
        $fallback = rtrim(sys_get_temp_dir(), '/\\') . DIRECTORY_SEPARATOR;
        if (is_dir($fallback) && is_writable($fallback)) {
            return $fallback . $name;
        }
        throw new RuntimeException('Unable to create the report file. Make the uploads/appointment_tmp folder writable.');
    }

    /** Keep merged documents, which hold personal data, out of the browser. */
    private function protect_temp_dir($dir)
    {
        if (!is_file($dir . '.htaccess')) {
            @file_put_contents($dir . '.htaccess', "<IfModule mod_authz_core.c>\n  Require all denied\n</IfModule>\n<IfModule !mod_authz_core.c>\n  Order allow,deny\n  Deny from all\n</IfModule>\n");
        }
        if (!is_file($dir . 'index.html')) {
            @file_put_contents($dir . 'index.html', '');
        }
    }

    /** Drop leftovers from runs that ended before their file was removed. */
    private function purge_temp_dir($dir)
    {
        foreach ((array) glob($dir . 'appt_*') as $file) {
            if (is_file($file) && filemtime($file) < time() - 3600) {
                @unlink($file);
            }
        }
    }

    private function merge_spreadsheet($source, $output, array $values, $documentType)
    {
        if (!class_exists('PhpOffice\\PhpSpreadsheet\\IOFactory')) {
            require_once FCPATH . 'vendor/autoload.php';
        }
        $book = IOFactory::load($source);
        foreach ($book->getWorksheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                foreach ($row->getCellIterator() as $cell) {
                    $value = $cell->getValue();
                    if (is_string($value)) {
                        $merged = strtr($value, $values);
                        if ($merged !== $value) {
                            $cell->setValueExplicit($merged, DataType::TYPE_STRING);
                        }
                    }
                }
            }
        }

        // The supplied appointment guide predates placeholders. Its labelled
        // CS Form 33-B cells are filled directly while retaining every style,
        // merge, image, page setting, and certification sheet in the workbook.
        if ($documentType === 'appointment' && $book->getSheetCount() > 0) {
            $sheet = $book->getSheet(0);
            $sheet->setCellValue('C18', rtrim($values['{{PREFIX}}'], '.') . '.:');
            $sheet->setCellValue('D18', mb_strtoupper($values['{{APPLICANT_NAME}}'], 'UTF-8'));
            $sheet->setCellValue('J22', mb_strtoupper($values['{{POSITION_TITLE}}'], 'UTF-8'));
            $sgStep = '';
            if ($values['{{SALARY_GRADE}}'] !== '') {
                $sgStep = '(SG ' . $values['{{SALARY_GRADE}}'];
                if ($values['{{STEP}}'] !== '') {
                    $sgStep .= ' STEP ' . $values['{{STEP}}'];
                }
                $sgStep .= ')';
            }
            $sheet->setCellValue('S22', $sgStep);
            $sheet->setCellValue('D25', mb_strtoupper($values['{{EMPLOYMENT_STATUS}}'], 'UTF-8'));
            $office = $values['{{DEPARTMENT}}'];
            if ($office === '') {
                $office = trim($values['{{AGENCY}}'] . ($values['{{DIVISION}}'] !== '' ? ' - DIVISION OF ' . mb_strtoupper($values['{{DIVISION}}'], 'UTF-8') : ''));
            }
            if ($office !== '') {
                $sheet->setCellValue('L25', $office);
            }
            // Compensation in words and figures. With no plantilla/salary
            // schedule for the grade, the rate written on the template stays.
            if ($values['{{MONTHLY_SALARY}}'] !== '') {
                $sheet->setCellValue('G28', mb_strtoupper($values['{{MONTHLY_SALARY_WORDS}}'], 'UTF-8'));
                $sheet->setCellValue('S28', '(P' . $values['{{MONTHLY_SALARY}}'] . ')');
            }
            $sheet->setCellValue('K32', mb_strtoupper($values['{{NATURE_OF_APPOINTMENT}}'], 'UTF-8'));
            $sheet->setCellValue('P32', $values['{{VICE}}']);
            $sheet->setCellValue('F38', $values['{{ITEM_NUMBER}}']);
            $sheet->setCellValue('R38', $values['{{PAGE}}']);
            if ($values['{{REGION}}'] !== '') {
                $sheet->setCellValue('C11', $values['{{REGION}}']);
            }
            if ($values['{{DIVISION}}'] !== '') {
                $sheet->setCellValue('C12', 'DIVISION OF ' . mb_strtoupper($values['{{DIVISION}}'], 'UTF-8'));
            }
            if ($values['{{DIVISION_ADDRESS}}'] !== '') {
                $sheet->setCellValue('C13', mb_strtoupper($values['{{DIVISION_ADDRESS}}'], 'UTF-8'));
            }
            if ($values['{{SDS_NAME}}'] !== '') {
                // The signing account carries no name extension/title, so the
                // one written on the template (", CESO V") is kept.
                $sdsName = mb_strtoupper($values['{{SDS_NAME}}'], 'UTF-8');
                $templateName = (string) $sheet->getCell('L51')->getValue();
                if (strpos($sdsName, ',') === false && ($comma = strpos($templateName, ',')) !== false) {
                    $sdsName .= substr($templateName, $comma);
                }
                $sheet->setCellValue('L51', $sdsName);
            }
            if ($book->getSheetCount() > 1) {
                $certs = $book->getSheet(1);
                if ($values['{{HRMO_NAME}}'] !== '') {
                    $certs->setCellValue('L15', mb_strtoupper($values['{{HRMO_NAME}}'], 'UTF-8'));
                    $certs->setCellValue('L16', $values['{{HRMO_POSITION}}']);
                }
                if ($values['{{ASDS_NAME}}'] !== '') {
                    $certs->setCellValue('L29', $values['{{ASDS_NAME}}']);
                }
            }
            $this->align_appointment_signatories($book);
        }

        $writerType = strtolower(pathinfo($output, PATHINFO_EXTENSION)) === 'xls' ? 'Xls' : 'Xlsx';
        $writer = IOFactory::createWriter($book, $writerType);
        $writer->save($output);
        $book->disconnectWorksheets();
    }

    /**
     * CS Form 33-B signature blocks, shared by the report and the template
     * preview so both lay the signatories out the same way.
     */
    private function align_appointment_signatories($book)
    {
        if ($book->getSheetCount() < 1) {
            return;
        }
        // The date of signing sits under the same L:T block as the
        // Appointing Officer, so both lines share one centre. The date
        // itself is written by hand when the appointment is signed.
        $sheet = $book->getSheet(0);
        $this->realign_block($sheet, 'M55:T55', 'L55:T55');
        $this->realign_block($sheet, 'M56:T56', 'L56:T56');
        if ($book->getSheetCount() > 1) {
            // Each certification's name and titles are centred on one
            // K:N block instead of spilling right from the narrow L cell.
            $certs = $book->getSheet(1);
            foreach ([15, 16, 17, 29, 30, 31] as $row) {
                $this->realign_block($certs, 'L' . $row, 'K' . $row . ':N' . $row);
            }
        }
    }

    /**
     * Moves a signatory line from $source (a cell or merged range) onto the
     * merged block $target, keeping its value and look, centred across it.
     */
    private function realign_block($sheet, $source, $target)
    {
        list($sourceStart) = explode(':', $source);
        list($targetStart, $targetEnd) = explode(':', $target);
        $value = $sheet->getCell($sourceStart)->getValue();
        $style = $sheet->getStyle($sourceStart);
        if (strpos($source, ':') !== false && isset($sheet->getMergeCells()[$source])) {
            $sheet->unmergeCells($source);
        }
        // Keep the ruled line of the source span if the new first cell lacks it.
        $sheet->duplicateStyle($style, $targetStart . ':' . $targetEnd);
        if ($sourceStart !== $targetStart) {
            $sheet->setCellValue($sourceStart, null);
        }
        $sheet->setCellValue($targetStart, $value);
        $sheet->mergeCells($target);
        $sheet->getStyle($target)->getAlignment()->setHorizontal('center');
    }

    private function merge_docx($source, $output, array $values, $documentType)
    {
        if (!copy($source, $output)) {
            throw new RuntimeException('Unable to prepare the Word template.');
        }
        $zip = new ZipArchive();
        if ($zip->open($output) !== true) {
            throw new RuntimeException('The uploaded Word template is not a valid DOCX file.');
        }

        $legacy = [];
        if ($documentType === 'assumption') {
            $legacy = [
                'Ms. MANELYN S. CELING' => $values['{{PREFIX}}'] . ' ' . $values['{{APPLICANT_NAME_UPPER}}'],
                'Ms. CELING' => $values['{{PREFIX}}'] . ' ' . mb_strtoupper($values['{{LAST_NAME}}'], 'UTF-8'),
                'MANELYN S. CELING' => $values['{{APPLICANT_NAME_UPPER}}'],
                'Administrative Officer II' => $values['{{POSITION_TITLE}}'],
                'PHOEBE GAY L. REFAMONTE, CESO V' => $values['{{SDS_NAME}}'],
                'NORBERTO S. MANLANGIT CE, MPA' => $values['{{HRMO_NAME}}'],
                'Administrative Officer V' => $values['{{HRMO_POSITION}}'] !== '' ? $values['{{HRMO_POSITION}}'] : 'Administrative Officer V',
                // "Done this ___ day of ___" and the attestation date stay
                // blank: they are written by hand on signing.
            ];
            $hired = strtotime($values['{{DATE_HIRED}}']);
            if ($values['{{DATE_HIRED}}'] !== '' && $hired) {
                $legacy['effective   ____________________'] = 'effective   ' . date('F j, Y', $hired);
            }
        } elseif ($documentType === 'assignment') {
            $locality = trim($values['{{MUNICIPALITY}}'] . ', ' . $values['{{PROVINCE}}'], " \t\n\r\0\x0B,");
            if ($locality === '') {
                $locality = $values['{{COMPLETE_ADDRESS}}'];
            }
            $legacy = [
                'MANELYN S. CELING' => $values['{{APPLICANT_NAME_UPPER}}'],
                'Administrative Officer II' => $values['{{POSITION_TITLE}}'],
                'Bagong Taas Elementary School' => $values['{{SCHOOL_NAME}}'],
                'Monkayo West District' => $values['{{DISTRICT}}'],
                'Monkayo, Davao de Oro' => $locality,
                'PHOEBE GAY L. REFAMONTE, CESO V' => $values['{{SDS_NAME}}'],
                // The order date line stays blank for the date of signing.
                // The order number itself is issued by Records, so it is left
                // to fill in; only the Special Order month/year is set.
                'SEPTEMBER– 0514 s. 2026' => mb_strtoupper(date('F'), 'UTF-8') . '– ______ s. ' . date('Y'),
            ];
        }

        // Parts are edited in memory and written once: ZipArchive keeps
        // returning the original entry until the archive is closed.
        $replacements = $values + $legacy;
        $parts = [];
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);
            if (!preg_match('#^word/(document|header[0-9]+|footer[0-9]+)\.xml$#', $name)) {
                continue;
            }
            $xml = $zip->getFromIndex($i);
            if ($xml === false) {
                continue;
            }
            $parts[$name] = $this->replace_word_text($xml, $replacements);
        }

        // Each signatory's name and position share one centre: the SDS on the
        // right half of the text column, the attesting HRMO on the left.
        if (isset($parts['word/document.xml'])) {
            $blocks = [];
            if (in_array($documentType, ['assumption', 'assignment'], true)) {
                $blocks[] = [$values['{{SDS_NAME}}'], 'right'];
            }
            if ($documentType === 'assumption') {
                $blocks[] = [$values['{{HRMO_NAME}}'], 'left'];
            }
            $parts['word/document.xml'] = $this->center_docx_signatories($parts['word/document.xml'], $blocks);
        }

        foreach ($parts as $name => $xml) {
            if ($xml !== false) {
                $zip->addFromString($name, $xml);
            }
        }
        $zip->close();
    }

    /**
     * The templates push names over with spaces/tabs and indent the position
     * line separately, so the two never share a centre. The name paragraph and
     * the position line under it are re-laid as one centred block on the
     * chosen half of the text column.
     *
     * @param array $blocks list of [signatory name, 'left'|'right']
     */
    private function center_docx_signatories($xml, array $blocks)
    {
        $dom = new DOMDocument();
        $dom->preserveWhiteSpace = true;
        if (!@$dom->loadXML($xml)) {
            return $xml;
        }
        $w = 'http://schemas.openxmlformats.org/wordprocessingml/2006/main';
        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('w', $w);

        // Half of the text column, in twips.
        $half = 4320;
        $size = $xpath->query('//w:body/w:sectPr/w:pgSz')->item(0);
        $margin = $xpath->query('//w:body/w:sectPr/w:pgMar')->item(0);
        if ($size && $margin) {
            $text = (int) $size->getAttributeNS($w, 'w') - (int) $margin->getAttributeNS($w, 'left') - (int) $margin->getAttributeNS($w, 'right');
            if ($text > 0) {
                $half = (int) round($text / 2);
            }
        }

        $paragraphText = function (DOMElement $p) use ($xpath) {
            $text = '';
            foreach ($xpath->query('.//w:t[not(ancestor::w:txbxContent)]', $p) as $node) {
                $text .= $node->nodeValue;
            }
            return trim($text);
        };
        $paragraphs = [];
        foreach ($xpath->query('//w:body//w:p[not(ancestor::w:txbxContent)]') as $p) {
            $paragraphs[] = $p;
        }

        foreach ($blocks as $block) {
            list($name, $side) = $block;
            if (trim($name) === '') {
                continue;
            }
            foreach ($paragraphs as $index => $p) {
                if ($paragraphText($p) !== trim($name)) {
                    continue;
                }
                $lines = [$p];
                // The position line: the next paragraph that carries text.
                for ($next = $index + 1; $next < count($paragraphs) && $next <= $index + 3; $next++) {
                    if ($paragraphText($paragraphs[$next]) !== '') {
                        $lines[] = $paragraphs[$next];
                        break;
                    }
                }
                foreach ($lines as $line) {
                    $this->center_docx_paragraph($dom, $xpath, $line, $side === 'right' ? $half : 0, $side === 'right' ? 0 : $half);
                }
                break;
            }
        }
        return $dom->saveXML();
    }

    private function center_docx_paragraph(DOMDocument $dom, DOMXPath $xpath, DOMElement $p, $left, $right)
    {
        $w = 'http://schemas.openxmlformats.org/wordprocessingml/2006/main';
        // Drop the spaces/tabs used to push the text over. Runs may sit inside
        // a content control (w:sdt), so match them at any depth.
        $runs = './/w:r[not(ancestor::w:txbxContent)]';
        foreach ($xpath->query($runs . '/w:tab|' . $runs . '/w:ptab', $p) as $tab) {
            $tab->parentNode->removeChild($tab);
        }
        $texts = $xpath->query($runs . '/w:t', $p);
        foreach ($texts as $node) {
            $trimmed = ltrim($node->nodeValue, " \t\xC2\xA0");
            $node->nodeValue = $trimmed;
            if ($trimmed !== '') {
                break;
            }
        }
        for ($i = $texts->length - 1; $i >= 0; $i--) {
            $node = $texts->item($i);
            $trimmed = rtrim($node->nodeValue, " \t\xC2\xA0");
            $node->nodeValue = $trimmed;
            if ($trimmed !== '') {
                break;
            }
        }

        $pPr = $xpath->query('./w:pPr', $p)->item(0);
        if (!$pPr) {
            $pPr = $dom->createElementNS($w, 'w:pPr');
            $p->insertBefore($pPr, $p->firstChild);
        }
        foreach ($xpath->query('./w:ind|./w:jc|./w:tabs', $pPr) as $old) {
            $pPr->removeChild($old);
        }
        // Schema order within w:pPr: w:ind comes before contextualSpacing /
        // mirrorIndents / suppressOverlap, and w:jc right after those.
        $afterJc = ['textDirection', 'textAlignment', 'textboxTightWrap', 'outlineLvl', 'divId', 'cnfStyle', 'rPr', 'sectPr', 'pPrChange'];
        $afterInd = array_merge(['contextualSpacing', 'mirrorIndents', 'suppressOverlap'], $afterJc);
        $firstOf = function (array $names) use ($pPr) {
            foreach ($pPr->childNodes as $child) {
                if ($child instanceof DOMElement && in_array($child->localName, $names, true)) {
                    return $child;
                }
            }
            return null;
        };
        $ind = $dom->createElementNS($w, 'w:ind');
        $ind->setAttributeNS($w, 'w:left', (string) $left);
        $ind->setAttributeNS($w, 'w:right', (string) $right);
        $ind->setAttributeNS($w, 'w:firstLine', '0');
        $pPr->insertBefore($ind, $firstOf($afterInd));
        $jc = $dom->createElementNS($w, 'w:jc');
        $jc->setAttributeNS($w, 'w:val', 'center');
        $pPr->insertBefore($jc, $firstOf($afterJc));
    }

    /**
     * Replace text even when Word has split it across several w:t runs.
     * Two passes through unique markers, so a replaced value is never
     * matched again by a later rule (e.g. the sample name inside the new one).
     */
    private function replace_word_text($xml, array $replacements)
    {
        $markers = [];
        $finals = [];
        $index = 0;
        foreach ($replacements as $search => $replacement) {
            if ($search === '' || $search === $replacement) {
                continue;
            }
            $marker = "\u{E000}" . $index++ . "\u{E001}";
            $markers[$search] = $marker;
            $finals[$marker] = $replacement;
        }
        return $this->replace_word_text_pass($this->replace_word_text_pass($xml, $markers), $finals);
    }

    private function replace_word_text_pass($xml, array $replacements)
    {
        $dom = new DOMDocument();
        $dom->preserveWhiteSpace = true;
        if (!@$dom->loadXML($xml)) {
            return strtr($xml, $replacements);
        }
        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
        foreach ($xpath->query('//w:p') as $paragraph) {
            $nodes = [];
            foreach ($xpath->query('.//w:t', $paragraph) as $node) {
                $nodes[] = $node;
            }
            foreach ($replacements as $search => $replacement) {
                if ($search === '' || $search === $replacement) {
                    continue;
                }
                $offset = 0;
                while (true) {
                    $text = '';
                    $spans = [];
                    foreach ($nodes as $index => $node) {
                        $start = strlen($text);
                        $text .= $node->nodeValue;
                        $spans[$index] = [$start, strlen($text)];
                    }
                    $position = strpos($text, $search, $offset);
                    if ($position === false) {
                        break;
                    }
                    $endPosition = $position + strlen($search);
                    $startIndex = null;
                    $endIndex = null;
                    foreach ($spans as $index => $span) {
                        if ($startIndex === null && $position >= $span[0] && $position < $span[1]) {
                            $startIndex = $index;
                        }
                        if ($endPosition > $span[0] && $endPosition <= $span[1]) {
                            $endIndex = $index;
                            break;
                        }
                    }
                    if ($startIndex === null || $endIndex === null) {
                        break;
                    }
                    $startOffset = $position - $spans[$startIndex][0];
                    $endOffset = $endPosition - $spans[$endIndex][0];
                    $before = substr($nodes[$startIndex]->nodeValue, 0, $startOffset);
                    $after = substr($nodes[$endIndex]->nodeValue, $endOffset);
                    if ($startIndex === $endIndex) {
                        $nodes[$startIndex]->nodeValue = $before . $replacement . $after;
                    } else {
                        $nodes[$startIndex]->nodeValue = $before . $replacement;
                        for ($i = $startIndex + 1; $i < $endIndex; $i++) {
                            $nodes[$i]->nodeValue = '';
                        }
                        $nodes[$endIndex]->nodeValue = $after;
                    }
                    $offset = $position + strlen($replacement);
                }
            }
        }
        return $dom->saveXML();
    }

    /**
     * Renders a saved template into viewable HTML so users can inspect the
     * uploaded format without downloading it. Spreadsheets come back as a
     * complete HTML document (shown inside an iframe by the preview view);
     * Word files come back as inner HTML styled by that view.
     */
    public function preview($template)
    {
        $source = $this->source_path($template);
        if (!is_file($source)) {
            return null;
        }
        $extension = strtolower((string) $template->extension);
        if (in_array($extension, ['xls', 'xlsx'], true)) {
            // Built from the printable sheets: no "Sheet1 / Sheet2 / Sheet3"
            // navigation, no blank worksheets, no screen-only gridlines.
            if ((string) $template->document_type === 'appointment') {
                $file = $this->aligned_appointment_preview($source, $extension);
            } else {
                $file = $this->preview_file($source, $extension);
            }
            return empty($file) ? null : ['kind' => 'spreadsheet', 'html' => $this->sheet_preview_document($file)];
        }
        if ($extension === 'docx') {
            $html = $this->docx_preview_html($source);
            return $html === null ? null : ['kind' => 'docx', 'html' => $html];
        }
        return null;
    }

    /**
     * The template as it will print: signatories centred like the report,
     * with the placeholder text left as written on the template.
     */
    private function aligned_appointment_preview($source, $extension)
    {
        if (!class_exists('PhpOffice\\PhpSpreadsheet\\IOFactory')) {
            require_once FCPATH . 'vendor/autoload.php';
        }
        $output = $this->temp_output_path($extension);
        try {
            $book = IOFactory::load($source);
            $this->align_appointment_signatories($book);
            IOFactory::createWriter($book, $extension === 'xls' ? 'Xls' : 'Xlsx')->save($output);
            $book->disconnectWorksheets();
            return $this->preview_file($output, $extension);
        } finally {
            @unlink($output);
        }
    }

    /**
     * Stand-alone page for the template preview iframe: each worksheet on its
     * own white sheet, centred, at the column widths the office designed.
     */
    private function sheet_preview_document(array $file)
    {
        $html = '<!DOCTYPE html><html><head><meta charset="utf-8"><style type="text/css">' . $file['style'] . '</style>'
            . '<style>'
            . 'html,body{margin:0;padding:0;background:#eef2f7;}'
            . 'body{padding:24px 16px;}'
            . '.tp-sheet{box-sizing:border-box;width:max-content;margin:0 auto 24px;padding:28px 32px;background:#fff;border-radius:4px;box-shadow:0 10px 30px rgba(31,58,95,.12);}'
            . '.rp-sheet table{border-collapse:collapse;table-layout:fixed;width:100%;}'
            . '.rp-sheet td,.rp-sheet th{white-space:nowrap;}'
            . '</style></head><body>';
        foreach ($file['pages'] as $page) {
            $width = !empty($page['width']) ? ' style="width:' . (float) $page['width'] . 'pt;"' : '';
            $html .= '<div class="tp-sheet"><div class="rp-sheet"' . $width . '>' . $page['html'] . '</div></div>';
        }
        return $html . '</body></html>';
    }

    /**
     * Same conversion as preview(), but for any generated file on disk. The
     * spreadsheet branch is split into style + body so the sheet can be
     * printed inline on a page instead of being downloaded.
     */
    public function preview_file($path, $extension)
    {
        if (!is_file($path)) {
            return null;
        }
        $extension = strtolower((string) $extension);
        if (in_array($extension, ['xls', 'xlsx'], true)) {
            $html = $this->spreadsheet_preview_html($path);
            if ($html === null || $html === false) {
                return null;
            }
            $parts = $this->split_preview_html($html);
            return [
                'kind' => 'spreadsheet',
                'style' => $parts['style'],
                'pages' => $this->split_sheet_pages($parts['body'], $parts['style']),
            ];
        }
        if ($extension === 'docx') {
            // The template's own page (letterhead, text boxes, content
            // controls, tab stops...) so the preview follows the Word form.
            require_once APPPATH . 'libraries/Docx_page_renderer.php';
            $page = (new Docx_page_renderer($path))->render();
            return [
                'kind' => 'docx',
                'style' => '',
                'pages' => [['html' => $page['html'], 'width' => $page['width']]],
                'page' => $this->docx_page_setup($path),
            ];
        }
        return null;
    }

    /**
     * Rows past the end of the form. Excel keeps a sheet's used range long
     * after the content stops, and those blank rows would otherwise stretch
     * the printed page. Rows that still carry a border or fill are kept, so
     * the form's own frame stays intact.
     */
    private function trim_trailing_rows($sheet)
    {
        $highestRow = (int) $sheet->getHighestRow();
        if ($highestRow < 2) {
            return;
        }
        $highestColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($sheet->getHighestColumn());
        for ($row = $highestRow; $row >= 1; $row--) {
            if ($this->row_has_content($sheet, $row, $highestColumn)) {
                if ($row < $highestRow) {
                    $sheet->removeRow($row + 1, $highestRow - $row);
                }
                return;
            }
        }
    }

    /**
     * Blank columns past the right edge of the form. They still count toward
     * the sheet width, so the form would fill only part of the A4 width.
     */
    private function trim_trailing_columns($sheet)
    {
        $highestColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($sheet->getHighestColumn());
        $highestRow = (int) $sheet->getHighestRow();
        // A merged range reaching into a column keeps it.
        $lastMerged = 0;
        foreach ($sheet->getMergeCells() as $range) {
            $bounds = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::rangeBoundaries($range);
            $lastMerged = max($lastMerged, (int) $bounds[1][0]);
        }
        $last = $highestColumn;
        while ($last > max(1, $lastMerged) && !$this->column_has_content($sheet, $last, $highestRow)) {
            $last--;
        }
        if ($last < $highestColumn) {
            $sheet->removeColumnByIndex($last + 1, $highestColumn - $last);
        }
    }

    private function column_has_content($sheet, $column, $highestRow)
    {
        $letter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($column);
        foreach ($sheet->getDrawingCollection() as $drawing) {
            if (preg_replace('/[0-9]+/', '', $drawing->getCoordinates()) === $letter) {
                return true;
            }
        }
        for ($row = 1; $row <= $highestRow; $row++) {
            if ($this->cell_has_content($sheet, $letter . $row)) {
                return true;
            }
        }
        return false;
    }

    private function cell_has_content($sheet, $coordinate)
    {
        if (!$sheet->cellExists($coordinate)) {
            return false;
        }
        $cell = $sheet->getCell($coordinate);
        if (trim((string) $cell->getValue()) !== '') {
            return true;
        }
        $style = $cell->getStyle();
        $borders = $style->getBorders();
        $none = \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE;
        foreach ([$borders->getTop(), $borders->getBottom(), $borders->getLeft(), $borders->getRight()] as $border) {
            if ($border->getBorderStyle() !== $none) {
                return true;
            }
        }
        return $style->getFill()->getFillType() !== \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_NONE;
    }

    private function row_has_content($sheet, $row, $highestColumn)
    {
        $none = \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE;
        for ($column = 1; $column <= $highestColumn; $column++) {
            $coordinate = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($column) . $row;
            if (!$sheet->cellExists($coordinate)) {
                continue;
            }
            $cell = $sheet->getCell($coordinate);
            if (trim((string) $cell->getValue()) !== '') {
                return true;
            }
            $style = $cell->getStyle();
            $borders = $style->getBorders();
            foreach ([$borders->getTop(), $borders->getBottom(), $borders->getLeft(), $borders->getRight()] as $border) {
                if ($border->getBorderStyle() !== $none) {
                    return true;
                }
            }
            if ($style->getFill()->getFillType() !== \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_NONE) {
                return true;
            }
        }
        return false;
    }

    /**
     * One printable block per worksheet. The writer opens each sheet with its
     * own "page: pageN" div, so the blocks are split there; worksheets with no
     * content of their own are dropped instead of printing a blank page.
     */
    private function split_sheet_pages($body, $style)
    {
        $chunks = preg_split("/(?=<div style='page: page)/", $body, -1, PREG_SPLIT_NO_EMPTY);
        if (empty($chunks)) {
            return [['html' => $body, 'width' => null]];
        }
        $pages = [];
        foreach ($chunks as $chunk) {
            if (strpos($chunk, "<div style='page: page") !== 0) {
                continue; // the writer's preamble, not a worksheet
            }
            $text = html_entity_decode(strip_tags($chunk), ENT_QUOTES, 'UTF-8');
            if (trim(str_replace("\xC2\xA0", ' ', $text)) === '' && stripos($chunk, '<img') === false) {
                continue; // worksheet with nothing on it
            }
            $index = preg_match("/id='sheet([0-9]+)'/", $chunk, $match) ? (int) $match[1] : null;
            $pages[] = [
                'html' => $chunk,
                'width' => $index === null ? null : $this->sheet_design_width($style, $index),
            ];
        }
        return empty($pages) ? [['html' => $body, 'width' => null]] : $pages;
    }

    /**
     * The sheet's designed width, taken from the column widths the writer
     * emitted. Laying the sheet out at exactly this width keeps the columns
     * where the office put them instead of letting the paper squeeze them.
     */
    private function sheet_design_width($style, $sheetIndex)
    {
        if (!preg_match_all('/table\.sheet' . $sheetIndex . ' col\.col[0-9]+ \{ width:([0-9.]+)pt \}/', $style, $matches)) {
            return null;
        }
        $width = array_sum($matches[1]);
        return $width > 0 ? round($width, 2) : null;
    }

    /**
     * Paper the office actually set on the template. These forms are mostly
     * Legal/Folio, so printing them on a forced A4 sheet would cut them.
     */
    private function paper_sizes()
    {
        return [
            1 => ['Letter', 8.5, 11],
            3 => ['Tabloid', 11, 17],
            5 => ['Legal', 8.5, 14],
            8 => ['A3', 11.69, 16.54],
            9 => ['A4', 8.27, 11.69],
            11 => ['A5', 5.83, 8.27],
            13 => ['B5', 6.93, 9.84],
            14 => ['Folio', 8.5, 13],
        ];
    }

    /**
     * The document's own text block, so a Word layout keeps its line length
     * when it is placed on the A4 sheet.
     */
    private function page_setup_values($label, $width, $height, $margins)
    {
        $inches = function ($value) { return round((float) $value, 2) . 'in'; };
        $contentWidth = max(1, $width - $margins[1] - $margins[3]);
        return [
            'label' => $label . ' ' . round($width, 2) . ' × ' . round($height, 2) . ' in',
            'content_width' => $inches($contentWidth),
            'margin_top' => $inches(min(max($margins[0], 0.4), 1.5)),
            'margin_bottom' => $inches(min(max($margins[2], 0.4), 1.5)),
        ];
    }

    private function docx_page_setup($source)
    {
        $zip = new \ZipArchive();
        if ($zip->open($source) !== true) {
            return null;
        }
        $xml = $zip->getFromName('word/document.xml');
        $zip->close();
        if ($xml === false) {
            return null;
        }
        $dom = new \DOMDocument();
        if (!@$dom->loadXML($xml)) {
            return null;
        }
        $xpath = new \DOMXPath($dom);
        $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
        $size = $xpath->query('//w:sectPr/w:pgSz')->item(0);
        if (!$size) {
            return null;
        }
        $ns = 'http://schemas.openxmlformats.org/wordprocessingml/2006/main';
        $twips = function ($value, $fallback) { return ((float) $value) > 0 ? ((float) $value) / 1440 : $fallback; };
        $width = $twips($size->getAttributeNS($ns, 'w'), 8.5);
        $height = $twips($size->getAttributeNS($ns, 'h'), 13);
        $orientation = (string) $size->getAttributeNS($ns, 'orient');
        $margins = [0.75, 0.75, 0.75, 0.75];
        $pgMar = $xpath->query('//w:sectPr/w:pgMar')->item(0);
        if ($pgMar) {
            $margins = [
                $twips($pgMar->getAttributeNS($ns, 'top'), 0.75),
                $twips($pgMar->getAttributeNS($ns, 'right'), 0.75),
                $twips($pgMar->getAttributeNS($ns, 'bottom'), 0.75),
                $twips($pgMar->getAttributeNS($ns, 'left'), 0.75),
            ];
        }
        $label = 'Page';
        foreach ($this->paper_sizes() as $paper) {
            if (abs($paper[1] - $width) < 0.15 && abs($paper[2] - $height) < 0.15) {
                $label = $paper[0];
                break;
            }
        }
        if (strtolower($orientation) === 'landscape' && $height > $width) {
            $swap = $width;
            $width = $height;
            $height = $swap;
        }
        return $this->page_setup_values($label, $width, $height, $margins);
    }

    /**
     * Pulls the <style> and <body> out of a full HTML document. The writer's
     * bare "html {}" rule is re-scoped so it cannot restyle the host page.
     */
    private function split_preview_html($html)
    {
        $style = '';
        if (preg_match_all('#<style[^>]*>(.*?)</style>#is', $html, $matches)) {
            $style = implode("\n", $matches[1]);
            // The writer styles a whole document: re-scope its page-level rules
            // so they cannot restyle or page-break the page hosting the sheet.
            $style = preg_replace('/(^|\})\s*html\s*\{/', '$1 .rp-sheet {', $style);
            $style = str_replace('div + div {page-break-before: always;}', '.rp-sheet div + div {page-break-before: always;}', $style);
            // Screen-only gridlines override the workbook's own cell borders,
            // which would make the preview differ from the printed sheet.
            $style = preg_replace('/\.gridlines (?:td|th) \{border: 1px solid black;\}/', '', $style);
            // Each sheet names its own @page with Excel's margins; drop them so
            // every page prints on the host page's edge-to-edge A4 sheet.
            $style = preg_replace('/@page\s+page[0-9]+\s*\{[^}]*\}/', '', $style);
        }
        $body = $html;
        if (preg_match('#<body[^>]*>(.*)</body>#is', $html, $match)) {
            $body = $match[1];
        }
        return ['style' => $style, 'body' => $body];
    }

    private function spreadsheet_preview_html($source)
    {
        if (!class_exists('PhpOffice\\PhpSpreadsheet\\IOFactory')) {
            require_once FCPATH . 'vendor/autoload.php';
        }
        // GD warns about harmless colour profiles in some uploaded e-signature
        // PNGs; the warning would otherwise be printed into the form.
        $level = error_reporting(error_reporting() & ~E_WARNING);
        try {
            $book = IOFactory::load($source);
        } finally {
            error_reporting($level);
        }
        foreach ($book->getWorksheetIterator() as $sheet) {
            $this->trim_trailing_rows($sheet);
            $this->trim_trailing_columns($sheet);
        }
        try {
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Html($book);
            $writer->setEmbedImages(true);
            $writer->setPreCalculateFormulas(false);
            // CS Form 33-B carries its certifications on a second worksheet,
            // so the whole form has to be rendered, not only the first sheet.
            $writer->writeAllSheets();
            ob_start();
            try {
                $writer->save('php://output');
                return ob_get_clean();
            } catch (\Throwable $e) {
                ob_end_clean();
                throw $e;
            }
        } finally {
            $book->disconnectWorksheets();
        }
    }

    private function docx_preview_html($source)
    {
        $zip = new \ZipArchive();
        if ($zip->open($source) !== true) {
            return null;
        }
        $xml = $zip->getFromName('word/document.xml');
        $zip->close();
        if ($xml === false) {
            return null;
        }
        $dom = new \DOMDocument();
        if (!@$dom->loadXML($xml)) {
            return null;
        }
        $xpath = new \DOMXPath($dom);
        $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
        $body = $xpath->query('//w:body')->item(0);
        if (!$body) {
            return null;
        }
        $html = '';
        foreach ($body->childNodes as $node) {
            if (!($node instanceof \DOMElement)) {
                continue;
            }
            if ($node->localName === 'p') {
                $html .= $this->docx_paragraph($xpath, $node);
            } elseif ($node->localName === 'tbl') {
                $html .= $this->docx_table($xpath, $node);
            }
        }
        return $html;
    }

    private function docx_paragraph(\DOMXPath $xpath, \DOMElement $p)
    {
        $align = '';
        $jc = $xpath->query('./w:pPr/w:jc', $p)->item(0);
        if ($jc) {
            $map = ['center' => 'center', 'right' => 'right', 'both' => 'justify'];
            $val = $jc->getAttribute('w:val');
            if (isset($map[$val])) {
                $align = ' style="text-align:' . $map[$val] . ';"';
            }
        }
        $inner = '';
        foreach ($xpath->query('.//w:r', $p) as $run) {
            $inner .= $this->docx_run($xpath, $run);
        }
        if (trim(strip_tags($inner)) === '') {
            return '<p class="dp-empty"' . $align . '>&nbsp;</p>';
        }
        return '<p' . $align . '>' . $inner . '</p>';
    }

    private function docx_run(\DOMXPath $xpath, \DOMElement $r)
    {
        $out = '';
        foreach ($r->childNodes as $child) {
            if (!($child instanceof \DOMElement)) {
                continue;
            }
            if ($child->localName === 't') {
                $out .= htmlspecialchars($child->textContent, ENT_QUOTES, 'UTF-8');
            } elseif ($child->localName === 'tab') {
                $out .= '<span class="dp-tab"></span>';
            } elseif ($child->localName === 'br' || $child->localName === 'cr') {
                $out .= '<br>';
            }
        }
        if ($out === '') {
            return '';
        }
        $rPr = $xpath->query('./w:rPr', $r)->item(0);
        if (!$rPr) {
            return $out;
        }
        $size = $xpath->query('./w:sz', $rPr)->item(0);
        if ($size) {
            $pt = ((int) $size->getAttribute('w:val')) / 2;
            if ($pt > 0) {
                $out = '<span style="font-size:' . $pt . 'pt;">' . $out . '</span>';
            }
        }
        if ($xpath->query('./w:u', $rPr)->length > 0) {
            $out = '<u>' . $out . '</u>';
        }
        if ($xpath->query('./w:i', $rPr)->length > 0) {
            $out = '<em>' . $out . '</em>';
        }
        if ($xpath->query('./w:b', $rPr)->length > 0) {
            $out = '<strong>' . $out . '</strong>';
        }
        return $out;
    }

    private function docx_table(\DOMXPath $xpath, \DOMElement $tbl)
    {
        $html = '<table class="dp-tbl">';
        foreach ($xpath->query('./w:tr', $tbl) as $tr) {
            $html .= '<tr>';
            foreach ($xpath->query('./w:tc', $tr) as $tc) {
                $cell = '';
                foreach ($tc->childNodes as $cellChild) {
                    if ($cellChild instanceof \DOMElement && $cellChild->localName === 'p') {
                        $cell .= $this->docx_paragraph($xpath, $cellChild);
                    }
                }
                $html .= '<td>' . $cell . '</td>';
            }
            $html .= '</tr>';
        }
        return $html . '</table>';
    }

    private function number_to_words($number)
    {
        $number = (int) $number;
        if ($number === 0) {
            return 'ZERO';
        }
        $ones = ['', 'ONE', 'TWO', 'THREE', 'FOUR', 'FIVE', 'SIX', 'SEVEN', 'EIGHT', 'NINE', 'TEN',
            'ELEVEN', 'TWELVE', 'THIRTEEN', 'FOURTEEN', 'FIFTEEN', 'SIXTEEN', 'SEVENTEEN', 'EIGHTEEN', 'NINETEEN'];
        $tens = ['', '', 'TWENTY', 'THIRTY', 'FORTY', 'FIFTY', 'SIXTY', 'SEVENTY', 'EIGHTY', 'NINETY'];
        $underThousand = function ($n) use (&$underThousand, $ones, $tens) {
            $parts = [];
            if ($n >= 100) {
                $parts[] = $ones[(int) floor($n / 100)] . ' HUNDRED';
                $n %= 100;
            }
            if ($n >= 20) {
                // Hyphenated as on the CS form: THIRTY-ONE.
                $parts[] = $tens[(int) floor($n / 10)] . ($n % 10 > 0 ? '-' . $ones[$n % 10] : '');
                $n = 0;
            }
            if ($n > 0) {
                $parts[] = $ones[$n];
            }
            return implode(' ', $parts);
        };
        $scales = [1000000000 => 'BILLION', 1000000 => 'MILLION', 1000 => 'THOUSAND'];
        $parts = [];
        foreach ($scales as $scale => $label) {
            if ($number >= $scale) {
                $parts[] = $underThousand((int) floor($number / $scale)) . ' ' . $label;
                $number %= $scale;
            }
        }
        if ($number > 0) {
            $parts[] = $underThousand($number);
        }
        return implode(' ', $parts);
    }
}
