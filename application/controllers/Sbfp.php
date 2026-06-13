<?php

class Sbfp extends CI_Controller
{

	function __construct()
	{
		parent::__construct();
		$this->load->model('Sbfp_model');
		$this->load->model('Common');
		$this->load->model('StudentModel');
		

	}

	public function calculate_bmi()
	{
		$user_id = $this->session->userdata('user_id');

		if (!$user_id) {
			echo "<script>alert('User ID is missing.'); window.location.href='" . base_url('Sbfp/sbfp_bmi') . "';</script>";
			exit;
		}

		$this->db->select('*');
		$this->db->from('semester_sbfp');
		$this->db->join('studeprofile', 'semester_sbfp.StudentNumber = studeprofile.StudentNumber');
		$this->db->where('semester_sbfp.schoolID', $user_id);
		$this->db->where('studeprofile.Sex', 'Male');
		$query = $this->db->get();
		$students = $query->result();

		if (empty($students)) {
			echo "<script>alert('No student records found.'); window.location.href='" . base_url('Sbfp/sbfp_bmi') . "';</script>";
			exit;
		}

		foreach ($students as $student) {
			$height = $student->height;
			$weight = $student->weight;
			$months = $student->months;

			if ($height > 0) {
				$bmi = round($weight / ($height * $height), 2);
			} else {
				$bmi = 0;
			}

			if ($months == 72) {
				if ($bmi <= 12.0) {
					$bmi_eqv = 'Severely Wasted';
				} elseif ($bmi > 12.0 && $bmi <= 12.9) {
					$bmi_eqv = 'Wasted';
				} elseif ($bmi >= 13.0 && $bmi <= 18.5) {
					$bmi_eqv = 'Normal';
				} elseif ($bmi >= 18.6 && $bmi <= 20.7) {
					$bmi_eqv = 'Overweight';
				} else {
					$bmi_eqv = 'Obese';
				}
			} else {
				if ($bmi < 18.5) {
					$bmi_eqv = 'Underweight';
				} elseif ($bmi >= 18.5 && $bmi <= 24.9) {
					$bmi_eqv = 'Normal weight';
				} elseif ($bmi >= 25.0 && $bmi <= 29.9) {
					$bmi_eqv = 'Overweight';
				} else {
					$bmi_eqv = 'Obesity';
				}
			}

			$this->db->where('sID', $student->sID);
			$this->db->update('semester_sbfp', ['bmi' => $bmi, 'bmi_eqv' => $bmi_eqv]);
		}

		$this->session->set_flashdata('success', 'BMI values successfully updated.');
		redirect('Sbfp/sbfp_bmiv2');
	}



	public function calculate_bmi_eqv()
	{
		$user_id = $this->session->userdata('user_id');

		if (!$user_id) {
			echo "<script>alert('User ID is missing.'); window.location.href='" . base_url('Sbfp/sbfp_bmi') . "';</script>";
			return;
		}

		$this->db->select('*');
		$this->db->from('semester_sbfp');
		$this->db->join('studeprofile', 'semester_sbfp.StudentNumber = studeprofile.StudentNumber');
		$this->db->where('semester_sbfp.schoolID', $user_id);
		$this->db->where('studeprofile.Sex', 'Male');
		$query = $this->db->get();
		$students = $query->result();

		if (empty($students)) {
			echo "<script>alert('No student records found.'); window.location.href='" . base_url('Sbfp/sbfp_bmi') . "';</script>";
			return;
		}

		// Load the BMI classification table
		require_once(APPPATH . 'config/bmi_boys_reference_table.php');

		foreach ($students as $student) {
			$bmi = $student->bmi;
			$months = $student->months;
			$bmi_eqv = 'Undefined';

			if (!is_numeric($bmi) || !is_numeric($months)) {
				log_message('error', "Invalid BMI or months for student ID: {$student->sID}");
				continue;
			}

			if (isset($bmi_table[$months])) {
				$ref = $bmi_table[$months];

				if ($bmi <= $ref['sw']) {
					$bmi_eqv = 'Severely Wasted';
				} elseif ($bmi >= $ref['w'][0] && $bmi <= $ref['w'][1]) {
					$bmi_eqv = 'Wasted';
				} elseif ($bmi >= $ref['n'][0] && $bmi <= $ref['n'][1]) {
					$bmi_eqv = 'Normal';
				} elseif ($bmi >= $ref['ow'][0] && $bmi <= $ref['ow'][1]) {
					$bmi_eqv = 'Overweight';
				} elseif ($bmi >= $ref['ob']) {
					$bmi_eqv = 'Obese';
				}
			} else {
				log_message('error', "Months value {$months} not found in BMI table for student ID: {$student->sID}");
			}

			$this->db->where('sID', $student->sID);
			$this->db->update('semester_sbfp', ['bmi_eqv' => $bmi_eqv]);
		}

		$this->session->set_flashdata('success', 'BMI interpretations successfully updated.');
		redirect('Sbfp/sbfp_bmiv2');
	}

