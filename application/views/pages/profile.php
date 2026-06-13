<!-- ============================================================== -->
<!-- Start Page Content here -->
<!-- ============================================================== -->
 <?php 
    $setting = $this->Common->one_cond_row('settings','id',11); 
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
                                <div class="profile-user-img">
                                    <?php if ($this->session->userdata('position') === 'user') : ?>
                                        <img src="<?= base_url(); ?>uploads/profile/<?= $this->session->userdata('image'); ?>" alt="" class="avatar-lg rounded-circle">
                                    
                                        <?php elseif ($this->session->userdata('position') === 'School') : ?>
                                            <img src="<?= base_url(); ?>uploads/profile/<?= $users->image; ?>" alt="" class="avatar-lg rounded-circle">
                                    
                                            <?php elseif ($this->session->userdata('position') === 'Staff') : ?>
                                            <img src="<?= base_url(); ?>uploads/profile/<?= $users->image; ?>" alt="" class="avatar-lg rounded-circle">
                                    

                                            <?php elseif ($this->session->userdata('position') === 'Admin') : ?>
                                            <img src="<?= base_url(); ?>uploads/profile/<?= $users->image; ?>" alt="" class="avatar-lg rounded-circle">
                                    

                                    <?php else : ?>
                                        <img src="<?= base_url(); ?>uploads/profile/<?= $users->image; ?>" alt="" class="avatar-lg rounded-circle">
                                    <?php endif; ?>
                                </div>

                                <div class="">
                                    <h4 class="mt-5 font-18 ellipsis"><?= $FirstName . ' ' . $MiddleName . ' ' . $LastName; ?></h4>
                                    <p class="font-13"> <?= $empPosition; ?> </p>
                                    <p class="text-muted mb-0">Current Status: <strong><?= $currentStatus; ?></strong></p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="text-right">
                                    <div class="button-list">
                                    <a href="<?= base_url(); ?>Pages/employee_edit/<?= $id; ?>" class="btn btn-success waves-effect waves-light"><i class="mdi mdi-account-settings-variant mr-1"></i> Edit Profile</a>
                                    <button type="button" class="btn btn-primary waves-effect waves-light" data-toggle="modal" data-target="#myModal">e-Signature</button>
                                    <?php if ($this->session->userdata('position') === 'Admin') : ?>
                                        <a href="<?= base_url(); ?>Page/empServiceRecord?id=<?= $id; ?>&fname=<?= $FirstName; ?>&mname=<?= $MiddleName; ?>&lname=<?= $LastName; ?>" class="btn btn-info waves-effect waves-light"><i class="mdi mdi-account-settings-variant mr-1"></i> Service Record</a>
                                        <!-- <a data-toggle="modal_awards" data-id="<?= $id; ?>" class="open-addAwards btn btn-primary waves-effect width-md waves-light" href="#addEmployment">Deactivate</a> -->


                                    <?php elseif ($this->session->userdata('position') === 'User') : ?>
                                        <!-- <a data-toggle="modal_awards" data-id="<?= $id; ?>" class="open-addAwards btn btn-info waves-effect width-md waves-light" href="#addEmployment">Deactivate</a> -->

                                    <?php endif ?>
                                    </div>
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

                            <?php if ($this->session->flashdata('success')) : ?>

                                <?= '<div class="alert alert-success alert-dismissible fade show" role="alert">
                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>'
                                    . $this->session->flashdata('success') .
                                    '</div>';
                                ?>
                            <?php endif; ?>

                            <?php if ($this->session->flashdata('danger')) : ?>
                                <?= '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>'
                                    . $this->session->flashdata('danger') .
                                    '</div>';
                                ?>
                            <?php endif;  ?>

                            <!-- <ul class=" nav nav-tabs tabs-bordered nav-justified"> -->
                            <ul class="nav nav-pills navtab-bg nav-justified">
                                <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#aboutme">About</a></li>
                                <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#family">Family</a></li>
                                <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#education">Education</a></li>
                                <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#trainings">Trainings</a></li>
                                <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#work">Work Experience</a></li>
                                <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#awards">Awards</a></li>
                                <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#files">201 Files</a></li>
                                <!-- <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#employment">Employment</a></li> -->
                                <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#ipcr">IPCR</a></li>
                                <?php if($this->session->userdata('position') === 'asds'){ ?>
                                <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#docs">Appointment Form</a></li>
                                <?php } ?>
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
                                                            <th scope="row">Employee No.</th>
                                                            <td><?= $id; ?></td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">Current Designation</th>
                                                            <td><?= $jobTitle; ?></td>
                                                        </tr>

                                                        <tr>
                                                            <th scope="row">Current Position</th>
                                                            <td><?= $empPosition; ?></td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">Item No.</th>
                                                            <td><?= $itemNo; ?></td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">Salary Grade / Step</th>
                                                            <td><?= $sgNo . ' / ' . $stepNo; ?></td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">Annual Salary</th>
                                                            <td><?= number_format($actualSalary, 2); ?></td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">Emp. Status</th>
                                                            <td><?= $empStatus; ?></td>
                                                        </tr>



                                                    </tbody>
                                                </table>
                                            </div>

                                            <div class="col-sm-4">
                                                <!--<h5 class="mt-4">Contact Person</h5>-->
                                                <table class="table table-condensed mb-0">

                                                    <tbody>
                                                        <tr>
                                                            <th scope="row">Eligibility</th>
                                                            <td><?= $csEligibility; ?></td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">Department</th>
                                                            <td><?= $Department; ?></td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">Date Hired</th>
                                                            <td><?= $dateHired; ?></td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">Last Date of Promotion</th>
                                                            <td><?= $lastAppointmentDate; ?></td>
                                                        </tr>

                                                        <tr>
                                                            <th scope="row">Expected Ret. Year</th>
                                                            <td><?= $retYear; ?></td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">Lenght of Service (in years)</th>
                                                            <td><?= $serviceLenght; ?></td>
                                                        </tr>

                                                    </tbody>

                                                </table>
                                            </div>

                                            <div class="col-sm-4">
                                                <!--<h5 class="mt-4">Contact Person</h5>-->
                                                <table class="table table-condensed mb-0">

                                                    <tbody>
                                                        <tr>
                                                            <th scope="row">Group</th>
                                                            <td><?= $payGroup; ?></td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">Category</th>
                                                            <td><?= $payCat; ?></td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">GSIS BP No.</th>
                                                            <td><?= $gsis; ?></td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">PAG-IBIG No.</th>
                                                            <td><?= $pagibig; ?></td>
                                                        </tr>

                                                        <tr>
                                                            <th scope="row">SSS</th>
                                                            <td><?= $sssNo; ?></td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">PhilHealth No.</th>
                                                            <td><?= $philHealth; ?></td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">TIN</th>
                                                            <td><?= $tinNo; ?> </td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">LBP Account No.</th>
                                                            <td><?= $bnk_acct; ?> </td>
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
                                                            <td><?= $Sex; ?></td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">Birth Date</th>
                                                            <td><?= $BirthDate; ?></td>
                                                        </tr>

                                                        <tr>
                                                            <th scope="row">Birth Place</th>
                                                            <td><?= $BirthPlace; ?></td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">Age</th>
                                                            <td><?= $age; ?></td>
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
                                                            <td><?= $bloodType; ?></td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">Marital Status</th>
                                                            <td><?= $MaritalStatus; ?></td>
                                                        </tr>

                                                        <tr>
                                                            <th scope="row">Citizenship</th>
                                                            <td><?= $citizenship; ?></td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">Citizenship Type</th>
                                                            <td><?= $citizenshipType; ?></td>
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
                                                            <td><?= $dualCitizenship; ?></td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">Country</th>
                                                            <td><?= $citizenshipCountry; ?></td>
                                                        </tr>

                                                        <tr>
                                                            <th scope="row">Height</th>
                                                            <td><?= $height; ?></td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">Weight</th>
                                                            <td><?= $weight; ?></td>
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
                                                            <td><?= $empMobile; ?> <?= $empTelNo; ?></td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">Official Email</th>
                                                            <td><?= $empEmail; ?></td>
                                                        </tr>

                                                        <tr>
                                                            <th scope="row">Address</th>
                                                            <td class="ng-binding"> <?= $resVillage; ?>, <?= $resBarangay; ?>, <?= $resCity; ?>, <?= $resProvince; ?></td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">Facebook</th>
                                                            <td><?= $fb; ?></td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">Skype</th>
                                                            <td><?= $skype; ?></td>
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
                                                            <td><?= $contactName; ?></td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">Relationship</th>
                                                            <td><?= $contactRel; ?></td>
                                                        </tr>

                                                        <tr>
                                                            <th scope="row">Address</th>
                                                            <td class="ng-binding"><?= $contactAddress; ?></td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">Email</th>
                                                            <td><?= $contactEmail; ?></td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">Contact No.</th>
                                                            <td><?= $contactNo; ?></td>
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
                                                <h4 class="page-title">FAMILY MEMBERS</h4>
                                                <div class="page-title-right">
                                                    <ol class="breadcrumb p-0 m-0">
                                                        <li><a data-toggle="modal" data-id="<?= $id; ?>" class="open-AddBookDialog btn btn-info waves-effect width-md waves-light" href="#familymodal">Add New</a></li>
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
                                                        <?php foreach ($family as $row) {  ?>
                                                            <tr>
                                                                <td><?= $row['fullName']; ?></td>
                                                                <td><?= $row['relationship']; ?></td>
                                                                <td><?= $row['bDate']; ?></td>
                                                                <td style="text-align:center">
                                                                    <a href="<?= base_url(); ?>Pages/delete_family?famID=<?= $row['famID']; ?>&id=<?= $row['IDNumber']; ?>" class="text-danger"><i class="mdi mdi-file-document-box-check-outline"></i>Delete</a>
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
                                                        <li><a data-toggle="modal" data-id="<?= $id; ?>" class="open-AddBookDialog btn btn-info waves-effect width-md waves-light" href="#educationmodal">Add New</a></li>
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
                                                        <?php foreach ($educ as $row) {  ?>
                                                            <tr>
                                                                <td><?= $row['level']; ?></td>
                                                                <td><?= $row['schoolName']; ?></td>
                                                                <td><?= $row['course']; ?></td>
                                                                <td><?= $row['yearStarted']; ?></td>
                                                                <td><?= $row['yearEnded']; ?></td>
                                                                <td><?= $row['scholarship']; ?></td>
                                                                <td style="text-align:center">
                                                                    <a href="<?= base_url(); ?>Pages/delete_education?educID=<?= $row['educID']; ?>&id=<?= $row['IDNumber']; ?>" class="text-danger"><i class="mdi mdi-file-document-box-check-outline"></i>Delete</a>
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
                                                        <li><a data-toggle="modal" data-id="<?= $id; ?>" class="open-AddBookDialog btn btn-info waves-effect width-md waves-light" href="#trainingmodal">Add New</a></li>
                                                    </ol>
                                                </div>
                                                <div class="clearfix"></div>
                                                <br />
                                                <!-- <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;"> -->
                                                <table class="table mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th>Training Title</th>
                                                            <th>Attachment</th>
                                                            <th>Date Started</th>
                                                            <th>Date Finished</th>
                                                            <th>No. of Hours</th>
                                                            <th>Conducted By</th>
                                                            <th>Status</th>
                                                            <th style="text-align:center">Manage</th>
                                                        </tr>
                                                    </thead>

                                                    <tbody>
                                                        <?php foreach ($trainings as $row) {  ?>
                                                            <tr>
                                                                <td><?= $row->trainingTitle; ?></td>
                                                                <td><a  href="<?= base_url().'uploads/trainings_staff/'.$row->file; ?>" target="_blank" class="tooltips" data-placement="top" data-toggle="tooltip" data-original-title="View File Attachment"><i  class="fas fa-file-alt btn btn-lg text-primary"></i></a></td>
                                                                <td><?= $row->dateStarted; ?></td>
                                                                <td><?= $row->dateFinished; ?></td>
                                                                <td>
                                                                    <?php if ($this->session->position == 'asds' || $this->session->position == 'raters' || $this->session->position == 'Evaluator'){ ?>
                                                                            <a data-toggle="modal" data-id="<?= $row->trainingID; ?>" data-appid="<?= $row->noHours; ?>" class="open-AddBookDialog badge badge-primary" href=".ivan"><?= $row->noHours; ?></a>
                                                                        <?php }else{ ?>
                                                                            <?php if ($setting->status == 0){ ?>
                                                                                <a data-toggle="modal" data-id="<?= $row->trainingID; ?>" data-appid="<?= $row->noHours; ?>" class="open-AddBookDialog badge badge-primary" href=".ivan"><?= $row->noHours; ?></a>
                                                                        <?php }else{ ?>
                                                                                <span class="badge badge-success"><?= $row->noHours; ?></span>
                                                                        <?php } ?>
                                                                    <?php } ?>
                                                                </td>
                                                                <td><?= $row->sponsor; ?></td>
                                                                <td>
                                                                                        <?php if ($this->session->position == 'asds' || $this->session->position == 'raters' || $this->session->position == 'Evaluator'): ?>
                                                                                         <?php if ($row->stat == 0): ?>
                                                                                            <a class="btn btn-sm btn-success" href="<?= base_url(); ?>Page/update_cert_training_stat_staff/<?= $row->trainingID; ?>/1">Relevant</a>
                                                                                            <a class="btn btn-sm btn-secondary" href="<?= base_url(); ?>Page/update_cert_training_stat_staff/<?= $row->trainingID; ?>/2">Not Relevant</a>
                                                                                        <?php elseif ($row->stat == 1): ?>
                                                                                            <a class="btn btn-sm btn-success" href="<?= base_url(); ?>Page/update_cert_training_stat_staff/<?= $row->trainingID; ?>/2">Relevant</a>
                                                                                        <?php else: ?>
                                                                                            <a class="btn btn-sm btn-secondary" href="<?= base_url(); ?>Page/update_cert_training_stat_staff/<?= $row->trainingID; ?>/1">Not Relevant</a>
                                                                                        <?php endif; ?>


                                                                                        <?php else: ?>
                                                                                            <?php if ($row->stat == 0): ?>
                                                                                            <span class="badge badge-secondary">No Action</span>
                                                                                        <?php elseif ($row->stat == 1): ?>
                                                                                            <span class="badge badge-success">Relevant</span>
                                                                                        <?php else: ?>
                                                                                            <span class="badge badge-secondary">Relevant</span>
                                                                                        <?php endif; ?>
                                                                                        <?php endif; ?>

                                                                </td>
                                                                <td style="text-align:center">
                                                                    <a onclick="return confirm('Are you sure?')" href="<?= base_url(); ?>Page/training_delete_staff/<?= $row->trainingID; ?>" class="text-danger"><i class="mdi mdi-file-document-box-check-outline"></i>Delete</a>
                                                                </td>
                                                            </tr>
                                                        <?php } ?>
                                                                                <tr>
                                                                                    <th colspan="2">Total</th>
                                                                                    <td></td>
                                                                                    <td></td>
                                                                                    <td><span class="badge badge-purple"><?= $training_sum; ?></span></td>
                                                                                    <td></td>
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
                                <div id="awards" class="tab-pane">
                                    <!-- start page title -->
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="page-title-box">
                                                <h4 class="page-title">AWARDS AND RECOGNITIONS</h4>
                                                <div class="page-title-right">
                                                    <ol class="breadcrumb p-0 m-0">
                                                        <li><a data-toggle="modal" data-id="<?= $id; ?>" class="open-AddBookDialog btn btn-info waves-effect width-md waves-light" href="#addAwards">Add New</a></li>
                                                    </ol>
                                                </div>
                                                <div class="clearfix"></div>
                                                <br />
                                                <!-- <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;"> -->
                                                <table class="table mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th>Awards</th>
                                                            <th>Award Description</th>
                                                            <th>Awarded By</th>
                                                            <th style="text-align:center">Manage</th>
                                                        </tr>
                                                    </thead>

                                                    <tbody>
                                                        <?php foreach ($awards as $row) {  ?>
                                                            <tr>
                                                                <td><?= $row['award']; ?></td>
                                                                <td><?= $row['awardDesc']; ?></td>
                                                                <td><?= $row['awardedBy']; ?></td>
                                                                <td style="text-align:center">
                                                                    <a href="<?= base_url(); ?>Pages/delete_awards?awardsID=<?= $row['id']; ?>&id=<?= $row['IDNumber']; ?>" class="text-danger"><i class="mdi mdi-file-document-box-check-outline"></i>Delete</a>
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
                                <!-- End of Awards -->

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
                                                                                <a data-toggle="modal" data-id="<?= $users->id; ?>" class="open-AddBookDialog btn btn-info waves-effect width-md waves-light" href=".renrenguapo">Add New</a>
                                                                            <?php endif; ?>
                                                                        </li> 
                                                                        </ol>
                                                                    </div>
                                                                    <div class="clearfix"></div>
                                                                    <br />
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
                                                                                    <td>
                                                                                        <?php if ($this->session->position == 'asds' || $this->session->position == 'raters' || $this->session->position == 'Evaluator'): ?>
                                                                                         <?php if ($row->stat == 0): ?>
                                                                                            <a class="btn btn-sm btn-success" href="<?= base_url(); ?>Page/update_cert_xp_stat/<?= $row->id; ?>/1">Relevant</a>
                                                                                            <a class="btn btn-sm btn-secondary" href="<?= base_url(); ?>Page/update_cert_xp_stat/<?= $row->id; ?>/2">Not Relevant</a>
                                                                                        <?php elseif ($row->stat == 1): ?>
                                                                                            <a class="btn btn-sm btn-success" href="<?= base_url(); ?>Page/update_cert_xp_stat/<?= $row->id; ?>/2">Relevant</a>
                                                                                        <?php else: ?>
                                                                                            <a class="btn btn-sm btn-secondary" href="<?= base_url(); ?>Page/update_cert_xp_stat/<?= $row->id; ?>/1">Not Relevant</a>
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
                                                    <!-- End of work experience -->


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
                                                        <li><a data-toggle="modal" data-id="<?= $id; ?>" class="open-AddBookDialog btn btn-info waves-effect width-md waves-light" href="#addBookDialog">Add New</a></li>
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
                                                        <?php foreach ($files as $row) {  ?>
                                                            <tr>
                                                                <td><?= $row['docName']; ?></td>
                                                                <td><?= $row['dateUploaded']; ?></td>
                                                                <td style="text-align:center">
                                                                    <!-- <a href="#/<?= $row['IDNumber']; ?>" class="text-success"><i class="mdi mdi-file-document-box-check-outline"></i>View</a>&nbsp;&nbsp;&nbsp;&nbsp; -->
                                                                    <a href="<?= base_url(); ?>uploads/201files/<?= $row['fileName']; ?>" target="_blank" class="text-success"><i class="mdi mdi-file-document-box-check-outline"></i>View</a>&nbsp;&nbsp;&nbsp;&nbsp;
                                                                    <a href="<?= base_url(); ?>Pages/del_201/<?= $row['id']; ?>" class="text-danger"><i class="mdi mdi-file-document-box-check-outline"></i>Delete</a>
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

                                                        <li><a data-toggle="modal" data-id="<?= $id; ?>" class="open-AddBookDialog btn btn-info waves-effect width-md waves-light" href="#employmentmodal">Add New</a></li>
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
                                                        <?php foreach ($employment as $row) {  ?>
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
                                                                    <a href="<?= base_url(); ?>Pages/delete_employment?empID=<?= $row['empID']; ?>&id=<?= $row['IDNumber']; ?>" class="text-danger"><i class="mdi mdi-file-document-box-check-outline"></i>Delete</a>
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


                                <!-- IPCR -->
                                <div id="ipcr" class="tab-pane">
                                    <!-- <h5 class="text-uppercase font-weight-bold">201 Files</h5> -->
                                    <!-- start page title -->
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="page-title-box">
                                                <h4 class="page-title">INDIVIDUAL PERFORMANCE COMMITMENT AND REVIEW (IPCR) </h4>
                                                <div class="page-title-right">
                                                    <ol class="breadcrumb p-0 m-0">

                                                        <li><a data-toggle="modal" data-id="<?= $id; ?>" class="open-AddBookDialog btn btn-info waves-effect width-md waves-light" href="#ipcrmodal">Add New</a></li>
                                                    </ol>
                                                </div>
                                                <div class="clearfix"></div>
                                                <br />
                                                <table class="table mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th>CY</th>
                                                            <th>Rating</th>
                                                            <th>Rating Equivalent</th>
                                                            <th>File</th>
                                                            <th style="text-align:center">Manage</th>
                                                        </tr>
                                                    </thead>


                                                    <tbody>
                                                        <?php foreach ($ipcr as $row) {  ?>
                                                            <tr>
                                                                <td><?= $row['cYear']; ?></td>
                                                                <td><?= $row['aRating']; ?></td>
                                                                <td><?= $row['adRating']; ?></td>
                                                                <td><?= $row['fileName']; ?></td>
                                                                <td style="text-align:center">
                                                                    <a href="<?= base_url(); ?>uploads/ipcr/<?= $row['fileName']; ?>" target="_blank" class="text-success"><i class="mdi mdi-file-document-box-check-outline"></i>View</a>&nbsp;&nbsp;&nbsp;&nbsp;
                                                                    <a href="<?= base_url(); ?>Pages/del_ipcr/<?= $row['id']; ?>" class="text-danger"><i class="mdi mdi-file-document-box-check-outline"></i>Delete</a>
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
                                <!-- End of IPCR -->

                                <!-- 201 Files -->
                                <div id="docs" class="tab-pane">
                                    <!-- <h5 class="text-uppercase font-weight-bold">201 Files</h5> -->
                                    <!-- start page title -->
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="page-title-box">
                                                <h4 class="page-title">New Appointment Form</h4>
                                               
                                                <div class="clearfix"></div>
                                                <br />
                                                <table class="table mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th>Position</th>
                                                            <th>Appointment Form</th>
                                                        </tr>
                                                    </thead>

                                                    <tbody>
                                                        
                                                        <?php 
                                                        $hire = $this->Hiring_model->getHireWithJob($id);
                                                        foreach($hire as $row){?>
                                                        <tr>
                                                            <td><?= $row->jobTitle; ?> <?= $jobTypes[$row->job_type] ?? ''; ?></td>
                                                            <td>
                                                                <a target="_blank" href="<?= base_url(); ?>Pages/assignment_order/<?= $row->id; ?>/<?= $id; ?>" class="btn btn-sm btn-primary">Assignment Letter</a>
                                                                <a target="_blank" href="<?= base_url(); ?>Pages/assumption_order/<?= $row->id; ?>/<?= $id; ?>" class="btn btn-sm btn-success">Assumption Order</a>
                                                                <a target="_blank" href="<?= base_url(); ?>Pages/form_33b/<?= $row->id; ?>/<?= $id; ?>" class="btn btn-sm btn-purple">CS Form No. 33-B</a>
                                                            </td>
                                                        </tr>
                                                        <?php } ?>
                                                        <!-- <tr>
                                                            <td>ASSIGNMENT LETTER</td>
                                                            <td class="text-center"><a target="_blank" href="<?= base_url(); ?>Page/new_appointment/<?= $id; ?>" class="btn btn-sm btn-success">View</a></td>
                                                        </tr>
                                                        <tr>
                                                            <td>ASSIGNMENT ORDER</td>
                                                            <td class="text-center"><a target="_blank" href="<?= base_url(); ?>Page/new_appointment_order/<?= $id; ?>" class="btn btn-sm btn-success">View</a></td>
                                                        </tr>
                                                        <tr>
                                                            <td>CS Form No. 33-B </td>
                                                            <td class="text-center"><a target="_blank" href="<?= base_url(); ?>Page/new_appointment_cs/<?= $id; ?>" class="btn btn-sm btn-success">View</a></td>
                                                        </tr> -->
                                                    </tbody>
                                                </table>

                                            </div>
                                        </div>
                                    </div>
                                    <!-- end page title -->

                                </div>
                                <!-- End of 201 Files -->


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

                                        <!--  modal content -->
                                        <div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="myModalLabel">Add/Update Electronic Signature</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <?= form_open_multipart('Travel/esig'); ?>
                                                        <input type="hidden" value="<?= $id; ?>" name="staff_id">


                                                        <div class="row">
                                                            <div class="col-lg-12">
                                                                <div class="form-group">
                                                                    <label><span class="text-danger">Note: Only PNG file format is allowed. The maximum file size is 2MB, and the image dimensions must be 150px wide and 70px high.</span></label>
                                                                    <input type="file"  class="form-control" name="userfile"  size="20" />
                                                                </div>	
                                                            </div>	              
                                                        </div>

                                                        </div>
                                                        <div class="modal-footer">
                                                            <input type="submit" class="btn btn-primary waves-effect waves-light" value="Save changes" />
                                                        </div>
                                                    </form>
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
                                                            <label for="inputPassword5" class="col-md-5 col-form-label">Year and Month</label>
                                                            <div class="col-lg-7">
                                                                <input name="nh" type="text" id="appid" maxlength="11" oninput="this.value=this.value.replace(/[^0-9]/g,'')" name="assign" class="form-control" value="" required>
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
