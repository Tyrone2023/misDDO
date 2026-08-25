<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Secretariat-managed "Field Evaluator" tagging.
 *
 * Tags an existing Evaluator account as the Field Evaluator of one of the
 * Secretariat's vacancies. The tag grants a vacancy-wide view (every applicant
 * of that vacancy plus the evaluator tagged to each one); it never assigns
 * applicants and never touches hris_rater_assignments.
 */
class SecretariatFieldEvaluator extends CI_Controller
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
            show_error('Only Secretariat users can manage Field Evaluator tagging.', 403, 'Forbidden');
            exit;
        }
    }

    private function user_id(): int
    {
        return (int) ($this->session->id ?? $this->session->userdata('id'));
    }

    private function post_only(): void
    {
        if (strtoupper((string) $this->input->method()) !== 'POST') {
            show_error('Method Not Allowed', 405);
            exit;
        }
    }

    private function list_url(int $jobId = 0): string
    {
        return base_url('secretariat/field-evaluators' . ($jobId > 0 ? '?job_id=' . $jobId : ''));
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
            redirect($this->list_url());
            return;
        }

        $data = [
            'title' => 'Field Evaluator Tagging',
            'vacancies' => $vacancies,
            'selectedVacancy' => $selectedVacancy,
            'selectedJobId' => $jobId,
            'taggedEvaluators' => $selectedVacancy
                ? $this->secretariat->field_evaluator_tags($userId, $jobId)
                : [],
            'evaluators' => $selectedVacancy
                ? $this->secretariat->taggable_field_evaluators()
                : [],
        ];

        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/secretariat_field_evaluators', $data);
        $this->load->view('templates/footer');
    }

    public function tag(): void
    {
        $this->guard();
        $this->post_only();

        $jobId = (int) $this->input->post('job_id');
        $evaluatorId = (int) $this->input->post('evaluator_id');

        if ($jobId <= 0 || $evaluatorId <= 0) {
            $this->session->set_flashdata('danger', 'Select an evaluator before saving the tag.');
            redirect($this->list_url($jobId));
            return;
        }

        $result = $this->secretariat->tag_field_evaluator(
            $this->user_id(),
            $jobId,
            $evaluatorId,
            $this->user_id()
        );

        if (!empty($result['ok'])) {
            $this->Audit->log('create', [
                'entity_type' => 'hris_field_evaluator_access',
                'entity_id' => $evaluatorId,
                'description' => 'Tagged ' . ($result['evaluator_name'] ?? ('user #' . $evaluatorId))
                    . ' as Field Evaluator of vacancy #' . $jobId,
            ]);
        }

        $this->session->set_flashdata(!empty($result['ok']) ? 'success' : 'danger', $result['message']);
        redirect($this->list_url($jobId));
    }

    public function untag(): void
    {
        $this->guard();
        $this->post_only();

        $jobId = (int) $this->input->post('job_id');
        $evaluatorId = (int) $this->input->post('evaluator_id');

        if ($jobId <= 0 || $evaluatorId <= 0) {
            $this->session->set_flashdata('danger', 'The Field Evaluator tag is missing.');
            redirect($this->list_url($jobId));
            return;
        }

        $result = $this->secretariat->untag_field_evaluator($this->user_id(), $jobId, $evaluatorId);

        if (!empty($result['ok'])) {
            $this->Audit->log('delete', [
                'entity_type' => 'hris_field_evaluator_access',
                'entity_id' => $evaluatorId,
                'description' => 'Removed the Field Evaluator tag of user #' . $evaluatorId
                    . ' on vacancy #' . $jobId,
            ]);
        }

        $this->session->set_flashdata(!empty($result['ok']) ? 'success' : 'danger', $result['message']);
        redirect($this->list_url($jobId));
    }
}