	public function calculate_bmi_girls()
	{
		$user_id = $this->session->userdata('user_id');

		if (!$user_id) {
			echo "<script>alert('User ID is missing.'); window.location.href='" . base_url('Sbfp/sbfp_bmi') . "';</script>";
			exit;
		}

		$this->db->select('*');
		$this->db->from('semester_sbfp');
		$this->db->join('studeprofile', 'semester_sbfp.StudentNumber = studeprofile.StudentNumber');
		$this->db->where('semester_sbfp.schoolID', $user_id);
		$this->db->where('studeprofile.Sex', 'Female');
		$query = $this->db->get();
		$students = $query->result();

		if (empty($students)) {
			echo "<script>alert('No student records found.'); window.location.href='" . base_url('Sbfp/sbfp_bmi') . "';</script>";
			exit;
		}

		foreach ($students as $student) {
			$height = $student->height;
			$weight = $student->weight;
			$months = $student->months;

			if ($height > 0) {
				$bmi = round($weight / ($height * $height), 2);
			} else {
				$bmi = 0;
			}

			if ($months == 72) {
				if ($bmi <= 12.0) {
					$bmi_eqv = 'Severely Wasted';
				} elseif ($bmi > 12.0 && $bmi <= 12.9) {
					$bmi_eqv = 'Wasted';
				} elseif ($bmi >= 13.0 && $bmi <= 18.5) {
					$bmi_eqv = 'Normal';
				} elseif ($bmi >= 18.6 && $bmi <= 20.7) {
					$bmi_eqv = 'Overweight';
				} else {
					$bmi_eqv = 'Obese';
				}
			} else {
				if ($bmi < 18.5) {
					$bmi_eqv = 'Underweight';
				} elseif ($bmi >= 18.5 && $bmi <= 24.9) {
					$bmi_eqv = 'Normal weight';
				} elseif ($bmi >= 25.0 && $bmi <= 29.9) {
					$bmi_eqv = 'Overweight';
				} else {
					$bmi_eqv = 'Obesity';
				}
			}

			$this->db->where('sID', $student->sID);
			$this->db->update('semester_sbfp', ['bmi' => $bmi, 'bmi_eqv' => $bmi_eqv]);
		}

		$this->session->set_flashdata('success', 'BMI values successfully updated.');
		redirect('Sbfp/sbfp_bmiv2');
	}



