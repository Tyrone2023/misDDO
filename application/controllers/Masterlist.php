<?php
class Masterlist extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->helper('url');
		$this->load->helper('url', 'form');
		$this->load->library('form_validation');
		$this->load->model('StudentModel');
		$this->load->model('SettingsModel');

		if ($this->session->userdata('logged_in') !== TRUE) {
			redirect('login');
		}
	}
	//Masterlist by Grade Level
	function masterlistAll()
	{
		$semester = $this->session->userdata('semester');
		$sy = $this->session->userdata('sy');
		$result['data'] = $this->StudentModel->masterlistAll($semester, $sy);
		$this->load->view('masterlist_all', $result);
	}

	//Masterlist by Grade Level
	function byGradeYL()
	{
		$yearlevel = $this->input->get('yearlevel');
		$semester = $this->session->userdata('semester');
		$sy = $this->session->userdata('sy');
		$result['data'] = $this->StudentModel->byGradeLevel($yearlevel, $semester, $sy);
		$result['data1'] = $this->StudentModel->byGradeLevelCount($yearlevel, $semester, $sy);
		$this->load->view('masterlist_by_gradelevel', $result);

		if ($this->input->post('submit')) {
			$yearlevel = $this->input->get('yearlevel');
			$semester = $this->input->get('semester');
			$sy = $this->session->userdata('sy');
			$result['data'] = $this->StudentModel->byGradeLevel($yearlevel, $semester, $sy);
			$result['data1'] = $this->StudentModel->byGradeLevelCount($yearlevel, $semester, $sy);
			$this->load->view('masterlist_by_gradelevel', $result);
		}
	}

	function trackMasterList()
	{
		$sy = $this->session->userdata('sy');
		$sem = $this->session->userdata('semester');
		$trackVal = $this->input->get('track');
		$strandVal = $this->input->get('strand');
		$gradelevel = $this->input->get('gradelevel');
		$result['track'] = $this->SettingsModel->getTrack();
		$result['data'] = $this->StudentModel->byTrack($sy, $trackVal, $strandVal, $gradelevel);
		$result['trackVal'] = $trackVal;
		$result['strandVal'] = $strandVal;
		$this->load->view('masterlist_by_track', $result);
	}

	function signupList()
	{
		$result['data'] = $this->StudentModel->bySignup();
		$this->load->view('masterlist_by_signup', $result);
	}
	//Masterlist by Grade Level
	function byGradeLevel()
	{
		$yearlevel = $this->input->get('yearlevel');
		// $semester = $this->input->get('semester'); 
		$sy = $this->session->userdata('sy');
		$result['data'] = $this->StudentModel->byGradeLevel($yearlevel, $sy);
		$this->load->view('masterlist_by_gradelevel', $result);

		if ($this->input->post('submit')) {
			$yearlevel = $this->input->get('yearlevel');
			$semester = $this->input->get('semester');
			$sy = $this->session->userdata('sy');
			$result['data'] = $this->StudentModel->byGradeLevel($yearlevel, $semester, $sy);
			$this->load->view('masterlist_by_gradelevel', $result);
		}
	}


	function bySection()
	{
		$section = $this->input->get('section');
		$YearLevel = $this->input->get('yearLevel');
		$sy = $this->session->userdata('sy');

		// Fetch data for the specified section
		$result['data'] = $this->StudentModel->bySection($section, $YearLevel, $sy);
		$result['section'] = $this->StudentModel->getSections();

		// Check for form submission
		if ($this->input->post('submit')) {
			$section = $this->input->get('section');
			$YearLevel = $this->input->get('yearlevel');
			$result['data'] = $this->StudentModel->bySection($section, $YearLevel, $sy);
		}

		// Load the view
		$this->load->view('masterlist_by_section', $result);
	}


	public function getSectionsByYearLevel()
	{
		$yearLevel = $this->input->get('yearLevel');
		$sections = $this->StudentModel->getSectionsByYearLevel($yearLevel);
		echo json_encode($sections);
	}


	//enrolled list
	function enrolledList(){
		
		
		$result['csy'] = $this->Common->one_cond_row_select('mis_settings','settingsID,fy','settingsID',1);
		$csy = $this->Common->one_cond_row_select('mis_settings','settingsID,fy','settingsID',1);
		$sy = $csy->fy;


		$yearlevel = $this->input->get('yearlevel');
		$section = $this->input->get('section');
	
		$result['data'] = $this->StudentModel->bySY($sy, $yearlevel, $section);
	
		$loggedInUsername = $this->session->username;
	
		// Fetch dropdown options based on current school and SY
		$result['yearlevels'] = $this->StudentModel->getYearLevels($loggedInUsername, $sy);
		$result['sections'] = $this->StudentModel->get_Sections($loggedInUsername, $sy);
	
		$result['course'] = $this->StudentModel->getCourse();
		$result['gl'] = $this->Common->no_cond('course_table');
		$result['track'] = $this->SettingsModel->getTrack();
		$result['strand'] = $this->SettingsModel->getStrand1();
		$result['stud'] = $this->Common->getStudeProfiles();
		$result['t'] = $this->SettingsModel->track();
		$result['s'] = $this->SettingsModel->strand();
		$result['section'] = $this->Common->two_cond('sections','school_id',$loggedInUsername,'SY',$sy);
	
		$this->load->view('enrolled_list', $result);
	}
	
	//Masterlist By School Year
	function bySY()
	{
		$sy = $this->session->userdata('sy');
		$sem = $this->session->userdata('semester');
		$result['data'] = $this->StudentModel->bySY($sy);
		$this->load->view('masterlist_by_sy', $result);

		if ($this->input->post('submit')) {
			$sy = $this->input->get('sy');
			$result['data'] = $this->StudentModel->bySY($$sy);
			$this->load->view('masterlist_by_sy', $result);
		}
	}
	//Masterlist Daily Enrollment
	function dailyEnrollees()
	{
		$date = $this->input->get('date');
		$result['data'] = $this->StudentModel->byDate($date);
		$result['data1'] = $this->StudentModel->byDateCourseSum($date);
		$this->load->view('masterlist_daily_enrollees', $result);

		if ($this->input->post('submit')) {
			$date = $this->input->get('date');
			$result['data'] = $this->StudentModel->byDate($date);
			$result['data1'] = $this->StudentModel->byDateCourseSum($date);
			$this->load->view('masterlist_daily_enrollees', $result);
		}
	}

	//Masterlist by Course
	function byCourse()
	{
		$sy = $this->session->userdata('sy');
		$sem = $this->session->userdata('semester');
		$course = $this->input->get('course');
		$result['data'] = $this->StudentModel->byCourse($course, $sy, $sem);
		$this->load->view('masterlist_by_course', $result);

		if ($this->input->post('submit')) {
			$sy = $this->session->userdata('sy');
			$department = $this->input->get('department');
			$result['data'] = $this->StudentModel->byDepartment($department, $sy);
			$this->load->view('masterlist_by_department', $result);
		}
	}

	//Masterlist by Department
	function byDepartment()
	{
		$sy = $this->session->userdata('sy');
		$department = $this->input->get('department');
		$result['data'] = $this->StudentModel->byDepartment($department, $sy);
		$result['data1'] = $this->StudentModel->bySectionCount($department, $sy);
		//$result['data2']=$this->StudentModel->byYearLevelCount($department, $sy);
		$this->load->view('masterlist_by_department', $result);

		if ($this->input->post('submit')) {
			$sy = $this->session->userdata('sy');
			$department = $this->input->get('department');
			$result['data'] = $this->StudentModel->byDepartment($department, $sy);
			$this->load->view('masterlist_by_department', $result);
		}
	}

	//Masterlist by Department
	function byDepartmentSHS()
	{
		$sy = $this->session->userdata('sy');
		$department = $this->input->get('department');
		$result['data'] = $this->StudentModel->byDepartmentSHS($department, $sy);
		$result['data1'] = $this->StudentModel->bySectionCount($department, $sy);
		//$result['data2']=$this->StudentModel->byYearLevelCount($department, $sy);
		$this->load->view('masterlist_by_department', $result);

		if ($this->input->post('submit')) {
			$sy = $this->session->userdata('sy');
			$department = $this->input->get('department');
			$result['data'] = $this->StudentModel->byDepartmentSHS($department, $sy);
			$this->load->view('masterlist_by_department', $result);
		}
	}

	//Masterlist by Department
	function byDepartmentSHS2()
	{
		$sy = $this->session->userdata('sy');
		$department = $this->input->get('department');
		$result['data'] = $this->StudentModel->byDepartmentSHS2($department, $sy);
		$result['data1'] = $this->StudentModel->bySectionCount($department, $sy);
		//$result['data2']=$this->StudentModel->byYearLevelCount($department, $sy);
		$this->load->view('masterlist_by_department', $result);

		if ($this->input->post('submit')) {
			$sy = $this->session->userdata('sy');
			$department = $this->input->get('department');
			$result['data'] = $this->StudentModel->byDepartmentSHS2($department, $sy);
			$this->load->view('masterlist_by_department', $result);
		}
	}

	//Masterlist by Enrolled Online
	function byEnrolledOnline()
	{
		$sy = $this->session->userdata('sy');
		$department = $this->input->get('department');
		$result['data'] = $this->StudentModel->byEnrolledOnline($department, $sy);
		$this->load->view('masterlist_by_oe', $result);

		if ($this->input->post('submit')) {
			$sy = $this->session->userdata('sy');
			$department = $this->input->get('department');
			$result['data'] = $this->StudentModel->byEnrolledOnline($department, $sy);
			$this->load->view('masterlist_by_oe', $result);
		}
	}

	//Masterlist for Payment Acceptance
	function forPaymentAcceptance()
	{
		$result['data'] = $this->StudentModel->byEnrolledOnline();
		$this->load->view('masterlist_by_op_verification', $result);

		if ($this->input->post('submit')) {
			$result['data'] = $this->StudentModel->byEnrolledOnline();
			$this->load->view('masterlist_by_op_verification', $result);
		}
	}

	//Masterlist by Religion
	function studeReligion()
	{
		$sy = $this->session->userdata('sy');
		$sem = $this->session->userdata('semester');
		$religion = $this->input->get('religion');
		$result['data'] = $this->StudentModel->religionList($sy, $religion);
		$this->load->view('masterlist_by_religion', $result);
	}
	//Masterlist by City
	function cityList()
	{
		$sy = $this->session->userdata('sy');
		$sem = $this->session->userdata('semester');
		$city = $this->input->get('city');
		$result['data'] = $this->StudentModel->cityList($sy, $city);
		$this->load->view('masterlist_by_city', $result);
	}
	//Masterlist by Ethnicity
	function ethnicityList()
	{
		$sy = $this->session->userdata('sy');
		$sem = $this->session->userdata('semester');
		$ethnicity = $this->input->get('ethnicity');
		$result['data'] = $this->StudentModel->ethnicityList($sy, $ethnicity);
		$this->load->view('masterlist_by_ethnicity', $result);
	}
	//Masterlist of Teachers
	function teachers()
	{
		$result['data'] = $this->StudentModel->teachers();
		$this->load->view('masterlist_teachers', $result);
	}

	//Masterlist by Enrolled Online
	function byEnrolledOnlineSem()
	{
		$sy = $this->session->userdata('sy');
		$sem = $this->session->userdata('semester');
		$result['data'] = $this->StudentModel->byEnrolledOnlineSem($sy);
		$this->load->view('masterlist_by_oe', $result);
	}
	function byEnrolledOnlineAll()
	{
		$result['data'] = $this->StudentModel->byEnrolledOnlineAll();
		$this->load->view('masterlist_by_oe_all', $result);
	}

	function studeGrades()
	{
		$studeno = $this->input->get('studeno');
		$sy = $this->session->userdata('sy');
		$sem = $this->session->userdata('semester');
		// $result['data']=$this->StudentModel->studeGrades($studeno, $sem, $sy);
		$result['data'] = $this->StudentModel->instructor();
		$this->load->view('stude_grades', $result);
	}

	function studeGradesView()
	{
		if ($this->session->userdata('level') === 'Student') {
			$studeno = $this->session->userdata('username');
			$sy = $this->session->userdata('sy');
			$sem = $this->session->userdata('semester');
			$result['data'] = $this->StudentModel->studeGrades($studeno, $sy);
			$result['data1'] = $this->SettingsModel->getSchoolInfo();
			$this->load->view('stude_grades_view', $result);
		} else {
			$studeno = $this->input->get('studeno');
			$sy = $this->session->userdata('sy');
			$sem = $this->session->userdata('semester');
			$result['data'] = $this->StudentModel->studeGrades($studeno, $sem, $sy);
			$result['data1'] = $this->SettingsModel->getSchoolInfo();
			$this->load->view('stude_grades_view', $result);
		}
	}

	function COR()
	{
		if ($this->session->userdata('level') === 'Student') {
			$studeno = $this->session->userdata('username');
			$sy = $this->session->userdata('sy');
			$sem = $this->session->userdata('semester');
			$result['data'] = $this->StudentModel->studeCOR($studeno, $sy);
			$this->load->view('stude_cor', $result);
		} else {
			$studeno = $this->input->get('studeno');
			$sy = $this->session->userdata('sy');
			$sem = $this->session->userdata('semester');
			$result['data'] = $this->StudentModel->studeCOR($studeno, $sem, $sy);
			$this->load->view('stude_cor', $result);
		}
	}

	public function slotsMonitoring()
	{
		$sy = $this->session->userdata('sy');
		$sem = $this->session->userdata('semester');
		$result['data'] = $this->StudentModel->slotsMonitoring($sy);
		$this->load->view('registrar_slots_monitoring', $result);
	}

	public function subjectMasterlist()
	{
		$subjectcode = $this->input->get('subjectcode');
		$section = $this->input->get('section');
		$sy = $this->session->userdata('sy');
		// $sem = $this->session->userdata('semester');
		$result['data'] = $this->StudentModel->subjectMasterlist($sy, $subjectcode, $section);
		$this->load->view('registrar_subject_masterlist', $result);
	}

	public function crossEnrollees()
	{
		$sy = $this->session->userdata('sy');
		$sem = $this->session->userdata('semester');
		$result['data'] = $this->StudentModel->crossEnrollees($sem, $sy);
		$this->load->view('registrar_cross_enrollees', $result);
	}

	public function fteRecords()
	{
		$course = $this->input->get('course');
		$yearlevel = $this->input->get('yearlevel');
		$sy = $this->session->userdata('sy');
		$sem = $this->session->userdata('semester');
		$result['course'] = $this->StudentModel->getCourse();
		$result['data'] = $this->StudentModel->fteRecords($sem, $sy, $course, $yearlevel);
		$this->load->view('registrar_fte_records', $result);
	}

	public function subregistration()
	{
		$sy = $this->session->userdata('sy');
		$sem = $this->session->userdata('semester');
		$result['data'] = $this->StudentModel->slotsMonitoring($sy);
		$this->load->view('registrar_subjects', $result);
	}

	public function grades()
	{
		$sy = $this->session->userdata('sy');
		$sem = $this->session->userdata('semester');
		$result['data'] = $this->StudentModel->grades($sy);
		$this->load->view('registrar_grades', $result);
	}

	public function gradeSheets()
	{
		$SubjectCode = $this->input->get('SubjectCode');
		$Description = $this->input->get('Description');
		//$Instructor = $this->input->get('Instructor'); 
		$Section = $this->input->get('Section');
		$sy = $this->session->userdata('sy');
		$sem = $this->session->userdata('semester');
		$result['data'] = $this->StudentModel->gradeSheets($sy, $SubjectCode, $Description, $Section);
		$this->load->view('registrar_grades_sheets', $result);
	}


	public function grades_sheets_pv()
	{
		$result['data'] = "Grading Sheets";
		$result['grade'] = $this->Common->one_cond_row('grades', 'gradeID', $this->uri->segment(3));
		$result['so'] = $this->Common->one_cond_row('srms_settings_o', 'settingsID', 1);
		$this->load->view('registrar_grading_sheet', $result);
	}

	public function grades_sheets_all()
	{
		$result['data'] = "Grading Sheets";
		$result['grade'] = $this->Common->one_cond_row('grades', 'gradeID', $this->uri->segment(3));
		$result['so'] = $this->Common->one_cond_row('srms_settings_o', 'settingsID', 1);
		$this->load->view('registrar_grading_sheet_all', $result);
	}

			
	public function consol(){
		$gl = $this->input->post('grade_level');
		$grading = $this->input->post('grading');
		$section = $this->input->post('section');

		$result['gl'] = $this->input->post('grade_level');
		$result['sec'] = $this->input->post('section');


		$result['data'] = "Grading Sheets";
		$result['sub'] = $this->Common->two_cond_gb('grades', 'YearLevel', $gl,'SY',$this->session->sy,'SubjectCode','SubjectCode','asc');
		$result['grade'] = $this->Common->cons($gl,$section);
		$result['so'] = $this->Common->one_cond_row('srms_settings_o', 'settingsID', 1);
		$result['section'] = $this->Common->one_cond('sections', 'SY', $this->session->sy);
		$result['grade_level'] = $this->Common->one_cond_gb('sections', 'SY', $this->session->sy,'YearLevel','Section','DESC');
		$this->load->view('consol_report', $result);
	}
}  
