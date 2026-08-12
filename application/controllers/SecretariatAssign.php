<?php
defined('BASEPATH') or exit('No direct script access allowed');

class SecretariatAssign extends CI_Controller
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
        if ($this->session->userdata('position') !== 'Super Admin') {
            show_error('Only Super Admin users can access this page.', 403, 'Forbidden');
            exit;
        }
    }

    public function index(): void
    {
        $this->guard();

        $selectedId   = (int) $this->input->get('id');
        $secretariats = $this->secretariat->list_secretariats();
        if ($selectedId === 0 && !empty($secretariats)) {
            $selectedId = (int) $secretariats[0]->id;
        }

        $data = [
            'title'           => 'Assign Secretariat Coverage',
            'secretariats'    => $secretariats,
            'assignments'     => $this->secretariat->assignments_indexed(),
            'job_types'       => $this->secretariat->job_types_map(),
            'scope_catalog'   => $this->secretariat->scope_catalog(),
            'vacancy_counts'  => $this->secretariat->open_vacancy_counts(),
            'selected_id'     => $selectedId,
        ];

        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/secretariat_assign', $data);
        $this->load->view('templates/footer');
    }

    public function save(): void
    {
        $this->guard();

        $userId = (int) $this->input->post('secretariat_id');
        $scopes = $this->input->post('scopes');

        // pre-position-group forms posted job_types[]; keep accepting them
        if (!is_array($scopes)) {
            $scopes = $this->input->post('job_types');
        }
        if (!is_array($scopes)) {
            $scopes = [];
        }

        $creatorId = $this->session->id ?? $this->session->userdata('id');
        $this->secretariat->save_assignments($userId, $scopes, $creatorId);

        $this->session->set_flashdata('success', 'Secretariat coverage updated.');
        redirect(base_url('SecretariatAssign?id=' . $userId));
    }
}
