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
    .ds-shell { padding-bottom: 24px; }

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

    .ds-live { display: inline-flex; align-items: center; gap: 7px; border-radius: 999px; padding: .4rem .8rem; font-size: .74rem; font-weight: 700; background: rgba(255,255,255,.16); border: 1px solid rgba(255,255,255,.22); color: #fff; white-space: nowrap; }
    .ds-live .dot { width: 9px; height: 9px; border-radius: 50%; background: #5af0c4; box-shadow: 0 0 0 0 rgba(90,240,196,.7); animation: ds-pulse 1.6s infinite; }
    @keyframes ds-pulse { 0% { box-shadow: 0 0 0 0 rgba(90,240,196,.6); } 70% { box-shadow: 0 0 0 9px rgba(90,240,196,0); } 100% { box-shadow: 0 0 0 0 rgba(90,240,196,0); } }

    .ds-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 18px; }
    @media (max-width: 1199px) { .ds-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 575px) { .ds-grid { grid-template-columns: 1fr; } }

    .ds-card { display: block; border: 0; border-radius: 16px; background: #fff; box-shadow: 0 8px 26px rgba(31,58,95,.08); padding: 18px 20px; text-decoration: none; transition: transform .15s ease, box-shadow .15s ease; }
    .ds-card:hover { transform: translateY(-3px); box-shadow: 0 16px 34px rgba(31,58,95,.16); text-decoration: none; }
    .ds-card.ds-static:hover { transform: none; box-shadow: 0 8px 26px rgba(31,58,95,.08); }
    .ds-card .ds-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px; }
    .ds-card .ds-ico { width: 46px; height: 46px; border-radius: 13px; display: inline-flex; align-items: center; justify-content: center; font-size: 24px; }
    .ds-card .ds-num { font-size: 2.1rem; font-weight: 800; line-height: 1; color: var(--rqa-text); }
    .ds-card .ds-label { font-size: .82rem; font-weight: 800; color: var(--rqa-text); margin-top: 8px; }
    .ds-card .ds-sub { font-size: .72rem; color: var(--rqa-muted); margin-top: 2px; }

    .ds-card.ds-reco .ds-ico { background: #eef5ff; color: #274b7a; }
    .ds-card.ds-waiv .ds-ico { background: #fff0f0; color: #b94a48; }
    .ds-card.ds-appr .ds-ico { background: #f1ecff; color: #5b43a8; }
    .ds-card.ds-appt .ds-ico { background: #e8fff8; color: #129777; }

    .ds-panel { border: 0; border-radius: 16px; background: #fff; box-shadow: 0 8px 26px rgba(31,58,95,.08); padding: 18px 20px; margin-bottom: 18px; }
    .ds-panel-title { margin: 0 0 14px; font-size: .94rem; font-weight: 800; color: var(--rqa-text); display: flex; align-items: center; gap: 8px; }
    .ds-panel-title i { width: 32px; height: 32px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; background: var(--rqa-soft); color: var(--rqa-primary); font-size: 17px; }

    .ds-links { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; }
    @media (max-width: 767px) { .ds-links { grid-template-columns: 1fr; } }
    .ds-link { display: flex; align-items: center; gap: 12px; padding: 16px 18px; border-radius: 13px; border: 1px solid var(--rqa-border); background: #fbfdff; color: var(--rqa-text); text-decoration: none; font-weight: 700; transition: background .15s ease, border-color .15s ease; }
    .ds-link:hover { background: var(--rqa-soft); border-color: #cfe0f4; text-decoration: none; color: var(--rqa-primary); }
    .ds-link > i { font-size: 24px; color: var(--rqa-primary); flex: 0 0 auto; }
    .ds-link span { flex: 1 1 auto; }
    .ds-link small { display: block; font-weight: 600; color: var(--rqa-muted); font-size: .7rem; margin-top: 2px; }
    .ds-link .ds-pill { flex: 0 0 auto; border-radius: 999px; padding: .2rem .6rem; font-size: .7rem; font-weight: 800; background: var(--rqa-soft); color: var(--rqa-primary); }

    .ds-table { width: 100%; margin: 0; font-size: .8rem; }
    .ds-table thead th { border: 0; border-bottom: 1px solid var(--rqa-border); color: var(--rqa-muted); font-size: .68rem; font-weight: 800; text-transform: uppercase; letter-spacing: .3px; padding: .4rem .55rem; white-space: nowrap; }
    .ds-table tbody td { border: 0; border-bottom: 1px solid #f2f6fb; padding: .55rem; color: var(--rqa-text); vertical-align: middle; }
    .ds-table tbody tr:last-child td { border-bottom: 0; }
    .ds-empty { text-align: center; color: var(--rqa-muted); padding: 18px 0 !important; }

</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid ds-shell">

            <div class="rqa-hero">
                <div class="rqa-hero-content">
                    <div class="rqa-title-block">
                        <h4><?= h($title ?? 'District Supervisor Dashboard'); ?></h4>
                        <p>Read-only view of the Teacher I track in the Registry of Qualified Applicants. The figures below cover every Teacher I record and update automatically as applicants are recommended, approved and appointed &mdash; no need to refresh.</p>
                    </div>
                    <span class="ds-live"><span class="dot"></span> Live &middot; updates every 10s</span>
                </div>
            </div>

            <div class="ds-grid">
                <a href="<?= base_url(); ?>Pages/rqa_recommendation" class="ds-card ds-reco">
                    <div class="ds-top">
                        <span class="ds-num" id="ds-recommended">&mdash;</span>
                        <span class="ds-ico"><i class="mdi mdi-account-check-outline"></i></span>
                    </div>
                    <div class="ds-label">Recommended</div>
                    <div class="ds-sub">Awaiting approval</div>
                </a>

                <div class="ds-card ds-appr ds-static">
                    <div class="ds-top">
                        <span class="ds-num" id="ds-approved">&mdash;</span>
                        <span class="ds-ico"><i class="mdi mdi-check-decagram"></i></span>
                    </div>
                    <div class="ds-label">For Issuance</div>
                    <div class="ds-sub">Approved, appointment pending</div>
                </div>

                <a href="<?= base_url(); ?>Pages/rqa_appointed_list" class="ds-card ds-appt">
                    <div class="ds-top">
                        <span class="ds-num" id="ds-appointed">&mdash;</span>
                        <span class="ds-ico"><i class="mdi mdi-account-tie"></i></span>
                    </div>
                    <div class="ds-label">Appointed</div>
                    <div class="ds-sub">Appointment issued</div>
                </a>

                <div class="ds-card ds-waiv ds-static">
                    <div class="ds-top">
                        <span class="ds-num" id="ds-waived">&mdash;</span>
                        <span class="ds-ico"><i class="mdi mdi-cancel"></i></span>
                    </div>
                    <div class="ds-label">Waived</div>
                    <div class="ds-sub">Applicant waived the post</div>
                </div>
            </div>

            <div class="ds-panel">
                <h5 class="ds-panel-title"><i class="mdi mdi-clipboard-text-outline"></i> What you can do</h5>
                <div class="ds-links">
                    <a href="<?= base_url(); ?>Pages/rqa_recommendation" class="ds-link">
                        <i class="mdi mdi-account-check-outline"></i>
                        <span>RQA Recommendation<small>Ranked Teacher I applicants, read-only</small></span>
                    </a>
                    <a href="<?= base_url(); ?>Pages/rqa_appointed_list" class="ds-link">
                        <i class="mdi mdi-account-tie"></i>
                        <span>Appointed List<small>Teacher I applicants already appointed</small></span>
                        <span class="ds-pill" id="ds-appointed-pill">&mdash;</span>
                    </a>
                </div>
            </div>

            <div class="ds-panel">
                <h5 class="ds-panel-title"><i class="mdi mdi-history"></i> Latest Teacher I appointments</h5>
                <div class="table-responsive">
                    <table class="table ds-table">
                        <thead>
                            <tr>
                                <th>Applicant</th>
                                <th>Position</th>
                                <th>Item Number</th>
                                <th>School Assigned</th>
                                <th>Appointment Issued</th>
                            </tr>
                        </thead>
                        <tbody id="ds-recent">
                            <tr><td colspan="5" class="ds-empty">Loading&hellip;</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var countsUrl = '<?= base_url('Pages/district_sup_counts'); ?>';

    function setNum(id, val) {
        var el = document.getElementById(id);
        if (el) el.textContent = (val == null ? '0' : String(val));
    }

    function escHtml(v) {
        return $('<div>').text(v == null ? '' : String(v)).html();
    }


    function renderRecent(rows) {
        var $body = $('#ds-recent');
        if (!rows || !rows.length) {
            $body.html('<tr><td colspan="5" class="ds-empty">No appointments issued yet.</td></tr>');
            return;
        }

        var html = '';
        $.each(rows, function (i, r) {
            html += '<tr>';
            html += '<td><strong>' + escHtml(r.name) + '</strong></td>';
            html += '<td>' + escHtml(r.position) + '</td>';
            html += '<td>' + escHtml(r.itemNumber) + '</td>';
            html += '<td>' + escHtml(r.school) + '</td>';
            html += '<td>' + escHtml(r.date) + '</td>';
            html += '</tr>';
        });
        $body.html(html);
    }

    function loadCounts() {
        $.getJSON(countsUrl).done(function (res) {
            if (!res || res.status !== 'success' || !res.counts) return;
            var c = res.counts;
            setNum('ds-recommended', c.recommended);
            setNum('ds-approved', c.approved);
            setNum('ds-appointed', c.appointed);
            setNum('ds-waived', c.waived);
            setNum('ds-appointed-pill', c.appointed);
            renderRecent(res.recent);
        });
    }

    loadCounts();
    setInterval(loadCounts, 10000);
});
</script>
