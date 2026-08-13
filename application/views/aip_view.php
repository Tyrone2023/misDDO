<?php
// The school's own Annual Implementation Plan for the allocation batch selected on
// Page/implementation_plans (kept in $_SESSION['aip'] / $_SESSION['fy']).
//
// Everything on this page keys off $aip_s, the sgod_aip_submit row for that batch:
//   null    -> nothing submitted yet, the plan is editable
//   status 0 -> Submitted (or unlocked again after an approved request) - editable
//   status 3 -> AIP Reviewed      | status 4 -> Funds Available
//   status 1 -> Approved          | status 2/6 -> submitted on the SNED / SBFP track
// Once a plan has left status 0 it is read-only, so the school asks for it to be
// unlocked; that request is what Page/aip_request records.

$aip_s   = isset($aip_s) ? $aip_s : null;
$aip_r   = isset($aip_r) ? $aip_r : null;
$alloc   = isset($alloc) ? $alloc : null;
$data    = isset($data) ? $data : array();
$fy      = isset($fy) ? $fy : (isset($_SESSION['fy']) ? $_SESSION['fy'] : '');
$bcode   = isset($bcode) ? $bcode : (isset($_SESSION['aip']) ? $_SESSION['aip'] : '');

$submitted = !empty($aip_s);
$status    = $submitted ? (int) $aip_s->status : null;

// A plan is editable before it is submitted and while it sits at status 0 - which is
// also where an approved unlock request puts it back.
$canEdit = !$submitted || $status === 0;

// Pipeline stages in order, with the sgod_aip_submit.status each one corresponds to.
$pipeline = array(
    array('status' => 0, 'label' => 'Submitted',       'icon' => 'mdi-send'),
    array('status' => 3, 'label' => 'AIP Reviewed',    'icon' => 'mdi-file-find'),
    array('status' => 4, 'label' => 'Funds Available', 'icon' => 'mdi-cash-multiple'),
    array('status' => 1, 'label' => 'Approved',        'icon' => 'mdi-check-decagram'),
);

// How far along the pipeline the plan is (index into $pipeline), and the pill shown
// in the hero. Status 2 (SNED) and 6 (SBFP) are submissions on their own track.
$stageIndex = -1;
$pill = array('Not Submitted', 'ap-pill-grey', 'mdi-file-outline');

if ($submitted) {
    switch ($status) {
        case 1:
            $stageIndex = 3;
            $pill = array('Approved', 'ap-pill-green', 'mdi-check-decagram');
            break;
        case 4:
            $stageIndex = 2;
            $pill = array('Funds Available', 'ap-pill-amber', 'mdi-cash-multiple');
            break;
        case 3:
            $stageIndex = 1;
            $pill = array('AIP Reviewed', 'ap-pill-sky', 'mdi-file-find');
            break;
        default:
            $stageIndex = 0;
            // update_aip_open() stamps this label when a plan is reopened for editing.
            $pill = (trim($aip_s->remarks) === 'Unlocked for Editing')
                ? array('Unlocked for Editing', 'ap-pill-red', 'mdi-lock-open-variant')
                : array('Submitted', 'ap-pill-blue', 'mdi-send');
            break;
    }
}

// Unlock request state for this batch. The original rules are kept: no new request
// while one is pending, and at most three granted requests per batch.
$requestPending  = !empty($aip_r) && (int) $aip_r->stat === 0;
$grantedRequests = 0;
if (!empty($aip_r) && !$requestPending) {
    $grantedRequests = $this->SGODModel
        ->count_all_two_cond('sgod_aip_request', 'stat', 1, 'b_code', $bcode)
        ->num_rows();
}
$requestCapReached = !empty($aip_r) && !$requestPending && $grantedRequests > 2;
$canRequestUnlock  = $submitted && !$requestPending && !$requestCapReached;

// Which submit action applies depends on the fund the batch was allocated from.
$allocType  = isset($alloc->alloc_type) ? $alloc->alloc_type : '';
$submitLink = 'Page/submit_aip';
if ($allocType == 'SNED Fund') {
    $submitLink = 'Page/submit_aip_sned';
} elseif ($allocType == 'SBFP Fund') {
    $submitLink = 'Page/submit_aip_sbfp';
}

