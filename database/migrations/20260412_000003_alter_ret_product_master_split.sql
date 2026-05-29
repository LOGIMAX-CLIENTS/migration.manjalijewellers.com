-- Migration: Add ag_parts to ret_product_master
-- Feature: Partly Sale — per-product enable/disable for physical tag splitting
-- Used by: ret_tag_parts_model.php (check_product_parts_config)
-- Safe: Column checked via INFORMATION_SCHEMA before adding

-- UP

-- ag_parts — Controls whether tags of this product can be partly sold
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ret_product_master' AND COLUMN_NAME = 'ag_parts');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `ret_product_master` ADD `ag_parts` TINYINT(1) NOT NULL DEFAULT 0 COMMENT "0=No, 1=Yes (Allow Partly Sale)"', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- DOWN
-- ALTER TABLE `ret_product_master` DROP COLUMN `ag_parts`;
