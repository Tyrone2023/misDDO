<?php
// Review / funds worklists: Page/aip_sub_review, Page/aip_reviewed and Page/aip_sub_funds.
// Same layout as the Approved Plans page. The stage action is its own button and asks for a
// simple confirmation before it runs.
//   $stage_action : 'review' | 'funds' | '' (reference list, no stage action)
//   $can_act      : the logged-in role owns $stage_action (Page::aip_stage_allowed() checks again)
//   $from         : the list open_aip() returns to after an unlock.
$total     = count($data);
$districts = array();
$groups    = array();
foreach ($data as $r) {
    if (!empty($r->district))    { $districts[$r->district] = true; }
    if (!empty($r->alloc_group)) { $groups[$r->alloc_group] = true; }
}

// sgod_aip_submit.status -> how the row reads on screen.
$statuses = array(
    0 => array('Submitted',       'ap-pill-blue',  'mdi-send'),
    1 => array('Approved',        'ap-pill-green', 'mdi-check-decagram'),
    2 => array('For SNED',        'ap-pill-grey',  'mdi-account-group-outline'),
    3 => array('AIP Reviewed',    'ap-pill-sky',   'mdi-file-find'),
    4 => array('Funds Available', 'ap-pill-amber', 'mdi-cash-multiple'),
    6 => array('For SBFP',        'ap-pill-grey',  'mdi-food-apple-outline'),
);

// Statuses each stage action moves a plan from (mirrors Page::approved_aip_review/funds).
$stageFrom = array('review' => array(0, 2, 6), 'funds' => array(3));

$copy = array(
    'review' => array(
        'go'      => 'Mark as Reviewed',
        'icon'    => 'mdi-check-circle-outline',
        'path'    => 'Page/approved_aip_review/',
        'effect'  => 'It will move to Funds Certification.',
        'no_role' => 'View only. Marking plans as reviewed is done by the Review account.',
    ),
    'funds' => array(
        'go'      => 'Certify Funds Available',
        'icon'    => 'mdi-cash-multiple',
        'path'    => 'Page/approved_aip_funds/',
        'effect'  => 'It will go to the SGOD Chief for approval.',
        'no_role' => 'View only. Certifying funds is done by the Funds account.',
    ),
);
$mode   = isset($copy[$stage_action]) ? $copy[$stage_action] : null;
$canAct = $mode !== null && !empty($can_act);

