<?php
if (!function_exists('h')) {
    function h($v)
    {
        return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
    }
}

$suffixes = $jobTypeSuffixes ?? [];
$selectedJobId = (int) ($selectedJobId ?? 0);
$years = $years ?? [];
$selectedYear = (int) ($selectedYear ?? 0);
?>

<style>
    :root {
        --rqa-primary: #1f3a5f;
        --rqa-primary-2: #274b7a;
        --rqa-accent: #1abc9c;
        --rqa-bg: #f4f7fb;
        --rqa-border: #e5ecf5;
        --rqa-text: #25364a;
        --rqa-muted: #7b8794;
        --rqa-soft: #eef5ff;
        --rqa-success-soft: #e8fff8;
        --rqa-success: #129777;
    }

    .content-page {
        background: var(--rqa-bg);
        min-height: 100vh;
    }

    .rqa-page-shell {
        padding-bottom: 24px;
    }

    .rqa-hero {
        position: relative;
        overflow: hidden;
        border-radius: 18px;
        padding: 22px 24px;
        margin-bottom: 18px;
        background: linear-gradient(135deg, var(--rqa-primary), var(--rqa-primary-2));
        box-shadow: 0 14px 35px rgba(31, 58, 95, .18);
        color: #fff;
    }

    .rqa-hero:before {
        content: "";
        position: absolute;
        width: 230px;
        height: 230px;
        border-radius: 50%;
        right: -80px;
        top: -95px;
        background: rgba(255,255,255,.10);
    }

    .rqa-hero:after {
        content: "";
        position: absolute;
        width: 160px;
        height: 160px;
        border-radius: 50%;
        right: 90px;
        bottom: -100px;
        background: rgba(26,188,156,.22);
    }

    .rqa-hero-content {
        position: relative;
        z-index: 2;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
    }

    .rqa-title-block h4 {
        color: #fff;
        font-size: 1.25rem;
        font-weight: 800;
        letter-spacing: .2px;
        margin: 0 0 5px;
    }

    .rqa-title-block p {
        color: rgba(255,255,255,.82);
        margin: 0;
        max-width: 760px;
        font-size: .86rem;
        line-height: 1.45;
    }

    .rqa-hero-icon {
        width: 54px;
        height: 54px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255,255,255,.14);
        border: 1px solid rgba(255,255,255,.18);
        flex: 0 0 auto;
    }

    .rqa-hero-icon i {
        font-size: 28px;
        color: #fff;
    }

    #rqa-count-badge {
        display: none;
        border-radius: 999px;
        padding: .5rem .75rem;
        font-size: .78rem;
        font-weight: 700;
        background: rgba(255,255,255,.16);
        border: 1px solid rgba(255,255,255,.22);
        color: #fff;
        white-space: nowrap;
    }

    .rqa-card {
        border: 0;
        border-radius: 16px;
        box-shadow: 0 8px 26px rgba(31, 58, 95, .08);
        overflow: hidden;
    }

    .rqa-card .card-body {
        padding: 18px;
    }

    .rqa-section-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 14px;
    }

    .rqa-section-title {
        margin: 0;
        font-size: .94rem;
        font-weight: 800;
        color: var(--rqa-text);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .rqa-section-title i {
        width: 32px;
        height: 32px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: var(--rqa-soft);
        color: var(--rqa-primary);
        font-size: 17px;
    }

    .rqa-filter-card {
        margin-bottom: 18px;
    }

    .rqa-filter-card label {
        font-weight: 800;
        font-size: .68rem;
        text-transform: uppercase;
        letter-spacing: .55px;
        color: var(--rqa-muted);
        margin-bottom: 6px;
    }

    .rqa-filter-card .form-group {
        margin-bottom: 0;
    }

    .rqa-filter-grid {
        row-gap: 14px;
    }

    .rqa-help-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: var(--rqa-muted);
        font-size: .76rem;
        background: #f8fafd;
        border: 1px solid var(--rqa-border);
        padding: 6px 10px;
        border-radius: 999px;
    }

    .rqa-results-card .card-body {
        padding: 0;
    }

    .rqa-results-header {
        padding: 16px 18px;
        border-bottom: 1px solid var(--rqa-border);
        background: #fff;
    }

    .rqa-results-title-wrap {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
    }

    .rqa-results-title-wrap h5 {
        margin: 0;
    }

    .rqa-results-subtitle {
        color: var(--rqa-muted);
        font-size: .78rem;
        margin: 4px 0 0;
    }

    .rqa-results-wrap {
        max-height: none;
        overflow: visible;
        border: 0;
        border-radius: 0;
        background: #fff;
        width: 100%;
    }

    #rqa-table {
        width: 100%;
        margin-bottom: 0;
        table-layout: fixed;
        border-collapse: collapse;
    }

    #rqa-table thead th {
        background: #f8fbff;
        border-top: 0;
        border-bottom: 1px solid var(--rqa-border);
        color: #516070;
        font-size: .62rem;
        text-transform: uppercase;
        letter-spacing: .35px;
        white-space: normal;
        padding: 9px 6px;
        font-weight: 800;
        line-height: 1.2;
    }

    #rqa-table td {
        vertical-align: middle;
        font-size: .72rem;
        color: var(--rqa-text);
        padding: 8px 6px;
        border-color: #eef2f7;
        background: #fff;
        line-height: 1.25;
        word-break: normal;
    }

    #rqa-table tbody tr {
        transition: background .18s ease;
    }

    #rqa-table tbody tr:hover td {
        background: #fbfdff;
    }

    #rqa-table td.num,
    #rqa-table th.num {
        text-align: center;
    }

    .rqa-name-cell {
        width: 100%;
    }

    .rqa-name-main {
        display: block;
        font-weight: 800;
        color: #223047;
        font-size: .74rem;
        line-height: 1.2;
        max-width: 100%;
        white-space: normal;
        overflow-wrap: break-word;
    }

    .rqa-name-sub {
        display: flex;
        align-items: center;
        gap: 4px;
        margin-top: 4px;
        font-size: .65rem;
        color: var(--rqa-muted);
        flex-wrap: wrap;
        line-height: 1.15;
    }

    .rqa-code-inline {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        background: #f2f6fb;
        color: #46566a;
        font-weight: 800;
        font-size: .62rem;
        padding: .12rem .35rem;
        border: 1px solid #e5ecf5;
        line-height: 1.1;
        max-width: 100%;
    }

    .rqa-location-tag {
        display: block;
        font-weight: 700;
        color: #34465c;
        font-size: .68rem;
        line-height: 1.25;
        overflow-wrap: break-word;
    }

    .rqa-rank-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 24px;
        height: 24px;
        padding: 0 .35rem;
        border-radius: 999px;
        background: linear-gradient(135deg, #eaf1ff, #f7fbff);
        color: var(--rqa-primary);
        font-weight: 900;
        font-size: .65rem;
        border: 1px solid #dce8f7;
    }

    #rqa-table td.score-cell {
        font-weight: 700;
        color: #435269;
        font-size: .7rem;
    }

    #rqa-table td.total {
        text-align: center;
    }

    .rqa-total-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 42px;
        border-radius: 999px;
        background: var(--rqa-success-soft);
        color: var(--rqa-success);
        border: 1px solid #c9f5eb;
        font-weight: 900;
        padding: .22rem .35rem;
        font-size: .68rem;
    }

    .rqa-item-number,
    .rqa-remarks {
        width: 100%;
        min-width: 0;
        border-radius: 7px;
        border-color: #dfe7f1;
        font-size: .68rem;
        height: 30px;
        padding: 4px 6px;
    }

    .rqa-item-number:focus,
    .rqa-remarks:focus {
        border-color: var(--rqa-accent);
        box-shadow: 0 0 0 .12rem rgba(26,188,156,.13);
    }

    .rqa-recommend-btn {
        width: 100%;
        border-radius: 999px;
        padding: .34rem .35rem;
        font-weight: 800;
        font-size: .62rem;
        background: var(--rqa-accent);
        border-color: var(--rqa-accent);
        box-shadow: 0 5px 12px rgba(26,188,156,.16);
        white-space: normal;
        line-height: 1.1;
    }

    .rqa-recommend-btn:hover {
        background: #16a98b;
        border-color: #16a98b;
    }

    .rqa-empty-state {
        margin: 18px;
        border: 1px dashed #cbd8e7;
        background: #f8fbff;
        color: #526173;
        border-radius: 14px;
        padding: 30px 18px;
        text-align: center;
    }

    .rqa-empty-state i {
        font-size: 36px;
        color: var(--rqa-primary);
        opacity: .78;
        display: block;
        margin-bottom: 8px;
    }

    .rqa-empty-state strong {
        display: block;
        color: var(--rqa-text);
        font-size: .94rem;
        margin-bottom: 4px;
    }

    .rqa-empty-state span {
        font-size: .8rem;
    }

    .rqa-loading-box {
        margin: 18px;
        border-radius: 14px;
        background: #fff;
        border: 1px solid var(--rqa-border);
        padding: 36px 18px;
    }

    .rqa-loading-box .spinner-border {
        width: 2.2rem;
        height: 2.2rem;
    }

    .rqa-table-note {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        font-size: .74rem;
        color: var(--rqa-muted);
    }

    .select2-container {
        width: 100% !important;
    }

    .select2-container .select2-selection--single {
        height: 38px;
        border-radius: 10px;
        border-color: #dfe7f1;
        display: flex;
        align-items: center;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 36px;
        color: #344054;
        padding-left: 12px;
        font-size: .84rem;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
        right: 7px;
    }

    .select2-container--default.select2-container--focus .select2-selection--single,
    .select2-container--default.select2-container--open .select2-selection--single {
        border-color: var(--rqa-accent);
        box-shadow: 0 0 0 .15rem rgba(26,188,156,.13);
    }

    .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: #98a2b3;
    }

    /* Multi-select filters */
    .select2-container--default .select2-selection--multiple {
        min-height: 38px;
        border-radius: 10px;
        border-color: #dfe7f1;
        padding: 1px 4px;
    }

    .select2-container--default.select2-container--focus .select2-selection--multiple,
    .select2-container--default.select2-container--open .select2-selection--multiple {
        border-color: var(--rqa-accent);
        box-shadow: 0 0 0 .15rem rgba(26,188,156,.13);
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background: var(--rqa-soft);
        border: 1px solid #dce8f7;
        color: var(--rqa-primary);
        border-radius: 6px;
        font-size: .76rem;
        font-weight: 700;
        margin-top: 5px;
        padding: 2px 8px;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: var(--rqa-primary);
        margin-right: 5px;
    }

    .select2-container--default .select2-selection--multiple .select2-search__field {
        margin-top: 7px;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__placeholder {
        color: #98a2b3;
        margin-top: 6px;
    }

    @media (max-width: 1199.98px) {
        #rqa-table thead th {
            font-size: .58rem;
            padding: 8px 4px;
        }

        #rqa-table td {
            font-size: .68rem;
            padding: 7px 4px;
        }

        .rqa-name-main {
            font-size: .68rem;
        }

        .rqa-name-sub {
            font-size: .6rem;
        }

        .rqa-item-number,
        .rqa-remarks {
            font-size: .62rem;
            height: 28px;
        }

        .rqa-recommend-btn {
            font-size: .58rem;
        }
    }

    @media (max-width: 991.98px) {
        .rqa-hero-content {
            align-items: flex-start;
        }

        .rqa-hero-icon {
            display: none;
        }

        #rqa-count-badge {
            align-self: flex-start;
        }

        .rqa-results-title-wrap {
            align-items: flex-start;
            flex-direction: column;
        }

        .rqa-table-note {
            display: none;
        }
    }

    @media (max-width: 767.98px) {
        .rqa-hero {
            padding: 18px;
            border-radius: 14px;
        }

        .rqa-hero-content {
            flex-direction: column;
        }

        .rqa-title-block h4 {
            font-size: 1.1rem;
        }

        .rqa-title-block p {
            font-size: .8rem;
        }

        .rqa-card .card-body {
            padding: 14px;
        }

        .rqa-section-head {
            flex-direction: column;
            align-items: flex-start;
        }

        #rqa-table thead th {
            font-size: .52rem;
            padding: 6px 3px;
        }

        #rqa-table td {
            font-size: .6rem;
            padding: 6px 3px;
        }

        .rqa-name-main {
            font-size: .61rem;
        }

        .rqa-name-sub,
        .rqa-code-inline {
            font-size: .52rem;
        }

        .rqa-rank-badge {
            min-width: 20px;
            height: 20px;
            font-size: .55rem;
        }

        .rqa-total-pill {
            min-width: 34px;
            font-size: .56rem;
        }

        .rqa-item-number,
        .rqa-remarks {
            height: 24px;
            font-size: .52rem;
            padding: 2px 4px;
        }

        .rqa-recommend-btn {
            font-size: .5rem;
            padding: .26rem .2rem;
        }
    }

    /* Per-row School picker (Select2) sizing */
    #rqa-table td .select2-container { width: 100% !important; min-width: 150px; }
    #rqa-table td .select2-container--default .select2-selection--single {
        height: 28px; border-color: #ced4da;
    }
    #rqa-table td .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 26px; font-size: .62rem; padding-left: 6px; padding-right: 18px;
    }
    #rqa-table td .select2-container--default .select2-selection--single .select2-selection__arrow { height: 26px; }
    #rqa-table td .select2-container--default .select2-selection--single .select2-selection__clear { font-size: .8rem; }

    /* Legend */
    .rqa-legend {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 14px;
        margin-top: 10px;
    }
    .rqa-legend-item {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: .72rem;
        font-weight: 700;
        color: var(--rqa-muted);
    }
    .rqa-legend-swatch {
        width: 14px;
        height: 14px;
        border-radius: 4px;
        display: inline-block;
        border: 1px solid rgba(0,0,0,.08);
    }
    .rqa-legend-swatch.tie { background: #fff3cd; border-color: #ffe69c; }
    .rqa-legend-swatch.corr { background: #dce9ff; border-color: #b6d0ff; }
    .rqa-legend-hint i { font-size: 15px; vertical-align: middle; }

    /* Tie + Corrigendum/Addendum row highlighting */
    #rqa-table tbody tr.rqa-tie td { background: #fff8e6; }
    #rqa-table tbody tr.rqa-tie:hover td { background: #fff2d0; }
    #rqa-table tbody tr.rqa-corr td { background: #eaf1ff; }
    #rqa-table tbody tr.rqa-corr:hover td { background: #ddeaff; }
    #rqa-table tbody tr.rqa-drag-over td { box-shadow: inset 0 2px 0 0 var(--rqa-accent); }
    #rqa-table tbody tr.rqa-dragging td { opacity: .5; }

    /* Drag handle (only shown on tied rows) */
    .rqa-drag-handle {
        cursor: grab;
        color: #b0a36a;
        margin-right: 3px;
        display: inline-flex;
        align-items: center;
        font-size: 15px;
    }
    .rqa-drag-handle:active { cursor: grabbing; }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid rqa-page-shell">

            <div class="rqa-hero">
                <div class="rqa-hero-content">
                    <div class="rqa-title-block">
                        <h4><?= h($title ?? 'RQA Recommendation'); ?></h4>
                        <p>Pick a position to load qualified applicants ranked from highest to lowest RQA score. Use the filters, enter the item number, then recommend the selected applicant.</p>
                    </div>

                    <div class="d-flex align-items-center" style="gap:12px;">
                        <span id="rqa-count-badge"></span>
                        <div class="rqa-hero-icon">
                            <i class="mdi mdi-account-check-outline"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card rqa-card rqa-filter-card">
                <div class="card-body">
                    <div class="rqa-section-head">
                        <h5 class="rqa-section-title">
                            <i class="mdi mdi-filter-variant"></i>
                            Filters
                        </h5>
                        <span class="rqa-help-chip">
                            <i class="mdi mdi-information-outline"></i>
                            Pick a year, then a position to begin
                        </span>
                    </div>

                    <div class="form-row rqa-filter-grid">
                        <div class="form-group col-xl-2 col-lg-2 col-md-6">
                            <label for="year-filter">Year</label>
                            <select id="year-filter" class="form-control">
                                <?php foreach ($years as $yr) : ?>
                                    <option value="<?= (int) $yr; ?>" <?= $selectedYear === (int) $yr ? 'selected' : ''; ?>>
                                        S.Y. <?= (int) $yr; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group col-xl-3 col-lg-3 col-md-6">
                            <label for="job-filter">Position</label>
                            <select id="job-filter" class="form-control">
                                <option value="">Select a position…</option>
                            </select>
                        </div>

                        <div class="form-group col-xl-2 col-lg-2 col-md-6">
                            <label for="municipality-filter">Municipality</label>
                            <select id="municipality-filter" class="form-control" multiple="multiple" disabled></select>
                        </div>

                        <div class="form-group col-xl-2 col-lg-2 col-md-6">
                            <label for="brgy-filter">Barangay</label>
                            <select id="brgy-filter" class="form-control" multiple="multiple" disabled></select>
                        </div>

                        <div class="form-group col-xl-2 col-lg-2 col-md-6 rqa-kind-filter" id="jhs-group-filter-wrap" style="display:none;">
                            <label for="jhs-group-filter">Specialization</label>
                            <select id="jhs-group-filter" class="form-control" multiple="multiple"></select>
                        </div>

                        <div class="form-group col-xl-2 col-lg-2 col-md-6 rqa-kind-filter" id="strand-filter-wrap" style="display:none;">
                            <label for="strand-filter">Strand</label>
                            <select id="strand-filter" class="form-control" multiple="multiple"></select>
                        </div>

                        <div class="form-group col-xl-2 col-lg-2 col-md-6 rqa-kind-filter" id="major-filter-wrap" style="display:none;">
                            <label for="major-filter">Specialization</label>
                            <select id="major-filter" class="form-control" multiple="multiple"></select>
                        </div>

                    </div>
                </div>
            </div>

            <div class="card rqa-card rqa-results-card">
                <div class="card-body">
                    <div class="rqa-results-header">
                        <div class="rqa-results-title-wrap">
                            <div>
                                <h5 class="rqa-section-title" id="rqa-results-title">
                                    <i class="mdi mdi-format-list-numbered"></i>
                                    Ranked Results
                                </h5>
                                <!-- <p class="rqa-results-subtitle">Compact table view. Applicant name and code are merged to fit inside the page.</p> -->
                            </div>

                            <!-- <span class="rqa-table-note">
                                <i class="mdi mdi-table-large"></i>
                                Full-width compact table
                            </span> -->
                        </div>

                        <div class="rqa-legend">
                            <span class="rqa-legend-item"><span class="rqa-legend-swatch tie"></span> Tie Score</span>
                            <span class="rqa-legend-item"><span class="rqa-legend-swatch corr"></span> Corrigendum / Addendum</span>
                            <span class="rqa-legend-item rqa-legend-hint"><i class="mdi mdi-drag-vertical"></i> Drag tied rows to set their order</span>
                        </div>
                    </div>

                    <div id="rqa-loading" class="text-center text-muted rqa-loading-box" style="display:none;">
                        <div class="spinner-border text-primary" role="status"></div>
                        <div class="mt-3 font-weight-bold">Loading applicants…</div>
                        <small>Please wait while the ranked list is being prepared.</small>
                    </div>

                    <div id="rqa-empty" class="rqa-empty-state">
                        <i class="mdi mdi-briefcase-search-outline"></i>
                        <strong>Select a position to begin.</strong>
                        <span>The ranked applicant list will appear here after selecting a position.</span>
                    </div>

                    <div class="rqa-results-wrap" id="rqa-results" style="display:none;">
                        <table class="table table-hover table-bordered w-100" id="rqa-table">
                            <colgroup>
                                <col style="width:4%;">
                                <col style="width:13%;">
                                <col style="width:10%;">
                                <col class="rqa-col-spec" style="width:7%; display:none;">
                                <col style="width:4%;">
                                <col style="width:4%;">
                                <col style="width:4%;">
                                <col style="width:4%;">
                                <col style="width:4%;">
                                <col style="width:4%;">
                                <col style="width:5%;">
                                <col style="width:13%;">
                                <col style="width:8%;">
                                <col style="width:9%;">
                                <col style="width:7%;">
                            </colgroup>

                            <thead>
                                <tr>
                                    <th class="num">No.</th>
                                    <th>Applicant</th>
                                    <th>Municipality - Brgy</th>
                                    <th class="rqa-col-spec" style="display:none;">Spec.</th>
                                    <th class="num">Educ</th>
                                    <th class="num">Train</th>
                                    <th class="num">Exp</th>
                                    <th class="num">LET</th>
                                    <th class="num">Demo</th>
                                    <th class="num">TRF</th>
                                    <th class="num">Total</th>
                                    <th>School</th>
                                    <th>Item No.</th>
                                    <th>Remarks</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody></tbody>
                        </table>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var saveUrl = '<?= base_url('Pages/rqa_recommend_save'); ?>';
    var dataUrl = '<?= base_url('Pages/rqa_recommendation_data'); ?>';
    var metaUrl = '<?= base_url('Pages/rqa_ranking_meta_save'); ?>';
    var schoolSearchUrl = '<?= base_url('Pages/rqa_school_search'); ?>';
    var preselectJob = '<?= $selectedJobId > 0 ? $selectedJobId : ''; ?>';

    // Every position (Open and Closed) with its school year. The position
    // dropdown is rebuilt from this list for the selected year so the report
    // keeps working on vacancies that have already been closed.
    var allJobs = <?= json_encode(array_map(function ($j) use ($suffixes) {
        $suffix = $suffixes[(int) $j->job_type] ?? ('- Job Type ' . (int) $j->job_type);
        return [
            'id' => (int) $j->jobID,
            'label' => trim(($j->jobTitle ?? '') . ' ' . $suffix),
            'sy' => (int) $j->sy,
        ];
    }, $jobOptions ?? []), JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;

    var allRows = [];
    var specializationApplicable = false;
    var specializationKind = 'none';

    var $year = $('#year-filter');
    var $job = $('#job-filter');
    var $mun = $('#municipality-filter');
    var $brgy = $('#brgy-filter');
    var $jhsGroupWrap = $('#jhs-group-filter-wrap');
    var $strandWrap = $('#strand-filter-wrap');
    var $majorWrap = $('#major-filter-wrap');
    var $jhsGroup = $('#jhs-group-filter');
    var $strand = $('#strand-filter');
    var $major = $('#major-filter');

    function escHtml(s) {
        return String(s == null ? '' : s)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }

    function escAttr(s) {
        return escHtml(s).replace(/"/g, '&quot;');
    }

    $year.select2({
        width: '100%',
        minimumResultsForSearch: Infinity
    });

    $job.select2({
        width: '100%',
        placeholder: 'Select a position…',
        allowClear: true
    });

    // Rebuild the Position list for a school year (keeps the placeholder).
    // Vacancies that share the same position (same title/type) are merged into
    // one option; its value is the comma-separated list of their jobIDs.
    function buildJobOptions(year) {
        year = parseInt(year, 10) || 0;

        var groups = {};
        var order = [];

        allJobs
            .filter(function (j) { return j.sy === year; })
            .forEach(function (j) {
                if (!groups[j.label]) {
                    groups[j.label] = [];
                    order.push(j.label);
                }
                groups[j.label].push(j.id);
            });

        var html = '<option value="">Select a position…</option>';

        order.forEach(function (label) {
            html += '<option value="' + groups[label].join(',') + '">' + escHtml(label) + '</option>';
        });

        $job.html(html).val('').trigger('change.select2');
    }

    // Find the (possibly merged) Position option that contains a given jobID.
    function findJobGroupValue(jobId) {
        jobId = parseInt(jobId, 10);
        var match = '';

        $job.find('option').each(function () {
            var val = $(this).val();
            if (!val) return;

            var ids = val.split(',');
            for (var i = 0; i < ids.length; i++) {
                if (parseInt(ids[i], 10) === jobId) {
                    match = val;
                    return false;
                }
            }
        });

        return match;
    }

    $mun.select2({
        width: '100%',
        placeholder: 'All Municipalities'
    });

    $brgy.select2({
        width: '100%',
        placeholder: 'All Barangays'
    });

    $jhsGroup.select2({
        width: '100%',
        placeholder: 'All'
    });

    $strand.select2({
        width: '100%',
        placeholder: 'All Strands'
    });

    $major.select2({
        width: '100%',
        placeholder: 'All Specializations'
    });

    // Per-row School picker (Select2 with server-side search; not restricted by municipality/barangay)
    function initSchoolSelects() {
        $('#rqa-table tbody .rqa-school').each(function () {
            if ($(this).hasClass('select2-hidden-accessible')) return;
            $(this).select2({
                width: '100%',
                placeholder: 'Search school…',
                allowClear: true,
                ajax: {
                    url: schoolSearchUrl,
                    dataType: 'json',
                    delay: 200,
                    data: function (params) { return { q: params.term || '' }; },
                    processResults: function (data) { return { results: (data && data.results) ? data.results : [] }; },
                    cache: true
                }
            });
        });
    }

    function setSelectOptions($el, values) {
        var html = '';

        values.forEach(function (v) {
            html += '<option value="' + escAttr(v) + '">' + escHtml(v) + '</option>';
        });

        $el.html(html).val([]).trigger('change.select2');
    }

    function distinct(arr) {
        var seen = {};
        var out = [];

        arr.forEach(function (v) {
            v = (v || '').trim();

            if (v !== '' && !seen[v.toLowerCase()]) {
                seen[v.toLowerCase()] = true;
                out.push(v);
            }
        });

        out.sort(function (a, b) {
            return a.localeCompare(b);
        });

        return out;
    }

    function eqi(a, b) {
        return (a || '').trim().toLowerCase() === (b || '').trim().toLowerCase();
    }

    // Case-insensitive membership test for the multi-select filter values.
    function inListI(list, val) {
        val = (val || '').trim().toLowerCase();

        for (var i = 0; i < list.length; i++) {
            if ((list[i] || '').trim().toLowerCase() === val) return true;
        }

        return false;
    }

    function locationText(r) {
        var municipality = (r.municipality || '').trim();
        var brgy = (r.brgy || '').trim();

        if (municipality && brgy) return municipality + ' - ' + brgy;
        return municipality || brgy || '';
    }

    function specializationText(r) {
        if (specializationKind === 'jhs') return (r.specializationGroup || r.specialization || '').trim();
        if (specializationKind === 'shs') {
            var strand = (r.strand || '').trim();
            var major = (r.major || '').trim();
            if (strand && major) return strand + ' / ' + major;
            return strand || major || '';
        }
        return (r.specialization || '').trim();
    }

    function rowHtml(r, index, tied) {
        var isCorr = parseInt(r.isCorrigendum, 10) === 1;
        var trClass = isCorr ? ' rqa-corr' : (tied ? ' rqa-tie' : '');

        var html = '<tr class="rqa-row' + trClass + '" data-app-id="' + r.appID + '"'
            + ' data-job-id="' + r.jobID + '"'
            + ' data-tie-key="' + escAttr(tieKey(r)) + '"'
            + (tied ? ' data-tied="1"' : '') + '>';

        html += '<td class="num">';
        if (tied) {
            html += '<span class="rqa-drag-handle" draggable="true" title="Drag to reorder this tie"><i class="mdi mdi-drag-vertical"></i></span>';
        }
        html += '<span class="rqa-rank-badge">' + index + '</span></td>';

        html += '<td class="rqa-name-cell">';
        html += '<span class="rqa-name-main">' + escHtml(r.name) + '</span>';
        html += '<span class="rqa-name-sub">Code: <span class="rqa-code-inline">' + escHtml(r.code) + '</span></span>';
        html += '</td>';

        html += '<td><span class="rqa-location-tag">' + escHtml(locationText(r)) + '</span></td>';

        if (specializationApplicable) {
            html += '<td class="rqa-col-spec">' + escHtml(specializationText(r)) + '</td>';
        }

        html += '<td class="num score-cell">' + escHtml(r.education) + '</td>';
        html += '<td class="num score-cell">' + escHtml(r.training) + '</td>';
        html += '<td class="num score-cell">' + escHtml(r.experience) + '</td>';
        html += '<td class="num score-cell">' + escHtml(r.let_rating) + '</td>';
        html += '<td class="num score-cell">' + escHtml(r.demo_rating) + '</td>';
        html += '<td class="num score-cell">' + escHtml(r.tr_rating) + '</td>';

        html += '<td class="total"><span class="rqa-total-pill">' + escHtml(r.total_points) + '</span></td>';

        html += '<td><select class="form-control form-control-sm rqa-school"></select></td>';
        html += '<td><input type="text" class="form-control form-control-sm rqa-item-number" placeholder="Item"></td>';
        html += '<td><input type="text" class="form-control form-control-sm rqa-remarks" placeholder="Remarks"></td>';

        html += '<td>';
        html += '<button type="button" class="btn btn-sm btn-success rqa-recommend-btn"'
            + ' data-jobid="' + r.jobID + '"'
            + ' data-appid="' + r.appID + '"'
            + ' data-email="' + escAttr(r.empEmail) + '"'
            + ' data-record="' + escAttr(r.code) + '"'
            + ' data-name="' + escAttr(r.name) + '"'
            + ' data-total="' + escAttr(r.total_points) + '">';
        html += '<i class="mdi mdi-check-circle-outline mr-1"></i>Recommend';
        html += '</button>';
        html += '</td>';

        html += '</tr>';

        return html;
    }

    // Numeric RQA total for a row (rows are pre-filtered to total >= 50).
    function rowScoreNum(r) {
        var n = parseFloat(r.total_points);
        return isNaN(n) ? -Infinity : n;
    }

    // Case-insensitive A-Z compare; blanks are pushed to the end.
    function cmpText(a, b) {
        a = (a || '').trim();
        b = (b || '').trim();
        if (a === b) return 0;
        if (a === '') return 1;
        if (b === '') return -1;
        return a.localeCompare(b, undefined, { sensitivity: 'base' });
    }

    // The specialization "bucket" a row sorts into. Rows that share the same
    // bucket AND the same score are a tie that needs manual ordering.
    function specSortKey(r) {
        if (specializationKind === 'jhs') return (r.specializationGroup || r.specialization || '').trim().toLowerCase();
        if (specializationKind === 'shs') return (r.strand || '').trim().toLowerCase() + ' / ' + (r.major || '').trim().toLowerCase();
        return '';
    }

    // Identity of a tie group: same specialization bucket + same score.
    function tieKey(r) {
        return specSortKey(r) + '||' + String(r.total_points || '');
    }

    // Manual tie-break order (lower = higher up). Unset rows sort after ordered
    // ones, then a stable fallback by appID keeps the order deterministic.
    function rowTieOrder(r) {
        var n = parseInt(r.tieOrder, 10);
        return isNaN(n) ? 1e9 : n;
    }

    // Order the rows so applicants are grouped by specialization, then ranked
    // by score (highest first) inside each group. JHS groups by Specialization
    // (A-Z); SHS groups by Strand then Specialization (A-Z). Positions without
    // a specialization keep the plain highest-to-lowest score order. Tied
    // applicants (same bucket + same score) follow the saved manual order.
    function sortRows(rows) {
        rows.sort(function (a, b) {
            var c;
            if (specializationKind === 'jhs') {
                c = cmpText(a.specializationGroup || a.specialization, b.specializationGroup || b.specialization);
                if (c !== 0) return c;
            } else if (specializationKind === 'shs') {
                c = cmpText(a.strand, b.strand);
                if (c !== 0) return c;
                c = cmpText(a.major, b.major);
                if (c !== 0) return c;
            }
            c = rowScoreNum(b) - rowScoreNum(a);
            if (c !== 0) return c;
            c = rowTieOrder(a) - rowTieOrder(b);
            if (c !== 0) return c;
            return (a.appID || 0) - (b.appID || 0);
        });
        return rows;
    }

    function getFilteredRows() {
        var muns = $mun.val() || [];
        var brgys = $brgy.val() || [];
        var jhsGroups = specializationKind === 'jhs' ? ($jhsGroup.val() || []) : [];
        var strands = specializationKind === 'shs' ? ($strand.val() || []) : [];
        var majors = specializationKind === 'shs' ? ($major.val() || []) : [];

        return allRows.filter(function (r) {
            var total = parseFloat(r.total_points);
            if (isNaN(total) || total < 50) return false;
            if (muns.length && !inListI(muns, r.municipality)) return false;
            if (brgys.length && !inListI(brgys, r.brgy)) return false;
            if (jhsGroups.length && !inListI(jhsGroups, r.specializationGroup || r.specialization)) return false;
            if (strands.length && !inListI(strands, r.strand)) return false;
            if (majors.length && !inListI(majors, r.major)) return false;

            return true;
        });
    }

    function setEmptyState(icon, title, text) {
        $('#rqa-empty')
            .html(
                '<i class="mdi ' + icon + '"></i>' +
                '<strong>' + escHtml(title) + '</strong>' +
                '<span>' + escHtml(text) + '</span>'
            )
            .show();
    }

    function renderTable() {
        var rows = sortRows(getFilteredRows());
        var $tbody = $('#rqa-table tbody');

        $('.rqa-col-spec').toggle(specializationApplicable);
        $('#rqa-table thead th.rqa-col-spec').text(specializationKind === 'shs' ? 'Strand / Spec.' : 'Spec. Group');

        if (rows.length === 0) {
            $('#rqa-results').hide();

            if (allRows.length === 0) {
                setEmptyState(
                    'mdi-account-off-outline',
                    'No qualified applicants found.',
                    'There are no qualified applicants available for the selected position.'
                );
            } else {
                setEmptyState(
                    'mdi-filter-remove-outline',
                    'No applicants match the selected filters.',
                    'Try changing the municipality, barangay, or specialization filter.'
                );
            }
        } else {
            // A row is "tied" when another visible row shares its tie key
            // (same specialization bucket + same score) within this filter.
            var tieCount = {};
            rows.forEach(function (r) {
                var k = tieKey(r);
                tieCount[k] = (tieCount[k] || 0) + 1;
            });

            var html = '';

            rows.forEach(function (r, i) {
                html += rowHtml(r, i + 1, tieCount[tieKey(r)] > 1);
            });

            $tbody.html(html);
            initSchoolSelects();
            $('#rqa-empty').hide();
            $('#rqa-results').show();
        }

        var badge = $('#rqa-count-badge');

        if (allRows.length > 0) {
            badge
                .text(rows.length + ' of ' + allRows.length + ' applicant' + (allRows.length === 1 ? '' : 's'))
                .show();
        } else {
            badge.hide();
        }
    }

    function rebuildSpecializations() {
        $('.rqa-kind-filter').hide();
        setSelectOptions($jhsGroup, []);
        setSelectOptions($strand, []);
        setSelectOptions($major, []);

        if (specializationKind === 'jhs') {
            var groups = distinct(allRows.map(function (r) {
                return r.specializationGroup || r.specialization;
            }));
            setSelectOptions($jhsGroup, groups);
            $jhsGroupWrap.show();
        } else if (specializationKind === 'shs') {
            var strands = distinct(allRows.map(function (r) {
                return r.strand;
            }));
            var majors = distinct(allRows.map(function (r) {
                return r.major;
            }));
            setSelectOptions($strand, strands);
            setSelectOptions($major, majors);
            $strandWrap.show();
            $majorWrap.show();
        }
    }

    function rebuildMunicipalities() {
        var muns = distinct(allRows.map(function (r) {
            return r.municipality;
        }));

        setSelectOptions($mun, muns);
        $mun.prop('disabled', muns.length === 0).trigger('change.select2');

        rebuildBarangays();
    }

    function rebuildBarangays() {
        var selectedMuns = $mun.val() || [];

        if (selectedMuns.length === 0) {
            setSelectOptions($brgy, []);
            $brgy.prop('disabled', true).trigger('change.select2');
            return;
        }

        var brgys = distinct(
            allRows
                .filter(function (r) {
                    return inListI(selectedMuns, r.municipality);
                })
                .map(function (r) {
                    return r.brgy;
                })
        );

        setSelectOptions($brgy, brgys);
        $brgy.prop('disabled', brgys.length === 0).trigger('change.select2');
    }

    function resetFilters() {
        allRows = [];
        specializationKind = 'none';
        specializationApplicable = false;

        $('.rqa-kind-filter').hide();
        $mun.prop('disabled', true);
        setSelectOptions($mun, []);
        $brgy.prop('disabled', true);
        setSelectOptions($brgy, []);
        setSelectOptions($jhsGroup, []);
        setSelectOptions($strand, []);
        setSelectOptions($major, []);
    }

    function loadPosition(jobID) {
        resetFilters();

        $('#rqa-results').hide();
        $('#rqa-count-badge').hide();

        if (!jobID) {
            $('#rqa-loading').hide();
            setEmptyState(
                'mdi-briefcase-search-outline',
                'Select a position to begin.',
                'The ranked applicant list will appear here after selecting a position.'
            );
            return;
        }

        $('#rqa-empty').hide();
        $('#rqa-loading').show();

        $.getJSON(dataUrl, { job: jobID }).done(function (res) {
            $('#rqa-loading').hide();

            if (!res || res.status !== 'success') {
                setEmptyState(
                    'mdi-alert-circle-outline',
                    'Unable to load applicants.',
                    (res && res.message) ? res.message : 'Please try again.'
                );
                return;
            }

            allRows = res.rows || [];
            specializationApplicable = !!res.specializationApplicable;
            specializationKind = res.specializationKind || 'none';

            rebuildMunicipalities();
            rebuildSpecializations();
            renderTable();

        }).fail(function () {
            $('#rqa-loading').hide();

            setEmptyState(
                'mdi-wifi-off',
                'Unable to load applicants.',
                'Please check your connection and try again.'
            );
        });
    }

    $year.on('change', function () {
        buildJobOptions($(this).val());
        // Changing year clears the current position/results.
        loadPosition('');
    });

    $job.on('change', function () {
        loadPosition($(this).val());
    });

    $mun.on('change', function () {
        rebuildBarangays();
        renderTable();
    });

    $brgy.on('change', function () {
        renderTable();
    });

    $jhsGroup.on('change', function () {
        renderTable();
    });

    $strand.on('change', function () {
        renderTable();
    });

    $major.on('change', function () {
        renderTable();
    });

    // ----- Drag-to-reorder tied applicants -------------------------------
    // Tied rows (same specialization bucket + same score) carry a drag handle.
    // Dragging one above/below another tied row physically moves it, renumbers
    // the list, and persists the new order so every user sees it.
    var dragAppId = null;
    var $tbody = $('#rqa-table tbody');

    function clearDragState() {
        $('#rqa-table tbody tr').removeClass('rqa-dragging rqa-drag-over');
        dragAppId = null;
    }

    function renumberRows() {
        $('#rqa-table tbody tr').each(function (i) {
            $(this).find('.rqa-rank-badge').text(i + 1);
        });
    }

    // Read the current DOM order of a tie group, update allRows, and save it.
    function persistTieOrder(keyVal) {
        var payload = [];

        $('#rqa-table tbody tr').each(function () {
            if (String($(this).attr('data-tie-key')) === String(keyVal)) {
                payload.push({
                    appID: parseInt($(this).attr('data-app-id'), 10),
                    jobID: parseInt($(this).attr('data-job-id'), 10)
                });
            }
        });

        payload.forEach(function (p, idx) {
            var row = allRows.filter(function (r) { return r.appID === p.appID; })[0];
            if (row) row.tieOrder = idx;
        });

        $.post(metaUrl, { action: 'order', order: JSON.stringify(payload) }, null, 'json')
            .done(function (res) {
                if (!res || res.status !== 'success') {
                    Swal.fire({ icon: 'error', title: 'Could not save order', text: (res && res.message) ? res.message : 'Please try again.' });
                }
            })
            .fail(function () {
                Swal.fire({ icon: 'error', title: 'Could not save order', text: 'Please check your connection and try again.' });
            });
    }

    $tbody.on('dragstart', '.rqa-drag-handle', function (e) {
        var $row = $(this).closest('tr');
        dragAppId = parseInt($row.attr('data-app-id'), 10);
        $row.addClass('rqa-dragging');

        var dt = e.originalEvent.dataTransfer;
        if (dt) {
            dt.effectAllowed = 'move';
            try { dt.setData('text/plain', String(dragAppId)); } catch (err) {}
        }
    });

    $tbody.on('dragend', '.rqa-drag-handle', function () {
        clearDragState();
    });

    $tbody.on('dragover', 'tr', function (e) {
        if (dragAppId == null) return;

        var $target = $(this);
        var $drag = $('#rqa-table tbody tr[data-app-id="' + dragAppId + '"]');
        if (!$drag.length || String($target.attr('data-tie-key')) !== String($drag.attr('data-tie-key'))) return;

        e.preventDefault();
        if (e.originalEvent.dataTransfer) e.originalEvent.dataTransfer.dropEffect = 'move';

        $('#rqa-table tbody tr').removeClass('rqa-drag-over');
        if ($target.attr('data-app-id') != dragAppId) $target.addClass('rqa-drag-over');
    });

    $tbody.on('drop', 'tr', function (e) {
        if (dragAppId == null) return;

        var $target = $(this);
        var $drag = $('#rqa-table tbody tr[data-app-id="' + dragAppId + '"]');
        var keyVal = $drag.length ? String($drag.attr('data-tie-key')) : null;

        if (!$drag.length || keyVal === null || String($target.attr('data-tie-key')) !== keyVal) {
            clearDragState();
            return;
        }

        e.preventDefault();

        if ($target.attr('data-app-id') != dragAppId) {
            var rect = this.getBoundingClientRect();
            var after = (e.originalEvent.clientY - rect.top) > rect.height / 2;

            if (after) $drag.insertAfter($target);
            else $drag.insertBefore($target);

            renumberRows();
            persistTieOrder(keyVal);
        }

        clearDragState();
    });

    $(document).on('click', '.rqa-recommend-btn', function () {
        var $btn = $(this);
        var $row = $btn.closest('tr');
        var appID = parseInt($btn.data('appid'), 10);
        var itemNumber = $.trim($row.find('.rqa-item-number').val());
        var remarks = $.trim($row.find('.rqa-remarks').val());

        var $school = $row.find('.rqa-school');
        var schoolId = $school.val() || '';
        var schoolData = $school.select2('data');
        var schoolName = (schoolData && schoolData[0]) ? (schoolData[0].name || schoolData[0].text || '') : '';

        if (schoolId === '') {
            Swal.fire({
                icon: 'warning',
                title: 'School required',
                text: 'Please select the School where the applicant will be assigned.'
            });
            $school.select2('open');
            return;
        }

        if (itemNumber === '') {
            Swal.fire({
                icon: 'warning',
                title: 'Item Number required',
                text: 'Please enter an Item Number before recommending.'
            });

            $row.find('.rqa-item-number').focus();
            return;
        }

        Swal.fire({
            title: 'Recommend this applicant?',
            html: 'Applicant: <strong>' + escHtml($btn.data('name')) + '</strong><br>School: <strong>' + escHtml(schoolName) + '</strong><br>Item Number: <strong>' + escHtml(itemNumber) + '</strong>',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, recommend',
            confirmButtonColor: '#1abc9c'
        }).then(function (result) {
            if (!result.value) return;

            $btn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin mr-1"></i>Saving…');

            $.post(saveUrl, {
                jobID: $btn.data('jobid'),
                appID: appID,
                empEmail: $btn.data('email'),
                record_no: $btn.data('record'),
                applicant_name: $btn.data('name'),
                total_points: $btn.data('total'),
                item_number: itemNumber,
                remarks: remarks,
                school_id: schoolId,
                school_name: schoolName
            }, null, 'json').done(function (res) {
                if (res && res.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Recommended',
                        text: res.message,
                        timer: 1400,
                        showConfirmButton: false
                    });

                    allRows = allRows.filter(function (r) {
                        return r.appID !== appID;
                    });

                    $row.fadeOut(250, function () {
                        renderTable();
                    });

                } else if (res && res.status === 'duplicate') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Duplicate Item Number',
                        text: res.message
                    });

                    $btn.prop('disabled', false).html('<i class="mdi mdi-check-circle-outline mr-1"></i>Recommend');
                    $row.find('.rqa-item-number').focus();

                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: (res && res.message) ? res.message : 'Something went wrong.'
                    });

                    $btn.prop('disabled', false).html('<i class="mdi mdi-check-circle-outline mr-1"></i>Recommend');
                }
            }).fail(function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Unable to save. Please try again.'
                });

                $btn.prop('disabled', false).html('<i class="mdi mdi-check-circle-outline mr-1"></i>Recommend');
            });
        });
    });

    // If a position was preselected, switch to its school year first so it
    // shows up in the (year-scoped) Position list.
    if (preselectJob) {
        var preJob = allJobs.filter(function (j) { return j.id === parseInt(preselectJob, 10); })[0];
        if (preJob && $year.val() != preJob.sy) {
            $year.val(String(preJob.sy)).trigger('change.select2');
        }
    }

    buildJobOptions($year.val());

    if (preselectJob) {
        var groupVal = findJobGroupValue(preselectJob);
        if (groupVal) {
            $job.val(groupVal).trigger('change.select2');
            loadPosition(groupVal);
        }
    }
});
</script>
