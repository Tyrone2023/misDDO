<?php
/*
 * Per-vacancy signatory block for the RQA / CAR reports.
 *
 * Expects $vsign - the rows of hris_vacancy_signatories for this jobID, already
 * ordered (see Vacancy_signatory_model::get_by_job). Renders nothing at all when
 * the vacancy has no signatory encoded, so a report that was never set up looks
 * exactly as it did before.
 *
 * Optional: $vsign_caption overrides the heading line.
 *
 * Maintained from Page/jobVacancy > Actions > Signatories
 * (VacancySignatories/index/{jobID}).
 */

$vsign_rows = (isset($vsign) && is_array($vsign)) ? $vsign : array();

if (!empty($vsign_rows)) :

    $vsign_caption = isset($vsign_caption)
        ? $vsign_caption
        : 'Prepared by the HRMPSB <span class="vsig-note">(All members should affix signature)</span>';

    // Excel exports (render_rqa_report) are HTML saved as .xls - Excel cannot
    // fetch the signature over http, so those sheets get the names only.
    $vsign_excel = !empty($is_excel_export);

    // A slot is one cell in a five-column print grid. Unlike array_chunk(),
    // explicit slots retain intentional blanks (for example, an approving
    // authority that must appear at the far right).
    $vsign_slots = array();
    $vsign_max_slot = 1;
    foreach ($vsign_rows as $vsign_item) {
        $vsign_slot = isset($vsign_item->print_slot) && (int) $vsign_item->print_slot > 0
            ? (int) $vsign_item->print_slot
            : max(1, (int) $vsign_item->signatory_order);
        $vsign_slot = min(50, $vsign_slot);

        // Old/hand-edited data may contain a duplicate. Keep both visible by
        // placing the later row in the next free cell for this render.
        while (isset($vsign_slots[$vsign_slot]) && $vsign_slot < 50) {
            $vsign_slot++;
        }

        $vsign_slots[$vsign_slot] = $vsign_item;
        $vsign_max_slot = max($vsign_max_slot, $vsign_slot);
    }

    $vsign_row_count = (int) ceil($vsign_max_slot / 5);
    $vsign_columns = 5;

    $vsign_manage_positions = array('Human Resource Admin', 'HR Staff', 'Super Admin', 'asds', 'sds');
    $vsign_can_manage = !$vsign_excel
        && $this->session->logged_in != false
        && in_array((string) $this->session->position, $vsign_manage_positions, true);
?>

