<?php

class Legal_model extends CI_Model
{

    public function __construct()
    {
        $this->load->database();
    }

    public function complaint_insert()
    {
        date_default_timezone_set('Asia/Manila');
        //$time = date('H:i:s A');
        $date = date("Y-m-d");

        $data = array(
            'nature_of_offense' => $this->input->post('nature_of_offense'), 
            'case_no' => $this->input->post('case_no'), 
            'complainant' => $this->input->post('complainant'), 
            'respondent' => $this->input->post('respondent'), 
            'stat' => $this->input->post('stat'), 
            'remarks' => $this->input->post('remarks'),
            'encode_date' => $date,
            'fy' => $this->input->post('fy'),
        );
        return $this->db->insert('legal', $data);
    }

    public function complaint_update()
    {
        date_default_timezone_set('Asia/Manila');
        //$time = date('H:i:s A');
        $date = date("Y-m-d");

        $data = array(
            'nature_of_offense' => $this->input->post('nature_of_offense'), 
            'case_no' => $this->input->post('case_no'), 
            'complainant' => $this->input->post('complainant'), 
            'respondent' => $this->input->post('respondent'), 
            'stat' => $this->input->post('stat'), 
            'remarks' => $this->input->post('remarks'),
            'fy' => $this->input->post('fy'),
        );
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('legal', $data);
    }
    

    public function complaint()
    {
        $this->db->select('a.*, a.id as ic, b.id,b.description,b.id,b.description as ivy,c.id,c.description as ivan');
        $this->db->from('legal' . ' as a');
        $this->db->join('legal_nature_of_offense' . ' as b', 'a.nature_of_offense = b.id', 'left');
        $this->db->join('legal_status' . ' as c', 'a.stat = c.id', 'left');
        //$this->db->where($col, $val);
        $query = $this->db->get();
        return $query->result();
    }

   
}