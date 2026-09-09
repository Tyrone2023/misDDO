<?php
defined('BASEPATH') or exit('No direct script access allowed');

$supportingFile = basename((string) ($supporting_file ?? ''));
$supportingLabel = (string) ($supporting_label ?? 'Supporting Document');
$supportingType = (string) ($supporting_type ?? '');
$supportingColumn = (string) ($supporting_column ?? '');
$supportingBackground = (string) ($supporting_background ?? '#f7e8bc');
$supportingCanManage = !empty($supporting_can_manage);
$supportingApplicantId = (int) ($supporting_applicant_id ?? 0);
$supportingJobId = (int) ($supporting_job_id ?? 0);
$supportingSchoolId = (string) ($supporting_school_id ?? '');
$supportingAppId = (int) ($supporting_app_id ?? 0);
?>
<tr>
    <th class="text-right"><?= htmlspecialchars($supportingLabel, ENT_QUOTES, 'UTF-8'); ?></th>
    <td class="text-left" style="background: <?= htmlspecialchars($supportingBackground, ENT_QUOTES, 'UTF-8'); ?>; color:#464545">
        <?php if ($supportingFile !== ''): ?>
            <a href="<?= base_url(); ?>Pages/pdf/<?= $supportingApplicantId; ?>/<?= rawurlencode($supportingColumn); ?>/?<?= http_build_query(['label' => $supportingLabel]); ?>" target="_blank" class="tooltips" data-placement="top" data-toggle="tooltip" data-original-title="View File Attachment"><i class="fas fa-file-alt btn btn-lg"></i></a>
        <?php endif; ?>
        <?php if ($supportingCanManage): ?>
            <a href="#" data-toggle="modal" data-target=".supporting-document-<?= htmlspecialchars($supportingType, ENT_QUOTES, 'UTF-8'); ?>"><i class="fas fa-paperclip btn btn-lg tooltips" data-placement="top" data-toggle="tooltip" data-original-title="Attach File"></i></a>
            <?php if ($supportingFile !== ''): ?>
                <a onclick="return confirm('Are you sure?')" href="<?= base_url(); ?>Pages/remove_supporting_document/<?= $supportingApplicantId; ?>/<?= $supportingJobId; ?>/<?= rawurlencode($supportingSchoolId); ?>/<?= rawurlencode($supportingType); ?>/<?= $supportingAppId; ?>" class="btn btn-warning"><i class="mdi mdi-block-helper mr-2 text-danger tooltips" data-placement="top" data-toggle="tooltip" data-original-title="Remove Attachment"></i>Remove Attachment</a>
            <?php endif; ?>
        <?php endif; ?>
    </td>
</tr>
