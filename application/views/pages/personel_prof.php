

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
                                            <div class="profile-user-img"><img src="<?= base_url(); ?>assets/images/users/avatar-1.jpg" alt="" class="avatar-lg rounded-circle"></div>
                                            <div class="">
                                                <h4 class="mt-5 font-18 ellipsis"><?= $fname.' '.$mname.' '.$lname; ?></h4>
                                                <p class="font-13"><?= $emposition; ?></p>
                                                <p class="text-muted mb-0"><small>California, United States</small></p>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="text-right">
                                                <a href="<?= base_url(); ?>personel_edit/<?= $IDNumber; ?>" class="btn btn-success waves-effect waves-light">
                                                    <i class="mdi mdi-account-settings-variant mr-1"></i>Edit Profile
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
                                            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#user-activities">Activities</a></li>
                                            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#edit-profile">Settings</a></li>
                                            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#projects">Projects</a></li>
                                        </ul>

                                        <div class="tab-content m-0 p-4">

                                            <div id="aboutme" class="tab-pane active">
                                                <div class="profile-desk">
                                                    <h5 class="text-uppercase font-weight-bold">Johnathan Deo</h5>
                                                    <div class="designation mb-4">PRODUCT DESIGNER (UX / UI / Visual Interaction)</div>
                                                    <p class="text-muted">
                                                        I have 10 years of experience designing for the web, and specialize in the areas of user interface design, interaction design, visual design and prototyping. I’ve worked with notable startups including Pearl Street Software.
                                                    </p>
                                                    <a class="btn btn-primary mt-4" href="#"> <i class="fa fa-check"></i> Following</a>

                                                    <h5 class="mt-4">Contact Information</h5>
                                                    <table class="table table-condensed mb-0">
                                                        
                                                            <tbody>
                                                                <tr>
                                                                    <th scope="row">Url</th>
                                                                    <td>
                                                                        <a href="#" class="ng-binding">
                                                                            www.example.com
                                                                        </a>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <th scope="row">Email</th>
                                                                    <td>
                                                                        <a href="" class="ng-binding">
                                                                            jonathandeo@example.com
                                                                        </a>
                                                                    </td>
                                                                </tr>

                                                                <tr>
                                                                    <th scope="row">Phone</th>
                                                                    <td class="ng-binding">(123)-456-7890</td>
                                                                </tr>
                                                                <tr>
                                                                    <th scope="row">Skype</th>
                                                                    <td>
                                                                        <a href="#" class="ng-binding">
                                                                            jonathandeo123
                                                                        </a>
                                                                    </td>
                                                                </tr>
                                                                
                                                            </tbody>
                                                        </table>
                                                    </div> <!-- end profile-desk -->
                                                </div> <!-- about-me -->

                                                <!-- Activities -->
                                                <div id="user-activities" class="tab-pane">
                                                    <div class="timeline-2">
                                                        <div class="time-item">
                                                            <div class="item-info ml-3 mb-3">
                                                                <div class="text-muted">5 minutes ago</div>
                                                                <p><strong><a href="#" class="text-info">John Doe</a></strong> Uploaded a photo <strong>"DSC000586.jpg"</strong></p>
                                                            </div>
                                                        </div>

                                                        <div class="time-item">
                                                            <div class="item-info ml-3 mb-3">
                                                                <div class="text-muted">30 minutes ago</div>
                                                                <p><a href="" class="text-info">Lorem</a> commented your post.</p>
                                                                <p><em>"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam laoreet tellus ut tincidunt euismod. "</em></p>
                                                            </div>
                                                        </div>

                                                        <div class="time-item">
                                                            <div class="item-info ml-3 mb-3">
                                                                <div class="text-muted">59 minutes ago</div>
                                                                <p><a href="" class="text-info">Jessi</a> attended a meeting with<a href="#" class="text-success">John Doe</a>.</p>
                                                                <p><em>"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam laoreet tellus ut tincidunt euismod. "</em></p>
                                                            </div>
                                                        </div>

                                                        <div class="time-item">
                                                            <div class="item-info ml-3 mb-3">
                                                                <div class="text-muted">5 minutes ago</div>
                                                                <p><strong><a href="#" class="text-info">John Doe</a></strong>Uploaded 2 new photos</p>
                                                            </div>
                                                        </div>

                                                        <div class="time-item">
                                                            <div class="item-info ml-3 mb-3">
                                                                <div class="text-muted">30 minutes ago</div>
                                                                <p><a href="" class="text-info">Lorem</a> commented your post.</p>
                                                                <p><em>"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam laoreet tellus ut tincidunt euismod. "</em></p>
                                                            </div>
                                                        </div>

                                                        <div class="time-item">
                                                            <div class="item-info ml-3 mb-3">
                                                                <div class="text-muted">59 minutes ago</div>
                                                                <p><a href="" class="text-info">Jessi</a> attended a meeting with<a href="#" class="text-success">John Doe</a>.</p>
                                                                <p><em>"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam laoreet tellus ut tincidunt euismod. "</em></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- settings -->
                                                <div id="edit-profile" class="tab-pane">
                                                    <div class="user-profile-content">
                                                        <form>
                                                            <div class="form-group">
                                                                <label for="FullName">Full Name</label>
                                                                <input type="text" value="John Doe" id="FullName" class="form-control">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="Email">Email</label>
                                                                <input type="email" value="first.last@example.com" id="Email" class="form-control">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="Username">Username</label>
                                                                <input type="text" value="john" id="Username" class="form-control">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="Password">Password</label>
                                                                <input type="password" placeholder="6 - 15 Characters" id="Password" class="form-control">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="RePassword">Re-Password</label>
                                                                <input type="password" placeholder="6 - 15 Characters" id="RePassword" class="form-control">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="AboutMe">About Me</label>
                                                                <textarea style="height: 125px;" id="AboutMe" class="form-control">Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat.</textarea>
                                                            </div>
                                                            <button class="btn btn-primary" type="submit">Save</button>
                                                        </form>
                                                    </div>
                                                </div>

                                                <!-- profile -->
                                                <div id="projects" class="tab-pane">
                                                    <div class="row m-t-10">
                                                        <div class="col-md-12">
                                                            <div class="portlet"><!-- /primary heading -->
                                                                <div id="portlet2" class="panel-collapse collapse show">
                                                                    <div class="portlet-body">
                                                                        <div class="table-responsive">
                                                                            <table class="table mb-0">
                                                                                <thead>
                                                                                    <tr>
                                                                                        <th>#</th>
                                                                                        <th>Project Name</th>
                                                                                        <th>Start Date</th>
                                                                                        <th>Due Date</th>
                                                                                        <th>Status</th>
                                                                                        <th>Assign</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <td>1</td>
                                                                                        <td>Velonic Admin</td>
                                                                                        <td>01/01/2015</td>
                                                                                        <td>07/05/2015</td>
                                                                                        <td><span class="badge badge-info">Work in Progress</span></td>
                                                                                        <td>Coderthemes</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>2</td>
                                                                                        <td>Velonic Frontend</td>
                                                                                        <td>01/01/2015</td>
                                                                                        <td>07/05/2015</td>
                                                                                        <td><span class="badge badge-success">Pending</span></td>
                                                                                        <td>Coderthemes</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>3</td>
                                                                                        <td>Velonic Admin</td>
                                                                                        <td>01/01/2015</td>
                                                                                        <td>07/05/2015</td>
                                                                                        <td><span class="badge badge-pink">Done</span></td>
                                                                                        <td>Coderthemes</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>4</td>
                                                                                        <td>Velonic Frontend</td>
                                                                                        <td>01/01/2015</td>
                                                                                        <td>07/05/2015</td>
                                                                                        <td><span class="badge badge-purple">Work in Progress</span></td>
                                                                                        <td>Coderthemes</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>5</td>
                                                                                        <td>Velonic Admin</td>
                                                                                        <td>01/01/2015</td>
                                                                                        <td>07/05/2015</td>
                                                                                        <td><span class="badge badge-warning">Coming soon</span></td>
                                                                                        <td>Coderthemes</td>
                                                                                    </tr>

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

                
