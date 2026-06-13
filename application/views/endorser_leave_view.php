<?php include('templates/head.php'); ?>
<link href="<?= base_url(); ?>assets/libs/custombox/custombox.min.css" rel="stylesheet" type="text/css">
<?php include('templates/header.php'); ?>

<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            <!-- Page Title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="header-title mb-4">Leave Applications for Recommendation</h4>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>

            <!-- Flash Messages -->
            <?php if ($this->session->flashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $this->session->flashdata('success'); ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <?php if ($this->session->flashdata('danger')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $this->session->flashdata('danger'); ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <!-- Table -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body table-responsive">
                            <table id="datatable" class="table table-bordered dt-responsive nowrap" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>Employee No.</th>
                                        <th>Employee Name</th>
                                        <th style='text-align:center'>Date Applied</th>
                                        <th style='text-align:center'>Leave Type</th>
                                        <th style='text-align:center'>From</th>
                                        <th style='text-align:center'>To</th>
                                                <th style='text-align:center'>Leave Attachment</th>
                                        <th style='text-align:center'>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($leaves)): ?>
                                        <?php foreach ($leaves as $row): ?>
                                            <tr>
                                                <td><?= $row->StaffID ?></td>
                                                <td><?= $row->FirstName . ' ' . $row->MiddleName . ' ' . $row->LastName ?></td>
                                                <td style="text-align:center"><?= $row->appDate ?></td>
                                                <td style="text-align:center"><?= $row->leaveType ?></td>
                                                <td style="text-align:center"><?= $row->dateFrom ?></td>
                                                <td style="text-align:center"><?= $row->dateTo ?></td>

                                                 <td style='text-align:center'>
                                                        <?php if (!empty($row->leaveAttachment)) : ?>
                                                            <a href="<?= base_url('uploads/leave_attachement/' . $row->leaveAttachment); ?>" target="_blank" class="text-primary">
                                                                <i class="mdi mdi-file-pdf"></i> View Attachment
                                                            </a>
                                                        <?php else : ?>
                                                            -
                                                        <?php endif; ?>
                                                    </td>
                                                <td style="text-align:center">
                                                    <a href="<?= base_url("Page/pendingLeaveEvaluation?empID={$row->StaffID}&leaveID={$row->leaveID}") ?>" class="text-success">
                                                        <i class="mdi mdi-file-document-box-check-outline"></i>Recommend
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center">No leave applications found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div> <!-- container-fluid -->
    </div> <!-- content -->
</div> <!-- content-page -->

<?php include('templates/footer.php'); ?>
