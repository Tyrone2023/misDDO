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

$untaggedApplicants = [];
$taggedApplicants = [];
foreach ($applicants as $applicant) {
    if (!empty($applicant->assignment_id)) {
        $taggedApplicants[] = $applicant;
    } else {
        $untaggedApplicants[] = $applicant;
    }
}

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
    .sat-page { --sat-ink:#183153; --sat-muted:#6b7a90; --sat-line:#e6ebf2; --sat-blue:#2457d6; --sat-soft:#f5f8fc; }
    .sat-page .sat-hero { background:linear-gradient(125deg,#123b70 0%,#2457d6 65%,#3f78e8 100%); border-radius:16px; color:#fff; padding:26px 28px; box-shadow:0 14px 32px rgba(24,49,83,.16); }
    .sat-page .sat-eyebrow { color:#cfe0ff; font-size:12px; font-weight:700; letter-spacing:.1em; text-transform:uppercase; }
    .sat-page .sat-hero h2 { color:#fff; font-size:25px; margin:7px 0 5px; }
    .sat-page .sat-hero p { color:#dce8ff; max-width:720px; margin:0; }
    .sat-page .sat-step { display:flex; align-items:center; justify-content:center; width:34px; height:34px; border-radius:10px; background:#eaf0ff; color:var(--sat-blue); font-weight:800; flex:0 0 34px; }
    .sat-page .sat-card { border:1px solid var(--sat-line); border-radius:14px; box-shadow:0 6px 22px rgba(24,49,83,.06); }
    .sat-page .sat-card .card-body { padding:22px; }
    .sat-page .sat-select { min-height:46px; border-color:#ced7e5; }
    .sat-page .sat-note { display:flex; gap:10px; align-items:flex-start; background:#eef6ff; border:1px solid #d9eaff; border-radius:10px; color:#31577d; padding:12px 14px; }
    .sat-page .sat-metric { background:#fff; border:1px solid var(--sat-line); border-radius:12px; padding:15px 17px; height:100%; }
    .sat-page .sat-metric-label { color:var(--sat-muted); font-size:12px; font-weight:700; letter-spacing:.04em; text-transform:uppercase; }
    .sat-page .sat-metric-value { color:var(--sat-ink); font-size:25px; font-weight:800; line-height:1.2; margin-top:3px; }
    .sat-page .sat-table-wrap { border:1px solid var(--sat-line); border-radius:12px; padding:14px 14px 2px; }
    .sat-page .sat-table { margin:0; width:100% !important; }
    .sat-page .sat-table thead th { background:var(--sat-soft); border-bottom:1px solid #dfe6ef; color:#506176; font-size:11px; letter-spacing:.04em; padding:12px; text-transform:uppercase; }
    .sat-page .sat-table td { border-top:1px solid #edf1f6; padding:13px 12px; vertical-align:middle; }
    .sat-page .sat-name { color:var(--sat-ink); font-weight:700; line-height:1.25; }
    .sat-page .sat-sub { color:var(--sat-muted); font-size:12px; margin-top:3px; }
    .sat-page .sat-status { border-radius:20px; display:inline-flex; font-size:11px; font-weight:700; padding:5px 9px; white-space:nowrap; }
    .sat-page .sat-status-submitted { background:#fff3d8; color:#8a5b00; }
    .sat-page .sat-status-validated { background:#e2f6eb; color:#197447; }
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
    .sat-page .dataTables_wrapper .dataTables_filter input { border:1px solid #d8e0eb; border-radius:8px; margin-left:7px; padding:6px 10px; }
    .sat-page .dataTables_wrapper .dataTables_length select { border:1px solid #d8e0eb; border-radius:7px; padding:4px 22px 4px 8px; }
    .sat-page .dataTables_wrapper .dataTables_info,
    .sat-page .dataTables_wrapper .dataTables_length,
    .sat-page .dataTables_wrapper .dataTables_filter { color:var(--sat-muted); font-size:12px; }
    .sat-page .page-item.active .page-link { background-color:var(--sat-blue); border-color:var(--sat-blue); }
    .sat-page .sat-empty { padding:52px 20px; text-align:center; }
    .sat-page .sat-empty-icon { align-items:center; background:#edf3ff; border-radius:16px; color:var(--sat-blue); display:inline-flex; font-size:26px; height:58px; justify-content:center; width:58px; }
    @media (max-width:767px) { .sat-page .sat-hero { padding:22px 20px; } .sat-page .sat-table-wrap { padding:10px 8px 1px; } .sat-page .sat-tag-form { min-width:220px; } }
</style>

<div class="content-page sat-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-12">
                    <div class="sat-hero">
                        <div class="sat-eyebrow">Secretariat recruitment workspace</div>
                        <h2>Applicant Evaluator Tagging</h2>
                        <!-- <p>Select one of your tagged vacancies, review all submitted and validated applicants, then assign an evaluator directly from the same table.</p> -->
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

            <div class="card sat-card mb-3">
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
                                                <?= $tagging_h($vacancyLabel); ?> (<?= (int) $vacancy->applicant_total; ?> applicants)
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
                <div class="card sat-card">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between flex-wrap mb-3">
                            <div class="d-flex align-items-start mb-2">
                                <span class="sat-step">2</span>
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
                                <div class="sat-metric"><div class="sat-metric-label">Applicants</div><div class="sat-metric-value"><?= (int) $selectedVacancy->applicant_total; ?></div></div>
                            </div>
                            <div class="col-xl-3 col-sm-6 mb-2">
                                <div class="sat-metric"><div class="sat-metric-label">Submitted</div><div class="sat-metric-value text-warning"><?= (int) $selectedVacancy->submitted_total; ?></div></div>
                            </div>
                            <div class="col-xl-3 col-sm-6 mb-2">
                                <div class="sat-metric"><div class="sat-metric-label">Tagged</div><div id="tagged-count" class="sat-metric-value text-success"><?= (int) $selectedVacancy->tagged_total; ?></div></div>
                            </div>
                            <div class="col-xl-3 col-sm-6 mb-2">
                                <div class="sat-metric"><div class="sat-metric-label">Still untagged</div><div id="untagged-count" class="sat-metric-value text-danger"><?= (int) $selectedVacancy->untagged_total; ?></div></div>
                            </div>
                        </div>

                        <div class="d-flex align-items-start justify-content-between flex-wrap mb-2">
                            <div>
                                <div class="sat-table-title">Applicants for tagging</div>
                                <p class="text-muted mb-0">Select an evaluator and tag the applicant directly from this table.</p>
                            </div>
                            <span class="badge badge-warning p-2"><span id="untagged-table-count"><?= count($untaggedApplicants); ?></span> untagged</span>
                        </div>

                        <div class="sat-table-wrap mb-4">
                            <table class="table table-bordered dt-responsive sat-table" id="untagged-datatable">
                                <thead>
                                    <tr>
                                        <th style="width:30%">Applicant</th>
                                        <th style="width:12%">Status</th>
                                        <th style="width:28%">School / district</th>
                                        <th style="width:30%">Tag to evaluator</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($untaggedApplicants as $applicant) : ?>
                                        <?php $fullName = $applicantName($applicant); $profileUrl = $applicantProfileUrl($applicant); ?>
                                        <tr>
                                            <td>
                                                <div class="sat-name"><?= $tagging_h($fullName); ?></div>
                                                <div class="sat-sub">
                                                    No. <?= $tagging_h($applicant->record_no); ?>
                                                    <?php if ($profileUrl !== '') : ?>&middot; <a href="<?= $tagging_h($profileUrl); ?>" target="_blank" rel="noopener">View application</a><?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="sat-status <?= $applicant->appStatus === 'Validated' ? 'sat-status-validated' : 'sat-status-submitted'; ?>">
                                                    <?= $applicant->appStatus === 'Validated' ? 'Validated' : 'Submitted'; ?>
                                                </span>
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

                        <div class="d-flex align-items-start justify-content-between flex-wrap mb-2 pt-2">
                            <div>
                                <div class="sat-table-title">Tagged applicants</div>
                                <p class="text-muted mb-0">View the assigned evaluator or select another evaluator to reassign an applicant.</p>
                            </div>
                            <span class="badge badge-success p-2"><span id="tagged-table-count"><?= count($taggedApplicants); ?></span> tagged</span>
                        </div>

                        <div class="sat-table-wrap">
                            <table class="table table-bordered dt-responsive sat-table" id="tagged-datatable">
                                <thead>
                                    <tr>
                                        <th style="width:25%">Applicant</th>
                                        <th style="width:11%">Status</th>
                                        <th style="width:22%">School / district</th>
                                        <th style="width:17%">Evaluator</th>
                                        <th style="width:25%">Reassign evaluator</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($taggedApplicants as $applicant) : ?>
                                        <?php $fullName = $applicantName($applicant); $profileUrl = $applicantProfileUrl($applicant); ?>
                                        <tr>
                                            <td>
                                                <div class="sat-name"><?= $tagging_h($fullName); ?></div>
                                                <div class="sat-sub">
                                                    No. <?= $tagging_h($applicant->record_no); ?>
                                                    <?php if ($profileUrl !== '') : ?>&middot; <a href="<?= $tagging_h($profileUrl); ?>" target="_blank" rel="noopener">View application</a><?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="sat-status <?= $applicant->appStatus === 'Validated' ? 'sat-status-validated' : 'sat-status-submitted'; ?>">
                                                    <?= $applicant->appStatus === 'Validated' ? 'Validated' : 'Submitted'; ?>
                                                </span>
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
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <?php if (empty($evaluatorOptions)) : ?>
                            <div class="alert alert-warning mt-3 mb-0">No user account with the Evaluator position is currently available.</div>
                        <?php endif; ?>
                    </div>
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

        taggedDataTable = taggedTable.DataTable({
            pageLength: 10,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'All']],
            order: [[0, 'asc']],
            responsive: true,
            autoWidth: false,
            columnDefs: [
                { targets: 0, responsivePriority: 2 },
                { targets: 3, responsivePriority: 3 },
                { targets: 4, responsivePriority: 1, orderable: false }
            ],
            language: { emptyTable: 'No applicants have been tagged yet.' }
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
                    taggedDataTable.row.add([
                        applicantCell,
                        statusCell,
                        schoolCell,
                        evaluatorWrapper.innerHTML,
                        reassignForm
                    ]).draw(false);
                    initEvaluatorSelects();
                } else {
                    window.location.reload();
                }

                showMessage(true, body.message + ' You can tag the next applicant now.');
                return;
            }

            row.querySelector('.sat-assignee-name').textContent = body.evaluator_name;
            row.querySelector('.assignment-date').textContent = 'Tagged just now';
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
