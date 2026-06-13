<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div style="margin-left: 250px;">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0 font-size-18">Biometric Employee Mapping</h4>
            </div>

            <?php if ($this->session->flashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $this->session->flashdata('success'); ?>
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <?php if ($this->session->flashdata('danger')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $this->session->flashdata('danger'); ?>
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">Unmapped Biometric Records</h4>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Biometric No.</th>
                                    <th>ID Number</th>
                                    <th>Name</th>
                                    <th>Card No</th>
                                    <th>Department</th>
                                    <th>Total Logs</th>
                                    <th>Map to Staff</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($unmapped_logs)): ?>
                                    <?php foreach ($unmapped_logs as $log): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($log->employee_no); ?></td>
                                            <td><?= htmlspecialchars($log->id_number); ?></td>
                                            <td><?= htmlspecialchars($log->employee_name); ?></td>
                                            <td><?= htmlspecialchars($log->card_no); ?></td>
                                            <td><?= htmlspecialchars($log->department); ?></td>
                                            <td><?= htmlspecialchars($log->total_logs); ?></td>
                                            <td style="min-width: 320px;">
                                                <form method="post" action="<?= base_url('System_settings/save_biometric_employee_mapping'); ?>">
                                                    <input type="hidden" name="biometric_no" value="<?= htmlspecialchars($log->employee_no); ?>">
                                                    <input type="hidden" name="biometric_id_number" value="<?= htmlspecialchars($log->id_number); ?>">
                                                    <input type="hidden" name="biometric_card_no" value="<?= htmlspecialchars($log->card_no); ?>">
                                                    <input type="hidden" name="biometric_name" value="<?= htmlspecialchars($log->employee_name); ?>">

                                                    <div class="form-group mb-2">
                                                        <select name="staff_idnumber" class="form-control" required>
                                                            <option value="">Select staff...</option>
                                                            <?php foreach ($staff_list as $staff): ?>
                                                                <?php
                                                                    $full_name = trim($staff->LastName . ', ' . $staff->FirstName . ' ' . $staff->MiddleName);
                                                                ?>
                                                                <option value="<?= htmlspecialchars($staff->IDNumber); ?>">
                                                                    <?= htmlspecialchars($full_name); ?> | ID: <?= htmlspecialchars($staff->IDNumber); ?> | EmpNo: <?= htmlspecialchars($staff->employeeNo); ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>

                                                    <button type="submit" class="btn btn-primary btn-sm">
                                                        Save Mapping
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center">No unmapped biometric records found.</td>
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