<?php
$ret_h = static function ($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};

$positionGroups = [
    1 => 'Teaching',
    2 => 'School Administration',
    3 => 'Related Teaching',
    4 => 'Non-Teaching',
];

$vacancies = $vacancies ?? [];
$requests = $requests ?? [];
$retentionCounts = $retentionCounts ?? [];
$selectedVacancy = $selectedVacancy ?? null;
$selectedJobId = (int) ($selectedJobId ?? 0);
$pType = (int) ($pType ?? 0);
$maxPoints = $maxPoints ?? [];
$scoreMap = $scoreMap ?? [];

$pending = [];
$resolved = [];
foreach ($requests as $request) {
    if ((int) $request->stat === 0) {
        $pending[] = $request;
    } else {
        $resolved[] = $request;
    }
}

// Most requests have no earlier scored application to copy from; the header
// says so up front rather than leaving the empty dropdowns to explain it.
$copyable = 0;
foreach ($pending as $request) {
    if ((int) $request->source_count > 0) {
        $copyable++;
    }
}
$noSource = max(0, count($pending) - $copyable);

$applicantName = static function ($request) {
    $name = trim(implode(' ', array_filter([
        trim((string) ($request->FirstName ?? '')),
        trim((string) ($request->MiddleName ?? '')),
        trim((string) ($request->LastName ?? '')),
        trim((string) ($request->NameExtn ?? '')),
    ], static function ($part) { return $part !== ''; })));

    return $name !== '' ? $name : 'Applicant ' . (string) ($request->applicant_id ?? '');
};

$profileUrl = static function ($request) {
    if (empty($request->profile_route) || empty($request->appID)) {
        return '';
    }

    return base_url('Pages/' . $request->profile_route . '/'
        . rawurlencode((string) $request->profile_id) . '/'
        . (int) ($request->vacancy->jobID ?? 0) . '/'
        . rawurlencode((string) $request->pre_school) . '/'
        . (int) $request->appID . '/'
        . rawurlencode((string) $request->record_no));
};

$scopeLabel = static function ($request) use ($pType) {
    if ((int) $request->r_type === 2) {
        return $pType === 1
            ? 'Demonstration Teaching and Teacher Reflection only'
            : 'Interview and Written Examination only';
    }
    return 'All criteria';
};

$fmtDate = static function ($value) {
    $value = trim((string) $value);
    if ($value === '' || strpos($value, '0000-00-00') === 0) {
        return '';
    }
    $stamp = strtotime($value);
    return $stamp ? date('M j, Y', $stamp) : $value;
};

$evaluatorName = static function ($request) {
    $name = trim((string) ($request->evaluator_name ?? ''));
    return $name !== '' ? $name : '';
};

$fmtMax = static function ($value) {
    $value = (float) $value;
    return $value > 0 ? rtrim(rtrim(number_format($value, 2, '.', ''), '0'), '.') : '';
};
?>

