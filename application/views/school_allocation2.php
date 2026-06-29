<?php include('templates/head.php'); ?>
<link href="<?= base_url(); ?>assets/libs/custombox/custombox.min.css" rel="stylesheet" type="text/css">

<!-- DataTables (make sure these exist in your template assets; if already included, you can remove these) -->
<link href="<?= base_url(); ?>assets/libs/datatables/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
<link href="<?= base_url(); ?>assets/libs/datatables/responsive.bootstrap4.min.css" rel="stylesheet" type="text/css" />

<?php include('templates/header.php'); ?>

<style>
  .money { text-align: right; white-space: nowrap; }
  .table thead th { vertical-align: middle; }
  .subtle { color: #6c757d; font-size: 12px; }

  /* Summary table */
  #yearSummary td, #yearSummary th { vertical-align: middle; }

  /* Detail modal: top-aligned + maximized view */
  .detail-modal .modal-dialog { max-width: 96%; margin: 1rem auto; }
  .detail-modal .modal-content { border: 0; border-radius: 14px; overflow: hidden; box-shadow: 0 14px 44px rgba(0,0,0,.20); }
  .detail-modal .modal-header {
    background: linear-gradient(135deg, #4b7bec 0%, #3867d6 100%);
    color: #fff; border-bottom: 0; padding: 16px 22px;
  }
  .detail-modal .modal-header .modal-title { font-weight: 600; }
  .detail-modal .modal-header .close { color: #fff; opacity: .9; text-shadow: none; }
  .detail-modal .modal-body { max-height: 82vh; overflow: auto; background: #f5f6fa; padding: 20px 22px; }
  .detail-modal .modal-footer { border-top: 1px solid rgba(0,0,0,.06); }

  .summary-bar { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; margin-bottom: 18px; }
  .badge-soft {
    background: #fff; color: #3867d6; border: 1px solid rgba(56,103,214,.25);
    padding: 8px 14px; border-radius: 999px; font-weight: 600; font-size: 12.5px;
    display: inline-flex; align-items: center; gap: 6px;
  }

  /* Batch cards + month grid (no compressed single row) */
  .batch-card { background:#fff; border:1px solid rgba(0,0,0,.06); border-radius:12px; margin-bottom:16px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,.05); }
  .batch-head { display:flex; flex-wrap:wrap; gap:12px; align-items:center; justify-content:space-between; padding:14px 18px; border-bottom:1px solid rgba(0,0,0,.06); }
  .batch-head .meta { display:flex; gap:8px; flex-wrap:wrap; align-items:center; }
  .batch-no { font-weight:700; font-size:14px; color:#2f3542; }
  .chip { display:inline-flex; align-items:center; gap:5px; background:#f1f3fa; color:#3867d6; padding:5px 11px; border-radius:8px; font-size:12px; font-weight:600; }
  .chip.type { background:#eafaf1; color:#1e9e63; }
  .batch-total { text-align:right; }
  .batch-total .lbl { display:block; font-size:11px; text-transform:uppercase; letter-spacing:.04em; color:#8a94a6; font-weight:600; }
  .batch-total .val { font-size:19px; font-weight:700; color:#3867d6; white-space:nowrap; }

  .month-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:12px; padding:18px; }
  .month-tile { border:1px solid rgba(0,0,0,.07); border-radius:10px; padding:12px 14px; background:#fff; transition:.15s; }
  .month-tile:hover { border-color:rgba(56,103,214,.40); box-shadow:0 3px 10px rgba(56,103,214,.10); transform:translateY(-1px); }
  .month-tile .m-label { font-size:11px; text-transform:uppercase; letter-spacing:.05em; color:#8a94a6; font-weight:600; margin-bottom:4px; }
  .month-tile .m-value { font-size:15px; font-weight:600; color:#2f3542; white-space:nowrap; }
  .month-tile.zero { background:#f8f9fb; }
  .month-tile.zero .m-value { color:#c2c8d0; font-weight:500; }

  .totals-card { border:0; background:linear-gradient(135deg,#2f3542 0%,#3867d6 100%); }
  .totals-card .batch-head { border-bottom-color:rgba(255,255,255,.15); }
  .totals-card .batch-no { color:#fff; }
  .totals-card .batch-total .lbl { color:rgba(255,255,255,.7); }
  .totals-card .batch-total .val { color:#fff; }
  .totals-card .month-tile { background:rgba(255,255,255,.08); border-color:rgba(255,255,255,.16); }
  .totals-card .month-tile:hover { transform:none; box-shadow:none; }
  .totals-card .month-tile .m-label { color:rgba(255,255,255,.7); }
  .totals-card .month-tile .m-value { color:#fff; }
  .totals-card .month-tile.zero { background:rgba(255,255,255,.04); }
  .totals-card .month-tile.zero .m-value { color:rgba(255,255,255,.45); }

  @media (max-width: 992px){ .month-grid { grid-template-columns:repeat(3,1fr); } }
  @media (max-width: 768px){ .month-grid { grid-template-columns:repeat(2,1fr); } .detail-modal .modal-dialog { max-width:98%; } }
  @media (max-width: 480px){ .month-grid { grid-template-columns:repeat(1,1fr); } }
</style>

<?php
// -----------------------------
// Group data by Year
// -----------------------------
$grouped = [];
if (!empty($data)) {
  foreach ($data as $r) {
    $y = (string)$r->alloc_year;
    if (!isset($grouped[$y])) $grouped[$y] = [];
    $grouped[$y][] = $r;
  }
  // Sort years DESC (latest first)
  krsort($grouped, SORT_NUMERIC);
}

$months = [
  'mo_jan' => 'Jan', 'mo_feb' => 'Feb', 'mo_mar' => 'Mar', 'mo_apr' => 'Apr',
  'mo_may' => 'May', 'mo_jun' => 'Jun', 'mo_jul' => 'Jul', 'mo_aug' => 'Aug',
  'mo_sep' => 'Sep', 'mo_oct' => 'Oct', 'mo_nov' => 'Nov', 'mo_dec' => 'Dec',
];

if (!function_exists('n2')) {
  function n2($v) { return number_format((float)$v, 2); }
}
if (!function_exists('sum_field')) {
  function sum_field($rows, $field) {
    $t = 0.0;
    foreach ($rows as $r) { $t += (float)($r->$field ?? 0); }
    return $t;
  }
}
?>

<div class="content-page">
  <div class="content">
    <div class="container-fluid">

      <!-- Page Title -->
      <div class="row">
        <div class="col-12">
          <div class="page-title-box">
            <div></div>
          </div>
        </div>
      </div>

      <!-- Flash Alerts -->
      <?php if ($this->session->flashdata('success')) : ?>
        <?= '<div class="alert alert-success alert-dismissible fade show" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>' . $this->session->flashdata('success') . '</div>'; ?>
      <?php endif; ?>

      <?php if ($this->session->flashdata('danger')) : ?>
        <?= '<div class="alert alert-danger alert-dismissible fade show" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>' . $this->session->flashdata('danger') . '</div>'; ?>
      <?php endif; ?>

      <!-- MAIN CARD: summary by year -->
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-body">

              <div class="d-flex align-items-center justify-content-between mb-3">
                <h4 class="header-title mb-0">SCHOOL ALLOCATION</h4>
                <span class="badge badge-secondary"><?= !empty($data) ? count($data) : 0; ?> record(s)</span>
              </div>

              <?php if (empty($grouped)) { ?>
                <div class="alert alert-info mb-0">No allocation records found.</div>
              <?php } else { ?>

                <div class="table-responsive">
                  <table id="yearSummary" class="table table-bordered table-hover nowrap w-100">
                    <thead class="thead-light">
                      <tr>
                        <th>Year</th>
                        <th class="text-center">Batches</th>
                        <th class="money">Total Allocation (₱)</th>
                        <th class="money">Monthly Total (₱)</th>
                        <th class="text-center">Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($grouped as $year => $rows):
                        $yearAllocTotal = sum_field($rows, 'alloc_amount');
                        $yearMonthlyTotal = 0.0;
                        foreach ($months as $mf => $ml) { $yearMonthlyTotal += sum_field($rows, $mf); }
                      ?>
                        <tr>
                          <td><strong><?= htmlspecialchars($year); ?></strong></td>
                          <td class="text-center"><span class="badge badge-primary"><?= count($rows); ?></span></td>
                          <td class="money"><?= n2($yearAllocTotal); ?></td>
                          <td class="money"><?= n2($yearMonthlyTotal); ?></td>
                          <td class="text-center">
                            <button type="button" class="btn btn-sm btn-primary waves-effect waves-light"
                                    data-toggle="modal" data-target="#yearModal_<?= htmlspecialchars($year); ?>">
                              <i class="mdi mdi-eye-outline"></i> View Details
                            </button>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>

              <?php } ?>

            </div>
          </div>
        </div>
      </div>

    </div><!-- container-fluid -->
  </div><!-- content -->
</div><!-- content-page -->


<!-- ============================================
     PER-YEAR DETAIL MODALS (maximized view)
============================================= -->
<?php if (!empty($grouped)) : ?>
  <?php foreach ($grouped as $year => $rows):
    $yearAllocTotal = sum_field($rows, 'alloc_amount');
    $monthTotals = [];
    $yearMonthlyTotal = 0.0;
    foreach ($months as $mf => $ml) {
      $monthTotals[$mf] = sum_field($rows, $mf);
      $yearMonthlyTotal += $monthTotals[$mf];
    }
  ?>
  <div class="modal fade detail-modal" id="yearModal_<?= htmlspecialchars($year); ?>"
       tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">

        <div class="modal-header">
          <h5 class="modal-title">
            <i class="mdi mdi-calendar-month-outline"></i>
            Year <?= htmlspecialchars($year); ?> &mdash; Allocation Details
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        </div>

        <div class="modal-body">

          <div class="summary-bar">
            <span class="badge-soft"><i class="mdi mdi-folder-multiple-outline"></i> <?= count($rows); ?> batch(es)</span>
            <span class="badge-soft"><i class="mdi mdi-cash-multiple"></i> Total Allocation: ₱ <?= n2($yearAllocTotal); ?></span>
            <?php if (round($yearMonthlyTotal, 2) != round($yearAllocTotal, 2)) { ?>
              <span class="badge-soft"><i class="mdi mdi-calendar-check-outline"></i> Distributed to Months: ₱ <?= n2($yearMonthlyTotal); ?></span>
            <?php } ?>
          </div>

          <?php foreach ($rows as $row) { ?>
            <div class="batch-card">
              <div class="batch-head">
                <div class="meta">
                  <span class="batch-no">Batch <?= htmlspecialchars($row->alloc_batch); ?></span>
                  <span class="chip"><i class="mdi mdi-account-group-outline"></i> <?= htmlspecialchars($row->alloc_group); ?></span>
                  <span class="chip type"><i class="mdi mdi-tag-outline"></i> <?= htmlspecialchars($row->alloc_type); ?></span>
                </div>
                <div class="batch-total">
                  <span class="lbl">Total Allocation</span>
                  <span class="val">₱ <?= n2($row->alloc_amount); ?></span>
                </div>
              </div>
              <div class="month-grid">
                <?php foreach ($months as $mf => $ml) {
                  $val = (float)$row->$mf;
                ?>
                  <div class="month-tile<?= $val == 0.0 ? ' zero' : ''; ?>">
                    <div class="m-label"><?= $ml; ?></div>
                    <div class="m-value">₱ <?= n2($val); ?></div>
                  </div>
                <?php } ?>
              </div>
            </div>
          <?php } ?>

          <?php if (count($rows) > 1) { // combined totals only make sense with multiple batches ?>
          <!-- Combined totals across all batches -->
          <div class="batch-card totals-card">
            <div class="batch-head">
              <div class="meta">
                <span class="batch-no"><i class="mdi mdi-sigma"></i> Combined Totals &middot; All Batches</span>
              </div>
              <div class="batch-total">
                <span class="lbl">Total Allocation</span>
                <span class="val">₱ <?= n2($yearAllocTotal); ?></span>
              </div>
            </div>
            <div class="month-grid">
              <?php foreach ($months as $mf => $ml) {
                $val = (float)$monthTotals[$mf];
              ?>
                <div class="month-tile<?= $val == 0.0 ? ' zero' : ''; ?>">
                  <div class="m-label"><?= $ml; ?></div>
                  <div class="m-value">₱ <?= n2($val); ?></div>
                </div>
              <?php } ?>
            </div>
          </div>
          <?php } ?>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
        </div>

      </div>
    </div>
  </div>
  <?php endforeach; ?>
<?php endif; ?>


<?php include('templates/footer.php'); ?>

<!-- DataTables scripts (remove if already loaded globally) -->
<script src="<?= base_url(); ?>assets/libs/datatables/jquery.dataTables.min.js"></script>
<script src="<?= base_url(); ?>assets/libs/datatables/dataTables.bootstrap4.min.js"></script>
<script src="<?= base_url(); ?>assets/libs/datatables/dataTables.responsive.min.js"></script>
<script src="<?= base_url(); ?>assets/libs/datatables/responsive.bootstrap4.min.js"></script>

<script>
  $(document).ready(function () {
    // Clean summary table (few columns -> no overlap)
    $('#yearSummary').DataTable({
      responsive: true,
      pageLength: 10,
      lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
      order: [[0, 'desc']],
      columnDefs: [
        { targets: [1, 2, 3, 4], orderable: false }
      ]
    });
  });
</script>
