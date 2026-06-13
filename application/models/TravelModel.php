<?php
class TravelModel extends CI_Model
{

    private $table = 'travel_requests';

    public function get_all()
    {
        $user_id = $this->session->userdata('username');
        $this->db->where('IDNumber', $user_id);
        $this->db->order_by('id', 'DESC');
        return $this->db->get($this->table)->result();
    }

    function formatOfficialDateRange($from, $to)
{
    $fromTime = strtotime($from);
    $toTime   = strtotime($to);

    if (!$fromTime || !$toTime) {
        return '';
    }

    $fromMonth = date('F', $fromTime);
    $toMonth   = date('F', $toTime);

    $fromDay = date('j', $fromTime);
    $toDay   = date('j', $toTime);

    $fromYear = date('Y', $fromTime);
    $toYear   = date('Y', $toTime);

    // Same month and same year
    if ($fromMonth == $toMonth && $fromYear == $toYear) {
        if ($fromDay == $toDay) {
            return date('F j, Y', $fromTime);
        }
        return $fromMonth . ' ' . $fromDay . '–' . $toDay . ', ' . $fromYear;
    }

    // Different month but same year
    if ($fromYear == $toYear) {
        return date('F j', $fromTime) . ' – ' . date('F j, Y', $toTime);
    }

    // Different year
    return date('F j, Y', $fromTime) . ' – ' . date('F j, Y', $toTime);
}

