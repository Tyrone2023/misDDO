<div class="container-fluid">
    <div style="margin-left: 250px; margin-top: 150px;">

        <div class="row">
            <div class="col-12">
                <h4 class="page-title">
                    <?= isset($title) ? $title : 'Upload Leave Balances'; ?>
                </h4>
            </div>
        </div>

        <!-- Alerts -->
        <?php if ($this->session->flashdata('success')): ?>
            <div class="alert alert-success">
                <?= $this->session->flashdata('success'); ?>
            </div>
        <?php endif; ?>

        <?php if ($this->session->flashdata('error')): ?>
            <div class="alert alert-danger">
                <?= $this->session->flashdata('error'); ?>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-8">

                <div class="card-box">

                    <h4 class="header-title mb-3">
                        Upload Converted Excel Template
                    </h4>

                    <form method="post"
                        action="<?= site_url('leave_balance_admin/process_upload'); ?>"
                        enctype="multipart/form-data"
                        onsubmit="this.querySelector('button[type=submit]').disabled=true; this.querySelector('button[type=submit]').innerHTML='Uploading...';">
                         

                        <div class="form-group">
                            <label>
                                Select Excel File (.xls / .xlsx)
                            </label>

                            <input type="file"
                                   name="excel_file"
                                   class="form-control"
                                   accept=".xls,.xlsx"
                                   required>
                        </div>

                        <div class="alert alert-info mt-3">
                            <strong>Supported Sheets:</strong><br>
                            • leave_credit_upload<br>
                            • coc_upload
                        </div>

                        <div class="alert alert-light border">
                            <strong>Required Column:</strong>
                            staff_idnumber
                            <hr>

                            <strong>Supported Leave Columns:</strong><br>
                            VL / SL / SPL / Forced Leave / Wellness / Solo Parent / COC
                        </div>

                        <button type="submit"
                                name="upload_leave_balance"
                                value="1"
                                class="btn btn-primary">
                            <i class="mdi mdi-upload"></i>
                            Upload File
                        </button>

                        <a href="<?= base_url('leave_balance_admin/upload'); ?>"
                           class="btn btn-secondary">
                            <i class="mdi mdi-history"></i>
                            Upload History
                        </a>

                    </form>

                </div>

            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card-box">

                    <h4 class="header-title mb-3">
                        Latest Uploaded Leave Balances
                    </h4>

                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>Staff ID</th>
                                    <th>Employee</th>
                                    <th>Year</th>
                                    <th>VL</th>
                                    <th>SL</th>
                                    <th>SPL</th>
                                    <th>FL</th>
                                    <th>Solo Parent</th>
                                    <th>Wellness</th>
                                    <th>COC Hours</th>
                                    <th>VSC Days</th>
                                    <th>Updated By</th>
                                    <th>Updated At</th>
                                    <th>Source File</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if (!empty($latest_balances)): ?>
                                    <?php foreach ($latest_balances as $b): ?>
                                        <?php
                                            $employee_name = trim(
                                                ($b->FirstName ?? '') . ' ' .
                                                ($b->MiddleName ?? '') . ' ' .
                                                ($b->LastName ?? '') . ' ' .
                                                ($b->NameExtn ?? '')
                                            );
                                        ?>

                                        <tr>
                                            <td><?= htmlspecialchars((string)$b->staff_idnumber); ?></td>
                                            <td>
                                                <strong><?= htmlspecialchars($employee_name); ?></strong><br>
                                                <small class="text-muted">
                                                    <?= htmlspecialchars((string)($b->empPosition ?? '')); ?>
                                                </small>
                                            </td>
                                            <td><?= htmlspecialchars((string)$b->balance_year); ?></td>
                                            <td><?= number_format((float)$b->vl_balance, 2); ?></td>
                                            <td><?= number_format((float)$b->sl_balance, 2); ?></td>
                                            <td><?= number_format((float)$b->spl_balance, 2); ?></td>
                                            <td><?= number_format((float)$b->fl_balance, 2); ?></td>
                                            <td><?= number_format((float)$b->solo_parent_balance, 2); ?></td>
                                            <td><?= number_format((float)$b->wellness_balance, 2); ?></td>
                                            <td><?= number_format((float)$b->coc_balance_hours, 2); ?></td>
                                            <td><?= number_format((float)$b->vsc_balance_days, 2); ?></td>
                                            <td><?= htmlspecialchars((string)($b->updated_by ?? '')); ?></td>
                                            <td><?= htmlspecialchars((string)($b->updated_at ?? '')); ?></td>
                                            <td><?= htmlspecialchars((string)($b->source_file ?? '')); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="14" class="text-center">
                                            No uploaded balances found.
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