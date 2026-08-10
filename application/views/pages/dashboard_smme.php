<?php
// AIP pipeline dashboard shared by the `smme` and `Accountant` roles.
// The counts arrive as CI query objects from Pages::view().
$isAccountant = ($this->session->position == 'Accountant');

$districts = is_object($district) ? $district->num_rows() : 0;
$submitted = is_object($sub)      ? $sub->num_rows()      : 0;
$approved  = is_object($ap)       ? $ap->num_rows()       : 0;
$requested = is_object($req)      ? $req->num_rows()      : 0;

$batchCode = ($isAccountant && !empty($last) && isset($last->alloc_batch)) ? $last->alloc_batch : '';

$cards = array(
    array(
        'value' => $districts,
        'label' => 'District',
        'sub'   => 'Districts in the division',
        'link'  => 'Page/aip_sub_district',
        'icon'  => 'mdi-map-marker-multiple',
        'tone'  => 'mis-t-blue',
    ),
    array(
        'value' => $submitted,
        'label' => 'Submitted',
        'sub'   => 'Plans awaiting action',
        'link'  => 'Page/aip_sub',
        'icon'  => 'mdi-send-check-outline',
        'tone'  => 'mis-t-purple',
    ),
    array(
        'value' => $approved,
        'label' => 'Approved',
        'sub'   => 'Reached final approval',
        'link'  => 'Page/aip_approved',
        'icon'  => 'mdi-check-decagram',
        'tone'  => 'mis-t-green',
    ),
    array(
        'value' => $requested,
        'label' => 'Requested',
        'sub'   => 'Open fund requests',
        'link'  => 'Page/aip_requested',
        'icon'  => 'mdi-cash-multiple',
        'tone'  => 'mis-t-amber',
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
                    <span class="mis-hero-eyebrow"><i class="mdi mdi-notebook-multiple"></i> Implementation Plans</span>
                    <h3 class="mis-hero-title">AIP Pipeline</h3>
                    <p class="mis-hero-sub">
                        Annual Implementation Plan activity for fiscal year
                        <strong><?= html_escape($this->session->cur_fy); ?></strong>.
                        Select any count to open its list.
                    </p>
                </div>
                <div class="mis-hero-aside">
                    <?php if ($batchCode !== '') : ?>
                        <div class="mis-hero-stat">
                            <span class="mis-hero-stat-value"><?= html_escape($batchCode); ?></span>
                            <span class="mis-hero-stat-label">Last Batch</span>
                        </div>
                    <?php endif; ?>
                    <a href="#" class="mis-pill" data-toggle="modal" data-target="#myModal">
                        <i class="mdi mdi-calendar-outline"></i> FY <?= html_escape($this->session->cur_fy); ?>
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
                        <div class="mis-card-label"><?= $card['label']; ?></div>
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
