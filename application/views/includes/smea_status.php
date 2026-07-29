<?php
/**
 * SMEA submission status — shared by the quarterly report (smea_generate) and the
 * year-end summary (smea_generate_summary), for both audiences:
 *
 *   - the school, which sees where its report stands and any remark the division
 *     office attached when it returned the report FOR COMPLIANCE;
 *   - the division office reviewing the report, which additionally gets the
 *     VALIDATE and FOR COMPLIANCE actions.
 *
 * Expects, from the controller: $smea_row (sgod_smea row or null), $school,
 * $school_id, $bcode, $fy, $review_mode. $smea_back is the URL the decision
 * returns to; $smea_review_q is the quarter on show, set by the quarterly report
 * only — the year-end summary leaves it unset and gets no quarter switcher.
 */

$smea_row    = isset($smea_row) ? $smea_row : null;
$review_mode = isset($review_mode) ? $review_mode : false;
$smea_school = isset($school) ? $school : null;

$smea_state = 'none';
if (!empty($smea_row)) {
    $raw = isset($smea_row->status) && $smea_row->status !== '' ? $smea_row->status : 'Submitted';
    if ($raw === 'SDO Validated') {
        $smea_state = 'validated';
    } elseif ($raw === 'For Compliance') {
        $smea_state = 'compliance';
    } else {
        $smea_state = 'submitted';
    }
}

// Short label for the masthead chip, long label for the banner.
$smea_state_labels = array(
    'none'       => array('For Submission', 'Not yet submitted to the division office.'),
    'submitted'  => array('Submitted',      'Submitted and waiting for the division office to validate. It can no longer be edited.'),
    'validated'  => array('SDO Validated',  'Validated by the division office. This report is final and can no longer be edited.'),
    'compliance' => array('For Compliance', 'Returned by the division office for compliance. Address the remarks below, then submit the SMEA again.'),
);
$smea_state_label = $smea_state_labels[$smea_state][0];
$smea_state_blurb = $smea_state_labels[$smea_state][1];