	public function calculate_bmi_eqv_girls()
	{
		$user_id = $this->session->userdata('user_id');

		if (!$user_id) {
			echo "<script>alert('User ID is missing.'); window.location.href='" . base_url('Sbfp/sbfp_bmi') . "';</script>";
			return;
		}

		// $this->db->where('schoolID', $user_id);
		// $query = $this->db->get('semester_sbfp');
		// $students = $query->result();

		$this->db->select('*');
		$this->db->from('semester_sbfp');
		$this->db->join('studeprofile', 'semester_sbfp.StudentNumber = studeprofile.StudentNumber');
		$this->db->where('semester_sbfp.schoolID', $user_id);
		$this->db->where('studeprofile.Sex', 'Female');
		$query = $this->db->get();
		$students = $query->result();

		if (empty($students)) {
			echo "<script>alert('No student records found.'); window.location.href='" . base_url('Sbfp/sbfp_bmi') . "';</script>";
			return;
		}

		// Load the BMI classification table
		require_once(APPPATH . 'config/bmi_table_girls.php');

		foreach ($students as $student) {
			$bmi = $student->bmi;
			$months = $student->months;
			$bmi_eqv = 'Undefined';

			if (!is_numeric($bmi) || !is_numeric($months)) {
				log_message('error', "Invalid BMI or months for student ID: {$student->sID}");
				continue;
			}

			if (isset($bmi_table[$months])) {
				$ref = $bmi_table[$months];

				if ($bmi <= $ref['sw']) {
					$bmi_eqv = 'Severely Wasted';
				} elseif ($bmi >= $ref['w'][0] && $bmi <= $ref['w'][1]) {
					$bmi_eqv = 'Wasted';
				} elseif ($bmi >= $ref['n'][0] && $bmi <= $ref['n'][1]) {
					$bmi_eqv = 'Normal';
				} elseif ($bmi >= $ref['ow'][0] && $bmi <= $ref['ow'][1]) {
					$bmi_eqv = 'Overweight';
				} elseif ($bmi >= $ref['ob']) {
					$bmi_eqv = 'Obese';
				}
			} else {
				log_message('error', "Months value {$months} not found in BMI table for student ID: {$student->sID}");
			}

			$this->db->where('sID', $student->sID);
			$this->db->update('semester_sbfp', ['bmi_eqv' => $bmi_eqv]);
		}

		$this->session->set_flashdata('success', 'BMI interpretations successfully updated.');
		redirect('Sbfp/sbfp_bmiv2');
	}


	public function getEnrolees()
{
    $sy = $this->input->post('sy');
    $sbfp_date = $this->input->post('sbfp_date');
    $w_group = $this->input->post('w_group');
    $year_level = $this->input->post('YearLevel');
    $section = $this->input->post('Section');

    // Check for existing records to avoid duplicate entries
    $this->db->where('sy', $sy);
    $this->db->where('YearLevel', $year_level);
    $this->db->where('Section', $section);
    $this->db->where('w_group', $w_group);
    $query = $this->db->get('semester_sbfp');

    if ($query->num_rows() > 0) {
        // Duplicate found, set flashdata and redirect
        $this->session->set_flashdata('danger', 
            'Duplicate entry: Records already exist for SY ' . $sy . ', Year Level ' . $year_level . ', Section ' . $section . ', and Group ' . $w_group . '.');
        redirect('Sbfp/sbfp_bmi');
        return;
    }

    // No duplicate, proceed with insert
    $sql = "
        INSERT INTO semester_sbfp (
            StudentNumber, y_mo, months, sy, schoolID, bmi, bmi_eqv, sbfp_date, 
            height, w_group, YearLevel, Section, weight
        )
        SELECT 
            sp.StudentNumber,
            CONCAT(
                TIMESTAMPDIFF(YEAR, sp.BirthDate, CURDATE()), '-', 
                MOD(TIMESTAMPDIFF(MONTH, sp.BirthDate, CURDATE()), 12)
            ) AS y_mo,
            TIMESTAMPDIFF(MONTH, sp.BirthDate, CURDATE()) AS months,
            ss.SY,
            ss.SchoolID,
            '0' AS bmi,
            '' AS bmi_eqv,
            ? AS sbfp_date,
            '0' AS height,
            ? AS w_group,
            ? AS YearLevel,  
            ? AS Section,
            '0' AS weight    
        FROM studeprofile sp
        INNER JOIN semesterstude ss 
            ON sp.StudentNumber = ss.StudentNumber
        LEFT JOIN semester_sbfp sb 
            ON sp.StudentNumber = sb.StudentNumber 
            AND sb.sy = ? 
            AND sb.YearLevel = ? 
            AND sb.Section = ? 
            AND sb.w_group = ?
        WHERE ss.SY = ? 
        AND ss.YearLevel = ?
        AND ss.Section = ?
        AND sb.StudentNumber IS NULL
    ";

    $this->db->query($sql, array(
        $sbfp_date,
        $w_group,
        $year_level,
        $section,
        $sy,
        $year_level,
        $section,
        $w_group,
        $sy,
        $year_level,
        $section
    ));

    $this->session->set_flashdata('success', 
        'Records successfully inserted for SY ' . $sy . ', Year Level ' . $year_level . ', Section ' . $section . ', Group ' . $w_group . '.');
    redirect('Sbfp/sbfp_bmi');
}

