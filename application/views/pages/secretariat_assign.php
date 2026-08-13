<?php
function h($v) { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); }

$selectedId     = (int) ($selected_id ?? 0);
$assignments    = $assignments ?? [];
$vacancies      = $vacancies ?? [];
$jobTypeLabels  = $job_types ?? [];
$positionGroups = $position_groups ?? [];
$active         = $assignments[$selectedId] ?? [];

$selected = null;
foreach ($secretariats as $sec) {
    if ((int) $sec->id === $selectedId) {
        $selected = $sec;
        break;
    }
}

$totalSecretariats = count($secretariats);
$withCoverage      = 0;
foreach ($secretariats as $sec) {
    if (!empty($assignments[(int) $sec->id])) {
        $withCoverage++;
    }
}
$withoutCoverage = $totalSecretariats - $withCoverage;
$openVacancies   = count($vacancies);

$jobTitleMap = [];
foreach ($vacancies as $job) {
    $jobTitleMap[(int) $job->jobID] = $job->jobTitle;
}

// Group open vacancies by position group and job type for display headers
$grouped = [];
foreach ($vacancies as $job) {
    $g = (int) $job->position;
    $t = (int) $job->job_type;
    $grouped[$g][$t][] = $job;
}
?>

<?php $this->load->view('includes/hr_recruitment_styles'); ?>
<style>
    .sa-person {
        display: flex; align-items: center; gap: .6rem;
        padding: .55rem .7rem;
        border: 1px solid #e9edf2; border-radius: 10px;
        background: #f8fafc; margin-bottom: .9rem;
    }
    .sa-person .hrp-avatar { flex: 0 0 auto; }
    .sa-count-pill {
        background: #eef2f8; color: #5c6873;
        border-radius: 999px; padding: .1rem .5rem;
        font-size: .7rem; font-weight: 600; white-space: nowrap;
    }
    .sa-count-pill.is-on { background: #2c5282; color: #fff; }

    .sa-search { position: relative; flex: 1 1 200px; min-width: 180px; }
    .sa-search i { position: absolute; left: .6rem; top: 50%; transform: translateY(-50%); color: #b0bac4; font-size: .95rem; }
    .sa-search input {
        width: 100%;
        border: 1px solid #e9edf2; border-radius: 8px;
        padding: .4rem .6rem .4rem 1.9rem;
        font-size: .84rem; color: #313a46;
    }
    .sa-search input:focus { outline: none; border-color: #2c5282; box-shadow: 0 0 0 3px rgba(44,82,130,.08); }

    .sa-savebar {
        position: sticky; bottom: 0;
        display: flex; align-items: center; gap: .6rem; flex-wrap: wrap;
        background: #fff;
        border-top: 1px solid #eef1f6;
        padding: .8rem 0 .2rem;
        margin-top: .2rem;
    }
    .sa-savebar-note { font-size: .8rem; color: #98a6ad; margin-left: auto; }

    .sa-group-h { background: #f8fafc; padding: .55rem .75rem; font-weight: 600; color: #313a46; border-radius: 8px 8px 0 0; border: 1px solid #e9edf2; border-bottom: 0; }
    .sa-type-h { background: #fff; padding: .4rem .75rem; font-size: .82rem; color: #5c6873; border-left: 1px solid #e9edf2; border-right: 1px solid #e9edf2; }
    .sa-table-wrap { border: 1px solid #e9edf2; border-radius: 0 0 8px 8px; overflow: hidden; }
    .sa-table-wrap table { margin-bottom: 0; }
    .sa-table-wrap th { background: #f8fafc; font-size: .78rem; color: #5c6873; font-weight: 600; white-space: nowrap; }
    .sa-table-wrap td { font-size: .82rem; color: #313a46; vertical-align: middle; }
    .sa-table-wrap tr.is-hidden { display: none; }
    .sa-empty { display: none; text-align: center; color: #98a6ad; font-size: .84rem; padding: 1rem; }
    .sa-row-unassigned td { background: #fffdf6; }
    .sa-chips { display: flex; flex-wrap: wrap; gap: .25rem; }
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
                                Coverage is tagged to any available job vacancy from
                                <a href="<?= base_url(); ?>Page/jobVacancy" style="color:#fff;text-decoration:underline;">Job Vacancies</a>.
                                Archived (Closed) vacancies are removed automatically.
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
                                <span class="hrp-stat-value"><?= number_format($openVacancies); ?></span>
                                <span class="hrp-stat-label">Available Posts</span>
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
                        <span class="hrp-muted">Create one under Manage Users, then come back to set its coverage.</span>
                    </div>
                </div>
            <?php else : ?>

            <div class="row">
                <div class="col-xl-8">
                    <div class="hrp-card">
                        <div class="hrp-card-head">
                            <div>
                                <h5 class="hrp-card-title">Coverage for this Secretariat</h5>
                                <p class="hrp-card-sub">Tick the available vacancies this account should handle.</p>
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
                                <span class="sa-count-pill" id="sa-total-pill" title="Open vacancies assigned to this account">
                                    0 tagged
                                </span>
                            </div>
                        <?php endif; ?>

                        <div class="hrp-toolbar mb-2">
                            <div class="sa-search">
                                <i class="mdi mdi-magnify"></i>
                                <input type="text" id="sa-filter" placeholder="Filter job titles, groups or types..." autocomplete="off">
                            </div>
                            <button type="button" class="hrp-btn hrp-btn-sm" data-sa-all="1">
                                <i class="mdi mdi-checkbox-multiple-marked-outline"></i> Select all
                            </button>
                            <button type="button" class="hrp-btn hrp-btn-sm" data-sa-all="0">
                                <i class="mdi mdi-checkbox-multiple-blank-outline"></i> Clear all
                            </button>
                        </div>

                        <?php if (empty($vacancies)) : ?>
                            <div class="alert alert-light text-center text-muted" role="alert">
                                <i class="mdi mdi-briefcase-off-outline"></i> No available job vacancies at the moment.
                            </div>
                        <?php else : ?>
                            <div class="sa-table-wrap">
                                <table class="table table-sm" id="sa-vacancy-table">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="width:45px;">Tag</th>
                                            <th>Group</th>
                                            <th>Job Type</th>
                                            <th>Job Title</th>
                                            <th>Emp. Type</th>
                                            <th>S.Y.</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($grouped as $groupId => $types) : ?>
                                            <tr>
                                                <td class="sa-group-h" colspan="6">
                                                    <?= h($positionGroups[$groupId] ?? ('Group ' . $groupId)); ?>
                                                </td>
                                            </tr>
                                            <?php foreach ($types as $typeId => $jobs) : ?>
                                                <tr>
                                                    <td class="sa-type-h" colspan="6">
                                                        <?= h($jobTypeLabels[$typeId] ?? ('Level ' . $typeId)); ?> (<?= count($jobs); ?>)
                                                    </td>
                                                </tr>
                                                <?php foreach ($jobs as $job) : ?>
                                                    <?php
                                                        $isOn = in_array((int) $job->jobID, $active, true);
                                                        $searchText = strtolower(
                                                            ($job->jobTitle ?? '') . ' ' .
                                                            ($positionGroups[$groupId] ?? '') . ' ' .
                                                            ($jobTypeLabels[$typeId] ?? '') . ' ' .
                                                            ($job->empType ?? '')
                                                        );
                                                    ?>
                                                    <tr data-sa-text="<?= h($searchText); ?>">
                                                        <td class="text-center">
                                                            <input type="checkbox" name="job_ids[]" value="<?= (int) $job->jobID; ?>" <?= $isOn ? 'checked' : ''; ?>>
                                                        </td>
                                                        <td><?= h($positionGroups[$groupId] ?? ('Group ' . $groupId)); ?></td>
                                                        <td><?= h($jobTypeLabels[$typeId] ?? ('Level ' . $typeId)); ?></td>
                                                        <td><?= h($job->jobTitle); ?></td>
                                                        <td><?= h($job->empType); ?></td>
                                                        <td><?= h($job->sy); ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endforeach; ?>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                                <div class="sa-empty" id="sa-no-match">
                                    <i class="mdi mdi-magnify-close"></i> No vacancy matches that filter.
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="sa-savebar">
                            <button type="submit" class="hrp-btn hrp-btn-primary">
                                <i class="mdi mdi-content-save-outline"></i> Save Coverage
                            </button>
                            <a href="<?= base_url('SecretariatAssign?id=' . $selectedId); ?>" class="hrp-btn">
                                <i class="mdi mdi-undo-variant"></i> Reset
                            </a>
                            <span class="sa-savebar-note">
                                Saving replaces this account's whole coverage list.
                            </span>
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
                                    <p class="hrp-card-sub">What this account can see right now.</p>
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
        }

        function refreshCount() {
            var total = $form.find('input[name="job_ids[]"]:checked').length;
            $('#sa-total-pill')
                .text(total + ' tagged')
                .toggleClass('is-on', total > 0);
        }

        $form.on('change', 'input[name="job_ids[]"]', refreshCount);

        $form.on('click', '[data-sa-all]', function () {
            var on = $(this).data('sa-all') === 1;
            $form.find('#sa-vacancy-table tbody tr:not(.is-hidden) input[name="job_ids[]"]').prop('checked', on);
            refreshCount();
        });

        $('#sa-filter').on('input', function () {
            var q = $.trim(this.value).toLowerCase(),
                any = false;

            $('#sa-vacancy-table tbody tr').each(function () {
                if ($(this).has('td.sa-group-h, td.sa-type-h').length) {
                    return;
                }
                var match = q === '' || String($(this).data('sa-text')).indexOf(q) !== -1;
                $(this).toggleClass('is-hidden', !match);
                if (match) { any = true; }
            });

            $('#sa-no-match').toggle(!any);
            refreshCount();
        });

        refreshCount();
    });
</script>
