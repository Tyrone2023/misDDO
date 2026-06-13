<?php
class SettingsModel extends CI_Model 
{
    
    public function getStrandsByTrack()
{
    $track = $this->input->post('track');
    $this->db->select('strand');
    $this->db->where('track', $track);
    $this->db->order_by('strand', 'ASC');
    $query = $this->db->get('track_strand');

    $options = '<option value="">Select Strand</option>';
    foreach ($query->result() as $row) {
        $options .= '<option value="' . $row->strand . '">' . $row->strand . '</option>';
    }

    echo $options;
}




function getStrand1()
{
    $this->db->select('strand');
    $this->db->distinct();
    $this->db->order_by('strand','ASC');
    $query = $this->db->get('track_strand');
    return $query->result();
}
	//Get Track and Display on the combo box
	function getTrack()
	{
		$this->db->select('track');
		$this->db->distinct();
		$this->db->order_by('track','ASC');
		$query = $this->db->get('track_strand');
		return $query->result();
	}

	function getStrand($trackVal)
	{
		$this->db->select('track,strand');
		$this->db->where('track', $trackVal);
		$this->db->distinct();
		$this->db->order_by('track','ASC');
		$query = $this->db->get('track_strand');
		return $query->result();
	}
	
	//Get Track List
	function getTrackList()
	{
	$query=$this->db->query("select * from track_strand order by track");
	return $query->result();
	}
	
    public function getSchoolInfo()
    {
        // Load the database library if not autoloaded
        $this->load->database();
    
        // Execute the query and return the result
        $query = $this->db->get('srms_settings_o', 1);
        return $query->result();
    }
    

		function getDocReq()
		{
		$query=$this->db->query("select * from settings_doc_req order by docName");
		return $query->result();
		}

	
	//Get Section List
	function getSectionList()
	{
	$query=$this->db->query("select * from sections order by Section");
	return $query->result();
	}





	public function get_ethnicity() {
		$query = $this->db->get('settings_ethnicity'); 
		return $query->result();
	}

	public function insertethnicity($data) {
        $this->db->insert('settings_ethnicity', $data);
    }

	public function getethnicitybyId($id)
    {
        $query = $this->db->query("SELECT * FROM settings_ethnicity WHERE id = '" . $id . "'");
        return $query->result();
    }

	public function updateethnicity($id, $ethnicity)
    {
        $data = array(
            'ethnicity' => $ethnicity,
           
        );
        $this->db->where('id', $id);
        $this->db->update('settings_ethnicity', $data);
    }

	public function Delete_ethnicity($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('settings_ethnicity');
    }




	public function get_religion() {
		$query = $this->db->get('settings_religion'); 
		return $query->result();
	}

	public function insertreligion($data) {
        $this->db->insert('settings_religion', $data);
    }

	public function getreligionbyId($id)
    {
        $query = $this->db->query("SELECT * FROM settings_religion WHERE id = '" . $id . "'");
        return $query->result();
    }

	public function updatereligion($id, $religion)
    {
        $data = array(
            'religion' => $religion,
           
        );
        $this->db->where('id', $id);
        $this->db->update('settings_religion', $data);
    }

	public function Delete_religion($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('settings_religion');
    }



	// for prevschool

	public function get_prevschool() {
		$query = $this->db->get('prevschool'); 
		return $query->result();
	}

    public function displayrecordsById($StudentNumber)
{
    $this->db->where('StudentNumber', $StudentNumber); // Adjust based on your ID field
    $query = $this->db->get('studeprofile'); // Replace 'students' with your actual table name
    return $query->row(); // Ensure this returns a single row as an object
}


    // public function get_prevschool1() {
    //     $this->db->select('sp.*, ps.School');
    //     $this->db->from('studeprofile sp');
    //     $this->db->join('prevschool ps', 'sp.Elementary = ps.School', 'left'); // Use 'inner' or 'left' as needed
    //     $query = $this->db->get();
    //     return $query->result();
    // }

    // public function get_prevschool1() {
    //     $this->db->select('School');  // Select the needed fields
    //     $this->db->from('prevschool');
    //     $query = $this->db->get();
    //     return $query->result(); // All schools are fetched from 'prevschool'
    // }
    
    
	public function insertprevschool($data) {
        $this->db->insert('prevschool', $data);
    }

	public function getprevschoolbyId($schoolID)
    {
        $query = $this->db->query("SELECT * FROM prevschool WHERE schoolID = '" . $schoolID . "'");
        return $query->result();
    }

	public function updateprevschool($schoolID, $School, $Address)
    {
        $data = array(
            'School' => $School,
            'Address' => $Address,

           
        );
        $this->db->where('schoolID', $schoolID);
        $this->db->update('prevschool', $data);
    }

	public function Delete_prevschool($schoolID)
    {
        $this->db->where('schoolID', $schoolID);
        $this->db->delete('prevschool');
    }



    public function insertTrack_strand($data) {
        $this->db->insert('track_strand', $data);
    }

    public function get_track_strand() {
		$query = $this->db->get('track_strand'); 
		return $query->result();
	}

    public function get_track_strandbyId($trackID)
    {
        $query = $this->db->query("SELECT * FROM track_strand WHERE trackID = '" . $trackID . "'");
        return $query->result();
    }


    public function updatetrack_strand($trackID, $track, $strand)
    {
        $data = array(
            'track' => $track,
            'strand' => $strand,

           
        );
        $this->db->where('trackID', $trackID);
        $this->db->update('track_strand', $data);
    }


    public function Delete_track_strand($trackID)
    {
        $this->db->where('trackID', $trackID);
        $this->db->delete('track_strand');
    }



    public function insertprogram($data) {
        $this->db->insert('course_table', $data);
    }


    public function update_program($courseid, $CourseCode, $CourseDescription, $Major)
    {
        $data = array(
            'CourseCode' => $CourseCode,
            'CourseDescription' => $CourseDescription,
            'Major' => $Major,

           
        );
        $this->db->where('courseid', $courseid);
        $this->db->update('course_table', $data);
    }


    public function get_programbyId($courseid)
    {
        $query = $this->db->query("SELECT * FROM course_table WHERE courseid = '" . $courseid . "'");
        return $query->result();
    }


    public function Delete_program($courseid)
    {
        $this->db->where('courseid', $courseid);
        $this->db->delete('course_table');
    }


    public function get_subjects() {
		$query = $this->db->get('subjects'); 
		return $query->result();
	}

    public function get_staff() {
		$query = $this->db->get('staff'); 
		return $query->result();
	}

    public function get_Major() {
        $this->db->select('Major');
        $this->db->from('course_table');
        $this->db->order_by('Major');
        $query = $this->db->get();
		return $query->result();
	}

    public function get_year_levels() {
        $this->db->distinct();
        $this->db->select('yearLevel');
        $query = $this->db->get('subjects');
        return $query->result();
    }

    public function get_subjects_by_year($yearLevel) {
        $this->db->where('yearLevel', $yearLevel);  // Add a where clause to filter by yearLevel
        $query = $this->db->get('subjects');
        return $query->result();  // Return the result as an array of objects
    }
    
    

    public function insertsubjects($data) {
        $this->db->insert('subjects', $data);
    }


    public function update_subject($id, $subjectCode, $description, $yearLevel, $course, $sem, $subCategory)
    {
        $data = array(
            'subjectCode' => $subjectCode,
            'description' => $description,
            'yearLevel' => $yearLevel,
            'course' => $course,
            'sem' => $sem,
            'subCategory' => $subCategory,
           
        );
        $this->db->where('id', $id);
        $this->db->update('subjects', $data);
    }

    public function get_subjectbyId($id)
    {
        $query = $this->db->query("SELECT * FROM subjects WHERE id = '" . $id . "'");
        return $query->result();
    }


    public function Delete_subjects($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('subjects');
    }

    public function get_Yearlevels() {
        $this->db->distinct();
        $this->db->select('YearLevel');
        $query = $this->db->get('semsubjects');
        return $query->result();
    }

