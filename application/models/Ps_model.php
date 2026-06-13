<?php

class Ps_model extends CI_Model{

    public function __construct(){
          $this->load->database();

    }

    public function report_insert()
    {

        $file = $this->upload->data();
        $filename = $file['file_name']; 

        $data = array(
            'mpregnancy' => $this->input->post('mpregnancy'), 
            'fpregnancy' => $this->input->post('fpregnancy'), 
            'msubsidy' => $this->input->post('msubsidy'), 
            'fsubsidy' => $this->input->post('fsubsidy'), 
            'mletpass' => $this->input->post('mletpass'), 
            'fletpass' => $this->input->post('fletpass'), 
            'mnotletpass' => $this->input->post('mnotletpass'), 
            'fnotletpass' => $this->input->post('fnotletpass'), 
            'delivery' => $this->input->post('delivery'), 
            'full_persons_classes' => $this->input->post('full_persons_classes'), 
            'peac' => $this->input->post('peac'), 
            'esc' => $this->input->post('esc'), 
            'gastpe' => $this->input->post('gastpe'), 
            'blended_learning' => $this->input->post('blended_learning'), 
            'distrance_learning' => $this->input->post('distrance_learning'), 
            'quarter' => $this->input->post('quarter'), 
            'year' => $this->input->post('year'),
            'school_id' => $this->session->username,
            'file' => $filename
        );
        return $this->db->insert('private_report', $data);
    }


    public function report_file_update()
    {

        $file = $this->upload->data();
        $filename = $file['file_name']; 

        $data = array(
            'file' => $filename
        );

        $this->db->where('id', $this->input->post('ren'));
        return $this->db->update('private_report', $data);
    }

    public function report_update()
    {
        $data = array(
            'mpregnancy' => $this->input->post('mpregnancy'), 
            'fpregnancy' => $this->input->post('fpregnancy'), 
            'msubsidy' => $this->input->post('msubsidy'), 
            'fsubsidy' => $this->input->post('fsubsidy'), 
            'mletpass' => $this->input->post('mletpass'), 
            'fletpass' => $this->input->post('fletpass'), 
            'mnotletpass' => $this->input->post('mnotletpass'), 
            'fnotletpass' => $this->input->post('fnotletpass'), 
            'delivery' => $this->input->post('delivery'), 
            'full_persons_classes' => $this->input->post('full_persons_classes'), 
            'peac' => $this->input->post('peac'), 
            'esc' => $this->input->post('esc'), 
            'gastpe' => $this->input->post('gastpe'), 
            'blended_learning' => $this->input->post('blended_learning'), 
            'distrance_learning' => $this->input->post('distrance_learning'), 
            'quarter' => $this->input->post('quarter'), 
            'year' => $this->input->post('year')
        );
        
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('private_report', $data);
    }

    public function private_other_new()
    {
        $data = array(
            'quarter' => $this->input->post('quarter'), 
            'year' => $this->input->post('year'),
            'nmale' => $this->input->post('nmale'), 
            'nfemale' => $this->input->post('nfemale'), 
            'school_id' => $this->input->post('school_id'), 
            'pq_id' => $this->input->post('id'), 
            'grade_level' => $this->input->post('grade_level')
        );
        
        return $this->db->insert('private_other_data', $data);
    }

    public function private_indicator_new()
    {
        $data = array(
            'question' => $this->input->post('question')
        );
        
        return $this->db->insert('private_report_question', $data);
    }

    public function insert_user()
    {

        $password = 'private112';
        $hash = password_hash($password, PASSWORD_DEFAULT);


        $data = array(
            'Username' => $this->input->post('schoolID'),
            'Password' => $hash,
            'position' => 'private',
            'fname' => $this->input->post('schoolName'),
            //'sex' => $this->input->post('Sex'),
            'user_id' => $this->input->post('schoolID'),
            'image' => "",

        );

        return $this->db->insert('users', $data);
    }

    public function get_column_totals($quarter, $year)
    {
        return $this->db
            ->select("
                SUM(mpregnancy) AS mpregnancy_total,
                SUM(fpregnancy) AS fpregnancy_total,
                SUM(msubsidy) AS msubsidy_total,
                SUM(fsubsidy) AS fsubsidy_total,
                SUM(mletpass) AS mletpass_total,
                SUM(fletpass) AS fletpass_total,
                SUM(mnotletpass) AS mnotletpass_total,
                SUM(fnotletpass) AS fnotletpass_total,
                SUM(delivery) AS delivery_total,
                SUM(full_persons_classes) AS full_persons_classes_total,
                SUM(peac) AS peac_total,
                SUM(esc) AS esc_total,
                SUM(gastpe) AS gastpe_total,
                SUM(blended_learning) AS blended_learning_total,
                SUM(distrance_learning) AS distance_learning_total
            ", false)
            ->from('private_report')
            ->where('quarter', (int)$quarter)
            ->where('year', (int)$year)
            ->get()
            ->row(); 
    }

    public function get_others_total($quarter, $year,$gl,$pq_id)
    {
        return $this->db
            ->select("
                SUM(nmale) AS nmale_total,
                SUM(nfemale) AS nfemale_total
            ", false)
            ->from('private_other_data')
            ->where('quarter', (int)$quarter)
            ->where('year', (int)$year)
            ->where('grade_level', (int)$gl)
            ->where('pq_id', (int)$pq_id)
            ->get()
            ->row(); 
    }









}

?>