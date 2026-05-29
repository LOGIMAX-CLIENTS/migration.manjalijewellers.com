-- Migration: Add columns to `ret_billing`
-- Source: database_queries.sql
-- Safe: Each column checked via INFORMATION_SCHEMA before adding

-- UP

-- irn_date (database_queries.sql:145 by NAMBI MUTHU RAJA)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ret_billing' AND COLUMN_NAME = 'irn_date');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `ret_billing` ADD `irn_date` DATE DEFAULT NULL', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- goldrate_14ct (database_queries.sql:1407 by DEVADHARSHINI)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ret_billing' AND COLUMN_NAME = 'goldrate_14ct');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `ret_billing` ADD `goldrate_14ct` DECIMAL(10,2) NULL DEFAULT NULL AFTER `silverrate_1gm`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- goldrate_9ct (database_queries.sql:1415 by DEVADHARSHINI)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ret_billing' AND COLUMN_NAME = 'goldrate_9ct');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `ret_billing` ADD `goldrate_9ct` DECIMAL(10,2) NULL DEFAULT NULL AFTER `silverrate_1gm`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
