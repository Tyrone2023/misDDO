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
            margin: 10px 0;
        }

        h4 {
            text-transform: uppercase;
            font-weight: bold;
        }

        .date,
        .content,
        .footer {
            text-align: left;
        }

        .report-container {
            display: flex;
            gap: 10px;
            align-items: flex-start;
            margin-top: 10px;
        }

        .label {
            font-weight: bold;
            min-width: 120px;
            text-align: left;
        }

        .content {
            flex: 1;
            text-align: justify;
        }

        .footer {
            margin-top: 50px;
        }

        .footer p {
            margin: 2px 0;
        }

        .signature {
            width: auto;
            /* Maintain aspect ratio */
            height: 50px;
            /* Adjust height to match text size */
            display: block;
            margin-left: 20px;
            /* margin: auto auto auto auto; Center the signature */
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
        <h4>Medical Certificate <br>
            <strong><?php echo $data[0]->purpose; ?></strong>
        </h4>

        <br>
        <div class="date">
            <p>Date: <?php echo date('F d, Y'); ?></p>
        </div>
        <br>
        <div class="content">
            <p>TO WHOM IT MAY CONCERN:</p>
            <p style="text-indent: 40px;">THIS IS TO CERTIFY that person named hereunder has the following record of consultation in our office.</p>


            <div class="report-container">
                <div class="label">Name:</div>
                <div class="content"><?php echo $data[0]->FirstName; ?> <?php echo $data[0]->MiddleName; ?> <?php echo $data[0]->LastName; ?></div>
            </div>

            <div class="report-container">
                <div class="label">Age:</div>
                <div class="content"><?php echo $data[0]->age; ?></div>
            </div>

            <div class="report-container">
                <div class="label">Sex:</div>
                <div class="content"><?php echo $data[0]->sex; ?></div>
            </div>

            <div class="report-container">
                <div class="label">Address:</div>
                <div class="content"><?php echo $data[0]->address; ?></div>
            </div>
            <br>

            <div class="report-container">
                <div class="label">Medication:</div>
                <div class="content"><?php echo $data[0]->current_med; ?></div>
            </div><br>

            <div class="report-container">
                <div class="label">DIAGNOSIS:</div>
                <div class="content"><?php echo $data[0]->diagnosis; ?></div>
            </div>
            <div class="report-container">
                <div class="label">REMARKS:</div>
                <div class="content"><?php echo $data[0]->remarks; ?></div>
            </div>
            <?php if (!empty($data[0]->rest_no)) : ?>
                <p style="margin-top: 20px;">
                    <strong>ADVISED FOR REST FOR <?= $data[0]->rest_no; ?>
                        <?= ($data[0]->rest_no == 1) ? 'DAY' : 'DAYS'; ?>.</strong>
                </p>
            <?php endif; ?>


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