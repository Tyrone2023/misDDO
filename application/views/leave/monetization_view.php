<div class="container-fluid">
    <h4><?= $title; ?></h4>
    <table class="table table-bordered">
        <tr><th>Request No.</th><td><?= html_escape($request['monetization_no']); ?></td></tr>
        <tr><th>Type</th><td><?= html_escape($request['monetization_type']); ?></td></tr>
        <tr><th>Requested Days</th><td><?= html_escape($request['requested_days']); ?></td></tr>
        <tr><th>Filed</th><td><?= html_escape($request['date_filed']); ?></td></tr>
        <tr><th>Status</th><td><?= html_escape($request['status']); ?></td></tr>
        <tr><th>Reason</th><td><?= nl2br(html_escape($request['reason'])); ?></td></tr>
    </table>

    <a href="<?= base_url('leave_monetization/export/' . $request['id']); ?>" class="btn btn-success">Export Monetization Form</a>
</div>
