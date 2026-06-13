<?php include('templates/head.php'); ?>
<link href="<?= base_url(); ?>assets/libs/custombox/custombox.min.css" rel="stylesheet" type="text/css">
<?php include('templates/header.php'); ?>
<style>
    #consultations-table-wrapper {
        overflow: visible;
    }

    @media (max-width: 991.98px) {
        #consultations-table-wrapper {
            overflow-x: auto;
            overflow-y: visible;
        }
    }

    .dropdown-menu {
        z-index: 2000;
    }

    /* Center the Filter by District (label + select + clear) */
    .district-filter-bar {
        display: flex;
        justify-content: center;
        width: 100%;
    }

    .district-filter-inner {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: .6rem;
        flex-wrap: wrap;
    }

    .district-filter-inner .filter-label {
        font-weight: 700;
        white-space: nowrap;
        margin-bottom: 0;
    }

    .district-filter-inner .filter-select {
        width: 260px;
        max-width: 100%;
    }

    .district-filter-inner .filter-clear {
        min-width: 90px;
    }

    @media (max-width: 480px) {
        .district-filter-inner {
            flex-direction: column;
            align-items: stretch;
        }

        .district-filter-inner .filter-label {
            text-align: center;
        }

        .district-filter-inner .filter-clear {
            width: 100%;
        }
    }
