-- Migration: Alter ret_taging for Partly Sale Support
-- Feature: Partly Sale — adds columns to track child tag lineage
-- Used by: ret_tag_parts_model.php (save_parts, revert_parts)
-- Safe: Columns checked via INFORMATION_SCHEMA before adding

-- UP

-- is_parts_child — 1 if this tag was created by a partly sale
SET @col1 = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ret_taging' AND COLUMN_NAME = 'is_parts_child');
SET @sql1 = IF(@col1 = 0, 'ALTER TABLE `ret_taging` ADD `is_parts_child` TINYINT(1) NOT NULL DEFAULT 0 COMMENT "1=Created by partly sale" AFTER `tag_status`', 'SELECT 1');
PREPARE stmt1 FROM @sql1;
EXECUTE stmt1;
DEALLOCATE PREPARE stmt1;

-- parts_base_id — FK to base tag_id
SET @col2 = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ret_taging' AND COLUMN_NAME = 'parts_base_id');
SET @sql2 = IF(@col2 = 0, 'ALTER TABLE `ret_taging` ADD `parts_base_id` INT(11) DEFAULT NULL COMMENT "FK ret_taging.tag_id of the base tag" AFTER `is_parts_child`', 'SELECT 1');
PREPARE stmt2 FROM @sql2;
EXECUTE stmt2;
DEALLOCATE PREPARE stmt2;

-- parts_id — FK to ret_tag_parts_master.parts_id
SET @col3 = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ret_taging' AND COLUMN_NAME = 'parts_id');
SET @sql3 = IF(@col3 = 0, 'ALTER TABLE `ret_taging` ADD `parts_id` INT(11) DEFAULT NULL COMMENT "FK ret_tag_parts_master.parts_id" AFTER `parts_base_id`', 'SELECT 1');
PREPARE stmt3 FROM @sql3;
EXECUTE stmt3;
DEALLOCATE PREPARE stmt3;

-- Index on parts_base_id for revert lookups
SET @idx1 = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ret_taging' AND INDEX_NAME = 'idx_parts_base');
SET @sql4 = IF(@idx1 = 0, 'ALTER TABLE `ret_taging` ADD KEY `idx_parts_base` (`parts_base_id`)', 'SELECT 1');
PREPARE stmt4 FROM @sql4;
EXECUTE stmt4;
DEALLOCATE PREPARE stmt4;

-- Index on parts_id for join lookups
SET @idx2 = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ret_taging' AND INDEX_NAME = 'idx_parts_id');
SET @sql5 = IF(@idx2 = 0, 'ALTER TABLE `ret_taging` ADD KEY `idx_parts_id` (`parts_id`)', 'SELECT 1');
PREPARE stmt5 FROM @sql5;
EXECUTE stmt5;
DEALLOCATE PREPARE stmt5;

-- tag_status values documentation (no schema change):
-- 0  = On Sale (Available)
-- 1  = Sold
-- 2  = Deleted / Cancelled
-- 15 = Partly Sale (base frozen)
-- 18 = Admin Modified (weight adjustment on part child)

-- DOWN
-- ALTER TABLE `ret_taging` DROP KEY `idx_parts_id`;
-- ALTER TABLE `ret_taging` DROP KEY `idx_parts_base`;
-- ALTER TABLE `ret_taging` DROP COLUMN `parts_id`;
-- ALTER TABLE `ret_taging` DROP COLUMN `parts_base_id`;
-- ALTER TABLE `ret_taging` DROP COLUMN `is_parts_child`;
