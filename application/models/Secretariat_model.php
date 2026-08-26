<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Secretariat_model extends CI_Model
{
    /** users.position value used for the Secretariat's score-encoding-only logins. */
    const FIELD_ENCODER_POSITION = 'Field Encoder';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->ensure_table();
        $this->ensure_vacancy_table();
        $this->ensure_field_encoder_access_table();
        $this->ensure_field_evaluator_access_table();
        $this->ensure_assessment_table();
    }

    public function ensure_table(): void
    {
        // Lightweight guard so we can rely on the mapping table existing.
        $this->db->query("
            CREATE TABLE IF NOT EXISTS hris_secretariat_levels (
                id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                secretariat_user_id INT UNSIGNED NOT NULL,
                position_group INT UNSIGNED NOT NULL DEFAULT 1,
                job_type INT UNSIGNED NOT NULL,
                created_by INT UNSIGNED NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY uniq_secretariat_scope (secretariat_user_id, position_group, job_type),
                KEY idx_job_type (job_type)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");

        $this->ensure_position_group_column();
    }

    /**
     * The table shipped keyed on job_type only, which could never reach Related
     * Teaching / Non-Teaching vacancies because those are posted with job_type 0.
     * Widen it to (position group, job_type) in place, and fan the existing rows
     * out to School Administration too so nobody's reach silently shrinks - the
     * old job_type-only filter matched those vacancies as well.
     */
    private function ensure_position_group_column(): void
    {
        $debug = $this->db->db_debug;
        $this->db->db_debug = false;

        $col = $this->db->query(
            "select COLUMN_NAME from information_schema.COLUMNS
             where TABLE_SCHEMA = database()
               and TABLE_NAME = 'hris_secretariat_levels'
               and COLUMN_NAME = 'position_group'"
        );

        if (!$col || $col->num_rows() === 0) {
            $this->db->query("alter table `hris_secretariat_levels`
                add column `position_group` int unsigned not null default 1 after `secretariat_user_id`");
            $this->db->query("alter table `hris_secretariat_levels` drop index `uniq_secretariat_job`");
            $this->db->query("insert into `hris_secretariat_levels` (secretariat_user_id, position_group, job_type, created_by)
                select secretariat_user_id, 2, job_type, created_by
                from (select secretariat_user_id, job_type, created_by from `hris_secretariat_levels` where job_type between 1 and 4) legacy");
            $this->db->query("alter table `hris_secretariat_levels`
                add unique key `uniq_secretariat_scope` (secretariat_user_id, position_group, job_type)");
        }

        $this->db->db_debug = $debug;
    }

    /**
     * New table for per-vacancy secretariat coverage. The assignment page lets
     * admins tag a secretariat to an open job vacancy. Archived (Closed) jobs
     * are removed from here via remove_vacancy_assignments().
     */
    public function ensure_vacancy_table(): void
    {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS hris_secretariat_vacancies (
                id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                secretariat_user_id INT UNSIGNED NOT NULL,
                job_id INT UNSIGNED NOT NULL,
                created_by INT UNSIGNED NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY uniq_secretariat_vacancy (secretariat_user_id, job_id),
                KEY idx_job_id (job_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");
    }

    /**
     * Position groups as posted on Page/jobVacancy (hris_jobvacancy.position).
     */
    public function position_groups(): array
    {
        return [
            1 => 'Teaching',
            2 => 'School Administration',
            3 => 'Related Teaching',
            4 => 'Non-Teaching',
        ];
    }

    public function job_types_map(): array
    {
        return [
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
            11 => 'SHS Academic and Core Subjects',
            12 => 'SHS Arts and Design Track',
            13 => 'SHS Sports Track',
            14 => 'SHS Technical-Vocational (TVL) Track',
            15 => 'Elementary - SPIMS',
            16 => 'Junior High School - SPIMS',
            17 => 'DOST - (RA 7687)',
            18 => 'DOST - (RA 10612)',
            19 => '(SST I)',
            20 => 'FOR TESTING PURPOSES (DO NOT APPLY)',
        ];
    }

    /**
     * Every assignable scope, mirroring the group type dropdowns on the job
     * vacancy posting form. Related Teaching and Non-Teaching vacancies carry no
     * group type, so they get the single job_type 0 wildcard = the whole group.
     */
    public function scope_catalog(): array
    {
        $levels = array_keys($this->job_types_map());

        return [
            1 => [
                'label'     => 'Teaching',
                'sub'       => 'Group type picked when posting a teaching vacancy',
                'icon'      => 'mdi-school-outline',
                'tone'      => 'blue',
                'job_types' => $levels,
            ],
            2 => [
                'label'     => 'School Administration',
                'sub'       => 'School heads and principals, by level',
                'icon'      => 'mdi-office-building-outline',
                'tone'      => 'purple',
                'job_types' => [1, 2, 3, 4],
            ],
            3 => [
                'label'     => 'Related Teaching',
                'sub'       => 'Posted without a group type',
                'icon'      => 'mdi-book-open-page-variant-outline',
                'tone'      => 'teal',
                'job_types' => [0],
            ],
            4 => [
                'label'     => 'Non-Teaching',
                'sub'       => 'Posted without a group type',
                'icon'      => 'mdi-account-tie-outline',
                'tone'      => 'amber',
                'job_types' => [0],
            ],
        ];
    }

    public function scope_label(int $positionGroup, int $jobType): string
    {
        $groups = $this->position_groups();
        $group  = $groups[$positionGroup] ?? ('Group ' . $positionGroup);

        if ($jobType === 0) {
            return $group;
        }

        $levels = $this->job_types_map();
        return $group . ' - ' . ($levels[$jobType] ?? ('Level ' . $jobType));
    }

    /**
     * Open vacancy count per scope, keyed "positionGroup:jobType", so the assign
     * screen can show where the work actually is.
     */
    public function open_vacancy_counts(): array
    {
        $rows = $this->db
            ->select('position, job_type, COUNT(*) AS total', false)
            ->from('hris_jobvacancy')
            ->where('jvStatus', 'Open')
            ->group_by(['position', 'job_type'])
            ->get()
            ->result();

        $counts = [];
        foreach ($rows as $row) {
            $counts[(int) $row->position . ':' . (int) $row->job_type] = (int) $row->total;
        }
        return $counts;
    }

    /**
     * Eligible job vacancies for secretariat tagging. Archived (Closed)
     * vacancies are excluded; all other status values remain taggable.
     */
    public function open_vacancy_list(): array
    {
        return $this->db
            ->from('hris_jobvacancy')
            ->where('jvStatus !=', 'Closed')
            ->order_by('jobID', 'desc')
            ->get()
            ->result();
    }

    /**
     * Accepts "positionGroup:jobType" scope keys, a job_id row, or the legacy
     * flat job_type list.
     */
    public function normalize_scopes(array $input): array
    {
        $out = [];

        foreach ($input as $item) {
            if (is_array($item)) {
                $out[] = [
                    'position_group' => isset($item['position_group']) ? (int) $item['position_group'] : null,
                    'job_type'       => (int) ($item['job_type'] ?? 0),
                    'job_id'         => isset($item['job_id']) ? (int) $item['job_id'] : null,
                ];
            } elseif (is_object($item)) {
                $out[] = [
                    'position_group' => isset($item->position_group) ? (int) $item->position_group : null,
                    'job_type'       => (int) ($item->job_type ?? 0),
                    'job_id'         => isset($item->job_id) ? (int) $item->job_id : null,
                ];
            } elseif (is_string($item) && strpos($item, ':') !== false) {
                [$group, $type] = explode(':', $item, 2);
                $out[] = ['position_group' => (int) $group, 'job_type' => (int) $type, 'job_id' => null];
            } else {
                // legacy: job_type with no position group, matched across all groups
                $out[] = ['position_group' => null, 'job_type' => (int) $item, 'job_id' => null];
            }
        }

        return $out;
    }

    /**
     * The assigned scopes as one OR-grouped SQL predicate on the joined vacancy.
     * Every value is an integer id, so this interpolates safely.
     *
     * @return string '' when there is nothing to filter on.
     */
    public function scope_where_sql(array $scopes, string $positionCol = 'jv.position', string $jobTypeCol = 'jv.job_type', string $jobIdCol = 'jv.jobID'): string
    {
        $scopes = $this->normalize_scopes($scopes);
        if (empty($scopes)) {
            return '';
        }

        $byGroup = [];
        $legacy  = [];
        $jobIds  = [];
        foreach ($scopes as $scope) {
            if (!empty($scope['job_id'])) {
                $jobIds[] = $scope['job_id'];
                continue;
            }
            if ($scope['position_group'] === null) {
                $legacy[] = $scope['job_type'];
                continue;
            }
            $byGroup[$scope['position_group']][] = $scope['job_type'];
        }

        $parts = [];

        foreach ($byGroup as $group => $types) {
            $types = array_values(array_unique(array_map('intval', $types)));

            // job_type 0 is the wildcard for groups posted without a group type
            if (in_array(0, $types, true)) {
                $parts[] = '(' . $positionCol . ' = ' . (int) $group . ')';
                continue;
            }
            $parts[] = '(' . $positionCol . ' = ' . (int) $group
                . ' AND ' . $jobTypeCol . ' IN (' . implode(',', $types) . '))';
        }

        if (!empty($legacy)) {
            $legacy  = array_values(array_unique(array_map('intval', $legacy)));
            $parts[] = '(' . $jobTypeCol . ' IN (' . implode(',', $legacy) . '))';
        }

        if (!empty($jobIds)) {
            $jobIds = array_values(array_unique(array_map('intval', $jobIds)));
            $parts[] = '(' . $jobIdCol . ' IN (' . implode(',', $jobIds) . '))';
        }

        return '(' . implode(' OR ', $parts) . ')';
    }

    /**
     * Applies the assigned scopes to the pending query on the joined vacancy.
     */
    private function apply_scopes(array $scopes, string $positionCol = 'jv.position', string $jobTypeCol = 'jv.job_type', string $jobIdCol = 'jv.jobID'): void
    {
        $sql = $this->scope_where_sql($scopes, $positionCol, $jobTypeCol, $jobIdCol);
        if ($sql !== '') {
            $this->db->where($sql, null, false);
        }
    }

    /**
     * Merge duplicate hris_applications_rating rows based on record_no, appID, job_type, fy.
     * Keeps the newest row and backfills any 0.00001 ratings with non-zero values from duplicates.
     */
    public function merge_rating_duplicates(): void
    {
        $dups = $this->db
            ->select('record_no, appID, job_type, fy, COUNT(*) as cnt', false)
            ->from('hris_applications_rating')
            ->group_by(['record_no', 'appID', 'job_type', 'fy'])
            ->having('cnt >', 1)
            ->get()
            ->result();

        if (empty($dups)) {
            return;
        }

        foreach ($dups as $dup) {
            $rows = $this->db
                ->from('hris_applications_rating')
                ->where('record_no', $dup->record_no)
                ->where('appID', $dup->appID)
                ->where('job_type', $dup->job_type)
                ->where('fy', $dup->fy)
                ->order_by('id', 'desc') // newest first
                ->get()
                ->result_array();

            if (count($rows) < 2) {
                continue;
            }

            $keeper = $rows[0];
            $toDelete = [];
            $fields = ['education','training','experience','let_rating','demo_rating','tr_rating','total_points','eval_id1','eval_id2','eval_id3'];

            for ($i = 1; $i < count($rows); $i++) {
                $row = $rows[$i];
                $toDelete[] = $row['id'];
                foreach ($fields as $f) {
                    // prefer non-zero rating or non-empty evaluator ids
                    if (isset($row[$f])) {
                        $isRating = in_array($f, ['education','training','experience','let_rating','demo_rating','tr_rating','total_points']);
                        $isEval = in_array($f, ['eval_id1','eval_id2','eval_id3']);
                        if ($isRating) {
                            if ((float)$keeper[$f] == 0.00001 && (float)$row[$f] != 0.00001) {
                                $keeper[$f] = $row[$f];
                            }
                        } elseif ($isEval) {
                            if ((int)$keeper[$f] === 0 && (int)$row[$f] !== 0) {
                                $keeper[$f] = $row[$f];
                            }
                        }
                    }
                }
            }

            // Recompute total_points if any rating changed
            $keeper['total_points'] = ($keeper['education'] ?? 0) + ($keeper['training'] ?? 0) + ($keeper['experience'] ?? 0)
                                    + ($keeper['let_rating'] ?? 0) + ($keeper['demo_rating'] ?? 0) + ($keeper['tr_rating'] ?? 0);

            // Update keeper
            $this->db->where('id', $keeper['id'])->update('hris_applications_rating', $keeper);

            // Delete the rest
            if (!empty($toDelete)) {
                $this->db->where_in('id', $toDelete)->delete('hris_applications_rating');
            }
        }
    }

    public function list_secretariats(): array
    {
        return $this->db
            ->select('id, fname, mname, lname, username')
            ->from('users')
            ->where('position', 'Secretariat')
            ->order_by('lname', 'asc')
            ->order_by('fname', 'asc')
            ->get()
            ->result();
    }

    /**
     * map[userId] => [jobID, ...] for the vacancy-based assign screen.
     */
    public function assignments_indexed(): array
    {
        $rows = $this->db
            ->select('secretariat_user_id, job_id')
            ->from('hris_secretariat_vacancies')
            ->order_by('secretariat_user_id', 'asc')
            ->order_by('job_id', 'asc')
            ->get()
            ->result();

        $map = [];
        foreach ($rows as $row) {
            $uid = (int) $row->secretariat_user_id;
            $map[$uid][] = (int) $row->job_id;
        }
        return $map;
    }

    /**
     * Legacy shape: the distinct job types only, with no position group.
     * Kept so anything still filtering on job_type alone keeps working.
     */
    public function user_job_types(int $userId): array
    {
        $rows = $this->db
            ->select('job_type')
            ->distinct()
            ->from('hris_secretariat_levels')
            ->where('secretariat_user_id', $userId)
            ->get()
            ->result();

        $out = [];
        foreach ($rows as $row) {
            $out[] = (int) $row->job_type;
        }
        return $out;
    }

    /**
     * The assigned scopes for a secretariat user. Only per-vacancy assignments
     * are returned, as ['job_id' => int].
     */
    public function user_scopes(int $userId): array
    {
        $out = [];

        $rows = $this->db
            ->select('sv.job_id')
            ->from('hris_secretariat_vacancies sv')
            ->join('hris_jobvacancy j', 'j.jobID = sv.job_id AND j.jvStatus != "Closed"')
            ->where('sv.secretariat_user_id', $userId)
            ->order_by('sv.job_id', 'asc')
            ->get()
            ->result();

        foreach ($rows as $row) {
            $out[] = ['job_id' => (int) $row->job_id];
        }

        return $out;
    }

    public function user_scope_labels(int $userId): array
    {
        $labels = [];

        $vacancyRows = $this->db
            ->select('j.jobTitle')
            ->from('hris_secretariat_vacancies sv')
            ->join('hris_jobvacancy j', 'j.jobID = sv.job_id AND j.jvStatus != "Closed"')
            ->where('sv.secretariat_user_id', $userId)
            ->order_by('j.jobTitle', 'asc')
            ->get()
            ->result();

        foreach ($vacancyRows as $row) {
            $labels[] = (string) $row->jobTitle;
        }

        return array_values(array_unique($labels));
    }

    /**
     * Open vacancies explicitly tagged to a Secretariat account, together with
     * the applicant workload used by the dashboard and tagging screen.
     *
     * The headline figures (applicant_total, tagged_total) deliberately cover
     * every application received for the vacancy, whatever happened to it
     * afterwards. Filtering them down to the still-taggable statuses made the
     * totals shrink as soon as an applicant was endorsed, rated, or
     * disqualified, so a vacancy appeared to lose applicants and an evaluator
     * appeared to lose work they had already done.
     *
     * applicant_total = tagged_total + untagged_total, always. pending_total is
     * the slice of untagged_total that can still be acted on today.
     */
    public function tagging_vacancies(int $userId): array
    {
        return $this->db
            ->select("j.jobID, j.jobTitle, j.position, j.job_type, j.sy, j.itemNo, j.department,
                COUNT(DISTINCT a.appID) AS applicant_total,
                COUNT(DISTINCT CASE
                    WHEN a.appStatus = 'Application Submitted' AND a.dq != 2 THEN a.appID
                END) AS submitted_total,
                COUNT(DISTINCT CASE
                    WHEN a.appStatus = 'Validated' AND a.dq != 2 THEN a.appID
                END) AS validated_total,
                COUNT(DISTINCT CASE
                    WHEN a.appStatus IN ('Endorsed for Rating', 'Rated', 'Confirmed') AND a.dq != 2 THEN a.appID
                END) AS evaluated_total,
                COUNT(DISTINCT CASE WHEN a.dq = 2 THEN a.appID END) AS dq_total,
                COUNT(DISTINCT CASE WHEN ra.id IS NOT NULL THEN a.appID END) AS tagged_total,
                COUNT(DISTINCT CASE WHEN a.appID IS NOT NULL AND ra.id IS NULL THEN a.appID END) AS untagged_total,
                COUNT(DISTINCT CASE
                    WHEN ra.id IS NULL AND a.dq != 2 AND a.appStatus IN ('Application Submitted', 'Validated') THEN a.appID
                END) AS pending_total", false)
            ->from('hris_secretariat_vacancies sv')
            ->join('hris_jobvacancy j', 'j.jobID = sv.job_id')
            ->join('hris_applications a', 'a.jobID = j.jobID', 'left')
            ->join('hris_rater_assignments ra', 'ra.app_id = a.appID', 'left')
            ->where('sv.secretariat_user_id', $userId)
            ->where('j.jvStatus !=', 'Closed')
            ->group_by(['j.jobID', 'j.jobTitle', 'j.position', 'j.job_type', 'j.sy', 'j.itemNo', 'j.department'])
            ->order_by('j.sy', 'desc')
            ->order_by('j.jobTitle', 'asc')
            ->get()
            ->result();
    }

    public function secretariat_has_vacancy(int $userId, int $jobId): bool
    {
        return (bool) $this->db
            ->from('hris_secretariat_vacancies sv')
            ->join('hris_jobvacancy j', 'j.jobID = sv.job_id')
            ->where('sv.secretariat_user_id', $userId)
            ->where('sv.job_id', $jobId)
            ->where('j.jvStatus !=', 'Closed')
            ->count_all_results();
    }

    /**
     * Every applicant for one vacancy assigned to the current Secretariat user,
     * with the latest evaluator tag joined per row.
     *
     * Rows past the tagging stage (endorsed, rated, confirmed, disqualified)
     * are returned too, flagged with is_taggable = 0, so the tagged list and
     * the evaluator distribution keep showing work that is already done. Only
     * is_taggable rows can be tagged or reassigned.
     */
    public function applicants_for_tagging(int $userId, int $jobId): array
    {
        if (!$this->secretariat_has_vacancy($userId, $jobId)) {
            return [];
        }

        $latestAssignment = $this->db
            ->select('app_id, MAX(id) AS assignment_id', false)
            ->from('hris_rater_assignments')
            ->group_by('app_id')
            ->get_compiled_select();

        return $this->db
            ->select("a.appID, a.applicant_id, a.jobID, a.empEmail, a.appStatus, a.dateSubmitted,
                a.app_year, a.district, a.pre_school, a.dq,
                CASE
                    WHEN a.dq != 2 AND a.appStatus IN ('Application Submitted', 'Validated') THEN 1
                    ELSE 0
                END AS is_taggable,
                j.jobTitle, j.position, j.job_type, j.sy,
                COALESCE(ha.record_no, ha2.record_no, hs.IDNumber, a.applicant_id) AS record_no,
                COALESCE(ha.id, ha2.id, hs.IDNumber, a.applicant_id) AS profile_id,
                COALESCE(ha.FirstName, ha2.FirstName, hs.FirstName, '') AS FirstName,
                COALESCE(ha.MiddleName, ha2.MiddleName, hs.MiddleName, '') AS MiddleName,
                COALESCE(ha.LastName, ha2.LastName, hs.LastName, '') AS LastName,
                COALESCE(ha.NameExtn, ha2.NameExtn, hs.NameExtn, '') AS NameExtn,
                COALESCE(ha.specialization, ha2.specialization, '') AS specialization,
                CASE
                    WHEN ha.id IS NOT NULL OR ha2.id IS NOT NULL THEN 'ma'
                    WHEN hs.IDNumber IS NOT NULL THEN 'ma_staff'
                    ELSE ''
                END AS profile_route,
                s.schoolName,
                ra.id AS assignment_id, ra.rater_user_id, ra.assigned_at,
                CONCAT_WS(' ', NULLIF(TRIM(u.fname), ''), NULLIF(TRIM(u.mname), ''), NULLIF(TRIM(u.lname), '')) AS evaluator_name", false)
            ->from('hris_applications a')
            ->join('hris_jobvacancy j', 'j.jobID = a.jobID')
            ->join('hris_applicant ha', 'ha.id = a.applicant_id', 'left')
            ->join('hris_applicant ha2', 'ha2.record_no = CONVERT(CAST(a.applicant_id AS CHAR) USING latin1) COLLATE latin1_swedish_ci AND ha.id IS NULL', 'left', false)
            ->join('hris_staff hs', 'CONVERT(hs.IDNumber USING utf8mb4) COLLATE utf8mb4_general_ci = a.empEmail AND ha.id IS NULL AND ha2.id IS NULL', 'left', false)
            ->join('schools s', 's.schoolID = CONVERT(CAST(a.pre_school AS CHAR) USING utf8mb4) COLLATE utf8mb4_unicode_ci', 'left', false)
            ->join("($latestAssignment) latest_ra", 'latest_ra.app_id = a.appID', 'left')
            ->join('hris_rater_assignments ra', 'ra.id = latest_ra.assignment_id', 'left')
            ->join('users u', 'u.id = ra.rater_user_id', 'left')
            ->where('a.jobID', $jobId)
            ->order_by('is_taggable', 'desc')
            ->order_by('a.appStatus', 'desc')
            ->order_by('a.dateSubmitted', 'desc')
            ->order_by('ha.LastName', 'asc')
            ->get()
            ->result();
    }

    /**
     * Tagged applicants per evaluator for one vacancy. Counts every applicant
     * an evaluator was given, plus the breakdown that explains where they are
     * now, so the distribution never drops work that has already been rated.
     */
    public function evaluator_tag_counts(int $userId, int $jobId): array
    {
        if (!$this->secretariat_has_vacancy($userId, $jobId)) {
            return [];
        }

        return $this->db
            ->select("ra.rater_user_id,
                CONCAT_WS(' ', NULLIF(TRIM(u.fname), ''), NULLIF(TRIM(u.mname), ''), NULLIF(TRIM(u.lname), '')) AS evaluator_name,
                COUNT(DISTINCT a.appID) AS tagged_total,
                COUNT(DISTINCT CASE
                    WHEN a.dq != 2 AND a.appStatus IN ('Application Submitted', 'Validated') THEN a.appID
                END) AS pending_total,
                COUNT(DISTINCT CASE
                    WHEN a.dq != 2 AND a.appStatus IN ('Endorsed for Rating', 'Rated', 'Confirmed') THEN a.appID
                END) AS evaluated_total,
                COUNT(DISTINCT CASE WHEN a.dq = 2 THEN a.appID END) AS dq_total", false)
            ->from('hris_rater_assignments ra')
            ->join('hris_applications a', 'a.appID = ra.app_id')
            ->join('users u', 'u.id = ra.rater_user_id', 'left')
            ->where('a.jobID', $jobId)
            ->group_by(['ra.rater_user_id', 'u.fname', 'u.mname', 'u.lname'])
            ->order_by('tagged_total', 'desc')
            ->order_by('evaluator_name', 'asc')
            ->get()
            ->result();
    }

    /* ------------------------------------------------------------------ *
     * Secretariat interview and written-examination score encoding
     * ------------------------------------------------------------------ */

    /**
     * Applicants whose Interview and Written Examination scores are stored in
     * hris_rating_none. The latest rating and qualification-review rows are
     * joined once per application so duplicate historical rows cannot duplicate
     * an applicant on the encoding screen.
     */
    public function score_entry_applicants(int $userId, int $jobId, int $appId = 0): array
    {
        if (!$this->secretariat_has_vacancy($userId, $jobId)) {
            return [];
        }

        $vacancy = $this->db
            ->select('jobID, position')
            ->where('jobID', $jobId)
            ->where_not_in('position', [1, 5])
            ->get('hris_jobvacancy')
            ->row();

        if (empty($vacancy)) {
            return [];
        }

        $latestRating = $this->db
            ->select('appID, MAX(id) AS latest_id', false)
            ->from('hris_rating_none')
            ->group_by('appID')
            ->get_compiled_select();

        $latestDq = $this->db
            ->select('appID, MAX(id) AS latest_id', false)
            ->from('hris_app_dq')
            ->group_by('appID')
            ->get_compiled_select();

        $latestRater = $this->db
            ->select('app_id, MAX(id) AS latest_id', false)
            ->from('hris_rater_assignments')
            ->group_by('app_id')
            ->get_compiled_select();

        return $this->db
            ->select("a.appID, a.applicant_id, a.jobID, a.empEmail, a.appStatus, a.dateSubmitted,
                a.app_year, a.pre_school, a.dq,
                j.jobTitle, j.position, j.job_type, j.sy, j.itemNo, j.department,
                COALESCE(ha.record_no, ha2.record_no, a.empEmail, a.applicant_id) AS record_no,
                COALESCE(ha.id, ha2.id, hs.IDNumber, a.applicant_id) AS profile_id,
                COALESCE(ha.FirstName, ha2.FirstName, hs.FirstName, '') AS FirstName,
                COALESCE(ha.MiddleName, ha2.MiddleName, hs.MiddleName, '') AS MiddleName,
                COALESCE(ha.LastName, ha2.LastName, hs.LastName, '') AS LastName,
                COALESCE(ha.NameExtn, ha2.NameExtn, hs.NameExtn, '') AS NameExtn,
                CASE
                    WHEN ha.id IS NOT NULL OR ha2.id IS NOT NULL THEN 'ma'
                    WHEN hs.IDNumber IS NOT NULL THEN 'ma_staff'
                    ELSE ''
                END AS profile_route,
                dq.reason AS dq_reason,
                ra.rater_user_id,
                CONCAT_WS(' ', NULLIF(TRIM(u.fname), ''), NULLIF(TRIM(u.mname), ''), NULLIF(TRIM(u.lname), '')) AS evaluator_name,
                r.id AS rating_id, r.interview, r.written, r.total_points,
                r.eval_id1, r.eval_id2, r.eval_id3", false)
            ->from('hris_applications a')
            ->join('hris_jobvacancy j', 'j.jobID = a.jobID')
            ->join('hris_applicant ha', 'ha.id = a.applicant_id', 'left')
            ->join('hris_applicant ha2', 'ha2.record_no = CONVERT(CAST(a.applicant_id AS CHAR) USING latin1) COLLATE latin1_swedish_ci AND ha.id IS NULL', 'left', false)
            ->join('hris_staff hs', 'CONVERT(hs.IDNumber USING utf8mb4) COLLATE utf8mb4_general_ci = a.empEmail AND ha.id IS NULL AND ha2.id IS NULL', 'left', false)
            ->join("($latestRating) latest_rating", 'latest_rating.appID = a.appID', 'left')
            ->join('hris_rating_none r', 'r.id = latest_rating.latest_id', 'left')
            ->join("($latestDq) latest_dq", 'latest_dq.appID = a.appID', 'left')
            ->join('hris_app_dq dq', 'dq.id = latest_dq.latest_id', 'left')
            ->join("($latestRater) latest_rater", 'latest_rater.app_id = a.appID', 'left')
            ->join('hris_rater_assignments ra', 'ra.id = latest_rater.latest_id', 'left')
            ->join('users u', 'u.id = ra.rater_user_id', 'left')
            ->where('a.jobID', $jobId)
            ->where_not_in('j.position', [1, 5])
            ->group_start()
                ->where($appId > 0 ? 'a.appID' : 'a.appID >', $appId > 0 ? $appId : 0)
            ->group_end()
            ->order_by('a.dq', 'asc')
            ->order_by('ha.LastName', 'asc')
            ->order_by('hs.LastName', 'asc')
            ->order_by('a.appID', 'desc')
            ->get()
            ->result();
    }

    /** One application, after applying the same vacancy/role scope as the list. */
    public function score_entry_application(int $userId, int $appId)
    {
        if ($appId <= 0) {
            return null;
        }

        $application = $this->db
            ->select('jobID')
            ->where('appID', $appId)
            ->get('hris_applications')
            ->row();

        if (empty($application)) {
            return null;
        }

        $rows = $this->score_entry_applicants($userId, (int) $application->jobID, $appId);
        return $rows[0] ?? null;
    }

    /**
     * Save either or both scores into the exact rating row read by Pages/ma.
     * A missing row is initialized with the same 0.00001 sentinel used by the
     * regular rating forms; a legacy row under another record number is
     * normalized instead of creating a second rating for the application.
     */
    public function save_interview_written_scores(
        int $userId,
        int $appId,
        ?float $interview,
        ?float $written
    ): array {
        $application = $this->score_entry_application($userId, $appId);

        if (empty($application)) {
            return ['ok' => false, 'message' => 'The application or assigned vacancy is no longer available.'];
        }

        if ($interview === null && $written === null) {
            return ['ok' => false, 'message' => 'Enter an Interview or Written Examination score.'];
        }

        $recordNo = trim((string) ($application->record_no ?? ''));
        if ($recordNo === '') {
            $recordNo = trim((string) ($application->applicant_id ?? $application->empEmail ?? ''));
        }

        $this->db->trans_begin();

        $rating = $this->db
            ->where('appID', $appId)
            ->where('record_no', $recordNo)
            ->order_by('id', 'desc')
            ->limit(1)
            ->get('hris_rating_none')
            ->row();

        if (empty($rating)) {
            $rating = $this->db
                ->where('appID', $appId)
                ->order_by('id', 'desc')
                ->limit(1)
                ->get('hris_rating_none')
                ->row();

            if (!empty($rating)) {
                $this->db->where('id', (int) $rating->id)->update('hris_rating_none', ['record_no' => $recordNo]);
                $rating->record_no = $recordNo;
            }
        }

        if (empty($rating)) {
            $data = rating_required_defaults('hris_rating_none');
            foreach (rating_score_fields('hris_rating_none') as $field) {
                $data[$field] = .00001;
            }
            $data['appID'] = $appId;
            $data['record_no'] = $recordNo;
            $data['job_type'] = (int) $application->position;
            $data['fy'] = (int) $application->sy;
            // eval_id1 belongs to the evaluator who scores Education through
            // ALD. Leaving it at 0 keeps the Rate buttons on Pages/ma visible
            // to the assigned evaluator - rp_reg_none only renders them when
            // eval_id1 is 0 or matches the session.
            $data['eval_id1'] = 0;

            $this->db->insert('hris_rating_none', $data);
            $rating = $this->db->where('id', (int) $this->db->insert_id())->get('hris_rating_none')->row();

            if (empty($rating)) {
                $this->db->trans_rollback();
                return ['ok' => false, 'message' => 'The rating record could not be initialized. Please try again.'];
            }
        }

        $old = [
            'interview' => isset($rating->interview) ? (float) $rating->interview : null,
            'written' => isset($rating->written) ? (float) $rating->written : null,
        ];
        $updates = [];

        if ($interview !== null) {
            $updates['interview'] = $interview;
            if ((int) ($rating->eval_id2 ?? 0) === 0) {
                $updates['eval_id2'] = $userId;
            }
        }
        if ($written !== null) {
            $updates['written'] = $written;
            if ((int) ($rating->eval_id3 ?? 0) === 0) {
                $updates['eval_id3'] = $userId;
            }
        }

        // An old application can carry duplicate rating rows. Keep the two
        // Secretariat-owned scores and the canonical record key consistent on
        // every copy so the legacy MA lookup cannot display a stale duplicate.
        $updates['record_no'] = $recordNo;
        $this->db->where('appID', $appId)->update('hris_rating_none', $updates);
        $this->db
            ->set('total_points', 'COALESCE(educ,0)+COALESCE(trainings,0)+COALESCE(experience,0)+COALESCE(performance,0)+COALESCE(oa,0)+COALESCE(ae,0)+COALESCE(ald,0)+COALESCE(interview,0)+COALESCE(written,0)+COALESCE(skills,0)', false)
            ->where('appID', $appId)
            ->update('hris_rating_none');

        // Skills is optional. Once every other non-teaching component has a
        // real score, promote the workflow without making Secretariat click a
        // separate Rated action.
        $this->Reg->auto_mark_rated($appId);
        $statusRow = $this->db
            ->select('appStatus')
            ->where('appID', $appId)
            ->get('hris_applications')
            ->row();

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return ['ok' => false, 'message' => 'The scores could not be saved. Please try again.'];
        }

        $this->db->trans_commit();

        return [
            'ok' => true,
            'application' => $application,
            'application_status' => (string) ($statusRow->appStatus ?? $application->appStatus ?? ''),
            'old' => $old,
            'new' => ['interview' => $interview, 'written' => $written],
        ];
    }

    /** Dashboard progress for score-eligible assigned vacancies. */
    public function score_entry_counts(int $userId): array
    {
        $latestRating = $this->db
            ->select('appID, MAX(id) AS latest_id', false)
            ->from('hris_rating_none')
            ->group_by('appID')
            ->get_compiled_select();

        $rows = $this->db
            ->select("sv.job_id,
                COUNT(DISTINCT a.appID) AS total,
                COUNT(DISTINCT CASE
                    WHEN r.id IS NOT NULL AND ABS(COALESCE(r.interview, 0.00001) - 0.00001) > 0.000001 THEN a.appID
                END) AS interview_encoded,
                COUNT(DISTINCT CASE
                    WHEN r.id IS NOT NULL AND ABS(COALESCE(r.written, 0.00001) - 0.00001) > 0.000001 THEN a.appID
                END) AS written_encoded,
                COUNT(DISTINCT CASE
                    WHEN r.id IS NOT NULL
                     AND ABS(COALESCE(r.interview, 0.00001) - 0.00001) > 0.000001
                     AND ABS(COALESCE(r.written, 0.00001) - 0.00001) > 0.000001 THEN a.appID
                END) AS complete", false)
            ->from('hris_secretariat_vacancies sv')
            ->join('hris_jobvacancy j', 'j.jobID = sv.job_id')
            ->join('hris_applications a', 'a.jobID = sv.job_id', 'left')
            ->join("($latestRating) latest_rating", 'latest_rating.appID = a.appID', 'left')
            ->join('hris_rating_none r', 'r.id = latest_rating.latest_id', 'left')
            ->where('sv.secretariat_user_id', $userId)
            ->where('j.jvStatus !=', 'Closed')
            ->where_not_in('j.position', [1, 5])
            ->group_by('sv.job_id')
            ->get()
            ->result();

        $counts = [];
        foreach ($rows as $row) {
            $counts[(int) $row->job_id] = [
                'total' => (int) $row->total,
                'interview' => (int) $row->interview_encoded,
                'written' => (int) $row->written_encoded,
                'complete' => (int) $row->complete,
            ];
        }

        return $counts;
    }

    /* ------------------------------------------------------------------ *
     * Retention of points
     *
     * An applicant who was already rated for an earlier vacancy can ask to
     * keep those points instead of being rated again. The request lands in
     * hris_rating_request with stat 0, and is resolved either by copying the
     * scores off the earlier application or - when the earlier score is not on
     * file here - by encoding it manually. Both routes write the same rating
     * row and mark the same request as granted.
     * ------------------------------------------------------------------ */

    /**
     * Criteria a retention carries, as label => rating column, covering the
     * same columns as Pages::retention_score_map() which drives the panel on
     * the rating page. $partial is the r_type 2 scope.
     *
     * The labels are written out in full - the wording the rating pages
     * themselves use - because they are read by whoever encodes the score and
     * they are quoted back in the validation messages. Nothing keys off them.
     *
     * Skills is deliberately left out of the non-teaching map: it is not
     * retained, so it is neither encoded on the retention screen nor counted
     * when deciding whether an earlier application has a score worth copying.
     */
    public function retention_score_map(int $pType, bool $partial = false): array
    {
        if ($pType === 1) {
            $partialMap = [
                'PBET/LET/LEPT Rating' => 'let_rating',
                'Demonstration Teaching' => 'demo_rating',
                'Teacher Reflection' => 'tr_rating',
            ];

            return $partial ? $partialMap : array_merge(
                ['Education' => 'education', 'Training' => 'training', 'Experience' => 'experience'],
                $partialMap
            );
        }

        $partialMap = ['Interview' => 'interview', 'Written Examination' => 'written'];

        return $partial ? $partialMap : array_merge(
            ['Education' => 'educ',
             'Trainings and Seminars' => 'trainings',
             'Work Experience' => 'experience',
             'Performance Rating' => 'performance',
             'Outstanding Accomplishments' => 'oa',
             'Application of Education' => 'ae',
             'Application of Learning and Development' => 'ald'/*, 'Skills' => 'skills'*/],
            $partialMap
        );
    }

    public function retention_rating_table(int $pType): string
    {
        return $pType === 1 ? 'hris_applications_rating' : 'hris_rating_none';
    }

    /**
     * Whether a set of criterion scores holds anything worth carrying over.
     * 0.00001 is the placeholder the rating forms and copy routines write for
     * "not rated yet", so a row made only of those is empty, not zero-scored.
     */
    private function retention_has_real_score(array $scores): bool
    {
        foreach ($scores as $value) {
            if ((float) $value > 0.001) {
                return true;
            }
        }

        return false;
    }

    /**
     * Point ceiling per rating column for one vacancy, so manual encoding is
     * held to the same maximums the rating form enforces.
     *
     * Non-teaching ceilings come from hris_position_points via
     * hris_positions.bracket, overridden by the position's criteria sheet where
     * one exists. Interview, Written and Skills have no column in either table
     * - the rating form hard-codes 20 for each, so that is what is used here.
     */
    public function retention_max_points(string $jobTitle, int $pType): array
    {
        if ($pType === 1) {
            // The teaching form caps each criterion at 100 and relies on the
            // total; nothing narrower is on file to borrow.
            return ['education' => 100, 'training' => 100, 'experience' => 100,
                    'let_rating' => 100, 'demo_rating' => 100, 'tr_rating' => 100];
        }

        $max = ['educ' => 0, 'trainings' => 0, 'experience' => 0, 'performance' => 0,
                'oa' => 0, 'ae' => 0, 'ald' => 0,
                'interview' => 20, 'written' => 20, 'skills' => 20];

        $position = $this->db
            ->select('id, bracket')
            ->from('hris_positions')
            ->where('title', $jobTitle)
            ->get()
            ->row();

        if (empty($position)) {
            return $max;
        }

        $points = $this->db
            ->from('hris_position_points')
            ->where('id', $position->bracket)
            ->get()
            ->row();

        // hris_position_points names Training "tr" and Experience "exp"; the
        // rating table spells them out.
        $fromBracket = ['educ' => 'educ', 'trainings' => 'tr', 'experience' => 'exp',
                        'performance' => 'per', 'oa' => 'oa', 'ae' => 'ae', 'ald' => 'ald'];

        foreach ($fromBracket as $column => $bracketColumn) {
            if (!empty($points) && isset($points->$bracketColumn)) {
                $max[$column] = (float) $points->$bracketColumn;
            }
        }

        // A position with its own criteria sheet overrides the shared bracket.
        $this->load->model('Position_criteria_model', 'position_criteria');
        $slots = $this->position_criteria->slots((int) $position->id);
        $fromSheet = ['educ' => 'educ', 'trainings' => 'tr', 'experience' => 'exp',
                      'performance' => 'per', 'oa' => 'oa', 'ae' => 'ae', 'ald' => 'ald'];

        foreach ($fromSheet as $column => $slot) {
            if (isset($slots[$slot]['max_points'])) {
                $max[$column] = (float) $slots[$slot]['max_points'];
            }
        }

        return $max;
    }

    /**
     * Retention request counts per assigned vacancy, keyed by jobID, for the
     * dashboard. Pending is the actionable figure; granted and denied are kept
     * so a resolved queue does not read as "no requests were ever made".
     */
    public function retention_counts(int $userId): array
    {
        // The application is authoritative for the vacancy being applied for.
        // Historical request rows can carry the vacancy an applicant came
        // from; grouping on rr.job_id would put those requests in that earlier
        // vacancy instead of the vacancy receiving the application.
        $rows = $this->db
            ->select("sv.job_id,
                COUNT(rr.id) AS total,
                SUM(CASE WHEN rr.stat = 0 THEN 1 ELSE 0 END) AS pending,
                SUM(CASE WHEN rr.stat = 1 THEN 1 ELSE 0 END) AS granted,
                SUM(CASE WHEN rr.stat = 2 THEN 1 ELSE 0 END) AS denied", false)
            ->from('hris_secretariat_vacancies sv')
            ->join('hris_jobvacancy j', 'j.jobID = sv.job_id')
            ->join('hris_applications a', 'a.jobID = sv.job_id', 'left')
            ->join('hris_rating_request rr', 'rr.app_id = a.appID', 'left')
            ->where('sv.secretariat_user_id', $userId)
            ->where('j.jvStatus !=', 'Closed')
            ->group_by('sv.job_id')
            ->get()
            ->result();

        $counts = [];

        foreach ($rows as $row) {
            $counts[(int) $row->job_id] = [
                'total' => (int) $row->total,
                'pending' => (int) $row->pending,
                'granted' => (int) $row->granted,
                'denied' => (int) $row->denied,
            ];
        }

        return $counts;
    }

    /**
     * Applicants who asked to retain points on one assigned vacancy.
     *
     * source_count is how many of the applicant's earlier applications actually
     * carry a rating row that could be copied. It is 0 for most requests, which
     * is why manual encoding exists: without it those requests cannot be
     * resolved at all.
     */
    public function retention_requests(int $userId, int $jobId): array
    {
        if (!$this->secretariat_has_vacancy($userId, $jobId)) {
            return [];
        }

        $vacancy = $this->db
            ->select('jobID, jobTitle, position, job_type, sy, itemNo, jvStatus')
            ->from('hris_jobvacancy')
            ->where('jobID', $jobId)
            ->get()
            ->row();

        if (empty($vacancy)) {
            return [];
        }

        $rows = $this->db
            ->select("rr.id AS request_id, rr.app_id, rr.applicant_id, rr.r_type, rr.stat,
                rr.granted_scope, rr.deny_reason, rr.rdate, rr.adate, rr.fy, rr.p_type, rr.res,
                a.appID, a.appStatus, a.dq, a.empEmail, a.pre_school, a.district, a.app_year,
                dq.reason AS dq_reason,
                COALESCE(ha.record_no, hs.IDNumber, rr.applicant_id) AS record_no,
                COALESCE(ha.id, hs.IDNumber, rr.applicant_id) AS profile_id,
                COALESCE(ha.FirstName, hs.FirstName, '') AS FirstName,
                COALESCE(ha.MiddleName, hs.MiddleName, '') AS MiddleName,
                COALESCE(ha.LastName, hs.LastName, '') AS LastName,
                COALESCE(ha.NameExtn, hs.NameExtn, '') AS NameExtn,
                CASE
                    WHEN ha.id IS NOT NULL THEN 'ma'
                    WHEN hs.IDNumber IS NOT NULL THEN 'ma_staff'
                    ELSE ''
                END AS profile_route,
                CONCAT_WS(' ', NULLIF(TRIM(u.fname), ''), NULLIF(TRIM(u.lname), '')) AS resolved_by,
                ra.rater_user_id, ra.assigned_at,
                CONCAT_WS(' ', NULLIF(TRIM(ev.fname), ''), NULLIF(TRIM(ev.mname), ''), NULLIF(TRIM(ev.lname), '')) AS evaluator_name", false)
            ->from('hris_rating_request rr')
            // Inner join on purpose: a request pointing at an application that
            // is no longer in the system cannot be resolved either way, so it
            // is left out of the list entirely rather than shown as a dead end.
            ->join('hris_applications a', 'a.appID = rr.app_id')
            // An application can have several qualification-review rows over
            // time. Join only its latest one so the request is not duplicated
            // and the displayed disqualification reason is the current one.
            ->join(
                '(SELECT appID, MAX(id) AS latest_id FROM hris_app_dq GROUP BY appID) dq_latest',
                'dq_latest.appID = a.appID',
                'left',
                false
            )
            ->join('hris_app_dq dq', 'dq.id = dq_latest.latest_id', 'left')
            ->join('hris_applicant ha', 'ha.id = rr.applicant_id', 'left')
            ->join('hris_staff hs', 'ha.id IS NULL AND CONVERT(hs.IDNumber USING utf8mb4) COLLATE utf8mb4_general_ci = rr.applicant_id', 'left', false)
            ->join('users u', 'u.id = rr.res', 'left')
            // An application can be reassigned, so join only its latest
            // evaluator tag - otherwise a reassigned applicant is listed once
            // per evaluator they were ever given to.
            ->join(
                '(SELECT app_id, MAX(id) AS assignment_id FROM hris_rater_assignments GROUP BY app_id) latest_ra',
                'latest_ra.app_id = a.appID',
                'left',
                false
            )
            ->join('hris_rater_assignments ra', 'ra.id = latest_ra.assignment_id', 'left')
            ->join('users ev', 'ev.id = ra.rater_user_id', 'left')
            // A request belongs to the vacancy of the exact application it
            // references. Do not use rr.job_id here: old rows may contain the
            // vacancy whose ratings are being retained.
            ->where('a.jobID', $jobId)
            ->order_by('rr.stat', 'asc')
            ->order_by('ha.LastName', 'asc')
            ->order_by('rr.id', 'asc')
            ->get()
            ->result();

        $sourcesByApplicant = $this->retention_sources_bulk($rows, (int) $vacancy->jobID, (int) $vacancy->position);

        foreach ($rows as $row) {
            $pType = (int) ($row->p_type ?: $vacancy->position);
            $partial = ((int) $row->r_type === 2);

            $row->p_type_resolved = $pType;
            $row->vacancy = $vacancy;
            $row->score_map = $this->retention_score_map($pType, $partial);

            $sources = [];

            foreach ($sourcesByApplicant[(string) $row->applicant_id] ?? [] as $source) {
                // The application being resolved is never its own source.
                if ((int) $source['app_id'] !== (int) $row->app_id) {
                    $sources[] = $source;
                }
            }

            $row->sources = $sources;
            $row->source_count = count($sources);
        }

        return $rows;
    }

    /**
     * Every denied retention request across the vacancies assigned to one
     * Secretariat account, newest decision first.
     *
     * Scoped through hris_secretariat_vacancies rather than the request rows so
     * the list can never reach a vacancy the account was not given. Pass a
     * jobId to narrow it to one vacancy; 0 returns every assigned vacancy.
     *
     * res carries the user who denied the request, which is what the "my denied
     * only" filter reads. Rows are returned unfiltered on that column so the
     * caller can show both counts without a second query.
     */
    public function retention_denied(int $userId, int $jobId = 0): array
    {
        if ($jobId > 0 && !$this->secretariat_has_vacancy($userId, $jobId)) {
            return [];
        }

        $this->db
            ->select("rr.id AS request_id, rr.app_id, rr.applicant_id, rr.r_type, rr.stat,
                rr.deny_reason, rr.rdate, rr.adate, rr.fy, rr.p_type, rr.res,
                a.appID, a.appStatus, a.dq, a.empEmail, a.pre_school, a.district, a.app_year,
                j.jobID, j.jobTitle, j.position, j.job_type, j.sy, j.itemNo,
                dq.reason AS dq_reason,
                COALESCE(ha.record_no, hs.IDNumber, rr.applicant_id) AS record_no,
                COALESCE(ha.id, hs.IDNumber, rr.applicant_id) AS profile_id,
                COALESCE(ha.FirstName, hs.FirstName, '') AS FirstName,
                COALESCE(ha.MiddleName, hs.MiddleName, '') AS MiddleName,
                COALESCE(ha.LastName, hs.LastName, '') AS LastName,
                COALESCE(ha.NameExtn, hs.NameExtn, '') AS NameExtn,
                CASE
                    WHEN ha.id IS NOT NULL THEN 'ma'
                    WHEN hs.IDNumber IS NOT NULL THEN 'ma_staff'
                    ELSE ''
                END AS profile_route,
                CONCAT_WS(' ', NULLIF(TRIM(u.fname), ''), NULLIF(TRIM(u.lname), '')) AS resolved_by,
                ra.rater_user_id,
                CONCAT_WS(' ', NULLIF(TRIM(ev.fname), ''), NULLIF(TRIM(ev.mname), ''), NULLIF(TRIM(ev.lname), '')) AS evaluator_name", false)
            ->from('hris_secretariat_vacancies sv')
            ->join('hris_jobvacancy j', 'j.jobID = sv.job_id')
            ->join('hris_applications a', 'a.jobID = j.jobID')
            ->join('hris_rating_request rr', 'rr.app_id = a.appID')
            ->join(
                '(SELECT appID, MAX(id) AS latest_id FROM hris_app_dq GROUP BY appID) dq_latest',
                'dq_latest.appID = a.appID',
                'left',
                false
            )
            ->join('hris_app_dq dq', 'dq.id = dq_latest.latest_id', 'left')
            ->join('hris_applicant ha', 'ha.id = rr.applicant_id', 'left')
            ->join('hris_staff hs', 'ha.id IS NULL AND CONVERT(hs.IDNumber USING utf8mb4) COLLATE utf8mb4_general_ci = rr.applicant_id', 'left', false)
            ->join('users u', 'u.id = rr.res', 'left')
            ->join(
                '(SELECT app_id, MAX(id) AS assignment_id FROM hris_rater_assignments GROUP BY app_id) latest_ra',
                'latest_ra.app_id = a.appID',
                'left',
                false
            )
            ->join('hris_rater_assignments ra', 'ra.id = latest_ra.assignment_id', 'left')
            ->join('users ev', 'ev.id = ra.rater_user_id', 'left')
            ->where('sv.secretariat_user_id', $userId)
            ->where('j.jvStatus !=', 'Closed')
            ->where('rr.stat', 2);

        if ($jobId > 0) {
            $this->db->where('j.jobID', $jobId);
        }

        $rows = $this->db
            ->order_by('rr.adate', 'desc')
            ->order_by('rr.id', 'desc')
            ->get()
            ->result();

        foreach ($rows as $row) {
            $row->p_type_resolved = (int) ($row->p_type ?: $row->position);
        }

        return $rows;
    }

    /**
     * Copyable source applications for a whole list of requests at once, keyed
     * by applicant_id.
     *
     * Doing this per request cost one applications query plus one rating lookup
     * per candidate application - several hundred queries on a vacancy with a
     * busy retention queue. Three queries serve the whole page instead.
     */
    private function retention_sources_bulk(array $rows, int $jobId, int $vacancyPosition): array
    {
        $applicantIds = [];
        $recordNos = [];
        $pType = 0;

        foreach ($rows as $row) {
            $applicantId = trim((string) $row->applicant_id);

            if ($applicantId !== '') {
                $applicantIds[$applicantId] = true;
                $recordNos[$applicantId] = trim((string) ($row->record_no ?: $applicantId));
            }

            if ($pType === 0) {
                $pType = (int) ($row->p_type ?: $vacancyPosition);
            }
        }

        if (empty($applicantIds)) {
            return [];
        }

        $isTeaching = ($pType === 1);

        $this->db
            ->select('a.appID, a.applicant_id, a.jobID, a.appStatus, a.dateSubmitted,
                jv.jobTitle, jv.sy, jv.datePosted, jv.itemNo')
            ->from('hris_applications a')
            ->join('hris_jobvacancy jv', 'jv.jobID = a.jobID', 'left')
            ->where_in('a.applicant_id', array_keys($applicantIds))
            ->where('a.jobID !=', $jobId);

        if (!$isTeaching) {
            // Non-teaching scores live in hris_rating_none, so a teaching
            // vacancy can never be a source for them.
            $this->db->where('jv.position !=', 1);
        }

        $candidates = $this->db
            ->order_by('jv.datePosted', 'desc')
            ->order_by('a.appID', 'desc')
            ->get()
            ->result();

        if (empty($candidates)) {
            return [];
        }

        $appIds = [];
        foreach ($candidates as $candidate) {
            $appIds[] = (int) $candidate->appID;
        }

        $ratings = [];
        foreach ($this->db->from($this->retention_rating_table($pType))->where_in('appID', $appIds)->get()->result() as $rating) {
            // The copy routines match on record_no AND appID, so a rating filed
            // under a different record_no would copy nothing.
            $ratings[(int) $rating->appID . '|' . trim((string) $rating->record_no)] = $rating;
        }

        $partialByApplicant = [];
        foreach ($rows as $row) {
            $partialByApplicant[(string) $row->applicant_id] = ((int) $row->r_type === 2);
        }

        $out = [];

        foreach ($candidates as $candidate) {
            $applicantId = (string) $candidate->applicant_id;
            $recordNo = $recordNos[$applicantId] ?? '';
            $rating = $ratings[(int) $candidate->appID . '|' . $recordNo] ?? null;

            if (empty($rating)) {
                continue;
            }

            $scores = [];
            foreach ($this->retention_score_map($pType, $partialByApplicant[$applicantId] ?? false) as $label => $column) {
                $scores[$label] = $rating->$column ?? null;
            }

            // A rating row whose criteria are all still the 0.00001 "not rated
            // yet" placeholder is not a usable source: copying it would mark
            // this application Rated on a total of zero. Those applicants need
            // manual encoding, so the row is not offered at all.
            if (!$this->retention_has_real_score($scores)) {
                continue;
            }

            $out[$applicantId][] = [
                'app_id' => (int) $candidate->appID,
                'job_id' => (int) $candidate->jobID,
                'title' => trim((string) ($candidate->jobTitle ?? '')),
                'item_no' => trim((string) ($candidate->itemNo ?? '')),
                'sy' => trim((string) ($candidate->sy ?? '')),
                'date_applied' => trim((string) ($candidate->dateSubmitted ?? '')),
                'app_status' => trim((string) ($candidate->appStatus ?? '')),
                'total_points' => $rating->total_points ?? null,
                'scores' => $scores,
            ];
        }

        return $out;
    }

    /**
     * Earlier applications of the same applicant whose scores could be copied.
     *
     * Mirrors Pages::retention_source_applications(): the application being
     * resolved and any other application to the same vacancy are never sources,
     * a non-teaching request can only draw on a non-teaching vacancy, and a row
     * only counts when a rating row exists under the same record_no + appID the
     * copy routines match on.
     */
    public function retention_sources(object $request, int $pType, int $jobId): array
    {
        $isTeaching = ($pType === 1);
        $ratingTable = $this->retention_rating_table($pType);
        $partial = ((int) ($request->r_type ?? 0) === 2);
        $recordNo = $this->retention_record_no($request);

        $this->db
            ->select('a.appID, a.jobID, a.appStatus, a.dateSubmitted, a.app_year,
                jv.jobTitle, jv.job_type, jv.sy, jv.datePosted, jv.jvStatus, jv.itemNo')
            ->from('hris_applications a')
            ->join('hris_jobvacancy jv', 'jv.jobID = a.jobID', 'left')
            ->where('a.applicant_id', $request->applicant_id)
            ->where('a.appID !=', (int) $request->app_id)
            ->where('a.jobID !=', $jobId);

        if (!$isTeaching) {
            $this->db->where('jv.position !=', 1);
        }

        $rows = $this->db
            ->order_by('jv.datePosted', 'desc')
            ->order_by('a.appID', 'desc')
            ->get()
            ->result();

        $scoreMap = $this->retention_score_map($pType, $partial);
        $sources = [];

        foreach ($rows as $row) {
            $rating = $this->db
                ->from($ratingTable)
                ->where('record_no', $recordNo)
                ->where('appID', (int) $row->appID)
                ->get()
                ->row();

            if (empty($rating)) {
                continue;
            }

            $scores = [];

            foreach ($scoreMap as $label => $column) {
                $scores[$label] = $rating->$column ?? null;
            }

            if (!$this->retention_has_real_score($scores)) {
                continue;
            }

            $sources[] = [
                'app_id' => (int) $row->appID,
                'job_id' => (int) $row->jobID,
                'title' => trim((string) ($row->jobTitle ?? '')),
                'item_no' => trim((string) ($row->itemNo ?? '')),
                'sy' => trim((string) ($row->sy ?? '')),
                'date_applied' => trim((string) ($row->dateSubmitted ?? '')),
                'app_status' => trim((string) ($row->appStatus ?? '')),
                'total_points' => $rating->total_points ?? null,
                'scores' => $scores,
            ];
        }

        return $sources;
    }

    /**
     * One pending request the Secretariat is allowed to act on, or null. Guards
     * both the vacancy assignment and the request state, so a double submit
     * cannot resolve the same request twice.
     */
    public function retention_actionable_request(int $userId, int $requestId): ?object
    {
        $request = $this->db
            ->select('rr.*, a.jobID AS applied_job_id,
                j.jobID, j.jobTitle, j.position, j.job_type, j.sy, j.jvStatus,
                a.appID, a.appStatus, a.app_year, a.empEmail, a.applicant_id AS app_applicant_id')
            ->from('hris_rating_request rr')
            ->join('hris_applications a', 'a.appID = rr.app_id')
            ->join('hris_jobvacancy j', 'j.jobID = a.jobID')
            ->where('rr.id', $requestId)
            ->get()
            ->row();

        if (empty($request) || !$this->secretariat_has_vacancy($userId, (int) $request->applied_job_id)) {
            return null;
        }

        if ((int) $request->stat !== 0 || strcasecmp(trim((string) $request->jvStatus), 'Closed') === 0) {
            return null;
        }

        // All decision handlers use job_id for redirects, source filtering,
        // audit entries, and downstream status changes. Normalize it to the
        // vacancy actually applied for rather than propagating stale request
        // metadata.
        $request->job_id = (int) $request->applied_job_id;

        return $request;
    }

    /**
     * record_no the rating row must be keyed on. The copy routines and the
     * rating views look a rating up by record_no + appID, so a manual row that
     * used anything else would be invisible to them.
     */
    public function retention_record_no(object $request): string
    {
        // Already resolved by retention_requests(); everything else has to look
        // it up, and both must agree or a copy silently finds no source.
        if (!empty($request->record_no)) {
            return trim((string) $request->record_no);
        }

        $applicant = $this->db
            ->select('record_no')
            ->from('hris_applicant')
            ->where('id', $request->applicant_id)
            ->get()
            ->row();

        if (!empty($applicant->record_no)) {
            return trim((string) $applicant->record_no);
        }

        // Non-teaching rating rows for staff applicants are keyed on the
        // employee id, which is what empEmail carries on those applications.
        return trim((string) ($request->empEmail ?: $request->applicant_id));
    }

    /**
     * Write a manually encoded retained score.
     *
     * Mirrors Hiring_model::copy_rating() / Reg::copy_rating(): the same
     * columns, eval ids left at 0 so the scores stay visible and editable to
     * whoever holds the application, total_points summed the same way, and an
     * upsert on appID. Criteria outside the retention scope keep the 0.00001
     * "not rated yet" placeholder so a partial grant still leaves work for the
     * evaluator.
     */
    public function save_manual_retention(object $request, int $pType, array $scores, string $recordNo): bool
    {
        $table = $this->retention_rating_table($pType);
        $partial = ((int) $request->r_type === 2);
        $scoreMap = $this->retention_score_map($pType, $partial);
        $allColumns = array_values($this->retention_score_map($pType, false));

        $data = ['record_no' => $recordNo, 'appID' => (int) $request->appID];

        foreach ($allColumns as $column) {
            $data[$column] = 0.00001;
        }

        foreach ($scoreMap as $label => $column) {
            if (isset($scores[$column])) {
                $data[$column] = (float) $scores[$column];
            }
        }

        // Unclaimed, exactly as a copied retention arrives.
        $data['eval_id1'] = 0;
        $data['eval_id2'] = 0;
        $data['eval_id3'] = 0;
        $data['job_type'] = (int) $request->job_type;
        $data['fy'] = $request->app_year ?: $request->fy ?: date('Y');

        // The placeholder must not inflate the total.
        $total = 0.0;
        foreach ($allColumns as $column) {
            $value = (float) $data[$column];
            if ($value > 0.001) {
                $total += $value;
            }
        }
        $data['total_points'] = $total;

        $existing = $this->db
            ->from($table)
            ->where('appID', (int) $request->appID)
            ->get()
            ->row();

        if (!empty($existing)) {
            unset($data['fy']);
            return (bool) $this->db->where('appID', (int) $request->appID)->update($table, $data);
        }

        // Skills is out of the retention map, so it is never encoded here - but
        // the non-teaching rating row still carries the column, so a new row
        // gets the "not rated yet" placeholder rather than leaving it unset.
        // It stays out of total_points either way, being below the threshold.
        if ($pType !== 1) {
            $data['skills'] = 0.00001;
        }

        return (bool) $this->db->insert($table, $data);
    }

    /**
     * All users whose position is Evaluator, including their current load so
     * Secretariats can distribute applicants without leaving the table.
     */
    public function eligible_evaluators(?int $fy = null): array
    {
        $fy = $fy ?: (int) date('Y');

        return $this->db
            ->select("u.id, u.fname, u.mname, u.lname, u.username,
                COUNT(DISTINCT ra.app_id) AS assigned_total", false)
            ->from('users u')
            ->join('hris_rater_assignments ra', 'ra.rater_user_id = u.id AND ra.fy = ' . $this->db->escape($fy), 'left', false)
            ->where('u.position', 'Evaluator')
            ->group_by(['u.id', 'u.fname', 'u.mname', 'u.lname', 'u.username'])
            ->order_by('u.lname', 'asc')
            ->order_by('u.fname', 'asc')
            ->get()
            ->result();
    }

    /**
     * Assign or reassign one eligible application to an evaluator. This method
     * deliberately does not change appStatus, dq, or create a rating row.
     */
    public function tag_applicant_to_evaluator(int $userId, int $appId, int $jobId, int $raterId, ?int $assignedBy): array
    {
        $application = $this->db
            ->select('a.appID, a.applicant_id, a.app_year, a.jobID, j.job_type')
            ->from('hris_applications a')
            ->join('hris_jobvacancy j', 'j.jobID = a.jobID')
            ->join('hris_secretariat_vacancies sv', 'sv.job_id = j.jobID')
            ->where('sv.secretariat_user_id', $userId)
            ->where('a.appID', $appId)
            ->where('a.jobID', $jobId)
            ->where('j.jvStatus !=', 'Closed')
            ->where_in('a.appStatus', ['Application Submitted', 'Validated'])
            ->where('a.dq !=', 2)
            ->get()
            ->row();

        if (!$application) {
            return ['ok' => false, 'message' => 'This applicant is not available in your assigned vacancy.'];
        }

        $evaluator = $this->db
            ->select('id, fname, mname, lname')
            ->from('users')
            ->where('id', $raterId)
            ->where('position', 'Evaluator')
            ->get()
            ->row();

        if (!$evaluator) {
            return ['ok' => false, 'message' => 'Please select an eligible evaluator.'];
        }

        $evaluatorName = trim(implode(' ', array_filter([
            trim((string) ($evaluator->fname ?? '')),
            trim((string) ($evaluator->mname ?? '')),
            trim((string) ($evaluator->lname ?? '')),
        ], static function ($value) {
            return trim((string) $value) !== '';
        })));

        $existing = $this->db
            ->from('hris_rater_assignments')
            ->where('app_id', $application->appID)
            ->order_by('id', 'desc')
            ->get()
            ->row();

        if ($existing && (int) $existing->rater_user_id === $raterId) {
            return [
                'ok' => true,
                'changed' => false,
                'message' => $evaluatorName . ' is already assigned to this applicant.',
                'evaluator_name' => $evaluatorName,
                'rater_id' => $raterId,
                'assigned_at' => $existing->assigned_at,
            ];
        }

        $specialization = $this->db
            ->select('specialization')
            ->from('hris_applicant')
            ->where('id', $application->applicant_id)
            ->get()
            ->row();

        $fy = (int) $application->app_year;
        if ($fy <= 0) {
            $fy = (int) date('Y');
        }

        $now = date('Y-m-d H:i:s');
        $this->db->trans_start();

        if ($existing) {
            $this->db
                ->where('id', $existing->id)
                ->update('hris_rater_assignments', [
                    'rater_user_id' => $raterId,
                    'assigned_by' => $assignedBy,
                    'assigned_at' => $now,
                ]);
            $action = 'reassigned';
        } else {
            $this->db->insert('hris_rater_assignments', [
                'fy' => $fy,
                'applicant_id' => (string) $application->applicant_id,
                'app_id' => (int) $application->appID,
                'job_id' => (int) $application->jobID,
                'job_type' => (int) $application->job_type,
                'specialization' => $specialization->specialization ?? '',
                'rater_user_id' => $raterId,
                'assigned_by' => $assignedBy,
                'assigned_at' => $now,
            ]);
            $action = 'assigned';
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            return ['ok' => false, 'message' => 'The evaluator tag could not be saved. Please try again.'];
        }

        return [
            'ok' => true,
            'changed' => true,
            'message' => 'Applicant ' . $action . ' to ' . $evaluatorName . '.',
            'evaluator_name' => $evaluatorName,
            'rater_id' => $raterId,
            'assigned_at' => $now,
        ];
    }

    /**
     * Save per-vacancy coverage for a secretariat. $jobIds is an array of open
     * hris_jobvacancy.jobID values.
     */
    public function save_assignments(int $userId, array $jobIds, ?int $createdBy = null): void
    {
        if (!is_array($jobIds)) {
            $jobIds = [];
        }

        // Only link to real, open vacancies.
        $jobIds = array_values(array_filter(array_map('intval', $jobIds)));

        $rows = [];
        if (!empty($jobIds)) {
            $open = $this->db
                ->select('jobID')
                ->from('hris_jobvacancy')
                ->where('jvStatus !=', 'Closed')
                ->where_in('jobID', $jobIds)
                ->get()
                ->result();

            foreach ($open as $row) {
                $rows[] = [
                    'secretariat_user_id' => $userId,
                    'job_id'              => (int) $row->jobID,
                    'created_by'          => $createdBy,
                ];
            }
        }

        $this->db->trans_start();
        $this->db->where('secretariat_user_id', $userId)->delete('hris_secretariat_vacancies');

        // Also wipe legacy group/type assignments for this user so the switch is
        // clean; any vacancies not in the new table will no longer show.
        $this->db->where('secretariat_user_id', $userId)->delete('hris_secretariat_levels');

        foreach ($rows as $row) {
            $this->db->insert('hris_secretariat_vacancies', $row);
        }
        $this->db->trans_complete();
    }

    /**
     * Remove all secretariat coverage tied to a single job vacancy. Called when
     * a vacancy is archived (closed) so assignments do not linger.
     */
    public function remove_vacancy_assignments(int $jobId): void
    {
        $this->db
            ->where('job_id', $jobId)
            ->delete('hris_secretariat_vacancies');

        // Field Encoder tags hang off the same vacancy; drop them with it.
        $this->db
            ->where('job_id', $jobId)
            ->delete('hris_field_encoder_access');

        // Field Evaluator tags are scoped the same way.
        $this->db
            ->where('job_id', $jobId)
            ->delete('hris_field_evaluator_access');
    }

    public function count_by_status(array $jobTypes, string $status): int
    {
        if (empty($jobTypes)) {
            return 0;
        }

        $this->db
            ->from('hris_applications a')
            ->join('hris_jobvacancy jv', 'jv.jobID = a.jobID', 'left')
            ->where('a.appStatus', $status)
            ->where('jv.jvStatus', 'Open')
            ->where('a.dq !=', 2);
        $this->apply_scopes($jobTypes);

        return (int) $this->db->count_all_results();
    }

    public function count_endorsed_without_rater(array $jobTypes, int $fy): int
    {
        if (empty($jobTypes)) {
            return 0;
        }

        $this->db
            ->from('hris_applications app')
            ->join('hris_jobvacancy jv', 'jv.jobID = app.jobID', 'left')
            ->join('hris_rater_assignments ra', 'ra.app_id = app.appID AND ra.fy = ' . $this->db->escape($fy), 'left', false)
            ->where('app.appStatus', 'Endorsed for Rating')
            ->where('app.app_year', $fy)
            ->where('jv.jvStatus', 'Open')
            ->where('ra.id IS NULL', null, false)
            ->where('app.dq !=', 2);
        $this->apply_scopes($jobTypes);

        return (int) $this->db->count_all_results();
    }

    public function count_dq_applicants(array $jobTypes, int $fy): int
    {
        if (empty($jobTypes)) {
            return 0;
        }

        $this->db
            ->from('hris_applications app')
            ->join('hris_jobvacancy jv', 'jv.jobID = app.jobID', 'left')
            ->where('app.app_year', $fy)
            ->where('jv.jvStatus', 'Open')
            ->where('app.dq', 2);
        $this->apply_scopes($jobTypes);

        return (int) $this->db->count_all_results();
    }

    public function endorsed_applicants(array $jobTypes, int $fy): array
    {
        if (empty($jobTypes)) {
            return [];
        }

        $this->db
            ->select('app.appID, app.applicant_id, app.jobID, app.pre_school, app.appStatus, app.district, app.app_year,
                      jv.jobTitle, jv.job_type,
                      COALESCE(ha.record_no, ha2.record_no) AS record_no,
                      COALESCE(ha.FirstName, ha2.FirstName) AS FirstName,
                      COALESCE(ha.LastName, ha2.LastName) AS LastName,
                      COALESCE(ha.MiddleName, ha2.MiddleName) AS MiddleName,
                      app.dateSubmitted')
            ->from('hris_applications app')
            ->join('hris_jobvacancy jv', 'jv.jobID = app.jobID', 'left')
            ->join('hris_applicant ha', 'ha.id = app.applicant_id', 'left')
            ->join('hris_applicant ha2', 'ha2.record_no = app.applicant_id', 'left')
            ->where('app.appStatus', 'Endorsed for Rating')
            ->where('app.app_year', $fy);
        $this->apply_scopes($jobTypes);

        return $this->db
            ->order_by('jv.job_type', 'asc')
            ->order_by('app.dateSubmitted', 'desc')
            ->get()
            ->result();
    }

    public function demo_trf_scored(array $jobTypes, int $fy): array
    {
        if (empty($jobTypes)) {
            return [];
        }

        $this->db
            ->select('app.appID, app.applicant_id, app.jobID, app.pre_school, app.appStatus, app.district, app.app_year,
                      jv.jobTitle, jv.job_type, jv.jvStatus,
                      COALESCE(ha.record_no, ha2.record_no) AS record_no,
                      COALESCE(ha.FirstName, ha2.FirstName) AS FirstName,
                      COALESCE(ha.LastName, ha2.LastName) AS LastName,
                      COALESCE(ha.MiddleName, ha2.MiddleName) AS MiddleName,
                      rar.demo_rating, rar.tr_rating')
            ->from('hris_applications app')
            ->join('hris_jobvacancy jv', 'jv.jobID = app.jobID', 'left')
            ->join('hris_applications_rating rar', 'rar.appID = app.appID', 'left')
            ->join('hris_applicant ha', 'ha.id = app.applicant_id', 'left')
            ->join('hris_applicant ha2', 'ha2.record_no = app.applicant_id', 'left')
            ->where('app.app_year', $fy)
            ->where('jv.jvStatus', 'Open')
            ->where('rar.demo_rating !=', 0.00001)
            ->where('rar.tr_rating !=', 0.00001);
        $this->apply_scopes($jobTypes);

        return $this->db
            ->order_by('jv.jvStatus', 'asc')
            ->order_by('app.appID', 'desc')
            ->get()
            ->result();
    }

    public function demo_trf_unscored(array $jobTypes, int $fy): array
    {
        if (empty($jobTypes)) {
            return [];
        }

        $this->db
            ->select('app.appID, app.applicant_id, app.jobID, app.pre_school, app.appStatus, app.district, app.app_year,
                      jv.jobTitle, jv.job_type, jv.jvStatus,
                      COALESCE(ha.record_no, ha2.record_no) AS record_no,
                      COALESCE(ha.FirstName, ha2.FirstName) AS FirstName,
                      COALESCE(ha.LastName, ha2.LastName) AS LastName,
                      COALESCE(ha.MiddleName, ha2.MiddleName) AS MiddleName,
                      rar.demo_rating, rar.tr_rating')
            ->from('hris_applications app')
            ->join('hris_jobvacancy jv', 'jv.jobID = app.jobID', 'left')
            ->join('hris_applications_rating rar', 'rar.appID = app.appID', 'left')
            ->join('hris_applicant ha', 'ha.id = app.applicant_id', 'left')
            ->join('hris_applicant ha2', 'ha2.record_no = app.applicant_id', 'left')
            ->where('app.appStatus', 'Endorsed for Rating')
            ->where('app.app_year', $fy)
            ->where('jv.jvStatus', 'Open')
            ->group_start()
                ->where('rar.demo_rating', 0.00001)
                ->or_where('rar.tr_rating', 0.00001)
                ->or_where('rar.appID IS NULL', null, false)
            ->group_end();
        $this->apply_scopes($jobTypes);

        return $this->db
            ->order_by('jv.jvStatus', 'asc')
            ->order_by('app.appID', 'desc')
            ->get()
            ->result();
    }

    public function scores_report_job_options(array $jobTypes): array
    {
        // Return all open vacancies regardless of assigned job types
        // The jobTypes parameter is kept for compatibility but not used for filtering
        return $this->db
            ->select('jobID, jobTitle, job_type, sy')
            ->from('hris_jobvacancy')
            ->where('jvStatus', 'Open')
            ->where('jvStatus !=', 'Closed')
            ->where('job_type !=', 0)
            ->order_by('job_type', 'asc')
            ->order_by('jobTitle', 'asc')
            ->get()
            ->result();
    }

    public function scores_report_applicants(array $jobTypes, int $fy, ?int $jobTypeFilter = null, ?int $jobId = null, ?string $districtFilter = null, ?int $yearFilter = null): array
    {
        // The scores report intentionally bypasses Secretariat level assignments.
        // $jobTypes is retained for compatibility with existing callers.
        if (empty($jobTypes) && empty($jobId) && empty($jobTypeFilter) && empty($districtFilter) && empty($yearFilter)) {
            return [];
        }

        $ratingSql = "
            SELECT
                appID,
                MAX(CASE WHEN education IS NULL OR education IN (0.00001, 0.0001) THEN NULL ELSE education END) AS education,
                MAX(CASE WHEN training IS NULL OR training IN (0.00001, 0.0001) THEN NULL ELSE training END) AS training,
                MAX(CASE WHEN experience IS NULL OR experience IN (0.00001, 0.0001) THEN NULL ELSE experience END) AS experience,
                MAX(CASE WHEN let_rating IS NULL OR let_rating IN (0.00001, 0.0001) THEN NULL ELSE let_rating END) AS let_rating,
                MAX(CASE WHEN demo_rating IS NULL OR demo_rating IN (0.00001, 0.0001) THEN NULL ELSE demo_rating END) AS demo_rating,
                MAX(CASE WHEN tr_rating IS NULL OR tr_rating IN (0.00001, 0.0001) THEN NULL ELSE tr_rating END) AS tr_rating
            FROM hris_applications_rating
            GROUP BY appID
        ";

        $this->db
            ->select('app.appID, app.applicant_id, app.jobID, app.pre_school, app.appStatus, app.district, app.app_year,
                      jv.jobTitle, jv.job_type,
                      COALESCE(ha.record_no, ha2.record_no, app.applicant_id) AS record_no,
                      COALESCE(ha.FirstName, ha2.FirstName) AS FirstName,
                      COALESCE(ha.LastName, ha2.LastName) AS LastName,
                      COALESCE(ha.MiddleName, ha2.MiddleName) AS MiddleName,
                      sr.education, sr.training, sr.experience, sr.let_rating, sr.demo_rating, sr.tr_rating')
            ->from('hris_applications app')
            ->join('hris_jobvacancy jv', 'jv.jobID = app.jobID', 'left')
            ->join('(' . $ratingSql . ') sr', 'sr.appID = app.appID', 'left', false)
            ->join('hris_applicant ha', 'ha.id = app.applicant_id', 'left')
            ->join('hris_applicant ha2', 'ha2.record_no = app.applicant_id', 'left')
            ->where('jv.jvStatus', 'Open')
            ->where('app.dq !=', 2);

        // Apply year filter if specified, otherwise use current fiscal year
        if (!empty($yearFilter)) {
            $this->db->where('app.app_year', $yearFilter);
        } else {
            $this->db->where('app.app_year', $fy);
        }

        // Apply job filter
        if (!empty($jobId)) {
            $this->db->where('app.jobID', $jobId);
        } elseif (!empty($jobTypeFilter)) {
            $this->db->where('jv.job_type', $jobTypeFilter);
        }

        // Apply district filter if specified
        if (!empty($districtFilter)) {
            $this->db->where('app.district', $districtFilter);
        }

        return $this->db
            ->order_by('jv.job_type', 'asc')
            ->order_by('jv.jobTitle', 'asc')
            ->order_by('ha.LastName', 'asc')
            ->order_by('ha.FirstName', 'asc')
            ->get()
            ->result();
    }

    public function applicant_evaluation_report(int $fy, ?int $jobTypeFilter = null, ?int $jobId = null, ?int $yearFilter = null): array
    {
        if (empty($jobId) && empty($jobTypeFilter)) {
            return [];
        }

        $ratingSql = "
            SELECT
                appID,
                MAX(CASE WHEN education IS NULL OR education IN (0.00001, 0.0001) THEN NULL ELSE education END) AS education,
                MAX(CASE WHEN training IS NULL OR training IN (0.00001, 0.0001) THEN NULL ELSE training END) AS training,
                MAX(CASE WHEN experience IS NULL OR experience IN (0.00001, 0.0001) THEN NULL ELSE experience END) AS experience,
                MAX(CASE WHEN let_rating IS NULL OR let_rating IN (0.00001, 0.0001) THEN NULL ELSE let_rating END) AS let_rating,
                MAX(CASE WHEN demo_rating IS NULL OR demo_rating IN (0.00001, 0.0001) THEN NULL ELSE demo_rating END) AS demo_rating,
                MAX(CASE WHEN tr_rating IS NULL OR tr_rating IN (0.00001, 0.0001) THEN NULL ELSE tr_rating END) AS tr_rating,
                MAX(CASE WHEN total_points IS NULL OR total_points IN (0.00001, 0.0001) THEN NULL ELSE total_points END) AS total_points
            FROM hris_applications_rating
            GROUP BY appID
        ";

        $this->db
            ->select("
                app.appID,
                app.applicant_id,
                app.jobID,
                app.appStatus,
                app.app_year,
                jv.jobTitle,
                jv.job_type,
                COALESCE(NULLIF(ha.record_no, ''), NULLIF(ha2.record_no, ''), app.applicant_id) AS record_no,
                COALESCE(NULLIF(ha.FirstName, ''), NULLIF(ha2.FirstName, '')) AS FirstName,
                COALESCE(NULLIF(ha.MiddleName, ''), NULLIF(ha2.MiddleName, '')) AS MiddleName,
                COALESCE(NULLIF(ha.LastName, ''), NULLIF(ha2.LastName, '')) AS LastName,
                COALESCE(NULLIF(ha.NameExtn, ''), NULLIF(ha2.NameExtn, '')) AS NameExtn,
                COALESCE(ha.id, ha2.id, app.applicant_id) AS applicant_profile_id,
                COALESCE(NULLIF(ha.resHouseNo, ''), NULLIF(ha2.resHouseNo, '')) AS resHouseNo,
                COALESCE(NULLIF(ha.resStreet, ''), NULLIF(ha2.resStreet, '')) AS resStreet,
                COALESCE(NULLIF(ha.resVillage, ''), NULLIF(ha2.resVillage, '')) AS resVillage,
                COALESCE(NULLIF(ha.resBarangay, ''), NULLIF(ha2.resBarangay, '')) AS resBarangay,
                COALESCE(NULLIF(ha.resCity, ''), NULLIF(ha2.resCity, '')) AS resCity,
                COALESCE(NULLIF(ha.resProvince, ''), NULLIF(ha2.resProvince, '')) AS resProvince,
                COALESCE(NULLIF(ha.resZipCode, ''), NULLIF(ha2.resZipCode, '')) AS resZipCode,
                COALESCE(NULLIF(ha.`groups`, ''), NULLIF(ha2.`groups`, '')) AS applicant_group,
                COALESCE(NULLIF(ha.track, ''), NULLIF(ha2.track, '')) AS track,
                COALESCE(NULLIF(ha.jhss, ''), NULLIF(ha2.jhss, '')) AS jhss,
                COALESCE(NULLIF(ha.shss, ''), NULLIF(ha2.shss, '')) AS shss,
                COALESCE(NULLIF(ha.specialization, ''), NULLIF(ha2.specialization, '')) AS specialization,
                COALESCE(NULLIF(ha.tscfile, ''), NULLIF(ha2.tscfile, ''), NULLIF(ha.tcfile, ''), NULLIF(ha2.tcfile, '')) AS national_certificate_file,
                CASE
                    WHEN COALESCE(NULLIF(ha.tscfile, ''), NULLIF(ha2.tscfile, '')) IS NOT NULL THEN 'tscfile'
                    WHEN COALESCE(NULLIF(ha.tcfile, ''), NULLIF(ha2.tcfile, '')) IS NOT NULL THEN 'tcfile'
                    ELSE ''
                END AS national_certificate_column,
                sr.education,
                sr.training,
                sr.experience,
                sr.let_rating,
                sr.demo_rating,
                sr.tr_rating,
                sr.total_points
            ", false)
            ->from('hris_applications app')
            ->join('hris_jobvacancy jv', 'jv.jobID = app.jobID', 'left')
            ->join('(' . $ratingSql . ') sr', 'sr.appID = app.appID', 'left', false)
            ->join('hris_applicant ha', 'ha.id = app.applicant_id', 'left')
            ->join('hris_applicant ha2', 'ha2.record_no = app.applicant_id', 'left')
            ->where('jv.jvStatus', 'Open')
            ->where('app.dq !=', 2);

        if (!empty($yearFilter)) {
            $this->db->where('app.app_year', $yearFilter);
        } else {
            $this->db->where('app.app_year', $fy);
        }

        if (!empty($jobId)) {
            $this->db->where('app.jobID', $jobId);
        } elseif (!empty($jobTypeFilter)) {
            $this->db->where('jv.job_type', $jobTypeFilter);
        }

        return $this->db
            ->order_by('jv.job_type', 'asc')
            ->order_by('jv.jobTitle', 'asc')
            ->order_by('ha.LastName', 'asc')
            ->order_by('ha.FirstName', 'asc')
            ->get()
            ->result();
    }

    public function dq_applicants_list(array $jobTypes, int $fy): array
    {
        if (empty($jobTypes)) {
            return [];
        }

        $this->db
            ->select('app.appID, app.applicant_id, app.jobID, app.pre_school, app.appStatus, app.district, app.app_year, app.dq,
                      jv.jobTitle, jv.job_type, jv.jvStatus,
                      COALESCE(ha.record_no, ha2.record_no) AS record_no,
                      COALESCE(ha.FirstName, ha2.FirstName) AS FirstName,
                      COALESCE(ha.LastName, ha2.LastName) AS LastName,
                      COALESCE(ha.MiddleName, ha2.MiddleName) AS MiddleName')
            ->from('hris_applications app')
            ->join('hris_jobvacancy jv', 'jv.jobID = app.jobID', 'left')
            ->join('hris_applicant ha', 'ha.id = app.applicant_id', 'left')
            ->join('hris_applicant ha2', 'ha2.record_no = app.applicant_id', 'left')
            ->where('app.app_year', $fy)
            ->where('jv.jvStatus', 'Open')
            ->where('app.dq', 2);
        $this->apply_scopes($jobTypes);

        return $this->db
            ->order_by('jv.jvStatus', 'asc')
            ->order_by('app.appID', 'desc')
            ->get()
            ->result();
    }

    public function get_available_districts(array $jobTypes): array
    {
        // Return all districts regardless of job types
        // The jobTypes parameter is kept for compatibility but not used for filtering
        return $this->db
            ->select('app.district')
            ->distinct()
            ->from('hris_applications app')
            ->join('hris_jobvacancy jv', 'jv.jobID = app.jobID', 'left')
            ->where('jv.jvStatus', 'Open')
            ->where('app.district IS NOT NULL')
            ->where('app.district !=', '')
            ->order_by('app.district', 'asc')
            ->get()
            ->result();
    }

    public function get_available_years(array $jobTypes): array
    {
        // Return all years regardless of job types
        // The jobTypes parameter is kept for compatibility but not used for filtering
        return $this->db
            ->select('app.app_year')
            ->distinct()
            ->from('hris_applications app')
            ->join('hris_jobvacancy jv', 'jv.jobID = app.jobID', 'left')
            ->where('jv.jvStatus', 'Open')
            ->where('app.app_year IS NOT NULL')
            ->order_by('app.app_year', 'desc')
            ->get()
            ->result();
    }

    /**
     * Get application inquiries report for Secretariat level
     * Only shows inquiries with stat = 0 (not confirmed)
     * Excludes data where res (response) is not a valid email format
     * Groups by application_id to show one inquiry per application
     */
    public function inquiry_report(
        int $fy,
        ?int $jobTypeFilter = null,
        ?int $jobId = null,
        ?string $districtFilter = null,
        ?int $yearFilter = null,
        ?string $statusFilter = null
    ): array {
        
        // At least one filter must be provided
        if (empty($jobId) && empty($jobTypeFilter) && empty($districtFilter) && empty($yearFilter)) {
            return [];
        }

        $this->db
            ->select('
                COALESCE(ha.record_no, app.applicant_id) AS record_no,
                COALESCE(ha.FirstName, "") AS FirstName,
                COALESCE(ha.LastName, "") AS LastName,
                COALESCE(ha.MiddleName, "") AS MiddleName,
                jv.jobTitle,
                jv.job_type,
                MAX(ai.inquiry) AS inquiry,
                MAX(ai.idate) AS idate,
                MAX(ai.stat) AS stat,
                COUNT(ai.id) AS inquiry_count,
                app.appID,
                ai.job_id,
                ai.application_id,
                ai.res
            ')
            ->from('hris_application_inquiry ai')
            ->join('hris_applications app', 'app.appID = ai.application_id', 'left')
            ->join('hris_jobvacancy jv', 'jv.jobID = ai.job_id', 'left')
            ->join('hris_applicant ha', 'ha.id = app.applicant_id', 'left')
            ->where('jv.jvStatus', 'Open')
            ->where('ai.stat', 0);  // Only show inquiries with stat = 0

        // Apply year filter
        if (!empty($yearFilter)) {
            $this->db->where('app.app_year', $yearFilter);
        } else {
            $this->db->where('app.app_year', $fy);
        }

        // Apply job filter - MUST match on job_id or job_type
        if (!empty($jobId)) {
            $this->db->where('ai.job_id', $jobId);
        } elseif (!empty($jobTypeFilter)) {
            $this->db->where('jv.job_type', $jobTypeFilter);
        }

        // Apply district filter
        if (!empty($districtFilter)) {
            $this->db->where('app.district', $districtFilter);
        }

        // Group by application_id to show one inquiry per application
        $this->db->group_by('ai.application_id');

        $results = $this->db
            ->order_by('jv.job_type', 'asc')
            ->order_by('jv.jobTitle', 'asc')
            ->order_by('ha.LastName', 'asc')
            ->order_by('ha.FirstName', 'asc')
            ->get()
            ->result();

        // Filter out records where 'res' has data but is NOT a valid email format
        // Keep records where 'res' is empty or null
        $filtered_results = [];
        foreach ($results as $row) {
            $res = trim((string) ($row->res ?? ''));
            // If res is empty or null, keep it; if it has data, validate email format
            if (empty($res) || $this->is_valid_email($res)) {
                $filtered_results[] = $row;
            }
        }

        return $filtered_results;
    }

    /**
     * Validate if a string is a valid email format
     */
    public function is_valid_email(string $email): bool
    {
        return filter_var(trim($email), FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Get inquiry statistics grouped by status
     */
    public function inquiry_statistics(
        int $fy,
        ?int $jobTypeFilter = null,
        ?int $jobId = null,
        ?string $districtFilter = null,
        ?int $yearFilter = null
    ): array {
        
        $this->db
            ->select('ai.stat, COUNT(*) as count', false)
            ->from('hris_application_inquiry ai')
            ->join('hris_applications app', 'app.appID = ai.application_id OR app.applicant_id = ai.applicant_id', 'left')
            ->join('hris_jobvacancy jv', 'jv.jobID = ai.job_id OR jv.jobID = app.jobID', 'left')
            ->where('jv.jvStatus', 'Open')
            ->where('app.app_year', $yearFilter ?? $fy);

        if (!empty($jobId)) {
            $this->db->where('ai.job_id', $jobId);
        } elseif (!empty($jobTypeFilter)) {
            $this->db->where('jv.job_type', $jobTypeFilter);
        }

        if (!empty($districtFilter)) {
            $this->db->where('app.district', $districtFilter);
        }

        $results = $this->db
            ->group_by('ai.stat')
            ->get()
            ->result();

        $stats = [
            'confirmed' => 0,
            'not_confirmed' => 0,
            'invalid_email' => 0,
        ];

        foreach ($results as $row) {
            if ($row->stat == 1) {
                $stats['confirmed'] = (int)$row->count;
            } elseif ($row->stat == 0) {
                $stats['not_confirmed'] = (int)$row->count;
            }
        }

        // Count invalid emails
        $inquiries = $this->inquiry_report($fy, $jobTypeFilter, $jobId, $districtFilter, $yearFilter);
        foreach ($inquiries as $inquiry) {
            if (!empty($inquiry->empEmail) && !$this->is_valid_email($inquiry->empEmail)) {
                $stats['invalid_email']++;
            }
        }

        return $stats;
    }

    /* ------------------------------------------------------------------
     * Field Encoder accounts
     *
     * A Secretariat may create limited "Field Encoder" logins that can do
     * nothing but encode Interview / Written Examination scores for the
     * vacancies already assigned to that Secretariat. The accounts live in
     * `users` with position 'Field Encoder'; `users.user_id` holds the owning
     * Secretariat's `users.id`, which is what scopes every query below.
     * ------------------------------------------------------------------ */

    public function field_encoders(int $secretariatUserId): array
    {
        if ($secretariatUserId <= 0) {
            return [];
        }

        return $this->db
            ->select('id, username, fname, mname, lname, status')
            ->from('users')
            ->where('position', self::FIELD_ENCODER_POSITION)
            ->where('user_id', (string) $secretariatUserId)
            ->order_by('lname', 'asc')
            ->order_by('fname', 'asc')
            ->get()
            ->result();
    }

    public function field_encoder(int $secretariatUserId, int $encoderId)
    {
        if ($secretariatUserId <= 0 || $encoderId <= 0) {
            return null;
        }

        return $this->db
            ->select('id, username, fname, mname, lname, status')
            ->from('users')
            ->where('id', $encoderId)
            ->where('position', self::FIELD_ENCODER_POSITION)
            ->where('user_id', (string) $secretariatUserId)
            ->get()
            ->row();
    }

    /** The Secretariat that owns a Field Encoder login, or 0 when unowned. */
    public function field_encoder_owner(int $encoderId): int
    {
        if ($encoderId <= 0) {
            return 0;
        }

        $row = $this->db
            ->select('user_id')
            ->from('users')
            ->where('id', $encoderId)
            ->where('position', self::FIELD_ENCODER_POSITION)
            ->get()
            ->row();

        return $row ? (int) $row->user_id : 0;
    }

    public function username_taken(string $username, int $exceptId = 0): bool
    {
        $this->db->from('users')->where('username', $username);
        if ($exceptId > 0) {
            $this->db->where('id !=', $exceptId);
        }

        return (bool) $this->db->count_all_results();
    }

    public function create_field_encoder(int $secretariatUserId, array $fields): int
    {
        $this->db->insert('users', [
            'username' => $fields['username'],
            'password' => password_hash($fields['password'], PASSWORD_DEFAULT),
            'position' => self::FIELD_ENCODER_POSITION,
            'fname' => $fields['fname'],
            'mname' => $fields['mname'],
            'lname' => $fields['lname'],
            'address' => '',
            'sex' => '',
            'image' => '',
            'user_id' => (string) $secretariatUserId,
            'status' => 1,
            'sp' => 0,
            'egroup' => 0,
            'd_id' => 0,
        ]);

        return (int) $this->db->insert_id();
    }

    public function update_field_encoder(int $secretariatUserId, int $encoderId, array $fields): bool
    {
        if (empty($this->field_encoder($secretariatUserId, $encoderId))) {
            return false;
        }

        $this->db
            ->where('id', $encoderId)
            ->where('position', self::FIELD_ENCODER_POSITION)
            ->where('user_id', (string) $secretariatUserId)
            ->update('users', [
                'username' => $fields['username'],
                'fname' => $fields['fname'],
                'mname' => $fields['mname'],
                'lname' => $fields['lname'],
            ]);

        return true;
    }

    public function set_field_encoder_password(int $secretariatUserId, int $encoderId, string $password): bool
    {
        if (empty($this->field_encoder($secretariatUserId, $encoderId))) {
            return false;
        }

        $this->db
            ->where('id', $encoderId)
            ->where('position', self::FIELD_ENCODER_POSITION)
            ->where('user_id', (string) $secretariatUserId)
            ->update('users', ['password' => password_hash($password, PASSWORD_DEFAULT)]);

        return true;
    }

    public function delete_field_encoder(int $secretariatUserId, int $encoderId): bool
    {
        if (empty($this->field_encoder($secretariatUserId, $encoderId))) {
            return false;
        }

        $this->db
            ->where('id', $encoderId)
            ->where('position', self::FIELD_ENCODER_POSITION)
            ->where('user_id', (string) $secretariatUserId)
            ->delete('users');

        return true;
    }

    /* ------------------------------------------------------------------
     * Field Encoder access scope
     *
     * A Field Encoder borrows the Secretariat's vacancies, but the Secretariat
     * decides which of those vacancies the account may open and which of the
     * two scores it may encode there. One row per (encoder, vacancy) in
     * hris_field_encoder_access; `encode_mode` is written / interview / both.
     *
     * An encoder with no row at all reaches nothing - the scope is opt-in.
     * ------------------------------------------------------------------ */

    /** Allowed values of hris_field_encoder_access.encode_mode. */
    const ENCODE_MODES = ['written', 'interview', 'both'];

    public function ensure_field_encoder_access_table(): void
    {
        $debug = $this->db->db_debug;
        $this->db->db_debug = false;
        $existing = $this->db->query(
            "select 1 from information_schema.TABLES
             where TABLE_SCHEMA = database()
               and TABLE_NAME = 'hris_field_encoder_access'"
        );
        $fresh = !$existing || $existing->num_rows() === 0;
        $this->db->db_debug = $debug;

        $this->db->query("
            CREATE TABLE IF NOT EXISTS hris_field_encoder_access (
                id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                encoder_user_id INT UNSIGNED NOT NULL,
                job_id INT UNSIGNED NOT NULL,
                encode_mode VARCHAR(10) NOT NULL DEFAULT 'both',
                created_by INT UNSIGNED NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY uniq_encoder_vacancy (encoder_user_id, job_id),
                KEY idx_encoder (encoder_user_id),
                KEY idx_job_id (job_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");

        if ($fresh) {
            $this->backfill_field_encoder_access();
        }
    }

    /**
     * Field Encoders created before per-vacancy tagging existed reached every
     * vacancy their Secretariat holds. Seed exactly that on the one run that
     * creates the table, so the upgrade locks nobody out; from then on the
     * scope is whatever the Secretariat ticks.
     */
    private function backfill_field_encoder_access(): void
    {
        $this->db->query("
            insert ignore into hris_field_encoder_access (encoder_user_id, job_id, encode_mode, created_by)
            select u.id, sv.job_id, 'both', sv.secretariat_user_id
            from users u
            join hris_secretariat_vacancies sv on sv.secretariat_user_id = CAST(u.user_id AS UNSIGNED)
            join hris_jobvacancy j on j.jobID = sv.job_id
            where u.position = '" . self::FIELD_ENCODER_POSITION . "'
              and j.jvStatus != 'Closed'
              and j.position not in (1, 5)
        ");
    }

    public function normalize_encode_mode($mode): string
    {
        $mode = strtolower(trim((string) $mode));
        return in_array($mode, self::ENCODE_MODES, true) ? $mode : 'both';
    }

    /** Vacancies a Secretariat may hand to a Field Encoder (score-eligible only). */
    public function assignable_vacancies(int $secretariatUserId): array
    {
        $vacancies = [];
        foreach ($this->tagging_vacancies($secretariatUserId) as $vacancy) {
            // Teaching and promotion sheets use other rating tables/criteria,
            // so they are outside what a Field Encoder can reach.
            if (in_array((int) $vacancy->position, [1, 5], true)) {
                continue;
            }
            $vacancies[] = $vacancy;
        }

        return $vacancies;
    }

    /** [job_id => encode_mode] for one Field Encoder. */
    public function field_encoder_access(int $encoderId): array
    {
        if ($encoderId <= 0) {
            return [];
        }

        $access = [];
        $rows = $this->db
            ->select('job_id, encode_mode')
            ->from('hris_field_encoder_access')
            ->where('encoder_user_id', $encoderId)
            ->get()
            ->result();

        foreach ($rows as $row) {
            $access[(int) $row->job_id] = $this->normalize_encode_mode($row->encode_mode);
        }

        return $access;
    }

    /** [encoder_id => [job_id => encode_mode]] for a whole list page. */
    public function field_encoder_access_map(array $encoderIds): array
    {
        $encoderIds = array_values(array_filter(array_map('intval', $encoderIds)));
        if (empty($encoderIds)) {
            return [];
        }

        $map = [];
        $rows = $this->db
            ->select('encoder_user_id, job_id, encode_mode')
            ->from('hris_field_encoder_access')
            ->where_in('encoder_user_id', $encoderIds)
            ->get()
            ->result();

        foreach ($rows as $row) {
            $map[(int) $row->encoder_user_id][(int) $row->job_id] = $this->normalize_encode_mode($row->encode_mode);
        }

        return $map;
    }

    /**
     * Replace a Field Encoder's whole scope with $access ([job_id => mode]).
     * Job ids outside the Secretariat's own score-eligible vacancies are
     * dropped, so an encoder can never be handed more than its owner has.
     */
    public function save_field_encoder_access(int $secretariatUserId, int $encoderId, array $access): bool
    {
        if (empty($this->field_encoder($secretariatUserId, $encoderId))) {
            return false;
        }

        $allowed = [];
        foreach ($this->assignable_vacancies($secretariatUserId) as $vacancy) {
            $allowed[(int) $vacancy->jobID] = true;
        }

        $rows = [];
        foreach ($access as $jobId => $mode) {
            $jobId = (int) $jobId;
            if ($jobId <= 0 || !isset($allowed[$jobId])) {
                continue;
            }
            $rows[$jobId] = [
                'encoder_user_id' => $encoderId,
                'job_id' => $jobId,
                'encode_mode' => $this->normalize_encode_mode($mode),
                'created_by' => $secretariatUserId,
                'created_at' => date('Y-m-d H:i:s'),
            ];
        }

        $this->db->where('encoder_user_id', $encoderId)->delete('hris_field_encoder_access');
        if (!empty($rows)) {
            $this->db->insert_batch('hris_field_encoder_access', array_values($rows));
        }

        return true;
    }

    /** Drop the scope rows of a removed encoder / archived vacancy. */
    public function remove_field_encoder_access(int $encoderId): void
    {
        if ($encoderId > 0) {
            $this->db->where('encoder_user_id', $encoderId)->delete('hris_field_encoder_access');
        }
    }

    /** encode_mode a Field Encoder holds on one vacancy, or '' when locked out. */
    public function field_encoder_mode(int $encoderId, int $jobId): string
    {
        if ($encoderId <= 0 || $jobId <= 0) {
            return '';
        }

        $row = $this->db
            ->select('encode_mode')
            ->from('hris_field_encoder_access')
            ->where('encoder_user_id', $encoderId)
            ->where('job_id', $jobId)
            ->get()
            ->row();

        return $row ? $this->normalize_encode_mode($row->encode_mode) : '';
    }

    /* ------------------------------------------------------------------
     * Field Evaluator vacancy tagging
     *
     * A Secretariat may tag an existing Evaluator account as the "Field
     * Evaluator" of one of its vacancies. That tag does not assign applicants
     * and does not touch hris_rater_assignments - it only opens a vacancy-wide
     * view for that evaluator: every applicant of the vacancy, who is tagged to
     * evaluate each one, and a link into the application itself.
     *
     * One row per (evaluator, vacancy) in hris_field_evaluator_access. An
     * evaluator with no row reaches nothing extra, so the scope is opt-in.
     * ------------------------------------------------------------------ */

    /** users.position values that may hold a Field Evaluator tag. */
    const EVALUATOR_POSITIONS = ['Evaluator', 'rater', 'raters'];

    public function ensure_field_evaluator_access_table(): void
    {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS hris_field_evaluator_access (
                id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                evaluator_user_id INT UNSIGNED NOT NULL,
                job_id INT UNSIGNED NOT NULL,
                secretariat_user_id INT UNSIGNED NULL,
                created_by INT UNSIGNED NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY uniq_evaluator_vacancy (evaluator_user_id, job_id),
                KEY idx_evaluator (evaluator_user_id),
                KEY idx_job_id (job_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");
    }

    /** Field Evaluators already tagged on one of the Secretariat's vacancies. */
    public function field_evaluator_tags(int $userId, int $jobId): array
    {
        if (!$this->secretariat_has_vacancy($userId, $jobId)) {
            return [];
        }

        return $this->db
            ->select("fe.id AS tag_id, fe.evaluator_user_id, fe.created_at,
                u.username, u.fname, u.mname, u.lname,
                CONCAT_WS(' ', NULLIF(TRIM(u.fname), ''), NULLIF(TRIM(u.mname), ''), NULLIF(TRIM(u.lname), '')) AS evaluator_name,
                COUNT(DISTINCT ra.app_id) AS assigned_total", false)
            ->from('hris_field_evaluator_access fe')
            ->join('users u', 'u.id = fe.evaluator_user_id')
            ->join('hris_rater_assignments ra', 'ra.rater_user_id = fe.evaluator_user_id AND ra.job_id = fe.job_id', 'left', false)
            ->where('fe.job_id', $jobId)
            ->group_by(['fe.id', 'fe.evaluator_user_id', 'fe.created_at', 'u.username', 'u.fname', 'u.mname', 'u.lname'])
            ->order_by('u.lname', 'asc')
            ->order_by('u.fname', 'asc')
            ->get()
            ->result();
    }

    /** Tag one Evaluator account as Field Evaluator of one owned vacancy. */
    public function tag_field_evaluator(int $userId, int $jobId, int $evaluatorId, ?int $createdBy): array
    {
        if (!$this->secretariat_has_vacancy($userId, $jobId)) {
            return ['ok' => false, 'message' => 'That vacancy is not assigned to your Secretariat account.'];
        }

        $evaluator = $this->db
            ->select('id, fname, mname, lname, username')
            ->from('users')
            ->where('id', $evaluatorId)
            ->where_in('position', self::EVALUATOR_POSITIONS)
            ->get()
            ->row();

        if (empty($evaluator)) {
            return ['ok' => false, 'message' => 'Please select an eligible evaluator.'];
        }

        $name = trim(preg_replace('/\s+/', ' ', implode(' ', [
            (string) $evaluator->fname,
            (string) $evaluator->mname,
            (string) $evaluator->lname,
        ])));
        $name = $name !== '' ? $name : (string) $evaluator->username;

        $existing = $this->db
            ->from('hris_field_evaluator_access')
            ->where('evaluator_user_id', $evaluatorId)
            ->where('job_id', $jobId)
            ->count_all_results();

        if ($existing) {
            return [
                'ok' => false,
                'message' => $name . ' is already a Field Evaluator for this vacancy.',
            ];
        }

        $this->db->insert('hris_field_evaluator_access', [
            'evaluator_user_id' => $evaluatorId,
            'job_id' => $jobId,
            'secretariat_user_id' => $userId,
            'created_by' => $createdBy,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return [
            'ok' => true,
            'message' => $name . ' was tagged as Field Evaluator for this vacancy.',
            'evaluator_name' => $name,
        ];
    }

    /** Remove a Field Evaluator tag from one of the Secretariat's vacancies. */
    public function untag_field_evaluator(int $userId, int $jobId, int $evaluatorId): array
    {
        if (!$this->secretariat_has_vacancy($userId, $jobId)) {
            return ['ok' => false, 'message' => 'That vacancy is not assigned to your Secretariat account.'];
        }

        $this->db
            ->where('evaluator_user_id', $evaluatorId)
            ->where('job_id', $jobId)
            ->delete('hris_field_evaluator_access');

        return ['ok' => true, 'message' => 'The Field Evaluator tag was removed.'];
    }

    /** Evaluator accounts a Secretariat may tag (position Evaluator/rater). */
    public function taggable_field_evaluators(): array
    {
        return $this->db
            ->select('id, username, fname, mname, lname')
            ->from('users')
            ->where_in('position', self::EVALUATOR_POSITIONS)
            ->where('status', 1)
            ->order_by('lname', 'asc')
            ->order_by('fname', 'asc')
            ->get()
            ->result();
    }

    /** True when the account holds at least one Field Evaluator tag. */
    public function is_field_evaluator(int $evaluatorId): bool
    {
        if ($evaluatorId <= 0) {
            return false;
        }

        return (bool) $this->db
            ->from('hris_field_evaluator_access fe')
            ->join('hris_jobvacancy j', 'j.jobID = fe.job_id')
            ->where('fe.evaluator_user_id', $evaluatorId)
            ->where('j.jvStatus !=', 'Closed')
            ->count_all_results();
    }

    /** Vacancies one Field Evaluator may open, with their applicant totals. */
    public function field_evaluator_vacancies(int $evaluatorId): array
    {
        if ($evaluatorId <= 0) {
            return [];
        }

        return $this->db
            ->select("j.jobID, j.jobTitle, j.position, j.job_type, j.sy, j.itemNo, j.department,
                COUNT(DISTINCT a.appID) AS applicant_total,
                COUNT(DISTINCT CASE WHEN ra.id IS NOT NULL THEN a.appID END) AS tagged_total,
                COUNT(DISTINCT CASE WHEN a.appID IS NOT NULL AND ra.id IS NULL THEN a.appID END) AS untagged_total,
                COUNT(DISTINCT CASE WHEN a.dq = 2 THEN a.appID END) AS dq_total,
                COUNT(DISTINCT CASE
                    WHEN ra.rater_user_id = " . (int) $evaluatorId . " THEN a.appID
                END) AS mine_total", false)
            ->from('hris_field_evaluator_access fe')
            ->join('hris_jobvacancy j', 'j.jobID = fe.job_id')
            ->join('hris_applications a', 'a.jobID = j.jobID', 'left')
            ->join('hris_rater_assignments ra', 'ra.app_id = a.appID', 'left')
            ->where('fe.evaluator_user_id', $evaluatorId)
            ->where('j.jvStatus !=', 'Closed')
            ->group_by(['j.jobID', 'j.jobTitle', 'j.position', 'j.job_type', 'j.sy', 'j.itemNo', 'j.department'])
            ->order_by('j.sy', 'desc')
            ->order_by('j.jobTitle', 'asc')
            ->get()
            ->result();
    }

    public function field_evaluator_has_vacancy(int $evaluatorId, int $jobId): bool
    {
        if ($evaluatorId <= 0 || $jobId <= 0) {
            return false;
        }

        return (bool) $this->db
            ->from('hris_field_evaluator_access fe')
            ->join('hris_jobvacancy j', 'j.jobID = fe.job_id')
            ->where('fe.evaluator_user_id', $evaluatorId)
            ->where('fe.job_id', $jobId)
            ->where('j.jvStatus !=', 'Closed')
            ->count_all_results();
    }

    /**
     * The application's vacancy is one this account field-evaluates. Used by
     * the rating page to let a Field Evaluator open an application that is
     * tagged to somebody else; saving a rating stays gated on the real
     * hris_rater_assignments row.
     */
    public function field_evaluator_can_view_application(int $evaluatorId, int $appId): bool
    {
        if ($evaluatorId <= 0 || $appId <= 0) {
            return false;
        }

        return (bool) $this->db
            ->from('hris_applications a')
            ->join('hris_field_evaluator_access fe', 'fe.job_id = a.jobID')
            ->where('a.appID', $appId)
            ->where('fe.evaluator_user_id', $evaluatorId)
            ->count_all_results();
    }

    /**
     * Every applicant of one field-evaluated vacancy, with the evaluator tagged
     * to each row joined in. Same shape as applicants_for_tagging so the view
     * can build the very same profile links.
     */
    public function field_evaluator_applicants(int $evaluatorId, int $jobId): array
    {
        if (!$this->field_evaluator_has_vacancy($evaluatorId, $jobId)) {
            return [];
        }

        $latestAssignment = $this->db
            ->select('app_id, MAX(id) AS assignment_id', false)
            ->from('hris_rater_assignments')
            ->group_by('app_id')
            ->get_compiled_select();

        return $this->db
            ->select("a.appID, a.applicant_id, a.jobID, a.empEmail, a.appStatus, a.dateSubmitted,
                a.app_year, a.district, a.pre_school, a.dq,
                j.jobTitle, j.position, j.job_type, j.sy,
                COALESCE(ha.record_no, ha2.record_no, hs.IDNumber, a.applicant_id) AS record_no,
                COALESCE(ha.id, ha2.id, hs.IDNumber, a.applicant_id) AS profile_id,
                COALESCE(ha.FirstName, ha2.FirstName, hs.FirstName, '') AS FirstName,
                COALESCE(ha.MiddleName, ha2.MiddleName, hs.MiddleName, '') AS MiddleName,
                COALESCE(ha.LastName, ha2.LastName, hs.LastName, '') AS LastName,
                COALESCE(ha.NameExtn, ha2.NameExtn, hs.NameExtn, '') AS NameExtn,
                COALESCE(ha.specialization, ha2.specialization, '') AS specialization,
                CASE
                    WHEN ha.id IS NOT NULL OR ha2.id IS NOT NULL THEN 'ma'
                    WHEN hs.IDNumber IS NOT NULL THEN 'ma_staff'
                    ELSE ''
                END AS profile_route,
                s.schoolName,
                ra.id AS assignment_id, ra.rater_user_id, ra.assigned_at,
                CONCAT_WS(' ', NULLIF(TRIM(u.fname), ''), NULLIF(TRIM(u.mname), ''), NULLIF(TRIM(u.lname), '')) AS evaluator_name,
                u.username AS evaluator_username,
                CASE WHEN ra.rater_user_id = " . (int) $evaluatorId . " THEN 1 ELSE 0 END AS is_mine", false)
            ->from('hris_applications a')
            ->join('hris_jobvacancy j', 'j.jobID = a.jobID')
            ->join('hris_applicant ha', 'ha.id = a.applicant_id', 'left')
            ->join('hris_applicant ha2', 'ha2.record_no = CONVERT(CAST(a.applicant_id AS CHAR) USING latin1) COLLATE latin1_swedish_ci AND ha.id IS NULL', 'left', false)
            ->join('hris_staff hs', 'CONVERT(hs.IDNumber USING utf8mb4) COLLATE utf8mb4_general_ci = a.empEmail AND ha.id IS NULL AND ha2.id IS NULL', 'left', false)
            ->join('schools s', 's.schoolID = CONVERT(CAST(a.pre_school AS CHAR) USING utf8mb4) COLLATE utf8mb4_unicode_ci', 'left', false)
            ->join("($latestAssignment) latest_ra", 'latest_ra.app_id = a.appID', 'left')
            ->join('hris_rater_assignments ra', 'ra.id = latest_ra.assignment_id', 'left')
            ->join('users u', 'u.id = ra.rater_user_id', 'left')
            ->where('a.jobID', $jobId)
            ->order_by('ha.LastName', 'asc')
            ->order_by('hs.LastName', 'asc')
            ->order_by('a.appID', 'desc')
            ->get()
            ->result();
    }

    /* ------------------------------------------------------------------
     * Score encoding activity
     *
     * Every save writes an audit row (action 'rate', field interview/written)
     * through Audit_model. These read it back so the encoding screen can show
     * who touched which score and when.
     * ------------------------------------------------------------------ */

    /** Recent encode / edit actions on one vacancy, newest first. */
    public function score_activity(int $jobId, int $limit = 80): array
    {
        if ($jobId <= 0) {
            return [];
        }

        return $this->db
            ->select("t.id, t.created_at, t.user_id, t.username, t.fname, t.lname, t.position,
                t.app_id, t.applicant_id, t.field, t.description,
                COALESCE(ha.LastName, hs.LastName, '') AS app_last,
                COALESCE(ha.FirstName, hs.FirstName, '') AS app_first", false)
            ->from('hris_audit_trail t')
            ->join('hris_applications a', 'a.appID = t.app_id', 'left')
            ->join('hris_applicant ha', 'ha.id = a.applicant_id', 'left')
            ->join('hris_staff hs', 'CONVERT(hs.IDNumber USING utf8mb4) COLLATE utf8mb4_general_ci = a.empEmail AND ha.id IS NULL', 'left', false)
            ->where('t.job_id', $jobId)
            ->where('t.action', 'rate')
            ->where_in('t.field', ['interview', 'written'])
            ->order_by('t.id', 'desc')
            ->limit($limit)
            ->get()
            ->result();
    }

    /**
     * Latest actor per application and field for one vacancy, as
     * [app_id][field] => ['name' => ..., 'when' => ..., 'position' => ...].
     */
    public function score_last_actions(int $jobId): array
    {
        if ($jobId <= 0) {
            return [];
        }

        $latest = $this->db
            ->select('app_id, field, MAX(id) AS latest_id', false)
            ->from('hris_audit_trail')
            ->where('job_id', $jobId)
            ->where('action', 'rate')
            ->where_in('field', ['interview', 'written'])
            ->group_by(['app_id', 'field'])
            ->get_compiled_select();

        $rows = $this->db
            ->select('t.app_id, t.field, t.username, t.fname, t.lname, t.position, t.created_at, t.description')
            ->from('hris_audit_trail t')
            ->join("($latest) latest", 'latest.latest_id = t.id', 'inner')
            ->get()
            ->result();

        $map = [];
        foreach ($rows as $row) {
            $name = trim(trim((string) $row->fname) . ' ' . trim((string) $row->lname));
            if ($name === '') {
                $name = (string) $row->username;
            }
            $map[(int) $row->app_id][(string) $row->field] = [
                'name' => $name,
                'username' => (string) $row->username,
                'position' => (string) $row->position,
                'when' => (string) $row->created_at,
                'description' => (string) $row->description,
            ];
        }

        return $map;
    }

    /**
     * Indexes the recruitment reports lean on.
     *
     * hris_applications and hris_applicant store empEmail in different
     * character sets (utf8mb4 vs latin1), so joining them on email can never
     * use an index - the reports fetch each side separately instead. These
     * keys make those flat lookups, and the IER's per-row training/experience
     * totals, index seeks rather than full table scans.
     *
     * Idempotent: only missing keys are added, nothing is dropped or altered.
     */
    public function ensure_report_indexes(): void
    {
        $wanted = [
            'hris_applicant'    => ['idx_email_id'   => '(`empEmail`, `id`)'],
            'hris_applications' => ['idx_job_status' => '(`jobID`, `appStatus`, `dq`)'],
            'hris_experience'   => ['idx_idnum_stat' => '(`id_number`, `stat`)'],
            'hris_trainings'    => ['idx_idnum_stat' => '(`IDNumber`, `stat`)'],
        ];

        $existing = $this->db
            ->select('TABLE_NAME, INDEX_NAME')
            ->from('information_schema.STATISTICS')
            ->where('TABLE_SCHEMA', $this->db->database)
            ->where_in('TABLE_NAME', array_keys($wanted))
            ->get()
            ->result();

        $have = [];
        foreach ($existing as $row) {
            $have[$row->TABLE_NAME . '.' . $row->INDEX_NAME] = true;
        }

        $debug = $this->db->db_debug;
        $this->db->db_debug = false;

        foreach ($wanted as $table => $indexes) {
            foreach ($indexes as $name => $columns) {
                if (isset($have[$table . '.' . $name])) {
                    continue;
                }

                $this->db->query("ALTER TABLE `{$table}` ADD KEY `{$name}` {$columns}");
            }
        }

        $this->db->db_debug = $debug;
    }

    /**
     * Latest hris_applicant row per email, with the hris_staff record as the
     * fallback for in-service applicants, keyed by lower-cased email.
     *
     * Deliberately two flat lookups instead of joining hris_applications to
     * hris_applicant: see ensure_report_indexes() for why that join is slow.
     */
    private function applicant_details(array $emails): array
    {
        $emails = array_values(array_unique(array_filter(array_map('trim', $emails), 'strlen')));

        if (!$emails) {
            return [];
        }

        $columns = 'FirstName, MiddleName, LastName, NameExtn, empMobile, contactNo, bd, csEligibility';
        $people = [];

        foreach (array_chunk($emails, 500) as $chunk) {
            $rows = $this->db
                ->select('id, record_no, empEmail, ' . $columns)
                ->from('hris_applicant')
                ->where_in('empEmail', $chunk)
                ->order_by('id', 'ASC')
                ->get()
                ->result();

            // Ascending id, so a re-registered applicant's newest row is the
            // last one written and wins - same rule as the MAX(id) dedupe.
            foreach ($rows as $row) {
                $people[strtolower(trim($row->empEmail))] = $row;
            }
        }

        $missing = [];
        foreach ($emails as $email) {
            if (!isset($people[strtolower($email)])) {
                $missing[] = $email;
            }
        }

        foreach (array_chunk($missing, 500) as $chunk) {
            $rows = $this->db
                ->select('IDNumber, ' . $columns)
                ->from('hris_staff')
                ->where_in('IDNumber', $chunk)
                ->get()
                ->result();

            foreach ($rows as $row) {
                $row->id = $row->IDNumber;
                $row->record_no = $row->IDNumber;
                $people[strtolower(trim($row->IDNumber))] = $row;
            }
        }

        return $people;
    }

    /**
     * Shortlist contact column: the applicant's own empMobile alongside the
     * contactNo on file, printed as "empMobile / contactNo".
     *
     * Either field can be blank, hold the same number as the other, or already
     * hold two numbers separated by a slash, so the parts are split, trimmed
     * and de-duplicated before being joined back up.
     */
    private function merge_contact_numbers(string ...$fields): string
    {
        $numbers = [];

        foreach ($fields as $field) {
            foreach (preg_split('#[/,]#', $field) as $part) {
                $part = trim($part);

                if ($part === '') {
                    continue;
                }

                // Keyed on digits alone, so "0963 975 6150" and "09639756150"
                // are not printed as two different numbers.
                $digits = preg_replace('/\D+/', '', $part);
                $numbers[$digits !== '' ? $digits : strtolower($part)] = $part;
            }
        }

        return implode(' / ', $numbers);
    }

    private function sort_by_name(array $rows): array
    {
        usort($rows, static function ($a, $b) {
            return strcasecmp(
                $a->LastName . ' ' . $a->FirstName,
                $b->LastName . ' ' . $b->FirstName
            );
        });

        return $rows;
    }

    /**
     * Shortlist rows for one assigned vacancy.
     *
     * Everything that moved past intake: any appStatus other than
     * 'Application Submitted', and never a disqualified row (dq = 2).
     */
    public function shortlist_applicants(int $userId, int $jobId): array
    {
        if (!$this->secretariat_has_vacancy($userId, $jobId)) {
            return [];
        }

        $applications = $this->db
            ->select('appID, empEmail, appStatus')
            ->from('hris_applications')
            ->where('jobID', $jobId)
            ->where('appStatus !=', 'Application Submitted')
            ->where('dq !=', 2)
            ->get()
            ->result();

        if (!$applications) {
            return [];
        }

        $people = $this->applicant_details(array_column($applications, 'empEmail'));
        $rows = [];

        foreach ($applications as $application) {
            $person = $people[strtolower(trim($application->empEmail))] ?? null;

            if (!$person) {
                continue;
            }

            $rows[] = (object) [
                'appID'      => (int) $application->appID,
                'appStatus'  => (string) $application->appStatus,
                'record_no'  => (string) $person->record_no,
                'FirstName'  => (string) $person->FirstName,
                'MiddleName' => (string) $person->MiddleName,
                'LastName'   => (string) $person->LastName,
                'NameExtn'   => (string) $person->NameExtn,
                'contact_no' => $this->merge_contact_numbers(
                    (string) $person->empMobile,
                    (string) $person->contactNo
                ),
            ];
        }

        return $this->sort_by_name($rows);
    }

    /**
     * Qualified (dq = 1) applicants for one assigned vacancy, shaped for the
     * shared IER sheet at views/pages/ha_all_by_jp_v2.php - that view reads
     * jobID, code, id, bachelor, csEligibility and dq off each row.
     */
    public function ier_applicants(int $userId, int $jobId): array
    {
        if (!$this->secretariat_has_vacancy($userId, $jobId)) {
            return [];
        }

        $applications = $this->db
            ->select('appID, empEmail, jobID, dq')
            ->from('hris_applications')
            ->where('jobID', $jobId)
            ->where('dq', 1)
            ->get()
            ->result();

        if (!$applications) {
            return [];
        }

        $people = $this->applicant_details(array_column($applications, 'empEmail'));
        $rows = [];

        foreach ($applications as $application) {
            $person = $people[strtolower(trim($application->empEmail))] ?? null;

            if (!$person) {
                continue;
            }

            $rows[] = (object) [
                'jobID'         => (int) $application->jobID,
                'dq'            => (int) $application->dq,
                'code'          => (string) $person->record_no,
                'id'            => $person->id,
                'bachelor'      => (string) $person->bd,
                'csEligibility' => (string) $person->csEligibility,
                'FirstName'     => (string) $person->FirstName,
                'LastName'      => (string) $person->LastName,
            ];
        }

        return $this->sort_by_name($rows);
    }

    /* ------------------------------------------------------------------ *
     * Secretariat qualified / disqualified lists
     * ------------------------------------------------------------------ */

    /**
     * Qualified (dq = 1) or disqualified (dq = 2) applicants of one vacancy
     * assigned to the current Secretariat account.
     *
     * The qualified rows also carry the rating the evaluator encoded, read from
     * the table the vacancy is actually scored on - hris_applications_rating
     * for teaching, hris_rating_promotion for promotion / position 5 and
     * hris_rating_none for everything else - so the qualified list shows the
     * rated applicants alongside the ones still waiting for scores. The 0.00001
     * stub written by the insert_rate_* methods reads as "not rated yet"; a
     * genuine zero is a real score.
     */
    public function qualification_applicants(int $userId, int $jobId, int $dq): array
    {
        if (!in_array($dq, [1, 2], true) || !$this->secretariat_has_vacancy($userId, $jobId)) {
            return [];
        }

        $latestAssignment = $this->db
            ->select('app_id, MAX(id) AS latest_id', false)
            ->from('hris_rater_assignments')
            ->group_by('app_id')
            ->get_compiled_select();

        $latestDq = $this->db
            ->select('appID, MAX(id) AS latest_id', false)
            ->from('hris_app_dq')
            ->group_by('appID')
            ->get_compiled_select();

        $latestTeaching = $this->db
            ->select('appID, MAX(id) AS latest_id', false)
            ->from('hris_applications_rating')
            ->group_by('appID')
            ->get_compiled_select();

        $latestNone = $this->db
            ->select('appID, MAX(id) AS latest_id', false)
            ->from('hris_rating_none')
            ->group_by('appID')
            ->get_compiled_select();

        $latestPromotion = $this->db
            ->select('appID, MAX(id) AS latest_id', false)
            ->from('hris_rating_promotion')
            ->group_by('appID')
            ->get_compiled_select();

        $rows = $this->db
            ->select("a.appID, a.applicant_id, a.jobID, a.empEmail, a.appStatus, a.dateSubmitted,
                a.app_year, a.district, a.pre_school, a.dq,
                j.jobTitle, j.position, j.job_type, j.sy, j.itemNo, j.promotion,
                COALESCE(ha.record_no, ha2.record_no, hs.IDNumber, a.applicant_id) AS record_no,
                COALESCE(ha.id, ha2.id, hs.IDNumber, a.applicant_id) AS profile_id,
                COALESCE(ha.FirstName, ha2.FirstName, hs.FirstName, '') AS FirstName,
                COALESCE(ha.MiddleName, ha2.MiddleName, hs.MiddleName, '') AS MiddleName,
                COALESCE(ha.LastName, ha2.LastName, hs.LastName, '') AS LastName,
                COALESCE(ha.NameExtn, ha2.NameExtn, hs.NameExtn, '') AS NameExtn,
                COALESCE(ha.specialization, ha2.specialization, '') AS specialization,
                CASE
                    WHEN ha.id IS NOT NULL OR ha2.id IS NOT NULL THEN 'ma'
                    WHEN hs.IDNumber IS NOT NULL THEN 'ma_staff'
                    ELSE ''
                END AS profile_route,
                s.schoolName,
                dq.reason AS dq_reason, dq.vdate AS dq_date,
                ra.rater_user_id, ra.assigned_at,
                CONCAT_WS(' ', NULLIF(TRIM(u.fname), ''), NULLIF(TRIM(u.mname), ''), NULLIF(TRIM(u.lname), '')) AS evaluator_name,
                rt.id AS teach_row, rt.education AS teach_education, rt.training AS teach_training,
                rt.experience AS teach_experience, rt.let_rating AS teach_let,
                rt.demo_rating AS teach_demo, rt.tr_rating AS teach_tr, rt.total_points AS teach_total,
                rn.id AS none_row, rn.educ AS none_educ, rn.trainings AS none_trainings,
                rn.experience AS none_experience, rn.performance AS none_performance,
                rn.oa AS none_oa, rn.ae AS none_ae, rn.ald AS none_ald,
                rn.interview AS none_interview, rn.written AS none_written,
                rn.skills AS none_skills, rn.total_points AS none_total,
                rp.id AS promo_row, rp.educ AS promo_educ, rp.trainings AS promo_trainings,
                rp.experience AS promo_experience, rp.performance AS promo_performance,
                rp.ppstco AS promo_ppstco, rp.ppstpa AS promo_ppstpa, rp.total_points AS promo_total", false)
            ->from('hris_applications a')
            ->join('hris_jobvacancy j', 'j.jobID = a.jobID')
            ->join('hris_applicant ha', 'ha.id = a.applicant_id', 'left')
            ->join('hris_applicant ha2', 'ha2.record_no = CONVERT(CAST(a.applicant_id AS CHAR) USING latin1) COLLATE latin1_swedish_ci AND ha.id IS NULL', 'left', false)
            ->join('hris_staff hs', 'CONVERT(hs.IDNumber USING utf8mb4) COLLATE utf8mb4_general_ci = a.empEmail AND ha.id IS NULL AND ha2.id IS NULL', 'left', false)
            ->join('schools s', 's.schoolID = CONVERT(CAST(a.pre_school AS CHAR) USING utf8mb4) COLLATE utf8mb4_unicode_ci', 'left', false)
            ->join("($latestAssignment) latest_ra", 'latest_ra.app_id = a.appID', 'left')
            ->join('hris_rater_assignments ra', 'ra.id = latest_ra.latest_id', 'left')
            ->join('users u', 'u.id = ra.rater_user_id', 'left')
            ->join("($latestDq) latest_dq", 'latest_dq.appID = a.appID', 'left')
            ->join('hris_app_dq dq', 'dq.id = latest_dq.latest_id', 'left')
            ->join("($latestTeaching) latest_rt", 'latest_rt.appID = a.appID', 'left')
            ->join('hris_applications_rating rt', 'rt.id = latest_rt.latest_id', 'left')
            ->join("($latestNone) latest_rn", 'latest_rn.appID = a.appID', 'left')
            ->join('hris_rating_none rn', 'rn.id = latest_rn.latest_id', 'left')
            ->join("($latestPromotion) latest_rp", 'latest_rp.appID = a.appID', 'left')
            ->join('hris_rating_promotion rp', 'rp.id = latest_rp.latest_id', 'left')
            ->where('a.jobID', $jobId)
            ->where('a.dq', $dq)
            ->order_by('ha.LastName', 'asc')
            ->order_by('hs.LastName', 'asc')
            ->order_by('a.appID', 'desc')
            ->get()
            ->result();

        foreach ($rows as $row) {
            $this->attach_rating_state($row);
        }

        return $rows;
    }

    /**
     * Flag one applicant row with the rating that belongs to its vacancy.
     * Mirrors the Pending / With scores split used by the evaluator workload
     * screen so both places agree on who counts as rated.
     */
    private function attach_rating_state($row): void
    {
        $position = (int) ($row->position ?? 0);
        $isPromotion = (int) ($row->promotion ?? 0) === 1 || $position === 5;

        if ($position === 1 && !$isPromotion) {
            $hasRatingRow = !empty($row->teach_row);
            $row->rating_total = (float) ($row->teach_total ?? 0);
            $row->rating_source = 'Teaching';
            $components = [
                'Education'  => $row->teach_education ?? null,
                'Training'   => $row->teach_training ?? null,
                'Experience' => $row->teach_experience ?? null,
                'LET'        => $row->teach_let ?? null,
            ];
        } elseif ($isPromotion) {
            $hasRatingRow = !empty($row->promo_row);
            $row->rating_total = (float) ($row->promo_total ?? 0);
            $row->rating_source = 'Promotion';
            $components = [
                'Education'   => $row->promo_educ ?? null,
                'Training'    => $row->promo_trainings ?? null,
                'Experience'  => $row->promo_experience ?? null,
                'Performance' => $row->promo_performance ?? null,
                'PPST CO'     => $row->promo_ppstco ?? null,
                'PPST PA'     => $row->promo_ppstpa ?? null,
            ];
        } else {
            $hasRatingRow = !empty($row->none_row);
            $row->rating_total = (float) ($row->none_total ?? 0);
            $row->rating_source = 'Non-Teaching';
            $components = [
                'Education'   => $row->none_educ ?? null,
                'Training'    => $row->none_trainings ?? null,
                'Experience'  => $row->none_experience ?? null,
                'Performance' => $row->none_performance ?? null,
                'OA'          => $row->none_oa ?? null,
                'AE'          => $row->none_ae ?? null,
                'ALD'         => $row->none_ald ?? null,
            ];
            // Encoded by the Secretariat after the evaluator is done, so they
            // are shown but never decide whether the applicant is rated.
            $row->interview = $row->none_interview ?? null;
            $row->written = $row->none_written ?? null;
        }

        $row->rating_components = $components;
        $row->has_rating_row = $hasRatingRow;
        $row->is_rated = $hasRatingRow && !$this->has_stub_score($components);
    }

    /**
     * True when any core component is still the 0.00001 stub (0.0001 on legacy
     * rows) or was never written. A genuine zero is a real score.
     */
    private function has_stub_score(array $components): bool
    {
        $stubs = [0.00001, 0.0001];
        $tolerance = 0.000001;

        foreach ($components as $value) {
            if ($value === null || $value === '') {
                return true;
            }

            foreach ($stubs as $stub) {
                if (abs((float) $value - $stub) <= $tolerance) {
                    return true;
                }
            }
        }

        return false;
    }

    /* ------------------------------------------------------------------ *
     * Issued applicant documents (Evaluative Assessment / non-compliance
     * letter). The evaluator's own review in hris_app_dq stays untouched -
     * it seeds the first draft and remains the audit record. What the
     * Secretariat edits and issues is kept here.
     * ------------------------------------------------------------------ */

    public function ensure_assessment_table(): void
    {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS hris_app_assessment (
                id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                app_id INT UNSIGNED NOT NULL,
                job_id INT UNSIGNED NOT NULL,
                doc_type VARCHAR(30) NOT NULL,
                body LONGTEXT NOT NULL,
                issued_by INT UNSIGNED NULL,
                issued_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                released TINYINT(1) NOT NULL DEFAULT 0,
                released_at DATETIME NULL,
                UNIQUE KEY uniq_app_doc (app_id, doc_type),
                KEY idx_assessment_job (job_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");
    }

    /** Documents this module issues, keyed by the doc_type stored on the row. */
    public function assessment_types(): array
    {
        return [
            'assessment' => 'Evaluative Assessment',
            'letter'     => 'Letter to Applicants Non-Compliant of Documents',
        ];
    }

    /**
     * One application with everything the documents print: applicant, vacancy,
     * school and the latest qualification review. Returns null when the
     * application does not exist.
     */
    public function assessment_context(int $appId)
    {
        if ($appId <= 0) {
            return null;
        }

        $latestDq = $this->db
            ->select('appID, MAX(id) AS latest_id', false)
            ->from('hris_app_dq')
            ->group_by('appID')
            ->get_compiled_select();

        return $this->db
            ->select("a.appID, a.applicant_id, a.jobID, a.empEmail, a.appStatus, a.app_year,
                a.district, a.pre_school, a.dq,
                j.jobTitle, j.job_type, j.position, j.sy, j.itemNo, j.promotion, j.jvStatus,
                COALESCE(ha.record_no, ha2.record_no, hs.IDNumber, a.applicant_id) AS record_no,
                COALESCE(ha.FirstName, ha2.FirstName, hs.FirstName, '') AS FirstName,
                COALESCE(ha.MiddleName, ha2.MiddleName, hs.MiddleName, '') AS MiddleName,
                COALESCE(ha.LastName, ha2.LastName, hs.LastName, '') AS LastName,
                COALESCE(ha.NameExtn, ha2.NameExtn, hs.NameExtn, '') AS NameExtn,
                COALESCE(ha.Sex, ha2.Sex, hs.Sex, '') AS Sex,
                COALESCE(ha.id, ha2.id, a.applicant_id) AS profile_id,
                COALESCE(ha.bd, ha2.bd, hs.bd, '') AS bd,
                COALESCE(ha.csEligibility, ha2.csEligibility, hs.csEligibility, '') AS csEligibility,
                COALESCE(ha.resHouseNo, ha2.resHouseNo, '') AS resHouseNo,
                COALESCE(ha.resStreet, ha2.resStreet, '') AS resStreet,
                COALESCE(ha.resVillage, ha2.resVillage, '') AS resVillage,
                COALESCE(ha.resBarangay, ha2.resBarangay, '') AS resBarangay,
                COALESCE(ha.resCity, ha2.resCity, '') AS resCity,
                COALESCE(ha.resProvince, ha2.resProvince, '') AS resProvince,
                s.schoolName,
                dq.reason AS dq_reason, dq.vdate AS dq_date, dq.remarks AS dq_remarks,
                dq.li, dq.da_pds, dq.prc, dq.trbd, dq.omni, dq.educ, dq.exp, dq.tr, dq.eli", false)
            ->from('hris_applications a')
            ->join('hris_jobvacancy j', 'j.jobID = a.jobID')
            ->join('hris_applicant ha', 'ha.id = a.applicant_id', 'left')
            ->join('hris_applicant ha2', 'ha2.record_no = CONVERT(CAST(a.applicant_id AS CHAR) USING latin1) COLLATE latin1_swedish_ci AND ha.id IS NULL', 'left', false)
            ->join('hris_staff hs', 'CONVERT(hs.IDNumber USING utf8mb4) COLLATE utf8mb4_general_ci = a.empEmail AND ha.id IS NULL AND ha2.id IS NULL', 'left', false)
            ->join('schools s', 's.schoolID = CONVERT(CAST(a.pre_school AS CHAR) USING utf8mb4) COLLATE utf8mb4_unicode_ci', 'left', false)
            ->join("($latestDq) latest_dq", 'latest_dq.appID = a.appID', 'left')
            ->join('hris_app_dq dq', 'dq.id = latest_dq.latest_id', 'left')
            ->where('a.appID', $appId)
            ->limit(1)
            ->get()
            ->row();
    }

    /** Full name as printed on the documents. */
    public function assessment_applicant_name($ctx): string
    {
        $name = trim(preg_replace('/\s+/', ' ', implode(' ', [
            (string) ($ctx->FirstName ?? ''),
            (string) ($ctx->MiddleName ?? ''),
            (string) ($ctx->LastName ?? ''),
            (string) ($ctx->NameExtn ?? ''),
        ])));

        return $name !== '' ? $name : 'Applicant #' . (int) ($ctx->appID ?? 0);
    }

    /**
     * The HRMPSB Chair who signs the issued documents: the Assistant Schools
     * Division Superintendent that maintains an e-signature, same signatory the
     * certificate of rating uses. Falls back to the SDS named in mis_settings.
     */
    public function assessment_signatory(): array
    {
        $this->load->model('Common');
        $this->Common->ensure_columns('users', ['esig' => 'VARCHAR(255) NULL DEFAULT NULL']);

        $signatory = $this->db
            ->from('users')
            ->where('position', 'asst_sds')
            ->where("COALESCE(esig, '') != ''", null, false)
            ->order_by('id', 'DESC')
            ->limit(1)
            ->get()
            ->row();

        if (empty($signatory)) {
            $signatory = $this->db->where('position', 'asst_sds')->limit(1)->get('users')->row();
        }

        if (!empty($signatory)) {
            $mi = trim((string) ($signatory->mname ?? ''));
            $mi = ($mi !== '') ? ' ' . strtoupper(substr($mi, 0, 1)) . '.' : '';

            return [
                'name'  => trim(strtoupper(trim((string) $signatory->fname) . $mi . ' ' . trim((string) $signatory->lname))),
                'title' => 'Assistant Schools Division Superintendent',
                'role'  => 'HRMPSB Chair',
                'esig'  => trim((string) ($signatory->esig ?? '')),
            ];
        }

        $settings = $this->db->where('settingsID', 1)->get('mis_settings')->row();

        return [
            'name'  => strtoupper(trim((string) ($settings->sds ?? ''))),
            'title' => trim((string) ($settings->sdsPosition ?? 'Schools Division Superintendent')),
            'role'  => 'HRMPSB Chair',
            'esig'  => '',
        ];
    }

    /**
     * The applicant's own Education / Experience / Training / Eligibility, read
     * from the same places the Annex D evaluation sheet reads them.
     */
    public function assessment_qualifications($ctx): array
    {
        $applicantId = (int) ($ctx->profile_id ?? 0);

        $education = trim((string) ($ctx->bd ?? ''));
        $eligibility = trim((string) ($ctx->csEligibility ?? ''));
        $training = '';
        $experience = '';

        if ($applicantId > 0) {
            $hours = (float) ($this->db
                ->select('SUM(noHours) AS total', false)
                ->where('stat', 1)
                ->where('IDNumber', $applicantId)
                ->get('hris_trainings')
                ->row()
                ->total ?? 0);

            // Annex E/F is a one-page qualification summary. Listing every
            // seminar title can make one table row several pages tall, while
            // the source workbook asks only for the applicant's qualification.
            $training = $hours > 0
                ? rtrim(rtrim(number_format($hours, 2, '.', ''), '0'), '.') . ' hours of training'
                : '';

            $span = $this->db
                ->select('SUM(ny) AS years, SUM(nm) AS months', false)
                ->where('stat', 1)
                ->where('id_number', $applicantId)
                ->get('hris_experience')
                ->row();

            $years = (int) ($span->years ?? 0) + intdiv((int) ($span->months ?? 0), 12);
            $months = (int) ($span->months ?? 0) % 12;

            if ($years > 0 || $months > 0) {
                $experience = $years . ' year' . ($years === 1 ? '' : 's')
                    . ' and ' . $months . ' month' . ($months === 1 ? '' : 's');
            }
        }

        return [
            'education'   => $education,
            'experience'  => $experience,
            'training'    => $training,
            'eligibility' => $eligibility,
        ];
    }

    /**
     * First draft of a document, following the division's own forms:
     * ANNEX E (qualified) / ANNEX F (disqualified) for the Evaluative
     * Assessment, and the Letter to Applicants Non-Compliant of Documents.
     * Every value here is what the Secretariat sees before editing anything.
     */
    public function assessment_defaults($ctx, string $docType): array
    {
        $settings = $this->db->where('settingsID', 1)->get('mis_settings')->row();
        $division = trim((string) ($settings->division ?? ''));
        $jobTypes = $this->job_types_map();
        $typeLabel = trim((string) ($jobTypes[(int) ($ctx->job_type ?? 0)] ?? ''));
        $jobTitle = trim((string) ($ctx->jobTitle ?? ''));
        $position = trim($jobTitle . ($typeLabel !== '' ? ' - ' . $typeLabel : ''));
        $name = $this->assessment_applicant_name($ctx);
        $isDq = ((int) ($ctx->dq ?? 0) === 2);
        $courtesy = $this->assessment_courtesy($ctx);
        $lastName = ucwords(strtolower(trim((string) ($ctx->LastName ?? ''))));

        // Applicants type NA / N/A / none into address parts they do not have,
        // so those never reach the printed letter.
        $realPart = static function ($part) {
            $part = trim((string) $part);
            return $part !== '' && !in_array(strtoupper(str_replace([' ', '.', '/'], '', $part)), ['NA', 'NONE', '-', 'X'], true);
        };

        // Older applicant rows can contain the checkbox value "1" in the
        // village field. It is not an address component and used to print as
        // a line by itself on Annex E/F.
        $resVillage = trim((string) ($ctx->resVillage ?? ''));
        if ($resVillage === '1') {
            $resVillage = '';
        }

        $address = array_values(array_filter([
            trim(implode(' ', array_filter([
                (string) ($ctx->resHouseNo ?? ''),
                (string) ($ctx->resStreet ?? ''),
                $resVillage,
            ], $realPart))),
            trim(implode(', ', array_filter([
                (string) ($ctx->resBarangay ?? ''),
                (string) ($ctx->resCity ?? ''),
                (string) ($ctx->resProvince ?? ''),
            ], $realPart))),
        ], static function ($line) { return trim($line) !== ''; }));

        // The evaluator writes one reason per line; each line becomes its own
        // row of the requirements table instead of a single paragraph.
        $reasonLines = array_values(array_filter(array_map(
            static function ($line) { return trim($line); },
            preg_split('/\r\n|\r|\n/', (string) ($ctx->dq_reason ?? ''))
        ), static function ($line) { return $line !== ''; }));

        if ($docType === 'letter') {
            $items = [];
            foreach ($reasonLines as $line) {
                $items[] = ['requirement' => $line, 'remarks' => ''];
            }

            if ($items === []) {
                $items[] = ['requirement' => '', 'remarks' => ''];
            }

            $signatory = $this->assessment_signatory();

            return [
                'office'          => 'Office of the Schools Division Superintendent',
                'date'            => date('F j, Y'),
                'applicant'       => strtoupper($name),
                'position_line'   => $jobTitle . ' Applicant',
                'address'         => implode("\n", $address),
                'salutation'      => 'Dear ' . $courtesy . ' ' . $lastName . ',',
                'greeting'        => 'Greetings!',
                'body1'           => 'This pertains to your application for the position of ' . $position
                    . ' at Department of Education, Schools Division of ' . $division . '.',
                'body2'           => 'Please be informed that after thorough checking and verifying the completeness, '
                    . 'authenticity and veracity of the submitted documents to this Division through the Human Resource '
                    . 'Management Office, this is to formally inform you that you have not been included in the pool of '
                    . 'official applicants and cannot proceed to the next stage of the application process due to the '
                    . 'non-compliance with the provision stipulated in DepEd Order No. 007, s. 2023 as regards to the '
                    . 'Submission and Receipt of Application documents specifically on (Items 20.a to j) and Division '
                    . 'Memorandum No. 155, s. 2026 Annex C: Checklist of Requirements, to wit:',
                'items'           => $items,
                'body3'           => 'Further, Section 21 of DepEd Order No. 007, s. 2023 states that individuals who fail '
                    . 'to submit complete mandatory documents (Item 20.a to j) on the set deadline indicated in the '
                    . 'official memorandum shall not be included in the pool of official applicants.',
                'body4'           => 'We truly appreciate your enthusiasm and interest in joining our organization, and we '
                    . 'wish you every success in your future endeavors.',
                'body5'           => 'For more information regarding the result of your application, you may contact the '
                    . 'Division HRMO at cellphone no. 09476063872.',
                'closing'         => 'Very truly yours,',
                'signatory'       => $signatory['name'],
                'signatory_title' => $signatory['title'],
                'signatory_role'  => $signatory['role'],
            ];
        }

        $qualifications = $this->assessment_qualifications($ctx);
        $criteria = [
            'Education:'   => $qualifications['education'],
            'Experience:'  => $qualifications['experience'],
            'Training:'    => $qualifications['training'],
            'Eligibility:' => $qualifications['eligibility'],
        ];

        $items = [];
        foreach ($criteria as $criterion => $value) {
            $items[] = [
                'criterion' => $criterion,
                'qs'        => '',
                'yours'     => $value,
                'remarks'   => '',
            ];
        }

        $signatory = $this->assessment_signatory();
        $evaluationDate = trim((string) ($ctx->dq_date ?? ''));
        $evaluationDate = ($evaluationDate !== '' && strtotime($evaluationDate))
            ? date('F j, Y', strtotime($evaluationDate))
            : date('F j, Y');

        $common = [
            'annex'           => $isDq ? 'ANNEX F' : 'ANNEX E',
            'office'          => 'PERSONNEL DIVISION',
            'date'            => date('F j, Y'),
            'applicant'       => strtoupper($name),
            'address1'        => $address[0] ?? '',
            'address2'        => $address[1] ?? '',
            'salutation'      => 'Dear ' . $courtesy . ' ' . $lastName . ',',
            'item_no'         => trim((string) ($ctx->itemNo ?? '')),
            'items'           => $items,
            'closing'         => 'Very truly yours,',
            'signatory'       => $signatory['name'],
            'signatory_title' => $signatory['title'],
        ];

        if ($isDq) {
            return array_merge($common, [
                'greeting' => '',
                'intro'    => 'Please be informed of the results of the initial evaluation of your qualifications '
                    . 'vis-a-vis the Civil Service Commission (CSC) approved-Qualification Standards (QS) of '
                    . $position . ' position under ' . $division . ', as follows:',
                'body2'    => 'While your qualifications made a favorable impression, we regret to inform you that you '
                    . 'did not meet the minimum QS set for ' . $position . ' position. You may, however, continue to '
                    . 'submit job applications in response to other vacancy announcements that we publish at '
                    . 'www.csc.gov.ph/careers, DepEd bulletin boards, and official website www.depeddavaodeoro.ph.',
                'body3'    => 'The results of the initial evaluation shall be released and posted for transparency '
                    . 'purposes. You may refer to your assigned application code (' . trim((string) ($ctx->record_no ?? '')) . ') '
                    . 'in the official posting of results.',
                'thanks'   => 'Thank you and we wish you the best of luck in your future success.',
            ]);
        }

        return array_merge($common, [
            'greeting' => 'Congratulations!',
            'intro'    => 'We are pleased to inform you that based on the initial evaluation, we have found your '
                . 'qualifications to be substantial vis-a-vis the Civil Service Commission (CSC) approved '
                . 'Qualification Standards (QS) of ' . $position . ' position under ' . $division . '. Below are the '
                . 'results of the initial evaluation conducted by the undersigned dated ' . $evaluationDate . ':',
            'body2'    => 'Please be advised of your assigned application code (' . trim((string) ($ctx->record_no ?? '')) . ') '
                . 'which shall be used as you proceed with the next stage of the selection process. You may refer to '
                . 'the official issuances of the ' . $division . ' for the additional announcements in this regard. '
                . 'For inquiries, you may communicate with the Division HRMO.',
            'body3'    => '',
            'thanks'   => 'Thank you.',
        ]);
    }

    /** Mr. / Ms. for the salutation, from the applicant's recorded sex. */
    private function assessment_courtesy($ctx): string
    {
        $sex = strtolower(trim((string) ($ctx->Sex ?? '')));

        if ($sex === 'male' || $sex === 'm' || $sex === '0') {
            return 'Mr.';
        }

        if ($sex === 'female' || $sex === 'f' || $sex === '1') {
            return 'Ms.';
        }

        return 'Mr./Ms.';
    }

    /** The stored row for one document, or null when nothing was issued yet. */
    public function assessment_row(int $appId, string $docType)
    {
        return $this->db
            ->where('app_id', $appId)
            ->where('doc_type', $docType)
            ->limit(1)
            ->get('hris_app_assessment')
            ->row();
    }

    /**
     * The document as it should be shown: the saved version when there is one,
     * otherwise a fresh draft. Missing keys on an older saved row fall back to
     * the draft so a document never renders with holes in it.
     */
    public function assessment_document($ctx, string $docType): array
    {
        $defaults = $this->assessment_defaults($ctx, $docType);
        $row = $this->assessment_row((int) $ctx->appID, $docType);
        $doc = $defaults;
        $saved = false;

        if (!empty($row)) {
            $stored = json_decode((string) $row->body, true);
            if (is_array($stored)) {
                $doc = array_merge($defaults, $stored);

                // Clean the same legacy village flag from documents saved
                // before the address defaults were corrected. This changes
                // only a standalone line equal to "1"; real house numbers
                // and other address text are left intact.
                if (trim((string) ($ctx->resVillage ?? '')) === '1') {
                    if ($docType === 'assessment' && trim((string) ($doc['address1'] ?? '')) === '1') {
                        $doc['address1'] = '';
                    }

                    if ($docType === 'letter') {
                        $lines = preg_split('/\r\n|\r|\n/', (string) ($doc['address'] ?? ''));
                        $lines = array_values(array_filter($lines, static function ($line) {
                            return trim((string) $line) !== '1';
                        }));
                        $doc['address'] = implode("\n", $lines);
                    }
                }

                $saved = true;
            }
        }

        return [
            'doc'         => $doc,
            'saved'       => $saved,
            'released'    => !empty($row) && (int) $row->released === 1,
            'updated_at'  => $row->updated_at ?? null,
            'released_at' => $row->released_at ?? null,
        ];
    }

    /**
     * Save the edited document. Only the keys the draft defines are kept, and
     * every value is stored as plain text, so nothing a browser sends can come
     * back out as markup.
     */
    public function save_assessment(int $appId, int $jobId, string $docType, array $body, int $userId): bool
    {
        $existing = $this->assessment_row($appId, $docType);
        $payload = json_encode($body, JSON_UNESCAPED_UNICODE);

        if ($payload === false) {
            return false;
        }

        if (!empty($existing)) {
            return (bool) $this->db
                ->where('id', (int) $existing->id)
                ->update('hris_app_assessment', ['body' => $payload, 'issued_by' => $userId]);
        }

        return (bool) $this->db->insert('hris_app_assessment', [
            'app_id'    => $appId,
            'job_id'    => $jobId,
            'doc_type'  => $docType,
            'body'      => $payload,
            'issued_by' => $userId,
        ]);
    }

    /**
     * Release the document to the applicant, or take it back. A document that
     * was never saved is written first, so releasing always publishes something.
     */
    public function set_assessment_release(int $appId, string $docType, bool $released): bool
    {
        $existing = $this->assessment_row($appId, $docType);

        if (empty($existing)) {
            return false;
        }

        return (bool) $this->db
            ->where('id', (int) $existing->id)
            ->update('hris_app_assessment', [
                'released'    => $released ? 1 : 0,
                'released_at' => $released ? date('Y-m-d H:i:s') : null,
            ]);
    }

    /**
     * doc_type => released map per application, for the lists and for the
     * applicant's own Manage column. One query for the whole page.
     */
    public function issued_documents(array $appIds): array
    {
        $appIds = array_values(array_filter(array_map('intval', $appIds)));

        if (empty($appIds)) {
            return [];
        }

        $map = [];
        foreach ($this->db
            ->select('app_id, doc_type, released, updated_at')
            ->where_in('app_id', $appIds)
            ->get('hris_app_assessment')
            ->result() as $row) {
            $map[(int) $row->app_id][(string) $row->doc_type] = [
                'released'   => (int) $row->released === 1,
                'updated_at' => $row->updated_at,
            ];
        }

        return $map;
    }
}
