-- Announcement / remarks attached to a job vacancy posting.
--
-- HR maintains the text from the Announcement column on Page/jobVacancy; every
-- applicant who applied for that position sees it on their dashboard
-- (Pages/view_user). Blanking the text removes the announcement.
--
-- `announcement_by` / `announcement_at` are a snapshot of who last saved the
-- text and when, so the dashboard can show the source of the notice.
--
-- Safe to run more than once. The application also self-heals these columns via
-- Common::ensure_columns() the first time the recruitment or dashboard screens
-- are opened, so running this by hand is optional.

SET @ddl := (
    SELECT IF(
        COUNT(*) = 0,
        'ALTER TABLE `hris_jobvacancy` ADD COLUMN `announcement` TEXT NULL AFTER `file`',
        'DO 0'
    )
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'hris_jobvacancy'
      AND COLUMN_NAME = 'announcement'
);
PREPARE stmt FROM @ddl; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @ddl := (
    SELECT IF(
        COUNT(*) = 0,
        'ALTER TABLE `hris_jobvacancy` ADD COLUMN `announcement_by` VARCHAR(150) NULL AFTER `announcement`',
        'DO 0'
    )
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'hris_jobvacancy'
      AND COLUMN_NAME = 'announcement_by'
);
PREPARE stmt FROM @ddl; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @ddl := (
    SELECT IF(
        COUNT(*) = 0,
        'ALTER TABLE `hris_jobvacancy` ADD COLUMN `announcement_at` DATETIME NULL AFTER `announcement_by`',
        'DO 0'
    )
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'hris_jobvacancy'
      AND COLUMN_NAME = 'announcement_at'
);
PREPARE stmt FROM @ddl; EXECUTE stmt; DEALLOCATE PREPARE stmt;
