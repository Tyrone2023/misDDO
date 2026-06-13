<?php

class Provident_model extends CI_Model
{

    public function __construct()
    {
        $this->load->database();
    }

    public function provident_insert()
    {
        date_default_timezone_set('Asia/Manila');
        //$time = date('H:i:s A');
        $date = date("Y-m-d");

        $data = array(
            'employee_no' => $this->input->post('employee_no'), 
            'deduction_code' => $this->input->post('deduction_code'), 
            'effect_from_month' => $this->input->post('effect_from_month'), 
            'effect_from_year' => $this->input->post('effect_from_year'), 
            'effect_to_month' => $this->input->post('effect_to_month'), 
            'effect_to_year' => $this->input->post('effect_to_year'), 
            'deduction' => $this->input->post('deduction'), 
            'principal' => $this->input->post('principal'), 
            'approved_date' => date('Y-m-d', strtotime($this->input->post('approved_date'))), 
            'remarks' => $this->input->post('remarks'), 
            'verified' => date('Y-m-d', strtotime($this->input->post('verified'))),
            'stat' => 0,
            'reloan' => $this->input->post('reloan')
        );
        return $this->db->insert('provident', $data);
    }

    public function provident_imp_insert()
    {
        date_default_timezone_set('Asia/Manila');
        //$time = date('H:i:s A');
        $date = date("Y-m-d");

        $data = array(
            'employee_no' => $this->input->post('employee_no'), 
            'deduction_code' => $this->input->post('deduction_code'), 
            'effect_from_month' => $this->input->post('effect_from_month'), 
            'effect_from_year' => $this->input->post('effect_from_year'), 
            'effect_to_month' => $this->input->post('effect_to_month'), 
            'effect_to_year' => $this->input->post('effect_to_year'), 
            'deduction' => $this->input->post('deduction'), 
            'principal' => $this->input->post('principal'), 
            'approved_date' => date('Y-m-d', strtotime($this->input->post('approved_date'))), 
            'remarks' => $this->input->post('remarks'), 
            'verified' => date('Y-m-d', strtotime($this->input->post('verified'))),
            'stat' => 0,
            'school_id' => $this->input->post('school_id'), 
        );
        return $this->db->insert('provident_implementing', $data);
    }

    public function provident_update()
    {
        date_default_timezone_set('Asia/Manila');

        $data = array(
            'employee_no' => $this->input->post('employee_no'), 
            'deduction_code' => $this->input->post('deduction_code'), 
            'effect_from_month' => $this->input->post('effect_from_month'), 
            'effect_from_year' => $this->input->post('effect_from_year'), 
            'effect_to_month' => $this->input->post('effect_to_month'), 
            'effect_to_year' => $this->input->post('effect_to_year'), 
            'deduction' => $this->input->post('deduction'), 
            'principal' => $this->input->post('principal'), 
            'approved_date' => date('Y-m-d', strtotime($this->input->post('approved_date'))), 
            'remarks' => $this->input->post('remarks'), 
            'verified' => date('Y-m-d', strtotime($this->input->post('verified'))),
            //'school_id' => $this->input->post('school_id'), 
            'reloan' => $this->input->post('reloan'), 
        );
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('provident', $data);
    }

    public function set_status_one_many($ids)
    {
        $this->db->where_in('id', $ids);      // <-- change 'id' to your primary key
        $this->db->where('stat', 0);        // only those currently 0
        return $this->db->update('provident', ['stat' => 1]);
    }

    public function provident_insert_ledger()
    {
        date_default_timezone_set('Asia/Manila');
        //$time = date('H:i:s A');
        $date = date("Y-m-d");

        $data = array(
            'arrears' => $this->input->post('arrears'), 
            'reverse_int' => $this->input->post('reverse_int'), 
            'paydate' => $date,
            'dedID' => $this->input->post('dedID'),
            'type' => $this->input->post('type')
        );
        return $this->db->insert('provident_ledger', $data);
    }

    public function provident_update_ledger()
    {
        date_default_timezone_set('Asia/Manila');
        //$time = date('H:i:s A');
        $date = date("Y-m-d");

        $data = array(
            'arrears' => $this->input->post('arrears'), 
            'reverse_int' => $this->input->post('reverse_int')
        );

        $this->db->where('id', $this->input->post('id')); 
        return $this->db->update('provident_ledger', $data);
    }

