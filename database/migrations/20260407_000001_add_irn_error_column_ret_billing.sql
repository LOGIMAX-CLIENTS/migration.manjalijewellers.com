-- Migration: Add `irn_error` column to `ret_billing`
-- Feature: IRN E-Invoice Integration — stores GSP error message per bill
-- Used by: common_helper.php (generateGSPEInvoice_api), ret_reports_model.php (irn_details)
-- Safe: Column checked via INFORMATION_SCHEMA before adding

-- UP

-- irn_error — Stores the last GSP API error message for failed IRN generation
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ret_billing' AND COLUMN_NAME = 'irn_error');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `ret_billing` ADD `irn_error` TEXT NULL DEFAULT NULL COMMENT "Last GSP API error message for failed IRN generation" AFTER `qrcodeimage`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- DOWN
-- ALTER TABLE `ret_billing` DROP COLUMN `irn_error`;
