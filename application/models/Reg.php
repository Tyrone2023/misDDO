<?php

class Reg extends CI_Model{

    public function __construct(){
          $this->load->database();

    }

    public function educ_jhss_update(){
       $g = $this->input->post('learn');
       $s = $this->input->post('special');
       if($s == ''){
        $jhss = $g;
       }else{
        $jhss = $g.' - '.$s;
       }

      $data = array(
        'jhss' => $jhss
      );

      $this->db->where('id', $this->input->post('id'));
      return $this->db->update('hris_applicant', $data);
    }

    public function educ_shss_update(){
        $g = $this->input->post('group');
       $s = $this->input->post('strand');
       if($s == ''){
        $shss = $g;
       }else{
        $shss = $g.' - '.$s;
       }

      $data = array(
        'shss' => $shss
      );

      $this->db->where('id', $this->input->post('id'));
      return $this->db->update('hris_applicant', $data);
    }


    public function educ_update(){

       if($this->input->post('jhss') == ""){
       $g = $this->input->post('learn');
       $s = $this->input->post('special');
       if($s == ''){
        $jhss = $g;
       }else{
        $jhss = $g.' - '.$s;
       }
       }else{
        $jhss = $this->input->post('jhss');
       }
       
    
      if($this->input->post('shss') == ""){
       $i = $this->input->post('group');
       $c = $this->input->post('strand');
       if($c == ''){
        $shss = $i;
       }else{
        $shss = $i.' - '.$c;
       }
       }else{
        $shss = $this->input->post('shss');
       }

      $data = array(
        'bd' => $this->input->post('bd'), 
        'dg' => $this->input->post('dg'), 
        'du' => $this->input->post('du'), 
        'dgwa' => $this->input->post('dgwa'), 
        'ue' => $this->input->post('ue'), 
        'gwae' => $this->input->post('gwae'), 
        'pg' => $this->input->post('pg'), 
        'pgu' => $this->input->post('pgu'), 
        'jhss' => $jhss,
        'shss' => $shss,
        'specialization' => $this->input->post('s')
      );

      $this->db->where('id', $this->input->post('id'));
      return $this->db->update('hris_applicant', $data);
    }

    public function educ_update_staff(){
      if($this->input->post('jhss') == ""){
       $g = $this->input->post('learn');
       $s = $this->input->post('special');
       if($s == ''){
        $jhss = $g;
       }else{
        $jhss = $g.' - '.$s;
       }
       }else{
        $jhss = $this->input->post('jhss');
       }
       
    
      if($this->input->post('shss') == ""){
       $i = $this->input->post('group');
       $c = $this->input->post('strand');
       if($c == ''){
        $shss = $i;
       }else{
        $shss = $i.' - '.$c;
       }
       }else{
        $shss = $this->input->post('shss');
       }


      $data = array(
        'bd' => $this->input->post('bd'), 
        'dg' => $this->input->post('dg'), 
        'du' => $this->input->post('du'), 
        'dgwa' => $this->input->post('dgwa'), 
        'ue' => $this->input->post('ue'), 
        'gwae' => $this->input->post('gwae'), 
        'pg' => $this->input->post('pg'), 
        'pgu' => $this->input->post('pgu'), 
        'jhss' => $jhss,
        'shss' => $shss,
        'specialization' => $this->input->post('s')
      );

      $this->db->where('IDNumber', $this->input->post('id'));
      return $this->db->update('hris_staff', $data);
    }

    public function ai_update(){

      $data = array( 
              'FirstName' => $this->input->post('FirstName'),
              'MiddleName' => $this->input->post('MiddleName'),
              'LastName' => $this->input->post('LastName'),
              'NameExtn' => $this->input->post('NameExtn'),
              'resVillage' => $this->input->post('resVillage'),
              'resBarangay' => $this->input->post('resBarangay'),
              'resCity' => $this->input->post('resCity'),
              'resProvince' => $this->input->post('resProvince'),
              'Sex' => $this->input->post('Sex'),
              'contactNo' => $this->input->post('contactNo'),
              'asht' => $this->input->post('asht'),
              'empPosition' => $this->input->post('cp')
        );

      $this->db->where('id', $this->input->post('id'));
      return $this->db->update('hris_applicant', $data);
    }

    public function ai_update_staff(){

      $data = array( 
              'FirstName' => $this->input->post('FirstName'),
              'MiddleName' => $this->input->post('MiddleName'),
              'LastName' => $this->input->post('LastName'),
              'NameExtn' => $this->input->post('NameExtn'),
              'resVillage' => $this->input->post('resVillage'),
              'resBarangay' => $this->input->post('resBarangay'),
              'resCity' => $this->input->post('resCity'),
              'resProvince' => $this->input->post('resProvince'),
              'Sex' => $this->input->post('Sex'),
              'contactNo' => $this->input->post('contactNo'),
              'asht' => $this->input->post('asht'),
              'empPosition' => $this->input->post('cp')
        );

      $this->db->where('IDNumber', $this->input->post('id'));
      return $this->db->update('hris_staff', $data);
    }

   

    public function ai_sex_update(){
      if($this->input->post('Sex') == "M"){
        $sex = 0;
      }else{
        $sex = 1;
      }

      $data = array( 
        'sex' => $sex
      );

      $this->db->where('user_id', $this->input->post('id'));
      return $this->db->update('users', $data);
    }

    public function lr_update(){

      $data = array( 
              'lr' => $this->input->post('lr')
      );

      $this->db->where('empEmail', $this->input->post('empEmail'));
      //$this->db->where('jobID', $this->input->post('jobID'));
      return $this->db->update('hris_applicant', $data);
    }


    public function lr_update_staff(){

      $data = array( 
              'lr' => $this->input->post('lr')
      );

      $this->db->where('IDNumber', $this->input->post('id'));
      //$this->db->where('jobID', $this->input->post('jobID'));
      return $this->db->update('hris_staff', $data);
    }

    public function ept_update(){

      $data = array( 
              'ept' => $this->input->post('ept'),
              'eptr' => $this->input->post('eptr')
      );

      $this->db->where('empEmail', $this->input->post('empEmail'));
      $this->db->where('jobID', $this->input->post('jobID'));
      return $this->db->update('hris_applications', $data);
    }

    public function tc_update(){

      $data = array( 
              'tc' => $this->input->post('tc')
      );

      $this->db->where('empEmail', $this->input->post('empEmail'));
      //$this->db->where('jobID', $this->input->post('jobID'));
      return $this->db->update('hris_applicant', $data);
    }

    public function tc_update_staff(){

      $data = array( 
              'tc' => $this->input->post('tc')
      );

      $this->db->where('IDNumber', $this->input->post('id'));
      //$this->db->where('jobID', $this->input->post('jobID'));
      return $this->db->update('hris_staff', $data);
    }

    public function master_file_update(){

            $file = $this->upload->data();
            $filename = $file['file_name']; 

            $data = array(
                'master_file' => $filename,
                'master_units' => $this->input->post('units'),
                'master' => $this->input->post('master'),
                'master_stat' => $this->input->post('master_stat')
                );

                $this->db->where('empEmail', $this->input->post('empEmail'));
                //$this->db->where('jobID', $this->input->post('jobID'));
            return $this->db->update('hris_applicant', $data);
    }

    public function doctor_file_update(){

            $file = $this->upload->data();
            $filename = $file['file_name']; 

            $data = array(
                'doctor_file' => $filename,
                'doctor_units' => $this->input->post('units'),
                'doctor' => $this->input->post('doctor'),
                'doctor_stat' => $this->input->post('doctor_stat')
                );

                $this->db->where('empEmail', $this->input->post('empEmail'));
                //$this->db->where('jobID', $this->input->post('jobID'));
            return $this->db->update('hris_applicant', $data);
    }

    public function educfile_update(){

            $file = $this->upload->data();
            $filename = $file['file_name']; 

            $data = array(
                'efile' => $filename
                );

                $this->db->where('empEmail', $this->input->post('empEmail'));
                //$this->db->where('jobID', $this->input->post('jobID'));
            return $this->db->update('hris_applicant', $data);
    }

      public function educfile_update_staff(){

            $file = $this->upload->data();
            $filename = $file['file_name']; 

            $data = array(
                'efile' => $filename
                );

                $this->db->where('IDNumber', $this->input->post('id'));
                //$this->db->where('jobID', $this->input->post('jobID'));
            return $this->db->update('hris_staff', $data);
      }

      public function master_update_staff(){

            $file = $this->upload->data();
            $filename = $file['file_name']; 

            $data = array(
                'master_file' => $filename,
                'master_units' => $this->input->post('units'),
                'master' => $this->input->post('master'),
                'master_stat' => $this->input->post('master_stat')
                );

                $this->db->where('IDNumber', $this->input->post('id'));
                //$this->db->where('jobID', $this->input->post('jobID'));
            return $this->db->update('hris_staff', $data);
      }

      public function doctor_update_staff(){

            $file = $this->upload->data();
            $filename = $file['file_name']; 

            $data = array(
                'doctor_file' => $filename,
                'doctor_units' => $this->input->post('units'),
                'doctor' => $this->input->post('doctor'),
                'doctor_stat' => $this->input->post('doctor_stat')
                );

                $this->db->where('IDNumber', $this->input->post('id'));
                //$this->db->where('jobID', $this->input->post('jobID'));
            return $this->db->update('hris_staff', $data);
      }

      public function outfile_update_staff(){

        $file = $this->upload->data();
        $filename = $file['file_name']; 

        $data = array(
            'oa' => $filename
            );

            $this->db->where('IDNumber', $this->input->post('id'));
            //$this->db->where('jobID', $this->input->post('jobID'));
        return $this->db->update('hris_staff', $data);
      }

      public function aefile_update_staff(){

        $file = $this->upload->data();
        $filename = $file['file_name']; 

        $data = array(
            'ae' => $filename
            );

            $this->db->where('IDNumber', $this->input->post('id'));
            //$this->db->where('jobID', $this->input->post('jobID'));
        return $this->db->update('hris_staff', $data);
      }

      public function aefile_update(){

        $file = $this->upload->data();
        $filename = $file['file_name']; 

        $data = array(
            'ae' => $filename
            );

            $this->db->where('empEmail', $this->input->post('empEmail'));
                //$this->db->where('jobID', $this->input->post('jobID'));
            return $this->db->update('hris_applicant', $data);
      }

      public function aldfile_update_staff(){

        $file = $this->upload->data();
        $filename = $file['file_name']; 

        $data = array(
            'ald' => $filename
            );

            $this->db->where('IDNumber', $this->input->post('id'));
            //$this->db->where('jobID', $this->input->post('jobID'));
        return $this->db->update('hris_staff', $data);
      }

      public function aldfile_update(){

        $file = $this->upload->data();
        $filename = $file['file_name']; 

        $data = array(
            'ald' => $filename
            );

            $this->db->where('empEmail', $this->input->post('empEmail'));
                //$this->db->where('jobID', $this->input->post('jobID'));
            return $this->db->update('hris_applicant', $data);
      }

