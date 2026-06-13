<?php
class PersonnelModel extends CI_Model
{

	//Personnel by Department Counts
	public function perPlantillaGroup()
	{
		$query = $this->db->query("SELECT department, count(department) as Counts FROM hris_staff group by department order by department");
		return $query->result();
	}

	//my applications
	function myApplications($applicantEmail)
	{
		$query = $this->db->query("SELECT * FROM hris_applications ha join hris_jobvacancy jv on ha.jobID=jv.jobID where empEmail='" . $applicantEmail . "'");
		return $query->result();
	}

	//my rating
	function ratings($appID)
	{
		$query = $this->db->query("SELECT * FROM hris_applications_rating where appID='" . $appID . "'");
		return $query->result();
	}

	function appTrack($empEmail, $jobID)
	{
		$query = $this->db->query("SELECT * FROM hris_applications_track where empEmail='" . $empEmail . "' and jobID='" . $jobID . "' order by trackID desc");
		return $query->result();
	}

	//Personnel by Department Counts
	public function departmentcounts()
	{
		$query = $this->db->query("SELECT department, count(department) as Counts FROM hris_staff group by department order by department");
		return $query->result();
	}

    public function announcements()
    {
        $query = $this->db->query("select * from announcements order by id");
        return $query->result();
    }
    public function announcements_post()
    {
        $query = $this->db->query("select * from announcements where a_stat='1' order by id");
        return $query->result();
    }
	//my applications
	function viewApplicants($jobID){
		$query = $this->db->query("SELECT * FROM hris_applicant h join hris_applications a on h.empEmail=a.empEmail where jobID='" . $jobID . "'");
		return $query->result();
	}

	function appPerMun($jobID, $mun)
	{
		$query = $this->db->query("SELECT * FROM hris_applicant h join hris_applications a on h.empEmail=a.empEmail where jobID='" . $jobID . "' and perCity='" . $mun . "'");
		return $query->result();
	}

	function appCountsPerCity($jobID)
	{
		$query = $this->db->query("SELECT jobID, perCity, count(perCity) as cityCounts FROM hris_applicant h join hris_applications a on h.empEmail=a.empEmail where jobID='" . $jobID . "' group by perCity, jobID");
		return $query->result();
	}


	//display posted jobs
	function JobVancancies()
	{
		$query = $this->db->query("select * from hris_jobvacancy where jvStatus='Open' order by jobID desc");
		return $query->result();
	}

	function JobVancancies_promotion()
	{
		$query = $this->db->query("select * from hris_jobvacancy where jvStatus='Open' and promotion='1' order by jobID desc");
		return $query->result();
	}

	function JobVancancies_shs()
	{
		$query = $this->db->query("select * from hris_jobvacancy where jvStatus='Open' and jobTitle='Teacher I - Senior High School' order by jobID desc");
		return $query->result();
	}

	function jobArchieved()
	{
		$query = $this->db->query("select * from hris_jobvacancy where jvStatus='Closed' order by jobID desc");
		return $query->result();
	}

	public function searchPersonnelSchool($schoolID)
	{
		$this->db->select('IDNumber, FirstName, MiddleName, LastName');
		$this->db->from('hris_staff');
		$this->db->where('schoolID', $schoolID);
		$this->db->order_by('LastName');
		$query = $this->db->get();
		return $query->result();
	}

	public function systemFeedbackResults()
	{
		$this->db->select('*');
		$this->db->from('system_feedback');
		$query = $this->db->get();
		return $query->result();
	}

	public function systemFeedbackResults_Messages()
	{
		$this->db->select('*');
		$this->db->from('system_feedback');
		$this->db->where('q5Ans !=', '');
		$query = $this->db->get();
		return $query->result();
	}

	function mandatoryDed($IDNumber)
	{
		$query = $this->db->query("SELECT dedGSIS,dedPHealth,dedTax,dedPagibig, sum(dedGSIS+dedPHealth+dedTax+dedPagibig) as mandatoryDed FROM payroll_masterlist where IDNumber='" . $IDNumber . "'");
		return $query->result();
	}

	public function searchPersonnel()
	{
		$this->db->select('IDNumber, FirstName, MiddleName, LastName');
		$this->db->from('hris_staff');
		$this->db->order_by('LastName');
		$query = $this->db->get();
		return $query->result();
	}

	//Personnel List by Department
	public function employeelistDepartment($department)
	{
		$this->db->select('*');
		$this->db->from('hris_staff');
		$this->db->where('Department', $department);
		$this->db->order_by('LastName');
		$query = $this->db->get();
		return $query->result();
	}

	public function mis_settings()
	{
		$this->db->select('*');
		$this->db->from('mis_settings');
		$query = $this->db->get();
		return $query->result();
	}

	// Get Department
	public function getDepartment()
	{
		$this->db->select('Department');
		$this->db->from('hris_staff');
		$this->db->group_by('Department');
		$this->db->order_by('Department');
		$query = $this->db->get();
		return $query->result();
	}

	//Service Record
	// public function service_Record()
	// {
	// 	$this->db->select('*');
	// 	$this->db->from('hris_staff as s');
	// 	$this->db->join('hris_employment as l', 's.IDNumber = l.IDNumber');
	// 	$this->db->order_by('LastName');
	// 	$query = $this->db->get();
	// 	return $query->result();
	// }


	public function service_Record($department = null)
	{
		$this->db->select('*');
		$this->db->from('hris_staff as s');
		$this->db->join('hris_employment as l', 's.IDNumber = l.IDNumber');
	
		// Apply department filter if provided
		if ($department) {
			$this->db->where('s.Department', $department);
		}
	
		$this->db->order_by('LastName');
		$this->db->group_by('s.IDNumber'); 
		$query = $this->db->get();
		return $query->result();
	}
	


	//Service Record
	public function service_Record_school($schoolID)
	{
		$this->db->select('*');
		// $this->db->select('s.IDNumber, FirstName, MiddleName, LastName, empPosition');
		$this->db->from('hris_staff as s');
		$this->db->join('hris_employment as l', 's.IDNumber = l.IDNumber');
		$this->db->where('schoolID', $schoolID);
		$this->db->order_by('LastName');
		$this->db->group_by('s.IDNumber'); 
		$query = $this->db->get();
		return $query->result();
	}





	public function service_Record_view($IDNumber)
{
    $this->db->select('*');
    $this->db->from('hris_staff as s');
    $this->db->join('hris_employment as l', 's.IDNumber = l.IDNumber');
    $this->db->where('s.IDNumber', $IDNumber); // Specify the table name to avoid ambiguity
    $query = $this->db->get();
    return $query->result();
}











	//Service Record Request List
	public function sr_request_list()
	{
		$this->db->select('*');
		$this->db->from('doc_request as d');
		$this->db->join('hris_staff as s', 'd.IDNumber = s.IDNumber');
		$this->db->where('reqStat', "Pending");
		$this->db->order_by('id', 'desc');
		$query = $this->db->get();
		return $query->result();
	}

	public function sr_request_list_emp($IDNumber)
	{
		$this->db->select('*');
		$this->db->from('doc_request as d');
		$this->db->join('hris_staff as s', 'd.IDNumber = s.IDNumber');
		$this->db->where('d.IDNumber', $IDNumber);
		$this->db->order_by('id', 'desc');
		$query = $this->db->get();
		return $query->result();
	}




	// Get Department
	public function getPurpose()
	{
		$this->db->select('DISTINCT(purpose) as purpose'); // Fetch distinct purposes
		$this->db->from('doc_request');
		$this->db->order_by('purpose');
		$query = $this->db->get();
		return $query->result();
	}
	









	public function trainingNeeds($user_id)
	{
		$this->db->select('*');
		$this->db->from('hris_training_needs');
		$this->db->where('IDNumber', $user_id);
		// $this->db->order_by('LastName');
		$query = $this->db->get();
		return $query->result();
	}

