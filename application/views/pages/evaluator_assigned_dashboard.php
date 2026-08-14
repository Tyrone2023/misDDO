<?php
defined('BASEPATH') or exit('No direct script access allowed');

if (!function_exists('eh')) {
    function eh($value)
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

$pending = isset($pending) && is_array($pending) ? $pending : [];
$scored = isset($scored) && is_array($scored) ? $scored : [];
$jobTypes = isset($jobTypes) && is_array($jobTypes) ? $jobTypes : [];
$counts = array_merge([
    'total' => count($pending) + count($scored),
    'pending' => count($pending),
    'scored' => count($scored),
    'pending_queries' => 0,
], isset($counts) && is_array($counts) ? $counts : []);

if (isset($pending_queries)) {
    $counts['pending_queries'] = (int) $pending_queries;
}

$totalCount = (int) $counts['total'];
$pendingCount = (int) $counts['pending'];
$scoredCount = (int) $counts['scored'];
$pendingQueryCount = (int) $counts['pending_queries'];
$completionRate = $totalCount > 0 ? (int) round(($scoredCount / $totalCount) * 100) : 0;

$normalizeApplicant = static function ($row) use ($jobTypes) {
    $jobTypeId = (int) ($row->job_type ?? 0);

    return [
        'appId' => (int) ($row->appID ?? $row->app_id ?? 0),
        'jobId' => (int) ($row->jobID ?? $row->job_id ?? 0),
        'recordNo' => trim((string) ($row->record_no ?? $row->applicant_id ?? '')),
        'firstName' => trim((string) ($row->FirstName ?? '')),
        'middleName' => trim((string) ($row->MiddleName ?? '')),
        'lastName' => trim((string) ($row->LastName ?? '')),
        'jobTitle' => trim((string) ($row->jobTitle ?? '')),
        'jobType' => $jobTypes[$jobTypeId] ?? '',
        'preSchool' => trim((string) ($row->pre_school ?? '')),
        'status' => trim((string) ($row->appStatus ?? '')),
    ];
};

$evaluationUrl = static function (array $applicant) {
    return base_url('EvaluatorAssigned/open/'
        . rawurlencode($applicant['recordNo']) . '/'
        . $applicant['jobId'] . '/'
        . rawurlencode($applicant['preSchool']) . '/'
        . $applicant['appId'] . '/'
        . rawurlencode($applicant['recordNo']));
};

$nextPendingUrl = !empty($pending) ? $evaluationUrl($normalizeApplicant($pending[0])) : '';
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

    .ead-page .container-fluid {
        max-width: 1680px;
    }

    .ead-hero {
        position: relative;
        overflow: hidden;
        border: 0;
        border-radius: 18px;
        background: linear-gradient(122deg, #123b66 0%, #1d5f9e 58%, #2f83d7 100%);
        box-shadow: 0 18px 42px rgba(22, 64, 108, .18);
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

    .ead-hero .card-body {
        position: relative;
        z-index: 1;
        padding: 30px 32px;
    }

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
        color: rgba(255, 255, 255, .78);
        font-size: 14px;
        line-height: 1.65;
    }

    .ead-hero-actions {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-end;
        gap: 10px;
    }

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

    .ead-hero-actions .btn-light {
        border-color: #fff;
        color: var(--ead-navy);
    }

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

    .ead-query-notice {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        margin-bottom: 18px;
        border: 1px solid #f5d79b;
        border-radius: 14px;
        background: #fffaf0;
        padding: 15px 18px;
        color: #775311;
    }

    .ead-query-notice-main {
        display: flex;
        min-width: 0;
        align-items: center;
        gap: 12px;
    }

    .ead-query-notice i {
        color: #d58a15;
        font-size: 24px;
    }

    .ead-query-notice strong,
    .ead-query-notice span {
        display: block;
    }

    .ead-query-notice span {
        margin-top: 2px;
        color: #997334;
        font-size: 12px;
    }

    .ead-query-notice .btn {
        flex: 0 0 auto;
        border-radius: 9px;
        font-weight: 600;
    }

    .ead-stat-card,
    .ead-card {
        border: 1px solid var(--ead-border);
        border-radius: 15px;
        background: #fff;
        box-shadow: 0 7px 22px rgba(36, 54, 75, .055);
    }

    .ead-stat-card {
        height: calc(100% - 20px);
        margin-bottom: 20px;
    }

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

    .ead-stat-icon.is-blue { background: #eaf3ff; color: #2976ce; }
    .ead-stat-icon.is-amber { background: #fff4df; color: #bd7614; }
    .ead-stat-icon.is-green { background: #e9f8f2; color: #168668; }
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

    .ead-stat-help {
        margin-top: 5px;
        color: #92a0af;
        font-size: 11px;
    }

    .ead-progress-card {
        margin-bottom: 20px;
    }

    .ead-progress-card .card-body {
        padding: 20px 22px;
    }

    .ead-section-kicker {
        margin-bottom: 3px;
        color: var(--ead-text);
        font-size: 14px;
        font-weight: 700;
    }

    .ead-section-note {
        margin: 0;
        color: var(--ead-muted);
        font-size: 12px;
    }

    .ead-progress-number {
        color: var(--ead-navy);
        font-size: 21px;
        font-weight: 700;
    }

    .ead-progress-track {
        height: 9px;
        margin-top: 14px;
        overflow: hidden;
        border-radius: 999px;
        background: #ebf0f5;
    }

    .ead-progress-bar {
        height: 100%;
        border-radius: inherit;
        background: linear-gradient(90deg, #2878db, #22a37f);
        transition: width .3s ease;
    }

    .ead-quick-links {
        display: flex;
        height: 100%;
        align-items: center;
        justify-content: flex-end;
        gap: 9px;
    }

    .ead-quick-links a {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border: 1px solid var(--ead-border);
        border-radius: 9px;
        padding: 8px 11px;
        background: #f9fbfd;
        color: #496074;
        font-size: 12px;
        font-weight: 600;
    }

    .ead-quick-links a:hover {
        border-color: #bdd3ea;
        background: #f1f7fe;
        color: #2369ae;
    }

    .ead-card {
        margin-bottom: 22px;
        overflow: hidden;
    }

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
        background: var(--ead-sky);
        color: var(--ead-blue);
        font-size: 19px;
    }

    .ead-card-title-icon.is-green {
        background: #e9f8f2;
        color: var(--ead-green);
    }

    .ead-card-header h4 {
        margin: 0 0 3px;
        color: var(--ead-text);
        font-size: 16px;
        font-weight: 700;
    }

    .ead-card-header p {
        margin: 0;
        color: var(--ead-muted);
        font-size: 12px;
    }

    .ead-count-badge {
        display: inline-flex;
        min-width: 34px;
        height: 29px;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        padding: 0 10px;
        background: #eef5fd;
        color: #246bb4;
        font-size: 12px;
        font-weight: 700;
    }

    .ead-count-badge.is-green {
        background: #e9f8f2;
        color: var(--ead-green);
    }

    .ead-table-wrap {
        padding: 15px 20px 20px;
    }

    .ead-page .table {
        margin-bottom: 0 !important;
        color: #405367;
    }

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
    .ead-page .table th {
        padding: 13px 12px;
        vertical-align: middle;
    }

    .ead-page .table tbody td {
        border-top-color: #edf1f5;
    }

    .ead-page .table-hover tbody tr:hover {
        background: #f8fbff;
    }

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

    .ead-cell-sub {
        margin-top: 3px;
        color: #8b99a8;
        font-size: 11px;
    }

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

    .ead-status-pending {
        background: #fff4df;
        color: var(--ead-amber);
    }

    .ead-status-scored {
        background: #e9f8f2;
        color: var(--ead-green);
    }

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

    .ead-new-row {
        animation: eadRowPulse 1.25s ease;
    }

    @keyframes eadRowPulse {
        0% { background: #e6f5ff; }
        100% { background: transparent; }
    }

    @media (max-width: 991.98px) {
        .ead-hero-actions,
        .ead-quick-links {
            justify-content: flex-start;
            margin-top: 18px;
        }
    }

    @media (max-width: 575.98px) {
        .ead-hero .card-body {
            padding: 24px 20px;
        }

        .ead-hero-actions .btn,
        .ead-query-notice .btn {
            width: 100%;
        }

        .ead-query-notice,
        .ead-card-header {
            align-items: flex-start;
            flex-direction: column;
        }

        .ead-query-notice .btn {
            margin-top: 2px;
        }

        .ead-table-wrap {
            padding: 12px 12px 17px;
        }

        .ead-page .dataTables_wrapper .dataTables_filter input {
            width: 100%;
            min-width: 0;
            margin: 7px 0 0;
        }
    }
</style>

<div class="content-page ead-page">
    <div class="content">
        <div class="container-fluid py-3 py-lg-4">
            <div class="card ead-hero mb-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-lg-7">
                            <div class="ead-eyebrow"><i class="mdi mdi-clipboard-text-outline"></i> Evaluator workspace</div>
                            <h1>Assigned Applicants</h1>
                            <p class="ead-hero-copy">Review your assigned applicants, continue pending evaluations, and revisit submitted scores from one organized workspace.</p>
                        </div>
                        <div class="col-lg-5">
                            <div class="ead-hero-actions">
                                <?php if ($nextPendingUrl !== ''): ?>
                                    <a class="btn btn-light" href="<?= eh($nextPendingUrl) ?>" target="_blank" rel="noopener">
                                        <i class="mdi mdi-play-circle-outline mr-1"></i> Continue next evaluation
                                    </a>
                                <?php endif; ?>
                                <a class="btn ead-btn-ghost" href="<?= eh(base_url('ApplicantQueryAssigned')) ?>">
                                    <i class="mdi mdi-message-alert-outline mr-1"></i> Applicant queries
                                    <?php if ($pendingQueryCount > 0): ?>
                                        <span class="badge badge-light ml-2" id="hero-query-count"><?= $pendingQueryCount ?></span>
                                    <?php endif; ?>
                                </a>
                                <a class="btn ead-btn-ghost" href="<?= eh(base_url('EvaluatorAssigned/disqualified')) ?>">
                                    <i class="mdi mdi-account-remove-outline mr-1"></i> Disqualified applicants
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($pendingQueryCount > 0): ?>
                <div class="ead-query-notice" role="alert">
                    <div class="ead-query-notice-main">
                        <i class="mdi mdi-message-alert-outline" aria-hidden="true"></i>
                        <div>
                            <strong>You have <?= $pendingQueryCount ?> pending applicant <?= $pendingQueryCount === 1 ? 'query' : 'queries' ?>.</strong>
                            <span>Review these concerns so applicants can continue without unnecessary delays.</span>
                        </div>
                    </div>
                    <a class="btn btn-warning btn-sm" href="<?= eh(base_url('ApplicantQueryAssigned')) ?>">Review queries</a>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-sm-6 col-xl-3">
                    <div class="card ead-stat-card">
                        <div class="card-body">
                            <span class="ead-stat-icon is-blue"><i class="mdi mdi-account-multiple"></i></span>
                            <div>
                                <div class="ead-stat-label">Total assigned</div>
                                <div class="ead-stat-value" id="count-total"><?= $totalCount ?></div>
                                <div class="ead-stat-help">Applicants in your workload</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="card ead-stat-card">
                        <div class="card-body">
                            <span class="ead-stat-icon is-amber"><i class="mdi mdi-progress-clock"></i></span>
                            <div>
                                <div class="ead-stat-label">Pending</div>
                                <div class="ead-stat-value" id="count-pending"><?= $pendingCount ?></div>
                                <div class="ead-stat-help">Waiting for your evaluation</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="card ead-stat-card">
                        <div class="card-body">
                            <span class="ead-stat-icon is-green"><i class="mdi mdi-check-circle-outline"></i></span>
                            <div>
                                <div class="ead-stat-label">With scores</div>
                                <div class="ead-stat-value" id="count-scored"><?= $scoredCount ?></div>
                                <div class="ead-stat-help">Evaluations already submitted</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="card ead-stat-card">
                        <div class="card-body">
                            <span class="ead-stat-icon is-red"><i class="mdi mdi-message-alert-outline"></i></span>
                            <div>
                                <div class="ead-stat-label">Pending queries</div>
                                <div class="ead-stat-value" id="count-queries"><?= $pendingQueryCount ?></div>
                                <div class="ead-stat-help">Applicant concerns to review</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card ead-card ead-progress-card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-lg-8">
                            <div class="d-flex align-items-start justify-content-between">
                                <div>
                                    <div class="ead-section-kicker">Evaluation progress</div>
                                    <p class="ead-section-note"><span id="progress-scored-count"><?= $scoredCount ?></span> of <span id="progress-total-count"><?= $totalCount ?></span> assigned applicants have scores.</p>
                                </div>
                                <span class="ead-progress-number" id="evaluation-progress-label"><?= $completionRate ?>%</span>
                            </div>
                            <div class="ead-progress-track" role="progressbar" aria-label="Evaluation completion" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?= $completionRate ?>">
                                <div class="ead-progress-bar" id="evaluation-progress-bar" style="width: <?= $completionRate ?>%"></div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="ead-quick-links" aria-label="Dashboard sections">
                                <a href="#pending-work"><i class="mdi mdi-progress-clock"></i> Pending work</a>
                                <a href="#scored-work"><i class="mdi mdi-check-circle-outline"></i> Completed</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <section class="card ead-card" id="pending-work" aria-labelledby="pending-heading">
                <div class="ead-card-header">
                    <div class="ead-card-title-wrap">
                        <span class="ead-card-title-icon"><i class="mdi mdi-account-search-outline"></i></span>
                        <div>
                            <h4 id="pending-heading">Applicants to evaluate</h4>
                            <p>Start or continue the rating for an assigned applicant.</p>
                        </div>
                    </div>
                    <span class="ead-count-badge" id="pending-table-count"><?= $pendingCount ?></span>
                </div>
                <div class="ead-table-wrap">
                    <table id="pendingApplicantsTable" class="table table-hover dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>Record no.</th>
                                <th>Applicant</th>
                                <th>Position applied</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pending as $row): ?>
                                <?php
                                $applicant = $normalizeApplicant($row);
                                $fullName = trim($applicant['lastName'] . ', ' . $applicant['firstName'] . ' ' . $applicant['middleName'], ', ');
                                $needsQualification = in_array($applicant['status'], ['Application Submitted', 'Validated'], true);
                                $actionLabel = $needsQualification ? 'Review documents' : 'Start rating';
                                $actionIcon = $needsQualification ? 'mdi-clipboard-check-outline' : 'mdi-open-in-new';
                                ?>
                                <tr data-app-id="<?= $applicant['appId'] ?>" data-state="pending">
                                    <td><span class="ead-record-code"><?= eh($applicant['recordNo']) ?></span></td>
                                    <td><div class="ead-person-name"><?= eh($fullName) ?></div></td>
                                    <td>
                                        <div class="ead-position-name"><?= eh($applicant['jobTitle']) ?></div>
                                        <?php if ($applicant['jobType'] !== ''): ?><div class="ead-cell-sub"><?= eh($applicant['jobType']) ?></div><?php endif; ?>
                                    </td>
                                    <td>
                                        <a class="btn btn-primary btn-sm ead-action-btn" href="<?= eh($evaluationUrl($applicant)) ?>" target="_blank" rel="noopener">
                                            <?= eh($actionLabel) ?> <i class="mdi <?= eh($actionIcon) ?>"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="card ead-card" id="scored-work" aria-labelledby="scored-heading">
                <div class="ead-card-header">
                    <div class="ead-card-title-wrap">
                        <span class="ead-card-title-icon is-green"><i class="mdi mdi-check-circle-outline"></i></span>
                        <div>
                            <h4 id="scored-heading">Applicants with scores</h4>
                            <p>Review the evaluations you have already submitted.</p>
                        </div>
                    </div>
                    <span class="ead-count-badge is-green" id="scored-table-count"><?= $scoredCount ?></span>
                </div>
                <div class="ead-table-wrap">
                    <table id="scoredApplicantsTable" class="table table-hover dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>Record no.</th>
                                <th>Applicant</th>
                                <th>Position applied</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($scored as $row): ?>
                                <?php
                                $applicant = $normalizeApplicant($row);
                                $fullName = trim($applicant['lastName'] . ', ' . $applicant['firstName'] . ' ' . $applicant['middleName'], ', ');
                                ?>
                                <tr data-app-id="<?= $applicant['appId'] ?>" data-state="scored">
                                    <td><span class="ead-record-code"><?= eh($applicant['recordNo']) ?></span></td>
                                    <td><div class="ead-person-name"><?= eh($fullName) ?></div></td>
                                    <td>
                                        <div class="ead-position-name"><?= eh($applicant['jobTitle']) ?></div>
                                        <?php if ($applicant['jobType'] !== ''): ?><div class="ead-cell-sub"><?= eh($applicant['jobType']) ?></div><?php endif; ?>
                                    </td>
                                    <td>
                                        <a class="btn btn-outline-success btn-sm ead-action-btn" href="<?= eh($evaluationUrl($applicant)) ?>" target="_blank" rel="noopener">
                                            Review <i class="mdi mdi-open-in-new"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</div>

<script>
    (function () {
        function initEvaluatorDashboard() {
            if (!window.jQuery || !window.jQuery.fn || !window.jQuery.fn.DataTable) {
                return;
            }

            var $ = window.jQuery;
            var jobTypes = <?= json_encode($jobTypes, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?> || {};
            var updateUrl = <?= json_encode(base_url('EvaluatorAssigned/check_updates')) ?>;
            var evaluationBaseUrl = <?= json_encode(base_url('EvaluatorAssigned/open/')) ?>;

            function makeTable(selector, emptyMessage) {
                return $(selector).DataTable({
                    destroy: true,
                    responsive: true,
                    autoWidth: false,
                    pageLength: 10,
                    lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'All']],
                    order: [[1, 'asc']],
                    columnDefs: [
                        { targets: 1, responsivePriority: 1 },
                        { targets: 3, orderable: false, searchable: false, responsivePriority: 2 },
                        { targets: 0, responsivePriority: 3 }
                    ],
                    language: {
                        search: '',
                        searchPlaceholder: 'Search applicants...',
                        lengthMenu: 'Show _MENU_',
                        info: 'Showing _START_–_END_ of _TOTAL_ applicants',
                        infoEmpty: 'No applicants to show',
                        emptyTable: emptyMessage,
                        zeroRecords: 'No applicants match your search',
                        paginate: {
                            previous: '<i class="mdi mdi-chevron-left"></i>',
                            next: '<i class="mdi mdi-chevron-right"></i>'
                        }
                    }
                });
            }

            var pendingTable = makeTable('#pendingApplicantsTable', 'You have no applicants waiting for evaluation.');
            var scoredTable = makeTable('#scoredApplicantsTable', 'No scored applicants yet.');

            function escapeHtml(value) {
                return $('<div>').text(value == null ? '' : String(value)).html();
            }

            function normalizeItem(item) {
                var rawJobType = item.jobType != null ? item.jobType : item.job_type;
                var jobTypeLabel = '';

                if (rawJobType != null && String(rawJobType).trim() !== '') {
                    jobTypeLabel = jobTypes[rawJobType] || String(rawJobType);
                }

                return {
                    appId: parseInt(item.appID != null ? item.appID : item.app_id, 10) || 0,
                    jobId: parseInt(item.jobID != null ? item.jobID : item.job_id, 10) || 0,
                    recordNo: item.record_no != null ? item.record_no : (item.recordNo || ''),
                    firstName: item.firstName != null ? item.firstName : (item.FirstName || ''),
                    middleName: item.middleName != null ? item.middleName : (item.MiddleName || ''),
                    lastName: item.lastName != null ? item.lastName : (item.LastName || ''),
                    jobTitle: item.jobTitle || '',
                    jobType: jobTypeLabel,
                    preSchool: item.pre_school != null ? item.pre_school : (item.preSchool || ''),
                    appStatus: item.appStatus || ''
                };
            }

            function applicantUrl(item) {
                return evaluationBaseUrl
                    + encodeURIComponent(item.recordNo || '') + '/'
                    + encodeURIComponent(item.jobId || 0) + '/'
                    + encodeURIComponent(item.preSchool || '') + '/'
                    + encodeURIComponent(item.appId || 0) + '/'
                    + encodeURIComponent(item.recordNo || '');
            }

            function applicantName(item) {
                var givenNames = [item.firstName, item.middleName].filter(function (part) {
                    return part != null && String(part).trim() !== '';
                }).join(' ');

                return [item.lastName, givenNames].filter(function (part) {
                    return part != null && String(part).trim() !== '';
                }).join(', ');
            }

            function positionCell(item) {
                var jobType = item.jobType || '';
                var sub = jobType ? '<div class="ead-cell-sub">' + escapeHtml(jobType) + '</div>' : '';
                return '<div class="ead-position-name">' + escapeHtml(item.jobTitle || '') + '</div>' + sub;
            }

            function buildRow(item, state) {
                var isPending = state === 'pending';
                var buttonClass = isPending ? 'btn-primary' : 'btn-outline-success';
                var needsQualification = isPending && ['Application Submitted', 'Validated'].indexOf(item.appStatus) !== -1;
                var buttonText = isPending ? (needsQualification ? 'Review documents' : 'Start rating') : 'Review';
                var buttonIcon = needsQualification ? 'mdi-clipboard-check-outline' : 'mdi-open-in-new';

                return [
                    '<span class="ead-record-code">' + escapeHtml(item.recordNo || '') + '</span>',
                    '<div class="ead-person-name">' + escapeHtml(applicantName(item)) + '</div>',
                    positionCell(item),
                    '<a class="btn ' + buttonClass + ' btn-sm ead-action-btn" href="' + escapeHtml(applicantUrl(item)) + '" target="_blank" rel="noopener">' + buttonText + ' <i class="mdi ' + buttonIcon + '"></i></a>'
                ];
            }

            function stateMap(items) {
                var states = {};
                (items.pending || []).forEach(function (item) {
                    states[String(item.appId)] = 'pending';
                });
                (items.scored || []).forEach(function (item) {
                    states[String(item.appId)] = 'scored';
                });
                return states;
            }

            var currentStates = {};
            $('#pendingApplicantsTable tbody tr[data-app-id]').each(function () {
                currentStates[String($(this).data('app-id'))] = 'pending';
            });
            $('#scoredApplicantsTable tbody tr[data-app-id]').each(function () {
                currentStates[String($(this).data('app-id'))] = 'scored';
            });

            function addRow(table, item, state) {
                var node = table.row.add(buildRow(item, state)).draw(false).node();
                $(node)
                    .attr('data-app-id', item.appId)
                    .attr('data-state', state)
                    .addClass('ead-new-row');
            }

            function removeRow(table, appId) {
                table.rows(function (index, data, node) {
                    return String($(node).attr('data-app-id')) === String(appId);
                }).remove().draw(false);
            }

            function updateDashboardCounts(counts) {
                var total = parseInt(counts.total, 10) || 0;
                var pending = parseInt(counts.pending, 10) || 0;
                var scored = parseInt(counts.scored, 10) || 0;
                var queries = parseInt(counts.pending_queries, 10) || 0;
                var rate = total > 0 ? Math.round((scored / total) * 100) : 0;

                $('#count-total').text(total);
                $('#count-pending').text(pending);
                $('#count-scored').text(scored);
                $('#count-queries').text(queries);
                $('#pending-table-count').text(pending);
                $('#scored-table-count').text(scored);
                $('#progress-scored-count').text(scored);
                $('#progress-total-count').text(total);
                $('#evaluation-progress-label').text(rate + '%');
                $('#evaluation-progress-bar').css('width', rate + '%');
                $('.ead-progress-track').attr('aria-valuenow', rate);
            }

            function syncTables(payload) {
                if (!payload || payload.error) {
                    return;
                }

                var items = {
                    pending: (payload.pending || []).map(normalizeItem),
                    scored: (payload.scored || []).map(normalizeItem)
                };
                var nextStates = stateMap(items);
                var byId = {};
                items.pending.concat(items.scored).forEach(function (item) {
                    byId[String(item.appId)] = item;
                });

                Object.keys(currentStates).forEach(function (appId) {
                    if (!nextStates[appId]) {
                        removeRow(currentStates[appId] === 'pending' ? pendingTable : scoredTable, appId);
                        return;
                    }

                    if (currentStates[appId] !== nextStates[appId]) {
                        removeRow(currentStates[appId] === 'pending' ? pendingTable : scoredTable, appId);
                        addRow(nextStates[appId] === 'pending' ? pendingTable : scoredTable, byId[appId], nextStates[appId]);
                    }
                });

                Object.keys(nextStates).forEach(function (appId) {
                    if (!currentStates[appId]) {
                        addRow(nextStates[appId] === 'pending' ? pendingTable : scoredTable, byId[appId], nextStates[appId]);
                    }
                });

                currentStates = nextStates;
                updateDashboardCounts(payload.counts || {});
            }

            var pollInProgress = false;

            function pollUpdates() {
                if (pollInProgress) {
                    return;
                }

                pollInProgress = true;
                $.ajax({
                    url: updateUrl,
                    method: 'GET',
                    dataType: 'json',
                    cache: false
                }).done(syncTables).always(function () {
                    pollInProgress = false;
                });
            }

            setInterval(pollUpdates, 30000);
            $(window).on('focus', pollUpdates);

            if ('BroadcastChannel' in window) {
                var ratingChannel = new BroadcastChannel('scores_saved');
                ratingChannel.onmessage = function () {
                    pollUpdates();
                };
            }
        }

        if (document.readyState === 'complete') {
            window.setTimeout(initEvaluatorDashboard, 0);
        } else {
            window.addEventListener('load', initEvaluatorDashboard);
        }
    }());
</script>
