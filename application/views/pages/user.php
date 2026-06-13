
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
                                            <div class="profile-user-img"><img src="<?= base_url(); ?>./uploads/users/<?php if($image == ""){
                                                if($sex == "Male"){
                                                    echo "icon/avatar-1.jpg";
                                                }else{
                                                    echo "icon/avatar-2.jpg"; 
                                                }
                                            }else{echo $image;}?>"
                                             alt="" class="avatar-lg rounded-circle"></div>
                                            <div class="">
                                                <h4 class="mt-5 font-18 ellipsis"><?= $fname.' '.$mname.' '.$lname; ?></h4>
                                                <p class="font-13"><?= $position; ?> </p>
                                                <p class="text-muted mb-0"><small><?= $address; ?></small></p>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="text-right">
                                                <a href="<?= base_url(); ?>users_edit/<?= $id; ?>" class="btn btn-success waves-effect waves-light">
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
                            <?php if($this->session->flashdata('danger')) : ?>

                            <?= '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>'
                                    .$this->session->flashdata('danger'). 
                                '</div>'; 
                            ?>
                            <?php endif; ?>  
                            <?php if($this->session->flashdata('success')) : ?>

                                <?= '<div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>'
                                        .$this->session->flashdata('success'). 
                                    '</div>'; 
                                ?>
                                <?php endif; ?> 
                                <div class="card p-0">
                                    <div class="card-body p-0">
                                        <ul class=" nav nav-tabs tabs-bordered nav-justified">
                                            <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#aboutme">About</a></li>
                                            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#edit-profile">Settings</a></li>
                                        </ul>

                                        <div class="tab-content m-0 p-4">

                                            <div id="aboutme" class="tab-pane active">
                                                <div class="profile-desk">
                                                    <h5 class="text-uppercase font-weight-bold"><?= $fname.' '.$mname.' '.$lname; ?></h5>
                                                    <div class="designation mb-4"><?= $position; ?></div>
               
                                                    <h5 class="mt-4">Information</h5>
                                                        <table class="table table-condensed mb-0">
                                                        
                                                            <tbody>
                                                                <tr>
                                                                    <th scope="row">Birth Date</th>
                                                                    <td><?= $bday; ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <th scope="row">Age</th>
                                                                    <td><?= $age; ?></td>
                                                                </tr>
                                                                

                                                                <tr>
                                                                    <th scope="row">sex</th>
                                                                    <td class="ng-binding"><?= $sex; ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <th scope="row">Username</th>
                                                                    <td><?= $username; ?></td>
                                                                </tr>
                                                                
                                                            </tbody>
                                                        </table>
                                                    </div> <!-- end profile-desk -->
                                                </div> <!-- about-me -->

                                                

                                                <!-- settings -->
                                                <div id="edit-profile" class="tab-pane">
                                                    <div class="user-profile-content">
                                                        <table class="table table-condensed mb-0">
                                                        
                                                            <tbody>
                                                                <tr>
                                                                    <th scope="row">Username</th>
                                                                    <td>
                                                                        <a data-toggle="modal" data-id="<?= $id; ?>" href="#addBookDialog" class="open-AddBookDialog ng-binding text-purple mb-0"><i class=" ion ion-ios-person-add"></i>&nbsp; Changed Username</a>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <th scope="row">Password</th>
                                                                    <td><a data-toggle="modal" data-id="<?= $id; ?>"  href="#add" class="open-AddBookDialog ng-binding text-success"><i class="mdi mdi-shield-alert"></i> Changed Password</a></td>
                                                                </tr>

                                                                <tr>
                                                                    <th scope="row">User Profile</th>
                                                                    <td><a data-toggle="modal" data-id="<?= $id; ?>" href="#profileImage" class="open-AddBookDialog ng-binding text-warning"><i class="mdi mdi-face-profile"></i> Update Profile</a></td>
                                                                </tr>
                                                                
                                                            </tbody>
                                                        </table>
                                                    </div>    
                                                </div>

                                                <!-- profile -->
                                                
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

                

                <!-- Footer Start -->
                <footer class="footer">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                2015 - 2020 &copy; Velonic theme by <a href="">Coderthemes</a>
                            </div>
                        </div>
                    </div>
                </footer>
                <!-- end Footer -->

            </div>

            <!-- ============================================================== -->
            <!-- End Page content -->
            <!-- ============================================================== -->

        </div>
        <!-- END wrapper -->