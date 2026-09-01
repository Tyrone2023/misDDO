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
      $res = $this->db->update('hris_applicant', $data);
      $this->Audit->log('update_document', [
          'entity_type'  => 'document',
          'entity_id'    => 'education',
          'applicant_id' => $this->input->post('id'),
          'job_id'       => $this->input->post('jobID') ?: $this->uri->segment(4),
          'field'        => 'education',
          'description'  => 'Updated education / QS details.',
      ]);
      return $res;
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
      $res = $this->db->update('hris_staff', $data);
      $this->Audit->log('update_document', [
          'entity_type'  => 'document',
          'entity_id'    => 'education',
          'applicant_id' => $this->input->post('id'),
          'job_id'       => $this->input->post('jobID') ?: $this->uri->segment(4),
          'field'        => 'education',
          'description'  => 'Updated education / QS details (staff).',
      ]);
      return $res;
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

      $res    = $this->db->insert('hris_applications', $data);
      $appID  = $this->db->insert_id();
      $vacancy = $this->Common->one_cond_row('hris_jobvacancy', 'jobID', $this->input->post('id'));

      $this->Audit->log('submit_application', [
          'entity_type'  => 'application',
          'entity_table' => 'hris_applications',
          'entity_id'    => $appID,
          'app_id'       => $appID,
          'applicant_id' => $this->session->c_id,
          'job_id'       => $this->input->post('id'),
          'field'        => 'application',
          'description'  => 'Applicant applied to "'
              . trim((string) ($vacancy->jobTitle ?? ('vacancy #' . $this->input->post('id'))))
              . '" (item no. ' . trim((string) ($vacancy->itemNo ?? '-')) . ')'
              . ', preferred district "' . $this->input->post('district') . '"'
              . ', preferred school "' . $this->input->post('school') . '"'
              . '; application #' . $appID . ' created on ' . $date . ' at ' . $t . '.',
          'new_value'    => json_encode($data),
      ]);
      return $res;
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

      $res     = $this->db->insert('hris_applications', $data);
      $appID   = $this->db->insert_id();
      $vacancy = $this->Common->one_cond_row('hris_jobvacancy', 'jobID', $this->uri->segment(3));

      $this->Audit->log('submit_application', [
          'entity_type'  => 'application',
          'entity_table' => 'hris_applications',
          'entity_id'    => $appID,
          'app_id'       => $appID,
          'applicant_id' => $this->session->c_id,
          'job_id'       => $this->uri->segment(3),
          'field'        => 'application',
          'description'  => 'Applicant applied to the non-teaching vacancy "'
              . trim((string) ($vacancy->jobTitle ?? ('vacancy #' . $this->uri->segment(3))))
              . '" (item no. ' . trim((string) ($vacancy->itemNo ?? '-')) . ')'
              . '; application #' . $appID . ' created on ' . $date . ' at ' . $t . '.',
          'new_value'    => json_encode($data),
      ]);
      return $res;
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
      $res = $this->db->update('hris_applications', $data);
      $this->audit_status_change($status);
      return $res;
    }

    /**
     * Log an application status change. "Endorsed for Rating" is surfaced as an
     * `endorse` action so endorsements are easy to filter; everything else is a
     * generic `status_change`.
     */
    private function audit_status_change($status){
      $isEndorse = (stripos((string) $status, 'Endorsed') !== false);
      $this->Audit->log($isEndorse ? 'endorse' : 'status_change', [
          'entity_type'  => 'application',
          'entity_id'    => $this->uri->segment(6) ?: $this->input->post('appID'),
          'app_id'       => $this->uri->segment(6) ?: $this->input->post('appID'),
          'applicant_id' => $this->uri->segment(3),
          'job_id'       => $this->uri->segment(4),
          'field'        => 'appStatus',
          'description'  => 'Application status set to "' . $status . '".',
      ]);
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

    public function remove_attach($column, $removed_file = null){

      $data = array(
          $column => ''
          );

      $this->db->where('id', $this->uri->segment(3));
      $res = $this->db->update('hris_applicant', $data);
      $this->Audit->log('delete_document', [
          'entity_type'  => 'document',
          'entity_table' => 'hris_applicant',
          'entity_id'    => $column,
          'applicant_id' => $this->uri->segment(3),
          'job_id'       => $this->uri->segment(4),
          'field'        => $column,
          'description'  => 'Removed document / attachment "' . $column . '" from the applicant profile'
              . ($removed_file ? '; file deleted: ' . $removed_file : '') . '.',
          'old_value'    => $removed_file,
      ]);
      return $res;
    }

    public function remove_attach_staff($column, $removed_file = null){

      $data = array(
          $column => ''
          );

      $this->db->where('IDNumber', $this->uri->segment(3));
      $res = $this->db->update('hris_staff', $data);
      $this->Audit->log('delete_document', [
          'entity_type'  => 'document',
          'entity_table' => 'hris_staff',
          'entity_id'    => $column,
          'applicant_id' => $this->uri->segment(3),
          'job_id'       => $this->uri->segment(4),
          'field'        => $column,
          'description'  => 'Removed document / attachment "' . $column . '" from the staff 201 profile'
              . ($removed_file ? '; file deleted: ' . $removed_file : '') . '.',
          'old_value'    => $removed_file,
      ]);
      return $res;
    }

    public function remove_attach_app($column, $removed_file = null){

      $data = array(
          $column => ''
          );

      $this->db->where('appID', $this->uri->segment(7));
      $res = $this->db->update('hris_applications', $data);
      $this->Audit->log('delete_document', [
          'entity_type'  => 'document',
          'entity_table' => 'hris_applications',
          'entity_id'    => $column,
          'app_id'       => $this->uri->segment(7),
          'applicant_id' => $this->uri->segment(3),
          'job_id'       => $this->uri->segment(4),
          'field'        => $column,
          'description'  => 'Removed document / attachment "' . $column . '" from application #'
              . $this->uri->segment(7)
              . ($removed_file ? '; file deleted: ' . $removed_file : '') . '.',
          'old_value'    => $removed_file,
      ]);
      return $res;
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

    /**
     * Guarantee the rating row exists before a score is written into it.
     *
     * The rating row is normally created when the Secretariat endorses the
     * applicant (Pages::Unqualified_none -> insert_rate_none). Every update_rate*
     * / update_eval* method below is an UPDATE with no insert fallback, so when
     * that row is missing the write silently affects zero rows while the
     * controller still flashes "Successfuly Saved" - the evaluator's score is
     * lost with no error anywhere.
     *
     * Insert the same all-sentinel row insert_rate_* would have created, so the
     * UPDATE that follows lands somewhere.
     *
     * @param  string $table
     * @return bool TRUE when a row was created.
     */
    private function ensure_rate_row($table)
    {
      $appId  = $this->input->post('app_id');
      $record = $this->input->post('record_no');

      // Without both keys there is nothing to key a row on - let the UPDATE
      // no-op exactly as it did before rather than inserting a stray row.
      if (empty($appId) || $record === NULL || $record === '') {
          return FALSE;
      }

      $exists = $this->db->get_where($table, array(
          'appID'     => $appId,
          'record_no' => $record,
      ))->row();

      if ($exists) {
          return FALSE;
      }

      // NOT NULL columns without a database default first (total_points, skills,
      // ...), so the insert also succeeds on a server running in STRICT mode.
      $data = rating_required_defaults($table);

      foreach (rating_score_fields($table) as $field) {
          $data[$field] = .00001;
      }

      $data['appID']     = $appId;
      $data['record_no'] = $record;

      // job_type / fy come from the vacancy the application was filed against -
      // the same values insert_rate_* record at endorsement time.
      $app = $this->db->select('jobID')
                      ->get_where('hris_applications', array('appID' => $appId))
                      ->row();

      if ($app) {
          $job = $this->db->select('position, sy')
                          ->get_where('hris_jobvacancy', array('jobID' => $app->jobID))
                          ->row();

          if ($job) {
              $data['job_type'] = $job->position;
              $data['fy']       = $job->sy;
          }
      }

      return $this->db->insert($table, $data);
    }

    public function update_rate($educ){

      $this->ensure_rate_row('hris_applications_rating');

      $data = array(
          $educ => $this->input->post($educ)
      );


      $this->db->where('appID', $this->input->post('app_id'));
      $this->db->where('record_no', $this->input->post('record_no'));
      $res = $this->db->update('hris_applications_rating', $data);
      $this->audit_rating('hris_applications_rating', $educ);
      return $res;
    }

    public function update_rate_none($educ){

      $this->ensure_rate_row('hris_rating_none');

      $data = array(
          $educ => $this->input->post($educ),
      );


      $this->db->where('appID', $this->input->post('app_id'));
      $this->db->where('record_no', $this->input->post('record_no'));
      $res = $this->db->update('hris_rating_none', $data);
      $this->audit_rating('hris_rating_none', $educ);
      if ($res) {
          $this->auto_mark_rated((int) $this->input->post('app_id'));
      }
      return $res;
    }

    public function update_rate_promotion($educ){

      $this->ensure_rate_row('hris_rating_promotion');

      $data = array(
          $educ => $this->input->post($educ),
      );


      $this->db->where('appID', $this->input->post('app_id'));
      $this->db->where('record_no', $this->input->post('record_no'));
      $res = $this->db->update('hris_rating_promotion', $data);
      $this->audit_rating('hris_rating_promotion', $educ);
      return $res;
    }

    /**
     * Log a single encoded rating component to the audit trail. Called by every
     * update_rate* funnel so who-rated-what is captured regardless of which
     * controller flow (ma page, evaluator_applicant, etc.) triggered it.
     */
    private function audit_rating($table, $field){
      $label = $this->rating_field_label($field);
      $this->Audit->log('rate', [
          'entity_type'  => 'rating',
          'entity_id'    => $table,
          'app_id'       => $this->input->post('app_id'),
          'applicant_id' => $this->input->post('record_no'),
          'job_id'       => $this->input->post('jobID') ?: $this->uri->segment(4),
          'field'        => $field,
          'description'  => 'Encoded ' . $label . ' rating: ' . $this->input->post($field),
      ]);
    }

    /** Friendly label for a rating column, for the audit description. */
    private function rating_field_label($field){
      $map = [
          'education'   => 'Education',
          'educ'        => 'Education',
          'training'    => 'Training',
          'trainings'   => 'Training',
          'experience'  => 'Experience',
          'let_rating'  => 'LET',
          'demo_rating' => 'Demo',
          'tr_rating'   => "Teacher's Reflection",
          'performance' => 'Performance',
          'interview'   => 'Interview',
          'written'     => 'Written',
          'ppstco'      => 'PPST Classroom Observation',
          'ppstpa'      => 'PPST Performance Assessment',
          'oa'          => 'Outstanding Accomplishment',
          'skills'      => 'Skills',
      ];
      return $map[$field] ?? ucfirst(str_replace('_', ' ', (string) $field));
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

    /**
     * @param string $context 'validation'    - document validation screens
     *                        'qualification' - evaluator qualification gate.
     *                        Only changes how the decision is labelled in the
     *                        audit trail.
     */
    public function insert_dq($context = 'validation'){
      date_default_timezone_set('Asia/Manila');
      $date = date('Y-m-d');
      $t = date('h:i:s a', time());
      $li = ($this->input->post('li') == '') ? 0 : 1;
      $da_pds = ($this->input->post('da_pds') == '') ? 0 : 1;
      $prc = ($this->input->post('prc') == '') ? 0 : 1;
      $trbd = ($this->input->post('trbd') == '') ? 0 : 1;
      $omni = ($this->input->post('omni') == '') ? 0 : 1;
      $local = ($this->input->post('local') == '') ? 0 : 1;

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
              'local' => $local, 
              'remarks' => $this->input->post('remarks'), 
              'reason' => $this->input->post('reason'), 
              'vdate' => $date,
              'res' => $this->session->id,

              'educ' => $educ,
              'exp' => $exp,
              'tr' => $tr,
              'eli' => $eli,
              'fy' => $this->input->post('job_fy') ?: date('Y'),


      );

      $res = $this->db->insert('hris_app_dq', $data);
      $this->audit_validation($this->input->post('remarks'), $this->input->post('reason'), $context);
      return $res;
    }

    /**
     * Log a Qualified / Disqualified decision to the audit trail.
     * remarks: 1 = Qualified, 2 = Disqualified.
     *
     * The evaluator's qualification gate and the document validation screens
     * write the same hris_app_dq row, so $context keeps them apart on the
     * timeline: "Marked Qualified" vs "Validated".
     */
    private function audit_validation($remarks, $reason = '', $context = 'validation'){
      $qualified = ((string) $remarks === '1');
      $qualification = ($context === 'qualification');

      if ($qualified) {
          $action = $qualification ? 'qualify' : 'validate';
      } else {
          $action = 'disqualify';
      }

      $decision = $qualification
          ? ($qualified ? 'Marked applicant as Qualified and endorsed the application for rating.'
                        : 'Marked applicant as Disqualified at the qualification review.')
          : ($qualified ? 'Marked application as Qualified.'
                        : 'Marked application as Disqualified.');

      $this->Audit->log($action, [
          'entity_type'  => 'application',
          'entity_id'    => $this->input->post('appID'),
          'app_id'       => $this->input->post('appID'),
          'applicant_id' => $this->input->post('id'),
          'job_id'       => $this->input->post('jobID'),
          'field'        => 'remarks',
          'description'  => $decision . ($reason ? ' Reason: ' . $reason : ''),
      ]);
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
      $local = ($this->input->post('local') == '') ? 0 : 1;

      $data = array( 
              'jobID' => $this->input->post('jobID'), 
              'appID' => $this->input->post('appID'), 
              'apID' => $this->input->post('id'), 
              'li' => $li, 
              'da_pds' => $da_pds, 
              'prc' => $prc, 
              'trbd' => $trbd, 
              'omni' => $omni, 
              'local' => $local, 
              'remarks' => $this->input->post('remarks'), 
              'reason' => $this->input->post('reason'), 
              'vdate' => $date,
              'res' => $this->session->id
      );
  
      $this->db->where('id', $this->input->post('dq_id'));
      $res = $this->db->update('hris_app_dq', $data);
      $this->audit_validation($this->input->post('remarks'), $this->input->post('reason'));
      return $res;
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

    /**
     * The announcement columns were added after hris_jobvacancy shipped, so the
     * screens that read them create the columns on first use.  See
     * application/sql/hris_jobvacancy_announcement.sql.
     */
    public function ensure_announcement_columns(){

      $this->Common->ensure_columns('hris_jobvacancy', array(
          'announcement'    => 'text null',
          'announcement_by' => 'varchar(150) null',
          'announcement_at' => 'datetime null'
          ));
    }

    /**
     * Saves the announcement/remarks shown to everyone who applied for the
     * posting.  Blanking the text clears the announcement along with its
     * author/timestamp stamp.
     */
    public function announcement_update($jobID, $announcement){

      date_default_timezone_set('Asia/Manila');

      if ($announcement === '') {
        $data = array(
            'announcement' => null,
            'announcement_by' => null,
            'announcement_at' => null
            );
      } else {
        $data = array(
            'announcement' => $announcement,
            'announcement_by' => $this->session->username,
            'announcement_at' => date('Y-m-d H:i:s')
            );
      }

      $this->db->where('jobID', $jobID);
      return $this->db->update('hris_jobvacancy', $data);
    }

    public function update_eval($eval){

      $this->ensure_rate_row('hris_applications_rating');

      $data = array(
          $eval => $this->session->id
      );


      $this->db->where('appID', $this->input->post('app_id'));
      $this->db->where('record_no', $this->input->post('record_no'));
      return $this->db->update('hris_applications_rating', $data);
    }

    public function update_eval_none($eval){

      $this->ensure_rate_row('hris_rating_none');

      $data = array(
          $eval => $this->session->id
      );


      $this->db->where('appID', $this->input->post('app_id'));
      $this->db->where('record_no', $this->input->post('record_no'));
      return $this->db->update('hris_rating_none', $data);
    }

     public function update_eval_promotion($eval){

      $this->ensure_rate_row('hris_rating_promotion');

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
   * Release an eval_id1 claim on hris_rating_none that no score backs.
   *
   * eval_id1 belongs to the evaluator who scores Education through ALD;
   * Interview and Written Examination are tracked by eval_id2 / eval_id3. The
   * Secretariat's Interview / Written encoder used to stamp eval_id1 as well,
   * which hid every Rate button from the assigned evaluator - rp_reg_none only
   * renders them when eval_id1 is 0 or matches the session's own id.
   *
   * When every evaluator-owned criterion is still on the 0.00001 sentinel
   * nobody has actually rated the applicant, so the claim has no owner and is
   * cleared. Idempotent: a row already at 0, or one carrying real scores, is
   * left untouched.
   */
  public function release_unrated_eval_claim($appId)
  {
      $appId = (int) $appId;

      if ($appId <= 0) {
          return false;
      }

      // 0.0001 is the legacy sentinel; a genuine 0 is a real score and must
      // keep the claim.
      $stubs = [0.00001, 0.0001];

      $this->db->where('appID', $appId)->where('eval_id1 >', 0);

      foreach (['educ', 'trainings', 'experience', 'performance', 'oa', 'ae', 'ald'] as $column) {
          $this->db->where_in($column, $stubs);
      }

      return $this->db->update('hris_rating_none', ['eval_id1' => 0]);
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
   * Automatically set an application to Rated when every required score is
   * present. Non-teaching Skills is not a rated component: it does not block
   * the transition, is stored as zero once complete, and is excluded from the
   * total.
   */
  public function auto_mark_rated($appId)
  {
      $appId = (int) $appId;
      if ($appId <= 0) return false;

      $app = $this->db->get_where('hris_applications', ['appID' => $appId])->row();
      if (!$app) return false;

      $job = $this->db
                  ->select('position, promotion')
                  ->get_where('hris_jobvacancy', ['jobID' => (int) $app->jobID])
                  ->row();
      if (!$job) return false;

      $position = (int) ($job->position ?? 0);
      $promotion = (int) ($job->promotion ?? 0);

      if ($position === 1 && $promotion === 0) {
          $table = 'hris_applications_rating';
      } elseif ($position === 5 || ($position === 1 && $promotion !== 0)) {
          $table = 'hris_rating_promotion';
      } else {
          $table = 'hris_rating_none';
      }

      // These table-specific lists deliberately omit hris_rating_none.skills.
      $fields = rating_score_fields($table);
      if (empty($fields)) return false;

      // Match the rating views, which display the latest row for an appID when
      // a legacy application has duplicate rows or a mismatched record number.
      $rating = $this->db
                     ->where('appID', $appId)
                     ->order_by('id', 'DESC')
                     ->limit(1)
                     ->get($table)
                     ->row();
      if (!$rating) return false;

      $complete = true;
      $total = 0.0;
      foreach ($fields as $f) {
          if (!isset($rating->$f) || $this->rating_score_is_placeholder($rating->$f)) {
              $complete = false;
              continue;
          }
          $total += (float)$rating->$f;
      }

      $alreadyRated = in_array((string) $app->appStatus, ['Rated', 'Confirmed'], true);
      if (!$complete && !($table === 'hris_rating_none' && $alreadyRated)) {
          return false;
      }

      $ratingUpdate = ['total_points' => $total];
      if ($table === 'hris_rating_none') {
          $ratingUpdate['skills'] = 0;
      }
      $this->db->where('id', (int) $rating->id)->update($table, $ratingUpdate);

      // Preserve an existing workflow status while still normalizing Skills.
      if (!$complete) {
          return false;
      }

      // Only promote the workflow from the rating stage; never overwrite
      // applicant actions like "Confirmed" during a page refresh.
      if ($app->appStatus === 'Endorsed for Rating') {
          $this->db->where('appID', $appId)->update('hris_applications', ['appStatus' => 'Rated']);
      }

      return true;
  }

  /**
   * Reconcile a Secretariat score-encoding vacancy before its list is shown.
   * Completed Endorsed applications become Rated, while existing Rated or
   * Confirmed rows are normalized to the same zero-Skills rule.
   */
  public function auto_mark_rated_for_job($jobId)
  {
      $jobId = (int) $jobId;
      if ($jobId <= 0) return 0;

      $job = $this->db
                  ->select('position')
                  ->get_where('hris_jobvacancy', ['jobID' => $jobId])
                  ->row();
      if (!$job || in_array((int) $job->position, [1, 5], true)) return 0;

      $latestRating = $this->db
                           ->select('appID, MAX(id) AS latest_id', false)
                           ->from('hris_rating_none')
                           ->group_by('appID')
                           ->get_compiled_select();

      $rows = $this->db
                   ->select('a.appID, a.appStatus, r.*')
                   ->from('hris_applications a')
                   ->join("($latestRating) latest_rating", 'latest_rating.appID = a.appID')
                   ->join('hris_rating_none r', 'r.id = latest_rating.latest_id')
                   ->where('a.jobID', $jobId)
                   ->where_in('a.appStatus', ['Endorsed for Rating', 'Rated', 'Confirmed'])
                   ->get()
                   ->result();

      $fields = rating_score_fields('hris_rating_none');
      $ratingUpdates = [];
      $statusAppIds = [];

      foreach ($rows as $rating) {
          $complete = true;
          $total = 0.0;

          foreach ($fields as $field) {
              if (!isset($rating->$field) || $this->rating_score_is_placeholder($rating->$field)) {
                  $complete = false;
                  continue;
              }
              $total += (float) $rating->$field;
          }

          $alreadyRated = in_array((string) $rating->appStatus, ['Rated', 'Confirmed'], true);
          if (!$complete && !$alreadyRated) {
              continue;
          }

          $ratingUpdates[] = [
              'id' => (int) $rating->id,
              'skills' => 0,
              'total_points' => $total,
          ];

          if ($complete && (string) $rating->appStatus === 'Endorsed for Rating') {
              $statusAppIds[] = (int) $rating->appID;
          }
      }

      if (!empty($ratingUpdates)) {
          $this->db->update_batch('hris_rating_none', $ratingUpdates, 'id');
      }
      if (!empty($statusAppIds)) {
          $this->db
               ->where_in('appID', $statusAppIds)
               ->where('appStatus', 'Endorsed for Rating')
               ->update('hris_applications', ['appStatus' => 'Rated']);
      }

      return count($ratingUpdates);
  }

  /** A zero score is valid; only NULL/blank and legacy sentinels are missing. */
  private function rating_score_is_placeholder($value)
  {
      if ($value === null || $value === '') {
          return true;
      }

      $score = (float) $value;
      return abs($score - 0.00001) < 0.000001
          || abs($score - 0.0001) < 0.000001;
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
        // Retained scores arrive unclaimed. Carrying the source evaluator ids
        // over would hide the scores from the evaluator handling this
        // application - the rating views only show a criterion to the evaluator
        // who owns it (or to asds), and update_rate_* only claims an id when it
        // is still 0.
        'eval_id1'     => 0,
        'eval_id2'     => 0,
        'eval_id3'     => 0,
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
        // Unclaimed, so the evaluator on this application can see and re-rate
        // the retained Demo / TR scores.
        'eval_id1'     => 0,
        'eval_id2'     => 0,
        'eval_id3'     => 0,
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



/**
 * Mark a retention request as granted. $grantedScope records what was actually
 * retained: 1 = all criteria, 2 = partial (Demo & TR for teaching positions,
 * Interview & Written Examination for non-teaching ones).
 */
public function update_request_stat($grantedScope = null){

  date_default_timezone_set('Asia/Manila');

  $data = array(
    'stat' => 1,
    'res' => $this->session->id,
    'adate' => date('Y-m-d')
  );

  if ($grantedScope !== null) {
    $data['granted_scope'] = (int) $grantedScope;
  }

  $this->db->where('id', $this->input->post('id'));
  return $this->db->update('hris_rating_request', $data);
}

/**
 * Deny a retention request. The reason is surfaced to the applicant on
 * Pages/request_rating_applicant.
 */
public function deny_request_stat($id, $reason){

  date_default_timezone_set('Asia/Manila');

  $data = array(
    'stat' => 2,
    'deny_reason' => $reason,
    'res' => $this->session->id,
    'adate' => date('Y-m-d')
  );

  $this->db->where('id', (int) $id);
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

    public function ensure_experience_columns(){

      $this->Common->ensure_columns('hris_experience', array(
          'date_from'      => 'date null',
          'date_to'        => 'date null',
          'created_at'     => 'datetime null',
          'updated_at'     => 'datetime null',
          // A service record is read as "what post, at which office" - the
          // company alone never said what the applicant actually did there.
          'position_title' => 'varchar(255) null'
          ));
    }

    /**
     * Trainings carry the same "saved on" stamp as work experience, so the
     * profile can show when a record was encoded and when it was last touched.
     * Rows kept from before the columns existed simply have none.
     */
    public function ensure_training_columns(){

      $this->Common->ensure_columns('hris_trainings', array(
          'created_at' => 'datetime null',
          'updated_at' => 'datetime null'
          ));
    }

    /**
     * Trainings and work experience are the applicant's own supporting records
     * for a vacancy, so they stay editable only while a vacancy they applied
     * to is still open.  Once every applied vacancy is closed both sections
     * freeze - nothing may be added, corrected, re-rated or removed, and what
     * was evaluated at closing time stands.
     *
     * Scoped to one application when the profile is opened from a specific
     * vacancy (?appID / ?jobID); otherwise every application of the applicant
     * is weighed, and a single open vacancy keeps the sections editable.
     *
     * Applications whose vacancy row cannot be read are treated as open, so a
     * missing reference never locks an applicant out.
     *
     * @return array locked, reason, closed (vacancy titles), open (count)
     */
    public function applicant_record_lock($applicant_id, $appID = null, $jobID = null){

      $unlocked = array('locked' => false, 'reason' => '', 'closed' => array(), 'open' => 0);

      $applicant_id = trim((string) $applicant_id);
      if ($applicant_id === '') {
        return $unlocked;
      }

      // Staff 201 profiles share these tables but live in a different id
      // space; without an applicant row there is no vacancy to close against.
      $this->db->where('id', $applicant_id);
      if ($this->db->count_all_results('hris_applicant') < 1) {
        return $unlocked;
      }

      $this->db->select('a.appID, a.jobID, j.jobTitle, j.jvStatus');
      $this->db->from('hris_applications a');
      $this->db->join('hris_jobvacancy j', 'j.jobID = a.jobID', 'left');
      $this->db->where('a.applicant_id', $applicant_id);

      if ((int) $appID > 0) {
        $this->db->where('a.appID', (int) $appID);
      }
      if ((int) $jobID > 0) {
        $this->db->where('a.jobID', (int) $jobID);
      }

      $rows = $this->db->get()->result();

      if (empty($rows)) {
        return $unlocked;
      }

      $open   = 0;
      $closed = array();

      foreach ($rows as $row) {
        if (strcasecmp(trim((string) $row->jvStatus), 'Closed') === 0) {
          $title = trim((string) $row->jobTitle);
          $closed[$title !== '' ? $title : 'Vacancy #' . (int) $row->jobID] = true;
        } else {
          $open++;
        }
      }

      if ($open > 0 || empty($closed)) {
        $unlocked['open'] = $open;
        return $unlocked;
      }

      $closed = array_keys($closed);

      return array(
          'locked' => true,
          'reason' => (count($closed) === 1)
              ? 'The vacancy applied for (' . $closed[0] . ') is already closed.'
              : 'Every vacancy applied for is already closed.',
          'closed' => $closed,
          'open'   => 0,
          );
    }

    /**
     * The division-wide switch (settings row 11) that closes the Add and
     * Delete buttons on Work Experience and Trainings.
     *
     * Distinct from applicant_record_lock(): that one follows the vacancy,
     * this one is turned on and off by hand for everybody at once.
     */
    public function records_settings_locked(){

      $setting = $this->Common->one_cond_row('settings', 'id', 11);

      return (int) ($setting->status ?? 0) === 1;
    }

    /**
     * Server-side half of the two record locks: the encoding endpoints must
     * refuse a write, not merely hide the buttons. Bounces back to the
     * referring page with a message when locked.
     *
     * Two scopes, because the locks do not mean the same thing:
     *
     *   'attachment' (default) - anything that creates, replaces or destroys a
     *       file: adding a row, deleting a row, swapping the PDF. Blocked by
     *       either lock.
     *   'details' - correcting the text on a row that already exists (company,
     *       job title, hours). The settings lock leaves this open, because it
     *       exists to stop documents moving, not to freeze typos in place.
     *       A closed vacancy still blocks it: what was ranked stays as ranked.
     */
    public function block_when_records_locked($applicant_id, $anchor = '', $scope = 'attachment'){

      $lock = $this->applicant_record_lock($applicant_id);

      if (!empty($lock['locked'])) {
        $this->audit_block($applicant_id, $scope, $lock['reason']);

        $this->session->set_flashdata('danger',
            'This section is closed. ' . $lock['reason']);

        redirect(($_SERVER['HTTP_REFERER'] ?? base_url()) . $anchor);

        return true;
      }

      if ($scope !== 'details' && $this->records_settings_locked()) {
        $reason = 'Adding, replacing and deleting attachments is currently disabled by the administrator.';

        $this->audit_block($applicant_id, $scope, $reason);

        $this->session->set_flashdata('danger', $reason);

        redirect(($_SERVER['HTTP_REFERER'] ?? base_url()) . $anchor);

        return true;
      }

      return false;
    }

    /** A refused write is itself worth recording - it shows what was attempted. */
    private function audit_block($applicant_id, $scope, $reason){

      $this->Audit->log('blocked', array(
          'entity_type'  => 'lock',
          'entity_id'    => $scope,
          'applicant_id' => $applicant_id,
          'field'        => $scope,
          'description'  => 'Refused a ' . $scope . ' change: ' . $reason,
          ));
    }

    /**
     * Trainings are encoded with a start and end time, so the two date columns
     * have to carry a time component.  Older installs still have plain DATE
     * columns, so widen them once, in place.
     *
     * Legacy rows hold '0000-00-00', which strict mode refuses to convert, so
     * the zero-date checks are lifted for the conversion and those rows are
     * then stored as NULL - the profile already renders a blank for them.
     */
    public function ensure_training_datetime(){

      $debug = $this->db->db_debug;
      $this->db->db_debug = false;

      $q = $this->db->query(
          "select DATA_TYPE from information_schema.COLUMNS
            where TABLE_SCHEMA = database()
              and TABLE_NAME = 'hris_trainings'
              and COLUMN_NAME in ('dateStarted', 'dateFinished')");

      $needs_widening = false;
      if ($q) {
        foreach ($q->result() as $row) {
          if (strtolower($row->DATA_TYPE) === 'date') {
            $needs_widening = true;
          }
        }
      }

      if ($needs_widening) {
        $this->db->query("set @mis_sql_mode = @@session.sql_mode");
        $this->db->query("set session sql_mode = replace(replace(@@session.sql_mode, 'NO_ZERO_DATE', ''), 'NO_ZERO_IN_DATE', '')");

        $this->db->query("alter table `hris_trainings`
                            modify `dateStarted` datetime null default null,
                            modify `dateFinished` datetime null default null");

        $this->db->query("update `hris_trainings` set `dateStarted` = null where `dateStarted` = '0000-00-00 00:00:00'");
        $this->db->query("update `hris_trainings` set `dateFinished` = null where `dateFinished` = '0000-00-00 00:00:00'");

        $this->db->query("set session sql_mode = @mis_sql_mode");
      }

      $this->db->db_debug = $debug;
    }

    /**
     * Work experience is captured as an inclusive date range, but the rating
     * screens still sum the ny/nm columns.  This converts a range into those
     * columns so both stay in step: whole months are counted, and a trailing
     * partial month of at least 15 days rounds up (so Jan 1 - Dec 31 reads as
     * a full year rather than 11 months).
     */
    public function experience_duration($from, $to){

      $months = $this->experience_months($from, $to);

      return array('ny' => intdiv($months, 12), 'nm' => $months % 12);
    }

    /**
     * The same range expressed as a plain month count, for the profile's
     * length-of-service column and its running total.
     */
    public function experience_months($from, $to){

      if (empty($from) || empty($to)) {
        return 0;
      }

      $start = date_create($from);
      $end   = date_create($to);

      if (!$start || !$end || $end < $start) {
        return 0;
      }

      $diff = $start->diff($end);
      $months = ($diff->y * 12) + $diff->m;

      if ($diff->d >= 15) {
        $months++;
      }

      return $months;
    }

    public function insert_experience(){

      $file = $this->upload->data();
      $filename = $file['file_name'];

      $from = $this->input->post('date_from');
      $to   = $this->input->post('date_to');
      $span = $this->experience_duration($from, $to);

      $data = array(
          'title' => $this->input->post('title'),
          'position_title' => $this->input->post('position_title'),
          'file' => $filename,
          'date_from' => $from ?: null,
          'date_to' => $to ?: null,
          'nm' => $span['nm'],
          'ny' => $span['ny'],
          'stat' => 0,
          'id_number' => $this->input->post('id_number'),
          'created_at' => $this->record_stamp(),
          );
        $res = $this->db->insert('hris_experience', $data);
        $newId = $this->db->insert_id();

        $this->Audit->log('add_experience', [
            'entity_type'  => 'experience',
            'entity_table' => 'hris_experience',
            'entity_id'    => $newId,
            'applicant_id' => $this->input->post('id_number'),
            'field'        => 'experience',
            'description'  => 'Added work experience: '
                . ($data['position_title'] !== '' && $data['position_title'] !== null
                    ? '"' . $data['position_title'] . '" at ' : '')
                . '"' . $data['title'] . '"'
                . ($from && $to ? ' (' . $from . ' to ' . $to . ')' : '')
                . ', attachment "' . $filename . '", saved ' . $data['created_at'] . '.',
            'new_value'    => json_encode(array(
                'position_title' => $data['position_title'],
                'title'          => $data['title'],
                'date_from'      => $data['date_from'],
                'date_to'        => $data['date_to'],
                'file'           => $filename,
            )),
        ]);

        return $res;
    }

    /**
     * Correct the descriptive half of a work experience row - the company and
     * the job title held there. Deliberately separate from the attachment:
     * these two stay editable while the settings lock only freezes the file.
     */
    public function update_experience_details(){

      $id     = $this->input->post('id');
      $before = $this->Audit->snapshot('hris_experience', 'id', $id);

      $data = array(
          'title'          => trim((string) $this->input->post('title')),
          'position_title' => trim((string) $this->input->post('position_title')),
          'updated_at'     => $this->record_stamp(),
          );

      $this->db->where('id', $id);
      $res = $this->db->update('hris_experience', $data);

      $this->Audit->log_changes('update_experience_details',
          $this->Audit->diff($before, $data), array(
              'entity_type'  => 'experience',
              'entity_table' => 'hris_experience',
              'entity_id'    => $id,
              'applicant_id' => $before['id_number'] ?? $this->input->post('id_number'),
              'label'        => 'work experience #' . $id,
              'fields'       => array(
                  'title'          => 'Company / Office',
                  'position_title' => 'Job Title',
              ),
          ));

      return $res;
    }

    /** Local "saved on" stamp; the encoding screens all read Manila time. */
    public function record_stamp(){

      date_default_timezone_set('Asia/Manila');

      return date('Y-m-d H:i:s');
    }

    public function update_experience($col){

      $data = array(
          $col => $this->input->post($col),
          );

        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('hris_experience', $data);
    }

    public function update_experience_dates(){

      $from = $this->input->post('date_from');
      $to   = $this->input->post('date_to');
      $span = $this->experience_duration($from, $to);

      $data = array(
          'date_from' => $from ?: null,
          'date_to' => $to ?: null,
          'ny' => $span['ny'],
          'nm' => $span['nm'],
          'updated_at' => $this->record_stamp(),
          );

        $id  = $this->input->post('id');
        $row = $this->Common->one_cond_row('hris_experience', 'id', $id);

        $this->db->where('id', $id);
        $res = $this->db->update('hris_experience', $data);

        $this->Audit->log('update_experience', [
            'entity_type'  => 'experience',
            'entity_id'    => $id,
            'applicant_id' => $row->id_number ?? $this->input->post('id_number'),
            'field'        => 'experience_dates',
            'description'  => 'Updated inclusive dates of "' . ($row->title ?? '') . '" to '
                . $from . ' - ' . $to . ' (' . $span['ny'] . ' yr ' . $span['nm'] . ' mo),'
                . ' saved ' . $data['updated_at'] . '.',
        ]);

        return $res;
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
          'updated_at' => $this->record_stamp(),
          );

        $id  = $this->input->post('id');
        $row = $this->Common->one_cond_row('hris_trainings', 'trainingID', $id);

        $this->db->where('trainingID', $id);
        $res = $this->db->update('hris_trainings', $data);

        $this->Audit->log('update_training', [
            'entity_type'  => 'training',
            'entity_id'    => $id,
            'applicant_id' => $row->IDNumber ?? $this->input->post('id_number'),
            'field'        => 'training_hours',
            'description'  => 'Set no. of hours of "' . ($row->trainingTitle ?? '') . '" from '
                . (float) ($row->noHours ?? 0) . ' to ' . (float) $data['noHours']
                . ', saved ' . $data['updated_at'] . '.',
        ]);

        return $res;
    }

    public function update_cert_stat($table){

      $stat = $this->uri->segment(4);
      $id   = $this->uri->segment(3);

      $data = array(
          'stat' => $stat,
          );

        // Only the applicant-facing experience table carries the saved stamps.
        if ($table === 'hris_experience') {
          $data['updated_at'] = $this->record_stamp();
        }

        $row = $this->Common->one_cond_row($table, 'id', $id);

        $this->db->where('id', $id);
        $res = $this->db->update($table, $data);

        if ($table === 'hris_experience') {
          $this->Audit->log('update_experience', [
              'entity_type'  => 'experience',
              'entity_id'    => $id,
              'applicant_id' => $row->id_number ?? null,
              'field'        => 'experience_relevance',
              'description'  => 'Marked work experience "' . ($row->title ?? '') . '" as '
                  . $this->relevance_label($stat) . ', saved ' . $this->record_stamp() . '.',
          ]);
        }

        return $res;
    }

    /** Wording for the stat column shared by the training / experience tables. */
    public function relevance_label($stat){

      if ((int) $stat === 1) {
        return 'Relevant';
      }

      return ((int) $stat === 2) ? 'Not Relevant' : 'No Action';
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

      $stat = $this->uri->segment(4);
      $id   = $this->uri->segment(3);

      $data = array(
          'stat' => $stat,
          'updated_at' => $this->record_stamp(),
          );

        $row = $this->Common->one_cond_row($table, 'trainingID', $id);

        $this->db->where('trainingID', $id);
        $res = $this->db->update($table, $data);

        $this->Audit->log('update_training', [
            'entity_type'  => 'training',
            'entity_id'    => $id,
            'applicant_id' => $row->IDNumber ?? null,
            'field'        => 'training_relevance',
            'description'  => 'Marked training "' . ($row->trainingTitle ?? '') . '" as '
                . $this->relevance_label($stat) . ', saved ' . $data['updated_at'] . '.',
        ]);

        return $res;
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

    /**
     * Who may be assigned as the evaluator of a single application.
     *
     * Anyone carrying the Evaluator position qualifies - egroup only buckets
     * evaluators for the bulk Assign Rater tool, so requiring egroup 1 here hid
     * most of the roster from the rating screens. ASDS users stay eligible.
     *
     * Kept in one place so the dropdown in pages/_assign_evaluator.php and the
     * server-side checks in Pages cannot drift apart.
     */
    private function eligible_rater_conditions()
    {
      $this->db->group_start()
          ->where('position', 'Evaluator')
          ->or_where('position', 'asds')
        ->group_end();
    }

    public function eligible_raters()
    {
      $this->db->select('id,fname,mname,lname,username')->from('users');
      $this->eligible_rater_conditions();

      return $this->db
          ->order_by('lname', 'asc')
          ->order_by('fname', 'asc')
          ->get()
          ->result();
    }

    public function is_eligible_rater($raterId)
    {
      $raterId = (int) $raterId;

      if ($raterId <= 0) {
        return false;
      }

      $this->db->where('id', $raterId);
      $this->eligible_rater_conditions();

      return $this->db->count_all_results('users') > 0;
    }

    public function calculate_rating_none_ies($appID)
    {
        $appID = (int) $appID;
        if ($appID <= 0) {
            return;
        }
        $this->db->query('update hris_rating_none set total_points = educ+trainings+experience+performance+oa+ae+ald+interview+written+skills where appID=' . $appID);
    }

    /**
     * Persist an OMR written-exam score into the non-teaching rating table
     * so Pages/ma can display it under Written Examination.
     *
     * The record_no is canonicalised from the real applicant/staff record so it
     * matches the exact key Pages/ma uses (hris_applicant.record_no or
     * hris_staff.empEmail/IDNumber) rather than the possibly-fallback value the
     * scanner passed in.
     */
    public function write_omr_written_score(int $appId, string $recordNo, float $written): bool
    {
        if ($appId <= 0 || $written < 0) {
            return false;
        }

        $app = $this->db->get_where('hris_applications', ['appID' => $appId])->row();
        if (!$app) {
            return false;
        }

        $canonicalRecordNo = null;

        if (!empty($app->applicant_id)) {
            $applicant = $this->db->get_where('hris_applicant', ['id' => (int) $app->applicant_id])->row();
            if ($applicant && isset($applicant->record_no)) {
                $canonicalRecordNo = $applicant->record_no;
                $canonicalRecordNo = ($canonicalRecordNo === null) ? null : (string) $canonicalRecordNo;
            }
        }

        if ($canonicalRecordNo === null && (empty($app->applicant_id) || empty($applicant)) && !empty($app->empEmail)) {
            $staff = $this->db->get_where('hris_staff', ['IDNumber' => (string) $app->empEmail])->row();
            if ($staff) {
                $canonicalRecordNo = !empty($staff->empEmail) ? (string) $staff->empEmail : (string) $staff->IDNumber;
                $canonicalRecordNo = ($canonicalRecordNo === '') ? null : $canonicalRecordNo;
            }
        }

        if ($canonicalRecordNo === null && empty($applicant) && empty($staff)) {
            $canonicalRecordNo = ($recordNo === '') ? null : $recordNo;
        }

        // Look for the row Pages/ma will actually read.
        $this->db->where('appID', $appId);
        if ($canonicalRecordNo === null) {
            $this->db->group_start()->where('record_no', '')->or_where('record_no IS NULL', null, false)->group_end();
        } else {
            $this->db->where('record_no', $canonicalRecordNo);
        }
        $exists = $this->db->get('hris_rating_none')->row();

        if (!$exists) {
            $data = rating_required_defaults('hris_rating_none');
            foreach (rating_score_fields('hris_rating_none') as $field) {
                $data[$field] = .00001;
            }
            $data['appID'] = $appId;
            $data['record_no'] = $canonicalRecordNo;

            $job = $this->db->select('position, sy')->get_where('hris_jobvacancy', ['jobID' => (int) $app->jobID])->row();
            if ($job) {
                $data['job_type'] = $job->position;
                $data['fy'] = $job->sy;
            }

            $this->db->insert('hris_rating_none', $data);
        }

        $this->db->where('appID', $appId);
        if ($canonicalRecordNo === null) {
            $this->db->group_start()->where('record_no', '')->or_where('record_no IS NULL', null, false)->group_end();
        } else {
            $this->db->where('record_no', $canonicalRecordNo);
        }
        $this->db->update('hris_rating_none', ['written' => $written]);
        $this->calculate_rating_none_ies($appId);
        $this->auto_mark_rated($appId);
        return true;
    }

}
