            <?php include('templates/head.php'); ?> 
            <?php include('templates/header.php'); ?>          

            <!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->

            <?php 
                $dist = $this->Common->no_cond('district');
                $school = $this->Common->no_cond('schools'); 
                $settings = $this->Common->one_cond_row_select('mis_settings','sgod_sign_type,settingsID','settingsID',1);

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

                $jobs = [];
                foreach($data as $row) {
                    $jobs[$row->jobID] = [
                        'jobTitle' => $row->jobTitle,
                        'jobType'  => $row->job_type
                    ];
                }

                $tjobs = [];
                foreach($teaching as $row) {
                    $tjobs[$row->jobID] = [
                        'jobTitle' => $row->jobTitle,
                        'jobType'  => $row->job_type
                    ];
                }

            
            ?>
            <style>
                .dropdown-submenu {
                position: relative;
                }

                .dropdown-submenu .dropdown-menu {
                top: 0;
                left: 100%;
                margin-top: -1px;
                display: none;
                }

                .dropdown-submenu:hover .dropdown-menu {
                display: block;
                }

                

                
            </style>

            
            <div class="content-page">
                <div class="content">

                    <!-- Start Content-->
                    <div class="container-fluid">
                 

 
                        <!-- start page title -->
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box">
                                          <?php if($this->session->position==='Human Resource Admin' || $this->session->position==='HR Staff' || $this->session->position==='Super Admin' || $this->session->position==='asds' || $this->session->position==='sds') : ?>
                                            <button type="button" class="btn btn-primary waves-effect waves-light" data-toggle="modal" data-target=".bs-example-modal-lg">Add New</button>
                                            <button type="button" class="btn btn-success waves-effect waves-light" data-toggle="modal" data-target=".aa">Search Applicant</button>

                                            <a onclick="return confirm('Are you sure?')" href="<?=base_url(); ?>Pages/unlock_ads/" class="text-primary tooltips" data-placement="top" data-toggle="tooltip" data-original-title="Unlock Applicant Document Submission"><i class="mdi mdi-pencil-lock-outline btn btn-purple"></i></a>
                                            <a onclick="return confirm('Are you sure?')" href="<?=base_url(); ?>Pages/lock_ads/" class="text-warning tooltips" data-placement="top" data-toggle="tooltip" data-original-title="lock Applicant Document Submission"><i class="mdi mdi-pencil-lock-outline btn btn-warning"></i></a>

                                            <!-- <?php if($lock->num_rows() >= 1){ ?>
                                                    <a onclick="return confirm('Are you sure?')" href="<?=base_url(); ?>Pages/lock_applications/" class="text-primary tooltips" data-placement="top" data-toggle="tooltip" data-original-title="Lock Applications"><i class="mdi mdi-pencil-lock-outline btn btn-warning"></i></a>
                                                <?php }else{ ?>
                                                    <a onclick="return confirm('Are you sure?')" href="<?=base_url(); ?>Pages/unlock_applications/" class="text-primary tooltips" data-placement="top" data-toggle="tooltip" data-original-title="Unlock Applications"><i class="mdi mdi-account-edit btn btn-primary"></i></a>
                                            <?php } ?> -->
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-secondary dropdown-toggle waves-effect" data-toggle="dropdown" aria-expanded="false">Teaching Reports <i class="mdi mdi-chevron-down"></i> </button>
                                                    <div class="dropdown-menu">
                                                        <a class="dropdown-item" target="_blank" href="<?= base_url(); ?>Pages/ssc_report">SSC Status Report</a>
                                                        <a class="dropdown-item" target="_blank" href="<?= base_url(); ?>Pages/abd_report">Endorsed Applicant - District</a>
                                                        <a class="dropdown-item" target="_blank" href="<?= base_url(); ?>Pages/abd_report_rr">Requested Rating - District</a>
                                                        <a class="dropdown-item" target="_blank" href="<?= base_url(); ?>Pages/abd_report_validated">Validated - District</a>
                                                        <a class="dropdown-item" target="_blank" href="<?= base_url(); ?>Pages/abd_report_multiple">Multiple Application</a>
                                                        <a class="dropdown-item" target="_blank" href="<?= base_url(); ?>Pages/rated_applicantion">Rated Application</a>
                                                        <?php if($this->session->userdata('position')==='Super Admin' || $this->session->position==='Super Admin'): ?>
                                                        <a class="dropdown-item" target="_blank" href="<?= base_url(); ?>Pages/rated_applicants_missing_scores">Rated Applicants (No Scores)</a>
                                                        <?php endif; ?>
                                                        <a class="dropdown-item" target="_blank" href="<?= base_url(); ?>Pages/jshs_applicantion/3">JHS Application</a>
                                                        <a class="dropdown-item" target="_blank" href="<?= base_url(); ?>Pages/jshs_applicantion/4">SHS Application</a>
                                                        <!-- <a class="dropdown-item" target="_blank" href="<?= base_url(); ?>Pages/abd_report_shs">SHS Application</a>
                                                        <a class="dropdown-item" target="_blank" href="<?= base_url(); ?>Pages/abd_report_jhs">JHS Application</a> -->
                                                        <!-- <a class="dropdown-item" target="_blank" href="<?= base_url(); ?>Pages/abd_reportv2">By District TR</a>
                                                        <a class="dropdown-item" target="_blank" href="<?= base_url(); ?>Pages/abd_reportv3">Report V2</a>
                                                        <a class="dropdown-item" target="_blank" href="<?= base_url(); ?>Pages/abd_reportv4">Report V3</a>
                                                        <a class="dropdown-item" target="_blank" href="<?= base_url(); ?>Pages/abd_reportv5">Report V4</a>
                                                        <a class="dropdown-item" target="_blank" href="<?= base_url(); ?>Pages/abd_reportv6">Report V5</a>
                                                        <a class="dropdown-item" target="_blank" href="<?= base_url(); ?>Pages/lva">By School</a> -->
                                                        <a class="dropdown-item" target="_blank" href="<?= base_url(); ?>Pages/dabd_report">DQ List</a>
                                                        <a class="dropdown-item" target="_blank" href="<?= base_url(); ?>Pages/dabq_report">Qualified List</a>
                                                        <a class="dropdown-item" target="_blank" href="<?= base_url(); ?>Pages/dabq_reportv2">Qualified List - District </a>
                                                        <a class="dropdown-item" target="_blank" href="<?= base_url(); ?>Pages/dabq_reportv2_summary">Summary Of Applicants - Job Title</a>
                                                        <a class="dropdown-item" target="_blank" href="<?= base_url(); ?>Pages/abd_report_district">Summary Of Applicant - District</a>
                                                        <a class="dropdown-item" target="_blank" href="<?= base_url(); ?>Page/applicantList">Validated Applicants (All)</a>
                                                    </div>
                                                </div>

                                                <div class="btn-group">
                                                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    Reports by Job Title <i class="mdi mdi-chevron-down"></i>
                                                </button>
                                                <div class="dropdown-menu">

                                                    <div class="dropdown-submenu">
                                                        <a class="dropdown-item dropdown-toggle" href="#">Submitted Applicants</a>
                                                        <div class="dropdown-menu">
                                                            <?php foreach($jobs as $jobID => $info): ?>
                                                                    <a class="dropdown-item" href="<?= base_url(); ?>Page/submittedApplicantsByJob/<?= $jobID; ?>" target="_blank">
                                                                    <?= $info['jobTitle']; ?> 
                                                                    <?= $jobTypes[$info['jobType']] ?? ''; ?>
                                                                </a>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    </div>

                                                    <div class="dropdown-submenu">
                                                        <a class="dropdown-item dropdown-toggle" href="#">Validated Applicants</a>
                                                        <div class="dropdown-menu">
                                                            <?php foreach($jobs as $jobID => $info): ?>
                                                                    <a class="dropdown-item" href="<?= base_url(); ?>Page/validatedApplicantsByJob/<?= $jobID; ?>" target="_blank">
                                                                    <?= $info['jobTitle']; ?> 
                                                                    <?= $jobTypes[$info['jobType']] ?? ''; ?>
                                                                </a>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    </div>

                                                    <div class="dropdown-submenu">
                                                        <a class="dropdown-item dropdown-toggle" href="#">Shortlist</a>
                                                        <div class="dropdown-menu">
                                                            <?php foreach($jobs as $jobID => $info): ?>
                                                                    <a class="dropdown-item" href="<?= base_url(); ?>Page/endorsedApplicantsByJob/<?= $jobID; ?>" target="_blank">
                                                                    <?= $info['jobTitle']; ?> 
                                                                    <?= $jobTypes[$info['jobType']] ?? ''; ?>
                                                                </a>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    </div>

                                                    

                                                    <div class="dropdown-submenu">
                                                        <a class="dropdown-item dropdown-toggle" href="#">Rated Applicants</a>
                                                        <div class="dropdown-menu">
                                                            <?php foreach($jobs as $jobID => $info): ?>
                                                                    <a class="dropdown-item" href="<?= base_url(); ?>Page/ratedApplicantsByJob/<?= $jobID; ?>" target="_blank">
                                                                    <?= $info['jobTitle']; ?> 
                                                                    <?= $jobTypes[$info['jobType']] ?? ''; ?>
                                                                </a>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    </div>

                                                    <div class="dropdown-submenu">
                                                        <a class="dropdown-item dropdown-toggle" href="#">Counts by District</a>
                                                        <div class="dropdown-menu">
                                                            <?php foreach($jobs as $jobID => $info): ?>
                                                                <a class="dropdown-item" href="<?= base_url(); ?>Page/applicantCountsByDistrict/<?= $jobID; ?>" target="_blank">
                                                                    <?= $info['jobTitle']; ?> 
                                                                    <?= $jobTypes[$info['jobType']] ?? ''; ?>
                                                                </a>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    </div>

                                                    

                                                    

                                                </div>
                                                </div>

                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-purple dropdown-toggle waves-effect" data-toggle="dropdown" aria-expanded="false">Related and  Non-Teaching/School Administration Report<i class="mdi mdi-chevron-down"></i> </button>
                                                    <div class="dropdown-menu">
                                                        <!-- <a class="dropdown-item" href="<?= base_url(); ?>Pages/hiring_non_all_applicant" target="_blank" >List of Applicant</a> -->
                                                        
                                                        <div class="dropdown-submenu">
                                                            <a class="dropdown-item dropdown-toggle" href="#">IER</a>
                                                            <div class="dropdown-menu" style="max-height: 260px;overflow-y: auto; overflow-x: hidden;">
                                                                <?php foreach($jobs as $jobID => $info): ?>
                                                                        <a class="dropdown-item" href="<?= base_url(); ?>Pages/all_non_teaching_applicant/<?= $jobID; ?>" target="_blank">
                                                                        <?= $info['jobTitle']; ?> 
                                                                        <?= $jobTypes[$info['jobType']] ?? ''; ?>
                                                                    </a>
                                                                <?php endforeach; ?>
                                                            </div>
                                                        </div>

                                                        <div class="dropdown-submenu">
                                                            <a class="dropdown-item dropdown-toggle" href="#">IER V2</a>
                                                            <div class="dropdown-menu" style="max-height: 260px;overflow-y: auto; overflow-x: hidden;">
                                                                <?php foreach($jobs as $jobID => $info): ?>
                                                                        <a class="dropdown-item" href="<?= base_url(); ?>Pages/all_non_teaching_applicantv2/<?= $jobID; ?>" target="_blank">
                                                                        <?= $info['jobTitle']; ?> 
                                                                        <?= $jobTypes[$info['jobType']] ?? ''; ?>
                                                                    </a>
                                                                <?php endforeach; ?>
                                                            </div>
                                                        </div>

                                                        <div class="dropdown-submenu">
                                                            <a class="dropdown-item dropdown-toggle" href="#">IER V3</a>
                                                            <div class="dropdown-menu" style="max-height: 260px;overflow-y: auto; overflow-x: hidden;">
                                                                <?php foreach($jobs as $jobID => $info): ?>
                                                                        <a class="dropdown-item" href="<?= base_url(); ?>Pages/all_non_teaching_applicantv3/<?= $jobID; ?>" target="_blank">
                                                                        <?= $info['jobTitle']; ?> 
                                                                        <?= $jobTypes[$info['jobType']] ?? ''; ?>
                                                                    </a>
                                                                <?php endforeach; ?>
                                                            </div>
                                                        </div>

                                                        <div class="dropdown-submenu">
                                                            <a class="dropdown-item dropdown-toggle" href="#">IER V4</a>
                                                            <div class="dropdown-menu" style="max-height: 260px;overflow-y: auto; overflow-x: hidden;">
                                                                <?php foreach($jobs as $jobID => $info): ?>
                                                                        <a class="dropdown-item" href="<?= base_url(); ?>Pages/all_non_teaching_applicantv4/<?= $jobID; ?>" target="_blank">
                                                                        <?= $info['jobTitle']; ?> 
                                                                        <?= $jobTypes[$info['jobType']] ?? ''; ?>
                                                                    </a>
                                                                <?php endforeach; ?>
                                                            </div>
                                                        </div>

                                                        <div class="dropdown-submenu">
                                                            <a class="dropdown-item dropdown-toggle" href="#">IER Group by Municipality</a>
                                                            <div class="dropdown-menu" style="max-height: 260px;overflow-y: auto; overflow-x: hidden;">
                                                                <?php foreach($jobs as $jobID => $info): ?>
                                                                        <a class="dropdown-item" href="<?= base_url(); ?>Pages/ier_group_mun/<?= $jobID; ?>" target="_blank">
                                                                        <?= $info['jobTitle']; ?> 
                                                                        <?= $jobTypes[$info['jobType']] ?? ''; ?>
                                                                    </a>
                                                                <?php endforeach; ?>
                                                            </div>
                                                        </div>
                                                        <div class="dropdown-submenu">
                                                            <a class="dropdown-item dropdown-toggle" href="#">IER Group by Municipality V2</a>
                                                            <div class="dropdown-menu" style="max-height: 260px;overflow-y: auto; overflow-x: hidden;">
                                                                <?php foreach($jobs as $jobID => $info): ?>
                                                                        <a class="dropdown-item" href="<?= base_url(); ?>Pages/ier_group_munv2/<?= $jobID; ?>" target="_blank">
                                                                        <?= $info['jobTitle']; ?> 
                                                                        <?= $jobTypes[$info['jobType']] ?? ''; ?>
                                                                    </a>
                                                                <?php endforeach; ?>
                                                            </div>
                                                        </div>
                                                        <div class="dropdown-submenu">
                                                            <a class="dropdown-item dropdown-toggle" href="#">IER Learning Area Specialization</a>
                                                            <div class="dropdown-menu" style="max-height: 260px;overflow-y: auto; overflow-x: hidden;">
                                                                <?php foreach($tjobs as $jobID => $info): ?>
                                                                        <a class="dropdown-item" href="<?= base_url(); ?>Pages/las/<?= $jobID; ?>" target="_blank">
                                                                        <?= $info['jobTitle']; ?> 
                                                                        <?= $jobTypes[$info['jobType']] ?? ''; ?>
                                                                    </a>
                                                                <?php endforeach; ?>
                                                            </div>
                                                        </div>
                                                        <a class="dropdown-item" href="<?= base_url(); ?>Pages/hiring_non_qaulified" target="_blank" >Qualified List</a>
                                                        <a class="dropdown-item" href="<?= base_url(); ?>Pages/hiring_non_not_qaulified" target="_blank" >Not Qualified List</a>
                                                        <a class="dropdown-item" href="<?= base_url(); ?>Pages/not_qualify_promotion" target="_blank" >Not Qualified List V2</a>
                                                        <a class="dropdown-item" href="<?= base_url(); ?>Pages/hiring_non_qaulified_rated" target="_blank" >Qualified List Rated</a>
                                                        
                                                        <div class="dropdown-submenu">
                                                            <a class="dropdown-item" href="<?= base_url(); ?>Pages/qaulified_promotion" target="_blank" >Qualified List Promotion</a>
                                                            <div class="dropdown-menu" style="max-height: 260px;overflow-y: auto; overflow-x: hidden;">
                                                                <?php foreach($ren as $pro): ?>
                                                                        <a class="dropdown-item" href="<?= base_url(); ?>Pages/qaulified_promotion_job/<?= $pro->jobID; ?>" target="_blank">
                                                                         <?= $pro->jobTitle; ?> <?= $jobTypes[$pro->job_type] ?? ''; ?>   
                                                                    </a>
                                                                <?php endforeach; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

           


                                        <?php elseif($this->session->position == "doceval"):?>   
                                    
                                            <a target="_blank" href="<?= base_url(); ?>Pages/dabd_report" class="btn btn-info">Disqualified List</a>
                                            <a target="_blank" href="<?= base_url(); ?>Pages/dabq_report" class="btn btn-primary">Qualified List</a>
                                           
                                            
                                        <?php elseif($this->session->position == "School"):?>   
                                            <a href="<?= base_url(); ?>Pages/school_generate_report" class="btn btn-primary waves-effect waves-light" target="_blank">List of Validated Applicants</a>
                                        <?php elseif($this->session->position === 'Evaluator' || $this->session->position === 'Super Admin' || $this->session->position === 'Human Resource Admin' || $this->session->position === 'HR Staff') : ?>
                                            <!-- <button type="button" class="btn btn-primary waves-effect waves-light" data-toggle="modal" data-target=".aa">Search Applicant</button> &nbsp; &nbsp;
                                            <a href="<?= base_url(); ?>Pages/hiring_non_qaulified_rated" target="_blank" class="btn btn-info">Qalified List Rated</a> -->
                                        <?php elseif($this->session->position == "District"):?>
                                            <a href="<?= base_url(); ?>Pages/dgr" class="btn btn-primary waves-effect waves-light" target="_blank">Generate Transmittal</a>
                                            <?php endif;?>

                                            <?php if($this->session->position == 'raters'){?>

                                            <div class="btn-group">
                                                    <button type="button" class="btn btn-purple dropdown-toggle waves-effect" data-toggle="dropdown" aria-expanded="false">Related and  Non-Teaching/School Administration Report<i class="mdi mdi-chevron-down"></i> </button>
                                                    <div class="dropdown-menu">
                                                        <!-- <a class="dropdown-item" href="<?= base_url(); ?>Pages/hiring_non_all_applicant" target="_blank" >List of Applicant</a> -->
                                                        
                                                        <div class="dropdown-submenu">
                                                            <a class="dropdown-item dropdown-toggle" href="#">IER</a>
                                                            <div class="dropdown-menu">
                                                                <?php foreach($jobs as $jobID => $info): ?>
                                                                        <a class="dropdown-item" href="<?= base_url(); ?>Pages/all_non_teaching_applicant/<?= $jobID; ?>" target="_blank">
                                                                        <?= $info['jobTitle']; ?> 
                                                                        <?= $jobTypes[$info['jobType']] ?? ''; ?>
                                                                    </a>
                                                                <?php endforeach; ?>
                                                            </div>
                                                        </div>
                                                        <div class="dropdown-submenu">
                                                            <a class="dropdown-item dropdown-toggle" href="#">IER Group by Municipality</a>
                                                            <div class="dropdown-menu">
                                                                <?php foreach($jobs as $jobID => $info): ?>
                                                                        <a class="dropdown-item" href="<?= base_url(); ?>Pages/ier_group_mun/<?= $jobID; ?>" target="_blank">
                                                                        <?= $info['jobTitle']; ?> 
                                                                        <?= $jobTypes[$info['jobType']] ?? ''; ?>
                                                                    </a>
                                                                <?php endforeach; ?>
                                                            </div>
                                                        </div>
                                                        <a class="dropdown-item" href="<?= base_url(); ?>Pages/hiring_non_qaulified" target="_blank" >Qualified List</a>
                                                        <a class="dropdown-item" href="<?= base_url(); ?>Pages/hiring_non_not_qaulified" target="_blank" >Not Qualified List</a>
                                                        <a class="dropdown-item" href="<?= base_url(); ?>Pages/hiring_non_qaulified_rated" target="_blank" >Qualified List Rated</a>
                                                        
                                                        <div class="dropdown-submenu">
                                                            <a class="dropdown-item" href="<?= base_url(); ?>Pages/qaulified_promotion" target="_blank" >Qualified List Promotion</a>
                                                            <div class="dropdown-menu">
                                                                <?php foreach($ren as $pro): ?>
                                                                        <a class="dropdown-item" href="<?= base_url(); ?>Pages/qaulified_promotion_job/<?= $pro->jobID; ?>" target="_blank">
                                                                         <?= $pro->jobTitle; ?> <?= $jobTypes[$pro->job_type] ?? ''; ?>   
                                                                    </a>
                                                                <?php endforeach; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php } ?>



                                                
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->

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

                            

                        

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body table-responsive">
                                    <h4 class="header-title mb-4">Job Vacancies</h4><br />
                                       
                                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>Job Title</th>
												<th>Emp. Type</th>
                                                <th>Date Posted</th>
                                                <th>Office/Bureau/Service/<br />Unit where the vacancy exists</th>
                                                <!-- <th>Posted By</th> -->
                                                <th>Attachment</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                               <?php
										  $i=1;
										  foreach($data as $row){ 
                                            if($this->session->position==='Evaluator'){
                                            $d = $this->Common->one_cond_row('district','id',$this->session->c_id);
                                            $all_app = $this->Common->three_cond_count_row('hris_applications', 'jobID', $row->jobID,'appStatus','Endorsed for Rating','district',$d->discription);
                                            }
                                            $job = $this->Common->two_cond_count_row('hris_applications','jobID',$row->jobID,'empEmail',$this->session->username);
                                            $applicant = $this->Common->three_cond_count_row('hris_applications', 'jobID', $row->jobID,'pre_school',$this->session->c_id,'appStatus','Application Submitted');
                                            
										 ?> 

                                        <tr>
                                            <td>
                                                <?= $row->jobTitle; ?> <?php echo $jobTypes[$row->job_type] ?? ''; ?>
                                            </td>
                                            <td><?= $row->empType; ?></td>
                                            <td><?= $row->datePosted; ?></td>
                                            <td><?= $row->assign; ?></td>
                                            <td>
                                                <?php if($row->file != ""){?>
                                                    <a  href="<?= base_url().'uploads/regfile/'.$row->file; ?>" target="_blank" class="tooltips" data-placement="top" data-toggle="tooltip" data-original-title="View File Attachment"><i  class="fas fa-file-alt btn btn-lg text-primary"></i></a>
                                                <?php if($this->session->position==='Admin' || $this->session->position==='Super Admin' || $this->session->position==='HR Staff' || $this->session->position==='Human Resource Admin' || $this->session->position==='asds'): ?>        
                                                    <a href="#" data-id="<?= $row->jobID; ?>" class="text-warning waves-effect waves-light open-AddBookDialog" data-toggle="modal" data-target=".edit_vacancy_file"><i class="mdi mdi-file-image-outline tooltips" data-placement="top" data-toggle="tooltip" data-original-title="Edit File Attachment"></i></a>
                                                <?php endif;?>
                                                <?php }else{ ?>
                                                    <?php if($this->session->userdata('position')==='Human Resource Admin' || $this->session->userdata('position')==='HR Staff' || $this->session->userdata('position')==='Super Admin'){?>
                                                    <a href="#" data-id="<?= $row->jobID; ?>" class="text-warning waves-effect waves-light open-AddBookDialog" data-toggle="modal" data-target=".edit_vacancy_file"><i class="mdi mdi-file-image-outline tooltips" data-placement="top" data-toggle="tooltip" data-original-title="Edit File Attachment"></i></a>
                                                
                                                <?php }} ?>
                                            </td>


                                            
                                          <td class="text-center">
                                            <?php $validated = $this->Common->three_cond_count_row('hris_applications','jobID',$row->jobID,'dq',0,'appStatus','Validated'); ?>
                                            <?php $rated = $this->Common->three_cond_count_row('hris_applications','jobID',$row->jobID,'dq',1,'appStatus','Endorsed for Rating')?>

                                          <?php if($this->session->position==='raters'){ ?>
                                          <a href="<?=base_url(); ?>Pages/sa_view_applicant/<?= $row->jobID; ?>" class="btn btn-primary tooltips" data-placement="top" data-toggle="tooltip" data-original-title="Validated Applicants"><span class="badge badge-success"><?= $validated->num_rows(); ?></span> <i class="mdi mdi-file-document-box-check-outline"></i></a>
                                          <?php } ?>

                                          <?php if($this->session->position==='Human Resource Admin' || $this->session->position==='HR Staff' || $this->session->position==='Super Admin' || $this->session->position==='asds' || $this->session->position==='sds') : ?>

                                                        

                                                        <a href="<?=base_url(); ?>Page/viewApplicants?jobID=<?php echo $row->jobID; ?>&jobTitle=<?php echo $row->jobTitle; ?> <?= $jobTypes[$row->job_type] ?? ''; ?>" class="btn btn-info tooltips" data-placement="top" data-toggle="tooltip" data-original-title="View Applicants"><i class="mdi mdi-file-document-box-outline"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;

                                                        <a onclick="return confirm('Are you sure?')" href="<?=base_url(); ?>Pages/unlock_ads/<?= $row->jobID; ?>" class="text-primary tooltips" data-placement="top" data-toggle="tooltip" data-original-title="Unlock Applicant Document Submission for this Position"><i class="mdi mdi-pencil-lock-outline btn btn-purple"></i></a>
                                                        <a onclick="return confirm('Are you sure?')" href="<?=base_url(); ?>Pages/lock_ads/<?= $row->jobID; ?>" class="text-warning tooltips" data-placement="top" data-toggle="tooltip" data-original-title="Lock Applicant Document Submission for this Position"><i class="mdi mdi-pencil-lock-outline btn btn-warning"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;
                                                       
                                                        <?php if($validated->num_rows() != 0){ ?>
                                                        <a href="<?=base_url(); ?>Pages/sa_view_applicant/<?= $row->jobID; ?>" class="btn btn-primary tooltips" data-placement="top" data-toggle="tooltip" data-original-title="Validated Applicants"><span class="badge badge-success"><?= $validated->num_rows(); ?></span> <i class="mdi mdi-file-document-box-check-outline"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;
                                                        <?php } ?>
                                                        
                                                        <?php if($rated->num_rows() != 0){ ?>
                                                        <a href="<?=base_url(); ?>Pages/sa_view_applicant_endorsed/<?= $row->jobID; ?>" class="btn btn-warning tooltips" data-placement="top" data-toggle="tooltip" data-original-title="Endorsed Applicants"><span class="badge badge-success"><?= $rated->num_rows(); ?></span> <i class="mdi mdi-file-document-box-check-outline"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;
                                                        <?php } ?>

                                                        <a target="_blank" href="<?=base_url(); ?>Pages/rqa_list/<?= $row->sy; ?>?jobID=<?php echo $row->jobID; ?>&jobTitle=<?php echo $row->jobTitle; ?><?= $jobTypes[$row->job_type] ?? ''; ?>" class="btn btn-success tooltips" data-placement="top" data-toggle="tooltip" data-original-title="RQA"><i class="mdi mdi-calculator"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;

                                                        <a onclick="return confirm('Are you sure?')" href="<?=base_url(); ?>Pages/close_job/<?= $row->jobID; ?>" class="btn btn-pink"><i class="fas fa-lock-open tooltips" data-placement="top" data-toggle="tooltip" data-original-title="Archive"></i></a>

                                                        <a href="<?=base_url(); ?>Pages/edit_vacancy/<?= $row->jobID; ?>" class="btn btn-purple"><i class="mdi mdi-file-document-edit-outline tooltips" data-placement="top" data-toggle="tooltip" data-original-title="Edit Job Vacancy"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;
                                                        <?php if($row->a_stat == 0){ ?>
                                                            <a href="<?=base_url(); ?>Pages/closed_vacancy/<?= $row->jobID; ?>" class="btn btn-danger"><i class="mdi mdi-account-search tooltips" data-placement="top" data-toggle="tooltip" data-original-title="Closed Vacancy"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;
                                                        <?php }else{ ?>
                                                            <a href="<?=base_url(); ?>Pages/open_vacancy/<?= $row->jobID; ?>" class="btn btn-success"><i class="mdi mdi-account-search tooltips" data-placement="top" data-toggle="tooltip" data-original-title="Open Vacancy"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;
                                                        <?php } ?>

                                                        <?php 
                                                            if($row->position == 1){
                                                            $ca = $this->Common->two_cond_count_row('hris_applications', 'jobID', $row->jobID,'appStatus','Validated');
                                                            if($ca->num_rows() <= 1){
                                                        ?>
                                                        
                                                        <a onclick="return confirm('Are you sure?')" href="<?=base_url(); ?>Pages/validated_by_job_id/<?= $row->jobID; ?>" class="btn btn-primary"><i class="fas fa-handshake tooltips" data-placement="top" data-toggle="tooltip" data-original-title="Validate"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;
                                                        <?php } }?>


                                                        <?php elseif($this->session->position == 'doceval'): ?>
                                                    <a href="<?=base_url(); ?>Pages/sa_view_applicant/<?= $row->jobID; ?>" class="btn btn-primary tooltips" data-placement="top" data-toggle="tooltip" data-original-title="Validated Applicants"><i class="mdi mdi-file-document-box-check-outline"></i></a>
                                            
                                                <?php elseif($this->session->position == 'District'): ?>
                                                    <?php 
                                                        $d = $this->Common->one_cond_row('district','id',$this->session->c_id);
                                                        $acount = $this->Common->three_cond_count_row('hris_applications', 'jobID', $row->jobID,'district',$d->discription,'appStatus','Application Submitted');
                                                    ?>
                                                    <a href="<?=base_url(); ?>Pages/school_list_applicant/<?= $row->jobID; ?>" class="btn btn-success tooltips" data-placement="top" data-toggle="tooltip" data-original-title="View School">
                                                    <?php if($acount->num_rows() !=0){?>
                                                    <span class="badge badge-warning rounded-circle noti-icon-badge"><?= $acount->num_rows(); ?></span>
                                                    <?php } ?>
                                                    <i class="mdi mdi-file-document-box-outline"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;
                                                    
                                                    

                                                <?php elseif($this->session->position == 'Evaluator'): ?>
                                                    
                                                    <a href="<?=base_url(); ?>Pages/evaluator_applicant/<?= $row->jobID; ?>" class="btn btn-success tooltips" data-placement="top" data-toggle="tooltip" data-original-title="Applicants">
                                                    <?php if($all_app->num_rows() !=0){?>
                                                    <span class="badge badge-warning rounded-circle noti-icon-badge"><?= $all_app->num_rows(); ?></span>
                                                    <?php } ?>
                                                    <i class="mdi mdi-file-document-box-outline"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;

                                                <?php elseif($this->session->position == 'rater'): ?>

                                                    
                                                    <a href="<?=base_url(); ?>Pages/evaluator_applicant/<?= $row->jobID; ?>" class="btn btn-success tooltips" data-placement="top" data-toggle="tooltip" data-original-title="Applicants">
                                                    
                                                    <i class="mdi mdi-file-document-box-outline"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;
                                                    
                                                        
                                                <?php elseif($this->session->position == 'School' ): ?>

                                                    
                                                <a href="<?=base_url(); ?>Pages/school_applicant/<?= $row->jobID; ?>/0" class="btn btn-success tooltips" data-placement="top" data-toggle="tooltip" data-original-title="Validate Applicants">
                                                <?php if($applicant->num_rows() !=0){?>
                                                    <span class="badge badge-warning rounded-circle noti-icon-badge"><?= $applicant->num_rows(); ?></span>
                                                    <?php } ?>
                                                <i class="mdi mdi-file-document-box-check-outline"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;
                                                <a href="<?=base_url(); ?>Pages/school_applicant/<?= $row->jobID; ?>/1" class="btn btn-primary tooltips" data-placement="top" data-toggle="tooltip" data-original-title="All Applicants"><i class="mdi mdi-file-document-box-search-outline"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;

                                                

                                                <?php elseif($this->session->position==='reg'):?>
                                                    <?php if($row->a_stat == 0){if($row->position == 1){ ?>

                                                        <a href="#" data-toggle="modal" data-job="<?= $row->jobID; ?>" data-target=".apply" class="open-AddBookDialog"><i class="fas fa-clipboard btn btn-success tooltips" data-placement="top" data-toggle="tooltip" data-original-title="Apply"></i></a>
                                                    <?php }else{ ?>
                                                        <!-- <a href="#" data-toggle="modal" data-job="<?= $row->jobID; ?>" data-target=".applyme" class="open-AddBookDialog"><i class="fas fa-clipboard btn btn-success tooltips" data-placement="top" data-toggle="tooltip" data-original-title="Apply dsad"></i></a> -->
                                                         <a href="<?= base_url(); ?>Pages/submit_application_non_teaching/<?= $row->jobID; ?>" class="open-AddBookDialog"><i class="fas fa-clipboard btn btn-success tooltips" data-placement="top" data-toggle="tooltip" data-original-title="Apply"></i></a>

                                                    <?php } } ?>
                                                    
                                                <?php elseif($this->session->userdata('position')==='user'):?>
                                                    <?php if($row->a_stat == 0){ if($row->position == 1){ ?>

                                                    <a href="#" data-toggle="modal" data-job="<?= $row->jobID; ?>" data-target=".apply" class="open-AddBookDialog"><i class="fas fa-clipboard btn btn-success tooltips" data-placement="top" data-toggle="tooltip" data-original-title="Apply"></i></a>
                                                    <?php }else{ ?>
                                                         <a href="<?= base_url(); ?>Pages/submit_application_non_teaching/<?= $row->jobID; ?>" class="open-AddBookDialog"><i class="fas fa-clipboard btn btn-success tooltips" data-placement="top" data-toggle="tooltip" data-original-title="Apply"></i></a>

                                                    <?php } } ?>
                                                <?php endif;?>

                                                
                                           </td>

                                                    <?php echo "</tr>"; } ?>
                                        </tbody>

                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end row -->







                    </div>
                    <!-- end container-fluid -->

                </div>
                <!-- end content -->

                <?php include('templates/footer.php'); ?>  

       
       
                                        <!--  Add New Vacancies -->
                                        <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="myLargeModalLabel">Job Vacancy Posting</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                        
                                                    <?php 
                                                        $attributes = array('class' => 'parsley-examples');
                                                        echo form_open_multipart('Page/jobVacancy', $attributes);
                                                    ?>
                                                        <div class="form-group row">
                                                            <label for="inputPassword3" class="col-md-3 col-form-label">Position</label>
                                                            <div class="col-md-9">
                                                                <select class="form-control" name="position" id="position-select">
                                                                    <option></option>
                                                                    <?php $gt = array('Teaching' => 1,'School Administration' => 2,'Related Teaching' => 3, 'Non-Teaching' => 4); ?>
                                                                    <?php foreach($gt as $row=>$key){?>
                                                                    <option value="<?= $key; ?>"><?= $row; ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    
                                                        <div class="form-group row">
                                                            <label for="inputEmail3" class="col-md-3 col-form-label">Position Title</label>
                                                            <div class="col-md-9">
                                                                <select class="form-control" name="jobTitle" id="job-title-select">
                                                                    <option></option>
                                                                    <?php foreach($pos_title as $row){?>
                                                                    <option value="<?= $row->title; ?>" data-pos_id="<?= $row->pos_id; ?>" data-position_id="<?= $row->id; ?>"><?= $row->title; ?> </option>
                                                                    <?php } ?>

                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div id="teaching-group-type" style="display:none;">
                                                        <div class="form-group row">
                                                            
                                                            <label for="inputPassword3" class="col-md-3 col-form-label">Teaching Group Type</label>
                                                            <div class="col-md-9">
                                                                <select class="form-control" name="job_type1">
                                                                    <option value="0"></option>
                                                                    <?php $gt = array('Elementary' => 1,
                                                                    'Secondary'=>2,
                                                                    'Junior High School' => 3,
                                                                    'Senior High School' => 4, 
                                                                    'kindergarten' => 5, 
                                                                    'IPED Elementary' => 6,
                                                                    'IPED Secondary' =>7, 
                                                                    'IPED Junior High School' => 8, 
                                                                    'IPED Senior High School' => 9, 
                                                                    'SNED' => 10,
                                                                    'Senior High School - Academic and Core Subjects' => 11,
                                                                    'Senior High School - Arts and Design Track' => 12,
                                                                    'Senior High School - Sports Track' => 13,
                                                                    'Senior High School - Technical-Vocational(TVL) Track' => 14,
                                                                    'Elementary - SPIMS' => 15,
                                                                    'Junior High School - SPIMS' => 16,
                                                                    'DOST - (RA 7687)' => 17,
                                                                    'DOST - (RA 10612)' => 18,
                                                                    '(SST I)' => 19,

                                                                    'FOR TESTING PURPOSES (DO NOT APPLY)' => 20,
                                                                    ); ?>
                                                                    <?php foreach($gt as $row=>$key){?>
                                                                    <option value="<?= $key; ?>"><?= $row; ?></option>
                                                                    <?php } ?>

                                                                </select>
                                                            </div>
                                                            </div>
                                                        </div>

                                                        <div id="admin-group-type" style="display:none;">
                                                        <div class="form-group row">
                                                            
                                                            <label for="inputPassword3" class="col-md-3 col-form-label">Administration Group Type</label>
                                                            <div class="col-md-9">
                                                                <select class="form-control" name="job_type">
                                                                    <option value="0"></option>
                                                                    <?php $gt = array(
                                                                        'Elementary' => 1,
                                                                        'Secondary' => 2,
                                                                        'Junior High School'=>3,
                                                                        'Senior High School'=>4
                                                                    );
                                                                        
                                                                    ?>
                                                                    <?php foreach($gt as $row=>$key){?>
                                                                    <option value="<?= $key; ?>"><?= $row; ?></option>
                                                                    <?php } ?>

                                                                </select>
                                                            </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="form-group row">
                                                            <label for="inputPassword3" class="col-md-3 col-form-label">Employment Type</label>
                                                            <div class="col-md-9">
                                                                <select class="form-control" name="empType">
                                                                    <option></option>
                                                                    <option>Permanent Position</option>
                                                                    <option>Job Order</option>
                                                                    <option>Contract of Service</option>
                                                                    <option>Provisional</option>

                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label for="inputPassword5" class="col-md-3 col-form-label">Attachment</label>
                                                            <div class="col-md-9">
                                                                <input type="file" class="form-control" name="file" required>
                                                            </div>
                                                        </div>
                                                        


                                                        <div class="form-group row">
                                                            <label for="inputPassword5" class="col-md-3 col-form-label">Year</label>
                                                            <div class="col-md-9">
                                                            <select class="form-control" name="sy" required>
                                                                <option></option>
                                                            <?php 
                                                            $firstYear = (int)date('Y');
                                                            $lastYear = $firstYear + 5;
                                                            
                                                            for($i=$firstYear;$i<=$lastYear;$i++)
                                                            { 
                                                                echo '<option value='.$i.'>'.$i.'</option>';
                                                            }
                                                            ?>
                                                            </select>
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label for="inputPassword5" class="col-md-3 col-form-label">Office/Bureau/Service/Unit where the vacancy exists</label>
                                                            <div class="col-lg-9">
                                                                <input type="text" name="assign" class="form-control" value="" required>
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label for="inputPassword5" class="col-md-3 col-form-label">ECP</label>
                                                            <div class="col-md-9">
                                                            <select class="form-control" name="ecp" required>
                                                                <option></option>
                                                                <option value="0">No</option>
                                                                <option value="1">Yes</option>
                                                            </select>
                                                            </div>
                                                        </div>

                                                        <input type="hidden" name="position_id" id="pos-id-input" value="" />

                                                       
                                                        <div class="form-group mb-0 justify-content-end row">
                                                            <div class="col-md-9">
                                                                <input type="submit" name="submit" value="Submit" class="btn btn-primary waves-effect waves-light">
                                                                <!-- <button type="submit" class="btn btn-info waves-effect waves-light">Submit</button> -->
                                                            </div>
                                                        </div>
                                                    </form>

                                                    </div>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->
                                      
                                        <!--  Add New Vacancies -->
                                        <div class="modal fade bs-example-modal-apply" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="myLargeModalLabel">Submit an Application</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                        
                                                    <form class="form-horizontal" method="post">
                                                        <div class="form-group row">
                                                            <label for="inputEmail3" class="col-md-3 col-form-label">Position</label>
                                                            <div class="col-md-9">
                                                                <input type="text" class="form-control" name="jobTitle" >
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label for="inputEmail3" class="col-md-3 col-form-label">Item No. (if applicable)</label>
                                                            <div class="col-md-9">
                                                                <input type="text" class="form-control" name="itemNo" >
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label for="inputPassword3" class="col-md-3 col-form-label">Employment Type</label>
                                                            <div class="col-md-9">
                                                                <select class="form-control" name="empType">
                                                                    <option></option>
                                                                    <option>Permanent Position</option>
                                                                    <option>Job Order</option>
                                                                    <option>Contract of Service</option>

                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label for="inputPassword5" class="col-md-3 col-form-label">Department</label>
                                                            <div class="col-md-9">
                                                                <input type="text" class="form-control" name="department" >
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label for="inputPassword5" class="col-md-3 col-form-label">Job Description</label>
                                                            <div class="col-md-9">
                                                                <textarea class="form-control" rows="5" name="description"></textarea>
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label for="inputPassword5" class="col-md-3 col-form-label">No. of Vacancies</label>
                                                            <div class="col-md-9">
                                                                <input type="number" class="form-control" name="qty" >
                                                            </div>
                                                        </div>
                                                       
                                                        <div class="form-group mb-0 justify-content-end row">
                                                            <div class="col-md-9">
                                                                <input type="submit" name="submit" value="Submit" class="btn btn-info waves-effect waves-light">
                                                                <!-- <button type="submit" class="btn btn-info waves-effect waves-light">Submit</button> -->
                                                            </div>
                                                        </div>
                                                    </form>

                                                    </div>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->

                                        <div class="modal fade apply" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="myLargeModalLabel">Submit an Application</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                        
                                                    <?php 
                                                        $attributes = array('class' => 'parsley-examples');
                                                        echo form_open('pages/submit_application/'.$this->session->c_id);
                                                    ?>

                                                         <input type="hidden" name="id" id="job">
                                                        
                                                        
                                                        <div class="form-group row">
                                                            <label for="inputPassword3" class="col-md-3 col-form-label">District</label>
                                                            <div class="col-md-9">
                                                                <select class="form-control" name="district" id="dist" required>
                                                                <option value="">Please Select Your District</option>
                                                                <?php foreach($dist as $row){?>
                                                                <option value="<?= $row->discription; ?>"><?= strtoupper($row->discription); ?></option>
                                                                <?php }?>

                                                                </select>
                                                            </div>
                                                        </div>

                                                        <?php if($settings->sgod_sign_type == 1){?>
                                                            <input type="hidden" name="school" value="202401">
                                                        <?php }else{ ?> 
                                                            <div class="form-group row">
                                                                <label for="inputPassword3" class="col-md-3 col-form-label">School</label>
                                                                <div class="col-md-9">
                                                                    <select class="form-control" name="school" id="school" required>
                                                                        <option value="">Please Select School</option>
                                                                        <?php foreach($school as $row){?>
                                                                            <option data-dist="<?= $row->district; ?>" value="<?= $row->schoolID; ?>"><?= strtoupper($row->schoolName); ?></option>
                                                                        <?php } ?>

                                                                    </select>
                                                                </div>
                                                            </div>
                                                        <?php } ?>

                                                       
                                                        
                                                        
                                                       
                                                        <div class="form-group mb-0 justify-content-end row">
                                                            <div class="col-md-9">
                                                                <input type="submit" name="submit" value="Submit" class="btn btn-info waves-effect waves-light">
                                                                <!-- <button type="submit" class="btn btn-info waves-effect waves-light">Submit</button> -->
                                                            </div>
                                                        </div>
                                                    </form>

                                                    </div>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->

                                        <div class="modal fade applyme" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="myLargeModalLabel">Submit an Application</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                        
                                                    <?php 
                                                        $attributes = array('class' => 'parsley-examples');
                                                        echo form_open('pages/submit_application/'.$this->session->c_id);
                                                    ?>

                                                         <input type="hidden" name="id" id="job">
                                                        
                                                        
                                                        <div class="form-group row">
                                                            <label for="inputPassword3" class="col-md-3 col-form-label">District</label>
                                                            <div class="col-md-9">
                                                                <select class="form-control" name="district" id="distme" required>
                                                                <option value="">Please Select Your District</option>
                                                                <option value="School Division Office">School Division Office</option>

                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label for="inputPassword3" class="col-md-3 col-form-label">School</label>
                                                            <div class="col-md-9">
                                                                <select class="form-control" name="school" id="school" required>
                                                                    <option value="">Please Select School</option>
                                                                    <?php foreach($school as $row){?>
                                                                        <option data-dist="<?= $row->district; ?>" value="<?= $row->schoolID; ?>"><?= strtoupper($row->schoolName); ?></option>
                                                                    <?php } ?>

                                                                </select>
                                                            </div>
                                                        </div>

                                                       
                                                        
                                                        
                                                       
                                                        <div class="form-group mb-0 justify-content-end row">
                                                            <div class="col-md-9">
                                                                <input type="submit" name="submit" value="Submit" class="btn btn-info waves-effect waves-light">
                                                                <!-- <button type="submit" class="btn btn-info waves-effect waves-light">Submit</button> -->
                                                            </div>
                                                        </div>
                                                    </form>

                                                    </div>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->

                                        <div class="modal fade applyedit" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="myLargeModalLabel">Edit my Application</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                        
                                                    <?php 
                                                        $attributes = array('class' => 'parsley-examples');
                                                        echo form_open('pages/edit_application/'.$this->session->c_id);
                                                    ?>

                                                         <input type="hidden" name="id" id="job">
                                                        
                                                        
                                                        <div class="form-group row">
                                                            <label for="inputPassword3" class="col-md-3 col-form-label">District</label>
                                                            <div class="col-md-9">
                                                                <select class="form-control" name="district" id="distedit" required>
                                                                <option value="">Please Select Your District</option>
                                                                <?php foreach($dist as $row){?>
                                                                <option value="<?= $row->discription; ?>"><?= $row->discription; ?></option>
                                                                <?php } ?>

                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label for="inputPassword3" class="col-md-3 col-form-label">School</label>
                                                            <div class="col-md-9">
                                                                <select class="form-control" name="school" id="schooledit" required>
                                                                    <option value="">Please Select School</option>
                                                                    <?php foreach($school as $row){?>
                                                                        <option data-distedit="<?= $row->district; ?>" value="<?= $row->schoolID; ?>"><?= $row->schoolName; ?></option>
                                                                    <?php } ?>

                                                                </select>
                                                            </div>
                                                        </div>

                                                       
                                                        
                                                        
                                                       
                                                        <div class="form-group mb-0 justify-content-end row">
                                                            <div class="col-md-9">
                                                                <input type="submit" name="submit" value="Submit" class="btn btn-info waves-effect waves-light">
                                                                <!-- <button type="submit" class="btn btn-info waves-effect waves-light">Submit</button> -->
                                                            </div>
                                                        </div>
                                                    </form>

                                                    </div>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->

                                    

                                        <div class="modal fade generate" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog modal-smmodal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="myLargeModalLabel">Validated Applications</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                        
                                                    <?php 
                                                        $attributes = array('class' => 'parsley-examples');
                                                       if($this->session->position == "School"){
                                                            echo form_open('pages/school_generate_report/');
                                                       }else{
                                                            echo form_open('pages/district_generate_report/');
                                                       }
                                                                     ?>
                                                    <div class="row">
                                                    
                                                    <div class="col-md-12">
                                                            <label ></label>
                                                            <select class="form-control" name="fy" required>
                                                                <option></option>
                                                            <?php 
                                                            $firstYear = (int)date('Y');
                                                            $lastYear = $firstYear + 5;
                                                            
                                                            for($i=$firstYear;$i<=$lastYear;$i++)
                                                            { 
                                                                echo '<option value='.$i.'>'.$i.'</option>';
                                                            }
                                                            ?>
                                                            </select>


                                                        </div>
                                                        </div><br />
                                                        

                                                       
                                                        
                                                        
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <input type="submit" name="submit" value="Submit" class="btn btn-info waves-effect waves-light">
                                                                <!-- <button type="submit" class="btn btn-info waves-effect waves-light">Submit</button> -->
                                                            </div>
                                                        </div>
                                                    </form>

                                                    </div>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->

                                        <div class="modal fade gs" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog modal-smmodal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="myLargeModalLabel">Generate School List</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                        
                                                    <?php 
                                                        $attributes = array('class' => 'parsley-examples');
                                                         echo form_open('pages/dgr/'); 
                                                    ?>

                                                    <div class="row">
                                                    
                                                    <div class="col-md-12">
                                                            <label ></label>
                                                            <select class="form-control" name="fy" required>
                                                                <option></option>
                                                            <?php 
                                                            $firstYear = date('Y');
                                                            $lastYear = $firstYear + 5;
                                                            
                                                            for($i=$firstYear;$i<=$lastYear;$i++)
                                                            { 
                                                                echo '<option value='.$i.'>'.$i.'</option>';
                                                            }
                                                            ?>
                                                            </select>


                                                        </div>
                                                        </div><br />
                                                        

                                                       
                                                        
                                                        
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <input type="submit" name="submit" value="Submit" class="btn btn-info waves-effect waves-light">
                                                                <!-- <button type="submit" class="btn btn-info waves-effect waves-light">Submit</button> -->
                                                            </div>
                                                        </div>
                                                    </form>

                                                    </div>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->

                                        <!--  Edit Vacancies -->
                                        <div class="modal fade edit_vacancy_file" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" style="display: none;" aria-hidden="true">
                                                    <div class="modal-dialog modal-lg">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="myLargeModalLabel">Edit Job Vacancy</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                            </div>
                                                            <div class="modal-body">
                                                                
                                                            <?php 
                                                                $attributes = array('class' => 'parsley-examples');
                                                                echo form_open_multipart('Pages/jobVacancy_file_update', $attributes);
                                                            ?>
                                                              
                                                                <div class="form-group row">
                                                                    <label for="inputPassword5" class="col-md-3 col-form-label">Attachment</label>
                                                                    <div class="col-md-9">
                                                                        <input type="file" class="form-control" name="file" >
                                                                    </div>
                                                                </div>
                                                                <input type="hidden" name="jobID" id="id">
                                                                


                                                          
                                                            
                                                                <div class="form-group mb-0 justify-content-end row">
                                                                    <div class="col-md-9">
                                                                        <input type="submit" name="submit" value="Submit" class="btn btn-primary waves-effect waves-light">
                                                                        <!-- <button type="submit" class="btn btn-info waves-effect waves-light">Submit</button> -->
                                                                    </div>
                                                                </div>
                                                            </form>

                                                            </div>
                                                        </div>
                                                        <!-- /.modal-content -->
                                                    </div>
                                                    <!-- /.modal-dialog -->
                                            </div>
                                        <!-- /.modal -->

                                        <!--  Search Applicantion -->
                                        <div class="modal fade aa" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" style="display: none;" aria-hidden="true">
                                                    <div class="modal-dialog modal-lg">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="myLargeModalLabel">Search Applicant</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                            </div>
                                                            <div class="modal-body">
                                                                
                                                            <?php 
                                                                $attributes = array('class' => 'parsley-examples');
                                                                echo form_open('Pages/applicant_applications', $attributes);
                                                            ?>
                                                              
                                                                <div class="form-group row">
                                                                    <label for="inputPassword5" class="col-md-3 col-form-label">Record No.</label>
                                                                    <div class="col-md-9">
                                                                        <input type="text" class="form-control" name="record_no" >
                                                                    </div>
                                                                </div>


                                                                <?php  $position = $this->session->position; if($position==='Evaluator'): ?>

                                                                <div class="form-group row">
                                                                    <label for="inputPassword5" class="col-md-3 col-form-label">Year</label>
                                                                    <div class="col-md-9">
                                                                    <select class="form-control" name="fy" required>
                                                                        <option></option>
                                                                    <?php 
                                                                    $firstYear = (int)date('Y');
                                                                    $lastYear = $firstYear + 5;
                                                                    
                                                                    for($i=$firstYear;$i<=$lastYear;$i++)
                                                                    { 
                                                                        echo '<option';
                                                                        if($i == date('Y')){echo " selected ";}
                                                                        echo ' value='.$i.'>'.$i.'</option>';
                                                                    }
                                                                    ?>
                                                                    </select>
                                                                    </div>
                                                                </div>

                                                                <?php endif; ?>
                                                                


                                                          
                                                            
                                                                <div class="form-group mb-0 justify-content-end row">
                                                                    <div class="col-md-9">
                                                                        <input type="submit" name="submit" value="Submit" class="btn btn-primary waves-effect waves-light">
                                                                        <!-- <button type="submit" class="btn btn-info waves-effect waves-light">Submit</button> -->
                                                                    </div>
                                                                </div>
                                                            </form>

                                                            </div>
                                                        </div>
                                                        <!-- /.modal-content -->
                                                    </div>
                                                    <!-- /.modal-dialog -->
                                            </div>
                                        <!-- /.modal -->

                                    <script>
                                        $(document).ready(function() {
                                            $("#schooledit option").hide();

                                            $("#distedit").change(function() {
                                                var val = $(this).val();
                                                $("#schooledit option").hide();
                                                $("#schooledit").val("");
                                                $("#schooledit [data-distedit='" + val + "']").show(); //show options where attribute value matches.
                                                $("#schooledit").change();
                                            });


                                            });
                                    </script>

                                    <script>
                                        $(document).ready(function() {
                                            $("#school option").hide();

                                            $("#dist").change(function() {
                                                var val = $(this).val();
                                                $("#school option").hide();
                                                $("#school").val("");
                                                $("#school [data-dist='" + val + "']").show(); //show options where attribute value matches.
                                                $("#school").change();
                                            });

                                            $("#distme").change(function() {
                                                var val = $(this).val();
                                                $("#school option").hide();
                                                $("#school").val("");
                                                $("#school [data-dist='" + val + "']").show(); //show options where attribute value matches.
                                                $("#school").change();
                                            });


                                            });
                                    </script>

                                    <script type="text/javascript">
                                            $(document).on("click", ".open-AddBookDialog", function () {
                                                var myBookId = $(this).data('id');
                                                $(".modal-body #id").val( myBookId );


                                                });      
                                    </script>

                                    <script>
                                        document.getElementById("position-select").addEventListener("change", function() {
                                            var selectedPosition = this.value;
                                            var jobSelect = document.getElementById("job-title-select");
                                            var teachingGroupType = document.getElementById("teaching-group-type");
                                            var admingroup = document.getElementById("admin-group-type");

                                            // Enable the job title dropdown
                                            jobSelect.disabled = false;
                                            
                                            // Show or hide the "Group Type for Teaching" dropdown based on selected position
                                            if (selectedPosition === "1") { // Teaching Positions
                                                teachingGroupType.style.display = "block"; // Show
                                            } else {
                                                teachingGroupType.style.display = "none"; // Hide
                                            }

                                            // Show or hide the "Administrative Group" dropdown based on selected position
                                            if (selectedPosition === "2") { // Teaching Positions
                                                admingroup.style.display = "block"; // Show
                                            } else {
                                                admingroup.style.display = "none"; // Hide
                                            }
                                            
                                            // Loop through all the job titles and hide/show based on the selected position
                                            var jobOptions = jobSelect.querySelectorAll("option");
                                            jobOptions.forEach(function(option) {
                                                if (option.value && option.getAttribute("data-pos_id") !== selectedPosition) {
                                                    option.style.display = "none"; // Hide options that don't match
                                                } else {
                                                    option.style.display = ""; // Show matching options
                                                }
                                            });
                                            
                                            // If no position is selected, disable the job title dropdown
                                            if (selectedPosition === "") {
                                                jobSelect.disabled = true;
                                                teachingGroupType.style.display = "none"; // Hide group type if no position is selected
                                            }
                                        });


                                        $(document).ready(function(){
                                            // When the select value changes
                                            $('#job-title-select').on('change', function(){
                                                // Get the selected option's data-pos_id
                                                var posId = $(this).find('option:selected').data('position_id');
                                                
                                                // Set the value of the input field to the selected data-pos_id
                                                $('#pos-id-input').val(posId);
                                            });
                                        });
                                    </script>
