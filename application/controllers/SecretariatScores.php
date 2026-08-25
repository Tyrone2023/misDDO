<?php
defined('BASEPATH') or exit('No direct script access allowed');

/** Secretariat entry point for Interview and Written Examination scores. */
class SecretariatScores extends CI_Controller
{
    /** Per-request cache of a Field Encoder's [job_id => encode_mode] scope. */
    private $accessCache = null;

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper(['url', 'form']);
        $this->load->model('Secretariat_model', 'secretariat');
        $this->load->model('Audit_model', 'Audit');
    }

    private function guard(): void
    {
        if (!in_array($this->session->userdata('position'), ['Secretariat', 'Field Encoder'], true)) {
            show_error('Only Secretariat users can encode Interview and Written Examination scores.', 403, 'Forbidden');
            exit;
        }

        if ($this->session->userdata('position') === 'Field Encoder' && $this->user_id() <= 0) {
            show_error('This Field Encoder account is not linked to a Secretariat.', 403, 'Forbidden');
            exit;
        }
    }

    /**
     * Whose vacancies this page works on. A Field Encoder has no scope of its
     * own - it borrows the scope of the Secretariat that created the account.
     */
    private function user_id(): int
    {
        $sessionId = (int) ($this->session->id ?? $this->session->userdata('id'));

        if ($this->session->userdata('position') === 'Field Encoder') {
            return $this->secretariat->field_encoder_owner($sessionId);
        }

        return $sessionId;
    }

    private function encoding_mode($value): string
    {
        $value = strtolower(trim((string) $value));
        return in_array($value, ['written', 'interview', 'both'], true) ? $value : 'both';
    }

    /** The logged-in account itself, as opposed to the scope owner. */
    private function session_user_id(): int
    {
        return (int) ($this->session->id ?? $this->session->userdata('id'));
    }

    private function is_field_encoder(): bool
    {
        return $this->session->userdata('position') === 'Field Encoder';
    }

    /** [job_id => encode_mode] for a Field Encoder; empty for a Secretariat. */
    private function encoder_access(): array
    {
        if ($this->accessCache === null) {
            $this->accessCache = $this->is_field_encoder()
                ? $this->secretariat->field_encoder_access($this->session_user_id())
                : [];
        }

        return $this->accessCache;
    }

    /**
     * What the current account may encode on one vacancy: 'written',
     * 'interview', 'both', or '' when it may not open the vacancy at all.
     * A Secretariat always holds 'both' over its own assigned vacancies.
     */
    private function allowed_mode(int $jobId): string
    {
        if (!$this->is_field_encoder()) {
            return 'both';
        }

        $access = $this->encoder_access();
        return $access[$jobId] ?? '';
    }

    /** Encoding modes the toolbar may offer for one vacancy. */
    private function mode_options(int $jobId): array
    {
        $allowed = $this->allowed_mode($jobId);
        if ($allowed === '') {
            return [];
        }

        return $allowed === 'both' ? ['written', 'interview', 'both'] : [$allowed];
    }

    private function is_ajax_request(): bool
    {
        return strtolower((string) $this->input->server('HTTP_X_REQUESTED_WITH')) === 'xmlhttprequest'
            || strpos(strtolower((string) $this->input->server('HTTP_ACCEPT')), 'application/json') !== false;
    }

    private function json_response(array $payload, int $status = 200): void
    {
        $this->output
            ->set_status_header($status)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($payload));
    }

    private function list_url(int $jobId = 0, string $mode = 'both'): string
    {
        $query = ['mode' => $this->encoding_mode($mode)];
        if ($jobId > 0) {
            $query = ['job_id' => $jobId] + $query;
        }

        return base_url('secretariat/scores?' . http_build_query($query));
    }

    public function index(): void
    {
        $this->guard();

        $userId = $this->user_id();
        $jobId = (int) $this->input->get('job_id');
        $mode = $this->encoding_mode($this->input->get('mode'));
        $vacancies = [];
        $selectedVacancy = null;

        foreach ($this->secretariat->assignable_vacancies($userId) as $vacancy) {
            // A Field Encoder only sees the vacancies tagged to its account.
            if ($this->is_field_encoder() && $this->allowed_mode((int) $vacancy->jobID) === '') {
                continue;
            }

            $vacancies[] = $vacancy;
            if ((int) $vacancy->jobID === $jobId) {
                $selectedVacancy = $vacancy;
            }
        }

        if ($jobId > 0 && empty($selectedVacancy)) {
            $this->session->set_flashdata('danger', $this->is_field_encoder()
                ? 'That vacancy is not tagged to your Field Encoder account.'
                : 'That vacancy is not available for Secretariat score encoding.');
            redirect($this->list_url(0, $mode));
            return;
        }

        // The toolbar may only offer what the account is allowed to encode.
        $modeOptions = $selectedVacancy ? $this->mode_options($jobId) : ['written', 'interview', 'both'];
        if (!empty($modeOptions) && !in_array($mode, $modeOptions, true)) {
            $mode = $modeOptions[0];
        }

        $applicants = $selectedVacancy
            ? $this->secretariat->score_entry_applicants($userId, $jobId)
            : [];

        $data = [
            'title' => 'Interview and Written Examination Scores',
            'vacancies' => $vacancies,
            'selectedVacancy' => $selectedVacancy,
            'selectedJobId' => $jobId,
            'encodingMode' => $mode,
            'modeOptions' => $modeOptions,
            'applicants' => $applicants,
            'scoreCounts' => $this->secretariat->score_entry_counts($userId),
            'activity' => $selectedVacancy ? $this->secretariat->score_activity($jobId) : [],
            'lastActions' => $selectedVacancy ? $this->secretariat->score_last_actions($jobId) : [],
        ];

        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/secretariat_scores', $data);
        $this->load->view('templates/footer');
    }

    public function save(): void
    {
        $this->guard();

        if (strtoupper((string) $this->input->method()) !== 'POST') {
            show_error('Method Not Allowed', 405);
            return;
        }

        $appId = (int) $this->input->post('app_id');
        $jobId = (int) $this->input->post('job_id');
        $mode = $this->encoding_mode($this->input->post('mode'));
        $interviewInput = trim((string) $this->input->post('interview', true));
        $writtenInput = trim((string) $this->input->post('written', true));
        $interview = null;
        $written = null;
        $errors = [];

        foreach ([
            'Interview' => [$interviewInput, 'interview'],
            'Written Examination' => [$writtenInput, 'written'],
        ] as $label => $entry) {
            [$value, $field] = $entry;
            if ($value === '') {
                continue;
            }
            if (!is_numeric($value) || (float) $value < 0 || (float) $value > 20) {
                $errors[] = $label . ' must be a number from 0 to 20.';
                continue;
            }
            if ($field === 'interview') {
                $interview = (float) $value;
            } else {
                $written = (float) $value;
            }
        }

        if ($appId <= 0 || $jobId <= 0) {
            $errors[] = 'The application or vacancy is missing.';
        }
        if ($interviewInput === '' && $writtenInput === '') {
            $errors[] = 'Enter an Interview or Written Examination score.';
        }

        // A Field Encoder may only write the score(s) tagged to it on this vacancy.
        $allowed = $jobId > 0 ? $this->allowed_mode($jobId) : '';
        if ($allowed === '') {
            $errors[] = 'You are not allowed to encode scores for that vacancy.';
        } elseif ($allowed === 'written' && $interviewInput !== '') {
            $errors[] = 'Your account may only encode the Written Examination score for this vacancy.';
        } elseif ($allowed === 'interview' && $writtenInput !== '') {
            $errors[] = 'Your account may only encode the Interview score for this vacancy.';
        }

        if (!empty($errors)) {
            $message = implode(' ', $errors);
            if ($this->is_ajax_request()) {
                $this->json_response(['ok' => false, 'message' => $message], 422);
                return;
            }
            $this->session->set_flashdata('danger', $message);
            redirect($this->list_url($jobId, $mode));
            return;
        }

        $result = $this->secretariat->save_interview_written_scores(
            $this->user_id(),
            $appId,
            $interview,
            $written
        );

        if (empty($result['ok'])) {
            $message = $result['message'] ?? 'The scores could not be saved.';
            if ($this->is_ajax_request()) {
                $this->json_response(['ok' => false, 'message' => $message], 422);
                return;
            }
            $this->session->set_flashdata('danger', $message);
            redirect($this->list_url($jobId, $mode));
            return;
        }

        $application = $result['application'];
        $previous = $result['old'] ?? [];
        $actions = [];

        foreach (['interview' => $interview, 'written' => $written] as $field => $value) {
            if ($value === null) {
                continue;
            }

            $label = $field === 'interview' ? 'Interview' : 'Written Examination';
            $before = $previous[$field] ?? null;

            // Separate a first encode from an edit so the activity trail reads
            // as an action, not just a value. The action stays 'rate' - the
            // audit lookups on Pages/ma key off it.
            $isEdit = $this->score_is_encoded($before);
            $description = $isEdit
                ? 'Edited ' . $label . ' rating: ' . $this->score_text($before) . ' to ' . $this->score_text($value)
                : 'Encoded ' . $label . ' rating: ' . $this->score_text($value);

            $actions[$field] = [
                'kind' => $isEdit ? 'edit' : 'encode',
                'description' => $description,
            ];

            $this->Audit->log('rate', [
                'entity_type' => 'rating',
                'entity_id' => 'hris_rating_none',
                'app_id' => $appId,
                'applicant_id' => (string) $application->record_no,
                'job_id' => (int) $application->jobID,
                'field' => $field,
                'description' => $description,
            ]);
        }

        $saved = [];
        if ($interview !== null) {
            $saved['interview'] = $interview;
        }
        if ($written !== null) {
            $saved['written'] = $written;
        }

        if ($this->is_ajax_request()) {
            $this->json_response([
                'ok' => true,
                'message' => count($saved) === 2 ? 'Both scores saved' : 'Score saved',
                'app_id' => $appId,
                'saved' => $saved,
                'saved_at' => date('g:i:s A'),
                // Lets each score box credit its own encoder without a reload.
                'actor' => $this->actor_label(),
                'when' => date('M j, Y g:i A'),
                'actions' => $actions,
            ]);
            return;
        }

        $this->session->set_flashdata('success', 'The score was saved and is now displayed on the MA page.');
        redirect($this->list_url($jobId, $mode));
    }

    /** 0.00001 is the "not yet encoded" sentinel the rating forms write. */
    private function score_is_encoded($value): bool
    {
        return $value !== null && abs((float) $value - 0.00001) > 0.000001;
    }

    /** Display name of the logged-in account, as the audit trail records it. */
    private function actor_label(): string
    {
        $row = $this->db
            ->select('username, fname, lname')
            ->where('id', $this->session_user_id())
            ->get('users')
            ->row();

        $name = $row ? trim(trim((string) $row->fname) . ' ' . trim((string) $row->lname)) : '';
        if ($name !== '') {
            return $name;
        }

        if ($row && trim((string) $row->username) !== '') {
            return (string) $row->username;
        }

        return (string) $this->session->userdata('username');
    }

    private function score_text($value): string
    {
        return rtrim(rtrim(number_format((float) $value, 2, '.', ''), '0'), '.');
    }

    /**
     * Encoding activity for one vacancy, as JSON, so the trail on the encoding
     * screen can refresh after a save without reloading the page.
     */
    public function activity(): void
    {
        $this->guard();

        $jobId = (int) $this->input->get('job_id');

        if ($jobId <= 0 || $this->allowed_mode($jobId) === ''
            || !$this->secretariat->secretariat_has_vacancy($this->user_id(), $jobId)) {
            $this->json_response(['ok' => false, 'message' => 'That vacancy is not available.'], 403);
            return;
        }

        $entries = [];
        foreach ($this->secretariat->score_activity($jobId) as $row) {
            $actor = trim(trim((string) $row->fname) . ' ' . trim((string) $row->lname));
            $applicant = trim(trim((string) $row->app_last) . ', ' . trim((string) $row->app_first));

            $entries[] = [
                'id' => (int) $row->id,
                'when' => (string) $row->created_at,
                'actor' => $actor !== '' ? $actor : (string) $row->username,
                'username' => (string) $row->username,
                'role' => (string) $row->position,
                'field' => (string) $row->field,
                'app_id' => (int) $row->app_id,
                'applicant' => trim($applicant, ', '),
                'description' => (string) $row->description,
            ];
        }

        $this->json_response(['ok' => true, 'entries' => $entries]);
    }
}
