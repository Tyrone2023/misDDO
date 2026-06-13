<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class FileUploader extends CI_Controller {

	public function __construct(){

		parent::__construct();

		// load base_url
		$this->load->helper('url');

		// Load Model
		$this->load->model('Main_model');
	}
	
	public function index() {
		// Check if form is submitted
		if ($this->input->post('upload')) { 
			$data = ['response' => '']; // Initialize response
	
			// Check if file is uploaded
			if (!empty($_FILES['file']['name'])) { 
				// Set upload preferences
				$config = [
					'upload_path'   => 'uploads/studeprofile/',
					'allowed_types' => 'csv',
					'max_size'      => '1000', // max_size in kb
					'file_name'     => $_FILES['file']['name']
				];
	
				// Load upload library
				$this->load->library('upload', $config); 
	
				// Attempt to upload the file
				if ($this->upload->do_upload('file')) { 
					$uploadData = $this->upload->data(); 
					$filename = $uploadData['file_name'];
	
					// Reading the CSV file
					$importDataArr = [];
					if (($file = fopen("uploads/studeprofile/" . $filename, "r")) !== FALSE) {
						while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
							$importDataArr[] = $filedata; // Store each row
						}
						fclose($file);
					}
	
					// Insert import data, skipping the header row
					foreach (array_slice($importDataArr, 1) as $userdata) {
						$this->Main_model->insertRecord($userdata);
					}
					$data['response'] = '<div class="alert alert-info text-center">Uploaded Successfully.</div>'; 
				} else { 
					$data['response'] = '<div class="alert alert-warning text-center">Upload Failed: ' . $this->upload->display_errors() . '</div>'; 
				} 
			} else { 
				$data['response'] = '<div class="alert alert-warning text-center">No file selected.</div>'; 
			} 
	
			// Load view with response
			$this->load->view('file_uploader', $data); 
		} else {
			// Load view without response
			$this->load->view('file_uploader'); 
		} 
	}
	
public function teachers(){
		// Check if form is submitted
		if ($this->input->post('upload')) { 
			$data = ['response' => '']; // Initialize response
	
			// Check if file is uploaded
			if (!empty($_FILES['file']['name'])) { 
				// Set upload preferences
				$config = [
					'upload_path'   => 'upload/files/',
					'allowed_types' => 'csv',
					'max_size'      => '1000', // max_size in kb
					'file_name'     => $_FILES['file']['name']
				];
	
				// Load upload library
				$this->load->library('upload', $config); 
	
				// Attempt to upload the file
				if ($this->upload->do_upload('file')) { 
					$uploadData = $this->upload->data(); 
					$filename = $uploadData['file_name'];
	
					// Reading the CSV file
					$importDataArr = [];
					if (($file = fopen("upload/files/" . $filename, "r")) !== FALSE) {
						while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
							$importDataArr[] = $filedata; // Store each row
						}
						fclose($file);
					}
	
					// Insert import data, skipping the header row
					foreach (array_slice($importDataArr, 1) as $userdata) {
						$this->Main_model->insertTeachers($userdata);
					}
					$data['response'] = '<div class="alert alert-info text-center">Uploaded Successfully.</div>'; 
				} else { 
					$data['response'] = '<div class="alert alert-warning text-center">Upload Failed: ' . $this->upload->display_errors() . '</div>'; 
				} 
			} else { 
				$data['response'] = '<div class="alert alert-warning text-center">No file selected.</div>'; 
			} 
	
			// Load view with response
			$this->load->view('file_uploader_teacher', $data); 
		} else {
			// Load view without response
			$this->load->view('file_uploader_teacher'); 
		} 
	}

public function Courses(){

		// Check form submit or not 
 		if($this->input->post('upload') != NULL ){ 
   			$data = array(); 
   			if(!empty($_FILES['file']['name'])){ 
    			// Set preference 
    			$config['upload_path'] = 'upload/files/'; 
    			$config['allowed_types'] = 'csv'; 
    			$config['max_size'] = '1000'; // max_size in kb 
    			$config['file_name'] = $_FILES['file']['name']; 

    			// Load upload library 
    			$this->load->library('upload',$config); 
   
    			// File upload
    			if($this->upload->do_upload('file')){ 
     				// Get data about the file
     				$uploadData = $this->upload->data(); 
     				$filename = $uploadData['file_name']; 

     				// Reading file
                    $file = fopen("upload/files/".$filename,"r");
                    $i = 0;

                    $importData_arr = array();
                       
                    while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
                        $num = count($filedata);

                        for ($c=0; $c < $num; $c++) {
                            $importData_arr[$i][] = $filedata[$c];
                        }
                        $i++;
                    }
                    fclose($file);

                    $skip = 0;

                    // insert import data
                    foreach($importData_arr as $userdata){
                        if($skip != 0){
                            $this->Main_model->insertCourses($userdata);
                        }
                        $skip ++;
                    }
     				$data['response'] = '<div class="alert alert-info text-center">Uploaded Successfully.</div>'; 
					
    			}else{ 
     				$data['response'] = '<div class="alert alert-warning text-center">Failed</div>'; 
    			} 
   			}else{ 
    			$data['response'] = '<div class="alert alert-warning text-center">Failed</div>'; 
   			} 
   			// load view 
   			$this->load->view('file_uploader_courses',$data); 
  		}else{
   			// load view 
   			$this->load->view('file_uploader_courses'); 
			

  		} 

	}
