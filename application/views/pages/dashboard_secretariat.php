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

$positionGroups = [
    1 => 'Teaching',
    2 => 'School Administration',
    3 => 'Related Teaching',
    4 => 'Non-Teaching',
];

// Percent of every applicant received, not of the shrinking tagging queue --
// dividing by the queue made the figure jump around as applicants were rated.
$applicantTotal = (int) $counts['applicants'];
$taggedPercent = $applicantTotal > 0 ? round(((int) $counts['tagged'] / $applicantTotal) * 100) : 0;
?>

<style>
    .secretariat-dashboard { --sd-ink:#173252; --sd-muted:#6b7b91; --sd-line:#e6ebf2; --sd-blue:#2457d6; --sd-soft:#f4f7fb; }
    .secretariat-dashboard .sd-hero { align-items:center; background:linear-gradient(125deg,#103764 0%,#2357d5 62%,#4d82ed 100%); border-radius:17px; box-shadow:0 16px 35px rgba(23,50,82,.17); color:#fff; display:flex; justify-content:space-between; overflow:hidden; padding:28px 30px; position:relative; }
    .secretariat-dashboard .sd-hero:after { border:28px solid rgba(255,255,255,.08); border-radius:50%; content:""; height:190px; position:absolute; right:-35px; top:-65px; width:190px; }
    .secretariat-dashboard .sd-eyebrow { color:#cfe0ff; font-size:12px; font-weight:700; letter-spacing:.11em; text-transform:uppercase; }
    .secretariat-dashboard .sd-hero h2 { color:#fff; font-size:27px; margin:6px 0 6px; }
    .secretariat-dashboard .sd-hero p { color:#dce8ff; margin:0; max-width:650px; }
    .secretariat-dashboard .sd-hero-actions { display:flex; gap:9px; position:relative; z-index:1; }
    .secretariat-dashboard .sd-hero .btn-light { color:#164589; font-weight:700; }
    .secretariat-dashboard .sd-card { border:1px solid var(--sd-line); border-radius:14px; box-shadow:0 6px 21px rgba(23,50,82,.055); }
    .secretariat-dashboard .sd-stat { background:#fff; border:1px solid var(--sd-line); border-radius:13px; height:100%; padding:18px; position:relative; }
    .secretariat-dashboard .sd-stat-icon { align-items:center; border-radius:11px; display:flex; font-size:21px; height:42px; justify-content:center; width:42px; }
    .secretariat-dashboard .sd-stat-label { color:var(--sd-muted); font-size:12px; font-weight:700; letter-spacing:.045em; text-transform:uppercase; }
    .secretariat-dashboard .sd-stat-value { color:var(--sd-ink); font-size:28px; font-weight:800; line-height:1.15; margin-top:5px; }
    .secretariat-dashboard .sd-stat-note { color:#8492a6; font-size:12px; margin-top:5px; }
    .secretariat-dashboard .sd-blue { background:#e8efff; color:#2457d6; }
    .secretariat-dashboard .sd-amber { background:#fff2d7; color:#a66a00; }
    .secretariat-dashboard .sd-green { background:#e2f6eb; color:#197447; }
    .secretariat-dashboard .sd-purple { background:#efe9ff; color:#6e43c0; }
    .secretariat-dashboard .sd-card .card-body { padding:22px; }
    .secretariat-dashboard .sd-section-title { color:var(--sd-ink); font-size:17px; font-weight:800; }
    .secretariat-dashboard .sd-position { align-items:center; border-top:1px solid #edf1f6; display:grid; gap:14px; grid-template-columns:minmax(220px,1fr) repeat(3,85px) 105px; padding:15px 2px; }
    .secretariat-dashboard .sd-position:first-of-type { border-top:0; }
    .secretariat-dashboard .sd-position-name { color:var(--sd-ink); font-weight:750; }
    .secretariat-dashboard .sd-position-sub { color:var(--sd-muted); font-size:12px; margin-top:3px; }
    .secretariat-dashboard .sd-number { text-align:center; }
    .secretariat-dashboard .sd-number strong { color:var(--sd-ink); display:block; font-size:17px; }
    .secretariat-dashboard .sd-number span { color:var(--sd-muted); font-size:10px; letter-spacing:.04em; text-transform:uppercase; }
    .secretariat-dashboard .sd-progress { background:#e8edf5; border-radius:8px; height:8px; overflow:hidden; }
    .secretariat-dashboard .sd-progress > span { background:linear-gradient(90deg,#25a56a,#47c986); display:block; height:100%; }
    .secretariat-dashboard .sd-link-list a { align-items:center; border-top:1px solid #edf1f6; color:#304b6d; display:flex; justify-content:space-between; padding:13px 0; }
    .secretariat-dashboard .sd-link-list a:first-child { border-top:0; }
    .secretariat-dashboard .sd-link-list a:hover { color:var(--sd-blue); }
    .secretariat-dashboard .sd-scope { background:#edf3ff; border-radius:18px; color:#315ca6; display:inline-block; font-size:11px; font-weight:700; margin:3px 4px 3px 0; padding:6px 10px; }
    @media (max-width:991px) { .secretariat-dashboard .sd-hero { align-items:flex-start; flex-direction:column; } .secretariat-dashboard .sd-hero-actions { margin-top:18px; } .secretariat-dashboard .sd-position { grid-template-columns:minmax(190px,1fr) repeat(3,70px); } .secretariat-dashboard .sd-position .btn { grid-column:1/-1; } }
    @media (max-width:575px) { .secretariat-dashboard .sd-hero { padding:23px 20px; } .secretariat-dashboard .sd-hero-actions { width:100%; } .secretariat-dashboard .sd-hero-actions .btn { width:100%; } .secretariat-dashboard .sd-position { grid-template-columns:1fr 1fr; } .secretariat-dashboard .sd-position-main { grid-column:1/-1; } }
</style>

<div class="content-page secretariat-dashboard">
    <div class="content">
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-12">
                    <div class="sd-hero">
                        <div>
                            <div class="sd-eyebrow">Recruitment workspace</div>
                            <h2>Secretariat Dashboard</h2>
                            <p>Review applicant activity across your assigned vacancies and tag applicants to evaluators from one focused workspace.</p>
                        </div>
                        <div class="sd-hero-actions">
                            <a href="<?= base_url('secretariat/applicant-tagging'); ?>" class="btn btn-light">
                                <i class="mdi mdi-account-arrow-right-outline mr-1"></i> Tag applicants
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (empty($assigned)) : ?>
                <div class="alert alert-warning">
                    <strong>No vacancy assigned.</strong> Ask a Super Admin to tag an open vacancy to your Secretariat account before managing applicants.
                </div>
            <?php endif; ?>

            <div class="row mb-2">
                <div class="col-xl-3 col-sm-6 mb-3">
                    <div class="sd-stat">
                        <div class="d-flex align-items-start justify-content-between">
                            <div><div class="sd-stat-label">Total applicants</div><div class="sd-stat-value"><?= $applicantTotal; ?></div></div>
                            <span class="sd-stat-icon sd-blue"><i class="mdi mdi-account-group-outline"></i></span>
                        </div>
                        <div class="sd-stat-note">Every application received in your vacancies</div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 mb-3">
                    <div class="sd-stat">
                        <div class="d-flex align-items-start justify-content-between">
                            <div><div class="sd-stat-label">Tagged applicants</div><div class="sd-stat-value"><?= (int) $counts['tagged']; ?></div></div>
                            <span class="sd-stat-icon sd-green"><i class="mdi mdi-account-check-outline"></i></span>
                        </div>
                        <div class="sd-stat-note"><?= (int) $taggedPercent; ?>% of all applicants &middot; stays counted after rating</div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 mb-3">
                    <div class="sd-stat">
                        <div class="d-flex align-items-start justify-content-between">
                            <div><div class="sd-stat-label">Awaiting evaluator</div><div class="sd-stat-value"><?= (int) $counts['untagged']; ?></div></div>
                            <span class="sd-stat-icon sd-amber"><i class="mdi mdi-account-clock-outline"></i></span>
                        </div>
                        <div class="sd-stat-note">Submitted or validated, not yet tagged</div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 mb-3">
                    <div class="sd-stat">
                        <div class="d-flex align-items-start justify-content-between">
                            <div><div class="sd-stat-label">Past tagging</div><div class="sd-stat-value"><?= (int) $counts['evaluated'] + (int) $counts['dq']; ?></div></div>
                            <span class="sd-stat-icon sd-purple"><i class="mdi mdi-progress-check"></i></span>
                        </div>
                        <div class="sd-stat-note"><?= (int) $counts['evaluated']; ?> endorsed or rated &middot; <?= (int) $counts['dq']; ?> disqualified</div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-8 mb-3">
                    <div class="card sd-card h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between mb-2">
                                <div>
                                    <div class="sd-section-title">Assigned vacancy workload</div>
                                    <p class="text-muted mb-0">Open a position to view and tag its applicants. Applicants and Tagged count every applicant at every stage &mdash; only Waiting falls as you tag.</p>
                                </div>
                                <span class="badge badge-light p-2"><?= count($vacancies); ?> position<?= count($vacancies) === 1 ? '' : 's'; ?></span>
                            </div>

                            <?php if (empty($vacancies)) : ?>
                                <div class="text-center py-5">
                                    <i class="mdi mdi-briefcase-search-outline text-muted" style="font-size:40px"></i>
                                    <h5 class="mt-2">No vacancy workload yet</h5>
                                    <p class="text-muted mb-0">Your assigned open vacancies will appear here.</p>
                                </div>
                            <?php else : ?>
                                <?php foreach ($vacancies as $vacancy) : ?>
                                    <?php
                                    $vacancyTotal = (int) $vacancy->applicant_total;
                                    $vacancyProgress = $vacancyTotal > 0 ? round(((int) $vacancy->tagged_total / $vacancyTotal) * 100) : 0;
                                    $vacancyPastTagging = (int) $vacancy->evaluated_total + (int) $vacancy->dq_total;
                                    ?>
                                    <div class="sd-position">
                                        <div class="sd-position-main">
                                            <div class="sd-position-name"><?= $dashboard_h($vacancy->jobTitle); ?></div>
                                            <div class="sd-position-sub">
                                                <?= $dashboard_h($positionGroups[(int) $vacancy->position] ?? 'Vacancy'); ?>
                                                &middot; FY <?= $dashboard_h($vacancy->sy); ?>
                                                <?php if ($vacancyPastTagging > 0) : ?>
                                                    &middot; <?= $vacancyPastTagging; ?> past tagging
                                                    (<?= (int) $vacancy->evaluated_total; ?> endorsed/rated, <?= (int) $vacancy->dq_total; ?> DQ)
                                                <?php endif; ?>
                                            </div>
                                            <div class="sd-progress mt-2" title="<?= (int) $vacancyProgress; ?>% of all applicants tagged"><span style="width:<?= (int) $vacancyProgress; ?>%"></span></div>
                                        </div>
                                        <div class="sd-number"><strong><?= (int) $vacancy->applicant_total; ?></strong><span>Applicants</span></div>
                                        <div class="sd-number"><strong class="text-success"><?= (int) $vacancy->tagged_total; ?></strong><span>Tagged</span></div>
                                        <div class="sd-number"><strong class="text-danger"><?= (int) $vacancy->pending_total; ?></strong><span>Waiting</span></div>
                                        <a href="<?= base_url('secretariat/applicant-tagging?job_id=' . (int) $vacancy->jobID); ?>" class="btn btn-outline-primary btn-sm">Manage</a>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 mb-3">
                    <div class="card sd-card mb-3">
                        <div class="card-body">
                            <div class="sd-section-title mb-1">Your vacancy coverage</div>
                            <p class="text-muted mb-2">Positions tagged to your account</p>
                            <?php if (empty($assigned)) : ?>
                                <span class="text-muted">Waiting for assignment.</span>
                            <?php else : ?>
                                <?php foreach ($assigned as $label) : ?><span class="sd-scope"><?= $dashboard_h($label); ?></span><?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="card sd-card">
                        <div class="card-body">
                            <div class="sd-section-title mb-1">Recruitment shortcuts</div>
                            <div class="sd-link-list">
                                <a href="<?= base_url('secretariat/applicant-tagging'); ?>"><span><i class="mdi mdi-account-arrow-right-outline mr-2 text-primary"></i>Applicant tagging</span><i class="mdi mdi-chevron-right"></i></a>
                                <a href="<?= base_url('Pages/endorsed_applicants'); ?>"><span><i class="mdi mdi-send-check-outline mr-2 text-info"></i>Endorse applicants</span><i class="mdi mdi-chevron-right"></i></a>
                                <a href="<?= base_url('Pages/endorsed_applicants_unassigned'); ?>"><span><i class="mdi mdi-account-alert-outline mr-2 text-warning"></i>Endorsed without evaluator</span><span class="badge badge-warning"><?= (int) $counts['no_rater']; ?></span></a>
                                <a href="<?= base_url('Pages/secretariat_endorsed'); ?>"><span><i class="mdi mdi-chart-box-outline mr-2 text-success"></i>Endorsed &amp; scored</span><span class="badge badge-light"><?= (int) $counts['endorsed']; ?></span></a>
                                <a href="<?= base_url('Pages/secretariat_dq_applicants'); ?>"><span><i class="mdi mdi-account-remove-outline mr-2 text-danger"></i>Disqualified applicants</span><span class="badge badge-light"><?= (int) $counts['dq']; ?></span></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
