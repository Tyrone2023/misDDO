<?php
// Plan Supervisor dashboard: view-only counts of the AIP pipeline. The numbers
// are rendered server-side so the page works without JavaScript, then refreshed
// on a timer from Page/plansup_counts. Every card links to its own read-only list.
$cards = array(
    array(
        'key'   => 'submitted',
        'link'  => 'Page/plansup_submitted',
        'label' => 'Submitted AIP',
        'sub'   => 'Schools that submitted a plan',
        'icon'  => 'mdi-send-check-outline',
        'mod'   => 'ps-sub',
    ),
    array(
        'key'   => 'reviewed',
        'link'  => 'Page/plansup_reviewed',
        'label' => 'Reviewed',
        'sub'   => 'Passed the review stage',
        'icon'  => 'mdi-file-find-outline',
        'mod'   => 'ps-rev',
    ),
    array(
        'key'   => 'funds',
        'link'  => 'Page/plansup_funds',
        'label' => 'Funds Available',
        'sub'   => 'Certified with available funds',
        'icon'  => 'mdi-cash-multiple',
        'mod'   => 'ps-fun',
    ),
    array(
        'key'   => 'approved',
        'link'  => 'Page/plansup_approved',
        'label' => 'Approved',
        'sub'   => 'Reached final approval',
        'icon'  => 'mdi-check-decagram',
        'mod'   => 'ps-app',
    ),
);
?>

