-- Migration: Add columns to `transaction`
-- Source: database_queries.txt
-- Safe: Each column checked via INFORMATION_SCHEMA before adding

-- UP

-- benefit_value (database_queries.txt:261 by Abinaya M)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'transaction' AND COLUMN_NAME = 'benefit_value');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `transaction` ADD `benefit_value` DECIMAL(10,2) NOT NULL DEFAULT \'0.00\' COMMENT \'Digi gold benefit percentage value\', ADD `saved_benefit_amt` DECIMAL(10,2) NOT NULL DEFAULT \'0.00\' COMMENT \'Digi gold benefit amount\' AFTER `benefit_value`, ADD `saved_benefits_wgt` DECIMAL(10,3) NOT NULL DEFAULT \'0.000\' COMMENT \'Digi gold benefit weight\' AFTER `saved_benefit_amt`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
