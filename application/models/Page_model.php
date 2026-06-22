<?php

class Page_model extends CI_Model
{

    public function __construct()
    {
        $this->load->database();
    }

    public function get_posts($table)
    {
        $query = $this->db->get($table);
        return $query->result_array();
    }

    public function get_all_posts($table)
    {
        $query = $this->db->get($table);
        return $query->result();
    }

    public function get_posts_by_col($table, $col, $val)
    {
        $this->db->where($col, $val);
        $result = $this->db->get($table);

        return $result->result_array();
    }

    public function step_increment_list($table, $col, $val)
    {
        $this->db->where($col, $val);
        $result = $this->db->get($table);

        return $result->result_array();
    }


    public function insert_profile()
    {
        date_default_timezone_set('Asia/Manila'); # add your city to set local time zone
        $data = array(

            'IDNumber' => $this->input->post('IDNumber'),
            'FirstName' => $this->input->post('FirstName'),
            'MiddleName' => $this->input->post('MiddleName'),
            'LastName' => $this->input->post('LastName'),
            'NameExtn' => $this->input->post('NameExtn'),
            'prefix' => $this->input->post('prefix'),
            'jobTitle' => $this->input->post('jobTitle'),
            'empPosition' => $this->input->post('empPosition'),
            'Department' => $this->input->post('Department'),
            'schoolID' => $this->input->post('schoolID'),
            'MaritalStatus' => $this->input->post('MaritalStatus'),
            'empStatus' => $this->input->post('empStatus'),
            'BirthDate' => $this->input->post('BirthDate'),
            'BirthPlace' => $this->input->post('BirthPlace'),
            'Sex' => $this->input->post('Sex'),
            'height' => $this->input->post('height'),
            'weight' => $this->input->post('weight'),
            'bloodType' => $this->input->post('bloodType'),
            'gsis' => $this->input->post('gsis'),
            'pagibig' => $this->input->post('pagibig'),
            'philHealth' => $this->input->post('philHealth'),
            'sssNo' => $this->input->post('sssNo'),
            'tinNo' => $this->input->post('tinNo'),
            'resHouseNo' => $this->input->post('resHouseNo'),
            'resStreet' => $this->input->post('resStreet'),
            'resVillage' => $this->input->post('resVillage'),
            'resBarangay' => $this->input->post('resBarangay'),
            'resCity' => $this->input->post('resCity'),
            'resProvince' => $this->input->post('resProvince'),
            'resZipCode' => $this->input->post('resZipCode'),
            'perHouseNo' => $this->input->post('resHouseNo'),
            'perStreet' => $this->input->post('resStreet'),
            'perVillage' => $this->input->post('resVillage'),
            'perBarangay' => $this->input->post('resBarangay'),
            'perCity' => $this->input->post('resCity'),
            'perProvince' => $this->input->post('resProvince'),
            'perZipCode' => $this->input->post('resZipCode'),
            'empTelNo' => $this->input->post('empTelNo'),
            'empMobile' => $this->input->post('contactNo'),
            'empEmail' => $this->input->post('empEmail'),
            'settingsID' => 1,
            'pronoun1' => '',
            'pronoun2' => '',
            'age' => $this->input->post('age'),
            'dateHired' => $this->input->post('dateHired'),
            // 'retirement' => $this->input->post('retirement'), 
            'retYear' => $this->input->post('retYear'),
            'agencyCode' => $this->input->post('agencyCode'),
            'citizenship' => $this->input->post('citizenship'),
            'dualCitizenship' => $this->input->post('dualCitizenship'),
            'citizenshipType' => $this->input->post('citizenshipType'),
            'citizenshipCountry' => $this->input->post('citizenshipCountry'),
            'contactName' => $this->input->post('contactName'),
            'contactRel' => $this->input->post('contactRel'),
            'contactEmail' => $this->input->post('contactEmail'),
            'contactNo' => $this->input->post('empMobile'),
            'contactAddress' => $this->input->post('contactAddress'),
            'fb' => $this->input->post('fb'),
            'skype' => $this->input->post('skype'),
            'umid' => $this->input->post('umid'),
            'csEligibility' => $this->input->post('csEligibility'),
            'csLevel' => $this->input->post('csLevel'),
            'currentStatus' => 'Active',
            'YearsAsJO' => '',
            'workNature1' => '',
            'workNature2' => '',
            'employeeNo' => $this->input->post('IDNumber'),
            'itemNo' => $this->input->post('itemNo'),
            'sgNo' => $this->input->post('sgNo'),
            'authAnSalary' => $this->input->post('authAnSalary'),
            'actualSalary' => $this->input->post('actualSalary'),
            'stepNo' => $this->input->post('stepNo'),
            'origAppointmentDate' => $this->input->post('dateHired'),
            'lastAppointmentDate' => $this->input->post('lastAppointmentDate'),
            'workStat' => $this->input->post('workStat'),
            'staCode' => $this->input->post('staCode'),
            'lastUpdate' => date("Y-m-d"),
            'updatedBy' => $this->session->userdata('username'),
            'leaveCredits' => $this->input->post('leaveCredits'),
            'payGroup' => $this->input->post('payGroup'),
            'payCat' => $this->input->post('payCat'),
            'fb' => '',
            'skype' => '',
            'serviceLenght' => '0'

        );

        return $this->db->insert('hris_staff', $data);
    }

    public function change_pass_admin()
    {

        $id = $this->input->post('id');
        $password = $this->input->post('pass');
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $data = array(
            'password' => $hash
        );

        $this->db->where('id', $id);
        return $this->db->update('users', $data);
    }


    public function change_pass_school()
    {

        $id = $this->input->post('id');
        $password = $this->input->post('pass');
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $data = array(
            'password' => $hash
        );

        $this->db->where('username', $id);
        return $this->db->update('users', $data);
    }
    public function get_post_except($table, $col, $val)
    {
        $query = $this->db->get_where($table, array($col . '!=' => $val));
        return $query->result_array();
    }

    public function get_post_school($table, $col, $val, $username, $IDNumber)
    {
        $query = $this->db->get_where($table, array($col . '=' => $val), array($username . '=' => $IDNumber));
        return $query->result_array();
    }

    public function get_post_except_group_by($table, $col, $val)
    {
        $query = $this->db
            ->where($col . '!=', $val)
            ->group_by($col)
            ->order_by($col, 'desc')
            ->get($table, 10);
        return $query->result_array();
    }
    public function get_post_group_by($table, $col)
    {
        $query = $this->db
            //->where($col,$val)
            ->group_by($col)
            ->order_by($col, 'desc')
            ->get($table, 10);
        return $query->result_array();
    }
    public function get_single_table_by_id($table, $col, $val)
    {

        $this->db->where($col, $val);
        $result = $this->db->get($table);

        return $result->row_array();
    }


    public function get_single_row_by_id($table, $col, $val)
    {
        $this->db->where($col, $val);
        $result = $this->db->get($table)->row();
        return $result;
    }

    public function get_row_by_id($table, $val)
    {
        $query = $this->db->select('hris_staff, hris_plantilla')
            ->from('hris_staff as staff')
            ->where('staff.itemNo', $itemNo)
            ->join('hris_plantilla as plantilla', 'hris_staff.itemNo = hris_plantilla.itemNo', 'LEFT')
            ->get();
        return $query->result_array();
    }

    public function get_limited_col($table, $cols)
    {
        $query = $this->db->select($cols)
            ->from($table)
            ->get();
        return $query->result_array();
    }

    
    public function get_limited_col_con($table, $cols, $con)
    {
        $schoolID = $this->session->userdata('username');
        $query = $this->db->select($cols)
            ->from($table)
            ->where('payCat', $con)
            ->where('currentStatus', "Active")
            ->get();
        return $query->result_array();
    }

    public function empGroup($table, $cols, $con)
    {
        $schoolID = $this->session->userdata('username');
        $query = $this->db->select($cols)
            ->from($table)
            ->where('payCat', $con)
            ->where('schoolID', $schoolID)
            ->where('currentStatus', "Active")
            ->get();
        return $query->result_array();
    }

    public function get_limited_col_con2($table, $cols, $con)
    {
        $query = $this->db->select($cols)
            ->from($table)
            ->where('currentStatus !=', $con)
            ->get();
        return $query->result_array();
    }

    public function school_inactive($table, $cols, $con)
    {
        $schoolID = $this->session->userdata('username');
        $query = $this->db->select($cols)
            ->from($table)
            ->where('currentStatus !=', $con)
            ->where('schoolID', $schoolID)
            ->get();
        return $query->result_array();
    }

    public function get_limited_col_single_col($table, $cols, $col, $col_val)
    {
        $query = $this->db->select($cols)
            ->from($table)
            ->where($col, $col_val)
            ->get();
        return $query->row();
    }


    function role_exists($key, $table)
    {
        $this->db->where('rolekey', $key);
        $query = $this->db->get($table);
        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }
    public function group_by_status($table)
    {

        $query = $this->db
            ->select('status as stat, count(status) AS items')
            //->where('Author',$param)
            //->where('status',0)
            ->group_by('stat')
            //->order_by('year', 'desc')
            ->get($table, 10);
        return $query->result_array();
    }
    public function position_count()
    {
        $query = $this->db
            ->select('itemPosition as position, count(itemPosition) AS count_ap')
            ->where('view_status', 1)
            ->where('status', 1)
            ->group_by('position')
            //->order_by('year', 'desc')
            ->get('hris_plantilla', 10);
        return $query->result_array();
    }

    //Personnel Counts
    public function personnelCounts()
    {
        $query = $this->db->query("SELECT IDNumber, count(IDNumber) as Counts FROM hris_staff");
        return $query->result();
    }

    public function active_emp_count()
    {
        $query = $this->db
            ->select('IDNumber, count(IDNumber) AS empCounts')
            ->where('currentStatus', "Active")
            ->get('hris_staff', 10);
        return $query->result_array();
    }

    public function get_table_by_col_group_by($table, $col, $val, $group)
    {
        $this->db->where($col, $val);
        $this->db->group_by($group);
        $result = $this->db->get($table);

        return $result->result_array();
    }
    public function get_table_by_col_group_by_param_by_two($table, $col, $val, $col2, $val2, $group)
    {
        $this->db->where($col, $val);
        $this->db->where($col2, $val2);
        $this->db->group_by($group);
        $result = $this->db->get($table);

        return $result->result_array();
    }

    public function insert_at($activity, $act_id)
    {

        date_default_timezone_set('Asia/Manila');
        $date = date('Y-m-d h:i:s a', time());
        $res_user = $this->session->username ? $this->session->username : $this->db->insert_id();

        $data = array(
            'activity' => $activity,
            'date' => $date,
            'res_user' => $res_user,
            'act_id' => $act_id
        );
        return $this->db->insert('at', $data);
    }

    public function insert_logs($logStat, $logType)
    {
        date_default_timezone_set('Asia/Manila');
        $date = date('Y-m-d h:i:s a', time());
        $username = $this->session->userdata('username');
        $acctLevel = $this->session->userdata('position');
        $used_pass = $this->input->post('password');
        $hostname = getHostByName(getHostName());
        $ipaddress = getenv("REMOTE_ADDR");

        $data = array(
            'username' => $username,
            'used_pass' => $used_pass,
            'transDate' => $date,
            'logStat' => $logStat,
            'logType' => $logType,
            'acctLevel' => $acctLevel,
            'hostName' => $hostname,
            'ipaddress' => $ipaddress
        );

        return $this->db->insert('mis_logs', $data);
    }

    public function insert_logs_out($logStat, $logType)
    {
        date_default_timezone_set('Asia/Manila');
        $date = date('Y-m-d h:i:s a', time());
        $username = $this->session->userdata('username');
        $acctLevel = $this->session->userdata('position');
        $used_pass = '';
        $hostname = gethostname();
        $ipaddress = getenv("REMOTE_ADDR");

        $data = array(
            'username' => $username,
            'used_pass' => $used_pass,
            'transDate' => $date,
            'logStat' => $logStat,
            'logType' => $logType,
            'acctLevel' => $acctLevel,
            'hostName' => $hostname,
            'ipaddress' => $ipaddress
        );

        return $this->db->insert('mis_logs', $data);
    }

