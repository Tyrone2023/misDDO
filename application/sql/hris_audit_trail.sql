-- Detailed audit trail for the recruitment / rating workflow.
--
-- One row per meaningful action: applicants submitting/updating/deleting
-- documents, validators marking Qualified/Disqualified, status changes
-- (endorsement, etc.) and every rating that is encoded (education, training,
-- experience, LET, demo, teacher's reflection, and the non-teaching / promotion
-- components). Actor identity (username, first/last name, position) is captured
-- as a snapshot so the log stays readable even if the user record later changes.
--
-- Safe to run more than once.

CREATE TABLE IF NOT EXISTS `hris_audit_trail` (
    `id`           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `created_at`   DATETIME        NOT NULL,
    `user_id`      INT                      DEFAULT NULL,  -- users.id of the actor
    `username`     VARCHAR(100)             DEFAULT NULL,  -- snapshot of users.username
    `fname`        VARCHAR(200)             DEFAULT NULL,  -- snapshot of users.fname
    `lname`        VARCHAR(200)             DEFAULT NULL,  -- snapshot of users.lname
    `position`     VARCHAR(100)             DEFAULT NULL,  -- snapshot of users.position (role)
    `action`       VARCHAR(64)     NOT NULL,               -- submit_application, upload_document, update_document, delete_document, validate, disqualify, status_change, rate, ...
    `entity_type`  VARCHAR(64)              DEFAULT NULL,  -- application, document, rating, ...
    `entity_id`    VARCHAR(191)             DEFAULT NULL,  -- id/key of the affected row (rating id, document column, ...)
    `app_id`       INT                      DEFAULT NULL,  -- hris_applications.appID
    `applicant_id` VARCHAR(191)             DEFAULT NULL,  -- applicant id / record_no
    `job_id`       INT                      DEFAULT NULL,  -- hris_jobvacancy.jobID
    `field`        VARCHAR(100)             DEFAULT NULL,  -- rating component / document column (drives "who rated this part")
    `description`  VARCHAR(500)             DEFAULT NULL,  -- human readable summary (includes the value that changed)
    PRIMARY KEY (`id`),
    KEY `idx_app`        (`app_id`),
    KEY `idx_app_field`  (`app_id`, `field`),
    KEY `idx_action`     (`action`),
    KEY `idx_applicant`  (`applicant_id`),
    KEY `idx_job`        (`job_id`),
    KEY `idx_user`       (`user_id`),
    KEY `idx_created`    (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
