-- Trainings are encoded with a start and end time, so the two date columns
-- have to carry a time component.
--
-- The application applies this itself (Reg::ensure_training_datetime, called
-- from the registered profile), so this file is only for running the change
-- ahead of a deployment or on a database the app has not opened yet. It is
-- safe to run twice.
--
-- Legacy rows hold '0000-00-00', which strict mode refuses to convert, so the
-- zero-date checks are lifted for the conversion and those rows are then
-- stored as NULL - the profile already renders a blank for them.

SET @mis_sql_mode = @@SESSION.sql_mode;
SET SESSION sql_mode = REPLACE(REPLACE(@@SESSION.sql_mode, 'NO_ZERO_DATE', ''), 'NO_ZERO_IN_DATE', '');

ALTER TABLE `hris_trainings`
    MODIFY `dateStarted`  DATETIME NULL DEFAULT NULL,
    MODIFY `dateFinished` DATETIME NULL DEFAULT NULL;

UPDATE `hris_trainings` SET `dateStarted`  = NULL WHERE `dateStarted`  = '0000-00-00 00:00:00';
UPDATE `hris_trainings` SET `dateFinished` = NULL WHERE `dateFinished` = '0000-00-00 00:00:00';

SET SESSION sql_mode = @mis_sql_mode;