      public function educfile_update_staff_none(){

        $file = $this->upload->data();
        $filename = $file['file_name']; 

        $data = array(
            'tor_cav' => $filename
            );

            $this->db->where('IDNumber', $this->input->post('id'));
            //$this->db->where('jobID', $this->input->post('jobID'));
        return $this->db->update('hris_staff', $data);
      }

      public function educfile_update_none(){

        $file = $this->upload->data();
        $filename = $file['file_name']; 

        $data = array(
            'tor_cav' => $filename
            );

            $this->db->where('empEmail', $this->input->post('empEmail'));
            //$this->db->where('jobID', $this->input->post('jobID'));
        return $this->db->update('hris_applicant', $data);
      }

      public function wefile_update(){

            $file = $this->upload->data();
            $filename = $file['file_name']; 

            $data = array(
                'wefile' => $filename
                );

            $this->db->where('empEmail', $this->input->post('empEmail'));
            //$this->db->where('jobID', $this->input->post('jobID'));
        return $this->db->update('hris_applicant', $data);
      }

      public function wefile_update_staff(){

            $file = $this->upload->data();
            $filename = $file['file_name']; 

            $data = array(
                'wefile' => $filename
                );

            $this->db->where('IDNumber', $this->input->post('id'));
            //$this->db->where('jobID', $this->input->post('jobID'));
        return $this->db->update('hris_staff', $data);
      }

      public function eligibility_update_staff(){

            $file = $this->upload->data();
            $filename = $file['file_name']; 

            $data = array(
                'eligibility' => $filename
                );

            $this->db->where('IDNumber', $this->input->post('id'));
            //$this->db->where('jobID', $this->input->post('jobID'));
        return $this->db->update('hris_staff', $data);
      }

      public function eligibility_update(){

            $file = $this->upload->data();
            $filename = $file['file_name']; 

            $data = array(
                'eligibility' => $filename
                );

                $this->db->where('empEmail', $this->input->post('empEmail'));
            //$this->db->where('jobID', $this->input->post('jobID'));
        return $this->db->update('hris_applicant', $data);
      }

      public function letfile_update(){

            $file = $this->upload->data();
            $filename = $file['file_name']; 

            $data = array(
                'letfile' => $filename
                );

            $this->db->where('empEmail', $this->input->post('empEmail'));
            //$this->db->where('jobID', $this->input->post('jobID'));
        return $this->db->update('hris_applicant', $data);
      }

      public function letfile_update_staff(){

            $file = $this->upload->data();
            $filename = $file['file_name']; 

            $data = array(
                'letfile' => $filename
                );

            $this->db->where('IDNumber', $this->input->post('id'));
            //$this->db->where('jobID', $this->input->post('jobID'));
        return $this->db->update('hris_staff', $data);
      }

      public function tscfile_update(){

          $file = $this->upload->data();
          $filename = $file['file_name']; 

          $data = array(
              'tscfile' => $filename
              );

            $this->db->where('empEmail', $this->input->post('empEmail'));
            //$this->db->where('jobID', $this->input->post('jobID'));
        return $this->db->update('hris_applicant', $data);
      }

      public function tscfile_update_staff(){

        $file = $this->upload->data();
        $filename = $file['file_name']; 

        $data = array(
            'tscfile' => $filename
            );

          $this->db->where('IDNumber', $this->input->post('id'));
          //$this->db->where('jobID', $this->input->post('jobID'));
      return $this->db->update('hris_staff', $data);
    }

      public function tcfile_update(){

          $file = $this->upload->data();
          $filename = $file['file_name']; 

          $data = array(
              'tcfile' => $filename
              );

              $this->db->where('empEmail', $this->input->post('empEmail'));
              //$this->db->where('jobID', $this->input->post('jobID'));
        return $this->db->update('hris_applicant', $data);
      }

      public function tcfile_update_staff(){

        $file = $this->upload->data();
        $filename = $file['file_name']; 

        $data = array(
            'tcfile' => $filename
            );

            $this->db->where('IDNumber', $this->input->post('id'));
            //$this->db->where('jobID', $this->input->post('jobID'));
      return $this->db->update('hris_staff', $data);
    }

      public function omni_update(){

          $file = $this->upload->data();
          $filename = $file['file_name']; 

          $data = array(
              'omnibus' => $filename
              );

              $this->db->where('empEmail', $this->input->post('empEmail'));
              //$this->db->where('jobID', $this->input->post('jobID'));
        return $this->db->update('hris_applicant', $data);
      }

      public function omni_update_staff(){

        $file = $this->upload->data();
        $filename = $file['file_name']; 

        $data = array(
            'omnibus' => $filename
            );

            $this->db->where('IDNumber', $this->input->post('id'));
            //$this->db->where('jobID', $this->input->post('jobID'));
      return $this->db->update('hris_staff', $data);
    }

      public function apfile_update(){

          $file = $this->upload->data();
          $filename = $file['file_name']; 

          $data = array(
              'application' => $filename
              );

              // $this->db->where('empEmail', $this->input->post('empEmail'));
              // $this->db->where('jobID', $this->input->post('jobID'));
              // $this->db->where('pre_school', $this->input->post('school_id'));
              $this->db->where('appID', $this->input->post('appID'));
              return $this->db->update('hris_applications', $data);
      }

      public function apfile_rploi_update(){

          $file = $this->upload->data();
          $filename = $file['file_name']; 

          $data = array(
              'rploi' => $filename
              );

              // $this->db->where('empEmail', $this->input->post('empEmail'));
              // $this->db->where('jobID', $this->input->post('jobID'));
              // $this->db->where('pre_school', $this->input->post('school_id'));
              $this->db->where('appID', $this->input->post('appID'));
              return $this->db->update('hris_applications', $data);
      }

      public function apfile_update_staff(){

        $file = $this->upload->data();
        $filename = $file['file_name']; 

        $data = array(
            'application' => $filename
            );

            // $this->db->where('empEmail', $this->input->post('id'));
            // $this->db->where('jobID', $this->input->post('jobID'));
            // $this->db->where('pre_school', $this->input->post('school_id'));
             $this->db->where('appID', $this->input->post('appID'));
      return $this->db->update('hris_applications', $data);
    }

      public function voters_update(){

        $file = $this->upload->data();
        $filename = $file['file_name']; 

        $data = array(
            'voters' => $filename
            );

            $this->db->where('empEmail', $this->input->post('empEmail'));
            //$this->db->where('jobID', $this->input->post('jobID'));
        return $this->db->update('hris_applicant', $data);
    }

    public function voters_update_staff(){

      $file = $this->upload->data();
      $filename = $file['file_name']; 

      $data = array(
          'voters' => $filename
          );

          $this->db->where('IDNumber', $this->input->post('id'));
          //$this->db->where('jobID', $this->input->post('jobID'));
      return $this->db->update('hris_staff', $data);
  }

	  public function pdsfile_update(){

        $file = $this->upload->data();
        $filename = $file['file_name']; 

        $data = array(
            'pdsfile' => $filename
            );

            $this->db->where('empEmail', $this->input->post('empEmail'));
            //$this->db->where('jobID', $this->input->post('jobID'));
      return $this->db->update('hris_applicant', $data);
    }

    public function pdsfile_update_staff(){

        $file = $this->upload->data();
        $filename = $file['file_name']; 

        $data = array(
            'pdsfile' => $filename
            );

            $this->db->where('IDNumber', $this->input->post('id'));
            //$this->db->where('jobID', $this->input->post('jobID'));
      return $this->db->update('hris_staff', $data);
    }

	  public function oafile_update(){

        $file = $this->upload->data();
        $filename = $file['file_name']; 

        $data = array(
            'oafile' => $filename
            );

            $this->db->where('empEmail', $this->input->post('empEmail'));
            // $this->db->where('jobID', $this->input->post('jobID'));
      return $this->db->update('hris_applicant', $data);
    }

    public function oafile_update_staff(){

        $file = $this->upload->data();
        $filename = $file['file_name']; 

        $data = array(
            'oafile' => $filename
            );

            $this->db->where('IDNumber', $this->input->post('id'));
            // $this->db->where('jobID', $this->input->post('jobID'));
      return $this->db->update('hris_staff', $data);
    }

    public function outfile_update(){

        $file = $this->upload->data();
        $filename = $file['file_name']; 

        $data = array(
            'oa' => $filename
            );

            $this->db->where('empEmail', $this->input->post('empEmail'));
            // $this->db->where('jobID', $this->input->post('jobID'));
      return $this->db->update('hris_applicant', $data);
    }

  

	  public function ipcrffile_update(){

        $file = $this->upload->data();
        $filename = $file['file_name']; 

        $data = array(
            'ipcrffile' => $filename
            );

            $this->db->where('empEmail', $this->input->post('empEmail'));
            // $this->db->where('jobID', $this->input->post('jobID'));
      return $this->db->update('hris_applicant', $data);
    }

    public function ipcrffile_update_staff(){

        $file = $this->upload->data();
        $filename = $file['file_name']; 

        $data = array(
            'ipcrffile' => $filename
            );

            $this->db->where('IDNumber', $this->input->post('id'));
            // $this->db->where('jobID', $this->input->post('jobID'));
      return $this->db->update('hris_staff', $data);
    }

    public function ppstco_update_staff(){

        $file = $this->upload->data();
        $filename = $file['file_name']; 

        $data = array(
            'ppstco' => $filename
            );

            $this->db->where('IDNumber', $this->input->post('id'));
            // $this->db->where('jobID', $this->input->post('jobID'));
        return $this->db->update('hris_staff', $data);
    }

    public function ppstpa_update_staff(){

        $file = $this->upload->data();
        $filename = $file['file_name']; 

        $data = array(
            'ppstpa' => $filename
            );

            $this->db->where('IDNumber', $this->input->post('id'));
            // $this->db->where('jobID', $this->input->post('jobID'));
        return $this->db->update('hris_staff', $data);
    }

    public function ap_submit(){
      date_default_timezone_set('Asia/Manila');
      $date = date('Y-m-d');
      $t = date('h:i:s a', time());

      $data = array( 
              'jobID' => $this->input->post('id'), 
              'empEmail' => $this->session->username, 
              'dateSubmitted' => $date, 
              'appStatus' => 'Application Submitted', 
              'applicant_id' => $this->session->c_id,
              'app_year' => date('Y'),
              'district' => $this->input->post('district'), 
              'pre_school' => $this->input->post('school')
      );
  
      return $this->db->insert('hris_applications', $data);
    }
    public function ap_submit_non_teaching(){
      date_default_timezone_set('Asia/Manila');
      $date = date('Y-m-d');
      $t = date('h:i:s a', time());

      $data = array( 
              'jobID' => $this->uri->segment(3), 
              'empEmail' => $this->session->username, 
              'dateSubmitted' => $date, 
              'appStatus' => 'Application Submitted', 
              'applicant_id' => $this->session->c_id,
              'app_year' => date('Y'),
              'district' => 'School Division Office', 
              'pre_school' => '202401'
      );
  
      return $this->db->insert('hris_applications', $data);
    }