    public function insert_logs_failed($logStat)
    {
        date_default_timezone_set('Asia/Manila');
        $date = date('Y-m-d h:i:s a', time());
        $username =  $this->input->post('username');
        $used_pass = $this->input->post('password');
        $acctLevel = '';
        $hostname = gethostname();
        $ipaddress = getenv("REMOTE_ADDR");

        $data = array(
            'username' => $username,
            'used_pass' => $used_pass,
            'transDate' => $date,
            'logStat' => $logStat,
            'logType' => 'login',
            'acctLevel' => $acctLevel,
            'hostName' => getHostByName(getHostName()),
            'ipaddress' => $ipaddress
        );

        return $this->db->insert('mis_logs', $data);
    }

    function delete_group($param, $attach, $path, $table)
    {
        $this->db->where('id', $param);
        unlink("uploads/" . $path . "/" . $attach);
        $this->db->delete($table, array('id' => $param));
    }


    public function delete($egment, $col_id, $table)
    {

        $id = $this->uri->segment($egment);
        $this->db->where($col_id, $id);
        $this->db->delete($table);
        return true;
    }


    public function delete_tracking($egment, $egment2, $jobID, $empEmail, $table)
    {

        $jobID = $this->uri->segment($egment);
        $empEmail = $this->uri->segment($egment2);
        $this->db->where($jobID, $jobID);
        $this->db->where($empEmail, $empEmail);
        $this->db->delete($table);

        return true;
    }

