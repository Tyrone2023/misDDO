-- ---------------------------------------------------------------------------
-- Per-criterion remarks for the rating screens.
--
-- hris_applications already carried educ_remarks, training_remarks and
-- experience_remarks - the three criteria that had an "Applicants QS" picker.
-- Performance, Outstanding Accomplishments, Application of Education and
-- Application of Learning and Development now have one too, so they need
-- somewhere to keep the evaluator's note as well.
--
-- NULL-able unlike the three older columns: those are NOT NULL with no default,
-- which makes every INSERT that omits them fail under STRICT_TRANS_TABLES.
--
-- Also created on demand by Pages::ensure_rating_remark_columns(), so running
-- this file is optional - it is here for a clean/manual install.
--
-- Run against: depedddomis_db
--   /Applications/XAMPP/bin/mysql -h127.0.0.1 -uroot -p depedddomis_db < hris_applications_rating_remarks.sql
-- ---------------------------------------------------------------------------

ALTER TABLE `hris_applications`
    ADD COLUMN `performance_remarks` TEXT NULL DEFAULT NULL,
    ADD COLUMN `oa_remarks`          TEXT NULL DEFAULT NULL,
    ADD COLUMN `ae_remarks`          TEXT NULL DEFAULT NULL,
    ADD COLUMN `ald_remarks`         TEXT NULL DEFAULT NULL;

-- Rollback:
-- ALTER TABLE `hris_applications`
--     DROP COLUMN `performance_remarks`,
--     DROP COLUMN `oa_remarks`,
--     DROP COLUMN `ae_remarks`,
--     DROP COLUMN `ald_remarks`;
