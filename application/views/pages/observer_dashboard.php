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
    .obs-shell { padding-bottom: 24px; }

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
    .rqa-hero-icon { width: 54px; height: 54px; border-radius: 16px; display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,.14); border: 1px solid rgba(255,255,255,.18); flex: 0 0 auto; }
    .rqa-hero-icon i { font-size: 28px; color: #fff; }

    .obs-live { display: inline-flex; align-items: center; gap: 7px; border-radius: 999px; padding: .4rem .8rem; font-size: .74rem; font-weight: 700; background: rgba(255,255,255,.16); border: 1px solid rgba(255,255,255,.22); color: #fff; white-space: nowrap; }
    .obs-live .dot { width: 9px; height: 9px; border-radius: 50%; background: #5af0c4; box-shadow: 0 0 0 0 rgba(90,240,196,.7); animation: obs-pulse 1.6s infinite; }
    @keyframes obs-pulse { 0% { box-shadow: 0 0 0 0 rgba(90,240,196,.6); } 70% { box-shadow: 0 0 0 9px rgba(90,240,196,0); } 100% { box-shadow: 0 0 0 0 rgba(90,240,196,0); } }

    .obs-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 18px; }
    @media (max-width: 1199px) { .obs-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 575px) { .obs-grid { grid-template-columns: 1fr; } }

    .obs-card { display: block; border: 0; border-radius: 16px; background: #fff; box-shadow: 0 8px 26px rgba(31,58,95,.08); padding: 18px 20px; text-decoration: none; transition: transform .15s ease, box-shadow .15s ease; }
    .obs-card:hover { transform: translateY(-3px); box-shadow: 0 16px 34px rgba(31,58,95,.16); text-decoration: none; }
    .obs-card .obs-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px; }
    .obs-card .obs-ico { width: 46px; height: 46px; border-radius: 13px; display: inline-flex; align-items: center; justify-content: center; font-size: 24px; }
    .obs-card .obs-num { font-size: 2.1rem; font-weight: 800; line-height: 1; color: var(--rqa-text); }
    .obs-card .obs-label { font-size: .82rem; font-weight: 800; color: var(--rqa-text); margin-top: 8px; }
    .obs-card .obs-sub { font-size: .72rem; color: var(--rqa-muted); margin-top: 2px; }

    .obs-card.obs-rec .obs-ico { background: #eef5ff; color: #274b7a; }
    .obs-card.obs-app .obs-ico { background: #fff6e9; color: #9a6b16; }
    .obs-card.obs-iss .obs-ico { background: #e8fff8; color: #129777; }
    .obs-card.obs-wai .obs-ico { background: #fff0f0; color: #b94a48; }

    .obs-links-card { border: 0; border-radius: 16px; background: #fff; box-shadow: 0 8px 26px rgba(31,58,95,.08); padding: 18px 20px; }
    .obs-links-title { margin: 0 0 14px; font-size: .94rem; font-weight: 800; color: var(--rqa-text); display: flex; align-items: center; gap: 8px; }
    .obs-links-title i { width: 32px; height: 32px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; background: var(--rqa-soft); color: var(--rqa-primary); font-size: 17px; }
    .obs-links { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; }
    @media (max-width: 767px) { .obs-links { grid-template-columns: 1fr; } }
    .obs-link { display: flex; align-items: center; gap: 12px; padding: 14px 16px; border-radius: 13px; border: 1px solid var(--rqa-border); background: #fbfdff; color: var(--rqa-text); text-decoration: none; font-weight: 700; transition: background .15s ease, border-color .15s ease; }
    .obs-link:hover { background: var(--rqa-soft); border-color: #cfe0f4; text-decoration: none; color: var(--rqa-primary); }
    .obs-link i { font-size: 22px; color: var(--rqa-primary); }
    .obs-link small { display: block; font-weight: 600; color: var(--rqa-muted); font-size: .7rem; }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid obs-shell">

            <div class="rqa-hero">
                <div class="rqa-hero-content">
                    <div class="rqa-title-block">
                        <h4><?= h($title ?? 'RQA Observer Dashboard'); ?></h4>
                        <p>Read-only overview of the Registry of Qualified Applicants pipeline. The figures below update automatically as applicants are recommended, approved, and issued &mdash; no need to refresh.</p>
                    </div>
                    <span class="obs-live"><span class="dot"></span> Live &middot; updates every 5s</span>
                </div>
            </div>

            <div class="obs-grid">
                <a href="<?= base_url(); ?>Pages/observer_approval" class="obs-card obs-rec">
                    <div class="obs-top">
                        <span class="obs-num" id="obs-recommended">&mdash;</span>
                        <span class="obs-ico"><i class="mdi mdi-account-check-outline"></i></span>
                    </div>
                    <div class="obs-label">Recommended</div>
                    <div class="obs-sub">Awaiting approval</div>
                </a>

                <a href="<?= base_url(); ?>Pages/observer_issuance" class="obs-card obs-app">
                    <div class="obs-top">
                        <span class="obs-num" id="obs-approved">&mdash;</span>
                        <span class="obs-ico"><i class="mdi mdi-check-decagram"></i></span>
                    </div>
                    <div class="obs-label">Approved</div>
                    <div class="obs-sub">For issuance (no date hired)</div>
                </a>

                <a href="<?= base_url(); ?>Pages/observer_issuance" class="obs-card obs-iss">
                    <div class="obs-top">
                        <span class="obs-num" id="obs-issued">&mdash;</span>
                        <span class="obs-ico"><i class="mdi mdi-file-document-check-outline"></i></span>
                    </div>
                    <div class="obs-label">Issued / Hired</div>
                    <div class="obs-sub">Date hired recorded</div>
                </a>

                <a href="<?= base_url(); ?>Pages/observer_issuance" class="obs-card obs-wai">
                    <div class="obs-top">
                        <span class="obs-num" id="obs-waived">&mdash;</span>
                        <span class="obs-ico"><i class="mdi mdi-cancel"></i></span>
                    </div>
                    <div class="obs-label">Waived</div>
                    <div class="obs-sub">Applicant waived the post</div>
                </a>
            </div>

            <div class="obs-links-card">
                <h5 class="obs-links-title"><i class="mdi mdi-eye-outline"></i> Observe a stage</h5>
                <div class="obs-links">
                    <a href="<?= base_url(); ?>Pages/observer_recommendation" class="obs-link">
                        <i class="mdi mdi-account-check-outline"></i>
                        <span>Recommendation<small>Ranked qualified applicants</small></span>
                    </a>
                    <a href="<?= base_url(); ?>Pages/observer_approval" class="obs-link">
                        <i class="mdi mdi-gavel"></i>
                        <span>Approval<small>Recommended applicants</small></span>
                    </a>
                    <a href="<?= base_url(); ?>Pages/observer_issuance" class="obs-link">
                        <i class="mdi mdi-file-document-outline"></i>
                        <span>Issuance<small>Issued &amp; waived list</small></span>
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var countsUrl = '<?= base_url('Pages/observer_counts'); ?>';

    function setNum(id, val) {
        var el = document.getElementById(id);
        if (el) el.textContent = (val == null ? '0' : String(val));
    }

    function loadCounts() {
        $.getJSON(countsUrl).done(function (res) {
            if (!res || res.status !== 'success' || !res.counts) return;
            var c = res.counts;
            setNum('obs-recommended', c.recommended);
            setNum('obs-approved', c.approved);
            setNum('obs-issued', c.issued);
            setNum('obs-waived', c.waived);
        });
    }

    loadCounts();
    setInterval(loadCounts, 5000);
});
</script>
