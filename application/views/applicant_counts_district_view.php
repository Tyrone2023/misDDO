<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Responsive bootstrap 4 admin template" name="description" />
    <meta content="Coderthemes" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="shortcut icon" href="<?= base_url(); ?>assets/images/hris.ico">

    <link href="<?= base_url(); ?>assets/css/renren.css" rel="stylesheet" type="text/css" />

    <style>
        @page { size: A4; margin: 30px 25px; }
        body { font-family: "Calibri", sans-serif; font-size: 12px; }
        .cert { width: 90%; margin: 0 auto; }
        .rrr { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .rrr th, .rrr td { border: 1px solid #000; padding: 6px 8px; }
        .rrr th { background: #f2f2f2; text-align: left; }
        .header { text-align: center; margin-top: 10px; }
        .logo { width: 75px; float: left; margin-right: 10px; }
        .title { margin-top: 30px; margin-bottom: 10px; }
        .footer { margin-top: 20px; font-size: 11px; }
    </style>
</head>

<body class="aip_generate" id="printTable">

<!-- <div class="cert">
    <img class="logo" src="<?= base_url(); ?>assets/images/report/ke.png" alt="">
    <div class="header">
        <p style="margin-bottom:0;">
            <span class="rp">Republic of the Philippines</span><br />
            <span class="de">Department of Education</span><br />
            <span class="r">Region XI</span><br />
            <span class="r">Schools Division of <?= $mis_settings[0]->division; ?></span>
        </p>
    </div> -->

    <hr style="margin:10px 0" />

    <h4 class="title">
        Applicant Counts by District<br>
        <small>
            <?= $job->jobTitle; ?>
            <?php
            $jobTypes = [
                1  => '- Elementary',
                2  => '- Secondary',
                3  => '- Junior High School',
                4  => '- Senior High School',
                5  => '- Kindergarten',
                6  => '- IPED Elementary',
                7  => '- IPED Secondary',
                8  => '- IPED Junior High School',
                9  => '- IPED Senior High School',
                10 => '- SNED',
                11 => '- SHS Academic and Core Subjects',
                12 => '- SHS Arts and Design Track',
                13 => '- SHS Sports Track',
                14 => '- SHS Technical-Vocational(TVL) Track',
                15 => '- FOR TESTING PURPOSES (DO NOT APPLY)',
            ];
            echo isset($jobTypes[$job->job_type]) ? ' ' . $jobTypes[$job->job_type] : '';
            ?>
        </small>
    </h4>

    <?php if (!empty($counts)): ?>
    <table class="rrr" id="myTable">
        <thead>
            <tr>
                <th>District</th>
                <th>Total Applicants</th>
                <th>Validated Applicants</th>
                <th>Retained Applicants (Endorsed for Rating / Rated)</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($counts as $row): ?>
            <tr>
                <td><?= $row->district_name; ?></td>
                <td><?= $row->total_applicants; ?></td>
                <td><?= $row->validated_applicants; ?></td>
                <td><?= $row->retained_applicants; ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
        <p>No applicant records found for this job.</p>
    <?php endif; ?>

    <div class="footer">
        <!-- Generated on <?= date('F d, Y h:i A'); ?> -->
    </div>
</div>

</body>
</html>