	public function sbfp_bmi()
	{
		$result['csy'] = $this->Common->one_cond_row_select('mis_settings','settingsID,fy','settingsID',1);
		$csy = $this->Common->one_cond_row_select('mis_settings','settingsID,fy','settingsID',1);
		$sy = $csy->fy;
		$YearLevel = $this->input->post('YearLevel');
		$Section = $this->input->post('Section');
		$w_group = $this->input->post('w_group');

		$this->session->set_userdata('bmi_sy', $sy);
		$this->session->set_userdata('bmi_YearLevel', $YearLevel);
		$this->session->set_userdata('bmi_Section', $Section);
		$this->session->set_userdata('bmi_w_group', $w_group);

		$result['gl'] = $this->Common->getLevel();
		$result['section'] = $this->Common->one_cond('sections','school_id',$this->session->username);

		if ($this->input->post('view')) {
			// Store SY in session for use elsewhere (if needed)
			$this->session->set_userdata('sy', $sy);

			// Fetch students matching filters
			$result['data'] = $this->StudentModel->sbfp_student($sy, $YearLevel, $Section, $w_group);

			// Also pass filter values back to view for form repopulation
			$result['sy'] = $sy;
			$result['YearLevel'] = $YearLevel;
			$result['Section'] = $Section;
			$result['w_group'] = $w_group;
		} else {
			// No filters submitted yet
			$result['data'] = [];
			$result['sy'] = '';
			$result['YearLevel'] = '';
			$result['Section'] = '';
			$result['w_group'] = '';
		}

		$this->load->view('med_bmi', $result);
	}

	public function sbfp_bmi_dn()
	{


		$result['school'] = $this->Common->no_cond_group_ob('schools', 'schoolID', 'schoolName', 'ASC');

		//dn - division nurse
		$sy = $this->input->post('sy');
		$YearLevel = $this->input->post('YearLevel');
		$Section = $this->input->post('Section');
		$w_group = $this->input->post('w_group');
		

		$this->session->set_userdata('bmi_sy', $sy);
		$this->session->set_userdata('bmi_YearLevel', $YearLevel);
		$this->session->set_userdata('bmi_Section', $Section);
		$this->session->set_userdata('bmi_w_group', $w_group);
		

		

		$result['gl'] = $this->Common->getLevel();
		$result['section'] = $this->Common->one_cond('sections','school_id',$this->session->bmi_school_id);

		if ($this->input->post('view')) {
			// Store SY in session for use elsewhere (if needed)
			$this->session->set_userdata('sy', $sy);

			// Fetch students matching filters
			$result['data'] = $this->StudentModel->sbfp_student($sy, $YearLevel, $Section, $w_group);

			// Also pass filter values back to view for form repopulation
			$result['sy'] = $sy;
			$result['YearLevel'] = $YearLevel;
			$result['Section'] = $Section;
			$result['w_group'] = $w_group;
		} else {
			// No filters submitted yet
			$result['data'] = [];
			$result['sy'] = '';
			$result['YearLevel'] = '';
			$result['Section'] = '';
			$result['w_group'] = '';
		}

		$this->load->view('med_bmi_dn', $result);
	}

	public function sbfp_bmi_dn_form()
	{

		$school_id = $this->input->post('schoolID');

		$this->session->set_userdata('bmi_school_id', $school_id);

		if($this->session->bmi_school_id){
			redirect(base_url() . 'Sbfp/sbfp_bmi_dn');
		}



		$result['school'] = $this->Common->no_cond_group_ob('schools', 'schoolID', 'schoolName', 'ASC');
		$this->load->view('med_bmi_dn_form', $result);
	}



