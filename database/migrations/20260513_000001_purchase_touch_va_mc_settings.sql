-- ============================================================
-- Purchase Touch Type — Schema Migration
-- Feature: Add Purchase Touch as Type 3 in V.A & M.C Settings
--          + Global toggle: is_va_mc_touch_based
-- Date: 2026-05-13
-- ============================================================

-- 1. Add touch range columns to the detail table
-- Safe to re-run: checks if columns exist before adding
-- UP
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'ret_design_weight_range_wc' 
    AND COLUMN_NAME = 'touch_from');

SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `ret_design_weight_range_wc` ADD COLUMN `touch_from` DECIMAL(5,2) DEFAULT NULL COMMENT ''Purchase Touch from value (e.g. 91.00)'' AFTER `wc_to_weight`, ADD COLUMN `touch_to` DECIMAL(5,2) DEFAULT NULL COMMENT ''Purchase Touch to value (e.g. 92.00)'' AFTER `touch_from`',
    'SELECT ''Columns touch_from/touch_to already exist, skipping'' AS status');

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 2. Update type column comment for documentation
ALTER TABLE `ret_selling_settings`
  MODIFY COLUMN `type` TINYINT(1) DEFAULT NULL COMMENT '1=Fixed, 2=Weight Range, 3=Purchase Touch';

-- 3. Global setting: V.A & M.C lookup mode toggle
-- 0 = Weight Range based (default / existing behavior)
-- 1 = Purchase Touch Range based (forces Type 2 to behave like Type 3)
INSERT INTO `ret_settings` (`name`, `value`, `description`, `created_on`, `created_by`)
SELECT 'is_va_mc_touch_based', '0', 'V.A and M.C based on Purchase Touch Range (0=Weight Range, 1=Touch Range)', NOW(), 1
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `ret_settings` WHERE `name` = 'is_va_mc_touch_based');
