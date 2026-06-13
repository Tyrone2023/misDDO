<!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->
             
                <?php 
                    $job = $this->Common->one_cond_row('hris_jobvacancy', 'jobID',$this->uri->segment(4));
                    $history = $this->Common->one_cond('hris_applications','empEmail',$staff->empEmail); 

                    // Prefer explicit appID from URL to avoid picking an older application when retained ratings exist
                    $appIdFromUrl = $this->uri->segment(6);
                    $aa = null;
                    if (!empty($appIdFromUrl)) {
                        $aa = $this->Common->one_cond_row('hris_applications','appID',$appIdFromUrl);
                    }

                    if (empty($aa)) {
                        $aa = $this->Common->three_cond_row('hris_applications','empEmail',$staff->empEmail,'jobID',$this->uri->segment(4),'pre_school',$this->uri->segment(5)); 
                    }

                    if (empty($aa)) {
                        $aa = $this->db
                            ->where('empEmail', $staff->empEmail)
                            ->where('jobID', $this->uri->segment(4))
                            ->order_by('appID','DESC')
                            ->limit(1)
                            ->get('hris_applications')
                            ->row();
                    }

                    $rating = $this->Common->one_cond_row('hris_rating_promotion','appID',$aa->appID ?? null); 
                    $pt = $this->Common->one_cond_row('hris_positions','title',$job->jobTitle);
                    $ptp = $this->Common->one_cond_row('hris_position_points','id',$pt->bracket);
                    $inquery = $this->Common->one_cond_count_row('hris_application_inquiry', 'application_id', $aa->appID);
                    $applicantInquery = $this->Common->two_cond_count_row('hris_application_inquiry', 'application_id', $aa->appID, 'res', $user->username ?? $this->session->username);
                    $open = $this->Page_model->get_single_row_by_id('settings', 'id', 7);
                    $pt = $this->Common->one_cond_row('hris_positions','title',$job->jobTitle);
                    //$ptp = $this->Common->one_cond_row('hris_position_points','id',$pt->bracket);
                    $dq_hide = $this->Common->one_cond_row('settings', 'id', 10);
                    $canUploadDocuments = (int)($aa->stat ?? 1) === 0;
                    $jobTypes = [
                    1 => '- Elementary',
                    2 => '- Secondary',
                    3 => '- Junior High School',
                    4 => '- Senior High School',
                    5 => '- Kindergarten',
                    6 => '- IPED Elementary',
                    7 => '- IPED Secondary',
                    8 => '- IPED Junior High School',
                    9 => '- IPED Senior High School',
                    10 => '- SNED',
                    11 => '- SHS Academic and Core Subjects',
                    12 => '- SHS Arts and Design Track',
                    13 => '- SHS Sports Track',
                    14 => '- SHS Technical-Vocational(TVL) Track',
                    15 => '- Elementary - SPIMS',
                    16 => '- Junior High School - SPIMS',
                    17 => '- DOST - (RA 7687)',
                    18 => '- DOST - (RA 10612)',
                    19 => '- (SST I)',
                    20 => '- FOR TESTING PURPOSES (DO NOT APPLY)'
                ];
                ?>
           

            

            <div class="content-page">
                <div class="content"> 
                   
                
                    <!-- Start Content-->
                    <div class="container-fluid">
                       

                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box">
                                    <?php if($this->session->position == 'asds'){?>
                                    <a target="_blank" href="<?= base_url(); ?>Pages/request_to_rr/<?= $this->uri->segment(3); ?>/<?= $this->uri->segment(4); ?>/<?= $aa->appID?>" class="btn btn-info">Request to Retain Rating</a>
                                    <a href="#" class="btn btn-primary waves-effect waves-light open-AddBookDialog" data-id="<?= $aa->appID; ?>" data-item="<?= $aa->empEmail; ?>" data-toggle="modal" data-target=".bs-example-modal-center">copy</a>
                                    <?php } ?>

                                    <?php if($this->session->position == 'raters'){?>
                                    <a href="#" class="btn btn-primary waves-effect waves-light open-AddBookDialog" data-id="<?= $aa->appID; ?>" data-item="<?= $aa->empEmail; ?>" data-toggle="modal" data-target=".bs-example-modal-center">copy</a>
                                    <?php } ?>

                                    <div class="clearfix"></div>
                                    
                                    <?php if($this->session->position != "reg"){ ?>
                                    <div class="page-title-right">
                                        <ol class="breadcrumb p-0 m-0">
                                            <li class="breadcrumb-item"><a href="<?= base_url(); ?>Pages/evaluator_applicant/<?= $this->uri->segment(4); ?>">List of Applicants</a></li>
                                            <li class="breadcrumb-item active">Applicant Applicantion </li>
                                        </ol>
                                    </div>
                                    <?php } ?>
                                    <!-- <h4 class="page-title">Basic Tables</h4> -->

                                    <?php if($staff->empEmail == 'jhunlio@gmail.com' || $staff->empEmail == 'joselito.edong@deped.gov.ph'){ ?>
                                    <div class="alert alert-icon alert-danger alert-dismissible fade show" role="alert">
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                            <i class="mdi mdi-block-helper mr-2"></i>
                                            <strong>Oh snap!</strong> Developer's test application please don't mind it.
                                        </div>
                                    <?php } ?>

                                   
                                    <div class="clearfix"></div>
                                    <?php if($this->session->flashdata('success')) : ?>

                                        <?= '<div class="alert alert-success alert-dismissible fade show" role="alert">
                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>'
                                                .$this->session->flashdata('success'). 
                                            '</div>'; 
                                        ?>
                                    <?php endif; ?>

                                    <?php if($this->session->flashdata('danger')) : ?>
                                        <?= '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>'
                                                .$this->session->flashdata('danger'). 
                                            '</div>'; 
                                        ?>
                                    <?php endif;  ?>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->
                        <?php 
                            //$app = $this->Common->three_cond('hris_applications', 'empEmail', $staff->empEmail,'app_year',date('Y'),'jobID',$this->uri->segment(4)); 
                            
                            $ca = $this->Common->two_cond_count_row('hris_applications', 'empEmail', $staff->empEmail,'app_year',date('Y')); 

                            if($ca->num_rows() >= 1){
                        ?>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped mb-0">
                                                <thead>
                                                    <tr class="bg-info text-center text-white">
                                                        <th colspan="2">APPLICATION DETAILS &nbsp; &nbsp; &nbsp;
                                                            <a href="<?= base_url(); ?>Pages/application_history/<?= $this->uri->segment(3); ?>/<?= $this->uri->segment(4); ?>/<?= $aa->appID; ?>/<?= $this->uri->segment(5); ?>"><i class="mdi mdi-notebook-multiple tooltips text-white" data-placement="top" data-toggle="tooltip" data-original-title="View Application Status"></i></a> &nbsp; &nbsp;
                                                            
                                                        </th> 
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if($staff->pre_school != ""){?>
                                                    
                                                    <?php } ?>
                                                    <?php
                                                        //$jv = $this->Common->one_cond_row('hris_jobvacancy', 'jobID',$row->jobID);
                                                        //$s = $this->Common->one_cond_row('schools', 'schoolID',$row->pre_school);
                                                        
                                                        
                                                        $app = $this->Common->three_cond_row('hris_applications','applicant_id',$this->uri->segment(3),'jobID',$this->uri->segment(4),'pre_school',$this->uri->segment(5));
                                           
                                                        $s = $this->Common->one_cond_row('schools', 'schoolID',$this->uri->segment(5));
                                                    
                                                        ?>
                                                    <tr>
                                                        <th width="30%" class="text-right">Position Applied</th>
                                                        <td style="background: #9ddcf4; color:#222"><?= $job->jobTitle; ?> <?= $jobTypes[$job->job_type] ?? ''; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th class="text-right">District</th>
                                                        <td style="background: #9ddcf4; color:#222"><?php if($job->sy != '2023'){?><?= $s->district; ?><?php } ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th class="text-right">Preferred School</th>
                                                        
                                                        <td style="background: #9ddcf4; color:#222"><?php if($job->sy != '2023'){?><?= $s->schoolName; ?><?php } ?></td>
                                                    </tr>
                                                    <?php $this->load->view('pages/partials/online_demo_row', [
                                                        'online_demo_enabled' => $online_demo_enabled ?? false,
                                                        'application' => $aa,
                                                        'applicant_id' => $this->uri->segment(3),
                                                        'job_id' => $this->uri->segment(4),
                                                        'school_id' => $this->uri->segment(5),
                                                    ]); ?>
                                                    <tr>
                                                        <th class="text-right">Status</th>
                                                        <td style="background: #9ddcf4;">
                                                            <?php if($dq_hide->status == 0){?>
                                                            <?php $dqs = $this->Common->one_cond_row('hris_app_dq', 'appID', $aa->appID); ?>
                                                            <span class="badge badge-warning"><?php if($aa->dq == 2){ ?> 
                                                                 Disqualified : <?= $dqs->reason; ?> 
                                                                <?php }else{echo $aa->appStatus; } ?></span>
                                                            <?php }else{ ?>
                                                                <span class="badge badge-warning">Application Submitted</span>
                                                            <?php } ?>
                                                        </td>
                                                    </tr>
                                                    <?php if(!empty($rating)){ ?>
                                                    <?php //if($open->status == 0){if($this->session->position == "reg" || $this->session->position == "asds"){ ?>
                                                    <?php if($open->status == 0){ ?>
                                                    <?php //if($this->session->position == "admin"){ ?>
                                                    <?php //if($rating->interview != 0.00001){ ?>
                                                    <?php //if($rating->written != 0.00001){ ?>
                                                    <?php if($aa->appStatus == "Rated"){ ?>
                                                    <tr>
                                                        <th class="text-right">Action</th>
                                                        <td style="background: #bbecfe;">
                                                            <p class="text-dark">Click CONFIRM if you agree with the results otherwise click WITH QUERY and input your query in the box provided.<br /> (Note: <i>you can only submit query ONCE therefore make sure to read again before clicking SUBMIT.</i>)</p>
                                                            <a onclick="return confirm('Are you sure?')" class="btn btn-purple btn-sm" href="<?= base_url(); ?>Pages/confirmation/<?= $this->uri->segment(3); ?>/<?= $this->uri->segment(4); ?>/<?= $this->uri->segment(5); ?>/<?= $aa->appID; ?>/<?= $this->uri->segment(2); ?>">CONFIRM</a> &nbsp; &nbsp;
                                                            <!-- <a class="btn btn-success btn-sm" href="<?= base_url(); ?>Pages/inquiry/<?= $this->uri->segment(3); ?>/<?= $this->uri->segment(4); ?>/<?= $this->uri->segment(5); ?>/<?= $aa->appID; ?>"></a> -->
                                                            <?php if($applicantInquery->num_rows() == 0){ ?>
                                                                <a href="#" class="btn btn-info btn-sm" data-toggle="modal" data-target=".comment">WITH QUERY</a>
                                                            <?php } ?>
                                                            
                                                        </td>
                                                    </tr>
                                                    <?php }}} ?>

                                                    <?php if($aa->appStatus == 'Confirmed'){ ?>
                                                    <tr>
                                                        <th class="text-right">Individual Evaluation Sheet</th>
                                                        <td style="background: #bbecfe;">
                                                            <?php if($job->promotion == 1){?>
                                                                <a href="<?= base_url(); ?>Pages/ies_promotion/<?= $staff->record_no; ?>/<?= $aa->appID; ?>/<?= $this->uri->segment(4); ?>" target="_blank" class="btn btn-info btn-sm">View</a>
                                                            <?php }else{ ?>
                                                                <a href="<?= base_url(); ?>Pages/ies/<?= $staff->record_no; ?>/<?= $aa->appID; ?>/<?= $this->uri->segment(4); ?>" target="_blank" class="btn btn-info btn-sm">View</a>
                                                            <?php } ?>
                                                        </td>
                                                    </tr>
                                                    <?php } ?>


                                                    <?php if($inquery->num_rows() >= 1){ ?>
                                                    <tr>
                                                        <th class="text-right"></th>
                                                        <td style="background: #9ddcf4;">
                                                        <a href="<?= base_url(); ?>Pages/inquiry_non/<?= $this->uri->segment(3); ?>/<?= $job->jobID; ?>/<?= $aa->pre_school; ?>/<?= $aa->appID; ?>" class="btn btn-info btn-sm">QUERY</a>
                                                        </td>
                                                    </tr>
                                                    <?php } ?>

                                                    
                                                    
                                                    
                                                    <tr>
                                                        <td colspan="2"></td>
                                                    </tr>

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php } ?>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped mb-0">
                                            <thead>
                                                    <tr class="bg-primary text-white">
                                                        <th colspan="2" class="text-center">APPLICANT'S INFORMATION<?php if($this->session->c_id == $user->user_id || $this->session->position == 'asds'){?><a href="#" data-toggle="modal" data-target=".ai"><i class="fas fa-marker btn btn-sm tooltips" data-placement="top" data-toggle="tooltip" data-original-title="Edit"></i></a></a><?php } ?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    
                                                    <tr>
                                                        <th class="text-right" rowspan="6"  width="30%">
                                                        <?php if($this->session->c_id == $user->user_id){?><a onMouseOver="this.style.opacity='0.6'" onMouseOut="this.style.opacity='1'" href="<?= base_url()?>Page/changeDP"><?php } ?>
                                                        <img style="width: 2in; height: 2in; padding:0; margin:0" src="<?= base_url(); ?>/uploads/profile/<?php if($user->image != ""){echo $user->image;}else{
                                                            if(isset($user->sex)){if($user->sex == 0){echo "icon/m.jpg";}else{echo "icon/f.jpg";}}
                                                        } ?>" alt="">
                                                        <?php if($this->session->c_id == $user->user_id){?></a><?php } ?>
                                                            
                                                        </th>
                                                        <td class="text-left"><b><h4>
                                                            <?= strtoupper($staff->LastName).' '.strtoupper($staff->NameExtn).', '.strtoupper($staff->FirstName).' '.strtoupper($staff->MiddleName); ?></h4></b>
                                                            <h6 class="text-success">
                                                            <?= strtoupper($staff->record_no); ?>
                                                        </h6>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-left"><?= strtoupper($staff->resVillage); ?>, <?= strtoupper($staff->resBarangay); ?>, <?= strtoupper($staff->resCity); ?>, <?= strtoupper($staff->resProvince); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-left"><?= strtoupper($staff->resCity); ?>, <?= strtoupper($staff->resProvince); ?></td>
                                                    </tr>
                                                    <tr id="appen">
                                                        <td class="text-left"><?= $staff->contactNo; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-left"><?=$staff->Sex; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-left"><?= $staff->empEmail; ?></td>
                                                    </tr>

                                                    <tr class="bg-success text-white">
                                                        
                                                        <th colspan="2" class="text-center">  APPENDICES <?php //if($canUploadDocuments){if($this->session->c_id == $user->user_id){?><a href="#"  data-toggle="modal" data-target=".ept"><!--<i class="fas fa-marker btn btn-sm tooltips" data-placement="top" data-toggle="tooltip" data-original-title="Edit"></i> --></a><?php //}} ?></th>
                                                    </tr>
                                                    <tr>
                                                        <th class="text-right">Annex C - Checklist of Requirement duly notarized</th>
                                                        <td class="text-left"  style="background: #90bdf9; color:#464545">
                                                            <?php if($staff->omnibus != ""){?><a href="<?= base_url(); ?>Pages/pdf/<?= $staff->id; ?>/omnibus/?label=Omnibus Sworn Statement" target="_blank" class="tooltips" data-placement="top" data-toggle="tooltip" data-original-title="View File Attachment"><i  class="fas fa-file-alt btn btn-lg"></i></a><?php } ?>
                                                            <?php if($canUploadDocuments){if($this->session->c_id == $user->user_id){?>
                                                                <a href="#"  data-toggle="modal" data-target=".omni"><i class="fas fa-paperclip fas btn btn-lg tooltips" data-placement="top" data-toggle="tooltip" data-original-title="Attach File"></i></a>
                                                                <?php if($staff->omnibus != ""){ ?><a onclick="return confirm('Are you sure?')" href="<?= base_url(); ?>Pages/remove_attachment/<?= $this->uri->segment(3).'/'. $this->uri->segment(4).'/'. $this->uri->segment(5); ?>/omnibus" class="btn btn-warning"><i class="mdi mdi-block-helper mr-2 text-danger tooltips" data-placement="top" data-toggle="tooltip" data-original-title="Remove Attachment"></i>Remove Attachment</a><?php } ?>
                                                                <?php }} ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th class="text-right">Letter of Intent</th>
                                                        <td class="text-left"  style="background: #90bdf9; color:#464545">
                                                            <?php if($aa->application != ""){?><a href="<?= base_url(); ?>Pages/pdfv2/<?= $aa->appID; ?>/application/?label=Letter of Intent" target="_blank" class="tooltips" data-placement="top" data-toggle="tooltip" data-original-title="View File Attachment"><i  class="fas fa-file-alt btn btn-lg"></i></a><?php } ?>
                                                            <?php if($canUploadDocuments){if($this->session->c_id == $user->user_id){?>
                                                                <a href="#"  data-toggle="modal" data-target=".apfile"><i class="fas fa-paperclip fas btn btn-lg tooltips" data-placement="top" data-toggle="tooltip" data-original-title="Attach File"></i></a>
                                                                <?php if($aa->application != ""){ ?><a onclick="return confirm('Are you sure?')" href="<?= base_url(); ?>Pages/remove_attachment_app_none/<?= $this->uri->segment(3).'/'. $this->uri->segment(4).'/'. $this->uri->segment(5); ?>/application/<?= $aa->appID; ?>" class="btn btn-warning"><i class="mdi mdi-block-helper mr-2 text-danger tooltips" data-placement="top" data-toggle="tooltip" data-original-title="Remove Attachment"></i>Remove Attachment</a><?php } ?>
                                                                
                                                                <?php }} ?>
                                                        </td>
                                                    </tr>
                                                    
                                                   
                                                    <tr>
                                                        <th class="text-right">Personal Data Sheet</th>
                                                        <td class="text-left"  style="background: #90bdf9; color:#464545">
                                                            <?php if($staff->pdsfile != ""){?><a href="<?= base_url(); ?>Pages/pdf/<?= $staff->id; ?>/pdsfile/?label=Personal Data Sheet" target="_blank" class="tooltips" data-placement="top" data-toggle="tooltip" data-original-title="View File Attachment"><i  class="fas fa-file-alt btn btn-lg"></i></a><?php } ?>
                                                            <?php if($canUploadDocuments){if($this->session->c_id == $user->user_id){?>
                                                                <a href="#"  data-toggle="modal" data-target=".pdsfile"><i class="fas fa-paperclip fas btn btn-lg tooltips" data-placement="top" data-toggle="tooltip" data-original-title="Attach File"></i></a>
                                                                <?php if($staff->pdsfile != ""){ ?><a onclick="return confirm('Are you sure?')" href="<?= base_url(); ?>Pages/remove_attachment/<?= $this->uri->segment(3).'/'. $this->uri->segment(4).'/'. $this->uri->segment(5); ?>/pdsfile" class="btn btn-warning"><i class="mdi mdi-block-helper mr-2 text-danger tooltips" data-placement="top" data-toggle="tooltip" data-original-title="Remove Attachment"></i>Remove Attachment</a><?php } ?>
                                                                
                                                            <?php }} ?>
                                                        </td>
                                                    </tr>



                                                    <tr class="bg-warning text-white">
                                                        <th colspan="2" class="text-center" id="efile">EDUCATION (<?= $ptp->educ; ?>)</th>
                                                    </tr>

                                                    
                                                    <tr id="tsc">
                                                        <th class="text-right">TOR & CAV - College / Graduate Studies</th>
                                                        <td class="text-left" style="background: #f3d57d; color:#464545">
                                                            <?php if($staff->tor_cav != ""){?><a href="<?= base_url(); ?>Pages/pdf/<?= $staff->id; ?>/tor_cav/?label=TOR & CAV - College / Graduate Studies" target="_blank" class="tooltips" data-placement="top" data-toggle="tooltip" data-original-title="View File Attachment"><i  class="fas fa-file-alt btn btn-lg"></i></a><?php } ?>
                                                            <?php if($canUploadDocuments){if($this->session->c_id == $user->user_id){?>
                                                                <a href="#" data-toggle="modal" data-target=".tor_cav"><i class="fas fa-paperclip btn btn-lg tooltips" data-placement="top" data-toggle="tooltip" data-original-title="Attach File"></i></a>
                                                                <?php if($staff->tor_cav != ""){ ?><a onclick="return confirm('Are you sure?')" href="<?= base_url(); ?>Pages/remove_attachment/<?= $this->uri->segment(3).'/'. $this->uri->segment(4).'/'. $this->uri->segment(5); ?>/tor_cav" class="btn btn-warning"><i class="mdi mdi-block-helper mr-2 text-danger tooltips" data-placement="top" data-toggle="tooltip" data-original-title="Remove Attachment"></i>Remove Attachment</a><?php } ?>
                                                                
                                                            <?php }} ?>
                                                        </td>
                                                    </tr>
                                                    <?php if(!empty($rating)){?>
                                                    <tr>
                                                        <th class="text-right">Rating</th>
                                                        <td class="text-left" style="background: #fde18e; color:#464545">
                                                          
                                                        <?php if($this->session->position == 'Evaluator' || $this->session->position == 'asds' || $this->session->position == 'raters' || $this->session->position == 'Secretariat'){?>
                                                            <?php if($rating->eval_id1 == 0){ ?>
                                                                                            
                                                            <?php if($rating->educ != 0.00001){echo $rating->educ; } ?>
                                                            <a href="#" data-toggle="modal" data-target=".educrating"><i class="mdi mdi-notebook-outline btn btn-lg tooltips <?php if($rating->educ != 0.00001){echo 'text-success'; } ?>" data-placement="top" data-toggle="tooltip" data-original-title="Rate"></i></a>
                                                            
                                                        <?php }else{ ?>

                                                            <?php if($rating->eval_id1 == $this->session->id || $this->session->position == 'asds' || $this->session->position == 'raters' || $this->session->position == 'Secretariat'){if($rating->educ != 0.00001){echo $rating->educ; }  ?>
                                                            <a href="#" data-toggle="modal" data-target=".educrating"><i class="mdi mdi-notebook-outline btn btn-lg tooltips <?php if($rating->educ != 0.00001){echo 'text-success'; } ?>" data-placement="top" data-toggle="tooltip" data-original-title="Rate"></i></a>
                                                            <?php } ?>
                                                            
                                                            <?php } ?>
                                                        
                                                            <?php }else{
                                                           if($rating->educ != 0.00001){ ?>
                                                                <?php if($open->status == 0){?>
                                                                    <?= number_format($rating->educ, 3); ?>
                                                                <?php }else{?> 
                                                                    <span class="badge badge-info noti-icon-badge">Rated</span>
                                                                <?php } ?>
                                                           <?php }else{ ?>
                                                                <span class="badge badge-purple noti-icon-badge">Not Yet Rated</span>
                                                           <?php } ?>
                                                           
                                                        <?php } ?>


                                                        </td>
                                                    </tr>
                                                    <?php } ?>

                                                    <tr class="bg-info text-white">
                                                        <th colspan="2" class="text-center" id="ept">TRAININGS AND SEMINARS (<?= $ptp->tr; ?>)</th>
                                                    </tr>

                                                    <?php 
                                                        $educ=array(
                                                            "TESDA Certificates"=>$staff->tc
                                                    );
                                                    ?>

                                                    <tr>
                                                        <th class="text-right">Training/Seminar Certificates</th>
                                                        <td class="text-left" style="background: #9ddcf4; color:#464545">
                                                        <?php if($staff->tscfile != ""){?><a href="<?= base_url(); ?>Pages/pdf/<?= $staff->id; ?>/tscfile/?label=Training/Seminar Certificates" target="_blank" class="tooltips" data-placement="top" data-toggle="tooltip" data-original-title="View File Attachment"><i  class="fas fa-file-alt btn btn-lg"></i></a><?php } ?>
                                                            <?php if($canUploadDocuments){if($this->session->c_id == $user->user_id){?>
                                                                <a href="#" data-toggle="modal" data-target=".tscfile"><i class="fas fa-paperclip fas btn btn-lg tooltips" data-placement="top" data-toggle="tooltip" data-original-title="Attach File"></i></a>
                                                                <?php if($staff->tscfile != ""){ ?><a onclick="return confirm('Are you sure?')" href="<?= base_url(); ?>Pages/remove_attachment/<?= $this->uri->segment(3).'/'. $this->uri->segment(4).'/'. $this->uri->segment(5); ?>/tscfile" class="btn btn-warning"><i class="mdi mdi-block-helper mr-2 text-danger tooltips" data-placement="top" data-toggle="tooltip" data-original-title="Remove Attachment"></i>Remove Attachment</a><?php } ?>
                                                                
                                                            <?php }} ?>
                                                        </td>
                                                    </tr>

                                                    
                                                    <?php if(!empty($rating)){?>
                                                    <tr>
                                                        <th class="text-right">Rating</th>
                                                        <td class="text-left" style="background: #fde18e; color:#464545">
                                                        <?php if($this->session->position == 'Evaluator' || $this->session->position == 'asds' || $this->session->position == 'raters' || $this->session->position == 'Secretariat'){?>
                                                            <?php if($rating->eval_id1 == 0){ ?>
                                                                                            
                                                            <?php if($rating->trainings != 0.00001){echo $rating->trainings; } ?>
                                                            <a href="#" data-toggle="modal" data-target=".certrating"><i class="mdi mdi-notebook-outline btn btn-lg tooltips <?php if($rating->trainings != 0.00001){echo 'text-success'; } ?>" data-placement="top" data-toggle="tooltip" data-original-title="Rate"></i></a>
                                                            
                                                        <?php }else{ ?>

                                                            <?php if($rating->eval_id1 == $this->session->id || $this->session->position == 'asds' || $this->session->position == 'raters' || $this->session->position == 'Secretariat'){if($rating->trainings != 0.00001){echo $rating->trainings; }  ?>
                                                            <a href="#" data-toggle="modal" data-target=".certrating"><i class="mdi mdi-notebook-outline btn btn-lg tooltips <?php if($rating->trainings != 0.00001){echo 'text-success'; } ?>" data-placement="top" data-toggle="tooltip" data-original-title="Rate"></i></a>
                                                            <?php } ?>
                                                            
                                                            <?php } ?>
                                                        
                                                            <?php }else{
                                                           if($rating->trainings != 0.00001){ ?>
                                                                <?php if($open->status == 0){?>
                                                                    <?= number_format($rating->trainings,3); ?>
                                                                <?php }else{?> 
                                                                    <span class="badge badge-info noti-icon-badge">Rated</span>
                                                                <?php } ?>
                                                           <?php }else{ ?>
                                                                <span class="badge badge-purple noti-icon-badge">Not Yet Rated</span>
                                                           <?php } ?>
                                                           
                                                        <?php } ?>
                                                        </td>
                                                    </tr>
                                                    <?php } ?>

                                                    <tr class="bg-danger text-white">
                                                        <th colspan="2" class="text-center" id="lr">WORK EXPERIENCE (<?= $ptp->exp; ?>)</th>
                                                    </tr>
                                                    <tr>
                                                        <th class="text-right">Work Experiences (Attachments)</th>
                                                        <td class="text-left"  style="background: #fc9494; color:#464545">
                                                            <?php if($staff->wefile != ""){?><a href="<?= base_url(); ?>Pages/pdf/<?= $staff->id; ?>/wefile/?label=Work Experiences (Attachments)" target="_blank" class="tooltips" data-placement="top" data-toggle="tooltip" data-original-title="View File Attachment"><i  class="fas fa-file-alt btn btn-lg"></i></a><?php } ?>
                                                            <?php if($canUploadDocuments){if($this->session->c_id == $user->user_id){?>
                                                                <a href="#" data-toggle="modal" data-target=".wefile"><i class="fas fa-paperclip fas btn btn-lg tooltips" data-placement="top" data-toggle="tooltip" data-original-title="Attach File"></i></a>
                                                                <?php if($staff->wefile != ""){ ?><a onclick="return confirm('Are you sure?')" href="<?= base_url(); ?>Pages/remove_attachment/<?= $this->uri->segment(3).'/'. $this->uri->segment(4).'/'. $this->uri->segment(5); ?>/wefile" class="btn btn-warning"><i class="mdi mdi-block-helper mr-2 text-danger tooltips" data-placement="top" data-toggle="tooltip" data-original-title="Remove Attachment"></i>Remove Attachment</a><?php } ?>
                                                                
                                                            <?php }} ?>
                                                        </td>
                                                    </tr>

                                                    <?php if(!empty($rating)){?>
                                                    <tr>
                                                        <th class="text-right">Rating</th>
                                                        <td class="text-left" style="background: #fde18e; color:#464545">

                                                          <?php if($this->session->position == 'Evaluator' || $this->session->position == 'asds' || $this->session->position == 'raters' || $this->session->position == 'Secretariat'){?>
                                                            <?php if($rating->eval_id1 == 0){ ?>
                                                                                            
                                                            <?php if($rating->experience != 0.00001){echo $rating->experience; } ?>
                                                            <a href="#" data-toggle="modal" data-target=".werating"><i class="mdi mdi-notebook-outline btn btn-lg tooltips <?php if($rating->experience != 0.00001){echo 'text-success'; } ?>" data-placement="top" data-toggle="tooltip" data-original-title="Rate"></i></a>
                                                            
                                                        <?php }else{ ?>

                                                            <?php if($rating->eval_id1 == $this->session->id || $this->session->position == 'asds' || $this->session->position == 'raters' || $this->session->position == 'Secretariat'){if($rating->experience != 0.00001){echo $rating->experience; }  ?>
                                                            <a href="#" data-toggle="modal" data-target=".werating"><i class="mdi mdi-notebook-outline btn btn-lg tooltips <?php if($rating->experience != 0.00001){echo 'text-success'; } ?>" data-placement="top" data-toggle="tooltip" data-original-title="Rate"></i></a>
                                                            <?php } ?>
                                                            
                                                            <?php } ?>
                                                        
                                                            <?php }else{
                                                           if($rating->experience != 0.00001){ ?>
                                                                <?php if($open->status == 0){?>
                                                                    <?= number_format($rating->experience,0); ?>
                                                                <?php }else{?> 
                                                                    <span class="badge badge-info noti-icon-badge">Rated</span>
                                                                <?php } ?>
                                                           <?php }else{ ?>
                                                                <span class="badge badge-purple noti-icon-badge">Not Yet Rated</span>
                                                           <?php } ?>
                                                           
                                                        <?php } ?>
                                                          
                                                        </td>
                                                    </tr>
                                                    <?php } ?>

                                                    <tr class="bg-purple text-white">
                                                        <th colspan="2" class="text-center">ELIGIBILITY </th>
                                                    </tr>

                                                   
                                                        <th class="text-right">Eligibility (Attachment)</th>
                                                        <td class="text-left" style="background: #bbb7eb; color:#464545">
                                                            <?php if($staff->eligibility != ""){?><a href="<?= base_url(); ?>Pages/pdf/<?= $staff->id; ?>/eligibility/?label=Eligibility (Attachment)" target="_blank" class="tooltips" data-placement="top" data-toggle="tooltip" data-original-title="View File Attachment"><i  class="fas fa-file-alt btn btn-lg"></i></a><?php } ?>
                                                            <?php if($canUploadDocuments){if($this->session->c_id == $user->user_id){?>
                                                                <a href="#" data-toggle="modal" data-target=".elegfile"><i class="fas fa-paperclip fas btn btn-lg tooltips" data-placement="top" data-toggle="tooltip" data-original-title="Attach File"></i></a>
                                                                <?php if($staff->eligibility != ""){ ?><a onclick="return confirm('Are you sure?')" href="<?= base_url(); ?>Pages/remove_attachment/<?= $this->uri->segment(3).'/'. $this->uri->segment(4).'/'. $this->uri->segment(5); ?>/eligibility" class="btn btn-warning"><i class="mdi mdi-block-helper mr-2 text-danger tooltips" data-placement="top" data-toggle="tooltip" data-original-title="Remove Attachment"></i>Remove Attachment</a><?php } ?>
                                                                
                                                            <?php }} ?>
                                                        </td>
                                                    </tr>

                                                    <tr class="text-white" style="background-color:#B9B28A">
                                                        <th colspan="2" class="text-center" id="lr">PERFORMANCE RATING (30)</th>
                                                    </tr>
                                                    <tr>
                                                        <th class="text-right">Performance (Attachments)</th>
                                                        <td class="text-left"  style="background: #e2e1d9; color:#464545">
                                                            <?php if($staff->ipcrffile != ""){?><a href="<?= base_url(); ?>Pages/pdf/<?= $staff->id; ?>/ipcrffile/?label=IPCRF for Non-Teaching (Attachments)" target="_blank" class="tooltips" data-placement="top" data-toggle="tooltip" data-original-title="View File Attachment"><i  class="fas fa-file-alt btn btn-lg"></i></a><?php } ?>
                                                            <?php if($canUploadDocuments){if($this->session->c_id == $user->user_id){?>
                                                                <a href="#" data-toggle="modal" data-target=".ipcrffile"><i class="fas fa-paperclip fas btn btn-lg tooltips" data-placement="top" data-toggle="tooltip" data-original-title="Attach File"></i></a>
                                                                <?php if($staff->ipcrffile != ""){ ?><a onclick="return confirm('Are you sure?')" href="<?= base_url(); ?>Pages/remove_attachment/<?= $this->uri->segment(3).'/'. $this->uri->segment(4).'/'. $this->uri->segment(5); ?>/ipcrffile" class="btn btn-warning"><i class="mdi mdi-block-helper mr-2 text-danger tooltips" data-placement="top" data-toggle="tooltip" data-original-title="Remove Attachment"></i>Remove Attachment</a><?php } ?>
                                                                
                                                            <?php }} ?>
                                                        </td>
                                                    </tr>
                                                    <?php if(!empty($rating)){?>
                                                    <tr>
                                                        <th class="text-right">Rating </th>
                                                        <td class="text-left" style="background: #fde18e; color:#464545">
                                                          <?php if($this->session->position == 'Evaluator' || $this->session->position == 'asds' || $this->session->position == 'raters' || $this->session->position == 'Secretariat'){?>
                                                            <?php if($rating->eval_id1 == 0){ ?>

                                                            <?php if($rating->performance != 0.00001){echo number_format($rating->performance, 2); } ?>
                                                            <a href="#" data-toggle="modal" data-target=".performance"><i class="mdi mdi-notebook-outline btn btn-lg tooltips <?php if($rating->performance != 0.00001){echo 'text-success'; } ?>" data-placement="top" data-toggle="tooltip" data-original-title="Rate"></i></a>
                                                            
                                                        <?php }else{ ?>

                                                            <?php if($rating->eval_id1 == $this->session->id || $this->session->position == 'asds' || $this->session->position == 'raters' || $this->session->position == 'Secretariat'){if($rating->performance != 0.00001){echo $rating->performance; } ?>
                                                            <a href="#" data-toggle="modal" data-target=".performance"><i class="mdi mdi-notebook-outline btn btn-lg tooltips <?php if($rating->performance != 0.00001){echo 'text-success'; } ?>" data-placement="top" data-toggle="tooltip" data-original-title="Rate"></i></a>
                                                            <?php } ?>
                                                            
                                                            <?php } ?>
                                                        
                                                        <?php }else{
                                                           if($rating->performance != 0.00001){ ?>
                                                                <?php if($open->status == 0){?>
                                                                    <?= number_format($rating->performance, 3); ?>
                                                                <?php }else{?> 
                                                                    <span class="badge badge-info noti-icon-badge">Rated</span>
                                                                <?php } ?>
                                                           <?php }else{ ?>
                                                                <span class="badge badge-purple noti-icon-badge">Not Yet Rated</span>
                                                           <?php } ?>
                                                           
                                                        <?php } ?>
                                                        </td>
                                                    </tr>
                                                    <?php } ?>


                                                    <tr class="text-white" style="background-color:#7965C1">
                                                        <th colspan="2" class="text-center" id="lr">PPST COIs - Classroom Observation (25)</th>
                                                    </tr>
                                                    <tr>
                                                        <th class="text-right">PPST COIs (Attachments)</th>
                                                        <td class="text-left"  style="background: #c9bcf6; color:#464545">
                                                            <!-- <?php if($staff->ppstco != ""){?><a href="<?= base_url(); ?>Pages/pdf_staff/<?= $staff->record_no; ?>/ppstco/?label=PPST COIs (Attachments)" target="_blank" class="tooltips" data-placement="top" data-toggle="tooltip" data-original-title="View File Attachment"><i  class="fas fa-file-alt btn btn-lg"></i></a><?php } ?>
                                                            <?php if($canUploadDocuments){if($this->session->c_id == $user->user_id){?>
                                                                <a href="#" data-toggle="modal" data-target=".ppstco"><i class="fas fa-paperclip fas btn btn-lg tooltips" data-placement="top" data-toggle="tooltip" data-original-title="Attach File"></i></a>
                                                                <?php if($staff->ppstco != ""){ ?><a onclick="return confirm('Are you sure?')" href="<?= base_url(); ?>Pages/remove_attachment/<?= $this->uri->segment(3).'/'. $this->uri->segment(4).'/'. $this->uri->segment(5); ?>/ppstco" class="btn btn-warning"><i class="mdi mdi-block-helper mr-2 text-danger tooltips" data-placement="top" data-toggle="tooltip" data-original-title="Remove Attachment"></i>Remove Attachment</a><?php } ?>
                                                                
                                                            <?php }} ?> -->
                                                        </td>
                                                    </tr>
                                                    <?php if(!empty($rating)){?>
                                                    <tr>
                                                        <th class="text-right">Rating </th>
                                                        <td class="text-left" style="background: #fde18e; color:#464545">
                                                          <?php if($this->session->position == 'Evaluator' || $this->session->position == 'asds' || $this->session->position == 'raters' || $this->session->position == 'Secretariat'){?>
                                                            <?php if($rating->eval_id1 == 0){ ?>

                                                            <?php if($rating->ppstco != 0.00001){echo number_format($rating->ppstco, 2); } ?>
                                                            <a href="#" data-toggle="modal" data-target=".ppstcor"><i class="mdi mdi-notebook-outline btn btn-lg tooltips <?php if($rating->ppstco != 0.00001){echo 'text-success'; } ?>" data-placement="top" data-toggle="tooltip" data-original-title="Rate"></i></a>
                                                            
                                                        <?php }else{ ?>

                                                            <?php if($rating->eval_id1 == $this->session->id || $this->session->position == 'asds' || $this->session->position == 'raters' || $this->session->position == 'Secretariat'){if($rating->ppstco != 0.00001){echo $rating->ppstco; } ?>
                                                            <a href="#" data-toggle="modal" data-target=".ppstcor"><i class="mdi mdi-notebook-outline btn btn-lg tooltips <?php if($rating->ppstco != 0.00001){echo 'text-success'; } ?>" data-placement="top" data-toggle="tooltip" data-original-title="Rate"></i></a>
                                                            <?php } ?>
                                                            
                                                            <?php } ?>
                                                        
                                                        <?php }else{
                                                           if($rating->ppstco != 0.00001){ ?>
                                                                <?php if($open->status == 0){?>
                                                                    <?= number_format($rating->ppstco, 3); ?>
                                                                <?php }else{?> 
                                                                    <span class="badge badge-info noti-icon-badge">Rated</span>
                                                                <?php } ?>
                                                           <?php }else{ ?>
                                                                <span class="badge badge-purple noti-icon-badge">Not Yet Rated</span>
                                                           <?php } ?>
                                                           
                                                        <?php } ?>
                                                        </td>
                                                    </tr>
                                                    <?php } ?>

                                                    <tr class="text-white" style="background-color:#F2C078">
                                                        <th colspan="2" class="text-center" id="lr">PPST NCOIs - Portfolio Annotations and BEI (15)</th>
                                                    </tr>
                                                    <tr>
                                                        <th class="text-right">PPST COIs (Attachments)</th>
                                                        <td class="text-left"  style="background: #f8e2c3; color:#464545">
                                                            <!-- <?php if($staff->ppstpa != ""){?><a href="<?= base_url(); ?>Pages/pdf_staff/<?= $staff->record_no; ?>/ppstpa/?label=PPST NCOIs - Portfolio Annotations and BEI (Attachments)" target="_blank" class="tooltips" data-placement="top" data-toggle="tooltip" data-original-title="View File Attachment"><i  class="fas fa-file-alt btn btn-lg"></i></a><?php } ?>
                                                            <?php if($canUploadDocuments){if($this->session->c_id == $user->user_id){?>
                                                                <a href="#" data-toggle="modal" data-target=".ppstpa"><i class="fas fa-paperclip fas btn btn-lg tooltips" data-placement="top" data-toggle="tooltip" data-original-title="Attach File"></i></a>
                                                                <?php if($staff->ppstpa != ""){ ?><a onclick="return confirm('Are you sure?')" href="<?= base_url(); ?>Pages/remove_attachment/<?= $this->uri->segment(3).'/'. $this->uri->segment(4).'/'. $this->uri->segment(5); ?>/ppstpa" class="btn btn-warning"><i class="mdi mdi-block-helper mr-2 text-danger tooltips" data-placement="top" data-toggle="tooltip" data-original-title="Remove Attachment"></i>Remove Attachment</a><?php } ?>
                                                                
                                                            <?php }} ?> -->
                                                        </td>
                                                    </tr>
                                                    <?php if(!empty($rating)){?>
                                                    <tr>
                                                        <th class="text-right">Rating </th>
                                                        <td class="text-left" style="background: #fde18e; color:#464545">
                                                          <?php if($this->session->position == 'Evaluator' || $this->session->position == 'asds' || $this->session->position == 'raters' || $this->session->position == 'Secretariat'){?>
                                                            <?php if($rating->eval_id1 == 0){ ?>

                                                            <?php if($rating->ppstpa != 0.00001){echo number_format($rating->ppstpa, 2); } ?>
                                                            <a href="#" data-toggle="modal" data-target=".ppstpar"><i class="mdi mdi-notebook-outline btn btn-lg tooltips <?php if($rating->ppstpa != 0.00001){echo 'text-success'; } ?>" data-placement="top" data-toggle="tooltip" data-original-title="Rate"></i></a>
                                                            
                                                        <?php }else{ ?>

                                                            <?php if($rating->eval_id1 == $this->session->id || $this->session->position == 'asds' || $this->session->position == 'raters' || $this->session->position == 'Secretariat'){if($rating->ppstpa != 0.00001){echo $rating->ppstpa; } ?>
                                                            <a href="#" data-toggle="modal" data-target=".ppstpar"><i class="mdi mdi-notebook-outline btn btn-lg tooltips <?php if($rating->ppstpa != 0.00001){echo 'text-success'; } ?>" data-placement="top" data-toggle="tooltip" data-original-title="Rate"></i></a>
                                                            <?php } ?>
                                                            
                                                            <?php } ?>
                                                        
                                                        <?php }else{
                                                           if($rating->ppstpa != 0.00001){ ?>
                                                                <?php if($open->status == 0){?>
                                                                    <?= number_format($rating->ppstpa, 3); ?>
                                                                <?php }else{?> 
                                                                    <span class="badge badge-info noti-icon-badge">Rated</span>
                                                                <?php } ?>
                                                           <?php }else{ ?>
                                                                <span class="badge badge-purple noti-icon-badge">Not Yet Rated</span>
                                                           <?php } ?>
                                                           
                                                        <?php } ?>
                                                        </td>
                                                    </tr>
                                                    <?php } ?>


                                                    
                         
                                                    <?php if($this->session->position == '0'){?>
                                                    <tr class="bg-warning text-white">
                                                        <th colspan="2" class="text-center" id="history">APPLICATION HISTORY</th>
                                                    </tr>

                                                    <?php
                                                        foreach($history as $row){
                                                        $jv = $this->Common->one_cond_row('hris_jobvacancy', 'jobID',$row->jobID);
                                                        $s = $this->Common->one_cond_row('schools', 'schoolID',$row->pre_school);
                                                    ?>
                                                    <tr>
                                                        <td class="text-right">Position Applied</td>
                                                        <td class="text-left"  style="background: #f7e8bc; color:#464545">
                                                            <span style="display:inline-block; width:70%"><?= $jv->jobTitle; ?> </span>
                                                            <a href="#" class="btn btn-success btn-sm">Status</a>
                                                            <a href="#" class="btn btn-primary btn-sm">Rating</a>
                                                        </td>
                                                      
                                                    </tr>
                                                    <?php } }?>



                                                    


                                                    <?php if($this->session->position != "reg"){ ?>

                                                    <tr class="bg-primary text-white">
                                                        <th colspan="2" class="text-center" id="history">ACTION</th>
                                                    </tr>

                                                    <tr>
                                                        <th class="text-right"></th>
                                                        <td style="background: #ceeffb;">
                                                            
                                                                


                                                                   
                                                               

                                                                <?php if($this->session->position==='Human Resource Admin' || $this->session->position==='HR Staff' || $this->session->position==='Super Admin' || $this->session->position==='asds' || $this->session->position==='doceval' || $this->session->position==='raters' || $this->session->position==='Secretariat') { ?>
                                                                                                                                    <?php if($aa->appStatus == "Endorsed for Rating"){ ?><a onclick="return confirm('Are you sure?')" class="btn btn-purple btn-sm" href="<?= base_url(); ?>Pages/rated/<?= $this->uri->segment(3); ?>/<?= $this->uri->segment(4); ?>/<?= $this->uri->segment(5); ?>/<?= $this->uri->segment(6); ?>/<?= $this->uri->segment(2); ?>">Rated</a><?php } ?>

                                                                    <?php if($aa->appStatus == "Application Submitted" && $aa->dq == 0){ ?>
                                                                    <a href="#"  data-toggle="modal" data-target=".dq" class="btn btn-info btn-sm">Remarks</a>
                                                                <?php } } ?>

                                                                <?php if($this->session->position==='asds' || $this->session->username==='Cyanne19' || $this->session->position==='Secretariat') { ?>
                                                                <?php if($aa->dq != 0){ ?>
                                                                    <a href="#"  data-toggle="modal" data-target=".dqedit" class="btn btn-info btn-sm">Edit Remarks</a>
                                                                    <?php 
                                                                        $ren = $this->Common->two_cond_count_row('hris_applications_rating', 'appID', $this->uri->segment(6),'record_no',$staff->empEmail); 
                                                                        if($ren->num_rows() == 0){
                                                                    ?>
                                                                    <a href="<?= base_url(); ?>pages/ir/<?= $job->job_type; ?>/<?= $job->sy; ?>/<?= $staff->empEmail; ?>/<?= $this->uri->segment(6); ?>/<?= $this->uri->segment(5); ?>/<?= $aa->jobID; ?>/<?= $this->uri->segment(3); ?>/<?= $this->uri->segment(6); ?>"  class="btn btn-primary btn-sm">Insert Rating</a>
                                                                    <?php } ?>
                                                                <?php } }  ?>


                                                                


                                                                <?php if($this->session->position == "reg"){ ?>
                                                                    <?php if($aa->appStatus == "Rated"){ ?><a class="btn btn-info btn-sm" href="<?= base_url(); ?>Pages/confirmation/<?= $this->uri->segment(3); ?>/<?= $this->uri->segment(4); ?>">Confirmation</a><?php } ?>
                                                                <?php } ?>
                                                            
                                                        </td>
                                                    </tr>

                                                    <?php } ?>
                                                    

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            
                        </div>
                        <!--- end row -->


                    </div>
                    <!-- end container-fluid -->

                </div>
                <!-- end content -->


                                        <!--  Modal content for the above example -->
                                        <div class="modal fade educ" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-warning">
                                                        <h5 class="modal-title" id="myLargeModalLabel">Update Education</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="card">
                                                                <div class="card-body">

                                                                    <form class="parsley-examples" action="<?= base_url(); ?>pages/update_educ_staff" method="post">
                                                                    <input type="hidden" name="id" value="<?= $staff->record_no; ?>">
                                                                    <input type="hidden" name="empEmail" value="<?= $staff->empEmail; ?>">
                                                                    <input type="hidden" name="jobID" value="<?= $this->uri->segment(4); ?>">
                                                                    <input type="hidden" name="school_id" value="<?= $this->uri->segment(5); ?>">
                                                                        
                                                                    <div class="row">
                                                                        <div class="col-lg-8">
                                                                                <div class="form-group">
                                                                                    <label>Bachelors Degree</label>
                                                                                    <input type="text" class="form-control" name="bd"  value="<?= $staff->bd; ?>" >
                                                                                </div>	
                                                                        </div>	
                                                                        <div class="col-lg-4">
                                                                                <div class="form-group">
                                                                                    <label>Date Graduated</label>
                                                                                    <input type="date" class="form-control" name="dg"  value="<?= $staff->dg; ?>" >
                                                                                </div>	
                                                                        </div>
                                                                        
                                                                    </div>	

                                                                    <div class="row">
                                                                        <div class="col-lg-4">
                                                                                <div class="form-group">
                                                                                    <label>Degree Units</label>
                                                                                    <input type="text" class="form-control" name="du"  value="<?= $staff->du; ?>" >
                                                                                </div>	
                                                                        </div>	
                                                                        <div class="col-lg-4">
                                                                                <div class="form-group">
                                                                                    <label>Degree GWA</label>
                                                                                    <input type="text" class="form-control" name="dgwa"  value="<?= $staff->dgwa; ?>" >
                                                                                </div>	
                                                                        </div>
                                                                        <div class="col-lg-4">
                                                                                <div class="form-group">
                                                                                    <label>Units Earned</label>
                                                                                    <input type="text" class="form-control" name="ue"  value="<?= $staff->ue; ?>" >
                                                                                </div>	
                                                                        </div>
                                                                        
                                                                    </div>	

                                                                    <div class="row">
                                                                        	
                                                                        <div class="col-lg-4">
                                                                                <div class="form-group">
                                                                                    <label>GWA Earned</label>
                                                                                    <input type="text" class="form-control" name="gwae"  value="<?= $staff->gwae; ?>" >
                                                                                </div>	
                                                                        </div>
                                                                        <div class="col-lg-4">
                                                                                <div class="form-group">
                                                                                    <label>Post Graduate</label>
                                                                                    <input type="text" class="form-control" name="pg"  value="<?= $staff->pg; ?>" >
                                                                                </div>	
                                                                        </div>
                                                                        <div class="col-lg-4">
                                                                                <div class="form-group">
                                                                                    <label>Major</label>
                                                                                    <input type="text" class="form-control" name="s"  value="<?= $staff->specialization; ?>" >
                                                                                </div>	
                                                                        </div>
                                                                        
                                                                    </div>
                                                                    
                                                                    <div class="row">

                                                                    <div class="col-lg-4">
                                                                                <div class="form-group">
                                                                                    <label>Post Graduate Units</label>
                                                                                    <input type="text" class="form-control" name="pgu"  value="<?= $staff->pgu; ?>" >
                                                                                </div>	
                                                                        </div>
                                                                        	
                                                                        <div class="col-lg-4">
                                                                                <div class="form-group">
                                                                                    <label>Junior HS Specialization</label>
                                                                                    <input type="text" class="form-control" name="jhss"  value="<?= $staff->jhss; ?>" >
                                                                                </div>	
                                                                        </div>
                                                                        <div class="col-lg-4">
                                                                                <div class="form-group">
                                                                                    <label>Senior HS Specialization</label>
                                                                                    <input type="text" class="form-control" name="shss"  value="<?= $staff->shss; ?>" >
                                                                                </div>	
                                                                        </div>
                                                                        
                                                                    </div>
                                                                        
                                                                        
                                                                        

                                                                        <div class="form-group text-right mb-0">
                                                                            <button class="btn btn-warning waves-effect waves-light mr-1" type="submit">
                                                                                Submit
                                                                            </button>
                                                                        </div>

                                                                    </form>
                                                                </div>
                                                            </div>
                                                            <!-- end card -->
                                                        </div>
                                                        <!-- end col -->

                            
                                                    </div>
                                                    <!-- end row -->






                                                    </div>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->


                                        <!--  Modal content for the above example -->
                                        <div class="modal fade ai" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-primary">
                                                        <h5 class="modal-title" id="myLargeModalLabel">Update Applicants Information</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="card">
                                                                <div class="card-body">

                                                                    <form class="parsley-examples" action="<?= base_url(); ?>pages/update_ai_staff" method="post">
                                                                    <input type="hidden" name="id" value="<?= $this->uri->segment(3); ?>">
                                                                    <input type="hidden" name="jobID" value="<?= $this->uri->segment(4); ?>">
                                                                    <input type="hidden" name="school_id" value="<?= $this->uri->segment(5); ?>">
                                                                        
                                                                    <div class="row">
                                                                        <div class="col-lg-4">
                                                                                <div class="form-group">
                                                                                    <label>First Name</label>
                                                                                    <input type="text" class="form-control" name="FirstName"  value="<?= $staff->FirstName; ?>" required>
                                                                                </div>	
                                                                        </div>	
                                                                        <div class="col-lg-3">
                                                                                <div class="form-group">
                                                                                    <label>Middle Name</label>
                                                                                    <input type="text" class="form-control" name="MiddleName"  value="<?= $staff->MiddleName; ?>" >
                                                                                </div>	
                                                                        </div>
                                                                        <div class="col-lg-3">
                                                                                <div class="form-group">
                                                                                    <label>Last Name</label>
                                                                                    <input type="text" class="form-control" name="LastName"  value="<?= $staff->LastName; ?>" required>
                                                                                </div>	
                                                                        </div>
                                                                        <div class="col-lg-2">
                                                                                <div class="form-group">
                                                                                    <label>Name Extn.</label>
                                                                                    <input type="text" class="form-control" name="NameExtn"  value="<?= $staff->NameExtn; ?>" >
                                                                                </div>	
                                                                        </div>
                                                                        
                                                                    </div>
                                                                    
                                                                    <div class="row">
                                                                        <div class="col-lg-4">
                                                                                <div class="form-group">
                                                                                    <label>Village</label>
                                                                                    <input type="text" class="form-control" name="resVillage"  value="<?= $staff->resVillage; ?>" >
                                                                                </div>	
                                                                        </div>	
                                                                        <div class="col-lg-4">
                                                                                <div class="form-group">
                                                                                    <label>Barangay</label>
                                                                                    <input type="text" class="form-control" name="resBarangay"  value="<?= $staff->resBarangay ; ?>" >
                                                                                </div>	
                                                                        </div>

                                                                        <div class="col-lg-4">
                                                                                <div class="form-group">
                                                                                    <label>City / Municipality</label>
                                                                                    <input type="text" class="form-control" name="resCity"  value="<?= $staff->resCity; ?>" >
                                                                                </div>	
                                                                        </div>
                                                                        
                                                                    </div>

                                                                    <div class="row">
                                                                        	
                                                                        <div class="col-lg-4">
                                                                                <div class="form-group">
                                                                                    <label>Province</label>
                                                                                    <input type="text" class="form-control" name="resProvince"  value="<?= $staff->resProvince; ?>" >
                                                                                </div>	
                                                                        </div>

                                                                        <div class="col-lg-4">
                                                                                <div class="form-group">
                                                                                    <label>Sex</label>
                                                                                    <select class="form-control" name="Sex" required>
                                                                                    <option <?php if($staff->Sex == "Male"){echo "selected";} ?> value="Male">Male</option>
                                                                                    <option <?php if($staff->Sex == "Female"){echo "selected";} ?> value="Female">Female</option>
                                                                                </select>
                                                                                </div>	
                                                                        </div>	
                                                                        <div class="col-lg-4">
                                                                                <div class="form-group">
                                                                                    <label>Contact</label>
                                                                                    <input type="text" class="form-control" name="contactNo"  value="<?= $staff->contactNo; ?>" >
                                                                                </div>	
                                                                        </div>
                                                                        
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-lg-6">
                                                                                <div class="form-group">
                                                                                    <label>Assigned as School Head/Classroom Teacher</label>
                                                                                    <select class="form-control" name="asht" required>
                                                                                    <option <?php if($staff->asht == "0"){echo "selected";} ?> value="0">Classroom Teacher</option>
                                                                                    <option <?php if($staff->asht == "1"){echo "selected";} ?> value="1">School Head</option>
                                                                                    <option <?php if($staff->asht == "2"){echo "selected";} ?> value="2">Detailed as Assistant SP</option>
                                                                                </select>
                                                                                </div>	
                                                                        </div>
                                                                        <div class="col-lg-6">
                                                                                <div class="form-group">
                                                                                    <label>Current Position</label>
                                                                                    <input type="text" class="form-control" name="cp"  value="<?= $staff->empPosition; ?>" >
                                                                                </div>	
                                                                        </div>
                                                                    </div>

                                                                    


                              
                                                                    
                                                                        <div class="form-group text-right mb-0">
                                                                            <button class="btn btn-primary waves-effect waves-light mr-1" type="submit">
                                                                                Submit
                                                                            </button>
                                                                        </div>

                                                                    </form>
                                                                </div>
                                                            </div>
                                                            <!-- end card -->
                                                        </div>
                                                        <!-- end col -->

                            
                                                    </div>
                                                    <!-- end row -->






                                                    </div>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->

                                        <!--  Modal content for the above example -->
                                        <div id="myModal" class="modal fade lr" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-purple">
                                                        <h5 class="modal-title text-white" id="myLargeModalLabel">Update LET Rating</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="card">
                                                                <div class="card-body">

                                                                    <form class="parsley-examples" action="<?= base_url(); ?>pages/update_lr_staff" method="post">
                                                                    <input type="hidden" name="id" value="<?= $staff->record_no; ?>">
                                                                    <input type="hidden" name="empEmail" value="<?= $staff->empEmail; ?>">
                                                                    <input type="hidden" name="jobID" value="<?= $this->uri->segment(4); ?>">
                                                                    <input type="hidden" name="school_id" value="<?= $this->uri->segment(5); ?>">
                                                                        
                                                                    <div class="row">
                                                                        <div class="col-lg-12">
                                                                                <div class="form-group">
                                                                                    <label>LET Rating</label>
                                                                                    <input type="text" class="form-control" name="lr"  value="<?= $staff->lr; ?>" >
                                                                                </div>	
                                                                        </div>	
                                                                        
                                                                    </div>
                                                                    

                              
                                                                    
                                                                        <div class="form-group text-right mb-0">
                                                                            <button class="btn btn-purple waves-effect waves-light mr-1" type="submit">
                                                                                Submit
                                                                            </button>
                                                                        </div>

                                                                    </form>
                                                                </div>
                                                            </div>
                                                            <!-- end card -->
                                                        </div>
                                                        <!-- end col -->

                            
                                                    </div>
                                                    <!-- end row -->






                                                    </div>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->

                                        <!--  Modal content for the above example -->
                                        <div id="myModal" class="modal fade cert" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-info">
                                                        <h5 class="modal-title" id="myLargeModalLabel">Certificates</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="card">
                                                                <div class="card-body">

                                                                    <form class="parsley-examples" action="<?= base_url(); ?>pages/update_tc_staff" method="post">
                                                                    <input type="hidden" name="id" value="<?= $staff->record_no; ?>">
                                                                    <input type="hidden" name="empEmail" value="<?= $staff->empEmail; ?>">
                                                                    <input type="hidden" name="jobID" value="<?= $this->uri->segment(4); ?>">
                                                                    <input type="hidden" name="school_id" value="<?= $this->uri->segment(5); ?>">
                                                                        
                                                                    <div class="row">
                                                                        <div class="col-lg-12">
                                                                                <div class="form-group">
                                                                                    <label>TESDA Certificates</label>
                                                                                    <input type="text" class="form-control" name="tc"  value="<?= $staff->tc; ?>" >
                                                                                </div>	
                                                                        </div>	
                                                                        
                                                                    </div>
                                                                    

                              
                                                                    
                                                                        <div class="form-group text-right mb-0">
                                                                            <button class="btn btn-info waves-effect waves-light mr-1" type="submit">
                                                                                Submit
                                                                            </button>
                                                                        </div>

                                                                    </form>
                                                                </div>
                                                            </div>
                                                            <!-- end card -->
                                                        </div>
                                                        <!-- end col -->

                            
                                                    </div>
                                                    <!-- end row -->






                                                    </div>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->


                                        <!--  Modal content for the above example -->
                                        <div id="myModal" class="modal fade ept" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-success">
                                                        <h5 class="modal-title" id="myLargeModalLabel">Update EPT and other APPENDICES</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="card">
                                                                <div class="card-body">

                                                                    <form class="parsley-examples" action="<?= base_url(); ?>pages/update_ept" method="post">
                                                                    <input type="hidden" name="id" value="<?= $staff->id; ?>">
                                                                    <input type="hidden" name="empEmail" value="<?= $staff->empEmail; ?>">
                                                                    <input type="hidden" name="jobID" value="<?= $this->uri->segment(4); ?>">
                                                                    <input type="hidden" name="school_id" value="<?= $this->uri->segment(5); ?>">
                                                                        
                                                                    <div class="row">
                                                                        <div class="col-lg-12">
                                                                                <div class="form-group">
                                                                                    <label>Do you have English Proficiency Test?</label>
                                                                                    <input type="text" class="form-control" name="ept"  value="<?= $staff->ept; ?>" >
                                                                                </div>	
                                                                        </div>	
                                                                        
                                                                    </div>

                                                                    <div class="row">
                                                                        <div class="col-lg-12">
                                                                                <div class="form-group">
                                                                                    <label>EPT Rating</label>
                                                                                    <input type="text" class="form-control" name="eptr"  value="<?= $staff->eptr; ?>" >
                                                                                </div>	
                                                                        </div>	
                                                                        
                                                                    </div>
                                                                    

                              
                                                                    
                                                                        <div class="form-group text-right mb-0">
                                                                            <button class="btn btn-success waves-effect waves-light mr-1" type="submit">
                                                                                Submit
                                                                            </button>
                                                                        </div>

                                                                    </form>
                                                                </div>
                                                            </div>
                                                            <!-- end card -->
                                                        </div>
                                                        <!-- end col -->

                            
                                                    </div>
                                                    <!-- end row -->






                                                    </div>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->


                                        <!--  Modal content for the above example -->
                                        <div id="myModal" class="modal fade educfile" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-warning">
                                                        <h5 class="modal-title text-white" id="myLargeModalLabel">Education Files</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="card">
                                                                <div class="card-body">

                                                                <?php 
                                                                    $attributes = array('class' => 'parsley-examples');
                                                                    echo form_open_multipart('pages/update_efile_staff', $attributes);
                                                                ?>
                                                                    <input type="hidden" name="id" value="<?= $staff->record_no; ?>">
                                                                    <input type="hidden" name="empEmail" value="<?= $staff->empEmail; ?>">
                                                                    <input type="hidden" name="jobID" value="<?= $this->uri->segment(4); ?>">
                                                                    <input type="hidden" name="school_id" value="<?= $this->uri->segment(5); ?>">
                                                                    
                                                                    
                                                                    <div class="form-group row">
                                                                        <label for="hori-pass1" class="col-md-4 col-form-label">Select PDF File<span class="text-danger">*</span></label>
                                                                        <div class="col-md-7">
                                                                            <input id="myInput" type="file"  name="file"  required class="form-control">
                                                                        </div>
                                                                    </div>

                                                                
                                                                    
                                                                    <div class="form-group row mb-0">
                                                                        <div class="col-md-8 offset-md-4">
                                                                            <button type="submit" class="btn btn-warning waves-effect waves-light mr-1">
                                                                                Submit
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                                </div>
                                                            </div>
                                                            <!-- end card -->
                                                        </div>
                                                        <!-- end col -->

                            
                                                    </div>
                                                    <!-- end row -->






                                                    </div>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->

                                        <!--  Modal content for the above example -->
                                        <div id="myModal" class="modal fade oafile" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-info">
                                                        <h5 class="modal-title text-white" id="myLargeModalLabel">Outstanding Accomplishments</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="card">
                                                                <div class="card-body">

                                                                <?php 
                                                                    $attributes = array('class' => 'parsley-examples');
                                                                    echo form_open_multipart('pages/update_outfile_staff', $attributes);
                                                                ?>
                                                                    <input type="hidden" name="id" value="<?= $staff->record_no; ?>">
                                                                    <input type="hidden" name="empEmail" value="<?= $staff->empEmail; ?>">
                                                                    <input type="hidden" name="jobID" value="<?= $this->uri->segment(4); ?>">
                                                                    <input type="hidden" name="school_id" value="<?= $this->uri->segment(5); ?>">
                                                                    
                                                                    
                                                                    <div class="form-group row">
                                                                        <label for="hori-pass1" class="col-md-4 col-form-label">Select PDF File<span class="text-danger">*</span></label>
                                                                        <div class="col-md-7">
                                                                            <input id="myInput" type="file"  name="file"  required class="form-control">
                                                                        </div>
                                                                    </div>

                                                                
                                                                    
                                                                    <div class="form-group row mb-0">
                                                                        <div class="col-md-8 offset-md-4">
                                                                            <button type="submit" class="btn btn-info waves-effect waves-light mr-1">
                                                                                Submit
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                                </div>
                                                            </div>
                                                            <!-- end card -->
                                                        </div>
                                                        <!-- end col -->

                            
                                                    </div>
                                                    <!-- end row -->






                                                    </div>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->

                                        <!--  Modal content for the above example -->
                                        <div id="myModal" class="modal fade aefile" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-info">
                                                        <h5 class="modal-title text-white" id="myLargeModalLabel">Application Of Education</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="card">
                                                                <div class="card-body">

                                                                <?php 
                                                                    $attributes = array('class' => 'parsley-examples');
                                                                    echo form_open_multipart('pages/update_aefile_staff', $attributes);
                                                                ?>
                                                                    <input type="hidden" name="id" value="<?= $staff->record_no; ?>">
                                                                    <input type="hidden" name="empEmail" value="<?= $staff->empEmail; ?>">
                                                                    <input type="hidden" name="jobID" value="<?= $this->uri->segment(4); ?>">
                                                                    <input type="hidden" name="school_id" value="<?= $this->uri->segment(5); ?>">
                                                                    
                                                                    
                                                                    <div class="form-group row">
                                                                        <label for="hori-pass1" class="col-md-4 col-form-label">Select PDF File<span class="text-danger">*</span></label>
                                                                        <div class="col-md-7">
                                                                            <input id="myInput" type="file"  name="file"  required class="form-control">
                                                                        </div>
                                                                    </div>

                                                                
                                                                    
                                                                    <div class="form-group row mb-0">
                                                                        <div class="col-md-8 offset-md-4">
                                                                            <button type="submit" class="btn btn-info waves-effect waves-light mr-1">
                                                                                Submit
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                                </div>
                                                            </div>
                                                            <!-- end card -->
                                                        </div>
                                                        <!-- end col -->

                            
                                                    </div>
                                                    <!-- end row -->






                                                    </div>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->

                                        <!--  Modal content for the above example -->
                                        <div id="myModal" class="modal fade ald" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-info">
                                                        <h5 class="modal-title text-white" id="myLargeModalLabel">Application Of Learning & Development</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="card">
                                                                <div class="card-body">

                                                                <?php 
                                                                    $attributes = array('class' => 'parsley-examples');
                                                                    echo form_open_multipart('pages/update_aldfile_staff', $attributes);
                                                                ?>
                                                                    <input type="hidden" name="id" value="<?= $staff->record_no; ?>">
                                                                    <input type="hidden" name="empEmail" value="<?= $staff->empEmail; ?>">
                                                                    <input type="hidden" name="jobID" value="<?= $this->uri->segment(4); ?>">
                                                                    <input type="hidden" name="school_id" value="<?= $this->uri->segment(5); ?>">
                                                                    
                                                                    
                                                                    <div class="form-group row">
                                                                        <label for="hori-pass1" class="col-md-4 col-form-label">Select PDF File<span class="text-danger">*</span></label>
                                                                        <div class="col-md-7">
                                                                            <input id="myInput" type="file"  name="file"  required class="form-control">
                                                                        </div>
                                                                    </div>

                                                                
                                                                    
                                                                    <div class="form-group row mb-0">
                                                                        <div class="col-md-8 offset-md-4">
                                                                            <button type="submit" class="btn btn-info waves-effect waves-light mr-1">
                                                                                Submit
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                                </div>
                                                            </div>
                                                            <!-- end card -->
                                                        </div>
                                                        <!-- end col -->

                            
                                                    </div>
                                                    <!-- end row -->






                                                    </div>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->

                                        <!--  Modal content for the above example -->
                                        <div id="myModal" class="modal fade tor_cav" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-warning">
                                                        <h5 class="modal-title text-white" id="myLargeModalLabel">TOR & CAV - College / Graduate Studies</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="card">
                                                                <div class="card-body">

                                                                <?php 
                                                                    $attributes = array('class' => 'parsley-examples');
                                                                    echo form_open_multipart('pages/update_efile', $attributes);
                                                                ?>
                                                                    <input type="hidden" name="id" value="<?= $staff->record_no; ?>">
                                                                    <input type="hidden" name="empEmail" value="<?= $staff->empEmail; ?>">
                                                                    <input type="hidden" name="jobID" value="<?= $this->uri->segment(4); ?>">
                                                                    <input type="hidden" name="school_id" value="<?= $this->uri->segment(5); ?>">
                                                                    
                                                                    
                                                                    <div class="form-group row">
                                                                        <label for="hori-pass1" class="col-md-4 col-form-label">Select PDF File<span class="text-danger">*</span></label>
                                                                        <div class="col-md-7">
                                                                            <input id="myInput" type="file"  name="file"  required class="form-control">
                                                                        </div>
                                                                    </div>

                                                                
                                                                    <div class="form-group row mb-0">
                                                                        <div class="col-md-8 offset-md-4">
                                                                            <button type="submit" class="btn btn-warning waves-effect waves-light mr-1">
                                                                                Submit
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                                </div>
                                                            </div>
                                                            <!-- end card -->
                                                        </div>
                                                        <!-- end col -->

                            
                                                    </div>
                                                    <!-- end row -->






                                                    </div>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->

                                        <!--  Modal content for the above example -->
                                        <div id="myModal" class="modal fade wefile" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-danger">
                                                        <h5 class="modal-title text-white" id="myLargeModalLabel">Work Experiences File</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="card">
                                                                <div class="card-body">

                                                                <?php 
                                                                    $attributes = array('class' => 'parsley-examples');
                                                                    echo form_open_multipart('pages/update_wefile', $attributes);
                                                                ?>
                                                                    <input type="hidden" name="id" value="<?= $staff->record_no; ?>">
                                                                    <input type="hidden" name="empEmail" value="<?= $staff->empEmail; ?>">
                                                                    <input type="hidden" name="jobID" value="<?= $this->uri->segment(4); ?>">
                                                                    <input type="hidden" name="school_id" value="<?= $this->uri->segment(5); ?>">
                                                                    
                                                                    
                                                                    
                                                                    <div class="form-group row">
                                                                        <label for="hori-pass1" class="col-md-4 col-form-label">Select PDF File<span class="text-danger">*</span></label>
                                                                        <div class="col-md-7">
                                                                            <input id="myInput" type="file"  name="file"  required class="form-control">
                                                                        </div>
                                                                    </div>

                                                                
                                                                    
                                                                    <div class="form-group row mb-0">
                                                                        <div class="col-md-8 offset-md-4">
                                                                            <button type="submit" class="btn btn-danger waves-effect waves-light mr-1">
                                                                                Submit
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                                </div>
                                                            </div>
                                                            <!-- end card -->
                                                        </div>
                                                        <!-- end col -->

                            
                                                    </div>
                                                    <!-- end row -->






                                                    </div>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->

                                        <!--  Modal content for the above example -->
                                        <div id="myModal" class="modal fade elegfile" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-purple">
                                                        <h5 class="modal-title text-white" id="myLargeModalLabel">Eligibility</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="card">
                                                                <div class="card-body">

                                                                <?php 
                                                                    $attributes = array('class' => 'parsley-examples');
                                                                    echo form_open_multipart('pages/update_eligibility', $attributes);
                                                                ?>
                                                                    <input type="hidden" name="id" value="<?= $staff->record_no; ?>">
                                                                    <input type="hidden" name="empEmail" value="<?= $staff->empEmail; ?>">
                                                                    <input type="hidden" name="jobID" value="<?= $this->uri->segment(4); ?>">
                                                                    <input type="hidden" name="school_id" value="<?= $this->uri->segment(5); ?>">
                                                                    
                                                                    
                                                                    <div class="form-group row">
                                                                        <label for="hori-pass1" class="col-md-4 col-form-label">Select PDF File<span class="text-danger">*</span></label>
                                                                        <div class="col-md-7">
                                                                            <input id="myInput" type="file"  name="file"  required class="form-control">
                                                                        </div>
                                                                    </div>

                                                                
                                                                    
                                                                    <div class="form-group row mb-0">
                                                                        <div class="col-md-8 offset-md-4">
                                                                            <button type="submit" class="btn btn-purple waves-effect waves-light mr-1">
                                                                                Submit
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                                </div>
                                                            </div>
                                                            <!-- end card -->
                                                        </div>
                                                        <!-- end col -->

                            
                                                    </div>
                                                    <!-- end row -->






                                                    </div>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->

                                        <!--  Modal content for the above example -->
                                        <div id="myModal" class="modal fade letfile" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-purple">
                                                        <h5 class="modal-title text-white" id="myLargeModalLabel">LET (Attachment) File</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="card">
                                                                <div class="card-body">

                                                                <?php 
                                                                    $attributes = array('class' => 'parsley-examples');
                                                                    echo form_open_multipart('pages/update_letfile_staff', $attributes);
                                                                ?>
                                                                    <input type="hidden" name="id" value="<?= $staff->record_no; ?>">
                                                                    <input type="hidden" name="empEmail" value="<?= $staff->empEmail; ?>">
                                                                    <input type="hidden" name="jobID" value="<?= $this->uri->segment(4); ?>">
                                                                    <input type="hidden" name="school_id" value="<?= $this->uri->segment(5); ?>">
                                                                    
                                                                    
                                                                    <div class="form-group row">
                                                                        <label for="hori-pass1" class="col-md-4 col-form-label">Select PDF File<span class="text-danger">*</span></label>
                                                                        <div class="col-md-7">
                                                                            <input id="myInput" type="file"  name="file"  required class="form-control">
                                                                        </div>
                                                                    </div>

                                                                
                                                                    
                                                                    <div class="form-group row mb-0">
                                                                        <div class="col-md-8 offset-md-4">
                                                                            <button type="submit" class="btn btn-purple waves-effect waves-light mr-1">
                                                                                Submit
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                                </div>
                                                            </div>
                                                            <!-- end card -->
                                                        </div>
                                                        <!-- end col -->

                            
                                                    </div>
                                                    <!-- end row -->






                                                    </div>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->

                                        <!--  Modal content for the above example -->
                                        <div id="myModal" class="modal fade tscfile" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header" style="background:#edc755">
                                                        <h5 class="modal-title" id="myLargeModalLabel">Training/Seminar Certificates File</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="card">
                                                                <div class="card-body">

                                                                <?php 
                                                                    $attributes = array('class' => 'parsley-examples');
                                                                    echo form_open_multipart('pages/update_tscfile_staff', $attributes);
                                                                ?>
                                                                    <input type="hidden" name="id" value="<?= $staff->record_no; ?>">
                                                                    <input type="hidden" name="empEmail" value="<?= $staff->empEmail; ?>">
                                                                    <input type="hidden" name="jobID" value="<?= $this->uri->segment(4); ?>">
                                                                    <input type="hidden" name="school_id" value="<?= $this->uri->segment(5); ?>">
                                                                    
                                                                    
                                                                    <div class="form-group row">
                                                                        <label for="hori-pass1" class="col-md-4 col-form-label">Select PDF File<span class="text-danger">*</span></label>
                                                                        <div class="col-md-7">
                                                                            <input id="myInput" type="file"  name="file"  required class="form-control">
                                                                        </div>
                                                                    </div>

                                                                
                                                                    
                                                                    <div class="form-group row mb-0">
                                                                        <div class="col-md-8 offset-md-4">
                                                                            <button type="submit" class="btn btn-primary waves-effect waves-light mr-1">
                                                                                Submit
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                                </div>
                                                            </div>
                                                            <!-- end card -->
                                                        </div>
                                                        <!-- end col -->

                            
                                                    </div>
                                                    <!-- end row -->






                                                    </div>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->

                                        <!--  Modal content for the above example -->
                                        <div id="myModal" class="modal fade tcfile" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="myLargeModalLabel">Tesda Cert. (Attachments) File</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="card">
                                                                <div class="card-body">

                                                                <?php 
                                                                    $attributes = array('class' => 'parsley-examples');
                                                                    echo form_open_multipart('pages/update_tcfile_staff', $attributes);
                                                                ?>
                                                                    <input type="hidden" name="id" value="<?= $staff->record_no; ?>">
                                                                    <input type="hidden" name="empEmail" value="<?= $staff->empEmail; ?>">
                                                                    <input type="hidden" name="jobID" value="<?= $this->uri->segment(4); ?>">
                                                                    <input type="hidden" name="school_id" value="<?= $this->uri->segment(5); ?>">
                                                                    
                                                                    
                                                                    <div class="form-group row">
                                                                        <label for="hori-pass1" class="col-md-4 col-form-label">Select PDF File<span class="text-danger">*</span></label>
                                                                        <div class="col-md-7">
                                                                            <input id="myInput" type="file"  name="file"  required class="form-control">
                                                                        </div>
                                                                    </div>

                                                                
                                                                    
                                                                    <div class="form-group row mb-0">
                                                                        <div class="col-md-8 offset-md-4">
                                                                            <button type="submit" class="btn btn-primary waves-effect waves-light mr-1">
                                                                                Submit
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                                </div>
                                                            </div>
                                                            <!-- end card -->
                                                        </div>
                                                        <!-- end col -->

                            
                                                    </div>
                                                    <!-- end row -->






                                                    </div>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->

                                        <!--  Modal content for the above example -->
                                        <div id="myModal" class="modal fade omni" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-success">
                                                        <h5 class="modal-title text-white" id="myLargeModalLabel">OMNIBUS SWORN STATEMENT</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="card">
                                                                <div class="card-body">

                                                                <?php 
                                                                    $attributes = array('class' => 'parsley-examples');
                                                                    echo form_open_multipart('pages/update_omni', $attributes);
                                                                ?>
                                                                    <input type="hidden" name="id" value="<?= $staff->record_no; ?>">
                                                                    
                                                                    <input type="hidden" name="empEmail" value="<?= $staff->empEmail; ?>">
                                                                    <input type="hidden" name="jobID" value="<?= $this->uri->segment(4); ?>">
                                                                    <input type="hidden" name="school_id" value="<?= $this->uri->segment(5); ?>">
                                                                    
                                                                    
                                                                    <div class="form-group row">
                                                                        <label for="hori-pass1" class="col-md-4 col-form-label">Select PDF File<span class="text-danger">*</span></label>
                                                                        <div class="col-md-7">
                                                                            <input id="myInput" type="file"  name="file"  required class="form-control">
                                                                        </div>
                                                                    </div>

                                                                
                                                                    
                                                                    <div class="form-group row mb-0">
                                                                        <div class="col-md-8 offset-md-4">
                                                                            <button type="submit" class="btn btn-success waves-effect waves-light mr-1">
                                                                                Submit
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                                </div>
                                                            </div>
                                                            <!-- end card -->
                                                        </div>
                                                        <!-- end col -->

                            
                                                    </div>
                                                    <!-- end row -->


                                                    </div>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->

                                        <!--  Modal content for the above example -->
                                        <div id="myModal" class="modal fade apfile" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-success">
                                                        <h5 class="modal-title text-white" id="myLargeModalLabel">LETTER OF INTENT</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="card">
                                                                <div class="card-body">

                                                                <?php 
                                                                    $attributes = array('class' => 'parsley-examples');
                                                                    echo form_open_multipart('pages/update_apfile_staff', $attributes);
                                                                ?>
                                                                    <input type="hidden" name="id" value="<?= $staff->record_no; ?>">
                                                                    <input type="hidden" name="empEmail" value="<?= $staff->empEmail; ?>">
                                                                    <input type="hidden" name="jobID" value="<?= $this->uri->segment(4); ?>">
                                                                    <input type="hidden" name="school_id" value="<?= $this->uri->segment(5); ?>">
                                                                    <input type="hidden" name="appID" value="<?= $aa->appID; ?>">
                                                                    
                                                                    
                                                                    <div class="form-group row">
                                                                        <label for="hori-pass1" class="col-md-4 col-form-label">Select PDF File<span class="text-danger">*</span></label>
                                                                        <div class="col-md-7">
                                                                            <input id="myInput" type="file"  name="file"  required class="form-control">
                                                                        </div>
                                                                    </div>

                                                                
                                                                    
                                                                    <div class="form-group row mb-0">
                                                                        <div class="col-md-8 offset-md-4">
                                                                            <button type="submit" class="btn btn-success waves-effect waves-light mr-1">
                                                                                Submit
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                                </div>
                                                            </div>
                                                            <!-- end card -->
                                                        </div>
                                                        <!-- end col -->

                            
                                                    </div>
                                                    <!-- end row -->






                                                    </div>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->

                                        <!--  Modal content for the above example -->
                                        <div id="myModal" class="modal fade voters" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-success">
                                                        <h5 class="modal-title text-white" id="myLargeModalLabel">VOTER'S ID/CERTIFICATE</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="card">
                                                                <div class="card-body">

                                                                <?php 
                                                                    $attributes = array('class' => 'parsley-examples');
                                                                    echo form_open_multipart('pages/update_voters_staff', $attributes);
                                                                ?>
                                                                    <input type="hidden" name="id" value="<?= $staff->record_no; ?>">
                                                                    <input type="hidden" name="empEmail" value="<?= $staff->empEmail; ?>">
                                                                    <input type="hidden" name="jobID" value="<?= $this->uri->segment(4); ?>">
                                                                    <input type="hidden" name="school_id" value="<?= $this->uri->segment(5); ?>">
                                                                    
                                                                    
                                                                    <div class="form-group row">
                                                                        <label for="hori-pass1" class="col-md-4 col-form-label">Select PDF File<span class="text-danger">*</span></label>
                                                                        <div class="col-md-7">
                                                                            <input id="myInput" type="file"  name="file"  required class="form-control">
                                                                        </div>
                                                                    </div>

                                                                
                                                                    
                                                                    <div class="form-group row mb-0">
                                                                        <div class="col-md-8 offset-md-4">
                                                                            <button type="submit" class="btn btn-success waves-effect waves-light mr-1">
                                                                                Submit
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                                </div>
                                                            </div>
                                                            <!-- end card -->
                                                        </div>
                                                        <!-- end col -->

                            
                                                    </div>
                                                    <!-- end row -->






                                                    </div>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->

                                        <!--  Modal content for the above example -->
                                        <div id="myModal" class="modal fade pdsfile" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-success">
                                                        <h5 class="modal-title text-white" id="myLargeModalLabel">PERSONAL DATA SHEET</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="card">
                                                                <div class="card-body">

                                                                <?php 
                                                                    $attributes = array('class' => 'parsley-examples');
                                                                    echo form_open_multipart('pages/update_pdsfile', $attributes);
                                                                ?>
                                                                    <input type="hidden" name="id" value="<?= $staff->record_no; ?>">
                                                                    <input type="hidden" name="empEmail" value="<?= $staff->empEmail; ?>">
                                                                    <input type="hidden" name="jobID" value="<?= $this->uri->segment(4); ?>">
                                                                    <input type="hidden" name="school_id" value="<?= $this->uri->segment(5); ?>">
                                                                    
                                                                    
                                                                    <div class="form-group row">
                                                                        <label for="hori-pass1" class="col-md-4 col-form-label">Select PDF File<span class="text-danger">*</span></label>
                                                                        <div class="col-md-7">
                                                                            <input id="myInput" type="file"  name="file"  required class="form-control">
                                                                        </div>
                                                                    </div>

                                                                
                                                                    
                                                                    <div class="form-group row mb-0">
                                                                        <div class="col-md-8 offset-md-4">
                                                                            <button type="submit" class="btn btn-success waves-effect waves-light mr-1">
                                                                                Submit
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                                </div>
                                                            </div>
                                                            <!-- end card -->
                                                        </div>
                                                        <!-- end col -->

                            
                                                    </div>
                                                    <!-- end row -->






                                                    </div>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->

                                        <!--  Modal content for the above example -->
                                        <div id="myModal" class="modal fade oafile" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-success">
                                                        <h5 class="modal-title text-white" id="myLargeModalLabel">Other Appendices</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="card">
                                                                <div class="card-body">

                                                                <?php 
                                                                    $attributes = array('class' => 'parsley-examples');
                                                                    echo form_open_multipart('pages/update_oafile_staff', $attributes);
                                                                ?>
                                                                    <input type="hidden" name="id" value="<?= $staff->record_no; ?>">
                                                                    <input type="hidden" name="empEmail" value="<?= $staff->empEmail; ?>">
                                                                    <input type="hidden" name="jobID" value="<?= $this->uri->segment(4); ?>">
                                                                    <input type="hidden" name="school_id" value="<?= $this->uri->segment(5); ?>">
                                                                    
                                                                    
                                                                    <div class="form-group row">
                                                                        <label for="hori-pass1" class="col-md-4 col-form-label">Select PDF File<span class="text-danger">*</span></label>
                                                                        <div class="col-md-7">
                                                                            <input id="myInput" type="file"  name="file"  required class="form-control">
                                                                        </div>
                                                                    </div>

                                                                
                                                                    
                                                                    <div class="form-group row mb-0">
                                                                        <div class="col-md-8 offset-md-4">
                                                                            <button type="submit" class="btn btn-success waves-effect waves-light mr-1">
                                                                                Submit
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                                </div>
                                                            </div>
                                                            <!-- end card -->
                                                        </div>
                                                        <!-- end col -->

                            
                                                    </div>
                                                    <!-- end row -->






                                                    </div>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->

                                        <!--  Modal content for the above example -->
                                        <div id="myModal" class="modal fade ipcrffile" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header" style="background:#b9b28a">
                                                        <h5 class="modal-title text-white" id="myLargeModalLabel">Performance (Attachments) File</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="card">
                                                                <div class="card-body">

                                                                <?php 
                                                                    $attributes = array('class' => 'parsley-examples');
                                                                    echo form_open_multipart('pages/update_ipcrffile', $attributes);
                                                                ?>
                                                                    <input type="hidden" name="id" value="<?= $staff->record_no; ?>">
                                                                    <input type="hidden" name="empEmail" value="<?= $staff->empEmail; ?>">
                                                                    <input type="hidden" name="jobID" value="<?= $this->uri->segment(4); ?>">
                                                                    <input type="hidden" name="school_id" value="<?= $this->uri->segment(5); ?>">
                                                                    
                                                                    
                                                                    <div class="form-group row">
                                                                        <label for="hori-pass1" class="col-md-4 col-form-label">Select PDF File<span class="text-danger">*</span></label>
                                                                        <div class="col-md-7">
                                                                            <input id="myInput" type="file"  name="file"  required class="form-control">
                                                                        </div>
                                                                    </div>

                                                                
                                                                    
                                                                    <div class="form-group row mb-0">
                                                                        <div class="col-md-8 offset-md-4">
                                                                            <button type="submit" class="btn waves-effect waves-light mr-1" style="background:#b9b28a">
                                                                                Submit
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                                </div>
                                                            </div>
                                                            <!-- end card -->
                                                        </div>
                                                        <!-- end col -->

                            
                                                    </div>
                                                    <!-- end row -->






                                                    </div>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->

                                        <!--  Modal content for the above example -->
                                        <div id="myModal" class="modal fade ppstco" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header" style="background:#7965c1">
                                                        <h5 class="modal-title text-white" id="myLargeModalLabel">PPST COIs - Classroom Observation</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="card">
                                                                <div class="card-body">

                                                                <?php 
                                                                    $attributes = array('class' => 'parsley-examples');
                                                                    echo form_open_multipart('pages/update_ppstco', $attributes);
                                                                ?>
                                                                    <input type="hidden" name="id" value="<?= $staff->record_no; ?>">
                                                                    <input type="hidden" name="empEmail" value="<?= $staff->empEmail; ?>">
                                                                    <input type="hidden" name="jobID" value="<?= $this->uri->segment(4); ?>">
                                                                    <input type="hidden" name="school_id" value="<?= $this->uri->segment(5); ?>">
                                                                    
                                                                    
                                                                    <div class="form-group row">
                                                                        <label for="hori-pass1" class="col-md-4 col-form-label">Select PDF File<span class="text-danger">*</span></label>
                                                                        <div class="col-md-7">
                                                                            <input id="myInput" type="file"  name="file"  required class="form-control">
                                                                        </div>
                                                                    </div>

                                                                
                                                                    
                                                                    <div class="form-group row mb-0">
                                                                        <div class="col-md-8 offset-md-4">
                                                                            <button type="submit" class="btn waves-effect waves-light mr-1" style="background:#7965c1">
                                                                                Submit
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                                </div>
                                                            </div>
                                                            <!-- end card -->
                                                        </div>
                                                        <!-- end col -->

                            
                                                    </div>
                                                    <!-- end row -->






                                                    </div>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->

                                        <!--  Modal content for the above example -->
                                        <div id="myModal" class="modal fade ppstpa" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header" style="background:#f2c078">
                                                        <h5 class="modal-title text-white" id="myLargeModalLabel">IPCRF for Non-Teaching (Attachments) File</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="card">
                                                                <div class="card-body">

                                                                <?php 
                                                                    $attributes = array('class' => 'parsley-examples');
                                                                    echo form_open_multipart('pages/update_ppstpa', $attributes);
                                                                ?>
                                                                    <input type="hidden" name="id" value="<?= $staff->record_no; ?>">
                                                                    <input type="hidden" name="empEmail" value="<?= $staff->empEmail; ?>">
                                                                    <input type="hidden" name="jobID" value="<?= $this->uri->segment(4); ?>">
                                                                    <input type="hidden" name="school_id" value="<?= $this->uri->segment(5); ?>">
                                                                    
                                                                    
                                                                    <div class="form-group row">
                                                                        <label for="hori-pass1" class="col-md-4 col-form-label">Select PDF File<span class="text-danger">*</span></label>
                                                                        <div class="col-md-7">
                                                                            <input id="myInput" type="file"  name="file"  required class="form-control">
                                                                        </div>
                                                                    </div>

                                                                
                                                                    
                                                                    <div class="form-group row mb-0">
                                                                        <div class="col-md-8 offset-md-4">
                                                                            <button type="submit" class="btn waves-effect waves-light mr-1" style="background:#f2c078">
                                                                                Submit
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                                </div>
                                                            </div>
                                                            <!-- end card -->
                                                        </div>
                                                        <!-- end col -->

                            
                                                    </div>
                                                    <!-- end row -->






                                                    </div>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->
                                        
                                        <!--  Modal content for the above example -->
                                        <div id="myModal" class="modal fade educrating" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-warning">
                                                        <h5 class="modal-title text-white" id="myLargeModalLabel">EDUCATION RATING</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <p class="text-danger"><i>Note: Maximum allowed value is <?= $ptp->educ; ?></i></p>
                                                            <div class="card">
                                                                <div class="card-body">

                                                                <form class="parsley-examples" action="<?= base_url(); ?>pages/update_educ_rate_promotion/<?= $this->uri->segment(3); ?>/<?= $this->uri->segment(4); ?>/<?= $this->uri->segment(5); ?>/<?= $aa->appID; ?>" method="post">
                                                                    <input type="hidden" name="app_id" value="<?= $aa->appID; ?>">
                                                                    <input type="hidden" name="empEmail" value="<?= $staff->empEmail; ?>">
                                                                    <input type="hidden" name="record_no" value="<?= $staff->record_no; ?>">
                                                                    <input type="hidden" name="school_id" value="<?= $this->uri->segment(5); ?>">
                                                                    <input type="hidden" name="page" value="<?= $this->uri->segment(2); ?>">
                                                                    <input type="hidden" name="max" value="<?= $ptp->educ; ?>">
                                                                        
                                                                    <div class="row">
                                                                        <div class="col-lg-12">
                                                                                <div class="form-group">
                                                                                    <label>Rating</label>
                                                                                    <input  type="text" class="form-control" name="educ"  value="<?php if($rating->educ != 0.00001){echo $rating->educ; } ?>" >
                                                                                </div>	
                                                                        </div>	
                                                                        
                                                                    </div>

                              
                                                                    
                                                                        <div class="form-group text-right mb-0">
                                                                            <button class="btn btn-warning waves-effect waves-light mr-1" type="submit">
                                                                                Submit
                                                                            </button>
                                                                        </div>

                                                                    </form>
                                                                </div>
                                                            </div>
                                                            <!-- end card -->
                                                        </div>
                                                        <!-- end col -->

                            
                                                    </div>
                                                    <!-- end row -->


                                                    </div>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->

                                        <!--  Modal content for the above example -->
                                        <div id="myModal" class="modal fade werating" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-danger">
                                                        <h5 class="modal-title text-white" id="myLargeModalLabel">WORK EXPERIENCE RATING</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <p class="text-danger"><i>Note: Maximum allowed value is <?= $ptp->exp; ?></i></p>
                                                            <div class="card">
                                                                <div class="card-body">

                                                                <form class="parsley-examples" action="<?= base_url(); ?>pages/update_rate_promotion/<?= $this->uri->segment(3); ?>/<?= $this->uri->segment(4); ?>/0/<?= $aa->appID; ?>" method="post">
                                                                    <input type="hidden" name="app_id" value="<?= $aa->appID; ?>">
                                                                    <input type="hidden" name="empEmail" value="<?= $staff->empEmail; ?>">
                                                                    <input type="hidden" name="record_no" value="<?= $staff->record_no; ?>">
                                                                    <input type="hidden" name="school_id" value="<?= $this->uri->segment(5); ?>">
                                                                    <input type="hidden" name="page" value="<?= $this->uri->segment(2); ?>">
                                                                    <input type="hidden" name="col" value="experience">
                                                                    <input type="hidden" name="message" value="Work Experiences">
                                                                    <input type="hidden" name="maxpoint" value="<?= $ptp->exp; ?>">
                                                                        
                                                                    <div class="row">
                                                                        <div class="col-lg-12">
                                                                                <div class="form-group">
                                                                                    <label>Rating</label>
                                                                                    <input type="text" class="form-control" name="experience"  value="<?php if($rating->experience != 0.00001){echo $rating->experience; } ?>" >
                                                                                </div>	
                                                                        </div>	
                                                                        
                                                                    </div>

                              
                                                                    
                                                                        <div class="form-group text-right mb-0">
                                                                            <button class="btn btn-danger waves-effect waves-light mr-1" type="submit">
                                                                                Submit
                                                                            </button>
                                                                        </div>

                                                                    </form>
                                                                </div>
                                                            </div>
                                                            <!-- end card -->
                                                        </div>
                                                        <!-- end col -->

                            
                                                    </div>
                                                    <!-- end row -->


                                                    </div>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->

                                         <!--  Modal content for the above example -->
                                         <div id="myModal" class="modal fade lrrating" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-purple">
                                                        <h5 class="modal-title text-white" id="myLargeModalLabel">LET RATING</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <p class="text-danger"><i>Note: Maximum allowed value is 10</i></p>
                                                            <div class="card">
                                                                <div class="card-body">

                                                                <form class="parsley-examples" action="<?= base_url(); ?>pages/update_rate_none/<?= $this->uri->segment(3); ?>/<?= $this->uri->segment(4); ?>/0/<?= $aa->appID; ?>" method="post">
                                                                    <input type="hidden" name="app_id" value="<?= $aa->appID; ?>">
                                                                    <input type="hidden" name="empEmail" value="<?= $staff->empEmail; ?>">
                                                                    <input type="hidden" name="record_no" value="<?= $this->uri->segment(3); ?>">
                                                                    <input type="hidden" name="school_id" value="<?= $this->uri->segment(5); ?>">
                                                                    <input type="hidden" name="page" value="<?= $this->uri->segment(2); ?>">
                                                                    <input type="hidden" name="col" value="experience">
                                                                    <input type="hidden" name="message" value="Work Experiences">
                                                                    <input type="hidden" name="maxpoint" value="10">
                                                                        
                                                                    <div class="row">
                                                                        <div class="col-lg-12">
                                                                                <div class="form-group">
                                                                                    <label>Rating</label>
                                                                                    <input type="text"  class="form-control" name="let_rating"  value="<?php if($rating->let_rating != 0.00001){echo $rating->let_rating; } ?>" >
                                                                                </div>	
                                                                        </div>	
                                                                        
                                                                    </div>

                              
                                                                    
                                                                        <div class="form-group text-right mb-0">
                                                                            <button class="btn btn-purple waves-effect waves-light mr-1" type="submit">
                                                                                Submit
                                                                            </button>
                                                                        </div>

                                                                    </form>
                                                                </div>
                                                            </div>
                                                            <!-- end card -->
                                                        </div>
                                                        <!-- end col -->

                            
                                                    </div>
                                                    <!-- end row -->


                                                    </div>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->

                                        <!--  Modal content for the above example -->
                                        <div id="myModal" class="modal fade performance" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-warning">
                                                        <h5 class="modal-title text-white" id="myLargeModalLabel">PERFORMANCE RATING</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <p class="text-danger"><i>Note: Maximum allowed value is 30</i></p>
                                                            <div class="card">
                                                                <div class="card-body">

                                                                <form class="parsley-examples" action="<?= base_url(); ?>pages/update_rate_promotion/<?= $this->uri->segment(3); ?>/<?= $this->uri->segment(4); ?>/0/<?= $aa->appID; ?>" method="post">
                                                                    <input type="hidden" name="app_id" value="<?= $aa->appID; ?>">
                                                                    <input type="hidden" name="empEmail" value="<?= $staff->empEmail; ?>">
                                                                    <input type="hidden" name="record_no" value="<?= $staff->record_no; ?>">
                                                                    <input type="hidden" name="school_id" value="<?= $this->uri->segment(5); ?>">
                                                                    <input type="hidden" name="page" value="<?= $this->uri->segment(2); ?>">
                                                                    <input type="hidden" name="col" value="performance">
                                                                    <input type="hidden" name="message" value="performance">
                                                                    <input type="hidden" name="maxpoint" value="30">
                                                                        
                                                                    <div class="row">
                                                                        <div class="col-lg-12">
                                                                                <div class="form-group">
                                                                                    <label>Rating</label>
                                                                                    <input type="text"  class="form-control" name="performance"  value="<?php if($rating->performance != 0.00001){echo $rating->performance; } ?>" >
                                                                                </div>	
                                                                        </div>	
                                                                        
                                                                    </div>

                              
                                                                    
                                                                        <div class="form-group text-right mb-0">
                                                                            <button class="btn btn-purple waves-effect waves-light mr-1" type="submit">
                                                                                Submit
                                                                            </button>
                                                                        </div>

                                                                    </form>
                                                                </div>
                                                            </div>
                                                            <!-- end card -->
                                                        </div>
                                                        <!-- end col -->

                            
                                                    </div>
                                                    <!-- end row -->


                                                    </div>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->

                                        <!--  Modal content for the above example -->
                                        <div id="myModal" class="modal fade oa" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-info">
                                                        <h5 class="modal-title text-white" id="myLargeModalLabel">OUTSTANDING ACCOMPLISHMENTS</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <p class="text-danger"><i>Note: Maximum allowed value is <?= $ptp->oa; ?></i></p>
                                                            <div class="card">
                                                                <div class="card-body">

                                                                <form class="parsley-examples" action="<?= base_url(); ?>pages/update_rate_none/<?= $this->uri->segment(3); ?>/<?= $this->uri->segment(4); ?>/0/<?= $aa->appID; ?>" method="post">
                                                                    <input type="hidden" name="app_id" value="<?= $aa->appID; ?>">
                                                                    <input type="hidden" name="empEmail" value="<?= $staff->empEmail; ?>">
                                                                    <input type="hidden" name="record_no" value="<?= $this->uri->segment(3); ?>">
                                                                    <input type="hidden" name="school_id" value="<?= $this->uri->segment(5); ?>">
                                                                    <input type="hidden" name="page" value="<?= $this->uri->segment(2); ?>">
                                                                    <input type="hidden" name="col" value="oa">
                                                                    <input type="hidden" name="message" value="Outstanding Accomplishments">
                                                                    <input type="hidden" name="maxpoint" value="<?= $ptp->oa; ?>">
                                                                        
                                                                    <div class="row">
                                                                        <div class="col-lg-12">
                                                                                <div class="form-group">
                                                                                    <label>Rating</label>
                                                                                    <input type="text"  class="form-control" name="oa"  value="<?php if($rating->oa != 0.00001){echo $rating->oa; } ?>" >
                                                                                </div>	
                                                                        </div>	
                                                                        
                                                                    </div>

                              
                                                                    
                                                                        <div class="form-group text-right mb-0">
                                                                            <button class="btn btn-purple waves-effect waves-light mr-1" type="submit">
                                                                                Submit
                                                                            </button>
                                                                        </div>

                                                                    </form>
                                                                </div>
                                                            </div>
                                                            <!-- end card -->
                                                        </div>
                                                        <!-- end col -->

                            
                                                    </div>
                                                    <!-- end row -->


                                                    </div>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->

                                        <!--  Modal content for the above example -->
                                        <div id="myModal" class="modal fade ae" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-purple">
                                                        <h5 class="modal-title text-white" id="myLargeModalLabel">APPLICATION OF EDUCATION</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <p class="text-danger"><i>Note: Maximum allowed value is <?= $ptp->ae; ?></i></p>
                                                            <div class="card">
                                                                <div class="card-body">

                                                                <form class="parsley-examples" action="<?= base_url(); ?>pages/update_rate_none/<?= $this->uri->segment(3); ?>/<?= $this->uri->segment(4); ?>/0/<?= $aa->appID; ?>" method="post">
                                                                    <input type="hidden" name="app_id" value="<?= $aa->appID; ?>">
                                                                    <input type="hidden" name="empEmail" value="<?= $staff->empEmail; ?>">
                                                                    <input type="hidden" name="record_no" value="<?= $this->uri->segment(3); ?>">
                                                                    <input type="hidden" name="school_id" value="<?= $this->uri->segment(5); ?>">
                                                                    <input type="hidden" name="page" value="<?= $this->uri->segment(2); ?>">
                                                                    <input type="hidden" name="col" value="ae">
                                                                    <input type="hidden" name="message" value="Application Of Education">
                                                                    <input type="hidden" name="maxpoint" value="<?= $ptp->ae; ?>">
                                                                        
                                                                    <div class="row">
                                                                        <div class="col-lg-12">
                                                                                <div class="form-group">
                                                                                    <label>Rating</label>
                                                                                    <input type="text"  class="form-control" name="ae"  value="<?php if($rating->ae != 0.00001){echo $rating->ae; } ?>" >
                                                                                </div>	
                                                                        </div>	
                                                                        
                                                                    </div>

                              
                                                                    
                                                                        <div class="form-group text-right mb-0">
                                                                            <button class="btn btn-purple waves-effect waves-light mr-1" type="submit">
                                                                                Submit
                                                                            </button>
                                                                        </div>

                                                                    </form>
                                                                </div>
                                                            </div>
                                                            <!-- end card -->
                                                        </div>
                                                        <!-- end col -->

                            
                                                    </div>
                                                    <!-- end row -->


                                                    </div>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->

                                        <!--  Modal content for the above example -->
                                        <div id="myModal" class="modal fade aldrating" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-purple">
                                                        <h5 class="modal-title text-white" id="myLargeModalLabel">APPLICATION OF LEARNING & DEVELOPMENT</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <p class="text-danger"><i>Note: Maximum allowed value is <?= $ptp->ald; ?></i></p>
                                                            <div class="card">
                                                                <div class="card-body">

                                                                <form class="parsley-examples" action="<?= base_url(); ?>pages/update_rate_none/<?= $this->uri->segment(3); ?>/<?= $this->uri->segment(4); ?>/0/<?= $aa->appID; ?>" method="post">
                                                                    <input type="hidden" name="app_id" value="<?= $aa->appID; ?>">
                                                                    <input type="hidden" name="empEmail" value="<?= $staff->empEmail; ?>">
                                                                    <input type="hidden" name="record_no" value="<?= $this->uri->segment(3); ?>">
                                                                    <input type="hidden" name="school_id" value="<?= $this->uri->segment(5); ?>">
                                                                    <input type="hidden" name="page" value="<?= $this->uri->segment(2); ?>">
                                                                    <input type="hidden" name="col" value="ald">
                                                                    <input type="hidden" name="message" value="Application Of Learning & Development">
                                                                    <input type="hidden" name="maxpoint" value="<?= $ptp->ald; ?>">
                                                                        
                                                                    <div class="row">
                                                                        <div class="col-lg-12">
                                                                                <div class="form-group">
                                                                                    <label>Rating</label>
                                                                                    <input type="text"  class="form-control" name="ald"  value="<?php if($rating->ald != 0.00001){echo $rating->ald; } ?>" >
                                                                                </div>	
                                                                        </div>	
                                                                        
                                                                    </div>

                              
                                                                    
                                                                        <div class="form-group text-right mb-0">
                                                                            <button class="btn btn-purple waves-effect waves-light mr-1" type="submit">
                                                                                Submit
                                                                            </button>
                                                                        </div>

                                                                    </form>
                                                                </div>
                                                            </div>
                                                            <!-- end card -->
                                                        </div>
                                                        <!-- end col -->

                            
                                                    </div>
                                                    <!-- end row -->


                                                    </div>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->

                                        <!--  Modal content for the above example -->
                                        <div id="myModal" class="modal fade interrating" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-purple">
                                                        <h5 class="modal-title text-white" id="myLargeModalLabel">INTERVIEW</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <p class="text-danger"><i></i></p>
                                                            <div class="card">
                                                                <div class="card-body">

                                                                <form class="parsley-examples" action="<?= base_url(); ?>pages/update_rate_none/<?= $this->uri->segment(3); ?>/<?= $this->uri->segment(4); ?>/0/<?= $aa->appID; ?>" method="post">
                                                                    <input type="hidden" name="app_id" value="<?= $aa->appID; ?>">
                                                                    <input type="hidden" name="empEmail" value="<?= $staff->empEmail; ?>">
                                                                    <input type="hidden" name="record_no" value="<?= $this->uri->segment(3); ?>">
                                                                    <input type="hidden" name="school_id" value="<?= $this->uri->segment(5); ?>">
                                                                    <input type="hidden" name="page" value="<?= $this->uri->segment(2); ?>">
                                                                    <input type="hidden" name="col" value="interview">
                                                                    <input type="hidden" name="message" value="Interview">
                                                                    <input type="hidden" name="maxpoint" value="20">
                                                                        
                                                                    <div class="row">
                                                                        <div class="col-lg-12">
                                                                                <div class="form-group">
                                                                                    <label>Rating</label>
                                                                                    <input type="text"  class="form-control" name="interview"  value="<?php if($rating->interview != 0.00001){echo $rating->interview; } ?>" >
                                                                                </div>	
                                                                        </div>	
                                                                        
                                                                    </div>

                              
                                                                    
                                                                        <div class="form-group text-right mb-0">
                                                                            <button class="btn btn-purple waves-effect waves-light mr-1" type="submit">
                                                                                Submit
                                                                            </button>
                                                                        </div>

                                                                    </form>
                                                                </div>
                                                            </div>
                                                            <!-- end card -->
                                                        </div>
                                                        <!-- end col -->

                            
                                                    </div>
                                                    <!-- end row -->


                                                    </div>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->

                                        <!--  Modal content for the above example -->
                                        <div id="myModal" class="modal fade writtenrating" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-purple">
                                                        <h5 class="modal-title text-white" id="myLargeModalLabel">WRITTEN</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <p class="text-danger"><i></i></p>
                                                            <div class="card">
                                                                <div class="card-body">

                                                                <form class="parsley-examples" action="<?= base_url(); ?>pages/update_rate_none/<?= $this->uri->segment(3); ?>/<?= $this->uri->segment(4); ?>/0/<?= $aa->appID; ?>" method="post">
                                                                    <input type="hidden" name="app_id" value="<?= $aa->appID; ?>">
                                                                    <input type="hidden" name="empEmail" value="<?= $staff->empEmail; ?>">
                                                                    <input type="hidden" name="record_no" value="<?= $this->uri->segment(3); ?>">
                                                                    <input type="hidden" name="school_id" value="<?= $this->uri->segment(5); ?>">
                                                                    <input type="hidden" name="page" value="<?= $this->uri->segment(2); ?>">
                                                                    <input type="hidden" name="col" value="written">
                                                                    <input type="hidden" name="message" value="Written">
                                                                    <input type="hidden" name="maxpoint" value="20">
                                                                        
                                                                    <div class="row">
                                                                        <div class="col-lg-12">
                                                                                <div class="form-group">
                                                                                    <label>Rating</label>
                                                                                    <input type="text"  class="form-control" name="written"  value="<?php if($rating->written != 0.00001){echo $rating->written; } ?>" >
                                                                                </div>	
                                                                        </div>	
                                                                        
                                                                    </div>

                              
                                                                    
                                                                        <div class="form-group text-right mb-0">
                                                                            <button class="btn btn-purple waves-effect waves-light mr-1" type="submit">
                                                                                Submit
                                                                            </button>
                                                                        </div>

                                                                    </form>
                                                                </div>
                                                            </div>
                                                            <!-- end card -->
                                                        </div>
                                                        <!-- end col -->

                            
                                                    </div>
                                                    <!-- end row -->


                                                    </div>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->

                                        <!--  Modal content for the above example -->
                                        <div id="myModal" class="modal fade skillsrating" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-purple">
                                                        <h5 class="modal-title text-white" id="myLargeModalLabel">Skills</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <p class="text-danger"><i></i></p>
                                                            <div class="card">
                                                                <div class="card-body">

                                                                <form class="parsley-examples" action="<?= base_url(); ?>pages/update_rate_none/<?= $this->uri->segment(3); ?>/<?= $this->uri->segment(4); ?>/0/<?= $aa->appID; ?>" method="post">
                                                                    <input type="hidden" name="app_id" value="<?= $aa->appID; ?>">
                                                                    <input type="hidden" name="empEmail" value="<?= $staff->empEmail; ?>">
                                                                    <input type="hidden" name="record_no" value="<?= $this->uri->segment(3); ?>">
                                                                    <input type="hidden" name="school_id" value="<?= $this->uri->segment(5); ?>">
                                                                    <input type="hidden" name="page" value="<?= $this->uri->segment(2); ?>">
                                                                    <input type="hidden" name="col" value="skills">
                                                                    <input type="hidden" name="message" value="Skills">
                                                                    <input type="hidden" name="maxpoint" value="20">
                                                                        
                                                                    <div class="row">
                                                                        <div class="col-lg-12">
                                                                                <div class="form-group">
                                                                                    <label>Rating</label>
                                                                                    <input type="text"  class="form-control" name="skills"  value="<?php if($rating->skills != 0.00001){echo $rating->skills; } ?>" >
                                                                                </div>	
                                                                        </div>	
                                                                        
                                                                    </div>

                              
                                                                    
                                                                        <div class="form-group text-right mb-0">
                                                                            <button class="btn btn-purple waves-effect waves-light mr-1" type="submit">
                                                                                Submit
                                                                            </button>
                                                                        </div>

                                                                    </form>
                                                                </div>
                                                            </div>
                                                            <!-- end card -->
                                                        </div>
                                                        <!-- end col -->

                            
                                                    </div>
                                                    <!-- end row -->


                                                    </div>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->

                                        <!--  Modal content for the above example -->
                                        <div id="myModal" class="modal fade certrating" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-info">
                                                        <h5 class="modal-title text-white" id="myLargeModalLabel">TRAININGS AND SEMINARS RATING</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                        <p class="text-danger"><i>Note: Maximum allowed value is <?= $ptp->tr; ?></i></p>
                                                            <div class="card">
                                                                <div class="card-body">

                                                                <form class="parsley-examples" action="<?= base_url(); ?>pages/update_rate_promotion/<?= $this->uri->segment(3); ?>/<?= $this->uri->segment(4); ?>/0/<?= $aa->appID; ?>" method="post">
                                                                    <input type="hidden" name="app_id" value="<?= $aa->appID; ?>">
                                                                    <input type="hidden" name="empEmail" value="<?= $staff->empEmail; ?>">
                                                                    <input type="hidden" name="record_no" value="<?= $staff->record_no; ?>">
                                                                    <input type="hidden" name="school_id" value="<?= $this->uri->segment(5); ?>">
                                                                    <input type="hidden" name="page" value="<?= $this->uri->segment(2); ?>">
                                                                    <input type="hidden" name="col" value="trainings">
                                                                    <input type="hidden" name="message" value="trainings and seminars">
                                                                    <input type="hidden" name="maxpoint" value="<?= $ptp->tr; ?>">
                                                                        
                                                                    <div class="row">
                                                                        <div class="col-lg-12">
                                                                                <div class="form-group">
                                                                                    <label>Rating</label>
                                                                                    <input type="text"  class="form-control" name="trainings"  value="<?php if($rating->trainings != 0.00001){echo $rating->trainings; } ?>" >
                                                                                </div>	
                                                                        </div>	
                                                                        
                                                                    </div>

                              
                                                                    
                                                                        <div class="form-group text-right mb-0">
                                                                            <button class="btn btn-info waves-effect waves-light mr-1" type="submit">
                                                                                Submit
                                                                            </button>
                                                                        </div>

                                                                    </form>
                                                                </div>
                                                            </div>
                                                            <!-- end card -->
                                                        </div>
                                                        <!-- end col -->

                            
                                                    </div>
                                                    <!-- end row -->


                                                    </div>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->

                                        <!--  Modal content for the above example -->
                                        <div class="modal fade dq" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-info">
                                                        <h5 class="modal-title" id="myLargeModalLabel">Remarks</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="card">
                                                                <div class="card-body">

                                                                    <form class="parsley-examples" action="<?= base_url(); ?>pages/Unqualified_promotion/<?= $this->uri->segment(3); ?>/<?= $this->uri->segment(4); ?>" method="post">
                                                                    <input type="hidden" name="id" value="<?= $staff->applicant_id; ?>">
                                                                    <input type="hidden" name="empEmail" value="<?= $staff->empEmail; ?>">
                                                                    <input type="hidden" name="jobID" value="<?= $this->uri->segment(4); ?>">
                                                                    <input type="hidden" name="appID" value="<?= $aa->appID; ?>">
                                                                    <input type="hidden" name="job_type" value="<?= $job->job_type; ?>">
                                                                    <input type="hidden" name="job_fy" value="<?= $job->sy; ?>">
                                                                    <input type="hidden" name="dist" value="<?= $aa->district; ?>">
                                                                    <input type="hidden" name="record" value="<?= $this->uri->segment(3); ?>">

                                                                    
                                                                    <div class="row">
                                                                        <div class="col-lg-12">	
                                                                                <h4 class="header-title">Mandatory documents presented <span class='text-danger'>*</span></h4>
                                                                                <div class="form-group">
                                                                                <div class="custom-control custom-checkbox">
                                                                                <input type="checkbox" class="custom-control-input" id="customCheck1" name="li" value="1">
                                                                                <label class="custom-control-label text-xs" for="customCheck1">Letter of Intent</label>
                                                                            </div>
                                                                            <div class="custom-control custom-checkbox">
                                                                                <input type="checkbox" class="custom-control-input" id="customCheck2" name="da_pds" value="1">
                                                                                <label class="custom-control-label text-xs" for="customCheck2">Duly Accomplished PDS</label>
                                                                            </div>
                                                                            <div class="custom-control custom-checkbox">
                                                                                <input type="checkbox" class="custom-control-input" id="customCheck55" name="omni" value="1">
                                                                                <label class="custom-control-label text-xs" for="customCheck55">Annex C - Checklist of Requirement duly notarized</label>
                                                                            </div>
                                                                            <div class="custom-control custom-checkbox">
                                                                                <input type="checkbox" class="custom-control-input" id="customCheck56" name="educ" value="1">
                                                                                <label class="custom-control-label text-xs" for="customCheck56">Education (QS)</label>
                                                                            </div>
                                                                            <div class="custom-control custom-checkbox">
                                                                                <input type="checkbox" class="custom-control-input" id="customCheck57" name="exp" value="1">
                                                                                <label class="custom-control-label text-xs" for="customCheck57">Experience (QS)</label>
                                                                            </div>
                                                                            <div class="custom-control custom-checkbox">
                                                                                <input type="checkbox" class="custom-control-input" id="customCheck58" name="tr" value="1">
                                                                                <label class="custom-control-label text-xs" for="customCheck58">Training (QS)</label>
                                                                            </div>
                                                                            <div class="custom-control custom-checkbox">
                                                                                <input type="checkbox" class="custom-control-input" id="customCheck59" name="eli" value="1">
                                                                                <label class="custom-control-label text-xs" for="customCheck59">Eligibility (QS)</label>
                                                                            </div>
                                                                            <input type="hidden" name="prc" value="1">
                                                                            <input type="hidden" name="trbd" value="1">
                                                                            <input type="hidden" name="omni" value="1">
                                                                            <input type="hidden" name="local" value="1">

                                                                                </div>	
                                                                        </div>	
                                                                        
                                                                    </div>


                                                                    <div class="row">
                                                                        <div class="col-lg-12">	
                                                                                <h4 class="header-title">Remarks <span class='text-danger'>*</span></h4>
                                                                                <div class="form-group">
                                                                                <div class="custom-control custom-radio">
                                                                                <input type="radio" id="customRadio1" required name="remarks" class="custom-control-input" value="1">
                                                                                <label class="custom-control-label text-xs" for="customRadio1">Qualified</label>
                                                                            </div>
                                                                            <div class="custom-control custom-radio">
                                                                                <input type="radio" id="customRadio2" required name="remarks" class="custom-control-input" value="2">
                                                                                <label class="custom-control-label text-xs" for="customRadio2">Disqualified</label>
                                                                            </div>
                                                                                                                    

                                                                                </div>	
                                                                        </div>	
                                                                        
                                                                    </div>

                                                                        
                                                                    <div class="row">
                                                                        <div class="col-lg-12">
                                                                                <div class="form-group">
                                                                                    <label>If disqualified, state the reasons here. </label>
                                                                                    <textarea class="form-control" rows="5" name="reason" id="example-textarea"></textarea>
                                                                                </div>	
                                                                        </div>	
                                                                        
                                                                    </div>	

                                                                    
                                                                        
                                                                        

                                                                        <div class="form-group text-right mb-0">
                                                                            <button class="btn btn-info waves-effect waves-light mr-1" type="submit">
                                                                                Submit
                                                                            </button>
                                                                        </div>

                                                                    </form>
                                                                </div>
                                                            </div>
                                                            <!-- end card -->
                                                        </div>
                                                        <!-- end col -->

                            
                                                    </div>
                                                    <!-- end row -->






                                                    </div>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->
                                        
                                        <?php 
                                            $dq = $this->Common->one_cond_row('hris_app_dq','AppID',$aa->appID);
                                            if (empty($dq)) {
                                                $dq = $this->Common->one_cond_row('hris_app_dq','appID',$aa->appID);
                                            }
                                            if (empty($dq) && !empty($staff->id)) {
                                                $dq = $this->db
                                                    ->where('apID', $staff->id)
                                                    ->order_by('vdate','DESC')
                                                    ->order_by('id','DESC')
                                                    ->limit(1)
                                                    ->get('hris_app_dq')
                                                    ->row();
                                            }
                                            if (empty($dq)) {
                                                $dq = (object) ['li'=>0,'da_pds'=>0,'prc'=>0,'trbd'=>0,'omni'=>0,'local'=>0,'remarks'=>1,'reason'=>''];
                                            }
                                        ?>
                                        <?php if(!empty($dq)){ ?>

                                        <!--  Modal content for the above example -->
                                        <div class="modal fade dqedit" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-info">
                                                        <h5 class="modal-title" id="myLargeModalLabel">Remarks</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="card">
                                                                <div class="card-body">
                                                                    

                                                                    <form class="parsley-examples" action="<?= base_url(); ?>pages/Unqualifiededit/<?= $this->uri->segment(3); ?>/<?= $this->uri->segment(4); ?>" method="post">
                                                                    <input type="hidden" name="id" value="<?= $staff->applicant_id; ?>">
                                                                    <input type="hidden" name="empEmail" value="<?= $staff->empEmail; ?>">
                                                                    <input type="hidden" name="jobID" value="<?= $this->uri->segment(4); ?>">
                                                                    <input type="hidden" name="appID" value="<?= $aa->appID; ?>">
                                                                    <input type="hidden" name="job_type" value="<?= $job->job_type; ?>">
                                                                    <input type="hidden" name="dq_id" value="<?= $dq->id; ?>">

                                                                    
                                                                    
                                                                    
                                                                    
                                                                    
                                                                    <div class="row">
                                                                        <div class="col-lg-12">	
                                                                                <h4 class="header-title">Mandatory documents presented <span class='text-danger'>*</span></h4>
                                                                                <div class="form-group">
                                                                                <div class="custom-control custom-checkbox">
                                                                                <input type="checkbox" class="custom-control-input" id="customCheck11" <?php if($dq->li == 1){echo "checked";} ?> name="li" value="1">
                                                                                <label class="custom-control-label text-xs" for="customCheck11">Letter of Intent</label>
                                                                            </div>
                                                                            <div class="custom-control custom-checkbox">
                                                                                <input type="checkbox" class="custom-control-input" id="customCheck22" <?php if($dq->da_pds == 1){echo "checked";} ?> name="da_pds" value="1">
                                                                                <label class="custom-control-label text-xs" for="customCheck22">Duly Accomplished PDS</label>
                                                                            </div>
                                                                            <div class="custom-control custom-checkbox">
                                                                                <input type="checkbox" class="custom-control-input" id="customCheck33" <?php if($dq->prc == 1){echo "checked";} ?> name="prc" value="1">
                                                                                <label class="custom-control-label text-xs" for="customCheck33">Valid PRC License ( except for SHS which can be applied by non- licensed teachers)</label>
                                                                            </div>
                                                                            <div class="custom-control custom-checkbox">
                                                                                <input type="checkbox" class="custom-control-input" id="customCheck44" <?php if($dq->trbd == 1){echo "checked";} ?> name="trbd" value="1">
                                                                                <label class="custom-control-label text-xs" for="customCheck44">Transcript of Records of the Baccalaureate Degree</label>
                                                                            </div>
                                                                            <div class="custom-control custom-checkbox">
                                                                                <input type="checkbox" class="custom-control-input" id="customCheck55" <?php if($dq->omni == 1){echo "checked";} ?> name="omni" value="1">
                                                                                <label class="custom-control-label text-xs" for="customCheck55">Omnibus Sworn Statement</label>
                                                                            </div>
                                                                                    

                                                                                </div>	
                                                                        </div>	
                                                                        
                                                                    </div>

                                                                    <div class="row">
                                                                        <div class="col-lg-12">	
                                                                                <h4 class="header-title">Is the address in the MIS matched with the Voters Certificate/Barangay Certificate? <span class='text-danger'>*</span></h4>
                                                                                <div class="form-group">
                                                                                <div class="custom-control custom-radio">
                                                                                    <input type="radio" id="local11" name="local" <?php if($dq->local == 0){echo "checked";} ?> required class="custom-control-input" value="0">
                                                                                    <label class="custom-control-label text-xs" for="local11">Yes</label>
                                                                                </div>
                                                                                <div class="custom-control custom-radio">
                                                                                    <input type="radio" id="local22" name="local" <?php if($dq->local == 1){echo "checked";} ?> required class="custom-control-input" value="1">
                                                                                    <label class="custom-control-label text-xs" for="local22">No</label>
                                                                                </div>         

                                                                                </div>	
                                                                        </div>	
                                                                        
                                                                    </div>

                                                                    <div class="row">
                                                                        <div class="col-lg-12">	
                                                                                <h4 class="header-title">Remarks <span class='text-danger'>*</span></h4>
                                                                                <div class="form-group">
                                                                                <div class="custom-control custom-radio">
                                                                                <input type="radio" id="customRadio11" required name="remarks" <?php if($dq->remarks == 1){echo "checked";} ?> class="custom-control-input" value="1">
                                                                                <label class="custom-control-label text-xs" for="customRadio11">Qualified</label>
                                                                            </div>
                                                                            <div class="custom-control custom-radio">
                                                                                <input type="radio" id="customRadio22" required name="remarks" <?php if($dq->remarks == 2){echo "checked";} ?> class="custom-control-input" value="2">
                                                                                <label class="custom-control-label text-xs" for="customRadio22">Disqualified</label>
                                                                            </div>
                                                                                                                    

                                                                                </div>	
                                                                        </div>	
                                                                        
                                                                    </div>

                                                                        
                                                                    <div class="row">
                                                                        <div class="col-lg-12">
                                                                                <div class="form-group">
                                                                                    <label>If disqualified, state the reasons here. </label>
                                                                                    <textarea class="form-control" rows="5" name="reason" id="example-textarea"><?= $dq->reason; ?></textarea>
                                                                                </div>	
                                                                        </div>	
                                                                        
                                                                    </div>	

                                                                    
                                                                        
                                                                        

                                                                        <div class="form-group text-right mb-0">
                                                                            <button class="btn btn-info waves-effect waves-light mr-1" type="submit">
                                                                                Submit
                                                                            </button>
                                                                        </div>

                                                                    </form>
                                                                </div>
                                                            </div>
                                                            <!-- end card -->
                                                        </div>
                                                        <!-- end col -->

                            
                                                    </div>
                                                    <!-- end row -->






                                                    </div>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->
                                        <?php } ?>

                                        <!--  Modal content for the above example -->
                                        <div id="myModal" class="modal fade comment" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-info">
                                                        <h5 class="modal-title text-white" id="myLargeModalLabel">QUERY</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="card bg-in">
                                                                <div class="card-body">

                                                                <form class="parsley-examples" action="<?= base_url(); ?>pages/appinquiry/<?= $this->uri->segment(3); ?>/<?= $this->uri->segment(4); ?>/<?= $this->uri->segment(5); ?>/<?= $aa->appID; ?>" method="post">
                                                            
                                                                        
                                                                    <div class="form-group row">
                                                                        <label class="col-lg-12 col-form-label" for="example-textarea"></label>
                                                                        <div class="col-lg-12">
                                                                            <textarea class="form-control" rows="5" name="comment" id="example-textarea"></textarea>
                                                                        </div>
                                                                    </div>
                                                                    <input type="hidden" value="<?= $app->appID; ?>" name="app_id">
                                                                        
                              
                                                                    
                                                                        <div class="form-group text-right mb-0">
                                                                            <button onclick="return confirm('Are you sure?')" class="btn btn-info waves-effect waves-light mr-1" type="submit">
                                                                                Submit
                                                                            </button>
                                                                        </div>

                                                                    </form>
                                                                </div>
                                                            </div>
                                                            <!-- end card -->
                                                        </div>
                                                        <!-- end col -->

                            
                                                    </div>
                                                    <!-- end row -->


                                                    </div>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->

                                        <!--  Modal content for the above example -->
                                        <div id="myModal" class="modal fade ppstcor" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-warning">
                                                        <h5 class="modal-title text-white" id="myLargeModalLabel">PPST COIs - Classroom Observation</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <p class="text-danger"><i>Note: Maximum allowed value is 25</i></p>
                                                            <div class="card">
                                                                <div class="card-body">

                                                                <form class="parsley-examples" action="<?= base_url(); ?>pages/update_rate_promotion/<?= $this->uri->segment(3); ?>/<?= $this->uri->segment(4); ?>/0/<?= $aa->appID; ?>" method="post">
                                                                    <input type="hidden" name="app_id" value="<?= $aa->appID; ?>">
                                                                    <input type="hidden" name="empEmail" value="<?= $staff->empEmail; ?>">
                                                                    <input type="hidden" name="record_no" value="<?= $staff->record_no; ?>">
                                                                    <input type="hidden" name="school_id" value="<?= $this->uri->segment(5); ?>">
                                                                    <input type="hidden" name="page" value="<?= $this->uri->segment(2); ?>">
                                                                    <input type="hidden" name="col" value="ppstco">
                                                                    <input type="hidden" name="message" value="ppstco">
                                                                    <input type="hidden" name="maxpoint" value="25">
                                                                        
                                                                    <div class="row">
                                                                        <div class="col-lg-12">
                                                                                <div class="form-group">
                                                                                    <label>Rating</label>
                                                                                    <input type="text"  class="form-control" name="ppstco"  value="<?php if($rating->ppstco != 0.00001){echo $rating->ppstco; } ?>" >
                                                                                </div>	
                                                                        </div>	
                                                                        
                                                                    </div>

                              
                                                                    
                                                                        <div class="form-group text-right mb-0">
                                                                            <button class="btn btn-purple waves-effect waves-light mr-1" type="submit">
                                                                                Submit
                                                                            </button>
                                                                        </div>

                                                                    </form>
                                                                </div>
                                                            </div>
                                                            <!-- end card -->
                                                        </div>
                                                        <!-- end col -->

                            
                                                    </div>
                                                    <!-- end row -->


                                                    </div>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->

                                        <!--  Modal content for the above example -->
                                        <div id="myModal" class="modal fade ppstpar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-warning">
                                                        <h5 class="modal-title text-white" id="myLargeModalLabel">PPST NCOIs - Portfolio Annotations and BEI</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <p class="text-danger"><i>Note: Maximum allowed value is 15</i></p>
                                                            <div class="card">
                                                                <div class="card-body">

                                                                <form class="parsley-examples" action="<?= base_url(); ?>pages/update_rate_promotion/<?= $this->uri->segment(3); ?>/<?= $this->uri->segment(4); ?>/0/<?= $aa->appID; ?>" method="post">
                                                                    <input type="hidden" name="app_id" value="<?= $aa->appID; ?>">
                                                                    <input type="hidden" name="empEmail" value="<?= $staff->empEmail; ?>">
                                                                    <input type="hidden" name="record_no" value="<?= $staff->record_no; ?>">
                                                                    <input type="hidden" name="school_id" value="<?= $this->uri->segment(5); ?>">
                                                                    <input type="hidden" name="page" value="<?= $this->uri->segment(2); ?>">
                                                                    <input type="hidden" name="col" value="ppstpa">
                                                                    <input type="hidden" name="message" value="ppstpa">
                                                                    <input type="hidden" name="maxpoint" value="15">
                                                                        
                                                                    <div class="row">
                                                                        <div class="col-lg-12">
                                                                                <div class="form-group">
                                                                                    <label>Rating</label>
                                                                                    <input type="text"  class="form-control" name="ppstpa"  value="<?php if($rating->ppstpa != 0.00001){echo $rating->ppstpa; } ?>" >
                                                                                </div>	
                                                                        </div>	
                                                                        
                                                                    </div>

                              
                                                                    
                                                                        <div class="form-group text-right mb-0">
                                                                            <button class="btn btn-purple waves-effect waves-light mr-1" type="submit">
                                                                                Submit
                                                                            </button>
                                                                        </div>

                                                                    </form>
                                                                </div>
                                                            </div>
                                                            <!-- end card -->
                                                        </div>
                                                        <!-- end col -->

                            
                                                    </div>
                                                    <!-- end row -->


                                                    </div>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->



                                        <div class="modal fade bs-example-modal-center" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabelcenter" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="mySmallModalLabelcenter">Copy Application</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">

                                                    <?php 
                                                        $attributes = array('class' => 'parsley-examples');
                                                        echo form_open('Pages/copy_application_promotion', $attributes);
                                                    ?>
                                                    <input type="hidden" name="id" id="id" value="">
                                                    <input type="hidden" name="applicant_id" id="item" value="">
                                                        <div class="form-group row">
                                                            <label for="inputPassword3" class="col-md-3 col-form-label">Job Title</label>
                                                            <div class="col-md-9">
                                                                <select class="form-control" data-toggle="select2" name="cur_id" id="position-select">
                                                                    <option></option>
                                                                    <?php $gt = $this->Common->one_cond_select('hris_jobvacancy','jobTitle,job_type,jobID','jvStatus','Open'); ?>
                                                                    <?php foreach($gt as $row){?>
                                                                    <option value="<?= $row->jobID; ?>"><?= $row->jobTitle; ?> <?= $jobTypes[$row->job_type] ?? ''; ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    
                                                    </div>
                                                    <div class="modal-footer">
                                                        <input type="submit" name="submit" value="Copy" class="btn btn-info waves-effect waves-light">
                                                    </div>
                                                </div>
                                                </form>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->

                                       
