<?php
class Sbfp_model extends CI_Model{

    public function sbfp_enrollment_update()
	{

		$data = array(
			'YearLevel' => $this->input->post('YearLevel'),
			'Course' => $this->input->post('Course'),
			'Section' => $this->input->post('Section'), 
			'StudeStatus' => $this->input->post('StudeStatus'), 
			'BalikAral' => $this->input->post('BalikAral'), 
			'IP' => $this->input->post('IP'), 
			'FourPs' => $this->input->post('FourPs'), 
			'Repeater' => $this->input->post('Repeater'), 
			'Transferee' => $this->input->post('Transferee'), 
            'dewormStat' => $this->input->post('dewormStat'), 
            'pc_for_milk' => $this->input->post('pc_for_milk'), 
            'sbfp_ben_prevyear' => $this->input->post('sbfp_ben_prevyear')

		);

		$this->db->where('semstudentid', $this->input->post('id'));
		return $this->db->update('semesterstude', $data);
	}

	public function get_Adviser($SY)
    {
        $this->db->where('SY', $SY);
        $this->db->select('sections.*, hris_staff.*'); 
        $this->db->from('sections');
        $this->db->join('hris_staff', 'hris_staff.IDNumber = sections.IDNumber', 'left'); 
        $this->db->where('sections.school_id', $this->session->username);

        $query = $this->db->get();
        return $query->result();
    }

	public function get_staff()
    {
        $query = $this->db->get('hris_staff');
        return $query->result();
    }

    public function get_Major()
    {
        $this->db->select('Major');
        $this->db->from('course_table');
        $this->db->order_by('Major');
        $query = $this->db->get();
        return $query->result();
    }

    public function insertadviser(){

        $data = array(
            'Section' => $this->input->post('Section'),
            'YearLevel' => $this->input->post('YearLevel'),
            'IDNumber' => $this->input->post('IDNumber'),
            //'adviserName' => $this->input->post('adviserName'),
            'Sem' => $this->input->post('Sem'),
            'SY' => $this->session->cur_sy,
            'school_id' => $this->session->username
        );
  
        return $this->db->insert('sections', $data);
    }

    public function insert_gl(){

        $data = array(
            'CourseCode' => $this->input->post('CourseCode'), 
            'CourseDescription' => $this->input->post('CourseDescription'), 
            'Major' => $this->input->post('Major'),
            'school_id' => $this->session->username
        );
  
        return $this->db->insert('course_table', $data);
    }

    public function update_gl(){

        $data = array(
            'CourseCode' => $this->input->post('CourseCode'), 
            'CourseDescription' => $this->input->post('CourseDescription'), 
            'Major' => $this->input->post('Major'),
            'school_id' => $this->session->username
        );
  
        $this->db->where('courseid', $this->input->post('sectionID'));
        return $this->db->update('course_table', $data);
    }

    public function enroll_insert()
    {
        $data = array(
            'StudentNumber' => $this->input->post('StudentNumber'),
            'Course' => $this->input->post('Course'),
            'YearLevel' => $this->input->post('YearLevel'),
            'Status' => 'Enrolled',
            'Semester' => $this->input->post('Semester'),
            'SY' => $this->input->post('school_year'),
            'Section' => $this->input->post('Section'),
            'StudeStatus' => $this->input->post('StudeStatus'),
            //'Scholarship' => $this->input->post('Scholarship'), 
            //'YearLevelStat' => $this->input->post('YearLevelStat'), 
            //'Major' => $this->input->post('Major'), 
            'Track' => $this->input->post('Track'),
            'Qualification' => $this->input->post('Qualification'),
            'BalikAral' => $this->input->post('BalikAral'),
            'IP' => $this->input->post('IP'),
            'FourPs' => $this->input->post('FourPs'),
            'Repeater' => $this->input->post('Repeater'),
            'Transferee' => $this->input->post('Transferee'),
            'EnrolledDate' => date("Y-m-d"),
            'Adviser' => $this->input->post('AdviserID'),
            'schoolID' => $this->session->username,
            'ssn' => $this->input->post('ssn'),
            'dewormStat' => $this->input->post('dewormStat'), 
            'pc_for_milk' => $this->input->post('pc_for_milk'), 
            'sbfp_ben_prevyear' => $this->input->post('sbfp_ben_prevyear')
        );

        return $this->db->insert('semesterstude', $data);
    }

    public function sbfp_med_bmi_update()
	{

		$data = array(
			'weight' => $this->input->post('weight'),
			'height' => $this->input->post('height'),
		);

		$this->db->where('sID', $this->input->post('id'));
		return $this->db->update('semester_sbfp', $data);
	}

    public function insert_track_and_strand(){

        $data = array(
            'track' => $this->input->post('track'), 
            'strand' => $this->input->post('strand'),
            'school_id' => $this->session->username
        );
  
        return $this->db->insert('track_strand', $data);
    }

    public function insert_track_and_strand_update(){

        $data = array(
            'track' => $this->input->post('track'), 
            'strand' => $this->input->post('strand')
        );

        $this->db->where('trackID', $this->input->post('id'));
        return $this->db->update('track_strand', $data);
    }
    

    

}