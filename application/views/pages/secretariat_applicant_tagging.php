<?php
$tagging_h = static function ($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};

$positionGroups = [
    1 => 'Teaching',
    2 => 'School Administration',
    3 => 'Related Teaching',
    4 => 'Non-Teaching',
];

$vacancies = $vacancies ?? [];
$applicants = $applicants ?? [];
$evaluators = $evaluators ?? [];
$selectedJobId = (int) ($selectedJobId ?? 0);
$selectedVacancy = $selectedVacancy ?? null;

// Waiting = no evaluator yet AND still taggable. Applicants past the tagging
// stage without an evaluator are neither waiting nor tagged, so they are only
// reported as a footnote instead of padding an actionable queue.
$untaggedApplicants = [];
$taggedApplicants = [];
$closedUntaggedTotal = 0;
foreach ($applicants as $applicant) {
    if (!empty($applicant->assignment_id)) {
        $taggedApplicants[] = $applicant;
    } elseif ((int) ($applicant->is_taggable ?? 0) === 1) {
        $untaggedApplicants[] = $applicant;
    } else {
        $closedUntaggedTotal++;
    }
}

// Counted in SQL over every application for the vacancy, so an evaluator's
// number never drops when their applicants get rated or disqualified.
$evaluatorCountRows = $evaluatorTagCounts ?? [];
$evaluatorTagCounts = [];
foreach ($evaluatorCountRows as $row) {
    $evaluatorId = (int) ($row->rater_user_id ?? 0);
    if ($evaluatorId <= 0) {
        continue;
    }

    $evaluatorName = trim((string) ($row->evaluator_name ?? ''));
    $evaluatorTagCounts[$evaluatorId] = [
        'id' => $evaluatorId,
        'name' => $evaluatorName !== '' ? $evaluatorName : 'Evaluator #' . $evaluatorId,
        'count' => (int) $row->tagged_total,
        'pending' => (int) $row->pending_total,
        'evaluated' => (int) $row->evaluated_total,
        'dq' => (int) $row->dq_total,
    ];
}

$vacancyTotal = $selectedVacancy ? (int) $selectedVacancy->applicant_total : 0;
$taggingProgress = $vacancyTotal > 0
    ? (int) round(((int) $selectedVacancy->tagged_total / $vacancyTotal) * 100)
    : 0;

$statusChip = static function ($applicant) {
    if ((int) ($applicant->dq ?? 0) === 2) {
        return ['Disqualified', 'sat-status-dq'];
    }

    switch ((string) $applicant->appStatus) {
        case 'Validated':
            return ['Validated', 'sat-status-validated'];
        case 'Endorsed for Rating':
            return ['Endorsed', 'sat-status-endorsed'];
        case 'Rated':
            return ['Rated', 'sat-status-rated'];
        case 'Confirmed':
            return ['Confirmed', 'sat-status-rated'];
        default:
            return ['Submitted', 'sat-status-submitted'];
    }
};

$evaluatorOptions = [];
foreach ($evaluators as $evaluator) {
    $evaluatorName = trim(implode(' ', array_filter([
        trim((string) ($evaluator->fname ?? '')),
        trim((string) ($evaluator->mname ?? '')),
        trim((string) ($evaluator->lname ?? '')),
    ], static function ($part) { return trim((string) $part) !== ''; })));
    $evaluatorUsername = trim((string) ($evaluator->username ?? ''));

    $evaluatorOptions[] = [
        'id' => (int) $evaluator->id,
        'name' => $evaluatorName,
        'label' => $evaluatorName . ($evaluatorUsername !== '' ? ' — ' . $evaluatorUsername : ''),
        'assigned_total' => (int) $evaluator->assigned_total,
    ];
}

$applicantName = static function ($applicant) {
    $name = trim(implode(' ', array_filter([
        trim((string) ($applicant->FirstName ?? '')),
        trim((string) ($applicant->MiddleName ?? '')),
        trim((string) ($applicant->LastName ?? '')),
        trim((string) ($applicant->NameExtn ?? '')),
    ], static function ($part) { return trim((string) $part) !== ''; })));

    return $name !== '' ? $name : 'Applicant #' . $applicant->appID;
};

$applicantProfileUrl = static function ($applicant) {
    if (empty($applicant->profile_route)) {
        return '';
    }

    return base_url('Pages/' . $applicant->profile_route . '/'
        . rawurlencode((string) $applicant->profile_id) . '/'
        . (int) $applicant->jobID . '/'
        . rawurlencode((string) $applicant->pre_school) . '/'
        . (int) $applicant->appID . '/'
        . rawurlencode((string) $applicant->record_no));
};
?>

