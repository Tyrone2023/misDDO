<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Coor_model extends CI_Model{

    public function __construct(){
          $this->load->database();

    }

    public function coor_personnel_insert()
    {
        $data = array(
            'staff_id' => $this->input->post('staff'),
            'type_id' => $this->input->post('type'), 
            'coor_id' => $this->input->post('coor'), 
            'stat' => 0,
            'school_id' => $this->session->username
        );
        return $this->db->insert('cid_coor_staff', $data);
    }

    public function proficiency_insert()
    {
        $data = array(
            'year' => $this->input->post('year'), 
            'quarter' => $this->input->post('quarter'), 
            'section' => $this->input->post('section'), 
            'grade_level' => $this->input->post('grade_level'),
            'school_id' => $this->input->post('school_id'), 
            'mps' => $this->input->post('mps'), 
            'abovemps' => $this->input->post('abovemps'), 
            'learners' => $this->input->post('learners'), 
            'total' => $this->input->post('total'),
            'district_id' => $this->input->post('district_id'),
            'staff_id' => $this->session->username,
            'subject' => $this->input->post('subject')
        );
        return $this->db->insert('cid_coor_math_proficiency', $data);
    }

    public function get_summary_by_level_subject($q){
        $this->db->select('
            AVG(mps) AS average_mps,
            SUM(abovemps) AS total_abovemps,
            SUM(learners) AS total_learners,
            SUM(total) AS total_total
        ');
        $this->db->from('cid_coor_math_proficiency');
        $this->db->where('year', $this->session->cur_fy);
        $this->db->where('quarter', $q);

        $query = $this->db->get();
        return $query->row(); 
    }

    public function get_summary_by_grade_level($q,$grade){
        $this->db->select('
            AVG(mps) AS average_mps,
            SUM(abovemps) AS total_abovemps,
            SUM(learners) AS total_learners,
            SUM(total) AS total_total
        ');
        $this->db->from('cid_coor_math_proficiency');
        $this->db->where('year', $this->session->cur_fy);
        $this->db->where('quarter', $q);
         $this->db->where('grade_level', $grade);

        $query = $this->db->get();
        return $query->row(); 
    }

    public function get_summary_district_by_grade_level($d_id,$q,$grade){
        $this->db->select('
            AVG(mps) AS average_mps,
            SUM(abovemps) AS total_abovemps,
            SUM(learners) AS total_learners,
            SUM(total) AS total_total
        ');
        $this->db->from('cid_coor_math_proficiency');
        $this->db->where('year', $this->session->cur_fy);
        $this->db->where('quarter', $q);
        $this->db->where('grade_level', $grade);
        $this->db->where('district_id', $d_id);
        $this->db->where('subject', $this->uri->segment(3));

        $query = $this->db->get();
        return $query->row(); 
    }

    public function get_summary_by_level_subject_by_district($d_id,$q){
        $this->db->select('
            AVG(mps) AS average_mps,
            SUM(abovemps) AS total_abovemps,
            SUM(learners) AS total_learners,
            SUM(total) AS total_total
        ');
        $this->db->from('cid_coor_math_proficiency');
        $this->db->where('year', $this->session->cur_fy);
        $this->db->where('district_id', $d_id);
        $this->db->where('quarter', $q);
        $this->db->where('subject', $this->uri->segment(3));

        $query = $this->db->get();
        return $query->row(); 
    }

    public function get_summary_by_level_subject_by_school_report($d_id,$q){
        $this->db->select('
            AVG(mps) AS average_mps,
            SUM(abovemps) AS total_abovemps,
            SUM(learners) AS total_learners,
            SUM(total) AS total_total
        ');
        $this->db->from('cid_coor_math_proficiency');
        $this->db->where('year', $this->session->cur_fy);
        $this->db->where('school_id', $d_id);
        $this->db->where('quarter', $q);
        $this->db->where('subject', $this->uri->segment(4));

        $query = $this->db->get();
        return $query->row(); 
    }

    public function get_summary_by_level_subject_by_school($school_id,$q){
        $this->db->select('
            AVG(mps) AS average_mps,
            SUM(abovemps) AS total_abovemps,
            SUM(learners) AS total_learners,
            SUM(total) AS total_total
        ');
        $this->db->from('cid_coor_math_proficiency');
        $this->db->where('year', $this->session->cur_fy);
        $this->db->where('school_id', $school_id);
        $this->db->where('quarter', $q);

        $query = $this->db->get();
        return $query->row(); 
    }

   

    public function get_coor_by_category($category_id) {
        return $this->db->get_where('cid_coor_list', ['coor_type_id' => $category_id])->result();
    }

    public function insert_section_number(){

      $data = array(
          'count' => $this->input->post('count'), 
          'fy' => $this->input->post('fy'), 
          'staff_id' => $this->input->post('staff_id'),
          'school_id' => $this->input->post('school_id'),
          'district_id' => $this->input->post('district_id')
      );

      return $this->db->insert('section_count', $data);
    }








}

?>