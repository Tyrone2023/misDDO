<?php
/*
 * Print fitter for the CAR / RQA reports.
 *
 * The roster table paginates on its own. When it happens to end near the bottom
 * of a sheet, the signature block (page-break-inside: avoid) no longer fits in
 * the leftover space and the browser pushes it onto a fresh sheet, which then
 * prints with signatures only and no applicant rows on it.
 *
 * On beforeprint this measures the print layout and, only when that would
 * happen, tightens the roster rows and the signature block one step at a time
 * until the signatures land on the same sheet as the last rows. If even the
 * tightest step cannot fit them, the closing rows are kept with the signatures
 * so the final sheet still carries data.
 *
 * Reports that already print correctly are measured once and left untouched.
 *
 * Loaded from pages/_rqa_signatories.php - every CAR / RQA report includes it.
 */
?>
<style id="rqaFitBase">
@media print {
    .data tr { page-break-inside: avoid; break-inside: avoid; }
    .prep { page-break-after: avoid; break-after: avoid; }
    .sign, .sign tr { page-break-inside: avoid; break-inside: avoid; }
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
            css += '.data th, .data td { padding:' + px(Math.max(1, 3 * scale)) + ' '
                 + px(Math.max(2, 5 * scale)) + ' !important; line-height:1.15 !important; }'
                 + '.data th, .data td, .data th *, .data td * { font-size:' + px(12 * scale) + ' !important; }'
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

    window.addEventListener('beforeprint', fit);
    window.addEventListener('afterprint', function () { apply(1, false); });
})();
</script>
