<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daily Time Record</title>
    <style>
        @page { size: A4 portrait; margin: 7mm; }
        body { font-family: "Times New Roman", serif; margin: 0; padding: 0; color: #000; background: #fff; }
        .page { width: 196mm; margin: 0 auto; position: relative; padding: 1mm 3mm; box-sizing: border-box; }
        .watermark { position: fixed; top: 37%; left: 8%; width: 84%; text-align: center; font-size: 40px; color: rgba(120,120,120,0.10); transform: rotate(-30deg); z-index: 0; pointer-events: none; user-select: none; }
        .content { position: relative; z-index: 1; }
        .print-btn { margin-bottom: 8px; font-family: Arial, sans-serif; font-size: 12px; padding: 6px 12px; }
        @media print { .print-btn { display: none; } }
        .top-line { font-size: 11px; margin: 0 0 1px 4px; }
        .title { text-align: center; font-size: 14px; font-weight: bold; margin: 0; }
        .office-time { float: right; text-align: left; font-size: 11px; line-height: 1.15; margin-top: -2px; margin-right: 3px; }
        .employee-block { width: 100%; margin-top: 2px; margin-bottom: 6px; border-collapse: collapse; font-size: 11px; }
        .employee-block td { padding: 1px 0; vertical-align: bottom; }
        .line-value { display: inline-block; min-width: 235px; border-bottom: 1px solid #000; font-weight: bold; padding-left: 2px; }
        .line-value.month { min-width: 100px; text-transform: uppercase; }
        table.dtr { width: 100%; border-collapse: collapse; table-layout: fixed; font-size: 11px; }
        table.dtr th, table.dtr td { border: 1px solid #000; padding: 0; text-align: center; vertical-align: middle; line-height: 1.0; }
        table.dtr th { font-weight: normal; }
        .day-col { width: 9%; font-weight: bold; }
        .time-col { width: 17.75%; }
        .ot-col { width: 20%; }
        .row-day { height: 18px; }
        .head-top { height: 18px; font-size: 11px; }
        .head-sub { height: 16px; font-size: 10px; }
        .day-number { font-weight: bold; font-size: 11px; }
        .special-text { font-weight: bold; font-size: 11px; padding: 1px 4px; text-align: left; }
        .travel-cell { font-weight: bold; font-size: 10px; padding: 2px 4px; line-height: 1.1; text-align: center; }
        .summary-label { text-align: center; font-weight: bold; font-size: 11px; }
        .certification { margin-top: 2px; text-align: center; font-size: 10px; line-height: 1.2; }
        .signature-block { margin-top: 10px; text-align: center; font-size: 11px; }
        .signature-name { font-weight: bold; text-transform: uppercase; text-decoration: underline; }
        .signature-role { margin-top: 1px; }
        .verified-label { margin-top: 10px; font-size: 10px; text-align: left; }
        .verify-box { margin-top: 8px; font-size: 9px; text-align: left; line-height: 1.2; display: flex; align-items: flex-start; gap: 12px; }
        .qr-box img { width: 86px; height: 86px; border: 1px solid #000; }
        .clearfix::after { content: ""; display: block; clear: both; }
    </style>
</head>
<body>
<?php
    if (!function_exists('get_travel_title_for_dtr')) {
        function get_travel_title_for_dtr($travel)
        {
            if (empty($travel)) {
                return '';
            }

            if (is_array($travel)) {
                if (!empty($travel['title'])) {
                    return trim((string)$travel['title']);
                }
                if (!empty($travel['purpose'])) {
                    return trim((string)$travel['purpose']);
                }
                if (!empty($travel['travel_title'])) {
                    return trim((string)$travel['travel_title']);
                }
            }

            if (is_object($travel)) {
                if (!empty($travel->title)) {
                    return trim((string)$travel->title);
                }
                if (!empty($travel->purpose)) {
                    return trim((string)$travel->purpose);
                }
                if (!empty($travel->travel_title)) {
                    return trim((string)$travel->travel_title);
                }
            }

            if (is_string($travel)) {
                return trim($travel);
            }

            return '';
        }
    }

    if (!function_exists('get_event_title_for_dtr')) {
        function get_event_title_for_dtr($event)
        {
            if (empty($event)) {
                return '';
            }

            if (is_array($event)) {
                if (!empty($event['event_title'])) {
                    return trim((string)$event['event_title']);
                }
                if (!empty($event['event_type'])) {
                    return trim((string)$event['event_type']);
                }
            }

            if (is_object($event)) {
                if (!empty($event->event_title)) {
                    return trim((string)$event->event_title);
                }
                if (!empty($event->event_type)) {
                    return trim((string)$event->event_type);
                }
            }

            if (is_string($event)) {
                return trim($event);
            }

            return '';
        }
    }

    if (!function_exists('get_combined_day_note_for_dtr')) {
        function get_combined_day_note_for_dtr($row)
        {
            $travelText = get_travel_title_for_dtr(isset($row['travel']) ? $row['travel'] : null);
            $eventText = get_event_title_for_dtr(isset($row['event']) ? $row['event'] : null);

            // PRIORITY: TRAVEL FIRST
            if ($travelText !== '') {
                return $travelText;
            }

            // ONLY SHOW EVENT IF NO TRAVEL
            if ($eventText !== '') {
                return $eventText;
            }

            return '';
        }
    }

    /**
     * Build merged note blocks for consecutive rows with the same combined day note.
     * This now supports:
     * - travel only
     * - event only
     * - travel + event
     */
    $noteBlocks = [];
    $noteSkipRows = [];

    $rowCount = count($dtr_rows);
    $i = 0;

    while ($i < $rowCount) {
        $row = $dtr_rows[$i];

        $has_real_date = !empty($row['date']);
        $dayNote = get_combined_day_note_for_dtr($row);

        if (!$has_real_date || $dayNote === '') {
            $i++;
            continue;
        }

        $startIndex = $i;
        $rowspan = 1;

        for ($j = $i + 1; $j < $rowCount; $j++) {
            $nextRow = $dtr_rows[$j];

            $nextHasRealDate = !empty($nextRow['date']);
            $nextDayNote = get_combined_day_note_for_dtr($nextRow);

            if (!$nextHasRealDate || $nextDayNote !== $dayNote) {
                break;
            }

            $rowspan++;
        }

        $noteBlocks[$startIndex] = [
            'title'   => $dayNote,
            'rowspan' => $rowspan,
        ];

        for ($k = $startIndex + 1; $k < $startIndex + $rowspan; $k++) {
            $noteSkipRows[$k] = true;
        }

        $i = $startIndex + $rowspan;
    }
?>
    <div class="watermark">DepEd MIS Official Printout</div>

    <div class="page">
        <div class="content">
            <button class="print-btn" onclick="window.print()">Print</button>

            <div class="top-line">CS Form 48</div>
            <div class="clearfix">
                <div class="office-time">
                    Official Time<br>
                    A.M. - 8:00 - 12:00<br>
                    P.M. - 1:00 - 5:00
                </div>
                <div class="title">DAILY TIME RECORD</div>
            </div>

            <table class="employee-block">
                <tr>
                    <td>Name: <span class="line-value"><?= htmlspecialchars(trim($staff_info->FirstName . ' ' . $staff_info->MiddleName . ' ' . $staff_info->LastName)); ?></span></td>
                </tr>
                <tr>
                    <td>
                        For the month of 
                        <span class="line-value month">
                            <?= htmlspecialchars(strtoupper(date('F', strtotime(sprintf('%04d-%02d-01', $selected_year, $selected_month)))) . ' ' . $selected_year); ?>
                        </span>
                    </td>
                </tr>
            </table>

            <table class="dtr">
                <thead>
                    <tr class="head-top">
                        <th class="day-col">Day</th>
                        <th colspan="2">Morning</th>
                        <th colspan="2">Afternoon</th>
                        <th class="ot-col">Over/</th>
                    </tr>
                    <tr class="head-sub">
                        <th></th>
                        <th class="time-col">Arrival</th>
                        <th class="time-col">Departure</th>
                        <th class="time-col">Arrival</th>
                        <th class="time-col">Departure</th>
                        <th>Under time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $present_days = 0;
                    $absent_days = 0;

                    foreach ($dtr_rows as $index => $row):
                        $has_real_date = !empty($row['date']);
                        $is_sunday = ($has_real_date && (int)$row['day_of_week'] === 0);
                        $is_saturday = ($has_real_date && (int)$row['day_of_week'] === 6);
                        $is_weekend = $is_sunday || $is_saturday;

                        $has_time = !empty($row['morning_in']) || !empty($row['morning_out']) || !empty($row['afternoon_in']) || !empty($row['afternoon_out']);
                        $dayNote = get_combined_day_note_for_dtr($row);
                        $has_note = ($dayNote !== '');

                        if ($has_real_date) {
                            if ($has_time || $has_note) {
                                $present_days++;
                            } else {
                                if (!$is_weekend) {
                                    $absent_days++;
                                }
                            }
                        }
                    ?>
                        <tr class="row-day">
                            <td class="day-number"><?= (int)$row['day']; ?></td>

                            <?php if (!$has_real_date): ?>
                                <td></td><td></td><td></td><td></td><td></td>

                            <?php elseif ($has_note): ?>
                                <?php if (isset($noteBlocks[$index])): ?>
                                    <td colspan="4" rowspan="<?= (int)$noteBlocks[$index]['rowspan']; ?>" class="travel-cell">
                                        <?= nl2br(htmlspecialchars($noteBlocks[$index]['title'])); ?>
                                    </td>
                                <?php elseif (!isset($noteSkipRows[$index])): ?>
                                    <td colspan="4" class="travel-cell">
                                        <?= nl2br(htmlspecialchars($dayNote)); ?>
                                    </td>
                                <?php endif; ?>
                                <td></td>

                            <?php elseif ($is_weekend): ?>
                                <td colspan="4" class="special-text"><?= $is_sunday ? 'Sunday' : 'Saturday'; ?></td>
                                <td></td>

                            <?php else: ?>
                                    <td>
                                        <?= !empty($row['morning_status'])
                                            ? htmlspecialchars($row['morning_status'])
                                            : (!empty($row['morning_in']) ? htmlspecialchars(date('H:i', strtotime($row['morning_in']))) : ''); ?>
                                    </td>
                                    <td>
                                        <?= !empty($row['morning_status'])
                                            ? htmlspecialchars($row['morning_status'])
                                            : (!empty($row['morning_out']) ? htmlspecialchars(date('H:i', strtotime($row['morning_out']))) : ''); ?>
                                    </td>
                                    <td>
                                        <?= !empty($row['afternoon_status'])
                                            ? htmlspecialchars($row['afternoon_status'])
                                            : (!empty($row['afternoon_in']) ? htmlspecialchars(date('H:i', strtotime($row['afternoon_in']))) : ''); ?>
                                    </td>
                                    <td>
                                        <?= !empty($row['afternoon_status'])
                                            ? htmlspecialchars($row['afternoon_status'])
                                            : (!empty($row['afternoon_out']) ? htmlspecialchars(date('H:i', strtotime($row['afternoon_out']))) : ''); ?>
                                    </td>
                                    <td><?= !empty($row['remarks']) ? htmlspecialchars($row['remarks']) : ''; ?></td>
                                <td></td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>

                    <tr><td colspan="5" class="summary-label">Total Number of Days Present</td><td></td></tr>
                    <tr><td colspan="5" class="summary-label">Total Number of Days Absent</td><td></td></tr>
                    <tr><td colspan="5" class="summary-label">Total Over/Under Time</td><td></td></tr>
                </tbody>
            </table>

            <div class="certification">
                I certify on my honor that the above is a true and correct report of the <br>
                hours of work performed, record of which was made daily at the time <br>
                of arrival and departure from office.<br><br>
            </div>

            <div class="signature-block">
                <div class="signature-name"><?= htmlspecialchars(strtoupper(trim($staff_info->FirstName . ' ' . $staff_info->MiddleName . ' ' . $staff_info->LastName))); ?></div>
                <div class="signature-role">Employee</div>
            </div>

            <div class="verified-label">Verified as to the prescribed office hours:</div>

            <div class="signature-block" style="margin-top: 22px;">
                <div class="signature-name"><?= htmlspecialchars(strtoupper($immediate_supervisor)); ?></div>
                <div class="signature-role"><?= htmlspecialchars($immediate_supervisor_position); ?></div>
            </div>

            <div class="verify-box">
                <div class="qr-box">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=<?= urlencode($verify_url); ?>" alt="DTR QR Code">
                </div>
                <div>
                    <strong>Generated:</strong> <?= htmlspecialchars($generated_at); ?><br>
                    <strong>Verification URL:</strong> <?= htmlspecialchars($verify_url); ?><br>
                    <strong>Verification Token:</strong> <?= htmlspecialchars(substr($verification_token, 0, 20)); ?>...<br>
                    <strong>Notice:</strong> Scan the QR code to verify this printed DTR.
                </div>
            </div>
        </div>
    </div>
</body>
</html>