<div class="container-fluid">
    <h4><?= $title; ?></h4>

    <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger"><?= $this->session->flashdata('error'); ?></div>
    <?php endif; ?>

    <form method="post" action="<?= base_url('leave_monetization/store'); ?>">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Monetization Type</label>
                    <select name="monetization_type" class="form-control">
                        <?php if ($profile['leave_profile_code'] === 'TEACHING_DEPED'): ?>
                            <option value="TEACHER_SERVICE_CREDIT">Teacher Service Credit Monetization</option>
                        <?php else: ?>
                            <option value="REGULAR">Regular Monetization</option>
                            <option value="SPECIAL_50_PERCENT">Special Monetization (50% or more)</option>
                        <?php endif; ?>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Requested Days</label>
                    <input type="number" step="0.25" min="0" name="requested_days" class="form-control">
                </div>
            </div>
        </div>

        <div class="form-group">
            <label>Reason / Justification</label>
            <textarea name="reason" class="form-control" rows="4"></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Submit Monetization Request</button>
    </form>
</div>
