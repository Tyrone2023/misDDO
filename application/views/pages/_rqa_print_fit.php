<?php
/*
 * Print fitter for the CAR / RQA reports.
 *
 * The roster table paginates on its own. When it happens to end near the bottom
 * of a sheet, the signature block (page-break-inside: avoid) no longer fits in
 * the leftover space and the browser pushes it onto a fresh sheet, which then
 * prints with signatures only and no applicant rows on it.
 *
 * On beforeprint the signature block is therefore moved into a last, borderless
 * row of the roster table, inside a tbody that also carries the final rows, so
 * the browser can only move the signatures together with those rows - the last
 * sheet always shows applicant data. The DOM is restored on afterprint.
 *
 * The measured squeeze (STEPS below) stays for the reports that close with the
 * .down notes instead of a signature block; it tightens the rows one step at a
 * time when the closing text would not fit on the last sheet.
 *
 * Loaded from pages/_rqa_signatories.php - every CAR / RQA report includes it.
 */
?>
<style id="rqaFitBase">
@media print {
    .data tr { page-break-inside: avoid; break-inside: avoid; }
    .prep { page-break-after: avoid; break-after: avoid; }
    .sign, .sign tr { page-break-inside: avoid; break-inside: avoid; }

    /* The closing rows and the signatures are one unbreakable unit while the
       sheet is being printed - see group() below. */
    tbody.rqa-keep { page-break-inside: avoid; break-inside: avoid; }
    table.data.rqa-keeping, table.data td.rqa-keep-cell { border: 0 !important; }
    table.data td.rqa-keep-cell {
        padding: 0 !important;
        text-align: left !important;
        vertical-align: top !important;
    }
}
</style>
<script>
(function () {
    // Coarse first, then one refining step - a full relayout of a long roster
    // is not cheap, so keep the number of passes small.
    var STEPS = [1, 0.95, 0.90, 0.86, 0.82, 0.78];

    // renren_style.css prints @page { size: A4 landscape; margin: 6mm 6mm 6mm 0mm }
    var PAGE_CONTENT_MM = 210 - 6 - 6;
    var MM_TO_PX = 96 / 25.4;

    var styleEl = null;

    function sheet() {
        if (!styleEl) {
            styleEl = document.createElement('style');
            styleEl.id = 'rqaFitStep';
            document.head.appendChild(styleEl);
        }
        return styleEl;
    }

    function px(n) { return (Math.round(n * 10) / 10) + 'px'; }

    // Base values below come from renren_style.css (* { font-size:12px }, print
    // th/td padding) and from the .vsig print block in _rqa_signatories.php.
    function apply(scale, keepRows) {
        var css = '';

        if (scale < 1) {
            css += '.data th, .data td:not(.rqa-keep-cell) { padding:' + px(Math.max(1, 3 * scale)) + ' '
                 + px(Math.max(2, 5 * scale)) + ' !important; line-height:1.15 !important; }'
                 + '.data th, .data td:not(.rqa-keep-cell), .data th *, .data td:not(.rqa-keep-cell) * '
                 + '{ font-size:' + px(12 * scale) + ' !important; }'
                 + '.vsig-wrap { margin-top:' + px(20 * scale) + ' !important; }'
                 + 'table.vsig td { padding:' + px(8 * scale) + ' 5px ' + px(18 * scale) + ' 5px !important; }'
                 + '.vsig-img, .vsig-space { height:' + px(42 * scale) + ' !important; }'
                 + '.prep { margin-top:' + px(30 * scale) + ' !important; margin-bottom:' + px(50 * scale) + ' !important; }'
                 + '.dd { margin-bottom:' + px(50 * scale) + ' !important; }';
        }

        if (keepRows) {
            // Last resort: drag the closing rows onto the signature sheet so it
            // is never a sheet without applicant data.
            css += '.data tr:nth-last-child(-n+3) { page-break-after: avoid; break-after: avoid; }';
        }

        sheet().textContent = css ? '@media print {' + css + '}' : '';
    }

    function pageHeight() {
        var fallback = PAGE_CONTENT_MM * MM_TO_PX;
        var probe = document.createElement('div');

        // Outside <body> on purpose - body carries zoom:90%, the page box does not.
        probe.style.cssText = 'position:absolute;top:0;left:0;width:1px;height:100vh;'
            + 'margin:0;padding:0;border:0;visibility:hidden;';
        document.documentElement.appendChild(probe);
        var h = probe.getBoundingClientRect().height;
        document.documentElement.removeChild(probe);

        if (!isFinite(h) || h < fallback * 0.5 || h > fallback * 1.8) { h = fallback; }
        return h;
    }

    function footerBottom(offset) {
        var nodes = document.querySelectorAll('.vsig-wrap, table.sign, .prep, .down');
        var bottom = -Infinity;
        for (var i = 0; i < nodes.length; i++) {
            var r = nodes[i].getBoundingClientRect();
            if (r.height <= 0 && r.width <= 0) { continue; }
            if (r.bottom + offset > bottom) { bottom = r.bottom + offset; }
        }
        return bottom > -Infinity ? bottom : null;
    }

    /*
     * getBoundingClientRect gives continuous-flow coordinates, but the printer
     * moves any row that would straddle a page boundary down to the next sheet.
     * Over a long roster that slack adds up to more than a sheet, so replay the
     * pagination row by row instead of dividing the flow height by the page
     * height. Returns true when the closing block ends on the same sheet as the
     * last roster row - and true as well whenever the layout cannot be measured,
     * so an unexpected page never gets shrunk for nothing.
     */
    function fits() {
        var rows = document.querySelectorAll('table.data tr');
        if (!rows.length) { return true; }

        var page = pageHeight();
        if (!(page > 0)) { return true; }

        var offset = window.pageYOffset || 0;
        var i, tops = [], bottoms = [];
        for (i = 0; i < rows.length; i++) {
            var r = rows[i].getBoundingClientRect();
            tops.push(r.top + offset);
            bottoms.push(r.bottom + offset);
        }

        var foot = footerBottom(offset);
        if (foot === null) { return true; }

        var pageIndex = 0;
        var shift = 0;
        for (i = 0; i < rows.length; i++) {
            var top = tops[i] + shift;
            var bottom = bottoms[i] + shift;

            if (bottom > (pageIndex + 1) * page) {
                pageIndex++;
                shift += (pageIndex * page) - top;
                bottom = bottoms[i] + shift;

                // A row taller than one sheet is split by the printer anyway.
                while (bottom > (pageIndex + 1) * page) { pageIndex++; }
            }
        }

        return (foot + shift) <= (pageIndex + 1) * page;
    }

    function fit() {
        for (var i = 0; i < STEPS.length; i++) {
            apply(STEPS[i], false);
            if (!fits()) { continue; }

            // Do not shrink more than needed - try halfway back to the last
            // step that did not fit.
            if (i > 0) {
                var refined = (STEPS[i - 1] + STEPS[i]) / 2;
                apply(refined, false);
                if (!fits()) { apply(STEPS[i], false); }
            }
            return;
        }

        apply(STEPS[STEPS.length - 1], true);
    }

    /*
     * Chrome honours break-inside: avoid but ignores break-after: avoid on
     * table rows, so CSS alone cannot tie the closing rows to the block that
     * follows the table. For the print run the signature block is therefore
     * moved into a last, borderless row of the roster table, inside a tbody
     * that also holds the final rows - one unbreakable unit, so the signatures
     * always print on a sheet that carries applicant rows. Everything is put
     * back on afterprint.
     */
    var KEEP_ROWS = 5;
    var grouped = null;

    function footerNodes(table) {
        var nodes = document.querySelectorAll('.vsig-wrap, .prep, table.sign');
        var found = [];
        for (var i = 0; i < nodes.length; i++) {
            // Only what closes the report - never a block above the table.
            if (table.compareDocumentPosition(nodes[i]) & 4) { found.push(nodes[i]); }
        }
        return found;
    }

    function columnCount(body) {
        var cells = body.rows.length ? body.rows[0].cells : [];
        var total = 0;
        for (var i = 0; i < cells.length; i++) { total += cells[i].colSpan || 1; }
        return total || 1;
    }

    function group() {
        if (grouped) { return; }

        var tables = document.querySelectorAll('table.data');
        var table = tables.length ? tables[tables.length - 1] : null;
        if (!table || !table.tBodies.length) { return; }

        var body = table.tBodies[table.tBodies.length - 1];
        var footer = footerNodes(table);

        // Nothing to rescue on a report with no signatures, and a roster that
        // is only a couple of rows long is left exactly as it is.
        if (!footer.length || body.rows.length <= KEEP_ROWS + 2) { return; }

        var keep = [];
        for (var i = body.rows.length - KEEP_ROWS; i < body.rows.length; i++) {
            keep.push(body.rows[i]);
        }

        var host = document.createElement('tbody');
        host.className = 'rqa-keep';

        var cell = document.createElement('td');
        cell.className = 'rqa-keep-cell';
        cell.colSpan = columnCount(body);

        var row = document.createElement('tr');
        row.appendChild(cell);

        grouped = { table: table, body: body, host: host, rows: keep, footer: [] };

        for (i = 0; i < keep.length; i++) { host.appendChild(keep[i]); }
        for (i = 0; i < footer.length; i++) {
            grouped.footer.push({
                node: footer[i],
                parent: footer[i].parentNode,
                next: footer[i].nextSibling
            });
            cell.appendChild(footer[i]);
        }

        host.appendChild(row);
        table.appendChild(host);
        table.className += ' rqa-keeping';
    }

    function ungroup() {
        if (!grouped) { return; }

        for (var i = 0; i < grouped.rows.length; i++) {
            grouped.body.appendChild(grouped.rows[i]);
        }
        for (i = 0; i < grouped.footer.length; i++) {
            var item = grouped.footer[i];
            item.parent.insertBefore(item.node, item.next);
        }
        if (grouped.host.parentNode) {
            grouped.host.parentNode.removeChild(grouped.host);
        }
        grouped.table.className = grouped.table.className.replace(/ ?rqa-keeping/, '');
        grouped = null;
    }

    window.addEventListener('beforeprint', function () {
        group();
        fit();
    });
    window.addEventListener('afterprint', function () {
        apply(1, false);
        ungroup();
    });
})();
</script>
