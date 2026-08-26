-- Eligibility rating captured alongside the applicant's eligibility type.
-- Safe to run repeatedly: the ALTER executes only when the column is missing.

SET @eligibility_rating_column_exists = (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'hris_applicant'
      AND COLUMN_NAME = 'csEligibilityRating'
);

SET @eligibility_rating_sql = IF(
    @eligibility_rating_column_exists = 0,
    'ALTER TABLE `hris_applicant` ADD COLUMN `csEligibilityRating` VARCHAR(45) NULL AFTER `csEligibility`',
    'SELECT ''hris_applicant.csEligibilityRating already exists'' AS message'
);

PREPARE eligibility_rating_statement FROM @eligibility_rating_sql;
EXECUTE eligibility_rating_statement;
DEALLOCATE PREPARE eligibility_rating_statement;
