<?php
function h($v) { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); }

$selectedId    = (int) ($selected_id ?? 0);
$assignments   = $assignments ?? [];
$catalog       = $scope_catalog ?? [];
$levelLabels   = $job_types ?? [];
$openCounts    = $vacancy_counts ?? [];
$active        = $assignments[$selectedId] ?? [];

$selected = null;
foreach ($secretariats as $sec) {
    if ((int) $sec->id === $selectedId) {
        $selected = $sec;
        break;
    }
}

// hero counters
$totalSecretariats = count($secretariats);
$withCoverage      = 0;
foreach ($secretariats as $sec) {
    if (!empty($assignments[(int) $sec->id])) {
        $withCoverage++;
    }
}
$withoutCoverage = $totalSecretariats - $withCoverage;
$openVacancies   = array_sum($openCounts);

// open vacancies per position group, for the group headers
$groupOpenCounts = [];
foreach ($openCounts as $key => $total) {
    [$g] = explode(':', $key, 2);
    $groupOpenCounts[(int) $g] = ($groupOpenCounts[(int) $g] ?? 0) + $total;
}

$scopeText = function ($group, $type) use ($catalog, $levelLabels) {
    $groupName = $catalog[$group]['label'] ?? ('Group ' . $group);
    if ((int) $type === 0) {
        return $groupName;
    }
    return $groupName . ' - ' . ($levelLabels[$type] ?? ('Level ' . $type));
};
?>

