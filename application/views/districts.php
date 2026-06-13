            <?php include('templates/head.php'); ?>
            <!-- <link href="<?= base_url(); ?>assets/libs/custombox/custombox.min.css" rel="stylesheet" type="text/css"> -->
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
                                    <!-- <a href="<?= base_url()?>Page/school_new" class="btn btn-primary waves-effect waves-light">Add New</a> -->
                                    <a href="#" class="btn btn-primary waves-effect waves-light">Add New</a>

                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->

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

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body table-responsive">
                                        <h4 class="header-title mb-4">LIST OF DISTRICTS</h4><br />

                                        <!-- <table id="selection-datatable" class="table dt-responsive nowrap"> -->
                                        <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                                <thead>
                                                    <tr>
                                                        <th>District Name</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach($data as $row){ ?>
                                                    <tr>
                                                    <td><?= $row->discription; ?></td>
                                                   
                                                    <td>
                                                        <!-- <a href="<?=base_url(); ?>Page/schoolProfile?schoolid=<?php echo $row->id; ?>" class="text-success"><i class="mdi mdi-file-document-box-check-outline"></i>View</a> &nbsp; &nbsp; -->
                                                        <a href="#" class="text-info"><i class="mdi mdi-pencil-outline"></i>Edit</a> 
                                                    </td>

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




             
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
            </div>
            <!-- /.modal -->

            <?php include('templates/footer.php'); ?>