<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?= html_escape($title ?? 'Applications Summary by Position'); ?></title>
    <style>
        @page {
            size: A4 landscape;
            margin: 12mm;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #111827;
            background: #f3f4f6;
            margin: 0;
            padding: 20px;
        }

        .report-sheet {
            background: #fff;
            width: 100%;
            max-width: 1120px;
            margin: 0 auto;
            padding: 22px 24px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            border: 1px solid #d1d5db;
        }

        .report-header {
            text-align: center;
            border-bottom: 2px solid #1f2937;
            padding-bottom: 12px;
            margin-bottom: 18px;
        }

        .report-header h2 {
            margin: 0;
            font-size: 20px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .report-header .subtitle {
            margin-top: 6px;
            font-size: 12px;
            color: #4b5563;
        }

        .filter-section {
            margin-bottom: 16px;
            border: 1px solid #d1d5db;
            padding: 12px 14px;
            background: #f9fafb;
        }

        .filter-section form {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: end;
        }

        .filter-group {
            min-width: 220px;
            flex: 1 1 220px;
        }

        .filter-group label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .form-control {
            width: 100%;
            padding: 8px 10px;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            font-size: 12px;
            box-sizing: border-box;
        }

        .btn {
            display: inline-block;
            padding: 8px 14px;
            border: 1px solid #1d4ed8;
            background: #2563eb;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
        }

        .btn-secondary {
            background: #6b7280;
            border-color: #6b7280;
        }

        .summary-line {
            margin-bottom: 12px;
            font-size: 12px;
            font-weight: 700;
        }

        .table-wrap {
            overflow-x: auto;
        }

        table.report-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: auto;
        }

        table.report-table th,
        table.report-table td {
            border: 1px solid #9ca3af;
            padding: 8px 6px;
            font-size: 12px;
        }

        table.report-table thead th {
            background: #e5e7eb;
            text-align: center;
            font-weight: 700;
        }

        table.report-table tbody td {
            background: #fff;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .font-bold {
            font-weight: 700;
        }

        .grand-total-row td,
        .grand-total-row th {
            background: #f3f4f6 !important;
            font-weight: 700;
        }

        .no-print {
            display: block;
        }

        .print-tools {
            max-width: 1120px;
            margin: 0 auto 12px auto;
            text-align: right;
        }

        .print-btn {
            background: #16a34a;
            border-color: #16a34a;
        }

        @media print {
            body {
                background: #fff;
                padding: 0;
            }

            .report-sheet {
                box-shadow: none;
                border: none;
                max-width: 100%;
                padding: 0;
            }

            .no-print,
            .print-tools {
                display: none !important;
            }

            .filter-section {
                display: none;
            }
        }
    </style>
</head>
<body>

<?php
    $grand_total = 0;
    $submitted   = 0;
    $validated   = 0;
    $endorsed    = 0;
    $rated       = 0;
    $confirmed   = 0;
    $dq_total    = 0;
    $total_summary_all = 0;

    if (!empty($rows)) {
        foreach ($rows as $r) {
            $row_total_summary = (int)$r['application_submitted']
                               + (int)$r['validated']
                               + (int)$r['endorsed_for_rating']
                               + (int)$r['rated']
                               + (int)$r['confirmed']
                               + (int)$r['dq_count'];

            $grand_total += (int)$r['total_applicants'];
            $submitted   += (int)$r['application_submitted'];
            $validated   += (int)$r['validated'];
            $endorsed    += (int)$r['endorsed_for_rating'];
            $rated       += (int)$r['rated'];
            $confirmed   += (int)$r['confirmed'];
            $dq_total    += (int)$r['dq_count'];
            $total_summary_all += $row_total_summary;
        }
    }
?>

<div class="print-tools no-print">
    <button type="button" class="btn print-btn" onclick="window.print()">Print Report</button>
</div>

