<?php

class Legal extends CI_Controller
{

	function __construct()
	{
		parent::__construct();
	}

    public function complaints()
    {

        $page = "legal_complaints";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

		$data['title'] = "Legal";
        //$data['data'] = $this->Common->no_cond('legal');
        $data['offinse'] = $this->Common->no_cond('legal_nature_of_offense');
        $data['stat'] = $this->Common->no_cond('legal_status');
        $data['data'] = $this->Legal_model->complaint();


        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function complaint_update()
    {

        $this->form_validation->set_error_delimiters('<div class="alert alert-danger alert-dismissible fade show" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        ', '</div>');
        $this->form_validation->set_rules('case_no', 'Case No', 'required');
        //$this->form_validation->set_rules('termPeriodto', 'Term Period To', 'required');

        if ($this->form_validation->run() == FALSE) {

            $page = "legal_complaint_update";

            if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
                show_404();
            }

            $data['title'] = 'Update Complaints';

            $data['offinse'] = $this->Common->no_cond('legal_nature_of_offense');
            $data['stat'] = $this->Common->no_cond('legal_status');

            $data['data'] = $this->Common->one_cond_row('legal','id',$this->uri->segment(3));

            $this->load->view('templates/head');
            $this->load->view('templates/header');
            $this->load->view('pages/' . $page, $data);
            $this->load->view('templates/footer');
        } else {
                $this->Legal_model->complaint_update();
                $this->session->set_flashdata('success', 'Successfully Updated.');
                redirect(base_url() . 'complaints');

        }

    }

    public function legal_complaint_insert()
    {
        $this->Legal_model->complaint_insert();
		$this->session->set_flashdata('success', 'Successfully Saved');
		redirect($_SERVER['HTTP_REFERER']);
    }

    public function complaint_delete()
    {
        $this->Common->delete('legal', 'id','3');
        $this->session->set_flashdata('danger', 'Successfully Deleted');
        redirect($_SERVER['HTTP_REFERER']);
    }

    



}
