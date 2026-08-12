<?php
/*
 * "Applicants QS" modal for one criterion of the position being rated.
 *
 * The picker is built from the position's own scoring criteria (Positions
 * Settings -> Scoring Criteria): choosing an increment level drops its points
 * into the Rating box, and the evaluator's note goes in Remarks. The point
 * ceiling comes from the same sheet, so the maximum this form will accept is
 * whatever that criterion is worth for this position title.
 *
 * Expected:
 *   qs_class        modal class used by the button that opens it ("perfqs")
 *   qs_title        modal heading
 *   qs_header_bg    header colour class, matching the section it belongs to
 *   qs_button_class submit button colour class
 *   qs_col          hris_rating_none column this scores into ("performance")
 *   qs_remark_col   hris_applications column the note goes in
 *   qs_message      what the audit trail records
 *   qs_max          maximum points for this criterion on this position
 *   qs_levels       increment levels: objects with ->description and ->points
 *   qs_value        the rating already on file, or '' when not yet rated
 *   qs_remarks      the note already on file
 *   qs_app_id, qs_record_no, qs_emp_email
 */

$qs_select_id = 'qs-sel-' . $qs_class;
$qs_input_id  = 'qs-val-' . $qs_class;
?>
<div class="modal fade <?= $qs_class; ?>" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header <?= $qs_header_bg; ?>">
                <h5 class="modal-title text-white"><?= html_escape($qs_title); ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <p class="text-danger"><i>Note: Maximum allowed value is <?= $qs_max; ?></i></p>
                        <div class="card">
                            <div class="card-body">

                                <form class="parsley-examples" action="<?= base_url(); ?>pages/update_rate_none/<?= $this->uri->segment(3); ?>/<?= $this->uri->segment(4); ?>/0/<?= $qs_app_id; ?>" method="post">
                                    <input type="hidden" name="app_id" value="<?= $qs_app_id; ?>">
                                    <input type="hidden" name="empEmail" value="<?= html_escape($qs_emp_email); ?>">
                                    <input type="hidden" name="record_no" value="<?= html_escape($qs_record_no); ?>">
                                    <input type="hidden" name="school_id" value="<?= $this->uri->segment(5); ?>">
                                    <input type="hidden" name="page" value="<?= $this->uri->segment(2); ?>">
                                    <input type="hidden" name="col" value="<?= $qs_col; ?>">
                                    <input type="hidden" name="message" value="<?= html_escape($qs_message); ?>">
                                    <input type="hidden" name="maxpoint" value="<?= $qs_max; ?>">
                                    <input type="hidden" name="remark_col" value="<?= $qs_remark_col; ?>">

                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <label>Rating Criteria</label>
                                                <select class="form-control qs-select" id="<?= $qs_select_id; ?>" data-rating-input="<?= $qs_input_id; ?>">
                                                    <option disabled selected></option>
                                                    <?php foreach ($qs_levels as $level) : ?>
                                                        <option value="<?= $level->points; ?>">
                                                            <?= html_escape($level->description); ?>
                                                            &mdash; <?= rtrim(rtrim(number_format($level->points, 2, '.', ''), '0'), '.'); ?> pt<?= $level->points == 1 ? '' : 's'; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <?php if (empty($qs_levels)) : ?>
                                                    <small class="text-muted">
                                                        No increment levels set for this criterion yet &mdash; add them under
                                                        Positions Settings &rarr; Scoring Criteria, or type the rating below.
                                                    </small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <label>Rating</label>
                                                <input type="text" class="form-control" name="<?= $qs_col; ?>" id="<?= $qs_input_id; ?>" value="<?= html_escape($qs_value); ?>">
                                            </div>

                                            <div class="form-group">
                                                <label>Remarks</label>
                                                <textarea name="remarks" rows="3" class="form-control"><?= html_escape($qs_remarks); ?></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group text-right mb-0">
                                        <button class="btn <?= $qs_button_class; ?> waves-effect waves-light mr-1" type="submit">
                                            Submit
                                        </button>
                                    </div>
                                </form>

                            </div>
                        </div>
                        <!-- end card -->
                    </div>
                    <!-- end col -->
                </div>
                <!-- end row -->
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
