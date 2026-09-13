<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<style>
    .applicant-upload-reminder .modal-content {
        overflow: hidden;
        border: 0;
        border-radius: 16px;
        box-shadow: 0 24px 70px rgba(25, 47, 72, .3);
    }

    .applicant-upload-reminder .modal-header {
        align-items: center;
        border: 0;
        background: linear-gradient(120deg, #174b7a, #2785c5);
        padding: 20px 24px;
        color: #fff;
    }

    .applicant-upload-reminder .modal-title {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 0;
        color: #fff;
        font-size: 19px;
        font-weight: 700;
    }

    .applicant-upload-reminder .modal-title i {
        font-size: 25px;
    }

    .applicant-upload-reminder .modal-body {
        max-height: calc(100vh - 190px);
        overflow-y: auto;
        padding: 24px 26px 12px;
        color: #465a6d;
        font-size: 14px;
        line-height: 1.65;
    }

    .applicant-upload-reminder .upload-reminder-announcement {
        margin: 0 0 18px;
        overflow: hidden;
        border: 1px solid #badcf2;
        border-radius: 10px;
        background: #f5fbff;
    }

    .applicant-upload-reminder .upload-reminder-announcement-title {
        display: flex;
        align-items: center;
        gap: 7px;
        background: #dff2fd;
        padding: 10px 14px;
        color: #175f8d;
        font-weight: 800;
        letter-spacing: .02em;
        text-transform: uppercase;
    }

    .applicant-upload-reminder .upload-reminder-announcement-body {
        padding: 14px 16px;
        color: #31566d;
        line-height: 1.65;
    }

    .applicant-upload-reminder .upload-reminder-highlight {
        margin: 17px 0 5px;
        border-left: 4px solid #f1aa35;
        border-radius: 8px;
        background: #fff8e9;
        padding: 13px 15px;
        color: #76531a;
        font-weight: 600;
    }

    .applicant-upload-reminder .upload-reminder-shared {
        margin: 12px 0 5px;
        border-left: 4px solid #2785c5;
        border-radius: 8px;
        background: #edf7fd;
        padding: 13px 15px;
        color: #285d7e;
    }

    .applicant-upload-reminder .upload-reminder-feedback {
        margin: 0 0 16px;
        border: 0;
        border-radius: 9px;
    }

    .applicant-upload-reminder .modal-footer {
        border: 0;
        padding: 10px 26px 24px;
    }

    .applicant-upload-reminder .btn-confirm-reminder {
        min-width: 190px;
        border-radius: 9px;
        padding: 10px 18px;
        font-weight: 700;
    }
</style>

<div class="modal fade applicant-upload-reminder" id="applicantUploadReminderModal" tabindex="-1" role="dialog" aria-labelledby="applicantUploadReminderTitle" aria-describedby="applicantUploadReminderDescription" data-backdrop="static" data-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="applicantUploadReminderTitle">
                    <i class="mdi mdi-alert-circle-outline" aria-hidden="true"></i>
                    Important Upload Reminder
                </h5>
            </div>
            <div class="modal-body" id="applicantUploadReminderDescription">
                <?php $reminderAnnouncement = trim((string) ($applicant_document_reminder_announcement ?? '')); ?>
                <?php if ($reminderAnnouncement !== ''): ?>
                    <div class="upload-reminder-announcement" role="note" aria-label="Announcement">
                        <div class="upload-reminder-announcement-title">
                            <i class="mdi mdi-bullhorn-outline" aria-hidden="true"></i>
                            Announcement
                        </div>
                        <div class="upload-reminder-announcement-body">
                            <?= nl2br(htmlspecialchars($reminderAnnouncement, ENT_QUOTES, 'UTF-8')); ?>
                        </div>
                    </div>
                <?php endif; ?>
                <?php foreach (($applicant_document_reminder_feedback ?? []) as $feedbackType => $feedbackMessage): ?>
                    <div class="alert <?= $feedbackType === 'danger' ? 'alert-danger' : 'alert-success'; ?> upload-reminder-feedback" role="alert">
                        <i class="mdi <?= $feedbackType === 'danger' ? 'mdi-alert-outline' : 'mdi-check-circle-outline'; ?> mr-1" aria-hidden="true"></i>
                        <?= nl2br(htmlspecialchars(strip_tags((string) $feedbackMessage), ENT_QUOTES, 'UTF-8')); ?>
                    </div>
                <?php endforeach; ?>
                <p>Please double-check all required documents after uploading them. A weak or interrupted internet connection may prevent a selected file from being completely uploaded and saved.</p>
                <div class="upload-reminder-highlight">
                    Even if you have already uploaded your documents, review your entire application before proceeding. Confirm that every required attachment appears and opens correctly, including files and records under <strong>Training and Seminars</strong> and <strong>Work Experience</strong>. If anything is missing, incomplete, or cannot be opened, please upload or update it again.
                </div>
                <div class="upload-reminder-shared">
                    <strong>Do you have two or more applications?</strong><br>
                    Merge the documents needed for the same upload field into one PDF before uploading. Files automatically sync across all your applications, so replacing a file in one application also replaces it in the others.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-confirm-reminder" data-dismiss="modal">
                    <i class="mdi mdi-check-circle-outline mr-1" aria-hidden="true"></i>
                    I Understand, Continue
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    if (window.jQuery && window.jQuery.fn && window.jQuery.fn.modal) {
        window.jQuery('#applicantUploadReminderModal').modal({
            backdrop: 'static',
            keyboard: false,
            show: true
        });
    }
});
</script>