	public function tnCategory()
	{
		$this->db->select('*');
		$this->db->from('hris_training_needs_cat');
		$this->db->order_by('category');
		$query = $this->db->get();
		return $query->result();
	}

	public function individualDevelopment($user_id)
	{
		$this->db->select('*');
		$this->db->from('hris_ind_devt');
		$this->db->where('IDNumber', $user_id);
		// $this->db->order_by('LastName');
		$query = $this->db->get();
		return $query->result();
	}

	public function empServiceRecord($user_id)
	{
		$this->db->select('s.*, e.*, dr.*');
		$this->db->from('hris_staff as s');
		$this->db->join('hris_employment as e', 's.IDNumber = e.IDNumber');
		$this->db->join('doc_request as dr', 's.IDNumber = dr.IDNumber', 'left');
		$this->db->where('s.IDNumber', $user_id);
		$this->db->order_by('e.salary', 'ASC');
		$this->db->group_by(['e.appointDate']); // Groups by appointDate and endDate
		
		$query = $this->db->get();
		return $query->result();
	}
	

public function empServicereq($user_id)
{
    $this->db->select('*');
    $this->db->from('doc_request');
    $this->db->where('IDNumber', $user_id);
    $query = $this->db->get();
    return $query->result();
}



// 	public function empServiceRecord1($IDNumber)
// {
//     $this->db->select('*');
//     $this->db->from('hris_staff as s');
//     $this->db->join('hris_employment as e', 's.IDNumber = e.IDNumber', 'left');
//     $this->db->where('s.IDNumber', $IDNumber);
//     $query = $this->db->get();

//     // Log if no data is found
//     if ($query->num_rows() == 0) {
//         log_message('error', "No service record found for IDNumber: $IDNumber");
//     }

//     return $query->result(); // Return results
// }

public function empServiceRecord1($IDNumber, $id)
{
    $this->db->select('s.*, e.*, dr.id as id'); // Select fields
    $this->db->from('hris_staff as s');
    $this->db->join('hris_employment as e', 's.IDNumber = e.IDNumber', 'left');
    $this->db->join('doc_request as dr', 's.IDNumber = dr.IDNumber', 'left');
    $this->db->where('s.IDNumber', $IDNumber);

    // Additional condition for doc_request ID
    if ($id) {
        $this->db->where('dr.id', $id);
    }

    $query = $this->db->get();

    if ($query->num_rows() == 0) {
        log_message('error', "No service record found for IDNumber: $IDNumber and docRequestID: $id");
    }

    return $query->result();
}



	
	public function service_Record_id($id)
	{
		$this->db->select('*');
		$this->db->from('hris_employment');
		$this->db->where('empID', $id);
		$query = $this->db->get();
		return $query->result();
	}

	public function school_dashboard($id)
	{
		$this->db->select('*');
		$this->db->from('schools');
		$this->db->where('schoolID', $id);
		$query = $this->db->get();
		return $query->result();
	}

	public function idHR( $user_id)
	{
		$this->db->select('*');
		$this->db->from('hris_ind_devt');
		$this->db->where('IDNumber', $user_id);
		// $this->db->order_by('LastName');
		$query = $this->db->get();
		return $query->result();
	}

	//Personnel Counts
	public function personnelCounts()
	{
		$query = $this->db->query("SELECT empPosition, count(empPosition) as Counts FROM hris_staff group by empPosition");
		return $query->result();
	}

	//Personnel List by Position
	public function employeelistPosition($position)
	{
		$this->db->select('*');
		$this->db->from('hris_staff');
		$this->db->where('empPosition', $position);
		$query = $this->db->get();
		return $query->result();
	}

	//Personnel Per Salary Grade
	public function sg_summary()
	{
		$query = $this->db->query("SELECT sgNo, count(sgNo) as Counts FROM hris_staff group by sgNo");
		return $query->result();
	}

	//Personnel List by SG
	public function employeelistSG($sg)
	{
		$this->db->select('LastName,FirstName,MiddleName, IDNumber, Department, empPosition');
		$this->db->from('hris_staff');
		$this->db->where('sgNo', $sg);
		$query = $this->db->get();
		return $query->result();
	}

	//Personnel List v1
	public function employeelistv1()
	{
		$this->db->select('IDNumber, FirstName, MiddleName, LastName, NameExtn, empPosition, Department, gsis, pagibig, philHealth, tinNo');
		$this->db->from('hris_staff');
		$this->db->where('currentStatus', "Active");
		$query = $this->db->get();
		return $query->result();
	}

	//Personnel List v2
	public function employeelistv2()
	{
		$this->db->select('IDNumber, schoolID, FirstName, MiddleName, LastName, NameExtn, empPosition, Department, origAppointmentDate, lastAppointmentDate, retirement, retYear');
		$this->db->from('hris_staff');
		$this->db->where('currentStatus', "Active");
		if($this->session->position == 'School'){
			$this->db->where('schoolID', $this->session->username);
		}
		$query = $this->db->get();
		return $query->result();
	}

	//Personnel List v3 (employee with 60-65 years old)
	public function employeelistv3()
	{
		$this->db->select('IDNumber, FirstName, MiddleName, LastName, NameExtn, empPosition, Department, BirthDate, age, origAppointmentDate, lastAppointmentDate, retirement, retYear','schoolID');
		$this->db->from('hris_staff');
		$this->db->where('age >', "59");
		if($this->session->position == 'School'){
		$this->db->where('schoolID', $this->session->username);
		}
		$this->db->where('currentStatus', "Active");
		$query = $this->db->get();
		return $query->result();
	}

	public function for_retirement_school($schoolID)
	{
		$this->db->select('IDNumber, FirstName, MiddleName, LastName, NameExtn, empPosition, Department, BirthDate, age, origAppointmentDate, lastAppointmentDate, retirement, retYear');
		$this->db->from('hris_staff');
		$this->db->where('age >', "59");
		$this->db->where('schoolID', $schoolID);
		$this->db->where('currentStatus', "Active");
		$query = $this->db->get();
		return $query->result();
	}

	//For Step Increment
	public function step_increment_list($id)
	{
		$this->db->select('IDNumber, FirstName, MiddleName, LastName, NameExtn, empPosition, Department, BirthDate, age, origAppointmentDate, lastAppointmentDate, retirement');
		$this->db->from('hris_staff');
		$this->db->where('sl_after_lastappointment =', $id);
		$this->db->where('currentStatus', "Active");
		$query = $this->db->get();
		return $query->result();
	}

	public function step_increment_list_school($id, $schoolID)
	{
		$this->db->select('IDNumber, FirstName, MiddleName, LastName, NameExtn, empPosition, Department, BirthDate, age, origAppointmentDate, lastAppointmentDate, retirement');
		$this->db->from('hris_staff');
		$this->db->where('sl_after_lastappointment =', $id);
		$this->db->where('schoolID =', $schoolID);
		$this->db->where('currentStatus', "Active");
		$query = $this->db->get();
		return $query->result();
	}

	public function step_increment()
	{
		$query = $this->db->query("SELECT count(sl_after_lastappointment) as counts FROM hris_staff WHERE sl_after_lastappointment='3' and currentStatus='Active'");
		return $query->result();
	}

	public function step_increment1()
	{
		$query = $this->db->query("SELECT count(sl_after_lastappointment) as counts FROM hris_staff WHERE sl_after_lastappointment='6' and currentStatus='Active'");
		return $query->result();
	}

	public function step_increment2()
	{
		$query = $this->db->query("SELECT count(sl_after_lastappointment) as counts FROM hris_staff WHERE sl_after_lastappointment='9' and currentStatus='Active'");
		return $query->result();
	}

	public function step_increment3()
	{
		$query = $this->db->query("SELECT count(sl_after_lastappointment) as counts FROM hris_staff WHERE sl_after_lastappointment='12' and currentStatus='Active'");
		return $query->result();
	}

	public function step_increment4()
	{
		$query = $this->db->query("SELECT count(sl_after_lastappointment) as counts FROM hris_staff WHERE sl_after_lastappointment='15' and currentStatus='Active'");
		return $query->result();
	}

