<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div style="margin-left: 250px;">
                <div class="page-title-box">
                    <h4 class="mb-0 font-size-18">View Leave Application</h4>
                </div>

                <?php if (!empty($application)): ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 mb-2"><strong>Leave Type:</strong><br><?= htmlspecialchars((string)($application->leave_type_code ?? '')); ?></div>
                                <div class="col-md-4 mb-2"><strong>Date Filed:</strong><br><?= htmlspecialchars((string)($application->date_filed ?? '')); ?></div>
                                <div class="col-md-4 mb-2"><strong>Status:</strong><br><?= htmlspecialchars((string)($application->status ?? '')); ?></div>
                                <div class="col-md-4 mb-2"><strong>Date From:</strong><br><?= htmlspecialchars((string)($application->date_from ?? '')); ?></div>
                                <div class="col-md-4 mb-2"><strong>Date To:</strong><br><?= htmlspecialchars((string)($application->date_to ?? '')); ?></div>
                                <div class="col-md-4 mb-2"><strong>Working Days:</strong><br><?= htmlspecialchars((string)($application->working_days ?? '')); ?></div>
                                <div class="col-12 mb-2"><strong>Reason:</strong><br><?= nl2br(htmlspecialchars((string)($application->reason ?? ''))); ?></div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h5 class="mb-3">Applicant Attachment</h5>

                            <?php if (!empty($attachment_exists) && !empty($attachment_is_pdf) && !empty($application->attachment_path)): ?>
                                <div class="mb-3">
                                    <a href="<?= base_url($application->attachment_path); ?>" target="_blank" class="btn btn-sm btn-primary">Open PDF in New Tab</a>
                                </div>
                                <div style="border:1px solid #ddd; height:800px; overflow:hidden;">
                                    <iframe src="<?= base_url($application->attachment_path); ?>" width="100%" height="100%" style="border:0;"></iframe>
                                </div>
                            <?php elseif (!empty($application->attachment_path)): ?>
                                <a href="<?= base_url($application->attachment_path); ?>" target="_blank" class="btn btn-sm btn-primary">Open Attachment</a>
                            <?php else: ?>
                                <div class="alert alert-secondary mb-0">No attachment uploaded.</div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-danger">Leave application not found.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
