<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div style="margin-left: 250px;">

                <div class="page-title-box">
                    <h4 class="mb-0 font-size-18">Upload History</h4>
                </div>

                <div class="card mb-3">
                    <div class="card-body">
                        <form method="get"
                              action="<?= base_url('leave_balance_admin/upload_history'); ?>"
                              class="form-row align-items-end">

                            <div class="col-md-3 mb-2">
                                <label>Date From</label>
                                <input type="date"
                                       name="date_from"
                                       class="form-control"
                                       value="<?= htmlspecialchars((string)($filters['date_from'] ?? '')); ?>">
                            </div>

                            <div class="col-md-3 mb-2">
                                <label>Date To</label>
                                <input type="date"
                                       name="date_to"
                                       class="form-control"
                                       value="<?= htmlspecialchars((string)($filters['date_to'] ?? '')); ?>">
                            </div>

                            <div class="col-md-3 mb-2">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="">All</option>
                                    <option value="PROCESSING" <?= (($filters['status'] ?? '') === 'PROCESSING') ? 'selected' : ''; ?>>PROCESSING</option>
                                    <option value="COMPLETED" <?= (($filters['status'] ?? '') === 'COMPLETED') ? 'selected' : ''; ?>>COMPLETED</option>
                                    <option value="FAILED" <?= (($filters['status'] ?? '') === 'FAILED') ? 'selected' : ''; ?>>FAILED</option>
                                </select>
                            </div>

                            <div class="col-md-3 mb-2">
                                <button type="submit" class="btn btn-primary">
                                    Filter
                                </button>

                                <a href="<?= base_url('leave_balance_admin/upload_history'); ?>"
                                   class="btn btn-secondary">
                                    Reset
                                </a>
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
                                        <th>Date Uploaded</th>
                                        <th>Original File</th>
                                        <th>Stored File</th>
                                        <th>Total</th>
                                        <th>Inserted</th>
                                        <th>Updated</th>
                                        <th>Skipped</th>
                                        <th>COC</th>
                                        <th>Status</th>
                                        <th>Uploaded By</th>
                                        

                                        <th>Message</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php if (!empty($logs)): ?>
                                        <?php foreach ($logs as $row): ?>
                                            <tr>
                                                <td><?= htmlspecialchars((string)($row->uploaded_at ?? '')); ?></td>
                                                <td><?= htmlspecialchars((string)($row->original_filename ?? '')); ?></td>
                                                <td><?= htmlspecialchars((string)($row->stored_filename ?? '')); ?></td>
                                                <td><?= htmlspecialchars((string)($row->total_rows ?? 0)); ?></td>
                                                <td><?= htmlspecialchars((string)($row->inserted_rows ?? 0)); ?></td>
                                                <td><?= htmlspecialchars((string)($row->updated_rows ?? 0)); ?></td>
                                                <td><?= htmlspecialchars((string)($row->skipped_rows ?? 0)); ?></td>
                                                <td><?= htmlspecialchars((string)($row->coc_rows ?? 0)); ?></td>

                                                <td>
                                                    <?php
                                                        $status = strtoupper((string)($row->status ?? ''));

                                                        if ($status === 'COMPLETED') {
                                                            $badge = 'success';
                                                        } elseif ($status === 'FAILED') {
                                                            $badge = 'danger';
                                                        } else {
                                                            $badge = 'warning';
                                                        }
                                                    ?>

                                                    <span class="badge badge-<?= $badge; ?>">
                                                        <?= htmlspecialchars($status); ?>
                                                    </span>
                                                </td>

                                                <td><?= htmlspecialchars((string)($row->uploaded_by ?? '')); ?></td>
                                                
                                                
                                                <td><?= htmlspecialchars((string)($row->message ?? '')); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="13" class="text-center">
                                                No upload history found.
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