-- Table that stores RQA recommendations made from the
-- Pages/rqa_recommendation report and approved/issued from Pages/rqa_approval.
--
-- The application also auto-creates this table (and back-fills new columns)
-- on first visit, so running this file manually is optional. An Item Number
-- may only belong to one ACTIVE applicant at a time, but a WAIVED post keeps
-- its record (so the waiver can be undone) while freeing its Item Number for
-- reuse. That "one active per Item Number" rule is enforced in PHP
-- (rqa_recommend_save), so `item_number` carries a plain index, not a UNIQUE
-- key. Older installs that still have `uniq_item_number` have it dropped
-- automatically on first visit.

CREATE TABLE IF NOT EXISTS `hris_rqa_recommendation` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `jobID` INT(11) NOT NULL,
  `appID` INT(11) DEFAULT NULL,
  `empEmail` VARCHAR(150) DEFAULT NULL,
  `record_no` VARCHAR(100) DEFAULT NULL,
  `applicant_name` VARCHAR(255) DEFAULT NULL,
  `item_number` VARCHAR(100) NOT NULL,
  `remarks` TEXT DEFAULT NULL,
  `total_points` DECIMAL(10,2) DEFAULT NULL,
  `school_id` INT(11) DEFAULT NULL,            -- schools.recID
  `school_name` VARCHAR(180) DEFAULT NULL,     -- denormalised for display
  `status` VARCHAR(20) NOT NULL DEFAULT 'recommended', -- recommended | approved | waived
  `recommended_by` INT(11) DEFAULT NULL,
  `created_at` DATETIME DEFAULT NULL,
  `approved_by` INT(11) DEFAULT NULL,
  `approved_at` DATETIME DEFAULT NULL,
  `date_hired` DATE DEFAULT NULL,              -- set on the List of Issuance
  `date_waived` DATE DEFAULT NULL,             -- set when the applicant waives the post
  PRIMARY KEY (`id`),
  KEY `idx_item_number` (`item_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
