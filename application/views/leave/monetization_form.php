<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="container-fluid">
    <div class="page-title-box">
        <h4 class="page-title"><?= html_escape($title); ?></h4>
    </div>

    <div class="card-box">
        <form method="post" action="<?= base_url('leave_monetization/store'); ?>" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-4">
                    <label>Monetization Type</label>
                    <select name="monetization_type" class="form-control" required>
                        <?php if ($profile_code === 'NON_TEACHING_CSC'): ?>
                            <option value="REGULAR">Regular Monetization</option>
                            <option value="SPECIAL_50_PERCENT">Special Monetization (50% or more)</option>
                        <?php else: ?>
                            <option value="TEACHER_SERVICE_CREDIT">Teacher Service Credit Monetization</option>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Requested Days</label>
                    <input type="number" step="0.01" min="0.5" name="requested_days" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label>Letter Request</label>
                    <input type="file" name="letter_request" class="form-control">
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-12">
                    <label>Reason</label>
                    <textarea name="reason" class="form-control" rows="4" required></textarea>
                </div>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Submit Monetization Request</button>
                <a href="<?= base_url('leave_monetization'); ?>" class="btn btn-secondary">Back</a>
            </div>
        </form>
    </div>
</div>
