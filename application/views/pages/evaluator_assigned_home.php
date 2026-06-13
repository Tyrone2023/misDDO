<?php
$jobTypes = $jobTypes ?? [];
function h($v){return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');}
?>
<div class="content-page">
  <div class="content">
    <div class="container-fluid">

      <div class="row mb-3">
        <div class="col-12">
          <div class="page-title-box">
            <h4 class="page-title">Assigned Applicants Dashboard</h4>
            <p class="text-muted mb-0">Evaluator view &middot; counts are based on your assigned applicants</p>
          </div>
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-md-4">
          <div class="card">
            <div class="card-body">
              <h5 class="text-muted">Total Assigned</h5>
              <h2 class="mb-0"><?= (int)($counts['total'] ?? 0); ?></h2>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card">
            <div class="card-body">
              <h5 class="text-muted">Pending to Rate</h5>
              <h2 class="mb-0 text-warning"><?= (int)($counts['pending'] ?? 0); ?></h2>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card">
            <div class="card-body">
              <h5 class="text-muted">With Scores</h5>
              <h2 class="mb-0 text-success"><?= (int)($counts['scored'] ?? 0); ?></h2>
            </div>
          </div>
        </div>
      </div>

      <div class="row mb-4">
        <div class="col-md-6">
          <a class="btn btn-primary btn-block" href="<?= base_url('EvaluatorAssigned/list'); ?>">View Assigned Applicants</a>
        </div>
        <div class="col-md-6">
          <a class="btn btn-outline-secondary btn-block" href="<?= base_url(); ?>Page/jobVacancy" target="_blank">Job Vacancies</a>
        </div>
      </div>

      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-body table-responsive">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <h4 class="header-title mb-0">Recently Assigned</h4>
                <small class="text-muted">Most recent 5 assignments</small>
              </div>
              <table class="table table-striped table-sm">
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>Record #</th>
                    <th>Position</th>
                    <th>Assigned</th>
                    <th style="width:90px">Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if(!empty($recent)): ?>
                    <?php foreach($recent as $row): ?>
                      <?php
                        $name = trim(($row->LastName ?? $row->last_name ?? '').', '.($row->FirstName ?? $row->first_name ?? '').' '.($row->MiddleName ?? $row->middle_name ?? ''));
                        $record = $row->record_no ?? $row->applicant_id;
                        $pos = ($row->jobTitle ?? '');
                        $lvl = $jobTypes[$row->job_type] ?? '';
                        $pre_school = $row->pre_school ?? '';
                        $appID = $row->appID ?? $row->app_id ?? '';
                        $link = base_url('Pages/ma/'.$record.'/'.$row->job_id.'/'.$pre_school.'/'.$appID.'/'.$record);
                      ?>
                      <tr>
                        <td><?= h($name); ?></td>
                        <td><?= h($record); ?></td>
                        <td><?= h($pos.' '.$lvl); ?></td>
                        <td><?= h(date('M d, Y', strtotime($row->assigned_at ?? ''))); ?></td>
                        <td><a class="btn btn-primary btn-sm" target="_blank" href="<?= $link; ?>">Open</a></td>
                      </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr><td colspan="5" class="text-center text-muted">No assignments yet.</td></tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>
