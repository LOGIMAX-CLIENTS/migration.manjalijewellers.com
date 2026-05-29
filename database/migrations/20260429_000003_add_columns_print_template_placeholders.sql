-- Migration: Add V2 columns to print_template_placeholders
-- Module: Print Template Designer — V2 field picker metadata
-- Author: Antigravity
-- Safe: Uses IF NOT EXISTS column check
-- Note: Columns already included in 000002 CREATE TABLE. This is a no-op safety net
--       for installations that may have created the table without these columns.

-- UP
-- field_type column
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'print_template_placeholders' AND COLUMN_NAME = 'field_type');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `print_template_placeholders` ADD COLUMN `field_type` VARCHAR(20) DEFAULT ''text'' COMMENT ''text, number, date, image, boolean''', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- is_loop_field column
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'print_template_placeholders' AND COLUMN_NAME = 'is_loop_field');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `print_template_placeholders` ADD COLUMN `is_loop_field` TINYINT(1) DEFAULT 0 COMMENT ''1 = belongs to a loop/table array''', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- parent_loop column
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'print_template_placeholders' AND COLUMN_NAME = 'parent_loop');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `print_template_placeholders` ADD COLUMN `parent_loop` VARCHAR(100) DEFAULT NULL COMMENT ''Parent loop key e.g. items, purchase_items''', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
