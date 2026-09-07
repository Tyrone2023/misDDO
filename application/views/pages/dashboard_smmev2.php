<?php
// AIP dashboard for the `review` and `funds` roles. Both sit mid-pipeline, so
// the view shows only the stages this role acts on and highlights the one it owns.
// $counts comes from SGODModel::aip_stage_counts() — the same source the Plan
// Supervisor dashboard uses. Before, this page rendered hardcoded figures.
$counts = isset($counts) && is_array($counts)
    ? $counts
    : array('submitted' => 0, 'awaiting_review' => 0, 'reviewed' => 0, 'funds' => 0, 'approved' => 0);

$fy = isset($fy) ? $fy : $this->session->cur_fy;
$districts = (isset($district) && is_object($district)) ? $district->num_rows() : 0;

// Open unlock requests from schools. This queue used to be visible only to SMME;
// review / funds / SGOD Chief work it too, so the card lives on their dashboards.
$requests    = (isset($requests) && is_array($requests)) ? $requests : array('open' => 0, 'opened' => 0, 'total' => 0);
$openRequests = (int) $requests['open'];

$isFunds = ($this->session->position == 'funds');

// Each role only sees the stages it acts on, named after the action it owes, so the
// card label and the list it opens say the same thing.
//   review : plans still waiting for review (status 0) plus the ones it has passed (3).
//   funds  : only the reviewed plans (status 3) - those are the ones it has to certify,
//            and they are exactly what Page/aip_sub_funds lists.
$ownStage = $isFunds ? 'for_funds' : 'for_review';

$cards = array();

if ($isFunds) {
    $cards[] = array(
        'key'   => 'for_funds',
        // aip_sub_funds lists status 3, so the count is the reviewed total. Status 4 is
        // what this role has already certified, which is not a queue it works on.
        'value' => (int) $counts['reviewed'],
        'label' => 'For Funds Available',
        'sub'   => 'Reviewed plans waiting for certification',
        'link'  => 'Page/aip_sub_funds',
        'icon'  => 'mdi-cash-multiple',
        'tone'  => 'mis-t-amber',
    );
} else {
    $cards[] = array(
        'key'   => 'for_review',
        // Every plan still short of review, whatever the fund source: 'submitted' is the
        // total of status 0 (MOOE), 2 (SNED) and 6 (SBFP), which is exactly what
        // aip_sub_review lists.
        'value' => (int) $counts['submitted'],
        'label' => 'For Review AIP',
        'sub'   => 'All plans waiting for your review',
        'link'  => 'Page/aip_sub_review',
        'icon'  => 'mdi-file-find-outline',
        'tone'  => 'mis-t-blue',
    );
    $cards[] = array(
        'key'   => 'reviewed',
        'value' => (int) $counts['reviewed'],
        'label' => 'Reviewed',
        'sub'   => 'Passed the review stage',
        'link'  => 'Page/aip_reviewed',
        'icon'  => 'mdi-clipboard-check-outline',
        'tone'  => 'mis-t-sky',
    );
}

$cards[] = array(
    'key'   => 'approved',
    'value' => (int) $counts['approved'],
    'label' => 'Approved',
    'sub'   => 'Reached final approval',
    'link'  => 'Page/aip_sub_approved',
    'icon'  => 'mdi-check-decagram',
    'tone'  => 'mis-t-green',
);

$cards[] = array(
    'key'   => 'requested',
    'value' => $openRequests,
    'label' => 'Requested',
    'sub'   => 'Open unlock requests',
    'link'  => 'Page/aip_requested',
    'icon'  => 'mdi-lock-open-variant-outline',
    'tone'  => 'mis-t-red',
);

// The headline figure is this role's own queue, so it never repeats a stage the
// dashboard no longer shows.
$heroValue = $isFunds ? (int) $counts['reviewed'] : (int) $counts['submitted'];
$heroLabel = $isFunds ? 'For Funds Available' : 'For Review';

// Card count drives the grid: 5 columns for five, 3 for three, otherwise the 4-up default.
$gridClass = count($cards) === 5 ? ' mis-grid-5' : (count($cards) === 3 ? ' mis-grid-3' : '');
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
                        <span class="mis-hero-stat-value"><?= number_format($heroValue); ?></span>
                        <span class="mis-hero-stat-label"><?= $heroLabel; ?></span>
                    </div>
                    <a href="#" class="mis-pill" data-toggle="modal" data-target="#myModal">
                        <i class="mdi mdi-calendar-outline"></i> FY <?= html_escape($fy); ?>
                        <i class="mdi mdi-pencil-outline"></i>
                    </a>
                </div>
            </div>

            <div class="mis-grid<?= $gridClass; ?>">
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
                        <p class="mis-panel-sub">The lists this role works on</p>
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
