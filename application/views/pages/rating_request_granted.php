<?php
    // Safe job type labels
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
        10 => '- SNED'
    ];

    // Safe current job display
    $currentJobTitle = !empty($job) ? ($job->jobTitle ?? 'Unknown Position') : 'Unknown Position';
    $currentJobType  = (!empty($job) && isset($job->job_type)) ? ($jobTypes[$job->job_type] ?? '- Non-Teaching / Other') : '';
    $currentJobID    = !empty($job) ? ($job->jobID ?? '') : $this->uri->segment(4);

    // Applications of the applicant
    $application = $this->Common->one_cond('hris_applications', 'applicant_id', $this->uri->segment(5));
?>

<!-- ============================================================== -->
<!-- Start Page Content here -->
<!-- ============================================================== -->
<div class="content-page">
    <div class="content">

        <!-- Start Content-->
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <?php if ($this->session->flashdata('success')) : ?>
                <?= '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>' .
                        $this->session->flashdata('success') .
                    '</div>'; ?>
            <?php endif; ?>

            <?php if ($this->session->flashdata('danger')) : ?>
                <?= '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>' .
                        $this->session->flashdata('danger') .
                    '</div>'; ?>
            <?php endif; ?>

            <?php if (validation_errors()) : ?>
                <div class="mb-2">
                    <?= validation_errors(); ?>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body table-responsive">

                            <h4 class="header-title mb-4">
                                <?= $title; ?><br />
                                <?php if (!empty($job)) : ?>
                                    <span class="float-left badge badge-primary inline mt-2">
                                        <?= $currentJobTitle; ?> <?= $currentJobType; ?>
                                    </span>
                                <?php else : ?>
                                    <span class="float-left badge badge-secondary inline mt-2">
                                        Unknown Position
                                    </span>
                                <?php endif; ?>
                            </h4>
                            <br />

                            <div class="row">
                                <div class="col-lg-8 col-md-10 col-sm-12">
                                    <?php
                                        $attributes = ['class' => 'parsley-examples'];
                                        echo form_open_multipart('pages/request_rating_granted', $attributes);
                                    ?>

                                        <div class="form-group row">
                                            <label class="col-lg-2 col-form-label">Position Applied</label>
                                            <div class="col-lg-10">
                                                <input
                                                    type="text"
                                                    readonly
                                                    class="form-control"
                                                    value="<?= $currentJobTitle . ' ' . $currentJobType; ?>"
                                                >
                                            </div>
                                        </div>

                                        <input type="hidden" name="record_no" value="<?= trim($this->uri->segment(6)); ?>">
                                        <input type="hidden" name="app_id" value="<?= trim($this->uri->segment(7)); ?>">
                                        <input type="hidden" name="id" value="<?= trim($this->uri->segment(3)); ?>">
                                        <input type="hidden" name="r_type" value="<?= trim($this->uri->segment(8)); ?>">
                                        <input type="hidden" name="jobID" value="<?= trim($currentJobID); ?>">

                                        <div class="form-group row">
                                            <label class="col-lg-2 col-form-label">Select Application</label>
                                            <div class="col-lg-10">
                                                <select class="form-control" required name="application">
                                                    <option value="" disabled selected>-- Select application --</option>

                                                    <?php if (!empty($application)) : ?>
                                                        <?php foreach ($application as $row) : ?>
                                                            <?php
                                                                $jh     = $this->Common->one_cond_row('hris_jobvacancy', 'jobID', $row->jobID);
                                                                $rating = $this->Common->one_cond_row('hris_applications_rating', 'appID', $row->appID);
                                                                $hasRate = !empty($rating);

                                                                $jobTitle = !empty($jh) ? ($jh->jobTitle ?? 'Unknown Position') : 'Unknown Position';
                                                                $level    = (!empty($jh) && isset($jh->job_type)) ? ($jobTypes[$jh->job_type] ?? '- Non-Teaching / Other') : '- Non-Teaching / Other';
                                                                $sy       = !empty($jh) ? (!empty($jh->sy) ? $jh->sy : 'N/A') : 'N/A';
                                                            ?>
                                                            <option value="<?= $row->appID; ?>" <?= $hasRate ? '' : 'disabled'; ?>>
                                                                <?= $jobTitle; ?> <?= $level; ?> (<?= $sy; ?>)
                                                                <?= $hasRate ? '' : ' - No prior rating'; ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </select>

                                                <small class="text-muted">
                                                    All past applications are listed. Entries without prior ratings are disabled.
                                                </small>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-lg-2 col-form-label"></label>
                                            <div class="col-lg-10">
                                                <input type="submit" value="Submit" class="btn btn-primary waves-effect waves-light mr-1">
                                                <a href="<?= base_url('Pages/request_rating'); ?>" class="btn btn-secondary waves-effect waves-light">
                                                    Cancel
                                                </a>
                                            </div>
                                        </div>

                                    </form>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <!-- end row -->

        </div>
        <!-- end container-fluid -->

    </div>
    <!-- end content -->
</div>