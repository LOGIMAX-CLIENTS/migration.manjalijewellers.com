-- Migration: Add columns to `ret_karigar_metal_issue`
-- Source: database_queries.txt
-- Safe: Each column checked via INFORMATION_SCHEMA before adding

-- UP

-- fin_year_code (database_queries.txt:405 by Jothish)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ret_karigar_metal_issue' AND COLUMN_NAME = 'fin_year_code');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `ret_karigar_metal_issue` ADD `fin_year_code` INT NOT NULL AFTER `met_issue_id`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
