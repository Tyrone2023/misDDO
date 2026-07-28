<?php
// Plan Supervisor list of implementation plans at one pipeline stage. Strictly
// view-only: the supervisor can read the tracking history and open the generated
// documents, but there is no Approve / Open / Evaluate action anywhere on it.
$total     = count($data);
$districts = array();
$groups    = array();
foreach ($data as $r) {
    if (!empty($r->district))    { $districts[$r->district] = true; }
    if (!empty($r->alloc_group)) { $groups[$r->alloc_group] = true; }
}

// sgod_aip_submit.status -> how the row reads on screen.
$statuses = array(
    0 => array('Submitted',       'ap-badge-primary',   'mdi-send'),
    1 => array('Approved',        'ap-badge-success',   'mdi-check-decagram'),
    2 => array('For SNED',        'ap-badge-secondary', 'mdi-account-group-outline'),
    3 => array('AIP Reviewed',    'ap-badge-info',      'mdi-file-find'),
    4 => array('Funds Available', 'ap-badge-warning',   'mdi-cash-multiple'),
    6 => array('For SBFP',        'ap-badge-secondary', 'mdi-food-apple-outline'),
);
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
                                <div class="ap-hero">
                                    <div class="ap-hero-text">
                                        <span class="ap-hero-eyebrow"><i class="mdi <?= html_escape($icon); ?>"></i> <?= html_escape($eyebrow); ?></span>
                                        <h3 class="ap-hero-title"><?= html_escape($title); ?></h3>
                                        <p class="ap-hero-sub">
                                            Fiscal Year <strong><?= html_escape($fy); ?></strong>
                                            <span class="ap-dotsep">&bull;</span> <?= html_escape($blurb); ?>
                                            <span class="ap-dotsep">&bull;</span> View&#8209;only
                                        </p>
                                    </div>
                                    <div class="ap-hero-stats">
                                        <div class="ap-stat">
                                            <span class="ap-stat-value"><?= number_format($total); ?></span>
                                            <span class="ap-stat-label">Plans</span>
                                        </div>
                                        <div class="ap-stat">
                                            <span class="ap-stat-value"><?= number_format(count($districts)); ?></span>
                                            <span class="ap-stat-label">Districts</span>
                                        </div>
                                        <div class="ap-stat">
                                            <span class="ap-stat-value"><?= number_format(count($groups)); ?></span>
                                            <span class="ap-stat-label">Groups</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end page header -->

                        <div class="row">
                            <div class="col-12">
                                <div class="ap-card">

                                    <?php if ($total === 0) : ?>
                                        <div class="ap-empty">
                                            <i class="mdi mdi-file-document-outline"></i>
                                            <h5>Nothing to show yet</h5>
                                            <p>No plan has reached this stage for FY <?= html_escape($fy); ?>.</p>
                                        </div>
                                    <?php else : ?>
                                    <div class="table-responsive">
                                        <table id="datatable" class="table ap-table dt-responsive nowrap" style="width:100%;">
                                            <thead>
                                                <tr>
                                                    <th>School</th>
                                                    <th>Budget Code</th>
                                                    <th>Group</th>
                                                    <th>Date</th>
                                                    <th>Status</th>
                                                    <th class="text-right">View</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($data as $row) :
                                                    $name    = !empty($row->schoolName) ? $row->schoolName : 'Unknown school';
                                                    $initial = strtoupper(substr(trim($name), 0, 1));
                                                    $docs    = $row->school_id . '/' . $row->fy . '/' . $row->b_code . '/' . $row->id;
                                                    $st      = isset($statuses[(int) $row->status])
                                                        ? $statuses[(int) $row->status]
                                                        : array('In Progress', 'ap-badge-secondary', 'mdi-progress-clock');
                                                    ?>
                                                <tr>
                                                    <td>
                                                        <div class="ap-school">
                                                            <span class="ap-avatar"><?= html_escape($initial); ?></span>
                                                            <span class="ap-school-text">
                                                                <span class="ap-school-name"><?= html_escape($name); ?></span>
                                                                <?php if (!empty($row->district)) : ?>
                                                                    <span class="ap-school-sub"><?= html_escape($row->district); ?></span>
                                                                <?php endif; ?>
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td><span class="ap-chip"><?= html_escape($row->b_code); ?></span></td>
                                                    <td>
                                                        <?php if (!empty($row->alloc_group)) : ?>
                                                            <span class="ap-badge ap-badge-info"><?= html_escape($row->alloc_group); ?></span>
                                                        <?php else : ?>
                                                            <span class="ap-muted">&mdash;</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><span class="ap-date"><?= html_escape($row->date); ?></span></td>
                                                    <td>
                                                        <span class="ap-status-pill <?= $st[1]; ?>">
                                                            <i class="mdi <?= $st[2]; ?>"></i> <?= html_escape($st[0]); ?>
                                                        </span>
                                                    </td>
                                                    <td class="text-right">
                                                        <button type="button"
                                                                class="ap-btn ap-btn-status js-view-status"
                                                                data-id="<?= $row->id; ?>"
                                                                data-school="<?= html_escape($name); ?>">
                                                            <i class="mdi mdi-map-marker-path"></i> Track
                                                        </button>

                                                        <div class="btn-group">
                                                            <button type="button" class="ap-btn ap-btn-docs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                <i class="mdi mdi-file-document-multiple-outline"></i> View
                                                            </button>
                                                            <div class="dropdown-menu dropdown-menu-right ap-menu">
                                                                <a class="dropdown-item" target="_blank" href="<?= base_url(); ?>Page/view_sip_admin/<?= $row->school_id; ?>">
                                                                    <i class="mdi mdi-book-open-page-variant"></i> School Improvement Plan
                                                                </a>
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
                                                                <?php if ((int) $row->status === 1) : ?>
                                                                    <div class="dropdown-divider"></div>
                                                                    <a class="dropdown-item" target="_blank" href="<?= base_url(); ?>page/generate_ca/<?= $row->school_id; ?>/<?= $row->b_code; ?>/<?= $row->id; ?>">
                                                                        <i class="mdi mdi-certificate-outline"></i> Certificate of Approval
                                                                    </a>
                                                                <?php endif; ?>
                                                            </div>
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

                <!-- Status modal -->
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

        <style>
        /* ---- Plan Supervisor list (view-only). Shares the class names used by
               the Approved Plans page so the two read alike. ---- */
        .ap-dotsep { margin: 0 .4rem; opacity: .45; }
        .ap-muted { color: #98a6ad; }

        .ap-hero {
            background: linear-gradient(120deg, #1f3a5f 0%, #2c5282 55%, #3182ce 100%);
            border-radius: 14px;
            padding: 1.75rem 1.9rem;
            margin-bottom: 1.5rem;
            color: #fff;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 1.25rem;
            box-shadow: 0 10px 26px rgba(31, 58, 95, .22);
        }
        .ap-hero-eyebrow {
            display: inline-flex; align-items: center; gap: .35rem;
            background: rgba(255,255,255,.18);
            border-radius: 999px;
            padding: .22rem .7rem;
            font-size: .7rem; font-weight: 600;
            text-transform: uppercase; letter-spacing: .06em;
        }
        .ap-hero-title { margin: .6rem 0 .3rem; font-weight: 600; color: #fff; }
        .ap-hero-sub { margin: 0; opacity: .85; font-size: .875rem; }
        .ap-hero-sub strong { color: #fff; }
        .ap-hero-stats { display: flex; gap: .75rem; flex-wrap: wrap; }
        .ap-stat {
            background: rgba(255,255,255,.13);
            border: 1px solid rgba(255,255,255,.18);
            border-radius: 11px;
            padding: .75rem 1.15rem;
            min-width: 108px;
            text-align: center;
        }
        .ap-stat-value { display: block; font-size: 1.5rem; font-weight: 600; line-height: 1.15; }
        .ap-stat-label { display: block; font-size: .68rem; text-transform: uppercase; letter-spacing: .06em; opacity: .8; }

        .ap-card {
            background: #fff;
            border: 1px solid #e9edf2;
            border-radius: 14px;
            padding: 1.35rem;
            box-shadow: 0 2px 10px rgba(16, 30, 54, .05);
        }

        .ap-table { border-collapse: separate; border-spacing: 0; }
        .ap-table thead th {
            border: none;
            border-bottom: 1px solid #e9edf2;
            background: transparent;
            color: #7b8794;
            font-size: .7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .06em;
            padding: .7rem .75rem;
        }
        .ap-table tbody td {
            border: none;
            border-bottom: 1px solid #f1f4f8;
            padding: .8rem .75rem;
            vertical-align: middle;
        }
        .ap-table tbody tr:hover { background: #f7f9fc; }

        .ap-school { display: flex; align-items: center; gap: .7rem; }
        .ap-avatar {
            flex: 0 0 auto;
            width: 36px; height: 36px;
            border-radius: 10px;
            background: linear-gradient(135deg, #e3edfa, #cfe0f7);
            color: #2c5282;
            font-weight: 600;
            display: inline-flex; align-items: center; justify-content: center;
        }
        .ap-school-text { display: flex; flex-direction: column; line-height: 1.3; }
        .ap-school-name { font-weight: 600; color: #313a46; white-space: normal; }
        .ap-school-sub { font-size: .74rem; color: #98a6ad; }

        .ap-chip {
            display: inline-block;
            background: #f1f4f8;
            border: 1px solid #e4e9f0;
            border-radius: 6px;
            padding: .16rem .5rem;
            font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
            font-size: .76rem;
            color: #4a5568;
        }
        .ap-date { color: #5c6873; font-size: .84rem; }

        .ap-badge {
            display: inline-block;
            border-radius: 999px;
            padding: .2rem .65rem;
            font-size: .72rem;
            font-weight: 600;
        }
        .ap-badge-info      { background: #e6f2fb; color: #1d6fa5; }
        .ap-badge-success   { background: #e4f5ec; color: #1e7d44; }
        .ap-badge-warning   { background: #fdf3e2; color: #a5701d; }
        .ap-badge-primary   { background: #e8edfb; color: #33499e; }
        .ap-badge-secondary { background: #eef0f3; color: #5c6873; }
        .ap-badge-latest    { background: #313a46; color: #fff; }

        .ap-status-pill {
            display: inline-flex; align-items: center; gap: .3rem;
            border: 1px solid currentColor;
            border-radius: 999px;
            padding: .22rem .7rem;
            font-size: .74rem;
            font-weight: 600;
            white-space: nowrap;
        }
        .ap-status-pill i { font-size: .9rem; }

        .ap-btn {
            display: inline-flex; align-items: center; gap: .35rem;
            border: 1px solid #dbe2ea;
            background: #fff;
            color: #4a5568;
            border-radius: 8px;
            padding: .38rem .78rem;
            font-size: .8rem;
            font-weight: 500;
            transition: all .15s ease;
        }
        .ap-btn:hover { background: #f4f7fb; border-color: #c3cfdd; color: #2c5282; text-decoration: none; }
        .ap-btn-status { border-color: #cfe0f7; color: #2c5282; background: #f2f7fd; }
        .ap-btn-status:hover { background: #2c5282; border-color: #2c5282; color: #fff; }
        .ap-btn-docs::after { margin-left: .3rem; }
        .ap-btn-ghost { background: #f1f4f8; }

        .ap-menu { border: 1px solid #e9edf2; border-radius: 10px; box-shadow: 0 8px 24px rgba(16,30,54,.12); padding: .35rem; }
        .ap-menu .dropdown-item { border-radius: 7px; font-size: .82rem; padding: .45rem .7rem; display: flex; align-items: center; gap: .5rem; }
        .ap-menu .dropdown-item i { color: #7b8794; width: 1rem; }
        .ap-menu .dropdown-item.disabled { color: #b6bfc9; }

        .ap-empty, .ap-track-empty { text-align: center; padding: 3rem 1rem; color: #98a6ad; }
        .ap-empty i, .ap-track-empty i { font-size: 2.75rem; opacity: .5; display: block; margin-bottom: .6rem; }
        .ap-empty h5 { color: #5c6873; }

        /* ---- Status modal ---- */
        .ap-modal { border: none; border-radius: 14px; overflow: hidden; }
        .ap-modal .modal-header { background: #f7f9fc; border-bottom: 1px solid #e9edf2; }
        .ap-modal .modal-title { font-size: .98rem; font-weight: 600; color: #313a46; display: flex; align-items: center; gap: .4rem; }
        .ap-modal .modal-footer { background: #f7f9fc; border-top: 1px solid #e9edf2; }
        .ap-modal .modal-body { max-height: 65vh; overflow-y: auto; }
        .ap-loading { text-align: center; padding: 2.5rem 1rem; color: #98a6ad; }

        .ap-track-head { border-bottom: 1px solid #eef1f5; padding-bottom: .85rem; margin-bottom: 1.1rem; }
        .ap-track-school { font-size: 1rem; font-weight: 600; color: #313a46; }
        .ap-track-meta { margin-top: .4rem; display: flex; gap: .4rem; flex-wrap: wrap; }

        .ap-timeline { list-style: none; margin: 0; padding: 0 0 0 1.9rem; position: relative; }
        .ap-timeline::before {
            content: ""; position: absolute; left: .62rem; top: .35rem; bottom: .35rem;
            width: 2px; background: #eef1f5;
        }
        .ap-timeline-item { position: relative; padding-bottom: 1.35rem; }
        .ap-timeline-item:last-child { padding-bottom: 0; }
        .ap-timeline-dot {
            position: absolute; left: -1.9rem; top: 0;
            width: 1.55rem; height: 1.55rem;
            border-radius: 50%;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: .82rem;
            border: 2px solid #fff;
            box-shadow: 0 0 0 1px #eef1f5;
        }
        .ap-dot-success   { background: #e4f5ec; color: #1e7d44; }
        .ap-dot-warning   { background: #fdf3e2; color: #a5701d; }
        .ap-dot-info      { background: #e6f2fb; color: #1d6fa5; }
        .ap-dot-primary   { background: #e8edfb; color: #33499e; }
        .ap-dot-secondary { background: #eef0f3; color: #5c6873; }

        .ap-timeline-top { display: flex; align-items: center; gap: .4rem; flex-wrap: wrap; }
        .ap-timeline-body {
            background: #f9fbfd;
            border: 1px solid #eef1f5;
            border-radius: 10px;
            padding: .65rem .85rem;
        }
        .ap-timeline-item.is-latest .ap-timeline-body { background: #fff; border-color: #dbe6f5; box-shadow: 0 2px 8px rgba(44,82,130,.07); }
        .ap-timeline-sub, .ap-timeline-time { font-size: .76rem; color: #98a6ad; margin-top: .3rem; }
        .ap-timeline-sub i, .ap-timeline-time i { opacity: .7; }

        @media (max-width: 767px) {
            .ap-hero { padding: 1.25rem; }
            .ap-hero-stats { width: 100%; }
            .ap-stat { flex: 1; min-width: 0; padding: .6rem .5rem; }
        }
        </style>

        <!-- Vendor js -->
        <script src="<?= base_url(); ?>assets/js/vendor.min.js"></script>

        <!-- App js -->
        <script src="<?= base_url(); ?>assets/js/app.min.js"></script>

        <!-- Required datatable js -->
        <script src="<?= base_url(); ?>assets/libs/datatables/jquery.dataTables.min.js"></script>
        <script src="<?= base_url(); ?>assets/libs/datatables/dataTables.bootstrap4.min.js"></script>
        <!-- Buttons examples -->
        <script src="<?= base_url(); ?>assets/libs/datatables/dataTables.buttons.min.js"></script>
        <script src="<?= base_url(); ?>assets/libs/datatables/buttons.bootstrap4.min.js"></script>
        <script src="<?= base_url(); ?>assets/libs/jszip/jszip.min.js"></script>
        <script src="<?= base_url(); ?>assets/libs/pdfmake/pdfmake.min.js"></script>
        <script src="<?= base_url(); ?>assets/libs/pdfmake/vfs_fonts.js"></script>
        <script src="<?= base_url(); ?>assets/libs/datatables/buttons.html5.min.js"></script>
        <script src="<?= base_url(); ?>assets/libs/datatables/buttons.print.min.js"></script>

        <!-- Responsive examples -->
        <script src="<?= base_url(); ?>assets/libs/datatables/dataTables.responsive.min.js"></script>
        <script src="<?= base_url(); ?>assets/libs/datatables/responsive.bootstrap4.min.js"></script>

        <script src="<?= base_url(); ?>assets/libs/datatables/dataTables.keyTable.min.js"></script>
        <script src="<?= base_url(); ?>assets/libs/datatables/dataTables.select.min.js"></script>

        <script src="<?= base_url(); ?>assets/libs/custombox/custombox.min.js"></script>

        <script type="text/javascript">
            $(function () {
                // Own DataTables init (the shared datatables.init.js also targets #datatable,
                // so guard against a double initialisation).
                if (!$.fn.DataTable.isDataTable('#datatable')) {
                    $('#datatable').DataTable({
                        pageLength: 25,
                        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                        order: [[0, 'asc']],
                        columnDefs: [{ orderable: false, targets: 5 }],
                        language: {
                            search: '',
                            searchPlaceholder: 'Search school, budget code, group…',
                            lengthMenu: '_MENU_ per page',
                            info: 'Showing _START_ to _END_ of _TOTAL_ plans',
                            infoEmpty: 'No plans',
                            zeroRecords: 'No matching plans found'
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
