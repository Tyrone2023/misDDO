<?php
$jobTypes = [
    1  => 'Elementary',
    2  => 'Secondary',
    3  => 'Junior High School',
    4  => 'Senior High School',
    5  => 'Kindergarten',
    6  => 'IPED Elementary',
    7  => 'IPED Secondary',
    8  => 'IPED Junior High School',
    9  => 'IPED IPED Senior High School',
    10 => 'SNED',
];

function h($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
?>

<div class="content-page">
  <div class="content">
    <div class="container-fluid">

      <div class="row">
        <div class="col-12">
          <div class="page-title-box">
            <h4 class="page-title">Assign Applicants to Rater</h4>
            <p class="text-muted mb-0">FY <?= h($fy); ?> &nbsp;|&nbsp; Only Evaluators (egroup 1) appear in the rater list.</p>
          </div>
        </div>
      </div>

      <?php if($this->session->flashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <?= h($this->session->flashdata('success')); ?>
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
      <?php endif; ?>

      <?php if($this->session->flashdata('danger')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <?= h($this->session->flashdata('danger')); ?>
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
      <?php endif; ?>

      <div class="row">
        <div class="col-lg-8">
          <div class="card">
            <div class="card-body table-responsive">
              <h4 class="header-title mb-3">Level Summary</h4>
              <table class="table table-bordered mb-0">
                <thead>
                  <tr>
                    <th style="width:45%">Level</th>
                    <th style="width:15%" class="text-center">Available</th>
                    <th style="width:15%" class="text-center">Assigned</th>
                    <th style="width:25%" class="text-center">Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (!empty($levels)): ?>
                    <?php foreach ($levels as $row): ?>
                      <?php
                        $label = $jobTypes[$row['job_type']] ?? ('Job Type '.$row['job_type']);
                        $available = (int)$row['available'];
                        $assigned  = (int)$row['assigned'];
                      ?>
                      <tr>
                        <td><?= h($label); ?></td>
                        <td class="text-center"><span class="badge badge-primary"><?= $available; ?></span></td>
                        <td class="text-center"><span class="badge badge-info"><?= $assigned; ?></span></td>
                        <td class="text-center">
                          <button
                            type="button"
                            class="btn btn-sm btn-success assign-btn"
                            data-job_type="<?= (int)$row['job_type']; ?>"
                            data-label="<?= h($label); ?>"
                            data-available="<?= $available; ?>"
                            data-spec=""
                            <?= ($available <= 0 ? 'disabled' : ''); ?>
                          >Assign</button>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr><td colspan="4" class="text-center text-muted">No pending applicants found.</td></tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div class="col-lg-4">
          <div class="card">
            <div class="card-body table-responsive">
              <h4 class="header-title mb-3">Rater Load (FY <?= h($fy); ?>)</h4>
              <table class="table table-sm table-hover mb-0">
                <thead>
                  <tr>
                    <th>Name</th>
                    <th class="text-center">Assigned</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if(!empty($rater_load)): ?>
                    <?php foreach($rater_load as $r): ?>
                      <tr>
                        <td><?= h(trim($r->lname . ', ' . $r->fname . ' ' . ($r->mname ?? ''))); ?> <span class="text-muted small">(<?= h($r->username ?? ''); ?>)</span></td>
                        <td class="text-center"><span class="badge badge-secondary"><?= (int)$r->assigned_total; ?></span></td>
                      </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr><td colspan="2" class="text-center text-muted">No raters found.</td></tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-body table-responsive">
              <div class="d-flex align-items-center mb-3">
                <h4 class="header-title mb-0">Senior High School Breakdown</h4>
                <span class="text-muted ml-2">Grouped by specialization</span>
              </div>
              <table class="table table-bordered mb-0">
                <thead>
                  <tr>
                    <th style="width:45%">Specialization</th>
                    <th style="width:15%" class="text-center">Available</th>
                    <th style="width:15%" class="text-center">Assigned</th>
                    <th style="width:25%" class="text-center">Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if(!empty($specializations)): ?>
                    <?php foreach($specializations as $row): ?>
                      <?php
                        $specLabel = ($row['specialization'] === '' ? 'Unspecified' : $row['specialization']);
                        $available = (int)$row['available'];
                        $assigned  = (int)$row['assigned'];
                        $jt        = (int)$row['job_type'];
                      ?>
                      <tr>
                        <td><?= h($specLabel); ?></td>
                        <td class="text-center"><span class="badge badge-primary"><?= $available; ?></span></td>
                        <td class="text-center"><span class="badge badge-info"><?= $assigned; ?></span></td>
                        <td class="text-center">
                          <button
                            type="button"
                            class="btn btn-sm btn-success assign-btn"
                            data-job_type="<?= $jt; ?>"
                            data-label="<?= h($jobTypes[$jt] ?? 'Senior High School'); ?>"
                            data-available="<?= $available; ?>"
                            data-spec="<?= h($row['specialization']); ?>"
                            <?= ($available <= 0 ? 'disabled' : ''); ?>
                          >Assign</button>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr><td colspan="4" class="text-center text-muted">No SHS applicants pending assignment.</td></tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-body table-responsive">
              <h4 class="header-title mb-3">Recent Assignments</h4>
              <table class="table table-sm table-striped mb-0">
                <thead>
                  <tr>
                    <th>Date</th>
                    <th>Rater</th>
                    <th>Level</th>
                    <th>Specialization</th>
                    <th>Job Title</th>
                    <th>Applicant ID</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if(!empty($recent)): ?>
                    <?php foreach($recent as $row): ?>
                      <tr>
                        <td><?= h(date('M d, Y h:i A', strtotime($row->assigned_at ?? ''))); ?></td>
                        <td><?= h($row->rater_name ?? ''); ?></td>
                        <td><?= h($jobTypes[$row->job_type] ?? ('Job Type '.$row->job_type)); ?></td>
                        <td><?= h($row->specialization ?? ''); ?></td>
                        <td><?= h($row->jobTitle ?? ''); ?></td>
                        <td><?= h($row->applicant_id ?? ''); ?></td>
                      </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr><td colspan="6" class="text-center text-muted">No assignments yet.</td></tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div> -->

    </div>
  </div>
</div>

<!-- Assign Modal -->
<div class="modal fade" id="assignModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <form method="post" action="<?= base_url('AssignRater/assign'); ?>" id="assignForm">
        <div class="modal-header">
          <h5 class="modal-title">Assign Applicants</h5>

          <!-- Bootstrap 4 close -->
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>

          <!-- Bootstrap 5 close (won't hurt BS4) -->
          <button type="button" class="btn-close d-none" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <input type="hidden" name="job_type" id="assign-job-type" value="">
          <input type="hidden" name="specialization" id="assign-spec" value="">

          <div class="form-group">
            <label class="mb-1">Level</label>
            <div class="form-control" id="assign-level-label" style="background:#f8f9fa;">&nbsp;</div>
          </div>

          <div class="form-group" id="spec-group" style="display:none;">
            <label class="mb-1">Specialization</label>
            <div class="form-control" id="assign-spec-label" style="background:#f8f9fa;">&nbsp;</div>
          </div>

          <div class="form-group">
            <label for="rater_id">Select Rater</label>
            <select name="rater_id" id="rater_id" class="form-control" required>
              <option value="">-- choose evaluator --</option>
              <?php foreach($raters as $r): ?>
                <option value="<?= (int)$r->id; ?>">
                  <?= h(trim($r->lname . ', ' . $r->fname . ' ' . ($r->mname ?? ''))); ?>
                </option>
              <?php endforeach; ?>
            </select>
            <small class="text-muted">Only users with position "Evaluator" and egroup = 1 are shown.</small>
          </div>

          <div class="form-group">
            <label for="assign-count">How many applicants to assign?</label>
            <input type="number" min="1" step="1" class="form-control" name="count" id="assign-count" value="1" required>
            <small class="text-muted">Available for this bucket: <span id="assign-available">0</span></small>
          </div>
        </div>

        <div class="modal-footer">
          <!-- Bootstrap 4 cancel -->
          <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
          <!-- Bootstrap 5 cancel -->
          <button type="button" class="btn btn-light d-none" data-bs-dismiss="modal">Cancel</button>

          <button type="submit" class="btn btn-primary">Save Assignment</button>
        </div>
      </form>

    </div>
  </div>
</div>

<style>
  /* GUARANTEED modal visibility (works with or without Bootstrap JS/jQuery) */
  #assignModal {
    display: none;
    position: fixed;
    inset: 0;
    z-index: 99999;
    overflow: auto;
    padding: 20px 12px;
    background: rgba(0,0,0,0.55);
  }
  #assignModal.force-show { display: block !important; }

  /* Keep bootstrap modal layout even without bootstrap JS */
  #assignModal .modal-dialog {
    margin: 40px auto;
    max-width: 520px;
    width: 100%;
  }
  #assignModal .modal-content {
    border-radius: 12px;
    box-shadow: 0 20px 70px rgba(0,0,0,0.35);
  }

  /* Prevent fade/transform issues if bootstrap adds .fade */
  #assignModal.fade,
  #assignModal.fade .modal-dialog {
    opacity: 1 !important;
    transform: none !important;
    transition: none !important;
  }

  body.modal-locked { overflow: hidden !important; }