<style>
    .ps-shell { padding-bottom: 24px; }

    .ps-hero {
        position: relative; overflow: hidden; border-radius: 16px;
        padding: 1.75rem 1.9rem; margin-bottom: 1.5rem;
        background: linear-gradient(120deg, #1f3a5f 0%, #2c5282 55%, #3182ce 100%);
        box-shadow: 0 10px 26px rgba(31, 58, 95, .22);
        color: #fff;
        display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1.25rem;
    }
    .ps-hero::before { content: ""; position: absolute; width: 230px; height: 230px; border-radius: 50%; right: -80px; top: -95px; background: rgba(255,255,255,.10); }
    .ps-hero-text { position: relative; z-index: 2; }
    .ps-hero-eyebrow {
        display: inline-flex; align-items: center; gap: .35rem;
        background: rgba(255,255,255,.18); border-radius: 999px;
        padding: .22rem .7rem; font-size: .7rem; font-weight: 600;
        text-transform: uppercase; letter-spacing: .06em;
    }
    .ps-hero-title { margin: .6rem 0 .3rem; font-weight: 600; color: #fff; }
    .ps-hero-sub { margin: 0; opacity: .85; font-size: .875rem; }
    .ps-hero-sub strong { color: #fff; }
    .ps-live {
        position: relative; z-index: 2;
        display: inline-flex; align-items: center; gap: .45rem;
        border-radius: 999px; padding: .4rem .8rem;
        font-size: .74rem; font-weight: 600; white-space: nowrap;
        background: rgba(255,255,255,.16); border: 1px solid rgba(255,255,255,.22);
    }
    .ps-live .ps-dot { width: 9px; height: 9px; border-radius: 50%; background: #5af0c4; animation: ps-pulse 1.6s infinite; }
    @keyframes ps-pulse {
        0%   { box-shadow: 0 0 0 0 rgba(90,240,196,.6); }
        70%  { box-shadow: 0 0 0 9px rgba(90,240,196,0); }
        100% { box-shadow: 0 0 0 0 rgba(90,240,196,0); }
    }

    .ps-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.5rem; }
    @media (max-width: 1199px) { .ps-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 575px)  { .ps-grid { grid-template-columns: 1fr; } }

    .ps-card {
        display: block; border: 1px solid #e9edf2; border-radius: 14px;
        background: #fff; padding: 1.15rem 1.25rem; text-decoration: none;
        box-shadow: 0 2px 10px rgba(16, 30, 54, .05);
        transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease;
    }
    .ps-card:hover { transform: translateY(-3px); box-shadow: 0 14px 30px rgba(31,58,95,.14); border-color: #dbe6f5; text-decoration: none; }
    .ps-card-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: .6rem; }
    .ps-card-ico { width: 44px; height: 44px; border-radius: 12px; display: inline-flex; align-items: center; justify-content: center; font-size: 22px; }
    .ps-card-num { font-size: 2rem; font-weight: 600; line-height: 1; color: #313a46; }
    .ps-card-label { font-size: .84rem; font-weight: 600; color: #313a46; }
    .ps-card-sub { font-size: .74rem; color: #98a6ad; margin-top: .15rem; }
    .ps-card-go { margin-top: .75rem; font-size: .74rem; font-weight: 600; color: #2c5282; display: inline-flex; align-items: center; gap: .25rem; }

    .ps-card.ps-sub .ps-card-ico { background: #e8edfb; color: #33499e; }
    .ps-card.ps-rev .ps-card-ico { background: #e6f2fb; color: #1d6fa5; }
    .ps-card.ps-fun .ps-card-ico { background: #fdf3e2; color: #a5701d; }
    .ps-card.ps-app .ps-card-ico { background: #e4f5ec; color: #1e7d44; }

    .ps-links-card { background: #fff; border: 1px solid #e9edf2; border-radius: 14px; padding: 1.35rem; box-shadow: 0 2px 10px rgba(16, 30, 54, .05); }
    .ps-links-title { margin: 0 0 1rem; font-size: .94rem; font-weight: 600; color: #313a46; display: flex; align-items: center; gap: .5rem; }
    .ps-links-title i { width: 32px; height: 32px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; background: #eef5ff; color: #1f3a5f; font-size: 17px; }
    .ps-links { display: grid; grid-template-columns: repeat(4, 1fr); gap: .75rem; }
    @media (max-width: 991px) { .ps-links { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 575px) { .ps-links { grid-template-columns: 1fr; } }
    .ps-link {
        display: flex; align-items: center; gap: .7rem;
        padding: .8rem 1rem; border-radius: 11px;
        border: 1px solid #e9edf2; background: #fbfdff;
        color: #313a46; text-decoration: none; font-weight: 600; font-size: .84rem;
        transition: background .15s ease, border-color .15s ease;
    }
    .ps-link:hover { background: #eef5ff; border-color: #cfe0f4; color: #2c5282; text-decoration: none; }
    .ps-link i { font-size: 20px; color: #2c5282; }
    .ps-link small { display: block; font-weight: 400; color: #98a6ad; font-size: .7rem; }

    @media (max-width: 767px) { .ps-hero { padding: 1.25rem; } }
</style>

<!-- ============================================================== -->
<!-- Start Page Content here -->
<!-- ============================================================== -->

<div class="content-page">
    <div class="content">

        <div class="container-fluid ps-shell">

            <div class="ps-hero">
                <div class="ps-hero-text">
                    <span class="ps-hero-eyebrow"><i class="mdi mdi-eye-outline"></i> View only</span>
                    <h3 class="ps-hero-title"><?= html_escape($title); ?></h3>
                    <p class="ps-hero-sub">
                        Annual Implementation Plan pipeline for Fiscal Year <strong><?= html_escape($fy); ?></strong>.
                        Select any count to open its list.
                    </p>
                </div>
                <span class="ps-live"><span class="ps-dot"></span> Live &middot; updates every 10s</span>
            </div>

            <div class="ps-grid">
                <?php foreach ($cards as $card) : ?>
                    <a href="<?= base_url() . $card['link']; ?>" class="ps-card <?= $card['mod']; ?>">
                        <div class="ps-card-top">
                            <span class="ps-card-num" id="ps-<?= $card['key']; ?>"><?= number_format((int) $counts[$card['key']]); ?></span>
                            <span class="ps-card-ico"><i class="mdi <?= $card['icon']; ?>"></i></span>
                        </div>
                        <div class="ps-card-label"><?= html_escape($card['label']); ?></div>
                        <div class="ps-card-sub"><?= html_escape($card['sub']); ?></div>
                        <span class="ps-card-go">View list <i class="mdi mdi-arrow-right"></i></span>
                    </a>
                <?php endforeach; ?>
            </div>

            <div class="ps-links-card">
                <h5 class="ps-links-title"><i class="mdi mdi-format-list-bulleted"></i> Plan lists</h5>
                <div class="ps-links">
                    <?php foreach ($cards as $card) : ?>
                        <a href="<?= base_url() . $card['link']; ?>" class="ps-link">
                            <i class="mdi <?= $card['icon']; ?>"></i>
                            <span><?= html_escape($card['label']); ?><small><?= html_escape($card['sub']); ?></small></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>
        <!-- end container-fluid -->

    </div>
    <!-- end content -->
</div>

<script>
    // This view renders before templates/footer pulls in vendor.min.js, so jQuery
    // is only safe to touch once the document has finished parsing.
    document.addEventListener('DOMContentLoaded', function () {
        var countsUrl = '<?= base_url('Page/plansup_counts'); ?>';
        var keys = ['submitted', 'reviewed', 'funds', 'approved'];

        function loadCounts() {
            $.getJSON(countsUrl).done(function (res) {
                if (!res || res.status !== 'success' || !res.counts) {
                    return;
                }
                keys.forEach(function (key) {
                    var el = document.getElementById('ps-' + key);
                    if (!el) {
                        return;
                    }
                    var val = res.counts[key] == null ? 0 : Number(res.counts[key]);
                    el.textContent = val.toLocaleString();
                });
            });
        }

        setInterval(loadCounts, 10000);
    });
</script>
