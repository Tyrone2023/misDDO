<style>
  .ip-card { border: 0; border-radius: 14px; box-shadow: 0 2px 12px rgba(0,0,0,.05); }
  .ip-section-title { font-weight: 600; color: #2f3542; margin-bottom: 4px; }
  .ip-label { font-weight: 600; font-size: 12px; color: #5a6270; text-transform: uppercase; letter-spacing: .03em; }
  .ip-fy-badge { font-size: 13px; padding: 8px 14px; font-weight: 600; border-radius: 999px; }
  .ip-group-title { font-weight: 600; color: #2f3542; margin: 22px 0 14px; font-size: 15px; display: flex; align-items: center; gap: 8px; }
  .ip-group-title:before { content: ""; width: 4px; height: 18px; background: #4b7bec; border-radius: 4px; display: inline-block; }

  .ip-context-bar {
    background: linear-gradient(135deg, #4b7bec 0%, #3867d6 100%);
    color: #fff; border-radius: 14px; padding: 16px 22px; margin-bottom: 6px;
    display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;
  }
  .ip-context-label { display: block; font-size: 11px; text-transform: uppercase; letter-spacing: .05em; opacity: .85; }
  .ip-context-value { font-size: 18px; font-weight: 700; }

  .ip-tile {
    display: flex; align-items: center; gap: 14px; background: #fff;
    border: 1px solid rgba(0,0,0,.06); border-radius: 14px; padding: 16px;
    margin-bottom: 20px; text-decoration: none; transition: .18s;
    position: relative; box-shadow: 0 2px 6px rgba(0,0,0,.04); height: calc(100% - 20px);
  }
  .ip-tile:hover { transform: translateY(-3px); box-shadow: 0 10px 24px rgba(0,0,0,.10); border-color: rgba(75,123,236,.35); text-decoration: none; }
  .ip-tile-icon { width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 25px; flex-shrink: 0; }
  .ip-tile-body { display: flex; flex-direction: column; min-width: 0; }
  .ip-tile-code { font-weight: 700; color: #2f3542; font-size: 14px; }
  .ip-tile-name { font-size: 12.5px; color: #8a94a6; }
  .ip-tile-action { margin-left: auto; color: #c2c8d0; font-size: 18px; align-self: flex-start; }
  .ip-tile-disabled { opacity: .5; cursor: not-allowed; }
  .ip-tile-disabled:hover { transform: none; box-shadow: 0 2px 6px rgba(0,0,0,.04); border-color: rgba(0,0,0,.06); }

  .c-primary   { background: #eef2ff; color: #4b7bec; }
  .c-info      { background: #e7f6fb; color: #39afd1; }
  .c-secondary { background: #eef0f3; color: #6c757d; }
  .c-success   { background: #eafaf1; color: #1e9e63; }
  .c-warning   { background: #fef6e7; color: #e9a23b; }
  .c-purple    { background: #f1ecfb; color: #6b5eae; }
</style>

<div class="content-page">
  <div class="content">
    <div class="container-fluid">

      <!-- Page title -->
      <div class="row">
        <div class="col-12">
          <div class="page-title-box d-flex align-items-center justify-content-between flex-wrap">
            <h4 class="page-title mb-0">Implementation Plans</h4>
            <span class="badge badge-primary ip-fy-badge">
              <i class="mdi mdi-calendar-blank-outline"></i> Fiscal Year <?= htmlspecialchars($fys); ?>
            </span>
          </div>
        </div>
      </div>

      <!-- Flash messages -->
      <?php if ($this->session->flashdata('success')) : ?>
        <?= '<div class="alert alert-success alert-dismissible fade show" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'
            . $this->session->flashdata('success') . '</div>'; ?>
      <?php endif; ?>
      <?php if ($this->session->flashdata('danger')) : ?>
        <?= '<div class="alert alert-danger alert-dismissible fade show" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'
            . $this->session->flashdata('danger') . '</div>'; ?>
      <?php endif; ?>

      <!-- ===== Plan context selector (fiscal year + batch in one view) ===== -->
      <div class="row">
        <div class="col-12">
          <div class="card ip-card">
            <div class="card-body">
              <h5 class="ip-section-title"><i class="mdi mdi-tune-vertical"></i> Plan Context</h5>
              <p class="text-muted mb-4">Pick the fiscal year and allocation batch, then select it. Your selection is remembered until you log out or choose another batch. The fiscal year defaults to the current year and can be switched anytime.</p>

              <?= form_open('Page/implementation_plans', ['id' => 'ipForm']); ?>
                <input type="hidden" name="school_id" value="<?= $this->session->username; ?>">
                <div class="form-row">

                  <div class="form-group col-lg-3 col-md-4">
                    <label class="ip-label">Fiscal Year</label>
                    <select class="form-control" name="fy" onchange="document.getElementById('ipForm').submit();">
                      <?php
                        $yearset = [];
                        if (!empty($years)) {
                          foreach ($years as $y) {
                            if (is_numeric($y->alloc_year)) $yearset[(int)$y->alloc_year] = true;
                          }
                        }
                        $cur = (int)date('Y');
                        for ($i = $cur - 1; $i <= $cur + 5; $i++) { $yearset[$i] = true; }
                        krsort($yearset);
                        foreach (array_keys($yearset) as $yv) {
                          $sel = ((string)$yv === (string)$fys) ? 'selected' : '';
                          echo "<option value='" . $yv . "' " . $sel . ">" . $yv . "</option>";
                        }
                      ?>
                    </select>
                  </div>

                  <div class="form-group col-lg-6 col-md-8">
                    <label class="ip-label">Allocation Batch</label>
                    <select class="form-control" data-toggle="select2" name="code" required>
                      <option></option>
                      <?php if (!empty($ssa)) foreach ($ssa as $row) {
                        $sel = (isset($_SESSION['aip']) && (string)$_SESSION['aip'] === (string)$row->alloc_batch) ? 'selected' : '';
                        echo "<option value='" . $row->alloc_batch . "' " . $sel . ">Batch " . $row->alloc_batch
                           . " &bull; " . $row->alloc_group . " &mdash; PHP " . number_format($row->alloc_amount, 2) . "</option>";
                      } ?>
                    </select>
                    <?php if (empty($ssa)) { ?>
                      <small class="text-danger">No allocation batches found for FY <?= htmlspecialchars($fys); ?>. Try another fiscal year.</small>
                    <?php } ?>
                  </div>

                  <div class="form-group col-lg-3 col-md-12">
                    <label class="ip-label d-none d-lg-block">&nbsp;</label>
                    <button type="submit" name="aip" value="1" class="btn btn-primary btn-block waves-effect waves-light" <?= empty($ssa) ? 'disabled' : ''; ?>>
                      <i class="mdi mdi-check-circle-outline"></i> Select Batch
                    </button>
                  </div>

                </div>
              </form>
            </div>
          </div>
        </div>
      </div>

      <?php if (isset($_SESSION['aip'])) {
        $sap = $this->SGODModel->two_cond_row('sgod_app_percentage', 'b_code', $_SESSION['aip'], 'fy', $_SESSION['fy']);
      ?>

      <!-- Active context -->
      <div class="row">
        <div class="col-12">
          <div class="ip-context-bar">
            <div>
              <span class="ip-context-label">Active Context</span>
              <span class="ip-context-value">Batch <?= htmlspecialchars($_SESSION['aip']); ?> &middot; FY <?= htmlspecialchars($_SESSION['fy']); ?></span>
            </div>
            <a href="<?= base_url(); ?>Page/aip" class="btn btn-light btn-sm">
              <i class="mdi mdi-clipboard-text-outline"></i> Go to AIP Workspace
            </a>
          </div>
        </div>
      </div>

      <!-- Manage plans -->
      <div class="row">
        <div class="col-12"><div class="ip-group-title">Manage Plans</div></div>
        <?php
          $manage = [
            ['AIP',  'Annual Implementation Plan',      'mdi-clipboard-text-outline', 'primary',   base_url() . 'Page/aip'],
            ['SOP',  'School Operational Plan',         'mdi-file-document-outline',  'info',      base_url() . 'Page/sop'],
            ['APP',  'Annual Procurement Plan',         'mdi-cart-outline',           'secondary', base_url() . 'Page/view_app'],
            ['PPMP', 'Project Procurement Mgmt Plan',   'mdi-package-variant-closed', 'success',   base_url() . 'Page/generate_ppmp'],
            ['SMEA', 'Monitoring & Evaluation',         'mdi-chart-box-outline',      'warning',   base_url() . 'Page/smeav2'],
          ];
          foreach ($manage as $m) { ?>
            <div class="col-xl-3 col-md-4 col-sm-6">
              <a href="<?= $m[4]; ?>" class="ip-tile">
                <span class="ip-tile-icon c-<?= $m[3]; ?>"><i class="mdi <?= $m[2]; ?>"></i></span>
                <span class="ip-tile-body">
                  <span class="ip-tile-code"><?= $m[0]; ?></span>
                  <span class="ip-tile-name"><?= $m[1]; ?></span>
                </span>
              </a>
            </div>
        <?php } ?>
        <div class="col-xl-3 col-md-4 col-sm-6">
          <a href="#rca" data-toggle="modal" class="ip-tile">
            <span class="ip-tile-icon c-purple"><i class="mdi mdi-cash-multiple"></i></span>
            <span class="ip-tile-body">
              <span class="ip-tile-code">RCA</span>
              <span class="ip-tile-name">Request for Cash Advance</span>
            </span>
          </a>
        </div>
      </div>

      <!-- Generated documents -->
      <div class="row">
        <div class="col-12"><div class="ip-group-title">Generated Documents</div></div>
        <?php
          $docs = [
            ['AIP',  'Annual Implementation Plan',    'primary',   base_url() . 'Page/generate_aip',  true],
            ['SOP',  'School Operational Plan',       'info',      base_url() . 'Page/generate_sop',  true],
            ['APP',  'Annual Procurement Plan',       'secondary', base_url() . 'Page/generate_app',  isset($sap->id)],
            ['PPMP', 'Project Procurement Mgmt Plan', 'success',   base_url() . 'Page/generate_ppmp', true],
          ];
          foreach ($docs as $d) { ?>
            <div class="col-xl-3 col-md-4 col-sm-6">
              <?php if ($d[4]) { ?>
                <a href="<?= $d[3]; ?>" target="_blank" class="ip-tile">
                  <span class="ip-tile-icon c-<?= $d[2]; ?>"><i class="mdi mdi-file-pdf-box"></i></span>
                  <span class="ip-tile-body">
                    <span class="ip-tile-code"><?= $d[0]; ?></span>
                    <span class="ip-tile-name"><?= $d[1]; ?></span>
                  </span>
                  <span class="ip-tile-action"><i class="mdi mdi-open-in-new"></i></span>
                </a>
              <?php } else { ?>
                <span class="ip-tile ip-tile-disabled">
                  <span class="ip-tile-icon c-<?= $d[2]; ?>"><i class="mdi mdi-file-pdf-box"></i></span>
                  <span class="ip-tile-body">
                    <span class="ip-tile-code"><?= $d[0]; ?></span>
                    <span class="ip-tile-name">Not yet available</span>
                  </span>
                </span>
              <?php } ?>
            </div>
        <?php } ?>
      </div>

      <?php } ?>

    </div>
    <!-- end container-fluid -->
  </div>
  <!-- end content -->
</div>

<!-- ===== RCA month picker modal ===== -->
<div id="rca" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="rcaLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="rcaLabel">Request for Cash Advance</h5>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      </div>
      <div class="modal-body">
        <?= form_open('Page/generate_rca', ['target' => '_blank']); ?>
          <div class="form-group">
            <label>Select Month</label>
            <select class="form-control" name="month" required>
              <option value=""></option>
              <?php
                $month = ['January' => 'jan', 'February' => 'feb', 'March' => 'mar', 'April' => 'april', 'May' => 'may', 'June' => 'june', 'July' => 'july', 'August' => 'aug', 'September' => 'sept', 'October' => 'oct', 'November' => 'nov', 'December' => 'ddec'];
                foreach ($month as $m => $val) {
                  echo "<option value='" . $val . "'>" . $m . "</option>";
                }
              ?>
            </select>
          </div>
          <div class="modal-footer px-0">
            <button type="submit" name="aip" class="btn btn-primary waves-effect waves-light">
              <i class="mdi mdi-file-pdf-box"></i> Generate
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