    public function edit_submit(){

      $data = array( 
              'district' => $this->input->post('district'), 
              'pre_school' => $this->input->post('school')
      );
  
      $this->db->where('empEmail', $this->session->username);
      $this->db->where('jobID', $this->input->post('id'));
      return $this->db->update('hris_applications', $data);
    }

    public function ap_change_stat($status){

      $data = array( 
              'appStatus' => $status
      );

      $this->db->where('applicant_id', $this->uri->segment(3));
      $this->db->where('jobID', $this->uri->segment(4));
      return $this->db->update('hris_applications', $data);
    }

    public function ap_change_stat_all_application($status){

      $data = array( 
              'appStatus' => $status,
              'dq' => 1
      );

      $this->db->where('applicant_id', $this->uri->segment(3));
      $this->db->where('app_year', date('Y'));
      return $this->db->update('hris_applications', $data);
    }

    // public function ap_change_stat($status){

    //   $data = array( 
    //           'appStatus' => $status
    //   );

    //   $this->db->where('applicant_id', $this->uri->segment(3));
    //   $this->db->where('jobID', $this->uri->segment(4));
    //   return $this->db->update('hris_applications', $data);
    // }

    public function ap_change_stat_all($status){

      $data = array( 
              'appStatus' => $status
      );

      $this->db->where('appStatus', 'Validated');
      $this->db->where('app_year', date('Y'));
      return $this->db->update('hris_applications', $data);
    }

    public function ap_change_stat_district($status){

      $data = array( 
              'appStatus' => $status
      );

      $this->db->where('district', $this->input->get('district'));
      $this->db->where('jobID', $this->uri->segment(4));
      $this->db->where('appStatus','Validated');
      return $this->db->update('hris_applications', $data);
    }

    public function ap_trackv4($status){
      date_default_timezone_set('Asia/Manila');
      $date = date('Y-m-d');
      $t = date('h:i:s a', time());

      $data = array( 
              'jobID' => $this->uri->segment(4), 
              'dateSubmitted' => $date, 
              'appStatus' => $status, 
              'timeSubmitted' => $t,
              'applicant_id' => $this->uri->segment(3),
              'res' => $this->session->username,
              'app_id' => $this->uri->segment(6)
      );
  
      return $this->db->insert('hris_applications_track', $data);
    }
    

    public function ap_track($status){
      date_default_timezone_set('Asia/Manila');
      $date = date('Y-m-d');
      $t = date('h:i:s a', time());

      if($this->session->position != 'reg'){
        $jobid = $this->uri->segment(4);
      }else{
        $jobid = $this->input->post('id');
      }

      $data = array( 
              'jobID' => $jobid, 
              //'empEmail' => $this->session->username, 
              'dateSubmitted' => $date, 
              'appStatus' => $status, 
              'timeSubmitted' => $t,
              'applicant_id' => $this->uri->segment(3),
              'res' => $this->session->username,
              'app_id' => $this->uri->segment(6)
      );
  
      return $this->db->insert('hris_applications_track', $data);
    }

    public function ap_trackv3($status){
      date_default_timezone_set('Asia/Manila');
      $date = date('Y-m-d');
      $t = date('h:i:s a', time());

      if($this->session->position != 'reg'){
        $jobid = $this->uri->segment(4);
      }else{
        $jobid = $this->input->post('id');
      }

      $data = array( 
              'jobID' => $jobid, 
              //'empEmail' => $this->session->username, 
              'dateSubmitted' => $date, 
              'appStatus' => $status, 
              'timeSubmitted' => $t,
              'applicant_id' => $this->uri->segment(3),
              'res' => $this->session->username,
              'app_id' => $this->uri->segment(6)
      );
  
      return $this->db->insert('hris_applications_track', $data);
    }

    public function ap_trackv2($status){
      date_default_timezone_set('Asia/Manila');
      $date = date('Y-m-d');
      $t = date('h:i:s a', time());

      $data = array( 
              'jobID' => $this->input->post('jobID'),
              'dateSubmitted' => $date, 
              'appStatus' => $status, 
              'timeSubmitted' => $t,
              'applicant_id' => $this->uri->segment(3),
              'res' => $this->session->username,
              'app_id' => $this->input->post('appID')
      );
  
      return $this->db->insert('hris_applications_track', $data);
    }


    public function ap_track_apply($status,$app_id){
      date_default_timezone_set('Asia/Manila');
      $date = date('Y-m-d');
      $t = date('h:i:s a', time());

      if($this->session->position != 'reg'){
        $jobid = $this->uri->segment(4);
      }else{
        $jobid = $this->input->post('id');
      }

      $data = array( 
              'jobID' => $jobid, 
              //'empEmail' => $this->session->username, 
              'dateSubmitted' => $date, 
              'appStatus' => $status, 
              'timeSubmitted' => $t,
              'applicant_id' => $this->uri->segment(3),
              'res' => $this->session->username,
              'app_id' => $app_id
      );
  
      return $this->db->insert('hris_applications_track', $data);
    }

    public function ap_track_apply_user($status,$app_id){
      date_default_timezone_set('Asia/Manila');
      $date = date('Y-m-d');
      $t = date('h:i:s a', time());

      $jobid = $this->input->post('id');

      $data = array( 
              'jobID' => $jobid, 
              //'empEmail' => $this->session->username, 
              'dateSubmitted' => $date, 
              'appStatus' => $status, 
              'timeSubmitted' => $t,
              'applicant_id' => $this->uri->segment(3),
              'res' => $this->session->username,
              'app_id' => $app_id
      );
  
      return $this->db->insert('hris_applications_track', $data);
    }

    public function ap_track_apply_user_none_teaching($status,$app_id,$jobid){
      date_default_timezone_set('Asia/Manila');
      $date = date('Y-m-d');
      $t = date('h:i:s a', time());

      //$jobid = $this->input->post('id');

      $data = array( 
              'jobID' => $jobid, 
              //'empEmail' => $this->session->username, 
              'dateSubmitted' => $date, 
              'appStatus' => $status, 
              'timeSubmitted' => $t,
              'applicant_id' => $this->uri->segment(3),
              'res' => $this->session->username,
              'app_id' => $app_id
      );
  
      return $this->db->insert('hris_applications_track', $data);
    }

    

    public function ap_track_comment($status){
      date_default_timezone_set('Asia/Manila');
      $date = date('Y-m-d');
      $t = date('h:i:s a', time());

      
      $data = array( 
              'jobID' => $this->uri->segment(4),
              //'empEmail' => $this->session->username, 
              'dateSubmitted' => $date, 
              'appStatus' => $status, 
              'timeSubmitted' => $t,
              'applicant_id' => $this->uri->segment(3),
              'res' => $this->session->username,
              'app_id' => $this->input->post('app_id')
      );
  
      return $this->db->insert('hris_applications_track', $data);
    }

    public function app_inquiry(){
      date_default_timezone_set('Asia/Manila');
      $date = date('Y-m-d');
      $t = date('h:i:s a', time());

      
      $data = array( 
              'inquiry' => $this->input->post('comment'), 
              'res' => $this->session->username,
              'idate' => $date, 
              'application_id' => $this->input->post('app_id'), 
              'applicant_id' => $this->uri->segment(3),
              'job_id' => $this->uri->segment(4),
              'timeSubmitted' => $t,
              
      );
  
      return $this->db->insert('hris_application_inquiry', $data);
    }


    public function close_jv(){


      $data = array(
          'jvStatus' => 'Closed'
          );

      $this->db->where('jobID', $this->uri->segment(3));
      return $this->db->update('hris_jobvacancy', $data);
    }

    public function remove_attach($column){

      $data = array(
          $column => ''
          );

      $this->db->where('id', $this->uri->segment(3));
      return $this->db->update('hris_applicant', $data);
    }

    public function remove_attach_staff($column){

      $data = array(
          $column => ''
          );

      $this->db->where('IDNumber', $this->uri->segment(3));
      return $this->db->update('hris_staff', $data);
    }

    public function remove_attach_app($column){

      $data = array(
          $column => ''
          );

      $this->db->where('appID', $this->uri->segment(7));
      return $this->db->update('hris_applications', $data);
    }

    public function open_jv(){


      $data = array(
          'jvStatus' => 'Open'
          );

      $this->db->where('jobID', $this->uri->segment(3));
      return $this->db->update('hris_jobvacancy', $data);
    }

    public function insert_rate($gt, $fy){

      $data = array(
          'record_no' => $this->uri->segment(5), 
          'appID' => $this->uri->segment(6),
          'education' => .00001, 
          'training' => .00001, 
          'experience' => .00001, 
          'let_rating' => .00001, 
          'demo_rating' => .00001, 
          'tr_rating' => .00001,
          'job_type' => $gt,
          'fy' => $fy,
          
      );

      return $this->db->insert('hris_applications_rating', $data);
    }

    public function insert_rate_none(){

      $data = array(
          'educ' => .00001, 
          'trainings' => .00001, 
          'experience' => .00001, 
          'performance' => .00001, 
          'oa' => .00001, 
          'ae' => .00001, 
          'ald' => .00001, 
          'interview' => .00001, 
          'written' => .00001, 
          'record_no' => $this->input->post('record'),
          'appID' => $this->input->post('appID'),
          'job_type' => $this->input->post('job_type'),
          'fy' => $this->input->post('job_fy'),
          
      );

      return $this->db->insert('hris_rating_none', $data);
    }

    public function insert_rate_promotion(){

      $data = array(
          'educ' => .00001, 
          'trainings' => .00001, 
          'experience' => .00001, 
          'performance' => .00001, 
          'ppstco' => .00001, 
          'ppstpa' => .00001,
          'record_no' => $this->input->post('record'),
          'appID' => $this->input->post('appID'),
          'job_type' => $this->input->post('job_type'),
          'fy' => $this->input->post('job_fy'),
          
      );

      return $this->db->insert('hris_rating_promotion', $data);
    }

    public function trspecialupdate(){


      $data = array( 
          'tr_rating' => $this->uri->segment(3),
          'eval_id3' => $this->session->id
          
      );
      $this->db->where('fy',$this->uri->segment(4));
      $this->db->where('record_no',$this->uri->segment(5));
      $this->db->where('tr_rating','0.00001');
      return $this->db->update('hris_applications_rating', $data);
    }

    public function insert_exp_rate(){

      $record = $this->input->post('record_no');
      $appId  = $this->input->post('app_id');

      $data = array(
          'education' => .1,
          'training' => .1,
          'experience' => .1,
          'let_rating' => .1,
          'demo_rating' => $this->input->post('demo_rating'),
          'tr_rating' => .1,
          'eval_id2' => $this->session->id
      );

      $exists = $this->db->get_where('hris_applications_rating', [
          'record_no' => $record,
          'appID' => $appId
      ])->row();

      if ($exists) {
          return $this->db->where('record_no', $record)->where('appID', $appId)->update('hris_applications_rating', $data);
      }

      $data['record_no'] = $record;
      $data['appID'] = $appId;
      return $this->db->insert('hris_applications_rating', $data);
    }

