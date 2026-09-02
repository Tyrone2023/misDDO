<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Signatories maintained per job vacancy.
 *
 * Works for every vacancy regardless of position group - the module is keyed on
 * hris_jobvacancy.jobID only. What is saved here is printed at the foot of the
 * RQA reports (Pages/car_rqa_administrative and .../_posting) in the layout set
 * on this page. Signature images are stored in uploads/esig, alongside the
 * per-user signatures kept by Pages/esignature.
 */
class VacancySignatories extends CI_Controller
{
    /** Relative to FCPATH - same folder the per-user e-signatures use. */
    private $uploadDir = 'uploads/esig/';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Vacancy_signatory_model', 'vsign');

        if ($this->session->logged_in == false) {
            redirect(base_url() . 'log_in');
        }
    }

    /**
     * Same set of roles that already maintains job vacancies on Page/jobVacancy.
     */
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
            $this->session->set_flashdata('danger', 'You are not allowed to maintain vacancy signatories.');
            redirect(base_url() . 'Page/jobVacancy');
        }
    }

    /**
     * Group types as posted on Page/jobVacancy (hris_jobvacancy.job_type).
     * Same labels the vacancy list shows, so a vacancy reads the same here.
     */
    private function job_types()
    {
        return array(
            1  => 'Elementary',
            2  => 'Secondary',
            3  => 'Junior High School',
            4  => 'Senior High School',
            5  => 'Kindergarten',
            6  => 'IPED Elementary',
            7  => 'IPED Secondary',
            8  => 'IPED Junior High School',
            9  => 'IPED Senior High School',
            10 => 'SNED',
            11 => 'SHS Academic and Core Subjects',
            12 => 'SHS Arts and Design Track',
            13 => 'SHS Sports Track',
            14 => 'SHS Technical-Vocational(TVL) Track',
            15 => 'Elementary - SPIMS',
            16 => 'Junior High School - SPIMS',
            17 => 'DOST - (RA 7687)',
            18 => 'DOST - (RA 10612)',
            19 => '(SST I)',
            20 => 'FOR TESTING PURPOSES (DO NOT APPLY)'
        );
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

    public function index($jobID = null)
    {
        $this->guard();

        $jobID = (int) $jobID;
        $job   = $this->job_or_redirect($jobID);

        $data['job']    = $job;
        $data['rows']   = $this->vsign->get_by_job($jobID);
        $data['next']   = $this->vsign->max_order($jobID) + 1;
        $data['next_slot'] = $this->vsign->next_available_slot($jobID, $this->vsign->max_slot($jobID) + 1);
        $data['groups'] = array(
            1 => 'Teaching',
            2 => 'School Administration',
            3 => 'Related Teaching',
            4 => 'Non-Teaching'
        );
        $data['job_types'] = $this->job_types();

        // vacancies that already carry a panel, for the copy-from picker
        $data['sources'] = $this->db->query(
            "select v.jobID, v.jobTitle, v.sy, v.job_type, v.position, v.empType, count(s.id) as total
               from hris_vacancy_signatories s
               join hris_jobvacancy v on v.jobID = s.job_id
              where s.job_id != ?
              group by v.jobID, v.jobTitle, v.sy, v.job_type, v.position, v.empType
              order by v.jobID desc",
            array($jobID)
        )->result();

        $this->load->view('vacancy_signatories', $data);
    }

    /**
     * Insert when id is empty, update otherwise. The signature image is only
     * replaced when a new file is actually uploaded, so editing a name never
     * loses the signature already on file.
     */
    public function save()
    {
        $this->guard();

        $id    = (int) $this->input->post('id');
        $jobID = (int) $this->input->post('job_id');
        $job   = $this->job_or_redirect($jobID);

        $back = base_url() . 'VacancySignatories/index/' . $jobID;

        $name = trim((string) $this->input->post('name'));
        if ($name === '') {
            $this->session->set_flashdata('danger', 'Signatory name is required.');
            redirect($back);
        }

        $order = $this->vsign->max_order($jobID) + 1;
        $slot = (int) $this->input->post('print_slot');
        if ($slot < 1 || $slot > 50) {
            $slot = $this->vsign->next_available_slot($jobID, $this->vsign->max_slot($jobID) + 1);
        }

        $fields = array(
            'name'            => $name,
            'designation'     => trim((string) $this->input->post('designation')),
            'sign_role'       => trim((string) $this->input->post('sign_role')),
            'print_label'     => mb_substr(trim((string) $this->input->post('print_label')), 0, 200),
            'signatory_order' => $order
        );

        $existing = $id ? $this->vsign->get_by_id($id) : null;

        if ($id && (empty($existing) || (int) $existing->job_id !== $jobID)) {
            $this->session->set_flashdata('danger', 'Signatory record not found for this vacancy.');
            redirect($back);
        }

        // upload first - a failed upload must not half-save the row
        if (!empty($_FILES['esig']['name'])) {
            $upload = $this->upload_esig($jobID, $name);

            if ($upload['status'] === 'error') {
                $this->session->set_flashdata('danger', 'Signature upload failed: ' . $upload['error']);
                redirect($back);
            }

            $fields['esig'] = $upload['file'];
        }

        if ($id) {
            $old = !empty($existing->esig) ? $existing->esig : '';
            // Keep its legacy sequence number until the layout move has
            // completed; normalize_orders() then makes it match the grid.
            $fields['signatory_order'] = (int) $existing->signatory_order;
            $this->vsign->update($id, $fields);
            $this->vsign->move_to_slot($id, $slot);

            if (!empty($fields['esig']) && $old !== '' && $old !== $fields['esig']) {
                $this->delete_esig_file($old);
            }

            $this->Page_model->insert_at('Updated vacancy signatory: ' . $name, $jobID);
            $this->session->set_flashdata('success', 'Signatory updated.');
        } else {
            $fields['job_id']     = $jobID;
            $fields['created_by'] = (int) $this->session->id;
            // Insert temporarily after the occupied layout, then swap into the
            // requested position. This avoids duplicate cells at every point.
            $fields['print_slot'] = $this->vsign->next_available_slot($jobID, $this->vsign->max_slot($jobID) + 1);
            $newID = $this->vsign->insert($fields);
            $this->vsign->move_to_slot($newID, $slot);

            $this->Page_model->insert_at('Added vacancy signatory: ' . $name, $jobID);
            $this->session->set_flashdata('success', 'Signatory added.');
        }

        $this->vsign->normalize_orders($jobID);
        redirect($back);
    }

    public function delete($id = null)
    {
        $this->guard();

        $row = $this->vsign->get_by_id((int) $id);

        if (empty($row)) {
            $this->session->set_flashdata('danger', 'Signatory record not found.');
            redirect(base_url() . 'Page/jobVacancy');
        }

        $jobID = (int) $row->job_id;

        $this->vsign->delete($row->id);
        $this->delete_esig_file($row->esig);
        $this->vsign->normalize_orders($jobID);

        $this->Page_model->insert_at('Deleted vacancy signatory: ' . $row->name, $jobID);
        $this->session->set_flashdata('success', 'Signatory deleted.');
        redirect(base_url() . 'VacancySignatories/index/' . $jobID);
    }

    /**
     * Nudge a signatory one slot up or down the printing order.
     */
    public function move($id = null, $direction = 'up')
    {
        $this->guard();

        $row = $this->vsign->get_by_id((int) $id);

        if (empty($row)) {
            $this->session->set_flashdata('danger', 'Signatory record not found.');
            redirect(base_url() . 'Page/jobVacancy');
        }

        $direction = ($direction === 'down') ? 'down' : 'up';
        $this->vsign->move($row->id, $direction);

        redirect(base_url() . 'VacancySignatories/index/' . (int) $row->job_id);
    }

    /**
     * AJAX endpoint used by the arrows directly on the printable CAR/RQA.
     * Directions move through a five-column layout and swap occupied cells.
     */
    public function move_layout()
    {
        if (!$this->can_manage()) {
            return $this->json_result(false, 'You are not allowed to arrange vacancy signatories.');
        }

        $id = (int) $this->input->post('id');
        $direction = trim((string) $this->input->post('direction'));
        $row = $this->vsign->get_by_id($id);

        if (empty($row) || !in_array($direction, array('left', 'right', 'up', 'down'), true)) {
            return $this->json_result(false, 'Invalid signatory or direction.');
        }

        if (!$this->vsign->move_in_layout($id, $direction)) {
            return $this->json_result(false, 'That signatory cannot move farther in this direction.');
        }

        return $this->json_result(true, 'Layout saved.');
    }

    /** Save the optional text printed immediately above one signatory. */
    public function save_label()
    {
        if (!$this->can_manage()) {
            return $this->json_result(false, 'You are not allowed to edit vacancy signatories.');
        }

        $id = (int) $this->input->post('id');
        $row = $this->vsign->get_by_id($id);
        if (empty($row)) {
            return $this->json_result(false, 'Signatory not found.');
        }

        $label = mb_substr(trim((string) $this->input->post('print_label')), 0, 200);
        if (!$this->vsign->update($id, array('print_label' => $label))) {
            return $this->json_result(false, 'The label could not be saved.');
        }

        return $this->json_result(true, 'Label saved.');
    }

    private function json_result($success, $message)
    {
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(array(
                'status'  => $success ? 'success' : 'error',
                'message' => $message
            )));

        return null;
    }

    /**
     * Copy an existing signatory list into this vacancy so a panel that signs
     * several postings does not have to be re-encoded each time. Existing rows
     * are kept; the copies are appended after them.
     */
    public function copy_from()
    {
        $this->guard();

        $jobID   = (int) $this->input->post('job_id');
        $sourceID = (int) $this->input->post('source_job_id');
        $this->job_or_redirect($jobID);

        $back = base_url() . 'VacancySignatories/index/' . $jobID;

        if ($sourceID <= 0 || $sourceID === $jobID) {
            $this->session->set_flashdata('danger', 'Choose a different vacancy to copy from.');
            redirect($back);
        }

        $source = $this->vsign->get_by_job($sourceID);

        if (empty($source)) {
            $this->session->set_flashdata('danger', 'That vacancy has no signatories to copy.');
            redirect($back);
        }

        $order = $this->vsign->max_order($jobID);
        // Keep the copied panel's blank cells and relative placement. When the
        // destination already has entries, begin at the next full print row.
        $slotOffset = $this->vsign->max_slot($jobID) > 0
            ? (int) ceil($this->vsign->max_slot($jobID) / 5) * 5
            : 0;

        foreach ($source as $row) {
            $order++;
            $sourceSlot = max(1, (int) $row->print_slot);
            $copySlot = $slotOffset + $sourceSlot;
            if ($copySlot > 50) {
                $copySlot = $this->vsign->next_available_slot($jobID, 1);
            }
            $this->vsign->insert(array(
                'job_id'          => $jobID,
                'name'            => $row->name,
                'designation'     => $row->designation,
                'sign_role'       => $row->sign_role,
                'print_label'     => $row->print_label,
                // the image file is shared, not duplicated on disk
                'esig'            => $row->esig,
                'signatory_order' => $order,
                'print_slot'      => $copySlot,
                'created_by'      => (int) $this->session->id
            ));
        }

        $this->vsign->normalize_orders($jobID);

        $this->Page_model->insert_at('Copied ' . count($source) . ' vacancy signatories from jobID ' . $sourceID, $jobID);
        $this->session->set_flashdata('success', count($source) . ' signatory record(s) copied.');
        redirect($back);
    }

    /**
     * Stores one signature under uploads/esig with a readable, vacancy-scoped
     * name (vsig_76_dela_cruz_juan.png). CI appends a counter when the name is
     * already taken, so an existing file is never overwritten.
     *
     * @return array{status:string,file:?string,error?:string}
     */
    private function upload_esig($jobID, $name)
    {
        $path = FCPATH . $this->uploadDir;

        if (!is_dir($path)) {
            @mkdir($path, 0777, true);
        }

        $extension = 'png';
        $ext = strtolower(pathinfo($_FILES['esig']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, array('jpg', 'jpeg'), true)) {
            $extension = $ext;
        }

        $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '_', $name));
        $slug = trim((string) $slug, '_');
        if ($slug === '') {
            $slug = 'signatory';
        }

        $config = array(
            'upload_path'   => $path,
            'allowed_types' => 'png|jpg|jpeg',
            'max_size'      => 2048,
            'file_name'     => 'vsig_' . (int) $jobID . '_' . $slug . '.' . $extension,
            'overwrite'     => false
        );

        $this->load->library('upload', $config);
        $this->upload->initialize($config);

        if (!$this->upload->do_upload('esig')) {
            return array(
                'status' => 'error',
                'file'   => null,
                'error'  => strip_tags($this->upload->display_errors('', ''))
            );
        }

        $data = $this->upload->data();

        return array('status' => 'ok', 'file' => $data['file_name']);
    }

    /**
     * Removes a signature file, but only when no other signatory row still
     * points at it (copy_from shares the file across vacancies).
     */
    private function delete_esig_file($filename)
    {
        $filename = trim((string) $filename);
        if ($filename === '') {
            return;
        }

        $still_used = (int) $this->db
            ->where('esig', $filename)
            ->count_all_results('hris_vacancy_signatories');

        if ($still_used > 0) {
            return;
        }

        $file = FCPATH . $this->uploadDir . basename($filename);
        if (is_file($file)) {
            @unlink($file);
        }
    }
}