	function sbfp_update()
	{
		$result['sbfp'] = $this->Common->one_cond_row('semesterstude', 'semstudentid', $this->uri->segment(3));

		if ($this->input->post('submit')) {
			$this->SGODModel->sbfp_update();
			$this->session->set_flashdata('success', 'Updated successfully.');
			redirect(base_url() . 'Sbfp/sbfp_update/'.$this->input->post('id'));
		}

		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('sbfp_update', $result);
		$this->load->view('templates/footer');
	}

	public function delete_sbfp()
	{
		$this->Common->del('semesterstude', 'semstudentid', $this->uri->segment(3));
		$this->Page_model->insert_at('Deleted SBFP', $this->uri->segment(3));
		$this->session->set_flashdata('danger', 'Deleted successfully!');
		redirect('Page/sbfp_form');
	}

	public function delete_gl()
	{
		$this->Common->del('course_table', 'courseid', $this->uri->segment(3));
		$this->Page_model->insert_at('Deleted Grade Level', $this->uri->segment(3));
		$this->session->set_flashdata('danger', 'Deleted successfully!');
		redirect('Sbfp/grade_level_add');
	}

	function sbfp_nut_stat()
	{
		//$result['sbfp'] = $this->Common->one_cond_row('semesterstude','semstudentid',$this->uri->segment(3));
		$result['tittle'] = 'ren';


		if ($this->session->d_id == 0 && $this->session->position == 'SHNS') {
			$this->load->view('nut_stat_div', $result);
		} elseif ($this->session->d_id == 0 && $this->session->position == 'School') {
			$this->load->view('nut_stat', $result);
		} else {
			$this->load->view('nut_stat_district', $result);
		}
	}

	function sbfp_div_report()
	{
		$result['district'] = $this->Common->no_cond('district');

		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('sbfp_div_report', $result);
		$this->load->view('templates/footer');
	}

	function sbfp_enrollment_update()
	{
		$result['district'] = 'fdsafdsa';

		$result['semstud'] = $this->Common->one_cond_row('semesterstude', 'semstudentid', $this->uri->segment(3));
		$result['course'] = $this->Common->no_cond('course_table');
		$result['dep'] = $this->Common->no_cond_group_ob('course_table','CourseDescription','CourseDescription','ASC');
		$result['track'] = $this->Common->no_cond('track_strand','CourseDescription','CourseDescription','ASC');

		if($this->input->post('submit')) {
			$this->Sbfp_model->sbfp_enrollment_update();
			$this->session->set_flashdata('success', 'Data successfully updated!');
			redirect(base_url() . 'Sbfp/sbfp_enrollment_update/'.$this->input->post('id'));
		}

		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('enrollment_form_update', $result);
		$this->load->view('templates/footer');
	}

	function sbfp_enrollment_delete()
	{
			$this->Common->delete('semesterstude','semstudentid',3);
			$this->session->set_flashdata('danger', 'Data successfully deleted!');
			redirect(base_url() . 'Masterlist/enrolledList?sy='.$this->uri->segment(4));
		
	}

	function section_delete()
	{
			$this->Common->delete('sections','sectionID',3);
			$this->session->set_flashdata('danger', 'Data successfully deleted!');
			redirect(base_url() . 'Sbfp/SectionAdviser');
		
	}

	public function SectionAdviser()
	{
		$sy = $this->session->cur_sy;

		$result['data'] = $this->Sbfp_model->get_Adviser($sy);
		$result['staff'] = $this->Common->one_cond_select('hris_staff','schoolID,IDNumber,FirstName,MiddleName,LastName','schoolID',$this->session->username);
		$result['grade'] = $this->Sbfp_model->get_Major();


		if ($this->input->post('save')) {
			$this->Sbfp_model->insertadviser();
			redirect(base_url() . 'Sbfp/SectionAdviser?sy='.$sy);
		}
			$this->load->view('templates/head');
            $this->load->view('templates/header');
            $this->load->view('adviser', $result);
            $this->load->view('templates/footer');
		
	}

	

