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

// per-vacancy signatory counts, handed over by the controller in one query so
// the Actions menu can badge them without a query per row
$signatory_counts = isset($signatory_counts) && is_array($signatory_counts) ? $signatory_counts : array();

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
    .dropdown-submenu.is-open > .dropdown-menu { display: block; }

    /* toolbar dropdowns tuned to the card look */
    .hrp-toolbar {
        position: relative;
        display: flex;
        flex-wrap: wrap;
        gap: .45rem;
        align-items: center;
    }
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
    .hrp-toolbar .dropdown-submenu > .dropdown-item {
        display: flex;
        align-items: center;
        gap: .4rem;
    }
    .hrp-toolbar .dropdown-submenu > .dropdown-item::after {
        display: inline-block;
        width: 0;
        height: 0;
        margin-left: auto;
        content: "";
        border-top: .3em solid transparent;
        border-right: 0;
        border-bottom: .3em solid transparent;
        border-left: .3em solid;
        transition: transform .15s ease;
    }
    .hrp-toolbar .dropdown-item:hover { background: #f1f4f8; color: #313a46; }
    .hrp-toolbar .dropdown-menu .dropdown-menu { max-height: 300px; overflow-y: auto; overflow-x: hidden; min-width: 280px; }
    .hrp-toolbar > .btn-group > .dropdown-menu.hrp-viewport-menu {
        position: fixed !important;
        top: var(--hrp-menu-top, 0) !important;
        right: auto !important;
        bottom: auto !important;
        left: var(--hrp-menu-left, 0) !important;
        transform: none !important;
        will-change: auto !important;
    }

    /* Wide view: the report menu scrolls inside the viewport and each fly-out is
       pinned to the viewport as well, so no report row can land off-screen (or
       behind .content-page's overflow:hidden) where it cannot be clicked. */
    @media (min-width: 992px) {
        .hrp-toolbar > .btn-group > .dropdown-menu.hrp-viewport-menu {
            max-height: var(--hrp-menu-max-h, none);
            overflow-x: hidden;
            overflow-y: auto;
        }
        .hrp-toolbar .dropdown-submenu > .dropdown-menu.hrp-flyout-fixed {
            position: fixed !important;
            top: var(--hrp-fly-top, 0) !important;
            left: var(--hrp-fly-left, 0) !important;
            right: auto !important;
            bottom: auto !important;
            margin: 0 !important;
            transform: none !important;
            max-height: var(--hrp-fly-max-h, 300px);
        }
        /* the caret has to admit it when the fly-out opened on the other side */
        .hrp-toolbar .dropdown-submenu.hrp-flyout-left > .dropdown-item::after {
            transform: scaleX(-1);
        }
    }
    .hrp-toolbar-group-label {
        font-size: .68rem;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: #98a6ad;
        font-weight: 600;
        margin-right: .15rem;
    }

    /* Keep the original report menus, but let them follow the available view. */
    @media (max-width: 991.98px) {
        .hrp-toolbar > .hrp-btn {
            flex: 1 1 180px;
            justify-content: center;
            min-width: 0;
            text-align: center;
            white-space: normal;
        }
        .hrp-toolbar > .btn-group {
            position: static;
            flex: 1 1 220px;
            min-width: 0;
        }
        .hrp-toolbar > .btn-group > .hrp-btn {
            justify-content: center;
            width: 100%;
            min-width: 0;
            text-align: center;
            white-space: normal;
        }
        .hrp-toolbar > .btn-group > .dropdown-menu {
            top: calc(100% + .45rem) !important;
            right: 0 !important;
            left: 0 !important;
            width: 100%;
            min-width: 0;
            max-height: calc(100vh - 120px);
            overflow-x: hidden;
            overflow-y: auto;
            transform: none !important;
            -webkit-overflow-scrolling: touch;
        }
        .hrp-toolbar.hrp-dropdown-up > .btn-group.show > .dropdown-menu {
            top: auto !important;
            bottom: calc(100% + .45rem) !important;
        }
        .hrp-toolbar .dropdown-submenu:hover > .dropdown-menu { display: none; }
        .hrp-toolbar .dropdown-submenu.is-open > .dropdown-menu { display: block; }
        .hrp-toolbar .dropdown-submenu > .dropdown-menu {
            position: static !important;
            float: none;
            width: 100%;
            min-width: 0;
            max-height: 42vh;
            margin: .3rem 0 .4rem !important;
            border: 1px solid #e3e9f1;
            border-left: 3px solid #9bb8de;
            border-radius: 8px;
            box-shadow: none;
            transform: none !important;
        }
        .hrp-toolbar .dropdown-submenu > .dropdown-item::after {
            transform: rotate(90deg);
        }
        .hrp-toolbar .dropdown-submenu.is-open > .dropdown-item::after {
            transform: rotate(-90deg);
        }
    }

    @media (max-width: 575.98px) {
        .hrp-toolbar > .hrp-btn,
        .hrp-toolbar > .btn-group { flex-basis: 100%; }
        .hrp-toolbar .dropdown-item {
            padding: .55rem .7rem;
            white-space: normal;
            overflow-wrap: anywhere;
        }
    }

    /* application details column — one button, the fields live in the modal */
    .hrp-details-btn {
        white-space: nowrap;
        background: #eef4fd;
        border-color: #d9e6f9;
        color: #2c5282;
        font-weight: 600;
    }
    .hrp-details-btn:hover { background: #e2edfb; color: #234069; }
    .hrp-details-btn .mdi { color: #3b7dd8; }

    /* vacancy details modal */
    .hrp-detail-list { display: flex; flex-direction: column; gap: .1rem; }
    .hrp-detail-row {
        display: flex;
        gap: 1rem;
        padding: .6rem 0;
        border-bottom: 1px solid #f1f4f8;
    }
    .hrp-detail-row:last-child { border-bottom: 0; }
    .hrp-detail-label {
        flex: 0 0 40%;
        font-size: .74rem;
        text-transform: uppercase;
        letter-spacing: .05em;
        color: #98a6ad;
        font-weight: 600;
        padding-top: .1rem;
    }
    .hrp-detail-value {
        flex: 1 1 auto;
        font-size: .87rem;
        color: #4a5568;
        line-height: 1.5;
        word-break: break-word;
    }

    /* announcement/remarks column — one button, the text lives in the modal */
    .hrp-ann-cell { white-space: nowrap; }
    .hrp-ann-btn { white-space: nowrap; }
    .hrp-ann-btn-posted {
        background: #fdf3e2;
        border-color: #f0dcb4;
        color: #a86c14;
        font-weight: 600;
    }
    .hrp-ann-btn-posted:hover { background: #fbe9cd; color: #8c580e; }
    .hrp-ann-btn-posted .mdi { color: #c07d15; }

    /* readable by the DataTables search box, invisible in the cell */
    .hrp-ann-search {
        position: absolute;
        width: 1px; height: 1px;
        padding: 0; margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        border: 0;
    }

    /* announcement modal */
    .hrp-ann-head {
        display: flex; flex-direction: column; gap: .15rem;
        padding-bottom: .7rem;
        margin-bottom: .9rem;
        border-bottom: 1px solid #eef1f6;
    }
    .hrp-ann-head-pos { font-size: .95rem; font-weight: 600; color: #313a46; }
    .hrp-ann-head-meta { font-size: .74rem; color: #98a6ad; }
    .hrp-ann-head-meta:empty { display: none; }

    .hrp-ann-read {
        font-size: .87rem;
        color: #4a5568;
        line-height: 1.6;
        white-space: pre-wrap;
        word-break: break-word;
        background: #fdf9ef;
        border-left: 3px solid #e0b34d;
        border-radius: 0 8px 8px 0;
        padding: .8rem 1rem;
        max-height: 45vh;
        overflow-y: auto;
    }
    .hrp-ann-none {
        display: flex; flex-direction: column; align-items: center; gap: .4rem;
        padding: 2rem 1rem;
        color: #98a6ad;
        font-size: .85rem;
        text-align: center;
    }
    .hrp-ann-none i { font-size: 2.1rem; opacity: .5; }

    .hrp-ann-actions {
        display: flex; align-items: center; gap: .45rem;
        margin-top: 1.1rem;
    }
    .hrp-ann-actions-spacer { margin-left: auto; }
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
                                <table id="jv-table" class="table hrp-table nowrap" style="width:100%;">
                                    <thead>
                                        <tr>
                                            <th>Position</th>
                                            <?php // hidden: keeps the default "newest first" sort and lets the
                                                  // search box still reach type/date/office now that the
                                                  // details live in a modal instead of a responsive child row ?>
                                            <th>Posted</th>
                                            <th class="text-center">Application Details</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Attachment</th>
                                            <th class="text-center">Announcement</th>
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

                                            // employment type, date posted and office/bureau are shown in the
                                            // details modal so the row stays narrow enough to fit without the
                                            // responsive expand control
                                            $datePostedLabel = trim((string) $row->datePosted) !== ''
                                                ? date('M d, Y', strtotime($row->datePosted))
                                                : '';
                                            $detailTitle = trim($row->jobTitle . ($typeLabel !== '' ? ' - ' . $typeLabel : ''));
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
                                                <td data-order="<?= html_escape($row->datePosted); ?>">
                                                    <?= html_escape($row->datePosted . ' ' . $datePostedLabel . ' ' . $row->empType . ' ' . $row->assign); ?>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="hrp-btn hrp-btn-sm hrp-details-btn"
                                                            data-title="<?= html_escape($detailTitle); ?>"
                                                            data-sy="<?= html_escape($row->sy); ?>"
                                                            data-emptype="<?= html_escape($row->empType); ?>"
                                                            data-posted="<?= html_escape($datePostedLabel); ?>"
                                                            data-office="<?= html_escape($row->assign); ?>">
                                                        <i class="mdi mdi-information-outline"></i> View Details
                                                    </button>
                                                </td>
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
                                                // for this posting sees it on their dashboard. The text itself
                                                // lives in the modal — the cell only carries the button that
                                                // opens it, so long advisories no longer stretch the row.
                                                $announcement = isset($row->announcement) ? trim((string) $row->announcement) : '';
                                                $annBy        = isset($row->announcement_by) ? trim((string) $row->announcement_by) : '';
                                                $annAt        = isset($row->announcement_at) ? trim((string) $row->announcement_at) : '';
                                                $annAtLabel   = $annAt !== '' ? date('M d, Y g:i A', strtotime($annAt)) : '';
                                                $annPosition  = trim($row->jobTitle . ($typeLabel !== '' ? ' - ' . $typeLabel : ''));
                                                ?>
                                                <td class="hrp-ann-cell text-center">
                                                    <?php if ($announcement !== '' || $is_hr) : ?>
                                                        <button type="button"
                                                                class="hrp-btn hrp-btn-sm hrp-ann-btn<?= $announcement !== '' ? ' hrp-ann-btn-posted' : ''; ?>"
                                                                data-job="<?= $row->jobID; ?>"
                                                                data-title="<?= html_escape($annPosition); ?>"
                                                                data-announcement="<?= html_escape($announcement); ?>"
                                                                data-by="<?= html_escape($annBy); ?>"
                                                                data-at="<?= html_escape($annAtLabel); ?>">
                                                            <i class="mdi <?= $announcement !== '' ? 'mdi-bullhorn' : 'mdi-bullhorn-outline'; ?>"></i>
                                                            <?php if ($announcement !== '') : ?>
                                                                <?= $is_hr ? 'View / Edit' : 'View'; ?>
                                                            <?php else : ?>
                                                                Add
                                                            <?php endif; ?>
                                                        </button>
                                                        <?php if ($announcement !== '') : ?>
                                                            <!-- keeps the text reachable from the table's search box -->
                                                            <span class="hrp-ann-search"><?= html_escape($announcement); ?></span>
                                                        <?php endif; ?>
                                                    <?php else : ?>
                                                        <span class="hrp-muted">&mdash;</span>
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

                                                            <?php // signatories printed on this vacancy's RQA/CAR reports ?>
                                                            <a class="hrp-action-item" href="<?= base_url(); ?>VacancySignatories/index/<?= $row->jobID; ?>">
                                                                <i class="mdi mdi-draw hrp-i-teal"></i> Signatories
                                                                <?php if (!empty($signatory_counts[$row->jobID])) : ?><span class="hrp-item-count"><?= (int) $signatory_counts[$row->jobID]; ?></span><?php endif; ?>
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

    <!--  Announcement / Remarks — HR edits here, everyone else reads -->
    <div class="modal fade hrp-modal" id="announcementModal" tabindex="-1" role="dialog" aria-labelledby="announcementModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="announcementModalLabel"><i class="mdi mdi-bullhorn-outline"></i> Announcement / Remarks</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">

                    <div class="hrp-ann-head">
                        <span class="hrp-ann-head-pos" id="ann-position"></span>
                        <span class="hrp-ann-head-meta" id="ann-meta"></span>
                    </div>

                    <?php if ($is_hr) : ?>

                        <?php
                        $attributes = array('class' => 'parsley-examples');
                        echo form_open('Pages/jobVacancy_announcement_update', $attributes);
                        ?>

                        <div class="hrp-field">
                            <label class="hrp-label" for="ann-text">Announcement / Remarks</label>
                            <textarea class="form-control" name="announcement" id="ann-text" rows="6" placeholder="e.g. Schedule of demonstration teaching, list of requirements, or any advisory for this position."></textarea>
                            <span class="hrp-help">Everyone who applied for this position sees this on their dashboard. Leave it blank to remove the announcement.</span>
                        </div>

                        <input type="hidden" name="jobID" id="ann-job-id">

                        <div class="hrp-ann-actions">
                            <button type="button" class="hrp-btn hrp-btn-sm hrp-btn-ghost-danger" id="ann-remove">
                                <i class="mdi mdi-trash-can-outline"></i> Remove
                            </button>
                            <span class="hrp-ann-actions-spacer"></span>
                            <button type="button" class="hrp-btn hrp-btn-sm" data-dismiss="modal">Cancel</button>
                            <input type="submit" name="submit" value="Save" class="hrp-btn hrp-btn-sm hrp-btn-primary">
                        </div>
                        </form>

                    <?php else : ?>

                        <div class="hrp-ann-read" id="ann-read"></div>
                        <div class="hrp-ann-none" id="ann-empty">
                            <i class="mdi mdi-bullhorn-outline"></i>
                            <span>No announcement has been posted for this position yet.</span>
                        </div>

                        <div class="hrp-ann-actions">
                            <span class="hrp-ann-actions-spacer"></span>
                            <button type="button" class="hrp-btn hrp-btn-sm" data-dismiss="modal">Close</button>
                        </div>

                    <?php endif; ?>

                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->

    <!--  Vacancy details -->
    <div class="modal fade hrp-modal hrp-modal-compact" id="jvDetailsModal" tabindex="-1" role="dialog" aria-labelledby="jvDetailsModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="jvDetailsModalLabel"><i class="mdi mdi-information-outline"></i> Vacancy Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">

                    <div class="hrp-ann-head">
                        <span class="hrp-ann-head-pos" id="jvd-position"></span>
                        <span class="hrp-ann-head-meta" id="jvd-sy"></span>
                    </div>

                    <div class="hrp-detail-list">
                        <div class="hrp-detail-row">
                            <div class="hrp-detail-label">Employment Type</div>
                            <div class="hrp-detail-value" id="jvd-emptype"></div>
                        </div>
                        <div class="hrp-detail-row">
                            <div class="hrp-detail-label">Date Posted</div>
                            <div class="hrp-detail-value" id="jvd-posted"></div>
                        </div>
                        <div class="hrp-detail-row">
                            <div class="hrp-detail-label">Office/Bureau/Service/Unit where the vacancy exists</div>
                            <div class="hrp-detail-value" id="jvd-office"></div>
                        </div>
                    </div>

                    <div class="hrp-ann-actions">
                        <span class="hrp-ann-actions-spacer"></span>
                        <button type="button" class="hrp-btn hrp-btn-sm" data-dismiss="modal">Close</button>
                    </div>

                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->

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

            /* ---------- responsive report dropdowns ---------- */
            var compactReportMenus = window.matchMedia('(max-width: 991.98px)');
            var $reportToolbar = $('.hrp-toolbar');

            function closeReportSubmenus($scope) {
                var $subs = $scope.filter('.dropdown-submenu').add($scope.find('.dropdown-submenu'));

                $subs.children('.dropdown-menu').each(function() {
                    $(this).removeClass('hrp-flyout-fixed');
                    this.style.removeProperty('--hrp-fly-top');
                    this.style.removeProperty('--hrp-fly-left');
                    this.style.removeProperty('--hrp-fly-max-h');
                });

                $subs.removeClass('is-open hrp-flyout-left')
                    .children('.dropdown-item')
                    .attr('aria-expanded', 'false');
            }

            // A compact view cannot fit a second fly-out beside the first menu.
            // The first tap expands it in place; a second tap follows a real link.
            $(document).on('click', '.hrp-toolbar .dropdown-submenu > .dropdown-item', function(e) {
                var $trigger = $(this);
                var $submenu = $trigger.parent('.dropdown-submenu');
                var hasRealLink = ($trigger.attr('href') || '#') !== '#';

                if (compactReportMenus.matches) {
                    if (!$submenu.hasClass('is-open')) {
                        e.preventDefault();
                        e.stopPropagation();

                        closeReportSubmenus($submenu.siblings('.dropdown-submenu'));
                        $submenu.addClass('is-open');
                        $trigger.attr('aria-expanded', 'true');
                        return;
                    }

                    if (!hasRealLink) {
                        e.preventDefault();
                        e.stopPropagation();
                        $submenu.removeClass('is-open');
                        $trigger.attr('aria-expanded', 'false');
                    }
                    return;
                }

                // Preserve normal destinations such as Qualified List Promotion.
                if (!hasRealLink) {
                    e.preventDefault();
                    e.stopPropagation();
                    $submenu.toggleClass('is-open');
                    $trigger.attr('aria-expanded', $submenu.hasClass('is-open') ? 'true' : 'false');

                    if ($submenu.hasClass('is-open')) {
                        placeFlyout($submenu);
                    }
                }
            });

            // On wide views the fly-out is pinned to the viewport, measured and
            // placed in the same frame so it never moves once it is on screen.
            // The old version also listened on focusin and re-placed a frame
            // later, so the mousedown that focuses a report link shifted the
            // fly-out out from under the pointer and the click never landed.
            function placeFlyout($submenu) {
                if (compactReportMenus.matches) { return; }

                var $fly = $submenu.children('.dropdown-menu');
                if (!$fly.length) { return; }

                var gutter = 12;
                var fly = $fly.addClass('hrp-flyout-fixed').get(0);
                var room = Math.max(160, window.innerHeight - (gutter * 2));

                fly.style.setProperty('--hrp-fly-max-h', Math.floor(Math.min(300, room)) + 'px');

                var row = $submenu.get(0).getBoundingClientRect();
                var flyRect = fly.getBoundingClientRect();
                var viewWidth = document.documentElement.clientWidth;
                var left = row.right;

                // only swap sides when the fly-out truly cannot fit on the right
                if (left + flyRect.width > viewWidth - gutter) {
                    left = row.left - flyRect.width;
                    $submenu.addClass('hrp-flyout-left');
                } else {
                    $submenu.removeClass('hrp-flyout-left');
                }

                left = Math.min(left, viewWidth - gutter - flyRect.width);
                var top = Math.min(row.top, window.innerHeight - gutter - flyRect.height);

                fly.style.setProperty('--hrp-fly-left', Math.floor(Math.max(gutter, left)) + 'px');
                fly.style.setProperty('--hrp-fly-top', Math.floor(Math.max(gutter, top)) + 'px');
            }

            $(document).on('mouseenter', '.hrp-toolbar .dropdown-submenu', function() {
                placeFlyout($(this));
            });

            function fitOpenReportMenu($group) {
                var $menu = $group.children('.dropdown-menu');
                var trigger = $group.children('.dropdown-toggle').get(0);
                if (!$menu.length || !trigger || !$reportToolbar.length) { return; }

                if (compactReportMenus.matches) {
                    $menu.removeClass('hrp-viewport-menu');
                    var toolbarRect = $reportToolbar.get(0).getBoundingClientRect();
                    var roomBelow = window.innerHeight - toolbarRect.bottom - 12;
                    var roomAbove = toolbarRect.top - 12;
                    var openAbove = roomBelow < 260 && roomAbove > roomBelow;
                    var availableRoom = openAbove ? roomAbove : roomBelow;

                    $reportToolbar.toggleClass('hrp-dropdown-up', openAbove);
                    $menu.css('max-height', Math.max(140, Math.floor(availableRoom)) + 'px');
                    return;
                }

                // Bootstrap flips a tall menu above its button when the space below
                // is a little short. At 100% zoom that can put the first reports
                // underneath the fixed top bar. Clamp the original menu inside the
                // visible viewport instead, while keeping nested fly-outs intact.
                var menu = $menu.addClass('hrp-viewport-menu').get(0);
                var triggerRect = trigger.getBoundingClientRect();
                var topbar = document.querySelector('.navbar-custom');
                var topbarBottom = topbar ? topbar.getBoundingClientRect().bottom : 0;
                var safeTop = Math.max(12, Math.ceil(topbarBottom) + 10);

                menu.style.setProperty('--hrp-menu-max-h',
                    Math.max(180, Math.floor(window.innerHeight - safeTop - 12)) + 'px');
                menu.style.setProperty('--hrp-menu-top', Math.ceil(triggerRect.bottom + 5) + 'px');
                menu.style.setProperty('--hrp-menu-left', Math.floor(triggerRect.left) + 'px');

                window.requestAnimationFrame(function() {
                    if (!$group.hasClass('show')) { return; }

                    var menuRect = menu.getBoundingClientRect();
                    var top = triggerRect.bottom + 5;
                    var left = triggerRect.left;
                    var viewportBottom = window.innerHeight - 12;
                    var viewportRight = document.documentElement.clientWidth - 12;

                    if (top + menuRect.height > viewportBottom) {
                        top = viewportBottom - menuRect.height;
                    }
                    if (left + menuRect.width > viewportRight) {
                        left = viewportRight - menuRect.width;
                    }

                    menu.style.setProperty('--hrp-menu-top', Math.max(safeTop, Math.floor(top)) + 'px');
                    menu.style.setProperty('--hrp-menu-left', Math.max(12, Math.floor(left)) + 'px');
                });
            }

            $reportToolbar.on('shown.bs.dropdown', '.btn-group', function() {
                var $group = $(this);
                fitOpenReportMenu($group);

                // the menu scrolls now, so an open fly-out has to follow its row
                $group.children('.dropdown-menu').on('scroll.hrpFlyout', function() {
                    $(this).children('.dropdown-submenu').each(function() {
                        var $submenu = $(this);
                        if ($submenu.hasClass('is-open') || $submenu.is(':hover')) {
                            placeFlyout($submenu);
                        }
                    });
                });
            });

            $reportToolbar.on('hidden.bs.dropdown', '.btn-group', function() {
                $reportToolbar.removeClass('hrp-dropdown-up');
                var $menu = $(this).children('.dropdown-menu');
                $menu.off('scroll.hrpFlyout').removeClass('hrp-viewport-menu').css('max-height', '');
                if ($menu.length) {
                    $menu.get(0).style.removeProperty('--hrp-menu-top');
                    $menu.get(0).style.removeProperty('--hrp-menu-left');
                    $menu.get(0).style.removeProperty('--hrp-menu-max-h');
                }
                closeReportSubmenus($(this));
            });

            $(window).on('resize', function() {
                $reportToolbar.removeClass('hrp-dropdown-up');
                $reportToolbar.children('.btn-group').children('.dropdown-menu').css('max-height', '');
                closeReportSubmenus($reportToolbar);

                var $openGroup = $reportToolbar.children('.btn-group.show');
                if ($openGroup.length) {
                    fitOpenReportMenu($openGroup.first());
                }
            });

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
                    order: [[1, 'desc']],
                    columnDefs: [
                        { orderable: false, targets: [2, 4, 5, 6] },
                        // date/type/office are read from the details modal; the column
                        // stays in the DOM only to drive sorting and the search box
                        { visible: false, searchable: true, targets: 1 }
                    ],
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

            /* ---------- vacancy details modal ---------- */
            $(document).on('click', '.hrp-details-btn', function() {
                var $btn = $(this),
                    dash = '—';

                $('#jvd-position').text($btn.attr('data-title') || '');

                var sy = $btn.attr('data-sy') || '';
                $('#jvd-sy').text(sy === '' ? '' : 'SY ' + sy);

                $('#jvd-emptype').text($btn.attr('data-emptype') || dash);
                $('#jvd-posted').text($btn.attr('data-posted') || dash);
                $('#jvd-office').text($btn.attr('data-office') || dash);

                $('#jvDetailsModal').modal('show');
            });

            /* ---------- announcement modal ---------- */
            // .attr() rather than .data(), so a numeric announcement stays a string
            $(document).on('click', '.hrp-ann-btn', function() {
                var $btn = $(this),
                    text  = $btn.attr('data-announcement') || '',
                    by    = $btn.attr('data-by') || '',
                    at    = $btn.attr('data-at') || '';

                $('#ann-position').text($btn.attr('data-title') || '');

                var meta = (by !== '' && at !== '') ? by + ' • ' + at : (by || at);
                $('#ann-meta').text(meta === '' ? '' : 'Posted by ' + meta);

                // HR: editable form. Everyone else: read-only panel.
                $('#ann-job-id').val($btn.attr('data-job'));
                $('#ann-text').val(text);
                $('#ann-remove').toggle(text !== '');

                $('#ann-read').text(text).toggle(text !== '');
                $('#ann-empty').toggle(text === '');

                $('#announcementModal').modal('show');
            });

            // clearing the box and saving is what removes it — the controller
            // treats an empty announcement as a removal
            $(document).on('click', '#ann-remove', function() {
                if (!confirm('Remove this announcement? Applicants will no longer see it on their dashboard.')) {
                    return;
                }
                // click the real submit input — the form has a control named
                // "submit", which shadows form.submit() and breaks .submit()
                $('#ann-text').val('');
                $(this).closest('form').find('input[type="submit"]').trigger('click');
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
