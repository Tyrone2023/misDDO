<div class="container-fluid">
    <h4><?= $title; ?></h4>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Application No.</th>
                <th>Staff ID</th>
                <th>Type</th>
                <th>Dates</th>
                <th>Days</th>
                <th>Status</th>
                <th width="220">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($applications as $row): ?>
            <tr>
                <td><?= html_escape($row['application_no']); ?></td>
                <td><?= html_escape($row['staff_idnumber']); ?></td>
                <td><?= html_escape($row['leave_type_code']); ?></td>
                <td><?= html_escape($row['date_from']); ?> to <?= html_escape($row['date_to']); ?></td>
                <td><?= html_escape($row['working_days']); ?></td>
                <td><?= html_escape($row['status']); ?></td>
                <td>
                    <a href="<?= base_url('leave_admin/recommend/' . $row['id']); ?>" class="btn btn-sm btn-warning">Recommend</a>
                    <a href="<?= base_url('leave_admin/approve/' . $row['id']); ?>" class="btn btn-sm btn-success">Approve</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
