-- Migration: Add columns to `scheme`
-- Source: database_queries.sql, database_queries.txt
-- Safe: Each column checked via INFORMATION_SCHEMA before adding

-- UP

-- rate_type (database_queries.sql:151 by NAMBI MUTHU RAJA)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'scheme' AND COLUMN_NAME = 'rate_type');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `scheme` ADD `rate_type` INT(11) NOT NULL DEFAULT \'1\' COMMENT \'1 - Board Rate, 2 - Avg rate per gram\' AFTER `store_closing_balance`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- digi_target (database_queries.txt:252 by Abinaya M)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'scheme' AND COLUMN_NAME = 'digi_target');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `scheme` ADD `digi_target` TINYINT(1) NOT NULL DEFAULT \'0\' COMMENT \'0 - set target disabled, 1- set target enabled\' AFTER `total_days_to_pay`, ADD `digi_target_split_unit` TINYINT(1) NOT NULL DEFAULT \'0\' COMMENT \'1- days, 2- week, 3- month\' AFTER `digi_target`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- preclose_type (database_queries.txt:524 by NAMBI MUTHU RAJA)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'scheme' AND COLUMN_NAME = 'preclose_type');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `scheme` ADD `preclose_type` TINYINT(1) NOT NULL DEFAULT \'1\' COMMENT \'0 -> Days , 1-> Installments\' AFTER `allow_preclose`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
