-- Migration for adding columns to receipt table
-- Sowmiya
-- Safe: Non-destructive ALTER (adds nullable columns)

-- UP
ALTER TABLE `ret_issue_receipt` ADD COLUMN `goldrate_18ct` INT(11) NULL AFTER `deposit_type`;
ALTER TABLE `ret_issue_receipt` ADD COLUMN `goldrate_22ct` INT(11) NULL AFTER `goldrate_18ct`;
ALTER TABLE `ret_issue_receipt` ADD COLUMN `silverrate_1gm` INT(11) NULL AFTER `goldrate_22ct`;