<style>
    .vsig-wrap { margin-top: 26px; }
    .vsig-cap {
        margin: 0 0 6px 0;
        font-size: 13px;
        font-weight: bold;
    }
    .vsig-cap .vsig-note { font-weight: normal; font-style: italic; font-size: 12px; }
    .vsig-editor {
        margin: 0 0 10px 0;
        padding: 7px 9px;
        border: 1px solid #cdd9e8;
        border-radius: 4px;
        background: #f4f8fc;
        color: #44546a;
        font: 11px Calibri, Arial, sans-serif;
    }
    .vsig-editor a { color: #245c9d; font-weight: bold; }
    .vsig-editor-status { float: right; }
    table.vsig, table.vsig td {
        border: 0 !important;
        text-align: center;
        vertical-align: bottom;
    }
    table.vsig { width: 100% !important; table-layout: fixed; border-collapse: collapse; }
    table.vsig td { position: relative; width: 20%; padding: 10px 6px 22px 6px; }
    .vsig-label {
        min-height: 20px;
        margin-bottom: 2px;
        font-size: 12px;
        font-weight: bold;
        text-align: left;
    }
    .vsig-label-input {
        box-sizing: border-box;
        width: 100%;
        padding: 2px 4px;
        border: 1px dashed #9db2ca;
        border-radius: 2px;
        background: #fffdf3;
        color: #111;
        font: inherit;
        font-weight: bold;
    }
    .vsig-label-input:focus { outline: none; border-color: #2f6fbf; background: #fff; }
    .vsig-tools {
        display: grid;
        grid-template-columns: repeat(3, 24px);
        grid-template-rows: repeat(3, 21px);
        justify-content: center;
        align-items: center;
        margin: 0 auto 3px auto;
    }
    .vsig-move {
        border: 1px solid #c9d4e1;
        border-radius: 3px;
        background: #fff;
        color: #536b86;
        font: bold 14px/18px Arial, sans-serif;
        cursor: pointer;
    }
    .vsig-move:hover { border-color: #668bb5; color: #245c9d; }
    .vsig-move:disabled { opacity: .45; cursor: not-allowed; }
    .vsig-move-up { grid-column: 2; grid-row: 1; }
    .vsig-move-left { grid-column: 1; grid-row: 2; }
    .vsig-slot { grid-column: 2; grid-row: 2; font: bold 9px/1 Arial, sans-serif; color: #61758c; }
    .vsig-move-right { grid-column: 3; grid-row: 2; }
    .vsig-move-down { grid-column: 2; grid-row: 3; }
    .vsig-empty {
        box-sizing: border-box;
        min-height: 104px;
        padding-top: 42px;
        border: 1px dashed #dce4ec;
        color: #a2adba;
        font: 10px Calibri, Arial, sans-serif;
    }
    .vsig-img {
        display: block;
        margin: 0 auto -8px auto;
        height: 46px;
        max-width: 92%;
        object-fit: contain;
    }
    .vsig-space { height: 46px; }
    .vsig-name {
        display: inline-block;
        border-bottom: 1px solid #000;
        font-weight: bold;
        font-size: 14px;
        padding: 0 12px;
        white-space: nowrap;
    }
    .vsig-pos { font-size: 11px; line-height: 1.25; margin-top: 2px; }
    .vsig-role { font-size: 11px; line-height: 1.25; }

    @media print {
        .vsig-editor, .vsig-tools, .vsig-empty { display: none !important; }
        .vsig-wrap { margin-top: 20px; page-break-inside: avoid; break-inside: avoid; }
        table.vsig tr { page-break-inside: avoid; break-inside: avoid; }
        table.vsig, table.vsig td { border: 0 !important; }
        table.vsig td { padding: 8px 5px 18px 5px; }
        .vsig-img { height: 42px; }
        .vsig-space { height: 42px; }
        .vsig-label-input {
            border: 0 !important;
            background: transparent !important;
            padding: 0 !important;
        }
        .vsig-label-input::placeholder { color: transparent !important; }
    }
</style>

<div class="vsig-wrap">
    <p class="vsig-cap"><?= $vsign_caption; ?></p>

    <?php if ($vsign_can_manage) : ?>
        <div class="vsig-editor">
            Use the arrows to move each signatory through the five-column grid. Type an optional heading such as
            <strong>Approving Authority:</strong>; it saves on change.
            <a href="<?= base_url(); ?>VacancySignatories/index/<?= (int) ($vsign_rows[0]->job_id ?? 0); ?>" target="_blank">Manage names and signatures</a>
            <span class="vsig-editor-status" id="vsigEditorStatus">Layout ready</span>
        </div>
    <?php endif; ?>

    <table class="vsig">
        <?php for ($vsign_row = 1; $vsign_row <= $vsign_row_count; $vsign_row++) : ?>
            <tr>
                <?php for ($vsign_col = 1; $vsign_col <= $vsign_columns; $vsign_col++) : ?>
                    <?php $vsign_current_slot = (($vsign_row - 1) * 5) + $vsign_col; ?>
                    <td>
                        <?php if (isset($vsign_slots[$vsign_current_slot])) : ?>
                            <?php $vsig = $vsign_slots[$vsign_current_slot]; ?>

                            <?php if ($vsign_can_manage) : ?>
                                <div class="vsig-tools">
                                    <button type="button" class="vsig-move vsig-move-up" data-id="<?= (int) $vsig->id; ?>" data-direction="up" title="Move up"<?= $vsign_row === 1 ? ' disabled' : ''; ?>>&#8593;</button>
                                    <button type="button" class="vsig-move vsig-move-left" data-id="<?= (int) $vsig->id; ?>" data-direction="left" title="Move left"<?= $vsign_col === 1 ? ' disabled' : ''; ?>>&#8592;</button>
                                    <span class="vsig-slot">R<?= $vsign_row; ?> C<?= $vsign_col; ?></span>
                                    <button type="button" class="vsig-move vsig-move-right" data-id="<?= (int) $vsig->id; ?>" data-direction="right" title="Move right"<?= $vsign_col === 5 ? ' disabled' : ''; ?>>&#8594;</button>
                                    <button type="button" class="vsig-move vsig-move-down" data-id="<?= (int) $vsig->id; ?>" data-direction="down" title="Move down"<?= $vsign_row === 10 ? ' disabled' : ''; ?>>&#8595;</button>
                                </div>
                            <?php endif; ?>

                            <?php if ($vsign_can_manage) : ?>
                                <div class="vsig-label">
                                    <input type="text" class="vsig-label-input" maxlength="200"
                                           data-id="<?= (int) $vsig->id; ?>"
                                           value="<?= html_escape($vsig->print_label ?? ''); ?>"
                                           placeholder="Add heading, e.g. Approving Authority:">
                                </div>
                            <?php else : ?>
                                <div class="vsig-label"><?= html_escape($vsig->print_label ?? ''); ?></div>
                            <?php endif; ?>

                            <?php if (!$vsign_excel && trim((string) $vsig->esig) !== '') : ?>
                                <img class="vsig-img"
                                     src="<?= base_url(); ?>uploads/esig/<?= rawurlencode($vsig->esig); ?>"
                                     alt="">
                            <?php elseif (!$vsign_excel) : ?>
                                <div class="vsig-space"></div>
                            <?php endif; ?>

                            <span class="vsig-name"><?= html_escape(strtoupper($vsig->name)); ?></span>

                            <?php if (trim((string) $vsig->designation) !== '') : ?>
                                <div class="vsig-pos"><?= html_escape($vsig->designation); ?></div>
                            <?php endif; ?>

                            <?php if (trim((string) $vsig->sign_role) !== '') : ?>
                                <div class="vsig-role"><?= html_escape($vsig->sign_role); ?></div>
                            <?php endif; ?>
                        <?php elseif ($vsign_can_manage) : ?>
                            <div class="vsig-empty">Empty<br>R<?= $vsign_row; ?> C<?= $vsign_col; ?></div>
                        <?php endif; ?>
                    </td>
                <?php endfor; ?>
            </tr>
        <?php endfor; ?>
    </table>
</div>

<?php if ($vsign_can_manage) : ?>
<script>
(function () {
    var status = document.getElementById('vsigEditorStatus');
    var moveUrl = '<?= base_url(); ?>VacancySignatories/move_layout';
    var labelUrl = '<?= base_url(); ?>VacancySignatories/save_label';

    function encode(data) {
        var parts = [];
        for (var key in data) {
            if (Object.prototype.hasOwnProperty.call(data, key)) {
                parts.push(encodeURIComponent(key) + '=' + encodeURIComponent(data[key]));
            }
        }
        return parts.join('&');
    }

    function post(url, data, done) {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', url, true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.onload = function () {
            var result = null;
            try { result = JSON.parse(xhr.responseText); } catch (e) { result = null; }
            done(result && result.status === 'success', result ? result.message : 'Request failed.');
        };
        xhr.onerror = function () { done(false, 'Connection error.'); };
        xhr.send(encode(data));
    }

    var moveButtons = document.querySelectorAll('.vsig-move');
    for (var i = 0; i < moveButtons.length; i++) {
        moveButtons[i].addEventListener('click', function () {
            var button = this;
            button.disabled = true;
            if (status) { status.textContent = 'Moving...'; }

            post(moveUrl, {
                id: button.getAttribute('data-id'),
                direction: button.getAttribute('data-direction')
            }, function (ok, message) {
                if (ok) {
                    if (status) { status.textContent = 'Layout saved'; }
                    window.location.reload();
                    return;
                }
                button.disabled = false;
                if (status) { status.textContent = message || 'Could not move'; }
            });
        });
    }

    var labelInputs = document.querySelectorAll('.vsig-label-input');
    for (var j = 0; j < labelInputs.length; j++) {
        (function (input) {
            input.setAttribute('data-saved', input.value);

            function saveLabel() {
                if (input.value === input.getAttribute('data-saved')) { return; }
                if (input.getAttribute('data-saving') === '1') { return; }

                var sentValue = input.value;
                input.setAttribute('data-saving', '1');
                if (status) { status.textContent = 'Saving heading...'; }

                post(labelUrl, {
                    id: input.getAttribute('data-id'),
                    print_label: sentValue
                }, function (ok, message) {
                    input.removeAttribute('data-saving');
                    if (ok) {
                        input.setAttribute('data-saved', sentValue);
                        if (status) { status.textContent = 'Heading saved'; }
                        // If the value changed during the request, immediately
                        // persist the newer text as well.
                        if (input.value !== sentValue) { saveLabel(); }
                        return;
                    }
                    if (status) { status.textContent = message || 'Heading not saved'; }
                });
            }

            input.addEventListener('change', saveLabel);
            input.addEventListener('blur', saveLabel);
        })(labelInputs[j]);
    }
})();
</script>
<?php endif; ?>

<?php endif; ?>

<?php
// Keeps the signature block on the same sheet as the closing roster rows.
// Loaded outside the block above so the reports that still render the legacy
// .prep / table.sign footer get it too. Excel exports are plain HTML sheets.
if (empty($is_excel_export)) {
    $this->load->view('pages/_rqa_print_fit');
}
