-- Retention request: granted scope + denial with reason.
-- Run once per database (already applied to the local depedddomis_db / depedddomis_db2).
--
--   granted_scope  what the reviewer actually granted:
--                    1 = all criteria
--                    2 = partial (Demo & TR for teaching, Interview & Written for non-teaching)
--                  NULL for rows granted before this change.
--   deny_reason    reason shown to the applicant when the request is denied (hris_rating_request.stat = 2).
--   adate          date the request was granted or denied.

ALTER TABLE `hris_rating_request`
  ADD COLUMN `granted_scope` TINYINT NULL DEFAULT NULL AFTER `r_type`,
  ADD COLUMN `deny_reason` TEXT NULL DEFAULT NULL AFTER `granted_scope`,
  ADD COLUMN `adate` VARCHAR(45) NULL DEFAULT NULL AFTER `deny_reason`;