$totalBudget = 0;
foreach ($data as $row) {
    $totalBudget += (float) str_replace(array(',', ' '), '', $row->budget);
}
?>

            <!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->

            <div class="content-page">
                <div class="content">

                    <!-- Start Content-->
                    <div class="container-fluid">

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

                                <!-- start page header -->
                                <div class="ap-hero ap-hero-indigo">
                                    <div class="ap-hero-text">
                                        <span class="ap-hero-eyebrow"><i class="mdi mdi-notebook-multiple"></i> Implementation Plan</span>
                                        <h3 class="ap-hero-title"><?= html_escape($title); ?></h3>
                                        <p class="ap-hero-sub">
                                            Batch <strong><?= html_escape($bcode); ?></strong>
                                            <span class="ap-dotsep">&bull;</span> Fiscal Year <strong><?= html_escape($fy); ?></strong>
                                            <?php if (!empty($alloc->alloc_group)) : ?>
                                                <span class="ap-dotsep">&bull;</span> <?= html_escape($alloc->alloc_group); ?>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                    <div class="ap-hero-stats">
                                        <div class="ap-stat">
                                            <span class="ap-stat-value"><?= number_format(count($data)); ?></span>
                                            <span class="ap-stat-label">Activities</span>
                                        </div>
                                        <div class="ap-stat">
                                            <span class="ap-stat-value"><?= number_format($totalBudget, 2); ?></span>
                                            <span class="ap-stat-label">Planned Budget</span>
                                        </div>
                                        <?php if (!empty($alloc->alloc_amount)) : ?>
                                            <div class="ap-stat">
                                                <span class="ap-stat-value"><?= number_format((float) $alloc->alloc_amount, 2); ?></span>
                                                <span class="ap-stat-label">Allocation</span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <!-- end page header -->
                            </div>
                        </div>

                        <!-- ===== Plan status ===== -->
                        <div class="row">
                            <div class="col-12">
                                <div class="ap-card">

                                    <div class="ap-card-head">
                                        <div>
                                            <h5 class="ap-card-title">
                                                <i class="mdi mdi-progress-check"></i> Status:
                                                <span class="ap-status-pill ap-status-pill-lg <?= $pill[1]; ?>">
                                                    <i class="mdi <?= $pill[2]; ?>"></i> <?= $pill[0]; ?>
                                                </span>
                                            </h5>
                                            <p class="ap-card-sub">
                                                <?php if (!$submitted) : ?>
                                                    This plan has not been submitted for evaluation yet.
                                                <?php elseif ($canEdit) : ?>
                                                    Submitted on <?= html_escape($aip_s->date); ?>. The plan is open for editing.
                                                <?php else : ?>
                                                    Submitted on <?= html_escape($aip_s->date); ?>. The plan is locked - request an unlock to edit it.
                                                <?php endif; ?>
                                            </p>
                                        </div>

                                        <div class="ap-actions">
                                            <?php if ($submitted) : ?>
                                                <button type="button" class="ap-btn ap-btn-status js-view-status"
                                                        data-id="<?= $aip_s->id; ?>">
                                                    <i class="mdi mdi-map-marker-path"></i> Track Plan
                                                </button>

                                                <?php if ($canRequestUnlock) : ?>
                                                    <button type="button" class="ap-btn ap-btn-unlock" data-toggle="modal" data-target="#ap-request-modal">
                                                        <i class="mdi mdi-lock-open-variant"></i> Request for Unlock
                                                    </button>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <?php if ($submitted) : ?>
                                        <div class="ap-steps mb-2">
                                            <?php foreach ($pipeline as $i => $step) : ?>
                                                <?php if ($i > 0) : ?><span class="ap-step-sep"><i class="mdi mdi-chevron-right"></i></span><?php endif; ?>
                                                <span class="ap-step <?= $i < $stageIndex ? 'is-done' : ($i === $stageIndex ? 'is-current' : ''); ?>">
                                                    <i class="mdi <?= $i < $stageIndex ? 'mdi-check' : $step['icon']; ?>"></i> <?= $step['label']; ?>
                                                </span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($requestPending) : ?>
                                        <div class="ap-note ap-note-amber mt-3 mb-0">
                                            <i class="mdi mdi-clock-alert-outline"></i>
                                            <div>
                                                <strong>Unlock request pending.</strong>
                                                Sent <?= html_escape($aip_r->tdate); ?> at <?= html_escape($aip_r->ttime); ?>.
                                                You will be able to edit this plan once it is approved.
                                                <div class="mt-1"><em><?= html_escape($aip_r->remarks); ?></em></div>
                                            </div>
                                        </div>
                                    <?php elseif ($requestCapReached) : ?>
                                        <div class="ap-note mt-3 mb-0">
                                            <i class="mdi mdi-lock-outline"></i>
                                            <div>
                                                <strong>No more unlock requests for this batch.</strong>
                                                This plan has already been unlocked <?= (int) $grantedRequests; ?> times. Contact the SGOD Planning Unit if you still need to make a change.
                                            </div>
                                        </div>
                                    <?php elseif ($submitted && !$canEdit) : ?>
                                        <div class="ap-note ap-note-blue mt-3 mb-0">
                                            <i class="mdi mdi-information-outline"></i>
                                            <div>
                                                Editing is closed while the plan moves through review. Use <strong>Request for Unlock</strong> to ask the SGOD Planning Unit to reopen it.
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <div class="ap-meta-grid mt-3">
                                        <div class="ap-meta">
                                            <span class="ap-meta-label">Batch Code</span>
                                            <span class="ap-meta-value"><?= html_escape($bcode); ?></span>
                                        </div>
                                        <div class="ap-meta">
                                            <span class="ap-meta-label">Fiscal Year</span>
                                            <span class="ap-meta-value"><?= html_escape($fy); ?></span>
                                        </div>
                                        <div class="ap-meta">
                                            <span class="ap-meta-label">Fund Type</span>
                                            <span class="ap-meta-value"><?= $allocType !== '' ? html_escape($allocType) : '&mdash;'; ?></span>
                                        </div>
                                        <div class="ap-meta">
                                            <span class="ap-meta-label">Group</span>
                                            <span class="ap-meta-value"><?= !empty($alloc->alloc_group) ? html_escape($alloc->alloc_group) : '&mdash;'; ?></span>
                                        </div>
                                        <div class="ap-meta">
                                            <span class="ap-meta-label">Allocation</span>
                                            <span class="ap-meta-value"><?= !empty($alloc->alloc_amount) ? number_format((float) $alloc->alloc_amount, 2) : '&mdash;'; ?></span>
                                        </div>
                                        <div class="ap-meta">
                                            <span class="ap-meta-label">Planned Budget</span>
                                            <span class="ap-meta-value"><?= number_format($totalBudget, 2); ?></span>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <!-- ===== Actions ===== -->
                        <div class="row">
                            <div class="col-12">
                                <div class="ap-toolbar">
                                    <?php if ($canEdit) : ?>
                                        <a class="ap-btn ap-btn-solid" href="<?= base_url() . 'Page/' . $b_link; ?>">
                                            <i class="mdi mdi-plus"></i> <?= html_escape(trim(str_replace('+', '', $b_label))); ?>
                                        </a>
                                    <?php endif; ?>

                                    <a class="ap-btn" target="_blank" href="<?= base_url(); ?>Page/generate_aip">
                                        <i class="mdi mdi-file-document-outline"></i> Generate AIP
                                    </a>

                                    <?php if (!$submitted) : ?>
                                        <?php if (empty($data)) : ?>
                                            <span class="ap-btn disabled" title="Add at least one activity before submitting">
                                                <i class="mdi mdi-send-outline"></i> Submit for Evaluation
                                            </span>
                                        <?php else : ?>
                                            <a class="ap-btn ap-btn-success" onclick="return confirm('Submit this plan for evaluation? You will not be able to edit it afterwards without an approved unlock request.')"
                                               href="<?= base_url() . $submitLink; ?>">
                                                <i class="mdi mdi-send-outline"></i> Submit for Evaluation
                                            </a>
                                        <?php endif; ?>
                                    <?php else : ?>
                                        <a class="ap-btn" href="<?= base_url(); ?>Page/aip_action_list">
                                            <i class="mdi mdi-clipboard-check-outline"></i> Submitted AIP
                                        </a>
                                    <?php endif; ?>

                                    <?php if ($status === 1) : ?>
                                        <a class="ap-btn" target="_blank"
                                           href="<?= base_url(); ?>page/generate_ca/<?= $this->session->username; ?>/<?= $bcode; ?>/<?= $aip_s->id; ?>">
                                            <i class="mdi mdi-certificate-outline"></i> Certificate of Acceptance
                                        </a>
                                    <?php endif; ?>

                                    <a class="ap-btn" href="<?= base_url(); ?>Page/sop"><i class="mdi mdi-clipboard-list-outline"></i> SOP</a>
                                    <a class="ap-btn" href="<?= base_url(); ?>Page/view_app"><i class="mdi mdi-cart-outline"></i> APP</a>
                                    <a class="ap-btn" href="<?= base_url(); ?>Page/smeav2"><i class="mdi mdi-chart-box-outline"></i> SMEA</a>
                                    <a class="ap-btn" href="<?= base_url(); ?>Page/implementation_plans"><i class="mdi mdi-swap-horizontal"></i> Change Batch</a>
                                </div>
                            </div>
                        </div>

                        <!-- ===== Activities ===== -->
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="ap-card">

                                    <div class="ap-card-head">
                                        <div>
                                            <h5 class="ap-card-title"><i class="mdi mdi-format-list-bulleted"></i> Planned Activities</h5>
                                            <p class="ap-card-sub">
                                                <?= number_format(count($data)); ?> activit<?= count($data) === 1 ? 'y' : 'ies'; ?>
                                                <span class="ap-dotsep">&bull;</span> PHP <?= number_format($totalBudget, 2); ?> planned
                                            </p>
                                        </div>
                                    </div>

                                    <?php if (empty($data)) : ?>
                                        <div class="ap-empty">
                                            <i class="mdi mdi-clipboard-text-outline"></i>
                                            <h5>No activities yet</h5>
                                            <p>
                                                <?php if ($canEdit) : ?>
                                                    Add the school improvement activities for batch <?= html_escape($bcode); ?> to build this plan.
                                                <?php else : ?>
                                                    Nothing was recorded for batch <?= html_escape($bcode); ?>.
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                    <?php else : ?>
                                    <div class="table-responsive">
                                        <table id="datatable" class="table ap-table dt-responsive nowrap" style="width:100%;">
                                            <thead>
                                                <tr>
                                                    <th>SCHOOL IMPROVEMENT PROJECT TITLE</th>
                                                    <th>STRATEGY ACTIVITIES</th>
                                                    <th>PILLAR</th>
                                                    <th>DOMAIN</th>
                                                    <th>STRAND</th>
                                                    <th>PIA's</th>
                                                    <th>PROJECT OBJECTIVE</th>
                                                    <th>OUTPUT FOR THE YEAR</th>
                                                    <th>PERFORMANCE INDICATORS</th>
                                                    <th>MOVs</th>
                                                    <th>PERSON(S) RESPONSIBLE</th>
                                                    <th>SCHEDULE</th>
                                                    <th>VENUE</th>
                                                    <th>BUDGET PER ACTIVITY</th>
                                                    <th>BUDGET SOURCE</th>
                                                    <th>MATERIALS</th>
                                                    <th>BATCH CODE</th>
                                                    <th>FISCAL YEAR</th>
                                                    <th>MANAGE</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($data as $row) : ?>
                                                <tr>
                                                    <td><?= $row->sip_project; ?></td>
                                                    <td><?= $row->strategy; ?></td>
                                                    <td><?= $row->pillar; ?></td>
                                                    <td><?= $row->domain; ?></td>
                                                    <td><?= $row->strand; ?></td>
                                                    <td><?= $row->pia; ?></td>
                                                    <td><?= $row->sip_pObjective; ?></td>
                                                    <td><?= $row->sip_output; ?></td>
                                                    <td><?= $row->pi; ?></td>
                                                    <td><?= $row->movs; ?></td>
                                                    <td><?= $row->pr; ?></td>
                                                    <td><?= $row->schedule; ?></td>
                                                    <td><?= $row->venue; ?></td>
                                                    <td><?= $row->budget; ?></td>
                                                    <td><?= $row->budget_source; ?></td>
                                                    <td><?= $row->materials; ?></td>
                                                    <td><span class="ap-chip"><?= $row->b_code; ?></span></td>
                                                    <td><?= $row->fy; ?></td>
                                                    <td>
                                                        <?php if ($canEdit) : ?>
                                                            <div class="ap-actions">
                                                                <a class="ap-btn" href="<?= base_url(); ?>Page/aip_edit/<?= $row->id; ?>">
                                                                    <i class="mdi mdi-pencil-outline"></i> Edit
                                                                </a>
                                                                <a class="ap-btn ap-btn-danger" onclick="return confirm('Delete this activity?')"
                                                                   href="<?= base_url(); ?>Page/aip_delete/<?= $row->id; ?>">
                                                                    <i class="mdi mdi-trash-can-outline"></i> Delete
                                                                </a>
                                                            </div>
                                                        <?php else : ?>
                                                            <span class="ap-muted"><i class="mdi mdi-lock-outline"></i> Locked</span>
                                                        <?php endif; ?>
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

                <!-- Tracking modal. Same fragment the SGOD worklists use; the full page
                     is still available at Page/aip_track/<submit id>. -->
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
                                <?php if ($submitted) : ?>
                                    <a class="ap-btn" href="<?= base_url(); ?>Page/aip_track/<?= $aip_s->id; ?>">
                                        <i class="mdi mdi-open-in-new"></i> Open full history
                                    </a>
                                <?php endif; ?>
                                <button type="button" class="ap-btn ap-btn-ghost" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if ($canRequestUnlock) : ?>
                <!-- Request for Unlock -->
                <div id="ap-request-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="apRequestLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content ap-modal">
                            <?= form_open('Page/aip_request'); ?>
                            <div class="modal-header">
                                <h5 class="modal-title" id="apRequestLabel"><i class="mdi mdi-lock-open-variant"></i> Request for Unlock</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="id" value="<?= $aip_s->id; ?>">
                                <input type="hidden" name="school_id" value="<?= html_escape($aip_s->school_id); ?>">

                                <div class="ap-note ap-note-blue">
                                    <i class="mdi mdi-information-outline"></i>
                                    <div>
                                        Asking to reopen batch <strong><?= html_escape($bcode); ?></strong> (FY <?= html_escape($fy); ?>),
                                        currently <strong><?= $pill[0]; ?></strong>. The SGOD Planning Unit reviews every request.
                                    </div>
                                </div>

                                <div class="form-group mb-0">
                                    <label class="ap-meta-label" for="ap-request-remarks">Reason for the request</label>
                                    <textarea name="remarks" id="ap-request-remarks" rows="4" required class="form-control"
                                              placeholder="Explain what needs to be corrected, e.g. wrong budget source, missing activity."></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="ap-btn ap-btn-ghost" data-dismiss="modal">Cancel</button>
                                <button type="submit" name="submit" class="ap-btn ap-btn-unlock">
                                    <i class="mdi mdi-send-outline"></i> Send Request
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
                if ($('#datatable').length && !$.fn.DataTable.isDataTable('#datatable')) {
                    $('#datatable').DataTable({
                        pageLength: 25,
                        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                        order: [],
                        columnDefs: [{ orderable: false, targets: -1 }],
                        language: {
                            search: '',
                            searchPlaceholder: 'Search activities…',
                            lengthMenu: '_MENU_ per page',
                            info: 'Showing _START_ to _END_ of _TOTAL_ activities',
                            infoEmpty: 'No activities',
                            zeroRecords: 'No matching activities found'
                        }
                    });
                }

                var $body = $('#ap-status-body');

                $(document).on('click', '.js-view-status', function () {
                    var id = $(this).data('id');

                    $body.html('<div class="ap-loading"><i class="mdi mdi-loading mdi-spin"></i> Loading tracking history…</div>');
                    $('#ap-status-modal').modal('show');

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
