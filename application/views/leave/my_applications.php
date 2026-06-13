<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div style="margin-left: 250px;">
                <div class="page-title-box">
                    <h4 class="mb-0 font-size-18">My Leave Applications</h4>
                </div>

                <?php if ($this->session->flashdata('success')): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($this->session->flashdata('success')); ?></div>
                <?php endif; ?>

                <?php if ($this->session->flashdata('error')): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($this->session->flashdata('error')); ?></div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th>Leave Type</th>
                                        <th>Date Filed</th>
                                        <th>Date From</th>
                                        <th>Date To</th>
                                        <th>Working Days</th>
                                        <th>Status</th>
                                        <th>Attachment</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($applications)): ?>
                                        <?php foreach ($applications as $row): ?>
                                            <?php $has_attachment = !empty($row->attachment_path); ?>
                                            <tr>
                                                <td><?= htmlspecialchars((string)($row->leave_type_code ?? '')); ?></td>
                                                <td><?= htmlspecialchars((string)($row->date_filed ?? '')); ?></td>
                                                <td><?= htmlspecialchars((string)($row->date_from ?? '')); ?></td>
                                                <td><?= htmlspecialchars((string)($row->date_to ?? '')); ?></td>
                                                <td><?= htmlspecialchars((string)($row->working_days ?? '')); ?></td>
                                                <td><?= htmlspecialchars((string)($row->status ?? '')); ?></td>
                                                <td>
                                                    <?php if ($has_attachment): ?>
                                                        <a href="<?= base_url($row->attachment_path); ?>" target="_blank" class="btn btn-primary btn-sm">Open PDF</a>
                                                    <?php else: ?>
                                                        -
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <a href="<?= base_url('leave/view_application/' . (int)$row->id); ?>" class="btn btn-sm btn-info">
                                                        View
                                                    </a>
                                                    <a href="<?= base_url('leave/print_preview/' . (int)$row->id); ?>" class="btn btn-sm btn-secondary" target="_blank">
                                                        Print Preview
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="text-center">No leave applications yet.</td>
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
