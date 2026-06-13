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
                        <a href="<?= $link; ?>" class="btn btn-success waves-effect width-md waves-light"><?= $pn; ?></a>
                        <a href="<?= base_url(); ?>page/profileUploading" class="btn btn-info waves-effect width-md waves-light">Bulk Upload Profile</a>
                        <?php if($this->session->position == "asds" || $this->session->position == "Human Resource Admin" || $this->session->position == "HR Staff"){?>
                        <a href="<?= base_url(); ?>pages/employee_report" target="_blank" class="btn btn-purple waves-effect width-md waves-light">List of Employee</a>
                        <a onclick="return confirm('Are you sure?')" href="<?= base_url(); ?>pages/resetempno" class="btn btn-warning waves-effect width-md waves-light">Reset Employee No.</a>
                        <?php } ?>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body table-responsive">
                            <h4 class="header-title mb-4"><?= $title; ?></h4>

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

                            <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">

                                <thead>
                                    <tr>
                                        <th>Employee Name</th>
                                        <th>Employee No.</th>
                                        <th>Position</th>
                                        <th>Department</th>
                                        <th style="text-align: center;">Eligibility</th>
                                        <th style="text-align: center;">TIN</th>
                                        <th style="text-align: center;">Date Hired</th>
                                        <th style="text-align: center;">Last Appointment</th>
                                        <th style="text-align: center;">Expected Retirement Year</th>
                                        <th style="text-align: center;">Lenght of Service</th>
                                        <th style="text-align: center;">Civil Status</th>
                                        <th style="text-align: center;">Birth Date</th>
                                        <th style="text-align: center;">Age</th>
                                        <th>ACTION</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php foreach ($personel as $row) {
                                        $user = $this->Page_model->get_limited_col_single_col('users', 'id', 'id', $row['IDNumber']);
                                    ?>
                                        <tr <?php if ($row['currentStatus'] !== 'Active') { ?> class="inactive" <?php } ?>>

                                            <td><?= ucfirst($row['LastName']) . ', ' . ucfirst($row['FirstName']) . ' ' . ucfirst(substr($row['MiddleName'], 0, 1)); ?></td>
                                            <td><?= $row['IDNumber']; ?></td>
                                            <td><?= $row['empPosition']; ?></td>
                                            <td><?= $row['Department']; ?></td>
                                            <td><?= $row['csEligibility']; ?></td>
                                            <td><?= $row['tinNo']; ?></td>
                                            <td><?= $row['dateHired']; ?></td>
                                            <td><?= $row['lastAppointmentDate']; ?></td>
                                            <td><?= $row['retYear']; ?></td>
                                            <td><?= $row['serviceLenght']; ?></td>
                                            <td><?= $row['MaritalStatus']; ?></td>
                                            <td><?= $row['BirthDate']; ?></td>
                                            <td><?= $row['age']; ?></td>

                                            <td>
                                                <a href="personnel_profile/<?= $row['IDNumber']; ?>" class="text-success"><i class="mdi mdi-file-document-box-check-outline"></i>View</a>&nbsp;&nbsp;&nbsp;&nbsp;

                                                <a href="Pages/personel_del?id=<?= $row['IDNumber']; ?>" class="text-danger" onclick="return confirm('Are you sure?')"><i class="mdi mdi-file-document-box-check-outline"></i>Delete</a>
                                            </td>
                                        </tr>
                                    <?php } ?>
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

    <style>
        .inactive {
            background-color: #ffcccc;
        }
    </style>