<?php

class Nosa extends CI_Controller
{

	function __construct()
	{
		parent::__construct();
	}

    public function nosa()
    {

        $page = "nosa_print";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

		$data['title'] = "Nosa";


        $this->load->view('pages/' . $page, $data);
    }





}
