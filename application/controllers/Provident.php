<?php

class Provident extends CI_Controller
{

	function __construct()
	{
		parent::__construct();
	}

    public function provident()
    {

        $page = "provident_list";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

		$data['title'] = "Provident List";
        $data['staff'] = $this->Common->one_cond_select_ob('hris_staff','IDNumber,currentStatus,FirstName,MiddleName,LastName,NameExtn','currentStatus','Active','LastName','ASC');
        $data['data'] = $this->Common->two_join_one_cond_not_gb('provident', 'hris_staff', 'a.*,b.IDNumber,b.FirstName,b.MiddleName,b.LastName', 'a.employee_no = b.IDNumber', 'effect_to_year', $this->session->cur_fy, 'b.LastName', 'ASC');

        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function provident_update()
    {

        $this->form_validation->set_error_delimiters('<div class="alert alert-danger alert-dismissible fade show" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        ', '</div>');
        $this->form_validation->set_rules('employee_no', 'Employee No', 'required');
        //$this->form_validation->set_rules('termPeriodto', 'Term Period To', 'required');

        if ($this->form_validation->run() == FALSE) {

            $page = "provident_update";

            if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
                show_404();
            }

            $data['title'] = 'Update Provident';

            $data['staff'] = $this->Common->one_cond_select_ob('hris_staff','IDNumber,currentStatus,FirstName,MiddleName,LastName,NameExtn','currentStatus','Active','LastName','ASC');
            $data['data'] = $this->Common->one_cond_row('provident','id',$this->uri->segment(3));

            $this->load->view('templates/head');
            $this->load->view('templates/header');
            $this->load->view('pages/' . $page, $data);
            $this->load->view('templates/footer');
        } else {
                $this->Provident_model->provident_update();
                $this->session->set_flashdata('success', 'Successfully Updated.');
                redirect(base_url() . 'provident');

        }

    }

    public function provident_insert()
    {
        $this->Provident_model->provident_insert();
		$this->session->set_flashdata('success', 'Successfully Saved');
		redirect($_SERVER['HTTP_REFERER']);
    }

    public function provident_imp_insert()
    {
        $this->Provident_model->provident_imp_insert();
		$this->session->set_flashdata('success', 'Successfully Saved');
		redirect($_SERVER['HTTP_REFERER']);
    }

    public function provident_imp_payment_insert()
    {
        $month = $this->input->post('month');
        $fy    = $this->input->post('fy');
        $id    = $this->input->post('IDNumber');

        $pay = $this->Common->three_cond_row('provident_implementing_payment','month',$month,'fy',$fy,'IDNumber',$id);

        if ($pay) {
            $this->session->set_flashdata('danger', 'Payment for this Month and FY already exists.');
            redirect($_SERVER['HTTP_REFERER']);
            exit;
        }

        $this->Provident_model->provident_imp_payment();

		$this->session->set_flashdata('success', 'Successfully Saved');
		redirect($_SERVER['HTTP_REFERER']);
    }

