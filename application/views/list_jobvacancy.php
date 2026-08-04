<?php include('templates/head.php'); ?>
<?php include('templates/header.php'); ?>

<!-- ============================================================== -->
<!-- Start Page Content here -->
<!-- ============================================================== -->

<?php
$dist     = $this->Common->no_cond('district');
$school   = $this->Common->no_cond('schools');
$settings = $this->Common->one_cond_row_select('mis_settings', 'sgod_sign_type,settingsID', 'settingsID', 1);

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
    10 => '- SNED',
    11 => '- SHS Academic and Core Subjects',
    12 => '- SHS Arts and Design Track',
    13 => '- SHS Sports Track',
    14 => '- SHS Technical-Vocational(TVL) Track',
    15 => '- Elementary - SPIMS',
    16 => '- Junior High School - SPIMS',
    17 => '- DOST - (RA 7687)',
    18 => '- DOST - (RA 10612)',
    19 => '- (SST I)',
    20 => '- FOR TESTING PURPOSES (DO NOT APPLY)'
];

// same labels without the leading dash, for use as a stand-alone chip
$jobTypeLabels = [];
foreach ($jobTypes as $k => $v) {
    $jobTypeLabels[$k] = ltrim($v, '- ');
}

// position groups come from the controller so Positions Settings stays the single source
$positionGroups = isset($groups) ? $groups : [
    1 => 'Teaching',
    2 => 'School Administration',
    3 => 'Related Teaching',
    4 => 'Non-Teaching'
];

$jobs = [];
foreach ($data as $row) {
    $jobs[$row->jobID] = [
        'jobTitle' => $row->jobTitle,
        'jobType'  => $row->job_type
    ];
}

$tjobs = [];
foreach ($teaching as $row) {
    $tjobs[$row->jobID] = [
        'jobTitle' => $row->jobTitle,
        'jobType'  => $row->job_type
    ];
}

// hero counters
$total_vacancies = count($data);
$open_apps       = 0;
$closed_apps     = 0;
foreach ($data as $row) {
    if ((int) $row->a_stat === 0) {
        $open_apps++;
    } else {
        $closed_apps++;
    }
}

// position titles handed to select2 so the dropdown can be rebuilt per position group
$positionOptions = [];
foreach ($pos_title as $row) {
    $positionOptions[] = [
        'id'     => (int) $row->id,
        'title'  => $row->title,
        'pos_id' => (int) $row->pos_id,
        'sg'     => ($row->sg === null || $row->sg === '') ? '' : (int) $row->sg
    ];
}

$is_hr = ($this->session->position === 'Human Resource Admin'
    || $this->session->position === 'HR Staff'
    || $this->session->position === 'Super Admin'
    || $this->session->position === 'asds'
    || $this->session->position === 'sds');
?>

