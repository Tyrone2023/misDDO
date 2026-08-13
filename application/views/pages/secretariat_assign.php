<?php
function h($v) { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); }

$selectedId    = (int) ($selected_id ?? 0);
$assignments   = $assignments ?? [];
$vacancies     = $vacancies ?? [];
$positionGroups = $position_groups ?? [];
$jobTypeLabels  = $job_types ?? [];
$active         = $assignments[$selectedId] ?? [];

$selected = null;
foreach ($secretariats as $sec) {
    if ((int) $sec->id === $selectedId) {
        $selected = $sec;
        break;
    }
}

$totalSecretariats = count($secretariats);
$withCoverage = 0;
foreach ($secretariats as $sec) {
    if (!empty($assignments[(int) $sec->id])) {
        $withCoverage++;
    }
}
$withoutCoverage = $totalSecretariats - $withCoverage;
$totalVacancies  = count($vacancies);

$jobTitleMap = [];
foreach ($vacancies as $job) {
    $jobTitleMap[(int) $job->jobID] = $job->jobTitle;
}
?>

<?php $this->load->view('includes/hr_recruitment_styles'); ?>
<style>
    .sa-person {
        display: flex;
        align-items: center;
        gap: .6rem;
        padding: .65rem .75rem;
        border: 1px solid #e9edf2;
        border-radius: 10px;
        background: #f8fafc;
        margin-bottom: 1rem;
    }
    .sa-person .hrp-avatar { flex: 0 0 auto; }
    .sa-count-pill {
        margin-left: auto;
        background: #eef2f8;
        color: #5c6873;
        border-radius: 999px;
        padding: .15rem .55rem;
        font-size: .72rem;
        font-weight: 600;
        white-space: nowrap;
    }
    .sa-count-pill.is-on { background: #2c5282; color: #fff; }

    .sa-position-picker .select2-container { width: 100% !important; }
    .sa-position-picker .select2-container--default .select2-selection--multiple {
        min-height: 46px;
        border: 1px solid #dce3eb;
        border-radius: 9px;
        padding: 2px 4px;
    }
    .sa-position-picker .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: #2c5282;
        box-shadow: 0 0 0 3px rgba(44, 82, 130, .09);
    }
    .sa-position-picker .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background: #eef4fb;
        border: 1px solid #d5e1ef;
        border-radius: 6px;
        color: #294e73;
        font-size: .78rem;
        margin-top: 7px;
        padding: 3px 8px;
    }
    .sa-position-picker .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: #6f8398;
        margin-right: 5px;
    }
    .sa-position-picker .select2-search--inline .select2-search__field { margin-top: 10px; }
    .sa-picker-help {
        display: block;
        margin-top: .4rem;
        color: #8795a4;
        font-size: .78rem;
    }

    .select2-results__group {
        color: #2c5282;
        font-size: .75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .035em;
    }
    .select2-results__option[role="treeitem"] { padding-left: 1.25rem; }

    .sa-savebar {
        position: sticky;
        bottom: 0;
        display: flex;
        align-items: center;
        gap: .6rem;
        flex-wrap: wrap;
        background: #fff;
        border-top: 1px solid #eef1f6;
        padding: .85rem 0 .2rem;
        margin-top: .35rem;
    }
    .sa-savebar-note { font-size: .8rem; color: #98a6ad; margin-left: auto; }
    .sa-row-unassigned td { background: #fffdf6; }
    .sa-chips { display: flex; flex-wrap: wrap; gap: .3rem; }
    .sa-name-link { color: #313a46; font-weight: 600; }
    .sa-name-link:hover { color: #2c5282; text-decoration: none; }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="hrp-hero">
                        <div class="hrp-hero-text">
                            <span class="hrp-hero-eyebrow"><i class="mdi mdi-account-key-outline"></i> Recruitment</span>
                            <h3 class="hrp-hero-title"><i class="mdi mdi-account-multiple-check-outline"></i> <?= h($title ?? 'Assign Secretariat Coverage'); ?></h3>
                            <p class="hrp-hero-sub">
                                Tag a Secretariat account to specific job vacancies.
                                Search by job title, position group, or type to find the exact vacancy.
                            </p>
                        </div>
                        <div class="hrp-hero-stats">
                            <div class="hrp-stat">
                                <span class="hrp-stat-value"><?= number_format($totalSecretariats); ?></span>
                                <span class="hrp-stat-label">Secretariat</span>
                            </div>
                            <div class="hrp-stat">
                                <span class="hrp-stat-value"><?= number_format($withCoverage); ?></span>
                                <span class="hrp-stat-label">Assigned</span>
                            </div>
                            <div class="hrp-stat">
                                <span class="hrp-stat-value"><?= number_format($withoutCoverage); ?></span>
                                <span class="hrp-stat-label">Unassigned</span>
                            </div>
                            <div class="hrp-stat">
                                <span class="hrp-stat-value"><?= number_format($totalVacancies); ?></span>
                                <span class="hrp-stat-label">Vacancies</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($this->session->flashdata('success')) : ?>
                <div class="hrp-alert hrp-alert-success">
                    <i class="mdi mdi-check-circle-outline"></i>
                    <div><?= h($this->session->flashdata('success')); ?></div>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
            <?php endif; ?>

            <?php if ($this->session->flashdata('danger')) : ?>
                <div class="hrp-alert hrp-alert-danger">
                    <i class="mdi mdi-alert-circle-outline"></i>
                    <div><?= h($this->session->flashdata('danger')); ?></div>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
            <?php endif; ?>

            <?php if (empty($secretariats)) : ?>
                <div class="hrp-card">
                    <div class="hrp-empty">
                        <i class="mdi mdi-account-off-outline"></i>
                        No Secretariat accounts yet.<br>
                        <span class="hrp-muted">Create one under Manage Users, then return here to set its coverage.</span>
                    </div>
                </div>
            <?php else : ?>
                <div class="row">
                    <div class="col-xl-8">
                        <div class="hrp-card">
                            <div class="hrp-card-head">
                                <div>
                                    <h5 class="hrp-card-title">Coverage for this Secretariat</h5>
                                    <p class="hrp-card-sub">Search and select the exact vacancies this account should handle.</p>
                                </div>
                            </div>

                            <?= form_open('SecretariatAssign/save', ['id' => 'sa-form']); ?>
                            <input type="hidden" name="secretariat_id" value="<?= $selectedId; ?>">

                            <div class="hrp-field">
                                <label class="hrp-label" for="sa-secretariat">Secretariat Account <span class="hrp-req">*</span></label>
                                <select class="form-control" id="sa-secretariat"
                                        onchange="window.location='<?= base_url('SecretariatAssign?id='); ?>'+this.value;">
                                    <?php foreach ($secretariats as $sec) : ?>
                                        <?php $count = count($assignments[(int) $sec->id] ?? []); ?>
                                        <option value="<?= (int) $sec->id; ?>" <?= (int) $sec->id === $selectedId ? 'selected' : ''; ?>>
                                            <?= h("{$sec->lname}, {$sec->fname}"); ?> (<?= h($sec->username); ?>)
                                            &mdash; <?= $count ? $count . ' tagged' : 'no coverage'; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <?php if ($selected) : ?>
                                <div class="sa-person">
                                    <span class="hrp-avatar"><?= h(strtoupper(substr((string) $selected->fname, 0, 1) . substr((string) $selected->lname, 0, 1))); ?></span>
                                    <div class="hrp-title-text">
                                        <span class="hrp-title-name"><?= h("{$selected->fname} {$selected->lname}"); ?></span>
                                        <span class="hrp-title-sub"><i class="mdi mdi-account-outline"></i> <?= h($selected->username); ?></span>
                                    </div>
                                    <span class="sa-count-pill <?= !empty($active) ? 'is-on' : ''; ?>" id="sa-total-pill">
                                        <?= count($active); ?> tagged
                                    </span>
                                </div>
                            <?php endif; ?>

                            <div class="hrp-field sa-position-picker">
                                <label class="hrp-label" for="sa-vacancies">Job Vacancies</label>
                                <select class="form-control" id="sa-vacancies" name="job_ids[]" multiple>
                                    <?php
                                    $grouped = [];
                                    foreach ($vacancies as $job) {
                                        $g = (int) $job->position;
                                        $grouped[$g][] = $job;
                                    }
                                    foreach ($grouped as $groupId => $jobs) :
                                        $groupLabel = $positionGroups[$groupId] ?? ('Group ' . $groupId);
                                    ?>
                                        <optgroup label="<?= h($groupLabel); ?>">
                                            <?php foreach ($jobs as $job) :
                                                $typeLabel = $jobTypeLabels[(int) $job->job_type] ?? '';
                                                $optionText = $job->jobTitle;
                                                if ($typeLabel) {
                                                    $optionText .= ' — ' . $typeLabel;
                                                }
                                                if (!empty($job->empType)) {
                                                    $optionText .= ' (' . $job->empType . ')';
                                                }
                                                if (!empty($job->sy)) {
                                                    $optionText .= ' S.Y. ' . $job->sy;
                                                }
                                                $isOn = in_array((int) $job->jobID, $active, true);
                                            ?>
                                                <option value="<?= (int) $job->jobID; ?>" <?= $isOn ? 'selected' : ''; ?>>
                                                    <?= h($optionText); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </optgroup>
                                    <?php endforeach; ?>
                                </select>
                                <span class="sa-picker-help">
                                    <i class="mdi mdi-magnify"></i> Type to search for a specific vacancy. Options are grouped by position.
                                </span>
                            </div>

                            <div class="sa-savebar">
                                <button type="submit" class="hrp-btn hrp-btn-primary">
                                    <i class="mdi mdi-content-save-outline"></i> Save Coverage
                                </button>
                                <a href="<?= base_url('SecretariatAssign?id=' . $selectedId); ?>" class="hrp-btn">
                                    <i class="mdi mdi-undo-variant"></i> Reset
                                </a>
                                <span class="sa-savebar-note">Saving replaces this account's whole vacancy list.</span>
                            </div>

                            <?= form_close(); ?>
                        </div>
                    </div>

                    <div class="col-xl-4">
                        <div class="hrp-card">
                            <div class="hrp-card-head">
                                <div>
                                    <h5 class="hrp-card-title">Current Coverage</h5>
                                    <p class="hrp-card-sub"><?= number_format($withCoverage); ?> of <?= number_format($totalSecretariats); ?> accounts have coverage.</p>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table hrp-table mb-0">
                                    <thead>
                                        <tr>
                                            <th>Secretariat</th>
                                            <th>Tagged</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($secretariats as $sec) : ?>
                                            <?php
                                                $keys = $assignments[(int) $sec->id] ?? [];
                                                $titles = [];
                                                foreach ($keys as $jid) {
                                                    $titles[] = $jobTitleMap[$jid] ?? ('Vacancy ' . $jid);
                                                }
                                            ?>
                                            <tr class="<?= empty($keys) ? 'sa-row-unassigned' : ''; ?>">
                                                <td>
                                                    <div class="hrp-title-cell">
                                                        <span class="hrp-avatar"><?= h(strtoupper(substr((string) $sec->fname, 0, 1) . substr((string) $sec->lname, 0, 1))); ?></span>
                                                        <span class="hrp-title-text">
                                                            <a class="sa-name-link" href="<?= base_url('SecretariatAssign?id=' . (int) $sec->id); ?>"><?= h("{$sec->lname}, {$sec->fname}"); ?></a>
                                                            <span class="hrp-title-sub"><?= h($sec->username); ?></span>
                                                        </span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <?php if (empty($keys)) : ?>
                                                        <span class="hrp-chip hrp-chip-amber"><i class="mdi mdi-alert-outline"></i> Not assigned</span>
                                                    <?php else : ?>
                                                        <span class="hrp-chip hrp-chip-blue" title="<?= h(implode(', ', $titles)); ?>">
                                                            <i class="mdi mdi-tag-outline"></i> <?= count($keys); ?> tagged
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <?php if (!empty($active)) : ?>
                            <div class="hrp-card">
                                <div class="hrp-card-head">
                                    <div>
                                        <h5 class="hrp-card-title">Saved for <?= h($selected->fname ?? ''); ?></h5>
                                        <p class="hrp-card-sub">Vacancies this account can see right now.</p>
                                    </div>
                                </div>
                                <div class="sa-chips">
                                    <?php foreach ($active as $jid) : ?>
                                        <span class="hrp-chip hrp-chip-blue"><?= h($jobTitleMap[$jid] ?? ('Vacancy ' . $jid)); ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    jQuery(function ($) {
        var $form = $('#sa-form');
        if (!$form.length) { return; }

        if ($.fn.select2) {
            $('#sa-secretariat').select2({ width: '100%' });
            $('#sa-vacancies').select2({
                width: '100%',
                placeholder: 'Search and select job vacancies...',
                closeOnSelect: false,
                language: {
                    noResults: function () {
                        return 'No matching vacancy found.';
                    }
                }
            });
        }

        function refreshCount() {
            var total = ($('#sa-vacancies').val() || []).length;
            $('#sa-total-pill')
                .text(total + ' tagged')
                .toggleClass('is-on', total > 0);
        }

        $form.on('change', '#sa-vacancies', refreshCount);
        refreshCount();
    });
</script>
