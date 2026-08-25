<?php
$fe_h = static function ($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};

$positionGroups = [
    1 => 'Teaching',
    2 => 'School Administration',
    3 => 'Related Teaching',
    4 => 'Non-Teaching',
];

$vacancies = $vacancies ?? [];
$evaluators = $evaluators ?? [];
$taggedEvaluators = $taggedEvaluators ?? [];
$selectedJobId = (int) ($selectedJobId ?? 0);
$selectedVacancy = $selectedVacancy ?? null;

// Evaluators already tagged on this vacancy are dropped from the add list, so
// the picker only ever offers a tag that can actually be saved.
$alreadyTagged = [];
foreach ($taggedEvaluators as $tag) {
    $alreadyTagged[(int) $tag->evaluator_user_id] = true;
}

$evaluatorLabel = static function ($evaluator) {
    $name = trim(preg_replace('/\s+/', ' ', implode(' ', [
        (string) ($evaluator->fname ?? ''),
        (string) ($evaluator->mname ?? ''),
        (string) ($evaluator->lname ?? ''),
    ])));

    if ($name === '') {
        $name = (string) ($evaluator->username ?? '');
    }

    return $name . ' (' . (string) ($evaluator->username ?? '') . ')';
};
?>

<style>
    .sfe-page { --sfe-ink:#183153; --sfe-muted:#6b7a90; --sfe-line:#e6ebf2; --sfe-blue:#2457d6; --sfe-soft:#f5f8fc; padding-bottom:28px; }
    .sfe-page .container-fluid { max-width:1280px; }
    .sfe-page .sfe-hero { align-items:center; background:linear-gradient(125deg,#0f3d3a 0%,#166f63 62%,#31a58f 100%); border-radius:18px; box-shadow:0 16px 36px rgba(16,58,54,.18); color:#fff; display:flex; justify-content:space-between; overflow:hidden; padding:27px 30px; }
    .sfe-page .sfe-hero h2 { color:#fff; font-weight:700; margin:0 0 6px; }
    .sfe-page .sfe-hero p { margin:0; opacity:.9; }
    .sfe-page .sfe-eyebrow { font-size:11px; letter-spacing:.14em; opacity:.85; text-transform:uppercase; }
    .sfe-page .sfe-hero-icon { font-size:54px; opacity:.28; }
    .sfe-page .sfe-card { background:#fff; border:1px solid var(--sfe-line); border-radius:14px; box-shadow:0 6px 18px rgba(24,49,83,.06); }
    .sfe-page .sfe-card h4 { color:var(--sfe-ink); font-size:17px; font-weight:700; }
    .sfe-page .sfe-step { align-items:center; background:var(--sfe-blue); border-radius:50%; color:#fff; display:inline-flex; font-weight:700; height:30px; justify-content:center; min-width:30px; }
    .sfe-page .sfe-selected-icon { align-items:center; background:#e7f5f1; border-radius:12px; color:#12796a; display:inline-flex; font-size:22px; height:46px; justify-content:center; min-width:46px; }
    .sfe-page .sfe-note { align-items:flex-start; background:var(--sfe-soft); border-left:3px solid var(--sfe-blue); border-radius:8px; color:var(--sfe-ink); display:flex; gap:10px; padding:12px 14px; }
    .sfe-page .sfe-metric { align-items:center; background:#fff; border:1px solid var(--sfe-line); border-radius:12px; display:flex; gap:12px; height:100%; padding:14px 16px; }
    .sfe-page .sfe-metric-icon { align-items:center; border-radius:10px; display:inline-flex; font-size:20px; height:40px; justify-content:center; min-width:40px; }
    .sfe-page .sfe-icon-blue { background:#eaf1ff; color:#2457d6; }
    .sfe-page .sfe-icon-green { background:#e7f6f0; color:#12796a; }
    .sfe-page .sfe-metric-label { color:var(--sfe-muted); font-size:12px; }
    .sfe-page .sfe-metric-value { color:var(--sfe-ink); font-size:22px; font-weight:700; line-height:1.1; }
    .sfe-page .table thead th { background:var(--sfe-soft); border-bottom:1px solid var(--sfe-line); color:var(--sfe-muted); font-size:11px; letter-spacing:.06em; text-transform:uppercase; }
    .sfe-page .table td { vertical-align:middle; }
    .sfe-page .sfe-name { color:var(--sfe-ink); font-weight:600; }
    .sfe-page .sfe-sub { color:var(--sfe-muted); font-size:12px; }
    .sfe-page .sfe-empty { padding:44px 20px; text-align:center; }
    .sfe-page .sfe-empty-icon { align-items:center; background:#e7f5f1; border-radius:16px; color:#12796a; display:inline-flex; font-size:26px; height:58px; justify-content:center; width:58px; }
    @media (max-width:767px) { .sfe-page .sfe-hero { padding:22px 20px; } .sfe-page .sfe-hero-icon { display:none; } .sfe-page .sfe-card .card-body { padding:18px; } }
</style>

<div class="content-page sfe-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-12">
                    <div class="sfe-hero">
                        <div>
                            <div class="sfe-eyebrow">Secretariat recruitment workspace</div>
                            <h2>Field Evaluator Tagging</h2>
                            <p>Give an evaluator oversight of a whole vacancy: every applicant, and who is evaluating each one.</p>
                        </div>
                        <div class="sfe-hero-icon"><i class="mdi mdi-account-eye-outline"></i></div>
                    </div>
                </div>
            </div>

            <?php if ($this->session->flashdata('success')) : ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $fe_h($this->session->flashdata('success')); ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
            <?php endif; ?>

            <?php if ($this->session->flashdata('danger')) : ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $fe_h($this->session->flashdata('danger')); ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
            <?php endif; ?>

            <div class="card sfe-card mb-3">
                <div class="card-body">
                    <div class="d-flex align-items-start mb-3">
                        <span class="sfe-step">1</span>
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
                        <form method="get" action="<?= base_url('secretariat/field-evaluators'); ?>">
                            <div class="form-row align-items-end">
                                <div class="col-lg-9 col-md-8">
                                    <label for="job_id" class="font-weight-bold">Position / vacancy</label>
                                    <select name="job_id" id="job_id" class="form-control" required>
                                        <option value="">Choose a position first...</option>
                                        <?php foreach ($vacancies as $vacancy) : ?>
                                            <?php
                                            $group = $positionGroups[(int) $vacancy->position] ?? 'Vacancy';
                                            $vacancyLabel = $vacancy->jobTitle . ' — ' . $group . ' — FY ' . $vacancy->sy;
                                            ?>
                                            <option value="<?= (int) $vacancy->jobID; ?>" <?= (int) $vacancy->jobID === $selectedJobId ? 'selected' : ''; ?>>
                                                <?= $fe_h($vacancyLabel); ?> (<?= (int) $vacancy->applicant_total; ?> applicants)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-lg-3 col-md-4 mt-2 mt-md-0">
                                    <button type="submit" class="btn btn-primary btn-block font-weight-bold">
                                        <i class="mdi mdi-account-eye-outline mr-1"></i> View field evaluators
                                    </button>
                                </div>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($selectedVacancy) : ?>
                <div class="card sfe-card mb-3">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between flex-wrap mb-3">
                            <div class="d-flex align-items-start mb-2">
                                <span class="sfe-selected-icon"><i class="mdi mdi-briefcase-check"></i></span>
                                <div class="ml-3">
                                    <h4 class="mb-1"><?= $fe_h($selectedVacancy->jobTitle); ?></h4>
                                    <div class="text-muted">
                                        <?= $fe_h($positionGroups[(int) $selectedVacancy->position] ?? 'Vacancy'); ?>
                                        &middot; FY <?= $fe_h($selectedVacancy->sy); ?>
                                        <?php if (!empty($selectedVacancy->itemNo)) : ?>&middot; Item <?= $fe_h($selectedVacancy->itemNo); ?><?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <a href="<?= base_url('secretariat/field-evaluators'); ?>" class="btn btn-outline-secondary btn-sm">
                                <i class="mdi mdi-swap-horizontal mr-1"></i> Change position
                            </a>
                        </div>

                        <div class="sfe-note mb-3">
                            <i class="mdi mdi-information-outline font-20"></i>
                            <div>
                                <strong>Vacancy-wide access.</strong> A Field Evaluator sees every applicant of this vacancy and
                                who is tagged to evaluate each one, and may <em>add or edit the evaluation</em> of any of them at
                                any stage &mdash; a qualification decision is not required first. Marking an applicant qualified or
                                disqualified stays with the evaluator tagged to them in
                                <a href="<?= base_url('secretariat/applicant-tagging?job_id=' . (int) $selectedVacancy->jobID); ?>">Applicant Evaluator Tagging</a>.
                            </div>
                        </div>

                        <div class="row mb-1">
                            <div class="col-sm-6 mb-2">
                                <div class="sfe-metric">
                                    <span class="sfe-metric-icon sfe-icon-blue"><i class="mdi mdi-account-group-outline"></i></span>
                                    <div>
                                        <div class="sfe-metric-label">Applicants in this vacancy</div>
                                        <div class="sfe-metric-value"><?= (int) $selectedVacancy->applicant_total; ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 mb-2">
                                <div class="sfe-metric">
                                    <span class="sfe-metric-icon sfe-icon-green"><i class="mdi mdi-account-eye-outline"></i></span>
                                    <div>
                                        <div class="sfe-metric-label">Tagged field evaluators</div>
                                        <div class="sfe-metric-value"><?= count($taggedEvaluators); ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card sfe-card mb-3">
                    <div class="card-body">
                        <div class="d-flex align-items-start mb-3">
                            <span class="sfe-step">2</span>
                            <div class="ml-3">
                                <h4 class="mb-1">Tag a field evaluator</h4>
                                <p class="text-muted mb-0">Pick an evaluator account to give oversight of this vacancy.</p>
                            </div>
                        </div>

                        <form method="post" action="<?= base_url('secretariat/field-evaluators/tag'); ?>">
                            <input type="hidden" name="job_id" value="<?= (int) $selectedVacancy->jobID; ?>">
                            <div class="form-row align-items-end">
                                <div class="col-lg-9 col-md-8">
                                    <label for="evaluator_id" class="font-weight-bold">Evaluator account</label>
                                    <select name="evaluator_id" id="evaluator_id" class="form-control sfe-evaluator-select" required>
                                        <option value="">Select an evaluator...</option>
                                        <?php foreach ($evaluators as $evaluator) : ?>
                                            <?php if (isset($alreadyTagged[(int) $evaluator->id])) { continue; } ?>
                                            <option value="<?= (int) $evaluator->id; ?>"><?= $fe_h($evaluatorLabel($evaluator)); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-lg-3 col-md-4 mt-2 mt-md-0">
                                    <button type="submit" class="btn btn-success btn-block font-weight-bold">
                                        <i class="mdi mdi-plus mr-1"></i> Tag as Field Evaluator
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card sfe-card">
                    <div class="card-body">
                        <div class="d-flex align-items-start mb-3">
                            <span class="sfe-step">3</span>
                            <div class="ml-3">
                                <h4 class="mb-1">Field evaluators for this vacancy</h4>
                                <p class="text-muted mb-0">These accounts can open the full applicant list of this vacancy.</p>
                            </div>
                        </div>

                        <?php if (empty($taggedEvaluators)) : ?>
                            <div class="sfe-empty">
                                <div class="sfe-empty-icon mb-3"><i class="mdi mdi-account-eye-outline"></i></div>
                                <h5 class="mb-1">No field evaluator yet</h5>
                                <p class="text-muted mb-0">Tag an evaluator above to give them oversight of this vacancy.</p>
                            </div>
                        <?php else : ?>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Field evaluator</th>
                                            <th>Username</th>
                                            <th class="text-center">Applicants tagged to them here</th>
                                            <th>Tagged on</th>
                                            <th class="text-right">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($taggedEvaluators as $tag) : ?>
                                            <?php
                                            $name = trim((string) $tag->evaluator_name);
                                            if ($name === '') {
                                                $name = (string) $tag->username;
                                            }
                                            ?>
                                            <tr>
                                                <td><span class="sfe-name"><?= $fe_h($name); ?></span></td>
                                                <td><span class="sfe-sub"><?= $fe_h($tag->username); ?></span></td>
                                                <td class="text-center"><?= (int) $tag->assigned_total; ?></td>
                                                <td>
                                                    <span class="sfe-sub">
                                                        <?= !empty($tag->created_at) ? $fe_h(date('M j, Y g:i A', strtotime((string) $tag->created_at))) : '&mdash;'; ?>
                                                    </span>
                                                </td>
                                                <td class="text-right">
                                                    <form method="post" action="<?= base_url('secretariat/field-evaluators/untag'); ?>"
                                                          onsubmit="return confirm('Remove <?= $fe_h(addslashes($name)); ?> as Field Evaluator of this vacancy?');">
                                                        <input type="hidden" name="job_id" value="<?= (int) $selectedVacancy->jobID; ?>">
                                                        <input type="hidden" name="evaluator_id" value="<?= (int) $tag->evaluator_user_id; ?>">
                                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                                            <i class="mdi mdi-account-remove-outline mr-1"></i> Remove tag
                                                        </button>
                                                    </form>
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
    // jQuery and select2 are loaded by templates/footer.php, i.e. after this
    // view, so the picker is upgraded once the page has finished loading.
    (function () {
        function initEvaluatorPicker() {
            if (!window.jQuery || !jQuery.fn || !jQuery.fn.select2) return;

            jQuery('.sfe-evaluator-select').each(function () {
                var $sel = jQuery(this);
                if ($sel.data('select2')) return;
                $sel.select2({ width: '100%', placeholder: 'Select an evaluator...' });
            });
        }

        if (document.readyState === 'complete') {
            initEvaluatorPicker();
        } else {
            window.addEventListener('load', initEvaluatorPicker);
        }
    })();
</script>
