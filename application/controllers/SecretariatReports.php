<?php
defined('BASEPATH') or exit('No direct script access allowed');

class SecretariatReports extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper(['url', 'form']);
        $this->load->model('Secretariat_model', 'secretariat');
        $this->load->model('SettingsModel');

        // Keeps the lookups these sheets depend on index-backed.
        $this->secretariat->ensure_report_indexes();
    }

    private function guard(): void
    {
        if ($this->session->userdata('position') !== 'Secretariat') {
            show_error('Only Secretariat users can access recruitment reports.', 403, 'Forbidden');
            exit;
        }
    }

    private function user_id(): int
    {
        return (int) ($this->session->id ?? $this->session->userdata('id'));
    }

    /**
     * A report may only be opened for a vacancy assigned to this Secretariat
     * account, the same rule the tagging and scores screens use.
     */
    private function vacancy(int $jobId)
    {
        if ($jobId <= 0 || !$this->secretariat->secretariat_has_vacancy($this->user_id(), $jobId)) {
            show_error('That vacancy is not assigned to your Secretariat account.', 403, 'Forbidden');
            exit;
        }

        return $this->Common->one_cond_row('hris_jobvacancy', 'jobID', $jobId);
    }

    public function shortlist($jobId = 0): void
    {
        $this->render_shortlist((int) $jobId, false);
    }

    public function shortlist_contact($jobId = 0): void
    {
        $this->render_shortlist((int) $jobId, true);
    }

    private function render_shortlist(int $jobId, bool $withContact): void
    {
        $this->guard();

        $job = $this->vacancy($jobId);

        $data = [
            'title' => 'Shortlist',
            'mis_settings' => $this->SettingsModel->mis_settings(),
            'job' => $job,
            'jobID' => $jobId,
            'withContact' => $withContact,
            'applicants' => $this->secretariat->shortlist_applicants($this->user_id(), $jobId),
        ];

        $this->load->view('pages/secretariat_shortlist', $data);
    }

    /**
     * Same IER sheet as Pages/all_non_teaching_applicantv3, trimmed to the
     * Qualified rows only (dq = 1), so disqualified applicants are left out.
     */
    public function ier($jobId = 0): void
    {
        $this->guard();

        $jobId = (int) $jobId;
        $this->vacancy($jobId);

        $data = [
            'title' => 'Initial Evaluation Result (IER)',
            'mis_settings' => $this->SettingsModel->mis_settings(),
            'data' => $this->secretariat->ier_applicants($this->user_id(), $jobId),
        ];

        $this->load->view('pages/ha_all_by_jp_v2', $data);
    }
}
