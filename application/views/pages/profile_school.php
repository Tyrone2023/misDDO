<!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->

            <div class="content-page">
                <div class="content">
                   
                <?php
					foreach($data as $row)
					{
					?>

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
                                            <!-- <div class="profile-user-img"><img src="<?= base_url(); ?>uploads/users/<?php if($image == ""){echo "icon/avatar-1.jpg";}else{echo $image;}?>" alt="" class="avatar-lg rounded-circle"></div> -->
                                            <div class="">
                                                <h4 class="mt-5 font-18 ellipsis"><?php echo $row->schoolName; ?></h4>
                                                <p class="font-13"> School Address </p>
                                                <p class="text-muted mb-0">School ID: <strong>School ID</strong></p>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="text-right">
                                                <!-- <a href="<?= base_url(); ?>Pages/profile_reg_edit/<?= $id; ?>" class="btn btn-success waves-effect waves-light"><i class="mdi mdi-account-settings-variant mr-1"></i> Edit Profile</a> -->


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
                                            <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#aboutme">About</a></li>
                                            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#family">Family</a></li>
                                            <!-- <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#education">Education</a></li>
                                            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#trainings">Trainings</a></li>
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
                                                                <td><?php if(empty($jobTitle)){echo "";}else{echo $jobTitle;} ?></td>
                                                            </tr>

                                                             <tr>
                                                                <th scope="row">Position</th>
                                                                <td></td>
                                                            </tr>
                                                            <tr>
                                                                <th scope="row">Emp. Status</th>
                                                                <td></td>
                                                            </tr>

                                                            <tr>
                                                                <th scope="row">Eligibility</th>
                                                                <td></td>
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
                                                                    <td><?= $Department; ?></td>
                                                                </tr>

                                                                <tr>
                                                                    <th scope="row">Expected Ret. Year</th>
                                                                    <td><?= $retYear; ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <th scope="row">TIN</th>
                                                                    <td><?= $tinNo; ?> </td>
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
                                                                    <td class="ng-binding">  <?= $resVillage; ?>, <?= $resBarangay; ?>, <?= $resCity; ?>, <?= $resProvince; ?></td>
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


                                                
<?php } ?>

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



