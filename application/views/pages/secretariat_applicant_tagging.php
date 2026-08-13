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
    .sat-page .sat-toolbar { display:flex; gap:10px; align-items:center; flex-wrap:wrap; }
    .sat-page .sat-search { position:relative; min-width:260px; flex:1; }
    .sat-page .sat-search i { color:#8492a6; left:13px; position:absolute; top:12px; }
    .sat-page .sat-search input { border-color:#d8e0eb; border-radius:9px; height:40px; padding-left:36px; }
    .sat-page .sat-filter { border-color:#d8e0eb; border-radius:9px; height:40px; min-width:185px; }
    .sat-page .sat-table-wrap { border:1px solid var(--sat-line); border-radius:12px; overflow:auto; }
    .sat-page .sat-table { min-width:1120px; margin:0; }
    .sat-page .sat-table thead th { background:var(--sat-soft); border-bottom:1px solid #dfe6ef; color:#506176; font-size:11px; letter-spacing:.04em; padding:12px; position:sticky; text-transform:uppercase; top:0; z-index:2; }
    .sat-page .sat-table td { border-top:1px solid #edf1f6; padding:13px 12px; vertical-align:middle; }
    .sat-page .sat-name { color:var(--sat-ink); font-weight:700; line-height:1.25; }
    .sat-page .sat-sub { color:var(--sat-muted); font-size:12px; margin-top:3px; }
    .sat-page .sat-status { border-radius:20px; display:inline-flex; font-size:11px; font-weight:700; padding:5px 9px; white-space:nowrap; }
    .sat-page .sat-status-submitted { background:#fff3d8; color:#8a5b00; }
    .sat-page .sat-status-validated { background:#e2f6eb; color:#197447; }
    .sat-page .sat-assignee { min-width:160px; }
    .sat-page .sat-assignee-name { color:#243b5a; font-size:13px; font-weight:700; }
    .sat-page .sat-unassigned { color:#9a6b13; font-weight:600; }
    .sat-page .sat-tag-form { display:flex; align-items:center; gap:7px; min-width:340px; }
    .sat-page .sat-tag-form select { border-color:#ced7e5; border-radius:8px; height:36px; min-width:225px; }
    .sat-page .sat-tag-form button { border-radius:8px; font-weight:700; min-width:82px; }
    .sat-page .sat-empty { padding:52px 20px; text-align:center; }
    .sat-page .sat-empty-icon { align-items:center; background:#edf3ff; border-radius:16px; color:var(--sat-blue); display:inline-flex; font-size:26px; height:58px; justify-content:center; width:58px; }
    @media (max-width:767px) { .sat-page .sat-hero { padding:22px 20px; } .sat-page .sat-toolbar > * { width:100%; } .sat-page .sat-search { min-width:100%; } }
</style>

<div class="content-page sat-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-12">
                    <div class="sat-hero">
                        <div class="sat-eyebrow">Secretariat recruitment workspace</div>
                        <h2>Applicant Evaluator Tagging</h2>
                        <p>Select one of your tagged vacancies, review all submitted and validated applicants, then assign an evaluator directly from the same table.</p>
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

                        <div class="sat-toolbar mb-3">
                            <div class="sat-search">
                                <i class="mdi mdi-magnify"></i>
                                <input type="search" id="applicant-search" class="form-control" placeholder="Search applicant, number, district, or school..." autocomplete="off">
                            </div>
                            <select id="applicant-filter" class="form-control sat-filter" aria-label="Filter applicants">
                                <option value="all">All applicants</option>
                                <option value="Application Submitted">Submitted only</option>
                                <option value="Validated">Validated only</option>
                                <option value="tagged">Tagged only</option>
                                <option value="untagged">Untagged only</option>
                            </select>
                            <span class="text-muted small"><strong id="visible-count"><?= count($applicants); ?></strong> shown</span>
                        </div>

                        <?php if (empty($applicants)) : ?>
                            <div class="sat-empty">
                                <span class="sat-empty-icon"><i class="mdi mdi-account-search-outline"></i></span>
                                <h4 class="mt-3 mb-1">No submitted or validated applicants</h4>
                                <p class="text-muted mb-0">Applicants will appear here once they submit an application for this vacancy.</p>
                            </div>
                        <?php else : ?>
                            <div class="sat-table-wrap">
                                <table class="table sat-table" id="applicant-table">
                                    <thead>
                                        <tr>
                                            <th style="width:23%">Applicant</th>
                                            <th style="width:11%">Status</th>
                                            <th style="width:18%">School / district</th>
                                            <th style="width:10%">Submitted</th>
                                            <th style="width:16%">Assigned evaluator</th>
                                            <th style="width:22%">Tag to evaluator</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($applicants as $applicant) : ?>
                                            <?php
                                            $fullName = trim(implode(' ', array_filter([
                                                trim((string) ($applicant->FirstName ?? '')),
                                                trim((string) ($applicant->MiddleName ?? '')),
                                                trim((string) ($applicant->LastName ?? '')),
                                                trim((string) ($applicant->NameExtn ?? '')),
                                            ], static function ($part) { return trim((string) $part) !== ''; })));
                                            $fullName = $fullName !== '' ? $fullName : 'Applicant #' . $applicant->appID;
                                            $isTagged = !empty($applicant->assignment_id);
                                            $searchText = strtolower(implode(' ', [
                                                $fullName,
                                                $applicant->record_no ?? '',
                                                $applicant->empEmail ?? '',
                                                $applicant->district ?? '',
                                                $applicant->schoolName ?? '',
                                            ]));
                                            $profileUrl = '';
                                            if (!empty($applicant->profile_route)) {
                                                $profileUrl = base_url('Pages/' . $applicant->profile_route . '/'
                                                    . rawurlencode((string) $applicant->profile_id) . '/'
                                                    . (int) $applicant->jobID . '/'
                                                    . rawurlencode((string) $applicant->pre_school) . '/'
                                                    . (int) $applicant->appID . '/'
                                                    . rawurlencode((string) $applicant->record_no));
                                            }
                                            ?>
                                            <tr data-search="<?= $tagging_h($searchText); ?>" data-status="<?= $tagging_h($applicant->appStatus); ?>" data-tagged="<?= $isTagged ? 'tagged' : 'untagged'; ?>">
                                                <td>
                                                    <div class="sat-name"><?= $tagging_h($fullName); ?></div>
                                                    <div class="sat-sub">
                                                        No. <?= $tagging_h($applicant->record_no); ?>
                                                        <?php if ($profileUrl !== '') : ?>
                                                            &middot; <a href="<?= $tagging_h($profileUrl); ?>" target="_blank" rel="noopener">View application</a>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <?php if ($applicant->appStatus === 'Validated') : ?>
                                                        <span class="sat-status sat-status-validated">Validated</span>
                                                    <?php else : ?>
                                                        <span class="sat-status sat-status-submitted">Submitted</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div><?= $tagging_h($applicant->schoolName ?: 'School not specified'); ?></div>
                                                    <div class="sat-sub"><?= $tagging_h($applicant->district ?: 'District not specified'); ?></div>
                                                </td>
                                                <td>
                                                    <?= $tagging_h(date('M d, Y', strtotime((string) $applicant->dateSubmitted))); ?>
                                                </td>
                                                <td class="sat-assignee">
                                                    <div class="sat-assignee-name <?= $isTagged ? '' : 'sat-unassigned'; ?>">
                                                        <?= $tagging_h($isTagged ? $applicant->evaluator_name : 'Not tagged yet'); ?>
                                                    </div>
                                                    <div class="sat-sub assignment-date">
                                                        <?= $isTagged && !empty($applicant->assigned_at) ? 'Tagged ' . $tagging_h(date('M d, Y', strtotime($applicant->assigned_at))) : 'Waiting for evaluator'; ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <form class="sat-tag-form" method="post" action="<?= base_url('secretariat/applicant-tagging/tag'); ?>">
                                                        <input type="hidden" name="app_id" value="<?= (int) $applicant->appID; ?>">
                                                        <input type="hidden" name="job_id" value="<?= (int) $selectedVacancy->jobID; ?>">
                                                        <select name="rater_id" class="form-control form-control-sm" required aria-label="Evaluator for <?= $tagging_h($fullName); ?>" <?= empty($evaluators) ? 'disabled' : ''; ?>>
                                                            <option value="">Select evaluator...</option>
                                                            <?php foreach ($evaluators as $evaluator) : ?>
                                                                <?php
                                                                $evaluatorName = trim(implode(' ', array_filter([
                                                                    trim((string) ($evaluator->fname ?? '')),
                                                                    trim((string) ($evaluator->mname ?? '')),
                                                                    trim((string) ($evaluator->lname ?? '')),
                                                                ], static function ($part) { return trim((string) $part) !== ''; })));
                                                                ?>
                                                                <option value="<?= (int) $evaluator->id; ?>" <?= (int) $applicant->rater_user_id === (int) $evaluator->id ? 'selected' : ''; ?>>
                                                                    <?= $tagging_h($evaluatorName); ?> (<?= (int) $evaluator->assigned_total; ?>)
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                        <button type="submit" class="btn btn-sm <?= $isTagged ? 'btn-outline-primary' : 'btn-primary'; ?>" <?= empty($evaluators) ? 'disabled' : ''; ?>>
                                                            <?= $isTagged ? 'Reassign' : 'Tag'; ?>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <?php if (empty($evaluators)) : ?>
                                <div class="alert alert-warning mt-3 mb-0">No eligible Evaluator account (Evaluator, group 1) is currently available.</div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
(function () {
    var table = document.getElementById('applicant-table');
    if (!table) return;

    var rows = Array.prototype.slice.call(table.querySelectorAll('tbody tr'));
    var search = document.getElementById('applicant-search');
    var filter = document.getElementById('applicant-filter');
    var visibleCount = document.getElementById('visible-count');
    var message = document.getElementById('tagging-message');

    function filterRows() {
        var term = (search.value || '').toLowerCase().trim();
        var selected = filter.value;
        var visible = 0;

        rows.forEach(function (row) {
            var matchesText = term === '' || (row.getAttribute('data-search') || '').indexOf(term) !== -1;
            var matchesFilter = selected === 'all'
                || row.getAttribute('data-status') === selected
                || row.getAttribute('data-tagged') === selected;
            var show = matchesText && matchesFilter;
            row.style.display = show ? '' : 'none';
            if (show) visible++;
        });

        visibleCount.textContent = visible;
    }

    function showMessage(ok, text) {
        message.className = 'alert ' + (ok ? 'alert-success' : 'alert-danger');
        message.textContent = text;
        message.classList.remove('d-none');
        window.clearTimeout(showMessage.timer);
        showMessage.timer = window.setTimeout(function () { message.classList.add('d-none'); }, 5000);
    }

    search.addEventListener('input', filterRows);
    filter.addEventListener('change', filterRows);

    table.addEventListener('submit', function (event) {
        var form = event.target;
        if (!form.classList.contains('sat-tag-form') || !window.fetch) return;

        event.preventDefault();
        var button = form.querySelector('button[type="submit"]');
        var row = form.closest('tr');
        var wasTagged = row.getAttribute('data-tagged') === 'tagged';
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
            var assignee = row.querySelector('.sat-assignee-name');
            var date = row.querySelector('.assignment-date');
            assignee.textContent = body.evaluator_name;
            assignee.classList.remove('sat-unassigned');
            date.textContent = 'Tagged just now';
            row.setAttribute('data-tagged', 'tagged');
            button.textContent = 'Reassign';
            button.classList.remove('btn-primary');
            button.classList.add('btn-outline-primary');

            if (!wasTagged) {
                var taggedCount = document.getElementById('tagged-count');
                var untaggedCount = document.getElementById('untagged-count');
                taggedCount.textContent = parseInt(taggedCount.textContent || '0', 10) + 1;
                untaggedCount.textContent = Math.max(0, parseInt(untaggedCount.textContent || '0', 10) - 1);
            }

            showMessage(true, body.message);
            filterRows();
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
