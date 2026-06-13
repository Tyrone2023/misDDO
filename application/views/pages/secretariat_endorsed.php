<?php
function h($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
$jtLabels = $jobTypeLabels ?? [];
?>

<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="page-title mb-0"><?= h($title ?? 'Endorsed Applicants'); ?></h4>
                        <div>
                            <?php if($hasAssignment ?? false): ?>
                                <span class="badge badge-primary">Levels assigned</span>
                            <?php else: ?>
                                <span class="badge badge-warning">No level assignment</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <?php if(!($hasAssignment ?? false)): ?>
                <div class="alert alert-warning">
                    You don't have assigned levels. Ask a Super Admin to assign levels on the “Assign Secretariat Levels” page.
                </div>
            <?php endif; ?>

            <div class="card mb-3">
                <div class="card-body table-responsive">
                    <h5 class="header-title">Endorsed Applicants (no Demo/TRF yet)</h5>
                    <table id="datatable1" class="table table-bordered table-striped dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Record #</th>
                                <th>Job Title</th>
                                <th>Level</th>
                                <th>District</th>
                                <th>Date Endorsed</th>
                                <th style="width:120px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($endorsed)): ?>
                                <?php foreach($endorsed as $row): ?>
                                    <?php
                                        $record    = $row->record_no ?? '';
                                        $jobId     = $row->jobID ?? $row->jobId ?? '';
                                        $preSchool = $row->pre_school ?? '';
                                        $appId     = $row->appID ?? '';
                                        $viewLink  = base_url('Pages/ma/'.$record.'/'.$jobId.'/'.$preSchool.'/'.$appId.'/'.$record);
                                    ?>
                                    <tr>
                                        <td><?= h(trim(($row->LastName ?? '').', '.($row->FirstName ?? '').' '.($row->MiddleName ?? ''))); ?></td>
                                        <td><?= h($record); ?></td>
                                        <td><?= h($row->jobTitle ?? ''); ?></td>
                                        <td><?= h($jtLabels[$row->job_type] ?? $row->job_type); ?></td>
                                        <td><?= h($row->district ?? ''); ?></td>
                                        <td><?= h($row->dateSubmitted ?? ''); ?></td>
                                        <td>
                                            <a class="btn btn-primary btn-sm" target="_blank" href="<?= h($viewLink); ?>">
                                                View Applicant Info
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted">No endorsed applicants found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-body table-responsive">
                    <h5 class="header-title">Applicants with Demo &amp; TRF Scores</h5>
                    <table id="datatable2" class="table table-bordered table-striped dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Record #</th>
                                <th>Job Title</th>
                                <th>Level</th>
                                <th>Demo Rating</th>
                                <th>TRF Rating</th>
                                <th style="width:120px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($scored)): ?>
                                <?php foreach($scored as $row): ?>
                                    <?php
                                        $record    = $row->record_no ?? '';
                                        $jobId     = $row->jobID ?? $row->jobId ?? '';
                                        $preSchool = $row->pre_school ?? '';
                                        $appId     = $row->appID ?? '';
                                        $viewLink  = base_url('Pages/ma/'.$record.'/'.$jobId.'/'.$preSchool.'/'.$appId.'/'.$record);
                                    ?>
                                    <tr>
                                        <td><?= h(trim(($row->LastName ?? '').', '.($row->FirstName ?? '').' '.($row->MiddleName ?? ''))); ?></td>
                                        <td><?= h($record); ?></td>
                                        <td><?= h($row->jobTitle ?? ''); ?></td>
                                        <td><?= h($jtLabels[$row->job_type] ?? $row->job_type); ?></td>
                                        <td><?= h($row->demo_rating ?? ''); ?></td>
                                        <td><?= h($row->tr_rating ?? ''); ?></td>
                                        <td>
                                            <a class="btn btn-primary btn-sm" target="_blank" href="<?= h($viewLink); ?>">
                                                View Applicant Info
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted">
                                        No applicants have both Demo and TRF scores yet.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- DataTables CSS -->
<link rel="stylesheet" href="<?= base_url('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css'); ?>">
<link rel="stylesheet" href="<?= base_url('assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css'); ?>">

<!-- jQuery -->
<script src="<?= base_url('assets/js/vendor.min.js'); ?>"></script>

<!-- DataTables JS -->
<script src="<?= base_url('assets/libs/datatables.net/js/jquery.dataTables.min.js'); ?>"></script>
<script src="<?= base_url('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js'); ?>"></script>
<script src="<?= base_url('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js'); ?>"></script>
<script src="<?= base_url('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js'); ?>"></script>

<script>
    $(document).ready(function () {
        $('#datatable1').DataTable({
            responsive: true,
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            ordering: true,
            searching: true,
            autoWidth: false,
            columnDefs: [
                { orderable: false, targets: [6] }
            ]
        });

        $('#datatable2').DataTable({
            responsive: true,
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            ordering: true,
            searching: true,
            autoWidth: false,
            columnDefs: [
                { orderable: false, targets: [6] }
            ]
        });
    });
</script>