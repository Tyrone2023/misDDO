<?php

class Users extends CI_Controller{

    function __construct(){
        parent::__construct();
        $this->load->database();
        $this->load->helper('url');
    }


    public function users_sub(){
        $page = "user_list_sub";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }


        $data['title'] = "User's List";
        $data['pn'] = "Add New User";
        $data['link'] = "users_sub_create";
        
        
        $data['users'] = $this->Common->one_cond('users', 'user_id', $this->session->c_id);  
        
       
        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/modal_com');
        $this->load->view('templates/footer');
    }

    public function users_sub_create(){

        $this->form_validation->set_error_delimiters('<div class="alert alert-danger alert-dismissible fade show" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        ', '</div>');
        $this->form_validation->set_rules('position', 'Position', 'required');
        //$this->form_validation->set_rules('termPeriodto', 'Term Period To', 'required');

        if ($this->form_validation->run() == FALSE) {

            $page = "user_sub_new";

            if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
                show_404();
            }

            $data['title'] = "Create New User";
            
            
            $data['sp'] = $this->Common->one_cond('users_sp', 'main_position', $this->session->position);  
            $data['district'] = $this->Common->no_cond('district');  
            
        
            $this->load->view('templates/head');
            $this->load->view('templates/header');
            $this->load->view('pages/' . $page, $data);
            $this->load->view('templates/modal_com');
            $this->load->view('templates/footer');
        } else {
            $check = $this->Common->one_cond_count_row('users','username',$this->input->post('username'));

            if($check->num_rows() >= 1){
                $this->session->set_flashdata('danger', 'Duplicate username');
                redirect(base_url() . 'Users/users_sub');

            }else{
                $this->Reg->insert_sub_user();
                $this->Page_model->insert_at('New position added', $this->db->insert_id());
                $this->session->set_flashdata('success', 'New user successfully added');
                redirect(base_url() . 'Users/users_sub');
            }
        }
    }


    public function users_sub_update(){

        $this->form_validation->set_error_delimiters('<div class="alert alert-danger alert-dismissible fade show" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        ', '</div>');
        $this->form_validation->set_rules('username', 'Username', 'required');
        //$this->form_validation->set_rules('termPeriodto', 'Term Period To', 'required');

        if ($this->form_validation->run() == FALSE) {

            $page = "user_sub_edit";

            if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
                show_404();
            }

            $data['title'] = "Update User";
            
            
            $data['sp'] = $this->Common->one_cond('users_sp', 'main_position', $this->session->position);  
            $data['user'] = $this->Common->one_cond_row('users', 'id', $this->uri->segment(3));  
            
        

            $this->load->view('templates/head');
            $this->load->view('templates/header');
            $this->load->view('pages/' . $page, $data);
            $this->load->view('templates/modal_com');
            $this->load->view('templates/footer');
        } else {
            $check = $this->Common->one_cond_count_row('users','username',$this->input->post('username'));

            if($check->num_rows() >= 1){
                $this->session->set_flashdata('danger', 'Duplicate username');
                redirect(base_url() . 'Users/users_sub');

            }else{
                $this->Reg->update_sub_user();
                $this->Page_model->insert_at('Updated', $this->db->insert_id());
                $this->session->set_flashdata('success', 'Successfully Updated');
                redirect(base_url() . 'Users/users_sub');
            }
        }
    }
    

    public function users_sp(){

        $this->form_validation->set_error_delimiters('<div class="alert alert-danger alert-dismissible fade show" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        ', '</div>');
        $this->form_validation->set_rules('position', 'Position', 'required');
        //$this->form_validation->set_rules('termPeriodto', 'Term Period To', 'required');

        if ($this->form_validation->run() == FALSE) {

            $page = "user_sp";

            if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
                show_404();
            }

            $data['title'] = "Sub-user's Position";
            
            
            $data['users'] = $this->Common->no_cond('users_sp');  
            $data['pos'] = $this->Common->no_cond_group('users','position');  
            
        

            $this->load->view('templates/head');
            $this->load->view('templates/header');
            $this->load->view('pages/' . $page, $data);
            $this->load->view('templates/modal_com');
            $this->load->view('templates/footer');
        } else {
            $this->Reg->sp_position_insert();
            $this->Page_model->insert_at('New position added', $this->db->insert_id());
            $this->session->set_flashdata('success', 'New position successfully added');
            redirect(base_url() . 'Users/users_sp');
        }
    }

    public function user_sp_delete($param){
        $this->Common->delete('users_sp','id',3);
        $this->Page_model->insert_at('Sub Position Deleted', $param);
        $this->session->set_flashdata('danger', ' Sub Position deleted.');
        redirect(base_url() . 'Users/users_sub');
    }

    public function sp_user_delete($param){
        $this->Common->delete('users','id',3);
        $this->Page_model->insert_at('Sub Position Deleted', $param);
        $this->session->set_flashdata('danger', ' Sub Position deleted.');
        redirect(base_url() . 'Users/users_sub');
    }

    

    public function user_sp_edit(){
        $this->Reg->sp_position_update();
        $this->session->set_flashdata('success', ' Sub Position edited.');
        redirect(base_url() . 'Users/users_sub');
    }



}