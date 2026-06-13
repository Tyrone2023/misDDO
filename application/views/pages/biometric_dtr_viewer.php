<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div style="margin-left: 250px;">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0 font-size-18">Biometric DTR Viewer</h4>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="get" onsubmit="event.preventDefault(); goToDTR();">
                        <div class="row">
                            <div class="col-md-5">
                                <label>Employee</label>
                                <select id="staff_idnumber" class="form-control" required>
                                    <option value="">Select employee...</option>
                                    <?php foreach ($staff_list as $staff): ?>
                                        <?php $full_name = trim($staff->LastName . ', ' . $staff->FirstName . ' ' . $staff->MiddleName); ?>
                                        <option value="<?= htmlspecialchars($staff->IDNumber); ?>" <?= ($selected_staff_idnumber == $staff->IDNumber ? 'selected' : ''); ?>>
                                            <?= htmlspecialchars($full_name); ?> | ID: <?= htmlspecialchars($staff->IDNumber); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label>Year</label>
                                <input type="number" id="year" class="form-control" value="<?= htmlspecialchars($selected_year); ?>" required>
                            </div>

                            <div class="col-md-2">
                                <label>Month</label>
                                <input type="number" id="month" min="1" max="12" class="form-control" value="<?= htmlspecialchars($selected_month); ?>" required>
                            </div>

                            <div class="col-md-2">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary btn-block">View DTR</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <?php if (!empty($staff_info)): ?>
                <div class="card">
                    <div class="card-body">
                        <h5 class="mb-3">
                            <?= htmlspecialchars($staff_info->LastName . ', ' . $staff_info->FirstName . ' ' . $staff_info->MiddleName); ?>
                        </h5>
                        <p class="mb-1"><strong>ID Number:</strong> <?= htmlspecialchars($staff_info->IDNumber); ?></p>
                        <p class="mb-1"><strong>Employee No:</strong> <?= htmlspecialchars($staff_info->employeeNo); ?></p>
                        <p class="mb-0"><strong>Department:</strong> <?= htmlspecialchars($staff_info->Department); ?></p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Day</th>
                                        <th>Morning In</th>
                                        <th>Morning Out</th>
                                        <th>Afternoon In</th>
                                        <th>Afternoon Out</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($dtr_rows as $row): ?>
                                        <?php
                                            $merge_label = '';
                                            $row_style = '';

                                            if (!empty($row['travel'])) {
                                                $merge_label = 'TRAVEL: ' . $row['travel']['title'];
                                                $row_style = 'background:#fff3cd;';
                                            } elseif (!empty($row['event'])) {
                                                $event_title = !empty($row['event']['event_title']) ? $row['event']['event_title'] : $row['event']['event_type'];
                                                $merge_label = strtoupper($event_title);

                                                if (!empty($row['event']['remarks'])) {
                                                    $merge_label .= ' - ' . $row['event']['remarks'];
                                                }

                                                $row_style = 'background:#d1ecf1;';
                                            } elseif (!empty($row['is_weekend'])) {
                                                $merge_label = 'WEEKEND';
                                                $row_style = 'background:#f8f9fa;';
                                            }
                                        ?>
                                        <tr style="<?= $row_style; ?>">
                                            <td><?= htmlspecialchars(date('M d, Y', strtotime($row['date']))); ?></td>
                                            <td><?= htmlspecialchars($row['day_name']); ?></td>

                                            <?php if ($merge_label !== ''): ?>
                                                <td colspan="4" class="text-center font-weight-bold">
                                                    <?= htmlspecialchars($merge_label); ?>
                                                </td>
                                            <?php else: ?>
                                                <td><?= !empty($row['morning_in']) ? htmlspecialchars(date('h:i A', strtotime($row['morning_in']))) : ''; ?></td>
                                                <td><?= !empty($row['morning_out']) ? htmlspecialchars(date('h:i A', strtotime($row['morning_out']))) : ''; ?></td>
                                                <td><?= !empty($row['afternoon_in']) ? htmlspecialchars(date('h:i A', strtotime($row['afternoon_in']))) : ''; ?></td>
                                                <td><?= !empty($row['afternoon_out']) ? htmlspecialchars(date('h:i A', strtotime($row['afternoon_out']))) : ''; ?></td>
                                            <?php endif; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
</div>
        </div>
    </div>
</div>

<script>
function goToDTR() {
    var staff = document.getElementById('staff_idnumber').value;
    var year = document.getElementById('year').value;
    var month = document.getElementById('month').value;

    if (!staff || !year || !month) {
        return;
    }

    window.location.href = "<?= base_url('System_settings/biometric_dtr_viewer'); ?>/" + encodeURIComponent(staff) + "/" + year + "/" + month;
}
</script>