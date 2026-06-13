<?php 
    $jobTypes = $jobTypes ?? [
        1 => '- Elementary',
        2 => '- Secondary',
        3 => '- Junior High School',
        4 => '- Senior High School',
        5 => '- Kindergarten',
        6 => '- IPED Elementary',
        7 => '- IPED Secondary',
        8 => '- IPED Junior High School',
        9 => '- IPED Senior High School',
        10 => '- SNED',
    ];

    $selectedFy = $selected_fy ?? date('Y');
    $selectedSourceFy = $selected_source_fy ?? ($selectedFy - 1);
    $fyOptions = $fy_options ?? [];
    if (empty($fyOptions)) {
        $fyOptions = [$selectedFy];
    }
    if (!in_array($selectedSourceFy, $fyOptions, true)) {
        $selectedSourceFy = $selectedFy;
    }
?>

<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="page-title mb-0"><?= $title ?? 'Approved Retentions With Placeholder Ratings'; ?></h4>
                        <a href="<?= base_url('RatingBatch'); ?>" class="btn btn-outline-secondary btn-sm">Back to Rating Batch</a>
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
            <?php endif; ?>

            <div class="row mb-3">
                <div class="col-12">
                    <div class="d-flex flex-wrap align-items-center">
                        <form class="form-inline mr-3" method="get" action="<?= base_url('RatingBatch/retention_placeholders'); ?>">
                            <label class="mr-2 mb-0 text-muted">Target FY</label>
                            <select name="fy" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                                <?php foreach($fyOptions as $year): ?>
                                    <option value="<?= $year; ?>" <?= ((int)$selectedFy === (int)$year) ? 'selected' : ''; ?>><?= $year; ?></option>
                                <?php endforeach; ?>
                            </select>

                            <label class="mr-2 mb-0 text-muted">Use ratings from</label>
                            <select name="source_fy" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                                <?php foreach($fyOptions as $year): ?>
                                    <option value="<?= $year; ?>" <?= ((int)$selectedSourceFy === (int)$year) ? 'selected' : ''; ?>><?= $year; ?></option>
                                <?php endforeach; ?>
                            </select>

                            <noscript><button type="submit" class="btn btn-sm btn-outline-primary">Apply</button></noscript>
                        </form>

                        <form class="form-inline mr-3" method="post" action="<?= base_url('RatingBatch/update_placeholder_ratings'); ?>">
                            <input type="hidden" name="fy" value="<?= $selectedFy; ?>">
                            <input type="hidden" name="source_fy" value="<?= $selectedSourceFy; ?>">
                            <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Add/Update ratings for FY <?= $selectedFy; ?> using source FY <?= $selectedSourceFy; ?>?');">
                                Add / Update Ratings
                            </button>
                        </form>

                        <span class="text-muted small">Target FY <?= $selectedFy; ?> · Source FY <?= $selectedSourceFy; ?>.</span>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body table-responsive">
                            <p class="text-muted mb-3">
                                Shows approved retention requests (All Criteria or Demo/TR) where retained components are still 0 or 0.00001.
                            </p>
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
                                        <tr><td colspan="9" class="text-center text-muted">No approved retention requests with placeholder ratings found.</td></tr>
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
                                        } else { // r_type == 2
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