    public function provident_imp_payment()
    {
        date_default_timezone_set('Asia/Manila');
        $time = date('H:i:s A');
        $date = date("Y-m-d");

        $data = array(
            'IDNumber' => $this->input->post('IDNumber'), 
            'amount' => $this->input->post('amount'), 
            'remarks' => $this->input->post('remarks'), 
            'loan_id' => $this->input->post('loan_id'), 
            'month' => $this->input->post('month'), 
            'fy' => $this->input->post('fy'), 
            'date_encoded' => $date,
            'time_encoded' => $time,
            'school_id' => $this->input->post('school_id'),
            'stat' => 0
        );
        return $this->db->insert('provident_implementing_payment', $data);
    }

    public function provident_imp_payment_school($loan_id)
    {
        date_default_timezone_set('Asia/Manila');
        $time = date('H:i:s A');
        $date = date("Y-m-d");

        $data = array(
            'IDNumber' => $this->input->post('IDNumber'), 
            'amount' => $this->input->post('amount'), 
            'remarks' => $this->input->post('remarks'), 
            'loan_id' => $loan_id, 
            'month' => $this->input->post('month'), 
            'fy' => $this->input->post('fy'), 
            'date_encoded' => $date,
            'time_encoded' => $time,
            'school_id' => $this->input->post('school_id'),
            'stat' => 0
        );
        return $this->db->insert('provident_implementing_payment', $data);
    }

    public function loan_paid_update()
    {

        $data = array(
            'stat' => 1, 
        );

        $this->db->where('id', $this->uri->segment(3)); 
        return $this->db->update('provident_implementing', $data);
    }

    public function pay_confirmed()
    {

        $data = array(
            'stat' => 1, 
        );

        $this->db->where('id', $this->uri->segment(3)); 
        return $this->db->update('provident_implementing_payment', $data);
    }


    public function count_payments_by_loan($school_id)
    {
        $this->db->where('school_id', $school_id);
        $this->db->group_by('loan_id');
        $this->db->order_by('loan_id', 'ASC');
        $result = $this->db->get('provident_implementing_payment');
        return $result;
    }

    public function provident_add_info()
    {

        $data = array(
            'month' => $this->input->post('month'), 
            'fy' => $this->input->post('fy'), 
            'school_id' => $this->input->post('school_id'), 
            'serial' => $this->input->post('serial'), 
            'cdate' => $this->input->post('cdate')
        );

        return $this->db->insert('provident_add_info', $data);
    }

    public function provident_update_info()
    {

        $data = array( 
            'serial' => $this->input->post('serial'), 
            'cdate' => $this->input->post('cdate')
        );

        $this->db->where('id', $this->input->post('id')); 
        return $this->db->update('provident_add_info', $data);
    }

    public function provident_comaker_add()
    {

        $data = array(
            'IDNumber' => $this->input->post('IDNumber'), 
            'deduction_id' => $this->input->post('deduction_id'), 
            'comaker_id' => $this->input->post('comaker_id')
        );

        return $this->db->insert('provident_comaker', $data);
    }

   public function comaker($did)
        {
            return $this->db
                ->select('
                    pc.*,
                    hs.FirstName,
                    hs.LastName,
                    hs.MiddleName,
                    hs.schoolID,
                    s.stationCode,
                    s.schoolName,
                    s.district
                ')
                ->from('provident_comaker pc')
                ->join('hris_staff hs', 'hs.IDNumber = pc.comaker_id', 'left')
                ->join('schools s', 's.schoolID = hs.schoolID', 'left')
                ->where('pc.deduction_id', $did)
                ->get()
                ->row();
    }

    public function provident_comaker_update()
    {

        $data = array(
            'comaker_id' => $this->input->post('comaker_id')
        );

        $this->db->where('id', $this->input->post('id')); 
        return $this->db->update('provident_comaker', $data);
    }

    public function provident_remarks_insert()
    {

        $data = array(
            'IDNumber' => $this->input->post('IDNumber'), 
            'deduction_id' => $this->input->post('deduction_id'), 
            'reloan_date' => $this->input->post('reloan_date'), 
            'loan_amount' => $this->input->post('loan_amount'),
            'less_loan_bal' => $this->input->post('less_loan_bal'),
            'net_amount' => $this->input->post('net_amount')
        );

        return $this->db->insert('provident_loan_remarks', $data);
    }

    public function provident_remarks_update()
    {

        $data = array( 
            'reloan_date' => $this->input->post('reloan_date'), 
            'loan_amount' => $this->input->post('loan_amount'),
            'less_loan_bal' => $this->input->post('less_loan_bal'),
            'net_amount' => $this->input->post('net_amount')
        );

        $this->db->where('id', $this->input->post('id')); 
        return $this->db->Update('provident_loan_remarks', $data);
    }










   
}