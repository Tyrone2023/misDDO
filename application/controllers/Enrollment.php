<?php
class Enrollment extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->helper('url');
		$this->load->model('PersonnelModel');
		$this->load->helper('url');
		$this->load->helper('url', 'form');
		$this->load->library('form_validation');
		$this->load->model('StudentModel');
		$this->load->model('SettingsModel');

		if ($this->session->userdata('logged_in') !== TRUE) {
			redirect('login');
		}
	}

	//Profile List
	function profileList()
	{
		if($this->session->position == "School"){
			$result['data'] = $this->StudentModel->getProfile();
		}else{
			$result['data'] = $this->StudentModel->getProfile_by_adviser();
		}

		$data['provinces'] = $this->SettingsModel->get_provinces();
		$this->load->view('profile_list', $result);
	}

	function profileEntry()
	{
		$data['ethnicity'] = $this->SettingsModel->get_ethnicity();
		$data['religion'] = $this->SettingsModel->get_religion();
		$data['prevschool'] = $this->SettingsModel->get_prevschool();
		$data['staff'] = $this->Common->one_cond_row_select('hris_staff','schoolID,IDNumber','IDNumber',$this->session->username);

		$this->load->view('profile_form', $data);  // Pass the data to the view


		if ($this->input->post('submit')) {
			// Get and format data from the form
			$data = [
				'StudentNumber' => trim($this->input->post('StudentNumber')),
				'LRN' => trim($this->input->post('LRN')),
				'FirstName' => strtoupper($this->input->post('FirstName')),
				'MiddleName' => strtoupper($this->input->post('MiddleName')),
				'LastName' => strtoupper($this->input->post('LastName')),
				'nameExt' => $this->input->post('nameExt'),
				'Sex' => $this->input->post('Sex'),
				'CivilStatus' => $this->input->post('CivilStatus'),
				'Citizenship' => $this->input->post('Citizenship'),
				'BloodType' => $this->input->post('BloodType'),
				'Religion' => $this->input->post('Religion'),
				'BirthPlace' => $this->input->post('BirthPlace'),
				'BirthDate' => $this->input->post('BirthDate'),
				'Age' => $this->input->post('Age'),
				'Ethnicity' => $this->input->post('Ethnicity'),
				'Elementary' => $this->input->post('Elementary'),

				'TelNumber' => $this->input->post('TelNumber'),
				'MobileNumber' => $this->input->post('MobileNumber'),
				'EmailAddress' => $this->input->post('EmailAddress'),
				'Province' => $this->input->post('Province'),
				'City' => $this->input->post('City'),
				'Brgy' => $this->input->post('Brgy'),
				'Sitio' => $this->input->post('Sitio'),
				'Guardian' => $this->input->post('Guardian'),
				'GuardianContact' => $this->input->post('GuardianContact'),
				'GuardianAddress' => $this->input->post('GuardianAddress'),
				'GuardianRelationship' => $this->input->post('GuardianRelationship'),
				'GuardianTelNo' => $this->input->post('GuardianTelNo'),
				'guardianOccupation' => $this->input->post('guardianOccupation'),
				'Father' => $this->input->post('Father'),
				'FOccupation' => $this->input->post('FOccupation'),

				'fContactNo' => $this->input->post('fContactNo'),
				'mContactNo' => $this->input->post('mContactNo'),

				'Mother' => $this->input->post('Mother'),
				'MOccupation' => $this->input->post('MOccupation'),

				'Notes' => $this->input->post('Notes'),
				'schoolID' => $this->input->post('schoolID'),

			];

			$completeName = $data['FirstName'] . ' ' . $data['LastName'];
			$Encoder = $this->input->post('encode');
			// $AdmissionDate = date("Y-m-d");
			// $GraduationDate = date("Y-m-d");
			$Password = sha1($data['BirthDate']);
			$now = date('H:i:s A');

			// Check if record exists
			$exists = $this->db->where('StudentNumber', $data['StudentNumber'])->count_all_results('studeprofile');
			if ($exists) {
				$this->session->set_flashdata('msg', '<div class="alert alert-danger text-center"><b>Student Number is in use.</b></div>');
				redirect('Enrollment/profileList');
			} else {
				// Save profile
				$this->db->insert('studeprofile', array_merge($data, [
					'Encoder' => $Encoder,
					// 'AdmissionDate' => $AdmissionDate,
					// 'GraduationDate' => $GraduationDate,
					'schoolID' => $this->input->post('schoolID'),
					'EmailAddress' => $data['EmailAddress'],
				]));

				$fullName = $data['FirstName'] . ' ' . $data['MiddleName'] . ' ' . $data['LastName'];

				// Log to the activity trail with the student's full name
				$this->db->insert('atrail', [
					'atDesc' => 'Created profile and user account for ' . $fullName,  // Include student's name in atDesc
					'atDate' => date("Y-m-d"),
					'atTime' => $now,
					'atRes' => $Encoder,
					'atSNo' => $data['StudentNumber'],
				]);

				$this->session->set_flashdata('msg', '<div class="alert alert-success text-center"><b>Profile has been saved successfully.</b></div>');

				// Email Notification
				$this->load->config('email');
				$this->load->library('email');

				$mail_message = 'Dear ' . $data['FirstName'] . ',<br><br>Your profile is now encoded to SRMS. Please take note of the following:<br>';
				$mail_message .= 'Username: <b>' . $data['StudentNumber'] . '</b><br>Password: <b>' . $data['BirthDate'] . '</b><br><br>Thanks & Regards,<br>SRMS - Online';

				$this->email->from('no-reply@srmsportal.com', 'SRMS Online Team')
					->to($data['EmailAddress'])
					->subject('Account Created')
					->message($mail_message)
					->send();

				redirect('Enrollment/profileList');
			}
		}
	}

	function studentsprofile()
	{
		$id = $this->input->get('id');
		$result['data'] = $this->StudentModel->displayrecordsById($id);
		
		// Load the EnrollmentModel if not already loaded
		$this->load->model('StudentModel');
		// Check if the student is already enrolled
		$result['isEnrolled'] = $this->StudentModel->isEnrolled($id);
		
		$this->load->view('profile_page', $result);
	}
	

	public function deleteProfile()
	{
		$id = $this->input->get('id');
		$username = $this->session->userdata('username');

		date_default_timezone_set('Asia/Manila');
		$now = date('H:i:s A');
		$date = date("Y-m-d");

		$this->db->where('StudentNumber', $id)->delete('studeprofile');
		$data = [
			'atDesc' => "Deleted profile of student: $id ",
			'atDate' => $date,
			'atTime' => $now,
			'atRes' => $username,
			'atSNo' => $id
		];
		$this->db->insert('atrail', $data);
		$this->session->set_flashdata('success', 'Student profile has been successfully deleted.');
		redirect('Enrollment/profileList');
	}
	

	public function get_provinces()
	{
		$provinces = $this->StudentModel->get_provinces();
		echo json_encode($provinces);
	}

	public function get_cities()
	{
		$province = $this->input->post('province');
		$cities = $this->StudentModel->get_cities($province);
		echo json_encode($cities);
	}

	public function get_barangays()
	{
		$city = $this->input->post('city');
		$barangays = $this->StudentModel->get_barangays($city);
		echo json_encode($barangays);
	}


	
	function updateStudeProfile()
	{
		
		
		$StudentNumber = $this->input->get('StudentNumber');
		$result['data'] = $this->StudentModel->displayrecordsById($StudentNumber);
		$result['ethnicity'] = $this->SettingsModel->get_ethnicity();
		$result['religion'] = $this->SettingsModel->get_religion();
		$result['prevschool'] = $this->SettingsModel->get_prevschool();

		// Debug the data
		log_message('debug', print_r($result, true));

		$this->load->view('profile_form_update', $result);


		if ($this->input->post('submit')) {
			// Retrieve form data
			$data = [

				'StudentNumber'       => $this->input->post('StudentNumber'),
				'FirstName'          => $this->input->post('FirstName'),
				'MiddleName'         => $this->input->post('MiddleName'),
				'LastName'           => $this->input->post('LastName'),
				'nameExt'            => $this->input->post('nameExt'),
				'Sex'                => $this->input->post('Sex'),
				'CivilStatus'        => $this->input->post('CivilStatus'),
				'Religion'           => $this->input->post('Religion'),
				'Ethnicity'          => $this->input->post('Ethnicity'),
				'Elementary'          => $this->input->post('Elementary'),
				'MobileNumber'       => $this->input->post('MobileNumber'),
				'BirthDate'          => $this->input->post('BirthDate'),
				'BirthPlace'         => $this->input->post('BirthPlace'),
				'Age'                => $this->input->post('Age'),
				// Parents
				'Father'             => $this->input->post('Father'),
				'FOccupation'        => $this->input->post('FOccupation'),
				'Mother'             => $this->input->post('Mother'),
				'MOccupation'        => $this->input->post('MOccupation'),
				'ParentsMonthly'        => $this->input->post('ParentsMonthly'),
				// Guardian Info
				'Guardian'           => $this->input->post('Guardian'),
				'GuardianContact'    => $this->input->post('GuardianContact'),
				'GuardianRelationship' => $this->input->post('GuardianRelationship'),
				'GuardianAddress'    => $this->input->post('GuardianAddress'),
				// Address
				'Sitio'              => $this->input->post('Sitio'),
				'Brgy'               => $this->input->post('Brgy'),
				'City'               => $this->input->post('City'),
				'Province'           => $this->input->post('Province'),
				'EmailAddress'       => $this->input->post('EmailAddress'),
			];

			$Encoder = $this->session->userdata('username');
			$updatedDate = date("Y-m-d");
			$updatedTime = date("h:i:s A");

			// Update profile
			$this->db->where('StudentNumber', $this->input->post('OldStudentNumber'));
			$this->db->update('studeprofile', array_merge($data, ['Encoder' => $Encoder]));

			// Update users
			//  $this->db->where('username', $this->input->post('OldStudentNumber'));
			//  $this->db->update('o_users', array_merge($data, ['Encoder' => $Encoder]));

			// Insert audit trail
			$this->db->insert('atrail', [
				'atDesc'        => 'Updated Profile',
				'atDate'   => $updatedDate,
				'atTime'   => $updatedTime,
				'atRes'       => $Encoder,
				'atSNo' => $data['StudentNumber']
			]);

			$this->session->set_flashdata('msg', '<div class="alert alert-success text-center"><b>Updated successfully.</b></div>');
			redirect('Enrollment/studentsprofile?id=' . $this->input->post('StudentNumber'));
		}
	}

	function enrollmentAcceptance()
{
    $courseVal = $this->input->post('course');
    $yearlevelVal = $this->input->post('yearlevel');
    
    $result['course'] = $this->StudentModel->getCourse();
    $result['track'] = $this->SettingsModel->getTrack();
    $result['strand'] = $this->SettingsModel->getStrand1();
    $result['courseVal'] = $courseVal;
    $result['yearlevelVal'] = $yearlevelVal;
    
    $this->load->view('enrollment_form_final', $result);

    if ($this->input->post('submit')) {
        // Get data from the form
        $data_semesterstude = [
            'StudentNumber' => $this->input->post('StudentNumber'),
            'schoolID' => $this->input->post('schoolID'),
            'Course' => $this->input->post('Course'),
            'YearLevel' => $this->input->post('YearLevel'),
            'Status' => $this->input->post('Status'),
            'StudeStatus' => $this->input->post('StudeStatus'),
            'Semester' => $this->input->post('Semester'),
            'SY' => $this->input->post('SY'),
            'Section' => $this->input->post('Section'),
            'EnrolledDate' => date("Y-m-d"),
            'Track' => $this->input->post('Track'),
            'Qualification' => $this->input->post('Qualification'),
            'BalikAral' => $this->input->post('BalikAral'),
            'IP' => $this->input->post('IP'),
            'FourPs' => $this->input->post('FourPs'),
            'Repeater' => $this->input->post('Repeater'),
            'Transferee' => $this->input->post('Transferee')
        ];

        // Insert into semesterstude table
        $this->db->insert('semesterstude', $data_semesterstude);
        
        $this->session->set_flashdata('msg', '<div class="alert alert-success text-center"><b>Enrollment details have been added successfully.</b></div>');
        redirect('Masterlist/enrolledList');
    }
}


