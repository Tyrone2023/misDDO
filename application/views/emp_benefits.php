<?php include('templates/head.php'); ?>
<link href="<?= base_url(); ?>assets/libs/custombox/custombox.min.css" rel="stylesheet" type="text/css">
<?php include('templates/header.php'); ?>

<div class="content-page">
  <div class="content">
    <div class="container-fluid">

      <!-- page title -->
      <div class="row">
        <div class="col-12">
          <div class="page-title-box">
            <div class="clearfix"></div>
          </div>
        </div>
      </div>

      <?php if ($this->session->flashdata('success')) : ?>
        <?= '<div class="alert alert-success alert-dismissible fade show" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>'
          . $this->session->flashdata('success') .
          '</div>';
        ?>
      <?php endif; ?>

      <?php if ($this->session->flashdata('danger')) : ?>
        <?= '<div class="alert alert-danger alert-dismissible fade show" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>'
          . $this->session->flashdata('danger') .
          '</div>';
        ?>
      <?php endif; ?>

      <style>
        .benefits-card {
          border: 0;
          border-radius: 12px;
          box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
          overflow: hidden;
        }

        .benefits-header {
          padding: 16px 20px;
          background: #ffffff;
          border-bottom: 1px solid #f1f1f1;
        }

        .benefits-title {
          margin: 0;
          font-weight: 900;
          font-size: 16px;
          letter-spacing: .3px;
          text-transform: uppercase;
          color: #1f2937;
        }

        .benefits-subtitle {
          margin: 4px 0 0;
          font-size: 12px;
          color: #6c757d;
        }

        .benefits-table th {
          background: #f8fafc;
          font-weight: 800;
          white-space: nowrap;
          border-bottom: 2px solid #e9ecef !important;
        }

        .benefits-table td {
          vertical-align: middle;
        }

        .money {
          text-align: right !important;
          font-variant-numeric: tabular-nums;
          white-space: nowrap;
        }

        .year-row td {
          background: #eef2ff; /* light indigo */
          font-weight: 900;
          color: #1f2937;
        }

        .year-subtotal td {
          background: #fff;
          font-weight: 900;
          border-top: 2px solid #e9ecef !important;
        }

        .grand-total td {
          background: #fff;
          font-weight: 900;
          border-top: 3px solid #111827 !important;
        }

        .empty-state {
          padding: 16px;
          border: 1px dashed #dee2e6;
          border-radius: 10px;
          background: #fafafa;
          color: #6c757d;
        }
      </style>

      <?php
      // helper for safe output
      function h($s)
      {
        return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
      }

      // ---- Group data by year ----
      $grouped = [];
      if (!empty($data)) {
        foreach ($data as $r) {
          $yr = (string)$r->payYear;
          if (!isset($grouped[$yr])) $grouped[$yr] = [];
          $grouped[$yr][] = $r;
        }

        // sort years descending (latest first). Change to sort($keys) if you want ascending.
        $years = array_keys($grouped);
        rsort($years, SORT_NATURAL);
      } else {
        $years = [];
      }
      ?>

      <div class="row">
        <div class="col-12">
          <div class="card benefits-card">
            <div class="benefits-header">
              <h4 class="benefits-title">Employee Benefits</h4>
              <div class="benefits-subtitle">Grouped summary of benefits per year and category</div>
            </div>

            <div class="card-body table-responsive">

              <?php if (empty($years)) : ?>
                <div class="empty-state text-center">
                  <strong>No benefit records found.</strong><br>
                  Please check if benefit entries are available for this employee.
                </div>
              <?php else : ?>

                <?php $grandTotal = 0; ?>

                <table class="table table-striped table-bordered dt-responsive nowrap benefits-table"
                       style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                  <thead>
                    <tr>
                      <th style="width: 140px;">Year</th>
                      <th>Category</th>
                      <th class="money" style="width: 200px;">Amount</th>
                    </tr>
                  </thead>

                  <tbody>
                    <?php foreach ($years as $yr) : ?>
                      <?php
                      $yearSubtotal = 0;
                      ?>

                      <!-- Year Header Row -->
                      <tr class="year-row">
                        <td colspan="3">YEAR: <?= h($yr); ?></td>
                      </tr>

                      <?php foreach ($grouped[$yr] as $row) : ?>
                        <?php
                        $amt = (float)$row->netPay;
                        $yearSubtotal += $amt;
                        $grandTotal += $amt;
                        ?>
                        <tr>
                          <td><?= h($row->payYear); ?></td>
                          <td><?= h($row->bonusCategory); ?></td>
                          <td class="money"><?= number_format($amt, 2); ?></td>
                        </tr>
                      <?php endforeach; ?>

                      <!-- Year Subtotal -->
                      <tr class="year-subtotal">
                        <td colspan="2" class="text-right">Subtotal (<?= h($yr); ?>)</td>
                        <td class="money"><?= number_format($yearSubtotal, 2); ?></td>
                      </tr>

                    <?php endforeach; ?>
                  </tbody>

                  <tfoot>
                    <tr class="grand-total">
                      <td colspan="2" class="text-right">GRAND TOTAL</td>
                      <td class="money"><?= number_format($grandTotal, 2); ?></td>
                    </tr>
                  </tfoot>
                </table>

              <?php endif; ?>

            </div>
          </div>
        </div>
      </div>
      <!-- end row -->

    </div>
  </div>

  <?php include('templates/footer.php'); ?>

  <!-- Training Needs Modal (kept as-is) -->
  <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="myLargeModalLabel">Training Needs</h5>
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        </div>
        <div class="modal-body">

          <form class="form-horizontal" method="post">
            <div class="form-group row">
              <label for="inputEmail3" class="col-md-3 col-form-label">Training Need</label>
              <div class="col-md-9">
                <input type="text" class="form-control" name="trainingNeeds">
              </div>
            </div>

            <div class="form-group row">
              <label for="inputPassword5" class="col-md-3 col-form-label">Justification</label>
              <div class="col-md-9">
                <textarea class="form-control" rows="5" name="justification"></textarea>
              </div>
            </div>

            <div class="form-group row">
              <label for="inputEmail3" class="col-md-3 col-form-label">Category</label>
              <div class="col-md-9">
                <select class="form-control" name="trainingCat">
                  <option>Acquisition Financial Management</option>
                  <option>Budget Calculation</option>
                  <option>Contact Management</option>
                  <option>Financial Budget and Program Analysis</option>
                  <option>Administrative Support</option>
                  <option>Internal Resource Management</option>
                  <option>Occupational Health and Safety Knowledge</option>
                  <option>Process Management</option>
                  <option>Ethics Knowledge</option>
                  <option>Performance Management for Human Resource Professionals</option>
                </select>
              </div>
            </div>

            <div class="form-group mb-0 justify-content-end row">
              <div class="col-md-9">
                <input type="submit" name="submit" value="Submit" class="btn btn-info waves-effect waves-light">
              </div>
            </div>
          </form>

        </div>
      </div>
    </div>
  </div>

</div>
