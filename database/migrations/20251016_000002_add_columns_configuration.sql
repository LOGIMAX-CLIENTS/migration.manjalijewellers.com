-- Migration: Add columns to `configuration`
-- Source: database_queries.txt
-- Safe: Each column checked via INFORMATION_SCHEMA before adding

-- UP

-- show_language (database_queries.txt:458 by Karthikai Kumaran)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'configuration' AND COLUMN_NAME = 'show_language');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `configuration` ADD `show_language` TINYINT NOT NULL DEFAULT \'0\' COMMENT \'For app to show the language option or not\' AFTER `new_ios_version`, ADD `show_close` TINYINT NOT NULL DEFAULT \'0\' COMMENT \'For app to show the closed accounts or not\' AFTER `show_language`, ADD `show_wgt_history` TINYINT NOT NULL DEFAULT \'0\' COMMENT \'For app to show the paid weights history\' AFTER `show_close`, ADD `show_amt_history` TINYINT NOT NULL DEFAULT \'0\' COMMENT \'For app to show the paid amounts history\' AFTER `show_wgt_history`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
