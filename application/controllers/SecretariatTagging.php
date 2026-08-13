<?php
defined('BASEPATH') or exit('No direct script access allowed');

class SecretariatTagging extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper(['url', 'form']);
        $this->load->model('Secretariat_model', 'secretariat');
    }

    private function guard(): void
    {
        if ($this->session->userdata('position') !== 'Secretariat') {
            show_error('Only Secretariat users can access applicant tagging.', 403, 'Forbidden');
            exit;
        }
    }

    private function user_id(): int
    {
        return (int) ($this->session->id ?? $this->session->userdata('id'));
    }

    private function is_ajax_request(): bool
    {
        return strtolower((string) $this->input->server('HTTP_X_REQUESTED_WITH')) === 'xmlhttprequest'
            || strpos(strtolower((string) $this->input->server('HTTP_ACCEPT')), 'application/json') !== false;
    }

    public function index(): void
    {
        $this->guard();

        $userId = $this->user_id();
        $jobId = (int) $this->input->get('job_id');
        $vacancies = $this->secretariat->tagging_vacancies($userId);
        $selectedVacancy = null;

        foreach ($vacancies as $vacancy) {
            if ((int) $vacancy->jobID === $jobId) {
                $selectedVacancy = $vacancy;
                break;
            }
        }

        if ($jobId > 0 && !$selectedVacancy) {
            $this->session->set_flashdata('danger', 'That vacancy is not assigned to your Secretariat account.');
            redirect(base_url('secretariat/applicant-tagging'));
            return;
        }

        $applicants = $selectedVacancy
            ? $this->secretariat->applicants_for_tagging($userId, $jobId)
            : [];

        $data = [
            'title' => 'Applicant Evaluator Tagging',
            'vacancies' => $vacancies,
            'selectedVacancy' => $selectedVacancy,
            'selectedJobId' => $jobId,
            'applicants' => $applicants,
            'evaluators' => $this->secretariat->eligible_evaluators(
                $selectedVacancy ? (int) $selectedVacancy->sy : (int) date('Y')
            ),
        ];

        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/secretariat_applicant_tagging', $data);
        $this->load->view('templates/footer');
    }

    public function tag(): void
    {
        $this->guard();

        if (strtoupper((string) $this->input->method()) !== 'POST') {
            show_error('Method Not Allowed', 405);
            return;
        }

        $appId = (int) $this->input->post('app_id');
        $jobId = (int) $this->input->post('job_id');
        $raterId = (int) $this->input->post('rater_id');

        if ($appId <= 0 || $jobId <= 0 || $raterId <= 0) {
            $result = ['ok' => false, 'message' => 'Select an evaluator before saving the tag.'];
        } else {
            $result = $this->secretariat->tag_applicant_to_evaluator(
                $this->user_id(),
                $appId,
                $jobId,
                $raterId,
                $this->user_id()
            );
        }

        if ($this->is_ajax_request()) {
            $this->output
                ->set_status_header(!empty($result['ok']) ? 200 : 422)
                ->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode($result));
            return;
        }

        $this->session->set_flashdata(!empty($result['ok']) ? 'success' : 'danger', $result['message']);
        redirect(base_url('secretariat/applicant-tagging?job_id=' . $jobId));
    }
}
