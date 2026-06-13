<?php

class Ps extends CI_Controller
{

	function __construct()
	{
		parent::__construct();
	}

    public function private_list()
    {

        $page = "private_report_list";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }
            
		$data['data'] = $this->Common->one_cond('private_report','school_id',$this->session->username);
		$data['title'] = "List of required data";


        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function report_delete($param)
    {
        $report = $this->Common->one_cond_row('private_report', 'id', $this->uri->segment(3));

        if ($report && !empty($report->file)) {
            $file_path = FCPATH . 'uploads/private/' . $report->file;

            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
        
        $this->Page_model->delete('3', 'id', 'private_report');
        $this->session->set_flashdata('danger', 'Successfully deleted.');
        redirect(base_url() . 'Ps/private_list');
    }

    public function private_update_file()
    {
            $report = $this->Common->one_cond_row('private_report', 'id', $this->input->post('ren'));
            if ($report && !empty($report->file)) {
                $file_path = FCPATH . 'uploads/private/' . $report->file;

                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }
        
            $config['allowed_types'] = 'pdf';
            $config['upload_path'] = './uploads/private/';
            $this->load->library('upload', $config);

            $this->upload->do_upload('file');

            $this->Ps_model->report_file_update();
            $this->session->set_flashdata('success', 'Successfully Saved.');
            redirect(base_url() . 'Ps/private_list');
    }

    

	

    public function private_report_add()
    {

        $this->form_validation->set_error_delimiters('<div class="alert alert-danger alert-dismissible fade show" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        ', '</div>');
        $this->form_validation->set_rules('year', 'Year', 'required');
        $this->form_validation->set_rules('quarter', 'Quarter', 'required');

        if ($this->form_validation->run() == FALSE) {

            $page = "private_report_new";

            if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
                show_404();
            }

            $data['title'] = "Add New";

            $this->load->view('templates/head');
            $this->load->view('templates/header');
            $this->load->view('pages/' . $page, $data);
            $this->load->view('templates/footer');
        } else {

            $config['allowed_types'] = 'pdf';
            $config['upload_path'] = './uploads/private/';
            $this->load->library('upload', $config);

            $this->upload->do_upload('file');

            $this->Ps_model->report_insert();
            $this->session->set_flashdata('success', 'Successfully Saved.');
            redirect(base_url() . 'Ps/private_list');
        }
    }

