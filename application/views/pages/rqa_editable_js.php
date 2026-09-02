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
    }

    @media print {
        .rqa-editbar { display: none !important; }
        .rqa-edit {
            border: 0 !important;
            background: transparent !important;
            padding: 0 !important;
        }
    }
</style>

<div class="rqa-editbar no-print" id="rqaEditBar">Item no., deliberation date and the hand-filled columns save automatically</div>

<script>
(function () {
    var jobID    = "<?= (int) ($jobID ?? 0); ?>";
    var cellUrl  = "<?= base_url(); ?>Pages/save_rqa_cell";
    var sheetUrl = "<?= base_url(); ?>Pages/save_rqa_sheet";
    var bar      = document.getElementById('rqaEditBar');
    var fields   = document.querySelectorAll('.rqa-edit');

    function setStatus(text) {
        if (bar) { bar.textContent = text; }
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
            input.addEventListener('change', function () { save(input); });
            input.addEventListener('blur', function () { save(input); });
        })(fields[i]);
    }
})();
</script>
