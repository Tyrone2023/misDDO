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
    .rqa-date-input { height: 30px; font-size: .7rem; max-width: 150px; }
    .rqa-waived-badge { display: inline-block; font-weight: 800; font-size: .62rem; color: #b94a48; background: #fff0f0; border: 1px solid #f4c7c3; border-radius: 999px; padding: .12rem .45rem; margin-bottom: 4px; }
    .rqa-reused-note { display: inline-flex; align-items: center; gap: 4px; margin-top: 4px; font-size: .62rem; font-weight: 700; color: var(--rqa-muted); }
    .rqa-reused-note i { font-size: 13px; }

    .rqa-empty-state { display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; padding: 48px 20px; color: var(--rqa-muted); }
    .rqa-empty-state i { font-size: 46px; margin-bottom: 12px; color: #c4d2e3; }
    .rqa-empty-state strong { color: var(--rqa-text); font-size: .95rem; }
    .rqa-empty-state span { font-size: .82rem; margin-top: 4px; max-width: 460px; }
    .rqa-loading-box { padding: 48px 20px; }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid rqa-page-shell">

            <div class="rqa-hero">
                <div class="rqa-hero-content">
                    <div class="rqa-title-block">
                        <h4><?= h($title ?? 'List of Issuance'); ?></h4>
                        <p>Approved applicants ready for issuance. Set the Date Hired, and if an applicant waives the position, mark it Waived and record the Date Waived.</p>
                    </div>
                    <div class="d-flex align-items-center" style="gap:12px;">
                        <span id="rqa-count-badge"></span>
                        <button type="button" id="rqa-report-btn" class="btn btn-light btn-sm font-weight-bold" style="border-radius:10px;">
                            <i class="mdi mdi-printer mr-1"></i>Report
                        </button>
                        <div class="rqa-hero-icon"><i class="mdi mdi-file-document-edit-outline"></i></div>
                    </div>
                </div>
            </div>

            <div class="card rqa-card rqa-filter-card">
                <div class="card-body">
                    <div class="rqa-section-head">
                        <h5 class="rqa-section-title"><i class="mdi mdi-filter-variant"></i> Filters</h5>
                    </div>
                    <div class="form-row rqa-filter-grid">
                        <div class="form-group col-xl-6 col-lg-6 col-md-6">
                            <label for="position-filter">Position</label>
                            <select id="position-filter" class="form-control"><option value="">All Positions</option></select>
                        </div>
                        <div class="form-group col-xl-6 col-lg-6 col-md-6">
                            <label for="school-filter">School Assigned</label>
                            <select id="school-filter" class="form-control"><option value="">All Schools</option></select>
                        </div>
                        <!-- <div class="form-group col-xl-4 col-lg-4 col-md-6">
                            <label for="status-filter">Status</label>
                            <select id="status-filter" class="form-control">
                                <option value="">All</option>
                                <option value="hired">Hired (with date)</option>
                                <option value="waived">Waived</option>
                                <option value="pending">Pending (no date)</option>
                            </select>
                        </div> -->
                    </div>
                </div>
            </div>

            <div class="card rqa-card rqa-results-card">
                <div class="card-body">
                    <div class="rqa-results-header">
                        <h5 class="rqa-section-title"><i class="mdi mdi-file-document-edit-outline"></i> List of Issuance</h5>
                    </div>

                    <div id="rqa-issuance-loading" class="text-center text-muted rqa-loading-box" style="display:none;">
                        <div class="spinner-border text-primary" role="status"></div>
                        <div class="mt-3 font-weight-bold">Loading list of issuance…</div>
                    </div>

                    <div id="rqa-issuance-empty" class="rqa-empty-state">
                        <i class="mdi mdi-file-document-outline"></i>
                        <strong>Loading…</strong>
                        <span>Please wait.</span>
                    </div>

                    <div class="rqa-results-wrap table-responsive" id="rqa-issuance-results" style="display:none;">
                        <table class="table table-hover table-bordered w-100" id="rqa-issuance-table">
                            <thead>
                                <tr>
                                    <th class="num">No.</th>
                                    <th>Applicant</th>
                                    <th>Complete Address</th>
                                    <th class="rqa-col-tribe" style="display:none;">Tribe</th>
                                    <th>Contact Info</th>
                                    <th>Position</th>
                                    <th>Item No.</th>
                                    <th>School Assigned</th>
                                    <th>Date Hired</th>
                                    <th>Waiver</th>
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
    var issuanceDataUrl = '<?= base_url('Pages/rqa_issuance_data'); ?>';
    var issuanceUpdateUrl = '<?= base_url('Pages/rqa_issuance_update'); ?>';
    var issuanceWaiveUrl = '<?= base_url('Pages/rqa_issuance_waive'); ?>';
    var issuanceUnwaiveUrl = '<?= base_url('Pages/rqa_issuance_unwaive'); ?>';
    var reportPageUrl = '<?= base_url('Pages/rqa_issuance_report'); ?>';

    var issuanceRows = [];

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

    function setSelectOptions($el, values, allLabel) {
        var html = '<option value="">' + allLabel + '</option>';
        values.forEach(function (v) { html += '<option value="' + escAttr(v) + '">' + escHtml(v) + '</option>'; });
        $el.html(html).val('').trigger('change.select2');
    }

    $pos.select2({ width: '100%', placeholder: 'All Positions' });
    $school.select2({ width: '100%', placeholder: 'All Schools' });
    $status.select2({ width: '100%', minimumResultsForSearch: Infinity });

    function todayStr() {
        var d = new Date();
        var m = ('0' + (d.getMonth() + 1)).slice(-2);
        var day = ('0' + d.getDate()).slice(-2);
        return d.getFullYear() + '-' + m + '-' + day;
    }

    function setEmptyState(icon, title, text) {
        $('#rqa-issuance-empty').html('<i class="mdi ' + icon + '"></i><strong>' + escHtml(title) + '</strong><span>' + escHtml(text) + '</span>').show();
    }

    function locationText(r) {
        var completeAddress = (r.completeAddress || '').trim();
        if (completeAddress) return completeAddress;

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

    function waiverCellHtml(r) {
        var isWaived = (r.status === 'waived') || !!r.dateWaived;
        if (isWaived) {
            var html = '<span class="rqa-waived-badge"><i class="mdi mdi-cancel"></i> Waived</span>'
                + '<input type="date" class="form-control form-control-sm rqa-date-input rqa-date-waived" value="' + escAttr(r.dateWaived) + '">';
            if (r.canUndo) {
                html += '<button type="button" class="btn btn-sm btn-link rqa-unwaive-btn p-0 mt-1"><i class="mdi mdi-undo-variant mr-1"></i>Undo waiver</button>';
            } else {
                html += '<span class="rqa-reused-note" title="The Item Number has been re-assigned to another applicant, so this waiver can no longer be undone."><i class="mdi mdi-lock-outline"></i> Item No. reused</span>';
            }
            return html;
        }
        return '<button type="button" class="btn btn-sm btn-outline-danger rqa-waive-btn"><i class="mdi mdi-cancel mr-1"></i>Mark Waived</button>';
    }

    function issuanceRowHtml(r, index) {
        var html = '<tr data-rec-id="' + r.recId + '">';
        html += '<td class="num"><span class="rqa-rank-badge">' + index + '</span></td>';
        html += '<td><span class="rqa-name-main">' + escHtml(r.name) + '</span><span class="rqa-name-sub">Code: <span class="rqa-code-inline">' + escHtml(r.code) + '</span></span></td>';
        html += '<td><span class="rqa-location-tag">' + escHtml(locationText(r)) + '</span></td>';
        html += '<td class="rqa-col-tribe">' + (r.tribe ? escHtml(r.tribe) : '<span class="text-muted">—</span>') + '</td>';
        html += '<td>' + contactHtml(r) + '</td>';
        html += '<td><span class="rqa-pos-tag">' + escHtml(r.position) + '</span></td>';
        html += '<td><span class="rqa-item-pill">' + escHtml(r.itemNumber) + '</span></td>';
        html += '<td>' + (r.school ? '<span class="rqa-school-tag">' + escHtml(r.school) + '</span>' : '<span class="text-muted">—</span>') + '</td>';
        html += '<td><input type="date" class="form-control form-control-sm rqa-date-input rqa-date-hired" value="' + escAttr(r.dateHired) + '"></td>';
        html += '<td class="rqa-waiver-cell">' + waiverCellHtml(r) + '</td>';
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
                setEmptyState('mdi-file-document-outline', 'No issuance records yet.', 'Approved applicants will appear here for issuance, date hired, and waiver.');
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

        // Tribe applies only to IPED Elementary / Secondary; show the column
        // when at least one approved applicant belongs to such a position.
        $('#rqa-issuance-table .rqa-col-tribe').toggle(issuanceRows.some(function (r) { return r.tribeApplicable; }));

        var badge = $('#rqa-count-badge');
        if (issuanceRows.length > 0) {
            badge.text(rows.length + ' of ' + issuanceRows.length + ' approved').show();
        } else {
            badge.hide();
        }
    }

    function rebuildFilters() {
        setSelectOptions($pos, distinct(issuanceRows.map(function (r) { return r.position; })), 'All Positions');
        setSelectOptions($school, distinct(issuanceRows.map(function (r) { return r.school; })), 'All Schools');
    }

    function loadIssuance() {
        $('#rqa-issuance-empty').hide();
        $('#rqa-issuance-results').hide();
        $('#rqa-issuance-loading').show();

        $.getJSON(issuanceDataUrl).done(function (res) {
            $('#rqa-issuance-loading').hide();
            if (!res || res.status !== 'success') {
                issuanceRows = [];
                setEmptyState('mdi-alert-circle-outline', 'Unable to load records.', (res && res.message) ? res.message : 'Please try again.');
                return;
            }
            issuanceRows = res.rows || [];
            rebuildFilters();
            renderIssuance();
        }).fail(function () {
            $('#rqa-issuance-loading').hide();
            issuanceRows = [];
            setEmptyState('mdi-wifi-off', 'Unable to load records.', 'Please check your connection and try again.');
        });
    }

    function saveIssuanceField(recId, field, value, $input) {
        $input.prop('disabled', true);
        $.post(issuanceUpdateUrl, { rec_id: recId, field: field, value: value }, null, 'json').done(function (res) {
            $input.prop('disabled', false);
            if (res && res.status === 'success') {
                var row = issuanceRows.filter(function (r) { return r.recId === recId; })[0];
                if (row) { row[field === 'date_hired' ? 'dateHired' : 'dateWaived'] = value; }
                Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: res.message, timer: 1200, showConfirmButton: false });
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: (res && res.message) ? res.message : 'Unable to save.' });
            }
        }).fail(function () {
            $input.prop('disabled', false);
            Swal.fire({ icon: 'error', title: 'Error', text: 'Unable to save. Please try again.' });
        });
    }

    $pos.on('change', function () { renderIssuance(); });
    $school.on('change', function () { renderIssuance(); });
    $status.on('change', function () { renderIssuance(); });

    $(document).on('change', '.rqa-date-hired', function () {
        var $input = $(this);
        var recId = parseInt($input.closest('tr').data('rec-id'), 10);
        saveIssuanceField(recId, 'date_hired', $input.val(), $input);
    });

    $(document).on('change', '.rqa-date-waived', function () {
        var $input = $(this);
        var recId = parseInt($input.closest('tr').data('rec-id'), 10);
        saveIssuanceField(recId, 'date_waived', $input.val(), $input);
    });

    $(document).on('click', '.rqa-waive-btn', function () {
        var $btn = $(this);
        var $row = $btn.closest('tr');
        var recId = parseInt($row.data('rec-id'), 10);

        Swal.fire({
            title: 'Mark this applicant as Waived?',
            text: 'This records that the applicant waived the position and frees its Item Number for reuse. You can undo this afterwards.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, mark waived',
            confirmButtonColor: '#e74c3c'
        }).then(function (result) {
            if (!result.value) return;
            var waivedDate = todayStr();
            $btn.prop('disabled', true);
            $.post(issuanceWaiveUrl, { rec_id: recId, value: waivedDate }, null, 'json').done(function (res) {
                if (res && res.status === 'success') {
                    var row = issuanceRows.filter(function (r) { return r.recId === recId; })[0];
                    if (row) { row.status = 'waived'; row.dateWaived = waivedDate; row.canUndo = true; }
                    $row.find('.rqa-waiver-cell').html(waiverCellHtml({ status: 'waived', dateWaived: waivedDate, canUndo: true }));
                    Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: res.message || 'Marked as waived.', timer: 1600, showConfirmButton: false });
                } else {
                    $btn.prop('disabled', false);
                    Swal.fire({ icon: 'error', title: 'Error', text: (res && res.message) ? res.message : 'Unable to save.' });
                }
            }).fail(function () {
                $btn.prop('disabled', false);
                Swal.fire({ icon: 'error', title: 'Error', text: 'Unable to save. Please try again.' });
            });
        });
    });

    $(document).on('click', '.rqa-unwaive-btn', function () {
        var $btn = $(this);
        var $row = $btn.closest('tr');
        var recId = parseInt($row.data('rec-id'), 10);

        Swal.fire({
            title: 'Undo this waiver?',
            text: 'The applicant returns to the List of Issuance and the Item Number is assigned to them again.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, undo waiver',
            confirmButtonColor: '#1abc9c'
        }).then(function (result) {
            if (!result.value) return;
            $btn.prop('disabled', true);
            $.post(issuanceUnwaiveUrl, { rec_id: recId }, null, 'json').done(function (res) {
                if (res && res.status === 'success') {
                    var row = issuanceRows.filter(function (r) { return r.recId === recId; })[0];
                    if (row) { row.status = 'approved'; row.dateWaived = ''; row.canUndo = false; }
                    $row.find('.rqa-waiver-cell').html(waiverCellHtml({ status: 'approved', dateWaived: '' }));
                    Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: res.message || 'Waiver undone.', timer: 1600, showConfirmButton: false });
                } else {
                    $btn.prop('disabled', false);
                    Swal.fire({ icon: 'error', title: 'Cannot undo waiver', text: (res && res.message) ? res.message : 'Unable to undo.' });
                }
            }).fail(function () {
                $btn.prop('disabled', false);
                Swal.fire({ icon: 'error', title: 'Error', text: 'Unable to undo. Please try again.' });
            });
        });
    });

    // Open the printable report page, carrying the selected Position so the
    // report opens pre-filtered (Status defaults to Hired & Waived there).
    $('#rqa-report-btn').on('click', function () {
        var pos = ($pos.val() || '').trim();
        var url = reportPageUrl + (pos ? ('?position=' + encodeURIComponent(pos)) : '');
        window.location.href = url;
    });

    loadIssuance();
});
</script>
