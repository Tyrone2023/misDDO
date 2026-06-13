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
                        <h4 class="page-title">Update Memo</h4>

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
                                        <?= form_open_multipart('Page/' . $add_action); ?>

                                        <div class="form-group col-md-12">
                                            <label>File</label>
                                            <input type="file" name="file" class="form-control" />
                                            <input type="hidden" name="id" value="<?= $data->id; ?>">
                                        </div>


                                        <div class="modal-footer">
                                            <input type="submit" name="submit" class="btn btn-primary waves-effect waves-light" value="Submit" />
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