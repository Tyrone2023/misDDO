<?php
// School dashboard. `$data`..`$data3` are the personnel counts, `$data4` the
// announcements and `$data5` the school record — all supplied by Pages::view().
// Each is a result array that can legitimately come back empty, hence the
// defensive reads below rather than indexing straight into [0].
$schoolName = (!empty($data5) && isset($data5[0]->schoolName)) ? $data5[0]->schoolName : 'School Dashboard';

$totalActive = (!empty($data)  && isset($data[0]->Counts))  ? (int) $data[0]->Counts  : 0;
$teaching    = (!empty($data1) && isset($data1[0]->Counts)) ? (int) $data1[0]->Counts : 0;
$nonTeaching = (!empty($data2) && isset($data2[0]->Counts)) ? (int) $data2[0]->Counts : 0;
$inactive    = (!empty($data3) && isset($data3[0]->Counts)) ? (int) $data3[0]->Counts : 0;

$announcements = !empty($data4) ? $data4 : array();

$cards = array(
    array(
        'value' => $totalActive,
        'label' => 'Total Active Employees',
        'sub'   => 'Currently in service',
        'link'  => 'Page/employeelist_active',
        'icon'  => 'mdi-account-multiple-check',
        'tone'  => 'mis-t-blue',
    ),
    array(
        'value' => $teaching,
        'label' => 'Teaching Personnel',
        'sub'   => 'Teachers and master teachers',
        'link'  => 'Pages/tp',
        'icon'  => 'mdi-account-multiple',
        'tone'  => 'mis-t-purple',
    ),
    array(
        'value' => $nonTeaching,
        'label' => 'Non-Teaching Personnel',
        'sub'   => 'Support and admin staff',
        'link'  => 'Pages/ntp',
        'icon'  => 'mdi-account-tie-outline',
        'tone'  => 'mis-t-sky',
    ),
    array(
        'value' => $inactive,
        'label' => 'Inactive',
        'sub'   => 'Separated, retired or transferred',
        'link'  => 'Pages/school_inactive',
        'icon'  => 'mdi-account-off-outline',
        'tone'  => 'mis-t-grey',
    ),
);

$quickLinks = array(
    array('link' => 'Page/schoolProfile',      'icon' => 'mdi-school-outline',       'label' => 'School Profile',       'sub' => 'Update school information'),
    array('link' => 'Page/jobVacancy',         'icon' => 'mdi-briefcase-outline',    'label' => 'Job Vacancies',        'sub' => 'Posted positions'),
    array('link' => 'Page/school_allocations2','icon' => 'mdi-cash-multiple',        'label' => 'Fund Allocation',      'sub' => 'School MOOE allocation'),
    array('link' => 'page/fy_setting_school',  'icon' => 'mdi-notebook-multiple',    'label' => 'Implementation Plans', 'sub' => 'Prepare and submit the AIP'),
);
?>

<!-- ============================================================== -->
<!-- Start Page Content here -->
<!-- ============================================================== -->

<div class="content-page">
    <div class="content">

        <!-- Start Content-->
        <div class="container-fluid mis-shell">

            <div class="mis-hero">
                <div class="mis-hero-text">
                    <span class="mis-hero-eyebrow"><i class="mdi mdi-view-dashboard-outline"></i> School Dashboard</span>
                    <h3 class="mis-hero-title"><?= html_escape($schoolName); ?></h3>
                    <p class="mis-hero-sub">
                        Personnel summary and division announcements for your school.
                        Select any figure to open the matching list.
                    </p>
                </div>
                <div class="mis-hero-aside">
                    <div class="mis-hero-stat">
                        <span class="mis-hero-stat-value"><?= number_format($totalActive); ?></span>
                        <span class="mis-hero-stat-label">Active</span>
                    </div>
                    <div class="mis-hero-stat">
                        <span class="mis-hero-stat-value"><?= number_format($teaching + $nonTeaching); ?></span>
                        <span class="mis-hero-stat-label">Personnel</span>
                    </div>
                </div>
            </div>

            <?php if ($this->session->flashdata('success')) : ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <?= $this->session->flashdata('success'); ?>
                </div>
            <?php endif; ?>

            <?php if ($this->session->flashdata('danger')) : ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <?= $this->session->flashdata('danger'); ?>
                </div>
            <?php endif; ?>

            <div class="mis-grid">
                <?php foreach ($cards as $card) : ?>
                    <a href="<?= base_url() . $card['link']; ?>" class="mis-card <?= $card['tone']; ?>">
                        <div class="mis-card-top">
                            <span class="mis-card-num"><?= number_format($card['value']); ?></span>
                            <span class="mis-card-ico"><i class="mdi <?= $card['icon']; ?>"></i></span>
                        </div>
                        <div class="mis-card-label"><?= $card['label']; ?></div>
                        <div class="mis-card-sub"><?= $card['sub']; ?></div>
                        <span class="mis-card-go">View list <i class="mdi mdi-arrow-right"></i></span>
                    </a>
                <?php endforeach; ?>
            </div>

            <div class="mis-panel">
                <div class="mis-panel-head">
                    <div>
                        <h5 class="mis-panel-title"><i class="mdi mdi-flash-outline"></i> Quick access</h5>
                        <p class="mis-panel-sub">The screens this account uses most often</p>
                    </div>
                </div>
                <div class="mis-panel-body">
                    <div class="mis-links">
                        <?php foreach ($quickLinks as $q) : ?>
                            <a href="<?= base_url() . $q['link']; ?>" class="mis-link">
                                <i class="mdi <?= $q['icon']; ?>"></i>
                                <span><?= $q['label']; ?><small><?= $q['sub']; ?></small></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="mis-panel">
                <div class="mis-panel-head">
                    <div>
                        <h5 class="mis-panel-title"><i class="mdi mdi-bullhorn-outline"></i> Announcements</h5>
                        <p class="mis-panel-sub">Notices posted by the division office</p>
                    </div>
                    <span class="mis-chip mis-chip-amber">
                        <?= count($announcements); ?> post<?= count($announcements) === 1 ? '' : 's'; ?>
                    </span>
                </div>
                <div class="mis-panel-body">
                    <?php if (empty($announcements)) : ?>
                        <div class="mis-empty">
                            <i class="mdi mdi-bullhorn-outline"></i>
                            <p>No announcements have been posted yet.</p>
                        </div>
                    <?php else : ?>
                        <?php foreach ($announcements as $row) : ?>
                            <div class="mb-4">
                                <h6 class="mis-card-label mb-2"><?= html_escape($row->title); ?></h6>
                                <a href="<?= base_url(); ?>uploads/announcements/<?= $row->fileAttachment; ?>" target="_blank">
                                    <img src="<?= base_url(); ?>uploads/announcements/<?= $row->fileAttachment; ?>"
                                         class="img-fluid rounded"
                                         alt="<?= html_escape($row->title); ?>"
                                         style="width:100%;">
                                </a>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

        </div>
        <!-- end container-fluid -->

    </div>
    <!-- end content -->
