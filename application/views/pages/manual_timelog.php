<style>
    .manual-timelog-wrapper {
        margin-left: 250px;
        width: calc(100% - 250px);
        padding: 20px;
    }

    @media (max-width: 991.98px) {
        .manual-timelog-wrapper {
            margin-left: 0;
            width: 100%;
            padding: 15px;
        }
    }

    .manual-timelog-wrapper .card {
        width: 100%;
        margin-bottom: 20px;
    }

    .manual-timelog-wrapper .btn {
        margin-right: 8px;
        margin-bottom: 8px;
    }
</style>

<div class="manual-timelog-wrapper">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="page-title">Manual Timelog Tools</h4>
                </div>
            </div>
        </div>

        <?php if ($this->session->flashdata('success')): ?>
            <div class="alert alert-success"><?= $this->session->flashdata('success'); ?></div>
        <?php endif; ?>

        <?php if ($this->session->flashdata('danger')): ?>
            <div class="alert alert-danger"><?= $this->session->flashdata('danger'); ?></div>
        <?php endif; ?>

        <div class="row">
            <div class="col-lg-8">

                <div class="card">
                    <div class="card-body">
                        <h5 class="mb-3">Generate Missing Logs for a Month</h5>

                        <form method="post" action="<?= base_url('System_settings/generate_missing_dtr_logs'); ?>" onsubmit="return confirm('Generate missing logs for this employee and month?');">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>Employee</label>
                                    <select name="staff_idnumber" class="form-control" required>
                                        <option value="">Select employee</option>
                                        <?php foreach ($employees as $emp): ?>
                                            <option value="<?= $emp->IDNumber; ?>">
                                                <?= $emp->LastName . ', ' . $emp->FirstName . ' ' . $emp->MiddleName; ?> (<?= $emp->employeeNo; ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label>Year</label>
                                    <input type="number" name="year" class="form-control" value="<?= date('Y'); ?>" required>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label>Month</label>
                                    <select name="month" class="form-control" required>
                                        <?php for ($m = 1; $m <= 12; $m++): ?>
                                            <option value="<?= $m; ?>" <?= ($m == date('n')) ? 'selected' : ''; ?>>
                                                <?= date('F', mktime(0, 0, 0, $m, 1)); ?>
                                            </option>
                                        <?php endfor; ?>
                                    </select>
                                </div>

                                <div class="col-12">
                                    <button type="submit" class="btn btn-warning">Generate Missing Logs</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h5 class="mb-3">Add Log for Specific Date and Time Slot</h5>

                        <form method="post" action="<?= base_url('System_settings/add_specific_dtr_log'); ?>" onsubmit="return confirm('Add random log to the selected slot?');">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>Employee</label>
                                    <select name="staff_idnumber" class="form-control" required>
                                        <option value="">Select employee</option>
                                        <?php foreach ($employees as $emp): ?>
                                            <option value="<?= $emp->IDNumber; ?>">
                                                <?= $emp->LastName . ', ' . $emp->FirstName . ' ' . $emp->MiddleName; ?> (<?= $emp->employeeNo; ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label>Date</label>
                                    <input type="date" name="dtr_date" class="form-control" required value="<?= date('Y-m-d'); ?>">
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label>Time Slot</label>
                                    <select name="slot" class="form-control" required>
                                        <option value="">Select slot</option>
                                        <option value="morning_in">Morning In</option>
                                        <option value="morning_out">Morning Out</option>
                                        <option value="afternoon_in">Afternoon In</option>
                                        <option value="afternoon_out">Afternoon Out</option>
                                    </select>
                                </div>

                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">Add Specific Log</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h5 class="mb-3">Delete Log for Specific Date and Time Slot</h5>

                        <form method="post" action="<?= base_url('System_settings/delete_specific_dtr_log'); ?>" onsubmit="return confirm('Delete the selected log slot?');">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>Employee</label>
                                    <select name="staff_idnumber" class="form-control" required>
                                        <option value="">Select employee</option>
                                        <?php foreach ($employees as $emp): ?>
                                            <option value="<?= $emp->IDNumber; ?>">
                                                <?= $emp->LastName . ', ' . $emp->FirstName . ' ' . $emp->MiddleName; ?> (<?= $emp->employeeNo; ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label>Date</label>
                                    <input type="date" name="dtr_date" class="form-control" required value="<?= date('Y-m-d'); ?>">
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label>Time Slot</label>
                                    <select name="slot" class="form-control" required>
                                        <option value="">Select slot</option>
                                        <option value="morning_in">Morning In</option>
                                        <option value="morning_out">Morning Out</option>
                                        <option value="afternoon_in">Afternoon In</option>
                                        <option value="afternoon_out">Afternoon Out</option>
                                    </select>
                                </div>

                                <div class="col-12">
                                    <button type="submit" class="btn btn-danger">Delete Specific Log</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                 <div class="card">
                    <div class="card-body">
                        <h5 class="mb-3">Add Day Event</h5>

                        <form method="post" action="<?= base_url('System_settings/save_manual_day_event'); ?>" onsubmit="return confirm('Save this day event?');">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label>Employee</label>
                                    <select name="staff_idnumber" class="form-control" required>
                                        <option value="">Select employee</option>
                                        <option value="ALL">All listed employees</option>
                                        <?php foreach ($employees as $emp): ?>
                                            <option value="<?= $emp->IDNumber; ?>">
                                                <?= $emp->LastName . ', ' . $emp->FirstName . ' ' . $emp->MiddleName; ?> (<?= $emp->employeeNo; ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-2 mb-3">
                                    <label>Date</label>
                                    <input type="date" name="event_date" class="form-control" required value="<?= date('Y-m-d'); ?>">
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label>Event Type</label>
                                    <select name="event_type" class="form-control" required>
                                        <option value="">Select type</option>
                                        <option value="Holiday">Holiday</option>
                                        <option value="Work From Home">Work From Home</option>
                                        <option value="Office Closure">Office Closure</option>
                                        <option value="Suspension">Suspension</option>
                                        <option value="Training">Training</option>
                                        <option value="Seminar">Seminar</option>
                                        <option value="Official Business">Official Business</option>
                                        <option value="Special Event">Special Event</option>
                                    </select>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label>Title</label>
                                    <input type="text" name="event_title" class="form-control" placeholder="Optional title">
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label>Remarks</label>
                                    <textarea name="remarks" class="form-control" rows="2" placeholder="Optional remarks"></textarea>
                                </div>

                                <div class="col-12">
                                    <button type="submit" class="btn btn-info">Add Event</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>                               
            </div>
        </div>

    </div>
</div>