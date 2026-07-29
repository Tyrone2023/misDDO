<?php
/**
 * SMEA (School Monitoring, Evaluation and Adjustment) listing.
 * The table shows only the key columns; every remaining field of the
 * activity is rendered into a per-row template and shown in a modal.
 */

// [aip_id][type] => sgod_sop row, prepared by the controller.
$sop_map = isset($sop_map) ? $sop_map : [];

// Prints a value, escaped, with a dash placeholder when empty.
$val = function ($v) {
    $v = trim((string) $v);
    return $v === '' ? '<span class="text-muted">&mdash;</span>' : nl2br(html_escape($v));
};

// Prints a number formatted with 2 decimals when numeric, otherwise the raw text.
$num = function ($v) {
    $v = trim((string) $v);
    if ($v === '') {
        return '<span class="text-muted">&mdash;</span>';
    }
    return is_numeric($v) ? number_format((float) $v, 2) : html_escape($v);
};

$pillar_class = function ($p) {
    switch (strtoupper(trim((string) $p))) {
        case 'ACCESS':            return 'badge-light-primary';
        case 'EQUITY':            return 'badge-light-purple';
        case 'QUALITY':           return 'badge-light-success';
        case 'RESILIENCY':        return 'badge-light-warning';
        case 'ENABLING MECHANISM':return 'badge-light-info';
        default:                  return 'badge-light-secondary';
    }
};

// Labels for the three sgod_sop target types.
$sop_types = [
    '1' => 'Physical Target',
    '2' => 'Financial Target (MOOE)',
    '3' => 'Financial Target (Other Sources of Fund)',
];

$total_budget = 0;
$total_rows   = 0;
foreach ($data as $r) {
    $total_budget += (float) $r->budget;
    $total_rows++;
}
$is_submitted = ($smea->num_rows() > 0);

// Division-office decision on this batch, from the controller. `locked` is TRUE while
// the school may not edit; "For Compliance" reopens the report and asks for a re-submit.
$smea_row  = isset($smea_row) ? $smea_row : null;
$is_locked = isset($locked) ? $locked : $is_submitted;
$smea_status = $smea_row
    ? (trim((string) $smea_row->status) !== '' ? $smea_row->status : 'Submitted')
    : '';

$status_badges = array(
    'Submitted'      => array('badge-info',    'mdi-check-decagram', 'Submitted'),
    'SDO Validated'  => array('badge-success', 'mdi-shield-check',   'SDO Validated'),
    'For Compliance' => array('badge-warning', 'mdi-alert-outline',  'For Compliance'),
);
?>

