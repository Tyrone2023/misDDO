<?php
class StudentModel extends CI_Model
{

	public function insert_students()
	{
		// Step 1: Prepare the subquery to get existing usernames
		$this->db->select('username');
		$this->db->from('o_users');
		$subquery = $this->db->get_compiled_select();

		// Step 2: Prepare the main query to select from `studeprofile`
		$this->db->select('StudentNumber AS username', FALSE);
		$this->db->select('SHA1(DATE_FORMAT(birthDate, "%Y-%m-%d")) AS password', FALSE);
		$this->db->select("'Student' AS position", FALSE);
		$this->db->select('FirstName AS fName');
		$this->db->select('MiddleName AS mName');
		$this->db->select('LastName AS lName');
		$this->db->select('EmailAddress AS email');
		$this->db->select("'avatar.png' AS avatar", FALSE);
		$this->db->select("'active' AS acctStat", FALSE);
		$this->db->select('NOW() AS dateCreated', FALSE);
		$this->db->from('studeprofile');
		$this->db->where_not_in('StudentNumber', $subquery);

		// Compile the select part of the query
		$select_query = $this->db->get_compiled_select();

		// Step 3: Execute the insert query with INSERT IGNORE
		$this->db->query("INSERT IGNORE INTO o_users (username, password, position, fName, mName, lName, email, avatar, acctStat, dateCreated) $select_query");
	}

	public function insert_teachers()
	{
		// Step 1: Prepare the subquery to get existing usernames
		$this->db->select('username');
		$this->db->from('o_users');
		$subquery = $this->db->get_compiled_select();

		// Step 2: Prepare the main query to select from `studeprofile`
		$this->db->select('IDNumber AS username', FALSE);
		$this->db->select('SHA1(DATE_FORMAT(BirthDate, "%Y-%m-%d")) AS password', FALSE);
		$this->db->select("'Teacher' AS position", FALSE);
		$this->db->select('FirstName AS fName');
		$this->db->select('MiddleName AS mName');
		$this->db->select('LastName AS lName');
		$this->db->select('empEmail AS email');
		$this->db->select("'avatar.png' AS avatar", FALSE);
		$this->db->select("'active' AS acctStat", FALSE);
		$this->db->select('NOW() AS dateCreated', FALSE);
		$this->db->from('staff');
		$this->db->where_not_in('IDNumber', $subquery);

		// Compile the select part of the query
		$select_query = $this->db->get_compiled_select();

		// Step 3: Execute the insert query with INSERT IGNORE
		$this->db->query("INSERT IGNORE INTO o_users (username, password, position, fName, mName, lName, email, avatar, acctStat, dateCreated) $select_query");
	}


	//Sex Count Summary Per Semester
	public function SexCount1($sy)
	{
		$this->db->select('p.Sex, COUNT(p.Sex) as sexCount');
		$this->db->from('studeprofile p');
		$this->db->join('semesterstude s', 'p.StudentNumber = s.StudentNumber');
		$this->db->where('s.SY', $sy);
		$this->db->where('s.Status', 'Enrolled');
		$this->db->group_by('p.Sex');

		$query = $this->db->get();
		return $query->result();
	}

	public function announcement()
	{
		$this->db->select('*');
		$this->db->from('announcement');
		$this->db->order_by('aID', 'desc');
		$query = $this->db->get();
		return $query->result();
	}

	public function deleteAnnouncement($id)
	{
		$this->db->where('aID', $id);
		$this->db->delete('announcement');
	}

	public function medInfo()
	{
		$this->db->select('*');
		$this->db->from('medical_info m');
		$this->db->join('studeprofile p', 'm.StudentNumber = p.StudentNumber');
		$query = $this->db->get();
		return $query->result();
	}


	public function sbfp_student($sy, $YearLevel, $Section, $w_group)
	{
		$this->db->select('sb.StudentNumber, p.FirstName, p.MiddleName, p.LastName, sb.y_mo, sb.months, sb.sbfp_date, sb.height, sb.sy, sb.bmi, sb.bmi_eqv, YearLevel, Section, weight, w_group, sID');
		$this->db->from('semester_sbfp sb');
		$this->db->join('studeprofile p', 'sb.StudentNumber = p.StudentNumber');
		$this->db->where('sb.sy', $sy);
		$this->db->where('sb.YearLevel', $YearLevel);
		$this->db->where('sb.Section', $Section);
		$this->db->where('sb.w_group', $w_group);
		$query = $this->db->get();

		return $query->result();
	}


