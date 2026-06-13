<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class EvaluatorAssigned extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper('url');
        $this->load->model('AssignRater_model', 'assignRater');
        $this->load->model('Common');
    }

    private function guard()
    {
        $pos = $this->session->userdata('position');
        if (!in_array($pos, ['Evaluator','rater','raters','District','doceval'])) {
            show_error('Forbidden', 403);
            exit;
        }
    }

    public function index()
    {
        $this->guard();

        $raterId = (int)($this->session->id ?? $this->session->userdata('id'));
        if (!$raterId) {
            redirect(base_url());
            return;
        }

        $assignments = $this->assignRater->get_assigned_applicants($raterId);

        if (empty($assignments['pending']) && empty($assignments['scored'])) {
            redirect(base_url());
            return;
        }

        // Count distinct applicants (by applicant_id) with pending queries (stat = 0)
        $pending_queries = $this->db
            ->select('COUNT(DISTINCT ai.applicant_id) as cnt', false)
            ->from('hris_application_inquiry ai')
            ->join('hris_rater_assignments ra', 'ra.app_id = ai.application_id')
            ->where('ra.rater_user_id', $raterId)
            ->where('ai.stat', 0)
            ->where('ai.applicant_id IS NOT NULL', null, false)
            ->get()
            ->row()
            ->cnt ?? 0;

        $data = [
            'title'           => 'Assigned Applicants',
            'pending'         => $assignments['pending'],
            'scored'          => $assignments['scored'],
            'counts'          => $assignments['counts'],
            'jobTypes'        => $this->assignRater->job_types_map(),
            'pending_queries' => (int)$pending_queries,
        ];

        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/evaluator_assigned_dashboard', $data);
        $this->load->view('templates/footer');
    }

   public function open($record_no = null, $job_id = null, $pre_school = null, $appID = null, $record_no2 = null)
{
    $this->guard();

    // normalize record_no from URL/DB
    $normalize_record_no = function ($value) {
        $value = urldecode((string)$value);          // converts %20 to actual space
        $value = str_replace("\xC2\xA0", ' ', $value); // non-breaking space
        $value = trim($value);
        $value = preg_replace('/\s+/', '', $value); // remove ALL whitespace
        return $value;
    };

    $rawRecordNo1 = (string)$record_no;
    $rawRecordNo2 = (string)$record_no2;

    $record_no  = $normalize_record_no($record_no);
    $record_no2 = $normalize_record_no($record_no2);
    $job_id     = (int)$job_id;
    $appID      = (int)$appID;
    $pre_school = trim(urldecode((string)$pre_school));

    if ($appID <= 0) {
        show_error('Invalid application ID.', 400);
        return;
    }

    // 1) get application
    $app = $this->db
        ->where('appID', $appID)
        ->get('hris_applications')
        ->row();

    if (!$app) {
        show_error('Application not found.', 404);
        return;
    }

    // 2) get job
    $job = $this->db
        ->where('jobID', $app->jobID)
        ->get('hris_jobvacancy')
        ->row();

    // 3) resolve applicant
    $applicant = null;

    if (!empty($app->applicant_id)) {
        // try by applicant primary id first
        $applicant = $this->db
            ->where('id', $app->applicant_id)
            ->get('hris_applicant')
            ->row();

        // fallback: applicant_id may actually contain record_no
        if (!$applicant) {
            $candidate = $normalize_record_no($app->applicant_id);

            // find by exact cleaned value
            $applicant = $this->db
                ->group_start()
                    ->where('record_no', $candidate)
                    ->or_where("REPLACE(TRIM(record_no), ' ', '') =", $candidate, false)
                ->group_end()
                ->get('hris_applicant')
                ->row();
        }
    }

    // fallback from URL param 1
    if (!$applicant && $record_no !== '') {
        $applicant = $this->db
            ->group_start()
                ->where('record_no', $record_no)
                ->or_where("REPLACE(TRIM(record_no), ' ', '') =", $record_no, false)
            ->group_end()
            ->get('hris_applicant')
            ->row();
    }

    // fallback from URL param 2
    if (!$applicant && $record_no2 !== '') {
        $applicant = $this->db
            ->group_start()
                ->where('record_no', $record_no2)
                ->or_where("REPLACE(TRIM(record_no), ' ', '') =", $record_no2, false)
            ->group_end()
            ->get('hris_applicant')
            ->row();
    }

    // 4) final cleaned record no
    $cleanRecordNo = '';
    $oldApplicantRecordNo = '';

    if ($applicant && !empty($applicant->record_no)) {
        $oldApplicantRecordNo = (string)$applicant->record_no;
        $cleanRecordNo = $normalize_record_no($applicant->record_no);
    } elseif ($record_no !== '') {
        $cleanRecordNo = $record_no;
    } elseif ($record_no2 !== '') {
        $cleanRecordNo = $record_no2;
    }

    if ($cleanRecordNo === '') {
        show_error('Applicant record number not found.', 404);
        return;
    }

    // 5) if applicant record_no in DB is dirty, update it
    if ($applicant && $oldApplicantRecordNo !== $cleanRecordNo) {
        $this->db
            ->where('id', $applicant->id)
            ->update('hris_applicant', ['record_no' => $cleanRecordNo]);
    }

    // 6) if rating row already exists but record_no is dirty, update it too
    $rating = $this->db
        ->where('appID', $appID)
        ->get('hris_applications_rating')
        ->row();

    if ($rating) {
        $oldRatingRecordNo = isset($rating->record_no) ? (string)$rating->record_no : '';
        $cleanRatingRecordNo = $normalize_record_no($oldRatingRecordNo);

        if ($cleanRatingRecordNo !== $cleanRecordNo || $oldRatingRecordNo !== $cleanRecordNo) {
            $this->db
                ->where('appID', $appID)
                ->update('hris_applications_rating', ['record_no' => $cleanRecordNo]);
        }
    }

    // 7) if missing, insert rating row
    if (!$rating) {
        $insert = [
            'record_no'    => $cleanRecordNo,
            'appID'        => $appID,
            'education'    => 0.00001,
            'training'     => 0.00001,
            'experience'   => 0.00001,
            'let_rating'   => 0.00001,
            'demo_rating'  => 0.00001,
            'tr_rating'    => 0.00001,
            'total_points' => 0.00006,
            'eval_id1'     => 0,
            'eval_id2'     => 0,
            'eval_id3'     => 0,
            'job_type'     => !empty($job->job_type) ? (int)$job->job_type : 0,
            'fy'           => !empty($app->app_year) ? trim((string)$app->app_year) : date('Y'),
        ];

        $this->db->trans_begin();

        $ok = $this->db->insert('hris_applications_rating', $insert);

        if (!$ok) {
            $dbError = $this->db->error();
            $this->db->trans_rollback();
            show_error('Failed to save hris_applications_rating: ' . $dbError['message'], 500);
            return;
        }

        $verify = $this->db
            ->where('appID', $appID)
            ->get('hris_applications_rating')
            ->row();

        if (!$verify) {
            $this->db->trans_rollback();
            show_error('Rating row was not created.', 500);
            return;
        }

        $this->db->trans_commit();
    }

    // 8) always redirect using CLEAN record no
    $redirectJobId = !empty($app->jobID) ? (int)$app->jobID : $job_id;
    $redirectPreSchool = !empty($app->pre_school) ? trim((string)$app->pre_school) : $pre_school;

    redirect(base_url(
        'Pages/ma/' .
        rawurlencode($cleanRecordNo) . '/' .
        $redirectJobId . '/' .
        rawurlencode($redirectPreSchool) . '/' .
        $appID . '/' .
        rawurlencode($cleanRecordNo)
    ));
}




    public function check_updates()
    {
        $this->guard();

        header('Content-Type: application/json');

        $raterId = (int)($this->session->id ?? $this->session->userdata('id'));
        if (!$raterId) {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $assignments = $this->assignRater->get_assigned_applicants($raterId);
        $jobTypes    = $this->assignRater->job_types_map();

        // Count distinct applicants (by applicant_id) with pending queries (stat = 0)
        $pending_queries = $this->db
            ->select('COUNT(DISTINCT ai.applicant_id) as cnt', false)
            ->from('hris_application_inquiry ai')
            ->join('hris_rater_assignments ra', 'ra.app_id = ai.application_id')
            ->where('ra.rater_user_id', $raterId)
            ->where('ai.stat', 0)
            ->where('ai.applicant_id IS NOT NULL', null, false)
            ->get()
            ->row()
            ->cnt ?? 0;

        $normalize = fn($row) => [
            'appID'      => (int)($row->appID    ?? $row->app_id    ?? 0),
            'jobID'      => (int)($row->jobID    ?? $row->job_id    ?? 0),
            'record_no'  => trim((string)($row->record_no  ?? $row->applicant_id ?? '')),
            'firstName'  => trim((string)($row->FirstName  ?? '')),
            'middleName' => trim((string)($row->MiddleName ?? '')),
            'lastName'   => trim((string)($row->LastName   ?? '')),
            'jobTitle'   => trim((string)($row->jobTitle   ?? '')),
            'jobType'    => $jobTypes[(int)($row->job_type ?? 0)] ?? '',
            'appStatus'  => trim((string)($row->appStatus  ?? '')),
            'pre_school' => trim((string)($row->pre_school ?? '')),
        ];

        $counts = $assignments['counts'];
        $counts['pending_queries'] = (int)$pending_queries;

        echo json_encode([
            'counts'  => $counts,
            'pending' => array_map($normalize, $assignments['pending']),
            'scored'  => array_map($normalize, $assignments['scored']),
        ]);
    }
}