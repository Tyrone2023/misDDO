<?php
// Unlock requests that were turned down (sgod_aip_request.stat = 2). Read-only: the
// decision was already made on Page/aip_requested, this page is the record of it.
// The plans behind these rows stayed locked - denying never touches sgod_aip_submit.
$counts = (isset($counts) && is_array($counts)) ? $counts : array('open' => 0, 'opened' => 0, 'denied' => 0, 'total' => 0);
$data   = isset($data) ? $data : array();

$deniedCount = count($data);
$districts = array();
foreach ($data as $r) {
    if (!empty($r->district)) { $districts[$r->district] = true; }
}

// sgod_aip_submit.status -> how the plan currently sits in the pipeline.
$stages = array(
    0 => array('Submitted',       'ap-pill-blue',  'mdi-send'),
    1 => array('Approved',        'ap-pill-green', 'mdi-check-decagram'),
    2 => array('Submitted',       'ap-pill-blue',  'mdi-send'),
    3 => array('AIP Reviewed',    'ap-pill-sky',   'mdi-file-find'),
    4 => array('Funds Available', 'ap-pill-amber', 'mdi-cash-multiple'),
    6 => array('Submitted',       'ap-pill-blue',  'mdi-send'),
);

if (!function_exists('aip_denied_stage_pill')) {
    function aip_denied_stage_pill($status, $stages)
    {
        if ($status === null || !isset($stages[(int) $status])) {
            return array('Not submitted', 'ap-pill-grey', 'mdi-help-circle-outline');
        }

        return $stages[(int) $status];
    }
}
?>

            <!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->

            <div class="content-page">
                <div class="content">

                    <div class="container-fluid">

                        <!-- start page header -->
                        <div class="row">
                            <div class="col-12">

                                <?php if ($this->session->flashdata('success')) : ?>
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        <?= $this->session->flashdata('success'); ?>
                                    </div>
                                <?php endif; ?>

                                <?php if ($this->session->flashdata('danger')) : ?>
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        <?= $this->session->flashdata('danger'); ?>
                                    </div>
                                <?php endif; ?>

                                <div class="ap-hero ap-hero-amber">
                                    <div class="ap-hero-text">
                                        <span class="ap-hero-eyebrow"><i class="mdi mdi-close-circle-outline"></i> Denied Requests</span>
                                        <h3 class="ap-hero-title"><?= html_escape($title); ?></h3>
                                        <p class="ap-hero-sub">
                                            Fiscal Year <strong><?= html_escape($fy); ?></strong>
                                            <span class="ap-dotsep">&bull;</span>
                                            Unlock requests that were turned down &mdash; these plans stayed locked
                                        </p>
                                    </div>
                                    <div class="ap-hero-stats">
                                        <div class="ap-stat">
                                            <span class="ap-stat-value"><?= number_format($deniedCount); ?></span>
                                            <span class="ap-stat-label">Denied</span>
                                        </div>
                                        <div class="ap-stat">
                                            <span class="ap-stat-value"><?= number_format((int) $counts['open']); ?></span>
                                            <span class="ap-stat-label">Pending</span>
                                        </div>
                                        <div class="ap-stat">
                                            <span class="ap-stat-value"><?= number_format(count($districts)); ?></span>
                                            <span class="ap-stat-label">Districts</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end page header -->

                        <div class="row">
                            <div class="col-12">
                                <div class="ap-card">

                                    <div class="ap-card-head">
                                        <div>
                                            <h5 class="ap-card-title"><i class="mdi mdi-close-circle-outline"></i> Denied unlock requests</h5>
                                            <p class="ap-card-sub">The school can see the denial and its reason on its own plan page. The plan remains <strong>locked</strong> and cannot be edited.</p>
                                        </div>
                                        <div>
                                            <a href="<?= base_url(); ?>Page/aip_requested" class="ap-btn ap-btn-status">
                                                <i class="mdi mdi-lock-open-variant-outline"></i> Pending requests
                                            </a>
                                        </div>
                                    </div>

                                    <?php if ($deniedCount === 0) : ?>
                                        <div class="ap-empty">
                                            <i class="mdi mdi-check-circle-outline"></i>
                                            <h5>No denied requests</h5>
                                            <p>No unlock request has been turned down for FY <?= html_escape($fy); ?>.</p>
                                        </div>
                                    <?php else : ?>
                                    <div class="table-responsive">
                                        <table id="rq-denied-table" class="table ap-table dt-responsive nowrap" style="width:100%;">
                                            <thead>
                                                <tr>
                                                    <th>School</th>
                                                    <th>Budget Code</th>
                                                    <th>Plan Status</th>
                                                    <th>Denied</th>
                                                    <th>School's Reason</th>
                                                    <th>Reason for Denial</th>
                                                    <th class="text-right">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($data as $row) :
                                                    $name    = !empty($row->schoolName) ? $row->schoolName : 'Unknown school';
                                                    $initial = strtoupper(substr(trim($name), 0, 1));
                                                    $pill    = aip_denied_stage_pill($row->submit_status, $stages);
                                                    $docs    = $row->school_id . '/' . $row->fy . '/' . $row->b_code . '/' . $row->s_id;
                                                    ?>
                                                <tr>
                                                    <td>
                                                        <div class="ap-school">
                                                            <span class="ap-avatar"><?= html_escape($initial); ?></span>
                                                            <span class="ap-school-text">
                                                                <span class="ap-school-name"><?= html_escape($name); ?></span>
                                                                <span class="ap-school-sub">
                                                                    <?= !empty($row->district) ? html_escape($row->district) : 'No district'; ?>
                                                                    <?php if (!empty($row->alloc_group)) : ?>
                                                                        <span class="ap-dotsep">&bull;</span><?= html_escape($row->alloc_group); ?>
                                                                    <?php endif; ?>
                                                                </span>
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="ap-chip"><?= html_escape($row->b_code); ?></span>
                                                        <?php if (!empty($row->alloc_amount)) : ?>
                                                            <div class="ap-school-sub mt-1">PHP <?= number_format((float) $row->alloc_amount, 2); ?></div>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <span class="ap-status-pill <?= $pill[1]; ?>"><i class="mdi <?= $pill[2]; ?>"></i> <?= $pill[0]; ?></span>
                                                    </td>
                                                    <td>
                                                        <span class="ap-date"><?= !empty($row->deny_date) ? html_escape($row->deny_date) : html_escape($row->tdate); ?></span>
                                                        <div class="ap-school-sub">
                                                            <?= !empty($row->deny_time) ? html_escape($row->deny_time) : html_escape($row->ttime); ?>
                                                            <?php if (!empty($row->deny_by)) : ?>
                                                                <span class="ap-dotsep">&bull;</span>by <?= html_escape($row->deny_by); ?>
                                                            <?php endif; ?>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="ap-reason"><?= html_escape($row->remarks); ?></div>
                                                    </td>
                                                    <td>
                                                        <div class="ap-reason"><?= !empty($row->deny_remarks) ? html_escape($row->deny_remarks) : '—'; ?></div>
                                                    </td>
                                                    <td class="text-right">
                                                        <div class="ap-actions">
                                                            <?php if (!empty($row->s_id)) : ?>
                                                                <button type="button"
                                                                        class="ap-btn ap-btn-status js-view-status"
                                                                        data-id="<?= $row->s_id; ?>"
                                                                        data-school="<?= html_escape($name); ?>">
                                                                    <i class="mdi mdi-map-marker-path"></i> Track
                                                                </button>

                                                                <div class="btn-group">
                                                                    <button type="button" class="ap-btn ap-btn-docs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                        <i class="mdi mdi-file-document-multiple-outline"></i> View
                                                                    </button>
                                                                    <div class="dropdown-menu dropdown-menu-right ap-menu">
                                                                        <a class="dropdown-item" target="_blank" href="<?= base_url(); ?>Page/aip_admin/<?= $docs; ?>">
                                                                            <i class="mdi mdi-clipboard-text-outline"></i> Annual Implementation Plan
                                                                        </a>
                                                                        <a class="dropdown-item" target="_blank" href="<?= base_url(); ?>Page/generate_sop_admin/<?= $docs; ?>">
                                                                            <i class="mdi mdi-clipboard-list-outline"></i> School Operational Plan
                                                                        </a>
                                                                        <?php if (!empty($row->app_id)) : ?>
                                                                            <a class="dropdown-item" target="_blank" href="<?= base_url(); ?>Page/generate_app_admin_sned/<?= $docs; ?>">
                                                                                <i class="mdi mdi-cart-outline"></i> Annual Procurement Plan
                                                                            </a>
                                                                        <?php else : ?>
                                                                            <span class="dropdown-item disabled">
                                                                                <i class="mdi mdi-cart-off"></i> Annual Procurement Plan
                                                                            </span>
                                                                        <?php endif; ?>
                                                                    </div>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <?php endif; ?>

                                </div>
                            </div>
                        </div>
                        <!--- end row -->

                    </div>
                    <!-- end container-fluid -->

                </div>
                <!-- end content -->

                <!-- Tracking modal -->
                <div id="ap-status-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="apStatusLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                        <div class="modal-content ap-modal">
                            <div class="modal-header">
                                <h5 class="modal-title" id="apStatusLabel"><i class="mdi mdi-map-marker-path"></i> Plan Tracking</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            </div>
                            <div class="modal-body" id="ap-status-body">
                                <div class="ap-loading"><i class="mdi mdi-loading mdi-spin"></i> Loading status&hellip;</div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="ap-btn ap-btn-ghost" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

            <!-- ============================================================== -->
            <!-- End Page content -->
            <!-- ============================================================== -->

             <!-- Footer Start -->
             <?php include('includes/footer.php'); ?>
            <!-- end Footer -->

        </div>
        <!-- END wrapper -->

        <!-- Vendor js -->
        <script src="<?= base_url(); ?>assets/js/vendor.min.js"></script>

        <!-- App js -->
        <script src="<?= base_url(); ?>assets/js/app.min.js"></script>

        <!-- Required datatable js -->
        <script src="<?= base_url(); ?>assets/libs/datatables/jquery.dataTables.min.js"></script>
        <script src="<?= base_url(); ?>assets/libs/datatables/dataTables.bootstrap4.min.js"></script>
        <script src="<?= base_url(); ?>assets/libs/datatables/dataTables.responsive.min.js"></script>
        <script src="<?= base_url(); ?>assets/libs/datatables/responsive.bootstrap4.min.js"></script>

        <script type="text/javascript">
            $(function () {
                if (!$.fn.DataTable.isDataTable('#rq-denied-table')) {
                    $('#rq-denied-table').DataTable({
                        pageLength: 25,
                        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                        order: [[3, 'desc']],
                        columnDefs: [{ orderable: false, targets: [4, 5, 6] }],
                        language: {
                            search: '',
                            searchPlaceholder: 'Search school, budget code, district…',
                            lengthMenu: '_MENU_ per page',
                            info: 'Showing _START_ to _END_ of _TOTAL_ denied requests',
                            infoEmpty: 'No denied requests',
                            zeroRecords: 'No matching requests found'
                        }
                    });
                }

                var $modal = $('#ap-status-modal');
                var $body  = $('#ap-status-body');

                $(document).on('click', '.js-view-status', function () {
                    var id     = $(this).data('id');
                    var school = $(this).data('school') || 'Plan Tracking';

                    $('#apStatusLabel').html('<i class="mdi mdi-map-marker-path"></i> ' + school);
                    $body.html('<div class="ap-loading"><i class="mdi mdi-loading mdi-spin"></i> Loading tracking history…</div>');
                    $modal.modal('show');

                    $.get('<?= base_url(); ?>Page/aip_track_modal/' + id)
                        .done(function (html) { $body.html(html); })
                        .fail(function () {
                            $body.html('<div class="ap-track-empty"><i class="mdi mdi-alert-circle-outline"></i><p>Could not load the status history. Please try again.</p></div>');
                        });
                });
            });
        </script>

    </body>
</html>
