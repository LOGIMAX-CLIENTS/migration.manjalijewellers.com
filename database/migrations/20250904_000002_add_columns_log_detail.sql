-- Migration: Add columns to `log_detail`
-- Source: database_queries.txt
-- Safe: Each column checked via INFORMATION_SCHEMA before adding

-- UP

-- form_data (database_queries.txt:373 by Karthikai Kumaran T)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'log_detail' AND COLUMN_NAME = 'form_data');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `log_detail` ADD `form_data` LONGTEXT NULL DEFAULT NULL AFTER `event_through`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
