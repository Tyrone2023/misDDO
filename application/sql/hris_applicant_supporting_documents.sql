-- WES, degree CAV, and PRC/board-rating uploads for applicant rating pages.
-- Safe to run repeatedly; Pages::ma() also adds missing columns on demand.

SET @ddl = (
    SELECT IF(COUNT(*) = 0,
        'ALTER TABLE `hris_applicant` ADD COLUMN `wes_file` VARCHAR(255) NULL DEFAULT NULL',
        'DO 0')
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'hris_applicant' AND COLUMN_NAME = 'wes_file'
);
PREPARE stmt FROM @ddl; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @ddl = (
    SELECT IF(COUNT(*) = 0,
        'ALTER TABLE `hris_applicant` ADD COLUMN `bachelor_cav` VARCHAR(255) NULL DEFAULT NULL',
        'DO 0')
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'hris_applicant' AND COLUMN_NAME = 'bachelor_cav'
);
PREPARE stmt FROM @ddl; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @ddl = (
    SELECT IF(COUNT(*) = 0,
        'ALTER TABLE `hris_applicant` ADD COLUMN `master_cav` VARCHAR(255) NULL DEFAULT NULL',
        'DO 0')
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'hris_applicant' AND COLUMN_NAME = 'master_cav'
);
PREPARE stmt FROM @ddl; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @ddl = (
    SELECT IF(COUNT(*) = 0,
        'ALTER TABLE `hris_applicant` ADD COLUMN `doctor_cav` VARCHAR(255) NULL DEFAULT NULL',
        'DO 0')
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'hris_applicant' AND COLUMN_NAME = 'doctor_cav'
);
PREPARE stmt FROM @ddl; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @ddl = (
    SELECT IF(COUNT(*) = 0,
        'ALTER TABLE `hris_applicant` ADD COLUMN `prc_license` VARCHAR(255) NULL DEFAULT NULL',
        'DO 0')
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'hris_applicant' AND COLUMN_NAME = 'prc_license'
);
PREPARE stmt FROM @ddl; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @ddl = (
    SELECT IF(COUNT(*) = 0,
        'ALTER TABLE `hris_applicant` ADD COLUMN `board_rating` VARCHAR(255) NULL DEFAULT NULL',
        'DO 0')
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'hris_applicant' AND COLUMN_NAME = 'board_rating'
);
PREPARE stmt FROM @ddl; EXECUTE stmt; DEALLOCATE PREPARE stmt;
