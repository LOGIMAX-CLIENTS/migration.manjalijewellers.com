-- Migration: Add columns to `payment_mode_details`
-- Source: database_queries.txt
-- Safe: Each column checked via INFORMATION_SCHEMA before adding

-- UP

-- adv_receipt_no (database_queries.txt:347 by Karthikai Kumaran T)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payment_mode_details' AND COLUMN_NAME = 'adv_receipt_no');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `payment_mode_details` ADD `adv_receipt_no` VARCHAR(45) NULL DEFAULT NULL COMMENT \'In this column we store a bill no id thats column name is id_issue_receipt in ret_issue_receipt against a payment which is done by adj adjustment\' AFTER `id_bank`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
