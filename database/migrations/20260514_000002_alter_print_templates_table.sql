-- Migration: Alter print_templates table to match final production schema
-- Module: Print Template Designer
-- Author: Antigravity
-- Date: 2026-05-14
-- Reason: Upgrades print_templates structure from the initial version in 000001 to the final production version.
--         This ensures that existing installations are compatible with the latest GrapesJS-based designer.

-- UP
SET @db_name = DATABASE();

-- 1. Rename id_print_template to id_template (if exists)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'print_templates' AND COLUMN_NAME = 'id_print_template');
SET @sql = IF(@col_exists > 0, 'ALTER TABLE `print_templates` CHANGE `id_print_template` `id_template` INT(11) NOT NULL AUTO_INCREMENT', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 2. Modify template_code length and add code column
ALTER TABLE `print_templates` MODIFY `template_code` VARCHAR(100) DEFAULT NULL;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'print_templates' AND COLUMN_NAME = 'code');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `print_templates` ADD COLUMN `code` VARCHAR(100) DEFAULT NULL AFTER `template_code`', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 3. Modify template_name length
ALTER TABLE `print_templates` MODIFY `template_name` VARCHAR(255) DEFAULT NULL;

-- 4. Modify template_category default to '0'
ALTER TABLE `print_templates` MODIFY `template_category` VARCHAR(50) DEFAULT '0';

-- 5. Add GJS section columns for V2 Designer
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'print_templates' AND COLUMN_NAME = 'header_gjs_data');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `print_templates` ADD COLUMN `header_gjs_data` LONGTEXT DEFAULT NULL AFTER `gjs_data`', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'print_templates' AND COLUMN_NAME = 'body_gjs_data');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `print_templates` ADD COLUMN `body_gjs_data` LONGTEXT DEFAULT NULL AFTER `header_gjs_data`', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'print_templates' AND COLUMN_NAME = 'footer_gjs_data');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `print_templates` ADD COLUMN `footer_gjs_data` LONGTEXT DEFAULT NULL AFTER `body_gjs_data`', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'print_templates' AND COLUMN_NAME = 'header_height');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `print_templates` ADD COLUMN `header_height` VARCHAR(20) DEFAULT NULL AFTER `footer_gjs_data`', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 6. Add config_json for advanced settings
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'print_templates' AND COLUMN_NAME = 'config_json');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `print_templates` ADD COLUMN `config_json` LONGTEXT DEFAULT NULL AFTER `template_css`', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 7. Add precise paper width/height
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'print_templates' AND COLUMN_NAME = 'paper_width');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `print_templates` ADD COLUMN `paper_width` INT(11) DEFAULT NULL AFTER `paper_size`', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'print_templates' AND COLUMN_NAME = 'paper_height');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `print_templates` ADD COLUMN `paper_height` INT(11) DEFAULT NULL AFTER `paper_width`', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 8. Add margins for V2 Designer
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'print_templates' AND COLUMN_NAME = 'margin_top');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `print_templates` ADD COLUMN `margin_top` VARCHAR(20) DEFAULT ''0'' AFTER `page_orientation`', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'print_templates' AND COLUMN_NAME = 'margin_right');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `print_templates` ADD COLUMN `margin_right` VARCHAR(20) DEFAULT ''0'' AFTER `margin_top`', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'print_templates' AND COLUMN_NAME = 'margin_bottom');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `print_templates` ADD COLUMN `margin_bottom` VARCHAR(20) DEFAULT ''0'' AFTER `margin_right`', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'print_templates' AND COLUMN_NAME = 'margin_left');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `print_templates` ADD COLUMN `margin_left` VARCHAR(20) DEFAULT ''0'' AFTER `margin_bottom`', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 9. Modify is_active default to 0 (all templates inactive by default, must be explicitly enabled)
ALTER TABLE `print_templates` MODIFY `is_active` TINYINT(1) DEFAULT 0;

-- 10. Rename version to version_number (if exists)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'print_templates' AND COLUMN_NAME = 'version');
SET @sql = IF(@col_exists > 0, 'ALTER TABLE `print_templates` CHANGE `version` `version_number` INT(11) DEFAULT 1', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 11. Add parent_template_id for versioning
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'print_templates' AND COLUMN_NAME = 'parent_template_id');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `print_templates` ADD COLUMN `parent_template_id` INT(11) DEFAULT NULL AFTER `version_number`', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 12. Add created_by for audit trail
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'print_templates' AND COLUMN_NAME = 'created_by');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `print_templates` ADD COLUMN `created_by` INT(11) DEFAULT NULL AFTER `id_branch`', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 13. Add index for code lookup
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'print_templates' AND INDEX_NAME = 'idx_code');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE `print_templates` ADD INDEX `idx_code` (`code`)', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 14. Drop old index if exists (replaced by more granular lookups)
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'print_templates' AND INDEX_NAME = 'idx_active_default');
SET @sql = IF(@idx_exists > 0, 'ALTER TABLE `print_templates` DROP INDEX `idx_active_default`', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
