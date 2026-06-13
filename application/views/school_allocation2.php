<?php include('templates/head.php'); ?>
<link href="<?= base_url(); ?>assets/libs/custombox/custombox.min.css" rel="stylesheet" type="text/css">

<!-- DataTables (make sure these exist in your template assets; if already included, you can remove these) -->
<link href="<?= base_url(); ?>assets/libs/datatables/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
<link href="<?= base_url(); ?>assets/libs/datatables/responsive.bootstrap4.min.css" rel="stylesheet" type="text/css" />

<?php include('templates/header.php'); ?>

<style>
  .money { text-align: right; white-space: nowrap; }
  .table thead th { vertical-align: middle; }
  .year-summary {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
  }
  .year-summary .badges { display: flex; gap: 8px; flex-wrap: wrap; }
  .badge-soft {
    background: rgba(0,0,0,.05);
    color: #333;
    padding: 6px 10px;
    border-radius: 999px;
    font-weight: 600;
    font-size: 12px;
  }
  .accordion .card { border: 1px solid rgba(0,0,0,.06); }
  .accordion .card-header { background: #fff; }
  .accordion .btn-link { text-decoration: none !important; width: 100%; text-align: left; }
  .table-responsive { padding-bottom: 6px; }
  .action-col { white-space: nowrap; }
  .subtle { color: #6c757d; font-size: 12px; }
</style>

<div class="content-page">
  <div class="content">
    <div class="container-fluid">

      <!-- Page Title / Actions -->
      <div class="row">
        <div class="col-12">
          <div class="page-title-box d-flex align-items-center justify-content-between">
            <!-- <div>
              <h4 class="page-title mb-0">School Allocation</h4>
              <div class="subtle">Grouped view by year • Monthly breakdown included</div>
            </div> -->

            <div>
              <?php if(isset($st) && (int)$st->type === 1){ ?>
                <a data-toggle="modal" class="btn btn-primary waves-effect waves-light" href="#add">
                  <i class="mdi mdi-plus"></i> Add New
                </a>
              <?php } ?>
            </div>
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

      function n2($v) {
        $n = (float)$v;
        return number_format($n, 2);
      }

      function sum_field($rows, $field) {
        $t = 0.0;
        foreach ($rows as $r) {
          $t += (float)($r->$field ?? 0);
        }
        return $t;
      }
      ?>

      <!-- MAIN CARD -->
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-body">

              <div class="d-flex align-items-center justify-content-between mb-3">
                <h4 class="header-title mb-0">SCHOOL ALLOCATION</h4>
                <span class="badge badge-secondary"><?= !empty($data) ? count($data) : 0; ?> record(s)</span>
              </div>

              <?php if (empty($grouped)) { ?>
                <div class="alert alert-info mb-0">
                  No allocation records found.
                </div>
              <?php } else { ?>

                <div id="yearAccordion" class="accordion">

                  <?php
                  $idx = 0;
                  foreach ($grouped as $year => $rows):

                    $yearAllocTotal = sum_field($rows, 'alloc_amount');
                    $yearJan = sum_field($rows, 'mo_jan');
                    $yearFeb = sum_field($rows, 'mo_feb');
                    $yearMar = sum_field($rows, 'mo_mar');
                    $yearApr = sum_field($rows, 'mo_apr');
                    $yearMay = sum_field($rows, 'mo_may');
                    $yearJun = sum_field($rows, 'mo_jun');
                    $yearJul = sum_field($rows, 'mo_jul');
                    $yearAug = sum_field($rows, 'mo_aug');
                    $yearSep = sum_field($rows, 'mo_sep');
                    $yearOct = sum_field($rows, 'mo_oct');
                    $yearNov = sum_field($rows, 'mo_nov');
                    $yearDec = sum_field($rows, 'mo_dec');

                    $collapseId = "collapseYear_" . $year;
                    $headingId  = "headingYear_" . $year;

                    $isOpen = ($idx === 0); // open latest year by default
                  ?>

                  <div class="card mb-2">
                    <div class="card-header" id="<?= $headingId; ?>">
                      <button class="btn btn-link p-0 d-flex align-items-center justify-content-between"
                              data-toggle="collapse"
                              data-target="#<?= $collapseId; ?>"
                              aria-expanded="<?= $isOpen ? 'true' : 'false'; ?>"
                              aria-controls="<?= $collapseId; ?>">
                        <div class="year-summary w-100">
                          <div class="d-flex align-items-center gap-2">
                            <h5 class="mb-0">
                              <i class="mdi mdi-calendar-month-outline"></i>
                              Year <?= htmlspecialchars($year); ?>
                            </h5>
                            <span class="badge badge-primary"><?= count($rows); ?> batch(es)</span>
                          </div>

                          <div class="badges">
                            <span class="badge-soft">Total Allocation: ₱ <?= n2($yearAllocTotal); ?></span>
                            <span class="badge-soft">Jan–Dec Total: ₱ <?= n2($yearJan+$yearFeb+$yearMar+$yearApr+$yearMay+$yearJun+$yearJul+$yearAug+$yearSep+$yearOct+$yearNov+$yearDec); ?></span>
                          </div>
                        </div>
                      </button>
                    </div>

                    <div id="<?= $collapseId; ?>"
                         class="collapse <?= $isOpen ? 'show' : ''; ?>"
                         aria-labelledby="<?= $headingId; ?>"
                         data-parent="#yearAccordion">

                      <div class="card-body">

                        <!-- Year totals row -->
                        <div class="alert alert-light border d-flex flex-wrap align-items-center justify-content-between mb-3">
                          <div><strong>Year Summary (<?= htmlspecialchars($year); ?>)</strong></div>
                          <div class="subtle">
                            Allocation Total: <strong>₱ <?= n2($yearAllocTotal); ?></strong>
                            &nbsp;•&nbsp; Monthly Total: <strong>₱ <?= n2($yearJan+$yearFeb+$yearMar+$yearApr+$yearMay+$yearJun+$yearJul+$yearAug+$yearSep+$yearOct+$yearNov+$yearDec); ?></strong>
                          </div>
                        </div>

                        <div class="table-responsive">
                          <table class="table table-bordered dt-responsive nowrap datatable-year"
                                 style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead class="thead-light">
                              <tr>
                                <th>Batch</th>
                                <th>Group</th>
                                <th>Allocation Type</th>
                                <th class="money">Total Allocation</th>
                                <th class="money">Jan</th>
                                <th class="money">Feb</th>
                                <th class="money">Mar</th>
                                <th class="money">Apr</th>
                                <th class="money">May</th>
                                <th class="money">Jun</th>
                                <th class="money">Jul</th>
                                <th class="money">Aug</th>
                                <th class="money">Sep</th>
                                <th class="money">Oct</th>
                                <th class="money">Nov</th>
                                <th class="money">Dec</th>
                                <th class="action-col">Action</th>
                              </tr>
                            </thead>

                            <tbody>
                              <?php foreach ($rows as $row) { ?>
                                <tr>
                                  <td><?= htmlspecialchars($row->alloc_batch); ?></td>
                                  <td><?= htmlspecialchars($row->alloc_group); ?></td>
                                  <td><?= htmlspecialchars($row->alloc_type); ?></td>

                                  <td class="money"><?= n2($row->alloc_amount); ?></td>
                                  <td class="money"><?= n2($row->mo_jan); ?></td>
                                  <td class="money"><?= n2($row->mo_feb); ?></td>
                                  <td class="money"><?= n2($row->mo_mar); ?></td>
                                  <td class="money"><?= n2($row->mo_apr); ?></td>
                                  <td class="money"><?= n2($row->mo_may); ?></td>
                                  <td class="money"><?= n2($row->mo_jun); ?></td>
                                  <td class="money"><?= n2($row->mo_jul); ?></td>
                                  <td class="money"><?= n2($row->mo_aug); ?></td>
                                  <td class="money"><?= n2($row->mo_sep); ?></td>
                                  <td class="money"><?= n2($row->mo_oct); ?></td>
                                  <td class="money"><?= n2($row->mo_nov); ?></td>
                                  <td class="money"><?= n2($row->mo_dec); ?></td>

                                  <td class="action-col">
                                    <?php if(isset($st) && (int)$st->type === 1){ ?>
                                      <a class="text-success font-weight-bold"
                                         href="<?= base_url('Page/school_allocation_edit/' . $row->id); ?>">
                                        <i class="mdi mdi-pencil"></i> Edit
                                      </a>
                                    <?php } else { ?>
                                      <span class="text-muted">—</span>
                                    <?php } ?>
                                  </td>
                                </tr>
                              <?php } ?>
                            </tbody>

                            <tfoot>
                              <tr class="font-weight-bold">
                                <td colspan="3" class="text-right">Totals (<?= htmlspecialchars($year); ?>)</td>
                                <td class="money">₱ <?= n2($yearAllocTotal); ?></td>
                                <td class="money">₱ <?= n2($yearJan); ?></td>
                                <td class="money">₱ <?= n2($yearFeb); ?></td>
                                <td class="money">₱ <?= n2($yearMar); ?></td>
                                <td class="money">₱ <?= n2($yearApr); ?></td>
                                <td class="money">₱ <?= n2($yearMay); ?></td>
                                <td class="money">₱ <?= n2($yearJun); ?></td>
                                <td class="money">₱ <?= n2($yearJul); ?></td>
                                <td class="money">₱ <?= n2($yearAug); ?></td>
                                <td class="money">₱ <?= n2($yearSep); ?></td>
                                <td class="money">₱ <?= n2($yearOct); ?></td>
                                <td class="money">₱ <?= n2($yearNov); ?></td>
                                <td class="money">₱ <?= n2($yearDec); ?></td>
                                <td></td>
                              </tr>
                            </tfoot>

                          </table>
                        </div>

                      </div>
                    </div>
                  </div>

                  <?php
                    $idx++;
                  endforeach;
                  ?>

                </div><!-- /accordion -->

              <?php } ?>

            </div>
          </div>
        </div>
      </div>

    </div><!-- container-fluid -->
  </div><!-- content -->
</div><!-- content-page -->


<!-- ============================================
     ADD MODAL
============================================= -->
<div id="add" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="myModalLabel">Change Allocation</h5>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      </div>

      <div class="modal-body">
        <?= form_open('Page/fund_add'); ?>
          <input type="hidden" required value="<?= $this->session->username ?>" name="schoolID" class="form-control">

          <div class="form-group">
            <label>Fund Allocation Amount</label>
            <input type="text" required value="<?= set_value('alloc_amount'); ?>" name="alloc_amount" class="form-control" placeholder="e.g. 100000.00">
          </div>

          <input type="hidden" required value="<?= isset($last->alloc_batch) ? ((int)$last->alloc_batch + 1) : 1; ?>" name="bcode" class="form-control">

          <div class="form-group">
            <label>Fiscal Year</label>
            <select class="form-control" name="fy" required>
              <option value=""></option>
              <?php
                $firstYear = (int)date('Y');
                $lastYear  = $firstYear + 5;
                for($i=$firstYear; $i<=$lastYear; $i++){
                  echo '<option value="'.$i.'">'.$i.'</option>';
                }
              ?>
            </select>
          </div>

          <div class="form-group">
            <label>Group</label>
            <select class="form-control" name="group" required>
              <option value=""></option>
              <?php
                $g = array('Senior HS','Junior HS','Elementary');
                foreach($g as $row){
              ?>
                <option value="<?= htmlspecialchars($row); ?>"><?= htmlspecialchars($row); ?></option>
              <?php } ?>
            </select>
          </div>

          <div class="form-group">
            <label>Allocation Type</label>
            <select class="form-control" name="type" required>
              <option value=""></option>
              <?php if(!empty($bs)) foreach($bs as $row){ ?>
                <option value="<?= htmlspecialchars($row->description); ?>"><?= htmlspecialchars($row->description); ?></option>
              <?php } ?>
            </select>
          </div>

          <div class="modal-footer px-0">
            <button type="submit" name="submit" class="btn btn-primary waves-effect waves-light">
              <i class="mdi mdi-check"></i> Submit
            </button>
          </div>
        </form>
      </div>

    </div>
  </div>
</div>


<?php include('templates/footer.php'); ?>

<!-- DataTables scripts (remove if already loaded globally) -->
<script src="<?= base_url(); ?>assets/libs/datatables/jquery.dataTables.min.js"></script>
<script src="<?= base_url(); ?>assets/libs/datatables/dataTables.bootstrap4.min.js"></script>
<script src="<?= base_url(); ?>assets/libs/datatables/dataTables.responsive.min.js"></script>
<script src="<?= base_url(); ?>assets/libs/datatables/responsive.bootstrap4.min.js"></script>

<script>
  $(document).ready(function () {
    // Initialize DataTables for each year table
    $('.datatable-year').DataTable({
      responsive: true,
      pageLength: 10,
      lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
      order: [],
      columnDefs: [
        { targets: [0,1,2,16], orderable: true },
        { targets: [3,4,5,6,7,8,9,10,11,12,13,14,15], orderable: false }
      ]
    });
  });
</script>
