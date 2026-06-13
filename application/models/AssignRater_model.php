<?php
defined('BASEPATH') or exit('No direct script access allowed');

class AssignRater_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    private function assigned_app_ids(int $fy): array
    {
        $rows = $this->db
            ->select('app_id')
            ->from('hris_rater_assignments')
            ->where('fy', $fy)
            ->get()
            ->result();

        $ids = [];
        foreach ($rows as $row) {
            if (!empty($row->app_id)) {
                $ids[] = (int) $row->app_id;
            }
        }
        return $ids;
    }

    public function get_level_summary(int $fy): array
    {
        $excluded = $this->assigned_app_ids($fy);

        $this->db
            ->select('jv.job_type, COUNT(*) AS available', false)
            ->from('hris_applications app')
            ->join('hris_jobvacancy jv', 'jv.jobID = app.jobID', 'left')
            ->where('app.appStatus', 'Endorsed for Rating')
            ->where('app.app_year', $fy)
            ->group_by('jv.job_type')
            ->order_by('jv.job_type', 'asc');

        if (!empty($excluded)) {
            $this->db->where_not_in('app.appID', $excluded);
        }

        $available = $this->db->get()->result();

        $assigned = $this->db
            ->select('job_type, COUNT(*) AS total', false)
            ->from('hris_rater_assignments')
            ->where('fy', $fy)
            ->group_by('job_type')
            ->get()
            ->result();

        $assignedMap = [];
        foreach ($assigned as $row) {
            $assignedMap[(int)$row->job_type] = (int)$row->total;
        }

        $levels = [];
        foreach ($available as $row) {
            $jobType = (int) $row->job_type;
            $levels[$jobType] = [
                'job_type' => $jobType,
                'available' => (int) $row->available,
                'assigned' => $assignedMap[$jobType] ?? 0,
            ];
        }

        // Ensure we still show levels that already have assignments but no remaining available.
        foreach ($assignedMap as $jobType => $count) {
            if (!isset($levels[$jobType])) {
                $levels[$jobType] = [
                    'job_type' => $jobType,
                    'available' => 0,
                    'assigned' => $count,
                ];
            }
        }

        ksort($levels);
        return $levels;
    }

    public function get_specialization_summary(int $fy, array $jobTypes = [4]): array
    {
        $excluded = $this->assigned_app_ids($fy);

        $this->db
            ->select('COALESCE(ha.specialization, "") AS specialization, jv.job_type, COUNT(*) AS available', false)
            ->from('hris_applications app')
            ->join('hris_jobvacancy jv', 'jv.jobID = app.jobID', 'left')
            ->join('hris_applicant ha', 'ha.id = app.applicant_id OR ha.record_no = app.applicant_id', 'left')
            ->where('app.appStatus', 'Endorsed for Rating')
            ->where('app.app_year', $fy)
            ->where_in('jv.job_type', $jobTypes)
            ->group_by('jv.job_type')
            ->group_by('COALESCE(ha.specialization, "")', false)
            ->order_by('COALESCE(ha.specialization, "")', 'asc', false);

        if (!empty($excluded)) {
            $this->db->where_not_in('app.appID', $excluded);
        }

        $rows = $this->db->get()->result();

        $assigned = $this->db
            ->select('job_type, specialization, COUNT(*) AS total', false)
            ->from('hris_rater_assignments')
            ->where('fy', $fy)
            ->where_in('job_type', $jobTypes)
            ->group_by(['job_type', 'specialization'])
            ->get()
            ->result();

        $assignedMap = [];
        foreach ($assigned as $row) {
            $key = $this->spec_key((int)$row->job_type, (string)$row->specialization);
            $assignedMap[$key] = (int)$row->total;
        }

        $list = [];
        foreach ($rows as $row) {
            $jobType = (int) $row->job_type;
            $spec = $row->specialization ?? '';
            $key = $this->spec_key($jobType, $spec);
            $list[] = [
                'job_type' => $jobType,
                'specialization' => $spec,
                'available' => (int) $row->available,
                'assigned' => $assignedMap[$key] ?? 0,
            ];
        }

        // add specs that only exist in assigned side
        foreach ($assignedMap as $key => $count) {
            [$jobType, $spec] = explode(':', $key, 2);
            $jobType = (int)$jobType;
            $found = false;
            foreach ($list as $row) {
                if ($row['job_type'] === $jobType && $row['specialization'] === $spec) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $list[] = [
                    'job_type' => $jobType,
                    'specialization' => $spec,
                    'available' => 0,
                    'assigned' => $count,
                ];
            }
        }

        return $list;
    }

    private function spec_key(int $jobType, string $spec): string
    {
        return $jobType . ':' . $spec;
    }

    public function get_raters(): array
    {
        return $this->db
            ->select('id, fname, mname, lname, position, egroup')
            ->from('users')
            ->where('position', 'Evaluator')
            ->where('egroup', 1)
            ->order_by('lname', 'asc')
            ->order_by('fname', 'asc')
            ->get()
            ->result();
    }

    public function rater_is_valid(int $userId): bool
    {
        return (bool) $this->db
            ->from('users')
            ->where('id', $userId)
            ->where('position', 'Evaluator')
            ->where('egroup', 1)
            ->count_all_results();
    }

    public function get_rater_load(int $fy): array
    {
        return $this->db
            ->select('u.id, u.fname, u.mname, u.lname, u.username, COUNT(ra.id) AS assigned_total', false)
            ->from('users u')
            ->where('u.position', 'Evaluator')
            ->where('u.egroup', 1)
            ->join('hris_rater_assignments ra', 'ra.rater_user_id = u.id AND ra.fy = '.$this->db->escape($fy), 'left', false)
            ->group_by(['u.id', 'u.fname', 'u.mname', 'u.lname', 'u.username'])
            ->order_by('u.lname', 'asc')
            ->order_by('u.fname', 'asc')
            ->get()
            ->result();
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
        ];
    }

  public function get_assigned_applicants(int $raterId): array
{
    // One row per appID, but keep full rating visibility
    $rarSub = $this->db
        ->select('
            appID,
            MAX(education)    AS education,
            MAX(training)     AS training,
            MAX(experience)   AS experience,
            MAX(let_rating)   AS let_rating,
            MAX(demo_rating)  AS demo_rating,
            MAX(tr_rating)    AS tr_rating,
            MAX(total_points) AS total_points,
            COUNT(*)          AS rating_rows
        ', false)
        ->from('hris_applications_rating')
        ->group_by('appID')
        ->get_compiled_select();

    $rows = $this->db
        ->select("
            ra.*,
            app.appID, app.appStatus, app.pre_school, app.app_year, app.applicant_id, app.jobID,
            jv.job_type, jv.jobTitle,
            COALESCE(ha_id.record_no, ha_rec.record_no) AS record_no,
            COALESCE(ha_id.FirstName,  ha_rec.FirstName)  AS FirstName,
            COALESCE(ha_id.LastName,   ha_rec.LastName)   AS LastName,
            COALESCE(ha_id.MiddleName, ha_rec.MiddleName) AS MiddleName,
            COALESCE(ha_id.specialization, ha_rec.specialization) AS specialization,
            rar.education,
            rar.training,
            rar.experience,
            rar.let_rating,
            rar.demo_rating,
            rar.tr_rating,
            rar.total_points,
            rar.rating_rows
        ", false)
    ->from('hris_rater_assignments ra')
    ->join('hris_applications app', 'app.appID = ra.app_id', 'left')
    // join job vacancy but exclude assignments where the vacancy is explicitly Closed
    // keep LEFT JOIN so if the job row is missing we still show the assignment
    ->join('hris_jobvacancy jv', 'jv.jobID = ra.job_id', 'left')
        ->join('hris_applicant ha_id', 'ha_id.id = app.applicant_id', 'left')
        ->join('hris_applicant ha_rec', 'ha_rec.record_no = app.applicant_id AND ha_id.id IS NULL', 'left')
        ->join("($rarSub) rar", 'rar.appID = ra.app_id', 'left')
        ->where('ra.rater_user_id', $raterId)
        // Do not return assignments for vacancies that are closed.
        // jvStatus may be NULL when the job record is missing; allow those through.
        ->where(' (jv.jvStatus IS NULL OR jv.jvStatus != ' . $this->db->escape('Closed') . ') ', null, false)
        ->group_by('ra.app_id')
        ->order_by('MAX(ra.assigned_at)', 'desc')
        ->get()
        ->result();

    $pending = [];
    $scored  = [];
    $counts  = ['total' => count($rows), 'pending' => 0, 'scored' => 0];

    // Treat placeholder scores (used as initial stub values) as "no rating".
    // The user requested the cutoff to be 0.0001 and to ignore demo/TR ratings
    // when deciding whether an applicant already has scores.
    $stubPlaceholders = [0.0001, 0.00001];
    $tolerance = 0.000001; // small epsilon to handle float comparisons

    foreach ($rows as $row) {
        $hasRatingRow = ((int)($row->rating_rows ?? 0) > 0);

        // Missing rating row = definitely pending
        if (!$hasRatingRow) {
            $pending[] = $row;
            $counts['pending']++;
            continue;
        }

        // Only consider education, training, experience, and LET rating for
        // pending/with-score classification. Demo and TR ratings stay excluded
        // per earlier request.
        $values = [
            (float)($row->education   ?? 0),
            (float)($row->training    ?? 0),
            (float)($row->experience  ?? 0),
            (float)($row->let_rating  ?? 0),
        ];

        // The user wants an applicant kept in "Pending" if ANY of the four core
        // components is still sitting at the stub placeholder (0.00001; keep
        // 0.0001 for backward compatibility). Zero is treated as a real score.
        $hasStubValue = false;
        foreach ($values as $v) {
            $vFloat = (float) $v;

            foreach ($stubPlaceholders as $stub) {
                if (abs($vFloat - $stub) <= $tolerance) {
                    $hasStubValue = true;
                    break 2; // no need to keep checking
                }
            }
        }

        // Classification: if any core rating is still stub, stay in Pending;
        // otherwise move to With Scores.
        if ($hasStubValue) {
            $pending[] = $row;
            $counts['pending']++;
        } else {
            $scored[] = $row;
            $counts['scored']++;
        }
    }

    return compact('pending', 'scored', 'counts');
}

    public function get_recent_assignments(int $fy, int $limit = 20): array
    {
        return $this->db
            ->select('
                ra.id,
                ra.assigned_at,
                ra.job_type,
                ra.specialization,
                CONCAT(u.lname, ", ", u.fname, " ", IFNULL(u.mname, "")) AS rater_name,
                rr.applicant_id,
                rr.app_id,
                jv.jobTitle
            ', false)
            ->from('hris_rater_assignments ra')
            ->join('users u', 'u.id = ra.rater_user_id', 'left')
            ->join('hris_rating_request rr', 'rr.app_id = ra.app_id', 'left')
            ->join('hris_jobvacancy jv', 'jv.jobID = ra.job_id', 'left')
            ->where('ra.fy', $fy)
            ->order_by('ra.assigned_at', 'desc')
            ->limit($limit)
            ->get()
            ->result();
    }

    public function assign_requests(int $fy, int $jobType, int $raterId, int $count, ?string $specialization, ?int $assignedBy): int
    {
        $count = max(1, $count);
        $excluded = $this->assigned_app_ids($fy);
        $pending = $this->get_pending_requests($fy, $jobType, $specialization, $count, $excluded);

        if (empty($pending)) {
            return 0;
        }

        $now = date('Y-m-d H:i:s');
        $this->db->trans_start();

        foreach ($pending as $row) {
            $this->db->insert('hris_rater_assignments', [
                'fy' => $fy,
                'applicant_id' => $row->applicant_id,
                'app_id' => $row->appID,
                'job_id' => $row->job_id,
                'job_type' => $row->job_type,
                'specialization' => $row->specialization,
                'rater_user_id' => $raterId,
                'assigned_by' => $assignedBy,
                'assigned_at' => $now,
            ]);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            return 0;
        }

        return count($pending);
    }

    private function get_pending_requests(int $fy, int $jobType, ?string $specialization, int $limit, array $excluded): array
    {
        $this->db
            ->select('app.appID, app.applicant_id, app.jobID AS job_id, jv.job_type, COALESCE(ha.specialization, "") AS specialization')
            ->from('hris_applications app')
            ->join('hris_jobvacancy jv', 'jv.jobID = app.jobID', 'left')
            ->join('hris_applicant ha', 'ha.id = app.applicant_id OR ha.record_no = app.applicant_id', 'left')
            ->where('app.appStatus', 'Endorsed for Rating')
            ->where('app.app_year', $fy)
            ->where('jv.job_type', $jobType)
            ->order_by('app.appID', 'asc')
            ->limit($limit);

        if ($specialization !== null && $specialization !== '') {
            $this->db->where('ha.specialization', $specialization);
        }

        if (!empty($excluded)) {
            $this->db->where_not_in('app.appID', $excluded);
        }

        return $this->db->get()->result();
    }
}
