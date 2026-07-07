<?php
// Lightweight partial returned via AJAX and injected into the SMEA modal on the
// generate_smea page. It renders ONLY the quarter being edited so the form stays
// focused on the cell the user actually clicked. Values for the other quarters are
// preserved through hidden inputs so update_smea()/update_smea_ad() (which rewrite
// smea_q1..q4) never wipe them.
$qn = (int) $q;
$labels = [1 => '1ST', 2 => '2ND', 3 => '3RD', 4 => '4TH'];
$label  = isset($labels[$qn]) ? $labels[$qn] : $qn;

$target_field = 'q' . $qn;          // planned target for this quarter
$acc_field    = 'smea_q' . $qn;     // accomplishment for this quarter
$rem_field    = 'remarks_q' . $qn;  // remarks for this quarter

// Sum of the OTHER quarters' accomplishments, used to keep the running total correct.
$others_sum = 0;
for ($i = 1; $i <= 4; $i++) {
    if ($i === $qn) {
        continue;
    }
    $others_sum += (float) $sop->{'smea_q' . $i};
}
?>
<form class="smea-modal-form" method="post" data-others-sum="<?= $others_sum; ?>">
    <p class="smea-modal-sub"><?= $label; ?> QUARTER &mdash; enter this quarter's accomplishment against the planned target.</p>

    <div class="smea-field">
        <label><?= $label; ?> QUARTER TARGET</label>
        <input type="text" class="smea-input" value="<?= htmlspecialchars($sop->$target_field, ENT_QUOTES); ?>" disabled>
    </div>

    <div class="smea-field">
        <label><?= $label; ?> QUARTER ACCOMPLISHMENT <span class="smea-req">*</span></label>
        <input type="text" class="smea-input smea-acc" name="q<?= $qn; ?>" value="<?= htmlspecialchars($sop->$acc_field, ENT_QUOTES); ?>" autofocus>
    </div>

    <div class="smea-field">
        <label><?= $label; ?> QUARTER REMARKS</label>
        <input type="text" class="smea-input" name="remarks_q<?= $qn; ?>" value="<?= htmlspecialchars($sop->$rem_field, ENT_QUOTES); ?>">
    </div>

    <div class="smea-field">
        <label>TOTAL ACCOMPLISHMENT (all quarters)</label>
        <input type="text" class="smea-input smea-total-display" value="" disabled>
    </div>

    <?php // Preserve the accomplishments of the quarters we are not editing.
    for ($i = 1; $i <= 4; $i++) {
        if ($i === $qn) {
            continue;
        } ?>
        <input type="hidden" name="q<?= $i; ?>" value="<?= htmlspecialchars($sop->{'smea_q' . $i}, ENT_QUOTES); ?>">
    <?php } ?>

    <input type="hidden" name="total" class="smea-total" value="<?= htmlspecialchars($sop->smea_total, ENT_QUOTES); ?>">
    <input type="hidden" name="q" value="<?= $qn; ?>">
    <input type="hidden" name="submit" value="1">

    <div class="smea-actions">
        <button type="submit" class="smea-btn-save">Save Quarter <?= $qn; ?></button>
        <button type="button" class="smea-btn-cancel smea-modal-close">Cancel</button>
    </div>
</form>
