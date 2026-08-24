<?php
defined('BASEPATH') or exit('No direct script access allowed');

/** Secretariat entry point for Interview and Written Examination scores. */
class SecretariatScores extends CI_Controller
{
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
        if ($this->session->userdata('position') !== 'Secretariat') {
            show_error('Only Secretariat users can encode Interview and Written Examination scores.', 403, 'Forbidden');
            exit;
        }
    }

    private function user_id(): int
    {
        return (int) ($this->session->id ?? $this->session->userdata('id'));
    }

    private function encoding_mode($value): string
    {
        $value = strtolower(trim((string) $value));
        return in_array($value, ['written', 'interview', 'both'], true) ? $value : 'both';
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

        foreach ($this->secretariat->tagging_vacancies($userId) as $vacancy) {
            // Teaching and promotion sheets use other rating tables/criteria.
            if (in_array((int) $vacancy->position, [1, 5], true)) {
                continue;
            }

            $vacancies[] = $vacancy;
            if ((int) $vacancy->jobID === $jobId) {
                $selectedVacancy = $vacancy;
            }
        }

        if ($jobId > 0 && empty($selectedVacancy)) {
            $this->session->set_flashdata('danger', 'That vacancy is not available for Secretariat score encoding.');
            redirect($this->list_url(0, $mode));
            return;
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
            'applicants' => $applicants,
            'scoreCounts' => $this->secretariat->score_entry_counts($userId),
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
        foreach (['interview' => $interview, 'written' => $written] as $field => $value) {
            if ($value === null) {
                continue;
            }
            $label = $field === 'interview' ? 'Interview' : 'Written Examination';
            $this->Audit->log('rate', [
                'entity_type' => 'rating',
                'entity_id' => 'hris_rating_none',
                'app_id' => $appId,
                'applicant_id' => (string) $application->record_no,
                'job_id' => (int) $application->jobID,
                'field' => $field,
                'description' => 'Encoded ' . $label . ' rating: ' . $value,
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
            ]);
            return;
        }

        $this->session->set_flashdata('success', 'The score was saved and is now displayed on the MA page.');
        redirect($this->list_url($jobId, $mode));
    }
}
