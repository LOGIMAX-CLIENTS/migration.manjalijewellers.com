-- Migration: Add columns to `payment`
-- Source: database_queries.txt
-- Safe: Each column checked via INFORMATION_SCHEMA before adding

-- UP

-- saved_benefits (database_queries.txt:257 by Abinaya M)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payment' AND COLUMN_NAME = 'saved_benefits');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE payment ADD saved_benefits DECIMAL(10,3) NULL DEFAULT NULL COMMENT \'For digi gold(weight) \' AFTER is_gateway_verified, ADD saved_benefit_amt DECIMAL(10,2) NOT NULL DEFAULT \'0.00\' COMMENT \'Benefit Amount\' AFTER saved_benefits, ADD benefit_value DECIMAL(10,3) NULL DEFAULT NULL COMMENT \'For digi gold\' AFTER saved_benefit_amt, ADD benefit_type INT NULL DEFAULT NULL COMMENT \'For digi gold\' AFTER benefit_value', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- metalrate_edit_date (database_queries.txt:318 by Esakkiraja. N)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payment' AND COLUMN_NAME = 'metalrate_edit_date');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE payment ADD metalrate_edit_date DATETIME NULL DEFAULT NULL AFTER refund_id', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- dg_other_benefit_wgt (database_queries.txt:539 by NAMBI MUTHU RAJA)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payment' AND COLUMN_NAME = 'dg_other_benefit_wgt');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `payment` ADD `dg_other_benefit_wgt` DECIMAL(10,3) NULL DEFAULT NULL AFTER `benefit_type`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- dg_other_benefit_amt (database_queries.txt:540 by NAMBI MUTHU RAJA)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payment' AND COLUMN_NAME = 'dg_other_benefit_amt');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `payment` ADD `dg_other_benefit_amt` DECIMAL(10,2) NULL DEFAULT NULL AFTER `dg_other_benefit_wgt`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
