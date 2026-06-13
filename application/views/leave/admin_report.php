<div class="container-fluid">
    <h4><?= $title; ?></h4>
    <table class="table table-bordered">
        <tr><th>Approved</th><td><?= (int)$summary['approved']; ?></td></tr>
        <tr><th>Pending</th><td><?= (int)$summary['pending']; ?></td></tr>
        <tr><th>Rejected</th><td><?= (int)$summary['rejected']; ?></td></tr>
        <tr><th>Cancelled</th><td><?= (int)$summary['cancelled']; ?></td></tr>
    </table>
</div>
