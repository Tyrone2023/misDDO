<?php
// SGOD Chief landing page. Counts arrive as CI query objects from Pages::view().
$schools   = is_object($school)   ? $school->num_rows()   : 0;
$accoms    = is_object($accom)    ? $accom->num_rows()    : 0;
$sections  = is_object($section)  ? $section->num_rows()  : 0;
// Replaces the "Users: 55" card, which was a hardcoded literal.
$districts = (isset($district) && is_object($district)) ? $district->num_rows() : 0;

// Open unlock requests from schools. Page/sgodDashboard renders this view without
// the counts, so fall back to zero rather than warning.
$requests     = (isset($requests) && is_array($requests)) ? $requests : array('open' => 0, 'opened' => 0, 'total' => 0);
$openRequests = (int) $requests['open'];

$cards = array(
    array(
        'value' => $openRequests,
        'label' => 'Requested',
        'sub'   => 'Open AIP unlock requests',
        'link'  => 'Page/aip_requested',
        'icon'  => 'mdi-lock-open-variant-outline',
        'tone'  => 'mis-t-red',
    ),
    array(
        'value' => $schools,
        'label' => 'Schools',
        'sub'   => 'Public and private schools',
        'link'  => 'Pages/schools?type=Public',
        'icon'  => 'mdi-school-outline',
        'tone'  => 'mis-t-blue',
    ),
    array(
        'value' => $districts,
        'label' => 'Districts',
        'sub'   => 'Districts in the division',
        'link'  => 'Page/districts',
        'icon'  => 'mdi-map-marker-multiple',
        'tone'  => 'mis-t-teal',
    ),
    array(
        'value' => $sections,
        'label' => 'Sections',
        'sub'   => 'SGOD sections on record',
        'link'  => 'Page/sections',
        'icon'  => 'mdi-view-grid',
        'tone'  => 'mis-t-sky',
    ),
    array(
        'value' => $accoms,
        'label' => 'Accomplishments',
        'sub'   => 'Reported section accomplishments',
        'link'  => 'Page/sections',
        'icon'  => 'mdi-trophy-outline',
        'tone'  => 'mis-t-purple',
    ),
);

$quickLinks = array(
    array('link' => 'Page/aip_sub_sgod_chief', 'icon' => 'mdi-notebook-multiple',    'label' => 'Implementation Plans', 'sub' => 'Plans awaiting the chief'),
    array('link' => 'Page/aip_requested',      'icon' => 'mdi-lock-open-variant-outline', 'label' => 'Unlock Requests', 'sub' => 'Schools asking to edit a plan'),
    array('link' => 'Page/aip_sub_approved',   'icon' => 'mdi-check-decagram',       'label' => 'Approved Plans',       'sub' => 'Reached final approval'),
    array('link' => 'Page/sbm_list',           'icon' => 'mdi-clipboard-list-outline','label' => 'SBM',                 'sub' => 'Self-assessment checklist'),
    array('link' => 'Page/memo',               'icon' => 'mdi-file-document-outline','label' => 'Memo',                 'sub' => 'Issued memoranda'),
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
                    <span class="mis-hero-eyebrow"><i class="mdi mdi-office-building"></i> SGOD</span>
                    <h3 class="mis-hero-title"><?= html_escape($title); ?></h3>
                    <p class="mis-hero-sub">
                        Division coverage at a glance for fiscal year
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

            <div class="mis-grid mis-grid-5">
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

        </div>
        <!-- end container-fluid -->

    </div>
    <!-- end content -->

    <!-- Change Fiscal Year. templates/modal.php does not define #myModal, so the
         old breadcrumb link on this page opened nothing. -->
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
