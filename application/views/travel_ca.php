<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="<?= base_url(); ?>/assets/images/hris.ico">
    <title>Certificate of Appearance</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 5mm 10mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: "Times New Roman", serif;
            background: #e9e9e9;
        }

        .page {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 5mm 10mm 10mm 10mm;
            background: #fff;
        }

        .certificate {
            width: 100%;
            border: 2px solid #000;
        }

        .top-header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding: 8px 12px 4px;
        }

        .top-header img.logo {
            width: 35%;
            object-fit: contain;
            display: block;
            margin: 0 auto 0;
            height:100px;
        }

        .rp {
            font-size: 13px;
            font-weight: bold;
            margin: 0;
        }

        .deped {
            font-size: 24px;
            font-weight: bold;
            margin: 0;
            line-height: 1.1;
        }

        .region {
            font-size: 12px;
            font-weight: bold;
            margin: 2px 0 0;
        }

        .division {
            font-size: 14px;
            font-weight: bold;
            margin: 2px 0 0;
        }

        .title {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            padding: 5px 12px;
            line-height: 1;
        }

        .content {
            padding: 0 20px 10px 20px;
            min-height: 200px;
        }

        .concern {
            font-size: 16px;
            font-weight: bold;
            margin: 10px 0 10px;
        }

        .text-block {
            font-size: 16px;
            line-height: 1em;
        }

        .fill-line {
            display: inline-flex;
            align-items: flex-end;
            justify-content: center;
            border-bottom: 1px solid #000;
            vertical-align: baseline;
            padding: 0 6px 1px 6px;
            margin: 0 4px;
            min-height: 26px;
            text-align: center;
        }

        .name-line {
            width: 380px;
        }

        .office-line {
            width: 440px;
        }

        .purpose-line {
            width: 550px;
        }

        .date-line {
            width: 180px;
        }

        .signature-wrap {
            margin-top: 1px;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 12px;
            padding-right: 40px;
        }

        .qr-box img {
            width: 70px;
            height: 70px;
            object-fit: contain;
            display: block;
        }

        .signature {
            text-align: center;
            width: 280px;
            margin: 0;
        }

        .signature .name {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 0;
        }
        .signature img{
            position:relative;
            top:30px;
        }

        .signature .position {
            font-size: 16px;
            margin-top: 2px;
        }

        .footer {
            border-top: 2px solid #000;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 2px 10px 2px 10px;
            min-height: 78px;
        }

        .footer-left {
            display: flex;
            align-items: center;
            gap: 8px;
            width: 70%;
        }

        .footer-left img {
            width: 54px;
            height: 54px;
            object-fit: contain;
            flex-shrink: 0;
        }

        .footer-left .info {
            font-size: 12px;
            line-height: 1.45;
        }

        .footer-center {
            width: 12%;
            text-align: center;
            font-size: 11px;
            font-weight: bold;
            line-height: 1.3;
        }

        .footer-right {
            width: 18%;
            text-align: right;
        }

        .footer-right img {
            width: 78px;
            height: auto;
            object-fit: contain;
        }

        @media print {
            body {
                background: #fff;
            }

            .page {
                margin: 0;
                padding: 0;
                width: auto;
                min-height: auto;
            }
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="certificate">
            <div class="top-header">
                <img src="<?= base_url(); ?>assets/images/header.png" alt="DepEd Logo" class="logo">
            </div>

            <div class="title">CERTIFICATE OF APPEARANCE</div>

            <div class="content">
                <div class="concern">To Whom it may Concern:</div>

                <div class="text-block">
                    This is to Certify
                    <span class="fill-line name-line"><?= !empty($staff) ? htmlspecialchars(strtoupper($staff->FirstName.' '.$staff->MiddleName.' '.$staff->LastName.' '.$staff->NameExtn)) : '' ?></span><br>

                    of
                    <span class="fill-line office-line"><?= $staff->Department; ?></span>
                    was in the office for the purpose of
                    <span class="fill-line purpose-line"><?= $tr->purpose; ?></span><br />
                    on
                    <span class="fill-line date-line"><?= !empty($ca) ? htmlspecialchars(date('F d, Y', strtotime($ca->dt))) : '' ?></span>
                </div>

                <div class="signature-wrap">
                    <div class="qr-box">
                        <img class="qr" src="https://qrcode.tec-it.com/API/QRCode?data=<?= base_url(); ?>travel/travel_ca/<?= $this->uri->segment(3); ?>/<?= $this->uri->segment(4); ?>" title="" />
                    </div>

                    <div class="signature">
                        <img src="<?= base_url(); ?>assets/isig/emma.png" alt="Division Seal">
                        <div class="name">EMMA O. RABUYA</div>
                        <div class="position">Administrative Officer V</div>
                    </div>
                </div>
            </div>

            <div class="footer">
                <div class="footer-left">
                    <img src="<?= base_url(); ?>assets/images/flogo.gif" alt="Division Seal">
                    <div class="info">
                        <div>Address: Government Center, Dahican, Mati City, Davao Oriental</div>
                        <div>Telephone Number: (087) 388-3372</div>
                        <div>Email Address: davao.oriental@deped.gov.ph</div>
                    </div>
                </div>

                <!-- <div class="footer-center">
                    <div>ISO 9001:2015</div>
                    <div>CERTIFIED</div>
                </div> -->

                <!-- <div class="footer-right">
                    <img src="<?= base_url(); ?>assets/images/flogo2.gif" alt="TUV Logo">
                </div> -->
            </div>
        </div>
    </div>
</body>
</html>