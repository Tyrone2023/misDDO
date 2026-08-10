<?php
$redirect_urls = [
    'reg' => 'Pages/view_user',
    'user' => 'Pages/view_employee',
    'SHNS' => 'Page/Health/'
];
if (isset($redirect_urls[$this->session->position])) {
    redirect(base_url() . $redirect_urls[$this->session->position]);
}

// Division-office landing page (Admin / Super Admin / Staff / smme). The counts
// arrive as CI query objects from Pages::view().
$isAdmin = ($this->session->position == 'Admin');

$total       = is_object($data)  ? $data->num_rows()  : 0;
$teaching    = is_object($data2) ? $data2->num_rows() : 0;
$nonTeaching = is_object($data3) ? $data3->num_rows() : 0;
$inactive    = is_object($data1) ? $data1->num_rows() : 0;

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
                    <span class="mis-hero-eyebrow"><i class="mdi mdi-view-dashboard-outline"></i> Dashboard</span>
                    <h3 class="mis-hero-title">Division Personnel Overview</h3>
                    <p class="mis-hero-sub">
                        Headcount across the division for fiscal year
                        <strong><?= html_escape($this->session->cur_fy); ?></strong>.
                    </p>
                </div>
                <div class="mis-hero-aside">
                    <a href="#" class="mis-pill" data-toggle="modal" data-target="#myModal">
                        <i class="mdi mdi-calendar-outline"></i> FY <?= html_escape($this->session->cur_fy); ?>
                        <i class="mdi mdi-pencil-outline"></i>
                    </a>
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

            <?php if ($is_endorser) : ?>
                <div class="mis-panel">
                    <div class="mis-panel-head">
                        <div>
                            <h5 class="mis-panel-title"><i class="mdi mdi-clipboard-check-outline"></i> Pending Task</h5>
                            <p class="mis-panel-sub">Leave applications awaiting your recommendation</p>
                        </div>
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
                                        <td>Leave Applications for Recommendation</td>
                                        <td class="text-center"><span class="mis-chip mis-chip-blue"><?= $data5; ?></span></td>
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

            <?php elseif ($is_approver) : ?>
                <div class="mis-panel">
                    <div class="mis-panel-head">
                        <div>
                            <h5 class="mis-panel-title"><i class="mdi mdi-clipboard-check-outline"></i> Pending Task</h5>
                            <p class="mis-panel-sub">Leave applications awaiting your approval</p>
                        </div>
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
                                        <td class="text-center"><span class="mis-chip mis-chip-blue"><?= $data7; ?></span></td>
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

        </div>
        <!-- end container-fluid -->

    </div>
    <!-- end content -->

    <!-- Change Fiscal Year -->
    <div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h5 class="modal-title text-white" id="myModalLabel">Change Fiscal Year</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <form action="<?= base_url('Pages/change_fy') ?>" method="post">
                        <div class="form-group row">
                            <div class="col-lg-12">
                                <select name="new_fy" class="form-control" onchange="this.form.submit()">
                                    <option disabled selected>Change FY</option>
                                    <?php for ($y = 2023; $y <= 2030; $y++) : ?>
                                        <option value="<?= $y ?>" <?= ($this->session->userdata('cur_fy') == $y) ? 'selected' : '' ?>>
                                            <?= $y ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
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
