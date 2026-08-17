<?php
/**
 * Retention request panel for the rating page (Pages/ma).
 *
 * Same decision as Pages/request_rating -> Retain / Deny Retention, brought to
 * the page where the rating actually happens and rendered right under the
 * qualification stage panel. The grant modal posts to the very same endpoint
 * the Retain screen uses (request_rating_granted for teaching positions,
 * request_rating_granted_none for the rest), so the server logic is untouched.
 *
 * Expects: $retention_request_panel from Pages::retention_request_panel().
 */
$rrp = $retention_request_panel ?? [];
$rrpRequest = $rrp['request'] ?? null;
$rrpJob = $rrp['job'] ?? null;

if (!$rrpRequest || !$rrpJob) {
    return;
}

$rrp_h = static function ($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
};

$rrpApplicant = $rrp['applicant'] ?? null;
$rrpPType     = (int)($rrp['p_type'] ?? 0);
$rrpAppId     = (int)($rrp['app_id'] ?? 0);
$rrpRequestId = (int)($rrpRequest->id ?? 0);
$rrpRecordNo  = trim((string)($rrp['record_no'] ?? ''));
$rrpStat      = (int)($rrpRequest->stat ?? 0);
$rrpCanAct    = !empty($rrp['can_act']);
$rrpJobClosed = !empty($rrp['job_closed']);
$rrpReturnUrl = current_url();

$rrpIsPending  = $rrpStat === 0;
$rrpIsGranted  = $rrpStat === 1;
$rrpIsDenied   = $rrpStat === 2;

// r_type 2 means Demo & TR for teaching positions, Interview & Written
// Examination for every other position - same wording as the Retain screens.
$rrpScopeLabel = static function ($scope, $pType) {
    if ((int)$scope === 1) {
        return 'All scores';
    }

    if ((int)$scope !== 2) {
        return 'Not recorded';
    }

    return ((int)$pType === 1)
        ? 'Demo and TR ratings only'
        : 'Interview and Written Examination ratings only';
};

$rrpScopeHelp = static function ($scope, $pType) {
    if ((int)$scope === 2) {
        return ((int)$pType === 1)
            ? 'Copies the LET, Demo and TR scores only. Education, Training and Experience still have to be rated.'
            : 'Copies the Interview and Written Examination scores only. Every other criterion still has to be rated.';
    }

    return ((int)$pType === 1)
        ? 'Copies every criterion (Education, Training, Experience, LET, Demo and TR) and marks the application Rated.'
        : 'Copies every criterion (Education, Training, Experience, Performance, OA, AE, ALD, Skills, Interview and Written Examination) and marks the application Rated.';
};

$rrpRequestedType  = (int)($rrpRequest->r_type ?? 0);
$rrpRequestedLabel = $rrpScopeLabel($rrpRequestedType, $rrpPType);
$rrpRequestedHelp  = $rrpScopeHelp($rrpRequestedType, $rrpPType);
$rrpGrantedLabel   = $rrpScopeLabel($rrpRequest->granted_scope ?? null, $rrpPType);

$rrpGrantAction = base_url(($rrpPType === 1)
    ? 'pages/request_rating_granted'
    : 'pages/request_rating_granted_none');

$rrpApplicantName = trim(implode(' ', array_filter([
    trim((string)($rrpApplicant->FirstName ?? '')),
    trim((string)($rrpApplicant->MiddleName ?? '')),
    trim((string)($rrpApplicant->LastName ?? '')),
], static function ($part) {
    return $part !== '';
})));

$rrpApplications = $rrp['applications'] ?? [];
$rrpSelectable   = (int)($rrp['selectable'] ?? 0);
$rrpRetained     = $rrp['retained'] ?? null;

// Scores kept by another evaluator never reach the rating form of whoever holds
// the application, so the panel offers to release them.
$rrpNeedsRelease = $rrpIsGranted
    && !empty($rrpRetained)
    && (int)($rrpRetained['claimed_by'] ?? 0) > 0
    && empty($rrpRetained['claimed_by_me']);

