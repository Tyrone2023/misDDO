<?php include('templates/head.php'); ?>
<?php include('templates/header.php'); ?>

<?php
/*
 * Signatories of one job vacancy - CRUD over hris_vacancy_signatories.
 * The grid position set here is where each name prints on the RQA reports
 * (Pages/car_rqa_administrative and Pages/car_rqa_administrative_posting).
 */

$jobTypes = isset($job_types) && is_array($job_types) ? $job_types : array();

$group_chip = array(1 => 'hrp-chip-blue', 2 => 'hrp-chip-purple', 3 => 'hrp-chip-amber', 4 => 'hrp-chip-green');

$jobID      = (int) $job->jobID;
$groupName  = isset($groups[(int) $job->position]) ? $groups[(int) $job->position] : '';
$typeLabel  = isset($jobTypes[(int) $job->job_type]) ? $jobTypes[(int) $job->job_type] : '';
$withEsig   = 0;
$withLabel  = 0;
foreach ($rows as $r) {
    if (trim((string) $r->esig) !== '') {
        $withEsig++;
    }
    if (trim((string) $r->print_label) !== '') {
        $withLabel++;
    }
}
$total = count($rows);
?>

<?php include('includes/hr_recruitment_styles.php'); ?>
<style>
    .vs-preview {
        height: 42px;
        max-width: 150px;
        object-fit: contain;
        background: #fff;
    }
    .vs-preview-box {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 90px;
        height: 46px;
        padding: 2px 6px;
        border: 1px dashed #dfe3ea;
        border-radius: 8px;
        background: #fbfcfe;
    }
    .vs-order {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 26px;
        height: 26px;
        border-radius: 50%;
        background: #eef2ff;
        color: #3b4ab0;
        font-weight: 700;
        font-size: 12px;
    }
    .vs-layout-control {
        display: grid;
        grid-template-columns: 24px 44px 24px;
        grid-template-rows: 21px 28px 21px;
        align-items: center;
        justify-content: center;
        line-height: 1;
    }
    .vs-layout-control a {
        color: #8a94a6;
        font-size: 15px;
        line-height: 1;
        text-align: center;
    }
    .vs-layout-control a:hover { color: #3b4ab0; }
    .vs-layout-control a.disabled { color: #dfe3ea; pointer-events: none; }
    .vs-layout-up { grid-column: 2; grid-row: 1; }
    .vs-layout-left { grid-column: 1; grid-row: 2; }
    .vs-layout-slot { grid-column: 2; grid-row: 2; font-size: 10px; font-weight: 700; color: #3b4ab0; }
    .vs-layout-right { grid-column: 3; grid-row: 2; }
    .vs-layout-down { grid-column: 2; grid-row: 3; }
    .vs-file-hint { font-size: 11.5px; color: #8a94a6; }
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
                            <span class="hrp-hero-eyebrow"><i class="mdi mdi-draw"></i> Vacancy Signatories</span>
                            <h3 class="hrp-hero-title"><i class="mdi mdi-file-sign"></i> <?= html_escape($job->jobTitle); ?></h3>
                            <p class="hrp-hero-sub">
                                Signatories printed at the foot of this vacancy's
                                <strong>RQA / CAR</strong> reports in a flexible five-column grid.
                                Each signatory may carry a heading and an e-signature image
                                stored in <strong>uploads/esig</strong>.
                            </p>
                            <p class="hrp-hero-sub" style="margin-top:.35rem;">
                                <?php if ($groupName !== '') : ?><?= html_escape($groupName); ?><span class="hrp-dotsep">&bull;</span><?php endif; ?>
                                <?php if ($typeLabel !== '') : ?><?= html_escape($typeLabel); ?><span class="hrp-dotsep">&bull;</span><?php endif; ?>
                                SY <?= html_escape($job->sy); ?>
                                <span class="hrp-dotsep">&bull;</span> Job ID #<?= $jobID; ?>
                            </p>
                        </div>
                        <div class="hrp-hero-stats">
                            <div class="hrp-stat">
                                <span class="hrp-stat-value"><?= number_format($total); ?></span>
                                <span class="hrp-stat-label">Signatories</span>
                            </div>
                            <div class="hrp-stat">
                                <span class="hrp-stat-value"><?= number_format($withEsig); ?></span>
                                <span class="hrp-stat-label">With e-sig</span>
                            </div>
                            <div class="hrp-stat">
                                <span class="hrp-stat-value"><?= number_format($withLabel); ?></span>
                                <span class="hrp-stat-label">With label</span>
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
                                <h4 class="hrp-card-title">Signatory Panel</h4>
                                <p class="hrp-card-sub">
                                    <?= number_format($total); ?> signator<?= $total == 1 ? 'y' : 'ies'; ?> on file
                                    <span class="hrp-dotsep">&bull;</span>
                                    arranged in a five-column print grid; arrows move a signatory one cell
                                </p>
                            </div>
                            <div class="hrp-card-actions">
                                <a href="<?= base_url(); ?>Page/jobVacancy" class="hrp-btn">
                                    <i class="mdi mdi-arrow-left"></i> Job Vacancies
                                </a>
                                <a href="<?= base_url(); ?>Pages/car_rqa_administrative/<?= $jobID; ?>" target="_blank" class="hrp-btn hrp-btn-info">
                                    <i class="mdi mdi-file-eye-outline"></i> Preview CAR
                                </a>
                                <?php if (!empty($sources)) : ?>
                                    <button type="button" class="hrp-btn hrp-btn-purple" id="vs-copy-btn">
                                        <i class="mdi mdi-content-duplicate"></i> Copy From
                                    </button>
                                <?php endif; ?>
                                <button type="button" class="hrp-btn hrp-btn-primary" id="vs-add-btn">
                                    <i class="mdi mdi-plus"></i> Add Signatory
                                </button>
                            </div>
                        </div>

                        <?php if (empty($rows)) : ?>
                            <div class="hrp-empty">
                                <i class="mdi mdi-draw"></i>
                                No signatory encoded for this vacancy yet. Use <strong>Add Signatory</strong> to
                                set the first one &mdash; the RQA reports print nothing here until at least one exists.
                            </div>
                        <?php else : ?>
                            <div class="table-responsive">
                                <table class="table hrp-table nowrap" style="width:100%;">
                                    <thead>
                                        <tr>
                                            <th style="width:125px;" class="text-center">Placement</th>
                                            <th>Name</th>
                                            <th>Heading / Label</th>
                                            <th>Position / Designation</th>
                                            <th>Role in Panel</th>
                                            <th class="text-center">E-Signature</th>
                                            <th class="text-right">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($rows as $i => $s) : ?>
                                            <?php $initials = strtoupper(mb_substr(trim((string) $s->name), 0, 2)); ?>
                                            <?php $slot = max(1, (int) $s->print_slot); $layoutRow = (int) ceil($slot / 5); $layoutCol = (($slot - 1) % 5) + 1; ?>
                                            <tr>
                                                <td class="text-center">
                                                    <div class="vs-layout-control">
                                                        <a href="javascript:void(0);" class="vs-layout-btn vs-layout-up<?= $layoutRow === 1 ? ' disabled' : ''; ?>" data-id="<?= (int) $s->id; ?>" data-direction="up" title="Move to the row above"><i class="mdi mdi-chevron-up"></i></a>
                                                        <a href="javascript:void(0);" class="vs-layout-btn vs-layout-left<?= $layoutCol === 1 ? ' disabled' : ''; ?>" data-id="<?= (int) $s->id; ?>" data-direction="left" title="Move left"><i class="mdi mdi-chevron-left"></i></a>
                                                        <span class="vs-layout-slot">R<?= $layoutRow; ?> C<?= $layoutCol; ?></span>
                                                        <a href="javascript:void(0);" class="vs-layout-btn vs-layout-right<?= $layoutCol === 5 ? ' disabled' : ''; ?>" data-id="<?= (int) $s->id; ?>" data-direction="right" title="Move right"><i class="mdi mdi-chevron-right"></i></a>
                                                        <a href="javascript:void(0);" class="vs-layout-btn vs-layout-down<?= $layoutRow === 10 ? ' disabled' : ''; ?>" data-id="<?= (int) $s->id; ?>" data-direction="down" title="Move to the row below"><i class="mdi mdi-chevron-down"></i></a>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="hrp-title-cell">
                                                        <span class="hrp-avatar"><?= html_escape($initials); ?></span>
                                                        <span class="hrp-title-text">
                                                            <span class="hrp-title-name"><?= html_escape($s->name); ?></span>
                                                            <span class="hrp-title-sub">Print slot <?= $slot; ?></span>
                                                        </span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <?php if (trim((string) $s->print_label) !== '') : ?>
                                                        <strong><?= html_escape($s->print_label); ?></strong>
                                                    <?php else : ?>
                                                        <span class="hrp-muted">&mdash;</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if (trim((string) $s->designation) !== '') : ?>
                                                        <?= html_escape($s->designation); ?>
                                                    <?php else : ?>
                                                        <span class="hrp-muted">&mdash;</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if (trim((string) $s->sign_role) !== '') : ?>
                                                        <span class="hrp-chip hrp-chip-blue"><?= html_escape($s->sign_role); ?></span>
                                                    <?php else : ?>
                                                        <span class="hrp-muted">&mdash;</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <span class="vs-preview-box">
                                                        <?php if (trim((string) $s->esig) !== '') : ?>
                                                            <img class="vs-preview" src="<?= base_url(); ?>uploads/esig/<?= rawurlencode($s->esig); ?>" alt="<?= html_escape($s->name); ?> e-signature">
                                                        <?php else : ?>
                                                            <span class="hrp-muted">No image</span>
                                                        <?php endif; ?>
                                                    </span>
                                                </td>
                                                <td class="text-right">
                                                    <div class="hrp-actions">
                                                        <a href="javascript:void(0);"
                                                           class="hrp-ico hrp-ico-blue vs-edit-btn"
                                                           title="Edit signatory"
                                                           data-id="<?= (int) $s->id; ?>"
                                                           data-name="<?= html_escape($s->name); ?>"
                                                           data-designation="<?= html_escape($s->designation); ?>"
                                                           data-role="<?= html_escape($s->sign_role); ?>"
                                                           data-order="<?= (int) $s->signatory_order; ?>"
                                                           data-slot="<?= $slot; ?>"
                                                           data-label="<?= html_escape($s->print_label); ?>"
                                                           data-esig="<?= html_escape($s->esig); ?>">
                                                            <i class="mdi mdi-pencil-outline"></i>
                                                        </a>
                                                        <a href="<?= base_url(); ?>VacancySignatories/delete/<?= (int) $s->id; ?>"
                                                           class="hrp-ico hrp-ico-red"
                                                           title="Delete signatory"
                                                           onclick="return confirm('Delete <?= html_escape(addslashes($s->name)); ?> from this vacancy\'s signatories?');">
                                                            <i class="mdi mdi-trash-can-outline"></i>
                                                        </a>
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

    <!-- Add / Edit signatory -->
    <div class="modal fade hrp-modal hrp-modal-compact" id="vs-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <?php echo form_open_multipart('VacancySignatories/save', array('id' => 'vs-form')); ?>
                <div class="modal-header">
                    <h5 class="modal-title"><i class="mdi mdi-draw"></i> <span id="vs-modal-title">Add Signatory</span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="vs-id" value="">
                    <input type="hidden" name="job_id" value="<?= $jobID; ?>">

                    <div class="hrp-field">
                        <label class="hrp-label" for="vs-name">Name <span class="hrp-req">*</span></label>
                        <input type="text" class="form-control" name="name" id="vs-name" maxlength="200" required
                               placeholder="e.g. JUAN A. DELA CRUZ">
                    </div>

                    <div class="hrp-field">
                        <label class="hrp-label" for="vs-label">Heading / Label above the signature</label>
                        <input type="text" class="form-control" name="print_label" id="vs-label" maxlength="200"
                               placeholder="e.g. Approving Authority:">
                        <span class="hrp-help">Optional text printed directly above this signatory.</span>
                    </div>

                    <div class="hrp-field">
                        <label class="hrp-label" for="vs-designation">Position / Designation</label>
                        <input type="text" class="form-control" name="designation" id="vs-designation" maxlength="200"
                               placeholder="e.g. Chief Education Supervisor, CID">
                    </div>

                    <div class="hrp-field">
                        <label class="hrp-label" for="vs-role">Role in Panel</label>
                        <input type="text" class="form-control" name="sign_role" id="vs-role" maxlength="100"
                               list="vs-role-list" placeholder="e.g. Member">
                        <datalist id="vs-role-list">
                            <option value="Member"></option>
                            <option value="Chairperson"></option>
                            <option value="Vice-Chairperson"></option>
                            <option value="Recommending Approval"></option>
                            <option value="Approved by"></option>
                        </datalist>
                        <span class="hrp-help">Printed under the position, e.g. <em>Member</em> or <em>Chairperson</em>.</span>
                    </div>

                    <div class="hrp-field">
                        <label class="hrp-label" for="vs-slot">Print Position</label>
                        <input type="number" class="form-control" name="print_slot" id="vs-slot" min="1" max="50" value="<?= (int) $next_slot; ?>">
                        <span class="hrp-help">Positions 1&ndash;5 are row 1, 6&ndash;10 are row 2, and so on. An occupied position is swapped.</span>
                    </div>

                    <div class="hrp-field mb-0">
                        <label class="hrp-label" for="vs-esig">E-Signature Image</label>
                        <input type="file" class="form-control" name="esig" id="vs-esig" accept=".png,.jpg,.jpeg">
                        <span class="hrp-help vs-file-hint">
                            PNG (transparent background preferred) or JPG, max 2 MB. Saved to
                            <strong>uploads/esig</strong>.
                            <span id="vs-current-esig"></span>
                        </span>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="hrp-btn" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="hrp-btn hrp-btn-primary"><i class="mdi mdi-content-save-outline"></i> Save Signatory</button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <?php if (!empty($sources)) : ?>
    <!-- Copy signatories from another vacancy -->
    <div class="modal fade hrp-modal hrp-modal-compact" id="vs-copy-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <?php echo form_open('VacancySignatories/copy_from', array('id' => 'vs-copy-form')); ?>
                <div class="modal-header">
                    <h5 class="modal-title"><i class="mdi mdi-content-duplicate"></i> Copy Signatories</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="job_id" value="<?= $jobID; ?>">
                    <div class="hrp-field mb-0">
                        <label class="hrp-label" for="vs-source">Copy the panel of</label>
                        <select class="form-control" name="source_job_id" id="vs-source" required>
                            <option value=""></option>
                            <?php foreach ($sources as $src) : ?>
                                <?php
                                // spell out what the source vacancy is, so two postings of the
                                // same title (Elementary vs Secondary, say) are told apart
                                $srcBits = array();
                                if (isset($jobTypes[(int) $src->job_type])) {
                                    $srcBits[] = $jobTypes[(int) $src->job_type];
                                }
                                if (isset($groups[(int) $src->position])) {
                                    $srcBits[] = $groups[(int) $src->position];
                                }
                                if (trim((string) $src->empType) !== '') {
                                    $srcBits[] = trim((string) $src->empType);
                                }
                                $srcBits[] = 'SY ' . $src->sy;
                                ?>
                                <option value="<?= (int) $src->jobID; ?>"><?= html_escape($src->jobTitle); ?> &mdash; <?= html_escape(implode(' • ', $srcBits)); ?> (<?= (int) $src->total; ?> signator<?= (int) $src->total === 1 ? 'y' : 'ies'; ?>)</option>
                            <?php endforeach; ?>
                        </select>
                        <span class="hrp-help">The copied names are appended after the ones already listed here.</span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="hrp-btn" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="hrp-btn hrp-btn-primary"><i class="mdi mdi-content-copy"></i> Copy</button>
                </div>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script>
        $(document).ready(function () {

            var nextSlot = <?= (int) $next_slot; ?>;

            $('#vs-add-btn').on('click', function () {
                $('#vs-modal-title').text('Add Signatory');
                $('#vs-form')[0].reset();
                $('#vs-id').val('');
                $('#vs-slot').val(nextSlot);
                $('#vs-current-esig').html('');
                $('#vs-modal').modal('show');
            });

            // .attr() rather than .data() so names that look numeric stay strings
            $(document).on('click', '.vs-edit-btn', function () {
                var $b = $(this);
                var esig = $b.attr('data-esig') || '';

                $('#vs-modal-title').text('Edit Signatory');
                $('#vs-form')[0].reset();
                $('#vs-id').val($b.attr('data-id'));
                $('#vs-name').val($b.attr('data-name'));
                $('#vs-designation').val($b.attr('data-designation'));
                $('#vs-role').val($b.attr('data-role'));
                $('#vs-label').val($b.attr('data-label'));
                $('#vs-slot').val($b.attr('data-slot'));

                $('#vs-current-esig').html(
                    esig === ''
                        ? ' Currently no image on file.'
                        : ' Currently <strong>' + $('<div>').text(esig).html() + '</strong> - leave empty to keep it.'
                );

                $('#vs-modal').modal('show');
            });

            $('#vs-modal').on('shown.bs.modal', function () {
                $('#vs-name').trigger('focus');
            });

            $('#vs-form').on('submit', function (e) {
                if ($.trim($('#vs-name').val()) === '') {
                    e.preventDefault();
                    alert('Signatory name is required.');
                }
            });

            $(document).on('click', '.vs-layout-btn', function () {
                var $button = $(this);
                if ($button.hasClass('disabled')) { return; }

                $button.addClass('disabled');
                $.post('<?= base_url(); ?>VacancySignatories/move_layout', {
                    id: $button.attr('data-id'),
                    direction: $button.attr('data-direction')
                }).done(function (response) {
                    if (response && response.status === 'success') {
                        window.location.reload();
                        return;
                    }
                    alert((response && response.message) || 'The signatory could not be moved.');
                    $button.removeClass('disabled');
                }).fail(function () {
                    alert('The signatory could not be moved. Please try again.');
                    $button.removeClass('disabled');
                });
            });

            <?php if (!empty($sources)) : ?>
            $('#vs-source').select2({
                dropdownParent: $('#vs-copy-modal'),
                width: '100%',
                placeholder: 'Select a vacancy...'
            });

            $('#vs-copy-btn').on('click', function () {
                $('#vs-source').val('').trigger('change');
                $('#vs-copy-modal').modal('show');
            });
            <?php endif; ?>
        });
    </script>
