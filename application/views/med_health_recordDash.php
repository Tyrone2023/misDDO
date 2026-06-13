<?php include('templates/head.php'); ?>
<link href="<?= base_url(); ?>assets/libs/custombox/custombox.min.css" rel="stylesheet" type="text/css">
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
                        <!-- <button type="button" class="btn btn-primary waves-effect waves-light" data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target=".bs-example-modal-lg">Add New</button> -->
                        <a href="<?= base_url(); ?>Page/med_healthRecAdd" class="btn btn-primary waves-effect waves-light">Add New</a>
                        <div class="clearfix"></div>
                        <br>
                        <div class="row">
                            <div class="col-12">

                            </div>
                        </div>
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

                        <span>
                                </span>
                            </h4><br />

                            <!-- Check if data exists -->
                            <div class="table-responsive">
    <table id="datatable" class="table table-bordered dt-responsive nowrap" 
           style="border-collapse: collapse; border-spacing: 0; width: 100%;">
        <thead>
        <tr>
                    <th>Name</th>
                    <th>Department</th>
                    <th>District</th>
                    <th>School</th>
                    <th>Manage</th>

                </tr>
            </thead>
            <tbody>
                <?php foreach ($data as $row) : ?>
                    <tr>
                        <td><?= $row->fullName; ?></td>
                        <td><?= $row->department; ?></td>
                        <td><?= $row->district; ?></td>
                        <td><?= $row->school; ?></td>

                        <td>
                        <a href="<?= base_url(); ?>Page/med_healthRec/<?= $row->healthID; ?>" class="text-primary" target="_blank">
    <i class="mdi mdi-file-document-outline" data-toggle="tooltip" title="Health Examination Record"></i>
</a>


                            <a href="<?= base_url(); ?>Page/update_healthRec/<?= $row->healthID; ?>" class="text-warning" target="_blank">
                                <i class="mdi mdi-file-document-edit-outline" data-toggle="tooltip" title="Update Health Record"></i>
                            </a>

                            <a href="<?= base_url('Page/delete_healthRec/' . $row->healthID); ?>" 
                            class="text-danger" 
                            onclick="return confirm('Are you sure you want to delete this record?');">
                            <i class="mdi mdi-delete" data-toggle="tooltip" title="Delete"></i>
                            </a>


                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
    </table>
</div>

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


<!-- jQuery and Bootstrap JS -->

