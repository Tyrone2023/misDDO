            <?php 
                $jobTypes = [
                    1 => '- Elementary',
                    2 => '- Secondary',
                    3 => '- Junior High School',
                    4 => '- Senior High School',
                    5 => '- kindergarten',
                    6 => '- IPED Elementary',
                    7 => '- IPED Secondary',
                    8 => '- IPED Junior High School',
                    9 => '- IPED Senior High School',
                    10 => '- SNED',
                ];
            ?>

            <div class="content-page">
                <div class="content">

                    <div class="container-fluid">

                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box">
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>

                        <?php if($this->session->flashdata('success')) : ?>
                            <?= '<div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>'
                                    .$this->session->flashdata('success'). 
                                '</div>'; 
                            ?>
                        <?php endif; ?>

                        <?php if($this->session->flashdata('danger')) : ?>
                            <?= '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>'
                                    .$this->session->flashdata('danger'). 
                                '</div>'; 
                            ?>
                        <?php endif;  ?>

                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                <div class="d-flex flex-wrap gap-2">
                                            <?php foreach($level_buttons as $key => $cfg): ?>
                                                <a href="<?= base_url('RatingBatch/accept_level/'.$cfg['job_type']); ?>" class="btn <?= $cfg['class']; ?> waves-effect waves-light mb-2 mr-1">
                                                    <?= $cfg['label']; ?>
                                                </a>
                                            <?php endforeach; ?>
                                                <a href="<?= base_url('RatingBatch/pending_report'); ?>" class="btn btn-outline-secondary waves-effect waves-light mb-2 mr-1">
                                                    View Pending Report
                                                </a>
                                                <a href="<?= base_url('RatingBatch/retention_placeholders'); ?>" class="btn btn-outline-danger waves-effect waves-light mb-2 mr-1">
                                                    View Placeholder Retentions
                                                </a>
                                        </div>
                                        <small class="text-muted">Only requests with a previous rating in the same level will be accepted; others stay pending.</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body table-responsive">
                                        <h4 class="header-title mb-4">Pending Requests</h4>
                                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th>Applicant No.</th>
                                                    <th>Name</th>
                                                    <th>Position Applied</th>
                                                    <th>Date Submitted</th>
                                                    <th>Application SY</th>
                                                    <th>Request Type</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach($data as $row){ 
                                                    // tolerant profile lookup
                                                    $a = $this->Common->one_cond_row('hris_applicant','id',$row->applicant_id);
                                                    if(empty($a)){
                                                        $a = $this->Common->one_cond_row('hris_applicant','record_no',$row->applicant_id);
                                                    }
                                                    if(!empty($a)){
                                                        $page = 'ma';
                                                        $id_no = $a->id;
                                                        $record_no = $a->record_no;
                                                    }else{
                                                        $a = $this->Common->one_cond_row('hris_staff','IDNumber',$row->applicant_id);
                                                        $page = 'ma_staff';
                                                        $id_no = $a->IDNumber ?? $row->applicant_id;
                                                        $record_no = $a->IDNumber ?? $row->applicant_id;
                                                    }
                                                    $job = $this->Common->one_cond_row('hris_jobvacancy','jobID',$row->job_id);
                                                    $app = null;
                                                    if(!empty($row->app_id)){
                                                        $app = $this->Common->one_cond_row('hris_applications','appID', $row->app_id);
                                                    }
                                                    $pre_school = (!empty($app) && isset($app->pre_school)) ? $app->pre_school : '';
                                                ?>
                                                    <tr>
                                                        <td><a target="_blank" href="<?= base_url(); ?>Pages/<?= $page; ?>/<?= $id_no; ?>/<?= $job->jobID; ?>/<?= $pre_school; ?>"><?php echo strtoupper($record_no); ?></a></td>
                                                        <td><?= $a->LastName ?? ''; ?>, <?= $a->FirstName ?? ''; ?> <?= $a->MiddleName ?? ''; ?></td>
                                                        <td><?= $job->jobTitle; ?> <?=  $jobTypes[$job->job_type] ?? ''; ?></td>    
                                                        <td><?= $row->rdate; ?></td>
                                                        <td><?= $job->sy; ?></td>
                                                        <td><?php if($row->r_type == 1){echo "<span class='badge badge-warning'>Retention of Rating";}else{echo "<span class='badge badge-purple'>Update Credentials";} ?></span></td>
                                                    </tr>
                                                <?php } ?>
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
                                        <h4 class="header-title mb-4">Retained Requests</h4>
                                        <table class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Applicant No.</th>
                                                    <th>Name</th>
                                                    <th>Position Applied</th>
                                                    <th>Date Submitted</th>
                                                    <th>Request Type</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $c=1; foreach($granted as $row){ 
                                                    $jobs = $this->Common->one_cond_row('hris_jobvacancy','jobID',$row->job_id);
                                                    if($jobs->jvStatus == "Open"){
                                                        // tolerant profile lookup
                                                        $a = $this->Common->one_cond_row('hris_applicant','id',$row->applicant_id);
                                                        if(empty($a)){
                                                            $a = $this->Common->one_cond_row('hris_applicant','record_no',$row->applicant_id);
                                                        }
                                                        if(!empty($a)){
                                                            $page = 'ma';
                                                            $id_no = $a->id;
                                                            $record_no = $a->record_no;
                                                        }else{
                                                            $a = $this->Common->one_cond_row('hris_staff','IDNumber',$row->applicant_id);
                                                            $page = 'ma_staff';
                                                            $id_no = $a->IDNumber ?? $row->applicant_id;
                                                            $record_no = $a->IDNumber ?? $row->applicant_id;
                                                        }
                                                        $job = $this->Common->one_cond_row('hris_jobvacancy','jobID',$row->job_id);
                                                        $app = null;
                                                        if(!empty($row->app_id)){
                                                            $app = $this->Common->one_cond_row('hris_applications','appID', $row->app_id);
                                                        }
                                                        $pre_school = (!empty($app) && isset($app->pre_school)) ? $app->pre_school : '';
                                                ?>
                                                    <tr>
                                                        <td><?= $c++; ?></td>
                                                        <td><a target="_blank" href="<?= base_url(); ?>Pages/<?= $page; ?>/<?= $id_no; ?>/<?= $job->jobID; ?>/<?= $pre_school; ?>"><?php echo strtoupper($record_no); ?></a></td>
                                                        <td><?= $a->LastName ?? ''; ?>, <?= $a->FirstName ?? ''; ?> <?= $a->MiddleName ?? ''; ?></td>
                                                        <td><?= $job->jobTitle; ?> <?=  $jobTypes[$job->job_type] ?? ''; ?></td>    
                                                        <td><?= $row->rdate; ?></td>
                                                        <td><?php if($row->r_type == 1){echo "<span class='badge badge-warning'>Retention of Rating";}else{echo "<span class='badge badge-purple'>Update Credentials";} ?></span></td>
                                                    </tr>
                                                <?php } } ?>
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
                                        <h4 class="header-title mb-3">Approved Retentions With Placeholder Ratings</h4>
                                        <p class="text-muted mb-3">Approved retention requests where retained components are still 0 or 0.00001.</p>
                                        <table class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Applicant No.</th>
                                                    <th>Name</th>
                                                    <th>Position Applied</th>
                                                    <th>Request Type</th>
                                                    <th>Status</th>
                                                    <th>Rating FY</th>
                                                    <th>Zero / Placeholder Fields</th>
                                                    <th>Date Submitted</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (empty($zero_retention)) : ?>
                                                    <tr><td colspan="9" class="text-center text-muted">No retention requests with placeholder ratings found.</td></tr>
                                                <?php else: $i = 1; foreach($zero_retention as $row){
                                                    // tolerant profile lookup
                                                    $a = $this->Common->one_cond_row('hris_applicant','id',$row->applicant_id);
                                                    if(empty($a)){
                                                        $a = $this->Common->one_cond_row('hris_applicant','record_no',$row->applicant_id);
                                                    }
                                                    if(!empty($a)){
                                                        $page = 'ma';
                                                        $id_no = $a->id;
                                                        $record_no = $a->record_no;
                                                    }else{
                                                        $a = $this->Common->one_cond_row('hris_staff','IDNumber',$row->applicant_id);
                                                        $page = 'ma_staff';
                                                        $id_no = $a->IDNumber ?? $row->applicant_id;
                                                        $record_no = $a->IDNumber ?? $row->applicant_id;
                                                    }
                                                    $job = $this->Common->one_cond_row('hris_jobvacancy','jobID',$row->job_id);
                                                    $app = null;
                                                    if(!empty($row->app_id)){
                                                        $app = $this->Common->one_cond_row('hris_applications','appID', $row->app_id);
                                                    }
                                                    $pre_school = (!empty($app) && isset($app->pre_school)) ? $app->pre_school : '';

                                                    // Only flag fields that should be retained for the request type
                                                    if ($row->r_type == 1) {
                                                        $fieldsMap = [
                                                            'education' => 'Education',
                                                            'training' => 'Training',
                                                            'experience' => 'Experience',
                                                            'let_rating' => 'LET',
                                                            'demo_rating' => 'Demo',
                                                            'tr_rating' => 'TR',
                                                            'total_points' => 'Total Points'
                                                        ];
                                                    } else { // r_type == 2, Demo & TR retention
                                                        $fieldsMap = [
                                                            'let_rating' => 'LET',
                                                            'demo_rating' => 'Demo',
                                                            'tr_rating' => 'TR'
                                                        ];
                                                    }
                                                    $zeroFields = [];
                                                    foreach ($fieldsMap as $key => $label) {
                                                        $val = isset($row->$key) ? (float)$row->$key : null;
                                                        if ($val === 0.0 || $val === 0.00001 || $val === 0.0001) {
                                                            $zeroFields[] = $label;
                                                        }
                                                    }
                                                    $zeroText = empty($zeroFields) ? '-' : implode(', ', $zeroFields);

                                                    $reqLabel = ($row->r_type == 1) ? 'Retention: All Criteria' : 'Retention: Demo & TR';
                                                    $statLabel = ($row->stat == 1) ? 'Granted' : 'Pending';
                                                    $statClass = ($row->stat == 1) ? 'badge-success' : 'badge-secondary';
                                                ?>
                                                    <tr>
                                                        <td><?= $i++; ?></td>
                                                        <td><a target="_blank" href="<?= base_url(); ?>Pages/<?= $page; ?>/<?= $id_no; ?>/<?= $job->jobID; ?>/<?= $pre_school; ?>"><?php echo strtoupper($record_no); ?></a></td>
                                                        <td><?= $a->LastName ?? ''; ?>, <?= $a->FirstName ?? ''; ?> <?= $a->MiddleName ?? ''; ?></td>
                                                        <td><?= $job->jobTitle; ?> <?=  $jobTypes[$job->job_type] ?? ''; ?></td>
                                                        <td><span class="badge badge-warning"><?= $reqLabel; ?></span></td>
                                                        <td><span class="badge <?= $statClass; ?>"><?= $statLabel; ?></span></td>
                                                        <td><?= $row->rating_fy ?? '—'; ?></td>
                                                        <td><?= $zeroText; ?></td>
                                                        <td><?= $row->rdate; ?></td>
                                                    </tr>
                                                <?php } endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
