            <?php include('templates/head.php'); ?>
            <?php include('templates/header.php'); ?>

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
                                         
                                            <div class="">
                                                <h4 class="mt-5 font-18 ellipsis"><?php echo $data[0]->FirstName . ' ' . $data[0]->MiddleName . ' ' . $data[0]->LastName; ?></h4>
                                                <p class="font-13" style="text-transform: uppercase;">
                                                    <?php echo $data[0]->Sitio . ' ' . $data[0]->Brgy . ', ' . $data[0]->City . ', ' . $data[0]->Province; ?>
                                                </p>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="text-right">


                                                <!-- <button type="button" class="btn btn-success waves-effect waves-light">

                                                    <?php if ($this->session->userdata('level') === 'Admin') : ?>
                                                        <a href="<?= base_url(); ?>Page/updateStudeProfile?StudentNumber=<?php echo $data[0]->StudentNumber; ?>"><b><i class="far fa-edit mr-1"></i><span> Edit Profile</span></b></a>

                                                    <?php elseif ($this->session->userdata('level') === 'Registrar') : ?>
                                                        <a href="<?= base_url(); ?>Page/updateStudeProfile?StudentNumber=<?php echo $data[0]->StudentNumber; ?>"><b><i class="mdi mdi-account-settings-variant mr-1 "></i> Edit Profile</b></a>

                                                    <?php elseif ($this->session->userdata('level') === 'Student') : ?>
                                                        <a href="<?= base_url(); ?>Page/updateStudeProfile?StudentNumber=<?php echo $this->session->userdata('username'); ?>"><b><i class="mdi mdi-account-settings-variant mr-1"></i> Edit Profile</b></a>
                                                   
                                                        <?php elseif ($this->session->userdata('level') === 'School') : ?>
                                                            <a href="<?= base_url(); ?>Page/updateStudeProfile?StudentNumber=<?php echo $this->session->userdata('username'); ?>"><b><i class="mdi mdi-account-settings-variant mr-1"></i> Edit Profile</b></a>
                                                        <?php endif; ?>
                                                </button> -->


                                              

                                                

                                                <?php if ($this->session->userdata('level') === 'Student') : ?>

                                                <?php else : ?>

                                                    <!-- <a href=<?= base_url(); ?>stude_grades.php?view=<?php echo $data[0]->StudentNumber; ?> target="_blank">
                                                    <button type="button" class="btn btn-success waves-effect waves-light">Complete Grades</button>
                                                </a> -->
                                                <button type="button" class="btn btn-success waves-effect waves-light">
                                                <a href="<?= base_url(); ?>Enrollment/updateStudeProfile?StudentNumber=<?php echo $data[0]->StudentNumber; ?>"><b><i class="mdi mdi-account-settings-variant mr-1"></i> Edit Profile</b></a>
                                                </button>

                                                    <!-- <button type="button" class="btn btn-success waves-effect waves-light" data-toggle="modal" data-target="#editStudentNumberModal">
                                                        Edit Student Number
                                                    </button> -->



                                                    <?php if(!$isEnrolled): ?>
                                                    <a href="<?= base_url(); ?>Enrollment/enrollmentAcceptance?id=<?= $data[0]->StudentNumber; ?>&FName=<?= $data[0]->FirstName; ?>&MName=<?= $data[0]->MiddleName; ?>&LName=<?= $data[0]->LastName; ?>&Course=&YearLevel=&sem=&sy=">
                                                        <button type="button" class="btn btn-success waves-effect waves-light">Enroll</button>
                                                    </a>
                                                <?php endif; ?>

                                                <?php endif; ?>
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
                                            <li class="nav-item"><a class="nav-link <?php if ($this->input->post('grade') == "") {
                                                                                        echo ' active ';
                                                                                    } ?>" data-toggle="tab" href="#aboutme">About</a></li>
                                            <!-- <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#requirements">Submitted Requirements</a></li> -->

                                            <!-- <?php if ($this->session->userdata('level') !== 'Student') : ?>
                                                <li class="nav-item">
                                                    <a class="nav-link <?php if ($this->input->post('grade') != "") {
                                                                            echo ' active ';
                                                                        } ?>" data-toggle="tab" href="#grades">Grades</a>
                                                </li>
                                            <?php endif; ?> -->



                                            <!-- <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#accounthistory">Account History</a></li> -->
                                            <!-- <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#enrollmenthistory">Enrollment History</a></li> -->
                                            <!-- <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#requesthistory">Request</a></li> -->
                                        </ul>
                                        <?php echo $this->session->flashdata('msg'); ?>
                                        <div class="tab-content m-0 p-4">

                                            <div id="aboutme" class="tab-pane <?php if ($this->input->post('grade') == "") {
                                                                                    echo ' active ';
                                                                                } ?>">
                                                <div class="profile-desk">
                                                    <h4 class="mt-1 font-18 ellipsis">Student's Information</h4>
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <!--<h5 class="mt-4">Official Information</h5>-->
                                                            <table class="table table-condensed mb-0">

                                                                <tbody>
                                                                    <tr>
                                                                        <th scope="row">Student No.</th>
                                                                        <td>
                                                                            <?php echo $data[0]->StudentNumber; ?>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th scope="row">Birth Date</th>
                                                                        <td>
                                                                            <?php echo $data[0]->BirthDate; ?>
                                                                        </td>
                                                                    </tr>

                                                                    <tr>
                                                                        <th scope="row">Age</th>
                                                                        <td>
                                                                            <?php echo $data[0]->Age; ?>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th scope="row">Sex</th>
                                                                        <td>
                                                                            <?php echo $data[0]->Sex; ?>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th scope="row">Civil Status</th>
                                                                        <td>
                                                                            <?php echo $data[0]->CivilStatus; ?>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th scope="row">Mobile No.</th>
                                                                        <td>
                                                                            <?php echo $data[0]->MobileNumber; ?>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th scope="row">Email</th>
                                                                        <td>
                                                                            <?php echo $data[0]->EmailAddress; ?>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>

                                                            </table>
                                                        </div>

                                                        <div class="col-sm-6">
                                                            <!--<h5 class="mt-4">Contact Person</h5>-->
                                                            <table class="table table-condensed mb-0">

                                                                <tbody>
                                                                    <tr>
                                                                        <th scope="row">Father</th>
                                                                        <td>
                                                                            <?php echo $data[0]->Father; ?>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th scope="row">Occupation</th>
                                                                        <td>
                                                                            <?php echo $data[0]->FOccupation; ?>
                                                                        </td>
                                                                    </tr>

                                                                    <tr>
                                                                        <th scope="row">Mother</th>
                                                                        <td>
                                                                            <?php echo $data[0]->Mother; ?>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th scope="row">Occupation</th>
                                                                        <td>
                                                                            <?php echo $data[0]->MOccupation; ?>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th scope="row">Guardian</th>
                                                                        <td>
                                                                            <?php echo $data[0]->Guardian; ?>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th scope="row">Contact No.</th>
                                                                        <td>
                                                                            <?php echo $data[0]->GuardianContact; ?>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th scope="row">Address</th>
                                                                        <td>
                                                                            <?php echo $data[0]->GuardianAddress; ?>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>

                                                            </table>
                                                        </div>
                                                    </div>
                                                </div> <!-- end profile-desk -->
                                            </div> <!-- about-me -->

                                            <div id="requirements" class="tab-pane">
                                                <h4 class="mt-1 font-18 ellipsis">Submitted Requirements</h4>
                                                <table class="table mb-0">
                                                    <tbody>
                                                        <tr>
                                                            <th>File Name</td>
                                                            <th style="text-align:center;">Date Uploaded</td>
                                                            <th>
                                                                </td>
                                                        </tr>
                                                        <?php
                                                        foreach ($data2 as $row) {
                                                            echo "<tr>";
                                                        ?>
                                                            <td><a href="<?= base_url(); ?>upload/requirements/<?php echo $row->fileAttachment; ?>" target="_blank"><?php echo $row->docName; ?></a></td>
                                                            <td style="text-align:center;"><?php echo $row->dateUploaded; ?></td>
                                                            <td style="text-align:right;"><a href="<?= base_url(); ?>upload/requirements/<?php echo $row->fileAttachment; ?>" target="_blank"><button type="button" class="btn btn-primary btn-xs waves-effect waves-light">View File</button></a></td>
                                                        <?php
                                                            echo "</tr>";
                                                        }
                                                        ?>
                                                    </tbody>

                                                </table>
                                            </div>
                                            
                                            <!-- settings -->
                                            <div id="accounthistory" class="tab-pane">
                                                <h4 class="mt-1 font-18 ellipsis">Account History</h4>
                                                <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                                    <tr>
                                                        <th style="text-align: center">Semester, SY</th>
                                                        <th style="text-align: center">Total Account</th>
                                                        <th style="text-align: center">Discount</th>
                                                        <th style="text-align: center">Total Payments</th>
                                                        <th style="text-align: center">Balance</th>
                                                    </tr>
                                                    <?php
                                                    foreach ($data4 as $row) {
                                                        echo "<tr>";
                                                        echo "<td style='text-align: center'>" . $row->Semester . "</td>";
                                                        echo "<td style='text-align: center'>" . $row->AcctTotal . "</td>";
                                                        echo "<td style='text-align: center'>" . number_format($row->Discount, 2) . "</td>";
                                                    ?>
                                                        <td style="text-align: center"><a href="studepayments?studentno=<?php echo $row->StudentNumber; ?>&sy=<?php echo $row->SY; ?>&sem=<?php echo $row->Sem; ?>"><button type="button" class="btn btn-primary"><?php echo $row->TotalPayments; ?></button></a></td>
                                                    <?php
                                                        echo "<td style='text-align: center'>" . $row->CurrentBalance . "</td>";
                                                        echo "</tr>";
                                                    }
                                                    ?>
                                                </table>
                                            </div>

                                            <!-- profile -->
                                            <div id="enrollmenthistory" class="tab-pane">
                                                <h4 class="mt-1 font-18 ellipsis">Enrollment History</h4>
                                                <table class="table mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th>Semester/SY</th>
                                                            <th>Course</th>
                                                            <th>Year Level</th>

                                                        </tr>
                                                    </thead>
                                                    <tbody>


                                                        <?php
                                                        $i = 1;
                                                        foreach ($data5 as $row) {
                                                            echo "<tr>";
                                                            echo "<td>" . $row->Semester . ', SY ' . $row->SY . "</td>";
                                                            echo "<td>" . $row->Course . "</td>";
                                                            echo "<td>" . $row->YearLevel . "</td>";

                                                            echo "</tr>";
                                                        }
                                                        ?>
                                                    </tbody>

                                                </table>
                                            </div>
                                            <!-- request -->
                                            <div id="requesthistory" class="tab-pane">
                                                <h4 class="mt-1 font-18 ellipsis">Document Request History</h4>






                                                <table class="table mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th>Document Requested</th>
                                                            <th>Date</th>
                                                            <th>Tracking No.</th>
                                                            <th>Status</th>
                                                            <th>Manage</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>


                                                        <?php
                                                        $i = 1;
                                                        foreach ($data6 as $row) {

                                                            if (!$data6) {
                                                                echo "No Records Found ";
                                                            } else {

                                                                echo "<tr>";
                                                                echo "<td>" . $row->docName . "</td>";
                                                                echo "<td>" . $row->dateReq . ', SY ' . $row->timeReq . "</td>";
                                                                echo "<td>" . $row->trackingNo  . "</td>";
                                                                echo "<td>" . $row->reqStat  . "</td>";
                                                            }
                                                        ?>

                                                            <td>
                                                                <a href="<?= base_url(); ?>Page/studentRequestStat?trackingNo=<?= $row->trackingNo; ?>" class="text-primary"><i class="mdi mdi-file-document-box-check-outline"></i>Track</a>
                                                            </td>
                                                        <?php
                                                            echo "</tr>";
                                                        }
                                                        ?>
                                                    </tbody>

                                                </table>
                                            </div>

                                        </div>

                                    </div>
                                </div>
                            </div>
                            <!-- end page title -->

                        </div>
                        <!-- end row -->

                    </div>
                </div>
            </div>
            <!-- end container-fluid -->

            </div>
            <!-- end content -->

            <?php include('templates/footer.php'); ?>