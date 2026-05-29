-- Migration: Add IRN e-invoice columns to `ret_billing`
-- Feature: IRN E-Invoice Integration
-- Safe: Each column checked via INFORMATION_SCHEMA before adding

-- UP

-- cusdel_irn — Invoice Reference Number from GSP
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ret_billing' AND COLUMN_NAME = 'cusdel_irn');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `ret_billing` ADD `cusdel_irn` VARCHAR(100) NULL DEFAULT NULL COMMENT "Invoice Reference Number from GSP"', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- cusdel_signature — Decoded signed invoice JWT payload
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ret_billing' AND COLUMN_NAME = 'cusdel_signature');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `ret_billing` ADD `cusdel_signature` LONGTEXT NULL DEFAULT NULL COMMENT "Decoded signed invoice JWT payload"', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- qrcodeimage — Base64 encoded QR code image
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ret_billing' AND COLUMN_NAME = 'qrcodeimage');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `ret_billing` ADD `qrcodeimage` LONGTEXT NULL DEFAULT NULL COMMENT "Base64 encoded QR code image"', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- DOWN
-- ALTER TABLE `ret_billing` DROP COLUMN `cusdel_irn`;
-- ALTER TABLE `ret_billing` DROP COLUMN `cusdel_signature`;
-- ALTER TABLE `ret_billing` DROP COLUMN `qrcodeimage`;
