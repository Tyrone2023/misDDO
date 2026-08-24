<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Secretariat_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->ensure_table();
        $this->ensure_vacancy_table();
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
            $data['eval_id1'] = $userId;

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
        }
        if ($written !== null) {
            $updates['written'] = $written;
        }
        if ((int) ($rating->eval_id1 ?? 0) === 0) {
            $updates['eval_id1'] = $userId;
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

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return ['ok' => false, 'message' => 'The scores could not be saved. Please try again.'];
        }

        $this->db->trans_commit();

        return [
            'ok' => true,
            'application' => $application,
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
                CONCAT_WS(' ', NULLIF(TRIM(u.fname), ''), NULLIF(TRIM(u.lname), '')) AS resolved_by", false)
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
}
