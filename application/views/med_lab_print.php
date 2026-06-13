<?php
$parse_selected = function($raw) {
    $value = trim((string) $raw);
    if ($value === '') {
        return [];
    }
    $protected = [
        'TPAG, A/G Ratio' => '__OPT_TPAG_AG__',
    ];
    $value = strtr($value, $protected);
    $parts = array_map('trim', explode(',', $value));
    $parts = array_filter($parts, function($item) {
        return $item !== '';
    });
    $parts = array_map(function($item) use ($protected) {
        return strtr($item, array_flip($protected));
    }, $parts);
    return array_values(array_unique($parts));
};

$is_checked = function(array $selected, $option) {
    foreach ($selected as $item) {
        if ($item === $option) {
            return true;
        }
        if ($option === 'Others' && stripos($item, 'Others:') === 0) {
            return true;
        }
    }
    return false;
};

$extract_other = function(array $selected) {
    foreach ($selected as $item) {
        if (stripos($item, 'Others:') === 0) {
            return trim(substr($item, 7));
        }
    }
    return '';
};

$tests = [
    'lab_test' => $parse_selected($row->lab_test ?? ''),
    'bleed_test' => $parse_selected($row->bleed_test ?? ''),
    'hepatitis_test' => $parse_selected($row->hepatitis_test ?? ''),
    'cardiac' => $parse_selected($row->cardiac ?? ''),
    'blood_test' => $parse_selected($row->blood_test ?? ''),
    'liver_profile' => $parse_selected($row->liver_profile ?? ''),
    'renal_func' => $parse_selected($row->renal_func ?? ''),
    'serology' => $parse_selected($row->serology ?? ''),
    'thyroid' => $parse_selected($row->thyroid ?? ''),
    'x_ray' => $parse_selected($row->x_ray ?? ''),
    'ultrasound' => $parse_selected($row->ultrasound ?? ''),
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Laboratory Request Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #111;
        }

        .actions {
            margin-bottom: 10px;
        }

        .actions button {
            border: 1px solid #0f4c81;
            background: #0f4c81;
            color: #fff;
            padding: 8px 12px;
            border-radius: 3px;
            cursor: pointer;
        }

        .frame {
            border: 2px solid #111;
            padding: 14px;
        }

        .title {
            text-align: center;
            font-weight: 700;
            margin-bottom: 8px;
            font-size: 22px;
        }

        .subhead {
            font-size: 13px;
            margin-bottom: 10px;
        }

        .meta {
            font-size: 13px;
            margin-bottom: 12px;
            line-height: 1.5;
        }

        .meta strong {
            display: inline-block;
            min-width: 90px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px 14px;
        }

        .section {
            break-inside: avoid;
        }

        .section-title {
            font-weight: 700;
            font-size: 14px;
            margin-bottom: 3px;
        }

        .option {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            margin: 2px 0;
            padding: 1px 2px;
            border-radius: 3px;
        }

        .box {
            width: 12px;
            height: 12px;
            border: 1px solid #222;
            display: inline-block;
            flex: 0 0 auto;
            position: relative;
        }

        .option.selected {
            background: #ffe2e6;
        }

        .option.selected .box {
            background: #d90429;
            border-color: #d90429;
        }

        .other-note {
            margin-left: 18px;
            margin-top: 2px;
            font-size: 12px;
            border-bottom: 1px solid #444;
            min-height: 16px;
        }

        @media print {
            .actions {
                display: none;
            }
            body {
                margin: 0;
                padding: 14px;
            }

            /* In print, selected tests use a visible check mark even if backgrounds are disabled. */
            .option.selected .box {
                background: transparent !important;
                border-color: #111 !important;
            }

            .option.selected .box::after {
                content: '\2713';
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -58%);
                font-size: 12px;
                font-weight: 700;
                line-height: 1;
                color: #111;
            }
        }
    </style>
