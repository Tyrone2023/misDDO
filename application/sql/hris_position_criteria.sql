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
--   hris_position_criteria        - the criteria this title is rated on and how
--                                   many points each is worth (must total 100).
--   hris_position_criteria_level  - the increment levels inside a criterion
--                                   ("16-23 hours" = 4 points, and so on).
--
-- The criteria list is not fixed: it is named, added to and cut down per
-- position, because not every title is rated on the same things. `criterion` is
-- simply the row's place in the sheet (0, 1, 2 ...) and is what the level table
-- joins on; a save rewrites the whole sheet, so the numbering is rebuilt each
-- time from the order on screen.
--
-- A position with no rows here has never been set up and falls back to the
-- standard DepEd breakdown (Education 5, Training 10, Experience 15,
-- Performance 20, Outstanding Accomplishments 10, Application of Education 10,
-- Application of Learning and Development 10, Potential 20).
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
    `label`       VARCHAR(150) NOT NULL DEFAULT '',
    `max_points`  DECIMAL(6,2) NOT NULL DEFAULT 0.00,
    `updated_by`  VARCHAR(100) DEFAULT NULL,
    `updated_at`  DATETIME     DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_position_criterion` (`position_id`, `criterion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- for an install that already created the table before criteria carried their
-- own name (also handled automatically by ensure_position_criteria_tables())
-- ALTER TABLE `hris_position_criteria`
--     ADD COLUMN `label` VARCHAR(150) NOT NULL DEFAULT '' AFTER `criterion`,
--     DROP COLUMN `qs`;

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
