<?php
$dashboard_h = static function ($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};

$assigned = $jobTypes ?? [];
$vacancies = $vacancies ?? [];
$counts = array_merge([
    'applicants' => 0,
    'submitted' => 0,
    'validated' => 0,
    'endorsed' => 0,
    'evaluated' => 0,
    'rated' => 0,
    'no_rater' => 0,
    'dq' => 0,
    'tagged' => 0,
    'untagged' => 0,
], $counts ?? []);
$jobTypeLabels = $jobTypeLabels ?? [];
$retentionCounts = $retentionCounts ?? [];
$examCounts = $examCounts ?? [];
$scoreCounts = $scoreCounts ?? [];
$positionGroups = [1 => 'Teaching', 2 => 'School Administration', 3 => 'Related Teaching', 4 => 'Non-Teaching', 5 => 'Promotion'];
?>

<style>
    .secretariat-dashboard { --sd-ink:#132c4a; --sd-muted:#6b7b91; --sd-line:#e5eaf2; --sd-blue:#2457d6; --sd-soft:#f6f9fd; }
    .secretariat-dashboard .sd-hero { background:linear-gradient(135deg,#ffffff 0%,#f4f8ff 100%); border:1px solid var(--sd-line); border-radius:18px; box-shadow:0 6px 24px rgba(24,52,88,.05); display:flex; flex-wrap:wrap; gap:14px; justify-content:space-between; padding:22px 24px; }
    .secretariat-dashboard .sd-eyebrow { color:#7b8ca3; font-size:10px; font-weight:800; letter-spacing:.12em; text-transform:uppercase; }
    .secretariat-dashboard .sd-title { color:var(--sd-ink); font-size:25px; font-weight:800; letter-spacing:-.4px; margin:4px 0 0; }
    .secretariat-dashboard .sd-subtitle { color:var(--sd-muted); font-size:13px; margin:5px 0 0; }
    .secretariat-dashboard .sd-scopes { display:flex; flex-wrap:wrap; gap:6px; justify-content:flex-end; max-width:420px; }
    .secretariat-dashboard .sd-scope { background:#e8effe; border-radius:20px; color:#2c5aa8; font-size:10px; font-weight:700; letter-spacing:.02em; padding:6px 11px; }
    .secretariat-dashboard .sd-card { background:#fff; border:1px solid var(--sd-line); border-radius:16px; box-shadow:0 4px 18px rgba(31,58,91,.045); }
    .secretariat-dashboard .sd-section-head { align-items:center; display:flex; flex-wrap:wrap; gap:12px; justify-content:space-between; margin:22px 2px 12px; }
    .secretariat-dashboard .sd-section-title { color:var(--sd-ink); font-size:17px; font-weight:800; margin:0; }
    .secretariat-dashboard .sd-section-sub { color:var(--sd-muted); font-size:12px; margin:2px 0 0; }
    .secretariat-dashboard .sd-filter { position:relative; width:260px; }
    .secretariat-dashboard .sd-filter i { color:#93a0b2; left:12px; position:absolute; top:9px; }
    .secretariat-dashboard .sd-filter input { background:#fff; border:1px solid #d9e1ec; border-radius:9px; font-size:13px; height:36px; padding-left:34px; width:100%; }
    .secretariat-dashboard .sd-filter input:focus { border-color:var(--sd-blue); box-shadow:0 0 0 3px rgba(36,87,214,.1); outline:none; }
    .secretariat-dashboard .sd-grid { display:grid; gap:16px; grid-template-columns:repeat(auto-fill,minmax(440px,1fr)); }
    .secretariat-dashboard .sd-vac { background:#fff; border:1px solid var(--sd-line); border-radius:16px; box-shadow:0 4px 18px rgba(31,58,91,.045); display:flex; flex-direction:column; overflow:hidden; transition:transform .16s ease,box-shadow .16s ease,border-color .16s ease; }
    .secretariat-dashboard .sd-vac:hover { border-color:#c9d8ef; box-shadow:0 12px 28px rgba(24,52,88,.1); transform:translateY(-2px); }
    .secretariat-dashboard .sd-vac-head { border-bottom:1px solid #eef2f8; padding:16px 18px 14px; }
    .secretariat-dashboard .sd-tagline { align-items:center; display:flex; flex-wrap:wrap; gap:6px; margin-bottom:7px; }
    .secretariat-dashboard .sd-chip { border-radius:6px; font-size:9px; font-weight:800; letter-spacing:.06em; padding:4px 8px; text-transform:uppercase; }
    .secretariat-dashboard .sd-chip-group { background:#eaf0ff; color:#2c55b5; }
    .secretariat-dashboard .sd-chip-type { background:#e6f5ef; color:#1c7a55; }
    .secretariat-dashboard .sd-chip-plain { background:#f1f4f9; color:#65758c; }
    .secretariat-dashboard .sd-chip-alert { background:#fdeceb; color:#b8443c; }
    .secretariat-dashboard .sd-vac-name { color:var(--sd-ink); font-size:16px; font-weight:800; line-height:1.3; margin:0; }
    .secretariat-dashboard .sd-vac-type { color:#5c7188; font-size:13px; font-weight:650; }
    .secretariat-dashboard .sd-vac-body { flex:1; padding:14px 18px 4px; }
    .secretariat-dashboard .sd-bar-top { align-items:baseline; color:var(--sd-muted); display:flex; font-size:11px; justify-content:space-between; margin-bottom:6px; }
    .secretariat-dashboard .sd-bar-top strong { color:var(--sd-ink); font-size:12px; }
    .secretariat-dashboard .sd-progress { background:#eaeff6; border-radius:8px; height:8px; overflow:hidden; }
    .secretariat-dashboard .sd-progress span { background:linear-gradient(90deg,#2e9e6b,#4ecb92); border-radius:8px; display:block; height:100%; transition:width .4s ease; }
    .secretariat-dashboard .sd-stats { display:grid; gap:7px; grid-template-columns:repeat(5,1fr); margin-top:14px; }
    .secretariat-dashboard .sd-stat { background:var(--sd-soft); border:1px solid #eef2f8; border-radius:11px; padding:9px 4px; text-align:center; }
    .secretariat-dashboard .sd-stat strong { color:var(--sd-ink); display:block; font-size:17px; font-weight:800; line-height:1.2; }
    .secretariat-dashboard .sd-stat span { color:#8494a8; font-size:9px; font-weight:700; letter-spacing:.04em; text-transform:uppercase; }
    .secretariat-dashboard .sd-stat.is-alert { background:#fdf3f2; border-color:#f8dedb; }
    .secretariat-dashboard .sd-stat.is-alert strong { color:#c0453c; }
    .secretariat-dashboard .sd-stat.is-dq strong { color:#b8443c; }
    .secretariat-dashboard .sd-vac-foot { display:flex; flex-wrap:wrap; gap:7px; padding:14px 18px 16px; }
    .secretariat-dashboard .sd-act { align-items:center; border:1px solid #dbe3ee; border-radius:9px; color:#3d5876; display:inline-flex; font-size:12px; font-weight:650; gap:5px; padding:7px 12px; transition:all .14s ease; }
    .secretariat-dashboard .sd-act:hover { background:#f2f6fd; border-color:#b9cbe8; color:var(--sd-blue); text-decoration:none; }
    .secretariat-dashboard .sd-act-primary { background:var(--sd-blue); border-color:var(--sd-blue); color:#fff; }
    .secretariat-dashboard .sd-act-primary:hover { background:#1c48b8; border-color:#1c48b8; color:#fff; }
    .secretariat-dashboard .sd-act .sd-pill { background:rgba(0,0,0,.07); border-radius:9px; font-size:10px; font-weight:800; padding:1px 6px; }
    .secretariat-dashboard .sd-act-primary .sd-pill { background:rgba(255,255,255,.25); }
    .secretariat-dashboard .sd-act-warn .sd-pill { background:#fdebd0; color:#95610b; }
    .secretariat-dashboard .sd-vac-reports { align-items:center; background:#fafcff; border-top:1px solid #eef2f8; display:flex; flex-wrap:wrap; gap:7px; padding:11px 18px 13px; }
    .secretariat-dashboard .sd-reports-label { color:#8494a8; font-size:9px; font-weight:800; letter-spacing:.08em; text-transform:uppercase; width:100%; }
    .secretariat-dashboard .sd-report { align-items:center; background:#fff; border:1px solid #dbe3ee; border-radius:9px; color:#3d5876; display:inline-flex; font-size:12px; font-weight:650; gap:5px; padding:6px 11px; transition:all .14s ease; }
    .secretariat-dashboard .sd-report:hover { background:#f2f6fd; border-color:#b9cbe8; color:var(--sd-blue); text-decoration:none; }
    .secretariat-dashboard .sd-empty { color:var(--sd-muted); padding:52px 20px; text-align:center; }
    .secretariat-dashboard .sd-links { align-items:center; display:flex; flex-wrap:wrap; gap:10px 8px; padding:14px 18px; }
    .secretariat-dashboard .sd-links a { align-items:center; border:1px solid #e6ebf3; border-radius:9px; color:#425a79; display:inline-flex; font-size:12px; gap:6px; padding:7px 12px; transition:all .14s ease; }
    .secretariat-dashboard .sd-links a:hover { background:#f4f8fd; border-color:#c9d8ef; color:var(--sd-blue); text-decoration:none; }
    @media (max-width:575px) {
        .secretariat-dashboard .sd-hero { padding:18px; }
        .secretariat-dashboard .sd-scopes { justify-content:flex-start; }
        .secretariat-dashboard .sd-filter { width:100%; }
        .secretariat-dashboard .sd-grid { grid-template-columns:1fr; }
        .secretariat-dashboard .sd-stats { grid-template-columns:repeat(auto-fit,minmax(74px,1fr)); }
    }
</style>

<div class="content-page secretariat-dashboard">
    <div class="content">
        <div class="container-fluid">
            <div class="sd-hero">
                <div>
                    <div class="sd-eyebrow">Recruitment</div>
                    <h2 class="sd-title">Secretariat Workspace</h2>
                    <p class="sd-subtitle">Open a vacancy below to tag applicants, encode scores, build exams, or settle retention requests.</p>
                </div>
                <!-- <div class="sd-scopes">
                    <?php if (empty($assigned)) : ?>
                        <span class="badge badge-warning p-2">No vacancy assigned</span>
                    <?php else : ?>
                        <?php foreach (array_slice($assigned, 0, 4) as $label) : ?><span class="sd-scope"><?= $dashboard_h($label); ?></span><?php endforeach; ?>
                        <?php if (count($assigned) > 4) : ?><span class="sd-scope">+<?= count($assigned) - 4; ?> more</span><?php endif; ?>
                    <?php endif; ?>
                </div> -->
            </div>

            <!-- <?php if (empty($assigned)) : ?>
                <div class="alert alert-warning mt-3"><strong>No vacancy assigned.</strong> Ask a Super Admin to assign an open vacancy before managing applicants.</div>
            <?php endif; ?> -->

            <div class="sd-section-head">
                <div>
                    <h5 class="sd-section-title">Assigned vacancies</h5>
                    <p class="sd-section-sub"><span id="sd-visible-count"><?= count($vacancies); ?></span> of <?= count($vacancies); ?> vacanc<?= count($vacancies) === 1 ? 'y' : 'ies'; ?> &middot; each card shows only that vacancy's workload.</p>
                </div>
                <?php if (!empty($vacancies)) : ?>
                    <div class="sd-filter">
                        <i class="mdi mdi-magnify"></i>
                        <input type="search" id="sd-vacancy-filter" placeholder="Filter by title, item, group, FY" autocomplete="off">
                    </div>
                <?php endif; ?>
            </div>

            <?php if (empty($vacancies)) : ?>
                <div class="sd-card sd-empty">
                    <i class="mdi mdi-briefcase-search-outline" style="font-size:40px"></i>
                    <h5 class="mt-2">No assigned vacancy workload</h5>
                    <p class="mb-0 small">Vacancies assigned to your account will appear here.</p>
                </div>
            <?php else : ?>
                <div class="sd-grid" id="sd-vacancy-grid">
                    <?php foreach ($vacancies as $vacancy) : ?>
                        <?php
                        $jobId = (int) $vacancy->jobID;
                        $vacancyTotal = (int) $vacancy->applicant_total;
                        $vacancyTagged = (int) $vacancy->tagged_total;
                        $vacancyPending = (int) $vacancy->pending_total;
                        $vacancyProgress = $vacancyTotal > 0 ? round(($vacancyTagged / $vacancyTotal) * 100) : 0;
                        $vacancyRetention = $retentionCounts[$jobId] ?? ['pending' => 0, 'total' => 0];
                        $vacancyExams = $examCounts[$jobId] ?? ['total' => 0];
                        $vacancyScores = $scoreCounts[$jobId] ?? ['total' => 0, 'complete' => 0];
                        $scoreEligible = !in_array((int) $vacancy->position, [1, 5], true);
                        $groupLabel = $positionGroups[(int) $vacancy->position] ?? 'Vacancy';
                        // Related Teaching and Non-Teaching carry job_type 0 = the whole group, so they have no label.
                        $typeLabel = $jobTypeLabels[(int) ($vacancy->job_type ?? 0)] ?? '';
                        $vacancyDq = (int) ($vacancy->dq_total ?? 0);
                        $filterText = strtolower(trim($vacancy->jobTitle . ' ' . $groupLabel . ' ' . $typeLabel . ' ' . $vacancy->sy . ' ' . ($vacancy->itemNo ?? '')));
                        ?>
                        <div class="sd-vac" data-vacancy-search="<?= $dashboard_h($filterText); ?>">
                            <div class="sd-vac-head">
                                <div class="sd-tagline">
                                    <span class="sd-chip sd-chip-group"><?= $dashboard_h($groupLabel); ?></span>
                                    <?php if ($typeLabel !== '') : ?><span class="sd-chip sd-chip-type"><?= $dashboard_h($typeLabel); ?></span><?php endif; ?>
                                    <span class="sd-chip sd-chip-plain">FY <?= $dashboard_h($vacancy->sy); ?></span>
                                    <?php if (!empty($vacancy->itemNo)) : ?><span class="sd-chip sd-chip-plain">Item <?= $dashboard_h($vacancy->itemNo); ?></span><?php endif; ?>
                                    <?php if ($vacancyPending > 0) : ?><span class="sd-chip sd-chip-alert"><?= $vacancyPending; ?> waiting</span><?php endif; ?>
                                </div>
                                <h6 class="sd-vac-name"><?= $dashboard_h($vacancy->jobTitle); ?><?= $typeLabel !== '' ? ' <span class="sd-vac-type">&mdash; ' . $dashboard_h($typeLabel) . '</span>' : ''; ?></h6>
                            </div>
                            <div class="sd-vac-body">
                                <div class="sd-bar-top">
                                    <span>Assigned to evaluators</span>
                                    <strong><?= (int) $vacancyProgress; ?>%</strong>
                                </div>
                                <div class="sd-progress" title="<?= $vacancyTagged; ?> of <?= $vacancyTotal; ?> applicants assigned"><span style="width:<?= (int) $vacancyProgress; ?>%"></span></div>
                                <div class="sd-stats">
                                    <div class="sd-stat"><strong><?= $vacancyTotal; ?></strong><span>Applicants</span></div>
                                    <div class="sd-stat"><strong><?= $vacancyTagged; ?></strong><span>Tagged</span></div>
                                    <div class="sd-stat <?= $vacancyDq > 0 ? 'is-dq' : ''; ?>"><strong><?= $vacancyDq; ?></strong><span>DQ</span></div>
                                    <div class="sd-stat <?= $vacancyPending > 0 ? 'is-alert' : ''; ?>"><strong><?= $vacancyPending; ?></strong><span>Waiting</span></div>
                                    <div class="sd-stat"><strong><?= $scoreEligible ? (int) $vacancyScores['complete'] . '/' . (int) $vacancyScores['total'] : '&mdash;'; ?></strong><span>Scores</span></div>
                                </div>
                            </div>
                            <div class="sd-vac-foot">
                                <a href="<?= base_url('secretariat/applicant-tagging?job_id=' . $jobId); ?>" class="sd-act sd-act-primary"><i class="mdi mdi-account-multiple-outline"></i>Applicants</a>
                                <?php if ($scoreEligible) : ?>
                                    <a href="<?= base_url('secretariat/scores?job_id=' . $jobId); ?>" class="sd-act"><i class="mdi mdi-clipboard-edit-outline"></i>Scores</a>
                                <?php endif; ?>
                                <!-- <a href="<?= base_url('secretariat/exams?job_id=' . $jobId); ?>" class="sd-act"><i class="mdi mdi-clipboard-text-outline"></i>Exam<?php if ((int) $vacancyExams['total'] > 0) : ?><span class="sd-pill"><?= (int) $vacancyExams['total']; ?></span><?php endif; ?></a> -->
                                <a href="<?= base_url('secretariat/qualified?job_id=' . $jobId); ?>" class="sd-act"><i class="mdi mdi-account-check-outline"></i>Qualified</a>
                                <a href="<?= base_url('secretariat/disqualified?job_id=' . $jobId); ?>" class="sd-act"><i class="mdi mdi-account-remove-outline"></i>Disqualified<?php if ($vacancyDq > 0) : ?><span class="sd-pill"><?= $vacancyDq; ?></span><?php endif; ?></a>
                                <a href="<?= base_url('secretariat/retention?job_id=' . $jobId); ?>" class="sd-act sd-act-warn"><i class="mdi mdi-file-restore-outline"></i>Retention<?php if ((int) $vacancyRetention['pending'] > 0) : ?><span class="sd-pill"><?= (int) $vacancyRetention['pending']; ?></span><?php endif; ?></a>
                            </div>
                            <div class="sd-vac-reports">
                                <span class="sd-reports-label">Reports</span>
                                <a href="<?= base_url('secretariat/reports/shortlist/' . $jobId); ?>" target="_blank" class="sd-report"><i class="mdi mdi-format-list-numbered"></i>Shortlist</a>
                                <a href="<?= base_url('secretariat/reports/shortlist-contact/' . $jobId); ?>" target="_blank" class="sd-report"><i class="mdi mdi-phone-outline"></i>Shortlist with Contact No.</a>
                                <a href="<?= base_url('secretariat/reports/ier/' . $jobId); ?>" target="_blank" class="sd-report"><i class="mdi mdi-clipboard-check-outline"></i>IER (Qualified)</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="sd-card sd-empty mt-2" id="sd-no-match" style="display:none">
                    <i class="mdi mdi-magnify-close" style="font-size:34px"></i>
                    <h6 class="mt-2 mb-0">No vacancy matches that filter</h6>
                </div>
            <?php endif; ?>

            <div class="sd-section-head">
                <!-- <div>
                    <h5 class="sd-section-title">Other recruitment views</h5>
                    <p class="sd-section-sub">Cross-vacancy lists kept for reference.</p>
                </div> -->
            </div>
            <!-- <div class="sd-card mb-3">
                <div class="sd-links">
                    <a href="<?= base_url('Pages/endorsed_applicants'); ?>"><i class="mdi mdi-send-check-outline"></i>Endorse applicants</a>
                    <a href="<?= base_url('Pages/endorsed_applicants_unassigned'); ?>"><i class="mdi mdi-account-alert-outline"></i>Without evaluator <?php if ((int) $counts['no_rater'] > 0) : ?><span class="badge badge-warning"><?= (int) $counts['no_rater']; ?></span><?php endif; ?></a>
                    <a href="<?= base_url('Pages/secretariat_endorsed'); ?>"><i class="mdi mdi-chart-box-outline"></i>Endorsed &amp; scored</a>
                    <a href="<?= base_url('Pages/secretariat_dq_applicants'); ?>"><i class="mdi mdi-account-remove-outline"></i>Disqualified applicants</a>
                </div>
            </div> -->
        </div>
    </div>
</div>

<script>
(function () {
    var filter = document.getElementById('sd-vacancy-filter');
    var grid = document.getElementById('sd-vacancy-grid');
    var counter = document.getElementById('sd-visible-count');
    var noMatch = document.getElementById('sd-no-match');
    if (!filter || !grid) return;

    filter.addEventListener('input', function () {
        var needle = filter.value.trim().toLowerCase();
        var visible = 0;
        grid.querySelectorAll('.sd-vac').forEach(function (card) {
            var matches = !needle || (card.getAttribute('data-vacancy-search') || '').indexOf(needle) !== -1;
            card.style.display = matches ? '' : 'none';
            if (matches) visible += 1;
        });
        if (counter) counter.textContent = visible;
        if (noMatch) noMatch.style.display = visible ? 'none' : '';
    });
})();
</script>
