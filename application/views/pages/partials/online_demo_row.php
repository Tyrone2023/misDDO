<?php
    $application = $application ?? null;
    $onlineDemoEnabled = !empty($online_demo_enabled);
?>
<?php if ($onlineDemoEnabled && !empty($application)) : ?>
    <?php
        $demoLink = trim((string)($application->online_demo ?? ''));
        $demoHref = $demoLink;
        if ($demoHref !== '' && !preg_match('#^https?://#i', $demoHref)) {
            $demoHref = 'https://' . ltrim($demoHref, '/');
        }
        $applicantId = $applicant_id ?? $this->uri->segment(3);
        $jobId = $job_id ?? $this->uri->segment(4);
        $schoolId = $school_id ?? $this->uri->segment(5);
        $isApplicantOwner = in_array((string)$this->session->position, ['reg', 'user'], true)
            && (string)$this->session->c_id === (string)$applicantId
            && (string)($application->applicant_id ?? '') === (string)$this->session->c_id;
        $formId = 'online-demo-form-' . (int)($application->appID ?? 0);
    ?>
    <tr id="online_demo">
        <th class="text-right">Demo link</th>
        <td style="background: #9ddcf4; color:#222">
            <?php if ($demoLink !== '') : ?>
                <a href="<?= html_escape($demoHref); ?>" target="_blank" rel="noopener noreferrer" class="btn btn-info btn-sm">Demo link</a>
                <?php if ($isApplicantOwner) : ?>
                    <a href="#<?= $formId; ?>" data-toggle="collapse" class="btn btn-secondary btn-sm ml-1">Update</a>
                <?php endif; ?>
            <?php endif; ?>

            <?php if ($isApplicantOwner) : ?>
                <div class="<?= $demoLink !== '' ? 'collapse mt-2' : ''; ?>" id="<?= $formId; ?>">
                    <form class="form-inline" action="<?= base_url(); ?>pages/update_online_demo" method="post">
                        <input type="hidden" name="id" value="<?= html_escape($applicantId); ?>">
                        <input type="hidden" name="jobID" value="<?= html_escape($jobId); ?>">
                        <input type="hidden" name="school_id" value="<?= html_escape($schoolId); ?>">
                        <input type="hidden" name="appID" value="<?= (int)($application->appID ?? 0); ?>">
                        <div class="input-group" style="max-width: 620px; width: 100%;">
                            <input type="url" class="form-control" name="online_demo" maxlength="500" placeholder="https://drive.google.com/..." required>
                            <div class="input-group-append">
                                <button class="btn btn-info" type="submit">Save</button>
                            </div>
                        </div>
                    </form>
                </div>
            <?php elseif ($demoLink === '') : ?>
                <span class="text-muted">No demo link submitted.</span>
            <?php endif; ?>
        </td>
    </tr>
<?php endif; ?>
