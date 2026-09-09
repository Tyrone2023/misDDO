<?php
/**
 * Shared styles + autosave for the hand-filled cells of the RQA / CAR sheets.
 *
 * Two kinds of field:
 *   .rqa-sheet-edit  - one value per vacancy (Plantilla Item Number, Date of
 *                      Final Deliberation), saved through Pages/save_rqa_sheet
 *   .rqa-cell-edit   - one value per applicant column (Background Investigation
 *                      Yes/No, the two For Appointment columns), saved through
 *                      Pages/save_rqa_cell keyed on data-record + data-field
 *
 * Everything autosaves on change/blur, exactly like the remarks sheets already
 * do, and prints as plain text with no box around it.
 *
 * Expects $jobID from the controller.
 */
$rqaPostRoles = array('Human Resource Admin', 'HR Staff', 'Super Admin', 'asds', 'sds', 'asst_sds', 'HRMO');
$rqaCanPost = empty($is_excel_export)
    && in_array((string) $this->session->userdata('position'), $rqaPostRoles, true)
    && (int) ($jobID ?? 0) > 0;
$rqaApplicantView = in_array((string) $this->session->userdata('position'), array('user', 'reg'), true);
$rqaPost = $rqaCanPost ? $this->Common->rqa_post((int) $jobID) : null;
$rqaPublished = !empty($rqaPost) && (int) $rqaPost->is_active === 1;
?>
<style>
    .rqa-edit {
        display: block;
        box-sizing: border-box;
        width: 100%;
        min-width: 40px;
        margin: 0;
        padding: 2px 3px;
        border: 1px solid #ccc;
        border-radius: 2px;
        background: #fffdf3;
        font: inherit;
        line-height: 1.35;
        text-align: center;
    }
    .rqa-edit:focus {
        outline: none;
        border-color: #2f6fbf;
        background: #fff;
    }
    .rqa-edit.saving { background: #fff8e1; }
    .rqa-edit.saved  { background: #eefaef; }
    .rqa-edit.failed { background: #fdecea; border-color: #d9534f; }
    .rqa-edit[readonly] { cursor: default; border-color: #e2e8f0; background: #f8fafc; }

    /* header fields sit on the ruled line, so they carry no box of their own */
    .toptable .rqa-edit {
        border: 0;
        border-radius: 0;
        background: #fffdf3;
        text-align: left;
    }

    .data td.rqa-cell { padding: 2px !important; vertical-align: top; }

    .rqa-editbar {
        position: fixed;
        top: 10px;
        left: 12px;
        z-index: 999;
        background: #fff;
        border: 1px solid #d5d5d5;
        border-radius: 4px;
        padding: 6px 10px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, .15);
        font: 12px Calibri, arial, sans-serif;
        color: #666;
        display: flex;
        align-items: center;
        gap: 9px;
    }
    .rqa-post-button {
        cursor: pointer;
        border: 1px solid #166534;
        background: #15803d;
        color: #fff;
        border-radius: 3px;
        padding: 5px 11px;
        font: bold 12px Calibri, arial, sans-serif;
        white-space: nowrap;
    }
    .rqa-post-button:hover { background: #166534; }

    .rqa-post-modal {
        position: fixed;
        inset: 0;
        z-index: 10000;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
        background: rgba(15, 23, 42, .52);
        font-family: Arial, sans-serif;
    }
    .rqa-post-modal.is-open { display: flex; }
    .rqa-post-dialog {
        width: 100%;
        max-width: 520px;
        border-radius: 9px;
        background: #fff;
        box-shadow: 0 20px 50px rgba(0, 0, 0, .28);
        overflow: hidden;
    }
    .rqa-post-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 15px 18px;
        border-bottom: 1px solid #e5e7eb;
    }
    .rqa-post-head h2 { margin: 0; color: #1f2937; font-size: 17px; }
    .rqa-post-close {
        cursor: pointer;
        border: 0;
        background: transparent;
        color: #64748b;
        font-size: 24px;
        line-height: 1;
    }
    .rqa-post-body { padding: 17px 18px; color: #475569; font-size: 13px; line-height: 1.45; }
    .rqa-post-position { margin: 0 0 13px; font-weight: bold; color: #334155; }
    .rqa-post-body label { display: block; margin-bottom: 6px; color: #334155; font-weight: bold; }
    .rqa-post-caption {
        box-sizing: border-box;
        width: 100%;
        min-height: 92px;
        padding: 9px 10px;
        resize: vertical;
        border: 1px solid #cbd5e1;
        border-radius: 5px;
        font: 13px Arial, sans-serif;
        line-height: 1.45;
    }
    .rqa-post-caption:focus { outline: 2px solid #bfdbfe; border-color: #2563eb; }
    .rqa-post-help { margin: 7px 0 0; color: #64748b; font-size: 12px; }
    .rqa-post-message { min-height: 18px; margin-top: 10px; color: #b91c1c; font-size: 12px; }
    .rqa-post-foot {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 8px;
        padding: 12px 18px;
        border-top: 1px solid #e5e7eb;
        background: #f8fafc;
    }
    .rqa-post-foot button {
        cursor: pointer;
        border: 1px solid #cbd5e1;
        border-radius: 4px;
        background: #fff;
        color: #334155;
        padding: 7px 12px;
        font: 13px Arial, sans-serif;
    }
    .rqa-post-foot .rqa-post-submit { border-color: #166534; background: #15803d; color: #fff; }
    .rqa-post-foot .rqa-post-remove { margin-right: auto; border-color: #fecaca; color: #b91c1c; }
    .rqa-post-foot button:disabled { cursor: wait; opacity: .6; }

    @media (max-width: 700px) {
        .rqa-editbar { right: 10px; flex-wrap: wrap; }
    }

    @media print {
        .rqa-editbar, .rqa-post-modal { display: none !important; }
        .rqa-edit {
            border: 0 !important;
            background: transparent !important;
            padding: 0 !important;
        }
    }
</style>

<div class="rqa-editbar no-print" id="rqaEditBar">
    <span id="rqaEditStatus"><?= $rqaApplicantView ? 'Posted RQA - view only' : 'Item no., deliberation date and the hand-filled columns save automatically'; ?></span>
    <?php if ($rqaCanPost) : ?>
        <button type="button" class="rqa-post-button" id="rqaPostOpen">
            <?= $rqaPublished ? 'Update RQA Post' : 'Post RQA'; ?>
        </button>
    <?php endif; ?>
</div>

<?php if ($rqaCanPost) : ?>
<div class="rqa-post-modal no-print" id="rqaPostModal" role="dialog" aria-modal="true" aria-labelledby="rqaPostTitle">
    <div class="rqa-post-dialog">
        <div class="rqa-post-head">
            <h2 id="rqaPostTitle">Post RQA to Applicant Dashboard</h2>
            <button type="button" class="rqa-post-close" id="rqaPostClose" aria-label="Close">&times;</button>
        </div>
        <div class="rqa-post-body">
            <p class="rqa-post-position"><?= htmlspecialchars((string) ($job->jobTitle ?? 'Selected vacancy'), ENT_QUOTES, 'UTF-8'); ?></p>
            <label for="rqaPostCaption">Caption</label>
            <textarea id="rqaPostCaption" class="rqa-post-caption" maxlength="500" placeholder="Example: The CAR-RQA results for this position are now available."><?= htmlspecialchars((string) ($rqaPost->caption ?? ''), ENT_QUOTES, 'UTF-8'); ?></textarea>
            <p class="rqa-post-help">Applicants for this vacancy will see the caption as a clickable announcement. Clicking it opens this report.</p>
            <div class="rqa-post-message" id="rqaPostMessage" aria-live="polite"></div>
        </div>
        <div class="rqa-post-foot">
            <button type="button" class="rqa-post-remove" id="rqaPostRemove"<?= $rqaPublished ? '' : ' style="display:none"'; ?>>Unpublish</button>
            <button type="button" id="rqaPostCancel">Cancel</button>
            <button type="button" class="rqa-post-submit" id="rqaPostSubmit"><?= $rqaPublished ? 'Update Post' : 'Post RQA'; ?></button>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
(function () {
    var jobID    = "<?= (int) ($jobID ?? 0); ?>";
    var cellUrl  = "<?= base_url(); ?>Pages/save_rqa_cell";
    var sheetUrl = "<?= base_url(); ?>Pages/save_rqa_sheet";
    var status   = document.getElementById('rqaEditStatus');
    var fields   = document.querySelectorAll('.rqa-edit');
    var readOnly = <?= $rqaApplicantView ? 'true' : 'false'; ?>;

    function setStatus(text) {
        if (status) { status.textContent = text; }
    }

    function save(input) {
        // nothing changed since the last successful save - skip the round trip
        if (input.value === input.getAttribute('data-saved')) { return; }

        var isSheet = input.classList.contains('rqa-sheet-edit');
        var record  = input.getAttribute('data-record');
        var field   = input.getAttribute('data-field');

        if (!field || (!isSheet && !record)) { return; }

        var base = input.className.replace(/\s*(saving|saved|failed)\s*/g, ' ').trim();
        input.className = base + ' saving';
        setStatus('Saving...');

        var body = 'jobID=' + encodeURIComponent(jobID) +
                   '&field=' + encodeURIComponent(field) +
                   '&value=' + encodeURIComponent(input.value);

        if (!isSheet) {
            body += '&record_no=' + encodeURIComponent(record);
        }

        var xhr = new XMLHttpRequest();
        xhr.open('POST', isSheet ? sheetUrl : cellUrl, true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

        xhr.onload = function () {
            var ok = false;
            try { ok = JSON.parse(xhr.responseText).status === 'success'; } catch (e) { ok = false; }

            if (ok) {
                input.setAttribute('data-saved', input.value);
                input.className = base + ' saved';
                setStatus('Saved');
            } else {
                input.className = base + ' failed';
                setStatus('Not saved - please try again');
            }
        };

        xhr.onerror = function () {
            input.className = base + ' failed';
            setStatus('Not saved - connection error');
        };

        xhr.send(body);
    }

    for (var i = 0; i < fields.length; i++) {
        (function (input) {
            input.setAttribute('data-saved', input.value);
            if (readOnly) {
                input.readOnly = true;
                return;
            }
            input.addEventListener('change', function () { save(input); });
            input.addEventListener('blur', function () { save(input); });
        })(fields[i]);
    }
})();
</script>

<?php if ($rqaCanPost) : ?>
<script>
(function () {
    var jobID       = "<?= (int) $jobID; ?>";
    var modal       = document.getElementById('rqaPostModal');
    var openButton  = document.getElementById('rqaPostOpen');
    var closeButton = document.getElementById('rqaPostClose');
    var cancel      = document.getElementById('rqaPostCancel');
    var submit      = document.getElementById('rqaPostSubmit');
    var remove      = document.getElementById('rqaPostRemove');
    var caption     = document.getElementById('rqaPostCaption');
    var message     = document.getElementById('rqaPostMessage');
    var status      = document.getElementById('rqaEditStatus');
    var saveUrl     = "<?= base_url(); ?>Pages/rqa_post_save";
    var removeUrl   = "<?= base_url(); ?>Pages/rqa_post_unpublish";

    function showModal() {
        message.textContent = '';
        modal.className = 'rqa-post-modal no-print is-open';
        window.setTimeout(function () { caption.focus(); }, 0);
    }

    function hideModal() {
        modal.className = 'rqa-post-modal no-print';
    }

    function setBusy(busy) {
        submit.disabled = busy;
        remove.disabled = busy;
        cancel.disabled = busy;
        closeButton.disabled = busy;
    }

    function request(url, values, callback) {
        var pairs = [];
        var key;
        for (key in values) {
            if (Object.prototype.hasOwnProperty.call(values, key)) {
                pairs.push(encodeURIComponent(key) + '=' + encodeURIComponent(values[key]));
            }
        }

        var xhr = new XMLHttpRequest();
        xhr.open('POST', url, true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.onload = function () {
            var response;
            try { response = JSON.parse(xhr.responseText); }
            catch (e) { response = { status: 'error', message: 'The server returned an invalid response.' }; }
            callback(response);
        };
        xhr.onerror = function () {
            callback({ status: 'error', message: 'Connection error. Please try again.' });
        };
        xhr.send(pairs.join('&'));
    }

    openButton.addEventListener('click', showModal);
    closeButton.addEventListener('click', hideModal);
    cancel.addEventListener('click', hideModal);
    modal.addEventListener('click', function (event) {
        if (event.target === modal) { hideModal(); }
    });
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && modal.className.indexOf('is-open') !== -1) { hideModal(); }
    });

    submit.addEventListener('click', function () {
        var text = caption.value.replace(/^\s+|\s+$/g, '');
        if (text === '') {
            message.textContent = 'Please enter a caption before posting the RQA.';
            caption.focus();
            return;
        }

        setBusy(true);
        message.textContent = 'Posting...';
        request(saveUrl, {
            jobID: jobID,
            caption: text,
            report_url: window.location.href.split('#')[0]
        }, function (response) {
            setBusy(false);
            if (response.status !== 'success') {
                message.textContent = response.message || 'The RQA could not be posted.';
                return;
            }

            openButton.textContent = 'Update RQA Post';
            submit.textContent = 'Update Post';
            remove.style.display = '';
            status.textContent = response.message;
            hideModal();
        });
    });

    remove.addEventListener('click', function () {
        if (!window.confirm('Unpublish this RQA from applicant dashboards?')) { return; }

        setBusy(true);
        message.textContent = 'Unpublishing...';
        request(removeUrl, { jobID: jobID }, function (response) {
            setBusy(false);
            if (response.status !== 'success') {
                message.textContent = response.message || 'The RQA could not be unpublished.';
                return;
            }

            openButton.textContent = 'Post RQA';
            submit.textContent = 'Post RQA';
            remove.style.display = 'none';
            status.textContent = response.message;
            hideModal();
        });
    });
})();
</script>
<?php endif; ?>
