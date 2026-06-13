<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div style="margin-left: 250px;">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0 font-size-18">My DTR</h4>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="get" onsubmit="event.preventDefault(); goToMyDTR();">
                        <div class="row">
                            <div class="col-md-3">
                                <label>Year</label>
                                <input type="number" id="year" class="form-control" value="<?= htmlspecialchars($selected_year); ?>" required>
                            </div>

                            <div class="col-md-3">
                                <label>Month</label>
                                <input type="number" id="month" min="1" max="12" class="form-control" value="<?= htmlspecialchars($selected_month); ?>" required>
                            </div>

                            <div class="col-md-3">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary btn-block">View DTR</button>
                            </div>
                        </div>
                    </form>

                    <hr>

                    <form method="post"
                          target="_blank"
                          action="<?= base_url('System_settings/print_my_dtr/' . $selected_year . '/' . $selected_month); ?>">
                        <div class="row">
                            <div class="col-md-5">
                                <label>Immediate Supervisor</label>
                                <input type="text" name="immediate_supervisor" class="form-control" required>
                            </div>

                            <div class="col-md-4">
                                <label>Position / Title</label>
                                <input type="text" name="immediate_supervisor_position" class="form-control" value="Immediate Supervisor">
                            </div>

                            <div class="col-md-3">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-success btn-block">Print My DTR</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

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
                                    <th>Event</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($dtr_rows as $row): ?>
                                    <?php
                                        $event_text = '';
                                        $event_title_attr = '';

                                        if (!empty($row['event'])) {
                                            $event_type = isset($row['event']['event_type']) ? trim($row['event']['event_type']) : '';
                                            $event_title = isset($row['event']['event_title']) ? trim($row['event']['event_title']) : '';
                                            $event_remarks = isset($row['event']['remarks']) ? trim($row['event']['remarks']) : '';

                                            $event_text = $event_title !== '' ? $event_title : $event_type;

                                            $tooltip_parts = array();
                                            if ($event_type !== '') {
                                                $tooltip_parts[] = 'Type: ' . $event_type;
                                            }
                                            if ($event_title !== '') {
                                                $tooltip_parts[] = 'Title: ' . $event_title;
                                            }
                                            if ($event_remarks !== '') {
                                                $tooltip_parts[] = 'Remarks: ' . $event_remarks;
                                            }

                                            $event_title_attr = implode(' | ', $tooltip_parts);
                                        }
                                    ?>
                                    <tr>
                                        <td><?= htmlspecialchars(date('M d, Y', strtotime($row['date']))); ?></td>
                                        <td><?= htmlspecialchars($row['day_name']); ?></td>
                                        <td>
                                            <?= !empty($row['morning_status'])
                                                ? htmlspecialchars($row['morning_status'])
                                                : (!empty($row['morning_in']) ? htmlspecialchars(date('h:i A', strtotime($row['morning_in']))) : ''); ?>
                                        </td>
                                        <td>
                                            <?= !empty($row['morning_status'])
                                                ? htmlspecialchars($row['morning_status'])
                                                : (!empty($row['morning_out']) ? htmlspecialchars(date('h:i A', strtotime($row['morning_out']))) : ''); ?>
                                        </td>
                                        <td>
                                            <?= !empty($row['afternoon_status'])
                                                ? htmlspecialchars($row['afternoon_status'])
                                                : (!empty($row['afternoon_in']) ? htmlspecialchars(date('h:i A', strtotime($row['afternoon_in']))) : ''); ?>
                                        </td>
                                        <td>
                                            <?= !empty($row['afternoon_status'])
                                                ? htmlspecialchars($row['afternoon_status'])
                                                : (!empty($row['afternoon_out']) ? htmlspecialchars(date('h:i A', strtotime($row['afternoon_out']))) : ''); ?>
                                        </td>
                                        <td title="<?= htmlspecialchars($event_title_attr); ?>">
                                            <?= !empty($event_text) ? htmlspecialchars($event_text) : ''; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
</div>
        </div>
    </div>
</div>

<script>
function goToMyDTR() {
    var year = document.getElementById('year').value;
    var month = document.getElementById('month').value;

    if (!year || !month) return;

    window.location.href = "<?= base_url('System_settings/my_dtr'); ?>/" + year + "/" + month;
}
</script>