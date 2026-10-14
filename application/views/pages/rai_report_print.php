<?php
/**
 * MISDDO RAI prototype printable preview
 * Expected from RaiReport::preview(): $header, $pages, $applicantCount, $isDraft
 * Read-only view: no database access, JavaScript data parsing, or persistence.
 */
defined('BASEPATH') OR exit('No direct script access allowed');
$header = isset($header) && is_array($header) ? $header : array();
$pages  = isset($pages) && is_array($pages) ? $pages : array();
$isDraft = !empty($isDraft);
$esc = static function ($value) { return html_escape((string)($value === null ? '' : $value)); };
$h = static function ($key) use ($header, $esc) { return $esc(isset($header[$key]) && is_scalar($header[$key]) ? $header[$key] : ''); };
$monthNames = array(1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December');
$monthIndex = isset($header['report_month']) ? (int)$header['report_month'] : 0;
$period = (isset($monthNames[$monthIndex]) ? $monthNames[$monthIndex] : '__________') . ' ' . $h('report_year');
$fmtDate = static function ($value) use ($esc) {
    $value = trim((string)$value);
    if ($value === '') return '';
    $dt = DateTimeImmutable::createFromFormat('!Y-m-d', $value);
    return $dt && $dt->format('Y-m-d') === $value ? $esc($dt->format('m/d/Y')) : $esc($value);
};
$fmtRange = static function ($from, $to) use ($fmtDate) {
    $a = $fmtDate($from); $b = $fmtDate($to);
    return $a && $b ? $a . ' – ' . $b : ($a ?: $b);
};
$fmtMoney = static function ($value) use ($esc) {
    if ($value === '' || $value === null) return '';
    return is_numeric($value) && (float)$value >= 0 ? number_format((float)$value,2,'.',',') : $esc($value);
};
$labels = array(
    'No.', 'Date Issued / Effectivity', 'Last Name', 'First Name', 'Ext.', 'Middle Name',
    'Sex', 'PWD', 'Position Level', 'Position Title', 'Item Number', 'Salary / Job / Pay Grade & Step',
    'Monthly Salary Rate', 'Employment Status', 'Period of Employment', 'Nature of Appointment',
    'Date / Period', 'Mode of Publication', 'Appointment Identification Number', 'CSC Action',
    'Date of CSC Action', 'Date of Release'
);
$widths = array(1.461,4.626,7.547,6.865,2.872,5.599,1.849,1.849,3.213,4.528,13.779,2.969,4.090,5.355,4.673,4.820,3.895,3.895,3.992,3.749,4.235,4.138);
?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>RAI Preview — <?= $esc($period) ?></title>
<style>
*{box-sizing:border-box}html,body{margin:0;background:#edf1f5;color:#151515;font-family:Arial,Helvetica,sans-serif}
.toolbar{position:sticky;top:0;z-index:10;background:#193a58;color:#fff;padding:11px 20px;display:flex;align-items:center;gap:12px;flex-wrap:wrap;justify-content:space-between;font-size:13px}
.toolbar button{background:#fff;border:0;border-radius:5px;padding:9px 16px;font-weight:700;cursor:pointer;color:#173750}.toolbar .muted{color:#d4e4ed}
.preview-info{margin:12px auto;max-width:13in;font-size:12px;color:#41536a;padding:0 8px}
.sheet{width:13in;height:8.5in;margin:16px auto;background:white;box-shadow:0 4px 24px rgba(0,0,0,.14);position:relative;padding:.2in;overflow:hidden;page-break-after:always;break-after:page}
.sheet:last-of-type{page-break-after:auto;break-after:auto}
.form-header{display:flex;justify-content:space-between;align-items:start;font-size:8px;margin:0 0 5px}
.form-header .form-code{text-align:right;font-weight:700}
.form-title{font-size:15px;font-weight:800;text-align:center;text-transform:uppercase;line-height:1.15;margin:2px 0 3px;letter-spacing:.2px}
.form-subtitle{text-align:center;font-weight:700;font-size:8px;margin-bottom:5px}
.top-meta{width:100%;border-collapse:collapse;font-size:8px;table-layout:fixed;margin:2px 0 5px}
.top-meta td{padding:2px 4px;vertical-align:top}.top-meta .value{border-bottom:1px solid #555;font-weight:700}
.instruction{font-size:7.15px;line-height:1.2;margin:4px 0 6px;border:1px solid #444;padding:4px 6px}
.rai-table{width:100%;border-collapse:collapse;table-layout:fixed;font-family:'Arial Narrow',Arial,sans-serif}
.rai-table th,.rai-table td{border:1px solid #272727;text-align:center;vertical-align:middle;overflow-wrap:anywhere;word-break:normal;padding:1px 1.2px}
.rai-table thead th{font-size:6.3px;line-height:1.14;font-weight:700;height:47px}
.rai-table .group-head th{height:16px;font-size:7px;background:#f7f7f7}
.rai-table .minor-head th{height:20px;font-size:6px}
.rai-table tbody tr.applicant{height:24px}
.rai-table tbody td{font-size:6.65px;line-height:1.1;max-height:24px}
.rai-table .left{text-align:left;padding-left:2px}
.rai-table .nothing td{height:24px;text-align:center;font-size:8px;font-weight:700;letter-spacing:.1px}
.rai-table .blank-row td{height:24px}
.certifications{display:grid;grid-template-columns:1fr 1fr;column-gap:11px;margin-top:6px;font-size:7.3px;line-height:1.22}
.cert{border:1px solid #333;min-height:91px;padding:5px 6px;position:relative}
.cert .cert-title{font-weight:700;text-align:center;text-transform:uppercase;font-size:8px;margin-bottom:4px}
.cert .signature{margin-top:21px;text-align:center;font-size:7.3px;font-weight:700}
.cert .designation{text-align:center;font-size:6.6px}
.post-audit{margin-top:7px;border:1px solid #333;display:grid;grid-template-columns:1fr 1fr 1fr;font-size:7px;min-height:37px}
.post-audit>div{padding:5px;border-right:1px solid #333}.post-audit>div:last-child{border-right:0}
.remarks{border:1px solid #333;border-top:0;min-height:42px;padding:4px 6px;font-size:7px;white-space:pre-wrap;overflow-wrap:anywhere}
.remarks strong{display:block;text-align:center;margin-bottom:3px}
.sheet-foot{position:absolute;bottom:.12in;left:.2in;right:.2in;display:flex;justify-content:space-between;font-size:7px;color:#555}
.draft-mark{position:absolute;top:4px;right:50%;transform:translateX(50%);opacity:.6;font-size:8px;letter-spacing:1px}
.no-records{padding:22px;text-align:center;border:1px solid #ccc;font-weight:600}
@page{size:13in 8.5in;margin:0}
@media print{html,body{background:white!important;-webkit-print-color-adjust:exact;print-color-adjust:exact}.toolbar,.preview-info{display:none!important}.sheet{margin:0!important;box-shadow:none!important;width:13in;height:8.5in;padding:.2in;page-break-after:always;break-after:page}.sheet:last-of-type{page-break-after:auto;break-after:auto}}
@media screen and (max-width:1300px){.sheet{transform-origin:top left}.preview-info{max-width:100%}}
</style></head><body>
<div class="toolbar"><div><strong>RAI — Print Preview</strong> <span class="muted">| <?= $esc($period) ?> · <?= (int)($applicantCount ?? 0) ?> applicant(s) · <?= count($pages) ?> page(s)</span></div><div><span class="muted">Folio 13 × 8.5 in · Landscape</span> &nbsp; <button type="button" onclick="window.print()">Print / Save as PDF</button> <button type="button" onclick="window.close()">Close</button></div></div>
<div class="preview-info">This is a temporary, unsaved preview. Check print settings: Folio 8.5 × 13 in, landscape, 100% scale, no browser headers/footers. Confirm certification wording against your official CSC template before submission.</div>
<?php if (empty($pages)): ?><section class="sheet"><h2 class="form-title">Report on Appointments Issued (RAI)</h2><div class="no-records">No applicants selected. Return to the report builder to add applicants.</div></section><?php endif; ?>
<?php foreach ($pages as $pageIndex => $page):
    $items = isset($page['applicants']) && is_array($page['applicants']) ? array_slice($page['applicants'],0,10) : array();
    $count = count($items);
    $nothing = !empty($page['nothing_follows']) && $count < 10;
    $classification = isset($page['classification']) ? (string)$page['classification'] : '';
    $classificationLabel = $classification;
    foreach ($items as $row) { if (!empty($row['classification_label'])) {$classificationLabel=(string)$row['classification_label'];break;} }
    $classificationLabel = trim(str_replace('_',' ', $classificationLabel));
    $line = static function ($item,$key) use ($esc) { return $esc(isset($item[$key]) && is_scalar($item[$key]) ? $item[$key] : ''); };
?>
<section class="sheet" aria-label="RAI page <?= $pageIndex+1 ?>">
  <?php if ($isDraft): ?><span class="draft-mark">UNSAVED DRAFT PREVIEW</span><?php endif; ?>
  <div class="form-header"><div>Republic of the Philippines<br><strong>CIVIL SERVICE COMMISSION</strong></div><div class="form-code">Enhanced CS Form No. 2<br>Revised 2025</div></div>
  <div class="form-title">Report on Appointments Issued (RAI)</div>
  <div class="form-subtitle">For the Month of <?= $esc($period) ?></div>
  <table class="top-meta"><tr><td width="12%">Agency:</td><td class="value" width="52%"> <?= $h('agency_name') ?> <?= $h('division_name') !== '' ? ' — ' . $h('division_name') : '' ?></td><td width="18%">CSC Resolution No.:</td><td class="value"><?= $h('csc_resolution_no') ?></td></tr><tr><td>Classification:</td><td class="value"><?= $esc(ucwords($classificationLabel)) ?></td><td>Resolution Date:</td><td class="value"><?= $fmtDate($header['csc_resolution_date'] ?? '') ?></td></tr></table>
  <div class="instruction"><strong>Instructions:</strong> Enter issued appointments for the reporting period using the prescribed CSC format. Dates, salary, appointment details, and CSC action entries must be verified against official records. Blank CSC-use fields must remain blank until official action is recorded.</div>
  <table class="rai-table"><colgroup><?php foreach($widths as $width): ?><col style="width:<?= number_format($width,3,'.','') ?>% "><?php endforeach; ?></colgroup>
  <thead><tr class="group-head"><th rowspan="2">No.</th><th rowspan="2">Date Issued / Effectivity</th><th colspan="4">Name of Appointee</th><th rowspan="2">Sex</th><th rowspan="2">PWD</th><th rowspan="2">Position Level</th><th rowspan="2">Position Title</th><th rowspan="2">Item Number</th><th rowspan="2">Salary / Job / Pay Grade &amp; Step</th><th rowspan="2">Monthly Salary Rate</th><th rowspan="2">Employment Status</th><th rowspan="2">Period of Employment</th><th rowspan="2">Nature of Appointment</th><th colspan="2">Publication</th><th colspan="4">For CSC Use</th></tr>
  <tr class="minor-head"><th>Last Name</th><th>First Name</th><th>Ext.</th><th>Middle Name</th><th>Date / Period</th><th>Mode</th><th>Appointment ID No.</th><th>CSC Action</th><th>Date of CSC Action</th><th>Date of Release</th></tr></thead>
  <tbody>
  <?php foreach ($items as $item):
  $grade=trim((string)($item['salary_grade']??'')); $step=trim((string)($item['salary_step']??''));
  if($step !== '') $grade .= ($grade!==''?' / ':'') . 'Step ' . $step;
  $action = (string)($item['csc_action']??''); if($action==='V') $action='Validated'; elseif($action==='INV') $action='Invalidated';
  ?>
  <tr class="applicant"><td><?= (int)($item['sequence_number'] ?? 0) ?></td><td><?= $fmtDate($item['date_issued']??'') ?></td><td class="left"><?= $line($item,'last_name') ?></td><td class="left"><?= $line($item,'first_name') ?></td><td><?= $line($item,'name_extension') ?></td><td class="left"><?= $line($item,'middle_name') ?></td><td><?= $line($item,'sex') ?></td><td><?= $line($item,'pwd') ?></td><td><?= $line($item,'position_level') ?></td><td class="left"><?= $line($item,'position_title') ?></td><td class="left"><?= $line($item,'item_number') ?></td><td><?= $esc($grade) ?></td><td><?= $fmtMoney($item['monthly_salary']??'') ?></td><td><?= $line($item,'employment_status') ?></td><td><?= $fmtRange($item['employment_period_from']??'', $item['employment_period_to']??'') ?></td><td><?= $line($item,'nature_of_appointment') ?></td><td><?= $fmtRange($item['publication_from']??'', $item['publication_to']??'') ?></td><td><?= $line($item,'publication_mode') ?></td><td><?= $line($item,'appointment_identification_no') ?></td><td><?= $esc($action) ?></td><td><?= $fmtDate($item['csc_action_date']??'') ?></td><td><?= $fmtDate($item['csc_release_date']??'') ?></td></tr>
  <?php endforeach; ?>
  <?php if($nothing): ?><tr class="nothing"><td colspan="22">***Nothing Follows***</td></tr><?php endif; ?>
  <?php for($i=$count+($nothing?1:0);$i<10;$i++): ?><tr class="blank-row"><?php for($c=0;$c<22;$c++): ?><td>&nbsp;</td><?php endfor; ?></tr><?php endfor; ?>
  </tbody></table>
  <div class="certifications">
    <section class="cert"><div class="cert-title">Certification</div>I certify that the information entered in this report is based on the available appointment records and has been checked for accuracy.<div class="signature"><?= $h('certifying_officer') ?: '________________________________' ?></div><div class="designation"><?= $h('certifying_position') ?: 'Certifying Officer / Position' ?></div></section>
    <section class="cert"><div class="cert-title">Certification</div>I certify that the appointments listed above have been issued in accordance with applicable rules and are submitted for the appropriate CSC action.<div class="signature"><?= $h('appointing_officer') ?: '________________________________' ?></div><div class="designation"><?= $h('appointing_position') ?: 'Appointing Officer / Position' ?></div></section>
  </div>
  <div class="post-audit"><div><strong>CSC Post-Audit / Evaluated by:</strong><br><br>____________________________</div><div><strong>Date Received / Evaluated:</strong><br><br>____________________________</div><div><strong>CSC Official / Action:</strong><br><br>____________________________</div></div>
  <div class="remarks"><strong>CSC REMARKS / COMMENTS / RECOMMENDATIONS</strong><?= $h('remarks') ?></div>
  <div class="sheet-foot"><span>Enhanced CS Form No. 2 • Revised 2025</span><span>Page <?= $pageIndex+1 ?> of <?= count($pages) ?></span></div>
</section>
<?php endforeach; ?>
</body></html>
