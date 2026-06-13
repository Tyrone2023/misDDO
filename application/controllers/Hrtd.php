<?php

class Hrtd extends CI_Controller
{

	function __construct()
	{
		parent::__construct();
	}

    public function hrtd_trainings()
    {
        $page = "sgod_hrtd_training";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $data['title'] = "Training List";
        $data['data'] = $this->Common->no_cond('sgod_hrtd');


        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function hrtd_trainings_insert()
    {
        $this->Hrtd_model->training_insert();
		$this->session->set_flashdata('success', 'Successfully Saved');
		redirect($_SERVER['HTTP_REFERER']);
    }

    public function training_delete()
    {
        $this->Common->delete('sgod_hrtd', 'id','3');
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function training_update()
    {

        $this->form_validation->set_error_delimiters('<div class="alert alert-danger alert-dismissible fade show" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        ', '</div>');
        $this->form_validation->set_rules('description', 'Description', 'required');
        //$this->form_validation->set_rules('termPeriodto', 'Term Period To', 'required');

        if ($this->form_validation->run() == FALSE) {

            $page = "sgod_hrtd_training_update";

            if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
                show_404();
            }

            $data['title'] = 'Update Training info';

            $data['data'] = $this->Common->one_cond_row('sgod_hrtd','id',$this->uri->segment(3));

            $this->load->view('templates/head');
            $this->load->view('templates/header');
            $this->load->view('pages/' . $page, $data);
            $this->load->view('templates/footer');
        } else {
                $this->Hrtd_model->training_update();
                $this->session->set_flashdata('success', 'Successfully Updated.');
                redirect(base_url() . 'trainings');

        }

    }

    public function hrtd_trainings_school()
    {

        $page = "sgod_hrtd_trainings_school";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $data['title'] = "Training List";
        $data['data'] = $this->Common->one_cond('sgod_hrtd','stat',1);


        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function hrtd_trainings_program_owned()
    {

        $page = "sgod_hrtd_training";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $data['title'] = "Training List";
        $data['data'] = $this->Common->one_cond('sgod_hrtd','po',$this->session->username);


        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function register_training()
    {

        $this->form_validation->set_error_delimiters('<div class="alert alert-danger alert-dismissible fade show" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        ', '</div>');
        $this->form_validation->set_rules('passkey', 'passkey', 'required');
        //$this->form_validation->set_rules('termPeriodto', 'Term Period To', 'required');

        if ($this->form_validation->run() == FALSE) {

            $page = "sgod_hrtd_register";

            if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
                show_404();
            }

            $data['title'] = 'Register';

            $data['staff'] = $this->Common->one_cond_row('hris_staff','IDNumber',$this->session->username);
            $data['data'] = $this->Common->one_cond_row('sgod_hrtd','id',$this->uri->segment(3));
            $data['position'] = $this->Common->one_cond('sgod_hrtd_position','group',$data['staff']->payCat);

            $this->load->view('templates/head');
            $this->load->view('templates/header');
            $this->load->view('pages/' . $page, $data);
            $this->load->view('templates/footer');
        } else {
                $check = $this->Common->two_cond_row('sgod_hrtd','passkey',$this->input->post('passkey'),'id',$this->input->post('id'));
                $duplicate = $this->Common->two_cond_row('sgod_hrtd_training_registered','IDNumber',$this->input->post('IDNumber'),'training_id',$this->input->post('id'));
                if($duplicate){
                    $this->session->set_flashdata('danger', "You have already registered with this passkey. Please check your details.");
                    redirect($_SERVER['HTTP_REFERER']);
                }else{
                if ($check) {
                    $this->Hrtd_model->register_insert();
                    $this->session->set_flashdata('success', "Success! Your registration was completed successfully.");
                    redirect(base_url() . 'available_trainings');
                }else{
                    $this->session->set_flashdata('danger', "Oops! We couldn't find a match for the passkey or the training. Please double-check your details and try again.");
                    redirect($_SERVER['HTTP_REFERER']);

                }}
                

        }

    }

    public function registered_in_training()
        {

            $page = "sgod_hrtd_registered_list";

            if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
                show_404();
            }

            $data['title'] = "Training List";
            $data['data'] = $this->Common->one_cond('sgod_hrtd_training_registered','training_id',$this->uri->segment(3));
            $data['training'] = $this->Common->one_cond_row('sgod_hrtd','id',$this->uri->segment(3));


            $this->load->view('templates/head');
            $this->load->view('templates/header');
            $this->load->view('pages/' . $page, $data);
            $this->load->view('templates/footer');
        }

        public function participant_delete()
        {
            $this->Common->delete('sgod_hrtd_training_registered', 'id','3');
            redirect($_SERVER['HTTP_REFERER']);
        }


        public function update_wap_file() {
            $config['allowed_types'] = 'pdf';
            $config['upload_path'] = './uploads/hrdfile';
            $new_name = $this->input->post('IDNumber').'-wap-'.time().$_FILES["file"]['name'];
            $config['file_name'] = $new_name;
            $this->load->library('upload', $config);
        
            if ($this->upload->do_upload('file')) {
                $id = $this->input->post('id');
                $hrtfile = $this->Common->one_cond_row('sgod_hrtd_training_registered', 'id', $id);
                
                if (!empty($hrtfile->wap)) {
                    unlink("uploads/hrdfile/" . $hrtfile->wap);
                }
                
                $this->Hrtd_model->update_wap();
                $this->session->set_flashdata('success', 'Successfully updated.');
            } else {
                $this->session->set_flashdata('danger', $this->upload->display_errors());
            }
            
            redirect($_SERVER['HTTP_REFERER']);
        }

        public function update_mov_file() {
            $config['allowed_types'] = 'pdf';
            $config['upload_path'] = './uploads/hrdfile';
            $new_name = $this->input->post('IDNumber').'-mov-'.time().$_FILES["file"]['name'];
            $config['file_name'] = $new_name;
            $this->load->library('upload', $config);
        
            if ($this->upload->do_upload('file')) {
                $id = $this->input->post('id');
                $hrtfile = $this->Common->one_cond_row('sgod_hrtd_training_registered', 'id', $id);
                
                if (!empty($hrtfile->mov)) {
                    unlink("uploads/hrdfile/" . $hrtfile->mov);
                }
                
                $this->Hrtd_model->update_mov();
                $this->session->set_flashdata('success', 'Successfully updated.');
            } else {
                $this->session->set_flashdata('danger', $this->upload->display_errors());
            }
            
            redirect($_SERVER['HTTP_REFERER']);
        }

        public function training_stat_update()
        {
            $this->Hrtd_model->training_stat_update();
            redirect($_SERVER['HTTP_REFERER']);
        }














}