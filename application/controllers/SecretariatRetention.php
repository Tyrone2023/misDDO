<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Retention of points, from the Secretariat side.
 *
 * An applicant already rated for an earlier vacancy can ask to keep those
 * points. The request is resolved one of two ways:
 *
 *   copy   - lift the scores off an earlier application of the same applicant
 *   manual - encode the score by hand
 *
 * Manual encoding is not a convenience: most requests have no earlier
 * application on file carrying a rating row, so copying cannot resolve them at
 * all. Both routes write the same rating row, mark the same request granted and
 * move the application to the same status, so a retention looks identical
 * downstream however it was resolved.
 *
 * Every action is limited to vacancies assigned to the signed-in Secretariat
 * account and is written to the audit trail under their user id.
 */
class SecretariatRetention extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper(['url', 'form']);
        $this->load->model('Secretariat_model', 'secretariat');
        $this->load->model('Hiring_model');
        $this->load->model('Reg');
        $this->load->model('Audit_model', 'Audit');
    }

    private function guard(): void
    {
        if ($this->session->userdata('position') !== 'Secretariat') {
            show_error('Only Secretariat users can manage retention requests.', 403, 'Forbidden');
            exit;
        }
    }

    private function user_id(): int
    {
        return (int) ($this->session->id ?? $this->session->userdata('id'));
    }

    private function require_post(): void
    {
        if (strtoupper((string) $this->input->method()) !== 'POST') {
            show_error('Method Not Allowed', 405);
            exit;
        }
    }

    private function list_url(int $jobId): string
    {
        return base_url('secretariat/retention?job_id=' . $jobId);
    }

    public function index(): void
    {
        $this->guard();

        $userId = $this->user_id();
        $jobId = (int) $this->input->get('job_id');
        $assignedVacancies = $this->secretariat->tagging_vacancies($userId);
        $counts = $this->secretariat->retention_counts($userId);
        $vacancies = [];
        $selectedVacancy = null;

        foreach ($assignedVacancies as $vacancy) {
            // The retention picker is a request queue, not a list of every
            // vacancy assigned to the Secretariat. Keep vacancies with no
            // retention request out of the picker.
            if ((int) ($counts[(int) $vacancy->jobID]['total'] ?? 0) > 0) {
                $vacancies[] = $vacancy;
            }

            if ((int) $vacancy->jobID === $jobId) {
                $selectedVacancy = $vacancy;
            }
        }

        if ($jobId > 0 && !$selectedVacancy) {
            $this->session->set_flashdata('danger', 'That vacancy is not assigned to your Secretariat account.');
            redirect(base_url('secretariat/retention'));
            return;
        }

        $requests = $selectedVacancy
            ? $this->secretariat->retention_requests($userId, $jobId)
            : [];

        $pType = 0;
        $maxPoints = [];

        if ($selectedVacancy) {
            $pType = (int) $selectedVacancy->position;
            foreach ($requests as $request) {
                if (!empty($request->p_type_resolved)) {
                    $pType = (int) $request->p_type_resolved;
                    break;
                }
            }
            $maxPoints = $this->secretariat->retention_max_points((string) $selectedVacancy->jobTitle, $pType);
        }

        $data = [
            'title' => 'Retention of Points',
            'vacancies' => $vacancies,
            'retentionCounts' => $counts,
            'selectedVacancy' => $selectedVacancy,
            'selectedJobId' => $jobId,
            'requests' => $requests,
            'pType' => $pType,
            'maxPoints' => $maxPoints,
            'scoreMap' => $pType > 0 ? $this->secretariat->retention_score_map($pType, false) : [],
        ];

        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/secretariat_retention', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Read-only register of denied retention requests.
     *
     * Kept apart from index() because the pending queue is a work screen and
     * this is a record: nothing here can be acted on, so it spans every
     * assigned vacancy by default instead of forcing a vacancy choice first.
     *
     * scope=mine narrows it to denials this account made (hris_rating_request.res),
     * which Reg::deny_request_stat() stamps at the moment of the decision.
     */
    public function denied(): void
    {
        $this->guard();

        $userId = $this->user_id();
        $jobId = (int) $this->input->get('job_id');
        $scope = strtolower(trim((string) $this->input->get('scope'))) === 'mine' ? 'mine' : 'all';

        $counts = $this->secretariat->retention_counts($userId);
        $vacancies = [];
        $selectedVacancy = null;

        foreach ($this->secretariat->tagging_vacancies($userId) as $vacancy) {
            // Only vacancies that actually carry a denial belong in the picker.
            if ((int) ($counts[(int) $vacancy->jobID]['denied'] ?? 0) > 0) {
                $vacancies[] = $vacancy;
            }

            if ((int) $vacancy->jobID === $jobId) {
                $selectedVacancy = $vacancy;
            }
        }

        if ($jobId > 0 && !$selectedVacancy) {
            $this->session->set_flashdata('danger', 'That vacancy is not assigned to your Secretariat account.');
            redirect(base_url('secretariat/retention/denied'));
            return;
        }

        // One query covers both chips: the full set is counted, then narrowed
        // for display, so "All" and "Mine" always show a figure.
        $allDenied = $this->secretariat->retention_denied($userId, $jobId);
        $mineDenied = [];

        foreach ($allDenied as $row) {
            if ((int) $row->res === $userId) {
                $mineDenied[] = $row;
            }
        }

        $data = [
            'title' => 'Denied Retention Requests',
            'vacancies' => $vacancies,
            'retentionCounts' => $counts,
            'selectedVacancy' => $selectedVacancy,
            'selectedJobId' => $jobId,
            'scope' => $scope,
            'requests' => $scope === 'mine' ? $mineDenied : $allDenied,
            'allCount' => count($allDenied),
            'mineCount' => count($mineDenied),
            'jobTypeLabels' => $this->secretariat->job_types_map(),
            'currentUserId' => $userId,
        ];

        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/secretariat_retention_denied', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Resolve a request by copying an earlier application's scores.
     *
     * The copy routines and the request/status updates read app_id and id
     * straight off the POST, which is why they are re-posted here rather than
     * passed as arguments - this is the same call sequence
     * Pages::request_rating_granted() uses, so a Secretariat grant and an HR
     * grant leave identical state behind.
     */
    public function grant_copy(): void
    {
        $this->guard();
        $this->require_post();

        $requestId = (int) $this->input->post('id');
        $sourceAppId = (int) $this->input->post('application');
        $request = $this->secretariat->retention_actionable_request($this->user_id(), $requestId);

        if (empty($request)) {
            $this->session->set_flashdata('danger', 'That retention request is no longer open for a decision.');
            redirect(base_url('secretariat/retention'));
            return;
        }

        $jobId = (int) $request->job_id;

        if ($sourceAppId <= 0) {
            $this->session->set_flashdata('danger', 'Choose the application to retain the scores from.');
            redirect($this->list_url($jobId));
            return;
        }

        $pType = (int) ($request->p_type ?: $request->position);
        $recordNo = $this->secretariat->retention_record_no($request);
        $scope = ((int) $request->r_type === 2) ? 2 : 1;

        // Confirm the chosen source really is one of this applicant's rated
        // applications; the select is not the only thing that can post here.
        $allowed = false;
        foreach ($this->secretariat->retention_sources($request, $pType, $jobId) as $source) {
            if ((int) $source['app_id'] === $sourceAppId) {
                $allowed = true;
                break;
            }
        }

        if (!$allowed) {
            $this->session->set_flashdata('danger', 'That application cannot be used as the source for this retention.');
            redirect($this->list_url($jobId));
            return;
        }

        $copied = ($pType === 1)
            ? ($scope === 1
                ? $this->Reg->copy_rating($recordNo, $sourceAppId)
                : $this->Reg->copy_limited_rating($recordNo, $sourceAppId))
            : ($scope === 1
                ? $this->Hiring_model->copy_rating($recordNo, $sourceAppId)
                : $this->Hiring_model->copy_limited_rating($recordNo, $sourceAppId));

        if ($copied === false) {
            $this->session->set_flashdata('danger', 'No rating was found on that application, so nothing was copied. Encode the score manually instead.');
            redirect($this->list_url($jobId));
            return;
        }

        $this->Reg->application_change_stat($scope === 1 ? 'Rated' : 'Endorsed for Rating');
        $this->Reg->copy_dq($sourceAppId, (int) $request->app_id, $jobId);
        $this->Reg->update_request_stat($scope);

        $this->audit(
            $request,
            'Granted retention of ' . $this->scope_label($scope, $pType)
                . ' by copying application #' . $sourceAppId . '.'
        );

        $this->session->set_flashdata('success', 'Retention granted from application #' . $sourceAppId . '.');
        redirect($this->list_url($jobId));
    }

    /**
     * Resolve a request by encoding the retained score by hand.
     *
     * Every criterion in the retention scope must be given and must sit inside
     * the position's ceiling; a partial score would leave the application in a
     * state neither the ranking nor the evaluator screens can read correctly.
     */
    public function grant_manual(): void
    {
        $this->guard();
        $this->require_post();

        $requestId = (int) $this->input->post('id');
        $request = $this->secretariat->retention_actionable_request($this->user_id(), $requestId);

        if (empty($request)) {
            $this->session->set_flashdata('danger', 'That retention request is no longer open for a decision.');
            redirect(base_url('secretariat/retention'));
            return;
        }

        $jobId = (int) $request->job_id;

        if (empty($request->appID)) {
            $this->session->set_flashdata('danger', 'This request points at an application that no longer exists, so a score cannot be recorded against it.');
            redirect($this->list_url($jobId));
            return;
        }

        $pType = (int) ($request->p_type ?: $request->position);
        $scope = ((int) $request->r_type === 2) ? 2 : 1;
        $scoreMap = $this->secretariat->retention_score_map($pType, $scope === 2);
        $maxPoints = $this->secretariat->retention_max_points((string) $request->jobTitle, $pType);

        $scores = [];
        $errors = [];

        foreach ($scoreMap as $label => $column) {
            $raw = trim((string) $this->input->post($column));

            if ($raw === '') {
                $errors[] = $label . ' is required';
                continue;
            }

            if (!is_numeric($raw)) {
                $errors[] = $label . ' must be a number';
                continue;
            }

            $value = (float) $raw;
            $max = (float) ($maxPoints[$column] ?? 0);

            if ($value < 0) {
                $errors[] = $label . ' cannot be negative';
                continue;
            }

            if ($max > 0 && $value > $max) {
                $errors[] = $label . ' cannot exceed ' . rtrim(rtrim(number_format($max, 2, '.', ''), '0'), '.');
                continue;
            }

            $scores[$column] = $value;
        }

        if (!empty($errors)) {
            $this->session->set_flashdata('danger', 'Score not saved: ' . implode('; ', $errors) . '.');
            redirect($this->list_url($jobId));
            return;
        }

        $recordNo = $this->secretariat->retention_record_no($request);

        if (!$this->secretariat->save_manual_retention($request, $pType, $scores, $recordNo)) {
            $this->session->set_flashdata('danger', 'The score could not be saved. Please try again.');
            redirect($this->list_url($jobId));
            return;
        }

        // application_change_stat() and update_request_stat() read app_id and id
        // off the POST, which this form already carries.
        $this->Reg->application_change_stat($scope === 1 ? 'Rated' : 'Endorsed for Rating');
        $this->Reg->update_request_stat($scope);

        $total = array_sum($scores);

        $this->audit(
            $request,
            'Granted retention of ' . $this->scope_label($scope, $pType)
                . ' by manual encoding, total ' . number_format($total, 2) . ' points.'
        );

        $this->session->set_flashdata('success', 'Retained score encoded (' . number_format($total, 2) . ' points).');
        redirect($this->list_url($jobId));
    }

    public function deny(): void
    {
        $this->guard();
        $this->require_post();

        $requestId = (int) $this->input->post('id');
        $reason = trim((string) $this->input->post('deny_reason'));
        $request = $this->secretariat->retention_actionable_request($this->user_id(), $requestId);

        if (empty($request)) {
            $this->session->set_flashdata('danger', 'That retention request is no longer open for a decision.');
            redirect(base_url('secretariat/retention'));
            return;
        }

        $jobId = (int) $request->job_id;

        if ($reason === '') {
            $this->session->set_flashdata('danger', 'A reason is required when denying a retention request.');
            redirect($this->list_url($jobId));
            return;
        }

        if (!$this->Reg->deny_request_stat($requestId, $reason)) {
            $this->session->set_flashdata('danger', 'The retention request could not be denied. Please try again.');
            redirect($this->list_url($jobId));
            return;
        }

        $this->audit($request, 'Denied retention: ' . $reason, 'retention_deny');

        $this->session->set_flashdata('success', 'Retention request denied.');
        redirect($this->list_url($jobId));
    }

    private function scope_label(int $scope, int $pType): string
    {
        if ($scope === 2) {
            return $pType === 1
                ? 'Demonstration Teaching and Teacher Reflection ratings only'
                : 'Interview and Written Examination ratings only';
        }

        return 'all scores';
    }

    /**
     * Audited against the application that receives the scores, matching
     * Pages::audit_retention(), so the entry lands on that application's
     * tracking timeline next to its rating history.
     */
    private function audit(object $request, string $description, string $action = 'retention_grant'): void
    {
        $this->Audit->log($action, [
            'entity_type' => 'rating_request',
            'entity_id' => $request->id ?? null,
            'app_id' => (int) ($request->app_id ?? 0),
            'applicant_id' => $request->applicant_id ?? null,
            'job_id' => (int) ($request->job_id ?? 0),
            'field' => 'retention',
            'description' => $description,
        ]);
    }
}
