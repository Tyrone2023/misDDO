<?php
/**
 * One issued applicant document, laid out after the division's own forms and
 * rendered the same way in the Secretariat's editing modal and on the
 * printable A4 page.
 *
 *   assessment - ANNEX E (qualified) / ANNEX F (disqualified),
 *                EVALUATIVE-ASSESSMENT-OF-HRMPSB
 *   letter     - LETTER TO APPLICANTS NON-COMPLIANT OF DOCS
 *
 * $doc       array  the document body (see Secretariat_model::assessment_defaults)
 * $docType   string 'assessment' or 'letter'
 * $editable  bool   true in the modal, false when printing
 * $esig      string signature file kept under uploads/esig, print only
 *
 * Every value is printed as escaped text and collected back as plain text, so
 * nothing typed into the document can come back out as markup.
 */
$doc = $doc ?? [];
$docType = ($docType ?? 'assessment') === 'letter' ? 'letter' : 'assessment';
$editable = !empty($editable);
$esig = trim((string) ($esig ?? ''));

$ad_h = static function ($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};

// contenteditable is added only in the modal; the print page renders plain text.
$ad_edit = $editable ? ' contenteditable="true" spellcheck="false"' : '';
$ad_field = static function ($name, $value, $tag = 'span', $class = '') use ($ad_h, $ad_edit) {
    $class = trim('ad-f ' . $class);
    return '<' . $tag . ' class="' . $class . '" data-field="' . $ad_h($name) . '"' . $ad_edit . '>'
        . nl2br($ad_h($value)) . '</' . $tag . '>';
};
$ad_cell = static function ($name, $value, $class = '') use ($ad_h, $ad_edit) {
    $class = trim('ad-f ' . $class);
    return '<span class="' . $class . '" data-cell="' . $ad_h($name) . '"' . $ad_edit . '>'
        . nl2br($ad_h($value)) . '</span>';
};
$ad_items = (array) ($doc['items'] ?? []);
?>

