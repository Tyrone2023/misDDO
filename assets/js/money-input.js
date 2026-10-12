/*
 * Money inputs: show thousand separators while typing, post plain numbers.
 * Mark an input with data-money. MySQL runs non-strict here, so a posted
 * "4,636.36" would be silently saved as 4 - commas must never reach the server.
 */
(function () {
    // Comma-safe number for calculators (Number("4,636") is NaN, parseFloat gives 4).
    window.moneyVal = function (v) {
        return parseFloat(String(v == null ? '' : v).replace(/[^\d.\-]/g, '')) || 0;
    };

    window.moneyFmt = function (n) {
        return (Math.round(moneyVal(n) * 100) / 100).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    };

    function format(el) {
        if (el.value === '') return;
        var caret = el.selectionEnd, oldLen = el.value.length;
        var parts = el.value.replace(/[^\d.]/g, '').split('.');
        el.value = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',') + (parts.length > 1 ? '.' + parts[1].slice(0, 2) : '');
        if (document.activeElement === el && caret != null) {
            el.selectionEnd = Math.max(0, caret + (el.value.length - oldLen));
        }
    }

    function strip(form) {
        var els = form.querySelectorAll('input[data-money]');
        for (var i = 0; i < els.length; i++) els[i].value = els[i].value.replace(/,/g, '');
    }

    document.addEventListener('input', function (e) {
        if (e.target.matches && e.target.matches('input[data-money]:not([readonly])')) format(e.target);
    }, true);

    // Capture phase so commas are gone before any other submit handler serialises the form.
    document.addEventListener('submit', function (e) { strip(e.target); }, true);

    document.addEventListener('DOMContentLoaded', function () {
        var els = document.querySelectorAll('input[data-money]');
        for (var i = 0; i < els.length; i++) format(els[i]);
    });
})();