	public function step_increment5()
	{
		$query = $this->db->query("SELECT count(sl_after_lastappointment) as counts FROM hris_staff WHERE sl_after_lastappointment='18' and currentStatus='Active'");
		return $query->result();
	}

	public function step_increment6()
	{
		$query = $this->db->query("SELECT count(sl_after_lastappointment) as counts FROM hris_staff WHERE sl_after_lastappointment='21' and currentStatus='Active'");
		return $query->result();
	}

	public function step_increment7()
	{
		$query = $this->db->query("SELECT count(sl_after_lastappointment) as counts FROM hris_staff WHERE sl_after_lastappointment='24' and currentStatus='Active'");
		return $query->result();
	}

	public function step_increment_sc($schoolID)
	{
		$query = $this->db->query("SELECT count(sl_after_lastappointment) as counts FROM hris_staff WHERE sl_after_lastappointment='3' and schoolID='".$schoolID."' and currentStatus='Active'");
		return $query->result();
	}

	public function step_increment1_sc($schoolID)
	{
		$query = $this->db->query("SELECT count(sl_after_lastappointment) as counts FROM hris_staff WHERE sl_after_lastappointment='6' and schoolID='".$schoolID."' and currentStatus='Active'");
		return $query->result();
	}

	public function step_increment2_sc($schoolID)
	{
		$query = $this->db->query("SELECT count(sl_after_lastappointment) as counts FROM hris_staff WHERE sl_after_lastappointment='9' and schoolID='".$schoolID."' and currentStatus='Active'");
		return $query->result();
	}

	public function step_increment3_sc($schoolID)
	{
		$query = $this->db->query("SELECT count(sl_after_lastappointment) as counts FROM hris_staff WHERE sl_after_lastappointment='12' and schoolID='".$schoolID."' and currentStatus='Active'");
		return $query->result();
	}

	public function step_increment4_sc($schoolID)
	{
		$query = $this->db->query("SELECT count(sl_after_lastappointment) as counts FROM hris_staff WHERE sl_after_lastappointment='15' and schoolID='".$schoolID."' and currentStatus='Active'");
		return $query->result();
	}

	public function step_increment5_sc($schoolID)
	{
		$query = $this->db->query("SELECT count(sl_after_lastappointment) as counts FROM hris_staff WHERE sl_after_lastappointment='18' and schoolID='".$schoolID."' and currentStatus='Active'");
		return $query->result();
	}

	public function step_increment6_sc($schoolID)
	{
		$query = $this->db->query("SELECT count(sl_after_lastappointment) as counts FROM hris_staff WHERE sl_after_lastappointment='21' and schoolID='".$schoolID."' and currentStatus='Active'");
		return $query->result();
	}

	public function step_increment7_sc($schoolID)
	{
		$query = $this->db->query("SELECT count(sl_after_lastappointment) as counts FROM hris_staff WHERE sl_after_lastappointment='24' and schoolID='".$schoolID."' and currentStatus='Active'");
		return $query->result();
	}
	public function regApplicants()
	{
		$this->db->select('*');
		$this->db->from('hris_registration');
		$query = $this->db->get();
		return $query->result();
	}

	public function employeelist($department = null)
	{
		$this->db->select('*');
		$this->db->from('hris_staff');
		
		// Filter by department if a department is selected
		if ($department) {
			$this->db->where('Department', $department);
		}
	
		$this->db->order_by("LastName");
		$query = $this->db->get();
		return $query->result();
	}
	

	public function emp_list_dept($schoolID)
	{
		$this->db->select('*');
		$this->db->from('hris_staff');
		$this->db->order_by("LastName");
		$this->db->where('schoolID', $schoolID);
		$query = $this->db->get();
		return $query->result();
	}



	public function school_allocations()
	{
		$this->db->select('*');
		$this->db->from('sgod_school_allocation');
		$query = $this->db->get();
		return $query->result();
	}

	public function school_allocations2($schoolID)
	{
		$this->db->select('*');
		$this->db->from('sgod_school_allocation');
		$this->db->where('schoolID', $schoolID);
		$query = $this->db->get();
		return $query->result();
	}

	
	public function view_sip($schoolID)
	{
		$this->db->select('*');
		$this->db->from('school_imp_plans');
		// $this->db->where('subType', "AIP");
		$this->db->where('schoolID', $schoolID);
		// $this->db->order_by("id");
		$query = $this->db->get();
		return $query->result();
	}

	//Personnel List v4 - personnel with corresponding number of years in service
	public function employeelistv4()
	{
		$this->db->select('IDNumber, FirstName, MiddleName, LastName, NameExtn, empPosition, Department, dateHired, CURDATE(), TIMESTAMPDIFF(YEAR,dateHired,CURDATE()) AS serviceLenght');
		$this->db->from('hris_staff');
		$this->db->where('currentStatus', "Active");
		$query = $this->db->get();
		return $query->result();
	}

	public function service_lenght($schoolID)
	{
		$this->db->select('IDNumber, FirstName, MiddleName, LastName, NameExtn, empPosition, Department, dateHired, CURDATE(), TIMESTAMPDIFF(YEAR,dateHired,CURDATE()) AS serviceLenght');
		$this->db->from('hris_staff');
		$this->db->where('currentStatus', "Active");
		$this->db->where('schoolID', $schoolID);
		$query = $this->db->get();
		return $query->result();
	}

	//Personnel List v5 - Employment Status
	public function employeelistv5($stat)
	{
		$this->db->select('IDNumber, FirstName, MiddleName, LastName, NameExtn, empPosition, Department, origAppointmentDate');
		$this->db->from('hris_staff');
		$this->db->where('currentStatus', $stat);
		$this->db->order_by("LastName");
		$query = $this->db->get();
		return $query->result();
	}

	//Personnel List
	public function listPerPositionDept($position, $group)
	{
		$this->db->select('*');
		$this->db->from('hris_staff');
		$this->db->where('currentStatus', "Active");
		$this->db->where('empPosition', $position);
		$this->db->where('payGroup', $group);
		$this->db->order_by("LastName");
		$query = $this->db->get();
		return $query->result();
	}


	//For Retirement List
	public function retirement($year)
	{
		$this->db->select('IDNumber, FirstName, MiddleName, LastName, NameExtn, empPosition, Department, origAppointmentDate, retYear, retirement, CURDATE(), TIMESTAMPDIFF(YEAR,origAppointmentDate,CURDATE()) AS serviceLenght');
		$this->db->from('hris_staff');
		$this->db->where('retYear', $year);
		$this->db->order_by("LastName");
		$query = $this->db->get();
		return $query->result();
	}

	//Leave Credits
	public function leaveCredits($currentMonth, $currentYear)
{
    $this->db->select('s.IDNumber, FirstName, MiddleName, LastName, empPosition, vlCredit, slCredit, vlMonth, vlYear');
    $this->db->from('hris_staff as s');
    $this->db->join('hris_leave_credits as l', 's.IDNumber = l.IDNumber');
    
  
    $this->db->where('leaveCredits', "Yes");
	$this->db->where('currentStatus', "Active");
    $this->db->where('vlMonth', $currentMonth);  
    $this->db->where('vlYear', $currentYear);   
 
    $this->db->order_by("LastName");
    
     $query = $this->db->get();
    return $query->result();
}


	public function leaveCreditsMonthly($month, $year)
	{
		$this->db->select('s.IDNumber, FirstName, MiddleName, LastName, empPosition, vlCredit, slCredit,  vlMonth, vlYear');
		$this->db->from('hris_staff as s');
		$this->db->join('hris_leave_credits as l', 's.IDNumber = l.IDNumber');
		$this->db->where('leaveCredits', "Yes");
		$this->db->where('vlMonth', $month);
		$this->db->where('vlYear', $year);
		$this->db->order_by("LastName");
		$query = $this->db->get();
		return $query->result();
	}



