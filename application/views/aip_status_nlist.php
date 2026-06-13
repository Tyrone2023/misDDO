
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
                                    <h4 class="page-title" id="myLargeModalLabel"> 
                                        
                                    </h4>
                                    <!-- <div class="page-title-right">
                                        <ol class="breadcrumb p-0 m-0">
                                            <li class="breadcrumb-item"><a href="<?= base_url(); ?>Page/aip_stat_notsub">Submitted</a></li>
                                            <li class="breadcrumb-item active">Not Submitted</li>
                                        </ol>
                                    </div> -->
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->


                        
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body">

                                    <h4 class="header-title mb-4"><?= $title; ?></h4>
                                        <div class="table-responsive">
                                            <table class="table mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>School Name</th>
                                                        <th>Distric</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php 
                                                    $no = 1;
                                                    foreach($data as $row){
                                                    $school = $this->SGODModel->two_cond_row('sgod_aip_submit', 'school_id', $row->schoolID,'fy',$fy);

                                                    if(empty($school->school_id)){
                                                    ?>
                                                    
                                                    <tr>
                                                        <th scope="row"><?= $no++; ?></th>
                                                        <td><?= $row->schoolName; ?></td>
                                                        <td><?= $row->district; ?></td>
                                                    </tr>
                                                    <?php }} ?>
                                                </tbody>
                                            </table>
                                        </div>



                                    
                                    </div>
                                </div>

                            </div>

                        </div>
                        <!--- end row -->

                    </div>
                    <!-- end container-fluid -->

                </div>
                <!-- end content -->

                
                                        


            <!-- ============================================================== -->
            <!-- End Page content -->
            <!-- ============================================================== -->

             <!-- Footer Start -->
             <?php include('includes/footer.php'); ?>
            <!-- end Footer -->

        </div>
        <!-- END wrapper -->

        
    

        <!-- Vendor js -->
        <script src="<?= base_url(); ?>assets/js/vendor.min.js"></script>

        <!-- App js -->
        <script src="<?= base_url(); ?>assets/js/app.min.js"></script>


 


    </body>
</html>