// Same list rp.php uses - the shorter 1-10 map in the older views leaves the
// SHS tracks and SPIMS levels unlabelled.
$rrpJobTypes = [
    1  => 'Elementary',
    2  => 'Secondary',
    3  => 'Junior High School',
    4  => 'Senior High School',
    5  => 'Kindergarten',
    6  => 'IPED Elementary',
    7  => 'IPED Secondary',
    8  => 'IPED Junior High School',
    9  => 'IPED Senior High School',
    10 => 'SNED',
    11 => 'SHS Academic and Core Subjects',
    12 => 'SHS Arts and Design Track',
    13 => 'SHS Sports Track',
    14 => 'SHS Technical-Vocational (TVL) Track',
    15 => 'Elementary - SPIMS',
    16 => 'Junior High School - SPIMS',
    17 => 'DOST - (RA 7687)',
    18 => 'DOST - (RA 10612)',
    19 => '(SST I)',
    20 => 'FOR TESTING PURPOSES (DO NOT APPLY)',
];

// Dates are stored as plain strings and older rows carry zero dates, so only
// something that really parses gets reformatted.
$rrpDate = static function ($value) {
    $value = trim((string)$value);

    if ($value === '' || strpos($value, '0000-00-00') === 0) {
        return '';
    }

    $stamp = strtotime($value);

    return $stamp ? date('M j, Y', $stamp) : $value;
};

// 0.00001 is the "not rated yet" placeholder the rating forms and copy routines
// write, so it must not be shown as a score of 0.00.
$rrpPoints = static function ($value) {
    $value = (float)$value;

    if ($value > 0 && $value < 0.001) {
        return '--';
    }

    return number_format($value, 2);
};

if ($rrpIsGranted) {
    $rrpPalette = ['border' => '#bde3d1', 'accent' => '#2f9e73', 'bg' => 'linear-gradient(110deg, #f3fbf7, #ffffff)', 'iconBg' => '#e3f6ed', 'iconFg' => '#16815f', 'kicker' => '#1d7b5b'];
    $rrpIcon = 'mdi-check-decagram';
} elseif ($rrpIsDenied) {
    $rrpPalette = ['border' => '#f1b9b9', 'accent' => '#cf5555', 'bg' => '#fff6f6', 'iconBg' => '#fde4e4', 'iconFg' => '#c34d4d', 'kicker' => '#a83d3d'];
    $rrpIcon = 'mdi-close-octagon-outline';
} else {
    $rrpPalette = ['border' => '#f3d9ab', 'accent' => '#d69a2d', 'bg' => 'linear-gradient(110deg, #fffaf0, #ffffff)', 'iconBg' => '#fdf0d6', 'iconFg' => '#b3781a', 'kicker' => '#a8761f'];
    $rrpIcon = 'mdi-file-restore';
}
?>

