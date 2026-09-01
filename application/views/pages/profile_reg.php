<!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->
             <?php
                $setting = $this->Common->one_cond_row('settings','id',11);

                // Trainings and work experience close with the vacancy applied for.
                // Once closed the two sections are read-only for everyone - nothing
                // is added, re-dated, re-rated or deleted, so what stood at closing
                // time is what the ranking was built on.
                $recordLock   = !empty($record_lock['locked']);
                $lockReason   = $recordLock ? (string) $record_lock['reason'] : '';

                // Two different locks, two different meanings.
                //   $recordsOpen     - may add or delete a row, i.e. may bring an
                //                      attachment in or take one out. Closed by
                //                      either the settings switch or the vacancy.
                //   $detailsEditable - may correct the text on a row that already
                //                      exists (company, job title, hours). The
                //                      settings switch leaves this open; only a
                //                      closed vacancy freezes it, so what was
                //                      ranked stays as it was ranked.
                $settingsLock    = ($setting->status != 0);
                $recordsOpen     = !$settingsLock && !$recordLock;
                $detailsEditable = !$recordLock;

                // "Saved on" stamp for a record. Rows encoded before the column
                // existed carry none, so those read as a dash.
                $saved_stamp = function ($created, $updated = null) {
                    $out = '<span class="text-muted">&mdash;</span>';

                    $c = !empty($created) ? strtotime($created) : false;
                    if ($c) {
                        $out = date('M d, Y', $c) . '<br><small class="text-muted">' . date('g:i A', $c) . '</small>';
                    }

                    $u = !empty($updated) ? strtotime($updated) : false;
                    if ($u && (!$c || date('Y-m-d H:i', $u) !== date('Y-m-d H:i', $c))) {
                        $out .= '<br><small class="text-info" title="Last change to this record">edited '
                            . date('M d, Y g:i A', $u) . '</small>';
                    }

                    return $out;
                };

                // Shown at the top of both closed sections.
                $lock_notice = function ($what) use ($lockReason) {
                    return '<div class="alert alert-warning d-flex align-items-center mb-3" role="alert">'
                        . '<i class="mdi mdi-lock-outline mr-2 font-18"></i>'
                        . '<div><strong>' . $what . ' is closed.</strong> ' . html_escape($lockReason)
                        . ' Records are shown for reference only and can no longer be added, edited or deleted.</div>'
                        . '</div>';
                };

                // The softer of the two: the file is frozen, the text is not.
                $attach_notice = function ($what) {
                    return '<div class="alert alert-info d-flex align-items-center mb-3" role="alert">'
                        . '<i class="mdi mdi-paperclip-off mr-2 font-18"></i>'
                        . '<div><strong>Attachments for ' . $what . ' are locked.</strong>'
                        . ' Records can no longer be added or removed, and files cannot be replaced.'
                        . ' Existing details may still be corrected.</div>'
                        . '</div>';
                };
             ?>

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
                /* Text cells that open an edit modal: readable first, clickable second. */
                .profile-record-table a.xp-editable {
                    color: inherit;
                    border-bottom: 1px dashed #adb5bd;
                    text-decoration: none;
                }
                .profile-record-table a.xp-editable:hover { color: #3f6ad8; border-bottom-color: #3f6ad8; }
                .profile-record-table a.xp-editable .mdi { opacity: 0; transition: opacity .15s ease; }
                .profile-record-table a.xp-editable:hover .mdi { opacity: 1; }
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
                                                                        <?php if($recordsOpen): ?>
                                                                        <li><a data-toggle="modal" data-id="<?= $a_user->id; ?>" class="open-AddBookDialog btn btn-info waves-effect width-md waves-light" href=".bs-example-modal-lg">Add New</a></li>
                                                                        <?php elseif($recordLock): ?>
                                                                        <li><span class="badge badge-warning"><i class="mdi mdi-lock-outline mr-1"></i>Closed</span></li>
                                                                        <?php endif; ?>
                                                                    </ol>
                                                                    </div>
                                                                    <div class="clearfix"></div>
                                                                    <br />
                                                                    <?php if($recordLock): ?>
                                                                        <?= $lock_notice('Trainings and seminars'); ?>
                                                                    <?php elseif($settingsLock): ?>
                                                                        <?= $attach_notice('trainings and seminars'); ?>
                                                                    <?php endif; ?>
                                                                    <?php
                                                                        // Deleting a training destroys its certificate, so it follows
                                                                        // the attachment lock; editing the hours does not.
                                                                        $canDeleteTrainings = $recordsOpen && !in_array($this->session->position, ['Evaluator','rater','raters']);
                                                                        $canManageTrainings = $detailsEditable && !in_array($this->session->position, ['Evaluator','rater','raters']);
                                                                        $canRateTrainings = !$recordLock && ($this->session->position == 'asds' || $this->session->position == 'raters' || ($this->session->position == 'Evaluator' && !empty($isAssignedEvaluator)));
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
                                                                                    <th class="text-center text-nowrap">Date &amp; Time Saved</th>
                                                                                    <th class="text-center"><?= $canDeleteTrainings ? 'Manage' : '&nbsp;' ?></th>
                                                                                </tr>
                                                                            </thead>

                                                                            <tbody>
                                                                                <?php
                                                                                    // Rows kept a time only once the encoding form started asking for
                                                                                    // one, so midnight reads as "no time recorded".
                                                                                    $training_stamp = function ($value) {
                                                                                        if (empty($value) || substr($value, 0, 10) === '0000-00-00') {
                                                                                            return '<span class="text-muted">&mdash;</span>';
                                                                                        }

                                                                                        $stamp = strtotime($value);
                                                                                        if (!$stamp) {
                                                                                            return '<span class="text-muted">&mdash;</span>';
                                                                                        }

                                                                                        $out = date('M d, Y', $stamp);
                                                                                        if (date('H:i', $stamp) !== '00:00') {
                                                                                            $out .= '<br><small class="text-muted">' . date('g:i A', $stamp) . '</small>';
                                                                                        }

                                                                                        return $out;
                                                                                    };
                                                                                ?>
                                                                                <?php foreach($training as $row){ ?>
                                                                                <tr>
                                                                                    <td><?= html_escape($row->trainingTitle); ?></td>
                                                                                    <td class="text-nowrap"><?= $training_stamp($row->dateStarted); ?></td>
                                                                                    <td class="text-nowrap"><?= $training_stamp($row->dateFinished); ?></td>
                                                                                    <td class="text-center"><a  href="<?= base_url().'uploads/trainings_staff/'.$row->file; ?>" target="_blank" class="tooltips" data-placement="top" data-toggle="tooltip" data-original-title="View File Attachment"><i  class="fas fa-file-alt btn btn-lg text-primary"></i></a></td>
                                                                                    <td class="text-center">
                                                                                        <?php if ($canRateTrainings){ ?>
                                                                                            <a data-toggle="modal" data-id="<?= $row->trainingID; ?>" data-appid="<?= $row->noHours; ?>" class="open-AddBookDialog badge badge-primary" href=".ivan"><?= $row->noHours; ?></a>
                                                                                        <?php }else{ ?>
                                                                                            <?php // Hours are a detail, so the settings lock leaves them open. ?>
                                                                                            <?php if ($detailsEditable){ ?>
                                                                                                <a data-toggle="modal" data-id="<?= $row->trainingID; ?>" data-appid="<?= $row->noHours; ?>" class="open-AddBookDialog badge badge-primary" href=".ivan"><?= $row->noHours; ?></a>
                                                                                            <?php }else{ ?>
                                                                                                <span class="badge badge-success"><?= $row->noHours; ?></span>
                                                                                            <?php } ?>
                                                                                        <?php } ?>
                                                                                    </td>
                                                                                    <td class="text-center">
                                                                                        <?php if (!$recordLock && ($canRateTrainings || $this->session->position == 'Evaluator')): ?>
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
                                                                                    <td class="text-center text-nowrap"><?= $saved_stamp($row->created_at ?? null, $row->updated_at ?? null); ?></td>
                                                                                    <td class="text-center">
                                                                                        <?php if($canDeleteTrainings): ?>
                                                                                            <a onclick="return confirm('Are you sure?')" href="<?= base_url(); ?>Page/training_delete_staff/<?= $row->trainingID; ?>" class="btn btn-sm btn-outline-danger"><i class="mdi mdi-trash-can-outline"></i> Delete</a>
                                                                                        <?php elseif($recordLock): ?>
                                                                                            <span class="text-muted" title="<?= html_escape($lockReason); ?>"><i class="mdi mdi-lock-outline"></i></span>
                                                                                        <?php endif; ?>
                                                                                    </td>
                                                                                </tr>
                                                                                <?php } ?>
                                                                                <?php if(empty($training)){ ?>
                                                                                <tr>
                                                                                    <td colspan="8" class="text-center text-muted py-4">No trainings or seminars recorded yet.</td>
                                                                                </tr>
                                                                                <?php } ?>
                                                                            </tbody>
                                                                            <tfoot>
                                                                                <tr class="bg-light">
                                                                                    <th colspan="4" class="text-right">Total Relevant Hours</th>
                                                                                    <th class="text-center"><span class="badge badge-purple"><?= (float) ($training_sum ?? 0); ?></span></th>
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
                                                                            <?php if($recordsOpen): ?>
                                                                                <a data-toggle="modal" data-id="<?= $user->id; ?>" class="open-AddBookDialog btn btn-info waves-effect width-md waves-light" href=".renrenguapo">Add New</a>
                                                                            <?php elseif($recordLock): ?>
                                                                                <span class="badge badge-warning"><i class="mdi mdi-lock-outline mr-1"></i>Closed</span>
                                                                            <?php endif; ?>
                                                                        </li>
                                                                        </ol>
                                                                    </div>
                                                                    <div class="clearfix"></div>
                                                                    <br />
                                                                    <?php if($recordLock): ?>
                                                                        <?= $lock_notice('Work experience'); ?>
                                                                    <?php elseif($settingsLock): ?>
                                                                        <?= $attach_notice('work experience'); ?>
                                                                    <?php endif; ?>
                                                                    <?php
                                                                        $canRateExperience = !$recordLock && ($this->session->position == 'asds' || $this->session->position == 'raters' || ($this->session->position == 'Evaluator' && !empty($isAssignedEvaluator)));
                                                                        // Company, job title and dates are details, so the settings
                                                                        // lock does not close them - only a closed vacancy does.
                                                                        $canEditExperienceDates = $detailsEditable;
                                                                        $canEditExperienceInfo  = $detailsEditable;
                                                                    ?>
                                                                    <?php if($canEditExperienceInfo): ?>
                                                                        <p class="text-muted mb-2">Tip: Click the <strong>company name</strong>, <strong>job title</strong> or <strong>inclusive dates</strong> of a row to correct them. The length of service is computed from the date range.</p>
                                                                    <?php endif; ?>
                                                                    <div class="table-responsive">
                                                                    <table class="table table-hover align-middle profile-record-table mb-0">
                                                                        <thead class="thead-light">
                                                                                <tr>
                                                                                    <th>Company Name</th>
                                                                                    <th>Job Title</th>
                                                                                    <th class="text-nowrap">From</th>
                                                                                    <th class="text-nowrap">To</th>
                                                                                    <th class="text-center text-nowrap">Length of Service</th>
                                                                                    <th class="text-center">Attachment</th>
                                                                                    <th class="text-center">Status</th>
                                                                                    <th class="text-center text-nowrap">Date &amp; Time Saved</th>
                                                                                    <th class="text-center">Manage</th>
                                                                                </tr>
                                                                            </thead>

                                                                            <tbody>
                                                                                <?php $xpTotalMonths = 0; ?>
                                                                                <?php foreach($experience as $row){
                                                                                    $xpFrom = !empty($row->date_from ?? null) && $row->date_from !== '0000-00-00' ? $row->date_from : null;
                                                                                    $xpTo   = !empty($row->date_to   ?? null) && $row->date_to   !== '0000-00-00' ? $row->date_to   : null;

                                                                                    // Read the length straight off the range. Records kept from
                                                                                    // before the dates were captured have none to read, so those
                                                                                    // still show the stored years and months.
                                                                                    $xpFromRange = ($xpFrom && $xpTo);
                                                                                    $xpSpan = $xpFromRange
                                                                                        ? $this->Reg->experience_months($xpFrom, $xpTo)
                                                                                        : ((int) $row->ny * 12) + (int) $row->nm;

                                                                                    if ((int) $row->stat === 1) {
                                                                                        $xpTotalMonths += $xpSpan;
                                                                                    }
                                                                                ?>
                                                                                <?php
                                                                                    $xpJobTitle = trim((string) ($row->position_title ?? ''));

                                                                                    // One control spans both date cells: clicking either opens the
                                                                                    // same "inclusive dates" modal pre-filled with the stored range.
                                                                                    $dateAttrs = 'data-id="' . (int) $row->id . '"'
                                                                                        . ' data-from="' . html_escape((string) $xpFrom) . '"'
                                                                                        . ' data-to="' . html_escape((string) $xpTo) . '"'
                                                                                        . ' data-title="' . html_escape((string) $row->title) . '"';

                                                                                    // Both text cells open the same "details" modal.
                                                                                    $infoAttrs = 'data-id="' . (int) $row->id . '"'
                                                                                        . ' data-company="' . html_escape((string) $row->title) . '"'
                                                                                        . ' data-job="' . html_escape($xpJobTitle) . '"';
                                                                                ?>
                                                                                <tr>
                                                                                    <td>
                                                                                        <?php if($canEditExperienceInfo){ ?>
                                                                                            <a data-toggle="modal" <?= $infoAttrs; ?> class="open-xp-info xp-editable" href=".xpinfo" title="Click to correct the company / office name"><?= html_escape($row->title); ?> <i class="mdi mdi-pencil-outline text-muted ml-1"></i></a>
                                                                                        <?php }else{ ?>
                                                                                            <?= html_escape($row->title); ?>
                                                                                        <?php } ?>
                                                                                    </td>
                                                                                    <td>
                                                                                        <?php if($canEditExperienceInfo){ ?>
                                                                                            <a data-toggle="modal" <?= $infoAttrs; ?> class="open-xp-info xp-editable<?= $xpJobTitle === '' ? ' text-warning' : ''; ?>" href=".xpinfo" title="Click to set the job title held at this office"><?= $xpJobTitle !== '' ? html_escape($xpJobTitle) : 'Set job title'; ?> <i class="mdi mdi-pencil-outline text-muted ml-1"></i></a>
                                                                                        <?php }else{ ?>
                                                                                            <?= $xpJobTitle !== '' ? html_escape($xpJobTitle) : '<span class="text-muted">&mdash;</span>'; ?>
                                                                                        <?php } ?>
                                                                                    </td>
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
                                                                                        <span class="badge badge-info" title="<?= $xpFromRange ? 'Computed from the inclusive dates.' : 'Stored years and months - this record has no inclusive dates.'; ?>"><?= intdiv($xpSpan, 12); ?> yr <?= $xpSpan % 12; ?> mo</span>
                                                                                    </td>
                                                                                    <td class="text-center"><a  href="<?= base_url().'uploads/experience/'.$row->file; ?>" target="_blank" class="tooltips" data-placement="top" data-toggle="tooltip" data-original-title="View File Attachment"><i  class="fas fa-file-alt btn btn-lg text-primary"></i></a></td>
                                                                                    <td class="text-center">
                                                                                        <?php if (!$recordLock && ($canRateExperience || $this->session->position == 'Evaluator')): ?>
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
                                                                                    <td class="text-center text-nowrap"><?= $saved_stamp($row->created_at ?? null, $row->updated_at ?? null); ?></td>
                                                                                    <td class="text-center">
                                                                                        <?php if($recordsOpen): ?>
                                                                                        <a onclick="return confirm('Are you sure?')" href="<?= base_url(); ?>Page/experience_delete/<?= $row->id; ?>" class="btn btn-sm btn-outline-danger"><i class="mdi mdi-trash-can-outline"></i> Delete</a>
                                                                                        <?php elseif($recordLock): ?>
                                                                                        <span class="text-muted" title="<?= html_escape($lockReason); ?>"><i class="mdi mdi-lock-outline"></i></span>
                                                                                        <?php endif; ?>
                                                                                    </td>
                                                                                </tr>
                                                                                <?php } ?>
                                                                                <?php if(empty($experience)){ ?>
                                                                                <tr>
                                                                                    <td colspan="9" class="text-center text-muted py-4">No work experience recorded yet.</td>
                                                                                </tr>
                                                                                <?php } ?>
                                                                            </tbody>
                                                                            <tfoot>
                                                                                <tr class="bg-light">
                                                                                    <th colspan="4" class="text-right">Total Relevant Experience</th>
                                                                                    <th class="text-center text-nowrap">
                                                                                        <span class="badge badge-purple"><?= intdiv($xpTotalMonths, 12); ?> yr <?= $xpTotalMonths % 12; ?> mo</span>
                                                                                    </th>
                                                                                    <th colspan="4"></th>
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
                                                                    <label class="font-weight-semibold">Inclusive Date &amp; Time &mdash; From <span class="text-danger">*</span></label>
                                                                    <input type="datetime-local" value="<?= set_value('dateStarted'); ?>" name="dateStarted" class="form-control js-range-from" data-range="training" required>
                                                                </div>
                                                                <div class="form-group col-md-6">
                                                                    <label class="font-weight-semibold">Inclusive Date &amp; Time &mdash; To <span class="text-danger">*</span></label>
                                                                    <input type="datetime-local" value="<?= set_value('dateFinished'); ?>" name="dateFinished" class="form-control js-range-to" data-range="training" required>
                                                                </div>
                                                            </div>

                                                            <div class="text-danger small mb-2 js-range-warning" data-for="training" style="display:none;"></div>

                                                            <div class="form-row">
                                                                <div class="form-group col-md-8">
                                                                    <label class="font-weight-semibold">Conducted By</label>
                                                                    <input type="text" value="<?= set_value('sponsor'); ?>" name="sponsor" class="form-control" placeholder="Sponsoring office or organization">
                                                                </div>
                                                                <div class="form-group col-md-4">
                                                                    <label class="font-weight-semibold">No. of Hours <span class="text-danger">*</span></label>
                                                                    <input name="noHours" type="text" class="form-control js-hours-total" data-for="training" value="" readonly required>
                                                                    <small class="form-text text-muted">Daily time window &times; number of days; 8 hours a day when no time window is set.</small>
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

                                                            <div class="form-group">
                                                                <label class="font-weight-semibold">Job Title / Position Held <span class="text-danger">*</span></label>
                                                                <input name="position_title" type="text" class="form-control" maxlength="255" placeholder="e.g. Administrative Assistant II" value="<?= set_value('position_title'); ?>" required>
                                                                <small class="form-text text-muted">The post actually held at this office, as written on the service record.</small>
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

                                                            <div class="text-danger small mb-2 js-range-warning" data-for="newxp" style="display:none;"></div>

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

                                        <div class="modal fade xpinfo" tabindex="-1" role="dialog" aria-labelledby="xpInfoLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <?php echo form_open('Page/update_experience_details', array('class' => 'parsley-examples')); ?>
                                                        <div class="modal-header bg-info text-white">
                                                            <h5 class="modal-title" id="xpInfoLabel"><i class="mdi mdi-briefcase-edit-outline mr-1"></i> Edit Work Experience</h5>
                                                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                        </div>

                                                        <div class="modal-body">
                                                            <input type="hidden" value="<?= $this->uri->segment(2); ?>" name="id_number">
                                                            <input type="hidden" name="id" value="" id="xp_info_id">

                                                            <div class="form-group">
                                                                <label class="font-weight-semibold">Company / Office Name <span class="text-danger">*</span></label>
                                                                <textarea name="title" id="xp_info_company" class="form-control" rows="3" required></textarea>
                                                            </div>

                                                            <div class="form-group mb-0">
                                                                <label class="font-weight-semibold">Job Title / Position Held</label>
                                                                <input name="position_title" id="xp_info_job" type="text" maxlength="255" class="form-control" value="">
                                                                <small class="form-text text-muted">The post actually held at this office, as written on the service record.</small>
                                                            </div>
                                                        </div>

                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                                                            <button type="submit" name="submit" class="btn btn-primary waves-effect waves-light">Save Details</button>
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

                                                            <div class="text-danger small mb-2 js-range-warning" data-for="editxp" style="display:none;"></div>

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

                                                // Handles both the date-only experience fields and the training
                                                // fields, which carry a time (2025-01-15T08:00).
                                                function parseDate(value) {
                                                    var parts = /^(\d{4})-(\d{2})-(\d{2})(?:T(\d{2}):(\d{2}))?/.exec(value || '');
                                                    if (!parts) return null;
                                                    return new Date(+parts[1], parts[2] - 1, +parts[3], +(parts[4] || 0), +(parts[5] || 0));
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

                                                // Records are historical, so these fields must accept any date. A min/max
                                                // bound on them (Parsley reads those attributes) is what blocked saving an
                                                // older range, so the bounds are stripped and the order is checked here.
                                                function unbindDateLimits(field) {
                                                    if (!field) return;
                                                    field.removeAttribute('min');
                                                    field.removeAttribute('max');
                                                }

                                                function clearAllDateLimits() {
                                                    var fields = document.querySelectorAll('.js-range-from, .js-range-to');
                                                    for (var i = 0; i < fields.length; i++) {
                                                        unbindDateLimits(fields[i]);
                                                    }
                                                }

                                                function setRangeWarning(group, message) {
                                                    var box = document.querySelector('.js-range-warning[data-for="' + group + '"]');
                                                    if (!box) return;
                                                    box.textContent = message || '';
                                                    box.style.display = message ? '' : 'none';
                                                }

                                                // Mirrors Page_model::training_hours(): the daily time window times
                                                // the number of days, so overnight gaps are not credited. Without a
                                                // usable window the day counts as a standard 8 hours.
                                                function trainingHours(start, end) {
                                                    var startDay = new Date(start.getFullYear(), start.getMonth(), start.getDate());
                                                    var endDay = new Date(end.getFullYear(), end.getMonth(), end.getDate());
                                                    var days = Math.round((endDay - startDay) / 86400000) + 1;
                                                    if (days <= 0) return 0;

                                                    var window = (end.getHours() * 60 + end.getMinutes()) - (start.getHours() * 60 + start.getMinutes());
                                                    if (window <= 0) return days * 8;

                                                    return Math.round(days * window / 60 * 100) / 100;
                                                }

                                                function refreshRange(group) {
                                                    var from = document.querySelector('.js-range-from[data-range="' + group + '"]');
                                                    var to = document.querySelector('.js-range-to[data-range="' + group + '"]');
                                                    var preview = document.querySelector('.js-xp-preview[data-for="' + group + '"]');
                                                    var hours = document.querySelector('.js-hours-total[data-for="' + group + '"]');
                                                    if (!from || !to) return;

                                                    unbindDateLimits(from);
                                                    unbindDateLimits(to);

                                                    var start = parseDate(from.value);
                                                    var end = parseDate(to.value);
                                                    var reversed = !!(start && end && end < start);

                                                    setRangeWarning(group, reversed ? 'The "to" date cannot be earlier than the "from" date.' : '');

                                                    if (hours) {
                                                        hours.value = (start && end && !reversed) ? trainingHours(start, end) : '';
                                                    }

                                                    if (!preview) return;

                                                    if (!start || !end || reversed) {
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

                                                // Captured on the way down so the range is settled before Parsley's own
                                                // submit handler on the form runs.
                                                document.addEventListener('submit', function (e) {
                                                    var form = e.target;
                                                    if (!form || !form.querySelector) return;

                                                    var from = form.querySelector('.js-range-from');
                                                    var to = form.querySelector('.js-range-to');
                                                    if (!from || !to) return;

                                                    unbindDateLimits(from);
                                                    unbindDateLimits(to);

                                                    var start = parseDate(from.value);
                                                    var end = parseDate(to.value);
                                                    if (start && end && end < start) {
                                                        e.preventDefault();
                                                        e.stopImmediatePropagation();
                                                        setRangeWarning(from.getAttribute('data-range'), 'The "to" date cannot be earlier than the "from" date.');
                                                        to.focus();
                                                    }
                                                }, true);

                                                clearAllDateLimits();
                                                document.addEventListener('DOMContentLoaded', clearAllDateLimits);

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

                                                // Same idea for the company / job title modal.
                                                document.addEventListener('click', function (e) {
                                                    var trigger = e.target && e.target.closest ? e.target.closest('.open-xp-info') : null;
                                                    if (!trigger) return;

                                                    document.getElementById('xp_info_id').value = trigger.getAttribute('data-id') || '';
                                                    document.getElementById('xp_info_company').value = trigger.getAttribute('data-company') || '';
                                                    document.getElementById('xp_info_job').value = trigger.getAttribute('data-job') || '';
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
