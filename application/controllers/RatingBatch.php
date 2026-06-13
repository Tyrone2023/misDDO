<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class RatingBatch extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper('url');

        // Shared models used inside the view and during processing
        $this->load->model('Common');
        $this->load->model('Reg');
        $this->load->model('RatingBatch_model', 'ratingBatch');
    }

    public function pending_report()
    {
        $fy = date('Y');
        $data = [
            'title' => 'Pending Rating Requests Report',
            'rows' => $this->ratingBatch->get_pending_with_history($fy),
        ];

        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/rating_request_pending_report', $data);
        $this->load->view('templates/footer');
    }

    public function index()
    {
        $fy = date('Y');

        $data = [
            'title' => 'Mass Retained Rating',
            'data' => $this->ratingBatch->get_requests_by_status($fy, 0),
            'granted' => $this->ratingBatch->get_requests_by_status($fy, 1),
            'level_buttons' => $this->ratingBatch->get_level_buttons($fy),
            'zero_retention' => $this->ratingBatch->get_retention_zero_scores($fy),
        ];

        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/rating_request_mass', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Standalone view for approved retention requests with placeholder ratings.
     * Useful for printing or focused review.
     */
    public function retention_placeholders()
    {
        $fyInput = $this->input->get('fy');
        $fy = is_numeric($fyInput) ? (int)$fyInput : (int)date('Y');
        $fyOptions = $this->ratingBatch->get_request_years();
        $sourceFyInput = $this->input->get('source_fy');
        $defaultSourceFy = $fy - 1;
        $sourceFy = is_numeric($sourceFyInput) ? (int)$sourceFyInput : (in_array($defaultSourceFy, $fyOptions) ? $defaultSourceFy : $fy);
        $data = [
            'title' => 'Approved Retentions With Placeholder Ratings',
            'zero_retention' => $this->ratingBatch->get_retention_zero_scores($fy),
            'selected_fy' => $fy,
            'fy_options' => $fyOptions,
            'selected_source_fy' => $sourceFy,
            'jobTypes' => [
                1 => '- Elementary',
                2 => '- Secondary',
                3 => '- Junior High School',
                4 => '- Senior High School',
                5 => '- Kindergarten',
                6 => '- IPED Elementary',
                7 => '- IPED Secondary',
                8 => '- IPED Junior High School',
                9 => '- IPED Senior High School',
                10 => '- SNED',
            ],
        ];

        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/rating_retention_zero', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Fill in placeholder ratings (0/0.00001) for the chosen fiscal year
     * using the latest available rating per applicant / job level.
     */
    public function update_placeholder_ratings()
    {
        $fyInput = $this->input->post('fy');
        $sourceFyInput = $this->input->post('source_fy');
        $fy = is_numeric($fyInput) ? (int)$fyInput : (int)date('Y');
        $sourceFy = is_numeric($sourceFyInput) ? (int)$sourceFyInput : null;

        $summary = $this->ratingBatch->fill_placeholder_ratings(
            $fy,
            $sourceFy,
            $this->session->id ?? $this->session->userdata('id')
        );

        if ($summary['updated'] > 0) {
            $this->session->set_flashdata(
                'success',
                sprintf(
                    'Target FY %d from source FY %s: updated %d rating row(s) (created %d, updated %d, stubbed %d). Skipped %d without source rating, %d without profile%s.',
                    $fy,
                    $sourceFy ? $sourceFy : 'any',
                    $summary['updated'],
                    $summary['created_missing'],
                    $summary['updated_existing'],
                    $summary['created_stub'],
                    $summary['skipped_no_source_rating'],
                    $summary['skipped_no_profile'],
                    ($summary['skipped_no_job_type'] ?? 0) ? ', '.$summary['skipped_no_job_type'].' missing job type' : ''
                )
            );
        } else {
            $this->session->set_flashdata(
                'danger',
                sprintf(
                    'Target FY %d from source FY %s: no placeholder ratings were updated. Missing source rating: %d, missing profile: %d%s.',
                    $fy,
                    $sourceFy ? $sourceFy : 'any',
                    $summary['skipped_no_source_rating'],
                    $summary['skipped_no_profile'],
                    ($summary['skipped_no_job_type'] ?? 0) ? ', missing job type: '.$summary['skipped_no_job_type'] : ''
                )
            );
        }

        $qs = http_build_query(['fy' => $fy, 'source_fy' => $sourceFy]);
        redirect(base_url('RatingBatch/retention_placeholders?'.$qs));
    }

    public function accept_level($level = null)
    {
        $fy = date('Y');
        $levelKey = $level ?? $this->input->post('level');
        $buttons = $this->ratingBatch->get_level_buttons($fy);

        // $levelKey can be job_type int or key; support both.
        if (!$levelKey) {
            $this->session->set_flashdata('danger', 'Please choose a valid level to process.');
            redirect(base_url('RatingBatch'));
        }

        // Normalise numeric job_type input
        if (is_numeric($levelKey)) {
            $levelKey = (int)$levelKey;
        }

        // If provided as numeric job_type but buttons keyed by type int, accept it
        $cfg = $buttons[$levelKey] ?? null;

        if (!$cfg) {
            $this->session->set_flashdata('danger', 'Please choose a valid level to process.');
            redirect(base_url('RatingBatch'));
        }

        $map = $cfg;
        $summary = $this->ratingBatch->mass_accept_by_level(
            $map['types'],
            $this->session->id ?? $this->session->userdata('id')
        );

        if ($summary['accepted'] > 0) {
            $this->session->set_flashdata(
                'success',
                sprintf(
                    '%s: accepted %d of %d pending requests. Skipped %d (no matching previous rating: %d, prior app unrated/validated: %d, missing profile: %d, missing application: %d, already rated reused: %d).',
                    $map['label'],
                    $summary['accepted'],
                    $summary['checked'],
                    $summary['skipped'],
                    $summary['skipped_no_previous'],
                    $summary['skipped_unrated_prior'],
                    $summary['skipped_no_profile'],
                    $summary['skipped_no_application'],
                    $summary['accepted_existing_rating']
                )
            );
        } else {
            $this->session->set_flashdata(
                'danger',
                sprintf(
                    '%s: no requests were processed. Skipped %d (no matching previous rating: %d, prior app unrated/validated: %d, missing profile: %d, missing application: %d, already rated reused: %d).',
                    $map['label'],
                    $summary['skipped'],
                    $summary['skipped_no_previous'],
                    $summary['skipped_unrated_prior'],
                    $summary['skipped_no_profile'],
                    $summary['skipped_no_application'],
                    $summary['accepted_existing_rating']
                )
            );
        }

        redirect(base_url('RatingBatch'));
    }
}
