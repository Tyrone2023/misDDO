<?php
/**
 * Enhanced CS Form No. 2 (Revised 2025) printable RAI.
 * Static labels and certification text follow the supplied CSC workbook;
 * report and applicant values are the only dynamic portions.
 */
defined('BASEPATH') OR exit('No direct script access allowed');
$header = isset($header) && is_array($header) ? $header : array();
$pages = isset($pages) && is_array($pages) ? $pages : array();
$isDraft = !empty($isDraft);
$reportId = isset($reportId) ? (int) $reportId : 0;
$raiToken = isset($raiToken) ? (string) $raiToken : '';
$savePreviewUrl = isset($savePreviewUrl) ? (string) $savePreviewUrl : '';
$canSave = !$isDraft && $reportId > 0 && $raiToken !== '' && $savePreviewUrl !== '';
$esc = static function ($value) { return html_escape((string) ($value === null ? '' : $value)); };
$raw = static function ($key) use ($header) {
    return isset($header[$key]) && is_scalar($header[$key]) ? trim((string) $header[$key]) : '';
};
$monthNames = array(1=>'JANUARY',2=>'FEBRUARY',3=>'MARCH',4=>'APRIL',5=>'MAY',6=>'JUNE',7=>'JULY',8=>'AUGUST',9=>'SEPTEMBER',10=>'OCTOBER',11=>'NOVEMBER',12=>'DECEMBER');
$monthIndex = (int) $raw('report_month');
$period = (isset($monthNames[$monthIndex]) ? $monthNames[$monthIndex] : '__________') . ' ' . $raw('report_year');

$fmtDate = static function ($value) {
    $value = trim((string) $value);
    if ($value === '') return '';
    $dt = DateTimeImmutable::createFromFormat('!Y-m-d', substr($value, 0, 10));
    return $dt && $dt->format('Y-m-d') === substr($value, 0, 10) ? $dt->format('m/d/Y') : $value;
};
$fmtRange = static function ($from, $to) use ($fmtDate) {
    $a = $fmtDate($from);
    $b = $fmtDate($to);
    return $a && $b ? $a . ' to ' . $b : ($a ?: $b);
};
$fmtMoney = static function ($value) {
    if ($value === '' || $value === null) return '';
    return is_numeric($value) && (float) $value >= 0 ? number_format((float) $value, 2, '.', ',') : (string) $value;
};
$itemValue = static function ($item, $key) {
    return isset($item[$key]) && is_scalar($item[$key]) ? trim((string) $item[$key]) : '';
};
$rowControl = static function ($rowIndex, $field, $value, $display = null, $placeholder = '') use ($esc, $canSave) {
    $shown = $display === null ? (string) $value : (string) $display;
    $html = '<span class="view-value">' . ($shown !== '' ? $esc($shown) : '&nbsp;') . '</span>';
    if ($canSave) {
        $html .= '<input class="edit-control cell-input" type="text" data-row="' . (int) $rowIndex
            . '" data-field="' . $esc($field) . '" value="' . $esc($value) . '"'
            . ($placeholder !== '' ? ' placeholder="' . $esc($placeholder) . '"' : '')
            . ' autocomplete="off" spellcheck="false">';
    }
    return $html;
};
$rowPairControl = static function ($rowIndex, $firstField, $firstValue, $secondField, $secondValue, $display, $firstPlaceholder, $secondPlaceholder) use ($esc, $canSave) {
    $html = '<span class="view-value">' . ($display !== '' ? $esc($display) : '&nbsp;') . '</span>';
    if ($canSave) {
        $html .= '<span class="edit-control edit-pair">'
            . '<input class="cell-input" type="text" data-row="' . (int) $rowIndex . '" data-field="' . $esc($firstField) . '" value="' . $esc($firstValue) . '" placeholder="' . $esc($firstPlaceholder) . '" autocomplete="off" spellcheck="false">'
            . '<span>/</span>'
            . '<input class="cell-input" type="text" data-row="' . (int) $rowIndex . '" data-field="' . $esc($secondField) . '" value="' . $esc($secondValue) . '" placeholder="' . $esc($secondPlaceholder) . '" autocomplete="off" spellcheck="false">'
            . '</span>';
    }
    return $html;
};
$headerControl = static function ($field, $value, $fallback = '', $textarea = false) use ($esc, $canSave) {
    $shown = trim((string) $value) !== '' ? (string) $value : (string) $fallback;
    $html = '<span class="view-value">' . ($shown !== '' ? $esc($shown) : '&nbsp;') . '</span>';
    if ($canSave) {
        if ($textarea) {
            $html .= '<textarea class="edit-control header-input" data-header-field="' . $esc($field) . '" rows="2">' . $esc($value) . '</textarea>';
        } else {
            $html .= '<input class="edit-control header-input" type="text" data-header-field="' . $esc($field) . '" value="' . $esc($value) . '" autocomplete="off" spellcheck="false">';
        }
    }
    return $html;
};