	public function grade_level_add()
	{
		$sy = $this->input->get('sy');

		$result['data'] = $this->Common->no_cond('course_table');
		$result['staff'] = $this->Common->one_cond_select('hris_staff','schoolID,IDNumber,FirstName,MiddleName,LastName','schoolID',$this->session->username);
		$result['Major'] = $this->Sbfp_model->get_Major();


		if ($this->input->post('save')) {
			$this->Sbfp_model->insert_gl();
			redirect(base_url() . 'Sbfp/grade_level_add');
		}

		if ($this->input->post('update')) {
			$this->Sbfp_model->update_gl();
			redirect(base_url() . 'Sbfp/grade_level_add');
		}

			$this->load->view('templates/head');
            $this->load->view('templates/header');
            $this->load->view('grade_level_list', $result);
            $this->load->view('templates/footer');
		
	}

	public function enroll()
	{
		$this->Sbfp_model->enroll_insert();
		$this->session->set_flashdata('success', 'Student successfully enrolled.');
		redirect(base_url() . 'Masterlist/enrolledList?sy=' . $this->input->post('school_year'));
	}
	
	
	function sbfp_med_bmi_update()
	{
		$sy = $this->input->post('sy');
		$YearLevel = $this->input->post('YearLevel');
		$Section = $this->input->post('Section');
		$w_group = $this->input->post('w_group');

		$result['sbfp'] = $this->Common->one_cond_row('semester_sbfp', 'sID', $this->uri->segment(3));

		if ($this->input->post('submit')){
			$this->Sbfp_model->sbfp_med_bmi_update();
			$this->session->set_flashdata('success', 'Updated successfully.');
			redirect(base_url() . 'Sbfp/sbfp_bmi_update?sy='.$sy.'&Section='.$Section.'&w_group='.$w_group.'&YearLevel='.$YearLevel);
		}

		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('sbfp_med_bmi_update', $result);
		$this->load->view('templates/footer');
	}

	function sbfp_bmi_data_update()
	{
		$this->Sbfp_model->sbfp_med_bmi_update();
		$this->session->set_flashdata('success', 'Updated successfully.');
		redirect('Sbfp/sbfp_bmiv2');
	}

	public function sbfp_bmi_update()
	{
		$sy = $this->input->get('sy');
		$YearLevel = $this->input->get('YearLevel');
		$Section = $this->input->get('Section');
		$w_group = $this->input->get('w_group');


		$result['gl'] = $this->StudentModel->getLevel();
		$result['section'] = $this->Common->one_cond('sections','school_id',$this->session->username);

		$result['data'] = $this->StudentModel->sbfp_student($sy, $YearLevel, $Section, $w_group);

		
		$this->load->view('med_bmi', $result);
	}

	public function sbfp_bmiv2()
	{
		$sy = $this->session->bmi_sy;
		$YearLevel = $this->session->bmi_YearLevel;
		$Section = $this->session->bmi_Section;
		$w_group = $this->session->bmi_w_group;


		$result['gl'] = $this->StudentModel->getLevel();
		$result['section'] = $this->Common->one_cond('sections','school_id',$this->session->username);

		$result['data'] = $this->StudentModel->sbfp_student($sy, $YearLevel, $Section, $w_group);

		
		$this->load->view('med_bmi', $result);
	}

	public function enrolledList_count_gl(){
		$result['data'] = '';

		$result['yl'] = $this->Common->two_cond_select_gb('semesterstude','schoolID,SY,YearLevel','schoolID',$this->session->username,'SY',$this->input->get('sy'),'YearLevel');

		$this->load->view('enrolled_list_count_gl', $result);
	}

	public function enrolledList_of_names(){

		$result['data'] = $this->Common->four_cond_select('semesterstude','StudentNumber,Section,schoolID,SY,YearLevel,BalikAral','BalikAral','Yes','schoolID',$this->session->username,'SY',$this->input->get('sy'),'YearLevel',$this->input->get('yl'));
		$this->load->view('enrolled_list_of_names', $result);
	}

	public function enrollment_summary(){
		$result['data'] = "";
		
		$result['data'] = $this->Common->two_join_two_cond('semesterstude', 'studeprofile', '*', 'a.StudentNumber=b.StudentNumber', 'a.SY', $this->input->get('sy'), 'a.YearLevel',$this->input->get('gl'), 'b.LastName', 'ASC');
		$this->load->view('enrollment_sum', $result);
	}