function ethnicity()
	{
		$result['data'] = $this->SettingsModel->get_ethnicity();
		$this->load->view('settings_ethnicity', $result);
	}




	public function Addethnicity()
	{
		if ($this->input->post('save')) {
			$data = array(
				'ethnicity' => $this->input->post('ethnicity')
			);
			$this->load->model('SettingsModel');
			$this->SettingsModel->insertethnicity($data);


			redirect('Settings/ethnicity');
		}
		$this->load->view('settings_Addethnicity');
	}


	public function updateethnicity()
	{
		$id = $this->input->get('id');
		$result['data'] = $this->SettingsModel->getethnicitybyId($id);
		$this->load->view('updateethnicity', $result);

		if ($this->input->post('update')) {

			$ethnicity = $this->input->post('ethnicity');

			$this->SettingsModel->updateethnicity($id, $ethnicity);
			$this->session->set_flashdata('author', 'Record updated successfully');
			redirect('Settings/ethnicity');
		}
	}


	public function Deleteethnicity()
	{
		$id = $this->input->get('id');
		if ($id) {
			$this->SettingsModel->Delete_ethnicity($id);
			$this->session->set_flashdata('ethnicity', 'Record deleted successfully');
		} else {
			$this->session->set_flashdata('ethnicity', 'Error deleting record');
		}

		redirect('Settings/ethnicity');
	}






}
