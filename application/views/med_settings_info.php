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
                            <h4 class="header-title mb-4">
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
                <th>Manage</th>
            </tr>
        </thead>
        <tbody>
    <?php foreach ($data as $row): ?>
        <?php if ($row->patientType == 'Employee'): // Filter for Employee only ?>
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
                    $attachmentRaw = trim((string) ($row->attachment ?? ''));
                    $attachmentFiles = [];
                    if ($attachmentRaw !== '') {
                        $decodedAttachments = json_decode($attachmentRaw, true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($decodedAttachments)) {
                            foreach ($decodedAttachments as $fileName) {
                                $name = trim((string) $fileName);
                                if ($name !== '') {
                                    $attachmentFiles[] = $name;
                                }
                            }
                        } else {
                            $attachmentFiles[] = $attachmentRaw;
                        }
                    }
                    $attachmentFiles = array_values(array_unique($attachmentFiles));

                    if (!empty($attachmentFiles)):
                        foreach ($attachmentFiles as $fileName):
                            $attachmentPath = FCPATH . 'uploads/med/' . $fileName;
                            if (is_file($attachmentPath)):
                                $attachmentUrl = base_url('uploads/med/' . rawurlencode($fileName));
                    ?>
                                <a href="<?= $attachmentUrl ?>" target="_blank" style="margin-right: 6px;">
                                    <i class="fas fa-file-alt" style="font-size: 18px;"></i>
                                </a>
                            <?php else: ?>
                                <span class="text-danger font-weight-bold" data-toggle="tooltip" title="Attachment not found on server. Re-upload to restore.">Missing</span>
                    <?php
                            endif;
                        endforeach;
                    endif;
                    ?>
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
                <td>
                    <?php $user_sp = $this->session->userdata('sp'); ?>

                    <!-- Processed Status -->
                    <?php if ($row->consultationStat == 'Processed'): ?>
                        <?php if ($row->print_Perm == 'Yes'): ?>
        <a href="<?= base_url(); ?>Page/med_patient_report?medID=<?= $row->medID; ?>" 
           class="text-success" target="_blank">
            <i class="mdi mdi-certificate" data-toggle="tooltip" 
               data-placement="top" title="View Certificate"></i>
        </a>

        <a href="<?= base_url(); ?>Page/med_patient_reportv2?medID=<?= $row->medID; ?>" 
           class="text-primary" target="_blank">
            <i class="mdi mdi-file-document-outline" data-toggle="tooltip" 
               data-placement="top" title="View Certificate V2"></i>
        </a>
    <?php endif; ?>

                        <a href="<?= base_url(); ?>Page/med_patient_reportRX?medID=<?= $row->medID; ?>" 
                        class="text-danger" target="_blank">
                            <i class="mdi mdi-pill" data-toggle="tooltip" 
                            data-placement="top" title="View RX"></i>
                        </a>

                        <a href="<?= base_url(); ?>Page/med_patient_abstract?medID=<?= $row->medID; ?>" 
                        class="text-primary" target="_blank">
                            <i class="mdi mdi-file-document" data-toggle="tooltip" 
                            data-placement="top" title="View Medical Abstract"></i>
                        </a>

                        <a href="<?= base_url(); ?>Page/med_patient_update1?medID=<?= $row->medID; ?>" 
                        class="text-primary" target="_blank">
                            <i class="mdi mdi-pencil-outline" data-toggle="tooltip" 
                               data-placement="top" title="Update Consultation"></i>
                        </a>

                        <a href="<?= base_url(); ?>Page/delete_medpatient?id=<?= $row->medID; ?>" 
                        onclick="return confirm('Are you sure you want to delete this record?')" 
                        class="text-danger">
                            <i class="mdi mdi-delete" data-toggle="tooltip" 
                               data-placement="top" title="Delete Record"></i>
                        </a>

                    <!-- Pending Status -->
                    <?php elseif ($row->consultationStat == 'Pending'): ?>
                        <?php if ($user_sp == 0): ?>
                            <a href="<?= base_url(); ?>Page/med_patient_update?medID=<?= $row->medID; ?>" 
                            class="text-warning font-weight-bold" target="_blank">
                                <i class="mdi mdi-alert-circle-outline" data-toggle="tooltip" 
                                   data-placement="top" title="Pending Consultation"></i>
                            </a>
                        <?php else: ?>
                            <span class="text-warning font-weight-bold">
                                <i class="mdi mdi-alert-circle-outline" data-toggle="tooltip" 
                                   data-placement="top" title="You do not have permission to Proceed"></i>
                            </span>
                        <?php endif; ?>
                    <?php endif; ?>

                    <!-- Show Update and Delete only if NOT Processed -->
                    <?php if ($row->consultationStat != 'Processed'): ?>
                        <a href="<?= base_url(); ?>Page/med_patient_update1?medID=<?= $row->medID; ?>" 
                        class="text-primary" target="_blank">
                            <i class="mdi mdi-pencil-outline" data-toggle="tooltip" 
                               data-placement="top" title="Update Consultation"></i>
                        </a>

                        <a href="<?= base_url(); ?>Page/delete_medpatient?id=<?= $row->medID; ?>" 
                        onclick="return confirm('Are you sure you want to delete this record?')" 
                        class="text-danger">
                            <i class="mdi mdi-delete" data-toggle="tooltip" 
                               data-placement="top" title="Delete Record"></i>
                        </a>
                    <?php endif; ?>

                </td>
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
