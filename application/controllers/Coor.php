<?php

class Coor extends CI_Controller
{

	function __construct()
	{
		parent::__construct();
	}

    public function coor()
    {

        $page = "cid_coor_list";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $data['cat'] = $this->Common->no_cond('cid_coor_type');    
        $data['coor'] = $this->Common->no_cond('cid_coor_list');    
        $data['staff'] = $this->Common->one_cond('hris_staff','schoolID',$this->session->username); 
		$data['data'] = $this->Common->one_cond('cid_coor_staff','school_id',$this->session->username);
		$data['title'] = "List of Personnel with Coordinatorship";


        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function mycoor()
    {

        $page = "cid_my_coor";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

		$data['data'] = $this->Common->one_cond('cid_coor_staff','staff_id',$this->session->c_id);
		$data['title'] = "List of my Coordinatorship";


        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function mycoor_district()
    {

        $page = "mathcoor_district";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

		//$data['data'] = $this->Common->one_cond('cid_coor_staff','district_id',$this->session->c_id);
		$data['title'] = "List of my Coordinatorship";


        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function mycoor_subject_district()
    {

        $page = "mathcoor_subject_district";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

		$data['data'] = $this->Common->one_cond('cid_coor_list','coor_type_id',1);
		$data['title'] = "List of my Coordinatorship";


        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function coor_view()
    {
        // if($this->uri->segment(3) == 2){
        //     $page = "cid_coor_math_view";
        //     $data['title'] = "Mathematics Coordinator";
        // }else{
        //     redirect(base_url().'mycoor');
        // }
        $data['title'] = "";
        $page = "cid_coor_math_view";
        
        
        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }
        $data['staff'] = $this->Common->one_cond_row_select('hris_staff','IDNumber,schoolID','IDNumber',$this->session->c_id);

        $school_id = trim($data['staff']->schoolID);

        $data['section'] = $this->Common->one_cond_group_ob('sections','school_id',$school_id, 'Section','Section', 'ASC');
		$data['data'] = $this->Common->one_cond('cid_coor_staff','staff_id',$this->session->c_id);
		


        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function coor_entry_view()
    {
        $page = "cid_coor_math_entry_view";
        
        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }
        $data['staff'] = $this->Common->one_cond_row_select('hris_staff','IDNumber,schoolID','IDNumber',$this->session->c_id);

        $school_id = trim($data['staff']->schoolID);

        $data['school'] = $this->Common->one_cond_row_select('schools','district,schoolID','schoolID',$school_id);
        $dis = trim($data['school']->district);

        $data['district'] = $this->Common->one_cond_row_select('district','discription,id','discription',$dis);

        $coor = $this->Common->one_cond_row('cid_coor_list','id',$this->uri->segment(3));

        $data['section'] = $this->Common->one_cond_group_ob('sections','school_id',$school_id, 'Section','Section', 'ASC');

		//$data['data'] = $this->Common->one_cond_group_ob('cid_coor_math_proficiency','staff_id',$this->session->c_id, 'quarter,year,grade_level','grade_level', 'ASC');
		$data['data'] = $this->Common->two_cond_group_ob('cid_coor_math_proficiency','staff_id',$this->session->c_id, 'subject',$this->uri->segment(3), 'quarter,year,grade_level','grade_level', 'ASC');
        
        $data['title'] = "Level Proficiency ".$coor->name;


        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function coor_entry_view_school()
    {
        $page = "cid_coor_math_entry_view";
        
        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }
        $data['staff'] = $this->Common->one_cond_row_select('hris_staff','IDNumber,schoolID','IDNumber',$this->session->c_id);

        $school_id = $this->session->username;

        $data['school'] = $this->Common->one_cond_row_select('schools','district,schoolID','schoolID',$school_id);
        $dis = trim($data['school']->district);

        $data['district'] = $this->Common->one_cond_row_select('district','discription,id','discription',$dis);

        $coor = $this->Common->one_cond_row('cid_coor_list','id',$this->uri->segment(3));

        $data['section'] = $this->Common->one_cond_group_ob('sections','school_id',$school_id, 'Section','Section', 'ASC');

		//$data['data'] = $this->Common->one_cond_group_ob('cid_coor_math_proficiency','staff_id',$this->session->c_id, 'quarter,year,grade_level','grade_level', 'ASC');
		$data['data'] = $this->Common->two_cond_group_ob('cid_coor_math_proficiency','school_id',$school_id, 'subject',$this->uri->segment(3), 'quarter,year,grade_level','grade_level', 'ASC');
        
        $data['title'] = "Level Proficiency ".$coor->name;


        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    

    public function coor_entry_view_admin()
    {
        $page = "cid_coor_math_entry_view_admin";

        
        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }
        //$data['staff'] = $this->Common->one_cond_row_select('hris_staff','IDNumber,schoolID','IDNumber',$this->uri->segment(4));

        $school_id = $this->uri->segment(4);

        $data['school'] = $this->Common->one_cond_row_select('schools','district,schoolID','schoolID',$school_id);
        $dis = trim($data['school']->district);

        $data['district'] = $this->Common->one_cond_row_select('district','discription,id','discription',$dis);

        $data['section'] = $this->Common->one_cond_group_ob('sections','school_id',$school_id, 'Section','Section', 'ASC');

		$data['data'] = $this->Common->one_cond_group_ob('cid_coor_math_proficiency','school_id',$school_id, 'quarter,year,grade_level','grade_level', 'ASC');
		$data['title'] = "Level Proficiency";


        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function coor_entry_details()
    {
        $page = "cid_coor_math_details";
        
        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

		$data['data'] = $this->Common->five_cond('cid_coor_math_proficiency','staff_id',$this->session->c_id,'quarter',$this->uri->segment(3),'year',$this->uri->segment(4),'grade_level',$this->uri->segment(5),'subject',$this->uri->segment(6));
		$data['title'] = "Level Proficiency";


        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function coor_entry_details_school()
    {
        $page = "cid_coor_math_details";
        
        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }
        $staff = $this->Common->two_cond_row('cid_coor_staff','school_id',$this->session->c_id,'coor_id',$this->uri->segment(6));

		$data['data'] = $this->Common->five_cond('cid_coor_math_proficiency','staff_id',$staff->staff_id,'quarter',$this->uri->segment(3),'year',$this->uri->segment(4),'grade_level',$this->uri->segment(5),'subject',$this->uri->segment(6));
		$data['title'] = "Level Proficiency";


        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function coor_entry_details_admin()
    {
        $page = "cid_coor_math_details";
        
        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

		$data['data'] = $this->Common->four_cond('cid_coor_math_proficiency','school_id',$this->uri->segment(6),'quarter',$this->uri->segment(3),'year',$this->uri->segment(4),'grade_level',$this->uri->segment(5));
		$data['title'] = "Level Proficiency";


        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function math_coor_proficiency_report()
    {
        $page = "cid_coor_math_report";
        
        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

		$data['district'] = $this->Common->no_cond_except('district','id',18);
		$data['title'] = "District Level Proficiency";


        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function math_coor_proficiency_schoollist_report()
    {
        $page = "cid_coor_math_school_report";
        
        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

		$data['school'] = $this->Common->three_cond_select('schools','schoolID,schoolName,district','district',$this->input->get('district'),'schoolType','Public','stat',0);
		$data['title'] = "District Level Proficiency";


        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function math_coor_proficiency_school_report()
    {
        $page = "cid_coor_school_math_report";
        
        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

		$data['school'] = $this->Common->one_cond_select('schools','district,schoolName,schoolID','district',$this->input->get('district'));
		$data['title'] = "District Level Proficiency";


        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function level_proficiency()
    {
        $this->Coor_model->proficiency_insert();
        $this->session->set_flashdata('success', 'Successfully Saved.');
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function total_section()
    {
        $fy = $this->input->post('fy');
        $school_id = $this->input->post('school_id');
        
        $sc = $this->Common->two_cond_count_row('section_count', 'fy', $fy, 'school_id', $school_id);
        if($sc->num_rows() == 0){
            $this->Coor_model->insert_section_number();
            $this->session->set_flashdata('success', 'Successfully Saved.');
        }else{
            $this->session->set_flashdata('danger', 'Duplicate entry.');
        }

        
        redirect(base_url() . 'Coor/coor_entry_view');
    }

    public function report_delete()
    {
        $this->Page_model->delete('3', 'id', 'cid_coor_staff');
        $this->session->set_flashdata('danger', 'Successfully deleted.');
        redirect(base_url() . 'coor');
    }

    public function get_coordinatorships_by_category() {
        $category_id = $this->input->post('category_id');
        $data = $this->Coor_model->get_coor_by_category($category_id);

        echo json_encode($data);
    }

    public function coor_personnel_add()
    {
        $this->Coor_model->coor_personnel_insert();
        $this->session->set_flashdata('success', 'Successfully Saved.');
        redirect(base_url() . 'coor');
    }

    public function cid_coor_delete_entry()
    {
        $this->Page_model->delete('3', 'id', 'cid_coor_math_proficiency');
        $this->session->set_flashdata('danger', 'Successfully deleted.');
        redirect($_SERVER['HTTP_REFERER']);
    }

    





}
