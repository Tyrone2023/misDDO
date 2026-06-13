<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=210mm, initial-scale=1.0">
    <title>Health Examination Record</title>
    <link rel="shortcut icon" href="<?= base_url(); ?>assets/images/hris.ico">

    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
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
            width: 80px;
            margin-bottom: 10px;
        }

        .hr {
            border-top: 1.5px solid black;
            margin: 10px 0;
        }

        h4 {
            margin-bottom: 5px;
        }

        .tophead {
            font-weight: bold;
            font-size: 13px;
            text-align: left;
            margin-top: 20px;
        }

        .form-section {
            font-size: 13px;
            text-align: left;
            margin-top: 20px;
        }

        .form-group {
            margin-bottom: 8px;
        }

        .form-group span {
            display: inline-block;
        }

        .label {
            font-weight: bold;
        }

        .underline {
            display: inline-block;
            border-bottom: 1px solid black;
            height: 1.2em;
            vertical-align: bottom;
        }

        .longer { width: 560px; }
        .longer1 { width: 510px; }
        .longer2 { width: 490px; }
        .long { width: 300px; }
        .med1 { width: 250px; }
        .med2 { width: 200px; }
        .med { width: 150px; }
        .short1 { width: 120px; }
        .short { width: 60px; }
        .xshort { width: 40px; }

        .indent {
            margin-left: 20px;
        }
    </style>
</head>

<body>

<div class="cert">
    <img class="logo" src="<?= base_url(); ?>assets/images/report/ke.png" alt="Logo">
    <p style="margin: 0;">
        <span>Republic of the Philippines</span><br />
        <strong>Department of Education</strong><br />
        <span>REGION XI</span><br />
        <span>SCHOOLS DIVISION OF <?= $settings[0]->division; ?></span>
    </p>
    <div class="hr"></div>
</div>

<div class="tophead">General Form 86</div>
<h4 style="text-align:center;">HEALTH EXAMINATION RECORD</h4>

