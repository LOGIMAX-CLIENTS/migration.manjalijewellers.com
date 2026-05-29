-- Migration: Add columns to `profile`
-- Source: database_queries.sql, database_queries.txt
-- Safe: Each column checked via INFORMATION_SCHEMA before adding

-- UP

-- allow_other_issue (database_queries.sql:37 by unknown)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'profile' AND COLUMN_NAME = 'allow_other_issue');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `profile` ADD `allow_other_issue` TINYINT(5) NOT NULL DEFAULT \'0\' COMMENT \'0 -> No 1-> Yes \' AFTER `metal_rate_datelimit`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- wedding_wastage_slab (database_queries.sql:39 by unknown)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'profile' AND COLUMN_NAME = 'wedding_wastage_slab');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `profile` ADD `wedding_wastage_slab` TINYINT(1) NOT NULL DEFAULT \'0\' COMMENT \'0 -> No, 1 -> Yes\' AFTER `metalrate_edit`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- metalrate_edit (database_queries.txt:317 by Esakkiraja. N)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'profile' AND COLUMN_NAME = 'metalrate_edit');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE profile ADD metalrate_edit TINYINT(5) NOT NULL DEFAULT \'0\' AFTER order_delievery_otp, ADD metal_rate_datelimit INT(11) NULL DEFAULT \'1\' AFTER metalrate_edit', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
