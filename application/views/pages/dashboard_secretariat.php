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
$retentionCounts = $retentionCounts ?? [];
$retentionTotals = array_merge(['pending' => 0, 'granted' => 0, 'denied' => 0, 'total' => 0], $retentionTotals ?? []);
$examCounts = $examCounts ?? [];
$examTotals = array_merge(['total' => 0, 'published' => 0, 'draft' => 0, 'questions' => 0], $examTotals ?? []);
$scoreCounts = $scoreCounts ?? [];
$scoreTotals = array_merge(['total' => 0, 'interview' => 0, 'written' => 0, 'complete' => 0], $scoreTotals ?? []);
$positionGroups = [1 => 'Teaching', 2 => 'School Administration', 3 => 'Related Teaching', 4 => 'Non-Teaching', 5 => 'Promotion'];
$applicantTotal = (int) $counts['applicants'];
$taggedPercent = $applicantTotal > 0 ? round(((int) $counts['tagged'] / $applicantTotal) * 100) : 0;
?>

<style>
    .secretariat-dashboard { --sd-ink:#173252; --sd-muted:#6b7b91; --sd-line:#e3e9f1; --sd-blue:#2457d6; --sd-soft:#f5f8fc; }
    .secretariat-dashboard .sd-top { align-items:center; background:#fff; border:1px solid var(--sd-line); border-radius:14px; display:flex; justify-content:space-between; padding:20px 22px; }
    .secretariat-dashboard .sd-title { color:var(--sd-ink); font-size:24px; font-weight:800; margin:0; }
    .secretariat-dashboard .sd-subtitle { color:var(--sd-muted); margin:4px 0 0; }
    .secretariat-dashboard .sd-scope { background:#edf3ff; border-radius:16px; color:#315ca6; display:inline-block; font-size:10px; font-weight:700; margin:3px 0 3px 5px; padding:5px 9px; }
    .secretariat-dashboard .sd-workflow { background:#fff; border:1px solid var(--sd-line); border-radius:13px; color:inherit; display:block; height:100%; padding:17px; transition:transform .14s ease,box-shadow .14s ease,border-color .14s ease; }
    .secretariat-dashboard .sd-workflow:hover { border-color:#c8d6ea; box-shadow:0 9px 22px rgba(28,56,89,.09); color:inherit; text-decoration:none; transform:translateY(-2px); }
    .secretariat-dashboard .sd-workflow-head { align-items:center; display:flex; justify-content:space-between; }
    .secretariat-dashboard .sd-workflow-icon { align-items:center; border-radius:10px; display:flex; font-size:22px; height:42px; justify-content:center; width:42px; }
    .secretariat-dashboard .sd-blue { background:#e9efff; color:#2457d6; }
    .secretariat-dashboard .sd-teal { background:#e4f7f4; color:#12806f; }
    .secretariat-dashboard .sd-purple { background:#f0ebff; color:#7147bd; }
    .secretariat-dashboard .sd-amber { background:#fff1d7; color:#a56800; }
    .secretariat-dashboard .sd-workflow-value { color:var(--sd-ink); font-size:25px; font-weight:800; }
    .secretariat-dashboard .sd-workflow h6 { color:var(--sd-ink); font-size:15px; font-weight:800; margin:12px 0 3px; }
    .secretariat-dashboard .sd-workflow p { color:var(--sd-muted); font-size:12px; margin:0; min-height:36px; }
    .secretariat-dashboard .sd-card { border:1px solid var(--sd-line); border-radius:14px; box-shadow:0 5px 18px rgba(31,58,91,.045); }
    .secretariat-dashboard .sd-summary { align-items:center; display:flex; flex-wrap:wrap; gap:8px 22px; }
    .secretariat-dashboard .sd-summary-item { min-width:120px; }
    .secretariat-dashboard .sd-summary-item strong { color:var(--sd-ink); display:block; font-size:21px; }
    .secretariat-dashboard .sd-summary-item span { color:var(--sd-muted); font-size:10px; font-weight:700; letter-spacing:.04em; text-transform:uppercase; }
    .secretariat-dashboard .sd-section-title { color:var(--sd-ink); font-size:17px; font-weight:800; }
    .secretariat-dashboard .sd-vacancy { border-top:1px solid #edf1f6; padding:17px 0; }
    .secretariat-dashboard .sd-vacancy:first-child { border-top:0; }
    .secretariat-dashboard .sd-vacancy-grid { align-items:center; display:grid; gap:14px; grid-template-columns:minmax(230px,1.4fr) minmax(250px,1fr) auto; }
    .secretariat-dashboard .sd-vacancy-name { color:var(--sd-ink); font-weight:800; }
    .secretariat-dashboard .sd-vacancy-meta { color:var(--sd-muted); font-size:12px; margin-top:3px; }
    .secretariat-dashboard .sd-progress { background:#e8edf5; border-radius:7px; height:7px; margin-top:9px; overflow:hidden; }
    .secretariat-dashboard .sd-progress span { background:linear-gradient(90deg,#25a56a,#4ac989); display:block; height:100%; }
    .secretariat-dashboard .sd-vacancy-counts { display:grid; gap:7px; grid-template-columns:repeat(4,minmax(55px,1fr)); }
    .secretariat-dashboard .sd-count { background:var(--sd-soft); border-radius:9px; padding:7px 5px; text-align:center; }
    .secretariat-dashboard .sd-count strong { color:var(--sd-ink); display:block; font-size:16px; }
    .secretariat-dashboard .sd-count span { color:var(--sd-muted); font-size:9px; font-weight:700; text-transform:uppercase; }
    .secretariat-dashboard .sd-actions { display:flex; flex-wrap:wrap; gap:6px; justify-content:flex-end; max-width:330px; }
    .secretariat-dashboard .sd-secondary { align-items:center; display:flex; flex-wrap:wrap; gap:8px 18px; }
    .secretariat-dashboard .sd-secondary a { color:#405b7b; font-size:12px; }
    .secretariat-dashboard .sd-secondary a:hover { color:var(--sd-blue); }
    @media (max-width:1100px) { .secretariat-dashboard .sd-vacancy-grid { grid-template-columns:1fr 1fr; } .secretariat-dashboard .sd-actions { grid-column:1/-1; justify-content:flex-start; max-width:none; } }
    @media (max-width:767px) { .secretariat-dashboard .sd-top { align-items:flex-start; flex-direction:column; gap:10px; } .secretariat-dashboard .sd-scope { margin-left:0; margin-right:4px; } .secretariat-dashboard .sd-vacancy-grid { grid-template-columns:1fr; } .secretariat-dashboard .sd-actions { grid-column:auto; } }
    @media (max-width:480px) { .secretariat-dashboard .sd-vacancy-counts { grid-template-columns:1fr 1fr; } }
</style>

<div class="content-page secretariat-dashboard">
    <div class="content">
        <div class="container-fluid">
            <div class="sd-top mb-3">
                <div>
                    <h2 class="sd-title">Secretariat Workspace</h2>
                    <p class="sd-subtitle">Your recruitment tasks, organized by workflow and assigned vacancy.</p>
                </div>
                <div class="text-md-right">
                    <?php if (empty($assigned)) : ?>
                        <span class="badge badge-warning p-2">No vacancy assigned</span>
                    <?php else : ?>
                        <?php foreach (array_slice($assigned, 0, 3) as $label) : ?><span class="sd-scope"><?= $dashboard_h($label); ?></span><?php endforeach; ?>
                        <?php if (count($assigned) > 3) : ?><span class="sd-scope">+<?= count($assigned) - 3; ?> more</span><?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (empty($assigned)) : ?>
                <div class="alert alert-warning"><strong>No vacancy assigned.</strong> Ask a Super Admin to assign an open vacancy before managing applicants.</div>
            <?php endif; ?>

            <div class="row mb-1">
                <div class="col-xl-3 col-md-6 mb-3">
                    <a class="sd-workflow" href="<?= base_url('secretariat/applicant-tagging'); ?>">
                        <div class="sd-workflow-head"><span class="sd-workflow-icon sd-blue"><i class="mdi mdi-account-arrow-right-outline"></i></span><span class="sd-workflow-value"><?= (int) $counts['untagged']; ?></span></div>
                        <h6>Applicant assignment</h6>
                        <p>Review applicants and assign those waiting to an evaluator.</p>
                    </a>
                </div>
                <div class="col-xl-3 col-md-6 mb-3">
                    <a class="sd-workflow" href="<?= base_url('secretariat/scores'); ?>">
                        <div class="sd-workflow-head"><span class="sd-workflow-icon sd-teal"><i class="mdi mdi-clipboard-edit-outline"></i></span><span class="sd-workflow-value"><?= (int) $scoreTotals['complete']; ?>/<?= (int) $scoreTotals['total']; ?></span></div>
                        <h6>Interview &amp; written scores</h6>
                        <p>Encode both scores and publish them to each applicant's MA page.</p>
                    </a>
                </div>
                <div class="col-xl-3 col-md-6 mb-3">
                    <a class="sd-workflow" href="<?= base_url('secretariat/exams'); ?>">
                        <div class="sd-workflow-head"><span class="sd-workflow-icon sd-purple"><i class="mdi mdi-clipboard-text-outline"></i></span><span class="sd-workflow-value"><?= (int) $examTotals['total']; ?></span></div>
                        <h6>Exam builder</h6>
                        <p><?= (int) $examTotals['published']; ?> published and <?= (int) $examTotals['draft']; ?> draft exam<?= (int) $examTotals['total'] === 1 ? '' : 's'; ?>.</p>
                    </a>
                </div>
                <div class="col-xl-3 col-md-6 mb-3">
                    <a class="sd-workflow" href="<?= base_url('secretariat/retention'); ?>">
                        <div class="sd-workflow-head"><span class="sd-workflow-icon sd-amber"><i class="mdi mdi-file-restore-outline"></i></span><span class="sd-workflow-value"><?= (int) $retentionTotals['pending']; ?></span></div>
                        <h6>Retention requests</h6>
                        <p>Resolve pending requests by copying or manually encoding scores.</p>
                    </a>
                </div>
            </div>

            <div class="card sd-card mb-3">
                <div class="card-body py-3">
                    <div class="sd-summary">
                        <div class="mr-auto"><div class="sd-section-title">Recruitment summary</div><div class="text-muted small">Across all assigned open vacancies</div></div>
                        <div class="sd-summary-item"><strong><?= $applicantTotal; ?></strong><span>Total applicants</span></div>
                        <div class="sd-summary-item"><strong><?= (int) $counts['tagged']; ?></strong><span>Tagged (<?= (int) $taggedPercent; ?>%)</span></div>
                        <div class="sd-summary-item"><strong><?= (int) $counts['evaluated']; ?></strong><span>Endorsed / rated</span></div>
                        <div class="sd-summary-item"><strong><?= (int) $counts['dq']; ?></strong><span>Disqualified</span></div>
                    </div>
                </div>
            </div>

            <div class="card sd-card mb-3">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between mb-1">
                        <div><div class="sd-section-title">Assigned vacancies</div><p class="text-muted small mb-0">Open a vacancy directly in the workflow you need.</p></div>
                        <span class="badge badge-light p-2"><?= count($vacancies); ?> vacancy<?= count($vacancies) === 1 ? '' : 'ies'; ?></span>
                    </div>

                    <?php if (empty($vacancies)) : ?>
                        <div class="text-center text-muted py-5"><i class="mdi mdi-briefcase-search-outline" style="font-size:38px"></i><h5 class="mt-2">No assigned vacancy workload</h5></div>
                    <?php else : ?>
                        <?php foreach ($vacancies as $vacancy) : ?>
                            <?php
                            $jobId = (int) $vacancy->jobID;
                            $vacancyTotal = (int) $vacancy->applicant_total;
                            $vacancyProgress = $vacancyTotal > 0 ? round(((int) $vacancy->tagged_total / $vacancyTotal) * 100) : 0;
                            $vacancyRetention = $retentionCounts[$jobId] ?? ['pending' => 0, 'total' => 0];
                            $vacancyExams = $examCounts[$jobId] ?? ['total' => 0];
                            $vacancyScores = $scoreCounts[$jobId] ?? ['total' => 0, 'complete' => 0];
                            $scoreEligible = !in_array((int) $vacancy->position, [1, 5], true);
                            ?>
                            <div class="sd-vacancy">
                                <div class="sd-vacancy-grid">
                                    <div>
                                        <div class="sd-vacancy-name"><?= $dashboard_h($vacancy->jobTitle); ?></div>
                                        <div class="sd-vacancy-meta">
                                            <?= $dashboard_h($positionGroups[(int) $vacancy->position] ?? 'Vacancy'); ?> &middot; FY <?= $dashboard_h($vacancy->sy); ?>
                                            <?= !empty($vacancy->itemNo) ? ' &middot; Item ' . $dashboard_h($vacancy->itemNo) : ''; ?>
                                        </div>
                                        <div class="sd-progress" title="<?= (int) $vacancyProgress; ?>% assigned to evaluators"><span style="width:<?= (int) $vacancyProgress; ?>%"></span></div>
                                    </div>
                                    <div class="sd-vacancy-counts">
                                        <div class="sd-count"><strong><?= $vacancyTotal; ?></strong><span>Applicants</span></div>
                                        <div class="sd-count"><strong><?= (int) $vacancy->tagged_total; ?></strong><span>Tagged</span></div>
                                        <div class="sd-count"><strong class="<?= (int) $vacancy->pending_total > 0 ? 'text-danger' : ''; ?>"><?= (int) $vacancy->pending_total; ?></strong><span>Waiting</span></div>
                                        <div class="sd-count"><strong><?= $scoreEligible ? (int) $vacancyScores['complete'] . '/' . (int) $vacancyScores['total'] : '&mdash;'; ?></strong><span>Scores</span></div>
                                    </div>
                                    <div class="sd-actions">
                                        <a href="<?= base_url('secretariat/applicant-tagging?job_id=' . $jobId); ?>" class="btn btn-outline-primary btn-sm">Applicants</a>
                                        <?php if ($scoreEligible) : ?><a href="<?= base_url('secretariat/scores?job_id=' . $jobId); ?>" class="btn btn-outline-success btn-sm">Scores</a><?php endif; ?>
                                        <a href="<?= base_url('secretariat/exams?job_id=' . $jobId); ?>" class="btn btn-outline-secondary btn-sm">Exam<?= (int) $vacancyExams['total'] > 0 ? ' (' . (int) $vacancyExams['total'] . ')' : ''; ?></a>
                                        <a href="<?= base_url('secretariat/retention?job_id=' . $jobId); ?>" class="btn btn-outline-warning btn-sm">Retention<?= (int) $vacancyRetention['pending'] > 0 ? ' (' . (int) $vacancyRetention['pending'] . ')' : ''; ?></a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card sd-card">
                <div class="card-body py-3 sd-secondary">
                    <strong class="small" style="color:#173252">Other recruitment views</strong>
                    <a href="<?= base_url('Pages/endorsed_applicants'); ?>"><i class="mdi mdi-send-check-outline mr-1"></i>Endorse applicants</a>
                    <a href="<?= base_url('Pages/endorsed_applicants_unassigned'); ?>"><i class="mdi mdi-account-alert-outline mr-1"></i>Without evaluator <span class="badge badge-warning ml-1"><?= (int) $counts['no_rater']; ?></span></a>
                    <a href="<?= base_url('Pages/secretariat_endorsed'); ?>"><i class="mdi mdi-chart-box-outline mr-1"></i>Endorsed &amp; scored</a>
                    <a href="<?= base_url('Pages/secretariat_dq_applicants'); ?>"><i class="mdi mdi-account-remove-outline mr-1"></i>Disqualified applicants</a>
                </div>
            </div>
        </div>
    </div>
</div>
