<?php
/*
 * Per-vacancy signatory block for the RQA / CAR reports.
 *
 * Expects $vsign - the rows of hris_vacancy_signatories for this jobID, already
 * ordered (see Vacancy_signatory_model::get_by_job). Renders nothing at all when
 * the vacancy has no signatory encoded, so a report that was never set up looks
 * exactly as it did before.
 *
 * Optional: $vsign_caption overrides the heading line.
 *
 * Maintained from Page/jobVacancy > Actions > Signatories
 * (VacancySignatories/index/{jobID}).
 */

$vsign_rows = (isset($vsign) && is_array($vsign)) ? $vsign : array();

if (!empty($vsign_rows)) :

    $vsign_caption = isset($vsign_caption)
        ? $vsign_caption
        : 'Prepared by the HRMPSB <span class="vsig-note">(All members should affix signature)</span>';

    // five to a row, matching the column count the CAR sheet has always used
    $vsign_chunks = array_chunk($vsign_rows, 5);

    // Excel exports (render_rqa_report) are HTML saved as .xls - Excel cannot
    // fetch the signature over http, so those sheets get the names only.
    $vsign_excel = !empty($is_excel_export);
?>

<style>
    .vsig-wrap { margin-top: 26px; }
    .vsig-cap {
        margin: 0 0 6px 0;
        font-size: 13px;
        font-weight: bold;
    }
    .vsig-cap .vsig-note { font-weight: normal; font-style: italic; font-size: 12px; }
    table.vsig, table.vsig td {
        border: 0 !important;
        text-align: center;
        vertical-align: bottom;
    }
    table.vsig { width: 100% !important; border-collapse: collapse; }
    table.vsig td { padding: 10px 6px 22px 6px; }
    .vsig-img {
        display: block;
        margin: 0 auto -8px auto;
        height: 46px;
        max-width: 92%;
        object-fit: contain;
    }
    .vsig-space { height: 46px; }
    .vsig-name {
        display: inline-block;
        border-bottom: 1px solid #000;
        font-weight: bold;
        font-size: 14px;
        padding: 0 12px;
        white-space: nowrap;
    }
    .vsig-pos { font-size: 11px; line-height: 1.25; margin-top: 2px; }
    .vsig-role { font-size: 11px; line-height: 1.25; }

    @media print {
        .vsig-wrap { margin-top: 20px; page-break-inside: avoid; break-inside: avoid; }
        table.vsig tr { page-break-inside: avoid; break-inside: avoid; }
        table.vsig, table.vsig td { border: 0 !important; }
        table.vsig td { padding: 8px 5px 18px 5px; }
        .vsig-img { height: 42px; }
        .vsig-space { height: 42px; }
    }
</style>

<div class="vsig-wrap">
    <p class="vsig-cap"><?= $vsign_caption; ?></p>

    <table class="vsig">
        <?php foreach ($vsign_chunks as $vsign_row) : ?>
            <tr>
                <?php foreach ($vsign_row as $vsig) : ?>
                    <td>
                        <?php if (!$vsign_excel && trim((string) $vsig->esig) !== '') : ?>
                            <img class="vsig-img"
                                 src="<?= base_url(); ?>uploads/esig/<?= rawurlencode($vsig->esig); ?>"
                                 alt="">
                        <?php elseif (!$vsign_excel) : ?>
                            <div class="vsig-space"></div>
                        <?php endif; ?>

                        <span class="vsig-name"><?= html_escape(strtoupper($vsig->name)); ?></span>

                        <?php if (trim((string) $vsig->designation) !== '') : ?>
                            <div class="vsig-pos"><?= html_escape($vsig->designation); ?></div>
                        <?php endif; ?>

                        <?php if (trim((string) $vsig->sign_role) !== '') : ?>
                            <div class="vsig-role"><?= html_escape($vsig->sign_role); ?></div>
                        <?php endif; ?>
                    </td>
                <?php endforeach; ?>

                <?php // pad a short last row only when there is a full row above it
                      // to line up with - a single short row spreads on its own ?>
                <?php if (count($vsign_chunks) > 1) : ?>
                    <?php for ($i = count($vsign_row); $i < 5; $i++) : ?>
                        <td></td>
                    <?php endfor; ?>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

<?php endif; ?>
