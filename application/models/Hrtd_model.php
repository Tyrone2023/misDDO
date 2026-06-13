<?php

class Hrtd_model extends CI_Model
{

    public function __construct()
    {
        $this->load->database();
    }

    public function training_insert()
    {
        $randomNumber = rand(10000, 99999);

        $data = array(
            'target_date' => $this->input->post('target_date'), 
            'actual_date' => $this->input->post('target_date'),
            'modality' => $this->input->post('modality'), 
            'venue' => $this->input->post('venue'), 
            'funds' => $this->input->post('funds'), 
            'participant' => $this->input->post('participant'), 
            'quarter' => $this->input->post('quarter'), 
            'description' => $this->input->post('description'),
            'stat' => 0,
            'passkey' => $randomNumber,
            'no_participant' => $this->input->post('no_participant'),
            'po' => $this->input->post('po'),
            'prc' => $this->input->post('prc'),
            'cpd' => $this->input->post('cpd'),
        );
        return $this->db->insert('sgod_hrtd', $data);
    }

    public function training_update()
    {
        $data = array(
            'target_date' => $this->input->post('target_date'), 
            'actual_date' => $this->input->post('actual_date'), 
            'modality' => $this->input->post('modality'), 
            'venue' => $this->input->post('venue'), 
            'funds' => $this->input->post('funds'), 
            'participant' => $this->input->post('participant'), 
            'quarter' => $this->input->post('quarter'), 
            'description' => $this->input->post('description'),
            'stat' => 0,
            'no_participant' => $this->input->post('no_participant'),
            'po' => $this->input->post('po'),
            'prc' => $this->input->post('prc'),
            'cpd' => $this->input->post('cpd'),
        );

        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('sgod_hrtd', $data);
    }

    public function register_insert()
    {
        date_default_timezone_set('Asia/Manila');
        $time = date('H:i:s A');
        $date = date("Y-m-d");
        $fy = date("Y");

        $data = array(
            'date_registered' => $date, 
            'time_registered' => $time, 
            'fy' => $fy, 
            'training_id' => $this->input->post('id'), 
            'IDNumber' => $this->input->post('IDNumber'), 
            'position' => $this->input->post('position')
        );
        return $this->db->insert('sgod_hrtd_training_registered', $data);
    }

    function autoBrEvery20Words($text, $limit = 10) {
        $words = explode(' ', trim($text));
        $out = [];

        foreach ($words as $i => $word) {
            $out[] = $word;
            if (($i + 1) % $limit === 0) {
                $out[] = '<br>';
            }
        }
        return implode(' ', $out);
    }

    public function training_stat_update()
    {
        $data = array(
            'stat' => $this->uri->segment(3)
        );

        $this->db->where('id', $this->uri->segment(4));
        return $this->db->update('sgod_hrtd', $data);
    }

    public function update_wap(){

            $file = $this->upload->data();
            $filename = $file['file_name']; 

            $data = array(
                'wap' => $filename,
                );

            $this->db->where('id', $this->input->post('id'));
            return $this->db->update('sgod_hrtd_training_registered', $data);
    }

    public function update_mov(){

            $file = $this->upload->data();
            $filename = $file['file_name']; 

            $data = array(
                'mov' => $filename,
                );

            $this->db->where('id', $this->input->post('id'));
            return $this->db->update('sgod_hrtd_training_registered', $data);
    }


}