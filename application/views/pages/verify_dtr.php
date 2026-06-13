<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Verify DTR</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        body {
            margin: 0;
            padding: 0;
            background: #f4f6f9;
            font-family: Arial, Helvetica, sans-serif;
            color: #222;
        }

        .page-wrap {
            max-width: 1100px;
            margin: 30px auto;
            padding: 0 15px;
        }

        .card {
            background: #fff;
            border: 1px solid #dcdfe3;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.04);
            overflow: hidden;
        }

        .card-header {
            padding: 18px 24px;
            border-bottom: 1px solid #e9ecef;
            background: #ffffff;
        }

        .card-header h2 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
        }

        .card-body {
            padding: 24px;
        }

        .alert {
            padding: 14px 16px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-success {
            background: #eaf7ed;
            border: 1px solid #b7e1c0;
            color: #1f6b2d;
        }

        .alert-danger {
            background: #fdeaea;
            border: 1px solid #f1b8b8;
            color: #8a1f1f;
        }

        .section-title {
            margin: 28px 0 12px;
            font-size: 18px;
            font-weight: 700;
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .meta-table th,
        .meta-table td,
        .dtr-table th,
        .dtr-table td {
            border: 1px solid #d7dce1;
            padding: 10px 12px;
            font-size: 14px;
            vertical-align: middle;
        }

        .meta-table th,
        .dtr-table th {
            background: #f8f9fb;
            text-align: left;
            font-weight: 700;
        }

        .meta-table th {
            width: 220px;
        }

        .text-center {
            text-align: center;
        }

        .font-weight-bold {
            font-weight: 700;
        }

        .text-muted {
            color: #6c757d;
        }

        .token-cell {
            word-break: break-all;
        }

        .top-actions {
            margin-top: 18px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-block;
            padding: 10px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            border: 1px solid #cfd6dd;
            background: #fff;
            color: #222;
        }

        .btn:hover {
            background: #f3f4f6;
        }

        .page-note {
            margin-top: 12px;
            font-size: 13px;
            color: #6b7280;
        }

        @media print {
            body {
                background: #fff;
            }

            .page-wrap {
                max-width: 100%;
                margin: 0;
                padding: 0;
            }

            .card {
                border: none;
                box-shadow: none;
            }

            .top-actions {
                display: none;
            }
        }
    </style>
</head>
<body>

<div class="page-wrap">
    <div class="card">
        <div class="card-header">
            <h2>Verify DTR</h2>
        </div>

        <div class="card-body">

            <?php if (!empty($is_valid)): ?>
                <div class="alert alert-success">
                    This DTR print record is valid.
                </div>

                <div class="table-responsive">
                    <table class="meta-table">
                        <tr>
                            <th>Employee Name</th>
                            <td>
                                <?php if (!empty($staff_info)): ?>
                                    <?= htmlspecialchars(trim($staff_info->LastName . ', ' . $staff_info->FirstName . ' ' . $staff_info->MiddleName)); ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th>ID Number</th>
                            <td><?= !empty($print_log->staff_idnumber) ? htmlspecialchars($print_log->staff_idnumber) : ''; ?></td>
                        </tr>
                        <tr>
                            <th>DTR Period</th>
                            <td>
                                <?= !empty($print_log) ? htmlspecialchars(date('F Y', strtotime(sprintf('%04d-%02d-01', $print_log->dtr_year, $print_log->dtr_month)))) : ''; ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Immediate Supervisor</th>
                            <td><?= !empty($print_log->immediate_supervisor) ? htmlspecialchars($print_log->immediate_supervisor) : ''; ?></td>
                        </tr>
                        <tr>
                            <th>Position / Title</th>
                            <td><?= !empty($print_log->immediate_supervisor_position) ? htmlspecialchars($print_log->immediate_supervisor_position) : ''; ?></td>
                        </tr>
                        <tr>
                            <th>Generated At</th>
                            <td><?= !empty($print_log->generated_at) ? htmlspecialchars($print_log->generated_at) : ''; ?></td>
                        </tr>
                        <tr>
                            <th>Verification Token</th>
                            <td class="token-cell">
                                <?= !empty($print_log->verification_token) ? htmlspecialchars($print_log->verification_token) : ''; ?>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="section-title">Server-side DTR Record</div>

                <div class="table-responsive">
                    <table class="dtr-table">
                        <thead>
                            <tr>
                                <th style="width: 70px;">Day</th>
                                <th>Morning In</th>
                                <th>Morning Out</th>
                                <th>Afternoon In</th>
                                <th>Afternoon Out</th>
                                <th style="width: 260px;">Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($dtr_rows)): ?>
                                <?php foreach ($dtr_rows as $row): ?>
                                    <?php
                                        $has_real_date = !empty($row['date']);
                                        $is_sunday = ($has_real_date && (int)$row['day_of_week'] === 0);
                                        $is_saturday = ($has_real_date && (int)$row['day_of_week'] === 6);
                                        $is_weekend = $is_sunday || $is_saturday;
                                        $travel = !empty($row['travel']) ? $row['travel'] : null;
                                        $event = !empty($row['event']) ? $row['event'] : null;
                                    ?>
                                    <tr>
                                        <td><?= (int)$row['day']; ?></td>

                                        <?php if (!$has_real_date): ?>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>

                                        <?php elseif ($travel): ?>
                                            <td colspan="4" class="text-center font-weight-bold">
                                                <?= htmlspecialchars($travel['title']); ?>
                                                <?php if (isset($travel['is_start']) && !$travel['is_start']): ?>
                                                    <span class="text-muted">(continued)</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>Approved Travel</td>

                                        <?php elseif ($event): ?>
                                            <td colspan="4" class="text-center font-weight-bold">
                                                <?= htmlspecialchars(!empty($event['event_title']) ? $event['event_title'] : $event['event_type']); ?>
                                            </td>
                                            <td>
                                                <?= !empty($event['remarks']) ? htmlspecialchars($event['remarks']) : 'Recorded Event'; ?>
                                            </td>

                                        <?php elseif ($is_weekend): ?>
                                            <td colspan="4" class="text-center font-weight-bold">
                                                <?= $is_sunday ? 'Sunday' : 'Saturday'; ?>
                                            </td>
                                            <td>Weekend</td>

                                        <?php else: ?>
                                            <td>
                                                <?= !empty($row['morning_in']) ? htmlspecialchars(date('H:i', strtotime($row['morning_in']))) : ''; ?>
                                            </td>
                                            <td>
                                                <?= !empty($row['morning_out']) ? htmlspecialchars(date('H:i', strtotime($row['morning_out']))) : ''; ?>
                                            </td>
                                            <td>
                                                <?= !empty($row['afternoon_in']) ? htmlspecialchars(date('H:i', strtotime($row['afternoon_in']))) : ''; ?>
                                            </td>
                                            <td>
                                                <?= !empty($row['afternoon_out']) ? htmlspecialchars(date('H:i', strtotime($row['afternoon_out']))) : ''; ?>
                                            </td>
                                            <td></td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">No DTR rows found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="top-actions">
                    <a href="javascript:window.print();" class="btn">Print</a>
                    <a href="<?= htmlspecialchars(base_url()); ?>" class="btn">Back to Dashboard</a>
                </div>

                <div class="page-note">
                    This verification page is viewable only by a logged-in Admin account through the controller restriction.
                </div>

            <?php else: ?>
                <div class="alert alert-danger">
                    Invalid or unknown DTR verification token.
                </div>

                <div class="top-actions">
                    <a href="<?= htmlspecialchars(base_url()); ?>" class="btn">Back to Dashboard</a>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

</body>
</html>