<?php
/**
 * Read-only lock for the rating/scoring pages (rp, rp_reg_none, rp_reg_promotion,
 * rp_staff, ...). Loaded by Pages::ma()/ma_staff() when the job vacancy's
 * jvStatus is 'Closed'. It disables every form control inside the page content
 * for ALL roles, so a closed vacancy can no longer be re-scored from here.
 * Score corrections after closing go through the Corrigendum / Addendum page.
 */
?>
<style>
    .rating-locked-banner {
        position: sticky;
        top: 0;
        z-index: 1020;
        margin: 0 0 14px;
        padding: 12px 16px;
        border-radius: 10px;
        background: #fff4e5;
        border: 1px solid #ffd9a8;
        color: #8a5300;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .rating-locked-banner i { font-size: 20px; }
    .rating-locked-banner small { display: block; font-weight: 500; color: #a06a1f; }
    .rating-locked .content-page [disabled] { cursor: not-allowed; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var scope = document.querySelector('.content-page') || document.body;
    if (!scope) return;

    document.body.classList.add('rating-locked');

    // Disable every editable control + submit/action button in the page body.
    var controls = scope.querySelectorAll('input, select, textarea, button, .btn');
    controls.forEach(function (el) {
        // Leave plain navigation links (<a>) clickable; only lock form controls
        // and buttons that change the rating.
        if (el.tagName === 'A') return;
        el.setAttribute('disabled', 'disabled');
        el.classList.add('disabled');
    });

    // Stop any form in the content area from submitting, just in case.
    scope.querySelectorAll('form').forEach(function (f) {
        f.addEventListener('submit', function (e) {
            e.preventDefault();
            e.stopPropagation();
            return false;
        });
    });

    // Banner at the top of the content.
    var content = scope.querySelector('.content') || scope;
    var banner = document.createElement('div');
    banner.className = 'rating-locked-banner';
    banner.innerHTML = '<i class="mdi mdi-lock-outline"></i>'
        + '<span>This vacancy is <strong>Closed</strong> &mdash; scoring is locked and cannot be edited.'
        + '<small>To correct or add a score after closing, use the Corrigendum / Addendum page.</small></span>';
    content.insertBefore(banner, content.firstChild);
});
</script>
