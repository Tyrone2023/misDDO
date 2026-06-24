<?php
if (!function_exists('h')) {
    function h($v)
    {
        return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
    }
}
$suffixes = $jobTypeSuffixes ?? [];
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
    .rqa-hero:after { content: ""; position: absolute; width: 160px; height: 160px; border-radius: 50%; right: 90px; bottom: -100px; background: rgba(26,188,156,.22); }
    .rqa-hero-content { position: relative; z-index: 2; display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; }
    .rqa-title-block h4 { color: #fff; font-size: 1.25rem; font-weight: 800; margin: 0 0 5px; }
    .rqa-title-block p { color: rgba(255,255,255,.82); margin: 0; max-width: 760px; font-size: .86rem; line-height: 1.45; }

    .obs-live { display: inline-flex; align-items: center; gap: 7px; border-radius: 999px; padding: .4rem .8rem; font-size: .74rem; font-weight: 700; background: rgba(255,255,255,.16); border: 1px solid rgba(255,255,255,.22); color: #fff; white-space: nowrap; }
    .obs-live .dot { width: 9px; height: 9px; border-radius: 50%; background: #5af0c4; box-shadow: 0 0 0 0 rgba(90,240,196,.7); animation: obs-pulse 1.6s infinite; }
    @keyframes obs-pulse { 0% { box-shadow: 0 0 0 0 rgba(90,240,196,.6); } 70% { box-shadow: 0 0 0 9px rgba(90,240,196,0); } 100% { box-shadow: 0 0 0 0 rgba(90,240,196,0); } }
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

    #rqa-table { width: 100%; margin-bottom: 0; border-collapse: collapse; }
    #rqa-table thead th { background: #f8fbff; border-top: 0; border-bottom: 1px solid var(--rqa-border); color: #516070; font-size: .64rem; text-transform: uppercase; letter-spacing: .35px; white-space: normal; padding: 9px 6px; font-weight: 800; line-height: 1.2; }
    #rqa-table td { vertical-align: middle; font-size: .74rem; color: var(--rqa-text); padding: 8px 6px; border-color: #eef2f7; background: #fff; line-height: 1.25; }
    #rqa-table tbody tr { transition: background .18s ease; }
    #rqa-table tbody tr:hover td { background: #fbfdff; }
    #rqa-table td.num, #rqa-table th.num { text-align: center; }
    #rqa-table tbody tr.rqa-corr td { background: #fff7ec; }
    #rqa-table tbody tr.rqa-has-remarks td { background: #e8fbea; }

    .rqa-name-main { display: block; font-weight: 800; color: #223047; font-size: .76rem; line-height: 1.2; overflow-wrap: break-word; }
    .rqa-name-sub { display: flex; align-items: center; gap: 6px; margin-top: 4px; font-size: .66rem; color: var(--rqa-muted); flex-wrap: wrap; }
    .rqa-code-inline { display: inline-flex; align-items: center; border-radius: 999px; background: #f2f6fb; color: #46566a; font-weight: 800; font-size: .64rem; padding: .12rem .4rem; border: 1px solid #e5ecf5; }
    .rqa-location-tag { display: block; font-weight: 700; color: #34465c; font-size: .7rem; line-height: 1.25; overflow-wrap: break-word; }
    .rqa-rank-badge { display: inline-flex; align-items: center; justify-content: center; min-width: 24px; height: 24px; border-radius: 8px; background: var(--rqa-soft); color: var(--rqa-primary); font-weight: 800; font-size: .68rem; }
    .rqa-total-pill { display: inline-flex; align-items: center; justify-content: center; min-width: 40px; border-radius: 999px; background: #e8fff8; color: #129777; font-weight: 800; font-size: .72rem; padding: .2rem .45rem; border: 1px solid #c5f3e6; }
    .rqa-contact-line { display: block; font-size: .68rem; color: var(--rqa-text); line-height: 1.3; overflow-wrap: break-word; }
    .rqa-contact-line i { color: var(--rqa-muted); width: 13px; }
    .rqa-contact-line.muted { color: var(--rqa-muted); }

    .rqa-empty-state { display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; padding: 48px 20px; color: var(--rqa-muted); }
    .rqa-empty-state i { font-size: 46px; margin-bottom: 12px; color: #c4d2e3; }
    .rqa-empty-state strong { color: var(--rqa-text); font-size: .95rem; }
    .rqa-empty-state span { font-size: .82rem; margin-top: 4px; max-width: 460px; }
    .rqa-loading-box { padding: 48px 20px; }

    .select2-container { width: 100% !important; }
    .select2-container--default .select2-selection--single { height: 38px; border-radius: 10px; border-color: #dfe7f1; display: flex; align-items: center; }
    .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 36px; color: #344054; padding-left: 12px; font-size: .84rem; }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 36px; right: 7px; }
    .select2-container--default .select2-selection--multiple { min-height: 38px; border-radius: 10px; border-color: #dfe7f1; padding: 1px 4px; }
    .select2-container--default.select2-container--focus .select2-selection--multiple,
    .select2-container--default.select2-container--open .select2-selection--multiple { border-color: var(--rqa-accent); box-shadow: 0 0 0 .15rem rgba(26,188,156,.13); }
    .select2-container--default .select2-selection--multiple .select2-selection__choice { background: var(--rqa-soft); border: 1px solid #dce8f7; color: var(--rqa-primary); border-radius: 6px; font-size: .76rem; font-weight: 700; margin-top: 5px; padding: 2px 8px; }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove { color: var(--rqa-primary); margin-right: 5px; }
    .select2-container--default .select2-selection--multiple .select2-search__field { margin-top: 7px; }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid rqa-page-shell">

            <div class="rqa-hero">
                <div class="rqa-hero-content">
                    <div class="rqa-title-block">
                        <!-- <h4><?= h($title ?? 'RQA Recommendation (Observer)'); ?></h4> -->
                         <h4>RQA Recommendation</h4>
                        <p>Read-only view. Pick a year and a position to watch the ranked qualified applicants. As an applicant is recommended by another user, they leave this list automatically &mdash; no need to refresh.</p>
                    </div>
                    <div class="d-flex align-items-center" style="gap:12px; flex-wrap:wrap;">
                        <span id="rqa-count-badge"></span>
                        <span class="obs-live"><span class="dot"></span> Live</span>
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
                                <?php foreach (($years ?? []) as $yr) : ?>
                                    <option value="<?= (int) $yr; ?>" <?= (int) ($selectedYear ?? 0) === (int) $yr ? 'selected' : ''; ?>>S.Y. <?= (int) $yr; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group col-xl-3 col-lg-3 col-md-6">
                            <label for="job-filter">Position</label>
                            <select id="job-filter" class="form-control"><option value="">Select a position&hellip;</option></select>
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
                        <h5 class="rqa-section-title"><i class="mdi mdi-format-list-numbered"></i> Ranked Results</h5>
                    </div>

                    <div id="rqa-loading" class="text-center text-muted rqa-loading-box" style="display:none;">
                        <div class="spinner-border text-primary" role="status"></div>
                        <div class="mt-3 font-weight-bold">Loading applicants&hellip;</div>
                    </div>

                    <div id="rqa-empty" class="rqa-empty-state">
                        <i class="mdi mdi-briefcase-search-outline"></i>
                        <strong>Select a position to begin.</strong>
                        <span>The ranked applicant list will appear here after selecting a position.</span>
                    </div>

                    <div class="rqa-results-wrap table-responsive" id="rqa-results" style="display:none;">
                        <table class="table table-hover table-bordered w-100" id="rqa-table">
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
                                    <th class="rqa-col-tribe" style="display:none;">Tribe</th>
                                    <th>Remarks</th>
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
    var dataUrl = '<?= base_url('Pages/rqa_recommendation_data'); ?>';
    var preselectJob = '<?= ($selectedJobId ?? 0) > 0 ? (int) $selectedJobId : ''; ?>';
    var POLL_MS = 5000;

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
    var tribeApplicable = false;

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

    function escHtml(s) { return String(s == null ? '' : s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;'); }
    function escAttr(s) { return escHtml(s).replace(/"/g, '&quot;'); }
    function eqi(a, b) { return (a || '').trim().toLowerCase() === (b || '').trim().toLowerCase(); }

    function inListI(list, val) {
        val = (val || '').trim().toLowerCase();
        for (var i = 0; i < list.length; i++) {
            if ((list[i] || '').trim().toLowerCase() === val) return true;
        }
        return false;
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

    // Rebuild a multi-select's options. `keep` retains still-valid selections
    // (used while polling) so the observer's filter survives each refresh.
    function setMulti($el, values, keep) {
        var prev = keep ? ($el.val() || []) : [];
        var avail = {};
        values.forEach(function (v) { avail[(v || '').trim().toLowerCase()] = true; });
        var restored = prev.filter(function (v) { return avail[(v || '').trim().toLowerCase()]; });
        var html = '';
        values.forEach(function (v) { html += '<option value="' + escAttr(v) + '">' + escHtml(v) + '</option>'; });
        $el.html(html).val(restored).trigger('change.select2');
    }

    $year.select2({ width: '100%', minimumResultsForSearch: Infinity });
    $job.select2({ width: '100%', placeholder: 'Select a position…', allowClear: true });
    $mun.select2({ width: '100%', placeholder: 'All Municipalities' });
    $brgy.select2({ width: '100%', placeholder: 'All Barangays' });
    $jhsGroup.select2({ width: '100%', placeholder: 'All' });
    $strand.select2({ width: '100%', placeholder: 'All Strands' });
    $major.select2({ width: '100%', placeholder: 'All Specializations' });

    // Rebuild the Position list for a school year. Vacancies that share the same
    // position (title/type) are merged into one option whose value is the
    // comma-separated jobIDs.
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

    function setEmptyState(icon, title, text) {
        $('#rqa-empty').html('<i class="mdi ' + icon + '"></i><strong>' + escHtml(title) + '</strong><span>' + escHtml(text) + '</span>').show();
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

    function rowHtml(r, index) {
        var isCorr = parseInt(r.isCorrigendum, 10) === 1;
        var hasRemarks = (r.remarks || '').trim() !== '';
        var trClass = isCorr ? ' rqa-corr' : '';
        if (hasRemarks) trClass += ' rqa-has-remarks';

        var html = '<tr class="rqa-row' + trClass + '" data-app-id="' + r.appID + '">';
        html += '<td class="num"><span class="rqa-rank-badge">' + index + '</span></td>';
        html += '<td>';
        html += '<span class="rqa-name-main">' + escHtml(r.name) + '</span>';
        html += '<span class="rqa-name-sub">Code: <span class="rqa-code-inline">' + escHtml(r.code) + '</span></span>';
        html += '<span class="rqa-contact-line' + (r.contact ? '' : ' muted') + '"><i class="mdi mdi-phone"></i> ' + (r.contact ? escHtml(r.contact) : 'N/A') + '</span>';
        html += '<span class="rqa-contact-line' + (r.empEmail ? '' : ' muted') + '"><i class="mdi mdi-email-outline"></i> ' + (r.empEmail ? escHtml(r.empEmail) : 'N/A') + '</span>';
        html += '</td>';
        html += '<td><span class="rqa-location-tag">' + escHtml(locationText(r)) + '</span></td>';
        if (specializationApplicable) html += '<td class="rqa-col-spec">' + escHtml(specializationText(r)) + '</td>';
        html += '<td class="num">' + escHtml(r.education) + '</td>';
        html += '<td class="num">' + escHtml(r.training) + '</td>';
        html += '<td class="num">' + escHtml(r.experience) + '</td>';
        html += '<td class="num">' + escHtml(r.let_rating) + '</td>';
        html += '<td class="num">' + escHtml(r.demo_rating) + '</td>';
        html += '<td class="num">' + escHtml(r.tr_rating) + '</td>';
        html += '<td class="num"><span class="rqa-total-pill">' + escHtml(r.total_points) + '</span></td>';
        if (tribeApplicable) html += '<td class="rqa-col-tribe">' + (r.tribe ? escHtml(r.tribe) : '<span class="text-muted">&mdash;</span>') + '</td>';
        html += '<td>' + (hasRemarks ? escHtml(r.remarks) : '<span class="text-muted">&mdash;</span>') + '</td>';
        html += '</tr>';
        return html;
    }

    function rowScoreNum(r) { var n = parseFloat(r.total_points); return isNaN(n) ? -Infinity : n; }

    function cmpText(a, b) {
        a = (a || '').trim(); b = (b || '').trim();
        if (a === b) return 0;
        if (a === '') return 1;
        if (b === '') return -1;
        return a.localeCompare(b, undefined, { sensitivity: 'base' });
    }

    function rowTieOrder(r) { var n = parseInt(r.tieOrder, 10); return isNaN(n) ? 1e9 : n; }

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

    function renderTable() {
        var rows = sortRows(getFilteredRows());
        var $tbody = $('#rqa-table tbody');

        $('.rqa-col-spec').toggle(specializationApplicable);
        $('.rqa-col-tribe').toggle(tribeApplicable);
        $('#rqa-table thead th.rqa-col-spec').text(specializationKind === 'shs' ? 'Strand / Spec.' : 'Spec.');

        if (rows.length === 0) {
            $('#rqa-results').hide();
            if (allRows.length === 0) {
                setEmptyState('mdi-account-off-outline', 'No qualified applicants found.', 'There are no qualified applicants left for the selected position.');
            } else {
                setEmptyState('mdi-filter-remove-outline', 'No applicants match the selected filters.', 'Try changing the municipality, barangay, or specialization filter.');
            }
        } else {
            var html = '';
            rows.forEach(function (r, i) { html += rowHtml(r, i + 1); });
            $tbody.html(html);
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

    function syncSpecializations(keep) {
        $('.rqa-kind-filter').hide();
        if (specializationKind !== 'jhs') setMulti($jhsGroup, [], false);
        if (specializationKind !== 'shs') { setMulti($strand, [], false); setMulti($major, [], false); }

        if (specializationKind === 'jhs') {
            setMulti($jhsGroup, distinct(allRows.map(function (r) { return r.specializationGroup || r.specialization; })), keep);
            $jhsGroupWrap.show();
        } else if (specializationKind === 'shs') {
            setMulti($strand, distinct(allRows.map(function (r) { return r.strand; })), keep);
            setMulti($major, distinct(allRows.map(function (r) { return r.major; })), keep);
            $strandWrap.show();
            $majorWrap.show();
        }
    }

    function syncMunicipalities(keep) {
        var muns = distinct(allRows.map(function (r) { return r.municipality; }));
        setMulti($mun, muns, keep);
        $mun.prop('disabled', muns.length === 0).trigger('change.select2');
        syncBarangays(keep);
    }

    function syncBarangays(keep) {
        var selectedMuns = $mun.val() || [];
        if (selectedMuns.length === 0) {
            setMulti($brgy, [], false);
            $brgy.prop('disabled', true).trigger('change.select2');
            return;
        }
        var brgys = distinct(
            allRows.filter(function (r) { return inListI(selectedMuns, r.municipality); })
                .map(function (r) { return r.brgy; })
        );
        setMulti($brgy, brgys, keep);
        $brgy.prop('disabled', brgys.length === 0).trigger('change.select2');
    }

    function resetFilters() {
        allRows = [];
        specializationKind = 'none';
        specializationApplicable = false;
        tribeApplicable = false;
        $('.rqa-kind-filter').hide();
        $mun.prop('disabled', true);
        setMulti($mun, [], false);
        $brgy.prop('disabled', true);
        setMulti($brgy, [], false);
        setMulti($jhsGroup, [], false);
        setMulti($strand, [], false);
        setMulti($major, [], false);
    }

    // Load (or poll) the ranked applicants for a position. While polling
    // (isPoll = true) the observer's current filter selections are preserved.
    function loadPosition(jobID, isPoll) {
        if (!isPoll) {
            resetFilters();
            $('#rqa-results').hide();
            $('#rqa-count-badge').hide();
        }

        if (!jobID) {
            $('#rqa-loading').hide();
            setEmptyState('mdi-briefcase-search-outline', 'Select a position to begin.', 'The ranked applicant list will appear here after selecting a position.');
            return;
        }

        if (!isPoll) {
            $('#rqa-empty').hide();
            $('#rqa-loading').show();
        }

        $.getJSON(dataUrl, { job: jobID }).done(function (res) {
            $('#rqa-loading').hide();
            // Ignore a stale poll response if the observer switched positions.
            if (isPoll && ($job.val() || '') !== jobID) return;

            if (!res || res.status !== 'success') {
                if (!isPoll) setEmptyState('mdi-alert-circle-outline', 'Unable to load applicants.', (res && res.message) ? res.message : 'Please try again.');
                return;
            }

            allRows = res.rows || [];
            specializationApplicable = !!res.specializationApplicable;
            specializationKind = res.specializationKind || 'none';
            tribeApplicable = !!res.tribeApplicable;

            syncMunicipalities(!!isPoll);
            syncSpecializations(!!isPoll);
            renderTable();
        }).fail(function () {
            $('#rqa-loading').hide();
            if (!isPoll) setEmptyState('mdi-wifi-off', 'Unable to load applicants.', 'Please check your connection and try again.');
        });
    }

    $year.on('change', function () { buildJobOptions($(this).val()); loadPosition('', false); });
    $job.on('change', function () { loadPosition($(this).val(), false); });
    $mun.on('change', function () { syncBarangays(false); renderTable(); });
    $brgy.on('change', function () { renderTable(); });
    $jhsGroup.on('change', function () { renderTable(); });
    $strand.on('change', function () { renderTable(); });
    $major.on('change', function () { renderTable(); });

    // Initial position list for the selected year, with optional preselection.
    buildJobOptions($year.val());
    if (preselectJob) {
        var grp = findJobGroupValue(preselectJob);
        if (grp) { $job.val(grp).trigger('change.select2'); loadPosition(grp, false); }
    }

    // Realtime: re-poll the currently selected position so recommended
    // applicants drop off (and remarks/tribe updates show) on their own.
    setInterval(function () {
        var jobID = $job.val() || '';
        if (jobID) loadPosition(jobID, true);
    }, POLL_MS);
});
</script>
