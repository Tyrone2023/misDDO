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
                </p>
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
                                <div class="rrp-applicant-position"><?= $rrp_h($rrpJob->jobTitle ?? '') ?> &middot; <?= $rrp_h($rrpRecordNo) ?></div>
                            </div>
                        </div>

                        <div class="rrp-section">
                            <div class="rrp-section-title">Source application <span class="text-danger">*</span></div>
                            <div class="rrp-section-help">Past applications of this applicant. The scores of the one you select are copied into the application being rated.</div>
                            <select class="form-control" required name="application">
                                <option value="" disabled selected>-- Select application --</option>
                                <?php foreach ($rrpApplications as $rrpRow) : ?>
                                    <?php
                                        $rrpSourceId = (int)($rrpRow->appID ?? 0);

                                        if ($rrpSourceId === 0 || $rrpSourceId === $rrpAppId) {
                                            continue;
                                        }

                                        if ($rrpPType === 1) {
                                            // Teaching: job details are not joined in, and the scores
                                            // live in hris_applications_rating.
                                            $rrpSourceJob = $this->Common->one_cond_row('hris_jobvacancy', 'jobID', $rrpRow->jobID);
                                            $rrpSourceTitle = $rrpSourceJob->jobTitle ?? 'Unknown Position';
                                            $rrpSourceYear = $rrpSourceJob->sy ?? '';
                                            $rrpHasRating = (bool)$this->Common->one_cond_row('hris_applications_rating', 'appID', $rrpSourceId);
                                        } else {
                                            $rrpSourceTitle = $rrpRow->jobTitle ?? 'Unknown Position';
                                            $rrpSourceYear = $rrpRow->sy ?? '';
                                            $rrpHasRating = (bool)$this->Common->one_cond_row('hris_rating_none', 'appID', $rrpSourceId);
                                        }
                                    ?>
                                    <option value="<?= $rrpSourceId ?>" <?= ($rrpPType === 1 && !$rrpHasRating) ? 'disabled' : '' ?>>
                                        <?= $rrp_h($rrpSourceTitle) ?><?= $rrpSourceYear !== '' ? ' (' . $rrp_h($rrpSourceYear) . ')' : '' ?><?= $rrpHasRating ? '' : ' - no prior rating' ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (empty($rrpApplications)) : ?>
                                <small class="text-danger">This applicant has no other application on record, so there is nothing to retain.</small>
                            <?php else : ?>
                                <small class="text-muted">Entries flagged <em>no prior rating</em> have no score to copy.</small>
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
                        <button type="submit" class="btn btn-success" id="rrp-grant-submit">
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
