<?php
if (!function_exists('h')) {
    function h($value) { return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); }
}
$kind = $preview['kind'] ?? '';
$pages = $preview['pages'] ?? [];

// Official output is A4 portrait with a narrow 6 mm border, the least most
// printers can reach. A spreadsheet form keeps its exact layout and is scaled
// as a whole to fill the sheet edge to edge, like Excel's "fit to page".
$margin = '6mm';
$availableWidth = 746;   // (210mm - 2 x 6mm) at 96dpi, less a safety pixel or two
$availableHeight = 1074; // (297mm - 2 x 6mm) at 96dpi, less a safety pixel or two
if ($kind === 'docx') {
    // A Word page already carries its own margins (and a letterhead that runs
    // to the paper edge), so it is laid on the A4 sheet without a border.
    $margin = '0mm';
    $availableWidth = 793;
    $availableHeight = 1121;
}
$docPageWidth = (float) ($pages[0]['width'] ?? 0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= h($title); ?> — <?= h($applicantName); ?></title>
<!-- No CSS framework here on purpose: Bootstrap's body line-height inflates
     every row of an Excel form and breaks its layout. -->
<link href="<?= base_url(); ?>assets/css/icons.min.css" rel="stylesheet" type="text/css" />
<noscript><style>.rp-fit { visibility:visible !important; }</style></noscript>
<?php if (!empty($preview['style'])) : ?>
<style type="text/css" id="rpSheetStyle"><?= $preview['style']; ?></style>
<?php endif; ?>
<style>
    :root { --rp-primary:#1f3a5f; --rp-bg:#eef2f7; --rp-border:#e3eaf3; --rp-muted:#758396; }
    html, body { margin:0; padding:0; min-height:100%; background:var(--rp-bg); }
    .rp-bar { position:sticky; top:0; z-index:50; background:#fff; border-bottom:1px solid var(--rp-border); padding:10px 18px; display:flex; align-items:center; gap:14px; flex-wrap:wrap; box-shadow:0 4px 16px rgba(31,58,95,.07); }
    .rp-bar h6 { margin:0; font-weight:800; color:#26384d; font-size:.9rem; }
    .rp-meta { color:var(--rp-muted); font-size:.72rem; margin-top:3px; }
    .rp-pill { display:inline-flex; padding:.14rem .5rem; border-radius:999px; font-size:.63rem; font-weight:800; background:#eef5ff; color:#315a85; border:1px solid #dae8f8; margin-right:4px; }
    .rp-pill.rp-pill-nature { background:#f0f9f6; color:#14805f; border-color:#d3ece3; }
    .rp-pill.rp-pill-paper { background:#fff6e8; color:#926719; border-color:#f3dfb8; }
    .rp-actions { margin-left:auto; display:flex; gap:8px; }
    .rp-btn { display:inline-flex; align-items:center; gap:5px; border:1px solid var(--rp-border); background:#fff; color:#41556c; border-radius:8px; padding:.36rem .7rem; font-size:.76rem; font-weight:700; text-decoration:none; cursor:pointer; font-family:inherit; }
    .rp-btn:hover { border-color:#b9c9da; color:var(--rp-primary); }
    .rp-btn-print { background:var(--rp-primary); border-color:var(--rp-primary); color:#fff; }
    .rp-btn-print:hover { background:#274a77; color:#fff; }

    /* One A4 sheet per page of the form. */
    .rp-paper { position:relative; box-sizing:border-box; background:#fff; margin:22px auto; padding:<?= $margin; ?>; width:210mm; min-height:297mm; box-shadow:0 10px 30px rgba(31,58,95,.12); border-radius:3px; }
    .rp-paper:last-of-type { margin-bottom:40px; }
    .rp-page-no { position:absolute; top:-18px; right:2px; font-size:.62rem; font-weight:800; letter-spacing:.4px; color:var(--rp-muted); text-transform:uppercase; }
    /* The scaled sheet keeps its full-size layout box, so the box is clipped to
       the space the scaled form actually takes; without this the leftover box
       spills onto an extra printed page. */
    .rp-fit-box { margin:0 auto; overflow:hidden; }
    /* max-content keeps the form at its designed width: the A4 sheet must not
       squeeze the columns, the whole block is scaled to fit instead. */
    /* Text longer than its column spills over the next cells and is cut at the
       edge of the sheet, exactly as Excel prints it. */
    .rp-fit { transform-origin:top left; visibility:hidden; overflow:hidden; }
    .rp-fitted .rp-fit { visibility:visible; }
    /* Fixed layout keeps the office's column widths, and nowrap keeps text on
       the single line Excel puts it on, so nothing reflows. */
    .rp-sheet table { border-collapse:collapse; table-layout:fixed; width:100%; }
    .rp-sheet td, .rp-sheet th { white-space:nowrap; }
    /* E-signatures float over the name as in Excel, not shrunk into one cell. */
    .rp-sheet td > img { max-width:none !important; pointer-events:none; mix-blend-mode:multiply; }
    /* Edit mode: the form can be corrected in place before printing. */
    .rp-editing .rp-fit { outline:2px dashed #9fb8d6; outline-offset:4px; cursor:text; }
    .rp-editing [contenteditable]:focus { outline-color:var(--rp-primary); }
    .rp-btn-edit.active { background:#fff6e8; border-color:#f3dfb8; color:#926719; }
    .rp-btn[disabled] { opacity:.6; cursor:default; }
    .rp-pill.rp-pill-saved { background:#f0f9f6; color:#14805f; border-color:#d3ece3; }
    .rp-save-status { align-self:center; font-size:.72rem; font-weight:700; color:var(--rp-muted); }
    .rp-save-status.is-error { color:#c0392b; }
    /* Formatting toolbar, shown under the bar while editing. */
    .rp-tools { display:none; flex-basis:100%; align-items:center; gap:4px; flex-wrap:wrap; padding-top:8px; border-top:1px solid var(--rp-border); }
    .rp-editing .rp-tools { display:flex; }
    .rp-tool { display:inline-flex; align-items:center; justify-content:center; min-width:32px; height:30px; padding:0 6px; border:1px solid var(--rp-border); background:#fff; color:#41556c; border-radius:6px; font-size:1.05rem; cursor:pointer; font-family:inherit; }
    .rp-tool:hover { border-color:#b9c9da; color:var(--rp-primary); background:#f6f9fc; }
    .rp-tool.active { background:#e8f0fb; border-color:#b9cde6; color:var(--rp-primary); }
    .rp-tool-sep { width:1px; height:22px; background:var(--rp-border); margin:0 4px; }
    .rp-tools-hint { margin-left:auto; font-size:.72rem; color:var(--rp-muted); display:inline-flex; align-items:center; gap:4px; }
    .rp-tools-hint kbd { font-family:inherit; font-size:.66rem; font-weight:700; background:#f1f4f8; border:1px solid var(--rp-border); border-radius:4px; padding:0 4px; }
    @media(max-width:900px){ .rp-tools-hint { margin-left:0; flex-basis:100%; } }
    /* Word page: the template's own page box, scaled onto the A4 sheet. */
    /* Images that bleed past the paper (letterheads) are cut at its edge, as in Word. */
    .rp-docpage .dx-page { overflow:hidden; }
    .rp-docpage .dx-tab { display:inline-block; width:0; }
    .rp-docpage .dx-shape img { mix-blend-mode:multiply; }
    .rp-docpage table.dx-table td > .dx-p:last-child { margin-bottom:0; }
    .rp-fallback { max-width:600px; margin:80px auto; text-align:center; color:var(--rp-muted); }
    .rp-fallback i { font-size:52px; color:#c4d1df; display:block; margin-bottom:12px; }
    @media(max-width:900px){ .rp-paper{ width:auto; min-height:0; padding:14px; } .rp-actions{width:100%; margin-left:0} }
    @media print {
        @page { size:A4 portrait; margin:<?= $margin; ?>; }
        html, body { background:#fff; }
        /* Keep the form's cell fills (blue frame, grey headers) on paper;
           browsers drop background colours in print unless told not to. */
        .rp-paper, .rp-paper * { -webkit-print-color-adjust:exact !important; print-color-adjust:exact !important; color-adjust:exact !important; }
        .rp-bar, .rp-noprint, .rp-page-no { display:none !important; }
        .rp-paper { width:auto; min-height:0; margin:0; padding:0; box-shadow:none; border-radius:0; }
        .rp-paper + .rp-paper { page-break-before:always; }
        .rp-fit { outline:none !important; }
    }
</style>
</head>
<body>
    <div class="rp-bar">
        <a href="<?= h($backUrl); ?>" class="rp-btn" title="Back to Appointment Reports"><i class="mdi mdi-arrow-left"></i></a>
        <div>
            <h6><?= h($title); ?></h6>
            <div class="rp-meta">
                <span class="rp-pill"><?= h($applicantName); ?></span>
                <?php if ($positionTitle !== '') : ?><span class="rp-pill"><?= h($positionTitle); ?></span><?php endif; ?>
                <span class="rp-pill"><?= h($groupName); ?></span>
                <span class="rp-pill rp-pill-nature"><?= h($natureName); ?></span>
                <span class="rp-pill rp-pill-paper"><i class="mdi mdi-printer-outline"></i> A4 &middot; <?= count($pages); ?> page<?= count($pages) === 1 ? '' : 's'; ?></span>
                <?php if ($savedAt !== '') : ?><span class="rp-pill rp-pill-saved" id="rpSavedPill" title="Opened from the saved copy<?= $savedBy !== '' ? ' — saved by ' . h($savedBy) : ''; ?>"><i class="mdi mdi-content-save-outline"></i>&nbsp;Saved edits &middot; <?= h($savedAt); ?></span><?php endif; ?>
                <?= h($templateName); ?>
            </div>
        </div>
        <div class="rp-actions">
            <?php if ($kind === 'spreadsheet' || $kind === 'docx') : ?>
            <span class="rp-save-status" id="rpSaveStatus"></span>
            <?php if ($canSave && $savedAt !== '') : ?>
            <button type="button" class="rp-btn" id="rpReset" title="Discard the saved edits and regenerate the form from the template and current data."><i class="mdi mdi-restore"></i>Reset</button>
            <?php endif; ?>
            <button type="button" class="rp-btn rp-btn-edit" id="rpEdit" title="<?= $canSave ? 'Edit the text before printing. Changes are saved when you click Done.' : 'Edit the text before printing. Changes are not saved.'; ?>"><i class="mdi mdi-pencil-outline"></i><span>Edit</span></button>
            <?php endif; ?>
            <button type="button" class="rp-btn rp-btn-print" onclick="window.print();"><i class="mdi mdi-printer"></i>Print</button>
        </div>
        <?php if ($kind === 'spreadsheet' || $kind === 'docx') : ?>
        <div class="rp-tools" id="rpTools" role="toolbar" aria-label="Text formatting">
            <button type="button" class="rp-tool" data-cmd="bold" title="Bold (Ctrl+B)"><i class="mdi mdi-format-bold"></i></button>
            <button type="button" class="rp-tool" data-cmd="italic" title="Italic (Ctrl+I)"><i class="mdi mdi-format-italic"></i></button>
            <button type="button" class="rp-tool" data-cmd="underline" title="Underline (Ctrl+U)"><i class="mdi mdi-format-underline"></i></button>
            <button type="button" class="rp-tool" data-cmd="strikeThrough" title="Strikethrough"><i class="mdi mdi-format-strikethrough-variant"></i></button>
            <span class="rp-tool-sep"></span>
            <button type="button" class="rp-tool" data-size="0.9" title="Smaller text"><i class="mdi mdi-format-font-size-decrease"></i></button>
            <button type="button" class="rp-tool" data-size="1.1" title="Larger text"><i class="mdi mdi-format-font-size-increase"></i></button>
            <button type="button" class="rp-tool" data-cmd="uppercase" title="UPPERCASE"><i class="mdi mdi-format-letter-case-upper"></i></button>
            <span class="rp-tool-sep"></span>
            <button type="button" class="rp-tool" data-cmd="justifyLeft" title="Align left"><i class="mdi mdi-format-align-left"></i></button>
            <button type="button" class="rp-tool" data-cmd="justifyCenter" title="Align center"><i class="mdi mdi-format-align-center"></i></button>
            <button type="button" class="rp-tool" data-cmd="justifyRight" title="Align right"><i class="mdi mdi-format-align-right"></i></button>
            <span class="rp-tool-sep"></span>
            <button type="button" class="rp-tool" data-cmd="removeFormat" title="Clear formatting"><i class="mdi mdi-format-clear"></i></button>
            <button type="button" class="rp-tool" data-cmd="undo" title="Undo (Ctrl+Z)"><i class="mdi mdi-undo"></i></button>
            <button type="button" class="rp-tool" data-cmd="redo" title="Redo (Ctrl+Y)"><i class="mdi mdi-redo"></i></button>
            <span class="rp-tools-hint"><i class="mdi mdi-information-outline"></i>Select text, then pick a format. <kbd>Ctrl</kbd>+<kbd>B</kbd> / <kbd>I</kbd> / <kbd>U</kbd> also work. Click <strong>&nbsp;Done&nbsp;</strong> to <?= $canSave ? 'save' : 'finish'; ?>.</span>
        </div>
        <?php endif; ?>
    </div>

    <?php if ($kind === 'spreadsheet') : ?>
        <?php foreach ($pages as $index => $sheet) : ?>
            <div class="rp-paper">
                <span class="rp-page-no">Page <?= (int) $index + 1; ?> of <?= count($pages); ?></span>
                <div class="rp-fit-box">
                    <div class="rp-fit rp-sheet"<?= !empty($sheet['width']) ? ' style="width:' . (float) $sheet['width'] . 'pt;" data-width="' . (float) $sheet['width'] . '"' : ''; ?>><?= $sheet['html']; ?></div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php elseif ($kind === 'docx') : ?>
        <div class="rp-paper">
            <div class="rp-fit-box">
                <div class="rp-fit rp-docpage"<?= $docPageWidth > 0 ? ' style="width:' . $docPageWidth . 'pt;" data-width="' . $docPageWidth . '"' : ''; ?>><?= $pages[0]['html']; ?></div>
            </div>
        </div>
    <?php else : ?>
        <div class="rp-fallback rp-noprint">
            <i class="mdi mdi-file-hidden"></i>
            <h5>No preview available</h5>
            <p><?= $error !== '' ? h($error) : 'This document could not be rendered in the browser.'; ?></p>
            <a class="rp-btn" href="<?= h($backUrl); ?>"><i class="mdi mdi-arrow-left"></i>Back to Appointment Reports</a>
        </div>
    <?php endif; ?>

<script>
(function () {
    // Scale each form page as one block so the official layout is never
    // reflowed: it keeps its proportions and simply fits the A4 sheet.
    var AVAILABLE_WIDTH = <?= (int) $availableWidth; ?>;
    var AVAILABLE_HEIGHT = <?= (int) $availableHeight; ?>;
    // Breathing room between text and the form's borders, in unscaled px.
    var CELL_INSET = 8;

    // Excel lets a cell's text run over the empty cells beside it, but it stops
    // at the next value or at a ruled line. The browser has no such limit and
    // its font metrics are a little wider, so text was crossing the form's
    // borders. Each cell is measured against the room the form actually gives
    // it and eased down only when it needs more.
    function cellHasInk(cell) {
        return !!cell.textContent.replace(/\s|\u00a0/g, '');
    }

    // Where a cell's text has to stop on one side: the next cell that carries a
    // value, or the next ruled line of the form.
    function stopEdge(cell, side) {
        var rect = cell.getBoundingClientRect();
        var edge = side === 'right' ? rect.right : rect.left;
        var row = cell.parentNode;
        if (!row || !row.cells) { return edge; }
        var cells = row.cells, step = side === 'right' ? 1 : -1;
        // Only a ruled line (or the sheet edge) gets the inset; text stopping
        // at a neighbouring value keeps the full room Excel gives it.
        var ruled = true;
        if (!sideBordered(cell, side)) {
            for (var i = cell.cellIndex + step; i >= 0 && i < cells.length; i += step) {
                var next = cells[i], style = window.getComputedStyle(next);
                if (cellHasInk(next)) { ruled = false; break; }
                var near = side === 'right' ? style.borderLeftStyle : style.borderRightStyle;
                if (near && near !== 'none') { break; }
                var nextRect = next.getBoundingClientRect();
                edge = side === 'right' ? nextRect.right : nextRect.left;
                var far = side === 'right' ? style.borderRightStyle : style.borderLeftStyle;
                if (far && far !== 'none') { break; }
            }
        }
        if (!ruled) { return edge; }
        return side === 'right' ? edge - CELL_INSET : edge + CELL_INSET;
    }

    // Whether a vertical ruled line runs along this side of the cell: its own
    // border or the facing border of the cell beside it.
    function sideBordered(cell, side) {
        var own = window.getComputedStyle(cell)[side === 'right' ? 'borderRightStyle' : 'borderLeftStyle'];
        if (own && own !== 'none') { return true; }
        var row = cell.parentNode, beside = row && row.cells ? row.cells[cell.cellIndex + (side === 'right' ? 1 : -1)] : null;
        if (!beside) { return false; }
        var facing = window.getComputedStyle(beside)[side === 'right' ? 'borderLeftStyle' : 'borderRightStyle'];
        return !!facing && facing !== 'none';
    }

    // The converter puts each run of text in its own sized span, so the cell and
    // everything inside it is resized together. The original size is remembered
    // once, which keeps repeated runs (resize, print) stable.
    function scaleCellText(cell, ratio) {
        var nodes = [cell], children = cell.getElementsByTagName('*');
        for (var c = 0; c < children.length; c++) { nodes.push(children[c]); }
        for (var n = 0; n < nodes.length; n++) {
            var node = nodes[n];
            if (!node.getAttribute('data-rp-size')) {
                node.setAttribute('data-rp-size', parseFloat(window.getComputedStyle(node).fontSize) || 0);
            }
            var base = parseFloat(node.getAttribute('data-rp-size'));
            if (base) { node.style.fontSize = (base * ratio) + 'px'; }
        }
    }

    function fitCells(sheet) {
        var cells = sheet.querySelectorAll('td'), range = document.createRange();

        function measure(cell) {
            range.selectNodeContents(cell);
            var text = range.getBoundingClientRect();
            var left = stopEdge(cell, 'left'), right = stopEdge(cell, 'right');
            return {
                width: text.width,
                room: right - left,
                over: Math.max(text.right - right, left - text.left, 0)
            };
        }

        for (var i = 0; i < cells.length; i++) {
            var cell = cells[i];
            if (!cellHasInk(cell)) { continue; }
            scaleCellText(cell, 1);
            var fit = measure(cell);
            if (!fit.width || fit.over <= 1 || fit.room <= 0) { continue; }
            // Only ever ease text down, never up, and never below 70%
            // of the size the office set.
            var ratio = Math.min(1, Math.max((fit.width - fit.over) / fit.width, 0.7));
            for (var pass = 0; pass < 4 && ratio < 1; pass++) {
                scaleCellText(cell, ratio);
                if (measure(cell).over <= 1) { break; }
                ratio = Math.max(ratio - 0.02, 0.7);
            }
        }
    }

    // Rows are heightened evenly (never the text) so a form that is wider
    // than it is tall still reaches the bottom of the sheet.
    function setRowStretch(sheet, factor) {
        var rows = sheet.getElementsByTagName('tr');
        for (var r = 0; r < rows.length; r++) {
            var row = rows[r];
            if (!row.getAttribute('data-rp-height')) {
                row.setAttribute('data-rp-height', row.getBoundingClientRect().height || 0);
            }
            var base = parseFloat(row.getAttribute('data-rp-height'));
            row.style.height = factor === 1 || !base ? '' : (base * factor) + 'px';
        }
    }

    // Scaling to a fractional size leaves hairline gaps between neighbouring
    // filled cells (the blue frame, grey headers). Each filled cell bleeds its
    // own colour a fraction of a pixel so the fill reads solid on screen and paper.
    function sealFills() {
        var cells = document.querySelectorAll('.rp-sheet td');
        for (var i = 0; i < cells.length; i++) {
            // Inset text away from a ruled line beside it, on top of any indent
            // the office set. Other sides and empty spacer columns are left
            // alone so labels keep their room and column widths never change.
            if (cellHasInk(cells[i]) && !cells[i].getAttribute('data-rp-inset')) {
                var pad = window.getComputedStyle(cells[i]);
                if (sideBordered(cells[i], 'left')) {
                    cells[i].style.paddingLeft = (parseFloat(pad.paddingLeft) || 0) + CELL_INSET + 'px';
                }
                if (sideBordered(cells[i], 'right')) {
                    cells[i].style.paddingRight = (parseFloat(pad.paddingRight) || 0) + CELL_INSET + 'px';
                }
                cells[i].setAttribute('data-rp-inset', '1');
            }
            var bg = window.getComputedStyle(cells[i]).backgroundColor;
            if (bg && bg !== 'transparent' && !/^rgba\(.*,\s*0\)$/.test(bg) && bg !== 'rgb(255, 255, 255)') {
                cells[i].style.boxShadow = '0 0 0 0.75px ' + bg;
            }
        }
    }

    // Word tab stops: each tab runs to the paragraph's next stop (its own
    // stops first, then the document's default interval), measured from the
    // column edge. Left, center and right stops are honoured.
    var PX_PER_PT = 96 / 72;
    function layoutTabs(root) {
        var page = root.querySelector('.dx-page');
        var defaultTab = page ? (parseFloat(page.getAttribute('data-tab')) || 36) : 36;
        var paragraphs = root.querySelectorAll('.dx-p');
        for (var i = 0; i < paragraphs.length; i++) {
            var p = paragraphs[i], tabs = [], all = p.querySelectorAll('.dx-tab');
            for (var t = 0; t < all.length; t++) {
                if (all[t].closest('.dx-p') === p) { tabs.push(all[t]); }
            }
            if (!tabs.length) { continue; }
            for (t = 0; t < tabs.length; t++) { tabs[t].style.width = '0px'; }
            var stops = [];
            (p.getAttribute('data-tabs') || '').split(',').forEach(function (entry) {
                var bits = entry.split(':');
                if (bits.length === 2) { stops.push({ pos: parseFloat(bits[0]), type: bits[1] }); }
            });
            var column = p.getBoundingClientRect().left - (parseFloat(p.getAttribute('data-left')) || 0) * PX_PER_PT;
            for (t = 0; t < tabs.length; t++) {
                var tab = tabs[t];
                var x = (tab.getBoundingClientRect().left - column) / PX_PER_PT;
                var stop = null;
                for (var s = 0; s < stops.length; s++) {
                    if (stops[s].pos > x + 0.5) { stop = stops[s]; break; }
                }
                if (!stop) { stop = { pos: (Math.floor(x / defaultTab) + 1) * defaultTab, type: 'left' }; }
                var width = stop.pos - x;
                if (stop.type === 'center' || stop.type === 'right' || stop.type === 'decimal') {
                    var range = document.createRange();
                    range.setStartAfter(tab);
                    if (tabs[t + 1]) { range.setEndBefore(tabs[t + 1]); } else { range.setEnd(p, p.childNodes.length); }
                    var segment = range.getBoundingClientRect().width / PX_PER_PT;
                    width -= stop.type === 'center' ? segment / 2 : segment;
                }
                tab.style.width = Math.max(0, width) * PX_PER_PT + 'px';
            }
        }
    }

    function fitPages() {
        var boxes = document.querySelectorAll('.rp-fit');
        for (var i = 0; i < boxes.length; i++) {
            var sheet = boxes[i], holder = sheet.parentNode;
            sheet.style.transform = 'none';
            holder.style.width = '';
            holder.style.height = '';
            var isDoc = sheet.classList.contains('rp-docpage');
            if (isDoc) {
                layoutTabs(sheet);
            } else {
                setRowStretch(sheet, 1);
                fitCells(sheet);
            }
            // The width is the sheet's own box (spilled text is clipped, as in
            // Excel); only the height has to be measured from the content.
            var width = sheet.getBoundingClientRect().width || sheet.offsetWidth;
            // A Word page is measured by its own box: shapes bleeding past the
            // paper edge are clipped there and must not shrink the page.
            var height = isDoc && sheet.firstElementChild ? sheet.firstElementChild.offsetHeight : sheet.scrollHeight;
            if (!width || !height) { continue; }
            // Fill the sheet edge to edge: scale up as well as down.
            var scale = Math.min(AVAILABLE_WIDTH / width, AVAILABLE_HEIGHT / height);
            // A Word page keeps its own proportions; only Excel rows stretch.
            var stretch = isDoc ? 1 : Math.min(AVAILABLE_HEIGHT / (height * scale), 1.6);
            if (stretch > 1.01) {
                setRowStretch(sheet, stretch);
                height = sheet.scrollHeight;
                scale = Math.min(AVAILABLE_WIDTH / width, AVAILABLE_HEIGHT / height);
            }
            sheet.style.transform = 'scale(' + scale + ')';
            holder.style.width = Math.floor(width * scale) + 'px';
            holder.style.height = Math.floor(height * scale) + 'px';
        }
        document.body.className = 'rp-fitted';
    }

    // Word-like correction before printing. For HR/SDS accounts the edited
    // form is saved on Done, so reopening or reprinting it needs no re-edit.
    var CAN_SAVE = <?= $canSave ? 'true' : 'false'; ?>;
    var SAVE_URL = <?= json_encode($saveUrl); ?>;
    var SAVE_KEY = {
        rec_id: <?= json_encode($recKey); ?>,
        document_type: <?= json_encode($documentType); ?>,
        nature: <?= json_encode($natureName); ?>
    };
    var KIND = <?= json_encode($kind); ?>;
    var saveStatus = document.getElementById('rpSaveStatus');

    function setStatus(text, isError) {
        if (!saveStatus) { return; }
        saveStatus.textContent = text;
        saveStatus.className = 'rp-save-status' + (isError ? ' is-error' : '');
    }

    // UTF-8 safe base64 of the JSON body.
    function toBase64(text) {
        var bytes = new TextEncoder().encode(text), binary = '';
        for (var i = 0; i < bytes.length; i += 0x8000) {
            binary += String.fromCharCode.apply(null, bytes.subarray(i, i + 0x8000));
        }
        return btoa(binary);
    }

    function post(fields, done) {
        var body = [];
        for (var k in fields) {
            if (Object.prototype.hasOwnProperty.call(fields, k)) {
                body.push(encodeURIComponent(k) + '=' + encodeURIComponent(fields[k]));
            }
        }
        var xhr = new XMLHttpRequest();
        xhr.open('POST', SAVE_URL, true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.onload = function () {
            var res = null;
            try { res = JSON.parse(xhr.responseText); } catch (e) {}
            done(res && res.status === 'success', res ? res : { message: 'The server returned an unexpected response (HTTP ' + xhr.status + ').' });
        };
        xhr.onerror = function () { done(false, { message: 'Network error. The edits were not saved.' }); };
        xhr.send(body.join('&'));
    }

    function saveEdits() {
        var areas = document.querySelectorAll('.rp-fit'), pages = [];
        for (var a = 0; a < areas.length; a++) {
            pages.push({ html: areas[a].innerHTML, width: parseFloat(areas[a].getAttribute('data-width')) || null });
        }
        var styleEl = document.getElementById('rpSheetStyle');
        var payload = { kind: KIND, style: styleEl ? styleEl.textContent : '', pages: pages };
        setStatus('Saving…');
        var fields = { payload: toBase64(JSON.stringify(payload)) };
        for (var k in SAVE_KEY) { fields[k] = SAVE_KEY[k]; }
        post(fields, function (ok, res) {
            setStatus(ok ? 'Saved ' + (res.savedAt || '') : (res.message || 'Save failed.'), !ok);
        });
    }

    var resetBtn = document.getElementById('rpReset');
    if (resetBtn) {
        resetBtn.addEventListener('click', function () {
            if (!window.confirm('Discard the saved edits and regenerate this form from the template and current data?')) { return; }
            resetBtn.disabled = true;
            var fields = { action: 'reset' };
            for (var k in SAVE_KEY) { fields[k] = SAVE_KEY[k]; }
            post(fields, function (ok, res) {
                if (ok) { window.location.reload(); return; }
                resetBtn.disabled = false;
                setStatus(res.message || 'Reset failed.', true);
            });
        });
    }

    // Formatting toolbar. Buttons keep the focus (and selection) in the form.
    var tools = document.getElementById('rpTools');
    var STATE_CMDS = ['bold', 'italic', 'underline', 'strikeThrough'];

    function selectionInForm() {
        var sel = window.getSelection();
        if (!sel || !sel.rangeCount) { return null; }
        var node = sel.getRangeAt(0).commonAncestorContainer;
        node = node.nodeType === 1 ? node : node.parentNode;
        return node && node.closest && node.closest('.rp-fit[contenteditable]') ? sel : null;
    }

    // Scale the selected text from its current size.
    function resizeSelection(factor) {
        var sel = selectionInForm();
        if (!sel || sel.isCollapsed) { return; }
        var range = sel.getRangeAt(0), start = range.startContainer;
        var base = parseFloat(window.getComputedStyle(start.nodeType === 1 ? start : start.parentNode).fontSize) || 14;
        var span = document.createElement('span');
        span.style.fontSize = (Math.round(base * factor * 10) / 10) + 'px';
        span.appendChild(range.extractContents());
        // Nested sizes inside the selection follow the new size.
        var inner = span.querySelectorAll('[style*="font-size"]');
        for (var i = 0; i < inner.length; i++) { inner[i].style.fontSize = ''; inner[i].removeAttribute('data-rp-size'); }
        range.insertNode(span);
        sel.removeAllRanges();
        var next = document.createRange();
        next.selectNodeContents(span);
        sel.addRange(next);
    }

    function uppercaseSelection() {
        var sel = selectionInForm();
        if (!sel || sel.isCollapsed) { return; }
        document.execCommand('insertText', false, sel.toString().toUpperCase());
    }

    function refreshToolState() {
        if (!tools) { return; }
        var inForm = !!selectionInForm();
        for (var i = 0; i < STATE_CMDS.length; i++) {
            var btn = tools.querySelector('[data-cmd="' + STATE_CMDS[i] + '"]');
            var on = false;
            try { on = inForm && document.queryCommandState(STATE_CMDS[i]); } catch (e) {}
            if (btn) { btn.classList.toggle('active', on); }
        }
    }

    if (tools) {
        tools.addEventListener('mousedown', function (e) {
            if (e.target.closest('.rp-tool')) { e.preventDefault(); }
        });
        tools.addEventListener('click', function (e) {
            var btn = e.target.closest('.rp-tool');
            if (!btn) { return; }
            var cmd = btn.getAttribute('data-cmd');
            if (cmd !== 'undo' && cmd !== 'redo' && !selectionInForm()) {
                setStatus('Click inside the form and select text first.', true);
                return;
            }
            setStatus('');
            if (btn.getAttribute('data-size')) {
                resizeSelection(parseFloat(btn.getAttribute('data-size')));
            } else if (cmd === 'uppercase') {
                uppercaseSelection();
            } else {
                try { document.execCommand('styleWithCSS', false, true); } catch (err) {}
                document.execCommand(cmd, false, null);
            }
            refreshToolState();
        });
        document.addEventListener('selectionchange', refreshToolState);
    }

    var editBtn = document.getElementById('rpEdit');
    if (editBtn) {
        editBtn.addEventListener('click', function () {
            var on = !editBtn.classList.contains('active');
            var areas = document.querySelectorAll('.rp-fit');
            for (var a = 0; a < areas.length; a++) {
                if (on) { areas[a].setAttribute('contenteditable', 'true'); areas[a].setAttribute('spellcheck', 'false'); }
                else { areas[a].removeAttribute('contenteditable'); }
            }
            editBtn.classList.toggle('active', on);
            document.documentElement.classList.toggle('rp-editing', on);
            editBtn.querySelector('span').textContent = on ? 'Done' : 'Edit';
            if (!on) {
                fitPages();
                if (CAN_SAVE) { saveEdits(); }
            }
        });
    }

    sealFills();
    if (document.readyState === 'complete') { fitPages(); }
    window.addEventListener('load', fitPages);
    window.addEventListener('resize', fitPages);
    window.addEventListener('beforeprint', fitPages);
})();
</script>
</body>
</html>
