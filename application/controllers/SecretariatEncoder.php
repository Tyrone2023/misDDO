<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Secretariat-managed "Field Encoder" logins.
 *
 * A Field Encoder can do exactly one thing: encode Interview / Written
 * Examination scores for the vacancies already assigned to the Secretariat
 * that created the account (secretariat/scores).
 */
class SecretariatEncoder extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper(['url', 'form']);
        $this->load->model('Secretariat_model', 'secretariat');
        $this->load->model('Audit_model', 'Audit');
    }

    private function guard(): void
    {
        if ($this->session->userdata('position') !== 'Secretariat') {
            show_error('Only Secretariat users can manage Field Encoder accounts.', 403, 'Forbidden');
            exit;
        }
    }

    private function user_id(): int
    {
        return (int) ($this->session->id ?? $this->session->userdata('id'));
    }

    private function list_url(): string
    {
        return base_url('secretariat/encoders');
    }

    private function post_only(): void
    {
        if (strtoupper((string) $this->input->method()) !== 'POST') {
            show_error('Method Not Allowed', 405);
            exit;
        }
    }

    /** Shared name/username validation for create and update. */
    private function collect(int $exceptId = 0): array
    {
        $fields = [
            'username' => trim((string) $this->input->post('username', true)),
            'fname' => trim((string) $this->input->post('fname', true)),
            'mname' => trim((string) $this->input->post('mname', true)),
            'lname' => trim((string) $this->input->post('lname', true)),
        ];

        $errors = [];
        if ($fields['username'] === '') {
            $errors[] = 'Username is required.';
        } elseif (strlen($fields['username']) > 45) {
            $errors[] = 'Username must be 45 characters or less.';
        } elseif ($this->secretariat->username_taken($fields['username'], $exceptId)) {
            $errors[] = 'That username is already taken.';
        }
        if ($fields['fname'] === '' || $fields['lname'] === '') {
            $errors[] = 'First name and last name are required.';
        }

        return [$fields, $errors];
    }

    private function collect_password(string $field = 'password'): array
    {
        $password = (string) $this->input->post($field);
        $confirm = (string) $this->input->post($field . '_confirm');

        $errors = [];
        if (strlen($password) < 6) {
            $errors[] = 'Password must be at least 6 characters.';
        } elseif ($password !== $confirm) {
            $errors[] = 'The password and its confirmation do not match.';
        }

        return [$password, $errors];
    }

    private function fail(array $errors, string $reopen = ''): void
    {
        $this->session->set_flashdata('danger', implode(' ', $errors));
        if ($reopen !== '') {
            $this->session->set_flashdata('fe_reopen', $reopen);
        }
        redirect($this->list_url());
    }

    /**
     * The vacancy scope posted by the form: access[] carries the checked job
     * ids and mode[<jobID>] the score(s) allowed there. The model drops
     * anything outside the Secretariat's own vacancies, so no filtering here.
     */
    private function collect_access(): array
    {
        $checked = $this->input->post('access');
        $modes = $this->input->post('mode');
        $access = [];

        if (!is_array($checked)) {
            return $access;
        }

        foreach ($checked as $jobId) {
            $jobId = (int) $jobId;
            if ($jobId <= 0) {
                continue;
            }
            $access[$jobId] = $this->secretariat->normalize_encode_mode(
                is_array($modes) && isset($modes[$jobId]) ? $modes[$jobId] : 'both'
            );
        }

        return $access;
    }

    /** One-line summary of a scope, for the audit trail. */
    private function describe_access(array $access): string
    {
        if (empty($access)) {
            return 'no vacancy';
        }

        $counts = ['written' => 0, 'interview' => 0, 'both' => 0];
        foreach ($access as $mode) {
            $counts[$mode] = ($counts[$mode] ?? 0) + 1;
        }

        $parts = [];
        foreach ($counts as $mode => $count) {
            if ($count > 0) {
                $parts[] = $count . ' ' . $mode;
            }
        }

        return count($access) . ' vacancy(ies) [' . implode(', ', $parts) . ']';
    }

    public function index(): void
    {
        $this->guard();

        $userId = $this->user_id();
        $encoders = $this->secretariat->field_encoders($userId);

        $data = [
            'title' => 'Manage Users - Field Encoder',
            'encoders' => $encoders,
            'vacancies' => $this->secretariat->assignable_vacancies($userId),
            'accessMap' => $this->secretariat->field_encoder_access_map(
                array_map(static fn($encoder) => (int) $encoder->id, $encoders)
            ),
            'reopen' => (string) $this->session->flashdata('fe_reopen'),
        ];

        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/secretariat_encoders', $data);
        $this->load->view('templates/footer');
    }

    public function store(): void
    {
        $this->guard();
        $this->post_only();

        [$fields, $errors] = $this->collect();
        [$password, $passwordErrors] = $this->collect_password();
        $errors = array_merge($errors, $passwordErrors);
        $access = $this->collect_access();

        if (empty($access)) {
            $errors[] = 'Tag at least one vacancy the account may encode.';
        }

        if (!empty($errors)) {
            $this->fail($errors, 'add');
            return;
        }

        $fields['password'] = $password;
        $userId = $this->user_id();
        $encoderId = $this->secretariat->create_field_encoder($userId, $fields);

        if ($encoderId <= 0) {
            $this->fail(['The Field Encoder account could not be created.'], 'add');
            return;
        }

        $this->secretariat->save_field_encoder_access($userId, $encoderId, $access);

        $this->Audit->log('create', [
            'entity_type' => 'users',
            'entity_id' => $encoderId,
            'description' => 'Created Field Encoder account: ' . $fields['username']
                . ' - access: ' . $this->describe_access($access),
        ]);

        $this->session->set_flashdata('success', 'Field Encoder "' . $fields['username'] . '" was created.');
        redirect($this->list_url());
    }

    public function update(): void
    {
        $this->guard();
        $this->post_only();

        $encoderId = (int) $this->input->post('id');
        [$fields, $errors] = $this->collect($encoderId);
        $access = $this->collect_access();

        if ($encoderId <= 0) {
            $errors[] = 'The Field Encoder account is missing.';
        }
        if (empty($access)) {
            $errors[] = 'Tag at least one vacancy the account may encode.';
        }

        if (!empty($errors)) {
            $this->fail($errors, 'edit-' . $encoderId);
            return;
        }

        $userId = $this->user_id();
        if (!$this->secretariat->update_field_encoder($userId, $encoderId, $fields)) {
            $this->fail(['That Field Encoder account is not yours to edit.']);
            return;
        }

        $this->secretariat->save_field_encoder_access($userId, $encoderId, $access);

        $this->Audit->log('update', [
            'entity_type' => 'users',
            'entity_id' => $encoderId,
            'description' => 'Updated Field Encoder account: ' . $fields['username']
                . ' - access: ' . $this->describe_access($access),
        ]);

        $this->session->set_flashdata('success', 'Field Encoder "' . $fields['username'] . '" was updated.');
        redirect($this->list_url());
    }

    public function reset_password(): void
    {
        $this->guard();
        $this->post_only();

        $encoderId = (int) $this->input->post('id');
        [$password, $errors] = $this->collect_password();

        if ($encoderId <= 0) {
            $errors[] = 'The Field Encoder account is missing.';
        }

        if (!empty($errors)) {
            $this->fail($errors, 'pass-' . $encoderId);
            return;
        }

        if (!$this->secretariat->set_field_encoder_password($this->user_id(), $encoderId, $password)) {
            $this->fail(['That Field Encoder account is not yours to edit.']);
            return;
        }

        $this->Audit->log('update', [
            'entity_type' => 'users',
            'entity_id' => $encoderId,
            'description' => 'Reset the password of Field Encoder account #' . $encoderId,
        ]);

        $this->session->set_flashdata('success', 'The password was reset.');
        redirect($this->list_url());
    }

    public function delete($encoderId = 0): void
    {
        $this->guard();

        $encoderId = (int) $encoderId;
        $encoder = $this->secretariat->field_encoder($this->user_id(), $encoderId);

        if (empty($encoder)) {
            $this->fail(['That Field Encoder account is not yours to delete.']);
            return;
        }

        $this->secretariat->delete_field_encoder($this->user_id(), $encoderId);
        $this->secretariat->remove_field_encoder_access($encoderId);

        $this->Audit->log('delete', [
            'entity_type' => 'users',
            'entity_id' => $encoderId,
            'description' => 'Deleted Field Encoder account: ' . $encoder->username,
        ]);

        $this->session->set_flashdata('success', 'Field Encoder "' . $encoder->username . '" was removed.');
        redirect($this->list_url());
    }
}
