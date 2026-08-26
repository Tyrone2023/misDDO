<?php
$eligibilityTypes = [
    'No Eligibility', 'PBET', 'RA 4670', 'RA 6850', 'RA 1080', 'LET',
    'PD 907', 'PD 997', 'Civil Service Professional',
    'Civil Service Sub-Professional', 'Barangay Officials',
];
$h = static function ($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};
?>
<div class="modal fade" id="applicantEligibilityModal" tabindex="-1" role="dialog"
     aria-labelledby="applicantEligibilityModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <?= form_open('Pages/update_applicant_eligibility'); ?>
                <div class="modal-header bg-purple">
                    <h5 class="modal-title text-white" id="applicantEligibilityModalLabel">
                        <i class="mdi mdi-certificate-outline mr-1"></i>Edit Eligibility
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" value="<?= (int) $staff->id; ?>">
                    <input type="hidden" name="appID" value="<?= (int) $this->uri->segment(6); ?>">

                    <div class="form-group">
                        <label for="eligibilityType">Eligibility Type</label>
                        <input type="text" class="form-control" id="eligibilityType" name="csEligibility"
                               list="eligibilityTypeOptions" maxlength="45"
                               value="<?= $h($staff->csEligibility ?? ''); ?>"
                               placeholder="Select or enter an eligibility type">
                        <datalist id="eligibilityTypeOptions">
                            <?php foreach ($eligibilityTypes as $type): ?>
                                <option value="<?= $h($type); ?>">
                            <?php endforeach; ?>
                        </datalist>
                    </div>

                    <div class="form-group mb-0">
                        <label for="eligibilityRating">Eligibility Rating</label>
                        <input type="text" class="form-control" id="eligibilityRating" name="csEligibilityRating"
                               maxlength="45" value="<?= $h($staff->csEligibilityRating ?? ''); ?>"
                               placeholder="e.g. 85.00">
                        <small class="form-text text-muted">Leave blank when the eligibility has no numerical rating.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-purple">
                        <i class="mdi mdi-content-save-outline mr-1"></i>Save changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
