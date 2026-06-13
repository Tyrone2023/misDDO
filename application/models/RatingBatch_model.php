<?php
defined('BASEPATH') or exit('No direct script access allowed');

class RatingBatch_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Extract the starting year (int) from a school year string (e.g., "2024-2025").
     * Returns null when parsing is not possible.
     */
    private function sy_start_year($sy)
    {
        if (is_numeric($sy)) {
            return (int) $sy;
        }

        if (preg_match('/(\\d{4})/', (string) $sy, $m)) {
            return (int) $m[1];
        }

        return null;
    }

    /**
     * List available fiscal years from rating requests (most recent first).
     */
    public function get_request_years(): array
    {
        $rows = $this->db
            ->select('DISTINCT fy', false)
            ->from('hris_rating_request')
            ->order_by('fy', 'desc')
            ->limit(12)
            ->get()
            ->result();

        $years = [];
        foreach ($rows as $row) {
            $year = isset($row->fy) && is_numeric($row->fy) ? (int) $row->fy : null;
            if ($year) {
                $years[] = $year;
            }
        }

        if (empty($years)) {
            $years[] = (int) date('Y');
        }

        return array_values(array_unique($years));
    }

    /**
     * Build level buttons dynamically from current pending requests (stat=0) for the given FY.
     * Each button represents the exact job_type present in pending rows so processing matches the current applications.
     */
    public function get_level_buttons(int $fy): array
    {
        $jobTypes = [
            1 => '- Elementary',
            2 => '- Secondary',
            3 => '- Junior High School',
            4 => '- Senior High School',
            5 => '- Kindergarten',
            6 => '- IPED Elementary',
            7 => '- IPED Secondary',
            8 => '- IPED Junior High School',
            9 => '- IPED Senior High School',
            10 => '- SNED',
        ];

        // Fetch distinct job types among pending requests for this fiscal year.
        $types = $this->db
            ->select('jv.job_type', false)
            ->distinct()
            ->from('hris_rating_request rr')
            ->join('hris_jobvacancy jv', 'jv.jobID = rr.job_id', 'left')
            ->where('rr.fy', $fy)
            ->where('rr.stat', 0)
            ->get()
            ->result();

        $buttons = [];
        $palette = ['btn-success','btn-info','btn-primary','btn-warning','btn-dark','btn-secondary'];
        $pi = 0;

        foreach ($types as $row) {
            $type = (int)$row->job_type;
            $label = isset($jobTypes[$type]) ? $jobTypes[$type] : 'Job Type '.$type;
            $buttons[$type] = [
                'job_type' => $type,
                'label' => 'Accept all '.$label,
                'types' => [$type],
                'class' => $palette[$pi % count($palette)],
            ];
            $pi++;
        }

        return $buttons;
    }

    /**
     * Approved retention requests (stat=1) whose retained components remain placeholders.
     * - r_type=1 (All Criteria): any rating field at 0/0.00001 is flagged.
     * - r_type=2 (Demo & TR): LET / Demo / TR at 0/0.00001 is flagged.
     */
    public function get_retention_zero_scores(int $fy)
    {
        $placeholders = [0, 0.00001, 0.0001];

        return $this->db
            ->select("
                rr.*,
                jv.jobTitle,
                jv.job_type,
                jv.sy,
                app.appStatus,
                rar.education,
                rar.training,
                rar.experience,
                rar.let_rating,
                rar.demo_rating,
                rar.tr_rating,
                rar.total_points,
                rar.fy AS rating_fy,
                COALESCE(a.record_no, s.IDNumber) AS record_no,
                COALESCE(a.LastName, s.LastName) AS last_name,
                COALESCE(a.FirstName, s.FirstName) AS first_name,
                COALESCE(a.MiddleName, s.MiddleName) AS middle_name
            ", false)
            ->from('hris_rating_request rr')
            ->join('hris_jobvacancy jv', 'jv.jobID = rr.job_id', 'left')
            ->join('hris_applications app', 'app.appID = rr.app_id', 'left')
            ->join('hris_applications_rating rar', 'rar.appID = rr.app_id', 'left')
            ->join('hris_applicant a', 'a.id = rr.applicant_id OR a.record_no = rr.applicant_id', 'left')
            ->join('hris_staff s', 's.IDNumber = rr.applicant_id', 'left')
            ->where('rr.fy', $fy)
            ->where('rr.stat', 1) // approved only
            ->group_start()
                // Missing rating row altogether
                ->where('rar.appID IS NULL', null, false)
                // Or placeholder values depending on request type
                ->or_group_start()
                    // All-criteria retention: include only if ALL components are placeholder
                    ->group_start()
                        ->where('rr.r_type', 1)
                        ->where_in('rar.education', $placeholders)
                        ->where_in('rar.training', $placeholders)
                        ->where_in('rar.experience', $placeholders)
                        ->where_in('rar.let_rating', $placeholders)
                        ->where_in('rar.demo_rating', $placeholders)
                        ->where_in('rar.tr_rating', $placeholders)
                        ->where_in('rar.total_points', $placeholders)
                    ->group_end()
                    // Demo/TR retention: include only if BOTH demo and TR are placeholder
                    ->or_group_start()
                        ->where('rr.r_type', 2)
                        ->where_in('rar.demo_rating', $placeholders)
                        ->where_in('rar.tr_rating', $placeholders)
                    ->group_end()
                ->group_end()
            ->group_end()
            ->order_by('rr.rdate', 'desc')
            ->order_by('rr.id', 'desc')
            ->get()
            ->result();
    }

    /**
     * Replace placeholder ratings (0 / 0.00001) for approved retention requests in a fiscal year
     * by copying the latest valid rating for the same applicant & job type.
     */
    public function fill_placeholder_ratings(int $fy, ?int $sourceFy = null, $userId = null): array
    {
        $rows = $this->get_retention_zero_scores($fy);
        $placeholders = [0, 0.00001, 0.0001];

        $summary = [
            'checked' => count($rows),
            'updated' => 0,
            'skipped_no_profile' => 0,
            'skipped_no_source_rating' => 0,
            'skipped_no_job_type' => 0,
            'updated_existing' => 0,
            'created_missing' => 0,
            'created_stub' => 0,
        ];

        foreach ($rows as $row) {
            if (empty($row->job_type)) {
                $summary['skipped_no_job_type']++;
                continue;
            }

            $resolved = $this->resolve_profile($row->applicant_id);
            if (!$resolved) {
                $summary['skipped_no_profile']++;
                continue;
            }

            $idCandidates = array_unique([
                $row->applicant_id,
                $resolved['record_no'],
                $resolved['id'],
            ]);

            // Find the most recent non-placeholder rating for the same level
            $candidates = $this->db
                ->select('rar.*, app.appID, jv.job_type, jv.sy')
                ->from('hris_applications_rating rar')
                ->join('hris_applications app', 'app.appID = rar.appID', 'left')
                ->join('hris_jobvacancy jv', 'jv.jobID = app.jobID', 'left')
                ->group_start()
                    ->where_in('rar.record_no', $idCandidates)
                    ->or_where_in('app.applicant_id', $idCandidates)
                ->group_end()
                ->where('rar.appID !=', $row->app_id)
                ->where('jv.job_type', $row->job_type)
                ->order_by('jv.sy', 'desc')
                ->order_by('rar.fy', 'desc')
                ->order_by('rar.appID', 'desc');

            if ($sourceFy) {
                $candidates = $candidates->where('rar.fy', $sourceFy);
            }

            $candidates = $candidates->get()->result();

            // If a specific source FY was requested but nothing found, fall back to any FY
            if ($sourceFy && empty($candidates)) {
                $candidates = $this->db
                    ->select('rar.*, app.appID, jv.job_type, jv.sy')
                    ->from('hris_applications_rating rar')
                    ->join('hris_applications app', 'app.appID = rar.appID', 'left')
                    ->join('hris_jobvacancy jv', 'jv.jobID = app.jobID', 'left')
                    ->group_start()
                        ->where_in('rar.record_no', $idCandidates)
                        ->or_where_in('app.applicant_id', $idCandidates)
                    ->group_end()
                    ->where('rar.appID !=', $row->app_id)
                    ->where('jv.job_type', $row->job_type)
                    ->order_by('jv.sy', 'desc')
                    ->order_by('rar.fy', 'desc')
                    ->order_by('rar.appID', 'desc')
                    ->get()
                    ->result();
            }

            $source = null;
            foreach ($candidates as $cand) {
                $demoValid = !in_array((float)($cand->demo_rating ?? 0), $placeholders, true);
                $trValid   = !in_array((float)($cand->tr_rating ?? 0), $placeholders, true);
                $totalValid = !in_array((float)($cand->total_points ?? 0), $placeholders, true);

                if ((int)$row->r_type === 2) {
                    if ($demoValid && $trValid) {
                        $source = $cand;
                        break;
                    }
                } else {
                    if ($totalValid) {
                        $source = $cand;
                        break;
                    }
                }
            }

            if (!$source) {
                // Create a stub rating row so the applicant has data in the target FY
                $payload = [
                    'record_no'    => $resolved['record_no'],
                    'appID'        => $row->app_id,
                    'education'    => 0.00001,
                    'training'     => 0.00001,
                    'experience'   => 0.00001,
                    'let_rating'   => 0.00001,
                    'demo_rating'  => 0.00001,
                    'tr_rating'    => 0.00001,
                    'total_points' => 0.00001,
                    'eval_id1'     => 0,
                    'eval_id2'     => 0,
                    'eval_id3'     => 0,
                    'job_type'     => $row->job_type,
                    'fy'           => $fy,
                ];

                $existing = $this->db->get_where('hris_applications_rating', ['appID' => $row->app_id])->row();
                if ($existing) {
                    // Already had a row; treat as update
                    $this->db->where('appID', $row->app_id)->update('hris_applications_rating', $payload);
                    $summary['updated_existing']++;
                } else {
                    $this->db->insert('hris_applications_rating', $payload);
                    $summary['created_missing']++;
                    $summary['created_stub']++;
                }

                $status = ((int)$row->r_type === 1) ? 'Rated' : 'Endorsed for Rating';
                $this->db->where('appID', $row->app_id)->update('hris_applications', [
                    'appStatus' => $status,
                    'dq' => 1,
                ]);

                if ($userId) {
                    $this->db->where('id', $row->id)->update('hris_rating_request', [
                        'res' => $userId,
                        'stat' => 1,
                    ]);
                }

                $summary['updated']++;
                $summary['skipped_no_source_rating']++;
                continue;
            }

            $payload = [
                'record_no'    => $resolved['record_no'],
                'appID'        => $row->app_id,
                'education'    => ((int)$row->r_type === 1) ? $source->education   : 0.00001,
                'training'     => ((int)$row->r_type === 1) ? $source->training    : 0.00001,
                'experience'   => ((int)$row->r_type === 1) ? $source->experience  : 0.00001,
                'let_rating'   => $source->let_rating,
                'demo_rating'  => $source->demo_rating,
                'tr_rating'    => $source->tr_rating,
                'total_points' => $source->total_points,
                'eval_id1'     => ((int)$row->r_type === 1) ? $source->eval_id1    : 0,
                'eval_id2'     => $source->eval_id2,
                'eval_id3'     => $source->eval_id3,
                'job_type'     => $row->job_type,
                'fy'           => $fy,
            ];

            $existing = $this->db->get_where('hris_applications_rating', ['appID' => $row->app_id])->row();

            if ($existing) {
                $this->db->where('appID', $row->app_id)->update('hris_applications_rating', $payload);
                $summary['updated_existing']++;
            } else {
                $this->db->insert('hris_applications_rating', $payload);
                $summary['created_missing']++;
            }

            // keep request + application status tidy
            $status = ((int)$row->r_type === 1) ? 'Rated' : 'Endorsed for Rating';
            $this->db->where('appID', $row->app_id)->update('hris_applications', [
                'appStatus' => $status,
                'dq' => 1,
            ]);

            if ($userId) {
                $this->db->where('id', $row->id)->update('hris_rating_request', [
                    'res' => $userId,
                    'stat' => 1,
                ]);
            }

            $summary['updated']++;
        }

        return $summary;
    }

    public function get_requests_by_status(int $fy, int $stat)
    {
        return $this->db
            ->from('hris_rating_request rr')
            ->join('hris_jobvacancy jv', 'jv.jobID = rr.job_id', 'left')
            ->where('rr.fy', $fy)
            ->where('rr.stat', $stat)
            ->order_by('rr.rdate', 'desc')
            ->get()
            ->result();
    }

    /**
     * Pending requests enriched with applicant name, current application, and history.
     */
    public function get_pending_with_history(int $fy)
    {
        $pending = $this->db
            ->select("
                rr.*,
                jv.jobTitle,
                jv.job_type,
                jv.sy,
                app.appStatus AS current_status,
                app.appID AS current_app_id,
                COALESCE(a.record_no, s.IDNumber) AS record_no,
                COALESCE(a.LastName, s.LastName) AS last_name,
                COALESCE(a.FirstName, s.FirstName) AS first_name,
                COALESCE(a.MiddleName, s.MiddleName) AS middle_name
            ", false)
            ->from('hris_rating_request rr')
            ->join('hris_jobvacancy jv', 'jv.jobID = rr.job_id', 'left')
            ->join('hris_applications app', 'app.appID = rr.app_id', 'left')
            ->join('hris_applicant a', 'a.id = rr.applicant_id OR a.record_no = rr.applicant_id', 'left')
            ->join('hris_staff s', 's.IDNumber = rr.applicant_id', 'left')
            ->where('rr.fy', $fy)
            ->where('rr.stat', 0)
            ->order_by('rr.rdate', 'desc')
            ->get()
            ->result();

        if (empty($pending)) {
            return [];
        }

        $ids = array_unique(array_map(function ($row) {
            return $row->applicant_id;
        }, $pending));

        $history = $this->db
            ->select('app.appID, app.applicant_id, app.appStatus, jv.jobTitle, jv.job_type, jv.sy')
            ->from('hris_applications app')
            ->join('hris_jobvacancy jv', 'jv.jobID = app.jobID', 'left')
            ->where_in('app.applicant_id', $ids)
            ->order_by('jv.sy', 'desc')
            ->order_by('app.appID', 'desc')
            ->get()
            ->result();

        $targetYear = date('Y') - 1; // show only previous year (e.g., 2025 when current is 2026)

        $historyMap = [];
        foreach ($history as $item) {
            $syStart = $this->sy_start_year($item->sy);
            if ($syStart !== $targetYear) {
                continue;
            }
            $historyMap[$item->applicant_id][] = $item;
        }

        foreach ($pending as $row) {
            $rowsHistory = $historyMap[$row->applicant_id] ?? [];
            // Exclude the current application if it falls in the same year bucket
            $row->history = array_values(array_filter($rowsHistory, function($h) use ($row) {
                return empty($row->current_app_id) || $h->appID != $row->current_app_id;
            }));
        }

        return $pending;
    }

    /**
     * Try multiple identifiers to resolve applicant/staff and return record_no used in ratings.
     */
    private function resolve_profile($applicantId)
    {
        // applicant table: id match
        $applicant = $this->db->get_where('hris_applicant', ['id' => $applicantId])->row();
        if (!$applicant) {
            // applicant table: record_no match (common case when applicant_id stores code)
            $applicant = $this->db->get_where('hris_applicant', ['record_no' => $applicantId])->row();
        }
        if ($applicant) {
            return [
                'type' => 'applicant',
                'record_no' => $applicant->record_no,
                'id' => $applicant->id,
                'profile' => $applicant,
            ];
        }

        // staff table: IDNumber
        $staff = $this->db->get_where('hris_staff', ['IDNumber' => $applicantId])->row();
        if ($staff) {
            return [
                'type' => 'staff',
                'record_no' => $staff->IDNumber,
                'id' => $staff->IDNumber,
                'profile' => $staff,
            ];
        }

        return null;
    }

    /**
     * Mass accept pending rating requests for the specified job_type group.
     *
     * Rules:
     * - Only requests with a previous rating in the same job_type are accepted.
     * - The most recent rating (by fy then appID) is copied to the current app_id.
     */
    public function mass_accept_by_level(array $jobTypes, $userId): array
    {
        $fyNow = date('Y');

        $pending = $this->db
            ->select('rr.*, jv.job_type, jv.jobTitle, jv.sy')
            ->from('hris_rating_request rr')
            ->join('hris_jobvacancy jv', 'jv.jobID = rr.job_id', 'left')
            ->where_in('jv.job_type', $jobTypes)
            ->where('rr.stat', 0)
            ->get()
            ->result();

        $summary = [
            'checked' => count($pending),
            'accepted' => 0,
            'skipped' => 0,
            'skipped_no_previous' => 0,
            'skipped_no_profile' => 0,
            'skipped_no_application' => 0,
            'accepted_existing_rating' => 0,
            'skipped_unrated_prior' => 0,
            'accepted_stubbed_rating' => 0,
        ];

        foreach ($pending as $row) {
            // Locate applicant profile using tolerant lookup
            $resolved = $this->resolve_profile($row->applicant_id);
            if (!$resolved) {
                $summary['skipped']++;
                $summary['skipped_no_profile']++;
                continue;
            }

            // Determine unique code used in ratings (record_no or IDNumber)
            $recordNo = $resolved['record_no'];
            $idCandidates = array_unique([
                $row->applicant_id,
                $resolved['record_no'],
                $resolved['id']
            ]);

            // Ensure current application exists (avoid null app causing missing errors)
            $currentApp = null;
            if (!empty($row->app_id)) {
                $currentApp = $this->db->get_where('hris_applications', ['appID' => $row->app_id])->row();
            }
            if (!$currentApp) {
                $summary['skipped']++;
                $summary['skipped_no_application']++;
                continue;
            }

            // Check if destination application already has rating (we will still accept; just avoid duplicate insert)
            $existing = $this->db->get_where('hris_applications_rating', [
                'appID' => $row->app_id,
            ])->row();

            // Find latest previous rating for same job_type; first try earlier SY than current, then fall back to any SY.
            $currentSyYear = $this->sy_start_year($row->sy ?? null);

            // Collect candidate previous ratings ordered by latest SY/FY then pick the best earlier year if possible.
            $candidates = $this->db
                ->select('rar.*, app.appID, app.applicant_id, jv.job_type, jv.sy')
                ->from('hris_applications_rating rar')
                ->join('hris_applications app', 'app.appID = rar.appID', 'left')
                ->join('hris_jobvacancy jv', 'jv.jobID = app.jobID', 'left')
                ->group_start()
                    ->where_in('rar.record_no', $idCandidates)
                    ->or_where_in('app.applicant_id', $idCandidates)
                ->group_end()
                ->where_in('jv.job_type', $jobTypes)
                ->where('rar.appID !=', $row->app_id)
                ->order_by('jv.sy', 'desc')
                ->order_by('rar.fy', 'desc')
                ->order_by('rar.appID', 'desc')
                ->limit(50)
                ->get()
                ->result();

            $previous = null;
            if (!empty($candidates) && $currentSyYear !== null) {
                foreach ($candidates as $cand) {
                    $candSy = $this->sy_start_year($cand->sy);
                    if ($candSy !== null && $candSy < $currentSyYear) {
                        $previous = $cand;
                        break;
                    }
                }
            }
            // If none strictly earlier, fall back to the most recent available candidate.
            if (!$previous && !empty($candidates)) {
                $previous = $candidates[0];
            }

            if (!$previous) {
                // Check if there is a prior application for same job_type but no rating yet (e.g., still Validated/Application Submitted)
                $priorQuery = $this->db
                    ->select('app.appID, jv.job_type, jv.sy')
                    ->from('hris_applications app')
                    ->join('hris_jobvacancy jv', 'jv.jobID = app.jobID', 'left')
                    ->where_in('app.applicant_id', $idCandidates)
                    ->where_in('jv.job_type', $jobTypes)
                    ->where('app.appID !=', $row->app_id);

                if (!empty($row->sy)) {
                    $priorQuery->group_start()
                        ->where('jv.sy <', $row->sy)
                        ->or_where('jv.sy', $row->sy)
                    ->group_end();
                }

                $priorApp = $priorQuery
                    ->order_by('jv.sy', 'desc')
                    ->order_by('app.appID', 'desc')
                    ->get()
                    ->row();

                if ($priorApp) {
                    // Create a stub rating so the request can be accepted even without prior rating
                    if (!$existing) {
                        $this->db->insert('hris_applications_rating', [
                            'record_no'   => $recordNo,
                            'appID'       => $row->app_id,
                            'education'   => 0.00001,
                            'training'    => 0.00001,
                            'experience'  => 0.00001,
                            'let_rating'  => 0.00001,
                            'demo_rating' => 0.00001,
                            'tr_rating'   => 0.00001,
                            'total_points'=> 0.00001,
                            'eval_id1'    => 0,
                            'eval_id2'    => 0,
                            'eval_id3'    => 0,
                            'job_type'    => $row->job_type,
                            'fy'          => $fyNow,
                        ]);
                    } else {
                        $summary['accepted_existing_rating']++;
                    }
                    $summary['accepted']++;
                    $summary['accepted_stubbed_rating']++;

                    // Update application status similar to single accept flow
                    $status = ($row->r_type == 1) ? 'Rated' : 'Endorsed for Rating';
                    $this->db->where('appID', $row->app_id)
                        ->update('hris_applications', [
                            'appStatus' => $status,
                            'dq' => 1,
                        ]);

                    // Mark request as granted
                    $this->db->where('id', $row->id)
                        ->update('hris_rating_request', [
                            'stat' => 1,
                            'res' => $userId,
                        ]);

                    continue;
                } else {
                    $summary['skipped']++;
                    $summary['skipped_no_previous']++;
                    continue;
                }
            }

            // Prepare retained rating payload (same logic as Pages::request_rating_granted -> copy_rating/copy_limited_rating)
            $ratingFy = $previous->fy ?? $fyNow;

            $ratingData = [
                'record_no'    => $recordNo,
                'appID'        => $row->app_id,
                'education'    => $row->r_type == 1 ? $previous->education   : 0.00001,
                'training'     => $row->r_type == 1 ? $previous->training    : 0.00001,
                'experience'   => $row->r_type == 1 ? $previous->experience  : 0.00001,
                'let_rating'   => $previous->let_rating,
                'demo_rating'  => $previous->demo_rating,
                'tr_rating'    => $previous->tr_rating,
                'total_points' => $previous->total_points,
                'eval_id1'     => $row->r_type == 1 ? $previous->eval_id1    : 0,
                'eval_id2'     => $previous->eval_id2,
                'eval_id3'     => $previous->eval_id3,
                'job_type'     => $previous->job_type,
                'fy'           => $ratingFy,
            ];

            // Upsert: insert new rating or update existing one (in case a stub/partial already exists)
            if (!$existing) {
                $this->db->insert('hris_applications_rating', $ratingData);
            } else {
                // Preserve the fiscal year on existing rows
                unset($ratingData['fy']);
                $this->db->where('appID', $row->app_id)->update('hris_applications_rating', $ratingData);
                $summary['accepted_existing_rating']++;
            }

            // Update application status similar to single accept flow
            $status = ($row->r_type == 1) ? 'Rated' : 'Endorsed for Rating';
            $this->db->where('appID', $row->app_id)
                ->update('hris_applications', [
                    'appStatus' => $status,
                    'dq' => 1,
                ]);

            // Mark request as granted
            $this->db->where('id', $row->id)
                ->update('hris_rating_request', [
                    'stat' => 1,
                    'res' => $userId,
                ]);

            $summary['accepted']++;
        }

        return $summary;
    }
}
