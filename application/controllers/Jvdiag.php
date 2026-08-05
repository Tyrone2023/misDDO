<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// TEMPORARY diagnostic controller — delete after verifying the announcement modal.
class Jvdiag extends CI_Controller
{
    public function page($role = 'Human Resource Admin')
    {
        if (!is_cli()) { show_404(); }

        $_SERVER['REQUEST_URI'] = '/misDDO/Page/jobVacancy';
        $this->uri->segments = [1 => 'Page', 2 => 'jobVacancy'];

        $this->session->set_userdata([
            'position' => $role, 'username' => 'HR', 'id' => 44234,
            'c_id' => 1, 'eg' => 1, 'logged_in' => true,
        ]);

        $this->load->model('PersonnelModel');
        $this->Reg->ensure_announcement_columns();

        $result['data']      = $this->PersonnelModel->JobVancancies();
        $result['teaching']  = $this->Hiring_model->JobVancancies();
        $result['ren']       = $this->Common->two_cond('hris_jobvacancy', 'promotion', 1, 'jvStatus', 'Open');
        $result['lock']      = $this->Common->one_cond_count_row('hris_applicant', 'stat', 0);
        $result['pos_title'] = $this->Common->no_cond_order_by('hris_positions', 'title', 'ASC');
        $result['groups']    = [1 => 'Teaching', 2 => 'School Administration', 3 => 'Related Teaching', 4 => 'Non-Teaching'];

        $out = $this->load->view('list_jobvacancy', $result, true);

        file_put_contents(FCPATH . 'jv_page.html', $out);

        echo "role={$role} bytes=" . strlen($out) . PHP_EOL;
        echo "ann buttons : " . substr_count($out, 'hrp-ann-btn"') . ' + posted ' . substr_count($out, 'hrp-ann-btn hrp-ann-btn-posted') . PHP_EOL;
        echo "modal       : " . substr_count($out, 'id="announcementModal"') . PHP_EOL;
        echo "textarea    : " . substr_count($out, 'id="ann-text"') . PHP_EOL;
        echo "read panel  : " . substr_count($out, 'id="ann-read"') . PHP_EOL;
        echo "old inline  : " . (strpos($out, 'hrp-ann-text') !== false ? 'STILL PRESENT' : 'removed') . PHP_EOL;
    }
}
