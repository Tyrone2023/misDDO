-- ---------------------------------------------------------------------------
-- Per-position scoring criteria (Criteria and Point System for Hiring and
-- Promotion).
--
-- Until now the point allocation lived in `hris_position_points` as a handful
-- of shared "brackets" that every position had to be squeezed into, and the
-- increment tables lived in `score_list` keyed by `hris_positions.g_score` -
-- a column that is 0 for every row on file, so nothing was actually linked.
--
-- These two tables replace that with one criteria sheet per position title:
--
--   hris_position_criteria        - the 8 criteria and how many points each is
--                                   worth for this position (must total 100),
--                                   plus the CSC qualification standard note
--                                   printed beside each table on the form.
--   hris_position_criteria_level  - the increment levels inside a criterion
--                                   ("16-23 hours" = 4 points, and so on).
--
-- criterion codes (kept in the same order as the DepEd form):
--   0 Education                          4 Outstanding Accomplishments
--   1 Training                           5 Application of Education
--   2 Experience                         6 Application of Learning and Development
--   3 Performance                        7 Potential (Written Exam, BEI)
--
-- The tables are also created on demand by Page::ensure_position_criteria_tables(),
-- so running this file is optional - it is here for a clean/manual install.
--
-- Run against: depedddomis_db
--   /Applications/XAMPP/bin/mysql -h127.0.0.1 -uroot -p depedddomis_db < hris_position_criteria.sql
-- ---------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `hris_position_criteria` (
    `id`          INT(11)      NOT NULL AUTO_INCREMENT,
    `position_id` INT(11)      NOT NULL,
    `criterion`   TINYINT(4)   NOT NULL,
    `max_points`  DECIMAL(6,2) NOT NULL DEFAULT 0.00,
    `qs`          VARCHAR(255) DEFAULT NULL,
    `updated_by`  VARCHAR(100) DEFAULT NULL,
    `updated_at`  DATETIME     DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_position_criterion` (`position_id`, `criterion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `hris_position_criteria_level` (
    `id`              INT(11)      NOT NULL AUTO_INCREMENT,
    `position_id`     INT(11)      NOT NULL,
    `criterion`       TINYINT(4)   NOT NULL,
    `increment_level` INT(11)      NOT NULL DEFAULT 0,
    `description`     VARCHAR(255) NOT NULL,
    `points`          DECIMAL(6,2) NOT NULL DEFAULT 0.00,
    PRIMARY KEY (`id`),
    KEY `idx_position_criterion` (`position_id`, `criterion`, `increment_level`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Rollback:
-- DROP TABLE IF EXISTS `hris_position_criteria_level`;
-- DROP TABLE IF EXISTS `hris_position_criteria`;
