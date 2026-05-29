-- Migration: Add columns to `ret_taging`
-- Source: database_queries.sql
-- Safe: Each column checked via INFORMATION_SCHEMA before adding

-- UP

-- purchase_touch (database_queries.sql:5262 by DEVADHARSHINI)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ret_taging' AND COLUMN_NAME = 'purchase_touch');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `ret_taging` ADD `purchase_touch` DECIMAL(10,2) NULL DEFAULT NULL AFTER `net_wt`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
