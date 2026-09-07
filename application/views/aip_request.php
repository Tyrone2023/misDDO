<?php
// AIP unlock queue: schools asking to have an already-submitted plan reopened so
// they can edit and re-submit it. Worked by Admin/SMME, Plan Review, Funds and the
// SGOD Chief - $can_open comes from Page::aip_requested() and decides whether the
// "Unlock" action is offered or the page is read-only.
$canOpen = !empty($can_open);
$counts  = (isset($counts) && is_array($counts)) ? $counts : array('open' => 0, 'opened' => 0, 'total' => 0);
$opened  = isset($opened) ? $opened : array();

$openCount = count($data);
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

if (!function_exists('aip_request_stage_pill')) {
    function aip_request_stage_pill($status, $stages)
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
                                        <span class="ap-hero-eyebrow"><i class="mdi mdi-lock-open-variant-outline"></i> Unlock Requests</span>
                                        <h3 class="ap-hero-title"><?= html_escape($title); ?></h3>
                                        <p class="ap-hero-sub">
                                            Fiscal Year <strong><?= html_escape($fy); ?></strong>
                                            <span class="ap-dotsep">&bull;</span>
                                            <?php if ($canOpen) : ?>
                                                Schools asking to reopen a submitted plan for editing
                                            <?php else : ?>
                                                View&#8209;only list of schools asking to reopen a submitted plan
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                    <div class="ap-hero-stats">
                                        <div class="ap-stat">
                                            <span class="ap-stat-value"><?= number_format($openCount); ?></span>
                                            <span class="ap-stat-label">Pending</span>
                                        </div>
                                        <div class="ap-stat">
                                            <span class="ap-stat-value"><?= number_format((int) $counts['opened']); ?></span>
                                            <span class="ap-stat-label">Unlocked</span>
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
                                            <h5 class="ap-card-title"><i class="mdi mdi-clock-alert-outline"></i> Pending requests</h5>
                                            <p class="ap-card-sub">Unlocking sets the plan back to <strong>Submitted</strong> so the school can edit and re-submit it.</p>
                                        </div>
                                    </div>

                                    <?php if ($openCount === 0) : ?>
                                        <div class="ap-empty">
                                            <i class="mdi mdi-check-circle-outline"></i>
                                            <h5>No pending requests</h5>
                                            <p>No school is waiting for a plan to be unlocked for FY <?= html_escape($fy); ?>.</p>
                                        </div>
                                    <?php else : ?>
                                    <div class="table-responsive">
                                        <table id="rq-open-table" class="table ap-table dt-responsive nowrap" style="width:100%;">
                                            <thead>
                                                <tr>
                                                    <th>School</th>
                                                    <th>Budget Code</th>
                                                    <th>Plan Status</th>
                                                    <th>Requested</th>
                                                    <th>Reason</th>
                                                    <th class="text-right">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($data as $row) :
                                                    $name    = !empty($row->schoolName) ? $row->schoolName : 'Unknown school';
                                                    $initial = strtoupper(substr(trim($name), 0, 1));
                                                    $pill    = aip_request_stage_pill($row->submit_status, $stages);
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
                                                        <span class="ap-date"><?= html_escape($row->tdate); ?></span>
                                                        <div class="ap-school-sub"><?= html_escape($row->ttime); ?></div>
                                                    </td>
                                                    <td>
                                                        <div class="ap-reason"><?= html_escape($row->remarks); ?></div>
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

                                                            <?php if ($canOpen) : ?>
                                                                <button type="button"
                                                                        class="ap-btn ap-btn-unlock js-open-aip"
                                                                        data-submit="<?= $row->s_id; ?>"
                                                                        data-request="<?= $row->id; ?>"
                                                                        data-schoolid="<?= html_escape($row->school_id); ?>"
                                                                        data-school="<?= html_escape($name); ?>"
                                                                        data-bcode="<?= html_escape($row->b_code); ?>"
                                                                        data-reason="<?= html_escape($row->remarks); ?>">
                                                                    <i class="mdi mdi-lock-open-variant"></i> Unlock
                                                                </button>

                                                                <button type="button"
                                                                        class="ap-btn ap-btn-deny js-deny-aip"
                                                                        data-submit="<?= $row->s_id; ?>"
                                                                        data-request="<?= $row->id; ?>"
                                                                        data-schoolid="<?= html_escape($row->school_id); ?>"
                                                                        data-school="<?= html_escape($name); ?>"
                                                                        data-bcode="<?= html_escape($row->b_code); ?>"
                                                                        data-reason="<?= html_escape($row->remarks); ?>">
                                                                    <i class="mdi mdi-close-circle-outline"></i> Deny
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

                        <?php if (!empty($opened)) : ?>
                        <div class="row">
                            <div class="col-12">
                                <div class="ap-card">

                                    <div class="ap-card-head">
                                        <div>
                                            <h5 class="ap-card-title"><i class="mdi mdi-history"></i> Already unlocked</h5>
                                            <p class="ap-card-sub">Requests granted this fiscal year, newest first.</p>
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table id="rq-opened-table" class="table ap-table dt-responsive nowrap" style="width:100%;">
                                            <thead>
                                                <tr>
                                                    <th>School</th>
                                                    <th>Budget Code</th>
                                                    <th>Plan Status</th>
                                                    <th>Requested</th>
                                                    <th>Reason</th>
                                                    <th class="text-right">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($opened as $row) :
                                                    $name    = !empty($row->schoolName) ? $row->schoolName : 'Unknown school';
                                                    $initial = strtoupper(substr(trim($name), 0, 1));
                                                    $pill    = aip_request_stage_pill($row->submit_status, $stages);
                                                    ?>
                                                <tr>
                                                    <td>
                                                        <div class="ap-school">
                                                            <span class="ap-avatar"><?= html_escape($initial); ?></span>
                                                            <span class="ap-school-text">
                                                                <span class="ap-school-name"><?= html_escape($name); ?></span>
                                                                <span class="ap-school-sub"><?= !empty($row->district) ? html_escape($row->district) : 'No district'; ?></span>
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td><span class="ap-chip"><?= html_escape($row->b_code); ?></span></td>
                                                    <td><span class="ap-status-pill <?= $pill[1]; ?>"><i class="mdi <?= $pill[2]; ?>"></i> <?= $pill[0]; ?></span></td>
                                                    <td>
                                                        <span class="ap-date"><?= html_escape($row->tdate); ?></span>
                                                        <div class="ap-school-sub"><?= html_escape($row->ttime); ?></div>
                                                    </td>
                                                    <td><div class="ap-reason"><?= html_escape($row->remarks); ?></div></td>
                                                    <td class="text-right">
                                                        <?php if (!empty($row->s_id)) : ?>
                                                            <button type="button"
                                                                    class="ap-btn ap-btn-status js-view-status"
                                                                    data-id="<?= $row->s_id; ?>"
                                                                    data-school="<?= html_escape($name); ?>">
                                                                <i class="mdi mdi-map-marker-path"></i> Track
                                                            </button>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
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

                <?php if ($canOpen) : ?>
                <!-- Unlock modal -->
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
                                <input type="hidden" name="from" value="aip_requested">

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

                <!-- Deny modal -->
                <div id="ap-deny-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="apDenyLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content ap-modal">
                            <?= form_open('Page/deny_aip'); ?>
                            <div class="modal-header">
                                <h5 class="modal-title" id="apDenyLabel"><i class="mdi mdi-close-circle-outline"></i> Deny Unlock Request</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="id" id="ap-deny-submit">
                                <input type="hidden" name="r_id" id="ap-deny-request">
                                <input type="hidden" name="school_id" id="ap-deny-schoolid">
                                <input type="hidden" name="from" value="aip_requested">

                                <div class="ap-note ap-note-red">
                                    <i class="mdi mdi-lock-outline"></i>
                                    <div>
                                        Denying leaves <strong id="ap-deny-school">this plan</strong>
                                        (batch <span id="ap-deny-bcode"></span>) <strong>locked</strong>.
                                        The school cannot edit it, and the reason below is shown on its plan page.
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="ap-meta-label">School&#39;s reason</label>
                                    <div class="ap-reason" id="ap-deny-reason" style="max-width:none;"></div>
                                </div>

                                <div class="form-group mb-0">
                                    <label class="ap-meta-label" for="ap-deny-remarks">Reason for denial <span class="text-danger">*</span></label>
                                    <textarea required name="remarks" id="ap-deny-remarks" rows="3" class="form-control"
                                              placeholder="Explain why the request is being turned down. The school sees this text."></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="ap-btn ap-btn-ghost" data-dismiss="modal">Cancel</button>
                                <button type="submit" name="submit" class="ap-btn ap-btn-deny">
                                    <i class="mdi mdi-close-circle-outline"></i> Deny Request
                                </button>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

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
                var tableOpts = {
                    pageLength: 25,
                    lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                    order: [[3, 'desc']],
                    columnDefs: [{ orderable: false, targets: [4, 5] }],
                    language: {
                        search: '',
                        searchPlaceholder: 'Search school, budget code, district…',
                        lengthMenu: '_MENU_ per page',
                        info: 'Showing _START_ to _END_ of _TOTAL_ requests',
                        infoEmpty: 'No requests',
                        zeroRecords: 'No matching requests found'
                    }
                };

                $('#rq-open-table, #rq-opened-table').each(function () {
                    if (!$.fn.DataTable.isDataTable(this)) {
                        $(this).DataTable(tableOpts);
                    }
                });

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

                $(document).on('click', '.js-deny-aip', function () {
                    var $b = $(this);

                    $('#ap-deny-submit').val($b.data('submit'));
                    $('#ap-deny-request').val($b.data('request'));
                    $('#ap-deny-schoolid').val($b.data('schoolid'));
                    $('#ap-deny-school').text($b.data('school') || 'this plan');
                    $('#ap-deny-bcode').text($b.data('bcode') || '—');
                    $('#ap-deny-reason').text($b.data('reason') || '—');
                    $('#ap-deny-remarks').val('');

                    $('#ap-deny-modal').modal('show');
                });

                $(document).on('click', '.js-open-aip', function () {
                    var $b = $(this);

                    $('#ap-open-submit').val($b.data('submit'));
                    $('#ap-open-request').val($b.data('request'));
                    $('#ap-open-schoolid').val($b.data('schoolid'));
                    $('#ap-open-school').text($b.data('school') || 'this plan');
                    $('#ap-open-bcode').text($b.data('bcode') || '—');
                    $('#ap-open-reason').text($b.data('reason') || '—');
                    $('#ap-open-remarks').val('');

                    $('#ap-open-modal').modal('show');
                });
            });
        </script>

    </body>
</html>