$smea_back = isset($smea_back) ? $smea_back : current_url();
$smea_review_q = isset($smea_review_q) ? (int) $smea_review_q : 0;
?>
<style>
    /* ---- status banner ---- */
    .smea-note{
        display:flex;
        align-items:flex-start;
        gap:11px;
        border:1px solid #dfe6f2;
        border-left:4px solid #6b7890;
        border-radius:9px;
        background:#f8faff;
        padding:11px 14px;
        margin-bottom:16px;
        text-align:left;
    }
    .smea-note-icon{
        flex:none;
        font-size:16px;
        line-height:1.3;
    }
    .smea-note-body{ flex:1 1 auto; min-width:0; }
    .smea-note-title{
        display:block;
        color:#172554;
        font-family:'Montserrat','Segoe UI',Arial,sans-serif;
        font-size:12px;
        font-weight:800;
        letter-spacing:.05em;
        text-transform:uppercase;
        margin-bottom:3px;
    }
    .smea-note p{ margin:0; color:#4c5b72; font-size:12.5px; line-height:1.5; }
    .smea-note blockquote{
        margin:8px 0 0;
        padding:9px 12px;
        background:#ffffff;
        border:1px solid #e5ebf6;
        border-radius:7px;
        color:#24324a;
        font-size:13px;
        line-height:1.5;
        white-space:pre-wrap;
        overflow-wrap:anywhere;
    }
    .smea-note-meta{ display:block; margin-top:6px; color:#6b7890; font-size:11px; }
    .smea-note.is-validated{ border-left-color:#0f7f6c; background:#f2fbf8; }
    .smea-note.is-compliance{ border-left-color:#c2610f; background:#fff8ef; }
    .smea-note.is-submitted{ border-left-color:#3157c8; background:#f4f7ff; }

    /* ---- division-office review bar ---- */
    .smea-review{
        display:flex;
        align-items:center;
        flex-wrap:wrap;
        gap:9px;
        background:#172554;
        border-radius:11px;
        box-shadow:0 7px 22px rgba(23,37,84,.20);
        color:#ffffff;
        padding:11px 14px;
        margin-bottom:16px;
        text-align:left;
    }
    .smea-review-id{ min-width:0; }
    .smea-review-id span{
        display:block;
        color:#9db0e8;
        font-family:'Montserrat','Segoe UI',Arial,sans-serif;
        font-size:10px;
        font-weight:800;
        letter-spacing:.09em;
        text-transform:uppercase;
        margin-bottom:2px;
    }
    .smea-review-id strong{
        display:block;
        font-size:14px;
        font-weight:700;
        line-height:1.3;
        overflow-wrap:anywhere;
    }
    .smea-review-spacer{ flex:1 1 auto; }
    .smea-review-pill{
        display:inline-flex;
        align-items:center;
        gap:6px;
        border-radius:999px;
        font-family:'Montserrat','Segoe UI',Arial,sans-serif;
        font-size:11px;
        font-weight:800;
        letter-spacing:.04em;
        padding:6px 12px;
        text-transform:uppercase;
        white-space:nowrap;
    }
    .smea-review-pill.is-submitted { background:#e8eeff; color:#234aa8; }
    .smea-review-pill.is-validated { background:#d8f3ea; color:#0b6558; }
    .smea-review-pill.is-compliance{ background:#ffe9d2; color:#96490a; }
    .smea-review-btn{
        display:inline-flex;
        align-items:center;
        gap:6px;
        border:1px solid transparent;
        border-radius:7px;
        cursor:pointer;
        font-family:'Montserrat','Segoe UI',Arial,sans-serif;
        font-size:12px;
        font-weight:700;
        padding:8px 14px;
        text-decoration:none;
        white-space:nowrap;
    }
    .smea-review-btn-ghost{ background:rgba(255,255,255,.12); border-color:rgba(255,255,255,.32); color:#ffffff !important; }
    .smea-review-btn-ghost:hover{ background:rgba(255,255,255,.22); color:#ffffff !important; }
    .smea-review-btn-ok{ background:#0f7f6c; border-color:#0f7f6c; color:#ffffff !important; }
    .smea-review-btn-ok:hover{ background:#0b6558; border-color:#0b6558; }
    .smea-review-btn-warn{ background:#d97706; border-color:#d97706; color:#ffffff !important; }
    .smea-review-btn-warn:hover{ background:#b45f04; border-color:#b45f04; }
    .smea-review-quarters{ display:inline-flex; align-items:center; gap:5px; }
    .smea-review-quarters em{
        color:#9db0e8;
        font-family:'Montserrat','Segoe UI',Arial,sans-serif;
        font-size:10px;
        font-style:normal;
        font-weight:800;
        letter-spacing:.09em;
        text-transform:uppercase;
        margin-right:2px;
    }
    .smea-review-q{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        min-width:34px;
        border:1px solid rgba(255,255,255,.32);
        border-radius:7px;
        color:#ffffff !important;
        font-family:'Montserrat','Segoe UI',Arial,sans-serif;
        font-size:12px;
        font-weight:700;
        padding:6px 8px;
        text-decoration:none;
    }
    .smea-review-q:hover{ background:rgba(255,255,255,.18); color:#ffffff !important; }
    .smea-review-q.is-active{ background:#ffffff; border-color:#ffffff; color:#172554 !important; }

    /* ---- compliance remark dialog ---- */
    .smea-cmp-overlay{
        display:none;
        position:fixed;
        inset:0;
        background:rgba(23,37,84,.45);
        z-index:1060;
        align-items:flex-start;
        justify-content:center;
    }
    .smea-cmp-overlay.show{ display:flex; }
    .smea-cmp{
        background:#ffffff;
        width:520px;
        max-width:92%;
        margin-top:8vh;
        border-radius:12px;
        box-shadow:0 14px 40px rgba(35,53,92,.28);
        overflow:hidden;
        text-align:left;
    }
    .smea-cmp-head{
        display:flex;
        align-items:center;
        justify-content:space-between;
        background:#d97706;
        color:#ffffff;
        padding:12px 16px;
        font-family:'Montserrat','Segoe UI',Arial,sans-serif;
        font-size:15px;
        font-weight:700;
    }
    .smea-cmp-head button{
        background:none; border:none; color:#fff; cursor:pointer;
        font-size:22px; line-height:1;
    }
    .smea-cmp-body{ padding:18px 16px; }
    .smea-cmp-body p{ margin:0 0 12px; color:#6b7890; font-size:13px; line-height:1.5; }
    .smea-cmp-body label{
        display:block;
        color:#58647a;
        font-size:11px;
        font-weight:800;
        letter-spacing:.035em;
        text-transform:uppercase;
        margin-bottom:5px;
    }
    .smea-cmp-body textarea{
        width:100%;
        min-height:120px;
        padding:9px 11px;
        border:1px solid #d7e0ee;
        border-radius:8px;
        box-sizing:border-box;
        color:#24324a;
        font-family:inherit;
        font-size:14px;
        resize:vertical;
    }
    .smea-cmp-body textarea:focus{
        border-color:#e0a35a;
        box-shadow:0 0 0 3px rgba(217,119,6,.12);
        outline:none;
    }
    .smea-cmp-actions{ display:flex; gap:8px; margin-top:14px; }

    @media print{
        .smea-review, .smea-cmp-overlay{ display:none !important; }
        .smea-note{
            background:transparent !important;
            border:.65px solid #111;
            border-left:1.2mm solid #111;
            border-radius:0;
        }
        .smea-note-title, .smea-note p, .smea-note blockquote, .smea-note-meta{ color:#000 !important; }
        .smea-note blockquote{ background:transparent !important; border:.65px solid #111; }
    }
</style>

<?php if ($review_mode && !empty($smea_row)) : ?>
    <div class="smea-review">
        <div class="smea-review-id">
            <span>Division Office Review</span>
            <strong>
                <?= html_escape($smea_school ? $smea_school->schoolName : $school_id); ?>
                &middot; Batch <?= html_escape($bcode); ?> &middot; FY <?= html_escape($fy); ?>
            </strong>
        </div>

        <?php if ($smea_review_q) : ?>
            <div class="smea-review-quarters">
                <em>Quarter</em>
                <?php for ($rq = 1; $rq <= 4; $rq++) : ?>
                    <a class="smea-review-q <?= (int) $q === $rq ? 'is-active' : ''; ?>"
                       href="<?= base_url(); ?>Page/smea_review/<?= (int) $smea_row->id; ?>/<?= $rq; ?>">Q<?= $rq; ?></a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>

        <span class="smea-review-spacer"></span>

        <span class="smea-review-pill is-<?= $smea_state; ?>"><?= html_escape($smea_state_label); ?></span>

        <?php if ($smea_review_q) : ?>
            <a class="smea-review-btn smea-review-btn-ghost" target="_blank"
               href="<?= base_url(); ?>Page/smea_review_summary/<?= (int) $smea_row->id; ?>">View SMEA Summary</a>
        <?php else : ?>
            <a class="smea-review-btn smea-review-btn-ghost" target="_blank"
               href="<?= base_url(); ?>Page/smea_review/<?= (int) $smea_row->id; ?>/1">View SMEA</a>
        <?php endif; ?>

        <?php if ($smea_state !== 'validated') : ?>
            <form method="post" action="<?= base_url(); ?>Page/smea_validate" style="margin:0;"
                  onsubmit="return confirm('Mark this SMEA as SDO VALIDATED?');">
                <input type="hidden" name="id" value="<?= (int) $smea_row->id; ?>">
                <input type="hidden" name="back" value="<?= html_escape($smea_back); ?>">
                <button type="submit" class="smea-review-btn smea-review-btn-ok">&#10003; Validate</button>
            </form>
        <?php endif; ?>

        <?php if ($smea_state !== 'compliance') : ?>
            <button type="button" class="smea-review-btn smea-review-btn-warn" id="smeaComplianceOpen">&#9888; For Compliance</button>
        <?php endif; ?>
    </div>

    <div class="smea-cmp-overlay" id="smeaComplianceModal">
        <div class="smea-cmp">
            <div class="smea-cmp-head">
                <span>Return for Compliance</span>
                <button type="button" class="smea-cmp-close" aria-label="Close">&times;</button>
            </div>
            <form method="post" action="<?= base_url(); ?>Page/smea_compliance">
                <div class="smea-cmp-body">
                    <p>
                        The school will see this remark on its SMEA and will be able to edit
                        and submit the report again.
                    </p>
                    <label for="smeaComplianceRemarks">Remarks / Comment <span style="color:#d84a4a;">*</span></label>
                    <textarea id="smeaComplianceRemarks" name="sdo_remarks" required
                              placeholder="What has to be corrected or completed before this SMEA can be validated?"></textarea>

                    <input type="hidden" name="id" value="<?= (int) $smea_row->id; ?>">
                    <input type="hidden" name="back" value="<?= html_escape($smea_back); ?>">

                    <div class="smea-cmp-actions">
                        <button type="submit" class="smea-review-btn smea-review-btn-warn">Return for Compliance</button>
                        <button type="button" class="smea-review-btn smea-cmp-close"
                                style="background:#f0f2f5;border-color:#b8c0cc;color:#3f4a5c !important;">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
    (function () {
        var overlay = document.getElementById('smeaComplianceModal');
        var opener  = document.getElementById('smeaComplianceOpen');
        if (!overlay || !opener) { return; }

        function close() { overlay.classList.remove('show'); }

        opener.addEventListener('click', function () {
            overlay.classList.add('show');
            var box = document.getElementById('smeaComplianceRemarks');
            if (box) { box.focus(); }
        });

        overlay.addEventListener('click', function (e) {
            if (e.target === overlay || e.target.closest('.smea-cmp-close')) { close(); }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') { close(); }
        });
    })();
    </script>
<?php endif; ?>

<?php if ($smea_state !== 'none') : ?>
    <div class="smea-note is-<?= $smea_state; ?>">
        <span class="smea-note-icon">
            <?= $smea_state === 'validated' ? '&#10003;' : ($smea_state === 'compliance' ? '&#9888;' : '&#8635;'); ?>
        </span>
        <div class="smea-note-body">
            <span class="smea-note-title"><?= html_escape($smea_state_label); ?></span>
            <p><?= html_escape($smea_state_blurb); ?></p>

            <?php if ($smea_state === 'compliance' && trim((string) $smea_row->sdo_remarks) !== '') : ?>
                <blockquote><?= html_escape($smea_row->sdo_remarks); ?></blockquote>
            <?php endif; ?>

            <?php if (!empty($smea_row->date_validated)) : ?>
                <span class="smea-note-meta">
                    <?= $smea_state === 'compliance' ? 'Returned by' : 'Validated by'; ?>
                    <?= html_escape($smea_row->validated_by !== '' ? $smea_row->validated_by : 'the division office'); ?>
                    on <?= html_escape(date('F d, Y g:i A', strtotime($smea_row->date_validated))); ?>
                </span>
            <?php elseif (!empty($smea_row->date_submit)) : ?>
                <span class="smea-note-meta">Submitted on <?= html_escape(date('F d, Y', strtotime($smea_row->date_submit))); ?></span>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>
