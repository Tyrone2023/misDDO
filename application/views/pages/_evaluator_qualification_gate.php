<?php
$gate = $evaluator_qualification_gate ?? [];
$gateApplication = $gate['application'] ?? null;
$gateApplicant = $gate['applicant'] ?? null;
$gateJob = $gate['job'] ?? null;

if (!$gateApplication || !$gateApplicant || !$gateJob) {
    return;
}

$gate_h = static function ($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
};

$isDisqualified = (int)($gateApplication->dq ?? 0) === 2;
$recordNo = trim((string)($gateApplicant->record_no ?? $gateApplication->applicant_id ?? ''));
$applicantName = trim(implode(' ', array_filter([
    trim((string)($gateApplicant->FirstName ?? '')),
    trim((string)($gateApplicant->MiddleName ?? '')),
    trim((string)($gateApplicant->LastName ?? '')),
], static function ($part) {
    return $part !== '';
})));
?>

<style>
    .eqg-panel {
        position: relative;
        z-index: 5;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        margin: 0 0 20px;
        border: 1px solid <?= $isDisqualified ? '#f1b9b9' : '#bcd8f5' ?>;
        border-left: 5px solid <?= $isDisqualified ? '#cf5555' : '#2878db' ?>;
        border-radius: 14px;
        background: <?= $isDisqualified ? '#fff6f6' : 'linear-gradient(110deg, #f3f8ff, #ffffff)' ?>;
        box-shadow: 0 8px 24px rgba(31, 65, 101, .08);
        padding: 18px 20px;
    }

    .eqg-panel-main {
        display: flex;
        min-width: 0;
        align-items: center;
        gap: 14px;
    }

    .eqg-panel-icon {
        display: inline-flex;
        width: 48px;
        height: 48px;
        flex: 0 0 48px;
        align-items: center;
        justify-content: center;
        border-radius: 13px;
        background: <?= $isDisqualified ? '#fde4e4' : '#dfedff' ?>;
        color: <?= $isDisqualified ? '#c34d4d' : '#276ebc' ?>;
        font-size: 24px;
    }

    .eqg-panel-kicker {
        margin-bottom: 3px;
        color: <?= $isDisqualified ? '#a83d3d' : '#2969aa' ?>;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .eqg-panel h4 {
        margin: 0 0 4px;
        color: #263c52;
        font-size: 16px;
        font-weight: 700;
    }

    .eqg-panel p {
        margin: 0;
        color: #6c7d8e;
        font-size: 12px;
        line-height: 1.55;
    }

    .eqg-panel .btn {
        display: inline-flex;
        min-height: 40px;
        flex: 0 0 auto;
        align-items: center;
        gap: 7px;
        border-radius: 9px;
        padding: 9px 14px;
        font-weight: 700;
    }

    .eqg-modal .modal-content {
        overflow: hidden;
        border: 0;
        border-radius: 16px;
        box-shadow: 0 24px 70px rgba(25, 47, 72, .28);
    }

    .eqg-modal .modal-header {
        align-items: flex-start;
        border: 0;
        background: linear-gradient(120deg, #153f6d, #2878db);
        padding: 21px 24px;
        color: #fff;
    }

    .eqg-modal .modal-title,
    .eqg-modal .close {
        color: #fff;
    }

    .eqg-modal .modal-title {
        font-size: 18px;
        font-weight: 700;
    }

    .eqg-modal-subtitle {
        margin-top: 4px;
        color: rgba(255, 255, 255, .74);
        font-size: 12px;
    }

    .eqg-modal .modal-body {
        padding: 22px 24px;
        background: #fbfcfe;
    }

    .eqg-applicant-summary {
        display: flex;
        align-items: center;
        gap: 11px;
        margin-bottom: 18px;
        border: 1px solid #e2e9f1;
        border-radius: 11px;
        background: #fff;
        padding: 12px 14px;
    }

    .eqg-applicant-summary i {
        color: #2878db;
        font-size: 22px;
    }

    .eqg-applicant-name {
        color: #293f55;
        font-size: 13px;
        font-weight: 700;
    }

    .eqg-applicant-position {
        margin-top: 2px;
        color: #7b8a99;
        font-size: 11px;
    }

    .eqg-section {
        margin-bottom: 20px;
        border: 1px solid #e4eaf1;
        border-radius: 12px;
        background: #fff;
        padding: 17px;
    }

    .eqg-section-title {
        margin-bottom: 4px;
        color: #293e53;
        font-size: 13px;
        font-weight: 700;
    }

    .eqg-section-help {
        margin-bottom: 13px;
        color: #81909f;
        font-size: 11px;
    }

    .eqg-doc-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 9px 18px;
    }

    .eqg-doc-grid .custom-control-label {
        color: #465b6f;
        font-size: 12px;
        line-height: 1.45;
    }

    .eqg-review-confirmation {
        margin-top: 14px;
        border-top: 1px solid #edf1f5;
        padding-top: 13px;
    }

    .eqg-review-confirmation .custom-control-label {
        color: #27496b;
        font-size: 12px;
        font-weight: 700;
    }

    .eqg-decision-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 11px;
    }

    .eqg-decision {
        position: relative;
        margin: 0;
    }

    .eqg-decision input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .eqg-decision label {
        display: flex;
        min-height: 76px;
        cursor: pointer;
        align-items: center;
        gap: 11px;
        margin: 0;
        border: 2px solid #e3e9ef;
        border-radius: 11px;
        padding: 13px;
        transition: .16s ease;
    }

    .eqg-decision-icon {
        display: inline-flex;
        width: 36px;
        height: 36px;
        flex: 0 0 36px;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        font-size: 19px;
    }

    .eqg-decision-qualified .eqg-decision-icon { background: #e3f6ed; color: #16815f; }
    .eqg-decision-disqualified .eqg-decision-icon { background: #fdeaea; color: #c95151; }
    .eqg-decision input:checked + label { box-shadow: 0 0 0 3px rgba(40, 120, 219, .09); }
    .eqg-decision-qualified input:checked + label { border-color: #31a67e; background: #f3fbf7; }
    .eqg-decision-disqualified input:checked + label { border-color: #d96666; background: #fff7f7; }

    .eqg-decision strong,
    .eqg-decision small {
        display: block;
    }

    .eqg-decision strong { color: #32485d; font-size: 13px; }
    .eqg-decision small { margin-top: 2px; color: #8492a0; font-size: 10px; line-height: 1.35; }

    .eqg-reason-wrap label {
        color: #354b61;
        font-size: 12px;
        font-weight: 700;
    }

    .eqg-reason-wrap textarea {
        min-height: 95px;
        border-color: #dce4ec;
        border-radius: 9px;
        resize: vertical;
    }

    .eqg-modal .modal-footer {
        border-top: 1px solid #e8edf3;
        padding: 15px 24px;
        background: #fff;
    }

    .eqg-modal .modal-footer .btn {
        border-radius: 8px;
        font-weight: 700;
    }

    @media (max-width: 767.98px) {
        .eqg-panel {
            align-items: flex-start;
            flex-direction: column;
        }

        .eqg-panel .btn {
            width: 100%;
            justify-content: center;
        }

        .eqg-doc-grid,
        .eqg-decision-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="eqg-panel" id="evaluatorQualificationGate">
    <div class="eqg-panel-main">
        <span class="eqg-panel-icon"><i class="mdi <?= $isDisqualified ? 'mdi-account-remove-outline' : 'mdi-clipboard-check-outline' ?>"></i></span>
        <div>
            <div class="eqg-panel-kicker">Qualification stage</div>
            <?php if ($isDisqualified): ?>
                <h4>Applicant marked Disqualified</h4>
                <p>The document review and disqualification reason were saved. Rating remains unavailable for this application.</p>
            <?php else: ?>
                <h4>Complete the qualification review before rating</h4>
                <p>Check the mandatory documents and record a Qualified or Disqualified decision. Qualified applicants will be endorsed for rating automatically.</p>
            <?php endif; ?>
        </div>
    </div>
    <?php if (!$isDisqualified): ?>
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#evaluatorQualificationModal">
            <i class="mdi mdi-clipboard-check-outline"></i> Qualification remarks
        </button>
    <?php endif; ?>
</div>

<?php if (!$isDisqualified): ?>
    <div class="modal fade eqg-modal" id="evaluatorQualificationModal" tabindex="-1" role="dialog" aria-labelledby="evaluatorQualificationTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="evaluatorQualificationForm" action="<?= $gate_h(base_url('EvaluatorAssigned/qualification')) ?>" method="post">
                    <div class="modal-header">
                        <div>
                            <h5 class="modal-title" id="evaluatorQualificationTitle">Qualification remarks</h5>
                            <div class="eqg-modal-subtitle">Complete this review before entering any applicant rating.</div>
                        </div>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>

                    <div class="modal-body">
                        <input type="hidden" name="appID" value="<?= (int)$gateApplication->appID ?>">
                        <input type="hidden" name="id" value="<?= $gate_h($gateApplicant->id ?? $gateApplication->applicant_id) ?>">
                        <input type="hidden" name="jobID" value="<?= (int)$gateApplication->jobID ?>">
                        <input type="hidden" name="job_type" value="<?= (int)($gateJob->job_type ?? 0) ?>">
                        <input type="hidden" name="job_fy" value="<?= $gate_h($gateApplication->app_year ?? $gateJob->sy ?? date('Y')) ?>">
                        <input type="hidden" name="record_no" value="<?= $gate_h($recordNo) ?>">
                        <input type="hidden" name="record" value="<?= $gate_h($recordNo) ?>">
                        <input type="hidden" name="return_url" value="<?= $gate_h(current_url()) ?>">

                        <div class="eqg-applicant-summary">
                            <i class="mdi mdi-account-circle-outline"></i>
                            <div>
                                <div class="eqg-applicant-name"><?= $gate_h($applicantName !== '' ? $applicantName : 'Applicant #' . $gateApplication->appID) ?></div>
                                <div class="eqg-applicant-position"><?= $gate_h($gateJob->jobTitle ?? '') ?> · <?= $gate_h($recordNo) ?></div>
                            </div>
                        </div>

                        <div class="eqg-section">
                            <div class="eqg-section-title">Mandatory documents presented <span class="text-danger">*</span></div>
                            <div class="eqg-section-help">Check every document or qualification item that the applicant presented and you verified.</div>
                            <div class="eqg-doc-grid">
                                <?php
                                $documents = [
                                    'li' => 'Letter of Intent',
                                    'da_pds' => 'Duly Accomplished PDS',
                                    'prc' => 'Valid PRC License, when applicable',
                                    'trbd' => 'Transcript of Records',
                                    'omni' => 'Omnibus Sworn Statement / Annex C',
                                    'educ' => 'Education qualification',
                                    'exp' => 'Experience qualification',
                                    'tr' => 'Training qualification',
                                    'eli' => 'Eligibility qualification',
                                ];
                                foreach ($documents as $field => $label):
                                    $controlId = 'eqg-document-' . $field;
                                ?>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="<?= $gate_h($controlId) ?>" name="<?= $gate_h($field) ?>" value="1">
                                        <label class="custom-control-label" for="<?= $gate_h($controlId) ?>"><?= $gate_h($label) ?></label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="eqg-review-confirmation custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="eqg-documents-reviewed" name="documents_reviewed" value="1" required>
                                <label class="custom-control-label" for="eqg-documents-reviewed">I confirm that I reviewed the mandatory documents presented by this applicant.</label>
                            </div>
                        </div>

                        <div class="eqg-section">
                            <div class="eqg-section-title">Evaluator decision <span class="text-danger">*</span></div>
                            <div class="eqg-section-help">A Qualified decision endorses the application for rating. A Disqualified decision requires a reason.</div>
                            <div class="eqg-decision-grid">
                                <div class="eqg-decision eqg-decision-qualified">
                                    <input type="radio" id="eqg-qualified" name="remarks" value="1" required>
                                    <label for="eqg-qualified">
                                        <span class="eqg-decision-icon"><i class="mdi mdi-check-circle-outline"></i></span>
                                        <span><strong>Qualified</strong><small>Endorse this applicant for rating.</small></span>
                                    </label>
                                </div>
                                <div class="eqg-decision eqg-decision-disqualified">
                                    <input type="radio" id="eqg-disqualified" name="remarks" value="2" required>
                                    <label for="eqg-disqualified">
                                        <span class="eqg-decision-icon"><i class="mdi mdi-close-circle-outline"></i></span>
                                        <span><strong>Disqualified</strong><small>Stop rating and record the reason.</small></span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="eqg-reason-wrap">
                            <label for="eqg-reason">Decision notes / disqualification reason <span class="text-danger" id="eqg-reason-required" hidden>*</span></label>
                            <textarea class="form-control" id="eqg-reason" name="reason" maxlength="2000" placeholder="Required when the applicant is disqualified. You may also add notes for a qualified decision."></textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="eqg-submit"><i class="mdi mdi-content-save-outline mr-1"></i> Save decision</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var gate = document.getElementById('evaluatorQualificationGate');
        var pageContainer = document.querySelector('.content-page .content .container-fluid');

        if (gate && pageContainer) {
            pageContainer.insertBefore(gate, pageContainer.firstChild);
        }

        // Rating controls stay unavailable until the server changes the status
        // to Endorsed for Rating. Document/profile viewing remains available.
        document.querySelectorAll('[data-target]').forEach(function (control) {
            var target = String(control.getAttribute('data-target') || '').toLowerCase();
            if (target.indexOf('rating') !== -1 || /qs$/.test(target)) {
                control.style.display = 'none';
                control.setAttribute('aria-hidden', 'true');
            }
        });

        document.querySelectorAll('form[action]').forEach(function (form) {
            var action = String(form.getAttribute('action') || '').toLowerCase();
            if (/pages\/update_[^/]*rate/.test(action)) {
                form.querySelectorAll('input, select, textarea, button').forEach(function (control) {
                    control.disabled = true;
                });
                form.addEventListener('submit', function (event) {
                    event.preventDefault();
                    event.stopPropagation();
                });
            }
        });

        var form = document.getElementById('evaluatorQualificationForm');
        if (!form) {
            return;
        }

        var reason = document.getElementById('eqg-reason');
        var requiredMarker = document.getElementById('eqg-reason-required');
        var decisions = form.querySelectorAll('input[name="remarks"]');

        function syncReasonRequirement() {
            var selected = form.querySelector('input[name="remarks"]:checked');
            var isDisqualified = selected && selected.value === '2';
            reason.required = Boolean(isDisqualified);
            requiredMarker.hidden = !isDisqualified;
        }

        decisions.forEach(function (decision) {
            decision.addEventListener('change', syncReasonRequirement);
        });

        form.addEventListener('submit', function () {
            syncReasonRequirement();
            if (!form.checkValidity()) {
                return;
            }

            var submit = document.getElementById('eqg-submit');
            submit.disabled = true;
            submit.innerHTML = '<span class="spinner-border spinner-border-sm mr-1" role="status" aria-hidden="true"></span> Saving...';
        });
    });
</script>
