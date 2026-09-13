<?php
defined('BASEPATH') or exit('No direct script access allowed');

if (!function_exists('eh')) {
    function eh($value)
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

$tagged   = isset($tagged) && is_array($tagged) ? $tagged : [];
$jobTypes = isset($jobTypes) && is_array($jobTypes) ? $jobTypes : [];

$taggedCount   = count($tagged);
$openCount     = 0;
$doneCount     = 0;
$handedOver    = 0;
$jobsTouched   = [];

// Every stage is listed on this page on purpose, so the stage is what the chip
// says rather than something the query filtered out.
$stageOf = static function ($row) {
    if ((int) ($row->dq ?? 0) === 2) {
        return ['Disqualified', 'is-dq', 'mdi-account-remove-outline'];
    }

    switch (trim((string) ($row->appStatus ?? ''))) {
        case 'Validated':
            return ['Validated', 'is-waiting', 'mdi-clipboard-check-outline'];
        case 'Endorsed for Rating':
            return ['Endorsed for rating', 'is-active', 'mdi-clipboard-arrow-right-outline'];
        case 'Rated':
            return ['Rated', 'is-done', 'mdi-star-check-outline'];
        case 'Confirmed':
            return ['Confirmed', 'is-done', 'mdi-check-decagram-outline'];
        default:
            return ['Application submitted', 'is-waiting', 'mdi-clipboard-text-outline'];
    }
};

// assigned_at carries the date and the time, and a re-tag rewrites it, so it is
// always "tagged to you at".
$stamp = static function ($value) {
    $value = trim((string) $value);
    if ($value === '' || $value === '0000-00-00 00:00:00') {
        return ['', ''];
    }

    $time = strtotime($value);
    if ($time === false) {
        return [$value, ''];
    }

    return [date('M d, Y', $time), date('h:i A', $time)];
};

$normalizeTag = static function ($row) use ($jobTypes) {
    $jobTypeId = (int) ($row->job_type ?? 0);

    return [
        'appId'          => (int) ($row->appID ?? $row->app_id ?? 0),
        'jobId'          => (int) ($row->jobID ?? $row->job_id ?? 0),
        'recordNo'       => trim((string) ($row->record_no ?? $row->applicant_id ?? '')),
        'firstName'      => trim((string) ($row->FirstName ?? '')),
        'middleName'     => trim((string) ($row->MiddleName ?? '')),
        'lastName'       => trim((string) ($row->LastName ?? '')),
        'jobTitle'       => trim((string) ($row->jobTitle ?? '')),
        'jobType'        => $jobTypes[$jobTypeId] ?? '',
        'preSchool'      => trim((string) ($row->pre_school ?? '')),
        'status'         => trim((string) ($row->appStatus ?? '')),
        'dq'             => (int) ($row->dq ?? 0),
        'assignedAt'     => trim((string) ($row->assigned_at ?? '')),
        'retaggedAt'     => trim((string) ($row->retagged_at ?? '')),
        'retagCount'     => (int) ($row->retag_count ?? 0),
        'takenFrom'      => trim((string) ($row->previous_evaluator ?? '')),
        'taggedBy'       => trim((string) ($row->retagged_by_name ?? '')),
        'specialization' => trim((string) ($row->specialization ?? '')),
        'dqReason'       => trim((string) ($row->dq_reason ?? '')),
        'dqVdate'        => trim((string) ($row->dq_vdate ?? '')),
        'jvStatus'       => trim((string) ($row->jvStatus ?? '')),
    ];
};

// Same entry point the dashboard uses: EvaluatorAssigned/open re-checks the
// assignment, cleans the record number and redirects to Pages/ma.
$evaluationUrl = static function (array $applicant) {
    return base_url('EvaluatorAssigned/open/'
        . rawurlencode($applicant['recordNo']) . '/'
        . $applicant['jobId'] . '/'
        . rawurlencode($applicant['preSchool']) . '/'
        . $applicant['appId'] . '/'
        . rawurlencode($applicant['recordNo']));
};

foreach ($tagged as $row) {
    $title = trim((string) ($row->jobTitle ?? ''));
    if ($title !== '') {
        $jobsTouched[$title] = true;
    }

    if (trim((string) ($row->retagged_at ?? '')) !== '') {
        $handedOver++;
    }

    if ((int) ($row->dq ?? 0) === 2
        || in_array(trim((string) ($row->appStatus ?? '')), ['Rated', 'Confirmed'], true)) {
        $doneCount++;
    } else {
        $openCount++;
    }
}

$jobCount = count($jobsTouched);
?>

<style>
    .ead-page {
        --ead-navy: #123b66;
        --ead-blue: #2878db;
        --ead-sky: #eaf4ff;
        --ead-green: #168668;
        --ead-amber: #c67a11;
        --ead-red: #d55a5a;
        --ead-text: #24364b;
        --ead-muted: #6b7b8e;
        --ead-border: #e5ebf2;
        background: #f5f8fc;
        min-height: calc(100vh - 70px);
    }

    .ead-page .container-fluid { max-width: 1680px; }

    .ead-hero {
        position: relative;
        overflow: hidden;
        border: 0;
        border-radius: 18px;
        background: linear-gradient(122deg, #6b1f1f 0%, #a83232 58%, #d55a5a 100%);
        box-shadow: 0 18px 42px rgba(108, 31, 31, .18);
        color: #fff;
    }

    .ead-hero::after {
        position: absolute;
        top: -85px;
        right: -55px;
        width: 260px;
        height: 260px;
        border: 45px solid rgba(255, 255, 255, .07);
        border-radius: 50%;
        content: "";
    }

    .ead-hero .card-body { position: relative; z-index: 1; padding: 30px 32px; }

    .ead-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        margin-bottom: 10px;
        color: rgba(255, 255, 255, .76);
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .11em;
        text-transform: uppercase;
    }

    .ead-hero h1 {
        margin-bottom: 8px;
        color: #fff;
        font-size: clamp(25px, 3vw, 35px);
        font-weight: 700;
        letter-spacing: -.025em;
    }

    .ead-hero-copy {
        max-width: 680px;
        margin: 0;
        color: rgba(255, 255, 255, .82);
        font-size: 14px;
        line-height: 1.65;
    }

    .ead-hero-actions { display: flex; flex-wrap: wrap; justify-content: flex-end; gap: 10px; }

    .ead-hero-actions .btn {
        display: inline-flex;
        min-height: 42px;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        padding: 10px 16px;
        font-weight: 600;
        box-shadow: none;
    }

    .ead-hero-actions .btn-light { border-color: #fff; color: var(--ead-navy); }

    .ead-btn-ghost {
        border: 1px solid rgba(255, 255, 255, .35);
        background: rgba(255, 255, 255, .08);
        color: #fff;
    }

    .ead-btn-ghost:hover,
    .ead-btn-ghost:focus {
        border-color: rgba(255, 255, 255, .65);
        background: rgba(255, 255, 255, .16);
        color: #fff;
    }

    .ead-stat-card,
    .ead-card {
        border: 1px solid var(--ead-border);
        border-radius: 15px;
        background: #fff;
        box-shadow: 0 7px 22px rgba(36, 54, 75, .055);
    }

    .ead-stat-card { height: calc(100% - 20px); margin-bottom: 20px; }

    .ead-stat-card .card-body {
        display: flex;
        min-height: 112px;
        align-items: center;
        gap: 14px;
        padding: 18px;
    }

    .ead-stat-icon {
        display: inline-flex;
        width: 48px;
        height: 48px;
        flex: 0 0 48px;
        align-items: center;
        justify-content: center;
        border-radius: 13px;
        font-size: 23px;
    }

    .ead-stat-icon.is-red { background: #fff0f0; color: #cf5656; }

    .ead-stat-label {
        margin-bottom: 3px;
        color: var(--ead-muted);
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .055em;
        text-transform: uppercase;
    }

    .ead-stat-value {
        margin: 0;
        color: var(--ead-text);
        font-size: 27px;
        font-weight: 700;
        line-height: 1;
    }

    .ead-stat-help { margin-top: 5px; color: #92a0af; font-size: 11px; }

    .ead-card { margin-bottom: 22px; overflow: hidden; }

    .ead-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        border-bottom: 1px solid var(--ead-border);
        padding: 18px 20px;
        background: #fff;
    }

    .ead-card-title-wrap {
        display: flex;
        min-width: 0;
        align-items: center;
        gap: 11px;
    }

    .ead-card-title-icon {
        display: inline-flex;
        width: 39px;
        height: 39px;
        flex: 0 0 39px;
        align-items: center;
        justify-content: center;
        border-radius: 11px;
        background: #fff0f0;
        color: var(--ead-red);
        font-size: 19px;
    }

    .ead-card-header h4 {
        margin: 0 0 3px;
        color: var(--ead-text);
        font-size: 16px;
        font-weight: 700;
    }

    .ead-card-header p { margin: 0; color: var(--ead-muted); font-size: 12px; }

    .ead-count-badge {
        display: inline-flex;
        min-width: 34px;
        height: 29px;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        padding: 0 10px;
        background: #fff0f0;
        color: var(--ead-red);
        font-size: 12px;
        font-weight: 700;
    }

    .ead-table-wrap { padding: 15px 20px 20px; }

    .ead-page .table { margin-bottom: 0 !important; color: #405367; }

    .ead-page .table thead th {
        border-top: 0;
        border-bottom: 1px solid #dfe7ef;
        background: #f7f9fc;
        color: #627387;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .055em;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .ead-page .table td,
    .ead-page .table th { padding: 13px 12px; vertical-align: middle; }

    .ead-page .table tbody td { border-top-color: #edf1f5; }

    .ead-page .table-hover tbody tr:hover { background: #fff7f7; }

    .ead-record-code {
        display: inline-flex;
        border-radius: 7px;
        padding: 5px 8px;
        background: #f0f4f8;
        color: #506579;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        font-size: 11px;
        font-weight: 600;
    }

    .ead-person-name,
    .ead-position-name {
        color: #283d52;
        font-size: 13px;
        font-weight: 600;
    }

    .ead-cell-sub { margin-top: 3px; color: #8b99a8; font-size: 11px; }

    .ead-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border-radius: 999px;
        padding: 6px 9px;
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
    }

    .ead-status::before {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: currentColor;
        content: "";
    }

    .ead-status-dq { background: #fff0f0; color: var(--ead-red); }

    .ead-action-btn {
        display: inline-flex;
        min-width: 92px;
        align-items: center;
        justify-content: center;
        gap: 5px;
        border-radius: 8px;
        padding: 7px 10px;
        font-size: 11px;
        font-weight: 700;
    }

    .ead-action-btn.is-reason {
        min-width: 80px;
        background: #fff;
        border: 1px solid #f1c2c2;
        color: var(--ead-red);
    }

    .ead-action-btn.is-reason:hover {
        background: #fff0f0;
        border-color: var(--ead-red);
        color: var(--ead-red);
    }

    .ead-action-btn.is-revert {
        min-width: 80px;
        background: #fff;
        border: 1px solid #b8d4c6;
        color: var(--ead-green);
    }

    .ead-action-btn.is-revert:hover {
        background: #e8f5ee;
        border-color: var(--ead-green);
        color: var(--ead-green);
    }

    .ead-page .dataTables_wrapper .dataTables_length label,
    .ead-page .dataTables_wrapper .dataTables_filter label,
    .ead-page .dataTables_wrapper .dataTables_info {
        color: #77879a;
        font-size: 11px;
    }

    .ead-page .dataTables_wrapper .dataTables_filter input,
    .ead-page .dataTables_wrapper .dataTables_length select {
        min-height: 36px;
        border: 1px solid #dce5ee;
        border-radius: 8px;
        box-shadow: none;
    }

    .ead-page .dataTables_wrapper .dataTables_filter input {
        min-width: 220px;
        margin-left: 8px;
        padding: 7px 10px;
    }

    .ead-page .dataTables_wrapper .dataTables_paginate .paginate_button {
        border-radius: 7px !important;
        font-size: 11px;
    }

    .ead-empty-state {
        padding: 40px 20px;
        text-align: center;
        color: var(--ead-muted);
    }

    .ead-empty-state i { font-size: 42px; color: #cdd6e0; }
    .ead-empty-state h5 { margin: 12px 0 4px; color: var(--ead-text); font-weight: 700; }
    .ead-empty-state p  { margin: 0; font-size: 12px; }

    @media (max-width: 991.98px) {
        .ead-hero-actions { justify-content: flex-start; margin-top: 18px; }
    }

    @media (max-width: 575.98px) {
        .ead-hero .card-body { padding: 24px 20px; }
        .ead-hero-actions .btn { width: 100%; }
        .ead-card-header { align-items: flex-start; flex-direction: column; }
        .ead-table-wrap { padding: 12px 12px 17px; }
        .ead-page .dataTables_wrapper .dataTables_filter input {
            width: 100%;
            min-width: 0;
            margin: 7px 0 0;
        }
    }
</style>
<style>
    /* The workspace CSS ships a red hero for the Denied Requests page; this is
       the evaluator's own workload, so it is restated in the workspace blue. */
    .ead-page .ead-hero {
        background: linear-gradient(122deg, #123b66 0%, #1d5aa8 58%, #2878db 100%);
        box-shadow: 0 18px 42px rgba(18, 59, 102, .18);
    }

    .ead-stage-tag {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        border-radius: 999px;
        padding: 3px 10px;
        background: #eef1f5;
        color: #5b6b7d;
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
    }

    .ead-stage-tag.is-waiting { background: #fff4df; color: #9d640f; }
    .ead-stage-tag.is-active  { background: #eef4fd; color: #2a5f9e; }
    .ead-stage-tag.is-done    { background: #e8f6f1; color: #157a5f; }
    .ead-stage-tag.is-dq      { background: #fff0f0; color: var(--ead-red); }

    .ead-stamp-date { font-weight: 700; color: var(--ead-text); }
    .ead-stamp-time { color: var(--ead-muted); font-size: 11.5px; }

    .ead-handover {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        border-radius: 999px;
        padding: 2px 9px;
        background: #f3e9fb;
        color: #6b3fa0;
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
    }

    .ead-action-btn.is-rate {
        min-width: 105px;
        border: 1px solid #b9d0ee;
        background: #fff;
        color: var(--ead-blue);
    }

    .ead-action-btn.is-rate:hover {
        border-color: var(--ead-blue);
        background: var(--ead-sky);
        color: var(--ead-blue);
    }

    .ead-action-btn.is-reason {
        border: 1px solid #f0cfcf;
        background: #fff;
        color: var(--ead-red);
    }

    .ead-action-btn.is-reason:hover {
        border-color: var(--ead-red);
        background: #fff6f6;
        color: var(--ead-red);
    }

    .ead-action-btn.is-revert {
        border: 1px solid #bfe3d4;
        background: #fff;
        color: var(--ead-green);
    }

    .ead-action-btn.is-revert:hover {
        border-color: var(--ead-green);
        background: #f2fbf7;
        color: var(--ead-green);
    }

    .ead-count-badge.is-blue { background: #eaf3ff; color: #2976ce; }

    .ead-stat-icon.is-blue   { background: #eaf3ff; color: #2976ce; }
    .ead-stat-icon.is-amber  { background: #fff4df; color: #9d640f; }
    .ead-stat-icon.is-green  { background: #e8f6f1; color: #157a5f; }
    .ead-stat-icon.is-violet { background: #f3e9fb; color: #6b3fa0; }

    .ead-page .ead-table-wrap .d-flex.gap-1 > * + * { margin-left: 6px; }
</style>

<div class="content-page ead-page">
    <div class="content">
        <div class="container-fluid py-3 py-lg-4">
            <?php
            $flashSuccess = $this->session->flashdata('success');
            $flashDanger  = $this->session->flashdata('danger');
            if ($flashSuccess): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="mdi mdi-check-circle-outline mr-1"></i> <?= eh($flashSuccess) ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
            <?php endif; ?>
            <?php if ($flashDanger): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="mdi mdi-alert-circle-outline mr-1"></i> <?= eh($flashDanger) ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
            <?php endif; ?>
            <div class="card ead-hero mb-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-lg-7">
                            <div class="ead-eyebrow"><i class="mdi mdi-format-list-checks"></i> Evaluator workspace</div>
                            <h1>All Tagged Applicants</h1>
                            <p class="ead-hero-copy">Every applicant tagged to you, in one list &mdash; waiting, endorsed, rated, confirmed and disqualified alike. Each row shows the stage it is at and the date and time it was tagged to you. Select <strong>Open</strong> to work on one; a disqualified applicant also offers <strong>Reason</strong> and <strong>Revert</strong>.</p>
                        </div>
                        <div class="col-lg-5">
                            <div class="ead-hero-actions">
                                <a class="btn btn-light" href="<?= eh(base_url('EvaluatorAssigned')) ?>">
                                    <i class="mdi mdi-arrow-left mr-1"></i> Back to dashboard
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-6 col-xl-3">
                    <div class="card ead-stat-card">
                        <div class="card-body">
                            <span class="ead-stat-icon is-blue"><i class="mdi mdi-account-multiple-outline"></i></span>
                            <div>
                                <div class="ead-stat-label">Tagged to you</div>
                                <div class="ead-stat-value" id="count-tagged"><?= $taggedCount ?></div>
                                <div class="ead-stat-help">All stages included</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="card ead-stat-card">
                        <div class="card-body">
                            <span class="ead-stat-icon is-amber"><i class="mdi mdi-clipboard-clock-outline"></i></span>
                            <div>
                                <div class="ead-stat-label">Still open</div>
                                <div class="ead-stat-value"><?= $openCount ?></div>
                                <div class="ead-stat-help">Not yet rated or disqualified</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="card ead-stat-card">
                        <div class="card-body">
                            <span class="ead-stat-icon is-green"><i class="mdi mdi-check-decagram-outline"></i></span>
                            <div>
                                <div class="ead-stat-label">Settled</div>
                                <div class="ead-stat-value"><?= $doneCount ?></div>
                                <div class="ead-stat-help">Rated, confirmed or disqualified</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="card ead-stat-card">
                        <div class="card-body">
                            <span class="ead-stat-icon is-violet"><i class="mdi mdi-account-switch"></i></span>
                            <div>
                                <div class="ead-stat-label">Handed over to you</div>
                                <div class="ead-stat-value"><?= $handedOver ?></div>
                                <div class="ead-stat-help">Re-tagged from another evaluator</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <section class="card ead-card" id="tagged-work" aria-labelledby="tagged-heading">
                <div class="ead-card-header">
                    <div class="ead-card-title-wrap">
                        <span class="ead-card-title-icon"><i class="mdi mdi-format-list-checks"></i></span>
                        <div>
                            <h4 id="tagged-heading">Applicants tagged to you</h4>
                            <p><?= $jobCount ?> vacanc<?= $jobCount === 1 ? 'y' : 'ies' ?> &middot; nothing is hidden behind a status filter.</p>
                        </div>
                    </div>
                    <span class="ead-count-badge is-blue" id="tagged-table-count"><?= $taggedCount ?></span>
                </div>
                <div class="ead-table-wrap">
                    <?php if (empty($tagged)): ?>
                        <div class="ead-empty-state">
                            <i class="mdi mdi-clipboard-text-outline"></i>
                            <h5>Nothing tagged to you yet</h5>
                            <p>Applicants the Secretariat tags to you will appear here.</p>
                        </div>
                    <?php else: ?>
                        <table id="taggedApplicantsTable" class="table table-hover dt-responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th>Record no.</th>
                                    <th>Applicant</th>
                                    <th>Position applied</th>
                                    <th>Status</th>
                                    <th>Date tagged</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($tagged as $row): ?>
                                    <?php
                                    $applicant = $normalizeTag($row);
                                    $fullName  = trim($applicant['lastName'] . ', ' . $applicant['firstName'] . ' ' . $applicant['middleName'], ', ');
                                    [$stageLabel, $stageClass, $stageIcon] = $stageOf($row);
                                    [$tagDate, $tagTime] = $stamp($applicant['assignedAt']);
                                    $wasRetagged = $applicant['retaggedAt'] !== '';
                                    ?>
                                    <tr data-app-id="<?= $applicant['appId'] ?>">
                                        <td><span class="ead-record-code"><?= eh($applicant['recordNo']) ?></span></td>
                                        <td>
                                            <div class="ead-person-name"><?= eh($fullName) ?></div>
                                            <?php if ($applicant['specialization'] !== ''): ?>
                                                <div class="ead-cell-sub"><?= eh($applicant['specialization']) ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="ead-position-name"><?= eh($applicant['jobTitle']) ?></div>
                                            <?php if ($applicant['jobType'] !== ''): ?><div class="ead-cell-sub"><?= eh($applicant['jobType']) ?></div><?php endif; ?>
                                        </td>
                                        <td><span class="ead-stage-tag <?= $stageClass ?>"><i class="mdi <?= $stageIcon ?>"></i> <?= eh($stageLabel) ?></span></td>
                                        <td data-order="<?= eh($applicant['assignedAt']) ?>">
                                            <?php if ($tagDate !== ''): ?>
                                                <div class="ead-stamp-date"><?= eh($tagDate) ?></div>
                                                <div class="ead-stamp-time"><?= eh($tagTime) ?></div>
                                            <?php else: ?>
                                                &mdash;
                                            <?php endif; ?>
                                            <?php if ($wasRetagged): ?>
                                                <div class="ead-cell-sub mt-1">
                                                    <span class="ead-handover" title="<?= eh($applicant['takenFrom'] !== '' ? 'Handed over from ' . $applicant['takenFrom'] : 'Handed over from another evaluator') ?>">
                                                        <i class="mdi mdi-account-switch"></i> Re-tagged<?= $applicant['retagCount'] > 1 ? ' ' . $applicant['retagCount'] . '&times;' : '' ?>
                                                    </span>
                                                </div>
                                                <?php if ($applicant['takenFrom'] !== ''): ?>
                                                    <div class="ead-cell-sub">from <?= eh($applicant['takenFrom']) ?></div>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-1">
                                                <a class="btn btn-sm ead-action-btn is-rate" href="<?= eh($evaluationUrl($applicant)) ?>" target="_blank" rel="noopener">
                                                    Open <i class="mdi mdi-open-in-new"></i>
                                                </a>
                                                <?php if ($applicant['dq'] === 2): ?>
                                                    <button type="button"
                                                            class="btn btn-sm ead-action-btn is-reason"
                                                            data-toggle="modal"
                                                            data-target="#dqReasonModal"
                                                            data-name="<?= eh($fullName) ?>"
                                                            data-job="<?= eh($applicant['jobTitle']) ?>"
                                                            data-date="<?= eh($applicant['dqVdate']) ?>"
                                                            data-reason="<?= eh($applicant['dqReason']) ?>">
                                                        <i class="mdi mdi-comment-alert-outline"></i> Reason
                                                    </button>
                                                    <?php // A closed vacancy freezes the decision, revert included. ?>
                                                    <?php if (strcasecmp($applicant['jvStatus'], 'Closed') !== 0): ?>
                                                        <button type="button"
                                                                class="btn btn-sm ead-action-btn is-revert"
                                                                data-toggle="modal"
                                                                data-target="#dqRevertModal"
                                                                data-app-id="<?= $applicant['appId'] ?>"
                                                                data-name="<?= eh($fullName) ?>">
                                                            <i class="mdi mdi-undo-variant"></i> Revert
                                                        </button>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </div>
</div>

<!-- Disqualification Reason Modal -->
<div id="dqReasonModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="dqReasonModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white" id="dqReasonModalLabel">
                    <i class="mdi mdi-account-remove-outline mr-1"></i>Disqualification Reason
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="font-weight-bold">Applicant</label>
                    <p id="dqReasonApplicant" class="form-control-plaintext mb-2"></p>
                </div>
                <div class="form-group">
                    <label class="font-weight-bold">Position</label>
                    <p id="dqReasonJob" class="form-control-plaintext mb-2"></p>
                </div>
                <div class="form-group">
                    <label class="font-weight-bold">Date Disqualified</label>
                    <p id="dqReasonDate" class="form-control-plaintext mb-2"></p>
                </div>
                <div class="form-group">
                    <label class="font-weight-bold">Reason</label>
                    <div id="dqReasonText" class="form-control bg-light" style="min-height:80px; white-space:pre-wrap;"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light waves-effect" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- /.modal -->

<!-- Revert Disqualification Modal -->
<div id="dqRevertModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="dqRevertModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="dqRevertForm" action="<?= eh(base_url('EvaluatorAssigned/revert_disqualification')) ?>" method="post">
                <div class="modal-header bg-success">
                    <h5 class="modal-title text-white" id="dqRevertModalLabel">
                        <i class="mdi mdi-undo-variant mr-1"></i>Revert Disqualification
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="appID" id="dqRevertAppId" value="">
                    <?php // Brings the evaluator back to this list instead of the disqualified one. ?>
                    <input type="hidden" name="return_url" value="<?= eh(base_url('EvaluatorAssigned/tagged')) ?>">
                    <p>Are you sure you want to revert the disqualification for <strong id="dqRevertName"></strong>?</p>
                    <p class="text-muted mb-0">The applicant goes back to the qualification review stage and the disqualification reason is removed.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light waves-effect" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success waves-effect">
                        <i class="mdi mdi-undo-variant mr-1"></i> Yes, Revert
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /.modal -->

<script>
    (function () {
        function wireModals($) {
            $('#dqReasonModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                $('#dqReasonApplicant').text(button.data('name') || '');
                $('#dqReasonJob').text(button.data('job') || '');
                $('#dqReasonDate').text(button.data('date') ? button.data('date') : '—');
                $('#dqReasonText').text(button.data('reason') ? button.data('reason') : 'No reason was recorded.');
            });

            $('#dqRevertModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                $('#dqRevertAppId').val(button.data('app-id') || '');
                $('#dqRevertName').text(button.data('name') || '');
            });
        }

        function initEvaluatorTagged() {
            if (!window.jQuery) {
                return;
            }

            var $ = window.jQuery;

            if ($.fn && $.fn.DataTable) {
                $('#taggedApplicantsTable').DataTable({
                    destroy: true,
                    responsive: true,
                    autoWidth: false,
                    pageLength: 25,
                    lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'All']],
                    order: [[4, 'desc']],
                    columnDefs: [
                        { targets: 1, responsivePriority: 1 },
                        { targets: 5, orderable: false, searchable: false, responsivePriority: 2 },
                        { targets: 0, responsivePriority: 3 }
                    ],
                    language: {
                        search: '',
                        searchPlaceholder: 'Search applicants...',
                        lengthMenu: 'Show _MENU_',
                        info: 'Showing _START_–_END_ of _TOTAL_ applicants',
                        infoEmpty: 'No applicants to show',
                        emptyTable: 'Nothing is tagged to you yet.',
                        zeroRecords: 'No applicant matches your search',
                        paginate: {
                            previous: '<i class="mdi mdi-chevron-left"></i>',
                            next: '<i class="mdi mdi-chevron-right"></i>'
                        }
                    }
                });
            }

            wireModals($);
        }

        if (document.readyState === 'complete') {
            window.setTimeout(initEvaluatorTagged, 0);
        } else {
            window.addEventListener('load', initEvaluatorTagged);
        }
    }());
</script>
