<?php
class Page extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->helper('url');
		$this->load->model('PersonnelModel');
		$this->load->model('Page_model');
		$this->load->model('SGODModel');
		$this->load->model('SettingsModel');
		$this->load->model('StudentModel');

		$this->load->helper('url', 'form');
		$this->load->library('form_validation');
		$this->load->library('session');
	}

	function districts()
	{
		$result['data'] = $this->Page_model->districts();
		$this->load->view('districts', $result);
	}


	public function smea()
	{
		$currentYear = date('Y');
		$schoolID = $this->session->userdata('username');
		$quarter = $this->input->get('quarter');
		$data['quarter'] = $quarter;

		$query = $this->db->get_where('smea', [
			'schoolID' => $schoolID,
			'sQtr' => $quarter,
			'sYear' => $currentYear,
		]);
		$existingRecord = $query->row();


		if ($existingRecord) {

			$data = [
				'sYear' => $existingRecord->sYear,
				'sQtr' => $existingRecord->sQtr,
				'sPhyTargets' => $existingRecord->sPhyTargets,
				'sAchieved' => $existingRecord->sAchieved,
				'sAccomplishmentsPercentage' => $existingRecord->sAccomplishmentsPercentage,
				'sGain' => $existingRecord->sGain,
				'sGainPercentage' => $existingRecord->sGainPercentage,
				'sGapBalance' => $existingRecord->sGapBalance,
				'sGapPercentage' => $existingRecord->sGapPercentage,
				'sFundAllocation' => $existingRecord->sFundAllocation,
				'sFundsUtilized' => $existingRecord->sFundsUtilized,
				'sUtilizationPercentage' => $existingRecord->sUtilizationPercentage,
				'sGapAmount' => $existingRecord->sGapAmount,
				'sGapPercentageUtilize' => $existingRecord->sGapPercentageUtilize,

				'fundsAllocatedQtr' => $existingRecord->fundsAllocatedQtr,
				'fundsUtilized' => $existingRecord->fundsUtilized,

				'fundsUtilizedPercentage' => $existingRecord->fundsUtilizedPercentage,
				'gapUtilizedAmount' => $existingRecord->gapUtilizedAmount,

				'gapUtilizedPercentage' => $existingRecord->gapUtilizedPercentage,
				'valueAdded' => $existingRecord->valueAdded,

				'reasons' => $existingRecord->reasons,
			];
		}


		if (!$existingRecord && $quarter) {
			$data['quarter'] = $quarter;
		}

		if ($this->input->post('submit')) {
			$dataToSave = [
				'schoolID' => $schoolID,
				'sYear' => $this->input->post('sYear', true),
				'sQtr' => $this->input->post('sQtr', true),
				'sPhyTargets' => $this->input->post('sPhyTargets', true),
				'sAchieved' => $this->input->post('sAchieved', true),
				'sAccomplishmentsPercentage' => $this->input->post('sAccomplishmentsPercentage', true),
				'sGain' => $this->input->post('sGain', true),
				'sGainPercentage' => $this->input->post('sGainPercentage', true),
				'sGapBalance' => $this->input->post('sGapBalance', true),
				'sGapPercentage' => $this->input->post('sGapPercentage', true),
				'sFundAllocation' => $this->input->post('sFundAllocation', true),
				'sFundsUtilized' => $this->input->post('sFundsUtilized', true),
				'sUtilizationPercentage' => $this->input->post('sUtilizationPercentage', true),
				'sGapAmount' => $this->input->post('sGapAmount', true),
				'sGapPercentageUtilize' => $this->input->post('sGapPercentageUtilize', true),

				'fundsAllocatedQtr' => $this->input->post('fundsAllocatedQtr', true),
				'fundsUtilized' => $this->input->post('fundsUtilized', true),

				'fundsUtilizedPercentage' => $this->input->post('fundsUtilizedPercentage', true),
				'gapUtilizedAmount' => $this->input->post('gapUtilizedAmount', true),

				'gapUtilizedPercentage' => $this->input->post('gapUtilizedPercentage', true),
				'valueAdded' => $this->input->post('valueAdded', true),

				'reasons' => $this->input->post('reasons', true),
			];

			// If record exists, update it
			if ($existingRecord) {
				$this->db->where(['schoolID' => $schoolID, 'sQtr' => $quarter]);
				$this->db->update('smea', $dataToSave);
				$this->session->set_flashdata('success', 'Record updated successfully!');
			} else {
				// Insert data into the database if no record exists
				if ($this->db->insert('smea', $dataToSave)) {
					$this->session->set_flashdata('success', 'Record saved successfully!');
				} else {
					$this->session->set_flashdata('error', 'Failed to save the record. Please try again.');
				}
			}

			// Redirect to the same page or another page
			redirect('Page/smea');
		}

		// Load the view and pass flash data to display messages in the view
		$this->load->view('smea', $data);
	}

	public function view_sip_district()
	{
		$dist = $this->Common->one_cond_row('district', 'id', $this->session->c_id);

		$result['data'] = $this->Common->one_cond('schools', 'district', $dist->discription);
		$this->load->view('ip_sip_district', $result);
	}

	public function view_sip_district_school()
	{
		$result['school']  = $this->Common->one_cond_row('schools', 'schoolID', $this->uri->segment(3));

		$result['data'] = $this->Common->one_cond('school_imp_plans', 'schoolID', $this->uri->segment(3));
		$this->load->view('ip_sip_school', $result);
	}

	public function view_plans_district()
	{
		$dist = $this->Common->one_cond_row('district', 'id', $this->session->c_id);

		$result['data'] = $this->Common->one_cond('schools', 'district', $dist->discription);
		$this->load->view('plans_district', $result);
	}

	public function view_plans_district_school()
	{
		$result['school']  = $this->Common->one_cond_row('schools', 'schoolID', $this->uri->segment(3));

		$result['data'] = $this->Common->one_cond_group('sgod_aip_submit', 'school_id', $this->uri->segment(3), 'school_id, fy');
		$this->load->view('plans_district_school', $result);
	}


	public function view_sip()
	{
		$schoolID = $this->session->userdata('username');
		$result['data'] = $this->PersonnelModel->view_sip($schoolID);
		$this->load->view('ip_sip', $result);
	}

	public function view_sip_admin()
	{
		$school_id = $this->uri->segment(3);
		$result['data'] = $this->SGODModel->one_cond('school_imp_plans', 'schoolID', $school_id);
		$this->load->view('ip_sip', $result);
	}

	public function school_allocations()
	{
		if ($this->session->position == "School") {
			redirect(base_url());
		}
		$result['data'] = $this->PersonnelModel->school_allocations();
		$result['bs'] = $this->SGODModel->no_cond('sgod_settings_bs');
		$result['school'] = $this->Common->no_cond_order_by('schools', 'schoolName', 'ASC');
		$result['district'] = $this->Common->no_cond_order_by('district', 'discription', 'ASC');
		$result['last'] = $this->SGODModel->get_last_record('sgod_school_allocation');
		$this->load->view('school_allocation', $result);
	}

	public function school_allocations2()
	{
		$schoolID = $this->session->userdata('username');
		$result['st'] = $this->SGODModel->one_cond_row('schools', 'schoolID', $this->session->username);
		$result['bs'] = $this->SGODModel->no_cond('sgod_settings_bs');
		$result['last'] = $this->SGODModel->get_last_record('sgod_school_allocation');
		$result['data'] = $this->PersonnelModel->school_allocations2($schoolID);
		$this->load->view('school_allocation2', $result);
	}

	public function school_allocation_edit()
	{
		$result['st'] = $this->SGODModel->one_cond_row('sgod_school_allocation', 'id', $this->uri->segment(3));
		$result['last'] = $this->SGODModel->get_last_record('sgod_school_allocation');
		$result['bs'] = $this->SGODModel->no_cond('sgod_settings_bs');
		$this->load->view('school_allocation_update', $result);
	}

	function systemFeedback()
	{
		$this->load->view('system_feedback');
		if ($this->input->post('submit')) {
			$system_user = $this->session->userdata('username');
			$position = $this->session->userdata('position');
			$q1Ans = $this->input->post('q1Ans');
			$q2Ans = $this->input->post('q2Ans');
			$q3Ans = $this->input->post('q3Ans');
			$q4Ans = $this->input->post('q4Ans');
			$q5Ans = $this->input->post('q5Ans');

			$que = $this->db->query("insert into system_feedback (system_user, position, q1Ans, q2Ans, q3Ans, q4Ans, q5Ans) values('$system_user','$position','$q1Ans','$q2Ans','$q3Ans','$q4Ans','$q5Ans')");
			$this->session->set_flashdata('success', 'Thank you for your feedback.');
			redirect('Page/systemFeedback');
			// redirect(base_url().'leaveApplications/'.$IDNumber);
		}
	}

	function systemFeedbackResults()
	{
		$result['data'] = $this->PersonnelModel->systemFeedbackResults();
		$result['data2'] = $this->PersonnelModel->systemFeedbackResults_Messages();
		$this->load->view('system_feedback_results', $result);
	}


	function schoolInfo()
	{

		$result['district'] = $this->Common->no_cond('district');

		if ($this->session->userdata('position') === 'School') {

			$schoolID = $this->session->userdata('username');
			$result['data'] = $this->PersonnelModel->schoolProfile($schoolID);

			$this->load->view('school_info', $result);
		} else {
			$schoolID = $this->input->get('schoolid');
			$result['data'] = $this->PersonnelModel->schoolProfile($schoolID);
			$this->load->view('school_info', $result);
		}

		if ($this->input->post('submit')) {
			// if ($this->session->userdata('position') === 'School') {
			// $schoolID = $this->session->userdata('username');
			// } else {
			// 	$schoolID = $this->input->get('schoolid');
			// }
			$oldSchoolID = $this->input->post('oldSchoolID');
			$schoolID = $this->input->post('schoolID');
			$schoolName = $this->input->post('schoolName');
			$district = $this->input->post('district');
			$brgy = $this->input->post('brgy');
			$city = $this->input->post('city');
			$province = $this->input->post('province');
			$adminFName = $this->input->post('adminFName');
			$adminMName = $this->input->post('adminMName');
			$adminLName = $this->input->post('adminLName');
			$adminDesignation = $this->input->post('adminDesignation');
			$schoolEmail = $this->input->post('schoolEmail');
			$adminEmail = $this->input->post('adminEmail');
			$adminMobile = $this->input->post('adminMobile');
			$st = $this->input->post('st');
			$electricity = $this->input->post('electricity');
			$internet = $this->input->post('internet');
			$mb = $this->input->post('mb');
			$provider = $this->input->post('provider');
			$coor = $this->input->post('coor');

			$que = $this->db->query("update schools set schoolID='$schoolID',schoolName='$schoolName',district='$district',brgy='$brgy',city='$city',province='$province',course='$st',adminFName='$adminFName',adminMName='$adminMName',adminLName='$adminLName',adminLName='$adminLName',adminLName='$adminLName',adminDesignation='$adminDesignation',schoolEmail='$schoolEmail',adminMobile='$adminMobile',adminEmail='$adminEmail',electricity='$electricity',internet='$internet',mb='$mb',provider='$provider',coor='$coor' where schoolID='" . $oldSchoolID . "'");
			$que = $this->db->query("update users set username='$schoolID',user_id='$schoolID',fname='$schoolName',mname='',lname='' where username='" . $oldSchoolID . "'");
			$this->session->set_flashdata('success', 'Updated successfully.');
			redirect('Page/schoolProfile?schoolid=' . $schoolID);
		}
	}

	function school_allocation_update()
	{

		if ($this->input->post('submit')) {
			$this->SGODModel->insert_school();
			$this->session->set_flashdata('success', 'Add successfully.');
			redirect(base_url() . 'Page/school_new');
		}
		$id = $this->uri->segment(3);
		$result['data'] = $this->SGODModel->one_cond_row('sgod_school_allocation', 'id', $id);

		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('school_allocation_edit', $result);
		$this->load->view('templates/footerv2');
	}

	function school_new()
	{
		$result['district'] = $this->Common->no_cond_select('district', 'id,discription');

		if ($this->input->post('submit')) {
			$this->SGODModel->insert_school();
			$this->session->set_flashdata('success', 'Added successfully.');
			redirect(base_url() . 'Page/school_new');
		}

		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('school_add', $result);
		$this->load->view('templates/footerv2');
	}



	function fund_update()
	{
		$this->SGODModel->update_fund_allocation();
		$this->session->set_flashdata('success', 'Updated successfully.');
		if ($this->session->position == 'School') {
			redirect(base_url() . 'Page/school_allocations2');
		} else {
			redirect(base_url() . 'Page/school_allocations');
		}
	}

	function fund_add()
	{
		$this->SGODModel->insert_fund_allocation();
		$this->session->set_flashdata('success', 'Added successfully.');
		if ($this->session->position == 'School') {
			redirect(base_url() . 'Page/school_allocations2');
		} else {
			redirect(base_url() . 'Page/school_allocations');
		}
	}

	function systemHelp()
	{
		$this->load->view('system_help');
	}




	function sr_request()
	{
		$IDNumber = $this->session->userdata('username');
		$result['data'] = $this->PersonnelModel->sr_request_list_emp($IDNumber);
		$result['purpose'] = $this->PersonnelModel->getPurpose();
		$this->load->view('service_record_request', $result);
		if ($this->input->post('submit')) {
			$system_user = $this->session->userdata('username');
			$purpose = $this->input->post('purpose');
			$message = $this->input->post('message');

			date_default_timezone_set('Asia/Manila'); # add your city to set local time zone
			$dateReq = date("Y-m-d");

			$que = $this->db->query("insert into doc_request (IDNumber, req_doc, dateReq, purpose, reqStat, message) values('$system_user','Service Record','$dateReq','$purpose', 'Pending','$message')");
			$this->session->set_flashdata('success', 'Thank you! Your request has been submitted.');
			redirect('Page/sr_request');
		}
	}



	function sr_request_list()
	{
		$result['data'] = $this->PersonnelModel->sr_request_list();
		$this->load->view('service_record_request_list', $result);
	}





	public function update_purpose($id)
	{
		// Get data from the request
		$forPrint = $this->input->post('forPrint');
		$username = $this->session->userdata('username'); // Get logged-in username

		// Fetch the user's full name from the database
		$user = $this->db->get_where('users', ['username' => $username])->row();

		// Combine fname, mname, and lname into full name
		$fullName = $user->fname . ' ' . (!empty($user->mname) ? $user->mname . ' ' : '') . $user->lname;

		// Get current date and time in Manila timezone
		$actionDate = (new DateTime('now', new DateTimeZone('Asia/Manila')))->format('F j, Y \a\t g:iA');

		// Update the request status and other details
		$this->db->set('reqStat', 'Processed');
		$this->db->set('forPrint', $forPrint);
		$this->db->set('action_date', $actionDate);
		$this->db->set('action_user', $fullName);

		$this->db->where('id', $id);
		$this->db->update('doc_request');

		// Check if the update was successful
		if ($this->db->affected_rows() > 0) {
			if ($forPrint == 1) {
				$qrData = base_url() . "Page/printServiceRecord/{$id}";

				$this->db->set('QRCode', $qrData);
				$this->db->where('id', $id);
				$this->db->update('doc_request');
			}
			echo json_encode(['status' => 'success']);
		} else {
			echo json_encode(['status' => 'error']);
		}
	}





	public function printServiceRecord()
	{
		$position = $this->session->userdata('position');
		$user_id = ($position === 'user')
			? $this->session->userdata('username')
			: $this->input->get('id');

		// Fetch data for the service record
		$result['data'] = $this->PersonnelModel->empServiceRecord($user_id);
		$result['data2'] = $this->PersonnelModel->empServicereq($user_id);
		$result['data1'] = $this->PersonnelModel->mis_settings();

		// Add the QR code data
		if (!empty($result['data'])) {
			$result['qrCodeUrl'] = base_url() . "Page/printServiceRecord/" . $user_id;
		}

		$this->load->view('service_record_print', $result);
	}










	public function disapprove_request($id)
	{
		// Get disapproval reason and logged-in username
		$disapprovalReason = $this->input->post('disapprovalReason');
		$username = $this->session->userdata('username'); // Get logged-in username

		// Fetch the user's full name from the database
		$user = $this->db->get_where('users', ['username' => $username])->row();

		// Combine fname, mname, and lname into full name
		$fullName = $user->fname . ' ' . (!empty($user->mname) ? $user->mname . ' ' : '') . $user->lname;

		// Get current date and time in Manila timezone
		$actionDate = (new DateTime('now', new DateTimeZone('Asia/Manila')))->format('Y-m-d H:i:s');

		// Update the request with disapproval details
		$data = array(
			'reqStat' => 'Disapproved',
			'disapprovalReason' => $disapprovalReason,
			'action_date' => $actionDate,
			'action_user' => $fullName
		);

		$this->db->where('id', $id);
		$this->db->update('doc_request', $data);

		// Set a flash message to indicate success
		$this->session->set_flashdata('success', 'Request has been disapproved.');

		// Redirect back to the service record request list
		redirect('Page/sr_request_list');
	}




	function mandatoryDed()
	{
		if ($this->session->userdata('position') === 'user') {
			$IDNumber = $this->session->userdata('username');
			$result['data'] = $this->PersonnelModel->mandatoryDed($IDNumber);
			$this->load->view('emp_mandatory_ded', $result);
		} else {
			echo "Access Denied";
		}
	}
	function Accountant()
	{
		$schoolID = $this->session->userdata('username');
		$result['data'] = $this->PersonnelModel->count_all_staff($schoolID);
		$result['data1'] = $this->PersonnelModel->count_all_teaching($schoolID);
		$result['data2'] = $this->PersonnelModel->count_all_nonteaching($schoolID);
		$result['data3'] = $this->PersonnelModel->count_all_inactive($schoolID);

		$this->load->view('dashboard_accountant', $result);
	}

	function perPlantillaGroup()
	{
		$result['data'] = $this->PersonnelModel->perPlantillaGroup();
		$this->load->view('by_plantilla_group', $result);
	}


	//Employee List By Department
	function employeelistDepartment()
	{
		$department = $this->input->post('Department');
		// if($this->input->post('submit')){
		$result['data1'] = $this->PersonnelModel->getDepartment();
		$result['data'] = $this->PersonnelModel->employeelistDepartment($department);
		$this->load->view('list_department', $result);
		// }

	}

	function pos_summary()
	{
		$result['data'] = $this->PersonnelModel->personnelCounts();
		$this->load->view('by_position', $result);
	}

	// 	public function serviceRecord()
	// {
	//     $position = $this->session->userdata('position');
	//     $schoolID = $this->session->userdata('username');
	//     $selectedDepartment = $this->input->post('departmentDropdown'); // Get the selected department from the form.

	//     // Initialize the data variable
	//     $data = [];

	//     if (in_array($position, ['Admin', 'Human Resource Admin', 'HR Staff'])) {
	//         // Only fetch the records if a department is selected
	//         if (!empty($selectedDepartment)) {
	//             $data = $this->PersonnelModel->service_Record($selectedDepartment); // Fetch data based on the department
	//         }
	//         $result['showDropdown'] = true; // Show dropdown for Admin and HR roles
	//     } elseif ($position === 'School') {
	//         $data = $this->PersonnelModel->service_Record_school($schoolID); // Fetch data for the logged-in school
	//         $result['showDropdown'] = false; // Hide dropdown for School role
	//     }

	//     // Prepare the view data
	//     $result['data'] = $data;
	//     $result['dept'] = $this->PersonnelModel->getDepartment(); // Fetch departments
	//     $result['data1'] = $this->PersonnelModel->searchPersonnel();

	//     $this->load->view('service_record_admin', $result);
	// }




	public function serviceRecord()
	{
		$position = $this->session->userdata('position');
		$schoolID = $this->session->userdata('username');
		$selectedDepartment = $this->input->post('departmentDropdown'); // Get the selected department.

		$data = [];

		if (in_array($position, ['Admin', 'Human Resource Admin', 'HR Staff'])) {
			if (!empty($selectedDepartment)) {
				$data = $this->PersonnelModel->service_Record($selectedDepartment); // Fetch data for selected department
			}
			$result['showDropdown'] = true;
			$result['data1'] = $this->PersonnelModel->searchPersonnel(); // Admin/HR see all personnel
		} elseif ($position === 'School') {
			$data = $this->PersonnelModel->service_Record_school($schoolID); // Fetch data for specific school
			$result['showDropdown'] = false;
			$result['data1'] = $this->PersonnelModel->searchPersonnelSchool($schoolID); // Fetch only school-specific personnel
		}

		$result['data'] = $data;
		$result['dept'] = $this->PersonnelModel->getDepartment(); // Fetch department list

		$this->load->view('service_record_admin', $result);
	}





	public function filterServiceRecords()
	{
		$department = $this->input->post('department');

		// Fetch filtered data based on the department
		$data['records'] = $this->PersonnelModel->getServiceRecordsByDepartment($department);

		// Load a partial view for the table
		$this->load->view('partials/service_record_table', $data);
	}


	public function addSR()
	{
		$this->Page_model->insert_sr();
		$IDNumber = $this->input->post('IDNumber');
		$this->session->set_flashdata('success', 'One record added successfully!');
		redirect(base_url('Page/serviceRecordview') . '?IDNumber=' . $IDNumber);
	}

	public function addLC()
	{
		$this->Page_model->insert_lc();
		$this->session->set_flashdata('success', 'One record added successfully!');
		redirect(base_url() . 'Page/' . 'leaveCreditsSummary/');
	}


	function serviceRecordview()
	{
		$IDNumber = $this->input->get('IDNumber');
		$result['data'] = $this->PersonnelModel->service_Record_view($IDNumber);
		$this->load->view('service_record_view', $result);
	}






	function serviceRecordEdit()
	{
		$id = $this->input->get('id');
		$result['data'] = $this->PersonnelModel->service_Record_id($id);
		$this->load->view('service_record_edit', $result);
		if ($this->input->post('submit')) {
			$empID = $this->input->post('empID');
			$appointDate = $this->input->post('appointDate');
			$endDate = $this->input->post('endDate');
			$empPosition = $this->input->post('empPosition');
			$empStatus = $this->input->post('empStatus');
			$salary = $this->input->post('salary');
			$empStation = $this->input->post('empStation');
			$separationDate = $this->input->post('separationDate');
			$separation = $this->input->post('separation');
			$remarks = $this->input->post('remarks');

			$que = $this->db->query("update hris_employment set appointDate='$appointDate',endDate='$endDate',empPosition='$empPosition',empStatus='$empStatus',salary='$salary',empStation='$empStation',separationDate='$separationDate',separation='$separation',remarks='$remarks' where empID='$empID'");
			$this->session->set_flashdata('success', 'Updated successfully.');
			redirect('Page/serviceRecord');
			// redirect(base_url().'leaveApplications/'.$IDNumber);
		}
	}

	function delete_serviceRecord()
	{
		$id = $this->input->get('id');
		$this->db->query("delete  from hris_employment where empID='" . $id . "'");
		$this->Page_model->insert_at('Deleted service record', $this->db->insert_id());
		$this->session->set_flashdata('success', 'Deleted successfully!');
		redirect('Page/serviceRecord');
	}

	function delete_serviceRecordAll()
	{
		$id = $this->input->get('id');
		$this->db->query("delete  from hris_employment where IDNumber='" . $id . "'");
		$this->Page_model->insert_at('Deleted service record', $this->db->insert_id());
		$this->session->set_flashdata('success', 'Deleted successfully!');
		redirect('Page/serviceRecord');
	}


	//Employee List By Position
	function employeelistPosition()
	{
		$position = $this->input->get('position');
		$result['data'] = $this->PersonnelModel->employeelistPosition($position);
		$this->load->view('list_position', $result);
	}

	function sg_summary()
	{
		$result['data'] = $this->PersonnelModel->sg_summary();
		$this->load->view('by_sg', $result);
	}

	function employeelistSG()
	{
		$sg = $this->input->get('sg');
		$result['data'] = $this->PersonnelModel->employeelistSG($sg);
		$this->load->view('list_sg', $result);
	}


	function employeelistv1()
	{
		$result['data'] = $this->PersonnelModel->employeelistv1();
		$this->load->view('list_personnelv1', $result);
	}

	public function printEmployeelistv1()
	{
		$result['data'] = $this->PersonnelModel->employeelistv1();
		$this->load->view('list_personnelv1_print', $result);
	}

	function employeelistv2()
	{
		$result['data'] = $this->PersonnelModel->employeelistv2();
		$this->load->view('list_personnelv2', $result);
	}

	function printEmployeelistv2()
	{
		$result['data'] = $this->PersonnelModel->employeelistv2();
		$this->load->view('list_personnelv2_print', $result);
	}

	//Active Employees with 60-65 years old
	function employeelistv3()
	{
		$que = $this->db->query("update hris_staff set age=(SELECT TIMESTAMPDIFF(YEAR,BirthDate,CURDATE()))");
		$que = $this->db->query("update hris_staff set retirement=DATE_ADD( BirthDate, INTERVAL 65 YEAR ),retYear=YEAR(DATE_ADD( BirthDate, INTERVAL 65 YEAR ))");
		$result['data'] = $this->PersonnelModel->employeelistv3();
		$this->load->view('list_personnelv3', $result);
	}

	//Active Employees with 60-65 years old
	function for_retirement_school()
	{
		$schoolID = $this->session->userdata('username');
		$que = $this->db->query("update hris_staff set age=(SELECT TIMESTAMPDIFF(YEAR,BirthDate,CURDATE()))");
		$que = $this->db->query("update hris_staff set retirement=DATE_ADD( BirthDate, INTERVAL 65 YEAR ),retYear=YEAR(DATE_ADD( BirthDate, INTERVAL 65 YEAR ))");
		$result['data'] = $this->PersonnelModel->for_retirement_school($schoolID);
		$this->load->view('list_personnelv3', $result);
	}

	function liquidation()
	{
		$fys = $this->session->cur_fy;
		$data['app'] = $this->Common->two_cond_group('sgod_app', 'school_id', $this->session->username, 'fy', $fys, 'b_code');
		$this->load->view('liquidation', $data);
	}

	function l_view()
	{
		$school_id = $this->uri->segment(3);
		$fy = $this->uri->segment(4);
		$bcode = $this->uri->segment(5);
		$mm = $this->uri->segment(6);

		$data['mon'] = $this->uri->segment(6);
		$data['data_row'] = $this->SGODModel->get_aip_row($school_id, $fy, $bcode);

		$data['school'] = $this->SGODModel->one_cond_row('schools', 'schoolId', $school_id);

		$liq = $this->Common->four_cond_count_row('sgod_liquidation', 'school_id', $school_id, 'bcode', $bcode, 'fy', $fy, 'mm', $mm);

		$bs = $this->SGODModel->one_cond_row('sgod_school_allocation', 'alloc_batch', $bcode);

		if ($liq->num_rows() == 0) {

			if ($bs->fund_type == 0) {
				$data['mr'] = $this->SGODModel->aip_category('sgod_aip', $school_id, $fy, $bcode, 'MINOR REPAIR');
				$data['mb'] = $this->SGODModel->aip_category('sgod_aip', $school_id, $fy, $bcode, 'MANDATORY BILLS');
				$data['tli'] = $this->SGODModel->aip_category('sgod_aip', $school_id, $fy, $bcode, 'TEACHING-LEARNING INSTRUCTION');
				$data['tst'] = $this->SGODModel->aip_category('sgod_aip', $school_id, $fy, $bcode, 'TRAININGS/SEMINAR/TRAVEL');
			}elseif($bs->fund_type == 2) {
				$data['mr'] = $this->SGODModel->aip_category_sbfp('sgod_aip', $school_id, $fy, $bcode, 'MINOR REPAIR');
				$data['mb'] = $this->SGODModel->aip_category_sbfp('sgod_aip', $school_id, $fy, $bcode, 'MANDATORY BILLS');
				$data['tli'] = $this->SGODModel->aip_category_sbfp('sgod_aip', $school_id, $fy, $bcode, 'TEACHING-LEARNING INSTRUCTION');
				$data['tst'] = $this->SGODModel->aip_category_sbfp('sgod_aip', $school_id, $fy, $bcode, 'TRAININGS/SEMINAR/TRAVEL');
			} else {
				$data['mr'] = $this->SGODModel->aip_category_sned('sgod_aip', $school_id, $fy, $bcode, 'MINOR REPAIR');
				$data['mb'] = $this->SGODModel->aip_category_sned('sgod_aip', $school_id, $fy, $bcode, 'MANDATORY BILLS');
				$data['tli'] = $this->SGODModel->aip_category_sned('sgod_aip', $school_id, $fy, $bcode, 'TEACHING-LEARNING INSTRUCTION');
				$data['tst'] = $this->SGODModel->aip_category_sned('sgod_aip', $school_id, $fy, $bcode, 'TRAININGS/SEMINAR/TRAVEL');
			}

			$this->load->view('liq_view', $data);
		} else {

			$data['mr'] = $this->SGODModel->aip_category_liq('sgod_liquidation', $school_id, $fy, $bcode, 'MINOR REPAIR', $mm);
			$data['mb'] = $this->SGODModel->aip_category_liq('sgod_liquidation', $school_id, $fy, $bcode, 'MANDATORY BILLS', $mm);
			$data['tli'] = $this->SGODModel->aip_category_liq('sgod_liquidation', $school_id, $fy, $bcode, 'TEACHING-LEARNING INSTRUCTION', $mm);
			$data['tst'] = $this->SGODModel->aip_category_liq('sgod_liquidation', $school_id, $fy, $bcode, 'TRAININGS/SEMINAR/TRAVEL', $mm);


			$this->load->view('liq_viewv2', $data);
		}
	}

	function liq_view()
	{

		$data['title'] = 'Title';

		$this->load->view('liq_form', $data);
	}

	function liq_update()
	{

		$data['title'] = 'Title';

		$data['data'] = $this->Common->one_cond_row('sgod_liquidation', 'id', $this->uri->segment(3));
		$data['acc_name'] = $this->Common->no_cond('sgod_liq_acc_name');


		$school_id = $this->input->post('school_id');
		$fy = $this->input->post('fy');
		$mm = $this->input->post('mm');
		$mqty = $this->input->post('mqty');
		$bcode = $this->input->post('bcode');

		if ($this->input->post('submit')) {
			$this->SGODModel->liq_update();
			$this->session->set_flashdata('success', 'Saved successfully.');
			redirect(base_url() . 'Page/l_view/' . $school_id . '/' . $fy . '/' . $bcode . '/' . $mm . '/' . $mqty);
		}

		$this->load->view('liq_form_update', $data);
	}

	function liquidation_view()
	{
		$data['title'] = 'Title';
		$data['data'] = $this->SGODModel->show_rca();

		$this->load->view('liq_viewv2', $data);
	}

	function liquidation_insert()
	{
		$data['title'] = 'Title';

		$mm = $this->uri->segment(3);
		$qmm = $this->uri->segment(4);
		$bcode = $this->uri->segment(5);

		$m = 'b.' . $mm; // Constructing the first variable

		$qm = $qmm; // Constructing the second variable

		$cm = $mm; // Constructing the second variable
		$qcm = $qmm; // Constructing the second variable

		// Call the model method and store the result
		$data['data'] = $this->SGODModel->insert_rca($m, $qm, $cm, $qcm, $bcode);

		redirect('Page/liquidation');
	}




	function printEmployeelistv3()
	{
		$que = $this->db->query("update hris_staff set age=(SELECT TIMESTAMPDIFF(YEAR,BirthDate,CURDATE()))");
		$que = $this->db->query("update hris_staff set retirement=DATE_ADD( BirthDate, INTERVAL 65 YEAR ),retYear=YEAR(DATE_ADD( BirthDate, INTERVAL 65 YEAR ))");
		$result['data'] = $this->PersonnelModel->employeelistv3();
		$this->load->view('list_personnelv3_print', $result);
	}

	//For Step Increment
	function step_increment()
	{
		$que = $this->db->query("update hris_staff set sl_after_lastappointment =(SELECT TIMESTAMPDIFF(YEAR,lastAppointmentDate,CURDATE()))");
		$result['data'] = $this->PersonnelModel->step_increment();
		$result['data1'] = $this->PersonnelModel->step_increment1();
		$result['data2'] = $this->PersonnelModel->step_increment2();
		$result['data3'] = $this->PersonnelModel->step_increment3();
		$result['data4'] = $this->PersonnelModel->step_increment4();
		$result['data5'] = $this->PersonnelModel->step_increment5();
		$result['data6'] = $this->PersonnelModel->step_increment6();
		$result['data7'] = $this->PersonnelModel->step_increment7();
		$this->load->view('list_step_increment', $result);
	}

	function step_increment_school()
	{
		$schoolID = $this->session->userdata('username');
		$que = $this->db->query("update hris_staff set sl_after_lastappointment =(SELECT TIMESTAMPDIFF(YEAR,lastAppointmentDate,CURDATE()))");
		$result['data'] = $this->PersonnelModel->step_increment_sc($schoolID);
		$result['data1'] = $this->PersonnelModel->step_increment1_sc($schoolID);
		$result['data2'] = $this->PersonnelModel->step_increment2_sc($schoolID);
		$result['data3'] = $this->PersonnelModel->step_increment3_sc($schoolID);
		$result['data4'] = $this->PersonnelModel->step_increment4_sc($schoolID);
		$result['data5'] = $this->PersonnelModel->step_increment5_sc($schoolID);
		$result['data6'] = $this->PersonnelModel->step_increment6_sc($schoolID);
		$result['data7'] = $this->PersonnelModel->step_increment7_sc($schoolID);
		$this->load->view('list_step_increment_school', $result);
	}

	function step_increment_list()
	{
		$id = $this->input->get('id');
		$result['data'] = $this->PersonnelModel->step_increment_list($id);
		$this->load->view('list_step_increment2', $result);
	}

	function step_increment_list_school()
	{
		$id = $this->input->get('id');
		$schoolID = $this->session->userdata('username');
		$result['data'] = $this->PersonnelModel->step_increment_list_school($id, $schoolID);
		$this->load->view('list_step_increment2', $result);
	}

	//Active Employees with corresponding number of years in service
	function employeelistv4()
	{
		$que = $this->db->query("update hris_staff set serviceLenght=(SELECT TIMESTAMPDIFF(YEAR,dateHired,CURDATE()))");
		$result['data'] = $this->PersonnelModel->employeelistv4();
		$this->load->view('list_personnelv4', $result);
	}

	function service_lenght()
	{
		$schoolID = $this->session->userdata('username');
		$que = $this->db->query("update hris_staff set serviceLenght=(SELECT TIMESTAMPDIFF(YEAR,dateHired,CURDATE()))");
		$result['data'] = $this->PersonnelModel->service_lenght($schoolID);
		$this->load->view('list_personnelv4', $result);
	}

	function regApplicants()
	{
		$result['data'] = $this->Page_model->get_all_posts('hris_applicant');
		$this->load->view('list_reg_applicants', $result);
	}

	function appPerMun()
	{
		$mun = $this->input->get('mun');
		$jobID = $this->input->get('jobID');
		$result['data'] = $this->PersonnelModel->appPerMun($jobID, $mun);
		$this->load->view('list_applicants_for_evaluation', $result);
	}

	public function hire()
	{

		$password = $this->input->get('bd');
		$hash = password_hash($password, PASSWORD_DEFAULT);

		$last = $this->Common->one_cond_row('count', 'id', 3);
		$number = $last->number;
		$lastThreeDigits = substr($number, -3);
		$lastThreeDigits = (int)$lastThreeDigits + 1;

		if ($lastThreeDigits > 999) {
			$lastThreeDigits = 000;
		}

		$lastThreeDigits = str_pad($lastThreeDigits, 3, "0", STR_PAD_LEFT);

		$newIDNumber = date('Y') . '' . date('m') . '' . $lastThreeDigits;

		date_default_timezone_set('Asia/Manila'); # add your city to set local time zone
		$dateHired = date("Y-m-d");

		$id = $this->input->get('id');
		$username = $this->input->get('empEmail');

		$this->db->query("insert into hris_staff (IDNumber, FirstName, MiddleName, LastName, NameExtn, prefix, MaritalStatus, BirthDate, Sex, height, weight, bloodType, gsis, pagibig, philHealth, sssNo, tinNo, resHouseNo, resStreet, resVillage, resCity, resProvince, perStreet, perVillage, perBarangay, perCity, perProvince, empTelNo, empMobile, empEmail, age, citizenship, currentStatus, dateHired, citizenshipCountry) select '" . $newIDNumber . "', FirstName, MiddleName, LastName, NameExtn, prefix, MaritalStatus, BirthDate, Sex, height, weight, bloodType, gsis, pagibig, philHealth, sssNo, tinNo, resHouseNo, resStreet, resVillage, resCity, resProvince, perStreet, perVillage, perBarangay, perCity, perProvince, empTelNo, empMobile, empEmail, age, citizenship, 'Active','" . $dateHired . "','Philippines' from hris_applicant where record_no='" . $id . "'");
		$this->db->query("INSERT INTO users(username, password, position, fname, mname, lname, sex, user_id)SELECT '" . $newIDNumber . "', '$hash', 'user', FirstName, MiddleName, LastName, Sex, '" . $newIDNumber . "' FROM hris_applicant where record_no='" . $id . "'");
		$this->Page_model->count_update(3, '1' . $lastThreeDigits);
		$this->session->set_flashdata('success', 'Data of the applicant has been copied successfully!');
		redirect('personnel_profile/' . $newIDNumber);
	}

	public function delete_regApplicants()
	{
		$id = $this->input->get('id');
		$this->db->query("delete  from hris_applicant where id='" . $id . "'");
		$this->Page_model->insert_at('Deleted applicant profile');
		$this->session->set_flashdata('success', 'Deleted successfully!');
		redirect('Page/regApplicants');
	}


	//Active Employees ()
	function employeelistv5()
	{
		$stat = 'Active';
		$result['data'] = $this->PersonnelModel->employeelistv5($stat);
		$this->load->view('list_personnelv5', $result);
	}
	//Resigned Employees ()
	public function Resigned()
	{
		$stat = 'Resigned';
		$result['data'] = $this->PersonnelModel->employeelistv5($stat);
		$this->load->view('list_personnelv5', $result);
	}

	//Retired Employees ()
	public function Retired()
	{
		$stat = 'Retired';
		$result['data'] = $this->PersonnelModel->employeelistv5($stat);
		$this->load->view('list_personnelv5', $result);
	}

	//Transferred Employees ()
	public function Transferred()
	{
		$stat = 'Transferred';
		$result['data'] = $this->PersonnelModel->employeelistv5($stat);
		$this->load->view('list_personnelv5', $result);
	}

	//Deceased Employees ()
	public function Deceased()
	{
		$stat = 'Deceased';
		$result['data'] = $this->PersonnelModel->employeelistv5($stat);
		$this->load->view('list_personnelv5', $result);
	}

	//For Retirement Employees ()
	public function retirement()
	{
		$year = date('Y');
		$result['data'] = $this->PersonnelModel->retirement($year);
		$this->load->view('list_retirement', $result);
	}

	public function perAppointmentDate()
	{
		$year = date('Y');
		$result['data'] = $this->PersonnelModel->retirement($year);
		$this->load->view('list_per_appointmentdate', $result);
	}

	public function retirement2()
	{
		$year = $this->input->post('year');
		$que = $this->db->query("update hris_staff set retirement=DATE_ADD( BirthDate, INTERVAL 65 YEAR ),retYear=YEAR(DATE_ADD( BirthDate, INTERVAL 65 YEAR ))");
		$result['data'] = $this->PersonnelModel->retirement($year);
		$this->load->view('list_retirement', $result);
	}

	public function unfilledItems()
	{
		$result['data'] = $this->PersonnelModel->getUnusedItemNos();
		$this->load->view('unfilled_items', $result);
	}

	// Leave Credits
	public function leaveCreditsEmp()
	{
		$IDNumber = $this->session->userdata('username');
		$result['data'] = $this->PersonnelModel->leaveCreditsEmp($IDNumber);
		$this->load->view('list_leave_credits_emp', $result);
	}


	public function update_leave_data()
	{
		$this->Page_model->update_leave_records();
		echo "Leave records updated successfully!";
	}

	// Leave Credits
	public function leaveCredits()
	{

		$currentMonth = date('m');
		$currentYear = date('Y');

		$result['data'] = $this->PersonnelModel->leaveCredits($currentMonth, $currentYear);

		$this->load->view('list_leave_credits', $result);
	}


	public function leaveCreditsMonthly()
	{
		$month = $this->input->post('month');
		$year = $this->input->post('year');
		$result['data'] = $this->PersonnelModel->leaveCreditsMonthly($month, $year);
		$this->load->view('list_leave_credits_monthly', $result);
	}

	public function leaveCreditsSummary()
	{
		// Retrieve user position and username from session data
		$position = $this->session->userdata('position');
		$schoolID = $this->session->userdata('username');

		// Determine the appropriate data to fetch based on user position
		if ($position === 'School') {
			$result['data'] = $this->PersonnelModel->leaveCreditsSummarySchool($schoolID);
		} else {
			$result['data'] = $this->PersonnelModel->leaveCreditsSummary();
		}

		// Add search personnel data
		$result['personnelList'] = $this->PersonnelModel->searchPersonnel();

		// Load the view with the data
		$this->load->view('list_leave_credits_v2', $result);
	}


	public function CalleaveCreditsSummary()
	{
		// Step 1: Fetch summarized data grouped by IDNumber
		$this->db->select('IDNumber, SUM(vlCredit) AS vlTotal, SUM(slCredit) AS slTotal');
		$this->db->group_by('IDNumber');
		$query = $this->db->get('hris_leave_credits');
		$leave_data = $query->result_array();

		// Counter for tracking processed records
		$updated_count = 0;
		$inserted_count = 0;

		// Step 2: Insert or Update hris_leaverecords
		foreach ($leave_data as $row) {
			// Check if IDNumber already exists
			$this->db->where('IDNumber', $row['IDNumber']);
			$existing = $this->db->get('hris_leaverecords')->row_array();

			if ($existing) {
				// Update record
				$this->db->where('IDNumber', $row['IDNumber']);
				$this->db->update('hris_leaverecords', [
					'vlTotal' => $row['vlTotal'],
					'slTotal' => $row['slTotal']
				]);
				$updated_count++; // Increment update counter
			} else {
				// Insert new record
				$this->db->insert('hris_leaverecords', [
					'IDNumber' => $row['IDNumber'],
					'vlTotal' => $row['vlTotal'],
					'slTotal' => $row['slTotal']
				]);
				$inserted_count++; // Increment insert counter
			}
		}

		// Step 3: Set flash message
		$this->session->set_flashdata(
			'success',
			"Leave credits summary updated successfully! Updated: $updated_count, Inserted: $inserted_count"
		);

		redirect('Page/leaveCreditsSummary');
	}



	public function updateLeaveCredits()
	{
		$this->load->helper('form');
		$this->load->library('form_validation');

		// Set validation rules
		$this->form_validation->set_rules('vlTotal', 'Vacation Leave', 'required|numeric');
		$this->form_validation->set_rules('slTotal', 'Sick Leave', 'required|numeric');
		$this->form_validation->set_rules('cocTotal', 'COC/Service Record', 'required|numeric');
		$this->form_validation->set_rules('ID', 'ID', 'required');

		if ($this->form_validation->run() == FALSE) {
			$this->session->set_flashdata('error', validation_errors());
			redirect('Page/leaveCreditsSummary');
		} else {
			// Get form data
			$id = $this->input->post('ID');
			$vlTotal = $this->input->post('vlTotal');
			$slTotal = $this->input->post('slTotal');
			$cocTotal = $this->input->post('cocTotal');
			$currentDate = date('Y-m-d');
			$currentTime = date('H:i:s');

			// Get session data for logging
			$position = $this->session->userdata('position');
			$username = $this->session->userdata('username');
			$IDNumber = $this->session->userdata('IDNumber'); // Fixed incorrect session retrieval

			// Check if ID exists in hris_leaverecords
			$this->db->where('ID', $id);
			$existing = $this->db->get('hris_leaverecords')->row_array();

			if ($existing) {
				// Log old values
				$oldVl = $existing['vlTotal'];
				$oldSl = $existing['slTotal'];
				$oldCoc = $existing['cocTotal'];

				// Update record
				$this->db->where('ID', $id);
				$this->db->update('hris_leaverecords', [
					'vlTotal' => $vlTotal,
					'slTotal' => $slTotal,
					'cocTotal' => $cocTotal
				]);

				// Insert into audit trail
				$atDesc = "Updated Leave Credits for ID $id (VL: $oldVl → $vlTotal, SL: $oldSl → $slTotal, COC: $oldCoc → $cocTotal)";
				$this->db->insert('atrail', [
					'atDesc' => $atDesc,
					'atDate' => $currentDate,
					'atTime' => $currentTime,
					'atRes' => $position,
					'atSNo' => $username
				]);

				$this->session->set_flashdata('success', 'Leave credits updated successfully!');
			} else {
				// Insert new record
				$this->db->insert('hris_leaverecords', [
					'ID' => $id,
					'vlTotal' => $vlTotal,
					'slTotal' => $slTotal,
					'cocTotal' => $cocTotal
				]);

				// Insert into audit trail
				// $atDesc = "Inserted new Leave Credits for ID $id (VL: $vlTotal, SL: $slTotal, COC: $cocTotal)";
				// $this->db->insert('atrail', [
				// 	'atDesc' => $atDesc,
				// 	'atDate' => $currentDate,
				// 	'atTime' => $currentTime,
				// 	'atRes' => $position,
				// 	'atSNo' => $username
				// ]);

				$this->session->set_flashdata('success', 'New leave credits record created successfully!');
			}

			redirect('Page/leaveCreditsSummary');
		}
	}




	public function leaveApplications()
	{
		$IDNumber = $this->session->userdata('username');
		$result['data'] = $this->PersonnelModel->leaveApplications($IDNumber);
		$result['data1'] = $this->PersonnelModel->leaveCreditsdisplay($IDNumber);
		$this->load->view('list_leave_applications', $result);

		if ($this->input->post('submit')) {
			$leaveType = $this->input->post('leaveType');
			$daysApplied = $this->input->post('daysApplied');
			$dateFrom = $this->input->post('dateFrom');
			$dateTo = $this->input->post('dateTo');
			$abroad = $this->input->post('abroad') ?? null;
			$spentPlace = $this->input->post('spentPlace') ?? null;
			$sickLeave = $this->input->post('sickLeave') ?? null;
			$leaveStatReasons = $this->input->post('leaveStatReasons') ?? null;
			$vlTotal = $this->input->post('vlTotal');
			$slTotal = $this->input->post('slTotal');
			$commutation = $this->input->post('commutation') ?? null;

			date_default_timezone_set('Asia/Manila');
			$appDate = date("Y-m-d");

			// File upload config
			$config['upload_path']   = './uploads/leave_attachement/';
			$config['allowed_types'] = 'pdf';
			$config['max_size']      = 2048; // 2MB
			$config['encrypt_name']  = TRUE;

			$this->load->library('upload', $config);

			$leaveAttachment = null;

			if (!empty($_FILES['leaveAttachment']['name'])) {
				if ($this->upload->do_upload('leaveAttachment')) {
					$uploadData = $this->upload->data();
					$leaveAttachment = $uploadData['file_name'];
				} else {
					$this->session->set_flashdata('error', $this->upload->display_errors());
					redirect('Page/leaveApplications');
				}
			}

			$data = [
				'IDNumber' => $IDNumber,
				'appDate' => $appDate,
				'leaveType' => $leaveType,
				'daysApplied' => $daysApplied,
				'dateFrom' => $dateFrom,
				'dateTo' => $dateTo,
				'leaveStatus' => 'For Approval',
				'abroad' => $abroad,
				'spentPlace' => $spentPlace,
				'sickLeave' => $sickLeave,
				'leaveStatReasons' => $leaveStatReasons,
				'vlTotal' => $vlTotal,
				'slTotal' => $slTotal,
				'commutation' => $commutation,
				'leaveAttachment' => $leaveAttachment // save file name here
			];

			$this->db->insert('hris_leave', $data);

			$this->session->set_flashdata('success', 'Encoded successfully.');
			redirect('Page/leaveApplications');
		}
	}



	public function LeaveSettings()
	{
		$IDNumber = $this->session->userdata('username');
		$result['data'] = $this->PersonnelModel->leaveSetting($IDNumber);
		$result['data1'] = $this->PersonnelModel->leaveCreditsdisplay($IDNumber);
		$result['data2'] = $this->PersonnelModel->hris_staff();
		$result['data3'] = $this->PersonnelModel->hris_users();

		$this->load->view('LeaveSettings', $result);

		if ($this->input->post('submit')) {
			// Check if already exists
			$existing = $this->db->get_where('epmloyee_superior', ['IDNumber' => $IDNumber])->row();

			if ($existing) {
				// If exists — just show message, do not insert
				$this->session->set_flashdata('error', 'You can only add one superior setup.');
				redirect('Page/LeaveSettings');
			} else {
				// If none — proceed to insert
				$endorser = $this->input->post('endorser');
				$approver = $this->input->post('approver');
				$endorserName = $this->input->post('endorserName');
				$approverName = $this->input->post('approverName');

				$data = [
					'IDNumber' => $IDNumber,
					'endorser' => $endorser,
					'approver' => $approver,
					'endorserName' => $endorserName,
					'approverName' => $approverName,
				];

				$this->db->insert('epmloyee_superior', $data);
				$this->session->set_flashdata('success', 'Encoded successfully.');
				redirect('Page/LeaveSettings');
			}
		}
	}





	public function updateSuperior()
	{
		$supID = $this->input->post('supID');
		$endorser = $this->input->post('endorser');
		$approver = $this->input->post('approver');
		$endorserName = $this->input->post('endorserName');
		$approverName = $this->input->post('approverName');

		$data = [
			'endorser' => $endorser,
			'approver' => $approver,
			'endorserName' => $endorserName,
			'approverName' => $approverName,
		];

		$this->db->where('supID', $supID);
		$this->db->update('epmloyee_superior', $data);

		$this->session->set_flashdata('success', 'Superior record updated successfully.');
		redirect('Page/LeaveSettings');
	}




	public function leave_credits_approval()
	{
		$result['data'] = $this->PersonnelModel->leave_coc();
		$result['data1'] = $this->PersonnelModel->hris_staff();

		if ($this->input->post()) {
			$cocTotal = $this->input->post('cocTotal');
			$cocID = $this->input->post('cocID');

			if (!empty($cocID) && !empty($cocTotal)) {
				// Fetch IDNumber from leave_coc
				$leaveRecord = $this->PersonnelModel->get_leave_coc_by_id($cocID);
				if ($leaveRecord) {
					$idNumber = $leaveRecord->IDNumber;

					// Fetch current cocTotal from hris_leaverecords
					$currentLeaveRecord = $this->PersonnelModel->get_hris_leaverecord_by_id($idNumber);
					$currentCocTotal = $currentLeaveRecord ? $currentLeaveRecord->cocTotal : 0;

					// Calculate new total
					$newCocTotal = $currentCocTotal + $cocTotal;

					// Update leave_coc status to Approved
					$updateData = [
						'cocTotal' => $cocTotal,
						'cocStat' => 'Approved'
					];
					$this->PersonnelModel->update_leave_coc($cocID, $updateData);

					// Update hris_leaverecords cocTotal
					$this->PersonnelModel->update_hris_leaverecords($idNumber, $newCocTotal);

					$this->session->set_flashdata('success', 'COC successfully approved and leave records updated.');
				} else {
					$this->session->set_flashdata('danger', 'Invalid COC ID.');
				}
			} else {
				$this->session->set_flashdata('danger', 'Invalid input. Approval failed.');
			}
			redirect('Page/leave_credits_approval');
		}

		$this->load->view('leave_credits_approval', $result);
	}


	public function disapprove_leave_coc()
	{
		$cocID = $this->input->post('cocID');
		$reason = $this->input->post('reason');

		if (!empty($cocID) && !empty($reason)) {
			$updateData = [
				'cocStat' => 'Disapproved',
				'reason' => $reason
			];
			$this->PersonnelModel->update_leave_coc($cocID, $updateData);
			$this->session->set_flashdata('danger', 'COC has been disapproved.');
		} else {
			$this->session->set_flashdata('danger', 'Invalid input. Disapproval failed.');
		}
		redirect('Page/leave_credits_approval');
	}




	// public function leave_credits_app()
	// {
	// 	$this->input->get('cocID');
	//     $cocTotal = $this->input->post('cocTotal');
	//         $cocID = $this->input->post('cocID');
	//     $updateData = [
	//         'cocTotal' => $cocTotal,
	//         'cocStat' => 'Approved'
	//     ];
	//     $this->PersonnelModel->update_leave_coc($cocID, $updateData);
	//     redirect('Page/leave_credits_approval');
	// }











	public function leave_credits()
	{
		$IDNumber = $this->session->userdata('username'); // Treat username as IDNumber

		$result['data'] = $this->PersonnelModel->leave_coc($IDNumber);
		$result['data1'] = $this->PersonnelModel->hris_staff(); // optional, for dropdowns etc.

		$this->load->view('leave_credits', $result);
	}


	public function addCOC()
	{
		$username = $this->session->userdata('username'); // Fetch username from session

		$IDNumber = $this->input->post('IDNumber');
		$act_attend = $this->input->post('act_attend');
		$act_date = $this->input->post('act_date');
		$to_date = $this->input->post('to_date');
		$noDays = $this->input->post('noDays');
		$cocStat = $this->input->post('cocStat');

		// Handle file upload
		$config['upload_path'] = './uploads/';
		$config['allowed_types'] = 'pdf|doc|docx|jpg|jpeg|png';
		$config['max_size'] = 2048; // Max 2MB
		$this->load->library('upload', $config);

		if ($this->upload->do_upload('fileAttach')) {
			$fileData = $this->upload->data();
			$fileAttach = $fileData['file_name'];

			$data = [
				'IDNumber' => $IDNumber,
				'act_attend' => $act_attend,
				'act_date' => $act_date,
				'to_date' => $to_date,
				'noDays' => $noDays,
				'cocStat' => $cocStat,
				'fileAttach' => $fileAttach,
			];

			$this->db->insert('leave_coc', $data);
			$this->session->set_flashdata('success', 'COC Credit added successfully! Waiting for approval.');
		} else {
			$this->session->set_flashdata('danger', $this->upload->display_errors());
		}

		redirect('Page/leave_credits');
	}


	public function delete_leave_coc()
	{
		// Get cocID from POST request
		$cocID = $this->input->post('cocID');

		if ($cocID) {
			// Call model function to delete the record
			$delete = $this->PersonnelModel->delete_leave_coc($cocID);

			if ($delete) {
				echo json_encode(["success" => true]);
			} else {
				echo json_encode(["success" => false, "message" => "Failed to delete record."]);
			}
		} else {
			echo json_encode(["success" => false, "message" => "Invalid request."]);
		}
	}













	public function deleteLeave()
	{
		$leaveID = $this->input->get('id'); // Get the leaveID from the URL

		if ($leaveID) {
			// Fetch the leaveAttachment filename from the database
			$this->db->where('leaveID', $leaveID);
			$leave = $this->db->get('hris_leave')->row();

			if ($leave && !empty($leave->leaveAttachment)) {
				// Build the file path
				$filePath = FCPATH . 'uploads/leave_attachement/' . $leave->leaveAttachment;

				// Check if file exists before deleting
				if (file_exists($filePath)) {
					unlink($filePath); // Delete the file
				}
			}

			// Delete the leave application from the database
			$this->db->where('leaveID', $leaveID);
			$this->db->delete('hris_leave');

			// Set a success message
			$this->session->set_flashdata('success', 'Leave application deleted successfully.');
		} else {
			// Set an error message
			$this->session->set_flashdata('error', 'Failed to delete leave application. Invalid ID.');
		}

		// Redirect back to the leave applications page
		redirect('Page/leaveApplications');
	}





	public function printLeaveForm()
	{

		$IDNumber = $this->session->userdata('username');
		$id = $this->input->get('id');
		$result['data2'] = $this->PersonnelModel->leaveCreditsdisplay($IDNumber);
		$result['data'] = $this->PersonnelModel->leaveApplicationsPrint($id);
		$result['data1'] = $this->PersonnelModel->leaveSettings();
		$result['evaluator'] = $this->PersonnelModel->getEvaluatorInfo($id);
		$result['endorser'] = $this->PersonnelModel->getEndorserInfo($id);
		$result['approver'] = $this->PersonnelModel->getapproveInfo($id);

		$this->load->view('list_leave_applications_print', $result);
	}

	// Leave Credits
	public function monthlyLeaveCredits()
	{
		$date = (new DateTime())->format('Y-m-d');

		$year = $this->input->get('year');
		$month = $this->input->get('month');

		if (empty($year) || empty($month)) {
			$this->session->set_flashdata('danger', 'Year and Month are required.');
			redirect('Page/leaveCredits');
			return;
		}

		$this->db->where('vlMonth', $month);
		$this->db->where('vlYear', $year);
		$query = $this->db->get('hris_leave_credits');

		if (!empty($query->result_array())) {
			$this->session->set_flashdata('danger', 'Duplicate records found for the selected month and year.');
			redirect('Page/leaveCredits');
			return;
		}

		$this->db->trans_start();

		$this->db->query("
			INSERT INTO hris_leave_credits (IDNumber, dateProcessed, vlMonth, vlYear, vlCredit, slCredit) 
			SELECT IDNumber, ?, ?, ?, '1.25', '1.25' 
			FROM hris_staff 
			WHERE currentStatus = 'Active' AND leaveCredits = 'Yes'
		", [$date, $month, $year]);


		$this->db->query("
			UPDATE hris_leaverecords t1
			JOIN hris_staff t2 
			ON t1.IDNumber = t2.IDNumber
			SET t1.vlTotal = t1.vlTotal + 1.25, 
				t1.slTotal = t1.slTotal + 1.25
			WHERE t2.currentStatus = 'Active' AND t2.leaveCredits = 'Yes'
		");


		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {

			$this->session->set_flashdata('danger', 'An error occurred while updating leave credits.');
		} else {

			$this->session->set_flashdata('success', 'Leave credits successfully updated!');
		}

		redirect('Page/leaveCredits');
	}


	function viewfilesAll()
	{
		$result['data'] = $this->PersonnelModel->viewfilesAll();
		$this->load->view('hr_201files', $result);
	}

	function plantillaPositions()
	{
		$result['data'] = $this->PersonnelModel->plantillaPositions();
		$this->load->view('list_plantilla', $result);
	}

	function loyalty()
	{
		$result['data'] = $this->PersonnelModel->loyalty();
		$result['data1'] = $this->PersonnelModel->loyaltyCountsv1();
		$this->load->view('list_for_loyalty', $result);
	}

	function loyaltyList()
	{
		$type = urldecode($this->uri->segment(3));
		$result['data'] = $this->PersonnelModel->loyaltyList($type);
		$this->load->view('list_for_loyalty_group', $result);
	}

	function retiredList()
	{
		$type = urldecode($this->uri->segment(3));
		$result['data'] = $this->PersonnelModel->loyaltyList($type);
		$this->load->view('list_for_loyalty_group', $result);
	}

	function empReports()
	{
		$result['data'] = $this->PersonnelModel->elemCounts();
		$result['data1'] = $this->PersonnelModel->secCounts();
		$result['data2'] = $this->PersonnelModel->shsCounts();
		$result['data3'] = $this->PersonnelModel->loyaltyCounts(); // loyalty
		$result['data4'] = $this->PersonnelModel->retiredCounts(); // retired
		$result['data5'] = $this->PersonnelModel->resignedCounts(); // resigned

		$this->load->view('reports', $result);
	}

	function empGroup()
	{
		$cat = urldecode($this->uri->segment(3));
		$group = urldecode($this->uri->segment(4));
		$result['data'] = $this->PersonnelModel->empGroup($cat, $group);
		$result['data1'] = $this->PersonnelModel->empGroupPosition($cat, $group);
		$this->load->view('empGroup', $result);
	}

	function empGroup2()
	{
		$group = urldecode($this->uri->segment(3));
		$result['data'] = $this->PersonnelModel->empGroup2($group);
		$result['data1'] = $this->PersonnelModel->empGroup2Position($group);
		$result['data2'] = $this->PersonnelModel->empGroup2PositionB($group);
		$this->load->view('empGroupv2', $result);
	}

	function listPerPositionDept()
	{
		$position = urldecode($this->uri->segment(3));
		$group = urldecode($this->uri->segment(4));
		$result['data'] = $this->PersonnelModel->listPerPositionDept($position, $group);
		$this->load->view('list_per_position_dept', $result);
	}

	//System Settings
	function settings()
	{
		// $result['data']=$this->PersonnelModel->employeelistv3();
		$this->load->view('system_settings');

		if ($this->input->post('submit')) {
			// Age Caculation
			$que = $this->db->query("update hris_staff set age=(SELECT TIMESTAMPDIFF(YEAR,BirthDate,CURDATE()))");
			$this->session->set_flashdata('success', 'Updated successfully!');
		} elseif ($this->input->post('retirement')) {
			$que = $this->db->query("update hris_staff set retirement=DATE_ADD( BirthDate, INTERVAL 65 YEAR ),retYear=YEAR(DATE_ADD( BirthDate, INTERVAL 65 YEAR ))");
			$this->session->set_flashdata('success', 'The retirement date for all employees has been updated successfully!');
			// lenght of service
		} elseif ($this->input->post('service')) {
			$que = $this->db->query("update hris_staff set serviceLenght=(SELECT TIMESTAMPDIFF(YEAR,dateHired,CURDATE()))");
			$this->session->set_flashdata('success', 'Successfully updated.');
		} elseif ($this->input->post('increment')) {
			$que = $this->db->query("update hris_staff set sl_after_lastappointment =(SELECT TIMESTAMPDIFF(YEAR,lastAppointmentDate,CURDATE()))");
			$this->session->set_flashdata('success', 'Updated successfully!');
		} elseif ($this->input->post('salaryUpdating')) {
			$que = $this->db->query("update hris_staff t1, payroll_masterlist t2 SET t1.monthlySalary = t2.basicSalary WHERE t1.IDNumber = t2.IDNumber");
			$this->session->set_flashdata('success', 'Updated successfully!');
		} elseif ($this->input->post('loyalty')) {
			$date = date("Y-m-d");
			$que = $this->db->query("update hris_staff set serviceLenght=TIMESTAMPDIFF(YEAR,origAppointmentDate,CURDATE())");
			$que = $this->db->query("delete from hris_loyalty");
			$que = $this->db->query("INSERT INTO hris_loyalty (IDNumber, loyaltyDate, loyaltyType, serviceLenght) SELECT IDNumber,'$date','10th Year', serviceLenght FROM hris_staff where serviceLenght=10");
			$que = $this->db->query("INSERT INTO hris_loyalty (IDNumber, loyaltyDate, loyaltyType, serviceLenght) SELECT IDNumber,'$date','15th Year', serviceLenght FROM hris_staff where serviceLenght=15");
			$que = $this->db->query("INSERT INTO hris_loyalty (IDNumber, loyaltyDate, loyaltyType, serviceLenght) SELECT IDNumber,'$date','20th Year', serviceLenght FROM hris_staff where serviceLenght=20");
			$que = $this->db->query("INSERT INTO hris_loyalty (IDNumber, loyaltyDate, loyaltyType, serviceLenght) SELECT IDNumber,'$date','25th Year', serviceLenght FROM hris_staff where serviceLenght=25");
			$que = $this->db->query("INSERT INTO hris_loyalty (IDNumber, loyaltyDate, loyaltyType, serviceLenght) SELECT IDNumber,'$date','30th Year', serviceLenght FROM hris_staff where serviceLenght=30");
			$que = $this->db->query("INSERT INTO hris_loyalty (IDNumber, loyaltyDate, loyaltyType, serviceLenght) SELECT IDNumber,'$date','35th Year', serviceLenght FROM hris_staff where serviceLenght=35");
			$que = $this->db->query("INSERT INTO hris_loyalty (IDNumber, loyaltyDate, loyaltyType, serviceLenght) SELECT IDNumber,'$date','40th Year', serviceLenght FROM hris_staff where serviceLenght=40");

			$this->session->set_flashdata('success', 'Updated successfully!');
		}
	}

	public function awards()
	{

		$id = $this->input->post('id');

		//get data from the form
		$IDNumber = $this->input->post('IDNumber');
		$award = $this->input->post('award');
		$awardDesc = $this->input->post('awardDesc');
		$awardedBy = $this->input->post('awardedBy');
		$que = $this->db->query("insert into hris_awards(IDNumber, award, awardDesc, awardedBy) values(''$IDNumber','$award','$awardDesc','$awardedBy')");
		$this->session->set_flashdata('success', 'One data added successfully!');
		redirect(base_url() . 'personnel_profile/' . $id);
	}


	function empLoans()
	{
		if ($this->session->userdata('position') === 'user') {
			$IDNumber = $this->session->userdata('username');
			$result['data'] = $this->PersonnelModel->empLoans($IDNumber);
			$this->load->view('emp_loans', $result);
		} else {
			echo "Access Denied";
		}
	}

	function empSalaryHistory()
	{
		if ($this->session->userdata('position') === 'user') {
			$IDNumber = $this->session->userdata('username');
			$result['data'] = $this->PersonnelModel->empSalaryHistory($IDNumber);
			$this->load->view('emp_salary_history', $result);
		} else {
			echo "Access Denied";
		}
	}

	function empBenefits()
	{
		if ($this->session->userdata('position') === 'user') {
			$IDNumber = $this->session->userdata('username');
			$result['data'] = $this->PersonnelModel->empBenefits($IDNumber);
			$this->load->view('emp_benefits', $result);
		} else {
			echo "Access Denied";
		}
	}

	//Employee List
	function employeelist()
	{
		$schoolID = $this->session->userdata('username');
		$result['data'] = $this->PersonnelModel->displaypersonnel($schoolID);
		$this->load->view('hr_personnel_list', $result);
	}

	function employeelist_ict()
	{
		$schoolID = $this->uri->segment(3);
		$result['data'] = $this->PersonnelModel->displaypersonnel($schoolID);
		$this->load->view('hr_personnel_list', $result);
	}

	function employeelist_active()
	{
		$schoolID = $this->session->userdata('username');
		$result['data'] = $this->PersonnelModel->school_personnel_active($schoolID);
		$this->load->view('hr_personnel_list', $result);
	}







	public function empList()
	{
		// Get the selected department from the form
		$selectedDepartment = $this->input->post('departmentDropdown');

		// If a department is selected, fetch the data for that department
		if ($selectedDepartment) {
			$result['data'] = $this->PersonnelModel->employeelist($selectedDepartment);
		} else {
			// If no department is selected, set the data to an empty array
			$result['data'] = [];
		}

		// Fetch all departments for the dropdown
		$result['dept'] = $this->PersonnelModel->getDepartment();

		// Load the view with the result
		$this->load->view('hr_personnel_list_printing', $result);
	}








	function emp_list_dept()
	{
		$schoolID = $this->session->userdata('username');
		$result['data'] = $this->PersonnelModel->emp_list_dept($schoolID);
		$this->load->view('hr_personnel_list_printing', $result);
	}

	//School Profile
	function schoolProfile()
	{
		if ($this->session->userdata('position') === 'School') {

			$schoolID = $this->session->userdata('username');
			$result['data'] = $this->PersonnelModel->schoolProfile($schoolID);
			$this->load->view('profile_school', $result);
		} else {
			$schoolID = $this->input->get('schoolid');
			$result['data'] = $this->PersonnelModel->schoolProfile($schoolID);
			$this->load->view('profile_school', $result);
		}
	}
	function schoolDashboard()
	{
		$schoolID = $this->session->userdata('username');
		$id = $this->session->userdata('username');
		$result['data'] = $this->PersonnelModel->count_all_staff($schoolID);
		$result['data1'] = $this->PersonnelModel->count_all_teaching($schoolID);
		$result['data2'] = $this->PersonnelModel->count_all_nonteaching($schoolID);
		$result['data3'] = $this->PersonnelModel->count_all_inactive($schoolID);
		$result['data4'] = $this->PersonnelModel->announcements_post();
		$result['data5'] = $this->PersonnelModel->school_dashboard($id);
		$result['tr'] = $this->Common->two_cond_count_row('hris_staff', 'payCat', 'Teaching Related', 'schoolID', $schoolID);
		$result['active'] = $this->Common->two_cond_count_row('hris_staff', 'currentStatus', 'Active', 'schoolID', $schoolID);
		$this->load->view('school_dashboard', $result);
	}

	// 	public function Health()
	// {
	//     // Get the logged-in user's position from the session
	//     $position = $this->session->userdata('position');

	//     // Initialize counts to 0
	//     $result['pendingConsultations'] = 0;
	//     $result['processedConsultations'] = 0;

	//     if ($position) { // Ensure position is set
	//         // Get medID(s) from med_patient where the position matches the logged-in user
	//         $medIDs = $this->SettingsModel->get_medIDs_by_position($position);

	//         if (!empty($medIDs)) {
	//             // Count pending consultations
	//             $result['pendingConsultations'] = $this->SettingsModel->med_count_by_status($medIDs, 'Pending');

	//             // Count processed consultations
	//             $result['processedConsultations'] = $this->SettingsModel->med_count_by_status($medIDs, 'Processed');
	//         }
	//     }

	//     // Load the view with the count data
	//     $this->load->view('dashboard_health', $result);
	// }



	public function Health()
	{
		$username = (string) $this->session->userdata('username');
		$sp       = (int) $this->session->userdata('sp');

		$this->load->model('SettingsModel');

		// ---- Consultations ----
		if ($sp > 0) {
			$pending   = (int) $this->SettingsModel->med_count_by_user_status($username, 'Pending');
			$processed = (int) $this->SettingsModel->med_count_by_user_status($username, 'Processed');
		} else {
			$pending   = (int) $this->SettingsModel->med_count_by_status('Pending');
			$processed = (int) $this->SettingsModel->med_count_by_status('Processed');
		}
		$result['pendingConsultations']   = $pending;
		$result['processedConsultations'] = $pending + $processed; // Total

		$result['labTotal'] = (int) $this->SettingsModel->lab_count_all();

		$result['healthExamTotal'] = (int) $this->SettingsModel->health_exam_count_all();
		$result['illnessTally'] = $this->SettingsModel->illness_tally(null);

		$result['caseCategoryTally'] = $this->SettingsModel->disease_category_tally();

		$this->load->view('dashboard_health', $result);
	}

	public function health_category()
	{
		$username = (string) $this->session->userdata('username');
		$sp       = (int) $this->session->userdata('sp');

		$this->load->model('SettingsModel');

		$category = trim((string) $this->input->get('category', true));
		if ($category === '') {
			$this->session->set_flashdata('danger', 'Please select a category.');
			redirect(base_url() . 'Page/Health');
			return;
		}

		$result['category'] = $category;
		$result['caseDiseaseTally'] = $this->SettingsModel->diseases_by_category($category);

		$result['categoryTotal'] = is_array($result['caseDiseaseTally']) ? count($result['caseDiseaseTally']) : 0;

		$this->load->view('health_category_cases', $result);
	}




	function deptDashboard()
	{
		$schoolID = $this->session->userdata('username');
		$id = $this->session->userdata('username');
		// $result['data'] = $this->PersonnelModel->count_all_staff($schoolID);
		// $result['data1'] = $this->PersonnelModel->count_all_teaching($schoolID);
		// $result['data2'] = $this->PersonnelModel->count_all_nonteaching($schoolID);
		// $result['data3'] = $this->PersonnelModel->count_all_inactive($schoolID);
		// $result['data4'] = $this->PersonnelModel->announcements_post();
		// $result['data5'] = $this->PersonnelModel->school_dashboard($id);
		$this->load->view('dashboard_department');
	}




























	function admin()
	{
		//System Administrator Access
		if ($this->session->userdata('level') === 'System Administrator') {
			$id = $this->session->userdata('IDNumber');
			$year = date('Y');
			$result['data'] = $this->PersonnelModel->getLeaveRecords($id);
			$result['data1'] = $this->PersonnelModel->leaveForApproval();
			$result['data2'] = $this->PersonnelModel->forRetirement($year);
			$result['data3'] = $this->PersonnelModel->forLoyalty($year);
			$result['data4'] = $this->PersonnelModel->birthday();
			$result['data5'] = $this->SchoolsModel->schoolCountsMun();
			$result['data6'] = $this->SchoolsModel->schoolCountsType();
			$result['data7'] = $this->SchoolsModel->schoolCountsCongDistric();
			$result['data8'] = $this->PersonnelModel->personnelCounts();
			$result['data9'] = $this->PersonnelModel->departmentcounts();
			$this->load->view('dashboard_system_administrator', $result);
		} else {
			echo "Access Denied";
		}
	}

	function sgodDashboard()
	{
		//School Dashboard
		if ($this->session->userdata('level') === 'SGOD') {
			$year = date('Y');
			$result['data5'] = $this->SchoolsModel->schoolCountsMun();
			$result['data6'] = $this->SchoolsModel->schoolCountsType();
			$result['data7'] = $this->SchoolsModel->schoolCountsCongDistric();
			$this->load->view('dashboard_sgod', $result);
		} else {
			echo "Access Denied";
		}
	}




	function personnel()
	{
		//Allowing access to Personnel only
		if ($this->session->userdata('level') === 'Personnel') {
			$id = $this->session->userdata('IDNumber');
			$year = date('Y');
			$result['data'] = $this->PersonnelModel->getLeaveRecords($id);
			$this->load->view('dashboard_hr_personnel', $result);
		} else {
			echo "Access Denied";
		}
	}

	function school()
	{
		//System Administrator Access
		if ($this->session->userdata('level') === 'System Administrator') {
			$id = $this->session->userdata('IDNumber');
			$year = date('Y');
			$result['data'] = $this->PersonnelModel->getLeaveRecords($id);
			$result['data1'] = $this->PersonnelModel->leaveForApproval();
			$result['data2'] = $this->PersonnelModel->forRetirement($year);
			$result['data3'] = $this->PersonnelModel->forLoyalty($year);
			$result['data4'] = $this->PersonnelModel->birthday();
			$result['data5'] = $this->SchoolsModel->schoolCountsMun();
			$result['data6'] = $this->SchoolsModel->schoolCountsType();
			$result['data7'] = $this->SchoolsModel->schoolCountsCongDistric();
			$result['data8'] = $this->PersonnelModel->personnelCounts();
			$result['data9'] = $this->PersonnelModel->departmentcounts();
			$this->load->view('dashboard_schools', $result);
		} else {
			echo "Access Denied";
		}
	}


	function employeesleave()
	{
		$result['data'] = $this->PersonnelModel->getLeaveAll();
		$this->load->view('hr_employees_leave', $result);
	}







	function employees()
	{
		//Allowing access to HR admin only
		if ($this->session->userdata('level') === 'HR Admin') {
			$departmentVal = $this->input->post('department');
			$positionVal = $this->input->post('position');
			$result['data'] = $this->PersonnelModel->getFilteredPersonnel($departmentVal, $positionVal);
			$result['department'] = $this->PersonnelModel->getDepartment();
			$result['departmentVal'] = $departmentVal;
			$result['positionVal'] = $positionVal;
			$this->load->view('hr_personnel_by_dept', $result);
		} else {
			echo "Access Denied";
		}
	}

	function applicant()
	{
		//Allowing access to Applicant only
		if ($this->session->userdata('level') === 'Applicant') {
			$this->load->view('dashboard_view');
		} else {
			echo "Access Denied";
		}
	}

	function fetch_position()
	{

		if ($this->input->post('department')) {
			$output = '<option value="">Select Position</option>';
			$positions = $this->PersonnelModel->getPosition($this->input->post('department'));
			foreach ($positions as $row) {
				$output .= '<option value ="' . $row->empPosition . '">' . $row->empPosition . '</option>';
			}
			echo $output;
		}
	}


	function employeesummary()
	{
		$result['data'] = $this->PersonnelModel->personnelCounts();
		$this->load->view('hr_personnel_summary', $result);
	}

	function contactdirectory()
	{
		$result['data'] = $this->PersonnelModel->displaypersonnel();
		$this->load->view('hr_directory', $result);
	}

	function hrfiles201()
	{
		$result['data'] = $this->PersonnelModel->displaypersonnel();
		$this->load->view('hr_201files', $result);
	}

	function hr_files_individual()
	{
		// if($this->session->userdata('level')==='Personnel'){
		// $id=$this->session->userdata('username');

		// $result['data']=$this->PersonnelModel->viewfiles($id);
		// //$this->output->cache(3); 
		// $this->load->view('hr_files_individual',$result);
		// } else {
		// $id = $this->input->get('id');

		// $result['data'] = $this->PersonnelModel->viewfiles($id);
		// $this->load->view('hr_files_individual', $result);
		// }
	}


	function profile()
	{
		$id = $this->input->get('id');
		$result['data'] = $this->PersonnelModel->displaypersonnelById($id);
		$this->load->view('personnel_profile', $result);
	}

	function profilepage()
	{
		$id = $this->input->get('id');
		$result['data'] = $this->PersonnelModel->displaypersonnelById($id);
		$result['data1'] = $this->PersonnelModel->viewfiles($id);
		$result['data2'] = $this->PersonnelModel->family($id);
		$result['data3'] = $this->PersonnelModel->education($id);
		$result['data4'] = $this->PersonnelModel->cs($id);
		$result['data5'] = $this->PersonnelModel->personnelTrainings($id);
		$result['data6'] = $this->PersonnelModel->profilepic($id);
		$this->load->view('profile_page', $result);
	}

	//Display all personnel leave
	function leaveapplication()
	{

		$this->load->view('leave-application');
	}

	//Get Personal Leave
	function leaveHistory()
	{
		if (in_array($this->session->userdata('position'), ['Admin', 'Super Admin', 'HR Staff', 'Human Resource Admin'])):
			$id = $this->input->get('id');
		elseif ($this->session->userdata('position') === 'user') :
			$id = $this->session->userdata('username');
		endif;

		$result['data'] = $this->PersonnelModel->getLeaveLeaveHistory($id);
		$this->load->view('list_leave_history', $result);
	}

	//Get Personal Leave
	function leaveapplicationview()
	{
		$id = $this->session->userdata('IDNumber');
		$result['data'] = $this->PersonnelModel->getLeaveLeaveHistory($id);
		$this->load->view('leave-application-view', $result);
	}

	// public function pendingLeave()
	// {
	// 	// Get the logged-in user's position
	// 	$userPosition = $this->session->userdata('position');

	// 	// Query builder for fetching leave applications
	// 	$this->db->select('hris_staff.IDNumber, FirstName, MiddleName, LastName, appDate, leaveType, daysApplied, dateFrom, dateTo, leaveStatus, leaveID');
	// 	$this->db->from('hris_leave');
	// 	$this->db->join('hris_staff', 'hris_staff.IDNumber = hris_leave.IDNumber');

	// 	// Apply condition based on the user's position
	// 	if ($userPosition === "Human Resource Admin") {
	// 		$this->db->where('leaveStatus', 'For Approval');
	// 	}

	// 	$query = $this->db->get();
	// 	$result['data'] = $query->result();

	// 	$this->load->view('list_pendingLeave', $result);
	// }





	public function pendingLeave()
	{
		$username = $this->session->userdata('username');
		$userPosition = $this->session->userdata('position');

		$this->load->model('Page_model'); // Ensure model is loaded

		// 1. HR Admin
		if ($userPosition === "Human Resource Admin") {
			$this->db->select('hris_staff.IDNumber, FirstName, MiddleName, LastName, appDate, leaveType, daysApplied, dateFrom, dateTo, leaveStatus, leaveAttachment, leaveID');
			$this->db->from('hris_leave');
			$this->db->join('hris_staff', 'hris_staff.IDNumber = hris_leave.IDNumber');
			$this->db->where('hris_leave.leaveStatus', 'For Approval');

			$query = $this->db->get();
			$result['data'] = $query->result();

			$this->load->view('list_pendingLeave', $result);
			return;
		}

		// 2. Approver
		if ($this->Page_model->is_user_approver($username)) {
			$result['leaves'] = $this->Page_model->get_approved_leaves($username); // Fetch "Recommended" leaves
			$this->load->view('approver_leave_view', $result);
			return;
		}

		// 3. Endorser
		if ($this->Page_model->is_user_endorser($username)) {
			$data['leaves'] = $this->Page_model->get_endorsed_leaves($username); // Fetch "Evaluated" leaves
			$this->load->view('endorser_leave_view', $data);
			return;
		}

		// 4. Fallback (optional): Show blank view or 403
		show_error("You do not have permission to view pending leave requests.", 403);
	}


	// function pendingLeaveEvaluation()
	// {
	// 	// Fetch and display pending leave evaluation data
	// 	$data['data'] = $this->PersonnelModel->pendingLeaveEvaluation(
	// 		$this->input->get('empID'),
	// 		$this->input->get('leaveID')
	// 	);

	// 	// Initialize success message
	// 	$data['success_message'] = null;

	// 	// Process form submission
	// 	if ($this->input->post('submit')) {
	// 		// Set timezone and current date
	// 		date_default_timezone_set('Asia/Manila');
	// 		$today = date("Y-m-d");

	// 		// Retrieve form data
	// 		$leaveID = $this->input->post('leaveID');
	// 		$IDNumber = $this->input->post('IDNumber');
	// 		$daysApplied = $this->input->post('daysApplied');
	// 		$leaveType = $this->input->post('leaveType');

	// 		// Map leave type to corresponding field
	// 		$leaveFields = [
	// 			"Vacation Leave" => 'vlTotal',
	// 			"Sick Leave" => 'slTotal',
	// 			"Compensatory Leave" => 'cocTotal' // Default leave type
	// 		];
	// 		$leaveField = $leaveFields[$leaveType] ?? null;

	// 		if ($leaveField) {
	// 			// Update leave status and leave balance
	// 			$this->db->trans_start(); // Start transaction
	// 			$this->db->update('hris_leave', [
	// 				'leaveStatus' => 'Approved',
	// 				'dateApproved' => $today
	// 			], ['leaveID' => $leaveID]);

	// 			$this->db->set($leaveField, "$leaveField - $daysApplied", false)
	// 				->where('IDNumber', $IDNumber)
	// 				->update('hris_leaverecords');
	// 			$this->db->trans_complete(); // End transaction

	// 			// Set success message
	// 			if ($this->db->trans_status()) {
	// 				$data['success_message'] = 'The selected leave application has been processed successfully.';
	// 				redirect('Page/leaveCreditsSummary');
	// 			}
	// 		}
	// 	}

	// 	$this->load->view('list_pendingLeave_eval', $data);
	// }






	// public function pendingLeaveEvaluation()
	// {
	// 	// Fetch and display pending leave evaluation data
	// 	$data['data'] = $this->PersonnelModel->pendingLeaveEvaluation(
	// 		$this->input->get('empID'),
	// 		$this->input->get('leaveID')
	// 	);

	// 	// Initialize success message
	// 	$data['success_message'] = null;

	// 	// Process form submission
	// 	if ($this->input->post('submit')) {
	// 		// Set timezone and current date
	// 		date_default_timezone_set('Asia/Manila');
	// 		$today = date("Y-m-d");

	// 		// Retrieve form data
	// 		$leaveID = $this->input->post('leaveID');
	// 		$IDNumber = $this->input->post('IDNumber');
	// 		$daysApplied = (int) $this->input->post('daysApplied'); // Ensure integer
	// 		$leaveType = $this->input->post('leaveType');

	// 		// Determine the correct leave field to update
	// 		if ($leaveType === "Sick Leave") {
	// 			$leaveField = 'slTotal';
	// 		} elseif ($leaveType === "Compensatory Leave") {
	// 			$leaveField = 'cocTotal';
	// 		} else {
	// 			$leaveField = 'vlTotal'; // Default to Vacation Leave
	// 		}

	// 		// Start transaction
	// 		$this->db->trans_start();

	// 		// Update leave status
	// 		$this->db->where('leaveID', $leaveID)
	// 			->update('hris_leave', [
	// 				'leaveStatus' => 'Approved',
	// 				'dateApproved' => $today
	// 			]);

	// 		// Deduct leave days from the correct leave balance
	// 		$this->db->set($leaveField, "$leaveField - $daysApplied", false)
	// 			->where('IDNumber', $IDNumber)
	// 			->update('hris_leaverecords');

	// 		// Complete transaction
	// 		$this->db->trans_complete();

	// 		// Set success message if transaction was successful
	// 		if ($this->db->trans_status()) {
	// 			$data['success_message'] = 'The selected leave application has been processed successfully.';
	// 			redirect('Page/leaveCreditsSummary');
	// 		}
	// 	}

	// 	// Load the view
	// 	$this->load->view('list_pendingLeave_eval', $data);
	// }










	// public function pendingLeaveEvaluation()
	// {
	// 	// Fetch and display pending leave evaluation data
	// 	$data['data'] = $this->PersonnelModel->pendingLeaveEvaluation(
	// 		$this->input->get('empID'),
	// 		$this->input->get('leaveID')
	// 	);

	// 	// Get the logged-in user's position and name
	// 	$userPosition = $this->session->userdata('position');
	// 	$responsibleUser = $this->session->userdata('username'); // Assuming username is stored in session

	// 	// Process form submission
	// 	if ($this->input->post('submit')) {
	// 		// Set timezone and current date
	// 		date_default_timezone_set('Asia/Manila');
	// 		$today = date("Y-m-d");

	// 		// Retrieve form data
	// 		$leaveID = $this->input->post('leaveID');
	// 		$IDNumber = $this->input->post('IDNumber');
	// 		$daysApplied = (int) $this->input->post('daysApplied'); // Ensure integer
	// 		$leaveType = $this->input->post('leaveType');

	// 		// Determine the correct leave field to update
	// 		if ($leaveType === "Sick Leave") {
	// 			$leaveField = 'slTotal';
	// 		} elseif ($leaveType === "Compensatory Leave") {
	// 			$leaveField = 'cocTotal';
	// 		} else {
	// 			$leaveField = 'vlTotal'; // Default to Vacation Leave
	// 		}

	// 		// Start transaction
	// 		$this->db->trans_start();

	// 		if ($userPosition === "Human Resource Admin") {
	// 			// If HR Admin, mark as "Evaluated" but do not deduct leave
	// 			$this->db->where('leaveID', $leaveID)
	// 				->update('hris_leave', [
	// 					'leaveStatus' => 'Evaluated',
	// 					'dateApproved' => $today
	// 				]);

	// 			// Save tracking record
	// 			$this->db->insert('hris_leave_tracking', [
	// 				'leaveID' => $leaveID,
	// 				'IDNumber' => $IDNumber,
	// 				'actionTaken' => 'Evaluated',
	// 				'responsibleUser' => $responsibleUser,
	// 				'position' => $userPosition
	// 			]);
	// 		}
	// 		elseif ($userPosition === "Endorser") {
	// 			$this->db->where('leaveID', $leaveID)
	// 				->update('hris_leave', [
	// 					'leaveStatus' => 'Recommended',
	// 					'dateApproved' => $today
	// 				]);
	// 			// Save tracking record
	// 			$this->db->insert('hris_leave_tracking', [
	// 				'leaveID' => $leaveID,
	// 				'IDNumber' => $IDNumber,
	// 				'actionTaken' => 'Recommended',
	// 				'responsibleUser' => $responsibleUser,
	// 				'position' => $userPosition
	// 			]);
	// 		}

	// 		elseif ($userPosition === "asds") {
	// 			// If "asds", approve leave and deduct leave balance
	// 			$this->db->where('leaveID', $leaveID)
	// 				->update('hris_leave', [
	// 					'leaveStatus' => 'Approved',
	// 					'dateApproved' => $today
	// 				]);

	// 			// Deduct leave days from the correct leave balance
	// 			$this->db->set($leaveField, "$leaveField - $daysApplied", false)
	// 				->where('IDNumber', $IDNumber)
	// 				->update('hris_leaverecords');

	// 			// Save tracking record
	// 			$this->db->insert('hris_leave_tracking', [
	// 				'leaveID' => $leaveID,
	// 				'IDNumber' => $IDNumber,
	// 				'actionTaken' => 'Approved',
	// 				'responsibleUser' => $responsibleUser,
	// 				'position' => $userPosition
	// 			]);
	// 		}

	// 		// Complete transaction
	// 		$this->db->trans_complete();

	// 		// Set success message if transaction was successful
	// 		if ($this->db->trans_status()) {
	// 			$this->session->set_flashdata('success', 'The selected leave application has been processed successfully.');
	// 		} else {
	// 			$this->session->set_flashdata('danger', 'An error occurred while processing the leave application.');
	// 		}

	// 		// Redirect based on user position
	// 		if ($userPosition === "asds") {
	// 			redirect('Page/leaveCreditsSummary');
	// 		} else {
	// 			redirect('Page/pendingLeave');
	// 		}
	// 	}

	// 	// Load the view
	// 	$this->load->view('list_pendingLeave_eval', $data);
	// }




	public function pendingLeaveEvaluation()
	{
		$this->load->model('PersonnelModel');

		// Fetch leave data for evaluation
		$data['data'] = $this->PersonnelModel->pendingLeaveEvaluation(
			$this->input->get('empID'),
			$this->input->get('leaveID')
		);

		// Get current user info
		$userPosition = $this->session->userdata('position');
		$responsibleUser = $this->session->userdata('username');

		// Determine if user is an endorser
		$CI = &get_instance();
		$CI->load->database();
		$isEndorser = $CI->db->where('endorser', $responsibleUser)
			->from('epmloyee_superior')
			->count_all_results();

		// Determine if user is an endorser
		$isApprover = $CI->db->where('approver', $responsibleUser)
			->from('epmloyee_superior')
			->count_all_results();

		// Process form submission
		if ($this->input->post('submit')) {
			date_default_timezone_set('Asia/Manila');
			$today = date("Y-m-d");

			$leaveID = $this->input->post('leaveID');
			$IDNumber = $this->input->post('IDNumber');
			$daysApplied = (int) $this->input->post('daysApplied');
			$leaveType = $this->input->post('leaveType');

			// Determine leave type field
			if ($leaveType === "Sick Leave") {
				$leaveField = 'slTotal';
			} elseif ($leaveType === "Compensatory Leave") {
				$leaveField = 'cocTotal';
			} else {
				$leaveField = 'vlTotal';
			}

			$this->db->trans_start();

			if ($userPosition === "Human Resource Admin") {
				// HR: Mark as evaluated
				$this->db->where('leaveID', $leaveID)
					->update('hris_leave', [
						'leaveStatus' => 'Evaluated'
					]);

				$this->db->insert('hris_leave_tracking', [
					'leaveID' => $leaveID,
					'IDNumber' => $IDNumber,
					'actionTaken' => 'Evaluated',
					'responsibleUser' => $responsibleUser,
					'position' => $userPosition
				]);
			} elseif ($isApprover > 0) {
				// ASDS: Approve leave and handle deduction
				date_default_timezone_set('Asia/Manila');
				$currentYear = date('Y');

				// Approve the leave first
				$this->db->where('leaveID', $leaveID)
					->update('hris_leave', [
						'leaveStatus' => 'Approved',
						'dateApproved' => $today
					]);

				// Handle deduction based on leaveType
				if ($leaveType === 'Special Privilege Leave') {
					// Deduct only if applied days exceed 3
					if ($daysApplied > 3) {
						$deductDays = $daysApplied - 3;
						$this->db->set($leaveField, "$leaveField - $deductDays", false)
							->where('IDNumber', $IDNumber)
							->update('hris_leaverecords');
					}
					// No deduction if 3 or less

				} elseif ($leaveType === 'Maternity Leave') {
					// Deduct only if applied days exceed 105
					if ($daysApplied > 105) {
						$deductDays = $daysApplied - 105;
						$this->db->set($leaveField, "$leaveField - $deductDays", false)
							->where('IDNumber', $IDNumber)
							->update('hris_leaverecords');
					}
				} elseif ($leaveType === 'Paternity Leave') {
					// Deduct only if applied days exceed 105
					if ($daysApplied > 7) {
						$deductDays = $daysApplied - 7;
						$this->db->set($leaveField, "$leaveField - $deductDays", false)
							->where('IDNumber', $IDNumber)
							->update('hris_leaverecords');
					}
				} elseif ($leaveType === 'Solo Parent Leave') {
					// Deduct only if applied days exceed 105
					if ($daysApplied > 7) {
						$deductDays = $daysApplied - 7;
						$this->db->set($leaveField, "$leaveField - $deductDays", false)
							->where('IDNumber', $IDNumber)
							->update('hris_leaverecords');
					}
				} elseif ($leaveType === 'Study Leave') {
					// Deduct only if applied days exceed 105
					if ($daysApplied > 180) {
						$deductDays = $daysApplied - 180;
						$this->db->set($leaveField, "$leaveField - $deductDays", false)
							->where('IDNumber', $IDNumber)
							->update('hris_leaverecords');
					}
				} elseif ($leaveType === '10-Day VAWC Leave') {
					// Deduct only if applied days exceed 105
					if ($daysApplied > 10) {
						$deductDays = $daysApplied - 10;
						$this->db->set($leaveField, "$leaveField - $deductDays", false)
							->where('IDNumber', $IDNumber)
							->update('hris_leaverecords');
					}
				} elseif ($leaveType === 'Rehabilitation Privilege') {
					// Deduct only if applied days exceed 105
					if ($daysApplied > 80) {
						$deductDays = $daysApplied - 80;
						$this->db->set($leaveField, "$leaveField - $deductDays", false)
							->where('IDNumber', $IDNumber)
							->update('hris_leaverecords');
					}
				} elseif ($leaveType === 'Mandatory/Forced Leave') {
					// Check total approved Mandatory/Forced Leave this year
					$this->db->select_sum('daysApplied');
					$this->db->from('hris_leave');
					$this->db->where('IDNumber', $IDNumber);
					$this->db->where('leaveType', 'Mandatory/Forced Leave');
					$this->db->where('leaveStatus', 'Approved');
					$this->db->where('YEAR(dateApproved)', $currentYear);
					$this->db->where('dateApproved IS NOT NULL', null, false);
					$this->db->where('leaveID !=', $leaveID); // <-- Exclude the current application

					$query = $this->db->get();
					$totalApprovedDays = (int) $query->row()->daysApplied;
					$newTotal = $totalApprovedDays + $daysApplied;

					if ($newTotal > 1000000) {
						$this->session->set_flashdata('danger', 'Cannot approve: Employee would exceed the maximum 5 days of Mandatory/Forced Leave for this year.');
						redirect('Page/pendingLeave');
						return;
					}

					// Deduct full applied days if within limit
					$this->db->set($leaveField, "$leaveField - $daysApplied", false)
						->where('IDNumber', $IDNumber)
						->update('hris_leaverecords');
				} else {
					// Regular deduction for other leave types
					$this->db->set($leaveField, "$leaveField - $daysApplied", false)
						->where('IDNumber', $IDNumber)
						->update('hris_leaverecords');
				}

				// Log action
				$this->db->insert('hris_leave_tracking', [
					'leaveID' => $leaveID,
					'IDNumber' => $IDNumber,
					'actionTaken' => 'Approved',
					'responsibleUser' => $responsibleUser,
					'position' => $userPosition
				]);
			} elseif ($isEndorser > 0) {
				// Identified as Endorser based on database, not session
				$this->db->where('leaveID', $leaveID)
					->update('hris_leave', [
						'leaveStatus' => 'Recommended'
					]);

				$this->db->insert('hris_leave_tracking', [
					'leaveID' => $leaveID,
					'IDNumber' => $IDNumber,
					'actionTaken' => 'Recommended',
					'responsibleUser' => $responsibleUser,
					'position' => 'Endorser'
				]);
			}

			$this->db->trans_complete();

			if ($this->db->trans_status()) {
				$this->session->set_flashdata('success', 'The selected leave application has been processed successfully.');
			} else {
				$this->session->set_flashdata('danger', 'An error occurred while processing the leave application.');
			}

			// Redirect based on user
			if ($isApprover > 0) {
				redirect('Page/leaveCreditsSummary');
			}

			if ($isEndorser > 0) {
				redirect('Pages/view_employee');
			} else {
				redirect('Page/pendingLeave');
			}
		}

		$this->load->view('list_pendingLeave_eval', $data);
	}














	// 	function pendingLeaveEvaluation()
	// {
	//     // Fetch and display pending leave evaluation data
	//     $data['data'] = $this->PersonnelModel->pendingLeaveEvaluation(
	//         $this->input->get('empID'), 
	//         $this->input->get('leaveID')
	//     );
	//     $this->load->view('list_pendingLeave_eval', $data);

	//     // Process form submission
	//     if ($this->input->post('submit')) {
	//         // Set timezone and current date
	//         date_default_timezone_set('Asia/Manila');
	//         $today = date("Y-m-d");

	//         // Retrieve form data
	//         $leaveID = $this->input->post('leaveID');
	//         $IDNumber = $this->input->post('IDNumber');
	//         $daysApplied = $this->input->post('daysApplied');
	//         $leaveType = $this->input->post('leaveType');

	//         // Map leave type to corresponding field
	//         $leaveFields = [
	//             "Vacation Leave" => 'vlTotal',
	//             "Sick Leave" => 'slTotal',
	//             "Compensatory Leave" => 'cocTotal' // Default leave type
	//         ];
	//         $leaveField = $leaveFields[$leaveType] ?? null;

	//         if ($leaveField) {
	//             // Update leave status and leave balance
	//             $this->db->trans_start(); // Start transaction
	//             $this->db->update('hris_leave', [
	//                 'leaveStatus' => 'Approved',
	//                 'dateApproved' => $today
	//             ], ['leaveID' => $leaveID]);

	//             $this->db->set($leaveField, "$leaveField - $daysApplied", false)
	//                      ->where('IDNumber', $IDNumber)
	//                      ->update('hris_leaverecords');
	//             $this->db->trans_complete(); // End transaction

	//             // Set success message
	//             if ($this->db->trans_status()) {
	//                 $this->session->set_flashdata('success', 'The selected leave application has been processed successfully.');
	//             }
	//         }
	//     }
	//}



	//List of Birthday Celebrants
	function birthdayCelebrants()
	{
		$result['data'] = $this->PersonnelModel->birthdayList();
		$this->load->view('hr_bday_celebrants', $result);
	}
	//For Loyalty Cash Award
	function forLoyaltyList()
	{
		//$year=date('Y');
		$result['data'] = $this->PersonnelModel->forLoyaltyList();
		$this->load->view('hr_loyalty_cash', $result);
	}

	//For Retirement
	function forRetirementList()
	{
		//$year=date('Y');
		$result['data'] = $this->PersonnelModel->forRetirementList();
		$this->load->view('hr_retirement_list', $result);
	}

	function leavecreditsview()
	{
		$id = $this->input->get('id');
		$result['data'] = $this->PersonnelModel->displayleavecredits($id);
		$this->load->view('leave-credits-view', $result);
	}

	function att()
	{
		$this->load->view('authority-to-travel');
	}

	function trainings()
	{
		$this->load->view('trainings');
	}

	function accomplishments()
	{
		$this->load->view('accomplishments');
	}

	function speakership()
	{
		$this->load->view('speakership-summary');
	}

	function pds()
	{
		$this->load->view('pds/pds');
	}

	//Change Password
	function changepassword()
	{
		$this->load->view('change_pass');
	}



	function update_password()
	{

		$this->form_validation->set_rules('currentpassword', 'Current Password', 'required|trim|callback__validate_currentpassword');
		$this->form_validation->set_rules('newpassword', 'New Password', 'required|trim|min_length[8]|alpha_numeric');
		$this->form_validation->set_rules('cnewpassword', 'Confirm New Password', 'required|trim|matches[newpassword]');

		$this->form_validation->set_message('required', "Please fill-up the form completely!");
		if ($this->form_validation->run()) {

			$username = $this->session->userdata('username');
			$newpass = sha1($this->input->post('newpassword'));
			if ($this->PersonnelModel->reset_userpassword($username, $newpass)) {

				$this->session->set_flashdata('success', 'You have changed your password succesfully!');
				$this->load->view('change_pass');
			} else {
				echo "Error";
			}
		} else {
			$this->session->set_flashdata('msg', '');
			$this->load->view('change_pass');
		}
	}

	function _validate_currentpassword()
	{
		$username = $this->session->userdata('username');
		$currentpass = sha1($this->input->post('currentpassword'));
		if ($this->PersonnelModel->is_current_password($username, $currentpass)) {
			return TRUE;
		} else {
			$this->form_validation->set_message('_validate_currentpassword', 'Wrong Current Password');
			return FALSE;
		}
	}

	//applicants pages
	function applicantsprofile()
	{
		$id = $this->input->get('id');
		$result['data'] = $this->ApplicantsModel->displayrecordsById($id);
		$this->load->view('applicants_profile', $result);
	}

	function applicantsrating()
	{
		$this->load->view('applicants_rating');
	}

	//Uploading of Training and Speakership
	function uploadFiles()
	{
		$id = $this->session->userdata('username');
		$result['data'] = $this->PersonnelModel->personnelTrainings($id);
		$this->load->view('hr_personnel_upload', $result);
	}

	//Processing of Training and Speakership
	public function processUpload()
	{
		$config['upload_path'] = './upload/trainings/';
		$config['allowed_types'] = 'pdf';
		$config['max_size'] = 15120;
		//$config['max_width'] = 1500;
		//$config['max_height'] = 1500;

		$this->load->library('upload', $config);

		if (!$this->upload->do_upload('nonoy')) {
			$msg = array('error' => $this->upload->display_errors());

			$this->load->view('uploadFiles', $msg);
		} else {
			$data = array('image_metadata' => $this->upload->data());
			//get data from the form
			$IDNumber = $this->session->userdata('username');
			$filename = $this->upload->data('file_name');
			$trainingTitle = $this->input->post('trainingTitle');
			$dateStarted = $this->input->post('dateStarted');
			$dateFinished = $this->input->post('dateFinished');
			$noHours = $this->input->post('noHours');
			$IdType = $this->input->post('IdType');
			$sponsor = $this->input->post('sponsor');

			$que = $this->db->query("insert into hris_trainings values('','$trainingTitle','$dateStarted','$dateFinished','$noHours','$IdType','$sponsor','$IDNumber','$filename')");
			$this->session->set_flashdata('msg', '<div class="alert alert-success text-center"><b>Recorded Succesfully!</b></div>');

			redirect('Page/uploadFiles');
		}
	}
	//Uploading of Personnel 201 Files
	function uploadFiles201()
	{
		$id = $this->session->userdata('username');
		$result['data'] = $this->PersonnelModel->view201Files($id);
		$this->load->view('hr_personnel_upload_201', $result);
	}

	//Processing of Training and Speakership
	public function processuploadFiles201()
	{
		$config['upload_path'] = './upload/files/';
		$config['allowed_types'] = 'pdf';
		$config['max_size'] = 15120;
		//$config['max_width'] = 1500;
		//$config['max_height'] = 1500;

		$this->load->library('upload', $config);

		if (!$this->upload->do_upload('nonoy')) {
			$msg = array('error' => $this->upload->display_errors());

			$this->load->view('uploadFiles', $msg);
		} else {
			$data = array('image_metadata' => $this->upload->data());
			//get data from the form
			$IDNumber = $this->session->userdata('username');
			$filename = $this->upload->data('file_name');
			$docName = $this->input->post('docName');

			date_default_timezone_set('Asia/Manila'); # add your city to set local time zone
			$dateUploaded = date("Y-m-d");

			$que = $this->db->query("insert into hris_files values('','$IDNumber','$docName','$filename','$dateUploaded')");
			$this->session->set_flashdata('msg', '<div class="alert alert-success text-center"><b>Recorded Succesfully!</b></div>');

			redirect('Page/uploadFiles201');
		}
	}
	//Uploading of Training and Speakership
	function expertServices()
	{
		$id = $this->session->userdata('username');
		$result['data'] = $this->PersonnelModel->personnelServices($id);
		$this->load->view('hr_personnel_expert_services', $result);
	}

	//Processing of Training and Speakership
	public function processExpertServices()
	{
		$config['upload_path'] = './upload/expert-services/';
		$config['allowed_types'] = 'pdf';
		$config['max_size'] = 15120;
		//$config['max_width'] = 1500;
		//$config['max_height'] = 1500;

		$this->load->library('upload', $config);

		if (!$this->upload->do_upload('nonoy')) {
			$msg = array('error' => $this->upload->display_errors());

			$this->load->view('uploadFiles', $msg);
		} else {
			$data = array('image_metadata' => $this->upload->data());
			//get data from the form
			$IDNumber = $this->session->userdata('username');
			$filename = $this->upload->data('file_name');
			$event = $this->input->post('event');
			$dateStarted = $this->input->post('dateStarted');
			$dateFinished = $this->input->post('dateFinished');

			$que = $this->db->query("insert into hris_expert values('','$event','$dateStarted','$dateFinished','$IDNumber','$filename')");
			$this->session->set_flashdata('msg', '<div class="alert alert-success text-center"><b>Recorded Succesfully!</b></div>');

			redirect('Page/expertServices');
		}
	}
	//Uploading of Achievement
	function uploadAchievements()
	{
		$id = $this->session->userdata('username');
		$result['data'] = $this->PersonnelModel->personnelAchievements($id);
		$this->load->view('hr_personnel_achievements', $result);
	}

	//Processing of Training and Speakership
	public function processuploadAchievements()
	{
		$config['upload_path'] = './upload/achievements/';
		$config['allowed_types'] = 'pdf';
		$config['max_size'] = 15120;
		//$config['max_width'] = 1500;
		//$config['max_height'] = 1500;

		$this->load->library('upload', $config);

		if (!$this->upload->do_upload('nonoy')) {
			$msg = array('error' => $this->upload->display_errors());

			$this->load->view('uploadFiles', $msg);
		} else {
			$data = array('image_metadata' => $this->upload->data());
			//get data from the form
			$IDNumber = $this->session->userdata('username');
			$filename = $this->upload->data('file_name');
			$achievement = $this->input->post('achievement');
			$dateAchieved = $this->input->post('dateAchieved');

			$que = $this->db->query("insert into hris_achievements values('','$achievement','$dateAchieved','$IDNumber','$filename')");
			$this->session->set_flashdata('msg', '<div class="alert alert-success text-center"><b>Recorded Succesfully!</b></div>');

			redirect('Page/uploadAchievements');
		}
	}

	//Uploading of Achievement
	function uploadOtherDocs()
	{
		$id = $this->session->userdata('username');
		$result['data'] = $this->PersonnelModel->personnelOtherDocs($id);
		$this->load->view('hr_personnel_upload_docs', $result);
	}

	//Processing of Training and Speakership
	public function processuploadOtherDocs()
	{
		$config['upload_path'] = './upload/files/';
		$config['allowed_types'] = '*';
		$config['max_size'] = 15120;
		//$config['max_width'] = 1500;
		//$config['max_height'] = 1500;

		$this->load->library('upload', $config);

		if (!$this->upload->do_upload('nonoy')) {
			$msg = array('error' => $this->upload->display_errors());

			$this->load->view('uploadFiles', $msg);
		} else {
			$data = array('image_metadata' => $this->upload->data());
			//get data from the form
			$IDNumber = $this->session->userdata('username');
			$filename = $this->upload->data('file_name');
			$docName = $this->input->post('docName');
			$fileType = $this->input->post('fileType');
			$dateUploaded = $this->input->post('dateUploaded');

			$que = $this->db->query("insert into hris_otherdocs values('','$docName','$fileType','$dateUploaded','$IDNumber','$filename')");
			$this->session->set_flashdata('msg', '<div class="alert alert-success text-center"><b>Recorded Succesfully!</b></div>');

			redirect('Page/uploadOtherDocs');
		}
	}

	function personnelEntry()
	{
		$this->load->view('hr_personnel_profile_form');
		if ($this->input->post('submit')) {
			//get data from the form
			$prefix = $this->input->post('prefix');
			$IDNumber = $this->input->post('IDNumber');
			$FirstName = strtoupper($this->input->post('FirstName'));
			$MiddleName = strtoupper($this->input->post('MiddleName'));
			$LastName = strtoupper($this->input->post('LastName'));
			$NameExtn = $this->input->post('NameExtn');
			$completeName = $FirstName . ' ' . $LastName;
			$Sex = $this->input->post('Sex');
			$BirthDate = $this->input->post('BirthDate');
			$age = $this->input->post('age');
			$BirthPlace = $this->input->post('BirthPlace');
			$MaritalStatus = $this->input->post('MaritalStatus');
			$height = $this->input->post('height');
			$weight = $this->input->post('weight');
			$bloodType = $this->input->post('bloodType');
			$empTelNo = $this->input->post('empTelNo');
			$empMobile = $this->input->post('empMobile');
			$empEmail = $this->input->post('empEmail');
			$fb = $this->input->post('fb');
			$skype = $this->input->post('skype');
			$citizenship = $this->input->post('citizenship');
			$dualCitizenship = $this->input->post('dualCitizenship');
			$citizenshipType = $this->input->post('citizenshipType');
			$citizenshipCountry = $this->input->post('citizenshipCountry');
			$empPosition = $this->input->post('empPosition');
			$Department = $this->input->post('Department');
			$empStatus = $this->input->post('empStatus');
			$agencyCode = $this->input->post('agencyCode');
			$dateHired = $this->input->post('dateHired');
			$retYear = $this->input->post('retYear');
			$gsis = $this->input->post('gsis');
			$pagibig = $this->input->post('pagibig');
			$philHealth = $this->input->post('philHealth');
			$sssNo = $this->input->post('sssNo');
			$tinNo = $this->input->post('tinNo');
			$contactName = $this->input->post('contactName');
			$contactRel = $this->input->post('contactRel');
			$contactEmail = $this->input->post('contactEmail');
			$contactNo = $this->input->post('contactNo');
			$contactAddress = $this->input->post('contactAddress');
			$resHouseNo = $this->input->post('resHouseNo');
			$resStreet = $this->input->post('resStreet');
			$resVillage = $this->input->post('resVillage');
			$resBarangay = $this->input->post('resBarangay');
			$resZipCode = $this->input->post('resZipCode');
			$resCity = $this->input->post('resCity');
			$resProvince = $this->input->post('resProvince');
			$stationCode = $this->input->post('stationCode');
			$umid = $this->input->post('umid');
			$csEligibility = $this->input->post('csEligibility');
			$csLevel = $this->input->post('csLevel');
			$currentStatus = $this->input->post('currentStatus');
			$YearsAsJO = $this->input->post('YearsAsJO');
			$workNature1 = $this->input->post('workNature1');
			$workNature2 = $this->input->post('workNature2');
			$employeeNo = $this->input->post('employeeNo');
			$currentItemNo = $this->input->post('currentItemNo');
			$sgNo = $this->input->post('sgNo');
			$authAnSalary = $this->input->post('authAnSalary');
			$actualSalary = $this->input->post('actualSalary');
			$stepNo = $this->input->post('stepNo');
			$origAppointmentDate = $this->input->post('origAppointmentDate');
			$lastAppointmentDate = $this->input->post('lastAppointmentDate');


			date_default_timezone_set('Asia/Manila'); # add your city to set local time zone
			$now = date('H:i:s A');
			$date = date("Y-m-d");
			$Password = sha1($this->input->post('BirthDate'));
			$Encoder = $this->session->userdata('username');

			//check if record exist
			$que = $this->db->query("select * from hris_staff where IDNumber='" . $IDNumber . "'");
			$row = $que->num_rows();
			if ($row) {
				//redirect('Page/notification_error');
				$this->session->set_flashdata('msg', '<div class="alert alert-danger text-center"><b>Employee Number is in use.</b></div>');
				redirect('Page/personnelEntry');
			} else {
				//save profile
				$que = $this->db->query("insert into hris_staff values('$IDNumber','$FirstName','$MiddleName','$LastName','$NameExtn','$prefix','','$empPosition','$Department','$MaritalStatus','$empStatus','$BirthDate','$BirthPlace','$Sex','$height','$weight','$bloodType','$gsis','$pagibig','$philHealth','$sssNo','$tinNo','$resHouseNo','$resStreet','$resVillage','$resBarangay','$resCity','$resProvince','$resZipCode','$resHouseNo','$resStreet','$resVillage','$resBarangay','$resCity','$resProvince','$resZipCode','$empTelNo','$empMobile','$empEmail','1','','','$age','$dateHired','','$retYear','$agencyCode','$citizenship','$dualCitizenship','$citizenshipType','$citizenshipCountry','$contactName','$contactRel','$contactEmail','$contactNo','$contactAddress','$fb','$skype','$stationCode','$umid','$csEligibility','$csLevel','$currentStatus','$YearsAsJO','$workNature1','$workNature2','$employeeNo','$currentItemNo','$sgNo','$authAnSalary','$actualSalary','$stepNo','$dateHired','$lastAppointmentDate')");
				$que = $this->db->query("insert into users values('$empEmail','$Password','$FirstName','$MiddleName','$LastName','$NameExtn','$empPosition','$date','avatar.png','Personnel','$empEmail','$IDNumber','active')");
				$que = $this->db->query("insert into atrail values('','Created Personnel Profile and User Account','$date','$now','$Encoder','$IDNumber')");
				$this->session->set_flashdata('msg', '<div class="alert alert-success text-center"><h4 class="m-t-0 header-title mb-4">Profile has been saved successfully.</h4></div>');

				//Email Notification
				$this->load->config('email');
				$this->load->library('email');
				$mail_message = 'Dear ' . $FirstName . ',' . "\r\n";
				$mail_message .= '<br><br>Your profile is now encoded to DepEd MIS. Please take note of the following:' . "\r\n";
				$mail_message .= '<br>Username: <b>' . $empEmail . '</b>' . "\r\n";
				$mail_message .= '<br>Password: <b>' . $BirthDate . '</b>' . "\r\n";

				$mail_message .= '<br><br>Thanks & Regards,';
				$mail_message .= '<br>DepEd MIS Team';

				$this->email->from('no-reply@lxeinfotechsolutions.com', 'DepEd MIS Team')
					->to($empEmail)
					->subject('Account Created')
					->message($mail_message);
				$this->email->send();

				redirect('Page/employeelist');
			}
		}
	}

	//Update Personnel Profile
	function updatePersonnelProfile()
	{
		if ($this->session->userdata('level') === 'System Administrator') {
			$id = $this->input->get('id');
		} elseif ($this->session->userdata('level') === 'School') {
			$id = $this->input->get('id');
		} elseif ($this->session->userdata('level') === 'HR Admin') {
			$id = $this->input->get('id');
		} else {
			$id = $this->session->userdata('IDNumber');
		}

		$result['data'] = $this->PersonnelModel->staffProfile($id);
		$this->load->view('hr_personnel_profile_update_form', $result);
		if ($this->input->post('submit')) {
			//get data from the form
			$OldIDNumber = $this->input->post('OldIDNumber');
			$prefix = $this->input->post('prefix');
			$IDNumber = $this->input->post('IDNumber');
			$FirstName = strtoupper($this->input->post('FirstName'));
			$MiddleName = strtoupper($this->input->post('MiddleName'));
			$LastName = strtoupper($this->input->post('LastName'));
			$NameExtn = $this->input->post('NameExtn');
			$completeName = $FirstName . ' ' . $LastName;
			$Sex = $this->input->post('Sex');
			$BirthDate = $this->input->post('BirthDate');
			$age = $this->input->post('age');
			$BirthPlace = $this->input->post('BirthPlace');
			$MaritalStatus = $this->input->post('MaritalStatus');
			$height = $this->input->post('height');
			$weight = $this->input->post('weight');
			$bloodType = $this->input->post('bloodType');
			$empTelNo = $this->input->post('empTelNo');
			$empMobile = $this->input->post('empMobile');
			$empEmail = $this->input->post('empEmail');
			$fb = $this->input->post('fb');
			$skype = $this->input->post('skype');
			$citizenship = $this->input->post('citizenship');
			$dualCitizenship = $this->input->post('dualCitizenship');
			$citizenshipType = $this->input->post('citizenshipType');
			$citizenshipCountry = $this->input->post('citizenshipCountry');
			$empPosition = $this->input->post('empPosition');
			$Department = $this->input->post('Department');
			$empStatus = $this->input->post('empStatus');
			$agencyCode = $this->input->post('agencyCode');
			$dateHired = $this->input->post('dateHired');
			$retYear = $this->input->post('retYear');
			$gsis = $this->input->post('gsis');
			$pagibig = $this->input->post('pagibig');
			$philHealth = $this->input->post('philHealth');
			$sssNo = $this->input->post('sssNo');
			$tinNo = $this->input->post('tinNo');
			$contactName = $this->input->post('contactName');
			$contactRel = $this->input->post('contactRel');
			$contactEmail = $this->input->post('contactEmail');
			$contactNo = $this->input->post('contactNo');
			$contactAddress = $this->input->post('contactAddress');
			$resHouseNo = $this->input->post('resHouseNo');
			$resStreet = $this->input->post('resStreet');
			$resVillage = $this->input->post('resVillage');
			$resBarangay = $this->input->post('resBarangay');
			$resZipCode = $this->input->post('resZipCode');
			$resCity = $this->input->post('resCity');
			$resProvince = $this->input->post('resProvince');

			$stationCode = $this->input->post('stationCode');

			date_default_timezone_set('Asia/Manila'); # add your city to set local time zone
			$now = date('H:i:s A');
			$date = date("Y-m-d");
			$Password = sha1($this->input->post('BirthDate'));
			$Encoder = $this->session->userdata('username');
			//$description='Updated Personnel Information.  Old ID Number: '.$OldIDNumber.' '.' New IDNumber: '.$IDNumber.; 

			//save profile
			$que = $this->db->query("update hris_staff set IDNumber='$IDNumber',FirstName='$FirstName',MiddleName='$MiddleName',LastName='$LastName',NameExtn='$NameExtn',prefix='$prefix',empPosition='$empPosition',Department='$Department',MaritalStatus='$MaritalStatus',empStatus='$empStatus',BirthDate='$BirthDate',BirthPlace='$BirthPlace',Sex='$Sex',height='$height',weight='$weight',bloodType='$bloodType',gsis='$gsis',pagibig='$pagibig',philHealth='$philHealth',sssNo='$sssNo',tinNo='$tinNo',resHouseNo='$resHouseNo',resStreet='$resStreet',resVillage='$resVillage',resBarangay='$resBarangay',resCity='$resCity',resProvince='$resProvince',resZipCode='$resZipCode',perHouseNo='$resHouseNo',perStreet='$resStreet',perVillage='$resVillage',perBarangay='$resBarangay',perCity='$resCity',perProvince='$resProvince',perZipCode='$resZipCode',empTelNo='$empTelNo',empMobile='$empMobile',empEmail='$empEmail',age='$age',dateHired='$dateHired',retYear='$retYear',agencyCode='$agencyCode',citizenship='$citizenship',dualCitizenship='$dualCitizenship',citizenshipType='$citizenshipType',citizenshipCountry='$citizenshipCountry',contactName='$contactName',contactRel='$contactRel',contactEmail='$contactEmail',contactNo='$contactNo',contactAddress='$contactAddress',fb='$fb',skype='$skype',stationCode='$stationCode' where IDNumber='$OldIDNumber'");
			$que1 = $this->db->query("update users set fName='$FirstName',mName='$MiddleName',lName='$LastName',email='$empEmail',IDNumber='$IDNumber' where IDNumber='$OldIDNumber'");
			$que2 = $this->db->query("insert into atrail values('','Updated Personnel Profile','$date','$now','$Encoder','$OldIDNumber')");
			$this->session->set_flashdata('msg', '<div id="sa-success" class="alert alert-success text-center"><b>Updated successfully.</b></div>');
			if ($this->session->userdata('level') === 'System Administrator') :
				redirect('Page/personnelEntry');
			elseif ($this->session->userdata('level') === 'School') :
				redirect('Schools/schoolDashboard');
			endif;
		}
	}

	public function myApplications()
	{
		// Default so $applicantEmail is always defined even when the logged-in
		// position matches none of the branches below. Use the ?id= applicant
		// when supplied, otherwise the logged-in user's own account.
		$applicantEmail = $this->input->get('id') ?: $this->session->userdata('username');
		if ($this->session->position === 'Admin' || $this->session->position === 'Super Admin' || $this->session->position === 'Human Resource Admin' || $this->session->position === 'HR Staff' || $this->session->position === 'asds') :
			$applicantEmail = $this->input->get('id');
		elseif ($this->session->userdata('position') === 'reg') :
			$applicantEmail = $this->session->userdata('username');
		elseif ($this->session->userdata('position') === 'user') :
			$applicantEmail = $this->session->userdata('username');
		endif;
		$result['rating'] = $this->Page_model->get_single_row_by_id('settings', 'id', 3);
		$result['track'] = $this->Page_model->get_single_row_by_id('settings', 'id', 5);
		$result['data'] = $this->PersonnelModel->myApplications($applicantEmail);
		$this->load->view('my_applications', $result);
	}

	public function ratings()
	{
		$appID = $this->input->get('appID');
		$result['data'] = $this->PersonnelModel->ratings($appID);
		$this->load->view('my_applications_rating', $result);

		if ($this->input->post('submit')) {
			//get data from the form
			$education = $this->input->post('education');
			$training = $this->input->post('training');
			$experience = $this->input->post('experience');
			$let_rating = $this->input->post('let_rating');
			$demo_rating = $this->input->post('demo_rating');
			$tr_rating = $this->input->post('tr_rating');
			$total_points = $this->input->post('total_points');

			$que = $this->db->query("update hris_applications_rating set education='" . $education . "', training='" . $training . "', experience='" . $experience . "', let_rating='" . $let_rating . "', demo_rating='" . $demo_rating . "', tr_rating='" . $tr_rating . "', total_points='" . $total_points . "' where appID='" . $appID . "'");
			$this->session->set_flashdata('success', 'Application rating has been updated successfully.');
			redirect('Page/ratings?appID=' . $appID);
		}
	}


	public function trainingNeeds()
	{
		$user_id = $this->session->userdata('username');
		$result['data'] = $this->PersonnelModel->trainingNeeds($user_id);
		$result['data1'] = $this->PersonnelModel->tnCategory();
		$this->load->view('my_training_needs', $result);
		if ($this->input->post('submit')) {
			//get data from the form
			$IDNumber = $this->session->userdata('username');
			$trainingNeeds = $this->input->post('trainingNeeds');
			$justification = $this->input->post('justification');
			$trainingCat = $this->input->post('trainingCat');

			$que1 = $this->db->query("insert into hris_training_needs (IDNumber, trainingNeeds, justification, trainingCat) values('$IDNumber','$trainingNeeds','$justification','$trainingCat')");
			$this->session->set_flashdata('success', 'Successfully added!');
			redirect('Page/trainingNeeds');
		}
	}

	public function tnCategory()
	{
		$result['data'] = $this->PersonnelModel->tnCategory();
		$this->load->view('training_needs_cat', $result);
		if ($this->input->post('submit')) {

			$category = $this->input->post('category');

			$que1 = $this->db->query("insert into hris_training_needs_cat (category) values('$category')");
			$this->session->set_flashdata('success', 'Successfully added!');
			redirect('Page/tnCategory');
		}
	}


	public function tnSummary()
	{
		$result['data'] = $this->Page_model->tnSummary();
		$this->load->view('training_needs_summary', $result);
	}

	public function tnHR()
	{
		$trainingCat = $this->input->get('trainingCat');
		$result['data'] = $this->Page_model->tnHR($trainingCat);
		$this->load->view('training_needs', $result);
	}

	public function idHR()
	{
		$result['data'] = $this->Page_model->idHR();
		$this->load->view('individual_development_hr', $result);
	}

	public function individualDevelopment()
	{
		$user_id = $this->session->userdata('username');
		$result['data'] = $this->PersonnelModel->individualDevelopment($user_id);
		$this->load->view('individual_development', $result);
		if ($this->input->post('submit')) {
			//get data from the form
			$IDNumber = $this->session->userdata('username');
			$targetCompetency = $this->input->post('targetCompetency');
			$priority = $this->input->post('priority');
			$devActivity = $this->input->post('devActivity');
			$supportNeeded = $this->input->post('supportNeeded');
			$trainingProvider = $this->input->post('trainingProvider');
			$sched = $this->input->post('sched');

			$que1 = $this->db->query("insert into hris_ind_devt (IDNumber, targetCompetency, priority, devActivity, supportNeeded, trainingProvider, sched) values('$IDNumber','$targetCompetency','$priority','$devActivity','$supportNeeded','$trainingProvider','$sched')");
			$this->Page_model->insert_at('Added Employee Development Plan');
			$this->session->set_flashdata('success', 'Successfully added!');
			redirect('Page/individualDevelopment');
		}
	}

	public function empServiceRecord()
	{
		if ($this->session->userdata('position') === 'Admin') :
			$user_id = $this->input->get('id');
		elseif ($this->session->userdata('position') === 'Human Resource Admin') :
			$user_id = $this->input->get('id');
		elseif ($this->session->userdata('position') === 'HR Staff') :
			$user_id = $this->input->get('id');
		elseif ($this->session->userdata('position') === 'user') :
			$user_id = $this->session->userdata('username');
		endif;

		$result['data'] = $this->PersonnelModel->empServiceRecord($user_id);
		$this->load->view('service_record', $result);
	}

	public function announcements()
	{
		$result['data'] = $this->PersonnelModel->announcements();
		$this->load->view('announcement', $result);
	}

	public function del_announcements($param)
	{
		$result['img'] = $this->Page_model->get_single_table_by_id("announcements", 'id', $param);
		$filename = $result['img']['fileAttachment'];
		$id = $result['img']['id'];
		$this->Page_model->delete_group($param, $filename, 'announcements', "announcements");
		$this->session->set_flashdata('danger', ' Deleted successfully!');
		redirect('Page/announcements');
	}

	public function addAnnouncement()

	{
		$config['allowed_types'] = 'png|jpg';
		$config['upload_path'] = './uploads/announcements/';
		$this->load->library('upload', $config);

		if ($this->upload->do_upload('fileAttachment')) {
			//$file = $this->upload->data();
			$id = $this->input->post('id');

			$this->Page_model->insert_announcements();
			$this->session->set_flashdata('success', 'Posted successfully!');
			redirect('Page/announcements');
		} else {
			print_r($this->upload->display_errors());
		}
	}


	// public function printServiceRecord()
	// {
	// 	if ($this->session->userdata('position') === 'Admin') :
	// 		$user_id = $this->input->get('id');
	// 	elseif ($this->session->userdata('position') === 'Staff') :
	// 		$user_id = $this->input->get('id');
	// 	elseif ($this->session->userdata('position') === 'School') :
	// 		$user_id = $this->input->get('id');
	// 	elseif ($this->session->userdata('position') === 'user') :
	// 		$user_id = $this->session->userdata('username');
	// 	endif;

	// 	$result['data'] = $this->PersonnelModel->empServiceRecord($user_id);
	// 	$result['data1'] = $this->PersonnelModel->mis_settings();
	// 	$this->load->view('service_record_print', $result);
	// }

	// public function printServiceRecord()
	// {
	// 	$position = $this->session->userdata('position');
	// 	$user_id = ($position === 'user')
	// 		? $this->session->userdata('username')
	// 		: $this->input->get('id');

	// 	$result['data'] = $this->PersonnelModel->empServiceRecord($user_id);
	// 	$result['data1'] = $this->PersonnelModel->mis_settings();

	// 	$this->load->view('service_record_print', $result);
	// }


	public function ServiceRecorddisplay($IDNumber, $id)
	{
		$result['data'] = $this->PersonnelModel->empServiceRecord1($IDNumber, $id);

		// Handle case when no data is returned
		if (empty($result['data'])) {
			$result['data'] = []; // Avoid undefined key errors
		}

		$result['data1'] = $this->PersonnelModel->mis_settings(); // Additional settings data
		$this->load->view('service_record_display', $result);
	}





	public function delete_tn()
	{
		$id = $this->input->get('id');
		$this->db->query("delete  from hris_training_needs where tnID='" . $id . "'");
		$this->session->set_flashdata('success', 'Deleted successfully!');
		redirect('Page/trainingNeeds');
	}

	public function delete_tnCat()
	{
		$id = $this->input->get('id');
		$this->db->query("delete  from hris_training_needs_cat where id='" . $id . "'");
		$this->session->set_flashdata('success', 'Deleted successfully!');
		redirect('Page/tnCategory');
	}



	public function delete_id()
	{
		$id = $this->input->get('id');
		$this->db->query("delete  from hris_ind_devt where id='" . $id . "'");
		$this->session->set_flashdata('success', 'Deleted successfully!');
		redirect('Page/individualDevelopment');
	}

	// public function viewApplicants()
	// {
	// 	$jobID = $this->input->get('jobID');
	// 	$jobTitle = $this->input->get('jobTitle');
	// 	//$result['data'] = $this->PersonnelModel->viewApplicants($jobID);
	// 	$result['data'] = $this->Common->two_cond('hris_applications', 'jobID', $jobID, 'dq', 0);
	// 	$result['job'] = $this->Common->one_cond_row('hris_jobvacancy', 'jobID', $jobID);
	// 	$result['data1'] = $this->PersonnelModel->appCountsPerCity($jobID);
	// 	$this->load->view('list_applicants_backup', $result);

	// 	if ($this->input->post('submit')) {
	// 		//get data from the form
	// 		$jobID = $this->input->post('jobID');
	// 		$empEmail = $this->input->post('empEmail');
	// 		$appStatus = $this->input->post('appStatus');
	// 		$note = $this->input->post('note');


	// 		date_default_timezone_set('Asia/Manila'); # add your city to set local time zone
	// 		$now = date('H:i:s A');
	// 		$date = date("Y-m-d");

	// 		$que = $this->db->query("insert into hris_applications_track (jobID, empEmail, dateSubmitted, appStatus, note, timeSubmitted) values('$jobID','$empEmail','$date','$appStatus','$note','$now')");
	// 		// $que1=$this->db->query("insert into atrail values('','Updated Personnel Profile','$date','$now','$Encoder','$OldIDNumber')");
	// 		// $this->session->set_flashdata('msg', '<div id="sa-success" class="alert alert-success text-center"><b>Updated successfully.</b></div>');
	// 		$this->session->set_flashdata('success', 'Application status has been posted successfully.');
	// 		redirect('Page/viewApplicants?jobID=' . $jobID . '&jobTitle=' . $jobTitle);
	// 	}
	// }

	public function viewApplicants()
	{
		$jobID = $this->input->get('jobID');
		$result['data'] = $this->Common->get_applicant_staff($jobID);

		//$result['job'] = $this->Common->one_cond_row('hris_jobvacancy', 'jobID', $jobID);
		$result['data1'] = $this->PersonnelModel->appCountsPerCity($jobID);
		$this->load->view('list_applicants', $result);
	}

	public function appTrack()
	{

		if ($this->session->userdata('position') === 'Admin') :
			$empEmail = $this->input->get('id');
		elseif ($this->session->userdata('position') === 'reg') :
			$empEmail = $this->session->userdata('username');
		endif;

		$jobID = $this->input->get('jobID');
		$result['data'] = $this->PersonnelModel->appTrack($empEmail, $jobID);
		$this->load->view('my_applications_tracking', $result);
	}

	public function jobArchieved()
	{
		$result['data'] = $this->PersonnelModel->jobArchieved();
		$this->load->view('list_jobvacancy_archieved', $result);
	}

	public function jobVacancy()
	{

		$result['data'] = $this->PersonnelModel->JobVancancies();
		$result['teaching'] = $this->Hiring_model->JobVancancies();
		$result['ren'] = $this->Common->two_cond('hris_jobvacancy', 'promotion', 1, 'jvStatus', 'Open');
		$result['lock'] = $this->Common->one_cond_count_row('hris_applicant', 'stat', 0);
		$result['pos_title'] = $this->Common->no_cond_order_by('hris_positions', 'title', 'ASC');


		$this->load->view('list_jobvacancy', $result);

		if ($this->input->post('submit')) {

			$config['allowed_types'] = 'pdf';
			$config['upload_path'] = './uploads/regfile';
			$new_name = time() . 'job' . $_FILES["file"]['name'];
			$config['file_name'] = $new_name;
			$this->load->library('upload', $config);

			if ($this->upload->do_upload('file')) {
				$this->Reg->insert_job();
				$this->session->set_flashdata('success', 'Successfully updated.');
				redirect(base_url() . 'Page/jobVacancy');
			} else {
				$this->session->set_flashdata('danger', $this->upload->display_errors());
				redirect(base_url() . 'Page/jobVacancy');
			}

			// //get data from the form
			// $jobTitle = $this->input->post('jobTitle');
			// $empType = $this->input->post('empType');
			// $department = $this->input->post('department');
			// $qty = $this->input->post('qty');
			// $itemNo = $this->input->post('itemNo');
			// $description = addslashes($this->input->post('description'));
			// $postedBy = $this->session->userdata('username');
			// $sy = $this->input->post('sy');


			// date_default_timezone_set('Asia/Manila'); # add your city to set local time zone
			// $now = date('H:i:s A');
			// $date = date("Y-m-d");

			// $que1 = $this->db->query("insert into hris_jobvacancy (jobTitle, empType, department, qty, description, postedBy, datePosted, jvStatus, itemNo, sy) values('$jobTitle','$empType','$department','$qty','$description','$postedBy','$date','Open','$itemNo','$sy')");
			// $this->session->set_flashdata('success', 'Successfully posted!');
			// redirect('Page/jobVacancy');
		}
	}

	public function archive_jv()
	{
		$id = $this->input->get('jobID');
		$this->db->query("update hris_jobvacancy set jvStatus='Closed' where jobID='" . $id . "'");
		$this->Page_model->insert_at('Archieved Job Vacancy.', $id);
		$this->session->set_flashdata('success', 'Archieved successfully!');
		redirect('Page/jobVacancy');
	}

	public function rqa()
	{
		$jobID = $this->input->get('jobID');
		$result['data'] = $this->PersonnelModel->rqa($jobID);
		$result['data1'] = $this->PersonnelModel->rqa_group($jobID);
		$result['data2'] = $this->PersonnelModel->rqa_group_shs($jobID);
		$this->load->view('list_jobvacancy_rqa', $result);
	}

	public function rqa_municipality()
	{
		$jobID = $this->input->get('jobID');
		$result['data1'] = $this->PersonnelModel->rqa_group_mun($jobID);
		$result['job'] = $this->Common->one_cond_row_select('hris_jobvacancy', 'jobID,sy,sign,job_type', 'jobID', $jobID);
		$this->load->view('list_jobvacancy_rqa_mun', $result);
	}


	public function rqa_municipality_track()
	{
		$jobID = $this->input->get('id');
		$spec = $this->input->get('spec');
		$result['data1'] = $this->PersonnelModel->rqa_municipality_track($jobID, $spec);
		$this->load->view('list_jobvacancy_rqa_mun_shs', $result);
	}

	public function rqa_municipality_specialization()
	{
		$jobID = $this->input->get('id');
		$spec = $this->input->get('spec');
		$result['data1'] = $this->PersonnelModel->rqa_municipality_spec($jobID, $spec);
		$this->load->view('list_jobvacancy_rqa_mun_spec', $result);
	}


	public function rqa_shs()
	{
		$jobID = $this->input->get('jobID');
		$result['data'] = $this->PersonnelModel->rqa($jobID);
		$result['data1'] = $this->PersonnelModel->rqa_group_shs($jobID);
		$this->load->view('list_jobvacancy_rqa_shs', $result);
	}

	public function rqa_specialization()
	{
		$jobID = $this->input->get('jobID');
		$spe = $this->input->get('spe');
		$result['data'] = $this->PersonnelModel->rqa_specialization($jobID, $spe);
		$this->load->view('list_jobvacancy_rqa_specialization', $result);
	}

	public function rqa_municipality_list()
	{
		$jobID = $this->input->get('jobID');
		$mun = $this->input->get('mun');
		$result['data'] = $this->PersonnelModel->rqa_municipality_list($jobID, $mun);
		$this->load->view('list_jobvacancy_rqa_munlist', $result);
	}

	public function rqa_track()
	{
		$jobID = $this->input->get('jobID');
		$spe = $this->input->get('spe');
		$result['data'] = $this->PersonnelModel->rqa_track($jobID, $spe);
		$this->load->view('list_jobvacancy_rqa_specialization', $result);
	}

	public function submitApplication()
	{
		// $result['data']=$this->PersonnelModel->JobVancancies();
		$this->load->view('job_application');

		if ($this->input->post('submit')) {
			//get data from the form
			// $applicantEmail=$this->input->get('appID');
			$jobID = $this->input->get('jobID');
			$applicantID = $this->input->post('applicantID');
			$docURL = $this->input->post('docURL');
			$note = $this->input->post('note');
			$applicantEmail = $this->session->userdata('username');

			date_default_timezone_set('Asia/Manila'); # add your city to set local time zone
			$now = date('H:i:s A');
			$date = date("Y-m-d");

			$que = $this->db->query("insert into hris_applications (jobID, empEmail, dateSubmitted, docURL, appStatus, note) values('$jobID','$applicantEmail','$date','$docURL','Application Submitted','$note')");
			$que1 = $this->db->query("insert into hris_applications_track (jobID, empEmail, dateSubmitted, appStatus, note,timeSubmitted) values('$jobID','$applicantEmail','$date','Application Submitted','$note','$now')");

			$this->session->set_flashdata('success', 'Your application has been submitted successfully.');

			redirect('Page/jobVacancy');
		}
	}



	//Request
	public function submitRequest()
	{
		$id = $this->session->userdata('username');

		$result['data'] = $this->PersonnelModel->docRequest($id);
		$result['data2'] = $this->PersonnelModel->getTrackingNo();
		$this->load->view('request_submit', $result);

		if ($this->input->post('submit')) {

			$config['upload_path'] = './upload/reqDocs/';
			$config['allowed_types'] = '*';
			$config['max_size'] = 5120;
			//$config['max_width'] = 1500;
			//$config['max_height'] = 1500;

			$this->load->library('upload', $config);
			$this->upload->do_upload('nonoy');
			$data = array('image_metadata' => $this->upload->data());
			$filename = $this->upload->data('file_name');

			$email = $this->input->post('email');
			$IDNumber = $this->input->post('IDNumber');
			$docName = $this->input->post('docName');
			$purpose = $this->input->post('purpose');
			$trackingNo = $this->input->post('trackingNo');
			$pReference = $this->input->post('pReference');
			$trackingNo = $this->input->post('trackingNo');

			$dateReq = date("Y-m-d");
			date_default_timezone_set('Asia/Manila'); # add your city to set local time zone
			$now = date('H:i:s A');

			//check if record exist
			$que = $this->db->query("select * from request where trackingNo='" . $trackingNo . "'");
			$row = $que->num_rows();
			if ($row) {
				//redirect('Page/notification_error');
				$this->session->set_flashdata('msg', '<div class="alert alert-danger text-center"><b>Tracking No. is in use.</b></div>');
				redirect('Page/submitRequest');
			} else {

				$que = $this->db->query("insert into request values('$trackingNo','$docName','$purpose','$dateReq','$now','$IDNumber','Open','$pReference','$filename')");
				$que = $this->db->query("insert into request_stat values('','$IDNumber','request submitted','$IDNumber','$dateReq','$now','$trackingNo','Open','','')");
				$que = $this->db->query("insert into atrail values('','Requested a Document','$dateReq','$now','$id','$id')");
				$this->session->set_flashdata('msg', '<div class="alert alert-success text-center"><b>Your request has been submitted.</b></div>');

				//Email Notification
				//$this->load->config('email');
				//$this->load->library('email');
				//$mail_message = 'Dear ' . $fname . ',' . "\r\n"; 
				//$mail_message .= '<br><br>Your request with tracking number <b>' . $trackingNo . ' </b>has been submitted.' . "\r\n"; 
				//$mail_message .= '<br><br>Login to your portal to check the status of your request.' . "\r\n"; 

				//$mail_message .= '<br><br>Thanks & Regards,';
				//$mail_message .= '<br>SRMS - Online';

				//$this->email->from('no-reply@lxeinfotechsolutions.com', 'SRMS Online Team')
				//->to($email)
				//->subject('Online Request')
				//->message($mail_message);
				//$this->email->send();

				redirect('Page/submitRequest');
			}
		}
	}
	function studentRequestStat()
	{
		$id = $this->input->get('trackingNo');
		$result['data'] = $this->PersonnelModel->studerequestTracking($id);
		$this->load->view('request_status', $result);

		if ($this->input->post('submit')) {
			$config['upload_path'] = './upload/reqDocs/';
			$config['allowed_types'] = '*';
			$config['max_size'] = 5120;
			//$config['max_width'] = 1500;
			//$config['max_height'] = 1500;

			$this->load->library('upload', $config);


			$this->upload->do_upload('nonoy');
			$data = array('image_metadata' => $this->upload->data());
			$filename = $this->upload->data('file_name');

			$StudentNumber = $this->input->post('StudentNumber');
			$trackingNo = $this->input->post('trackingNo');
			$reqStatus = $this->input->post('reqStatus');
			$reqStat = $this->input->post('reqStat');

			$dateReq = date("Y-m-d");
			date_default_timezone_set('Asia/Manila'); # add your city to set local time zone
			$now = date('H:i:s A');

			$que = $this->db->query("update request set reqStat='$reqStat' where trackingNo='$trackingNo'");
			$que = $this->db->query("insert into request_stat values('','$StudentNumber','$reqStatus','$id','$dateReq','$now','$trackingNo','$reqStat','$filename','')");
			$this->session->set_flashdata('msg', '<div class="alert alert-success text-center"><b>New request status has been posted.</b></div>');
		}
	}
	function newRequest()
	{
		$result['data'] = $this->PersonnelModel->getProfile();
		$this->load->view('request_search', $result);
	}
	function requestSummary()
	{
		$result['data'] = $this->PersonnelModel->totalStudeRequest();
		$result['data1'] = $this->PersonnelModel->openRequest();
		$result['data2'] = $this->PersonnelModel->closedRequest();
		$result['data3'] = $this->PersonnelModel->docReqCounts();
		$result['data4'] = $this->PersonnelModel->totalReleased();
		$result['data19'] = $this->PersonnelModel->studeRequestList();
		$this->load->view('dashboard_request', $result);
	}

	function releasedRequest()
	{
		$result['data'] = $this->PersonnelModel->releasedRequest();
		$this->load->view('request_released', $result);
	}

	//delete student's profile
	public function deleteRequest()
	{
		$id = $this->input->get('id');
		$username = $this->session->userdata('username');
		date_default_timezone_set('Asia/Manila'); # add your city to set local time zone
		$now = date('H:i:s A');
		$date = date("Y-m-d");
		$query = $this->db->query("delete from request where trackingNo='" . $id . "'");
		$query = $this->db->query("insert into atrail values('','Deleted Student''s Request','$date','$now','$username','$id')");

		redirect('Page/profileList');
	}





	public function cancelRequest()
	{
		$id = $this->input->get('id');

		// Update only if the reqStat is 'Pending'
		$this->db->set('reqStat', 'Canceled');
		$this->db->where('IDNumber', $id);
		$this->db->where('reqStat', 'Pending');  // Ensure reqStat is 'Pending'
		$this->db->update('doc_request');

		// Redirect or load a confirmation page
		redirect('Page/sr_request'); // Example redirect
	}



	function openDocRequest()
	{
		$result['data'] = $this->PersonnelModel->openDocRequest();
		$this->load->view('request_open', $result);
	}

	function closedDocRequest()
	{
		$result['data'] = $this->PersonnelModel->closedDocRequest();
		$this->load->view('request_closed', $result);
	}

	function allRequest()
	{
		$result['data'] = $this->StudentModel->allDocRequest();
		$this->load->view('request_all', $result);
	}

	//Change schoolLogo
	function schoolLogo()
	{
		$this->load->view('change-logo');
	}

	public function uploadLogo()
	{
		$config['upload_path'] = './upload/profile/';
		$config['allowed_types'] = 'jpg|gif|png';
		$config['max_size'] = 2048;
		//$config['max_width'] = 1500;
		//$config['max_height'] = 1500;

		$this->load->library('upload', $config);

		if (!$this->upload->do_upload('nonoy')) {
			$msg = array('error' => $this->upload->display_errors());

			$this->load->view('change-logo', $msg);
		} else {
			$data = array('image_metadata' => $this->upload->data());
			//get data from the form
			$username = $this->session->userdata('username');
			//$filename=$this->input->post('nonoy');
			$filename = $this->upload->data('file_name');

			$que = $this->db->query("update schools set schoolLogo='$filename' where schoolID='$username'");
			$que = $this->db->query("update users set avatar='$filename' where username='$username'");
			$this->session->set_flashdata('msg', '<div class="alert alert-success text-center"><b>Uploaded Succesfully!</b></div>');

			$this->load->view('change-logo');
		}
	}
	function serviceRecords()
	{
		$id = $this->session->userdata('username');
		$result['data'] = $this->PersonnelModel->serviceRecords($id);
		$result['data1'] = $this->PersonnelModel->searchPersonnel();
		$this->load->view('hr_personnel_sr', $result);
	}

	//Change Profile Pic
	function changeDP()
	{
		$this->load->view('upload_profile_pic');
	}

	//Change Profile Pic
	function change_app_DP()
	{
		$this->load->view('upload_app_profile');
	}

	public function uploadProfPic()
	{
		$config['upload_path'] = './uploads/profile/';
		$config['allowed_types'] = 'jpg|gif|png';
		$config['max_size'] = 2048;
		//$config['max_width'] = 1500;
		//$config['max_height'] = 1500;

		$this->load->library('upload', $config);

		if (!$this->upload->do_upload('nonoy')) {
			$msg = array('error' => $this->upload->display_errors());

			$this->load->view('upload_profile_pic', $msg);
		} else {
			$data = array('image_metadata' => $this->upload->data());
			//get data from the form
			$username = $this->session->userdata('username');
			$user = $this->Common->one_cond_row('users', 'username', $username);
			unlink("uploads/profile/" . $user->image);
			//$filename=$this->input->post('nonoy');
			$filename = $this->upload->data('file_name');

			$que = $this->db->query("update users set image='$filename' where username='$username'");
			$this->session->set_flashdata('success', 'The new profile image has been uploaded successfully.  Exit the system, then log in again to check if it works.');
			if ($this->session->position == "reg") {
				redirect(base_url() . 'Pages/ja/' . $this->session->c_id);
			} else {
				redirect(base_url() . 'Pages/view_employee');
			}

			$this->load->view('upload_profile_pic');
		}
	}


	public function createAccount()
	{
		$this->load->view('user_accounts');
		if ($this->input->post('submit')) {
			//get data from the form
			$username = $this->input->post('username');
			$IDNumber = $this->input->post('IDNumber');
			$password = sha1($this->input->post('password'));
			$acctLevel = $this->input->post('acctLevel');
			$fName = $this->input->post('fName');
			$mName = $this->input->post('mName');
			$lName = $this->input->post('lName');
			//$completeName=$fName.' '.$lName;
			$email = $this->input->post('email');
			$dateCreated = date("Y-m-d");

			//check if record exist
			$que = $this->db->query("select * from users where username='" . $username . "'");
			$row = $que->num_rows();
			if ($row) {
				//redirect('Page/notification_error');
				$this->session->set_flashdata('msg', '<div class="alert alert-danger text-center"><b>Username is in use.</b></div>');
				redirect('Page/createAccount');
			} else {
				//save user account
				$que = $this->db->query("insert into users values('$username','$password','$fName','$mName','$lName','','','$dateCreated','avatar.png','$acctLevel','$email','$IDNumber','Active')");
				$this->session->set_flashdata('msg', '<div class="alert alert-success data-dismiss=alert text-center"><b>New account has been created successfully.</b></div>');
				redirect('Page/createAccount');
			}
		}
	}
	function viewAccounts()
	{
		$result['data'] = $this->PersonnelModel->viewAccounts();
		$this->load->view('settings_view_users', $result);
	}
	public function deleteUserAccount()
	{
		$id = $this->input->get('id');
		$this->PersonnelModel->deleteUserAccount($id);
		redirect("Page/viewAccounts");
	}

	//update user accounts
	function updateUserAccount()
	{
		$id = $this->input->get('id');
		$result['data'] = $this->PersonnelModel->viewAccountsID($id);

		$this->load->view('user_accounts_update', $result);
		if ($this->input->post('submit')) {
			//get data from the form
			$username = $this->input->post('username');
			$IDNumber = $this->input->post('IDNumber');
			$password = sha1($this->input->post('password'));
			$password2 = $this->input->post('password');
			$acctLevel = $this->input->post('acctLevel');
			$fName = $this->input->post('fName');
			$mName = $this->input->post('mName');
			$lName = $this->input->post('lName');

			$email = $this->input->post('email');
			$date = date("Y-m-d");

			date_default_timezone_set('Asia/Manila'); # add your city to set local time zone
			$now = date('H:i:s A');
			$Encoder = $this->session->userdata('username');

			$que = $this->db->query("update users set password='$password',fName='$fName',mName='$mName',lName='$lName',acctLevel='$acctLevel',IDNumber='$IDNumber',email='$email' where username='$username'");
			$que = $this->db->query("insert into atrail values('','Updated user account','$date','$now','$Encoder','$username')");
			$this->session->set_flashdata('msg', '<div class="alert alert-success text-center"><b>The user account details have been updated successfully. </b></div>');

			redirect('Page/viewAccounts');
		}
	}

	//Submit DepEd Email Request
	function depedEmailRequest()
	{
		$id = $this->session->userdata('username');
		//$result['data']=$this->PersonnelModel->depedEmailRequest();  
		$this->load->view('hr_personnel_depedemail_request');
	}

	//Processing of Training and Speakership
	public function processdepedEmailRequest()
	{

		$IDNumber = $this->session->userdata('username');
		$fname = $this->session->userdata('fName');
		$requestType = $this->input->post('requestType');
		$depedEmail = $this->input->post('depedEmail');
		$personalEmail = $this->input->post('personalEmail');
		$email = $this->input->post('personalEmail');
		$school = $this->input->post('school');
		$district = $this->input->post('district');
		$contactNo = $this->input->post('contactNo');
		date_default_timezone_set('Asia/Manila'); # add your city to set local time zone
		$dateRequested = date("Y-m-d");

		$que = $this->db->query("insert into deped_email_request values('','$IDNumber','$requestType','$depedEmail','$personalEmail','$school','$district','$contactNo','$dateRequested','Open')");
		$this->session->set_flashdata('msg', '<div class="alert alert-success text-center"><b>Request submitted succesfully!</b></div>');

		//Email Notification
		$this->load->config('email');
		$this->load->library('email');
		$mail_message = 'Dear ' . $fname . ',' . "\r\n";
		$mail_message .= '<br><br>Your request has been submitted.' . "\r\n";

		$mail_message .= '<br><br>You will be notified once processed.' . "\r\n";
		$mail_message .= '<br><br>Thanks & Regards,';
		$mail_message .= '<br>MIS Team';

		$this->email->from('no-reply@lxeinfotechsolutions.com', 'DepEd Davao Oriental - MIS Team')
			->to($email)
			->subject('DepEd Email Request')
			->message($mail_message);
		$this->email->send();

		redirect('Page/depedEmailRequest');
	}
	//DepEd Email - For Processing List 
	function emailReqForProcessing()
	{
		$result['data'] = $this->PersonnelModel->emailReqForProcessing();
		$this->load->view('ict_depedemail_request.php', $result);
	}

	//Update Personnel Profile
	function updateEmailRequest()
	{
		$id = $this->input->get('id');
		$result['data'] = $this->PersonnelModel->processEmailRequest($id);
		$this->load->view('ict_depedemail_request_update', $result);
		if ($this->input->post('submit')) {
			//get data from the form
			$reqID = $this->input->post('reqID');
			$IDNumber = $this->input->post('IDNumber');
			$fname = $this->input->post('FirstName');
			$depedEmail = $this->input->post('depedEmail');
			$personalEmail = $this->input->post('personalEmail');
			$temPass = $this->input->post('temPass');

			date_default_timezone_set('Asia/Manila'); # add your city to set local time zone
			$now = date('H:i:s A');
			$date = date("Y-m-d");

			$que = $this->db->query("insert into deped_email_request_actions values('','$IDNumber','$depedEmail','$date','$temPass')");
			$que = $this->db->query("update deped_email_request set reqStat='Closed' where reqID='$reqID'");
			$this->session->set_flashdata('msg', '<div id="sa-success" class="alert alert-success text-center"><b>Processed successfully.</b></div>');

			//Email Notification
			$this->load->config('email');
			$this->load->library('email');
			$mail_message = 'Dear ' . $fname . ',' . "\r\n";
			$mail_message .= '<br><br>Your request has been processed.  Please take note of the following:' . "\r\n";
			$mail_message .= '<br>DepEd E-mail: <b>' . $depedEmail . '</b>' . "\r\n";
			$mail_message .= '<br>Temporary Password: <b>' . $temPass . '</b>' . "\r\n";

			$mail_message .= '<br><br>Thanks & Regards,';
			$mail_message .= '<br>MIS Team';

			$this->email->from('no-reply@lxeinfotechsolutions.com', 'DepEd Davao Oriental - MIS Team')
				->to($personalEmail)
				->subject('DepEd Email Request')
				->message($mail_message);
			$this->email->send();

			redirect('Page/emailReqForProcessing');
		}
	}

	public function deletecache1()
	{
		$this->output->delete_cache('Page');
	}
	public function deletecache2()
	{
		$this->output->delete_cache('Schools');
	}

	public function delete_lc()
	{
		$id = $this->input->get('id');
		$this->db->query("delete  from hris_leaverecords where ID='" . $id . "'");
		$this->session->set_flashdata('success', 'Deleted successfully!');
		redirect('Page/leaveCreditsSummary');
	}
	public function leaveCreditsUploading()
	{

		// Check form submit or not 
		if ($this->input->post('upload') != NULL) {
			$data = array();
			if (!empty($_FILES['file']['name'])) {
				// Set preference 
				$config['upload_path'] = 'uploads/leave_credits/';
				$config['allowed_types'] = 'csv';
				$config['max_size'] = '1000'; // max_size in kb 
				$config['file_name'] = $_FILES['file']['name'];

				// Load upload library 
				$this->load->library('upload', $config);

				// File upload
				if ($this->upload->do_upload('file')) {
					// Get data about the file
					$uploadData = $this->upload->data();
					$filename = $uploadData['file_name'];

					// Reading file
					$file = fopen("uploads/leave_credits/" . $filename, "r");
					$i = 0;

					$importData_arr = array();

					while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
						$num = count($filedata);

						for ($c = 0; $c < $num; $c++) {
							$importData_arr[$i][] = $filedata[$c];
						}
						$i++;
					}
					fclose($file);

					$skip = 0;

					// insert import data
					foreach ($importData_arr as $userdata) {
						if ($skip != 0) {
							$this->PersonnelModel->leaveCreditsUploading($userdata);
							$this->session->set_flashdata('success', 'Uploaded successfully.');
						}
						$skip++;
					}
					$data['response'] = '<div class="alert alert-info text-center">Uploaded Successfully.</div>';
				} else {
					$data['response'] = '<div class="alert alert-warning text-center">Failed</div>';
				}
			} else {
				$data['response'] = '<div class="alert alert-warning text-center">Failed</div>';
			}
			// load view 
			$this->load->view('file_uploader_leavecredits', $data);
		} else {
			// load view 
			$this->load->view('file_uploader_leavecredits');
		}
	}

	public function appRatingUploading()
	{

		// Check form submit or not 
		if ($this->input->post('upload') != NULL) {
			$data = array();
			if (!empty($_FILES['file']['name'])) {
				// Set preference 
				$config['upload_path'] = 'uploads/files/';
				$config['allowed_types'] = 'csv';
				$config['max_size'] = '1000'; // max_size in kb 
				$config['file_name'] = $_FILES['file']['name'];

				// Load upload library 
				$this->load->library('upload', $config);

				// File upload
				if ($this->upload->do_upload('file')) {
					// Get data about the file
					$uploadData = $this->upload->data();
					$filename = $uploadData['file_name'];

					// Reading file
					$file = fopen("uploads/files/" . $filename, "r");
					$i = 0;

					$importData_arr = array();

					while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
						$num = count($filedata);

						for ($c = 0; $c < $num; $c++) {
							$importData_arr[$i][] = $filedata[$c];
						}
						$i++;
					}
					fclose($file);

					$skip = 0;

					// insert import data
					foreach ($importData_arr as $userdata) {
						if ($skip != 0) {
							$this->PersonnelModel->appRatingUploading($userdata);
							$this->session->set_flashdata('success', 'Uploaded successfully.');
						}
						$skip++;
					}
					$data['response'] = '<div class="alert alert-info text-center">Uploaded Successfully.</div>';
				} else {
					$data['response'] = '<div class="alert alert-warning text-center">Failed</div>';
				}
			} else {
				$data['response'] = '<div class="alert alert-warning text-center">Failed</div>';
			}
			// load view 
			$this->load->view('file_uploader', $data);
		} else {
			// load view 
			$this->load->view('file_uploader');
		}
	}

	public function serviceRecordUploading()
	{

		// Check form submit or not 
		if ($this->input->post('upload') != NULL) {
			$data = array();
			if (!empty($_FILES['file']['name'])) {
				// Set preference 
				$config['upload_path'] = 'uploads/service_record/';
				$config['allowed_types'] = 'csv';
				$config['max_size'] = '10000'; // max_size in kb 
				$config['file_name'] = $_FILES['file']['name'];

				// Load upload library 
				$this->load->library('upload', $config);

				// File upload
				if ($this->upload->do_upload('file')) {
					// Get data about the file
					$uploadData = $this->upload->data();
					$filename = $uploadData['file_name'];

					// Reading file
					$file = fopen("uploads/service_record/" . $filename, "r");
					$i = 0;

					$importData_arr = array();

					while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
						$num = count($filedata);

						for ($c = 0; $c < $num; $c++) {
							$importData_arr[$i][] = $filedata[$c];
						}
						$i++;
					}
					fclose($file);

					$skip = 0;

					// insert import data
					foreach ($importData_arr as $userdata) {
						if ($skip != 0) {
							$this->PersonnelModel->serviceRecordUploading($userdata);
							$this->session->set_flashdata('success', 'Uploaded successfully.');
						}
						$skip++;
					}
					$data['response'] = '<div class="alert alert-info text-center">Uploaded Successfully.</div>';
				} else {
					$data['response'] = '<div class="alert alert-warning text-center">Failed</div>';
				}
			} else {
				$data['response'] = '<div class="alert alert-warning text-center">Failed</div>';
			}
			// load view 
			$this->load->view('file_uploader_sr', $data);
		} else {
			// load view 
			$this->load->view('file_uploader_sr');
		}
	}

	function uploadEnrolees()
	{
		$result['title'] = "Upload Enrolees";


		// $this->load->view('templates/head');
		// $this->load->view('templates/header');
		// $this->load->view('sbfp_upload', $result);
		// $this->load->view('templates/footer');

		// Check if the form is submitted
		if ($this->input->post('upload') !== NULL) {
			$data = array();

			if (!empty($_FILES['file']['name'])) {
				// Set upload preferences
				$config = array(
					'upload_path'   => 'uploads/enrolees/',
					'allowed_types' => 'csv',
					'max_size'      => '10000', // in kb
					'file_name'     => $_FILES['file']['name']
				);

				// Load upload library
				$this->load->library('upload', $config);

				// Perform file upload
				if ($this->upload->do_upload('file')) {
					// Get file data
					$uploadData = $this->upload->data();
					$filename = $uploadData['file_name'];
					$filePath = $config['upload_path'] . $filename;

					// Read file
					$importDataArr = array();
					if (($file = fopen($filePath, "r")) !== FALSE) {
						while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
							$importDataArr[] = $filedata;
						}
						fclose($file);
					}

					// Insert import data
					foreach ($importDataArr as $index => $userdata) {
						if ($index > 0) { // Skip header row
							$this->PersonnelModel->enroleesUploading($userdata);
						}
					}
					// $data['response'] = '<div class="alert alert-info text-center">Uploaded Successfully.</div>';
					$this->session->set_flashdata('success', ' Uploaded Successfully.');
				} else {
					$data['response'] = '<div class="alert alert-warning text-center">Upload Failed: ' . $this->upload->display_errors() . '</div>';
				}
			} else {
				$data['response'] = '<div class="alert alert-warning text-center">No file selected.</div>';
			}

			// Load view with data
			$this->load->view('sbfp_upload', $data);
		} else {
			// Load view without data
			$this->load->view('sbfp_upload');
		}
	}


	public function profileUploading()
	{

		// Check form submit or not 
		if ($this->input->post('upload') != NULL) {
			$data = array();
			if (!empty($_FILES['file']['name'])) {
				// Set preference 
				$config['upload_path'] = 'uploads/staff_profile/';
				$config['allowed_types'] = 'csv';
				$config['max_size'] = '10000'; // max_size in kb 
				$config['file_name'] = $_FILES['file']['name'];

				// Load upload library 
				$this->load->library('upload', $config);

				// File upload
				if ($this->upload->do_upload('file')) {
					// Get data about the file
					$uploadData = $this->upload->data();
					$filename = $uploadData['file_name'];

					// Reading file
					$file = fopen("uploads/staff_profile/" . $filename, "r");
					$i = 0;

					$importData_arr = array();

					while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
						$num = count($filedata);

						for ($c = 0; $c < $num; $c++) {
							$importData_arr[$i][] = $filedata[$c];
						}
						$i++;
					}
					fclose($file);

					$skip = 0;

					// insert import data
					foreach ($importData_arr as $userdata) {
						if ($skip != 0) {
							$this->PersonnelModel->profileUploading($userdata);
							$this->session->set_flashdata('success', 'Uploaded successfully.');
						}
						$skip++;
					}
					$data['response'] = '<div class="alert alert-info text-center">Uploaded Successfully.</div>';
				} else {
					$data['response'] = '<div class="alert alert-warning text-center">Failed</div>';
				}
			} else {
				$data['response'] = '<div class="alert alert-warning text-center">Failed</div>';
			}
			// load view 
			$this->load->view('file_uploader_profile', $data);
		} else {
			// load view 
			$this->load->view('file_uploader_profile');
		}
	}





	// From SGOD
	function sgod()
	{
		//Allowing access to Admin only
		if ($this->session->userdata('section') === 'Chief - SGOD') {
			$param = $this->session->userdata('secGroup');
			$result['data'] = $this->SGODModel->count_sec_users('sgod_users', $param);
			$result['data1'] = $this->SGODModel->count_sections('sgod_sections', $param);
			$result['data2'] = $this->SGODModel->count_sec_accomplishments('sgod_accomplishments', $param);
			$result['data3'] = $this->SGODModel->count_table_row('schools');
			$this->load->view('dashboard_admin', $result);
		} else {
			echo "Access Denied";
		}
	}

	//   function School(){
	// 	//Allowing access to Admin only
	// 	if($this->session->userdata('section')==='School'){
	// 		$result['data']=$this->SGODModel->count_table_row('sgod_aip');
	// 		$result['pillar']=$this->SGODModel->count_table_row('sgod_settings_pillar');
	// 		$result['domain']=$this->SGODModel->count_table_row('sgod_settings_domain');
	// 		$result['pias']=$this->SGODModel->count_table_row('sgod_settings_pias');
	// 		$this->load->view('dashboard_school', $result);
	// 	}else{
	// 		echo "Access Denied";
	// 	}
	//   }
	function SMME()
	{
		if ($this->session->userdata('section') === 'School Management Monitoring and Evaluation') {
			$section = $this->session->userdata('section');
			$result['data'] = $this->SGODModel->cPublic();
			$result['data1'] = $this->SGODModel->cPrivate();
			$result['data2'] = $this->SGODModel->aSectionAccomplishments($section);
			$result['data3'] = $this->SGODModel->totalSectionUsers($section);
			$this->load->view('dashboard_SMME', $result);
		} else {
			echo "Access Denied";
		}
	}


	function PESS()
	{
		if ($this->session->userdata('section') === 'Physical Education and Schools Sports') {
			$section = $this->session->userdata('section');
			$result['data'] = $this->SGODModel->cPublic();
			$result['data1'] = $this->SGODModel->cPrivate();
			$result['data2'] = $this->SGODModel->aSectionAccomplishments($section);
			$result['data3'] = $this->SGODModel->totalSectionUsers($section);
			$this->load->view('dashboard_PESS', $result);
		} else {
			echo "Access Denied";
		}
	}

	function DRRM()
	{
		if ($this->session->userdata('section') === 'Disaster Risk Reduction Management (DRRM) Section') {
			$section = $this->session->userdata('section');
			$result['data'] = $this->SGODModel->cPublic();
			$result['data1'] = $this->SGODModel->cPrivate();
			$result['data2'] = $this->SGODModel->aSectionAccomplishments($section);
			$result['data3'] = $this->SGODModel->totalSectionUsers($section);
			$this->load->view('dashboard_drrm', $result);
		} else {
			echo "Access Denied";
		}
	}

	function SHNS()
	{
		if ($this->session->userdata('section') === 'School Health and Nutrition Section') {
			$section = $this->session->userdata('section');
			$result['data'] = $this->SGODModel->cPublic();
			$result['data1'] = $this->SGODModel->cPrivate();
			$result['data2'] = $this->SGODModel->aSectionAccomplishments($section);
			$result['data3'] = $this->SGODModel->totalSectionUsers($section);
			$this->load->view('dashboard_shns', $result);
		} else {
			echo "Access Denied";
		}
	}


	function HRD()
	{
		if ($this->session->userdata('section') === 'Human Resource Development Section') {
			$section = $this->session->userdata('section');
			$result['data'] = $this->SGODModel->cPublic();
			$result['data1'] = $this->SGODModel->cPrivate();
			$result['data2'] = $this->SGODModel->aSectionAccomplishments($section);
			$result['data3'] = $this->SGODModel->totalSectionUsers($section);
			$this->load->view('dashboard_hrd', $result);
		} else {
			echo "Access Denied";
		}
	}

	function EFS()
	{
		if ($this->session->userdata('section') === 'Education Facilities Section') {
			$section = $this->session->userdata('section');
			$result['data'] = $this->SGODModel->cPublic();
			$result['data1'] = $this->SGODModel->cPrivate();
			$result['data2'] = $this->SGODModel->aSectionAccomplishments($section);
			$result['data3'] = $this->SGODModel->totalSectionUsers($section);
			$this->load->view('dashboard_efd', $result);
		} else {
			echo "Access Denied";
		}
	}

	function SMN()
	{
		if ($this->session->userdata('section') === 'Social Mobilization and Networking') {
			$section = $this->session->userdata('section');
			$result['data'] = $this->SGODModel->cPublic();
			$result['data1'] = $this->SGODModel->cPrivate();
			$result['data2'] = $this->SGODModel->aSectionAccomplishments($section);
			$result['data3'] = $this->SGODModel->totalSectionUsers($section);
			$this->load->view('dashboard_smn', $result);
		} else {
			echo "Access Denied";
		}
	}

	function Planning()
	{
		if ($this->session->userdata('section') === 'Planning') {
			$section = $this->session->userdata('section');
			$result['data'] = $this->SGODModel->cPublic();
			$result['data1'] = $this->SGODModel->cPrivate();
			$result['data2'] = $this->SGODModel->aSectionAccomplishments($section);
			$result['data3'] = $this->SGODModel->totalSectionUsers($section);
			$this->load->view('dashboard_planning', $result);
		} else {
			echo "Access Denied";
		}
	}

	function Research()
	{
		if ($this->session->userdata('section') === 'Research') {
			$section = $this->session->userdata('section');
			$result['data'] = $this->SGODModel->cPublic();
			$result['data1'] = $this->SGODModel->cPrivate();
			$result['data2'] = $this->SGODModel->aSectionAccomplishments($section);
			$result['data3'] = $this->SGODModel->totalSectionUsers($section);
			$this->load->view('dashboard_research', $result);
		} else {
			echo "Access Denied";
		}
	}

	function YFP()
	{
		if ($this->session->userdata('section') === 'Youth Formation Program') {
			$section = $this->session->userdata('section');
			$result['data'] = $this->SGODModel->cPublic();
			$result['data1'] = $this->SGODModel->cPrivate();
			$result['data2'] = $this->SGODModel->aSectionAccomplishments($section);
			$result['data3'] = $this->SGODModel->totalSectionUsers($section);
			$this->load->view('dashboard_youth', $result);
		} else {
			echo "Access Denied";
		}
	}


	function app()
	{
		$result['data'] = $this->SGODModel->get_all('app_school');
		$this->load->view('app', $result);
	}


	function app_category()
	{
		$data['title'] = "Category List";
		$data['m_title'] = "Add New Category";
		$data['e_title'] = "Update Category";
		$data['page'] = $this->SGODModel->get_all('sgod_settings_cat');
		$id = $this->input->post('id');
		$data['cat'] = $this->SGODModel->get_data_by_id('sgod_settings_cat', 'id', $id);

		$this->load->view('setting_cat', $data);

		if ($this->input->post('submit')) {
			$this->SGODModel->insert_app_cat();
			redirect(base_url() . 'Page/app_category');
		}

		if ($this->input->post('edit')) {
			//$data['cat'] = $this->SGODModel->get_data_by_id('app_cat', 'id',$id);
			$this->SGODModel->update_app_cat();
			redirect(base_url() . 'Page/app_category');
		}
	}

	public function app_cat_del()
	{
		$this->SGODModel->delete(3, 'id', 'app_cat');
		redirect(base_url() . 'Page/app_category');
	}



	function settings_pillar()
	{
		$data['title'] = "Pillar List";
		$data['m_title'] = "Add New Pillar";
		$data['e_title'] = "Update Pillar";
		$data['label'] = "Pillar Name";
		$data['action'] = "settings_pillar";
		$data['del'] = "setting_pillar_del";
		$r_page = "settings_pillar";

		$data['page'] = $this->SGODModel->get_all('sgod_settings_pillar');
		$id = $this->input->post('id');
		$data['pillar'] = $this->SGODModel->get_data_by_id('sgod_settings_pillar', 'id', $id);
		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('setting_pillar', $data);

		if ($this->input->post('submit')) {
			$this->SGODModel->insert_app_pillar();
			redirect(base_url() . 'Page/' . $r_page);
		}

		if ($this->input->post('edit')) {
			//$data['cat'] = $this->SGODModel->get_data_by_id('app_cat', 'id',$id);
			$this->SGODModel->update_app_pillar();
			redirect(base_url() . 'Page/' . $r_page);
		}
	}
	public function setting_pillar_del()
	{
		$this->SGODModel->delete(3, 'id', 'sgod_settings_pillar');
		redirect(base_url() . 'Page/settings_pillar');
	}

	function settings_domain()
	{
		$data['title'] = "Domain List";
		$data['m_title'] = "Add New Domain";
		$data['e_title'] = "Update Domain";
		$data['label'] = "Domain Name";
		$data['action'] = "settings_domain";
		$data['del'] = "setting_domain_del";
		$r_page = "settings_domain";

		$data['page'] = $this->SGODModel->get_all('sgod_settings_domain');
		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('setting_domain', $data);

		if ($this->input->post('submit')) {
			$this->SGODModel->insert_domain();
			redirect(base_url() . 'Page/' . $r_page);
		}

		if ($this->input->post('edit')) {
			$this->SGODModel->update_domain();
			redirect(base_url() . 'Page/' . $r_page);
		}
	}
	public function setting_domain_del()
	{
		$this->SGODModel->delete(3, 'id', 'sgod_settings_domain');
		redirect(base_url() . 'Page/settings_domain');
	}

	function settings_strand()
	{
		$data['title'] = "Strand List";
		$data['m_title'] = "Add New Strand";
		$data['e_title'] = "Update Strand";
		$data['label'] = "Strand Name";
		$data['action'] = "settings_strand";
		$data['del'] = "setting_strand_del";
		$r_page = "settings_strand";

		$data['page'] = $this->SGODModel->get_all('sgod_settings_strand');
		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('setting_strand', $data);

		if ($this->input->post('submit')) {
			$this->SGODModel->insert_Strand();
			redirect(base_url() . 'Page/' . $r_page);
		}

		if ($this->input->post('edit')) {
			$this->SGODModel->update_Strand();
			redirect(base_url() . 'Page/' . $r_page);
		}
	}
	public function setting_strand_del()
	{
		$this->SGODModel->delete(3, 'id', 'sgod_settings_Strand');
		redirect(base_url() . 'Page/settings_Strand');
	}

	function settings_pias()
	{
		$data['title'] = "PIAs List";
		$data['m_title'] = "Add New PIAs";
		$data['e_title'] = "Update PIAs";
		$data['label'] = "PIAs Name";
		$data['action'] = "settings_pias";
		$data['del'] = "setting_pias_del";
		$r_page = "settings_pias";

		$data['page'] = $this->SGODModel->one_cond('sgod_settings_pias', 'school_id', $this->session->username);
		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('setting_pias', $data);

		if ($this->input->post('submit')) {
			$this->SGODModel->insert_pias();
			redirect(base_url() . 'Page/' . $r_page);
		}

		if ($this->input->post('edit')) {
			$this->SGODModel->update_pias();
			redirect(base_url() . 'Page/' . $r_page);
		}
	}
	public function setting_pias_del()
	{
		$this->SGODModel->delete(3, 'id', 'sgod_settings_pias');
		redirect(base_url() . 'Page/settings_pias');
	}



	function generate_rca()
	{
		$school_id = $this->session->username;
		$fy = $_SESSION['fy'];
		$bcode = $_SESSION['aip'];

		$data['mon'] = $this->input->post('month');
		if (!isset($_SESSION['omt'])) {
			$_SESSION['omt'] = 0;
		}

		$data['data_row'] = $this->SGODModel->get_aip_row($school_id, $fy, $bcode);

		$data['school'] = $this->SGODModel->one_cond_row('schools', 'schoolId', $school_id);

		$bs = $this->SGODModel->one_cond_row('sgod_school_allocation', 'alloc_batch', $bcode);
		$data['bs'] = $this->SGODModel->one_cond_row('sgod_school_allocation', 'alloc_batch', $bcode);

		if ($bs->fund_type == 0) {
			$data['mr'] = $this->SGODModel->aip_category('sgod_aip', $school_id, $fy, $bcode, 'MINOR REPAIR');
			$data['mb'] = $this->SGODModel->aip_category('sgod_aip', $school_id, $fy, $bcode, 'MANDATORY BILLS');
			$data['tli'] = $this->SGODModel->aip_category('sgod_aip', $school_id, $fy, $bcode, 'TEACHING-LEARNING INSTRUCTION');
			$data['tst'] = $this->SGODModel->aip_category('sgod_aip', $school_id, $fy, $bcode, 'TRAININGS/SEMINAR/TRAVEL');
		} else {
			$data['mr'] = $this->SGODModel->aip_category_sned('sgod_aip', $school_id, $fy, $bcode, 'MINOR REPAIR');
			$data['mb'] = $this->SGODModel->aip_category_sned('sgod_aip', $school_id, $fy, $bcode, 'MANDATORY BILLS');
			$data['tli'] = $this->SGODModel->aip_category_sned('sgod_aip', $school_id, $fy, $bcode, 'TEACHING-LEARNING INSTRUCTION');
			$data['tst'] = $this->SGODModel->aip_category_sned('sgod_aip', $school_id, $fy, $bcode, 'TRAININGS/SEMINAR/TRAVEL');
		}





		$this->load->view('rca_generate', $data);
	}

	function generate_rca_admin()
	{
		$school_id = $this->input->post('sid');
		$fy = $this->input->post('fy');
		$bcode = $this->input->post('bcode');

		$data['mon'] = $this->input->post('month');
		if (!isset($_SESSION['omt'])) {
			$_SESSION['omt'] = 0;
		}

		$data['school'] = $this->SGODModel->one_cond_row('schools', 'schoolId', $school_id);
		$data['data_row'] = $this->SGODModel->get_aip_row($school_id, $fy, $bcode);


		$data['mr'] = $this->SGODModel->aip_category('sgod_aip', $school_id, $fy, $bcode, 'MINOR REPAIR');
		$data['mb'] = $this->SGODModel->aip_category('sgod_aip', $school_id, $fy, $bcode, 'MANDATORY BILLS');
		$data['tli'] = $this->SGODModel->aip_category('sgod_aip', $school_id, $fy, $bcode, 'TEACHING-LEARNING INSTRUCTION');
		$data['tst'] = $this->SGODModel->aip_category('sgod_aip', $school_id, $fy, $bcode, 'TRAININGS/SEMINAR/TRAVEL');
		$this->load->view('rca_generate', $data);
	}

	function rca_view()
	{
		$school_id = $this->uri->segment(3);
		$fy = $this->uri->segment(4);
		$bcode = $this->uri->segment(5);

		$data['mon'] = $this->uri->segment(6);
		$data['data_row'] = $this->SGODModel->get_aip_row($school_id, $fy, $bcode);

		$data['school'] = $this->SGODModel->one_cond_row('schools', 'schoolId', $school_id);

		$bs = $this->SGODModel->one_cond_row('sgod_school_allocation', 'alloc_batch', $bcode);

		if ($bs->fund_type == 0) {
			$data['mr'] = $this->SGODModel->aip_category('sgod_aip', $school_id, $fy, $bcode, 'MINOR REPAIR');
			$data['mb'] = $this->SGODModel->aip_category('sgod_aip', $school_id, $fy, $bcode, 'MANDATORY BILLS');
			$data['tli'] = $this->SGODModel->aip_category('sgod_aip', $school_id, $fy, $bcode, 'TEACHING-LEARNING INSTRUCTION');
			$data['tst'] = $this->SGODModel->aip_category('sgod_aip', $school_id, $fy, $bcode, 'TRAININGS/SEMINAR/TRAVEL');
		} else {
			$data['mr'] = $this->SGODModel->aip_category_sned('sgod_aip', $school_id, $fy, $bcode, 'MINOR REPAIR');
			$data['mb'] = $this->SGODModel->aip_category_sned('sgod_aip', $school_id, $fy, $bcode, 'MANDATORY BILLS');
			$data['tli'] = $this->SGODModel->aip_category_sned('sgod_aip', $school_id, $fy, $bcode, 'TEACHING-LEARNING INSTRUCTION');
			$data['tst'] = $this->SGODModel->aip_category_sned('sgod_aip', $school_id, $fy, $bcode, 'TRAININGS/SEMINAR/TRAVEL');
		}
		$this->load->view('rca_generate', $data);
	}



	function generate_rcav2()
	{
		$school_id = $this->session->username;
		$fy = $_SESSION['fy'];
		$bcode = $_SESSION['aip'];

		$data['mon'] = $this->uri->segment(3);
		if (!isset($_SESSION['omt'])) {
			$_SESSION['omt'] = 0;
		}

		$data['school'] = $this->SGODModel->one_cond_row('schools', 'schoolId', $school_id);

		$data['mr'] = $this->SGODModel->aip_category('sgod_aip', $school_id, $fy, $bcode, 'MINOR REPAIR');
		$data['mb'] = $this->SGODModel->aip_category('sgod_aip', $school_id, $fy, $bcode, 'MANDATORY BILLS');
		$data['tli'] = $this->SGODModel->aip_category('sgod_aip', $school_id, $fy, $bcode, 'TEACHING-LEARNING INSTRUCTION');
		$data['tst'] = $this->SGODModel->aip_category('sgod_aip', $school_id, $fy, $bcode, 'TRAININGS/SEMINAR/TRAVEL');
		$this->load->view('rca_generate', $data);
	}


	function settings_matatag()
	{
		$data['title'] = "Matatag List";
		$data['m_title'] = "Add New";
		$data['e_title'] = "Update";
		$data['label'] = "Name";
		$data['action'] = "settings_matatag";
		$data['del'] = "setting_matatag_del";
		$r_page = "settings_matatag";

		$data['page'] = $this->SGODModel->get_all('sgod_settings_matatag');
		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('setting_matatag', $data);

		if ($this->input->post('submit')) {
			$this->SGODModel->insert_matatag();
			redirect(base_url() . 'Page/' . $r_page);
		}

		if ($this->input->post('edit')) {
			$this->SGODModel->update_matatag();
			redirect(base_url() . 'Page/' . $r_page);
		}
	}
	public function setting_matatag_del()
	{
		$this->SGODModel->delete(3, 'id', 'sgod_settings_matatag');
		redirect(base_url() . 'Page/settings_matatag');
	}


	function sections()
	{
		$result['data'] = $this->SGODModel->viewSections();
		$this->load->view('sections', $result);

		if ($this->input->post('submit')) {
			$this->SGODModel->insert_sections();
			$this->session->set_flashdata('success', ' Added Successfully!');
			redirect('Page/sections');
		}
	}

	function sections_edit()
	{
		$result['data'] = $this->SGODModel->one_cond_row('sgod_sections', 'id', $this->uri->segment(3));
		$this->load->view('sections_edit', $result);

		if ($this->input->post('submit')) {
			$this->SGODModel->update_sections();
			$this->session->set_flashdata('success', ' Updated Successfully!');
			redirect('Page/sections');
		}
	}

	function submission()
	{
		$param = $this->session->userdata('secGroup');
		$result['data'] = $this->SGODModel->viewSectionsChecking($param);

		$result['quarter'] = $this->input->post('quarter');
		$result['year'] = $this->input->post('year');
		$result['week'] = $this->input->post('weekAcc');
		$result['month'] = $this->input->post('month');

		$this->load->view('sc', $result);
	}

	public function delete_sec()
	{
		$id = $this->input->get('id');
		$this->db->query("delete  from sgod_sections where id='" . $id . "'");
		$this->session->set_flashdata('success', 'Deleted successfully!');
		redirect('Page/sections');
	}


	function viewSecAccomplishments()
	{
		$secGroup = $this->session->userdata('secGroup');
		$section = $this->session->userdata('section');

		if ($this->input->post('submit')) {
			$month = $this->input->post('month');
			$week = $this->input->post('week');
			$year = $this->input->post('year');
			$secGroup = $this->session->userdata('secGroup');
			$section = $this->session->userdata('section');

			$result['data'] = $this->SGODModel->get_accomplishment_by_date($year, $month, $week, $section, $secGroup);
		} else {
			$result['data'] = $this->SGODModel->viewSecAccomplishments($section, $secGroup);
		}

		$this->load->view('sect_accomplishments', $result);
	}

	function copy_acc($param)
	{
		$this->SGODModel->copy_row($param);
		redirect('Page/viewSecAccomplishments');
	}

	function aip()
	{
		$result['title'] = "ANNUAL IMPLEMENTATION PLAN";
		$result['b_label'] = "+ Add New";
		$result['b_link'] = "aip_new";
		$result['fy'] = $_SESSION['fy'];
		$result['bcode'] = $_SESSION['aip'];
		$result['data'] = $this->SGODModel->two_cond('sgod_aip', 'school_id', $this->session->username, 'b_code', $_SESSION['aip']);
		$result['ssa'] = $this->SGODModel->two_cond('sgod_school_allocation', 'schoolID', $this->session->username, 'alloc_year', $_SESSION['fy']);

		$result['alloc'] = $this->SGODModel->two_cond_row('sgod_school_allocation', 'schoolID', $this->session->username, 'alloc_batch', $_SESSION['aip']);


		$result['aip_s'] = $this->SGODModel->two_cond_row('sgod_aip_submit', 'school_id', $this->session->username, 'b_code', $_SESSION['aip']);
		$result['aip_r'] = $this->SGODModel->two_cond_orderby('sgod_aip_request', 'school_id', $this->session->username, 'b_code', $_SESSION['aip'], 'id', 'DESC');


		$result['submit_aip'] = $this->SGODModel->two_cond('sgod_aip_submit', 'b_code', $_SESSION['aip'], 'school_id', $this->session->username);

		$result['pillar'] = $this->SGODModel->get_all('sgod_settings_pillar');
		$result['domain'] = $this->SGODModel->get_all('sgod_settings_domain');
		$result['pias'] = $this->SGODModel->get_all('sgod_settings_pias');
		$result['strand'] = $this->SGODModel->get_all('sgod_settings_strand');

		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('aip_view', $result);
	}

	function aip_request()
	{
		$this->SGODModel->request();
		$this->SGODModel->request_insert_track();
		redirect(base_url() . 'Page/aip');
	}

	function aip_filterd()
	{
		$result['title'] = "ANNUAL IMPLEMENTATION PLAN";
		$result['b_label'] = "+ ADD NEW";
		$result['b_link'] = "aip_new";

		$result['pillar'] = $this->SGODModel->get_all('sgod_settings_pillar');
		$result['domain'] = $this->SGODModel->get_all('sgod_settings_domain');
		$result['pias'] = $this->SGODModel->get_all('sgod_settings_pias');
		$result['strand'] = $this->SGODModel->get_all('sgod_settings_strand');

		$result['data'] = $this->SGODModel->get_all_aip_by();
		$this->load->view('aip_view', $result);
	}
	function aip_action()
	{
		$result['title'] = "TAKE ACTION ANNUAL IMPLEMENTATION PLAN";

		$this->load->view('aip_action', $result);

		if ($this->input->post('submit')) {
			$this->SGODModel->insert_aip_action();
			$this->SGODModel->update_aip_action();
			redirect(base_url() . 'Page/aip_action_list');
		}
	}
	function approved_aip()
	{
		$this->SGODModel->aip_approved();
		$this->SGODModel->update_aip_action();
		redirect(base_url() . 'Page/aip_sub');
	}

	function approved_aip_sned()
	{
		$this->SGODModel->aip_approved_sned();
		$this->SGODModel->update_aip_action_sned();
		redirect(base_url() . 'Page/aip_sub_sned');
	}

	function approved_aip_review()
	{
		$this->SGODModel->aip_review_track('AIP Reviewed');
		$this->SGODModel->update_aip_action_review(3, 'AIP Reviewed');
		redirect(base_url() . 'Page/aip_sub_review');
	}

	function approved_aip_funds()
	{
		$this->SGODModel->aip_review_track('Funds Available');
		$this->SGODModel->update_aip_action_review(4, 'Funds Available');
		redirect(base_url() . 'Page/aip_sub_funds');
	}

	function approved_aip_sgod_chief()
	{
		$this->SGODModel->aip_review_track('Approved');
		$this->SGODModel->update_aip_action_review(1, 'Approved');
		redirect(base_url() . 'Page/aip_sub_sgod_chief');
	}

	function remarks_aip()
	{
		$id = $this->input->post('id');

		$this->SGODModel->aip_remarks();
		redirect(base_url() . 'Page/aip_track/' . $id);
	}

	function open_aip()
	{
		$this->SGODModel->aip_open();
		$this->SGODModel->update_aip_open();
		$this->SGODModel->request_update();
		redirect(base_url() . 'Page/aip_sub');
	}

	function submit_aip()
	{
		$fy = $_SESSION['fy'];
		$id = $this->session->username;
		$bcode = $_SESSION['aip'];

		$this->SGODModel->aip_submit($fy, $id, $bcode);
		$this->SGODModel->aip_track($this->db->insert_id());
		redirect(base_url() . 'Page/aip_action_list');
	}

	function submit_aip_sned()
	{
		$fy = $_SESSION['fy'];
		$id = $this->session->username;
		$bcode = $_SESSION['aip'];

		$this->SGODModel->aip_submit_sned($fy, $id, $bcode);
		$this->SGODModel->aip_track($this->db->insert_id());
		redirect(base_url() . 'Page/aip_action_list');
	}

	function submit_aip_sbfp()
	{
		$fy = $_SESSION['fy'];
		$id = $this->session->username;
		$bcode = $_SESSION['aip'];

		$this->SGODModel->aip_submit_sbfp($fy, $id, $bcode);
		$this->SGODModel->aip_track($this->db->insert_id());
		redirect(base_url() . 'Page/aip_action_list');
	}

	function aip_action_list()
	{
		$result['title'] = "ANNUAL IMPLEMENTATION PLAN ACTION LIST";

		$result['data'] = $this->SGODModel->two_cond('sgod_aip_submit', 'school_id', $this->session->username, 'fy', $_SESSION['fy']);

		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('aip_action_view', $result);
	}

	function aip_sub()
	{
		$result['title'] = "SUBMITTED PLANS";
		//$fys = $this->session->cur_fy;
		$fys = $this->session->cur_fy;
		// if (!isset($fys)) {
		// 	redirect(base_url() . 'Page/fy_setting');
		// }

		//$aip_setting = $this->Common->one_cond_row_select('mis_settings','sgod_sign_type,settingsID','settingsID',1);

		//if($aip_setting->sgod_sign_type == 0){
		$result['data'] = $this->SGODModel->two_cond('sgod_aip_submit', 'fy', $fys, 'status', 0);
		$result['district'] = $this->Common->no_cond('district');

		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('aip_action_view', $result);
		// }else{

		// 	$this->load->view('templates/head');
		// 	$this->load->view('templates/header');
		// 	$this->load->view('aip_action_view_v2', $result);
		// }



	}

	function aip_sub_sned()
	{
		$result['title'] = "SUBMITTED PLANS";
		$fys = $this->session->cur_fy;

		$result['data'] = $this->SGODModel->two_cond('sgod_aip_submit', 'fy', $fys, 'status', 2);

		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('aip_action_view_sned', $result);
	}

	function aip_sub_sbfp()
	{
		$result['title'] = "SUBMITTED PLANS";
		$fys = $this->session->cur_fy;

		$result['data'] = $this->SGODModel->two_cond('sgod_aip_submit', 'fy', $fys, 'status', 6);

		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('aip_action_view_sbfp', $result);
	}

	function aip_sub_review()
	{
		$result['title'] = "SUBMITTED PLANS";
		$fys = $this->session->cur_fy;

		$result['data'] = $this->SGODModel->two_cond('sgod_aip_submit', 'fy', $fys, 'status', 0);

		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('aip_action_view_review', $result);
	}

	function aip_sub_funds()
	{
		$result['title'] = "SUBMITTED PLANS";
		$fys = $this->session->cur_fy;

		$result['data'] = $this->SGODModel->two_cond('sgod_aip_submit', 'fy', $fys, 'status', 3);

		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('aip_action_view_funds', $result);
	}

	function aip_sub_sgod_chief()
	{
		$result['title'] = "SUBMITTED PLANS";
		$fys = $this->session->cur_fy;

		$result['data'] = $this->SGODModel->two_cond('sgod_aip_submit', 'fy', $fys, 'status', 4);

		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('aip_action_view_sgod_chief', $result);
	}


	function aip_evaluate()
	{
		$result['title'] = "SUBMITTED PLANS";
		$fys = $this->session->cur_fy;

		$result['data'] = $this->SGODModel->two_cond('sgod_aip_submit', 'fy', $fys, 'status', 0);

		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('aip_evaluate', $result);
	}

	function sned_approved()
	{
		$result['title'] = "SUBMITTED PLANS";
		$fys = $this->session->cur_fy;

		$result['data'] = $this->SGODModel->two_cond('sgod_aip_submit', 'fy', $this->session->fy_admin, 'remarks', 'SNED Approved');

		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('aip_sned', $result);
	}

	// function aip_approved(){
	// 	$result['title'] = "SUBMITTED PLANS";
	// 	$fys = $this->session->cur_fy;

	// 	$result['data'] = $this->SGODModel->two_cond('sgod_aip_submit', 'fy', $fys,'status',1);

	// 	$this->load->view('templates/head');
	// 	$this->load->view('templates/header');
	// 	$this->load->view('aip_action_view', $result);
	// }

	function aip_approved()
	{
		$result['title'] = "Submission by District";

		$result['data'] = $this->SGODModel->no_cond_group_by('schools', 'district');
		$result['gl'] = $this->SGODModel->no_cond_group_by('schools', 'course');

		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('aip_districtv2', $result);
	}




	function aip_requested()
	{
		$result['title'] = "SUBMITTED PLANS";

		$fys = $this->session->cur_fy;

		$result['data'] = $this->SGODModel->two_cond('sgod_aip_request', 'fy', $this->session->cur_fy, 'stat', 0);

		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('aip_request', $result);
	}

	function aip_sub_district()
	{
		$result['title'] = "Submission by District";

		$result['data'] = $this->SGODModel->no_cond_group_by('schools', 'district');

		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('aip_district', $result);
	}

	function aip_sub_dist_school()
	{
		$result['title'] = "Submission by School";

		$school = urldecode($this->uri->segment('3'));

		$result['data'] = $this->SGODModel->one_cond('schools', 'district', $school);


		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('aip_sub_schools', $result);
	}

	function aip_sub_school()
	{
		$result['title'] = "Submition by School";

		$fy = $this->SGODModel->get_last_record('sgod_fy');

		$school_id = $this->uri->segment('3');

		$result['data'] = $this->SGODModel->two_cond('sgod_aip_submit', 'fy', $this->session->cur_fy, 'school_id', $school_id);


		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('aip_action_view', $result);
	}

	function aip_submitted()
	{
		$result['title'] = "Submission by School";

		$school = urldecode($this->uri->segment('3'));

		$result['data'] = $this->SGODModel->one_cond('schools', 'district', $school);


		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('aip_submitted', $result);
	}

	function aip_submitted_approved()
	{
		$result['title'] = "Submission by School";

		$school = urldecode($this->uri->segment('3'));

		$result['data'] = $this->SGODModel->one_cond('schools', 'district', $school);


		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('aip_submitted_approved', $result);
	}

	function aip_nsubmitted()
	{
		$result['title'] = "Submission by School";

		$school = urldecode($this->uri->segment('3'));

		$result['data'] = $this->SGODModel->one_cond('schools', 'district', $school);


		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('aip_not_submitted', $result);
	}

	function aip_track()
	{
		$result['title'] = "ANNUAL IMPLEMENTATION PLAN ACTION LIST";


		$id = $this->uri->segment(3);

		$result['data'] = $this->SGODModel->one_cond_orderby('sgod_aip_track', 'submit_id', $id, 'id', 'desc');
		$result['aip'] = $this->SGODModel->one_cond_row('sgod_aip_submit', 'id', $id);


		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('aip_track_view', $result);
	}

	function aip_noti_track()
	{
		$id = $this->uri->segment(3);
		$this->SGODModel->update_noti();
		redirect(base_url() . 'Page/aip_track/' . $id);
	}

	function fy_setting_school()
	{
		$result['title'] = "FISCAL YEAR";

		$result['label'] = "FISCAL YEAR";

		// if ($this->input->post('submit')) {
		// 	$this->SGODModel->insert_fy();
		// 	$this->session->set_flashdata('success', 'Saved successfully.');

		// 	redirect(base_url() . 'Page/fy_setting');
		// }

		$result['alloc_year'] = $this->Common->no_cond_group('sgod_school_allocation', 'alloc_year');


		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('setting_fy_school', $result);
	}



	function fy_setting()
	{
		$result['title'] = "FISCAL YEAR";

		$result['label'] = "FISCAL YEAR";

		if ($this->input->post('submit')) {
			//$this->SGODModel->insert_fy();
			$_SESSION['cur_fy'] = $this->input->post('fy');
			$this->session->set_flashdata('success', 'Saved successfully.');

			redirect(base_url() . 'Page/aip_sub');
		}


		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('setting_fy', $result);
	}

	function aip_stat_sub()
	{

		$result['title'] = "AIP School Submitted";

		$result['fy'] =  $this->input->post('fy');

		$result['data'] = $this->SGODModel->get_all_orderby('schools', 'district', 'desc');


		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('aip_status_list', $result);
	}

	function aip_stat_sub_summary()
	{

		$result['title'] = "AIP School Submitted";

		$result['fy'] =  $this->uri->segment(3);

		$result['data'] = $this->SGODModel->get_all_orderby('schools', 'district', 'desc');


		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('aip_status_list', $result);
	}

	function aip_stat_notsub()
	{

		$result['title'] = "AIP School Not Submitted";

		$result['fy'] =  $this->input->post('fy');

		$result['data'] = $this->SGODModel->get_all_orderby('schools', 'district', 'desc');




		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('aip_status_nlist', $result);
	}
	function aip_stat_notsub_summary()
	{

		$result['title'] = "AIP School Not Submitted";

		$result['fy'] =  $this->uri->segment(3);

		$result['data'] = $this->SGODModel->get_all_orderby('schools', 'district', 'desc');




		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('aip_status_nlist', $result);
	}

	function aip_stat()
	{
		$result['title'] = "FASCAL YEAR";

		$result['label'] = "FASCAL YEAR";

		if ($this->input->post('submit')) {
			$this->SGODModel->fys();
			$this->session->set_flashdata('success', 'Saved successfully.');

			redirect(base_url() . 'Page/fy_setting');
		}


		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('aip_status', $result);
	}

	function aip_track_delete()
	{
		$id = $this->uri->segment(4);
		$this->SGODModel->delete('3', 'id', 'sgod_aip_track');
		$this->session->set_flashdata('danger', ' Settings was deleted');

		redirect(base_url() . 'Page/aip_track/' . $id);
	}

	function app_percentage()
	{
		if ($this->input->post('total') == 100) {
			if ($this->input->post('id') == 0) {
				$this->SGODModel->insert_app_percentage();
			} else {
				$this->SGODModel->update_app_percentage();
			}

			$this->session->set_flashdata('success', 'Saved successfully.');
			redirect(base_url() . 'Page/view_app');
		} else {
			$this->session->set_flashdata('danger', 'The encoded percentage breakdown is not equal to 100%.');
			redirect(base_url() . 'Page/view_app');
		}
	}


	function generate_app()
	{
		$result['title'] = " ANNUAL PROCUREMENT PLAN (APP)";
		$result['school_id'] = $this->session->username;
		$result['fy'] = $_SESSION['fy'];
		$result['b_code'] = $_SESSION['aip'];


		$fy = $_SESSION['fy'];
		$b_code = $_SESSION['aip'];
		$school_id = $this->session->username;

		$data['data_row'] = $this->SGODModel->get_aip_row($school_id, $fy, $b_code);

		$result['school'] = $this->SGODModel->one_cond_row('schools', 'schoolId', $school_id);
		$result['abp'] = $this->SGODModel->one_cond_row('sgod_app_percentage', 'b_code', $_SESSION['aip']);
		$result['ssa'] = $this->SGODModel->three_cond_row('sgod_school_allocation', 'schoolID', $school_id, 'alloc_batch', $b_code, 'alloc_year', $fy);
		$result['aip_submit'] = $this->SGODModel->aip_related_row('sgod_aip_submit', $school_id, $fy, $b_code);
		$result['data_row'] = $this->SGODModel->get_aip_row($school_id, $fy, $b_code);

		$this->load->view('app_generate', $result);
	}

	function generate_appv2()
	{
		$result['title'] = " ANNUAL PROCUREMENT PLAN (APP)";
		$result['school_id'] = $this->session->username;
		$result['fy'] = $_SESSION['fy'];
		$result['b_code'] = $_SESSION['aip'];


		$fy = $_SESSION['fy'];
		$b_code = $_SESSION['aip'];
		$school_id = $this->session->username;

		$data['data_row'] = $this->SGODModel->get_aip_row($school_id, $fy, $b_code);

		$result['school'] = $this->SGODModel->one_cond_row('schools', 'schoolId', $school_id);
		$result['abp'] = $this->SGODModel->one_cond_row('sgod_app_percentage', 'b_code', $_SESSION['aip']);
		$result['ssa'] = $this->SGODModel->three_cond_row('sgod_school_allocation', 'schoolID', $school_id, 'alloc_batch', $b_code, 'alloc_year', $fy);
		$result['aip_submit'] = $this->SGODModel->aip_related_row('sgod_aip_submit', $school_id, $fy, $b_code);
		$result['data_row'] = $this->SGODModel->get_aip_row($school_id, $fy, $b_code);

		$result['ft'] = $this->SGODModel->two_cond_row('sgod_school_allocation', 'schoolID', $this->session->username, 'alloc_batch', $_SESSION['aip']);


		$this->load->view('app_generatev2', $result);
	}

	function generate_appv3()
	{
		$result['title'] = " ANNUAL PROCUREMENT PLAN (APP)";
		$result['school_id'] = $this->session->username;
		$result['fy'] = $_SESSION['fy'];
		$result['b_code'] = $_SESSION['aip'];


		$fy = $_SESSION['fy'];
		$b_code = $_SESSION['aip'];
		$school_id = $this->session->username;

		$data['data_row'] = $this->SGODModel->get_aip_row($school_id, $fy, $b_code);

		$result['school'] = $this->SGODModel->one_cond_row('schools', 'schoolId', $school_id);
		$result['abp'] = $this->SGODModel->one_cond_row('sgod_app_percentage', 'b_code', $_SESSION['aip']);
		$result['ssa'] = $this->SGODModel->three_cond_row('sgod_school_allocation', 'schoolID', $school_id, 'alloc_batch', $b_code, 'alloc_year', $fy);
		$result['aip_submit'] = $this->SGODModel->aip_related_row('sgod_aip_submit', $school_id, $fy, $b_code);
		$result['data_row'] = $this->SGODModel->get_aip_row($school_id, $fy, $b_code);

		$result['ft'] = $this->SGODModel->two_cond_row('sgod_school_allocation', 'schoolID', $this->session->username, 'alloc_batch', $_SESSION['aip']);


		$this->load->view('app_generatev2', $result);
	}

	function generate_ppmp()
	{
		$result['title'] = "PROJECT PROCUREMENT MANAGEMENT PLAN";
		$result['school_id'] = $this->session->username;
		$result['fy'] = $_SESSION['fy'];
		$result['b_code'] = $_SESSION['aip'];


		$fy = $_SESSION['fy'];
		$b_code = $_SESSION['aip'];
		$school_id = $this->session->username;

		$result['school'] = $this->SGODModel->one_cond_row('schools', 'schoolId', $school_id);
		$result['abp'] = $this->SGODModel->one_cond_row('sgod_app_percentage', 'b_code', $_SESSION['aip']);
		$result['ssa'] = $this->SGODModel->three_cond_row('sgod_school_allocation', 'schoolID', $school_id, 'alloc_batch', $b_code, 'alloc_year', $fy);

		$this->load->view('ppmp_generate', $result);
	}

	function generate_ppmp_admin()
	{
		$result['title'] = "PROJECT PROCUREMENT MANAGEMENT PLAN";
		$result['school_id'] = $this->uri->segment(3);
		$result['fy'] = $this->uri->segment(4);
		$result['b_code'] = $this->uri->segment(5);


		$fy = $this->uri->segment(4);
		$b_code = $this->uri->segment(5);
		$school_id = $this->uri->segment(3);

		$result['data_row'] = $this->SGODModel->get_aip_row($school_id, $fy, $b_code);

		$result['school'] = $this->SGODModel->one_cond_row('schools', 'schoolId', $school_id);
		$result['abp'] = $this->SGODModel->one_cond_row('sgod_app_percentage', 'b_code', $b_code);
		$result['ssa'] = $this->SGODModel->three_cond_row('sgod_school_allocation', 'schoolID', $school_id, 'alloc_batch', $b_code, 'alloc_year', $fy);

		$this->load->view('ppmp_generate', $result);
	}



	function generate_app_admin()
	{
		$result['title'] = " ANNUAL PROCUREMENT PLAN (APP)";
		$result['school_id'] = $this->uri->segment(3);
		$result['fy'] = $this->uri->segment(4);
		$result['b_code'] = $this->uri->segment(5);

		$school_id = $this->uri->segment(3);
		$fy = $this->uri->segment(4);
		$b_code = $this->uri->segment(5);

		$result['data_row'] = $this->SGODModel->get_aip_row($school_id, $fy, $b_code);

		$result['abp'] = $this->SGODModel->one_cond_row('sgod_app_percentage', 'b_code', $b_code);
		$result['school'] = $this->SGODModel->one_cond_row('schools', 'schoolId', $school_id);
		$result['ssa'] = $this->SGODModel->three_cond_row('sgod_school_allocation', 'schoolID', $school_id, 'alloc_batch', $b_code, 'alloc_year', $fy);

		$this->load->view('app_generate', $result);
	}

	function generate_app_admin_sned()
	{
		$result['title'] = " ANNUAL PROCUREMENT PLAN (APP)";
		$result['school_id'] = $this->uri->segment(3);
		$result['fy'] = $this->uri->segment(4);
		$result['b_code'] = $this->uri->segment(5);

		$school_id = $this->uri->segment(3);
		$fy = $this->uri->segment(4);
		$b_code = $this->uri->segment(5);

		$result['data_row'] = $this->SGODModel->get_aip_row($school_id, $fy, $b_code);

		$result['abp'] = $this->SGODModel->one_cond_row('sgod_app_percentage', 'b_code', $b_code);
		$result['school'] = $this->SGODModel->one_cond_row('schools', 'schoolId', $school_id);
		$result['ssa'] = $this->SGODModel->three_cond_row('sgod_school_allocation', 'schoolID', $school_id, 'alloc_batch', $b_code, 'alloc_year', $fy);

		$result['ft'] = $this->SGODModel->two_cond_row('sgod_school_allocation', 'schoolID', $school_id, 'alloc_batch', $b_code);

		$this->load->view('app_generatev2', $result);
	}

	function view_app()
	{
		$result['title'] = "ANNUAL PROCUREMENT PLAN (APP)";
		$result['data'] = $this->SGODModel->three_cond('sgod_app', 'school_id', $this->session->username, 'b_code', $_SESSION['aip'], 'bs', 'MOOE');
		$result['ssa'] = $this->SGODModel->two_cond('sgod_school_allocation', 'schoolID', $this->session->username, 'alloc_year', $_SESSION['fy']);

		$result['ft'] = $this->SGODModel->two_cond_row('sgod_school_allocation', 'schoolID', $this->session->username, 'alloc_batch', $_SESSION['aip']);


		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('app_view', $result);

		if ($this->input->post('submit')) {
			$this->SGODModel->reupdate_app();
			$this->session->set_flashdata('success', 'Saved successfully.');

			redirect(base_url() . 'Page/view_app');
		}
	}

	function view_appv2()
	{
		$result['title'] = "ANNUAL PROCUREMENT PLAN (APP)";
		$result['data'] = $this->Common->three_cond_group('sgod_app', 'school_id', $this->session->username, 'b_code', $_SESSION['aip'], 'bs', 'MOOE', 'aip_id');
		$result['ssa'] = $this->SGODModel->two_cond('sgod_school_allocation', 'schoolID', $this->session->username, 'alloc_year', $_SESSION['fy']);


		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('app_viewv2', $result);

		if ($this->input->post('submit')) {
			$this->SGODModel->reupdate_app();
			$this->session->set_flashdata('success', 'Saved successfully.');

			redirect(base_url() . 'Page/view_app');
		}
	}

	function app_edit()
	{
		$result['title'] = "UPDATE ANNUAL PROCUREMENT PLAN (APP)";
		$result['data'] = $this->SGODModel->one_cond_row('sgod_app', 'id', $this->uri->segment(3));


		if ($this->input->post('submit')) {
			$this->SGODModel->reupdate_app();
			$this->session->set_flashdata('success', 'Saved successfully.');

			redirect(base_url() . 'Page/view_app');
		}

		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('app_update', $result);
	}

	function gapp_edit()
	{
		$result['title'] = "UPDATE ANNUAL PROCUREMENT PLAN (APP)";
		$result['data'] = $this->SGODModel->one_cond_row('sgod_app', 'id', $this->uri->segment(3));


		if ($this->input->post('submit')) {
			$aip_id = $this->input->post('aip_id');
			$this->SGODModel->reupdate_app();
			$this->session->set_flashdata('success', 'Saved successfully.');

			redirect(base_url() . 'Page/group_app/' . $aip_id);
		}

		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('gapp_update', $result);
	}

	function app_delete()
	{

		$id = $this->uri->segment(4);
		$this->SGODModel->delete('3', 'id', 'sgod_app');

		//$aipmat = $this->SGODModel->one_cond_row('sgod_app', 'aip_id', $id);
		$aipmat = $this->SGODModel->one_cond('sgod_app', 'aip_id', $id);
		$rows = '';
		foreach ($aipmat as $row) {
			$rows .= $row->materials . ', ';
		}
		$materials = rtrim($rows, ', ');

		$this->SGODModel->update_aip_material($materials);





		$this->session->set_flashdata('danger', ' Settings was deleted');

		redirect(base_url() . 'Page/group_app/' . $id);
	}

	function gapp_new()
	{
		$result['title'] = "NEW ANNUAL PROCUREMENT PLAN (APP)";
		$result['data'] = $this->SGODModel->one_cond_row('sgod_app', 'id', $this->uri->segment(3));


		if ($this->input->post('submit')) {
			$aip_id = $this->input->post('aip_id');
			$this->SGODModel->reupdate_app();
			$this->session->set_flashdata('success', 'Saved successfully.');

			redirect(base_url() . 'Page/group_app/' . $aip_id);
		}

		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('gapp_create', $result);
	}


	function app_new()
	{
		$result['title'] = "NEW ANNUAL PROCUREMENT PLAN (APP)";
		$result['data'] = $this->SGODModel->one_cond_row('sgod_app', 'id', $this->uri->segment(3));


		if ($this->input->post('submit')) {
			$this->SGODModel->reupdate_app();
			$this->session->set_flashdata('success', 'Saved successfully.');

			redirect(base_url() . 'Page/view_app');
		}

		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('app_create', $result);
	}

	function group_app($param)
	{
		$result['title'] = "MATERIALS ANNUAL PROCUREMENT PLAN (APP)";
		$result['data'] = $this->SGODModel->one_cond('sgod_app', 'aip_id', $param);
		$result['aip'] = $this->SGODModel->one_cond_row('sgod_aip', 'id', $param);
		$result['aip_s'] = $this->SGODModel->two_cond_row('sgod_aip_submit', 'school_id', $this->session->username, 'b_code', $_SESSION['aip']);


		if ($this->input->post('submit')) {
			$this->SGODModel->insert_materials();
			$this->SGODModel->update_aip_materials();
			$this->session->set_flashdata('success', 'Saved successfully.');
			redirect(base_url() . 'Page/group_app/' . $param);
		}


		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('app_group', $result);
	}


	function aip_new()
	{
		$fys = $this->session->cur_fy;
		$result['title'] = "ADD NEW ANNUAL IMPLEMENTATION PLAN";
		$result['data'] = $this->SGODModel->get_all('sgod_aip');
		$result['pillar'] = $this->SGODModel->get_all_orderby('sgod_settings_pillar', 'pillar', 'ASC');
		$result['domain'] = $this->SGODModel->get_all_orderby('sgod_settings_domain', 'domain', 'ASC');

		$result['pias'] = $this->SGODModel->two_cond('sgod_settings_pias', 'year', $_SESSION['fy'], 'school_id', $this->session->username);


		$result['matatag'] = $this->SGODModel->get_all_orderby('sgod_settings_matatag', 'id', 'DESC');
		$result['agenda'] = $this->Common->get_agenda_indicators();
		$result['strand'] = $this->SGODModel->get_all_orderby('sgod_settings_strand', 'strand', 'ASC');
		$result['bs'] = $this->SGODModel->get_all_orderby('sgod_settings_bs', 'id', 'ASC');
		$result['last'] = $this->SGODModel->last_record('sgod_aip', 'id', 'DESC');
		$result['pil'] = $this->SGODModel->table_num('sgod_aip');



		$result['ssa'] = $this->SGODModel->two_cond('sgod_school_allocation', 'schoolID', $this->session->username, 'alloc_year', $_SESSION['fy']);
		$result['group'] = $this->Common->no_cond('sgod_alloc_group', 'alloc_group');

		$result['ssa'] = $this->SGODModel->get_all('sgod_setting_io');

		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('aip_add', $result);

		if ($this->input->post('submit')) {
			$fy = $this->input->post('fy');
			$b_code = $this->input->post('b_code');

			$check = $this->Common->four_cond_count_row('sgod_aip_submit', 'school_id', $this->session->username, 'fy', $fy, 'remarks', 'Approved', 'b_code', $b_code);
			if ($check->num_rows() == 0) {
				$this->SGODModel->insert_aip();
				$this->SGODModel->insert_app();
				$this->session->set_flashdata('success', 'Saved successfully.');
			} else {
				$this->session->set_flashdata('danger', 'AIP Locked.');
			}
			redirect(base_url() . 'Page/aip_new');
		}
	}

	function aip_edit($param)
	{

		$fys = $this->session->cur_fy;
		$result['title'] = "UPDATE ANNUAL IMPLEMENTATION PLAN";
		$result['data'] = $this->SGODModel->get_single_by_id('id', 'sgod_aip', $param);
		$result['pillar'] = $this->SGODModel->get_all_orderby('sgod_settings_pillar', 'pillar', 'ASC');
		$result['domain'] = $this->SGODModel->get_all_orderby('sgod_settings_domain', 'domain', 'ASC');
		//ss$result['pias'] = $this->SGODModel->one_cond_orderby('sgod_settings_pias', 'year', $_SESSION['fy'], 'pias', 'ASC');
		$result['pias'] = $this->SGODModel->two_cond('sgod_settings_pias', 'year', $_SESSION['fy'], 'school_id', $this->session->username);
		$result['matatag'] = $this->SGODModel->get_all_orderby('sgod_settings_matatag', 'matatag', 'ASC');
		$result['strand'] = $this->SGODModel->get_all_orderby('sgod_settings_strand', 'strand', 'ASC');
		$result['ssa'] = $this->SGODModel->get_all('sgod_school_allocation', 'schoolID', $this->session->username, 'alloc_year', $fys);
		$result['bs'] = $this->SGODModel->get_all_orderby('sgod_settings_bs', 'id', 'ASC');
		$result['agenda'] = $this->Common->get_agenda_indicators();

		$result['group'] = $this->Common->no_cond('sgod_alloc_group', 'alloc_group');


		if ($this->input->post('submit')) {
			$this->SGODModel->update_aip($param);
			//$this->SGODModel->delete('3', 'aip_id', 'sgod_app');
			//$this->SGODModel->update_app();
			$this->session->set_flashdata('success', 'Updated successfully');
			redirect(base_url() . 'Page/aip');
		}

		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('aip_update', $result);
	}

	function aip_delete()
	{
		$this->SGODModel->delete('3', 'id', 'sgod_aip');
		$this->session->set_flashdata('danger', ' Settings was deleted');
		$this->SGODModel->delete('3', 'aip_id', 'sgod_app');

		redirect(base_url() . 'Page/aip');
	}

	function sop()
	{
		$result['title'] = "SCHOOL OPERATIONAL PLAN";
		$result['b_label'] = "+ ADD NEW";
		$result['b_link'] = "#";
		$result['ssa'] = $this->SGODModel->one_cond('sgod_school_allocation', 'schoolID', $this->session->username);
		//$result['data']=$this->SGODModel->get_all('sgod_aip');

		$result['data'] = $this->Common->two_cond('sgod_aip', 'school_id', $this->session->username, 'b_code', $_SESSION['aip']);

		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('sop_view', $result);

		if ($this->input->post('submit')) {
			$this->SGODModel->insert_sop();
			$this->session->set_flashdata('success', 'Saved successfully.');

			redirect(base_url() . 'Page/sop');
		}
	}

	function sop_edit($param)
	{
		$result['title'] = "UPDATE TARGET";
		$result['b_label'] = "+ ADD NEW";
		$result['b_link'] = "#";

		$result['sop'] = $this->SGODModel->one_cond_row('sgod_sop', 'id', $param);


		if ($this->input->post('submit')) {
			$this->SGODModel->update_sop($param);
			$this->session->set_flashdata('success', 'Saved successfully.');

			redirect(base_url() . 'Page/sop');
		}

		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('sop_update', $result);
	}



	function generate_sop()
	{
		$result['title'] = "ANNUAL IMPLEMENTATION PLAN";
		$result['b_label'] = "+ ADD NEW";
		$result['b_link'] = "#";

		$school_id = $this->session->username;
		$fy = $_SESSION['fy'];
		$bcode = $_SESSION['aip'];

		$result['fy'] = $_SESSION['fy'];



		$result['data'] = $this->SGODModel->get_aip($school_id, $fy, $bcode);
		$result['data_row'] = $this->SGODModel->get_aip_row($school_id, $fy, $bcode);
		$result['school'] = $this->SGODModel->one_cond_row('schools', 'schoolId', $school_id);
		$result['aip_submit'] = $this->SGODModel->aip_related_row('sgod_aip_submit', $school_id, $fy, $bcode);
		$result['data_row'] = $this->SGODModel->get_aip_row($school_id, $fy, $bcode);

		$result['ft'] = $this->SGODModel->two_cond_row('sgod_school_allocation', 'schoolID', $this->session->username, 'alloc_batch', $_SESSION['aip']);


		$this->load->view('sop_generate', $result);
	}

	function generate_sop_admin()
	{
		$result['title'] = "ANNUAL IMPLEMENTATION PLAN";
		$result['b_label'] = "+ ADD NEW";
		$result['b_link'] = "#";

		$school_id = $this->uri->segment(3);
		$fy = $this->uri->segment(4);
		$bcode = $this->uri->segment(5);

		$result['fy'] = $this->uri->segment(4);

		$result['data'] = $this->SGODModel->get_aip($school_id, $fy, $bcode);
		$result['data_row'] = $this->SGODModel->get_aip_row($school_id, $fy, $bcode);
		$result['school'] = $this->SGODModel->one_cond_row('schools', 'schoolId', $school_id);


		$this->load->view('sop_generate', $result);
	}

	function generate_ca()
	{
		$result['title'] = "ca";
		$result['school'] = $this->SGODModel->one_cond_row('schools', 'schoolId', $this->uri->segment(3));
		$result['aip'] = $this->SGODModel->two_cond_row('sgod_aip_submit', 'school_id', $this->uri->segment(3), 'b_code', $this->uri->segment(4));
		$result['alloc'] = $this->SGODModel->one_cond_row('sgod_school_allocation', 'alloc_batch', $this->uri->segment(4));
		$result['a'] = $this->SGODModel->two_cond_orderby('sgod_aip_track', 'remarks', 'Approved', 'submit_id', $this->uri->segment(5), 'id', 'ASC');
		$this->load->view('ca', $result);
	}


	function generate_aip()
	{
		$result['title'] = "ANNUAL IMPLEMENTATION PLAN";
		$result['b_label'] = "+ ADD NEW";
		$result['b_link'] = "#";

		$school_id = $this->session->username;
		$fy = $_SESSION['fy'];
		$bcode = $_SESSION['aip'];

		echo $_SESSION['fy'];

		$result['fy'] = $_SESSION['fy'];

		$result['data'] = $this->SGODModel->get_aip($school_id, $fy, $bcode);
		$result['data_row'] = $this->SGODModel->get_aip_row($school_id, $fy, $bcode);

		$result['school'] = $this->SGODModel->one_cond_row('schools', 'schoolId', $school_id);
		$result['aip_submit'] = $this->SGODModel->aip_related_row('sgod_aip_submit', $school_id, $fy, $bcode);

		$result['aip_sc'] = $this->Common->four_cond_count_row('sgod_aip_submit', 'school_id', $school_id, 'fy', $fy, 'b_code', $bcode, 'remarks', 'Approved');

		$result['ft'] = $this->SGODModel->two_cond_row('sgod_school_allocation', 'schoolID', $this->session->username, 'alloc_batch', $_SESSION['aip']);



		$this->load->view('aip_generate', $result);
	}

	function aip_admin()
	{
		$result['title'] = "ANNUAL IMPLEMENTATION PLAN";
		$result['b_label'] = "+ ADD NEW";
		$result['b_link'] = "#";

		$result['fy'] = $this->uri->segment(4);

		$school_id = $this->uri->segment(3);
		$fy = $this->uri->segment(4);
		$bcode = $this->uri->segment(5);

		$result['data'] = $this->SGODModel->get_aip($school_id, $fy, $bcode);
		$result['data_row'] = $this->SGODModel->get_aip_row($school_id, $fy, $bcode);
		$result['school'] = $this->SGODModel->one_cond_row('schools', 'schoolId', $school_id);
		$result['aip_submit'] = $this->SGODModel->aip_related_row('sgod_aip_submit', $school_id, $fy, $bcode);

		$this->load->view('aip_generate', $result);
	}

	function generate_aip_filter()
	{
		$result['title'] = "Generate ANNUAL IMPLEMENTATION PLAN";
		$this->load->view('aip_generate_filter', $result);
	}

	function acc()
	{
		$data['page'] = $this->SGODModel->get_accomplishment();
		$data['section'] = $data['page']['section'];
		$id = $data['page']['id'];
		$data['acc'] = $this->SGODModel->get_all_data_where_single('sgod_accomplishments', 'year', '2023');

		$this->load->view('accomplishment', $data);
	}

	function secaccview($param)
	{
		$result['data'] = $this->SGODModel->get_table_where($param, 'sgod_acc_image');
		$result['sf'] = $this->SGODModel->get_table_where($param, 'sgod_files');
		$this->load->view('sec_acc_view', $result);
	}

	function report()
	{
		$quarter = $this->input->post('quarter');
		$year = $this->input->post('year');
		$week = $this->input->post('weekAcc');
		$month = $this->input->post('month');
		$category = $this->input->post('activityCategory');

		$result['cat'] = $category;

		if ($category == 'all') {
			$result['accomplish'] = $this->SGODModel->get_accomplishment_by('Accomplishment', $quarter, $year, $week, $month);
			$result['update'] = $this->SGODModel->get_accomplishment_by('Updates', $quarter, $year, $week, $month);
		} elseif ($category == 'accomplishment') {
			$result['accomplish'] = $this->SGODModel->get_accomplishment_by($category, $quarter, $year, $week, $month);
		} else {
			$result['update'] = $this->SGODModel->get_accomplishment_by($category, $quarter, $year, $week, $month);
		}
		$result['acc'] = $this->SGODModel->get_accomplishment_by_row($quarter, $year, $week, $month);

		if ($week == "") {
			$result['r'] = "Quarter";
			$result['rr'] = "ly";
			$result['q'] = $this->input->post('quarter');
		} else {
			$result['r'] = "Week";
			$result['rr'] = "ly";
			$result['q'] = $this->input->post('weekAcc');
		}



		$this->load->view('sec_report_view', $result);
	}
	function reportv2()
	{

		$quarter = $this->input->post('quarter');
		$year = $this->input->post('year');
		$week = $this->input->post('weekAcc');
		$month = $this->input->post('month');
		$category = $this->input->post('activityCategory');
		$secGroup = $this->session->userdata('secGroup');

		$result['cat'] = $category;

		if ($category == 'all') {
			$result['accomplish'] = $this->SGODModel->get_accomplishment_by('Accomplishment', $quarter, $year, $week, $month, $secGroup);
			$result['update'] = $this->SGODModel->get_accomplishment_by('Updates', $quarter, $year, $week, $month, $secGroup);
		} elseif ($category == 'accomplishment') {
			$result['accomplish'] = $this->SGODModel->get_accomplishment_by($category, $quarter, $year, $week, $month, $secGroup);
		} else {
			$result['update'] = $this->SGODModel->get_accomplishment_by($category, $quarter, $year, $week, $month, $secGroup);
		}
		$result['acc'] = $this->SGODModel->get_accomplishment_by_row($quarter, $year, $week, $month, $secGroup);

		if ($week == "") {
			$result['r'] = "Quarter";
			$result['rr'] = "ly";
			$result['q'] = $this->input->post('quarter');
		} else {
			$result['r'] = "Week";
			$result['rr'] = "ly";
			$result['q'] = $this->input->post('weekAcc');
		}



		$this->load->view('sec_report_viewv2', $result);
	}


	function sec_filter()
	{
		$this->load->view('sec_filter_report');
	}
	function sec_filterv2()
	{
		$this->load->view('sec_filter_reportv2');
	}
	function sfy()
	{
		$this->load->view('sec_filter_year');
	}

	function report_sfy()
	{

		$quarter = $this->input->post('quarter');
		$year = $this->input->post('year');
		$category = $this->input->post('activityCategory');
		$secGroup = $this->session->userdata('secGroup');

		$result['cat'] = $category;

		if ($category == 'all') {
			$result['accomplish'] = $this->SGODModel->get_year_accomplishment('Accomplishment', $year, $secGroup);
			$result['update'] = $this->SGODModel->get_year_accomplishment('Updates', $year, $secGroup);

			$result['accomplishment'] = $this->SGODModel->get_year_accomplishment('Accomplishment', $year, $secGroup);
			$result['updates'] = $this->SGODModel->get_year_accomplishment('Updates', $year, $secGroup);

			$result['sections'] = $this->SGODModel->get_acc_group_by_section_year($year);
		} elseif ($category == 'accomplishment') {
			$result['accomplish'] = $this->SGODModel->get_year_accomplishment($category, $year, $secGroup);
			$result['accomplishment'] = $this->SGODModel->get_year_accomplishment('Accomplishment', $year, $secGroup);
		} else {
			$result['update'] = $this->SGODModel->get_year_accomplishment($category, $year, $secGroup);
			$result['updates'] = $this->SGODModel->get_year_accomplishment('Updates', $year, $secGroup);
		}

		$result['acc'] = $this->SGODModel->get_accomplish_by_row_year($year);


		$this->load->view('sfy_view', $result);
	}

	function sec_filter_admin()
	{
		$this->load->view('sec_filter_report_admin');
	}
	function report_admin()
	{

		$quarter = $this->input->post('quarter');
		$year = $this->input->post('year');
		$week = $this->input->post('weekAcc');
		$month = $this->input->post('month');
		$category = $this->input->post('activityCategory');

		$result['cat'] = $category;

		if ($category == 'all') {
			$result['accomplish'] = $this->SGODModel->get_all_accomplishment('Accomplishment', $quarter, $year, $week, $month);
			$result['update'] = $this->SGODModel->get_all_accomplishment('Updates', $quarter, $year, $week, $month);

			$result['accomplishment'] = $this->SGODModel->get_all_accomplishment('Accomplishment', $quarter, $year, $week, $month);
			$result['updates'] = $this->SGODModel->get_all_accomplishment('Updates', $quarter, $year, $week, $month);

			$result['sections'] = $this->SGODModel->get_accomplishment_group_by_section($quarter, $year, $week, $month);
		} elseif ($category == 'accomplishment') {
			$result['accomplish'] = $this->SGODModel->get_all_accomplishment($category, $quarter, $year, $week, $month);
			$result['accomplishment'] = $this->SGODModel->get_all_accomplishment('Accomplishment', $quarter, $year, $week, $month);
		} else {
			$result['update'] = $this->SGODModel->get_all_accomplishment($category, $quarter, $year, $week, $month);
			$result['updates'] = $this->SGODModel->get_all_accomplishment('Updates', $quarter, $year, $week, $month);
		}
		$result['acc'] = $this->SGODModel->get_accomplish_by_row($quarter, $year, $week, $month);

		if ($week == "") {
			$result['r'] = "Quarter";
			$result['rr'] = "ly";
			$result['q'] = $this->input->post('quarter');
		} else {
			$result['r'] = "Week";
			$result['rr'] = "ly";
			$result['q'] = $this->input->post('weekAcc');
		}
		$this->load->view('sec_report_view_admin', $result);
	}
	function report_adminv2()
	{

		$quarter = $this->input->post('quarter');
		$year = $this->input->post('year');
		$week = $this->input->post('weekAcc');
		$month = $this->input->post('month');
		$category = $this->input->post('activityCategory');
		$secGroup = $this->session->userdata('secGroup');

		$result['cat'] = $category;

		if ($category == 'all') {
			$result['accomplish'] = $this->SGODModel->get_all_accomplishment('Accomplishment', $quarter, $year, $week, $month, $secGroup);
			$result['update'] = $this->SGODModel->get_all_accomplishment('Updates', $quarter, $year, $week, $month, $secGroup);

			$result['accomplishment'] = $this->SGODModel->get_all_accomplishment('Accomplishment', $quarter, $year, $week, $month, $secGroup);
			$result['updates'] = $this->SGODModel->get_all_accomplishment('Updates', $quarter, $year, $week, $month, $secGroup);

			$result['sections'] = $this->SGODModel->get_acc_group_by_section($quarter, $year, $week, $month);
		} elseif ($category == 'accomplishment') {
			$result['accomplish'] = $this->SGODModel->get_all_accomplishment($category, $quarter, $year, $week, $month, $secGroup);
			$result['accomplishment'] = $this->SGODModel->get_all_accomplishment('Accomplishment', $quarter, $year, $week, $month, $secGroup);
		} else {
			$result['update'] = $this->SGODModel->get_all_accomplishment($category, $quarter, $year, $week, $month, $secGroup);
			$result['updates'] = $this->SGODModel->get_all_accomplishment('Updates', $quarter, $year, $week, $month, $secGroup);
		}
		$result['acc'] = $this->SGODModel->get_accomplish_by_row($quarter, $year, $week, $month, $secGroup);

		if ($week == "") {
			$result['r'] = "Quarter";
			$result['rr'] = "ly";
			$result['q'] = $this->input->post('quarter');
		} else {
			$result['r'] = "Week";
			$result['rr'] = "ly";
			$result['q'] = $this->input->post('weekAcc');
		}
		$this->load->view('sec_report_view_adminv2', $result);
	}

	public function multiple_files()
	{
		$this->load->library('upload');
		$image = array();
		$ImageCount = count($_FILES['image_name']['name']);
		for ($i = 0; $i < $ImageCount; $i++) {
			$_FILES['file']['name']       = $_FILES['image_name']['name'][$i];
			$_FILES['file']['type']       = $_FILES['image_name']['type'][$i];
			$_FILES['file']['tmp_name']   = $_FILES['image_name']['tmp_name'][$i];
			$_FILES['file']['error']      = $_FILES['image_name']['error'][$i];
			$_FILES['file']['size']       = $_FILES['image_name']['size'][$i];

			// File upload configuration
			$uploadPath = 'upload/tr_images';
			$config['upload_path'] = $uploadPath;
			$config['allowed_types'] = 'jpg|jpeg|png|gif';

			// Load and initialize upload library
			$this->load->library('upload', $config);
			$this->upload->initialize($config);

			// Upload file to server
			if ($this->upload->do_upload('file')) {
				// Uploaded file data
				$imageData = $this->upload->data();
				$uploadImgData[$i]['file'] = $imageData['file_name'];
				$uploadImgData[$i]['acc_id'] = $this->input->post('id');
			}
		}
		if (!empty($uploadImgData)) {
			// Insert files data into the database
			$id = $this->input->post('id');
			$this->SGODModel->multiple_images($uploadImgData);
			redirect(base_url() . 'Page/secaccview/' . $id);
		}
	}
	public function atr()
	{
		$this->load->library('upload');
		$image = array();
		$ImageCount = count($_FILES['image_name']['name']);
		for ($i = 0; $i < $ImageCount; $i++) {
			$_FILES['file']['name']       = $_FILES['image_name']['name'][$i];
			$_FILES['file']['type']       = $_FILES['image_name']['type'][$i];
			$_FILES['file']['tmp_name']   = $_FILES['image_name']['tmp_name'][$i];
			$_FILES['file']['error']      = $_FILES['image_name']['error'][$i];
			$_FILES['file']['size']       = $_FILES['image_name']['size'][$i];

			// File upload configuration
			$uploadPath = 'upload/training_resources';
			$config['upload_path'] = $uploadPath;
			$config['allowed_types'] = 'pdf';

			// Load and initialize upload library
			$this->load->library('upload', $config);
			$this->upload->initialize($config);

			// Upload file to server
			if ($this->upload->do_upload('file')) {
				// Uploaded file data
				$imageData = $this->upload->data();
				$uploadImgData[$i]['file'] = $imageData['file_name'];
				$uploadImgData[$i]['file_title'] = $this->input->post('atr');
				$uploadImgData[$i]['acc_id'] = $this->input->post('id');
			}
		}
		if (!empty($uploadImgData)) {
			// Insert files data into the database
			$id = $this->input->post('id');
			$this->SGODModel->atr($uploadImgData);
			redirect(base_url() . 'Page/secaccview/' . $id);
		}
	}


	public function delete_attach($param)
	{
		$result['img'] = $this->SGODModel->get_single_table_by_id('id', 'sgod_acc_image', $param);
		$filename = $result['img']['file'];
		$id = $result['img']['acc_id'];
		$this->SGODModel->delete_group($param, $filename, 'tr_images', 'sgod_acc_image');
		redirect('Page/secaccview/' . $id);
	}
	public function delete_file($param)
	{
		$result['img'] = $this->SGODModel->get_single_table_by_id('id', 'sgod_files', $param);
		$filename = $result['img']['file'];
		$id = $result['img']['acc_id'];
		$this->SGODModel->delete_group($param, $filename, 'training_resources', 'sgod_files');
		redirect('Page/secaccview/' . $id);
	}

	public function addTrainingResources()
	{
		$config['upload_path'] = '/upload/training_resources/';
		$config['allowed_types'] = '*';
		$config['max_size'] = 15120;
		//$config['max_width'] = 1500;
		//$config['max_height'] = 1500;

		$this->load->library('upload', $config);

		if (!$this->upload->do_upload('nonoy')) {
			$msg = array('error' => $this->upload->display_errors());

			$this->load->view('sec_acc_view', $msg);
		} else {
			$data = array('image_metadata' => $this->upload->data());
			//get data from the form
			$IDNumber = $this->input->post('IDNumber');
			//$filename=$this->input->post('nonoy');
			$filename = $this->upload->data('nonoy');
			$docName = $this->input->post('docName');
			$date = date("Y-m-d");
			$que = $this->db->query("insert into sgod_files values('','$IDNumber','$docName','$filename','$date')");
			$this->session->set_flashdata('msg', '<div class="alert alert-success text-center"><b>Uploaded Succesfully!</b></div>');

			redirect('Page/sec_acc_view');
		}
	}


	function addAccomplishments()
	{
		$this->load->view('sect_accomplishments_add');

		if ($this->input->post('submit')) {
			//get data from the form


			$quarter = $this->input->post('quarter');
			$year = $this->input->post('year');
			$monthAcc = $this->input->post('monthAcc');
			$weekAcc = $this->input->post('weekAcc');
			$section = $this->session->userdata('section');
			$activity = addslashes($this->input->post('activity'));
			$particulars = addslashes($this->input->post('particulars'));
			$activityCategory = $this->input->post('activityCategory');
			$venue = addslashes($this->input->post('venue'));
			$targetDate = $this->input->post('targetDate');
			$dateConducted = $this->input->post('dateConducted');
			$resources = addslashes($this->input->post('resources'));
			$notes = addslashes($this->input->post('notes'));
			$remarks = addslashes($this->input->post('remarks'));
			$encoder = $this->session->userdata('username');
			$secGroup = $this->session->userdata('secGroup');

			$perIndicators = $this->input->post('perIndicators');
			$target = $this->input->post('target');
			$achieved = $this->input->post('achieved');
			$percentageAccom = $this->input->post('percentageAccom');


			$que = $this->db->query("insert into sgod_accomplishments (quarter, year, monthAcc, weekAcc, section, activity, particulars, activityCategory, venue, targetDate, dateConducted, encoder, resources, notes, perIndicators, target, achieved, percentageAccom, remarks, secGroup) values('$quarter','$year','$monthAcc','$weekAcc','$section','$activity','$particulars','$activityCategory','$venue','$targetDate','$dateConducted','$encoder','$resources','$notes','$perIndicators','$target','$achieved','$percentageAccom','$remarks','$secGroup')");
			$this->session->set_flashdata('success', ' Add Successfully!');
			redirect('Page/viewSecAccomplishments');
		}
	}

	function updateAccomplishments()
	{
		$id = $this->input->get('id');
		$result['data'] = $this->SGODModel->accombyid($id);
		$this->load->view('sect_accom_update', $result);

		if ($this->input->post('update')) {
			//get data from the form

			$quarter = $this->input->post('quarter');
			$year = $this->input->post('year');
			$weekAcc = $this->input->post('weekAcc');
			$monthAcc = $this->input->post('monthAcc');
			$particulars = addslashes($this->input->post('particulars'));
			$targetDate = $this->input->post('targetDate');
			$section = $this->session->userdata('section');
			$activity = addslashes($this->input->post('activity'));
			$activityCategory = $this->input->post('activityCategory');
			$venue = $this->input->post('venue');
			$dateConducted = $this->input->post('dateConducted');
			$encoder = $this->session->userdata('username');
			$resources = addslashes($this->input->post('resources'));
			$notes = addslashes($this->input->post('notes'));
			$remarks = addslashes($this->input->post('remarks'));

			$perIndicators = $this->input->post('perIndicators');
			$target = $this->input->post('target');
			$achieved = $this->input->post('achieved');
			$percentageAccom = $this->input->post('percentageAccom');

			$que = $this->db->query("update sgod_accomplishments set quarter='$quarter', year='$year', monthAcc='$monthAcc', weekAcc='$weekAcc', section='$section', activity='$activity', activityCategory='$activityCategory', particulars='$particulars', venue='$venue', targetDate='$targetDate', dateConducted='$dateConducted',resources='$resources',notes='$notes',perIndicators='$perIndicators',target='$target',achieved='$achieved',percentageAccom='$percentageAccom',remarks='$remarks' where id='" . $id . "'");
			$this->session->set_flashdata('success', ' Updated Successfully!');
			redirect('Page/viewSecAccomplishments');
		}
	}

	function deleteAccomplishment()
	{
		$id = $this->input->get('id');

		$que = $this->db->query("delete from sgod_accomplishments where id='" . $id . "'");
		$this->session->set_flashdata('danger', ' Deleted successfully.');
		redirect('Page/viewSecAccomplishments');
	}

	function implementation_plans()
	{
		//$result['fys'] = $this->session->cur_fy;
		//$fy = $this->session->cur_fy;
		$fy = $this->input->post('fy');
		$result['fys'] = $fy;
		$result['ssa'] = $this->SGODModel->two_cond('sgod_school_allocation', 'schoolID', $this->session->username, 'alloc_year', $fy);

		if ($this->input->post('aip')) {
			$code = $this->input->post('code');
			$fy = $this->input->post('fy');
			session_regenerate_id();
			$_SESSION['aip'] = $code;
			$_SESSION['fy'] = $fy;
			session_write_close();
			redirect(base_url() . 'Page/aip/');
		}

		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('ip', $result);
		$this->load->view('templates/footer');
	}

	function settings_io()
	{
		$data['title'] = "Intermediate Outcome List";
		$data['m_title'] = "Add New Intermediate Outcome";
		$data['e_title'] = "Update Intermediate Outcome";
		$data['label'] = "Intermediate Outcome Description";
		$data['action'] = "settings_io";
		$data['del'] = "setting_io_del";
		$r_page = "settings_io";


		$data['page'] = $this->SGODModel->get_all('sgod_setting_io');
		$data['pillar'] = $this->SGODModel->get_all('sgod_settings_pillar');
		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('setting_io', $data);

		if ($this->input->post('submit')) {
			$this->SGODModel->insert_io();
			redirect(base_url() . 'Page/' . $r_page);
		}

		if ($this->input->post('edit')) {
			$this->SGODModel->update_io();
			redirect(base_url() . 'Page/' . $r_page);
		}
	}
	public function setting_io_del()
	{
		$this->SGODModel->delete(3, 'id', 'sgod_setting_io');
		redirect(base_url() . 'Page/settings_io');
	}

	function settings_bs()
	{
		$data['title'] = "Budget Source List";
		$data['m_title'] = "Add New";
		$data['e_title'] = "Update";
		$data['label'] = "Description";
		$data['action'] = "settings_bs";
		$data['del'] = "setting_bs_del";
		$r_page = "settings_bs";

		$data['page'] = $this->SGODModel->get_all('sgod_settings_bs');
		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('setting_bs', $data);

		if ($this->input->post('submit')) {
			$this->SGODModel->insert_bs();
			redirect(base_url() . 'Page/' . $r_page);
		}

		if ($this->input->post('edit')) {
			$this->SGODModel->update_bs();
			redirect(base_url() . 'Page/' . $r_page);
		}
	}
	public function setting_bs_del()
	{
		$this->SGODModel->delete(3, 'id', 'sgod_settings_bs');
		redirect(base_url() . 'Page/settings_bs');
	}

	public function count_rows($table)
	{
		$result = $this->db->get($table);
		return $result;
	}


	function dra_aip()
	{
		$tdate = $this->input->post('tdate');
		if (isset($tdate)) {

			$result['aip'] = $this->SGODModel->two_cond('sgod_aip_track', 'remarks', 'Approved', 'tdate', $tdate);

			$this->load->view('dra_aip', $result);
		} else {
			redirect(base_url() . 'Page/aip_sub');
		}
	}

	function aip_sub_print()
	{

		if ($this->uri->segment(3) == 0) {
			$result['aip'] = $this->SGODModel->one_cond('sgod_aip_submit', 'remarks', 'Submitted');
		} else {
			$result['aip'] = $this->SGODModel->one_cond('sgod_aip_submit', 'remarks', 'Approved');
		}

		$this->load->view('dra_aip_print', $result);
	}

	function aip_dist_sub_print()
	{

		if ($this->uri->segment(3) == 0) {
			$result['aip'] = $this->SGODModel->two_cond('sgod_aip_submit', 'remarks', 'Submitted', 'fy', $this->session->cur_fy);
		} else {
			$result['aip'] = $this->SGODModel->two_cond('sgod_aip_submit', 'remarks', 'Approved', 'fy', $this->session->cur_fy);
		}

		$this->load->view('dra_aip_printv2', $result);
	}

	function aip_dist_sub_printv2()
	{
		redirect(base_url() . 'Page/aip_dist_sub_print/' . $this->input->post('stat') . '/' . $this->input->post('dist'));
	}

	function manage_evaluator()
	{
		$data['title'] = "Manage Evaluator";


		//$data['data'] = $this->Common->one_cond('sgod_setting_io');


		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('me', $data);
	}

	// Document Verifier //

	function document_verifier()
	{
		$data['title'] = 'Document Verifier';

		$data['data'] = $this->Common->no_cond('document_verifier');

		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('odv', $data);

		if ($this->input->post('submit')) {
			$this->Ren_model->new_document();
			$this->session->set_flashdata('success', ' New Document Entry.');
			//redirect(base_url() . 'document_verifier');
		}
	}

	function document_verifier_add()
	{
		$this->Reg->new_document();
		$this->session->set_flashdata('success', ' New Document Entry.');
		redirect(base_url() . 'Page/document_verifier');
	}

	function document_verifier_qr()
	{
		$data['data'] = $this->Common->one_cond_row('document_verifier', 'id', $this->uri->segment(3));

		$this->load->view('qr', $data);
	}
	function verify()
	{
		$data['data'] = $this->Common->one_cond_row('document_verifier', 'id', $this->uri->segment(3));

		$this->load->view('qr_verify', $data);
	}


	function sbfp_form()
	{

		$_SESSION['sbfp_fy'] = $this->input->post('sy');
		$_SESSION['yl'] = $this->input->post('YearLevel');
		$_SESSION['sec'] = $this->input->post('Section');
		$_SESSION['sid'] = $this->input->post('schoolID');

		$result['title'] = "Master List Beneficiaries for School-Based Feeding Program (SBFP)";

		if ($this->session->d_id == 0) {
			$result['school'] = $this->Common->no_cond_group_ob('schools', 'schoolID', 'schoolName', 'ASC');
		} else {
			$district = $this->Common->one_cond_row('district', 'id', $this->session->d_id);
			$result['school'] = $this->Common->one_cond_group_ob('schools', 'district', $district->discription, 'schoolID', 'schoolName', 'ASC');
		}


		if ($this->session->sp == 0) {
			$result['sbf'] = $this->Common->four_cond('semesterstude', 'schoolID', $this->session->c_id, 'SY', $this->input->post('sy'), 'Section', $this->input->post('Section'), 'YearLevel', $this->input->post('YearLevel'));

			$result['sy'] = $this->Common->one_cond_group('semesterstude', 'schoolID', $this->session->c_id, 'SY');
			$result['yl'] = $this->Common->one_cond_group('semesterstude', 'schoolID', $this->session->c_id, 'YearLevel');
			$result['section'] = $this->Common->one_cond_group('semesterstude', 'schoolID', $this->session->c_id, 'Section');
		} else {
			$result['sbf'] = $this->Common->four_cond('semesterstude', 'schoolID', $this->input->post('schoolID'), 'SY', $this->input->post('sy'), 'Section', $this->input->post('Section'), 'YearLevel', $this->input->post('YearLevel'));

			$result['sy'] = $this->Common->no_cond_group('semesterstude', 'SY');
			$result['yl'] = $this->Common->no_cond_group('semesterstude', 'YearLevel');
			$result['section'] = $this->Common->no_cond_group('semesterstude', 'Section');
		}
		//$result['sbf'] = $this->Common->three_cond('semesterstude', 'schoolID', $this->session->c_id,'SY',$this->input->post('sy'),'YearLevel',$this->input->post('YearLevel'));



		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('sbfp', $result);
		$this->load->view('templates/footer');
	}



	function consultations()
	{

		$result['title'] = "Consultations";
		$result['sbf'] = $this->Common->one_cond('sbfp_data', 'schoolID', $this->session->c_id);

		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('med-consultations', $result);
		$this->load->view('templates/footer');
	}

	function labRequest()
	{

		$result['title'] = "Lab Request";
		$result['sbf'] = $this->Common->one_cond('sbfp_data', 'schoolID', $this->session->c_id);

		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('med-lab-request', $result);
		$this->load->view('templates/footer');
	}


	function sbfp_dashboard()
	{
		$result['title'] = "SBFP DASHBOARD";

		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('sbfp_dashboard', $result);
		$this->load->view('templates/footer');
	}

	function baseline()
	{
		if (!isset($this->session->bmi_sy)) {
			redirect(base_url() . 'Sbfp/sbfp_bmi');
		}
		$result['title'] = "BASELINE WEIGHING";
		$result['sbf'] = $this->Common->two_join_five_cond('semester_sbfp', 'studeprofile', '*', 'a.StudentNumber=b.StudentNumber', 'a.schoolID', $this->session->c_id, 'a.SY', $this->session->bmi_sy, 'a.Section', $this->session->bmi_Section, 'a.YearLevel', $this->session->bmi_YearLevel, 'a.w_group', 'Baseline', 'LastName', 'ASC');


		$this->load->view('bw', $result);
	}

	function baseline_dn()
	{
		$id =  (int)$this->session->bmi_school_id;

		$result['title'] = "BASELINE WEIGHING";
		$result['sbf'] = $this->Common->two_join_five_cond('semester_sbfp', 'studeprofile', '*', 'a.StudentNumber=b.StudentNumber', 'a.schoolID', $id, 'a.SY', $this->session->bmi_sy, 'a.Section', $this->session->bmi_Section, 'a.YearLevel', $this->session->bmi_YearLevel, 'a.w_group', 'Baseline', 'LastName', 'ASC');


		$this->load->view('bw', $result);
	}

	function baseline2nd_dn()
	{
		$id =  (int)$this->session->bmi_school_id;

		$result['title'] = "BASELINE WEIGHING";
		$result['sbf'] = $this->Common->two_join_five_cond('semester_sbfp', 'studeprofile', '*', 'a.StudentNumber=b.StudentNumber', 'a.schoolID', $id, 'a.SY', $this->session->bmi_sy, 'a.Section', $this->session->bmi_Section, 'a.YearLevel', $this->session->bmi_YearLevel, 'a.w_group', '2nd', 'LastName', 'ASC');


		$this->load->view('bw2nd', $result);
	}

	function baseline3nd_dn()
	{
		$id =  (int)$this->session->bmi_school_id;

		$result['title'] = "BASELINE WEIGHING";
		$result['sbf'] = $this->Common->two_join_five_cond('semester_sbfp', 'studeprofile', '*', 'a.StudentNumber=b.StudentNumber', 'a.schoolID', $id, 'a.SY', $this->session->bmi_sy, 'a.Section', $this->session->bmi_Section, 'a.YearLevel', $this->session->bmi_YearLevel, 'a.w_group', '3rd', 'LastName', 'ASC');


		$this->load->view('bw3rd', $result);
	}

	function baseline2nd()
	{
		if (!isset($this->session->bmi_sy)) {
			redirect(base_url() . 'Sbfp/sbfp_bmi');
		}
		$result['title'] = "BASELINE WEIGHING";
		$result['sbf'] = $this->Common->two_join_five_cond('semester_sbfp', 'studeprofile', '*', 'a.StudentNumber=b.StudentNumber', 'a.schoolID', $this->session->c_id, 'a.SY', $this->session->bmi_sy, 'a.Section', $this->session->bmi_Section, 'a.YearLevel', $this->session->bmi_YearLevel, 'a.w_group', '2nd', 'LastName', 'ASC');


		$this->load->view('bw2nd', $result);
	}

	function baseline3nd()
	{
		if (!isset($this->session->bmi_sy)) {
			redirect(base_url() . 'Sbfp/sbfp_bmi');
		}
		$result['title'] = "BASELINE WEIGHING";
		$result['sbf'] = $this->Common->two_join_five_cond('semester_sbfp', 'studeprofile', '*', 'a.StudentNumber=b.StudentNumber', 'a.schoolID', $this->session->c_id, 'a.SY', $this->session->bmi_sy, 'a.Section', $this->session->bmi_Section, 'a.YearLevel', $this->session->bmi_YearLevel, 'a.w_group', '3rd', 'LastName', 'ASC');


		$this->load->view('bw3rd', $result);
	}

	function sbfp_form1()
	{
		if (!isset($this->session->bmi_sy)) {
			redirect(base_url() . 'Sbfp/sbfp_bmi');
		}
		$result['title'] = "Master List Beneficiaries for School-Based Feeding Program SBFP(SY date)";
		$result['sbf'] = $this->Common->four_cond('semesterstude', 'schoolID', $this->session->c_id, 'SY', $this->session->bmi_sy, 'Section', $this->session->bmi_Section, 'YearLevel', $this->session->bmi_YearLevel);


		$this->load->view('sbfp_form1', $result);
	}

	function sbfp_form2()
	{
		$result['title'] = "Master List Beneficiaries for School-Based Feeding Program SBFP(SY date)";
		//$result['data'] = $this->Common->one_cond_select_gb('');

		//$result['sbf'] = $this->Common->four_cond('semesterstude', 'schoolID', $this->session->c_id, 'SY', $this->session->bmi_sy, 'Section', $this->session->bmi_Section, 'YearLevel', $this->session->bmi_YearLevel);

		$this->load->view('sbfp_form2', $result);
	}

	function sbfp_form3()
	{
		$result['title'] = "SCHOOL-BASED FEEDING PROGRAM";
		$result['sbf'] = $this->Common->four_cond('semesterstude', 'schoolID', $this->session->c_id, 'SY', $this->session->bmi_sy, 'Section', $this->session->bmi_Section, 'YearLevel', $this->session->bmi_YearLevel);

		$this->load->view('sbfp_form3', $result);
	}

	function sbfp_sf8()
	{


		$result['title'] = "SCHOOL-BASED FEEDING PROGRAM";
		if ($this->session->d_id == 0) {
			$result['school'] = $this->Common->no_cond_group_ob('schools', 'schoolID', 'schoolName', 'ASC');
		} else {
			$district = $this->Common->one_cond_row('district', 'id', $this->session->d_id);
			$result['school'] = $this->Common->one_cond_group_ob('schools', 'district', $district->discription, 'schoolID', 'schoolName', 'ASC');
		}

		if ($this->session->sp == 0) {
			$result['sy'] = $this->Common->one_cond_group('semesterstude', 'schoolID', $this->session->c_id, 'SY');
			$result['yl'] = $this->Common->one_cond_group('semesterstude', 'schoolID', $this->session->c_id, 'YearLevel');
			$result['section'] = $this->Common->one_cond_group('semesterstude', 'schoolID', $this->session->c_id, 'Section');
			$result['track'] = $this->Common->one_cond_group('semesterstude', 'schoolID', $this->session->c_id, 'Track');
		} else {
			$result['sy'] = $this->Common->no_cond_group('semesterstude', 'SY');
			$result['yl'] = $this->Common->no_cond_group('semesterstude', 'YearLevel');
			$result['section'] = $this->Common->no_cond_group('semesterstude', 'Section');
			$result['track'] = $this->Common->no_cond_group('semesterstude', 'Track');
		}

		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('sbfp_sf8', $result);
		$this->load->view('templates/footer');
	}

	function sbfp_sf8_dn()
	{

		$id =  (int)$this->session->bmi_school_id;

		$result['title'] = "SCHOOL-BASED FEEDING PROGRAM";


		$result['sy'] = $this->Common->one_cond_group('semesterstude', 'schoolID', $id, 'SY');
		$result['yl'] = $this->Common->one_cond_group('semesterstude', 'schoolID', $id, 'YearLevel');
		$result['section'] = $this->Common->one_cond_group('semesterstude', 'schoolID', $id, 'Section');
		$result['track'] = $this->Common->one_cond_group('semesterstude', 'schoolID', $id, 'Track');


		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('sbfp_sf8_dn', $result);
		$this->load->view('templates/footer');
	}

	function sbfp_sf8_report()
	{
		$result['title'] = "SCHOOL-BASED FEEDING PROGRAM";

		$sy = $this->input->post('sy');
		$yl = $this->input->post('YearLevel');
		$sec = $this->input->post('Section');
		$track = $this->input->post('track');
		$sid = $this->input->post('schoolID');

		//$result['sbfp'] = $this->Common->five_cond('semesterstude', 'schoolID', $this->session->c_id,'SY',$sy,'YearLevel',$yl,'Section',$sec,'Track',$track);
		if ($this->session->sp == 0) {
			$result['sbfp'] = $this->Common->two_join_five_cond('semesterstude', 'studeprofile', '*', 'a.StudentNumber=b.StudentNumber', 'a.schoolID', $this->session->c_id, 'a.SY', $sy, 'a.YearLevel', $yl, 'a.Section', $sec, 'a.Track', $track, 'b.LastName', 'ASC');
		} else {
			$result['sbfp'] = $this->Common->two_join_five_cond('semesterstude', 'studeprofile', '*', 'a.StudentNumber=b.StudentNumber', 'a.schoolID', $sid, 'a.SY', $sy, 'a.YearLevel', $yl, 'a.Section', $sec, 'a.Track', $track, 'b.LastName', 'ASC');
		}

		$this->load->view('sbfp_sf8_report', $result);
	}

	function sbfp_sf8_report_dn()
	{
		$result['title'] = "SCHOOL-BASED FEEDING PROGRAM";

		$id =  (int)$this->session->bmi_school_id;

		$sy = $this->input->post('sy');
		$yl = $this->input->post('YearLevel');
		$sec = $this->input->post('Section');
		$track = $this->input->post('track');
		$sid = $this->input->post('schoolID');

		//$result['sbfp'] = $this->Common->five_cond('semesterstude', 'schoolID', $this->session->c_id,'SY',$sy,'YearLevel',$yl,'Section',$sec,'Track',$track);
		if ($this->session->sp == 0) {
			$result['sbfp'] = $this->Common->two_join_five_cond('semesterstude', 'studeprofile', '*', 'a.StudentNumber=b.StudentNumber', 'a.schoolID', $sid, 'a.SY', $sy, 'a.YearLevel', $yl, 'a.Section', $sec, 'a.Track', $track, 'b.LastName', 'ASC');
		} else {
			$result['sbfp'] = $this->Common->two_join_five_cond('semesterstude', 'studeprofile', '*', 'a.StudentNumber=b.StudentNumber', 'a.schoolID', $id, 'a.SY', $sy, 'a.YearLevel', $yl, 'a.Section', $sec, 'a.Track', $track, 'b.LastName', 'ASC');
		}

		$this->load->view('sbfp_sf8_report', $result);
	}

	function sbm()
	{
		$result['title'] = "SCHOOL-BASED FEEDING PROGRAM";
		$result['sbm'] = $this->Common->no_cond('sbm_indicator');

		if ($this->input->post('submit')) {
			$this->SGODModel->sbm_insert();
			redirect(base_url() . 'Page/sbm');
		}

		$this->load->view('sbm_indecator', $result);
	}

	function sbm_edit()
	{
		$result['title'] = "SCHOOL-BASED FEEDING PROGRAM";
		$result['sbm'] = $this->Common->one_cond_row('sbm_indicator', 'id', $this->uri->segment(3));

		if ($this->input->post('submit')) {
			$this->SGODModel->sbm_update();
			$this->session->set_flashdata('success', 'Saved successfully.');
			redirect(base_url() . 'Page/sbm');
		}

		$this->load->view('sbm_indecator_edit', $result);
	}

	function sbm_sub()
	{
		$result['title'] = "SCHOOL-BASED FEEDING PROGRAM";
		$result['sbm'] = $this->Common->no_cond('sbm_indicator');
		$result['sbm_sub'] = $this->Common->no_cond('sbm_sub_indicator');

		if ($this->input->post('submit')) {
			$this->SGODModel->sbm_sub_insert();
			$this->session->set_flashdata('success', 'Saved successfully.');
			redirect(base_url() . 'Page/sbm_sub');
		}


		$this->load->view('sbm_indecator_sub', $result);
	}

	function sbm_sub_edit()
	{
		$result['title'] = "SCHOOL-BASED FEEDING PROGRAM";
		$result['sbm'] = $this->Common->no_cond('sbm_indicator');
		$result['sbm_sub'] = $this->Common->one_cond_row('sbm_sub_indicator', 'id', $this->uri->segment(3));

		if ($this->input->post('submit')) {
			$this->SGODModel->sbm_sub_update();
			$this->session->set_flashdata('success', 'Saved successfully.');
			redirect(base_url() . 'Page/sbm_sub');
		}


		$this->load->view('sbm_indecator_sub_edit', $result);
	}

	function sbm_checklist()
	{
		$ap = $this->Common->two_cond_count_row('sgod_action_plan', 'school_id', $this->session->username, 'fy', $_SESSION['sbm_fy']);
		if ($ap->num_rows() <= 0) {
			redirect(base_url() . 'Page/sbm_action_plan');
		}

		$result['title'] = "SCHOOL-BASED FEEDING PROGRAM";
		$result['sbm'] = $this->Common->no_cond('sbm_indicator');
		$result['sbm_sub'] = $this->Common->no_cond('sbm_sub_indicator');
		$school = $this->Common->one_cond_row('schools', 'schoolID', $this->session->username);
		$result['district'] = $this->Common->one_cond_row('district', 'discription', $school->district);

		$result['sbmc'] = $this->Common->two_cond_row('sbm', 'school_id', $this->session->username, 'fy', $_SESSION['sbm_fy']);
		$sbm = $this->Common->two_cond_count_row('sbm', 'school_id', $this->session->username, 'fy', $_SESSION['sbm_fy']);

		if ($sbm->num_rows() <= 0) {

			if ($this->input->post('submit')) {
				$this->SGODModel->sbm_cecklist_insert();
				$this->session->set_flashdata('success', 'Saved successfully.');
				redirect(base_url() . 'Page/sbm_checklist');
			}
			$this->load->view('sbm_form', $result);
		} else {

			$this->load->view('sbm_form_edit', $result);
		}
	}

	function sbm_checklist_final()
	{
		$this->SGODModel->sbm_cecklist_lock_unloc(1);
		$this->session->set_flashdata('success', 'Saved successfully.');
		redirect(base_url() . 'Page/sbm_checklist');
	}

	function sbm_checklist_unlock()
	{
		$this->SGODModel->sbm_cecklist_lock_unloc(0);
		$this->session->set_flashdata('success', 'Saved successfully.');
		redirect(base_url() . 'Page/checklist_district/' . $this->uri->segment(4));
	}

	function checklist_district()
	{

		$result['title'] = "SCHOOL-BASED FEEDING PROGRAM";
		$result['sbm'] = $this->Common->no_cond('sbm_indicator');
		$result['sbm_sub'] = $this->Common->no_cond('sbm_sub_indicator');

		$school = $this->Common->one_cond_row('schools', 'schoolID', $this->uri->segment(3));
		$result['district'] = $this->Common->one_cond_row('district', 'discription', $school->district);


		$result['sbmc'] = $this->Common->two_cond_row('sbm', 'school_id', $this->uri->segment(3), 'fy', $_SESSION['sbm_fy']);
		$sbm = $this->Common->two_cond_count_row('sbm', 'school_id', $this->uri->segment(3), 'fy', $_SESSION['sbm_fy']);

		$this->load->view('sbm_form_edit', $result);
	}

	function sbm_checklist_edit()
	{
		$this->SGODModel->sbm_cecklist_update();
		$this->session->set_flashdata('success', 'Saved successfully.');
		redirect(base_url() . 'Page/sbm_checklist');
	}

	public function delete_sbm()
	{
		$this->Common->del('sbm_indicator', 'id', $this->uri->segment(3));
		$this->Page_model->insert_at('Deleted SBM Principles', $this->uri->segment(3));
		$this->session->set_flashdata('danger', 'Deleted successfully!');
		redirect('Page/sbm');
	}

	public function delete_sbm_sub()
	{
		$this->Common->del('sbm_sub_indicator', 'id', $this->uri->segment(3));
		$this->Page_model->insert_at('Deleted SBM Indicator', $this->uri->segment(3));
		$this->session->set_flashdata('danger', 'Deleted successfully!');
		redirect('Page/sbm_sub');
	}

	function sbm_district_list()
	{
		$_SESSION['sbm_fy'] = $this->input->post('fy');
		$result['title'] = "SCHOOL-BASED FEEDING PROGRAM";
		$district = $this->Common->one_cond_row('district', 'id', $this->session->c_id);
		$result['school'] = $this->Common->one_cond('schools', 'district', $district->discription);
		$result['sbmc'] = $this->Common->one_cond_row('sbm', 'school_id', $this->session->username);

		if ($this->session->position === 'smme') {
			redirect(base_url() . 'Page/sbm_districts');
		}


		// if ($this->input->post('submit')) {
		// 	$this->SGODModel->sbm_sub_insert();
		// 	$this->session->set_flashdata('success', 'Saved successfully.');
		// 	redirect(base_url() . 'Page/sbm_sub');
		// }

		$this->load->view('sbm_district', $result);
	}

	function sbm_schoo_list_admin()
	{
		$result['title'] = "SCHOOL-BASED FEEDING PROGRAM";
		$district = $this->Common->one_cond_row('district', 'id', $this->uri->segment(3));
		$result['school'] = $this->Common->one_cond('schools', 'district', $district->discription);
		$result['sbmc'] = $this->Common->one_cond_row('sbm', 'school_id', $this->session->username);


		if ($this->input->post('submit')) {
			$this->SGODModel->sbm_sub_insert();
			$this->session->set_flashdata('success', 'Saved successfully.');
			redirect(base_url() . 'Page/sbm_sub');
		}


		$this->load->view('sbm_district', $result);
	}



	function sbm_district_list_admin()
	{
		$result['title'] = "SCHOOL-BASED FEEDING PROGRAM";
		$district = $this->Common->one_cond_row('district', 'id', $this->uri->segment(3));
		//$result['school']=$this->Common->one_cond('schools','district',$district->discription);
		$result['school'] = $this->Common->one_cond('sbm_submitted', 'district_id', $this->uri->segment(3));

		if ($this->input->post('submit')) {
			$this->SGODModel->sbm_sub_insert();
			$this->session->set_flashdata('success', 'Saved successfully.');
			redirect(base_url() . 'Page/sbm_sub');
		}


		$this->load->view('sbm_district_admin', $result);
	}

	function sbm_district_list_admin_not()
	{
		$result['title'] = "SCHOOL-BASED FEEDING PROGRAM";
		$district = $this->Common->one_cond_row('district', 'id', $this->uri->segment(3));
		//$result['school']=$this->Common->one_cond('schools','district',$district->discription);
		$result['school'] = $this->Common->one_cond('sbm_submitted', 'district_id', $this->uri->segment(3));

		if ($this->input->post('submit')) {
			$this->SGODModel->sbm_sub_insert();
			$this->session->set_flashdata('success', 'Saved successfully.');
			redirect(base_url() . 'Page/sbm_sub');
		}


		$this->load->view('sbm_district_admin', $result);
	}

	function sbm_admin()
	{
		$result['title'] = "SCHOOL-BASED FEEDING PROGRAM";
		$district = $this->Common->one_cond_row('district', 'id', $this->session->c_id);
		$result['school'] = $this->Common->no_cond('schools');
		$result['sbmc'] = $this->Common->one_cond_row('sbm', 'school_id', $this->session->username);

		if ($this->input->post('submit')) {
			$this->SGODModel->sbm_sub_insert();
			$this->session->set_flashdata('success', 'Saved successfully.');
			redirect(base_url() . 'Page/sbm_sub');
		}

		$this->load->view('sbm_admin', $result);
	}

	function sbm_districts()
	{
		$result['title'] = "SCHOOL-BASED FEEDING PROGRAM - DISTRICT LIST";
		$district = $this->Common->one_cond_row('district', 'id', $this->session->c_id);
		$result['school'] = $this->Common->no_cond('schools');
		$result['sbmc'] = $this->Common->one_cond_row('sbm', 'school_id', $this->session->username);

		$result['district'] = $this->Common->no_cond('district');

		$this->load->view('sbm_district_list', $result);
	}

	function sbm_checklist_district()
	{
		$result['title'] = "SCHOOL-BASED FEEDING PROGRAM";
		$result['sbm'] = $this->Common->no_cond('sbm_indicator');
		$result['sbm_sub'] = $this->Common->no_cond('sbm_sub_indicator');

		$sbm = $this->Common->one_cond_count_row('sbm', 'school_id', $this->uri->segment(3));
		$result['sbmc'] = $this->Common->one_cond_row('sbm', 'school_id', $this->uri->segment(3));

		$result['sbmcr'] = $this->Common->one_cond_row('sbm_remark', 'school_id', $this->uri->segment(3));
		$sbmr = $this->Common->one_cond_count_row('sbm_remark', 'school_id', $this->uri->segment(3));


		if ($sbm->num_rows() <= 0) {
			$this->load->view('sbm_form', $result);
		} else {

			if ($sbmr->num_rows() <= 0) {

				$this->load->view('sbm_district_form', $result);
			} else {

				$this->load->view('sbm_district_form_edit', $result);
			}
		}


		if ($this->input->post('submit')) {
			$this->SGODModel->sbm_cecklist_district_insert();
			$this->session->set_flashdata('success', 'Saved successfully.');
			redirect(base_url() . 'Page/sbm_checklist');
		}
	}

	function tapr_form_district()
	{

		$result['title'] = "SCHOOL-BASED FEEDING PROGRAM";
		$result['sbm'] = $this->Common->no_cond('sbm_indicator');
		$result['sbm_sub'] = $this->Common->no_cond('sbm_sub_indicator');

		if ($this->session->position == 'smme') {
			$sbm = $this->Common->two_cond_count_row('sbm_remark_admin', 'school_id', $this->uri->segment(3), 'fy', $_SESSION['sbm_fy']);
		} else {
			$sbm = $this->Common->two_cond_count_row('sbm_remark', 'school_id', $this->uri->segment(3), 'fy', $_SESSION['sbm_fy']);
		}

		//$result['sbmc'] = $this->Common->two_cond_row('sbm','school_id',$this->uri->segment(3),'fy',date('Y'));
		$result['sbm_ta'] = $this->Common->two_cond_row('sbm_ta', 'school_id', $this->uri->segment(3), 'fy', $_SESSION['sbm_fy']);
		$result['sbm_remark'] = $this->Common->two_cond_row('sbm_remark', 'school_id', $this->uri->segment(3), 'fy', $_SESSION['sbm_fy']);
		$result['sbm_remark_admin'] = $this->Common->two_cond_row('sbm_remark_admin', 'school_id', $this->uri->segment(3), 'fy', $_SESSION['sbm_fy']);
		$result['sbmc'] = $this->Common->two_cond_row('sbm_ta', 'school_id', $this->uri->segment(3), 'fy', $_SESSION['sbm_fy']);
		$result['sbmc_count'] = $this->Common->two_cond_count_row('sbm_ta', 'school_id', $this->uri->segment(3), 'fy', $_SESSION['sbm_fy']);
		if ($sbm->num_rows() <= 0) {

			if ($this->input->post('submit')) {
				$this->SGODModel->sbm_ta_insert();
				$this->session->set_flashdata('success', 'Saved successfully.');
				redirect(base_url() . 'Page/tapr_form');
			}

			if ($this->session->position == 'smme') {
				$this->load->view('sbm_ta_admin', $result);
			} else {
				$this->load->view('sbm_ta_district', $result);
			}
		} else {
			if ($this->session->position == 'smme') {
				$this->load->view('sbm_ta_admin_update', $result);
			} else {
				$this->load->view('sbm_ta_district_update', $result);
			}
		}
	}



	function tapr_district()
	{
		$this->SGODModel->sbm_cecklist_district_insert();
		$this->session->set_flashdata('success', 'Saved successfully.');
		redirect(base_url() . 'Page/tapr_form_district/' . $this->input->post('school_id'));
	}

	function tapr_admin()
	{
		$this->SGODModel->sbm_cecklist_admin_insert();
		$this->session->set_flashdata('success', 'Saved successfully.');
		redirect(base_url() . 'Page/tapr_form_district/' . $this->input->post('school_id'));
	}

	function tapr_district_update()
	{
		$this->SGODModel->sbm_checklist_district_update();
		$this->session->set_flashdata('success', 'Saved successfully.');
		redirect(base_url() . 'Page/tapr_form_district/' . $this->input->post('school_id'));
	}

	function tapr_admin_update()
	{
		$this->SGODModel->sbm_checklist_admin_update();
		$this->session->set_flashdata('success', 'Saved successfully.');
		redirect(base_url() . 'Page/tapr_form_district/' . $this->input->post('school_id'));
	}

	function sbm_d_update()
	{
		$this->SGODModel->sbm_checklist_district_update();
		$this->session->set_flashdata('success', 'Saved successfully.');
		redirect(base_url() . 'Page/sbm_checklist_district/' . $this->input->post('school_id'));
	}

	function sbm_list()
	{
		$_SESSION['sbm_fy'] = $this->input->post('fy');
		$result['title'] = "SCHOOL-BASED FEEDING PROGRAM";
		$result['sbm'] = $this->Common->no_cond('sbm_indicator');
		$result['sbm_sub'] = $this->Common->no_cond('sbm_sub_indicator');

		$sbm = $this->Common->one_cond_count_row('sbm', 'school_id', $this->uri->segment(3));
		$result['sbmc'] = $this->Common->one_cond_row('sbm', 'school_id', $this->uri->segment(3));

		$result['sbmcr'] = $this->Common->one_cond_row('sbm_remark', 'school_id', $this->uri->segment(3));
		$sbmr = $this->Common->one_cond_count_row('sbm_remark', 'school_id', $this->uri->segment(3));


		$this->load->view('sbm_list', $result);
	}

	function sbm_rate_list()
	{
		$result['title'] = "SCHOOL-BASED FEEDING PROGRAM";
		$result['sbm'] = $this->Common->one_cond('sbm', $this->uri->segment(3), $this->uri->segment(4));


		$this->load->view('sbm_rate_list', $result);
	}

	function new_appointment()
	{
		$result['title'] = "SCHOOL-BASED FEEDING PROGRAM";
		$result['staff'] = $this->Common->one_cond_row('hris_staff', 'IDNumber', $this->uri->segment(3));


		$this->load->view('ass_letter', $result);
	}

	function new_appointment_order()
	{
		$result['title'] = "SCHOOL-BASED FEEDING PROGRAM";
		$result['staff'] = $this->Common->one_cond_row('hris_staff', 'IDNumber', $this->uri->segment(3));


		$this->load->view('ass_letter_order', $result);
	}

	function new_appointment_cs()
	{
		$result['title'] = "SCHOOL-BASED FEEDING PROGRAM";
		$result['staff'] = $this->Common->one_cond_row('hris_staff', 'IDNumber', $this->uri->segment(3));


		$this->load->view('ass_letter_cs', $result);
	}

	function sbm_action_plan()
	{
		$data['title'] = "Action Plan";

		$_SESSION['sbm_fy'] = $this->input->post('fy');


		$data['data'] = $this->Common->two_cond('sgod_action_plan', 'school_id', $this->session->username, 'fy', $_SESSION['sbm_fy']);
		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('sbm_action_plan', $data);
	}

	function sbm_district_tech()
	{
		$data['title'] = "Technical Assisstance Provision Form";


		$data['data'] = $this->Common->one_cond('sbm_tech', 'district', $this->session->c_id);
		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('sbm_district_tech', $data);
	}

	function sbm_district_tech_admin()
	{



		$data['data'] = $this->Common->one_cond('sbm_tech', 'district', $this->uri->segment(3));
		$d = $this->Common->one_cond_row('district', 'id', $this->uri->segment(3));
		$data['title'] = "Technical Assisstance Provision - " . $d->discription;
		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('sbm_district_tech', $data);
	}

	function sbm_district_tech_new()
	{
		$data['title'] = "Technical Assisstance Provision Form";


		if ($this->input->post('submit')) {
			$this->SGODModel->sbm_tech_insert();
			$this->session->set_flashdata('success', 'Saved successfully.');
			redirect(base_url() . 'Page/sbm_district_tech');
		}

		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('sbm_district_tech_new', $data);
	}

	function sbm_district_tech_edit()
	{
		$data['title'] = "Technical Assisstance Provision Form";

		$data['data'] = $this->Common->one_cond_row('sbm_tech', 'id', $this->uri->segment(3));

		if ($this->input->post('submit')) {
			$this->SGODModel->sbm_tech_update();
			$this->session->set_flashdata('success', 'Saved successfully.');
			redirect(base_url() . 'Page/sbm_district_tech');
		}

		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('sbm_district_tech_update', $data);
	}

	function sbm_action_plan_new()
	{
		$data['title'] = "Action Plan";


		if ($this->input->post('submit')) {
			$this->SGODModel->action_plan_insert();
			$this->session->set_flashdata('success', 'Saved successfully.');
			redirect(base_url() . 'Page/sbm_action_plan');
		}

		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('sbm_action_plan_new', $data);
	}

	function sbm_action_plan_edit()
	{
		$data['title'] = "Action Plan";

		$data['data'] = $this->Common->one_cond_row('sgod_action_plan', 'id', $this->uri->segment(3));


		if ($this->input->post('submit')) {
			$this->SGODModel->action_plan_update();
			$this->session->set_flashdata('success', 'Saved successfully.');
			redirect(base_url() . 'Page/sbm_action_plan');
		}

		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('sbm_action_plan_update', $data);
	}

	function sbm_action_plan_del()
	{
		$this->Common->delete('sgod_action_plan', 'id', 3);
		$this->session->set_flashdata('danger', 'Saved successfully.');
		redirect(base_url() . 'Page/sbm_action_plan');
	}

	function sbm_district_tech_del()
	{
		$this->Common->delete('sbm_tech', 'id', 3);
		$this->session->set_flashdata('danger', 'Saved successfully.');
		redirect(base_url() . 'Page/sbm_district_tech');
	}

	function sbm_action_plan_pview()
	{

		$data['school'] = $this->Common->one_cond_row('schools', 'schoolID', $this->session->username);
		$data['data'] = $this->Common->two_cond('sgod_action_plan', 'fy', $_SESSION['sbm_fy'], 'school_id', $this->session->username);

		$this->load->view('sbm_action_plan_pview', $data);
	}

	function school_list()
	{
		$district = $this->Common->one_cond_row('district', 'id', $this->session->c_id);
		$data['school'] = $this->Common->one_cond('schools', 'district', $district->discription);

		$this->load->view('school_list', $data);
	}

	function employee_list()
	{
		$data['staff'] = $this->Common->two_cond('hris_staff', 'schoolID', $this->uri->segment(3), 'currentStatus', 'Active');

		if ($this->input->post('submit')) {
			$this->SGODModel->school_id_update();
			$this->session->set_flashdata('success', 'Saved successfully.');
			redirect(base_url() . 'Page/school_list');
		}

		$this->load->view('district_employee_list', $data);
	}

	function add_employee_on_list()
	{
		$emp = $this->Common->one_cond_count_row('hris_staff', 'IDNumber', $this->input->post('IDNumber'));
		$employee = $this->Common->one_cond_row('hris_staff', 'IDNumber', $this->input->post('IDNumber'));
		$this->SGODModel->school_id_update();

		if ($emp->num_rows() >= 1) {
			$this->session->set_flashdata('success', $employee->FirstName . ' ' . $employee->LastName . ' added successfully.');
		} else {
			$this->session->set_flashdata('danger', 'No records were found.');
		}

		redirect(base_url() . 'Page/employee_list/' . $this->uri->segment(3));
	}

	function remove_employee_on_list()
	{
		$this->SGODModel->update_employee_station();
		redirect(base_url() . 'Page/employee_list/' . $this->uri->segment(4));
	}

	function add_employee_on_list_school()
	{
		$emp = $this->Common->one_cond_count_row('hris_staff', 'IDNumber', $this->input->post('IDNumber'));
		$employee = $this->Common->one_cond_row('hris_staff', 'IDNumber', $this->input->post('IDNumber'));
		$this->SGODModel->school_id_update();

		if ($emp->num_rows() >= 1) {
			$this->session->set_flashdata('success', $employee->FirstName . ' ' . $employee->LastName . ' added successfully.');
		} else {
			$this->session->set_flashdata('danger', 'No records were found.');
		}

		redirect(base_url() . 'Page/employeelist');
	}

	function sbm_action_plan_pview_district()
	{

		$data['school'] = $this->Common->one_cond_row('schools', 'schoolID', $this->uri->segment(3));
		$data['data'] = $this->Common->two_cond('sgod_action_plan', 'fy', $_SESSION['sbm_fy'], 'school_id', $this->uri->segment(3));

		$this->load->view('sbm_action_plan_pview', $data);
	}

	function tapr_form()
	{
		$ap = $this->Common->two_cond_count_row('sbm', 'school_id', $this->session->username, 'fy', $_SESSION['sbm_fy']);
		if ($ap->num_rows() <= 0) {
			redirect(base_url() . 'Page/sbm_checklist');
		}

		$result['title'] = "SCHOOL-BASED FEEDING PROGRAM";
		$result['sbm'] = $this->Common->no_cond('sbm_indicator');
		$result['sbm_sub'] = $this->Common->no_cond('sbm_sub_indicator');

		$school = $this->Common->one_cond_row('schools', 'schoolID', $this->session->username);
		$result['district'] = $this->Common->one_cond_row('district', 'discription', $school->district);

		$sbm = $this->Common->two_cond_count_row('sbm_ta', 'school_id', $this->session->username, 'fy', $_SESSION['sbm_fy']);
		$result['sbmc'] = $this->Common->two_cond_row('sbm_ta', 'school_id', $this->session->username, 'fy', $_SESSION['sbm_fy']);
		if ($sbm->num_rows() <= 0) {

			if ($this->input->post('submit')) {
				$this->SGODModel->sbm_ta_insert();
				$this->session->set_flashdata('success', 'Saved successfully.');
				redirect(base_url() . 'Page/tapr_form');
			}
			$this->load->view('sbm_ta', $result);
		} else {

			$this->load->view('sbm_ta_update', $result);
		}
	}

	function sbm_ta_final()
	{
		$this->SGODModel->sbm_ta_lock_unloc(1);
		$this->session->set_flashdata('success', 'Saved successfully.');
		redirect(base_url() . 'Page/tapr_form');
	}

	function sbm_ta_unlock()
	{
		$this->SGODModel->sbm_ta_lock_unloc(0);
		$this->session->set_flashdata('success', 'Saved successfully.');
		redirect(base_url() . 'Page/tapr_form_district/' . $this->uri->segment(4));
	}

	function ta_form_edit()
	{
		$this->SGODModel->sbm_ta_update();
		$this->session->set_flashdata('success', 'Saved successfully.');
		redirect(base_url() . 'Page/tapr_form');
	}

	function tapr_form_new()
	{
		$data['title'] = "TECHNICAL ASSISSTANCE PROVISION REPORT FORM";


		if ($this->input->post('submit')) {
			$this->SGODModel->tapr_insert();
			$this->session->set_flashdata('success', 'Saved successfully.');
			redirect(base_url() . 'Page/tapr_form');
		}

		$data['indicator'] = $this->Common->no_cond('sbm_indicator');

		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('sbm_tapr_new', $data);
	}

	function tapr_form_edit()
	{
		$data['title'] = "TECHNICAL ASSISSTANCE PROVISION REPORT FORM";

		$data['data'] = $this->Common->one_cond_row('sgod_sbm_tapr', 'id', $this->uri->segment(3));


		if ($this->input->post('submit')) {
			$this->SGODModel->tapr_update();
			$this->session->set_flashdata('success', 'Saved successfully.');
			redirect(base_url() . 'Page/tapr_form');
		}

		$data['indicator'] = $this->Common->no_cond('sbm_indicator');

		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('sbm_tapr_update', $data);
	}
	function tapr_form_del()
	{
		$this->Common->delete('sgod_sbm_tapr', 'id', 3);
		$this->session->set_flashdata('danger', 'Saved successfully.');
		redirect(base_url() . 'Page/tapr_form');
	}

	function lv()
	{
		$data['title'] = "Action Plan";
		$data['acc_name'] = $this->Common->no_cond('sgod_liq_acc_name');

		$data['data'] = $this->Common->one_cond('sgod_action_plan', 'school_id', $this->session->username);
		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('lv', $data);
	}

	function sbcp_list()
	{
		$result['title'] = "School-Based Child Protection Committee Functionality Indicators Monitoring Tool";
		$result['sbcp'] = $this->Common->no_cond('sbcp_indicators');

		$school = $this->Common->one_cond_row('schools', 'schoolID', $this->session->username);
		$result['district'] = $this->Common->one_cond_row('district', 'discription', $school->district);

		$result['sbcpl'] = $this->Common->one_cond_row('sbcp', 'school_id', $this->session->username);
		$sbcpl = $this->Common->two_cond_count_row('sbcp', 'school_id', $this->session->username, 'fy', date('Y'));
		if ($sbcpl->num_rows() <= 0) {

			if ($this->input->post('submit')) {
				$this->SGODModel->sbcp_insert();
				$this->session->set_flashdata('success', 'Saved successfully.');
				redirect(base_url() . 'Page/sbcp_list');
			}
			$this->load->view('sbcp_rate_list', $result);
		} else {

			$this->load->view('sbcp_rate_list_edit', $result);
		}
	}

	function sbcp_list_edit()
	{
		$this->SGODModel->sbcp_update();
		$this->session->set_flashdata('success', 'Saved successfully.');
		redirect(base_url() . 'Page/sbcp_list');
	}

	public function memo()
	{
		$data['title'] = "Memo List";
		$data['m_title'] = "Add New Memo";
		$data['e_title'] = "Update Memo";
		$data['add_action'] = "memo";

		$data['page'] = $this->SGODModel->get_all_orderby('sgod_memo', 'id', 'DESC');
		$data['ln'] = $this->SGODModel->get_last_record('sgod_memo');

		$id = $this->input->post('id');

		if ($id) {
			$data['data'] = $this->SGODModel->get_memo_by_id($id);
		}

		$this->load->view('sgod_memo', $data);

		if ($this->input->post('submit')) {
			$mn = $this->input->post('memoNo');
			$check = $this->SGODModel->one_cond_count('sgod_memo', 'memoNo', $mn);

			if ($check->num_rows() <= 0) {
				$config['allowed_types'] = 'pdf';
				$config['upload_path'] = './uploads/sgod_memo/';
				$this->load->library('upload', $config);
				$this->upload->do_upload('file');
				$this->SGODModel->insert_memo();
				$this->session->set_flashdata('success', 'Successfully saved.');
				redirect(base_url() . 'Page/memo');
			} else {
				$this->session->set_flashdata('danger', 'Duplicate Memo Number.');
				redirect(base_url() . 'Page/memo');
			}
		}
	}

	public function memo_update()
	{
		$data['title'] = "Memo List";
		$data['add_action'] = "memo_update";

		$data['data'] = $this->SGODModel->one_cond_row('sgod_memo', 'id', $this->uri->segment(3));

		$this->load->view('memo_edit', $data);

		if ($this->input->post('submit')) {
			$this->SGODModel->memo_update();
			$this->session->set_flashdata('success', 'Successfully saved.');
			redirect(base_url() . 'Page/memo');
		}
	}

	public function memo_file_update()
	{
		$data['title'] = "Memo List";
		$data['m_title'] = "Add New Memo";
		$data['e_title'] = "Update Memo";
		$data['add_action'] = "memo_file_update";

		$data['data'] = $this->SGODModel->one_cond_row('sgod_memo', 'id', $this->uri->segment(3));
		$this->load->view('memo_file_edit', $data);

		if ($this->input->post('submit')) {
			$config['allowed_types'] = 'pdf';
			$config['upload_path'] = './upload/sgod_memo/';
			$this->load->library('upload', $config);
			$this->upload->do_upload('file');
			$this->SGODModel->mfu();
			$this->session->set_flashdata('success', 'Successfully saved.');
			redirect(base_url() . 'Page/memo');
		}
	}

	public function memo_delete()
	{
		$id = $this->uri->segment(3);
		//$this->SGODModel->delete(3, 'id', 'sgod_memo');
		$mem = $this->Common->one_cond_row('sgod_memo', 'id', $id);
		$this->Common->delete_with_attachv2('sgod_memo', $id, 'uploads/sgod_memo', 'd.pdf');
		$this->session->set_flashdata('success', 'Deleted successfully!');
		redirect("Page/memo");
	}

	function smeav2()
	{
		$result['title'] = "School Monitoring, Evaluation and Adjustment (SMEA)";
		$result['b_label'] = "+ ADD NEW";
		$result['b_link'] = "#";
		$result['ssa'] = $this->SGODModel->one_cond('sgod_school_allocation', 'schoolID', $this->session->username);
		//$result['data']=$this->SGODModel->get_all('sgod_aip');

		$result['data'] = $this->SGODModel->one_cond('sgod_aip', 'b_code', $_SESSION['aip']);
		$result['smea'] = $this->SGODModel->one_cond_count('sgod_smea', 'b_code', $_SESSION['aip']);

		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('smea_view', $result);

		if ($this->input->post('submit')) {
			$this->SGODModel->insert_smea();
			$this->session->set_flashdata('success', 'Saved successfully.');

			redirect(base_url() . 'Page/smeav2');
		}
	}



	function adjustment()
	{
		$result['title'] = "SOP Adjustment";
		$result['b_label'] = "+ ADD NEW";
		$result['b_link'] = "#";
		$result['ssa'] = $this->SGODModel->one_cond('sgod_school_allocation', 'schoolID', $this->session->username);
		//$result['data']=$this->SGODModel->get_all('sgod_aip');

		$result['data'] = $this->SGODModel->one_cond('sgod_smea_adjustment', 'b_code', $_SESSION['aip']);
		//$result['smea'] = $this->SGODModel->one_cond_count('sgod_smea', 'b_code', $_SESSION['aip']);

		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('smea_adjustment', $result);

		if ($this->input->post('submit')) {
			$this->SGODModel->insert_smea();
			$this->session->set_flashdata('success', 'Saved successfully.');

			redirect(base_url() . 'Page/adjustment');
		}
	}

	function adjustment_update()
	{
		$result['title'] = "Edit SOP Adjustment";

		$result['data'] = $this->SGODModel->one_cond_row('sgod_smea_adjustment', 'id', $this->uri->segment(3));

		if ($this->input->post('submit')) {
			$this->SGODModel->smea_adjustment_update();
			$this->session->set_flashdata('success', 'Saved successfully.');

			redirect(base_url() . 'Page/adjustment');
		}

		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('smea_adjustment_update', $result);
	}



	function adjustment_new()
	{
		$result['title'] = " Create SOP Adjustment";

		//$result['data'] = $this->SGODModel->one_cond('sgod_smea_adjustment', 'fy', $_SESSION['fy']);
		//$result['data'] = $this->SGODModel->no_cond('sgod_innovations');

		if ($this->input->post('submit')) {
			$this->SGODModel->smea_adjustment_insert();
			$this->session->set_flashdata('success', 'Successfully saved.');
			redirect(base_url() . 'Page/adjustment');
		}

		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('smea_adjustment_add', $result);
	}

	function smea_adjustment_new()
	{
		$result['title'] = "SOP Adjustment";

		//$result['data'] = $this->SGODModel->one_cond('sgod_smea_adjustment', 'fy', $_SESSION['fy']);
		//$result['data'] = $this->SGODModel->no_cond('sgod_innovations');

		if ($this->input->post('submit')) {
			$this->SGODModel->smea_adjust_insert();
			$this->session->set_flashdata('success', 'Successfully saved.');
			redirect(base_url() . 'Page/adjustment');
		}

		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('smea_adjust_add', $result);
	}

	function smea_adjustment_update()
	{
		$result['title'] = "SOP Adjustment Update";

		$result['data'] = $this->Common->one_cond_row('sgod_sop_adjustment', 'id', $this->uri->segment(4));
		//$result['data'] = $this->SGODModel->no_cond('sgod_innovations');

		if ($this->input->post('submit')) {
			$this->SGODModel->smea_adjust_update();
			$this->session->set_flashdata('success', 'Successfully saved.');
			redirect(base_url() . 'Page/adjustment');
		}

		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('smea_adjust_update', $result);
	}

	function adjustment_delete()
	{
		$this->SGODModel->delete('3', 'id', 'sgod_smea_adjustment');
		$this->session->set_flashdata('danger', 'Successfully deleted');
		redirect(base_url() . 'Page/adjustment ');
	}

	function smea_edit($param)
	{
		$result['title'] = "UPDATE TARGET";
		$result['b_label'] = "+ ADD NEW";
		$result['b_link'] = "#";

		$result['sop'] = $this->SGODModel->one_cond_row('sgod_sop', 'id', $param);
		$_SESSION['q'] = $this->input->post('q');


		if ($this->input->post('submit')) {
			$this->SGODModel->update_smea($param);
			$this->session->set_flashdata('success', 'Saved successfully.');

			redirect(base_url() . 'Page/generate_smea');
		}


		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('smea_update', $result);
	}

	function smea_ad_edit($param)
	{
		$result['title'] = "UPDATE TARGET";
		$result['b_label'] = "+ ADD NEW";
		$result['b_link'] = "#";

		$result['sop'] = $this->SGODModel->one_cond_row('sgod_sop_adjustment', 'id', $param);
		$_SESSION['q'] = $this->input->post('q');


		if ($this->input->post('submit')) {
			$this->SGODModel->update_smea_ad($param);
			$this->session->set_flashdata('success', 'Saved successfully.');

			redirect(base_url() . 'Page/generate_smea#adjustment');
		}


		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('smea_ad_update', $result);
	}

	function generate_smea()
	{
		$result['title'] = "ANNUAL IMPLEMENTATION PLAN";

		$school_id = $this->session->username;
		$fy = $_SESSION['fy'];
		$bcode = $_SESSION['aip'];

		$result['fy'] = $_SESSION['fy'];
		$result['q'] = $this->input->post('q');
		$result['data'] = $this->SGODModel->get_smea($school_id, $fy, $bcode);

		$result['adjustment'] = $this->Common->three_cond_group('sgod_smea_adjustment', 'school_id', $school_id, 'fy', $fy, 'b_code', $bcode, 'pillar');
		$result['data_row'] = $this->SGODModel->get_aip_row($school_id, $fy, $bcode);
		// $result['school'] = $this->SGODModel->one_cond_row('schools', 'schoolId', $school_id);
		// $result['aip_submit'] = $this->SGODModel->aip_related_row('sgod_aip_submit', $school_id, $fy, $bcode);

		// $result['ft'] = $this->SGODModel->two_cond_row('sgod_school_allocation', 'schoolID', $this->session->username, 'alloc_batch', $_SESSION['aip']);


		$this->load->view('smea_generate', $result);
	}

	function smea_summary()
	{
		$result['title'] = "ANNUAL IMPLEMENTATION PLAN";
		$result['b_label'] = "+ ADD NEW";
		$result['b_link'] = "#";

		$school_id = $this->session->username;
		$fy = $_SESSION['fy'];
		$bcode = $_SESSION['aip'];

		$result['fy'] = $_SESSION['fy'];

		$result['q'] = $this->input->post('q');


		$result['data'] = $this->SGODModel->get_smea($school_id, $fy, $bcode);

		//$result['adjustment'] = $this->Common->three_cond_group('sgod_smea_adjustment', 'school_id', $school_id, 'fy', $fy, 'b_code', $bcode, 'pillar');
		$result['data_row'] = $this->SGODModel->get_aip_row($school_id, $fy, $bcode);
		//$result['school'] = $this->SGODModel->one_cond_row('schools', 'schoolId', $school_id);
		//$result['aip_submit'] = $this->SGODModel->aip_related_row('sgod_aip_submit', $school_id, $fy, $bcode);
		//$result['data_row'] = $this->SGODModel->get_aip_row($school_id, $fy, $bcode);

		//$result['ft'] = $this->SGODModel->two_cond_row('sgod_school_allocation', 'schoolID', $this->session->username, 'alloc_batch', $_SESSION['aip']);


		$this->load->view('smea_generate_summary', $result);
	}

	function smea_qr()
	{
		$result['title'] = "School Monitoring, Evaluation And Adjustment";
		$this->load->view('smea_qr', $result);
	}

	public function submit_smea()
	{
		$id = $this->input->get('id');
		$this->SGODModel->smea_submit();
		$this->session->set_flashdata('success', 'Submitted successfully.');
		redirect("Page/smeav2");
	}

	function smea_admin()
	{
		$result['title'] = "School Monitoring, Evaluation and Adjustment (SMEA)";

		if (!isset($_SESSION['fy_admin'])) {
			redirect(base_url() . 'Page/fy_setting');
		}

		//$result['data'] = $this->SGODModel->one_cond('sgod_aip', 'b_code', $_SESSION['aip']);
		$result['smea'] = $this->SGODModel->one_cond('sgod_smea', 'fy', $_SESSION['fy_admin']);

		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('smea_admin', $result);
	}

	function smea_admin_generate()
	{
		$result['title'] = "School Monitoring, Evaluation and Adjustment (SMEA)";

		//$result['data'] = $this->SGODModel->one_cond('sgod_aip', 'b_code', $_SESSION['aip']);
		$result['smea'] = $this->SGODModel->one_cond('sgod_smea', 'fy', $_SESSION['fy_admin']);
		$result['fy'] = $_SESSION['fy_admin'];
		$this->load->view('smea_generate_admin', $result);
	}

	function innovations()
	{
		$result['title'] = "Innovations";

		//$result['data'] = $this->SGODModel->one_cond('sgod_innovations', 'fy', $_SESSION['fy']);
		$result['data'] = $this->SGODModel->one_cond('sgod_innovations', 'school_id', $this->session->username);

		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('innovation', $result);
	}

	function innovation_new()
	{
		$result['title'] = " Create Innovations ";

		//$result['data'] = $this->SGODModel->one_cond('sgod_innovations', 'fy', $_SESSION['fy']);
		//$result['data'] = $this->SGODModel->no_cond('sgod_innovations');

		if ($this->input->post('submit')) {
			$this->SGODModel->innovation_insert();
			$this->session->set_flashdata('success', 'Successfully saved.');
			redirect(base_url() . 'Page/innovations');
		}

		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('innovation_add', $result);
	}

	function innovation_update()
	{
		$result['title'] = " Update Innovations ";

		//$result['data'] = $this->SGODModel->one_cond('sgod_innovations', 'fy', $_SESSION['fy']);
		$result['data'] = $this->SGODModel->one_cond_row('sgod_innovations', 'id', $this->uri->segment(3));

		if ($this->input->post('submit')) {
			$this->SGODModel->innovation_update();
			$this->session->set_flashdata('success', 'Successfully saved.');
			redirect(base_url() . 'Page/innovations');
		}

		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('innovation_edit', $result);
	}

	function innovation_delete()
	{
		$this->SGODModel->delete('3', 'id', 'sgod_innovations');
		$this->session->set_flashdata('danger', 'Successfully deleted');
		redirect(base_url() . 'Page/innovations ');
	}

	function quick_wins()
	{
		$result['title'] = "Quick Wins and Best Practices";

		//$result['data'] = $this->SGODModel->one_cond('sgod_innovations', 'fy', $_SESSION['fy']);
		$result['data'] = $this->SGODModel->one_cond('sgod_quick_wins', 'school_id', $this->session->username);

		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('quick_wins', $result);
	}


	function quick_wins_new()
	{
		$result['title'] = " Create Quick Wins and Best Practices ";

		//$result['data'] = $this->SGODModel->one_cond('sgod_innovations', 'fy', $_SESSION['fy']);
		//$result['data'] = $this->SGODModel->no_cond('sgod_innovations');

		if ($this->input->post('submit')) {
			$this->SGODModel->quick_wins_insert();
			$this->session->set_flashdata('success', 'Successfully saved.');
			redirect(base_url() . 'Page/quick_wins');
		}

		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('quick_wins_add', $result);
	}

	function quick_wins_update()
	{
		$result['title'] = "Updatem Quick Wins and Best Practices ";

		//$result['data'] = $this->SGODModel->one_cond('sgod_innovations', 'fy', $_SESSION['fy']);
		$result['data'] = $this->SGODModel->one_cond_row('sgod_quick_wins', 'id', $this->uri->segment(3));

		if ($this->input->post('submit')) {
			$this->SGODModel->quick_wins_update();
			$this->session->set_flashdata('success', 'Successfully saved.');
			redirect(base_url() . 'Page/quick_wins');
		}

		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('quick_wins_edit', $result);
	}

	function quick_wins_delete()
	{
		$this->SGODModel->delete('3', 'id', 'sgod_quick_wins');
		$this->session->set_flashdata('danger', 'Successfully deleted');
		redirect(base_url() . 'Page/quick_wins ');
	}

	function policy()
	{
		$result['title'] = "Policy Issues & Updates";

		//$result['data'] = $this->SGODModel->one_cond('sgod_innovations', 'fy', $_SESSION['fy']);
		$result['data'] = $this->SGODModel->one_cond('sgod_policy', 'school_id', $this->session->username);

		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('policy', $result);
	}

	function policy_new()
	{
		$result['title'] = "Policy Issues & Updates";

		//$result['data'] = $this->SGODModel->one_cond('sgod_innovations', 'fy', $_SESSION['fy']);
		$result['ppas'] = $this->SGODModel->no_cond('sgod_ppas');

		if ($this->input->post('submit')) {
			$this->SGODModel->policy_insert();
			$this->session->set_flashdata('success', 'Successfully saved.');
			redirect(base_url() . 'Page/policy');
		}

		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('policy_add', $result);
	}

	function policy_update()
	{
		$result['title'] = "Updatem Quick Wins and Best Practices ";

		//$result['data'] = $this->SGODModel->one_cond('sgod_innovations', 'fy', $_SESSION['fy']);
		$result['data'] = $this->SGODModel->one_cond_row('sgod_policy', 'id', $this->uri->segment(3));
		$result['ppas'] = $this->SGODModel->no_cond('sgod_ppas');

		if ($this->input->post('submit')) {
			$this->SGODModel->policy_update();
			$this->session->set_flashdata('success', 'Successfully saved.');
			redirect(base_url() . 'Page/policy');
		}

		$this->load->view('templates/head');
		$this->load->view('templates/header');
		$this->load->view('policy_edit', $result);
	}

	function policy_delete()
	{
		$this->SGODModel->delete('3', 'id', 'sgod_policy');
		$this->session->set_flashdata('danger', 'Successfully deleted');
		redirect(base_url() . 'Page/policy');
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


	public function med_patient()
	{
		$this->load->model('SettingsModel'); // Load the model

		$logged_in_user = $this->session->userdata('username'); // Get logged-in username
		$user_sp = $this->session->userdata('sp'); // Get SP value

		$result['data'] = $this->SettingsModel->med_patient($logged_in_user, $user_sp); // Pass parameters to filter data

		$this->load->view('med_patient', $result); // Load the view with filtered data
	}




	function med_patient_abstract()
	{
		$medID = $this->input->get('medID');

		$result['data'] = $this->SettingsModel->med_patient_report($medID);

		$rows = is_array($result['data']) ? $result['data'] : [$result['data']];

		if (!empty($rows) && is_object($rows[0])) {
			$row = $rows[0];

			$dob = $row->birthdate
				?? $row->BirthDate
				?? $row->Birthdate
				?? $row->bdate
				?? $row->DOB
				?? null;

			if ($dob) {
				try {
					$b   = new DateTime($dob);
					$now = new DateTime('today');
					$row->age = $b->diff($now)->y;   // <- this is what your view prints
				} catch (Exception $e) {
					$row->age = null;
				}
			} else {
				$row->age = null;
			}
		}

		$result['data'] = $rows;

		$result['settings']    = $this->SettingsModel->mis_settings();
		$result['med_setting'] = $this->SettingsModel->med_settings();

		$this->load->view('med_patient_abstract', $result);
	}

	public function med_healthRec($healthID)
	{
		// If no healthID is provided (though it should be), redirect or show 404
		if (!$healthID) {
			show_404(); // or redirect('somepage');
		}

		$result['settings'] = $this->SettingsModel->mis_settings();
		$result['health_record'] = $this->SettingsModel->get_med_health_record_by_id($healthID);

		// If record not found, also show 404 or handle it gracefully
		if (!$result['health_record']) {
			show_404(); // or custom error page
		}

		$this->load->view('med_health_record', $result);
	}




	public function update_healthRec($id)
	{
		// Fetch the existing record by ID
		$data['health_record'] = $this->SettingsModel->getRecordById($id);
		$data['staff'] = $this->SettingsModel->med_staff();

		// Load the update view with the record's data
		$this->load->view('med_health_record_update', $data);
	}


	public function save_healthRecUpdate($id)
	{
		$data = $this->input->post();
		$this->SettingsModel->updateRecord($id, $data);
		redirect('Page/med_healthRecDash');
	}




	public function med_healthRecDash()
	{
		$result['settings'] = $this->SettingsModel->mis_settings();
		$result['data'] = $this->SettingsModel->med_health_exam();
		$this->load->view('med_health_recordDash', $result);
	}

	public function med_healthRecAdd()
	{
		$data['settings'] = $this->SettingsModel->mis_settings();
		$data['staff'] = $this->SettingsModel->med_staff();
		$data['school'] = $this->SettingsModel->schools();
		$data['district'] = $this->SettingsModel->districts();

		$this->load->view('med_health_recordadd', $data);
	}

	public function saveRecord()
	{
		$data = $this->input->post();

		if ($this->SettingsModel->save_health_exam($data)) {
			$this->session->set_flashdata('success', 'Health examination record saved successfully.');
		} else {
			$this->session->set_flashdata('danger', 'Failed to save the record.');
		}

		redirect('Page/med_healthRecDash'); // adjust this to your desired page after submission
	}





	public function delete_healthRec($id)
	{
		$this->SettingsModel->deleteRecord($id);
		$this->session->set_flashdata('success', 'Health Examination Record deleted successfully.');
		redirect('Page/med_healthRecDash');
	}




	public function med_patient_student()
	{
		$this->load->model('SettingsModel'); // Load the model

		$logged_in_user = $this->session->userdata('username'); // Get logged-in username
		$user_sp = $this->session->userdata('sp'); // Get SP value

		// Pass parameters to filter data
		$result['data'] = $this->SettingsModel->med_patient_student($logged_in_user, $user_sp);

		$this->load->view('med_patient_student', $result);
	}



	public function med_patient_count()
	{
		$result['data'] = $this->SettingsModel->get_med_patient_summary();
		$this->load->view('med_patient_district', $result);
	}

	public function med_nurse_masterlist()
	{
		$this->load->model('SettingsModel');
		$user_id = $this->session->userdata('c_id');
		$result['data'] = $this->SettingsModel->get_nurse_masterlist($user_id);

		$selectedDistrict = $this->input->get('district', true);
		$patientType = $this->input->get('patient_type', true);

		$districts = [];
		foreach ($result['data'] as $row) {
			$district = $row->district_name ?: 'Unassigned';
			$districts[$district] = true;
		}
		if ($selectedDistrict && !isset($districts[$selectedDistrict])) {
			$selectedDistrict = null;
		}

		$result['selectedDistrict'] = $selectedDistrict;

		if ($selectedDistrict) {
			$divisionUsernames = [];
			foreach ($result['data'] as $row) {
				$district = $row->district_name ?: 'Unassigned';
				if ($district === $selectedDistrict && $row->sp_position === 'Division Nurse') {
					$divisionUsernames[] = $row->username;
				}
			}

			$result['divisionNurseUsernames'] = $divisionUsernames;

			if (!empty($divisionUsernames)) {
				$result['divisionPatientCounts'] = $this->SettingsModel->get_patient_counts_by_usernames($divisionUsernames);

				if (!empty($patientType)) {
					$normalizedType = ucfirst(strtolower($patientType));
					if ($normalizedType === 'All') {
						$result['divisionPatientType'] = 'All';
						$result['divisionPatients'] = $this->SettingsModel->get_patients_by_usernames($divisionUsernames);
					} elseif (in_array($normalizedType, ['Employee', 'Student'], true)) {
						$result['divisionPatientType'] = $normalizedType;
						$result['divisionPatients'] = $this->SettingsModel->get_patients_by_usernames($divisionUsernames, $normalizedType);
					}
				}
			}
		}

		$this->load->view('med_nurse_masterlist', $result);
	}

	public function med_patient_list($patientType, $district)
	{
		$district = urldecode($district);
		$district = str_replace('+', ' ', $district); // Fix spaces in district name

		$result['data'] = $this->SettingsModel->get_med_patient_list($patientType, $district);
		$this->load->view('med_patient_list', $result);
	}




	function med_patient_pending()
	{
		$logged_in_user = $this->session->userdata('username');
		$user_sp = $this->session->userdata('sp');

		$result['data'] = $this->SettingsModel->med_patient($logged_in_user, $user_sp);
		$this->load->view('med_patient_pending', $result);
	}

	function add_medPatient()
	{
		$this->load->library('upload');
		$data['district'] = $this->SettingsModel->district();
		$data['school'] = $this->SettingsModel->school();
		$data['staff'] = $this->SettingsModel->staff();
		$data['category'] = $this->SettingsModel->category();
		$data['disposition'] = $this->SettingsModel->disposition();
		$data['old'] = (array) $this->session->flashdata('med_patient_add_old');

		$this->load->view('med_patient_add', $data);

		if ($this->input->post()) {
			$oldInput = $this->capture_med_patient_form_input();
			$user_position = $this->session->userdata('position');
			$user_sp = $this->session->userdata('sp');
			$username = $this->session->userdata('username');


			$consultationStat = ($user_sp != 0) ? 'Pending' : (($user_position == 'SHNS') ? 'Processed' : 'Pending');
			$encoder_role = ($user_sp != 0) ? 'Nurse' : 'Doctor'; // Assign role based on sp value

			try {
				$attachmentFiles = $this->upload_med_attachment_files('attachment', 3);
			} catch (RuntimeException $e) {
				$this->session->set_flashdata('med_patient_add_old', $oldInput);
				$this->session->set_flashdata('danger', $e->getMessage());
				redirect('Page/add_medPatient');
				return;
			}
			$attachment = $this->format_med_attachments($attachmentFiles);

			$data = [
				'medID'             => set_value('medID', ''),
				'patientType'       => set_value('patientType', ''),
				'FirstName'         => set_value('FirstName', ''),
				'MiddleName'        => set_value('MiddleName', ''),
				'LastName'          => set_value('LastName', ''),
				'address'           => set_value('address', ''),
				'contact'           => set_value('contact', ''),
				'age'               => set_value('age', ''),
				'birthdate'         => set_value('birthdate', ''),
				'sex'               => set_value('sex', ''),
				'bp'                => set_value('bp', ''),
				'cardiac'           => set_value('cardiac', ''),
				'respiratory'       => set_value('respiratory', ''),
				'temp'              => set_value('temp', ''),
				'sat'               => set_value('sat', ''),
				'height'            => set_value('height', ''),
				'weight'            => set_value('weight', ''),
				'complaint'         => set_value('complaint', ''),
				'allergies'         => set_value('allergies', ''),
				'current_med'       => set_value('current_med', ''),  // Avoid NULL values
				'phy_exam'          => set_value('phy_exam', ''),
				'diagnosis'         => set_value('diagnosis', ''),
				'treatment'         => set_value('treatment', ''),
				'disposition'       => set_value('disposition', ''),
				'remarks'           => set_value('remarks', ''),
				'appdate'           => set_value('appdate', ''),
				'position'          => set_value('position', ''),
				'cstat'             => set_value('cstat', ''),
				'disposition_time'  => set_value('disposition_time', ''),
				'district'          => set_value('district', ''),
				'school'            => set_value('school', ''),
				'others_symp'       => set_value('others_symp', ''),
				'rest_no'           => set_value('rest_no', ''),
				'referral_facility' => set_value('referral_facility', ''),
				'referral_reason'   => set_value('referral_reason', ''),
				'referral_guardian' => set_value('referral_guardian', ''),
				'purpose'           => set_value('purpose', ''),
				'IDNumber'          => set_value('IDNumber', ''),
				'LRN'               => set_value('LRN', ''),
				'print_Perm'        => set_value('print_Perm', ''),
				'illness_history'   => set_value('illness_history', ''),
				'username'          => set_value('username', ''),
				'category'          => set_value('category', ''),
				'disease'           => set_value('disease', ''),
				'consultationStat'  => isset($consultationStat) ? $consultationStat : '',
				'encoder_role'      => isset($encoder_role) ? $encoder_role : '',
				'attachment'        => isset($attachment) ? $attachment : ''
			];


			if ($this->db->insert('med_patient', $data)) {
				$referralEmailSent = false;
				$pendingEmailSent = false;
				$medID = $this->db->insert_id();
				$patient = $this->db->get_where('med_patient', ['medID' => $medID])->row();

				if (($data['consultationStat'] ?? '') === 'Pending') {
					$pendingEmailSent = $this->send_pending_consultation_email($patient);
				}
				if (($data['disposition'] ?? '') === 'Transferred/Referred') {
					$referralEmailSent = $this->send_referral_email($patient);
				}

				$msg = 'Patient record created successfully.';
				if ($pendingEmailSent) {
					$msg .= ' Pending consultation email sent.';
				}
				if ($referralEmailSent) {
					$msg .= ' Referral email sent.';
				}
				$this->session->set_flashdata('success', $msg);
				redirect('Page/med_patient');
			} else {
				$this->session->set_flashdata('med_patient_add_old', $oldInput);
				$this->session->set_flashdata('error', 'Database insertion failed.');
				log_message('error', 'Database insert failed: ' . $this->db->error());
				redirect('Page/add_medPatient');
			}
		}
	}







	public function getDiseasesByCategory()
	{
		$category = $this->input->post('category');
		$diseases = $this->SettingsModel->disease($category);

		echo json_encode($diseases);
	}





	function add_medPatient1()
	{
		$this->load->library('upload');
		$data['district'] = $this->SettingsModel->district();
		$data['school'] = $this->SettingsModel->school();
		$data['staff'] = $this->SettingsModel->staff();
		$data['category'] = $this->SettingsModel->category();
		$data['disposition'] = $this->SettingsModel->disposition();
		$data['old'] = (array) $this->session->flashdata('med_patient_add1_old');

		$this->load->view('med_patient_add1', $data);

		if ($this->input->post()) {
			$oldInput = $this->capture_med_patient_form_input();
			$user_position = $this->session->userdata('position');
			$user_sp = $this->session->userdata('sp');
			$username = $this->session->userdata('username');


			$consultationStat = ($user_sp != 0) ? 'Pending' : (($user_position == 'SHNS') ? 'Processed' : 'Pending');
			$encoder_role = ($user_sp != 0) ? 'Nurse' : 'Doctor'; // Assign role based on sp value

			try {
				$attachmentFiles = $this->upload_med_attachment_files('attachment', 3);
			} catch (RuntimeException $e) {
				$this->session->set_flashdata('med_patient_add1_old', $oldInput);
				$this->session->set_flashdata('danger', $e->getMessage());
				redirect('Page/add_medPatient1');
				return;
			}
			$attachment = $this->format_med_attachments($attachmentFiles);

			$data = [
				'medID'             => set_value('medID', ''),
				'patientType'       => set_value('patientType', ''),
				'FirstName'         => set_value('FirstName', ''),
				'MiddleName'        => set_value('MiddleName', ''),
				'LastName'          => set_value('LastName', ''),
				'address'           => set_value('address', ''),
				'contact'           => set_value('contact', ''),
				'age'               => set_value('age', ''),
				'birthdate'         => set_value('birthdate', ''),
				'sex'               => set_value('sex', ''),
				'bp'                => set_value('bp', ''),
				'cardiac'           => set_value('cardiac', ''),
				'respiratory'       => set_value('respiratory', ''),
				'temp'              => set_value('temp', ''),
				'sat'               => set_value('sat', ''),
				'height'            => set_value('height', ''),
				'weight'            => set_value('weight', ''),
				'complaint'         => set_value('complaint', ''),
				'allergies'         => set_value('allergies', ''),
				'current_med'       => set_value('current_med', ''),  // Avoid NULL values
				'phy_exam'          => set_value('phy_exam', ''),
				'diagnosis'         => set_value('diagnosis', ''),
				'treatment'         => set_value('treatment', ''),
				'disposition'       => set_value('disposition', ''),
				'remarks'           => set_value('remarks', ''),
				'appdate'           => set_value('appdate', ''),
				'position'          => set_value('position', ''),
				'cstat'             => set_value('cstat', ''),
				'disposition_time'  => set_value('disposition_time', ''),
				'district'          => set_value('district', ''),
				'school'            => set_value('school', ''),
				'others_symp'       => set_value('others_symp', ''),
				'rest_no'           => set_value('rest_no', ''),
				'referral_facility' => set_value('referral_facility', ''),
				'referral_reason'   => set_value('referral_reason', ''),
				'referral_guardian' => set_value('referral_guardian', ''),
				'purpose'           => set_value('purpose', ''),
				'IDNumber'          => set_value('IDNumber', ''),
				'LRN'               => set_value('LRN', ''),
				'print_Perm'        => set_value('print_Perm', ''),
				'illness_history'   => set_value('illness_history', ''),
				'username'          => set_value('username', ''),
				'category'          => set_value('category', ''),
				'disease'           => set_value('disease', ''),
				'consultationStat'  => isset($consultationStat) ? $consultationStat : '',
				'encoder_role'      => isset($encoder_role) ? $encoder_role : '',
				'attachment'        => isset($attachment) ? $attachment : ''
			];


			if ($this->db->insert('med_patient', $data)) {
				$referralEmailSent = false;
				$pendingEmailSent = false;
				$medID = $this->db->insert_id();
				$patient = $this->db->get_where('med_patient', ['medID' => $medID])->row();

				if (($data['consultationStat'] ?? '') === 'Pending') {
					$pendingEmailSent = $this->send_pending_consultation_email($patient);
				}
				if (($data['disposition'] ?? '') === 'Transferred/Referred') {
					$referralEmailSent = $this->send_referral_email($patient);
				}

				$msg = 'Patient record created successfully.';
				if ($pendingEmailSent) {
					$msg .= ' Pending consultation email sent.';
				}
				if ($referralEmailSent) {
					$msg .= ' Referral email sent.';
				}
				$this->session->set_flashdata('success', $msg);
				redirect('Page/med_patient_student');
			} else {
				$this->session->set_flashdata('med_patient_add1_old', $oldInput);
				$this->session->set_flashdata('error', 'Database insertion failed.');
				log_message('error', 'Database insert failed: ' . $this->db->error());
				redirect('Page/add_medPatient1');
			}
		}
	}

	public function med_patient_add()
	{
		$this->add_medPatient();
	}

	public function med_patient_add1()
	{
		$this->add_medPatient1();
	}























	public function get_patients()
	{
		$patientType = $this->input->post('patientType');

		if ($patientType == 'Employee') {
			$this->db->select('IDNumber as id, IDNumber, FirstName, MiddleName, LastName, CONCAT(FirstName, " ", LastName) as name');
			$this->db->from('hris_staff');
		} else {
			$this->db->select('studentNumber as id, LRN, firstname as FirstName, MiddleName, lastname as LastName, CONCAT(firstname, " ", lastname) as name');
			$this->db->from('studeprofile');
		}

		$query = $this->db->get();
		echo json_encode($query->result());
	}


	// public function get_patients() {
	//     $patientType = $this->input->post('patientType'); // Get patient type from AJAX request
	//     $this->load->model('SettingsModel');

	//     if ($patientType == "Employee") {
	//         $patients = $this->SettingsModel->get_employees(); // Fetch Employees
	//     } else {
	//         $patients = $this->SettingsModel->get_students(); // Fetch Students
	//     }

	//     echo json_encode($patients); // Return JSON response
	// }








	public function get_patient_history()
	{
		$IDNumber = $this->input->post('IDNumber');
		$LRN = $this->input->post('LRN');

		log_message('debug', "Checking history for IDNumber: $IDNumber, LRN: $LRN"); // Debugging log

		$this->db->from('med_patient');

		// Check if either IDNumber or LRN matches in the database
		if (!empty($IDNumber) && !empty($LRN)) {
			$this->db->where("(IDNumber = '$IDNumber' OR LRN = '$LRN')");
		} elseif (!empty($IDNumber)) {
			$this->db->where('IDNumber', $IDNumber);
		} elseif (!empty($LRN)) {
			$this->db->where('LRN', $LRN);
		}

		$history_count = $this->db->count_all_results();

		echo json_encode(['history_count' => $history_count]);
	}




	public function get_schools_by_district()
	{
		$district = $this->input->post('district'); // Get selected district

		$this->db->select('schoolName');
		$this->db->from('schools');
		$this->db->where('district', $district); // Filter schools by district
		$query = $this->db->get();

		echo json_encode($query->result()); // Return as JSON
	}





	function med_patient_report()
	{
		$medID = $this->input->get('medID');

		$result['data'] = $this->SettingsModel->med_patient_report($medID);

		$rows = is_array($result['data']) ? $result['data'] : [$result['data']];
		if (!empty($rows) && isset($rows[0]) && is_object($rows[0])) {
			$row = $rows[0];
			$dob = $row->birthdate
				?? $row->BirthDate
				?? $row->Birthdate
				?? $row->bdate
				?? $row->DOB
				?? null;

			if ($dob) {
				try {
					$row->age = (new DateTime($dob))->diff(new DateTime('today'))->y;
				} catch (Exception $e) {
					$row->age = null;
				}
			} else {
				$row->age = null;
			}
		}
		$result['data'] = $rows;

		$result['settings']    = $this->SettingsModel->mis_settings();
		$result['med_setting'] = $this->SettingsModel->med_settings();

		$this->load->view('med_patient_report', $result);
	}

	function med_patient_reportv2()
	{
		$medID = $this->input->get('medID');

		$result['data'] = $this->SettingsModel->med_patient_report($medID);

		$rows = is_array($result['data']) ? $result['data'] : [$result['data']];
		if (!empty($rows) && isset($rows[0]) && is_object($rows[0])) {
			$row = $rows[0];
			$dob = $row->birthdate
				?? $row->BirthDate
				?? $row->Birthdate
				?? $row->bdate
				?? $row->DOB
				?? null;

			if ($dob) {
				try {
					$row->age = (new DateTime($dob))->diff(new DateTime('today'))->y;
				} catch (Exception $e) {
					$row->age = null;
				}
			} else {
				$row->age = null;
			}
		}
		$result['data'] = $rows;

		$result['settings']    = $this->SettingsModel->mis_settings();
		$result['med_setting'] = $this->SettingsModel->med_settings();

		$this->load->view('med_patient_reportv2', $result);
	}

	function med_patient_reportRX()
	{
		$medID = $this->input->get('medID');

		$result['data'] = $this->SettingsModel->med_patient_report($medID);

		$rows = is_array($result['data']) ? $result['data'] : [$result['data']];
		if (!empty($rows) && isset($rows[0]) && is_object($rows[0])) {
			$row = $rows[0];
			$dob = $row->birthdate
				?? $row->BirthDate
				?? $row->Birthdate
				?? $row->bdate
				?? $row->DOB
				?? null;

			if ($dob) {
				try {
					$row->age = (new DateTime($dob))->diff(new DateTime('today'))->y;
				} catch (Exception $e) {
					$row->age = null;
				}
			} else {
				$row->age = null;
			}
		}
		$result['data'] = $rows;

		$result['settings']    = $this->SettingsModel->mis_settings();
		$result['med_setting'] = $this->SettingsModel->med_settings();

		$this->load->view('med_patient_reportRX', $result);
	}

	function med_patient_referral()
	{
		$medID = $this->input->get('medID');

		$rows = $this->SettingsModel->med_patient_report($medID);
		$rows = is_array($rows) ? $rows : [$rows];

		if (!empty($rows) && isset($rows[0])) {
			$patient = $rows[0];
			$isObject = is_object($patient);
			$isArray = is_array($patient);

			if ($isObject || $isArray) {
				$rawAge = $isObject
					? ($patient->age ?? $patient->Age ?? null)
					: ($patient['age'] ?? $patient['Age'] ?? null);
				$rawAge = is_string($rawAge) ? trim($rawAge) : $rawAge;

				if ($rawAge === '' || $rawAge === null) {
					$dob = $isObject
						? ($patient->birthdate ?? $patient->BirthDate ?? $patient->Birthdate ?? $patient->bdate ?? $patient->DOB ?? null)
						: ($patient['birthdate'] ?? $patient['BirthDate'] ?? $patient['Birthdate'] ?? $patient['bdate'] ?? $patient['DOB'] ?? null);

					if (!empty($dob)) {
						try {
							$computedAge = (new DateTime($dob))->diff(new DateTime('today'))->y;
						} catch (Exception $e) {
							$computedAge = null;
						}
					} else {
						$computedAge = null;
					}

					if ($isObject) {
						$patient->age = $computedAge;
					} else {
						$patient['age'] = $computedAge;
					}
				} else {
					if ($isObject) {
						$patient->age = $rawAge;
					} else {
						$patient['age'] = $rawAge;
					}
				}

				$rows[0] = $patient;
			}
		}

		$result['data'] = $rows;
		$result['settings']    = $this->SettingsModel->mis_settings();
		$result['med_setting'] = $this->SettingsModel->med_settings();

		$this->load->view('med_patient_referral', $result);
	}

	private function capture_med_patient_form_input()
	{
		$keys = [
			'patientType',
			'FirstName',
			'MiddleName',
			'LastName',
			'IDNumber',
			'LRN',
			'district',
			'school',
			'contact',
			'sex',
			'birthdate',
			'age',
			'address',
			'bp',
			'cardiac',
			'respiratory',
			'temp',
			'height',
			'weight',
			'cstat',
			'sat',
			'complaint',
			'others_symp',
			'allergies',
			'category',
			'disease',
			'current_med',
			'disposition',
			'rest_no',
			'referral_facility',
			'referral_reason',
			'referral_guardian',
			'purpose',
			'illness_history',
			'phy_exam',
			'diagnosis',
			'treatment',
			'remarks',
			'disposition_time',
			'appdate',
			'position',
			'username',
			'print_Perm',
		];

		$values = [];
		foreach ($keys as $key) {
			$value = $this->input->post($key);
			if ($value !== null) {
				$values[$key] = $value;
			}
		}

		return $values;
	}

	private function parse_med_attachments($rawValue)
	{
		$raw = trim((string) $rawValue);
		if ($raw === '') {
			return [];
		}

		$decoded = json_decode($raw, true);
		if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
			$attachments = [];
			foreach ($decoded as $fileName) {
				$name = trim((string) $fileName);
				if ($name !== '') {
					$attachments[] = $name;
				}
			}
			return array_values(array_unique($attachments));
		}

		return [$raw];
	}

	private function format_med_attachments(array $attachments)
	{
		$clean = [];
		foreach ($attachments as $fileName) {
			$name = trim((string) $fileName);
			if ($name !== '') {
				$clean[] = $name;
			}
		}
		$clean = array_values(array_unique($clean));

		if (empty($clean)) {
			return '';
		}
		if (count($clean) === 1) {
			return $clean[0];
		}

		return json_encode($clean, JSON_UNESCAPED_SLASHES);
	}

	private function upload_med_attachment_files($fieldName = 'attachment', $maxFiles = 3)
	{
		if (!isset($_FILES[$fieldName])) {
			return [];
		}

		$fileBag = $_FILES[$fieldName];
		$queuedFiles = [];

		if (is_array($fileBag['name'])) {
			$total = count($fileBag['name']);
			for ($i = 0; $i < $total; $i++) {
				$name = trim((string) ($fileBag['name'][$i] ?? ''));
				if ($name === '') {
					continue;
				}
				$queuedFiles[] = [
					'name' => $fileBag['name'][$i],
					'type' => $fileBag['type'][$i],
					'tmp_name' => $fileBag['tmp_name'][$i],
					'error' => $fileBag['error'][$i],
					'size' => $fileBag['size'][$i],
				];
			}
		} else {
			$name = trim((string) ($fileBag['name'] ?? ''));
			if ($name !== '') {
				$queuedFiles[] = $fileBag;
			}
		}

		if (count($queuedFiles) > $maxFiles) {
			throw new RuntimeException('You can upload up to ' . $maxFiles . ' attachments only.');
		}
		if (empty($queuedFiles)) {
			return [];
		}

		$this->load->library('upload');
		$uploaded = [];
		$maxSizeKb = 10000; // 10 MB per file

		foreach ($queuedFiles as $file) {
			$fileError = (int) ($file['error'] ?? UPLOAD_ERR_OK);
			if ($fileError !== UPLOAD_ERR_OK) {
				unset($_FILES['__med_attachment']);
				if ($fileError === UPLOAD_ERR_INI_SIZE || $fileError === UPLOAD_ERR_FORM_SIZE) {
					throw new RuntimeException('File upload error: One or more files exceed the 10 MB size limit.');
				}
				throw new RuntimeException('File upload error: Upload failed for one or more files.');
			}

			$originalName = preg_replace('/[^A-Za-z0-9._-]/', '_', basename((string) $file['name']));
			$_FILES['__med_attachment'] = $file;

			$config = [
				'upload_path' => './uploads/med/',
				'allowed_types' => 'jpg|jpeg|png|pdf|doc|docx',
				'max_size' => $maxSizeKb,
				'file_name' => time() . '_' . mt_rand(1000, 9999) . '_' . $originalName,
			];

			$this->upload->initialize($config);
			if (!$this->upload->do_upload('__med_attachment')) {
				$error = strip_tags((string) $this->upload->display_errors());
				unset($_FILES['__med_attachment']);
				throw new RuntimeException('File upload error: ' . trim($error));
			}

			$uploadData = $this->upload->data();
			$storedName = trim((string) ($uploadData['file_name'] ?? ''));
			if ($storedName !== '') {
				$uploaded[] = $storedName;
			}
		}

		unset($_FILES['__med_attachment']);
		return $uploaded;
	}

	private function get_med_notification_recipient()
	{
		return 'darlwill.bayocboc@deped.gov.ph';
	}

	private function send_pending_consultation_email($patient)
	{
		if (empty($patient) || empty($patient->medID)) {
			return false;
		}

		$recipient = $this->get_med_notification_recipient();

		$this->load->config('email');
		$this->load->library('email');

		$patientName = trim(($patient->FirstName ?? '') . ' ' . ($patient->MiddleName ?? '') . ' ' . ($patient->LastName ?? ''));
		if ($patientName === '') {
			$patientName = 'Patient';
		}

		$subject = 'Pending Consultation Alert - ' . $patientName;
		$pendingLink = base_url('Page/med_patient_pending');
		$dashboardLink = base_url('Page/Health');
		$message = '<div style="background:#f5f7fb;padding:24px;font-family:Arial, sans-serif;color:#1f2937;">';
		$message .= '<div style="max-width:720px;margin:0 auto;background:#ffffff;border:1px solid #e5e7eb;border-radius:8px;overflow:hidden;">';
		$message .= '<div style="background:#38bdf8;color:#ffffff;padding:16px 20px;font-size:18px;font-weight:600;">Pending Consultation Notification</div>';
		$message .= '<div style="padding:20px;">';
		$message .= '<p style="margin:0 0 10px 0;">Good day,</p>';
		$message .= '<p style="margin:0 0 12px 0;">A new consultation has been saved with status <strong>Pending</strong>.</p>';
		$message .= '<table cellpadding="8" cellspacing="0" border="0" style="width:100%;border-collapse:collapse;font-size:14px;border:1px solid #e5e7eb;">';
		$message .= '<tr><td style="width:32%;background:#f8fafc;font-weight:600;border:1px solid #e5e7eb;">Patient</td><td style="border:1px solid #e5e7eb;">' . htmlspecialchars($patientName) . '</td></tr>';
		$message .= '<tr><td style="width:32%;background:#f8fafc;font-weight:600;border:1px solid #e5e7eb;">District</td><td style="border:1px solid #e5e7eb;">' . htmlspecialchars((string) ($patient->district ?? '')) . '</td></tr>';
		$message .= '<tr><td style="width:32%;background:#f8fafc;font-weight:600;border:1px solid #e5e7eb;">School</td><td style="border:1px solid #e5e7eb;">' . htmlspecialchars((string) ($patient->school ?? '')) . '</td></tr>';
		$message .= '<tr><td style="width:32%;background:#f8fafc;font-weight:600;border:1px solid #e5e7eb;">Cardiac Rate</td><td style="border:1px solid #e5e7eb;">' . htmlspecialchars((string) ($patient->cardiac ?? '')) . '</td></tr>';
		$message .= '<tr><td style="width:32%;background:#f8fafc;font-weight:600;border:1px solid #e5e7eb;">Respiratory Rate</td><td style="border:1px solid #e5e7eb;">' . htmlspecialchars((string) ($patient->respiratory ?? '')) . '</td></tr>';
		$message .= '<tr><td style="width:32%;background:#f8fafc;font-weight:600;border:1px solid #e5e7eb;">Temperature</td><td style="border:1px solid #e5e7eb;">' . htmlspecialchars((string) ($patient->temp ?? '')) . '</td></tr>';
		$message .= '<tr><td style="width:32%;background:#f8fafc;font-weight:600;border:1px solid #e5e7eb;">O2 SAT</td><td style="border:1px solid #e5e7eb;">' . htmlspecialchars((string) ($patient->sat ?? '')) . '</td></tr>';
		$message .= '<tr><td style="width:32%;background:#f8fafc;font-weight:600;border:1px solid #e5e7eb;">Chief Complaint</td><td style="border:1px solid #e5e7eb;">' . nl2br(htmlspecialchars((string) ($patient->complaint ?? ''))) . '</td></tr>';
		$message .= '<tr><td style="width:32%;background:#f8fafc;font-weight:600;border:1px solid #e5e7eb;">Allergies</td><td style="border:1px solid #e5e7eb;">' . nl2br(htmlspecialchars((string) ($patient->allergies ?? ''))) . '</td></tr>';
		$message .= '<tr><td style="width:32%;background:#f8fafc;font-weight:600;border:1px solid #e5e7eb;">Current Medication</td><td style="border:1px solid #e5e7eb;">' . nl2br(htmlspecialchars((string) ($patient->current_med ?? ''))) . '</td></tr>';
		$message .= '<tr><td style="width:32%;background:#f8fafc;font-weight:600;border:1px solid #e5e7eb;">Physical Examination</td><td style="border:1px solid #e5e7eb;">' . nl2br(htmlspecialchars((string) ($patient->phy_exam ?? ''))) . '</td></tr>';
		$message .= '</table>';
		$message .= '<div style="margin-top:16px;padding:12px;border:1px dashed #cbd5e1;border-radius:6px;background:#f8fafc;">';
		$message .= '<strong>Open Pending Consultations:</strong> <a href="' . $pendingLink . '" target="_blank" style="color:#0f4c81;">' . htmlspecialchars($pendingLink) . '</a><br>';
		$message .= '<strong>Open Health Dashboard:</strong> <a href="' . $dashboardLink . '" target="_blank" style="color:#0f4c81;">' . htmlspecialchars($dashboardLink) . '</a>';
		$message .= '</div>';
		$message .= '<p style="margin:18px 0 0 0;color:#6b7280;">Thank you.</p>';
		$message .= '</div></div></div>';

		$this->email->from('no-reply@depeddavor.com', 'DepEd MIS');
		$this->email->to($recipient);
		$this->email->subject($subject);
		$this->email->message($message);
		$sent = (bool) $this->email->send();
		if (!$sent) {
			$debug = $this->email->print_debugger(['headers']);
			log_message('error', 'Pending consultation email failed: ' . $debug);
		}

		return $sent;
	}

	private function send_referral_email($patient)
	{
		if (empty($patient) || empty($patient->medID)) {
			return false;
		}

		$recipient = $this->get_med_notification_recipient();

		$this->load->config('email');
		$this->load->library('email');

		$patientName = trim(($patient->FirstName ?? '') . ' ' . ($patient->MiddleName ?? '') . ' ' . ($patient->LastName ?? ''));
		$age = $patient->age ?? '';
		if (($age === '' || $age === null) && !empty($patient->birthdate)) {
			try {
				$age = (new DateTime($patient->birthdate))->diff(new DateTime('today'))->y;
			} catch (Exception $e) {
				$age = $patient->age ?? '';
			}
		}
		$subject = 'Referral Notification - ' . ($patientName !== '' ? $patientName : 'Patient');
		$link = base_url('Page/med_patient_referral?medID=' . urlencode((string) $patient->medID) . '&download=1');

		$attachmentLinks = [];
		foreach ($this->parse_med_attachments($patient->attachment ?? '') as $fileName) {
			$attachmentLinks[] = base_url('uploads/med/' . rawurlencode((string) $fileName));
		}
		$attachmentText = implode("\n", $attachmentLinks);

		$sections = [
			'Patient Information' => [
				'Patient Name' => $patientName,
				'Patient Type' => $patient->patientType ?? '',
				'ID Number' => $patient->IDNumber ?? '',
				'Position/Designation' => $patient->position ?? '',
				'Age' => $age,
				'Birthdate' => $patient->birthdate ?? '',
				'Sex' => $patient->sex ?? '',
				'Civil Status' => $patient->cstat ?? '',
				'Address' => $patient->address ?? '',
				'Contact' => $patient->contact ?? '',
				'School' => $patient->school ?? '',
				'District' => $patient->district ?? '',
			],
			'Vitals' => [
				'BP' => $patient->bp ?? '',
				'Cardiac Rate' => $patient->cardiac ?? '',
				'Respiratory Rate' => $patient->respiratory ?? '',
				'Temperature' => $patient->temp ?? '',
				'O2 SAT' => $patient->sat ?? '',
				'Height' => $patient->height ?? '',
				'Weight' => $patient->weight ?? '',
			],
			'Clinical Details' => [
				'Chief Complaint' => $patient->complaint ?? '',
				'Other Symptoms' => $patient->others_symp ?? '',
				'Allergies' => $patient->allergies ?? '',
				'Current Medication' => $patient->current_med ?? '',
				'Purpose' => $patient->purpose ?? '',
				'History of Present Illness' => $patient->illness_history ?? '',
				'Physical Exam' => $patient->phy_exam ?? '',
				'Diagnosis' => $patient->diagnosis ?? '',
				'Treatment/Management' => $patient->treatment ?? '',
				'Remarks' => $patient->remarks ?? '',
				'Category' => $patient->category ?? '',
				'Disease' => $patient->disease ?? '',
				'Disposition' => $patient->disposition ?? '',
				'Rest Required (Days)' => $patient->rest_no ?? '',
				'Disposition Time' => $patient->disposition_time ?? '',
				'Appointment Date' => $patient->appdate ?? '',
			],
			'Referral Details' => [
				'Referral Facility' => $patient->referral_facility ?? '',
				'Reasons for Referral' => $patient->referral_reason ?? '',
				'Parents/Guardian' => $patient->referral_guardian ?? '',
				'Attachments' => $attachmentText,
			],
		];

		$message = '<div style="background:#f5f7fb;padding:24px;font-family:Arial, sans-serif;color:#1f2937;">';
		$message .= '<div style="max-width:720px;margin:0 auto;background:#ffffff;border:1px solid #e5e7eb;border-radius:8px;overflow:hidden;">';
		$message .= '<div style="background:#0f4c81;color:#ffffff;padding:16px 20px;font-size:18px;font-weight:600;">Referral Notification</div>';
		$message .= '<div style="padding:20px;">';
		$message .= '<p style="margin:0 0 10px 0;">Good day,</p>';
		$message .= '<p style="margin:0 0 12px 0;">A patient consultation has been marked as <strong>Transferred/Referred</strong>.</p>';
		$message .= '<p style="margin:0 0 18px 0;color:#6b7280;">Below are the referral details.</p>';

		foreach ($sections as $sectionTitle => $rows) {
			$hasRows = false;
			foreach ($rows as $value) {
				$val = is_null($value) ? '' : trim((string) $value);
				if ($val !== '') {
					$hasRows = true;
					break;
				}
			}
			if (!$hasRows) {
				continue;
			}

			$message .= '<h3 style="margin:18px 0 8px 0;font-size:14px;text-transform:uppercase;letter-spacing:.04em;color:#0f4c81;">' . htmlspecialchars($sectionTitle) . '</h3>';
			$message .= '<table cellpadding="8" cellspacing="0" border="0" style="width:100%;border-collapse:collapse;font-size:14px;border:1px solid #e5e7eb;">';
			foreach ($rows as $label => $value) {
				$val = is_null($value) ? '' : trim((string) $value);
				if ($val === '') {
					continue;
				}
				$safeVal = nl2br(htmlspecialchars((string) $value));
				if ($label === 'Attachments' && !empty($attachmentLinks)) {
					$linksMarkup = [];
					foreach ($attachmentLinks as $attachmentLink) {
						$linksMarkup[] = '<a href="' . $attachmentLink . '" target="_blank" style="color:#0f4c81;">' . htmlspecialchars($attachmentLink) . '</a>';
					}
					$safeVal = implode('<br>', $linksMarkup);
				}
				$message .= '<tr>';
				$message .= '<td style="width:32%;background:#f8fafc;font-weight:600;border:1px solid #e5e7eb;">' . htmlspecialchars($label) . '</td>';
				$message .= '<td style="border:1px solid #e5e7eb;">' . $safeVal . '</td>';
				$message .= '</tr>';
			}
			$message .= '</table>';
		}

		$message .= '<div style="margin-top:16px;padding:12px;border:1px dashed #cbd5e1;border-radius:6px;background:#f8fafc;">';
		$message .= '<strong>Referral Form:</strong> <a href="' . $link . '" target="_blank" style="color:#0f4c81;">Download Referral Slip</a>';
		$message .= '</div>';
		$message .= '<p style="margin:18px 0 0 0;color:#6b7280;">Thank you.</p>';
		$message .= '</div></div></div>';

		$this->email->from('no-reply@depeddavor.com', 'DepEd MIS');
		$this->email->to($recipient);
		$this->email->subject($subject);
		$this->email->message($message);
		$sent = (bool) $this->email->send();
		if (!$sent) {
			$debug = $this->email->print_debugger(['headers']);
			log_message('error', 'Referral email failed: ' . $debug);
		}
		return $sent;
	}


	public function med_patient_update()
	{
		$medID = (int) $this->input->get('medID');
		$existing = $this->db->get_where('med_patient', ['medID' => $medID])->row();
		$listRoute = ($existing && strtolower((string) $existing->patientType) === 'student')
			? 'Page/med_patient_student'
			: 'Page/med_patient';

		if (empty($medID) || !$existing) {
			$this->session->set_flashdata('danger', 'Patient record not found.');
			redirect(base_url($listRoute));
			return;
		}


		if ($this->input->post('submit')) {
			// Get form data
			$updateData = array(
				'address' => $this->input->post('address'),
				'contact' => $this->input->post('contact'),
				'age' => $this->input->post('age'),
				'birthdate' => $this->input->post('birthdate'),
				'sex' => $this->input->post('sex'),
				'bp' => $this->input->post('bp'),
				'cardiac' => $this->input->post('cardiac'),
				'respiratory' => $this->input->post('respiratory'),
				'temp' => $this->input->post('temp'),
				'sat' => $this->input->post('sat'),
				'height' => $this->input->post('height'),
				'weight' => $this->input->post('weight'),
				'complaint' => $this->input->post('complaint'),
				'allergies' => $this->input->post('allergies'),
				'current_med' => $this->input->post('current_med'),
				'phy_exam' => $this->input->post('phy_exam'),
				'diagnosis' => $this->input->post('diagnosis'),
				'treatment' => $this->input->post('treatment'),
				'disposition' => $this->input->post('disposition'),
				'remarks' => $this->input->post('remarks'),
				'appdate' => $this->input->post('appdate'),
				'disposition_time' => $this->input->post('disposition_time'),
				'purpose' => $this->input->post('purpose'),
				'position' => $this->input->post('position'),
				'cstat' => $this->input->post('cstat'),
				'district' => $this->input->post('district'),
				'school' => $this->input->post('school'),
				'others_symp' => $this->input->post('others_symp'),
				'illness_history' => $this->input->post('illness_history'),
				'rest_no' => $this->input->post('rest_no'),
				'category' => $this->input->post('category'),
				'disease' => $this->input->post('disease'),
				'referral_facility' => $this->input->post('referral_facility'),
				'referral_reason' => $this->input->post('referral_reason'),
				'referral_guardian' => $this->input->post('referral_guardian'),
				'consultationStat' => 'Processed' // Update status to "Processed"
			);

			$this->db->where('medID', $medID);
			$this->db->update('med_patient', $updateData);

			$emailSent = false;
			if (($updateData['disposition'] ?? '') === 'Transferred/Referred') {
				$patient = $this->db->get_where('med_patient', ['medID' => $medID])->row();
				$emailSent = $this->send_referral_email($patient);
			}

			$msg = 'Patient record updated successfully.';
			if ($emailSent) {
				$msg .= ' Referral email sent.';
			}
			$this->session->set_flashdata('success', $msg);

			redirect(base_url($listRoute));
			return;
		}

		$result['data'] = $this->SettingsModel->med_patient_report($medID);
		if (empty($result['data'])) {
			$this->session->set_flashdata('danger', 'Patient record not found.');
			redirect(base_url($listRoute));
			return;
		}

		$result['settings'] = $this->SettingsModel->mis_settings();
		$result['med_setting'] = $this->SettingsModel->med_settings();
		$result['district'] = $this->SettingsModel->district();
		$result['category'] = $this->SettingsModel->category();
		$result['disposition'] = $this->SettingsModel->disposition();


		$this->load->view('med_patient_update', $result);
	}


	// public function med_patient_update()
	// {
	// 	$medID = $this->input->get('medID');

	// 	if ($this->input->post('submit')) {
	// 		// Get form data
	// 		$updateData = array(
	// 			'phy_exam' => $this->input->post('phy_exam'),
	// 			'diagnosis' => $this->input->post('diagnosis'),
	// 			'remarks' => $this->input->post('remarks'),
	// 			'consultationStat' => 'Processed' // Update status to "Processed"
	// 		);

	// 		// Update the database
	// 		$this->db->where('medID', $medID);
	// 		$this->db->update('med_patient', $updateData);

	// 		// Redirect after update (change the URL accordingly)
	// 		redirect(base_url('Page/med_patient'));
	// 	}

	// 	// Fetch patient report data
	// 	$result['data'] = $this->SettingsModel->med_patient_report($medID);

	// 	// Fetch settings separately
	// 	$result['settings'] = $this->SettingsModel->mis_settings();
	// 	$result['med_setting'] = $this->SettingsModel->med_settings();

	// 	// Load the view
	// 	$this->load->view('med_patient_update', $result);
	// }


	function delete_medpatient()
	{
		$id = $this->input->get('id');
		$this->db->query("delete  from med_patient where medID='" . $id . "'");
		$this->Page_model->insert_at('Deleted Patient Case', $this->db->insert_id());
		$this->session->set_flashdata('success', 'Deleted successfully!');
		redirect('Page/med_patient');
	}

	function delete_medpatient1()
	{
		$id = $this->input->get('id');
		$this->db->query("delete  from med_patient where medID='" . $id . "'");
		$this->Page_model->insert_at('Deleted Patient Case', $this->db->insert_id());
		$this->session->set_flashdata('success', 'Deleted successfully!');
		redirect('Page/med_patient_student');
	}


	public function med_patient_update1()
	{
		$this->load->library('upload');
		$medID = (int) $this->input->get('medID');

		// Get existing attachment filename (optional fallback)
		$existing = $this->db->get_where('med_patient', ['medID' => $medID])->row();
		$listRoute = ($existing && strtolower((string) $existing->patientType) === 'student')
			? 'Page/med_patient_student'
			: 'Page/med_patient';

		if (empty($medID) || !$existing) {
			$this->session->set_flashdata('danger', 'Patient record not found.');
			redirect(base_url($listRoute));
			return;
		}

		if ($this->input->post('submit')) {
			$existingAttachments = $this->parse_med_attachments($existing->attachment ?? '');
			try {
				$newAttachments = $this->upload_med_attachment_files('attachment', 3);
			} catch (RuntimeException $e) {
				$this->session->set_flashdata('danger', $e->getMessage());
				redirect(base_url('Page/med_patient_update1?medID=' . urlencode((string) $medID)));
				return;
			}

			$allAttachments = $existingAttachments;
			if (!empty($newAttachments)) {
				$allAttachments = array_values(array_unique(array_merge($existingAttachments, $newAttachments)));
			}
			if (count($allAttachments) > 3) {
				$this->session->set_flashdata('danger', 'A maximum of 3 attachments is allowed per patient record.');
				redirect(base_url('Page/med_patient_update1?medID=' . urlencode((string) $medID)));
				return;
			}

			$attachment = $this->format_med_attachments($allAttachments);

			// Collect form data
			$updateData = array(
				'address' => $this->input->post('address'),
				'contact' => $this->input->post('contact'),
				'age' => $this->input->post('age'),
				'birthdate' => $this->input->post('birthdate'),
				'sex' => $this->input->post('sex'),
				'bp' => $this->input->post('bp'),
				'cardiac' => $this->input->post('cardiac'),
				'respiratory' => $this->input->post('respiratory'),
				'temp' => $this->input->post('temp'),
				'sat' => $this->input->post('sat'),
				'height' => $this->input->post('height'),
				'weight' => $this->input->post('weight'),
				'complaint' => $this->input->post('complaint'),
				'allergies' => $this->input->post('allergies'),
				'current_med' => $this->input->post('current_med'),
				'phy_exam' => $this->input->post('phy_exam'),
				'diagnosis' => $this->input->post('diagnosis'),
				'treatment' => $this->input->post('treatment'),
				'disposition' => $this->input->post('disposition'),
				'remarks' => $this->input->post('remarks'),
				'appdate' => $this->input->post('appdate'),
				'disposition_time' => $this->input->post('disposition_time'),
				'purpose' => $this->input->post('purpose'),
				'position' => $this->input->post('position'),
				'cstat' => $this->input->post('cstat'),
				'district' => $this->input->post('district'),
				'school' => $this->input->post('school'),
				'others_symp' => $this->input->post('others_symp'),
				'illness_history' => $this->input->post('illness_history'),
				'rest_no' => $this->input->post('rest_no'),
				'category' => $this->input->post('category'),
				'disease' => $this->input->post('disease'),
				'referral_facility' => $this->input->post('referral_facility'),
				'referral_reason' => $this->input->post('referral_reason'),
				'referral_guardian' => $this->input->post('referral_guardian'),
				'attachment' => $attachment // Save attachment
			);

			// Update DB
			$this->db->where('medID', $medID);
			$this->db->update('med_patient', $updateData);

			$emailSent = false;
			if (($updateData['disposition'] ?? '') === 'Transferred/Referred') {
				$patient = $this->db->get_where('med_patient', ['medID' => $medID])->row();
				$emailSent = $this->send_referral_email($patient);
			}

			// Redirect
			$msg = 'Patient record updated successfully.';
			if ($emailSent) {
				$msg .= ' Referral email sent.';
			}
			$this->session->set_flashdata('success', $msg);
			redirect(base_url($listRoute));
			return;
		}

		// You may load the form here if you want edit page display logic



		// Fetch patient report data
		$result['data'] = $this->SettingsModel->med_patient_report($medID);
		if (empty($result['data'])) {
			$this->session->set_flashdata('danger', 'Patient record not found.');
			redirect(base_url($listRoute));
			return;
		}

		// Fetch settings separately
		$result['settings'] = $this->SettingsModel->mis_settings();
		$result['med_setting'] = $this->SettingsModel->med_settings();
		$result['district'] = $this->SettingsModel->district();
		$result['category'] = $this->SettingsModel->category();
		$result['disposition'] = $this->SettingsModel->disposition();


		// Load the view
		$this->load->view('med_patient_update1', $result);
	}







	public function med_patient_update_student()
	{
		$this->load->library('upload');
		$medID = (int) $this->input->get('medID');

		// Get existing attachment filename (optional fallback)
		$existing = $this->db->get_where('med_patient', ['medID' => $medID])->row();
		$listRoute = 'Page/med_patient_student';

		if (empty($medID) || !$existing) {
			$this->session->set_flashdata('danger', 'Patient record not found.');
			redirect(base_url($listRoute));
			return;
		}

		if ($this->input->post('submit')) {
			$existingAttachments = $this->parse_med_attachments($existing->attachment ?? '');
			try {
				$newAttachments = $this->upload_med_attachment_files('attachment', 3);
			} catch (RuntimeException $e) {
				$this->session->set_flashdata('danger', $e->getMessage());
				redirect(base_url('Page/med_patient_update_student?medID=' . urlencode((string) $medID)));
				return;
			}

			$allAttachments = $existingAttachments;
			if (!empty($newAttachments)) {
				$allAttachments = array_values(array_unique(array_merge($existingAttachments, $newAttachments)));
			}
			if (count($allAttachments) > 3) {
				$this->session->set_flashdata('danger', 'A maximum of 3 attachments is allowed per patient record.');
				redirect(base_url('Page/med_patient_update_student?medID=' . urlencode((string) $medID)));
				return;
			}

			$attachment = $this->format_med_attachments($allAttachments);

			// Collect form data
			$updateData = array(
				'address' => $this->input->post('address'),
				'contact' => $this->input->post('contact'),
				'age' => $this->input->post('age'),
				'birthdate' => $this->input->post('birthdate'),
				'sex' => $this->input->post('sex'),
				'bp' => $this->input->post('bp'),
				'cardiac' => $this->input->post('cardiac'),
				'respiratory' => $this->input->post('respiratory'),
				'temp' => $this->input->post('temp'),
				'sat' => $this->input->post('sat'),
				'height' => $this->input->post('height'),
				'weight' => $this->input->post('weight'),
				'complaint' => $this->input->post('complaint'),
				'allergies' => $this->input->post('allergies'),
				'current_med' => $this->input->post('current_med'),
				'phy_exam' => $this->input->post('phy_exam'),
				'diagnosis' => $this->input->post('diagnosis'),
				'treatment' => $this->input->post('treatment'),
				'disposition' => $this->input->post('disposition'),
				'remarks' => $this->input->post('remarks'),
				'appdate' => $this->input->post('appdate'),
				'disposition_time' => $this->input->post('disposition_time'),
				'purpose' => $this->input->post('purpose'),
				'position' => $this->input->post('position'),
				'cstat' => $this->input->post('cstat'),
				'district' => $this->input->post('district'),
				'school' => $this->input->post('school'),
				'others_symp' => $this->input->post('others_symp'),
				'illness_history' => $this->input->post('illness_history'),
				'rest_no' => $this->input->post('rest_no'),
				'category' => $this->input->post('category'),
				'disease' => $this->input->post('disease'),
				'referral_facility' => $this->input->post('referral_facility'),
				'referral_reason' => $this->input->post('referral_reason'),
				'referral_guardian' => $this->input->post('referral_guardian'),
				'attachment' => $attachment // Save attachment
			);

			// Update DB
			$this->db->where('medID', $medID);
			$this->db->update('med_patient', $updateData);

			$emailSent = false;
			if (($updateData['disposition'] ?? '') === 'Transferred/Referred') {
				$patient = $this->db->get_where('med_patient', ['medID' => $medID])->row();
				$emailSent = $this->send_referral_email($patient);
			}

			// Redirect
			$msg = 'Patient record updated successfully.';
			if ($emailSent) {
				$msg .= ' Referral email sent.';
			}
			$this->session->set_flashdata('success', $msg);
			redirect(base_url($listRoute));
			return;
		}

		// Fetch patient report data
		$result['data'] = $this->SettingsModel->med_patient_report($medID);
		if (empty($result['data'])) {
			$this->session->set_flashdata('danger', 'Patient record not found.');
			redirect(base_url($listRoute));
			return;
		}

		// Fetch settings separately
		$result['settings'] = $this->SettingsModel->mis_settings();
		$result['med_setting'] = $this->SettingsModel->med_settings();
		$result['district'] = $this->SettingsModel->district();
		$result['category'] = $this->SettingsModel->category();
		$result['disposition'] = $this->SettingsModel->disposition();


		// Load the view
		$this->load->view('med_patient_update_student', $result);
	}





	function delete_medpatient_student()
	{
		$id = $this->input->get('id');
		$this->db->query("delete  from med_patient where medID='" . $id . "'");
		$this->Page_model->insert_at('Deleted Patient Case', $this->db->insert_id());
		$this->session->set_flashdata('success', 'Deleted successfully!');
		redirect('Page/med_patient_student');
	}






	function lab_request()
	{
		$result['data'] = $this->SettingsModel->med_labPatient();
		$this->load->view('med_lab_request', $result);
	}


	public function count_consultation()
	{
		$IDNumber = $this->input->get('IDNumber'); // Get IDNumber from request
		$data['data'] = $this->SettingsModel->count_consultation($IDNumber); // Pass to model
		$this->load->view('count_consultation', $data); // Load view with filtered data
	}


	public function count_consultation1()
	{
		$LRN = $this->input->get('LRN'); // Get IDNumber from request
		$data['consultations'] = $this->SettingsModel->count_consultation1($LRN); // Pass to model
		$this->load->view('count_consultation1', $data); // Load view with filtered data
	}


	function med_settings()
	{
		$result['data'] = $this->SettingsModel->med_settings_info();
		$this->load->view('med_settings_info', $result);
	}


	public function updateSignature()
	{
		$med_id = $this->input->post('med_id');

		// File Upload Configuration
		$config['upload_path']   = './assets/images/';
		$config['allowed_types'] = 'jpg|jpeg|png';
		$config['file_name']     = 'signature_' . time();
		$config['overwrite']     = true;

		$this->load->library('upload', $config);

		if ($this->upload->do_upload('signatureFile')) {
			$uploadData = $this->upload->data();
			$signatureFile = $uploadData['file_name'];

			// Update database with new signature
			$data = array('signature' => $signatureFile);
			$this->db->where('med_id', $med_id);
			$this->db->update('med_settings', $data);

			$this->session->set_flashdata('success', 'Signature updated successfully.');
		} else {
			$this->session->set_flashdata('danger', 'Error updating signature.');
		}

		redirect('Page/med_settings');
	}


	public function get_students()
	{
		$this->load->model('SettingsModel');
		$students = $this->SettingsModel->med_patient(); // Fetch students from DB
		echo json_encode($students);
	}










	public function add_request()
	{
		$data['district'] = $this->SettingsModel->district();
		$this->load->view('med_lab_add', $data);  // Load the form view with district data
	}
	public function save_request()
	{
		$birthdate = trim((string) $this->input->post('birthdate'));
		$postedAge = trim((string) $this->input->post('age'));
		$computedAge = '';
		if ($birthdate !== '') {
			try {
				$computedAge = (string) ((new DateTime($birthdate))->diff(new DateTime('today'))->y);
			} catch (Exception $e) {
				$computedAge = '';
			}
		}
		$ageValue = ($postedAge !== '') ? $postedAge : $computedAge;

		// Collect test categories from input
		$test_categories = [
			'lab_test' => $this->input->post('lab_test') ?? [],
			'bleed_test' => $this->input->post('bleed_test') ?? [],
			'hepatitis_test' => $this->input->post('hepatitis_test') ?? [],
			'cardiac' => $this->input->post('cardiac') ?? [],
			'blood_test' => $this->input->post('blood_test') ?? [],
			'liver_profile' => $this->input->post('liver_profile') ?? [],
			'renal_func' => $this->input->post('renal_func') ?? [],
			'serology' => $this->input->post('serology') ?? [],
			'thyroid' => $this->input->post('thyroid') ?? [],
			'x_ray' => $this->input->post('x_ray') ?? [],
			'ultrasound' => $this->input->post('ultrasound') ?? []
		];
		$normalizedTests = [];
		foreach ($test_categories as $column => $tests) {
			$cleanTests = [];
			foreach ((array) $tests as $testName) {
				$name = trim((string) $testName);
				if ($name !== '') {
					$cleanTests[] = $name;
				}
			}
			$normalizedTests[$column] = array_values(array_unique($cleanTests));
		}

		// If "Others" is checked, attach the user-provided specific test.
		$otherMap = [
			'lab_test' => 'lab_test_other',
			'serology' => 'serology_other',
			'thyroid' => 'thyroid_other',
			'x_ray' => 'x_ray_other',
			'ultrasound' => 'ultrasound_other',
		];
		foreach ($otherMap as $column => $inputName) {
			if (!isset($normalizedTests[$column])) {
				continue;
			}
			$otherValue = trim((string) $this->input->post($inputName));
			foreach ($normalizedTests[$column] as $idx => $item) {
				if ($item !== 'Others') {
					continue;
				}
				$normalizedTests[$column][$idx] = ($otherValue !== '') ? ('Others: ' . $otherValue) : 'Others';
			}
		}

		// Extract patient details
		$patientData = [
			'patientType' => $this->input->post('patientType'),
			'FirstName' => $this->input->post('FirstName'),
			'MiddleName' => $this->input->post('MiddleName'),
			'LastName' => $this->input->post('LastName'),
			'sex' => $this->input->post('sex'),
			'birthdate' => $birthdate,
			'age' => $ageValue,
			'address' => $this->input->post('address'),
			'district' => $this->input->post('district'),
			'school' => $this->input->post('school'),
			'date_request' => date('Y-m-d H:i:s')
		];

		$hasRequestedTest = false;
		foreach ($normalizedTests as $tests) {
			if (!empty($tests)) {
				$hasRequestedTest = true;
				break;
			}
		}

		// If no tests are selected, stop the process
		if (!$hasRequestedTest) {
			$this->session->set_flashdata('danger', 'No tests selected.');
			redirect('Page/lab_request');
			return;
		}

		$record = $patientData;
		foreach ($normalizedTests as $column => $tests) {
			$record[$column] = implode(', ', $tests);
		}

		// Insert one row per laboratory request with all selected tests.
		if ($this->db->insert('med_labrequest', $record)) {
			$this->session->set_flashdata('success', 'Lab requests saved successfully!');
		} else {
			$this->session->set_flashdata('danger', 'Failed to save laboratory request.');
		}
		redirect('Page/lab_request');
	}






	public function delete_labReq($labID)
	{
		// Load the model
		$this->load->model('SettingsModel');

		// Delete record
		if ($this->SettingsModel->delete_labReq($labID)) {
			$this->session->set_flashdata('success', 'Lab request deleted successfully!');
		} else {
			$this->session->set_flashdata('error', 'Failed to delete lab request.');
		}

		// Redirect to the lab request page
		redirect('Page/lab_request');
	}

	public function edit_labReq($labID)
	{
		$labID = (int) $labID;
		$row = $this->db->get_where('med_labrequest', ['labID' => $labID])->row();
		if (!$row) {
			$this->session->set_flashdata('danger', 'Laboratory request not found.');
			redirect('Page/lab_request');
			return;
		}

		$data['row'] = $row;
		$data['district'] = $this->SettingsModel->district();
		$this->load->view('med_lab_edit', $data);
	}

	public function update_labReq($labID)
	{
		$labID = (int) $labID;
		$row = $this->db->get_where('med_labrequest', ['labID' => $labID])->row();
		if (!$row) {
			$this->session->set_flashdata('danger', 'Laboratory request not found.');
			redirect('Page/lab_request');
			return;
		}

		$birthdate = trim((string) $this->input->post('birthdate'));
		$postedAge = trim((string) $this->input->post('age'));
		$computedAge = '';
		if ($birthdate !== '') {
			try {
				$computedAge = (string) ((new DateTime($birthdate))->diff(new DateTime('today'))->y);
			} catch (Exception $e) {
				$computedAge = '';
			}
		}
		$ageValue = ($postedAge !== '') ? $postedAge : $computedAge;

		$test_categories = [
			'lab_test' => $this->input->post('lab_test') ?? [],
			'bleed_test' => $this->input->post('bleed_test') ?? [],
			'hepatitis_test' => $this->input->post('hepatitis_test') ?? [],
			'cardiac' => $this->input->post('cardiac') ?? [],
			'blood_test' => $this->input->post('blood_test') ?? [],
			'liver_profile' => $this->input->post('liver_profile') ?? [],
			'renal_func' => $this->input->post('renal_func') ?? [],
			'serology' => $this->input->post('serology') ?? [],
			'thyroid' => $this->input->post('thyroid') ?? [],
			'x_ray' => $this->input->post('x_ray') ?? [],
			'ultrasound' => $this->input->post('ultrasound') ?? [],
		];
		$normalizedTests = [];
		foreach ($test_categories as $column => $tests) {
			$cleanTests = [];
			foreach ((array) $tests as $testName) {
				$name = trim((string) $testName);
				if ($name !== '') {
					$cleanTests[] = $name;
				}
			}
			$normalizedTests[$column] = array_values(array_unique($cleanTests));
		}

		$otherMap = [
			'lab_test' => 'lab_test_other',
			'serology' => 'serology_other',
			'thyroid' => 'thyroid_other',
			'x_ray' => 'x_ray_other',
			'ultrasound' => 'ultrasound_other',
		];
		foreach ($otherMap as $column => $inputName) {
			if (!isset($normalizedTests[$column])) {
				continue;
			}
			$otherValue = trim((string) $this->input->post($inputName));
			foreach ($normalizedTests[$column] as $idx => $item) {
				if ($item !== 'Others') {
					continue;
				}
				$normalizedTests[$column][$idx] = ($otherValue !== '') ? ('Others: ' . $otherValue) : 'Others';
			}
		}

		$hasRequestedTest = false;
		foreach ($normalizedTests as $tests) {
			if (!empty($tests)) {
				$hasRequestedTest = true;
				break;
			}
		}

		if (!$hasRequestedTest) {
			$this->session->set_flashdata('danger', 'No tests selected.');
			redirect('Page/edit_labReq/' . $labID);
			return;
		}

		$updateData = [
			// Keep patient identity immutable in edit mode.
			'FirstName' => (string) $row->FirstName,
			'MiddleName' => (string) $row->MiddleName,
			'LastName' => (string) $row->LastName,
			'patientType' => (string) $row->patientType,
			'sex' => trim((string) $this->input->post('sex')),
			'birthdate' => $birthdate,
			'age' => $ageValue,
			'address' => trim((string) $this->input->post('address')),
			'district' => trim((string) $this->input->post('district')),
			'school' => trim((string) $this->input->post('school')),
		];
		foreach ($normalizedTests as $column => $tests) {
			$updateData[$column] = implode(', ', $tests);
		}

		$this->db->where('labID', $labID);
		if ($this->db->update('med_labrequest', $updateData)) {
			$this->session->set_flashdata('success', 'Laboratory request updated successfully!');
		} else {
			$this->session->set_flashdata('danger', 'Failed to update laboratory request.');
		}

		redirect('Page/lab_request');
	}

	public function print_labReq($labID)
	{
		$labID = (int) $labID;
		$row = $this->db->get_where('med_labrequest', ['labID' => $labID])->row();
		if (!$row) {
			show_404();
			return;
		}

		$data['row'] = $row;
		$this->load->view('med_lab_print', $data);
	}

	public function lab_patient_history($labID)
	{
		$labID = (int) $labID;
		$request = $this->db->get_where('med_labrequest', ['labID' => $labID])->row();
		if (!$request) {
			show_404();
			return;
		}

		$firstName = trim((string) ($request->FirstName ?? ''));
		$middleName = trim((string) ($request->MiddleName ?? ''));
		$lastName = trim((string) ($request->LastName ?? ''));
		$patientType = trim((string) ($request->patientType ?? ''));

		$this->db->select('*');
		$this->db->from('med_patient');
		$this->db->where('FirstName', $firstName);
		$this->db->where('LastName', $lastName);
		if ($middleName !== '') {
			$this->db->where('MiddleName', $middleName);
		}
		if ($patientType !== '') {
			$this->db->where('patientType', $patientType);
		}
		$this->db->order_by('medID', 'DESC');

		$data['lab_request'] = $request;
		$data['history'] = $this->db->get()->result();
		$this->load->view('med_lab_history', $data);
	}



	function applicantList()
	{
		$result['data'] = $this->SettingsModel->applicantList();
		$this->load->view('applicantList', $result);
	}


	public function endorsedApplicantsByJob($jobID)
	{
		$data['mis_settings'] = $this->SettingsModel->mis_settings();
		$data['applicants'] = $this->SettingsModel->getEndorsedApplicantsByJob($jobID);
		$data['jobID'] = $jobID;
		$this->load->view('endorsed_applicants_view', $data);
	}


	public function ratedApplicantsByJob($jobID)
	{
		$data['mis_settings'] = $this->SettingsModel->mis_settings();
		$data['applicants'] = $this->SettingsModel->getRatedApplicantsByJob($jobID);
		$data['jobID'] = $jobID;
		$this->load->view('rated_applicants_view', $data);
	}

	public function applicantCountsByDistrict($jobID)
	{
		$jobID = (int) $jobID;

		$data['job'] = $this->Common->one_cond_row('hris_jobvacancy', 'jobID', $jobID);
		if (!$data['job']) {
			show_404();
			return;
		}

		$data['mis_settings'] = $this->SettingsModel->mis_settings();
		$data['counts'] = $this->SettingsModel->getApplicantCountsByDistrict($jobID);
		$data['jobID'] = $jobID;

		$this->load->view('applicant_counts_district_view', $data);
	}


	public function validatedApplicantsByJob($jobID)
	{
		$data['mis_settings'] = $this->SettingsModel->mis_settings();
		$data['applicants'] = $this->SettingsModel->getvalidatedApplicantsByJob($jobID);
		$data['jobID'] = $jobID;
		$this->load->view('validated_applicants_view', $data);
	}

	public function submittedApplicantsByJob($jobID)
	{
		$data['mis_settings'] = $this->SettingsModel->mis_settings();
		$data['applicants'] = $this->SettingsModel->getsubmittedApplicantsByJob($jobID);
		$data['jobID'] = $jobID;
		$this->load->view('submitted_applicants_view', $data);
	}



	function med_disease()
	{
		$result['data'] = $this->SettingsModel->med_disease();
		$this->load->view('med_disease', $result);
	}


	public function save_medDisease()
	{
		$data = [
			'category' => $this->input->post('category'),
			'disease' => $this->input->post('disease')
		];
		$this->db->insert('med_disease', $data);
		$this->session->set_flashdata('success', 'New disease added successfully!');
		$returnUrl = trim((string) $this->input->post('return_url'));
		if ($returnUrl !== '' && strpos($returnUrl, base_url()) === 0) {
			redirect($returnUrl);
			return;
		}
		redirect('Page/med_disease');
	}



	public function update_medDisease()
	{
		$dID = $this->input->post('dID');
		$data = [
			'category' => $this->input->post('category'),
			'disease' => $this->input->post('disease')
		];
		$this->db->where('dID', $dID);
		$this->db->update('med_disease', $data);
		$this->session->set_flashdata('success', 'Disease updated successfully!');
		$returnUrl = trim((string) $this->input->post('return_url'));
		if ($returnUrl !== '' && strpos($returnUrl, base_url()) === 0) {
			redirect($returnUrl);
			return;
		}
		redirect('Page/med_disease');
	}

	public function delete_medDisease($dID)
	{
		$this->db->where('dID', $dID);
		$this->db->delete('med_disease');
		$this->session->set_flashdata('danger', 'Disease deleted successfully!');
		$returnUrl = trim((string) $this->input->get('return_url', true));
		if ($returnUrl !== '' && strpos($returnUrl, base_url()) === 0) {
			redirect($returnUrl);
			return;
		}
		redirect('Page/med_disease');
	}



	function med_disposition()
	{
		$result['data'] = $this->SettingsModel->med_disposition();
		$this->load->view('med_disposition', $result);
	}


	public function save_med_disposition()
	{
		$data = [
			'disposition' => $this->input->post('disposition')
		];
		$this->db->insert('med_disposition', $data);
		$this->session->set_flashdata('success', 'New disposition added successfully!');
		redirect('Page/med_disposition');
	}



	public function update_med_disposition()
	{
		$dis_id = $this->input->post('dis_id');
		$data = [
			'disposition' => $this->input->post('disposition')
		];
		$this->db->where('dis_id', $dis_id);
		$this->db->update('med_disposition', $data);
		$this->session->set_flashdata('success', 'disposition updated successfully!');
		redirect('Page/med_disposition');
	}

	public function delete_med_disposition($dis_id)
	{
		$this->db->where('dis_id', $dis_id);
		$this->db->delete('med_disposition');
		$this->session->set_flashdata('danger', 'disposition deleted successfully!');
		redirect('Page/med_disposition');
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


			redirect('Page/ethnicity');
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
			redirect('Page/ethnicity');
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

		redirect('Page/ethnicity');
	}





	function religion()
	{
		$result['data'] = $this->SettingsModel->get_religion();
		$this->load->view('settings_religion', $result);
	}




	public function Addreligion()
	{
		if ($this->input->post('save')) {
			$data = array(
				'religion' => $this->input->post('religion')
			);
			$this->load->model('SettingsModel');
			$this->SettingsModel->insertreligion($data);


			redirect('Page/religion');
		}
		$this->load->view('settings_Addreligion');
	}


	public function updatereligion()
	{
		$id = $this->input->get('id');
		$result['data'] = $this->SettingsModel->getreligionbyId($id);
		$this->load->view('updatereligion', $result);

		if ($this->input->post('update')) {

			$religion = $this->input->post('religion');

			$this->SettingsModel->updatereligion($id, $religion);
			$this->session->set_flashdata('author', 'Record updated successfully');
			redirect('Page/religion');
		}
	}


	public function Deletereligion()
	{
		$id = $this->input->get('id');
		if ($id) {
			$this->SettingsModel->Delete_religion($id);
			$this->session->set_flashdata('religion', 'Record deleted successfully');
		} else {
			$this->session->set_flashdata('religion', 'Error deleting record');
		}

		redirect('Page/religion');
	}


	function prevschool()
	{
		$result['data'] = $this->SettingsModel->get_prevschool();
		$this->load->view('settings_prevschool', $result);
	}




	public function Addprevschool()
	{
		if ($this->input->post('save')) {
			$data = array(
				'School' => $this->input->post('School'),
				'Address' => $this->input->post('Address')
			);
			$this->load->model('SettingsModel');
			$this->SettingsModel->insertprevschool($data);


			redirect('Page/prevschool');
		}
		$this->load->view('settings_Addprevschool');
	}


	public function updateprevschool()
	{
		$schoolID = $this->input->get('schoolID');
		$result['data'] = $this->SettingsModel->getprevschoolbyId($schoolID);
		$this->load->view('updateprevschool', $result);

		if ($this->input->post('update')) {

			$School = $this->input->post('School');
			$Address = $this->input->post('Address');


			$this->SettingsModel->updateprevschool($schoolID, $School, $Address);
			$this->session->set_flashdata('author', 'Record updated successfully');
			redirect('Page/prevschool');
		}
	}


	public function Deleteprevschool()
	{
		$schoolID = $this->input->get('schoolID');
		if ($schoolID) {
			$this->SettingsModel->Delete_prevschool($schoolID);
			$this->session->set_flashdata('prevschool', 'Record deleted successfully');
		} else {
			$this->session->set_flashdata('prevschool', 'Error deleting record');
		}

		redirect('Page/prevschool');
	}



	public function program()
	{
		$result['data'] = $this->SettingsModel->courseTable();
		$result['level'] = $this->SettingsModel->get_Major();

		if ($this->input->post('save')) {
			$data = array(
				'CourseCode' => $this->input->post('CourseCode'),
				'CourseDescription' => $this->input->post('CourseDescription'),
				'Major' => $this->input->post('Major')
			);
			$this->load->model('SettingsModel');
			$this->SettingsModel->insertprogram($data);


			redirect('Page/program');
		}
		$this->load->view('program', $result);
	}





	public function updateprogram()
	{
		$courseid = $this->input->post('courseid');
		if ($this->input->post('update')) {
			$CourseCode = $this->input->post('CourseCode');
			$CourseDescription = $this->input->post('CourseDescription');
			$Major = $this->input->post('Major');

			// Update track and strand in the database
			$this->SettingsModel->update_program($courseid, $CourseCode, $CourseDescription, $Major);

			$this->session->set_flashdata('success', 'Record updated successfully');
			redirect('Page/program');
		} else {
			$result['data'] = $this->SettingsModel->get_programbyId($courseid);
			$this->load->view('program', $result);
		}
	}



	public function DeleteProgram()
	{
		$courseid = $this->input->get('courseid');
		if ($courseid) {
			$this->SettingsModel->Delete_program($courseid);
			$this->session->set_flashdata('program', 'Record deleted successfully');
		} else {
			$this->session->set_flashdata('program', 'Error deleting record');
		}

		redirect('Page/program');
	}


	// Fetch sections based on YearLevel (for dependent dropdown)
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
	public function fetch_adviser()
	{
		if ($this->input->post('yearlevel') && $this->input->post('section')) {
			$yearlevel = $this->input->post('yearlevel');
			$section = $this->input->post('section');

			$adviser = $this->StudentModel->getAdviserBySection($yearlevel, $section);

			if ($adviser) {
				echo json_encode([
					'IDNumber' => $adviser->IDNumber,
					'adviserName' => $adviser->adviserName
				]);
			} else {
				echo json_encode([
					'IDNumber' => '',
					'adviserName' => 'No adviser assigned.'
				]);
			}
		}
	}


	public function fetch_adviser_id()
	{
		$section = $this->input->post('section');
		$yearLevel = $this->input->post('yearlevel');
		$schoolID = $this->session->userdata('school_id');
		$schoolYear = $this->session->userdata('sy');

		if ($section && $yearLevel && $schoolYear && $schoolID) {
			$adviser = $this->StudentModel->getAdviserByCriteria($section, $yearLevel, $schoolYear, $schoolID);
			if (!empty($adviser)) {
				echo htmlspecialchars($adviser->IDNumber, ENT_QUOTES);
			} else {
				echo '';
			}
		}
	}


	public function fetch_adviser_name()
	{
		$section = $this->input->post('section');
		$yearLevel = $this->input->post('yearlevel');
		$schoolID = $this->session->userdata('school_id');
		$schoolYear = $this->session->userdata('sy');

		if ($section && $yearLevel && $schoolYear && $schoolID) {
			$adviser = $this->StudentModel->getAdviserByCriteria($section, $yearLevel, $schoolYear, $schoolID);
			if (!empty($adviser)) {
				$staff = $this->StudentModel->getStaffByID($adviser->IDNumber);
				if (!empty($staff)) {
					$fullName = $staff->FirstName . ' ' . $staff->MiddleName . ' ' . $staff->LastName;
					echo htmlspecialchars($fullName, ENT_QUOTES);
				} else {
					echo 'Adviser not found';
				}
			} else {
				echo 'No adviser';
			}
		}
	}


	public function somePage()
	{
		$username = $this->session->userdata('username');
		$data['is_endorser'] = $this->PersonnelModel->is_endorser($username);

		// load your sidebar/main view and pass $data
		$this->load->view('header', $data);
	}

	public function allocation_delete()
	{
		$this->Common->delete('sgod_school_allocation', 'id', '3');
		$this->session->set_flashdata('danger', 'Successfully deleted.');
		redirect(base_url() . 'Page/school_allocations');
	}

	public function insert_trainings()
	{
		$config['allowed_types'] = 'pdf';
		$config['upload_path'] = './uploads/trainings';
		$new_name = $this->input->post('id_number') . '-' . time() . $_FILES["file"]['name'];
		$config['file_name'] = $new_name;
		$this->load->library('upload', $config);

		if ($this->upload->do_upload('file')) {
			$this->Reg->insert_trainings();
			$this->session->set_flashdata('success', 'Successfully updated.');
			redirect($_SERVER['HTTP_REFERER'] . '#trainings');
		} else {
			$this->session->set_flashdata('danger', $this->upload->display_errors());
			redirect($_SERVER['HTTP_REFERER'] . '#trainings');
		}
	}

	public function update_trainings()
	{
		$this->Reg->update_trainings();
		$this->session->set_flashdata('success', 'Successfully updated');
		redirect($_SERVER['HTTP_REFERER'] . '#trainings');
	}

	public function update_trainings_staff()
	{
		$this->Reg->update_training_staff();
		$this->session->set_flashdata('success', 'Successfully updated');
		redirect($_SERVER['HTTP_REFERER'] . '#trainings');
	}

	public function training_delete()
	{
		$attach = $this->Common->one_cond_row('hris_training', 'id', $this->uri->segment(3));
		$this->Common->delete_with_attachv2('hris_training', $attach->id, 'uploads/trainings', $attach->file);
		$this->session->set_flashdata('danger', 'Successfully deleted');
		redirect($_SERVER['HTTP_REFERER'] . '#trainings');
	}

	public function training_delete_staff()
	{
		$attach = $this->Common->one_cond_row('hris_trainings', 'trainingID', $this->uri->segment(3));
		$this->Page_model->delete_the_toxic('hris_trainings', 'trainingID', $attach->trainingID, 'uploads/trainings_staff', $attach->file);
		$this->session->set_flashdata('danger', 'Successfully deleted');
		redirect($_SERVER['HTTP_REFERER'] . '#trainings');
	}

	public function insert_experience()
	{
		$config['allowed_types'] = 'pdf';
		$config['upload_path'] = './uploads/experience';
		$new_name = $this->input->post('id_number') . '-' . time() . $_FILES["file"]['name'];
		$config['file_name'] = $new_name;
		$this->load->library('upload', $config);

		if ($this->upload->do_upload('file')) {
			$this->Reg->insert_experience();
			$this->session->set_flashdata('success', 'Successfully updated.');
			redirect($_SERVER['HTTP_REFERER'] . '#work');
		} else {
			$this->session->set_flashdata('danger', $this->upload->display_errors());
			redirect($_SERVER['HTTP_REFERER'] . '#work');
		}
	}

	public function update_experience()
	{
		$this->Reg->update_experience('nm');
		$this->session->set_flashdata('success', 'Successfully updated');
		redirect($_SERVER['HTTP_REFERER'] . '#work');
	}

	public function update_year_experience()
	{
		$this->Reg->update_experience('ny');
		$this->session->set_flashdata('success', 'Successfully updated');
		redirect($_SERVER['HTTP_REFERER'] . '#work');
	}

	public function experience_delete()
	{
		$attach = $this->Common->one_cond_row('hris_experience', 'id', $this->uri->segment(3));
		$this->Common->delete_with_attachv2('hris_experience', $attach->id, 'uploads/experience', $attach->file);
		$this->session->set_flashdata('danger', 'Successfully deleted');
		redirect($_SERVER['HTTP_REFERER'] . '#work');
	}

	public function update_cert_xp_stat()
	{
		$this->Reg->update_cert_stat('hris_experience');
		$this->session->set_flashdata('success', 'Successfully updated');
		redirect($_SERVER['HTTP_REFERER'] . '#work');
	}

	public function update_cert_training_stat()
	{
		$this->Reg->update_cert_stat('hris_training');
		$this->session->set_flashdata('success', 'Successfully updated');
		redirect($_SERVER['HTTP_REFERER'] . '#trainings');
	}

	public function update_cert_training_stat_staff()
	{
		$this->Reg->update_cert_stat_staff('hris_trainings');
		$this->session->set_flashdata('success', 'Successfully updated');
		redirect($_SERVER['HTTP_REFERER'] . '#trainings');
	}
}