<style>
    .sat-page { --sat-ink:#183153; --sat-muted:#6b7a90; --sat-line:#e6ebf2; --sat-blue:#2457d6; --sat-soft:#f5f8fc; padding-bottom:28px; }
    .sat-page .container-fluid { max-width:1540px; }
    .sat-page .sat-hero { align-items:center; background:linear-gradient(125deg,#102f59 0%,#2457d6 68%,#5688ef 100%); border-radius:18px; box-shadow:0 16px 36px rgba(24,49,83,.18); color:#fff; display:flex; justify-content:space-between; overflow:hidden; padding:27px 30px; position:relative; }
    .sat-page .sat-hero:after { border:30px solid rgba(255,255,255,.08); border-radius:50%; content:""; height:205px; position:absolute; right:-45px; top:-75px; width:205px; }
    .sat-page .sat-hero-copy { position:relative; z-index:1; }
    .sat-page .sat-hero-icon { align-items:center; background:rgba(255,255,255,.14); border:1px solid rgba(255,255,255,.2); border-radius:16px; display:flex; flex:0 0 62px; font-size:30px; height:62px; justify-content:center; position:relative; z-index:1; }
    .sat-page .sat-eyebrow { color:#cfe0ff; font-size:12px; font-weight:700; letter-spacing:.1em; text-transform:uppercase; }
    .sat-page .sat-hero h2 { color:#fff; font-size:25px; margin:7px 0 5px; }
    .sat-page .sat-hero p { color:#dce8ff; max-width:720px; margin:0; }
    .sat-page .sat-step { align-items:center; background:#eaf0ff; border-radius:11px; color:var(--sat-blue); display:flex; flex:0 0 38px; font-weight:800; height:38px; justify-content:center; width:38px; }
    .sat-page .sat-card { border:0; border-radius:16px; box-shadow:0 7px 24px rgba(24,49,83,.075); }
    .sat-page .sat-card .card-body { padding:22px 24px; }
    .sat-page .sat-picker-card { border-left:4px solid var(--sat-blue); }
    .sat-page .sat-select { border-color:#ced7e5; border-radius:9px; min-height:46px; }
    .sat-page .sat-selected-icon { align-items:center; background:linear-gradient(145deg,#e8efff,#f2f6ff); border-radius:13px; color:var(--sat-blue); display:flex; flex:0 0 46px; font-size:23px; height:46px; justify-content:center; width:46px; }
    .sat-page .sat-note { display:flex; gap:10px; align-items:flex-start; background:#eef6ff; border:1px solid #d9eaff; border-radius:10px; color:#31577d; padding:12px 14px; }
    .sat-page .sat-metric { align-items:center; background:#f8fafc; border:1px solid var(--sat-line); border-radius:13px; display:flex; gap:12px; height:100%; padding:13px 15px; }
    .sat-page .sat-metric-icon { align-items:center; border-radius:11px; display:flex; flex:0 0 38px; font-size:18px; height:38px; justify-content:center; width:38px; }
    .sat-page .sat-icon-blue { background:#e7efff; color:#2457d6; }
    .sat-page .sat-icon-amber { background:#fff1d1; color:#9d6500; }
    .sat-page .sat-icon-green { background:#def5e8; color:#187247; }
    .sat-page .sat-icon-red { background:#fde8e8; color:#b44040; }
    .sat-page .sat-metric-label { color:var(--sat-muted); font-size:12px; font-weight:700; letter-spacing:.04em; text-transform:uppercase; }
    .sat-page .sat-metric-value { color:var(--sat-ink); font-size:23px; font-weight:800; line-height:1.05; margin-top:3px; }
    .sat-page .sat-progress-track { background:#e7edf5; border-radius:20px; height:7px; overflow:hidden; }
    .sat-page .sat-progress-track span { background:linear-gradient(90deg,#2ca66e,#56ce91); display:block; height:100%; transition:width .25s ease; }
    .sat-page .sat-evaluator-summary { background:#f8fafc; border:1px solid var(--sat-line); border-radius:13px; padding:14px 15px; }
    .sat-page .sat-evaluator-list { display:flex; flex-wrap:wrap; gap:8px; margin-top:10px; }
    .sat-page .sat-evaluator-chip { align-items:center; background:#fff; border:1px solid #dfe6ef; border-radius:22px; box-shadow:0 2px 7px rgba(24,49,83,.04); cursor:pointer; display:inline-flex; gap:7px; max-width:260px; padding:5px 7px 5px 5px; text-align:left; transition:border-color .15s ease, box-shadow .15s ease; }
    .sat-page .sat-evaluator-chip:hover { border-color:#b6c9ec; box-shadow:0 3px 10px rgba(24,49,83,.09); }
    .sat-page .sat-evaluator-chip:focus { outline:2px solid rgba(36,87,214,.35); outline-offset:2px; }
    .sat-page .sat-evaluator-chip.is-active { background:#eef4ff; border-color:var(--sat-blue); box-shadow:0 3px 10px rgba(36,87,214,.16); }
    .sat-page .sat-evaluator-chip-icon { align-items:center; background:#e8f0ff; border-radius:50%; color:var(--sat-blue); display:flex; flex:0 0 28px; font-size:15px; height:28px; justify-content:center; width:28px; }
    .sat-page .sat-evaluator-chip-name { color:#304b6d; font-size:12px; font-weight:700; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
    .sat-page .sat-evaluator-chip-count { align-items:center; background:#2457d6; border-radius:12px; color:#fff; display:flex; flex:0 0 auto; font-size:11px; font-weight:800; height:22px; justify-content:center; min-width:22px; padding:0 6px; }
    .sat-page .sat-filter-bar { align-items:center; background:#eef4ff; border:1px solid #d3e1fb; border-radius:10px; color:#28486f; display:flex; flex-wrap:wrap; gap:10px; justify-content:space-between; margin:13px 24px 0; padding:10px 14px; }
    .sat-page .sat-filter-bar .sat-filter-text { font-size:13px; }
    .sat-page .sat-filter-bar .sat-filter-text strong { color:var(--sat-ink); }
    .sat-page .sat-filter-clear { background:#fff; border:1px solid #c3d6f5; border-radius:7px; color:var(--sat-blue); font-size:12px; font-weight:700; padding:5px 11px; }
    .sat-page .sat-filter-clear:hover { background:#dfeaff; }
    .sat-page .sat-table-card { overflow:visible; }
    .sat-page .sat-table-head { align-items:center; border-bottom:1px solid #edf1f6; display:flex; justify-content:space-between; padding:19px 24px 15px; }
    .sat-page .sat-table-head-icon { align-items:center; border-radius:11px; display:flex; flex:0 0 39px; font-size:19px; height:39px; justify-content:center; width:39px; }
    .sat-page .sat-table-wrap { padding:4px 24px 18px; }
    .sat-page .sat-table { margin:0; width:100% !important; }
    .sat-page .sat-table thead th { background:var(--sat-soft); border-color:#e4eaf2; color:#506176; font-size:11px; letter-spacing:.04em; padding:12px; text-transform:uppercase; }
    .sat-page .sat-table td { border-color:#edf1f6; padding:13px 12px; vertical-align:middle; }
    .sat-page .sat-table tbody tr:hover { background:#f8fbff; }
    .sat-page .sat-name { color:var(--sat-ink); font-weight:700; line-height:1.25; }
    .sat-page .sat-sub { color:var(--sat-muted); font-size:12px; margin-top:3px; }
    .sat-page .sat-status { border-radius:20px; display:inline-flex; font-size:11px; font-weight:700; padding:5px 9px; white-space:nowrap; }
    .sat-page .sat-status-submitted { background:#fff3d8; color:#8a5b00; }
    .sat-page .sat-status-validated { background:#e2f6eb; color:#197447; }
    .sat-page .sat-status-endorsed { background:#e8efff; color:#2457d6; }
    .sat-page .sat-status-rated { background:#efe9ff; color:#6e43c0; }
    .sat-page .sat-status-dq { background:#fdeaea; color:#a52c2c; }
    .sat-page .sat-metric-note { color:var(--sat-muted); font-size:11px; margin-top:3px; }
    .sat-page .sat-locked { color:var(--sat-muted); font-size:12px; }
    .sat-page .sat-assignee { min-width:160px; }
    .sat-page .sat-assignee-name { color:#243b5a; font-size:13px; font-weight:700; }
    .sat-page .sat-unassigned { color:#9a6b13; font-weight:600; }
    .sat-page .sat-tag-form { display:flex; align-items:center; gap:7px; min-width:260px; }
    .sat-page .sat-tag-form select { border-color:#ced7e5; border-radius:8px; height:36px; min-width:165px; }
    .sat-page .sat-tag-form button { border-radius:8px; font-weight:700; min-width:82px; }
    .sat-page .sat-tag-form .select2-container { flex:1 1 auto; min-width:0; }
    .sat-page .sat-tag-form .select2-container .select2-selection--single { border-color:#ced7e5; border-radius:8px; height:36px; }
    .sat-page .sat-tag-form .select2-container .select2-selection--single .select2-selection__rendered { line-height:34px; padding-left:10px; }
    .sat-page .sat-tag-form .select2-container .select2-selection--single .select2-selection__arrow { height:34px; }
    .sat-page .sat-tag-form.sat-form-unsaved { background:#fff8e8; border:1px solid #f2ce7b; border-radius:9px; padding:5px; }
    .sat-page .sat-unsaved-warning { align-items:center; background:#fff6dc; border:1px solid #f0cf77; border-radius:10px; color:#76530a; display:flex; gap:10px; padding:11px 14px; }
    .sat-page .sat-table-title { color:var(--sat-ink); font-size:17px; font-weight:800; }
    .sat-page .sat-count-badge { align-items:center; border-radius:20px; display:inline-flex; font-size:12px; font-weight:800; gap:5px; padding:7px 11px; }
    .sat-page #tagging-message { border:0; border-radius:11px; box-shadow:0 12px 30px rgba(24,49,83,.2); max-width:430px; position:fixed; right:24px; top:82px; z-index:1080; }
    .sat-page .dataTables_wrapper .dataTables_filter input { border:1px solid #d8e0eb; border-radius:8px; margin-left:7px; padding:6px 10px; }
    .sat-page .dataTables_wrapper .dataTables_length select { border:1px solid #d8e0eb; border-radius:7px; padding:4px 22px 4px 8px; }
    .sat-page .dataTables_wrapper .dataTables_info,
    .sat-page .dataTables_wrapper .dataTables_length,
    .sat-page .dataTables_wrapper .dataTables_filter { color:var(--sat-muted); font-size:12px; }
    .sat-page .page-item.active .page-link { background-color:var(--sat-blue); border-color:var(--sat-blue); }
    .sat-page .sat-empty { padding:52px 20px; text-align:center; }
    .sat-page .sat-empty-icon { align-items:center; background:#edf3ff; border-radius:16px; color:var(--sat-blue); display:inline-flex; font-size:26px; height:58px; justify-content:center; width:58px; }
    @media (max-width:767px) { .sat-page .sat-hero { padding:22px 20px; } .sat-page .sat-hero-icon { display:none; } .sat-page .sat-card .card-body { padding:18px; } .sat-page .sat-table-head { padding:17px 18px 13px; } .sat-page .sat-table-wrap { padding:4px 12px 14px; } .sat-page .sat-filter-bar { margin:11px 14px 0; } .sat-page .sat-tag-form { min-width:220px; } .sat-page #tagging-message { left:15px; max-width:none; right:15px; top:72px; } }
</style>

<div class="content-page sat-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-12">
                    <div class="sat-hero">
                        <div class="sat-hero-copy">
                            <div class="sat-eyebrow">Secretariat recruitment workspace</div>
                            <h2>Applicant Evaluator Tagging</h2>
                            <p>Choose a vacancy, assign evaluators, and monitor the distribution from one simple workspace.</p>
                        </div>
                        <div class="sat-hero-icon"><i class="mdi mdi-account-multiple-check"></i></div>
                    </div>
                </div>
            </div>

            <?php if ($this->session->flashdata('success')) : ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $tagging_h($this->session->flashdata('success')); ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
            <?php endif; ?>

            <?php if ($this->session->flashdata('danger')) : ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $tagging_h($this->session->flashdata('danger')); ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
            <?php endif; ?>

            <div id="tagging-message" class="alert d-none" role="status" aria-live="polite"></div>

            <div class="card sat-card sat-picker-card mb-3">
                <div class="card-body">
                    <div class="d-flex align-items-start mb-3">
                        <span class="sat-step">1</span>
                        <div class="ml-3">
                            <h4 class="mb-1">Select a tagged vacancy</h4>
                            <p class="text-muted mb-0">Only vacancies assigned to your Secretariat account are available.</p>
                        </div>
                    </div>

                    <?php if (empty($vacancies)) : ?>
                        <div class="alert alert-warning mb-0">
                            No open vacancy is assigned to your account. Please ask a Super Admin to tag a vacancy to your Secretariat account first.
                        </div>
                    <?php else : ?>
                        <form method="get" action="<?= base_url('secretariat/applicant-tagging'); ?>">
                            <div class="form-row align-items-end">
                                <div class="col-lg-9 col-md-8">
                                    <label for="job_id" class="font-weight-bold">Position / vacancy</label>
                                    <select name="job_id" id="job_id" class="form-control sat-select" required>
                                        <option value="">Choose a position first...</option>
                                        <?php foreach ($vacancies as $vacancy) : ?>
                                            <?php
                                            $group = $positionGroups[(int) $vacancy->position] ?? 'Vacancy';
                                            $vacancyLabel = $vacancy->jobTitle . ' — ' . $group . ' — FY ' . $vacancy->sy;
                                            ?>
                                            <option value="<?= (int) $vacancy->jobID; ?>" <?= (int) $vacancy->jobID === $selectedJobId ? 'selected' : ''; ?>>
                                                <?= $tagging_h($vacancyLabel); ?> (<?= (int) $vacancy->applicant_total; ?> applicants, <?= (int) $vacancy->pending_total; ?> waiting)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-lg-3 col-md-4 mt-2 mt-md-0">
                                    <button type="submit" class="btn btn-primary btn-block sat-select font-weight-bold">
                                        <i class="mdi mdi-account-search-outline mr-1"></i> View applicants
                                    </button>
                                </div>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($selectedVacancy) : ?>
                <div class="card sat-card mb-3">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between flex-wrap mb-3">
                            <div class="d-flex align-items-start mb-2">
                                <span class="sat-selected-icon"><i class="mdi mdi-briefcase-check"></i></span>
                                <div class="ml-3">
                                    <h4 class="mb-1"><?= $tagging_h($selectedVacancy->jobTitle); ?></h4>
                                    <div class="text-muted">
                                        <?= $tagging_h($positionGroups[(int) $selectedVacancy->position] ?? 'Vacancy'); ?>
                                        &middot; FY <?= $tagging_h($selectedVacancy->sy); ?>
                                        <?php if (!empty($selectedVacancy->itemNo)) : ?>&middot; Item <?= $tagging_h($selectedVacancy->itemNo); ?><?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <a href="<?= base_url('secretariat/applicant-tagging'); ?>" class="btn btn-outline-secondary btn-sm">
                                <i class="mdi mdi-swap-horizontal mr-1"></i> Change position
                            </a>
                        </div>

                        <div class="sat-note mb-3">
                            <i class="mdi mdi-information-outline font-20"></i>
                            <div><strong>Tagging only.</strong> Assigning an evaluator here does not mark an applicant qualified or disqualified and does not change the application status.</div>
                        </div>

                        <div id="unsaved-evaluator-warning" class="sat-unsaved-warning d-none mb-3" role="alert" aria-live="assertive">
                            <i class="mdi mdi-alert-outline font-20"></i>
                            <div><strong>Save the selected evaluator first.</strong> Click <span class="unsaved-action-label">Save tag</span> on the highlighted row before selecting an evaluator for another applicant.</div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-xl-3 col-sm-6 mb-2">
                                <div class="sat-metric">
                                    <span class="sat-metric-icon sat-icon-blue"><i class="mdi mdi-account-group-outline"></i></span>
                                    <div>
                                        <div class="sat-metric-label">Total applicants</div>
                                        <div id="applicant-count" class="sat-metric-value"><?= (int) $selectedVacancy->applicant_total; ?></div>
                                        <div class="sat-metric-note">All applications received</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6 mb-2">
                                <div class="sat-metric">
                                    <span class="sat-metric-icon sat-icon-green"><i class="mdi mdi-account-check-outline"></i></span>
                                    <div>
                                        <div class="sat-metric-label">Tagged</div>
                                        <div id="tagged-count" class="sat-metric-value"><?= (int) $selectedVacancy->tagged_total; ?></div>
                                        <div class="sat-metric-note">Given an evaluator, at any stage</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6 mb-2">
                                <div class="sat-metric">
                                    <span class="sat-metric-icon sat-icon-red"><i class="mdi mdi-account-clock-outline"></i></span>
                                    <div>
                                        <div class="sat-metric-label">Waiting</div>
                                        <div id="untagged-count" class="sat-metric-value"><?= (int) $selectedVacancy->pending_total; ?></div>
                                        <div class="sat-metric-note">Still needs an evaluator</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6 mb-2">
                                <div class="sat-metric">
                                    <span class="sat-metric-icon sat-icon-amber"><i class="mdi mdi-progress-check"></i></span>
                                    <div>
                                        <div class="sat-metric-label">Past tagging</div>
                                        <div class="sat-metric-value"><?= (int) $selectedVacancy->evaluated_total + (int) $selectedVacancy->dq_total; ?></div>
                                        <div class="sat-metric-note"><?= (int) $selectedVacancy->evaluated_total; ?> endorsed or rated &middot; <?= (int) $selectedVacancy->dq_total; ?> disqualified</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <p class="sat-sub mb-3">
                            Total applicants counts every application this vacancy received and never goes down.
                            Tagged and Past tagging keep counting applicants after they are endorsed, rated, or disqualified &mdash;
                            only <strong>Waiting</strong> falls as you work through the queue.
                        </p>

                        <div class="sat-evaluator-summary">
                            <div class="d-flex align-items-center justify-content-between flex-wrap">
                                <div>
                                    <div class="font-weight-bold text-dark"><i class="mdi mdi-chart-donut text-primary mr-1"></i> Evaluator distribution</div>
                                    <div class="sat-sub mt-1">Every applicant tagged to each evaluator for this vacancy, including the ones already rated or disqualified. <strong>Click an evaluator</strong> to list only their applicants below and move any of them to someone else.</div>
                                </div>
                                <span class="small font-weight-bold text-success"><span id="tagging-progress-label"><?= $taggingProgress; ?></span>% tagged</span>
                            </div>
                            <div class="sat-progress-track mt-2"><span id="tagging-progress-bar" style="width:<?= $taggingProgress; ?>%"></span></div>
                            <div class="sat-evaluator-list" id="evaluator-count-list">
                                <?php foreach ($evaluatorTagCounts as $evaluatorCount) : ?>
                                    <?php
                                    $chipTitle = sprintf(
                                        'Show the %d applicant%s tagged to %s: %d still to rate, %d endorsed or rated, %d disqualified',
                                        $evaluatorCount['count'],
                                        $evaluatorCount['count'] === 1 ? '' : 's',
                                        $evaluatorCount['name'],
                                        $evaluatorCount['pending'],
                                        $evaluatorCount['evaluated'],
                                        $evaluatorCount['dq']
                                    );
                                    ?>
                                    <button type="button" class="sat-evaluator-chip" data-evaluator-id="<?= (int) $evaluatorCount['id']; ?>" title="<?= $tagging_h($chipTitle); ?>" aria-pressed="false">
                                        <span class="sat-evaluator-chip-icon"><i class="mdi mdi-account-check-outline"></i></span>
                                        <span class="sat-evaluator-chip-name"><?= $tagging_h($evaluatorCount['name']); ?></span>
                                        <span class="sat-evaluator-chip-count"><?= (int) $evaluatorCount['count']; ?></span>
                                    </button>
                                <?php endforeach; ?>
                                <span id="evaluator-count-empty" class="text-muted small <?= empty($evaluatorTagCounts) ? '' : 'd-none'; ?>">No evaluator assignments yet.</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card sat-card sat-table-card mb-3">
                    <div class="sat-table-head">
                        <div class="d-flex align-items-center">
                            <span class="sat-table-head-icon sat-icon-amber"><i class="mdi mdi-account-clock-outline"></i></span>
                            <div class="ml-3">
                                <div>
                                    <div class="sat-table-title">Applicants for tagging</div>
                                    <p class="text-muted mb-0">
                                        Choose an evaluator, then save. The applicant moves below immediately.
                                        <?php if ($closedUntaggedTotal > 0) : ?>
                                            <br><small><?= (int) $closedUntaggedTotal; ?> applicant<?= $closedUntaggedTotal === 1 ? '' : 's'; ?> left the tagging stage without an evaluator and cannot be tagged here.</small>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <span class="sat-count-badge badge-warning"><i class="mdi mdi-account-clock-outline"></i><span id="untagged-table-count"><?= count($untaggedApplicants); ?></span> waiting</span>
                    </div>

                    <div class="sat-table-wrap">
                            <table class="table table-bordered table-hover dt-responsive sat-table" id="untagged-datatable">
                                <thead>
                                    <tr>
                                        <th style="width:30%">Applicant</th>
                                        <th style="width:12%">Status</th>
                                        <th style="width:28%">School / district</th>
                                        <th style="width:30%">Tag to evaluator <span class="font-weight-normal text-muted">(number = applicants tagged to them this FY, all vacancies)</span></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($untaggedApplicants as $applicant) : ?>
                                        <?php
                                        $fullName = $applicantName($applicant);
                                        $profileUrl = $applicantProfileUrl($applicant);
                                        [$statusLabel, $statusClass] = $statusChip($applicant);
                                        ?>
                                        <tr>
                                            <td>
                                                <div class="sat-name"><?= $tagging_h($fullName); ?></div>
                                                <div class="sat-sub">
                                                    No. <?= $tagging_h($applicant->record_no); ?>
                                                    <?php if ($profileUrl !== '') : ?>&middot; <a href="<?= $tagging_h($profileUrl); ?>" target="_blank" rel="noopener">View application</a><?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="sat-status <?= $statusClass; ?>"><?= $tagging_h($statusLabel); ?></span>
                                            </td>
                                            <td>
                                                <div><?= $tagging_h($applicant->schoolName ?: 'School not specified'); ?></div>
                                                <div class="sat-sub"><?= $tagging_h($applicant->district ?: 'District not specified'); ?></div>
                                            </td>
                                            <td>
                                                <form class="sat-tag-form" data-mode="tag" method="post" action="<?= base_url('secretariat/applicant-tagging/tag'); ?>">
                                                    <input type="hidden" name="app_id" value="<?= (int) $applicant->appID; ?>">
                                                    <input type="hidden" name="job_id" value="<?= (int) $selectedVacancy->jobID; ?>">
                                                    <select name="rater_id" class="form-control form-control-sm sat-evaluator-select" data-placeholder="Select evaluator..." data-saved-value="" required aria-label="Evaluator for <?= $tagging_h($fullName); ?>" <?= empty($evaluatorOptions) ? 'disabled' : ''; ?>>
                                                        <option value="">Select evaluator...</option>
                                                        <?php foreach ($evaluatorOptions as $evaluator) : ?>
                                                            <option value="<?= $evaluator['id']; ?>"><?= $tagging_h($evaluator['label']); ?> (<?= $evaluator['assigned_total']; ?>)</option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <button type="submit" class="btn btn-sm btn-primary" <?= empty($evaluatorOptions) ? 'disabled' : ''; ?>>Save tag</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                    </div>
                </div>

                <div class="card sat-card sat-table-card" id="tagged-applicants">
                    <div class="sat-table-head">
                        <div class="d-flex align-items-center">
                            <span class="sat-table-head-icon sat-icon-green"><i class="mdi mdi-account-check-outline"></i></span>
                            <div class="ml-3">
                                <div class="sat-table-title">Tagged applicants</div>
                                <p class="text-muted mb-0">Every applicant already given an evaluator, including those endorsed, rated, or disqualified. Reassignment stays open only while an applicant is still in the tagging stage.</p>
                            </div>
                        </div>
                        <span class="sat-count-badge badge-success"><i class="mdi mdi-account-check-outline"></i><span id="tagged-table-count"><?= count($taggedApplicants); ?></span> tagged</span>
                    </div>

                    <div id="evaluator-filter-bar" class="sat-filter-bar d-none" role="status" aria-live="polite">
                        <div class="sat-filter-text" id="evaluator-filter-text"></div>
                        <button type="button" class="sat-filter-clear" id="evaluator-filter-clear">Show all tagged applicants</button>
                    </div>

                    <div class="sat-table-wrap">
                            <table class="table table-bordered table-hover dt-responsive sat-table" id="tagged-datatable">
                                <thead>
                                    <tr>
                                        <th style="width:25%">Applicant</th>
                                        <th style="width:11%">Status</th>
                                        <th style="width:22%">School / district</th>
                                        <th style="width:17%">Evaluator</th>
                                        <th style="width:25%">Reassign evaluator <span class="font-weight-normal text-muted">(number = applicants tagged to them this FY, all vacancies)</span></th>
                                        <th>Evaluator key</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($taggedApplicants as $applicant) : ?>
                                        <?php
                                        $fullName = $applicantName($applicant);
                                        $profileUrl = $applicantProfileUrl($applicant);
                                        [$statusLabel, $statusClass] = $statusChip($applicant);
                                        $canReassign = (int) ($applicant->is_taggable ?? 0) === 1;
                                        ?>
                                        <tr data-rater-id="<?= (int) $applicant->rater_user_id; ?>">
                                            <td>
                                                <div class="sat-name"><?= $tagging_h($fullName); ?></div>
                                                <div class="sat-sub">
                                                    No. <?= $tagging_h($applicant->record_no); ?>
                                                    <?php if ($profileUrl !== '') : ?>&middot; <a href="<?= $tagging_h($profileUrl); ?>" target="_blank" rel="noopener">View application</a><?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="sat-status <?= $statusClass; ?>"><?= $tagging_h($statusLabel); ?></span>
                                            </td>
                                            <td>
                                                <div><?= $tagging_h($applicant->schoolName ?: 'School not specified'); ?></div>
                                                <div class="sat-sub"><?= $tagging_h($applicant->district ?: 'District not specified'); ?></div>
                                            </td>
                                            <td class="sat-assignee">
                                                <div class="sat-assignee-name"><?= $tagging_h($applicant->evaluator_name); ?></div>
                                                <div class="sat-sub assignment-date"><?= !empty($applicant->assigned_at) ? 'Tagged ' . $tagging_h(date('M d, Y', strtotime($applicant->assigned_at))) : ''; ?></div>
                                            </td>
                                            <td>
                                                <?php if (!$canReassign) : ?>
                                                    <span class="sat-locked">
                                                        <i class="mdi mdi-lock-outline mr-1"></i>
                                                        <?= (int) ($applicant->dq ?? 0) === 2
                                                            ? 'Disqualified — evaluator kept for the record.'
                                                            : 'Already past tagging — evaluator can no longer be changed.'; ?>
                                                    </span>
                                                <?php else : ?>
                                                    <form class="sat-tag-form" data-mode="reassign" method="post" action="<?= base_url('secretariat/applicant-tagging/tag'); ?>">
                                                        <input type="hidden" name="app_id" value="<?= (int) $applicant->appID; ?>">
                                                        <input type="hidden" name="job_id" value="<?= (int) $selectedVacancy->jobID; ?>">
                                                        <select name="rater_id" class="form-control form-control-sm sat-evaluator-select" data-placeholder="Select evaluator..." data-saved-value="<?= (int) $applicant->rater_user_id; ?>" required aria-label="Reassign evaluator for <?= $tagging_h($fullName); ?>" <?= empty($evaluatorOptions) ? 'disabled' : ''; ?>>
                                                            <option value="">Select evaluator...</option>
                                                            <?php foreach ($evaluatorOptions as $evaluator) : ?>
                                                                <option value="<?= $evaluator['id']; ?>" <?= (int) $applicant->rater_user_id === $evaluator['id'] ? 'selected' : ''; ?>><?= $tagging_h($evaluator['label']); ?> (<?= $evaluator['assigned_total']; ?>)</option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                        <button type="submit" class="btn btn-sm btn-outline-primary" <?= empty($evaluatorOptions) ? 'disabled' : ''; ?>>Save change</button>
                                                    </form>
                                                <?php endif; ?>
                                            </td>
                                            <?php // Hidden column: the evaluator chips filter this table on it. ?>
                                            <td><?= (int) $applicant->rater_user_id; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                    <?php if (empty($evaluatorOptions)) : ?>
                        <div class="px-4 pb-3"><div class="alert alert-warning mb-0">No user account with the Evaluator position is currently available.</div></div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
(function () {
    var message = document.getElementById('tagging-message');
    var unsavedWarning = document.getElementById('unsaved-evaluator-warning');
    var untaggedDataTable = null;
    var taggedDataTable = null;
    var activeDirtyForm = null;
    // Evaluator chip currently filtering the tagged table, as a string id.
    var activeEvaluatorFilter = '';
    var activeEvaluatorName = '';
    var RATER_KEY_COLUMN = 5;

    function showMessage(ok, text) {
        message.className = 'alert ' + (ok ? 'alert-success' : 'alert-danger');
        message.textContent = text;
        message.classList.remove('d-none');
        window.clearTimeout(showMessage.timer);
        showMessage.timer = window.setTimeout(function () { message.classList.add('d-none'); }, 5000);
    }

    function showUnsavedWarning(form, bringIntoView) {
        if (!unsavedWarning || !form) return;

        var actionLabel = form.getAttribute('data-mode') === 'tag' ? 'Save tag' : 'Save change';
        var label = unsavedWarning.querySelector('.unsaved-action-label');
        if (label) label.textContent = actionLabel;
        unsavedWarning.classList.remove('d-none');

        if (bringIntoView) {
            var button = form.querySelector('button[type="submit"]');
            if (button && form.getClientRects().length) {
                form.scrollIntoView({ behavior: 'smooth', block: 'center' });
                window.setTimeout(function () { button.focus(); }, 250);
            }
        }
    }

    function clearDirtyForm(form, commitSelection) {
        if (!form) return;

        var select = form.querySelector('select.sat-evaluator-select');
        if (commitSelection && select) {
            select.setAttribute('data-saved-value', select.value);
        }
        form.classList.remove('sat-form-unsaved');

        if (activeDirtyForm === form) {
            activeDirtyForm = null;
            if (unsavedWarning) unsavedWarning.classList.add('d-none');
        }
    }

    function updateTaggingProgress() {
        var tagged = parseInt((document.getElementById('tagged-count') || {}).textContent || '0', 10);
        var total = parseInt((document.getElementById('applicant-count') || {}).textContent || '0', 10);
        var percent = total > 0 ? Math.round((tagged / total) * 100) : 0;
        var label = document.getElementById('tagging-progress-label');
        var bar = document.getElementById('tagging-progress-bar');

        if (label) label.textContent = percent;
        if (bar) bar.style.width = percent + '%';
    }

    /* ------------------------------------------------------------------
     * Filtering the tagged table by evaluator.
     *
     * Clicking a distribution chip narrows the tagged table to that
     * evaluator's applicants, so a Secretariat can see who is carrying what
     * and move any of them to someone else without hunting through the
     * whole list. The match is on the hidden last column rather than on the
     * evaluator name, so two evaluators sharing a name stay separate and
     * rows added after a tag are filtered on the same value.
     * ------------------------------------------------------------------ */

    function raterKeyOf(rowData) {
        if (!rowData) return '';
        var value = rowData[RATER_KEY_COLUMN];
        return value === undefined || value === null ? '' : String(value).trim();
    }

    function updateEvaluatorFilterBar() {
        var bar = document.getElementById('evaluator-filter-bar');
        var text = document.getElementById('evaluator-filter-text');
        if (!bar || !text) return;

        if (!activeEvaluatorFilter || !taggedDataTable) {
            bar.classList.add('d-none');
            return;
        }

        var count = taggedDataTable.rows({ search: 'applied' }).count();
        var name = document.createElement('strong');
        name.textContent = activeEvaluatorName;

        while (text.firstChild) { text.removeChild(text.firstChild); }

        if (count === 0) {
            text.appendChild(document.createTextNode('No applicant is tagged to '));
            text.appendChild(name);
            text.appendChild(document.createTextNode(' anymore.'));
        } else {
            text.appendChild(document.createTextNode(
                'Showing the ' + count + ' applicant' + (count === 1 ? '' : 's') + ' tagged to '
            ));
            text.appendChild(name);
            text.appendChild(document.createTextNode(
                '. Use the last column to move ' + (count === 1 ? 'this applicant' : 'any of them') + ' to another evaluator.'
            ));
        }

        bar.classList.remove('d-none');
    }

    function syncEvaluatorChips() {
        var chips = document.querySelectorAll('.sat-evaluator-chip');
        Array.prototype.forEach.call(chips, function (chip) {
            var isActive = String(chip.getAttribute('data-evaluator-id') || '') === activeEvaluatorFilter
                && activeEvaluatorFilter !== '';
            chip.classList.toggle('is-active', isActive);
            chip.setAttribute('aria-pressed', isActive ? 'true' : 'false');
        });
    }

    function clearEvaluatorFilter() {
        if (!activeEvaluatorFilter) return;
        activeEvaluatorFilter = '';
        activeEvaluatorName = '';
        syncEvaluatorChips();
        // A full draw so paging restarts at page one on the wider list.
        if (taggedDataTable) taggedDataTable.draw();
        updateEvaluatorFilterBar();
    }

    function filterByEvaluator(chip) {
        var evaluatorId = String(chip.getAttribute('data-evaluator-id') || '');
        if (!evaluatorId) return;

        if (activeEvaluatorFilter === evaluatorId) {
            clearEvaluatorFilter();
            return;
        }

        var nameNode = chip.querySelector('.sat-evaluator-chip-name');
        activeEvaluatorFilter = evaluatorId;
        activeEvaluatorName = nameNode ? nameNode.textContent : ('Evaluator #' + evaluatorId);
        syncEvaluatorChips();

        if (taggedDataTable) {
            taggedDataTable.draw();
            updateEvaluatorFilterBar();
        }

        var card = document.getElementById('tagged-applicants');
        if (card) card.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    document.addEventListener('click', function (event) {
        if (!event.target.closest) return;

        var chip = event.target.closest('.sat-evaluator-chip');
        if (chip) {
            filterByEvaluator(chip);
            return;
        }

        if (event.target.closest('#evaluator-filter-clear')) {
            clearEvaluatorFilter();
        }
    });

    function adjustEvaluatorCount(evaluatorId, evaluatorName, adjustment) {
        evaluatorId = String(evaluatorId || '');
        if (!evaluatorId || !adjustment) return;

        var list = document.getElementById('evaluator-count-list');
        var empty = document.getElementById('evaluator-count-empty');
        if (!list) return;

        var chip = list.querySelector('[data-evaluator-id="' + evaluatorId.replace(/"/g, '') + '"]');
        if (!chip && adjustment > 0) {
            chip = document.createElement('button');
            chip.type = 'button';
            chip.className = 'sat-evaluator-chip';
            chip.setAttribute('data-evaluator-id', evaluatorId);
            chip.setAttribute('aria-pressed', 'false');

            var icon = document.createElement('span');
            icon.className = 'sat-evaluator-chip-icon';
            icon.innerHTML = '<i class="mdi mdi-account-check-outline"></i>';

            var name = document.createElement('span');
            name.className = 'sat-evaluator-chip-name';
            name.textContent = evaluatorName || ('Evaluator #' + evaluatorId);

            var count = document.createElement('span');
            count.className = 'sat-evaluator-chip-count';
            count.textContent = '0';

            chip.appendChild(icon);
            chip.appendChild(name);
            chip.appendChild(count);
            list.insertBefore(chip, empty || null);
        }

        if (!chip) return;
        var countElement = chip.querySelector('.sat-evaluator-chip-count');
        var nextCount = Math.max(0, parseInt(countElement.textContent || '0', 10) + adjustment);

        if (nextCount === 0) {
            chip.parentNode.removeChild(chip);
            // Nothing left to show for an evaluator whose chip just went away.
            if (activeEvaluatorFilter === evaluatorId) clearEvaluatorFilter();
        } else {
            countElement.textContent = nextCount;
            // The server-rendered tooltip carries a stage breakdown we cannot
            // recompute here, so fall back to the plain total once it moves.
            var chipName = chip.querySelector('.sat-evaluator-chip-name');
            chip.title = 'Show the ' + nextCount + ' applicant' + (nextCount === 1 ? '' : 's') + ' tagged to '
                + (chipName ? chipName.textContent : 'Evaluator #' + evaluatorId)
                + ' (reload for the stage breakdown)';
        }

        var remainingChips = list.querySelectorAll('.sat-evaluator-chip').length;
        if (empty) empty.classList.toggle('d-none', remainingChips > 0);
    }

    function evaluatorChanged(select) {
        var form = select.closest('form.sat-tag-form');
        if (!form) return;

        if (activeDirtyForm && activeDirtyForm !== form) {
            select.value = select.getAttribute('data-saved-value') || '';
            if (window.jQuery && jQuery.fn && jQuery.fn.select2) {
                jQuery(select).trigger('change.select2');
            }
            showUnsavedWarning(activeDirtyForm, true);
            return;
        }

        if (select.value !== (select.getAttribute('data-saved-value') || '')) {
            activeDirtyForm = form;
            form.classList.add('sat-form-unsaved');
            showUnsavedWarning(form, false);
        } else {
            clearDirtyForm(form, false);
        }
    }

    function initEvaluatorSelects() {
        if (!window.jQuery || !jQuery.fn || !jQuery.fn.select2) return;

        jQuery('select.sat-evaluator-select:visible').each(function () {
            var select = jQuery(this);
            if (select.data('select2')) return;

            select.select2({
                width: '100%',
                placeholder: select.data('placeholder') || 'Select evaluator...',
                dropdownParent: jQuery(document.body)
            });
        });
    }

    function initDataTables() {
        if (!window.jQuery || !jQuery.fn || !jQuery.fn.DataTable) return;

        var untaggedTable = jQuery('#untagged-datatable');
        var taggedTable = jQuery('#tagged-datatable');

        untaggedDataTable = untaggedTable.DataTable({
            pageLength: 10,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'All']],
            order: [[0, 'asc']],
            responsive: true,
            autoWidth: false,
            columnDefs: [
                { targets: 0, responsivePriority: 2 },
                { targets: 3, responsivePriority: 1, orderable: false }
            ],
            language: { emptyTable: 'No applicants are waiting to be tagged.' }
        });

        // Only rows whose hidden evaluator key matches the clicked chip stay
        // visible; with no chip active the table is untouched.
        jQuery.fn.dataTable.ext.search.push(function (settings, searchData, rowIndex, rowData) {
            if (settings.nTable.id !== 'tagged-datatable' || activeEvaluatorFilter === '') {
                return true;
            }

            return raterKeyOf(rowData) === activeEvaluatorFilter;
        });

        taggedDataTable = taggedTable.DataTable({
            pageLength: 10,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'All']],
            order: [[0, 'asc']],
            responsive: true,
            autoWidth: false,
            columnDefs: [
                { targets: 0, responsivePriority: 2 },
                { targets: 3, responsivePriority: 3 },
                { targets: 4, responsivePriority: 1, orderable: false },
                { targets: RATER_KEY_COLUMN, visible: false, searchable: false, orderable: false }
            ],
            language: {
                emptyTable: 'No applicants have been tagged yet.',
                zeroRecords: 'No tagged applicant matches the current filter.'
            }
        });

        untaggedTable.on('draw.dt', initEvaluatorSelects);
        taggedTable.on('draw.dt', initEvaluatorSelects);

        jQuery(document)
            .off('select2:opening.satUnsavedEvaluator')
            .on('select2:opening.satUnsavedEvaluator', 'select.sat-evaluator-select', function (event) {
                var form = this.closest('form.sat-tag-form');
                if (activeDirtyForm && activeDirtyForm !== form) {
                    event.preventDefault();
                    showUnsavedWarning(activeDirtyForm, true);
                }
            })
            .off('change.satUnsavedEvaluator', 'select.sat-evaluator-select')
            .on('change.satUnsavedEvaluator', 'select.sat-evaluator-select', function () {
                evaluatorChanged(this);
            });

        initEvaluatorSelects();
    }

    if (document.readyState === 'complete') {
        window.setTimeout(initDataTables, 0);
    } else {
        window.addEventListener('load', initDataTables);
    }

    document.addEventListener('change', function (event) {
        if (event.target.matches && event.target.matches('select.sat-evaluator-select')) {
            evaluatorChanged(event.target);
        }
    });

    window.addEventListener('beforeunload', function (event) {
        if (!activeDirtyForm) return;
        event.preventDefault();
        event.returnValue = '';
    });

    document.addEventListener('submit', function (event) {
        var form = event.target;
        if (!form.classList.contains('sat-tag-form') || !window.fetch) return;

        event.preventDefault();
        var button = form.querySelector('button[type="submit"]');
        var row = form.closest('tr');
        var mode = form.getAttribute('data-mode');
        var selectedEvaluator = form.querySelector('select.sat-evaluator-select');
        var previousEvaluatorId = selectedEvaluator ? (selectedEvaluator.getAttribute('data-saved-value') || '') : '';
        var selectedEvaluatorId = selectedEvaluator ? selectedEvaluator.value : '';
        var originalText = button.textContent;
        button.disabled = true;
        button.textContent = 'Saving...';

        fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            credentials: 'same-origin'
        })
        .then(function (response) {
            return response.json().then(function (body) {
                if (!response.ok || !body.ok) throw new Error(body.message || 'Unable to save evaluator tag.');
                return body;
            });
        })
        .then(function (body) {
            if (mode === 'tag') {
                clearDirtyForm(form, true);
                var taggedCount = document.getElementById('tagged-count');
                var untaggedCount = document.getElementById('untagged-count');
                var taggedTableCount = document.getElementById('tagged-table-count');
                var untaggedTableCount = document.getElementById('untagged-table-count');
                taggedCount.textContent = parseInt(taggedCount.textContent || '0', 10) + 1;
                untaggedCount.textContent = Math.max(0, parseInt(untaggedCount.textContent || '0', 10) - 1);
                taggedTableCount.textContent = parseInt(taggedTableCount.textContent || '0', 10) + 1;
                untaggedTableCount.textContent = Math.max(0, parseInt(untaggedTableCount.textContent || '0', 10) - 1);
                adjustEvaluatorCount(selectedEvaluatorId, body.evaluator_name, 1);
                updateTaggingProgress();

                if (untaggedDataTable && taggedDataTable && row && row.cells.length >= 4) {
                    var applicantCell = row.cells[0].innerHTML;
                    var statusCell = row.cells[1].innerHTML;
                    var schoolCell = row.cells[2].innerHTML;
                    var evaluatorSelect = form.querySelector('select.sat-evaluator-select');

                    if (window.jQuery && evaluatorSelect && jQuery(evaluatorSelect).data('select2')) {
                        jQuery(evaluatorSelect).select2('destroy');
                    }

                    form.setAttribute('data-mode', 'reassign');
                    evaluatorSelect.setAttribute('data-saved-value', evaluatorSelect.value);
                    evaluatorSelect.setAttribute('aria-label', 'Reassign evaluator');
                    Array.prototype.forEach.call(evaluatorSelect.options, function (option) {
                        if (option.value === evaluatorSelect.value) {
                            option.setAttribute('selected', 'selected');
                        } else {
                            option.removeAttribute('selected');
                        }
                    });
                    button.disabled = false;
                    button.textContent = 'Save change';
                    button.classList.remove('btn-primary');
                    button.classList.add('btn-outline-primary');

                    var evaluatorCell = '<div class="sat-assignee-name"></div><div class="sat-sub assignment-date">Tagged just now</div>';
                    var evaluatorWrapper = document.createElement('div');
                    evaluatorWrapper.innerHTML = evaluatorCell;
                    evaluatorWrapper.querySelector('.sat-assignee-name').textContent = body.evaluator_name;

                    var reassignForm = form.outerHTML;
                    untaggedDataTable.row(row).remove().draw(false);
                    var addedRow = taggedDataTable.row.add([
                        applicantCell,
                        statusCell,
                        schoolCell,
                        evaluatorWrapper.innerHTML,
                        reassignForm,
                        String(selectedEvaluatorId)
                    ]);
                    addedRow.draw(false);
                    if (addedRow.node()) {
                        addedRow.node().setAttribute('data-rater-id', selectedEvaluatorId);
                    }
                    initEvaluatorSelects();
                    updateEvaluatorFilterBar();
                } else {
                    window.location.reload();
                }

                showMessage(true, body.message + ' You can tag the next applicant now.');
                return;
            }

            row.querySelector('.sat-assignee-name').textContent = body.evaluator_name;
            row.querySelector('.assignment-date').textContent = 'Tagged just now';
            if (previousEvaluatorId !== selectedEvaluatorId) {
                adjustEvaluatorCount(previousEvaluatorId, '', -1);
                adjustEvaluatorCount(selectedEvaluatorId, body.evaluator_name, 1);

                // The row now belongs to the new evaluator, so the hidden key
                // moves with it - a row transferred out of the evaluator being
                // viewed drops out of the filtered list on the next draw.
                row.setAttribute('data-rater-id', selectedEvaluatorId);
                if (taggedDataTable) {
                    taggedDataTable.cell(row, RATER_KEY_COLUMN).data(String(selectedEvaluatorId));
                    if (activeEvaluatorFilter !== '') {
                        taggedDataTable.draw(false);
                        updateEvaluatorFilterBar();
                    }
                }
            }
            clearDirtyForm(form, true);
            button.textContent = 'Save change';
            showMessage(true, body.message);
        })
        .catch(function (error) {
            button.textContent = originalText;
            showMessage(false, error.message);
        })
        .then(function () {
            button.disabled = false;
        });
    });
})();
</script>