    public function insert_tr_rate(){

      $record = $this->input->post('record_no');
      $appId  = $this->input->post('app_id');

      $data = array(
          'education' => .1,
          'training' => .1,
          'experience' => .1,
          'let_rating' => .1,
          'demo_rating' => .1,
          'tr_rating' => $this->input->post('demo_rating'),
          'eval_id3' => $this->session->id
      );

      $exists = $this->db->get_where('hris_applications_rating', [
          'record_no' => $record,
          'appID' => $appId
      ])->row();

      if ($exists) {
          return $this->db->where('record_no', $record)->where('appID', $appId)->update('hris_applications_rating', $data);
      }

      $data['record_no'] = $record;
      $data['appID'] = $appId;
      return $this->db->insert('hris_applications_rating', $data);
    }

    public function update_rate($educ){


      $data = array(
          $educ => $this->input->post($educ)
      );


      $this->db->where('appID', $this->input->post('app_id'));
      $this->db->where('record_no', $this->input->post('record_no'));
      return $this->db->update('hris_applications_rating', $data);
    }

    public function update_rate_none($educ){


      $data = array(
          $educ => $this->input->post($educ),
      );


      $this->db->where('appID', $this->input->post('app_id'));
      $this->db->where('record_no', $this->input->post('record_no'));
      return $this->db->update('hris_rating_none', $data);
    }

    public function update_rate_promotion($educ){


      $data = array(
          $educ => $this->input->post($educ),
      );


      $this->db->where('appID', $this->input->post('app_id'));
      $this->db->where('record_no', $this->input->post('record_no'));
      return $this->db->update('hris_rating_promotion', $data);
    }

    public function lock_application($val){

      $data = array(
          'stat' => $val
      );

      //$this->db->where('jobID', $this->uri->segment(3));
      return $this->db->update('hris_applicant', $data);
    }

    public function lock_applicant_document_submission($table,$val){

      $data = array(
          'stat' => $val
      );

      //$this->db->where('jobID', $this->uri->segment(3));
      return $this->db->update($table, $data);
    }

    public function lock_applicant_document_submission_by_job($jobID,$val){

      $jobID = (int) $jobID;
      if ($jobID <= 0) {
        return false;
      }

      $data = array(
          'stat' => $val
      );

      $this->db->where('jobID', $jobID);
      return $this->db->update('hris_applications', $data);
    }

    

    public function update_dq($val){

      $data = array(
          'dq' => $val
      );

      $this->db->where('appID', $this->input->post('appID'));
      return $this->db->update('hris_applications', $data);
    }

    public function online_demo_update($appID, $demoLink)
    {
      $data = array(
        'online_demo' => $demoLink
      );

      $this->db->where('appID', (int) $appID);
      return $this->db->update('hris_applications', $data);
    }

    public function insert_dq(){
      date_default_timezone_set('Asia/Manila');
      $date = date('Y-m-d');
      $t = date('h:i:s a', time());
      $li = ($this->input->post('li') == '') ? 0 : 1;
      $da_pds = ($this->input->post('da_pds') == '') ? 0 : 1;
      $prc = ($this->input->post('prc') == '') ? 0 : 1;
      $trbd = ($this->input->post('trbd') == '') ? 0 : 1;
      $omni = ($this->input->post('omni') == '') ? 0 : 1;

      $educ = ($this->input->post('educ') == '') ? 0 : 1;
      $exp = ($this->input->post('exp') == '') ? 0 : 1;
      $tr = ($this->input->post('tr') == '') ? 0 : 1;
      $eli = ($this->input->post('eli') == '') ? 0 : 1;

      $data = array( 
              'jobID' => $this->input->post('jobID'), 
              'appID' => $this->input->post('appID'), 
              'apID' => $this->input->post('id'), 
              'li' => $li, 
              'da_pds' => $da_pds, 
              'prc' => $prc, 
              'trbd' => $trbd, 
              'omni' => $omni, 
              'local' => $this->input->post('local'), 
              'remarks' => $this->input->post('remarks'), 
              'reason' => $this->input->post('reason'), 
              'vdate' => $date,
              'res' => $this->session->id,

              'educ' => $educ,
              'exp' => $exp,
              'tr' => $tr,
              'eli' => $eli,


      );
  
      return $this->db->insert('hris_app_dq', $data);
    }

    public function update_dq2(){
      date_default_timezone_set('Asia/Manila');
      $date = date('Y-m-d');
      $t = date('h:i:s a', time());
      $li = ($this->input->post('li') == '') ? 0 : 1;
      $da_pds = ($this->input->post('da_pds') == '') ? 0 : 1;
      $prc = ($this->input->post('prc') == '') ? 0 : 1;
      $trbd = ($this->input->post('trbd') == '') ? 0 : 1;
      $omni = ($this->input->post('omni') == '') ? 0 : 1;

      $data = array( 
              'jobID' => $this->input->post('jobID'), 
              'appID' => $this->input->post('appID'), 
              'apID' => $this->input->post('id'), 
              'li' => $li, 
              'da_pds' => $da_pds, 
              'prc' => $prc, 
              'trbd' => $trbd, 
              'omni' => $omni, 
              'local' => $this->input->post('local'), 
              'remarks' => $this->input->post('remarks'), 
              'reason' => $this->input->post('reason'), 
              'vdate' => $date,
              'res' => $this->session->id
      );
  
      $this->db->where('id', $this->input->post('dq_id'));
      return $this->db->update('hris_app_dq', $data);
    }

    

    public function lock_applications($val){

      $data = array(
          'stat' => $val
      );

      //$this->db->where('jobID', $this->uri->segment(3));
      return $this->db->update('hris_applications', $data);
    }

    public function applicant_stat($val){

      $data = array(
          'a_stat' => $val
      );

      $this->db->where('jobID', $this->uri->segment(3));
      return $this->db->update('hris_jobvacancy', $data);
    }

    public function application_close_open($val){

      $data = array(
          'stat' => $val
      );

      $this->db->where('jobID', $this->uri->segment(3));
      return $this->db->update('hris_applications', $data);
    }

    public function in_lock_application($val){

      $data = array(
          'stat' => $val
      );

      $this->db->where('jobID', $this->uri->segment(3));
      $this->db->where('empEmail', $this->input->get('ee'));
      return $this->db->update('hris_applications', $data);
    }

    

    public function lock_applicant($id) {

      $this->db->where('record_no', $id);
      $query = $this->db->get('hris_applicant');

      $data = array('stat' => 1);

      if ($query->num_rows() > 0) {
          return $this->db->update('hris_applicant', $data, ['record_no' => $id]);
      } else {
          return $this->db->update('hris_staff', $data, ['IDNumber' => $id]);
      }
  }

  public function unlock_applicant($id) {

      $this->db->where('record_no', $id);
      $query = $this->db->get('hris_applicant');

      $data = array('stat' => 0);

      if ($query->num_rows() > 0) {
          return $this->db->update('hris_applicant', $data, ['record_no' => $id]);
      } else {
          return $this->db->update('hris_staff', $data, ['IDNumber' => $id]);
      }
  }


  public function unlock_hris_staff(){

      $data = array(
          'stat' => 0
      );

      $this->db->where('IDNumber', $this->session->username);
      return $this->db->update('hris_staff', $data);
    }
 

    public function lock_staff($val){

      $data = array(
          'stat' => $val
      );

      $this->db->where('jobID', $this->uri->segment(3));
      $this->db->where('empEmail', $this->input->get('ee'));
      return $this->db->update('hris_applications', $data);
    }

    public function applicant_status($val){

      $data = array(
          'stat' => $val
      );

      $this->db->where('empEmail', $this->input->get('ee'));
      return $this->db->update('hris_applicant', $data);
    }

    public function staff_status($val){

      $data = array(
          'stat' => $val
      );

      //$this->db->where('empEmail', $this->input->get('ee'));
      return $this->db->update('hris_staff', $data);
    }

    public function application_status($val){

      $data = array(
          'stat' => $val
      );

      $this->db->where('empEmail', $this->input->get('ee'));
      return $this->db->update('hris_applications', $data);
    }

    

    public function update_application_district(){

      $data = array(
          'district' => $this->input->post('district'),
          'pre_school' => $this->input->post('school')
      );

      $this->db->where('appID', $this->input->post('appID'));
      return $this->db->update('hris_applications', $data);
    }

    public function update_application_district_blank(){

      $data = array(
          'district' => '',
          'pre_school' => ''
      );

      $this->db->where('appID', $this->uri->segment(5));
      return $this->db->update('hris_applications', $data);
    }

    public function in_applicant_stat($val){

      $data = array(
          'a_stat' => $val
      );

      $this->db->where('jobID', $this->uri->segment(3));
      $this->db->where('empEmail', $this->input->get('ee'));
      return $this->db->update('hris_jobvacancy', $data);
    }

    public function insert_job(){

      $file = $this->upload->data();
      $filename = $file['file_name']; 

      date_default_timezone_set('Asia/Manila'); # add your city to set local time zone
			$now = date('H:i:s A');
			$date = date("Y-m-d");

      if($this->input->post('job_type') == 0){
        $jt = $this->input->post('job_type1');
      }else{
        $jt = $this->input->post('job_type');
      }

      $data = array(
          'jobTitle' => $this->input->post('jobTitle'), 
          'empType' => $this->input->post('empType'), 
          'file' => $filename,
          'postedBy'=> $this->session->username, 
          'datePosted'=> $date, 
          'jvStatus'=> 'Open',  
          'sy'=> $this->input->post('sy'),
          'job_type'=> $jt,
          'position'=> $this->input->post('position'),
          'assign'=> $this->input->post('assign'),
          'promotion'=> $this->input->post('ecp'),
          'position_id'=> $this->input->post('position_id')
          );
      return $this->db->insert('hris_jobvacancy', $data);
    }

    public function edit_jobvacancy(){

      $data = array(
          'jobTitle' => $this->input->post('jobTitle'), 
          'empType' => $this->input->post('empType'), 
          'sy' => $this->input->post('sy'),
          'job_type' => $this->input->post('job_type'),
          'position'=> $this->input->post('position'),
          'assign'=> $this->input->post('assign')
      );

      $this->db->where('jobID', $this->input->post('jobID'));
      return $this->db->update('hris_jobvacancy', $data);
    }

    public function doc_update(){

      $file = $this->upload->data();
      $filename = $file['file_name']; 

      $data = array(
          'file' => $filename
          );

          $this->db->where('jobID', $this->input->post('jobID'));
      return $this->db->update('hris_jobvacancy', $data);
    }

    public function update_eval($eval){


      $data = array(
          $eval => $this->session->id
      );


      $this->db->where('appID', $this->input->post('app_id'));
      $this->db->where('record_no', $this->input->post('record_no'));
      return $this->db->update('hris_applications_rating', $data);
    }

    public function update_eval_none($eval){


      $data = array(
          $eval => $this->session->id
      );


      $this->db->where('appID', $this->input->post('app_id'));
      $this->db->where('record_no', $this->input->post('record_no'));
      return $this->db->update('hris_rating_none', $data);
    }

     public function update_eval_promotion($eval){


      $data = array(
          $eval => $this->session->id
      );


      $this->db->where('appID', $this->input->post('app_id'));
      $this->db->where('record_no', $this->input->post('record_no'));
      return $this->db->update('hris_rating_promotion', $data);
    }

