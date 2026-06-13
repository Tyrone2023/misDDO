<style>
.table-sticky-header thead th {
    position: sticky;
    top: 0;
    z-index: 10;
    background: #ffffff;
}

.table-container {
    max-height: 500px; /* adjust as needed */
    overflow-y: auto;
}
</style>



<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div style="margin-left: 250px;">

                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0 font-size-18">Late / Undertime Summary</h4>
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
                        <form method="get" action="<?= base_url('System_settings/late_undertime_summary'); ?>" class="mb-4">
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="month">Month</label>
                                        <select name="month" id="month" class="form-control">
                                            <?php for ($m = 1; $m <= 12; $m++): ?>
                                                <option value="<?= $m; ?>" <?= ((int)$selected_month === $m) ? 'selected' : ''; ?>>
                                                    <?= date('F', mktime(0, 0, 0, $m, 1)); ?>
                                                </option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="year">Year</label>
                                        <select name="year" id="year" class="form-control">
                                            <?php for ($y = date('Y') + 1; $y >= 2020; $y--): ?>
                                                <option value="<?= $y; ?>" <?= ((int)$selected_year === $y) ? 'selected' : ''; ?>>
                                                    <?= $y; ?>
                                                </option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="department">Department</label>
                                        <select name="department" id="department" class="form-control">
                                            <option value="">All Departments</option>
                                            <?php foreach ($departments as $dept): ?>
                                                <option value="<?= htmlspecialchars($dept->Department); ?>"
                                                    <?= ($selected_department === $dept->Department) ? 'selected' : ''; ?>>
                                                    <?= htmlspecialchars($dept->Department); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group" style="margin-top: 30px;">
                                        <button type="submit" class="btn btn-primary">Filter</button>
                                        <button type="button" class="btn btn-success" onclick="window.print();">Print</button>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div class="table-container">
                            <table class="table table-bordered table-striped table-sm table-sticky-header">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Employee</th>
                                        <th>ID Number</th>
                                        <th>Department</th>
                                        <th>Late AM Days</th>
                                        <th>Late AM Minutes</th>
                                        <th>Late PM Days</th>
                                        <th>Late PM Minutes</th>
                                        <th>Undertime AM Days</th>
                                        <th>Undertime AM Minutes</th>
                                        <th>Undertime PM Days</th>
                                        <th>Undertime PM Minutes</th>
                                        <th>Incomplete Logs</th>
                                        <th>Total Late Minutes</th>
                                        <th>Total Undertime Minutes</th>
                                        <th>Grand Total Minutes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($summary)): ?>
                                        <?php $i = 1; ?>
                                        <?php foreach ($summary as $row): ?>
                                            <?php
                                                $middle = !empty($row->MiddleName) ? ' ' . $row->MiddleName : '';
                                                $full_name = trim($row->LastName . ', ' . $row->FirstName . $middle);

                                                $late_am_minutes = (int)$row->late_am_minutes;
                                                $late_pm_minutes = (int)$row->late_pm_minutes;
                                                $undertime_am_minutes = (int)$row->undertime_am_minutes;
                                                $undertime_pm_minutes = (int)$row->undertime_pm_minutes;
                                                $incomplete_log_days = (int)$row->incomplete_log_days;

                                                $total_late = $late_am_minutes + $late_pm_minutes;
                                                $total_undertime = $undertime_am_minutes + $undertime_pm_minutes;
                                                $grand_total = $total_late + $total_undertime;
                                            ?>
                                            <tr>
                                                <td><?= $i++; ?></td>
                                                <td><?= htmlspecialchars($full_name); ?></td>
                                                <td><?= htmlspecialchars($row->IDNumber); ?></td>
                                                <td><?= htmlspecialchars($row->Department); ?></td>
                                                <td><?= (int)$row->late_am_days; ?></td>
                                                <td><?= $late_am_minutes; ?></td>
                                                <td><?= (int)$row->late_pm_days; ?></td>
                                                <td><?= $late_pm_minutes; ?></td>
                                                <td><?= (int)$row->undertime_am_days; ?></td>
                                                <td><?= $undertime_am_minutes; ?></td>
                                                <td><?= (int)$row->undertime_pm_days; ?></td>
                                                <td><?= $undertime_pm_minutes; ?></td>
                                                <td><strong><?= $incomplete_log_days; ?></strong></td>
                                                <td><strong><?= $total_late; ?></strong></td>
                                                <td><strong><?= $total_undertime; ?></strong></td>
                                                <td><strong><?= $grand_total; ?></strong></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="16" class="text-center">
                                                No late, undertime, or incomplete log records found for the selected period.
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