<div class="form-section">
    <div class="form-group">
        <span class="label">NAME:</span> 
        <span class="underline long"><?= $health_record->fullName ?? ''; ?></span>
        &nbsp;&nbsp;&nbsp;
        <span class="label">DEPARTMENT:</span> 
        <span class="underline med1"><?= $health_record->department ?? ''; ?></span>
    </div>

    <div class="form-group">
        <span class="label">SCHOOL:</span> 
        <span class="underline med1"><?= $health_record->school ?? ''; ?></span>
        &nbsp;&nbsp;&nbsp;
        <span class="label">DISTRICT:</span> 
        <span class="underline long"><?= $health_record->district ?? ''; ?></span>
    </div>

    <div class="form-group">
        <span class="label">DATE OF BIRTH:</span> 
        <span class="underline med1"><?= $health_record->birthdate ?? ''; ?></span>
        &nbsp;&nbsp;&nbsp;
        <span class="label">SEX:</span> 
        <span class="underline short"><?= $health_record->sex ?? ''; ?></span>
        &nbsp;&nbsp;&nbsp;
        <span class="label">CIVIL STATUS:</span> 
        <span class="underline short1"><?= $health_record->civil_status ?? ''; ?></span>
    </div>
    <br>

    <div class="form-group indent">
        1. Date: <span class="underline med"><?= $health_record->exam_date ?? ''; ?></span> 
        Height: <span class="underline short1"><?= $health_record->height ?? ''; ?></span> 
        Weight: <span class="underline short1"><?= $health_record->weight ?? ''; ?></span> 
        BMI: <span class="underline short1"><?= $health_record->bmi ?? ''; ?></span>
    </div>

    <div class="form-group indent">2. Temperature: <span class="underline longer"><?= $health_record->temperature ?? ''; ?></span></div>
    <div class="form-group indent">3. Respiratory System: <span class="underline longer"><?= $health_record->respiratory ?? ''; ?></span></div>
    <div class="form-group indent">4. Fluorography Result: <span class="underline long"><?= $health_record->fluorography_result ?? ''; ?></span> Where: <span class="underline med2"><?= $health_record->fluorography_where ?? ''; ?></span></div>
    <div class="form-group indent">(Chest X-ray) Film #: <span class="underline long"><?= $health_record->film_no ?? ''; ?></span> Date Taken: <span class="underline med2"><?= $health_record->xray_date ?? ''; ?></span></div>
    <div class="form-group indent">Sputum Analysis: <span class="underline long"><?= $health_record->sputum ?? ''; ?></span></div>

    <div class="form-group indent">5. Circulatory System: <span class="underline long"><?= $health_record->circulatory ?? ''; ?></span> Blood Pressure: <span class="underline med"><?= $health_record->blood_pressure ?? ''; ?></span></div>
    <div class="form-group indent">6. Digestive System: <span class="underline longer"><?= $health_record->digestive ?? ''; ?></span></div>
    <div class="form-group indent">7. Genitourinary: <span class="underline longer"><?= $health_record->genitourinary ?? ''; ?></span></div>
    <div class="form-group indent">8. Urinalysis: <span class="underline longer"><?= $health_record->urinalysis ?? ''; ?></span></div>
    <div class="form-group indent">9. Skin: <span class="underline longer"><?= $health_record->skin ?? ''; ?></span></div>
    <div class="form-group indent">10. Locomotor: <span class="underline longer"><?= $health_record->locomotor ?? ''; ?></span></div>
    <div class="form-group indent">11. Nervous System: <span class="underline longer"><?= $health_record->nervous_system ?? ''; ?></span></div>
    <div class="form-group indent">12. Eyes, Conjunctiva, etc.: <span class="underline longer1"><?= $health_record->eyes ?? ''; ?></span></div>
    <div class="form-group indent">13. Color Perception: <span class="underline longer"><?= $health_record->color_perception ?? ''; ?></span></div>

    <div class="form-group indent">
        14. Vision:
        With Glasses: Far: <span class="underline med"><?= $health_record->vision_with_far ?? ''; ?></span> 
        Near: <span class="underline med"><?= $health_record->vision_with_near ?? ''; ?></span><br />
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        Without Glasses: Far: <span class="underline med"><?= $health_record->vision_without_far ?? ''; ?></span> 
        Near: <span class="underline med"><?= $health_record->vision_without_near ?? ''; ?></span>
    </div>

    <div class="form-group indent">15. Nose: <span class="underline longer"><?= $health_record->nose ?? ''; ?></span></div>
    <div class="form-group indent">16. Ear: <span class="underline longer"><?= $health_record->ear ?? ''; ?></span></div>
    <div class="form-group indent">17. Hearing: <span class="underline longer"><?= $health_record->hearing ?? ''; ?></span></div>
    <div class="form-group indent">18. Throat: <span class="underline longer"><?= $health_record->throat ?? ''; ?></span></div>
    <div class="form-group indent">19. Immunization: <span class="underline longer"><?= $health_record->immunization ?? ''; ?></span></div>
    <div class="form-group indent">20. Teeth and Gums: <span class="underline longer"><?= $health_record->teeth ?? ''; ?></span></div>
    <div class="form-group indent">Recommendation: <span class="underline longer"><?= $health_record->recommendation ?? ''; ?></span></div>
</div>

<br />

<div class="form-group indent">21. Employee’s Signature: <span class="underline longer1"><?= $health_record->emp_signature ?? ''; ?></span></div>
<div class="form-group indent">22. Employee’s Printed Name: <span class="underline longer2"><?= $health_record->fullName ?? ''; ?></span></div><br>
<div class="form-group indent">23. Physician’s Signature: <span class="underline longer1"><?= $health_record->physician_signature ?? ''; ?></span></div>
<div class="form-group indent">24. Physician’s Name: <span class="underline med1"><?= $health_record->physician_name ?? ''; ?></span> 
License No.: <span class="underline med2"><?= $health_record->license_no ?? ''; ?></span></div>

<br><br><br>

</body>
</html>
