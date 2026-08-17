<?php
/**
 * Deny a retention request with a reason. The reason is shown to the applicant
 * on Pages/request_rating_applicant.
 *
 * Expects: $requestId - hris_rating_request.id
 * Optional: $returnUrl - where to land after the decision (defaults to the
 *           Retained Rating Request list, server side).
 */
$denyRequestId = (int)($requestId ?? 0);
$denyReturnUrl = trim((string)($returnUrl ?? ''));
?>
<div class="modal fade" id="denyRetentionModal" tabindex="-1" role="dialog" aria-labelledby="denyRetentionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="<?= base_url('Pages/request_rating_deny'); ?>" method="post">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title text-white" id="denyRetentionModalLabel">
                        <i class="mdi mdi-close-circle-outline mr-1"></i>Deny Retention Request
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" value="<?= $denyRequestId; ?>">
                    <?php if ($denyReturnUrl !== '') : ?>
                        <input type="hidden" name="return_url" value="<?= htmlspecialchars($denyReturnUrl, ENT_QUOTES, 'UTF-8'); ?>">
                    <?php endif; ?>
                    <p class="text-muted">
                        No score will be retained. The applicant will see this request as
                        <strong>Denied</strong> together with the reason you write below.
                    </p>
                    <div class="form-group mb-0">
                        <label for="deny_reason">Reason for denial <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="deny_reason" name="deny_reason" rows="4" maxlength="2000" required
                                  placeholder="Explain why the retention of ratings is not granted."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light waves-effect" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger waves-effect">
                        <i class="mdi mdi-close-circle-outline mr-1"></i> Deny Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
