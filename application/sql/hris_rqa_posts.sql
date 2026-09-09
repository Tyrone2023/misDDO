-- Applicant-facing CAR/RQA publications.
--
-- HR posts the report currently open in the browser with a short caption. The
-- post then appears as a clickable announcement only to applicants who applied
-- for that vacancy. One report is active per vacancy; posting again replaces
-- its caption/link, and unpublishing retains the row for later reuse.
--
-- Safe to run more than once. The application also creates this table on first
-- use through Common::ensure_rqa_posts_table().

CREATE TABLE IF NOT EXISTS `hris_rqa_posts` (
    `jobID` INT UNSIGNED NOT NULL,
    `caption` VARCHAR(500) NOT NULL,
    `report_uri` VARCHAR(1000) NOT NULL,
    `posted_by` VARCHAR(150) NULL DEFAULT NULL,
    `posted_at` DATETIME NULL DEFAULT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `updated_at` DATETIME NULL DEFAULT NULL,
    PRIMARY KEY (`jobID`),
    KEY `idx_rqa_posts_active` (`is_active`, `posted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