<?php $this->load->view('includes/hr_recruitment_styles'); ?>
<style>
    /* scope picker ------------------------------------------------------- */
    .sa-group { border: 1px solid #e9edf2; border-radius: 12px; margin-bottom: .85rem; background: #fff; overflow: hidden; }
    .sa-group-head {
        display: flex;
        align-items: center;
        gap: .7rem;
        padding: .7rem .9rem;
        background: #f8fafc;
        border-bottom: 1px solid #eef1f6;
    }
    .sa-group-icon {
        width: 34px; height: 34px;
        border-radius: 9px;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 1.15rem;
        flex: 0 0 auto;
    }
    .sa-icon-blue   { background: #e8f0fb; color: #2c5282; }
    .sa-icon-purple { background: #f0ebfb; color: #5b3fb0; }
    .sa-icon-teal   { background: #e3f4f2; color: #0f766e; }
    .sa-icon-amber  { background: #fdf3e2; color: #a86c14; }
    .sa-group-text { min-width: 0; flex: 1 1 auto; line-height: 1.3; }
    .sa-group-name { font-weight: 600; color: #313a46; font-size: .92rem; }
    .sa-group-sub  { font-size: .73rem; color: #98a6ad; }
    .sa-group-tools { display: flex; align-items: center; gap: .35rem; flex: 0 0 auto; }
    .sa-count-pill {
        background: #eef2f8; color: #5c6873;
        border-radius: 999px; padding: .1rem .5rem;
        font-size: .7rem; font-weight: 600; white-space: nowrap;
    }
    .sa-count-pill.is-on { background: #2c5282; color: #fff; }

    .sa-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(232px, 1fr)); gap: .4rem; padding: .7rem .9rem .9rem; }
    .sa-grid.is-single { grid-template-columns: 1fr; }

    .sa-tile { position: relative; margin: 0; }
    .sa-tile input { position: absolute; opacity: 0; width: 0; height: 0; }
    .sa-tile-face {
        display: flex; align-items: center; gap: .55rem;
        border: 1px solid #e9edf2; border-radius: 9px;
        padding: .48rem .6rem;
        cursor: pointer;
        transition: all .12s ease-in-out;
        background: #fff;
        height: 100%;
    }
    .sa-tile-face:hover { border-color: #cfdcee; background: #f8fafd; }
    .sa-tile-box {
        width: 17px; height: 17px; flex: 0 0 auto;
        border: 1.5px solid #cbd4df; border-radius: 5px;
        display: inline-flex; align-items: center; justify-content: center;
        color: transparent; font-size: .8rem; line-height: 1;
        background: #fff;
    }
    .sa-tile-label { font-size: .82rem; color: #5c6873; line-height: 1.25; min-width: 0; }
    .sa-tile-jobs { display: block; font-size: .68rem; color: #b0bac4; }
    .sa-tile input:checked + .sa-tile-face { border-color: #2c5282; background: #eff5fc; box-shadow: inset 0 0 0 1px #2c5282; }
    .sa-tile input:checked + .sa-tile-face .sa-tile-box { background: #2c5282; border-color: #2c5282; color: #fff; }
    .sa-tile input:checked + .sa-tile-face .sa-tile-label { color: #22405f; font-weight: 600; }
    .sa-tile input:focus-visible + .sa-tile-face { border-color: #2c5282; }
    .sa-tile.is-hidden { display: none; }
    .sa-tile-face .sa-tile-open {
        margin-left: auto; flex: 0 0 auto;
        background: #e6f4ec; color: #1e7d44;
        border-radius: 999px; padding: .05rem .42rem;
        font-size: .66rem; font-weight: 600;
    }

    .sa-savebar {
        position: sticky; bottom: 0;
        display: flex; align-items: center; gap: .6rem; flex-wrap: wrap;
        background: #fff;
        border-top: 1px solid #eef1f6;
        padding: .8rem 0 .2rem;
        margin-top: .2rem;
    }
    .sa-savebar-note { font-size: .8rem; color: #98a6ad; margin-left: auto; }

    .sa-search { position: relative; flex: 1 1 200px; min-width: 180px; }
    .sa-search i { position: absolute; left: .6rem; top: 50%; transform: translateY(-50%); color: #b0bac4; font-size: .95rem; }
    .sa-search input {
        width: 100%;
        border: 1px solid #e9edf2; border-radius: 8px;
        padding: .4rem .6rem .4rem 1.9rem;
        font-size: .84rem; color: #313a46;
    }
    .sa-search input:focus { outline: none; border-color: #2c5282; box-shadow: 0 0 0 3px rgba(44,82,130,.08); }

    .sa-person {
        display: flex; align-items: center; gap: .6rem;
        padding: .55rem .7rem;
        border: 1px solid #e9edf2; border-radius: 10px;
        background: #f8fafc; margin-bottom: .9rem;
    }
    .sa-person .hrp-avatar { flex: 0 0 auto; }
    .sa-chips { display: flex; flex-wrap: wrap; gap: .25rem; }
    .sa-row-unassigned td { background: #fffdf6; }
    .sa-name-link { color: #313a46; font-weight: 600; }
    .sa-name-link:hover { color: #2c5282; text-decoration: none; }
    .sa-empty-groups { display: none; text-align: center; color: #98a6ad; font-size: .84rem; padding: .8rem 0; }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            <!-- start page header -->
            <div class="row">
                <div class="col-12">
                    <div class="hrp-hero">
                        <div class="hrp-hero-text">
                            <span class="hrp-hero-eyebrow"><i class="mdi mdi-account-key-outline"></i> Recruitment</span>
                            <h3 class="hrp-hero-title"><i class="mdi mdi-account-multiple-check-outline"></i> <?= h($title ?? 'Assign Secretariat Coverage'); ?></h3>
                            <p class="hrp-hero-sub">
                                Coverage follows how a vacancy is posted on
                                <a href="<?= base_url(); ?>Page/jobVacancy" style="color:#fff;text-decoration:underline;">Job Vacancies</a>
                                &mdash; position group first, then group type. Related Teaching and Non-Teaching are posted
                                without a group type, so they are assigned as a whole group.
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
                                <span class="hrp-stat-label">Open Posts</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end page header -->

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
                                <p class="hrp-card-sub">Tick every position group and group type this account should handle.</p>
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
                                        &mdash; <?= $count ? $count . ' assigned' : 'no coverage'; ?>
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
                                    <?= count($active); ?> selected
                                </span>
                            </div>
                        <?php endif; ?>

                        <div class="hrp-toolbar mb-2">
                            <div class="sa-search">
                                <i class="mdi mdi-magnify"></i>
                                <input type="text" id="sa-filter" placeholder="Filter group types..." autocomplete="off">
                            </div>
                            <button type="button" class="hrp-btn hrp-btn-sm" data-sa-all="1">
                                <i class="mdi mdi-checkbox-multiple-marked-outline"></i> Select all
                            </button>
                            <button type="button" class="hrp-btn hrp-btn-sm" data-sa-all="0">
                                <i class="mdi mdi-checkbox-multiple-blank-outline"></i> Clear all
                            </button>
                        </div>

                        <?php foreach ($catalog as $groupId => $group) : ?>
                            <?php
                                $groupTypes    = $group['job_types'];
                                $groupSelected = 0;
                                foreach ($groupTypes as $type) {
                                    if (in_array($groupId . ':' . $type, $active, true)) {
                                        $groupSelected++;
                                    }
                                }
                                $isWholeGroup = (count($groupTypes) === 1 && (int) $groupTypes[0] === 0);
                            ?>
                            <div class="sa-group" data-sa-group="<?= (int) $groupId; ?>">
                                <div class="sa-group-head">
                                    <span class="sa-group-icon sa-icon-<?= h($group['tone']); ?>"><i class="mdi <?= h($group['icon']); ?>"></i></span>
                                    <div class="sa-group-text">
                                        <div class="sa-group-name"><?= h($group['label']); ?></div>
                                        <div class="sa-group-sub"><?= h($group['sub']); ?></div>
                                    </div>
                                    <div class="sa-group-tools">
                                        <span class="sa-count-pill" title="Open vacancies posted under this group">
                                            <i class="mdi mdi-briefcase-outline"></i> <?= number_format($groupOpenCounts[$groupId] ?? 0); ?> open
                                        </span>
                                        <span class="sa-count-pill sa-group-pill <?= $groupSelected ? 'is-on' : ''; ?>">
                                            <?= $groupSelected; ?>/<?= count($groupTypes); ?>
                                        </span>
                                        <?php if (!$isWholeGroup) : ?>
                                            <button type="button" class="hrp-btn hrp-btn-sm" data-sa-group-all="1" title="Select all in this group">
                                                <i class="mdi mdi-check-all"></i>
                                            </button>
                                            <button type="button" class="hrp-btn hrp-btn-sm" data-sa-group-all="0" title="Clear this group">
                                                <i class="mdi mdi-close"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="sa-grid <?= $isWholeGroup ? 'is-single' : ''; ?>">
                                    <?php foreach ($groupTypes as $type) : ?>
                                        <?php
                                            $key    = $groupId . ':' . $type;
                                            $domId  = 'sc-' . $groupId . '-' . $type;
                                            $label  = ((int) $type === 0)
                                                ? 'All ' . $group['label'] . ' vacancies'
                                                : ($levelLabels[$type] ?? ('Level ' . $type));
                                            $isOn   = in_array($key, $active, true);
                                            $nOpen  = $openCounts[$key] ?? 0;
                                        ?>
                                        <label class="sa-tile" data-sa-text="<?= h(strtolower($label)); ?>">
                                            <input type="checkbox" name="scopes[]" id="<?= h($domId); ?>"
                                                   value="<?= h($key); ?>" <?= $isOn ? 'checked' : ''; ?>>
                                            <span class="sa-tile-face">
                                                <span class="sa-tile-box"><i class="mdi mdi-check"></i></span>
                                                <span class="sa-tile-label">
                                                    <?= h($label); ?>
                                                    <?php if ((int) $type !== 0) : ?>
                                                        <span class="sa-tile-jobs">Group type <?= (int) $type; ?></span>
                                                    <?php endif; ?>
                                                </span>
                                                <?php if ($nOpen > 0) : ?>
                                                    <span class="sa-tile-open" title="<?= (int) $nOpen; ?> open vacancy(ies)"><?= (int) $nOpen; ?></span>
                                                <?php endif; ?>
                                            </span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <div class="sa-empty-groups" id="sa-no-match">
                            <i class="mdi mdi-magnify-close"></i> No group type matches that filter.
                        </div>

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
                                        <th>Covers</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($secretariats as $sec) : ?>
                                        <?php
                                            $keys = $assignments[(int) $sec->id] ?? [];
                                            $byGroup = [];
                                            foreach ($keys as $key) {
                                                [$g, $t] = array_map('intval', explode(':', $key, 2));
                                                $byGroup[$g][] = $t;
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
                                                <?php if (empty($byGroup)) : ?>
                                                    <span class="hrp-chip hrp-chip-amber"><i class="mdi mdi-alert-outline"></i> Not assigned</span>
                                                <?php else : ?>
                                                    <div class="sa-chips">
                                                        <?php foreach ($byGroup as $g => $types) : ?>
                                                            <?php
                                                                $tone      = $catalog[$g]['tone'] ?? 'grey';
                                                                $groupName = $catalog[$g]['label'] ?? ('Group ' . $g);
                                                                $whole     = in_array(0, $types, true);
                                                                $names     = [];
                                                                foreach ($types as $t) {
                                                                    if ($t === 0) { continue; }
                                                                    $names[] = $levelLabels[$t] ?? ('Level ' . $t);
                                                                }
                                                                $tip = $whole ? $groupName . ' (all)' : $groupName . ': ' . implode(', ', $names);
                                                            ?>
                                                            <span class="hrp-chip hrp-chip-<?= h($tone); ?>" title="<?= h($tip); ?>">
                                                                <i class="mdi <?= h($catalog[$g]['icon'] ?? 'mdi-tag-outline'); ?>"></i>
                                                                <?= h($groupName); ?>
                                                                <?php if (!$whole) : ?>
                                                                    <span style="opacity:.7">&middot; <?= count($names); ?></span>
                                                                <?php endif; ?>
                                                            </span>
                                                        <?php endforeach; ?>
                                                    </div>
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
                                <?php foreach ($active as $key) : ?>
                                    <?php [$g, $t] = array_map('intval', explode(':', $key, 2)); ?>
                                    <span class="hrp-chip hrp-chip-<?= h($catalog[$g]['tone'] ?? 'grey'); ?>"><?= h($scopeText($g, $t)); ?></span>
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

        function refreshCounts() {
            var total = 0;

            $form.find('.sa-group').each(function () {
                var $group = $(this),
                    $boxes = $group.find('input[name="scopes[]"]'),
                    on     = $boxes.filter(':checked').length;

                total += on;
                $group.find('.sa-group-pill')
                    .text(on + '/' + $boxes.length)
                    .toggleClass('is-on', on > 0);
            });

            $('#sa-total-pill').text(total + ' selected').toggleClass('is-on', total > 0);
        }

        $form.on('change', 'input[name="scopes[]"]', refreshCounts);

        // global select / clear all - only touches rows the filter left visible
        $form.on('click', '[data-sa-all]', function () {
            var on = $(this).data('sa-all') === 1;
            $form.find('.sa-tile:not(.is-hidden) input[name="scopes[]"]').prop('checked', on);
            refreshCounts();
        });

        // per group select / clear
        $form.on('click', '[data-sa-group-all]', function () {
            var on = $(this).data('sa-group-all') === 1;
            $(this).closest('.sa-group')
                .find('.sa-tile:not(.is-hidden) input[name="scopes[]"]')
                .prop('checked', on);
            refreshCounts();
        });

        // filter group types by name
        $('#sa-filter').on('input', function () {
            var q = $.trim(this.value).toLowerCase(),
                anyVisible = false;

            $form.find('.sa-group').each(function () {
                var $group = $(this),
                    shown  = 0;

                $group.find('.sa-tile').each(function () {
                    var match = q === '' || String($(this).data('sa-text')).indexOf(q) !== -1;
                    $(this).toggleClass('is-hidden', !match);
                    if (match) { shown++; }
                });

                $group.toggle(shown > 0);
                if (shown > 0) { anyVisible = true; }
            });

            $('#sa-no-match').toggle(!anyVisible);
        });

        refreshCounts();
    });
</script>
