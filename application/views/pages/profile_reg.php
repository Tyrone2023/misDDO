<!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->
             <?php $setting = $this->Common->one_cond_row('settings','id',11); ?>

            <div class="content-page">
                <div class="content">

                <div class="row">
                            <div class="col-12">
                                <div class="page-title-box">
                                    
                                <?php
                            $email = $this->session->username;

                            if (strpos($email, '@deped.gov.ph') !== false) { ?>
                                <div class="alert alert-icon alert-danger alert-dismissible fade show" role="alert">
                                                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                        <i class="mdi mdi-block-helper mr-2"></i>
                                                                        <p><strong>Oh snap!</strong> Our system has identified that you are using a DepEd email address. This indicates that you may already be employed by DepEd Davao Oriental. In line with our application guidelines, this makes your job application ineligible for further processing.</p>
                                                                        <p>We kindly encourage you to review the application instructions carefully to ensure compliance and avoid disqualification. If you have any questions or need further assistance, please don’t hesitate to contact us..</p>
                                                                    </div>
                            
                            <?php } ?>

                                </div>
                            </div>
                        </div>
                   
                

                    <!-- Start Content-->
                    <div class="container-fluid">
                          <!-- start page title -->
                          <div class="row">
                            <div class="col-sm-12">
                            <div class="profile-bg-picture" style="background-image:url('<?= base_url(); ?>assets/images/mis.jpg')">
                                    <span class="picture-bg-overlay"></span>
                                    <!-- overlay -->
                                </div>
                                <!-- meta -->
                                <div class="profile-user-box">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="profile-user-img"><img src="<?= base_url(); ?>/uploads/profile/<?php if($user->image != ""){echo $user->image;}else{
                                                            if(isset($user->sex)){if($user->sex == 0){echo "icon/m.jpg";}else{echo "icon/f.jpg";}}
                                                        } ?>" alt="" class="avatar-lg rounded-circle"></div>
                                            <div class="">
                                                <h4 class="mt-5 font-18 ellipsis"><?= $a_user->FirstName.' '.$a_user->MiddleName.' '.$a_user->LastName.' '.strtoupper($a_user->NameExtn); ?></h4>
                                                <p class="font-13"> <?= $a_user->empPosition; ?> </p>
                                                <p class="text-muted mb-0">Applicant No.: <strong><?= strtoupper($a_user->record_no); ?></strong></p>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="text-right">
                                                <a href="<?= base_url(); ?>Pages/profile_reg_edit/<?= $a_user->id; ?>" class="btn btn-success waves-effect waves-light"><i class="mdi mdi-account-settings-variant mr-1"></i> Edit Profile</a>
                                                
                                                <?php if($this->session->userdata('position')==='Admin'):?>
                                                
                                                    <a data-toggle="modal_awards" data-id="<?= $a_user->id; ?>" class="open-addAwards btn btn-info waves-effect width-md waves-light" href="#addEmployment">Deactivate</a>
                                                    <?php elseif($this->session->userdata('position')==='User'):?>
                                                <?php endif ?>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--/ meta -->
                            </div>
                        </div>
                        <!-- end row -->

                        <div class="row mt-4">
                            <div class="col-sm-12">
                                <div class="card p-0">
                                    <div class="card-body p-0">

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

                                        <!-- <ul class=" nav nav-tabs tabs-bordered nav-justified"> -->
                                        <ul class="nav nav-pills navtab-bg nav-justified">
                                            <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#aboutme">About</a> </li>
                                            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#family">Family</a></li>
                                            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#trainings">Trainings</a></li>
                                            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#work">Work Experience</a></li>
                                            <!-- <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#education">Education</a></li>
                                            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#awards">Awards</a></li>
                                            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#employment">Employment</a></li> -->
                                        </ul>

                                        <div class="tab-content m-0 p-4">

                                            <div id="aboutme" class="tab-pane active">
                                                <div class="profile-desk">
                                                    <h5 class="text-uppercase font-weight-bold">Official Information</h5>
                                                    <div class="row">
													<div class="col-sm-4">
                                                    <table class="table table-condensed mb-0">
                                                        
                                                        <tbody>
                                                            <tr>
                                                                <th scope="row">Job Title</th>
                                                                <td><?php if(empty($a_user->jobTitle)){echo "";}else{echo $a_user->jobTitle;} ?></td>
                                                            </tr>

                                                             <tr>
                                                                <th scope="row">Position</th>
                                                                <td><?= $a_user->empPosition; ?></td>
                                                            </tr>
                                                            <tr>
                                                                <th scope="row">Emp. Status</th>
                                                                <td><?= $a_user->empStatus; ?></td>
                                                            </tr>

                                                            <tr>
                                                                <th scope="row">Eligibility</th>
                                                                <td><?= $a_user->csEligibility; ?></td>
                                                            </tr>
                                                            
                                                        </tbody>
                                                    </table>
                                                    </div>

                                                    <div class="col-sm-4">
													<!--<h5 class="mt-4">Contact Person</h5>-->
													<table class="table table-condensed mb-0">
                                                        
                                                            <tbody>
                                                                <tr>
                                                                    <th scope="row">Department</th>
                                                                    <td><?= $a_user->Department; ?></td>
                                                                </tr>

                                                                <tr>
                                                                    <th scope="row">Expected Ret. Year</th>
                                                                    <td><?= $a_user->retYear; ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <th scope="row">TIN</th>
                                                                    <td><?= $a_user->tinNo; ?> </td>
                                                                </tr>
                                                            </tbody>
															
                                                        </table>
														</div>

                                                        <div class="col-sm-4">
													<!--<h5 class="mt-4">Contact Person</h5>-->
													<table class="table table-condensed mb-0">
                                                        
                                                            <tbody>
                                                                <tr>
                                                                    <th scope="row">GSIS BP No.</th>
                                                                    <td><?= $a_user->gsis; ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <th scope="row">PAG-IBIG No.</th>
                                                                    <td><?= $a_user->pagibig; ?></td>
                                                                </tr>

                                                                <tr>
                                                                    <th scope="row">SSS</th>
                                                                    <td><?= $a_user->sssNo; ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <th scope="row">PhilHealth No.</th>
                                                                    <td><?= $a_user->philHealth; ?></td>
                                                                </tr>
                                                            </tbody>
															
                                                        </table>
														</div>
                                                </div>

                                                <!-- End of the row -->
                                                <h5 class="text-uppercase font-weight-bold">Personal Information</h5>
                                                <div class="row">
													    <div class="col-sm-4">
                                                        <table class="table table-condensed mb-0">
                                                        
                                                        <tbody>
                                                            <tr>
                                                                <th scope="row">Gender</th>
                                                                <td><?= $a_user->Sex; ?></td>
                                                            </tr>
                                                            <tr>
                                                                <th scope="row">Birth Date</th>
                                                                <td><?= $a_user->BirthDate; ?></td>
                                                            </tr>

                                                             <tr>
                                                                <th scope="row">Birth Place</th>
                                                                <td><?= $a_user->BirthPlace; ?></td>
                                                            </tr>
                                                            <tr>
                                                                <th scope="row">Age</th>
                                                                <td><?= $a_user->age; ?></td>
                                                            </tr>     
                                                        </tbody>															
                                                        </table>
                                                        </div>

                                                        <div class="col-sm-4">
													<!--<h5 class="mt-4">Contact Person</h5>-->
													<table class="table table-condensed mb-0">
                                                        
                                                            <tbody>
                                                                <tr>
                                                                    <th scope="row">Blood Type</th>
                                                                    <td><?= $a_user->bloodType; ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <th scope="row">Marital Status</th>
                                                                    <td><?= $a_user->MaritalStatus; ?></td>
                                                                </tr>

                                                                <tr>
                                                                    <th scope="row">Citizenship</th>
                                                                    <td><?= $a_user->citizenship; ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <th scope="row">Citizenship Type</th>
                                                                    <td><?= $a_user->citizenshipType; ?></td>
                                                                </tr>
                                                            </tbody>
															
                                                        </table>
														</div>

                                                        <div class="col-sm-4">
													<!--<h5 class="mt-4">Contact Person</h5>-->
													<table class="table table-condensed mb-0">
                                                        
                                                            <tbody>
                                                                <tr>
                                                                    <th scope="row">Dual Citizen?</th>
                                                                    <td><?= $a_user->dualCitizenship; ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <th scope="row">Country</th>
                                                                    <td><?= $a_user->citizenshipCountry; ?></td>
                                                                </tr>

                                                                <tr>
                                                                    <th scope="row">Height</th>
                                                                    <td><?= $a_user->height; ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <th scope="row">Weight</th>
                                                                    <td><?= $a_user->weight; ?></td>
                                                                </tr>
                                                            </tbody>
															
                                                        </table>
														</div>

                                                    </div>
                                                    <!-- End of the row -->

                                                   
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                        <h5 class="text-uppercase font-weight-bold">Contact Information</h5>
                                                            <table class="table table-condensed mb-0">
                                                        
                                                            <tbody>
                                                                <tr>
                                                                    <th scope="row">Contact No.</th>
                                                                    <td><?= $a_user->contactNo; ?> <?= $a_user->empTelNo; ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <th scope="row">Official Email</th>
                                                                    <td><?= $a_user->empEmail; ?></td>
                                                                </tr>

                                                                <tr>
                                                                    <th scope="row">Address</th>
                                                                    <td class="ng-binding">  <?= $a_user->resVillage; ?>, <?= $a_user->resBarangay; ?>, <?= $a_user->resCity; ?>, <?= $a_user->resProvince; ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <th scope="row">Facebook</th>
                                                                    <td><?= $a_user->fb; ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <th scope="row">Skype</th>
                                                                    <td><?= $a_user->skype; ?></td>
                                                                </tr>
                                                                
                                                            </tbody>
                                                            </table>
                                                        </div>

                                                        <div class="col-sm-6">
                                                        <h5 class="text-uppercase font-weight-bold">In Case of Emergency</h5>
                                                            <table class="table table-condensed mb-0">
                                                            
                                                                <tbody>
                                                                    <tr>
                                                                        <th scope="row">Contact Person</th>
                                                                        <td><?= $a_user->contactName; ?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th scope="row">Relationship</th>
                                                                        <td><?= $a_user->contactRel; ?></td>
                                                                    </tr>

                                                                    <tr>
                                                                        <th scope="row">Address</th>
                                                                        <td class="ng-binding"><?= $a_user->contactAddress; ?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th scope="row">Email</th>
                                                                        <td><?= $a_user->contactEmail; ?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th scope="row">Contact No.</th>
                                                                        <td><?= $a_user->empMobile; ?></td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
														</div> 


                                                    </div>




                                                    </div> <!-- end profile-desk -->
                                                </div> 
                                                <!-- end of about-me -->

                                                <!-- Family -->
                                                <div id="family" class="tab-pane">
                                                    <!-- start page title -->
                                                    <div class="row">
                                                            <div class="col-12">
                                                                <div class="page-title-box">
                                                                    <h4 class="page-title">FAMILY MEMBERS </h4>
                                                                    <div class="page-title-right">
                                                                        <ol class="breadcrumb p-0 m-0">
                                                                        <!-- <li><a data-toggle="modal" data-id="<?= $id; ?>" class="open-AddBookDialog btn btn-info waves-effect width-md waves-light" href="#familymodal">Add New</a></li>  -->
                                                                        </ol>
                                                                    </div>
                                                                    <div class="clearfix"></div>
                                                                    <br />
                                                                    <!-- <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;"> -->
                                                                    <table class="table mb-0">
                                                                        <thead>
                                                                                <tr>
                                                                                    <th>Full Name</th>
                                                                                    <th>Relationship</th>
                                                                                    <th>Birth Date</th>
                                                                                    <th style="text-align:center">Manage</th>
                                                                                </tr>
                                                                            </thead>

                                                                            <tbody>
                                                                                <?php foreach($family as $row){  ?>
                                                                                <tr>
                                                                                    <td><?= $row['fullName']; ?></td>
                                                                                    <td><?= $row['relationship']; ?></td>
                                                                                    <td><?= $row['bDate']; ?></td>
                                                                                    <td style="text-align:center">
                                                                                        <a href="<?=base_url(); ?>Pages/delete_family?famID=<?= $row['famID']; ?>&id=<?= $row['IDNumber']; ?>" class="text-danger"><i class="mdi mdi-file-document-box-check-outline"></i>Delete</a>
                                                                                    </td>
                                                                                </tr>
                                                                                <?php } ?>
                                                                            </tbody>
                                                                        </table>

                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- end page title -->


                                                </div>
                                                <!-- End of Family -->

                                                <!-- Education -->
                                                <div id="education" class="tab-pane">
                                                    <!-- start page title -->
                                                    <div class="row">
                                                            <div class="col-12">
                                                                <div class="page-title-box">
                                                                    <h4 class="page-title">EDUCATION</h4>
                                                                    <div class="page-title-right">
                                                                        <ol class="breadcrumb p-0 m-0">
                                                                        <li><a data-toggle="modal" data-id="<?= $user->id; ?>" class="open-AddBookDialog btn btn-info waves-effect width-md waves-light" href="#educationmodal">Add New</a></li> 
                                                                        </ol>
                                                                    </div>
                                                                    <div class="clearfix"></div>
                                                                    <br />
                                                                    <!-- <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;"> -->
                                                                    <table class="table mb-0">
                                                                        <thead>
                                                                                <tr>
                                                                                    <th>Level</th>
                                                                                    <th>School Name</th>
                                                                                    <th>Course</th>
                                                                                    <th>Year Started</th>
                                                                                    <th>Year Finished</th>
                                                                                    <th>Scholarship</th>
                                                                                    <th style="text-align:center">Manage</th>
                                                                                </tr>
                                                                            </thead>

                                                                            <tbody>
                                                                                <?php foreach($educ as $row){  ?>
                                                                                <tr>
                                                                                    <td><?= $row['level']; ?></td>
                                                                                    <td><?= $row['schoolName']; ?></td>
                                                                                    <td><?= $row['course']; ?></td>
                                                                                    <td><?= $row['yearStarted']; ?></td>
                                                                                    <td><?= $row['yearEnded']; ?></td>
                                                                                    <td><?= $row['scholarship']; ?></td>
                                                                                    <td style="text-align:center">
                                                                                         <a href="<?=base_url(); ?>Pages/delete_education?educID=<?= $row['educID']; ?>&id=<?= $row['IDNumber']; ?>" class="text-danger"><i class="mdi mdi-file-document-box-check-outline"></i>Delete</a>
                                                                                    </td>
                                                                                </tr>
                                                                                <?php } ?>
                                                                            </tbody>
                                                                        </table>

                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- end page title -->


                                                </div>
                                                <!-- End of Education -->

                                                <!-- Trainings -->
                                                <div id="trainings" class="tab-pane">
                                                    <!-- start page title -->
                                                    <div class="row">
                                                            <div class="col-12">
                                                                <div class="page-title-box">
                                                                    <h4 class="page-title">TRAININGS AND SEMINARS ATTENDED</h4>
                                                                    <div class="page-title-right">
                                                                        <ol class="breadcrumb p-0 m-0">
                                                                        <?php if($setting->status == 0): ?>
                                                                        <li><a data-toggle="modal" data-id="<?= $a_user->id; ?>" class="open-AddBookDialog btn btn-info waves-effect width-md waves-light" href=".bs-example-modal-lg">Add New</a></li> 
                                                                        <?php endif; ?>
                                                                    </ol>
                                                                    </div>
                                                                    <div class="clearfix"></div>
                                                                    <br />
                                                                    <?php 
                                                                        $canDeleteTrainings = ($setting->status == 0) && !in_array($this->session->position, ['Evaluator','rater','raters']);
                                                                        $canManageTrainings = $canDeleteTrainings;
                                                                        $canRateTrainings = ($this->session->position == 'asds' || $this->session->position == 'raters' || ($this->session->position == 'Evaluator' && !empty($isAssignedEvaluator)));
                                                                    ?>
                                                                    <?php if($canRateTrainings): ?>
                                                                        <p class="text-muted mb-2">Tip: Click the badge under <strong>No. of Hours</strong> to update the value.</p>
                                                                    <?php endif; ?>
                                                                    <!-- <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;"> -->
                                                                   <table class="table mb-0">
                                                                        <thead>
                                                                                <tr>
                                                                                    <th>Training Title</th>
                                                                                    <th>Attachment</th>
                                                                                    <th>No. of Hours</th>
                                                                                    <th class="text-center">Status</th>
                                                                                    <th style="text-align:center"><?= $canDeleteTrainings ? 'Manage' : '&nbsp;' ?></th>
                                                                                </tr>
                                                                            </thead>

                                                                            <tbody>
                                                                                <?php foreach($training as $row){  ?>
                                                                                <tr>
                                                                                    <td><?= $row->trainingTitle; ?></td>
                                                                                    <td><a  href="<?= base_url().'uploads/trainings_staff/'.$row->file; ?>" target="_blank" class="tooltips" data-placement="top" data-toggle="tooltip" data-original-title="View File Attachment"><i  class="fas fa-file-alt btn btn-lg text-primary"></i></a></td>
                                                                                    <td>
                                                                                        <?php if ($canRateTrainings){ ?>
                                                                                            <a data-toggle="modal" data-id="<?= $row->trainingID; ?>" data-appid="<?= $row->noHours; ?>" class="open-AddBookDialog badge badge-primary" href=".ivan"><?= $row->noHours; ?></a>
                                                                                        <?php }else{ ?>
                                                                                            <?php if ($setting->status == 0 || !empty($isAssignedEvaluator)){ ?>
                                                                                                <a data-toggle="modal" data-id="<?= $row->trainingID; ?>" data-appid="<?= $row->noHours; ?>" class="open-AddBookDialog badge badge-primary" href=".ivan"><?= $row->noHours; ?></a>
                                                                                            <?php }else{ ?>
                                                                                                <span class="badge badge-success"><?= $row->noHours; ?></span>
                                                                                            <?php } ?>
                                                                                        <?php } ?>
                                                                                    </td>
                                                                                    <td class="text-center">
                                                                                        <?php if ($canRateTrainings || $this->session->position == 'Evaluator'): ?>
                                                                                            <?php if ($row->stat == 1): ?>
                                                                                                <span class="badge badge-success mr-1">Relevant</span>
                                                                                                <a class="btn btn-sm btn-outline-secondary" href="<?= base_url(); ?>Page/update_cert_training_stat_staff/<?= $row->trainingID; ?>/2">Mark Not Relevant</a>
                                                                                            <?php elseif ($row->stat == 2): ?>
                                                                                                <span class="badge badge-secondary mr-1">Not Relevant</span>
                                                                                                <a class="btn btn-sm btn-outline-success" href="<?= base_url(); ?>Page/update_cert_training_stat_staff/<?= $row->trainingID; ?>/1">Mark Relevant</a>
                                                                                            <?php else: ?>
                                                                                                <span class="badge badge-secondary mr-1">No Action</span>
                                                                                                <a class="btn btn-sm btn-outline-success" href="<?= base_url(); ?>Page/update_cert_training_stat_staff/<?= $row->trainingID; ?>/1">Mark Relevant</a>
                                                                                                <a class="btn btn-sm btn-outline-secondary" href="<?= base_url(); ?>Page/update_cert_training_stat_staff/<?= $row->trainingID; ?>/2">Mark Not Relevant</a>
                                                                                            <?php endif; ?>
                                                                                        <?php else: ?>
                                                                                            <?php if ($row->stat == 0): ?>
                                                                                                <span class="badge badge-secondary">No Action</span>
                                                                                            <?php elseif ($row->stat == 1): ?>
                                                                                                <span class="badge badge-success">Relevant</span>
                                                                                            <?php else: ?>
                                                                                                <span class="badge badge-secondary">Not Relevant</span>
                                                                                            <?php endif; ?>
                                                                                        <?php endif; ?>
                                                                                    </td>
                                                                                    <td style="text-align:center">
                                                                                        <?php if($canDeleteTrainings): ?>
                                                                                            <a onclick="return confirm('Are you sure?')" href="<?= base_url(); ?>Page/training_delete_staff/<?= $row->trainingID; ?>" class="text-danger"><i class="mdi mdi-file-document-box-check-outline"></i>Delete</a> 
                                                                                        <?php endif; ?>
                                                                                    </td>
                                                                                </tr>
                                                                                <?php } ?>
                                                                                <tr>
                                                                                    <th colspan="2">Total</th>
                                                                                    <td><span class="badge badge-purple"><?= $training_sum; ?></span></td>
                                                                                    <td></td>
                                                                                    <td></td>
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>

                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- end page title -->


                                                </div>
                                                <!-- End of Trainings -->

                                                 <!-- Awards -->
                                                <div id="work" class="tab-pane">
                                                        <!-- start page title -->
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="page-title-box">
                                                                    <h4 class="page-title">WORK EXPERIENCE</h4>
                                                                    <div class="page-title-right">
                                                                        <ol class="breadcrumb p-0 m-0">
                                                                        <li>
                                                                            <?php if($setting->status == 0): ?>
                                                                                <a data-toggle="modal" data-id="<?= $user->id; ?>" class="open-AddBookDialog btn btn-info waves-effect width-md waves-light" href=".renrenguapo">Add New</a>
                                                                            <?php endif; ?>
                                                                        </li> 
                                                                        </ol>
                                                                    </div>
                                                                    <div class="clearfix"></div>
                                                                    <br />
                                                                    <?php 
                                                                        $canRateExperience = ($this->session->position == 'asds' || $this->session->position == 'raters' || ($this->session->position == 'Evaluator' && !empty($isAssignedEvaluator)));
                                                                    ?>
                                                                    <!-- <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;"> -->
                                                                    <table class="table mb-0">
                                                                        <thead>
                                                                                <tr>
                                                                                    <th>Company Name</th>
                                                                                    <th>Attachment</th>
                                                                                    <th>Year</th>
                                                                                    <th>Month</th>
                                                                                    <th>Status</th>
                                                                                    <th style="text-align:center">Manage</th>
                                                                                </tr>
                                                                            </thead>

                                                                            <tbody>
                                                                                <?php foreach($experience as $row){  ?>
                                                                                <tr>
                                                                                    <td><?= $row->title; ?></td>
                                                                                    <td><a  href="<?= base_url().'uploads/experience/'.$row->file; ?>" target="_blank" class="tooltips" data-placement="top" data-toggle="tooltip" data-original-title="View File Attachment"><i  class="fas fa-file-alt btn btn-lg text-primary"></i></a></td>
                                                                                    <td>
                                                                                        <?php if ($this->session->position == 'asds' || $this->session->position == 'raters' || $this->session->position == 'Evaluator'){ ?>
                                                                                        <a data-toggle="modal" data-id="<?= $row->id; ?>" data-appid="<?= $row->ny; ?>" class="open-AddBookDialog badge badge-success" href=".ivy"><?= $row->ny; ?></a>
                                                                                        <?php }else{ ?>
                                                                                            <?php if ($setting->status == 0){ ?>
                                                                                                <a data-toggle="modal" data-id="<?= $row->id; ?>" data-appid="<?= $row->ny; ?>" class="open-AddBookDialog badge badge-success" href=".ivy"><?= $row->ny; ?></a>
                                                                                            <?php }else{ ?>
                                                                                                <span class="badge badge-success"><?= $row->ny; ?></span>
                                                                                            <?php } ?>
                                                                                        <?php } ?>
                                                                                    </td>
                                                                                    <td>
                                                                                        <?php if ($this->session->position == 'asds' || $this->session->position == 'raters' || $this->session->position == 'Evaluator'){ ?>
                                                                                            <a data-toggle="modal" data-id="<?= $row->id; ?>" data-appid="<?= $row->nm; ?>" class="open-AddBookDialog badge badge-success" href=".ic"><?= $row->nm; ?></a>
                                                                                        <?php }else{ ?>
                                                                                            <?php if ($setting->status == 0){ ?>
                                                                                                <a data-toggle="modal" data-id="<?= $row->id; ?>" data-appid="<?= $row->nm; ?>" class="open-AddBookDialog badge badge-success" href=".ic"><?= $row->nm; ?></a>
                                                                                            <?php }else{ ?>
                                                                                                <span class="badge badge-success"><?= $row->nm; ?></span>
                                                                                            <?php } ?>
                                                                                        <?php } ?>
                                                                                    </td>
                                                                                    <td class="text-center">
                                                                                        <?php if ($canRateExperience || $this->session->position == 'Evaluator'): ?>
                                                                                            <?php if ($row->stat == 1): ?>
                                                                                                <span class="badge badge-success mr-1">Relevant</span>
                                                                                                <a class="btn btn-sm btn-outline-secondary" href="<?= base_url(); ?>Page/update_cert_xp_stat/<?= $row->id; ?>/2">Mark Not Relevant</a>
                                                                                            <?php elseif ($row->stat == 2): ?>
                                                                                                <span class="badge badge-secondary mr-1">Not Relevant</span>
                                                                                                <a class="btn btn-sm btn-outline-success" href="<?= base_url(); ?>Page/update_cert_xp_stat/<?= $row->id; ?>/1">Mark Relevant</a>
                                                                                            <?php else: ?>
                                                                                                <span class="badge badge-secondary mr-1">No Action</span>
                                                                                                <a class="btn btn-sm btn-outline-success" href="<?= base_url(); ?>Page/update_cert_xp_stat/<?= $row->id; ?>/1">Mark Relevant</a>
                                                                                                <a class="btn btn-sm btn-outline-secondary" href="<?= base_url(); ?>Page/update_cert_xp_stat/<?= $row->id; ?>/2">Mark Not Relevant</a>
                                                                                            <?php endif; ?>
                                                                                        <?php else: ?>
                                                                                            <?php if ($row->stat == 0): ?>
                                                                                                <span class="badge badge-secondary">No Action</span>
                                                                                            <?php elseif ($row->stat == 1): ?>
                                                                                                <span class="badge badge-success">Relevant</span>
                                                                                            <?php else: ?>
                                                                                                <span class="badge badge-secondary">Not Relevant</span>
                                                                                            <?php endif; ?>
                                                                                        <?php endif; ?>
                                                                                    </td>
                                                                                    <td style="text-align:center">
                                                                                        <?php if($setting->status == 0): ?>
                                                                                        <a onclick="return confirm('Are you sure?')" href="<?= base_url(); ?>Page/experience_delete/<?= $row->id; ?>" class="text-danger"><i class="mdi mdi-file-document-box-check-outline"></i>Delete</a> 
                                                                                        <?php endif; ?>
                                                                                    </td>
                                                                                </tr>
                                                                                <?php } ?>
                                                                                <tr>
                                                                                    <th colspan="2">Total</th>
                                                                                    <td><span class="badge badge-purple"><?= $ex_year_sum  = (int)($ex_year_sum  ?? 0); ?></span></td>
                                                                                    <td><span class="badge badge-purple"><?= $ex_month_sum = (int)($ex_month_sum ?? 0) ?></span></td>
                                                                                    <td colspan="2">
                                                                                        <span class="badge badge-purple"><?= ($ex_year_sum + intdiv($ex_month_sum,12)) . " years and " . ($ex_month_sum % 12) . " months"; ?></span>
                                                                                    </td>
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>

                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- end page title -->



                                                        
                                                        
                                                </div>
                                                <!-- End of Awards -->


                                                <!-- 201 Files -->
                                                <div id="files" class="tab-pane">
                                                    <!-- <h5 class="text-uppercase font-weight-bold">201 Files</h5> -->
                                                             <!-- start page title -->
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="page-title-box">
                                                                    <h4 class="page-title">201 FILES</h4>
                                                                    <div class="page-title-right">
                                                                        <ol class="breadcrumb p-0 m-0">
                                                                                <!-- <li><a data-toggle="modal_awards" data-id="<?= $id; ?>" class="open-AddBookDialog btn btn-info waves-effect width-md waves-light" href="#addAwards">Add New</a></li> -->
                                                                                <li><a data-toggle="modal" data-id="<?= $user->id; ?>" class="open-AddBookDialog btn btn-info waves-effect width-md waves-light" href="#addBookDialog">Add New</a></li>
                                                                        </ol>
                                                                    </div>
                                                                    <div class="clearfix"></div>
                                                                    <br />
                                                                    <table class="table mb-0">
                                                                        <thead>
                                                                                <tr>
                                                                                    <th>Document Name</th>
                                                                                    <th>Date Uploaded</th>
                                                                                    <th style="text-align:center">Manage</th>
                                                                                </tr>
                                                                            </thead>

                                                                            <tbody>
                                                                                <?php foreach($files as $row){  ?>
                                                                                <tr>
                                                                                    <td><?= $row['docName']; ?></td>
                                                                                    <td><?= $row['dateUploaded']; ?></td>
                                                                                    <td style="text-align:center">
                                                                                         <!-- <a href="#/<?= $row['IDNumber']; ?>" class="text-success"><i class="mdi mdi-file-document-box-check-outline"></i>View</a>&nbsp;&nbsp;&nbsp;&nbsp; -->
                                                                                         <a href="<?= base_url(); ?>uploads/201files/<?= $row['fileName']; ?>" target="_blank" class="text-success"><i class="mdi mdi-file-document-box-check-outline"></i>View</a>&nbsp;&nbsp;&nbsp;&nbsp;
                                                                                         <a href="<?=base_url(); ?>Pages/del_201/<?= $row['id']; ?>" class="text-danger"><i class="mdi mdi-file-document-box-check-outline"></i>Delete</a> 
                                                                                    </td>
                                                                                </tr>
                                                                                <?php } ?>
                                                                            </tbody>
                                                                        </table>

                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- end page title -->

                                                </div>
                                                <!-- End of 201 Files -->


                                                <!-- Employment -->
                                                <div id="employment" class="tab-pane">
                                                    <!-- <h5 class="text-uppercase font-weight-bold">201 Files</h5> -->
                                                             <!-- start page title -->
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="page-title-box">
                                                                    <h4 class="page-title">EMPLOYMENT HISTORY</h4>
                                                                    <div class="page-title-right">
                                                                        <ol class="breadcrumb p-0 m-0">
                                                                                
                                                                                <li><a data-toggle="modal" data-id="<?= $user->id; ?>" class="open-AddBookDialog btn btn-info waves-effect width-md waves-light" href="#employmentmodal">Add New</a></li>
                                                                        </ol>
                                                                    </div>
                                                                    <div class="clearfix"></div>
                                                                    <br />
                                                                    <table class="table mb-0">
                                                                        <thead>
                                                                                <tr>
                                                                                    <th>Position</th>
                                                                                    <th>SG</th>
                                                                                    <th>Step</th>
                                                                                    <th>Item No.</th>
                                                                                    <th>Salary</th>
                                                                                    <th>Station</th>
                                                                                    <th>Status</th>
                                                                                    <th>From</th>
                                                                                    <th>To</th>
                                                                                    <th style="text-align:center">Manage</th>
                                                                                </tr>
                                                                            </thead>

                                                                            <tbody>
                                                                                <?php foreach($employment as $row){  ?>
                                                                                <tr>
                                                                                    <td><?= $row['empPosition']; ?></td>
                                                                                    <td><?= $row['sgNo']; ?></td>
                                                                                    <td><?= $row['stepInc']; ?></td>
                                                                                    <td><?= $row['itemNo']; ?></td>
                                                                                    <td><?= $row['salary']; ?></td>
                                                                                    <td><?= $row['empStation']; ?></td>
                                                                                    <td><?= $row['empStatus']; ?></td>
                                                                                    <td><?= $row['appointDate']; ?></td>
                                                                                    <td><?= $row['endDate']; ?></td>
                                                                                    <td style="text-align:center">
                                                                                         <!-- <a href="#/<?= $row['IDNumber']; ?>" class="text-success"><i class="mdi mdi-file-document-box-check-outline"></i>View</a>&nbsp;&nbsp;&nbsp;&nbsp; -->
                                                                                         <!-- <a href="#/<?= $row['IDNumber']; ?>" target="_blank" class="text-success"><i class="mdi mdi-file-document-box-check-outline"></i>View</a>&nbsp;&nbsp;&nbsp;&nbsp; -->
                                                                                         <a href="<?=base_url(); ?>Pages/delete_employment?empID=<?= $row['empID']; ?>&id=<?= $row['IDNumber']; ?>" class="text-danger"><i class="mdi mdi-file-document-box-check-outline"></i>Delete</a> 
                                                                                    </td>
                                                                                </tr>
                                                                                <?php } ?>
                                                                            </tbody>
                                                                        </table>

                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- end page title -->

                                                </div>
                                                <!-- End of Employment -->


                                                


                                            </div>

                                        </div> 
                                    </div>
                                </div>
                            <!-- end page title -->

                        </div>
                        <!-- end row -->

                    </div>
                    <!-- end container-fluid -->

                </div>
                <!-- end content -->

                                        <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="myLargeModalLabel">TRAININGS AND SEMINARS ATTENDED</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                        
                                                   <?php 
                                                        $attributes = array('class' => 'parsley-examples');
                                                        echo form_open_multipart('Pages/trainings', $attributes);
                                                    ?>

                                                    <div class="form-group row">
                                                            <label for="inputPassword5" class="col-md-3 col-form-label">Training Title</label>
                                                            <div class="col-lg-9">
                                                                <textarea name="trainingTitle" class="form-control" rows="5" id=""></textarea>
                                                            </div>
                                                        </div>
                                                   
                                                

                                                   <div class="form-group row">
                                                        <label for="inputPassword5" class="col-md-3 col-form-label">Date Started</label>
                                                        <div class="col-lg-9">
                                                            <input type="date" value="<?= set_value('dateStarted'); ?>" name="dateStarted" class="form-control">
                                                        </div>
                                                   </div>

                                                   

                                                   <div class="form-group row">
                                                        <label for="inputPassword5" class="col-md-3 col-form-label">Date Finished</label>
                                                        <div class="col-lg-9">
                                                            <input type="date" value="<?= set_value('dateFinished'); ?>" name="dateFinished" class="form-control">
                                                        </div>
                                                   </div>
                                                   
                                                   <div class="form-group row">
                                                        <label for="inputPassword5" class="col-md-3 col-form-label">Conducted By</label>
                                                        <div class="col-lg-9">
                                                            <input type="text" value="<?= set_value('sponsor'); ?>" name="sponsor" class="form-control">
                                                        </div>
                                                   </div>

                                                        
                                                        <div class="form-group row">
                                                            <label for="inputPassword5" class="col-md-3 col-form-label">Attachment</label>
                                                            <div class="col-md-9">
                                                                <input type="file" class="form-control" name="file" required>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="form-group row">
                                                            <label for="inputPassword5" class="col-md-3 col-form-label">No. of hours</label>
                                                            <div class="col-lg-9">
                                                                <input name="noHours" type="text" maxlength="11" oninput="this.value=this.value.replace(/[^0-9]/g,'')" name="assign" class="form-control" value="" required>
                                                            </div>
                                                        </div>

                                                   
                                                   <div class="modal-body">
                                                       <input type="hidden" name="id" id="id" value="">
                                                   </div>    

                                               </div>
                                               <div class="modal-footer">
                                               <button type="submit" name="awards" class="btn btn-primary waves-effect waves-light">Save</button>
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


                                        <div class="modal renrenguapo" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="myLargeModalLabel">WORK EXPERIENCE</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                        
                                                    <?php 
                                                        $attributes = array('class' => 'parsley-examples');
                                                        echo form_open_multipart('Page/insert_experience', $attributes);
                                                    ?>

                                                    <input type="hidden" value="<?= $this->uri->segment(2); ?>" name="id_number">

                                                        <div class="form-group row">
                                                            <label for="inputPassword5" class="col-md-3 col-form-label">Company Name</label>
                                                            <div class="col-lg-9">
                                                                <textarea name="title" class="form-control" rows="5" id=""></textarea>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="form-group row">
                                                            <label for="inputPassword5" class="col-md-3 col-form-label">Attachment</label>
                                                            <div class="col-md-9">
                                                                <input type="file" class="form-control" name="file" required>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="form-group row">
                                                            <label for="inputPassword5" class="col-md-3 col-form-label">No. of Year</label>
                                                            <div class="col-lg-3">
                                                                <input name="ny" type="text" maxlength="11" oninput="this.value=this.value.replace(/[^0-9]/g,'')" class="form-control" value="" required>
                                                            </div>
                                                            <label for="inputPassword5" class="col-md-2 col-form-label">No. of Month</label>
                                                            <div class="col-lg-4">
                                                                <input name="nm" type="text" maxlength="11" oninput="this.value=this.value.replace(/[^0-9]/g,'')" class="form-control" value="" required>
                                                            </div>
                                                        </div>
                                                       
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

                                        <div class="modal ivy" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="myLargeModalLabel">UPDATE YEAR</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                        
                                                    <?php 
                                                        $attributes = array('class' => 'parsley-examples');
                                                        echo form_open('Page/update_year_experience', $attributes);
                                                    ?>

                                                    <input type="hidden" value="<?= $this->uri->segment(2); ?>" name="id_number">

                                                        <input type="hidden" name="id" value="" id="id">
                                                        <div class="form-group row">
                                                            <label for="inputPassword5" class="col-md-5 col-form-label">Year</label>
                                                            <div class="col-lg-7">
                                                                <input name="ny" type="text" id="appid" maxlength="11" oninput="this.value=this.value.replace(/[^0-9]/g,'')" name="assign" class="form-control" value="" required>
                                                            </div>
                                                        </div>
                                                       
                                                        <div class="form-group mb-0 justify-content-end row">
                                                            <div class="col-md-7">
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


                                        <div class="modal ic" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="myLargeModalLabel">UPDATE MONTH</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                        
                                                    <?php 
                                                        $attributes = array('class' => 'parsley-examples');
                                                        echo form_open('Page/update_experience', $attributes);
                                                    ?>

                                                    <input type="hidden" value="<?= $this->uri->segment(2); ?>" name="id_number">

                                                        <input type="hidden" name="id" value="" id="id">
                                                        <div class="form-group row">
                                                            <label for="inputPassword5" class="col-md-5 col-form-label">Month</label>
                                                            <div class="col-lg-7">
                                                                <input name="nm" type="text" id="appid" maxlength="11" oninput="this.value=this.value.replace(/[^0-9]/g,'')" name="assign" class="form-control" value="" required>
                                                            </div>
                                                        </div>
                                                       
                                                        <div class="form-group mb-0 justify-content-end row">
                                                            <div class="col-md-7">
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

                                        <div class="modal ivan" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="myLargeModalLabel">UPDATE</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                        
                                                    <?php 
                                                        $attributes = array('class' => 'parsley-examples');
                                                        echo form_open('Page/update_trainings_staff', $attributes);
                                                    ?>

                                                    <input type="hidden" value="<?= $this->uri->segment(2); ?>" name="id_number">

                                                        <input type="hidden" name="id" value="" id="id">
                                                        <div class="form-group row">
                                                            <label class="col-md-5 col-form-label">No. of Hours</label>
                                                            <div class="col-lg-7">
                                                                <input name="nh" type="text" id="appid" maxlength="11" oninput="this.value=this.value.replace(/[^0-9]/g,'')" class="form-control" value="" required>
                                                                <small class="form-text text-muted">Enter total hours for this training.</small>
                                                            </div>
                                                        </div>
                                                       
                                                        <div class="form-group mb-0 justify-content-end row">
                                                            <div class="col-md-7">
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
                                        document.addEventListener('DOMContentLoaded', function () {
                                        if (window.location.hash) {
                                            const link = document.querySelector('.nav a[href="' + window.location.hash + '"]');
                                            if (link) $(link).tab('show'); // bootstrap tab
                                        }
                                        });
                                        </script>
