<?php

class Hiring_model extends CI_Model
{

    public function __construct()
    {
        $this->load->database();
    }

    public function getApplications()
{
    $this->db->select("
        s.schoolID,
        s.schoolName,
        s.district,

        -- count only if vacancy exists and NOT Closed
        SUM(CASE
            WHEN j.jobID IS NOT NULL THEN 1
            ELSE 0
        END) AS total_applicants,

        -- count all applications EXCEPT 'Application Submitted'
        SUM(CASE
            WHEN j.jobID IS NOT NULL
             AND a.appStatus <> 'Application Submitted'
            THEN 1
            ELSE 0
        END) AS total_validated
    ", false);

    $this->db->from('schools s');
    $this->db->join('hris_applications a', 'a.pre_school = s.schoolID', 'left');

    $this->db->join(
        'hris_jobvacancy j',
        'j.jobID = a.jobID
         AND j.position = 1
         AND j.promotion = 0
         AND j.jvStatus <> "Closed"',
        'left'
    );

    $this->db->group_by(['s.schoolID', 's.schoolName', 's.district']);
    $this->db->order_by('s.schoolName', 'ASC');

    return $this->db->get()->result();
}

public function get_submitted_applicant($jobID)
{
    $this->db->select("
        a.*,
        j.jvStatus, j.jobID, j.job_type, j.jobTitle,

        COALESCE(app.record_no, staff.IDNumber) AS code,
        COALESCE(app.id, staff.IDNumber) AS id,
        COALESCE(app.empEmail, staff.IDNumber) AS renren,
        COALESCE(app.contactNo, staff.contactNo) AS contactNo,
        COALESCE(app.FirstName, staff.FirstName) AS FirstName,
        COALESCE(app.MiddleName, staff.MiddleName) AS MiddleName,
        COALESCE(app.NameExtn, staff.NameExtn) AS NameExtn,
        COALESCE(app.LastName, staff.LastName) AS LastName,
        COALESCE(app.resProvince, staff.resProvince) AS province,
        COALESCE(app.resCity, staff.resCity) AS resCity,
        COALESCE(app.resBarangay, staff.resBarangay) AS brgy,
        COALESCE(app.age, staff.age) AS age,
        COALESCE(app.Sex, staff.Sex) AS sex,
        COALESCE(app.MaritalStatus, staff.MaritalStatus) AS ms,
        COALESCE(app.empEmail, staff.empEmail) AS email,
        COALESCE(app.empMobile, staff.empMobile) AS cn,
        COALESCE(app.bd, staff.bd) AS bachelor,
        COALESCE(app.religion, staff.religion) AS religion,
        COALESCE(app.disability, staff.disability) AS disability,
        COALESCE(app.ethnicity, staff.ethnicity) AS ethnicity,
        COALESCE(app.csEligibility, staff.csEligibility) AS csEligibility,

        CASE
            WHEN app.record_no IS NOT NULL THEN 'profile_reg_edit'
            WHEN staff.IDNumber IS NOT NULL THEN 'employee_edit'
            ELSE 'unknown'
        END AS st,

        CASE
            WHEN rr.app_id IS NOT NULL THEN 'Requested for retention of rating'
            ELSE ''
        END AS note
    ");

    $this->db->from('hris_applications a');

    $this->db->join(
        "(SELECT x.*
          FROM hris_applicant x
          INNER JOIN (
              SELECT empEmail, MAX(id) AS max_id
              FROM hris_applicant
              GROUP BY empEmail
          ) m ON m.empEmail = x.empEmail AND m.max_id = x.id
        ) app",
        "a.empEmail = app.empEmail",
        "left",
        false
    );

    $this->db->join('hris_staff staff', 'app.record_no IS NULL AND a.empEmail = staff.IDNumber', 'left');
    $this->db->join('hris_jobvacancy j', 'a.jobID = j.jobID', 'left');

    $this->db->join(
        '(SELECT DISTINCT app_id FROM hris_rating_request) rr',
        'rr.app_id = a.appID',
        'left',
        false
    );

    $this->db->where('a.jobID', $jobID);
    $this->db->order_by('a.dq', 'ASC');


    return $this->db->get()->result();
}

function JobVancancies()
	{
		$query = $this->db->query("select * from hris_jobvacancy where jvStatus='Open' and promotion=0 and position=1 order by jobID desc");
		return $query->result();
	}


    public function get_grouped_applicants_by_mun_ierv2($jobID)
{
    $this->db->select("
        a.*,

        r.educ, r.trainings, r.experience, r.performance, r.oa, r.ae, r.ald, r.interview, r.written,
        r.record_no, r.appID, r.fy, r.job_type, r.total_points, r.skills,

        COALESCE(app.record_no, staff.IDNumber) AS code,
        COALESCE(app.empEmail, staff.empEmail) AS renren,
        COALESCE(app.contactNo, staff.contactNo) AS contactNo,

        COALESCE(app.FirstName, staff.FirstName) AS FirstName,
        COALESCE(app.LastName, staff.LastName) AS LastName,
        COALESCE(app.MiddleName, staff.MiddleName) AS MiddleName,

        COALESCE(app.jhss, staff.jhss) AS jhss,
        COALESCE(app.shss, staff.shss) AS shss,

        COALESCE(app.resCity, staff.resCity) AS resCity,
        COALESCE(app.resBarangay, staff.resBarangay) AS brgy,
        COALESCE(app.resProvince, staff.resProvince) AS province,

        COALESCE(app.age, staff.age) AS age,
        COALESCE(app.sex, staff.sex) AS sex,
        COALESCE(app.MaritalStatus, staff.MaritalStatus) AS ms,
        COALESCE(app.bd, staff.bd) AS bachelor,

        COALESCE(app.empEmail, staff.empEmail) AS email,
        COALESCE(app.religion, staff.religion) AS religion,
        COALESCE(app.ethnicity, staff.ethnicity) AS ethnicity,
        COALESCE(app.disability, staff.disability) AS disability,

        CASE 
            WHEN rr_status.app_id IS NOT NULL THEN 'Update Credentials'
            ELSE ''
        END AS retain_status
    ", false);

    $this->db->from('hris_applications a');

    $this->db->join('hris_jobvacancy j', 'j.jobID = a.jobID');
    $this->db->join('hris_applicant app', 'a.empEmail = app.empEmail', 'left');
    $this->db->join('hris_staff staff', 'app.record_no IS NULL AND a.empEmail = staff.IDNumber', 'left');
    $this->db->join('hris_rating_none r', 'a.appID = r.appID', 'left');

    // Exclude applications that have r_type = 1
    $this->db->join(
        'hris_rating_request rr_hide',
        'rr_hide.app_id = a.appID AND rr_hide.r_type = 1',
        'left'
    );

    // For retain_status
    $this->db->join(
        'hris_rating_request rr_status',
        'rr_status.app_id = a.appID AND rr_status.r_type = 2',
        'left'
    );

    $this->db->where('a.jobID', $jobID);
    $this->db->where('a.dq', 1);

    // Hide if may r_type = 1
    $this->db->where('rr_hide.app_id IS NULL', null, false);

    $this->db->order_by('resCity', 'ASC');
    $this->db->order_by('jhss', 'ASC');
    $this->db->order_by('r.total_points', 'DESC');

    $results = $this->db->get()->result();

    $grouped = [];
    foreach ($results as $row) {
        $municipalityRaw   = $row->district ?: 'Undefined';
        $municipalityClean = preg_replace('/\s+/', ' ', trim($municipalityRaw));
        $municipality      = ucwords(strtolower($municipalityClean));

        $jhssRaw   = $row->jhss ?: 'Undefined';
        $jhssClean = preg_replace('/\s+/', ' ', trim($jhssRaw));
        $jhss      = ucwords(strtolower($jhssClean));

        $grouped[$municipality][$jhss][] = $row;
    }

    return $grouped;
}

public function get_granted_rating_request($fy,$stat)
    {
        $this->db->select("
        a.*, a.id as rid,
        j.jvStatus,j.jobID,j.job_type,j.jobTitle,j.sy,j.position AS job_position,
        r.appID,r.pre_school,
        COALESCE(app.record_no, staff.IDNumber) AS code,
        COALESCE(app.id, staff.IDNumber) AS id,
        COALESCE(app.empEmail, staff.IDNumber) AS renren,
        COALESCE(app.contactNo, staff.contactNo) AS contactNo,
        COALESCE(app.FirstName, staff.FirstName) AS FirstName,
        COALESCE(app.MiddleName, staff.MiddleName) AS MiddleName,
        COALESCE(app.NameExtn, staff.NameExtn) AS NameExtn,
        COALESCE(app.LastName, staff.LastName) AS LastName,
        COALESCE(app.resProvince, staff.resProvince) AS province,
        COALESCE(app.resCity, staff.resCity) AS resCity,
        COALESCE(app.resBarangay, staff.resBarangay) AS brgy,
        COALESCE(app.age, staff.age) AS age,
        COALESCE(app.Sex, staff.Sex) AS sex,
        COALESCE(app.MaritalStatus, staff.MaritalStatus) AS ms,
        COALESCE(app.empEmail, staff.empEmail) AS email,
        COALESCE(app.contactNo, staff.contactNo) AS cn,
        CASE
            WHEN app.record_no IS NOT NULL THEN 'ma'
            WHEN staff.IDNumber IS NOT NULL THEN 'ma_staff'
            ELSE 'unknown'
        END AS st
    ");
    $this->db->from('hris_rating_request a');
    $this->db->join('hris_applicant app', 'a.applicant_id = app.id', 'left');
    $this->db->join('hris_staff staff', 'app.id IS NULL AND a.applicant_id = staff.IDNumber', 'left');
    $this->db->join('hris_jobvacancy j', 'a.job_id = j.jobID', 'left');
    $this->db->join('hris_applications r', 'a.app_id = r.appID', 'left');
    $this->db->where('j.jvStatus','Open');
    $this->db->where('a.fy', $fy);
    $this->db->where('a.stat', $stat);

    $query = $this->db->get();
    return $query->result();
    }

    public function rated_applicant()
    {
    $this->db->select("
        a.*,
        r.education, r.training, r.experience, r.let_rating, r.tr_rating, r.demo_rating,
        jv.*,
        COALESCE(app.record_no, staff.IDNumber) AS code,
        COALESCE(app.id, staff.IDNumber) AS id,
        COALESCE(app.empEmail, staff.IDNumber) AS renren,
        COALESCE(app.contactNo, staff.contactNo) AS contactNo,
        COALESCE(app.FirstName, staff.FirstName) AS FirstName,
        COALESCE(app.MiddleName, staff.MiddleName) AS MiddleName,
        COALESCE(app.NameExtn, staff.NameExtn) AS NameExtn,
        COALESCE(app.LastName, staff.LastName) AS LastName,
        COALESCE(app.perProvince, staff.perProvince) AS province,
        COALESCE(app.jhss, staff.jhss) AS jhss,
        COALESCE(app.shss, staff.shss) AS shss,
        COALESCE(app.resCity, staff.resCity) AS resCity,
        COALESCE(app.resBarangay, staff.resBarangay) AS brgy,
        CASE
            WHEN app.record_no IS NOT NULL THEN 'ma'
            WHEN staff.IDNumber IS NOT NULL THEN 'ma_staff'
            ELSE 'unknown'
        END AS st
        
    ");

    $this->db->from('hris_applications a');
    $this->db->join('hris_applicant app', 'a.empEmail = app.empEmail', 'left');
    $this->db->join('hris_staff staff', 'app.record_no IS NULL AND a.empEmail = staff.IDNumber', 'left');
    $this->db->join('hris_applications_rating r', 'a.appID = r.appID', 'left');
    $this->db->join('hris_jobvacancy jv', 'a.jobID = jv.jobID', 'left');

    $this->db->where('a.dq', 1);
    $this->db->where('jv.jvStatus', 'Open');
    $this->db->where('jv.position', 1);

    $this->db->order_by('app.FirstName', 'DESC');

    $query = $this->db->get();
    return $query->result();
}

    public function rated_applicants_with_missing_scores()
    {
        $ratingSql = "
            SELECT
                appID,
                MAX(CASE WHEN education = 0.00001 THEN NULL ELSE education END) AS education,
                MAX(CASE WHEN training = 0.00001 THEN NULL ELSE training END) AS training,
                MAX(CASE WHEN experience = 0.00001 THEN NULL ELSE experience END) AS experience,
                MAX(CASE WHEN let_rating = 0.00001 THEN NULL ELSE let_rating END) AS let_rating,
                MAX(CASE WHEN tr_rating = 0.00001 THEN NULL ELSE tr_rating END) AS tr_rating,
                MAX(CASE WHEN demo_rating = 0.00001 THEN NULL ELSE demo_rating END) AS demo_rating
            FROM hris_applications_rating
            GROUP BY appID
        ";

        $this->db->select("
            a.*,
            r.education, r.training, r.experience, r.let_rating, r.tr_rating, r.demo_rating,
            jv.*,
            COALESCE(app.record_no, staff.IDNumber) AS code,
            COALESCE(app.id, staff.IDNumber) AS id,
            COALESCE(app.empEmail, staff.IDNumber) AS renren,
            COALESCE(app.contactNo, staff.contactNo) AS contactNo,
            COALESCE(app.FirstName, staff.FirstName) AS FirstName,
            COALESCE(app.MiddleName, staff.MiddleName) AS MiddleName,
            COALESCE(app.NameExtn, staff.NameExtn) AS NameExtn,
            COALESCE(app.LastName, staff.LastName) AS LastName,
            COALESCE(app.perProvince, staff.perProvince) AS province,
            COALESCE(app.jhss, staff.jhss) AS jhss,
            COALESCE(app.shss, staff.shss) AS shss,
            COALESCE(app.resCity, staff.resCity) AS resCity,
            COALESCE(app.resBarangay, staff.resBarangay) AS brgy,
            CASE
                WHEN app.record_no IS NOT NULL THEN 'ma'
                WHEN staff.IDNumber IS NOT NULL THEN 'ma_staff'
                ELSE 'unknown'
            END AS st
        ");

        $this->db->from('hris_applications a');
        $this->db->join('hris_applicant app', 'a.empEmail = app.empEmail', 'left');
        $this->db->join('hris_staff staff', 'app.record_no IS NULL AND a.empEmail = staff.IDNumber', 'left');
        $this->db->join('(' . $ratingSql . ') r', 'a.appID = r.appID', 'left', false);
        $this->db->join('hris_jobvacancy jv', 'a.jobID = jv.jobID', 'left');

        $this->db->where('a.appStatus', 'Rated');
        $this->db->where('a.dq', 1);
        $this->db->where('jv.jvStatus', 'Open');
        $this->db->where('jv.position', 1);
        $this->db->where(
            '(r.education IS NULL OR r.training IS NULL OR r.experience IS NULL OR r.let_rating IS NULL OR r.tr_rating IS NULL OR r.demo_rating IS NULL)',
            null,
            false
        );

        $this->db->order_by('jv.jobTitle', 'ASC');
        $this->db->order_by('app.LastName', 'ASC');
        $this->db->order_by('app.FirstName', 'ASC');

        return $this->db->get()->result();
    }

    public function get_applicantion($applicant_id, $currentJobID)
    {
        $this->db->select('a.*, jv.*');
        $this->db->from('hris_applications a');
        $this->db->join('hris_jobvacancy jv', 'jv.jobID = a.jobID', 'left');
        $this->db->where('a.applicant_id', $applicant_id);
        $this->db->where('a.jobID !=', $currentJobID);
        $this->db->where('jv.position !=', 1);
        
        $query = $this->db->get();

        return $query->result();
    }



  public function copy_rating($record_no, $appID) {
    $result = $this->db->select('educ, trainings, experience, performance, oa, ae, ald, eval_id1, interview, eval_id2, written, eval_id3, record_no, appID, fy, job_type, total_points, skills')
                      ->from('hris_rating_none')
                      ->where('record_no', $record_no)
                      ->where('appID', $appID)
                      ->get()
                      ->row_array();

    if (!$result) {
        return false;
    }

    $targetAppId = $this->input->post('app_id');

    $existing = $this->db->get_where('hris_rating_none', ['appID' => $targetAppId])->row_array();

    $baseData = array(
        'record_no'    => $result['record_no'],
        'appID'        => $targetAppId,
        'educ'         => $result['educ'],
        'trainings'    => $result['trainings'],
        'experience'   => $result['experience'],
        'performance'  => $result['performance'],
        'oa'           => $result['oa'],
        'ae'           => $result['ae'],
        'ald'          => $result['ald'],
        'eval_id1'     => $result['eval_id1'],
        'interview'    => $result['interview'],
        'eval_id2'     => $result['eval_id2'],
        'written'      => $result['written'],
        'eval_id3'     => $result['eval_id3'],
        'fy'           => $result['fy'],
        'job_type'     => $result['job_type'],
        'total_points' => $result['total_points'],
        'skills'       => $result['skills']
    );

    // $targetFy = $existing['fy'] ?? $result['fy'] ?? null;
    // if ($targetFy === null) {
    //     $targetFy = date('Y');
    // }

    if ($existing) {
        return $this->db->where('appID', $targetAppId)->update('hris_rating_none', $baseData);
    }

    // $baseData['fy'] = $targetFy;
    return $this->db->insert('hris_rating_none', $baseData);
  }


  public function jshs_applicant($jt)
    {
    $this->db->select("
        a.*,
        r.education, r.training, r.experience, r.let_rating, r.tr_rating, r.demo_rating,
        jv.*,
        COALESCE(app.record_no, staff.IDNumber) AS code,
        COALESCE(app.id, staff.IDNumber) AS id,
        COALESCE(app.empEmail, staff.IDNumber) AS renren,
        COALESCE(app.contactNo, staff.contactNo) AS contactNo,
        COALESCE(app.FirstName, staff.FirstName) AS FirstName,
        COALESCE(app.MiddleName, staff.MiddleName) AS MiddleName,
        COALESCE(app.NameExtn, staff.NameExtn) AS NameExtn,
        COALESCE(app.LastName, staff.LastName) AS LastName,
        COALESCE(app.perProvince, staff.perProvince) AS province,
        COALESCE(app.jhss, staff.jhss) AS jhss,
        COALESCE(app.shss, staff.shss) AS shss,
        COALESCE(app.resCity, staff.resCity) AS resCity,
        COALESCE(app.resBarangay, staff.resBarangay) AS brgy,
        CASE
            WHEN app.record_no IS NOT NULL THEN 'ma'
            WHEN staff.IDNumber IS NOT NULL THEN 'ma_staff'
            ELSE 'unknown'
        END AS st
        
    ");

    $this->db->from('hris_applications a');
    $this->db->join('hris_applicant app', 'a.empEmail = app.empEmail', 'left');
    $this->db->join('hris_staff staff', 'app.record_no IS NULL AND a.empEmail = staff.IDNumber', 'left');
    $this->db->join('hris_applications_rating r', 'a.appID = r.appID', 'left');
    $this->db->join('hris_jobvacancy jv', 'a.jobID = jv.jobID', 'left');

    $this->db->where('a.dq', 1);
    $this->db->where('jv.jvStatus', 'Open');
    $this->db->where('jv.position', 1);
    $this->db->where_in('jv.job_type',$jt);

    $this->db->order_by('jv.job_type', 'DESC');

    $query = $this->db->get();
    return $query->result();
}


    public function shs_edit($table, $id){
       $i = $this->input->post('group');
       $c = $this->input->post('strand');
       $shss = $i . (!empty($c) ? ' - ' . $c : '');

      $data = array(
        'shss' => $shss,
      );

      $this->db->where($id, $this->input->post('id'));
      return $this->db->update($table, $data);
    }

    public function jhs_edit($table, $id){
       $g = $this->input->post('learn');
       $s = $this->input->post('special');
       $jhss = $g . (!empty($s) ? ' - ' . $s : '');

      $data = array(
        'jhss' => $jhss,
      );

      $this->db->where($id, $this->input->post('id'));
      return $this->db->update($table, $data);
    }

    public function is_old_applicant($email)
    {
        return $this->db
            ->select("
                empEmail,
                COUNT(DISTINCT app_year) AS total_years,
                GROUP_CONCAT(DISTINCT app_year ORDER BY app_year SEPARATOR ', ') AS years_applied
            ", false)
            ->from('hris_applications')
            ->where('empEmail', $email)
            ->where('app_year IS NOT NULL', null, false)
            ->where('app_year !=', '')
            ->group_by('empEmail')
            ->having('COUNT(DISTINCT app_year) > 1', null, false)
            ->get()
            ->row();
    }

    public function hris_hire_insert($IDNumber){

      $data = array(
        'emp_stat' => $this->input->post('emp_stat'), 
        'appointment' => $this->input->post('appointment'), 
        'separation' => $this->input->post('separation'), 
        'vice' => $this->input->post('vice'), 
        'plantilla_id' => $this->input->post('plantilla_id'), 
        'jobID' => $this->input->post('jobID'),
        'salary' => $this->input->post('salary'),
        'sg' => $this->input->post('sg'),
        'step' => $this->input->post('step'),
        'IDNumber' => $IDNumber,
        'appID' => $this->input->post('appID'),
        'pt' => $this->input->post('pt'),
        'pub_at' => $this->input->post('pub_at'),
        'date_pub_from' => $this->input->post('date_pub_from'),
        'date_pub_to' => $this->input->post('date_pub_to'),
        'started_on' => $this->input->post('started_on'),
        'del_held' => $this->input->post('del_held'),
        'sds' => $this->input->post('sds'),
        'asds' => $this->input->post('asds'),
        'aov' => $this->input->post('aov'),
        'd_id' => $this->input->post('d_id'),
        'school_id' => $this->input->post('school_id'),
        'page' => $this->input->post('page')
      );

      return $this->db->insert('hris_hire', $data);
    }
    
    public function get_single_joined_data($id) {
        $this->db->select('h.*,
                        s.FirstName, s.MiddleName, s.LastName, s.NameExtn, s.prefix, 
                        j.jobTitle, j.job_type,
                        hs.stat, hs.description, hs.id,
                        p.id AS plantilla_id, p.itemNo'); // Select from hris_plantilla

        $this->db->from('hris_hire h');
        
        $this->db->join('hris_staff s', 'h.IDNumber = s.IDNumber', 'inner');
        $this->db->join('hris_jobvacancy j', 'h.jobID = j.jobID', 'inner');
        $this->db->join('hris_hire_stat hs', 'h.emp_stat = hs.id', 'inner');
        $this->db->join('hris_plantilla p', 'h.plantilla_id = p.id', 'inner'); // Join with hris_plantilla

        $this->db->where('h.id', $id); 
        
        $query = $this->db->get();
        return $query->row(); 
    }

    public function getStaffByPosition() {
        $positions = ['Assistant Schools Division Superintendent', 'Administrative Officer V', 'Schools Division Superintendent'];

        $this->db->select('*');
        $this->db->from('hris_staff');
        $this->db->where_in('empPosition', $positions);
        $this->db->where('currentStatus', 'Active'); 
        
        $query = $this->db->get();

        return $query->result();
    }

    public function getHireWithJob($idNumber)
    {
        $this->db->select('hris_hire.*, hris_jobvacancy.jobID, hris_jobvacancy.jobTitle, hris_jobvacancy.job_type');
        $this->db->from('hris_hire');
        $this->db->join('hris_jobvacancy', 'hris_jobvacancy.jobID = hris_hire.jobID');
        $this->db->where('hris_hire.IDNumber', $idNumber);

        $query = $this->db->get();
        return $query->result();
    }














}