// Latest open unlock request per school + batch. Batch codes repeat across schools, so the
// school is part of the key. aip_request_list() is ordered id DESC, so the first hit wins.
$openRequests = array();
foreach ((array) $open_requests as $q) {
    $key = $q->school_id . '|' . $q->b_code;
    if (!isset($openRequests[$key])) {
        $openRequests[$key] = $q;
    }
}
?>

            <!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->

            <div class="content-page">
                <div class="content">

                    <div class="container-fluid">

                        <?php if ($this->session->flashdata('success')) : ?>
                            <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <?= $this->session->flashdata('success'); ?>
                            </div>
                        <?php endif; ?>
                        <?php if ($this->session->flashdata('danger')) : ?>
                            <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <?= $this->session->flashdata('danger'); ?>
                            </div>
                        <?php endif; ?>

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

                        <?php if ($mode !== null && !$canAct) : ?>
                            <div class="ap-note ap-note-amber">
                                <i class="mdi mdi-eye-outline"></i>
                                <div><?= html_escape($mode['no_role']); ?></div>
                            </div>
                        <?php endif; ?>

                        <div class="row">
                            <div class="col-12">
                                <div class="ap-card">

                                    <?php if ($total === 0) : ?>
                                        <div class="ap-empty">
                                            <i class="mdi mdi-file-document-outline"></i>
                                            <h5>Nothing to show yet</h5>
                                            <p>No plan is at this stage for FY <?= html_escape($fy); ?>.</p>
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
                                                    <th class="text-right">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($data as $row) :
                                                    $name    = !empty($row->schoolName) ? $row->schoolName : 'Unknown school';
                                                    $initial = strtoupper(substr(trim($name), 0, 1));
                                                    $docs    = $row->school_id . '/' . $row->fy . '/' . $row->b_code . '/' . $row->id;
                                                    $status  = (int) $row->status;
                                                    $st      = isset($statuses[$status])
                                                        ? $statuses[$status]
                                                        : array('In Progress', 'ap-pill-grey', 'mdi-progress-clock');
                                                    $req     = isset($openRequests[$row->school_id . '|' . $row->b_code])
                                                        ? $openRequests[$row->school_id . '|' . $row->b_code]
                                                        : null;
                                                    $rowCan  = $canAct && in_array($status, $stageFrom[$stage_action], true);
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
                                                        <?php if (!empty($row->alloc_type)) : ?>
                                                            <span class="ap-school-sub d-block mt-1"><?= html_escape($row->alloc_type); ?></span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><span class="ap-date"><?= html_escape($row->date); ?></span></td>
                                                    <td>
                                                        <span class="ap-status-pill <?= $st[1]; ?>">
                                                            <i class="mdi <?= $st[2]; ?>"></i> <?= html_escape($st[0]); ?>
                                                        </span>
                                                    </td>
                                                    <td class="text-right">
                                                        <div class="ap-actions">
                                                            <div class="btn-group">
                                                                <button type="button" class="ap-btn ap-btn-docs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                    <i class="mdi mdi-file-document-multiple-outline"></i> View
                                                                </button>
                                                                <div class="dropdown-menu dropdown-menu-right ap-menu">
                                                                    <a class="dropdown-item" target="_blank" href="<?= base_url(); ?>Page/view_sip_admin/<?= $row->school_id; ?>">
                                                                        <i class="mdi mdi-book-open-page-variant"></i> School Improvement Plan (SIP)
                                                                    </a>
                                                                    <a class="dropdown-item" target="_blank" href="<?= base_url(); ?>Page/aip_admin/<?= $docs; ?>">
                                                                        <i class="mdi mdi-clipboard-text-outline"></i> Annual Implementation Plan (AIP)
                                                                    </a>
                                                                    <a class="dropdown-item" target="_blank" href="<?= base_url(); ?>Page/generate_sop_admin/<?= $docs; ?>">
                                                                        <i class="mdi mdi-clipboard-list-outline"></i> School Operational Plan (SOP)
                                                                    </a>
                                                                    <?php if (!empty($row->app_id)) : ?>
                                                                        <a class="dropdown-item" target="_blank" href="<?= base_url(); ?>Page/generate_app_admin_sned/<?= $docs; ?>">
                                                                            <i class="mdi mdi-cart-outline"></i> Annual Procurement Plan (APP)
                                                                        </a>
                                                                    <?php else : ?>
                                                                        <span class="dropdown-item disabled" title="The school has not set its APP percentages yet">
                                                                            <i class="mdi mdi-cart-off"></i> Annual Procurement Plan (not prepared)
                                                                        </span>
                                                                    <?php endif; ?>
                                                                    <a class="dropdown-item js-rca" href="#"
                                                                       data-sid="<?= html_escape($row->school_id); ?>"
                                                                       data-fy="<?= html_escape($row->fy); ?>"
                                                                       data-bcode="<?= html_escape($row->b_code); ?>"
                                                                       data-school="<?= html_escape($name); ?>">
                                                                        <i class="mdi mdi-cash-multiple"></i> Request for Cash Advance (RCA)
                                                                    </a>
                                                                </div>
                                                            </div>

                                                            <button type="button"
                                                                    class="ap-btn ap-btn-status js-view-status"
                                                                    data-id="<?= $row->id; ?>"
                                                                    data-school="<?= html_escape($name); ?>">
                                                                <i class="mdi mdi-map-marker-path"></i> Track
                                                            </button>

                                                            <?php if (!empty($req)) : ?>
                                                                <button type="button"
                                                                        class="ap-btn ap-btn-unlock js-open-aip"
                                                                        data-submit="<?= $row->id; ?>"
                                                                        data-request="<?= $req->id; ?>"
                                                                        data-schoolid="<?= html_escape($row->school_id); ?>"
                                                                        data-school="<?= html_escape($name); ?>"
                                                                        data-bcode="<?= html_escape($row->b_code); ?>"
                                                                        data-reason="<?= html_escape($req->remarks); ?>">
                                                                    <i class="mdi mdi-lock-open-variant"></i> Unlock
                                                                </button>
                                                            <?php endif; ?>

                                                            <?php if ($rowCan) : ?>
                                                                <button type="button"
                                                                        class="ap-btn ap-btn-success ap-stage-btn js-stage-action"
                                                                        data-href="<?= base_url() . $mode['path'] . $row->id . '/' . $row->school_id; ?>"
                                                                        data-school="<?= html_escape($name); ?>"
                                                                        data-bcode="<?= html_escape($row->b_code); ?>">
                                                                    <i class="mdi <?= $mode['icon']; ?>"></i> <?= $mode['go']; ?>
                                                                </button>
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

                <?php if ($canAct) : ?>
                <!-- Stage action confirmation -->
                <div id="ap-confirm-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="apConfirmLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content ap-modal">
                            <div class="modal-header">
                                <h5 class="modal-title" id="apConfirmLabel"><i class="mdi <?= $mode['icon']; ?>"></i> <?= $mode['go']; ?>?</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            </div>
                            <div class="modal-body">
                                <p class="mb-1"><strong id="ap-confirm-school"></strong></p>
                                <p class="mb-2 text-muted">Batch <span id="ap-confirm-bcode"></span></p>
                                <p class="mb-0"><?= html_escape($mode['effect']); ?></p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="ap-btn ap-btn-ghost" data-dismiss="modal">Cancel</button>
                                <a href="#" id="ap-confirm-go" class="ap-btn ap-btn-success">
                                    <i class="mdi <?= $mode['icon']; ?>"></i> Yes, <?= $mode['go']; ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

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

                <!-- Unlock modal (same form as the Unlock Requests page) -->
                <div id="ap-open-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="apOpenLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content ap-modal">
                            <?= form_open('Page/open_aip'); ?>
                            <div class="modal-header">
                                <h5 class="modal-title" id="apOpenLabel"><i class="mdi mdi-lock-open-variant"></i> Unlock Plan</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="id" id="ap-open-submit">
                                <input type="hidden" name="r_id" id="ap-open-request">
                                <input type="hidden" name="school_id" id="ap-open-schoolid">
                                <input type="hidden" name="from" value="<?= html_escape($from); ?>">

                                <div class="ap-note ap-note-amber">
                                    <i class="mdi mdi-information-outline"></i>
                                    <div>
                                        Unlocking <strong id="ap-open-school">this plan</strong>
                                        (batch <span id="ap-open-bcode"></span>) returns it to <strong>Submitted</strong>.
                                        The school can then edit it and send it through the pipeline again.
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="ap-meta-label">School's reason</label>
                                    <div class="ap-reason" id="ap-open-reason" style="max-width:none;"></div>
                                </div>

                                <div class="form-group mb-0">
                                    <label class="ap-meta-label" for="ap-open-remarks">Your remarks</label>
                                    <textarea required name="remarks" id="ap-open-remarks" rows="3" class="form-control"
                                              placeholder="Note why the plan is being unlocked. This is recorded in the plan's tracking history."></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="ap-btn ap-btn-ghost" data-dismiss="modal">Cancel</button>
                                <button type="submit" name="submit" class="ap-btn ap-btn-unlock">
                                    <i class="mdi mdi-lock-open-variant"></i> Unlock Plan
                                </button>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Request for Cash Advance month picker -->
                <div id="ap-rca-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="apRcaLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content ap-modal">
                            <?= form_open('Page/generate_rca_admin', array('target' => '_blank')); ?>
                            <div class="modal-header">
                                <h5 class="modal-title" id="apRcaLabel"><i class="mdi mdi-cash-multiple"></i> Request for Cash Advance</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="sid" id="ap-rca-sid">
                                <input type="hidden" name="fy" id="ap-rca-fy">
                                <input type="hidden" name="bcode" id="ap-rca-bcode">

                                <p class="mb-3"><strong id="ap-rca-school">this plan</strong></p>

                                <div class="form-group mb-0">
                                    <label class="ap-meta-label" for="ap-rca-month">Month</label>
                                    <select class="form-control" name="month" id="ap-rca-month" required>
                                        <option value=""></option>
                                        <?php
                                        $month = array('January' => 'jan', 'February' => 'feb', 'March' => 'mar', 'April' => 'april', 'May' => 'may', 'June' => 'june', 'July' => 'july', 'August' => 'aug', 'September' => 'sept', 'October' => 'oct', 'November' => 'nov', 'December' => 'dec');
                                        foreach ($month as $m => $val) : ?>
                                            <option value="<?= $val; ?>"><?= $m; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="ap-btn ap-btn-ghost" data-dismiss="modal">Cancel</button>
                                <button type="submit" name="aip" value="1" class="ap-btn ap-btn-solid">
                                    <i class="mdi mdi-file-pdf-box"></i> Generate
                                </button>
                            </div>
                            </form>
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

        <!-- Shared ap-* styles live in assets/css/aip-plans.css (templates/head.php). -->
        <style>
            /* The stage action sits apart from the view/track buttons so it is never clicked by mistake. */
            .ap-stage-btn { margin-left: .5rem; }
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

                // attr() rather than data() throughout: ids are kept as strings.

                /* ---- Stage action: simple confirmation ---- */
                $(document).on('click', '.js-stage-action', function () {
                    var $b = $(this);

                    $('#ap-confirm-school').text($b.attr('data-school') || 'this plan');
                    $('#ap-confirm-bcode').text($b.attr('data-bcode') || '—');
                    $('#ap-confirm-go')
                        .attr('href', $b.attr('data-href'))
                        .removeClass('disabled')
                        .html($('#ap-confirm-go').data('label'));

                    $('#ap-confirm-modal').modal('show');
                });

                $('#ap-confirm-go').data('label', $('#ap-confirm-go').html());

                $('#ap-confirm-go').on('click', function (e) {
                    var $go = $(this);
                    if ($go.hasClass('disabled')) {
                        e.preventDefault();
                        return;
                    }
                    // One click only: the link navigates and the page reloads on the list.
                    $go.addClass('disabled').html('<i class="mdi mdi-loading mdi-spin"></i> Saving&hellip;');
                });

                /* ---- Tracking history ---- */
                $(document).on('click', '.js-view-status', function () {
                    var id     = $(this).attr('data-id');
                    var school = $(this).attr('data-school') || 'Plan Tracking';
                    var $body  = $('#ap-status-body');

                    $('#apStatusLabel').html('<i class="mdi mdi-map-marker-path"></i> ').append(document.createTextNode(school));
                    $body.html('<div class="ap-loading"><i class="mdi mdi-loading mdi-spin"></i> Loading tracking history…</div>');
                    $('#ap-status-modal').modal('show');

                    $.get('<?= base_url(); ?>Page/aip_track_modal/' + id)
                        .done(function (html) { $body.html(html); })
                        .fail(function () {
                            $body.html('<div class="ap-track-empty"><i class="mdi mdi-alert-circle-outline"></i><p>Could not load the status history. Please try again.</p></div>');
                        });
                });

                /* ---- Unlock ---- */
                $(document).on('click', '.js-open-aip', function () {
                    var $b = $(this);

                    $('#ap-open-submit').val($b.attr('data-submit'));
                    $('#ap-open-request').val($b.attr('data-request'));
                    $('#ap-open-schoolid').val($b.attr('data-schoolid'));
                    $('#ap-open-school').text($b.attr('data-school') || 'this plan');
                    $('#ap-open-bcode').text($b.attr('data-bcode') || '—');
                    $('#ap-open-reason').text($b.attr('data-reason') || '—');
                    $('#ap-open-remarks').val('');

                    $('#ap-open-modal').modal('show');
                });

                /* ---- Request for Cash Advance ---- */
                $(document).on('click', '.js-rca', function (e) {
                    e.preventDefault();
                    var $b = $(this);

                    $('#ap-rca-sid').val($b.attr('data-sid'));
                    $('#ap-rca-fy').val($b.attr('data-fy'));
                    $('#ap-rca-bcode').val($b.attr('data-bcode'));
                    $('#ap-rca-school').text($b.attr('data-school') || 'this plan');
                    $('#ap-rca-month').val('');

                    $('#ap-rca-modal').modal('show');
                });
            });
        </script>

    </body>
</html>
