<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

class Main_model extends CI_Model
{

	function insertRecord($record)
	{
		if (count($record) > 0) {

			$schoolID = $this->session->userdata('user_id');

			// Check if student already exists
			$this->db->where('StudentNumber', trim($record[0]));
			$exists = $this->db->get('studeprofile')->num_rows() > 0;

			// Insert record if not exists
			if (!$exists) {

				$newProfile =  [
					"StudentNumber" => trim($record[0]),
					"LRN" => trim($record[1]),
					"FirstName" => trim($record[2]),
					"MiddleName" => trim($record[3]),
					"LastName" => trim($record[4]),
					"nameExt" => trim($record[5]),
					"Sex" => trim($record[6]),
					"CivilStatus" => trim($record[7]),
					"BirthPlace" => trim($record[8]),
					"Religion" => trim($record[9]),
					"MobileNumber" => trim($record[10]),
					"BirthDate" => trim($record[11]),
					"Province" => trim($record[12]),
					"City" => trim($record[13]),
					"Brgy" => trim($record[14]),
					"Sitio" => trim($record[15]),
					"Ethnicity" => trim($record[16]),
					 "schoolID" => $schoolID,
				];

				$enrollment =  [
					"StudentNumber" => trim($record[0]),
					"Course" => trim($record[20]),
					"YearLevel" => trim($record[21]),
					"Section" => trim($record[22]),
					"SY" => trim($record[23]),
					"Semester" => '',
					"Status" => 'Enrolled',
					"EnrolledDate" => date('Y-m-d'),
					"schoolID" => $schoolID,
				];

				$this->db->insert('studeprofile', $newProfile);
				$this->db->insert('semesterstude', $enrollment);
			}
		}
	}

	function insertTeachers($record)
	{
		if (count($record) > 0) {

			// Check user
			$this->db->select('*');
			$this->db->where('IDNumber', $record[0]);
			$q = $this->db->get('staff');
			$response = $q->result_array();

			// Insert record
			if (count($response) == 0) {
				$newprofile = array(
					"IDNumber" => trim($record[0]),
					"FirstName" => trim($record[1]),
					"MiddleName" => trim($record[2]),
					"LastName" => trim($record[3]),
					"NameExtn" => trim($record[4]),
					"empPosition" => trim($record[5]),
					"Department" => trim($record[6]),
					"MaritalStatus" => trim($record[7]),
					"empStatus" => trim($record[8]),
					"BirthDate" => trim($record[9]),
					"BirthPlace" => trim($record[10]),
					"Sex" => trim($record[11]),
					"bloodType" => trim($record[12]),
					"pagibig" => trim($record[13]),
					"philHealth" => trim($record[14]),
					"sssNo" => trim($record[15]),
					"tinNo" => trim($record[16]),
					"dateHired" => trim($record[17]),
					"resVillage" => trim($record[18]),
					"resBarangay" => trim($record[19]),
					"resCity" => trim($record[20]),
					"resProvince" => trim($record[21]),
					"resZipCode" => trim($record[22]),
					"perVillage" => trim($record[18]),
					"perBarangay" => trim($record[19]),
					"perCity" => trim($record[20]),
					"perProvince" => trim($record[21]),
					"perZipCode" => trim($record[22]),
					"empMobile" => trim($record[23]),
					"empEmail" => trim($record[24]),
					"settingsID" => '1',
				);

				$newUsers = array(
					"username" => trim($record[0]),
					"password" => sha1(trim($record[9])),
					"position" => 'Teacher',
					"fName" => trim($record[1]),
					"mName" => trim($record[2]),
					"lName" => trim($record[3]),
					"email" => trim($record[24]),
					"avatar" => 'avatar.png',
					"acctStat" => 'active',
					"dateCreated" => date('Y-m-d'),
					"IDNumber" => trim($record[0]),

				);

				$this->db->insert('staff', $newprofile);
				$this->db->insert('users', $newUsers);
			}
		}
	}

	function insertCourses($record)
	{

		if (count($record) > 0) {

			// Check user
			$this->db->select('*');
			$this->db->where('CourseCode', $record[0]);
			$q = $this->db->get('course_table');
			$response = $q->result_array();

			// Insert record
			if (count($response) == 0) {
				$newcourse = array(
					"CourseCode" => trim($record[0]),
					"CourseDescription" => trim($record[1]),
					"Major" => trim($record[2]),
					"Duration" => trim($record[3]),
				);
				$this->db->insert('course_table', $newcourse);
			}
		}
	}

	function insertSections($record)
	{

		if (count($record) > 0) {

			// Check user
			$this->db->select('*');
			$this->db->where('Section', $record[0]);
			$q = $this->db->get('sections');
			$response = $q->result_array();

			// Insert record
			if (count($response) == 0) {
				$newsection = array(
					"Section" => trim($record[0]),
					"YearLevel" => trim($record[1]),
					"Adviser" => trim($record[2]),

				);
				$this->db->insert('sections', $newsection);
			}
		}
	}

	function insertSubjects($record)
	{

		if (count($record) > 0) {

			// Check user
			$this->db->select('*');
			$this->db->where('SubjectCode', $record[0]);
			$this->db->where('description', $record[1]);
			$this->db->where('Course', $record[8]);
			$q = $this->db->get('subjects');
			$response = $q->result_array();

			// Insert record
			if (count($response) == 0) {
				$newsubject = array(
					"SubjectCode" => trim($record[0]),
					"description" => trim($record[1]),
					"lecunit" => trim($record[2]),
					"labunit" => trim($record[3]),
					"prereq" => trim($record[4]),
					"curriculum" => trim($record[5]),
					"YearLevel" => trim($record[6]),
					"Semester" => trim($record[7]),
					"Course" => trim($record[8]),
					"SemEffective" => trim($record[9]),
					"SYEffective" => trim($record[10]),
					"Effectivity" => trim($record[11]),
					"subCategory" => trim($record[13]),


				);
				$this->db->insert('subjects', $newsubject);
			}
		}
	}

	function insertFees($record)
	{

		if (count($record) > 0) {

			// Check user
			$this->db->select('*');
			$this->db->where('Description', $record[0]);
			$this->db->where('Course', $record[2]);
			$this->db->where('YearLevel', $record[4]);
			$this->db->where('Semester', $record[5]);
			$q = $this->db->get('fees');
			$response = $q->result_array();

			// Insert record
			if (count($response) == 0) {
				$fees = array(
					"feesid" => trim('0'),
					"Description" => trim($record[0]),
					"Amount" => trim($record[1]),
					"Course" => trim($record[2]),
					"Major" => trim($record[3]),
					"YearLevel" => trim($record[4]),
					"Semester" => trim($record[5]),
					"feesType" => trim($record[6]),

				);
				$this->db->insert('fees', $fees);
			}
		}
	}
}