    public function notifychange(){
      $data = array(
          'nstat' => 1
      );


      $this->db->where('applicant_id', $this->uri->segment(3));
      $this->db->where('jobID',  $this->uri->segment(4));
      $this->db->where('res !=',  $this->session->username);
      $this->db->where('nstat',  0);
      return $this->db->update('hris_applications_track', $data);
    }

    public function notifychangeadmin(){
      $data = array(
          'nstat' => 1
      );


      $this->db->where('applicant_id', $this->uri->segment(3));
      $this->db->where('jobID',  $this->uri->segment(4));
      $this->db->where('res',  $this->input->get('empEmail'));
      $this->db->where('nstat',  0);
      return $this->db->update('hris_applications_track', $data);
    }

    public function new_document(){

      $data = array(
          'name' => $this->input->post('name'), 
          'doc_type' => $this->input->post('doc_type'), 
          'doc_des' => $this->input->post('doc_des'), 
          'doc_no' => $this->input->post('doc_no'), 
          'rdate' => $this->input->post('rdate')

      );

      return $this->db->insert('document_verifier', $data);
    }


  // sub users

  public function insert_sub_user(){

        $password = $this->input->post('password');
        $hash = password_hash($password, PASSWORD_DEFAULT);


        $data = array(
            'username' => $this->input->post('username'),
            'Password' => $hash,
            'position' => $this->input->post('position'),
            'fname' => $this->input->post('fname'),
            'mname' => $this->input->post('mname'),
            'lname' => $this->input->post('lname'),
            'address' => $this->input->post('address'),
            'sex' => $this->input->post('sex'),
            'user_id' => $this->session->c_id, 
            'd_id' => $this->input->post('district'),
            'sp' => $this->input->post('sp')

        );

        return $this->db->insert('users', $data);
  }

  public function update_sub_user(){


    $data = array(
        'username' => $this->input->post('username'),
        'fname' => $this->input->post('fname'),
        'mname' => $this->input->post('mname'),
        'lname' => $this->input->post('lname'),
        'address' => $this->input->post('address'),
        'sex' => $this->input->post('sex'),
        'sp' => $this->input->post('sp')

    );

    $this->db->where('id', $this->input->post('id'));
    return $this->db->update('users', $data);
  }


  public function sp_position_insert(){

    $data = array( 
            'position' => $this->input->post('position'), 
            'main_position' => $this->input->post('mp')
    );

    return $this->db->insert('users_sp', $data);
  }

  public function sp_position_update(){

    $data = array( 
            'position' => $this->input->post('position'), 
            'main_position' => $this->input->post('mp')
    );

    $this->db->where('id', $this->input->post('id'));
    return $this->db->update('users_sp', $data);
  }

