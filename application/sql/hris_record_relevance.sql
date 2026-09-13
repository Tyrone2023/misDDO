-- Vacancy-specific relevance for applicant trainings and work experience.
-- Runtime installation is also guarded by Reg::ensure_record_relevance_table().

CREATE TABLE IF NOT EXISTS `hris_record_relevance` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `record_type` varchar(20) NOT NULL,
  `record_id` int unsigned NOT NULL,
  `applicant_id` int unsigned NOT NULL,
  `job_id` int unsigned NOT NULL,
  `stat` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 No Action, 1 Relevant, 2 Not Relevant',
  `updated_by` int unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_record_job` (`record_type`, `record_id`, `job_id`),
  KEY `idx_applicant_job` (`applicant_id`, `job_id`),
  KEY `idx_job` (`job_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Early versions copied the old global stat into every open vacancy and left
-- updated_by NULL. Those inherited marks must not count as a vacancy choice;
-- only a selection explicitly saved by an evaluator is vacancy relevance.
UPDATE `hris_record_relevance`
SET `stat` = 0
WHERE `updated_by` IS NULL;