// These two labels are prescribed text in the supplied Division RAI template.
$agencyLine = 'Department of Education-DIVISION OF Davao De Oro';
$resolutionText = 'CSC Resolution No. 2100919 dated November 19, 2021';

$checklistItems = array(
    array('code'=>'appointment_form','label'=>'APPOINTMENT FORM (CS Form No. 33-B, Revised 2025) - Certified True Copy of appointment form','hrmo_status'=>'','csc_status'=>'','note'=>''),
    array('code'=>'plantilla_casual','label'=>'PLANTILLA OF CASUAL APPOINTMENT (CS Form No. 34-B, Revised 2025)- Certified True Copy for CSC','hrmo_status'=>'','csc_status'=>'','note'=>''),
    array('code'=>'personal_data_sheet','label'=>'PERSONAL DATA SHEET (CS Form No. 212, Revised 2025)','hrmo_status'=>'','csc_status'=>'','note'=>''),
    array('code'=>'proof_of_eligibility','label'=>"PROOF OF ELIGIBILITY - Report of rating/license/certificate of admission to the Bar/ certificate of eligibility/ eligibility card (original copy, authenticated copy, certified true copy, photocopy, scanned copy, or site/screen capture of eligibility using CSC's CSEVS, PRC's LERIS, or SC's Lawyer's List)",'hrmo_status'=>'','csc_status'=>'','note'=>''),
    array('code'=>'position_description_form','label'=>'POSITION DESCRIPTION FORM (DBM-CSC Form No. 1, Revised 2017)','hrmo_status'=>'','csc_status'=>'','note'=>''),
    array('code'=>'panunumpa','label'=>'PANUNUMPA SA KATUNGKULAN (SS Porma Blg. 32, Narebisa 2025)','hrmo_status'=>'','csc_status'=>'','note'=>''),
    array('code'=>'assumption_to_duty','label'=>'CERTIFICATE OF ASSUMPTION TO DUTY (CS Form No. 4, Revised 2025)','hrmo_status'=>'','csc_status'=>'','note'=>'')
);
$checklistData = json_decode($raw('checklist_json'), true);
if (!is_array($checklistData)) $checklistData = array();
$savedChecklistItems = isset($checklistData['items']) && is_array($checklistData['items'])
    ? $checklistData['items']
    : (isset($checklistData[0]) && is_array($checklistData[0]) ? $checklistData : array());
foreach ($checklistItems as $index => &$checklistItem) {
    $savedItem = array();
    foreach ($savedChecklistItems as $candidate) {
        if (is_array($candidate) && isset($candidate['code']) && $candidate['code'] === $checklistItem['code']) {
            $savedItem = $candidate;
            break;
        }
    }
    if (empty($savedItem) && isset($savedChecklistItems[$index]) && is_array($savedChecklistItems[$index])) {
        $savedItem = $savedChecklistItems[$index];
    }
    foreach (array('hrmo_status','csc_status','note') as $field) {
        $checklistItem[$field] = trim((string) ($savedItem[$field] ?? ''));
    }
}
unset($checklistItem);
$cscReceivingOfficer = trim((string) ($checklistData['csc_receiving_officer'] ?? ''));
$cscReceivingPosition = trim((string) ($checklistData['csc_receiving_position'] ?? ''));
if ($cscReceivingPosition === '') $cscReceivingPosition = 'CSC FO Receiving Officer';

