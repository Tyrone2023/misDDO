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
                        <!-- start page title -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="page-title-box">
                                    <h4 class="page-title"></h4>
                                    <div class="page-title-right">
                                        <ol class="breadcrumb p-0 m-0">
                                            <!-- <li class="breadcrumb-item"><a href="#"><span class="badge badge-purple mb-3">Currently login to <b>SY <?php echo $this->session->userdata('sy'); ?> <?php echo $this->session->userdata('semester'); ?></span></b></a></li> -->
                                        </ol>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>

                        <!-- end page title -->
                        <div class="row">
                            <div class="col-md-12">
                                <?php echo $this->session->flashdata('msg'); ?>
                                <div class="card">
                                    <div class="card-body table-responsive">
                                        <h4 class="m-t-0 header-title mb-4">Bulk Upload Students' Profile<br />
                                            <!-- <span class="badge badge-purple mb-3">SY <?php echo $this->session->userdata('sy'); ?> <?php echo $this->session->userdata('semester'); ?></span> -->
                                        </h4>

                                        <?php
                                        if (isset($response)) {
                                            echo $response;
                                        }
                                        ?>
                                        <form method='post' action='' enctype="multipart/form-data">
                                         
                                            <div class="box-body">
                                                <div class="form-group">
                                                    <label></label>
                                                    <input type='file' class="form-control" name='file' required>
                                                </div>
                                            </div>

                                            <div class="box-footer">
                                                <input type='submit' value='Upload' class="btn btn-info float-right" name='upload'>
                                            </div>
                                        </form>

                                        <!-- <form class="form-horizontal" method='post' action='' enctype="multipart/form-data">
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-md-3 col-form-label">School Year</label>
                                                <div class="col-md-9">
                                                    <input type="text" class="form-control" name="sy" placeholder="2025-2026" required>
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
                                        </form> -->

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end container-fluid -->

                </div>
                <!-- end content -->

                <?php include('templates/footer.php'); ?>