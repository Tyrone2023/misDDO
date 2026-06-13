<?php
if (!function_exists('h')) {
    function h($v)
    {
        return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('is_valid_email_format')) {
    function is_valid_email_format($email)
    {
        return filter_var(trim((string)$email), FILTER_VALIDATE_EMAIL) !== false;
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
$jobsByType = $jobsByType ?? [];
$hasOpenJobs = $hasOpenJobs ?? false;
$statistics = $statistics ?? [];
?>

<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="page-title mb-0"><?= h($title ?? 'Inquiry Report'); ?></h4>
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
                    <form method="get" action="<?= base_url('Pages/secretariat_inquiry_report'); ?>">
                        <div class="form-row">
                            <div class="form-group col-lg-3 col-md-3">
                                <label for="inquiry-filter">Job Title / Job Type</label>
                                <select
                                    name="filter"
                                    id="inquiry-filter"
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
                            <div class="form-group col-lg-2 col-md-2">
                                <label for="inquiry-district">District</label>
                                <select
                                    name="district"
                                    id="inquiry-district"
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
                            <div class="form-group col-lg-2 col-md-2">
                                <label for="inquiry-year">Year</label>
                                <select
                                    name="year"
                                    id="inquiry-year"
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
                            <div class="form-group col-lg-1 col-md-1">
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
                <!-- Statistics Card -->
                <!-- <div class="row mb-3">
                    <div class="col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="header-title">Confirmed</h5>
                                <h3 class="text-success"><?= h($statistics['confirmed'] ?? 0); ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="header-title">Not Confirmed</h5>
                                <h3 class="text-warning"><?= h($statistics['not_confirmed'] ?? 0); ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="header-title">Invalid Email</h5>
                                <h3 class="text-danger"><?= h($statistics['invalid_email'] ?? 0); ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="header-title">Total Inquiries</h5>
                                <h3><?= h(count($inquiries ?? [])); ?></h3>
                            </div>
                        </div>
                    </div>
                </div> -->

                <!-- Report Table -->
                <div class="card">
                    <div class="card-body table-responsive">
                        <div class="row mb-3">
                            <div class="col-12">
                                <h5 class="header-title">Application Inquiries Report</h5>
                                <button type="button" class="btn btn-sm btn-secondary" onclick="openPrintView()">
                                    <i class="fa fa-print"></i> Print Report
                                </button>
                            </div>
                        </div>

                        <?php if (empty($inquiries)) : ?>
                            <div class="alert alert-info mb-3">No inquiries found for the selected filter.</div>
                        <?php endif; ?>

                        <table id="datatable-buttons" class="table table-striped table-bordered w-100">
                            <thead>
                                <tr>
                                    <th width="15%">Record # / Name</th>
                                    <th width="20%">Job Title / Level</th>
                                    <th width="65%">Inquiry</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (($inquiries ?? []) as $row) : ?>
                                    <?php
                                        $name = trim(($row->LastName ?? '') . ', ' . ($row->FirstName ?? '') . ' ' . ($row->MiddleName ?? ''));
                                        $name = trim($name, ', ');
                                        $positionSuffix = $jobTypeSuffixes[(int) ($row->job_type ?? 0)] ?? ('- Job Type ' . (int) ($row->job_type ?? 0));
                                        $position = trim(($row->jobTitle ?? '') . ' ' . $positionSuffix);
                                    ?>
                                    <tr>
                                        <td>
                                            <strong><?= h($row->record_no ?? ''); ?></strong><br>
                                            <small><?= h($name); ?></small>
                                        </td>
                                        <td><?= h($position); ?></td>
                                        <td style="white-space: pre-wrap; word-wrap: break-word;">
                                            <?= h($row->inquiry ?? ''); ?>
                                        </td>
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

<script>
function openPrintView() {
    // Get current URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    
    // Build print URL with same parameters
    let printUrl = '<?= base_url('Pages/secretariat_inquiry_report_print'); ?>';
    
    if (urlParams.toString()) {
        printUrl += '?' + urlParams.toString();
    }
    
    // Open in new tab
    window.open(printUrl, '_blank');
}
</script>

<style media="print">
    .btn, .form-row, .card-body .row:first-child, .page-title-box {
        display: none !important;
    }
    
    .table-responsive {
        overflow: visible !important;
    }
    
    @page {
        size: landscape;
        margin: 0.5in;
    }
</style>
