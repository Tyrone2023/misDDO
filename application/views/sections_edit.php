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
                                    <h4 class="page-title">Edit Section</h4>
                                   
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">

                                        <div class="row">
                                            <div class="col-12">
                                                <div class="">
                                                <form class="parsley-examples" method="post" >
                                            
                                            <div class="form-group">
                                                <label >Section<span class="text-danger">*</span></label>
                                                <input type="text" name="sectionName" required class="form-control" value="<?= $data->sectionName; ?>">
                                            </div>
                                            <div class="form-group">
                                                <label >Section Head</label>
                                                <input type="text"  class="form-control" name="sectionHead" value="<?= $data->sectionHead; ?>">
                                            </div>
                                           

                                            <div class="form-group">
                                                <label >Position</label>
                                                <input type="text"  class="form-control" name="sectionHeadPosition" value="<?= $data->sectionHeadPosition; ?>">
                                            </div>

                                            <div class="form-group">
                                                <label>Section Group <span class="text-danger">*</span></label>
                                                <input type="text" required  class="form-control" name="secGroup" value="<?= $data->secGroup; ?>">
                                            </div>

                                            <div class="form-group">
                                                <label>Members <span class="text-danger">*</span></label>
                                                <input type="text" required  class="form-control" name="member" value="<?= $data->member; ?>">
                                                <input type="hidden" required  class="form-control" name="id" value="<?= $data->id; ?>">
                                            </div>

                                           
                                            <div class="form-group text-right mb-0">
                                               <input type="submit" name="submit" value="Submit" class="btn btn-primary waves-effect waves-light mr-1">
                                                
                                               
                                            </div>

                                        </form>
                                                </div>
                                            </div>

                                        </div>
                                        <!-- end row -->

                                    </div>
                                </div>
                                <!-- end card -->
                            </div>
                            <!-- end col -->
                        </div>
                        <!-- end row -->

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

     <script src="<?= base_url(); ?>assets/libs/custombox/custombox.min.js"></script>


    </body>
</html>