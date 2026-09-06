<?php
$sq_h = static function ($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};

$positionGroups = [
    1 => 'Teaching',
    2 => 'School Administration',
    3 => 'Related Teaching',
    4 => 'Non-Teaching',
    5 => 'Promotion',
];

$mode = ($mode ?? 'qualified') === 'disqualified' ? 'disqualified' : 'qualified';
$isQualified = ($mode === 'qualified');
$vacancies = $vacancies ?? [];
$applicants = $applicants ?? [];
$selectedJobId = (int) ($selectedJobId ?? 0);
$selectedVacancy = $selectedVacancy ?? null;
$issuedDocs = $issuedDocs ?? [];
$otherMode = $isQualified ? 'disqualified' : 'qualified';
$otherLabel = $isQualified ? 'Disqualified list' : 'Qualified list';

$ratedTotal = 0;
foreach ($applicants as $applicant) {
    if (!empty($applicant->is_rated)) {
        $ratedTotal++;
    }
}
$awaitingRating = count($applicants) - $ratedTotal;

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

$statusChip = static function ($applicant) {
    if ((int) ($applicant->dq ?? 0) === 2) {
        return ['Disqualified', 'sq-status-dq'];
    }

    switch ((string) $applicant->appStatus) {
        case 'Validated':
            return ['Validated', 'sq-status-validated'];
        case 'Endorsed for Rating':
            return ['Endorsed', 'sq-status-endorsed'];
        case 'Rated':
            return ['Rated', 'sq-status-rated'];
        case 'Confirmed':
            return ['Confirmed', 'sq-status-rated'];
        default:
            return ['Submitted', 'sq-status-submitted'];
    }
};

$scoreText = static function ($value) {
    if ($value === null || $value === '') {
        return '—';
    }

    // 0.00001 is the "not rated yet" stub written when a rating row is created.
    if (abs((float) $value - 0.00001) <= 0.000001 || abs((float) $value - 0.0001) <= 0.000001) {
        return '—';
    }

    return rtrim(rtrim(number_format((float) $value, 3, '.', ''), '0'), '.');
};
?>

