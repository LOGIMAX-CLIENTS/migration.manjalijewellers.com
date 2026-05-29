-- Migration: Add columns to `ret_karigar`
-- Source: database_queries.sql
-- Safe: Each column checked via INFORMATION_SCHEMA before adding

-- UP

-- owner_account (database_queries.sql:2671 by DEVADHARSHINI)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ret_karigar' AND COLUMN_NAME = 'owner_account');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `ret_karigar` ADD `owner_account` INT NOT NULL AFTER `opening_balance_amount`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
