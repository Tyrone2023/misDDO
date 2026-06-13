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
                                    <h4 class="header-title mb-4">List of Personnel<br /><span class="float-left badge badge-primary inline mt-2"><?= urldecode($this->uri->segment(4)); ?>, <?= urldecode($this->uri->segment(3)); ?></span></h4><br />
                                        <!-- <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;"> -->
                                        <table class="table table-sm mb-0">
                                        <thead>
                                            <tr>
                                                <th>No.</th>
                                                <th>Last Name</th>
												<th>First Name</th>
												<th>Middle Name</th>
                                                <th>Employee No.</th>
                                                <th>Position</th>
                                                <th>Department</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                                  $i=1;
                                                  foreach($data as $row)
                                                  { ?>
                                                    <tr>
                                                        <td><?= $i++; ?></td>
                                                        <td><?= $row->LastName; ?></td>
                                                        <td><?= $row->FirstName; ?></td>
                                                        <td><?= $row->MiddleName; ?></td>
                                                        <td><?= $row->IDNumber; ?></td>
                                                        <td><?= $row->empPosition; ?></td>
                                                        <td><?= $row->Department; ?></td>
                                                       
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

             
 