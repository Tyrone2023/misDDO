<?php
/**
 * Shared toolbar + autosave used by the "RQA with Remarks" printable views.
 * Each .remarks-input carries data-record so the note can be stored per
 * (vacancy, application code) through Pages/save_rqa_remark.
 *
 * These sheets are for internal use, so the remark has to be readable in full:
 * the field is a textarea that wraps and grows to fit whatever is typed, both
 * on screen and on the printed page.
 *
 * Expects $jobID from the controller.
 */
?>
<style>
    .data td.remarks-cell {
        min-width: 130px;
        vertical-align: top;
        padding: 2px !important;
    }

    .remarks-input {
        display: block;
        box-sizing: border-box;
        width: 100%;
        min-height: 1.4em;
        margin: 0;
        padding: 2px 3px;
        border: 1px solid #ccc;
        border-radius: 2px;
        background: #fffdf3;
        font: inherit;
        line-height: 1.35;
        text-align: left;
        white-space: pre-wrap;   /* keep the typed line breaks */
        word-break: break-word;  /* long unbroken words still wrap */
        overflow: hidden;        /* height is driven by the auto-grow below */
        resize: none;
    }
    .remarks-input:focus {
        outline: none;
        border-color: #2f6fbf;
        background: #fff;
    }
    .remarks-input.saving { background: #fff8e1; }
    .remarks-input.saved  { background: #eefaef; }
    .remarks-input.failed { background: #fdecea; border-color: #d9534f; }

    .rqa-toolbar {
        position: fixed;
        top: 10px;
        right: 12px;
        z-index: 999;
        background: #fff;
        border: 1px solid #d5d5d5;
        border-radius: 4px;
        padding: 6px 10px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, .15);
        font: 12px Calibri, arial, sans-serif;
    }
    .rqa-toolbar button {
        cursor: pointer;
        border: 1px solid #2f6fbf;
        background: #2f6fbf;
        color: #fff;
        border-radius: 3px;
        padding: 4px 12px;
        font: 12px Calibri, arial, sans-serif;
    }
    .rqa-toolbar button:hover { background: #275c9e; }
    .rqa-toolbar .rqa-status { margin-left: 8px; color: #666; }

    @media print {
        .rqa-toolbar { display: none !important; }
        .remarks-input {
            border: 0 !important;
            background: transparent !important;
            padding: 0 !important;
            overflow: visible !important;
        }
    }
</style>

<div class="rqa-toolbar no-print">
    <button type="button" id="rqaPrint">Print</button>
    <span class="rqa-status" id="rqaStatus">Remarks save automatically</span>
</div>

<script>
(function () {
    var jobID = "<?= (int) ($jobID ?? 0); ?>";
    var url = "<?= base_url(); ?>Pages/save_rqa_remark";
    var status = document.getElementById('rqaStatus');
    var inputs = document.querySelectorAll('.remarks-input');

    function setStatus(text) {
        if (status) { status.textContent = text; }
    }

    /* Grow the field to its content so nothing is cut off - a scrollbar would
       be useless once the sheet is printed. */
    function autoGrow(input) {
        input.style.height = 'auto';
        input.style.height = (input.scrollHeight + 2) + 'px';
    }

    function growAll() {
        for (var i = 0; i < inputs.length; i++) { autoGrow(inputs[i]); }
    }

    function save(input) {
        var record = input.getAttribute('data-record');
        if (!record) { return; }

        // Nothing changed since the last successful save - skip the round trip.
        if (input.value === input.getAttribute('data-saved')) { return; }

        input.className = 'remarks-input saving';
        setStatus('Saving...');

        var xhr = new XMLHttpRequest();
        xhr.open('POST', url, true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

        xhr.onload = function () {
            var ok = false;
            try { ok = JSON.parse(xhr.responseText).status === 'success'; } catch (e) { ok = false; }

            if (ok) {
                input.setAttribute('data-saved', input.value);
                input.className = 'remarks-input saved';
                setStatus('Saved');
            } else {
                input.className = 'remarks-input failed';
                setStatus('Not saved - please try again');
            }
        };

        xhr.onerror = function () {
            input.className = 'remarks-input failed';
            setStatus('Not saved - connection error');
        };

        xhr.send(
            'jobID=' + encodeURIComponent(jobID) +
            '&record_no=' + encodeURIComponent(record) +
            '&remarks=' + encodeURIComponent(input.value)
        );
    }

    for (var i = 0; i < inputs.length; i++) {
        (function (input) {
            input.setAttribute('data-saved', input.value);
            autoGrow(input);
            input.addEventListener('input', function () { autoGrow(input); });
            input.addEventListener('change', function () { save(input); });
            input.addEventListener('blur', function () { save(input); });
        })(inputs[i]);
    }

    // Widths settle only after the fonts and the table layout are final.
    window.addEventListener('load', growAll);
    window.addEventListener('resize', growAll);
    window.addEventListener('beforeprint', growAll);

    document.getElementById('rqaPrint').addEventListener('click', function () {
        growAll();
        window.print();
    });
})();
</script>