	public function leaveCreditsSummary()
{
    $this->db->select('s.IDNumber, s.FirstName, s.MiddleName, s.LastName, s.empPosition, l.vlTotal, l.slTotal, l.cocTotal, l.ID');
    $this->db->from('hris_staff as s');
    $this->db->join('hris_leaverecords as l', 's.IDNumber = l.IDNumber', 'inner'); 
    $this->db->order_by('s.LastName', 'ASC');
    
    $query = $this->db->get();
    return $query->result();
}


	public function leaveCreditsSummarySchool($schoolID)
	{
		$this->db->select('s.IDNumber, FirstName, MiddleName, LastName, empPosition, vlTotal, slTotal, cocTotal, ID');
		$this->db->from('hris_staff as s');
		$this->db->join('hris_leaverecords as l', 's.IDNumber = l.IDNumber');
		$this->db->where("schoolID", $schoolID);
		$this->db->order_by("LastName");
		$query = $this->db->get();
		return $query->result();
	}


	public function getSuperiorById($supID)
	{
		return $this->db->get_where('epmloyee_superior', ['supID' => $supID])->row();
	}
	


	public function leaveSetting($IDNumber)
	{
		$this->db->select('*');
		$this->db->from('epmloyee_superior');
		$this->db->where('IDNumber', $IDNumber);
		$query = $this->db->get();
		return $query->result();
	}

	//Leave Applications
	public function leaveApplications($IDNumber)
	{
		$this->db->select('s.IDNumber, FirstName, MiddleName, LastName, empPosition, appDate, leaveType, spentPlace, abroad, daysApplied, dateFrom, dateTo, commutation, sickLeave, leaveStatus, leaveStatReasons, withPay, withoutPay, vlTotal, slTotal, cocTotal, leaveTotal, dateApproved, leaveAttachment, leaveID');
		$this->db->from('hris_staff as s');
		$this->db->join('hris_leave as l', 's.IDNumber = l.IDNumber');
		$this->db->order_by("LastName");
		$this->db->where('l.IDNumber', $IDNumber);
		$query = $this->db->get();
		return $query->result();
	}

public function getEvaluatorInfo($leaveID)
{
    $this->db->select('s.FirstName, s.MiddleName, s.LastName, s.empPosition');
    $this->db->from('hris_leave_tracking as t');
    $this->db->join('hris_staff as s', 's.IDNumber = t.responsibleUser'); // join on responsibleUser
    $this->db->where('t.leaveID', $leaveID);
    $this->db->where('t.actionTaken', 'Evaluated');
    // $this->db->order_by('t.timestamp', 'DESC');
    $this->db->limit(1);
    $query = $this->db->get();
    return $query->row();
}

public function getEndorserInfo($leaveID)
{
    $this->db->select('s.FirstName, s.MiddleName, s.LastName, s.empPosition');
    $this->db->from('hris_leave_tracking as t');
    $this->db->join('hris_staff as s', 's.IDNumber = t.responsibleUser'); // join on responsibleUser
    $this->db->where('t.leaveID', $leaveID);
    $this->db->where('t.actionTaken', 'Recommended');
    // $this->db->order_by('t.timestamp', 'DESC');
    $this->db->limit(1);
    $query = $this->db->get();
    return $query->row();
}

public function getapproveInfo($leaveID)
{
    $this->db->select('s.fname, s.mname, s.lname, s.position');
    $this->db->from('hris_leave_tracking as t');
    $this->db->join('users as s', 's.username = t.responsibleUser'); // join on responsibleUser
    $this->db->where('t.leaveID', $leaveID);
    $this->db->where('t.actionTaken', 'Approved');
    // $this->db->order_by('t.timestamp', 'DESC');
    $this->db->limit(1);
    $query = $this->db->get();
    return $query->row();
}



	public function leaveApplicationsPrint($id)
	{
		$this->db->select('s.IDNumber, FirstName, MiddleName, LastName, empPosition, Department, monthlySalary, directHead, directHeadPosition, appDate, leaveType, spentPlace, abroad, daysApplied, dateFrom, dateTo, commutation, sickLeave, leaveStatus, leaveStatReasons, withPay, withoutPay, vlTotal, slTotal, cocTotal, leaveTotal, dateApproved, leaveID');
		$this->db->from('hris_staff as s');
		$this->db->join('hris_leave as l', 's.IDNumber = l.IDNumber');
		$this->db->order_by("LastName");
		$this->db->where('leaveID', $id);
		$query = $this->db->get();
		return $query->result();
	}

	public function leaveSettings()
	{
		$this->db->select('*');
		$this->db->from('settings_leave');
		$query = $this->db->get();
		return $query->result();
	}


public function leave_coc($IDNumber)
{
    $this->db->select('leave_coc.*, hris_staff.*');
    $this->db->from('leave_coc');
    $this->db->join('hris_staff', 'hris_staff.IDNumber = leave_coc.IDNumber');
    $this->db->where('leave_coc.IDNumber', $IDNumber); // Filter by matching IDNumber from session username
    $query = $this->db->get();
    return $query->result();
}


	public function delete_leave_coc($cocID) {
        $this->db->where('cocID', $cocID);
        return $this->db->delete('leave_coc'); // Delete record from leave_coc table
    }




	public function get_leave_coc_by_id($cocID)
    {
        return $this->db->get_where('leave_coc', ['cocID' => $cocID])->row();
    }

    // Fetch hris_leaverecord by IDNumber
    public function get_hris_leaverecord_by_id($IDNumber)
    {
        return $this->db->get_where('hris_leaverecords', ['IDNumber' => $IDNumber])->row();
    }

    // Update leave_coc status
    public function update_leave_coc($cocID, $data)
    {
        $this->db->where('cocID', $cocID);
        return $this->db->update('leave_coc', $data);
    }

    // Update hris_leaverecords cocTotal
    public function update_hris_leaverecords($IDNumber, $newCocTotal)
    {
        $this->db->where('IDNumber', $IDNumber);
        return $this->db->update('hris_leaverecords', ['cocTotal' => $newCocTotal]);
    }

	
	



	

	public function hris_staff()
	{
		$this->db->select('*');
		$this->db->from('hris_staff');
		$query = $this->db->get();
		return $query->result();
	}


	public function hris_users()
	{
		$this->db->select('fname, mname, lname, username,');
		$this->db->from('users');
		$query = $this->db->get();
		return $query->result();
	}

	

	//Leave Credits
	public function leaveCreditsEmp($IDNumber)
	{
		$this->db->select('*');
		$this->db->from('hris_leaverecords');
		$this->db->where('IDNumber', $IDNumber);
		$query = $this->db->get();
		return $query->result();
	}


	//Unfilled Items
	function unfilledItems()
	{
		$query = $this->db->query("SELECT id, itemNo, itemPosition, sg, step, pGroup FROM hris_plantilla WHERE itemNo NOT IN (SELECT itemNo FROM hris_staff)");
		return $query->result();
	}

	public function getUnusedItemNos() {
		$this->db->select('id, pGroup, itemNo, itemPosition, sg, authAnnualSalary, step, status, view_status');
		$this->db->from('hris_plantilla');
		$this->db->where("TRIM(LOWER(itemNo)) NOT IN (
			SELECT TRIM(LOWER(itemNo)) FROM hris_staff WHERE itemNo IS NOT NULL
		)", NULL, FALSE);
		$query = $this->db->get();
		return $query->result();
	}

	//Plantilla Positions
	public function plantillaPositions()
	{
		$this->db->select('*');
		$this->db->from('hris_plantilla');
		// $this->db->order_by("LastName");
		$query = $this->db->get();
		return $query->result();
	}

	//Counts of Elementary
	function elemCounts()
	{
		$query = $this->db->query("SELECT payGroup, count(payGroup) as counts FROM hris_staff where payGroup='Elementary'");
		return $query->result();
	}
	//Counts of Secondary
	function secCounts()
	{
		$query = $this->db->query("SELECT payGroup, count(payGroup) as counts FROM hris_staff where payGroup='Secondary'");
		return $query->result();
	}

