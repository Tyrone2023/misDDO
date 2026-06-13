<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Responsive bootstrap 4 admin template" name="description" />
    <meta content="Coderthemes" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Medical Certificate</title>

    <!-- App favicon -->
    <link rel="shortcut icon" href="<?= base_url(); ?>assets/images/hris.ico">

    <!-- Custom Styles -->
    <style>
    body {
        font-family: Calibri, sans-serif;
        width: 210mm;
        height: 297mm;
        margin: auto;
        padding: 40px;
        box-sizing: border-box;
    }
    .cert {
        text-align: center;
    }
    .logo {
        width: 100px;
    }
    .hr {
        border-top: 2px solid black;
        margin: 5px 0;
    }
    h4 {
        text-transform: uppercase;
        font-weight: bold;
        margin-bottom: 5px;
    }
    .content, .footer {
        text-align: left;
    }
    .date {
        text-align: right;
    }
    .report-container {
        display: flex;
        gap: 5px;
        align-items: flex-start;
        margin-top: 5px;
    }
    .label {
        font-weight: bold;
        min-width: 100px;
        text-align: left;
    }
    .content {
        flex: 1;
        text-align: justify;
    }
    .footer {
        margin-top: 30px;
    }
    .footer p {
        margin: 1px 0;
    }
    .signature {
        height: 40px;
        display: block;
        margin-left: 10px;
    }

    .treatment {
            flex: 1;
            text-align: justify;
            font-size: 12px;
        }
</style>
</head>

<body>
    <div class="cert">
        <img class="logo" src="<?= base_url(); ?>assets/images/report/ke.png" alt="">
        <p style="margin-bottom:0;">
            <span class="rp">Republic of the Philippines</span><br />
            <span class="de">Department of Education</span><br />
            <span class="r">Region XI</span><br />
            <span class="r">Schools Division of <?php echo $settings[0]->division; ?></span>
        </p>
        <div class="hr"></div>
        <h4>Medical Abstract <br>
            <!-- <strong><?php echo $data[0]->purpose; ?></strong> -->
        </h4>

        <div class="date">
            <p>Date: <?php echo date('F d, Y'); ?></p>
        </div>
        <div class="content">
        <div class="report-container">
                <div class="label">Name:</div>
                <div class="content"><?php echo $data[0]->FirstName; ?> <?php echo $data[0]->MiddleName; ?> <?php echo $data[0]->LastName; ?></div>
            </div>

            <div class="report-container">
                <div class="label">Age/Sex:</div>
                <div class="content"><?php echo $data[0]->age; ?> / <?php echo $data[0]->sex; ?></div>
            </div>

            <div class="report-container">
                <div class="label">Address:</div>
                <div class="content"><?php echo $data[0]->address; ?></div>
            </div>
            <br>


<div class="typecont">
    <strong>HISTORY OF PRESENT ILLNESS</strong>
    <p class="treatment"><?php echo nl2br(htmlspecialchars($data[0]->illness_history)); ?></p>
</div>

<div class="typecont">
    <strong>PHYSICAL EXAMINATION:</strong>
    <p class="treatment"><?php echo nl2br(htmlspecialchars($data[0]->phy_exam)); ?></p>
</div>


<div class="typecont">
    <strong>FINAL DIAGNOSIS:</strong>
    <p class="treatment"><?php echo nl2br(htmlspecialchars($data[0]->diagnosis)); ?></p>
</div>

<div class="typecont">
    <strong>MEDICATION:</strong>
    <p class="treatment"><?php echo nl2br(htmlspecialchars($data[0]->treatment)); ?></p>
</div>

          

           
    </div>
    <?php if (!empty($_GET['download'])): ?>
<script>
  window.addEventListener('load', function() {
    window.print();
  });
</script>
<?php endif; ?>

</body>

</html>