<style>
    /* ---- SMEA page ---- */
    .smea-toolbar .btn { margin: 0 .35rem .5rem 0; }

    .smea-stat {
        border: 1px solid #eef2f7;
        border-radius: .3rem;
        padding: .85rem 1rem;
        height: 100%;
    }
    .smea-stat .smea-stat-label {
        font-size: .7rem;
        text-transform: uppercase;
        letter-spacing: .05em;
        color: #98a6ad;
        margin-bottom: .2rem;
    }
    .smea-stat .smea-stat-value { font-size: 1.15rem; font-weight: 600; margin: 0; }

    #smea-datatable { width: 100% !important; }
    #smea-datatable td, #smea-datatable th { vertical-align: middle; }
    #smea-datatable thead th {
        font-size: .72rem;
        text-transform: uppercase;
        letter-spacing: .04em;
        white-space: nowrap;
    }
    #smea-datatable td { font-size: .8125rem; }
    .smea-project { font-weight: 600; color: #343a40; }
    .smea-sub { font-size: .75rem; color: #98a6ad; }
    .smea-clamp {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .col-project { min-width: 260px; max-width: 380px; }
    .col-class   { min-width: 150px; }
    .col-sched   { min-width: 130px; }

    /* ---- details modal ---- */
    .detail-section-title {
        font-size: .72rem;
        text-transform: uppercase;
        letter-spacing: .06em;
        font-weight: 600;
        color: #6c757d;
        border-bottom: 1px solid #eef2f7;
        padding-bottom: .35rem;
        margin: 1.25rem 0 .85rem;
    }
    .detail-section-title:first-of-type { margin-top: .25rem; }
    .detail-item { margin-bottom: 1rem; }
    .detail-label {
        display: block;
        font-size: .7rem;
        text-transform: uppercase;
        letter-spacing: .04em;
        color: #98a6ad;
        margin-bottom: .15rem;
    }
    .detail-value { font-size: .875rem; color: #343a40; word-break: break-word; }
    .detail-value.lead-value { font-size: 1rem; font-weight: 600; }
    .sop-card { border: 1px solid #eef2f7; border-radius: .3rem; margin-bottom: 1rem; }
    .sop-card .sop-card-head {
        padding: .5rem .75rem;
        background: #f7f9fc;
        border-bottom: 1px solid #eef2f7;
        font-weight: 600;
        font-size: .8rem;
    }
    .sop-card table { margin-bottom: 0; font-size: .8125rem; }
</style>

<!-- ============================================================== -->
<!-- Start Page Content here -->
<!-- ============================================================== -->

<div class="content-page">
    <div class="content">

        <!-- Start Content-->
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <?php if ($this->session->flashdata('success')) : ?>
                            <?= '<div class="alert alert-success alert-dismissible fade show" role="alert">
                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>'
                                . $this->session->flashdata('success') .
                                '</div>'; ?>
                        <?php endif; ?>

                        <?php if ($this->session->flashdata('danger')) : ?>
                            <?= '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>'
                                . $this->session->flashdata('danger') .
                                '</div>'; ?>
                        <?php endif; ?>

                        <div class="page-title-right">
                            <ol class="breadcrumb p-0 m-0">
                                <li class="breadcrumb-item"><a href="<?= base_url(); ?>Page/aip">AIP</a></li>
                                <li class="breadcrumb-item"><a href="<?= base_url(); ?>Page/view_app">APP</a></li>
                                <li class="breadcrumb-item active"><a href="<?= base_url(); ?>Page/smeav2">SMEA</a></li>
                            </ol>
                        </div>

                        <h4 class="page-title" id="myLargeModalLabel"><?= $title; ?></h4>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <!-- action toolbar -->
            <div class="row">
                <div class="col-12">
                    <div class="smea-toolbar mb-2">
                        <a data-toggle="modal" class="open-AddBookDialog btn btn-primary waves-effect waves-light" href="#sop">
                            <i class="mdi mdi-file-document-outline mr-1"></i>SMEA Report
                        </a>
                        <a target="_blank" class="btn btn-purple waves-effect waves-light" href="<?= base_url(); ?>Page/smea_summary">
                            <i class="mdi mdi-chart-bar mr-1"></i>SMEA Summary
                        </a>

                        <?php if (!$is_locked) { ?>
                            <a class="btn btn-warning waves-effect waves-light"
                                onclick="return confirm('<?= $is_submitted ? 'Send this SMEA back to the division office for validation?' : 'Submit this SMEA to the division office? It can no longer be edited afterwards.'; ?>')"
                                href="<?= base_url(); ?>Page/submit_smea/<?= $this->session->username; ?>/<?= $_SESSION['aip']; ?>/<?= $_SESSION['fy']; ?>">
                                <i class="mdi mdi-send mr-1"></i><?= $is_submitted ? 'Re-submit SMEA' : 'Submit SMEA'; ?>
                            </a>
                        <?php } ?>

                        <div class="btn-group">
                            <button type="button" class="btn btn-secondary dropdown-toggle waves-effect" data-toggle="dropdown" aria-expanded="false">
                                Qualitative Data <i class="mdi mdi-chevron-down"></i>
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="<?= base_url(); ?>Page/innovations">Innovations</a>
                                <a class="dropdown-item" href="<?= base_url(); ?>Page/quick_wins">Quick Wins and Best Practices</a>
                                <a class="dropdown-item" href="<?= base_url(); ?>Page/policy">Policy Issues &amp; Updates</a>
                            </div>
                        </div>

                        <a class="btn btn-primary waves-effect waves-light" href="<?= base_url(); ?>Page/adjustment">
                            <i class="mdi mdi-tune mr-1"></i>SOP Adjustment
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">

                            <!-- summary strip -->
                            <div class="row mb-3">
                                <div class="col-md-4 mb-2">
                                    <div class="smea-stat">
                                        <div class="smea-stat-label">Total Activities</div>
                                        <p class="smea-stat-value"><?= number_format($total_rows); ?></p>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <div class="smea-stat">
                                        <div class="smea-stat-label">Total Budget</div>
                                        <p class="smea-stat-value">&#8369; <?= number_format($total_budget, 2); ?></p>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <div class="smea-stat">
                                        <div class="smea-stat-label">Submission Status</div>
                                        <p class="smea-stat-value">
                                            <?php if (isset($status_badges[$smea_status])) {
                                                $badge = $status_badges[$smea_status]; ?>
                                                <span class="badge <?= $badge[0]; ?>"><i class="mdi <?= $badge[1]; ?> mr-1"></i><?= $badge[2]; ?></span>
                                            <?php } else { ?>
                                                <span class="badge badge-warning">Not yet submitted</span>
                                            <?php } ?>
                                            <span class="smea-sub ml-1">Batch <?= html_escape($_SESSION['aip']); ?></span>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <?php if ($smea_status === 'For Compliance') { ?>
                                <div class="alert alert-warning" role="alert">
                                    <h5 class="alert-heading mb-1"><i class="mdi mdi-alert-outline mr-1"></i>Returned for compliance</h5>
                                    <p class="mb-1">
                                        The division office returned this SMEA. Update the accomplishments it flags,
                                        then use <strong>Re-submit SMEA</strong> above.
                                    </p>
                                    <?php if (trim((string) $smea_row->sdo_remarks) !== '') { ?>
                                        <hr class="my-2">
                                        <p class="mb-0" style="white-space: pre-wrap;"><?= html_escape($smea_row->sdo_remarks); ?></p>
                                    <?php } ?>
                                    <?php if (!empty($smea_row->date_validated)) { ?>
                                        <small class="text-muted d-block mt-2">
                                            Returned by <?= html_escape(trim((string) $smea_row->validated_by) !== '' ? $smea_row->validated_by : 'the division office'); ?>
                                            on <?= html_escape(date('F d, Y g:i A', strtotime($smea_row->date_validated))); ?>
                                        </small>
                                    <?php } ?>
                                </div>
                            <?php } elseif ($smea_status === 'SDO Validated') { ?>
                                <div class="alert alert-success" role="alert">
                                    <i class="mdi mdi-shield-check mr-1"></i>
                                    This SMEA has been <strong>validated by the division office</strong> and is final. It can no longer be edited.
                                </div>
                            <?php } elseif ($smea_status === 'Submitted') { ?>
                                <div class="alert alert-info" role="alert">
                                    <i class="mdi mdi-clock-outline mr-1"></i>
                                    Submitted and waiting for the division office to validate. It can no longer be edited
                                    unless it is returned for compliance.
                                </div>
                            <?php } ?>

                            <h4 class="header-title mb-1">Activities for Monitoring &amp; Evaluation</h4>
                            <p class="smea-sub mb-3">
                                Only the key columns are listed below &mdash; click
                                <span class="badge badge-light-info">Details</span> on a row to view the complete record.
                            </p>

                            <div class="table-responsive">
                                <table id="smea-datatable" class="table table-hover table-centered mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th style="width:45px;">#</th>
                                            <th class="col-project">PROJECT / ACTIVITY</th>
                                            <th class="col-class">CLASSIFICATION</th>
                                            <th class="col-sched">SCHEDULE / VENUE</th>
                                            <th class="text-right">BUDGET</th>
                                            <th>SOURCE</th>
                                            <th>FY</th>
                                            <th class="text-center" style="width:90px;">DETAILS</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $i = 1;
                                        foreach ($data as $row) { ?>
                                            <tr>
                                                <td class="text-muted"><?= $i++; ?></td>
                                                <td class="col-project">
                                                    <div class="smea-project smea-clamp" title="<?= html_escape($row->sip_project); ?>">
                                                        <?= $val($row->sip_project); ?>
                                                    </div>
                                                    <?php if (trim((string) $row->strategy) !== '') { ?>
                                                        <div class="smea-sub smea-clamp" title="<?= html_escape($row->strategy); ?>">
                                                            <?= html_escape($row->strategy); ?>
                                                        </div>
                                                    <?php } ?>
                                                </td>
                                                <td class="col-class">
                                                    <span class="badge <?= $pillar_class($row->pillar); ?>">
                                                        <?= html_escape($row->pillar) !== '' ? html_escape($row->pillar) : 'N/A'; ?>
                                                    </span>
                                                    <div class="smea-sub mt-1 smea-clamp" title="<?= html_escape($row->domain . ' / ' . $row->strand); ?>">
                                                        <?= html_escape($row->domain); ?>
                                                    </div>
                                                </td>
                                                <td class="col-sched">
                                                    <?= $val($row->schedule); ?>
                                                    <div class="smea-sub"><?= html_escape($row->venue); ?></div>
                                                </td>
                                                <td class="text-right" data-order="<?= (float) $row->budget; ?>">
                                                    <?= number_format((float) $row->budget, 2); ?>
                                                </td>
                                                <td><span class="smea-sub"><?= html_escape($row->budget_source); ?></span></td>
                                                <td><span class="badge badge-light-secondary"><?= html_escape($row->fy); ?></span></td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-sm btn-outline-info smea-view-details"
                                                        data-detail-id="<?= $row->id; ?>">
                                                        <i class="mdi mdi-eye-outline"></i> View
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>

                </div>

            </div>
            <!--- end row -->

        </div>
        <!-- end container-fluid -->

    </div>

    <!-- ============================================================== -->
    <!-- Hidden per-row detail templates (cloned into the modal) -->
    <!-- ============================================================== -->
    <div id="smea-detail-store" style="display:none;">
        <?php foreach ($data as $row) {
            // Target / accomplishment rows for this activity, keyed by type (from the controller).
            $sop_by_type = isset($sop_map[(string) $row->id]) ? $sop_map[(string) $row->id] : [];
            ?>
            <div class="smea-detail-tpl" data-detail-id="<?= $row->id; ?>"
                data-title="<?= html_escape($row->sip_project !== '' ? $row->sip_project : 'SMEA Activity'); ?>">

                <div class="mb-2">
                    <span class="badge <?= $pillar_class($row->pillar); ?>"><?= html_escape($row->pillar); ?></span>
                    <span class="badge badge-light-secondary">FY <?= html_escape($row->fy); ?></span>
                    <span class="badge badge-light-secondary">Batch <?= html_escape($row->b_code); ?></span>
                </div>

                <div class="detail-section-title">Classification</div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="detail-item"><span class="detail-label">Pillar</span>
                            <div class="detail-value"><?= $val($row->pillar); ?></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="detail-item"><span class="detail-label">Domain</span>
                            <div class="detail-value"><?= $val($row->domain); ?></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="detail-item"><span class="detail-label">Strand</span>
                            <div class="detail-value"><?= $val($row->strand); ?></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="detail-item"><span class="detail-label">PIA's</span>
                            <div class="detail-value"><?= $val($row->pia); ?></div>
                        </div>
                    </div>
                </div>

                <div class="detail-section-title">Project Information</div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="detail-item"><span class="detail-label">School Improvement Project Title</span>
                            <div class="detail-value lead-value"><?= $val($row->sip_project); ?></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item"><span class="detail-label">Project Objective</span>
                            <div class="detail-value"><?= $val($row->sip_pObjective); ?></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item"><span class="detail-label">Output for the Year</span>
                            <div class="detail-value"><?= $val($row->sip_output); ?></div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="detail-item"><span class="detail-label">Strategy / Activities</span>
                            <div class="detail-value"><?= $val($row->strategy); ?></div>
                        </div>
                    </div>
                </div>

                <div class="detail-section-title">Monitoring</div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="detail-item"><span class="detail-label">Performance Indicators</span>
                            <div class="detail-value"><?= $val($row->pi); ?></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item"><span class="detail-label">Means of Verification (MOVs)</span>
                            <div class="detail-value"><?= $val($row->movs); ?></div>
                        </div>
                    </div>
                </div>

                <div class="detail-section-title">Implementation</div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="detail-item"><span class="detail-label">Person(s) Responsible</span>
                            <div class="detail-value"><?= $val($row->pr); ?></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="detail-item"><span class="detail-label">Schedule</span>
                            <div class="detail-value"><?= $val($row->schedule); ?></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="detail-item"><span class="detail-label">Venue</span>
                            <div class="detail-value"><?= $val($row->venue); ?></div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="detail-item"><span class="detail-label">Materials</span>
                            <div class="detail-value"><?= $val($row->materials); ?></div>
                        </div>
                    </div>
                </div>

                <div class="detail-section-title">Budget</div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="detail-item"><span class="detail-label">Budget per Activity</span>
                            <div class="detail-value lead-value">&#8369; <?= number_format((float) $row->budget, 2); ?></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item"><span class="detail-label">Budget Source</span>
                            <div class="detail-value"><?= $val($row->budget_source); ?></div>
                        </div>
                    </div>
                </div>

                <div class="detail-section-title">Targets vs. Accomplishments</div>
                <?php if (empty($sop_by_type)) { ?>
                    <p class="text-muted mb-0"><i class="mdi mdi-information-outline mr-1"></i>No physical or financial target has been encoded for this activity yet.</p>
                <?php } else {
                    foreach ($sop_types as $type_key => $type_label) {
                        if (!isset($sop_by_type[$type_key])) {
                            continue;
                        }
                        $s = $sop_by_type[$type_key]; ?>
                        <div class="sop-card">
                            <div class="sop-card-head"><?= $type_label; ?></div>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th style="width:90px;">Quarter</th>
                                            <th class="text-right" style="width:140px;">Target</th>
                                            <th class="text-right" style="width:140px;">Accomplishment</th>
                                            <th>Remarks</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php for ($q = 1; $q <= 4; $q++) {
                                            $t  = 'q' . $q;
                                            $a  = 'smea_q' . $q;
                                            $rm = 'remarks_q' . $q; ?>
                                            <tr>
                                                <td>Q<?= $q; ?></td>
                                                <td class="text-right"><?= $num($s->$t); ?></td>
                                                <td class="text-right"><?= $num($s->$a); ?></td>
                                                <td><?= $val($s->$rm); ?></td>
                                            </tr>
                                        <?php } ?>
                                        <tr class="font-weight-bold">
                                            <td>Total</td>
                                            <td class="text-right"><?= $num($s->total); ?></td>
                                            <td class="text-right"><?= $num($s->smea_total); ?></td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php }
                } ?>

                <div class="detail-section-title">Record</div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="detail-item"><span class="detail-label">Batch Code</span>
                            <div class="detail-value"><?= $val($row->b_code); ?></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="detail-item"><span class="detail-label">Fiscal Year</span>
                            <div class="detail-value"><?= $val($row->fy); ?></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="detail-item"><span class="detail-label">Record ID</span>
                            <div class="detail-value"><?= (int) $row->id; ?></div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>

    <!-- Details modal -->
    <div id="smeaDetailModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="smeaDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="smeaDetailModalLabel">SMEA Activity Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body" id="smeaDetailBody"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- SMEA report (quarter picker) modal -->
    <div id="sop" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">Generate SMEA Report</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <?= form_open('page/generate_smea', ['target' => '_blank']); ?>
                    <div class="form-group col-md-12">
                        <label>Select Quarter</label>
                        <select class="form-control" name="q" required>
                            <option></option>
                            <option value="1">Quarter 1</option>
                            <option value="2">Quarter 2</option>
                            <option value="3">Quarter 3</option>
                            <option value="4">Quarter 4</option>
                        </select>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                        <input type="submit" name="submit" class="btn btn-primary waves-effect waves-light" value="Submit" />
                    </div>
                    </form>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
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

<script type="text/javascript">
    $(document).on("click", ".open-AddBookDialog", function() {
        var myBookId = $(this).data('id');
        $(".modal-body #id").val(myBookId);

        var fieldId = $(this).data('field');
        $(".modal-body #field").val(fieldId);
    });
</script>


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

<!-- Datatables init -->
<script src="<?= base_url(); ?>assets/js/pages/datatables.init.js"></script>

<script src="<?= base_url(); ?>assets/libs/custombox/custombox.min.js"></script>

<script>
    $(document).ready(function() {
        $('#smea-datatable').DataTable({
            responsive: true,
            order: [],
            pageLength: 10,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
            columnDefs: [
                // keep the project and the details button visible at every width
                { targets: 1, className: 'all' },
                { targets: -1, orderable: false, className: 'all text-center' }
            ],
            language: {
                search: "",
                searchPlaceholder: "Search activities...",
                emptyTable: "No SMEA activities found for this batch.",
                lengthMenu: "Show _MENU_ entries"
            }
        });

        // Load the matching hidden template into the details modal.
        $(document).on('click', '.smea-view-details', function() {
            var id = $(this).data('detail-id');
            var tpl = $('#smea-detail-store').find('.smea-detail-tpl[data-detail-id="' + id + '"]');

            if (!tpl.length) {
                return;
            }

            $('#smeaDetailBody').html(tpl.html());
            $('#smeaDetailModalLabel').text(tpl.data('title') || 'SMEA Activity Details');
            $('#smeaDetailModal').modal('show');
        });
    });
</script>

<script>
    function calculateTotal() {
        var value1 = parseFloat(document.getElementById('value1').value) || 0;
        var value2 = parseFloat(document.getElementById('value2').value) || 0;
        var value3 = parseFloat(document.getElementById('value3').value) || 0;
        var value4 = parseFloat(document.getElementById('value4').value) || 0;
        var total = value1 + value2 + value3 + value4;

        document.getElementById('total').value = total;
    }
</script>

<SCRIPT language=Javascript>
    function isNumberKey(evt) {
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode != 46 && charCode > 31 &&
            (charCode < 48 || charCode > 57))
            return false;

        return true;
    }
</SCRIPT>

</body>

</html>
