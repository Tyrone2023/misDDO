<?php include('templates/head.php'); ?>
<link href="<?= base_url(); ?>assets/libs/custombox/custombox.min.css" rel="stylesheet" type="text/css">
<?php include('templates/header.php'); ?>

<?php
$lab = $lab_request ?? null;
$rows = is_array($history ?? null) ? $history : [];
$patientName = trim(preg_replace('/\s+/', ' ', (string) (($lab->FirstName ?? '') . ' ' . ($lab->MiddleName ?? '') . ' ' . ($lab->LastName ?? ''))));
?>

<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <a href="<?= base_url(); ?>Page/lab_request" class="btn btn-secondary waves-effect waves-light">Back to Lab Requests</a>
                        <div class="clearfix"></div>
                        <br>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body table-responsive">
                            <h4 class="header-title mb-2">PATIENT CONSULTATION HISTORY</h4>
                            <div class="mb-3 text-muted">
                                <strong>Patient:</strong> <?= htmlspecialchars($patientName !== '' ? $patientName : 'N/A'); ?>
                                | <strong>Type:</strong> <?= htmlspecialchars((string) ($lab->patientType ?? 'N/A')); ?>
                                | <strong>Records Found:</strong> <?= (int) count($rows); ?>
                            </div>

                            <?php if (empty($rows)): ?>
                                <div class="alert alert-warning mb-0">
                                    No consultation history found for this patient.
                                </div>
                            <?php else: ?>
                                <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Date</th>
                                            <th>Complaint</th>
                                            <th>Diagnosis</th>
                                            <th>Treatment</th>
                                            <th>Disposition</th>
                                            <th>Status</th>
                                            <th>Reports</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $i = 1; ?>
                                        <?php foreach ($rows as $row): ?>
                                            <?php
                                            $dispositionNorm = strtolower(trim((string) ($row->disposition ?? '')));
                                            $hasReferralInfo = in_array($dispositionNorm, ['transferred/referred', 'transferred / referred', 'referred', 'transferred'], true)
                                                || strpos($dispositionNorm, 'referred') !== false
                                                || trim((string) ($row->referral_facility ?? '')) !== ''
                                                || trim((string) ($row->referral_reason ?? '')) !== ''
                                                || trim((string) ($row->referral_guardian ?? '')) !== '';
                                            ?>
                                            <tr>
                                                <td><?= $i++; ?></td>
                                                <td><?= htmlspecialchars((string) ($row->appdate ?? '')); ?></td>
                                                <td><?= htmlspecialchars((string) ($row->complaint ?? '')); ?></td>
                                                <td><?= htmlspecialchars((string) ($row->diagnosis ?? '')); ?></td>
                                                <td><?= htmlspecialchars((string) ($row->treatment ?? '')); ?></td>
                                                <td><?= htmlspecialchars((string) ($row->disposition ?? '')); ?></td>
                                                <td><?= htmlspecialchars((string) ($row->consultationStat ?? '')); ?></td>
                                                <td>
                                                    <?php if (!empty($row->medID)): ?>
                                                        <a href="<?= base_url(); ?>Page/med_patient_report?medID=<?= (int) $row->medID; ?>" class="btn btn-success btn-sm mb-1" target="_blank" data-toggle="tooltip" title="Certificate v1">V1</a>
                                                        <a href="<?= base_url(); ?>Page/med_patient_reportv2?medID=<?= (int) $row->medID; ?>" class="btn btn-primary btn-sm mb-1" target="_blank" data-toggle="tooltip" title="Certificate v2">V2</a>
                                                        <a href="<?= base_url(); ?>Page/med_patient_reportRX?medID=<?= (int) $row->medID; ?>" class="btn btn-danger btn-sm mb-1" target="_blank" data-toggle="tooltip" title="Prescription">RX</a>
                                                        <?php if ($hasReferralInfo): ?>
                                                            <a href="<?= base_url(); ?>Page/med_patient_referral?medID=<?= (int) $row->medID; ?>&download=1" class="btn btn-info btn-sm mb-1" target="_blank" data-toggle="tooltip" title="Referral Form">
                                                                <i class="mdi mdi-file-document-box"></i>
                                                            </a>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('templates/footer.php'); ?>

<script>
$(document).ready(function () {
    $('[data-toggle="tooltip"]').tooltip({
        container: 'body'
    });
});
</script>
