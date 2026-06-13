<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="container-fluid">
    <div class="page-title-box">
        <h4 class="page-title"><?= html_escape($title); ?></h4>
    </div>

    <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger"><?= $this->session->flashdata('error'); ?></div>
    <?php endif; ?>

    <div class="card-box">
        <form method="post" action="<?= base_url('leave/store'); ?>" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-4">
                    <label>Leave Type</label>
                    <select name="leave_type_code" class="form-control" required>
                        <option value="">Select leave type</option>
                        <?php foreach ($leave_rules as $code => $rule): ?>
                            <?php if (in_array($profile_code, $rule['allowed_profiles'], true)): ?>
                                <option value="<?= html_escape($code); ?>"><?= html_escape($rule['label']); ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Date From</label>
                    <input type="date" name="date_from" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label>Date To</label>
                    <input type="date" name="date_to" class="form-control" required>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-4">
                    <label>Half Day</label>
                    <select name="half_day" class="form-control">
                        <option value="NONE">Whole Day</option>
                        <option value="AM">AM Only</option>
                        <option value="PM">PM Only</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Commutation</label>
                    <select name="commutation" class="form-control">
                        <option value="REQUESTED">Requested</option>
                        <option value="NOT_REQUESTED">Not Requested</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Supporting File</label>
                    <input type="file" name="attachment_pdf" accept="application/pdf" class="form-control">
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-12">
                    <label>Reason / Details</label>
                    <textarea name="reason" class="form-control" rows="4" required></textarea>
                </div>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Submit Leave Application</button>
                <a href="<?= base_url('leave'); ?>" class="btn btn-secondary">Back</a>
            </div>
        </form>
    </div>
</div>
