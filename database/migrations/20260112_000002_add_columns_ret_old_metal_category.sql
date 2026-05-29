-- Migration: Add columns to `ret_old_metal_category`
-- Source: database_queries.sql
-- Safe: Each column checked via INFORMATION_SCHEMA before adding

-- UP

-- is_active (database_queries.sql:5257 by DEVADHARSHINI)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ret_old_metal_category' AND COLUMN_NAME = 'is_active');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `ret_old_metal_category` ADD `is_active` INT NULL COMMENT \'1 -> Active, 0 -> InActive\' AFTER `created_by`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
