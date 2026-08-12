<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Secretariat_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->ensure_table();
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
     * Accepts either the new "positionGroup:jobType" scope keys / assoc rows or
     * the legacy flat job_type list, so existing callers keep working unchanged.
     */
    public function normalize_scopes(array $input): array
    {
        $out = [];

        foreach ($input as $item) {
            if (is_array($item)) {
                $out[] = [
                    'position_group' => isset($item['position_group']) ? (int) $item['position_group'] : null,
                    'job_type'       => (int) ($item['job_type'] ?? 0),
                ];
            } elseif (is_object($item)) {
                $out[] = [
                    'position_group' => isset($item->position_group) ? (int) $item->position_group : null,
                    'job_type'       => (int) ($item->job_type ?? 0),
                ];
            } elseif (is_string($item) && strpos($item, ':') !== false) {
                [$group, $type] = explode(':', $item, 2);
                $out[] = ['position_group' => (int) $group, 'job_type' => (int) $type];
            } else {
                // legacy: job_type with no position group, matched across all groups
                $out[] = ['position_group' => null, 'job_type' => (int) $item];
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
    public function scope_where_sql(array $scopes, string $positionCol = 'jv.position', string $jobTypeCol = 'jv.job_type'): string
    {
        $scopes = $this->normalize_scopes($scopes);
        if (empty($scopes)) {
            return '';
        }

        $byGroup = [];
        $legacy  = [];
        foreach ($scopes as $scope) {
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

        return '(' . implode(' OR ', $parts) . ')';
    }

    /**
     * Applies the assigned scopes to the pending query on the joined vacancy.
     */
    private function apply_scopes(array $scopes, string $positionCol = 'jv.position', string $jobTypeCol = 'jv.job_type'): void
    {
        $sql = $this->scope_where_sql($scopes, $positionCol, $jobTypeCol);
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
     * map[userId] => ['1:3', '4:0', ...] scope keys, ready for the assign screen.
     */
    public function assignments_indexed(): array
    {
        $rows = $this->db
            ->select('secretariat_user_id, position_group, job_type')
            ->from('hris_secretariat_levels')
            ->order_by('secretariat_user_id', 'asc')
            ->order_by('position_group', 'asc')
            ->order_by('job_type', 'asc')
            ->get()
            ->result();

        $map = [];
        foreach ($rows as $row) {
            $uid = (int) $row->secretariat_user_id;
            $map[$uid][] = (int) $row->position_group . ':' . (int) $row->job_type;
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
     * The assigned scopes as ['position_group' => int, 'job_type' => int] rows.
     */
    public function user_scopes(int $userId): array
    {
        $rows = $this->db
            ->select('position_group, job_type')
            ->from('hris_secretariat_levels')
            ->where('secretariat_user_id', $userId)
            ->order_by('position_group', 'asc')
            ->order_by('job_type', 'asc')
            ->get()
            ->result();

        $out = [];
        foreach ($rows as $row) {
            $out[] = [
                'position_group' => (int) $row->position_group,
                'job_type'       => (int) $row->job_type,
            ];
        }
        return $out;
    }

    public function user_scope_labels(int $userId): array
    {
        $labels = [];
        foreach ($this->user_scopes($userId) as $scope) {
            $labels[] = $this->scope_label($scope['position_group'], $scope['job_type']);
        }
        return $labels;
    }

    /**
     * @param array $scopes "positionGroup:jobType" keys, or a legacy job_type list
     *                      which is stored against the Teaching group.
     */
    public function save_assignments(int $userId, array $scopes, ?int $createdBy = null): void
    {
        $catalog = $this->scope_catalog();
        $rows    = [];

        foreach ($this->normalize_scopes($scopes) as $scope) {
            $group = $scope['position_group'] ?? 1;
            $type  = $scope['job_type'];

            // only persist scopes the posting form can actually produce
            if (!isset($catalog[$group]) || !in_array($type, $catalog[$group]['job_types'], true)) {
                continue;
            }
            $rows[$group . ':' . $type] = [
                'secretariat_user_id' => $userId,
                'position_group'      => $group,
                'job_type'            => $type,
                'created_by'          => $createdBy,
            ];
        }

        $this->db->trans_start();
        $this->db->where('secretariat_user_id', $userId)->delete('hris_secretariat_levels');

        foreach ($rows as $row) {
            $this->db->insert('hris_secretariat_levels', $row);
        }
        $this->db->trans_complete();
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
