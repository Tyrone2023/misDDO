<?php
// =========================
// Pending Rating Requests Report (PRINT-READY + PRINT ALL DATATABLE ROWS)
// =========================
$jobTypes = [
    1  => '- Elementary',
    2  => '- Secondary',
    3  => '- Junior High School',
    4  => '- Senior High School',
    5  => '- Kindergarten',
    6  => '- IPED Elementary',
    7  => '- IPED Secondary',
    8  => '- IPED Junior High School',
    9  => '- IPED Senior High School',
    10 => '- SNED',
];

function h($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

// Optional: print header info
$reportTitle = 'Pending Rating Requests Report';
$printDate   = date('F d, Y h:i A');
$totalRows   = is_array($rows ?? null) ? count($rows) : 0;
?>

<style>
/* SCREEN STYLES (optional small UI polish) */
.report-header {
    display:flex;
    align-items:flex-end;
    justify-content:space-between;
    gap:12px;
    margin-bottom:12px;
}
.report-header .meta {
    color:#6b7280;
    font-size:12px;
}
.badge-soft {
    display:inline-block;
    padding:4px 10px;
    border-radius:999px;
    background:#eef2ff;
    color:#3730a3;
    font-weight:700;
    font-size:12px;
}
.table td, .table th { vertical-align: top; }
.prev-list { margin:0; padding-left:18px; }
.prev-list li { margin:0; }

/* =========================
   PRINT STYLES (CLEAN + FULL DATA)
========================= */
@media print {

  /* hide non-print UI */
  .d-print-none,
  .page-title-box,
  .dataTables_filter,
  .dataTables_length,
  .dataTables_info,
  .dataTables_paginate,
  .dt-buttons,
  .buttons-print,
  .buttons-html5,
  .navbar,
  .footer {
    display: none !important;
  }

  /* remove responsive scroll wrappers */
  .table-responsive,
  .dataTables_wrapper,
  .dataTables_scroll,
  .dataTables_scrollBody {
    overflow: visible !important;
  }

  /* force full width and safe wrapping */
  table {
    width: 100% !important;
    table-layout: fixed !important;
    border-collapse: collapse !important;
  }

  th, td {
    font-size: 11px !important;
    padding: 6px 8px !important;
    vertical-align: top !important;
    word-wrap: break-word !important;
    overflow-wrap: break-word !important;
    white-space: normal !important; /* critical: no nowrap on print */
  }

  thead { display: table-header-group; }  /* repeat header each page */
  tfoot { display: table-footer-group; }

  tr, td, th {
    page-break-inside: avoid !important;
  }

  /* remove card borders/shadows */
  .card {
    border: none !important;
    box-shadow: none !important;
  }

  /* nice print margins */
  @page {
    size: A4 portrait;
    margin: 10mm;
  }

  body {
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
    background: #fff !important;
  }

  /* print-only header */
  .print-only-header {
    display: block !important;
    margin-bottom: 10px !important;
  }
}

/* hide print-only header on screen */
.print-only-header { display:none; }
.print-only-header .title { font-size:16px; font-weight:900; margin:0; }
.print-only-header .sub { font-size:12px; color:#374151; margin:2px 0 0; }
</style>

<div class="content-page">
  <div class="content">
    <div class="container-fluid">

      <!-- SCREEN TITLE BAR -->
      <div class="row">
        <div class="col-12">
          <div class="page-title-box d-print-none">
            <h4 class="page-title"><?= h($reportTitle); ?></h4>
            <a href="<?= base_url('RatingBatch'); ?>" class="btn btn-sm btn-outline-secondary">Back to Mass Retention</a>
          </div>
        </div>
      </div>

      <!-- ACTION BAR -->
      <div class="row">
        <div class="col-12 d-flex justify-content-between align-items-center mb-2 d-print-none">
          <!-- <div class="meta text-muted" style="font-size:12px;">
            Generated: <?= h($printDate); ?> &nbsp; | &nbsp;
            Total: <span class="badge-soft"><?= (int)$totalRows; ?></span>
          </div> -->
          <button class="btn btn-sm btn-primary" onclick="window.print()">Print</button>
        </div>

        <div class="col-12">
          <div class="card">
            <div class="card-body">

              <!-- PRINT HEADER (ONLY VISIBLE ON PRINT) -->
              <!-- <div class="print-only-header">
                <p class="title"><?= h($reportTitle); ?></p>
                <p class="sub">Generated: <?= h($printDate); ?> | Total Applicants: <?= (int)$totalRows; ?></p>
              </div> -->

              <h4 class="header-title mb-3">Disapproved Applicants Requests</h4>

              <div class="table-responsive">
                <table id="datatable" class="table table-bordered" style="border-collapse: collapse; border-spacing:0; width:100%;">
                  <thead>
                    <tr>
                      <th style="width: 28%;">Name</th>
                      <th style="width: 32%;">Current Application</th>
                      <th style="width: 40%;">Previous Applications</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (!empty($rows) && is_array($rows)): ?>
                      <?php foreach ($rows as $row): ?>
                        <?php
                          $lvl = $jobTypes[$row->job_type] ?? '';
                          $fullName = trim(
                            ($row->last_name ?? '') . ', ' .
                            ($row->first_name ?? '') . ' ' .
                            ($row->middle_name ?? '')
                          );
                        ?>
                        <tr>
                          <td>
                            <div style="font-weight:800; color:#111827;"><?= h($fullName); ?></div>
                          </td>

                          <td>
                            <div style="font-weight:800;"><?= h($row->jobTitle ?? 'N/A'); ?> <?= h($lvl); ?></div>
                          </td>

                          <td>
                            <?php if (empty($row->history) || !is_array($row->history)): ?>
                              <span class="text-muted">No prior applications</span>
                            <?php else: ?>
                              <ul class="prev-list">
                                <?php foreach ($row->history as $hist): ?>
                                  <li>
                                    <strong><?= h($hist->jobTitle ?? ''); ?></strong>
                                    <?= h($jobTypes[$hist->job_type] ?? ''); ?>
                                  </li>
                                <?php endforeach; ?>
                              </ul>
                            <?php endif; ?>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <tr>
                        <td colspan="3" class="text-center text-muted">No records found.</td>
                      </tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>

            </div><!-- card-body -->
          </div><!-- card -->
        </div><!-- col -->
      </div><!-- row -->

    </div>
  </div>
</div>

<script>
// =========================
// DataTables PRINT ALL ROWS (beforeprint/afterprint)
// Works even if paging is enabled; prints ALL rows.
// =========================
document.addEventListener('DOMContentLoaded', function () {
  if (!window.jQuery || !jQuery.fn.DataTable) return;

  // If DataTable already initialized elsewhere, use it; else init lightly.
  var $tbl = $('#datatable');
  var table = $.fn.dataTable.isDataTable($tbl) ? $tbl.DataTable() : $tbl.DataTable({
    pageLength: 25,
    lengthChange: true,
    ordering: true,
    searching: true
  });

  var oldLength = table.page.len();

  window.addEventListener('beforeprint', function () {
    try {
      oldLength = table.page.len();
      table.page.len(-1).draw(false); // show ALL rows
    } catch (e) {}
  });

  window.addEventListener('afterprint', function () {
    try {
      table.page.len(oldLength).draw(false); // restore
    } catch (e) {}
  });
});
</script> 