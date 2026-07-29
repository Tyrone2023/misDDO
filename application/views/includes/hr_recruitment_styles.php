<style>
/* ---- Shared shell for the HR recruitment screens (Job Vacancies / Positions Settings) ---- */

.hrp-hero {
    background: linear-gradient(120deg, #1f3a5f 0%, #2c5282 55%, #3182ce 100%);
    border-radius: 14px;
    padding: 1.6rem 1.9rem;
    margin-bottom: 1.4rem;
    color: #fff;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 1.25rem;
    box-shadow: 0 10px 26px rgba(31, 58, 95, .22);
}
.hrp-hero-eyebrow {
    display: inline-flex; align-items: center; gap: .35rem;
    background: rgba(255,255,255,.18);
    border-radius: 999px;
    padding: .22rem .7rem;
    font-size: .7rem; font-weight: 600;
    text-transform: uppercase; letter-spacing: .06em;
}
.hrp-hero-title { margin: .6rem 0 .3rem; font-weight: 600; color: #fff; display: flex; align-items: center; gap: .5rem; font-size: 1.4rem; }
.hrp-hero-sub { margin: 0; opacity: .85; font-size: .875rem; max-width: 62ch; }
.hrp-hero-sub strong { color: #fff; }
.hrp-hero-stats { display: flex; gap: .75rem; flex-wrap: wrap; }
.hrp-stat {
    background: rgba(255,255,255,.13);
    border: 1px solid rgba(255,255,255,.18);
    border-radius: 11px;
    padding: .7rem 1.1rem;
    min-width: 104px;
    text-align: center;
}
.hrp-stat-value { display: block; font-size: 1.45rem; font-weight: 600; line-height: 1.15; }
.hrp-stat-label { display: block; font-size: .68rem; text-transform: uppercase; letter-spacing: .06em; opacity: .8; }
.hrp-dotsep { margin: 0 .4rem; opacity: .45; }

.hrp-card {
    background: #fff;
    border: 1px solid #e9edf2;
    border-radius: 14px;
    padding: 1.35rem;
    box-shadow: 0 2px 10px rgba(16, 30, 54, .05);
    margin-bottom: 1.4rem;
}
.hrp-card-head {
    display: flex; align-items: flex-start; justify-content: space-between;
    flex-wrap: wrap; gap: .75rem;
    margin-bottom: 1.1rem;
}
.hrp-card-title { margin: 0; font-size: 1.05rem; font-weight: 600; color: #313a46; }
.hrp-card-sub { margin: .25rem 0 0; font-size: .82rem; color: #98a6ad; }
.hrp-card-actions { display: flex; gap: .45rem; flex-wrap: wrap; align-items: center; }

/* ---- buttons ---- */
.hrp-btn {
    display: inline-flex; align-items: center; gap: .4rem;
    border: 1px solid #e4e9f0;
    background: #f7f9fc;
    color: #5c6873;
    border-radius: 8px;
    padding: .45rem .9rem;
    font-size: .82rem;
    font-weight: 500;
    line-height: 1.3;
    cursor: pointer;
    transition: all .15s ease;
}
.hrp-btn:hover { background: #eef2f8; color: #313a46; text-decoration: none; }
.hrp-btn-primary { background: #2c5282; border-color: #2c5282; color: #fff; }
.hrp-btn-primary:hover { background: #24446c; border-color: #24446c; color: #fff; }
.hrp-btn-success { background: #1e7d44; border-color: #1e7d44; color: #fff; }
.hrp-btn-success:hover { background: #196a39; border-color: #196a39; color: #fff; }
.hrp-btn-info { background: #1a7f8e; border-color: #1a7f8e; color: #fff; }
.hrp-btn-info:hover { background: #146874; border-color: #146874; color: #fff; }
.hrp-btn-teal { background: #0f766e; border-color: #0f766e; color: #fff; }
.hrp-btn-teal:hover { background: #0c5f59; border-color: #0c5f59; color: #fff; }
.hrp-btn-purple { background: #5b3fb0; border-color: #5b3fb0; color: #fff; }
.hrp-btn-purple:hover { background: #4a3390; border-color: #4a3390; color: #fff; }
.hrp-btn-warning { background: #c07d15; border-color: #c07d15; color: #fff; }
.hrp-btn-warning:hover { background: #a06811; border-color: #a06811; color: #fff; }
.hrp-btn-danger { background: #b03a3a; border-color: #b03a3a; color: #fff; }
.hrp-btn-danger:hover { background: #932e2e; border-color: #932e2e; color: #fff; }
.hrp-btn-ghost-danger { color: #b03a3a; border-color: #f0dcdc; background: #fdf5f5; }
.hrp-btn-ghost-danger:hover { background: #f8e7e7; color: #8f2c2c; }
.hrp-btn-sm { padding: .3rem .65rem; font-size: .78rem; border-radius: 7px; }

/* row actions, rendered inside a modal instead of a dropdown */
.hrp-actions-list { text-align: left; }
.hrp-actions-list .hrp-action-item {
    display: flex;
    align-items: center;
    gap: .6rem;
    padding: .58rem .75rem;
    margin-bottom: .35rem;
    border: 1px solid #e9edf2;
    border-radius: 9px;
    background: #fff;
    color: #5c6873;
    font-size: .87rem;
    line-height: 1.35;
    transition: all .15s ease;
}
.hrp-actions-list .hrp-action-item:hover {
    background: #f1f4f8;
    border-color: #dbe3ee;
    color: #313a46;
    text-decoration: none;
    transform: translateX(2px);
}
.hrp-actions-list .hrp-action-item i { font-size: 1.05rem; width: 1.2rem; text-align: center; flex: 0 0 auto; }
.hrp-actions-list .hrp-item-count {
    margin-left: auto;
    background: #e8f0fb;
    color: #2c5282;
    border-radius: 999px;
    padding: .04rem .48rem;
    font-size: .72rem;
    font-weight: 600;
}
.hrp-actions-list .hrp-action-group {
    font-size: .66rem;
    text-transform: uppercase;
    letter-spacing: .06em;
    color: #98a6ad;
    font-weight: 600;
    margin: .85rem 0 .4rem;
}
.hrp-actions-list .hrp-action-group:first-child { margin-top: 0; }
.hrp-actions-list .hrp-action-sep { display: none; }

.hrp-i-blue   { color: #2c5282; }
.hrp-i-green  { color: #1e7d44; }
.hrp-i-amber  { color: #a86c14; }
.hrp-i-purple { color: #5b3fb0; }
.hrp-i-red    { color: #b03a3a; }
.hrp-i-teal   { color: #0f766e; }
.hrp-i-grey   { color: #7b8794; }

/* icon-only action buttons in table rows */
.hrp-actions { display: inline-flex; flex-wrap: wrap; gap: .3rem; }
.hrp-ico {
    display: inline-flex; align-items: center; justify-content: center;
    width: 32px; height: 32px;
    border-radius: 8px;
    border: 1px solid #e4e9f0;
    background: #f7f9fc;
    color: #5c6873;
    font-size: .95rem;
    transition: all .15s ease;
}
.hrp-ico:hover { text-decoration: none; transform: translateY(-1px); }
.hrp-ico-blue   { background: #e8f0fb; border-color: #d6e4f7; color: #2c5282; }
.hrp-ico-blue:hover   { background: #d6e4f7; color: #22405f; }
.hrp-ico-green  { background: #e6f4ec; border-color: #d2ebdd; color: #1e7d44; }
.hrp-ico-green:hover  { background: #d2ebdd; color: #176034; }
.hrp-ico-amber  { background: #fdf3e2; border-color: #f7e5c6; color: #a86c14; }
.hrp-ico-amber:hover  { background: #f7e5c6; color: #85560f; }
.hrp-ico-purple { background: #f0ebfb; border-color: #e2d9f7; color: #5b3fb0; }
.hrp-ico-purple:hover { background: #e2d9f7; color: #48318b; }
.hrp-ico-red    { background: #fdf5f5; border-color: #f0dcdc; color: #b03a3a; }
.hrp-ico-red:hover    { background: #f8e7e7; color: #8f2c2c; }
.hrp-ico-grey   { background: #f1f4f8; border-color: #e4e9f0; color: #5c6873; }
.hrp-ico-grey:hover   { background: #e4e9f0; color: #313a46; }

.hrp-ico-badge {
    position: absolute;
    top: -6px; right: -6px;
    min-width: 17px; height: 17px;
    padding: 0 4px;
    border-radius: 999px;
    background: #b03a3a;
    color: #fff;
    font-size: .62rem;
    font-weight: 600;
    line-height: 17px;
    text-align: center;
}
.hrp-ico-wrap { position: relative; display: inline-flex; }

/* ---- tabs / filter pills ---- */
.hrp-tabs { display: flex; gap: .4rem; flex-wrap: wrap; margin-bottom: 1.1rem; }
.hrp-tab {
    display: inline-flex; align-items: center; gap: .4rem;
    border: 1px solid #e4e9f0;
    background: #f7f9fc;
    color: #5c6873;
    border-radius: 999px;
    padding: .38rem .9rem;
    font-size: .82rem;
    font-weight: 500;
    cursor: pointer;
    transition: all .15s ease;
}
.hrp-tab:hover { background: #eef2f8; color: #313a46; text-decoration: none; }
.hrp-tab-count {
    background: #e4e9f0; color: #5c6873;
    border-radius: 999px; padding: .05rem .45rem;
    font-size: .72rem; font-weight: 600;
}
.hrp-tab.is-active { background: #2c5282; border-color: #2c5282; color: #fff; }
.hrp-tab.is-active .hrp-tab-count { background: rgba(255,255,255,.24); color: #fff; }

/* ---- tables ---- */
.hrp-table { border-collapse: separate !important; border-spacing: 0; width: 100%; }
.hrp-table thead th {
    border: none !important;
    border-bottom: 1px solid #e9edf2 !important;
    background: transparent;
    color: #7b8794;
    font-size: .7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .06em;
    padding: .7rem .75rem !important;
    white-space: nowrap;
}
.hrp-table tbody td {
    border: none !important;
    border-bottom: 1px solid #f1f4f8 !important;
    padding: .75rem !important;
    vertical-align: middle;
}
.hrp-table tbody tr:hover { background: #f9fbfd; }

.hrp-title-cell { display: flex; align-items: center; gap: .7rem; }
.hrp-avatar {
    flex: 0 0 auto;
    width: 36px; height: 36px;
    border-radius: 10px;
    background: linear-gradient(135deg, #e3edfa, #cfe0f7);
    color: #2c5282;
    font-weight: 600;
    font-size: .82rem;
    display: inline-flex; align-items: center; justify-content: center;
}
.hrp-title-text { display: flex; flex-direction: column; line-height: 1.35; min-width: 0; }
.hrp-title-name { font-weight: 600; color: #313a46; white-space: normal; }
.hrp-title-sub { font-size: .74rem; color: #98a6ad; }

.hrp-chip {
    display: inline-flex; align-items: center; gap: .3rem;
    background: #f1f4f8;
    border: 1px solid #e4e9f0;
    border-radius: 999px;
    padding: .16rem .6rem;
    font-size: .74rem;
    font-weight: 500;
    color: #4a5568;
    white-space: nowrap;
}
.hrp-chip-blue   { background: #e8f0fb; border-color: #d6e4f7; color: #2c5282; }
.hrp-chip-green  { background: #e6f4ec; border-color: #d2ebdd; color: #1e7d44; }
.hrp-chip-amber  { background: #fdf3e2; border-color: #f7e5c6; color: #a86c14; }
.hrp-chip-purple { background: #f0ebfb; border-color: #e2d9f7; color: #5b3fb0; }
.hrp-chip-grey   { background: #f1f4f8; border-color: #e4e9f0; color: #7b8794; }
.hrp-chip-red    { background: #fdf5f5; border-color: #f0dcdc; color: #b03a3a; }

.hrp-mono {
    font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
    font-size: .78rem;
    color: #4a5568;
}
.hrp-muted { color: #98a6ad; }
.hrp-date { color: #5c6873; font-size: .84rem; white-space: nowrap; }

.hrp-empty {
    text-align: center;
    padding: 2.75rem 1rem;
    color: #98a6ad;
}
.hrp-empty i { font-size: 2.4rem; display: block; margin-bottom: .5rem; opacity: .5; }

/* ---- alerts ---- */
.hrp-alert {
    display: flex; align-items: flex-start; gap: .6rem;
    border-radius: 11px;
    padding: .8rem 1rem;
    margin-bottom: 1.1rem;
    font-size: .86rem;
    border: 1px solid transparent;
}
.hrp-alert i { font-size: 1.05rem; line-height: 1.3; }
.hrp-alert-success { background: #e6f4ec; border-color: #d2ebdd; color: #176034; }
.hrp-alert-danger  { background: #fdf5f5; border-color: #f0dcdc; color: #8f2c2c; }
.hrp-alert .close { margin-left: auto; opacity: .45; font-size: 1.1rem; }

/* ---- modals ---- */
.hrp-modal .modal-content { border: none; border-radius: 14px; overflow: hidden; box-shadow: 0 18px 44px rgba(16,30,54,.22); }
.hrp-modal .modal-header {
    background: linear-gradient(120deg, #1f3a5f 0%, #2c5282 100%);
    color: #fff;
    border-bottom: none;
    padding: 1.05rem 1.4rem;
}
.hrp-modal .modal-header .modal-title { color: #fff; font-weight: 600; font-size: 1rem; display: flex; align-items: center; gap: .5rem; }
.hrp-modal .modal-header .close { color: #fff; opacity: .8; text-shadow: none; }
.hrp-modal .modal-body { padding: 1.4rem; background: #fbfcfe; }
.hrp-modal .modal-footer { background: #fbfcfe; border-top: 1px solid #eef1f6; padding: .9rem 1.4rem; }

/* tighter variant for form-heavy modals */
.hrp-modal-compact .modal-header { padding: .8rem 1.15rem; }
.hrp-modal-compact .modal-body { padding: 1.05rem 1.15rem .4rem; }
.hrp-modal-compact .modal-footer { padding: .7rem 1.15rem; }
.hrp-modal-compact .hrp-field { margin-bottom: .7rem; }
.hrp-modal-compact .hrp-label { margin-bottom: .25rem; }
.hrp-modal-compact .form-control { padding: .4rem .7rem; font-size: .86rem; }
.hrp-modal-compact .select2-container--default .select2-selection--single { min-height: 34px; }
.hrp-modal-compact .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 26px; }
.hrp-modal-compact .select2-container--default .select2-selection--single .select2-selection__arrow { height: 32px; }

.hrp-field { margin-bottom: 1rem; }
.hrp-label {
    display: block;
    font-size: .72rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .05em;
    color: #7b8794;
    margin-bottom: .35rem;
}
.hrp-label .hrp-req { color: #b03a3a; }
.hrp-help { display: block; font-size: .74rem; color: #98a6ad; margin-top: .3rem; }
.hrp-control, .hrp-modal .form-control {
    border-radius: 9px;
    border: 1px solid #dfe5ee;
    height: auto;
    padding: .5rem .75rem;
    font-size: .88rem;
    color: #313a46;
    background-color: #fff;
}
.hrp-control:focus, .hrp-modal .form-control:focus {
    border-color: #2c5282;
    box-shadow: 0 0 0 3px rgba(44, 82, 130, .12);
}

/* select2 tuned to match the fields above */
.hrp-modal .select2-container--default .select2-selection--single,
.hrp-card .select2-container--default .select2-selection--single {
    height: auto;
    min-height: 38px;
    border-radius: 9px;
    border: 1px solid #dfe5ee;
    padding: .18rem .2rem;
}
.hrp-modal .select2-container--default .select2-selection--single .select2-selection__rendered,
.hrp-card .select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 30px;
    color: #313a46;
    font-size: .88rem;
    padding-left: .6rem;
}
.hrp-modal .select2-container--default .select2-selection--single .select2-selection__arrow,
.hrp-card .select2-container--default .select2-selection--single .select2-selection__arrow { height: 36px; }
.select2-container--open .select2-dropdown { border-color: #dfe5ee; border-radius: 9px; box-shadow: 0 12px 28px rgba(16,30,54,.12); }
.select2-container--default .select2-results__option--highlighted[aria-selected] { background-color: #2c5282; }
.select2-results__option { font-size: .87rem; }
.select2-container { width: 100% !important; }
.hrp-select2-group { font-size: .72rem; text-transform: uppercase; letter-spacing: .05em; color: #7b8794; }

/* datatables chrome */
.hrp-card div.dataTables_wrapper div.dataTables_filter input {
    border-radius: 8px;
    border: 1px solid #dfe5ee;
    padding: .35rem .7rem;
    font-size: .85rem;
}
.hrp-card div.dataTables_wrapper div.dataTables_length select {
    border-radius: 8px;
    border: 1px solid #dfe5ee;
    padding: .25rem 1.4rem .25rem .5rem;
    font-size: .85rem;
}
.hrp-card .dataTables_info, .hrp-card .dataTables_length label, .hrp-card .dataTables_filter label {
    font-size: .82rem;
    color: #7b8794;
}
.hrp-card .page-link { border-radius: 8px; margin: 0 2px; border-color: #e9edf2; color: #5c6873; font-size: .84rem; }
.hrp-card .page-item.active .page-link { background: #2c5282; border-color: #2c5282; }

@media (max-width: 767.98px) {
    .hrp-hero { padding: 1.25rem; }
    .hrp-hero-title { font-size: 1.15rem; }
    .hrp-stat { min-width: 88px; padding: .55rem .8rem; }
}
</style>
