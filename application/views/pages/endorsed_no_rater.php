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
    9  => 'IPED Senior High School',
    10 => 'SNED',
];
function h($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function fmtDate($dt){ if(!$dt) return ''; $ts=strtotime($dt); return $ts?date('M d, Y', $ts):''; }
?>

<div class="content-page">
  <div class="content">
    <div class="container-fluid">

      <div class="row">
        <div class="col-12">
          <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="page-title mb-0">Endorsed Applicants (No Evaluator Assigned)</h4>
            <small class="text-muted">Current FY: <?= date('Y'); ?></small>
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

      <?php if(empty($grouped)): ?>
        <div class="alert alert-info">All endorsed applicants already have evaluators assigned.</div>
      <?php endif; ?>

      <div id="accordion">
        <?php foreach($grouped as $jt => $rows): ?>
          <?php $label = $jobTypes[$jt] ?? ('Job Type '.$jt); ?>
          <div class="card mb-2">
            <div class="card-header" id="heading-<?= (int)$jt; ?>">
              <h5 class="mb-0">
                <button class="btn btn-link" data-toggle="collapse" data-target="#collapse-<?= (int)$jt; ?>" aria-expanded="true" aria-controls="collapse-<?= (int)$jt; ?>">
                  <?= h($label); ?> <span class="badge badge-primary ml-2"><?= count($rows); ?></span>
                </button>
              </h5>
            </div>

            <div id="collapse-<?= (int)$jt; ?>" class="collapse" aria-labelledby="heading-<?= (int)$jt; ?>" data-parent="#accordion">
              <div class="card-body table-responsive">
                <table class="table table-sm table-striped mb-0">
                  <thead>
                    <tr>
                      <th>Name</th>
                      <th>Record #</th>
                      <th>Position</th>
                      <th>District</th>
                      <th>Endorsed Date</th>
                      <th style="width:120px">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach($rows as $r): ?>
                      <?php
                        $name = trim(($r->LastName ?? '').', '.($r->FirstName ?? '').' '.($r->MiddleName ?? ''));
                        $record = $r->record_no ?? $r->rec_no ?? '';
                        $jobTitle = $r->jobTitle ?? '';
                        $district = $r->district ?? '';
                        $pre_school = $r->pre_school ?? '';
                        $appID = $r->appID ?? '';
                        $job_id = $r->jobID ?? '';
                        $link = base_url('Pages/ma/'.$record.'/'.$job_id.'/'.$pre_school.'/'.$appID.'/'.$record);
                        $endorsedDate = $r->assigned_date ?? $r->app_date ?? '';
                      ?>
                      <tr>
                        <td><?= h($name); ?></td>
                        <td><?= h($record); ?></td>
                        <td><?= h($jobTitle); ?></td>
                        <td><?= h($district); ?></td>
                        <td><?= h(fmtDate($endorsedDate)); ?></td>
                        <td><a class="btn btn-primary btn-sm" target="_blank" href="<?= h($link); ?>">Open</a></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

    </div>
  </div>
</div>

<!-- jQuery and Bootstrap assumed already loaded globally -->