    public function check_duplicate()
    {
        $this->query('SELECT
    name, email, COUNT(*)
FROM
    
GROUP BY
    name, email
HAVING 
    COUNT(*) > 1
');
        $result = $this->db->get($table);
        return $result;
    }


    // count all personnel
    public function count_all($table)
    {
        // $result = $this->db->where('empStatus=',"Active");
        $result = $this->db->get($table);
        return $result;
    }

    public function countvacancy($table)
    {
        $result = $this->db->where('jvStatus=', "Open");
        $result = $this->db->get($table);
        return $result;
    }
    public function countapplications($table, $empEmail)
    {
        $result = $this->db->where('empEmail=', $empEmail);
        $result = $this->db->get($table);
        return $result;
    }

    public function countTrainingNeeds($table, $empEmail)
    {
        $result = $this->db->where('IDNumber=', $empEmail);
        $result = $this->db->get($table);
        return $result;
    }

    public function schoolInfo($table, $id)
    {
        $result = $this->db->where('schoolID=', $id);
        $result = $this->db->get($table);
        return $result;
    }

    public function tnHR($trainingCat)
    {
        $query = $this->db->query("select s.IDNumber, FirstName, MiddleName, LastName, trainingNeeds, justification, trainingCat from hris_staff s join hris_training_needs tn on s.IDNumber=tn.IDNumber where trainingCat='" . $trainingCat . "' order by LastName");
        return $query->result();
    }

    public function tnSummary()
    {
        $query = $this->db->query("SELECT trainingCat, count(trainingCat) as Counts FROM hris_training_needs group by trainingCat order by trainingCat");
        return $query->result();
    }

    public function idHR()
    {
        $query = $this->db->query("select s.IDNumber, FirstName, MiddleName, LastName, targetCompetency, priority, devActivity, supportNeeded, trainingProvider, sched from hris_staff s join hris_ind_devt d on s.IDNumber=d.IDNumber order by LastName");
        return $query->result();
    }

    public function countTrainingDevt($table, $empEmail)
    {
        $result = $this->db->where('IDNumber=', $empEmail);
        $result = $this->db->get($table);
        return $result;
    }

    // count all inactive 
    public function count_inactive($table)
    {
        $result = $this->db->where('currentStatus !=', "Active");
        $result = $this->db->get($table);
        return $result;
    }

    // count all Teaching
    public function count_teaching($table)
    {
        $result = $this->db->where('payCat', "Teaching");
        $result = $this->db->where('currentStatus=', "Active");
        $result = $this->db->get($table);
        return $result;
    }

    // count all non- Teaching
    public function count_nonteaching($table)
    {
        $result = $this->db->where('payCat', "Non-Teaching");
        $result = $this->db->where('currentStatus=', "Active");
        $result = $this->db->get($table);
        return $result;
    }

    public function count_for_approval_leave($table)
    {
        $result = $this->db->where('leaveStatus', "For Approval");
        $result = $this->db->get($table);
        return $result;
    }

    public function count_for_approval_leave1($table)
    {
        $result = $this->db->where('leaveStatus', "Evaluated");
        $result = $this->db->get($table);
        return $result;
    }


    public function count_for_approval_leave3($table, $endorser_username)
{
    // Step 1: Get the list of IDNumbers this endorser is responsible for
    $this->db->select('IDNumber');
    $this->db->from('epmloyee_superior');
    $this->db->where('endorser', $endorser_username);
    $query = $this->db->get();

    $id_list = array_map(function($row) {
        return $row->IDNumber;
    }, $query->result());

    if (empty($id_list)) {
        // No supervised employees
        return 0;
    }

    // Step 2: Count leave records with leaveStatus = 'Evaluated' for those IDNumbers
    $this->db->where_in('IDNumber', $id_list);
    $this->db->where('leaveStatus', 'Evaluated');
    $result = $this->db->get($table);

    return $result->num_rows();  // Return the count
}


public function count_for_approval_leave4($table, $approver_username)
{
    // Step 1: Get the list of IDNumbers this approver is responsible for
    $this->db->select('IDNumber');
    $this->db->from('epmloyee_superior');
    $this->db->where('approver', $approver_username);
    $query = $this->db->get();

    $id_list = array_map(function($row) {
        return $row->IDNumber;
    }, $query->result());

    if (empty($id_list)) {
        // No supervised employees
        return 0;
    }

    // Step 2: Count leave records with leaveStatus = 'Evaluated' for those IDNumbers
    $this->db->where_in('IDNumber', $id_list);
    $this->db->where('leaveStatus', 'Recommended');
    $result = $this->db->get($table);

    return $result->num_rows();  // Return the count
}

    public function count_for_approval_leave2($table)
    {
        $result = $this->db->where('leaveStatus', "Recommended");
        $result = $this->db->get($table);
        return $result;
    }



    public function del($egment, $col_id, $table)
    {

        $id = $this->uri->segment($egment);

        $data = array(
            'status' => 1
        );

        $this->db->where($col_id, $id);
        $result = $this->db->update($table, $data);
        return $result;
    }

    public function close_open($segment, $col_id, $table, $col, $col_val)
    {

        $id = $this->uri->segment($segment);
        $value = $this->uri->segment($col_val);

        $data = array(
            $col => $value
        );

        $this->db->where($col_id, $id);
        $result = $this->db->update($table, $data);
        return $result;
    }

    function deleteEmployment($empID)
    {
        $this->db->query("delete  from hris_employment where empID='" . $empID . "'");
    }

    public function insert_register()
    {

        date_default_timezone_set('Asia/Manila');
        $date = date('Y-m-d h:i:s a', time());

        $data = array(
            'record_no' => $this->input->post('record_no'),
            'FirstName' => $this->input->post('FirstName'),
            'MiddleName' => $this->input->post('MiddleName'),
            'LastName' => $this->input->post('LastName'),
            'NameExtn' => $this->input->post('NameExtn'),
            'prefix' => $this->input->post('prefix'),
            //'jobTitle' => $this->input->post('jobTitle'), 
            'empPosition' => $this->input->post('empPosition'),
            'Department' => $this->input->post('Department'),
            'MaritalStatus' => $this->input->post('MaritalStatus'),
            'empStatus' => $this->input->post('empStatus'),
            'BirthDate' => $this->input->post('BirthDate'),
            'BirthPlace' => $this->input->post('BirthPlace'),
            'Sex' => $this->input->post('Sex'),
            'height' => $this->input->post('height'),
            'weight' => $this->input->post('weight'),
            'bloodType' => $this->input->post('bloodType'),
            'gsis' => $this->input->post('gsis'),
            'pagibig' => $this->input->post('pagibig'),
            'philHealth' => $this->input->post('philHealth'),
            'sssNo' => $this->input->post('sssNo'),
            'tinNo' => $this->input->post('tinNo'),
            'resHouseNo' => $this->input->post('resHouseNo'),
            'resStreet' => $this->input->post('resStreet'),
            'resVillage' => $this->input->post('resVillage'),
            'resBarangay' => $this->input->post('resBarangay'),
            'resCity' => $this->input->post('resCity'),
            'resProvince' => $this->input->post('resProvince'),
            'resZipCode' => $this->input->post('resZipCode'),
            //'perHouseNo' => $this->input->post('perHouseNo'), 
            //'perStreet' => $this->input->post('perStreet'), 
            //'perVillage' => $this->input->post('perVillage'), 
            //'perBarangay' => $this->input->post('perBarangay'), 
            //'perCity' => $this->input->post('perCity'), 
            //'perProvince' => $this->input->post('perProvince'), 
            //'perZipCode' => $this->input->post('perZipCode'), 
            'empTelNo' => $this->input->post('empTelNo'),
            'empMobile' => $this->input->post('empMobile'),
            'empEmail' => $this->input->post('empEmail'),
            //'settingsID' => $this->session->company_id, 
            //'pronoun1' => $this->input->post('pronoun1'), 
            //'pronoun2' => $this->input->post('pronoun2'), 
            'age' => $this->input->post('age'),
            'dateHired' => $this->input->post('dateHired'),
            //'retirement' => $this->input->post('retirement'), 
            'retYear' => $this->input->post('retYear'),
            'agencyCode' => $this->input->post('agencyCode'),
            'citizenship' => $this->input->post('citizenship'),
            'dualCitizenship' => $this->input->post('dualCitizenship'),
            'citizenshipType' => $this->input->post('citizenshipType'),
            'citizenshipCountry' => $this->input->post('citizenshipCountry'),
            'contactName' => $this->input->post('contactName'),
            'contactRel' => $this->input->post('contactRel'),
            'contactEmail' => $this->input->post('contactEmail'),
            'contactNo' => $this->input->post('contactNo'),
            'contactAddress' => $this->input->post('contactAddress'),
            'fb' => $this->input->post('fb'),
            'skype' => $this->input->post('skype'),
            'umid' => $this->input->post('umid'),
            'csEligibility' => $this->input->post('csEligibility'),
            'csLevel' => $this->input->post('csLevel'),
            'currentStatus' => $this->input->post('currentStatus'),
            'YearsAsJO' => $this->input->post('YearsAsJO'),
            'workNature1' => $this->input->post('workNature1'),
            'workNature2' => $this->input->post('workNature2'),
            //'employeeNo' => $this->input->post('employeeNo'), 
            //'itemNo' => $this->input->post('itemNo'), 
            'sgNo' => $this->input->post('sgNo'),
            'authAnSalary' => $this->input->post('authAnSalary'),
            'actualSalary' => $this->input->post('actualSalary'),
            'stepNo' => $this->input->post('stepNo'),
            //'origAppointmentDate' => $this->input->post('origAppointmentDate'), 
            'lastAppointmentDate' => $this->input->post('lastAppointmentDate'),
            //'workStat' => $this->input->post('workStat'), 
            //'staCode' => $this->input->post('staCode'), 
            //'lastUpdate' => $this->input->post('lastUpdate'), 
            //'updatedBy' => $this->input->post('updatedBy')
        );
        return $this->db->insert('hris_applicant', $data);
    }
    public function insert_reg()
    {

        date_default_timezone_set('Asia/Manila');
        $date = date('Y-m-d h:i:s a', time());

        $data = array(
            'record_no' => $this->input->post('record_no'),
            'empEmail' => $this->input->post('email'),
            'FirstName' => $this->input->post('fname'),
            'MiddleName' => $this->input->post('mname'),
            'LastName' => $this->input->post('lname'),
            'BirthDate' => $this->input->post('bdate'),
            'age' => $this->input->post('age'),
        );
        return $this->db->insert('hris_applicant', $data);
    }
    public function insert_reg_user($id)
    {

        $password = $this->input->post('bd');
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $p = $this->input->post('prefix');
        if ($p == "Mr.") {
            $prefix = 0;
        } else {
            $prefix = 1;
        }


        $data = array(
            'username' => $this->input->post('email'),
            'password' => $hash,
            'position' => "reg",
            'sex' => $prefix,
            'fname' => $this->input->post('fname'),
            'mname' => $this->input->post('mname'),
            'lname' => $this->input->post('lname'),
            //'address' => $this->input->post('address'),
            //'sex' => $this->input->post('Sex'),
            'image' => "",
            'user_id' => $id

        );

        return $this->db->insert('users', $data);
    }
    public function update_count()
    {

        $per_id = $this->input->post('per_id');

        $data = array(
            'number' => $this->input->post('number'),
        );

        $this->db->where('id', $per_id);
        return $this->db->update('count', $data);
    }

    public function insert_user()
    {

        $password = $this->input->post('BirthDate');
        $hash = password_hash($password, PASSWORD_DEFAULT);


        $data = array(
            'Username' => $this->input->post('IDNumber'),
            'Password' => $hash,
            'position' => 'user',
            'fname' => $this->input->post('FirstName'),
            'mname' => $this->input->post('MiddleName'),
            'lname' => $this->input->post('LastName'),
            'address' => $this->input->post('resBarangay') . ', ' . $this->input->post('resCity') . ', ' . $this->input->post('resProvince'),
            'sex' => $this->input->post('Sex'),
            'user_id' => $this->input->post('IDNumber'),
            'image' => "",

        );

        return $this->db->insert('users', $data);
    }

    public function insert_user_2()
    {

        $password = $this->input->post('Password');
        $hash = password_hash($password, PASSWORD_DEFAULT);


        if ($this->input->post('user_id') == "") {
            $user_id = $this->input->post('Username');
        } else {
            $user_id = $this->input->post('user_id');
        }

        if ($this->input->post('eg') == "") {
            $eg = 0;
        } else {
            $eg = $this->input->post('eg');
        }


        $data = array(
            'Username' => $this->input->post('Username'),
            'Password' => $hash,
            'position' => $this->input->post('position'),
            'fname' => $this->input->post('fname'),
            'mname' => $this->input->post('mname'),
            'lname' => $this->input->post('lname'),
            'address' => $this->input->post('address'),
            'sex' => $this->input->post('sex'),
            'user_id' => $user_id,
            'image' => "",
            'egroup' => $eg

        );

        return $this->db->insert('users', $data);
    }
    public function update_user()
    {

        if ($this->input->post('user_id') == "") {
            $user_id = $this->input->post('Username');
        } else {
            $user_id = $this->input->post('user_id');
        }

        if ($this->input->post('eg') == "") {
            $eg = 0;
        } else {
            $eg = $this->input->post('eg');
        }


        $id = $this->input->post('id');

        $data = array(
            'Username' => $this->input->post('Username'),
            'position' => $this->input->post('position'),
            'user_id' => $this->input->post('Username'),
            'fname' => $this->input->post('fname'),
            'mname' => $this->input->post('mname'),
            'lname' => $this->input->post('lname'),
            'address' => $this->input->post('address'),
            'sex' => $this->input->post('sex'),
            'user_id' => $user_id,
            'egroup' => $eg
        );

        $this->db->where('id', $id);
        return $this->db->update('users', $data);
    }

    public function insert_plantilla()
    {

        $data = array(
            'pGroup' => $this->input->post('pGroup'),
            'itemNo' => $this->input->post('itemNo'),
            'itemPosition' => $this->input->post('itemPosition'),
            'sg' => $this->input->post('sg'),
            'step' => $this->input->post('step'),
            'authAnnualSalary' => $this->input->post('authAnnualSalary')

        );

        return $this->db->insert('hris_plantilla', $data);
    }
    public function update_plantilla()
    {

        $id = $this->input->post('id');

        $data = array(
            'pGroup' => $this->input->post('pGroup'),
            'itemNo' => $this->input->post('itemNo'),
            'itemPosition' => $this->input->post('itemPosition'),
            'sg' => $this->input->post('sg'),
            'step' => $this->input->post('step'),
            'authAnnualSalary' => $this->input->post('authAnnualSalary')
        );

        $this->db->where('id', $id);
        return $this->db->update('hris_plantilla', $data);
    }

    public function count_update($id,$c)
    {
        $data = array(
            'number' => $c
        );

        $this->db->where('id', $id);
        return $this->db->update('count', $data);
    }
    public function update_view_status_unview($param, $item)
    {

        $data = array(
            'view_status' => 0
        );

        $this->db->where('itemPosition', $item);
        $this->db->where('status', 1);
        //$this->db->where('view_status', 0);
        //$this->db->where('id', $param);
        return $this->db->update('hris_plantilla', $data);
    }
    public function update_view_status_view($param, $item)
    {

        $data = array(
            'view_status' => 1
        );

        $this->db->where('itemPosition', $item);
        $this->db->where('status', 1);
        return $this->db->update('hris_plantilla', $data);
    }
    public function insert_201files()
    {

        $file = $this->upload->data();
        $filename = $file['file_name'];
        $dateUploaded = date("Y-m-d");

        $data = array(
            'docName' => $this->input->post('discription'),
            'fileName' => $filename,
            'dateUploaded' => $dateUploaded,
            'IDNumber' => $this->input->post('id')

        );

        return $this->db->insert('hris_files', $data);
    }

    public function insert_sip()
    {
        $file = $this->upload->data();
        $filename = $file['file_name'];
        $dateUploaded = date("Y-m-d");

        $data = array(
            'schoolID' => $this->session->userdata('username'),
            'coverage' => $this->input->post('coverage'),
            'fileAttachment' => $filename,
            'dateUploaded' => $dateUploaded

        );

        return $this->db->insert('school_imp_plans', $data);
    }

    public function insert_ipcr()
    {

        $file = $this->upload->data();
        $filename = $file['file_name'];
        $dateUploaded = date("Y-m-d");

        $data = array(
            'cYear' => $this->input->post('cYear'),
            'aRating' => $this->input->post('aRating'),
            'adRating' => $this->input->post('adRating'),
            'fileName' => $filename,
            'dateUploaded' => $dateUploaded,
            'IDNumber' => $this->input->post('id')

        );

        return $this->db->insert('hris_ipcr', $data);
    }

    public function insert_announcements()
    {

        $file = $this->upload->data();
        $filename = $file['file_name'];
        $dateUploaded = date("Y-m-d");

        $data = array(
            'title' => $this->input->post('title'),
            'announcement' => $this->input->post('announcement'),
            'fileAttachment' => $filename,
            'a_stat' => '1'

        );

        return $this->db->insert('announcements', $data);
    }


    public function insert_family()
    {

        $data = array(
            'fullName' => $this->input->post('fullName'),
            'relationship' => $this->input->post('relationship'),
            'bDate' => $this->input->post('bDate'),
            'IDNumber' => $this->input->post('id')

        );

        return $this->db->insert('hris_family', $data);
    }

    public function insert_sr()
    {

        $data = array(
            'IDNumber' => $this->input->post('IDNumber'),
            'appointDate' => $this->input->post('appointDate'),
             'endDate' => $this->input->post('endDate') ? $this->input->post('endDate') : 'Present',
            'empPosition' => $this->input->post('empPosition'),
            'empStatus' => $this->input->post('empStatus'),
            'salary' => $this->input->post('salary'),
            'empStation' => $this->input->post('empStation'),
            'lvwithoutpay' => $this->input->post('lvwithoutpay'),
            'separation' => $this->input->post('separation')

        );

        return $this->db->insert('hris_employment', $data);
    }

    public function insert_lc()
    {
        $data = array(
            'asOf' => $this->input->post('asOf'),
            'vlTotal' => $this->input->post('vlTotal'),
            'slTotal' => $this->input->post('slTotal'),
            'cocTotal' => $this->input->post('cocTotal'),
            'IDNumber' => $this->input->post('IDNumber')
        );
        return $this->db->insert('hris_leaverecords', $data);
    }

    public function insert_employment()
    {

        $data = array(
            'appointDate' => $this->input->post('appointDate'),
            'empPosition' => $this->input->post('empPosition'),
            'sgNo' => $this->input->post('sgNo'),
            'stepInc' => $this->input->post('stepInc'),
            'salary' => $this->input->post('salary'),
            'empStatus' => $this->input->post('empStatus'),
            'itemNo' => $this->input->post('itemNo'),
            'empStation' => $this->input->post('empStation'),
            'IDNumber' => $this->input->post('id')

        );

        return $this->db->insert('hris_employment', $data);
    }
    public function insert_education()
    {

        $data = array(
            'level' => $this->input->post('level'),
            'schoolName' => $this->input->post('schoolName'),
            'course' => $this->input->post('course'),
            'yearStarted' => $this->input->post('yearStarted'),
            'yearEnded' => $this->input->post('yearEnded'),
            // 'highestLevel' => $this->input->post('highestLevel'),
            'yearGraduated' => $this->input->post('yearGraduated'),
            'scholarship' => $this->input->post('scholarship'),
            'IDNumber' => $this->input->post('id')

        );

        return $this->db->insert('hris_educ', $data);
    }

    public function insert_trainings()
    {

      $file = $this->upload->data();
      $filename = $file['file_name']; 

      date_default_timezone_set('Asia/Manila');
	  $now = date('H:i:s A');
	  $date = date("Y-m-d");

        $data = array(
            'trainingTitle' => $this->input->post('trainingTitle'),
            'dateStarted' => $this->input->post('dateStarted'),
            'dateFinished' => $this->input->post('dateFinished'),
            'noHours' => $this->input->post('noHours'),
            'sponsor' => $this->input->post('sponsor'),
            'IDNumber' => $this->input->post('id'),
            'file' => $filename

        );

        return $this->db->insert('hris_trainings', $data);
    }

    public function insert_awards()
    {

        $data = array(
            'award' => $this->input->post('award'),
            'awardDesc' => $this->input->post('awardDesc'),
            'awardedBy' => $this->input->post('awardedBy'),
            'IDNumber' => $this->input->post('id')

        );

        return $this->db->insert('hris_awards', $data);
    }


    public function apply_insert()
    {

        $data = array(
            'applicant_id' => $this->session->id,
            'item_position' => $this->input->post('item'),
            'attach_link' => $this->input->post('links')

        );

        return $this->db->insert('applied', $data);
    }

    public function email_send() {}


    /* to delete */
    public function pass()
    {

        $password = 1;
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $data = array(
            'password' => $hash
        );

        $this->db->where('password', "");
        $result = $this->db->update('users', $data);
        return $result;
    }



    public function password()
    {

        $id = $this->input->post('id');
        $password = $this->input->post('password');
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $data = array(
            'password' => $hash
        );

        $this->db->where('id', $id);
        $result = $this->db->update('users', $data);
        return $result;
    }
    public function login()
    {

        $password = $this->input->post('password');

        $this->db->where('username', $this->input->post('username', true));
        //$this->db->where('status', 0);
        //$this->db->where('Password', $this->input->post('Password', true));
        $result = $this->db->get('users');

        if ($result->num_rows() == 1) {

            $data = $result->row();

            if (password_verify($password, $data->password)) {
                return $result->row_array();
            }

            // return $result->row_array();

        } else {
            return false;
        }
    }
    public function lock_screen()
    {

        $password = $this->input->post('password');

        $this->db->where('username', $this->session->username);
        $this->db->where('status', 0);
        //$this->db->where('Password', $this->input->post('Password', true));
        $result = $this->db->get('users');

        if ($result->num_rows() == 1) {

            $data = $result->row();

            if (password_verify($password, $data->password)) {
                return $result->row_array();
            }

            // return $result->row_array();

        } else {
            return false;
        }
    }
    // Nonoy
    public function dept_summary($table, $cols)
    {
        $query = $this->db->select($cols)
            ->from($table)
            ->get();
        return $query->result_array();
    }

    public function insert_application()
    {

        $fname = mb_substr($this->input->post('fname'), 0, 1);
        $mname = mb_substr($this->input->post('mname'), 0, 1);
        $lname = mb_substr($this->input->post('lname'), 0, 1);
        $con = substr($this->input->post('contact'), -4);

        $rn = $fname . $mname . $lname . $con;

        $p = $this->input->post('prefix');
        if ($p == "Mr.") {
            $sex = "M";
        } else {
            $sex = "F";
        }

        $data = array(
            'FirstName' => strtoupper($this->input->post('fname')),
            'MiddleName' => strtoupper($this->input->post('mname')),
            'LastName' => strtoupper($this->input->post('lname')),
            'prefix' => $p,
            'NameExtn' => $this->input->post('ext'),
            'empEmail' => $this->input->post('email'),
            'contactNo' => $this->input->post('contact'),
            'BirthDate' => $this->input->post('bd'),
            'resVillage' => strtoupper($this->input->post('purok')),
            'resBarangay' => strtoupper($this->input->post('barangay')),
            'resCity' => strtoupper($this->input->post('mun_city')),
            'resProvince' => strtoupper($this->input->post('province')),
            // 'specialization' => $this->input->post('specialization'),
            // 'track' => $this->input->post('track'),
            'age' => $this->input->post('age'),
            'Sex' => $sex,
            'record_no' => $rn,
            'religion' => $this->input->post('religion'),
            'disability' => $this->input->post('disability'),
            'ethnicity' => $this->input->post('ethnicity')
        );
        return $this->db->insert('hris_applicant', $data);
    }

    public function check_exist($table, $fname, $lname, $mname)
    {
        $result = $this->db->where('FirstName=', $fname);
        $result = $this->db->where('LastName=', $lname);
        $result = $this->db->where('MiddleName=', $mname);
        $result = $this->db->get($table);
        return $result;
    }

    public function check_single_row_exist($table, $emailval, $email)
    {
        $result = $this->db->where($emailval, $email);
        $result = $this->db->get($table);
        return $result;
    }

    public function update_app_count()
    {
        $data = array(
            'number' => $this->input->post('number'),
        );
        $this->db->where('id', 1);
        return $this->db->update('count', $data);
    }

    public function get_row_data($table, $col, $val)
    {
        $this->db->where($col, $val);
        $result = $this->db->get($table);
        return $result->row();
    }

    //Get Available Leave Records
    //  public function getLeaveRecords($id){
    //     $this->db->select('*');
    //     $this->db->from('hris_leaverecords');
    //     $this->db->where('IDNumber',$id);
    //     $query=$this->db->get();
    //     return $query->result();
    // }

    public function update_leave_records() {
        // Get the sum of vlCredit, slCredit, and coc per IDNumber from hris_leave_credits
        $query = $this->db->select('IDNumber, SUM(vlCredit) AS total_vl, SUM(slCredit) AS total_sl, SUM(coc) AS total_coc')
                          ->group_by('IDNumber')
                          ->get('hris_leave_credits');

        $leaveCredits = $query->result();

        // Loop through each result to update hris_leave_records
        foreach ($leaveCredits as $credit) {
            // Fetch the existing record for the given IDNumber
            $record = $this->db->select('vlTotal, slTotal, cocTotal, totalLeave')
                               ->where('IDNumber', $credit->IDNumber)
                               ->get('hris_leaverecords')
                               ->row();

            if ($record) {
                // Calculate new totals by adding the existing values and the new credits
                $new_vlTotal = $record->vlTotal + $credit->total_vl;
                $new_slTotal = $record->slTotal + $credit->total_sl;
                $new_cocTotal = $record->cocTotal + $credit->total_coc;
                $new_totalLeave = $record->totalLeave + ($credit->total_vl + $credit->total_sl + $credit->total_coc);

                // Update the hris_leave_records table
                $data = array(
                    'vlTotal' => $new_vlTotal,
                    'slTotal' => $new_slTotal,
                    'cocTotal' => $new_cocTotal,
                    'totalLeave' => $new_totalLeave
                );

                $this->db->where('IDNumber', $credit->IDNumber);
                $this->db->update('hris_leaverecords', $data);
            }
        }
    }

    function vlCount($id)
    {
        $query = $this->db->query("SELECT IDNumber, vlTotal, slTotal, cocTotal FROM hris_leaverecords where IDNumber='" . $id . "'");
        return $query->result();
    }

    public function update_profile_reg()
    {

        $id = $this->input->post('id');

        $data = array(
            'FirstName' => $this->input->post('FirstName'),
            'MiddleName' => $this->input->post('MiddleName'),
            'LastName' => $this->input->post('LastName'),
            'NameExtn' => $this->input->post('NameExtn'),
            'prefix' => $this->input->post('prefix'),
            'jobTitle' => $this->input->post('jobTitle'),
            'empPosition' => $this->input->post('empPosition'),
            'Department' => $this->input->post('Department'),
            'MaritalStatus' => $this->input->post('MaritalStatus'),
            'empStatus' => $this->input->post('empStatus'),
            'BirthDate' => $this->input->post('BirthDate'),
            'BirthPlace' => $this->input->post('BirthPlace'),
            'Sex' => $this->input->post('Sex'),
            'height' => $this->input->post('height'),
            'weight' => $this->input->post('weight'),
            'bloodType' => $this->input->post('bloodType'),
            'gsis' => $this->input->post('gsis'),
            'pagibig' => $this->input->post('pagibig'),
            'philHealth' => $this->input->post('philHealth'),
            'sssNo' => $this->input->post('sssNo'),
            'tinNo' => $this->input->post('tinNo'),
            'resHouseNo' => $this->input->post('resHouseNo'),
            'resStreet' => $this->input->post('resStreet'),
            'resVillage' => $this->input->post('resVillage'),
            'resBarangay' => $this->input->post('resBarangay'),
            'resCity' => $this->input->post('resCity'),
            'resProvince' => $this->input->post('resProvince'),
            'resZipCode' => $this->input->post('resZipCode'),
            'perHouseNo' => $this->input->post('resHouseNo'),
            'perStreet' => $this->input->post('resStreet'),
            'perVillage' => $this->input->post('resVillage'),
            'perBarangay' => $this->input->post('resBarangay'),
            'perCity' => $this->input->post('resCity'),
            'perProvince' => $this->input->post('resProvince'),
            'perZipCode' => $this->input->post('resZipCode'),
            'empTelNo' => $this->input->post('empTelNo'),
            'empMobile' => $this->input->post('contactNo'),
            'empEmail' => $this->input->post('empEmail'),
            'settingsID' => $this->input->post('settingsID'),
            'pronoun1' => $this->input->post('pronoun1'),
            'pronoun2' => $this->input->post('pronoun2'),
            'age' => $this->input->post('age'),
            'dateHired' => $this->input->post('dateHired'),
            'retirement' => $this->input->post('retirement'),
            'retYear' => $this->input->post('retYear'),
            'agencyCode' => $this->input->post('agencyCode'),
            'citizenship' => $this->input->post('citizenship'),
            'dualCitizenship' => $this->input->post('dualCitizenship'),
            'citizenshipType' => $this->input->post('citizenshipType'),
            'citizenshipCountry' => $this->input->post('citizenshipCountry'),
            'contactName' => $this->input->post('contactName'),
            'contactRel' => $this->input->post('contactRel'),
            'contactEmail' => $this->input->post('contactEmail'),
            'contactNo' => $this->input->post('empMobile'),
            'contactAddress' => $this->input->post('contactAddress'),
            //'fb' => $this->input->post('fb'),
            //'skype' => $this->input->post('skype'),
            'umid' => $this->input->post('umid'),
            'csEligibility' => $this->input->post('csEligibility'),
            'csLevel' => $this->input->post('csLevel'),
            'currentStatus' => $this->input->post('currentStatus'),
            'YearsAsJO' => $this->input->post('YearsAsJO'),
            'workNature1' => $this->input->post('workNature1'),
            'workNature2' => $this->input->post('workNature2'),
            'employeeNo' => $this->input->post('employeeNo'),
            'itemNo' => $this->input->post('itemNo'),
            'sgNo' => $this->input->post('sgNo'),
            'authAnSalary' => $this->input->post('authAnSalary'),
            'actualSalary' => $this->input->post('actualSalary'),
            'stepNo' => $this->input->post('stepNo'),
            'origAppointmentDate' => $this->input->post('origAppointmentDate'),
            'lastAppointmentDate' => $this->input->post('lastAppointmentDate'),
            'workStat' => $this->input->post('workStat'),
            'staCode' => $this->input->post('staCode'),
            'lastUpdate' => $this->input->post('lastUpdate'),
            'updatedBy' => $this->input->post('updatedBy'),
            'status' => $this->input->post('status'),
            'groups' => $this->input->post('groups'),
            'specialization' => $this->input->post('specialization'),
            'track' => $this->input->post('track'),
            'pre_school' => $this->input->post('pre_school'),
            'bd' => $this->input->post('bd'),
            'dg' => $this->input->post('dg'),
            'pgu' => $this->input->post('pgu'),
            'jhss' => $this->input->post('jhss'),
            'shss' => $this->input->post('shss'),
            'Major' => $this->input->post('Major'),
            'religion' => $this->input->post('religion'),
            'disability' => $this->input->post('disability'),
            'ethnicity' => $this->input->post('ethnicity')

        );

        $this->db->where('id', $id);
        return $this->db->update('hris_applicant', $data);
    }

    public function update_employee()
    {

        if ($this->session->userdata('position') === 'Admin') :
            $id = $this->input->post('id');
        elseif ($this->session->userdata('position') === 'School') :
            $id = $this->input->post('id');
        elseif ($this->session->userdata('position') === 'Staff') :
            $id = $this->input->post('id');
        elseif ($this->session->userdata('position') === 'Super Admin' || $this->session->userdata('position') === 'Human Resource Admin' || $this->session->userdata('position') === 'HR Staff' || $this->session->userdata('position') === 'asds') :
            $id = $this->input->post('id');
        elseif ($this->session->userdata('position') === 'user') :
            $id = $this->session->userdata('username');
        endif;

        date_default_timezone_set('Asia/Manila'); # add your city to set local time zone

        $data = array(
            'IDNumber' => $this->input->post('empNo'),
            'FirstName' => $this->input->post('FirstName'),
            'MiddleName' => $this->input->post('MiddleName'),
            'LastName' => $this->input->post('LastName'),
            'NameExtn' => $this->input->post('NameExtn'),
            'prefix' => $this->input->post('prefix'),
            'jobTitle' => $this->input->post('jobTitle'),
            'empPosition' => $this->input->post('empPosition'),
            'Department' => $this->input->post('Department'),
            'schoolID' => $this->input->post('schoolID'),
            'MaritalStatus' => $this->input->post('MaritalStatus'),
            'empStatus' => $this->input->post('empStatus'),
            'BirthDate' => $this->input->post('BirthDate'),
            'BirthPlace' => $this->input->post('BirthPlace'),
            'Sex' => $this->input->post('Sex'),
            'height' => $this->input->post('height'),
            'weight' => $this->input->post('weight'),
            'bloodType' => $this->input->post('bloodType'),
            'gsis' => $this->input->post('gsis'),
            'pagibig' => $this->input->post('pagibig'),
            'philHealth' => $this->input->post('philHealth'),
            'sssNo' => $this->input->post('sssNo'),
            'tinNo' => $this->input->post('tinNo'),
            'resHouseNo' => $this->input->post('resHouseNo'),
            'resStreet' => $this->input->post('resStreet'),
            'resVillage' => $this->input->post('resVillage'),
            'resBarangay' => $this->input->post('resBarangay'),
            'resCity' => $this->input->post('resCity'),
            'resProvince' => $this->input->post('resProvince'),
            'resZipCode' => $this->input->post('resZipCode'),
            'perHouseNo' => $this->input->post('resHouseNo'),
            'perStreet' => $this->input->post('resStreet'),
            'perVillage' => $this->input->post('resVillage'),
            'perBarangay' => $this->input->post('resBarangay'),
            'perCity' => $this->input->post('resCity'),
            'perProvince' => $this->input->post('resProvince'),
            'perZipCode' => $this->input->post('resZipCode'),
            'empTelNo' => $this->input->post('empTelNo'),
            'empMobile' => $this->input->post('empMobile'),
            'empEmail' => $this->input->post('empEmail'),
            'settingsID' => $this->input->post('settingsID'),
            'pronoun1' => $this->input->post('pronoun1'),
            'pronoun2' => $this->input->post('pronoun2'),
            'age' => $this->input->post('age'),
            'dateHired' => $this->input->post('dateHired'),
            'retirement' => $this->input->post('retirement'),
            'retYear' => $this->input->post('retYear'),
            'agencyCode' => $this->input->post('agencyCode'),
            'citizenship' => $this->input->post('citizenship'),
            'dualCitizenship' => $this->input->post('dualCitizenship'),
            'citizenshipType' => $this->input->post('citizenshipType'),
            'citizenshipCountry' => $this->input->post('citizenshipCountry'),
            'contactName' => $this->input->post('contactName'),
            'contactRel' => $this->input->post('contactRel'),
            'contactEmail' => $this->input->post('contactEmail'),
            'contactNo' => $this->input->post('contactNo'),
            'contactAddress' => $this->input->post('contactAddress'),
            'fb' => $this->input->post('fb'),
            'skype' => $this->input->post('skype'),
            'umid' => $this->input->post('umid'),
            'csEligibility' => $this->input->post('csEligibility'),
            'csLevel' => $this->input->post('csLevel'),
            'currentStatus' => $this->input->post('currentStatus'),
            'YearsAsJO' => $this->input->post('YearsAsJO'),
            'workNature1' => $this->input->post('workNature1'),
            'workNature2' => $this->input->post('workNature2'),
            'employeeNo' => $this->input->post('employeeNo'),
            'itemNo' => $this->input->post('itemNo'),
            'sgNo' => $this->input->post('sgNo'),
            'authAnSalary' => $this->input->post('authAnSalary'),
            'actualSalary' => $this->input->post('actualSalary'),
            'stepNo' => $this->input->post('stepNo'),
            'origAppointmentDate' => $this->input->post('origAppointmentDate'),
            'lastAppointmentDate' => $this->input->post('lastAppointmentDate'),
            'workStat' => $this->input->post('workStat'),
            'staCode' => $this->input->post('staCode'),
            'updatedBy' => $this->session->userdata('username'),
            'lastUpdate' => date("Y-m-d"),
            'leaveCredits' => $this->input->post('leaveCredits'),
            'payGroup' => $this->input->post('payGroup'),
            'payCat' => $this->input->post('payCat'),
            'directHead' => $this->input->post('directHead'),
            'bnk_acct' => $this->input->post('bnk_acct'),
            'leaveCredits' => $this->input->post('leaveCredits'),

            'directHeadPosition' => $this->input->post('directHeadPosition'),
            'religion' => $this->input->post('religion'),
            'disability' => $this->input->post('disability'),
            'ethnicity' => $this->input->post('ethnicity')
     
        );

        $this->db->where('IDNumber', $id);
        return $this->db->update('hris_staff', $data);
    }

    public function update_staff()
    {
        date_default_timezone_set('Asia/Manila'); # add your city to set local time zone
        $id = $this->input->post('id');

        $data = array(
            'FirstName' => $this->input->post('FirstName'),
            'MiddleName' => $this->input->post('MiddleName'),
            'LastName' => $this->input->post('LastName'),
            'NameExtn' => $this->input->post('NameExtn'),
            'prefix' => $this->input->post('prefix'),
            'jobTitle' => $this->input->post('jobTitle'),
            'empPosition' => $this->input->post('empPosition'),
            'Department' => $this->input->post('Department'),
            'schoolID' => $this->input->post('schoolID'),
            'MaritalStatus' => $this->input->post('MaritalStatus'),
            'empStatus' => $this->input->post('empStatus'),
            'BirthDate' => $this->input->post('BirthDate'),
            'BirthPlace' => $this->input->post('BirthPlace'),
            'Sex' => $this->input->post('Sex'),
            'height' => $this->input->post('height'),
            'weight' => $this->input->post('weight'),
            'bloodType' => $this->input->post('bloodType'),
            'gsis' => $this->input->post('gsis'),
            'pagibig' => $this->input->post('pagibig'),
            'philHealth' => $this->input->post('philHealth'),
            'sssNo' => $this->input->post('sssNo'),
            'tinNo' => $this->input->post('tinNo'),
            'resHouseNo' => $this->input->post('resHouseNo'),
            'resStreet' => $this->input->post('resStreet'),
            'resVillage' => $this->input->post('resVillage'),
            'resBarangay' => $this->input->post('resBarangay'),
            'resCity' => $this->input->post('resCity'),
            'resProvince' => $this->input->post('resProvince'),
            'resZipCode' => $this->input->post('resZipCode'),
            'perHouseNo' => $this->input->post('resHouseNo'),
            'perStreet' => $this->input->post('resStreet'),
            'perVillage' => $this->input->post('resVillage'),
            'perBarangay' => $this->input->post('resBarangay'),
            'perCity' => $this->input->post('resCity'),
            'perProvince' => $this->input->post('resProvince'),
            'perZipCode' => $this->input->post('resZipCode'),
            'empTelNo' => $this->input->post('empTelNo'),
            'empMobile' => $this->input->post('empMobile'),
            'empEmail' => $this->input->post('empEmail'),
            'settingsID' => $this->input->post('settingsID'),
            'pronoun1' => $this->input->post('pronoun1'),
            'pronoun2' => $this->input->post('pronoun2'),
            'age' => $this->input->post('age'),
            'dateHired' => $this->input->post('dateHired'),
            'retirement' => $this->input->post('retirement'),
            'retYear' => $this->input->post('retYear'),
            'agencyCode' => $this->input->post('agencyCode'),
            'citizenship' => $this->input->post('citizenship'),
            'dualCitizenship' => $this->input->post('dualCitizenship'),
            'citizenshipType' => $this->input->post('citizenshipType'),
            'citizenshipCountry' => $this->input->post('citizenshipCountry'),
            'contactName' => $this->input->post('contactName'),
            'contactRel' => $this->input->post('contactRel'),
            'contactEmail' => $this->input->post('contactEmail'),
            'contactNo' => $this->input->post('contactNo'),
            'contactAddress' => $this->input->post('contactAddress'),
            'fb' => $this->input->post('fb'),
            'skype' => $this->input->post('skype'),
            'umid' => $this->input->post('umid'),
            'csEligibility' => $this->input->post('csEligibility'),
            'csLevel' => $this->input->post('csLevel'),
            'currentStatus' => $this->input->post('currentStatus'),
            'YearsAsJO' => $this->input->post('YearsAsJO'),
            'workNature1' => $this->input->post('workNature1'),
            'workNature2' => $this->input->post('workNature2'),
            'employeeNo' => $this->input->post('employeeNo'),
            'itemNo' => $this->input->post('itemNo'),
            'sgNo' => $this->input->post('sgNo'),
            'authAnSalary' => $this->input->post('authAnSalary'),
            'actualSalary' => $this->input->post('actualSalary'),
            'stepNo' => $this->input->post('stepNo'),
            'origAppointmentDate' => $this->input->post('origAppointmentDate'),
            'lastAppointmentDate' => $this->input->post('lastAppointmentDate'),
            'workStat' => $this->input->post('workStat'),
            'staCode' => $this->input->post('staCode'),
            'updatedBy' => $this->session->userdata('username'),
            'lastUpdate' => date("Y-m-d"),
            'leaveCredits' => $this->input->post('leaveCredits'),
            'payGroup' => $this->input->post('payGroup'),
            'payCat' => $this->input->post('payCat'),
            'directHead' => $this->input->post('directHead'),
            'bnk_acct' => $this->input->post('bnk_acct'),
            'religion' => $this->input->post('religion'),
            'disability' => $this->input->post('disability'),
            'ethnicity' => $this->input->post('ethnicity'),
            
            'directHeadPosition' => $this->input->post('directHeadPosition')

        );

        $this->db->where('IDNumber', $id);
        return $this->db->update('hris_staff', $data);
    }

    


    public function update_user_acct()
    {

        if ($this->session->position != 'user') {
            $id = $this->input->post('id');
        } else {
            $id = $this->session->userdata('username');
        }


        $data = array(
            'username' => $this->input->post('empNo'),
            'user_id' => $this->input->post('empNo'),
            'fname' => $this->input->post('FirstName'),
            'mname' => $this->input->post('MiddleName'),
            'lname' => $this->input->post('LastName')

        );

        $this->db->where('username', $id);
        return $this->db->update('users', $data);
    }

    public function update_user_account()
    {

        $data = array(
            'username' => $this->input->post('empNo'),
            'user_id' => $this->input->post('empNo'),
            'fname' => $this->input->post('FirstName'),
            'mname' => $this->input->post('MiddleName'),
            'lname' => $this->input->post('LastName')
        );

        $this->db->where('username', $this->input->post('id'));
        return $this->db->update('users', $data);
    }


    public function check_ownership($param)
    {
        if ($this->session->position == "reg") {
            if ($this->session->c_id != $param) {
                redirect(base_url() . 'registered_profile/' . $this->session->c_id);
            }
        }  else if ($this->session->position == "user") {
            if ($this->session->c_id != $param) {
                redirect(base_url() . 'Page/schoolDashboard/' . $this->session->c_id);
            } else if ($this->session->position == "Super Admin") {
                if ($this->session->c_id != $param) {
                    redirect(base_url() . 'Pages/view/' . $this->session->c_id);
                }
            }

            // else if ($this->session->position == "Admin") {
            //     redirect('Pages/view');
            // }
        }
    }

    public function check_position_ownership($user_id, $position, $link)
    {
        if ($this->session->position == $position) {
            if ($this->session->c_id != $user_id) {
                redirect(base_url() . $link);
            }
        }
    }

    public function change_pass()
    {

        $password = $this->input->post('pass');
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $data = array(
            'password' => $hash
        );

        $this->db->where('username', $this->session->username);
        return $this->db->update('users', $data);
    }

    public function change_pass_school_emp()
    {

        $password = 'davor112';
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $data = array(
            'password' => $hash
        );

        $this->db->where('username', $this->uri->segment(3));
        return $this->db->update('users', $data);
    }

    public function user_pass_change($param)
    {

        $password = 1;
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $data = array(
            'password' => $hash
        );

        $this->db->where('id', $param);
        return $this->db->update('users', $data);
    }

    public function cluster_jhs()
    {
        $query = $this->db->query("SELECT * FROM hris_applications a join hris_applicant r on a.empEmail=r.empEmail where dq=1 GROUP BY specialization");
        return $query->result();
    }

    public function cluster_shs()
    {
        $query = $this->db->query("SELECT a.empEmail,r.empEmail,dq,r.shss FROM hris_applications a join hris_applicant r on a.empEmail=r.empEmail where dq=1 GROUP BY r.shss");
        return $query->result();
    }


    // public function rqa($jobID)
    // {
    //     $query = $this->db->query("SELECT a.appID,r.appID,id, record_no, education, training, experience, let_rating, demo_rating, tr_rating, total_points FROM hris_applications a join hris_applications_rating r on a.appID=r.appID where jobID='" . $jobID . "' and total_points >= '50' ORDER BY total_points DESC");
    //     return $query->result();
    // }

    // public function rqa($jobID)
    // {
    //     $this->db->select('a.appID, r.appID, id, record_no, education, training, experience, let_rating, demo_rating, tr_rating, total_points, empEmail,app_year');
    //     $this->db->from('hris_applications a');
    //     $this->db->join('hris_applications_rating r', 'a.appID = r.appID');
    //     $this->db->where('a.jobID', $jobID);
    //     $this->db->where('dq', 1);
    //     $this->db->where('total_points >=', 50);
    //     $this->db->order_by('total_points', 'DESC');
        
    //     $query = $this->db->get();
    //     return $query->result();
    // }

    public function rqa($jobID)
    {
        $this->db->select("
        a.*,
        r.education, r.training, r.experience, r.let_rating, r.demo_rating, r.tr_rating, r.total_points,
        COALESCE(app.record_no, staff.IDNumber) AS code,
        COALESCE(app.empEmail, staff.IDNumber) AS renren,
        COALESCE(app.contactNo, staff.contactNo) AS contactNo,
        COALESCE(app.FirstName, staff.FirstName) AS FirstName,
        COALESCE(app.MiddleName, staff.MiddleName) AS MiddleName,
        COALESCE(app.NameExtn, staff.NameExtn) AS NameExtn,
        COALESCE(app.LastName, staff.LastName) AS LastName,
        COALESCE(app.perProvince, staff.perProvince) AS province,
        COALESCE(app.specialization, staff.specialization) AS specialization,
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
    $this->db->where('a.jobID', $jobID);
    $this->db->where('dq', 1);
    $this->db->where('total_points >=', 50);

    $this->db->group_start();
    $this->db->where('app.record_no IS NOT NULL', null, false);
    $this->db->or_where('staff.IDNumber IS NOT NULL', null, false);
    $this->db->group_end();


    $this->db->order_by('r.total_points', 'DESC');

    $query = $this->db->get();
    return $query->result();
    }

    /**
     * Ranked RQA list (highest -> lowest total_points) used by the
     * RQA Recommendation report. Same qualifying rules as rqa() but it
     * also excludes any application that has already been recommended
     * (i.e. already has an Item Number assigned in hris_rqa_recommendation).
     */
    private function rqa_major_select_sql()
    {
        return $this->db->field_exists('Major', 'hris_applicant')
            ? 'app.Major AS Major'
            : "'' AS Major";
    }

    public function rqa_ranked($jobID)
    {
        // Accept a single jobID or a list of jobIDs. Several vacancy records can
        // share the same position (same title/type/year); the RQA Recommendation
        // page merges them into one entry, so the ranked list spans every jobID.
        $jobIDs = array_values(array_unique(array_filter(array_map('intval', (array) $jobID), function ($v) {
            return $v > 0;
        })));
        if (empty($jobIDs)) {
            return [];
        }
        $jobIdList = implode(',', $jobIDs);
        $majorSelect = $this->rqa_major_select_sql();

        $this->db->select("
        a.*,
        r.education, r.training, r.experience, r.let_rating, r.demo_rating, r.tr_rating, r.total_points,
        COALESCE(app.record_no, staff.IDNumber) AS code,
        COALESCE(app.empEmail, staff.IDNumber) AS renren,
        COALESCE(app.contactNo, staff.contactNo) AS contactNo,
        COALESCE(app.FirstName, staff.FirstName) AS FirstName,
        COALESCE(app.MiddleName, staff.MiddleName) AS MiddleName,
        COALESCE(app.NameExtn, staff.NameExtn) AS NameExtn,
        COALESCE(app.LastName, staff.LastName) AS LastName,
        COALESCE(app.perProvince, staff.perProvince) AS province,
        COALESCE(app.specialization, staff.specialization) AS specialization,
        COALESCE(app.jhss, staff.jhss) AS jhss,
        COALESCE(app.shss, staff.shss) AS shss,
        $majorSelect,
        COALESCE(app.resCity, staff.resCity) AS resCity,
        COALESCE(app.resBarangay, staff.resBarangay) AS brgy,
        COALESCE(app.tribe, '') AS tribe
    ");
        $this->db->from('hris_applications a');
        $this->db->join('hris_applicant app', 'a.empEmail = app.empEmail', 'left');
        $this->db->join('hris_staff staff', 'app.record_no IS NULL AND a.empEmail = staff.IDNumber', 'left');
        $this->db->join('hris_applications_rating r', 'a.appID = r.appID', 'left');
        $this->db->where_in('a.jobID', $jobIDs);
        $this->db->where('a.dq', 1);
        $this->db->where('r.total_points >=', 50);

        $this->db->group_start();
        $this->db->where('app.record_no IS NOT NULL', null, false);
        $this->db->or_where('staff.IDNumber IS NOT NULL', null, false);
        $this->db->group_end();

        // Hide applicants that were already recommended for any of these jobs
        $this->db->where('a.appID NOT IN (SELECT appID FROM hris_rqa_recommendation WHERE jobID IN (' . $jobIdList . '))', null, false);

        $this->db->order_by('r.total_points', 'DESC');

        return $this->db->get()->result();
    }

    /**
     * Qualified applicants for the Corrigendum / Addendum page. Unlike
     * rqa_ranked() this keeps applicants whose rating row is missing or below
     * 50 (LEFT JOIN on the rating, no score gate) so their score can be added
     * or corrected, and does not exclude already-recommended applicants. The
     * current corrigendum/addendum type is joined in for display.
     */
    public function rqa_corrigendum_applicants($jobID)
    {
        $jobIDs = array_values(array_unique(array_filter(array_map('intval', (array) $jobID), function ($v) {
            return $v > 0;
        })));
        if (empty($jobIDs)) {
            return [];
        }
        $majorSelect = $this->rqa_major_select_sql();

        $this->db->select("
            a.appID, a.jobID, a.empEmail, a.dq,
            r.education, r.training, r.experience, r.let_rating, r.demo_rating, r.tr_rating, r.total_points,
            COALESCE(app.record_no, staff.IDNumber) AS code,
            COALESCE(app.empEmail, staff.IDNumber) AS renren,
            COALESCE(app.FirstName, staff.FirstName) AS FirstName,
            COALESCE(app.MiddleName, staff.MiddleName) AS MiddleName,
            COALESCE(app.NameExtn, staff.NameExtn) AS NameExtn,
            COALESCE(app.LastName, staff.LastName) AS LastName,
            COALESCE(app.specialization, staff.specialization) AS specialization,
            COALESCE(app.jhss, staff.jhss) AS jhss,
            COALESCE(app.shss, staff.shss) AS shss,
            $majorSelect,
            COALESCE(app.resCity, staff.resCity) AS resCity,
            COALESCE(app.resBarangay, staff.resBarangay) AS brgy,
            cor.type AS corrigendum_type
        ");
        $this->db->from('hris_applications a');
        $this->db->join('hris_applicant app', 'a.empEmail = app.empEmail', 'left');
        $this->db->join('hris_staff staff', 'app.record_no IS NULL AND a.empEmail = staff.IDNumber', 'left');
        $this->db->join('hris_applications_rating r', 'a.appID = r.appID', 'left');
        $this->db->join('hris_rqa_corrigendum cor', 'cor.appID = a.appID', 'left');
        $this->db->where_in('a.jobID', $jobIDs);
        $this->db->where('a.dq', 1);

        $this->db->group_start();
        $this->db->where('app.record_no IS NOT NULL', null, false);
        $this->db->or_where('staff.IDNumber IS NOT NULL', null, false);
        $this->db->group_end();

        $this->db->order_by('LastName', 'ASC');
        $this->db->order_by('FirstName', 'ASC');

        return $this->db->get()->result();
    }

    /**
     * Applicants for JHS vacancies whose Learning Area (jhss) is still blank.
     * This intentionally does not enforce RQA score/DQ thresholds because the
     * report is for profile cleanup before recommendation work.
     */
    public function rqa_missing_jhs_learning_area($jobID)
    {
        $jobIDs = array_values(array_unique(array_filter(array_map('intval', (array) $jobID), function ($v) {
            return $v > 0;
        })));
        if (empty($jobIDs)) {
            return [];
        }

        $this->db->select("
            a.appID, a.jobID, a.empEmail, a.appStatus, a.dq,
            r.education, r.training, r.experience, r.let_rating, r.demo_rating, r.tr_rating, r.total_points,
            COALESCE(NULLIF(app.record_no, ''), staff.IDNumber) AS code,
            COALESCE(app.id, staff.IDNumber) AS profile_id,
            COALESCE(app.empEmail, staff.empEmail, staff.IDNumber) AS renren,
            COALESCE(app.contactNo, app.empMobile, staff.contactNo, staff.empMobile) AS contactNo,
            COALESCE(app.FirstName, staff.FirstName) AS FirstName,
            COALESCE(app.MiddleName, staff.MiddleName) AS MiddleName,
            COALESCE(app.NameExtn, staff.NameExtn) AS NameExtn,
            COALESCE(app.LastName, staff.LastName) AS LastName,
            COALESCE(app.resCity, staff.resCity) AS resCity,
            COALESCE(app.resBarangay, staff.resBarangay) AS brgy,
            COALESCE(app.jhss, staff.jhss) AS jhss,
            CASE
                WHEN app.id IS NOT NULL THEN 'profile_reg_edit'
                WHEN staff.IDNumber IS NOT NULL THEN 'employee_edit'
                ELSE ''
            END AS profile_route
        ");
        $this->db->from('hris_applications a');
        $this->db->join('hris_jobvacancy jv', 'jv.jobID = a.jobID', 'left');
        $this->db->join('hris_applicant app', 'a.empEmail = app.empEmail', 'left');
        $this->db->join('hris_staff staff', 'app.record_no IS NULL AND a.empEmail = staff.IDNumber', 'left');
        $this->db->join('hris_applications_rating r', 'a.appID = r.appID', 'left');
        $this->db->where_in('a.jobID', $jobIDs);
        $this->db->where_in('jv.job_type', [3, 8, 16]);
        $this->db->where("TRIM(COALESCE(app.jhss, staff.jhss, '')) = ''", null, false);
        $this->db->group_start();
        $this->db->where('app.id IS NOT NULL', null, false);
        $this->db->or_where('staff.IDNumber IS NOT NULL', null, false);
        $this->db->group_end();
        $this->db->order_by('r.total_points', 'DESC');
        $this->db->order_by('LastName', 'ASC');
        $this->db->order_by('FirstName', 'ASC');

        return $this->db->get()->result();
    }

    /**
     * Position options for the RQA Recommendation report.
     *
     * Unlike scores_report_job_options(), this intentionally ignores the
     * Open/Closed status so the recommendation work can continue on positions
     * whose vacancy has already been closed. The report is instead scoped by
     * the vacancy's school year (sy). Pass a $year to limit to that year.
     */
    public function rqa_job_options($year = null)
    {
        $this->db
            ->select('jobID, jobTitle, job_type, sy')
            ->from('hris_jobvacancy')
            ->where('job_type !=', 0);

        if (!empty($year)) {
            $this->db->where('sy', (int) $year);
        }

        return $this->db
            ->order_by('sy', 'desc')
            ->order_by('job_type', 'asc')
            ->order_by('jobTitle', 'asc')
            ->get()
            ->result();
    }

    /**
     * Distinct school years (sy) that have at least one vacancy, newest first.
     * Drives the Year filter on the RQA Recommendation report.
     */
    public function rqa_available_years()
    {
        $rows = $this->db
            ->select('sy')
            ->distinct()
            ->from('hris_jobvacancy')
            ->where('job_type !=', 0)
            ->where('sy IS NOT NULL', null, false)
            ->where('sy !=', 0)
            ->order_by('sy', 'desc')
            ->get()
            ->result();

        $years = [];
        foreach ($rows as $r) {
            $years[] = (int) $r->sy;
        }

        return $years;
    }

    /**
     * Recommended applicants for the SDS approval screen.
     * Pulls everything stored in hris_rqa_recommendation for the given status
     * and joins the live rating + applicant/staff details so the approver sees
     * the full comparative assessment. Ordered by position then highest RQA.
     */
    public function recommended_for_approval($status = 'recommended', $jobID = null)
    {
        $majorSelect = $this->rqa_major_select_sql();

        $this->db->select("
            rec.id AS rec_id, rec.item_number, rec.remarks, rec.status, rec.created_at,
            rec.applicant_name AS rec_name, rec.total_points AS rec_total,
            rec.school_id, rec.school_name, rec.date_hired, rec.date_waived,
            a.appID, a.jobID, a.empEmail,
            jv.jobTitle, jv.job_type,
            r.education, r.training, r.experience, r.let_rating, r.demo_rating, r.tr_rating, r.total_points,
            COALESCE(app.record_no, staff.IDNumber) AS code,
            COALESCE(app.contactNo, staff.contactNo) AS contactNo,
            COALESCE(app.FirstName, staff.FirstName) AS FirstName,
            COALESCE(app.MiddleName, staff.MiddleName) AS MiddleName,
            COALESCE(app.NameExtn, staff.NameExtn) AS NameExtn,
            COALESCE(app.LastName, staff.LastName) AS LastName,
            COALESCE(app.specialization, staff.specialization) AS specialization,
            COALESCE(app.jhss, staff.jhss) AS jhss,
            COALESCE(app.shss, staff.shss) AS shss,
            $majorSelect,
            COALESCE(app.resCity, staff.resCity) AS resCity,
            COALESCE(app.resBarangay, staff.resBarangay) AS brgy,
            COALESCE(app.tribe, '') AS tribe
        ");
        $this->db->from('hris_rqa_recommendation rec');
        $this->db->join('hris_applications a', 'a.appID = rec.appID', 'left');
        $this->db->join('hris_jobvacancy jv', 'jv.jobID = rec.jobID', 'left');
        $this->db->join('hris_applicant app', 'a.empEmail = app.empEmail', 'left');
        $this->db->join('hris_staff staff', 'app.record_no IS NULL AND a.empEmail = staff.IDNumber', 'left');
        $this->db->join('hris_applications_rating r', 'a.appID = r.appID', 'left');
        if (is_array($status)) {
            $this->db->where_in('rec.status', $status);
        } else {
            $this->db->where('rec.status', $status);
        }
        if (!empty($jobID)) {
            $this->db->where('rec.jobID', (int) $jobID);
        }
        $this->db->where('COALESCE(r.total_points, rec.total_points) >= 50', null, false);
        $this->db->order_by('jv.jobTitle', 'ASC');
        $this->db->order_by('r.total_points', 'DESC');

        return $this->db->get()->result();
    }

    public function rqa_promotion($jobID)
    {
        $this->db->select("
        a.*,
        r.educ, r.trainings, r.experience, r.performance, r.ppstco, r.ppstpa, r.total_points,
        COALESCE(app.record_no, staff.IDNumber) AS code,
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
        COALESCE(app.resBarangay, staff.resBarangay) AS brgy
    ");
    $this->db->from('hris_applications a');
    $this->db->join('hris_applicant app', 'a.empEmail = app.empEmail', 'left');
    $this->db->join('hris_staff staff', 'app.record_no IS NULL AND a.empEmail = staff.IDNumber', 'left');
    $this->db->join('hris_rating_promotion r', 'a.appID = r.appID', 'left');
    $this->db->where('a.jobID', $jobID);
    $this->db->where('dq', 1);
    $this->db->where('total_points >=', 50);
    $this->db->order_by('r.total_points', 'DESC');

    $query = $this->db->get();
    return $query->result();
    }

    public function rqa_cluster($jobID, $district)
    {
        $query = $this->db->query("SELECT * FROM hris_applications a join hris_applications_rating r on a.appID=r.appID where jobID='" . $jobID . "' and district='" . $district . "' and dq=1 and  total_points>='50' ORDER BY total_points DESC");
        return $query->result();
    }

    public function rqa_cluster_jhs($jobID, $specialization)
    {
        $query = $this->db->query("SELECT * FROM hris_applications a join hris_applications_rating r on a.appID=r.appID join hris_applicant i on a.empEmail=i.empEmail where jobID='" . $jobID . "' and dq=1 and i.specialization='" . $specialization . "' and  total_points>='50' ORDER BY total_points DESC");
        return $query->result();
    }

    public function rqa_cluster_shs_count($jobID, $shss)
    {
        $query = $this->db->query("SELECT a.appID,r.appID,a.empEmail,i.empEmail,jobID,dq,total_points FROM hris_applications a join hris_applications_rating r on a.appID=r.appID join hris_applicant i on a.empEmail=i.empEmail where jobID='" . $jobID . "' and dq=1 and i.shss='" . $shss . "' and  total_points>='50' ORDER BY total_points DESC");
        return $query;
    }

    public function rqa_cluster_shs_count_mun($jobID, $shss, $mun)
{
    // Using prepared statements to avoid SQL injection
    $sql = "
        SELECT
            a.appID,         
            a.appStatus,
            a.dq,
            a.jobID,
            a.empEmail,
            ap.empEmail,    
            ap.shss,
            ap.resCity,
            ap.id,              
            st.IDNumber,        
            st.shss,
            st.resCity,
            ar.appID,
            ar.record_no,
            ar.education,
            ar.training,
            ar.total_points
        FROM
            hris_applications a
        JOIN
            hris_applicant ap ON a.empEmail = ap.empEmail 
        LEFT JOIN
            hris_staff st ON a.empEmail = st.empEmail
        LEFT JOIN
            hris_applications_rating ar ON a.appID = ar.appID
        WHERE
            (ap.shss = ? OR st.shss = ?)
            AND a.jobID = ?
            AND (ap.resCity = ? OR st.resCity = ?)
            AND total_points>='50';
    ";

    // Prepare and execute the query
    $query = $this->db->query($sql, array($shss, $shss, $jobID, $mun, $mun));

    return $query;
}

    public function rqa_cluster_shs_count_mun_print($jobID, $shss,$mun)
    {
        // Using prepared statements to avoid SQL injection
        $sql = "
            SELECT
            a.appID,         
            a.appStatus,
            a.dq,
            a.jobID,
            a.empEmail,
            ap.empEmail,    
            ap.shss,
            ap.resCity,
            ap.id,              
            st.IDNumber,        
            st.shss,
            st.resCity,
            ar.appID,
            ar.record_no,
            ar.education,
            ar.training,
            ar.experience,
            ar.let_rating,
            ar.demo_rating,
            ar.tr_rating,
            ar.total_points
        FROM
            hris_applications a
        JOIN
            hris_applicant ap ON a.empEmail = ap.empEmail 
        LEFT JOIN
            hris_staff st ON a.empEmail = st.empEmail
        LEFT JOIN
            hris_applications_rating ar ON a.appID = ar.appID
        WHERE
            (ap.shss = ? OR st.shss = ?)
            AND a.jobID = ?
            AND (ap.resCity = ? OR st.resCity = ?)
            AND total_points>='50';
    ";

        // Prepare and execute the query
        $query = $this->db->query($sql, array($shss, $shss, $jobID,$mun,$mun));

        return $query->result();
    }

    public function rqa_cluster_shs($jobID, $shss)
    {
        $query = $this->db->query("SELECT * FROM hris_applications a join hris_applications_rating r on a.appID=r.appID join hris_applicant i on a.empEmail=i.empEmail where jobID='" . $jobID . "' and dq=1 and i.shss='" . $shss . "' and  total_points>='50' ORDER BY total_points DESC");
        return $query->result();
    }


    public function rqa_cluster_jhs_count($jobID, $specialization)
    {
        $query = $this->db->query("SELECT * FROM hris_applications a join hris_applications_rating r on a.appID=r.appID join hris_applicant i on a.empEmail=i.empEmail where jobID='" . $jobID . "' and dq=1 and i.specialization='" . $specialization . "' and  total_points>='50' ORDER BY total_points DESC");
        return $query;
    }

    public function rqa_cluster_count($jobID, $district)
    {
        $query = $this->db->query("SELECT * FROM hris_applications a join hris_applications_rating r on a.appID=r.appID where jobID='" . $jobID . "' and district='" . $district . "' and dq=1 and  total_points>='50' ORDER BY total_points DESC");
        return $query;
    }

    // public function car($jobID)
    // {
    //     $query = $this->db->query("SELECT * FROM hris_applications a join hris_applications_rating r on a.appID=r.appID where jobID='" . $jobID . "' and a.dq=1  ORDER BY total_points DESC");
    //     return $query->result();
    // }

    // public function car($jobID)
    // {
    //     $this->db->select('
    //     a.appID,
    //     a.jobID,
    //     a.dq,
    //     a.empEmail,
    //     r.education,
    //     r.training,
    //     r.experience,
    //     r.let_rating,
    //     r.demo_rating,
    //     r.tr_rating,
    //     r.total_points,
    //     r.record_no
    // ');
    // $this->db->from('hris_applications a');
    // $this->db->join('hris_applications_rating r', 'a.appID = r.appID');
    // $this->db->where('a.jobID', $jobID);
    // $this->db->where('a.dq', 1);
    // $this->db->order_by('r.total_points', 'DESC'); 

    // $query = $this->db->get();
    // return $query->result();
    // }

    public function car($jobID)
    {
        $this->db->select("
        a.*,
        r.education, r.training, r.experience, r.let_rating, r.demo_rating, r.tr_rating, r.total_points,
        COALESCE(app.record_no, staff.IDNumber) AS code,
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
        COALESCE(app.resBarangay, staff.resBarangay) AS brgy
    ");
    $this->db->from('hris_applications a');
    $this->db->join('hris_applicant app', 'a.empEmail = app.empEmail', 'left');
    $this->db->join('hris_staff staff', 'app.record_no IS NULL AND a.empEmail = staff.IDNumber', 'left');
    $this->db->join('hris_applications_rating r', 'a.appID = r.appID', 'left');
    $this->db->where('a.jobID', $jobID);
    $this->db->order_by('r.total_points', 'DESC');

    $query = $this->db->get();
    return $query->result();
    }

    // public function car_nlet($jobID)
    // {
    //     $query = $this->db->query("SELECT * FROM hris_applications a join hris_applications_rating r on a.appID=r.appID where jobID='" . $jobID . "' and dq=1 and let_rating=0 and total_points>='50' ORDER BY total_points DESC");
    //     return $query->result();
    // }

    public function car_nlet($jobID)
    {
        $this->db->select("
        a.*,
        r.education, r.training, r.experience, r.let_rating, r.demo_rating, r.tr_rating, r.total_points,
        COALESCE(app.record_no, staff.IDNumber) AS code,
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
        COALESCE(app.resBarangay, staff.resBarangay) AS brgy
    ");
    $this->db->from('hris_applications a');
    $this->db->join('hris_applicant app', 'a.empEmail = app.empEmail', 'left');
    $this->db->join('hris_staff staff', 'app.record_no IS NULL AND a.empEmail = staff.IDNumber', 'left');
    $this->db->join('hris_applications_rating r', 'a.appID = r.appID', 'left');
    $this->db->where('a.jobID', $jobID);
    $this->db->where('a.dq', 1);
    $this->db->where('r.let_rating', 0);
    $this->db->where('total_points >=', 50);
    $this->db->order_by('r.total_points', 'DESC');

    $query = $this->db->get();
    return $query->result();
    }

    public function rqa_specialization($jobID, $spe)
    {
        $query = $this->db->query("SELECT * FROM hris_applications a join hris_applications_rating r on a.appID=r.appID join hris_applicant ha on ha.record_no=r.record_no where jobID='" . $jobID . "' and specialization='" . $spe . "' and dq=1 and  total_points>='50' ORDER BY total_points DESC");
        return $query->result();
    }

    // public function rqa_mun($jobID, $mun)
    // {
    //     $query = $this->db->query("SELECT * FROM hris_applications a join hris_applications_rating r on a.appID=r.appID join hris_applicant ha on ha.record_no=r.record_no join hris_staff hs on a.empEmail=hs.IDNumber  where jobID='" . $jobID . "' and ha.resCity='" . $mun . "' and dq=1 and  total_points>='50' ORDER BY total_points DESC");
    //     return $query->result();
    // }

   

    // public function rqa_mun($jobID)
    // {
    //     $this->db->select('a.appID, r.appID, id, record_no, education, training, experience, let_rating, demo_rating, tr_rating, total_points, empEmail,app_year');
    //     $this->db->from('hris_applications a');
    //     $this->db->join('hris_applications_rating r', 'a.appID = r.appID');
    //     $this->db->where('a.jobID', $jobID);
    //     $this->db->where('dq', 1);
    //     $this->db->where('total_points >=', 50);
    //     $this->db->order_by('total_points', 'DESC');
        
    //     $query = $this->db->get();
    //     return $query->result();
    // }

    // public function rqa_mun($jobID, $resCity = null)
    // {
    //     $jobID = $this->input->get('id');
    //     $mun = $this->input->get('mun');

    //     $this->db->select('a.appID, a.empEmail, a.app_year, ha.record_no,
    //                     r.education, r.training, r.experience, 
    //                     r.let_rating, r.demo_rating, r.tr_rating, r.total_points,
    //                     ha.resCity');
    //     $this->db->from('hris_applications a');
    //     $this->db->join('hris_applications_rating r', 'a.appID = r.appID');
    //     $this->db->join('hris_applicant ha', 'a.applicant_id = ha.id'); 
    //     $this->db->where('a.jobID', $jobID);
    //     $this->db->where('a.dq', 1);
    //     $this->db->where('r.total_points >=', 50);

    //     if (!empty($resCity)) {
    //         $this->db->where('ha.resCity', $mun);
    //     }

    //     $this->db->order_by('r.total_points', 'DESC');

    //     $query = $this->db->get();
    //     return $query->result();
    // }

    // public function rqa_mun($jobID, $mun)
    // {
    //     $this->db->select("
    //         a.*,
    //         r.education, r.training, r.experience, r.let_rating, r.demo_rating, r.tr_rating, r.total_points,
    //         app.record_no AS code,
    //         app.empEmail AS renren,
    //         app.contactNo,
    //         app.FirstName,
    //         app.MiddleName,
    //         app.NameExtn,
    //         app.LastName,
    //         app.perProvince AS province,
    //         app.jhss,
    //         app.shss,
    //         app.resCity,
    //         app.resBarangay AS brgy
    //     ");

    //     $this->db->from('hris_applications a');

    //     // INNER JOIN = only show applications with existing applicant record
    //     $this->db->join('hris_applicant app', 'a.empEmail = app.empEmail', 'inner');

    //     $this->db->join('hris_applications_rating r', 'a.appID = r.appID', 'left');

    //     $this->db->where('a.jobID', $jobID);
    //     $this->db->where('r.total_points >=', 50);
    //     $this->db->where('app.resCity', $mun);

    //     $this->db->order_by('r.total_points', 'DESC');

    //     $query = $this->db->get();
    //     return $query->result();
    // }

    public function rqa_mun($jobID, $mun)
{
    $this->db->select("
        a.*,
        r.education,
        r.training,
        r.experience,
        r.let_rating,
        r.demo_rating,
        r.tr_rating,
        r.total_points,

        COALESCE(app.record_no, staff.IDNumber) AS code,
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
        COALESCE(app.resBarangay, staff.resBarangay) AS brgy
    ", false);

    $this->db->from('hris_applications a');

    $this->db->join(
        'hris_applicant app',
        'a.empEmail = app.empEmail',
        'left'
    );

    $this->db->join(
        'hris_staff staff',
        'app.record_no IS NULL AND a.empEmail = staff.IDNumber',
        'left'
    );

    $this->db->join(
        'hris_applications_rating r',
        'a.appID = r.appID',
        'left'
    );

    $this->db->where('a.jobID', $jobID);
    $this->db->where('r.total_points >=', 50);

    // Correct municipality/city filter
    $this->db->where(
        "COALESCE(app.resCity, staff.resCity) = " . $this->db->escape($mun),
        null,
        false
    );

    $this->db->order_by('r.total_points', 'DESC');

    $query = $this->db->get();
    return $query->result();
}

    

    public function rqa_mun_track($jobID, $mun, $spec)
    {
        $query = $this->db->query("SELECT * FROM hris_applications a join hris_applications_rating r on a.appID=r.appID join hris_applicant ha on ha.record_no=r.record_no where jobID='" . $jobID . "' and resCity='" . $mun . "' and shss='" . $spec . "' and dq=1 and  total_points>='50' ORDER BY total_points DESC");
        return $query->result();
    }

    public function rqa_mun_spec($jobID, $mun, $spec)
    {
        $query = $this->db->query("SELECT * FROM hris_applications a join hris_applications_rating r on a.appID=r.appID join hris_applicant ha on ha.record_no=r.record_no where jobID='" . $jobID . "' and resCity='" . $mun . "' and specialization='" . $spec . "' and dq=1 and  total_points>='50' ORDER BY total_points DESC");
        return $query->result();
    }

    public function rqa_track($jobID, $spe)
    {
        $query = $this->db->query("SELECT * FROM hris_applications a join hris_applications_rating r on a.appID=r.appID join hris_applicant ha on ha.record_no=r.record_no where jobID='" . $jobID . "' and track='" . $spe . "' and dq=1 and  total_points>='50' ORDER BY total_points DESC");
        return $query->result();
    }

    function schools($type)
    {
        $this->db->where('schoolType', $type);
        $this->db->order_by('schoolName');
        $result = $this->db->get('schools');
        return $result->result();
    }

    function districts()
    {
        $this->db->order_by('discription');
        $result = $this->db->get('district');
        return $result->result();
    }


public function is_user_endorser($username)
{
    $this->db->select('hris_leave.*');
    $this->db->from('epmloyee_superior');
    $this->db->join('hris_leave', 'epmloyee_superior.IDNumber = hris_leave.IDNumber');
    $this->db->where('epmloyee_superior.endorser', $username);
    $this->db->where_in('hris_leave.leaveStatus', ['Evaluated']);
    
    $query = $this->db->get();
    return $query->num_rows() > 0;
}


public function is_user_approver($username)
{
    $this->db->select('hris_leave.*');
    $this->db->from('epmloyee_superior');
    $this->db->join('hris_leave', 'epmloyee_superior.IDNumber = hris_leave.IDNumber');
    $this->db->where('epmloyee_superior.approver', $username);
    $this->db->where_in('hris_leave.leaveStatus', ['Recommended']);
    
    $query = $this->db->get();
    return $query->num_rows() > 0;
}



// public function is_user_approver($username)
// {
//     $this->db->where('approver', $username);
//     $query = $this->db->get('epmloyee_superior');
//     return $query->num_rows() > 0;
// }



public function get_endorsed_leaves($endorserUsername)
{
    // Get the IDNumbers this user endorses
    $endorsedIDsQuery = $this->db->select('IDNumber')
                                 ->from('epmloyee_superior')
                                 ->where('endorser', $endorserUsername)
                                 ->get();

    $endorsedIDs = array_column($endorsedIDsQuery->result_array(), 'IDNumber');

    if (empty($endorsedIDs)) {
        return [];
    }

    $this->db->select('hris_staff.IDNumber AS StaffID, hris_staff.FirstName, hris_staff.MiddleName, hris_staff.LastName, 
                       hris_leave.appDate, hris_leave.leaveType, hris_leave.daysApplied, hris_leave.dateFrom, 
                       hris_leave.dateTo, hris_leave.leaveStatus, hris_leave.leaveAttachment, hris_leave.leaveID');
    $this->db->from('hris_leave');
    $this->db->join('hris_staff', 'hris_staff.IDNumber = hris_leave.IDNumber');
    $this->db->where('hris_leave.leaveStatus', 'Evaluated');
    $this->db->where_in('hris_leave.IDNumber', $endorsedIDs);

    return $this->db->get()->result();
}





public function get_approved_leaves($approveUsername)
{
    // Get the IDNumbers this user endorses
    $approvedIDsQuery = $this->db->select('IDNumber')
                                 ->from('epmloyee_superior')
                                 ->where('approver', $approveUsername)
                                 ->get();

    $approveID = array_column($approvedIDsQuery->result_array(), 'IDNumber');

    if (empty($approveID)) {
        return [];
    }

    $this->db->select('hris_staff.IDNumber AS StaffID, hris_staff.FirstName, hris_staff.MiddleName, hris_staff.LastName, 
                       hris_leave.appDate, hris_leave.leaveType, hris_leave.daysApplied, hris_leave.dateFrom, 
                       hris_leave.dateTo, hris_leave.leaveStatus, hris_leave.leaveAttachment, hris_leave.leaveID');
    $this->db->from('hris_leave');
    $this->db->join('hris_staff', 'hris_staff.IDNumber = hris_leave.IDNumber');
    $this->db->where('hris_leave.leaveStatus', 'Recommended');
    $this->db->where_in('hris_leave.IDNumber', $approveID);

    return $this->db->get()->result();
}

public function get_all_applicants_indexed()
{
    $result = $this->db->get('hris_applicant')->result();
    $indexed = [];
    foreach ($result as $row) {
        $indexed[$row->record_no] = $row;
    }
    return $indexed;
}

public function get_all_staff_indexed()
{
    $result = $this->db->get('hris_staff')->result();
    $indexed = [];
    foreach ($result as $row) {
        $indexed[$row->IDNumber] = $row;
    }
    return $indexed;
}

public function all_fields_positive($id)
    {
        $this->db->from('sbm');
        $this->db->where('id', $id);

        for ($i = 1; $i <= 42; $i++) {
            $this->db->where("q{$i} >", 0);
        }

        $query = $this->db->get();
        return $query->num_rows() > 0; // true if all q1..q42 > 0
    }
    

public function dq_list_promotion($jobID)
    {
        $this->db->select("
        a.*,
        r.*,
        COALESCE(app.record_no, staff.IDNumber) AS code,
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
    $this->db->join('hris_app_dq r', 'a.appID = r.appID', 'left');
    $this->db->where('a.jobID', $jobID);
    $this->db->where('dq', 2);
    //$this->db->where('total_points >=', 50);
    $this->db->order_by('FirstName', 'DESC');

    $query = $this->db->get();
    return $query->result();
    }

    public function sbm_cecklist_lock_unloc($stat){
	$data = array(
		'stat' => $stat
	);

	$this->db->where('id', $this->uri->segment(3));
	return $this->db->update('sbm', $data);
}


    public function insert_ier()
    {
        $data = array(
            'disability' => $this->input->post('disability'), 
            'etchnic' => $this->input->post('etchnic'), 
            'ttitle' => $this->input->post('ttitle'), 
            'thours' => $this->input->post('thours'), 
            'edetails' => $this->input->post('edetails'), 
            'eyears' => $this->input->post('eyears'), 
            'eeligibility' => $this->input->post('eeligibility'), 
            'jobID' => $this->input->post('jobID'), 
            'code' => $this->input->post('code'),
            'religion' => $this->input->post('religion'),
            'MaritalStatus' => $this->input->post('MaritalStatus'),
        );

        return $this->db->insert('ier_info', $data);
    }

    public function update_ier()
    {
        $data = array(
            'disability' => $this->input->post('disability'), 
            'etchnic' => $this->input->post('etchnic'), 
            'ttitle' => $this->input->post('ttitle'), 
            'thours' => $this->input->post('thours'), 
            'edetails' => $this->input->post('edetails'), 
            'eyears' => $this->input->post('eyears'), 
            'eeligibility' => $this->input->post('eeligibility'), 
            'jobID' => $this->input->post('jobID'), 
            'code' => $this->input->post('code'),
            'religion' => $this->input->post('religion'),
            'MaritalStatus' => $this->input->post('MaritalStatus'),
        );

        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('ier_info', $data);
    }

    public function update_ier_person($table)
    {
        if($table === 'hris_staff'){
            $col="IDNumber";
        }else{
            $col='record_no';
        }

        $data = array(
            'disability' => $this->input->post('disability'), 
            'ethnicity' => $this->input->post('etchnic'), 
            'bd' => $this->input->post('bd'),
            'religion' => $this->input->post('religion'),
            'MaritalStatus' => $this->input->post('MaritalStatus'),
        );

        if ($table === 'hris_staff') {
            $data['empEmail']  = $this->input->post('empEmail');
            $data['empMobile'] = $this->input->post('empMobile');
        }

    
        $this->db->where($col, $this->input->post('code'));

        return $this->db->update($table, $data);
    }


    public function list_of_applicant()
    {
        $this->db->select("
        a.*,
        j.jobTitle,j.job_type,
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
    $this->db->join('hris_jobvacancy j', 'j.jobID = a.jobID', 'left');
    $this->db->where('j.jvStatus', 'Open');
    $this->db->where('a.pre_school', $this->session->username);
    $this->db->order_by('app.LastName', 'DESC');

    $query = $this->db->get();
    return $query->result();
    }

    function delete_the_toxic($table,$id, $segment, $folder, $attach)
    {
        $this->db->where($id, $segment);
        unlink($folder . "/" . $attach);
        $this->db->delete($table);
    }

    public function get_applicant_staff_validated_school_account($jvStatus,$appStatus)
    {
        $this->db->select("
        a.*,
        j.jvStatus,j.jobID,j.job_type,j.jobTitle,
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
    $this->db->join('hris_jobvacancy j', 'a.jobID = j.jobID', 'left');
    $this->db->where('j.jvStatus', 'Open');
    $this->db->where('a.appStatus', $appStatus);
    $this->db->where('a.pre_school', $this->session->c_id);
    $this->db->order_by('j.jvStatus', 'asc');

    $query = $this->db->get();
    return $query->result();
    }

    public function validators_remarks(){
        $data = array(
            'validator_remarks' => $this->input->post('validator_remarks')
        );

        $this->db->where('appID', $this->input->post('app_id'));
        return $this->db->update('hris_applications', $data);
    }

    




}
