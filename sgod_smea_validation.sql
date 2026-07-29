-- ---------------------------------------------------------------------------
-- SMEA validation workflow (SDO / SMME review of a submitted SMEA)
--
-- A school submits its SMEA (one sgod_smea row per school + budget code). The
-- division office then either VALIDATES it or returns it FOR COMPLIANCE with a
-- remark. While the row sits at "Submitted" or "SDO Validated" the school can no
-- longer edit or add accomplishments; "For Compliance" reopens it for editing.
--
-- YOU DO NOT HAVE TO RUN THIS. SGODModel::smea_ensure_schema() applies the same
-- columns and indexes on the first SMEA page load if they are missing, so a server
-- that gets the code without the schema — or a partial dump import that drops it
-- again — repairs itself. This file is here for the case where you would rather
-- apply the change deliberately, up front, instead of on a user's first request.
--
-- Apply with:
--   /Applications/XAMPP/bin/mysql -h 127.0.0.1 -u root -p depedddomis_db < sgod_smea_validation.sql
-- ---------------------------------------------------------------------------

ALTER TABLE `sgod_smea`
    ADD COLUMN `status`         VARCHAR(30)  NOT NULL DEFAULT 'Submitted' AFTER `remarks`,
    ADD COLUMN `sdo_remarks`    TEXT         NULL     AFTER `status`,
    ADD COLUMN `validated_by`   VARCHAR(150) NULL     AFTER `sdo_remarks`,
    ADD COLUMN `date_validated` DATETIME     NULL     AFTER `validated_by`;

-- Everything already in the table was a plain submission.
UPDATE `sgod_smea` SET `status` = 'Submitted' WHERE `status` = '' OR `status` IS NULL;

-- The admin roll-ups filter on school_id + fy; the drill-down joins schools to
-- sgod_smea on those two columns.
ALTER TABLE `sgod_smea` ADD INDEX `idx_school_fy` (`school_id`, `fy`);

-- ---------------------------------------------------------------------------
-- The index behind the "sometimes it delays and renders unstyled" report.
--
-- Every page renders templates/header.php, which lists the unread sgod_aip_track
-- notifications and looked the commenter up in `users` once per row. With ~1,100
-- unread rows and no index on users.username that is ~1,100 full scans of a 4,600
-- row table: measured at 3.85s of pure database time before a single byte of the
-- page reaches the browser. The N+1 itself is fixed in the template; this index
-- removes the rest of the cost and helps every other lookup by username.
--
-- Deliberately NOT indexed: sgod_aip_track. An index on (notify, res) does make
-- its own query ~10ms faster, but that query has no ORDER BY, so the index also
-- changes the order the rows come back in — the notification dropdown silently
-- reordered itself from oldest-first to grouped-by-school. Not worth 10ms.
-- ---------------------------------------------------------------------------

ALTER TABLE `users` ADD INDEX `idx_username` (`username`);

-- The SMEA report itself: smea_generate.php looks a target row up per activity per
-- type (~140 lookups on a 57,000 row table with no index but the primary key), which
-- measured 6.7s for one school. sgod_sop has duplicate (aip_id, type) rows, so this
-- index could in principle change which duplicate ->row() returns; it does not —
-- InnoDB appends the primary key to a secondary index, so an equal (aip_id, type)
-- group still comes back in id order, exactly as the previous full scan did. Verified
-- against 300 duplicate groups before and after: 0 changed. Report time 6.7s -> 0.15s.
ALTER TABLE `sgod_sop` ADD INDEX `idx_aip_type` (`aip_id`, `type`);

-- ---------------------------------------------------------------------------
-- NOT APPLIED — needs a decision, see the notes handed over with this change.
--
-- Page/smea_summary still takes ~10s. Its per-pillar, per-quarter queries join
-- sgod_aip to sgod_sop on a.id = b.aip_id, but sgod_aip.id is INT UNSIGNED while
-- sgod_sop.aip_id is VARCHAR(200). MySQL therefore compares them numerically and
-- cannot use any index on aip_id: every one of the ~120 joins the page fires scans
-- all 57,000 sgod_sop rows (~45ms each).
--
-- Aligning the column type fixes it. All 57,000 existing aip_id values are plain
-- integers (checked), so the conversion is lossless — but it is a type change on a
-- live column with 21 call sites, so it is left here rather than applied blind:
--
--   ALTER TABLE `sgod_sop` MODIFY `aip_id` INT UNSIGNED NULL;
-- ---------------------------------------------------------------------------
