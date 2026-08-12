<?php include('templates/head.php'); ?>
<?php include('templates/header.php'); ?>

<?php
/*
 * Scoring Criteria for one position title.
 *
 * This is the "Criteria and Point System for Hiring and Promotion" sheet put
 * on screen: the criteria this title is rated on and what each is worth (they
 * have to total 100), plus the increment-level table that turns an applicant's
 * actual qualification into points.
 *
 * The criteria list is not fixed - it can be renamed, added to and cut down per
 * position, because not every title is rated on the same things. Whatever ends
 * up in the Breakdown of Points is what gets a table in the section below it.
 *
 * Every criterion carries a form key ("c0" for one already on file, "n4" for one
 * added on screen) that its increment levels are posted under, so the two halves
 * stay tied together no matter how the list is edited before saving.
 */

$pid   = (int) $position_row->id;
$gid   = (int) $position_row->pos_id;
$gname = isset($groups[$gid]) ? $groups[$gid] : '';

$group_chip = array(1 => 'hrp-chip-blue', 2 => 'hrp-chip-purple', 3 => 'hrp-chip-amber', 4 => 'hrp-chip-green');

$total_points = 0;
$total_levels = 0;
foreach ($criteria as $c) {
    $total_points += (float) $c['max_points'];
    $total_levels += count($c['levels']);
}

if (!function_exists('hrp_num')) {
    // 5.00 -> "5", 2.50 -> "2.5"; the sheet is full of whole numbers and
    // trailing zeros make the tables hard to scan
    function hrp_num($value)
    {
        return rtrim(rtrim(number_format((float) $value, 2, '.', ''), '0'), '.');
    }
}
?>

<?php include('includes/hr_recruitment_styles.php'); ?>

