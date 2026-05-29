-- Migration: Add columns to `ret_estimation_items`
-- Source: database_queries.sql
-- Safe: Each column checked via INFORMATION_SCHEMA before adding

-- UP

-- wast_slab_value (database_queries.sql:41 by unknown)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ret_estimation_items' AND COLUMN_NAME = 'wast_slab_value');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `ret_estimation_items` ADD `wast_slab_value` DECIMAL(10,2) NULL DEFAULT NULL AFTER `wastage_slab_id`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- tag_blk_disc (database_queries.sql:43 by unknown)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ret_estimation_items' AND COLUMN_NAME = 'tag_blk_disc');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `ret_estimation_items` ADD `tag_blk_disc` TINYINT(5) NOT NULL DEFAULT \'0\' COMMENT \'0 -> Bulk disc is not applied\r\n1 -> Bulk disc applied.\' AFTER `wastage_slab_id`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- is_split_row (database_queries.sql:49 by unknown)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ret_estimation_items' AND COLUMN_NAME = 'is_split_row');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `ret_estimation_items` ADD `is_split_row` INT(11) NOT NULL DEFAULT \'0\' COMMENT \'0=> normal 1=> is_split_row\' AFTER `isTagsplitted`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- stone_amount (database_queries.sql:85 by unknown)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ret_estimation_items' AND COLUMN_NAME = 'stone_amount');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `ret_estimation_items` ADD `stone_amount` DECIMAL(10,2) NOT NULL DEFAULT \'0.00\' AFTER `act_wast_per`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- diamond_amount (database_queries.sql:87 by unknown)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ret_estimation_items' AND COLUMN_NAME = 'diamond_amount');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `ret_estimation_items` ADD `diamond_amount` DECIMAL(10,2) NOT NULL DEFAULT \'0.00\' AFTER `stone_amount`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- act_wast_per (database_queries.sql:91 by unknown)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ret_estimation_items' AND COLUMN_NAME = 'act_wast_per');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `ret_estimation_items` ADD `act_wast_per` INT(5) NOT NULL DEFAULT \'0\' AFTER `tag_blk_disc`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- wastage_slab_id (database_queries.sql:104 by unknown)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ret_estimation_items' AND COLUMN_NAME = 'wastage_slab_id');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `ret_estimation_items` ADD `wastage_slab_id` INT(11) NULL DEFAULT NULL AFTER `round_off_amount`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- max_mc (database_queries.sql:158 by NAMBI MUTHU RAJA)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ret_estimation_items' AND COLUMN_NAME = 'max_mc');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `ret_estimation_items` ADD `max_mc` INT(10) NULL DEFAULT NULL AFTER `wastage_percent`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- max_VA (database_queries.sql:161 by NAMBI MUTHU RAJA)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ret_estimation_items' AND COLUMN_NAME = 'max_VA');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `ret_estimation_items` ADD `max_VA` INT(10) NULL DEFAULT NULL AFTER `wastage_percent`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- max_va_per (database_queries.sql:164 by NAMBI MUTHU RAJA)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ret_estimation_items' AND COLUMN_NAME = 'max_va_per');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `ret_estimation_items` ADD `max_va_per` INT(10) NULL DEFAULT NULL AFTER `wastage_percent`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
