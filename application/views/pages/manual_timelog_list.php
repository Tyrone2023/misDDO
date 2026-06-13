<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Manual Timelog List</h4>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <a href="<?= base_url('System_settings/manual_timelog'); ?>" class="btn btn-primary mb-3">Add Manual Timelog</a>

            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Staff ID</th>
                            <th>Employee No</th>
                            <th>Name</th>
                            <th>Department</th>
                            <th>Log DateTime</th>
                            <th>Source</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($logs)): $i = 1; ?>
                            <?php foreach ($logs as $row): ?>
                                <tr>
                                    <td><?= $i++; ?></td>
                                    <td><?= $row->mapped_staff_idnumber; ?></td>
                                    <td><?= $row->employee_no; ?></td>
                                    <td><?= $row->employee_name; ?></td>
                                    <td><?= $row->department; ?></td>
                                    <td><?= date('M d, Y h:i A', strtotime($row->log_datetime)); ?></td>
                                    <td><?= $row->location_id; ?></td>
                                    <td><?= $row->card_no; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center">No manual timelogs found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>