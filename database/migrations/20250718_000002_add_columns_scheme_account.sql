-- Migration: Add columns to `scheme_account`
-- Source: database_queries.txt
-- Safe: Each column checked via INFORMATION_SCHEMA before adding

-- UP

-- dg_target_value_wgt (database_queries.txt:255 by Abinaya M)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'scheme_account' AND COLUMN_NAME = 'dg_target_value_wgt');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE scheme_account ADD dg_target_value_wgt DECIMAL(10,3) NOT NULL DEFAULT \'0.000\' AFTER total_paid_ins, ADD dg_target_wgt_achieved DECIMAL(10,3) NOT NULL DEFAULT \'0.000\' AFTER dg_target_value_wgt', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- add_ben_type (database_queries.txt:341 by Esakkiraja. N)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'scheme_account' AND COLUMN_NAME = 'add_ben_type');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE scheme_account ADD add_ben_type TINYINT(5) NOT NULL DEFAULT \'0\' COMMENT \'0 -> amount, 1 -> weight\' AFTER additional_benefits', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- old_scheme_acc_number (database_queries.txt:531 by NAMBI MUTHU RAJA)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'scheme_account' AND COLUMN_NAME = 'old_scheme_acc_number');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `scheme_account` ADD `old_scheme_acc_number` INT(50) NULL DEFAULT NULL', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- dg_other_benefit_wgt (database_queries.txt:541 by NAMBI MUTHU RAJA)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'scheme_account' AND COLUMN_NAME = 'dg_other_benefit_wgt');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `scheme_account` ADD `dg_other_benefit_wgt` DECIMAL(10,3) NULL DEFAULT NULL AFTER `dg_target_wgt_achieved`, ADD `dg_other_benefit_amt` DECIMAL(10,2) NULL DEFAULT NULL AFTER `dg_other_benefit_wgt`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
