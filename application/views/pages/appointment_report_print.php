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
<style type="text/css"><?= $preview['style']; ?></style>
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
                <?= h($templateName); ?>
            </div>
        </div>
        <div class="rp-actions">
            <?php if ($kind === 'spreadsheet' || $kind === 'docx') : ?>
            <button type="button" class="rp-btn rp-btn-edit" id="rpEdit" title="Edit the text before printing. Changes are not saved."><i class="mdi mdi-pencil-outline"></i><span>Edit</span></button>
            <?php endif; ?>
            <button type="button" class="rp-btn rp-btn-print" onclick="window.print();"><i class="mdi mdi-printer"></i>Print</button>
        </div>
    </div>

    <?php if ($kind === 'spreadsheet') : ?>
        <?php foreach ($pages as $index => $sheet) : ?>
            <div class="rp-paper">
                <span class="rp-page-no">Page <?= (int) $index + 1; ?> of <?= count($pages); ?></span>
                <div class="rp-fit-box">
                    <div class="rp-fit rp-sheet"<?= !empty($sheet['width']) ? ' style="width:' . (float) $sheet['width'] . 'pt;"' : ''; ?>><?= $sheet['html']; ?></div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php elseif ($kind === 'docx') : ?>
        <div class="rp-paper">
            <div class="rp-fit-box">
                <div class="rp-fit rp-docpage"<?= $docPageWidth > 0 ? ' style="width:' . $docPageWidth . 'pt;"' : ''; ?>><?= $pages[0]['html']; ?></div>
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

    // Word-like correction before printing. Edits live only on this page;
    // reloading brings back the generated form.
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
            if (!on) { fitPages(); }
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
