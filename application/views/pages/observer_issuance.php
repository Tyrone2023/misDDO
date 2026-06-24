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

    .rqa-results-card .card-body { padding: 0; }
    .rqa-results-header { padding: 16px 18px; border-bottom: 1px solid var(--rqa-border); background: #fff; }
    .rqa-results-wrap { background: #fff; width: 100%; }

    #rqa-issuance-table { width: 100%; margin-bottom: 0; border-collapse: collapse; }
    #rqa-issuance-table thead th { background: #f8fbff; border-top: 0; border-bottom: 1px solid var(--rqa-border); color: #516070; font-size: .64rem; text-transform: uppercase; letter-spacing: .35px; white-space: normal; padding: 9px 6px; font-weight: 800; line-height: 1.2; }
    #rqa-issuance-table td { vertical-align: middle; font-size: .74rem; color: var(--rqa-text); padding: 8px 6px; border-color: #eef2f7; background: #fff; line-height: 1.25; }
    #rqa-issuance-table tbody tr { transition: background .18s ease; }
    #rqa-issuance-table tbody tr:hover td { background: #fbfdff; }
    #rqa-issuance-table td.num, #rqa-issuance-table th.num { text-align: center; }

    .rqa-name-main { display: block; font-weight: 800; color: #223047; font-size: .76rem; line-height: 1.2; overflow-wrap: break-word; }
    .rqa-name-sub { display: flex; align-items: center; gap: 6px; margin-top: 4px; font-size: .66rem; color: var(--rqa-muted); flex-wrap: wrap; }
    .rqa-code-inline { display: inline-flex; align-items: center; border-radius: 999px; background: #f2f6fb; color: #46566a; font-weight: 800; font-size: .64rem; padding: .12rem .4rem; border: 1px solid #e5ecf5; }
    .rqa-location-tag { display: block; font-weight: 700; color: #34465c; font-size: .7rem; line-height: 1.25; overflow-wrap: break-word; }

    .rqa-rank-badge { display: inline-flex; align-items: center; justify-content: center; min-width: 24px; height: 24px; border-radius: 8px; background: var(--rqa-soft); color: var(--rqa-primary); font-weight: 800; font-size: .68rem; }
    .rqa-item-pill { display: inline-flex; align-items: center; border-radius: 8px; background: #fff6e9; color: #9a6b16; font-weight: 800; font-size: .68rem; padding: .2rem .45rem; border: 1px solid #f1ddb9; }

    .rqa-pos-tag { display: inline-block; font-weight: 700; color: #34465c; font-size: .7rem; }
    .rqa-school-tag { display: inline-block; font-weight: 700; color: #1f5f8b; font-size: .68rem; }
    .rqa-contact-line { display: block; font-size: .68rem; color: var(--rqa-text); }
    .rqa-contact-line i { color: var(--rqa-muted); width: 13px; }
    .rqa-contact-line.muted { color: var(--rqa-muted); }
    .rqa-date-tag { display: inline-flex; align-items: center; gap: 5px; font-weight: 700; color: #2c5d3f; background: #ecfbf2; border: 1px solid #c9eed6; border-radius: 8px; font-size: .68rem; padding: .15rem .45rem; }
    .rqa-date-tag i { font-size: 13px; }
    .rqa-waived-badge { display: inline-flex; align-items: center; gap: 4px; font-weight: 800; font-size: .62rem; color: #b94a48; background: #fff0f0; border: 1px solid #f4c7c3; border-radius: 999px; padding: .12rem .45rem; }
    .rqa-status-pill { display: inline-flex; align-items: center; gap: 4px; font-weight: 800; font-size: .62rem; border-radius: 999px; padding: .12rem .5rem; }
    .rqa-status-hired { color: #129777; background: #e8fff8; border: 1px solid #c5f3e6; }
    .rqa-status-pending { color: #9a6b16; background: #fff6e9; border: 1px solid #f1ddb9; }

    .rqa-empty-state { display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; padding: 48px 20px; color: var(--rqa-muted); }
    .rqa-empty-state i { font-size: 46px; margin-bottom: 12px; color: #c4d2e3; }
    .rqa-empty-state strong { color: var(--rqa-text); font-size: .95rem; }
    .rqa-empty-state span { font-size: .82rem; margin-top: 4px; max-width: 460px; }
    .rqa-loading-box { padding: 48px 20px; }

    .select2-container { width: 100% !important; }
    .select2-container--default .select2-selection--single { height: 38px; border-radius: 10px; border-color: #dfe7f1; display: flex; align-items: center; }
    .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 36px; color: #344054; padding-left: 12px; font-size: .84rem; }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 36px; right: 7px; }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid rqa-page-shell">

            <div class="rqa-hero">
                <div class="rqa-hero-content">
                    <div class="rqa-title-block">
                        <!-- <h4><?= h($title ?? 'List of Issuance (Observer)'); ?></h4> -->
                         <h4>RQA Issuance</h4>
                        <p>Read-only view of approved applicants for issuance. Date Hired and Waiver status update on their own as the issuing officer records them &mdash; no need to refresh.</p>
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
                    </div>
                    <div class="form-row rqa-filter-grid">
                        <div class="form-group col-xl-4 col-lg-4 col-md-6">
                            <label for="position-filter">Position</label>
                            <select id="position-filter" class="form-control"><option value="">All Positions</option></select>
                        </div>
                        <div class="form-group col-xl-4 col-lg-4 col-md-6">
                            <label for="school-filter">School Assigned</label>
                            <select id="school-filter" class="form-control"><option value="">All Schools</option></select>
                        </div>
                        <div class="form-group col-xl-4 col-lg-4 col-md-6">
                            <label for="status-filter">Status</label>
                            <select id="status-filter" class="form-control">
                                <option value="">All</option>
                                <option value="hired">Hired (with date)</option>
                                <option value="pending">Pending (no date)</option>
                                <option value="waived">Waived</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card rqa-card rqa-results-card">
                <div class="card-body">
                    <div class="rqa-results-header">
                        <h5 class="rqa-section-title"><i class="mdi mdi-file-document-outline"></i> List of Issuance</h5>
                    </div>

                    <div id="rqa-issuance-loading" class="text-center text-muted rqa-loading-box" style="display:none;">
                        <div class="spinner-border text-primary" role="status"></div>
                        <div class="mt-3 font-weight-bold">Loading list of issuance&hellip;</div>
                    </div>

                    <div id="rqa-issuance-empty" class="rqa-empty-state">
                        <i class="mdi mdi-file-document-outline"></i>
                        <strong>Loading&hellip;</strong>
                        <span>Please wait.</span>
                    </div>

                    <div class="rqa-results-wrap table-responsive" id="rqa-issuance-results" style="display:none;">
                        <table class="table table-hover table-bordered w-100" id="rqa-issuance-table">
                            <thead>
                                <tr>
                                    <th class="num">No.</th>
                                    <th>Applicant</th>
                                    <th>Municipality - Brgy</th>
                                    <th class="rqa-col-tribe" style="display:none;">Tribe</th>
                                    <th>Contact Info</th>
                                    <th>Position</th>
                                    <th>Item No.</th>
                                    <th>School Assigned</th>
                                    <th>Date Hired</th>
                                    <th>Status</th>
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
    var issuanceDataUrl = '<?= base_url('Pages/observer_issuance_data'); ?>';
    var POLL_MS = 5000;

    var issuanceRows = [];
    var firstLoad = true;

    var $pos = $('#position-filter');
    var $school = $('#school-filter');
    var $status = $('#status-filter');

    function escHtml(s) { return String(s == null ? '' : s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;'); }
    function escAttr(s) { return escHtml(s).replace(/"/g, '&quot;'); }
    function eqi(a, b) { return (a || '').trim().toLowerCase() === (b || '').trim().toLowerCase(); }

    function distinct(arr) {
        var seen = {}, out = [];
        arr.forEach(function (v) {
            v = (v || '').trim();
            if (v !== '' && !seen[v.toLowerCase()]) { seen[v.toLowerCase()] = true; out.push(v); }
        });
        out.sort(function (a, b) { return a.localeCompare(b); });
        return out;
    }

    // Rebuild a single-select's options; `keep` retains the current value if it
    // still exists (used while polling) so the observer's filter is preserved.
    function setSelectOptions($el, values, allLabel, keep) {
        var prev = keep ? ($el.val() || '') : '';
        var valid = (prev === '') || values.some(function (v) { return v === prev; });
        var html = '<option value="">' + allLabel + '</option>';
        values.forEach(function (v) { html += '<option value="' + escAttr(v) + '">' + escHtml(v) + '</option>'; });
        $el.html(html).val(valid ? prev : '').trigger('change.select2');
    }

    $pos.select2({ width: '100%', placeholder: 'All Positions' });
    $school.select2({ width: '100%', placeholder: 'All Schools' });
    $status.select2({ width: '100%', minimumResultsForSearch: Infinity });

    function setEmptyState(icon, title, text) {
        $('#rqa-issuance-empty').html('<i class="mdi ' + icon + '"></i><strong>' + escHtml(title) + '</strong><span>' + escHtml(text) + '</span>').show();
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

    function statusCellHtml(r) {
        var isWaived = (r.status === 'waived') || !!r.dateWaived;
        if (isWaived) {
            var html = '<span class="rqa-waived-badge"><i class="mdi mdi-cancel"></i> Waived</span>';
            if (r.dateWaived) html += '<div class="rqa-contact-line muted mt-1">' + escHtml(r.dateWaived) + '</div>';
            return html;
        }
        if (r.dateHired) return '<span class="rqa-status-pill rqa-status-hired"><i class="mdi mdi-check-bold"></i> Hired</span>';
        return '<span class="rqa-status-pill rqa-status-pending"><i class="mdi mdi-clock-outline"></i> Pending</span>';
    }

    function dateHiredHtml(r) {
        if (r.dateHired) return '<span class="rqa-date-tag"><i class="mdi mdi-calendar-check"></i> ' + escHtml(r.dateHired) + '</span>';
        return '<span class="text-muted">&mdash;</span>';
    }

    function issuanceRowHtml(r, index) {
        var html = '<tr data-rec-id="' + r.recId + '">';
        html += '<td class="num"><span class="rqa-rank-badge">' + index + '</span></td>';
        html += '<td><span class="rqa-name-main">' + escHtml(r.name) + '</span><span class="rqa-name-sub">Code: <span class="rqa-code-inline">' + escHtml(r.code) + '</span></span></td>';
        html += '<td><span class="rqa-location-tag">' + escHtml(locationText(r)) + '</span></td>';
        html += '<td class="rqa-col-tribe">' + (r.tribe ? escHtml(r.tribe) : '<span class="text-muted">&mdash;</span>') + '</td>';
        html += '<td>' + contactHtml(r) + '</td>';
        html += '<td><span class="rqa-pos-tag">' + escHtml(r.position) + '</span></td>';
        html += '<td><span class="rqa-item-pill">' + escHtml(r.itemNumber) + '</span></td>';
        html += '<td>' + (r.school ? '<span class="rqa-school-tag">' + escHtml(r.school) + '</span>' : '<span class="text-muted">&mdash;</span>') + '</td>';
        html += '<td>' + dateHiredHtml(r) + '</td>';
        html += '<td>' + statusCellHtml(r) + '</td>';
        html += '</tr>';
        return html;
    }

    function getFilteredRows() {
        var pos = $pos.val() || '';
        var school = $school.val() || '';
        var status = $status.val() || '';
        return issuanceRows.filter(function (r) {
            if (pos !== '' && !eqi(r.position, pos)) return false;
            if (school !== '' && !eqi(r.school, school)) return false;
            var isWaived = (r.status === 'waived') || !!r.dateWaived;
            if (status === 'hired' && (isWaived || !r.dateHired)) return false;
            if (status === 'waived' && !isWaived) return false;
            if (status === 'pending' && (isWaived || r.dateHired)) return false;
            return true;
        });
    }

    function renderIssuance() {
        var rows = getFilteredRows();
        var $tbody = $('#rqa-issuance-table tbody');

        if (rows.length === 0) {
            $('#rqa-issuance-results').hide();
            if (issuanceRows.length === 0) {
                setEmptyState('mdi-file-document-outline', 'No issuance records yet.', 'Approved applicants will appear here automatically for issuance.');
            } else {
                setEmptyState('mdi-filter-remove-outline', 'No records match the selected filters.', 'Try changing the position, school, or status filter.');
            }
        } else {
            var html = '';
            rows.forEach(function (r, i) { html += issuanceRowHtml(r, i + 1); });
            $tbody.html(html);
            $('#rqa-issuance-empty').hide();
            $('#rqa-issuance-results').show();
        }

        $('#rqa-issuance-table .rqa-col-tribe').toggle(issuanceRows.some(function (r) { return r.tribeApplicable; }));

        var badge = $('#rqa-count-badge');
        if (issuanceRows.length > 0) {
            badge.text(rows.length + ' of ' + issuanceRows.length + ' records').show();
        } else {
            badge.hide();
        }
    }

    function syncFilters(keep) {
        setSelectOptions($pos, distinct(issuanceRows.map(function (r) { return r.position; })), 'All Positions', keep);
        setSelectOptions($school, distinct(issuanceRows.map(function (r) { return r.school; })), 'All Schools', keep);
    }

    function refreshData(isPoll) {
        if (!isPoll) {
            $('#rqa-issuance-empty').hide();
            $('#rqa-issuance-results').hide();
            $('#rqa-issuance-loading').show();
        }

        $.getJSON(issuanceDataUrl).done(function (res) {
            $('#rqa-issuance-loading').hide();
            if (!res || res.status !== 'success') {
                if (!isPoll) { issuanceRows = []; setEmptyState('mdi-alert-circle-outline', 'Unable to load records.', (res && res.message) ? res.message : 'Please try again.'); }
                return;
            }
            issuanceRows = res.rows || [];
            syncFilters(!firstLoad);
            firstLoad = false;
            renderIssuance();
        }).fail(function () {
            $('#rqa-issuance-loading').hide();
            if (!isPoll) { issuanceRows = []; setEmptyState('mdi-wifi-off', 'Unable to load records.', 'Please check your connection and try again.'); }
        });
    }

    $pos.on('change', function () { renderIssuance(); });
    $school.on('change', function () { renderIssuance(); });
    $status.on('change', function () { renderIssuance(); });

    refreshData(false);
    setInterval(function () { refreshData(true); }, POLL_MS);
});
</script>