public function sections(){

		// Check form submit or not 
 		if($this->input->post('upload') != NULL ){ 
   			$data = array(); 
   			if(!empty($_FILES['file']['name'])){ 
    			// Set preference 
    			$config['upload_path'] = 'upload/files/'; 
    			$config['allowed_types'] = 'csv'; 
    			$config['max_size'] = '1000'; // max_size in kb 
    			$config['file_name'] = $_FILES['file']['name']; 

    			// Load upload library 
    			$this->load->library('upload',$config); 
   
    			// File upload
    			if($this->upload->do_upload('file')){ 
     				// Get data about the file
     				$uploadData = $this->upload->data(); 
     				$filename = $uploadData['file_name']; 

     				// Reading file
                    $file = fopen("upload/files/".$filename,"r");
                    $i = 0;

                    $importData_arr = array();
                       
                    while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
                        $num = count($filedata);

                        for ($c=0; $c < $num; $c++) {
                            $importData_arr[$i][] = $filedata[$c];
                        }
                        $i++;
                    }
                    fclose($file);

                    $skip = 0;

                    // insert import data
                    foreach($importData_arr as $userdata){
                        if($skip != 0){
                            $this->Main_model->insertSections($userdata);
                        }
                        $skip ++;
                    }
     				$data['response'] = '<div class="alert alert-info text-center">Uploaded Successfully.</div>'; 
					
    			}else{ 
     				$data['response'] = '<div class="alert alert-warning text-center">Failed</div>'; 
    			} 
   			}else{ 
    			$data['response'] = '<div class="alert alert-warning text-center">Failed</div>'; 
   			} 
   			// load view 
   			$this->load->view('file_uploader_sections',$data); 
  		}else{
   			// load view 
   			$this->load->view('file_uploader_sections'); 
  		} 

	}

public function subjects(){

		// Check form submit or not 
 		if($this->input->post('upload') != NULL ){ 
   			$data = array(); 
   			if(!empty($_FILES['file']['name'])){ 
    			// Set preference 
    			$config['upload_path'] = 'upload/files/'; 
    			$config['allowed_types'] = 'csv'; 
    			$config['max_size'] = '1000'; // max_size in kb 
    			$config['file_name'] = $_FILES['file']['name']; 

    			// Load upload library 
    			$this->load->library('upload',$config); 
   
    			// File upload
    			if($this->upload->do_upload('file')){ 
     				// Get data about the file
     				$uploadData = $this->upload->data(); 
     				$filename = $uploadData['file_name']; 

     				// Reading file
                    $file = fopen("upload/files/".$filename,"r");
                    $i = 0;

                    $importData_arr = array();
                       
                    while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
                        $num = count($filedata);

                        for ($c=0; $c < $num; $c++) {
                            $importData_arr[$i][] = $filedata[$c];
                        }
                        $i++;
                    }
                    fclose($file);

                    $skip = 0;

                    // insert import data
                    foreach($importData_arr as $userdata){
                        if($skip != 0){
                            $this->Main_model->insertSubjects($userdata);
                        }
                        $skip ++;
                    }
     				$data['response'] = '<div class="alert alert-info text-center">Uploaded Successfully.</div>'; 
					
    			}else{ 
     				$data['response'] = '<div class="alert alert-warning text-center">Failed</div>'; 
    			} 
   			}else{ 
    			$data['response'] = '<div class="alert alert-warning text-center">Failed</div>'; 
   			} 
   			// load view 
   			$this->load->view('file_uploader_subjects',$data); 
  		}else{
   			// load view 
   			$this->load->view('file_uploader_subjects'); 
  		} 

	}
public function courseFees(){

		// Check form submit or not 
 		if($this->input->post('upload') != NULL ){ 
   			$data = array(); 
   			if(!empty($_FILES['file']['name'])){ 
    			// Set preference 
    			$config['upload_path'] = 'upload/files/'; 
    			$config['allowed_types'] = 'csv'; 
    			$config['max_size'] = '1000'; // max_size in kb 
    			$config['file_name'] = $_FILES['file']['name']; 

    			// Load upload library 
    			$this->load->library('upload',$config); 
   
    			// File upload
    			if($this->upload->do_upload('file')){ 
     				// Get data about the file
     				$uploadData = $this->upload->data(); 
     				$filename = $uploadData['file_name']; 

     				// Reading file
                    $file = fopen("upload/files/".$filename,"r");
                    $i = 0;

                    $importData_arr = array();
                       
                    while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
                        $num = count($filedata);

                        for ($c=0; $c < $num; $c++) {
                            $importData_arr[$i][] = $filedata[$c];
                        }
                        $i++;
                    }
                    fclose($file);

                    $skip = 0;

                    // insert import data
                    foreach($importData_arr as $userdata){
                        if($skip != 0){
                            $this->Main_model->insertFees($userdata);
                        }
                        $skip ++;
                    }
     				$data['response'] = '<div class="alert alert-info text-center">Uploaded Successfully.</div>'; 
					
    			}else{ 
     				$data['response'] = '<div class="alert alert-warning text-center">Failed</div>'; 
    			} 
   			}else{ 
    			$data['response'] = '<div class="alert alert-warning text-center">Failed</div>'; 
   			} 
   			// load view 
   			$this->load->view('file_uploader_course_fees',$data); 
  		}else{
   			// load view 
   			$this->load->view('file_uploader_course_fees'); 
  		} 

	}	
}
