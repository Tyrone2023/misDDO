<?php include('templates/head.php'); ?>
<?php include('templates/header.php'); ?>

<?php
/*
 * Selective IER / RQA - selection rounds of one vacancy.
 * A round holds the applicants manually picked for a later deliberation;
 * the IER / RQA reports read it through ?batch={id}.
 */
$rs_h = static function ($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};

$jobID     = (int) $job->jobID;
$jobTypes  = isset($job_types) && is_array($job_types) ? $job_types : array();
$groupName = (int) $job->promotion === 1 ? 'Promotion' : ($groups[(int) $job->position] ?? 'Vacancy');
$typeLabel = $jobTypes[(int) $job->job_type] ?? '';
$totalPicked = 0;
foreach ($batches as $b) {
    $totalPicked += (int) $b->total;
}
?>

<style>
    .rs-page { --rs-ink:#132c4a; --rs-muted:#6b7b91; --rs-line:#e5eaf2; --rs-blue:#2457d6; --rs-soft:#f6f9fd; }
    .rs-page .rs-hero { background:linear-gradient(135deg,#ffffff 0%,#f4f8ff 100%); border:1px solid var(--rs-line); border-radius:18px; box-shadow:0 6px 24px rgba(24,52,88,.05); display:flex; flex-wrap:wrap; gap:14px; justify-content:space-between; padding:22px 24px; }
    .rs-page .rs-back { align-items:center; color:#5c7188; display:inline-flex; font-size:12px; font-weight:650; gap:5px; margin-bottom:8px; }
    .rs-page .rs-back:hover { color:var(--rs-blue); text-decoration:none; }
    .rs-page .rs-title { color:var(--rs-ink); font-size:24px; font-weight:800; letter-spacing:-.4px; margin:0; }
    .rs-page .rs-tagline { align-items:center; display:flex; flex-wrap:wrap; gap:6px; margin-top:8px; }
    .rs-page .rs-chip { border-radius:6px; font-size:9px; font-weight:800; letter-spacing:.06em; padding:4px 8px; text-transform:uppercase; }
    .rs-page .rs-chip-group { background:#eaf0ff; color:#2c55b5; }
    .rs-page .rs-chip-type { background:#e6f5ef; color:#1c7a55; }
    .rs-page .rs-chip-plain { background:#f1f4f9; color:#65758c; }
    .rs-page .rs-hero-stats { display:flex; gap:26px; align-items:center; }
    .rs-page .rs-hero-stat strong { color:var(--rs-ink); display:block; font-size:22px; font-weight:800; line-height:1.1; }
    .rs-page .rs-hero-stat span { color:#8494a8; font-size:10px; font-weight:700; letter-spacing:.06em; text-transform:uppercase; }
    .rs-page .rs-card { background:#fff; border:1px solid var(--rs-line); border-radius:16px; box-shadow:0 4px 18px rgba(31,58,91,.045); margin-top:18px; }
    .rs-page .rs-card-head { align-items:center; border-bottom:1px solid #eef2f8; display:flex; flex-wrap:wrap; gap:10px; justify-content:space-between; padding:16px 20px; }
    .rs-page .rs-card-title { color:var(--rs-ink); font-size:16px; font-weight:800; margin:0; }
    .rs-page .rs-card-sub { color:var(--rs-muted); font-size:12px; margin:2px 0 0; }
    .rs-page .rs-new { align-items:center; display:flex; flex-wrap:wrap; gap:8px; }
    .rs-page .rs-new input { background:#fff; border:1px solid #d9e1ec; border-radius:9px; font-size:13px; height:38px; padding:0 12px; width:230px; }
    .rs-page .rs-new input:focus { border-color:var(--rs-blue); box-shadow:0 0 0 3px rgba(36,87,214,.1); outline:none; }
    .rs-page .rs-btn { align-items:center; border:1px solid #dbe3ee; border-radius:9px; color:#3d5876; display:inline-flex; font-size:13px; font-weight:650; gap:6px; padding:8px 13px; transition:all .14s ease; }
    .rs-page .rs-btn:hover { background:#f2f6fd; border-color:#b9cbe8; color:var(--rs-blue); text-decoration:none; }
    .rs-page .rs-btn-primary { background:var(--rs-blue); border-color:var(--rs-blue); color:#fff; }
    .rs-page .rs-btn-primary:hover { background:#1c48b8; border-color:#1c48b8; color:#fff; }
    .rs-page .rs-btn-quiet { border-color:transparent; color:#93a0b2; }
    .rs-page .rs-btn-quiet:hover { background:#fdf1f0; border-color:#f6ded9; color:#c0453c; }
    .rs-page .rs-round { align-items:center; border-bottom:1px solid #f1f5fa; display:flex; flex-wrap:wrap; gap:12px; padding:16px 20px; }
    .rs-page .rs-round:last-child { border-bottom:0; }
    .rs-page .rs-round-no { align-items:center; background:#eef4ff; border-radius:12px; color:#2c55b5; display:flex; flex-direction:column; height:46px; justify-content:center; min-width:46px; }
    .rs-page .rs-round-no strong { font-size:16px; font-weight:800; line-height:1; }
    .rs-page .rs-round-no span { font-size:8px; font-weight:700; letter-spacing:.08em; text-transform:uppercase; }
    .rs-page .rs-round-main { flex:1 1 240px; min-width:0; }
    .rs-page .rs-round-name { color:var(--rs-ink); font-size:15px; font-weight:700; margin:0; }
    .rs-page .rs-round-meta { color:var(--rs-muted); font-size:12px; margin:3px 0 0; }
    .rs-page .rs-picked { background:#e9f7ef; border-radius:20px; color:#1c7a55; font-size:11px; font-weight:800; padding:3px 10px; }
    .rs-page .rs-picked.is-zero { background:#fdf3e8; color:#95610b; }
    .rs-page .rs-round-acts { align-items:center; display:flex; flex-wrap:wrap; gap:7px; margin-left:auto; }
    .rs-page .dropdown-menu { border:1px solid #e3e9f2; border-radius:10px; box-shadow:0 10px 26px rgba(24,52,88,.12); font-size:13px; padding:6px; }
    .rs-page .dropdown-item { border-radius:7px; color:#3d5876; padding:7px 10px; }
    .rs-page .dropdown-item:hover { background:#f2f6fd; color:var(--rs-blue); }
    .rs-page .rs-empty { color:var(--rs-muted); padding:44px 20px; text-align:center; }
    .rs-page .rs-hint { color:var(--rs-muted); font-size:12px; margin:10px 2px 0; }
    @media (max-width:575px) {
        .rs-page .rs-hero { padding:18px; }
        .rs-page .rs-new input { width:100%; }
        .rs-page .rs-round-acts { margin-left:0; width:100%; }
    }
</style>

<div class="content-page rs-page">
    <div class="content">
        <div class="container-fluid">

            <div class="rs-hero">
                <div>
                    <a href="<?= base_url(); ?>Reselection" class="rs-back"><i class="mdi mdi-arrow-left"></i> All vacancies</a>
                    <h2 class="rs-title"><?= $rs_h($job->jobTitle); ?></h2>
                    <div class="rs-tagline">
                        <span class="rs-chip rs-chip-group"><?= $rs_h($groupName); ?></span>
                        <?php if ($typeLabel !== '') : ?><span class="rs-chip rs-chip-type"><?= $rs_h($typeLabel); ?></span><?php endif; ?>
                        <span class="rs-chip rs-chip-plain">FY <?= $rs_h($job->sy); ?></span>
                    </div>
                </div>
                <div class="rs-hero-stats">
                    <div class="rs-hero-stat"><strong><?= count($batches); ?></strong><span>Rounds</span></div>
                    <div class="rs-hero-stat"><strong><?= $totalPicked; ?></strong><span>Picked</span></div>
                </div>
            </div>

            <?php if ($this->session->flashdata('success')) : ?>
                <div class="alert alert-success mt-3"><?= $this->session->flashdata('success'); ?></div>
            <?php endif; ?>
            <?php if ($this->session->flashdata('danger')) : ?>
                <div class="alert alert-danger mt-3"><?= $this->session->flashdata('danger'); ?></div>
            <?php endif; ?>

            <div class="rs-card">
                <div class="rs-card-head">
                    <div>
                        <h5 class="rs-card-title">Selection rounds</h5>
                        <p class="rs-card-sub">Each round prints its own IER and RQA, covering only the applicants you pick.</p>
                    </div>
                    <form method="post" action="<?= base_url(); ?>Reselection/create" class="rs-new">
                        <input type="hidden" name="jobID" value="<?= $jobID; ?>">
                        <input type="hidden" name="round_no" value="<?= (int) $next; ?>">
                        <input type="text" name="batch_name" maxlength="200" required
                               value="<?= $rs_h($next == 2 ? 'Second Selection' : 'Round ' . (int) $next . ' Selection'); ?>">
                        <button type="submit" class="rs-btn rs-btn-primary"><i class="mdi mdi-plus"></i> New Round</button>
                    </form>
                </div>

                <?php if (empty($batches)) : ?>
                    <div class="rs-empty">
                        <i class="mdi mdi-account-multiple-check-outline" style="font-size:38px"></i>
                        <h6 class="mt-2 mb-1">No round yet</h6>
                        <p class="mb-0 small">Name the round above and create it, then tick the applicants it covers.</p>
                    </div>
                <?php else : ?>
                    <?php foreach ($batches as $b) :
                        $bid    = (int) $b->id;
                        $picked = (int) $b->total;
                    ?>
                        <div class="rs-round">
                            <div class="rs-round-no">
                                <strong><?= (int) $b->round_no; ?></strong>
                                <span>Round</span>
                            </div>
                            <div class="rs-round-main">
                                <p class="rs-round-name">
                                    <?= $rs_h($b->batch_name); ?>
                                    <span class="rs-picked <?= $picked === 0 ? 'is-zero' : ''; ?>"><?= $picked; ?> picked</span>
                                </p>
                                <p class="rs-round-meta">
                                    <?= !empty($b->created_by_name) ? $rs_h($b->created_by_name) . ' &middot; ' : ''; ?>
                                    <?= date('M d, Y', strtotime($b->created_at)); ?>
                                    <?= !empty($b->remarks) ? ' &middot; ' . $rs_h($b->remarks) : ''; ?>
                                </p>
                            </div>
                            <div class="rs-round-acts">
                                <a class="rs-btn rs-btn-primary" href="<?= base_url(); ?>Reselection/select/<?= $bid; ?>">
                                    <i class="mdi mdi-checkbox-marked-outline"></i> <?= $picked === 0 ? 'Pick Applicants' : 'Edit Picks'; ?>
                                </a>

                                <?php if ($picked > 0 && !empty($links[$bid])) : ?>
                                    <div class="dropdown">
                                        <a class="rs-btn dropdown-toggle" href="#" data-toggle="dropdown" aria-expanded="false">
                                            <i class="mdi mdi-file-chart-outline"></i> IER
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <?php foreach ($links[$bid]['ier'] as $link) : ?>
                                                <a class="dropdown-item" target="_blank" href="<?= $link['url']; ?>"><?= $link['label']; ?></a>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    <div class="dropdown">
                                        <a class="rs-btn dropdown-toggle" href="#" data-toggle="dropdown" aria-expanded="false">
                                            <i class="mdi mdi-file-document-outline"></i> RQA
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <?php foreach ($links[$bid]['rqa'] as $link) : ?>
                                                <a class="dropdown-item" target="_blank" href="<?= $link['url']; ?>"><?= $link['label']; ?></a>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <a class="rs-btn rs-btn-quiet" title="Remove this round"
                                   onclick="return confirm('Remove this round? The applicants and their ratings are not deleted.')"
                                   href="<?= base_url(); ?>Reselection/remove/<?= $bid; ?>">
                                    <i class="mdi mdi-trash-can-outline"></i>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <p class="rs-hint">
                <i class="mdi mdi-information-outline"></i>
                The reports keep their own rules (rated, qualified, 50 points and above) - a round only narrows them to the applicants you picked.
                The full lists stay on <a target="_blank" href="<?= base_url(); ?>Pages/rqa_list/<?= $rs_h($job->sy); ?>?jobID=<?= $jobID; ?>&jobTitle=<?= urlencode($job->jobTitle); ?>">the vacancy's RQA page</a>.
            </p>

        </div>
    </div>

    <?php include('templates/footer.php'); ?>
</div>
