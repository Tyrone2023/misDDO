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
        --jhs-primary: #1f3a5f;
        --jhs-accent: #1abc9c;
        --jhs-bg: #f4f7fb;
        --jhs-border: #e5ecf5;
        --jhs-text: #25364a;
        --jhs-muted: #7b8794;
        --jhs-soft: #eef5ff;
        --jhs-warn-soft: #fff8e8;
        --jhs-warn: #a56a00;
    }

    .content-page {
        background: var(--jhs-bg);
        min-height: 100vh;
    }

    .jhs-page-shell {
        padding-bottom: 24px;
    }

    .jhs-hero {
        border-radius: 14px;
        padding: 22px 24px;
        margin-bottom: 18px;
        background: #fff;
        border: 1px solid var(--jhs-border);
        box-shadow: 0 8px 26px rgba(31, 58, 95, .08);
    }

    .jhs-hero-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
    }

    .jhs-title-block h4 {
        color: var(--jhs-text);
        font-size: 1.25rem;
        font-weight: 800;
        margin: 0 0 5px;
    }

    .jhs-title-block p {
        color: var(--jhs-muted);
        margin: 0;
        max-width: 760px;
        font-size: .86rem;
        line-height: 1.45;
    }

    .jhs-hero-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--jhs-soft);
        color: var(--jhs-primary);
        flex: 0 0 auto;
    }

    .jhs-hero-icon i {
        font-size: 28px;
    }

    #jhs-count-badge {
        display: none;
        border-radius: 999px;
        padding: .48rem .72rem;
        font-size: .78rem;
        font-weight: 800;
        background: var(--jhs-soft);
        border: 1px solid #dce8f7;
        color: var(--jhs-primary);
        white-space: nowrap;
    }

    .jhs-card {
        border: 0;
        border-radius: 14px;
        box-shadow: 0 8px 26px rgba(31, 58, 95, .08);
        overflow: hidden;
    }

    .jhs-card .card-body {
        padding: 18px;
    }

    .jhs-section-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 14px;
    }

    .jhs-section-title {
        margin: 0;
        font-size: .94rem;
        font-weight: 800;
        color: var(--jhs-text);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .jhs-section-title i {
        width: 32px;
        height: 32px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: var(--jhs-soft);
        color: var(--jhs-primary);
        font-size: 17px;
    }

    .jhs-filter-card {
        margin-bottom: 18px;
    }

    .jhs-filter-card label {
        font-weight: 800;
        font-size: .68rem;
        text-transform: uppercase;
        letter-spacing: .55px;
        color: var(--jhs-muted);
        margin-bottom: 6px;
    }

    .jhs-filter-card .form-group {
        margin-bottom: 0;
    }

    .jhs-filter-grid {
        row-gap: 14px;
    }

    .jhs-help-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: var(--jhs-muted);
        font-size: .76rem;
        background: #f8fafd;
        border: 1px solid var(--jhs-border);
        padding: 6px 10px;
        border-radius: 999px;
    }

    .jhs-results-card .card-body {
        padding: 0;
    }

    .jhs-results-header {
        padding: 16px 18px;
        border-bottom: 1px solid var(--jhs-border);
        background: #fff;
    }

    .jhs-results-wrap {
        background: #fff;
        width: 100%;
        overflow-x: auto;
    }

    #jhs-table {
        width: 100%;
        margin-bottom: 0;
        table-layout: fixed;
        border-collapse: collapse;
    }

    #jhs-table thead th {
        background: #f8fbff;
        border-top: 0;
        border-bottom: 1px solid var(--jhs-border);
        color: #516070;
        font-size: .62rem;
        text-transform: uppercase;
        letter-spacing: .35px;
        white-space: normal;
        padding: 9px 6px;
        font-weight: 800;
        line-height: 1.2;
    }

    #jhs-table td {
        vertical-align: middle;
        font-size: .72rem;
        color: var(--jhs-text);
        padding: 8px 6px;
        border-color: #eef2f7;
        background: #fff;
        line-height: 1.25;
        word-break: normal;
    }

    #jhs-table tbody tr:hover td {
        background: #fbfdff;
    }

    #jhs-table td.num,
    #jhs-table th.num {
        text-align: center;
    }

    .jhs-rank-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 24px;
        height: 24px;
        padding: 0 .35rem;
        border-radius: 999px;
        background: var(--jhs-soft);
        color: var(--jhs-primary);
        font-weight: 900;
        font-size: .65rem;
        border: 1px solid #dce8f7;
    }

    .jhs-name-main {
        display: block;
        font-weight: 800;
        color: #223047;
        font-size: .74rem;
        line-height: 1.2;
        overflow-wrap: break-word;
    }

    .jhs-name-sub {
        display: flex;
        align-items: center;
        gap: 4px;
        margin-top: 4px;
        font-size: .65rem;
        color: var(--jhs-muted);
        flex-wrap: wrap;
        line-height: 1.15;
    }

    .jhs-code-inline {
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

    .jhs-location-tag {
        display: block;
        font-weight: 700;
        color: #34465c;
        font-size: .68rem;
        line-height: 1.25;
        overflow-wrap: break-word;
    }

    .jhs-status {
        display: inline-flex;
        align-items: center;
        max-width: 100%;
        border-radius: 999px;
        background: #f2f6fb;
        color: #46566a;
        border: 1px solid #e5ecf5;
        font-weight: 800;
        font-size: .62rem;
        padding: .16rem .42rem;
        white-space: normal;
        line-height: 1.15;
    }

    .jhs-missing-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        background: var(--jhs-warn-soft);
        color: var(--jhs-warn);
        border: 1px solid #f4dfad;
        font-weight: 900;
        padding: .2rem .45rem;
        font-size: .64rem;
        white-space: nowrap;
    }

    .jhs-total-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 42px;
        border-radius: 999px;
        background: #e8fff8;
        color: #129777;
        border: 1px solid #c9f5eb;
        font-weight: 900;
        padding: .22rem .35rem;
        font-size: .68rem;
    }

    .jhs-edit-btn {
        width: 100%;
        border-radius: 999px;
        padding: .34rem .35rem;
        font-weight: 800;
        font-size: .62rem;
        white-space: normal;
        line-height: 1.1;
    }

    .jhs-empty-state {
        margin: 18px;
        border: 1px dashed #cbd8e7;
        background: #f8fbff;
        color: #526173;
        border-radius: 14px;
        padding: 30px 18px;
        text-align: center;
    }

    .jhs-empty-state i {
        font-size: 36px;
        color: var(--jhs-primary);
        opacity: .78;
        display: block;
        margin-bottom: 8px;
    }

    .jhs-empty-state strong {
        display: block;
        color: var(--jhs-text);
        font-size: .94rem;
        margin-bottom: 4px;
    }

    .jhs-empty-state span {
        font-size: .8rem;
    }

    .jhs-loading-box {
        margin: 18px;
        border-radius: 14px;
        background: #fff;
        border: 1px solid var(--jhs-border);
        padding: 36px 18px;
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

    .select2-container--default .select2-selection--multiple {
        min-height: 38px;
        border-radius: 10px;
        border-color: #dfe7f1;
        padding: 1px 4px;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background: var(--jhs-soft);
        border: 1px solid #dce8f7;
        color: var(--jhs-primary);
        border-radius: 6px;
        font-size: .76rem;
        font-weight: 700;
        margin-top: 5px;
        padding: 2px 8px;
    }

    @media (max-width: 991.98px) {
        .jhs-hero-content,
        .jhs-section-head {
            align-items: flex-start;
            flex-direction: column;
        }

        .jhs-hero-icon {
            display: none;
        }

        #jhs-table {
            min-width: 980px;
        }
    }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid jhs-page-shell">

            <div class="jhs-hero">
                <div class="jhs-hero-content">
                    <div class="jhs-title-block">
                        <h4><?= h($title ?? 'JHS Applicants Missing Learning Area'); ?></h4>
                        <p>Pick a Junior High School position to find applicants whose profile has no Learning Area value. Open the profile to update the Education section.</p>
                    </div>

                    <div class="d-flex align-items-center" style="gap:12px;">
                        <span id="jhs-count-badge"></span>
                        <div class="jhs-hero-icon">
                            <i class="mdi mdi-book-alert-outline"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card jhs-card jhs-filter-card">
                <div class="card-body">
                    <div class="jhs-section-head">
                        <h5 class="jhs-section-title">
                            <i class="mdi mdi-filter-variant"></i>
                            Filters
                        </h5>
                        <span class="jhs-help-chip">
                            <i class="mdi mdi-information-outline"></i>
                            Only Junior High School positions are listed
                        </span>
                    </div>

                    <div class="form-row jhs-filter-grid">
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

                        <div class="form-group col-xl-4 col-lg-4 col-md-6">
                            <label for="job-filter">Position</label>
                            <select id="job-filter" class="form-control">
                                <option value="">Select a position...</option>
                            </select>
                        </div>

                        <div class="form-group col-xl-3 col-lg-3 col-md-6">
                            <label for="municipality-filter">Municipality</label>
                            <select id="municipality-filter" class="form-control" multiple="multiple" disabled></select>
                        </div>

                        <div class="form-group col-xl-3 col-lg-3 col-md-6">
                            <label for="brgy-filter">Barangay</label>
                            <select id="brgy-filter" class="form-control" multiple="multiple" disabled></select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card jhs-card jhs-results-card">
                <div class="card-body">
                    <div class="jhs-results-header">
                        <h5 class="jhs-section-title" id="jhs-results-title">
                            <i class="mdi mdi-account-search-outline"></i>
                            Applicants Missing JHS Learning Area
                        </h5>
                    </div>

                    <div id="jhs-loading" class="text-center text-muted jhs-loading-box" style="display:none;">
                        <div class="spinner-border text-primary" role="status"></div>
                        <div class="mt-3 font-weight-bold">Loading applicants...</div>
                        <small>Please wait while the list is being prepared.</small>
                    </div>

                    <div id="jhs-empty" class="jhs-empty-state">
                        <i class="mdi mdi-briefcase-search-outline"></i>
                        <strong>Select a position to begin.</strong>
                        <span>Applicants with no Junior High School Learning Area will appear here.</span>
                    </div>

                    <div class="jhs-results-wrap" id="jhs-results" style="display:none;">
                        <table class="table table-hover table-bordered w-100" id="jhs-table">
                            <colgroup>
                                <col style="width:4%;">
                                <col style="width:16%;">
                                <col style="width:12%;">
                                <col style="width:10%;">
                                <col style="width:9%;">
                                <col style="width:8%;">
                                <col style="width:5%;">
                                <col style="width:5%;">
                                <col style="width:5%;">
                                <col style="width:5%;">
                                <col style="width:5%;">
                                <col style="width:5%;">
                                <col style="width:5%;">
                                <col style="width:6%;">
                            </colgroup>

                            <thead>
                                <tr>
                                    <th class="num">No.</th>
                                    <th>Applicant</th>
                                    <th>Municipality - Brgy</th>
                                    <th>Contact</th>
                                    <th>Status</th>
                                    <th>Learning Area</th>
                                    <th class="num">Educ</th>
                                    <th class="num">Train</th>
                                    <th class="num">Exp</th>
                                    <th class="num">LET</th>
                                    <th class="num">Demo</th>
                                    <th class="num">TRF</th>
                                    <th class="num">Total</th>
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
    var dataUrl = '<?= base_url('Pages/rqa_missing_jhs_learning_area_data'); ?>';
    var preselectJob = '<?= $selectedJobId > 0 ? $selectedJobId : ''; ?>';

    var allJobs = <?= json_encode(array_map(function ($j) use ($suffixes) {
        $suffix = $suffixes[(int) $j->job_type] ?? ('- Job Type ' . (int) $j->job_type);
        return [
            'id' => (int) $j->jobID,
            'label' => trim(($j->jobTitle ?? '') . ' ' . $suffix),
            'sy' => (int) $j->sy,
        ];
    }, $jobOptions ?? []), JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;

    var allRows = [];

    var $year = $('#year-filter');
    var $job = $('#job-filter');
    var $mun = $('#municipality-filter');
    var $brgy = $('#brgy-filter');

    function escHtml(s) {
        return String(s == null ? '' : s)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }

    function escAttr(s) {
        return escHtml(s).replace(/"/g, '&quot;');
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

    function inListI(list, val) {
        val = (val || '').trim().toLowerCase();

        for (var i = 0; i < list.length; i++) {
            if ((list[i] || '').trim().toLowerCase() === val) return true;
        }

        return false;
    }

    function setSelectOptions($el, values) {
        var html = '';

        values.forEach(function (v) {
            html += '<option value="' + escAttr(v) + '">' + escHtml(v) + '</option>';
        });

        $el.html(html).val([]).trigger('change.select2');
    }

    function locationText(r) {
        var municipality = (r.municipality || '').trim();
        var brgy = (r.brgy || '').trim();

        if (municipality && brgy) return municipality + ' - ' + brgy;
        return municipality || brgy || '';
    }

    function statusText(r) {
        var parts = [];
        if (r.applicationStatus) parts.push(r.applicationStatus);
        parts.push(parseInt(r.dq, 10) === 1 ? 'Qualified' : 'Not qualified');
        return parts.join(' / ');
    }

    function rowHtml(r, index) {
        var profileUrl = r.profileUrl || '';
        var action = profileUrl
            ? '<a class="btn btn-sm btn-primary jhs-edit-btn" target="_blank" href="' + escAttr(profileUrl) + '"><i class="mdi mdi-open-in-new mr-1"></i>Edit</a>'
            : '<button type="button" class="btn btn-sm btn-secondary jhs-edit-btn" disabled>No link</button>';

        var html = '<tr data-app-id="' + r.appID + '">';
        html += '<td class="num"><span class="jhs-rank-badge">' + index + '</span></td>';
        html += '<td>';
        html += '<span class="jhs-name-main">' + escHtml(r.name) + '</span>';
        html += '<span class="jhs-name-sub">Code: <span class="jhs-code-inline">' + escHtml(r.code) + '</span></span>';
        html += '</td>';
        html += '<td><span class="jhs-location-tag">' + escHtml(locationText(r)) + '</span></td>';
        html += '<td>' + escHtml(r.contact) + '</td>';
        html += '<td><span class="jhs-status">' + escHtml(statusText(r)) + '</span></td>';
        html += '<td><span class="jhs-missing-pill">No data</span></td>';
        html += '<td class="num">' + escHtml(r.education) + '</td>';
        html += '<td class="num">' + escHtml(r.training) + '</td>';
        html += '<td class="num">' + escHtml(r.experience) + '</td>';
        html += '<td class="num">' + escHtml(r.let_rating) + '</td>';
        html += '<td class="num">' + escHtml(r.demo_rating) + '</td>';
        html += '<td class="num">' + escHtml(r.tr_rating) + '</td>';
        html += '<td class="num"><span class="jhs-total-pill">' + escHtml(r.total_points) + '</span></td>';
        html += '<td>' + action + '</td>';
        html += '</tr>';

        return html;
    }

    function getFilteredRows() {
        var muns = $mun.val() || [];
        var brgys = $brgy.val() || [];

        return allRows.filter(function (r) {
            if (muns.length && !inListI(muns, r.municipality)) return false;
            if (brgys.length && !inListI(brgys, r.brgy)) return false;
            return true;
        });
    }

    function setEmptyState(icon, title, text) {
        $('#jhs-empty')
            .html(
                '<i class="mdi ' + icon + '"></i>' +
                '<strong>' + escHtml(title) + '</strong>' +
                '<span>' + escHtml(text) + '</span>'
            )
            .show();
    }

    function renderTable() {
        var rows = getFilteredRows();
        var $tbody = $('#jhs-table tbody');

        if (rows.length === 0) {
            $('#jhs-results').hide();

            if (allRows.length === 0) {
                setEmptyState(
                    'mdi-check-circle-outline',
                    'No missing Learning Area records found.',
                    'All applicants for this position already have Junior High School Learning Area data.'
                );
            } else {
                setEmptyState(
                    'mdi-filter-remove-outline',
                    'No applicants match the selected filters.',
                    'Try changing the municipality or barangay filter.'
                );
            }
        } else {
            var html = '';
            rows.forEach(function (r, i) {
                html += rowHtml(r, i + 1);
            });

            $tbody.html(html);
            $('#jhs-empty').hide();
            $('#jhs-results').show();
        }

        var badge = $('#jhs-count-badge');
        if (allRows.length > 0) {
            badge
                .text(rows.length + ' of ' + allRows.length + ' applicant' + (allRows.length === 1 ? '' : 's'))
                .show();
        } else {
            badge.hide();
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

        $mun.prop('disabled', true);
        setSelectOptions($mun, []);

        $brgy.prop('disabled', true);
        setSelectOptions($brgy, []);
    }

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

        var html = '<option value="">Select a position...</option>';

        order.forEach(function (label) {
            html += '<option value="' + groups[label].join(',') + '">' + escHtml(label) + '</option>';
        });

        $job.html(html).val('').trigger('change.select2');
    }

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

    function loadPosition(jobID) {
        resetFilters();
        $('#jhs-results').hide();
        $('#jhs-count-badge').hide();

        if (!jobID) {
            $('#jhs-loading').hide();
            setEmptyState(
                'mdi-briefcase-search-outline',
                'Select a position to begin.',
                'Applicants with no Junior High School Learning Area will appear here.'
            );
            return;
        }

        $('#jhs-empty').hide();
        $('#jhs-loading').show();

        $.getJSON(dataUrl, { job: jobID }).done(function (res) {
            $('#jhs-loading').hide();

            if (!res || res.status !== 'success') {
                setEmptyState(
                    'mdi-alert-circle-outline',
                    'Unable to load applicants.',
                    (res && res.message) ? res.message : 'Please try again.'
                );
                return;
            }

            allRows = res.rows || [];
            rebuildMunicipalities();
            renderTable();
        }).fail(function () {
            $('#jhs-loading').hide();
            setEmptyState(
                'mdi-wifi-off',
                'Unable to load applicants.',
                'Please check your connection and try again.'
            );
        });
    }

    $year.select2({
        width: '100%',
        minimumResultsForSearch: Infinity
    });

    $job.select2({
        width: '100%',
        placeholder: 'Select a position...',
        allowClear: true
    });

    $mun.select2({
        width: '100%',
        placeholder: 'All Municipalities'
    });

    $brgy.select2({
        width: '100%',
        placeholder: 'All Barangays'
    });

    $year.on('change', function () {
        buildJobOptions($(this).val());
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