	//Counts of SHS
	function shsCounts()
	{
		$query = $this->db->query("SELECT payGroup, count(payGroup) as counts FROM hris_staff where payGroup='Senior High'");
		return $query->result();
	}

	//Counts of for Loyalty
	function loyaltyCounts()
	{
		$query = $this->db->query("SELECT count(IDNumber) as counts FROM hris_loyalty");
		return $query->result();
	}

	//Counts of for Retired
	function retiredCounts()
	{
		$query = $this->db->query("SELECT count(IDNumber) as counts FROM hris_staff where empStatus='Retired'");
		return $query->result();
	}

	//Counts of for Resigned
	function resignedCounts()
	{
		$query = $this->db->query("SELECT count(IDNumber) as counts FROM hris_staff where empStatus='Resigned'");
		return $query->result();
	}

	//Counts of for Loyalty
	function loyaltyCountsv1()
	{
		$query = $this->db->query("SELECT loyaltyType, count(loyaltyType) as counts FROM hris_loyalty group by loyaltyType");
		return $query->result();
	}

	public function loyalty()
	{
		$this->db->select('s.IDNumber, FirstName, MiddleName, LastName, empPosition, Department, dateHired, loyaltyType, l.serviceLenght');
		$this->db->from('hris_staff as s');
		$this->db->join('hris_loyalty as l', 's.IDNumber = l.IDNumber');
		$this->db->order_by("LastName");
		$query = $this->db->get();
		return $query->result();
	}

	public function loyaltyList($type)
	{
		$this->db->select('s.IDNumber, FirstName, MiddleName, LastName, empPosition, Department, dateHired, loyaltyType, l.serviceLenght');
		$this->db->from('hris_staff as s');
		$this->db->join('hris_loyalty as l', 's.IDNumber = l.IDNumber');
		$this->db->where('loyaltyType', $type);
		$this->db->order_by("LastName");
		$query = $this->db->get();
		return $query->result();
	}

	public function empGroup($cat, $group)
	{
		$this->db->select('*');
		$this->db->from('hris_staff');
		$this->db->where('payGroup', $group);
		$this->db->where('payCat', $cat);
		$this->db->order_by("LastName");
		$query = $this->db->get();
		return $query->result();
	}

	//Counts per Position
	function empGroupPosition($cat, $group)
	{
		$query = $this->db->query("SELECT empPosition, count(empPosition) as counts FROM hris_staff where payGroup='" . $group . "' and payCat='" . $cat . "' group by empPosition");
		return $query->result();
	}

	function empGroup2Position($group)
	{
		$query = $this->db->query("SELECT empPosition, count(empPosition) as counts FROM hris_staff where payGroup='" . $group . "' and payCat='Teaching' group by empPosition");
		return $query->result();
	}

	function empGroup2PositionB($group)
	{
		$query = $this->db->query("SELECT empPosition, count(empPosition) as counts FROM hris_staff where payGroup='" . $group . "' and payCat='Non-Teaching' group by empPosition");
		return $query->result();
	}


	public function empGroup2($group)
	{
		$this->db->select('*');
		$this->db->from('hris_staff');
		$this->db->where('payGroup', $group);
		$this->db->order_by("LastName");
		$query = $this->db->get();
		return $query->result();
	}

	//Elementary Teaching
	public function elemTeaching()
	{
		$this->db->select('*');
		$this->db->from('hris_staff');
		$this->db->where('payGroup', "Elementary");
		$this->db->where('payCat', "Teaching");
		$this->db->order_by("LastName");
		$query = $this->db->get();
		return $query->result();
	}

	//Elementary Non-Teaching
	public function elemNonTeaching()
	{
		$this->db->select('*');
		$this->db->from('hris_staff');
		$this->db->where('payGroup', "Elementary");
		$this->db->where('payCat', "Non-Teaching");
		$this->db->order_by("LastName");
		$query = $this->db->get();
		return $query->result();
	}

	//Secondary Teaching
	public function secTeaching()
	{
		$this->db->select('*');
		$this->db->from('hris_staff');
		$this->db->where('payGroup', "Secondary");
		$this->db->where('payCat', "Teaching");
		$this->db->order_by("LastName");
		$query = $this->db->get();
		return $query->result();
	}

	//Secondary Non-Teaching
	public function secNonTeaching()
	{
		$this->db->select('*');
		$this->db->from('hris_staff');
		$this->db->where('payGroup', "Secondary");
		$this->db->where('payCat', "Non-Teaching");
		$this->db->order_by("LastName");
		$query = $this->db->get();
		return $query->result();
	}

	//Secondary Teaching
	public function shsTeaching()
	{
		$this->db->select('*');
		$this->db->from('hris_staff');
		$this->db->where('payGroup', "SHS");
		$this->db->where('payCat', "Teaching");
		$this->db->order_by("LastName");
		$query = $this->db->get();
		return $query->result();
	}

	//SHS
	public function shsNonTeaching()
	{
		$this->db->select('*');
		$this->db->from('hris_staff');
		$this->db->where('payGroup', "SHS");
		$this->db->where('payCat', "Non-Teaching");
		$this->db->order_by("LastName");
		$query = $this->db->get();
		return $query->result();
	}

	function empLoans($IDNumber)
	{
		$query = $this->db->query("SELECT dedAgency, dedCode, policyID, concat(effectMonthFrom,' ',effectYearFrom) as effectivity, concat(effectMonthTo,' ',effectYearTo) as termination, dedAmount, principalAmount, dedStatus FROM payroll_empdeductions where IDNumber='" . $IDNumber . "' and loanStat='Active' and dedStatus!='Mandatory' order by dedID desc");
		return $query->result();
	}

	function empSalaryHistory($IDNumber)
	{
		$query = $this->db->query("SELECT * FROM payroll_monthly where IDNumber='" . $IDNumber . "' order by id desc");
		return $query->result();
	}

	function empBenefits($IDNumber)
	{
		$query = $this->db->query("SELECT * FROM payroll_special where IDNumber='" . $IDNumber . "' order by id desc");
		return $query->result();
	}

	function displaypersonnel($schoolID)
	{
		$this->db->select('*');
		$this->db->from('hris_staff');
		$this->db->where('schoolID', $schoolID);
		$this->db->order_by("LastName");
		$query = $this->db->get();
		return $query->result();
	}

	function school_personnel_active($schoolID)
	{
		$this->db->select('*');
		$this->db->from('hris_staff');
		$this->db->where('schoolID', $schoolID);
		$this->db->where('currentStatus', 'Active');
		$this->db->order_by("LastName");
		$query = $this->db->get();
		return $query->result();
	}

	function schoolProfile($schoolID)
	{
		$this->db->select('*');
		$this->db->from('schools');
		$this->db->where('schoolID', $schoolID);
		$query = $this->db->get();
		return $query->result();
	}


























	function displaypersonnelById($id)
	{
		$query = $this->db->query("select * from hris_staff where IDNumber='" . $id . "'");
		return $query->result();
	}

	//Personnel Expert Services
	function personnelServices($id)
	{
		$query = $this->db->query("select * from hris_expert where IDNumber='" . $id . "' order by dateStarted desc");
		return $query->result();
	}
	//Service Record
	function serviceRecords($id)
	{
		$query = $this->db->query("SELECT * FROM hris_staff s join mis_settings ms on s.settingsID=ms.settingsID join hris_employment he on s.IDNumber=he.IDNumber where s.IDNumber='" . $id . "' order by he.appointDate");
		return $query->result();
	}
	//Personnel 201Files
	function view201Files($id)
	{
		$query = $this->db->query("select * from hris_files where IDNumber='" . $id . "' order by docName");
		return $query->result();
	}

	//Personnel Peronnel Other Docs
	function personnelOtherDocs($id)
	{
		$query = $this->db->query("select * from hris_otherdocs where IDNumber='" . $id . "' order by docName");
		return $query->result();
	}