  public function insert_va(){

    $this->db->simple_query('INSERT INTO hris_applications_rating (record_no, appID, education, training, experience, let_rating, demo_rating, tr_rating)
    SELECT record_no, appID, 0.1, 0.1, 0.1, 0.1, 0.1, 0.1
    FROM hris_applications INNER JOIN hris_applicant ON hris_applications.applicant_id=hris_applicant.id where appStatus="Validated"');
    return true;
  }

  function random_password(){
    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $password = array(); 
    $alpha_length = strlen($alphabet) - 1; 
    for ($i = 0; $i < 8; $i++) 
    {
        $n = rand(0, $alpha_length);
        $password[] = $alphabet[$n];
    }
    return implode($password); 
}

  public function update_request_password(){
    
    if($this->input->post('at') == 1){
        $user = $this->Common->one_cond_row('users','username',$this->input->post('email'));
        $email = $this->input->post('email');
        $username = $this->input->post('email');
    }elseif($this->input->post('at') == 2){
        $user = $this->Common->one_cond_row('hris_staff','empEmail',$this->input->post('email'));
        $username = $user->IDNumber;
        $email = $user->empEmail;
    }else{
        $user = $this->Common->one_cond_row('schools','schoolEmail',$this->input->post('email'));
        $username = $user->schoolID;
        $email = $user->schoolEmail;
    }

    $password = $this->Reg->random_password();

    $fname = 'Maam/Sir';

                //Email Notification
                $this->load->config('email');
                $this->load->library('email');
                $mail_message = '
                <!doctype html>
                <html>
                <head>
                  <meta charset="utf-8">
                  <meta name="viewport" content="width=device-width,initial-scale=1">
                </head>
                <body style="margin:0; padding:0; background:#f3f5f7; font-family:Arial, Helvetica, sans-serif;">
                  <div style="padding:24px 12px;">
                    <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="max-width:640px; margin:0 auto; background:#ffffff; border-radius:14px; overflow:hidden; box-shadow:0 10px 30px rgba(15,23,42,.10);">
                      
                      <!-- Header -->
                      <tr>
                        <td style="background:linear-gradient(135deg,#2563eb,#7c3aed); padding:22px 26px; color:#ffffff;">
                          <div style="font-size:18px; font-weight:700; letter-spacing:.2px;">DepEd MIS - Online</div>
                          <div style="font-size:13px; opacity:.95; margin-top:4px;">Password Reset Notification</div>
                        </td>
                      </tr>

                      <!-- Body -->
                      <tr>
                        <td style="padding:26px;">
                          <div style="font-size:15px; color:#111827; line-height:1.6;">
                            <div style="font-size:16px; font-weight:700; margin-bottom:10px;">Dear '.$fname.',</div>

                            <p style="margin:0 0 14px 0;">
                              You have successfully reset your password. Please use the temporary password below to log in.
                            </p>

                            <div style="margin:18px 0; padding:16px; border:1px solid #e5e7eb; border-radius:12px; background:#f9fafb;">
                              <div style="font-size:12px; color:#6b7280; margin-bottom:6px;">Temporary Password</div>
                              <div style="font-size:20px; font-weight:800; color:#dc2626; letter-spacing:.8px;">'.$password.'</div>
                            </div>

                            <p style="margin:0 0 14px 0; color:#374151;">
                              For your security, please change your password immediately after logging in.
                            </p>

                            <div style="margin-top:18px; padding-top:16px; border-top:1px solid #e5e7eb; color:#111827;">
                              <div style="font-weight:700;">Thanks &amp; Regards,</div>
                              <div>DepEd MIS - Online</div>
                            </div>
                          </div>
                        </td>
                      </tr>

                      <!-- Footer -->
                      <tr>
                        <td style="padding:16px 26px; background:#f9fafb; color:#6b7280; font-size:12px; line-height:1.5;">
                          This email was generated automatically. If you did not request a password reset, please contact your system administrator immediately.
                        </td>
                      </tr>

                    </table>
                  </div>
                </body>
                </html>
                ';

                $this->email->from('no-reply@depeddavor.com', 'DepEd DavOr MIS')
                    ->to($email)
                    ->subject('Password Changed')
                    ->message($mail_message);
                $this->email->send();

    $hash = password_hash($password, PASSWORD_DEFAULT);

    $data = array(
        'Password' => $hash

    );

    $this->db->where('username', $username);
    return $this->db->update('users', $data);
}


public function aip_app_join(){

  $this->db->select('a.id,a.category,a.school_id,b.materials');
  $this->db->from('sgod_aip as a');
  $this->db->join('sgod_app as b', 'b.aip_id=a.id', 'left');
  $this->db->where('category','MINOR REPAIR');  
  $this->db->where('a.school_id','500417');  
  $this->db->group_by('b.materials');
  $this->db->order_by('b.materials','ASC');
  $query = $this->db->get(); 
  return $query->result();
}

function sbfp_upload($record)
	{

		if (count($record) > 0) {

			$data = array( 
        "fname" => trim($record[0]), 
        "mname" => trim($record[1]), 
        "lname" => trim($record[2]), 
        "lrn" => trim($record[3]), 
        "sy" => trim($record[4]), 
        "school_id" => trim($record[5]), 
        "dbirth" => trim($record[6]), 
        "w_kg" => trim($record[7]), 
        "h_m" => trim($record[8]), 
        "sex" => trim($record[9]), 
        "h_m2" => trim($record[10]), 
        "age_y" => trim($record[1]), 
        "age_m" => trim($record[12]), 
        "bmi" => trim($record[13]), 
        "nut_stat" => trim($record[14]), 
        "section" => trim($record[15]), 
        "pcfm" => trim($record[16]), 
        "p_4ps" => trim($record[17]), 
        "sbfp_in_prev" => trim($record[18]), 
        "dw" => trim($record[19]), 
        "cat_primary" => trim($record[20]), 
        "cat_second" => trim($record[21]), 
        "nut_stat_1_ns" => trim($record[22]), 
        "nut_stat_1_ha" => trim($record[23]), 
        "nut_stat_2_ns" => trim($record[24]), 
        "nut_stat_2_ha" => trim($record[25]), 
        "nut_stat_3_ns" => trim($record[26]), 
        "nut_stat_3_ha" => trim($record[27]), 
        "nut_stat_4_ns" => trim($record[28]), 
        "nut_stat_4_ha" => trim($record[29]), 
        "nut_stat_5_ns" => trim($record[30]), 
        "nut_stat_5_ha" => trim($record[31])
			);


			$this->db->insert('sbfp', $data);

			// }

		}
	}


  public function calculate_rating($fy){
    $this->db->query('update hris_applications_rating set total_points = education+training+experience+let_rating+demo_rating+tr_rating where fy='.$fy);
  }

  public function calculate_rating_promotion($fy){
    $this->db->query('update hris_rating_promotion set total_points = educ+trainings+experience+performance+ppstco+ppstpa where fy='.$fy);
  }

  public function calculate_rating_promotion_ies($appID){
    $this->db->query('update hris_rating_promotion set total_points = educ+trainings+experience+performance+ppstco+ppstpa where appID='.$appID);
  }

  public function calculate_rating_none($fy){
    $this->db->query('update hris_rating_none set total_points = educ+trainings+experience+performance+oa+ae+ald+interview+written+skills where fy='.$fy);
  }

  /**
   * Consolidate duplicate rating rows (same appID + fy) by keeping the max value per component,
   * rewriting a single row, and deleting the extras.
   */
  public function consolidate_rating_single($appId)
  {
      if (empty($appId)) return;

      // Determine FY from the application; fall back to current year
      $app = $this->db->get_where('hris_applications', ['appID' => $appId])->row();
      $fy  = $app->app_year ?? date('Y');

      $rows = $this->db->where('appID', $appId)
                       ->where('fy', $fy)
                       ->get('hris_applications_rating')
                       ->result();

      if (count($rows) <= 1) return; // nothing to merge

      $fields = ['education','training','experience','let_rating','demo_rating','tr_rating'];
      $maxVals = array_fill_keys($fields, 0.00001);
      $primaryId = $rows[0]->id ?? null;

      foreach ($rows as $r) {
          foreach ($fields as $f) {
              if (isset($r->$f) && (float)$r->$f > (float)$maxVals[$f]) {
                  $maxVals[$f] = (float)$r->$f;
              }
          }
          if ($primaryId === null && isset($r->id)) {
              $primaryId = $r->id;
          }
      }

      // Recompute total points from merged values
      $maxVals['total_points'] = array_sum($maxVals);

      if ($primaryId) {
          $this->db->where('id', $primaryId)->update('hris_applications_rating', $maxVals);
          // Remove the rest
          $this->db->where('appID', $appId)->where('fy', $fy)->where_not_in('id', [$primaryId])->delete('hris_applications_rating');
      }
  }

  /**
   * Automatically set application status to "Rated" when all rating components are filled
   * (i.e., none remain at the placeholder 0.00001).
   */
  public function auto_mark_rated($appId)
  {
      if (empty($appId)) return;

      $app = $this->db->get_where('hris_applications', ['appID' => $appId])->row();
      if (!$app) return;

      $fy = $app->app_year ?? date('Y');

      $rating = $this->db->where('appID', $appId)
                         ->where('fy', $fy)
                         ->get('hris_applications_rating')
                         ->row();
      if (!$rating) return;

      $fields = ['education','training','experience','let_rating','demo_rating','tr_rating'];
      foreach ($fields as $f) {
          if (!isset($rating->$f) || (float)$rating->$f == 0.00001) {
              return; // still incomplete; do nothing
          }
      }

      // All components present: ensure total_points is up-to-date
      $total = 0;
      foreach ($fields as $f) {
          $total += (float)$rating->$f;
      }
      $this->db->where('appID', $appId)->where('fy', $fy)->update('hris_applications_rating', ['total_points' => $total]);

      // Only promote the workflow from the rating stage; never overwrite
      // applicant actions like "Confirmed" during a page refresh.
      if ($app->appStatus === 'Endorsed for Rating') {
          $this->db->where('appID', $appId)->update('hris_applications', ['appStatus' => 'Rated']);
      }
  }

  public function lock_applicant_application($table, $jobid,$emp){
    $this->db->set('stat', 1)
             ->where('EXISTS (SELECT 1 FROM hris_applications WHERE hris_applications.empEmail = '.$table.'.'.$emp.' AND hris_applications.jobID = '.$jobid.')', null, false)
             ->update($table);
  }



  public function update_aq(){

    $data = array(
      'stat' => 1, 
    );

    $this->db->where('application_id', $this->uri->segment(3));
    return $this->db->update('hris_application_inquiry', $data);
  }

  public function rrall(){

      date_default_timezone_set('Asia/Manila');
      $date = date('Y-m-d');
      $fy = date('Y');

    $data = array(
        'rdate' => $date, 
        'job_id' => $this->uri->segment(4), 
        'app_id' =>$this->uri->segment(5), 
        'stat' => 0, 
        'applicant_id' =>  $this->uri->segment(3), 
        'r_type' => $this->uri->segment(6),
        'fy' => $fy,
        'p_type' => $this->uri->segment(9)

    );

    return $this->db->insert('hris_rating_request', $data);
  }

  /**
   * Copy full rating from a previous application into the target app.
   * Upsert behaviour: update existing row for target appID, insert when missing.
   */
  public function copy_rating($record_no, $appID) {
    $result = $this->db->select('record_no, appID, education, training, experience, let_rating, demo_rating, tr_rating, total_points, eval_id1, eval_id2, eval_id3, job_type, fy')
                      ->from('hris_applications_rating')
                      ->where('record_no', $record_no)
                      ->where('appID', $appID)
                      ->get()
                      ->row_array();

    if (!$result) {
        return false;
    }

    $targetAppId = $this->input->post('app_id');

    // Preserve existing FY when updating; only set FY when inserting a fresh row.
    $existing = $this->db->get_where('hris_applications_rating', ['appID' => $targetAppId])->row_array();

    $baseData = array(
        'record_no'    => $result['record_no'],
        'appID'        => $targetAppId,
        'education'    => $result['education'],
        'training'     => $result['training'],
        'experience'   => $result['experience'],
        'let_rating'   => $result['let_rating'],
        'demo_rating'  => $result['demo_rating'],
        'tr_rating'    => $result['tr_rating'],
        'total_points' => $result['total_points'],
        'eval_id1'     => $result['eval_id1'],
        'eval_id2'     => $result['eval_id2'],
        'eval_id3'     => $result['eval_id3'],
        'job_type'     => $result['job_type']
    );

    $targetFy = $existing['fy'] ?? $result['fy'] ?? null;
    if ($targetFy === null) {
        $targetFy = date('Y');
    }

    if ($existing) {
        // Do not overwrite the fiscal year when retaining ratings
        return $this->db->where('appID', $targetAppId)->update('hris_applications_rating', $baseData);
    }

    $baseData['fy'] = $targetFy;
    return $this->db->insert('hris_applications_rating', $baseData);
  }

  /**
   * Copy validation / mandatory document checklist (hris_app_dq) from a source
   * application into a target application. Upsert behaviour: update when the
   * target already has a row, insert otherwise.
   */
  public function copy_dq($sourceAppId, $targetAppId, $jobID = null)
{
    $sourceAppId = trim($sourceAppId);
    $targetAppId = trim($targetAppId);
    $jobID       = trim((string)$jobID);

    if (empty($sourceAppId) || empty($targetAppId)) {
        log_message('error', 'copy_dq failed: sourceAppId or targetAppId is empty.');
        return false;
    }

    /*
     * Fallback: if jobID is empty, try to get it from the target application first,
     * then from the source application.
     */
    if ($jobID === '') {
        $targetApp = $this->db
            ->select('appID, job_id, jobID')
            ->from('hris_applications')
            ->where('appID', $targetAppId)
            ->get()
            ->row();

        if ($targetApp) {
            if (isset($targetApp->jobID) && !empty($targetApp->jobID)) {
                $jobID = $targetApp->jobID;
            } elseif (isset($targetApp->job_id) && !empty($targetApp->job_id)) {
                $jobID = $targetApp->job_id;
            }
        }
    }

    if ($jobID === '') {
        $sourceApp = $this->db
            ->select('appID, job_id, jobID')
            ->from('hris_applications')
            ->where('appID', $sourceAppId)
            ->get()
            ->row();

        if ($sourceApp) {
            if (isset($sourceApp->jobID) && !empty($sourceApp->jobID)) {
                $jobID = $sourceApp->jobID;
            } elseif (isset($sourceApp->job_id) && !empty($sourceApp->job_id)) {
                $jobID = $sourceApp->job_id;
            }
        }
    }

    if ($jobID === '') {
        log_message('error', 'copy_dq failed: jobID is still empty. sourceAppId=' . $sourceAppId . ', targetAppId=' . $targetAppId);
        return false;
    }

    // Get source DQ row
    $sourceDQ = $this->db
        ->where('appID', $sourceAppId)
        ->order_by('id', 'DESC')
        ->get('hris_app_dq')
        ->row();

    if (!$sourceDQ) {
        log_message('error', 'copy_dq failed: no source DQ found for appID=' . $sourceAppId);
        return false;
    }

    // Optional: prevent duplicate target DQ row
    $existing = $this->db
        ->where('appID', $targetAppId)
        ->where('jobID', $jobID)
        ->get('hris_app_dq')
        ->row();

    if ($existing) {
        // Update existing row instead of inserting duplicate
        $updateData = array(
            'li'      => $sourceDQ->li,
            'da_pds'  => $sourceDQ->da_pds,
            'prc'     => $sourceDQ->prc,
            'trbd'    => $sourceDQ->trbd,
            'omni'    => $sourceDQ->omni,
            'local'   => $sourceDQ->local,
            'remarks' => $sourceDQ->remarks,
            'reason'  => $sourceDQ->reason,
            'vdate'   => $sourceDQ->vdate,
            'res'     => $sourceDQ->res,
            'educ'    => $sourceDQ->educ,
            'exp'     => $sourceDQ->exp,
            'tr'      => $sourceDQ->tr,
            'eli'     => $sourceDQ->eli,
            'fy'      => $sourceDQ->fy
        );

        $this->db->where('id', $existing->id);
        return $this->db->update('hris_app_dq', $updateData);
    }

    // Insert new DQ row
    $insertData = array(
        'jobID'   => $jobID,
        'appID'   => $targetAppId,
        'apID'    => $sourceAppId,
        'li'      => $sourceDQ->li,
        'da_pds'  => $sourceDQ->da_pds,
        'prc'     => $sourceDQ->prc,
        'trbd'    => $sourceDQ->trbd,
        'omni'    => $sourceDQ->omni,
        'local'   => $sourceDQ->local,
        'remarks' => $sourceDQ->remarks,
        'reason'  => $sourceDQ->reason,
        'vdate'   => $sourceDQ->vdate,
        'res'     => $sourceDQ->res,
        'educ'    => $sourceDQ->educ,
        'exp'     => $sourceDQ->exp,
        'tr'      => $sourceDQ->tr,
        'eli'     => $sourceDQ->eli,
        'fy'      => $sourceDQ->fy
    );

    return $this->db->insert('hris_app_dq', $insertData);
}

  /**
   * Copy only demo/TR/LET ratings (limited retention). Upsert on target appID.
   */
  public function copy_limited_rating($record_no, $appID) {
    $result = $this->db->select('record_no, appID, education, training, experience, let_rating, demo_rating, tr_rating, total_points, eval_id1, eval_id2, eval_id3, job_type, fy')
                      ->from('hris_applications_rating')
                      ->where('record_no', $record_no)
                      ->where('appID', $appID)
                      ->get()
                      ->row_array();

    if (!$result) {
        return false;
    }

    $targetAppId = $this->input->post('app_id');

    // Preserve existing FY when updating; only set FY when inserting a fresh row.
    $existing = $this->db->get_where('hris_applications_rating', ['appID' => $targetAppId])->row_array();

    $baseData = array(
        'record_no'    => $result['record_no'],
        'appID'        => $targetAppId,
        'education'    => 0.00001,
        'training'     => 0.00001,
        'experience'   => 0.00001,
        'let_rating'   => $result['let_rating'],
        'demo_rating'  => $result['demo_rating'],
        'tr_rating'    => $result['tr_rating'],
        'total_points' => $result['total_points'],
        'eval_id1'     => 0,
        'eval_id2'     => $result['eval_id2'],
        'eval_id3'     => $result['eval_id3'],
        'job_type'     => $result['job_type']
    );

    $targetFy = $existing['fy'] ?? $result['fy'] ?? null;
    if ($targetFy === null) {
        $targetFy = date('Y');
    }

    if ($existing) {
        // Do not overwrite the fiscal year when retaining ratings
        return $this->db->where('appID', $targetAppId)->update('hris_applications_rating', $baseData);
    }

    $baseData['fy'] = $targetFy;
    return $this->db->insert('hris_applications_rating', $baseData);
  }

public function application_change_stat_by_job_id($status){

  $data = array( 
          'appStatus' => $status
  );

  $this->db->where('jobID', $this->uri->segment(4));
  return $this->db->update('hris_applications', $data);
}



public function update_request_stat(){

  $data = array(
    'stat' => 1, 
    'res' => $this->session->id
  );

  $this->db->where('id', $this->input->post('id'));
  return $this->db->update('hris_rating_request', $data);
}

public function application_change_stat($status){

  $data = array( 
          'appStatus' => $status,
          'dq' => 1,
  );

  $this->db->where('appID', $this->input->post('app_id'));
  return $this->db->update('hris_applications', $data);
}

public function special_change_stat($status){

  $data = array( 
          'appStatus' => $status
  );

  $this->db->where('appID', $this->uri->segment(4));
  return $this->db->update('hris_applications', $data);
}

public function insert_rate_by_job(){
            $jobID = $this->uri->segment(3); 
            $job = $this->Common->one_cond_row('hris_jobvacancy','jobID', $jobID);
            
            $this->db->where('jobID', $jobID); 
            $this->db->where('appStatus','Application Submitted'); 
            $this->db->update('hris_applications', [
                'appStatus' => 'Validated'
            ]);
            
            if (!$updated) {
              log_message('error', 'Update failed: ' . $this->db->last_query());
            }
            
            // $query = $this->db->select('applicant_id, jobID, appID')
            //                   ->from('hris_applications')
            //                   ->where('jobID', $jobID)
            //                   ->get();
    
            // foreach ($query->result() as $row) {
            //     $this->db->insert('hris_applications_rating', [
            //         'record_no' => $row->applicant_id,
            //         'appID'     => $row->appID,
            //         'education' => .00001, 
            //         'training' => .00001, 
            //         'experience' => .00001, 
            //         'let_rating' => .00001, 
            //         'demo_rating' => .00001, 
            //         'tr_rating' => .00001,
            //         'job_type' => $job->job_type,
            //         'fy' => $job->sy,
            //     ]);
            // }
  
}

public function insert_ndq_rate($gt, $fy){

  $data = array(
      'record_no' => $this->input->post('record_no'), 
      'appID' => $this->input->post('appID'),
      'education' => .00001, 
      'training' => .00001, 
      'experience' => .00001, 
      'let_rating' => .00001, 
      'demo_rating' => .00001, 
      'tr_rating' => .00001,
      'job_type' => $gt,
      'fy' => $fy
      
  );

  return $this->db->insert('hris_applications_rating', $data);
}

public function resetemployeeno(){

  $data = array(
      'number' => 1000
  );

  $this->db->where('id', 3);
  return $this->db->update('count', $data);
}

public function join_applicant_and_staff() {
  // Raw SQL to join the two tables using FirstName, MiddleName, and LastName
  $sql = "SELECT a.*, s.*
          FROM hris_applicant a
          JOIN hris_staff s
              ON a.FirstName = s.FirstName
              AND a.MiddleName = s.MiddleName
              AND a.LastName = s.LastName";
  
  // Execute the query
  $query = $this->db->query($sql);
  
  // Return the result as an array of rows
  return $query->result();
}

public function get_grouped_applicants_by_job($jobID, $mun)
{
    $this->db->select("
        a.*,
        r.education, r.training, r.experience, r.let_rating, r.demo_rating, r.tr_rating, r.total_points,
        COALESCE(app.record_no, staff.IDNumber) AS code,
        COALESCE(app.empEmail, staff.IDNumber) AS renren,
        COALESCE(app.contactNo, staff.contactNo) AS contactNo,
        COALESCE(app.FirstName, staff.FirstName) AS FirstName,
        COALESCE(app.LastName, staff.LastName) AS LastName,
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
    $this->db->where('r.total_points >=', 50); 
    $this->db->where("(app.resCity = " . $this->db->escape($mun) . " OR (app.empEmail IS NULL AND staff.resCity = " . $this->db->escape($mun) . "))", null, false);
    $this->db->order_by('jhss', 'ASC');
    $this->db->order_by('r.total_points', 'DESC');

    $query = $this->db->get();
    $results = $query->result();

    // Group results by jhss
    $grouped = [];
    foreach ($results as $row) {
        $jhss = $row->jhss ?? 'Undefined';
        $grouped[$jhss][] = $row;
    }

    return $grouped;
}

public function get_grouped_applicants_by_mun($jobID)
{
    $this->db->select("
        a.*,
        r.educ, r.trainings, r.experience, r.performance, r.oa, r.ae, r.ald, r.interview, r.written,
        r.record_no, r.appID, fy, job_type, total_points, skills,
        COALESCE(app.record_no, staff.IDNumber) AS code,
        COALESCE(app.empEmail, staff.IDNumber) AS renren,
        COALESCE(app.contactNo, staff.contactNo) AS contactNo,
        COALESCE(app.FirstName, staff.FirstName) AS FirstName,
        COALESCE(app.LastName, staff.LastName) AS LastName,
        COALESCE(app.jhss, staff.jhss) AS jhss,
        COALESCE(app.shss, staff.shss) AS shss,
        COALESCE(app.resCity, staff.resCity) AS resCity,
        COALESCE(app.resBarangay, staff.resBarangay) AS brgy,
        COALESCE(app.empPosition, staff.empPosition) AS position
    ");
    $this->db->from('hris_applications a');
    $this->db->join('hris_applicant app', 'a.empEmail = app.empEmail', 'left');
    $this->db->join('hris_staff staff', 'app.record_no IS NULL AND a.empEmail = staff.IDNumber', 'left');
    $this->db->join('hris_rating_none r', 'a.appID = r.appID', 'left');
    $this->db->where('a.jobID', $jobID);
    $this->db->where('a.dq ',1);
    $this->db->order_by('r.total_points', 'DESC');

    $query = $this->db->get();
    $results = $query->result();

    $grouped = [];
    foreach ($results as $row) {
        $municipalityRaw = $row->resCity ?? 'Undefined';
        $municipalityClean = preg_replace('/\s+/', ' ', trim($municipalityRaw));
        $municipality = ucwords(strtolower($municipalityClean));
        $grouped[$municipality][] = $row;
    }

    return $grouped;
}


public function get_grouped_applicants_by_shss($jobID, $mun)
{
    $shssValue = "UPPER(TRIM(COALESCE(app.shss, staff.shss, 'Undefined')))";

    $shssOrder = "
        CASE
            WHEN {$shssValue} LIKE 'HUMSS%I-A:%' OR {$shssValue} LIKE 'HUMMS%I-A:%' THEN 1
            WHEN {$shssValue} LIKE 'HUMSS%I-B:%' OR {$shssValue} LIKE 'HUMMS%I-B:%' THEN 2
            WHEN {$shssValue} LIKE 'HUMSS%I-C:%' OR {$shssValue} LIKE 'HUMMS%I-C:%' THEN 3
            WHEN {$shssValue} LIKE 'HUMSS%I-D:%' OR {$shssValue} LIKE 'HUMMS%I-D:%' THEN 4

            WHEN {$shssValue} LIKE 'STEM%III-A:%' THEN 5
            WHEN {$shssValue} LIKE 'STEM%III-B:%' THEN 6

            WHEN {$shssValue} LIKE 'TVL%IV-A:%' THEN 7
            WHEN {$shssValue} LIKE 'TVL%IV-B:%' THEN 8
            WHEN {$shssValue} LIKE 'TVL%IV-C:%' THEN 9
            WHEN {$shssValue} LIKE 'TVL%IV-D:%' THEN 10

            WHEN {$shssValue} LIKE 'ABM%' THEN 11
            WHEN {$shssValue} LIKE 'SPORTS%' THEN 12
            WHEN {$shssValue} LIKE 'ARTS%' THEN 13

            ELSE 99
        END
    ";

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
        COALESCE(app.LastName, staff.LastName) AS LastName,
        COALESCE(app.MiddleName, staff.MiddleName) AS MiddleName,
        COALESCE(app.NameExtn, staff.NameExtn) AS NameExtn,
        COALESCE(app.jhss, staff.jhss) AS jhss,
        COALESCE(app.shss, staff.shss) AS shss,
        COALESCE(app.resCity, staff.resCity) AS resCity,
        COALESCE(app.resBarangay, staff.resBarangay) AS brgy
    ", false);

    $this->db->from('hris_applications a');
    $this->db->join('hris_applicant app', 'a.empEmail = app.empEmail', 'left');
    $this->db->join('hris_staff staff', 'app.record_no IS NULL AND a.empEmail = staff.IDNumber', 'left');
    $this->db->join('hris_applications_rating r', 'a.appID = r.appID', 'left');

    $this->db->where('a.jobID', $jobID);
    $this->db->where('r.total_points >=', 50);
    $this->db->where('r.let_rating !=', 0);

    $this->db->where("
        (
            app.resCity = ".$this->db->escape($mun)."
            OR 
            (app.empEmail IS NULL AND staff.resCity = ".$this->db->escape($mun).")
        )
    ", null, false);

    $this->db->order_by($shssOrder, 'ASC', false);
    $this->db->order_by('r.total_points', 'DESC');

    $query = $this->db->get();
    $results = $query->result();

    $grouped = [];

    foreach ($results as $row) {
        $shss = trim($row->shss ?? '');

        if ($shss === '') {
            $shss = 'Undefined';
        }

        $grouped[$shss][] = $row;
    }

    return $grouped;
}

  // public function get_grouped_applicants_by_mun_ier($jobID)
  // {
  //     $this->db->select("
  //         a.*,
  //         r.educ, r.trainings, r.experience, r.performance, r.oa, r.ae, r.ald, r.interview, r.written,
  //         r.record_no, r.appID, fy, job_type, total_points, skills,
  //         COALESCE(app.record_no, staff.IDNumber) AS code,
  //         COALESCE(app.empEmail, staff.IDNumber) AS renren,
  //         COALESCE(app.contactNo, staff.contactNo) AS contactNo,
  //         COALESCE(app.FirstName, staff.FirstName) AS FirstName,
  //         COALESCE(app.LastName, staff.LastName) AS LastName,
  //         COALESCE(app.MiddleName, staff.MiddleName) AS MiddleName,
  //         COALESCE(app.jhss, staff.jhss) AS jhss,
  //         COALESCE(app.jhss, staff.jhss) AS jhss,
  //         COALESCE(app.shss, staff.shss) AS shss,
  //         COALESCE(app.resCity, staff.resCity) AS resCity,
  //         COALESCE(app.resBarangay, staff.resBarangay) AS brgy,
  //         COALESCE(app.resProvince, staff.resProvince) AS province,
  //         COALESCE(app.age, staff.age) AS age,
  //         COALESCE(app.sex, staff.sex) AS sex,
  //         COALESCE(app.MaritalStatus, staff.MaritalStatus) AS ms,
  //         COALESCE(app.empEmail, staff.empEmail) AS email,
  //         COALESCE(app.religion, staff.religion) AS religion,
  //         COALESCE(app.ethnicity, staff.ethnicity) AS ethnicity,
  //         COALESCE(app.disability, staff.disability) AS disability,
  //     ");
  //     $this->db->from('hris_applications a');
  //     $this->db->join('hris_applicant app', 'a.empEmail = app.empEmail', 'left');
  //     $this->db->join('hris_staff staff', 'app.record_no IS NULL AND a.empEmail = staff.IDNumber', 'left');
  //     $this->db->join('hris_rating_none r', 'a.appID = r.appID', 'left');
  //     $this->db->where('a.jobID', $jobID);
  //     //$this->db->where('a.dq ',1);
  //     $this->db->order_by('r.total_points', 'DESC');

  //     $query = $this->db->get();
  //     $results = $query->result();

  //     $grouped = [];
  //     foreach ($results as $row) {
  //         $municipalityRaw = $row->resCity ?? 'Undefined';
  //         $municipalityClean = preg_replace('/\s+/', ' ', trim($municipalityRaw));
  //         $municipality = ucwords(strtolower($municipalityClean));
  //         $grouped[$municipality][] = $row;
  //     }

  //     return $grouped;
  // }

  public function get_grouped_applicants_by_mun_ier($jobID)
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
        COALESCE(app.disability, staff.disability) AS disability
    ", false);

    $this->db->from('hris_applications a');

    $this->db->join('hris_jobvacancy j',"j.jobID = a.jobID",);

    $this->db->join('hris_applicant app', 'a.empEmail = app.empEmail', 'left');
    $this->db->join('hris_staff staff', 'app.record_no IS NULL AND a.empEmail = staff.IDNumber', 'left');

    $this->db->join('hris_rating_none r', 'a.appID = r.appID', 'left');

    $this->db->join('hris_rating_request rr', 'rr.app_id = a.appID', 'left');
    $this->db->where('rr.app_id IS NULL', null, false);

    $this->db->where('a.jobID', $jobID);

    $this->db->order_by('r.total_points', 'DESC');

    $results = $this->db->get()->result();

    $grouped = [];
    foreach ($results as $row) {
        $municipalityRaw   = $row->resCity ?? 'Undefined';
        $municipalityClean = preg_replace('/\s+/', ' ', trim($municipalityRaw));
        $municipality      = ucwords(strtolower($municipalityClean));
        $grouped[$municipality][] = $row;
    }

    return $grouped;
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

    // For retain_status (example: request with r_type = 2)
    $this->db->join(
        'hris_rating_request rr_status',
        'rr_status.app_id = a.appID AND rr_status.r_type = 2',
        'left'
    );

    $this->db->where('a.jobID', $jobID);
    $this->db->where('a.dq', 1);

    // Hide if may r_type = 1
    $this->db->where('rr_hide.app_id IS NULL', null, false);

    $this->db->order_by('r.total_points', 'DESC');

    $results = $this->db->get()->result();

    $grouped = [];
    foreach ($results as $row) {
        $municipalityRaw   = $row->resCity ?? 'Undefined';
        $municipalityClean = preg_replace('/\s+/', ' ', trim($municipalityRaw));
        $municipality      = ucwords(strtolower($municipalityClean));
        $grouped[$municipality][] = $row;
    }

    return $grouped;
}
    public function ap_track_apply_non_teaching($status,$app_id){
      date_default_timezone_set('Asia/Manila');
      $date = date('Y-m-d');
      $t = date('h:i:s a', time());

      $jobid = $this->uri->segment(3);
    

      $data = array( 
              'jobID' => $jobid, 
              //'empEmail' => $this->session->username, 
              'dateSubmitted' => $date, 
              'appStatus' => $status, 
              'timeSubmitted' => $t,
              'applicant_id' => $this->uri->segment(3),
              'res' => $this->session->username,
              'app_id' => $app_id
      );
  
      return $this->db->insert('hris_applications_track', $data);
    }

    public function new_confession(){
      date_default_timezone_set('Asia/Manila');
      $date = date('Y-m-d');
      $t = date('h:i:s a', time());

      $data = array( 
              'school_id' => $date, 
              'con' => $this->input->post('con'),
              'cdate' => $date, 
              'stat' => 0, 
              'ctime' => $t
      );
  
      return $this->db->insert('sbcp_confession', $data);
    }

    public function all_applicant_non_teaching($jobID, $mun)
    {
        $this->db->select("
            a.*,
            r.education, r.training, r.experience, r.let_rating, r.demo_rating, r.tr_rating, r.total_points,
            COALESCE(app.record_no, staff.IDNumber) AS code,
            COALESCE(app.empEmail, staff.IDNumber) AS renren,
            COALESCE(app.contactNo, staff.contactNo) AS contactNo,
            COALESCE(app.FirstName, staff.FirstName) AS FirstName,
            COALESCE(app.LastName, staff.LastName) AS LastName,
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
        $this->db->where('r.total_points >=', 50);
        $this->db->where("(app.resCity = " . $this->db->escape($mun) . " OR (app.empEmail IS NULL AND staff.resCity = " . $this->db->escape($mun) . "))", null, false);
        $this->db->order_by('r.total_points', 'DESC');

        $query = $this->db->get();
        $results = $query->result();

        // 🔄 Group results by shss instead of jhss
        $grouped = [];
        foreach ($results as $row) {
            $shss = $row->shss ?? 'Undefined';
            $grouped[$shss][] = $row;
        }

        return $grouped;
    }


    public function update_query_stat($applicant,$jobID){

      $data = array(
          'stat' => 1
          );

      $this->db->where('applicant_id', $applicant);
      $this->db->where('job_id', $jobID);
      return $this->db->update('hris_application_inquiry', $data);
    }

    public function insert_trainings(){

      $file = $this->upload->data();
      $filename = $file['file_name']; 

      date_default_timezone_set('Asia/Manila');
			$now = date('H:i:s A');
			$date = date("Y-m-d");

      $data = array(
          'title' => $this->input->post('title'), 
          'file' => $filename,
          'nh' => $this->input->post('nh'), 
          'stat' => 0, 
          'id_number' => $this->input->post('id_number'), 
          );
        return $this->db->insert('hris_training', $data);
    }

    public function insert_experience(){

      $file = $this->upload->data();
      $filename = $file['file_name']; 

      date_default_timezone_set('Asia/Manila');
			$now = date('H:i:s A');
			$date = date("Y-m-d");

      $data = array(
          'title' => $this->input->post('title'), 
          'file' => $filename,
          'nm' => $this->input->post('nm'), 
          'ny' => $this->input->post('ny'), 
          'stat' => 0, 
          'id_number' => $this->input->post('id_number'), 
          );
        return $this->db->insert('hris_experience', $data);
    }

    public function update_experience($col){

      $data = array(
          $col => $this->input->post($col), 
          );

        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('hris_experience', $data);
    }

    public function update_trainings(){

      $data = array(
          'nh' => $this->input->post('nh'), 
          );

        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('hris_training', $data);
    }

    public function update_training_staff(){

      $data = array(
          'noHours' => $this->input->post('nh'), 
          );

        $this->db->where('trainingID', $this->input->post('id'));
        return $this->db->update('hris_trainings', $data);
    }

    public function update_cert_stat($table){

      $data = array(
          'stat' => $this->uri->segment(4), 
          );

        $this->db->where('id', $this->uri->segment(3));
        return $this->db->update($table, $data);
    }

    public function gettotaltraining($table,$col,$id_number)
    {
        return $this->db
            ->select('SUM('.$col.') AS total')
            ->where('stat', 1)
            ->where('id_number', $id_number)
            ->get($table)
            ->row()
            ->total;
    }

    public function gettotaltraining_staff($table,$col,$id_number)
    {
        return $this->db
            ->select('SUM('.$col.') AS total')
            ->where('stat', 1)
            ->where('IDNumber', $id_number)
            ->get($table)
            ->row()
            ->total;
    }

    public function gettotaltraining_staffv2($table,$col,$id_number)
    {
        return $this->db
            ->select('SUM('.$col.') AS total')
            ->where('stat', 1)
            ->where('id_number', $id_number)
            ->get($table)
            ->row()
            ->total;
    }

    public function update_cert_stat_staff($table){

      $data = array(
          'stat' => $this->uri->segment(4), 
          );

        $this->db->where('trainingID', $this->uri->segment(3));
        return $this->db->update($table, $data);
    }

    public function update_remarks($col){

            $data = array(
                $col => $this->input->post('remarks')
                );

            $this->db->where('appID', $this->input->post('app_id'));
            return $this->db->update('hris_applications', $data);
    }

    public function insert_rftp(){

          $data = [
              'IDNumber' => $this->input->post('id', TRUE), // change to 'id' if that's your input name
              'fy'       => $this->session->userdata('cur_fy'),
          ];

          for ($i = 1; $i <= 37; $i++) {
              $key = 'q' . $i;
              $data[$key] = $this->input->post($key, TRUE); // will be NULL if not posted
          }

          return $this->db->insert('hris_rftp', $data);
      }

      public function update_rftp(){

          for ($i = 1; $i <= 37; $i++) {
              $key = 'q' . $i;
              $data[$key] = $this->input->post($key, TRUE); // will be NULL if not posted
          }


          $this->db->where('id', $this->input->post('id'));
          return $this->db->update('hris_rftp', $data);
      }

      public function count_ones_twos($idNumber = null, $fy = null)
      {
          $cols = [];
          for ($i = 1; $i <= 37; $i++) $cols[] = "q{$i}";

          $sum1 = "SUM(" . implode(" + ", array_map(fn($c) => "({$c}=5)", $cols)) . ") AS total_1";
          $sum2 = "SUM(" . implode(" + ", array_map(fn($c) => "({$c}=4)", $cols)) . ") AS total_2";

          $this->db->select("$sum1, $sum2", false);
          $this->db->from('hris_rftp');

          if (!empty($idNumber)) $this->db->where('IDNumber', $idNumber);
          if (!empty($fy))       $this->db->where('fy', $fy);

          return $this->db->get()->row();
      }

      public function count_ncoi($idNumber = null, $fy = null)
      {
          $cols = ['q2','q20','q21','q22','q25','q27','q28','q29','q30','q31','q32','q33','q34','q35','q36','q37']; 

          $sum1 = "SUM(" . implode(" + ", array_map(function($c){ return "({$c}=5)"; }, $cols)) . ") AS total_1";
          $sum2 = "SUM(" . implode(" + ", array_map(function($c){ return "({$c}=4)"; }, $cols)) . ") AS total_2";

          $this->db->select("$sum1, $sum2", false);
          $this->db->from('hris_rftp');

          if (!empty($idNumber)) $this->db->where('IDNumber', $idNumber);
          if (!empty($fy))       $this->db->where('fy', $fy);

          return $this->db->get()->row(); 
      }

      public function count_coi($idNumber = null, $fy = null)
      {
          $cols = ['q1','q3','q4','q5','q6','q7','q8','q9','q10','q11','q12','q13','q14','q15','q16','q17','q18','q19','q23','q24','q26']; 

          $sum1 = "SUM(" . implode(" + ", array_map(function($c){ return "({$c}=5)"; }, $cols)) . ") AS total_1";
          $sum2 = "SUM(" . implode(" + ", array_map(function($c){ return "({$c}=4)"; }, $cols)) . ") AS total_2";

          $this->db->select("$sum1, $sum2", false);
          $this->db->from('hris_rftp');

          if (!empty($idNumber)) $this->db->where('IDNumber', $idNumber);
          if (!empty($fy))       $this->db->where('fy', $fy);

          return $this->db->get()->row(); 
      }
  

  
    

    





}