// A blank report still renders the prescribed form instead of a placeholder.
$renderPages = $pages;
if (empty($renderPages)) {
    $renderPages[] = array('classification' => '', 'applicants' => array(), 'nothing_follows' => false);
}
$editableApplicants = array();
foreach ($renderPages as $editablePage) {
    foreach ((array) ($editablePage['applicants'] ?? array()) as $editableApplicant) {
        $editableApplicants[] = $editableApplicant;
    }
}
$editableChecklist = array(
    'items' => $checklistItems,
    'csc_receiving_officer' => $cscReceivingOfficer,
    'csc_receiving_position' => $cscReceivingPosition
);
$totalPrintedPages = count($renderPages) + 1; // one Checklist of Common Requirements page
$widths = array(1.461,4.626,7.547,6.865,2.872,5.599,1.849,1.849,3.213,4.528,13.779,2.969,4.090,5.355,4.673,4.820,3.895,3.895,3.992,3.749,4.235,4.138);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>RAI Preview — <?= $esc($period) ?></title>
<style>
*{box-sizing:border-box}html,body{margin:0;background:#e9edf2;color:#000;font-family:Arial,Helvetica,sans-serif}
.toolbar{position:sticky;top:0;z-index:10;background:#193a58;color:#fff;padding:10px 20px;display:flex;align-items:center;gap:12px;flex-wrap:wrap;justify-content:space-between;font-size:13px}
.toolbar button{background:#fff;border:0;border-radius:5px;padding:9px 16px;font-weight:700;cursor:pointer;color:#173750}.toolbar button:disabled{cursor:wait;opacity:.65}.toolbar .muted{color:#d4e4ed}.toolbar-actions{display:flex;align-items:center;gap:8px}.save-status{min-width:105px;text-align:right;color:#d4e4ed}.save-status.error{color:#ffd1d1}.save-status.success{color:#d7f7df}
.sheet{width:14in;height:8.5in;margin:16px auto;background:#fff;box-shadow:0 4px 24px rgba(0,0,0,.14);position:relative;padding:.5in .18in .11in;overflow:hidden;page-break-after:always;break-after:page}
.sheet:last-of-type{page-break-after:auto;break-after:auto}
.draft-mark{position:absolute;top:5px;left:50%;transform:translateX(-50%);font-size:7px;letter-spacing:1.2px;color:#777}
.form-ident{display:flex;justify-content:space-between;align-items:flex-start;min-height:.31in;font-size:8px;line-height:1.35}.form-ident strong{font-size:9px;font-style:italic}.form-ident .revision{font-style:italic}.accredited{border:1px solid #111;font-size:7px;font-weight:700;text-align:center;padding:2px 12px}
.form-title{text-align:center;font-size:13px;font-weight:800;line-height:1.1;margin:0}.form-month{text-align:center;font-size:9px;font-style:italic;margin:1px 0 4px}.form-month strong{font-size:10px}
.csc-use{width:25%;margin:0 0 3px auto;font-size:6.7px;line-height:1.25}.csc-use strong{display:block;border:1px solid #111;background:#d9d9d9;padding:3px 5px}.csc-use span{display:block;border:1px solid #111;border-top:0;background:#fff;padding:3px 5px 8px}
.agency-block{display:grid;grid-template-columns:minmax(0,1fr) auto minmax(0,1fr);align-items:end;gap:14px;font-size:7.4px;margin:2px 0 4px}.agency-row{display:flex;align-items:flex-end;justify-content:flex-start;gap:5px}.agency-label{font-weight:800}.agency-value{display:inline-block;border-bottom:1px solid #111;font-weight:800;font-size:8px;padding:0 5px 2px}.resolution{text-align:center;white-space:nowrap}.resolution strong{display:inline-block;border-bottom:1px solid #111;font-size:7.7px;padding:0 2px 1px}.agency-spacer{min-width:0}
.instructions{display:grid;grid-template-columns:1.18in 1fr;font-size:7px;line-height:1.25;margin-bottom:3px}.instructions-title{font-weight:800}.instructions-lines div{margin-bottom:1px}.instructions-lines .indent{padding-left:10px}
.section-caption{font-size:7.1px;font-weight:800;margin:7px 0 2px}
.rai-table{width:100%;border-collapse:collapse;table-layout:fixed;font-family:"Arial Narrow",Arial,sans-serif}
.rai-table th,.rai-table td{border:1px solid #111;text-align:center;vertical-align:middle;padding:1px;overflow-wrap:anywhere}
.rai-table thead th{font-size:5.65px;line-height:1.06;font-weight:400}
.rai-table .group-head{height:13px;font-size:6px}.rai-table .group-head .major-head{font-weight:800}.rai-table .minor-head{height:72px}.rai-table .minor-head th{vertical-align:middle}.rai-table .stack{height:84px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:3px;padding:2px 0;text-align:center}.rai-table .stack b{font-weight:400}.rai-table .tiny{font-size:5px;line-height:1.05}.rai-table tbody tr{height:17px}.rai-table tbody td{font-size:5.9px;line-height:1.02;text-transform:uppercase;white-space:normal}.rai-table tbody .left{text-align:left;padding-left:2px}.rai-table tbody .nothing td{font-weight:800;text-align:center;padding-left:0}.rai-table tbody .blank-row td{color:transparent}
.certifications{display:grid;grid-template-columns:36.4% 35.6% 28%;font-size:6.2px;line-height:1.23;margin-top:5px;min-height:1.05in}.cert,.post-audit{padding:3px 8px;display:flex;flex-direction:column}.cert-title{font-weight:800;margin-bottom:6px}.signature{width:78%;align-self:center;margin-top:auto;border-bottom:1px solid #000;padding:0 3px 2px;text-align:center;font-weight:800;font-size:6.8px;text-transform:uppercase}.designation{text-align:center;font-size:6.1px;min-height:14px;padding-top:2px}.post-audit .line{margin:auto auto 2px;width:78%;border-bottom:1px solid #000}.post-audit .designation{text-align:center}
.csc-only-label{font-size:6.2px;font-weight:800;line-height:1.25;margin-top:1px;padding:1px 4px}.remarks{border:1px solid #111;font-size:6.2px;line-height:1.25}.remarks .label{font-weight:800;padding:2px 4px;border-bottom:1px solid #111}.remarks-row{height:.13in;padding:2px 5px;white-space:pre-wrap}.remarks-row+.remarks-row{border-top:1px solid #111}
.checklist-sheet{padding:1.02in 1.18in .55in 1.82in}.checklist-table{width:100%;border-collapse:collapse;table-layout:fixed;font-size:8px}.checklist-table th,.checklist-table td{border:1px solid #111;padding:4px 6px;vertical-align:middle}.checklist-table th{font-weight:800}.checklist-title{text-align:left;font-size:11px}.checklist-instructions{font-weight:400;text-align:left}.checklist-no{text-align:center;width:4%}.checklist-requirement{width:72%}.checklist-status{text-align:center;width:12%;font-weight:700}.checklist-note{display:block;margin-top:3px;font-size:6.7px;font-style:italic;text-transform:none}.checklist-table .req-row{height:.47in}.checklist-table .proof-row{height:.64in;font-size:7.2px;line-height:1.2}.checklist-cert{height:.72in;font-size:7.2px;line-height:1.3;text-align:left}.checklist-signature{height:.55in;text-align:center;font-size:7.2px}.checklist-signature .sig-line{display:flex;align-items:flex-end;justify-content:center;width:90%;min-height:18px;margin:0 auto 2px;border-bottom:1px solid #111}.checklist-signature .sig-line strong{display:block;padding:0 3px 2px;text-transform:uppercase}.checklist-signature .sig-position{display:block}.page-two{text-align:right;margin-top:5px;font-size:7px}
.edit-control{display:none}.cell-input,.header-input,.checklist-input{width:100%;min-width:0;border:1px solid #b78600;border-radius:2px;background:#fffdf1;color:#111;font:inherit;line-height:1.15;padding:2px;text-align:center;text-transform:none}.rai-table td.left .cell-input{text-align:left}.edit-pair{align-items:center;gap:1px}.edit-pair input{width:calc(50% - 4px);min-width:0;border:1px solid #b78600;border-radius:2px;background:#fffdf1;font:inherit;padding:1px;text-align:center}.header-input{font-weight:700;text-transform:uppercase}.remarks .header-input{min-height:.28in;text-align:left;font-weight:400;text-transform:none;resize:vertical}.checklist-input{text-align:left;text-transform:none}.checklist-status .checklist-input{text-align:center;font-weight:700}.checklist-note-editor{margin-top:3px;min-height:26px;resize:vertical}.edit-control.invalid,.edit-pair input.invalid{border-color:#c62828!important;background:#ffe9e9!important;outline:1px solid #c62828}.rai-editing .view-value{display:none!important}.rai-editing .edit-control{display:block}.rai-editing .edit-pair{display:flex}.rai-editing .sheet{height:auto;min-height:8.5in;overflow:visible}.rai-editing .rai-table td:has(.edit-control),.rai-editing .signature:has(.edit-control),.rai-editing .designation:has(.edit-control),.rai-editing .remarks-row:has(.edit-control),.rai-editing .checklist-table td:has(.edit-control){background:#fff8d8}.editing-hint{display:none}.rai-editing .editing-hint{display:inline;color:#ffe69c}
@page{size:14in 8.5in;margin:0}
@media print{html,body{background:#fff!important;-webkit-print-color-adjust:exact;print-color-adjust:exact}.toolbar,.draft-mark,.edit-control{display:none!important}.view-value{display:inline!important}.sheet,.rai-editing .sheet{margin:0!important;box-shadow:none!important;width:14in;height:8.5in;min-height:0;padding:.5in .18in .11in;overflow:hidden;page-break-after:always;break-after:page}.checklist-sheet,.rai-editing .checklist-sheet{padding:1.02in 1.18in .55in 1.82in}.sheet:last-of-type{page-break-after:auto;break-after:auto}}
</style>
</head>
<body>
<div class="toolbar">
  <div><strong>RAI — Print Preview</strong> <span class="muted">| <?= $esc($period) ?> · <?= (int) ($applicantCount ?? 0) ?> applicant(s) · <?= $totalPrintedPages ?> printed page(s)</span></div>
  <div class="toolbar-actions">
    <?php if ($canSave): ?><span class="editing-hint">Edit the yellow fields, then select Done.</span><span id="raiSaveStatus" class="save-status" role="status" aria-live="polite"></span><button type="button" id="raiEditButton">Edit</button><?php endif; ?>
    <button type="button" id="raiPrintButton">Print / Save as PDF</button>
    <button type="button" onclick="window.close()">Close</button>
  </div>
</div>

<?php foreach ($renderPages as $pageIndex => $page):
    $items = isset($page['applicants']) && is_array($page['applicants']) ? array_slice($page['applicants'], 0, 10) : array();
    $count = count($items);
    $nothing = !empty($page['nothing_follows']) && $count < 10;
?>
<section class="sheet" aria-label="RAI data page <?= $pageIndex + 1 ?>">
  <?php if ($isDraft): ?><span class="draft-mark">UNSAVED DRAFT PREVIEW</span><?php endif; ?>
  <div class="form-ident">
    <div><strong>Enhanced CS Form No. 2</strong><br><span class="revision">Revised 2025</span></div>
    <div class="accredited">For Use of Accredited Agencies Only</div>
  </div>
  <div class="form-title">REPORT ON APPOINTMENTS ISSUED (RAI)</div>
  <div class="form-month">For the month of <strong><?= $esc($period) ?></strong></div>
  <div class="csc-use"><strong>For CSC RO/FO's Use:</strong><span>Date Received:</span></div>
  <div class="agency-block">
    <div class="agency-row">
      <div class="agency-label">AGENCY:</div>
      <div class="agency-value"><?= $esc($agencyLine) ?></div>
    </div>
    <div class="resolution">CSC Resolution No: <strong><?= $esc($resolutionText) ?></strong></div>
    <div class="agency-spacer" aria-hidden="true"></div>
  </div>
  <div class="instructions">
    <div class="instructions-title">INSTRUCTIONS:</div>
    <div class="instructions-lines">
      <div>(1) Fill out the data needed in the form completely and accurately.</div>
      <div>(2) Do not abbreviate entries in the form.</div>
      <div>(3) Accomplish the Checklist of Common Requirements and sign the certification.</div>
      <div>(4) Submit the duly accomplished form in electronic and printed copy (2 copies) to the CSC Field Office</div>
      <div class="indent">together with the original CSC copy of appointments and supporting documents within the 30th day of the succeeding month.</div>
    </div>
  </div>
  <div class="section-caption">Pertinent data on appointment/s issued</div>

  <table class="rai-table">
    <colgroup><?php foreach ($widths as $width): ?><col style="width:<?= number_format($width, 3, '.', '') ?>%"><?php endforeach; ?></colgroup>
    <thead>
      <tr class="group-head">
        <th rowspan="2"></th>
        <th rowspan="2"><div class="stack"><b>Date Issued/</b><span>Effectivity<br>(mm/dd/yyyy)</span></div></th>
        <th colspan="4" class="major-head">NAME OF APPOINTEE/S</th>
        <th rowspan="2"><div class="stack"><b>Sex</b><span>(M/F)</span></div></th>
        <th rowspan="2"><div class="stack"><b>PWD</b><span>(Y/N)</span></div></th>
        <th rowspan="2"><div class="stack"><b>Position<br>Level</b><span>(1st<br>/ 2nd)</span></div></th>
        <th rowspan="2"><div class="stack"><b>POSITION TITLE</b><span class="tiny">(Indicate<br>parenthetical title,<br>if applicable)</span></div></th>
        <th rowspan="2">ITEM NO.</th>
        <th rowspan="2">SALARY/<br>JOB/<br><br>PAY<br>GRADE</th>
        <th rowspan="2">SALARY RATE<br>(Monthly)</th>
        <th rowspan="2">EMPLOYMENT<br>STATUS</th>
        <th rowspan="2"><span class="tiny">PERIOD OF EMPLOYMENT<br>(For Temporary, Casual/<br>Contractual Appointments)<br>(mm/dd/yyyy to mm/dd/yyyy)</span></th>
        <th rowspan="2">NATURE OF<br>APPOINTMENT</th>
        <th colspan="2" class="major-head">PUBLICATION</th>
        <th colspan="4" class="major-head">CSC ACTION</th>
      </tr>
      <tr class="minor-head">
        <th>Last Name</th><th>First Name</th><th>Name Extension<br>(Jr./III)</th><th>Middle Name</th>
        <th><span class="tiny">DATE<br>(Indicate period of publication)<br>(mm/dd/yyyy to mm/dd/yyyy)</span></th>
        <th><span class="tiny">MODE<br>(CSC Bulletin of Vacant Positions,<br>Agency Website, Newspaper, etc.)</span></th>
        <th>Appointment<br>Identification No.</th><th>V-Validated<br>INV- Invalidated</th><th>Date of Action<br>(mm/dd/yyyy)</th><th>Date of Release<br>(mm/dd/yyyy)</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($items as $item):
        $rowIndex = max(0, (int) ($item['sequence_number'] ?? 1) - 1);
        $grade = $itemValue($item, 'salary_grade');
        $step = $itemValue($item, 'salary_step');
        $gradeStep = $grade . ($grade !== '' && $step !== '' ? '-' . $step : '');
        $employmentRange = $fmtRange($itemValue($item, 'employment_period_from'), $itemValue($item, 'employment_period_to'));
        $publicationRange = $fmtRange($itemValue($item, 'publication_from'), $itemValue($item, 'publication_to'));
        $publicationMode = 'CSC WEBSITE';
        $nameExtension = trim((string) $itemValue($item, 'name_extension'));
        if (preg_match('/^(?:N\/?A|NONE|-)$/i', $nameExtension)) $nameExtension = '';
        $employmentStatus = trim((string) preg_replace('/\s+Position$/i', '', $itemValue($item, 'employment_status')));
        $action = strtoupper($itemValue($item, 'csc_action'));
        if ($action === 'VALIDATED') $action = 'V';
        if ($action === 'INVALIDATED') $action = 'INV';
    ?>
      <tr>
        <td><?= (int) ($item['sequence_number'] ?? 0) ?></td>
        <td><?= $rowControl($rowIndex, 'date_issued', $itemValue($item, 'date_issued'), $fmtDate($itemValue($item, 'date_issued')), 'YYYY-MM-DD') ?></td>
        <td class="left"><?= $rowControl($rowIndex, 'last_name', $itemValue($item, 'last_name')) ?></td>
        <td class="left"><?= $rowControl($rowIndex, 'first_name', $itemValue($item, 'first_name')) ?></td>
        <td><?= $rowControl($rowIndex, 'name_extension', $itemValue($item, 'name_extension'), $nameExtension) ?></td>
        <td class="left"><?= $rowControl($rowIndex, 'middle_name', $itemValue($item, 'middle_name')) ?></td>
        <td><?= $rowControl($rowIndex, 'sex', $itemValue($item, 'sex')) ?></td><td><?= $rowControl($rowIndex, 'pwd', $itemValue($item, 'pwd')) ?></td>
        <td><?= $rowControl($rowIndex, 'position_level', $itemValue($item, 'position_level')) ?></td>
        <td class="left"><?= $rowControl($rowIndex, 'position_title', $itemValue($item, 'position_title')) ?></td>
        <td class="left"><?= $rowControl($rowIndex, 'item_number', $itemValue($item, 'item_number')) ?></td>
        <td><?= $rowPairControl($rowIndex, 'salary_grade', $grade, 'salary_step', $step, $gradeStep, 'SG', 'Step') ?></td><td><?= $rowControl($rowIndex, 'monthly_salary', $itemValue($item, 'monthly_salary'), $fmtMoney($itemValue($item, 'monthly_salary')), '0.00') ?></td>
        <td><?= $rowControl($rowIndex, 'employment_status', $itemValue($item, 'employment_status'), $employmentStatus) ?></td>
        <td><?= $rowPairControl($rowIndex, 'employment_period_from', $itemValue($item, 'employment_period_from'), 'employment_period_to', $itemValue($item, 'employment_period_to'), $employmentRange !== '' ? $employmentRange : 'N/A', 'YYYY-MM-DD', 'YYYY-MM-DD') ?></td>
        <td><?= $rowControl($rowIndex, 'nature_of_appointment', $itemValue($item, 'nature_of_appointment')) ?></td>
        <td><?= $rowPairControl($rowIndex, 'publication_from', $itemValue($item, 'publication_from'), 'publication_to', $itemValue($item, 'publication_to'), $publicationRange !== '' ? $publicationRange : 'N/A', 'YYYY-MM-DD', 'YYYY-MM-DD') ?></td>
        <td><?= $esc($publicationMode) ?></td>
        <td><?= $rowControl($rowIndex, 'appointment_identification_no', $itemValue($item, 'appointment_identification_no')) ?></td><td><?= $rowControl($rowIndex, 'csc_action', $itemValue($item, 'csc_action'), $action, 'V / INV') ?></td>
        <td><?= $rowControl($rowIndex, 'csc_action_date', $itemValue($item, 'csc_action_date'), $fmtDate($itemValue($item, 'csc_action_date')), 'YYYY-MM-DD') ?></td><td><?= $rowControl($rowIndex, 'csc_release_date', $itemValue($item, 'csc_release_date'), $fmtDate($itemValue($item, 'csc_release_date')), 'YYYY-MM-DD') ?></td>
      </tr>
    <?php endforeach; ?>
    <?php if ($nothing): ?><tr class="nothing"><td colspan="22">***Nothing Follows***</td></tr><?php endif; ?>
    <?php for ($i = $count + ($nothing ? 1 : 0); $i < 10; $i++): ?><tr class="blank-row"><?php for ($column = 0; $column < 22; $column++): ?><td>&nbsp;</td><?php endfor; ?></tr><?php endfor; ?>
    </tbody>
  </table>

  <div class="certifications">
    <section class="cert">
      <div class="cert-title">CERTIFICATION:</div>
      This is to certify that the information contained in this<br>
      report are true, correct, and complete based on the Plantilla<br>
      of Personnel and appointment/s issued.
      <div class="signature"><?= $headerControl('certifying_officer', $raw('certifying_officer'), '________________________________') ?></div>
      <div class="designation"><?= $headerControl('certifying_position', $raw('certifying_position'), 'HRMO') ?></div>
    </section>
    <section class="cert">
      <div class="cert-title">CERTIFICATION:</div>
      This is to certify that the appointment/s issued<br>
      is/are in accordance with existing Civil Service Law,<br>
      rules, and regulations.
      <div class="signature"><?= $raw('appointing_officer') !== '' ? $esc($raw('appointing_officer')) : '________________________________' ?></div>
      <div class="designation"><?= $raw('appointing_position') !== '' ? $esc($raw('appointing_position')) : 'Appointing Officer' ?></div>
    </section>
    <section class="post-audit"><strong>Post-Audited by:</strong><div class="line"></div><div class="designation">CSC Official</div></section>
  </div>
  <div class="csc-only-label">For CSC Use Only:</div>
  <div class="remarks">
    <div class="label">REMARKS/COMMENTS/RECOMMENDATIONS (e.g. Reasons for Invalidation):</div>
    <div class="remarks-row"><?= $headerControl('remarks', $raw('remarks'), '', true) ?></div>
    <div class="remarks-row"></div>
    <div class="remarks-row"></div>
  </div>
</section>
<?php endforeach; ?>

<section class="sheet checklist-sheet" aria-label="Checklist of Common Requirements">
  <table class="checklist-table">
    <colgroup><col style="width:4%"><col style="width:72%"><col style="width:12%"><col style="width:12%"></colgroup>
    <tr><th colspan="2" class="checklist-title">CHECKLIST OF COMMON REQUIREMENTS</th><th>HRMO</th><th>CSC FO</th></tr>
    <tr><th colspan="4" class="checklist-instructions">Instructions: Put a check if the requirements are complete. If incomplete, use the space provided to indicate the name of appointee and the lacking requirement/s.</th></tr>
    <?php foreach ($checklistItems as $index => $checklistItem): ?>
    <tr class="req-row <?= $checklistItem['code'] === 'proof_of_eligibility' ? 'proof-row' : '' ?>">
      <td class="checklist-no"><?= $index + 1 ?></td>
      <td class="checklist-requirement">
        <?= $esc($checklistItem['label']) ?>
        <span class="view-value checklist-note"><?= $checklistItem['note'] !== '' ? $esc($checklistItem['note']) : '' ?></span>
        <?php if ($canSave): ?><textarea class="edit-control checklist-input checklist-note-editor" data-check-index="<?= $index ?>" data-check-field="note" rows="2" placeholder="Appointee and lacking requirement/s"><?= $esc($checklistItem['note']) ?></textarea><?php endif; ?>
      </td>
      <td class="checklist-status"><span class="view-value"><?= $checklistItem['hrmo_status'] !== '' ? $esc($checklistItem['hrmo_status']) : '&nbsp;' ?></span><?php if ($canSave): ?><input class="edit-control checklist-input" type="text" data-check-index="<?= $index ?>" data-check-field="hrmo_status" value="<?= $esc($checklistItem['hrmo_status']) ?>" placeholder="✓ / status" autocomplete="off"><?php endif; ?></td>
      <td class="checklist-status"><span class="view-value"><?= $checklistItem['csc_status'] !== '' ? $esc($checklistItem['csc_status']) : '&nbsp;' ?></span><?php if ($canSave): ?><input class="edit-control checklist-input" type="text" data-check-index="<?= $index ?>" data-check-field="csc_status" value="<?= $esc($checklistItem['csc_status']) ?>" placeholder="✓ / status" autocomplete="off"><?php endif; ?></td>
    </tr>
    <?php endforeach; ?>
    <tr class="checklist-cert"><td colspan="2"></td><td>This is to certify that I have checked the veracity, authenticity, and completeness of all the requirements in support of the appointments attached herein.</td><td>This is to certify that I have checked all the requirements in support of the appointments attached herein and found these to be [ ] complete / [ ] lacking.</td></tr>
    <tr class="checklist-signature">
      <td colspan="2"></td>
      <td><span class="sig-line"><strong><?= $headerControl('certifying_officer', $raw('certifying_officer'), '') ?></strong></span><span class="sig-position"><?= $headerControl('certifying_position', $raw('certifying_position'), 'HRMO') ?></span></td>
      <td><span class="sig-line"><strong><span class="view-value"><?= $cscReceivingOfficer !== '' ? $esc($cscReceivingOfficer) : '&nbsp;' ?></span><?php if ($canSave): ?><input class="edit-control checklist-input" type="text" data-check-meta="csc_receiving_officer" value="<?= $esc($cscReceivingOfficer) ?>" autocomplete="off"><?php endif; ?></strong></span><span class="sig-position"><span class="view-value"><?= $esc($cscReceivingPosition) ?></span><?php if ($canSave): ?><input class="edit-control checklist-input" type="text" data-check-meta="csc_receiving_position" value="<?= $esc($cscReceivingPosition) ?>" autocomplete="off"><?php endif; ?></span></td>
    </tr>
  </table>
  <div class="page-two">Page 2 of 2</div>
</section>
<script>
(function () {
  'use strict';

  var canSave = <?= $canSave ? 'true' : 'false' ?>;
  var printButton = document.getElementById('raiPrintButton');
  var editButton = document.getElementById('raiEditButton');
  var statusNode = document.getElementById('raiSaveStatus');
  var applicants = <?= json_encode($editableApplicants, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
  var header = <?= json_encode(array(
      'certifying_officer' => $raw('certifying_officer'),
      'certifying_position' => $raw('certifying_position'),
      'remarks' => $raw('remarks')
  ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
  var checklist = <?= json_encode($editableChecklist, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
  var saveUrl = <?= json_encode(rtrim($savePreviewUrl, '/') . '/' . $reportId, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
  var raiToken = <?= json_encode($raiToken, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
  var dirty = false;
  var saving = false;
  var dateFields = ['date_issued', 'employment_period_from', 'employment_period_to', 'publication_from', 'publication_to', 'csc_action_date', 'csc_release_date'];

  function showStatus(message, type) {
    if (!statusNode) return;
    statusNode.textContent = message || '';
    statusNode.className = 'save-status' + (type ? ' ' + type : '');
  }

  function isValidDate(value) {
    if (value === '') return true;
    if (!/^\d{4}-\d{2}-\d{2}$/.test(value)) return false;
    var parts = value.split('-').map(Number);
    var test = new Date(Date.UTC(parts[0], parts[1] - 1, parts[2]));
    return test.getUTCFullYear() === parts[0] && test.getUTCMonth() === parts[1] - 1 && test.getUTCDate() === parts[2];
  }

  function validate() {
    var invalid = null;
    document.querySelectorAll('[data-row][data-field]').forEach(function (input) {
      input.classList.remove('invalid');
      var field = input.getAttribute('data-field');
      var value = input.value.trim();
      if (dateFields.indexOf(field) !== -1 && !isValidDate(value)) {
        input.classList.add('invalid');
        if (!invalid) invalid = input;
      }
      if (field === 'monthly_salary' && value !== '' && !/^\d+(?:\.\d{1,2})?$/.test(value.replace(/,/g, ''))) {
        input.classList.add('invalid');
        if (!invalid) invalid = input;
      }
    });
    if (invalid) {
      invalid.focus();
      showStatus('Use YYYY-MM-DD for dates and numbers for salary.', 'error');
      return false;
    }
    return true;
  }

  function collectPayload() {
    document.querySelectorAll('[data-row][data-field]').forEach(function (input) {
      var row = Number(input.getAttribute('data-row'));
      var field = input.getAttribute('data-field');
      if (!applicants[row]) return;
      var value = input.value.trim();
      applicants[row][field] = field === 'monthly_salary' ? value.replace(/,/g, '') : value;
    });
    applicants.forEach(function (item) { item.publication_mode = 'CSC WEBSITE'; });

    document.querySelectorAll('[data-header-field]').forEach(function (input) {
      header[input.getAttribute('data-header-field')] = input.value.trim();
    });
    document.querySelectorAll('[data-check-index][data-check-field]').forEach(function (input) {
      var index = Number(input.getAttribute('data-check-index'));
      if (!checklist.items[index]) return;
      checklist.items[index][input.getAttribute('data-check-field')] = input.value.trim();
    });
    document.querySelectorAll('[data-check-meta]').forEach(function (input) {
      checklist[input.getAttribute('data-check-meta')] = input.value.trim();
    });

    return {applicants: applicants, header: header, checklist: checklist};
  }

  function enterEditMode() {
    document.body.classList.add('rai-editing');
    editButton.textContent = 'Done';
    showStatus('Editing', '');
    var first = document.querySelector('.edit-control');
    if (first) first.focus();
  }

  function saveChanges() {
    if (saving || !validate()) return;
    saving = true;
    editButton.disabled = true;
    showStatus('Saving…', '');
    var body = new URLSearchParams({
      rai_token: raiToken,
      payload: JSON.stringify(collectPayload())
    });

    fetch(saveUrl, {
      method: 'POST',
      credentials: 'same-origin',
      headers: {'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8', 'X-Requested-With': 'XMLHttpRequest'},
      body: body.toString()
    }).then(function (response) {
      return response.text().then(function (text) {
        var data;
        try { data = JSON.parse(text); } catch (error) { data = {}; }
        if (!response.ok || data.status !== 'success') throw new Error(data.message || 'The preview changes could not be saved.');
        return data;
      });
    }).then(function (data) {
      dirty = false;
      showStatus(data.message || 'Saved.', 'success');
      window.setTimeout(function () { window.location.reload(); }, 450);
    }).catch(function (error) {
      saving = false;
      editButton.disabled = false;
      showStatus(error.message || 'The preview changes could not be saved.', 'error');
    });
  }

  if (printButton) {
    printButton.addEventListener('click', function () {
      if (document.body.classList.contains('rai-editing')) {
        showStatus('Select Done to save before printing.', 'error');
        return;
      }
      window.print();
    });
  }

  if (!canSave || !editButton) return;

  editButton.addEventListener('click', function () {
    if (document.body.classList.contains('rai-editing')) saveChanges();
    else enterEditMode();
  });

  document.addEventListener('input', function (event) {
    if (!document.body.classList.contains('rai-editing') || !event.target.matches('.edit-control, .edit-control input')) return;
    dirty = true;
    event.target.classList.remove('invalid');
    var headerField = event.target.getAttribute('data-header-field');
    if (headerField) {
      document.querySelectorAll('[data-header-field="' + headerField + '"]').forEach(function (duplicate) {
        if (duplicate !== event.target) duplicate.value = event.target.value;
      });
    }
    showStatus('Unsaved changes', '');
  });

  window.addEventListener('beforeunload', function (event) {
    if (!dirty || saving) return;
    event.preventDefault();
    event.returnValue = '';
  });
}());
</script>
</body>
</html>
