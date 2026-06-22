<?php
if (!function_exists('h')) {
    function h($v)
    {
        return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
    }
}
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

    #rqa-table { width: 100%; margin-bottom: 0; border-collapse: collapse; }
    #rqa-table thead th { background: #f8fbff; border-top: 0; border-bottom: 1px solid var(--rqa-border); color: #516070; font-size: .64rem; text-transform: uppercase; letter-spacing: .35px; white-space: normal; padding: 9px 6px; font-weight: 800; line-height: 1.2; }
    #rqa-table td { vertical-align: middle; font-size: .74rem; color: var(--rqa-text); padding: 8px 6px; border-color: #eef2f7; background: #fff; line-height: 1.25; }
    #rqa-table tbody tr { transition: background .18s ease; }
    #rqa-table tbody tr:hover td { background: #fbfdff; }
    #rqa-table td.num, #rqa-table th.num { text-align: center; }

    .rqa-name-main { display: block; font-weight: 800; color: #223047; font-size: .76rem; line-height: 1.2; overflow-wrap: break-word; }
    .rqa-name-sub { display: flex; align-items: center; gap: 6px; margin-top: 4px; font-size: .66rem; color: var(--rqa-muted); flex-wrap: wrap; }
    .rqa-code-inline { display: inline-flex; align-items: center; border-radius: 999px; background: #f2f6fb; color: #46566a; font-weight: 800; font-size: .64rem; padding: .12rem .4rem; border: 1px solid #e5ecf5; }
    .rqa-location-tag { display: block; font-weight: 700; color: #34465c; font-size: .7rem; line-height: 1.25; overflow-wrap: break-word; }

    .rqa-rank-badge { display: inline-flex; align-items: center; justify-content: center; min-width: 24px; height: 24px; border-radius: 8px; background: var(--rqa-soft); color: var(--rqa-primary); font-weight: 800; font-size: .68rem; }
    .rqa-item-pill { display: inline-flex; align-items: center; border-radius: 8px; background: #fff6e9; color: #9a6b16; font-weight: 800; font-size: .68rem; padding: .2rem .45rem; border: 1px solid #f1ddb9; }
    .rqa-total-pill { display: inline-flex; align-items: center; justify-content: center; min-width: 40px; border-radius: 999px; background: #e8fff8; color: #129777; font-weight: 800; font-size: .72rem; padding: .2rem .45rem; border: 1px solid #c5f3e6; }

    .rqa-pos-tag { display: inline-block; font-weight: 700; color: #34465c; font-size: .7rem; }
    .rqa-school-tag { display: inline-block; font-weight: 700; color: #1f5f8b; font-size: .68rem; }
    .rqa-contact-line { display: block; font-size: .68rem; color: var(--rqa-text); line-height: 1.3; overflow-wrap: break-word; }
    .rqa-contact-line i { color: var(--rqa-muted); width: 13px; }
    .rqa-contact-line.muted { color: var(--rqa-muted); }

    .rqa-action-group { display: flex; gap: 6px; justify-content: center; flex-wrap: wrap; }
    .rqa-approve-btn, .rqa-decline-btn { font-size: .64rem; font-weight: 700; padding: .3rem .55rem; border-radius: 8px; white-space: nowrap; }

    .rqa-empty-state { display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; padding: 48px 20px; color: var(--rqa-muted); }
    .rqa-empty-state i { font-size: 46px; margin-bottom: 12px; color: #c4d2e3; }
    .rqa-empty-state strong { color: var(--rqa-text); font-size: .95rem; }
    .rqa-empty-state span { font-size: .82rem; margin-top: 4px; max-width: 460px; }
    .rqa-loading-box { padding: 48px 20px; }

    /* Filter selects (Select2) */
    .select2-container { width: 100% !important; }
    .select2-container--default .select2-selection--single { height: 38px; border-radius: 10px; border-color: #dfe7f1; display: flex; align-items: center; }
    .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 36px; color: #344054; padding-left: 12px; font-size: .84rem; }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 36px; right: 7px; }

    /* Multi-select filters */
    .select2-container--default .select2-selection--multiple { min-height: 38px; border-radius: 10px; border-color: #dfe7f1; padding: 1px 4px; }
    .select2-container--default.select2-container--focus .select2-selection--multiple,
    .select2-container--default.select2-container--open .select2-selection--multiple { border-color: var(--rqa-accent); box-shadow: 0 0 0 .15rem rgba(26,188,156,.13); }
    .select2-container--default .select2-selection--multiple .select2-selection__choice { background: var(--rqa-soft); border: 1px solid #dce8f7; color: var(--rqa-primary); border-radius: 6px; font-size: .76rem; font-weight: 700; margin-top: 5px; padding: 2px 8px; }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove { color: var(--rqa-primary); margin-right: 5px; }
    .select2-container--default .select2-selection--multiple .select2-search__field { margin-top: 7px; }
    .select2-container--default .select2-selection--multiple .select2-selection__placeholder { color: #98a2b3; margin-top: 6px; }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid rqa-page-shell">

            <div class="rqa-hero">
                <div class="rqa-hero-content">
                    <div class="rqa-title-block">
                        <h4><?= h($title ?? 'RQA Recommended Applicants - For Approval'); ?></h4>
                        <p>As the Approving Officer, review the applicants recommended by the HRMPSB. Use the filters to narrow the list, then Approve or Decline each recommendation.</p>
                    </div>
                    <div class="d-flex align-items-center" style="gap:12px;">
                        <span id="rqa-count-badge"></span>
                        <div class="rqa-hero-icon"><i class="mdi mdi-gavel"></i></div>
                    </div>
                </div>
            </div>

            <div class="card rqa-card rqa-filter-card">
                <div class="card-body">
                    <div class="rqa-section-head">
                        <h5 class="rqa-section-title"><i class="mdi mdi-filter-variant"></i> Filters</h5>
                        <span class="rqa-help-chip"><i class="mdi mdi-information-outline"></i> Select a position to show JHS or SHS filters</span>
                    </div>
                    <div class="form-row rqa-filter-grid">
                        <div class="form-group col-xl-3 col-lg-3 col-md-6">
                            <label for="position-filter">Position</label>
                            <select id="position-filter" class="form-control"><option value="">All Positions</option></select>
                        </div>
                        <div class="form-group col-xl-2 col-lg-2 col-md-6">
                            <label for="municipality-filter">Municipality</label>
                            <select id="municipality-filter" class="form-control" multiple="multiple"></select>
                        </div>
                        <div class="form-group col-xl-2 col-lg-2 col-md-6">
                            <label for="brgy-filter">Barangay</label>
                            <select id="brgy-filter" class="form-control" multiple="multiple" disabled></select>
                        </div>
                        <div class="form-group col-xl-2 col-lg-2 col-md-6 rqa-kind-filter" id="jhs-group-filter-wrap" style="display:none;">
                            <label for="jhs-group-filter">Specialization Group</label>
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
                        <h5 class="rqa-section-title"><i class="mdi mdi-account-check-outline"></i> Recommended Applicants</h5>
                    </div>

                    <div id="rqa-loading" class="text-center text-muted rqa-loading-box" style="display:none;">
                        <div class="spinner-border text-primary" role="status"></div>
                        <div class="mt-3 font-weight-bold">Loading recommended applicants…</div>
                    </div>

                    <div id="rqa-empty" class="rqa-empty-state">
                        <i class="mdi mdi-account-clock-outline"></i>
                        <strong>Loading…</strong>
                        <span>Please wait.</span>
                    </div>

                    <div class="rqa-results-wrap table-responsive" id="rqa-results" style="display:none;">
                        <table class="table table-hover table-bordered w-100" id="rqa-table">
                            <thead>
                                <tr>
                                    <th class="num">No.</th>
                                    <th>Applicant</th>
                                    <th>Municipality - Brgy</th>
                                    <th class="rqa-col-tribe" style="display:none;">Tribe</th>
                                    <th>Contact Info</th>
                                    <th>Position</th>
                                    <th>School</th>
                                    <th>Item No.</th>
                                    <th class="num">Educ</th>
                                    <th class="num">Train</th>
                                    <th class="num">Exp</th>
                                    <th class="num">LET</th>
                                    <th class="num">Demo</th>
                                    <th class="num">TRF</th>
                                    <th class="num">Total</th>
                                    <th>Remarks</th>
                                    <th class="num">Action</th>
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
    var dataUrl = '<?= base_url('Pages/rqa_approval_data'); ?>';
    var actionUrl = '<?= base_url('Pages/rqa_approval_action'); ?>';

    var allRows = [];

    var $pos = $('#position-filter');
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

    // Case-insensitive membership test for the multi-select filter values.
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

    function setSelectOptions($el, values, allLabel) {
        var html = '<option value="">' + allLabel + '</option>';
        values.forEach(function (v) { html += '<option value="' + escAttr(v) + '">' + escHtml(v) + '</option>'; });
        $el.html(html).val('').trigger('change.select2');
    }

    // Populate a multi-select; selection is cleared and the placeholder is shown.
    function setMultiOptions($el, values) {
        var html = '';
        values.forEach(function (v) { html += '<option value="' + escAttr(v) + '">' + escHtml(v) + '</option>'; });
        $el.html(html).val([]).trigger('change.select2');
    }

    $pos.select2({ width: '100%', placeholder: 'All Positions' });
    $mun.select2({ width: '100%', placeholder: 'All Municipalities' });
    $brgy.select2({ width: '100%', placeholder: 'All Barangays' });
    $jhsGroup.select2({ width: '100%', placeholder: 'All Groups' });
    $strand.select2({ width: '100%', placeholder: 'All Strands' });
    $major.select2({ width: '100%', placeholder: 'All Specializations' });

    function setEmptyState(icon, title, text) {
        $('#rqa-empty').html('<i class="mdi ' + icon + '"></i><strong>' + escHtml(title) + '</strong><span>' + escHtml(text) + '</span>').show();
    }

    function locationText(r) {
        var municipality = (r.municipality || '').trim();
        var brgy = (r.brgy || '').trim();

        if (municipality && brgy) return municipality + ' - ' + brgy;
        return municipality || brgy || '';
    }

    function contactHtml(r) {
        var html = '';
        html += '<span class="rqa-contact-line' + (r.contact ? '' : ' muted') + '"><i class="mdi mdi-phone"></i> ' + (r.contact ? escHtml(r.contact) : 'N/A') + '</span>';
        html += '<span class="rqa-contact-line' + (r.email ? '' : ' muted') + '"><i class="mdi mdi-email-outline"></i> ' + (r.email ? escHtml(r.email) : 'N/A') + '</span>';
        return html;
    }

    function rowSpecializationText(r) {
        if (r.specializationKind === 'jhs') return (r.specializationGroup || r.specialization || '').trim();
        if (r.specializationKind === 'shs') {
            var strand = (r.strand || '').trim();
            var major = (r.major || '').trim();
            if (strand && major) return strand + ' / ' + major;
            return strand || major || '';
        }
        return (r.specialization || '').trim();
    }

    function rowHtml(r, index) {
        var sub = 'Code: <span class="rqa-code-inline">' + escHtml(r.code) + '</span>';
        var specText = rowSpecializationText(r);
        if (specText) sub += '<span class="rqa-code-inline">' + escHtml(specText) + '</span>';

        var html = '<tr data-rec-id="' + r.recId + '">';
        html += '<td class="num"><span class="rqa-rank-badge">' + index + '</span></td>';
        html += '<td><span class="rqa-name-main">' + escHtml(r.name) + '</span><span class="rqa-name-sub">' + sub + '</span></td>';
        html += '<td><span class="rqa-location-tag">' + escHtml(locationText(r)) + '</span></td>';
        html += '<td class="rqa-col-tribe">' + (r.tribe ? escHtml(r.tribe) : '<span class="text-muted">—</span>') + '</td>';
        html += '<td>' + contactHtml(r) + '</td>';
        html += '<td><span class="rqa-pos-tag">' + escHtml(r.position) + '</span></td>';
        html += '<td>' + (r.school ? '<span class="rqa-school-tag">' + escHtml(r.school) + '</span>' : '<span class="text-muted">—</span>') + '</td>';
        html += '<td><span class="rqa-item-pill">' + escHtml(r.itemNumber) + '</span></td>';
        html += '<td class="num">' + escHtml(r.education) + '</td>';
        html += '<td class="num">' + escHtml(r.training) + '</td>';
        html += '<td class="num">' + escHtml(r.experience) + '</td>';
        html += '<td class="num">' + escHtml(r.let_rating) + '</td>';
        html += '<td class="num">' + escHtml(r.demo_rating) + '</td>';
        html += '<td class="num">' + escHtml(r.tr_rating) + '</td>';
        html += '<td class="num"><span class="rqa-total-pill">' + escHtml(r.total_points) + '</span></td>';
        html += '<td>' + escHtml(r.remarks) + '</td>';
        html += '<td class="num"><div class="rqa-action-group">'
            + '<button type="button" class="btn btn-success rqa-approve-btn" data-name="' + escAttr(r.name) + '" data-item="' + escAttr(r.itemNumber) + '"><i class="mdi mdi-check-bold mr-1"></i>Approve</button>'
            + '<button type="button" class="btn btn-outline-danger rqa-decline-btn" data-name="' + escAttr(r.name) + '" data-item="' + escAttr(r.itemNumber) + '"><i class="mdi mdi-close-thick mr-1"></i>Decline</button>'
            + '</div></td>';
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

    // Keep each position's applicants together, grouped by specialization and
    // then ranked by score (highest first) inside each group. JHS groups by
    // Specialization (A-Z); SHS groups by Strand then Specialization (A-Z).
    function sortRows(rows) {
        rows.sort(function (a, b) {
            var c = cmpText(a.position, b.position);
            if (c !== 0) return c;

            var kind = a.specializationKind || 'none';
            if (kind === 'jhs') {
                c = cmpText(a.specializationGroup || a.specialization, b.specializationGroup || b.specialization);
                if (c !== 0) return c;
            } else if (kind === 'shs') {
                c = cmpText(a.strand, b.strand);
                if (c !== 0) return c;
                c = cmpText(a.major, b.major);
                if (c !== 0) return c;
            }
            return rowScoreNum(b) - rowScoreNum(a);
        });
        return rows;
    }

    function getFilteredRows() {
        var pos = $pos.val() || '';
        var muns = $mun.val() || [];
        var brgys = $brgy.val() || [];
        var kind = selectedPositionKind();
        var jhsGroups = kind === 'jhs' ? ($jhsGroup.val() || []) : [];
        var strands = kind === 'shs' ? ($strand.val() || []) : [];
        var majors = kind === 'shs' ? ($major.val() || []) : [];

        return allRows.filter(function (r) {
            var total = parseFloat(r.total_points);
            if (isNaN(total) || total < 50) return false;
            if (pos !== '' && !eqi(r.position, pos)) return false;
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

        if (rows.length === 0) {
            $('#rqa-results').hide();
            if (allRows.length === 0) {
                setEmptyState('mdi-account-clock-outline', 'No applicants awaiting approval.', 'Recommended applicants from the HRMPSB will appear here for your approval.');
            } else {
                setEmptyState('mdi-filter-remove-outline', 'No applicants match the selected filters.', 'Try changing the position, municipality, barangay, or specialization filter.');
            }
        } else {
            var html = '';
            rows.forEach(function (r, i) { html += rowHtml(r, i + 1); });
            $tbody.html(html);
            $('#rqa-empty').hide();
            $('#rqa-results').show();
        }

        // The Tribe column only applies to IPED Elementary / Secondary; show it
        // when at least one recommended applicant belongs to such a position.
        $('#rqa-table .rqa-col-tribe').toggle(allRows.some(function (r) { return r.tribeApplicable; }));

        var badge = $('#rqa-count-badge');
        if (allRows.length > 0) {
            badge.text(rows.length + ' of ' + allRows.length + ' pending').show();
        } else {
            badge.hide();
        }
    }

    function rebuildFilters() {
        setSelectOptions($pos, distinct(allRows.map(function (r) { return r.position; })), 'All Positions');
        setMultiOptions($mun, distinct(allRows.map(function (r) { return r.municipality; })));
        setMultiOptions($brgy, []);
        $brgy.prop('disabled', true).trigger('change.select2');
        rebuildKindFilters();
    }

    function rebuildBarangays() {
        var selectedMuns = $mun.val() || [];
        if (selectedMuns.length === 0) {
            setMultiOptions($brgy, []);
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

        setMultiOptions($brgy, brgys);
        $brgy.prop('disabled', brgys.length === 0).trigger('change.select2');
    }

    function selectedPositionRows() {
        var pos = $pos.val() || '';
        if (pos === '') return [];
        return allRows.filter(function (r) {
            return eqi(r.position, pos);
        });
    }

    function selectedPositionKind() {
        var rows = selectedPositionRows();
        if (rows.length === 0) return 'none';

        var kind = rows[0].specializationKind || 'none';
        for (var i = 1; i < rows.length; i++) {
            if ((rows[i].specializationKind || 'none') !== kind) return 'mixed';
        }
        return kind;
    }

    function rebuildKindFilters() {
        var kind = selectedPositionKind();
        var rows = selectedPositionRows();

        $('.rqa-kind-filter').hide();
        setMultiOptions($jhsGroup, []);
        setMultiOptions($strand, []);
        setMultiOptions($major, []);

        if (kind === 'jhs') {
            setMultiOptions($jhsGroup, distinct(rows.map(function (r) {
                return r.specializationGroup || r.specialization;
            })));
            $jhsGroupWrap.show();
        } else if (kind === 'shs') {
            setMultiOptions($strand, distinct(rows.map(function (r) {
                return r.strand;
            })));
            setMultiOptions($major, distinct(rows.map(function (r) {
                return r.major;
            })));
            $strandWrap.show();
            $majorWrap.show();
        }
    }

    function loadData() {
        $('#rqa-empty').hide();
        $('#rqa-results').hide();
        $('#rqa-loading').show();

        $.getJSON(dataUrl).done(function (res) {
            $('#rqa-loading').hide();
            if (!res || res.status !== 'success') {
                setEmptyState('mdi-alert-circle-outline', 'Unable to load applicants.', (res && res.message) ? res.message : 'Please try again.');
                return;
            }
            allRows = res.rows || [];
            rebuildFilters();
            renderTable();
        }).fail(function () {
            $('#rqa-loading').hide();
            setEmptyState('mdi-wifi-off', 'Unable to load applicants.', 'Please check your connection and try again.');
        });
    }

    $pos.on('change', function () { rebuildKindFilters(); renderTable(); });
    $mun.on('change', function () { rebuildBarangays(); renderTable(); });
    $brgy.on('change', function () { renderTable(); });
    $jhsGroup.on('change', function () { renderTable(); });
    $strand.on('change', function () { renderTable(); });
    $major.on('change', function () { renderTable(); });

    function doAction($btn, action) {
        var $row = $btn.closest('tr');
        var recId = parseInt($row.data('rec-id'), 10);
        var isApprove = (action === 'approve');

        Swal.fire({
            title: isApprove ? 'Approve this applicant?' : 'Decline this recommendation?',
            html: 'Applicant: <strong>' + escHtml($btn.data('name')) + '</strong><br>Item Number: <strong>' + escHtml($btn.data('item')) + '</strong>'
                + (isApprove ? '' : '<br><small class="text-muted">Declining returns the applicant to the recommender and frees the item number.</small>'),
            icon: isApprove ? 'question' : 'warning',
            showCancelButton: true,
            confirmButtonText: isApprove ? 'Yes, approve' : 'Yes, decline',
            confirmButtonColor: isApprove ? '#1abc9c' : '#e74c3c'
        }).then(function (result) {
            if (!result.value) return;

            $row.find('.rqa-approve-btn, .rqa-decline-btn').prop('disabled', true);

            $.post(actionUrl, { rec_id: recId, action: action }, null, 'json').done(function (res) {
                if (res && res.status === 'success') {
                    Swal.fire({ icon: 'success', title: isApprove ? 'Approved' : 'Declined', text: res.message, timer: 1300, showConfirmButton: false });
                    allRows = allRows.filter(function (r) { return r.recId !== recId; });
                    $row.fadeOut(250, function () { rebuildFilters(); renderTable(); });
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: (res && res.message) ? res.message : 'Something went wrong.' });
                    $row.find('.rqa-approve-btn, .rqa-decline-btn').prop('disabled', false);
                }
            }).fail(function () {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Unable to process. Please try again.' });
                $row.find('.rqa-approve-btn, .rqa-decline-btn').prop('disabled', false);
            });
        });
    }

    $(document).on('click', '.rqa-approve-btn', function () { doAction($(this), 'approve'); });
    $(document).on('click', '.rqa-decline-btn', function () { doAction($(this), 'decline'); });

    loadData();
});
</script>
