<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Field Evaluator workspace.
 *
 * An Evaluator tagged by a Secretariat as the Field Evaluator of a vacancy
 * gets a vacancy-wide list here: every applicant of that vacancy, the
 * evaluator tagged to each one, and a link into the application. Rating stays
 * gated on the evaluator's own hris_rater_assignments rows.
 */
class FieldEvaluator extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper('url');
        $this->load->model('Secretariat_model', 'secretariat');
    }

    private function user_id(): int
    {
        return (int) ($this->session->id ?? $this->session->userdata('id'));
    }

    private function guard(): void
    {
        $position = (string) $this->session->userdata('position');

        if (!in_array($position, ['Evaluator', 'rater', 'raters'], true)) {
            show_error('Only evaluator accounts can open the Field Evaluator workspace.', 403, 'Forbidden');
            exit;
        }

        if (!$this->secretariat->is_field_evaluator($this->user_id())) {
            show_error('Your account is not tagged as a Field Evaluator for any vacancy.', 403, 'Forbidden');
            exit;
        }
    }

    public function index(): void
    {
        $this->guard();

        $evaluatorId = $this->user_id();
        $jobId = (int) $this->input->get('job_id');
        $vacancies = $this->secretariat->field_evaluator_vacancies($evaluatorId);
        $selectedVacancy = null;

        foreach ($vacancies as $vacancy) {
            if ((int) $vacancy->jobID === $jobId) {
                $selectedVacancy = $vacancy;
                break;
            }
        }

        // A single tagged vacancy needs no picking - open it straight away.
        if (!$selectedVacancy && $jobId <= 0 && count($vacancies) === 1) {
            $selectedVacancy = $vacancies[0];
            $jobId = (int) $selectedVacancy->jobID;
        }

        if ($jobId > 0 && !$selectedVacancy) {
            $this->session->set_flashdata('danger', 'You are not tagged as Field Evaluator for that vacancy.');
            redirect(base_url('field-evaluator'));
            return;
        }

        $data = [
            'title' => 'All Applicants',
            'vacancies' => $vacancies,
            'selectedVacancy' => $selectedVacancy,
            'selectedJobId' => $jobId,
            'applicants' => $selectedVacancy
                ? $this->secretariat->field_evaluator_applicants($evaluatorId, $jobId)
                : [],
            'evaluatorId' => $evaluatorId,
        ];

        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/field_evaluator_applicants', $data);
        $this->load->view('templates/footer');
    }
}
