<div class="container-fluid">
    <h4><?= $title; ?></h4>
    <a href="<?= base_url('leave_monetization/apply'); ?>" class="btn btn-primary mb-3">New Monetization Request</a>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>No.</th>
                <th>Type</th>
                <th>Requested Days</th>
                <th>Date Filed</th>
                <th>Status</th>
                <th width="140">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($requests as $row): ?>
                <tr>
                    <td><?= html_escape($row['monetization_no']); ?></td>
                    <td><?= html_escape($row['monetization_type']); ?></td>
                    <td><?= html_escape($row['requested_days']); ?></td>
                    <td><?= html_escape($row['date_filed']); ?></td>
                    <td><?= html_escape($row['status']); ?></td>
                    <td><a href="<?= base_url('leave_monetization/view/' . $row['id']); ?>" class="btn btn-sm btn-info">View</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
