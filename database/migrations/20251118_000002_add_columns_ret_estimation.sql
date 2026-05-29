-- Migration: Add columns to `ret_estimation`
-- Source: database_queries.sql
-- Safe: Each column checked via INFORMATION_SCHEMA before adding

-- UP

-- bulk_was_disc_per (database_queries.sql:45 by unknown)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ret_estimation' AND COLUMN_NAME = 'bulk_was_disc_per');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `ret_estimation` ADD `bulk_was_disc_per` DECIMAL(10,2) NOT NULL DEFAULT \'0.00\' COMMENT \'Bulk tag wastage discount perc.\' AFTER `disc_per`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- manual_rate (database_queries.sql:63 by unknown)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ret_estimation' AND COLUMN_NAME = 'manual_rate');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `ret_estimation` ADD `manual_rate` TINYINT(2) NOT NULL DEFAULT \'0\' COMMENT \'0 -> unchecked 1 -> checked\' AFTER `bulk_was_disc_per`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- disc_per (database_queries.sql:96 by unknown)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ret_estimation' AND COLUMN_NAME = 'disc_per');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `ret_estimation` ADD `disc_per` DECIMAL(10,2) NOT NULL DEFAULT \'0\' COMMENT \'Stone discount percentage\' AFTER `handling_charges`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- goldrate_14ct (database_queries.sql:1409 by DEVADHARSHINI)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ret_estimation' AND COLUMN_NAME = 'goldrate_14ct');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `ret_estimation` ADD `goldrate_14ct` DECIMAL(10,2) NULL DEFAULT NULL AFTER `silverrate_1gm`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- goldrate_9ct (database_queries.sql:1417 by DEVADHARSHINI)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ret_estimation' AND COLUMN_NAME = 'goldrate_9ct');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `ret_estimation` ADD `goldrate_9ct` DECIMAL(10,2) NULL DEFAULT NULL AFTER `silverrate_1gm`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
