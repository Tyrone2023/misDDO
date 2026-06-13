<?php
if (!function_exists('h')) {
    function h($v)
    {
        return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
    }
}
$division = isset($settings->division) ? (string) $settings->division : '';
?>

<style>
    :root {
        --rqa-primary: #1f3a5f;
        --rqa-border: #e5ecf5;
        --rqa-text: #25364a;
        --rqa-muted: #7b8794;
        --rqa-soft: #eef5ff;
    }

    .content-page { background: #f4f7fb; min-height: 100vh; }
    .rqair-shell { padding-bottom: 24px; }

    .rqair-toolbar {
        background: #fff; border: 1px solid var(--rqa-border); border-radius: 14px;
        padding: 16px 18px; margin-bottom: 16px;
        box-shadow: 0 8px 26px rgba(31, 58, 95, .07);
    }
    .rqair-toolbar label { font-weight: 800; font-size: .66rem; text-transform: uppercase; letter-spacing: .5px; color: var(--rqa-muted); margin-bottom: 6px; }
    .rqair-toolbar .form-group { margin-bottom: 0; }
    .rqair-actions { display: flex; align-items: flex-end; gap: 8px; height: 100%; }

    .rqair-sheet {
        background: #fff; border: 1px solid var(--rqa-border); border-radius: 14px;
        padding: 26px 30px; box-shadow: 0 8px 26px rgba(31, 58, 95, .07);
    }

    .rqair-head { text-align: center; margin-bottom: 6px; }
    .rqair-head img { height: 72px; margin-bottom: 6px; }
    .rqair-head .rp { font-size: .82rem; color: #333; }
    .rqair-head .de { font-weight: 800; font-size: .9rem; color: #222; }
    .rqair-head .ll { font-size: .82rem; color: #333; }
    .rqair-title { text-align: center; font-size: 1.12rem; font-weight: 800; text-transform: uppercase; letter-spacing: .6px; margin: 14px 0 2px; color: var(--rqa-primary); }
    .rqair-meta { text-align: center; color: #555; font-size: .76rem; margin-bottom: 16px; }
    .rqair-meta b { color: var(--rqa-text); }

    #rqair-table { width: 100%; border-collapse: collapse; }
    #rqair-table th, #rqair-table td { border: 1px solid #b9c4d0; padding: 6px 9px; vertical-align: top; font-size: .76rem; }
    #rqair-table thead th { background: var(--rqa-primary); color: #fff; text-align: left; font-size: .68rem; text-transform: uppercase; letter-spacing: .3px; }
    #rqair-table th.num, #rqair-table td.num { text-align: center; width: 38px; }
    #rqair-table td.item { font-weight: 800; }
    #rqair-table .sub { color: var(--rqa-muted); font-size: .66rem; margin-top: 2px; }
    #rqair-table tr.school-head td { background: #eaf1fb; font-weight: 800; color: var(--rqa-primary); font-size: .8rem; }
    #rqair-table tr.school-head .school-count { color: var(--rqa-muted); font-weight: 600; }
    .badge-hired { display: inline-block; font-weight: 800; font-size: .64rem; color: #1b7a52; background: #e8f7ef; border: 1px solid #b9e6cd; border-radius: 999px; padding: .1rem .45rem; }
    .badge-waived { display: inline-block; font-weight: 800; font-size: .64rem; color: #b94a48; background: #fff0f0; border: 1px solid #f4c7c3; border-radius: 999px; padding: .1rem .45rem; }

    .rqair-empty { text-align: center; padding: 40px 18px; color: var(--rqa-muted); }
    .rqair-empty i { font-size: 42px; color: #c4d2e3; display: block; margin-bottom: 10px; }

    @media print {
        .navbar-custom, .left-side-menu, .footer, .rqair-toolbar, .d-print-none { display: none !important; }
        .content-page, body { background: #fff !important; }
        .content, .container-fluid { padding: 0 !important; margin: 0 !important; max-width: 100% !important; }
        .rqair-sheet { border: 0 !important; box-shadow: none !important; padding: 0 !important; }
        #rqair-table th, #rqair-table td { font-size: 11px !important; }
        #rqair-table thead th { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        tr, td, th { page-break-inside: avoid !important; }
        thead { display: table-header-group; }
        @page { size: A4 portrait; margin: 10mm; }
    }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid rqair-shell">

            <div class="rqair-toolbar d-print-none">
                <div class="form-row align-items-end">
                    <div class="form-group col-xl-4 col-lg-4 col-md-6">
                        <label for="rqair-position">Position</label>
                        <select id="rqair-position" class="form-control"><option value="">All Positions</option></select>
                    </div>
                    <div class="form-group col-xl-3 col-lg-3 col-md-6">
                        <label for="rqair-status">Status</label>
                        <select id="rqair-status" class="form-control">
                            <option value="">Hired &amp; Waived</option>
                            <option value="hired">Hired</option>
                            <option value="waived">Waived</option>
                        </select>
                    </div>
                    <div class="form-group col-xl-5 col-lg-5 col-md-12">
                        <div class="rqair-actions justify-content-md-end">
                            <a href="<?= base_url('Pages/rqa_issuance'); ?>" class="btn btn-light font-weight-bold"><i class="mdi mdi-arrow-left mr-1"></i>Back</a>
                            <button type="button" id="rqair-print" class="btn btn-primary font-weight-bold"><i class="mdi mdi-printer mr-1"></i>Print</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rqair-sheet" id="rqair-sheet">
                <div class="rqair-head">
                    <img src="<?= base_url('assets/images/report/ke.png'); ?>" alt="">
                    <div class="rp">Republic of the Philippines</div>
                    <div class="de">Department of Education</div>
                    <div class="ll">Region XI</div>
                    <?php if ($division !== ''): ?>
                        <div class="ll">Schools Division of <?= h($division); ?></div>
                    <?php endif; ?>
                </div>

                <div class="rqair-title">List of Issuance</div>
                <!-- <div class="rqair-meta" id="rqair-meta"></div> -->

                <div id="rqair-body"></div>
            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var reportData = <?= json_encode($rows, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?> || [];

    var $pos = $('#rqair-position');
    var $status = $('#rqair-status');

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

    function queryParam(name) {
        var m = new RegExp('[?&]' + name + '=([^&]*)').exec(window.location.search);
        return m ? decodeURIComponent(m[1].replace(/\+/g, ' ')) : '';
    }

    // Build the Position filter from the data, pre-selecting any ?position=
    (function () {
        var preset = queryParam('position');
        var positions = distinct(reportData.map(function (r) { return r.position; }));
        var html = '<option value="">All Positions</option>';
        positions.forEach(function (v) {
            var sel = (v.toLowerCase() === preset.trim().toLowerCase()) ? ' selected' : '';
            html += '<option value="' + escAttr(v) + '"' + sel + '>' + escHtml(v) + '</option>';
        });
        $pos.html(html);
    })();

    if ($.fn.select2) {
        $pos.select2({ width: '100%', placeholder: 'All Positions' });
        $status.select2({ width: '100%', minimumResultsForSearch: Infinity });
    }

    function statusBadge(r) {
        if (r.reportStatus === 'waived') {
            return '<span class="badge-waived">Waived</span>' + (r.dateWaived ? ' <span class="sub">' + escHtml(r.dateWaived) + '</span>' : '');
        }
        return '<span class="badge-hired">Hired</span>' + (r.dateHired ? ' <span class="sub">' + escHtml(r.dateHired) + '</span>' : '');
    }

    function getRows() {
        var pos = $pos.val() || '';
        var status = $status.val() || '';
        return reportData.filter(function (r) {
            if (pos !== '' && !eqi(r.position, pos)) return false;
            if (status !== '' && r.reportStatus !== status) return false;
            return true;
        });
    }

    function render() {
        var rows = getRows();

        var posLabel = ($pos.val() || '').trim() || 'All Positions';
        var statusMap = { '': 'Hired & Waived', hired: 'Hired', waived: 'Waived' };
        var statusLabel = statusMap[$status.val() || ''] || 'Hired & Waived';

        $('#rqair-meta').html('Position: <b>' + escHtml(posLabel) + '</b> &nbsp;|&nbsp; Status: <b>'
            + escHtml(statusLabel) + '</b> &nbsp;|&nbsp; Total: <b>' + rows.length + '</b> &nbsp;|&nbsp; Printed: <b>'
            + escHtml(new Date().toLocaleDateString()) + '</b>');

        if (rows.length === 0) {
            $('#rqair-body').html('<div class="rqair-empty"><i class="mdi mdi-file-document-outline"></i>No records match the selected Position and Status.</div>');
            return;
        }

        // Group by school (blanks under "Unassigned"), schools ordered ASC
        var groups = {};
        rows.forEach(function (r) {
            var key = (r.school || '').trim() || 'Unassigned';
            (groups[key] = groups[key] || []).push(r);
        });
        var schools = Object.keys(groups).sort(function (a, b) { return a.localeCompare(b); });

        var body = '', n = 0;
        schools.forEach(function (school) {
            var list = groups[school].slice().sort(function (a, b) { return (a.name || '').localeCompare(b.name || ''); });
            body += '<tr class="school-head"><td colspan="7">' + escHtml(school) + ' <span class="school-count">(' + list.length + ')</span></td></tr>';
            list.forEach(function (r) {
                n++;
                body += '<tr>'
                    + '<td class="num">' + n + '</td>'
                    + '<td>' + escHtml(r.name) + (r.code ? '<div class="sub">Code: ' + escHtml(r.code) + '</div>' : '') + '</td>'
                    + '<td>' + (r.contact ? escHtml(r.contact) : '<span class="sub">N/A</span>') + (r.email ? '<div class="sub">' + escHtml(r.email) + '</div>' : '') + '</td>'
                    + '<td>' + escHtml(r.position) + '</td>'
                    + '<td class="item">' + escHtml(r.itemNumber) + '</td>'
                    + '<td>' + statusBadge(r) + '</td>'
                    + '<td>' + (r.dateHired ? escHtml(r.dateHired) : '—') + '</td>'
                    + '</tr>';
            });
        });

        var html = '<table id="rqair-table"><thead><tr>'
            + '<th class="num">No.</th><th>Applicant</th><th>Contact Info</th><th>Position</th><th>Item No.</th><th>Status</th><th>Date Hired</th>'
            + '</tr></thead><tbody>' + body + '</tbody></table>';
        $('#rqair-body').html(html);
    }

    $pos.on('change', render);
    $status.on('change', render);
    $('#rqair-print').on('click', function () { window.print(); });

    render();
});
</script>
