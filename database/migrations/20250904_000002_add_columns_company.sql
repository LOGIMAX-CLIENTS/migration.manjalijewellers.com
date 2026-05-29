-- Migration: Add columns to `company`
-- Source: database_queries.txt
-- Safe: Each column checked via INFORMATION_SCHEMA before adding

-- UP

-- whatsapp_no1 (database_queries.txt:389 by Karthikai Kumaran T)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'company' AND COLUMN_NAME = 'whatsapp_no1');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `company` ADD `whatsapp_no1` VARCHAR(20) NULL DEFAULT NULL COMMENT \'For alternate whatsapp number\' AFTER `whatsapp_no`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
