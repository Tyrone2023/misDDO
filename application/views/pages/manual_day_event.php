<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Manual Day Event Entry</h4>
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
        <div class="col-md-5">
            <div class="card">
                <div class="card-body">
                    <form method="post" action="<?= base_url('System_settings/save_manual_day_event'); ?>">
                        <div class="mb-3">
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

                        <div class="mb-3">
                            <label>Date</label>
                            <input type="date" name="event_date" class="form-control" required value="<?= date('Y-m-d'); ?>">
                        </div>

                        <div class="mb-3">
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

                        <div class="mb-3">
                            <label>Title</label>
                            <input type="text" name="event_title" class="form-control" placeholder="Optional title">
                        </div>

                        <div class="mb-3">
                            <label>Remarks</label>
                            <textarea name="remarks" class="form-control" rows="3"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">Save Day Event</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card">
                <div class="card-body">
                    <h5>Recent Day Events</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Employee</th>
                                    <th>Type</th>
                                    <th>Title</th>
                                    <th>Remarks</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($events)): ?>
                                    <?php foreach ($events as $row): ?>
                                        <tr>
                                            <td><?= $row->event_date; ?></td>
                                            <td><?= $row->LastName . ', ' . $row->FirstName . ' ' . $row->MiddleName; ?></td>
                                            <td><?= $row->event_type; ?></td>
                                            <td><?= $row->event_title; ?></td>
                                            <td><?= $row->remarks; ?></td>
                                            <td>
                                                <a href="<?= base_url('System_settings/delete_manual_day_event/' . $row->id); ?>"
                                                   class="btn btn-sm btn-danger"
                                                   onclick="return confirm('Delete this event?');">
                                                   Delete
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center">No events found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>