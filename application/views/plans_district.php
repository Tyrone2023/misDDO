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
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body table-responsive">
                                    <h4 class="header-title mb-4">SCHOOL IMPROVEMENT PLAN (SIP)</h4><br />
                                    <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>School Name</th>
                                                <th>School ID</th>
                                                <th style='text-align:center'>Manage</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($data as $row){?>
                                         <tr>
                                            <td><?= strtoupper($row->schoolName); ?></td>
                                            <td><?= $row->schoolID; ?> </td>
                                            <td><a href="<?= base_url(); ?>Page/view_plans_district_school/<?= $row->schoolID; ?>" class="btn btn-primary waves-effect waves-light btn-sm">View Plans</a></td>
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
       
 