<?php include('includes/hr_recruitment_styles.php'); ?>
<style>
    .dropdown-submenu { position: relative; }
    .dropdown-submenu .dropdown-menu {
        top: 0;
        left: 100%;
        margin-top: -1px;
        display: none;
    }
    .dropdown-submenu:hover .dropdown-menu { display: block; }

    /* toolbar dropdowns tuned to the card look */
    .hrp-toolbar { display: flex; flex-wrap: wrap; gap: .45rem; align-items: center; }
    .hrp-toolbar .btn-group > .hrp-btn { border-radius: 8px; }
    .hrp-toolbar .dropdown-menu {
        border: 1px solid #e9edf2;
        border-radius: 11px;
        box-shadow: 0 12px 30px rgba(16, 30, 54, .12);
        padding: .35rem;
        font-size: .84rem;
    }
    .hrp-toolbar .dropdown-item {
        border-radius: 7px;
        padding: .4rem .65rem;
        color: #5c6873;
    }
    .hrp-toolbar .dropdown-item:hover { background: #f1f4f8; color: #313a46; }
    .hrp-toolbar .dropdown-menu .dropdown-menu { max-height: 300px; overflow-y: auto; overflow-x: hidden; min-width: 280px; }
    .hrp-toolbar-group-label {
        font-size: .68rem;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: #98a6ad;
        font-weight: 600;
        margin-right: .15rem;
    }

    /* announcement/remarks column */
    .hrp-ann-cell { white-space: normal !important; min-width: 220px; max-width: 320px; }
    .hrp-ann-text {
        font-size: .82rem;
        color: #4a5568;
        line-height: 1.45;
        background: #fdf9ef;
        border-left: 3px solid #e0b34d;
        border-radius: 0 7px 7px 0;
        padding: .4rem .6rem;
        max-height: 5.5rem;
        overflow-y: auto;
    }
    .hrp-ann-meta { font-size: .7rem; color: #98a6ad; margin-top: .3rem; }
    .hrp-ann-btn { margin-top: .35rem; }
</style>

<div class="content-page">
    <div class="content">

        <!-- Start Content-->
        <div class="container-fluid">

            <!-- start page header -->
            <div class="row">
                <div class="col-12">
                    <div class="hrp-hero">
                        <div class="hrp-hero-text">
                            <span class="hrp-hero-eyebrow"><i class="mdi mdi-briefcase-outline"></i> Recruitment</span>
                            <h3 class="hrp-hero-title"><i class="mdi mdi-clipboard-text-outline"></i> Job Vacancies</h3>
                            <p class="hrp-hero-sub">
                                <?php if ($is_hr) : ?>
                                    All open postings for the division. Position titles are maintained in
                                    <a href="<?= base_url(); ?>Page/positionSettings" style="color:#fff;text-decoration:underline;">Positions Settings</a>.
                                <?php else : ?>
                                    Open postings for the division. Use the actions on each row to view or act on applications.
                                <?php endif; ?>
                            </p>
                        </div>
                        <div class="hrp-hero-stats">
                            <div class="hrp-stat">
                                <span class="hrp-stat-value"><?= number_format($total_vacancies); ?></span>
                                <span class="hrp-stat-label">Open Posts</span>
                            </div>
                            <div class="hrp-stat">
                                <span class="hrp-stat-value"><?= number_format($open_apps); ?></span>
                                <span class="hrp-stat-label">Accepting</span>
                            </div>
                            <div class="hrp-stat">
                                <span class="hrp-stat-value"><?= number_format($closed_apps); ?></span>
                                <span class="hrp-stat-label">Closed</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end page header -->

            <?php if ($this->session->flashdata('success')) : ?>
                <div class="hrp-alert hrp-alert-success">
                    <i class="mdi mdi-check-circle-outline"></i>
                    <div><?= $this->session->flashdata('success'); ?></div>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
            <?php endif; ?>

            <?php if ($this->session->flashdata('danger')) : ?>
                <div class="hrp-alert hrp-alert-danger">
                    <i class="mdi mdi-alert-circle-outline"></i>
                    <div><?= $this->session->flashdata('danger'); ?></div>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
            <?php endif; ?>

            <!-- start toolbar -->
            <div class="row">
                <div class="col-12">
                    <div class="hrp-card">
                        <div class="hrp-toolbar">

                            <?php if ($is_hr) : ?>

                                <button type="button" class="hrp-btn hrp-btn-primary" data-toggle="modal" data-target=".bs-example-modal-lg">
                                    <i class="mdi mdi-plus"></i> Add New Vacancy
                                </button>
                                <button type="button" class="hrp-btn hrp-btn-success" data-toggle="modal" data-target=".aa">
                                    <i class="mdi mdi-account-search-outline"></i> Search Applicant
                                </button>

                                <a href="<?= base_url(); ?>Page/positionSettings" class="hrp-btn hrp-btn-purple">
                                    <i class="mdi mdi-cog-outline"></i> Positions Settings
                                </a>

                                <!-- <span class="hrp-toolbar-group-label ml-2">Document submission</span>
                                <a onclick="return confirm('Unlock applicant document submission for ALL postings?')" href="<?= base_url(); ?>Pages/unlock_ads/" class="hrp-btn hrp-btn-teal" title="Unlock Applicant Document Submission">
                                    <i class="mdi mdi-lock-open-variant-outline"></i> Unlock All
                                </a>
                                <a onclick="return confirm('Lock applicant document submission for ALL postings?')" href="<?= base_url(); ?>Pages/lock_ads/" class="hrp-btn hrp-btn-warning" title="Lock Applicant Document Submission">
                                    <i class="mdi mdi-lock-outline"></i> Lock All
                                </a> -->

                                <div class="btn-group">
                                    <button type="button" class="hrp-btn hrp-btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                        <i class="mdi mdi-chart-box-outline"></i> Teaching Reports
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" target="_blank" href="<?= base_url(); ?>Pages/ssc_report">SSC Status Report</a>
                                        <a class="dropdown-item" target="_blank" href="<?= base_url(); ?>Pages/abd_report">Endorsed Applicant - District</a>
                                        <a class="dropdown-item" target="_blank" href="<?= base_url(); ?>Pages/abd_report_rr">Requested Rating - District</a>
                                        <a class="dropdown-item" target="_blank" href="<?= base_url(); ?>Pages/abd_report_validated">Validated - District</a>
                                        <a class="dropdown-item" target="_blank" href="<?= base_url(); ?>Pages/abd_report_multiple">Multiple Application</a>
                                        <a class="dropdown-item" target="_blank" href="<?= base_url(); ?>Pages/rated_applicantion">Rated Application</a>
                                        <?php if ($this->session->userdata('position') === 'Super Admin' || $this->session->position === 'Super Admin') : ?>
                                            <a class="dropdown-item" target="_blank" href="<?= base_url(); ?>Pages/rated_applicants_missing_scores">Rated Applicants (No Scores)</a>
                                        <?php endif; ?>
                                        <a class="dropdown-item" target="_blank" href="<?= base_url(); ?>Pages/jshs_applicantion/3">JHS Application</a>
                                        <a class="dropdown-item" target="_blank" href="<?= base_url(); ?>Pages/jshs_applicantion/4">SHS Application</a>
                                        <a class="dropdown-item" target="_blank" href="<?= base_url(); ?>Pages/dabd_report">DQ List</a>
                                        <a class="dropdown-item" target="_blank" href="<?= base_url(); ?>Pages/dabq_report">Qualified List</a>
                                        <a class="dropdown-item" target="_blank" href="<?= base_url(); ?>Pages/dabq_reportv2">Qualified List - District </a>
                                        <a class="dropdown-item" target="_blank" href="<?= base_url(); ?>Pages/dabq_reportv2_summary">Summary Of Applicants - Job Title</a>
                                        <a class="dropdown-item" target="_blank" href="<?= base_url(); ?>Pages/abd_report_district">Summary Of Applicant - District</a>
                                        <a class="dropdown-item" target="_blank" href="<?= base_url(); ?>Page/applicantList">Validated Applicants (All)</a>
                                    </div>
                                </div>

                                <div class="btn-group">
                                    <button type="button" class="hrp-btn hrp-btn-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="mdi mdi-file-tree-outline"></i> Reports by Job Title
                                    </button>
                                    <div class="dropdown-menu">

                                        <div class="dropdown-submenu">
                                            <a class="dropdown-item dropdown-toggle" href="#">Submitted Applicants</a>
                                            <div class="dropdown-menu">
                                                <?php foreach ($jobs as $jobID => $info) : ?>
                                                    <a class="dropdown-item" href="<?= base_url(); ?>Page/submittedApplicantsByJob/<?= $jobID; ?>" target="_blank">
                                                        <?= $info['jobTitle']; ?>
                                                        <?= $jobTypes[$info['jobType']] ?? ''; ?>
                                                    </a>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>

                                        <div class="dropdown-submenu">
                                            <a class="dropdown-item dropdown-toggle" href="#">Validated Applicants</a>
                                            <div class="dropdown-menu">
                                                <?php foreach ($jobs as $jobID => $info) : ?>
                                                    <a class="dropdown-item" href="<?= base_url(); ?>Page/validatedApplicantsByJob/<?= $jobID; ?>" target="_blank">
                                                        <?= $info['jobTitle']; ?>
                                                        <?= $jobTypes[$info['jobType']] ?? ''; ?>
                                                    </a>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>

                                        <div class="dropdown-submenu">
                                            <a class="dropdown-item dropdown-toggle" href="#">Shortlist</a>
                                            <div class="dropdown-menu">
                                                <?php foreach ($jobs as $jobID => $info) : ?>
                                                    <a class="dropdown-item" href="<?= base_url(); ?>Page/endorsedApplicantsByJob/<?= $jobID; ?>" target="_blank">
                                                        <?= $info['jobTitle']; ?>
                                                        <?= $jobTypes[$info['jobType']] ?? ''; ?>
                                                    </a>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>

                                        <div class="dropdown-submenu">
                                            <a class="dropdown-item dropdown-toggle" href="#">Rated Applicants</a>
                                            <div class="dropdown-menu">
                                                <?php foreach ($jobs as $jobID => $info) : ?>
                                                    <a class="dropdown-item" href="<?= base_url(); ?>Page/ratedApplicantsByJob/<?= $jobID; ?>" target="_blank">
                                                        <?= $info['jobTitle']; ?>
                                                        <?= $jobTypes[$info['jobType']] ?? ''; ?>
                                                    </a>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>

                                        <div class="dropdown-submenu">
                                            <a class="dropdown-item dropdown-toggle" href="#">Counts by District</a>
                                            <div class="dropdown-menu">
                                                <?php foreach ($jobs as $jobID => $info) : ?>
                                                    <a class="dropdown-item" href="<?= base_url(); ?>Page/applicantCountsByDistrict/<?= $jobID; ?>" target="_blank">
                                                        <?= $info['jobTitle']; ?>
                                                        <?= $jobTypes[$info['jobType']] ?? ''; ?>
                                                    </a>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <div class="btn-group">
                                    <button type="button" class="hrp-btn hrp-btn-warning dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                        <i class="mdi mdi-clipboard-list-outline"></i> Related &amp; Non-Teaching Reports
                                    </button>
                                    <div class="dropdown-menu">

                                        <div class="dropdown-submenu">
                                            <a class="dropdown-item dropdown-toggle" href="#">IER</a>
                                            <div class="dropdown-menu">
                                                <?php foreach ($jobs as $jobID => $info) : ?>
                                                    <a class="dropdown-item" href="<?= base_url(); ?>Pages/all_non_teaching_applicant/<?= $jobID; ?>" target="_blank">
                                                        <?= $info['jobTitle']; ?>
                                                        <?= $jobTypes[$info['jobType']] ?? ''; ?>
                                                    </a>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>

                                        <div class="dropdown-submenu">
                                            <a class="dropdown-item dropdown-toggle" href="#">IER V2</a>
                                            <div class="dropdown-menu">
                                                <?php foreach ($jobs as $jobID => $info) : ?>
                                                    <a class="dropdown-item" href="<?= base_url(); ?>Pages/all_non_teaching_applicantv2/<?= $jobID; ?>" target="_blank">
                                                        <?= $info['jobTitle']; ?>
                                                        <?= $jobTypes[$info['jobType']] ?? ''; ?>
                                                    </a>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>

                                        <div class="dropdown-submenu">
                                            <a class="dropdown-item dropdown-toggle" href="#">IER V3</a>
                                            <div class="dropdown-menu">
                                                <?php foreach ($jobs as $jobID => $info) : ?>
                                                    <a class="dropdown-item" href="<?= base_url(); ?>Pages/all_non_teaching_applicantv3/<?= $jobID; ?>" target="_blank">
                                                        <?= $info['jobTitle']; ?>
                                                        <?= $jobTypes[$info['jobType']] ?? ''; ?>
                                                    </a>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>

                                        <div class="dropdown-submenu">
                                            <a class="dropdown-item dropdown-toggle" href="#">IER V4</a>
                                            <div class="dropdown-menu">
                                                <?php foreach ($jobs as $jobID => $info) : ?>
                                                    <a class="dropdown-item" href="<?= base_url(); ?>Pages/all_non_teaching_applicantv4/<?= $jobID; ?>" target="_blank">
                                                        <?= $info['jobTitle']; ?>
                                                        <?= $jobTypes[$info['jobType']] ?? ''; ?>
                                                    </a>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>

                                        <div class="dropdown-submenu">
                                            <a class="dropdown-item dropdown-toggle" href="#">IER Group by Municipality</a>
                                            <div class="dropdown-menu">
                                                <?php foreach ($jobs as $jobID => $info) : ?>
                                                    <a class="dropdown-item" href="<?= base_url(); ?>Pages/ier_group_mun/<?= $jobID; ?>" target="_blank">
                                                        <?= $info['jobTitle']; ?>
                                                        <?= $jobTypes[$info['jobType']] ?? ''; ?>
                                                    </a>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>

                                        <div class="dropdown-submenu">
                                            <a class="dropdown-item dropdown-toggle" href="#">IER Group by Municipality V2</a>
                                            <div class="dropdown-menu">
                                                <?php foreach ($jobs as $jobID => $info) : ?>
                                                    <a class="dropdown-item" href="<?= base_url(); ?>Pages/ier_group_munv2/<?= $jobID; ?>" target="_blank">
                                                        <?= $info['jobTitle']; ?>
                                                        <?= $jobTypes[$info['jobType']] ?? ''; ?>
                                                    </a>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>

                                        <div class="dropdown-submenu">
                                            <a class="dropdown-item dropdown-toggle" href="#">IER Learning Area Specialization</a>
                                            <div class="dropdown-menu">
                                                <?php foreach ($tjobs as $jobID => $info) : ?>
                                                    <a class="dropdown-item" href="<?= base_url(); ?>Pages/las/<?= $jobID; ?>" target="_blank">
                                                        <?= $info['jobTitle']; ?>
                                                        <?= $jobTypes[$info['jobType']] ?? ''; ?>
                                                    </a>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>

                                        <a class="dropdown-item" href="<?= base_url(); ?>Pages/hiring_non_qaulified" target="_blank">Qualified List</a>
                                        <a class="dropdown-item" href="<?= base_url(); ?>Pages/hiring_non_not_qaulified" target="_blank">Not Qualified List</a>
                                        <a class="dropdown-item" href="<?= base_url(); ?>Pages/not_qualify_promotion" target="_blank">Not Qualified List V2</a>
                                        <a class="dropdown-item" href="<?= base_url(); ?>Pages/hiring_non_qaulified_rated" target="_blank">Qualified List Rated</a>

                                        <div class="dropdown-submenu">
                                            <a class="dropdown-item" href="<?= base_url(); ?>Pages/qaulified_promotion" target="_blank">Qualified List Promotion</a>
                                            <div class="dropdown-menu">
                                                <?php foreach ($ren as $pro) : ?>
                                                    <a class="dropdown-item" href="<?= base_url(); ?>Pages/qaulified_promotion_job/<?= $pro->jobID; ?>" target="_blank">
                                                        <?= $pro->jobTitle; ?> <?= $jobTypes[$pro->job_type] ?? ''; ?>
                                                    </a>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            <?php elseif ($this->session->position == "doceval") : ?>

                                <a target="_blank" href="<?= base_url(); ?>Pages/dabd_report" class="hrp-btn hrp-btn-danger"><i class="mdi mdi-account-cancel-outline"></i> Disqualified List</a>
                                <a target="_blank" href="<?= base_url(); ?>Pages/dabq_report" class="hrp-btn hrp-btn-primary"><i class="mdi mdi-account-check-outline"></i> Qualified List</a>

                            <?php elseif ($this->session->position == "School") : ?>
                                <a href="<?= base_url(); ?>Pages/school_generate_report" class="hrp-btn hrp-btn-primary" target="_blank"><i class="mdi mdi-file-document-outline"></i> List of Validated Applicants</a>
                            <?php elseif ($this->session->position === 'Evaluator' || $this->session->position === 'Super Admin' || $this->session->position === 'Human Resource Admin' || $this->session->position === 'HR Staff') : ?>
                                <!-- reserved for evaluator shortcuts -->
                            <?php elseif ($this->session->position == "District") : ?>
                                <a href="<?= base_url(); ?>Pages/dgr" class="hrp-btn hrp-btn-primary" target="_blank"><i class="mdi mdi-file-send-outline"></i> Generate Transmittal</a>
                            <?php endif; ?>

                            <?php if ($this->session->position == 'raters') { ?>
                                <div class="btn-group">
                                    <button type="button" class="hrp-btn hrp-btn-warning dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                        <i class="mdi mdi-clipboard-list-outline"></i> Related &amp; Non-Teaching Reports
                                    </button>
                                    <div class="dropdown-menu">

                                        <div class="dropdown-submenu">
                                            <a class="dropdown-item dropdown-toggle" href="#">IER</a>
                                            <div class="dropdown-menu">
                                                <?php foreach ($jobs as $jobID => $info) : ?>
                                                    <a class="dropdown-item" href="<?= base_url(); ?>Pages/all_non_teaching_applicant/<?= $jobID; ?>" target="_blank">
                                                        <?= $info['jobTitle']; ?>
                                                        <?= $jobTypes[$info['jobType']] ?? ''; ?>
                                                    </a>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                        <div class="dropdown-submenu">
                                            <a class="dropdown-item dropdown-toggle" href="#">IER Group by Municipality</a>
                                            <div class="dropdown-menu">
                                                <?php foreach ($jobs as $jobID => $info) : ?>
                                                    <a class="dropdown-item" href="<?= base_url(); ?>Pages/ier_group_mun/<?= $jobID; ?>" target="_blank">
                                                        <?= $info['jobTitle']; ?>
                                                        <?= $jobTypes[$info['jobType']] ?? ''; ?>
                                                    </a>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                        <a class="dropdown-item" href="<?= base_url(); ?>Pages/hiring_non_qaulified" target="_blank">Qualified List</a>
                                        <a class="dropdown-item" href="<?= base_url(); ?>Pages/hiring_non_not_qaulified" target="_blank">Not Qualified List</a>
                                        <a class="dropdown-item" href="<?= base_url(); ?>Pages/hiring_non_qaulified_rated" target="_blank">Qualified List Rated</a>

                                        <div class="dropdown-submenu">
                                            <a class="dropdown-item" href="<?= base_url(); ?>Pages/qaulified_promotion" target="_blank">Qualified List Promotion</a>
                                            <div class="dropdown-menu">
                                                <?php foreach ($ren as $pro) : ?>
                                                    <a class="dropdown-item" href="<?= base_url(); ?>Pages/qaulified_promotion_job/<?= $pro->jobID; ?>" target="_blank">
                                                        <?= $pro->jobTitle; ?> <?= $jobTypes[$pro->job_type] ?? ''; ?>
                                                    </a>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>

                        </div>
                    </div>
                </div>
            </div>
            <!-- end toolbar -->

            <div class="row">
                <div class="col-12">
                    <div class="hrp-card">

                        <div class="hrp-card-head">
                            <div>
                                <h4 class="hrp-card-title">Open Vacancies</h4>
                                <p class="hrp-card-sub">
                                    <?= number_format($total_vacancies); ?> posting<?= $total_vacancies == 1 ? '' : 's'; ?>
                                    <span class="hrp-dotsep">&bull;</span>
                                    <?= number_format($open_apps); ?> still accepting applications
                                </p>
                            </div>
                        </div>

                        <?php if (empty($data)) : ?>
                            <div class="hrp-empty">
                                <i class="mdi mdi-briefcase-remove-outline"></i>
                                There are no open job vacancies right now.
                            </div>
                        <?php else : ?>
                            <?php // closed postings are filtered out by default; the pills bring them back ?>
                            <div class="hrp-tabs" id="jv-status-tabs" data-default="<?= $open_apps > 0 ? '0' : ''; ?>">
                                <a href="javascript:void(0);" class="hrp-tab" data-status="0">
                                    Accepting <span class="hrp-tab-count"><?= number_format($open_apps); ?></span>
                                </a>
                                <a href="javascript:void(0);" class="hrp-tab" data-status="1">
                                    Closed <span class="hrp-tab-count"><?= number_format($closed_apps); ?></span>
                                </a>
                                <a href="javascript:void(0);" class="hrp-tab" data-status="">
                                    All <span class="hrp-tab-count"><?= number_format($total_vacancies); ?></span>
                                </a>
                            </div>

                            <div class="table-responsive">
                                <table id="jv-table" class="table hrp-table dt-responsive nowrap" style="width:100%;">
                                    <thead>
                                        <tr>
                                            <th>Position</th>
                                            <th>Emp. Type</th>
                                            <th>Date Posted</th>
                                            <th>Office/Bureau/Service/<br />Unit where the vacancy exists</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Attachment</th>
                                            <th>Announcement/Remarks</th>
                                            <th class="text-right">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $i = 1;
                                        $rowMenus = [];
                                        foreach ($data as $row) {
                                            if ($this->session->position === 'Evaluator') {
                                                $d = $this->Common->one_cond_row('district', 'id', $this->session->c_id);
                                                $all_app = $this->Common->three_cond_count_row('hris_applications', 'jobID', $row->jobID, 'appStatus', 'Endorsed for Rating', 'district', $d->discription);
                                            }
                                            $job = $this->Common->two_cond_count_row('hris_applications', 'jobID', $row->jobID, 'empEmail', $this->session->username);
                                            $applicant = $this->Common->three_cond_count_row('hris_applications', 'jobID', $row->jobID, 'pre_school', $this->session->c_id, 'appStatus', 'Application Submitted');

                                            $typeLabel = $jobTypeLabels[$row->job_type] ?? '';
                                            $initials  = strtoupper(mb_substr(trim((string) $row->jobTitle), 0, 2));
                                        ?>

                                            <tr data-status="<?= (int) $row->a_stat === 0 ? '0' : '1'; ?>">
                                                <td data-order="<?= html_escape($row->jobTitle); ?>">
                                                    <div class="hrp-title-cell">
                                                        <span class="hrp-avatar"><?= html_escape($initials); ?></span>
                                                        <span class="hrp-title-text">
                                                            <span class="hrp-title-name"><?= $row->jobTitle; ?></span>
                                                            <span class="hrp-title-sub">
                                                                <?php if ($typeLabel !== '') : ?>
                                                                    <?= $typeLabel; ?><span class="hrp-dotsep">&bull;</span>
                                                                <?php endif; ?>
                                                                SY <?= $row->sy; ?>
                                                            </span>
                                                        </span>
                                                    </div>
                                                </td>
                                                <td><span class="hrp-chip hrp-chip-grey"><?= $row->empType; ?></span></td>
                                                <td><span class="hrp-date"><?= $row->datePosted; ?></span></td>
                                                <td><?= $row->assign; ?></td>
                                                <td class="text-center" data-order="<?= (int) $row->a_stat; ?>">
                                                    <?php if ((int) $row->a_stat === 0) : ?>
                                                        <span class="hrp-chip hrp-chip-green"><i class="mdi mdi-check-circle-outline"></i> Accepting</span>
                                                    <?php else : ?>
                                                        <span class="hrp-chip hrp-chip-red"><i class="mdi mdi-close-circle-outline"></i> Closed</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <div class="hrp-actions justify-content-center">
                                                        <?php if ($row->file != "") { ?>
                                                            <a href="<?= base_url() . 'uploads/regfile/' . $row->file; ?>" target="_blank" class="hrp-ico hrp-ico-blue tooltips" data-placement="top" data-toggle="tooltip" data-original-title="View File Attachment"><i class="mdi mdi-file-document-outline"></i></a>
                                                            <?php if ($this->session->position === 'Admin' || $this->session->position === 'Super Admin' || $this->session->position === 'HR Staff' || $this->session->position === 'Human Resource Admin' || $this->session->position === 'asds') : ?>
                                                                <a href="#" data-id="<?= $row->jobID; ?>" class="hrp-ico hrp-ico-amber open-AddBookDialog tooltips" data-toggle="modal" data-target=".edit_vacancy_file" data-placement="top" data-original-title="Replace File Attachment"><i class="mdi mdi-file-replace-outline"></i></a>
                                                            <?php endif; ?>
                                                        <?php } else { ?>
                                                            <?php if ($this->session->userdata('position') === 'Human Resource Admin' || $this->session->userdata('position') === 'HR Staff' || $this->session->userdata('position') === 'Super Admin') { ?>
                                                                <a href="#" data-id="<?= $row->jobID; ?>" class="hrp-ico hrp-ico-amber open-AddBookDialog tooltips" data-toggle="modal" data-target=".edit_vacancy_file" data-placement="top" data-original-title="Upload File Attachment"><i class="mdi mdi-file-upload-outline"></i></a>
                                                            <?php } else { ?>
                                                                <span class="hrp-muted">&mdash;</span>
                                                            <?php } ?>
                                                        <?php } ?>
                                                    </div>
                                                </td>

                                                <?php
                                                // announcement/remarks: HR maintains it here, every applicant
                                                // for this posting sees it on their dashboard
                                                $announcement = isset($row->announcement) ? trim((string) $row->announcement) : '';
                                                $annBy        = isset($row->announcement_by) ? trim((string) $row->announcement_by) : '';
                                                $annAt        = isset($row->announcement_at) ? trim((string) $row->announcement_at) : '';
                                                ?>
                                                <td class="hrp-ann-cell">
                                                    <?php if ($announcement !== '') : ?>
                                                        <div class="hrp-ann-text"><?= nl2br(html_escape($announcement)); ?></div>
                                                        <?php if ($annBy !== '' || $annAt !== '') : ?>
                                                            <div class="hrp-ann-meta">
                                                                <?php if ($annBy !== '') : ?><?= html_escape($annBy); ?><?php endif; ?>
                                                                <?php if ($annBy !== '' && $annAt !== '') : ?><span class="hrp-dotsep">&bull;</span><?php endif; ?>
                                                                <?php if ($annAt !== '') : ?><?= date('M d, Y g:i A', strtotime($annAt)); ?><?php endif; ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    <?php elseif (!$is_hr) : ?>
                                                        <span class="hrp-muted">&mdash;</span>
                                                    <?php endif; ?>

                                                    <?php if ($is_hr) : ?>
                                                        <button type="button" class="hrp-btn hrp-btn-sm hrp-ann-btn"
                                                                data-job="<?= $row->jobID; ?>"
                                                                data-title="<?= html_escape(trim($row->jobTitle . ($typeLabel !== '' ? ' - ' . $typeLabel : ''))); ?>"
                                                                data-announcement="<?= html_escape($announcement); ?>">
                                                            <i class="mdi <?= $announcement !== '' ? 'mdi-pencil-outline' : 'mdi-bullhorn-outline'; ?>"></i>
                                                            <?= $announcement !== '' ? 'Edit' : 'Add'; ?>
                                                        </button>
                                                    <?php endif; ?>
                                                </td>

                                                <td class="text-right">
                                                    <?php $validated = $this->Common->three_cond_count_row('hris_applications', 'jobID', $row->jobID, 'dq', 0, 'appStatus', 'Validated'); ?>
                                                    <?php $rated = $this->Common->three_cond_count_row('hris_applications', 'jobID', $row->jobID, 'dq', 1, 'appStatus', 'Endorsed for Rating') ?>

                                                    <?php
                                                    // every role's actions are collected first, then rendered as one
                                                    // named menu instead of a row of unlabelled icons
                                                    ob_start();
                                                    ?>

                                                        <?php if ($this->session->position === 'raters') { ?>
                                                            <a class="hrp-action-item" href="<?= base_url(); ?>Pages/sa_view_applicant/<?= $row->jobID; ?>">
                                                                <i class="mdi mdi-file-document-box-check-outline hrp-i-blue"></i> Validated Applicants
                                                                <?php if ($validated->num_rows() != 0) : ?><span class="hrp-item-count"><?= $validated->num_rows(); ?></span><?php endif; ?>
                                                            </a>
                                                        <?php } ?>

                                                        <?php if ($is_hr) : ?>

                                                            <div class="hrp-action-group">Applicants</div>

                                                            <a class="hrp-action-item" href="<?= base_url(); ?>Page/viewApplicants?jobID=<?php echo $row->jobID; ?>&jobTitle=<?php echo $row->jobTitle; ?> <?= $jobTypes[$row->job_type] ?? ''; ?>">
                                                                <i class="mdi mdi-file-document-box-outline hrp-i-blue"></i> View Applicants
                                                            </a>

                                                            <?php if ($validated->num_rows() != 0) { ?>
                                                                <a class="hrp-action-item" href="<?= base_url(); ?>Pages/sa_view_applicant/<?= $row->jobID; ?>">
                                                                    <i class="mdi mdi-file-document-box-check-outline hrp-i-green"></i> Validated Applicants
                                                                    <span class="hrp-item-count"><?= $validated->num_rows(); ?></span>
                                                                </a>
                                                            <?php } ?>

                                                            <?php if ($rated->num_rows() != 0) { ?>
                                                                <a class="hrp-action-item" href="<?= base_url(); ?>Pages/sa_view_applicant_endorsed/<?= $row->jobID; ?>">
                                                                    <i class="mdi mdi-file-document-box-check-outline hrp-i-amber"></i> Endorsed Applicants
                                                                    <span class="hrp-item-count"><?= $rated->num_rows(); ?></span>
                                                                </a>
                                                            <?php } ?>

                                                            <a class="hrp-action-item" target="_blank" href="<?= base_url(); ?>Pages/rqa_list/<?= $row->sy; ?>?jobID=<?php echo $row->jobID; ?>&jobTitle=<?php echo $row->jobTitle; ?><?= $jobTypes[$row->job_type] ?? ''; ?>">
                                                                <i class="mdi mdi-calculator hrp-i-green"></i> RQA
                                                            </a>

                                                            <?php
                                                            if ($row->position == 1) {
                                                                $ca = $this->Common->two_cond_count_row('hris_applications', 'jobID', $row->jobID, 'appStatus', 'Validated');
                                                                if ($ca->num_rows() <= 1) {
                                                            ?>
                                                                    <a class="hrp-action-item" onclick="return confirm('Are you sure?')" href="<?= base_url(); ?>Pages/validated_by_job_id/<?= $row->jobID; ?>">
                                                                        <i class="mdi mdi-handshake-outline hrp-i-blue"></i> Validate
                                                                    </a>
                                                            <?php }
                                                            } ?>

                                                            <div class="hrp-action-sep"></div>
                                                            <div class="hrp-action-group">Posting</div>

                                                            <a class="hrp-action-item" href="<?= base_url(); ?>Pages/edit_vacancy/<?= $row->jobID; ?>">
                                                                <i class="mdi mdi-file-document-edit-outline hrp-i-purple"></i> Edit Job Vacancy
                                                            </a>

                                                            <?php if ($row->a_stat == 0) { ?>
                                                                <a class="hrp-action-item" href="<?= base_url(); ?>Pages/closed_vacancy/<?= $row->jobID; ?>">
                                                                    <i class="mdi mdi-account-cancel-outline hrp-i-red"></i> Close Vacancy
                                                                </a>
                                                            <?php } else { ?>
                                                                <a class="hrp-action-item" href="<?= base_url(); ?>Pages/open_vacancy/<?= $row->jobID; ?>">
                                                                    <i class="mdi mdi-account-check-outline hrp-i-green"></i> Open Vacancy
                                                                </a>
                                                            <?php } ?>

                                                            <a class="hrp-action-item" onclick="return confirm('Unlock applicant document submission for this position?')" href="<?= base_url(); ?>Pages/unlock_ads/<?= $row->jobID; ?>">
                                                                <i class="mdi mdi-lock-open-variant-outline hrp-i-teal"></i> Unlock Document Submission
                                                            </a>
                                                            <a class="hrp-action-item" onclick="return confirm('Lock applicant document submission for this position?')" href="<?= base_url(); ?>Pages/lock_ads/<?= $row->jobID; ?>">
                                                                <i class="mdi mdi-lock-outline hrp-i-amber"></i> Lock Document Submission
                                                            </a>

                                                            <a class="hrp-action-item" onclick="return confirm('Archive this job vacancy?')" href="<?= base_url(); ?>Pages/close_job/<?= $row->jobID; ?>">
                                                                <i class="mdi mdi-archive-arrow-down-outline hrp-i-grey"></i> Archive
                                                            </a>

                                                        <?php elseif ($this->session->position == 'doceval') : ?>
                                                            <a class="hrp-action-item" href="<?= base_url(); ?>Pages/sa_view_applicant/<?= $row->jobID; ?>">
                                                                <i class="mdi mdi-file-document-box-check-outline hrp-i-blue"></i> Validated Applicants
                                                            </a>

                                                        <?php elseif ($this->session->position == 'District') : ?>
                                                            <?php
                                                            $d = $this->Common->one_cond_row('district', 'id', $this->session->c_id);
                                                            $acount = $this->Common->three_cond_count_row('hris_applications', 'jobID', $row->jobID, 'district', $d->discription, 'appStatus', 'Application Submitted');
                                                            ?>
                                                            <a class="hrp-action-item" href="<?= base_url(); ?>Pages/school_list_applicant/<?= $row->jobID; ?>">
                                                                <i class="mdi mdi-file-document-box-outline hrp-i-green"></i> View School
                                                                <?php if ($acount->num_rows() != 0) { ?><span class="hrp-item-count"><?= $acount->num_rows(); ?></span><?php } ?>
                                                            </a>

                                                        <?php elseif ($this->session->position == 'Evaluator') : ?>
                                                            <a class="hrp-action-item" href="<?= base_url(); ?>Pages/evaluator_applicant/<?= $row->jobID; ?>">
                                                                <i class="mdi mdi-file-document-box-outline hrp-i-green"></i> Applicants
                                                                <?php if ($all_app->num_rows() != 0) { ?><span class="hrp-item-count"><?= $all_app->num_rows(); ?></span><?php } ?>
                                                            </a>

                                                        <?php elseif ($this->session->position == 'rater') : ?>
                                                            <a class="hrp-action-item" href="<?= base_url(); ?>Pages/evaluator_applicant/<?= $row->jobID; ?>">
                                                                <i class="mdi mdi-file-document-box-outline hrp-i-green"></i> Applicants
                                                            </a>

                                                        <?php elseif ($this->session->position == 'School') : ?>
                                                            <a class="hrp-action-item" href="<?= base_url(); ?>Pages/school_applicant/<?= $row->jobID; ?>/0">
                                                                <i class="mdi mdi-file-document-box-check-outline hrp-i-green"></i> Validate Applicants
                                                                <?php if ($applicant->num_rows() != 0) { ?><span class="hrp-item-count"><?= $applicant->num_rows(); ?></span><?php } ?>
                                                            </a>
                                                            <a class="hrp-action-item" href="<?= base_url(); ?>Pages/school_applicant/<?= $row->jobID; ?>/1">
                                                                <i class="mdi mdi-file-document-box-search-outline hrp-i-blue"></i> All Applicants
                                                            </a>

                                                        <?php elseif ($this->session->position === 'reg') : ?>
                                                            <?php if ($row->a_stat == 0) {
                                                                if ($row->position == 1) { ?>
                                                                    <a class="hrp-action-item open-AddBookDialog" href="#" data-modal=".apply" data-job="<?= $row->jobID; ?>">
                                                                        <i class="mdi mdi-clipboard-check-outline hrp-i-green"></i> Apply
                                                                    </a>
                                                                <?php } else { ?>
                                                                    <a class="hrp-action-item open-AddBookDialog" href="<?= base_url(); ?>Pages/submit_application_non_teaching/<?= $row->jobID; ?>">
                                                                        <i class="mdi mdi-clipboard-check-outline hrp-i-green"></i> Apply
                                                                    </a>
                                                            <?php }
                                                            } ?>

                                                        <?php elseif ($this->session->userdata('position') === 'user') : ?>
                                                            <?php if ($row->a_stat == 0) {
                                                                if ($row->position == 1) { ?>
                                                                    <a class="hrp-action-item open-AddBookDialog" href="#" data-modal=".apply" data-job="<?= $row->jobID; ?>">
                                                                        <i class="mdi mdi-clipboard-check-outline hrp-i-green"></i> Apply
                                                                    </a>
                                                                <?php } else { ?>
                                                                    <a class="hrp-action-item open-AddBookDialog" href="<?= base_url(); ?>Pages/submit_application_non_teaching/<?= $row->jobID; ?>">
                                                                        <i class="mdi mdi-clipboard-check-outline hrp-i-green"></i> Apply
                                                                    </a>
                                                            <?php }
                                                            } ?>
                                                        <?php endif; ?>

                                                    <?php $rowMenu = trim(ob_get_clean()); ?>

                                                    <?php if ($rowMenu !== '') : ?>
                                                        <?php // the menu itself is rendered after the table so its text
                                                              // does not leak into the DataTables column search ?>
                                                        <?php $rowMenus[$row->jobID] = $rowMenu; ?>
                                                        <button type="button" class="hrp-btn hrp-btn-primary hrp-btn-sm hrp-actions-btn"
                                                                data-job="<?= $row->jobID; ?>"
                                                                data-title="<?= html_escape(trim($row->jobTitle . ($typeLabel !== '' ? ' - ' . $typeLabel : ''))); ?>">
                                                            <i class="mdi mdi-cog-outline"></i> Actions
                                                        </button>
                                                    <?php else : ?>
                                                        <span class="hrp-muted">&mdash;</span>
                                                    <?php endif; ?>
                                                </td>

                                        <?php echo "</tr>";
                                        } ?>
                                    </tbody>

                                </table>
                            </div>

                            <!-- action lists, cloned into the modal on demand -->
                            <div id="hrp-row-menus" style="display:none;">
                                <?php foreach ($rowMenus as $menuJobID => $menuHtml) : ?>
                                    <div class="hrp-row-menu" data-job="<?= $menuJobID; ?>"><?= $menuHtml; ?></div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
            <!-- end row -->

        </div>
        <!-- end container-fluid -->

    </div>
    <!-- end content -->

    <?php include('templates/footer.php'); ?>


    <!--  Row actions -->
    <div class="modal fade hrp-modal hrp-modal-compact" id="hrp-actions-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="mdi mdi-cog-outline"></i>
                        <span id="hrp-actions-title">Actions</span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="hrp-actions-list" id="hrp-actions-list"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.modal -->

    <!--  Add New Vacancies -->
    <div class="modal fade bs-example-modal-lg hrp-modal hrp-modal-compact" id="addVacancyModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myLargeModalLabel"><i class="mdi mdi-briefcase-plus-outline"></i> Job Vacancy Posting</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>

                <?php
                $attributes = array('class' => 'parsley-examples');
                echo form_open_multipart('Page/jobVacancy', $attributes);
                ?>
                <div class="modal-body">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="hrp-field">
                                <label class="hrp-label" for="position-select">Position Group <span class="hrp-req">*</span></label>
                                <select class="form-control" name="position" id="position-select" required>
                                    <option value=""></option>
                                    <?php foreach ($positionGroups as $gid => $gname) { ?>
                                        <option value="<?= $gid; ?>"><?= html_escape($gname); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="hrp-field">
                                <label class="hrp-label" for="job-title-select">Position Title <span class="hrp-req">*</span></label>
                                <select class="form-control" name="jobTitle" id="job-title-select" required>
                                    <option value=""></option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div id="teaching-group-type" style="display:none;">
                        <div class="hrp-field">
                            <label class="hrp-label" for="job_type1">Teaching Group Type</label>
                            <select class="form-control" name="job_type1" id="job_type1">
                                <option value="0"></option>
                                <?php $gt = array(
                                    'Elementary' => 1,
                                    'Secondary' => 2,
                                    'Junior High School' => 3,
                                    'Senior High School' => 4,
                                    'kindergarten' => 5,
                                    'IPED Elementary' => 6,
                                    'IPED Secondary' => 7,
                                    'IPED Junior High School' => 8,
                                    'IPED Senior High School' => 9,
                                    'SNED' => 10,
                                    'Senior High School - Academic and Core Subjects' => 11,
                                    'Senior High School - Arts and Design Track' => 12,
                                    'Senior High School - Sports Track' => 13,
                                    'Senior High School - Technical-Vocational(TVL) Track' => 14,
                                    'Elementary - SPIMS' => 15,
                                    'Junior High School - SPIMS' => 16,
                                    'DOST - (RA 7687)' => 17,
                                    'DOST - (RA 10612)' => 18,
                                    '(SST I)' => 19,
                                    'FOR TESTING PURPOSES (DO NOT APPLY)' => 20,
                                ); ?>
                                <?php foreach ($gt as $row => $key) { ?>
                                    <option value="<?= $key; ?>"><?= $row; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div id="admin-group-type" style="display:none;">
                        <div class="hrp-field">
                            <label class="hrp-label" for="job_type">Administration Group Type</label>
                            <select class="form-control" name="job_type" id="job_type">
                                <option value="0"></option>
                                <?php $gt = array(
                                    'Elementary' => 1,
                                    'Secondary' => 2,
                                    'Junior High School' => 3,
                                    'Senior High School' => 4
                                ); ?>
                                <?php foreach ($gt as $row => $key) { ?>
                                    <option value="<?= $key; ?>"><?= $row; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="hrp-field">
                                <label class="hrp-label" for="empType">Employment Type</label>
                                <select class="form-control" name="empType" id="empType">
                                    <option value=""></option>
                                    <option>Permanent Position</option>
                                    <option>Job Order</option>
                                    <option>Contract of Service</option>
                                    <option>Provisional</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="hrp-field">
                                <label class="hrp-label" for="sy">Year <span class="hrp-req">*</span></label>
                                <select class="form-control" name="sy" id="sy" required>
                                    <option value=""></option>
                                    <?php
                                    $firstYear = (int)date('Y');
                                    $lastYear = $firstYear + 5;

                                    for ($i = $firstYear; $i <= $lastYear; $i++) {
                                        echo '<option value=' . $i . '>' . $i . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="hrp-field">
                        <label class="hrp-label" for="assign">Office/Bureau/Service/Unit where the vacancy exists <span class="hrp-req">*</span></label>
                        <input type="text" name="assign" id="assign" class="form-control" value="" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="hrp-field">
                                <label class="hrp-label" for="ecp">ECP <span class="hrp-req">*</span></label>
                                <select class="form-control" name="ecp" id="ecp" required>
                                    <option value=""></option>
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="hrp-field">
                                <label class="hrp-label" for="jv-file">Attachment (PDF) <span class="hrp-req">*</span></label>
                                <input type="file" class="form-control" name="file" id="jv-file" accept="application/pdf" required>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="position_id" id="pos-id-input" value="" />

                </div>
                <div class="modal-footer">
                    <button type="button" class="hrp-btn" data-dismiss="modal">Cancel</button>
                    <button type="submit" name="submit" value="Submit" class="hrp-btn hrp-btn-primary"><i class="mdi mdi-content-save-outline"></i> Post Vacancy</button>
                </div>
                </form>

            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->

    <!--  Add New Vacancies -->
    <div class="modal fade bs-example-modal-apply hrp-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myLargeModalLabel">Submit an Application</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">

                    <form class="form-horizontal" method="post">
                        <div class="form-group row">
                            <label for="inputEmail3" class="col-md-3 col-form-label">Position</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" name="jobTitle">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="inputEmail3" class="col-md-3 col-form-label">Item No. (if applicable)</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" name="itemNo">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="inputPassword3" class="col-md-3 col-form-label">Employment Type</label>
                            <div class="col-md-9">
                                <select class="form-control" name="empType">
                                    <option></option>
                                    <option>Permanent Position</option>
                                    <option>Job Order</option>
                                    <option>Contract of Service</option>

                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="inputPassword5" class="col-md-3 col-form-label">Department</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" name="department">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="inputPassword5" class="col-md-3 col-form-label">Job Description</label>
                            <div class="col-md-9">
                                <textarea class="form-control" rows="5" name="description"></textarea>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="inputPassword5" class="col-md-3 col-form-label">No. of Vacancies</label>
                            <div class="col-md-9">
                                <input type="number" class="form-control" name="qty">
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
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->

    <div class="modal fade apply hrp-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myLargeModalLabel"><i class="mdi mdi-clipboard-check-outline"></i> Submit an Application</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">

                    <?php
                    $attributes = array('class' => 'parsley-examples');
                    echo form_open('pages/submit_application/' . $this->session->c_id);
                    ?>

                    <input type="hidden" name="id" id="job">

                    <div class="hrp-field">
                        <label class="hrp-label" for="dist">District</label>
                        <select class="form-control" name="district" id="dist" required>
                            <option value="">Please Select Your District</option>
                            <?php foreach ($dist as $row) { ?>
                                <option value="<?= $row->discription; ?>"><?= strtoupper($row->discription); ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <?php if ($settings->sgod_sign_type == 1) { ?>
                        <input type="hidden" name="school" value="202401">
                    <?php } else { ?>
                        <div class="hrp-field">
                            <label class="hrp-label" for="school">School</label>
                            <select class="form-control" name="school" id="school" required>
                                <option value="">Please Select School</option>
                                <?php foreach ($school as $row) { ?>
                                    <option data-dist="<?= $row->district; ?>" value="<?= $row->schoolID; ?>"><?= strtoupper($row->schoolName); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    <?php } ?>

                    <div class="form-group mb-0 justify-content-end row">
                        <div class="col-md-12 text-right">
                            <input type="submit" name="submit" value="Submit Application" class="hrp-btn hrp-btn-primary">
                        </div>
                    </div>
                    </form>

                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->

    <div class="modal fade applyme hrp-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myLargeModalLabel">Submit an Application</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">

                    <?php
                    $attributes = array('class' => 'parsley-examples');
                    echo form_open('pages/submit_application/' . $this->session->c_id);
                    ?>

                    <input type="hidden" name="id" id="job">

                    <div class="hrp-field">
                        <label class="hrp-label" for="distme">District</label>
                        <select class="form-control" name="district" id="distme" required>
                            <option value="">Please Select Your District</option>
                            <option value="School Division Office">School Division Office</option>
                        </select>
                    </div>

                    <div class="hrp-field">
                        <label class="hrp-label" for="school">School</label>
                        <select class="form-control" name="school" id="school" required>
                            <option value="">Please Select School</option>
                            <?php foreach ($school as $row) { ?>
                                <option data-dist="<?= $row->district; ?>" value="<?= $row->schoolID; ?>"><?= strtoupper($row->schoolName); ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group mb-0 justify-content-end row">
                        <div class="col-md-12 text-right">
                            <input type="submit" name="submit" value="Submit Application" class="hrp-btn hrp-btn-primary">
                        </div>
                    </div>
                    </form>

                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->

    <div class="modal fade applyedit hrp-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myLargeModalLabel">Edit my Application</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">

                    <?php
                    $attributes = array('class' => 'parsley-examples');
                    echo form_open('pages/edit_application/' . $this->session->c_id);
                    ?>

                    <input type="hidden" name="id" id="job">

                    <div class="hrp-field">
                        <label class="hrp-label" for="distedit">District</label>
                        <select class="form-control" name="district" id="distedit" required>
                            <option value="">Please Select Your District</option>
                            <?php foreach ($dist as $row) { ?>
                                <option value="<?= $row->discription; ?>"><?= $row->discription; ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="hrp-field">
                        <label class="hrp-label" for="schooledit">School</label>
                        <select class="form-control" name="school" id="schooledit" required>
                            <option value="">Please Select School</option>
                            <?php foreach ($school as $row) { ?>
                                <option data-distedit="<?= $row->district; ?>" value="<?= $row->schoolID; ?>"><?= $row->schoolName; ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group mb-0 justify-content-end row">
                        <div class="col-md-12 text-right">
                            <input type="submit" name="submit" value="Submit" class="hrp-btn hrp-btn-primary">
                        </div>
                    </div>
                    </form>

                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->

    <div class="modal fade generate hrp-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myLargeModalLabel">Validated Applications</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">

                    <?php
                    $attributes = array('class' => 'parsley-examples');
                    if ($this->session->position == "School") {
                        echo form_open('pages/school_generate_report/');
                    } else {
                        echo form_open('pages/district_generate_report/');
                    }
                    ?>
                    <div class="hrp-field">
                        <label class="hrp-label" for="fy">Fiscal Year</label>
                        <select class="form-control" name="fy" required>
                            <option></option>
                            <?php
                            $firstYear = (int)date('Y');
                            $lastYear = $firstYear + 5;

                            for ($i = $firstYear; $i <= $lastYear; $i++) {
                                echo '<option value=' . $i . '>' . $i . '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <div class="text-right">
                        <input type="submit" name="submit" value="Submit" class="hrp-btn hrp-btn-primary">
                    </div>
                    </form>

                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->

    <div class="modal fade gs hrp-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myLargeModalLabel">Generate School List</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">

                    <?php
                    $attributes = array('class' => 'parsley-examples');
                    echo form_open('pages/dgr/');
                    ?>

                    <div class="hrp-field">
                        <label class="hrp-label" for="fy">Fiscal Year</label>
                        <select class="form-control" name="fy" required>
                            <option></option>
                            <?php
                            $firstYear = date('Y');
                            $lastYear = $firstYear + 5;

                            for ($i = $firstYear; $i <= $lastYear; $i++) {
                                echo '<option value=' . $i . '>' . $i . '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <div class="text-right">
                        <input type="submit" name="submit" value="Submit" class="hrp-btn hrp-btn-primary">
                    </div>
                    </form>

                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->

    <!--  Edit Vacancies -->
    <div class="modal fade edit_vacancy_file hrp-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myLargeModalLabel"><i class="mdi mdi-file-replace-outline"></i> Job Vacancy Attachment</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">

                    <?php
                    $attributes = array('class' => 'parsley-examples');
                    echo form_open_multipart('Pages/jobVacancy_file_update', $attributes);
                    ?>

                    <div class="hrp-field">
                        <label class="hrp-label">Attachment</label>
                        <input type="file" class="form-control" name="file">
                        <span class="hrp-help">Uploading a new file replaces the current attachment.</span>
                    </div>
                    <input type="hidden" name="jobID" id="id">

                    <div class="text-right">
                        <input type="submit" name="submit" value="Submit" class="hrp-btn hrp-btn-primary">
                    </div>
                    </form>

                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->

    <!--  Announcement / Remarks -->
    <?php if ($is_hr) : ?>
        <div class="modal fade hrp-modal" id="announcementModal" tabindex="-1" role="dialog" aria-labelledby="announcementModalLabel" style="display: none;" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="announcementModalLabel"><i class="mdi mdi-bullhorn-outline"></i> Announcement / Remarks</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    </div>
                    <div class="modal-body">

                        <?php
                        $attributes = array('class' => 'parsley-examples');
                        echo form_open('Pages/jobVacancy_announcement_update', $attributes);
                        ?>

                        <div class="hrp-field">
                            <label class="hrp-label">Position</label>
                            <input type="text" class="form-control" id="ann-position" readonly>
                        </div>

                        <div class="hrp-field">
                            <label class="hrp-label" for="ann-text">Announcement / Remarks</label>
                            <textarea class="form-control" name="announcement" id="ann-text" rows="5" placeholder="e.g. Schedule of demonstration teaching, list of requirements, or any advisory for this position."></textarea>
                            <span class="hrp-help">Everyone who applied for this position sees this on their dashboard. Leave it blank to remove the announcement.</span>
                        </div>

                        <input type="hidden" name="jobID" id="ann-job-id">

                        <div class="text-right">
                            <input type="submit" name="submit" value="Save" class="hrp-btn hrp-btn-primary">
                        </div>
                        </form>

                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->
    <?php endif; ?>

    <!--  Search Applicantion -->
    <div class="modal fade aa hrp-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myLargeModalLabel"><i class="mdi mdi-account-search-outline"></i> Search Applicant</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">

                    <?php
                    $attributes = array('class' => 'parsley-examples');
                    echo form_open('Pages/applicant_applications', $attributes);
                    ?>

                    <div class="hrp-field">
                        <label class="hrp-label">Record No.</label>
                        <input type="text" class="form-control" name="record_no" placeholder="Applicant record number">
                    </div>

                    <?php $position = $this->session->position;
                    if ($position === 'Evaluator') : ?>
                        <div class="hrp-field">
                            <label class="hrp-label">Year</label>
                            <select class="form-control" name="fy" required>
                                <option></option>
                                <?php
                                $firstYear = (int)date('Y');
                                $lastYear = $firstYear + 5;

                                for ($i = $firstYear; $i <= $lastYear; $i++) {
                                    echo '<option';
                                    if ($i == date('Y')) {
                                        echo " selected ";
                                    }
                                    echo ' value=' . $i . '>' . $i . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                    <?php endif; ?>

                    <div class="text-right">
                        <input type="submit" name="submit" value="Search" class="hrp-btn hrp-btn-primary">
                    </div>
                    </form>

                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->

    <script>
        $(document).ready(function() {
            $("#schooledit option").hide();

            $("#distedit").change(function() {
                var val = $(this).val();
                $("#schooledit option").hide();
                $("#schooledit").val("");
                $("#schooledit [data-distedit='" + val + "']").show(); //show options where attribute value matches.
                $("#schooledit").change();
            });


        });
    </script>

    <script>
        $(document).ready(function() {
            $("#school option").hide();

            $("#dist").change(function() {
                var val = $(this).val();
                $("#school option").hide();
                $("#school").val("");
                $("#school [data-dist='" + val + "']").show(); //show options where attribute value matches.
                $("#school").change();
            });

            $("#distme").change(function() {
                var val = $(this).val();
                $("#school option").hide();
                $("#school").val("");
                $("#school [data-dist='" + val + "']").show(); //show options where attribute value matches.
                $("#school").change();
            });


        });
    </script>

    <script type="text/javascript">
        $(document).on("click", ".open-AddBookDialog", function() {
            var myBookId = $(this).data('id');
            $(".modal-body #id").val(myBookId);


        });
    </script>

    <script>
        $(document).ready(function() {

            /* ---------- vacancies table ---------- */
            var $jv = $('#jv-table');
            var jvTable = null;

            // closed postings stay out of the list until the pill asks for them
            var jvStatus = $('#jv-status-tabs').data('default');
            jvStatus = (jvStatus === undefined || jvStatus === null) ? '' : String(jvStatus);

            $.fn.dataTable.ext.search.push(function(settings, rowData, dataIndex) {
                if (settings.nTable.id !== 'jv-table' || jvStatus === '') {
                    return true;
                }
                return String($(settings.aoData[dataIndex].nTr).data('status')) === jvStatus;
            });

            if ($jv.length) {
                jvTable = $jv.DataTable({
                    pageLength: 25,
                    lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
                    order: [[2, 'desc']],
                    columnDefs: [{ orderable: false, targets: [5, 6, 7] }],
                    language: { search: '', searchPlaceholder: 'Search vacancies...' },
                    dom: "<'row'<'col-sm-6'l><'col-sm-6'f>>tp<'row'<'col-sm-12'i>>"
                });
            }

            $('#jv-status-tabs .hrp-tab').each(function() {
                if (String($(this).data('status')) === jvStatus) {
                    $(this).addClass('is-active');
                }
            }).on('click', function() {
                var status = $(this).data('status');
                jvStatus = (status === undefined || status === null) ? '' : String(status);

                $('#jv-status-tabs .hrp-tab').removeClass('is-active');
                $(this).addClass('is-active');

                if (jvTable) { jvTable.draw(); }
            });

            /* ---------- row actions modal ---------- */
            $(document).on('click', '.hrp-actions-btn', function() {
                var jobId = $(this).data('job');
                var $menu = $('#hrp-row-menus .hrp-row-menu[data-job="' + jobId + '"]');

                $('#hrp-actions-title').text($(this).data('title') || 'Actions');
                $('#hrp-actions-list').html($menu.html() || '');
                $('#hrp-actions-modal').modal('show');
            });

            // entries that open another modal have to wait for this one to close,
            // otherwise the two backdrops stack
            $(document).on('click', '#hrp-actions-list [data-modal]', function(e) {
                e.preventDefault();
                var target = $(this).data('modal');

                $('#hrp-actions-modal')
                    .one('hidden.bs.modal', function() {
                        $(target).modal('show');
                    })
                    .modal('hide');
            });

            /* ---------- announcement modal ---------- */
            // .attr() rather than .data(), so a numeric announcement stays a string
            $(document).on('click', '.hrp-ann-btn', function() {
                $('#ann-job-id').val($(this).attr('data-job'));
                $('#ann-position').val($(this).attr('data-title') || '');
                $('#ann-text').val($(this).attr('data-announcement') || '');
                $('#announcementModal').modal('show');
            });

            $('[data-toggle="tooltip"]').tooltip();

            /* ---------- Job Vacancy Posting modal ---------- */
            var $groupSelect = $('#position-select');
            var $titleSelect = $('#job-title-select');

            if (!$groupSelect.length || !$titleSelect.length) { return; }

            // position titles maintained under Page/positionSettings
            var POSITIONS = <?= json_encode($positionOptions, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;

            var $modal = $('#addVacancyModal');

            function select2Opts(searchThreshold) {
                return {
                    dropdownParent: $modal,
                    width: '100%',
                    minimumResultsForSearch: (searchThreshold === undefined ? 8 : searchThreshold)
                };
            }

            $groupSelect.select2(select2Opts());
            $('#job_type1').select2(select2Opts());
            $('#job_type').select2(select2Opts());
            $('#empType').select2(select2Opts());
            $('#sy').select2(select2Opts());
            $('#ecp').select2(select2Opts(Infinity));

            // select2 hides the underlying <select>, so the title options are rebuilt
            // per group instead of being hidden with CSS the way the plain dropdown did
            function rebuildTitles(groupId) {
                var matches = POSITIONS.filter(function(p) {
                    return String(p.pos_id) === String(groupId);
                });

                if ($titleSelect.data('select2')) {
                    $titleSelect.select2('destroy');
                }

                $titleSelect.empty().append(new Option('', '', false, false));

                matches.forEach(function(p) {
                    var label = p.sg === '' ? p.title : p.title + '  (SG ' + p.sg + ')';
                    $titleSelect.append(
                        $('<option></option>')
                            .attr('value', p.title)
                            .attr('data-position_id', p.id)
                            .text(label)
                    );
                });

                $('#pos-id-input').val('');
                $titleSelect.select2(select2Opts(0));
            }

            $groupSelect.on('change', function() {
                var selectedPosition = $(this).val() || '';

                $('#teaching-group-type').toggle(selectedPosition === '1');
                $('#admin-group-type').toggle(selectedPosition === '2');

                if (selectedPosition !== '1') { $('#job_type1').val('0').trigger('change.select2'); }
                if (selectedPosition !== '2') { $('#job_type').val('0').trigger('change.select2'); }

                rebuildTitles(selectedPosition);
            });

            $titleSelect.on('change', function() {
                $('#pos-id-input').val($(this).find('option:selected').data('position_id') || '');
            });

            rebuildTitles('');
        });
    </script>
