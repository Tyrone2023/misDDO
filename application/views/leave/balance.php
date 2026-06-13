<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div style="margin-left: 250px; margin-top: 100px;">

                <div class="card">
                    <div class="card-body">

                        <h5>Regular Leave Balances</h5>

                        <?php
                        $balances = is_array($balances ?? null) ? $balances : array();

                        $balance_rows = array(
                            'Vacation Leave'               => $balances['vl_balance'] ?? 0,
                            'Sick Leave'                   => $balances['sl_balance'] ?? 0,
                            'Special Privilege Leave'      => $balances['spl_balance'] ?? 0,
                            'Forced Leave'                 => $balances['forced_leave_balance'] ?? $balances['fl_balance'] ?? 0,
                            'Solo Parent Leave'            => $balances['solo_parent_balance'] ?? 0,
                            'Wellness Leave'               => $balances['wellness_balance'] ?? 0,
                            'COC (Hours)'                  => $balances['coc_balance_hours'] ?? $balances['coc_balance'] ?? 0,
                            'VSC / Service Credits (Days)' => $balances['vsc_balance_days'] ?? 0,
                        );
                        ?>

                                <table class="table table-bordered table-sm">
                                    <thead>
                                        <tr>
                                            <th>Leave Credit Type</th>
                                            <th>Available Balance</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($balance_rows as $label => $value): ?>
                                            <tr>
                                                <th><?= htmlspecialchars($label); ?></th>
                                                <td><?= number_format((float)$value, 2); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>

                    </div>
                </div>

                <!-- ENTITLEMENTS -->
                <div class="card mt-3">
                    <div class="card-body">

                        <h5>Privilege Leave Credits</h5>

                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>Leave Type</th>
                                    <th>Credits</th>
                                    <th>Used</th>
                                    <th>Remaining</th>
                                    <th>Status</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if (!empty($entitlements)): ?>
                                    <?php foreach ($entitlements as $e): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($e->leave_type_code); ?></td>

                                            <td><?= number_format((float)$e->credits, 2); ?></td>

                                            <td><?= number_format((float)$e->used, 2); ?></td>

                                            <td>
                                                <?= number_format((float)$e->credits - (float)$e->used, 2); ?>
                                            </td>

                                            <td>
                                                <span class="badge badge-<?= 
                                                    $e->status === 'APPROVED' ? 'success' : 
                                                    ($e->status === 'DENIED' ? 'danger' : 'warning'); ?>">
                                                    <?= htmlspecialchars($e->status); ?>
                                                </span>
                                            </td>

                                            <td><?= htmlspecialchars($e->action_reason ?? ''); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">
                                            No entitlement records found.
                                        </td>
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