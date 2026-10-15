
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * MISDDO - Report on Appointments Issued (RAI)
 *
 * Responsibilities:
 * - CRUD for RAI report headers.
 * - Manage applicants on a saved report (system selection or manual).
 * - Search appointed applicants.
 * - Render the Folio landscape report preview.
 *
 * Report headers and report-only applicant rows are stored in
 * hris_rai_reports / hris_rai_report_applicants.
 */
class RaiReport extends CI_Controller
{
    private $allowed_roles = array(
        'sds',
        'asst_sds',
        'HRMO',
        'Human Resource Admin',
        'asds',
        'Secretariat'
    );

    private $max_applicants = 200;

    public function __construct()
    {
        parent::__construct();

        if (!$this->session->userdata('logged_in')) {
            redirect(base_url('log_in'));
            exit;
        }

        if (!$this->can_access()) {
            show_error(
                'You are not authorized to access RAI Reports.',
                403
            );
            exit;
        }

        $this->load->database();
        $this->load->model('RaiReport_model', 'rai');
        $this->load->helper(array('url', 'form'));
    }

    /**
     * Check user role.
     */
    private function can_access()
    {
        $position = (string) $this->session->userdata('position');

        return in_array($position, $this->allowed_roles, true);
    }

    /**
     * Require GET request.
     */
    private function require_get()
    {
        if (strtoupper($this->input->method()) !== 'GET') {
            show_error('Method Not Allowed', 405);
            exit;
        }
    }

    /**
     * Require POST request.
     */
    private function require_post()
    {
        if (strtoupper($this->input->method()) !== 'POST') {
            show_error('Method Not Allowed', 405);
            exit;
        }
    }

    /** True when the request was issued via XMLHttpRequest (AJAX). */
    private function is_ajax()
    {
        return strtolower((string) $this->input->server('HTTP_X_REQUESTED_WITH')) === 'xmlhttprequest';
    }

    /**
     * Generate or retrieve a session-bound token.
     */
    private function form_token()
    {
        $token = $this->session->userdata('rai_preview_token');

        if (!is_string($token) || strlen($token) !== 64) {
            $token = bin2hex(random_bytes(32));

            $this->session->set_userdata(
                'rai_preview_token',
                $token
            );
        }

        return $token;
    }

    /**
     * Validate preview request token.
     */
    private function verify_form_token()
    {
        $expected = $this->form_token();
        $received = (string) $this->input->post('rai_token');

        return $received !== ''
            && hash_equals($expected, $received);
    }

