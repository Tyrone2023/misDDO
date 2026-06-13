<div class="container-fluid">
    <h4><?= $title; ?></h4>

    <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger"><?= $this->session->flashdata('error'); ?></div>
    <?php endif; ?>

    <form method="post" action="<?= base_url('leave/store'); ?>">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Leave Type</label>
                    <select name="leave_type_code" class="form-control" required>
                        <option value="">-- Select --</option>
                        <?php foreach ($leave_types as $type): ?>
                            <option value="<?= html_escape($type['code']); ?>"><?= html_escape($type['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Date From</label>
                    <input type="date" name="date_from" class="form-control" required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Date To</label>
                    <input type="date" name="date_to" class="form-control" required>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label>Half Day</label>
                    <select name="half_day" class="form-control">
                        <option value="NONE">None</option>
                        <option value="AM">AM</option>
                        <option value="PM">PM</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Commutation</label>
                    <select name="commutation" class="form-control">
                        <option value="NOT_REQUESTED">Not Requested</option>
                        <option value="REQUESTED">Requested</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Location / Illness / Other Detail</label>
                    <input type="text" name="location_detail" class="form-control" placeholder="Use for travel location if applicable">
                </div>
            </div>
        </div>

        <div class="form-group">
            <label>Study / Illness / Other Purpose Detail</label>
            <input type="text" name="study_purpose" class="form-control">
        </div>

        <div class="form-group">
            <label>Reason</label>
            <textarea name="reason" class="form-control" rows="4"></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Submit Leave Application</button>
    </form>
</div>
