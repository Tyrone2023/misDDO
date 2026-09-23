<?php
if (!function_exists('h')) {
    function h($value) { return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); }
}
$kind = $preview['kind'] ?? '';
$pages = $preview['pages'] ?? [];

// Official output is A4 portrait with a formal 12.7 mm (0.5") border on all
// sides. A spreadsheet form keeps its exact layout and is scaled down as a
// whole to fit the sheet, the way Excel's "fit to one page" prints it.
$margin = '12.7mm';
$availableWidth = 692;   // (210mm - 2 x 12.7mm) at 96dpi, less a safety pixel or two
$availableHeight = 1020; // (297mm - 2 x 12.7mm) at 96dpi, less a safety pixel or two
$docWidth = $preview['page']['content_width'] ?? '6.5in';
$docTop = $preview['page']['margin_top'] ?? '1in';
$docBottom = $preview['page']['margin_bottom'] ?? '1in';
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
    /* Word single spacing, and no extra gap between paragraphs: the converter
       already emits the document's own blank lines. */
    .rp-doc { width:<?= h($docWidth); ?>; max-width:100%; margin:0 auto; padding:calc(<?= h($docTop); ?> - <?= $margin; ?>) 0 calc(<?= h($docBottom); ?> - <?= $margin; ?>); font-family:'Times New Roman',serif; color:#000; line-height:1.15; }
    .rp-doc p { margin:0; }
    .dp-tab { display:inline-block; width:2.4em; }
    table.dp-tbl { width:100%; border-collapse:collapse; margin:.5rem 0; }
    table.dp-tbl td { border:1px solid #000; padding:4px 8px; vertical-align:top; }
    .rp-fallback { max-width:600px; margin:80px auto; text-align:center; color:var(--rp-muted); }
    .rp-fallback i { font-size:52px; color:#c4d1df; display:block; margin-bottom:12px; }
    @media(max-width:900px){ .rp-paper{ width:auto; min-height:0; padding:14px; } .rp-actions{width:100%; margin-left:0} }
    @media print {
        @page { size:A4 portrait; margin:<?= $margin; ?>; }
        html, body { background:#fff; }
        .rp-bar, .rp-noprint, .rp-page-no { display:none !important; }
        .rp-paper { width:auto; min-height:0; margin:0; padding:0; box-shadow:none; border-radius:0; }
        .rp-paper + .rp-paper { page-break-before:always; }
        .rp-doc { padding:0; }
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
        <div class="rp-paper"><div class="rp-doc"><?= $pages[0]['html']; ?></div></div>
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
        for (var i = cell.cellIndex + step; i >= 0 && i < cells.length; i += step) {
            var next = cells[i], style = window.getComputedStyle(next);
            if (cellHasInk(next)) { break; }
            var near = side === 'right' ? style.borderLeftStyle : style.borderRightStyle;
            if (near && near !== 'none') { break; }
            var nextRect = next.getBoundingClientRect();
            edge = side === 'right' ? nextRect.right : nextRect.left;
            var far = side === 'right' ? style.borderRightStyle : style.borderLeftStyle;
            if (far && far !== 'none') { break; }
        }
        return edge;
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
            // Only ever ease text down, never up, and never below three quarters
            // of the size the office set.
            var ratio = Math.min(1, Math.max((fit.width - fit.over) / fit.width, 0.75));
            for (var pass = 0; pass < 4 && ratio < 1; pass++) {
                scaleCellText(cell, ratio);
                if (measure(cell).over <= 1) { break; }
                ratio = Math.max(ratio - 0.02, 0.75);
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
            fitCells(sheet);
            // The width is the sheet's own box (spilled text is clipped, as in
            // Excel); only the height has to be measured from the content.
            var width = sheet.getBoundingClientRect().width || sheet.offsetWidth;
            var height = sheet.scrollHeight;
            if (!width || !height) { continue; }
            var scale = Math.min(AVAILABLE_WIDTH / width, AVAILABLE_HEIGHT / height, 1);
            sheet.style.transform = 'scale(' + scale + ')';
            holder.style.width = Math.floor(width * scale) + 'px';
            holder.style.height = Math.floor(height * scale) + 'px';
        }
        document.body.className = 'rp-fitted';
    }

    if (document.readyState === 'complete') { fitPages(); }
    window.addEventListener('load', fitPages);
    window.addEventListener('resize', fitPages);
    window.addEventListener('beforeprint', fitPages);
})();
</script>
</body>
</html>
