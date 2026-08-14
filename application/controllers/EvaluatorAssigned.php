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
        $this->load->model('Reg');
    }

    private function guard()
    {
        $pos = $this->session->userdata('position');
        if (!in_array($pos, ['Evaluator','rater','raters','District','doceval','asds'])) {
            show_error('Forbidden', 403);
            exit;
        }
    }

    private function currentEvaluatorId()
    {
        return (int)($this->session->id ?? $this->session->userdata('id'));
    }

    private function isAssignedToCurrentEvaluator($appID)
    {
        $evaluatorId = $this->currentEvaluatorId();
        if ($evaluatorId <= 0 || (int)$appID <= 0) {
            return false;
        }

        return (bool)$this->db
            ->from('hris_rater_assignments')
            ->where('app_id', (int)$appID)
            ->where('rater_user_id', $evaluatorId)
            ->count_all_results();
    }

    private function safeQualificationReturnUrl()
    {
        $returnUrl = trim((string)$this->input->post('return_url'));
        if ($returnUrl !== ''
            && strpos($returnUrl, base_url()) === 0
            && strpos($returnUrl, "\r") === false
            && strpos($returnUrl, "\n") === false) {
            return $returnUrl;
        }

        return base_url('EvaluatorAssigned');
    }

    private function ensureRatingRecord($application, $job, $recordNo)
    {
        $appID = (int)$application->appID;
        $jobPosition = (int)($job->position ?? 0);
        $isPromotion = (int)($job->promotion ?? 0) === 1 || $jobPosition === 5;
        $fy = trim((string)($application->app_year ?? $job->sy ?? date('Y')));
        $jobType = (int)($job->job_type ?? 0);

        if ($jobPosition === 1 && !$isPromotion) {
            if ($this->db->where('appID', $appID)->count_all_results('hris_applications_rating') > 0) {
                return true;
            }

            return $this->db->insert('hris_applications_rating', [
                'record_no' => $recordNo,
                'appID' => $appID,
                'education' => 0.00001,
                'training' => 0.00001,
                'experience' => 0.00001,
                'let_rating' => 0.00001,
                'demo_rating' => 0.00001,
                'tr_rating' => 0.00001,
                'total_points' => 0.00006,
                'eval_id1' => 0,
                'eval_id2' => 0,
                'eval_id3' => 0,
                'job_type' => $jobType,
                'fy' => $fy,
            ]);
        }

        if ($isPromotion) {
            if ($this->db->where('appID', $appID)->count_all_results('hris_rating_promotion') > 0) {
                return true;
            }

            return $this->db->insert('hris_rating_promotion', [
                'record_no' => $recordNo,
                'appID' => $appID,
                'educ' => 0.00001,
                'trainings' => 0.00001,
                'experience' => 0.00001,
                'performance' => 0.00001,
                'ppstco' => 0.00001,
                'ppstpa' => 0.00001,
                'total_points' => 0.00006,
                'eval_id1' => 0,
                'eval_id2' => 0,
                'eval_id3' => 0,
                'job_type' => $jobType,
                'fy' => $fy,
            ]);
        }

        if ($this->db->where('appID', $appID)->count_all_results('hris_rating_none') > 0) {
            return true;
        }

        return $this->db->insert('hris_rating_none', [
            'record_no' => $recordNo,
            'appID' => $appID,
            'educ' => 0.00001,
            'trainings' => 0.00001,
            'experience' => 0.00001,
            'performance' => 0.00001,
            'oa' => 0.00001,
            'ae' => 0.00001,
            'ald' => 0.00001,
            'interview' => 0.00001,
            'written' => 0.00001,
            'skills' => 0.00001,
            'total_points' => 0.00010,
            'eval_id1' => 0,
            'eval_id2' => 0,
            'eval_id3' => 0,
            'job_type' => $jobType,
            'fy' => $fy,
        ]);
    }

    public function index()
    {
        $this->guard();

        $raterId = (int)($this->session->id ?? $this->session->userdata('id'));
        if (!$raterId) {
            redirect(base_url());
            return;
        }

        // This is the evaluator's home page, so an empty assignment list is a valid
        // state: the dashboard renders its own "nothing assigned yet" rows rather
        // than bouncing back to the old landing page.
        $assignments = $this->assignRater->get_assigned_applicants($raterId);

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

    if (!$this->isAssignedToCurrentEvaluator($appID)) {
        show_error('This application is not assigned to your evaluator account.', 403);
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

    public function qualification()
    {
        $this->guard();
        date_default_timezone_set('Asia/Manila');

        $returnUrl = $this->safeQualificationReturnUrl();
        $position = (string)$this->session->userdata('position');
        if (!in_array($position, ['Evaluator', 'rater', 'raters'], true)) {
            show_error('Only an assigned evaluator may submit this qualification review.', 403);
            return;
        }

        if (strtoupper((string)$this->input->server('REQUEST_METHOD')) !== 'POST') {
            show_error('Method Not Allowed', 405);
            return;
        }

        $appID = (int)$this->input->post('appID');
        if ($appID <= 0 || !$this->isAssignedToCurrentEvaluator($appID)) {
            show_error('This application is not assigned to your evaluator account.', 403);
            return;
        }

        $application = $this->db
            ->where('appID', $appID)
            ->get('hris_applications')
            ->row();

        if (!$application) {
            show_error('Application not found.', 404);
            return;
        }

        $allowedStatuses = ['Application Submitted', 'Validated'];
        if (!in_array((string)$application->appStatus, $allowedStatuses, true) || (int)$application->dq === 2) {
            $this->session->set_flashdata('danger', 'This qualification review has already been completed or is no longer available.');
            redirect($returnUrl);
            return;
        }

        $decision = (int)$this->input->post('remarks');
        $reason = trim((string)$this->input->post('reason'));
        $documentsReviewed = (int)$this->input->post('documents_reviewed') === 1;

        if (!in_array($decision, [1, 2], true)) {
            $this->session->set_flashdata('danger', 'Select whether the applicant is Qualified or Disqualified.');
            redirect($returnUrl);
            return;
        }

        if (!$documentsReviewed) {
            $this->session->set_flashdata('danger', 'Confirm that the mandatory documents were reviewed before saving the decision.');
            redirect($returnUrl);
            return;
        }

        if ($decision === 2 && $reason === '') {
            $this->session->set_flashdata('danger', 'A reason is required when an applicant is disqualified.');
            redirect($returnUrl);
            return;
        }

        $job = $this->db
            ->where('jobID', (int)$application->jobID)
            ->get('hris_jobvacancy')
            ->row();

        if (!$job) {
            show_error('Job vacancy not found.', 404);
            return;
        }

        if (isset($job->jvStatus) && strcasecmp(trim((string)$job->jvStatus), 'Closed') === 0) {
            $this->session->set_flashdata('danger', 'This vacancy is closed. Qualification decisions can no longer be changed.');
            redirect($returnUrl);
            return;
        }

        $recordNo = trim((string)$this->input->post('record_no'));
        if ($recordNo === '') {
            $recordNo = trim((string)$application->applicant_id);
        }
        $applicantIdForTracking = (int)$this->input->post('id');
        if ($applicantIdForTracking <= 0 && is_numeric($application->applicant_id)) {
            $applicantIdForTracking = (int)$application->applicant_id;
        }

        $this->db->trans_begin();

        $decisionSaved = $this->Reg->update_dq($decision);
        $remarksSaved = $this->Reg->insert_dq();
        $ratingReady = true;
        $statusSaved = true;
        $trackingSaved = true;

        if ($decision === 1) {
            $statusSaved = $this->db
                ->where('appID', $appID)
                ->update('hris_applications', ['appStatus' => 'Endorsed for Rating']);
            $trackingSaved = $this->db->insert('hris_applications_track', [
                'jobID' => (int)$application->jobID,
                'empEmail' => (string)($application->empEmail ?? ''),
                'dateSubmitted' => date('Y-m-d'),
                'appStatus' => 'Endorsed for Rating.',
                'note' => '',
                'timeSubmitted' => date('h:i:s a'),
                'applicant_id' => $applicantIdForTracking,
                'res' => (string)$this->session->userdata('username'),
                'nstat' => 0,
                'app_id' => $appID,
            ]);
            $ratingReady = $this->ensureRatingRecord($application, $job, $recordNo);
        }

        if (!$decisionSaved || !$remarksSaved || !$statusSaved || !$trackingSaved || !$ratingReady || $this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('danger', 'The qualification review could not be saved. Please try again.');
            redirect($returnUrl);
            return;
        }

        $this->db->trans_commit();

        if ($decision === 1) {
            $this->session->set_flashdata('success', 'Applicant marked Qualified and endorsed for rating. You may now start rating.');
        } else {
            $this->session->set_flashdata('success', 'Applicant marked Disqualified. The reason and document review were saved.');
        }

        redirect($returnUrl);
    }




    /**
     * Disqualified applicants assigned to the current evaluator/rater.
     * Renders a dedicated list with a per-row "Reason" button that opens a
     * modal showing the disqualification reason recorded in hris_app_dq.
     */
    public function disqualified()
    {
        $this->guard();

        $raterId = (int)($this->session->id ?? $this->session->userdata('id'));
        if (!$raterId) {
            redirect(base_url());
            return;
        }

        $page = 'evaluator_disqualified';
        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $data = [
            'title'           => 'Disqualified Applicants',
            'disqualified'    => $this->assignRater->get_disqualified_applicants($raterId),
            'jobTypes'        => $this->assignRater->job_types_map(),
        ];

        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
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
