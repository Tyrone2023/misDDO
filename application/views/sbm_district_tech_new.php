
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
                                            <li class="breadcrumb-item">SGOD Management System v1.0</li>
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


                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="card">
                                                    <div class="card-body">

                                                        <?= form_open(base_url().'Page/sbm_district_tech_new'); ?>
                                                            <div class="row">
                                                                <div class="form-group col-lg-4">
                                                                    <label for="userName">TA Recommendation</label>
                                                                    <textarea class="form-control" rows="2" name="ta_rec" id="example-textarea"></textarea>
                                                                </div>
                                                                <div class="form-group col-lg-8">
                                                                    <label for="emailAddress">Strategies/Activities</label>
                                                                    <textarea class="form-control" rows="2" name="sa" id="example-textarea"></textarea>
                                                                </div>
                                                            </div>

                                                            <div class="row">
                                                                <div class="form-group col-lg-4">
                                                                    <label for="userName">Concerned: Districts/SDO</label>
                                                                    <textarea class="form-control" rows="2" name="cd" id="example-textarea"></textarea>
                                                                </div>
                                                                <div class="form-group col-lg-8">
                                                                    <label for="emailAddress">Management Team District/SDO</label>
                                                                    <textarea class="form-control" rows="2" name="mtd" id="example-textarea"></textarea>
                                                                </div>
                                                            </div>

                                                            <div class="row">
                                                                <div class="form-group col-lg-4">
                                                                    <label for="userName">Schedule</label>
                                                                    <input type="text" name="schedule" parsley-trigger="change"  class="form-control">
                                                                </div>
                                                                <div class="form-group col-lg-8">
                                                                    <label for="emailAddress">Composite Team</label>
                                                                    <input type="text" name="ct" parsley-trigger="change"  class="form-control">
                                                                </div>
                                                            </div>



                                                            <div class="form-group text-right mb-0">
                                                                <input type="submit" name="submit" value="Submit" class="btn btn-primary waves-effect waves-light mr-1">
                                                            </div>

                                                        </form>
                                                    </div>
                                                </div>
                                                <!-- end card -->
                                            </div>
                                            <!-- end col -->
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


        <!-- Required datatable js -->
        <script src="<?= base_url(); ?>assets/libs/datatables/jquery.dataTables.min.js"></script>
        <script src="<?= base_url(); ?>assets/libs/datatables/dataTables.bootstrap4.min.js"></script>
        <!-- Buttons examples -->
        <script src="<?= base_url(); ?>assets/libs/datatables/dataTables.buttons.min.js"></script>
        <script src="<?= base_url(); ?>assets/libs/datatables/buttons.bootstrap4.min.js"></script>
        <script src="<?= base_url(); ?>assets/libs/jszip/jszip.min.js"></script>
        <script src="<?= base_url(); ?>assets/libs/pdfmake/pdfmake.min.js"></script>
        <script src="<?= base_url(); ?>assets/libs/pdfmake/vfs_fonts.js"></script>
        <script src="<?= base_url(); ?>assets/libs/datatables/buttons.html5.min.js"></script>
        <script src="<?= base_url(); ?>assets/libs/datatables/buttons.print.min.js"></script>

        <!-- Responsive examples -->
        <script src="<?= base_url(); ?>assets/libs/datatables/dataTables.responsive.min.js"></script>
        <script src="<?= base_url(); ?>assets/libs/datatables/responsive.bootstrap4.min.js"></script>

        <script src="<?= base_url(); ?>assets/libs/datatables/dataTables.keyTable.min.js"></script>
        <script src="<?= base_url(); ?>assets/libs/datatables/dataTables.select.min.js"></script>

        <!-- Datatables init -->
        <script src="<?= base_url(); ?>assets/js/pages/datatables.init.js"></script>


     <script src="<?= base_url(); ?>assets/libs/custombox/custombox.min.js"></script>


    </body>
</html>