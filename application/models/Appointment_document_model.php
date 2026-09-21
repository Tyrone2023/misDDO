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

        $this->seed_guide_templates();
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

    public function values_for_row($row)
    {
        $groupNames = $this->position_groups();
        $first = trim((string) ($row->FirstName ?? ''));
        $middle = trim((string) ($row->MiddleName ?? ''));
        $last = trim((string) ($row->LastName ?? ''));
        $extension = trim((string) ($row->NameExtn ?? ''));
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

        $plantilla = $this->db->where('itemNo', (string) ($row->item_number ?? ''))->get('hris_plantilla')->row();
        $sg = !empty($plantilla) ? (int) $plantilla->sg : 0;
        $step = !empty($plantilla) ? (int) $plantilla->step : 0;
        $monthlySalary = !empty($plantilla) && (float) $plantilla->authAnnualSalary > 0
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
        if ($step < 1 && $sg > 0) {
            $step = 1;
        }
        if ($monthlySalary <= 0 && $sg > 0 && $this->db->table_exists('payroll_salary')) {
            $this->db->where('sgNo', (string) $sg)->where('stepNo', (string) $step);
            if ($this->db->field_exists('sgYear', 'payroll_salary')) {
                $this->db->order_by('sgYear', 'DESC');
            }
            $salaryRow = $this->db->get('payroll_salary')->row();
            $monthlySalary = !empty($salaryRow) ? (float) ($salaryRow->salary ?? 0) : 0;
        }

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

        $sdsName = $setting('sds');
        $sdsPosition = $setting('sdsPosition');
        $hrmoName = $setting('sigSR');
        $hrmoPosition = $setting('sigSRPosition');
        $agency = $setting('agency');

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
            'AGENCY' => $agency !== '' ? $agency : 'Department of Education',
        ];
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
        $tempBase = tempnam(sys_get_temp_dir(), 'appt_doc_');
        if ($tempBase === false) {
            throw new RuntimeException('Unable to create the report file.');
        }
        @unlink($tempBase);
        $output = $tempBase . '.' . $extension;
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
            $sheet->setCellValue('G28', mb_strtoupper($values['{{MONTHLY_SALARY_WORDS}}'], 'UTF-8'));
            $sheet->setCellValue('S28', $values['{{MONTHLY_SALARY}}'] !== '' ? '(P' . $values['{{MONTHLY_SALARY}}'] . ')' : '');
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
                $sheet->setCellValue('L51', mb_strtoupper($values['{{SDS_NAME}}'], 'UTF-8'));
            }
            if ($book->getSheetCount() > 1 && $values['{{HRMO_NAME}}'] !== '') {
                $book->getSheet(1)->setCellValue('L15', mb_strtoupper($values['{{HRMO_NAME}}'], 'UTF-8'));
                $book->getSheet(1)->setCellValue('L16', $values['{{HRMO_POSITION}}']);
            }
        }

        $writerType = strtolower(pathinfo($output, PATHINFO_EXTENSION)) === 'xls' ? 'Xls' : 'Xlsx';
        $writer = IOFactory::createWriter($book, $writerType);
        $writer->save($output);
        $book->disconnectWorksheets();
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
            ];
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
            ];
        }

        $replacements = $values + $legacy;
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);
            if (!preg_match('#^word/(document|header[0-9]+|footer[0-9]+)\.xml$#', $name)) {
                continue;
            }
            $xml = $zip->getFromIndex($i);
            if ($xml === false) {
                continue;
            }
            $xml = $this->replace_word_text($xml, $replacements);
            $zip->addFromString($name, $xml);
        }
        $zip->close();
    }

    /** Replace text even when Word has split it across several w:t runs. */
    private function replace_word_text($xml, array $replacements)
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
            $html = $this->spreadsheet_preview_html($source);
            return $html === null ? null : ['kind' => 'spreadsheet', 'html' => $html];
        }
        if ($extension === 'docx') {
            $html = $this->docx_preview_html($source);
            return $html === null ? null : ['kind' => 'docx', 'html' => $html];
        }
        return null;
    }

    private function spreadsheet_preview_html($source)
    {
        if (!class_exists('PhpOffice\\PhpSpreadsheet\\IOFactory')) {
            require_once FCPATH . 'vendor/autoload.php';
        }
        $book = IOFactory::load($source);
        try {
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Html($book);
            $writer->setEmbedImages(true);
            $writer->setPreCalculateFormulas(false);
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
                $parts[] = $tens[(int) floor($n / 10)];
                $n %= 10;
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
