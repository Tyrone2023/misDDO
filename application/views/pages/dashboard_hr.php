<?php
$redirect_urls = [
    'reg' => 'Pages/view_user',
    'user' => 'Pages/view_employee',
    'SHNS' => 'Page/Health/'
];
if (isset($redirect_urls[$this->session->position])) {
    redirect(base_url() . $redirect_urls[$this->session->position]);
}

// Human Resource Admin landing page. Counts arrive as CI query objects from
// Pages::view(); $data4 is the queue of leave applications awaiting approval.
$isAdmin = ($this->session->position == 'Admin');

$total        = is_object($data)  ? $data->num_rows()  : 0;
$teaching     = is_object($data2) ? $data2->num_rows() : 0;
$nonTeaching  = is_object($data3) ? $data3->num_rows() : 0;
$inactive     = is_object($data1) ? $data1->num_rows() : 0;
$pendingLeave = is_object($data4) ? $data4->num_rows() : 0;

// Only Admin may drill into the personnel lists; everyone else sees the figure
// without a working link, which is how this page has always behaved.
$cards = array(
    array(
        'value' => $total,
        'label' => 'Total Employees',
        'sub'   => 'All personnel on record',
        'link'  => $isAdmin ? 'Pages/personnel' : '',
        'icon'  => 'mdi-account-group-outline',
        'tone'  => 'mis-t-blue',
    ),
    array(
        'value' => $teaching,
        'label' => 'Teaching Personnel',
        'sub'   => 'Teachers and master teachers',
        'link'  => $isAdmin ? 'Pages/personnel_teaching' : '',
        'icon'  => 'mdi-teach',
        'tone'  => 'mis-t-purple',
    ),
    array(
        'value' => $nonTeaching,
        'label' => 'Non-Teaching Personnel',
        'sub'   => 'Support and admin staff',
        'link'  => $isAdmin ? 'Pages/personnel_nonteaching' : '',
        'icon'  => 'mdi-account-tie-outline',
        'tone'  => 'mis-t-sky',
    ),
    array(
        'value' => $inactive,
        'label' => 'Inactive List',
        'sub'   => 'Separated, retired or transferred',
        'link'  => $isAdmin ? 'Pages/personnel_inactive' : '',
        'icon'  => 'mdi-account-off-outline',
        'tone'  => 'mis-t-grey',
    ),
);

$quickLinks = array(
    array('link' => 'Page/jobVacancy',           'icon' => 'mdi-briefcase-outline',     'label' => 'Job Vacancies',  'sub' => 'Posted positions'),
    array('link' => 'Pages/endorsed_applicants', 'icon' => 'mdi-account-check-outline', 'label' => 'Endorsed',       'sub' => 'Applicants for rating'),
    array('link' => 'Pages/rqa_appointed_list',  'icon' => 'mdi-account-tie',           'label' => 'Appointed List', 'sub' => 'Issued appointments'),
    array('link' => 'hrusers',                   'icon' => 'mdi-account-settings-outline',   'label' => 'Manage Users',   'sub' => 'System accounts'),
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
                    <span class="mis-hero-eyebrow"><i class="mdi mdi-account-group-outline"></i> Human Resource</span>
                    <h3 class="mis-hero-title">Personnel Overview</h3>
                    <p class="mis-hero-sub">
                        Headcount across the division, plus anything currently waiting on you.
                    </p>
                </div>
                <div class="mis-hero-aside">
                    <div class="mis-hero-stat">
                        <span class="mis-hero-stat-value"><?= number_format($total); ?></span>
                        <span class="mis-hero-stat-label">Employees</span>
                    </div>
                    <?php if (!$isAdmin) : ?>
                        <div class="mis-hero-stat">
                            <span class="mis-hero-stat-value"><?= number_format($pendingLeave); ?></span>
                            <span class="mis-hero-stat-label">Pending Leave</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="mis-grid">
                <?php foreach ($cards as $card) : ?>
                    <?php $linkable = ($card['link'] !== ''); ?>
                    <a <?php if ($linkable) : ?>href="<?= base_url() . $card['link']; ?>"<?php endif; ?>
                       class="mis-card <?= $card['tone']; ?><?= $linkable ? '' : ' mis-card-static'; ?>">
                        <div class="mis-card-top">
                            <span class="mis-card-num"><?= number_format($card['value']); ?></span>
                            <span class="mis-card-ico"><i class="mdi <?= $card['icon']; ?>"></i></span>
                        </div>
                        <div class="mis-card-label"><?= $card['label']; ?></div>
                        <div class="mis-card-sub"><?= $card['sub']; ?></div>
                        <?php if ($linkable) : ?>
                            <span class="mis-card-go">View list <i class="mdi mdi-arrow-right"></i></span>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <?php if (!$isAdmin) : ?>
                <div class="mis-panel">
                    <div class="mis-panel-head">
                        <div>
                            <h5 class="mis-panel-title"><i class="mdi mdi-clipboard-check-outline"></i> Pending Task</h5>
                            <p class="mis-panel-sub">Items waiting for action from this account</p>
                        </div>
                        <?php if ($pendingLeave > 0) : ?>
                            <span class="mis-chip mis-chip-amber"><?= number_format($pendingLeave); ?> pending</span>
                        <?php else : ?>
                            <span class="mis-chip mis-chip-green">All clear</span>
                        <?php endif; ?>
                    </div>
                    <div class="mis-panel-body mis-panel-body-flush">
                        <div class="table-responsive">
                            <table class="table mis-table">
                                <thead>
                                    <tr>
                                        <th>Task</th>
                                        <th class="text-center">Counts</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Leave Applications for Approval</td>
                                        <td class="text-center"><span class="mis-chip mis-chip-blue"><?= number_format($pendingLeave); ?></span></td>
                                        <td class="text-center">
                                            <a href="<?= base_url(); ?>Page/pendingLeave" class="mis-btn mis-btn-primary mis-btn-sm">
                                                <i class="mdi mdi-file-document-box-check-outline"></i> View List
                                            </a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="mis-panel">
                <div class="mis-panel-head">
                    <div>
                        <h5 class="mis-panel-title"><i class="mdi mdi-flash-outline"></i> Quick access</h5>
                        <p class="mis-panel-sub">The recruitment screens used most often</p>
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

        </div>
        <!-- end container-fluid -->

    </div>
    <!-- end content -->