    /**
     * Send JSON response.
     */
    private function json_response($data, $status = 200)
    {
        $this->output
            ->set_status_header($status)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode(
                $data,
                JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE
            ));
    }

    /**
     * ------------------------------------------------------
     * RAI REPORT LIST (CRUD)
     * ------------------------------------------------------
     *
     * URL:
     * /RaiReport
     *
     * Lists saved report headers. Each row can be edited, deleted,
     * or opened in the Manage screen where applicants are added and
     * the report can be previewed.
     */
    public function index()
    {
        $this->require_get();

        $data = array(
            'title'        => 'RAI Reports',
            'pageTitle'    => 'Report on Appointments Issued (RAI)',
            'reports'      => $this->rai->list_reports(),
            'defaults'     => $this->rai->get_report_defaults(),
            'natureOptions'=> $this->rai->nature_options(),
            'raiToken'     => $this->form_token(),
            'createUrl'    => site_url('RaiReport/create'),
            'storeUrl'     => site_url('RaiReport/store'),
            'updateUrl'    => site_url('RaiReport/update'),
            'dataUrl'      => site_url('RaiReport/data'),
            'deleteUrl'    => site_url('RaiReport/delete'),
            'candidatesUrl'=> site_url('RaiReport/candidates'),
            'bulkCandidatesUrl' => site_url('RaiReport/bulk_candidates'),
            'saveApplicantsUrl' => site_url('RaiReport/save_applicants'),
            'saveChecklistUrl' => site_url('RaiReport/save_checklist'),
            'savePreviewUrl' => site_url('RaiReport/save_preview'),
            'checklistDefaults' => $this->rai->checklist_defaults(),
            'previewUrl'   => site_url('RaiReport/preview'),
            'manageUrl'    => site_url('RaiReport/manage'),
            'editUrl'      => site_url('RaiReport/edit')
        );

        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/rai_report_index', $data);
        $this->load->view('templates/footer');
    }

    /** New report information form. */
    public function create()
    {
        $this->require_get();
        $this->render_form('create', null);
    }

    /** Persist a new report header. AJAX-aware (JSON when X-Requested-With). */
    public function store()
    {
        $this->require_post();

        $input = $this->collect_report_input();

        if (isset($input['errors'])) {
            if ($this->is_ajax()) {
                $this->json_response(array(
                    'status'  => 'error',
                    'message' => implode(' ', $input['errors']),
                    'fields'  => $input['values']
                ), 422);
                return;
            }
            $this->session->set_flashdata('rai_report_old', $input['values']);
            $this->fail(implode(' ', $input['errors']), site_url('RaiReport/create'));
            return;
        }

        $report_id = $this->rai->create_report(
            $input['values'],
            (int) $this->session->userdata('id')
        );

        if ($report_id < 1) {
            if ($this->is_ajax()) {
                $this->json_response(array(
                    'status'  => 'error',
                    'message' => 'The report could not be saved. Please try again.'
                ), 500);
                return;
            }
            $this->fail('The report could not be saved. Please try again.', site_url('RaiReport/create'));
            return;
        }

        if ($this->is_ajax()) {
            $this->json_response(array(
                'status'    => 'success',
                'message'   => 'Report information saved.',
                'report_id' => $report_id,
                'report'    => $this->rai->get_report($report_id)
            ));
            return;
        }

        $this->session->set_flashdata('success', 'Report information saved. Add applicants below.');
        redirect(site_url('RaiReport/manage/' . $report_id));
    }

    /** Edit an existing report header. */
    public function edit($report_id = 0)
    {
        $this->require_get();
        $report = $this->rai->get_report((int) $report_id);

        if (empty($report)) {
            $this->session->set_flashdata('danger', 'That RAI report was not found.');
            redirect(site_url('RaiReport'));
            return;
        }

        $this->render_form('edit', $report);
    }

    /** Persist edits to a report header. AJAX-aware (JSON when X-Requested-With). */
    public function update($report_id = 0)
    {
        $this->require_post();

        $report_id = (int) $report_id;
        $report = $this->rai->get_report($report_id);

        if (empty($report)) {
            if ($this->is_ajax()) {
                $this->json_response(array('status' => 'error', 'message' => 'That RAI report was not found.'), 404);
                return;
            }
            $this->session->set_flashdata('danger', 'That RAI report was not found.');
            redirect(site_url('RaiReport'));
            return;
        }

        $input = $this->collect_report_input();

        if (isset($input['errors'])) {
            if ($this->is_ajax()) {
                $this->json_response(array(
                    'status'  => 'error',
                    'message' => implode(' ', $input['errors']),
                    'fields'  => $input['values']
                ), 422);
                return;
            }
            $this->session->set_flashdata('rai_report_old', $input['values']);
            $this->fail(implode(' ', $input['errors']), site_url('RaiReport/edit/' . $report_id));
            return;
        }

        $this->rai->update_report($report_id, $input['values']);

        if ($this->is_ajax()) {
            $this->json_response(array(
                'status'  => 'success',
                'message' => 'Report information updated.',
                'report'  => $this->rai->get_report($report_id)
            ));
            return;
        }

        $this->session->set_flashdata('success', 'Report information updated.');
        redirect(site_url('RaiReport/manage/' . $report_id));
    }

    /** Remove a report header and its report-only applicant rows. */
    public function delete($report_id = 0)
    {
        $this->require_post();

        $report_id = (int) $report_id;

        if (empty($this->rai->get_report($report_id))) {
            $this->session->set_flashdata('danger', 'That RAI report was not found.');
            redirect(site_url('RaiReport'));
            return;
        }

        $this->rai->delete_report($report_id);
        $this->session->set_flashdata('success', 'Report deleted.');
        redirect(site_url('RaiReport'));
    }

    /**
     * ------------------------------------------------------
     * MANAGE REPORT APPLICANTS
     * ------------------------------------------------------
     *
     * URL:
     * /RaiReport/manage/{id}
     *
     * Shows the saved report header and lets the user add applicants
     * from the system (appointed RQA records) or manually, then preview
     * the CSC report.
     */
    public function manage($report_id = 0)
    {
        $this->require_get();

        $report_id = (int) $report_id;
        $report = $this->rai->get_report($report_id);

        if (empty($report)) {
            $this->session->set_flashdata('danger', 'That RAI report was not found.');
            redirect(site_url('RaiReport'));
            return;
        }

        $data = array(
            'title'       => 'Manage RAI Report',
            'pageTitle'   => 'Report on Appointments Issued (RAI)',
            'formTitle'   => 'Enhanced CS Form No. 2',
            'formRevision'=> 'Revised 2025',
            'report'      => $report,
            'applicants'  => $this->rai->get_applicants($report_id),
            'natureOptions' => $this->rai->nature_options(),
            'raiToken'    => $this->form_token(),
            'candidateUrl' => site_url('RaiReport/candidates'),
            'detailUrl'    => site_url('RaiReport/candidate'),
            'previewUrl'   => site_url('RaiReport/preview'),
            'saveUrl'      => site_url('RaiReport/save_applicants/' . $report_id),
            'indexUrl'     => site_url('RaiReport'),
            'editUrl'      => site_url('RaiReport/edit/' . $report_id)
        );

        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/rai_report', $data);
        $this->load->view('templates/footer');
    }

    /**
     * AJAX endpoint that replaces the saved applicant list for a report.
     */
    public function save_applicants($report_id = 0)
    {
        $this->require_post();

        if (!$this->verify_form_token()) {
            $this->json_response(array(
                'status'  => 'error',
                'message' => 'Invalid or expired RAI form token.'
            ), 403);
            return;
        }

        $report_id = (int) $report_id;
        $report = $this->rai->get_report($report_id);

        if (empty($report)) {
            $this->json_response(array(
                'status'  => 'error',
                'message' => 'Report not found.'
            ), 404);
            return;
        }

        $raw = $this->input->post('payload', false);
        $payload = is_string($raw) ? json_decode($raw, true) : null;

        if (!is_array($payload) || !isset($payload['applicants']) || !is_array($payload['applicants'])) {
            $this->json_response(array(
                'status'  => 'error',
                'message' => 'Invalid applicant data.'
            ), 400);
            return;
        }

        $applicants = $payload['applicants'];

        if (count($applicants) > $this->max_applicants) {
            $this->json_response(array(
                'status'  => 'error',
                'message' => 'Maximum applicant count exceeded.'
            ), 400);
            return;
        }

        $ok = $this->rai->save_applicants($report_id, $applicants);

        $this->json_response(array(
            'status'  => $ok ? 'success' : 'error',
            'message' => $ok ? 'Applicants saved.' : 'Applicants could not be saved.'
        ), $ok ? 200 : 500);
    }

    /** Save editable Page 2 checklist statuses, notes, and CSC receiver. */
    public function save_checklist($report_id = 0)
    {
        $this->require_post();

        if (!$this->verify_form_token()) {
            $this->json_response(array('status' => 'error', 'message' => 'Invalid or expired RAI form token.'), 403);
            return;
        }

        $report_id = (int) $report_id;
        if (empty($this->rai->get_report($report_id))) {
            $this->json_response(array('status' => 'error', 'message' => 'Report not found.'), 404);
            return;
        }

        $raw = $this->input->post('payload', false);
        $payload = is_string($raw) ? json_decode($raw, true) : null;
        if (!is_array($payload)) {
            $this->json_response(array('status' => 'error', 'message' => 'Invalid checklist data.'), 400);
            return;
        }

        $checklist = $this->rai->normalize_checklist($payload);
        $ok = $this->rai->save_checklist($report_id, $checklist);
        $this->json_response(array(
            'status' => $ok ? 'success' : 'error',
            'message' => $ok ? 'Page 2 checklist saved.' : 'Checklist could not be saved.',
            'checklist' => $checklist
        ), $ok ? 200 : 500);
    }

    /** Save structured edits made directly in a saved RAI preview. */
    public function save_preview($report_id = 0)
    {
        $this->require_post();

        if (!$this->verify_form_token()) {
            $this->json_response(array('status' => 'error', 'message' => 'Invalid or expired RAI form token.'), 403);
            return;
        }

        $report_id = (int) $report_id;
        $report = $this->rai->get_report($report_id);
        if (empty($report)) {
            $this->json_response(array('status' => 'error', 'message' => 'Report not found.'), 404);
            return;
        }

        $raw = $this->input->post('payload', false);
        if (!is_string($raw) || strlen($raw) > 250000) {
            $this->json_response(array('status' => 'error', 'message' => 'Invalid preview data.'), 400);
            return;
        }
        $payload = json_decode($raw, true);
        if (!is_array($payload) || !isset($payload['applicants']) || !is_array($payload['applicants'])) {
            $this->json_response(array('status' => 'error', 'message' => 'Invalid preview data.'), 400);
            return;
        }
        if (count($payload['applicants']) > $this->max_applicants) {
            $this->json_response(array('status' => 'error', 'message' => 'Maximum applicant count exceeded.'), 400);
            return;
        }

        $items = array();
        foreach ($payload['applicants'] as $item) {
            if (!is_array($item)) {
                $this->json_response(array('status' => 'error', 'message' => 'Invalid applicant data.'), 400);
                return;
            }
            $clean_item = $this->clean_preview_item($item);
            // This is prescribed as a static value in the current RAI template.
            $clean_item['publication_mode'] = 'CSC WEBSITE';
            $items[] = $clean_item;
        }

        $header = isset($payload['header']) && is_array($payload['header']) ? $payload['header'] : array();
        $report_data = array(
            'report_month' => $report['report_month'] ?? '',
            'report_year' => $report['report_year'] ?? '',
            'nature_of_appointment' => $report['nature_of_appointment'] ?? '',
            'agency_name' => 'Department of Education',
            'division_name' => 'Davao De Oro',
            'csc_resolution_no' => '2100919',
            'csc_resolution_date' => '2021-11-19',
            'certifying_officer' => $report['certifying_officer'] ?? '',
            'certifying_position' => $report['certifying_position'] ?? '',
            'appointing_officer' => $report['appointing_officer'] ?? '',
            'appointing_position' => $report['appointing_position'] ?? '',
            'remarks' => $report['remarks'] ?? ''
        );
        foreach (array('certifying_officer', 'certifying_position', 'remarks') as $field) {
            if (isset($header[$field]) && is_scalar($header[$field])) {
                $report_data[$field] = mb_substr(trim((string) $header[$field]), 0, 500);
            }
        }

        $checklist = $this->rai->normalize_checklist($payload['checklist'] ?? array());
        $this->db->trans_begin();
        $applicants_saved = $this->rai->save_applicants($report_id, $items);
        $checklist_saved = $this->rai->save_checklist($report_id, $checklist);
        $header_saved = $this->rai->update_report($report_id, $report_data);
        $ok = $applicants_saved && $checklist_saved && $header_saved && $this->db->trans_status();
        if ($ok) {
            $this->db->trans_commit();
        } else {
            $this->db->trans_rollback();
        }

        $this->json_response(array(
            'status' => $ok ? 'success' : 'error',
            'message' => $ok ? 'RAI preview changes saved.' : 'Some preview changes could not be saved.',
            'savedAt' => date('M j, Y g:i A')
        ), $ok ? 200 : 500);
    }

    /** Read + validate the report information form fields. */
    private function collect_report_input()
    {
        $fields = array(
            'report_month',
            'report_year',
            'nature_of_appointment',
            'certifying_officer',
            'certifying_position',
            'remarks'
        );

        $values = array();
        foreach ($fields as $field) {
            $values[$field] = trim((string) $this->input->post($field));
        }

        // Prescribed Division template values are static and are no longer
        // exposed as editable report fields.
        $values['agency_name'] = 'Department of Education';
        $values['division_name'] = 'Davao De Oro';
        $values['csc_resolution_no'] = '2100919';
        $values['csc_resolution_date'] = '2021-11-19';
        $defaults = $this->rai->get_report_defaults();
        $values['appointing_officer'] = isset($defaults['appointing_officer'])
            ? (string) $defaults['appointing_officer']
            : '';
        $values['appointing_position'] = isset($defaults['appointing_position'])
            ? (string) $defaults['appointing_position']
            : '';

        $errors = array();
        $month = (int) $values['report_month'];
        $year = (int) $values['report_year'];

        if ($month < 1 || $month > 12) {
            $errors[] = 'Choose a valid reporting month.';
        }
        if ($year < 2000 || $year > 2100) {
            $errors[] = 'Enter a reporting year between 2000 and 2100.';
        }
        if (!isset($this->rai->nature_options()[$values['nature_of_appointment']])) {
            $errors[] = 'Choose a valid Nature of Appointment.';
        }
        if (!empty($errors)) {
            return array('errors' => $errors, 'values' => $values);
        }

        return array('values' => $values);
    }

    private function render_form($mode, $report)
    {
        $data = array(
            'title'     => $mode === 'edit' ? 'Edit RAI Report' : 'New RAI Report',
            'pageTitle' => 'Report on Appointments Issued (RAI)',
            'mode'      => $mode,
            'report'    => is_array($report) ? $report : array(),
            'defaults'  => $this->rai->get_report_defaults(),
            'natureOptions' => $this->rai->nature_options(),
            'old'       => (array) $this->session->flashdata('rai_report_old'),
            'formAction'=> $mode === 'edit'
                ? site_url('RaiReport/update/' . (int) $report['id'])
                : site_url('RaiReport/store'),
            'indexUrl'  => site_url('RaiReport')
        );

        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/rai_report_form', $data);
        $this->load->view('templates/footer');
    }

    private function fail($message, $redirect)
    {
        $this->session->set_flashdata('danger', $message);
        redirect($redirect);
    }

    /**
     * ------------------------------------------------------
     * SEARCH APPOINTED APPLICANTS
     * ------------------------------------------------------
     *
     * URL:
     * /RaiReport/candidates
     *
     * AJAX GET parameters:
     * search
     * month
     * year
     * page
     *
     * Only officially appointed records with valid
     * issuance timestamps may be returned by the model.
     */
    public function candidates()
    {
        $this->require_get();

        $search = trim((string) $this->input->get('search'));
        $search = mb_substr($search, 0, 100);

        $month = (int) $this->input->get('month');
        $year  = (int) $this->input->get('year');
        $page  = max(1, (int) $this->input->get('page'));

        $limit = 20;
        $offset = ($page - 1) * $limit;

        if ($month < 1 || $month > 12) {
            $month = 0;
        }

        if ($year < 2000 || $year > 2100) {
            $year = 0;
        }

        $filters = array(
            'search' => $search,
            'month'  => $month,
            'year'   => $year,
            'limit'  => $limit,
            'offset' => $offset
        );

        $result = $this->rai->search_appointed_applicants(
            $filters
        );

        $rows = isset($result['rows']) && is_array($result['rows'])
            ? $result['rows']
            : array();

        $total = isset($result['total'])
            ? (int) $result['total']
            : 0;

        $this->json_response(array(
            'status'  => 'success',
            'rows'    => $rows,
            'total'   => $total,
            'page'    => $page,
            'pages'   => (int) ceil($total / $limit),
            'perPage' => $limit
        ));
    }

    /**
     * ------------------------------------------------------
     * FETCH ONE APPOINTED APPLICANT
     * ------------------------------------------------------
     *
     * URL:
     * /RaiReport/candidate/{recommendation_id}
     *
     * Returns the available RAI field values for
     * the selected applicant.
     *
     * Missing values remain empty.
     */
    public function candidate($recommendation_id = 0)
    {
        $this->require_get();

        if (
            !ctype_digit((string) $recommendation_id)
            || (int) $recommendation_id < 1
        ) {
            $this->json_response(array(
                'status'  => 'error',
                'message' => 'Invalid applicant record.'
            ), 400);
            return;
        }

        $applicant = $this->rai->get_appointed_applicant(
            (int) $recommendation_id
        );

        if (empty($applicant)) {
            $this->json_response(array(
                'status'  => 'error',
                'message' => 'Appointed applicant not found.'
            ), 404);
            return;
        }

        $this->json_response(array(
            'status'    => 'success',
            'applicant' => $applicant
        ));
    }

    /**
     * ------------------------------------------------------
     * FETCH A SAVED REPORT + APPLICANTS (AJAX)
     * ------------------------------------------------------
     *
     * URL:
     * /RaiReport/data/{id}
     *
     * Returns the saved report header and applicant rows as JSON
     * so the index page can populate the Manage modal without a
     * full page navigation.
     */
    public function data($report_id = 0)
    {
        $this->require_get();

        $report_id = (int) $report_id;
        $report = $this->rai->get_report($report_id);

        if (empty($report)) {
            $this->json_response(array(
                'status'  => 'error',
                'message' => 'That RAI report was not found.'
            ), 404);
            return;
        }

        $this->json_response(array(
            'status'     => 'success',
            'report'     => $report,
            'applicants' => $this->rai->get_applicants($report_id),
            'raiToken'   => $this->form_token()
        ));
    }

    /**
     * ------------------------------------------------------
     * BULK FETCH APPOINTED APPLICANTS (AJAX)
     * ------------------------------------------------------
     *
     * URL:
     * /RaiReport/bulk_candidates
     *
     * POST:
     * ids[] = array of recommendation IDs
     *
     * Returns the full RAI field values for every requested
     * appointed applicant in one request, so the Manage modal
     * can add multiple system applicants at once.
     */
    public function bulk_candidates()
    {
        $this->require_post();

        $ids = $this->input->post('ids');
        if (!is_array($ids)) {
            $ids = $this->input->post('ids') === null
                ? array()
                : array_filter(array_map('trim', explode(',', (string) $this->input->post('ids'))));
        }

        $clean = array();
        foreach ($ids as $id) {
            $id = (int) $id;
            if ($id > 0) {
                $clean[$id] = $id;
            }
        }

        if (count($clean) > $this->max_applicants) {
            $this->json_response(array(
                'status'  => 'error',
                'message' => 'Too many applicants selected at once.'
            ), 400);
            return;
        }

        $applicants = array();
        foreach ($clean as $id) {
            $applicant = $this->rai->get_appointed_applicant($id);
            if (!empty($applicant)) {
                $applicants[] = $applicant;
            }
        }

        $this->json_response(array(
            'status'     => 'success',
            'applicants' => $applicants
        ));
    }

    /**
     * ------------------------------------------------------
     * REPORT PREVIEW
     * ------------------------------------------------------
     *
     * URL:
     * /RaiReport/preview          (POST: token + JSON payload, current screen state)
     * /RaiReport/preview/{id}     (GET: renders the saved report + applicants)
     *
     * POST:
     * rai_token = session-bound token
     * payload   = JSON report data
     */
    public function preview($report_id = 0)
    {
        if (strtoupper($this->input->method()) === 'GET') {
            $report_id = (int) $report_id;

            if ($report_id < 1) {
                show_error('RAI report not found.', 404);
                return;
            }

            $this->render_saved_preview($report_id);
            return;
        }

        $this->require_post();

        if (!$this->verify_form_token()) {
            show_error(
                'Invalid or expired RAI form token.',
                403
            );
            return;
        }

        $raw = $this->input->post('payload', false);

        if (!is_string($raw) || strlen($raw) > 250000) {
            show_error('Invalid RAI report payload.', 400);
            return;
        }

        $payload = json_decode($raw, true);

        if (
            json_last_error() !== JSON_ERROR_NONE
            || !is_array($payload)
        ) {
            show_error('Invalid RAI report data.', 400);
            return;
        }

        $header = isset($payload['header'])
            && is_array($payload['header'])
            ? $payload['header']
            : array();

        $applicants = isset($payload['applicants'])
            && is_array($payload['applicants'])
            ? $payload['applicants']
            : array();

        if (count($applicants) > $this->max_applicants) {
            show_error(
                'Maximum preview size exceeded.',
                400
            );
            return;
        }

        // Only accept recognized, bounded header fields.
        $clean_header = array();

        foreach ($this->preview_header_fields() as $field) {
            $value = isset($header[$field]) && is_scalar($header[$field])
                ? (string) $header[$field]
                : '';

            $clean_header[$field] = mb_substr(
                trim($value),
                0,
                $field === 'checklist_json' ? 20000 : 500
            );
        }

        // These are editable report snapshot fields,
        // never the original database records.
        $clean_items = array();

        foreach ($applicants as $item) {
            if (!is_array($item)) {
                show_error('Invalid applicant data.', 400);
                return;
            }

            $clean_items[] = $this->clean_preview_item($item);
        }

        $this->render_pages($clean_header, $clean_items, true, 0);
    }

    private function preview_header_fields()
    {
        return array(
            'report_month',
            'report_year',
            'nature_of_appointment',
            'agency_name',
            'division_name',
            'csc_resolution_no',
            'csc_resolution_date',
            'certifying_officer',
            'certifying_position',
            'appointing_officer',
            'appointing_position',
            'checklist_json',
            'remarks'
        );
    }

    private function preview_item_fields()
    {
        return array(
            'source_type',
            'source_id',
            'classification_key',
            'classification_label',
            'date_issued',
            'last_name',
            'first_name',
            'name_extension',
            'middle_name',
            'sex',
            'pwd',
            'position_level',
            'position_title',
            'item_number',
            'salary_grade',
            'salary_step',
            'monthly_salary',
            'employment_status',
            'employment_period_from',
            'employment_period_to',
            'nature_of_appointment',
            'publication_from',
            'publication_to',
            'publication_mode',
            'appointment_identification_no',
            'csc_action',
            'csc_action_date',
            'csc_release_date'
        );
    }

    private function clean_preview_item($item)
    {
        $clean = array();

        foreach ($this->preview_item_fields() as $field) {
            $value = isset($item[$field]) && is_scalar($item[$field])
                ? (string) $item[$field]
                : '';

            $clean[$field] = mb_substr(trim($value), 0, 255);
        }

        return $clean;
    }

    /**
     * GET /RaiReport/preview/{id}
     * Renders the saved report header + saved applicants through the same
     * printable layout used by the manage screen's live preview.
     */
    private function render_saved_preview($report_id)
    {
        $report = $this->rai->get_report($report_id);

        if (empty($report)) {
            show_error('RAI report not found.', 404);
            return;
        }

        $clean_header = array();
        foreach ($this->preview_header_fields() as $field) {
            $clean_header[$field] = mb_substr(
                trim((string) (isset($report[$field]) ? $report[$field] : '')),
                0,
                $field === 'checklist_json' ? 20000 : 500
            );
        }

        $clean_items = array();
        foreach ($this->rai->get_applicants($report_id) as $item) {
            $clean_items[] = $this->clean_preview_item($item);
        }

        $this->render_pages($clean_header, $clean_items, false, $report_id);
    }

    /** Preserve submitted order and paginate every 10 applicants. */
    private function render_pages($clean_header, $clean_items, $isDraft = true, $report_id = 0)
    {
        $pages = array();
        $sequence = 1;
        $prepared_items = array();

        foreach ($clean_items as $item) {
            if ($item['nature_of_appointment'] === '' && !empty($clean_header['nature_of_appointment'])) {
                $item['nature_of_appointment'] = $clean_header['nature_of_appointment'];
            }
            if ($item['classification_key'] === '') {
                $parts = array_filter(array(
                    trim((string) $item['employment_status']),
                    trim((string) $item['nature_of_appointment'])
                ), function ($value) {
                    return $value !== '';
                });
                $label = !empty($parts) ? implode(' — ', $parts) : 'Unclassified';
                $item['classification_label'] = $label;
                $item['classification_key'] = trim(strtolower((string) preg_replace('/[^a-z0-9]+/i', '_', $label)), '_');
            }
            $item['sequence_number'] = $sequence++;
            $prepared_items[] = $item;
        }

        foreach (array_chunk($prepared_items, 10) as $chunk) {
            $pages[] = array(
                'classification'  => '',
                'applicants'      => $chunk,
                'nothing_follows' => count($chunk) < 10
            );
        }

        $data = array(
            'title'          => 'RAI Report Preview',
            'formTitle'      => 'Enhanced CS Form No. 2',
            'formRevision'   => 'Revised 2025',
            'header'         => $clean_header,
            'pages'          => $pages,
            'applicantCount' => count($clean_items),
            'paperSize'      => 'Legal',
            'orientation'    => 'landscape',
            'isDraft'        => $isDraft,
            'reportId'       => (int) $report_id,
            'raiToken'       => $this->form_token(),
            'savePreviewUrl' => site_url('RaiReport/save_preview')
        );

        $this->output->set_header(
            'Cache-Control: no-store, no-cache, must-revalidate'
        );

        $this->load->view(
            'pages/rai_report_print',
            $data
        );
    }
}
