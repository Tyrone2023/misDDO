<?php
class Travel extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('TravelModel');
        $this->load->helper(['form', 'url']);
        $this->load->library('form_validation');
    }

    public function index()
    {
        $data['requests'] = $this->TravelModel->get_all();
        $this->load->view('travel_request', $data);
    }

    public function viewPending()
    {
        $data['requests'] = $this->TravelModel->get_all_pending();
        $this->load->view('travel_request_pending', $data);
    }

    public function viewProcessedATT()
    {
        $data['requests'] = $this->TravelModel->get_all_processed();
        $this->load->view('travel_request_processed', $data);
    }

    // public function create()
    // {
    //     if ($this->input->post()) {
    //         $data = $this->input->post();
    //         $this->TravelModel->insert($data);
    //         $l_id = $this->db->insert_id();
    //         $this->TravelModel->travel_track_insert($l_id,'Pending');

    //         redirect('travel');
    //     } else {
    //         $this->load->view('travel_request_form');
    //     }
    // }

    public function create()
    {
        if ($this->input->post()) {
            $data = $this->input->post(); 

            $config['upload_path'] = './uploads/travel/';  
            $config['allowed_types'] = 'pdf|jpg|png';
            $config['max_size'] = 2048;
            $config['encrypt_name'] = TRUE;

            $this->load->library('upload', $config);

            if (!empty($_FILES['file_url']['name'])) {
                if ($this->upload->do_upload('file_url')) {
                    $fileData = $this->upload->data();
                    $data['file_url'] = $fileData['file_name']; 
                } else {
                    $this->session->set_flashdata('danger', $this->upload->display_errors());
                    redirect($_SERVER['HTTP_REFERER']);
                    return;
                }
            }

            $this->TravelModel->insert($data);
            $l_id = $this->db->insert_id();
            $this->TravelModel->travel_track_insert($l_id, 'Pending');
            $staff = $this->Common->one_cond_row_select('hris_staff','IDNumber,schoolID,d_id,emp_type','IDNumber',$this->session->username); 
            $school = $this->Common->one_cond_row_select('schools','adminEmail,schoolID','schoolID',$staff->schoolID); 
            $this->session->set_flashdata('success', 'Travel request added successfully.');

           //Email Notification
            $this->load->config('email');
            $this->load->library('email');
            
           if($staff->schoolID == 0){ 
                $email = 'christian.sango@deped.gov.ph';
                //$email = 'jhunlio@gmail.com';
           }elseif($staff->schoolID == 1){
                $email = 'ernesto.cabanes@deped.gov.ph';
           }elseif($staff->schoolID == 2){
                $email = 'nancy.sumagaysay@deped.gov.ph';
           }else{
                $email = $school->adminEmail;
           }


            $mail_message = '<br><br>This is to inform you that a <b>Travel Authority request</b> has been submitted through the system.' . "\r\n";
            $mail_message .= '<br><br>Please log in to the HRIS portal to review the request and take the necessary action.' . "\r\n";
            $mail_message .= '<br><br><b>Employee:</b> ' .ucfirst($this->session->user) . "\r\n";
            $mail_message .= '<br><br>You may access the system using your account:' . "\r\n";

            $this->email->from('no-reply@depeddavor.com', ucfirst($this->session->user).' (HRIS)')
                ->to($email)
                ->subject('Travel Authority')
                ->message($mail_message);
            $this->email->send();


            if($staff->emp_type == 4 || $staff->emp_type == 3){
                redirect(base_url().'Travel/travel_list/Endorsed');
            }else{
               redirect(base_url().'Travel/travel_list/Pending'); 
            }

        } else {
            $this->load->view('travel_request_form');
        }
    }

    
    public function edit($id)
    {
        $data['request'] = $this->TravelModel->get_by_id($id);
        $this->load->view('travel_request_form', $data);
    }

    public function edit_stat($id)
    {
        $this->load->model('TravelModel');

        $data = ['status' => 'approved'];

        if ($this->TravelModel->updateRequest($id, $data)) {
            $this->session->set_flashdata('success', 'Travel request approved successfully.');
        } else {
            $this->session->set_flashdata('error', 'Failed to approve travel request.');
        }

        // Redirect to the travel request listing
        redirect('travel/viewPending');
    }


    // public function update($id)
    // {
    //     $data = $this->input->post();
    //     $this->TravelModel->update($id, $data);
    //     redirect('travel');
    // }

    public function update($id)
    {
        if ($this->input->post()) {
            $data = $this->input->post();

            $existing = $this->TravelModel->get_by_id($id);

            $config['upload_path'] = './uploads/travel/';
            $config['allowed_types'] = 'pdf';
            $config['max_size'] = 2048;
            $config['encrypt_name'] = TRUE;

            $this->load->library('upload', $config);

            if (!empty($_FILES['file_url']['name'])) {
                if ($this->upload->do_upload('file_url')) {
                    $fileData = $this->upload->data();
                    $newFileName = $fileData['file_name'];
                    $data['file_url'] = $newFileName;

                    if (!empty($existing->file_url) && file_exists('./uploads/travel/' . $existing->file_url)) {
                        unlink('./uploads/travel/' . $existing->file_url);
                    }
                } else {
                    $this->session->set_flashdata('error', $this->upload->display_errors());
                    $this->load->view('travel_request_form', compact('existing'));
                    return;
                }
            } else {
                $data['file_url'] = $existing->file_url;
            }

            $this->TravelModel->update($id, $data);

            redirect('Travel/travel_list/' . $existing->status);
        } else {
            $request = $this->TravelModel->get_by_id($id);
            $this->load->view('travel_request_form', compact('request'));
        }
    }

    public function delete($id)
    {
        $this->TravelModel->delete($id);
        //$this->Common->del('travel_tracker', 'travel_req_id', $id);
        redirect('travel');
    }

    public function delete_the_travel()
    {
        $id = $this->uri->segment(3);
        $tr = $this->Common->one_cond_row('travel_requests','id',$id);
        if($tr->file_url != ''){
            $this->Common->delete_with_attachv2('travel_requests', $id, 'uploads/travel', $tr->file_url);
        }else{
         $this->Common->del('travel_requests', 'id', $id);   
        }
        //$this->Common->del('travel_tracker', 'travel_req_id', $id);
        redirect($_SERVER['HTTP_REFERER']);
    }


    public function return_travel()
    {
        $this->load->model('TravelModel');
        $id = $this->input->post('request_id');
        $remarks = $this->input->post('remarks');

        $data = [
            'status' => 'Returned',
            'asds_comments' => $remarks
        ];

        if ($this->TravelModel->updateRequest($id, $data)) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error']);
        }
    }

    function travel_sign_settings()
        {
            $result['title'] = "SCHOOL-BASED FEEDING PROGRAM";
            $result['data'] = $this->Common->one_cond('travel_sign_settings','user_id',$this->session->c_id);
            $result['staff'] = $this->Common->one_cond_select('hris_staff','IDNumber,FirstName,MiddleName,LastName','currentStatus','Active');
            $result['cur_staff'] = $this->Common->one_cond_row_select('hris_staff','emp_type,IDNumber,d_id,schoolID','IDNumber',$this->session->username);

            if ($this->input->post('submit')) {
                $this->TravelModel->travel_sign_insert();
                redirect(base_url() . 'Travel/travel_sign_settings');
            }

            $this->load->view('travel_sign_setting', $result);
    }

    public function delete_travel_setting()
	{
		$this->Common->del('travel_sign_settings', 'id', $this->uri->segment(3));
        //$this->Common->del('travel_tracker', 'travel_req_id', $this->uri->segment(3));
		$this->Page_model->insert_at('Delete travel autority signaturies', $this->uri->segment(3));
		$this->session->set_flashdata('danger', 'Deleted successfully!');
		redirect('Travel/travel_sign_settings');
	}

    function travel_request_for_action()
        {
            //$result['title'] = "SCHOOL-BASED FEEDING PROGRAM";

            //$result['data'] = $this->Common->travel_for_action();
            //$result['staff'] = $this->Common->one_cond_select('hris_staff','IDNumber,FirstName,MiddleName,LastName','currentStatus','Active');
             //$user = $this->Common->two_cond_row('travel_sign_settings','staff_id',7302760,'ttype',$this->uri->segment(3));
             
             $result['user'] = $this->Common->two_cond_row('travel_sign_settings','staff_id',$this->session->username,'ttype',$this->uri->segment(3));
            

             $result['data'] = $this->Common->two_cond('travel_requests','IDNumber',$result['user']->user_id,'ttype',$this->uri->segment(3));

            $this->load->view('travel_for_action', $result);
    }

    public function action_travel()
	{
       
		$this->TravelModel->travel_update_stat();
        $this->TravelModel->travel_track_insert($this->uri->segment(3),$this->uri->segment(4));
        
		$this->session->set_flashdata('danger', 'Deleted successfully!');
		redirect($_SERVER['HTTP_REFERER']);
	}

    public function action_travel_reject()
	{
       
		$this->TravelModel->travel_update_stat_reject();
        $this->TravelModel->travel_track_insert_reject($this->uri->segment(3),$this->uri->segment(4));
        
		$this->session->set_flashdata('danger', 'Rejected successfully!');
		redirect($_SERVER['HTTP_REFERER']);
	}

    

    public function travel_print_view()
	{
        $result['travel'] = $this->Common->one_cond_row('travel_requests','id',$this->uri->segment(3));
        
		$this->load->view('travel_view', $result);
	}

    public function esig() {
        $staff_id = $this->input->post('staff_id');

        // Get current esig filename from DB
        $query = $this->db->get_where('hris_staff', ['IDNumber' => $staff_id]);
        $staff = $query->row();

        $config['upload_path']   = './uploads/esig/';
        $config['allowed_types'] = 'png';
        $config['max_size']      = 2048; 
        $config['encrypt_name']  = TRUE; 

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('userfile')) {
            $error = ['error' => $this->upload->display_errors()];
            $this->session->set_flashdata('danger', $error['error']);
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            if (!empty($staff->esig)) {
                $old_file = './uploads/esig/' . $staff->esig;
                if (file_exists($old_file)) {
                    unlink($old_file);
                }
            }

            $upload_data = $this->upload->data();
            $filename = $upload_data['file_name'];

            $this->db->where('IDNumber', $staff_id);
            $this->db->update('hris_staff', ['esig' => $filename]);

            $this->session->set_flashdata('success', 'Saved successfully!');
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function update_emp_type()
	{
        $this->TravelModel->emp_type_update();
        redirect('Travel/travel_sign_settings');
        
		$this->load->view('travel_view', $result);
	}

    public function travel_request_list(){
        $ttype = $this->uri->segment(3);
        
        $data['requests'] = $this->Common->four_cond('travel_requests','status','Pending','schoolID',$this->session->username,'ttype',$ttype,'emp_type',1);

		$this->load->view('travel_request_school', $data);
	}

    public function travel_request_list_district(){
        $ttype = $this->uri->segment(3);
        
        $data['requests'] = $this->Common->three_cond('travel_requests','status','Pending','d_id',$this->session->user_id,'ttype',$ttype);

		$this->load->view('travel_request_district', $data);
	}

    public function travel_approved_list(){
        $data['requests'] = $this->Common->two_cond('travel_requests','status','Approved','schoolID',$this->session->username);

		$this->load->view('travel_request_school', $data);
	}

    public function travel_list_school(){
        $data['requests'] = $this->Common->three_cond('travel_requests','schoolID',$this->session->username,'emp_type',1,'fy',$this->session->cur_fy);

		$this->load->view('travel_request_school', $data);
	}

    public function travel_list_district(){
        $data['requests'] = $this->Common->two_cond('travel_requests','d_id',$this->session->user_id,'fy',$this->session->cur_fy);

		$this->load->view('travel_request_district', $data);
	}

    public function travel_list(){
        $stat = $this->uri->segment(3);
        
        $data['requests'] = $this->Common->three_cond('travel_requests','status',$stat,'IDNumber',$this->session->username,'fy',$this->session->cur_fy);

		$this->load->view('travel_request', $data);
	}

    public function travel_list_asds(){
        $ttype = $this->uri->segment(3);
        //$data['requests'] = $this->Common->three_cond('travel_requests','status','Endorsed','ttype',$ttype,'ivy',0);
        //$data['requests'] = $this->Common->two_cond('travel_requests','status','Endorsed','ttype',$ttype,'schoolID',0);
        $data['requests'] = $this->TravelModel->get_travel_requests_asds($ttype);

		$this->load->view('travel_request_asds', $data);
	}

    public function travel_list_asdsv2(){
        $data['requests'] = $this->TravelModel->get_travel_summary();

		$this->load->view('travel_request_all', $data);
	}

    public function travel_list_m_report(){
        $month      = $this->session->cur_month;
        $fy         = $this->session->cur_fy;

        $data['requests'] = $this->TravelModel->get_travel_by_month($month,$fy);

		$this->load->view('travel_monthly', $data);
	}

    public function travel_list_m_report_cheif(){
        $month      = $this->session->cur_month;
        $fy         = $this->session->cur_fy;
        $school_id  = $this->uri->segment(3);


        $data['requests'] = $this->TravelModel->get_travel_by_month_chief($month,$fy,$school_id);

		$this->load->view('travel_monthly', $data);
	}

    public function travel_list_sds(){
        $ttype = $this->uri->segment(3);
        $data['requests'] = $this->Common->two_cond('travel_requests','status','Recommended','ttype',$ttype);

		$this->load->view('travel_request_sds', $data);
	}

    public function travel_list_sdsv2(){
        $data['requests'] = $this->TravelModel->get_travel_summary();

		$this->load->view('travel_request_all', $data);
	}

    public function travel_list_sgod(){
        $ttype = $this->uri->segment(3);
        $data['requests'] = $this->Common->three_cond('travel_requests','status','Endorsed','ttype',$ttype,'schoolID','1');

		$this->load->view('travel_request_chief', $data);
	}

   

    // public function travel_list_sgod_chief(){
    //     $data['requests'] = $this->Common->two_cond('travel_requests','schoolID','1','fy',$this->session->cur_fy);

	// 	$this->load->view('travel_request_chief', $data);
	// }

    public function travel_list_sgod_chief(){
        $data['requests'] = $this->TravelModel->get_travel_summary_chief(1);

		$this->load->view('travel_request_all', $data);
	}

    public function travel_list_cid(){
        $ttype = $this->uri->segment(3);
        $data['requests'] = $this->Common->three_cond('travel_requests','status','Endorsed','ttype',$ttype,'schoolID','2');

		$this->load->view('travel_request_chief', $data);
	}

    public function travel_list_cid_chief(){
        $data['requests'] = $this->TravelModel->get_travel_summary_chief(2);

		$this->load->view('travel_request_all', $data);
	}

    public function travel_list_titling(){
        $ttype = $this->uri->segment(3);
        $data['requests'] = $this->Common->three_cond('travel_requests','status','Endorsed','ttype',$ttype,'schoolID','3');

		$this->load->view('travel_request_chief', $data);
	}

    public function travel_list_titlingv2(){
        $data['requests'] = $this->TravelModel->get_travel_summary_chief(3);

		$this->load->view('travel_request_all', $data);
	}

    
    // public function travel_list_cid_chief(){
    //     $data['requests'] = $this->Common->two_cond('travel_requests','schoolID','2','fy',$this->session->cur_fy);

	// 	$this->load->view('travel_request_chief', $data);
	// }

    public function travel_by_staff(){
        
        $id = $this->uri->segment(3);

        $data['requests'] = $this->Common->two_cond('travel_requests','IDNumber',$id,'fy',$this->session->cur_fy);
        $data['staff'] = $this->Common->one_cond_row_select('hris_staff','LastName,FirstName,IDNumber,empPosition,schoolID,Department','IDNumber',$id);

		$this->load->view('travel_request_staff', $data);
	}

    public function travel_ca(){
        
        $id = $this->uri->segment(3);
        $ca_id = $this->uri->segment(4);


        $data['staff'] = $this->Common->one_cond_row('hris_staff','IDNumber',$id);
        $data['ca'] = $this->Common->one_cond_row('travel_ca','travel_id',$ca_id);
        $data['tr'] = $this->Common->one_cond_row('travel_requests','id',$ca_id);

		$this->load->view('travel_ca', $data);
	}

    
    
    

    
}
