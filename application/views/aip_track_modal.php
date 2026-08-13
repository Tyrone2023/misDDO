<?php
// Read-only fragment rendered into the "Track" modal used by the Approved Plans
// page, the unlock-request queue and the school's own Implementation Plan page.
$school = isset($aip->school_id) ? $this->SGODModel->get_data_by_id('schools', 'schoolId', $aip->school_id) : null;

// Milestone remarks get a matching colour; anything else is treated as a free-text comment.
$milestones = array(
    'Submitted'            => array('primary', 'mdi-send'),
    'AIP Reviewed'         => array('info',    'mdi-file-find'),
    'Funds Available'      => array('warning', 'mdi-cash-multiple'),
    'Approved'             => array('success', 'mdi-check-decagram'),
    'SNED Approved'        => array('success', 'mdi-check-decagram'),
    'Unlocked for Editing' => array('danger',  'mdi-lock-open-variant'),
);

// Statuses the plan itself can sit at, shown as a header pill.
$stages = array(
    0 => array('Submitted',       'ap-pill-blue',  'mdi-send'),
    1 => array('Approved',        'ap-pill-green', 'mdi-check-decagram'),
    2 => array('Submitted',       'ap-pill-blue',  'mdi-send'),
    3 => array('AIP Reviewed',    'ap-pill-sky',   'mdi-file-find'),
    4 => array('Funds Available', 'ap-pill-amber', 'mdi-cash-multiple'),
    6 => array('Submitted',       'ap-pill-blue',  'mdi-send'),
);
$stage = isset($aip->status) && isset($stages[(int) $aip->status])
    ? $stages[(int) $aip->status]
    : array('Pending', 'ap-pill-grey', 'mdi-clock-outline');
?>

<div class="ap-track">

    <div class="ap-track-head">
        <div class="ap-track-school"><?= isset($school->schoolName) ? html_escape($school->schoolName) : '&mdash;'; ?></div>
        <div class="ap-track-meta">
            <?php if (isset($aip->status)) : ?>
                <span class="ap-status-pill <?= $stage[1]; ?>"><i class="mdi <?= $stage[2]; ?>"></i> <?= $stage[0]; ?></span>
            <?php endif; ?>
            <?php if (isset($aip->b_code)) : ?><span class="ap-chip"><?= html_escape($aip->b_code); ?></span><?php endif; ?>
            <?php if (isset($aip->fy)) : ?><span class="ap-chip">FY <?= html_escape($aip->fy); ?></span><?php endif; ?>
            <?php if (isset($school->district)) : ?><span class="ap-chip"><?= html_escape($school->district); ?></span><?php endif; ?>
        </div>
    </div>

    <?php if (empty($data)) : ?>
        <div class="ap-track-empty">
            <i class="mdi mdi-timeline-clock-outline"></i>
            <p>No status history recorded for this plan.</p>
        </div>
    <?php else : ?>
        <ol class="ap-timeline">
            <?php foreach ($data as $i => $row) :
                $remarks = trim($row->remarks);
                $note    = '';

                if (isset($milestones[$remarks])) {
                    $label = $remarks;
                    $color = $milestones[$remarks][0];
                    $icon  = $milestones[$remarks][1];
                } elseif (stripos($remarks, 'Request for Unlock:') === 0) {
                    // Written by SGODModel::request_insert_track() as "Request for Unlock: <reason>".
                    $label = 'Unlock Requested';
                    $color = 'warning';
                    $icon  = 'mdi-lock-question';
                    $note  = trim(substr($remarks, strlen('Request for Unlock:')));
                } else {
                    $label = 'Remarks';
                    $color = 'secondary';
                    $icon  = 'mdi-comment-text-outline';
                    $note  = $remarks;
                }

                $user = $this->SGODModel->one_cond_row('users', 'username', $row->res);
                $name = '';
                if (isset($user->fname)) {
                    $name = trim($user->fname . ' ' . $user->mname . ' ' . $user->lname);
                }
                if ($name === '') {
                    $name = $row->res;
                }
                ?>
                <li class="ap-timeline-item <?= $i === 0 ? 'is-latest' : ''; ?>">
                    <span class="ap-timeline-dot ap-dot-<?= $color; ?>"><i class="mdi <?= $icon; ?>"></i></span>
                    <div class="ap-timeline-body">
                        <div class="ap-timeline-top">
                            <span class="ap-badge ap-badge-<?= $color; ?>"><?= html_escape($label); ?></span>
                            <?php if ($i === 0) : ?><span class="ap-badge ap-badge-latest">Latest</span><?php endif; ?>
                        </div>
                        <?php if ($note !== '') : ?>
                            <div class="ap-timeline-note"><?= html_escape($note); ?></div>
                        <?php endif; ?>
                        <div class="ap-timeline-sub">
                            <i class="mdi mdi-account-outline"></i> <?= html_escape($name); ?>
                            <?php if (!empty($row->position)) : ?>
                                <span class="ap-dotsep">&bull;</span><?= html_escape($row->position); ?>
                            <?php endif; ?>
                        </div>
                        <div class="ap-timeline-time">
                            <i class="mdi mdi-calendar-blank-outline"></i> <?= html_escape($row->tdate); ?>
                            <span class="ap-dotsep">&bull;</span><?= html_escape($row->dtime); ?>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
        </ol>
    <?php endif; ?>

</div>
