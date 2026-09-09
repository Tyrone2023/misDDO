<?php
defined('BASEPATH') or exit('No direct script access allowed');

$supportingLabel = (string) ($supporting_label ?? 'Supporting Document');
$supportingType = (string) ($supporting_type ?? '');
?>
<div class="modal fade supporting-document-<?= htmlspecialchars($supportingType, ENT_QUOTES, 'UTF-8'); ?>" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title text-white"><?= htmlspecialchars($supportingLabel, ENT_QUOTES, 'UTF-8'); ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                <?= form_open_multipart('pages/update_supporting_document', ['class' => 'parsley-examples']); ?>
                    <input type="hidden" name="document_type" value="<?= htmlspecialchars($supportingType, ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="id" value="<?= (int) ($supporting_applicant_id ?? 0); ?>">
                    <input type="hidden" name="jobID" value="<?= (int) ($supporting_job_id ?? 0); ?>">
                    <input type="hidden" name="school_id" value="<?= htmlspecialchars((string) ($supporting_school_id ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="appID" value="<?= (int) ($supporting_app_id ?? 0); ?>">

                    <div class="form-group row">
                        <label class="col-md-4 col-form-label">Select PDF File<span class="text-danger">*</span></label>
                        <div class="col-md-8">
                            <input type="file" name="file" accept="application/pdf,.pdf" required class="form-control">
                        </div>
                    </div>

                    <div class="form-group row mb-0">
                        <div class="col-md-8 offset-md-4">
                            <button type="submit" class="btn btn-info waves-effect waves-light">Upload</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
