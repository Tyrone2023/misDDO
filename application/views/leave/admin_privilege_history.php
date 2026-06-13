<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div style="margin-left: 250px;">
                <div class="page-title-box"><h4 class="mb-0 font-size-18">Privilege History</h4></div>

                <div class="card mb-3">
                    <div class="card-body">
                        <form method="get" action="<?= base_url('leave_balance_admin/privilege_history'); ?>" class="form-row align-items-end">
                            <div class="col-md-2 mb-2">
                                <label>Staff ID</label>
                                <input type="text" name="staff_idnumber" class="form-control" value="<?= htmlspecialchars((string)($filters['staff_idnumber'] ?? '')); ?>">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label>Leave Type</label>
                                <select name="leave_type_code" class="form-control">
                                    <option value="">All</option>
                                    <?php foreach ($controlled_leave_types as $code => $label): ?>
                                        <option value="<?= htmlspecialchars($code); ?>" <?= (($filters['leave_type_code'] ?? '') === $code ? 'selected' : ''); ?>><?= htmlspecialchars($label); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2 mb-2">
                                <label>Action</label>
                                <select name="action" class="form-control">
                                    <option value="">All</option>
                                    <?php foreach (array('APPROVED','DENIED','PENDING') as $status): ?>
                                        <option value="<?= $status; ?>" <?= (($filters['action'] ?? '') === $status ? 'selected' : ''); ?>><?= $status; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2 mb-2">
                                <label>Date From</label>
                                <input type="date" name="date_from" class="form-control" value="<?= htmlspecialchars((string)($filters['date_from'] ?? '')); ?>">
                            </div>
                            <div class="col-md-2 mb-2">
                                <label>Date To</label>
                                <input type="date" name="date_to" class="form-control" value="<?= htmlspecialchars((string)($filters['date_to'] ?? '')); ?>">
                            </div>
                            <div class="col-md-1 mb-2">
                                <button type="submit" class="btn btn-primary">Go</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Staff ID</th>
                                        <th>Employee</th>
                                        <th>Leave Type</th>
                                        <th>Action</th>
                                        <th>Remarks</th>
                                        <th>Acted By</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($logs)): ?>
                                        <?php foreach ($logs as $row): ?>
                                            <tr>
                                                <td><?= htmlspecialchars((string)$row->created_at); ?></td>
                                                <td><?= htmlspecialchars((string)$row->staff_idnumber); ?></td>
                                                <td><?= htmlspecialchars(trim((string)$row->FirstName . ' ' . (string)$row->LastName)); ?></td>
                                                <td><?= htmlspecialchars((string)$row->leave_type_code); ?></td>
                                                <td><?= htmlspecialchars((string)$row->action); ?></td>
                                                <td><?= htmlspecialchars((string)$row->remarks); ?></td>
                                                <td><?= htmlspecialchars((string)($row->acted_by_username ?: $row->acted_by)); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="7" class="text-center">No privilege history found.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
