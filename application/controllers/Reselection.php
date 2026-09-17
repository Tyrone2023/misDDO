<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Selective IER / RQA - second (and later) round selection.
 *
 * One vacancy can be deliberated more than once. The later round should not
 * carry the applicants already acted on, so HR picks by hand who belongs to it
 * here. The picked list is a batch; the existing IER and RQA reports are then
 * opened with ?batch={id} and print only those applicants.
 *
 * Reached from the vacancy list (Page/jobVacancy) action menu.
 */
class Reselection extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper('url');
        $this->load->model('Reselection_model', 'resel');

        if ($this->session->logged_in == false) {
            redirect(base_url() . 'log_in');
        }
    }

    /** Same roles that already maintain vacancies on Page/jobVacancy. */
    private function can_manage()
    {
        return in_array((string) $this->session->position, array(
            'Human Resource Admin',
            'HR Staff',
            'Super Admin',
            'asds',
            'sds'
        ), true);
    }

    private function guard()
    {
        if (!$this->can_manage()) {
            $this->session->set_flashdata('danger', 'You are not allowed to manage selective IER / RQA batches.');
            redirect(base_url() . 'Page/jobVacancy');
        }
    }

    private function job_or_redirect($jobID)
    {
        $job = $this->Common->one_cond_row('hris_jobvacancy', 'jobID', (int) $jobID);

        if (empty($job)) {
            $this->session->set_flashdata('danger', 'Job vacancy not found.');
            redirect(base_url() . 'Page/jobVacancy');
        }

        return $job;
    }

    private function batch_or_redirect($batch_id)
    {
        $batch = $this->resel->get_batch($batch_id);

        if (empty($batch)) {
            $this->session->set_flashdata('danger', 'Selection batch not found.');
            redirect(base_url() . 'Page/jobVacancy');
        }

        return $batch;
    }

    /** Group types as posted on Page/jobVacancy (hris_jobvacancy.job_type). */
    private function job_types()
    {
        return $this->resel->job_types();
    }

    /**
     * Report buttons of one batch. Each entry points at the report the vacancy
     * already uses on Pages/rqa_list, with ?batch= appended so only the picked
     * applicants are printed.
     */
    private function report_links($job, $batch_id)
    {
        $jobID = (int) $job->jobID;
        $q     = '?batch=' . (int) $batch_id;
        $base  = base_url() . 'Pages/';

        $ier = array(
            array('label' => 'IER', 'url' => $base . 'all_non_teaching_applicant/' . $jobID . $q),
            array('label' => 'IER V2', 'url' => $base . 'all_non_teaching_applicantv2/' . $jobID . $q),
            array('label' => 'IER V3', 'url' => $base . 'all_non_teaching_applicantv3/' . $jobID . $q),
            array('label' => 'IER V4', 'url' => $base . 'all_non_teaching_applicantv4/' . $jobID . $q)
        );

        if ((int) $job->promotion === 1) {
            $rqa = array(
                array('label' => 'RQA Printable View', 'url' => $base . 'car_rqa_promotion/' . $jobID . $q),
                array('label' => 'RQA For Posting', 'url' => $base . 'car_rqa1_promotion/' . $jobID . $q),
                array('label' => 'RQA For Region', 'url' => $base . 'car_rqa1_promotion_region/' . $jobID . $q)
            );
        } elseif ((int) $job->position === 1) {
            $rqa = array(
                array('label' => 'RQA Printable View', 'url' => $base . 'car_rqa/' . $jobID . $q),
                array('label' => 'RQA For Posting', 'url' => $base . 'car_rqa1/' . $jobID . $q)
            );
        } elseif ((int) $job->position === 2 || (int) $job->position === 4) {
            $rqa = array(
                array('label' => 'RQA Printable View', 'url' => $base . 'car_rqa_administrative/' . $jobID . $q),
                array('label' => 'RQA For Posting', 'url' => $base . 'car_rqa_administrative_posting/' . $jobID . $q),
                array('label' => 'RQA with Remarks', 'url' => $base . 'car_rqa_administrative_remarks/' . $jobID . $q),
                array('label' => 'RQA For Region', 'url' => $base . 'car_rqa_administrative_region/' . $jobID . $q)
            );
        } elseif ((int) $job->position === 3) {
            $rqa = array(
                array('label' => 'RQA Printable View', 'url' => $base . 'car_rqa_related/' . $jobID . $q),
                array('label' => 'RQA For Posting', 'url' => $base . 'car_rqa_related_posting/' . $jobID . $q),
                array('label' => 'RQA with Remarks', 'url' => $base . 'car_rqa_related_remarks/' . $jobID . $q)
            );
        } else {
            $rqa = array(
                array('label' => 'RQA Printable View', 'url' => $base . 'car_rqa_non/' . $jobID . $q),
                array('label' => 'RQA For Posting', 'url' => $base . 'car_rqa1_none/' . $jobID . $q),
                array('label' => 'RQA with Remarks', 'url' => $base . 'car_rqa_non_remarks/' . $jobID . $q)
            );
        }

        return array('ier' => $ier, 'rqa' => $rqa);
    }

    /**
     * No vacancy yet - every vacancy as a card, pick one to work on.
     */
    public function vacancies()
    {
        $this->guard();

        $all = (int) $this->input->get('all') === 1;

        $data['title']     = 'Selective IER / RQA';
        $data['all']       = $all;
        $data['vacancies'] = $this->resel->dashboard_vacancies($all);

        $this->load->view('reselection_dashboard', $data);
    }

    /**
     * Selection rounds of one vacancy. Without a jobID this is the vacancy
     * picker, so the sidebar can point at a single address.
     */
    public function index($jobID = null)
    {
        $this->guard();

        if ($jobID === null || $jobID === '') {
            $this->vacancies();
            return;
        }

        $jobID = (int) $jobID;
        $job   = $this->job_or_redirect($jobID);

        $data['job']       = $job;
        $data['title']     = 'Selective IER / RQA';
        $data['batches']   = $this->resel->get_batches($jobID);
        $data['next']      = $this->resel->next_round($jobID);
        $data['job_types'] = $this->job_types();
        $data['groups']    = $this->resel->position_groups();

        $links = array();
        foreach ($data['batches'] as $batch) {
            $links[(int) $batch->id] = $this->report_links($job, $batch->id);
        }
        $data['links'] = $links;

        $this->load->view('reselection_batches', $data);
    }
    /**
     * Creates a batch and goes straight to its applicant picker.
     */
    public function create()
    {
        $this->guard();

        $jobID = (int) $this->input->post('jobID');
        $this->job_or_redirect($jobID);

        $name = trim((string) $this->input->post('batch_name'));
        if ($name === '') {
            $this->session->set_flashdata('danger', 'Batch name is required.');
            redirect(base_url() . 'Reselection/index/' . $jobID);
        }

        $round = (int) $this->input->post('round_no');
        if ($round < 1) {
            $round = $this->resel->next_round($jobID);
        }

        $id = $this->resel->create_batch(array(
            'jobID'           => $jobID,
            'batch_name'      => mb_substr($name, 0, 200),
            'round_no'        => $round,
            'remarks'         => trim((string) $this->input->post('remarks')),
            'created_by'      => (int) $this->session->id,
            'created_by_name' => mb_substr((string) $this->session->user, 0, 150)
        ));

        $this->session->set_flashdata('success', 'Selection batch created. Pick the applicants included in this round.');
        redirect(base_url() . 'Reselection/select/' . (int) $id);
    }

    /**
     * Deletes a batch. Only the batch and its picked list go - the
     * applications and their ratings are untouched.
     */
    public function remove($id = null)
    {
        $this->guard();

        $batch = $this->batch_or_redirect($id);
        $jobID = (int) $batch->jobID;

        $this->resel->delete_batch($batch->id);

        $this->session->set_flashdata('success', 'Selection batch removed.');
        redirect(base_url() . 'Reselection/index/' . $jobID);
    }

    /**
     * Applicant picker of one batch, with the report buttons.
     */
    public function select($id = null)
    {
        $this->guard();

        $batch = $this->batch_or_redirect($id);
        $job   = $this->job_or_redirect($batch->jobID);

        $data['job']        = $job;
        $data['batch']      = $batch;
        $data['title']      = 'Selective IER / RQA';
        $data['rows']       = $this->resel->applicants($job);
        $data['picked']     = array_flip($this->resel->member_ids($batch->id));
        $data['used']       = $this->resel->used_ids($job->jobID, $batch->id);
        $data['links']      = $this->report_links($job, $batch->id);
        $data['job_types']  = $this->job_types();
        $data['groups']     = $this->resel->position_groups();

        $this->load->view('reselection_select', $data);
    }

    /**
     * Saves the picked applicants of a batch (AJAX from the picker).
     */
    public function save()
    {
        $this->guard();

        $id    = (int) $this->input->post('batch_id');
        $batch = $this->resel->get_batch($id);

        if (empty($batch)) {
            echo json_encode(array('status' => 'error', 'message' => 'Selection batch not found.'));
            return;
        }

        $appIDs = $this->input->post('appIDs');
        if (!is_array($appIDs)) {
            $appIDs = array();
        }

        $total = $this->resel->set_members($id, $appIDs);

        echo json_encode(array(
            'status'  => 'success',
            'total'   => $total,
            'message' => $total . ' applicant(s) saved for this selection.'
        ));
    }
}
