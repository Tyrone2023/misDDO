<?php include('templates/head.php'); ?>
<link href="<?= base_url(); ?>assets/libs/custombox/custombox.min.css" rel="stylesheet" type="text/css">
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
                <div class="col-12">
                    <div class="page-title-box">
                    <a data-toggle="modal"  class="open-AddBookDialog btn btn-primary waves-effect waves-light" href="#add">Add New</a>

                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
            <!-- end page title -->

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
            <?php endif; ?>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body table-responsive">
                            <h4 class="header-title mb-4">Personnel List<br /></h4><br />
                            <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                    <tr>
                                        <th style="text-align: center;">Last Name</th>
                                        <th style="text-align: center;">First Name</th>
                                        <th style="text-align: center;">Middle Name</th>
                                        <th style="text-align: center;">Employee No.</th>
                                        <th style="text-align: center;">Position</th>
                                        <th style="text-align: center;">Salary Grade</th>
                                        <th style="text-align: center;">Step</th>
                                        <th style="text-align: center;">Item No.</th>
                                        <th style="text-align: center;">Department</th>
                                        <th style="text-align: center;">Eligibility</th>
                                        <th style="text-align: center;">TIN</th>
                                        <th style="text-align: center;">Date Hired</th>
                                        <th style="text-align: center;">Last Appointment</th>
                                        <th style="text-align: center;">Expected Retirement Year</th>
                                        <th style="text-align: center;">Lenght of Service</th>
                                        <th style="text-align: center;">Civil Status</th>
                                        <th style="text-align: center;">Birth Date</th>
                                        <th style="text-align: center;">Age</th>
                                        <th style="text-align: center;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;
                                    foreach ($data as $row) {
                                        echo "<tr>";
                                        echo "<td>" . $row->LastName . "</td>";
                                        echo "<td>" . $row->FirstName . "</td>";
                                        echo "<td>" . $row->MiddleName . "</td>";
                                        echo "<td>" . $row->IDNumber . "</td>";
                                        echo "<td>" . $row->empPosition . "</td>";
                                        echo "<td>" . $row->sgNo . "</td>";
                                        echo "<td>" . $row->stepNo . "</td>";
                                        echo "<td>" . $row->itemNo . "</td>";
                                        echo "<td>" . $row->Department . "</td>";
                                        echo "<td>" . $row->csEligibility . "</td>";
                                        echo "<td>" . $row->tinNo . "</td>";
                                        echo "<td>" . $row->dateHired . "</td>";
                                        echo "<td>" . $row->lastAppointmentDate . "</td>";
                                        echo "<td>" . $row->retYear . "</td>";
                                        echo "<td>" . $row->serviceLenght . "</td>";
                                        echo "<td>" . $row->MaritalStatus . "</td>";
                                        echo "<td>" . $row->BirthDate . "</td>";
                                        echo "<td>" . $row->age . "</td>";
                                    ?>
                                        <td><a href="<?= base_url(); ?>personnel_profile/<?= $row->IDNumber; ?>" class="text-success"><i class="mdi mdi-file-document-box-check-outline"></i>View
                                                Details</a>&nbsp;&nbsp;&nbsp;&nbsp;

                                            <!-- <a data-toggle="modal" data-id="<?= $row->IDNumber; ?>" class="open-AddBookDialog text-info" href="#change_pass_school"><i class="fas fa-user-alt"></i><span> Reset Password </span></a> -->
                                            <a class="text-info" href="<?= base_url(); ?>Pages/pass_change_in_school/<?= $row->IDNumber; ?>"><i class="fas fa-user-alt"></i><span> Reset Password </span></a>
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
            <!-- end row -->



        </div>
        <!-- end container-fluid -->

    </div>
    <!-- end content -->

    <!-- sample modal content -->
    <div id="add" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">

                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="myModalLabel">Add Employee</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    
                                                    <div class="modal-body">
                                                    <?= form_open('Page/add_employee_on_list_school/'.$this->uri->segment(3)); ?>
                                                        <div class="form-group col-md-12">
                                                            <label>Employee Number</label>
                                                            <input type="text" required value="" name="IDNumber" class="form-control"> 
                                                        </div>
                                                        <input type="hidden" name="school_id" value="<?= $this->session->username; ?>">
                                                    
                                                        <div class="modal-footer">
                                                            <button type="submit" onclick="return confirm('Are you sure you want to add this employee to this school? Please review it first before clicking the OK button.')" name="submit" class="btn btn-primary waves-effect waves-light">Save</button>
                                                        </div>
                                                </form>
                                            </div>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->

    <?php include('templates/footer.php'); ?>