    // public function get_subjects_by_yearlevel($YearLevel) {
    //     $this->db->select("semsubjects.*, CONCAT(staff.FirstName, ' ', staff.MiddleName, ' ', staff.LastName) AS Fullname");
    //     $this->db->from('semsubjects');
    //     $this->db->join('staff', 'semsubjects.IDNumber = staff.IDNumber', 'left');
    //     $this->db->where('semsubjects.YearLevel', $YearLevel);
    //     $query = $this->db->get();
    
    //     return $query->result();
    // }

    // public function get_subjects_by_yearlevel($YearLevel) {
    //     $this->db->select("subjects.*, CONCAT(staff.FirstName, ' ', staff.MiddleName, ' ', staff.LastName) AS Fullname");
    //     $this->db->from('subjects');
    //     $this->db->join('staff', 'subjects.id = staff.IDNumber', 'left');
    //     $this->db->where('subjects.YearLevel', $YearLevel);
    //     $query = $this->db->get();
    
    //     return $query->result();
    // }
    
    
    
    // public function get_classProgram() {
	// 	$query = $this->db->get('semsubjects'); 
	// 	return $query->result();
	// }


    // public function get_classProgram() {
    //     $this->db->select("semsubjects.*, CONCAT(staff.FirstName, ' ', staff.MiddleName, ' ', staff.LastName) AS Fullname");
    //     $this->db->from('semsubjects');
    //     $this->db->join('staff', 'semsubjects.IDNumber = staff.IDNumber', 'left'); // Left join to include all semsubjects even if no matching staff
    //     $query = $this->db->get();
    //     return $query->result();
    // }
    

    public function get_subjects_by_yearlevel($YearLevel,$sy)
    {
        $this->db->select("semsubjects.*, CONCAT(staff.FirstName, ' ', staff.MiddleName, ' ', staff.LastName) AS Fullname");
        $this->db->from('semsubjects');
        $this->db->join('staff', 'semsubjects.IDNumber = staff.IDNumber', 'left');
        $this->db->where('semsubjects.YearLevel', $YearLevel);
        $this->db->where('semsubjects.SY', $sy);
        $this->db->order_by('semsubjects.SubjectCode', 'ASC');  // Sort by SubjectCode
        $query = $this->db->get();
    
        return $query->result();
    }
    
    public function get_classProgram($sy)
    {
        $this->db->select("semsubjects.*, CONCAT(staff.FirstName, ' ', staff.MiddleName, ' ', staff.LastName) AS Fullname");
        $this->db->from('semsubjects');
        $this->db->join('staff', 'semsubjects.IDNumber = staff.IDNumber', 'left');
        $this->db->where('semsubjects.SY', $sy);
        $this->db->order_by('semsubjects.SubjectCode', 'ASC');  // Sort by SubjectCode
        $query = $this->db->get();
    
        return $query->result();
    }
    




















    public function insertclass($data) {
        $this->db->insert('semsubjects', $data);
    }






    public function update_class($subjectid, $SubjectCode, $Description, $Section, $SchedTime, $IDNumber, $SY, $Course, $YearLevel, $SubjectStatus)
    {
        $data = array(
            'subjectid' => $subjectid,
                'SubjectCode' => $SubjectCode,
				'Description' => $Description,
				'Section' => $Section,
				'SchedTime' => $SchedTime,
				'IDNumber' => $IDNumber,
				'SY' => $SY,
				'Course' => $Course,
				'YearLevel' => $YearLevel,
				'SubjectStatus' => $SubjectStatus,
           
        );
        $this->db->where('subjectid', $subjectid);
        $this->db->update('semsubjects', $data);
    }

    public function get_classbyId($subjectid)
    {
        $query = $this->db->query("SELECT * FROM semsubjects WHERE subjectid = '" . $subjectid . "'");
        return $query->result();
    }

    public function Delete_class($subjectid)
    {
        $this->db->where('subjectid', $subjectid);
        $this->db->delete('semsubjects');
    }


    public function get_Adviser($SY) {
        // Join 'staff' table on 'IDNumber'
        $this->db->where('SY', $SY); // Filter by logged-in SY
        $this->db->select('sections.*, staff.*'); // Select columns you need
        $this->db->from('sections');
        $this->db->join('staff', 'staff.IDNumber = sections.IDNumber', 'left'); // Use 'left' join if you want all records from 'sections'
        
        $query = $this->db->get();
        return $query->result();
    }
     



    


    public function insertadviser($data) {
        $this->db->insert('sections', $data);
    }

    public function update_Adviser($sectionID, $Section, $IDNumber)
    {
        $data = array(
            'Section' => $Section,  
            'IDNumber' => $IDNumber  
        );
        $this->db->where('sectionID', $sectionID);
        $this->db->update('sections', $data);
    }
    


    public function get_adviserbyId($sectionID)
    {
        $query = $this->db->query("SELECT * FROM sections WHERE sections = '" . $sectionID . "'");
        return $query->result();
    }


    
    public function Delete_adviser($sectionID)
    {
        $this->db->where('sectionID', $sectionID);
        $this->db->delete('sections');
    }



    public function update_Section($sectionID, $Section, $IDNumber)
    {
        $data = array(
            'Section' => $Section,
            'IDNumber' => $IDNumber,

        );
        $this->db->where('sectionID', $sectionID);
        $this->db->update('sections', $data);
    }

    public function Delete_section($sectionID)
    {
        $this->db->where('sectionID', $sectionID);
        $this->db->delete('sections');
    }





	public function get_expenses() {
		$query = $this->db->get('expenses'); 
		return $query->result();
	}

	public function expenses() {
		$query = $this->db->get('expenses'); 
		return $query->result();
	}

	public function insertexpenses($data) {
		return $this->db->insert('expenses', $data);
	}
	
	

	public function getexpensesbyId($expensesid)
    {
        $query = $this->db->query("SELECT * FROM expenses WHERE expensesid = '" . $expensesid . "'");
        return $query->result();
    }

	public function updateexpenses($expensesid, $Description, $Amount, $Responsible, $ExpenseDate, $Category)
    {
        $data = array(
            'Description' => $Description,
            'Amount' => $Amount,
			'Responsible' => $Responsible,
            'ExpenseDate' => $ExpenseDate,
			'Category' => $Category,
           

           
        );
        $this->db->where('expensesid', $expensesid);
        $this->db->update('expenses', $data);
    }

	public function Delete_expenses($expensesid)
    {
        $this->db->where('expensesid', $expensesid);
        $this->db->delete('expenses');
    }


	public function get_expensesCategory() {
		$query = $this->db->get('expensescategory'); 
		return $query->result();
	}

	public function insertexpensesCategory($data) {
		return $this->db->insert('expensescategory', $data);
	}

	public function getexpensescategorybyId($categoryID)
    {
        $query = $this->db->query("SELECT * FROM expensescategory WHERE categoryID = '" . $categoryID . "'");
        return $query->result();
    }

	public function updateexpensescategory($categoryID, $Category)
    {
        $data = array(
			'Category' => $Category,           
        );
        $this->db->where('categoryID', $categoryID);
        $this->db->update('expensescategory', $data);
    }

	public function Delete_expensescategory($categoryID)
    {
        $this->db->where('categoryID', $categoryID);
        $this->db->delete('expensescategory');
    }


