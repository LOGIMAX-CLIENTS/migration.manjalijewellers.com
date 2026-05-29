-- Add touch_from and touch_to columns to ret_old_metal_category for auto-selection of Old Gold Category based on purity/touch range
-- UP
ALTER TABLE `ret_old_metal_category`
ADD COLUMN `touch_from` DECIMAL(10,2) NOT NULL DEFAULT 0 COMMENT 'Touch range start for auto-category selection' AFTER `old_metal_discount`,
ADD COLUMN `touch_to` DECIMAL(10,2) NOT NULL DEFAULT 0 COMMENT 'Touch range end for auto-category selection' AFTER `touch_from`;
