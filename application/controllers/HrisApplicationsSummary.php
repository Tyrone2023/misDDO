<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class HrisApplicationsSummary extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('HrisApplicationsSummary_model', 'summary_model');
        $this->load->helper(array('url', 'form'));
        $this->load->library('session');
    }

    public function index()
    {
        $selected_sy       = $this->input->get('sy', TRUE);
        $selected_jobTitle = $this->input->get('jobTitle', TRUE);

        if (empty($selected_sy)) {
            $latest = $this->summary_model->get_latest_sy();
            $selected_sy = !empty($latest) ? $latest : '';
        }

        $data['title']             = 'Applications Summary by Position';
        $data['selected_sy']       = $selected_sy;
        $data['selected_jobTitle'] = $selected_jobTitle;
        $data['school_years']      = $this->summary_model->get_school_years();
        $data['job_titles']        = $this->summary_model->get_job_titles($selected_sy);
        $data['rows']              = $this->summary_model->get_summary_by_position($selected_sy, $selected_jobTitle);

        $this->load->view('Pages/applications_summary_view', $data);
    }
}