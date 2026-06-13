<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AssignRater extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper(['url', 'form']);
        $this->load->model('AssignRater_model', 'assignRater');
    }

    private function guard()
    {
        if ($this->session->userdata('position') !== 'Super Admin') {
            show_error('Only Super Admin users can access this page.', 403, 'Forbidden');
            exit;
        }
    }

    public function index()
    {
        $this->guard();
        $fy = (int) date('Y');

        $data = [
            'title' => 'Assign Applicants to Rater',
            'levels' => $this->assignRater->get_level_summary($fy),
            'specializations' => $this->assignRater->get_specialization_summary($fy, [4, 9]),
            'raters' => $this->assignRater->get_raters(),
            'rater_load' => $this->assignRater->get_rater_load($fy),
            'recent' => $this->assignRater->get_recent_assignments($fy, 25),
            'fy' => $fy,
        ];

        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/assign_applicant_rater', $data);
        $this->load->view('templates/footer');
    }

    public function assign()
    {
        $this->guard();
        $fy = (int) date('Y');

        $jobType = (int) $this->input->post('job_type');
        $raterId = (int) $this->input->post('rater_id');
        $count = (int) $this->input->post('count');
        $spec = trim((string) $this->input->post('specialization'));

        if ($jobType === 0 || $raterId === 0) {
            $this->session->set_flashdata('danger', 'Please choose a level and rater.');
            redirect(base_url('AssignRater'));
        }

        if (!$this->assignRater->rater_is_valid($raterId)) {
            $this->session->set_flashdata('danger', 'Selected rater is not eligible (Evaluator, egroup 1 only).');
            redirect(base_url('AssignRater'));
        }

        $count = max(1, $count);
        $assignedBy = $this->session->id ?? $this->session->userdata('id');

        $added = $this->assignRater->assign_requests($fy, $jobType, $raterId, $count, $spec, $assignedBy);

        if ($added > 0) {
            $this->session->set_flashdata('success', "Assigned {$added} applicant(s) to the selected rater.");
        } else {
            $this->session->set_flashdata('danger', 'No available applicants to assign for that level/specialization.');
        }

        redirect(base_url('AssignRater'));
    }
}