    public function provident_delete()
    {
        $this->Common->delete('provident', 'id','3');
        $this->session->set_flashdata('danger', 'Successfully Deleted');
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function provident_generate_report()
    {

        $page = "provident_report";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

		$data['title'] = "Provident List";
        $data['data'] = $this->Common->two_join_two_cond('provident', 'hris_staff', 'a.*,b.IDNumber,b.FirstName,b.MiddleName,b.LastName', 'a.employee_no = b.IDNumber', 'effect_from_year', $this->input->post('fy'), 'effect_from_month', $this->input->post('month'), 'b.LastName', 'ASC');
        $data['del'] = $this->Common->two_join_two_cond('provident', 'hris_staff', 'a.*,b.IDNumber,b.FirstName,b.MiddleName,b.LastName', 'a.employee_no = b.IDNumber', 'effect_to_year', $this->input->post('fy'), 'effect_to_month', $this->input->post('month'), 'b.LastName', 'ASC');
        $data['count'] = $this->Common->three_cond_count_row('provident','effect_from_year', $this->input->post('fy'), 'effect_from_month', $this->input->post('month'),'stat',0);

        $data['fy'] = $this->input->post('fy');
        $data['month'] = $this->input->post('month');

        $this->load->view('pages/' . $page, $data);
    }

    public function provident_accept()
    {
        $this->Provident_model->provident_accept();
		redirect($_SERVER['HTTP_REFERER']);
    }

    public function approve_all()
    {
    $ids = $this->input->post('ids');

    if (empty($ids)) {
        $this->session->set_flashdata('error', 'No records found to update.');
        redirect('Deductions');
    }

    $ok = $this->Provident_model->set_status_one_many($ids);

    $this->session->set_flashdata($ok ? 'success' : 'error',
        $ok ? 'All displayed records updated to status = 1.' : 'Update failed.'
    );
    $this->provident_generate_report();
}

    public function loan_list()
    {

        $page = "loan_list";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

		$data['title'] = "Provident Loan List";
        $data['data'] = $this->Common->one_cond_select_ob('payroll_empdeductions','dedID,IDNumber,fName,mName,lName,principalAmount,effectYearFrom,effectYearTo,dedAmount','dedCode','0007','lName','ASC');

        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function implementing_loans()
    {

        $page = "provident_implementing";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

		$data['title'] = "Provident Loan List";
        $data['staff'] = $this->Common->one_cond_select_ob('hris_staff','IDNumber,currentStatus,FirstName,MiddleName,LastName,NameExtn','currentStatus','Active','LastName','ASC');
        $data['school'] = $this->Common->no_cond('implementing_school');
        
        $data['data'] = $this->Common->two_join_one_cond_not_gb('provident_implementing', 'hris_staff', 'b.FirstName,b.MiddleName,b.LastName,b.IDNumber,a.*', 'a.employee_no = b.IDNumber', 'a.stat', 0, 'b.LastName', 'asc');

        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function paid_loan_list()
    {
        $page = "loan_list";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

		$data['title'] = "Provident Loan List";
        $data['data'] = $this->Common->two_cond_select_ob('payroll_empdeductions','dedID,IDNumber,fName,mName,lName,principalAmount,effectYearFrom,effectYearTo,dedAmount','loanStat','Paid','dedCode','0007','lName','ASC');

        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function loan_ledger()
    {

        $page = "loan_ledger";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

		$data['title'] = "Ledger";
        $data['staff'] = $this->Common->one_cond_row('hris_staff','IDNumber',$this->uri->segment(3));
        $data['data'] = $this->Common->one_cond_row('payroll_empdeductions','dedID',$this->uri->segment(4));
        $data['payment'] = $this->Common->five_cond_ob('payroll_empdeductions_monthly','IDNumber',$this->uri->segment(3),'dedCode',$data['data']->dedCode,'effectYearTo',$data['data']->effectYearTo,'effectYearFrom',$data['data']->effectYearFrom,'dedStatus','Deducted','dedID','ASC');
        $data['comaker'] = $this->Common->one_cond_select_ob('hris_staff','IDNumber,currentStatus,FirstName,MiddleName,LastName,NameExtn','currentStatus','Active','LastName','ASC');

        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function loan_ledger_full_term()
    {

        $page = "loan_ledger_full_term";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

		$data['title'] = "Ledger";
        $data['staff'] = $this->Common->one_cond_row('hris_staff','IDNumber',$this->uri->segment(3));
        $data['data'] = $this->Common->one_cond_row('payroll_empdeductions','dedID',$this->uri->segment(4));
        $data['payment'] = $this->Common->four_cond_ob('payroll_empdeductions_monthly','IDNumber',$this->uri->segment(3),'dedCode',$data['data']->dedCode,'effectYearTo',$data['data']->effectYearTo,'effectYearFrom',$data['data']->effectYearFrom,'dedID','ASC');
        //$data['payment'] = $this->Provident_model->getEmployeeDeductions($this->uri->segment(4));

        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function provident_ledger_insert()
    {
        $this->Provident_model->provident_insert_ledger();
		redirect($_SERVER['HTTP_REFERER']);
    }

    public function provident_ledger_update()
    {
        $this->Provident_model->provident_update_ledger();
		redirect($_SERVER['HTTP_REFERER']);
    }

    public function loan_ledger_implementing()
    {

        $page = "loan_ledger_implementing";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

		$data['title'] = "Ledger";
        $data['staff'] = $this->Common->one_cond_row('hris_staff','IDNumber',$this->uri->segment(3));
        $data['data'] = $this->Common->one_cond_row('provident_implementing','id',$this->uri->segment(4));
        $data['comaker'] = $this->Common->one_cond_select_ob('hris_staff','IDNumber,currentStatus,FirstName,MiddleName,LastName,NameExtn','currentStatus','Active','LastName','ASC');
        $data['payment'] = $this->Common->two_cond_order_by('provident_implementing_payment','IDNumber',$this->uri->segment(3),'loan_id',$data['data']->id,'id','ASC');
        $data['school'] = $this->Common->one_cond_row('implementing_school','school_id',$data['data']->school_id);


        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function loan_ledger_full_term_implementing()
    {

        $page = "loan_ledger_full_term_implementing";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

		$data['title'] = "Ledger";
        $data['staff'] = $this->Common->one_cond_row('hris_staff','IDNumber',$this->uri->segment(3));
        $data['data'] = $this->Common->one_cond_row('provident_implementing','id',$this->uri->segment(4));
        $data['payment'] = $this->Common->two_cond_order_by('provident_implementing_payment','IDNumber',$this->uri->segment(3),'loan_id',$data['data']->id,'id','ASC');
        $data['school'] = $this->Common->one_cond_row('implementing_school','school_id',$data['data']->school_id);


        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function implementing_payment()
    {

        $page = "provident_implementing_payment";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

		$data['title'] = "Provident Loan Payment";
        $data['staff'] = $this->Common->one_cond_select_ob('hris_staff','IDNumber,currentStatus,FirstName,MiddleName,LastName,NameExtn','currentStatus','Active','LastName','ASC');
        
        
        //$data['data'] = $this->Common->two_join_one_cond_not_gb('provident_implementing_payment', 'hris_staff', 'b.FirstName,b.MiddleName,b.LastName,b.IDNumber,a.*', 'a.IDNumber = b.IDNumber','loan_id',$this->uri->segment(2),'b.LastName', 'asc');
        $data['data'] = $this->Common->one_cond('provident_implementing_payment','loan_id',$this->uri->segment(2));
        $data['emp'] = $this->Common->one_cond_row_select('hris_staff','FirstName,LastName,LastName','IDNumber',$this->uri->segment(3));
        $data['loan'] = $this->Common->one_cond_row('provident_implementing','id',$this->uri->segment(2));
        $data['school'] = $this->Common->one_cond_row('implementing_school','school_id',$data['loan']->school_id);
        

        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function provident_implementing_delete()
    {
        $this->Common->delete('provident_implementing_payment', 'id','3');
        $this->session->set_flashdata('danger', 'Successfully Deleted');
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function implementing_school_payment()
    {

        // $page = "provident_implementing_school_payment";

        // if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
        //     show_404();
        // }

        $school_id  = $this->input->post('school_id');
        $month      = $this->input->post('month');
        $fy         = $this->input->post('fy');
        redirect(base_url() .'Provident/implementing_school_paymentv2/'.$month.'/'.$fy.'/'.$school_id);

		// $data['title'] = "Provident Loan Payment";
        // $data['staff'] = $this->Common->two_cond_select_ob('hris_staff','IDNumber,currentStatus,FirstName,MiddleName,LastName,NameExtn','currentStatus','Active','schoolID',$data['school_id'],'LastName','ASC');
        
        
        
        //$data['data'] = $this->Common->two_join_one_cond_not_gb('provident_implementing_payment', 'hris_staff', 'b.FirstName,b.MiddleName,b.LastName,b.IDNumber,a.*', 'a.IDNumber = b.IDNumber','loan_id',$this->uri->segment(2),'b.LastName', 'asc');
        // $data['data'] = $this->Common->three_cond('provident_implementing_payment','school_id', $data['school_id'],'month',$data['month'],'fy',$data['fy']);
        // $data['school'] = $this->Common->one_cond_row('implementing_school','school_id', $data['school_id']);
        

        // $this->load->view('templates/head');
        // $this->load->view('templates/header');
        // $this->load->view('pages/' . $page, $data);
        // $this->load->view('templates/footer');
    }

    public function provident_imp_school_payment_insert()
    {
        $month = $this->input->post('month');
        $fy    = $this->input->post('fy');
        $id    = $this->input->post('IDNumber');
        $school_id    = $this->input->post('school_id');


        $pay = $this->Common->three_cond_row('provident_implementing_payment','month',$month,'fy',$fy,'IDNumber',$id);

        if ($pay) {
            $this->session->set_flashdata('danger', 'Payment for this Month and FY already exists.');
            redirect($_SERVER['HTTP_REFERER']);
            exit;
        }

        $loan = $this->Common->two_cond_row('provident_implementing','employee_no',$id,'stat',0);

        if(empty($loan)) {
            $this->session->set_flashdata('danger', 'This individual does not have any active loans.');
            redirect($_SERVER['HTTP_REFERER']);
            exit;
        }

        $this->Provident_model->provident_imp_payment_school($loan->id);

		$this->session->set_flashdata('success', 'Successfully Saved');
		redirect(base_url() .'Provident/implementing_school_paymentv2/'.$month.'/'.$fy.'/'.$school_id);
    }

    public function implementing_order_of_payment($school_id, $amount, $month, $fy)
    {

        $this->session->set_userdata('op_school_id', $school_id);
        $this->session->set_userdata('op_amount', $amount);
        $this->session->set_userdata('op_month', $month);
        $this->session->set_userdata('op_fy', $fy);

        redirect('Provident/order_of_payment');

    }

    

    public function implementing_school_paymentv2()
    {

        $page = "provident_implementing_school_payment";
        

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $data['school_id']  = $this->uri->segment('5');
        $data['month']      = $this->uri->segment('3');
        $data['fy']         = $this->uri->segment('4');

        

		$data['title'] = "Provident Loan Payment";
        $data['staff'] = $this->Common->two_cond_select_ob('hris_staff','IDNumber,currentStatus,FirstName,MiddleName,LastName,NameExtn','currentStatus','Active','schoolID',$data['school_id'],'LastName','ASC');
        
        
        
        //$data['data'] = $this->Common->two_join_one_cond_not_gb('provident_implementing_payment', 'hris_staff', 'b.FirstName,b.MiddleName,b.LastName,b.IDNumber,a.*', 'a.IDNumber = b.IDNumber','loan_id',$this->uri->segment(2),'b.LastName', 'asc');
        //$data['data'] = $this->Common->three_cond('provident_implementing_payment','school_id', $data['school_id'],'month',$data['month'],'fy',$data['fy']);
        $data['data'] = $this->Common->two_join_four_cond('provident_implementing_payment','provident_implementing', 'a.*,b.id as pi_id,b.stat,', 'a.loan_id = b.id', 'a.school_id', $data['school_id'],'a.month',$data['month'],'a.fy',$data['fy'], 'b.stat', 0, 'a.id', 'asc');
        
        $data['school'] = $this->Common->one_cond_row('implementing_school','school_id', $data['school_id']);
        

        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function implementing_provident_delete()
    {
        $this->Common->delete('provident_implementing', 'id','3');
        $this->session->set_flashdata('danger', 'Successfully Deleted');
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function provident_school()
    {

        $page = "provident_implementing_list_school";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }
        $school_id = $this->session->username;

		$data['title'] = "Provident Loan List";
        $data['staff'] = $this->Common->one_cond_select_ob('hris_staff','IDNumber,currentStatus,FirstName,MiddleName,LastName,NameExtn','currentStatus','Active','LastName','ASC');
        $data['school'] = $this->Common->no_cond('implementing_school');
        
        $data['data'] = $this->Common->two_join_two_cond('provident_implementing', 'hris_staff', 'b.FirstName,b.MiddleName,b.LastName,b.IDNumber,a.*', 'a.employee_no = b.IDNumber', 'a.stat', 0,'school_id',$school_id, 'b.LastName', 'asc');

        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function provident_school_list()
    {

        $page = "provident_implementing_list_school";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }
        $school_id = $this->uri->segment(3);

		$data['title'] = "Provident Loan List";
        $data['staff'] = $this->Common->one_cond_select_ob('hris_staff','IDNumber,currentStatus,FirstName,MiddleName,LastName,NameExtn','currentStatus','Active','LastName','ASC');
        $data['school'] = $this->Common->no_cond('implementing_school');
        
        $data['data'] = $this->Common->two_join_two_cond('provident_implementing', 'hris_staff', 'b.FirstName,b.MiddleName,b.LastName,b.IDNumber,a.*', 'a.employee_no = b.IDNumber', 'a.stat', 0,'school_id',$school_id, 'b.LastName', 'asc');

        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    

    public function loan_paid()
    {
        $this->Provident_model->loan_paid_update();
		redirect($_SERVER['HTTP_REFERER']);
    }

    public function payment_confirm()
    {
        $this->Provident_model->pay_confirmed();
		redirect($_SERVER['HTTP_REFERER']);
    }

    public function provident_monthly()
    {

        $page = "provident_monthly";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

		$data['title'] = "Provident List";
        $data['school'] = $this->Common->no_cond('implementing_school');
        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function collection_interest()
    {

        $page = "provident_collection";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

		$data['title'] = "Provident List";
        $data['school'] = $this->Common->no_cond('implementing_school');
        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function collection_deposits()
    {

        $page = "provident_interest_deposits";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

		$data['title'] = "Provident List";
        $data['school'] = $this->Common->no_cond('provident_add_info');
        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function change_fy_and_m() {
        $new_fy = $this->input->post('fy');
        $new_m = $this->input->post('month');
        if (!empty($new_fy)) {
            $this->session->set_userdata('cur_fy', $new_fy);
            $this->session->set_userdata('cur_month', $new_m);
        }
        redirect($_SERVER['HTTP_REFERER']); 
    }

    public function order_of_payment()
    {

    
        if (!$this->session->op_school_id) {
            show_404();
        }
        $page = "provident_order_of_payment";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $data['payment'] = $this->Common->one_cond_row('provident_implementing_payment','id',$this->session->op_school_id);
        $data['school'] = $this->Common->one_cond_row_select('schools','schoolID,schoolName,city,province','schoolID',$this->session->op_school_id);

		$data['title'] = "Provident List";
        $this->load->view('pages/' . $page, $data);
    }

    public function provident_info_add ()
    {
        $this->Provident_model->provident_add_info();
        $this->session->set_flashdata('success', 'Successfully Saved');
		redirect($_SERVER['HTTP_REFERER']);
    }

    public function provident_info_update ()
    {
        $this->Provident_model->provident_update_info();
        $this->session->set_flashdata('success', 'Successfully Saved');
		redirect($_SERVER['HTTP_REFERER']);
    }

    public function provident_comaker()
    {
        $this->Provident_model->provident_comaker_add();
        $this->session->set_flashdata('success', 'Successfully Saved');
		redirect($_SERVER['HTTP_REFERER']);
    }

    public function provident_comaker_update()
    {
        $this->Provident_model->provident_comaker_update();
        $this->session->set_flashdata('success', 'Successfully Saved');
		redirect($_SERVER['HTTP_REFERER']);
    }


    public function provident_remarks()
    {
        $this->Provident_model->provident_remarks_insert();
        $this->session->set_flashdata('success', 'Successfully Saved');
		redirect($_SERVER['HTTP_REFERER']);
    }

    public function provident_remarks_edit()
    {
        $this->Provident_model->provident_remarks_update();
        $this->session->set_flashdata('success', 'Successfully Saved');
		redirect($_SERVER['HTTP_REFERER']);
    }

    public function order_of_payment_single()
    {

        $page = "provident_order_of_payment_single";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        //$data['payment'] = $this->Common->one_cond_row('provident_implementing_payment','id',$this->session->op_school_id);
        $data['staff'] = $this->Common->one_cond_row('hris_staff','IDNumber',$this->uri->segment(3));
        $data['school'] = $this->Common->one_cond_row_select('schools','schoolID,schoolName,city,province','schoolID',$this->uri->segment(7));

		$data['title'] = "Provident List";
        $this->load->view('pages/' . $page, $data);
    }

    public function order_of_payment_single_psu()
    {

        $page = "provident_order_of_payment_single_psu";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        //$data['payment'] = $this->Common->one_cond_row('provident_implementing_payment','id',$this->session->op_school_id);
        $data['staff'] = $this->Common->one_cond_row('hris_staff','IDNumber',$this->uri->segment(3));
        $data['school'] = $this->Common->one_cond_row_select('schools','schoolID,schoolName,city,province','schoolID',$this->uri->segment(7));

		$data['title'] = "Provident List";
        $this->load->view('pages/' . $page, $data);
    }

    

    



   

    



}
