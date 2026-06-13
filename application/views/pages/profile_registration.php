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
                                <div class="profile-bg-picture" style="background-image:url('<?= base_url(); ?>assets/images/mis.jpg')">
                                    <span class="picture-bg-overlay"></span>
                                    <!-- overlay -->
                                </div>
                                <!-- meta -->
                                <div class="profile-user-box">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="profile-user-img"><img src="<?= base_url(); ?>uploads/users/<?php if($user->image == ""){echo "icon/avatar-1.jpg";}else{echo $image;}?>"
                                             alt="" class="avatar-lg rounded-circle"></div>
                                            <div class="">
                                                <h4 class="mt-5 font-18 ellipsis"><?= $profile->FirstName.' '.$profile->MiddleName.' '.$profile->LastName; ?></h4>
                                                <p class="font-13"></p>
                                                <p class="text-muted mb-0"><small><?= $profile->BirthDate; ?></small></p>
                                                
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="text-right">


                                                <a href="<?= base_url(); ?>edit_prof/<?= $profile->id; ?>" class="btn btn-success waves-effect waves-light">
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
                                        </ul>

                                        <div class="tab-content m-0 p-4">

                                            <div id="aboutme" class="tab-pane active">
                                                <div class="profile-desk">
               
                                                    <h5 class="mt-4">Information</h5>
                                                    <table class="table table-condensed mb-0">
                                                        
                                                            <tbody>
                                                                <tr>
                                                                    <th scope="row">Birth Date</th>
                                                                    <td><?= $profile->BirthDate; ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <th scope="row">Age</th>
                                                                    <td></td>
                                                                </tr>

                                                                <tr>
                                                                    <th scope="row">Sex</th>
                                                                    <td class="ng-binding"></td>
                                                                </tr>
                                                                <tr>
                                                                    <th scope="row">Civil Status</th>
                                                                    <td></td>
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
                                                                <td></td>
                                                            </tr>
                                                            
                                                            
                                                        </tbody>
                                                    </table>   
                                                    
                                                </div>

                                                <!-- settings -->
                                                <div id="edit-profile" class="tab-pane">
                                                    <div class="user-profile-content">
                                                    <?= form_open_multipart('upload_member_profile/'. $profile->id); ?>
                                                            <div class="form-group">
                                                                <label for="image">Profile Picture</label>
                                                                <input type="file" required value="" name="image" id="image" class="form-control">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="FirstName">First Name</label>
                                                                <input type="text" value="<?= $profile->FirstName; ?>" name="FirstName" id="FirstName" class="form-control">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="MiddleName">Middle Name</label>
                                                                <input type="text" value="<?= $profile->MiddleName; ?>" name="MiddleName" id="MiddleName" class="form-control">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="image">Last Name</label>
                                                                <input type="text" value="<?= $profile->LastName; ?>" name="LastName" id="LastName" class="form-control">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="image">Address</label>
                                                                <input type="text" value="" name="Address" id="Address" class="form-control">
                                                                <input type="hidden" value="<?= $profile->id; ?>" name="id">
                                                            </div>
                                                            
                                                            <button class="btn btn-primary" type="submit">Update Info</button>
                                                        </form>
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

                

               