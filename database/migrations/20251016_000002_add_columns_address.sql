-- Migration: Add columns to `address`
-- Source: database_queries.txt
-- Safe: Each column checked via INFORMATION_SCHEMA before adding

-- UP

-- added_by (database_queries.txt:465 by Karthikai Kumaran)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'address' AND COLUMN_NAME = 'added_by');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `address` ADD `added_by` INT(2) NULL DEFAULT NULL COMMENT \'0 - WebApp , 1 - admin, 2 - MobileApp, 3 - Collection app, 4 - Retail App, 5- Sync, 6-Import 7-Retail admin\' AFTER `company_name`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