<style>
    .sq-page { --sq-ink:#183153; --sq-muted:#6b7a90; --sq-line:#e6ebf2; --sq-blue:#2457d6; --sq-soft:#f5f8fc; padding-bottom:28px; }
    .sq-page .container-fluid { max-width:1540px; }
    .sq-page .sq-hero { align-items:center; border-radius:18px; box-shadow:0 16px 36px rgba(24,49,83,.18); color:#fff; display:flex; justify-content:space-between; overflow:hidden; padding:27px 30px; position:relative; }
    .sq-page .sq-hero-qualified { background:linear-gradient(125deg,#0f5137 0%,#1c8a5b 68%,#41b985 100%); }
    .sq-page .sq-hero-disqualified { background:linear-gradient(125deg,#5c1d1d 0%,#a83232 68%,#d76161 100%); }
    .sq-page .sq-hero:after { border:30px solid rgba(255,255,255,.08); border-radius:50%; content:""; height:205px; position:absolute; right:-45px; top:-75px; width:205px; }
    .sq-page .sq-hero-copy { position:relative; z-index:1; }
    .sq-page .sq-hero-icon { align-items:center; background:rgba(255,255,255,.14); border:1px solid rgba(255,255,255,.2); border-radius:16px; display:flex; flex:0 0 62px; font-size:30px; height:62px; justify-content:center; position:relative; z-index:1; }
    .sq-page .sq-eyebrow { color:rgba(255,255,255,.82); font-size:12px; font-weight:700; letter-spacing:.1em; text-transform:uppercase; }
    .sq-page .sq-hero h2 { color:#fff; font-size:25px; margin:7px 0 5px; }
    .sq-page .sq-hero p { color:rgba(255,255,255,.88); max-width:720px; margin:0; }
    .sq-page .sq-card { border:0; border-radius:16px; box-shadow:0 7px 24px rgba(24,49,83,.075); }
    .sq-page .sq-card .card-body { padding:22px 24px; }
    .sq-page .sq-picker-card { border-left:4px solid var(--sq-blue); }
    .sq-page .sq-select { border-color:#ced7e5; border-radius:9px; min-height:46px; }
    .sq-page .sq-vacancy-select2 + .select2-container .select2-selection--single { border-color:#ced7e5; border-radius:9px; height:46px; }
    .sq-page .sq-vacancy-select2 + .select2-container .select2-selection--single .select2-selection__rendered { line-height:44px; padding-left:14px; color:#183153; font-weight:600; }
    .sq-page .sq-vacancy-select2 + .select2-container .select2-selection--single .select2-selection__arrow { height:44px; }
    .sq-page .sq-vacancy-select2 + .select2-container .select2-selection--single .select2-selection__placeholder { color:#6b7a90; font-weight:500; }
    .sq-page .sq-selected-icon { align-items:center; background:linear-gradient(145deg,#e8efff,#f2f6ff); border-radius:13px; color:var(--sq-blue); display:flex; flex:0 0 46px; font-size:23px; height:46px; justify-content:center; width:46px; }
    .sq-page .sq-metric { align-items:center; background:#f8fafc; border:1px solid var(--sq-line); border-radius:13px; display:flex; gap:12px; height:100%; padding:13px 15px; }
    .sq-page .sq-metric-icon { align-items:center; border-radius:11px; display:flex; flex:0 0 38px; font-size:18px; height:38px; justify-content:center; width:38px; }
    .sq-page .sq-icon-blue { background:#e7efff; color:#2457d6; }
    .sq-page .sq-icon-green { background:#def5e8; color:#187247; }
    .sq-page .sq-icon-amber { background:#fff1d1; color:#9d6500; }
    .sq-page .sq-icon-red { background:#fde8e8; color:#b44040; }
    .sq-page .sq-metric-label { color:var(--sq-muted); font-size:12px; font-weight:700; letter-spacing:.04em; text-transform:uppercase; }
    .sq-page .sq-metric-value { color:var(--sq-ink); font-size:23px; font-weight:800; line-height:1.05; margin-top:3px; }
    .sq-page .sq-metric-note { color:var(--sq-muted); font-size:11px; margin-top:3px; }
    .sq-page .sq-table-head { align-items:center; border-bottom:1px solid #edf1f6; display:flex; flex-wrap:wrap; gap:10px; justify-content:space-between; padding:19px 24px 15px; }
    .sq-page .sq-table-title { color:var(--sq-ink); font-size:17px; font-weight:800; }
    .sq-page .sq-search-box .input-group { width:260px; }
    .sq-page .sq-search-box .input-group-text { border-color:#d8e0eb; border-radius:8px 0 0 8px; color:#6b7a90; }
    .sq-page .sq-search-box .form-control { border-color:#d8e0eb; border-radius:0 8px 8px 0; font-size:13px; height:36px; }
    .sq-page .sq-search-box .form-control:focus { box-shadow:none; border-color:#2457d6; }
    @media (max-width:767px) {
        .sq-page .sq-search-box .input-group { width:100%; }
    }
    .sq-page .sq-table-wrap { padding:4px 24px 18px; }
    .sq-page .sq-table { margin:0; width:100% !important; }
    .sq-page .sq-table thead th { background:var(--sq-soft); border-color:#e4eaf2; color:#506176; font-size:11px; letter-spacing:.04em; padding:12px; text-transform:uppercase; }
    .sq-page .sq-table td { border-color:#edf1f6; padding:13px 12px; vertical-align:middle; }
    .sq-page .sq-table tbody tr:hover { background:#f8fbff; }
    .sq-page .sq-name { color:var(--sq-ink); font-weight:700; line-height:1.25; }
    .sq-page .sq-sub { color:var(--sq-muted); font-size:12px; margin-top:3px; }
    .sq-page .sq-status { border-radius:20px; display:inline-flex; font-size:11px; font-weight:700; padding:5px 9px; white-space:nowrap; }
    .sq-page .sq-status-submitted { background:#fff3d8; color:#8a5b00; }
    .sq-page .sq-status-validated { background:#e2f6eb; color:#197447; }
    .sq-page .sq-status-endorsed { background:#e8efff; color:#2457d6; }
    .sq-page .sq-status-rated { background:#efe9ff; color:#6e43c0; }
    .sq-page .sq-status-dq { background:#fdeaea; color:#a52c2c; }
    .sq-page .sq-rated { background:#e2f6eb; border-radius:20px; color:#197447; display:inline-flex; font-size:11px; font-weight:700; padding:5px 9px; }
    .sq-page .sq-unrated { background:#fff3d8; border-radius:20px; color:#8a5b00; display:inline-flex; font-size:11px; font-weight:700; padding:5px 9px; }
    .sq-page .sq-points { color:var(--sq-ink); font-size:15px; font-weight:800; }
    .sq-page .sq-breakdown { color:var(--sq-muted); font-size:11px; margin-top:3px; }
    .sq-page .sq-reason { color:#7a3b3b; font-size:12px; max-width:340px; white-space:normal; }
    .sq-page .sq-chip-filter { background:#fff; border:1px solid #dfe6ef; border-radius:22px; color:#304b6d; font-size:12px; font-weight:700; padding:6px 13px; transition:border-color .15s ease, box-shadow .15s ease; }
    .sq-page .sq-chip-filter:hover { border-color:#b6c9ec; }
    .sq-page .sq-chip-filter.is-active { background:#eef4ff; border-color:var(--sq-blue); color:var(--sq-blue); }
    .sq-page .sq-actions { align-items:center; display:flex; flex-wrap:wrap; gap:5px; }
    .sq-page .sq-doc-btn { align-items:center; display:inline-flex; font-size:12px; font-weight:600; white-space:nowrap; }
    .sq-page .sq-doc-dot { border-radius:50%; display:inline-block; height:8px; margin-left:6px; width:8px; }
    .sq-page .sq-doc-dot.is-draft { background:#f0b429; box-shadow:0 0 0 2px rgba(240,180,41,.25); }
    .sq-page .sq-doc-dot.is-released { background:#2ca66e; box-shadow:0 0 0 2px rgba(44,166,110,.25); }
    .sq-page .sq-empty { padding:52px 20px; text-align:center; }
    .sq-page .sq-empty-icon { align-items:center; background:#edf3ff; border-radius:16px; color:var(--sq-blue); display:inline-flex; font-size:26px; height:58px; justify-content:center; width:58px; }
    .sq-page .dataTables_wrapper .dataTables_filter input { border:1px solid #d8e0eb; border-radius:8px; margin-left:7px; padding:6px 10px; }
    .sq-page .dataTables_wrapper .dataTables_length select { border:1px solid #d8e0eb; border-radius:7px; padding:4px 22px 4px 8px; }
    .sq-page .dataTables_wrapper .dataTables_info,
    .sq-page .dataTables_wrapper .dataTables_length { color:var(--sq-muted); font-size:12px; }
    .sq-page .sq-dt-top { padding:12px 24px 0; margin:0 !important; align-items:center; }
    .sq-page .sq-dt-top .dataTables_length { padding-top:4px; }
    .sq-page .sq-dt-top .dataTables_info { padding-top:6px; }
    .sq-page .sq-dt-table { overflow-x:auto; }
    .sq-page .sq-dt-bottom { padding:8px 24px 14px; margin:0 !important; align-items:center; }
    .sq-page .sq-dt-bottom .dataTables_paginate { margin:0; }
    .sq-page .dataTables_wrapper .dataTables_paginate .paginate_button { color:var(--sq-muted) !important; border-radius:6px; }
    .sq-page .dataTables_wrapper .dataTables_paginate .paginate_button:hover { background:#eef4ff; border-color:transparent; color:var(--sq-blue) !important; }
    .sq-page .dataTables_wrapper .dataTables_paginate .paginate_button.current { background:var(--sq-blue); border-color:var(--sq-blue); color:#fff !important; }
    .sq-page .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover { background:var(--sq-blue); border-color:var(--sq-blue); color:#fff !important; }
    .sq-page .dataTables_wrapper .dataTables_paginate .paginate_button.disabled { color:#c5cdd9 !important; }
    .sq-page .page-item.active .page-link { background-color:var(--sq-blue); border-color:var(--sq-blue); }
    @media (max-width:767px) {
        .sq-page .sq-hero { padding:22px 20px; }
        .sq-page .sq-hero-icon { display:none; }
        .sq-page .sq-card .card-body { padding:18px; }
        .sq-page .sq-table-head { padding:17px 18px 13px; }
        .sq-page .sq-table-wrap { padding:4px 12px 14px; }
    }
    @media print {
        .sq-page .sq-hero, .sq-page .sq-picker-card, .sq-page .sq-chip-filter,
        .sq-page .sq-search-box, .sq-page .sq-dt-top, .sq-page .sq-dt-bottom,
        .sq-page .dataTables_length, .sq-page .dataTables_filter, .sq-page .dataTables_paginate,
        .sq-page .sq-print-hide { display:none !important; }
        .sq-page .sq-card { box-shadow:none; }
    }
</style>

<div class="content-page sq-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-12">
                    <div class="sq-hero <?= $isQualified ? 'sq-hero-qualified' : 'sq-hero-disqualified'; ?>">
                        <div class="sq-hero-copy">
                            <div class="sq-eyebrow"><?= !empty($isVerifier) ? 'Verifier review workspace' : 'Secretariat recruitment workspace'; ?></div>
                            <h2><?= $isQualified ? 'Qualified Applicants' : 'Disqualified Applicants'; ?></h2>
                            <p>
                                <?php if ($isQualified) : ?>
                                    Applicants the evaluator marked Qualified for this vacancy, including the ones already rated and their total points.
                                <?php else : ?>
                                    Applicants the evaluator marked Disqualified for this vacancy, with the recorded reason.
                                <?php endif; ?>
                            </p>
                        </div>
                        <div class="sq-hero-icon"><i class="mdi <?= $isQualified ? 'mdi-account-check' : 'mdi-account-cancel'; ?>"></i></div>
                    </div>
                </div>
            </div>

            <?php if ($this->session->flashdata('danger')) : ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $sq_h($this->session->flashdata('danger')); ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
            <?php endif; ?>

            <div class="card sq-card sq-picker-card mb-3">
                <div class="card-body">
                    <?php if (empty($vacancies)) : ?>
                        <div class="alert alert-warning mb-0">
                            No open vacancy is assigned to your account. Please ask a Super Admin to tag a vacancy to your Secretariat account first.
                        </div>
                    <?php else : ?>
                        <form method="get" action="<?= base_url('secretariat/' . $mode); ?>">
                            <div class="form-row align-items-end">
                                <div class="col-lg-9 col-md-8">
                                    <label for="job_id" class="font-weight-bold">Position / vacancy</label>
                                    <select name="job_id" id="job_id" class="form-control sq-select sq-vacancy-select2" required>
                                        <option value="">Choose a position first...</option>
                                        <?php foreach ($vacancies as $vacancy) : ?>
                                            <?php
                                            $group = $positionGroups[(int) $vacancy->position] ?? 'Vacancy';
                                            $jobTypeLabel = $jobTypeLabels[(int) ($vacancy->job_type ?? 0)] ?? '';
                                            $vacancyLabel = $vacancy->jobTitle;
                                            if ($jobTypeLabel !== '') {
                                                $vacancyLabel .= ' — ' . $jobTypeLabel;
                                            }
                                            $vacancyLabel .= ' — ' . $group . ' — FY ' . $vacancy->sy;
                                            ?>
                                            <option value="<?= (int) $vacancy->jobID; ?>" <?= (int) $vacancy->jobID === $selectedJobId ? 'selected' : ''; ?>>
                                                <?= $sq_h($vacancyLabel); ?> (<?= (int) $vacancy->applicant_total; ?> applicants)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-lg-3 col-md-4 mt-2 mt-md-0">
                                    <button type="submit" class="btn btn-primary btn-block sq-select font-weight-bold">
                                        <i class="mdi mdi-account-search-outline mr-1"></i> View list
                                    </button>
                                </div>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($selectedVacancy) : ?>
                <div class="card sq-card mb-3">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between flex-wrap mb-3">
                            <div class="d-flex align-items-start mb-2">
                                <span class="sq-selected-icon"><i class="mdi mdi-briefcase-check"></i></span>
                                <div class="ml-3">
                                    <h4 class="mb-1"><?= $sq_h($selectedVacancy->jobTitle); ?></h4>
                                    <div class="text-muted">
                                        <?php $selJobTypeLabel = $jobTypeLabels[(int) ($selectedVacancy->job_type ?? 0)] ?? ''; ?>
                                        <?php if ($selJobTypeLabel !== '') : ?>
                                            <?= $sq_h($selJobTypeLabel); ?> &middot;
                                        <?php endif; ?>
                                        <?= $sq_h($positionGroups[(int) $selectedVacancy->position] ?? 'Vacancy'); ?>
                                        &middot; FY <?= $sq_h($selectedVacancy->sy); ?>
                                        <?php if (!empty($selectedVacancy->itemNo)) : ?>&middot; Item <?= $sq_h($selectedVacancy->itemNo); ?><?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="sq-print-hide">
                                <?php if (empty($isVerifier)) : ?>
                                <a href="<?= base_url('secretariat/' . $otherMode . '?job_id=' . $selectedJobId); ?>" class="btn btn-outline-secondary btn-sm">
                                    <i class="mdi mdi-swap-horizontal mr-1"></i> <?= $sq_h($otherLabel); ?>
                                </a>
                                <?php endif; ?>
                                <a href="<?= base_url('secretariat/disqualified'); ?>" class="btn btn-outline-secondary btn-sm">
                                    <i class="mdi mdi-view-dashboard-outline mr-1"></i> <?= !empty($isVerifier) ? 'Refresh' : 'Dashboard'; ?>
                                </a>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl-4 col-sm-6 mb-2">
                                <div class="sq-metric">
                                    <span class="sq-metric-icon <?= $isQualified ? 'sq-icon-green' : 'sq-icon-red'; ?>">
                                        <i class="mdi <?= $isQualified ? 'mdi-account-check-outline' : 'mdi-account-remove-outline'; ?>"></i>
                                    </span>
                                    <div>
                                        <div class="sq-metric-label"><?= $isQualified ? 'Qualified' : 'Disqualified'; ?></div>
                                        <div class="sq-metric-value"><?= count($applicants); ?></div>
                                        <div class="sq-metric-note">Decision recorded by the evaluator</div>
                                    </div>
                                </div>
                            </div>
                            <?php if ($isQualified) : ?>
                                <div class="col-xl-4 col-sm-6 mb-2">
                                    <div class="sq-metric">
                                        <span class="sq-metric-icon sq-icon-blue"><i class="mdi mdi-star-check-outline"></i></span>
                                        <div>
                                            <div class="sq-metric-label">Rated</div>
                                            <div class="sq-metric-value"><?= $ratedTotal; ?></div>
                                            <div class="sq-metric-note">All core criteria already scored</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-sm-6 mb-2">
                                    <div class="sq-metric">
                                        <span class="sq-metric-icon sq-icon-amber"><i class="mdi mdi-clock-outline"></i></span>
                                        <div>
                                            <div class="sq-metric-label">Awaiting rating</div>
                                            <div class="sq-metric-value"><?= $awaitingRating; ?></div>
                                            <div class="sq-metric-note">Qualified but not yet fully scored</div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="card sq-card">
                    <div class="sq-table-head">
                        <div class="sq-table-title"><?= $isQualified ? 'Qualified list' : 'Disqualified list'; ?></div>
                        <div class="d-flex align-items-center flex-wrap" style="gap:10px;">
                            <?php if ($isQualified && !empty($applicants)) : ?>
                                <div class="sq-print-hide">
                                    <button type="button" class="sq-chip-filter is-active" data-rating-filter="">All</button>
                                    <button type="button" class="sq-chip-filter" data-rating-filter="rated">Rated (<?= $ratedTotal; ?>)</button>
                                    <button type="button" class="sq-chip-filter" data-rating-filter="unrated">Not yet rated (<?= $awaitingRating; ?>)</button>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($applicants)) : ?>
                                <div class="sq-print-hide sq-search-box">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-prepend">
                                            <span class="input-group-text bg-white border-right-0"><i class="mdi mdi-magnify"></i></span>
                                        </span>
                                        <input type="text" class="form-control border-left-0 sq-table-search" placeholder="Search name, record #, school..." autocomplete="off">
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if (empty($applicants)) : ?>
                        <div class="sq-empty">
                            <span class="sq-empty-icon"><i class="mdi mdi-clipboard-text-outline"></i></span>
                            <h5 class="mt-3 mb-1">No <?= $isQualified ? 'qualified' : 'disqualified'; ?> applicant yet</h5>
                            <p class="text-muted mb-0">Rows appear here once the evaluator records a decision for this vacancy.</p>
                        </div>
                    <?php else : ?>
                        <div class="sq-table-wrap table-responsive">
                            <table id="sq-table" class="table sq-table">
                                <thead>
                                    <tr>
                                        <th>Record #</th>
                                        <th>Applicant</th>
                                        <th>Status</th>
                                        <th>School / District</th>
                                        <th>Evaluator</th>
                                        <?php if ($isQualified) : ?>
                                            <th>Rating</th>
                                        <?php else : ?>
                                            <th>Reason</th>
                                        <?php endif; ?>
                                        <th class="sq-print-hide">Action</th>
                                        <th style="display:none;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($applicants as $applicant) : ?>
                                        <?php
                                        [$statusLabel, $statusClass] = $statusChip($applicant);
                                        $profileUrl = $applicantProfileUrl($applicant);
                                        $isRated = !empty($applicant->is_rated);
                                        $breakdown = [];
                                        foreach (($applicant->rating_components ?? []) as $label => $value) {
                                            $breakdown[] = $sq_h($label . ' ' . $scoreText($value));
                                        }
                                        ?>
                                        <tr>
                                            <td><?= $sq_h($applicant->record_no); ?></td>
                                            <td>
                                                <div class="sq-name"><?= $sq_h($applicantName($applicant)); ?></div>
                                                <?php if (trim((string) ($applicant->specialization ?? '')) !== '') : ?>
                                                    <div class="sq-sub"><?= $sq_h($applicant->specialization); ?></div>
                                                <?php endif; ?>
                                            </td>
                                            <td><span class="sq-status <?= $statusClass; ?>"><?= $sq_h($statusLabel); ?></span></td>
                                            <td>
                                                <div class="sq-sub"><?= $sq_h($applicant->schoolName ?? ''); ?></div>
                                                <?php if (trim((string) ($applicant->district ?? '')) !== '') : ?>
                                                    <div class="sq-sub"><?= $sq_h($applicant->district); ?></div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php $evaluatorName = trim((string) ($applicant->evaluator_name ?? '')); ?>
                                                <?php if ($evaluatorName !== '') : ?>
                                                    <div class="sq-sub"><?= $sq_h($evaluatorName); ?></div>
                                                <?php else : ?>
                                                    <span class="text-muted">Not tagged</span>
                                                <?php endif; ?>
                                            </td>
                                            <?php if ($isQualified) : ?>
                                                <td data-order="<?= $isRated ? (float) ($applicant->rating_total ?? 0) : -1; ?>">
                                                    <?php if ($isRated) : ?>
                                                        <span class="sq-rated"><i class="mdi mdi-check-circle-outline mr-1"></i>Rated</span>
                                                        <div class="sq-points mt-1"><?= $scoreText($applicant->rating_total ?? 0); ?> pts</div>
                                                    <?php else : ?>
                                                        <span class="sq-unrated"><i class="mdi mdi-clock-outline mr-1"></i>Not yet rated</span>
                                                    <?php endif; ?>
                                                    <?php if (!empty($breakdown)) : ?>
                                                        <div class="sq-breakdown"><?= implode(' &middot; ', $breakdown); ?></div>
                                                    <?php endif; ?>
                                                    <?php if (isset($applicant->interview) || isset($applicant->written)) : ?>
                                                        <div class="sq-breakdown">
                                                            Interview <?= $scoreText($applicant->interview ?? null); ?>
                                                            &middot; Written <?= $scoreText($applicant->written ?? null); ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </td>
                                            <?php else : ?>
                                                <td>
                                                    <div class="sq-reason"><?= nl2br($sq_h(trim((string) ($applicant->dq_reason ?? '')) !== '' ? $applicant->dq_reason : 'No reason recorded.')); ?></div>
                                                    <?php if (trim((string) ($applicant->dq_date ?? '')) !== '') : ?>
                                                        <div class="sq-sub"><?= $sq_h($applicant->dq_date); ?></div>
                                                    <?php endif; ?>
                                                </td>
                                            <?php endif; ?>
                                            <td class="sq-print-hide">
                                                <?php
                                                $appDocs = $issuedDocs[(int) $applicant->appID] ?? [];
                                                $docButtons = $isQualified
                                                    ? ['assessment' => 'Evaluative Assessment']
                                                    : ['assessment' => 'Evaluative Assessment', 'letter' => 'Letter (Non-Compliant)'];
                                                ?>
                                                <div class="sq-actions">
                                                    <?php if (empty($isVerifier)) : ?>
                                                        <?php foreach ($docButtons as $docKey => $docLabel) : ?>
                                                            <?php $docState = $appDocs[$docKey] ?? null; ?>
                                                            <a class="btn btn-sm <?= $docKey === 'letter' ? 'btn-outline-danger' : 'btn-primary'; ?> sq-doc-btn"
                                                                target="_blank"
                                                                href="<?= base_url('application-document/' . (int) $applicant->appID . '/' . $docKey); ?>"
                                                                title="<?= $sq_h($docLabel); ?>">
                                                                <i class="mdi <?= $docKey === 'letter' ? 'mdi-email-outline' : 'mdi-file-document-edit-outline'; ?> mr-1"></i><?= $docKey === 'letter' ? 'Letter' : 'Assessment'; ?>
                                                                <?php if ($docState !== null) : ?>
                                                                    <span class="sq-doc-dot <?= !empty($docState['released']) ? 'is-released' : 'is-draft'; ?>"
                                                                        title="<?= !empty($docState['released']) ? 'Released to the applicant' : 'Saved, not released yet'; ?>"></span>
                                                                <?php endif; ?>
                                                            </a>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                    <?php if ($profileUrl !== '') : ?>
                                                        <a class="btn btn-outline-secondary btn-sm" target="_blank" href="<?= $sq_h($profileUrl); ?>" title="View application">
                                                            <i class="mdi mdi-account-details mr-1"></i>View Application
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td style="display:none;"><?= $isRated ? 'rated' : 'unrated'; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            <?php elseif (!empty($vacancies)) : ?>
                <div class="card sq-card">
                    <div class="sq-empty">
                        <span class="sq-empty-icon"><i class="mdi mdi-briefcase-search-outline"></i></span>
                        <h5 class="mt-3 mb-1">Select a vacancy</h5>
                        <p class="text-muted mb-0">Choose a position above to open its <?= $isQualified ? 'qualified' : 'disqualified'; ?> list.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>


<script>
(function () {
    var RATING_COLUMN = <?= $isQualified ? 7 : -1; ?>;
    var table = null;

    function initVacancySelect2() {
        if (!window.jQuery || !jQuery.fn || !jQuery.fn.select2) return;
        var $sel = jQuery('#job_id.sq-vacancy-select2');
        if (!$sel.length || $sel.data('select2')) return;
        $sel.select2({
            width: '100%',
            placeholder: 'Choose a position first...',
            allowClear: false,
            matcher: function (params, data) {
                if (!params.term || !params.term.trim()) return data;
                var text = (data.text || '').toString();
                if (text.toLowerCase().indexOf(params.term.trim().toLowerCase()) > -1) {
                    return data;
                }
                return null;
            }
        });
    }

    function initTable() {
        if (!window.jQuery || !jQuery.fn || !jQuery.fn.DataTable) return;
        var node = jQuery('#sq-table');
        if (!node.length || jQuery.fn.DataTable.isDataTable(node)) return;

        table = node.DataTable({
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
            order: [[1, 'asc']],
            autoWidth: false,
            // Custom DOM: no default filter (we use our own search box), keep
            // length menu + info + pagination. The table renders inside the
            // sq-table-wrap div, so the dom only adds the top and bottom bars.
            dom: '<"row sq-dt-top"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 text-md-right"i>>' +
                 '<"sq-dt-table"rt>' +
                 '<"row sq-dt-bottom"<"col-sm-12 text-right"p>>',
            columnDefs: [
                { orderable: false, targets: [6] },
                { visible: false, searchable: true, targets: [7] }
            ]
        });

        // Wire the custom search box to the DataTable.
        jQuery('.sq-table-search').on('keyup change', function () {
            if (table) table.search(this.value).draw();
        });

        jQuery('.sq-chip-filter').on('click', function () {
            var value = jQuery(this).attr('data-rating-filter') || '';
            jQuery('.sq-chip-filter').removeClass('is-active');
            jQuery(this).addClass('is-active');
            if (!table || RATING_COLUMN < 0) return;
            table.column(RATING_COLUMN).search(value ? '^' + value + '$' : '', true, false).draw();
        });
    }

    if (document.readyState === 'complete') {
        initVacancySelect2();
        initTable();
    } else {
        window.addEventListener('load', function () {
            initVacancySelect2();
            initTable();
        });
    }
})();
</script>
