<!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->
             <?php
                $setting = $this->Common->one_cond_row('settings','id',11);

                // Adding/removing attachments closes with the vacancy and with the
                // administrator switch. Existing details remain correctable by
                // staff; only the applicant is frozen when editing is closed.
                $recordLock   = !empty($record_lock['locked']);
                $lockReason   = $recordLock ? (string) $record_lock['reason'] : '';
                $sessionRole  = trim((string) ($this->session->position ?? ''));
                $isApplicantAccount = strtolower($sessionRole) === 'reg';
                // Verifier is intentionally review-only across the application.
                $isStaffAccount = $sessionRole !== ''
                    && !$isApplicantAccount
                    && $sessionRole !== 'Verifier';

                // Two different locks, two different meanings.
                //   $recordsOpen     - may add or delete a row, i.e. may bring an
                //                      attachment in or take one out. Closed by
                //                      either the settings switch or the vacancy.
                //   $detailsEditable - staff may correct existing details at any
                //                      time; applicants may do so only while both
                //                      locks are open.
                $settingsLock    = ((int) ($setting->status ?? 0) !== 0);
                $recordsOpen     = !$settingsLock && !$recordLock;
                $detailsEditable = $isStaffAccount || ($isApplicantAccount && $recordsOpen);

                $relevanceVacancies = $relevance['vacancies'] ?? array();
                $relevanceMap       = $relevance['map'] ?? array('training' => array(), 'experience' => array());
                $selectedVacancy    = null;
                foreach ($relevanceVacancies as $vacancy) {
                    if (!empty($vacancy->is_context)) {
                        $selectedVacancy = $vacancy;
                        break;
                    }
                }
                $selectedJobId = (int) ($selectedVacancy->jobID ?? 0);
                $selectedVacancyLabel = trim((string) ($selectedVacancy->label ?? ''));
                $canRateSelectedVacancy = $selectedJobId > 0
                    && $this->Reg->can_set_relevance((int) $a_user->id, $selectedJobId);

                $relevance_label = function ($stat) {
                    if ((int) $stat === 1) return array('Relevant', 'success');
                    if ((int) $stat === 2) return array('Not Relevant', 'danger');
                    return array('No Action', 'secondary');
                };

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

                // One compact line replaces the repeated vacancy and lock notices.
                $record_context_strip = function () use (
                    $selectedJobId,
                    $selectedVacancyLabel,
                    $recordLock,
                    $settingsLock,
                    $detailsEditable,
                    $lockReason
                ) {
                    $vacancy = $selectedJobId > 0 ? $selectedVacancyLabel : 'No active vacancy';
                    $lockText = $recordLock ? 'Records closed' : ($settingsLock ? 'Attachments locked' : 'Attachments open');
                    $lockIcon = $recordLock ? 'mdi-lock-outline' : ($settingsLock ? 'mdi-paperclip-off' : 'mdi-paperclip');
                    $lockTitle = $recordLock && $lockReason !== '' ? ' title="' . html_escape($lockReason) . '"' : '';
                    $editText = $detailsEditable ? 'Details editable' : 'Read-only';
                    $editClass = $detailsEditable ? 'is-editable' : 'is-readonly';

                    return '<div class="record-context-strip" role="status">'
                        . '<span class="context-vacancy"><i class="mdi mdi-information-outline"></i><strong>Relevance: '
                        . html_escape($vacancy) . '</strong></span>'
                        . '<span' . $lockTitle . '><i class="mdi ' . $lockIcon . '"></i>' . $lockText . '</span>'
                        . '<span class="' . $editClass . '"><i class="mdi mdi-pencil-outline"></i>' . $editText . '</span>'
                        . '</div>';
                };
             ?>

            <style>
                .record-section {
                    background: #fff;
                    border: 1px solid #e7ebf0;
                    border-radius: 16px;
                    box-shadow: 0 8px 24px rgba(31, 45, 61, .06);
                    overflow: hidden;
                }
                .record-section-header {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    gap: 1rem;
                    padding: 1.15rem 1.25rem;
                    border-bottom: 1px solid #edf0f4;
                    background: linear-gradient(135deg, #f8fbff 0%, #fff 70%);
                }
                .record-section-title { margin: 0; font-size: 1.05rem; font-weight: 700; color: #263238; }
                .record-section-body { padding: 1.1rem 1.25rem 1.25rem; }
                .profile-record-table { min-width: 760px; }
                .profile-record-table th,
                .profile-record-table td { vertical-align: middle; }
                .profile-record-table thead th {
                    text-transform: uppercase;
                    font-size: .72rem;
                    letter-spacing: .04em;
                    border-top: 0;
                    white-space: nowrap;
                }
                .profile-record-table .badge { font-size: .78rem; padding: .35em .6em; }
                .profile-record-table .record-title { font-weight: 600; color: #263238; line-height: 1.35; }
                .profile-record-table .record-meta { color: #7b8794; font-size: .77rem; margin-top: .3rem; }
                .profile-record-table a.record-editable {
                    color: inherit;
                    border-bottom: 1px dashed #adb5bd;
                    text-decoration: none;
                }
                .profile-record-table a.record-editable:hover { color: #3f6ad8; border-bottom-color: #3f6ad8; }
                .record-dates { color: #405061; font-size: .84rem; line-height: 1.65; }
                .record-credit { font-size: 1rem; font-weight: 700; color: #2c5cc5; }
                .record-file { margin-top: .45rem; }
                .record-context-strip {
                    display: flex;
                    align-items: center;
                    flex-wrap: wrap;
                    gap: .4rem .9rem;
                    padding: .55rem .75rem;
                    margin-bottom: .85rem;
                    border: 1px solid #d8e5f6;
                    border-radius: 8px;
                    background: #f7faff;
                    color: #52667d;
                    font-size: .78rem;
                }
                .record-context-strip span { display: inline-flex; align-items: center; gap: .28rem; white-space: nowrap; }
                .record-context-strip .context-vacancy { color: #244b78; }
                .record-context-strip .is-editable { color: #18845c; }
                .record-context-strip .is-readonly { color: #9a6700; }
                .vacancy-totals { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: .75rem; margin-top: 1rem; }
                .vacancy-total-card { border: 1px solid #e4e9f0; border-radius: 12px; padding: .9rem 1rem; background: #f8fafc; }
                .vacancy-total-label { color: #667085; font-size: .76rem; line-height: 1.25; }
                .vacancy-total-value { color: #253858; font-size: 1.15rem; font-weight: 700; margin-top: .25rem; }
                .vacancy-total-card.is-context { border-color: #8cb4ff; background: #f2f7ff; }
                .record-actions { display: flex; flex-direction: row; flex-wrap: wrap; gap: .45rem; align-items: center; padding-top: .15rem; }
                .record-actions .btn { white-space: nowrap; }
                .record-edit-button {
                    border: 0;
                    border-radius: 7px;
                    box-shadow: 0 3px 8px rgba(52, 140, 212, .24);
                    font-weight: 600;
                }
                .record-edit-button:hover { box-shadow: 0 5px 12px rgba(52, 140, 212, .32); transform: translateY(-1px); }
                .relevance-editor { display: flex; align-items: center; gap: .35rem; margin: 0; }
                .relevance-editor .form-control {
                    min-width: 142px;
                    height: 36px;
                    padding: .25rem 1.7rem .25rem .55rem;
                    border-width: 2px;
                    border-radius: 7px;
                    font-size: .78rem;
                    font-weight: 600;
                    cursor: pointer;
                }
                .relevance-editor .relevance-status-0 { border-color: #c8d0da; background-color: #f8f9fa; color: #5f6b76; }
                .relevance-editor .relevance-status-1 { border-color: #48b88a; background-color: #effaf5; color: #187a55; }
                .relevance-editor .relevance-status-2 { border-color: #e28b94; background-color: #fff4f5; color: #ad3340; }
                .relevance-editor.is-saving .form-control { opacity: .62; cursor: wait; }
                .relevance-save-state { display: none; color: #348cd4; font-size: .75rem; white-space: nowrap; }
                .relevance-editor.is-saving .relevance-save-state { display: inline-flex; align-items: center; gap: .2rem; }
                .swal2-popup.relevance-feedback-modal { border-radius: 14px; }
                .relevance-feedback-copy { color: #52606d; font-size: .82rem; line-height: 1.5; text-align: left; }
                .relevance-feedback-total { margin-top: .7rem; padding: .55rem .65rem; border-radius: 7px; background: #f2f7ff; color: #244b78; font-weight: 600; }
                .profile-record-table .btn-lg { padding: 0; line-height: 1; }
                @media (max-width: 767.98px) {
                    .tab-content { padding-left: .75rem !important; padding-right: .75rem !important; }
                    .record-section-header { align-items: flex-start; padding: 1rem; }
                    .record-section-header .btn { width: auto; }
                    .record-section-body { padding: .8rem; }
                    .profile-record-table { min-width: 0; border-collapse: separate; border-spacing: 0 .75rem; }
                    .profile-record-table thead { display: none; }
                    .profile-record-table tbody, .profile-record-table tr, .profile-record-table td { display: block; width: 100%; }
                    .profile-record-table tbody tr { border: 1px solid #e3e8ef; border-radius: 12px; padding: .35rem .8rem; background: #fff; box-shadow: 0 4px 12px rgba(31,45,61,.05); }
                    .profile-record-table tbody td { display: grid; grid-template-columns: minmax(105px, 34%) 1fr; gap: .65rem; padding: .65rem 0; border-top: 1px solid #eef1f5; text-align: left !important; }
                    .profile-record-table tbody td:first-child { border-top: 0; }
                    .profile-record-table tbody td::before { content: attr(data-label); color: #7b8794; font-size: .7rem; font-weight: 700; letter-spacing: .04em; text-transform: uppercase; }
                    .profile-record-table tbody td.record-primary { display: block; padding-top: .75rem; }
                    .profile-record-table tbody td.record-primary::before { display: none; }
                    .profile-record-table tbody td[colspan] { display: block; }
                    .profile-record-table tbody td[colspan]::before { display: none; }
                    .relevance-editor { align-items: stretch; flex-direction: column; }
                    .relevance-editor .form-control { width: 100%; }
                    .vacancy-totals { grid-template-columns: 1fr; }
                    .modal-dialog { margin: .5rem; }
                }
                @media (max-width: 479.98px) {
                    .record-section-header { flex-direction: column; }
                    .record-section-header .btn { width: 100%; }
                }
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
                                                    <?php
                                                        $canDeleteTrainings = $recordsOpen && !in_array($sessionRole, array('Evaluator', 'rater', 'raters'), true);
                                                        $trainingTotal = 0;

                                                        // Legacy rows may have dates without a time component.
                                                        $training_stamp = function ($value) {
                                                            if (empty($value) || substr($value, 0, 10) === '0000-00-00') return '<span class="text-muted">&mdash;</span>';
                                                            $stamp = strtotime($value);
                                                            if (!$stamp) return '<span class="text-muted">&mdash;</span>';
                                                            $out = date('M d, Y', $stamp);
                                                            if (date('H:i', $stamp) !== '00:00') $out .= ' <small class="text-muted">' . date('g:i A', $stamp) . '</small>';
                                                            return $out;
                                                        };
                                                    ?>
                                                    <section class="record-section">
                                                        <div class="record-section-header">
                                                            <div>
                                                                <h4 class="record-section-title"><i class="mdi mdi-school-outline text-info mr-1"></i> Trainings &amp; Seminars</h4>
                                                            </div>
                                                            <?php if($recordsOpen): ?>
                                                                <a data-toggle="modal" data-id="<?= (int) $a_user->id; ?>" class="open-AddBookDialog btn btn-info waves-effect waves-light" href=".bs-example-modal-lg"><i class="mdi mdi-plus mr-1"></i>Add Training</a>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="record-section-body">
                                                            <?= $record_context_strip(); ?>

                                                            <div class="table-responsive">
                                                                <table class="table table-hover profile-record-table mb-0">
                                                                    <thead class="thead-light">
                                                                        <tr>
                                                                            <th style="width:32%">Training</th>
                                                                            <th style="width:21%">Inclusive Schedule</th>
                                                                            <th style="width:17%" class="text-center">Hours &amp; File</th>
                                                                            <th style="width:30%">Relevance</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <?php foreach($training as $row){
                                                                            $trainingId = (int) $row->trainingID;
                                                                            $startValue = !empty($row->dateStarted) && substr($row->dateStarted, 0, 10) !== '0000-00-00' ? date('Y-m-d\TH:i', strtotime($row->dateStarted)) : '';
                                                                            $endValue = !empty($row->dateFinished) && substr($row->dateFinished, 0, 10) !== '0000-00-00' ? date('Y-m-d\TH:i', strtotime($row->dateFinished)) : '';
                                                                            $trainingAttrs = 'data-id="' . $trainingId . '"'
                                                                                . ' data-title="' . html_escape((string) $row->trainingTitle) . '"'
                                                                                . ' data-from="' . html_escape($startValue) . '"'
                                                                                . ' data-to="' . html_escape($endValue) . '"'
                                                                                . ' data-hours="' . html_escape((string) $row->noHours) . '"';

                                                                            $trainingStat = (int) ($relevanceMap['training'][$trainingId][$selectedJobId] ?? 0);
                                                                            if ($selectedJobId > 0 && $trainingStat === 1) {
                                                                                $trainingTotal += (float) $row->noHours;
                                                                            }
                                                                        ?>
                                                                        <tr>
                                                                            <td class="record-primary" data-label="Training">
                                                                                <div class="record-title"><?= html_escape($row->trainingTitle); ?></div>
                                                                                <div class="record-meta">Saved <?= $saved_stamp($row->created_at ?? null, $row->updated_at ?? null); ?></div>
                                                                                <div class="record-actions mt-2">
                                                                                    <?php if($detailsEditable): ?>
                                                                                        <a data-toggle="modal" <?= $trainingAttrs; ?> class="open-training-edit btn btn-sm btn-info record-edit-button" href=".training-edit"><i class="mdi mdi-pencil-outline mr-1"></i>Edit details</a>
                                                                                    <?php endif; ?>
                                                                                    <?php if($canDeleteTrainings): ?>
                                                                                        <a onclick="return confirm('Delete this training and its attachment?')" href="<?= base_url(); ?>Page/training_delete_staff/<?= $trainingId; ?>" class="btn btn-sm btn-outline-danger"><i class="mdi mdi-trash-can-outline mr-1"></i>Delete</a>
                                                                                    <?php elseif(!$detailsEditable): ?>
                                                                                        <span class="text-muted"><i class="mdi mdi-lock-outline mr-1"></i>Read-only</span>
                                                                                    <?php endif; ?>
                                                                                </div>
                                                                            </td>
                                                                            <td data-label="Inclusive Schedule">
                                                                                <div class="record-dates"><strong>From</strong> <?= $training_stamp($row->dateStarted); ?><br><strong>To</strong> <?= $training_stamp($row->dateFinished); ?></div>
                                                                            </td>
                                                                            <td class="text-center" data-label="Credit &amp; File">
                                                                                <div class="record-credit"><?= html_escape((string) $row->noHours); ?> hr</div>
                                                                                <div class="record-file">
                                                                                    <?php if(!empty($row->file)): ?>
                                                                                        <a href="<?= base_url().'uploads/trainings_staff/'.rawurlencode((string) $row->file); ?>" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary"><i class="fas fa-file-pdf mr-1"></i>Certificate</a>
                                                                                    <?php else: ?>
                                                                                        <span class="text-muted small">No attachment</span>
                                                                                    <?php endif; ?>
                                                                                </div>
                                                                            </td>
                                                                            <td data-label="Vacancy Relevance">
                                                                                <?php $status = $relevance_label($trainingStat); ?>
                                                                                <?php if($selectedJobId < 1): ?>
                                                                                    <span class="badge badge-secondary">No vacancy selected</span>
                                                                                <?php elseif($canRateSelectedVacancy): ?>
                                                                                    <?= form_open('Page/update_record_relevance', array('class' => 'relevance-editor js-relevance-form')); ?>
                                                                                        <select name="relevance" class="form-control custom-select js-relevance-select relevance-status-<?= $trainingStat; ?>" data-previous-value="training:<?= $trainingId; ?>:<?= $selectedJobId; ?>:<?= $trainingStat; ?>" aria-label="Training relevance for <?= html_escape($selectedVacancyLabel); ?>">
                                                                                            <option value="training:<?= $trainingId; ?>:<?= $selectedJobId; ?>:0" <?= $trainingStat === 0 ? 'selected' : ''; ?>>No Action</option>
                                                                                            <option value="training:<?= $trainingId; ?>:<?= $selectedJobId; ?>:1" <?= $trainingStat === 1 ? 'selected' : ''; ?>>Relevant</option>
                                                                                            <option value="training:<?= $trainingId; ?>:<?= $selectedJobId; ?>:2" <?= $trainingStat === 2 ? 'selected' : ''; ?>>Not Relevant</option>
                                                                                        </select>
                                                                                        <span class="relevance-save-state"><i class="mdi mdi-loading mdi-spin"></i>Saving</span>
                                                                                    </form>
                                                                                <?php else: ?>
                                                                                    <span class="badge badge-<?= $status[1]; ?>"><?= $status[0]; ?></span>
                                                                                <?php endif; ?>
                                                                            </td>
                                                                        </tr>
                                                                        <?php } ?>
                                                                        <?php if(empty($training)): ?>
                                                                            <tr><td colspan="4" class="text-center text-muted py-4">No trainings or seminars recorded yet.</td></tr>
                                                                        <?php endif; ?>
                                                                    </tbody>
                                                                </table>
                                                            </div>

                                                            <div class="vacancy-totals" aria-label="Total relevant training hours for selected vacancy">
                                                                <?php $hoursText = rtrim(rtrim(number_format($trainingTotal, 2, '.', ''), '0'), '.'); ?>
                                                                <div class="vacancy-total-card is-context">
                                                                    <div class="vacancy-total-label"><?= $selectedJobId > 0 ? 'Total relevant hours for ' . html_escape($selectedVacancyLabel) : 'No active vacancy selected'; ?></div>
                                                                    <div class="vacancy-total-value" data-relevance-total="training"><?= $hoursText; ?> hour<?= $trainingTotal === 1.0 ? '' : 's'; ?></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </section>
                                                </div>
                                                <!-- End of Trainings -->

                                                <!-- Work Experience -->
                                                <div id="work" class="tab-pane">
                                                    <?php
                                                        $experienceTotal = 0;
                                                    ?>
                                                    <section class="record-section">
                                                        <div class="record-section-header">
                                                            <div>
                                                                <h4 class="record-section-title"><i class="mdi mdi-briefcase-outline text-info mr-1"></i> Work Experience</h4>
                                                            </div>
                                                            <?php if($recordsOpen): ?>
                                                                <a data-toggle="modal" data-id="<?= (int) $user->id; ?>" class="open-AddBookDialog btn btn-info waves-effect waves-light" href=".renrenguapo"><i class="mdi mdi-plus mr-1"></i>Add Experience</a>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="record-section-body">
                                                            <?= $record_context_strip(); ?>

                                                            <div class="table-responsive">
                                                                <table class="table table-hover profile-record-table mb-0">
                                                                    <thead class="thead-light">
                                                                        <tr>
                                                                            <th style="width:32%">Experience</th>
                                                                            <th style="width:21%">Inclusive Dates</th>
                                                                            <th style="width:17%" class="text-center">Duration &amp; File</th>
                                                                            <th style="width:30%">Relevance</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <?php foreach($experience as $row){
                                                                            $experienceId = (int) $row->id;
                                                                            $xpFrom = !empty($row->date_from ?? null) && $row->date_from !== '0000-00-00' ? $row->date_from : null;
                                                                            $xpTo   = !empty($row->date_to ?? null) && $row->date_to !== '0000-00-00' ? $row->date_to : null;
                                                                            $xpFromRange = ($xpFrom && $xpTo);
                                                                            $xpSpan = $xpFromRange
                                                                                ? $this->Reg->experience_months($xpFrom, $xpTo)
                                                                                : ((int) $row->ny * 12) + (int) $row->nm;
                                                                            $xpJobTitle = trim((string) ($row->position_title ?? ''));
                                                                            $experienceAttrs = 'data-id="' . $experienceId . '"'
                                                                                . ' data-company="' . html_escape((string) $row->title) . '"'
                                                                                . ' data-job="' . html_escape($xpJobTitle) . '"'
                                                                                . ' data-from="' . html_escape((string) $xpFrom) . '"'
                                                                                . ' data-to="' . html_escape((string) $xpTo) . '"';

                                                                            $experienceStat = (int) ($relevanceMap['experience'][$experienceId][$selectedJobId] ?? 0);
                                                                            if ($selectedJobId > 0 && $experienceStat === 1) {
                                                                                $experienceTotal += $xpSpan;
                                                                            }
                                                                        ?>
                                                                        <tr>
                                                                            <td class="record-primary" data-label="Experience">
                                                                                <div class="record-title"><?= $xpJobTitle !== '' ? html_escape($xpJobTitle) : '<span class="text-warning">Job title not set</span>'; ?></div>
                                                                                <div class="text-muted mt-1"><?= html_escape($row->title); ?></div>
                                                                                <div class="record-meta">Saved <?= $saved_stamp($row->created_at ?? null, $row->updated_at ?? null); ?></div>
                                                                                <div class="record-actions mt-2">
                                                                                    <?php if($detailsEditable): ?>
                                                                                        <a data-toggle="modal" <?= $experienceAttrs; ?> class="open-experience-edit btn btn-sm btn-info record-edit-button" href=".experience-edit"><i class="mdi mdi-pencil-outline mr-1"></i>Edit details</a>
                                                                                    <?php endif; ?>
                                                                                    <?php if($recordsOpen): ?>
                                                                                        <a onclick="return confirm('Delete this experience and its attachment?')" href="<?= base_url(); ?>Page/experience_delete/<?= $experienceId; ?>" class="btn btn-sm btn-outline-danger"><i class="mdi mdi-trash-can-outline mr-1"></i>Delete</a>
                                                                                    <?php elseif(!$detailsEditable): ?>
                                                                                        <span class="text-muted"><i class="mdi mdi-lock-outline mr-1"></i>Read-only</span>
                                                                                    <?php endif; ?>
                                                                                </div>
                                                                            </td>
                                                                            <td data-label="Inclusive Dates">
                                                                                <div class="record-dates"><strong>From</strong> <?= $xpFrom ? date('M d, Y', strtotime($xpFrom)) : '<span class="text-muted">&mdash;</span>'; ?><br><strong>To</strong> <?= $xpTo ? date('M d, Y', strtotime($xpTo)) : '<span class="text-muted">&mdash;</span>'; ?></div>
                                                                            </td>
                                                                            <td class="text-center" data-label="Duration &amp; File">
                                                                                <div class="record-credit" title="<?= $xpFromRange ? 'Calculated from the inclusive dates.' : 'Stored duration; inclusive dates are not yet set.'; ?>"><?= intdiv($xpSpan, 12); ?> yr <?= $xpSpan % 12; ?> mo</div>
                                                                                <div class="record-file">
                                                                                    <?php if(!empty($row->file)): ?>
                                                                                        <a href="<?= base_url().'uploads/experience/'.rawurlencode((string) $row->file); ?>" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary"><i class="fas fa-file-pdf mr-1"></i>Evidence</a>
                                                                                    <?php else: ?>
                                                                                        <span class="text-muted small">No attachment</span>
                                                                                    <?php endif; ?>
                                                                                </div>
                                                                            </td>
                                                                            <td data-label="Vacancy Relevance">
                                                                                <?php $status = $relevance_label($experienceStat); ?>
                                                                                <?php if($selectedJobId < 1): ?>
                                                                                    <span class="badge badge-secondary">No vacancy selected</span>
                                                                                <?php elseif($canRateSelectedVacancy): ?>
                                                                                    <?= form_open('Page/update_record_relevance', array('class' => 'relevance-editor js-relevance-form')); ?>
                                                                                        <select name="relevance" class="form-control custom-select js-relevance-select relevance-status-<?= $experienceStat; ?>" data-previous-value="experience:<?= $experienceId; ?>:<?= $selectedJobId; ?>:<?= $experienceStat; ?>" aria-label="Experience relevance for <?= html_escape($selectedVacancyLabel); ?>">
                                                                                            <option value="experience:<?= $experienceId; ?>:<?= $selectedJobId; ?>:0" <?= $experienceStat === 0 ? 'selected' : ''; ?>>No Action</option>
                                                                                            <option value="experience:<?= $experienceId; ?>:<?= $selectedJobId; ?>:1" <?= $experienceStat === 1 ? 'selected' : ''; ?>>Relevant</option>
                                                                                            <option value="experience:<?= $experienceId; ?>:<?= $selectedJobId; ?>:2" <?= $experienceStat === 2 ? 'selected' : ''; ?>>Not Relevant</option>
                                                                                        </select>
                                                                                        <span class="relevance-save-state"><i class="mdi mdi-loading mdi-spin"></i>Saving</span>
                                                                                    </form>
                                                                                <?php else: ?>
                                                                                    <span class="badge badge-<?= $status[1]; ?>"><?= $status[0]; ?></span>
                                                                                <?php endif; ?>
                                                                            </td>
                                                                        </tr>
                                                                        <?php } ?>
                                                                        <?php if(empty($experience)): ?>
                                                                            <tr><td colspan="4" class="text-center text-muted py-4">No work experience recorded yet.</td></tr>
                                                                        <?php endif; ?>
                                                                    </tbody>
                                                                </table>
                                                            </div>

                                                            <div class="vacancy-totals" aria-label="Total relevant experience for selected vacancy">
                                                                <div class="vacancy-total-card is-context">
                                                                    <div class="vacancy-total-label"><?= $selectedJobId > 0 ? 'Total relevant experience for ' . html_escape($selectedVacancyLabel) : 'No active vacancy selected'; ?></div>
                                                                    <div class="vacancy-total-value" data-relevance-total="experience"><?= intdiv($experienceTotal, 12); ?> yr <?= $experienceTotal % 12; ?> mo</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </section>
                                                </div>
                                                <!-- End of Work Experience -->


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
                                                            <input type="hidden" name="id" id="id" value="<?= (int) $a_user->id; ?>">
                                                            <input type="hidden" name="profile_app_id" value="<?= (int) ($profile_application->appID ?? 0); ?>">
                                                            <input type="hidden" name="profile_job_id" value="<?= (int) ($profile_application->jobID ?? 0); ?>">

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
                                                            <input type="hidden" name="profile_app_id" value="<?= (int) ($profile_application->appID ?? 0); ?>">
                                                            <input type="hidden" name="profile_job_id" value="<?= (int) ($profile_application->jobID ?? 0); ?>">

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

                                        <div class="modal fade experience-edit" tabindex="-1" role="dialog" aria-labelledby="experienceEditLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                                <div class="modal-content">
                                                    <?= form_open('Page/update_experience_details', array('class' => 'parsley-examples')); ?>
                                                        <div class="modal-header bg-info text-white">
                                                            <h5 class="modal-title" id="experienceEditLabel"><i class="mdi mdi-briefcase-edit-outline mr-1"></i>Edit Work Experience</h5>
                                                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <input type="hidden" value="<?= (int) $this->uri->segment(2); ?>" name="id_number">
                                                            <input type="hidden" name="id" value="" id="experience_edit_id">
                                                            <input type="hidden" name="profile_app_id" value="<?= (int) ($profile_application->appID ?? 0); ?>">
                                                            <input type="hidden" name="profile_job_id" value="<?= (int) ($profile_application->jobID ?? 0); ?>">

                                                            <div class="form-row">
                                                                <div class="form-group col-md-6">
                                                                    <label class="font-weight-semibold">Company / Office <span class="text-danger">*</span></label>
                                                                    <textarea name="title" id="experience_edit_company" class="form-control" rows="3" required></textarea>
                                                                </div>
                                                                <div class="form-group col-md-6">
                                                                    <label class="font-weight-semibold">Job Title / Position Held <span class="text-danger">*</span></label>
                                                                    <input name="position_title" id="experience_edit_job" type="text" maxlength="255" class="form-control" required>
                                                                    <small class="form-text text-muted">Use the title shown on the service record.</small>
                                                                </div>
                                                            </div>
                                                            <div class="form-row">
                                                                <div class="form-group col-md-6">
                                                                    <label class="font-weight-semibold">From <span class="text-danger">*</span></label>
                                                                    <input name="date_from" type="date" id="experience_edit_from" class="form-control js-range-from" data-range="editexperience" required>
                                                                </div>
                                                                <div class="form-group col-md-6">
                                                                    <label class="font-weight-semibold">To <span class="text-danger">*</span></label>
                                                                    <input name="date_to" type="date" id="experience_edit_to" class="form-control js-range-to" data-range="editexperience" required>
                                                                </div>
                                                            </div>
                                                            <div class="text-danger small mb-2 js-range-warning" data-for="editexperience" style="display:none;"></div>
                                                            <div class="alert alert-secondary py-2 mb-0">
                                                                <i class="mdi mdi-calendar-clock mr-1"></i>Total experience: <strong class="js-xp-preview" data-for="editexperience">&mdash;</strong>
                                                                <br><small class="text-muted">Automatically recalculated from the inclusive dates.</small>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-primary waves-effect waves-light">Save Changes</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal fade training-edit" tabindex="-1" role="dialog" aria-labelledby="trainingEditLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                                <div class="modal-content">
                                                    <?= form_open('Page/update_trainings_staff', array('class' => 'parsley-examples')); ?>
                                                        <div class="modal-header bg-info text-white">
                                                            <h5 class="modal-title" id="trainingEditLabel"><i class="mdi mdi-school-outline mr-1"></i>Edit Training / Seminar</h5>
                                                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <input type="hidden" value="<?= (int) $this->uri->segment(2); ?>" name="id_number">
                                                            <input type="hidden" name="id" value="" id="training_edit_id">
                                                            <input type="hidden" name="profile_app_id" value="<?= (int) ($profile_application->appID ?? 0); ?>">
                                                            <input type="hidden" name="profile_job_id" value="<?= (int) ($profile_application->jobID ?? 0); ?>">

                                                            <div class="form-group">
                                                                <label class="font-weight-semibold">Training Title <span class="text-danger">*</span></label>
                                                                <textarea name="trainingTitle" id="training_edit_title" class="form-control" rows="3" required></textarea>
                                                            </div>
                                                            <div class="form-row">
                                                                <div class="form-group col-md-6">
                                                                    <label class="font-weight-semibold">From <span class="text-danger">*</span></label>
                                                                    <input name="dateStarted" type="datetime-local" id="training_edit_from" class="form-control js-range-from" data-range="edittraining" required>
                                                                </div>
                                                                <div class="form-group col-md-6">
                                                                    <label class="font-weight-semibold">To <span class="text-danger">*</span></label>
                                                                    <input name="dateFinished" type="datetime-local" id="training_edit_to" class="form-control js-range-to" data-range="edittraining" required>
                                                                </div>
                                                            </div>
                                                            <div class="text-danger small mb-2 js-range-warning" data-for="edittraining" style="display:none;"></div>
                                                            <div class="form-group mb-0">
                                                                <label class="font-weight-semibold">Total Relevant Hours <span class="text-danger">*</span></label>
                                                                <input name="nh" type="number" id="training_edit_hours" min="0" step="0.01" class="form-control js-hours-total" data-for="edittraining" required>
                                                                <small class="form-text text-muted">Recalculated when the date range changes; you may correct the credited total before saving.</small>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-primary waves-effect waves-light">Save Changes</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>


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

                                                // Fill the consolidated experience editor from its record button.
                                                document.addEventListener('click', function (e) {
                                                    var trigger = e.target && e.target.closest ? e.target.closest('.open-experience-edit') : null;
                                                    if (!trigger) return;

                                                    document.getElementById('experience_edit_id').value = trigger.getAttribute('data-id') || '';
                                                    document.getElementById('experience_edit_company').value = trigger.getAttribute('data-company') || '';
                                                    document.getElementById('experience_edit_job').value = trigger.getAttribute('data-job') || '';
                                                    document.getElementById('experience_edit_from').value = trigger.getAttribute('data-from') || '';
                                                    document.getElementById('experience_edit_to').value = trigger.getAttribute('data-to') || '';

                                                    refreshRange('editexperience');
                                                });

                                                // Fill title, inclusive date/time and credited hours together.
                                                document.addEventListener('click', function (e) {
                                                    var trigger = e.target && e.target.closest ? e.target.closest('.open-training-edit') : null;
                                                    if (!trigger) return;

                                                    document.getElementById('training_edit_id').value = trigger.getAttribute('data-id') || '';
                                                    document.getElementById('training_edit_title').value = trigger.getAttribute('data-title') || '';
                                                    document.getElementById('training_edit_from').value = trigger.getAttribute('data-from') || '';
                                                    document.getElementById('training_edit_to').value = trigger.getAttribute('data-to') || '';
                                                    refreshRange('edittraining');
                                                    // Keep the stored credited value authoritative until a date is changed.
                                                    document.getElementById('training_edit_hours').value = trigger.getAttribute('data-hours') || '';
                                                });

                                                function relevanceStatus(value) {
                                                    var parts = String(value || '').split(':');
                                                    return parts.length === 4 ? parts[3] : '0';
                                                }

                                                function paintRelevance(select, stat) {
                                                    select.classList.remove('relevance-status-0', 'relevance-status-1', 'relevance-status-2');
                                                    select.classList.add('relevance-status-' + stat);
                                                }

                                                function safeMarkup(value) {
                                                    var element = document.createElement('span');
                                                    element.textContent = String(value || '');
                                                    return element.innerHTML;
                                                }

                                                function showRelevanceMessage(data, success) {
                                                    var message = data && data.message
                                                        ? data.message
                                                        : 'The relevance change could not be saved. Your previous selection was restored.';
                                                    var total = data && data.total
                                                        ? '<div class="relevance-feedback-total">' + safeMarkup(data.total) + '</div>'
                                                        : '';

                                                    if (window.Swal && typeof window.Swal.fire === 'function') {
                                                        window.Swal.fire({
                                                            title: safeMarkup((data && data.title) || (success ? 'Relevance updated' : 'Unable to save relevance')),
                                                            html: '<div class="relevance-feedback-copy">' + safeMarkup(message) + total + '</div>',
                                                            type: success ? 'success' : 'error',
                                                            width: 410,
                                                            padding: '1.2rem',
                                                            customClass: 'relevance-feedback-modal',
                                                            showCloseButton: true,
                                                            confirmButtonText: 'OK',
                                                            confirmButtonColor: '#348cd4'
                                                        });
                                                    } else {
                                                        window.alert(message + (data && data.total ? '\n\n' + data.total : ''));
                                                    }
                                                }

                                                // Relevance saves as soon as an evaluator changes the selection.
                                                document.addEventListener('change', function (e) {
                                                    var select = e.target;
                                                    if (!select || !select.classList || !select.classList.contains('js-relevance-select')) return;

                                                    var form = select.closest('.js-relevance-form');
                                                    if (!form || form.classList.contains('is-saving')) return;

                                                    var previous = select.getAttribute('data-previous-value') || select.value;
                                                    var formData = new FormData(form);
                                                    form.classList.add('is-saving');
                                                    select.disabled = true;

                                                    if (!window.fetch) {
                                                        select.disabled = false;
                                                        form.submit();
                                                        return;
                                                    }

                                                    window.fetch(form.action, {
                                                        method: 'POST',
                                                        body: formData,
                                                        credentials: 'same-origin',
                                                        headers: {
                                                            'X-Requested-With': 'XMLHttpRequest',
                                                            'Accept': 'application/json'
                                                        }
                                                    }).then(function (response) {
                                                        return response.json();
                                                    }).then(function (data) {
                                                        if (!data || !data.success) {
                                                            var rejected = new Error(data && data.message ? data.message : 'Save failed.');
                                                            rejected.responseData = data;
                                                            throw rejected;
                                                        }

                                                        select.setAttribute('data-previous-value', select.value);
                                                        paintRelevance(select, String(data.stat));

                                                        var total = document.querySelector('[data-relevance-total="' + data.record_type + '"]');
                                                        if (total && data.total_value) total.textContent = data.total_value;

                                                        showRelevanceMessage(data, true);
                                                    }).catch(function (error) {
                                                        select.value = previous;
                                                        paintRelevance(select, relevanceStatus(previous));
                                                        showRelevanceMessage(error.responseData || {
                                                            title: 'Unable to save relevance',
                                                            message: 'The relevance change could not be saved. Your previous selection was restored. Please check your connection and try again.'
                                                        }, false);
                                                    }).then(function () {
                                                        select.disabled = false;
                                                        form.classList.remove('is-saving');
                                                    });
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