	//Personnel Achievements
	function personnelAchievements($id)
	{
		$query = $this->db->query("select * from hris_achievements where IDNumber='" . $id . "' order by dateAchieved desc");
		return $query->result();
	}

	//Personnel Trainings
	function personnelTrainings($id)
	{
		$query = $this->db->query("select * from hris_trainings where IDNumber='" . $id . "' order by dateStarted desc");
		return $query->result();
	}

	//Display Staff Profile
	function staffProfile($id)
	{
		$query = $this->db->query("select * from hris_staff where IDNumber='" . $id . "'");
		return $query->result();
	}


	//Personnel by Station Code
	function personnelByStationCode($stationCode)
	{
		$query = $this->db->query("select * from hris_staff where stationCOde='" . $stationCode . "'");
		return $query->result();
	}

	function getFilteredPersonnel($department, $position)
	{
		$this->db->select('*');
		if ($department)
			$this->db->where('Department', $department);
		if ($position)
			$this->db->where('empPosition', $position);
		$query = $this->db->get('hris_staff');
		return $query->result();
	}

	function getPosition($department)
	{
		$this->db->select('empPosition');
		$this->db->where('Department', $department);
		$this->db->distinct();
		$this->db->order_by('empPosition', 'ASC');
		$query = $this->db->get('hris_staff');
		return $query->result();
	}

