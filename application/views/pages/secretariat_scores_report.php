<?php
if (!function_exists('h')) {
    function h($v)
    {
        return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('score_report_value')) {
    function score_report_value($v)
    {
        if ($v === null || $v === '' || (float) $v == 0.00001 || (float) $v == 0.0001) {
            return '';
        }

        return h($v);
    }
}

$jtLabels = $jobTypeLabels ?? [];
$jobTypeSuffixes = [
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
$assigned = $assignedJobTypes ?? [];
$jobsByType = [];
foreach (($jobOptions ?? []) as $job) {
    $jobType = (int) $job->job_type;
    if ($jobType === 0) {
        continue;
    }
    $jobsByType[$jobType][] = $job;
}
$hasOpenJobs = !empty($jobsByType);
?>

<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="page-title mb-0"><?= h($title ?? 'Scores Report'); ?></h4>
                    </div>
                </div>
            </div>

            <?php if (!empty($filterError)) : ?>
                <div class="alert alert-danger">
                    <?= h($filterError); ?>
                </div>
            <?php endif; ?>

            <?php if (!$hasOpenJobs) : ?>
                <div class="alert alert-info">
                    No open positions found.
                </div>
            <?php endif; ?>

            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="header-title">Report Filter</h5>
                    <form method="get" action="<?= base_url('Pages/secretariat_scores_report'); ?>">
                        <div class="form-row">
                            <div class="form-group col-lg-4 col-md-4">
                                <label for="score-report-filter">Job Title / Job Type</label>
                                <select
                                    name="filter"
                                    id="score-report-filter"
                                    class="form-control"
                                    <?= !$hasOpenJobs ? 'disabled' : ''; ?>
                                >
                                    <option value="">Select open job title or job type</option>

                                    <?php foreach ($jobsByType as $jobType => $jobs) : ?>
                                        <?php foreach ($jobs as $job) : ?>
                                            <?php
                                                $value = 'job:' . (int) $job->jobID;
                                                $typeSuffix = $jobTypeSuffixes[(int) $job->job_type] ?? ('- Job Type ' . (int) $job->job_type);
                                            ?>
                                            <option value="<?= h($value); ?>" <?= ($selectedFilter ?? '') === $value ? 'selected' : ''; ?>>
                                                <?= h($job->jobTitle ?? ''); ?> <?= h($typeSuffix); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group col-lg-3 col-md-3">
                                <label for="district-filter">District</label>
                                <select
                                    name="district"
                                    id="district-filter"
                                    class="form-control"
                                    <?= !$hasOpenJobs ? 'disabled' : ''; ?>
                                >
                                    <option value="">All Districts</option>
                                    <?php foreach ($districts ?? [] as $district) : ?>
                                        <option value="<?= h($district->district); ?>" <?= ($selectedDistrict ?? '') === $district->district ? 'selected' : ''; ?>>
                                            <?= h($district->district); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group col-lg-3 col-md-3">
                                <label for="year-filter">Year</label>
                                <select
                                    name="year"
                                    id="year-filter"
                                    class="form-control"
                                    <?= !$hasOpenJobs ? 'disabled' : ''; ?>
                                >
                                    <option value="">Current Year</option>
                                    <?php foreach ($years ?? [] as $year) : ?>
                                        <option value="<?= h($year->app_year); ?>" <?= ($selectedYear ?? '') === (string) $year->app_year ? 'selected' : ''; ?>>
                                            <?= h($year->app_year); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group col-lg-2 col-md-2">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary btn-block" <?= !$hasOpenJobs ? 'disabled' : ''; ?>>
                                    Submit
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <?php if (($submitted ?? false) && empty($filterError)) : ?>
                <div class="card">
                    <div class="card-body table-responsive">
                        <h5 class="header-title">Scores Report</h5>
                        <?php if (empty($scores)) : ?>
                            <div class="alert alert-info mb-3">No applicants found for the selected filter.</div>
                        <?php endif; ?>
                        <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th>Record #</th>
                                    <th>Name</th>
                                    <th>Job Title / Job Type</th>
                                    <th>Education</th>
                                    <th>Trainings</th>
                                    <th>Experience</th>
                                    <th>LET</th>
                                    <th>Demo</th>
                                    <th>TRF</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (($scores ?? []) as $row) : ?>
                                    <?php
                                        $name = trim(($row->LastName ?? '') . ', ' . ($row->FirstName ?? '') . ' ' . ($row->MiddleName ?? ''));
                                        $name = trim($name, ', ');
                                        $positionSuffix = $jobTypeSuffixes[(int) ($row->job_type ?? 0)] ?? ('- Job Type ' . (int) ($row->job_type ?? 0));
                                        $position = trim(($row->jobTitle ?? '') . ' ' . $positionSuffix);
                                    ?>
                                    <tr>
                                        <td><?= h($row->record_no ?? ''); ?></td>
                                        <td><?= h($name); ?></td>
                                        <td><?= h($position); ?></td>
                                        <td><?= score_report_value($row->education ?? null); ?></td>
                                        <td><?= score_report_value($row->training ?? null); ?></td>
                                        <td><?= score_report_value($row->experience ?? null); ?></td>
                                        <td><?= score_report_value($row->let_rating ?? null); ?></td>
                                        <td><?= score_report_value($row->demo_rating ?? null); ?></td>
                                        <td><?= score_report_value($row->tr_rating ?? null); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>
