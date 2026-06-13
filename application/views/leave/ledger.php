<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div style="margin-left: 250px; margin-top: 50px; padding-bottom: 90px;">

                <div class="card">
                    <div class="card-body">

                        <h3 style="font-weight: bold;">Leave Balance Ledger</h3>

                        <form method="get" action="<?= base_url('leave_balance_admin/ledger'); ?>" class="mb-3">
                            <div class="row">
                                <div class="col-md-4">
                                    <label>Staff ID Number</label>
                                    <input type="text"
                                           name="staff_idnumber"
                                           class="form-control"
                                           value="<?= html_escape($staff_idnumber); ?>"
                                           placeholder="Enter Staff ID Number">
                                </div>

                                <div class="col-md-2" style="margin-top: 28px;">
                                    <button type="submit" class="btn btn-primary">
                                        Search
                                    </button>
                                </div>
                            </div>
                        </form>

                        <?php if (!empty($employee)): ?>
                            <div class="alert alert-info">
                                <strong>Employee:</strong>
                                <?= html_escape($employee->FirstName . ' ' . $employee->LastName); ?>
                                <br>
                                <strong>ID Number:</strong>
                                <?= html_escape($employee->IDNumber); ?>
                                <br>
                                <strong>Position:</strong>
                                <?= html_escape($employee->empPosition); ?>
                            </div>
                        <?php elseif ($staff_idnumber !== ''): ?>
                            <div class="alert alert-warning">
                                No employee found for this Staff ID.
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($balances)): ?>
                            <h5 style="font-weight: bold;">Current / Yearly Balances</h5>

                            <div class="table-responsive">
                                <table class="table table-bordered table-sm">
                                    <thead>
                                        <tr>
                                            <th>Year</th>
                                            <th>VL</th>
                                            <th>SL</th>
                                            <th>SPL</th>
                                            <th>Forced</th>
                                            <th>Solo Parent</th>
                                            <th>Wellness</th>
                                            <th>COC Hours</th>
                                            <th>VSC Days</th>
                                            <th>Updated</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($balances as $b): ?>
                                            <tr>
                                                <td><?= html_escape($b->balance_year); ?></td>
                                                <td><?= number_format((float)$b->vl_balance, 2); ?></td>
                                                <td><?= number_format((float)$b->sl_balance, 2); ?></td>
                                                <td><?= number_format((float)$b->spl_balance, 2); ?></td>
                                                <td><?= number_format((float)$b->forced_leave_balance, 2); ?></td>
                                                <td><?= number_format((float)$b->solo_parent_balance, 2); ?></td>
                                                <td><?= number_format((float)$b->wellness_balance, 2); ?></td>
                                                <td><?= number_format((float)$b->coc_balance_hours, 2); ?></td>
                                                <td><?= number_format((float)$b->vsc_balance_days, 2); ?></td>
                                                <td><?= html_escape($b->updated_at); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>

                       <?php if (!empty($ledger)): ?>
                    <h5 style="font-weight: bold; margin-top: 25px;">Ledger History</h5>

                    <div class="table-responsive"  style="text-align: center;">
                        <table class="table table-hover table-bordered align-middle">
                            <thead style="background-color: #f5f6fa;">
                                <tr>
                                    <th style="width: 100px;">Reference #</th>
                                    <th style="width: 260px;">Date</th>
                                    <th style="width: 120px;">Type</th>
                                    <th style="width: 120px;">Transaction</th>
                                    <th style="width: 120px;">Quantity</th>
                                    <th style="width: 150px;">Performed By</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php foreach ($ledger as $l): ?>

                                    <?php
                                        $clean_remarks = $l->remarks;

                                        if (stripos($clean_remarks, 'Reason:') !== false) {
                                            $parts = explode('Reason:', $clean_remarks);
                                            $clean_remarks = trim(end($parts));
                                        }
                                    ?>

                                    <tr>
                                        <td>
                                            #<?= str_pad((int)$l->id, 6, '0', STR_PAD_LEFT); ?>
                                        </td>

                                        <td>
                                            <?= date('M d, Y h:i A', strtotime($l->created_at)); ?>
                                        </td>

                                        

                                        <td>
                                            <?= html_escape($l->leave_type_code); ?>
                                        </td>

                                        <td>
                                            <?php
                                                $transaction_type = strtoupper(trim((string)$l->transaction_type));
                                            ?>

                                            <?php if ($transaction_type === 'ADD'): ?>
                                                <span class="badge badge-success px-3 py-2">
                                                    ADD
                                                </span>

                                            <?php elseif ($transaction_type === 'DEDUCT'): ?>
                                                <span class="badge badge-danger px-3 py-2">
                                                    DEDUCT
                                                </span>

                                            <?php elseif ($transaction_type === 'RESTORE'): ?>
                                                <span class="badge badge-info px-3 py-2">
                                                    RESTORE
                                                </span>

                                            <?php elseif ($transaction_type === 'ADJUST'): ?>
                                                <span class="badge badge-primary px-3 py-2">
                                                    ADJUST
                                                </span>

                                            <?php else: ?>
                                                <span class="badge badge-secondary px-3 py-2">
                                                    <?= html_escape($transaction_type); ?>
                                                </span>
                                            <?php endif; ?>
                                        </td>

                                        <td>
                                            <strong>
                                                <?= number_format((float)$l->quantity, 0); ?>
                                            </strong>
                                        </td>

                                        <td>
                                            <?= html_escape($l->performed_by); ?>
                                        </td>

                                        <td>
                                            <?= html_escape($clean_remarks); ?>
                                        </td>
                                    </tr>

                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php if (!empty($ledger) && $total_pages > 1): ?>

                    <div class="mt-3 d-flex justify-content-between align-items-center">

                        <div>
                            Showing latest ledger entries
                        </div>

                        <div>

                            <?php if ($current_page > 1): ?>
                                <a class="btn btn-sm btn-outline-primary"
                                href="<?= base_url('leave_balance_admin/ledger?staff_idnumber=' . urlencode($staff_idnumber) . '&page=' . ($current_page - 1)); ?>">
                                    Previous
                                </a>
                            <?php endif; ?>

                            <span style="margin: 0 10px;">
                                Page <?= (int)$current_page; ?> of <?= (int)$total_pages; ?>
                            </span>

                            <?php if ($current_page < $total_pages): ?>
                                <a class="btn btn-sm btn-outline-primary"
                                href="<?= base_url('leave_balance_admin/ledger?staff_idnumber=' . urlencode($staff_idnumber) . '&page=' . ($current_page + 1)); ?>">
                                    Next
                                </a>
                            <?php endif; ?>

                        </div>

                    </div>

                <?php endif; ?>
                    </div>

                <?php elseif ($staff_idnumber !== ''): ?>
                    <div class="alert alert-secondary">
                        No ledger records found.
                    </div>
                <?php endif; ?>







                            <?php if (!empty($balances)): ?>
                                <h5 style="font-weight: bold; margin-top: 25px;">Manual Balance Adjustment</h5>

                                <form method="post" action="<?= base_url('leave_balance_admin/adjust_balance'); ?>">
                                    <input type="hidden" name="staff_idnumber" value="<?= html_escape($staff_idnumber); ?>">

                                    <div class="row">
                                        <div class="col-md-2">
                                            <label>Year</label>
                                            <select name="balance_year" class="form-control" required>
                                                <?php foreach ($balances as $b): ?>
                                                    <option value="<?= html_escape($b->balance_year); ?>">
                                                        <?= html_escape($b->balance_year); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div class="col-md-3">
                                            <label>Balance Field</label>
                                                <select name="leave_type_code" class="form-control" required>
                                                    <option value="VL">Vacation Leave</option>
                                                    <option value="SL">Sick Leave</option>
                                                    <option value="SPL">SPL</option>
                                                    <option value="FL">Forced Leave</option>
                                                    <option value="SOLO_PARENT">Solo Parent</option>
                                                    <option value="WELLNESS">Wellness</option>
                                                    <option value="COC">COC Hours</option>
                                                    <option value="VSC">VSC Days</option>
                                                </select>
                                        </div>

                                        <div class="col-md-2">
                                            <label>Action</label>
                                        
                                            <select name="action" class="form-control" required>
                                                
                                                <option value="ADD">Add Credits</option>
                                                <option value="SUBTRACT">Subtract Credits</option>
                                                <option value="SET">Set / Adjust Balance</option>
                                            </select>
                                        </div>

                                        <div class="col-md-2">
                                            <label>Value</label>
                                            <input type="number" step="0.01" min="0.01" name="quantity" class="form-control" required>
                                        </div>

                                        <div class="col-md-3">
                                            <label>Remarks</label>
                                            <input type="text" name="remarks" class="form-control" required placeholder="Required reason">
                                        </div>
                                    </div>

                                    <button type="submit"
                                            class="btn btn-danger mt-3"
                                            onclick="return confirm('Confirm manual balance adjustment? This will be recorded in ledger.');">
                                        Save Adjustment
                                    </button>
                                </form>
                            <?php endif; ?>







                    </div>
                </div>













                
            </div>
        </div>
    </div>
</div>