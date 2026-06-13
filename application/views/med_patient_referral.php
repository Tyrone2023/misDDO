<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Referral Form</title>
    <link rel="shortcut icon" href="<?= base_url(); ?>assets/images/hris.ico">
    <style>
        @page {
            size: A4 portrait;
            margin: 0;
        }

        html,
        body {
            font-family: Calibri, sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            color: #000;
        }

        body {
            display: flex;
            justify-content: center;
        }

        .print-wrapper {
            width: 210mm;
            height: 297mm;
            padding: 6mm;
            margin: 0 auto;
            box-sizing: border-box;
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }

        .page {
            border: 2px solid #000;
            padding: 9mm 10mm 8mm;
            box-sizing: border-box;
            width: 100%;
            transform-origin: top center;
        }

        .header {
            text-align: center;
            line-height: 1.2;
        }

        .logo {
            width: 70px;
            display: block;
            margin: 0 auto 6px;
        }

        .title {
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 8px;
            margin-bottom: 12px;
        }

        .row {
            display: flex;
            gap: 12px;
            margin-bottom: 6px;
        }

        .col {
            flex: 1;
            min-width: 0;
        }

        .line {
            border-bottom: 1px solid #000;
            min-height: 18px;
            line-height: 1.2;
            padding: 2px 4px;
            white-space: pre-line;
            overflow-wrap: anywhere;
            word-break: break-word;
            box-sizing: border-box;
            text-align: center;
        }

        .label {
            font-size: 12px;
            font-weight: bold;
        }

        .section-title {
            font-weight: bold;
            margin: 8px 0 5px;
        }

        .signature {
            height: 50px;
            display: block;
            margin: 0 auto 4px;
            max-width: 100%;
            object-fit: contain;
        }

        .signature-placeholder {
            height: 50px;
            border-bottom: 1px solid #000;
            margin-bottom: 4px;
        }

        .referred-by-row {
            margin-top: 12px;
            justify-content: flex-end;
        }

        .referred-by-col {
            flex: 0 0 245px;
            max-width: 245px;
            text-align: center;
        }

        .referred-by-label {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .line-caption {
            text-align: center;
        }

        .return-sign-row {
            margin-top: 10px;
            justify-content: center;
        }

        .return-sign-col {
            flex: 0 0 260px;
            max-width: 260px;
            text-align: center;
        }

        .small {
            font-size: 12px;
        }

        .divider {
            border-top: 1px dashed #000;
            margin: 12px 0 8px;
        }

        .print-mode .print-wrapper {
            overflow: hidden;
        }

        @media print {
            body {
                width: 210mm;
                height: 297mm;
                display: flex;
                justify-content: center;
            }

            .print-wrapper {
                margin: 0;
                width: 210mm;
                height: 297mm;
                padding: 6mm;
                overflow: hidden;
            }

            .page {
                border: 1px solid #000;
                overflow: visible;
            }
        }
    </style>
</head>

<?php
$patient = $data[0] ?? null;
$downloadMode = !empty($_GET['download']);
$displayAge = '';

if (is_object($patient) || is_array($patient)) {
    $rawAge = is_object($patient)
        ? ($patient->age ?? $patient->Age ?? null)
        : ($patient['age'] ?? $patient['Age'] ?? null);
    $displayAge = trim((string) $rawAge);

    if ($displayAge === '') {
        $dob = is_object($patient)
            ? ($patient->birthdate ?? $patient->BirthDate ?? $patient->Birthdate ?? $patient->bdate ?? $patient->DOB ?? null)
            : ($patient['birthdate'] ?? $patient['BirthDate'] ?? $patient['Birthdate'] ?? $patient['bdate'] ?? $patient['DOB'] ?? null);

        if (!empty($dob)) {
            try {
                $displayAge = (string) ((new DateTime($dob))->diff(new DateTime('today'))->y);
            } catch (Exception $e) {
                $displayAge = '';
            }
        }
    }
}
?>

<body class="<?= $downloadMode ? 'print-mode' : ''; ?>">
    <div class="print-wrapper">
        <div class="page" id="referral-page">
            <div class="header">
                <img class="logo" src="<?= base_url(); ?>assets/images/report/ke.png" alt="">
                <div>Republic of the Philippines</div>
                <div>Department of Education</div>
                <div>Region XI</div>
                <div>Schools Division of <?= htmlspecialchars($settings[0]->division ?? ''); ?></div>
                <div class="title">Referral Slip</div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="label">School:</div>
                    <div class="line"><?= htmlspecialchars($patient->school ?? ''); ?></div>
                </div>
                <div class="col">
                    <div class="label">Date:</div>
                    <div class="line"><?= date('F d, Y'); ?></div>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="label">Referral Facility (Agency):</div>
                    <div class="line"><?= htmlspecialchars($patient->referral_facility ?? ''); ?></div>
                </div>
            </div>

            <div class="section-title">Respectfully referring to you:</div>

            <div class="row">
                <div class="col">
                    <div class="label">Name:</div>
                    <div class="line"><?= htmlspecialchars(($patient->FirstName ?? '') . ' ' . ($patient->MiddleName ?? '') . ' ' . ($patient->LastName ?? '')); ?></div>
                </div>
                <div class="col" style="max-width: 90px;">
                    <div class="label">Age:</div>
                    <div class="line"><?= htmlspecialchars($displayAge); ?></div>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="label">Date of Birth:</div>
                    <div class="line"><?= htmlspecialchars($patient->birthdate ?? ''); ?></div>
                </div>
                <div class="col">
                    <div class="label">Contact No.:</div>
                    <div class="line"><?= htmlspecialchars($patient->contact ?? ''); ?></div>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="label">Name of Parent/Guardian:</div>
                    <div class="line"><?= htmlspecialchars($patient->referral_guardian ?? ''); ?></div>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="label">Address:</div>
                    <div class="line"><?= htmlspecialchars($patient->address ?? ''); ?></div>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="label">Chief Complaint:</div>
                    <div class="line"><?= htmlspecialchars($patient->complaint ?? ''); ?></div>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="label">History of Present Illness:</div>
                    <div class="line"><?= htmlspecialchars($patient->illness_history ?? ''); ?></div>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="label">Diagnosis:</div>
                    <div class="line"><?= htmlspecialchars($patient->diagnosis ?? ''); ?></div>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="label">Treatment/Management:</div>
                    <div class="line"><?= htmlspecialchars($patient->treatment ?? ''); ?></div>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="label">Reason/s for Referral:</div>
                    <div class="line"><?= htmlspecialchars($patient->referral_reason ?? ''); ?></div>
                </div>
            </div>

            <div class="row referred-by-row">
                <div class="col referred-by-col">
                    <div class="referred-by-label">Referred by:</div>
                    <?php if (!empty($med_setting[0]->signature)): ?>
                        <img class="signature" src="<?= base_url('assets/images/' . $med_setting[0]->signature) ?>" alt="Signature">
                    <?php else: ?>
                        <div class="signature-placeholder"></div>
                    <?php endif; ?>
                    <div class="line"><?= htmlspecialchars($med_setting[0]->doc_name ?? ''); ?></div>
                    <div class="small line-caption">Name and Signature</div>
                    <div class="line"><?= htmlspecialchars($med_setting[0]->position ?? ''); ?></div>
                    <div class="small line-caption">Designation</div>
                </div>
            </div>

            <div class="divider"></div>

            <div class="section-title" style="text-align:center; margin-top: 6px;">Return Slip</div>
            <div class="small" style="text-align:center; margin-bottom: 6px;">Note: To be detached from upper portion and sent back to the school.</div>

            <div class="row">
                <div class="col">
                    <div class="label">Returned to:</div>
                    <div class="line"></div>
                </div>
                <div class="col">
                    <div class="label">Date Referred:</div>
                    <div class="line"></div>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="label">Name of Patient:</div>
                    <div class="line"><?= htmlspecialchars(($patient->FirstName ?? '') . ' ' . ($patient->MiddleName ?? '') . ' ' . ($patient->LastName ?? '')); ?></div>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="label">Chief Complaint/s:</div>
                    <div class="line"><?= htmlspecialchars($patient->complaint ?? ''); ?></div>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="label">Findings:</div>
                    <div class="line"></div>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="label">Action/Recommendations:</div>
                    <div class="line"></div>
                </div>
            </div>

            <div class="row return-sign-row">
                <div class="col return-sign-col">
                    <div class="line"></div>
                    <div class="small line-caption">Name & Signature</div>
                    <div class="line"></div>
                    <div class="small line-caption">Designation</div>
                </div>
            </div>
        </div>
    </div>

    <?php if ($downloadMode): ?>
        <script>
            function fitReferralToOnePage() {
                var wrapper = document.querySelector('.print-wrapper');
                var page = document.getElementById('referral-page');
                if (!wrapper || !page) {
                    return;
                }

                page.style.transform = 'none';
                page.style.width = '100%';
                page.style.margin = '0';
                page.style.transformOrigin = 'top center';

                var wrapperStyle = window.getComputedStyle(wrapper);
                var padLeft = parseFloat(wrapperStyle.paddingLeft) || 0;
                var padRight = parseFloat(wrapperStyle.paddingRight) || 0;
                var padTop = parseFloat(wrapperStyle.paddingTop) || 0;
                var padBottom = parseFloat(wrapperStyle.paddingBottom) || 0;
                var availableWidth = wrapper.clientWidth - padLeft - padRight;
                var availableHeight = wrapper.clientHeight - padTop - padBottom;

                page.style.width = availableWidth + 'px';

                // Force reflow before measuring.
                page.offsetHeight;

                var contentWidth = page.scrollWidth;
                var contentHeight = page.scrollHeight;
                var widthScale = contentWidth > 0 ? (availableWidth / contentWidth) : 1;
                var heightScale = contentHeight > 0 ? (availableHeight / contentHeight) : 1;
                var scale = Math.min(1, widthScale, heightScale);

                if (scale < 1) {
                    var safeScale = Math.max(0.1, scale - 0.005);
                    page.style.transform = 'scale(' + safeScale + ')';
                }
            }

            window.addEventListener('load', function() {
                fitReferralToOnePage();
                window.addEventListener('beforeprint', fitReferralToOnePage);
                setTimeout(function() {
                    window.print();
                }, 120);
            });
        </script>
    <?php endif; ?>
</body>

</html>