<style>
    .hrc-alloc { width: 100%; border-collapse: separate; border-spacing: 0; }
    .hrc-alloc thead th {
        background: #f6f8fb; border-bottom: 1px solid #e9edf2; color: #5c6873;
        font-size: .72rem; text-transform: uppercase; letter-spacing: .05em;
        font-weight: 600; padding: .6rem .75rem;
    }
    .hrc-alloc tbody td { border-bottom: 1px solid #f0f3f7; padding: .35rem .5rem; vertical-align: middle; font-size: .87rem; }
    .hrc-alloc tbody tr:hover { background: #f9fbfd; }
    .hrc-alloc tfoot td { padding: .65rem .75rem; font-weight: 600; border-top: 2px solid #e9edf2; }
    .hrc-alloc .form-control { padding: .34rem .6rem; font-size: .86rem; border-radius: 8px; border: 1px solid #dfe5ee; }
    .hrc-alloc .form-control:focus { border-color: #2c5282; box-shadow: 0 0 0 .13rem rgba(44,82,130,.13); }
    .hrc-letter {
        display: inline-flex; align-items: center; justify-content: center;
        width: 22px; height: 22px; border-radius: 6px; background: #eef2f8;
        color: #5c6873; font-size: .72rem; font-weight: 700;
    }
    .hrc-label-cell { display: flex; align-items: center; gap: .55rem; }
    .hrc-pts { width: 104px; text-align: center; }
    .hrc-lvl { width: 78px; text-align: center; }
    .hrc-num { text-align: center; font-variant-numeric: tabular-nums; }
    .hrc-del-cell { width: 44px; text-align: center; }

    .hrc-total-pill {
        display: inline-flex; align-items: center; gap: .4rem; padding: .3rem .75rem;
        border-radius: 999px; font-size: .82rem; font-weight: 600; border: 1px solid transparent;
    }
    .hrc-total-ok   { background: #e6f4ec; border-color: #d2ebdd; color: #176034; }
    .hrc-total-bad  { background: #fdf3e2; border-color: #f7e5c6; color: #85560f; }

    .hrc-bar {
        position: sticky; bottom: 0; z-index: 20;
        display: flex; align-items: center; gap: .75rem; flex-wrap: wrap;
        background: #fff; border: 1px solid #e9edf2; border-radius: 12px;
        padding: .7rem 1rem; margin-top: 1rem;
        box-shadow: 0 -6px 18px rgba(16,30,54,.07);
    }
    .hrc-bar .hrc-bar-spacer { margin-left: auto; }

    .hrc-crit { border: 1px solid #e9edf2; border-radius: 12px; margin-bottom: .9rem; background: #fff; overflow: hidden; }
    .hrc-crit-head {
        display: flex; align-items: center; gap: .6rem; flex-wrap: wrap;
        padding: .7rem .95rem; background: #f8fafc; border-bottom: 1px solid #eef1f6; cursor: pointer;
    }
    .hrc-crit-head h6 { margin: 0; font-size: .92rem; font-weight: 600; color: #313a46; }
    .hrc-crit-head h6.is-unnamed { color: #98a6ad; font-style: italic; font-weight: 500; }
    .hrc-crit-head .hrc-caret { margin-left: auto; color: #98a6ad; transition: transform .15s ease; }
    .hrc-crit.is-open .hrc-crit-head .hrc-caret { transform: rotate(180deg); }
    .hrc-crit-body { padding: .9rem .95rem; display: none; }
    .hrc-crit.is-open .hrc-crit-body { display: block; }

    .hrc-levels { width: 100%; border-collapse: separate; border-spacing: 0; }
    .hrc-levels thead th {
        background: #f6f8fb; border-bottom: 1px solid #e9edf2; color: #5c6873;
        font-size: .7rem; text-transform: uppercase; letter-spacing: .05em;
        font-weight: 600; padding: .5rem .6rem; text-align: left;
    }
    .hrc-levels tbody td { border-bottom: 1px solid #f2f5f9; padding: .3rem .35rem; vertical-align: middle; }
    .hrc-levels .form-control { padding: .32rem .6rem; font-size: .84rem; border-radius: 8px; border: 1px solid #dfe5ee; }
    .hrc-levels .form-control:focus { border-color: #2c5282; box-shadow: 0 0 0 .13rem rgba(44,82,130,.13); }
    .hrc-levels tbody tr:last-child td { border-bottom: none; }
    .hrc-row-del {
        border: none; background: transparent; color: #b03a3a; opacity: .55;
        font-size: 1.05rem; line-height: 1; padding: .2rem .35rem; cursor: pointer;
    }
    .hrc-row-del:hover { opacity: 1; }
    .hrc-empty-levels { font-size: .82rem; color: #98a6ad; padding: .5rem .1rem; }
    .hrc-no-crit { font-size: .85rem; color: #98a6ad; padding: 1.1rem .1rem; text-align: center; }

    .hrc-note {
        background: #f8fafc; border: 1px dashed #dfe5ee; border-radius: 10px;
        padding: .6rem .8rem; font-size: .8rem; color: #5c6873; margin-bottom: .9rem;
    }
    .hrc-note strong { color: #313a46; }
</style>

<!-- ============================================================== -->
<!-- Start Page Content here -->
<!-- ============================================================== -->

<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            <!-- start page header -->
            <div class="row">
                <div class="col-12">
                    <div class="hrp-hero">
                        <div class="hrp-hero-text">
                            <span class="hrp-hero-eyebrow"><i class="mdi mdi-clipboard-check-outline"></i> Scoring Criteria</span>
                            <h3 class="hrp-hero-title"><i class="mdi mdi-briefcase-account-outline"></i> <?= html_escape($position_row->title); ?></h3>
                            <!-- <p class="hrp-hero-sub">
                                Criteria and point system used when rating applicants for this position title.
                                Set whichever criteria this title is actually rated on &mdash; they only have to add up to
                                <strong>100 points</strong>. The increment levels below each one convert an applicant's
                                qualification into a score.
                            </p> -->
                        </div>
                        <div class="hrp-hero-stats">
                            <div class="hrp-stat">
                                <span class="hrp-stat-value" id="hrc-hero-total"><?= hrp_num($total_points); ?></span>
                                <span class="hrp-stat-label">Total Points</span>
                            </div>
                            <div class="hrp-stat">
                                <span class="hrp-stat-value" id="hrc-hero-levels"><?= number_format($total_levels); ?></span>
                                <span class="hrp-stat-label">Increment Levels</span>
                            </div>
                            <div class="hrp-stat">
                                <span class="hrp-stat-value"><?= ($position_row->sg !== null && $position_row->sg !== '') ? 'SG ' . (int) $position_row->sg : '&mdash;'; ?></span>
                                <span class="hrp-stat-label">Salary Grade</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end page header -->

            <?php if ($this->session->flashdata('success')) : ?>
                <div class="hrp-alert hrp-alert-success">
                    <i class="mdi mdi-check-circle-outline"></i>
                    <div><?= html_escape($this->session->flashdata('success')); ?></div>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
            <?php endif; ?>

            <?php if ($this->session->flashdata('danger')) : ?>
                <div class="hrp-alert hrp-alert-danger">
                    <i class="mdi mdi-alert-circle-outline"></i>
                    <div><?= html_escape($this->session->flashdata('danger')); ?></div>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
            <?php endif; ?>

            <?php echo form_open('Page/positionCriteria_save', array('id' => 'hrc-form')); ?>
            <input type="hidden" name="position_id" value="<?= $pid; ?>">

            <div class="row">
                <div class="col-12">
                    <div class="hrp-card">

                        <div class="hrp-card-head">
                            <div>
                                <h4 class="hrp-card-title">Breakdown of Points</h4>
                                <p class="hrp-card-sub">
                                    <?= html_escape($position_row->title); ?>
                                    <?php if ($gname !== '') : ?>
                                        <span class="hrp-dotsep">&bull;</span>
                                        <span class="hrp-chip <?= isset($group_chip[$gid]) ? $group_chip[$gid] : 'hrp-chip-grey'; ?>"><?= html_escape($gname); ?></span>
                                    <?php endif; ?>
                                    <?php if ($is_new) : ?>
                                        <span class="hrp-dotsep">&bull;</span>
                                        <span style="color:#a86c14;">not saved yet &mdash; pre-filled with the standard breakdown</span>
                                    <?php elseif ($meta && $meta->updated_at) : ?>
                                        <span class="hrp-dotsep">&bull;</span>
                                        last updated <?= html_escape(date('M j, Y g:i a', strtotime($meta->updated_at))); ?><?= $meta->updated_by ? ' by ' . html_escape($meta->updated_by) : ''; ?>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <div class="hrp-card-actions">
                                <a href="<?= base_url(); ?>Page/positionSettings" class="hrp-btn">
                                    <i class="mdi mdi-arrow-left"></i> Positions
                                </a>
                                <button type="button" class="hrp-btn hrp-btn-purple" id="hrc-copy-btn">
                                    <i class="mdi mdi-content-duplicate"></i> Copy from another position
                                </button>
                                <button type="button" class="hrp-btn hrp-btn-warning" id="hrc-template-btn">
                                    <i class="mdi mdi-file-document-outline"></i> Load standard sheet
                                </button>
                            </div>
                        </div>

                        <div class="hrc-note">
                            <strong>How this is used:</strong> the panel scores each applicant on every criterion listed here.
                            A criterion with an increment table is scored off that table &mdash; find the row the applicant's
                            qualification falls into and award the points on that row. One without a table is rated directly,
                            up to its maximum. Rename, add or remove criteria to match this position's own qualification standards.
                        </div>

                        <div class="table-responsive">
                            <table class="hrc-alloc" id="hrc-alloc">
                                <thead>
                                    <tr>
                                        <th style="min-width:260px;">Criteria</th>
                                        <th style="width:210px;">Graded by</th>
                                        <th class="hrc-pts">Points</th>
                                        <th class="hrc-del-cell"></th>
                                    </tr>
                                </thead>
                                <tbody id="hrc-alloc-body">
                                    <?php foreach ($criteria as $c) : ?>
                                        <tr class="hrc-alloc-row" data-key="<?= html_escape($c['key']); ?>">
                                            <td>
                                                <div class="hrc-label-cell">
                                                    <span class="hrc-letter"></span>
                                                    <input type="text" class="form-control hrc-label"
                                                           name="criteria[<?= html_escape($c['key']); ?>][label]"
                                                           data-key="<?= html_escape($c['key']); ?>"
                                                           value="<?= html_escape($c['label']); ?>"
                                                           maxlength="150" placeholder="Criterion name">
                                                </div>
                                            </td>
                                            <td>
                                                <a href="javascript:void(0);" class="hrp-chip hrc-jump" data-key="<?= html_escape($c['key']); ?>"></a>
                                            </td>
                                            <td class="hrc-pts">
                                                <input type="number" class="form-control hrc-num hrc-max"
                                                       name="criteria[<?= html_escape($c['key']); ?>][points]"
                                                       data-key="<?= html_escape($c['key']); ?>"
                                                       value="<?= hrp_num($c['max_points']); ?>"
                                                       min="0" max="100" step="0.5">
                                            </td>
                                            <td class="hrc-del-cell">
                                                <button type="button" class="hrc-row-del hrc-crit-del" data-key="<?= html_escape($c['key']); ?>" title="Remove criterion">
                                                    <i class="mdi mdi-close"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="2" class="text-right">TOTAL</td>
                                        <td class="hrc-pts">
                                            <span class="hrc-total-pill hrc-total-bad" id="hrc-total-pill">
                                                <i class="mdi mdi-sigma"></i> <span id="hrc-total">0</span>
                                            </span>
                                        </td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="hrc-no-crit" id="hrc-no-crit" style="display:none;">
                            No criteria on this sheet yet. Use <strong>Add criterion</strong> to build it,
                            or load the standard breakdown.
                        </div>

                        <button type="button" class="hrp-btn hrp-btn-sm" id="hrc-add-crit" style="margin-top:.7rem;">
                            <i class="mdi mdi-plus"></i> Add criterion
                        </button>

                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="hrp-card">

                        <div class="hrp-card-head">
                            <div>
                                <h4 class="hrp-card-title">Computation of Points per Criterion</h4>
                                <p class="hrp-card-sub">
                                    One section per criterion in the breakdown above. Add a row per increment level;
                                    a row with no qualification text is discarded on save, and a criterion with no rows
                                    at all is simply rated directly out of its maximum.
                                </p>
                            </div>
                        </div>

                        <div id="hrc-crit-list">
                            <?php foreach ($criteria as $c) : ?>
                                <?php $key = html_escape($c['key']); ?>
                                <div class="hrc-crit is-open" id="hrc-crit-<?= $key; ?>" data-key="<?= $key; ?>">
                                    <div class="hrc-crit-head">
                                        <h6><?= html_escape($c['label']); ?></h6>
                                        <span class="hrp-chip hrp-chip-grey">
                                            <span class="hrc-crit-max" data-key="<?= $key; ?>"><?= hrp_num($c['max_points']); ?></span>&nbsp;points
                                        </span>
                                        <span class="hrp-chip hrp-chip-blue hrc-crit-count" data-key="<?= $key; ?>"></span>
                                        <i class="mdi mdi-chevron-down hrc-caret"></i>
                                    </div>
                                    <div class="hrc-crit-body">

                                        <div class="table-responsive">
                                            <table class="hrc-levels" data-key="<?= $key; ?>">
                                                <thead>
                                                    <tr>
                                                        <th class="hrc-lvl">Level</th>
                                                        <th>Applicant's qualification</th>
                                                        <th class="hrc-pts">Points</th>
                                                        <th class="hrc-del-cell"></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($c['levels'] as $i => $lvl) : ?>
                                                        <tr>
                                                            <td class="hrc-lvl">
                                                                <input type="number" class="form-control hrc-num"
                                                                       name="level[<?= $key; ?>][<?= $i; ?>][increment_level]"
                                                                       value="<?= (int) $lvl->increment_level; ?>" min="0" step="1">
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control"
                                                                       name="level[<?= $key; ?>][<?= $i; ?>][description]"
                                                                       value="<?= html_escape($lvl->description); ?>" maxlength="255"
                                                                       placeholder="e.g. 16-23 hours">
                                                            </td>
                                                            <td class="hrc-pts">
                                                                <input type="number" class="form-control hrc-num hrc-level-pts"
                                                                       name="level[<?= $key; ?>][<?= $i; ?>][points]"
                                                                       value="<?= hrp_num($lvl->points); ?>" min="0" step="0.5">
                                                            </td>
                                                            <td class="hrc-del-cell">
                                                                <button type="button" class="hrc-row-del" title="Remove level">
                                                                    <i class="mdi mdi-close"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                            <div class="hrc-empty-levels" data-key="<?= $key; ?>">
                                                No increment levels &mdash; applicants are rated directly out of
                                                <span class="hrc-crit-max" data-key="<?= $key; ?>"><?= hrp_num($c['max_points']); ?></span> points.
                                            </div>
                                        </div>

                                        <button type="button" class="hrp-btn hrp-btn-sm hrc-add-level" data-key="<?= $key; ?>" style="margin-top:.6rem;">
                                            <i class="mdi mdi-plus"></i> Add increment level
                                        </button>

                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="hrc-no-crit" id="hrc-no-crit-2" style="display:none;">
                            Nothing to compute yet &mdash; add a criterion in the breakdown above.
                        </div>

                        <div class="hrc-bar">
                            <span class="hrc-total-pill hrc-total-bad" id="hrc-total-pill-2">
                                <i class="mdi mdi-sigma"></i> Total: <span id="hrc-total-2">0</span> / 100
                            </span>
                            <span class="hrp-card-sub" id="hrc-total-msg" style="margin:0;"></span>
                            <span class="hrc-bar-spacer"></span>
                            <?php if (!$is_new) : ?>
                                <a href="<?= base_url(); ?>Page/positionCriteria_clear/<?= $pid; ?>"
                                   class="hrp-btn hrp-btn-ghost-danger"
                                   onclick="return confirm('Clear the scoring criteria of \'<?= html_escape(addslashes($position_row->title)); ?>\'? This cannot be undone.');">
                                    <i class="mdi mdi-trash-can-outline"></i> Clear criteria
                                </a>
                            <?php endif; ?>
                            <button type="submit" class="hrp-btn hrp-btn-primary" id="hrc-save">
                                <i class="mdi mdi-content-save-outline"></i> Save Scoring Criteria
                            </button>
                        </div>

                    </div>
                </div>
            </div>

            </form>

        </div>
        <!-- end container-fluid -->
    </div>
    <!-- end content -->

    <?php include('templates/footer.php'); ?>

    <!-- Copy criteria from another position -->
    <div class="modal fade hrp-modal hrp-modal-compact" id="hrc-copy-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <?php echo form_open('Page/positionCriteria_copy', array('id' => 'hrc-copy-form')); ?>
                <div class="modal-header">
                    <h5 class="modal-title"><i class="mdi mdi-content-duplicate"></i> Copy Scoring Criteria</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="position_id" value="<?= $pid; ?>">

                    <div class="hrp-field mb-0">
                        <label class="hrp-label" for="hrc-source">Copy the criteria of <span class="hrp-req">*</span></label>
                        <select class="form-control" name="source_id" id="hrc-source" required>
                            <option value=""></option>
                            <?php foreach ($copy_source as $src) : ?>
                                <option value="<?= (int) $src->id; ?>">
                                    <?= html_escape($src->title); ?>
                                    <?= ((int) $src->criteria_count > 0) ? '(' . hrp_num($src->total) . ' pts)' : '(standard sheet)'; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <span class="hrp-help">
                            This replaces everything currently on <strong><?= html_escape($position_row->title); ?></strong>'s
                            sheet, including its increment levels. A title marked <em>standard sheet</em> has not been set up
                            itself and hands over the default breakdown.
                        </span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="hrp-btn" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="hrp-btn hrp-btn-primary"><i class="mdi mdi-content-duplicate"></i> Copy Criteria</button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {

            /* The standard non-teaching sheet (Administrative Officer II, SG 11).
               Loaded on request only - it is a starting point, not a rule. */
            var STANDARD = [
                { label: 'Education', points: 5, levels: [
                    [6, "Bachelor's degree to 5 MA units", 0],
                    [7, '6-8 MA units', 1],
                    [8, '9-11 MA units', 1],
                    [9, '12-14 MA units', 2],
                    [10, '15-17 MA units', 2],
                    [11, '18-20 MA units', 3],
                    [12, '21-23 MA units', 3],
                    [13, '24-26 MA units', 4],
                    [14, '27-29 MA units', 4],
                    [15, '30-32 MA units', 5],
                    [16, '33 MA units or more', 5]
                ] },
                { label: 'Training', points: 10, levels: [
                    [1, '0-7 hours of relevant training', 0],
                    [2, '8-15 hours', 2],
                    [3, '16-23 hours', 4],
                    [4, '24-31 hours', 6],
                    [5, '32-39 hours', 8],
                    [6, '40 hours or more', 10]
                ] },
                { label: 'Experience', points: 15, levels: [
                    [1, '0 to less than 6 months', 0],
                    [2, '6 mos. to less than 1 year', 3],
                    [3, '1 yr. to less than 1 yr. & 6 mos.', 3],
                    [4, '1 yr. & 6 mos. to less than 2 yrs.', 6],
                    [5, '2 yrs. to less than 2 yrs. & 6 mos.', 6],
                    [6, '2 yrs. & 6 mos. to less than 3 yrs.', 9],
                    [7, '3 yrs. to less than 3 yrs. & 6 mos.', 9],
                    [8, '3 yrs. & 6 mos. to less than 4 yrs.', 12],
                    [9, '4 yrs. to less than 4 yrs. & 6 mos.', 12],
                    [10, '4 yrs. & 6 mos. to less than 5 yrs.', 15],
                    [11, '5 yrs. or more', 15]
                ] },
                { label: 'Performance', points: 20, levels: [] },
                { label: 'Outstanding Accomplishments', points: 10, levels: [] },
                { label: 'Application of Education', points: 10, levels: [] },
                { label: 'Application of Learning and Development', points: 10, levels: [] },
                { label: 'Potential (Written Exam, BEI)', points: 20, levels: [] }
            ];

            // form keys for criteria added on screen, and a per-criterion counter
            // for level rows so every input name stays unique after deletions
            var critSeq = 0;
            var rowSeq = {};
            $('.hrc-levels').each(function () {
                rowSeq[$(this).data('key')] = $(this).find('tbody tr').length + 100;
            });

            function esc(value) {
                return $('<div>').text(value === null || value === undefined ? '' : value).html();
            }

            function trim(value) {
                return (value === null || value === undefined) ? 0 : Math.round(value * 100) / 100;
            }

            // a, b, ... z, aa, ab - the lettering down the left of the paper form
            function letterFor(i) {
                var out = '';
                i += 1;
                while (i > 0) {
                    var rem = (i - 1) % 26;
                    out = String.fromCharCode(97 + rem) + out;
                    i = Math.floor((i - 1) / 26);
                }
                return out;
            }

            function levelRow(key, level, description, points) {
                if (rowSeq[key] === undefined) { rowSeq[key] = 0; }
                var base = 'level[' + key + '][' + (rowSeq[key]++) + ']';

                return '<tr>' +
                    '<td class="hrc-lvl"><input type="number" class="form-control hrc-num" name="' + base + '[increment_level]" value="' + (level === '' ? '' : level) + '" min="0" step="1"></td>' +
                    '<td><input type="text" class="form-control" name="' + base + '[description]" value="' + esc(description) + '" maxlength="255" placeholder="e.g. 16-23 hours"></td>' +
                    '<td class="hrc-pts"><input type="number" class="form-control hrc-num hrc-level-pts" name="' + base + '[points]" value="' + points + '" min="0" step="0.5"></td>' +
                    '<td class="hrc-del-cell"><button type="button" class="hrc-row-del" title="Remove level"><i class="mdi mdi-close"></i></button></td>' +
                    '</tr>';
            }

            function allocRow(key, label, points) {
                return '<tr class="hrc-alloc-row" data-key="' + key + '">' +
                    '<td><div class="hrc-label-cell"><span class="hrc-letter"></span>' +
                        '<input type="text" class="form-control hrc-label" name="criteria[' + key + '][label]" data-key="' + key + '" value="' + esc(label) + '" maxlength="150" placeholder="Criterion name"></div></td>' +
                    '<td><a href="javascript:void(0);" class="hrp-chip hrc-jump" data-key="' + key + '"></a></td>' +
                    '<td class="hrc-pts"><input type="number" class="form-control hrc-num hrc-max" name="criteria[' + key + '][points]" data-key="' + key + '" value="' + points + '" min="0" max="100" step="0.5"></td>' +
                    '<td class="hrc-del-cell"><button type="button" class="hrc-row-del hrc-crit-del" data-key="' + key + '" title="Remove criterion"><i class="mdi mdi-close"></i></button></td>' +
                    '</tr>';
            }

            function critCard(key, label, points) {
                return '<div class="hrc-crit is-open" id="hrc-crit-' + key + '" data-key="' + key + '">' +
                    '<div class="hrc-crit-head">' +
                        '<h6>' + esc(label) + '</h6>' +
                        '<span class="hrp-chip hrp-chip-grey"><span class="hrc-crit-max" data-key="' + key + '">' + points + '</span>&nbsp;points</span>' +
                        '<span class="hrp-chip hrp-chip-blue hrc-crit-count" data-key="' + key + '"></span>' +
                        '<i class="mdi mdi-chevron-down hrc-caret"></i>' +
                    '</div>' +
                    '<div class="hrc-crit-body">' +
                        '<div class="table-responsive">' +
                            '<table class="hrc-levels" data-key="' + key + '">' +
                                '<thead><tr><th class="hrc-lvl">Level</th><th>Applicant\'s qualification</th><th class="hrc-pts">Points</th><th class="hrc-del-cell"></th></tr></thead>' +
                                '<tbody></tbody>' +
                            '</table>' +
                            '<div class="hrc-empty-levels" data-key="' + key + '">No increment levels &mdash; applicants are rated directly out of ' +
                                '<span class="hrc-crit-max" data-key="' + key + '">' + points + '</span> points.</div>' +
                        '</div>' +
                        '<button type="button" class="hrp-btn hrp-btn-sm hrc-add-level" data-key="' + key + '" style="margin-top:.6rem;">' +
                            '<i class="mdi mdi-plus"></i> Add increment level</button>' +
                    '</div>' +
                '</div>';
            }

            // adds a criterion to the breakdown and its matching section below,
            // so the two lists can never drift apart
            function addCriterion(label, points, levels) {
                var key = 'n' + (++critSeq);

                $('#hrc-alloc-body').append(allocRow(key, label, points));
                $('#hrc-crit-list').append(critCard(key, label, points));

                if (levels && levels.length) {
                    var $body = $('.hrc-levels[data-key="' + key + '"]').find('tbody');
                    $.each(levels, function (i, row) {
                        $body.append(levelRow(key, row[0], row[1], row[2]));
                    });
                }

                refreshCriterion(key);
                return key;
            }

            function refreshCriterion(key) {
                var $table = $('.hrc-levels[data-key="' + key + '"]');
                var count = $table.find('tbody tr').length;

                $('.hrc-crit-count[data-key="' + key + '"]').text(count + ' level' + (count === 1 ? '' : 's'));
                $('.hrc-empty-levels[data-key="' + key + '"]').toggle(count === 0);

                $('.hrc-jump[data-key="' + key + '"]')
                    .toggleClass('hrp-chip-blue', count > 0)
                    .toggleClass('hrp-chip-grey', count === 0)
                    .html(count > 0
                        ? '<i class="mdi mdi-table"></i> ' + count + ' increment level' + (count === 1 ? '' : 's')
                        : '<i class="mdi mdi-gesture-tap"></i> rated directly');
            }

            function refreshSheet() {
                var total = 0;
                var levels = 0;

                $('#hrc-alloc-body .hrc-alloc-row').each(function (i) {
                    var $row = $(this);
                    var key = $row.data('key');

                    $row.find('.hrc-letter').text(letterFor(i));

                    var points = parseFloat($row.find('.hrc-max').val());
                    if (isNaN(points)) { points = 0; }
                    total += points;

                    $('.hrc-crit-max[data-key="' + key + '"]').text(trim(points));
                    levels += $('.hrc-levels[data-key="' + key + '"]').find('tbody tr').length;
                });

                total = trim(total);

                var count = $('#hrc-alloc-body .hrc-alloc-row').length;
                var ok = (total === 100 && count > 0);
                var msg = total > 100 ? (trim(total - 100) + ' over') : (trim(100 - total) + ' short');

                $('#hrc-total, #hrc-total-2').text(total);
                $('#hrc-hero-total').text(total);
                $('#hrc-hero-levels').text(levels);

                $('#hrc-total-msg').text(count === 0
                    ? 'Add at least one criterion.'
                    : (ok ? 'Ready to save.' : 'The criteria must total exactly 100 points - currently ' + msg + '.'));

                $('#hrc-total-pill, #hrc-total-pill-2')
                    .toggleClass('hrc-total-ok', ok)
                    .toggleClass('hrc-total-bad', !ok);

                $('#hrc-no-crit, #hrc-no-crit-2').toggle(count === 0);
            }

            /* ---- criteria: add, rename, remove ---- */

            $('#hrc-add-crit').on('click', function () {
                var key = addCriterion('', 0, []);
                refreshSheet();
                $('.hrc-label[data-key="' + key + '"]').trigger('focus');
                $('html, body').animate({ scrollTop: $('.hrc-label[data-key="' + key + '"]').offset().top - 140 }, 250);
            });

            // the breakdown row's name field is the one source of truth for the
            // criterion's label; the section heading just mirrors it
            $(document).on('input', '.hrc-label', function () {
                var label = $.trim($(this).val());
                var $head = $('#hrc-crit-' + $(this).data('key')).find('.hrc-crit-head h6');

                $head.text(label === '' ? '(unnamed criterion)' : label)
                     .toggleClass('is-unnamed', label === '');
            });

            $(document).on('click', '.hrc-crit-del', function () {
                var key = $(this).data('key');
                var label = $.trim($('.hrc-label[data-key="' + key + '"]').val()) || 'this criterion';

                if (!confirm('Remove "' + label + '" from the sheet, along with its increment levels?')) {
                    return;
                }

                $('#hrc-alloc-body .hrc-alloc-row[data-key="' + key + '"]').remove();
                $('#hrc-crit-' + key).remove();
                delete rowSeq[key];
                refreshSheet();
            });

            $(document).on('input change', '.hrc-max', refreshSheet);

            /* ---- increment levels ---- */

            $(document).on('click', '.hrc-add-level', function () {
                var key = $(this).data('key');
                var $body = $('.hrc-levels[data-key="' + key + '"]').find('tbody');
                var next = $body.find('tr').length + 1;

                // continue whatever numbering the table already uses
                var $last = $body.find('tr:last input[name$="[increment_level]"]');
                if ($last.length) {
                    var lastLevel = parseInt($last.val(), 10);
                    if (!isNaN(lastLevel)) { next = lastLevel + 1; }
                }

                $body.append(levelRow(key, next, '', 0));
                $body.find('tr:last input[type="text"]').trigger('focus');
                refreshCriterion(key);
                refreshSheet();
            });

            $(document).on('click', '.hrc-row-del:not(.hrc-crit-del)', function () {
                var key = $(this).closest('.hrc-levels').data('key');
                $(this).closest('tr').remove();
                refreshCriterion(key);
                refreshSheet();
            });

            /* ---- navigation, copy, template ---- */

            $(document).on('click', '.hrc-crit-head', function (e) {
                if ($(e.target).is('input, select, textarea, button')) { return; }
                $(this).closest('.hrc-crit').toggleClass('is-open');
            });

            $(document).on('click', '.hrc-jump', function () {
                var $crit = $('#hrc-crit-' + $(this).data('key'));
                if (!$crit.length) { return; }
                $crit.addClass('is-open');
                $('html, body').animate({ scrollTop: $crit.offset().top - 90 }, 250);
            });

            $('#hrc-copy-btn').on('click', function () {
                $('#hrc-copy-modal').modal('show');
            });

            $('#hrc-source').select2({
                dropdownParent: $('#hrc-copy-modal'),
                minimumResultsForSearch: 5,
                width: '100%',
                placeholder: 'Search a position title...'
            });

            $('#hrc-template-btn').on('click', function () {
                if (!confirm('Load the standard sheet? This replaces every criterion currently on screen, along with their increment levels. Nothing is saved until you press Save.')) {
                    return;
                }

                $('#hrc-alloc-body').empty();
                $('#hrc-crit-list').empty();
                rowSeq = {};

                $.each(STANDARD, function (i, crit) {
                    addCriterion(crit.label, crit.points, crit.levels);
                });

                refreshSheet();
            });

            /* ---- save ---- */

            $('#hrc-form').on('submit', function (e) {
                var rows = $('#hrc-alloc-body .hrc-alloc-row');

                if (rows.length === 0) {
                    e.preventDefault();
                    alert('Add at least one criterion before saving.');
                    return;
                }

                var total = 0;
                var unnamed = 0;

                rows.each(function () {
                    var points = parseFloat($(this).find('.hrc-max').val());
                    if (!isNaN(points)) { total += points; }
                    if ($.trim($(this).find('.hrc-label').val()) === '') { unnamed++; }
                });
                total = trim(total);

                if (unnamed > 0) {
                    e.preventDefault();
                    alert('Every criterion needs a name - ' + unnamed + ' row(s) are still blank. Name them or remove them.');
                    return;
                }

                if (total !== 100) {
                    e.preventDefault();
                    alert('The criteria must add up to exactly 100 points. They currently total ' + total + '.');
                    return;
                }

                // a level may not award more than the criterion it belongs to
                var bad = null;
                $('.hrc-levels').each(function () {
                    var key = $(this).data('key');
                    var max = parseFloat($('.hrc-max[data-key="' + key + '"]').val()) || 0;
                    var label = $.trim($('.hrc-label[data-key="' + key + '"]').val());

                    $(this).find('.hrc-level-pts').each(function () {
                        var points = parseFloat($(this).val());
                        if (!bad && !isNaN(points) && points > max) {
                            bad = label + ': an increment level gives ' + points + ' points but the criterion is only worth ' + max + '.';
                        }
                    });
                });

                if (bad) {
                    e.preventDefault();
                    alert(bad);
                }
            });

            $('#hrc-alloc-body .hrc-alloc-row').each(function () {
                refreshCriterion($(this).data('key'));
            });
            refreshSheet();
        });
    </script>
