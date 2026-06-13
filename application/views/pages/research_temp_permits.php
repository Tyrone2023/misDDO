<!-- ============================================================== -->
<!-- Start Page Content here -->
<!-- ============================================================== -->
<?php
    $temp_permit_count = isset($temp_permit_count) ? (int)$temp_permit_count : 0;
    $research_requests = isset($research_requests) ? $research_requests : [];
    $h = function ($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); };
?>

<!-- DataTables styles (use existing bundled paths) -->
<link rel="stylesheet" href="<?= base_url('assets/libs/datatables/dataTables.bootstrap4.min.css'); ?>">
<link rel="stylesheet" href="<?= base_url('assets/libs/datatables/responsive.bootstrap4.min.css'); ?>">

<div class="content-page">
    <div class="content">

        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="page-title mb-0"><?= $h($title); ?></h4>
                        <span class="badge badge-primary" style="font-size: 14px;">Total: <?= $temp_permit_count; ?></span>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-12 d-flex justify-content-between align-items-center">
                    <a href="<?= base_url('Pages/view'); ?>" class="btn btn-light">
                        <i class="mdi mdi-arrow-left"></i> Back to Dashboard
                    </a>
                    <div class="text-muted small">Latest entries appear first.</div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header py-3 bg-transparent d-flex justify-content-between align-items-center">
                            <h5 class="header-title mb-0">Temporary Permits</h5>
                            <div class="card-widgets">
                                <a href="javascript:;" data-toggle="reload"><i class="mdi mdi-refresh"></i></a>
                            </div>
                        </div>
                        <div class="card-body">

                            <!-- Custom Search -->
                            <div class="row mb-3">
                                <div class="col-md-4 col-sm-6">
                                    <label for="permitSearch" class="font-weight-bold">Search</label>
                                    <input type="text" id="permitSearch" class="form-control" placeholder="Search permits...">
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table id="datatable-permits" class="table table-bordered table-striped table-sm w-100">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Control No.</th>
                                            <th>Research Title</th>
                                            <th>Researcher Type</th>
                                            <th>Request Date</th>
                                            <!-- <th>Status</th> -->
                                            <th>School / HEI</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($research_requests)): ?>
                                            <?php foreach ($research_requests as $req): ?>
                                                <?php
                                                    $dateVal = isset($req->request_date) ? trim((string)$req->request_date) : '';
                                                    $dateFmt = $dateVal !== '' ? date('M d, Y', strtotime($dateVal)) : '';
                                                    $status = trim((string)($req->status ?? ''));
                                                    $statusClass = 'badge-secondary';

                                                    if (strcasecmp($status, 'Submitted') === 0) {
                                                        $statusClass = 'badge-info';
                                                    } elseif (strcasecmp($status, 'Approved') === 0) {
                                                        $statusClass = 'badge-success';
                                                    } elseif (strcasecmp($status, 'Returned') === 0 || strcasecmp($status, 'Disapproved') === 0) {
                                                        $statusClass = 'badge-danger';
                                                    } elseif (strcasecmp($status, 'In-Process') === 0 || strcasecmp($status, 'Processing') === 0) {
                                                        $statusClass = 'badge-warning';
                                                    }
                                                ?>
                                                <tr>
                                                    <td><?= $h($req->control_no ?? ''); ?></td>
                                                    <td><?= $h($req->research_title ?? ''); ?></td>
                                                    <td><?= $h($req->researcher_type ?? ''); ?></td>
                                                    <td data-order="<?= $dateVal !== '' ? date('Y-m-d', strtotime($dateVal)) : ''; ?>">
                                                        <?= $h($dateFmt); ?>
                                                    </td>
                                                    <!-- <td>
                                                        <span class="badge <?= $statusClass; ?>"><?= $h($status); ?></span>
                                                    </td> -->
                                                    <td><?= $h($req->hei_name ?? ''); ?></td>
                                                    <td>
                                                        <?php if (isset($req->id)): ?>
                                                            <a class="btn btn-sm btn-outline-primary" target="_blank" href="<?= base_url('Research/report/' . (int)$req->id); ?>">
                                                                View
                                                            </a>
                                                        <?php else: ?>
                                                            <span class="text-muted">—</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="7" class="text-center text-muted">No temporary permit requests found.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>

<script>
    // Wait for all global assets (vendor + DataTables) to finish loading
    window.addEventListener('load', function () {
        if (!window.jQuery || !jQuery.fn.DataTable) {
            console.error('DataTables assets not loaded.');
            return;
        }

        var $table = jQuery('#datatable-permits');

        if (jQuery.fn.DataTable.isDataTable($table)) {
            $table.DataTable().destroy();
        }

        var table = $table.DataTable({
            responsive: true,
            autoWidth: false,
            pageLength: 25,
            lengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "All"]
            ],
            order: [[3, 'desc']],
            columnDefs: [
                { orderable: false, targets: 6 }
            ],
            dom: 'lrtip', // hides default DataTables search box
            language: {
                lengthMenu: "Show _MENU_ entries",
                zeroRecords: "No matching permits found",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                infoEmpty: "Showing 0 to 0 of 0 entries",
                infoFiltered: "(filtered from _MAX_ total entries)"
            }
        });

        // Custom search input
        jQuery('#permitSearch').on('keyup change', function () {
            table.search(this.value).draw();
        });
    });
</script>
