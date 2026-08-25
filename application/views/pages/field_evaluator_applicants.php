<?php
$fea_h = static function ($value) {
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
$selectedJobId = (int) ($selectedJobId ?? 0);
$selectedVacancy = $selectedVacancy ?? null;
$evaluatorId = (int) ($evaluatorId ?? 0);

$applicantName = static function ($applicant) {
    $name = trim(preg_replace('/\s+/', ' ', implode(' ', [
        (string) ($applicant->FirstName ?? ''),
        (string) ($applicant->MiddleName ?? ''),
        (string) ($applicant->LastName ?? ''),
        (string) ($applicant->NameExtn ?? ''),
    ])));

    return $name !== '' ? $name : 'Applicant #' . (int) $applicant->appID;
};

// Same link the Secretariat uses on the tagging page, so a Field Evaluator
// lands on the very same application view.
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

// Same chip vocabulary as the Secretariat's tagging screen.
$statusBadge = static function ($applicant) {
    if ((int) ($applicant->dq ?? 0) === 2) {
        return ['fea-status-dq', 'Disqualified'];
    }

    switch ((string) ($applicant->appStatus ?? '')) {
        case 'Validated':
            return ['fea-status-validated', 'Validated'];
        case 'Endorsed for Rating':
            return ['fea-status-endorsed', 'Endorsed'];
        case 'Rated':
            return ['fea-status-rated', 'Rated'];
        case 'Confirmed':
            return ['fea-status-rated', 'Confirmed'];
        default:
            return ['fea-status-submitted', 'Submitted'];
    }
};

$untaggedTotal = 0;
$mineTotal = 0;
foreach ($applicants as $applicant) {
    if (empty($applicant->assignment_id)) {
        $untaggedTotal++;
    }
    if ((int) ($applicant->is_mine ?? 0) === 1) {
        $mineTotal++;
    }
}
?>

<style>
    .fea-page { --fea-ink:#183153; --fea-muted:#6b7a90; --fea-line:#e6ebf2; --fea-blue:#2457d6; --fea-soft:#f5f8fc; padding-bottom:28px; }
    .fea-page .container-fluid { max-width:1440px; }
    .fea-page .fea-hero { align-items:center; background:linear-gradient(125deg,#0f3d3a 0%,#166f63 62%,#31a58f 100%); border-radius:18px; box-shadow:0 16px 36px rgba(16,58,54,.18); color:#fff; display:flex; justify-content:space-between; overflow:hidden; padding:27px 30px; }
    .fea-page .fea-hero h2 { color:#fff; font-weight:700; margin:0 0 6px; }
    .fea-page .fea-hero p { margin:0; opacity:.9; }
    .fea-page .fea-eyebrow { font-size:11px; letter-spacing:.14em; opacity:.85; text-transform:uppercase; }
    .fea-page .fea-hero-icon { font-size:54px; opacity:.28; }
    .fea-page .fea-card { background:#fff; border:1px solid var(--fea-line); border-radius:14px; box-shadow:0 6px 18px rgba(24,49,83,.06); }
    .fea-page .fea-card h4 { color:var(--fea-ink); font-size:17px; font-weight:700; }
    .fea-page .fea-selected-icon { align-items:center; background:#e7f5f1; border-radius:12px; color:#12796a; display:inline-flex; font-size:22px; height:46px; justify-content:center; min-width:46px; }
    .fea-page .fea-note { align-items:flex-start; background:var(--fea-soft); border-left:3px solid var(--fea-blue); border-radius:8px; color:var(--fea-ink); display:flex; gap:10px; padding:12px 14px; }
    .fea-page .fea-metric { align-items:center; background:#fff; border:1px solid var(--fea-line); border-radius:12px; display:flex; gap:12px; height:100%; padding:14px 16px; }
    .fea-page .fea-metric-icon { align-items:center; border-radius:10px; display:inline-flex; font-size:20px; height:40px; justify-content:center; min-width:40px; }
    .fea-page .fea-icon-blue { background:#eaf1ff; color:#2457d6; }
    .fea-page .fea-icon-green { background:#e7f6f0; color:#12796a; }
    .fea-page .fea-icon-amber { background:#fff4e3; color:#a4700f; }
    .fea-page .fea-icon-grey { background:#eef1f6; color:#5b6b81; }
    .fea-page .fea-metric-label { color:var(--fea-muted); font-size:12px; }
    .fea-page .fea-metric-value { color:var(--fea-ink); font-size:22px; font-weight:700; line-height:1.1; }
    .fea-page .table thead th { background:var(--fea-soft); border-bottom:1px solid var(--fea-line); color:var(--fea-muted); font-size:11px; letter-spacing:.06em; text-transform:uppercase; }
    .fea-page .table td { vertical-align:middle; }
    .fea-page .fea-status { border-radius:20px; display:inline-flex; font-size:11px; font-weight:700; padding:5px 9px; white-space:nowrap; }
    .fea-page .fea-status-submitted { background:#fff3d8; color:#8a5b00; }
    .fea-page .fea-status-validated { background:#e2f6eb; color:#197447; }
    .fea-page .fea-status-endorsed { background:#e8efff; color:#2457d6; }
    .fea-page .fea-status-rated { background:#efe9ff; color:#6e43c0; }
    .fea-page .fea-status-dq { background:#fdeaea; color:#a52c2c; }
    .fea-page .fea-name { color:var(--fea-ink); font-weight:600; }
    .fea-page .fea-sub { color:var(--fea-muted); font-size:12px; }
    .fea-page .fea-eval { color:var(--fea-ink); font-weight:600; }
    .fea-page .fea-eval-none { color:#a4700f; font-weight:600; }
    .fea-page .fea-mine { background:#e7f6f0; border-radius:6px; color:#0f6d5c; display:inline-block; font-size:10px; font-weight:700; letter-spacing:.04em; margin-left:6px; padding:2px 6px; text-transform:uppercase; }
    .fea-page .fea-empty { padding:48px 20px; text-align:center; }
    .fea-page .fea-empty-icon { align-items:center; background:#e7f5f1; border-radius:16px; color:#12796a; display:inline-flex; font-size:26px; height:58px; justify-content:center; width:58px; }
    .fea-page .dataTables_wrapper .dataTables_filter input { border:1px solid #d8e0eb; border-radius:8px; margin-left:7px; padding:6px 10px; }
    .fea-page .dataTables_wrapper .dataTables_length select { border:1px solid #d8e0eb; border-radius:7px; padding:4px 22px 4px 8px; }
    .fea-page .dataTables_wrapper .dataTables_info,
    .fea-page .dataTables_wrapper .dataTables_length,
    .fea-page .dataTables_wrapper .dataTables_filter { color:var(--fea-muted); font-size:12px; }
    .fea-page .page-item.active .page-link { background-color:var(--fea-blue); border-color:var(--fea-blue); }
    @media (max-width:767px) { .fea-page .fea-hero { padding:22px 20px; } .fea-page .fea-hero-icon { display:none; } .fea-page .fea-card .card-body { padding:18px; } }
</style>

<div class="content-page fea-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-12">
                    <div class="fea-hero">
                        <div>
                            <div class="fea-eyebrow">Field Evaluator workspace</div>
                            <h2>All Applicants</h2>
                            <p>Every applicant of the vacancies you field-evaluate, and who is evaluating each one.</p>
                        </div>
                        <div class="fea-hero-icon"><i class="mdi mdi-account-eye-outline"></i></div>
                    </div>
                </div>
            </div>

            <?php if ($this->session->flashdata('danger')) : ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $fea_h($this->session->flashdata('danger')); ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
            <?php endif; ?>

            <?php if (count($vacancies) > 1) : ?>
                <div class="card fea-card mb-3">
                    <div class="card-body">
                        <form method="get" action="<?= base_url('field-evaluator'); ?>">
                            <div class="form-row align-items-end">
                                <div class="col-lg-9 col-md-8">
                                    <label for="job_id" class="font-weight-bold">Vacancy you field-evaluate</label>
                                    <select name="job_id" id="job_id" class="form-control" required>
                                        <option value="">Choose a vacancy...</option>
                                        <?php foreach ($vacancies as $vacancy) : ?>
                                            <?php
                                            $group = $positionGroups[(int) $vacancy->position] ?? 'Vacancy';
                                            $vacancyLabel = $vacancy->jobTitle . ' — ' . $group . ' — FY ' . $vacancy->sy;
                                            ?>
                                            <option value="<?= (int) $vacancy->jobID; ?>" <?= (int) $vacancy->jobID === $selectedJobId ? 'selected' : ''; ?>>
                                                <?= $fea_h($vacancyLabel); ?> (<?= (int) $vacancy->applicant_total; ?> applicants)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-lg-3 col-md-4 mt-2 mt-md-0">
                                    <button type="submit" class="btn btn-primary btn-block font-weight-bold">
                                        <i class="mdi mdi-account-search-outline mr-1"></i> View applicants
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!$selectedVacancy) : ?>
                <div class="card fea-card">
                    <div class="card-body fea-empty">
                        <div class="fea-empty-icon mb-3"><i class="mdi mdi-briefcase-outline"></i></div>
                        <h5 class="mb-1">Choose a vacancy</h5>
                        <p class="text-muted mb-0">Select one of the vacancies you were tagged to field-evaluate.</p>
                    </div>
                </div>
            <?php else : ?>
                <div class="card fea-card mb-3">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between flex-wrap mb-3">
                            <div class="d-flex align-items-start mb-2">
                                <span class="fea-selected-icon"><i class="mdi mdi-briefcase-check"></i></span>
                                <div class="ml-3">
                                    <h4 class="mb-1">All applicants for <?= $fea_h($selectedVacancy->jobTitle); ?></h4>
                                    <div class="text-muted">
                                        <?= $fea_h($positionGroups[(int) $selectedVacancy->position] ?? 'Vacancy'); ?>
                                        &middot; FY <?= $fea_h($selectedVacancy->sy); ?>
                                        <?php if (!empty($selectedVacancy->itemNo)) : ?>&middot; Item <?= $fea_h($selectedVacancy->itemNo); ?><?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php if (count($vacancies) > 1) : ?>
                                <a href="<?= base_url('field-evaluator'); ?>" class="btn btn-outline-secondary btn-sm">
                                    <i class="mdi mdi-swap-horizontal mr-1"></i> Change vacancy
                                </a>
                            <?php endif; ?>
                        </div>

                        <div class="fea-note mb-3">
                            <i class="mdi mdi-information-outline font-20"></i>
                            <div>
                                <strong>Field Evaluator access.</strong> You can open any application in this vacancy and
                                <strong>add or edit its evaluation</strong> &mdash; not only the rows marked
                                <span class="fea-mine">You</span>, and whatever stage the applicant is at. Marking an applicant
                                qualified or disqualified stays with the evaluator they are tagged to.
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl-3 col-sm-6 mb-2">
                                <div class="fea-metric">
                                    <span class="fea-metric-icon fea-icon-blue"><i class="mdi mdi-account-group-outline"></i></span>
                                    <div>
                                        <div class="fea-metric-label">Total applicants</div>
                                        <div class="fea-metric-value"><?= count($applicants); ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6 mb-2">
                                <div class="fea-metric">
                                    <span class="fea-metric-icon fea-icon-green"><i class="mdi mdi-account-check-outline"></i></span>
                                    <div>
                                        <div class="fea-metric-label">With an evaluator</div>
                                        <div class="fea-metric-value"><?= count($applicants) - $untaggedTotal; ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6 mb-2">
                                <div class="fea-metric">
                                    <span class="fea-metric-icon fea-icon-amber"><i class="mdi mdi-account-clock-outline"></i></span>
                                    <div>
                                        <div class="fea-metric-label">No evaluator yet</div>
                                        <div class="fea-metric-value"><?= $untaggedTotal; ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6 mb-2">
                                <div class="fea-metric">
                                    <span class="fea-metric-icon fea-icon-grey"><i class="mdi mdi-account-star-outline"></i></span>
                                    <div>
                                        <div class="fea-metric-label">Tagged to you</div>
                                        <div class="fea-metric-value"><?= $mineTotal; ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card fea-card">
                    <div class="card-body">
                        <?php if (empty($applicants)) : ?>
                            <div class="fea-empty">
                                <div class="fea-empty-icon mb-3"><i class="mdi mdi-account-search-outline"></i></div>
                                <h5 class="mb-1">No applicants yet</h5>
                                <p class="text-muted mb-0">No one has applied to this vacancy so far.</p>
                            </div>
                        <?php else : ?>
                            <div class="table-responsive">
                                <table id="fea-datatable" class="table table-hover mb-0" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>Applicant</th>
                                            <th>Record no.</th>
                                            <th>School / District</th>
                                            <th>Status</th>
                                            <th>Evaluator</th>
                                            <th class="text-right">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($applicants as $applicant) : ?>
                                            <?php
                                            [$badgeClass, $badgeLabel] = $statusBadge($applicant);
                                            $profileUrl = $applicantProfileUrl($applicant);
                                            $evaluatorName = trim((string) ($applicant->evaluator_name ?? ''));
                                            if ($evaluatorName === '') {
                                                $evaluatorName = trim((string) ($applicant->evaluator_username ?? ''));
                                            }
                                            ?>
                                            <tr>
                                                <td>
                                                    <div class="fea-name"><?= $fea_h($applicantName($applicant)); ?></div>
                                                    <?php if (!empty($applicant->specialization)) : ?>
                                                        <div class="fea-sub"><?= $fea_h($applicant->specialization); ?></div>
                                                    <?php endif; ?>
                                                </td>
                                                <td><span class="fea-sub"><?= $fea_h($applicant->record_no); ?></span></td>
                                                <td>
                                                    <span class="fea-sub">
                                                        <?= $fea_h($applicant->schoolName ?: ($applicant->district ?: '—')); ?>
                                                    </span>
                                                </td>
                                                <td><span class="fea-status <?= $fea_h($badgeClass); ?>"><?= $fea_h($badgeLabel); ?></span></td>
                                                <td>
                                                    <?php if (!empty($applicant->assignment_id)) : ?>
                                                        <span class="fea-eval"><?= $fea_h($evaluatorName); ?></span>
                                                        <?php if ((int) ($applicant->is_mine ?? 0) === 1) : ?>
                                                            <span class="fea-mine">You</span>
                                                        <?php endif; ?>
                                                        <?php if (!empty($applicant->assigned_at)) : ?>
                                                            <div class="fea-sub">Tagged <?= $fea_h(date('M j, Y', strtotime((string) $applicant->assigned_at))); ?></div>
                                                        <?php endif; ?>
                                                    <?php else : ?>
                                                        <span class="fea-eval-none">Not yet tagged</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-right">
                                                    <?php if ($profileUrl !== '') : ?>
                                                        <a href="<?= $fea_h($profileUrl); ?>" class="btn btn-outline-primary btn-sm" target="_blank" rel="noopener">
                                                            <i class="mdi mdi-file-document-outline mr-1"></i> Open application
                                                        </a>
                                                    <?php else : ?>
                                                        <span class="fea-sub">No application record</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    (function () {
        function initFeaTable() {
            if (!window.jQuery || !jQuery.fn || !jQuery.fn.DataTable) return;

            var table = jQuery('#fea-datatable');
            if (!table.length || jQuery.fn.DataTable.isDataTable(table)) return;

            table.DataTable({
                pageLength: 25,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'All']],
                order: [[0, 'asc']],
                responsive: true,
                autoWidth: false,
                columnDefs: [
                    { targets: 0, responsivePriority: 1 },
                    { targets: 4, responsivePriority: 2 },
                    { targets: 5, responsivePriority: 3, orderable: false }
                ],
                language: { emptyTable: 'No applicants for this vacancy yet.' }
            });
        }

        if (document.readyState === 'complete' || document.readyState === 'interactive') {
            window.setTimeout(initFeaTable, 0);
        } else {
            window.addEventListener('load', initFeaTable);
        }
    })();
</script>
