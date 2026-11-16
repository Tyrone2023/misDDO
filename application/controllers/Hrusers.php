<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * HR "Manage Users": personnel accounts (asds, Secretariat, HR, evaluators...).
 *
 * Edit changes the position, name, username and password of an account. A
 * username change is carried into every table that recorded the old username
 * (see User_account_model::USERNAME_COLUMNS) so the person's history follows
 * the account.
 */
class Hrusers extends CI_Controller
{
    const MANAGERS = array('Human Resource Admin', 'HR Staff', 'Admin', 'Super Admin', 'asds');

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_account_model', 'accounts');

        if (!$this->session->logged_in) {
            redirect(base_url() . 'log_in');
        }
        if (!in_array((string) $this->session->position, self::MANAGERS, true)) {
            show_error('You are not allowed to manage user accounts.', 403);
        }
    }

    private function is_admin()
    {
        return in_array((string) $this->session->position, User_account_model::PRIVILEGED, true);
    }

    private function token()
    {
        $t = $this->session->userdata('hrusers_token');
        if (!$t) {
            $t = bin2hex(random_bytes(16));
            $this->session->set_userdata('hrusers_token', $t);
        }
        return $t;
    }

    private function render($page, array $data)
    {
        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function index()
    {
        $tab = $this->input->get('tab') === 'applicants' ? 'applicants' : 'personnel';

        $this->render('hrusers_list', array(
            'title' => 'Manage Users',
            'tab'   => $tab,
            'users' => $tab === 'applicants'
                ? $this->accounts->applicants()
                : $this->accounts->personnel($this->is_admin()),
        ));
    }

    /** Loads the account and refuses ones this user may not touch. */
    private function editable($id)
    {
        $user = $this->accounts->get($id);

        if (!$user) {
            $this->session->set_flashdata('danger', 'Account not found.');
            redirect(base_url() . 'hrusers');
        }
        if (in_array($user->position, array('School', 'School Account'), true)) {
            // School accounts are keyed by school ID across the SGOD tables.
            $this->session->set_flashdata('danger', 'School accounts are managed from the school list, not here.');
            redirect(base_url() . 'hrusers');
        }
        if (in_array($user->position, User_account_model::PRIVILEGED, true) && !$this->is_admin()) {
            $this->session->set_flashdata('danger', 'Only an Admin can edit ' . $user->position . ' accounts.');
            redirect(base_url() . 'hrusers');
        }

        return $user;
    }

    public function edit($id = 0)
    {
        $user = $this->editable($id);

        if ($this->input->method() === 'post') {
            $this->update($user);
            return;
        }

        $applicant = in_array($user->position, array('reg', 'user'), true);

        $this->render('hrusers_edit', array(
            'title'      => 'Edit Account',
            'acct'       => $user,
            'is_self'    => (int) $user->id === (int) $this->session->userdata('id'),
            'applicant'  => $applicant,
            'positions'  => $applicant ? array() : $this->accounts->assignable_positions($this->is_admin(), $user->position),
            'linked'     => $this->accounts->linked_counts($user->username, $user->id),
            'history'    => $this->accounts->username_history($user->id),
            'code_ref'   => in_array(strtolower($user->username), array_map('strtolower', User_account_model::CODE_REFERENCED), true),
            'token'      => $this->token(),
        ));
    }

    private function update($user)
    {
        $back = base_url() . 'hrusers/edit/' . $user->id;

        if (!hash_equals($this->token(), (string) $this->input->post('token'))) {
            $this->session->set_flashdata('danger', 'The form expired. Please try again.');
            redirect($back);
        }

        $is_self   = (int) $user->id === (int) $this->session->userdata('id');
        $applicant = in_array($user->position, array('reg', 'user'), true);
        $errors    = array();

        $username = trim((string) $this->input->post('username'));
        $password = (string) $this->input->post('password');
        $confirm  = (string) $this->input->post('password_confirm');

        $changes = array(
            'fname' => trim((string) $this->input->post('fname')),
            'mname' => trim((string) $this->input->post('mname')),
            'lname' => trim((string) $this->input->post('lname')),
        );

        // Username — the format rules apply only to a new name, so an old
        // account with an unusual username can still get a password reset.
        if ($username === (string) $user->username) {
            $changes['username'] = $username;
        } elseif ($username === '') {
            $errors[] = 'Username is required.';
        } elseif ($user->position === 'user') {
            // Employee logins are their ID number, which keys payroll, leave,
            // PDS and other records — those are not renamed from here.
            $errors[] = 'Employee usernames are their ID numbers and cannot be changed here.';
        } elseif (mb_strlen($username) > 45) {
            $errors[] = 'Username must be 45 characters or fewer.';
        } elseif (!preg_match('/^[A-Za-z0-9._@-]+$/', $username)) {
            $errors[] = 'Username may only contain letters, numbers, and . _ @ -';
        } elseif ($user->position === 'reg' && !filter_var($username, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'An applicant\'s username is their e-mail address; enter a valid e-mail.';
        } elseif (in_array(strtolower($username), array_map('strtolower', User_account_model::CODE_REFERENCED), true)
            && strtolower($username) !== strtolower((string) $user->username)) {
            $errors[] = 'The username "' . $username . '" is reserved by the system; choose another.';
        } elseif ($this->accounts->skip_mixed($username)) {
            // Tracking columns also store numeric record IDs; a username equal
            // to one would make this person's activity indistinguishable from it.
            $errors[] = 'The username "' . $username . '" is the same as an existing record ID number; choose another.';
        } elseif ($this->accounts->username_taken($username, $user->id)) {
            $errors[] = 'The username "' . $username . '" is already used by another account.';
        } else {
            $changes['username'] = $username;
        }

        // Position
        if (!$applicant) {
            $position = (string) $this->input->post('acct_position');
            $allowed  = $this->accounts->assignable_positions($this->is_admin(), $user->position);

            if ($position === '' || !in_array($position, $allowed, true)) {
                $errors[] = 'Choose a valid position.';
            } elseif ($is_self && $position !== $user->position) {
                $errors[] = 'You cannot change your own position. Ask another administrator.';
            } else {
                $changes['position'] = $position;
            }
        }

        // Password (optional)
        if ($password !== '' || $confirm !== '') {
            if (strlen($password) < 6) {
                $errors[] = 'The new password must be at least 6 characters.';
            } elseif ($password !== $confirm) {
                $errors[] = 'The new password and its confirmation do not match.';
            } else {
                $changes['password'] = $password;
            }
        }

        if ($changes['fname'] === '' && $changes['lname'] === '') {
            $errors[] = 'Enter at least a first or last name.';
        }

        if (!empty($errors)) {
            $this->session->set_flashdata('danger', implode('<br>', array_map('html_escape', $errors)));
            redirect($back);
        }

        $result = $this->accounts->save($user->id, $changes);

        if (!$result['ok']) {
            $this->session->set_flashdata('danger', html_escape($result['error']));
            redirect($back);
        }

        if (empty($result['changed'])) {
            $this->session->set_flashdata('success', 'No changes were made.');
            redirect($back);
        }

        $msg = 'Account "' . html_escape($result['new']) . '" was updated.';

        if ($result['renamed']) {
            $total = array_sum($result['moved']);
            $msg  .= ' Username changed from <b>' . html_escape($result['old']) . '</b> to <b>' . html_escape($result['new'])
                   . '</b>; ' . $total . ' linked record(s) now point to the new username.';

            if (!empty($result['moved'])) {
                $parts = array();
                foreach ($result['moved'] as $label => $n) {
                    $parts[] = html_escape($label) . ': ' . $n;
                }
                $msg .= '<br><small>' . implode(' &middot; ', $parts) . '</small>';
            }
            if (!empty($result['skipped'])) {
                $msg .= '<br><small>Not changed (numeric username is ambiguous there): '
                      . html_escape(implode(', ', $result['skipped'])) . '</small>';
            }
            if (!$is_self) {
                $msg .= '<br><small>If this person is signed in now, they must sign out and sign in again with the new username.</small>';
            }
        }
        if (in_array('password', $result['changed'], true)) {
            $msg .= ' The password was reset.';
        }
        if (in_array('position', $result['changed'], true) && !$is_self) {
            $msg .= ' The new position takes effect on their next sign-in.';
        }

        $this->session->set_flashdata('success', $msg);
        redirect($back);
    }
}
