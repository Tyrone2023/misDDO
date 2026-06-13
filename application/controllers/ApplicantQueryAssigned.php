<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ApplicantQueryAssigned extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper('url');
        $this->load->model('Common');
    }

    private function current_user_id()
    {
        return $this->session->id ?? $this->session->userdata('id');
    }

    private function guard()
    {
        $pos = $this->session->userdata('position');
        if (!in_array($pos, ['Evaluator', 'rater', 'raters', 'District', 'doceval', 'Admin', 'Super Admin'])) {
            show_error('Forbidden', 403);
            exit;
        }
    }

    private function filter_assigned_queries($rows, $appIdLookup)
    {
        $filtered = [];

        foreach ($rows as $row) {
            $appId = trim((string)($row->application_id ?? ''));
            if ($appId === '' || !isset($appIdLookup[$appId])) {
                continue;
            }

            // De-duplicate by applicant number (record_no) so we only show one row per applicant.
            $recordNo = trim((string)($row->record_no ?? ''));

            if ($recordNo === '') {
                // Keep rows with no applicant number as-is.
                $filtered[] = $row;
                continue;
            }

            if (!isset($filtered[$recordNo])) {
                $filtered[$recordNo] = $row;
                continue;
            }

            // If a duplicate applicant number exists, keep the most recent query date.
            $currentDate  = strtotime((string)($row->idate ?? ''));
            $existingDate = strtotime((string)($filtered[$recordNo]->idate ?? ''));

            if ($currentDate !== false && $existingDate !== false && $currentDate > $existingDate) {
                $filtered[$recordNo] = $row;
            }
        }

        return array_values($filtered);
    }

    public function index()
    {
        $this->guard();

        $userId = $this->current_user_id();
        if (!$userId) {
            redirect(base_url('log_in'));
            return;
        }

        $assignedRows = $this->db
            ->select('app_id')
            ->distinct()
            ->from('hris_rater_assignments')
            ->where('rater_user_id', $userId)
            ->where('app_id IS NOT NULL', null, false)
            ->get()
            ->result();

        $appIdLookup = [];
        foreach ($assignedRows as $row) {
            $appId = trim((string)($row->app_id ?? ''));
            if ($appId !== '') {
                $appIdLookup[$appId] = true;
            }
        }

        $filtered = [];
        $finalized = [];
        if (!empty($appIdLookup)) {
            $filtered = $this->filter_assigned_queries(
                $this->Common->get_applications_with_rating_status(0),
                $appIdLookup
            );

            $finalized = $this->filter_assigned_queries(
                $this->Common->get_applications_with_rating_status(1),
                $appIdLookup
            );
        }

        $data = [
            'title'          => 'My Assigned Applicant Queries',
            'data'           => $filtered,
            'finalized_data' => $finalized,
        ];

        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/applicant_query_assigned', $data);
        $this->load->view('templates/footer');
    }
}
