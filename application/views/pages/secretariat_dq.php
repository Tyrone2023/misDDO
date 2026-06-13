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
                        <!-- <h4 class="page-title mb-0"><?= h($title ?? 'Disqualified Applicants'); ?></h4> -->
                       
                    </div>
                </div>
            </div>


            <div class="card">
                <div class="card-body table-responsive">
                    <h5 class="header-title">Disqualified Applicants</h5>
                    <table id="datatable_dq" class="table table-bordered table-striped dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                
                                <th>Record #</th>
                                <th>Name</th>
                                <th>Job Title</th>
                                <th>Level</th>
                                <th style="width:120px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($dq_applicants)): ?>
                                <?php foreach($dq_applicants as $row): ?>
                                    <?php
                                        $record    = $row->record_no ?? '';
                                        $jobId     = $row->jobID ?? $row->jobId ?? '';
                                        $preSchool = $row->pre_school ?? '';
                                        $appId     = $row->appID ?? '';
                                        $viewLink  = base_url('Pages/ma/'.$record.'/'.$jobId.'/'.$preSchool.'/'.$appId.'/'.$record);
                                    ?>
                                    <tr>
                                        <td><?= h($record); ?></td>
                                                                                <td><?= h(trim(($row->LastName ?? '').', '.($row->FirstName ?? '').' '.($row->MiddleName ?? ''))); ?></td>

                                        <td><?= h($row->jobTitle ?? ''); ?></td>
                                        <td><?= h($jtLabels[$row->job_type] ?? $row->job_type); ?></td>
                                        <td>
                                            <a class="btn btn-primary btn-sm" target="_blank" href="<?= h($viewLink); ?>">
                                                View Applicant Info
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted">No disqualified applicants found.</td>
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
        $('#datatable_dq').DataTable({
            responsive: true,
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            ordering: true,
            searching: true,
            autoWidth: false,
            columnDefs: [
                { orderable: false, targets: [7] }
            ]
        });
    });
</script>