<style>
    .sr-page { --sr-ink:#1f2a37; --sr-muted:#6d7885; --sr-line:#e6e9ef; --sr-accent:#5a4bc4; --sr-soft:#f7f8fb; padding-bottom:34px; }
    .sr-page .container-fluid { max-width:1280px; }

    .sr-page .sr-head { border-bottom:1px solid var(--sr-line); margin-bottom:20px; padding:6px 0 16px; }
    .sr-page .sr-head h2 { color:var(--sr-ink); font-size:22px; font-weight:700; letter-spacing:-.01em; margin:0; }
    .sr-page .sr-head p { color:var(--sr-muted); font-size:13.5px; line-height:1.55; margin:6px 0 0; max-width:700px; }
    .sr-page .sr-head-row { align-items:flex-start; display:flex; flex-wrap:wrap; gap:12px; justify-content:space-between; }
    .sr-page .sr-head-link { align-items:center; border:1px solid #d6dbe4; border-radius:8px; color:#4a5566; display:inline-flex; font-size:12.5px; font-weight:600; gap:6px; padding:8px 13px; white-space:nowrap; }
    .sr-page .sr-head-link:hover { background:#f7f8fb; border-color:#bcc4d1; color:var(--sr-accent); text-decoration:none; }

    .sr-page .sr-card { background:#fff; border:1px solid var(--sr-line); border-radius:12px; box-shadow:none; margin-bottom:18px; }
    .sr-page .sr-card .card-body { padding:20px 22px; }
    .sr-page .sr-card-head { align-items:center; border-bottom:1px solid var(--sr-line); display:flex; flex-wrap:wrap; gap:8px; justify-content:space-between; padding:16px 22px; }
    .sr-page .sr-card-title { color:var(--sr-ink); font-size:15px; font-weight:700; margin:0; }
    .sr-page .sr-card-count { color:var(--sr-muted); font-size:12.5px; }
    .sr-page .sr-pending-tools { align-items:center; display:flex; flex-wrap:wrap; gap:10px; }
    .sr-page .sr-search-form { align-items:center; display:flex; gap:7px; }
    .sr-page .sr-search-form input { border:1px solid #d6dbe4; border-radius:7px; font-size:13px; height:36px; min-width:230px; padding:6px 10px; }
    .sr-page .sr-search-form input:focus { border-color:var(--sr-accent); box-shadow:0 0 0 3px rgba(90,75,196,.12); outline:none; }
    .sr-page .sr-search-button { background:var(--sr-accent); border:0; border-radius:7px; color:#fff; font-size:12.5px; font-weight:600; height:36px; padding:0 13px; }
    .sr-page .sr-search-button:hover, .sr-page .sr-search-button:focus { background:#4a3cae; color:#fff; }
    .sr-page .sr-search-clear { background:#fff; border:1px solid #d6dbe4; border-radius:7px; color:var(--sr-muted); font-size:12.5px; height:36px; padding:0 11px; }

    .sr-page label.sr-label { color:var(--sr-ink); display:block; font-size:12.5px; font-weight:600; margin-bottom:6px; }
    .sr-page .sr-select { border:1px solid #d6dbe4; border-radius:8px; box-shadow:none; color:var(--sr-ink); height:42px; }
    .sr-page .sr-select:focus { border-color:var(--sr-accent); box-shadow:0 0 0 3px rgba(90,75,196,.12); }
    .sr-page .sr-btn-go { background:var(--sr-accent); border:0; border-radius:8px; color:#fff; font-size:13.5px; font-weight:600; height:42px; }
    .sr-page .sr-btn-go:hover, .sr-page .sr-btn-go:focus { background:#4a3cae; color:#fff; }

    .sr-page .sr-vacancy { align-items:flex-start; display:flex; flex-wrap:wrap; gap:12px; justify-content:space-between; }
    .sr-page .sr-vacancy h3 { color:var(--sr-ink); font-size:18px; font-weight:700; margin:0 0 4px; }
    .sr-page .sr-vacancy-meta { color:var(--sr-muted); font-size:13px; }
    .sr-page .sr-link { color:var(--sr-accent); font-size:13px; font-weight:600; }
    .sr-page .sr-link:hover { color:#4a3cae; text-decoration:underline; }

    .sr-page .sr-stats { display:grid; gap:12px; grid-template-columns:repeat(4,1fr); margin-top:18px; }
    .sr-page .sr-stat { border:1px solid var(--sr-line); border-radius:10px; padding:13px 15px; }
    .sr-page .sr-stat-label { color:var(--sr-muted); font-size:12px; font-weight:600; }
    .sr-page .sr-stat-value { color:var(--sr-ink); font-size:26px; font-weight:700; letter-spacing:-.02em; line-height:1.1; margin-top:4px; }
    .sr-page .sr-stat-note { color:#98a1ad; font-size:11.5px; margin-top:3px; }
    .sr-page .sr-stat-pending .sr-stat-value { color:#b1770b; }
    .sr-page .sr-stat-granted .sr-stat-value { color:#1a7a4c; }
    .sr-page .sr-stat-denied .sr-stat-value { color:#b03636; }
    .sr-page .sr-stat-link { display:block; text-decoration:none; transition:background .14s ease,border-color .14s ease,box-shadow .14s ease; }
    .sr-page .sr-stat-link:hover, .sr-page .sr-stat-link:focus { background:#fff6f6; border-color:#e2b9b9; box-shadow:0 4px 12px rgba(176,54,54,.1); text-decoration:none; }
    .sr-page .sr-stat-link .sr-stat-label { color:#b03636; }
    .sr-page .sr-stat-link .sr-stat-label i { font-size:13px; opacity:.75; vertical-align:-1px; }
    .sr-page .sr-stat-link:hover .sr-stat-note { color:#b03636; }

    .sr-page .sr-hint { color:var(--sr-muted); font-size:12.5px; line-height:1.55; margin:14px 0 0; }
    .sr-page .sr-hint strong { color:var(--sr-ink); font-weight:600; }

    .sr-page .sr-req { border:1px solid var(--sr-line); border-radius:11px; margin:0 22px 14px; overflow:hidden; }
    .sr-page .sr-req:first-of-type { margin-top:18px; }
    .sr-page .sr-req-head { align-items:flex-start; background:var(--sr-soft); border-bottom:1px solid var(--sr-line); display:flex; flex-wrap:wrap; gap:10px; justify-content:space-between; padding:13px 16px; }
    .sr-page .sr-name { color:var(--sr-ink); font-size:14.5px; font-weight:650; }
    .sr-page .sr-sub { color:var(--sr-muted); font-size:12.5px; margin-top:3px; }
    .sr-page .sr-sub a { color:var(--sr-accent); font-weight:600; }
    .sr-page .sr-chip { border-radius:6px; display:inline-flex; font-size:11.5px; font-weight:600; padding:4px 9px; white-space:nowrap; }
    .sr-page .sr-chip-scope { background:#f1efff; color:#5546b5; }
    .sr-page .sr-chip-granted { background:#e7f5ed; color:#1a7a4c; }
    .sr-page .sr-chip-denied { background:#fbeaea; color:#b03636; }
    .sr-page .sr-chip-pending { background:#fdf3e0; color:#96650a; }
    .sr-page .sr-chip-warn { background:#fbeaea; color:#b03636; }
    .sr-page .sr-chip-eval { background:#e8f1fb; color:#245b95; }
    .sr-page .sr-chip-noeval { background:#f2f4f7; color:#77808d; }
    .sr-page .sr-eval-cell { color:var(--sr-ink); font-size:12.5px; }
    .sr-page .sr-eval-cell .sr-eval-none { color:#8b939e; font-style:italic; }
    .sr-page .sr-dq-reason { background:#fff1f1; border-left:3px solid #c9524e; border-radius:0 7px 7px 0; color:#8f2b2b; flex:1 0 100%; font-size:12.5px; line-height:1.5; padding:8px 10px; }
    .sr-page .sr-dq-reason strong { font-weight:650; }
    .sr-page .sr-dq-detail { color:#8f2b2b; font-size:12px; line-height:1.45; margin-bottom:6px; }

    .sr-page .sr-req-body { padding:16px; }
    .sr-page .sr-routes { display:grid; gap:14px; grid-template-columns:1fr 1fr; }
    .sr-page .sr-route { border:1px solid var(--sr-line); border-radius:10px; padding:14px 15px; }
    .sr-page .sr-route h6 { color:var(--sr-ink); font-size:13.5px; font-weight:650; margin:0 0 3px; }
    .sr-page .sr-route p { color:var(--sr-muted); font-size:12.5px; line-height:1.5; margin:0 0 12px; }
    .sr-page .sr-route .form-control-sm { border:1px solid #d6dbe4; border-radius:7px; height:36px; }
    .sr-page .sr-copy-help { background:#f1efff; border-radius:7px; color:#5546b5; font-size:12px; line-height:1.45; margin-top:10px; padding:8px 10px; }
    .sr-page .sr-prefill-status { background:#eaf6f0; border-radius:7px; color:#176b4a; display:none; font-size:12px; line-height:1.45; margin-top:11px; padding:8px 10px; }
    .sr-page .sr-prefill-status.is-visible { display:block; }

    .sr-page .sr-score-grid { display:grid; gap:10px 12px; grid-template-columns:repeat(auto-fill,minmax(165px,1fr)); }
    /* Criterion names are spelled out in full, so a label can run to two or
       three lines - the cell is a column with the label growing, which keeps
       every input on the same baseline across the row. */
    .sr-page .sr-score { display:flex; flex-direction:column; }
    .sr-page .sr-score label { color:var(--sr-muted); flex:1 0 auto; font-size:12px; font-weight:600; line-height:1.35; margin-bottom:5px; }
    .sr-page .sr-score input { border:1px solid #d6dbe4; border-radius:7px; color:var(--sr-ink); font-size:14px; padding:7px 9px; width:100%; }
    .sr-page .sr-score input:focus { border-color:var(--sr-accent); box-shadow:0 0 0 3px rgba(90,75,196,.12); outline:none; }
    .sr-page .sr-total { align-items:baseline; background:var(--sr-soft); border-radius:8px; color:var(--sr-muted); display:flex; font-size:12.5px; gap:7px; margin-top:12px; padding:9px 12px; }
    .sr-page .sr-total-value { color:var(--sr-ink); font-size:16px; font-weight:700; }

    .sr-page .sr-action { border-radius:8px; font-size:13px; font-weight:600; padding:7px 14px; }
    .sr-page .sr-action-save { background:#17845a; border:0; color:#fff; }
    .sr-page .sr-action-save:hover, .sr-page .sr-action-save:focus { background:#136d4a; color:#fff; }
    .sr-page .sr-action-deny { background:#fff; border:1px solid #e0b6b6; color:#b03636; }
    .sr-page .sr-action-deny:hover, .sr-page .sr-action-deny:focus { background:#fbeaea; color:#8f2b2b; }

    .sr-page .sr-empty-route { color:var(--sr-muted); font-size:12.5px; line-height:1.5; }
    .sr-page .sr-deny { align-items:center; border-top:1px solid var(--sr-line); display:flex; flex-wrap:wrap; gap:10px; margin-top:15px; padding-top:14px; }
    .sr-page .sr-deny-label { color:var(--sr-muted); font-size:12.5px; font-weight:600; }
    .sr-page .sr-deny-form { align-items:center; display:flex; flex:1 1 320px; gap:9px; }
    .sr-page .sr-deny-form input[type="text"] { border:1px solid #d6dbe4; border-radius:7px; flex:1 1 auto; font-size:13px; height:36px; max-width:420px; min-width:0; padding:6px 10px; }
    .sr-page .sr-deny-form input[type="text"]:focus { border-color:#c98d8d; box-shadow:0 0 0 3px rgba(176,54,54,.1); outline:none; }

    .sr-page .sr-nothing { color:var(--sr-muted); font-size:13px; padding:30px 22px; text-align:center; }
    .sr-page .sr-table-wrap { padding:6px 22px 16px; }
    .sr-page table.sr-resolved { margin:0; width:100% !important; }
    .sr-page table.sr-resolved thead th { background:var(--sr-soft); border-bottom:1px solid var(--sr-line); border-top:0; color:#5b6673; font-size:11.5px; font-weight:650; letter-spacing:.02em; padding:11px 12px; text-transform:uppercase; }
    .sr-page table.sr-resolved td { border-color:#eef0f4; font-size:13px; padding:12px; vertical-align:middle; }
    .sr-page table.sr-resolved tbody tr:hover { background:#fafbfd; }
    .sr-page .dataTables_wrapper .dataTables_filter input { border:1px solid #d6dbe4; border-radius:7px; margin-left:7px; padding:5px 9px; }
    .sr-page .dataTables_wrapper .dataTables_length select { border:1px solid #d6dbe4; border-radius:7px; padding:4px 20px 4px 7px; }
    .sr-page .dataTables_wrapper .dataTables_info,
    .sr-page .dataTables_wrapper .dataTables_length,
    .sr-page .dataTables_wrapper .dataTables_filter { color:var(--sr-muted); font-size:12.5px; }
    .sr-page .page-item.active .page-link { background-color:var(--sr-accent); border-color:var(--sr-accent); }

    @media (max-width:991px) {
        .sr-page .sr-stats { grid-template-columns:repeat(2,1fr); }
        .sr-page .sr-routes { grid-template-columns:1fr; }
    }
    @media (max-width:575px) {
        .sr-page .sr-stats { grid-template-columns:1fr; }
        .sr-page .sr-req { margin-left:14px; margin-right:14px; }
        .sr-page .sr-card-head, .sr-page .sr-table-wrap { padding-left:14px; padding-right:14px; }
        .sr-page .sr-pending-tools, .sr-page .sr-search-form { align-items:stretch; width:100%; }
        .sr-page .sr-search-form { flex-wrap:wrap; }
        .sr-page .sr-search-form input { flex:1 1 100%; min-width:0; }
    }
</style>

<div class="content-page sr-page">
    <div class="content">
        <div class="container-fluid">

            <div class="sr-head sr-head-row">
                <div>
                    <h2>Retention of Points</h2>
                    <p>Applicants asking to keep points they already earned. Copy the scores from one of their earlier applications, or encode the retained score yourself.</p>
                </div>
                <a href="<?= base_url('secretariat/retention/denied' . ($selectedJobId > 0 ? '?job_id=' . $selectedJobId : '')); ?>" class="sr-head-link">
                    <i class="mdi mdi-close-circle-outline"></i>View denied requests
                </a>
            </div>

            <?php foreach (['success' => 'alert-success', 'danger' => 'alert-danger'] as $flash => $class) : ?>
                <?php if ($this->session->flashdata($flash)) : ?>
                    <div class="alert <?= $class; ?> alert-dismissible fade show" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <?= $ret_h($this->session->flashdata($flash)); ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>

            <div class="card sr-card">
                <div class="card-body">
                    <?php if (empty($vacancies)) : ?>
                        <div class="alert alert-warning mb-0">No retention request was found for your assigned open vacancies.</div>
                    <?php else : ?>
                        <form method="get" action="<?= base_url('secretariat/retention'); ?>">
                            <div class="form-row align-items-end">
                                <div class="col-lg-9 col-md-8">
                                    <label for="job_id" class="sr-label">Vacancy applied for</label>
                                    <select name="job_id" id="job_id" class="form-control sr-select" required>
                                        <option value="">Choose a position...</option>
                                        <?php foreach ($vacancies as $vacancy) : ?>
                                            <?php
                                            $jobId = (int) $vacancy->jobID;
                                            $vc = $retentionCounts[$jobId] ?? ['pending' => 0, 'total' => 0];
                                            $group = $positionGroups[(int) $vacancy->position] ?? 'Vacancy';
                                            ?>
                                            <option value="<?= $jobId; ?>" <?= $jobId === $selectedJobId ? 'selected' : ''; ?>>
                                                <?= $ret_h($vacancy->jobTitle . ' — ' . $group . ' — FY ' . $vacancy->sy); ?>
                                                (<?= (int) $vc['pending']; ?> pending of <?= (int) $vc['total']; ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-lg-3 col-md-4 mt-2 mt-md-0">
                                    <button type="submit" class="btn btn-block sr-btn-go">View requests</button>
                                </div>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($selectedVacancy) : ?>
                <?php $counts = $retentionCounts[(int) $selectedVacancy->jobID] ?? ['pending' => 0, 'granted' => 0, 'denied' => 0, 'total' => 0]; ?>

                <div class="card sr-card">
                    <div class="card-body">
                        <div class="sr-vacancy">
                            <div>
                                <h3><?= $ret_h($selectedVacancy->jobTitle); ?></h3>
                                <div class="sr-vacancy-meta">
                                    <?= $ret_h($positionGroups[(int) $selectedVacancy->position] ?? 'Vacancy'); ?>
                                    &middot; FY <?= $ret_h($selectedVacancy->sy); ?>
                                    <?php if (!empty($selectedVacancy->itemNo)) : ?>&middot; Item <?= $ret_h($selectedVacancy->itemNo); ?><?php endif; ?>
                                </div>
                            </div>
                            <a href="<?= base_url('secretariat/retention'); ?>" class="sr-link">Change position</a>
                        </div>

                        <div class="sr-stats">
                            <div class="sr-stat sr-stat-pending">
                                <div class="sr-stat-label">Pending</div>
                                <div class="sr-stat-value"><?= (int) $counts['pending']; ?></div>
                                <div class="sr-stat-note">Waiting for a decision</div>
                            </div>
                            <div class="sr-stat">
                                <div class="sr-stat-label">Ready to copy</div>
                                <div class="sr-stat-value"><?= (int) $copyable; ?></div>
                                <div class="sr-stat-note">Have an earlier score on file</div>
                            </div>
                            <div class="sr-stat sr-stat-granted">
                                <div class="sr-stat-label">Granted</div>
                                <div class="sr-stat-value"><?= (int) $counts['granted']; ?></div>
                                <div class="sr-stat-note">Points retained</div>
                            </div>
                            <?php if ((int) $counts['denied'] > 0) : ?>
                                <a class="sr-stat sr-stat-denied sr-stat-link"
                                   href="<?= base_url('secretariat/retention/denied?job_id=' . (int) $selectedVacancy->jobID); ?>"
                                   title="Open the denied list for this vacancy">
                                    <div class="sr-stat-label">Denied <i class="mdi mdi-arrow-top-right"></i></div>
                                    <div class="sr-stat-value"><?= (int) $counts['denied']; ?></div>
                                    <div class="sr-stat-note">Refused with a reason &mdash; view list</div>
                                </a>
                            <?php else : ?>
                                <div class="sr-stat sr-stat-denied">
                                    <div class="sr-stat-label">Denied</div>
                                    <div class="sr-stat-value">0</div>
                                    <div class="sr-stat-note">Refused with a reason</div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <?php if ($noSource > 0) : ?>
                            <p class="sr-hint">
                                <strong><?= (int) $noSource; ?> of <?= (int) count($pending); ?> pending request<?= count($pending) === 1 ? '' : 's'; ?></strong>
                                have no earlier score to copy &mdash; encode those by hand. Either way the request is granted the same.
                            </p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card sr-card">
                    <div class="sr-card-head">
                        <h5 class="sr-card-title">Pending requests</h5>
                        <div class="sr-pending-tools">
                            <span class="sr-card-count" id="retention-pending-count"><?= count($pending); ?> waiting</span>
                            <?php if (!empty($pending)) : ?>
                                <form class="sr-search-form" id="retention-applicant-search" role="search">
                                    <label class="sr-only" for="retention-applicant-query">Search applicant</label>
                                    <input type="search" id="retention-applicant-query"
                                           placeholder="Applicant name or number" autocomplete="off">
                                    <button type="submit" class="sr-search-button">Search</button>
                                    <button type="button" class="sr-search-clear" id="retention-applicant-clear" hidden>Clear</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if (empty($pending)) : ?>
                        <div class="sr-nothing">Nothing is waiting for a decision on this position.</div>
                    <?php endif; ?>

                    <div class="sr-nothing" id="retention-search-empty" hidden>No pending applicant matches your search.</div>

                    <?php foreach ($pending as $request) : ?>
                        <?php
                        $name = $applicantName($request);
                        $url = $profileUrl($request);
                        // Only the criteria this request's scope actually carries.
                        $rowMap = $request->score_map ?? $scoreMap;
                        ?>
                        <div class="sr-req" data-applicant-search="<?= $ret_h(implode(' ', [
                            $name,
                            (string) ($request->record_no ?? ''),
                            (string) ($request->applicant_id ?? ''),
                            (string) ($request->appID ?? ''),
                            (string) ($request->empEmail ?? ''),
                            (string) ($request->evaluator_name ?? ''),
                        ])); ?>">
                            <div class="sr-req-head">
                                <div>
                                    <div class="sr-name"><?= $ret_h($name); ?></div>
                                    <div class="sr-sub">
                                        No. <?= $ret_h($request->record_no); ?>
                                        <?php if ($url !== '') : ?>&middot; <a href="<?= $ret_h($url); ?>" target="_blank" rel="noopener">View application</a><?php endif; ?>
                                        <?php if ($fmtDate($request->rdate) !== '') : ?>&middot; Requested <?= $ret_h($fmtDate($request->rdate)); ?><?php endif; ?>
                                    </div>
                                </div>
                                <div>
                                    <span class="sr-chip sr-chip-scope"><?= $ret_h($scopeLabel($request)); ?></span>
                                    <?php $evalName = $evaluatorName($request); ?>
                                    <?php if ($evalName !== '') : ?>
                                        <span class="sr-chip sr-chip-eval ml-1" title="Evaluator assigned to this applicant">Evaluator: <?= $ret_h($evalName); ?></span>
                                    <?php else : ?>
                                        <span class="sr-chip sr-chip-noeval ml-1" title="No evaluator has been tagged for this applicant">No evaluator</span>
                                    <?php endif; ?>
                                    <?php if ((int) $request->dq === 2) : ?>
                                        <span class="sr-chip sr-chip-warn ml-1">Disqualified</span>
                                    <?php endif; ?>
                                </div>
                                <?php if ((int) $request->dq === 2) : ?>
                                    <?php $dqReason = trim((string) ($request->dq_reason ?? '')); ?>
                                    <div class="sr-dq-reason">
                                        <strong>Disqualification reason:</strong>
                                        <?= $dqReason !== '' ? nl2br($ret_h($dqReason)) : 'No reason was recorded.'; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="sr-req-body">
                                <div class="sr-routes">

                                    <div class="sr-route">
                                        <h6>Load from an earlier application</h6>
                                        <p>Select a scored application to fill the encoding fields for review.</p>

                                        <?php if ((int) $request->source_count === 0) : ?>
                                            <div class="sr-empty-route">
                                                No earlier application of this applicant has a score to copy. Encode it by hand instead.
                                            </div>
                                        <?php else : ?>
                                            <?php $sources = $request->sources ?? []; ?>
                                            <div class="form-group mb-0">
                                                <select class="form-control form-control-sm sr-copy-source">
                                                    <option value="">Choose an application...</option>
                                                    <?php foreach ($sources as $source) : ?>
                                                        <?php
                                                        // Source scores arrive keyed by their display label. Re-key
                                                        // them to the manual input columns for a safe client-side
                                                        // preview. The 0.00001 sentinel means "not rated yet", so it
                                                        // stays blank and must be reviewed before saving.
                                                        $sourceScores = [];
                                                        foreach ($rowMap as $scoreLabel => $scoreColumn) {
                                                            $scoreValue = $source['scores'][$scoreLabel] ?? null;
                                                            if ($scoreValue === null || $scoreValue === '' || !is_numeric($scoreValue)) {
                                                                continue;
                                                            }
                                                            $scoreValue = (float) $scoreValue;
                                                            if (abs($scoreValue - 0.00001) < 0.000001) {
                                                                continue;
                                                            }
                                                            $sourceScores[$scoreColumn] = $scoreValue;
                                                        }
                                                        $sourceTitle = $source['title'] ?: 'Application #' . $source['app_id'];
                                                        $sourceLabel = $sourceTitle
                                                            . ($source['sy'] !== '' ? ' — FY ' . $source['sy'] : '')
                                                            . ' — ' . number_format((float) $source['total_points'], 2) . ' pts';
                                                        ?>
                                                        <option value="<?= (int) $source['app_id']; ?>"
                                                                data-scores="<?= $ret_h(json_encode($sourceScores)); ?>"
                                                                data-source-label="<?= $ret_h($sourceLabel); ?>">
                                                            <?= $ret_h($sourceLabel); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="sr-copy-help">Selecting an application loads its scores into <strong>Encode the score</strong> for review. It does not save or grant the request.</div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="sr-route">
                                        <h6>Encode the score</h6>
                                        <p>Enter the points the applicant earned before.</p>

                                        <form method="post" action="<?= base_url('secretariat/retention/grant-manual'); ?>"
                                              class="sr-manual-form"
                                              onsubmit="return confirm('Save this retained score and grant the retention?');">
                                            <input type="hidden" name="id" value="<?= (int) $request->request_id; ?>">
                                            <input type="hidden" name="app_id" value="<?= (int) $request->appID; ?>">

                                            <div class="sr-score-grid">
                                                <?php foreach ($rowMap as $label => $column) : ?>
                                                    <?php $cap = $fmtMax($maxPoints[$column] ?? 0); ?>
                                                    <div class="sr-score">
                                                        <label for="m<?= (int) $request->request_id; ?>_<?= $ret_h($column); ?>"><?= $ret_h($label); ?></label>
                                                        <input type="number" step="0.01" min="0"
                                                               <?= $cap !== '' ? 'max="' . $ret_h($cap) . '" title="Maximum ' . $ret_h($cap) . ' points"' : ''; ?>
                                                               id="m<?= (int) $request->request_id; ?>_<?= $ret_h($column); ?>"
                                                               name="<?= $ret_h($column); ?>" required>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>

                                            <div class="sr-total">Total <span class="sr-total-value">0.00</span></div>
                                            <div class="sr-prefill-status" aria-live="polite"></div>

                                            <button type="submit" class="btn sr-action sr-action-save mt-3">Save score</button>
                                        </form>
                                    </div>
                                </div>

                                <div class="sr-deny">
                                    <span class="sr-deny-label">Deny instead?</span>
                                    <form method="post" action="<?= base_url('secretariat/retention/deny'); ?>"
                                          class="sr-deny-form"
                                          onsubmit="return confirm('Deny this retention request?');">
                                        <input type="hidden" name="id" value="<?= (int) $request->request_id; ?>">
                                        <input type="hidden" name="app_id" value="<?= (int) $request->appID; ?>">
                                        <input type="text" name="deny_reason" placeholder="Reason for denying" required>
                                        <button type="submit" class="btn sr-action sr-action-deny">Deny</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="card sr-card">
                    <div class="sr-card-head">
                        <h5 class="sr-card-title">Already decided</h5>
                        <span class="sr-card-count"><?= count($resolved); ?> resolved</span>
                    </div>

                    <div class="sr-table-wrap">
                        <div class="table-responsive">
                            <table class="table table-hover sr-resolved" id="retention-resolved">
                                <thead>
                                    <tr>
                                        <th style="width:23%">Applicant</th>
                                        <th style="width:15%">Evaluator</th>
                                        <th style="width:13%">Scope</th>
                                        <th style="width:10%">Decision</th>
                                        <th style="width:13%">Decided</th>
                                        <th style="width:26%">Detail</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($resolved as $request) : ?>
                                        <?php $url = $profileUrl($request); ?>
                                        <tr>
                                            <td>
                                                <div class="sr-name"><?= $ret_h($applicantName($request)); ?></div>
                                                <div class="sr-sub">
                                                    No. <?= $ret_h($request->record_no); ?>
                                                    <?php if ($url !== '') : ?>&middot; <a href="<?= $ret_h($url); ?>" target="_blank" rel="noopener">View</a><?php endif; ?>
                                                </div>
                                            </td>
                                            <td class="sr-eval-cell">
                                                <?php $evalName = $evaluatorName($request); ?>
                                                <?= $evalName !== '' ? $ret_h($evalName) : '<span class="sr-eval-none">Not tagged</span>'; ?>
                                            </td>
                                            <td><?= $ret_h($scopeLabel($request)); ?></td>
                                            <td>
                                                <?php if ((int) $request->stat === 1) : ?>
                                                    <span class="sr-chip sr-chip-granted">Granted</span>
                                                <?php elseif ((int) $request->stat === 2) : ?>
                                                    <span class="sr-chip sr-chip-denied">Denied</span>
                                                <?php else : ?>
                                                    <span class="sr-chip sr-chip-pending">Pending</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?= $ret_h($fmtDate($request->adate)); ?>
                                                <?php if (trim((string) $request->resolved_by) !== '') : ?>
                                                    <div class="sr-sub">by <?= $ret_h($request->resolved_by); ?></div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ((int) $request->dq === 2) : ?>
                                                    <?php $dqReason = trim((string) ($request->dq_reason ?? '')); ?>
                                                    <div class="sr-dq-detail">
                                                        <strong>Disqualified:</strong>
                                                        <?= $dqReason !== '' ? nl2br($ret_h($dqReason)) : 'No reason was recorded.'; ?>
                                                    </div>
                                                <?php endif; ?>
                                                <?php if ((int) $request->stat === 2 && trim((string) $request->deny_reason) !== '') : ?>
                                                    <span class="text-danger"><?= $ret_h($request->deny_reason); ?></span>
                                                <?php elseif ((int) $request->stat === 1) : ?>
                                                    <span class="text-muted">Application marked <?= $ret_h($request->appStatus ?: 'updated'); ?>.</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
(function () {
    // Running total mirrors the sum the server stores as total_points, so the
    // encoder sees the figure that will be ranked before saving.
    function wire(form) {
        var inputs = form.querySelectorAll('input[type="number"]');
        var output = form.querySelector('.sr-total-value');
        if (!output) return;

        function recalc() {
            var total = 0;
            Array.prototype.forEach.call(inputs, function (input) {
                var value = parseFloat(input.value);
                if (!isNaN(value)) total += value;

                // The ceiling is no longer printed next to the box, so an
                // over-max entry is flagged on the field itself instead.
                var max = parseFloat(input.getAttribute('max'));
                var over = !isNaN(max) && !isNaN(value) && value > max;
                input.style.borderColor = over ? '#c9524e' : '#d6dbe4';
            });
            output.textContent = total.toFixed(2);
        }

        Array.prototype.forEach.call(inputs, function (input) {
            input.addEventListener('input', recalc);
        });
        recalc();
    }

    Array.prototype.forEach.call(document.querySelectorAll('.sr-manual-form'), wire);

    // Pending requests are cards rather than a table, so give them a small
    // applicant-specific search without a page reload.
    var applicantSearch = document.getElementById('retention-applicant-search');
    if (applicantSearch) {
        var applicantQuery = document.getElementById('retention-applicant-query');
        var applicantClear = document.getElementById('retention-applicant-clear');
        var pendingCount = document.getElementById('retention-pending-count');
        var noMatches = document.getElementById('retention-search-empty');
        var requestCards = document.querySelectorAll('.sr-req[data-applicant-search]');

        function filterApplicants() {
            var query = (applicantQuery.value || '').trim().toLocaleLowerCase();
            var visible = 0;

            Array.prototype.forEach.call(requestCards, function (card) {
                var haystack = (card.getAttribute('data-applicant-search') || '').toLocaleLowerCase();
                var matches = query === '' || haystack.indexOf(query) !== -1;
                card.hidden = !matches;
                if (matches) visible++;
            });

            pendingCount.textContent = query === ''
                ? requestCards.length + ' waiting'
                : visible + ' of ' + requestCards.length + ' shown';
            noMatches.hidden = query === '' || visible > 0;
            applicantClear.hidden = query === '';
        }

        applicantSearch.addEventListener('submit', function (event) {
            event.preventDefault();
            filterApplicants();
        });

        applicantClear.addEventListener('click', function () {
            applicantQuery.value = '';
            filterApplicants();
            applicantQuery.focus();
        });
    }

    // A copyable application is a preview source. Selecting it fills the
    // matching manual fields and recalculates the total, but deliberately does
    // not submit either form. The reviewer can inspect/edit before Save score.
    Array.prototype.forEach.call(document.querySelectorAll('.sr-copy-source'), function (select) {
        select.addEventListener('change', function () {
            var requestCard = select.closest('.sr-req');
            var manualForm = requestCard ? requestCard.querySelector('.sr-manual-form') : null;
            if (!manualForm) return;

            var selected = select.options[select.selectedIndex];
            var scores = {};
            try {
                scores = JSON.parse(selected.getAttribute('data-scores') || '{}');
            } catch (error) {
                scores = {};
            }

            Array.prototype.forEach.call(manualForm.querySelectorAll('input[type="number"]'), function (input) {
                input.value = Object.prototype.hasOwnProperty.call(scores, input.name)
                    ? scores[input.name]
                    : '';
                input.dispatchEvent(new Event('input', { bubbles: true }));
            });

            var status = manualForm.querySelector('.sr-prefill-status');
            if (!status) return;

            if (select.value === '') {
                status.textContent = '';
                status.classList.remove('is-visible');
                return;
            }

            status.textContent = 'Scores loaded from '
                + (selected.getAttribute('data-source-label') || 'the selected application')
                + '. Review or edit them, then click Save score. Nothing has been saved yet.';
            status.classList.add('is-visible');
        });
    });

    if (window.jQuery && jQuery.fn && jQuery.fn.DataTable) {
        var table = jQuery('#retention-resolved');
        if (table.length && table.find('tbody tr').length) {
            table.DataTable({
                pageLength: 10,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'All']],
                order: [[0, 'asc']],
                autoWidth: false,
                language: { emptyTable: 'No retention request has been decided yet.' }
            });
        }
    }
})();
</script>
