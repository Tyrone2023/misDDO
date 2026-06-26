<?php
if (!function_exists('h')) {
    function h($v)
    {
        return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
    }
}
$scope = ($scope ?? 'mine') === 'all' ? 'all' : 'mine';
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

    /* Scope toggle (My / All) */
    .rqa-scope-toggle { display: inline-flex; background: rgba(255,255,255,.14); border: 1px solid rgba(255,255,255,.20); border-radius: 999px; padding: 3px; }
    .rqa-scope-btn { border: 0; background: transparent; color: rgba(255,255,255,.85); font-weight: 800; font-size: .74rem; padding: .32rem .7rem; border-radius: 999px; white-space: nowrap; }
    .rqa-scope-btn.active { background: #fff; color: var(--rqa-primary); }

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

    #rqa-list-table { width: 100%; margin-bottom: 0; border-collapse: collapse; }
    #rqa-list-table thead th { background: #f8fbff; border-top: 0; border-bottom: 1px solid var(--rqa-border); color: #516070; font-size: .64rem; text-transform: uppercase; letter-spacing: .35px; white-space: normal; padding: 9px 6px; font-weight: 800; line-height: 1.2; }
    #rqa-list-table td { vertical-align: middle; font-size: .74rem; color: var(--rqa-text); padding: 8px 6px; border-color: #eef2f7; background: #fff; line-height: 1.25; }
    #rqa-list-table tbody tr { transition: background .18s ease; }
    #rqa-list-table tbody tr:hover td { background: #fbfdff; }
    #rqa-list-table td.num, #rqa-list-table th.num { text-align: center; }

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

    .rqa-recby-main { display: block; font-weight: 700; color: #34465c; font-size: .7rem; line-height: 1.2; overflow-wrap: break-word; }
    .rqa-recby-sub { display: block; margin-top: 2px; font-size: .62rem; color: var(--rqa-muted); }
    .rqa-time-main { display: block; font-weight: 800; color: #223047; font-size: .68rem; }
    .rqa-time-sub { display: block; margin-top: 2px; font-size: .62rem; color: var(--rqa-muted); }

    .rqa-status-badge { display: inline-flex; align-items: center; gap: 4px; font-size: .6rem; font-weight: 800; text-transform: uppercase; letter-spacing: .3px; padding: .18rem .45rem; border-radius: 999px; }
    .rqa-status-recommended { background: #fff6e9; color: #9a6b16; border: 1px solid #f1ddb9; }
    .rqa-status-approved { background: #e8fff3; color: #0f9d6b; border: 1px solid #b8f0d6; }
    .rqa-status-waived { background: #fdecec; color: #c0392b; border: 1px solid #f5c6c2; }

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
                        <h4><?= h($title ?? 'RQA Recommended List'); ?></h4>
                        <p id="rqa-hero-sub">Recommended applicants with their position, item number, school, remarks, and who recommended them.</p>
                    </div>
                    <div class="d-flex align-items-center flex-wrap" style="gap:12px;">
                        <span id="rqa-count-badge"></span>
                        <div class="rqa-scope-toggle">
                            <button type="button" class="rqa-scope-btn<?= $scope === 'mine' ? ' active' : ''; ?>" data-scope="mine"><i class="mdi mdi-account-check mr-1"></i>My Recommended</button>
                            <button type="button" class="rqa-scope-btn<?= $scope === 'all' ? ' active' : ''; ?>" data-scope="all"><i class="mdi mdi-format-list-bulleted mr-1"></i>All Recommended</button>
                        </div>
                        <a href="<?= base_url('Pages/rqa_recommendation'); ?>" class="btn btn-light btn-sm font-weight-bold" style="border-radius:10px;">
                            <i class="mdi mdi-arrow-left mr-1"></i>Back
                        </a>
                        <div class="rqa-hero-icon"><i class="mdi mdi-clipboard-account-outline"></i></div>
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
                    </div>
                </div>
            </div>

            <div class="card rqa-card rqa-results-card">
                <div class="card-body">
                    <div class="rqa-results-header">
                        <h5 class="rqa-section-title"><i class="mdi mdi-clipboard-account-outline"></i> Recommended Applicants</h5>
                    </div>

                    <div id="rqa-list-loading" class="text-center text-muted rqa-loading-box" style="display:none;">
                        <div class="spinner-border text-primary" role="status"></div>
                        <div class="mt-3 font-weight-bold">Loading recommended list…</div>
                    </div>

                    <div id="rqa-list-empty" class="rqa-empty-state">
                        <i class="mdi mdi-clipboard-text-outline"></i>
                        <strong>Loading…</strong>
                        <span>Please wait.</span>
                    </div>

                    <div class="rqa-results-wrap table-responsive" id="rqa-list-results" style="display:none;">
                        <table class="table table-hover table-bordered w-100" id="rqa-list-table">
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
                                    <th>Remarks</th>
                                    <th>Status</th>
                                    <th>Recommended By</th>
                                    <th>Recommended On</th>
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
    var dataUrl = '<?= base_url('Pages/rqa_recommended_list_data'); ?>';
    var scope = '<?= $scope; ?>';

    var listRows = [];

    var $pos = $('#position-filter');
    var $school = $('#school-filter');

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

    function setEmptyState(icon, title, text) {
        $('#rqa-list-empty').html('<i class="mdi ' + icon + '"></i><strong>' + escHtml(title) + '</strong><span>' + escHtml(text) + '</span>').show();
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

    var MONTHS = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

    // Parse a 'YYYY-MM-DD HH:MM:SS' timestamp into a friendly date + time.
    function recommendedOnHtml(raw) {
        raw = (raw || '').trim();
        if (raw === '' || raw.indexOf('0000-00-00') === 0) return '<span class="text-muted">—</span>';

        var m = raw.match(/^(\d{4})-(\d{2})-(\d{2})(?:[ T](\d{2}):(\d{2}))?/);
        if (!m) return '<span class="rqa-time-main">' + escHtml(raw) + '</span>';

        var year = +m[1], mon = +m[2] - 1, day = +m[3];
        var dateStr = (MONTHS[mon] || '') + ' ' + day + ', ' + year;

        var timeStr = '';
        if (m[4] != null) {
            var hh = +m[4], mm = m[5];
            var ampm = hh >= 12 ? 'PM' : 'AM';
            var h12 = hh % 12; if (h12 === 0) h12 = 12;
            timeStr = h12 + ':' + mm + ' ' + ampm;
        }

        return '<span class="rqa-time-main">' + escHtml(dateStr) + '</span>'
            + (timeStr ? '<span class="rqa-time-sub"><i class="mdi mdi-clock-outline"></i> ' + escHtml(timeStr) + '</span>' : '');
    }

    function statusBadge(status) {
        status = (status || '').trim().toLowerCase();
        if (status === 'approved') return '<span class="rqa-status-badge rqa-status-approved"><i class="mdi mdi-check-decagram"></i>Approved</span>';
        if (status === 'waived') return '<span class="rqa-status-badge rqa-status-waived"><i class="mdi mdi-account-cancel-outline"></i>Waived</span>';
        return '<span class="rqa-status-badge rqa-status-recommended"><i class="mdi mdi-account-clock-outline"></i>Recommended</span>';
    }

    function recByHtml(r) {
        if (!r.recommendedBy) return '<span class="text-muted">—</span>';
        var html = '<span class="rqa-recby-main">' + escHtml(r.recommendedBy) + '</span>';
        if (r.recommendedByPosition) html += '<span class="rqa-recby-sub">' + escHtml(r.recommendedByPosition) + '</span>';
        return html;
    }

    function listRowHtml(r, index) {
        var html = '<tr>';
        html += '<td class="num"><span class="rqa-rank-badge">' + index + '</span></td>';
        html += '<td><span class="rqa-name-main">' + escHtml(r.name) + '</span><span class="rqa-name-sub">Code: <span class="rqa-code-inline">' + escHtml(r.code) + '</span></span></td>';
        html += '<td><span class="rqa-location-tag">' + escHtml(locationText(r)) + '</span></td>';
        html += '<td class="rqa-col-tribe">' + (r.tribe ? escHtml(r.tribe) : '<span class="text-muted">—</span>') + '</td>';
        html += '<td>' + contactHtml(r) + '</td>';
        html += '<td><span class="rqa-pos-tag">' + escHtml(r.position) + '</span></td>';
        html += '<td>' + (r.itemNumber ? '<span class="rqa-item-pill">' + escHtml(r.itemNumber) + '</span>' : '<span class="text-muted">—</span>') + '</td>';
        html += '<td>' + (r.school ? '<span class="rqa-school-tag">' + escHtml(r.school) + '</span>' : '<span class="text-muted">—</span>') + '</td>';
        html += '<td>' + (r.remarks ? escHtml(r.remarks) : '<span class="text-muted">—</span>') + '</td>';
        html += '<td>' + statusBadge(r.status) + '</td>';
        html += '<td>' + recByHtml(r) + '</td>';
        html += '<td>' + recommendedOnHtml(r.recommendedAt) + '</td>';
        html += '</tr>';
        return html;
    }

    function getFilteredRows() {
        var pos = $pos.val() || '';
        var school = $school.val() || '';
        return listRows.filter(function (r) {
            if (pos !== '' && !eqi(r.position, pos)) return false;
            if (school !== '' && !eqi(r.school, school)) return false;
            return true;
        });
    }

    function renderList() {
        var rows = getFilteredRows();
        var $tbody = $('#rqa-list-table tbody');

        if (rows.length === 0) {
            $('#rqa-list-results').hide();
            if (listRows.length === 0) {
                var msg = (scope === 'mine')
                    ? 'You have not recommended any applicants yet. Recommend an applicant on the RQA Recommendation page and it will appear here.'
                    : 'No applicants have been recommended yet.';
                setEmptyState('mdi-clipboard-text-outline', 'No recommended applicants.', msg);
            } else {
                setEmptyState('mdi-filter-remove-outline', 'No records match the selected filters.', 'Try changing the position or school filter.');
            }
        } else {
            var html = '';
            rows.forEach(function (r, i) { html += listRowHtml(r, i + 1); });
            $tbody.html(html);
            $('#rqa-list-empty').hide();
            $('#rqa-list-results').show();
        }

        // Tribe applies only to IPED Elementary / Secondary positions.
        $('#rqa-list-table .rqa-col-tribe').toggle(listRows.some(function (r) { return r.tribeApplicable; }));

        var badge = $('#rqa-count-badge');
        if (listRows.length > 0) {
            badge.text(rows.length + ' of ' + listRows.length + (scope === 'mine' ? ' (mine)' : '')).show();
        } else {
            badge.hide();
        }
    }

    function rebuildFilters() {
        setSelectOptions($pos, distinct(listRows.map(function (r) { return r.position; })), 'All Positions');
        setSelectOptions($school, distinct(listRows.map(function (r) { return r.school; })), 'All Schools');
    }

    function loadList() {
        $('#rqa-list-empty').hide();
        $('#rqa-list-results').hide();
        $('#rqa-list-loading').show();

        $.getJSON(dataUrl, { scope: scope }).done(function (res) {
            $('#rqa-list-loading').hide();
            if (!res || res.status !== 'success') {
                listRows = [];
                setEmptyState('mdi-alert-circle-outline', 'Unable to load records.', (res && res.message) ? res.message : 'Please try again.');
                return;
            }
            listRows = res.rows || [];
            rebuildFilters();
            renderList();
        }).fail(function () {
            $('#rqa-list-loading').hide();
            listRows = [];
            setEmptyState('mdi-wifi-off', 'Unable to load records.', 'Please check your connection and try again.');
        });
    }

    $pos.on('change', function () { renderList(); });
    $school.on('change', function () { renderList(); });

    // Switch between "My Recommended" and "All Recommended" without leaving the
    // page (keeps the URL in sync so the view can be bookmarked / refreshed).
    $('.rqa-scope-btn').on('click', function () {
        var next = $(this).data('scope');
        if (next === scope) return;
        scope = (next === 'all') ? 'all' : 'mine';

        $('.rqa-scope-btn').removeClass('active');
        $(this).addClass('active');

        $('#rqa-hero-sub').text(scope === 'mine'
            ? 'Applicants you have recommended, with their position, item number, school, remarks, and the time recommended.'
            : 'All recommended applicants, with their position, item number, school, remarks, who recommended them, and when.');

        if (window.history && window.history.replaceState) {
            window.history.replaceState(null, '', '?scope=' + scope);
        }

        loadList();
    });

    loadList();
});
</script>
