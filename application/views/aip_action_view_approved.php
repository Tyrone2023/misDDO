<?php
$total     = count($data);
$districts = array();
$groups    = array();
foreach ($data as $r) {
    if (!empty($r->district))    { $districts[$r->district] = true; }
    if (!empty($r->alloc_group)) { $groups[$r->alloc_group] = true; }
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
                                <div class="ap-hero">
                                    <div class="ap-hero-text">
                                        <span class="ap-hero-eyebrow"><i class="mdi mdi-check-decagram"></i> Approved</span>
                                        <h3 class="ap-hero-title"><?= html_escape($title); ?></h3>
                                        <p class="ap-hero-sub">
                                            Fiscal Year <strong><?= html_escape($fy); ?></strong>
                                            <span class="ap-dotsep">&bull;</span> View&#8209;only reference of fully approved implementation plans
                                        </p>
                                    </div>
                                    <div class="ap-hero-stats">
                                        <div class="ap-stat">
                                            <span class="ap-stat-value"><?= number_format($total); ?></span>
                                            <span class="ap-stat-label">Approved Plans</span>
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
                                            <h5>No approved plans yet</h5>
                                            <p>Nothing has reached final approval for FY <?= html_escape($fy); ?>.</p>
                                        </div>
                                    <?php else : ?>
                                    <div class="table-responsive">
                                        <table id="datatable" class="table ap-table dt-responsive nowrap" style="width:100%;">
                                            <thead>
                                                <tr>
                                                    <th>School</th>
                                                    <th>Budget Code</th>
                                                    <th>Group</th>
                                                    <th>Date Approved</th>
                                                    <th>Status</th>
                                                    <th class="text-right">Documents</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($data as $row) :
                                                    $name    = !empty($row->schoolName) ? $row->schoolName : 'Unknown school';
                                                    $initial = strtoupper(substr(trim($name), 0, 1));
                                                    $docs    = $row->school_id . '/' . $row->fy . '/' . $row->b_code . '/' . $row->id;
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
                                                        <span class="ap-status-pill">
                                                            <i class="mdi mdi-check-decagram"></i> Approved
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
                                                                <!-- <a class="dropdown-item" target="_blank" href="<?= base_url(); ?>Page/view_sip_admin/<?= $row->school_id; ?>">
                                                                    <i class="mdi mdi-book-open-page-variant"></i> School Improvement Plan
                                                                </a> -->
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
                                                                <div class="dropdown-divider"></div>
                                                                <a class="dropdown-item" target="_blank" href="<?= base_url(); ?>page/generate_ca/<?= $row->school_id; ?>/<?= $row->b_code; ?>/<?= $row->id; ?>">
                                                                    <i class="mdi mdi-certificate-outline"></i> Certificate of Approval
                                                                </a>
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

        <!-- The ap-* styles this page used to define inline now live in
             assets/css/aip-plans.css, loaded from templates/head.php, because the
             unlock-request queue and the school AIP page render the same markup. -->

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
                            info: 'Showing _START_ to _END_ of _TOTAL_ approved plans',
                            infoEmpty: 'No approved plans',
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
