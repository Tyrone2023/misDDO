<?php
/* ─────────────────────────────────────────────────────────────
   applicant_eval_report_view.php
   Drop this into your CodeIgniter view. It relies on the parent
   layout to supply the sidebar, top-navbar, Bootstrap, jQuery,
   and Font Awesome that are already present in your MIS.
   ───────────────────────────────────────────────────────────── */

if (!function_exists('h')) {
    function h($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
}
if (!function_exists('applicant_eval_score_valid')) {
    function applicant_eval_score_valid($v) {
        return $v !== null && $v !== '' && is_numeric($v)
            && (float)$v != 0.00001 && (float)$v != 0.0001;
    }
}
if (!function_exists('applicant_eval_score_value')) {
    function applicant_eval_score_value($v) {
        if (!applicant_eval_score_valid($v)) return '';
        return rtrim(rtrim(number_format((float)$v, 4, '.', ''), '0'), '.');
    }
}
if (!function_exists('applicant_eval_name')) {
    function applicant_eval_name($row) {
        $name = trim(($row->LastName ?? '') . ', ' . ($row->FirstName ?? '') . ' ' . ($row->MiddleName ?? ''));
        $name = trim($name, ', ');
        if (!empty($row->NameExtn)) $name .= ' ' . $row->NameExtn;
        return $name;
    }
}
if (!function_exists('applicant_eval_address')) {
    function applicant_eval_address($row) {
        $parts = [];
        foreach (['resHouseNo','resStreet','resVillage','resBarangay','resCity','resProvince','resZipCode'] as $f) {
            $v = trim((string)($row->$f ?? ''));
            if ($v !== '') $parts[] = $v;
        }
        return implode(', ', array_unique($parts));
    }
}
if (!function_exists('applicant_eval_track_strand')) {
    function applicant_eval_track_strand($row) {
        $parts = [];
        foreach (['track','shss','jhss','specialization'] as $f) {
            $v = trim((string)($row->$f ?? ''));
            if ($v !== '' && !in_array($v, $parts, true)) $parts[] = $v;
        }
        return implode(' / ', $parts);
    }
}

$jobTypeSuffixes = [
    1=>'Elementary', 2=>'Secondary', 3=>'Junior High School',
    4=>'Senior High School', 5=>'Kindergarten',
    6=>'IPED Elementary', 7=>'IPED Secondary',
    8=>'IPED Junior High School', 9=>'IPED Senior High School',
    10=>'SNED',
];

$jtLabels   = $jobTypeLabels ?? [];
$jobsByType = $jobsByType    ?? [];
$hasOpenJobs = $hasOpenJobs  ?? false;
$applicants  = $applicants   ?? [];
$submitted   = $submitted    ?? false;
$filterError = $filterError  ?? '';
$selectedFilter = $selectedFilter ?? '';
$selectedYear   = $selectedYear   ?? '';
$years          = $years          ?? [];
?>

<!-- ── Scripts (CDN) ───────────────────────────────────────── -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>

<!-- ── Scoped styles ──────────────────────────────────────── -->
<style>
/* Variables scoped to this page only */
.aer-wrap {
  --aer-accent:      #2563eb;
  --aer-accent-lt:   #dbeafe;
  --aer-accent-mid:  #93c5fd;
  --aer-navy:        #0f1f3d;
  --aer-gold:        #f59e0b;
  --aer-gold-lt:     #fef3c7;
  --aer-surface:     #ffffff;
  --aer-surface-alt: #f8fafc;
  --aer-surface-hov: #f1f5f9;
  --aer-border:      #e2e8f0;
  --aer-border-str:  #cbd5e1;
  --aer-text:        #0f172a;
  --aer-text-2:      #475569;
  --aer-text-3:      #94a3b8;
  --aer-success:     #059669;
  --aer-success-bg:  #d1fae5;
  --aer-warn:        #d97706;
  --aer-warn-bg:     #fef3c7;
  --aer-danger:      #dc2626;
  --aer-danger-bg:   #fee2e2;
  --aer-r:           6px;
  --aer-rl:          12px;
  --aer-sh:          0 2px 8px rgba(0,0,0,.07),0 1px 3px rgba(0,0,0,.04);
}

/* Page title row */
.aer-page-title {
  margin-bottom: 20px;
}
.aer-page-title h4 {
  font-size: 20px;
  font-weight: 700;
  color: var(--aer-navy);
  margin: 0;
}
.aer-page-title small {
  font-size: 12px;
  color: var(--aer-text-3);
}

/* Alerts */
.aer-alert {
  padding: 12px 16px;
  border-radius: var(--aer-r);
  font-size: 13px;
  display: flex;
  align-items: flex-start;
  gap: 8px;
  margin-bottom: 18px;
}
.aer-alert-danger  { background: var(--aer-danger-bg);  color: #7f1d1d; border: 1px solid #fca5a5; }
.aer-alert-info    { background: var(--aer-accent-lt);  color: #1e40af; border: 1px solid var(--aer-accent-mid); }

/* Cards */
.aer-card {
  background: var(--aer-surface);
  border-radius: var(--aer-rl);
  box-shadow: var(--aer-sh);
  margin-bottom: 20px;
  overflow: hidden;
}
.aer-card-head {
  padding: 13px 20px;
  border-bottom: 1px solid var(--aer-border);
  background: var(--aer-surface-alt);
  display: flex;
  align-items: center;
  gap: 10px;
}
.aer-card-head-icon {
  width: 28px; height: 28px;
  background: var(--aer-accent-lt);
  border-radius: var(--aer-r);
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
}
.aer-card-head-icon svg { width: 15px; height: 15px; color: var(--aer-accent); }
.aer-card-head-label {
  font-size: 12px;
  font-weight: 700;
  color: var(--aer-text-2);
  text-transform: uppercase;
  letter-spacing: .06em;
}
.aer-card-body { padding: 18px 20px; }

/* Filter grid */
.aer-filter-grid {
  display: grid;
  grid-template-columns: 2fr 1fr auto;
  gap: 14px;
  align-items: end;
}
@media (max-width: 768px) {
  .aer-filter-grid { grid-template-columns: 1fr; }
}
.aer-label {
  display: block;
  font-size: 11px;
  font-weight: 700;
  color: var(--aer-text-2);
  text-transform: uppercase;
  letter-spacing: .05em;
  margin-bottom: 6px;
}

/* Form controls — override Bootstrap minimally */
.aer-wrap .aer-select {
  width: 100%;
  padding: 8px 34px 8px 12px;
  font-size: 13px;
  color: var(--aer-text);
  background: var(--aer-surface);
  border: 1.5px solid var(--aer-border-str);
  border-radius: var(--aer-r);
  outline: none;
  appearance: none;
  -webkit-appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='11' height='11' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 10px center;
  transition: border-color .15s, box-shadow .15s;
  height: 38px;
}
.aer-wrap .aer-select:focus {
  border-color: var(--aer-accent);
  box-shadow: 0 0 0 3px rgba(37,99,235,.1);
}

/* Buttons */
.aer-btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 8px 16px;
  font-size: 13px;
  font-weight: 600;
  border: none;
  border-radius: var(--aer-r);
  cursor: pointer;
  transition: all .15s;
  white-space: nowrap;
  height: 38px;
  letter-spacing: .01em;
  text-decoration: none;
}
.aer-btn svg { width: 14px; height: 14px; flex-shrink: 0; }
.aer-btn-primary { background: var(--aer-accent); color: #fff; }
.aer-btn-primary:hover { background: #1d4ed8; transform: translateY(-1px); box-shadow: 0 4px 10px rgba(37,99,235,.35); }
.aer-btn-success { background: var(--aer-success); color: #fff; }
.aer-btn-success:hover { background: #047857; transform: translateY(-1px); }
.aer-btn-warn { background: var(--aer-warn); color: #fff; }
.aer-btn-warn:hover { background: #b45309; transform: translateY(-1px); }
.aer-btn:active { transform: translateY(0) !important; }

/* Stats bar */
.aer-stats {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(155px, 1fr));
  gap: 12px;
  margin-bottom: 20px;
}
.aer-stat {
  background: var(--aer-surface);
  border-radius: var(--aer-rl);
  padding: 14px 16px;
  box-shadow: var(--aer-sh);
  display: flex;
  align-items: center;
  gap: 13px;
  border-left: 3px solid transparent;
}
.aer-stat.c-blue  { border-left-color: var(--aer-accent); }
.aer-stat.c-green { border-left-color: var(--aer-success); }
.aer-stat.c-gold  { border-left-color: var(--aer-gold); }
.aer-stat.c-navy  { border-left-color: var(--aer-navy); }
.aer-stat-icon {
  width: 36px; height: 36px;
  border-radius: var(--aer-r);
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
}
.aer-stat-icon svg { width: 18px; height: 18px; }
.aer-stat-icon.c-blue  { background: var(--aer-accent-lt); color: var(--aer-accent); }
.aer-stat-icon.c-green { background: var(--aer-success-bg); color: var(--aer-success); }
.aer-stat-icon.c-gold  { background: var(--aer-gold-lt); color: var(--aer-warn); }
.aer-stat-icon.c-navy  { background: #dbeafe; color: var(--aer-navy); }
.aer-stat-val {
  font-size: 22px; font-weight: 700; line-height: 1;
  color: var(--aer-text); font-family: 'Courier New', monospace;
}
.aer-stat-lbl {
  font-size: 10px; font-weight: 700; color: var(--aer-text-3);
  text-transform: uppercase; letter-spacing: .06em; margin-top: 3px;
}

/* Report card header */
.aer-report-head {
  padding: 13px 20px;
  border-bottom: 1px solid var(--aer-border);
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 10px;
  background: var(--aer-surface-alt);
}
.aer-report-title { font-size: 14px; font-weight: 700; color: var(--aer-text); }
.aer-report-sub { font-size: 11px; color: var(--aer-text-3); margin-top: 1px; }
.aer-export-row { display: flex; gap: 8px; flex-wrap: wrap; }

/* Scroll hint */
.aer-scroll-hint {
  display: none;
  align-items: center;
  gap: 6px;
  font-size: 11px;
  color: var(--aer-text-3);
  padding: 7px 20px;
  border-bottom: 1px solid var(--aer-border);
  background: var(--aer-gold-lt);
  color: #78350f;
}
.aer-scroll-hint svg { width: 13px; height: 13px; flex-shrink: 0; }
@media (max-width: 1200px) { .aer-scroll-hint { display: flex; } }

/* Search bar */
.aer-search-row {
  padding: 12px 20px;
  border-bottom: 1px solid var(--aer-border);
  display: flex;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
}
.aer-search-wrap { position: relative; flex: 1; min-width: 200px; max-width: 380px; }
.aer-search-wrap svg {
  position: absolute; left: 11px; top: 50%; transform: translateY(-50%);
  width: 14px; height: 14px; color: var(--aer-text-3); pointer-events: none;
}
.aer-search-input {
  width: 100%;
  padding: 7px 12px 7px 34px;
  font-size: 13px;
  border: 1.5px solid var(--aer-border-str);
  border-radius: var(--aer-r);
  outline: none;
  background: var(--aer-surface);
  color: var(--aer-text);
  transition: border-color .15s, box-shadow .15s;
  height: 36px;
}
.aer-search-input:focus { border-color: var(--aer-accent); box-shadow: 0 0 0 3px rgba(37,99,235,.1); }
.aer-count-text { font-size: 12px; color: var(--aer-text-3); white-space: nowrap; }
.aer-count-text strong { color: var(--aer-text); font-weight: 700; }

/* Table */
.aer-table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }
.aer-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 13px;
  min-width: 1080px;
}
.aer-table thead th {
  background: var(--aer-navy);
  color: rgba(255,255,255,.92);
  padding: 10px 13px;
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: .07em;
  white-space: nowrap;
  border: none;
  cursor: pointer;
  user-select: none;
  transition: background .12s;
  position: sticky;
  top: 0;
  z-index: 2;
}
.aer-table thead th:hover { background: #1a3260; }
.aer-table thead th.tr { text-align: right; }
.aer-table thead th.tc { text-align: center; }
.aer-sort { display: inline-block; margin-left: 4px; opacity: .4; font-size: 10px; }
.aer-sort.on { opacity: 1; color: var(--aer-gold); }
.aer-table tbody tr { border-bottom: 1px solid var(--aer-border); transition: background .1s; }
.aer-table tbody tr:last-child { border-bottom: none; }
.aer-table tbody tr:hover { background: var(--aer-surface-hov); }
.aer-table tbody tr:nth-child(even) { background: #fbfcfd; }
.aer-table tbody tr:nth-child(even):hover { background: var(--aer-surface-hov); }
.aer-table td { padding: 10px 13px; vertical-align: top; color: var(--aer-text); }
.aer-table td.tr { text-align: right; }
.aer-table td.tc { text-align: center; }

/* Cell helpers */
.aer-name  { font-weight: 700; color: var(--aer-navy); font-size: 13px; line-height: 1.4; }
.aer-recno { font-size: 11px; color: var(--aer-text-3); margin-top: 2px; font-family: monospace; }
.aer-pos-title { font-size: 13px; font-weight: 600; color: var(--aer-text); }
.aer-badge {
  display: inline-block;
  font-size: 11px; font-weight: 600;
  padding: 2px 9px; border-radius: 20px;
  background: var(--aer-accent-lt);
  color: #1e40af;
  white-space: nowrap;
  margin-top: 3px;
}
.aer-addr { font-size: 12px; color: var(--aer-text-2); max-width: 210px; }
.aer-track { font-size: 12px; color: var(--aer-text-2); max-width: 170px; }

/* Score chip */
.aer-chip {
  display: inline-block;
  font-family: 'Courier New', monospace;
  font-size: 12px; font-weight: 600;
  padding: 3px 8px;
  border-radius: 5px;
  background: var(--aer-surface-alt);
  color: var(--aer-text);
  border: 1px solid var(--aer-border);
}
.aer-chip.hi { background: var(--aer-success-bg); border-color: #6ee7b7; color: #064e3b; }
.aer-chip.md { background: var(--aer-gold-lt);    border-color: #fcd34d; color: #78350f; }
.aer-chip.lo { background: var(--aer-danger-bg);  border-color: #fca5a5; color: #7f1d1d; }
.aer-chip.em { background: transparent; border-color: transparent; color: var(--aer-text-3); font-family: sans-serif; }

/* NC View button */
.aer-nc-btn {
  display: inline-flex; align-items: center; gap: 5px;
  font-size: 11px; font-weight: 700;
  padding: 3px 10px; border-radius: 5px;
  background: var(--aer-accent-lt); color: var(--aer-accent);
  border: 1px solid var(--aer-accent-mid);
  cursor: pointer; text-decoration: none;
  transition: all .12s;
}
.aer-nc-btn:hover { background: var(--aer-accent); color: #fff; border-color: var(--aer-accent); }
.aer-nc-btn svg { width: 12px; height: 12px; }

/* Empty state */
.aer-empty {
  padding: 56px 20px;
  text-align: center;
}
.aer-empty-icon {
  width: 58px; height: 58px;
  background: var(--aer-surface-alt);
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  margin: 0 auto 14px;
}
.aer-empty-icon svg { width: 28px; height: 28px; color: var(--aer-text-3); }
.aer-empty h6 { font-size: 15px; font-weight: 700; color: var(--aer-text); margin-bottom: 4px; }
.aer-empty p  { font-size: 13px; color: var(--aer-text-3); margin: 0; }

/* Table footer */
.aer-table-foot {
  padding: 12px 20px;
  border-top: 1px solid var(--aer-border);
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 10px;
  background: var(--aer-surface-alt);
}
.aer-pp-wrap { display: flex; align-items: center; gap: 8px; font-size: 12px; color: var(--aer-text-2); }
.aer-pp-select {
  padding: 4px 26px 4px 8px;
  font-size: 12px;
  border: 1.5px solid var(--aer-border-str);
  border-radius: var(--aer-r);
  outline: none;
  background: var(--aer-surface);
  appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 7px center;
  color: var(--aer-text);
}
.aer-pager { display: flex; gap: 4px; align-items: center; }
.aer-pgbtn {
  width: 30px; height: 30px;
  border-radius: var(--aer-r);
  border: 1.5px solid var(--aer-border-str);
  background: var(--aer-surface);
  color: var(--aer-text-2);
  font-size: 12px; font-weight: 700;
  cursor: pointer;
  display: flex; align-items: center; justify-content: center;
  transition: all .12s;
  font-family: inherit;
}
.aer-pgbtn:hover:not(:disabled) { background: var(--aer-accent-lt); border-color: var(--aer-accent-mid); color: var(--aer-accent); }
.aer-pgbtn.on { background: var(--aer-accent); border-color: var(--aer-accent); color: #fff; }
.aer-pgbtn:disabled { opacity: .35; cursor: not-allowed; }

/* Print */
@media print {
  .aer-filter-wrap, .aer-stats, .aer-report-head .aer-export-row,
  .aer-scroll-hint, .aer-search-row, .aer-table-foot { display: none !important; }
  .aer-card { box-shadow: none !important; border-radius: 0 !important; }
  .aer-table { min-width: unset !important; font-size: 10px !important; }
  .aer-table thead th { background: #0f1f3d !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
}
</style>

<!-- ── View ───────────────────────────────────────────────── -->
<div class="content-page">
<div class="content">
<div class="container-fluid">
<div class="aer-wrap">

  <!-- Page title -->
  <div class="aer-page-title">
    <h4><?= h($title ?? 'Applicant Evaluation Report') ?></h4>
    <!-- <small>Schools Division Office · Recruitment Module</small> -->
  </div>

  <!-- Errors -->
  <?php if (!empty($filterError)) : ?>
    <div class="aer-alert aer-alert-danger">
      <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:15px;height:15px;flex-shrink:0;margin-top:1px"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      <?= h($filterError) ?>
    </div>
  <?php endif; ?>

  <?php if (!$hasOpenJobs) : ?>
    <div class="aer-alert aer-alert-info">
      <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:15px;height:15px;flex-shrink:0;margin-top:1px"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      No open positions found.
    </div>
  <?php endif; ?>

  <!-- Filter card -->
  <div class="aer-card aer-filter-wrap">
    <div class="aer-card-head">
      <div class="aer-card-head-icon">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
      </div>
      <span class="aer-card-head-label">Report Filter</span>
    </div>
    <div class="aer-card-body">
      <form method="get" action="<?= base_url('Pages/secretariat_applicant_evaluation_report') ?>">
        <div class="aer-filter-grid">

          <div>
            <label class="aer-label" for="aer-filter">Job Title / Job Type</label>
            <select name="filter" id="aer-filter" class="aer-select" <?= !$hasOpenJobs ? 'disabled' : '' ?>>
              <option value="">Select open job title or job type</option>
              <?php foreach ($jobsByType as $jobType => $jobs) : ?>
                <?php foreach ($jobs as $job) : ?>
                  <?php
                    $val    = 'job:' . (int)$job->jobID;
                    $suffix = $jobTypeSuffixes[(int)$job->job_type] ?? ('Job Type ' . (int)$job->job_type);
                  ?>
                  <option value="<?= h($val) ?>" <?= $selectedFilter === $val ? 'selected' : '' ?>>
                    <?= h($job->jobTitle ?? '') ?> - <?= h($suffix) ?>
                  </option>
                <?php endforeach; ?>
              <?php endforeach; ?>
            </select>
          </div>

          <div>
            <label class="aer-label" for="aer-year">Year</label>
            <select name="year" id="aer-year" class="aer-select" <?= !$hasOpenJobs ? 'disabled' : '' ?>>
              <option value="">Current Year</option>
              <?php foreach ($years as $yr) : ?>
                <option value="<?= h($yr->app_year) ?>" <?= $selectedYear === (string)$yr->app_year ? 'selected' : '' ?>>
                  <?= h($yr->app_year) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div>
            <label class="aer-label">&nbsp;</label>
            <button type="submit" class="aer-btn aer-btn-primary" <?= !$hasOpenJobs ? 'disabled' : '' ?> style="width:100%">
              <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
              Load Report
            </button>
          </div>

        </div>
      </form>
    </div>
  </div>

  <?php if ($submitted && empty($filterError)) : ?>

    <?php
      /* ── Build JS-safe data from PHP ── */
      $jsRows = [];
      foreach ($applicants as $row) :
        $suffix   = $jobTypeSuffixes[(int)($row->job_type ?? 0)] ?? ('Job Type ' . (int)($row->job_type ?? 0));
        $certFile = trim((string)($row->national_certificate_file   ?? ''));
        $certCol  = trim((string)($row->national_certificate_column ?? ''));
        $profId   = (int)($row->applicant_profile_id ?? 0);
        $hasNC    = ($certFile !== '' && $certCol !== '' && $profId > 0);
        $ncUrl    = $hasNC ? base_url('Pages/pdf/' . $profId . '/' . $certCol . '/?label=National%20Certificates') : '';

        $jsRows[] = [
          'name'       => applicant_eval_name($row),
          'record'     => (string)($row->record_no ?? ''),
          'address'    => applicant_eval_address($row),
          'posTitle'   => (string)($row->jobTitle ?? ''),
          'posBadge'   => $suffix,
          'track'      => applicant_eval_track_strand($row),
          'hasNC'      => $hasNC,
          'ncUrl'      => $ncUrl,
          'education'  => applicant_eval_score_value($row->education  ?? null),
          'training'   => applicant_eval_score_value($row->training   ?? null),
          'experience' => applicant_eval_score_value($row->experience ?? null),
          'let'        => applicant_eval_score_value($row->let_rating ?? null),
          'demo'       => applicant_eval_score_value($row->demo_rating ?? null),
          'reflection' => applicant_eval_score_value($row->tr_rating  ?? null),
        ];
      endforeach;
    ?>

    <!-- Stats bar
    <div class="aer-stats" id="aer-stats">
      <div class="aer-stat c-blue">
        <div class="aer-stat-icon c-blue">
          <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        </div>
        <div><div class="aer-stat-val" id="aer-s-total">0</div><div class="aer-stat-lbl">Total Applicants</div></div>
      </div>
      <div class="aer-stat c-green">
        <div class="aer-stat-icon c-green">
          <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div><div class="aer-stat-val" id="aer-s-demo">0</div><div class="aer-stat-lbl">With Demo Rating</div></div>
      </div>
      <div class="aer-stat c-gold">
        <div class="aer-stat-icon c-gold">
          <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
        </div>
        <div><div class="aer-stat-val" id="aer-s-let">—</div><div class="aer-stat-lbl">Avg LET Rating</div></div>
      </div>
      <div class="aer-stat c-navy">
        <div class="aer-stat-icon c-navy">
          <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        </div>
        <div><div class="aer-stat-val" id="aer-s-nc">0</div><div class="aer-stat-lbl">With National Cert.</div></div>
      </div>
    </div> -->

    <!-- Report card -->
    <div class="aer-card">

      <div class="aer-report-head">
        <div>
          <div class="aer-report-title">Applicant Evaluation Report</div>
          <div class="aer-report-sub" id="aer-sub">Loading…</div>
        </div>
        <div class="aer-export-row">
          <button class="aer-btn aer-btn-success" onclick="aerExportExcel()">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
            Export Excel
          </button>
          <!-- <button class="aer-btn aer-btn-warn" onclick="aerExportPDF()">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
            Export PDF
          </button> -->
        </div>
      </div>

      <div class="aer-scroll-hint">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4"/></svg>
        Scroll left / right to see all columns
      </div>

      <div class="aer-search-row">
        <div class="aer-search-wrap">
          <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35"/></svg>
          <input type="text" id="aer-search" class="aer-search-input" placeholder="Search by name, address, position…">
        </div>
        <span class="aer-count-text" id="aer-count">Showing <strong>0</strong> of <strong>0</strong> applicants</span>
      </div>

      <div class="aer-table-wrap">
        <table class="aer-table" id="aer-table">
          <thead>
            <tr>
              <th onclick="aerSort(0)" style="min-width:175px">Name <span class="aer-sort" id="aer-si-0">↕</span></th>
              <th onclick="aerSort(1)" style="min-width:195px">Complete Address <span class="aer-sort" id="aer-si-1">↕</span></th>
              <th onclick="aerSort(2)" style="min-width:195px">Job Title / Type <span class="aer-sort" id="aer-si-2">↕</span></th>
              <th onclick="aerSort(3)" style="min-width:155px">Applied Track / Strand <span class="aer-sort" id="aer-si-3">↕</span></th>
              <th onclick="aerSort(4)" class="tc" style="min-width:105px">National Cert. <span class="aer-sort" id="aer-si-4">↕</span></th>
              <th onclick="aerSort(5)" class="tr" style="min-width:85px">Education <span class="aer-sort" id="aer-si-5">↕</span></th>
              <th onclick="aerSort(6)" class="tr" style="min-width:85px">Trainings <span class="aer-sort" id="aer-si-6">↕</span></th>
              <th onclick="aerSort(7)" class="tr" style="min-width:90px">Experience <span class="aer-sort" id="aer-si-7">↕</span></th>
              <th onclick="aerSort(8)" class="tr" style="min-width:90px">LET Rating <span class="aer-sort" id="aer-si-8">↕</span></th>
              <th onclick="aerSort(9)" class="tr" style="min-width:75px">Demo <span class="aer-sort" id="aer-si-9">↕</span></th>
              <th onclick="aerSort(10)" class="tr" style="min-width:120px">Teacher Reflection <span class="aer-sort" id="aer-si-10">↕</span></th>
            </tr>
          </thead>
          <tbody id="aer-tbody"></tbody>
        </table>
      </div>

      <div class="aer-table-foot">
        <div class="aer-pp-wrap">
          Rows per page:
          <select class="aer-pp-select" id="aer-pp" onchange="aerChangePP()">
            <option value="10">10</option>
            <option value="25" selected>25</option>
            <option value="50">50</option>
            <option value="100">100</option>
            <option value="-1">All</option>
          </select>
        </div>
        <div class="aer-pager" id="aer-pager"></div>
      </div>

    </div><!-- /.aer-card -->

  <?php endif; ?>

</div><!-- /.aer-wrap -->
</div></div></div><!-- content-page -->

<?php if ($submitted && empty($filterError)) : ?>
<script>
(function () {
  'use strict';

  /* ── Raw data from PHP ── */
  const RAW = <?= json_encode($jsRows, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP) ?>;
  const SELECTED_FILTER = <?= json_encode($selectedFilter) ?>;
  const SELECTED_YEAR   = <?= json_encode($selectedYear ?: 'Current Year') ?>;

  let filtered = [...RAW];
  let sortCol = -1, sortAsc = true;
  let page = 1, pp = 25;

  /* ── Score chip ── */
  function chip(v) {
    if (v === '') return '<span class="aer-chip em">—</span>';
    const n = parseFloat(v);
    const cls = n >= 90 ? 'hi' : n >= 80 ? 'md' : 'lo';
    return `<span class="aer-chip ${cls}">${v}</span>`;
  }

  /* ── Render rows ── */
  function render() {
    const tbody = document.getElementById('aer-tbody');
    const start = pp === -1 ? 0 : (page - 1) * pp;
    const end   = pp === -1 ? filtered.length : start + pp;
    const slice = filtered.slice(start, end);

    if (!slice.length) {
      tbody.innerHTML = `<tr><td colspan="11"><div class="aer-empty">
        <div class="aer-empty-icon"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" style="width:28px;height:28px"><path stroke-linecap="round" stroke-linejoin="round" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
        <h6>No applicants found</h6><p>Try adjusting the search or filter.</p>
      </div></td></tr>`;
    } else {
      tbody.innerHTML = slice.map(r => `<tr>
        <td>
          <div class="aer-name">${esc(r.name)}</div>
          ${r.record ? `<div class="aer-recno">${esc(r.record)}</div>` : ''}
        </td>
        <td><div class="aer-addr">${esc(r.address)}</div></td>
        <td>
          <div class="aer-pos-title">${esc(r.posTitle)}</div>
          <span class="aer-badge">${esc(r.posBadge)}</span>
        </td>
        <td><div class="aer-track">${esc(r.track) || '—'}</div></td>
        <td class="tc">
          ${r.hasNC
            ? `<a class="aer-nc-btn" href="${esc(r.ncUrl)}" target="_blank">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                View
               </a>`
            : '<span style="color:var(--aer-text-3);font-size:11px">—</span>'}
        </td>
        <td class="tr">${chip(r.education)}</td>
        <td class="tr">${chip(r.training)}</td>
        <td class="tr">${chip(r.experience)}</td>
        <td class="tr">${chip(r.let)}</td>
        <td class="tr">${chip(r.demo)}</td>
        <td class="tr">${chip(r.reflection)}</td>
      </tr>`).join('');
    }

    renderPager();
    updateStats();
    updateCount(slice.length);
  }

  function esc(s) {
    return String(s ?? '')
      .replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')
      .replace(/"/g,'&quot;').replace(/'/g,'&#39;');
  }

  /* ── Stats ── */
  function updateStats() {
    const d = filtered;
    document.getElementById('aer-s-total').textContent = d.length;
    document.getElementById('aer-s-demo').textContent  = d.filter(r => r.demo !== '').length;
    const lets = d.filter(r => r.let !== '').map(r => parseFloat(r.let));
    document.getElementById('aer-s-let').textContent   = lets.length
      ? (lets.reduce((a,b) => a+b,0)/lets.length).toFixed(1) : '—';
    document.getElementById('aer-s-nc').textContent    = d.filter(r => r.hasNC).length;
  }

  function updateCount(shown) {
    document.getElementById('aer-count').innerHTML =
      `Showing <strong>${shown}</strong> of <strong>${filtered.length}</strong> applicants`;
  }

  /* ── Subtitle ── */
  function updateSub() {
    const el = document.getElementById('aer-sub');
    if (!el) return;
    const sel = document.getElementById('aer-filter');
    const label = sel && sel.value
      ? (sel.options[sel.selectedIndex]?.text || 'Selected position')
      : 'All positions';
    el.textContent = `${label} · ${SELECTED_YEAR}`;
  }

  /* ── Pagination ── */
  function renderPager() {
    const pager = document.getElementById('aer-pager');
    if (pp === -1 || filtered.length === 0) { pager.innerHTML = ''; return; }
    const total = Math.ceil(filtered.length / pp);
    const arr = [`<button class="aer-pgbtn" onclick="aerPage(${page-1})" ${page===1?'disabled':''}><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" width="11" height="11"><polyline points="15 18 9 12 15 6"/></svg></button>`];
    for (let i = 1; i <= total; i++) {
      if (i===1||i===total||Math.abs(i-page)<=1) {
        arr.push(`<button class="aer-pgbtn${i===page?' on':''}" onclick="aerPage(${i})">${i}</button>`);
      } else if (Math.abs(i-page)===2) {
        arr.push(`<span style="color:var(--aer-text-3);font-size:11px;padding:0 2px">…</span>`);
      }
    }
    arr.push(`<button class="aer-pgbtn" onclick="aerPage(${page+1})" ${page===total?'disabled':''}><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" width="11" height="11"><polyline points="9 18 15 12 9 6"/></svg></button>`);
    pager.innerHTML = arr.join('');
  }

  window.aerPage = function(p) {
    const total = Math.ceil(filtered.length / pp);
    if (p < 1 || p > total) return;
    page = p;
    render();
    document.querySelector('.aer-card').scrollIntoView({ behavior:'smooth', block:'start' });
  };

  window.aerChangePP = function() {
    pp = parseInt(document.getElementById('aer-pp').value);
    page = 1;
    render();
  };

  /* ── Search ── */
  document.getElementById('aer-search').addEventListener('input', function() {
    const q = this.value.toLowerCase().trim();
    filtered = RAW.filter(r =>
      !q || [r.name, r.address, r.posTitle, r.posBadge, r.track, r.record]
        .some(f => String(f).toLowerCase().includes(q))
    );
    page = 1;
    render();
  });

  /* ── Sort ── */
  const KEYS = ['name','address','posTitle','track','hasNC','education','training','experience','let','demo','reflection'];
  window.aerSort = function(col) {
    if (sortCol === col) { sortAsc = !sortAsc; } else { sortCol = col; sortAsc = true; }
    document.querySelectorAll('.aer-sort').forEach((el,i) => {
      el.textContent = i===col ? (sortAsc ? '↑' : '↓') : '↕';
      el.classList.toggle('on', i===col);
    });
    filtered.sort((a, b) => {
      let va = a[KEYS[col]], vb = b[KEYS[col]];
      const na = parseFloat(va), nb = parseFloat(vb);
      if (!isNaN(na) && !isNaN(nb)) { va = na; vb = nb; }
      else if (va===''||va===null) { va = sortAsc ? Infinity : -Infinity; }
      else if (vb===''||vb===null) { vb = sortAsc ? Infinity : -Infinity; }
      else { va = String(va).toLowerCase(); vb = String(vb).toLowerCase(); }
      return sortAsc ? (va > vb ? 1 : va < vb ? -1 : 0) : (va < vb ? 1 : va > vb ? -1 : 0);
    });
    page = 1;
    render();
  };

  /* ── Excel export ── */
  window.aerExportExcel = function() {
    if (typeof XLSX === 'undefined') { alert('XLSX library not loaded.'); return; }
    const headers = ['Name','Record No.','Complete Address','Job Title','Job Type','Applied Track / Strand',
                     'National Certificate','Education','Trainings','Experience','LET Rating','Demo','Teacher Reflection'];
    const rows = filtered.map(r => [
      r.name, r.record, r.address, r.posTitle, r.posBadge, r.track,
      r.hasNC ? 'Yes' : 'No',
      r.education||'', r.training||'', r.experience||'', r.let||'', r.demo||'', r.reflection||''
    ]);
    const ws = XLSX.utils.aoa_to_sheet([headers, ...rows]);
    ws['!cols'] = [22,16,38,22,18,30,14,10,10,12,10,10,16].map(w => ({wch:w}));
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, 'Evaluation Report');
    XLSX.writeFile(wb, `Applicant_Evaluation_Report_${new Date().toISOString().slice(0,10)}.xlsx`);
  };

  /* ── PDF export ── */
  window.aerExportPDF = function() {
    if (typeof window.jspdf === 'undefined') { alert('jsPDF library not loaded.'); return; }
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF({ orientation:'landscape', unit:'mm', format:'a3' });

    doc.setFontSize(14);
    doc.setTextColor(15,31,61);
    doc.setFont(undefined,'bold');
    doc.text('Applicant Evaluation Report', 14, 15);

    doc.setFontSize(8.5);
    doc.setFont(undefined,'normal');
    doc.setTextColor(100,116,139);
    const sel = document.getElementById('aer-filter');
    const posLabel = sel && sel.value ? (sel.options[sel.selectedIndex]?.text||'') : 'All positions';
    doc.text(`Position: ${posLabel}   Year: ${SELECTED_YEAR}   Records: ${filtered.length}   Generated: ${new Date().toLocaleDateString('en-PH',{year:'numeric',month:'long',day:'numeric'})}`, 14, 22);

    const head = [['Name','Record No.','Address','Position','Type','Track / Strand','NC','Educ.','Train.','Exp.','LET','Demo','TR']];
    const body = filtered.map(r => [
      r.name, r.record,
      r.address.length > 48 ? r.address.slice(0,45)+'…' : r.address,
      r.posTitle,
      r.posBadge,
      r.track.length > 38 ? r.track.slice(0,35)+'…' : r.track,
      r.hasNC ? 'Yes' : '—',
      r.education||'—', r.training||'—', r.experience||'—', r.let||'—', r.demo||'—', r.reflection||'—'
    ]);

    doc.autoTable({
      head, body,
      startY: 27,
      styles:     { fontSize:7.5, cellPadding:{top:3,bottom:3,left:4,right:4}, valign:'top', overflow:'linebreak' },
      headStyles: { fillColor:[15,31,61], textColor:255, fontStyle:'bold', fontSize:7.5 },
      alternateRowStyles: { fillColor:[248,250,252] },
      columnStyles: {
        0: {cellWidth:32, fontStyle:'bold'},
        1: {cellWidth:22},
        2: {cellWidth:48},
        3: {cellWidth:24},
        4: {cellWidth:22},
        5: {cellWidth:35},
        6: {cellWidth:9, halign:'center'},
        7: {cellWidth:13, halign:'right'},
        8: {cellWidth:13, halign:'right'},
        9: {cellWidth:14, halign:'right'},
        10:{cellWidth:13, halign:'right'},
        11:{cellWidth:13, halign:'right'},
        12:{cellWidth:13, halign:'right'},
      },
      didDrawPage: () => {
        const pg = doc.internal.getCurrentPageInfo().pageNumber;
        const tot = doc.internal.getNumberOfPages();
        doc.setFontSize(7); doc.setTextColor(148,163,184);
        doc.text(`Page ${pg} of ${tot}`, doc.internal.pageSize.width-12, doc.internal.pageSize.height-7, {align:'right'});
      }
    });

    doc.save(`Applicant_Evaluation_Report_${new Date().toISOString().slice(0,10)}.pdf`);
  };

  /* ── Init ── */
  updateSub();
  render();
})();
</script>
<?php endif; ?>