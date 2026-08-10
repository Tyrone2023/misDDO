-- ---------------------------------------------------------------------------
-- hris_jobvacancy.position_id is TINYINT (max 127), but hris_positions.id is
-- already at 243 and every position added through Page/positionSettings gets a
-- higher id still. With STRICT_TRANS_TABLES the insert in Reg::insert_job()
-- fails with "Out of range value for column 'position_id'", so posting a
-- vacancy for any position with id > 127 silently does nothing.
--
-- Widening the column keeps all existing values intact (TINYINT -> INT is a
-- lossless widening) and unblocks the newly added position titles.
--
-- Run against: depedddomis_db
--   /Applications/XAMPP/bin/mysql -h127.0.0.1 -uroot -p depedddomis_db < hris_jobvacancy_position_id_widen.sql
-- ---------------------------------------------------------------------------

ALTER TABLE `hris_jobvacancy`
    MODIFY COLUMN `position_id` INT NOT NULL DEFAULT 0;

-- Rollback (only safe while every stored position_id is still <= 127):
-- ALTER TABLE `hris_jobvacancy` MODIFY COLUMN `position_id` TINYINT NOT NULL;
