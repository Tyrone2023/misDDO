<?php
// Read-only fragment rendered into the "View Status" modal on the Approved Plans page.
$school = isset($aip->school_id) ? $this->SGODModel->get_data_by_id('schools', 'schoolId', $aip->school_id) : null;

// Milestone remarks get a matching colour; anything else is treated as a free-text comment.
$milestones = array(
    'Submitted'       => array('primary', 'mdi-send'),
    'AIP Reviewed'    => array('info',    'mdi-file-find'),
    'Funds Available' => array('warning', 'mdi-cash-multiple'),
    'Approved'        => array('success', 'mdi-check-decagram'),
    'SNED Approved'   => array('success', 'mdi-check-decagram'),
);
?>

<div class="ap-track">

    <div class="ap-track-head">
        <div class="ap-track-school"><?= isset($school->schoolName) ? html_escape($school->schoolName) : '&mdash;'; ?></div>
        <div class="ap-track-meta">
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
                $known   = isset($milestones[$remarks]);
                $color   = $known ? $milestones[$remarks][0] : 'secondary';
                $icon    = $known ? $milestones[$remarks][1] : 'mdi-comment-text-outline';

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
                            <span class="ap-badge ap-badge-<?= $color; ?>"><?= $remarks !== '' ? html_escape($remarks) : 'No remarks'; ?></span>
                            <?php if ($i === 0) : ?><span class="ap-badge ap-badge-latest">Latest</span><?php endif; ?>
                        </div>
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
