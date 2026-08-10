<?php
// AIP dashboard for the `review` and `funds` roles. Both sit mid-pipeline, so
// the view shows every stage and highlights the one this role owns.
// $counts comes from SGODModel::aip_stage_counts() — the same source the Plan
// Supervisor dashboard uses. Before, this page rendered hardcoded figures.
$counts = isset($counts) && is_array($counts)
    ? $counts
    : array('submitted' => 0, 'reviewed' => 0, 'funds' => 0, 'approved' => 0);

$fy = isset($fy) ? $fy : $this->session->cur_fy;
$districts = (isset($district) && is_object($district)) ? $district->num_rows() : 0;

$isFunds = ($this->session->position == 'funds');

// The stage this role acts on gets the emphasis treatment.
$ownStage = $isFunds ? 'funds' : 'reviewed';

$cards = array(
    array(
        'key'   => 'submitted',
        'value' => (int) $counts['submitted'],
        'label' => 'Submitted AIP',
        'sub'   => 'Schools that submitted a plan',
        'link'  => 'Page/aip_sub',
        'icon'  => 'mdi-send-check-outline',
        'tone'  => 'mis-t-blue',
    ),
    array(
        'key'   => 'reviewed',
        'value' => (int) $counts['reviewed'],
        'label' => 'Reviewed',
        'sub'   => 'Passed the review stage',
        'link'  => 'Page/aip_sub_review',
        'icon'  => 'mdi-file-find-outline',
        'tone'  => 'mis-t-sky',
    ),
    array(
        'key'   => 'funds',
        'value' => (int) $counts['funds'],
        'label' => 'Funds Available',
        'sub'   => 'Certified with available funds',
        'link'  => 'Page/aip_sub_funds',
        'icon'  => 'mdi-cash-multiple',
        'tone'  => 'mis-t-amber',
    ),
    array(
        'key'   => 'approved',
        'value' => (int) $counts['approved'],
        'label' => 'Approved',
        'sub'   => 'Reached final approval',
        'link'  => 'Page/aip_sub_approved',
        'icon'  => 'mdi-check-decagram',
        'tone'  => 'mis-t-green',
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
                    <span class="mis-hero-eyebrow">
                        <i class="mdi <?= $isFunds ? 'mdi-cash-usd-outline' : 'mdi-file-find-outline'; ?>"></i>
                        <?= $isFunds ? 'Funds Certification' : 'Plan Review'; ?>
                    </span>
                    <h3 class="mis-hero-title"><?= html_escape($title); ?></h3>
                    <p class="mis-hero-sub">
                        Annual Implementation Plan pipeline for fiscal year <strong><?= html_escape($fy); ?></strong>
                        across <strong><?= number_format($districts); ?></strong> districts.
                        Select any count to open its list.
                    </p>
                </div>
                <div class="mis-hero-aside">
                    <div class="mis-hero-stat">
                        <span class="mis-hero-stat-value"><?= number_format((int) $counts[$ownStage]); ?></span>
                        <span class="mis-hero-stat-label"><?= $isFunds ? 'Certified' : 'Reviewed'; ?></span>
                    </div>
                    <a href="#" class="mis-pill" data-toggle="modal" data-target="#myModal">
                        <i class="mdi mdi-calendar-outline"></i> FY <?= html_escape($fy); ?>
                        <i class="mdi mdi-pencil-outline"></i>
                    </a>
                </div>
            </div>

            <div class="mis-grid">
                <?php foreach ($cards as $card) : ?>
                    <a href="<?= base_url() . $card['link']; ?>" class="mis-card <?= $card['tone']; ?>">
                        <div class="mis-card-top">
                            <span class="mis-card-num"><?= number_format($card['value']); ?></span>
                            <span class="mis-card-ico"><i class="mdi <?= $card['icon']; ?>"></i></span>
                        </div>
                        <div class="mis-card-label">
                            <?= $card['label']; ?>
                            <?php if ($card['key'] === $ownStage) : ?>
                                <span class="mis-chip mis-chip-blue ml-1">Your stage</span>
                            <?php endif; ?>
                        </div>
                        <div class="mis-card-sub"><?= $card['sub']; ?></div>
                        <span class="mis-card-go">View list <i class="mdi mdi-arrow-right"></i></span>
                    </a>
                <?php endforeach; ?>
            </div>

            <div class="mis-panel">
                <div class="mis-panel-head">
                    <div>
                        <h5 class="mis-panel-title"><i class="mdi mdi-format-list-bulleted"></i> Plan lists</h5>
                        <p class="mis-panel-sub">Every stage of the pipeline</p>
                    </div>
                </div>
                <div class="mis-panel-body">
                    <div class="mis-links">
                        <?php foreach ($cards as $card) : ?>
                            <a href="<?= base_url() . $card['link']; ?>" class="mis-link">
                                <i class="mdi <?= $card['icon']; ?>"></i>
                                <span><?= $card['label']; ?><small><?= $card['sub']; ?></small></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

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