</head>
<body>
    <div class="actions">
        <button type="button" onclick="window.print()">Print</button>
    </div>

    <div class="frame">
        <div class="title">LABORATORY REQUEST FORM</div>
        <div class="subhead">Date Requested: <?= htmlspecialchars((string) ($row->date_request ?? '')); ?></div>

        <div class="meta">
            <div><strong>Name:</strong> <?= htmlspecialchars(trim((string) ($row->FirstName ?? '') . ' ' . (string) ($row->MiddleName ?? '') . ' ' . (string) ($row->LastName ?? ''))); ?></div>
            <div><strong>Age / Sex:</strong> <?= htmlspecialchars((string) ($row->age ?? '')); ?> / <?= htmlspecialchars((string) ($row->sex ?? '')); ?></div>
            <div><strong>Address:</strong> <?= htmlspecialchars((string) ($row->address ?? '')); ?></div>
            <div><strong>District:</strong> <?= htmlspecialchars((string) ($row->district ?? '')); ?> | <strong>School:</strong> <?= htmlspecialchars((string) ($row->school ?? '')); ?></div>
        </div>

        <div class="grid">
            <div class="section">
                <div class="section-title">LABORATORY TEST</div>
                <?php $options = ['Urinalysis', 'Fecalysis', 'CBC w/ Platelet', 'Retic Count', 'Pregnancy Test(Urine)', 'Pregnancy Test(Serum)', 'Widal Test', 'Blood Typing/Rh']; ?>
                <?php foreach ($options as $option): ?>
                    <div class="option <?= $is_checked($tests['lab_test'], $option) ? 'selected' : ''; ?>">
                        <span class="box"></span><span><?= htmlspecialchars($option); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="section">
                <div class="section-title">BLEEDING PARAMETERS</div>
                <?php $options = ['Bleeding Time', 'Clotting Time', 'Prothrombin (PT)', 'Partial Thromboplastin (aPTT)']; ?>
                <?php foreach ($options as $option): ?>
                    <div class="option <?= $is_checked($tests['bleed_test'], $option) ? 'selected' : ''; ?>">
                        <span class="box"></span><span><?= htmlspecialchars($option); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="section">
                <div class="section-title">HEPATITIS MARKERS</div>
                <?php $options = ['HBsag', 'Anti-HBs', 'Anti-HBc', 'Anti-HBe', 'Hepa-A Profile', 'HBV DNA', 'HBeAg']; ?>
                <?php foreach ($options as $option): ?>
                    <div class="option <?= $is_checked($tests['hepatitis_test'], $option) ? 'selected' : ''; ?>">
                        <span class="box"></span><span><?= htmlspecialchars($option); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="section">
                <div class="section-title">CARDIAC FUNCTION</div>
                <?php $options = ['Total CPK', 'LDH', 'Troponin I', 'Troponin T']; ?>
                <?php foreach ($options as $option): ?>
                    <div class="option <?= $is_checked($tests['cardiac'], $option) ? 'selected' : ''; ?>">
                        <span class="box"></span><span><?= htmlspecialchars($option); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="section">
                <div class="section-title">BLOOD TEST</div>
                <?php $options = ['HbA1c', 'FBS', 'Serum Uric Acid', 'Lipid Profile', 'Electrolyte']; ?>
                <?php foreach ($options as $option): ?>
                    <div class="option <?= $is_checked($tests['blood_test'], $option) ? 'selected' : ''; ?>">
                        <span class="box"></span><span><?= htmlspecialchars($option); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="section">
                <div class="section-title">LIVER PROFILE</div>
                <?php $options = ['SGOT/AST', 'SGPT/ALT', 'Bilirubin Panel', 'Alkaline Phosphatase', 'TPAG, A/G Ratio']; ?>
                <?php foreach ($options as $option): ?>
                    <div class="option <?= $is_checked($tests['liver_profile'], $option) ? 'selected' : ''; ?>">
                        <span class="box"></span><span><?= htmlspecialchars($option); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="section">
                <div class="section-title">RENAL FUNCTION</div>
                <?php $options = ['BUA', 'BUN', 'Creatinine']; ?>
                <?php foreach ($options as $option): ?>
                    <div class="option <?= $is_checked($tests['renal_func'], $option) ? 'selected' : ''; ?>">
                        <span class="box"></span><span><?= htmlspecialchars($option); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="section">
                <div class="section-title">SEROLOGY</div>
                <?php $options = ['VDRL/RPR', 'CD4 Count', 'Others']; ?>
                <?php foreach ($options as $option): ?>
                    <div class="option <?= $is_checked($tests['serology'], $option) ? 'selected' : ''; ?>">
                        <span class="box"></span><span><?= htmlspecialchars($option); ?></span>
                    </div>
                <?php endforeach; ?>
                <div class="other-note"><?= htmlspecialchars($extract_other($tests['serology'])); ?></div>
            </div>

            <div class="section">
                <div class="section-title">THYROID FUNCTION</div>
                <?php $options = ['FT3', 'FT4', 'TSH', 'Others']; ?>
                <?php foreach ($options as $option): ?>
                    <div class="option <?= $is_checked($tests['thyroid'], $option) ? 'selected' : ''; ?>">
                        <span class="box"></span><span><?= htmlspecialchars($option); ?></span>
                    </div>
                <?php endforeach; ?>
                <div class="other-note"><?= htmlspecialchars($extract_other($tests['thyroid'])); ?></div>
            </div>

            <div class="section">
                <div class="section-title">X - RAY</div>
                <?php $options = ['Chest AP/L', 'Chest PA/L', 'Abdomen Supine', 'Electrocardiogram', '2D Echo', 'Others']; ?>
                <?php foreach ($options as $option): ?>
                    <div class="option <?= $is_checked($tests['x_ray'], $option) ? 'selected' : ''; ?>">
                        <span class="box"></span><span><?= htmlspecialchars($option); ?></span>
                    </div>
                <?php endforeach; ?>
                <div class="other-note"><?= htmlspecialchars($extract_other($tests['x_ray'])); ?></div>
            </div>

            <div class="section">
                <div class="section-title">ULTRASOUND</div>
                <?php $options = ['Breast', 'Transvaginal', 'Whole Abdomen', 'Lower Abdomen', 'Upper Abdomen', 'Thyroid', 'CT scan Plain/Contrast', 'KUB', 'Prostate', 'Pelvic', 'HBT', 'Inguinoscrotal', 'Others']; ?>
                <?php foreach ($options as $option): ?>
                    <div class="option <?= $is_checked($tests['ultrasound'], $option) ? 'selected' : ''; ?>">
                        <span class="box"></span><span><?= htmlspecialchars($option); ?></span>
                    </div>
                <?php endforeach; ?>
                <div class="other-note"><?= htmlspecialchars($extract_other($tests['ultrasound'])); ?></div>
            </div>
        </div>
    </div>
</body>
</html>