<div class="report-sheet">

    <div class="report-header">
        <h2><?= html_escape($title ?? 'Applications Summary by Position'); ?></h2>
        <div class="subtitle">School Year Based Applications Status Summary Report</div>
    </div>

    <div class="filter-section no-print">
        <form method="get" action="<?= base_url('HrisApplicationsSummary'); ?>">
            <div class="filter-group">
                <label for="sy">School Year</label>
                <select name="sy" id="sy" class="form-control" onchange="this.form.submit()">
                    <?php if (!empty($school_years)): ?>
                        <?php foreach ($school_years as $sy): ?>
                            <option value="<?= html_escape($sy); ?>" <?= ($selected_sy == $sy ? 'selected' : ''); ?>>
                                <?= html_escape($sy); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="">No School Year Found</option>
                    <?php endif; ?>
                </select>
            </div>

            <div class="filter-group">
                <label for="jobTitle">Job Title</label>
                <select name="jobTitle" id="jobTitle" class="form-control">
                    <option value="">All Job Titles</option>
                    <?php if (!empty($job_titles)): ?>
                        <?php foreach ($job_titles as $job): ?>
                            <option value="<?= html_escape($job); ?>" <?= ($selected_jobTitle == $job ? 'selected' : ''); ?>>
                                <?= html_escape($job); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div>
                <button type="submit" class="btn">Filter</button>
                <a href="<?= base_url('HrisApplicationsSummary'); ?>" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>

    <div class="summary-line">
        Filtered School Year:
        <span class="font-bold"><?= html_escape($selected_sy ?: 'All'); ?></span>
        &nbsp; | &nbsp;
        Filtered Job Title:
        <span class="font-bold"><?= html_escape($selected_jobTitle ?: 'All'); ?></span>
    </div>

    <div class="table-wrap">
        <table class="report-table" id="applications-summary-table">
            <thead>
                <tr>
                    <th style="min-width: 220px;">Position - Job Type</th>
                    <th>Total Applicants</th>
                    <th>Application Submitted</th>
                    <th>Validated</th>
                    <th>Endorsed for Rating</th>
                    <th>Rated</th>
                    <th>Confirmed</th>
                    <th>DQ</th>
                    <th>Total Summary</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($rows)): ?>
                    <?php foreach ($rows as $r): ?>
                        <?php
                            $row_total_summary = (int)$r['application_submitted']
                                               + (int)$r['validated']
                                               + (int)$r['endorsed_for_rating']
                                               + (int)$r['rated']
                                               + (int)$r['confirmed']
                                               + (int)$r['dq_count'];
                        ?>
                        <tr>
                            <td class="font-bold"><?= html_escape($r['position_jobtype']); ?></td>
                            <td class="text-center"><?= (int)$r['total_applicants']; ?></td>
                            <td class="text-center"><?= (int)$r['application_submitted']; ?></td>
                            <td class="text-center"><?= (int)$r['validated']; ?></td>
                            <td class="text-center"><?= (int)$r['endorsed_for_rating']; ?></td>
                            <td class="text-center"><?= (int)$r['rated']; ?></td>
                            <td class="text-center"><?= (int)$r['confirmed']; ?></td>
                            <td class="text-center"><?= (int)$r['dq_count']; ?></td>
                            <td class="text-center"><?= $row_total_summary; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center">No records found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>

            
            <tfoot>
                <tr class="grand-total-row">
                    <th class="text-right">Grand Total</th>
                    <th class="text-center"><?= $grand_total; ?></th>
                    <th class="text-center"><?= $submitted; ?></th>
                    <th class="text-center"><?= $validated; ?></th>
                    <th class="text-center"><?= $endorsed; ?></th>
                    <th class="text-center"><?= $rated; ?></th>
                    <th class="text-center"><?= $confirmed; ?></th>
                    <th class="text-center"><?= $dq_total; ?></th>
                    <th class="text-center"><?= $total_summary_all; ?></th>
                </tr>
            </tfoot>
           
        </table>
    </div>

</div>

</body>
</html>