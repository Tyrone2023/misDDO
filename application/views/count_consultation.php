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
                        <!-- <a href="<?= base_url(); ?>Page/add_medPatient" class="btn btn-primary waves-effect waves-light">Add New</a> -->
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
                            <h4 class="header-title mb-4">CONSULTATIONS
                                <span>
                                </span>
                            </h4><br />

                            <!-- Check if data exists -->
                            <div class="table-responsive">
    <table id="datatable" class="table table-bordered dt-responsive nowrap" 
           style="border-collapse: collapse; border-spacing: 0; width: 100%;">
        <thead>
            <tr>
                <th>Patient's Name</th>
                <th>Patient's Address</th>
                <th>Age</th>
                <th>Sex</th>
                <th>Civil Status</th>
                <th>Height</th>
                <th>Weight</th>
                <th>Contact</th>
                <th>attachment</th>
                <th>BP</th>
                <th>Cardiac Rate</th>
                <th>Respiratory Rate</th>
                <th>Temperature</th>
                <th>O2 SAT</th>
                <th>Complaint</th>
                <th>Allergies</th>
                <th>Current Medication</th>
                <th>Physical Examination</th>
                <th>Diagnosis</th>
                <th>Treatment/Management</th>
                <th>Remarks</th>
                <!-- <th>Manage</th> -->
            </tr>
        </thead>
        <tbody>
    <?php foreach ($data as $row): ?>
        <?php if ($row->patientType == 'Employee'): ?>
            <tr>
                <td><?= $row->FirstName . ' ' . $row->LastName ?></td>
                <td><?= $row->address ?></td>
                <td><?= $row->age ?></td>
                <td><?= $row->sex ?></td>
                <td><?= $row->cstat ?></td>
                <td><?= $row->height ?></td>
                <td><?= $row->weight ?></td>
                <td><?= $row->contact ?></td>
                <td style="text-align: center;">
                    <?php
                    $hasAttachment = !empty($row->attachment);
                    $attachmentPath = FCPATH . 'uploads/med/' . $row->attachment;
                    if ($hasAttachment && is_file($attachmentPath)) :
                        $attachmentUrl = base_url('uploads/med/' . rawurlencode($row->attachment));
                    ?>
                        <a href="<?= $attachmentUrl ?>" target="_blank">
                            <i class="fas fa-file-alt" style="font-size: 20px;"></i>
                        </a>
                    <?php elseif ($hasAttachment): ?>
                        <span class="text-danger font-weight-bold" data-toggle="tooltip" title="Attachment not found on server. Re-upload to restore.">Missing</span>
                    <?php endif; ?>
                </td>
                <td><?= $row->bp ?></td>
                <td><?= $row->cardiac ?></td>
                <td><?= $row->respiratory ?></td>
                <td><?= $row->temp ?></td>
                <td><?= $row->sat ?></td>
                <td><?= $row->complaint ?></td>
                <td><?= $row->allergies ?></td>
                <td><?= $row->current_med ?></td>
                <td><?= $row->phy_exam ?></td>
                <td><?= $row->diagnosis ?></td>
                <td><?= $row->treatment ?></td>
                <td><?= $row->remarks ?></td>
               

            </tr>
        <?php endif; // End Employee filter ?>

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

    <script>
    $(document).ready(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
<!-- jQuery and Bootstrap JS -->
