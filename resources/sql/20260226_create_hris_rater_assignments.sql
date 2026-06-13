-- Run this on the MIS database before using AssignRater.
-- Tracks which applicants (per application) are assigned to which evaluator.

CREATE TABLE IF NOT EXISTS `hris_rater_assignments` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `fy` int NOT NULL,
  `applicant_id` varchar(64) NOT NULL,
  `app_id` int unsigned DEFAULT NULL,
  `job_id` int unsigned NOT NULL,
  `job_type` tinyint NOT NULL,
  `specialization` varchar(255) DEFAULT NULL,
  `rater_user_id` int unsigned NOT NULL,
  `assigned_by` int unsigned DEFAULT NULL,
  `assigned_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_app_year` (`fy`,`app_id`),
  KEY `idx_rater_assign_rater` (`rater_user_id`),
  KEY `idx_rater_assign_job_type` (`job_type`),
  KEY `idx_rater_assign_spec` (`specialization`),
  CONSTRAINT `fk_rater_assign_user` FOREIGN KEY (`rater_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_rater_assign_job` FOREIGN KEY (`job_id`) REFERENCES `hris_jobvacancy` (`jobID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