    public function get_all_pending()
    {
        $this->db->select("travel_requests.*, 
        CONCAT(hris_staff.LastName, ', ', hris_staff.FirstName, ' ', hris_staff.MiddleName) AS staff_name");
        $this->db->from($this->table);
        $this->db->join('hris_staff', 'hris_staff.IDNumber = travel_requests.IDNumber', 'left');
        $this->db->where('travel_requests.status', 'Pending');
        $this->db->order_by('travel_requests.id', 'ASC');
        return $this->db->get()->result();
    }


    public function get_all_processed()
    {
        $this->db->select("travel_requests.*, 
        CONCAT(hris_staff.LastName, ', ', hris_staff.FirstName, ' ', hris_staff.MiddleName) AS staff_name");
        $this->db->from($this->table);
        $this->db->join('hris_staff', 'hris_staff.IDNumber = travel_requests.IDNumber', 'left');
        $this->db->where('travel_requests.status !=', 'Pending');
        $this->db->order_by('travel_requests.id', 'ASC');
        return $this->db->get()->result();
    }


    public function get_by_id($id)
    {
        return $this->db->get_where($this->table, ['id' => $id])->row();
    }

    public function insert($data)
    {
        // Get the logged-in user's ID (assuming you have a session with a user ID)
        $user_id = $this->session->userdata('username'); // Change 'user_id' to the actual session key you use for the user's ID

        // Set the IDNumber to the logged-in user's ID if not provided
        if (empty($data['IDNumber'])) {
            $data['IDNumber'] = $user_id;
        }

        $data['inclusive_date'] = $this->formatOfficialDateRange($data['date_start'], $data['date_end']);

        $staff = $this->Common->one_cond_row_select('hris_staff','IDNumber,schoolID,d_id,emp_type','IDNumber',$this->session->username); 
        if($staff->emp_type == 4 || $staff->emp_type == 3){
            $data['status'] = 'Endorsed';
        }else{
            $data['status'] = 'Pending';
        }
        $data['d_id'] = $staff->d_id;
        $data['schoolID'] = $staff->schoolID;
        $data['emp_type'] = $staff->emp_type;

        $raID = $this->Common->three_cond_row('travel_sign_settings','position',1,'user_id',$this->session->username,'ttype',$data['ttype']);
        $aID = $this->Common->three_cond_row('travel_sign_settings','position',2,'user_id',$this->session->username,'ttype',$data['ttype']);

        if($raID){
            $data['raID'] = $raID->staff_id;
        }else{
          $data['raID'] = 0;  
        }
        $data['aID'] = $aID->staff_id;
        $data['fy'] = date('Y');

        if($staff->emp_type == 1){
            if($data['ttype'] == 0){
                $data['ivy'] = 1;
            }else{
                $data['ivy'] = 0;
            }
            
        }elseif($staff->emp_type == 4){
            $data['ivy'] = 1;
        }else{
            $data['ivy'] = 0;
        }

        // Insert the data into the table
        return $this->db->insert($this->table, $data);
    }


    public function update($id, $data)
    {
        return $this->db->where('id', $id)->update($this->table, $data);
    }

    public function updateRequest($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('travel_requests', $data);
    }

    public function delete($id)
    {
        return $this->db->where('id', $id)->delete($this->table);
    }

    public function travel_sign_insert(){

      $data = array(
          'staff_id' => $this->input->post('staff_id'), 
          'position' => $this->input->post('position'),
          'user_id' => $this->session->c_id,
          'ttype' => $this->input->post('ttype'),
          'emp_type' => $this->input->post('emp_type')
      );

      return $this->db->insert('travel_sign_settings', $data);
    }

    public function travel_track_insert($l_id,$stat){
        date_default_timezone_set('Asia/Manila');
        $dt = date('Y-m-d H:i:s');

      $data = array(
          'dt' => $dt,
          'stat' => $stat, 
          'travel_req_id' => $l_id,
      );

      return $this->db->insert('travel_tracker', $data);
    }

    public function travel_track_insert_reject($l_id,$stat){
        date_default_timezone_set('Asia/Manila');
        $dt = date('Y-m-d H:i:s');

      $data = array(
          'dt' => $dt,
          'stat' => $this->input->post('stat'), 
          'travel_req_id' => $this->input->post('travel_req_id'),
          'remarks' => $this->input->post('remarks'),
      );

      return $this->db->insert('travel_tracker', $data);
    }

    public function travel_update_stat()
    {
        $stat = $this->uri->segment(4);
        $id = $this->uri->segment(3);

        $data = array(
          'status' => $stat
        );

        $this->db->where('id', $id);
        return $this->db->update('travel_requests', $data);
    }

    public function travel_update_stat_reject()
    {
        $stat = $this->input->post('stat');
        $id = $this->input->post('travel_req_id');

        $data = array(
          'status' => $stat
        );

        $this->db->where('id', $id);
        return $this->db->update('travel_requests', $data);
    }

    public function emp_type_update()
    {

        $data = array(
          'emp_type' => $this->input->post('emp_type'),
          'd_id' => $this->input->post('d_id'),
          'schoolID' => $this->input->post('schoolID')
        );

        $this->db->where('IDNumber', $this->input->post('user_id'));
        return $this->db->update('hris_staff', $data);
    }

    public function get_travel_summary()
        {
            $this->db->select('
                tr.IDNumber,
                s.LastName,
                s.FirstName,
                s.empPosition,
                COUNT(*) AS request_count
            ');
            $this->db->from('travel_requests AS tr');
            $this->db->join('hris_staff AS s', 's.IDNumber = tr.IDNumber', 'left');
            $this->db->where('fy', $this->session->cur_fy);
            $this->db->group_by(['tr.IDNumber', 's.LastName', 's.FirstName']);
            $this->db->order_by('request_count', 'DESC');

            $query = $this->db->get();
            return $query->result();
    }

    public function get_travel_summary_chief($val)
        {
            $this->db->select('
                tr.IDNumber,
                s.LastName,
                s.FirstName,
                s.empPosition,
                COUNT(*) AS request_count
            ');
            $this->db->from('travel_requests AS tr');
            $this->db->join('hris_staff AS s', 's.IDNumber = tr.IDNumber', 'left');
            $this->db->where('fy', $this->session->cur_fy);
            $this->db->where('tr.schoolID', $val);
            $this->db->group_by(['tr.IDNumber', 's.LastName', 's.FirstName']);
            $this->db->order_by('request_count', 'DESC');

            $query = $this->db->get();
            return $query->result();
    }

    public function four_cond($table, $col, $val, $col2, $val2, $col3, $val3, $col4, $val4)
{
    $this->db->where($col, $val);
    $this->db->where($col2, $val2);
    $this->db->where($col3, $val3);

    // check if value is array
    if (is_array($val4)) {
        $this->db->where_in($col4, $val4);
    } else {
        $this->db->where($col4, $val4);
    }

    $query = $this->db->get($table);

    return $query->result();
}

public function add_br_every_words($text, $limit = 10)
{
    $words = preg_split('/\s+/', trim($text));
    $chunks = array_chunk($words, $limit);
    $lines = array_map(function($chunk){
        return implode(' ', $chunk);
    }, $chunks);

    return implode('<br>', $lines);
}

// public function get_travel_by_month($month, $year)
// {
//     $this->db->select('tr.*, s.IDNumber, s.FirstName, s.LastName, s.empPosition');
//     $this->db->from('travel_requests tr');
//     $this->db->join('hris_staff s', 's.IDNumber = tr.IDNumber', 'left');

//     $this->db->where('MONTH(tr.date_created) =', (int)$month, false);
//     $this->db->where('YEAR(tr.date_created) =', (int)$year, false);

//     return $this->db->get()->result();
// }

public function get_travel_by_month($month, $year)
{
    $month_start = date('Y-m-01', strtotime($year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-01'));
    $month_end   = date('Y-m-t', strtotime($month_start));

    $this->db->select('tr.*, s.IDNumber, s.FirstName, s.LastName, s.empPosition');
    $this->db->from('travel_requests tr');
    $this->db->join('hris_staff s', 's.IDNumber = tr.IDNumber', 'left');

    // overlap condition
    $this->db->where('tr.date_start <=', $month_end);
    $this->db->where('tr.date_end >=', $month_start);
    $this->db->where('tr.status','Approved');

    return $this->db->get()->result();
}


public function get_travel_by_month_chief($month, $year, $school_id)
{
    $this->db->select('tr.*, s.IDNumber, s.FirstName, s.LastName, s.empPosition');
    $this->db->from('travel_requests tr');
    $this->db->join('hris_staff s', 's.IDNumber = tr.IDNumber', 'left');

    $this->db->where('tr.schoolID',$school_id);

    $this->db->where('MONTH(tr.date_created) =', (int)$month, false);
    $this->db->where('YEAR(tr.date_created) =', (int)$year, false);

    return $this->db->get()->result();
}

public function get_travel_requests_asds($ttype)
{
    return $this->db
        ->from('travel_requests')
        ->where('status','Endorsed')
        ->where('ttype',$ttype)
        ->where_in('emp_type', [2, 3])
        ->where_not_in('schoolID', [1, 2])
        ->get()
        ->result();
}


    

   

}
