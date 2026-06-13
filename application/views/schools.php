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
                                    <a href="<?= base_url()?>Page/school_new" class="btn btn-primary waves-effect waves-light">Add New</a>

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
                                        <h4 class="header-title mb-4">LIST OF SCHOOLS</h4><br />

                                        <!-- <table id="selection-datatable" class="table dt-responsive nowrap"> -->
                                        <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                                <thead>
                                                    <tr>
                                                        <th>School Name</th>
                                                        <th>School ID</th>
                                                        <th>District</th>
                                                        <th>Address</th>
                                                        <th>School Head</th>
                                                        <th>School Head Designation</th>
                                                        <th>Permit No.</th>
                                                        <th>Recognition No.</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach($data as $row){ ?>
                                                    <tr>
                                                    <td><?= $row->schoolName; ?></td>
                                                    <td><?= $row->schoolID; ?></td>
                                                    <td><?= $row->district; ?></td>
                                                    <td><?= $row->sitio.' '.$row->brgy.', '.$row->city.', '.$row->province; ?></td>
                                                    <td><?= $row->adminFName.' '.$row->adminMName.' '.$row->adminLName; ?></td>
                                                    <td><?= $row->adminDesignation; ?></td>
                                                    <td><?= $row->permitNo; ?></td>
                                                    <td><?= $row->recogNo; ?></td>
                                                    <td>
                                                        <a href="<?=base_url(); ?>Page/schoolProfile?schoolid=<?php echo $row->schoolID; ?>" class="text-success"><i class="mdi mdi-file-document-box-check-outline"></i>View</a> &nbsp; &nbsp;
                                                        <a target="_blank" href="<?=base_url(); ?>Page/employeelist_ict/<?= $row->schoolID; ?>" class="text-primary"><i class="mdi mdi-file-document-box-check-outline"></i>Personnel</a> &nbsp; &nbsp;
                                                        <a href="<?=base_url(); ?>Page/schoolInfo?schoolid=<?php echo $row->schoolID; ?>" class="text-warning"><i class="mdi mdi-pencil-outline"></i>edit</a> 
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