	function is_current_password($username, $currentpass)
	{
		$this->db->select();
		$this->db->from('users');
		$this->db->where('username', $username);
		$this->db->where('password', $currentpass);
		// $this->db->like('acctStat','active');
		$query = $this->db->get();
		$row = $query->row();
		if ($row) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	function reset_userpassword($username, $newpass)
	{
		$data = array(
			'password' => $newpass
		);
		$this->db->where('username', $username);
		if ($this->db->update('users', $data)) {
			return TRUE;
		} else {
			return FALSE;
		}
	}


	//Get Leave Credits
	function displayleavecredits($id)
	{
		$query = $this->db->query("select * from hris_leaverecords where IDNumber='" . $id . "'");
		return $query->result();
	}

	function leaveCreditsdisplay($IDNumber)
	{
		$query = $this->db->query("select * from hris_leaverecords where IDNumber='" . $IDNumber . "'");
		return $query->result();
	}

	//view files
	function viewfiles($id)
	{
		$query = $this->db->query("select * from hris_files f join hris_staff s on f.IDNumber=s.IDNumber where s.IDNumber='" . $id . "' order by docName");
		return $query->result();
	}
	//view 201 files All
	function viewfilesAll()
	{
		$query = $this->db->query("select * from hris_files f join hris_staff s on f.IDNumber=s.IDNumber group by s.IDNumber");
		return $query->result();
	}

	//Get Profile Pictures
	public function profilepic($id)
	{
		$this->db->select('*');
		$this->db->from('users');
		$this->db->where('IDNumber', $id);
		$query = $this->db->get();
		return $query->result();
	}

	public function pendingLeave()
	{
		$this->db->select('hris_staff.IDNumber, FirstName, MiddleName, LastName, appDate, leaveType, daysApplied, dateFrom, dateTo, leaveStatus, leaveID');
		$this->db->from('hris_leave');
		$this->db->join('hris_staff', 'hris_staff.IDNumber=hris_leave.IDNumber');
		$this->db->where('leaveStatus', 'For Approval');
		$query = $this->db->get();
		return $query->result();
	}


	

	public function pendingLeaveEvaluation($empID, $leaveID)
	{
		$query = $this->db->query("select s.IDNumber, FirstName, MiddleName, LastName, appDate, leaveType, daysApplied, dateFrom, dateTo, leaveStatus, leaveID, appDate, daysApplied, r.slTotal, r.vlTotal, r.cocTotal from hris_staff s join hris_leave l on s.IDNumber=l.IDNumber join hris_leaverecords r on s.IDNumber=r.IDNumber where s.IDNumber='" . $empID . "' and leaveID='" . $leaveID . "'");
		return $query->result();
	}


	//Get Available Leave History
	public function getLeaveLeaveHistory($id)
	{
		$this->db->select('*');
		$this->db->from('hris_leave');
		$this->db->join('hris_staff', 'hris_staff.IDNumber=hris_leave.IDNumber');
		$query = $this->db->get();
		return $query->result();
	}

	//Get Leave Records of All Employee
	public function getLeaveAll()
	{
		$this->db->select('*');
		$this->db->from('hris_leave');
		$this->db->join('hris_staff', 'hris_staff.IDNumber=hris_leave.IDNumber');
		$query = $this->db->get();
		return $query->result();
	}

	//Family	
	public function family($id)
	{
		$this->db->select('*');
		$this->db->from('hris_family');
		$this->db->where('IDNumber', $id);
		$query = $this->db->get();
		return $query->result();
	}

	//Education
	public function education($id)
	{
		$this->db->select('*');
		$this->db->from('hris_educ');
		$this->db->where('IDNumber', $id);
		$query = $this->db->get();
		return $query->result();
	}

	//Civil Service
	public function cs($id)
	{
		$this->db->select('*');
		$this->db->from('hris_cs');
		$this->db->where('IDNumber', $id);
		$query = $this->db->get();
		return $query->result();
	}

	//Trainings
	public function trainings($id)
	{
		$this->db->select('*');
		$this->db->from('hris_trainings');
		$this->db->where('IDNumber', $id);
		$query = $this->db->get();
		return $query->result();
	}

	public function rqa($jobID)
	{
		$query = $this->db->query("SELECT * FROM hris_applications a join hris_applications_rating r on a.appID=r.appID where jobID='" . $jobID . "' and  total_points>='50'");
		return $query->result();
	}

	public function rqa_group($jobID)
	{
		$query = $this->db->query("SELECT specialization, count(specialization) as speCounts, r.appID, a.jobID FROM hris_applications a join hris_applications_rating r on a.appID=r.appID join hris_applicant ha on ha.record_no=r.record_no where jobID='" . $jobID . "' and  total_points>='50' group by specialization");
		return $query->result();
	}

	public function rqa_group_mun($jobID)
	{
		$this->db->select("
			COALESCE(app.resCity, staff.resCity) AS resCity,
			COUNT(*) AS resCounts,
			MIN(r.appID) AS appID,
			a.jobID
		", false);
		$this->db->from('hris_applications a');
		$this->db->join('hris_applicant app', 'a.empEmail = app.empEmail', 'left');
		$this->db->join('hris_staff staff', 'app.record_no IS NULL AND a.empEmail = staff.IDNumber', 'left');
		$this->db->join('hris_applications_rating r', 'a.appID = r.appID', 'left');
		$this->db->where('a.jobID', $jobID);
		$this->db->where('r.total_points >=', 50);
		$this->db->group_by('COALESCE(app.resCity, staff.resCity)', false);
		$this->db->order_by('resCity', 'ASC');

		$query = $this->db->get();
		return $query->result();
	}

	public function rqa_municipality_track($jobID, $spec)
	{
		$query = $this->db->query("SELECT resCity, count(resCity) as resCounts, r.appID, a.jobID, shss FROM hris_applications a join hris_applications_rating r on a.appID=r.appID join hris_applicant ha on ha.record_no=r.record_no where jobID='" . $jobID . "' and shss='" . $spec . "' and  total_points>='50' group by resCity");
		return $query->result();
	}

	public function rqa_municipality_spec($jobID, $spec)
	{
		$query = $this->db->query("SELECT resCity, count(resCity) as resCounts, r.appID, a.jobID, specialization FROM hris_applications a join hris_applications_rating r on a.appID=r.appID join hris_applicant ha on ha.record_no=r.record_no where jobID='" . $jobID . "' and specialization='" . $spec . "' and  total_points>='50' group by resCity");
		return $query->result();
	}

	public function rqa_group_shs($jobID)
	{
		$query = $this->db->query("SELECT track, count(track) as speCounts, r.appID, a.jobID FROM hris_applications a join hris_applications_rating r on a.appID=r.appID join hris_applicant ha on ha.record_no=r.record_no where jobID='" . $jobID . "' and  total_points>='50' group by track");
		return $query->result();
	}

	public function rqa_group_jhs($jobID)
	{
		$query = $this->db->query("SELECT shss, count(shss) as speCounts, r.appID, a.jobID FROM hris_applications a join hris_applications_rating r on a.appID=r.appID join hris_applicant ha on ha.record_no=r.record_no where jobID='" . $jobID . "' and  total_points>='50' group by shss");
		return $query->result();
	}

	public function rqa_specialization($jobID, $spe)
	{
		$query = $this->db->query("SELECT * FROM hris_applications a join hris_applications_rating r on a.appID=r.appID join hris_applicant ha on ha.record_no=r.record_no where jobID='" . $jobID . "' and specialization='" . $spe . "' and  total_points>='50'");
		return $query->result();
	}

	public function rqa_municipality_list($jobID, $mun)
	{
		$query = $this->db->query("SELECT * FROM hris_applications a join hris_applications_rating r on a.appID=r.appID join hris_applicant ha on ha.record_no=r.record_no where jobID='" . $jobID . "' and resCity='" . $mun . "' and  total_points>='50'");
		return $query->result();
	}

	public function rqa_track($jobID, $spe)
	{
		$query = $this->db->query("SELECT * FROM hris_applications a join hris_applications_rating r on a.appID=r.appID join hris_applicant ha on ha.record_no=r.record_no where jobID='" . $jobID . "' and track='" . $spe . "' and  total_points>='50'");
		return $query->result();
	}


	public function count_all_staff($schoolID)
	{
		$query = $this->db->query("SELECT count(IDNumber) as Counts FROM hris_staff where schoolID='" . $schoolID . "' and currentStatus='Active'");
		return $query->result();
	}

	public function count_all_teaching($schoolID)
	{
		$query = $this->db->query("SELECT count(IDNumber) as Counts FROM hris_staff where schoolID='" . $schoolID . "' and payCat='Teaching' and currentStatus='Active'");
		return $query->result();
	}

	public function count_all_nonteaching($schoolID)
	{
		$query = $this->db->query("SELECT count(IDNumber) as Counts FROM hris_staff where schoolID='" . $schoolID . "' and payCat='Non-Teaching' and currentStatus='Active'");
		return $query->result();
	}

	public function count_all_inactive($schoolID)
	{
		$query = $this->db->query("SELECT count(IDNumber) as Counts FROM hris_staff where schoolID='" . $schoolID . "' and currentStatus!='Active'");
		return $query->result();
	}
	//Personnel Counts By Station
	public function stationpersonnelCounts($stationCode)
	{
		$query = $this->db->query("SELECT empPosition, count(empPosition) as Counts FROM hris_staff where stationCode='" . $stationCode . "' group by empPosition");
		return $query->result();
	}



	//Personnel by Department Counts by Station
	public function stationdepartmentcounts($stationCode)
	{
		$query = $this->db->query("SELECT department, count(department) as Counts FROM hris_staff where stationCOde='" . $stationCode . "' group by department order by department");
		return $query->result();
	}





	//Leave For Approval Counts
	public function leaveForApproval()
	{
		$query = $this->db->query("SELECT count(leaveStatus) as statCount FROM hris_leave where leaveStatus='For Approval' group by leaveStatus");
		return $query->result();
	}

	//For Retirement
	public function forRetirement($year)
	{
		$query = $this->db->query("SELECT count(retYear) as retYearCounts FROM hris_staff s join hris_employment e on s.IDNumber=e.IDNumber where s.retYear='" . $year . "' and e.endDate='Present'");
		return $query->result();
	}

	//For Loyalty Cash Award
	public function forLoyalty($year)
	{
		$query = $this->db->query("SELECT count(l.loyaltyDate) as loyaltyCounts FROM hris_loyalty l join hris_employment e on l.IDNumber=e.IDNumber WHERE YEAR(l.loyaltyDate ) = '" . $year . "' and e.endDate='Present'");
		return $query->result();
	}

	//For Loyalty Cash Award List
	public function forLoyaltyList()
	{
		$query = $this->db->query("SELECT * FROM hris_staff s join hris_employment e on s.IDNumber=e.IDNumber join hris_loyalty l on e.IDNumber=l.IDNumber join mis_settings st on s.settingsID=st.settingsID WHERE YEAR(l.loyaltyDate ) = YEAR( NOW()) and e.endDate='Present'");
		return $query->result();
	}

	//For Retirement List
	public function forRetirementList()
	{
		$query = $this->db->query("SELECT * FROM hris_staff s join mis_settings st on s.settingsID=st.settingsID join hris_employment e on s.IDNumber=e.IDNumber where s.retYear=YEAR( NOW()) group by s.IDNumber");
		return $query->result();
	}

	//Birthday Celebrants Counts
	public function birthday()
	{
		$query = $this->db->query("SELECT s.IDNumber, concat(s.LastName,', ',s.FirstName,'  ',s.MiddleName) as empName, MONTHNAME(s.BirthDate), count(MONTHNAME(s.BirthDate)) as celebrant, DAY(s.BirthDate) FROM hris_staff s join hris_employment e on s.IDNumber=e.IDNumber WHERE MONTH(s.BirthDate ) = MONTH( NOW())");
		return $query->result();
	}

	//Birthday Celebrants List
	public function birthdayList()
	{
		$query = $this->db->query("SELECT s.IDNumber, concat(s.LastName,', ',s.FirstName,'  ',s.MiddleName) as empName, s.BirthDate, MONTHNAME(s.BirthDate) as month, DAY(s.BirthDate) as date, s.Department FROM hris_staff s join hris_employment e on s.IDNumber=e.IDNumber WHERE MONTH(s.BirthDate ) = MONTH( NOW()) order by date");
		return $query->result();
	}

	//REQUEST ---------------------------------------------------------------------------------
	function docRequest($id)
	{
		$query = $this->db->query("SELECT * FROM request where IDNumber='" . $id . "' order by dateReq desc");
		return $query->result();
	}

	//Released Request
	function totalReleased()
	{
		$query = $this->db->query("SELECT ongoingStat, count(ongoingStat) as requestCounts FROM request_stat where ongoingStat='Released' group by ongoingStat");
		return $query->result();
	}

	//Released Request
	function releasedRequest()
	{
		$query = $this->db->query("select * from request sr join request_stat st on sr.trackingNo=st.trackingNo join hris_staff p on st.IDNumber=p.IDNumber where st.ongoingStat='Released'");
		return $query->result();
	}

	function studerequestTracking($id)
	{
		$query = $this->db->query("SELECT * FROM request sr join request_stat st on sr.trackingNo=st.trackingNo where sr.trackingNo='" . $id . "' order by statID desc");
		return $query->result();
	}

	function getTrackingNo()
	{
		$query = $this->db->query("select * from request order by trackingNo desc limit 1");
		return $query->result();
	}

	//Personnel List
	function getProfile()
	{
		$query = $this->db->query("select * from hris_staff order by LastName");
		return $query->result();
	}

	//Total Request
	function totalStudeRequest()
	{
		$query = $this->db->query("SELECT reqStat, count(reqStat) as requestCounts FROM request");
		return $query->result();
	}

	//Open Request
	function openRequest()
	{
		$query = $this->db->query("SELECT reqStat, count(reqStat) as requestCounts FROM request where reqStat='Open'");
		return $query->result();
	}

	//Open Request
	function closedRequest()
	{
		$query = $this->db->query("SELECT reqStat, count(reqStat) as requestCounts FROM request where reqStat='Closed'");
		return $query->result();
	}

	function docReqCounts()
	{
		$query = $this->db->query("SELECT docName, count(docName) as docCounts FROM request group by docName");
		return $query->result();
	}
	//Student REQUEST
	function studeRequestList()
	{
		$query = $this->db->query("select * from request sr join hris_staff p on sr.IDNumber=p.IDNumber where sr.reqStat='Open' order by trackingNo desc");
		return $query->result();
	}

	//Student REQUEST
	function openDocRequest()
	{
		$query = $this->db->query("select * from request sr join hris_staff p on sr.IDNumber=p.IDNumber where sr.reqStat='Open' order by sr.dateReq desc");
		return $query->result();
	}
	//Student REQUEST
	function closedDocRequest()
	{
		$query = $this->db->query("select * from request sr join hris_staff p on sr.IDNumber=p.IDNumber where sr.reqStat='Closed' order by sr.dateReq desc");
		return $query->result();
	}

	//USER ACCOUNTS ---------------------------------------------------------------------------------
	function viewAccounts()
	{
		$query = $this->db->query("SELECT * FROM users order by lName");
		return $query->result();
	}

	function viewAccountsID($id)
	{
		$query = $this->db->query("SELECT * FROM users where username='" . $id . "'");
		return $query->result();
	}

	//DepEd Email Request
	function emailReqForProcessing()
	{
		$query = $this->db->query("SELECT * FROM hris_staff h join deped_email_request d on h.IDNumber=d.IDNumber where reqStat='Open' order by reqID desc");
		return $query->result();
	}

	//DepEd Email Request
	function processEmailRequest($id)
	{
		$query = $this->db->query("SELECT * FROM hris_staff h join deped_email_request d on h.IDNumber=d.IDNumber where d.reqID='" . $id . "'");
		return $query->result();
	}

	function leaveCreditsUploading($record)
	{

		if (count($record) > 0) {

			date_default_timezone_set('Asia/Manila'); # add your city to set local time zone
			$date = date("Y-m-d");

			$newprofile = array(
				"asOf" => $date,
				"vlTotal" => trim($record[1]),
				"slTotal" => trim($record[2]),
				"cocTotal" => trim($record[3]),
				"IDNumber" => trim($record[0]),
			);

			$this->db->insert('hris_leaverecords', $newprofile);

			// }

		}
	}

	function appRatingUploading($record)
	{

		if (count($record) > 0) {

			// Check user
			// $this->db->select('*');
			// $this->db->where('IDNumber', $record[0]);
			// $q = $this->db->get('staff');
			// $response = $q->result_array();

			// Insert record
			// if(count($response) == 0){
			$newprofile = array(
				"record_no" => trim($record[0]),
				"appID" => trim($record[1]),
				"education" => trim($record[2]),
				"training" => trim($record[3]),
				"experience" => trim($record[4]),
				"let_rating" => trim($record[5]),
				"demo_rating" => trim($record[6]),
				"tr_rating" => trim($record[7]),
				"total_points" => trim($record[8]),
			);


			$this->db->insert('hris_applications_rating', $newprofile);

			// }

		}
	}

	// Define the formatDate function as a class method
public function formatDate($date, $format = 'm/d/Y')
{
    $dateTime = DateTime::createFromFormat($format, $date);
    return $dateTime ? $dateTime->format('Y-m-d') : null;
}

public function enroleesUploading($record)
{
    if (!empty($record)) {
        // Format date fields
        $formattedBDate = $this->formatDate(trim($record[4]));
        $formattedWeighingDate = $this->formatDate(trim($record[14]));
		$schoolID=$this->session->userdata('c_id');
		$username=$this->session->userdata('username');
		$sy=$this->input->post('sy');

        $newProfile = array(
            "lrn"               => trim($record[0]),
            "fName"             => trim($record[1]),
            "mName"             => trim($record[2]),
            "lName"             => trim($record[3]),
            "bDate"             => $formattedBDate,
            "weight"            => trim($record[5]),
            "height"            => trim($record[6]),
            "sex"               => trim($record[7]),
            "grLevel"           => trim($record[8]),
            "section"           => trim($record[9]),
            "dewormStat"        => trim($record[10]),
            "pc_for_milk"       => trim($record[11]),
            "4ps"               => trim($record[12]),
            "sbfp_ben_prevyear" => trim($record[13]),
            "weighingDate"      => $formattedWeighingDate,
            "categoryPri"       => trim($record[15]),
            "categorySec"       => trim($record[16]),
			"schoolID"       => $schoolID,
			"encoder"       => $username,
			"sy"       => $sy,
        );

        $this->db->insert('sbfp_data', $newProfile);
    }
}
	
	

	function serviceRecordUploading($record)
	{

		if (count($record) > 0) {

			// Check user
			// $this->db->select('*');
			// $this->db->where('IDNumber', $record[0]);
			// $q = $this->db->get('staff');
			// $response = $q->result_array();

			// Insert record
			// if(count($response) == 0){
			$newprofile = array(
				"IDNumber" => trim($record[0]),
				"appointDate" => trim($record[1]),
				"endDate" => trim($record[2]),
				"empPosition" => trim($record[3]),
				"empStatus" => trim($record[4]),
				"salary" => trim($record[5]),
				"empStation" => trim($record[6]),
				"lvwithoutpay" => trim($record[7]),
				"separation" => trim($record[8]),
			);


			$this->db->insert('hris_employment', $newprofile);

			// }

		}
	}

	function profileUploading($record)
	{

		if (count($record) > 0) {
			$newprofile = array(
				"IDNumber" => trim($record[0]),
				"employeeNo" => trim($record[0]),
				"FirstName" => trim($record[1]),
				"MiddleName" => trim($record[2]),
				"LastName" => trim($record[3]),
				"NameExtn" => trim($record[4]),
				"BirthDate" => trim($record[5]),
				"BirthPlace" => trim($record[6]),
				"Sex" => trim($record[7]),
				"resBarangay" => trim($record[8]),
				"perBarangay" => trim($record[8]),
				"resCity" => trim($record[9]),
				"perCity" => trim($record[9]),
				"resProvince" => trim($record[10]),
				"perProvince" => trim($record[10]),
				"empEmail" => trim($record[11]),
				"empMobile" => trim($record[12]),
				"empTelNo" => trim($record[13]),
				"empPosition" => trim($record[14]),
				"sgNo" => trim($record[15]),
				"stepNo" => trim($record[16]),
				"actualSalary" => trim($record[17]),
				"csEligibility" => trim($record[18]),
				"Department" => trim($record[19]),
				"dateHired" => trim($record[20]),
				"origAppointmentDate" => trim($record[20]),
				"lastAppointmentDate" => trim($record[21]),
				"staCode" => trim($record[22]),
				"schoolID" => trim($record[23]),
				"directHead" => trim($record[24]),
				"directHeadPosition" => trim($record[25]),
				"gsis" => trim($record[26]),
				"pagibig" => trim($record[27]),
				"philHealth" => trim($record[28]),
				"sssNo" => trim($record[29]),
				"tinNo" => trim($record[30]),
				"itemNo" => trim($record[31]),
				"empStatus" => trim($record[32]),
				"payGroup" => trim($record[33]),
				"payCat" => trim($record[34]),
				"currentStatus" => trim('Active'),
				"citizenship" => trim('Filipino'),
				"citizenshipCountry" => trim('Philippines'),
				"settingsID" => trim('1'),
			);

			$users = array(
				"username" => trim($record[0]),
				"user_id" => trim($record[0]),
				"fname" => trim($record[1]),
				"mname" => trim($record[2]),
				"lname" => trim($record[3]),
				"sex" => trim($record[7]),
				"status" => trim('1'),
				"image" => trim('avatar.png'),
				"position" => trim('user'),
				"password" => trim('$2y$10$XIJvhSKy/baPn3BheNKLP.4L7272YklMWR9WTMpRLbC9RIUR6RHmC'),
				// default password is security@123456
			);


			$this->db->insert('hris_staff', $newprofile);
			$this->db->insert('users', $users);

			// }

		}
	}

	public function is_endorser($username)
{
    $this->db->where('endorser', $username);
    $query = $this->db->get('epmloyee_superior'); // note: it should be 'epmloyee_superior' based on your earlier code
    return $query->num_rows() > 0;
}

}
