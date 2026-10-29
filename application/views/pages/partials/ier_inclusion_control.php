<?php
/**
 * "Include in IER" control for one Master's / Doctor's / TESDA NC row.
 *
 * Expects: $ier_item ('master'|'doctor'|'tesda'), $ier_has_data (bool),
 * $ier_inclusions (array keyed by item), $ier_can_include (bool).
 */
$ierIncluded = $ier_inclusions[$ier_item] ?? null;
$ierLabels = array('master' => "Master's Degree", 'doctor' => "Doctor's Degree", 'tesda' => 'TESDA National Certificate');
$ierLabel = $ierLabels[$ier_item];
$ierDate = static function ($value) {
    return !empty($value) ? date('M d, Y', strtotime($value)) : '';
};
?>
<?php if ($ierIncluded || ($ier_has_data && !empty($ier_can_include))): ?>
    <div class="ier-inclusion mt-1">
        <?php if ($ierIncluded): ?>
            <span class="badge badge-primary"><i class="mdi mdi-check mr-1"></i>Included in IER</span>
            <?php if ($ier_item === 'tesda'):
                $ierFrom = $ierDate($ierIncluded->date_from);
                $ierTo = $ierDate($ierIncluded->date_to);
                $ierHours = $ierIncluded->hours !== null ? rtrim(rtrim((string) $ierIncluded->hours, '0'), '.') : '';
            ?>
                <small class="text-muted ml-1">
                    <?= html_escape(trim($ierFrom . ($ierFrom !== '' && $ierTo !== '' ? ' - ' : '') . $ierTo)); ?>
                    <?= $ierHours !== '' ? '&middot; ' . html_escape($ierHours) . ' hrs' : ''; ?>
                </small>
            <?php endif; ?>
            <?php if (!empty($ier_can_include)): ?>
                <?php if ($ier_item === 'tesda'): ?>
                    <button type="button" class="btn btn-xs btn-outline-primary ml-1 js-ier-tesda-open"
                            data-date-from="<?= html_escape($ierIncluded->date_from ?? ''); ?>"
                            data-date-to="<?= html_escape($ierIncluded->date_to ?? ''); ?>"
                            data-hours="<?= html_escape($ierHours); ?>">Edit</button>
                <?php endif; ?>
                <button type="button" class="btn btn-xs btn-outline-danger ml-1 js-ier-remove"
                        data-item="<?= html_escape($ier_item); ?>"
                        data-label="<?= html_escape($ierLabel); ?>">Remove from IER</button>
            <?php endif; ?>
        <?php elseif ($ier_item === 'tesda'): ?>
            <button type="button" class="btn btn-sm btn-outline-primary js-ier-tesda-open">
                <i class="mdi mdi-plus mr-1"></i>Include in IER Training
            </button>
        <?php else: ?>
            <button type="button" class="btn btn-sm btn-outline-primary js-ier-include"
                    data-item="<?= html_escape($ier_item); ?>"
                    data-label="<?= html_escape($ierLabel); ?>">
                <i class="mdi mdi-plus mr-1"></i>Include in IER Education
            </button>
        <?php endif; ?>
    </div>
<?php endif; ?>
