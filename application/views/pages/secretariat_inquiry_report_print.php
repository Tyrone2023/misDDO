<?php
if (!function_exists('h')) {
    function h($v)
    {
        return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
    }
}

$jobTypeSuffixes = [
    1 => '- Elementary',
    2 => '- Secondary',
    3 => '- Junior High School',
    4 => '- Senior High School',
    5 => '- Kindergarten',
    6 => '- IPED Elementary',
    7 => '- IPED Secondary',
    8 => '- IPED Junior High School',
    9 => '- IPED Senior High School',
    10 => '- SNED',
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($title ?? 'Inquiry Report'); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 20px;
            color: #000;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        
        .header h1 {
            font-size: 18px;
            margin: 0;
            font-weight: bold;
        }
        
        .header h2 {
            font-size: 14px;
            margin: 10px 0 0 0;
            font-weight: normal;
        }
        
        .filter-info {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f5f5f5;
            border: 1px solid #ddd;
        }
        
        .filter-info strong {
            display: inline-block;
            width: 80px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        th, td {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }
        
        th {
            background-color: #f0f0f0;
            font-weight: bold;
            font-size: 12px;
        }
        
        td {
            font-size: 11px;
        }
        
        .no-data {
            text-align: center;
            padding: 20px;
            font-style: italic;
            color: #666;
        }
        
        .error {
            color: #d32f2f;
            font-weight: bold;
            text-align: center;
            padding: 20px;
        }
        
        @media print {
            body {
                margin: 10px;
                font-size: 10px;
            }
            
            .header h1 {
                font-size: 16px;
            }
            
            .header h2 {
                font-size: 12px;
            }
            
            th, td {
                font-size: 9px;
                padding: 6px;
            }
            
            @page {
                size: landscape;
                margin: 0.5in;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Application Inquiries Report</h1>
        <h2>Generated on <?= date('F j, Y - g:i A'); ?></h2>
    </div>

    <?php if (!empty($filterError)) : ?>
        <div class="error">
            <?= h($filterError); ?>
        </div>
    <?php elseif (!($submitted ?? false)) : ?>
        <div class="error">
            No filter parameters provided. Please select filters from the main report page.
        </div>
    <?php else: ?>
        <div class="filter-info">
            <div><strong>Job Title:</strong> <?= h($selectedJobName ?: 'Not specified'); ?></div>
            <div><strong>District:</strong> <?= h($selectedDistrictName ?: 'All Districts'); ?></div>
            <div><strong>Year:</strong> <?= h($selectedYearName); ?></div>
        </div>

        <?php if (empty($inquiries)) : ?>
            <div class="no-data">
                No inquiries found for the selected filter criteria.
            </div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th width="15%">Record # / Name</th>
                        <th width="20%">Job Title / Level</th>
                        <th width="65%">Inquiry</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($inquiries as $row) : ?>
                        <?php
                            $name = trim(($row->LastName ?? '') . ', ' . ($row->FirstName ?? '') . ' ' . ($row->MiddleName ?? ''));
                            $name = trim($name, ', ');
                            $positionSuffix = $jobTypeSuffixes[(int) ($row->job_type ?? 0)] ?? ('- Job Type ' . (int) ($row->job_type ?? 0));
                            $position = trim(($row->jobTitle ?? '') . ' ' . $positionSuffix);
                        ?>
                        <tr>
                            <td>
                                <strong><?= h($row->record_no ?? ''); ?></strong><br>
                                <small><?= h($name); ?></small>
                            </td>
                            <td><?= h($position); ?></td>
                            <td style="white-space: pre-wrap; word-wrap: break-word;">
                                <?= h($row->inquiry ?? ''); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div style="margin-top: 30px; text-align: center; font-size: 10px; color: #666;">
                Total Records: <?= count($inquiries); ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <script>
        // Auto-print when page loads
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
</body>
</html>