    public function private_report_update()
    {

        $this->form_validation->set_error_delimiters('<div class="alert alert-danger alert-dismissible fade show" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        ', '</div>');
        $this->form_validation->set_rules('year', 'Year', 'required');
        $this->form_validation->set_rules('quarter', 'Quarter', 'required');

        if ($this->form_validation->run() == FALSE) {

            $page = "private_report_update";

            if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
                show_404();
            }

            $data['data'] = $this->Common->one_cond_row('private_report','id',$this->uri->segment(3));
            $data['title'] = "Update";

            $this->load->view('templates/head');
            $this->load->view('templates/header');
            $this->load->view('pages/' . $page, $data);
            $this->load->view('templates/footer');
        } else {

            $this->Ps_model->report_update();
            $this->session->set_flashdata('success', 'Successfully Saved.');
            redirect(base_url() . 'Ps/private_list');
        }
    }

    public function private_list_other()
    {

        $page = "private_report_other_list";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }
            
		$data['data'] = $this->Common->no_cond('private_report_question');
		$data['title'] = "List of required data";


        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }
    

    public function private_other_add()
    {
        $this->Ps_model->private_other_new('3', 'id', 'private_report');
        $this->session->set_flashdata('success', 'Successfully Saved.');
        redirect(base_url() . 'Ps/private_list_other');
    }

    public function private_indicator_add()
    {
        $this->Ps_model->private_indicator_new();
        $this->session->set_flashdata('success', 'Successfully Saved.');
        redirect(base_url() . 'Ps/private_list_other');
    }

    public function private_indicator_delete()
    {
        $this->Page_model->delete('3', 'id', 'private_report_question');
        $this->session->set_flashdata('danger', 'Successfully deleted.');
        redirect(base_url() . 'Ps/private_list_other');
    }

    public function private_entry_list_other()
    {

        $page = "private_report_entry_list_other";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }
            
		$data['data'] = $this->Common->two_cond('private_other_data','pq_id',$this->uri->segment(3),'school_id',$this->session->username);
        $data['question'] = $this->Common->one_cond_row('private_report_question','id',$this->uri->segment(3));
		$data['title'] = "List of required data";


        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function report_other_delete()
    {
        $this->Page_model->delete('3', 'id', 'private_other_data');
        $this->session->set_flashdata('danger', 'Successfully deleted.');
        redirect(base_url() . 'Ps/private_entry_list_other/'.$this->uri->segment(3));
    }

    public function private()
    {


        $this->form_validation->set_error_delimiters('<div class="alert alert-danger alert-dismissible fade show" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        ', '</div>');
        $this->form_validation->set_rules('schoolName', 'school Name', 'required');
       
        if ($this->form_validation->run() == FALSE) {

            $page = "private_signup";

            if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
                show_404();
            }
            $data['district'] = $this->Common->no_cond_select('district', 'id,discription');

            $data['mis_settings'] = $this->Common->one_cond_row('mis_settings','settingsID',1);
            $data['title'] = "Register";

            $this->load->view('templates/header_public');
            $this->load->view('pages/' . $page, $data);
            $this->load->view('templates/footer_public');
        } else {

            $mis_settings = $this->Common->one_cond_row('mis_settings','settingsID',1);

            $recaptcha = $this->input->post('g-recaptcha-response');
            $secret = trim($mis_settings->secret_key);

            $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secret}&response={$recaptcha}");
            $responseKeys = json_decode($response, true);

            if (!$responseKeys["success"]) {
                $this->session->set_flashdata('danger', 'reCAPTCHA verification failed. Please try again.');
                redirect(base_url().'log_in'); 
            }


            $renren = $this->input->post('renren');
            $ivykate = $this->input->post('ivykate');
            $ivankyle = $this->input->post('ivankyle');
            $ic = $this->input->post('ic');

            if (!empty($renren) || !empty($ivykate) || !empty($ivankyle) || !empty($ic)) {
                $this->session->set_flashdata('danger', 'I Got you');
                redirect(base_url() . 'private');
            }

            $this->SGODModel->insert_school();
            $this->Ps_model->insert_user();

            $email = $this->input->post('schoolEmail');
            $name = $this->input->post('schoolName');
            $username = $this->input->post('schoolID');
            $pass = 'private112';

            //Email Notification
				$this->load->config('email');
				$this->load->library('email');
				$mail_message = '
                    <html>
                    <head>
                    <style>
                        body {
                        font-family: "Segoe UI", Roboto, Arial, sans-serif;
                        background-color: #f0f4f8;
                        margin: 0;
                        padding: 20px;
                        }
                        .email-wrapper {
                        max-width: 600px;
                        margin: auto;
                        background-color: #ffffff;
                        border-radius: 10px;
                        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
                        overflow: hidden;
                        }
                        .email-header {
                        background-color: #0d6efd;
                        color: white;
                        padding: 20px;
                        text-align: center;
                        }
                        .email-header h2 {
                        margin: 0;
                        font-size: 24px;
                        }
                        .email-body {
                        padding: 30px 25px;
                        color: #333333;
                        }
                        .email-body p {
                        font-size: 16px;
                        line-height: 1.6;
                        }
                        .credentials-box {
                        background-color: #e9f2ff;
                        padding: 15px;
                        border-left: 4px solid #0d6efd;
                        margin: 20px 0;
                        border-radius: 6px;
                        }
                        .credentials-box p {
                        margin: 0;
                        font-weight: bold;
                        color: #0d3f8f;
                        }
                        .email-footer {
                        background-color: #f7f7f7;
                        padding: 15px;
                        text-align: center;
                        font-size: 14px;
                        color: #666666;
                        }
                    </style>
                    </head>
                    <body>
                    <div class="email-wrapper">
                        <div class="email-header">
                        <h2>Welcome to DepEd MIS</h2>
                        </div>
                        <div class="email-body">
                        <p>Dear ' . htmlspecialchars($name) . ',</p>
                        <p>Your profile has been successfully encoded into the <strong>DepEd MIS</strong> system. Please find your login credentials below:</p>

                        <div class="credentials-box">
                            <p>Username: ' . htmlspecialchars($username) . '</p>
                            <p>Password: ' . htmlspecialchars($pass) . '</p>
                        </div>

                        <p>Kindly keep this information secure and do not share it with anyone.</p>
                        <p>Should you have any issues accessing your account, please contact your system administrator.</p>

                        <p style="margin-top: 30px;">Thanks & Regards,<br><strong>DepEd MIS Team</strong></p>
                        </div>
                        <div class="email-footer">
                        © ' . date('Y') . ' Department of Education | Management Information System
                        </div>
                    </div>
                    </body>
                    </html>';

				$this->email->from('no-reply@lxeinfotechsolutions.com', 'DepEd MIS Team')
					->to($email)
					->subject('Account Created')
					->message($mail_message);
				$this->email->send();

            $this->session->set_flashdata('success', 'School account has been registered successfully. Your username and password have been sent to your email.');
            redirect(base_url() . 'log_in');
                
            
        }
    }


    public function report_list_admin()
    {

        $page = "private_report_list_admin";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }
            
		$data['data'] = $this->Common->one_cond('private_report','school_id',$this->session->username);
		$data['title'] = "List of required data";


        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function list_of_schools()
    {

        $page = "private_school_list";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }
            
		$data['data'] = $this->Common->one_cond_select('schools','schoolType,schoolID,schoolName','schoolType','Private');
		$data['title'] = "List of required data";


        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function private_list_admin()
    {

        $page = "private_report_list";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }
            
		$data['data'] = $this->Common->one_cond('private_report','school_id',$this->uri->segment(3));
		$data['title'] = "List of required data";


        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function private_list_other_admin()
    {

        $page = "private_report_other_list_admin";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }
            
		$data['data'] = $this->Common->no_cond('private_report_question');
		$data['title'] = "List of required data";


        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function private_entry_list_other_admin()
    {

        $page = "private_report_entry_list_other";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }
            
		$data['data'] = $this->Common->two_cond('private_other_data','pq_id',$this->uri->segment(3),'school_id',$this->uri->segment(4));
        $data['question'] = $this->Common->one_cond_row('private_report_question','id',$this->uri->segment(3));
		$data['title'] = "List of required data";


        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function general_consolidated_report()
    {

        $page = "private_general_cons_report";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $quarter = $this->input->post('quarter');
        $year = $this->input->post('year');
            
		$data['total'] = $this->Ps_model->get_column_totals($quarter, $year);
		$data['title'] = "List of required data";


        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function other_consolidated_report()
    {

        $page = "private_other_cons_report";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }
            
		$data['data'] = $this->Common->no_cond('private_report_question');
		$data['title'] = "List of required data";


        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }





}
