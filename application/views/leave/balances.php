<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="container-fluid">
    <div class="page-title-box">
        <h4 class="page-title"><?= html_escape($title); ?></h4>
    </div>

    <div class="card-box">
        <table class="table table-bordered">
            <tr><th>Vacation Leave</th><td><?= number_format((float)$balance['vl_balance'], 2); ?></td></tr>
            <tr><th>Sick Leave</th><td><?= number_format((float)$balance['sl_balance'], 2); ?></td></tr>
            <tr><th>Special Privilege Leave</th><td><?= number_format((float)$balance['spl_balance'], 2); ?></td></tr>
            <tr><th>Forced Leave Availed</th><td><?= number_format((float)$balance['forced_leave_availed'], 2); ?></td></tr>
            <tr><th>Solo Parent Leave</th><td><?= number_format((float)$balance['solo_parent_balance'], 2); ?></td></tr>
            <tr><th>COC Hours</th><td><?= number_format((float)$balance['coc_balance_hours'], 2); ?></td></tr>
            <tr><th>Teacher Service Credits</th><td><?= number_format((float)$balance['vsc_balance_days'], 2); ?></td></tr>
        </table>
    </div>
</div>
