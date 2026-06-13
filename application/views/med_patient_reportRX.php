<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Responsive bootstrap 4 admin template" name="description" />
    <meta content="Coderthemes" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Medical Prescription</title>

    <!-- App favicon -->
    <link rel="shortcut icon" href="<?= base_url(); ?>assets/images/hris.ico">

    <!-- Custom Styles -->
    <style>
        body {
            font-family: Calibri, sans-serif;
            font-size: 12px;
            width: 102mm;
            height: 152mm;
            margin: auto;
            padding: 15px;
            box-sizing: border-box;
            border: 1px solid black;
            position: relative;
        }

        .cert {
            text-align: left;
        }

        .logo {
            width: 100%;
        }

        .hr {
            border-top: 2px solid black;
            margin: 10px 0;
        }

        h4 {
            text-transform: uppercase;
            font-weight: bold;
            font-size: 14px;
        }

        .date {
            text-align: right;
            font-weight: bold;
            font-size: 12px;
        }

        .report-container {
            display: flex;
            gap: 5px;
            align-items: flex-start;
            margin-top: 5px;
            font-size: 12px;
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

        .rx-container {
            display: flex;
            align-items: flex-start;
            margin-top: 10px;
        }

        .rx-image {
            width: 80px;
            margin-right: 10px;
            /* 10px space between image and text */
        }

        .treatment {
            flex: 1;
            text-align: justify;
            font-size: 12px;
        }

        .footer {
            position: absolute;
            bottom: 10px;
            left: 15px;
            width: calc(100% - 30px);
            text-align: left;
            font-size: 12px;
        }

        .footer p {
            margin: 2px 0;
        }

        .signature {
            width: auto;
            height: 40px;
            display: block;
            margin-top: 5px;
        }
    </style>
</head>

<body>
    <div class="cert">
        <img class="logo" src="<?= base_url(); ?>assets/images/hris/header.png" alt="">

        <div class="date">
            <p>Date: <?php echo date('F d, Y'); ?></p>
        </div>

        <div class="report-container">
            <div class="label">Name:</div>
            <div class="content"><?php echo $data[0]->FirstName; ?> <?php echo $data[0]->MiddleName; ?> <?php echo $data[0]->LastName; ?></div>
        </div>

        <div class="report-container">
            <div class="label">Age/Sex:</div>
            <div class="content"><?php echo $data[0]->age; ?>/<?php echo $data[0]->sex; ?></div>
        </div>

        <div class="report-container">
            <div class="label">Address:</div>
            <div class="content"><?php echo $data[0]->address; ?></div>
        </div>

        <!-- Rx Image -->

        <div class="rx-container">
            <img class="rx-image" src="<?= base_url(); ?>assets/images/hris/rx.jpg" alt="Rx Symbol">
            <p class="treatment"><?php echo nl2br(htmlspecialchars($data[0]->treatment)); ?></p>

        </div>

        <div class="footer">
            <img class="signature" src="<?= base_url('assets/images/' . $med_setting[0]->signature) ?>" alt="Signature">
            <p><strong><?php echo $med_setting[0]->doc_name; ?></strong></p>
            <p><strong>License No: <?php echo $med_setting[0]->license_no; ?></strong></p>
            <p><?php echo $med_setting[0]->position; ?></p>

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