	public function enrollment_age_profile(){
		$result['data'] = "";
		
		$result['data'] = $this->Common->two_join_three_cond('semesterstude', 'studeprofile', '*', 'a.StudentNumber=b.StudentNumber', 'a.SY', $this->input->get('sy'), 'a.YearLevel',$this->input->get('gl'), 'a.age',$this->input->get('age'), 'b.LastName', 'ASC');
		$this->load->view('enrollment_age_profile', $result);
	}

	public function track_and_strand_list(){

		$result['data'] = $this->Common->one_cond('track_strand','school_id',$this->session->username);


		if ($this->input->post('save')) {
			$this->Sbfp_model->insert_track_and_strand();
			redirect(base_url() . 'Sbfp/track_and_strand_list');
		}

		if ($this->input->post('update')) {
			$this->Sbfp_model->insert_track_and_strand_update();
			redirect(base_url() . 'Sbfp/track_and_strand_list');
		}


			$this->load->view('templates/head');
            $this->load->view('templates/header');
            $this->load->view('track_and_strand_list', $result);
            $this->load->view('templates/footer');
		
	}

	public function delete_track_strand()
	{
		$this->Common->del('track_strand', 'trackID', $this->uri->segment(3));
		$this->Page_model->insert_at('Deleted Track and Strand', $this->uri->segment(3));
		$this->session->set_flashdata('danger', 'Deleted successfully!');
		redirect('Sbfp/track_and_strand_list');
	}


	


	public function fetch_section()
	{
		if ($this->input->post('yearlevel')) {
			$output = '<option value=""></option>';
			$sections = $this->StudentModel->getSectionsByYearLevel($this->input->post('yearlevel'));
			foreach ($sections as $row) {
				$output .= '<option value="' . $row->Section . '">' . $row->Section . '</option>';
			}
			echo $output;
		}
	}
	


	
	public function fetch_adviser_id()
	{
		$section = $this->input->post('section');
		$yearLevel = $this->input->post('yearlevel'); // Get year level from request
		$schoolYear = $this->session->userdata('sy'); // Get SY from session

		if ($section && $yearLevel && $schoolYear) {
			$adviserID = $this->StudentModel->getAdviserByCriteria($section, $yearLevel, $schoolYear);

			log_message('debug', 'Adviser ID: ' . print_r($adviserID, true)); // Debugging

			if (!empty($adviserID)) {
				echo htmlspecialchars($adviserID->IDNumber, ENT_QUOTES);
			} else {
				echo ''; // Return empty if no adviser found
			}
		}
	}

	public function fetch_adviser_name()
	{
		$section = $this->input->post('section');
		$yearLevel = $this->input->post('yearlevel'); // Get year level from request
		$schoolYear = $this->session->userdata('sy'); // Get SY from session

		if ($section && $yearLevel && $schoolYear) {
			$adviserID = $this->StudentModel->getAdviserByCriteria($section, $yearLevel, $schoolYear);

			if (!empty($adviserID)) {
				$staff = $this->StudentModel->getStaffByID($adviserID->IDNumber);
				if (!empty($staff)) {
					$fullName = htmlspecialchars($staff->FirstName, ENT_QUOTES) . ' ' .
						htmlspecialchars($staff->MiddleName, ENT_QUOTES) . ' ' .
						htmlspecialchars($staff->LastName, ENT_QUOTES);
					echo $fullName;
				} else {
					echo 'Adviser not found';
				}
			} else {
				echo 'No Adviser found for the selected criteria';
			}
		}
	}


	function fetch_yearlevel()
	{

		if ($this->input->post('course')) {
			$output = '<option value=""></option>';
			$yearlevel = $this->StudentModel->getYearLevel($this->input->post('course'));
			foreach ($yearlevel as $row) {
				$output .= '<option value ="' . $row->Major . '">' . $row->Major . '</option>';
			}
			echo $output;
		}
	}




	function fetchStrand()
	{

		if ($this->input->post('track')) {
			$output = '<option value=""></option>';
			$strand = $this->SettingsModel->getStrand($this->input->post('track'));
			foreach ($strand as $row) {
				$output .= '<option value ="' . $row->strand . '">' . $row->strand . '</option>';
			}
			echo $output;
		}
	}



	


}
