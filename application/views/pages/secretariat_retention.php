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

    .sr-page .sr-card { background:#fff; border:1px solid var(--sr-line); border-radius:12px; box-shadow:none; margin-bottom:18px; }
    .sr-page .sr-card .card-body { padding:20px 22px; }
    .sr-page .sr-card-head { align-items:center; border-bottom:1px solid var(--sr-line); display:flex; flex-wrap:wrap; gap:8px; justify-content:space-between; padding:16px 22px; }
    .sr-page .sr-card-title { color:var(--sr-ink); font-size:15px; font-weight:700; margin:0; }
    .sr-page .sr-card-count { color:var(--sr-muted); font-size:12.5px; }

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

    .sr-page .sr-req-body { padding:16px; }
    .sr-page .sr-routes { display:grid; gap:14px; grid-template-columns:1fr 1fr; }
    .sr-page .sr-route { border:1px solid var(--sr-line); border-radius:10px; padding:14px 15px; }
    .sr-page .sr-route h6 { color:var(--sr-ink); font-size:13.5px; font-weight:650; margin:0 0 3px; }
    .sr-page .sr-route p { color:var(--sr-muted); font-size:12.5px; line-height:1.5; margin:0 0 12px; }
    .sr-page .sr-route .form-control-sm { border:1px solid #d6dbe4; border-radius:7px; height:36px; }

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
    .sr-page .sr-action-primary { background:var(--sr-accent); border:0; color:#fff; }
    .sr-page .sr-action-primary:hover, .sr-page .sr-action-primary:focus { background:#4a3cae; color:#fff; }
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
    }
</style>

<div class="content-page sr-page">
    <div class="content">
        <div class="container-fluid">

            <div class="sr-head">
                <h2>Retention of Points</h2>
                <p>Applicants asking to keep points they already earned. Copy the scores from one of their earlier applications, or encode the retained score yourself.</p>
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
                        <div class="alert alert-warning mb-0">No open vacancy is assigned to your account.</div>
                    <?php else : ?>
                        <form method="get" action="<?= base_url('secretariat/retention'); ?>">
                            <div class="form-row align-items-end">
                                <div class="col-lg-9 col-md-8">
                                    <label for="job_id" class="sr-label">Position</label>
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
                            <div class="sr-stat sr-stat-denied">
                                <div class="sr-stat-label">Denied</div>
                                <div class="sr-stat-value"><?= (int) $counts['denied']; ?></div>
                                <div class="sr-stat-note">Refused with a reason</div>
                            </div>
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
                        <span class="sr-card-count"><?= count($pending); ?> waiting</span>
                    </div>

                    <?php if (empty($pending)) : ?>
                        <div class="sr-nothing">Nothing is waiting for a decision on this position.</div>
                    <?php endif; ?>

                    <?php foreach ($pending as $request) : ?>
                        <?php
                        $name = $applicantName($request);
                        $url = $profileUrl($request);
                        // Only the criteria this request's scope actually carries.
                        $rowMap = $request->score_map ?? $scoreMap;
                        ?>
                        <div class="sr-req">
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
                                    <?php if ((int) $request->dq === 2) : ?>
                                        <span class="sr-chip sr-chip-warn ml-1">Disqualified</span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="sr-req-body">
                                <div class="sr-routes">

                                    <div class="sr-route">
                                        <h6>Copy from an earlier application</h6>
                                        <p>Carries over the scores already on file for this applicant.</p>

                                        <?php if ((int) $request->source_count === 0) : ?>
                                            <div class="sr-empty-route">
                                                No earlier application of this applicant has a score to copy. Encode it by hand instead.
                                            </div>
                                        <?php else : ?>
                                            <?php $sources = $request->sources ?? []; ?>
                                            <form method="post" action="<?= base_url('secretariat/retention/grant-copy'); ?>"
                                                  onsubmit="return confirm('Copy the scores from the chosen application and grant this retention?');">
                                                <input type="hidden" name="id" value="<?= (int) $request->request_id; ?>">
                                                <input type="hidden" name="app_id" value="<?= (int) $request->appID; ?>">
                                                <div class="form-group mb-3">
                                                    <select name="application" class="form-control form-control-sm" required>
                                                        <option value="">Choose an application...</option>
                                                        <?php foreach ($sources as $source) : ?>
                                                            <option value="<?= (int) $source['app_id']; ?>">
                                                                <?= $ret_h($source['title'] ?: 'Application #' . $source['app_id']); ?>
                                                                <?= $source['sy'] !== '' ? ' — FY ' . $ret_h($source['sy']) : ''; ?>
                                                                — <?= $ret_h(number_format((float) $source['total_points'], 2)); ?> pts
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <button type="submit" class="btn sr-action sr-action-primary">Copy scores</button>
                                            </form>
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
                                        <th style="width:26%">Applicant</th>
                                        <th style="width:14%">Scope</th>
                                        <th style="width:12%">Decision</th>
                                        <th style="width:14%">Decided</th>
                                        <th style="width:34%">Detail</th>
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