	public function get_categories()
{
    $this->db->distinct();
    $this->db->select('Category');
    $this->db->from('expenses');
    $query = $this->db->get();
    return $query->result_array(); // Fetches categories as an array
}


public function getDescriptionCategories() {
    $this->db->distinct();
    $this->db->select('description');
    $query = $this->db->get('paymentsaccounts');
    return $query->result_array();
}


public function Payment($SY) {
    $this->db->select('paymentsaccounts.*, studeprofile.*');
    $this->db->from('paymentsaccounts');
    $this->db->join('studeprofile', 'studeprofile.StudentNumber = paymentsaccounts.StudentNumber');
    $this->db->where('paymentsaccounts.SY', $SY); // Filter by SY
    $this->db->where('paymentsaccounts.CollectionSource', "Student's Account"); // Filter by CollectionSource
    $this->db->where('paymentsaccounts.ORStatus', "Valid");
    $this->db->order_by('studeprofile.LastName', 'ASC'); 
    $query = $this->db->get();
    return $query->result(); // Return the filtered data
}






public function services($SY) {
    $this->db->select('paymentsaccounts.*, studeprofile.*');
    $this->db->from('paymentsaccounts');
    $this->db->join('studeprofile', 'studeprofile.StudentNumber = paymentsaccounts.StudentNumber');
    $this->db->where('paymentsaccounts.SY', $SY); // Filter by SY
    $this->db->where('paymentsaccounts.CollectionSource', 'Services'); // Filter by CollectionSource = 'Services'
    $query = $this->db->get();
    return $query->result(); // Return the filtered data
}




public function void($SY) {
    $this->db->select('voidreceipts.ORNumber, voidreceipts.description, voidreceipts.Amount, voidreceipts.voidDate as PDate, voidreceipts.Reasons, studeprofile.FirstName, studeprofile.MiddleName, studeprofile.LastName, paymentsaccounts.ORStatus'); // Added ORStatus
    $this->db->from('voidreceipts');
    $this->db->join('paymentsaccounts', 'paymentsaccounts.ID = voidreceipts.ID');
    $this->db->join('studeprofile', 'studeprofile.StudentNumber = paymentsaccounts.StudentNumber');
    $this->db->where('paymentsaccounts.SY', $SY);
    $this->db->where('paymentsaccounts.ORStatus', 'Void');
    $query = $this->db->get();
    return $query->result();
}




public function Paymentlist($SY) {
    $this->db->select('description, SUM(amount) as total_amount, CollectionSource');
    $this->db->from('paymentsaccounts');
    $this->db->where('SY', $SY); // Apply the SY filter
    $this->db->group_by(['description', 'CollectionSource']); // Group by description and CollectionSource
    $query = $this->db->get();
    return $query->result_array(); // Return as an array for easier handling
}




// public function getSummaryData($fromDate, $toDate) {
//     $this->db->select('description, SUM(amount) as total_amount');
//     $this->db->from('paymentsaccounts');
//     $this->db->where('Pdate >=', $fromDate);
//     $this->db->where('Pdate <=', $toDate);
//     $this->db->group_by(['description', 'CollectionSource']); // Group by description and CollectionSource
//     $query = $this->db->get();

//     return $query->result_array();
// }









public function getCollectionReport($description, $collectionSource)
{
    $this->db->select('paymentsaccounts.*, studeprofile.*');
    $this->db->from('paymentsaccounts');
    $this->db->join('studeprofile', 'studeprofile.StudentNumber = paymentsaccounts.StudentNumber');
    $this->db->where('paymentsaccounts.CollectionSource', "Student's Account"); // Filter by CollectionSource
    $this->db->where('paymentsaccounts.ORStatus', "Valid");
    // Apply filters for both description and CollectionSource
    if ($description) {
        $this->db->where('paymentsaccounts.description', $description); // Filter by description
    }
    
    if ($collectionSource) {
        $this->db->where('paymentsaccounts.CollectionSource', $collectionSource); // Filter by CollectionSource
    }

    $query = $this->db->get();
    return $query->result(); 
}



public function semesterstude() {
    $query = $this->db->query("
        SELECT semesterstude.*, studeprofile.*
        FROM semesterstude
        JOIN studeprofile ON semesterstude.StudentNumber = studeprofile.StudentNumber
        GROUP BY semesterstude.StudentNumber
        ORDER BY LastName
    ");
    return $query->result();
}

public function getStudentCourse($studentNumber) {
    $query = $this->db->select('course')
                      ->from('semesterstude')
                      ->where('StudentNumber', $studentNumber)
                      ->get();

    return $query->row(); // Return a single row with the course
}





public function studeaccount() {
    $query = $this->db->query("
        SELECT studeaccount.*, studeprofile.*
        FROM studeaccount
        JOIN studeprofile ON studeaccount.StudentNumber = studeprofile.StudentNumber
        GROUP BY studeaccount.StudentNumber
        ORDER BY LastName
    ");
    return $query->result();
}









function studeAcc()
{
    $this->db->distinct();
    $this->db->select('Course');
    $this->db->from('coursefees');
    $this->db->order_by('Course');
    
    $query = $this->db->get();
    return $query->result();
}





// public function studeAcc() {
// 	// $query = $this->db->get('studeaccount'); 
//     $query = $this->db->query("SELECT * FROM studeaccount");
// 	return $query->result();
// }

public function courseTable() {
    $query = $this->db->query("SELECT DISTINCT courseid, CourseCode, CourseDescription, Major FROM course_table");
    return $query->result();
}






public function user($id){
    $this->db->select('*');
    $this->db->from('o_users');
    $this->db->where('IDNumber',$id);
    $query=$this->db->get();
    return $query->result();
}




public function getLastORNumber() {
    $this->db->select('ORNumber');
    $this->db->from('paymentsaccounts');
    $this->db->order_by('ORNumber', 'DESC');
    $this->db->limit(1);
    $query = $this->db->get();

    if ($query->num_rows() > 0) {
        return $query->row()->ORNumber;
    }
    return '1'; // Default starting OR number if no records found
}










// public function getStudentDetails($studentNumber)
// {
//     $this->db->select('Course, YearLevel');
//     $this->db->from('studeaccount'); 
//     $this->db->where('StudentNumber', $studentNumber);
//     $query = $this->db->get();

//     if ($query->num_rows() > 0) {
//         return $query->row(); // Return the row containing Course and YearLevel
//     } else {
//         return null; // Handle this case as needed
//     }
// }





public function CourseFees() {
    $query = $this->db->get('fees'); 
    return $query->result();
}

public function updateCourseFeesbyId($feesid)
{
    $query = $this->db->query("SELECT * FROM fees WHERE feesid = '" . $feesid . "'");
    return $query->result();
}


public function getYearLevels()
{
    // Fetch distinct YearLevel from the fees table
    $this->db->select('YearLevel');
    $this->db->distinct();
    $query = $this->db->get('fees');
    return $query->result();
}

// public function getFeesByYearLevel($yearLevel)
// {
//     // Fetch fees data based on selected YearLevel
//     $this->db->where('YearLevel', $yearLevel);
//     $query = $this->db->get('fees');
//     return $query->result();
// }


public function getFeesByYearLevel($yearLevel)
{
    // Fetch fees data based on selected YearLevel
    $this->db->where('YearLevel', $yearLevel);
    $query = $this->db->get('fees');
    return $query->result();
}

public function getTotalFeesByYearLevel($yearLevel)
{
    // Get total amount for a specific year level
    $this->db->select_sum('Amount');
    $this->db->where('YearLevel', $yearLevel);
    $query = $this->db->get('fees');
    return $query->row()->Amount;
}

public function getTotalFees()
{
    // Get total amount for all year levels
    $this->db->select_sum('Amount');
    $query = $this->db->get('fees');
    return $query->row()->Amount;
}



public function updateFees($feesid, $YearLevel, $Course, $Description, $Amount)
    {
        $data = array(
            'YearLevel' => $YearLevel,
            'Course' => $Course,
            'Description' => $Description,
            'Amount' => $Amount          
        );
        $this->db->where('feesid', $feesid);
        $this->db->update('fees', $data);
    }

	public function Deletefees($feesid)
    {
        $this->db->where('feesid', $feesid);
        $this->db->delete('fees');
    }

    public function insertfees($data) {
		return $this->db->insert('fees', $data);
	}


    public function getDistinctCourses()
{
    $this->db->select('DISTINCT(Course)');
    $this->db->from('fees');
    $query = $this->db->get();
    return $query->result();
}

public function getDistinctYearLevels()
{
    $this->db->select('DISTINCT(YearLevel)');
    $this->db->from('fees');
    $query = $this->db->get();
    return $query->result();
}


public function get_brand() {
    $query = $this->db->get('ls_brands'); 
    return $query->result();
}

public function get_brandbyID($brandID) {
    $query = $this->db->query("SELECT * FROM ls_brands WHERE brandID = '" . $brandID . "'");
    return $query->result();
}

public function insertBrand($data) {
    $this->db->insert('ls_brands', $data);
}

public function update_brand($brandID, $brand)
{
    $data = array(
        'brand' => $brand,
    );
    $this->db->where('brandID', $brandID);
    $this->db->update('ls_brands', $data);
}

public function Delete_brand($brandID)
{
    $this->db->where('brandID', $brandID);
    $this->db->delete('ls_brands');
}

public function get_category() {
    $query = $this->db->get('ls_categories'); 
    return $query->result();
}

public function get_categorybyID($CatNo) {
    $query = $this->db->query("SELECT * FROM ls_categories WHERE CatNo = '" . $CatNo . "'");
    return $query->result();
}

public function insertCategory($data) {
    $this->db->insert('ls_categories', $data);
}

public function update_category($CatNo, $Category, $Sub_category)
{
    $data = array(
        'Category' => $Category,
        'Sub_category' => $Sub_category,
    );
    $this->db->where('CatNo', $CatNo);
    $this->db->update('ls_categories', $data);
}

public function Delete_category($CatNo)
{
    $this->db->where('CatNo', $CatNo);
    $this->db->delete('ls_categories');
}


public function get_office() {
    $query = $this->db->get('ls_office'); 
    return $query->result();
}

public function get_officebyID($officeID) {
    $query = $this->db->query("SELECT * FROM ls_office WHERE officeID = '" . $officeID . "'");
    return $query->result();
}

public function insertOffice($data) {
    $this->db->insert('ls_office', $data);
}

public function update_office($officeID, $office)
{
    $data = array(
        'office' => $office,
    );
    $this->db->where('officeID', $officeID);
    $this->db->update('ls_office', $data);
}

public function Delete_office($officeID)
{
    $this->db->where('officeID', $officeID);
    $this->db->delete('ls_office');
}



function course()
{
    $this->db->distinct();
    $this->db->select('CourseDescription');
    $this->db->from('course_table');
    $this->db->order_by('CourseDescription');
    
    $query = $this->db->get();
    return $query->result();
}


public function getMajorsByCourse($CourseDescription)
{
    $this->db->select('Major');
    $this->db->from('course_table');
    $this->db->where('CourseDescription', $CourseDescription);
    $query = $this->db->get();

    return $query->result(); // Return the result as an array
}









// Get fees data based on selected YearLevel and SY
public function getFeesByYearLevelAndSY($yearLevel, $SY)
{
    $this->db->where('YearLevel', $yearLevel);
    $this->db->where('SY', $SY); // Filter by logged-in SY
    $query = $this->db->get('fees');
    return $query->result();
}

// Get total amount for a specific year level and SY
public function getTotalFeesByYearLevelAndSY($yearLevel, $SY)
{
    $this->db->select_sum('Amount');
    $this->db->where('YearLevel', $yearLevel);
    $this->db->where('SY', $SY); // Filter by logged-in SY
    $query = $this->db->get('fees');
    return $query->row()->Amount;
}

// Get fees data for the logged-in SY (all year levels)
public function getCourseFeesBySY($SY)
{
    $this->db->where('SY', $SY); // Filter by logged-in SY
    $query = $this->db->get('fees');
    return $query->result();
}

// Get total amount for all year levels for the logged-in SY
public function getTotalFeesBySY($SY)
{
    $this->db->select_sum('Amount');
    $this->db->where('SY', $SY); // Filter by logged-in SY
    $query = $this->db->get('fees');
    return $query->row()->Amount;
}


function track()
{
    $this->db->distinct();
    $this->db->select('track,school_id');
    $this->db->from('track_strand');
    $this->db->where('school_id', $this->session->username);
    $this->db->order_by('track');
    
    $query = $this->db->get();
    return $query->result();
}

function strand()
{
    $this->db->distinct();
    $this->db->select('strand,school_id');
    $this->db->from('track_strand');
    $this->db->where('school_id', $this->session->username);
    $this->db->order_by('strand');
    
    $query = $this->db->get();
    return $query->result();
}

function GetSub()
{
    $this->db->distinct();
    $this->db->select('subjectCode');
    $this->db->from('subjects');
    $this->db->order_by('subjectCode');
    $query = $this->db->get();
    return $query->result();
}


function GetSub1()
{
    $this->db->distinct();
    $this->db->select('description');
    $this->db->from('subjects');
    $this->db->order_by('description');
    $query = $this->db->get();
    return $query->result();
}

function GetSub2()
{
    $this->db->distinct();
    $this->db->select('course');
    $this->db->from('subjects');
    $this->db->order_by('course');
    $query = $this->db->get();
    return $query->result();
}

function GetSub3()
{
    $this->db->distinct();
    $this->db->select('yearLevel');
    $this->db->from('subjects');
    $this->db->order_by('yearLevel');
    $query = $this->db->get();
    return $query->result();
}

function GetSection()
{
    $this->db->distinct();
    $this->db->select('Section');
    $this->db->from('sections'); 
    $this->db->order_by('Section');
    $query = $this->db->get();
    return $query->result();
}


// public function get_subjects_by_yearlevel1($yearLevel)
// {
//     $this->db->distinct();  // Ensures unique rows
//     $this->db->select('subjects.SubjectCode, subjects.Description, sections.Section');
//     $this->db->from('subjects');
//     $this->db->join('sections', 'subjects.yearLevel = sections.YearLevel', 'left');
//     $this->db->where('subjects.yearLevel', $yearLevel);
//     $this->db->group_by(['subjects.SubjectCode', 'subjects.Description']); // Group by SubjectCode and Description
//     $query = $this->db->get();
//     return $query->result();
// }






// public function get_subjects_by_yearlevel1($yearLevel)
// {
//     $this->db->select('subjectCode, description'); // Corrected: use a single string
//     $this->db->from('subjects');
//     $this->db->where('yearLevel', $yearLevel); // No need for 'subjects.' prefix
//     $query = $this->db->get();
//     return $query->result();
// }



public function get_subjects_by_yearlevel1($yearLevel)
{
    $this->db->select('subjectCode, description');
    $this->db->from('subjects');
    $this->db->where('yearLevel', $yearLevel);
    $this->db->order_by('subjectCode', 'ASC'); // Sort by Subject Code
    $query = $this->db->get();
    return $query->result();
}





public function get_payment()
{
    // First, get ORNumbers from the 'voidreceipts' table
    $voided_ORNumbers = $this->db->select('ORNumber')
                                 ->from('voidreceipts')
                                 ->get()
                                 ->result_array();
    
    // Extract ORNumbers into an array
    $voided_ORNumbers = array_column($voided_ORNumbers, 'ORNumber');

    // Now query 'paymentsaccounts' for records from the last 7 days excluding voided ORNumbers
    $this->db->select('*');
    $this->db->from('paymentsaccounts');
    $this->db->where('pDate >=', date('Y-m-d', strtotime('-7 days')));
    
    // Exclude ORNumbers found in voidreceipts
    if (!empty($voided_ORNumbers)) {
        $this->db->where_not_in('ORNumber', $voided_ORNumbers);
    }

    $query = $this->db->get();
    return $query->result();
}




public function get_payment1()
{
    $this->db->select('Cashier');
    $this->db->from('paymentsaccounts');
    $query = $this->db->get();
    return $query->row(); // Return a single record as an object
}


// public function getPaymentDetailsByORNumber($ORNumber)
// {
//     $this->db->select('*');
//     $this->db->from('paymentsaccounts');
//     $this->db->where('ORNumber', $ORNumber);
//     $query = $this->db->get();

//     return $query->row();
// }

public function getPaymentDetailsByORNumber($ORNumber)
{
    $this->db->select('paymentsaccounts.*, studeprofile.FirstName, studeprofile.MiddleName, studeprofile.LastName');
    $this->db->from('paymentsaccounts');
    $this->db->join('studeprofile', 'studeprofile.StudentNumber = paymentsaccounts.StudentNumber');
    $this->db->where('paymentsaccounts.ORNumber', $ORNumber);
    $query = $this->db->get();

    return $query->row();
}




public function getPaymentById($id)
{
    $this->db->select('paymentsaccounts.*, studeprofile.FirstName, studeprofile.MiddleName, studeprofile.LastName');
    $this->db->from('paymentsaccounts');
    $this->db->join('studeprofile', 'studeprofile.StudentNumber = paymentsaccounts.StudentNumber');
    $this->db->where('paymentsaccounts.ID', $id);
    $query = $this->db->get();

    return $query->row();
}






public function updatePayment($id, $description, $Amount, $CheckNumber, $Bank, $PaymentType)
{
    $data = array(
        'description' => $description,
        'Amount' => $Amount,
        'CheckNumber' => $CheckNumber,
        'Bank' => $Bank,
        'PaymentType' => $PaymentType,
       

       
    );
    $this->db->where('id', $id);
    $this->db->update('paymentsaccounts', $data);
}




public function get_sections_by_yearlevel($yearLevel)
{
    $this->db->select('Section');
    $this->db->from('sections');
    $this->db->where('YearLevel', $yearLevel);
    $this->db->order_by('Section', 'ASC');
    $query = $this->db->get();

    return $query->result();
}

public function getTotalFeesGroupedByYearLevel($SY)
{
    $this->db->select('YearLevel, SUM(Amount) as total_amount');
    $this->db->from('fees');
    $this->db->where('SY', $SY);
    $this->db->group_by('YearLevel');
    $query = $this->db->get();
    return $query->result_array();
}



public function checkClassExists($yearLevel, $subjectCode, $section, $SY) {
    $this->db->where('YearLevel', $yearLevel);
    $this->db->where('SubjectCode', $subjectCode);
    $this->db->where('Section', $section);
    $this->db->where('SY', $SY);
    $query = $this->db->get('semsubjects');

    // Log the generated SQL query to debug if it's working correctly
    log_message('debug', 'SQL Query: ' . $this->db->last_query());

    return $query->num_rows() > 0; // Returns true if a record exists
}



public function get_subjects_by_yearlevel2($yearLevel)
{
    $this->db->where('YearLevel', $yearLevel);
    $this->db->order_by('SubjectCode', 'ASC'); // Sort by Subject Code
    return $this->db->get('subjects')->result();
}



// Get the current TotalPayments for a student
public function getTotalPayments($studentNumber)
{
    $this->db->select('TotalPayments');
    $this->db->from('studeaccount'); // Ensure this table exists
    $this->db->where('StudentNumber', $studentNumber);
    $query = $this->db->get();
    $result = $query->row();

    return $result ? $result->TotalPayments : 0;
}

// Update the TotalPayments for a student
public function updateTotalPayments($studentNumber, $newTotal)
{
    $this->db->set('TotalPayments', $newTotal);
    $this->db->where('StudentNumber', $studentNumber);
    $this->db->update('studeaccount');
}



public function insertpaymentsaccounts($data) {
    return $this->db->insert('paymentsaccounts', $data);
}


public function updateORStatus($ORNumber, $ORStatus, $voidData = [])
{
    // Update the ORStatus in paymentsaccounts
    $this->db->set('ORStatus', $ORStatus);
    $this->db->where('ORNumber', $ORNumber);
    $updateResult = $this->db->update('paymentsaccounts');

    if ($updateResult) {
        // Ensure 'ID' is provided
        if (!isset($voidData['ID'])) {
            // Handle missing ID, possibly log an error
            log_message('error', 'ID is missing in voidData when inserting into voidreceipts.');
            return false;
        }

        // Prepare data for insertion into voidreceipts
        $voidReceiptData = [
            'ID' => $voidData['ID'], // Include ID for foreign key
            'ORNumber' => $ORNumber,
            'Amount' => $voidData['amount'],
            'PaymentDate' => $voidData['pDate'],
            'Description' => $voidData['description'],
            'VoidDate' => $voidData['voidDate'],
            'Cashier' => $voidData['cashier'],
            'Reasons' => $voidData['Reasons']
        ];

        // Insert into voidreceipts table
        $insertResult = $this->db->insert('voidreceipts', $voidReceiptData);

        if (!$insertResult) {
            // Handle insertion error, possibly log
            log_message('error', 'Failed to insert into voidreceipts: ' . $this->db->error()['message']);
            return false;
        }
    }

    return $updateResult;
}




// public function getTotalPayments1($studentNumber, $SY) {
//     $this->db->select('SUM(Amount) as TotalPayments');
//     $this->db->from('paymentsaccounts');
//     $this->db->where('StudentNumber', $studentNumber);
//     $this->db->where('SY', $SY);

//     // Include only "Student's Account" and explicitly exclude "Services"
//     $this->db->group_start();
//     $this->db->where('CollectionSource', "Student's Account");
//     $this->db->or_where('CollectionSource IS NULL'); // Handle potential null values
//     $this->db->group_end();

//     $result = $this->db->get()->row();

//     return $result->TotalPayments ?? 0;
// }


// public function getAmountPaid1($studentNumber, $SY) {
//     $this->db->select_sum('Amount'); // Assuming 'Amount' is the column name for payment amount
//     $this->db->where('StudentNumber', $studentNumber);
//     $this->db->where('SY', $SY);  // Ensure it matches the current SY
// 	$this->db->where('CollectionSource !=', 'Services'); // Tyrone
//     $query = $this->db->get('paymentsaccounts'); // Replace with your actual payments table name
//     return $query->row()->Amount ?? 0; // Return the sum or 0 if no payments found
// }


// public function getTotalPayments1($studentNumber, $SY) {
//     $this->db->select_sum('Amount'); // Assuming 'Amount' is the column name for payment amount
//     $this->db->where('StudentNumber', $studentNumber);
//     $this->db->where('SY', $SY);  
//     $this->db->where('CollectionSource !=', 'Services'); // Exclude 'Services' payments
//     $query = $this->db->get('paymentsaccounts');
//     return $query->row()->Amount ?? 0; // Return the sum or 0 if no payments found
// }


public function getTotalPayments1($studentNumber, $SY) {
    $this->db->select_sum('Amount', 'TotalAmount');
    $this->db->where('StudentNumber', $studentNumber);
    $this->db->where('SY', $SY);
    $this->db->where('CollectionSource !=', 'Services');
    $this->db->where('ORStatus !=', 'Void');
    $query = $this->db->get('paymentsaccounts');

    // Ensure it returns 0 if no result is found
    return (float) ($query->row()->TotalAmount ?? 0);
}


public function calculateTotalPayments($studentNumber, $SY)
{
    $this->db->select_sum('Amount');
    $this->db->where('StudentNumber', $studentNumber);
    $this->db->where('SY', $SY);
    $query = $this->db->get('paymentsaccounts');

    if ($query->num_rows() > 0) {
        return (float)$query->row()->Amount; // Return the sum of payments
    }

    return 0; // Return 0 if no payments exist
}

 

public function getCurrentBalance($studentNumber, $SY) {
    $this->db->select('CurrentBalance');
    $this->db->where('StudentNumber', $studentNumber);
    $this->db->where('SY', $SY);
    return (float) ($this->db->get('studeaccount')->row()->CurrentBalance ?? 0);
}

public function getDiscount($studentNumber, $SY) {
    $this->db->select('Discount');
    $this->db->where('StudentNumber', $studentNumber);
    $this->db->where('SY', $SY);
    return (float) ($this->db->get('studeaccount')->row()->Discount ?? 0);
}

public function getAcctTotal($studentNumber, $SY) {
    $this->db->select('AcctTotal');
    $this->db->where('StudentNumber', $studentNumber);
    $this->db->where('SY', $SY);
    
    $result = $this->db->get('studeaccount')->row();
    
    // Ensure it returns 0 if no result is found
    return (float) ($result->AcctTotal ?? 0);
}

public function updateStudentAccount($studentNumber, $newTotalPayments, $newBalance, $SY) {
    $data = [
        'TotalPayments' => $newTotalPayments,
        'CurrentBalance' => $newBalance
    ];

    $this->db->where('StudentNumber', $studentNumber);
    $this->db->where('SY', $SY);

    $success = $this->db->update('studeaccount', $data);

    if (!$success) {
        log_message('error', "Failed to update studeaccount for Student {$studentNumber}, SY {$SY}");
        return false;
    }

    return true;
}



public function isORNumberExists($ORNumber)
{
    $this->db->where('ORNumber', $ORNumber);
    $query = $this->db->get('paymentsaccounts');
    return $query->num_rows() > 0;
}



public function get_provinces()
{
    $this->db->distinct();
    $this->db->select('Province');
    $this->db->order_by('Province', 'ASC');
    $query = $this->db->get('settings_address');
    return $query->result();
}

public function get_cities()
{
    $this->db->distinct();
    $this->db->select('City');
    $query = $this->db->get('settings_address');
    return $query->result();
    
}

public function get_barangays()
{
    $this->db->distinct();
    $this->db->select('Brgy');
    $query = $this->db->get('settings_address');
    return $query->result();
}


public function get_mis_settings()
{
    $this->db->select('division');
    $query = $this->db->get('mis_settings');
    return $query->row(); // Fetch a single row as an object
}



public function med_patient($logged_in_user, $user_sp)
{
    $scope = $this->resolve_patient_scope($logged_in_user, $user_sp);

    $this->db->select('*');
    $this->db->from('med_patient');

    if (!$scope['allow_all']) {
        if (!empty($scope['districts'])) {
            $this->db->group_start();
            $this->db->where_in('district', $scope['districts']);
            $this->db->or_where('print_Perm', 'Yes');
            $this->db->group_end();
        } elseif ($scope['use_username']) {
            $this->db->group_start();
            $this->db->where('username', $logged_in_user);
            $this->db->or_where('print_Perm', 'Yes');
            $this->db->group_end();
        }
    }

    $query = $this->db->get();
    return $query->result(); // Return filtered records
    }
    


    /**
     * Determine which records a sub-user can view.
     */
    private function resolve_patient_scope($logged_in_user, $user_sp)
    {
        $scope = [
            'allow_all'   => false,
            'districts'   => [],
            'use_username'=> false,
        ];

        if ($user_sp <= 0) {
            $scope['allow_all'] = true;
            return $scope;
        }

        $spRow = $this->db->get_where('users_sp', ['id' => $user_sp])->row();
        $position = $spRow->position ?? '';

        if ($position === 'Division Nurse') {
            $scope['allow_all'] = true; // Division nurses can view all records
            return $scope;
        }

        if ($position === 'Nurse') {
            $districtId = (int) $this->session->userdata('d_id');
            $districts  = $this->get_accessible_districts($districtId);

            if (!empty($districts)) {
                $scope['districts'] = $districts; // Limit to paired districts
                return $scope;
            }
        }

        // Default: fall back to the user's own submissions or print-permitted records
        $scope['use_username'] = true;
        return $scope;
    }

    /**
     * Return the district names a nurse is allowed to view, pairing North/South municipalities.
     */
    private function get_accessible_districts($districtId)
    {
        if ($districtId <= 0) {
            return [];
        }

        $pairMap = [
            1  => [1, 2],  // Baganga North/South
            2  => [1, 2],
            5  => [5, 6],  // Caraga North/South
            6  => [5, 6],
            7  => [7, 8],  // Cateel 1/2
            8  => [7, 8],
            9  => [9, 10], // Gov. Generoso North/South
            10 => [9, 10],
            11 => [11, 12],// Lupon East/West
            12 => [11, 12],
            13 => [13, 14],// Manay North/South
            14 => [13, 14],
            15 => [15, 16],// San Isidro North/South
            16 => [15, 16],
        ];

        $ids = $pairMap[$districtId] ?? [$districtId];

        $this->db->select('discription');
        $this->db->from('district');
        $this->db->where_in('id', $ids);
        $results = $this->db->get()->result_array();

        return array_column($results, 'discription');
    }

    public function med_patient_student($logged_in_user, $user_sp)
    {
        $scope = $this->resolve_patient_scope($logged_in_user, $user_sp);

        $this->db->select('*');
        $this->db->from('med_patient');

        if (!$scope['allow_all']) {
            if (!empty($scope['districts'])) {
                $this->db->group_start();
                $this->db->where_in('district', $scope['districts']);
                $this->db->or_where('print_Perm', 'Yes');
                $this->db->group_end();
            } elseif ($scope['use_username']) {
                $this->db->group_start();
                $this->db->where('username', $logged_in_user);
                $this->db->or_where('print_Perm', 'Yes');
                $this->db->group_end();
            }
        }

        $query = $this->db->get();
        return $query->result(); // Return filtered records
    }
    


public function get_med_patient_summary()
{
    $this->db->select('COALESCE(NULLIF(schools.district, \'\'), NULLIF(med_patient.district, \'\'), \'Unassigned\') AS district', false);
    $this->db->select('
        SUM(CASE WHEN med_patient.patientType = "Employee" THEN 1 ELSE 0 END) AS employee_count,
        SUM(CASE WHEN med_patient.patientType = "Student" THEN 1 ELSE 0 END) AS student_count
    ', false);
    $this->db->from('med_patient');
    $this->db->join('schools', 'schools.schoolName = med_patient.school', 'left');
    $this->db->group_by('district');
    $this->db->order_by('district', 'ASC');
    $query = $this->db->get();
    return $query->result();
}


public function get_nurse_masterlist($userId)
{
    $this->db->select('users.id, users.username, users.fname, users.mname, users.lname, users.address, users.sex, users.sp, users.d_id, district.discription AS district_name, users_sp.position AS sp_position');
    $this->db->from('users');
    $this->db->join('users_sp', 'users_sp.id = users.sp', 'left');
    $this->db->join('district', 'district.id = users.d_id', 'left');
    $this->db->where('users.user_id', $userId);
    $this->db->where_in('users_sp.position', ['Nurse', 'Division Nurse']);
    $this->db->order_by('district.discription', 'ASC');
    $this->db->order_by('users_sp.position', 'ASC');
    $this->db->order_by('users.lname', 'ASC');
    $query = $this->db->get();
    return $query->result();
}

public function get_patient_counts_by_usernames($usernames)
{
    $counts = [
        'Employee' => 0,
        'Student' => 0,
        'Total' => 0,
    ];

    if (empty($usernames)) {
        return $counts;
    }

    $this->db->select('patientType, COUNT(*) AS count');
    $this->db->from('med_patient');
    $this->db->where_in('username', $usernames);
    $this->db->group_by('patientType');
    $query = $this->db->get();

    foreach ($query->result() as $row) {
        if (!empty($row->patientType)) {
            $counts[$row->patientType] = (int) $row->count;
        }
    }

    $counts['Total'] = $counts['Employee'] + $counts['Student'];
    return $counts;
}

public function get_patients_by_usernames($usernames, $patientType = null)
{
    if (empty($usernames)) {
        return [];
    }

    $this->db->select('*');
    $this->db->from('med_patient');
    $this->db->where_in('username', $usernames);
    if (!empty($patientType)) {
        $this->db->where('patientType', $patientType);
    }
    $this->db->order_by('appdate', 'DESC');
    $query = $this->db->get();
    return $query->result();
}


public function get_med_patient_list($patientType, $district)
{
    $this->db->select('med_patient.*');
    $this->db->from('med_patient');
    $this->db->join('schools', 'schools.schoolName = med_patient.school', 'left');
    $this->db->where('med_patient.patientType', $patientType);

    if ($district === 'Unassigned') {
        $this->db->group_start();
        $this->db->where("(med_patient.district IS NULL OR med_patient.district = '')", null, false);
        $this->db->where("(schools.district IS NULL OR schools.district = '')", null, false);
        $this->db->group_end();
    } else {
        $this->db->group_start();
        $this->db->where('schools.district', $district);
        $this->db->or_where('med_patient.district', $district);
        $this->db->group_end();
    }

    $query = $this->db->get();
    return $query->result();
}


public function insertPatient() {
    $this->db->insert('med_patient');
}

public function med_patient_report($medID)
{
    $this->db->select('*');
    $this->db->from('med_patient');
    $this->db->where('medID', $medID);
    $query = $this->db->get();

    if ($query->num_rows() > 0) {
        return $query->result();
    } else {
        return []; // Return an empty array if no data is found
    }
}

public function mis_settings()
{
    $this->db->select('*');
    $this->db->from('mis_settings'); // No filtering on medID
    $query = $this->db->get();

    return $query->result();
}


public function med_settings()
{
    $this->db->select('*');
    $this->db->from('med_settings'); // No filtering on medID
    $query = $this->db->get();

    return $query->result();
}



public function get_medIDs_by_position($position)
{
    $this->db->select('medID');
    $this->db->from('med_patient');
    $this->db->where('position', $position); // Match the position

    $query = $this->db->get();
    
    if ($query->num_rows() > 0) {
        return array_column($query->result_array(), 'medID'); // Return an array of medIDs
    } else {
        return []; // Return empty array if no match
    }
}


// public function med_count_by_status($medIDs, $status)
// {
//     if (empty($medIDs)) {
//         return 0; // No medIDs found
//     }

//     $this->db->where_in('medID', $medIDs);
//     $this->db->where('consultationStat', $status);
//     return $this->db->count_all_results('med_patient'); // Return count
// }



public function district()
{
    $this->db->select('discription');
    $query = $this->db->get('district');
    return $query->result();
}

public function staff()
{
    $this->db->select('IDNumber, FirstName, MiddleName, LastName');
    $query = $this->db->get('hris_staff');
    return $query->result();
}

public function school()
{
    $this->db->select('schoolName');
    $query = $this->db->get('schools');
    return $query->result();
}



public function category()
{
    $this->db->distinct();
    $this->db->select('category');
    $query = $this->db->get('med_disease');
    return $query->result();
}

public function disease($category = null)
{
    $this->db->select('disease');
    $this->db->from('med_disease');
    
    if ($category) {
        $this->db->where('category', $category);
    }

    $query = $this->db->get();
    return $query->result();
}


public function disposition()
{
    $this->db->select('disposition');
    $query = $this->db->get('med_disposition');
    return $query->result();
}



public function med_labrequest()
{
    $this->db->select('*');
    $query = $this->db->get('med_labrequest');
    return $query->result();
}

public function count_consultation($IDNumber)
{
    $this->db->select('*');
    $this->db->from('med_patient');
    $this->db->where('IDNumber', $IDNumber);
    $query = $this->db->get();
    return $query->result();
}


public function count_consultation1($LRN)
{
    $this->db->select('*');
    $this->db->from('med_patient');
    $this->db->where('LRN', $LRN); // Only filter by IDNumber
    $query = $this->db->get();
    return $query->result();
}


public function med_settings_info()
{
    $this->db->select('*');
    $query = $this->db->get('med_settings');
    return $query->result();
}

public function update_signature($med_id, $signatureFile)
{
    $data = array('signature' => $signatureFile);
    $this->db->where('med_id', $med_id);
    return $this->db->update('med_settings', $data);
}


public function get_employees() {
    return $this->db->get_where('patients', ['type' => 'Employee'])->result();
}

public function get_students() {
    return $this->db->get_where('patients', ['type' => 'Student'])->result();
}




public function med_labPatient()
{
    $this->db->select('*');
    $query = $this->db->get('med_labrequest');
    return $query->result();
}


public function batch_save_requests($data) {
    return $this->db->insert_batch('med_labrequest', $data);
}



public function delete_labReq($labID) {
    $this->db->where('labID', $labID);
    return $this->db->delete('med_labrequest');
}


    
public function applicantList()
{
    $this->db->select('a.pre_school, a.district, a.empEmail, a.applicant_id, a.appStatus, a.jobID,
                       b.schoolName, b.schoolID, 
                       c.FirstName, c.MiddleName, c.LastName, 
                       d.jobTitle, d.job_type'); // Added jobTitle from hris_jobvacancy
    $this->db->from('hris_applications a');
    $this->db->join('schools b', 'b.schoolID = a.pre_school');
    $this->db->join('hris_applicant c', 'c.empEmail = a.empEmail'); // Join with applicants table
    $this->db->join('hris_jobvacancy d', 'd.jobID = a.jobID'); // Join with job vacancy table
    $this->db->where_in('a.appStatus', ['Endorsed for Rating', 'Validated']);
    $this->db->order_by('a.district, b.schoolName');

    $query = $this->db->get();
    return $query->result();
}



public function getEndorsedApplicantsByJob($jobID)
{
    $this->db->select('a.pre_school, a.district, a.empEmail, a.applicant_id, a.appStatus, a.jobID,
                       b.schoolName, b.schoolID, 
                       c.FirstName, c.MiddleName, c.LastName, c.perBarangay, c.perCity, c.perProvince, c.perZipCode, c.specialization, c.track, c.resBarangay, c.resCity, c.resProvince, c.resZipCode,
                       d.jobTitle, d.job_type');
    $this->db->from('hris_applications a');
    $this->db->join('schools b', 'b.schoolID = a.pre_school');
    $this->db->join('hris_applicant c', 'c.empEmail = a.empEmail');
    $this->db->join('hris_jobvacancy d', 'd.jobID = a.jobID');
    $this->db->where('a.appStatus', 'Endorsed for Rating');
    $this->db->where('a.jobID', $jobID);
    $this->db->order_by('b.schoolName');

    $query = $this->db->get();
    return $query->result();
}

public function getRatedApplicantsByJob($jobID)
{
    $this->db->select('a.pre_school, a.district, a.empEmail, a.applicant_id, a.appStatus, a.jobID,
                       b.schoolName, b.schoolID, 
                       c.FirstName, c.MiddleName, c.LastName, c.perBarangay, c.perCity, c.perProvince, c.perZipCode, c.specialization, c.track, c.resBarangay, c.resCity, c.resProvince, c.resZipCode,
                       d.jobTitle, d.job_type');
    $this->db->from('hris_applications a');
    $this->db->join('schools b', 'b.schoolID = a.pre_school');
    $this->db->join('hris_applicant c', 'c.empEmail = a.empEmail');
    $this->db->join('hris_jobvacancy d', 'd.jobID = a.jobID');
    $this->db->where('a.appStatus', 'Rated');
    $this->db->where('a.jobID', $jobID);
    $this->db->order_by('b.schoolName');

    $query = $this->db->get();
    return $query->result();
}


public function getvalidatedApplicantsByJob($jobID)
{
    $this->db->select('a.pre_school, a.district, a.empEmail, a.applicant_id, a.appStatus, a.jobID,
                       b.schoolName, b.schoolID, 
                       c.FirstName, c.MiddleName, c.LastName, c.perBarangay, c.perCity, c.perProvince, c.perZipCode, c.specialization, c.track, c.resBarangay, c.resCity, c.resProvince, c.resZipCode,
                       d.jobTitle, d.job_type');
    $this->db->from('hris_applications a');
    $this->db->join('schools b', 'b.schoolID = a.pre_school');
    $this->db->join('hris_applicant c', 'c.empEmail = a.empEmail');
    $this->db->join('hris_jobvacancy d', 'd.jobID = a.jobID');
    $this->db->where('a.appStatus', 'Validated');
    $this->db->where('a.jobID', $jobID);
    $this->db->order_by('a.district, b.schoolName');

    $query = $this->db->get();
    return $query->result();
}



    public function getsubmittedApplicantsByJob($jobID)
    {
        $this->db->select('a.pre_school, a.district, a.empEmail, a.applicant_id, a.appStatus, a.jobID,
                       b.schoolName, b.schoolID, 
                       c.FirstName, c.MiddleName, c.LastName, c.perBarangay, c.perCity, c.perProvince, c.perZipCode, c.specialization, c.track, c.resBarangay, c.resCity, c.resProvince, c.resZipCode,
                       d.jobTitle, d.job_type');
        $this->db->from('hris_applications a');
    $this->db->join('schools b', 'b.schoolID = a.pre_school');
    $this->db->join('hris_applicant c', 'c.empEmail = a.empEmail');
    $this->db->join('hris_jobvacancy d', 'd.jobID = a.jobID');
    $this->db->where('a.appStatus', 'Application Submitted');
    $this->db->where('a.jobID', $jobID);
    $this->db->order_by('a.district, b.schoolName');

        $query = $this->db->get();
        return $query->result();
    }


    /**
     * Get applicant counts by district for a specific job posting.
     * Columns: district_name, total_applicants, validated_applicants, retained_applicants.
     */
    public function getApplicantCountsByDistrict($jobID)
    {
        $jobID = (int) $jobID;

        // Normalize blank districts to a friendly label to keep grouping clean.
        $districtExpr = "COALESCE(NULLIF(TRIM(a.district), ''), 'Unassigned')";

        $this->db->select($districtExpr . ' AS district_name', false);
        $this->db->select('COUNT(*) AS total_applicants', false);
        $this->db->select("SUM(CASE WHEN a.appStatus = 'Validated' THEN 1 ELSE 0 END) AS validated_applicants", false);
        $this->db->select("SUM(CASE WHEN a.appStatus IN ('Endorsed for Rating','Rated') THEN 1 ELSE 0 END) AS retained_applicants", false);
        $this->db->from('hris_applications a');
        $this->db->where('a.jobID', $jobID);
        $this->db->group_by($districtExpr, false);
        $this->db->order_by('district_name', 'ASC');

        return $this->db->get()->result();
    }


public function med_count_by_status($status)
{
    $this->db->where('consultationStat', $status);
    $this->db->from('med_patient');

    return $this->db->count_all_results(); // Get count of all matching records
}



public function med_count_by_user_status($username, $status)
{
    $this->db->where('username', $username);
    $this->db->where('consultationStat', $status);
    $this->db->from('med_patient');

    return $this->db->count_all_results(); // Get count of matching records
}



public function med_disease()
{
    $this->db->select('*');
    $query = $this->db->get('med_disease');
    return $query->result();
}

public function med_disposition()
{
    $this->db->select('*');
    $query = $this->db->get('med_disposition');
    return $query->result();
}


public function med_staff()
{
    $this->db->select('*');
    $query = $this->db->get('hris_staff');
    return $query->result();
}


public function schools()
{
    $this->db->distinct();
    $this->db->select('schoolName');
    $query = $this->db->get('schools');
    return $query->result();
}

public function districts()
{
    $this->db->distinct();
    $this->db->select('district');
    $query = $this->db->get('schools');
    return $query->result();
}

public function med_health_exam()
{
    $this->db->select('*');
    $query = $this->db->get('med_health_exam');
    return $query->result();
}


// public function med_staff()
// {
//     $this->db->select('hris_staff.*, schools.schoolName, schools.district');
//     $this->db->from('hris_staff');
//     $this->db->join('schools', 'schools.schoolID = hris_staff.schoolID', 'left');
//     $query = $this->db->get();
//     return $query->result();
// }

public function save_health_exam($data)
{
    return $this->db->insert('med_health_exam', $data);
}


public function get_med_health_records()
{
    $this->db->select('*');
    $this->db->from('med_health_exam');
    $query = $this->db->get();
    return $query->result();
}

public function get_med_health_record_by_id($healthID)
{
    $this->db->select('*');
    $this->db->from('med_health_exam');
    $this->db->where('healthID', $healthID);
    $query = $this->db->get();
    return $query->row(); // single record
}


public function updateRecord($id, $data)
{
  $this->db->where('healthID', $id);
  $this->db->update('med_health_exam', $data);
}

public function getRecordById($id)
{
    return $this->db->get_where('med_health_exam', ['healthID' => $id])->row();
}


public function deleteRecord($id)
{
    $this->db->where('healthID', $id);
    $this->db->delete('med_health_exam');
}

public function lab_count_all()
{
    return (int) $this->db
        ->from('med_labrequest')
        ->count_all_results();
}

public function lab_count_today()
{
    $today = date('Y-m-d');
    return (int) $this->db
        ->from('med_labrequest')
        ->like('date_request', $today, 'after')
        ->count_all_results();
}

public function lab_count_by_status($status)
{
    return (int) $this->db
        ->from('med_labrequest')
        ->where('consultationStat', $status)
        ->count_all_results();
}

public function health_exam_count_all()
{
    return (int) $this->db
        ->from('med_health_exam')
        ->count_all_results();
}

public function disease_category_tally()
{
    $this->db->select('category, COUNT(*) AS total', false);
    $this->db->from('med_disease');
    $this->db->where("category IS NOT NULL AND category <> ''", null, false);
    $this->db->where("disease IS NOT NULL AND disease <> ''", null, false);
    $this->db->group_by('category');
    $this->db->order_by('category', 'ASC');
    return $this->db->get()->result();
}

public function diseases_by_category($category)
{
    $this->db->distinct();
    $this->db->select('dID, category, disease');
    $this->db->from('med_disease');
    $this->db->where('category', $category);
    $this->db->where("disease IS NOT NULL AND disease <> ''", null, false);
    $this->db->order_by('disease', 'ASC');
    return $this->db->get()->result();
}

public function case_category_tally($logged_in_user, $user_sp, $limit = 10)
{
    $scope = $this->resolve_patient_scope($logged_in_user, $user_sp);

    $this->db->select('category, COUNT(*) AS total', false);
    $this->db->from('med_patient');
    $this->db->where("category IS NOT NULL AND category <> ''", null, false);

    if (!$scope['allow_all']) {
        if (!empty($scope['districts'])) {
            $this->db->group_start();
            $this->db->where_in('district', $scope['districts']);
            $this->db->or_where('print_Perm', 'Yes');
            $this->db->group_end();
        } elseif ($scope['use_username']) {
            $this->db->group_start();
            $this->db->where('username', $logged_in_user);
            $this->db->or_where('print_Perm', 'Yes');
            $this->db->group_end();
        }
    }

    $this->db->group_by('category');
    $this->db->order_by('total', 'DESC');
    if (!empty($limit)) { $this->db->limit((int) $limit); }

    return $this->db->get()->result();
}

public function case_disease_tally($category, $logged_in_user, $user_sp, $limit = 10)
{
    $scope = $this->resolve_patient_scope($logged_in_user, $user_sp);

    $this->db->select('disease, COUNT(*) AS total', false);
    $this->db->from('med_patient');
    $this->db->where('category', $category);
    $this->db->where("disease IS NOT NULL AND disease <> ''", null, false);

    if (!$scope['allow_all']) {
        if (!empty($scope['districts'])) {
            $this->db->group_start();
            $this->db->where_in('district', $scope['districts']);
            $this->db->or_where('print_Perm', 'Yes');
            $this->db->group_end();
        } elseif ($scope['use_username']) {
            $this->db->group_start();
            $this->db->where('username', $logged_in_user);
            $this->db->or_where('print_Perm', 'Yes');
            $this->db->group_end();
        }
    }

    $this->db->group_by('disease');
    $this->db->order_by('total', 'DESC');
    if (!empty($limit)) { $this->db->limit((int) $limit); }

    return $this->db->get()->result();
}

public function illness_tally($limit = 10)
{
    $this->db->select('classification AS category, COUNT(*) AS total', false);
    $this->db->from('med_health_exam');
    $this->db->where("classification IS NOT NULL AND classification <> ''", null, false);
    $this->db->group_by('classification');
    $this->db->order_by('total', 'DESC');
    if (!empty($limit)) { $this->db->limit((int)$limit); }
    return $this->db->get()->result();
}

public function illness_tally_range($startDate, $endDate, $limit = 10)
{
    $this->db->select('classification AS category, COUNT(*) AS total', false);
    $this->db->from('med_health_exam');
    $this->db->where('exam_date >=', $startDate);
    $this->db->where('exam_date <=', $endDate);
    $this->db->where("classification IS NOT NULL AND classification <> ''", null, false);
    $this->db->group_by('classification');
    $this->db->order_by('total', 'DESC');
    if (!empty($limit)) { $this->db->limit((int)$limit); }
    return $this->db->get()->result();
}


}
