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
                                <?php if($this->session->flashdata('success')) : ?>

                                    <?= '<div class="alert alert-success alert-dismissible fade show" role="alert">
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>'
                                            .$this->session->flashdata('success'). 
                                        '</div>'; 
                                    ?>
                                    <?php endif; ?>

                                    <?php if($this->session->flashdata('danger')) : ?>
                                    <?= '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>'
                                            .$this->session->flashdata('danger'). 
                                        '</div>'; 
                                    ?>
                                <?php endif;  ?>


                                    <div class="card-body table-responsive">
                                        <h4 class="header-title mb-4">UPLOAD PROFILE IMAGE</h4>
                                        <form role="form" action="<?php echo ('uploadProfPic'); ?>" enctype='multipart/form-data' method="POST">
                                            <input type="hidden" class="form-control" name="StudentNumber" value="<?php echo $this->session->userdata('username'); ?>" readonly required>
                                            <div class="card-body">

                                                <div class="row">

                                                    <div class="col-md-8 form-group">
                                                        <label>Profile Picture</label>
                                                        <input type="file" class="form-control" name="nonoy" required>
                                                        <p>Limit the size to <span style="color:red; font-weight:bold">2MB only</span>. The recommended size is <span style="color:red; font-weight:bold">215px by 215x</span>.</p>
                                                    </div>

                                                </div>
                                                <input type="submit" name="submit" class="btn btn-info" value="Upload">
                                            </div>


                                    </div>
                                    </form>
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