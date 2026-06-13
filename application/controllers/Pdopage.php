<?php

class Pdopage extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();

    }

    public function bkdp()
    {
        $page = "bkdp";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $data['title'] = "BARKADA KONTRA DROGA PLUS";
        $data['sub'] = "MONITORING AND TECHNICAL ASSISTANCE TOOL";
        $data['data'] = $this->Common->no_cond('bkdp_indicator');

        $data['exist'] = $this->Common->two_cond_row('bkdp','school_id',$this->session->c_id,'fy',date('Y'));

        if ($this->input->post('submit')) {
            $this->PdoModel->bkdp_insert();
            $this->session->set_flashdata('success', 'Saved successfully.');
          
            redirect(base_url() . 'Pdopage/bkdp');
         }


        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/modal_com');
        $this->load->view('templates/footer');
    }

    public function fdsafds()
    {
        $result['title'] = 'BRIGADA ESKWELA '.date('Y').' MONITORING TOOL ';
        $result['indi_type'] = $this->Common->no_cond_group('brigada_imp_indicators','type');
        $result['en'] = $this->Common->no_cond('brigada_engagement');

        $school_id = $this->session->username;
        $district = $this->input->post('district');
        $fy = $this->input->post('fy');

        $result['exist'] = $this->Common->two_cond_row('brigada_monitored','school_id',$this->session->username,'fy',date('Y'));

        if ($this->input->post('submit')) {
            $this->BrigadaModel->monitor_insert();
            $this->session->set_flashdata('success', 'Saved successfully.');
          
            redirect(base_url() . 'Brigada/brigada_mon_tools_school_view');
         }

         if ($this->input->post('update')) {
            $this->BrigadaModel->monitor_update();
            $this->session->set_flashdata('success', 'Saved successfully.');
          
            redirect(base_url() . 'Brigada/brigada_mon_tools_school_view');
         }

        
        $this->load->view('brigada_mon_tools_school_view', $result);
    }

}


?>