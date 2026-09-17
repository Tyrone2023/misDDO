<?php include('templates/head.php'); ?>
<?php include('templates/header.php'); ?>

<?php
/*
 * Selective IER / RQA - vacancy picker.
 * One card per job vacancy; opening a card goes to its selection rounds.
 */
$rs_h = static function ($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};

$vacancies   = $vacancies ?? array();
$withBatches = 0;
$totalPicked = 0;
foreach ($vacancies as $v) {
    if ((int) $v->batch_total > 0) {
        $withBatches++;
    }
    $totalPicked += (int) $v->picked_total;
}
?>

<style>
    .rs-page { --rs-ink:#132c4a; --rs-muted:#6b7b91; --rs-line:#e5eaf2; --rs-blue:#2457d6; --rs-soft:#f6f9fd; }
    .rs-page .rs-hero { background:linear-gradient(135deg,#ffffff 0%,#f4f8ff 100%); border:1px solid var(--rs-line); border-radius:18px; box-shadow:0 6px 24px rgba(24,52,88,.05); display:flex; flex-wrap:wrap; gap:14px; justify-content:space-between; padding:22px 24px; }
    .rs-page .rs-eyebrow { color:#7b8ca3; font-size:10px; font-weight:800; letter-spacing:.12em; text-transform:uppercase; }
    .rs-page .rs-title { color:var(--rs-ink); font-size:25px; font-weight:800; letter-spacing:-.4px; margin:4px 0 0; }
    .rs-page .rs-subtitle { color:var(--rs-muted); font-size:13px; margin:5px 0 0; max-width:640px; }
    .rs-page .rs-hero-stats { display:flex; gap:26px; align-items:center; }
    .rs-page .rs-hero-stat strong { color:var(--rs-ink); display:block; font-size:22px; font-weight:800; line-height:1.1; }
    .rs-page .rs-hero-stat span { color:#8494a8; font-size:10px; font-weight:700; letter-spacing:.06em; text-transform:uppercase; }
    .rs-page .rs-section-head { align-items:center; display:flex; flex-wrap:wrap; gap:12px; justify-content:space-between; margin:22px 2px 12px; }
    .rs-page .rs-section-title { color:var(--rs-ink); font-size:17px; font-weight:800; margin:0; }
    .rs-page .rs-section-sub { color:var(--rs-muted); font-size:12px; margin:2px 0 0; }
    .rs-page .rs-tools { align-items:center; display:flex; flex-wrap:wrap; gap:8px; }
    .rs-page .rs-filter { position:relative; width:260px; }
    .rs-page .rs-filter i { color:#93a0b2; left:12px; position:absolute; top:9px; }
    .rs-page .rs-filter input { background:#fff; border:1px solid #d9e1ec; border-radius:9px; font-size:13px; height:36px; padding-left:34px; width:100%; }
    .rs-page .rs-filter input:focus { border-color:var(--rs-blue); box-shadow:0 0 0 3px rgba(36,87,214,.1); outline:none; }
    .rs-page .rs-toggle { align-items:center; border:1px solid #dbe3ee; border-radius:9px; color:#3d5876; display:inline-flex; font-size:12px; font-weight:650; gap:6px; height:36px; padding:0 13px; }
    .rs-page .rs-toggle:hover { background:#f2f6fd; border-color:#b9cbe8; color:var(--rs-blue); text-decoration:none; }
    .rs-page .rs-toggle.is-on { background:#eaf0ff; border-color:#b9cbe8; color:#2c55b5; }
    .rs-page .rs-grid { display:grid; gap:16px; grid-template-columns:repeat(auto-fill,minmax(340px,1fr)); }
    .rs-page .rs-vac { background:#fff; border:1px solid var(--rs-line); border-radius:16px; box-shadow:0 4px 18px rgba(31,58,91,.045); display:flex; flex-direction:column; overflow:hidden; transition:transform .16s ease,box-shadow .16s ease,border-color .16s ease; }
    .rs-page .rs-vac:hover { border-color:#c9d8ef; box-shadow:0 12px 28px rgba(24,52,88,.1); transform:translateY(-2px); }
    .rs-page .rs-vac-head { padding:16px 18px 14px; }
    .rs-page .rs-tagline { align-items:center; display:flex; flex-wrap:wrap; gap:6px; margin-bottom:8px; }
    .rs-page .rs-chip { border-radius:6px; font-size:9px; font-weight:800; letter-spacing:.06em; padding:4px 8px; text-transform:uppercase; }
    .rs-page .rs-chip-group { background:#eaf0ff; color:#2c55b5; }
    .rs-page .rs-chip-type { background:#e6f5ef; color:#1c7a55; }
    .rs-page .rs-chip-plain { background:#f1f4f9; color:#65758c; }
    .rs-page .rs-chip-live { background:#e9f7ef; color:#1c7a55; }
    .rs-page .rs-chip-archived { background:#f4f1f8; color:#6b5c8a; }
    .rs-page .rs-vac-name { color:var(--rs-ink); font-size:16px; font-weight:800; line-height:1.3; margin:0; }
    .rs-page .rs-vac-type { color:#5c7188; font-size:13px; font-weight:650; }
    .rs-page .rs-stats { display:grid; gap:7px; grid-template-columns:repeat(3,1fr); padding:0 18px; }
    .rs-page .rs-stat { background:var(--rs-soft); border:1px solid #eef2f8; border-radius:11px; padding:9px 4px; text-align:center; }
    .rs-page .rs-stat strong { color:var(--rs-ink); display:block; font-size:17px; font-weight:800; line-height:1.2; }
    .rs-page .rs-stat span { color:#8494a8; font-size:9px; font-weight:700; letter-spacing:.04em; text-transform:uppercase; }
    .rs-page .rs-stat.is-on { background:#eef5ff; border-color:#d9e6fb; }
    .rs-page .rs-stat.is-on strong { color:#2c55b5; }
    .rs-page .rs-vac-foot { margin-top:auto; padding:14px 18px 16px; }
    .rs-page .rs-open { align-items:center; background:var(--rs-blue); border:1px solid var(--rs-blue); border-radius:9px; color:#fff; display:flex; font-size:13px; font-weight:700; gap:6px; justify-content:center; padding:9px 12px; transition:all .14s ease; width:100%; }
    .rs-page .rs-open:hover { background:#1c48b8; border-color:#1c48b8; color:#fff; text-decoration:none; }
    .rs-page .rs-empty { background:#fff; border:1px solid var(--rs-line); border-radius:16px; color:var(--rs-muted); padding:52px 20px; text-align:center; }
    @media (max-width:575px) {
        .rs-page .rs-hero { padding:18px; }
        .rs-page .rs-filter { width:100%; }
        .rs-page .rs-grid { grid-template-columns:1fr; }
    }
</style>

<div class="content-page rs-page">
    <div class="content">
        <div class="container-fluid">

            <div class="rs-hero">
                <div>
                    <div class="rs-eyebrow">Recruitment</div>
                    <h2 class="rs-title">Selective IER / RQA</h2>
                    <p class="rs-subtitle">For a second (or later) deliberation on the same vacancy. Open a vacancy, pick only the applicants included in that round, then print the IER and RQA of those applicants alone.</p>
                </div>
                <div class="rs-hero-stats">
                    <div class="rs-hero-stat"><strong><?= count($vacancies); ?></strong><span>Vacancies</span></div>
                    <div class="rs-hero-stat"><strong><?= $withBatches; ?></strong><span>In Progress</span></div>
                    <div class="rs-hero-stat"><strong><?= $totalPicked; ?></strong><span>Picked</span></div>
                </div>
            </div>

            <?php if ($this->session->flashdata('success')) : ?>
                <div class="alert alert-success mt-3"><?= $this->session->flashdata('success'); ?></div>
            <?php endif; ?>
            <?php if ($this->session->flashdata('danger')) : ?>
                <div class="alert alert-danger mt-3"><?= $this->session->flashdata('danger'); ?></div>
            <?php endif; ?>

            <div class="rs-section-head">
                <div>
                    <h5 class="rs-section-title">Choose a vacancy</h5>
                    <p class="rs-section-sub"><span id="rs-visible"><?= count($vacancies); ?></span> of <?= count($vacancies); ?> shown<?= empty($all) ? ' &middot; posted vacancies and any with a round already started' : ' &middot; every vacancy, archived included'; ?></p>
                </div>
                <div class="rs-tools">
                    <div class="rs-filter">
                        <i class="mdi mdi-magnify"></i>
                        <input type="search" id="rs-search" placeholder="Filter by title, group, year" autocomplete="off">
                    </div>
                    <?php if (!empty($all)) : ?>
                        <a href="<?= base_url(); ?>Reselection" class="rs-toggle is-on"><i class="mdi mdi-filter-check-outline"></i> Showing all</a>
                    <?php else : ?>
                        <a href="<?= base_url(); ?>Reselection?all=1" class="rs-toggle"><i class="mdi mdi-archive-outline"></i> Show archived too</a>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (empty($vacancies)) : ?>
                <div class="rs-empty">
                    <i class="mdi mdi-briefcase-search-outline" style="font-size:40px"></i>
                    <h5 class="mt-2">No vacancy to show</h5>
                    <p class="mb-0 small">Posted vacancies appear here automatically.</p>
                </div>
            <?php else : ?>
                <div class="rs-grid" id="rs-grid">
                    <?php foreach ($vacancies as $v) :
                        $jobID  = (int) $v->jobID;
                        $search = strtolower($v->jobTitle . ' ' . $v->rs_group . ' ' . $v->rs_type . ' ' . $v->sy . ' ' . $v->itemNo);
                    ?>
                        <div class="rs-vac" data-search="<?= $rs_h($search); ?>">
                            <div class="rs-vac-head">
                                <div class="rs-tagline">
                                    <span class="rs-chip rs-chip-group"><?= $rs_h($v->rs_group); ?></span>
                                    <?php if ($v->rs_type !== '') : ?><span class="rs-chip rs-chip-type"><?= $rs_h($v->rs_type); ?></span><?php endif; ?>
                                    <span class="rs-chip rs-chip-plain">FY <?= $rs_h($v->sy); ?></span>
                                    <?php if ($v->jvStatus === 'Open') : ?>
                                        <span class="rs-chip rs-chip-live">Posted</span>
                                    <?php else : ?>
                                        <span class="rs-chip rs-chip-archived">Archived</span>
                                    <?php endif; ?>
                                </div>
                                <h6 class="rs-vac-name"><?= $rs_h($v->jobTitle); ?><?= $v->rs_type !== '' ? ' <span class="rs-vac-type">&mdash; ' . $rs_h($v->rs_type) . '</span>' : ''; ?></h6>
                            </div>
                            <div class="rs-stats">
                                <div class="rs-stat"><strong><?= (int) $v->applicant_total; ?></strong><span>Applicants</span></div>
                                <div class="rs-stat <?= (int) $v->batch_total > 0 ? 'is-on' : ''; ?>"><strong><?= (int) $v->batch_total; ?></strong><span>Rounds</span></div>
                                <div class="rs-stat <?= (int) $v->picked_total > 0 ? 'is-on' : ''; ?>"><strong><?= (int) $v->picked_total; ?></strong><span>Picked</span></div>
                            </div>
                            <div class="rs-vac-foot">
                                <a href="<?= base_url(); ?>Reselection/index/<?= $jobID; ?>" class="rs-open">
                                    <i class="mdi mdi-folder-open-outline"></i>
                                    <?= (int) $v->batch_total > 0 ? 'Open Selection Rounds' : 'Start a Selection Round'; ?>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="rs-empty mt-2" id="rs-nomatch" style="display:none">
                    <i class="mdi mdi-magnify-close" style="font-size:34px"></i>
                    <h6 class="mt-2 mb-0">No vacancy matches that filter</h6>
                </div>
            <?php endif; ?>

        </div>
    </div>

    <?php include('templates/footer.php'); ?>
</div>

<script>
(function () {
    var filter = document.getElementById('rs-search');
    var grid = document.getElementById('rs-grid');
    var counter = document.getElementById('rs-visible');
    var noMatch = document.getElementById('rs-nomatch');
    if (!filter || !grid) return;

    filter.addEventListener('input', function () {
        var needle = filter.value.trim().toLowerCase();
        var visible = 0;
        grid.querySelectorAll('.rs-vac').forEach(function (card) {
            var hit = !needle || (card.getAttribute('data-search') || '').indexOf(needle) !== -1;
            card.style.display = hit ? '' : 'none';
            if (hit) visible += 1;
        });
        if (counter) counter.textContent = visible;
        if (noMatch) noMatch.style.display = visible ? 'none' : '';
    });
})();
</script>
