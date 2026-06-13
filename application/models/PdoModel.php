<?php
class PdoModel extends CI_Model
{

    public function bkdp_insert()
    {
        $data = array(
            'school_id' => $this->input->post('school_id'),
            'fy' => $this->input->post('fy'),
            'district_id' => $this->input->post('district'),
            'q1' => $this->input->post('q1'),
            'q2' => $this->input->post('q2'),
            'q3' => $this->input->post('q3'),
            'q4' => $this->input->post('q4'),
            'q5' => $this->input->post('q5'),
            'q6' => $this->input->post('q6'),
            'q7' => $this->input->post('q7'),
            'q8' => $this->input->post('q8'),
            'q9' => $this->input->post('q9'),
            'q10' => $this->input->post('q10'),
            'q11' => $this->input->post('q11'),
            'q12' => $this->input->post('q12'),
            'q13' => $this->input->post('q13'),
            'q14' => $this->input->post('q14'),
            'q15' => $this->input->post('q15'),
            'q16' => $this->input->post('q16'),
            'q17' => $this->input->post('q17'),
            'q18' => $this->input->post('q18'),
            'q19' => $this->input->post('q19'),
            'r1' => $this->input->post('r1'),
            'r2' => $this->input->post('r2'),
            'r3' => $this->input->post('r3'),
            'r4' => $this->input->post('r4'),
            'r5' => $this->input->post('r5'),
            'r6' => $this->input->post('r6'),
            'r7' => $this->input->post('r7'),
            'r8' => $this->input->post('r8'),
            'r9' => $this->input->post('r9'),
            'r10' => $this->input->post('r10'),
            'r11' => $this->input->post('r11'),
            'r12' => $this->input->post('r12'),
            'r13' => $this->input->post('r13'),
            'r14' => $this->input->post('r14'),
            'r15' => $this->input->post('r15'),
            'r16' => $this->input->post('r16'),
            'r17' => $this->input->post('r17'),
            'r18' => $this->input->post('r18'),
            'r19' => $this->input->post('r19'),
            'bp' => $this->input->post('bp'),
            'ic' => $this->input->post('ic'),
            'sr' => $this->input->post('sr'),
            'stat' => 0
            
        );

        return $this->db->insert('bkdp', $data);
    }


} ?>