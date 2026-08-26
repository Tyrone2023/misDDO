<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Qualified / Disqualified applicant lists for one vacancy assigned to the
 * signed-in Secretariat account. Same vacancy scoping as applicant tagging.
 */
class SecretariatQualification extends CI_Controller
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
            show_error('Only Secretariat users can access this list.', 403, 'Forbidden');
            exit;
        }
    }

    private function user_id(): int
    {
        return (int) ($this->session->id ?? $this->session->userdata('id'));
    }

    public function qualified(): void
    {
        $this->render(1);
    }

    public function disqualified(): void
    {
        $this->render(2);
    }

    private function render(int $dq): void
    {
        $this->guard();

        $mode = ($dq === 1) ? 'qualified' : 'disqualified';
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
            redirect(base_url('secretariat/' . $mode));
            return;
        }

        $applicants = $selectedVacancy
            ? $this->secretariat->qualification_applicants($userId, $jobId, $dq)
            : [];

        $data = [
            'title' => ($dq === 1) ? 'Qualified Applicants' : 'Disqualified Applicants',
            'mode' => $mode,
            'vacancies' => $vacancies,
            'selectedVacancy' => $selectedVacancy,
            'selectedJobId' => $jobId,
            'jobTypeLabels' => $this->secretariat->job_types_map(),
            'applicants' => $applicants,
            // One query for the whole list, so each row knows whether its
            // documents were already issued and released.
            'issuedDocs' => $this->secretariat->issued_documents(
                array_map(static function ($applicant) { return (int) $applicant->appID; }, $applicants)
            ),
        ];

        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/secretariat_qualification', $data);
        $this->load->view('templates/footer');
    }

    /* ------------------------------------------------------------------ *
     * Issued applicant documents: the Evaluative Assessment for both lists
     * and, for a disqualified applicant, the letter of non-compliance.
     * ------------------------------------------------------------------ */

    private function json($payload, int $status = 200): void
    {
        $this->output
            ->set_status_header($status)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($payload));
    }

    private function doc_type(string $value): string
    {
        return array_key_exists($value, $this->secretariat->assessment_types()) ? $value : 'assessment';
    }

    /**
     * Document name as the division's forms label them: the Evaluative
     * Assessment is Annex E for a qualified applicant and Annex F for a
     * disqualified one.
     */
    private function doc_title($ctx, string $docType): string
    {
        $title = $this->secretariat->assessment_types()[$docType];

        if ($docType === 'assessment') {
            $title .= ((int) ($ctx->dq ?? 0) === 2) ? ' (Annex F)' : ' (Annex E)';
        }

        return $title;
    }

    /**
     * The context row behind a document, after checking the vacancy belongs to
     * this Secretariat account. Returns null when it does not.
     */
    private function assessment_context_for_user(int $appId)
    {
        $ctx = $this->secretariat->assessment_context($appId);

        if (empty($ctx) || !$this->secretariat->secretariat_has_vacancy($this->user_id(), (int) $ctx->jobID)) {
            return null;
        }

        return $ctx;
    }

    /** Save the edited document. */
    public function assessment_save(): void
    {
        $this->guard();

        if (strtoupper((string) $this->input->method()) !== 'POST') {
            $this->json(['ok' => false, 'message' => 'Method Not Allowed'], 405);
            return;
        }

        $appId = (int) $this->input->post('app_id');
        $docType = $this->doc_type((string) $this->input->post('doc'));
        $ctx = $this->assessment_context_for_user($appId);

        if (!$ctx) {
            $this->json(['ok' => false, 'message' => 'That application is not part of a vacancy assigned to your account.'], 403);
            return;
        }

        $body = json_decode((string) $this->input->post('body'), true);

        if (!is_array($body)) {
            $this->json(['ok' => false, 'message' => 'The document could not be read. Please reload and try again.'], 422);
            return;
        }

        $clean = $this->clean_document_body($body, $docType);

        if (!$this->secretariat->save_assessment($appId, (int) $ctx->jobID, $docType, $clean, $this->user_id())) {
            $this->json(['ok' => false, 'message' => 'The document could not be saved. Please try again.'], 500);
            return;
        }

        $this->json(['ok' => true, 'message' => 'Document saved.']);
    }

    /** Release the document to the applicant, or take it back. */
    public function assessment_release(): void
    {
        $this->guard();

        if (strtoupper((string) $this->input->method()) !== 'POST') {
            $this->json(['ok' => false, 'message' => 'Method Not Allowed'], 405);
            return;
        }

        $appId = (int) $this->input->post('app_id');
        $docType = $this->doc_type((string) $this->input->post('doc'));
        $release = (int) $this->input->post('released') === 1;
        $ctx = $this->assessment_context_for_user($appId);

        if (!$ctx) {
            $this->json(['ok' => false, 'message' => 'That application is not part of a vacancy assigned to your account.'], 403);
            return;
        }

        if (!$this->secretariat->set_assessment_release($appId, $docType, $release)) {
            $this->json(['ok' => false, 'message' => 'Save the document first, then release it to the applicant.'], 422);
            return;
        }

        $this->json([
            'ok'       => true,
            'released' => $release,
            'message'  => $release
                ? 'Released. The applicant can now open this document from their application list.'
                : 'Withdrawn. The applicant can no longer open this document.',
        ]);
    }

    /**
     * Keep only the keys the document actually defines, as plain text. Anything
     * the browser adds beyond that is dropped rather than stored.
     */
    private function clean_document_body(array $body, string $docType): array
    {
        $text = static function ($value) {
            $value = preg_replace('/\x{00a0}/u', ' ', (string) $value);
            $value = str_replace(["\r\n", "\r"], "\n", $value);
            return trim(preg_replace('/[ \t]+/', ' ', $value));
        };

        if ($docType === 'letter') {
            $fields = ['office', 'date', 'applicant', 'position_line', 'address', 'salutation',
                'greeting', 'body1', 'body2', 'body3', 'body4', 'body5', 'closing',
                'signatory', 'signatory_title', 'signatory_role'];
            $cells = ['requirement', 'remarks'];
        } else {
            $fields = ['annex', 'office', 'date', 'applicant', 'address1', 'address2', 'salutation',
                'greeting', 'intro', 'item_no', 'body2', 'body3', 'thanks', 'closing',
                'signatory', 'signatory_title'];
            $cells = ['criterion', 'qs', 'yours', 'remarks'];
        }

        $clean = [];
        foreach ($fields as $field) {
            $clean[$field] = $text($body[$field] ?? '');
        }

        $clean['items'] = [];
        foreach ((array) ($body['items'] ?? []) as $item) {
            if (!is_array($item)) {
                continue;
            }

            $row = [];
            $hasValue = false;
            foreach ($cells as $cell) {
                $row[$cell] = $text($item[$cell] ?? '');
                if ($row[$cell] !== '') {
                    $hasValue = true;
                }
            }

            if ($hasValue) {
                $clean['items'][] = $row;
            }
        }

        return $clean;
    }

    /**
     * The printable A4 document. Open to the Secretariat that owns the vacancy,
     * and to the applicant themselves once the document has been released.
     *
     * application-document/{appID}/{assessment|letter}
     */
    public function document($appId = 0, $docType = 'assessment'): void
    {
        $appId = (int) $appId;
        $docType = $this->doc_type((string) $docType);
        $ctx = $this->secretariat->assessment_context($appId);

        if (empty($ctx)) {
            show_404();
            return;
        }

        $position = (string) $this->session->userdata('position');
        $isSecretariat = ($position === 'Secretariat')
            && $this->secretariat->secretariat_has_vacancy($this->user_id(), (int) $ctx->jobID);

        // The applicant's own copy, matched the same way their application list
        // is scoped, and only once the Secretariat has released it.
        $isOwner = in_array($position, ['reg', 'user'], true)
            && strcasecmp(trim((string) $this->session->userdata('username')), trim((string) $ctx->empEmail)) === 0;

        if (!$isSecretariat && !$isOwner) {
            show_error('You are not allowed to open this document.', 403, 'Forbidden');
            return;
        }

        if ($docType === 'letter' && (int) $ctx->dq !== 2) {
            show_error('The letter of non-compliance applies to disqualified applicants only.', 404);
            return;
        }

        $state = $this->secretariat->assessment_document($ctx, $docType);

        if ($isOwner && !$state['released']) {
            show_error('This document has not been released yet.', 403, 'Forbidden');
            return;
        }

        $this->load->model('SettingsModel');
        $signatory = $this->secretariat->assessment_signatory();

        // The e-signature belongs to one person. If the Secretariat typed a
        // different name into the signature block, print the name alone rather
        // than somebody else's signature above it.
        $signedBy = strtoupper(trim((string) ($state['doc']['signatory'] ?? '')));
        $esig = ($signedBy !== '' && $signedBy === strtoupper(trim((string) $signatory['name'])))
            ? $signatory['esig']
            : '';

        $this->load->view('pages/secretariat_assessment_page', [
            'title'        => $this->doc_title($ctx, $docType),
            'doc'          => $state['doc'],
            'docType'      => $docType,
            'esig'         => $esig,
            'editable'     => $isSecretariat,
            'appId'        => $appId,
            'applicant'    => $this->secretariat->assessment_applicant_name($ctx),
            'saved'        => $state['saved'],
            'released'     => $state['released'],
            'mis_settings' => $this->SettingsModel->mis_settings(),
        ]);
    }
}