	public function collectionReportAll($SY)
	{
		// Set date limit for the last 3 months
		$date_limit = date('Y-m-d', strtotime('-3 months'));

		// Main collection report query
		$this->db->select("
        paymentsaccounts.PDate, 
        paymentsaccounts.ORNumber, 
        FORMAT(paymentsaccounts.Amount, 2) as Amount, 
        paymentsaccounts.description, 
        paymentsaccounts.StudentNumber, 
        CONCAT(studeprofile.LastName, ', ', studeprofile.FirstName, ' ', studeprofile.MiddleName) as Payor, 
        studeprofile.Course, 
        paymentsaccounts.PaymentType, 
        paymentsaccounts.Description, 
        paymentsaccounts.CheckNumber, 
        paymentsaccounts.Bank, 
        paymentsaccounts.CollectionSource, 
        CONCAT(paymentsaccounts.Sem, ' ', paymentsaccounts.SY) as Semester
    ");
		$this->db->from('paymentsaccounts');
		$this->db->join('studeprofile', 'paymentsaccounts.StudentNumber = studeprofile.StudentNumber');
		$this->db->where('paymentsaccounts.ORStatus', 'Valid');
		$this->db->where('paymentsaccounts.SY', $SY);
		$this->db->where('paymentsaccounts.PDate >=', $date_limit);
		$this->db->order_by('paymentsaccounts.PDate', 'DESC');
		$collection_data = $this->db->get()->result();

		// Yearly collection report query
		$this->db->select("
        YEAR(paymentsaccounts.PDate) as Year, 
        SUM(paymentsaccounts.Amount) as TotalAmount
    ");
		$this->db->from('paymentsaccounts');
		$this->db->where('paymentsaccounts.ORStatus', 'Valid');
		$this->db->where('paymentsaccounts.SY', $SY);
		$this->db->where('paymentsaccounts.PDate >=', $date_limit);
		$this->db->group_by('Year');
		$this->db->order_by('Year', 'DESC');
		$yearly_data = $this->db->get()->result();

		// Monthly collection report query
		$this->db->select("
        DATE_FORMAT(paymentsaccounts.PDate, '%Y-%m') as Month, 
        SUM(paymentsaccounts.Amount) as TotalAmount
    ");
		$this->db->from('paymentsaccounts');
		$this->db->where('paymentsaccounts.ORStatus', 'Valid');
		$this->db->where('paymentsaccounts.SY', $SY);
		$this->db->where('paymentsaccounts.PDate >=', $date_limit);
		$this->db->group_by('Month');
		$this->db->order_by('Month', 'DESC');
		$monthly_data = $this->db->get()->result();

		// Return all data
		return [
			'collection_data' => $collection_data,
			'yearly_data' => $yearly_data,
			'monthly_data' => $monthly_data
		];
	}

















	public function getReportByYear($year)
	{
		$this->db->select("
        paymentsaccounts.PDate, 
        paymentsaccounts.ORNumber, 
        FORMAT(paymentsaccounts.Amount, 2) as Amount, 
        paymentsaccounts.description, 
        paymentsaccounts.StudentNumber, 
        CONCAT(studeprofile.LastName, ', ', studeprofile.FirstName, ' ', studeprofile.MiddleName) as Payor, 
        studeprofile.Course, 
        paymentsaccounts.PaymentType, 
        paymentsaccounts.Description, 
        paymentsaccounts.CheckNumber, 
        paymentsaccounts.Bank, 
        paymentsaccounts.CollectionSource, 
        CONCAT(paymentsaccounts.Sem, ' ', paymentsaccounts.SY) as Semester
    ")
			->from('paymentsaccounts')
			->join('studeprofile', 'paymentsaccounts.StudentNumber = studeprofile.StudentNumber')
			->where('YEAR(paymentsaccounts.PDate)', $year)
			->where('paymentsaccounts.ORStatus', 'Valid')
			->order_by('paymentsaccounts.PDate', 'DESC');
		return $this->db->get()->result();
	}

	public function getReportByMonth($year, $month)
	{
		$this->db->select("
        paymentsaccounts.PDate, 
        paymentsaccounts.ORNumber, 
        FORMAT(paymentsaccounts.Amount, 2) as Amount, 
        paymentsaccounts.description, 
        paymentsaccounts.StudentNumber, 
        CONCAT(studeprofile.LastName, ', ', studeprofile.FirstName, ' ', studeprofile.MiddleName) as Payor, 
        paymentsaccounts.PaymentType, 
        paymentsaccounts.Description
    ")
			->from('paymentsaccounts')
			->join('studeprofile', 'paymentsaccounts.StudentNumber = studeprofile.StudentNumber')
			->where('YEAR(paymentsaccounts.PDate)', $year)
			->where('MONTH(paymentsaccounts.PDate)', $month)
			->where('paymentsaccounts.ORStatus', 'Valid')
			->order_by('paymentsaccounts.PDate', 'DESC');

		return $this->db->get()->result();
	}







	public function collectionReport($from = null, $to = null)
	{
		// Define the query
		$this->db->select("
        paymentsaccounts.PDate, 
        paymentsaccounts.ORNumber, 
        FORMAT(paymentsaccounts.Amount, 2) as Amount, 
        paymentsaccounts.description, 
        paymentsaccounts.StudentNumber, 
        CONCAT(studeprofile.LastName, ', ', studeprofile.FirstName, ' ', studeprofile.MiddleName) as Payor, 
        studeprofile.Course, 
        paymentsaccounts.PaymentType, 
        paymentsaccounts.Description, 
        paymentsaccounts.CheckNumber, 
        paymentsaccounts.Bank, 
        paymentsaccounts.CollectionSource, 
        CONCAT(paymentsaccounts.Sem, ' ', paymentsaccounts.SY) as Semester
    ")
			->from('paymentsaccounts')
			->join('studeprofile', 'paymentsaccounts.StudentNumber = studeprofile.StudentNumber')
			->where('paymentsaccounts.ORStatus', 'Valid');

		// Apply date filters if provided
		if ($from !== null) {
			$this->db->where('paymentsaccounts.PDate >=', $from);
		}
		if ($to !== null) {
			$this->db->where('paymentsaccounts.PDate <=', $to);
		}

		// Order by payment date descending
		$this->db->order_by('paymentsaccounts.PDate', 'DESC');

		// Execute the query and return the results
		return $this->db->get()->result();
	}


	public function collectionYear($year)
	{
		// Loading the database
		$this->load->database();

		// Building the query
		$this->db->select("PDate, ORNumber, FORMAT(Amount, 2) as Amount, paymentsaccounts.description, paymentsaccounts.StudentNumber, CONCAT(studeprofile.LastName, ', ', studeprofile.FirstName, ' ', studeprofile.MiddleName) as Payor, paymentsaccounts.Description, PaymentType, YEAR(PDate) as Year");
		$this->db->from('paymentsaccounts');
		$this->db->join('studeprofile', 'studeprofile.StudentNumber = paymentsaccounts.StudentNumber', 'left');
		$this->db->where('YEAR(PDate)', $year);
		$this->db->where('ORStatus', 'Valid');
		$this->db->order_by('PDate', 'desc');

		// Executing the query and returning the result
		$query = $this->db->get();
		return $query->result();
	}

	public function studeAccounts($sy, $yearlevel)
	{
		$this->db->select("sa.AccountID, 
                       sa.StudentNumber, 
                       CONCAT(sp.LastName, ', ', sp.FirstName, ' ', sp.MiddleName) as StudentName, 
                       sa.Course, 
                       FORMAT(sa.AcctTotal, 2) as AcctTotal, 
                       FORMAT(sa.TotalPayments, 2) as TotalPayments, 
                       FORMAT(sa.Discount, 2) as Discount, 
                       FORMAT(sa.CurrentBalance, 2) as CurrentBalance, 
                       sa.YearLevel, 
                       sa.Sem, 
                       sa.SY");
		$this->db->from("studeaccount sa");
		$this->db->join("studeprofile sp", "sp.StudentNumber = sa.StudentNumber");
		$this->db->where("sa.SY", $sy);
		$this->db->where("sa.YearLevel", $yearlevel);
		$this->db->group_by("sa.StudentNumber");
		$this->db->order_by("StudentName", "ASC");

		$query = $this->db->get();
		return $query->result();
	}

	// public function studeAccount()
	// {
	//     // Specify the table and join the tables
	//     $this->db->select('studeaccount.*, studeprofile.*');
	//     $this->db->from('studeaccount');
	//     $this->db->join('studeprofile', 'studeaccount.StudentNumber = studeprofile.StudentNumber');

	//     // // Add conditions if needed
	//     // $this->db->where('studeaccount.sy', $sy);
	//     // $this->db->where('studeprofile.course', $course);
	//     // $this->db->where('studeprofile.yearlevel', $yearlevel);

	//     // Execute the query
	//     $query = $this->db->get();
	//     return $query->result();
	// }

	public function studeAccount()
	{
		$query = $this->db->query("
        SELECT DISTINCT 
            sa.StudentNumber, 
            sa.Course, 
            sa.YearLevel, 
			sa.Status,
			sa.Section,
			sa.Status,
            sp.FirstName, 
            sp.MiddleName, 
            sp.LastName
			
        FROM semesterstude sa
        JOIN studeprofile sp ON sa.StudentNumber = sp.StudentNumber
    ");
		return $query->result();
	}


	public function getStudentsWithoutAccounts($schoolYear)
	{
		// Subquery: Get students with accounts in the current school year
		$this->db->distinct()
			->select('StudentNumber')
			->from('studeaccount')
			->where('SY', $schoolYear);
		$subQuery = $this->db->get_compiled_select();

		// Main query: Get students without accounts for the current SY
		$this->db->select('sa.StudentNumber, sp.FirstName, sp.MiddleName, sp.LastName')
			->from('semesterstude sa')
			->join('studeprofile sp', 'sa.StudentNumber = sp.StudentNumber', 'left')
			->where('sa.SY', $schoolYear)
			->where("sa.StudentNumber NOT IN ($subQuery)", NULL, FALSE);

		$query = $this->db->get();
		return $query->result();  // Return result set
	}





	// public function getStudentsWithoutAccounts($schoolYear) {
	//     // Subquery: Get students with accounts in the current school year
	//     $this->db->distinct()
	//              ->select('StudentNumber')
	//              ->from('studeaccount')
	//              ->where('SY', $schoolYear);
	//     $subQuery = $this->db->get_compiled_select();

	//     // Main query: Get all students from studeprofile without accounts
	//     $this->db->select('sp.StudentNumber, sp.FirstName, sp.MiddleName, sp.LastName')
	//              ->from('studeprofile sp')
	//              ->where("sp.StudentNumber NOT IN ($subQuery)", NULL, FALSE);

	//     $query = $this->db->get();
	//     return $query->result();  // Return result set
	// }






















	public function studefees()
	{
		// $query = $this->db->get('studeaccount'); 
		$query = $this->db->query("SELECT * FROM fees");
		return $query->result();
	}


	// public function getDescriptionsByYearLevel($yearLevel) {
	//     $this->db->where('YearLevel', $yearLevel);
	//     $query = $this->db->get('fees');
	//     return $query->result();
	// }

	public function insertstudeAccount($data)
	{
		return $this->db->insert('studeaccount', $data);
	}


	public function getAccountDetails($accountID)
	{
		// Join the studeaccount table with the studeprofile table
		$this->db->select('studeaccount.*, studeprofile.FirstName, studeprofile.MiddleName, studeprofile.LastName');
		$this->db->from('studeaccount');
		$this->db->join('studeprofile', 'studeprofile.StudentNumber = studeaccount.StudentNumber');
		$this->db->where('studeaccount.AccountID', $accountID);
		return $this->db->get()->row();
	}

	public function getAllStudents()
	{
		$this->db->select('StudentNumber, FirstName, MiddleName, LastName');
		return $this->db->get('studeprofile')->result();
	}

	// public function updateStudentAccount($StudentNumber, $data) {
	//     $this->db->where('StudentNumber', $StudentNumber);
	//     return $this->db->update('studeaccount', $data);
	// }


	public function updateStudentAccount($studentNumber, $SY, $data)
	{
		$this->db->where('StudentNumber', $studentNumber);
		$this->db->where('SY', $SY); // Ensure only the correct SY is updated
		return $this->db->update('studeaccount', $data);
	}

	public function addDiscount($data)
	{
		return $this->db->insert('studediscount', $data);
	}

	public function updateStudentAccountFields($studentNumber, $sy, $data)
	{
		$this->db->where('StudentNumber', $studentNumber);
		$this->db->where('SY', $sy);
		return $this->db->update('studeaccount', $data);
	}





	public function getAccountDetailsByStudentNumberAndSY($studentNumber, $SY)
	{
		$this->db->select('studeaccount.*, studeprofile.FirstName, studeprofile.MiddleName, studeprofile.LastName');
		$this->db->from('studeaccount');
		$this->db->join('studeprofile', 'studeprofile.StudentNumber = studeaccount.StudentNumber');
		$this->db->where('studeaccount.StudentNumber', $studentNumber);
		$this->db->where('studeaccount.SY', $SY); // Filter by SY
		return $this->db->get()->row(); // Return a single row
	}


	public function deleteRegistration($studentNumber, $sy)
	{
		$loggedInUser = $this->session->userdata('username');
		date_default_timezone_set('Asia/Manila');

		$this->db->where('StudentNumber', $studentNumber);
		$this->db->where('SY', $sy);

		$deleteResult = $this->db->delete('registration');

		$logData = [
			'atDesc' => $deleteResult ?
				'Deleted registration for student number ' . $studentNumber . ' in SY ' . $sy :
				'Failed to delete registration for student number ' . $studentNumber . ' in SY ' . $sy,
			'atDate' => date('Y-m-d'),
			'atTime' => date('H:i:s A'),
			'atRes' => $loggedInUser,
			'atSNo' => $studentNumber
		];

		$this->db->insert('atrail', $logData);

		return $deleteResult;
	}






	public function insertstudeAccount1($data)
	{
		return $this->db->insert('studeaccount', $data);
	}
	public function insertIntoStudeAdditional($data)
	{
		return $this->db->insert('studeadditional', $data);
	}






	public function deleteStudentAccount($StudentNumber, $SY)
	{
		// Ensure deletion only affects the current SY and the given StudentNumber
		$this->db->where('StudentNumber', $StudentNumber);
		$this->db->where('SY', $SY); // Filter by SY

		// Attempt to delete the record
		return $this->db->delete('studeaccount'); // Return true/false based on success
	}


	public function getAccountDetailsByStudentNumber($studentNumber)
	{
		// Join the studeaccount table with the studeprofile table
		$this->db->select('studeaccount.*, studeprofile.FirstName, studeprofile.MiddleName, studeprofile.LastName');
		$this->db->from('studeaccount');
		$this->db->join('studeprofile', 'studeprofile.StudentNumber = studeaccount.StudentNumber');
		$this->db->where('studeaccount.StudentNumber', $studentNumber); // Use StudentNumber to fetch account details
		return $this->db->get()->row(); // Return a single row
	}


	public function getStudentProfileByNumber($studentNumber)
	{
		$this->db->where('StudentNumber', $studentNumber);
		return $this->db->get('studeprofile')->row(); // Returns a single row
	}


	public function updateStudentAccount1($studentNumber, $data)
	{
		$this->db->where('StudentNumber', $studentNumber);
		return $this->db->update('studeaccount', $data);
	}

	// public function getTotalFeesAmount($studentNumber) {
	//     $this->db->select_sum('FeesAmount');
	//     $this->db->where('StudentNumber', $studentNumber);
	//     $result = $this->db->get('studeaccount')->row();

	//     return $result->FeesAmount ?? 0; // Default to 0 if no fees found
	// }


	public function getTotalFeesAmount($studentNumber, $SY)
	{
		$this->db->select_sum('FeesAmount');
		$this->db->where('StudentNumber', $studentNumber);
		$this->db->where('SY', $SY); // Filter by current school year
		$result = $this->db->get('studeaccount')->row();

		return $result->FeesAmount ?? 0; // Default to 0 if no fees found
	}







	public function checkExistingAccount($studentNumber, $currentSY)
	{
		$this->db->where('StudentNumber', $studentNumber);
		$this->db->where('SY', $currentSY);  // Check against the current School Year
		$query = $this->db->get('studeaccount'); // Replace with your actual table name
		return $query->num_rows() > 0;
	}





	public function getStudentDetails($studentNumber)
	{
		$this->db->where('StudentNumber', $studentNumber);
		$query = $this->db->get('semesterstude'); // Replace 'students' with your actual table name
		return $query->row();
	}

	public function getAmountPaid($studentNumber, $currentSY)
	{
		$this->db->select_sum('Amount'); // Assuming 'Amount' is the column name for payment amount
		$this->db->where('StudentNumber', $studentNumber);
		$this->db->where('SY', $currentSY);  // Ensure it matches the current SY
		$this->db->where('ORStatus !=', 'Void'); // Tyrone
		$this->db->where('CollectionSource !=', 'Services'); // Tyrone

		$query = $this->db->get('paymentsaccounts'); // Replace with your actual payments table name
		return $query->row()->Amount ?? 0; // Return the sum or 0 if no payments found
	}



	public function getTotalPayments($studentNumber, $currentSY)
	{
		$this->db->select_sum('Amount');  // Summing the payments for the student
		$this->db->where('StudentNumber', $studentNumber);
		$this->db->where('SY', $currentSY);  // Ensure SY matches
		$query = $this->db->get('paymentsaccounts');  // Replace with your actual table name

		return $query->row()->Amount ?? 0;  // Return the total amount or 0 if no records found
	}









	public function getDescriptionsByYearLevel($yearLevel)
	{
		$this->db->where('YearLevel', $yearLevel);
		$query = $this->db->get('fees');  // Assuming the fees are stored in a table named 'fees'
		return $query->result();
	}



	// In StudentModel.php
	public function getFilteredPayments($fromDate, $toDate)
	{
		$this->db->where('PDate >=', $fromDate);
		$this->db->where('PDate <=', $toDate);
		return $this->db->get('your_payments_table')->result(); // replace with your actual table name
	}

	public function getFilteredFees($fromDate, $toDate)
	{
		$this->db->select('description, SUM(Amount) as total_amount'); // Adjust the select fields as needed
		$this->db->where('PDate >=', $fromDate);
		$this->db->where('PDate <=', $toDate);
		$this->db->group_by('description');
		return $this->db->get('your_fees_table')->result_array(); // replace with your actual table name
	}












	public function studeAccountsWithBalance($sy, $course, $yearlevel)
	{
		$this->db->select("sa.AccountID, 
                       sa.StudentNumber, 
                       CONCAT(sp.LastName, ', ', sp.FirstName, ' ', sp.MiddleName) AS StudentName, 
                       sa.Course, 
                       FORMAT(sa.AcctTotal, 2) AS AcctTotal, 
                       FORMAT(sa.TotalPayments, 2) AS TotalPayments, 
                       FORMAT(sa.Discount, 2) AS Discount, 
                       FORMAT(sa.CurrentBalance, 2) AS CurrentBalance, 
                       sa.YearLevel, 
                       sa.Sem, 
                       sa.SY")
			->from("studeaccount sa")
			->join("studeprofile sp", "sp.StudentNumber = sa.StudentNumber")
			->where([
				"sa.SY" => $sy,
				"sa.YearLevel" => $yearlevel,
				"sa.Course" => $course,
				"sa.CurrentBalance >" => 0
			])
			->group_by("sa.StudentNumber")
			->order_by("StudentName", "ASC");

		return $this->db->get()->result();
	}


	function studentStatement($id, $sy)
	{
		$this->db->select('*');
		$this->db->from('studeaccount');
		$this->db->join('studeprofile', 'studeaccount.StudentNumber = studeprofile.StudentNumber');
		$this->db->where('studeaccount.StudentNumber', $id);
		$this->db->where('studeaccount.SY', $sy);
		$this->db->order_by('studeaccount.FeesDesc', 'ASC');
		$query = $this->db->get();

		return $query->result();
	}

	public function userAccounts()
	{
		$query = $this->db->get('o_users');
		return $query->result();
	}

	public function collectionSummaryAll()
	{
		// Use Query Builder to construct the query
		$this->db->select('PaymentType');
		$this->db->select('FORMAT(SUM(Amount), 2) AS TotalAmount', false); // Use FORMAT function to format the sum
		$this->db->from('paymentsaccounts');
		$this->db->where('ORStatus', 'Valid');
		$this->db->group_by('PaymentType');

		// Execute the query
		$query = $this->db->get();

		// Return the result
		return $query->result();
	}

	public function collectionTotalYear($year)
	{
		// Load the database library if not already loaded
		$this->load->database();

		// Use Query Builder to construct the query
		$this->db->select_sum('Amount', 'TotalAmount');
		$this->db->from('paymentsaccounts');
		$this->db->where('YEAR(PDate)', $year);
		$this->db->where('ORStatus', 'Valid');
		$this->db->order_by('PDate', 'desc');

		// Execute the query
		$query = $this->db->get();

		// Return the result
		return $query->result();
	}

	//Masterlist by Date
	public function byDate($date)
	{

		// Using Query Builder to construct the query
		$this->db->select('*');
		$this->db->from('studeprofile p');
		$this->db->join('semesterstude s', 'p.StudentNumber = s.StudentNumber');
		$this->db->where('s.EnrolledDate', $date);
		$this->db->where('s.Status', 'Enrolled');
		$this->db->group_by('p.StudentNumber');
		$this->db->order_by('p.LastName');

		// Execute the query
		$query = $this->db->get();

		// Return the result
		return $query->result();
	}










	function medInfo_stude($id)
	{
		$query = $this->db->query("SELECT * FROM medical_info m join studeprofile p on m.StudentNumber=p.StudentNumber where p.StudentNumber='" . $id . "'");
		return $query->result();
	}

	function medInfoInd($id)
	{
		$query = $this->db->query("SELECT * FROM medical_info m join studeprofile p on m.StudentNumber=p.StudentNumber where medID='" . $id . "'");
		return $query->result();
	}

	function incidents()
	{
		$query = $this->db->query("SELECT * FROM guidance_incidents m join studeprofile p on m.StudentNumber=p.StudentNumber");
		return $query->result();
	}

	function incidents_stude($id)
	{
		$query = $this->db->query("SELECT * FROM guidance_incidents m join studeprofile p on m.StudentNumber=p.StudentNumber where p.StudentNumber='" . $id . "'");
		return $query->result();
	}

	function incidentsInd($id)
	{
		$query = $this->db->query("SELECT * FROM guidance_incidents m join studeprofile p on m.StudentNumber=p.StudentNumber where incID='" . $id . "'");
		return $query->result();
	}

	function counselling()
	{
		$query = $this->db->query("SELECT * FROM guidance_counselling m join studeprofile p on m.StudentNumber=p.StudentNumber");
		return $query->result();
	}

	function counselling_stude($id)
	{
		$query = $this->db->query("SELECT * FROM guidance_counselling m join studeprofile p on m.StudentNumber=p.StudentNumber where p.StudentNumber='" . $id . "'");
		return $query->result();
	}

	function counsellingInd($id)
	{
		$query = $this->db->query("SELECT * FROM guidance_counselling m join studeprofile p on m.StudentNumber=p.StudentNumber where id='" . $id . "'");
		return $query->result();
	}

	function medRecords()
	{
		$query = $this->db->query("SELECT * FROM medical_records m join studeprofile p on m.StudentNumber=p.StudentNumber");
		return $query->result();
	}

	function medRecords_stude($id)
	{
		$query = $this->db->query("SELECT * FROM medical_records m join studeprofile p on m.StudentNumber=p.StudentNumber where p.StudentNumber='" . $id . "'");
		return $query->result();
	}


	function medRecordsInd($id)
	{
		$query = $this->db->query("SELECT * FROM medical_records m join studeprofile p on m.StudentNumber=p.StudentNumber where mrID='" . $id . "'");
		return $query->result();
	}

	public function searchStudents()
	{
		// $this->db->select('*');
		$this->db->select('StudentNumber, FirstName, MiddleName, LastName');
		$this->db->from('studeprofile');
		$this->db->order_by('LastName');
		$query = $this->db->get();
		return $query->result();
	}

	//Incidents
	function incidentsCounts()
	{
		$query = $this->db->query("SELECT count(incID) as StudeCount FROM guidance_incidents");
		return $query->result();
	}

	//counselling
	function counsellingCounts()
	{
		$query = $this->db->query("SELECT count(id) as StudeCount FROM guidance_counselling");
		return $query->result();
	}

	//medicalInfo
	function medInfoCounts()
	{
		$query = $this->db->query("SELECT count(medID) as StudeCount FROM medical_info");
		return $query->result();
	}

	//medicalRecords
	function medRecordsCounts()
	{
		$query = $this->db->query("SELECT count(mrID) as StudeCount FROM medical_records");
		return $query->result();
	}




	//VIEW REQUIREMENTS ---------------------------------------------------------------------------------
	function requirements($id)
	{
		$query = $this->db->query("Select * from online_requirements where StudentNumber='" . $id . "' order by fileAttachment");
		return $query->result();
	}

	//STUDENTS REQUEST ---------------------------------------------------------------------------------
	function studerequest($id)
	{
		$query = $this->db->query("SELECT * FROM stude_request where StudentNumber='" . $id . "'");
		return $query->result();
	}

	function studerequestTracking($id)
	{
		$query = $this->db->query("SELECT * FROM stude_request sr join stude_request_stat st on sr.trackingNo=st.trackingNo where sr.trackingNo='" . $id . "' order by statID desc");
		return $query->result();
	}

	function studeaccountById($id)
	{
		$query = $this->db->query("Select s.Course, Sem, SY, concat(Sem,', ',SY) as Semester, Format(AcctTotal,2) as AcctTotal, Format(TotalPayments,2) as TotalPayments, Format(CurrentBalance,2) as CurrentBalance, Discount, s.StudentNumber, p.FirstName, p.MiddleName, p.LastName from studeaccount s join studeprofile p on s.StudentNumber=p.StudentNumber where s.StudentNumber='" . $id . "' group by Semester order by AccountID desc, Sem");
		return $query->result();
	}

	// function studepayments($studentno,$sem,$sy){
	// $query=$this->db->query("SELECT p.StudentNumber, concat(p.FirstName,' ',p.LastName) as StudentName, s.Course, s.PDate, s.ORNumber, Format(s.Amount,2) as Amount, s.description, s.Sem, s.SY FROM paymentsaccounts s join studeprofile p on p.StudentNumber=s.StudentNumber where p.StudentNumber='".$studentno."' and s.Sem='".$sem."' and s.SY='".$sy."' and s.CollectionSource!='Services' and s.ORStatus='Valid'");
	// return $query->result();
	// }

	function studepayments($studentno, $sem, $sy)
	{
		$this->db->select("p.StudentNumber, CONCAT(p.FirstName, ' ', p.LastName) AS StudentName, s.Course, s.PDate, s.ORNumber, FORMAT(s.Amount, 2) AS Amount, s.description, s.Sem, s.SY");
		$this->db->from('paymentsaccounts s');
		$this->db->join('studeprofile p', 'p.StudentNumber = s.StudentNumber');
		$this->db->where('p.StudentNumber', $studentno);
		$this->db->where('s.Sem', $sem);
		$this->db->where('s.SY', $sy);
		$this->db->where('s.CollectionSource !=', 'Services');
		$this->db->where('s.ORStatus', 'Valid');

		$query = $this->db->get();
		return $query->result();
	}



	//Student Grades
	function studeGrades($studeno, $sy)
	{
		$query = $this->db->query("SELECT * FROM studeprofile s join grades g on s.StudentNumber=g.StudentNumber where s.StudentNumber='" . $studeno . "' and g.SY='" . $sy . "' group by g.Sem, SubjectCode order by SubjectCode");
		return $query->result();
	}

	function getTrackingNo()
	{
		$query = $this->db->query("select * from stude_request order by trackingNo desc limit 1");
		return $query->result();
	}

	//Student COR
	function studeCOR($studeno, $sy)
	{
		$query = $this->db->query("SELECT * FROM studeprofile s join registration r on s.StudentNumber=r.StudentNumber where s.StudentNumber='" . $studeno . "' and r.SY='" . $sy . "'");
		return $query->result();
	}

	//FTE Records
	function fteRecords($sem, $sy, $course, $yearlevel)
	{
		$query = $this->db->query("SELECT LastName, FirstName, MiddleName, Sem, SY, Course, Major, YearLevel, sum(LecUnit) as LecUnit, sum(LabUnit) as LabUnit FROM registration where Sem='" . $sem . "' and SY='" . $sy . "' and Course='" . $course . "' and YearLevel='" . $yearlevel . "' group by StudentNumber order by LastName");
		return $query->result();
	}


	//Display Students Profile
	function displayrecordsById($id)
	{
		$this->db->where('StudentNumber', $id);
		$query = $this->db->get('studeprofile');

		return $query->result();
	}


	function isEnrolled($studentNumber)
	{
		$this->db->where('StudentNumber', $studentNumber);
		$this->db->where('StudeStatus', 'Enrolled');
		$query = $this->db->get('semesterstude');
		return ($query->num_rows() > 0);
	}


	// public function displayrecordsById($StudentNumber)
	// {
	//     $query = $this->db->query("SELECT * FROM studeprofile WHERE StudentNumber = '" . $StudentNumber . "'");
	//     return $query->result();
	// }

	//Display Staff Profile
	function staffProfile($id)
	{
		$query = $this->db->query("select * from staff where IDNumber='" . $id . "'");
		return $query->result();
	}

	function getOR()
	{
		$query = $this->db->query("select * from paymentsaccounts order by ID desc limit 1");
		return $query->result();
	}

	function UploadedPayments($id)
	{
		$query = $this->db->query("select * from online_payments where StudentNumber='" . $id . "'");
		return $query->result();
	}

	function UploadedPaymentsAdmin($sy)
	{
		$query = $this->db->query("SELECT * FROM online_payments o join studeprofile p on o.StudentNumber=p.StudentNumber where o.depositStat='For Verification' and o.sy='" . $sy . "'");
		return $query->result();
	}

	function onlinePaymentsAll()
	{
		$query = $this->db->query("select * from online_payments op join studeprofile p on op.StudentNumber=p.StudentNumber join online_enrollment oe on p.StudentNumber=oe.StudentNumber group by op.opID");
		return $query->result();
	}

	function displayenrollees()
	{
		$query = $this->db->query("select * from online_enrollment order by LastName");
		return $query->result();
	}

	//Chart of Enrollment
	function chartEnrollment()
	{
		$query = $this->db->query("SELECT concat(Semester,', ',SY) as Sem, count(Semester) as Counts FROM semesterstude group by Sem");
		return $query->result();
	}

	//Counts of Teachers
	function teachersCount()
	{
		$query = $this->db->query("SELECT count(IDNumber) as staffCount FROM staff");
		return $query->result();
	}

	//Counts for Validation
	function forValidationCounts($SY)
	{
		$query = $this->db->query("SELECT count(StudentNumber) as StudeCount FROM online_enrollment where SY='" . $SY . "' and enrolStatus='For Validation'");
		return $query->result();
	}

	//Counts for Total Profile
	function totalProfile()
	{
		$query = $this->db->query("SELECT count(StudentNumber) as StudeCount FROM studeprofile");
		return $query->result();
	}


	//For payment verification count
	function forPaymentVerCount($sy)
	{
		$query = $this->db->query("SELECT count(StudentNumber) as Studecount FROM online_payments where sy='" . $sy . "' and depositStat='For Verification'");
		return $query->result();
	}

	//First Year Counts
	function enrolledFirst($sy, $sem)
	{
		$query = $this->db->query("SELECT count(StudentNumber) as StudeCount, SY, Semester, YearLevel, Course FROM semesterstude where SY='" . $sy . "' and Semester='" . $sem . "' and Status='Enrolled' and YearLevel='1st'");
		return $query->result();
	}

	//Second Year Counts
	function enrolledSecond($sy, $sem)
	{
		$query = $this->db->query("SELECT count(StudentNumber) as StudeCount, SY, Semester, YearLevel, Course FROM semesterstude where SY='" . $sy . "' and Semester='" . $sem . "' and Status='Enrolled' and YearLevel='2nd'");
		return $query->result();
	}

	//Third Year Counts
	function enrolledThird($sy, $sem)
	{
		$query = $this->db->query("SELECT count(StudentNumber) as StudeCount, SY, Semester, YearLevel, Course FROM semesterstude where SY='" . $sy . "' and Semester='" . $sem . "' and Status='Enrolled' and YearLevel='3rd'");
		return $query->result();
	}

	//Fourth Year Counts
	function enrolledFourth($sy, $sem)
	{
		$query = $this->db->query("SELECT count(StudentNumber) as StudeCount, SY, Semester, YearLevel, Course FROM semesterstude where SY='" . $sy . "' and Semester='" . $sem . "' and Status='Enrolled' and YearLevel='4th'");
		return $query->result();
	}

	//Semester Enrollees
	function getEnrolled($course, $yearlevel)
	{
		$this->db->select('*');
		if ($course)
			$this->db->where('Course', $course);
		if ($yearlevel)
			$this->db->where('YearLevel', $yearlevel);
		$query = $this->db->get('semesterstude');
		return $query->result();
	}

	//JHS Counts
	function enrolledJHS($sy)
	{
		$query = $this->db->query("SELECT count(StudentNumber) as StudeCount, SY, Course FROM semesterstude where SY='" . $sy . "' and Status='Enrolled' and Course='Junior High School'");
		return $query->result();
	}


	public function enrolledSHS($sy)
	{
		// Use query bindings to prevent SQL injection
		$this->db->select('count(StudentNumber) as StudeCount, SY, Course, Semester');
		$this->db->from('semesterstude');
		$this->db->where('SY', $sy);
		$this->db->where('Status', 'Enrolled');
		$this->db->where('Course', 'Senior High School');

		$query = $this->db->get();
		return $query->result();
	}

	public function preschool($sy)
	{
		// Use query bindings to prevent SQL injection
		$this->db->select('count(StudentNumber) as StudeCount, SY, Course, Semester');
		$this->db->from('semesterstude');
		$this->db->where('SY', $sy);
		$this->db->where('Status', 'Enrolled');
		$this->db->where('Course', 'Preschool');

		$query = $this->db->get();
		return $query->result();
	}

	public function byDepartment($department, $sy)
	{
		$this->db->select('*');
		$this->db->from('semesterstude ss');
		$this->db->join('studeprofile p', 'ss.StudentNumber = p.StudentNumber');
		$this->db->where('ss.SY', $sy);
		$this->db->where('ss.Status', 'Enrolled');
		$this->db->where('ss.Course', $department);
		$this->db->order_by('p.LastName'); // Assuming LastName is in studeprofile

		$query = $this->db->get();
		return $query->result();
	}
















	//Elementary Counts
	function enrolledElem($sy)
	{
		$query = $this->db->query("SELECT count(StudentNumber) as StudeCount, SY, Course FROM semesterstude where SY='" . $sy . "' and Status='Enrolled' and Course='Elementary'");
		return $query->result();
	}

	//Online Enrollees Count
	function onlineEnrollees($sy)
	{
		$query = $this->db->query("SELECT count(StudentNumber) as StudeCount, SY FROM online_enrollment where SY='" . $sy . "' and EnrolStatus='Enrolled'");
		return $query->result();
	}



	//Masterlist by Department
	function byDepartmentSHS($department, $sy)
	{
		$query = $this->db->query("select * from studeprofile p join semesterstude s on p.StudentNumber=s.StudentNumber where s.SY='" . $sy . "' and s.Semester='1st Sem.' and s.Status='Enrolled' and s.Course='" . $department . "' group by p.StudentNumber order by p.LastName, p.Sex");
		return $query->result();
	}

	//Masterlist by Department
	function byDepartmentSHS2($department, $sy)
	{
		$query = $this->db->query("select * from studeprofile p join semesterstude s on p.StudentNumber=s.StudentNumber where s.SY='" . $sy . "' and s.Semester='2nd Sem.' and s.Status='Enrolled' and s.Course='" . $department . "' group by p.StudentNumber order by p.LastName, p.Sex");
		return $query->result();
	}

	//Masterlist by Section Count
	function bySectionCount($department, $sy)
	{
		$query = $this->db->query("SELECT Section, count(Section) as SectionCount, Semester, SY FROM semesterstude where SY='" . $sy . "' and Status='Enrolled' and Course='" . $department . "' and Status='Enrolled' group by Section");
		return $query->result();
	}

	//Course Count Summary Per Semester
	function dailyEnrollStat()
	{
		$query = $this->db->query("SELECT Status, count(Status)as Counts FROM semesterstude where DAY(EnrolledDate)=DAY(NOW()) and MONTH(EnrolledDate)=MONTH(NOW()) and YEAR(EnrolledDate)=YEAR(NOW()) group by Status");
		return $query->result();
	}
	//Payment Summary Per Semester
	function paymentSummary($sy)
	{
		$query = $this->db->query("SELECT CollectionSource, sum(Amount) as Amount FROM paymentsaccounts where ORStatus='Valid' and SY='" . $sy . "' group by CollectionSource");
		return $query->result();
	}
	//Birthday Celebrants
	function birthdayCelebs($sem, $sy)
	{
		$query = $this->db->query("SELECT concat(p.LastName,', ',p.FirstName,' ',p.MiddleName) as StudeName, p.BirthDate FROM studeprofile p join semesterstude ss on p.StudentNumber=ss.StudentNumber where DAY(p.BirthDate)=DAY(NOW()) and MONTH(p.BirthDate)=MONTH(NOW()) and ss.Semester='" . $sem . "' and ss.SY='" . $sy . "'");
		return $query->result();
	}
	//Birthday Celebrants
	function birthdayMonths($sem, $sy)
	{
		$query = $this->db->query("SELECT concat(p.LastName,', ',p.FirstName,' ',p.MiddleName) as StudeName, Day(p.BirthDate) as Day, MONTH(p.BirthDate) as Month FROM studeprofile p join semesterstude ss on p.StudentNumber=ss.StudentNumber where MONTH(p.BirthDate)=MONTH(NOW()) and ss.Semester='" . $sem . "' and ss.SY='" . $sy . "' order by Day");
		return $query->result();
	}

	//Quick Today's Collection
	function collectionToday()
	{
		$query = $this->db->query("SELECT sum(Amount) as Amount FROM paymentsaccounts where ORStatus='Valid' and DAY(PDate)=DAY(NOW()) and MONTH(PDate)=MONTH(NOW()) and YEAR(PDate)=YEAR(NOW())");
		return $query->result();
	}
	//Quick This Month's Collection
	function collectionMonth()
	{
		$query = $this->db->query("SELECT sum(Amount) as Amount FROM paymentsaccounts where ORStatus='Valid' and MONTH(PDate)=MONTH(NOW()) and YEAR(PDate)=YEAR(NOW())");
		return $query->result();
	}
	//Quick This Year's Collection
	function YearlyCollections()
	{
		$query = $this->db->query("SELECT sum(Amount) as Amount FROM paymentsaccounts where ORStatus='Valid' and YEAR(PDate)=YEAR(NOW())");
		return $query->result();
	}
	//Course Count Summary Per Semester
	function CourseCount($sy)
	{
		$query = $this->db->query("SELECT Course, count(Course) as Counts FROM semesterstude where SY='" . $sy . "' group by Course");
		return $query->result();
	}

	//Sex Count Summary Per Semester
	function SexCount($section, $semester, $sy)
	{
		$query = $this->db->query("select p.Sex, count(p.Sex) as sexCount from studeprofile p join semesterstude s on p.StudentNumber=s.StudentNumber where s.Section='" . $section . "' and s.Semester='" . $semester . "' and s.SY='" . $sy . "' and s.Status='Enrolled' group by p.Sex");
		return $query->result();
	}



	//Masterlist by Track
	function byTrack($sy, $trackVal, $strandVal, $gradelevel)
	{
		$query = $this->db->query("select p.StudentNumber, concat(p.LastName,', ',p.FirstName,' ',p.MiddleName) as StudeName, ss.YearLevel, ss.Track, ss.Qualification, ss.Section, ss.Adviser from studeprofile p join semesterstude ss on p.StudentNumber=ss.StudentNumber where ss.SY='" . $sy . "' and ss.Track='" . $trackVal . "' and ss.Qualification='" . $strandVal . "' and ss.YearLevel='" . $gradelevel . "' and ss.Status='Enrolled' group by ss.StudentNumber order by p.LastName");
		return $query->result();
	}

	//Sex Summary
	function sexList($sy, $sex)
	{
		$query = $this->db->query("SELECT p.StudentNumber, p.FirstName, p.MiddleName, p.LastName, ss.Course, ss.YearLevel, p.Sex FROM studeprofile p join semesterstude ss on p.StudentNumber=ss.StudentNumber where ss.SY='" . $sy . "' and p.Sex='" . $sex . "'");
		return $query->result();
	}

	//City List Summary
	function cityList($sy, $city)
	{
		$query = $this->db->query("SELECT p.StudentNumber, p.FirstName, p.MiddleName, p.LastName, ss.Course, ss.YearLevel, p.city FROM studeprofile p join semesterstude ss on p.StudentNumber=ss.StudentNumber where ss.SY='" . $sy . "' and p.city='" . $city . "' order by p.LastName");
		return $query->result();
	}

	//Ethnicity List Summary
	function ethnicityList($sy, $ethnicity)
	{
		$query = $this->db->query("SELECT p.StudentNumber, p.FirstName, p.MiddleName, p.LastName, ss.Course, ss.YearLevel, p.ethnicity FROM studeprofile p join semesterstude ss on p.StudentNumber=ss.StudentNumber where ss.SY='" . $sy . "' and p.ethnicity='" . $ethnicity . "' order by p.LastName");
		return $query->result();
	}

	//Religion List Summary
	function religionList($sy, $religion)
	{
		$query = $this->db->query("SELECT p.StudentNumber, p.FirstName, p.MiddleName, p.LastName, ss.Course, ss.YearLevel, p.Religion FROM studeprofile p join semesterstude ss on p.StudentNumber=ss.StudentNumber where ss.SY='" . $sy . "' and p.Religion='" . $religion . "' order by p.LastName");
		return $query->result();
	}
	//Religion Count by Section
	function ReligionCount($section, $semester, $sy)
	{
		$query = $this->db->query("select p.Religion, count(p.Religion) as religionCount from studeprofile p join semesterstude s on p.StudentNumber=s.StudentNumber where s.Section='" . $section . "' and s.Semester='" . $semester . "' and s.SY='" . $sy . "' and s.Status='Enrolled' group by p.Religion order by p.Religion");
		return $query->result();
	}

	//Religion Count by Section
	function ReligionCount1($sy)
	{
		$query = $this->db->query("select p.Religion, count(p.Religion) as religionCount from studeprofile p join semesterstude s on p.StudentNumber=s.StudentNumber where s.SY='" . $sy . "' and s.Status='Enrolled' group by p.Religion order by p.Religion");
		return $query->result();
	}

	//Count by Ethnicity
	function ethnicityCount($sy)
	{
		$query = $this->db->query("SELECT p.Ethnicity, count(p.Ethnicity) as Counts FROM studeprofile p join semesterstude ss on p.StudentNumber=ss.StudentNumber where ss.SY='" . $sy . "' group by p.Ethnicity");
		return $query->result();
	}
	//Count by City
	function cityCount($sy)
	{
		$query = $this->db->query("SELECT p.city, count(p.city) as Counts FROM studeprofile p join semesterstude ss on p.StudentNumber=ss.StudentNumber where ss.SY='" . $sy . "' group by p.city");
		return $query->result();
	}
	//Student's List
	function getProfile()
	{
		$loggedInUsername = $this->session->userdata('username');

		$this->db->select('*');
		$this->db->from('studeprofile');
		$this->db->where('schoolID', $loggedInUsername);
		$this->db->order_by('LastName');

		$query = $this->db->get();
		return $query->result();
	}

	function getProfile_by_adviser()
	{
		$loggedInUsername = $this->session->userdata('username');

		$this->db->select('*');
		$this->db->from('studeprofile');
		$this->db->where('Encoder', $loggedInUsername);
		$this->db->order_by('LastName');

		$query = $this->db->get();
		return $query->result();
	}

	//Student's List
	function teachers()
	{
		$query = $this->db->query("select * from staff order by LastName");
		return $query->result();
	}

	//For Enrollment
	function forValidation($SY)
	{
		$query = $this->db->query("select * from studeprofile p join online_enrollment oe on p.StudentNumber=oe.StudentNumber where oe.SY='" . $SY . "' and oe.enrolStatus='For Validation'");
		return $query->result();
	}

	//get the latest semester and reflect it on the proof_payment
	function getSemesterfromOE($id)
	{
		$query = $this->db->query("select * from online_enrollment where StudentNumber='" . $id . "' order by oeID desc limit 1");
		return $query->result();
	}

	//Slot Monitoring
	function slotsMonitoring($sy)
	{
		$query = $this->db->query("select r.SubjectCode, r.Description, count(*) as Enrolled, r.Section, r.SchedTime, r.Instructor, r.Sem, r.SY from registration r where r.SY='" . $sy . "' group by r.SubjectCode, r.Section, r.Instructor, r.SchedTime order by r.SubjectCode");
		return $query->result();
	}

	//Subject Masterlist
	public function subjectMasterlist($sy, $subjectcode, $section)
	{
		$this->db->select('*');
		$this->db->from('registration r');
		$this->db->join('studeprofile p', 'r.StudentNumber = p.StudentNumber');
		$this->db->where('SY', $sy);
		$this->db->where('Section', $section);
		$this->db->where('subjectcode', $subjectcode);
		$this->db->group_by('p.StudentNumber');
		$this->db->order_by('LastName');

		$query = $this->db->get();
		return $query->result();
	}


	//Student REQUEST
	function studeRequestList()
	{
		$query = $this->db->query("select * from stude_request sr join studeprofile p on sr.StudentNumber=p.StudentNumber where sr.reqStat='Open' order by sr.dateReq desc");
		return $query->result();
	}
	//Student REQUEST
	function closedDocRequest()
	{
		$query = $this->db->query("select * from stude_request sr join studeprofile p on sr.StudentNumber=p.StudentNumber where sr.reqStat='Closed' order by sr.dateReq desc");
		return $query->result();
	}

	function openRequest()
	{
		$query = $this->db->query("select * from stude_request sr join studeprofile p on sr.StudentNumber=p.StudentNumber where sr.reqStat='Open' order by sr.dateReq desc");
		return $query->result();
	}

	//Grade
	function grades($sy)
	{
		$query = $this->db->query("select * from grades where SY='" . $sy . "' group by SubjectCode, Section, Instructor order by SubjectCode");
		return $query->result();
	}
	//Grading Sheets
	function gradeSheets($sy, $SubjectCode, $Description, $Section)
	{
		$query = $this->db->query("select * from grades g join studeprofile p on g.StudentNumber=p.StudentNumber where g.SY='" . $sy . "' and g.SubjectCode='" . $SubjectCode . "' and g.Section='" . $Section . "' order by p.LastName");
		return $query->result();
	}
	//CrossEnrollees
	function crossEnrollees($sem, $sy)
	{
		$query = $this->db->query("select concat(p.LastName,', ',p.FirstName,' ',p.MiddleName) as StudentName, ss.YearLevel, p.Sex, ss.Course, ss.classSession, ss.Semester, ss.SY  from studeprofile p join semesterstude ss on p.StudentNumber=ss.StudentNumber where ss.Status='Enrolled' and ss.crossEnrollee='Yes' and ss.Semester='" . $sem . "' and ss.SY='" . $sy . "' order by ss.LName");
		return $query->result();
	}

	//Admission History
	function admissionHistory($id)
	{
		// Load the database library if not already loaded
		$this->load->database();

		// Use query bindings to prevent SQL injection
		$query = $this->db->query("SELECT p.StudentNumber, CONCAT(p.FirstName, ' ', p.MiddleName, ' ', p.LastName) AS StudentName, s.Course, s.YearLevel, s.SY, s.Semester 
                                FROM studeprofile p 
                                JOIN semesterstude s ON p.StudentNumber = s.StudentNumber 
                                JOIN srms_settings_o st ON p.settingsID = st.settingsID 
                                WHERE p.StudentNumber = ?", array($id));

		return $query->result();
	}

	function studeRequestSum($id)
	{
		$query = $this->db->query("select p.StudentNumber, concat(p.FirstName,' ',p.MiddleName,' ',p.LastName) as StudentName, s.trackingNo, s.docName, s.dateReq, s.timeReq, s.reqStat  from studeprofile p join stude_request s on p.StudentNumber=s.StudentNumber where p.StudentNumber='" . $id . "'");
		return $query->result();
	}

	//Get Course and Display on the combo box
	function getCourse()
	{
		$this->db->select('CourseDescription');
		$this->db->distinct();
		$this->db->order_by('CourseDescription', 'ASC');
		$query = $this->db->get('course_table');
		return $query->result();
	}

	//Get Course and Display on the combo box
	function getLevel()
	{
		$this->db->select('Major');
		$this->db->distinct();
		$this->db->order_by('Major', 'ASC');
		$query = $this->db->get('course_table');
		return $query->result();
	}

	public function getLevelsByCourse($course)
	{
		$this->db->select('Major');
		$this->db->distinct();
		$this->db->where('CourseDescription', $course);  // Ensure you're querying by the correct column
		$query = $this->db->get('course_table');
		return $query->result();
	}



	// Get distinct provinces from the settings_address table
	public function get_course_table()
	{
		// Select distinct provinces
		$this->db->distinct();
		$this->db->select('CourseDescription');
		// Order the provinces by name in ascending order
		$this->db->order_by('CourseDescription', 'ASC');
		$query = $this->db->get('course_table');
		$CourseDescription = $query->result_array();

		// Format the data for dropdown
		$formatted_CourseDescription = [];
		foreach ($CourseDescription as $CourseDescription) {
			$formatted_CourseDescription[] = [
				'id' => $CourseDescription['CourseDescription'],
				'name' => $CourseDescription['CourseDescription']
			];
		}

		return $formatted_CourseDescription;
	}





	function bySignup()
	{
		$query = $this->db->query("SELECT * FROM studentsignup ss join online_enrollment oe on ss.StudentNumber=oe.StudentNumber order by ss.LastName");
		return $query->result();
	}

	//Masterlist 4Ps Count
	function FourPsCount($section, $semester, $sy)
	{
		$query = $this->db->query("select p.Sex, count(p.Sex) as sexCount from studeprofile p join semesterstude s on p.StudentNumber=s.StudentNumber where s.Section='" . $section . "' and s.Semester='" . $semester . "' and s.SY='" . $sy . "' and s.Status='Enrolled' and s.FourPs='Yes' group by p.Sex");
		return $query->result();
	}

	function BalikAral($section, $semester, $sy)
	{
		$query = $this->db->query("select p.Sex, count(p.Sex) as sexCount from studeprofile p join semesterstude s on p.StudentNumber=s.StudentNumber where s.Section='" . $section . "' and s.Semester='" . $semester . "' and s.SY='" . $sy . "' and s.Status='Enrolled' and s.BalikAral='Yes' group by p.Sex");
		return $query->result();
	}

	//IP Count
	function IPCounts($section, $semester, $sy)
	{
		$query = $this->db->query("select p.Sex, count(p.Sex) as sexCount from studeprofile p join semesterstude s on p.StudentNumber=s.StudentNumber where s.Section='" . $section . "' and s.Semester='" . $semester . "' and s.SY='" . $sy . "' and s.Status='Enrolled' and s.IP='Yes' group by p.Sex");
		return $query->result();
	}
	//Repeater Count
	function RepeaterCounts($section, $semester, $sy)
	{
		$query = $this->db->query("select p.Sex, count(p.Sex) as sexCount from studeprofile p join semesterstude s on p.StudentNumber=s.StudentNumber where s.Section='" . $section . "' and s.Semester='" . $semester . "' and s.SY='" . $sy . "' and s.Status='Enrolled' and s.Repeater='Yes' group by p.Sex");
		return $query->result();
	}
	//Transferee Count
	function TransfereeCounts($section, $semester, $sy)
	{
		$query = $this->db->query("select p.Sex, count(p.Sex) as sexCount from studeprofile p join semesterstude s on p.StudentNumber=s.StudentNumber where s.Section='" . $section . "' and s.Semester='" . $semester . "' and s.SY='" . $sy . "' and s.Status='Enrolled' and s.Transferee='Yes' group by p.Sex");
		return $query->result();
	}

	//Sitio Count by Section
	function SitioCount($section, $semester, $sy)
	{
		$query = $this->db->query("select p.Sitio, count(p.Sitio) as sitioCount from studeprofile p join semesterstude s on p.StudentNumber=s.StudentNumber where s.Section='" . $section . "' and s.Semester='" . $semester . "' and s.SY='" . $sy . "' and s.Status='Enrolled' group by p.Sitio order by p.Sitio");
		return $query->result();
	}
	//Brgy Count by Section
	function BrgyCount($section, $semester, $sy)
	{
		$query = $this->db->query("select p.Brgy, count(p.Brgy) as brgyCount from studeprofile p join semesterstude s on p.StudentNumber=s.StudentNumber where s.Section='" . $section . "' and s.Semester='" . $semester . "' and s.SY='" . $sy . "' and s.Status='Enrolled' group by p.Brgy order by p.Brgy");
		return $query->result();
	}
	//Sections
	// function getSections(){
	// $query=$this->db->query("Select * from sections order by YearLevel");
	// return $query->result();
	// }

	function getSections()
	{
		$this->db->distinct();
		$this->db->select('YearLevel');
		$this->db->from('sections');
		$this->db->order_by('YearLevel');

		$query = $this->db->get();
		return $query->result();
	}

	// public function getSectionsByYearLevel($yearLevel) {
	// 	$this->db->where('YearLevel', $yearLevel);
	// 	return $this->db->get('sections_table')->result(); // Adjust 'sections_table' to your actual table name
	// }

	function getMajor($course)
	{
		$this->db->select('Major');
		$this->db->where('CourseDescription', $course);
		$this->db->distinct();
		$this->db->order_by('Major', 'ASC');
		$query = $this->db->get('course_table');
		return $query->result();
	}

	function getSection()
	{
		$this->db->select('Section');
		$this->db->distinct();
		$this->db->group_by('Section', 'ASC');
		$this->db->order_by('Section', 'ASC');
		$query = $this->db->get('sections');
		return $query->result();
	}


	public function updateEnrollees($id)
	{
		$data = array(
			'enrolStatus' => 'Verified'
		);
		$this->db->where('oeID', $id);
		return $this->db->update('online_enrollment', $data);
	}



	public function byGradeLevel($yearlevel, $sy)
	{
		// Load the database library if not already loaded
		$this->load->database();

		// Define the query using Query Builder
		$this->db->select('*');
		$this->db->from('studeprofile p');
		$this->db->join('semesterstude s', 'p.StudentNumber = s.StudentNumber');
		$this->db->where('s.YearLevel', $yearlevel);
		// $this->db->where('s.Semester', $semester);
		$this->db->where('s.SY', $sy);
		$this->db->where('s.Status', 'Enrolled');
		$this->db->order_by('p.LastName, p.Sex');

		// Execute the query and return the result
		$query = $this->db->get();
		return $query->result();
	}


	//Student Enrollment Status
	public function studeEnrollStat($id, $sy)
	{
		$this->db->where('StudentNumber', $id);
		$this->db->where('SY', $sy);
		$query = $this->db->get('semesterstude');

		if ($query->num_rows() > 0) {
			return $query->result();
		}

		return false;
	}


	public function studeBalance($id, $sy)
	{
		$this->db->where('StudentNumber', $id);
		$this->db->where('SY', $sy);
		$query = $this->db->get('studeaccount', 1);

		if ($query->num_rows() > 0) {
			return $query->result();
		}

		return false;
	}


	//Faculty Load Counts
	function facultyLoadCounts($id, $sy)
	{
		$query = $this->db->query("SELECT count(SubjectCode) as subjectCounts FROM semsubjects where IDNumber='" . $id . "' and SY='" . $sy . "'");

		return $query->result();

		if ($query->num_rows() > 0) {
			return $query->result();
		}
		return false;
	}

	//Faculty Grades
	function facultyGrades($instructor, $sy)
	{
		$query = $this->db->query("SELECT count(SubjectCode) as subjectCounts FROM grades where Instructor='" . $instructor . "' and SY='" . $sy . "' group by SubjectCode");

		return $query->result();

		if ($query->num_rows() > 0) {
			return $query->result();
		}
		return false;
	}

	//Student Total Enrolled Subjects
	function studeTotalSubjects($id, $sem, $sy)
	{
		$query = $this->db->query("SELECT count(SubjectCode) as subjectCounts FROM registration where StudentNumber='" . $id . "' and Sem='" . $sem . "' and SY='" . $sy . "'");

		return $query->result();

		if ($query->num_rows() > 0) {
			return $query->result();
		}
		return false;
	}

	//Student Total Semesters Enrolled
	function semStudeCount($id)
	{
		$query = $this->db->query("SELECT StudentNumber, count(Semester) as SemesterCounts FROM semesterstude where StudentNumber='" . $id . "' group by StudentNumber");

		return $query->result();

		if ($query->num_rows() > 0) {
			return $query->result();
		}
		return false;
	}



	//Masterlist (All)
	function masterlistAll2($id, $sy)
	{
		$query = $this->db->query("select * from semesterstude where semstudentid='" . $id . "' and SY='" . $sy . "' and Status='Enrolled' group by StudentNumber");
		return $query->result();
	}

	//Count Summary Per Year Level
	function byGradeLevelCount($yearlevel, $semester, $sy)
	{
		$query = $this->db->query("SELECT Course, count(Course) enrollees FROM semesterstude where YearLevel='" . $yearlevel . "' and Semester='" . $semester . "' and SY='" . $sy . "' and Status='Enrolled' group by Course");
		return $query->result();
	}

	//Masterlist by Course
	function byCourse($course, $sy)
	{
		$query = $this->db->query("select * from studeprofile p join semesterstude s on p.StudentNumber=s.StudentNumber where s.SY='" . $sy . "' and s.Status='Enrolled' and s.Course='" . $course . "' and s.Status='Enrolled' group by p.StudentNumber order by p.LastName, p.Sex");
		return $query->result();
	}

	//Enrollees Counts Per Course (Year Level Counts)
	function CourseYLCounts($course, $sy)
	{
		$query = $this->db->query("SELECT StudentNumber, YearLevel, count(YearLevel) as yearLevelCounts FROM semesterstude where SY='" . $sy . "' and Status='Enrolled' and Course='" . $course . "' group by YearLevel");
		return $query->result();
	}

	//Enrollees Counts Per Section (Year Level Counts)
	function SectionCounts($course, $sy)
	{
		$query = $this->db->query("SELECT Section, count(Section) as sectionCounts FROM semesterstude where SY='" . $sy . "' and Status='Enrolled' and Course='" . $course . "' group by Section");
		return $query->result();
	}

	//Masterlist by Enrolled Online
	function byEnrolledOnline($department, $sy)
	{
		$query = $this->db->query("select * from studeprofile p join online_enrollment oe on p.StudentNumber=oe.StudentNumber where oe.SY='" . $sy . "' and oe.enrolStatus='Enrolled'");
		return $query->result();
	}

	//Masterlist by Enrolled Semester
	function byEnrolledOnlineSem($sy)
	{
		$query = $this->db->query("select * from studeprofile p join online_enrollment oe on p.StudentNumber=oe.StudentNumber where oe.SY='" . $sy . "' and oe.enrolStatus='Enrolled'");
		return $query->result();
	}

	//Masterlist by Enrolled Online (ALL)
	function byEnrolledOnlineAll()
	{
		$query = $this->db->query("select p.StudentNumber, concat(p.LastName,', ',p.FirstName,' ',p.MiddleName) as StudeName, oe.Course, oe.YearLevel, oe.enrolStatus, concat(oe.Semester,' ',oe.SY) as SY, oe.downPayment, oe.downPaymentStat  from studeprofile p join online_enrollment oe on p.StudentNumber=oe.StudentNumber order by p.LastName");
		return $query->result();
	}

	//Masterlist By Section
	function bySection($section, $YearLevel, $sy)
	{
		$this->db->select('*');
		$this->db->from('studeprofile p');
		$this->db->join('semesterstude s', 'p.StudentNumber = s.StudentNumber');
		$this->db->where('s.Section', $section);
		$this->db->where('s.YearLevel', $YearLevel);
		$this->db->where('s.SY', $sy);
		$this->db->where('s.Status', 'Enrolled');
		$this->db->order_by('p.LastName, p.Sex');

		$query = $this->db->get();
		return $query->result();
	}


	//Masterlist by SY
	public function bySY($sy, $yearlevel = null, $section = null)
	{
		$loggedInUsername = $this->session->username;
	
		$this->db->select('*');
		$this->db->from('semesterstude ss');
		$this->db->join('studeprofile p', 'ss.StudentNumber = p.StudentNumber');
		$this->db->where('Status', 'Enrolled');
		$this->db->where('ss.SchoolID', $loggedInUsername);
		$this->db->where('ss.SY', $sy);
	
		if (!empty($yearlevel)) {
			$this->db->where('ss.YearLevel', $yearlevel);
		}
	
		if (!empty($section)) {
			$this->db->where('ss.Section', $section);
		}
	
		$this->db->group_by('ss.StudentNumber');
		$this->db->order_by('LastName');
	
		$query = $this->db->get();
		return $query->result();
	}
	

	public function getYearLevels($school_id, $sy)
{
    $this->db->select('YearLevel');
    $this->db->distinct();
    $this->db->from('semesterstude');
    $this->db->where('SchoolID', $school_id);
    $this->db->where('SY', $sy);
    $this->db->order_by('YearLevel');
    return $this->db->get()->result();
}

public function get_Sections($school_id, $sy)
{
    $this->db->select('Section');
    $this->db->distinct();
    $this->db->from('semesterstude');
    $this->db->where('SchoolID', $school_id);
    $this->db->where('SY', $sy);
    $this->db->order_by('Section');
    return $this->db->get()->result();
}


	//Masterlist by Date Summary
	function byDateCourseSum($date)
	{
		$query = $this->db->query("SELECT Course, count(Course) as Enrollees FROM semesterstude where EnrolledDate='" . $date . "' and Status='Enrolled' group by Course order by Course");
		return $query->result();
	}



















	//PASSWORD ---------------------------------------------------------------------------------
	function is_current_password($username, $currentpass)
	{
		$this->db->select();
		$this->db->from('o_users');
		$this->db->where('username', $username);
		$this->db->where('password', $currentpass);
		$this->db->where('acctStat', 'active');
		$query = $this->db->get();
		$row = $query->row();
		if ($row) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	function getYearLevel($course)
	{
		$this->db->select('Major');
		$this->db->where('CourseDescription', $course);
		$this->db->distinct();
		$this->db->order_by('Major', 'ASC');
		$query = $this->db->get('course_table');
		return $query->result();
	}

	function reset_userpassword($username, $newpass)
	{
		$data = array(
			'password' => $newpass
		);
		$this->db->where('username', $username);
		if ($this->db->update('o_users', $data)) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	//Get Profile Pictures
	public function profilepic($id)
	{
		$this->db->select('*');
		$this->db->from('o_users');
		$this->db->where('IDNumber', $id);
		$query = $this->db->get();
		return $query->result();
	}

	// Get distinct provinces from the settings_address table
	public function get_provinces()
	{
		// Select distinct provinces
		$this->db->distinct();
		$this->db->select('Province');
		// Order the provinces by name in ascending order
		$this->db->order_by('Province', 'ASC');
		$query = $this->db->get('settings_address');
		$provinces = $query->result_array();

		// Format the data for dropdown
		$formatted_provinces = [];
		foreach ($provinces as $province) {
			$formatted_provinces[] = [
				'id' => $province['Province'],
				'name' => $province['Province']
			];
		}

		return $formatted_provinces;
	}


	public function get_cities($province)
	{
		$this->db->where('Province', $province);
		$this->db->distinct();
		$this->db->select('City');
		$query = $this->db->get('settings_address');
		$cities = $query->result_array();

		// Format the data for dropdown
		$formatted_cities = [];
		foreach ($cities as $city) {
			$formatted_cities[] = [
				'id' => $city['City'], // The 'id' should match the actual column name
				'name' => $city['City']
			];
		}

		return $formatted_cities;
	}

	// Get distinct barangays based on selected city
	public function get_barangays($city)
	{
		$this->db->where('City', $city);
		$this->db->distinct();
		$this->db->select('Brgy');
		$query = $this->db->get('settings_address');
		$barangays = $query->result_array();

		// Format the data for dropdown
		$formatted_barangays = [];
		foreach ($barangays as $barangay) {
			$formatted_barangays[] = [
				'id' => $barangay['Brgy'],
				'name' => $barangay['Brgy']
			];
		}

		return $formatted_barangays;
	}

	function countItemsByCategory($itemCategory)
	{
		$this->db->where('itemCategory', $itemCategory); // Filter by description
		$this->db->from('ls_items'); // Specify the table
		return $this->db->count_all_results(); // Return the count
	}


	function getInventory()
	{
		$this->db->select('ls_items.*, staff.FirstName, staff.MiddleName, staff.LastName'); // Select all from ls_items and relevant columns from staff
		$this->db->from('ls_items');
		$this->db->join('staff', 'ls_items.IDNumber = staff.IDNumber'); // Join on accountable from ls_items and IDNumber from staff
		$this->db->order_by('staff.FirstName, staff.MiddleName, staff.LastName'); // Order by staff name fields

		$query = $this->db->get();
		return $query->result();
	}


	function inventorySummary()
	{
		$this->db->select('itemName, SUM(qty) as itemCount');
		$this->db->from('ls_items');
		$this->db->group_by('itemName');

		$query = $this->db->get();
		return $query->result();
	}

	function getInventoryCategory()
	{
		$this->db->distinct();
		$this->db->select('Category');
		$this->db->from('ls_categories');
		$this->db->order_by('Category');

		$query = $this->db->get();
		return $query->result();
	}
	// Controller function to fetch subcategories
	public function getSubcategories()
	{
		$category = $this->input->post('category'); // Get selected category from POST data

		// Query to fetch subcategories based on the selected category
		$this->db->select('Sub_category');
		$this->db->from('ls_categories');
		$this->db->where('Category', $category);

		$query = $this->db->get();
		$result = $query->result();

		// Return subcategories in JSON format
		echo json_encode($result);
	}



	function getOffice()
	{
		$this->db->distinct(); // Ensure unique office values
		$this->db->select('office'); // Only select the 'office' column
		$this->db->from('ls_office');
		$this->db->order_by('office');

		$query = $this->db->get();
		return $query->result();
	}

	function getStaff()
	{

		$this->db->select('*');
		$this->db->from('staff');
		$this->db->order_by('FirstName, MiddleName, LastName');

		$query = $this->db->get();
		return $query->result();
	}

	function getBrand()
	{
		$this->db->select('*');
		$this->db->distinct();
		$this->db->from('ls_brands');
		$this->db->order_by('brand');

		$query = $this->db->get();
		return $query->result();
	}

	function getInventoryAccountable($accountable)
	{
		$this->db->select('*');
		$this->db->from('ls_items');
		$this->db->where('accountable', $accountable);

		$query = $this->db->get();
		return $query->result();
	}

	function inventorySummaryAccountable($accountable)
	{
		$this->db->select('itemName, SUM(qty) as itemCount');
		$this->db->from('ls_items');
		$this->db->where('accountable', $accountable);
		$this->db->group_by('itemName');

		$query = $this->db->get();
		return $query->result();
	}

	public function getInventoryByUserID($idNumber)
	{
		$this->db->select('ls_items.*, o_users.username, o_users.fName, o_users.mName, o_users.lName'); // Include all necessary fields
		$this->db->from('ls_items');
		$this->db->join('o_users', 'o_users.IDNumber = ls_items.accountable'); // Direct join with o_users
		$this->db->where('o_users.IDNumber', $idNumber); // Filter by IDNumber
		$query = $this->db->get();
		return $query->result();
	}

	function instructor()
	{
		$this->db->select('*');
		$this->db->from('grades');
		$this->db->group_by(['YearLevel', 'Instructor', 'SubjectCode']); // Group by YearLevel, Instructor, and SubjectCode

		$query = $this->db->get();
		return $query->result();
	}


	// public function getDescriptionsByYearLevelAndSY($yearLevel, $SY) {
	//     $this->db->where('YearLevel', $yearLevel);
	//     $this->db->where('SY', $SY);  // Filter by the logged-in SY
	//     $query = $this->db->get('fees');
	//     return $query->result();
	// }

	public function getStudentDetailsWithFees()
	{
		$studentNumber = $this->input->post('StudentNumber');
		$currentSY = $this->session->userdata('sy');  // Get logged-in SY

		$studentDetails = $this->StudentModel->getStudentDetails($studentNumber);

		if ($studentDetails) {
			$yearLevel = $studentDetails->YearLevel;
			// Fetch fees by YearLevel and current SY only
			$fees = $this->StudentModel->getDescriptionsByYearLevelAndSY($yearLevel, $currentSY);

			// Fetch the amount paid, restricted by the current SY
			$amountPaid = $this->StudentModel->getAmountPaid($studentNumber, $currentSY);

			// Combine student details, fees, and amount paid into one response
			$response = [
				'studentDetails' => $studentDetails,
				'fees' => $fees,
				'amountPaid' => $amountPaid  // Add amount paid to the response
			];

			echo json_encode($response);
		} else {
			echo json_encode(['error' => 'Student not found']);
		}
	}



	public function getDescriptionsByYearLevelAndSY($yearLevel, $SY)
	{
		$this->db->where('YearLevel', $yearLevel);
		$this->db->where('SY', $SY);  // Filter by the logged-in SY
		$query = $this->db->get('fees');
		return $query->result();
	}




	// Get Sections by YearLevel

	public function getSectionsByYearLevel($yearlevel)
	{
		$this->db->select('Section');
		$this->db->distinct();
		$this->db->where('YearLevel', $yearlevel);
		$this->db->order_by('Section', 'ASC');
		$query = $this->db->get('sections');
		return $query->result();
	}
	
// Get Adviser IDNumber by YearLevel and Section
public function getAdviserBySection($yearlevel, $section)
{
    $this->db->select('IDNumber, adviserName');
    $this->db->where('YearLevel', $yearlevel);
    $this->db->where('Section', $section);
    $query = $this->db->get('sections');
    return $query->row(); // since it's a single adviser
}

	



	public function getAdviserByCriteria($section, $yearLevel, $schoolYear, $schoolID)
{
    $this->db->select('IDNumber');
    $this->db->from('sections');
    $this->db->where('Section', $section);
    $this->db->where('YearLevel', $yearLevel);
    $this->db->where('SY', $schoolYear);
    $this->db->where('school_id', $schoolID);
    $this->db->limit(1);
    $query = $this->db->get();
    return $query->row(); // returns object with IDNumber
}


public function getStaffByID($id)
{
    $this->db->from('hris_staff');
    $this->db->where('IDNumber', $id);
    $query = $this->db->get();
    return $query->row(); // returns FirstName, MiddleName, LastName
}






	// public function getStaffByID($idNumber) {
	//     $this->db->select('FirstName, MiddleName, LastName'); // Selecting the name columns
	//     $this->db->where('IDNumber', $idNumber);
	//     $query = $this->db->get('staff'); // Assuming 'staff' is the table name
	//     return $query->row(); // Return the staff row object
	// }



	function getStrand($trackVal)
	{
		$this->db->select('strand');
		$this->db->distinct();
		$this->db->where('track', $trackVal); // Ensure 'track' is the correct column name
		$this->db->order_by('strand', 'ASC'); // Order results
		$query = $this->db->get('track_strand'); // Ensure this is the correct table name
		return $query->result(); // Return the results
	}




	public function updateStudentNumber($originalStudentNumber, $newStudentNumber)
	{
		$data = array('StudentNumber' => $newStudentNumber);
		$tables = ['studeprofile', 'semesterstude', 'registration', 'grades', 'studeaccount', 'paymentsaccounts', 'studeadditional', 'studediscount'];

		// Update the StudentNumber in each table
		foreach ($tables as $table) {
			$this->db->where('StudentNumber', $originalStudentNumber);
			$this->db->update($table, $data);
		}

		// Update the username in the o_users table
		$this->db->where('username', $originalStudentNumber);
		$this->db->update('o_users', array('username' => $newStudentNumber));
	}
}