<style>
    .rrp-panel {
        position: relative;
        z-index: 5;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        margin: 0 0 20px;
        border: 1px solid <?= $rrpPalette['border'] ?>;
        border-left: 5px solid <?= $rrpPalette['accent'] ?>;
        border-radius: 14px;
        background: <?= $rrpPalette['bg'] ?>;
        box-shadow: 0 8px 24px rgba(31, 65, 101, .08);
        padding: 18px 20px;
    }

    .rrp-panel-main {
        display: flex;
        min-width: 0;
        align-items: center;
        gap: 14px;
    }

    .rrp-panel-icon {
        display: inline-flex;
        width: 48px;
        height: 48px;
        flex: 0 0 48px;
        align-items: center;
        justify-content: center;
        border-radius: 13px;
        background: <?= $rrpPalette['iconBg'] ?>;
        color: <?= $rrpPalette['iconFg'] ?>;
        font-size: 24px;
    }

    .rrp-panel-kicker {
        margin-bottom: 3px;
        color: <?= $rrpPalette['kicker'] ?>;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .rrp-panel h4 {
        margin: 0 0 4px;
        color: #263c52;
        font-size: 16px;
        font-weight: 700;
    }

    .rrp-panel p {
        margin: 0;
        color: #6c7d8e;
        font-size: 12px;
        line-height: 1.55;
    }

    .rrp-panel p + p {
        margin-top: 4px;
    }

    .rrp-panel-actions {
        display: flex;
        flex: 0 0 auto;
        gap: 8px;
    }

    .rrp-panel .btn {
        display: inline-flex;
        min-height: 40px;
        align-items: center;
        gap: 7px;
        border-radius: 9px;
        padding: 9px 14px;
        font-weight: 700;
    }

    .rrp-retained {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 10px;
    }

    .rrp-retained-item {
        display: inline-flex;
        align-items: baseline;
        gap: 6px;
        border: 1px solid #d7e9e0;
        border-radius: 8px;
        background: #fff;
        padding: 4px 9px;
    }

    .rrp-retained-item em {
        color: #7b8a99;
        font-size: 10px;
        font-style: normal;
        font-weight: 700;
        letter-spacing: .03em;
        text-transform: uppercase;
    }

    .rrp-retained-item strong {
        color: #26414f;
        font-size: 12px;
    }

    .rrp-retained-total {
        border-color: #2f9e73;
        background: #eefaf4;
    }

    .rrp-retained-total strong {
        color: #16815f;
    }

    .rrp-retained-note {
        margin-top: 8px !important;
        font-size: 11px !important;
    }

    .rrp-panel-actions form {
        margin: 0;
    }

    .rrp-modal .modal-content {
        overflow: hidden;
        border: 0;
        border-radius: 16px;
        box-shadow: 0 24px 70px rgba(25, 47, 72, .28);
    }

    .rrp-modal .modal-header {
        align-items: flex-start;
        border: 0;
        background: linear-gradient(120deg, #14624a, #2f9e73);
        padding: 21px 24px;
        color: #fff;
    }

    .rrp-modal .modal-title,
    .rrp-modal .close {
        color: #fff;
    }

    .rrp-modal .modal-title {
        font-size: 18px;
        font-weight: 700;
    }

    .rrp-modal-subtitle {
        margin-top: 4px;
        color: rgba(255, 255, 255, .78);
        font-size: 12px;
    }

    .rrp-modal .modal-body {
        padding: 22px 24px;
        background: #fbfcfe;
    }

    .rrp-applicant-summary {
        display: flex;
        align-items: center;
        gap: 11px;
        margin-bottom: 18px;
        border: 1px solid #e2e9f1;
        border-radius: 11px;
        background: #fff;
        padding: 12px 14px;
    }

    .rrp-applicant-summary i {
        color: #2f9e73;
        font-size: 22px;
    }

    .rrp-applicant-name {
        color: #293f55;
        font-size: 13px;
        font-weight: 700;
    }

    .rrp-applicant-position {
        margin-top: 2px;
        color: #7b8a99;
        font-size: 11px;
    }

    .rrp-section {
        margin-bottom: 16px;
        border: 1px solid #e4eaf1;
        border-radius: 12px;
        background: #fff;
        padding: 17px;
    }

    .rrp-section:last-child {
        margin-bottom: 0;
    }

    .rrp-section-title {
        margin-bottom: 4px;
        color: #293e53;
        font-size: 13px;
        font-weight: 700;
    }

    .rrp-section-help {
        margin-bottom: 12px;
        color: #81909f;
        font-size: 11px;
    }

    .rrp-scope {
        border: 1px solid #e6ecf3;
        border-radius: 9px;
        background: #f6f9fc;
        padding: 10px 13px;
        color: #33506b;
        font-size: 13px;
        font-weight: 700;
    }

    .rrp-empty {
        border: 1px solid #f2d3d3;
        border-radius: 10px;
        background: #fff7f7;
        padding: 12px 14px;
        color: #7d4141;
        font-size: 12px;
        line-height: 1.55;
    }

    .rrp-source-list {
        display: grid;
        gap: 10px;
        max-height: 340px;
        overflow-y: auto;
        padding-right: 3px;
    }

    .rrp-empty + .rrp-source-list {
        margin-top: 12px;
    }

    .rrp-source {
        position: relative;
    }

    .rrp-source input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .rrp-source label {
        display: flex;
        width: 100%;
        cursor: pointer;
        align-items: flex-start;
        gap: 11px;
        margin: 0;
        border: 2px solid #e3e9ef;
        border-radius: 11px;
        background: #fff;
        padding: 12px 13px;
        transition: .16s ease;
    }

    .rrp-source label:hover {
        border-color: #cfe0d8;
    }

    .rrp-source input:checked + label {
        border-color: #31a67e;
        background: #f5fcf9;
        box-shadow: 0 0 0 3px rgba(47, 158, 115, .1);
    }

    .rrp-source-tick {
        display: inline-flex;
        width: 22px;
        height: 22px;
        flex: 0 0 22px;
        align-items: center;
        justify-content: center;
        margin-top: 1px;
        border: 2px solid #d6dee6;
        border-radius: 50%;
        background: #fff;
        color: transparent;
        font-size: 13px;
    }

    .rrp-source input:checked + label .rrp-source-tick {
        border-color: #31a67e;
        background: #31a67e;
        color: #fff;
    }

    .rrp-source-body {
        display: block;
        min-width: 0;
        flex: 1 1 auto;
    }

    .rrp-source-head {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        gap: 10px;
    }

    .rrp-source-title {
        color: #2c4256;
        font-size: 13px;
        font-weight: 700;
        line-height: 1.35;
    }

    .rrp-source-title em {
        color: #7b8a99;
        font-size: 11px;
        font-style: normal;
        font-weight: 600;
    }

    .rrp-badge {
        flex: 0 0 auto;
        border-radius: 20px;
        padding: 3px 9px;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: .02em;
        white-space: nowrap;
    }

    .rrp-badge-ok { background: #e3f6ed; color: #16815f; }
    .rrp-badge-none { background: #f1f3f5; color: #8a97a3; }

    .rrp-source-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 4px 14px;
        margin-top: 6px;
        color: #7b8a99;
        font-size: 11px;
    }

    .rrp-source-meta i {
        margin-right: 3px;
        opacity: .75;
    }

    .rrp-source-scores {
        display: flex;
        flex-wrap: wrap;
        gap: 3px 12px;
        margin-top: 8px;
        border-top: 1px dashed #e7edf3;
        padding-top: 7px;
        color: #55697c;
        font-size: 11px;
    }

    .rrp-source-scores strong {
        color: #2c4256;
    }

    .rrp-source-scores-label {
        color: #8a97a3;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .05em;
        font-size: 10px;
    }

    .rrp-source-scores-muted {
        color: #a17070;
    }

    .rrp-source-unusable label {
        cursor: not-allowed;
        border-style: dashed;
        background: #fbfcfd;
        opacity: .78;
    }

    .rrp-source-unusable label:hover {
        border-color: #e3e9ef;
    }

    .rrp-modal .modal-footer {
        border-top: 1px solid #e8edf3;
        padding: 15px 24px;
        background: #fff;
    }

    .rrp-modal .modal-footer .btn {
        border-radius: 8px;
        font-weight: 700;
    }

    @media (max-width: 767.98px) {
        .rrp-panel {
            align-items: flex-start;
            flex-direction: column;
        }

        .rrp-panel-actions {
            width: 100%;
        }

        .rrp-panel .btn {
            flex: 1 1 auto;
            justify-content: center;
        }
    }
</style>

<div class="rrp-panel" id="retentionRequestPanel">
    <div class="rrp-panel-main">
        <span class="rrp-panel-icon"><i class="mdi <?= $rrpIcon ?>"></i></span>
        <div>
            <div class="rrp-panel-kicker">Retention request</div>
            <?php if ($rrpIsGranted) : ?>
                <h4>Retention granted &mdash; <?= $rrp_h($rrpGrantedLabel) ?></h4>
                <p>
                    The scores of the selected previous application were copied into this one
                    <?php if (!empty($rrpRequest->adate)) : ?>on <?= $rrp_h($rrpRequest->adate) ?><?php endif; ?>.
                    <?php if (!empty($rrpRetained)) : ?>
                        These are the scores standing on the application now &mdash; the evaluator can still
                        change any of them from the rating form below.
                    <?php endif; ?>
                </p>

                <?php if (!empty($rrpRetained)) : ?>
                    <div class="rrp-retained">
                        <?php foreach ($rrpRetained['scores'] as $rrpLabel => $rrpValue) : ?>
                            <span class="rrp-retained-item">
                                <em><?= $rrp_h($rrpLabel) ?></em>
                                <strong><?= $rrp_h($rrpPoints($rrpValue)) ?></strong>
                            </span>
                        <?php endforeach; ?>
                        <span class="rrp-retained-item rrp-retained-total">
                            <em>Total</em>
                            <strong><?= $rrp_h($rrpPoints($rrpRetained['total'])) ?></strong>
                        </span>
                    </div>
                    <p class="rrp-retained-note">
                        <?= $rrp_h('--') ?> means the criterion is still unrated.
                        <?php if ($rrpNeedsRelease) : ?>
                            These scores are attributed to evaluator #<?= (int)$rrpRetained['claimed_by'] ?>
                            (the one who rated the previous application), so they stay hidden in the rating
                            form. Release them to make them visible and editable there.
                        <?php endif; ?>
                    </p>
                <?php endif; ?>
            <?php elseif ($rrpIsDenied) : ?>
                <h4>Retention denied</h4>
                <p>
                    No score was retained
                    <?php if (!empty($rrpRequest->adate)) : ?>(<?= $rrp_h($rrpRequest->adate) ?>)<?php endif; ?>.
                    This application has to be rated in full.
                </p>
                <?php if (!empty($rrpRequest->deny_reason)) : ?>
                    <p><strong>Reason:</strong> <?= $rrp_h($rrpRequest->deny_reason) ?></p>
                <?php endif; ?>
            <?php else : ?>
                <h4>Retention of ratings requested</h4>
                <p>
                    The applicant asked to retain <strong><?= $rrp_h($rrpRequestedLabel) ?></strong>
                    <?php if (!empty($rrpRequest->rdate)) : ?>on <?= $rrp_h($rrpRequest->rdate) ?><?php endif; ?>.
                    <?= $rrp_h($rrpRequestedHelp) ?>
                </p>
                <?php if ($rrpJobClosed) : ?>
                    <p>The vacancy is already Closed, so this request can no longer be acted on here.</p>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
    <?php if ($rrpCanAct) : ?>
        <div class="rrp-panel-actions">
            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#retentionGrantModal">
                <i class="mdi mdi-file-restore"></i> Grant retention
            </button>
            <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#denyRetentionModal">
                <i class="mdi mdi-close-circle-outline"></i> Deny retention
            </button>
        </div>
    <?php elseif ($rrpNeedsRelease) : ?>
        <div class="rrp-panel-actions">
            <form action="<?= $rrp_h(base_url('Pages/retention_release_scores')) ?>" method="post"
                  onsubmit="return confirm('Release the retained scores so they show in the rating form?');">
                <input type="hidden" name="appID" value="<?= $rrpAppId ?>">
                <input type="hidden" name="return_url" value="<?= $rrp_h($rrpReturnUrl) ?>">
                <button type="submit" class="btn btn-success">
                    <i class="mdi mdi-lock-open-variant-outline"></i> Show scores in rating form
                </button>
            </form>
        </div>
    <?php endif; ?>
</div>

<?php if ($rrpCanAct) : ?>
    <div class="modal fade rrp-modal" id="retentionGrantModal" tabindex="-1" role="dialog" aria-labelledby="retentionGrantModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="retentionGrantForm" action="<?= $rrp_h($rrpGrantAction) ?>" method="post">
                    <div class="modal-header">
                        <div>
                            <h5 class="modal-title" id="retentionGrantModalTitle">Grant retention of ratings</h5>
                            <div class="rrp-modal-subtitle">Pick the previous application whose scores should be copied into this one.</div>
                        </div>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>

                    <div class="modal-body">
                        <input type="hidden" name="id" value="<?= $rrpRequestId ?>">
                        <input type="hidden" name="app_id" value="<?= $rrpAppId ?>">
                        <input type="hidden" name="jobID" value="<?= (int)($rrpJob->jobID ?? 0) ?>">
                        <input type="hidden" name="record_no" value="<?= $rrp_h($rrpRecordNo) ?>">
                        <input type="hidden" name="r_type" value="<?= $rrpRequestedType ?>">
                        <input type="hidden" name="return_url" value="<?= $rrp_h($rrpReturnUrl) ?>">

                        <div class="rrp-applicant-summary">
                            <i class="mdi mdi-account-circle-outline"></i>
                            <div>
                                <div class="rrp-applicant-name"><?= $rrp_h($rrpApplicantName !== '' ? $rrpApplicantName : 'Applicant #' . $rrpAppId) ?></div>
                                <div class="rrp-applicant-position">
                                    Rating <?= $rrp_h($rrpJob->jobTitle ?? '') ?> &middot; <?= $rrp_h($rrpRecordNo) ?> &middot; App <?= $rrpAppId ?>
                                    <?php $rrpTargetOpened = $rrpDate($rrpJob->datePosted ?? ''); ?>
                                    <?php if ($rrpTargetOpened !== '') : ?>
                                        &middot; vacancy opened <?= $rrp_h($rrpTargetOpened) ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="rrp-section">
                            <div class="rrp-section-title">Source application <span class="text-danger">*</span></div>
                            <div class="rrp-section-help">
                                Other applications of this applicant, newest vacancy first. The application being rated and
                                anything else filed against this same vacancy are excluded. Only applications with scores
                                already on file can be used as a source.
                            </div>

                            <?php if (empty($rrpApplications)) : ?>
                                <div class="rrp-empty">
                                    This applicant has no other application on record, so there is nothing to retain.
                                </div>
                            <?php else : ?>
                                <?php if ($rrpSelectable === 0) : ?>
                                    <div class="rrp-empty">
                                        None of these applications has a rating on file under record no.
                                        <strong><?= $rrp_h($rrpRecordNo) ?></strong>, so there is no score to copy.
                                    </div>
                                <?php endif; ?>

                                <div class="rrp-source-list">
                                    <?php foreach ($rrpApplications as $rrpRow) : ?>
                                        <?php
                                            $rrpSourceId = (int)$rrpRow['app_id'];
                                            $rrpControlId = 'rrp-source-' . $rrpSourceId;
                                            $rrpHasRating = !empty($rrpRow['has_rating']);
                                            $rrpLevel = $rrpJobTypes[$rrpRow['job_type']] ?? '';
                                            $rrpOpened = $rrpDate($rrpRow['date_opened']);
                                            $rrpApplied = $rrpDate($rrpRow['date_applied']);
                                        ?>
                                        <div class="rrp-source<?= $rrpHasRating ? '' : ' rrp-source-unusable' ?>">
                                            <input type="radio" name="application" id="<?= $rrp_h($rrpControlId) ?>" value="<?= $rrpSourceId ?>"
                                                <?= $rrpHasRating ? '' : 'disabled' ?> <?= ($rrpSelectable > 0) ? 'required' : '' ?>>
                                            <label for="<?= $rrp_h($rrpControlId) ?>">
                                                <span class="rrp-source-tick"><i class="mdi mdi-check"></i></span>
                                                <span class="rrp-source-body">
                                                    <span class="rrp-source-head">
                                                        <span class="rrp-source-title">
                                                            <?= $rrp_h($rrpRow['title'] !== '' ? $rrpRow['title'] : 'Unknown Position') ?>
                                                            <?php if ($rrpLevel !== '') : ?>
                                                                <em>&ndash; <?= $rrp_h($rrpLevel) ?></em>
                                                            <?php endif; ?>
                                                        </span>
                                                        <?php if ($rrpHasRating) : ?>
                                                            <span class="rrp-badge rrp-badge-ok">
                                                                Rated &middot; total <?= $rrp_h($rrpPoints($rrpRow['total_points'])) ?>
                                                            </span>
                                                        <?php else : ?>
                                                            <span class="rrp-badge rrp-badge-none">No rating on file</span>
                                                        <?php endif; ?>
                                                    </span>

                                                    <span class="rrp-source-meta">
                                                        <span><i class="mdi mdi-calendar-plus"></i> Opened <?= $rrp_h($rrpOpened !== '' ? $rrpOpened : 'not recorded') ?></span>
                                                        <span><i class="mdi mdi-send-outline"></i> Applied <?= $rrp_h($rrpApplied !== '' ? $rrpApplied : 'not recorded') ?></span>
                                                        <?php if ($rrpRow['sy'] !== '') : ?>
                                                            <span><i class="mdi mdi-calendar-range"></i> SY <?= $rrp_h($rrpRow['sy']) ?></span>
                                                        <?php endif; ?>
                                                        <?php if ($rrpRow['item_no'] !== '') : ?>
                                                            <span><i class="mdi mdi-tag-outline"></i> Item <?= $rrp_h($rrpRow['item_no']) ?></span>
                                                        <?php endif; ?>
                                                        <?php if ($rrpRow['app_status'] !== '') : ?>
                                                            <span><i class="mdi mdi-progress-check"></i> <?= $rrp_h($rrpRow['app_status']) ?></span>
                                                        <?php endif; ?>
                                                        <?php if ($rrpRow['jv_status'] !== '') : ?>
                                                            <span><i class="mdi mdi-briefcase-outline"></i> Vacancy <?= $rrp_h($rrpRow['jv_status']) ?></span>
                                                        <?php endif; ?>
                                                        <span><i class="mdi mdi-pound"></i> App <?= $rrpSourceId ?></span>
                                                    </span>

                                                    <?php if ($rrpHasRating && !empty($rrpRow['scores'])) : ?>
                                                        <span class="rrp-source-scores">
                                                            <span class="rrp-source-scores-label">Will be copied:</span>
                                                            <?php foreach ($rrpRow['scores'] as $rrpScoreLabel => $rrpScoreValue) : ?>
                                                                <span><?= $rrp_h($rrpScoreLabel) ?> <strong><?= $rrp_h($rrpPoints($rrpScoreValue)) ?></strong></span>
                                                            <?php endforeach; ?>
                                                        </span>
                                                    <?php elseif (!$rrpHasRating) : ?>
                                                        <span class="rrp-source-scores rrp-source-scores-muted">
                                                            No row in <?= $rrp_h($rrpPType === 1 ? 'hris_applications_rating' : 'hris_rating_none') ?>
                                                            for record no. <?= $rrp_h($rrpRecordNo) ?> - nothing to copy from this application.
                                                        </span>
                                                    <?php endif; ?>
                                                </span>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="rrp-section">
                            <div class="rrp-section-title">Retain</div>
                            <div class="rrp-section-help"><?= $rrp_h($rrpRequestedHelp) ?></div>
                            <div class="rrp-scope"><?= $rrp_h($rrpRequestedLabel) ?></div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success" id="rrp-grant-submit" <?= $rrpSelectable === 0 ? 'disabled' : '' ?>>
                            <i class="mdi mdi-file-restore mr-1"></i> Grant retention
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php $this->load->view('pages/_deny_retention_modal', ['requestId' => $rrpRequestId, 'returnUrl' => $rrpReturnUrl]); ?>
<?php endif; ?>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var panel = document.getElementById('retentionRequestPanel');
        var pageContainer = document.querySelector('.content-page .content .container-fluid');

        if (panel && pageContainer) {
            // The qualification gate moves itself to the top of the page first;
            // this panel sits directly underneath it, or takes its place when
            // the gate is not rendered for the current role.
            var gate = document.getElementById('evaluatorQualificationGate');

            if (gate && gate.parentNode) {
                gate.parentNode.insertBefore(panel, gate.nextSibling);
            } else {
                var detailCard = pageContainer.querySelector('.row .col-lg-12 .card');

                if (detailCard) {
                    var detailRow = detailCard.closest('.row');
                    detailRow.parentNode.insertBefore(panel, detailRow);
                } else {
                    pageContainer.insertBefore(panel, pageContainer.firstChild);
                }
            }
        }

        var grantForm = document.getElementById('retentionGrantForm');

        if (grantForm) {
            grantForm.addEventListener('submit', function () {
                if (!grantForm.checkValidity()) {
                    return;
                }

                var submit = document.getElementById('rrp-grant-submit');
                submit.disabled = true;
                submit.innerHTML = '<span class="spinner-border spinner-border-sm mr-1" role="status" aria-hidden="true"></span> Granting...';
            });
        }
    });
</script>
