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
                        <a href="<?= base_url(); ?>Page/add_request" class="btn btn-primary waves-effect waves-light">Add New</a>
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
                            <style>
                                .action-buttons .btn {
                                    margin-right: 4px;
                                    margin-bottom: 2px;
                                }
                            </style>
                            <h4 class="header-title mb-4">LABORATORY REQUEST
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
                <th>Laboratory Test</th>
                <th>Bleeding Parameters</th>
                <th>Hepatitis Markers</th>
                <th>Cardiac Function</th>
                <th>Blood Test</th>
                <th>Liver Profile</th>
                <th>Renal Function</th>
                <th>Serology</th>
                <th>Thyroid Function</th>
                <th>X - Ray</th>
                <th>Ultrasound</th>
                <th>Manage</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data as $row): ?>
                <?php
                $ageDisplay = trim((string) ($row->age ?? ''));
                if ($ageDisplay === '') {
                    $birthdateValue = trim((string) ($row->birthdate ?? ''));
                    if ($birthdateValue !== '') {
                        try {
                            $ageDisplay = (string) ((new DateTime($birthdateValue))->diff(new DateTime('today'))->y);
                        } catch (Exception $e) {
                            $ageDisplay = '';
                        }
                    }
                }
                ?>
                <tr>
                    <td><?= $row->FirstName . ' ' . $row->LastName ?></td>
                    <td><?= $row->address ?></td>
                    <td><?= htmlspecialchars($ageDisplay) ?></td>
                    <td><?= $row->sex ?></td>
                    <td><?= $row->lab_test ?></td>
                    <td><?= $row->bleed_test ?></td>
                    <td><?= $row->hepatitis_test ?></td>
                    <td><?= $row->cardiac ?></td>
                    <td><?= $row->blood_test ?></td>
                    <td><?= $row->liver_profile ?></td>
                    <td><?= $row->renal_func ?></td>
                    <td><?= $row->serology ?></td>
                    <td><?= $row->thyroid ?></td>
                    <td><?= $row->x_ray ?></td>
                    <td><?= $row->ultrasound ?></td>
                    <td class="action-buttons">
                        <a href="<?= base_url(); ?>Page/lab_patient_history/<?= $row->labID ?>" class="btn btn-info btn-sm" data-toggle="tooltip" data-placement="top" title="Patient History" target="_blank" aria-label="Patient History">
                            <i class="mdi mdi-history"></i>
                        </a>
                        <a href="<?= base_url(); ?>Page/print_labReq/<?= $row->labID ?>" class="btn btn-success btn-sm" data-toggle="tooltip" data-placement="top" title="Print Request" target="_blank" aria-label="Print Request">
                            <i class="mdi mdi-printer"></i>
                        </a>
                        <a href="<?= base_url(); ?>Page/edit_labReq/<?= $row->labID ?>" class="btn btn-primary btn-sm" data-toggle="tooltip" data-placement="top" title="Edit Request" aria-label="Edit Request">
                            <i class="mdi mdi-pencil"></i>
                        </a>
                        <a href="<?= base_url(); ?>Page/delete_labReq/<?= $row->labID ?>" class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="top" title="Delete Request" onclick="return confirm('Are you sure you want to delete this item?');" aria-label="Delete Request">
                            <i class="mdi mdi-delete"></i>
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

    <script>
    function initLabRequestTooltips() {
        $('[data-toggle="tooltip"]').tooltip('dispose').tooltip({
            container: 'body',
            trigger: 'hover'
        });
    }

    $(document).ready(function () {
        initLabRequestTooltips();
        $('#datatable').on('draw.dt responsive-display.dt', function () {
            initLabRequestTooltips();
        });
    });
    </script>
<!-- jQuery and Bootstrap JS -->
