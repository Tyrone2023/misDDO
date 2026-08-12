<?php include('templates/head.php'); ?>
<?php include('templates/header.php'); ?>

<?php
/*
 * Positions Settings - CRUD over hris_positions.
 * This table feeds the "Position Title" dropdown on Page/jobVacancy, so anything
 * added here immediately becomes available when posting a new job vacancy.
 */

$group_counts = array_fill_keys(array_keys($groups), 0);
$ungrouped    = 0;
$in_use       = 0;
$scored       = 0;

foreach ($data as $p) {
    $gid = (int) $p->pos_id;
    if (isset($group_counts[$gid])) {
        $group_counts[$gid]++;
    } else {
        $ungrouped++;
    }
    if (!empty($usage[(int) $p->id])) {
        $in_use++;
    }
    if (!empty($criteria[(int) $p->id])) {
        $scored++;
    }
}

$group_chip = array(1 => 'hrp-chip-blue', 2 => 'hrp-chip-purple', 3 => 'hrp-chip-amber', 4 => 'hrp-chip-green');
?>

<?php include('includes/hr_recruitment_styles.php'); ?>

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
                            <span class="hrp-hero-eyebrow"><i class="mdi mdi-cog-outline"></i> Recruitment Settings</span>
                            <h3 class="hrp-hero-title"><i class="mdi mdi-briefcase-account-outline"></i> Positions Settings</h3>
                            <p class="hrp-hero-sub">
                                Maintain the master list of position titles. Every entry here shows up in the
                                <strong>Position Title</strong> dropdown when posting a
                                <a href="<?= base_url(); ?>Page/jobVacancy" style="color:#fff;text-decoration:underline;">job vacancy</a>,
                                filtered by its position group. Each title also carries its own
                                <strong>scoring criteria</strong> &mdash; the 100-point sheet applicants are rated against.
                            </p>
                        </div>
                        <div class="hrp-hero-stats">
                            <div class="hrp-stat">
                                <span class="hrp-stat-value"><?= number_format(count($data)); ?></span>
                                <span class="hrp-stat-label">Positions</span>
                            </div>
                            <div class="hrp-stat">
                                <span class="hrp-stat-value"><?= number_format(count($groups)); ?></span>
                                <span class="hrp-stat-label">Groups</span>
                            </div>
                            <div class="hrp-stat">
                                <span class="hrp-stat-value"><?= number_format($scored); ?></span>
                                <span class="hrp-stat-label">Scored</span>
                            </div>
                            <div class="hrp-stat">
                                <span class="hrp-stat-value"><?= number_format($in_use); ?></span>
                                <span class="hrp-stat-label">Posted</span>
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

            <div class="row">
                <div class="col-12">
                    <div class="hrp-card">

                        <div class="hrp-card-head">
                            <div>
                                <h4 class="hrp-card-title">Position Titles</h4>
                                <p class="hrp-card-sub">
                                    <?= number_format(count($data)); ?> position<?= count($data) == 1 ? '' : 's'; ?> on file
                                    <span class="hrp-dotsep">&bull;</span>
                                    <?= number_format($scored); ?> with scoring criteria set
                                    <span class="hrp-dotsep">&bull;</span>
                                    <?= number_format($in_use); ?> already used in a posting
                                    <?php if ($ungrouped > 0) : ?>
                                        <span class="hrp-dotsep">&bull;</span>
                                        <span style="color:#b03a3a;"><?= number_format($ungrouped); ?> without a group</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <div class="hrp-card-actions">
                                <a href="<?= base_url(); ?>Page/jobVacancy" class="hrp-btn">
                                    <i class="mdi mdi-briefcase-outline"></i> Job Vacancies
                                </a>
                                <button type="button" class="hrp-btn hrp-btn-primary" id="hrp-add-btn">
                                    <i class="mdi mdi-plus"></i> Add Position
                                </button>
                            </div>
                        </div>

                        <div class="hrp-tabs" id="hrp-group-tabs">
                            <a href="javascript:void(0);" class="hrp-tab is-active" data-group="">
                                All <span class="hrp-tab-count"><?= number_format(count($data)); ?></span>
                            </a>
                            <?php foreach ($groups as $gid => $gname) : ?>
                                <a href="javascript:void(0);" class="hrp-tab" data-group="<?= html_escape($gname); ?>">
                                    <?= html_escape($gname); ?>
                                    <span class="hrp-tab-count"><?= number_format($group_counts[$gid]); ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>

                        <?php if (empty($data)) : ?>
                            <div class="hrp-empty">
                                <i class="mdi mdi-briefcase-remove-outline"></i>
                                No position titles yet. Use <strong>Add Position</strong> to create the first one.
                            </div>
                        <?php else : ?>
                            <div class="table-responsive">
                                <table id="hrp-positions-table" class="table hrp-table dt-responsive nowrap" style="width:100%;">
                                    <thead>
                                        <tr>
                                            <th>Position Title</th>
                                            <th>Position Group</th>
                                            <th class="text-center">Salary Grade</th>
                                            <th>Scoring Criteria</th>
                                            <th class="text-right">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($data as $p) : ?>
                                            <?php
                                            $pid      = (int) $p->id;
                                            $gid      = (int) $p->pos_id;
                                            $gname    = isset($groups[$gid]) ? $groups[$gid] : '';
                                            $posts    = isset($usage[$pid]) ? (int) $usage[$pid] : 0;
                                            $initials = strtoupper(mb_substr(trim((string) $p->title), 0, 2));
                                            $sheet    = isset($criteria[$pid]) ? $criteria[$pid] : null;
                                            $total    = $sheet ? (float) $sheet['total'] : 0;
                                            ?>
                                            <tr data-group="<?= html_escape($gname); ?>">
                                                <td data-order="<?= html_escape($p->title); ?>">
                                                    <div class="hrp-title-cell">
                                                        <span class="hrp-avatar"><?= html_escape($initials); ?></span>
                                                        <span class="hrp-title-text">
                                                            <span class="hrp-title-name"><?= html_escape($p->title); ?></span>
                                                            <span class="hrp-title-sub">ID #<?= $pid; ?></span>
                                                        </span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <?php if ($gname !== '') : ?>
                                                        <span class="hrp-chip <?= isset($group_chip[$gid]) ? $group_chip[$gid] : 'hrp-chip-grey'; ?>"><?= html_escape($gname); ?></span>
                                                    <?php else : ?>
                                                        <span class="hrp-chip hrp-chip-red"><i class="mdi mdi-alert-outline"></i> Not set</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <?php if ($p->sg !== null && $p->sg !== '') : ?>
                                                        <span class="hrp-mono">SG <?= (int) $p->sg; ?></span>
                                                    <?php else : ?>
                                                        <span class="hrp-muted">&mdash;</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td data-order="<?= $sheet ? (int) round($total) : -1; ?>">
                                                    <?php if ($sheet && round($total, 2) == 100) : ?>
                                                        <span class="hrp-chip hrp-chip-green">
                                                            <i class="mdi mdi-check-circle-outline"></i> 100 pts
                                                        </span>
                                                        <span class="hrp-title-sub" style="margin-left:.35rem;"><?= number_format($sheet['levels']); ?> level<?= $sheet['levels'] == 1 ? '' : 's'; ?></span>
                                                    <?php elseif ($sheet) : ?>
                                                        <span class="hrp-chip hrp-chip-amber">
                                                            <i class="mdi mdi-alert-outline"></i> <?= rtrim(rtrim(number_format($total, 2, '.', ''), '0'), '.'); ?> pts
                                                        </span>
                                                    <?php else : ?>
                                                        <span class="hrp-chip hrp-chip-grey">Not set</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-right">
                                                    <div class="hrp-actions">
                                                        <a href="<?= base_url(); ?>Page/positionCriteria/<?= $pid; ?>"
                                                           class="hrp-ico hrp-ico-purple"
                                                           title="Scoring criteria">
                                                            <i class="mdi mdi-clipboard-check-outline"></i>
                                                        </a>
                                                        <a href="javascript:void(0);"
                                                           class="hrp-ico hrp-ico-blue hrp-edit-btn"
                                                           title="Edit position"
                                                           data-id="<?= $pid; ?>"
                                                           data-title="<?= html_escape($p->title); ?>"
                                                           data-pos_id="<?= $gid; ?>"
                                                           data-sg="<?= ($p->sg === null ? '' : (int) $p->sg); ?>">
                                                            <i class="mdi mdi-pencil-outline"></i>
                                                        </a>
                                                        <?php if ($posts > 0) : ?>
                                                            <span class="hrp-ico hrp-ico-grey" title="In use by <?= $posts; ?> posting(s) - cannot be deleted" style="cursor:not-allowed;opacity:.55;">
                                                                <i class="mdi mdi-lock-outline"></i>
                                                            </span>
                                                        <?php else : ?>
                                                            <a href="<?= base_url(); ?>Page/positionSettings_delete/<?= $pid; ?>"
                                                               class="hrp-ico hrp-ico-red"
                                                               title="Delete position"
                                                               onclick="return confirm('Delete \'<?= html_escape(addslashes($p->title)); ?>\'? This cannot be undone.');">
                                                                <i class="mdi mdi-trash-can-outline"></i>
                                                            </a>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>
            </div>

        </div>
        <!-- end container-fluid -->
    </div>
    <!-- end content -->

    <?php include('templates/footer.php'); ?>

    <!-- Add / Edit position -->
    <div class="modal fade hrp-modal hrp-modal-compact" id="hrp-position-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <?php echo form_open('Page/positionSettings_save', array('id' => 'hrp-position-form')); ?>
                <div class="modal-header">
                    <h5 class="modal-title"><i class="mdi mdi-briefcase-plus-outline"></i> <span id="hrp-modal-title">Add Position</span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">

                    <input type="hidden" name="id" id="hrp-id" value="">

                    <div class="hrp-field">
                        <label class="hrp-label" for="hrp-title">Position Title <span class="hrp-req">*</span></label>
                        <input type="text" class="form-control" name="title" id="hrp-title" maxlength="200" required>
                    </div>

                    <div class="hrp-field">
                        <label class="hrp-label" for="hrp-pos_id">Position Group <span class="hrp-req">*</span></label>
                        <select class="form-control" name="pos_id" id="hrp-pos_id" required>
                            <option value=""></option>
                            <?php foreach ($groups as $gid => $gname) : ?>
                                <option value="<?= $gid; ?>"><?= html_escape($gname); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="hrp-field mb-0">
                        <label class="hrp-label" for="hrp-sg">Salary Grade</label>
                        <input type="number" class="form-control" name="sg" id="hrp-sg" min="1" max="33">
                        <span class="hrp-help" id="hrp-criteria-help">
                            How applicants are scored for this title is set separately, under <strong>Scoring Criteria</strong>.
                        </span>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="hrp-btn" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="hrp-btn hrp-btn-primary"><i class="mdi mdi-content-save-outline"></i> Save Position</button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {

            var $table = $('#hrp-positions-table');
            var dt = null;
            var activeGroup = '';

            // filter on the row's data-group instead of the rendered cell text,
            // so the group pills stay exact even though the cell holds markup
            $.fn.dataTable.ext.search.push(function (settings, rowData, dataIndex) {
                if (settings.nTable.id !== 'hrp-positions-table' || activeGroup === '') {
                    return true;
                }
                return $(settings.aoData[dataIndex].nTr).data('group') === activeGroup;
            });

            if ($table.length) {
                dt = $table.DataTable({
                    pageLength: 25,
                    lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
                    order: [[0, 'asc']],
                    columnDefs: [{ orderable: false, targets: 4 }],
                    language: { search: '', searchPlaceholder: 'Search position title...' },
                    dom: "<'row'<'col-sm-6'l><'col-sm-6'f>>tp<'row'<'col-sm-12'i>>"
                });
            }

            $('#hrp-group-tabs .hrp-tab').on('click', function () {
                activeGroup = $(this).data('group') || '';
                $('#hrp-group-tabs .hrp-tab').removeClass('is-active');
                $(this).addClass('is-active');

                if (dt) { dt.draw(); }
            });

            $('#hrp-pos_id').select2({
                dropdownParent: $('#hrp-position-modal'),
                minimumResultsForSearch: 8,
                width: '100%'
            });

            $('#hrp-add-btn').on('click', function () {
                $('#hrp-modal-title').text('Add Position');
                $('#hrp-position-form')[0].reset();
                $('#hrp-id').val('');
                $('#hrp-pos_id').val('').trigger('change');
                $('#hrp-position-modal').modal('show');
            });

            // delegated: DataTables keeps rows for pages 2+ out of the DOM, so a
            // direct .on() only ever reached the first page of positions and the
            // pencil did nothing everywhere else.  .attr() rather than .data() so
            // titles that look numeric ("2024") stay strings.
            $(document).on('click', '.hrp-edit-btn', function () {
                var $b = $(this);
                $('#hrp-modal-title').text('Edit Position');
                $('#hrp-id').val($b.attr('data-id'));
                $('#hrp-title').val($b.attr('data-title'));
                $('#hrp-sg').val($b.attr('data-sg'));
                $('#hrp-pos_id').val($b.attr('data-pos_id')).trigger('change');
                $('#hrp-position-modal').modal('show');
            });

            $('#hrp-position-modal').on('shown.bs.modal', function () {
                $('#hrp-title').trigger('focus');
            });

            $('#hrp-position-form').on('submit', function (e) {
                if ($.trim($('#hrp-title').val()) === '' || $('#hrp-pos_id').val() === '') {
                    e.preventDefault();
                    alert('Position title and position group are required.');
                }
            });
        });
    </script>
