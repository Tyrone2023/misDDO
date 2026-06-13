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
                        <a href="<?= base_url(); ?>/resources/template-for-sbfp-enrollment.xlsx" class="btn btn-success waves-effect width-md waves-light">Download the Template Here</a>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body table-responsive">
                            <h4 class="header-title mb-4">Mass Upload <br /><span class="float-left badge badge-primary inline mt-2">SBFP FILE</span></h4><br />

                            <div class="float-left">
                                <h4 class="text-right">

                                </h4>

                            </div>

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


                            <form class="form-horizontal" method='post' action='' enctype="multipart/form-data">
                                <div class="form-group row">
                                    <label for="inputEmail3" class="col-md-3 col-form-label">School Year</label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" name="sy" placeholder="2024-2025" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="inputPassword3" class="col-md-3 col-form-label">File</label>
                                    <div class="col-md-9">
                                        <input type='file' class="form-control" name='file' required>
                                    </div>
                                </div>


                                <div class="form-group mb-0 justify-content-end row">
                                    <div class="col-md-9">
                                        <input type='submit' value='Upload' class="btn btn-info float-right" name='upload'>
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