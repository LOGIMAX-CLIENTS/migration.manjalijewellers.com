-- Migration: Add columns to `ret_estimation_item_stones`
-- Source: database_queries.sql
-- Safe: Each column checked via INFORMATION_SCHEMA before adding

-- UP

-- max_stn_amt (database_queries.sql:100 by unknown)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ret_estimation_item_stones' AND COLUMN_NAME = 'max_stn_amt');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `ret_estimation_item_stones` ADD `max_stn_amt` DECIMAL(10,2) NULL DEFAULT NULL COMMENT \'Stone amount before stone discount applied.\' AFTER `quality_id`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
