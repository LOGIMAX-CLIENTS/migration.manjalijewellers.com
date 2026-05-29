-- Migration: Add columns to `scheme_benefit_deduct_settings`
-- Source: database_queries.txt
-- Safe: Each column checked via INFORMATION_SCHEMA before adding

-- UP

-- commodity (database_queries.txt:538 by NAMBI MUTHU RAJA)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'scheme_benefit_deduct_settings' AND COLUMN_NAME = 'commodity');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `scheme_benefit_deduct_settings` ADD `commodity` INT NULL DEFAULT NULL AFTER `installment_no`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
