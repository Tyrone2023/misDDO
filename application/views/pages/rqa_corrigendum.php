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
        --rqa-accent: #2b6cd4;
        --rqa-bg: #f4f7fb;
        --rqa-border: #e5ecf5;
        --rqa-text: #25364a;
        --rqa-muted: #7b8794;
        --rqa-soft: #eef5ff;
    }

    .content-page { background: var(--rqa-bg); min-height: 100vh; }
    .rqa-page-shell { padding-bottom: 24px; }

    .rqa-hero {
        position: relative; overflow: hidden; border-radius: 18px;
        padding: 22px 24px; margin-bottom: 18px;
        background: linear-gradient(135deg, var(--rqa-primary), var(--rqa-primary-2));
        box-shadow: 0 14px 35px rgba(31, 58, 95, .18); color: #fff;
    }
    .rqa-hero:before { content: ""; position: absolute; width: 230px; height: 230px; border-radius: 50%; right: -80px; top: -95px; background: rgba(255,255,255,.10); }
    .rqa-hero:after { content: ""; position: absolute; width: 160px; height: 160px; border-radius: 50%; right: 90px; bottom: -100px; background: rgba(43,108,212,.22); }
    .rqa-hero-content { position: relative; z-index: 2; display: flex; align-items: center; justify-content: space-between; gap: 16px; }
    .rqa-title-block h4 { color: #fff; font-size: 1.25rem; font-weight: 800; margin: 0 0 5px; }
    .rqa-title-block p { color: rgba(255,255,255,.82); margin: 0; max-width: 760px; font-size: .86rem; line-height: 1.45; }
    .rqa-hero-icon { width: 54px; height: 54px; border-radius: 16px; display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,.14); border: 1px solid rgba(255,255,255,.18); flex: 0 0 auto; }
    .rqa-hero-icon i { font-size: 28px; color: #fff; }

    #rqa-count-badge { display: none; border-radius: 999px; padding: .5rem .75rem; font-size: .78rem; font-weight: 700; background: rgba(255,255,255,.16); border: 1px solid rgba(255,255,255,.22); color: #fff; white-space: nowrap; }

    .rqa-card { border: 0; border-radius: 16px; box-shadow: 0 8px 26px rgba(31, 58, 95, .08); overflow: hidden; }
    .rqa-card .card-body { padding: 18px; }
    .rqa-filter-card { margin-bottom: 18px; }

    .rqa-section-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 14px; }
    .rqa-section-title { margin: 0; font-size: .94rem; font-weight: 800; color: var(--rqa-text); display: flex; align-items: center; gap: 8px; }
    .rqa-section-title i { width: 32px; height: 32px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; background: var(--rqa-soft); color: var(--rqa-primary); font-size: 17px; }

    .rqa-filter-card label { font-weight: 800; font-size: .68rem; text-transform: uppercase; letter-spacing: .55px; color: var(--rqa-muted); margin-bottom: 6px; }
    .rqa-filter-card .form-group { margin-bottom: 0; }
    .rqa-filter-grid { row-gap: 14px; }
    .rqa-help-chip { display: inline-flex; align-items: center; gap: 6px; color: var(--rqa-muted); font-size: .76rem; background: #f8fafd; border: 1px solid var(--rqa-border); padding: 6px 10px; border-radius: 999px; }

    .rqa-results-card .card-body { padding: 0; }
    .rqa-results-header { padding: 16px 18px; border-bottom: 1px solid var(--rqa-border); background: #fff; }
    .rqa-results-wrap { background: #fff; width: 100%; }

    #rqa-table { width: 100%; margin-bottom: 0; table-layout: fixed; border-collapse: collapse; }
    #rqa-table thead th { background: #f8fbff; border-top: 0; border-bottom: 1px solid var(--rqa-border); color: #516070; font-size: .62rem; text-transform: uppercase; letter-spacing: .35px; white-space: normal; padding: 9px 6px; font-weight: 800; line-height: 1.2; }
    #rqa-table td { vertical-align: middle; font-size: .72rem; color: var(--rqa-text); padding: 8px 6px; border-color: #eef2f7; background: #fff; line-height: 1.25; }
    #rqa-table tbody tr:hover td { background: #fbfdff; }
    #rqa-table td.num, #rqa-table th.num { text-align: center; }
    #rqa-table tbody tr.rqa-marked td { background: #eaf1ff; }
    #rqa-table tbody tr.rqa-marked:hover td { background: #ddeaff; }

    .rqa-name-main { display: block; font-weight: 800; color: #223047; font-size: .74rem; line-height: 1.2; overflow-wrap: break-word; }
    .rqa-name-sub { display: flex; align-items: center; gap: 4px; margin-top: 4px; font-size: .65rem; color: var(--rqa-muted); flex-wrap: wrap; }
    .rqa-code-inline { display: inline-flex; align-items: center; border-radius: 999px; background: #f2f6fb; color: #46566a; font-weight: 800; font-size: .62rem; padding: .12rem .35rem; border: 1px solid #e5ecf5; }
    .rqa-location-tag { display: block; font-weight: 700; color: #34465c; font-size: .68rem; line-height: 1.25; overflow-wrap: break-word; }
    .rqa-rank-badge { display: inline-flex; align-items: center; justify-content: center; min-width: 24px; height: 24px; padding: 0 .35rem; border-radius: 999px; background: linear-gradient(135deg, #eaf1ff, #f7fbff); color: var(--rqa-primary); font-weight: 900; font-size: .65rem; border: 1px solid #dce8f7; }

    .rqa-score-input { width: 100%; min-width: 0; border-radius: 7px; border-color: #dfe7f1; font-size: .68rem; height: 30px; padding: 4px 4px; text-align: center; }
    .rqa-score-input.total { font-weight: 800; background: #f3fff9; color: #157347; cursor: default; }
    .rqa-score-input:focus { border-color: var(--rqa-accent); box-shadow: 0 0 0 .12rem rgba(43,108,212,.13); }
    .rqa-type-select { width: 100%; border-radius: 7px; border-color: #dfe7f1; font-size: .66rem; height: 30px; padding: 2px 4px; }

    .rqa-save-btn { width: 100%; border-radius: 999px; padding: .34rem .35rem; font-weight: 800; font-size: .62rem; background: var(--rqa-accent); border-color: var(--rqa-accent); box-shadow: 0 5px 12px rgba(43,108,212,.16); white-space: normal; line-height: 1.1; }
    .rqa-save-btn:hover { background: #245cb6; border-color: #245cb6; }
    .rqa-type-pill { display: inline-block; margin-top: 4px; font-size: .56rem; font-weight: 800; text-transform: uppercase; letter-spacing: .3px; color: #2b6cd4; }

    .rqa-empty-state { display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; padding: 40px 18px; color: var(--rqa-muted); }
    .rqa-empty-state i { font-size: 40px; margin-bottom: 10px; color: #c4d2e3; }
    .rqa-empty-state strong { color: var(--rqa-text); font-size: .95rem; }
    .rqa-empty-state span { font-size: .82rem; margin-top: 4px; max-width: 460px; }
    .rqa-loading-box { padding: 40px 18px; text-align: center; }

    .select2-container { width: 100% !important; }
    .select2-container--default .select2-selection--single { height: 38px; border-radius: 10px; border-color: #dfe7f1; display: flex; align-items: center; }
    .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 36px; color: #344054; padding-left: 12px; font-size: .84rem; }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 36px; right: 7px; }
    .select2-container--default .select2-selection--multiple { min-height: 38px; border-radius: 10px; border-color: #dfe7f1; padding: 1px 4px; }
    .select2-container--default .select2-selection--multiple .select2-selection__choice { background: var(--rqa-soft); border: 1px solid #dce8f7; color: var(--rqa-primary); border-radius: 6px; font-size: .76rem; font-weight: 700; margin-top: 5px; padding: 2px 8px; }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid rqa-page-shell">

            <div class="rqa-hero">
                <div class="rqa-hero-content">
                    <div class="rqa-title-block">
                        <h4><?= h($title ?? 'Corrigendum / Addendum'); ?></h4>
                        <p>Add or correct an applicant's RQA score after a vacancy has been closed. Pick a position, edit the score, choose Corrigendum or Addendum, then Save. Saved applicants are flagged on the RQA Recommendation report.</p>
                    </div>
                    <div class="d-flex align-items-center" style="gap:12px;">
                        <span id="rqa-count-badge"></span>
                        <div class="rqa-hero-icon"><i class="mdi mdi-file-document-edit-outline"></i></div>
                    </div>
                </div>
            </div>

            <div class="card rqa-card rqa-filter-card">
                <div class="card-body">
                    <div class="rqa-section-head">
                        <h5 class="rqa-section-title"><i class="mdi mdi-filter-variant"></i> Filters</h5>
                        <span class="rqa-help-chip"><i class="mdi mdi-information-outline"></i> Pick a year, then a position to begin</span>
                    </div>

                    <div class="form-row rqa-filter-grid">
                        <div class="form-group col-xl-2 col-lg-2 col-md-6">
                            <label for="year-filter">Year</label>
                            <select id="year-filter" class="form-control">
                                <?php foreach ($years as $yr) : ?>
                                    <option value="<?= (int) $yr; ?>" <?= $selectedYear === (int) $yr ? 'selected' : ''; ?>>S.Y. <?= (int) $yr; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group col-xl-3 col-lg-3 col-md-6">
                            <label for="job-filter">Position</label>
                            <select id="job-filter" class="form-control"><option value="">Select a position…</option></select>
                        </div>

                        <div class="form-group col-xl-4 col-lg-4 col-md-6">
                            <label for="name-filter">Applicant Name</label>
                            <input type="text" id="name-filter" class="form-control" placeholder="Search by name or code…" autocomplete="off">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card rqa-card rqa-results-card">
                <div class="card-body">
                    <div class="rqa-results-header">
                        <h5 class="rqa-section-title"><i class="mdi mdi-playlist-edit"></i> Applicants &mdash; Edit / Add Score</h5>
                    </div>

                    <div id="rqa-loading" class="text-muted rqa-loading-box" style="display:none;">
                        <div class="spinner-border text-primary" role="status"></div>
                        <div class="mt-3 font-weight-bold">Loading applicants…</div>
                    </div>

                    <div id="rqa-empty" class="rqa-empty-state">
                        <i class="mdi mdi-briefcase-search-outline"></i>
                        <strong>Select a position to begin.</strong>
                        <span>The applicant list will appear here after selecting a position.</span>
                    </div>

                    <div class="rqa-results-wrap table-responsive" id="rqa-results" style="display:none;">
                        <table class="table table-hover table-bordered w-100" id="rqa-table">
                            <colgroup>
                                <col style="width:4%;">
                                <col style="width:15%;">
                                <col style="width:11%;">
                                <col class="rqa-col-spec" style="width:8%; display:none;">
                                <col style="width:6%;">
                                <col style="width:6%;">
                                <col style="width:6%;">
                                <col style="width:6%;">
                                <col style="width:6%;">
                                <col style="width:6%;">
                                <col style="width:7%;">
                                <col style="width:9%;">
                                <col style="width:8%;">
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
                                    <th>Type</th>
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
    var dataUrl = '<?= base_url('Pages/rqa_corrigendum_data'); ?>';
    var saveUrl = '<?= base_url('Pages/rqa_corrigendum_save'); ?>';
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
    var specializationApplicable = false;
    var specializationKind = 'none';

    var $year = $('#year-filter');
    var $job = $('#job-filter');
    var $name = $('#name-filter');

    function escHtml(s) { return String(s == null ? '' : s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;'); }
    function escAttr(s) { return escHtml(s).replace(/"/g, '&quot;'); }

    $year.select2({ width: '100%', minimumResultsForSearch: Infinity });
    $job.select2({ width: '100%', placeholder: 'Select a position…', allowClear: true });

    function buildJobOptions(year) {
        year = parseInt(year, 10) || 0;
        var groups = {}, order = [];
        allJobs.filter(function (j) { return j.sy === year; }).forEach(function (j) {
            if (!groups[j.label]) { groups[j.label] = []; order.push(j.label); }
            groups[j.label].push(j.id);
        });
        var html = '<option value="">Select a position…</option>';
        order.forEach(function (label) { html += '<option value="' + groups[label].join(',') + '">' + escHtml(label) + '</option>'; });
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
                if (parseInt(ids[i], 10) === jobId) { match = val; return false; }
            }
        });
        return match;
    }

    function setSelectOptions($el, values) {
        var html = '';
        values.forEach(function (v) { html += '<option value="' + escAttr(v) + '">' + escHtml(v) + '</option>'; });
        $el.html(html).val([]).trigger('change.select2');
    }

    function distinct(arr) {
        var seen = {}, out = [];
        arr.forEach(function (v) {
            v = (v || '').trim();
            if (v !== '' && !seen[v.toLowerCase()]) { seen[v.toLowerCase()] = true; out.push(v); }
        });
        out.sort(function (a, b) { return a.localeCompare(b); });
        return out;
    }

    function inListI(list, val) {
        val = (val || '').trim().toLowerCase();
        for (var i = 0; i < list.length; i++) { if ((list[i] || '').trim().toLowerCase() === val) return true; }
        return false;
    }

    function cmpText(a, b) {
        a = (a || '').trim(); b = (b || '').trim();
        if (a === b) return 0;
        if (a === '') return 1;
        if (b === '') return -1;
        return a.localeCompare(b, undefined, { sensitivity: 'base' });
    }

    function locationText(r) {
        var m = (r.municipality || '').trim(), b = (r.brgy || '').trim();
        if (m && b) return m + ' - ' + b;
        return m || b || '';
    }

    function specializationText(r) {
        if (specializationKind === 'jhs') return (r.specializationGroup || r.specialization || '').trim();
        if (specializationKind === 'shs') {
            var strand = (r.strand || '').trim(), major = (r.major || '').trim();
            if (strand && major) return strand + ' / ' + major;
            return strand || major || '';
        }
        return (r.specialization || '').trim();
    }

    // Sort: group by specialization (A-Z), then by applicant name.
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
            return cmpText(a.name, b.name);
        });
        return rows;
    }

    var scoreComponentFields = [
        'education',
        'training',
        'experience',
        'let_rating',
        'demo_rating',
        'tr_rating'
    ];

    function numericScore(value) {
        var score = parseFloat(value);
        return isFinite(score) ? score : 0;
    }

    function calculatedTotal(r) {
        var total = 0;
        scoreComponentFields.forEach(function (field) {
            total += numericScore(r[field]);
        });
        return total.toFixed(2);
    }

    function scoreInput(field, value) {
        return '<input type="number" step="0.01" min="0" class="form-control form-control-sm rqa-score-input'
            + (field === 'total_points' ? ' total' : '') + '" data-field="' + field + '"'
            + (field === 'total_points' ? ' readonly tabindex="-1"' : '')
            + ' value="' + escAttr(value == null ? '' : value) + '">';
    }

    function rowHtml(r, index) {
        var marked = (r.corrigendumType || '') !== '';
        var html = '<tr class="rqa-row' + (marked ? ' rqa-marked' : '') + '" data-app-id="' + r.appID + '" data-job-id="' + r.jobID + '" data-code="' + escAttr(r.code) + '">';

        html += '<td class="num"><span class="rqa-rank-badge">' + index + '</span></td>';

        html += '<td><span class="rqa-name-main">' + escHtml(r.name) + '</span>'
            + '<span class="rqa-name-sub">Code: <span class="rqa-code-inline">' + escHtml(r.code) + '</span></span></td>';

        html += '<td><span class="rqa-location-tag">' + escHtml(locationText(r)) + '</span></td>';

        if (specializationApplicable) {
            html += '<td class="rqa-col-spec">' + escHtml(specializationText(r)) + '</td>';
        }

        html += '<td class="num">' + scoreInput('education', r.education) + '</td>';
        html += '<td class="num">' + scoreInput('training', r.training) + '</td>';
        html += '<td class="num">' + scoreInput('experience', r.experience) + '</td>';
        html += '<td class="num">' + scoreInput('let_rating', r.let_rating) + '</td>';
        html += '<td class="num">' + scoreInput('demo_rating', r.demo_rating) + '</td>';
        html += '<td class="num">' + scoreInput('tr_rating', r.tr_rating) + '</td>';
        html += '<td class="num">' + scoreInput('total_points', calculatedTotal(r)) + '</td>';

        var type = (r.corrigendumType || 'corrigendum').toLowerCase();
        html += '<td><select class="form-control form-control-sm rqa-type-select">'
            + '<option value="corrigendum"' + (type === 'corrigendum' ? ' selected' : '') + '>Corrigendum</option>'
            + '<option value="addendum"' + (type === 'addendum' ? ' selected' : '') + '>Addendum</option>'
            + '</select>'
            + (marked ? '<span class="rqa-type-pill">Marked: ' + escHtml(r.corrigendumType) + '</span>' : '')
            + '</td>';

        html += '<td><button type="button" class="btn btn-sm rqa-save-btn"><i class="mdi mdi-content-save-outline mr-1"></i>Save</button></td>';

        html += '</tr>';
        return html;
    }

    function getFilteredRows() {
        var term = ($name.val() || '').trim().toLowerCase();

        return allRows.filter(function (r) {
            if (term !== '') {
                var hay = ((r.name || '') + ' ' + (r.code || '')).toLowerCase();
                if (hay.indexOf(term) === -1) return false;
            }
            return true;
        });
    }

    function setEmptyState(icon, title, text) {
        $('#rqa-empty').html('<i class="mdi ' + icon + '"></i><strong>' + escHtml(title) + '</strong><span>' + escHtml(text) + '</span>').show();
    }

    function renderTable() {
        var rows = sortRows(getFilteredRows());

        $('.rqa-col-spec').toggle(specializationApplicable);
        $('#rqa-table thead th.rqa-col-spec').text(specializationKind === 'shs' ? 'Strand / Spec.' : 'Spec. Group');

        if (rows.length === 0) {
            $('#rqa-results').hide();
            if (allRows.length === 0) {
                setEmptyState('mdi-account-off-outline', 'No applicants found.', 'There are no qualified applicants for the selected position.');
            } else {
                setEmptyState('mdi-filter-remove-outline', 'No applicants match the search.', 'Try a different applicant name or code.');
            }
        } else {
            var html = '';
            rows.forEach(function (r, i) { html += rowHtml(r, i + 1); });
            $('#rqa-table tbody').html(html);
            $('#rqa-empty').hide();
            $('#rqa-results').show();
        }

        var badge = $('#rqa-count-badge');
        if (allRows.length > 0) {
            badge.text(rows.length + ' of ' + allRows.length + ' applicant' + (allRows.length === 1 ? '' : 's')).show();
        } else {
            badge.hide();
        }
    }

    function resetFilters() {
        allRows = [];
        specializationKind = 'none';
        specializationApplicable = false;
        $name.val('');
    }

    function loadPosition(jobID) {
        resetFilters();
        $('#rqa-results').hide();
        $('#rqa-count-badge').hide();

        if (!jobID) {
            $('#rqa-loading').hide();
            setEmptyState('mdi-briefcase-search-outline', 'Select a position to begin.', 'The applicant list will appear here after selecting a position.');
            return;
        }

        $('#rqa-empty').hide();
        $('#rqa-loading').show();

        $.getJSON(dataUrl, { job: jobID }).done(function (res) {
            $('#rqa-loading').hide();
            if (!res || res.status !== 'success') {
                setEmptyState('mdi-alert-circle-outline', 'Unable to load applicants.', (res && res.message) ? res.message : 'Please try again.');
                return;
            }
            allRows = res.rows || [];
            specializationApplicable = !!res.specializationApplicable;
            specializationKind = res.specializationKind || 'none';
            renderTable();
        }).fail(function () {
            $('#rqa-loading').hide();
            setEmptyState('mdi-wifi-off', 'Unable to load applicants.', 'Please check your connection and try again.');
        });
    }

    $year.on('change', function () { buildJobOptions($(this).val()); loadPosition(''); });
    $job.on('change', function () { loadPosition($(this).val()); });
    $name.on('input', function () { renderTable(); });

    $(document).on('input change', '.rqa-score-input:not(.total)', function () {
        var $row = $(this).closest('tr');
        var total = 0;

        scoreComponentFields.forEach(function (field) {
            total += numericScore($row.find('.rqa-score-input[data-field="' + field + '"]').val());
        });

        $row.find('.rqa-score-input[data-field="total_points"]').val(total.toFixed(2));
    });

    $(document).on('click', '.rqa-save-btn', function () {
        var $btn = $(this);
        var $row = $btn.closest('tr');
        var appID = parseInt($row.attr('data-app-id'), 10);
        var jobID = parseInt($row.attr('data-job-id'), 10);
        var code = $row.attr('data-code') || '';
        var type = $row.find('.rqa-type-select').val() || 'corrigendum';

        var payload = { appID: appID, jobID: jobID, record_no: code, type: type };
        $row.find('.rqa-score-input').each(function () {
            payload[$(this).attr('data-field')] = $.trim($(this).val());
        });

        Swal.fire({
            title: 'Save this score?',
            html: 'Applicant: <strong>' + escHtml($row.find('.rqa-name-main').text()) + '</strong><br>Mark as: <strong>' + escHtml(type.charAt(0).toUpperCase() + type.slice(1)) + '</strong>',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, save',
            confirmButtonColor: '#2b6cd4'
        }).then(function (result) {
            if (!result.value) return;

            $btn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin mr-1"></i>Saving…');

            $.post(saveUrl, payload, null, 'json').done(function (res) {
                if (res && res.status === 'success') {
                    Swal.fire({ icon: 'success', title: 'Saved', text: res.message, timer: 1500, showConfirmButton: false });

                    var row = allRows.filter(function (r) { return r.appID === appID; })[0];
                    if (row) {
                        row.corrigendumType = res.corrigendumType || type;
                        row.education = payload.education;
                        row.training = payload.training;
                        row.experience = payload.experience;
                        row.let_rating = payload.let_rating;
                        row.demo_rating = payload.demo_rating;
                        row.tr_rating = payload.tr_rating;
                        row.total_points = res.total_points || payload.total_points;
                    }

                    $row.addClass('rqa-marked');
                    if (!$row.find('.rqa-type-pill').length) {
                        $row.find('.rqa-type-select').after('<span class="rqa-type-pill">Marked: ' + escHtml(res.corrigendumType || type) + '</span>');
                    } else {
                        $row.find('.rqa-type-pill').text('Marked: ' + (res.corrigendumType || type));
                    }
                    $btn.prop('disabled', false).html('<i class="mdi mdi-content-save-outline mr-1"></i>Save');
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: (res && res.message) ? res.message : 'Something went wrong.' });
                    $btn.prop('disabled', false).html('<i class="mdi mdi-content-save-outline mr-1"></i>Save');
                }
            }).fail(function () {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Unable to save. Please try again.' });
                $btn.prop('disabled', false).html('<i class="mdi mdi-content-save-outline mr-1"></i>Save');
            });
        });
    });

    // Preselect a position if one was passed in the URL.
    if (preselectJob) {
        var preJob = allJobs.filter(function (j) { return j.id === parseInt(preselectJob, 10); })[0];
        if (preJob && $year.val() != preJob.sy) {
            $year.val(String(preJob.sy)).trigger('change.select2');
        }
    }

    buildJobOptions($year.val());

    if (preselectJob) {
        var groupVal = findJobGroupValue(preselectJob);
        if (groupVal) { $job.val(groupVal).trigger('change.select2'); loadPosition(groupVal); }
    }
});
</script>
