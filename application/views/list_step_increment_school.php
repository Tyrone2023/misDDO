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
                                    <!-- <a href="<?= $link; ?>" class="btn btn-success waves-effect width-md waves-light"><?= $pn; ?></a> -->
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body table-responsive">
                                        <h4 class="header-title mb-4">List of Personnel <br /><span class="float-left badge badge-primary inline mt-2">For Step Increment</span></h4><br />
                                        <table class="table table-striped mb-0">
                                            <thead>
                                                <tr>
                                                    <th style='text-align:center'>Step</th>
                                                    <th style='text-align:center'>Counts</th>
                                                    <th style='text-align:center'>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td style='text-align:center'>Step 2</td>
                                                    <td style='text-align:center'><?= number_format($data[0]->counts,0); ?></td>
                                                    <td style='text-align:center'> <a href="<?=base_url(); ?>Page/step_increment_list_school?id=3" class="text-success"><i class="mdi mdi-file-document-box-check-outline"></i>View List</a></td>
                                                </tr>
                                                <tr>
                                                    <td style='text-align:center'>Step 3</td>
                                                    <td style='text-align:center'><?= number_format($data1[0]->counts,0); ?></td>
                                                    <td style='text-align:center'> <a href="<?=base_url(); ?>Page/step_increment_list_school?id=6" class="text-success"><i class="mdi mdi-file-document-box-check-outline"></i>View List</a></td>
                                                </tr>
                                                <tr>
                                                    <td style='text-align:center'>Step 4</td>
                                                    <td style='text-align:center'><?= number_format($data2[0]->counts,0); ?></td>
                                                    <td style='text-align:center'><a href="<?=base_url(); ?>Page/step_increment_list_school?id=9" class="text-success"><i class="mdi mdi-file-document-box-check-outline"></i>View List</a></td>
                                                </tr>
                                                <tr>
                                                    <td style='text-align:center'>Step 5</td>
                                                    <td style='text-align:center'><?= number_format($data3[0]->counts,0); ?></td>
                                                    <td style='text-align:center'><a href="<?=base_url(); ?>Page/step_increment_list_school?id=12" class="text-success"><i class="mdi mdi-file-document-box-check-outline"></i>View List</a></td>
                                                </tr>
                                                <tr>
                                                    <td style='text-align:center'>Step 6</td>
                                                    <td style='text-align:center'><?= number_format($data4[0]->counts,0); ?></td>
                                                    <td style='text-align:center'><a href="<?=base_url(); ?>Page/step_increment_list_school?id=15" class="text-success"><i class="mdi mdi-file-document-box-check-outline"></i>View List</a></td>
                                                </tr>
                                                <tr>
                                                    <td style='text-align:center'>Step 7</td>
                                                    <td style='text-align:center'><?= number_format($data5[0]->counts,0); ?></td>
                                                    <td style='text-align:center'><a href="<?=base_url(); ?>Page/step_increment_list_school?id=18" class="text-success"><i class="mdi mdi-file-document-box-check-outline"></i>View List</a></td>
                                                </tr>
                                                <tr>
                                                    <td style='text-align:center'>Step 8</td>
                                                    <td style='text-align:center'><?= number_format($data6[0]->counts,0); ?></td>
                                                    <td style='text-align:center'><a href="<?=base_url(); ?>Page/step_increment_list_school?id=21" class="text-success"><i class="mdi mdi-file-document-box-check-outline"></i>View List</a></td>
                                                </tr>
                                              
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