</style>

<script>
(function(){
  var modal = document.getElementById('assignModal');
  if (!modal) return;

  // Ensure modal is directly under <body>
  if (modal.parentElement !== document.body) document.body.appendChild(modal);

  function clamp(n, min, max){
    n = parseInt(n, 10);
    if (isNaN(n)) n = min;
    return Math.max(min, Math.min(max, n));
  }

  function openModal(){
    // Guaranteed show
    modal.classList.add('force-show');
    modal.classList.add('show');
    modal.style.display = 'block';
    modal.removeAttribute('aria-hidden');
    modal.setAttribute('aria-modal', 'true');
    modal.setAttribute('role', 'dialog');
    document.body.classList.add('modal-locked');

    // focus first input/select
    setTimeout(function(){
      var focusEl = modal.querySelector('#rater_id') || modal.querySelector('input, select, button');
      if (focusEl) focusEl.focus();
    }, 50);
  }

  function closeModal(){
    modal.classList.remove('force-show');
    modal.classList.remove('show');
    modal.style.display = 'none';
    modal.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('modal-locked');
  }

  // close buttons (BS4 + BS5)
  modal.querySelectorAll('[data-dismiss="modal"], [data-bs-dismiss="modal"], .close, .btn-close')
    .forEach(function(btn){
      btn.addEventListener('click', function(e){
        e.preventDefault();
        closeModal();
      });
    });

  // outside click closes
  modal.addEventListener('click', function(e){
    var content = modal.querySelector('.modal-content');
    if (content && !content.contains(e.target)) closeModal();
  });

  // ESC closes
  document.addEventListener('keydown', function(e){
    if (e.key === 'Escape' && modal.classList.contains('force-show')) closeModal();
  });

  // Assign button click (delegation supports DataTables redraw)
  document.addEventListener('click', function(e){
    var btn = e.target.closest('.assign-btn');
    if (!btn) return;

    e.preventDefault();

    var jobType   = btn.getAttribute('data-job_type') || '';
    var label     = btn.getAttribute('data-label') || '';
    var available = parseInt(btn.getAttribute('data-available') || '0', 10) || 0;
    var spec      = btn.getAttribute('data-spec') || '';

    document.getElementById('assign-job-type').value = jobType;
    document.getElementById('assign-level-label').textContent = label;

    var countEl = document.getElementById('assign-count');
    var max = (available > 0 ? available : 1);
    countEl.setAttribute('max', String(max));
    countEl.value = String(clamp(1, 1, max));

    document.getElementById('assign-available').textContent = String(available);

    document.getElementById('assign-spec').value = spec;

    var specGroup = document.getElementById('spec-group');
    var specLabel = document.getElementById('assign-spec-label');

    if (spec !== '') {
      specGroup.style.display = '';
      specLabel.textContent = spec || 'Unspecified';
    } else {
      specGroup.style.display = 'none';
      specLabel.textContent = '';
    }

    openModal(); // GUARANTEED
  });

  // Clamp count before submit
  var form = document.getElementById('assignForm');
  if (form) {
    form.addEventListener('submit', function(){
      var available = parseInt(document.getElementById('assign-available').textContent || '0', 10) || 0;
      var max = (available > 0 ? available : 1);
      var countEl = document.getElementById('assign-count');
      countEl.value = String(clamp(countEl.value, 1, max));
    });
  }

  // Optional debug close
  window.__assignModalClose = closeModal;
})();
</script>