<style>
    .ad-doc { color:#111; font-family:"Bookman Old Style",Bookman,"URW Bookman","Times New Roman",serif; font-size:10pt; line-height:1.45; }
    .ad-doc .ad-f { display:inline-block; min-width:40px; outline:none; }
    .ad-doc [contenteditable="true"] { background:linear-gradient(180deg,rgba(255,251,234,.72),rgba(255,248,216,.72)); border-bottom:1px dashed #cdbd78; border-radius:3px; padding:1px 3px; transition:background .12s ease,box-shadow .12s ease; }
    .ad-doc [contenteditable="true"]:hover { background:#fff7d8; }
    .ad-doc [contenteditable="true"]:focus { background:#fff; box-shadow:0 0 0 2px rgba(32,91,189,.22); }
    .ad-doc .ad-office { display:block; font-weight:700; letter-spacing:.01em; margin-bottom:25px; text-transform:uppercase; }
    .ad-doc[data-doc-type="assessment"] .ad-office { text-align:center; width:100%; }
    .ad-doc .ad-annex { display:block; font-size:10pt; font-style:italic; font-weight:700; position:absolute; right:17.8mm; text-align:right; top:9mm; width:auto; z-index:1; }
    .ad-doc .ad-date { display:block; margin-bottom:24px; }
    .ad-doc .ad-addr { line-height:1.5; margin-bottom:24px; }
    .ad-doc .ad-addr .ad-f { display:block; }
    .ad-doc .ad-addr .ad-name { font-weight:700; text-transform:uppercase; }
    .ad-doc .ad-para { display:block; margin-bottom:14px; text-align:left; width:100%; }
    .ad-doc .ad-lead { display:block; margin-bottom:15px; width:100%; }
    .ad-doc .ad-table { border-collapse:collapse; margin:7px 0 17px; table-layout:fixed; width:100%; }
    .ad-doc .ad-table th, .ad-doc .ad-table td { border:.75px solid #111; padding:7px 8px; vertical-align:top; overflow-wrap:anywhere; }
    .ad-doc .ad-table th { background:#e7e6e6; font-size:10pt; font-weight:400; height:30pt; line-height:1.25; padding:6px; text-align:center; vertical-align:middle; }
    .ad-doc .ad-table th:nth-child(2) { font-weight:700; }
    .ad-doc .ad-table tbody tr { height:30pt; }
    .ad-doc .ad-table td .ad-f { display:block; min-height:22px; width:100%; }
    .ad-doc .ad-table td .ad-criterion { display:inline-block; font-weight:700; min-height:17px; width:auto; }
    .ad-doc .ad-table .ad-criterion + [data-cell="qs"] { display:inline-block; margin-left:4px; min-height:17px; vertical-align:baseline; width:auto; }
    .ad-doc .ad-table td.ad-itemno { font-weight:700; text-align:left; vertical-align:top; }
    .ad-doc .ad-sign { margin-top:34px; }
    .ad-doc .ad-sign .ad-isig { display:block; margin:0 0 -20px; max-height:70px; }
    .ad-doc .ad-sign .ad-signame { display:inline-block; font-weight:700; text-transform:uppercase; }
    .ad-doc .ad-sign .ad-sigtitle, .ad-doc .ad-sign .ad-sigrole { display:block; }
    .ad-doc .ad-action-cell { position:relative; }
    .ad-doc .ad-rowbtn { align-items:center; background:#fff; border:1px solid #d8dfe9; border-radius:50%; box-shadow:0 2px 7px rgba(24,49,83,.12); color:#7b8799; cursor:pointer; display:flex; font-family:Arial,sans-serif; font-size:15px; height:26px; justify-content:center; line-height:1; padding:0; position:absolute; right:-34px; top:50%; transform:translateY(-50%); width:26px; }
    .ad-doc .ad-rowbtn:hover { background:#fdeaea; border-color:#f0b9b9; color:#b23b3b; }
    .ad-doc .ad-addrow { background:#fff; border:1px dashed #b8c7da; border-radius:7px; color:#35597f; cursor:pointer; font-family:"Segoe UI",Arial,sans-serif; font-size:12px; font-weight:600; margin:-3px 0 16px; padding:6px 11px; }
    .ad-doc .ad-addrow:hover { background:#f2f6fd; border-color:#9ab6e3; }
    @media (max-width:900px) {
        .ad-doc .ad-rowbtn { right:4px; }
        .ad-doc .ad-action-cell { padding-right:36px; }
    }
    @media print {
        .ad-doc .ad-edit-only { display:none !important; }
        .ad-doc .ad-rowbtn, .ad-doc .ad-addrow { display:none !important; }
        .ad-doc [contenteditable] { background:none !important; border:0 !important; box-shadow:none !important; padding:0 !important; }
    }
</style>

<?php if ($docType === 'letter') : ?>

    <div class="ad-doc ad-letter" data-doc-type="letter">
        <?= $ad_field('office', $doc['office'] ?? '', 'span', 'ad-office'); ?>
        <?= $ad_field('date', $doc['date'] ?? '', 'span', 'ad-date'); ?>

        <div class="ad-addr">
            <?= $ad_field('applicant', $doc['applicant'] ?? '', 'span', 'ad-name'); ?>
            <?= $ad_field('position_line', $doc['position_line'] ?? '', 'span'); ?>
            <?= $ad_field('address', $doc['address'] ?? '', 'span'); ?>
        </div>

        <?= $ad_field('salutation', $doc['salutation'] ?? '', 'span', 'ad-lead'); ?>
        <?= $ad_field('greeting', $doc['greeting'] ?? '', 'span', 'ad-lead'); ?>
        <?= $ad_field('body1', $doc['body1'] ?? '', 'span', 'ad-para'); ?>
        <?= $ad_field('body2', $doc['body2'] ?? '', 'span', 'ad-para'); ?>

        <table class="ad-table">
            <colgroup>
                <col style="width:58%">
                <col style="width:42%">
            </colgroup>
            <thead>
                <tr>
                    <th>Documentary Requirement/s</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody data-rows>
                <?php foreach ($ad_items as $item) : ?>
                    <?php $item = is_array($item) ? $item : ['requirement' => (string) $item, 'remarks' => '']; ?>
                    <tr data-row>
                        <td><?= $ad_cell('requirement', $item['requirement'] ?? ''); ?></td>
                        <td class="ad-action-cell">
                            <?= $ad_cell('remarks', $item['remarks'] ?? ''); ?>
                            <?php if ($editable) : ?>
                                <button type="button" class="ad-rowbtn" data-remove-row title="Remove this row">&times;</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if ($editable) : ?>
            <button type="button" class="ad-addrow ad-edit-only" data-add-row>+ Add a requirement row</button>
        <?php endif; ?>

        <?= $ad_field('body3', $doc['body3'] ?? '', 'span', 'ad-para'); ?>
        <?= $ad_field('body4', $doc['body4'] ?? '', 'span', 'ad-para'); ?>
        <?= $ad_field('body5', $doc['body5'] ?? '', 'span', 'ad-para'); ?>
        <?= $ad_field('closing', $doc['closing'] ?? '', 'span', 'ad-lead'); ?>

        <div class="ad-sign">
            <?php if (!$editable && $esig !== '' && is_file(FCPATH . 'uploads/esig/' . basename($esig))) : ?>
                <img class="ad-isig" src="<?= base_url(); ?>uploads/esig/<?= rawurlencode(basename($esig)); ?>" alt="">
            <?php endif; ?>
            <?= $ad_field('signatory', $doc['signatory'] ?? '', 'span', 'ad-signame'); ?>
            <?= $ad_field('signatory_title', $doc['signatory_title'] ?? '', 'span', 'ad-sigtitle'); ?>
            <?= $ad_field('signatory_role', $doc['signatory_role'] ?? '', 'span', 'ad-sigrole'); ?>
        </div>
    </div>

<?php else : ?>

    <div class="ad-doc" data-doc-type="assessment">
        <?= $ad_field('annex', $doc['annex'] ?? '', 'span', 'ad-annex'); ?>
        <?= $ad_field('office', $doc['office'] ?? '', 'span', 'ad-office'); ?>
        <?= $ad_field('date', $doc['date'] ?? '', 'span', 'ad-date'); ?>

        <div class="ad-addr">
            <?= $ad_field('applicant', $doc['applicant'] ?? '', 'span', 'ad-name'); ?>
            <?= $ad_field('address1', $doc['address1'] ?? '', 'span'); ?>
            <?= $ad_field('address2', $doc['address2'] ?? '', 'span'); ?>
        </div>

        <?= $ad_field('salutation', $doc['salutation'] ?? '', 'span', 'ad-lead'); ?>
        <?php if (trim((string) ($doc['greeting'] ?? '')) !== '') : ?>
            <?= $ad_field('greeting', $doc['greeting'] ?? '', 'span', 'ad-lead'); ?>
        <?php endif; ?>
        <?= $ad_field('intro', $doc['intro'] ?? '', 'span', 'ad-para'); ?>

        <table class="ad-table">
            <colgroup>
                <col style="width:20.2%">
                <col style="width:28.1%">
                <col style="width:28.8%">
                <col style="width:22.9%">
            </colgroup>
            <thead>
                <tr>
                    <th>Position Applied for:</th>
                    <th>CSC-approved QS of the Position</th>
                    <th>Your Qualification</th>
                    <th>Remarks/Details</th>
                </tr>
            </thead>
            <tbody data-rows>
                <?php $adRows = count($ad_items); ?>
                <?php foreach ($ad_items as $adIndex => $item) : ?>
                    <?php $item = is_array($item) ? $item : ['criterion' => (string) $item, 'qs' => '', 'yours' => '', 'remarks' => '']; ?>
                    <tr data-row>
                        <?php if ($adIndex === 0) : ?>
                            <td class="ad-itemno" rowspan="<?= max(1, $adRows); ?>" data-itemno>
                                <?= $ad_field('item_no', $doc['item_no'] ?? '', 'span'); ?>
                            </td>
                        <?php endif; ?>
                        <td>
                            <?= $ad_cell('criterion', $item['criterion'] ?? '', 'ad-criterion'); ?>
                            <?= $ad_cell('qs', $item['qs'] ?? ''); ?>
                        </td>
                        <td><?= $ad_cell('yours', $item['yours'] ?? ''); ?></td>
                        <td class="ad-action-cell">
                            <?= $ad_cell('remarks', $item['remarks'] ?? ''); ?>
                            <?php if ($editable) : ?>
                                <button type="button" class="ad-rowbtn" data-remove-row title="Remove this row">&times;</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if ($editable) : ?>
            <button type="button" class="ad-addrow ad-edit-only" data-add-row>+ Add a criterion row</button>
        <?php endif; ?>

        <?= $ad_field('body2', $doc['body2'] ?? '', 'span', 'ad-para'); ?>
        <?php if (trim((string) ($doc['body3'] ?? '')) !== '' || $editable) : ?>
            <?= $ad_field('body3', $doc['body3'] ?? '', 'span', 'ad-para'); ?>
        <?php endif; ?>
        <?= $ad_field('thanks', $doc['thanks'] ?? '', 'span', 'ad-lead'); ?>
        <?= $ad_field('closing', $doc['closing'] ?? '', 'span', 'ad-lead'); ?>

        <div class="ad-sign">
            <?php if (!$editable && $esig !== '' && is_file(FCPATH . 'uploads/esig/' . basename($esig))) : ?>
                <img class="ad-isig" src="<?= base_url(); ?>uploads/esig/<?= rawurlencode(basename($esig)); ?>" alt="">
            <?php endif; ?>
            <?= $ad_field('signatory', $doc['signatory'] ?? '', 'span', 'ad-signame'); ?>
            <?= $ad_field('signatory_title', $doc['signatory_title'] ?? '', 'span', 'ad-sigtitle'); ?>
        </div>
    </div>

<?php endif; ?>