</style>

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
                        <a href="<?= base_url(); ?>Page/add_medPatient" class="btn btn-primary waves-effect waves-light">Add New</a>
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
                        <div class="card-body">
                            <h4 class="header-title mb-4">CONSULTATIONS
                                <span>
                                </span>
                            </h4><br />

                            <!-- Check if data exists -->
                            <?php
                            $districtOptions = [];
                            foreach ($data as $row) {
                                if (!empty($row->district)) {
                                    $districtOptions[$row->district] = true;
                                }
                            }
                            ?>
                            <div class="district-filter-bar mb-3">
                                <div class="district-filter-inner">
                                    <label for="districtFilter" class="filter-label">Filter by District:</label>

                                    <select id="districtFilter" class="form-control filter-select">
                                        <option value="">All districts</option>
                                        <?php foreach (array_keys($districtOptions) as $district): ?>
                                            <option value="<?= htmlspecialchars($district, ENT_QUOTES, 'UTF-8'); ?>">
                                                <?= htmlspecialchars($district, ENT_QUOTES, 'UTF-8'); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>

                                    <button type="button" id="clearDistrictFilter" class="btn btn-secondary filter-clear">
                                        Clear
                                    </button>
                                </div>
                            </div>


                            <div class="table-responsive" id="consultations-table-wrapper">

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
                                            <th>Referral Facility</th>
                                            <th>Reasons for Referral</th>
                                            <th>Manage</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $user_sp = $this->session->userdata('sp');
                                        $logged_in_user = $this->session->userdata('username');
                                        $posNorm = strtolower(trim((string) $this->session->userdata('position')));
                                        $isNurseRole = in_array($posNorm, ['nurse', 'division nurse'], true);

                                        foreach ($data as $row):
                                            if ($row->patientType == 'Employee'): ?>
                                                <tr>
                                                    <td><?= $row->FirstName . ' ' . $row->LastName ?></td>
                                                    <td><?= $row->address ?></td>
                                                    <?php
                                                    $ageDisplay = '';
                                                    if (isset($row->age) && is_numeric($row->age) && (int) $row->age > 0) {
                                                        $ageDisplay = (int) $row->age;
                                                    } elseif (!empty($row->birthdate)) {
                                                        $birthDate = date_create($row->birthdate);
                                                        if ($birthDate) {
                                                            $ageDisplay = (new DateTime())->diff($birthDate)->y;
                                                        }
                                                    }
                                                    ?>
                                                    <td><?= $ageDisplay !== '' ? $ageDisplay : ''; ?></td>
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
                                                            foreach ($attachmentFiles as $idx => $fileName):
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
                                                        <?php if ($row->disposition === 'Transferred/Referred'): ?>
                                                            <?= htmlspecialchars($row->referral_facility ?? '') ?>
                                                        <?php else: ?>
                                                            <span class="text-muted">-</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if ($row->disposition === 'Transferred/Referred'): ?>
                                                            <?= htmlspecialchars($row->referral_reason ?? '') ?>
                                                        <?php else: ?>
                                                            <span class="text-muted">-</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $printPermValue = strtolower(trim((string) ($row->print_Perm ?? '')));
                                                        $canPrintReports = $row->consultationStat == 'Processed'
                                                            && ($printPermValue === 'yes' || $printPermValue === '' || $isNurseRole);
                                                        ?>
                                                        <?php if ($user_sp == 0): ?>
                                                            <?php if ($row->consultationStat == 'Processed'): ?>
                                                                <!-- View Icons -->
                                                                <a href="<?= base_url(); ?>Page/med_patient_report?medID=<?= $row->medID; ?>" class="text-success" target="_blank" title="Certificate (v1)">
                                                                    <i class="mdi mdi-certificate" data-toggle="tooltip" title="Certificate (v1)"></i>
                                                                </a>
                                                                <a href="<?= base_url(); ?>Page/med_patient_reportv2?medID=<?= $row->medID; ?>" class="text-primary" target="_blank" title="Certificate (v2)">
                                                                    <i class="mdi mdi-file-document-outline" data-toggle="tooltip" title="Certificate (v2)"></i>
                                                                </a>
                                                                <a href="<?= base_url(); ?>Page/med_patient_reportRX?medID=<?= $row->medID; ?>" class="text-danger" target="_blank" title="RX">
                                                                    <i class="mdi mdi-pill" data-toggle="tooltip" title="RX"></i>
                                                                </a>
                                                                <?php if ($row->disposition === 'Transferred/Referred'): ?>
                                                                    <a href="<?= base_url(); ?>Page/med_patient_referral?medID=<?= $row->medID; ?>&download=1" class="text-info" target="_blank" title="Referral Form">
                                                                        <i class="mdi mdi-file-document-box" data-toggle="tooltip" title="Referral Form"></i>
                                                                    </a>
                                                                <?php endif; ?>
                                                                <a href="<?= base_url(); ?>Page/med_patient_abstract?medID=<?= $row->medID; ?>" class="text-primary" target="_blank" title="Medical Abstract">
                                                                    <i class="mdi mdi-file-document" data-toggle="tooltip" title="Medical Abstract"></i>
                                                                </a>

                                                                <a href="#"
                                                                    class="text-secondary ml-1 js-download-reports"
                                                                    data-report-v1="<?= base_url(); ?>Page/med_patient_report?medID=<?= $row->medID; ?>&download=1"
                                                                    data-report-v2="<?= base_url(); ?>Page/med_patient_reportv2?medID=<?= $row->medID; ?>&download=1"
                                                                    data-report-rx="<?= base_url(); ?>Page/med_patient_reportRX?medID=<?= $row->medID; ?>&download=1"
                                                                    data-report-abstract="<?= base_url(); ?>Page/med_patient_abstract?medID=<?= $row->medID; ?>&download=1"
                                                                    data-report-referral="<?= $row->disposition === 'Transferred/Referred' ? base_url() . 'Page/med_patient_referral?medID=' . $row->medID . '&download=1' : ''; ?>"
                                                                    title="Download Reports">
                                                                    <i class="mdi mdi-download" data-toggle="tooltip" title="Download Reports"></i>
                                                                </a>


                                                                <!-- Edit/Delete -->
                                                                <a href="<?= base_url(); ?>Page/med_patient_update1?medID=<?= $row->medID; ?>" class="text-primary" target="_blank">
                                                                    <i class="mdi mdi-pencil-outline" data-toggle="tooltip" title="Update Consultation"></i>
                                                                </a>
                                                                <a href="<?= base_url(); ?>Page/delete_medpatient?id=<?= $row->medID; ?>" onclick="return confirm('Are you sure you want to delete this record?')" class="text-danger">
                                                                    <i class="mdi mdi-delete" data-toggle="tooltip" title="Delete Record"></i>
                                                                </a>
                                                            <?php else: ?>
                                                                <?php if ($row->consultationStat == 'Pending'): ?>
                                                                    <a href="<?= base_url(); ?>Page/med_patient_update?medID=<?= $row->medID; ?>" class="text-warning font-weight-bold" target="_blank">
                                                                        <i class="mdi mdi-alert-circle-outline" data-toggle="tooltip" title="Pending Consultation"></i>
                                                                    </a>
                                                                <?php endif; ?>
                                                                <a href="<?= base_url(); ?>Page/med_patient_update1?medID=<?= $row->medID; ?>" class="text-primary" target="_blank">
                                                                    <i class="mdi mdi-pencil-outline" data-toggle="tooltip" title="Update Consultation"></i>
                                                                </a>
                                                                <a href="<?= base_url(); ?>Page/delete_medpatient?id=<?= $row->medID; ?>" onclick="return confirm('Are you sure you want to delete this record?')" class="text-danger">
                                                                    <i class="mdi mdi-delete" data-toggle="tooltip" title="Delete Record"></i>
                                                                </a>
                                                            <?php endif; ?>
                                                        <?php else: ?>
                                                            <?php if ($canPrintReports): ?>
                                                                <!-- View Icons -->
                                                                <a href="<?= base_url(); ?>Page/med_patient_report?medID=<?= $row->medID; ?>" class="text-success" target="_blank">
                                                                    <i class="mdi mdi-certificate" data-toggle="tooltip" title="View Certificate"></i>
                                                                </a>
                                                                <a href="<?= base_url(); ?>Page/med_patient_reportv2?medID=<?= $row->medID; ?>" class="text-primary" target="_blank">
                                                                    <i class="mdi mdi-file-document-outline" data-toggle="tooltip" title="View Certificate V2"></i>
                                                                </a>
                                                                <a href="<?= base_url(); ?>Page/med_patient_reportRX?medID=<?= $row->medID; ?>" class="text-danger" target="_blank">
                                                                    <i class="mdi mdi-pill" data-toggle="tooltip" title="View RX"></i>
                                                                </a>
                                                                <?php if ($row->disposition === 'Transferred/Referred'): ?>
                                                                    <a href="<?= base_url(); ?>Page/med_patient_referral?medID=<?= $row->medID; ?>&download=1" class="text-info" target="_blank">
                                                                        <i class="mdi mdi-file-document-box" data-toggle="tooltip" title="Referral Form"></i>
                                                                    </a>
                                                                <?php endif; ?>
                                                                <a href="<?= base_url(); ?>Page/med_patient_abstract?medID=<?= $row->medID; ?>" class="text-primary" target="_blank">
                                                                    <i class="mdi mdi-file-document" data-toggle="tooltip" title="View Medical Abstract"></i>
                                                                </a>

                                                                <a href="#"
                                                                    class="text-secondary ml-1 js-download-reports"
                                                                    data-report-v1="<?= base_url(); ?>Page/med_patient_report?medID=<?= $row->medID; ?>&download=1"
                                                                    data-report-v2="<?= base_url(); ?>Page/med_patient_reportv2?medID=<?= $row->medID; ?>&download=1"
                                                                    data-report-rx="<?= base_url(); ?>Page/med_patient_reportRX?medID=<?= $row->medID; ?>&download=1"
                                                                    data-report-abstract="<?= base_url(); ?>Page/med_patient_abstract?medID=<?= $row->medID; ?>&download=1"
                                                                    data-report-referral="<?= $row->disposition === 'Transferred/Referred' ? base_url() . 'Page/med_patient_referral?medID=' . $row->medID . '&download=1' : ''; ?>"
                                                                    title="Download Reports">
                                                                    <i class="mdi mdi-download" data-toggle="tooltip" title="Download Reports"></i>
                                                                </a>

                                                            <?php endif; ?>

                                                            <?php if ($row->consultationStat == 'Pending'): ?>
                                                                <?php if ($user_sp != 0): ?>
                                                                    <span class="text-warning font-weight-bold">
                                                                        <i class="mdi mdi-alert-circle-outline" data-toggle="tooltip" title="Pending Consultation"></i>
                                                                    </span>
                                                                <?php else: ?>
                                                                    <a href="<?= base_url(); ?>Page/med_patient_update?medID=<?= $row->medID; ?>" class="text-warning font-weight-bold" target="_blank">
                                                                        <i class="mdi mdi-alert-circle-outline" data-toggle="tooltip" title="Pending Consultation"></i>
                                                                    </a>
                                                                <?php endif; ?>
                                                                <a href="<?= base_url(); ?>Page/med_patient_update1?medID=<?= $row->medID; ?>" class="text-primary" target="_blank">
                                                                    <i class="mdi mdi-pencil-outline" data-toggle="tooltip" title="Update Consultation"></i>
                                                                </a>
                                                                <a href="<?= base_url(); ?>Page/delete_medpatient?id=<?= $row->medID; ?>" onclick="return confirm('Are you sure you want to delete this record?')" class="text-danger">
                                                                    <i class="mdi mdi-delete" data-toggle="tooltip" title="Delete Record"></i>
                                                                </a>
                                                            <?php endif; ?>
                                                        <?php endif; ?>
                                                    </td>


                                                </tr>
                                            <?php endif; // End Employee filter 
                                            ?>
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


    <div class="modal fade" id="downloadReportsModal" tabindex="-1" role="dialog" aria-labelledby="downloadReportsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="downloadReportsModalLabel">Download Reports</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-0">
                    <div class="list-group list-group-flush">
                        <a id="downloadReportV1Link" class="list-group-item list-group-item-action" target="_blank">Certificate (v1)</a>
                        <a id="downloadReportV2Link" class="list-group-item list-group-item-action" target="_blank">Certificate (v2)</a>
                        <a id="downloadReportRxLink" class="list-group-item list-group-item-action" target="_blank">RX</a>
                        <a id="downloadReferralLink" class="list-group-item list-group-item-action d-none" target="_blank">Referral Form</a>
                        <a id="downloadAbstractLink" class="list-group-item list-group-item-action" target="_blank">Medical Abstract</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include('templates/footer.php'); ?>

    <script>
        $(document).ready(function() {
            $('body').tooltip({
                selector: '[data-toggle="tooltip"]',
                container: 'body'
            });

            var table = $('#datatable').DataTable();
            $('#districtFilter').on('change', function() {
                var val = this.value;
                table.column(1).search(val ? '^' + $.fn.dataTable.util.escapeRegex(val) + '$' : '', true, false).draw();
            });
            $('#clearDistrictFilter').on('click', function() {
                $('#districtFilter').val('');
                table.column(1).search('').draw();
            });

            $(document).on('click', '.js-download-reports', function(e) {
                e.preventDefault();
                var $trigger = $(this);
                var referralLink = $trigger.attr('data-report-referral') || '';

                $('#downloadReportV1Link').attr('href', $trigger.attr('data-report-v1') || '#');
                $('#downloadReportV2Link').attr('href', $trigger.attr('data-report-v2') || '#');
                $('#downloadReportRxLink').attr('href', $trigger.attr('data-report-rx') || '#');
                $('#downloadAbstractLink').attr('href', $trigger.attr('data-report-abstract') || '#');

                if (referralLink) {
                    $('#downloadReferralLink').attr('href', referralLink).removeClass('d-none');
                } else {
                    $('#downloadReferralLink').attr('href', '#').addClass('d-none');
                }

                $('#downloadReportsModal').modal('show');
            });
        });
    </script>
    <!-- jQuery and Bootstrap JS -->
