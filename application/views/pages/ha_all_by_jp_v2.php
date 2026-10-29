<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Responsive bootstrap 4 admin template" name="description" />
    <meta content="Coderthemes" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="shortcut icon" href="<?= base_url(); ?>assets/images/hris.ico">
    <link href="<?= base_url(); ?>assets/css/renren_style.css" rel="stylesheet" type="text/css" />
    <link href="https://db.onlinewebfonts.com/a/0nH393RJctHgt1f2YvZvyruY" rel="stylesheet" type="text/css"/>
    <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></title>
    <style>
        .ier-meta { margin-bottom: 10px; table-layout: fixed; }
        .ier-meta, .ier-meta td { border: 0 !important; }
        .ier-meta td { padding: 3px 6px; vertical-align: bottom; }
        .ier-meta td.ier-meta-value { border-bottom: 1px solid #222 !important; }
        #myTable { table-layout: fixed; }
        #myTable th, #myTable td { vertical-align: middle; }
        #myTable td.ier-code-cell { white-space: nowrap; text-align: center; }
        #myTable td.ier-code-cell .ier-editable-text { white-space: nowrap; overflow-wrap: normal; }
        #myTable tr { page-break-inside: avoid; break-inside: avoid; }
        @media print {
            #btnExport, .ier-filter, .ier-editor { display: none !important; }
            #myTable thead { display: table-header-group; }
            #myTable th, #myTable td, #myTable th *, #myTable td * { font-size: 10.5px; }
            #myTable td.ier-detail-cell { text-align: left !important; }
            #myTable td.ier-tesda { background: transparent !important; }
            #myTable .ier-application-link {
                color: inherit !important;
                font-weight: normal !important;
                text-decoration: none !important;
            }
            .ier-editing .ier-editable-text {
                padding: 0;
                outline: 0 !important;
                background: transparent !important;
                box-shadow: none !important;
            }
        }
        .ier-filter {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin: -6px 0 18px;
        }
        .ier-filter a {
            display: inline-block;
            padding: 7px 13px;
            border: 1px solid #697785;
            border-radius: 4px;
            color: #263746;
            background: #fff;
            font-weight: 600;
            text-decoration: none;
        }
        .ier-filter a.is-active {
            border-color: #245b88;
            color: #fff;
            background: #245b88;
        }
        .ier-editor {
            position: sticky;
            top: 0;
            z-index: 20;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
            gap: 8px;
            margin: 0 0 14px;
            padding: 9px;
            border: 1px solid #b9c5cf;
            border-radius: 6px;
            background: rgba(247, 250, 252, .97);
            box-shadow: 0 2px 8px rgba(32, 49, 65, .12);
        }
        .ier-editor button {
            padding: 7px 14px;
            border: 1px solid #315b7d;
            border-radius: 4px;
            background: #fff;
            color: #24445e;
            cursor: pointer;
            font-weight: 700;
        }
        .ier-editor button.primary { color: #fff; background: #245b88; }
        .ier-editor button:disabled { cursor: wait; opacity: .6; }
        .ier-editor-help { color: #526270; font-size: 11px; }
        .ier-editor-status { min-width: 130px; font-weight: 700; }
        .ier-editor-status.is-error { color: #a82020; }
        .ier-editor-status.is-ok { color: #16713c; }
        .ier-editable-text {
            display: block;
            min-height: 1.3em;
            white-space: pre-wrap;
            overflow-wrap: anywhere;
        }
        .ier-editing .ier-editable-text {
            min-height: 1.8em;
            padding: 4px 5px;
            outline: 1px dashed #2d6fa3;
            outline-offset: -2px;
            background: #f7fcff;
            cursor: text;
        }
        .ier-editing .ier-editable-text:focus {
            outline: 2px solid #1e70ad;
            background: #fff;
            box-shadow: inset 0 0 0 1px #fff;
        }
        .ier-editing .ier-application-link { cursor: text; }
        .ier-top-label { font-weight: 600; }
        .ier-top-value { min-height: 1.7em; text-align: left; }
        .ier-record-state { color: #555; font-style: italic; }
        .ier-detail-cell { text-align: left !important; line-height: 1.45; }
        .ier-application-link {
            color: #0b4f80;
            font-weight: 700;
            text-decoration: underline;
        }
        .ier-tesda { background-color: #fff3a6 !important; }
        .ier-tesda .ier-application-link { color: #4d3b00; }
    </style>
</head>
<body>
<?php
    $ier_date_range = function ($from, $to) {
        $fmt = function ($value) {
            if (empty($value) || substr((string) $value, 0, 10) === '0000-00-00') { return ''; }
            $timestamp = strtotime((string) $value);
            return $timestamp ? date('M d, Y', $timestamp) : '';
        };
        $fromText = $fmt($from);
        $toText = $fmt($to);
        if ($fromText !== '' && $toText !== '') {
            return $fromText === $toText ? $fromText : $fromText . ' - ' . $toText;
        }
        return $fromText !== '' ? $fromText : $toText;
    };

    $ier_duration = function ($months) {
        $months = max(0, (int) $months);
        $years = intdiv($months, 12);
        $remaining = $months % 12;
        $parts = array();
        if ($years > 0) { $parts[] = $years . ' year' . ($years === 1 ? '' : 's'); }
        if ($remaining > 0) { $parts[] = $remaining . ' month' . ($remaining === 1 ? '' : 's'); }
        return empty($parts) ? '0 months' : implode(' and ', $parts);
    };

    $display_filter = isset($display_filter) ? $display_filter : 'all';
    $applicant_counts = isset($applicant_counts) ? $applicant_counts : array(
        'all' => count((array) $data),
        'qualified' => 0,
        'disqualified' => 0,
    );
    $ier_filter_url = function ($filter) {
        $query = $this->input->get();
        $query = is_array($query) ? $query : array();
        if ($filter === 'all') { unset($query['display']); } else { $query['display'] = $filter; }
        return current_url() . (!empty($query) ? '?' . http_build_query($query) : '');
    };

    $ier_inclusions = isset($ier_inclusions) && is_array($ier_inclusions) ? $ier_inclusions : array();
    $ier_degrees = isset($ier_degrees) && is_array($ier_degrees) ? $ier_degrees : array();
    $savedEdits = isset($ier_report_edits) && is_array($ier_report_edits) ? $ier_report_edits : array();
    $ierDefaults = array();
    $ierValue = function ($section, $key, $default, $appID = null) use ($savedEdits) {
        $source = $section === 'r'
            ? ($savedEdits['r'][(string) $appID] ?? array())
            : ($savedEdits[$section] ?? array());
        return is_array($source) && array_key_exists($key, $source)
            ? (string) $source[$key]
            : (string) $default;
    };
    $ierRender = function ($section, $key, $default, $appID = null, $class = '') use (&$ierDefaults, $ierValue) {
        $identity = $section . ':' . ($section === 'r' ? (string) $appID . ':' : '') . $key;
        $ierDefaults[$identity] = (string) $default;
        $attributes = ' data-ier-editable="1"'
            . ' data-ier-id="' . htmlspecialchars($identity, ENT_QUOTES, 'UTF-8') . '"'
            . ' data-ier-section="' . htmlspecialchars($section, ENT_QUOTES, 'UTF-8') . '"'
            . ' data-ier-key="' . htmlspecialchars($key, ENT_QUOTES, 'UTF-8') . '"';
        if ($section === 'r') { $attributes .= ' data-ier-app="' . (int) $appID . '"'; }
        $classes = trim('ier-editable-text ' . $class);
        return '<span class="' . htmlspecialchars($classes, ENT_QUOTES, 'UTF-8') . '"' . $attributes . '>'
            . htmlspecialchars($ierValue($section, $key, $default, $appID), ENT_QUOTES, 'UTF-8')
            . '</span>';
    };

    $topDefaults = array(
        'position_label' => 'Position:',
        'position' => trim((string) ($job->jobTitle ?? '')),
        'salary_label' => 'Salary Grade and Monthly Salary:',
        'salary' => '',
        'qualification_label' => 'Qualification Standards:',
        'qualification' => '',
        'education_label' => 'Education',
        'education' => '',
        'training_label' => 'Training',
        'training' => '',
        'experience_label' => 'Experience',
        'experience' => '',
        'eligibility_label' => 'Eligibility',
        'eligibility' => '',
    );
    $columnDefaults = array(
        'number' => 'No.',
        'application' => 'Application Code',
        'education' => 'Education',
        'training_group' => 'Training',
        'training_details' => 'Details',
        'training_hours' => 'Hours',
        'experience_group' => 'Experience',
        'experience_details' => 'Details',
        'experience_years' => 'Years',
        'eligibility' => 'Eligibility',
        'remarks' => "Remarks\n(Qualified or\nDisqualified)",
    );
?>
<div class="hwrap">
    <img class="logo" src="<?= base_url(); ?>assets/images/ke.png" alt="Department of Education logo">
    <p class="textwrap">
        <span class="rp">Republic of the Philippines</span>
        <span class="de">Department of Education</span>
        <span class="r">Region XI</span>
        <span class="r">Schools Division of <?= htmlspecialchars((string) ($mis_settings[0]->division ?? ''), ENT_QUOTES, 'UTF-8'); ?></span>
    </p>
    <iframe id="txtArea1" style="display:none"></iframe>
    <button id="btnExport" type="button" onclick="fnExcelReport();">EXPORT TO EXCEL</button>
</div>
<div class="blocker"></div>
<div class="wrap">
    <div class="inner">
        <h5 style="font-style:italic;">Annex D</h5>
        <h1 style="font-size:20px"><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></h1>

        <nav class="ier-filter" aria-label="Filter applicants by qualification status">
            <?php foreach (array('all' => 'All Applicants', 'qualified' => 'Qualified', 'disqualified' => 'Disqualified') as $filterKey => $filterLabel): ?>
                <a href="<?= htmlspecialchars($ier_filter_url($filterKey), ENT_QUOTES, 'UTF-8'); ?>"
                   class="<?= $display_filter === $filterKey ? 'is-active' : ''; ?>"
                   aria-current="<?= $display_filter === $filterKey ? 'page' : 'false'; ?>">
                    <?= $filterLabel; ?> (<?= (int) ($applicant_counts[$filterKey] ?? 0); ?>)
                </a>
            <?php endforeach; ?>
        </nav>

        <?php if (!empty($ier_can_edit)): ?>
            <div class="ier-editor" aria-label="IER editor">
                <button type="button" id="ierEditButton" class="primary">Edit IER</button>
                <button type="button" id="ierSaveButton" class="primary" hidden>Save changes</button>
                <button type="button" id="ierCancelButton" hidden>Cancel</button>
                <span class="ier-editor-help" id="ierEditorHelp">Edit the report directly like a document. Only changed text is saved.</span>
                <span class="ier-editor-status" id="ierEditorStatus" role="status" aria-live="polite"></span>
            </div>
        <?php endif; ?>

        <div id="ierReport">
        <table class="ier-meta">
            <colgroup><col style="width:3%"><col style="width:22%"><col style="width:75%"></colgroup>
            <tbody>
            <tr>
                <td style="text-align:left" colspan="2"><?= $ierRender('h', 'position_label', $topDefaults['position_label'], null, 'ier-top-label'); ?></td>
                <td class="ier-meta-value"><?= $ierRender('h', 'position', $topDefaults['position'], null, 'ier-top-value'); ?></td>
            </tr>
            <tr>
                <td style="text-align:left" colspan="2"><?= $ierRender('h', 'salary_label', $topDefaults['salary_label'], null, 'ier-top-label'); ?></td>
                <td class="ier-meta-value"><?= $ierRender('h', 'salary', $topDefaults['salary'], null, 'ier-top-value'); ?></td>
            </tr>
            <tr>
                <td style="text-align:left" colspan="2"><?= $ierRender('h', 'qualification_label', $topDefaults['qualification_label'], null, 'ier-top-label'); ?></td>
                <td class="ier-meta-value"><?= $ierRender('h', 'qualification', $topDefaults['qualification'], null, 'ier-top-value'); ?></td>
            </tr>
            <?php foreach (array('education', 'training', 'experience', 'eligibility') as $standard): ?>
                <tr>
                    <td></td>
                    <td style="text-align:left"><?= $ierRender('h', $standard . '_label', $topDefaults[$standard . '_label'], null, 'ier-top-label'); ?></td>
                    <td class="ier-meta-value"><?= $ierRender('h', $standard, $topDefaults[$standard], null, 'ier-top-value'); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <table class="data" id="myTable">
            <colgroup>
                <col style="width:4%">
                <col style="width:9%">
                <col style="width:13%">
                <col style="width:22%">
                <col style="width:5%">
                <col style="width:22%">
                <col style="width:8%">
                <col style="width:9%">
                <col style="width:8%">
            </colgroup>
            <thead>
            <tr>
                <th rowspan="2"><?= $ierRender('c', 'number', $columnDefaults['number']); ?></th>
                <th rowspan="2"><?= $ierRender('c', 'application', $columnDefaults['application']); ?></th>
                <th rowspan="2"><?= $ierRender('c', 'education', $columnDefaults['education']); ?></th>
                <th colspan="2"><?= $ierRender('c', 'training_group', $columnDefaults['training_group']); ?></th>
                <th colspan="3"><?= $ierRender('c', 'experience_group', $columnDefaults['experience_group']); ?></th>
                <th rowspan="2"><?= $ierRender('c', 'remarks', $columnDefaults['remarks']); ?></th>
            </tr>
            <tr>
                <th><?= $ierRender('c', 'training_details', $columnDefaults['training_details']); ?></th>
                <th><?= $ierRender('c', 'training_hours', $columnDefaults['training_hours']); ?></th>
                <th><?= $ierRender('c', 'experience_details', $columnDefaults['experience_details']); ?></th>
                <th><?= $ierRender('c', 'experience_years', $columnDefaults['experience_years']); ?></th>
                <th><?= $ierRender('c', 'eligibility', $columnDefaults['eligibility']); ?></th>
            </tr>
            </thead>
            <tbody>

            <?php $counter = 1; foreach ($data as $row):
                $personID = (string) ($row->id ?? '');
                $appID = (int) ($row->appID ?? 0);
                $allTrainings = $ier_trainings[$personID] ?? array();
                $allExperiences = $ier_experiences[$personID] ?? array();

                $relevantTrainings = array();
                $trainingHasNoAction = false;
                foreach ($allTrainings as $trainingRecord) {
                    $wasReviewed = $trainingRecord->updated_by !== null;
                    $stat = (int) ($trainingRecord->relevance_stat ?? 0);
                    if ($wasReviewed && $stat === 1) { $relevantTrainings[] = $trainingRecord; }
                    elseif (!$wasReviewed || $stat === 0) { $trainingHasNoAction = true; }
                }
                $trainingState = !empty($relevantTrainings)
                    ? 'relevant'
                    : (!empty($allTrainings) && $trainingHasNoAction ? 'no_action' : 'none');

                $trainingTotal = 0;
                $trainingLines = array();
                foreach ($relevantTrainings as $trainingRecord) {
                    $creditHours = $trainingRecord->credited_hours !== null
                        ? (float) $trainingRecord->credited_hours
                        : (float) $trainingRecord->noHours;
                    $trainingTotal += $creditHours;
                    $hoursText = $creditHours == (int) $creditHours ? (string) (int) $creditHours : (string) $creditHours;
                    $parts = array(
                        trim((string) ($trainingRecord->trainingTitle ?? '')),
                        trim((string) ($trainingRecord->sponsor ?? '')),
                        $ier_date_range($trainingRecord->dateStarted ?? null, $trainingRecord->dateFinished ?? null),
                        $hoursText . ' hrs',
                    );
                    $trainingLines[] = implode(' - ', array_values(array_filter($parts, function ($part) { return $part !== ''; })));
                }
                $included = $ier_inclusions[$appID] ?? array();
                $tesdaIncluded = $included['tesda'] ?? null;
                if ($tesdaIncluded) {
                    $tesdaHours = $tesdaIncluded->hours !== null ? (float) $tesdaIncluded->hours : null;
                    $parts = array(
                        trim((string) ($row->tesda_certificates ?? '')) ?: 'TESDA National Certificate',
                        'TESDA',
                        $ier_date_range($tesdaIncluded->date_from, $tesdaIncluded->date_to),
                        $tesdaHours !== null ? ($tesdaHours == (int) $tesdaHours ? (int) $tesdaHours : $tesdaHours) . ' hrs' : '',
                    );
                    array_unshift($trainingLines, implode(' - ', array_values(array_filter($parts, function ($part) { return $part !== ''; }))));
                    $trainingTotal += (float) $tesdaHours;
                    if ($trainingState === 'no_action') { $trainingLines[] = 'Other trainings: No action yet'; }
                }
                $trainingDetails = $trainingState === 'relevant' || $tesdaIncluded
                    ? implode("\n", $trainingLines)
                    : ($trainingState === 'no_action' ? 'No action yet' : 'No relevant training');
                $trainingHours = $trainingState === 'no_action' && !$tesdaIncluded
                    ? '—'
                    : ($trainingTotal == (int) $trainingTotal ? (string) (int) $trainingTotal : (string) $trainingTotal);

                $relevantExperiences = array();
                $experienceHasNoAction = false;
                foreach ($allExperiences as $experienceRecord) {
                    $wasReviewed = $experienceRecord->updated_by !== null;
                    $stat = (int) ($experienceRecord->relevance_stat ?? 0);
                    if ($wasReviewed && $stat === 1) { $relevantExperiences[] = $experienceRecord; }
                    elseif (!$wasReviewed || $stat === 0) { $experienceHasNoAction = true; }
                }
                $experienceState = !empty($relevantExperiences)
                    ? 'relevant'
                    : (!empty($allExperiences) && $experienceHasNoAction ? 'no_action' : 'none');

                $experienceMonths = 0;
                $experienceLines = array();
                foreach ($relevantExperiences as $experienceRecord) {
                    $from = !empty($experienceRecord->date_from) && $experienceRecord->date_from !== '0000-00-00' ? $experienceRecord->date_from : null;
                    $to = !empty($experienceRecord->date_to) && $experienceRecord->date_to !== '0000-00-00' ? $experienceRecord->date_to : null;
                    $creditMonths = $experienceRecord->credited_months !== null
                        ? (int) $experienceRecord->credited_months
                        : (($from && $to) ? $this->Reg->experience_months($from, $to) : ((int) $experienceRecord->ny * 12) + (int) $experienceRecord->nm);
                    $experienceMonths += $creditMonths;
                    $parts = array(
                        trim((string) ($experienceRecord->title ?? '')),
                        trim((string) ($experienceRecord->position_title ?? '')),
                        $ier_date_range($experienceRecord->date_from ?? null, $experienceRecord->date_to ?? null),
                        'Duration: ' . $ier_duration($creditMonths),
                    );
                    $experienceLines[] = implode(' - ', array_values(array_filter($parts, function ($part) { return $part !== ''; })));
                }
                $experienceDetails = $experienceState === 'relevant'
                    ? implode("\n", $experienceLines)
                    : ($experienceState === 'no_action' ? 'No action yet' : 'No relevant experience');
                $experienceYears = $experienceState === 'no_action' ? '—' : $ier_duration($experienceMonths);

                $eligibilityRating = trim((string) ($row->csEligibilityRating ?? ''));
                $eligibility = trim((string) ($row->csEligibility ?? ''));
                if ($eligibilityRating !== '') { $eligibility .= ($eligibility !== '' ? ' - ' : '') . $eligibilityRating; }
                $remarks = (int) ($row->dq ?? 0) === 1 ? 'Qualified' : ((int) ($row->dq ?? 0) === 2 ? 'Disqualified' : '');
                $educationLines = array(trim((string) ($row->bachelor ?? '')));
                $degreeRow = $ier_degrees[$personID] ?? null;
                foreach (array('master' => "Master's", 'doctor' => "Doctor's") as $degree => $degreeLabel) {
                    $degreeName = trim((string) ($degreeRow->{$degree} ?? ''));
                    if (!isset($included[$degree]) || $degreeName === '') { continue; }
                    $degreeStat = (int) $degreeRow->{$degree . '_stat'};
                    $degreeUnits = (float) $degreeRow->{$degree . '_units'};
                    $degreeNote = $degreeStat === 2
                        ? ($degreeUnits > 0 ? ' (' . ($degreeUnits == (int) $degreeUnits ? (int) $degreeUnits : $degreeUnits) . ' units)' : '')
                        : ($degreeStat === 1 ? '' : ' (Not graduated)');
                    $educationLines[] = $degreeLabel . ': ' . $degreeName . $degreeNote;
                }
                $education = implode("\n", array_values(array_filter($educationLines, 'strlen')));

                // Yellow marks TESDA data that is not yet carried into the IER.
                $hasTesda = !$tesdaIncluded && (
                    trim((string) ($row->tesda_certificates ?? '')) !== ''
                    || trim((string) ($row->tesda_training_file ?? '')) !== ''
                    || trim((string) ($row->tesda_certificate_file ?? '')) !== ''
                );
                $applicationUrl = base_url()
                    . 'Pages/ma/' . rawurlencode($personID)
                    . '/' . rawurlencode((string) ($row->jobID ?? ''))
                    . '/' . rawurlencode((string) ($row->pre_school ?? ''))
                    . '/' . rawurlencode((string) $appID);
            ?>
                <tr data-ier-app-row="<?= $appID; ?>">
                    <td><?= $ierRender('r', 'number', (string) $counter, $appID); ?></td>
                    <td class="ier-code-cell<?= $hasTesda ? ' ier-tesda' : ''; ?>"<?=$hasTesda ? ' title="TESDA National Certificate not yet included in this IER"' : ''; ?>>
                        <a class="ier-application-link" href="<?= htmlspecialchars($applicationUrl, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer">
                            <?= $ierRender('r', 'application', (string) ($row->code ?? ''), $appID); ?>
                        </a>
                    </td>
                    <td class="ier-detail-cell"><?= $ierRender('r', 'education', $education, $appID); ?></td>
                    <td class="ier-detail-cell"><?= $ierRender('r', 'training_details', $trainingDetails, $appID, $trainingState !== 'relevant' && !$tesdaIncluded ? 'ier-record-state' : ''); ?></td>
                    <td><?= $ierRender('r', 'training_hours', $trainingHours, $appID); ?></td>
                    <td class="ier-detail-cell"><?= $ierRender('r', 'experience_details', $experienceDetails, $appID, $experienceState !== 'relevant' ? 'ier-record-state' : ''); ?></td>
                    <td><?= $ierRender('r', 'experience_years', $experienceYears, $appID); ?></td>
                    <td><?= $ierRender('r', 'eligibility', $eligibility, $appID); ?></td>
                    <td><?= $ierRender('r', 'remarks', $remarks, $appID); ?></td>
                </tr>
            <?php $counter++; endforeach; ?>
            <?php if (empty($data)): ?>
                <tr><td colspan="9" style="padding:20px; color:#555;">No <?= $display_filter === 'all' ? '' : htmlspecialchars($display_filter, ENT_QUOTES, 'UTF-8') . ' '; ?>applicants found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
        </div>
    </div>
</div>

<script id="ierDefaults" type="application/json"><?= json_encode($ierDefaults, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?></script>
<script>
(function () {
    'use strict';
    var table = document.getElementById('ierReport');
    var defaultsNode = document.getElementById('ierDefaults');
    var defaults = defaultsNode ? JSON.parse(defaultsNode.textContent || '{}') : {};
    var editButton = document.getElementById('ierEditButton');
    var saveButton = document.getElementById('ierSaveButton');
    var cancelButton = document.getElementById('ierCancelButton');
    var help = document.getElementById('ierEditorHelp');
    var status = document.getElementById('ierEditorStatus');
    var editing = false;
    var dirty = false;

    function editableElements() {
        return table ? Array.prototype.slice.call(table.querySelectorAll('[data-ier-editable="1"]')) : [];
    }
    function normalizedText(element) {
        return (element.innerText || element.textContent || '')
            .replace(/\r\n?/g, '\n')
            .replace(/[ \t]+\n/g, '\n')
            .replace(/^\s+|\s+$/g, '');
    }
    function setStatus(message, type) {
        if (!status) return;
        status.textContent = message || '';
        status.className = 'ier-editor-status' + (type ? ' is-' + type : '');
    }
    function setEditing(enabled) {
        editing = enabled;
        if (!table) return;
        table.classList.toggle('ier-editing', enabled);
        editableElements().forEach(function (element) {
            if (enabled) {
                element.setAttribute('contenteditable', 'true');
                element.setAttribute('spellcheck', 'true');
                element.setAttribute('role', 'textbox');
                element.setAttribute('aria-multiline', 'true');
            } else {
                element.removeAttribute('contenteditable');
                element.removeAttribute('spellcheck');
                element.removeAttribute('role');
                element.removeAttribute('aria-multiline');
            }
        });
        if (editButton) editButton.hidden = enabled;
        if (saveButton) saveButton.hidden = !enabled;
        if (cancelButton) cancelButton.hidden = !enabled;
        if (help) {
            help.textContent = enabled
                ? 'Click any highlighted text or cell. Press Enter for a new line and Ctrl+S to save.'
                : 'Edit the report directly like a document. Only changed text is saved.';
        }
        setStatus('', '');
    }
    function collectEdits() {
        var edits = {h: {}, c: {}, r: {}};
        editableElements().forEach(function (element) {
            var identity = element.getAttribute('data-ier-id');
            var current = normalizedText(element);
            var original = Object.prototype.hasOwnProperty.call(defaults, identity) ? String(defaults[identity]) : '';
            original = original.replace(/\r\n?/g, '\n').replace(/[ \t]+\n/g, '\n').replace(/^\s+|\s+$/g, '');
            if (current === original) return;
            var section = element.getAttribute('data-ier-section');
            var key = element.getAttribute('data-ier-key');
            if (section === 'r') {
                var appID = element.getAttribute('data-ier-app');
                if (!edits.r[appID]) edits.r[appID] = {};
                edits.r[appID][key] = current;
            } else if (section === 'h' || section === 'c') {
                edits[section][key] = current;
            }
        });
        return edits;
    }

    if (editButton) {
        editButton.addEventListener('click', function () {
            dirty = false;
            setEditing(true);
            var position = table.querySelector('[data-ier-id="h:position"]');
            if (position) position.focus();
        });
    }
    if (table) {
        table.addEventListener('input', function (event) {
            if (editing && event.target.closest('[data-ier-editable="1"]')) dirty = true;
        });
        table.addEventListener('paste', function (event) {
            var editable = event.target.closest('[data-ier-editable="1"]');
            if (!editing || !editable) return;
            event.preventDefault();
            var text = (event.clipboardData || window.clipboardData).getData('text/plain');
            document.execCommand('insertText', false, text);
        });
        table.addEventListener('click', function (event) {
            if (editing && event.target.closest('.ier-application-link')) event.preventDefault();
        });
    }
    if (cancelButton) {
        cancelButton.addEventListener('click', function () {
            if (!dirty || window.confirm('Discard the unsaved IER changes?')) window.location.reload();
        });
    }
    if (saveButton) {
        saveButton.addEventListener('click', function () {
            var visibleAppIDs = Array.prototype.map.call(
                table.querySelectorAll('[data-ier-app-row]'),
                function (row) { return row.getAttribute('data-ier-app-row'); }
            );
            var form = new URLSearchParams();
            form.append('job_id', <?= (int) ($job->jobID ?? 0); ?>);
            form.append('edits', JSON.stringify(collectEdits()));
            form.append('visible_app_ids', JSON.stringify(visibleAppIDs));
            saveButton.disabled = true;
            cancelButton.disabled = true;
            setStatus('Saving…', '');
            fetch(<?= json_encode(base_url('Pages/save_ier_report_edits')); ?>, {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'},
                credentials: 'same-origin',
                body: form.toString()
            })
            .then(function (response) {
                return response.json().then(function (body) {
                    if (!response.ok || !body.ok) throw new Error(body.message || 'Unable to save the IER.');
                    return body;
                });
            })
            .then(function (body) {
                dirty = false;
                setStatus(body.message || 'Saved.', 'ok');
                window.setTimeout(function () { window.location.reload(); }, 500);
            })
            .catch(function (error) {
                setStatus(error.message || 'Unable to save the IER.', 'error');
                saveButton.disabled = false;
                cancelButton.disabled = false;
            });
        });
    }
    document.addEventListener('keydown', function (event) {
        if (editing && (event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 's') {
            event.preventDefault();
            if (saveButton && !saveButton.disabled) saveButton.click();
        }
    });
    window.addEventListener('beforeunload', function (event) {
        if (!editing || !dirty) return;
        event.preventDefault();
        event.returnValue = '';
    });
})();

function fnExcelReport() {
    var table = document.getElementById('ierReport');
    if (!table) return false;
    var clone = table.cloneNode(true);
    clone.querySelectorAll('[contenteditable]').forEach(function (node) { node.removeAttribute('contenteditable'); });
    clone.querySelectorAll('a').forEach(function (link) {
        var replacement = document.createElement('span');
        replacement.textContent = link.innerText || link.textContent || '';
        link.parentNode.replaceChild(replacement, link);
    });
    var html = '<html><head><meta charset="UTF-8"></head><body>' + clone.innerHTML + '</body></html>';
    var blob = new Blob([html], {type: 'application/vnd.ms-excel;charset=utf-8'});
    var url = URL.createObjectURL(blob);
    var download = document.createElement('a');
    download.href = url;
    download.download = <?= json_encode('IER-' . preg_replace('/[^A-Za-z0-9_-]+/', '-', (string) ($job->jobTitle ?? 'report')) . '.xls'); ?>;
    document.body.appendChild(download);
    download.click();
    document.body.removeChild(download);
    window.setTimeout(function () { URL.revokeObjectURL(url); }, 1000);
    return false;
}
</script>
</body>
</html>
