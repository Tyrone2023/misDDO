<style>
/* ---- Submitted SMEA: district roll-up and school drill-down ---- */
.sm-dotsep { margin: 0 .4rem; opacity: .45; }
.sm-muted { color: #98a6ad; }
.sm-text-success { color: #1e7d44; }
.sm-text-danger  { color: #b03a3a; }

.sm-hero {
    background: linear-gradient(120deg, #1f3a5f 0%, #2c5282 55%, #3182ce 100%);
    border-radius: 14px;
    padding: 1.75rem 1.9rem;
    margin-bottom: 1.5rem;
    color: #fff;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 1.25rem;
    box-shadow: 0 10px 26px rgba(31, 58, 95, .22);
}
.sm-hero-eyebrow {
    display: inline-flex; align-items: center; gap: .35rem;
    background: rgba(255,255,255,.18);
    border-radius: 999px;
    padding: .22rem .7rem;
    font-size: .7rem; font-weight: 600;
    text-transform: uppercase; letter-spacing: .06em;
}
.sm-hero-title { margin: .6rem 0 .3rem; font-weight: 600; color: #fff; display: flex; align-items: center; gap: .5rem; }
.sm-hero-sub { margin: 0; opacity: .85; font-size: .875rem; }
.sm-hero-sub strong { color: #fff; }
.sm-hero-stats { display: flex; gap: .75rem; flex-wrap: wrap; }
.sm-stat {
    background: rgba(255,255,255,.13);
    border: 1px solid rgba(255,255,255,.18);
    border-radius: 11px;
    padding: .75rem 1.15rem;
    min-width: 108px;
    text-align: center;
}
.sm-stat-value { display: block; font-size: 1.5rem; font-weight: 600; line-height: 1.15; }
.sm-stat-label { display: block; font-size: .68rem; text-transform: uppercase; letter-spacing: .06em; opacity: .8; }

.sm-back {
    display: inline-flex; align-items: center; gap: .35rem;
    background: rgba(255,255,255,.16);
    border: 1px solid rgba(255,255,255,.24);
    border-radius: 999px;
    padding: .4rem .95rem;
    color: #fff;
    font-size: .8rem;
    font-weight: 500;
    transition: background .15s ease;
}
.sm-back:hover { background: rgba(255,255,255,.28); color: #fff; text-decoration: none; }

.sm-card {
    background: #fff;
    border: 1px solid #e9edf2;
    border-radius: 14px;
    padding: 1.35rem;
    box-shadow: 0 2px 10px rgba(16, 30, 54, .05);
}
.sm-card-head {
    display: flex; align-items: flex-start; justify-content: space-between;
    flex-wrap: wrap; gap: .75rem;
    margin-bottom: 1.1rem;
}
.sm-card-title { margin: 0; font-size: 1.05rem; font-weight: 600; color: #313a46; }
.sm-card-sub { margin: .25rem 0 0; font-size: .82rem; color: #98a6ad; }
.sm-card-actions { display: flex; gap: .45rem; flex-wrap: wrap; }

.sm-tabs { display: flex; gap: .4rem; flex-wrap: wrap; margin-bottom: 1.1rem; }
.sm-tab {
    display: inline-flex; align-items: center; gap: .4rem;
    border: 1px solid #e4e9f0;
    background: #f7f9fc;
    color: #5c6873;
    border-radius: 999px;
    padding: .4rem .95rem;
    font-size: .82rem;
    font-weight: 500;
    transition: all .15s ease;
}
.sm-tab:hover { background: #eef2f8; color: #313a46; text-decoration: none; }
.sm-tab-count {
    background: #e4e9f0; color: #5c6873;
    border-radius: 999px; padding: .05rem .45rem;
    font-size: .72rem; font-weight: 600;
}
.sm-tab.is-active { background: #2c5282; border-color: #2c5282; color: #fff; }
.sm-tab.is-active .sm-tab-count { background: rgba(255,255,255,.24); color: #fff; }

.sm-table { border-collapse: separate; border-spacing: 0; }
.sm-table thead th {
    border: none;
    border-bottom: 1px solid #e9edf2;
    background: transparent;
    color: #7b8794;
    font-size: .7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .06em;
    padding: .7rem .75rem;
}
.sm-table tbody td {
    border: none;
    border-bottom: 1px solid #f1f4f8;
    padding: .8rem .75rem;
    vertical-align: middle;
}
.sm-table tbody tr:hover { background: #f7f9fc; }
.sm-table tfoot td {
    border: none;
    border-top: 2px solid #e9edf2;
    padding: .85rem .75rem;
    font-weight: 600;
    color: #313a46;
}
.sm-total-row td { background: #f7f9fc; }

.sm-num {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 1.9rem; height: 1.9rem;
    border-radius: 8px;
    background: #e8f0fb;
    color: #2c5282;
    font-size: .78rem;
    font-weight: 600;
}

.sm-district, .sm-school { display: flex; align-items: center; gap: .7rem; }
.sm-avatar {
    flex: 0 0 auto;
    width: 36px; height: 36px;
    border-radius: 10px;
    background: linear-gradient(135deg, #e3edfa, #cfe0f7);
    color: #2c5282;
    font-weight: 600;
    display: inline-flex; align-items: center; justify-content: center;
}
.sm-district-text, .sm-school-text { display: flex; flex-direction: column; line-height: 1.3; }
.sm-district-name, .sm-school-name { font-weight: 600; color: #313a46; white-space: normal; }
.sm-district-sub, .sm-school-sub { font-size: .74rem; color: #98a6ad; }

.sm-chip {
    display: inline-block;
    background: #f1f4f8;
    border: 1px solid #e4e9f0;
    border-radius: 6px;
    padding: .16rem .5rem;
    font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
    font-size: .76rem;
    color: #4a5568;
}
.sm-chips { display: flex; flex-wrap: wrap; gap: .25rem; }
.sm-date { color: #5c6873; font-size: .84rem; }

.sm-metric { display: grid; grid-template-columns: auto 1fr; grid-gap: .05rem .5rem; align-items: center; min-width: 150px; }
.sm-metric-value { grid-row: 1; grid-column: 1; font-size: 1.15rem; font-weight: 600; line-height: 1.2; }
.sm-metric-label { grid-row: 1; grid-column: 2; font-size: .72rem; color: #98a6ad; }
.sm-bar {
    grid-row: 2; grid-column: 1 / span 2;
    display: block; height: 6px;
    background: #eef1f5;
    border-radius: 999px;
    overflow: hidden;
    margin-top: .3rem;
}
.sm-bar-fill { display: block; height: 100%; border-radius: 999px; }
.sm-bar-success { background: linear-gradient(90deg, #3aa76d, #2f8f5b); }
.sm-bar-danger  { background: linear-gradient(90deg, #e8a33d, #d97b46); }
.sm-metric-pct { grid-row: 3; grid-column: 1 / span 2; font-size: .72rem; color: #98a6ad; margin-top: .2rem; }

.sm-status-pill {
    display: inline-flex; align-items: center; gap: .3rem;
    border-radius: 999px;
    padding: .22rem .7rem;
    font-size: .74rem;
    font-weight: 600;
    white-space: nowrap;
}
.sm-status-pill i { font-size: .9rem; }
.sm-status-submitted  { background: #e8effb; border: 1px solid #cfdff7; color: #2c5282; }
.sm-status-validated  { background: #e4f5ec; border: 1px solid #c6e8d5; color: #1e7d44; }
.sm-status-compliance { background: #fdf1e3; border: 1px solid #f6ddbd; color: #96490a; }
.sm-status-pending    { background: #fdeeee; border: 1px solid #f4d2d2; color: #b03a3a; }

.sm-status-note {
    display: block;
    margin-top: .25rem;
    font-size: .72rem;
    color: #98a6ad;
    white-space: normal;
}
.sm-status-remark {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    margin-top: .2rem;
    max-width: 280px;
    font-size: .74rem;
    font-style: italic;
    color: #96490a;
    white-space: normal;
}

.sm-actions { display: flex; flex-wrap: wrap; gap: .3rem; justify-content: flex-end; }
.sm-inline-form { display: inline; margin: 0; }

.sm-btn {
    display: inline-flex; align-items: center; gap: .35rem;
    border: 1px solid #dbe2ea;
    background: #fff;
    color: #4a5568;
    border-radius: 8px;
    padding: .38rem .78rem;
    font-size: .8rem;
    font-weight: 500;
    transition: all .15s ease;
}
.sm-btn:hover { background: #f4f7fb; border-color: #c3cfdd; color: #2c5282; text-decoration: none; }
.sm-btn-primary { background: #2c5282; border-color: #2c5282; color: #fff; }
.sm-btn-primary:hover { background: #24446b; border-color: #24446b; color: #fff; }
.sm-btn-success { background: #1e7d44; border-color: #1e7d44; color: #fff; }
.sm-btn-success:hover { background: #196636; border-color: #196636; color: #fff; }
.sm-btn-warning { background: #c2610f; border-color: #c2610f; color: #fff; }
.sm-btn-warning:hover { background: #a1500b; border-color: #a1500b; color: #fff; }

/* The table only exists as raw markup until DataTables initialises at the foot of
   the page. Keep it hidden behind a skeleton until then so the unpaginated,
   unstyled list never flashes. */
.sm-table-wrap { position: relative; min-height: 220px; }
.sm-table-wrap > table,
.sm-table-wrap > .dataTables_wrapper { visibility: hidden; }
.sm-table-wrap.is-ready { min-height: 0; }
.sm-table-wrap.is-ready > table,
.sm-table-wrap.is-ready > .dataTables_wrapper { visibility: visible; }
.sm-table-wrap.is-ready > .sm-table-skeleton { display: none; }

.sm-table-skeleton { padding: .5rem 0; }
.sm-table-skeleton span {
    display: block;
    height: 2.6rem;
    margin-bottom: .55rem;
    border-radius: 8px;
    background: linear-gradient(90deg, #f2f5f9 25%, #e9eef5 37%, #f2f5f9 63%);
    background-size: 400% 100%;
    animation: sm-shimmer 1.3s ease-in-out infinite;
}
.sm-table-skeleton span:first-child { height: 1.6rem; width: 40%; }
@keyframes sm-shimmer {
    0%   { background-position: 100% 50%; }
    100% { background-position: 0 50%; }
}
@media (prefers-reduced-motion: reduce) {
    .sm-table-skeleton span { animation: none; }
}

.sm-empty { text-align: center; padding: 3rem 1rem; color: #98a6ad; }
.sm-empty i { font-size: 2.75rem; opacity: .5; display: block; margin-bottom: .6rem; }
.sm-empty h5 { color: #5c6873; }

.sm-modal { border: none; border-radius: 14px; overflow: hidden; }
.sm-modal .modal-header { background: #f7f9fc; border-bottom: 1px solid #e9edf2; }
.sm-modal .modal-title { font-size: .98rem; font-weight: 600; color: #313a46; display: flex; align-items: center; gap: .4rem; }
.sm-modal .modal-footer { background: #f7f9fc; border-top: 1px solid #e9edf2; }

@media (max-width: 767px) {
    .sm-hero { padding: 1.25rem; }
    .sm-hero-stats { width: 100%; }
    .sm-stat { flex: 1; min-width: 0; padding: .6rem .5rem; }
}
</style>

<noscript>
    <style>
        .sm-table-wrap > table { visibility: visible; }
        .sm-table-skeleton { display: none; }
    </style>
</noscript>

<script>
// Safety net for the skeleton above: if DataTables never initialises (script blocked,
// slow CDN-less asset, JS error further down the page) the list still has to appear.
setTimeout(function () {
    var wraps = document.querySelectorAll('.sm-table-wrap');
    for (var i = 0; i < wraps.length; i++) { wraps[i].className += ' is-ready'; }
}, 5000);
</script>
