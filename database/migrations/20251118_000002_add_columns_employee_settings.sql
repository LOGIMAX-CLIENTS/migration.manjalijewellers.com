-- Migration: Add columns to `employee_settings`
-- Source: database_queries.sql
-- Safe: Each column checked via INFORMATION_SCHEMA before adding

-- UP

-- bulk_wast_disc_limit (database_queries.sql:53 by unknown)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'employee_settings' AND COLUMN_NAME = 'bulk_wast_disc_limit');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `employee_settings` ADD `bulk_wast_disc_limit` DECIMAL(10,2) NOT NULL DEFAULT \'0.00\'', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- dia_disc_limit (database_queries.sql:55 by unknown)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'employee_settings' AND COLUMN_NAME = 'dia_disc_limit');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `employee_settings` ADD `dia_disc_limit` DECIMAL(10,2) NOT NULL DEFAULT \'0.00\' AFTER `bulk_wast_disc_limit`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
