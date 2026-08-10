<!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->
             <?php $setting = $this->Common->one_cond_row('settings','id',11); ?>

            <style>
                .profile-record-table th,
                .profile-record-table td { vertical-align: middle; }
                .profile-record-table thead th {
                    text-transform: uppercase;
                    font-size: .72rem;
                    letter-spacing: .04em;
                    border-top: 0;
                    white-space: nowrap;
                }
                .profile-record-table tfoot th { border-top: 2px solid #dee2e6; }
                .profile-record-table .badge { font-size: .78rem; padding: .35em .6em; }
                /* Editable badges should read as controls, not as static labels. */
                .profile-record-table a.badge { cursor: pointer; }
                .profile-record-table a.badge:hover { filter: brightness(.9); }
                .profile-record-table .btn-lg { padding: 0; line-height: 1; }
            </style>

            <div class="content-page">
                <div class="content">



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
                                                                        <!-- <li><a data-toggle="modal" data-id="" class="open-AddBookDialog btn btn-info waves-effect width-md waves-light" href="#familymodal">Add New</a></li>  -->
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
                                                                    <div class="table-responsive">
                                                                   <table class="table table-hover align-middle profile-record-table mb-0">
                                                                        <thead class="thead-light">
                                                                                <tr>
                                                                                    <th>Training Title</th>
                                                                                    <th class="text-nowrap">From</th>
                                                                                    <th class="text-nowrap">To</th>
                                                                                    <th class="text-center">Attachment</th>
                                                                                    <th class="text-center text-nowrap">No. of Hours</th>
                                                                                    <th class="text-center">Status</th>
                                                                                    <th class="text-center"><?= $canDeleteTrainings ? 'Manage' : '&nbsp;' ?></th>
                                                                                </tr>
                                                                            </thead>

                                                                            <tbody>
                                                                                <?php foreach($training as $row){
                                                                                    // Rows added before the dates were captured store 0000-00-00.
                                                                                    $tStart = (!empty($row->dateStarted) && $row->dateStarted !== '0000-00-00') ? strtotime($row->dateStarted) : false;
                                                                                    $tEnd   = (!empty($row->dateFinished) && $row->dateFinished !== '0000-00-00') ? strtotime($row->dateFinished) : false;
                                                                                ?>
                                                                                <tr>
                                                                                    <td><?= html_escape($row->trainingTitle); ?></td>
                                                                                    <td class="text-nowrap"><?= $tStart ? date('M d, Y', $tStart) : '<span class="text-muted">&mdash;</span>'; ?></td>
                                                                                    <td class="text-nowrap"><?= $tEnd ? date('M d, Y', $tEnd) : '<span class="text-muted">&mdash;</span>'; ?></td>
                                                                                    <td class="text-center"><a  href="<?= base_url().'uploads/trainings_staff/'.$row->file; ?>" target="_blank" class="tooltips" data-placement="top" data-toggle="tooltip" data-original-title="View File Attachment"><i  class="fas fa-file-alt btn btn-lg text-primary"></i></a></td>
                                                                                    <td class="text-center">
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
                                                                                    <td class="text-center">
                                                                                        <?php if($canDeleteTrainings): ?>
                                                                                            <a onclick="return confirm('Are you sure?')" href="<?= base_url(); ?>Page/training_delete_staff/<?= $row->trainingID; ?>" class="btn btn-sm btn-outline-danger"><i class="mdi mdi-trash-can-outline"></i> Delete</a>
                                                                                        <?php endif; ?>
                                                                                    </td>
                                                                                </tr>
                                                                                <?php } ?>
                                                                                <?php if(empty($training)){ ?>
                                                                                <tr>
                                                                                    <td colspan="7" class="text-center text-muted py-4">No trainings or seminars recorded yet.</td>
                                                                                </tr>
                                                                                <?php } ?>
                                                                            </tbody>
                                                                            <tfoot>
                                                                                <tr class="bg-light">
                                                                                    <th colspan="4" class="text-right">Total Relevant Hours</th>
                                                                                    <th class="text-center"><span class="badge badge-purple"><?= (float) ($training_sum ?? 0); ?></span></th>
                                                                                    <th colspan="2"></th>
                                                                                </tr>
                                                                            </tfoot>
                                                                        </table>
                                                                    </div>

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
                                                                        $canEditExperienceDates = ($this->session->position == 'asds' || $this->session->position == 'raters' || $this->session->position == 'Evaluator' || $setting->status == 0);
                                                                    ?>
                                                                    <?php if($canEditExperienceDates): ?>
                                                                        <p class="text-muted mb-2">Tip: Click the <strong>inclusive dates</strong> of a row to correct them. The length of service is computed from the range.</p>
                                                                    <?php endif; ?>
                                                                    <div class="table-responsive">
                                                                    <table class="table table-hover align-middle profile-record-table mb-0">
                                                                        <thead class="thead-light">
                                                                                <tr>
                                                                                    <th>Company Name</th>
                                                                                    <th class="text-nowrap">From</th>
                                                                                    <th class="text-nowrap">To</th>
                                                                                    <th class="text-center text-nowrap">Length of Service</th>
                                                                                    <th class="text-center">Attachment</th>
                                                                                    <th class="text-center">Status</th>
                                                                                    <th class="text-center">Manage</th>
                                                                                </tr>
                                                                            </thead>

                                                                            <tbody>
                                                                                <?php foreach($experience as $row){
                                                                                    $xpFrom = !empty($row->date_from ?? null) && $row->date_from !== '0000-00-00' ? $row->date_from : null;
                                                                                    $xpTo   = !empty($row->date_to   ?? null) && $row->date_to   !== '0000-00-00' ? $row->date_to   : null;
                                                                                    $xpYears  = (int) $row->ny;
                                                                                    $xpMonths = (int) $row->nm;
                                                                                ?>
                                                                                <tr>
                                                                                    <td><?= html_escape($row->title); ?></td>
                                                                                    <?php
                                                                                        // One control spans both date cells: clicking either opens the
                                                                                        // same "inclusive dates" modal pre-filled with the stored range.
                                                                                        $dateAttrs = 'data-id="' . (int) $row->id . '"'
                                                                                            . ' data-from="' . html_escape((string) $xpFrom) . '"'
                                                                                            . ' data-to="' . html_escape((string) $xpTo) . '"'
                                                                                            . ' data-title="' . html_escape((string) $row->title) . '"';
                                                                                    ?>
                                                                                    <td class="text-nowrap">
                                                                                        <?php if($canEditExperienceDates){ ?>
                                                                                            <a data-toggle="modal" <?= $dateAttrs; ?> class="open-xp-dates badge badge-<?= $xpFrom ? 'success' : 'warning'; ?>" href=".ivy"><?= $xpFrom ? date('M d, Y', strtotime($xpFrom)) : 'Set date'; ?></a>
                                                                                        <?php }else{ ?>
                                                                                            <?= $xpFrom ? '<span class="badge badge-success">' . date('M d, Y', strtotime($xpFrom)) . '</span>' : '<span class="text-muted">&mdash;</span>'; ?>
                                                                                        <?php } ?>
                                                                                    </td>
                                                                                    <td class="text-nowrap">
                                                                                        <?php if($canEditExperienceDates){ ?>
                                                                                            <a data-toggle="modal" <?= $dateAttrs; ?> class="open-xp-dates badge badge-<?= $xpTo ? 'success' : 'warning'; ?>" href=".ivy"><?= $xpTo ? date('M d, Y', strtotime($xpTo)) : 'Set date'; ?></a>
                                                                                        <?php }else{ ?>
                                                                                            <?= $xpTo ? '<span class="badge badge-success">' . date('M d, Y', strtotime($xpTo)) . '</span>' : '<span class="text-muted">&mdash;</span>'; ?>
                                                                                        <?php } ?>
                                                                                    </td>
                                                                                    <td class="text-center text-nowrap">
                                                                                        <span class="badge badge-info"><?= $xpYears; ?> yr <?= $xpMonths; ?> mo</span>
                                                                                    </td>
                                                                                    <td class="text-center"><a  href="<?= base_url().'uploads/experience/'.$row->file; ?>" target="_blank" class="tooltips" data-placement="top" data-toggle="tooltip" data-original-title="View File Attachment"><i  class="fas fa-file-alt btn btn-lg text-primary"></i></a></td>
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
                                                                                    <td class="text-center">
                                                                                        <?php if($setting->status == 0): ?>
                                                                                        <a onclick="return confirm('Are you sure?')" href="<?= base_url(); ?>Page/experience_delete/<?= $row->id; ?>" class="btn btn-sm btn-outline-danger"><i class="mdi mdi-trash-can-outline"></i> Delete</a>
                                                                                        <?php endif; ?>
                                                                                    </td>
                                                                                </tr>
                                                                                <?php } ?>
                                                                                <?php if(empty($experience)){ ?>
                                                                                <tr>
                                                                                    <td colspan="7" class="text-center text-muted py-4">No work experience recorded yet.</td>
                                                                                </tr>
                                                                                <?php } ?>
                                                                            </tbody>
                                                                            <tfoot>
                                                                                <?php
                                                                                    $ex_year_sum  = (int) ($ex_year_sum  ?? 0);
                                                                                    $ex_month_sum = (int) ($ex_month_sum ?? 0);
                                                                                ?>
                                                                                <tr class="bg-light">
                                                                                    <th colspan="3" class="text-right">Total Relevant Experience</th>
                                                                                    <th class="text-center text-nowrap">
                                                                                        <span class="badge badge-purple"><?= ($ex_year_sum + intdiv($ex_month_sum, 12)); ?> yr <?= ($ex_month_sum % 12); ?> mo</span>
                                                                                    </th>
                                                                                    <th colspan="3"></th>
                                                                                </tr>
                                                                            </tfoot>
                                                                        </table>
                                                                    </div>

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
                                                                                <!-- <li><a data-toggle="modal_awards" data-id="" class="open-AddBookDialog btn btn-info waves-effect width-md waves-light" href="#addAwards">Add New</a></li> -->
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

                                        <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="addTrainingLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                                <div class="modal-content">
                                                    <?php echo form_open_multipart('Pages/trainings', array('class' => 'parsley-examples')); ?>
                                                        <div class="modal-header bg-info text-white">
                                                            <h5 class="modal-title" id="addTrainingLabel"><i class="mdi mdi-school-outline mr-1"></i> Add Training / Seminar</h5>
                                                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                        </div>

                                                        <div class="modal-body">
                                                            <!-- The shared footer handler fills .modal-body #id from data-id. -->
                                                            <input type="hidden" name="id" id="id" value="">

                                                            <div class="form-group">
                                                                <label class="font-weight-semibold">Training Title <span class="text-danger">*</span></label>
                                                                <textarea name="trainingTitle" class="form-control" rows="3" placeholder="e.g. Regional Training on Learning Recovery" required></textarea>
                                                            </div>

                                                            <div class="form-row">
                                                                <div class="form-group col-md-6">
                                                                    <label class="font-weight-semibold">Inclusive Date &mdash; From <span class="text-danger">*</span></label>
                                                                    <input type="date" value="<?= set_value('dateStarted'); ?>" name="dateStarted" class="form-control js-range-from" data-range="training" required>
                                                                </div>
                                                                <div class="form-group col-md-6">
                                                                    <label class="font-weight-semibold">Inclusive Date &mdash; To <span class="text-danger">*</span></label>
                                                                    <input type="date" value="<?= set_value('dateFinished'); ?>" name="dateFinished" class="form-control js-range-to" data-range="training" required>
                                                                </div>
                                                            </div>

                                                            <div class="form-row">
                                                                <div class="form-group col-md-8">
                                                                    <label class="font-weight-semibold">Conducted By</label>
                                                                    <input type="text" value="<?= set_value('sponsor'); ?>" name="sponsor" class="form-control" placeholder="Sponsoring office or organization">
                                                                </div>
                                                                <div class="form-group col-md-4">
                                                                    <label class="font-weight-semibold">No. of Hours <span class="text-danger">*</span></label>
                                                                    <input name="noHours" type="text" maxlength="11" oninput="this.value=this.value.replace(/[^0-9]/g,'')" class="form-control" value="" required>
                                                                </div>
                                                            </div>

                                                            <div class="form-group mb-0">
                                                                <label class="font-weight-semibold">Attachment <span class="text-danger">*</span></label>
                                                                <input type="file" class="form-control" name="file" accept="application/pdf" required>
                                                                <small class="form-text text-muted">PDF file only.</small>
                                                            </div>
                                                        </div>

                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                                                            <button type="submit" name="awards" class="btn btn-primary waves-effect waves-light">Save Training</button>
                                                        </div>
                                                    </form>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->


                                        <div class="modal fade renrenguapo" tabindex="-1" role="dialog" aria-labelledby="addExperienceLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                                <div class="modal-content">
                                                    <?php echo form_open_multipart('Page/insert_experience', array('class' => 'parsley-examples')); ?>
                                                        <div class="modal-header bg-info text-white">
                                                            <h5 class="modal-title" id="addExperienceLabel"><i class="mdi mdi-briefcase-outline mr-1"></i> Add Work Experience</h5>
                                                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                        </div>

                                                        <div class="modal-body">
                                                            <input type="hidden" value="<?= $this->uri->segment(2); ?>" name="id_number">

                                                            <div class="form-group">
                                                                <label class="font-weight-semibold">Company / Office Name <span class="text-danger">*</span></label>
                                                                <textarea name="title" class="form-control" rows="3" placeholder="e.g. Department of Education - Davao Oriental" required></textarea>
                                                            </div>

                                                            <div class="form-row">
                                                                <div class="form-group col-md-6">
                                                                    <label class="font-weight-semibold">Inclusive Date &mdash; From <span class="text-danger">*</span></label>
                                                                    <input name="date_from" type="date" class="form-control js-range-from js-xp-from" data-range="newxp" value="<?= set_value('date_from'); ?>" required>
                                                                </div>
                                                                <div class="form-group col-md-6">
                                                                    <label class="font-weight-semibold">Inclusive Date &mdash; To <span class="text-danger">*</span></label>
                                                                    <input name="date_to" type="date" class="form-control js-range-to js-xp-to" data-range="newxp" value="<?= set_value('date_to'); ?>" required>
                                                                </div>
                                                            </div>

                                                            <div class="alert alert-secondary py-2 mb-3">
                                                                <i class="mdi mdi-calendar-clock mr-1"></i>
                                                                Length of service: <strong class="js-xp-preview" data-for="newxp">&mdash;</strong>
                                                            </div>

                                                            <div class="form-group mb-0">
                                                                <label class="font-weight-semibold">Attachment <span class="text-danger">*</span></label>
                                                                <input type="file" class="form-control" name="file" accept="application/pdf" required>
                                                                <small class="form-text text-muted">Service record or certificate of employment, PDF file only.</small>
                                                            </div>
                                                        </div>

                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                                                            <button type="submit" name="submit" class="btn btn-primary waves-effect waves-light">Save Experience</button>
                                                        </div>
                                                    </form>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->

                                        <div class="modal fade ivy" tabindex="-1" role="dialog" aria-labelledby="xpDatesLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <?php echo form_open('Page/update_experience_dates', array('class' => 'parsley-examples')); ?>
                                                        <div class="modal-header bg-info text-white">
                                                            <h5 class="modal-title" id="xpDatesLabel"><i class="mdi mdi-calendar-range mr-1"></i> Update Inclusive Dates</h5>
                                                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                        </div>

                                                        <div class="modal-body">
                                                            <input type="hidden" value="<?= $this->uri->segment(2); ?>" name="id_number">
                                                            <input type="hidden" name="id" value="" id="xp_dates_id">

                                                            <p class="text-muted" id="xp_dates_title">&nbsp;</p>

                                                            <div class="form-row">
                                                                <div class="form-group col-md-6">
                                                                    <label class="font-weight-semibold">From <span class="text-danger">*</span></label>
                                                                    <input name="date_from" type="date" id="xp_date_from" class="form-control js-range-from js-xp-from" data-range="editxp" value="" required>
                                                                </div>
                                                                <div class="form-group col-md-6">
                                                                    <label class="font-weight-semibold">To <span class="text-danger">*</span></label>
                                                                    <input name="date_to" type="date" id="xp_date_to" class="form-control js-range-to js-xp-to" data-range="editxp" value="" required>
                                                                </div>
                                                            </div>

                                                            <div class="alert alert-secondary py-2 mb-0">
                                                                <i class="mdi mdi-calendar-clock mr-1"></i>
                                                                Length of service: <strong class="js-xp-preview" data-for="editxp">&mdash;</strong>
                                                                <br><small class="text-muted">This replaces the stored years and months for this record.</small>
                                                            </div>
                                                        </div>

                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                                                            <button type="submit" name="submit" class="btn btn-primary waves-effect waves-light">Save Dates</button>
                                                        </div>
                                                    </form>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->


                                        <div class="modal fade ivan" tabindex="-1" role="dialog" aria-labelledby="trainingHoursLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <?php echo form_open('Page/update_trainings_staff', array('class' => 'parsley-examples')); ?>
                                                        <div class="modal-header bg-info text-white">
                                                            <h5 class="modal-title" id="trainingHoursLabel"><i class="mdi mdi-clock-outline mr-1"></i> Update No. of Hours</h5>
                                                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                        </div>

                                                        <div class="modal-body">
                                                            <input type="hidden" value="<?= $this->uri->segment(2); ?>" name="id_number">
                                                            <!-- Both fields are filled by the shared footer handler. -->
                                                            <input type="hidden" name="id" value="" id="id">

                                                            <div class="form-group mb-0">
                                                                <label class="font-weight-semibold">No. of Hours <span class="text-danger">*</span></label>
                                                                <input name="nh" type="text" id="appid" maxlength="11" oninput="this.value=this.value.replace(/[^0-9]/g,'')" class="form-control" value="" required>
                                                                <small class="form-text text-muted">Enter total hours credited for this training.</small>
                                                            </div>
                                                        </div>

                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                                                            <button type="submit" name="submit" class="btn btn-primary waves-effect waves-light">Save Hours</button>
                                                        </div>
                                                    </form>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->


                                        <script type="text/javascript">
                                            // Plain JS throughout: jQuery is not loaded until the footer runs.
                                            (function () {

                                                function parseDate(value) {
                                                    var parts = /^(\d{4})-(\d{2})-(\d{2})$/.exec(value || '');
                                                    if (!parts) return null;
                                                    return new Date(+parts[1], parts[2] - 1, +parts[3]);
                                                }

                                                // Mirrors Reg::experience_duration() so the preview matches what the
                                                // server will store. The server value stays authoritative on save.
                                                function monthsBetween(from, to) {
                                                    var months = (to.getFullYear() - from.getFullYear()) * 12 + (to.getMonth() - from.getMonth());
                                                    var days = to.getDate() - from.getDate();

                                                    if (days < 0) {
                                                        months--;
                                                        var anniversary = new Date(to.getFullYear(), to.getMonth() - 1, from.getDate());
                                                        days = Math.round((to - anniversary) / 86400000);
                                                    }

                                                    if (days >= 15) months++;
                                                    return months;
                                                }

                                                function refreshRange(group) {
                                                    var from = document.querySelector('.js-range-from[data-range="' + group + '"]');
                                                    var to = document.querySelector('.js-range-to[data-range="' + group + '"]');
                                                    var preview = document.querySelector('.js-xp-preview[data-for="' + group + '"]');
                                                    if (!from || !to) return;

                                                    // Stop the end date being set before the start date.
                                                    to.min = from.value || '';
                                                    if (from.value && to.value && to.value < from.value) {
                                                        to.value = '';
                                                    }

                                                    if (!preview) return;

                                                    var start = parseDate(from.value);
                                                    var end = parseDate(to.value);
                                                    if (!start || !end || end < start) {
                                                        preview.textContent = '—';
                                                        return;
                                                    }

                                                    var months = monthsBetween(start, end);
                                                    preview.textContent = Math.floor(months / 12) + ' year(s) and ' + (months % 12) + ' month(s)';
                                                }

                                                document.addEventListener('change', function (e) {
                                                    var field = e.target;
                                                    if (!field || !field.classList) return;
                                                    if (field.classList.contains('js-range-from') || field.classList.contains('js-range-to')) {
                                                        refreshRange(field.getAttribute('data-range'));
                                                    }
                                                });

                                                // The shared .open-AddBookDialog handler in the footer only knows about
                                                // single-value modals, so the inclusive-dates modal fills itself.
                                                document.addEventListener('click', function (e) {
                                                    var trigger = e.target && e.target.closest ? e.target.closest('.open-xp-dates') : null;
                                                    if (!trigger) return;

                                                    document.getElementById('xp_dates_id').value = trigger.getAttribute('data-id') || '';
                                                    document.getElementById('xp_date_from').value = trigger.getAttribute('data-from') || '';
                                                    document.getElementById('xp_date_to').value = trigger.getAttribute('data-to') || '';
                                                    document.getElementById('xp_dates_title').textContent = trigger.getAttribute('data-title') || '';

                                                    refreshRange('editxp');
                                                });
                                            })();
                                        </script>

                                        <script>
                                        document.addEventListener('DOMContentLoaded', function () {
                                        if (window.location.hash) {
                                            const link = document.querySelector('.nav a[href="' + window.location.hash + '"]');
                                            if (link) $(link).tab('show'); // bootstrap tab
                                        }
                                        });
                                        </script>
