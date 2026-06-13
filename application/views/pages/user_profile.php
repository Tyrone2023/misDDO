<!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->

            <div class="content-page">
                <div class="content">
                   

                    <!-- Start Content-->
                    <div class="container-fluid">

                        <!-- start page title -->
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="profile-bg-picture" style="background-image:url('<?= base_url(); ?>assets/images/bg-profile.jpg')">
                                    <span class="picture-bg-overlay"></span>
                                    <!-- overlay -->
                                </div>
                                <!-- meta -->
                                <div class="profile-user-box">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="profile-user-img"><img src="<?= base_url(); ?>./uploads/members/<?php if($image == ""){echo "icon/avatar-1.jpg";}else{echo $image;}?>"
                                             alt="" class="avatar-lg rounded-circle"></div>
                                            <div class="">
                                                <h4 class="mt-5 font-18 ellipsis"><?= $fname.' '.$mname.' '.$lname; ?></h4>
                                                <p class="font-13"><?= $Position; ?></p>
                                                <p class="text-muted mb-0"><small><?= $add; ?></small></p>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="text-right">

                                                <a href="<?= base_url(); ?>termmembers_add/<?= $id; ?>" class="btn btn-success waves-effect waves-light">
                                                    <i class="mdi mdi-account-settings-variant mr-1"></i> Add as Active Member
                                                </a>


                                                <a href="<?= base_url(); ?>edit_prof/<?= $id; ?>" class="btn btn-success waves-effect waves-light">
                                                    <i class="mdi mdi-account-settings-variant mr-1"></i> Edit Profile
                                                </a>
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
                                        <ul class=" nav nav-tabs tabs-bordered nav-justified">
                                            <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#aboutme">About</a></li>
                                            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#user-activities">Other Info</a></li>
                                            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#edit-profile">Settings</a></li>
                                            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#res">Resolutions</a></li>
                                            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#or">Ordinances</a></li>
                                        </ul>

                                        <div class="tab-content m-0 p-4">

                                            <div id="aboutme" class="tab-pane active">
                                                <div class="profile-desk">
                                                    <h5 class="text-uppercase font-weight-bold"><?= $fname.' '.$mname.' '.$lname; ?></h5>
                                                    <div class="designation mb-4"><?= $Position; ?></div>
               
                                                    <h5 class="mt-4">Information</h5>
                                                    <table class="table table-condensed mb-0">
                                                        
                                                            <tbody>
                                                                <tr>
                                                                    <th scope="row">Birth Date</th>
                                                                    <td><?= $BDate; ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <th scope="row">Age</th>
                                                                    <td><?= $Age; ?></td>
                                                                </tr>

                                                                <tr>
                                                                    <th scope="row">Sex</th>
                                                                    <td class="ng-binding"><?= $Sex; ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <th scope="row">Civil Status</th>
                                                                    <td><?= $CivilStatus; ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <th scope="row">Eligibility</th>
                                                                    <td><?= $Eligibility; ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <th scope="row">Appointment Status</th>
                                                                    <td><?= $AppStatus; ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <th scope="row">Contact Number</th>
                                                                    <td><?= $ContactNos; ?></td>
                                                                </tr>
                                                                
                                                            </tbody>
                                                        </table>
                                                    </div> <!-- end profile-desk -->
                                                </div> <!-- about-me -->

                                                <!-- Activities -->
                                                <div id="user-activities" class="tab-pane">
                                                <table class="table table-condensed mb-0">
                                                        
                                                        <tbody>
                                                            <tr>
                                                                <th scope="row">Educucational Attainment</th>
                                                                <td><?= $EducAttainment; ?></td>
                                                            </tr>
                                                            <tr>
                                                                <th scope="row">District</th>
                                                                <td><?= $District; ?></td>
                                                            </tr>

                                                            <tr>
                                                                <th scope="row">CTC</th>
                                                                <td class="ng-binding"><?= $CTC; ?></td>
                                                            </tr>
                                                            <tr>
                                                                <th scope="row">IssuanceDate</th>
                                                                <td><?= $IssuanceDate; ?></td>
                                                            </tr>
                                                            <tr>
                                                                <th scope="row">IssuancePlace</th>
                                                                <td><?= $IssuancePlace; ?></td>
                                                            </tr>
                                                            <tr>
                                                                <th scope="row">TIN</th>
                                                                <td><?= $TIN; ?></td>
                                                            </tr>
                                                            <tr>
                                                                <th scope="row">PhilHealth</th>
                                                                <td><?= $PhilHealth; ?></td>
                                                            </tr>
                                                            <tr>
                                                                <th scope="row">Pagibig</th>
                                                                <td><?= $Pagibig; ?></td>
                                                            </tr>
                                                            <tr>
                                                                <th scope="row">SSS</th>
                                                                <td><?= $SSS; ?></td>
                                                            </tr>
                                                            <tr>
                                                                <th scope="row">GSIS</th>
                                                                <td><?= $GSIS; ?></td>
                                                            </tr>
                                                            <tr>
                                                                <th scope="row">Political Party</th>
                                                                <td><?= $PoliticalParty; ?></td>
                                                            </tr>
                                                            <tr>
                                                                <th scope="row">PhilHealth</th>
                                                                <td><?= $PhilHealth; ?></td>
                                                            </tr>
                                                            <tr>
                                                                <th scope="row">Notes</th>
                                                                <td><?= $Notes; ?></td>
                                                            </tr>
                                                            
                                                        </tbody>
                                                    </table>   
                                                    
                                                </div>

                                                <!-- settings -->
                                                <div id="edit-profile" class="tab-pane">
                                                    <div class="user-profile-content">
                                                    <?= form_open_multipart('upload_member_profile/'. $id); ?>
                                                            <div class="form-group">
                                                                <label for="image">Profile Picture</label>
                                                                <input type="file" required value="" name="image" id="image" class="form-control">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="FirstName">First Name</label>
                                                                <input type="text" value="<?= $fname; ?>" name="FirstName" id="FirstName" class="form-control">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="MiddleName">Middle Name</label>
                                                                <input type="text" value="<?= $mname; ?>" name="MiddleName" id="MiddleName" class="form-control">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="image">Last Name</label>
                                                                <input type="text" value="<?= $lname; ?>" name="LastName" id="LastName" class="form-control">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="image">Address</label>
                                                                <input type="text" value="<?= $add; ?>" name="Address" id="Address" class="form-control">
                                                                <input type="hidden" value="<?= $id; ?>" name="id">
                                                            </div>
                                                            
                                                            <button class="btn btn-primary" type="submit">Update Info</button>
                                                        </form>
                                                    </div>
                                                </div>

                                                <!-- profile -->
                                                <div id="res" class="tab-pane">
                                                    <div class="row m-t-10">
                                                        <div class="col-md-12">
                                                            <div class="portlet"><!-- /primary heading -->
                                                                <div id="portlet2" class="panel-collapse collapse show">
                                                                    <div class="portlet-body">
                                                                        <div class="table-responsive">
                                                                            
                                                                            <table class="table mb-0">
                                                                                <thead>
                                                                                    <tr>
                                                                                        <th>Resolution NO.</th>
                                                                                        <th>Title</th>
                                                                                        <th>Category</th>
                                                                                        <th>Effectivity</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    <?php 
                                                                                        //$res = $this->Page_model->get_mult_where_posts('resolutions','Author',$full);
                                                                                        foreach($res as $r){ 
                                                                                    ?>
                                                                                    <tr>
                                                                                        <td><?= $r['ResolutionNo']; ?></td>
                                                                                        <td><?= $r['Title']; ?></td>
                                                                                        <td><?= $r['Category']; ?></td>
                                                                                        <td><?= $r['Effectivity']; ?></td>
                                                                                    </tr>
                                                                                    
                                                                                    <?php } ?>
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div> <!-- /Portlet -->
                                                        </div>
                                                    </div>
                                                </div>

                                                <div id="or" class="tab-pane">
                                                    <div class="row m-t-10">
                                                        <div class="col-md-12">
                                                            <div class="portlet"><!-- /primary heading -->
                                                                <div id="portlet2" class="panel-collapse collapse show">
                                                                    <div class="portlet-body">
                                                                        <div class="table-responsive">
                                                                            
                                                                        <table class="table mb-0">
                                                                                <thead>
                                                                                    <tr>
                                                                                        <th>Resolution NO.</th>
                                                                                        <th>Title</th>
                                                                                        <th>Category</th>
                                                                                        <th>Effectivity</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    <?php 
                                                                                        //$or = $this->Page_model->get_mult_where_posts('ordinances','Author',$full);
                                                                                        foreach($or as $o){ 
                                                                                    ?>
                                                                                    <tr>
                                                                                        <td><?= $o['OrdinanceNo']; ?></td>
                                                                                        <td><?= $o['Title']; ?></td>
                                                                                        <td><?= $o['Category']; ?></td>
                                                                                        <td><?= $o['Effectivity']; ?></td>
                                                                                    </tr>
                                                                                    
                                                                                    <?php } ?>
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div> <!-- /Portlet -->
                                                        </div>
                                                    </div>
                                                </div>    


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

                

               