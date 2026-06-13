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
                            <div class="col-12">
                                <div class="page-title-box">
                                    <a class="btn btn-success" href="<?= base_url(); ?>Pages/district_report" target="_blank">View List</a>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body table-responsive">
                                    <h4 class="header-title mb-4">School List</h4>
                                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">

                                        <thead>
                                            <tr>
                                                <th>School Name</th>
                                                <th>School ID</th>
                                                <th>School Head</th>
                                                <th>School Head Designation</th>
                                                <th>Manage</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($school as $row){ ?>
                                              <tr>
                                                <td><?= $row->schoolName; ?></td>
                                                <td><?= $row->schoolID; ?></td>
                                                <td><?= $row->adminFName.' '.$row->adminMName.''.$row->adminLName.''; ?></td>
                                                <td><?= $row->adminDesignation; ?></td>
                                                <td><a class="btn btn-primary waves-effect waves-light btn-sm" href="<?= base_url(); ?>Page/employee_list/<?= $row->schoolID; ?>">Employee List</a></td>
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

                <?php include('templates/footer.php'); ?>       

             
 