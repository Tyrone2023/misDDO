<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div style="margin-left: 250px;margin-top: 50px;">

                <div class="page-title-box">
                    <h4 class="mb-0 font-size-18">Leave Privileges Dashboard</h4>
                    <p class="text-muted mb-0">HR approval dashboard for non-mandatory leave entitlements.</p>
                </div>

                <?php if ($this->session->flashdata('success')): ?>
                    <div class="alert alert-success"><?= $this->session->flashdata('success'); ?></div>
                <?php endif; ?>

                <?php if ($this->session->flashdata('error')): ?>
                    <div class="alert alert-danger"><?= $this->session->flashdata('error'); ?></div>
                <?php endif; ?>

                <div class="card mb-3">
                    <div class="card-body">
                        <h5>
                            Pending Requests
                            <?php if (!empty($pending_entitlements)): ?>
                                <span class="badge badge-danger"><?= count($pending_entitlements); ?></span>
                            <?php endif; ?>
                        </h5>

                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th>Date Requested</th>
                                        <th>Employee</th>
                                        <th>Position</th>
                                        <th>Office</th>
                                        <th>Leave Type</th>
                                        <th>Credits</th>
                                        <th>Attachment</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($pending_entitlements)): ?>
                                        <?php foreach ($pending_entitlements as $e): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($e->created_at); ?></td>

                                                <td>
                                                    <strong>
                                                        <?= htmlspecialchars(trim(($e->LastName ?? '') . ', ' . ($e->FirstName ?? '') . ' ' . ($e->MiddleName ?? ''))); ?>
                                                    </strong>
                                                    <br>
                                                    <small><?= htmlspecialchars($e->staff_idnumber); ?></small>
                                                </td>

                                                <td><?= htmlspecialchars($e->empPosition ?? ''); ?></td>

                                                <td><?= htmlspecialchars($e->Department ?? ''); ?></td>

                                                <td>
                                                    <span class="badge badge-info">
                                                        <?= htmlspecialchars($e->leave_type_code); ?>
                                                    </span>
                                                </td>

                                                <td><?= number_format((float)$e->credits, 2); ?></td>

                                                <td>
                                                    <?php if (!empty($e->attachment_path)): ?>
                                                        <a href="<?= base_url($e->attachment_path); ?>" target="_blank" class="btn btn-info btn-sm">
                                                            View
                                                        </a>
                                                    <?php else: ?>
                                                        <span class="text-danger">No Attachment</span>
                                                    <?php endif; ?>
                                                </td>

                                                <td>
                                                    <a href="<?= base_url('leave_balance_admin/approve_entitlement/' . $e->id); ?>"
                                                       class="btn btn-success btn-sm"
                                                       onclick="return confirm('Approve this leave entitlement?');">
                                                        Approve
                                                    </a>

                                                    <a href="<?= base_url('leave_balance_admin/deny_entitlement/' . $e->id); ?>"
                                                       class="btn btn-danger btn-sm"
                                                       onclick="return confirm('Deny this leave entitlement?');">
                                                        Deny
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="text-center text-muted">
                                                No pending entitlement requests.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h5>Recently Processed Requests</h5>

                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th>Processed Date</th>
                                        <th>Employee</th>
                                        <th>Leave Type</th>
                                        <th>Credits</th>
                                        <th>Used</th>
                                        <th>Available</th>
                                        <th>Status</th>
                                        <th>Processed By</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($processed_entitlements)): ?>
                                        <?php foreach ($processed_entitlements as $e): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($e->approved_at); ?></td>

                                                <td>
                                                    <?= htmlspecialchars(trim(($e->LastName ?? '') . ', ' . ($e->FirstName ?? ''))); ?>
                                                    <br>
                                                    <small><?= htmlspecialchars($e->staff_idnumber); ?></small>
                                                </td>

                                                <td><?= htmlspecialchars($e->leave_type_code); ?></td>

                                                <td><?= number_format((float)$e->credits, 2); ?></td>

                                                <td><?= number_format((float)$e->used, 2); ?></td>

                                                <td><?= number_format((float)$e->credits - (float)$e->used, 2); ?></td>

                                                <td>
                                                    <?php if ($e->status === 'PENDING'): ?>

                                                        <form method="post" action="<?= base_url('leave_balance_admin/process_entitlement_action'); ?>">
                                                            <input type="hidden" name="id" value="<?= (int)$e->id; ?>">

                                                            <div class="form-group mb-1">
                                                                <textarea name="action_reason"
                                                                        class="form-control form-control-sm"
                                                                        rows="2"
                                                                        placeholder="Reason / remarks"
                                                                        required></textarea>
                                                            </div>

                                                            <button type="submit"
                                                                    name="action"
                                                                    value="APPROVED"
                                                                    class="btn btn-success btn-sm"
                                                                    onclick="return confirm('Approve this entitlement?');">
                                                                Approve
                                                            </button>

                                                            <button type="submit"
                                                                    name="action"
                                                                    value="DENIED"
                                                                    class="btn btn-danger btn-sm"
                                                                    onclick="return confirm('Deny this entitlement?');">
                                                                Deny
                                                            </button>
                                                        </form>

                                                    <?php else: ?>
                                                        <strong><?= htmlspecialchars($e->status); ?></strong><br>
                                                        <small><?= htmlspecialchars($e->action_reason ?? ''); ?></small>
                                                    <?php endif; ?>
                                                </td>

                                                <td><?= htmlspecialchars($e->approved_by); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="text-center text-muted">
                                                No processed entitlement records.
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
</div>