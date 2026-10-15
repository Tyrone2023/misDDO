<?php defined('BASEPATH') OR exit('No direct script access allowed');
if (!function_exists('h')) {
    function h($v) { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); }
}
$reports = isset($reports) && is_array($reports) ? $reports : array();
$defaults = isset($defaults) && is_array($defaults) ? $defaults : array();
$natureOptions = isset($natureOptions) && is_array($natureOptions) ? $natureOptions : array();
$months = array(1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December');
$jsonFlags = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT;
$storeUrl = isset($storeUrl) ? $storeUrl : site_url('RaiReport/store');
$updateUrl = isset($updateUrl) ? $updateUrl : site_url('RaiReport/update');
$dataUrl = isset($dataUrl) ? $dataUrl : site_url('RaiReport/data');
$deleteUrl = isset($deleteUrl) ? $deleteUrl : site_url('RaiReport/delete');
$candidatesUrl = isset($candidatesUrl) ? $candidatesUrl : site_url('RaiReport/candidates');
$bulkCandidatesUrl = isset($bulkCandidatesUrl) ? $bulkCandidatesUrl : site_url('RaiReport/bulk_candidates');
$saveApplicantsUrl = isset($saveApplicantsUrl) ? $saveApplicantsUrl : site_url('RaiReport/save_applicants');
$saveChecklistUrl = isset($saveChecklistUrl) ? $saveChecklistUrl : site_url('RaiReport/save_checklist');
$checklistDefaults = isset($checklistDefaults) && is_array($checklistDefaults) ? $checklistDefaults : array('items' => array(), 'csc_receiving_officer' => '', 'csc_receiving_position' => 'CSC FO Receiving Officer');
$previewUrl = isset($previewUrl) ? $previewUrl : site_url('RaiReport/preview');
$raiToken = isset($raiToken) ? $raiToken : '';
?>
<style>
    :root {
        --rai-primary: #1f3a5f;
        --rai-primary-2: #274b7a;
        --rai-accent: #1abc9c;
        --rai-bg: #f4f7fb;
        --rai-border: #e5ecf5;
        --rai-text: #25364a;
        --rai-muted: #7b8794;
        --rai-soft: #eef5ff;
    }

    .content-page { background: var(--rai-bg); min-height: 100vh; }
    .rai-page-shell { padding-bottom: 24px; }

    /* Hero */
    .rai-hero {
        position: relative; overflow: hidden; border-radius: 18px;
        padding: 22px 24px; margin-bottom: 18px;
        background: linear-gradient(135deg, var(--rai-primary), var(--rai-primary-2));
        box-shadow: 0 14px 35px rgba(31, 58, 95, .18); color: #fff;
    }
    .rai-hero:before { content:""; position:absolute; width:230px; height:230px; border-radius:50%; right:-80px; top:-95px; background:rgba(255,255,255,.10); }
    .rai-hero:after { content:""; position:absolute; width:160px; height:160px; border-radius:50%; right:90px; bottom:-100px; background:rgba(26,188,156,.22); }
    .rai-hero-content { position:relative; z-index:2; display:flex; align-items:center; justify-content:space-between; gap:16px; }
    .rai-title-block h4 { color:#fff; font-size:1.25rem; font-weight:800; letter-spacing:.2px; margin:0 0 5px; }
    .rai-title-block p { color:rgba(255,255,255,.82); margin:0; max-width:760px; font-size:.86rem; line-height:1.45; }
    .rai-hero-icon { width:54px; height:54px; border-radius:16px; display:flex; align-items:center; justify-content:center; background:rgba(255,255,255,.14); border:1px solid rgba(255,255,255,.18); flex:0 0 auto; }
    .rai-hero-icon i { font-size:28px; color:#fff; }

    /* Cards */
    .rai-card { border:0; border-radius:16px; box-shadow:0 8px 26px rgba(31,58,95,.08); overflow:hidden; }
    .rai-card .card-body { padding:18px; }
    .rai-section-head { display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:14px; flex-wrap:wrap; }
    .rai-section-title { margin:0; font-size:.94rem; font-weight:800; color:var(--rai-text); display:flex; align-items:center; gap:8px; }
    .rai-section-title i { width:32px; height:32px; border-radius:10px; display:inline-flex; align-items:center; justify-content:center; background:var(--rai-soft); color:var(--rai-primary); font-size:17px; }
    .rai-help-chip { display:inline-flex; align-items:center; gap:6px; color:var(--rai-muted); font-size:.76rem; background:#f8fafd; border:1px solid var(--rai-border); padding:6px 10px; border-radius:999px; }

    /* Table */
    .rai-results-wrap { background:#fff; width:100%; overflow-x:auto; }
    #rai-table { width:100%; margin-bottom:0; }
    #rai-table thead th { background:#f8fbff; border-top:0; border-bottom:1px solid var(--rai-border); color:#516070; font-size:.68rem; text-transform:uppercase; letter-spacing:.35px; padding:10px 8px; font-weight:800; line-height:1.2; vertical-align:middle; }
    #rai-table td { font-size:.82rem; padding:10px 8px; vertical-align:middle; color:var(--rai-text); }
    .rai-sub { display:block; color:var(--rai-muted); font-size:.72rem; margin-top:2px; }
    .rai-count-pill { display:inline-block; background:var(--rai-soft); color:var(--rai-primary); border-radius:999px; padding:4px 12px; font-size:.74rem; font-weight:800; }

    .rai-empty-state { margin:18px; border:1px dashed #cbd8e7; background:#f8fbff; color:#526173; border-radius:14px; padding:30px 18px; text-align:center; }
    .rai-empty-state i { font-size:36px; color:var(--rai-primary); opacity:.78; display:block; margin-bottom:8px; }
    .rai-empty-state strong { display:block; color:var(--rai-text); font-size:.94rem; margin-bottom:4px; }
    .rai-empty-state span { font-size:.8rem; }

    /* Icon buttons */
    .rai-actions { display:flex; flex-wrap:nowrap; gap:4px; }
    .rai-actions form { display:inline; }
    .rai-icon-btn { display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:8px; padding:0; border:1px solid transparent; background:#f4f7fb; color:var(--rai-primary); font-size:16px; line-height:1; cursor:pointer; transition:background .15s, color .15s, border-color .15s; text-decoration:none; }
    .rai-icon-btn:hover { background:var(--rai-soft); border-color:var(--rai-border); color:var(--rai-primary); text-decoration:none; }
    .rai-icon-btn.is-danger { color:#c0392b; }
    .rai-icon-btn.is-danger:hover { background:#fdecea; border-color:#f5c6c0; color:#a93226; }
    .rai-icon-btn.is-primary { color:#fff; background:var(--rai-primary); border-color:var(--rai-primary); }
    .rai-icon-btn.is-primary:hover { background:var(--rai-primary-2); border-color:var(--rai-primary-2); color:#fff; }
    .rai-icon-btn.is-secondary { color:#516070; background:#eef3f9; border-color:#e1e8f1; }
    .rai-icon-btn.is-secondary:hover { background:#e2eaf3; border-color:#d3dde9; color:#3a4757; }
    .rai-icon-btn:disabled { opacity:.5; cursor:not-allowed; }

    /* ===== Modern modal styling ===== */
    .modal-dialog.rai-modal-lg { max-width: 920px; }
    .modal-dialog.rai-modal-xl { max-width: 1200px; }
    .rai-modal .modal-content { border: 0; border-radius: 16px; overflow: hidden; box-shadow: 0 24px 70px rgba(15,29,47,.32); }
    .rai-modal .modal-header { background: linear-gradient(135deg, #193552, var(--rai-primary-2)); color: #fff !important; padding: 18px 22px; border-bottom: 3px solid var(--rai-accent); position: relative; align-items:center; }
    .rai-modal .modal-heading { min-width:0; }
    .rai-modal .modal-header .modal-title { margin:0; color:#fff !important; font-weight: 800; font-size: 1.08rem; display: flex; align-items: center; gap: 10px; letter-spacing:.1px; }
    .rai-modal .modal-header .modal-title span { color:#fff !important; }
    .rai-modal .modal-header .modal-title i { width: 38px; height: 38px; border-radius: 11px; color:#fff !important; background: rgba(255,255,255,.15); border:1px solid rgba(255,255,255,.24); display: inline-flex; align-items: center; justify-content: center; font-size: 21px; }
    .rai-modal .modal-subtitle { display:block; margin:4px 0 0 48px; color:rgba(255,255,255,.82) !important; font-size:.73rem; font-weight:600; }
    .rai-modal .modal-header .close { display:inline-flex; align-items:center; justify-content:center; width:36px; height:36px; border-radius:10px; background:rgba(255,255,255,.12); color: #fff !important; opacity: 1; text-shadow: none; font-size: 1.55rem; padding: 0; margin: 0; line-height: 1; }
    .rai-modal .modal-header .close span { color:#fff !important; transform:translateY(-1px); }
    .rai-modal .modal-header .close:hover { background:rgba(255,255,255,.22); }
    .rai-modal .modal-body { padding: 22px; max-height: calc(100vh - 200px); overflow-y: auto; background: #fafcff; }
    .rai-modal .modal-footer { background: #fff; padding: 14px 22px; border-top: 1px solid var(--rai-border); }
    .rai-modal .btn { border-radius: 10px; font-weight: 700; }

    /* Form inside modal */
    .rai-form-card { margin:0; padding:18px 12px 4px; background:#fff; border:1px solid var(--rai-border); border-radius:12px; box-shadow:0 5px 18px rgba(31,58,95,.05); }
    .rai-form-card label { font-weight:800; font-size:.68rem; text-transform:uppercase; letter-spacing:.55px; color:var(--rai-muted); margin-bottom:6px; }
    .rai-form-card .form-group { margin-bottom:14px; }
    .rai-form-card .form-control { border-radius: 8px; border-color: #d6dfeb; font-size: .84rem; }
    .rai-form-card .form-control:focus { border-color: var(--rai-primary); box-shadow: 0 0 0 3px rgba(31,58,95,.1); }
    .rai-form-card textarea.form-control { min-height:38px; resize:vertical; }
    .rai-req { color:#c0392b; }

    /* Summary grid */
    .rai-summary { display:grid; grid-template-columns:repeat(4, minmax(0,1fr)); gap:12px; }
    .rai-summary div { padding:10px 12px; background:#f8fbff; border:1px solid var(--rai-border); border-radius:10px; font-size:.82rem; color:var(--rai-text); }
    .rai-summary label { display:block; font-size:.62rem; text-transform:uppercase; letter-spacing:.5px; color:var(--rai-muted); font-weight:800; margin-bottom:3px; }

    /* Tabs */
    .rai-tabs { display:flex; gap:4px; margin-bottom:16px; background:#eef3f9; border-radius:10px; padding:4px; }
    .rai-tab { flex:1; background:none; border:0; padding:10px 14px; font-weight:700; font-size:.82rem; color:var(--rai-muted); cursor:pointer; border-radius:8px; transition:all .15s; display:flex; align-items:center; justify-content:center; gap:6px; }
    .rai-tab:hover { color:var(--rai-primary); }
    .rai-tab.active { background:#fff; color:var(--rai-primary); box-shadow:0 2px 8px rgba(31,58,95,.1); }
    .rai-tab-pane { display:none; }
    .rai-tab-pane.active { display:block; }

    /* Select2 modern styling inside modal */
    .rai-modal .select2-container { width: 100% !important; }
    .rai-modal .select2-container--default .select2-selection--multiple { border-radius: 10px; border-color: #d6dfeb; min-height: 44px; padding: 4px 6px; }
    .rai-modal .select2-container--default.select2-container--focus .select2-selection--multiple { border-color: var(--rai-primary); box-shadow: 0 0 0 3px rgba(31,58,95,.1); }
    .rai-modal .select2-container--default .select2-selection--multiple .select2-selection__choice { background: var(--rai-soft); border-color: #c9d8ef; color: var(--rai-primary); border-radius: 6px; padding: 2px 8px; font-size: .8rem; font-weight: 600; }
    .rai-modal .select2-container--default .select2-selection--multiple .select2-selection__choice__remove { color: var(--rai-muted); margin-right: 5px; font-size: 1.1rem; line-height: 1; }
    .rai-modal .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover { color: #c0392b; }
    .rai-modal .select2-container--default .select2-search--inline .select2-search__field { font-size: .84rem; }
    .select2-container--open { z-index: 10060 !important; }
    .select2-dropdown { border-radius: 10px; border-color: #d6dfeb; box-shadow: 0 8px 24px rgba(31,58,95,.12); }
    .select2-results__option { font-size: .82rem; padding: 8px 12px; }
    .select2-container--default .select2-results__option--highlighted[aria-selected] { background-color: var(--rai-soft); color: var(--rai-primary); }

    /* Applicants table */
    .rai-table-wrap { overflow-x:auto; }
    .rai-table { border-collapse:collapse; width:100%; font-size:.82rem; }
    .rai-table th, .rai-table td { padding:10px 8px; border-bottom:1px solid var(--rai-border); text-align:left; vertical-align:middle; }
    .rai-table th { background:#f8fbff; color:#516070; font-size:.68rem; text-transform:uppercase; letter-spacing:.35px; font-weight:800; }
    .rai-pill { display:inline-block; background:var(--rai-soft); color:var(--rai-primary); border-radius:999px; padding:3px 9px; font-size:.68rem; font-weight:700; }
    .rai-muted { color:var(--rai-muted); font-size:.76rem; }

    .rai-note { border-left:3px solid #e7b150; padding:10px 13px; background:#fffaf0; border-radius:3px; color:#876129; font-size:.78rem; }
    .rai-error { color:#b23a38; font-size:.82rem; padding:10px; }
    .rai-hide { display:none !important; }

    /* Manual staging table */
    .rai-manual-staging th, .rai-manual-staging td { font-size:.76rem; padding:6px; }
    .rai-manual-staging input, .rai-manual-staging select { font-size:.76rem; padding:4px 6px; height:34px; border-radius:6px; border-color:#d6dfeb; }
    .rai-manual-staging input:focus, .rai-manual-staging select:focus { border-color: var(--rai-primary); box-shadow: 0 0 0 2px rgba(31,58,95,.1); outline: none; }

    /* Saved first-page applicant editor */
    .rai-editor-card { border:1px solid #cfdced; background:#f9fbfe; }
    .rai-editor-sections { display:grid; grid-template-columns:1fr; gap:14px; }
    .rai-editor-section { background:#fff; border:1px solid var(--rai-border); border-radius:12px; padding:14px; }
    .rai-editor-section-title { color:var(--rai-primary); font-size:.72rem; font-weight:800; letter-spacing:.5px; text-transform:uppercase; margin-bottom:10px; }
    .rai-editor-grid { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:10px; }
    .rai-editor-field label { display:block; color:var(--rai-muted); font-size:.65rem; font-weight:800; letter-spacing:.35px; text-transform:uppercase; margin-bottom:4px; }
    .rai-editor-field .form-control { height:36px; border-radius:8px; border-color:#d6dfeb; font-size:.8rem; }

    /* Editable Page 2 checklist */
    .rai-checklist-edit th { font-size:.65rem; text-transform:uppercase; letter-spacing:.3px; color:#516070; background:#f8fbff; vertical-align:middle; }
    .rai-checklist-edit td { font-size:.76rem; vertical-align:middle; }
    .rai-checklist-edit .form-control { min-width:80px; height:34px; padding:5px 7px; border-radius:7px; font-size:.76rem; }
    .rai-checklist-edit .rai-check-note { min-width:180px; }
    .rai-checklist-signers { display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-top:12px; }
    .rai-checklist-signers .form-group { margin-bottom:0; }

    @media (max-width: 991.98px) {
        .rai-hero-content { align-items:flex-start; }
        .rai-hero-icon { display:none; }
        .rai-section-head { align-items:flex-start; }
        .rai-summary { grid-template-columns: repeat(2, minmax(0,1fr)); }
    }
    @media (max-width: 767.98px) {
        .rai-hero { padding:18px; border-radius:14px; }
        .rai-hero-content { flex-direction:column; }
        .rai-title-block h4 { font-size:1.1rem; }
        .rai-card .card-body { padding:14px; }
        #rai-table thead th { font-size:.58rem; padding:7px 5px; }
        #rai-table td { font-size:.72rem; padding:7px 5px; }
        .rai-summary { grid-template-columns: 1fr; }
        .rai-tab { font-size: .72rem; padding: 8px 6px; }
        .rai-editor-grid { grid-template-columns:repeat(2,minmax(0,1fr)); }
        .rai-checklist-signers { grid-template-columns:1fr; }
    }
    @media (max-width: 575.98px) { .rai-editor-grid { grid-template-columns:1fr; } }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid rai-page-shell">

            <div class="rai-hero">
                <div class="rai-hero-content">
                    <div class="rai-title-block">
                        <h4>Report on Appointments Issued (RAI)</h4>
                        <p>Create report information, then manage applicants and preview the official CSC form — all from this page.</p>
                    </div>
                    <div class="d-flex align-items-center flex-wrap justify-content-end" style="gap:10px;">
                        <button type="button" class="btn btn-light btn-sm font-weight-bold" id="raiNewReportBtn" style="border-radius:10px;">
                            <i class="ph ph-plus-circle mr-1"></i>New Report
                        </button>
                        <div class="rai-hero-icon"><i class="ph ph-file-text"></i></div>
                    </div>
                </div>
            </div>

            <?php if ($this->session->flashdata('success')): ?>
                <div class="alert alert-success" role="alert"><i class="ph ph-check-circle mr-1"></i><?= h($this->session->flashdata('success')) ?></div>
            <?php endif; ?>
            <?php if ($this->session->flashdata('danger')): ?>
                <div class="alert alert-danger" role="alert"><i class="ph ph-warning-circle mr-1"></i><?= h($this->session->flashdata('danger')) ?></div>
            <?php endif; ?>

            <div class="card rai-card">
                <div class="card-body">
                    <div class="rai-section-head">
                        <h5 class="rai-section-title">
                            <i class="ph ph-list-bullets"></i>
                            RAI Reports
                        </h5>
                        <span class="rai-help-chip">
                            <i class="ph ph-info"></i>
                            <span id="raiReportCount"><?= count($reports) ?></span> report(s)
                        </span>
                    </div>

                    <div class="rai-results-wrap">
                        <table class="table table-hover table-bordered" id="rai-table">
                            <thead>
                                <tr>
                                    <th>Reporting Period</th>
                                    <th>Nature of Appointment</th>
                                    <th class="text-center">Applicants</th>
                                    <th>Last Updated</th>
                                    <th style="width:170px">Manage</th>
                                </tr>
                            </thead>
                            <tbody id="raiReportRows">
                            <?php foreach ($reports as $report):
                                $monthIndex = (int) (isset($report['report_month']) ? $report['report_month'] : 0);
                                $period = (isset($months[$monthIndex]) ? $months[$monthIndex] : 'Month ' . $monthIndex) . ' ' . (int) $report['report_year'];
                                $reportId = (int) $report['id'];
                                $updated = isset($report['updated_at']) ? $report['updated_at'] : '';
                            ?>
                                <tr data-report-id="<?= $reportId ?>">
                                    <td><strong><?= h($period) ?></strong></td>
                                    <td><?= h(isset($report['nature_of_appointment']) && $report['nature_of_appointment'] !== '' ? $report['nature_of_appointment'] : '—') ?></td>
                                    <td class="text-center"><span class="rai-count-pill" data-count="<?= (int) (isset($report['applicant_count']) ? $report['applicant_count'] : 0) ?>"><?= (int) (isset($report['applicant_count']) ? $report['applicant_count'] : 0) ?></span></td>
                                    <td><span class="rai-sub"><?= h($updated) ?></span></td>
                                    <td>
                                        <div class="rai-actions">
                                            <button type="button" class="rai-icon-btn is-primary rai-manage-btn" data-id="<?= $reportId ?>" data-toggle="tooltip" data-placement="top" title="Manage">
                                                <i class="ph ph-users"></i>
                                            </button>
                                            <a class="rai-icon-btn is-secondary" href="<?= site_url('RaiReport/preview/' . $reportId) ?>" target="_blank" data-toggle="tooltip" data-placement="top" title="Preview">
                                                <i class="ph ph-printer"></i>
                                            </a>
                                            <button type="button" class="rai-icon-btn is-secondary rai-edit-btn" data-id="<?= $reportId ?>" data-toggle="tooltip" data-placement="top" title="Edit Report Info">
                                                <i class="ph ph-pencil-simple"></i>
                                            </button>
                                            <form method="post" action="<?= site_url('RaiReport/delete/' . $reportId) ?>" onsubmit="return confirm('Delete this report and its applicants?');">
                                                <button type="submit" class="rai-icon-btn is-danger" data-toggle="tooltip" data-placement="top" title="Delete">
                                                    <i class="ph ph-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>

                        <?php if (empty($reports)): ?>
                            <div class="rai-empty-state" id="raiEmptyState">
                                <i class="ph ph-file-text"></i>
                                <strong>No reports yet.</strong>
                                <span>Click "New Report" to create the first report information record.</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- ============ NEW / EDIT REPORT MODAL ============ -->
<div class="modal fade rai-modal" id="raiReportModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog rai-modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-heading">
                    <h5 class="modal-title"><i class="ph ph-file-plus" id="raiReportModalIcon"></i><span id="raiReportModalTitle">New Report Information</span></h5>
                    <small class="modal-subtitle" id="raiReportModalSubtitle">Create a new monthly RAI record.</small>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form id="raiReportForm">
                <div class="modal-body">
                    <div id="raiReportFormAlert"></div>
                    <input type="hidden" name="rai_token" value="<?= h($raiToken) ?>">
                    <input type="hidden" id="raiReportEditId" name="rai_report_edit_id" value="">
                    <div class="form-row rai-form-card">
                        <div class="form-group col-lg-6 col-md-6">
                            <label for="rf_report_month">Reporting Month <span class="rai-req">*</span></label>
                            <select name="report_month" id="rf_report_month" class="form-control">
                                <?php foreach ($months as $number => $name): ?>
                                    <option value="<?= $number ?>" <?= (int) (isset($defaults['report_month']) ? $defaults['report_month'] : date('n')) === $number ? 'selected' : '' ?>><?= $name ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group col-lg-6 col-md-6">
                            <label for="rf_report_year">Reporting Year <span class="rai-req">*</span></label>
                            <input name="report_year" id="rf_report_year" type="number" min="2000" max="2100" class="form-control" value="<?= (int) (isset($defaults['report_year']) ? $defaults['report_year'] : date('Y')) ?>">
                        </div>
                        <div class="form-group col-lg-4 col-md-6">
                            <label for="rf_nature_of_appointment">Nature of Appointment <span class="rai-req">*</span></label>
                            <select name="nature_of_appointment" id="rf_nature_of_appointment" class="form-control" required>
                                <?php foreach ($natureOptions as $natureValue => $natureLabel): ?>
                                    <option value="<?= h($natureValue) ?>" <?= (string) (isset($defaults['nature_of_appointment']) ? $defaults['nature_of_appointment'] : '') === (string) $natureValue ? 'selected' : '' ?>><?= h($natureLabel) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group col-lg-4 col-md-6">
                            <label for="rf_certifying_officer">Certifying Officer</label>
                            <input name="certifying_officer" id="rf_certifying_officer" class="form-control" value="">
                        </div>
                        <div class="form-group col-lg-4 col-md-6">
                            <label for="rf_certifying_position">Certifying Officer Position</label>
                            <input name="certifying_position" id="rf_certifying_position" class="form-control" value="">
                        </div>
                        <div class="form-group col-12">
                            <label for="rf_remarks">CSC Remarks</label>
                            <textarea name="remarks" id="rf_remarks" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light btn-sm font-weight-bold" data-dismiss="modal"><i class="ph ph-x mr-1"></i>Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm font-weight-bold" id="raiReportSaveBtn">
                        <i class="ph ph-floppy-disk mr-1"></i>Save Report
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ============ MANAGE MODAL ============ -->
<div class="modal fade rai-modal" id="raiManageModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog rai-modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="ph ph-users"></i><span id="raiManageTitle">Manage RAI Report</span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div id="raiManageAlert"></div>

                <!-- Report info summary -->
                <div class="card rai-card mb-3">
                    <div class="card-body">
                        <div class="rai-section-head">
                            <h5 class="rai-section-title"><i class="ph ph-clipboard-text"></i>Report Information</h5>
                            <button type="button" class="btn btn-outline-secondary btn-sm font-weight-bold" id="raiEditInfoBtn" style="border-radius:10px;">
                                <i class="ph ph-pencil-simple mr-1"></i>Edit
                            </button>
                        </div>
                        <div class="rai-summary" id="raiReportSummary"></div>
                    </div>
                </div>

                <!-- Add applicants -->
                <div class="card rai-card mb-3">
                    <div class="card-body">
                        <div class="rai-section-head">
                            <h5 class="rai-section-title"><i class="ph ph-user-plus"></i>Add Applicants</h5>
                            <span class="rai-help-chip"><i class="ph ph-info"></i>Select multiple at once</span>
                        </div>

                        <div class="rai-tabs">
                            <button type="button" class="rai-tab active" data-tab="system"><i class="ph ph-database"></i>From System (RQA)</button>
                            <button type="button" class="rai-tab" data-tab="manual"><i class="ph ph-user-plus"></i>Manual Entry</button>
                        </div>

                        <!-- System tab: Select2 multi-select -->
                        <div class="rai-tab-pane active" data-pane="system">
                            <div class="form-group">
                                <label class="font-weight-bold small">Search and select appointed applicants (multiple allowed)</label>
                                <select id="raiSystemSelect" multiple="multiple" style="width:100%"></select>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-primary btn-sm font-weight-bold" id="raiAddSystemBtn" style="border-radius:10px;">
                                    <i class="ph ph-plus mr-1"></i>Add Selected to Report
                                </button>
                            </div>
                        </div>

                        <!-- Manual tab: staging table for multiple entries -->
                        <div class="rai-tab-pane" data-pane="manual">
                            <div class="rai-note mb-2">Add one or more manual applicants below. Click "Add Row" to stage another, then "Add All to Report" to commit them.</div>
                            <div class="rai-table-wrap">
                                <table class="table table-bordered rai-manual-staging" id="raiManualStaging">
                                    <thead>
                                        <tr>
                                            <th style="width:14%">Last Name</th>
                                            <th style="width:14%">First Name</th>
                                            <th style="width:12%">Middle Name</th>
                                            <th style="width:18%">Position Title</th>
                                            <th style="width:10%">Item No.</th>
                                            <th style="width:10%">Date Issued</th>
                                            <th style="width:14%">Nature</th>
                                            <th style="width:8%"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="raiManualBody"></tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-between mt-2">
                                <button type="button" class="btn btn-outline-primary btn-sm font-weight-bold" id="raiAddManualRowBtn" style="border-radius:10px;">
                                    <i class="ph ph-plus mr-1"></i>Add Row
                                </button>
                                <button type="button" class="btn btn-primary btn-sm font-weight-bold" id="raiAddManualAllBtn" style="border-radius:10px;">
                                    <i class="ph ph-check-all mr-1"></i>Add All to Report
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Editable fields printed on RAI Page 1 -->
                <div class="card rai-card rai-editor-card mb-3 rai-hide" id="raiApplicantEditorCard">
                    <div class="card-body">
                        <form id="raiApplicantEditorForm">
                            <div class="rai-section-head">
                                <div>
                                    <h5 class="rai-section-title"><i class="ph ph-pencil-simple"></i><span id="raiApplicantEditorTitle">Edit Applicant</span></h5>
                                    <div class="rai-muted mt-1">These changes are saved only to this RAI report. Publication Mode remains the prescribed <strong>CSC WEBSITE</strong>.</div>
                                </div>
                                <button type="button" class="rai-icon-btn is-secondary" id="raiCloseApplicantEditor" title="Close editor"><i class="ph ph-x"></i></button>
                            </div>
                            <div class="rai-editor-sections" id="raiApplicantEditorFields"></div>
                            <div class="d-flex justify-content-end mt-3" style="gap:8px;">
                                <button type="button" class="btn btn-light btn-sm" id="raiCancelApplicantEdit"><i class="ph ph-x mr-1"></i>Cancel</button>
                                <button type="submit" class="btn btn-primary btn-sm"><i class="ph ph-floppy-disk mr-1"></i>Save Applicant Data</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Current applicants -->
                <div class="card rai-card">
                    <div class="card-body">
                        <div class="rai-section-head">
                            <div>
                                <h5 class="rai-section-title"><i class="ph ph-users"></i>Applicants <span class="rai-count-pill" id="raiApplicantCount">0</span></h5>
                                <div class="rai-muted mt-1">Changes are saved automatically. <span id="raiSaveStatus">Saved</span></div>
                            </div>
                            <button type="button" class="btn btn-outline-secondary btn-sm font-weight-bold" id="raiPreviewBtn" style="border-radius:10px;">
                                <i class="ph ph-printer mr-1"></i>Preview
                            </button>
                        </div>
                        <div class="rai-table-wrap">
                            <table class="rai-table">
                                <thead>
                                    <tr>
                                        <th style="width:40px">No.</th>
                                        <th>Applicant</th>
                                        <th>Position / Item</th>
                                        <th>Date Issued</th>
                                        <th>Nature / Classification</th>
                                        <th>Source</th>
                                        <th style="width:150px">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="raiApplicantRows"></tbody>
                            </table>
                        </div>
                        <div class="rai-empty-state" id="raiApplicantEmpty">
                            <i class="ph ph-user-list"></i>
                            <strong>No applicants yet.</strong>
                            <span>Use the tabs above to add from the system or manually.</span>
                        </div>
                    </div>
                </div>

                <!-- Editable fields printed on RAI Page 2 -->
                <div class="card rai-card mt-3">
                    <div class="card-body">
                        <div class="rai-section-head">
                            <div>
                                <h5 class="rai-section-title"><i class="ph ph-check-square"></i>Page 2 Checklist</h5>
                                <div class="rai-muted mt-1">Enter HRMO/CSC FO marks or notes when available. The official requirement wording remains fixed.</div>
                            </div>
                            <span class="rai-help-chip" id="raiChecklistSaveStatus"><i class="ph ph-info"></i>Saved with this report</span>
                        </div>
                        <div class="rai-table-wrap">
                            <table class="table table-bordered rai-checklist-edit mb-0">
                                <thead><tr><th style="width:42px">No.</th><th>Requirement</th><th style="width:110px">HRMO</th><th style="width:110px">CSC FO</th><th style="width:24%">Note</th></tr></thead>
                                <tbody id="raiChecklistRows"></tbody>
                            </table>
                        </div>
                        <div class="rai-checklist-signers">
                            <div class="form-group">
                                <label class="font-weight-bold small" for="raiCscReceivingOfficer">CSC FO Receiving Officer Name</label>
                                <input id="raiCscReceivingOfficer" class="form-control form-control-sm" maxlength="150" placeholder="Leave blank for signature line only">
                            </div>
                            <div class="form-group">
                                <label class="font-weight-bold small" for="raiCscReceivingPosition">CSC FO Receiving Officer Position</label>
                                <input id="raiCscReceivingPosition" class="form-control form-control-sm" maxlength="150" value="CSC FO Receiving Officer">
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mt-3">
                            <button type="button" class="btn btn-primary btn-sm font-weight-bold" id="raiSaveChecklistBtn"><i class="ph ph-floppy-disk mr-1"></i>Save Page 2</button>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light btn-sm font-weight-bold" data-dismiss="modal"><i class="ph ph-check mr-1"></i>Done</button>
            </div>
        </div>
    </div>
</div>

<!-- Hidden preview form -->
<form id="raiPreviewForm" method="post" action="<?= h($previewUrl) ?>" target="_blank">
    <input type="hidden" name="rai_token" value="<?= h($raiToken) ?>">
    <input type="hidden" name="payload" id="raiPayload">
</form>

<script>
(function () {
'use strict';
const months = <?= json_encode(array_values($months), $jsonFlags) ?>;
const monthMap = <?= json_encode($months, $jsonFlags) ?>;
const storeUrl = <?= json_encode($storeUrl, $jsonFlags) ?>;
const updateUrl = <?= json_encode($updateUrl, $jsonFlags) ?>;
const dataUrl = <?= json_encode($dataUrl, $jsonFlags) ?>;
const candidatesUrl = <?= json_encode($candidatesUrl, $jsonFlags) ?>;
const bulkCandidatesUrl = <?= json_encode($bulkCandidatesUrl, $jsonFlags) ?>;
const saveApplicantsUrl = <?= json_encode($saveApplicantsUrl, $jsonFlags) ?>;
const saveChecklistUrl = <?= json_encode($saveChecklistUrl, $jsonFlags) ?>;
const previewUrl = <?= json_encode($previewUrl, $jsonFlags) ?>;
const raiToken = <?= json_encode($raiToken, $jsonFlags) ?>;
const reportDefaults = <?= json_encode($defaults, $jsonFlags) ?>;
const checklistDefaults = <?= json_encode($checklistDefaults, $jsonFlags) ?>;

const classifications = [['','Choose'],['sub_original','Substitute — Original'],['sub_reemployment','Substitute — Reemployment'],['perm_original','Permanent — Original'],['perm_transfer','Permanent — Transfer'],['perm_reemployment','Permanent — Reemployment'],['reclassification','Reclassification'],['other','Other']];
const natureOptions = <?= json_encode(array_keys($natureOptions), $jsonFlags) ?>;
const applicantFields = ['source_type','source_id','classification_key','classification_label','date_issued','last_name','first_name','name_extension','middle_name','sex','pwd','position_level','position_title','item_number','salary_grade','salary_step','monthly_salary','employment_status','employment_period_from','employment_period_to','nature_of_appointment','publication_from','publication_to','publication_mode','appointment_identification_no','csc_action','csc_action_date','csc_release_date'];
const applicantEditSections = [
    ['Personal Information', [['last_name','Last Name'],['first_name','First Name'],['middle_name','Middle Name'],['name_extension','Name Extension'],['sex','Sex','sex'],['pwd','PWD','pwd']]],
    ['Appointment Information', [['date_issued','Date Issued / Effectivity','date'],['position_level','Position Level','level'],['position_title','Position Title'],['item_number','Item Number'],['employment_status','Employment Status'],['nature_of_appointment','Nature of Appointment','nature'],['classification_key','Classification','class']]],
    ['Salary Information', [['salary_grade','Salary / Job / Pay Grade'],['salary_step','Step'],['monthly_salary','Monthly Salary Rate','number']]],
    ['Employment and Publication', [['employment_period_from','Employment Period From','date'],['employment_period_to','Employment Period To','date'],['publication_from','Publication From','date'],['publication_to','Publication To','date']]],
    ['CSC Information', [['appointment_identification_no','Appointment Identification Number'],['csc_action','CSC Action','action'],['csc_action_date','Date of CSC Action','date'],['csc_release_date','Date of Release','date']]]
];
const editableApplicantFields = applicantEditSections.flatMap(section => section[1]).map(field => field[0]);

let currentReportId = null;
let currentReport = {};
let items = [];
let editingApplicantIndex = null;
let currentChecklist = JSON.parse(JSON.stringify(checklistDefaults));
let saveTimer = null;
let systemSelectReady = false;

const $ = id => document.getElementById(id);
const esc = v => String(v == null ? '' : v).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
function classificationName(k){ const f = classifications.find(c => c[0]===k); return f ? f[1] : (k || 'Unclassified'); }
function blankItem(){ const x = {source_type:'manual',source_id:'',classification_label:''}; applicantFields.forEach(k => x[k]=''); return x; }
function setAlert(el, msg, isError){ el.innerHTML = msg ? '<div class="alert alert-'+(isError?'danger':'success')+' py-2"><i class="ph ph-'+(isError?'warning-circle':'check-circle')+' mr-1"></i>'+esc(msg)+'</div>' : ''; }
function normalizeChecklist(value){
    const result = JSON.parse(JSON.stringify(checklistDefaults));
    let parsed = value;
    if (typeof parsed === 'string' && parsed.trim()) { try { parsed = JSON.parse(parsed); } catch (_) { parsed = {}; } }
    if (!parsed || typeof parsed !== 'object') parsed = {};
    const sourceItems = Array.isArray(parsed) ? parsed : (Array.isArray(parsed.items) ? parsed.items : []);
    result.items.forEach((item, index) => {
        const source = sourceItems.find(x => x && x.code === item.code) || sourceItems[index] || {};
        item.hrmo_status = String(source.hrmo_status || '');
        item.csc_status = String(source.csc_status || '');
        item.note = String(source.note || '');
    });
    result.csc_receiving_officer = String(parsed.csc_receiving_officer || '');
    result.csc_receiving_position = String(parsed.csc_receiving_position || 'CSC FO Receiving Officer');
    return result;
}

/* ---------- jQuery shorthand (only for Bootstrap/Select2 calls) ---------- */
function jq(selector) { return window.jQuery(selector); }

/* ---------- Report form modal ---------- */
function openReportModal(mode, report){
    const isEdit = mode === 'edit';
    $('raiReportModalTitle').textContent = isEdit ? 'Edit Report Information' : 'New Report Information';
    $('raiReportModalSubtitle').textContent = isEdit ? 'Update this report’s period, appointment type, and certification details.' : 'Create a new monthly RAI record.';
    $('raiReportModalIcon').className = isEdit ? 'ph ph-pencil-simple' : 'ph ph-file-plus';
    $('raiReportEditId').value = isEdit ? (report.id || '') : '';
    const f = $('raiReportForm');
    f.reset();
    setAlert($('raiReportFormAlert'), '');
    const r = report || {};
    $('rf_report_month').value = r.report_month || (currentReport.report_month || <?= (int) date('n') ?>);
    $('rf_report_year').value = r.report_year || (currentReport.report_year || <?= (int) date('Y') ?>);
    $('rf_nature_of_appointment').value = r.nature_of_appointment || reportDefaults.nature_of_appointment || 'Original';
    $('rf_certifying_officer').value = r.certifying_officer || reportDefaults.certifying_officer || '';
    $('rf_certifying_position').value = r.certifying_position || reportDefaults.certifying_position || '';
    $('rf_remarks').value = r.remarks || '';
    jq('#raiReportModal').modal('show');
}

$('raiNewReportBtn').addEventListener('click', () => openReportModal('create', {}));

$('raiReportForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const editId = $('raiReportEditId').value;
    const btn = $('raiReportSaveBtn');
    btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm mr-1"></span>Saving...';
    setAlert($('raiReportFormAlert'), '');
    const fd = new FormData(e.target);
    fd.append('rai_token', raiToken);
    try {
        const url = editId ? updateUrl + '/' + editId : storeUrl;
        const res = await fetch(url, { method:'POST', credentials:'same-origin', headers:{'X-Requested-With':'XMLHttpRequest'}, body: fd });
        const j = await res.json();
        if (!res.ok || j.status !== 'success') throw new Error(j.message || 'Save failed');
        setAlert($('raiReportFormAlert'), j.message, false);
        if (editId) {
            currentReport = j.report || currentReport;
            renderSummary();
            updateRowInTable(currentReport);
            setTimeout(() => jq('#raiReportModal').modal('hide'), 600);
        } else {
            window.location.reload();
            return;
        }
    } catch (err) {
        setAlert($('raiReportFormAlert'), err.message, true);
    } finally {
        btn.disabled = false; btn.innerHTML = '<i class="ph ph-floppy-disk mr-1"></i>Save Report';
    }
});

/* ---------- Manage modal ---------- */
async function openManage(reportId){
    currentReportId = reportId;
    items = [];
    currentReport = {};
    $('raiManageTitle').textContent = 'Manage RAI Report';
    $('raiManageAlert').innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary"></div><div class="mt-2 font-weight-bold">Loading report...</div></div>';
    jq('#raiManageModal').modal('show');
    try {
        const res = await fetch(dataUrl + '/' + reportId, { credentials:'same-origin', headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'} });
        const j = await res.json();
        if (!res.ok || j.status !== 'success') throw new Error(j.message || 'Failed to load report');
        currentReport = j.report || {};
        items = Array.isArray(j.applicants) ? j.applicants : [];
        currentChecklist = normalizeChecklist(currentReport.checklist_json || {});
        $('raiManageAlert').innerHTML = '';
        closeApplicantEditor();
        renderSummary();
        renderApplicants();
        renderChecklist();
        resetSystemSelect();
        resetManualStaging();
    } catch (err) {
        $('raiManageAlert').innerHTML = '<div class="alert alert-danger"><i class="ph ph-warning-circle mr-1"></i>'+esc(err.message)+'</div>';
    }
}

function renderSummary(){
    const r = currentReport;
    const monthName = monthMap[r.report_month] || ('Month ' + r.report_month);
    const period = monthName + ' ' + (r.report_year || '');
    $('raiReportSummary').innerHTML = [
        ['Reporting Period', period],
        ['Nature of Appointment', r.nature_of_appointment || '—'],
        ['Certifying Officer', r.certifying_officer || '—'],
        ['Remarks', r.remarks || '—']
    ].map(([k,v]) => '<div><label>'+esc(k)+'</label>'+esc(v)+'</div>').join('');
}

$('raiEditInfoBtn').addEventListener('click', () => {
    jq('#raiManageModal').modal('hide');
    setTimeout(() => openReportModal('edit', currentReport), 350);
});

/* ---------- Tabs ---------- */
document.querySelectorAll('.rai-tab').forEach(t => t.addEventListener('click', () => {
    document.querySelectorAll('.rai-tab').forEach(x => x.classList.remove('active'));
    document.querySelectorAll('.rai-tab-pane').forEach(x => x.classList.remove('active'));
    t.classList.add('active');
    document.querySelector('.rai-tab-pane[data-pane="'+t.dataset.tab+'"]').classList.add('active');
}));

/* ---------- System Select2 multi-select (like appointment_reports) ---------- */
function initSystemSelect(){
    if (systemSelectReady || !window.jQuery || !window.jQuery.fn.select2) return;
    systemSelectReady = true;
    jq('#raiSystemSelect').select2({
        width: '100%',
        placeholder: 'Search by name, position, or item number...',
        allowClear: true,
        multiple: true,
        ajax: {
            url: candidatesUrl,
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return { search: params.term || '', page: params.page || 1 };
            },
            processResults: function (data) {
                return {
                    results: (data.rows || []).map(r => ({
                        id: r.id,
                        text: r.name + ' — ' + (r.position||'') + (r.itemNumber ? ' ('+r.itemNumber+')' : '') + (r.salaryGrade ? ' SG-'+r.salaryGrade : '') + (r.dateIssued ? ' ['+r.dateIssued+']' : '')
                    })),
                    pagination: { more: (data.page || 1) < (data.pages || 1) }
                };
            },
            cache: true
        },
        minimumInputLength: 1,
        templateSelection: function(item) {
            const text = item.text || '';
            return text.length > 50 ? text.substring(0, 50) + '...' : text;
        }
    });
}

function resetSystemSelect(){
    if (systemSelectReady) {
        jq('#raiSystemSelect').val(null).trigger('change');
    } else {
        $('raiSystemSelect').innerHTML = '';
    }
}

/* Initialize Select2 on DOM ready (like appointment_reports does) */
function domReady(fn){
    if (document.readyState !== 'loading') fn();
    else document.addEventListener('DOMContentLoaded', fn);
}
domReady(initSystemSelect);

$('raiAddSystemBtn').addEventListener('click', async () => {
    if (!window.jQuery) return;
    const selected = jq('#raiSystemSelect').val();
    if (!selected || !selected.length) { alert('Select at least one applicant first.'); return; }
    const btn = $('raiAddSystemBtn');
    btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm mr-1"></span>Adding...';
    try {
        const fd = new FormData();
        fd.append('rai_token', raiToken);
        selected.forEach(id => fd.append('ids[]', id));
        const res = await fetch(bulkCandidatesUrl, { method:'POST', credentials:'same-origin', headers:{'X-Requested-With':'XMLHttpRequest'}, body: fd });
        const j = await res.json();
        if (!res.ok || j.status !== 'success') throw new Error(j.message || 'Failed to fetch applicants');
        const existing = new Set(items.filter(x => x.source_type === 'rqa_recommendation').map(x => String(x.source_id)));
        let added = 0;
        (j.applicants || []).forEach(a => {
            if (existing.has(String(a.source_id))) return;
            const entry = Object.assign(blankItem(), a);
            entry.source_type = 'rqa_recommendation';
            if (!entry.nature_of_appointment) entry.nature_of_appointment = currentReport.nature_of_appointment || '';
            items.push(entry);
            added++;
        });
        renderApplicants();
        scheduleSave();
        resetSystemSelect();
        if (added === 0) alert('Those applicants are already in the report.');
    } catch (err) {
        alert(err.message);
    } finally {
        btn.disabled = false; btn.innerHTML = '<i class="ph ph-plus mr-1"></i>Add Selected to Report';
    }
});

/* ---------- Manual staging table ---------- */
function manualRowHtml(){
    const reportNature = currentReport.nature_of_appointment || '';
    const opts = arr => arr.map(o => '<option value="'+esc(o)+'" '+(o===reportNature?'selected':'')+'>'+esc(o||'—')+'</option>').join('');
    return '<tr class="rai-staging-row">'
        + '<td><input class="form-control form-control-sm m-last" placeholder="Last"></td>'
        + '<td><input class="form-control form-control-sm m-first" placeholder="First"></td>'
        + '<td><input class="form-control form-control-sm m-middle" placeholder="Middle"></td>'
        + '<td><input class="form-control form-control-sm m-position" placeholder="Position Title"></td>'
        + '<td><input class="form-control form-control-sm m-item" placeholder="Item No."></td>'
        + '<td><input type="date" class="form-control form-control-sm m-date"></td>'
        + '<td><select class="form-control form-control-sm m-nature">'+opts(natureOptions)+'</select></td>'
        + '<td><button type="button" class="rai-icon-btn is-danger rai-manual-remove" title="Remove row"><i class="ph ph-x"></i></button></td>'
        + '</tr>';
}
function resetManualStaging(){
    const body = $('raiManualBody');
    body.innerHTML = manualRowHtml() + manualRowHtml();
}
$('raiAddManualRowBtn').addEventListener('click', () => { $('raiManualBody').insertAdjacentHTML('beforeend', manualRowHtml()); });
$('raiManualBody').addEventListener('click', e => {
    const b = e.target.closest('.rai-manual-remove');
    if (b) b.closest('tr').remove();
});
$('raiAddManualAllBtn').addEventListener('click', () => {
    const rows = $('raiManualBody').querySelectorAll('.rai-staging-row');
    let added = 0;
    rows.forEach(tr => {
        const last = tr.querySelector('.m-last').value.trim();
        const first = tr.querySelector('.m-first').value.trim();
        if (!last && !first) return;
        const entry = blankItem();
        entry.source_type = 'manual';
        entry.last_name = last;
        entry.first_name = first;
        entry.middle_name = tr.querySelector('.m-middle').value.trim();
        entry.position_title = tr.querySelector('.m-position').value.trim();
        entry.item_number = tr.querySelector('.m-item').value.trim();
        entry.date_issued = tr.querySelector('.m-date').value;
        entry.nature_of_appointment = tr.querySelector('.m-nature').value || currentReport.nature_of_appointment || '';
        items.push(entry);
        added++;
    });
    if (added === 0) { alert('Fill at least a last or first name in a row before adding.'); return; }
    renderApplicants();
    scheduleSave();
    resetManualStaging();
});

/* ---------- Editable Page 1 applicant fields ---------- */
function applicantEditorControl(key, label, type, item){
    const value = String(item[key] || '');
    let options = null;
    if (type === 'sex') options = [['','Select'],['M','Male'],['F','Female']];
    if (type === 'pwd') options = [['','Not provided'],['Y','Yes'],['N','No']];
    if (type === 'level') options = [['','Select'],['1st','1st'],['2nd','2nd']];
    if (type === 'action') options = [['','Blank / Not yet recorded'],['V','Validated'],['INV','Invalidated']];
    if (type === 'class') options = classifications.slice();
    if (type === 'nature') options = [['','Select']].concat(natureOptions.map(x => [x,x]));
    let control = '';
    if (options) {
        if (value && !options.some(option => option[0] === value)) options.push([value, type === 'class' ? (item.classification_label || value) : value]);
        control = '<select class="form-control" name="'+esc(key)+'">'+options.map(option => '<option value="'+esc(option[0])+'" '+(value===option[0]?'selected':'')+'>'+esc(option[1])+'</option>').join('')+'</select>';
    } else {
        const inputType = type === 'date' ? 'date' : (type === 'number' ? 'number' : 'text');
        control = '<input class="form-control" name="'+esc(key)+'" type="'+inputType+'" '+(inputType==='number'?'min="0" step="0.01"':'maxlength="255"')+' value="'+esc(value)+'">';
    }
    return '<div class="rai-editor-field"><label>'+esc(label)+'</label>'+control+'</div>';
}
function openApplicantEditor(index){
    if (!items[index]) return;
    editingApplicantIndex = index;
    const item = items[index];
    $('raiApplicantEditorTitle').textContent = 'Edit Applicant '+(index+1)+' — '+([item.last_name,item.first_name].filter(Boolean).join(', ') || 'Unnamed');
    $('raiApplicantEditorFields').innerHTML = applicantEditSections.map(section => '<section class="rai-editor-section"><div class="rai-editor-section-title">'+esc(section[0])+'</div><div class="rai-editor-grid">'+section[1].map(field => applicantEditorControl(field[0],field[1],field[2],item)).join('')+'</div></section>').join('');
    $('raiApplicantEditorCard').classList.remove('rai-hide');
    $('raiApplicantEditorCard').scrollIntoView({behavior:'smooth',block:'start'});
}
function closeApplicantEditor(){
    editingApplicantIndex = null;
    $('raiApplicantEditorCard').classList.add('rai-hide');
    $('raiApplicantEditorFields').innerHTML = '';
}
$('raiCloseApplicantEditor').addEventListener('click', closeApplicantEditor);
$('raiCancelApplicantEdit').addEventListener('click', closeApplicantEditor);
$('raiApplicantEditorForm').addEventListener('submit', event => {
    event.preventDefault();
    if (editingApplicantIndex === null || !items[editingApplicantIndex]) return;
    const form = new FormData(event.target);
    const entry = Object.assign({}, items[editingApplicantIndex]);
    editableApplicantFields.forEach(key => entry[key] = String(form.get(key) || '').trim());
    if (entry.employment_period_from && entry.employment_period_to && entry.employment_period_from > entry.employment_period_to) { alert('Employment period end date must not precede its start date.'); return; }
    if (entry.publication_from && entry.publication_to && entry.publication_from > entry.publication_to) { alert('Publication end date must not precede its start date.'); return; }
    const classificationSelect = event.target.elements.classification_key;
    entry.classification_label = classificationSelect && classificationSelect.selectedIndex >= 0
        ? classificationSelect.options[classificationSelect.selectedIndex].text
        : classificationName(entry.classification_key);
    items[editingApplicantIndex] = entry;
    closeApplicantEditor();
    renderApplicants();
    scheduleSave();
});

/* ---------- Applicants table ---------- */
function renderApplicants(){
    const body = $('raiApplicantRows');
    body.innerHTML = '';
    items.forEach((x, i) => {
        const tr = document.createElement('tr');
        tr.innerHTML = '<td>'+(i+1)+'</td>'
            + '<td><strong>'+esc([x.last_name, x.first_name].filter(Boolean).join(', ') || 'Unnamed applicant')+'</strong><div class="rai-muted">'+esc(x.middle_name||'')+'</div></td>'
            + '<td>'+esc(x.position_title||'—')+'<div class="rai-muted">'+esc(x.item_number||'')+'</div></td>'
            + '<td>'+esc(x.date_issued||'—')+'</td>'
            + '<td>'+esc(x.nature_of_appointment||'—')+'<div class="rai-muted">'+esc(classificationName(x.classification_key))+'</div></td>'
            + '<td><span class="rai-pill">'+(x.source_type==='manual'?'Manual':'RQA')+'</span></td>'
            + '<td><div class="rai-actions">'
            + '<button type="button" class="rai-icon-btn" data-act="edit" data-i="'+i+'" data-toggle="tooltip" title="Edit saved report data"><i class="ph ph-pencil-simple"></i></button>'
            + '<button type="button" class="rai-icon-btn" data-act="up" data-i="'+i+'" '+(i===0?'disabled':'')+' data-toggle="tooltip" title="Move up"><i class="ph ph-arrow-up"></i></button>'
            + '<button type="button" class="rai-icon-btn" data-act="down" data-i="'+i+'" '+(i===items.length-1?'disabled':'')+' data-toggle="tooltip" title="Move down"><i class="ph ph-arrow-down"></i></button>'
            + '<button type="button" class="rai-icon-btn is-danger" data-act="remove" data-i="'+i+'" data-toggle="tooltip" title="Remove"><i class="ph ph-trash"></i></button>'
            + '</div></td>';
        body.appendChild(tr);
    });
    $('raiApplicantEmpty').classList.toggle('rai-hide', items.length > 0);
    $('raiApplicantCount').textContent = items.length;
    if (window.jQuery && window.jQuery.fn.tooltip) jq('#raiApplicantRows [data-toggle="tooltip"]').tooltip();
}
$('raiApplicantRows').addEventListener('click', e => {
    const b = e.target.closest('button[data-act]');
    if (!b) return;
    const i = Number(b.dataset.i);
    if (b.dataset.act === 'edit') { openApplicantEditor(i); return; }
    if (b.dataset.act === 'up' && i > 0) { [items[i-1], items[i]] = [items[i], items[i-1]]; renderApplicants(); scheduleSave(); }
    if (b.dataset.act === 'down' && i < items.length-1) { [items[i+1], items[i]] = [items[i], items[i+1]]; renderApplicants(); scheduleSave(); }
    if (b.dataset.act === 'remove') { if (confirm('Remove this applicant from the report?')) { items.splice(i,1); renderApplicants(); scheduleSave(); } }
});

/* ---------- Editable Page 2 checklist ---------- */
function renderChecklist(){
    const body = $('raiChecklistRows');
    body.innerHTML = '';
    currentChecklist.items.forEach((item, index) => {
        const tr = document.createElement('tr');
        tr.innerHTML = '<td class="text-center font-weight-bold">'+(index+1)+'</td>'
            + '<td>'+esc(item.label)+'</td>'
            + '<td><input class="form-control cl-hrmo" maxlength="50" value="'+esc(item.hrmo_status||'')+'" placeholder="✓ / status"></td>'
            + '<td><input class="form-control cl-csc" maxlength="50" value="'+esc(item.csc_status||'')+'" placeholder="✓ / status"></td>'
            + '<td><input class="form-control rai-check-note cl-note" maxlength="500" value="'+esc(item.note||'')+'" placeholder="Optional note"></td>';
        body.appendChild(tr);
    });
    $('raiCscReceivingOfficer').value = currentChecklist.csc_receiving_officer || '';
    $('raiCscReceivingPosition').value = currentChecklist.csc_receiving_position || 'CSC FO Receiving Officer';
}
function collectChecklist(){
    const rows = $('raiChecklistRows').querySelectorAll('tr');
    rows.forEach((row,index) => {
        if (!currentChecklist.items[index]) return;
        currentChecklist.items[index].hrmo_status = row.querySelector('.cl-hrmo').value.trim();
        currentChecklist.items[index].csc_status = row.querySelector('.cl-csc').value.trim();
        currentChecklist.items[index].note = row.querySelector('.cl-note').value.trim();
    });
    currentChecklist.csc_receiving_officer = $('raiCscReceivingOfficer').value.trim();
    currentChecklist.csc_receiving_position = $('raiCscReceivingPosition').value.trim() || 'CSC FO Receiving Officer';
}
$('raiSaveChecklistBtn').addEventListener('click', async () => {
    if (!currentReportId) return;
    collectChecklist();
    const button = $('raiSaveChecklistBtn');
    const status = $('raiChecklistSaveStatus');
    button.disabled = true;
    button.innerHTML = '<span class="spinner-border spinner-border-sm mr-1"></span>Saving...';
    status.innerHTML = '<i class="ph ph-circle-notch"></i>Saving Page 2...';
    try {
        const body = new URLSearchParams({rai_token:raiToken,payload:JSON.stringify(currentChecklist)});
        const response = await fetch(saveChecklistUrl+'/'+currentReportId,{method:'POST',credentials:'same-origin',headers:{'Content-Type':'application/x-www-form-urlencoded','X-Requested-With':'XMLHttpRequest'},body});
        const data = await response.json();
        if (!response.ok || data.status !== 'success') throw new Error(data.message || 'Save failed');
        currentChecklist = normalizeChecklist(data.checklist || currentChecklist);
        currentReport.checklist_json = JSON.stringify(currentChecklist);
        renderChecklist();
        status.innerHTML = '<i class="ph ph-check-circle"></i>Page 2 saved';
    } catch (error) {
        status.innerHTML = '<i class="ph ph-warning-circle"></i>'+esc(error.message);
    } finally {
        button.disabled = false;
        button.innerHTML = '<i class="ph ph-floppy-disk mr-1"></i>Save Page 2';
    }
});

/* ---------- Auto-save ---------- */
function setSaveStatus(text, isError){ const el = $('raiSaveStatus'); el.textContent = text; el.style.color = isError ? '#b2392b' : ''; }
function scheduleSave(){ setSaveStatus('Saving...'); clearTimeout(saveTimer); saveTimer = setTimeout(persistApplicants, 400); }
async function persistApplicants(){
    if (!currentReportId) return;
    try {
        const body = new URLSearchParams({ rai_token: raiToken, payload: JSON.stringify({ applicants: items }) });
        const res = await fetch(saveApplicantsUrl + '/' + currentReportId, { method:'POST', credentials:'same-origin', headers:{'Content-Type':'application/x-www-form-urlencoded','X-Requested-With':'XMLHttpRequest'}, body });
        const j = await res.json();
        if (!res.ok || j.status !== 'success') throw new Error(j.message || 'Save failed');
        setSaveStatus('Saved');
        updateCountInTable(currentReportId, items.length);
    } catch (e) {
        setSaveStatus('Save failed: '+e.message, true);
    }
}

/* ---------- Preview ---------- */
$('raiPreviewBtn').addEventListener('click', () => {
    if (!items.length) { alert('Add at least one applicant before previewing.'); return; }
    const header = {};
    ['report_month','report_year','nature_of_appointment','certifying_officer','certifying_position','appointing_officer','appointing_position','checklist_json','remarks'].forEach(k => header[k] = currentReport[k] || '');
    $('raiPayload').value = JSON.stringify({ header, applicants: items });
    $('raiPreviewForm').submit();
});

/* ---------- Table row helpers ---------- */
function updateRowInTable(report){
    const row = document.querySelector('#raiReportRows tr[data-report-id="'+report.id+'"]');
    if (!row) return;
    const monthName = monthMap[report.report_month] || ('Month ' + report.report_month);
    row.cells[0].innerHTML = '<strong>'+esc(monthName+' '+report.report_year)+'</strong>';
    row.cells[1].textContent = report.nature_of_appointment || '—';
    row.cells[3].innerHTML = '<span class="rai-sub">'+esc(report.updated_at||'')+'</span>';
}
function updateCountInTable(reportId, count){
    const row = document.querySelector('#raiReportRows tr[data-report-id="'+reportId+'"]');
    if (!row) return;
    const pill = row.querySelector('.rai-count-pill');
    if (pill) { pill.textContent = count; pill.dataset.count = count; }
}

/* ---------- Wire up manage/edit buttons ---------- */
document.addEventListener('click', e => {
    const mb = e.target.closest('.rai-manage-btn');
    if (mb) { openManage(Number(mb.dataset.id)); return; }
    const eb = e.target.closest('.rai-edit-btn');
    if (eb) {
        const id = Number(eb.dataset.id);
        fetch(dataUrl + '/' + id, { credentials:'same-origin', headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'} })
            .then(r => r.json())
            .then(j => { if (j.status === 'success') openReportModal('edit', j.report); })
            .catch(() => alert('Could not load report for editing.'));
    }
});

/* ---------- Init tooltips on page ---------- */
domReady(() => {
    if (window.jQuery && window.jQuery.fn.tooltip) jq('[data-toggle="tooltip"]').tooltip();
});
})();
</script>
