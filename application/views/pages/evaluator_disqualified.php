<?php
defined('BASEPATH') or exit('No direct script access allowed');

if (!function_exists('eh')) {
    function eh($value)
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

$disqualified = isset($disqualified) && is_array($disqualified) ? $disqualified : [];
$jobTypes     = isset($jobTypes) && is_array($jobTypes) ? $jobTypes : [];
$dqCount      = count($disqualified);

$normalizeApplicant = static function ($row) use ($jobTypes) {
    $jobTypeId = (int) ($row->job_type ?? 0);

    return [
        'appId'      => (int) ($row->appID ?? $row->app_id ?? 0),
        'jobId'      => (int) ($row->jobID ?? $row->job_id ?? 0),
        'recordNo'   => trim((string) ($row->record_no ?? $row->applicant_id ?? '')),
        'firstName'  => trim((string) ($row->FirstName ?? '')),
        'middleName' => trim((string) ($row->MiddleName ?? '')),
        'lastName'   => trim((string) ($row->LastName ?? '')),
        'jobTitle'   => trim((string) ($row->jobTitle ?? '')),
        'jobType'    => $jobTypes[$jobTypeId] ?? '',
        'preSchool'  => trim((string) ($row->pre_school ?? '')),
        'status'     => trim((string) ($row->appStatus ?? '')),
        'dqReason'   => trim((string) ($row->dq_reason ?? '')),
        'dqVdate'    => trim((string) ($row->dq_vdate ?? '')),
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

<div class="content-page ead-page">
    <div class="content">
        <div class="container-fluid py-3 py-lg-4">
            <div class="card ead-hero mb-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-lg-7">
                            <div class="ead-eyebrow"><i class="mdi mdi-account-remove-outline"></i> Evaluator workspace</div>
                            <h1>Disqualified Applicants</h1>
                            <p class="ead-hero-copy">Applicants you have marked as disqualified during the qualification review. Select <strong>Reason</strong> on any row to view the recorded disqualification reason.</p>
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
                            <span class="ead-stat-icon is-red"><i class="mdi mdi-account-remove-outline"></i></span>
                            <div>
                                <div class="ead-stat-label">Disqualified</div>
                                <div class="ead-stat-value" id="count-dq"><?= $dqCount ?></div>
                                <div class="ead-stat-help">Applicants you disqualified</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <section class="card ead-card" id="disqualified-work" aria-labelledby="dq-heading">
                <div class="ead-card-header">
                    <div class="ead-card-title-wrap">
                        <span class="ead-card-title-icon"><i class="mdi mdi-account-remove-outline"></i></span>
                        <div>
                            <h4 id="dq-heading">Disqualified applicants</h4>
                            <p>Review the reason recorded for each disqualification.</p>
                        </div>
                    </div>
                    <span class="ead-count-badge" id="dq-table-count"><?= $dqCount ?></span>
                </div>
                <div class="ead-table-wrap">
                    <?php if (empty($disqualified)): ?>
                        <div class="ead-empty-state">
                            <i class="mdi mdi-account-check-outline"></i>
                            <h5>No disqualified applicants</h5>
                            <p>Applicants you mark as disqualified during qualification review will appear here.</p>
                        </div>
                    <?php else: ?>
                        <table id="disqualifiedApplicantsTable" class="table table-hover dt-responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th>Record no.</th>
                                    <th>Applicant</th>
                                    <th>Position applied</th>
                                    <th>Date disqualified</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($disqualified as $row): ?>
                                    <?php
                                    $applicant  = $normalizeApplicant($row);
                                    $fullName   = trim($applicant['lastName'] . ', ' . $applicant['firstName'] . ' ' . $applicant['middleName'], ', ');
                                    $reasonAttr = eh($applicant['dqReason']);
                                    $dateAttr   = eh($applicant['dqVdate']);
                                    $nameAttr   = eh($fullName);
                                    $jobAttr    = eh($applicant['jobTitle']);
                                    ?>
                                    <tr data-app-id="<?= $applicant['appId'] ?>">
                                        <td><span class="ead-record-code"><?= eh($applicant['recordNo']) ?></span></td>
                                        <td><div class="ead-person-name"><?= eh($fullName) ?></div></td>
                                        <td>
                                            <div class="ead-position-name"><?= eh($applicant['jobTitle']) ?></div>
                                            <?php if ($applicant['jobType'] !== ''): ?><div class="ead-cell-sub"><?= eh($applicant['jobType']) ?></div><?php endif; ?>
                                        </td>
                                        <td><?= eh($applicant['dqVdate'] !== '' ? $applicant['dqVdate'] : '&mdash;') ?></td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-1">
                                                <a class="btn btn-outline-primary btn-sm ead-action-btn" href="<?= eh($evaluationUrl($applicant)) ?>" target="_blank" rel="noopener">
                                                    View <i class="mdi mdi-open-in-new"></i>
                                                </a>
                                                <button type="button"
                                                        class="btn btn-sm ead-action-btn is-reason dq-reason-btn"
                                                        data-toggle="modal"
                                                        data-target="#dqReasonModal"
                                                        data-name="<?= $nameAttr ?>"
                                                        data-job="<?= $jobAttr ?>"
                                                        data-date="<?= $dateAttr ?>"
                                                        data-reason="<?= $reasonAttr ?>">
                                                    <i class="mdi mdi-comment-alert-outline"></i> Reason
                                                </button>
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

<script>
    (function () {
        function initEvaluatorDisqualified() {
            if (!window.jQuery || !window.jQuery.fn || !window.jQuery.fn.DataTable) {
                // Still wire up the modal even if DataTables isn't ready
                if (window.jQuery) { wireModal(window.jQuery); }
                return;
            }

            var $ = window.jQuery;

            $('#disqualifiedApplicantsTable').DataTable({
                destroy: true,
                responsive: true,
                autoWidth: false,
                pageLength: 10,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'All']],
                order: [[3, 'desc']],
                columnDefs: [
                    { targets: 1, responsivePriority: 1 },
                    { targets: 4, orderable: false, searchable: false, responsivePriority: 2 },
                    { targets: 0, responsivePriority: 3 }
                ],
                language: {
                    search: '',
                    searchPlaceholder: 'Search applicants...',
                    lengthMenu: 'Show _MENU_',
                    info: 'Showing _START_–_END_ of _TOTAL_ applicants',
                    infoEmpty: 'No applicants to show',
                    emptyTable: 'No disqualified applicants.',
                    zeroRecords: 'No applicants match your search',
                    paginate: {
                        previous: '<i class="mdi mdi-chevron-left"></i>',
                        next: '<i class="mdi mdi-chevron-right"></i>'
                    }
                }
            });

            wireModal($);
        }

        function wireModal($) {
            $('#dqReasonModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var name   = button.data('name')   || '';
                var job    = button.data('job')    || '';
                var date   = button.data('date')   || '';
                var reason = button.data('reason') || '';

                $('#dqReasonApplicant').text(name);
                $('#dqReasonJob').text(job);
                $('#dqReasonDate').text(date ? date : '—');
                $('#dqReasonText').text(reason ? reason : 'No reason was recorded.');
            });
        }

        if (document.readyState === 'complete') {
            window.setTimeout(initEvaluatorDisqualified, 0);
        } else {
            window.addEventListener('load', initEvaluatorDisqualified);
        }
    }());
</script>
