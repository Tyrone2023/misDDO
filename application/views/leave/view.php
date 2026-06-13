<div class="container-fluid">
    <h4><?= $title; ?></h4>
    <table class="table table-bordered">
        <tr><th>Application No.</th><td><?= html_escape($application['application_no']); ?></td></tr>
        <tr><th>Leave Type</th><td><?= html_escape($application['leave_type_code']); ?></td></tr>
        <tr><th>Date Filed</th><td><?= html_escape($application['date_filed']); ?></td></tr>
        <tr><th>Inclusive Dates</th><td><?= html_escape($application['date_from']); ?> to <?= html_escape($application['date_to']); ?></td></tr>
        <tr><th>Working Days</th><td><?= html_escape($application['working_days']); ?></td></tr>
        <tr><th>Status</th><td><?= html_escape($application['status']); ?></td></tr>
        <tr><th>Reason</th><td><?= nl2br(html_escape($application['reason'])); ?></td></tr>
    </table>

    <a href="<?= base_url('leave/export/' . $application['id']); ?>" class="btn btn-success">Export Form</a>
</div>
