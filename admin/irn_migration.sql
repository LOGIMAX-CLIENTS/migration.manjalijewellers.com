--UP
-- IRN E-Invoice Integration — Database Migration
-- Idempotent: Safe to run multiple times

-- 1. Add IRN columns to ret_billing table
SET @dbname = DATABASE();

-- cusdel_irn
SET @columnexists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'ret_billing' AND COLUMN_NAME = 'cusdel_irn');
SET @query = IF(@columnexists = 0, 
    'ALTER TABLE `ret_billing` ADD COLUMN `cusdel_irn` VARCHAR(100) NULL DEFAULT NULL COMMENT "Invoice Reference Number from GSP"', 
    'SELECT 1');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- cusdel_signature
SET @columnexists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'ret_billing' AND COLUMN_NAME = 'cusdel_signature');
SET @query = IF(@columnexists = 0, 
    'ALTER TABLE `ret_billing` ADD COLUMN `cusdel_signature` LONGTEXT NULL DEFAULT NULL COMMENT "Decoded signed invoice JWT payload"', 
    'SELECT 1');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- qrcodeimage
SET @columnexists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'ret_billing' AND COLUMN_NAME = 'qrcodeimage');
SET @query = IF(@columnexists = 0, 
    'ALTER TABLE `ret_billing` ADD COLUMN `qrcodeimage` LONGTEXT NULL DEFAULT NULL COMMENT "Base64 encoded QR code image"', 
    'SELECT 1');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


-- 2. Add GSP credential columns to branch table (per-branch IRN config)

-- aspid
SET @columnexists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'branch' AND COLUMN_NAME = 'aspid');
SET @query = IF(@columnexists = 0, 
    'ALTER TABLE `branch` ADD COLUMN `aspid` VARCHAR(100) NULL DEFAULT NULL COMMENT "GSP ASP ID (Chartered Info)"', 
    'SELECT 1');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- gsp_password
SET @columnexists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'branch' AND COLUMN_NAME = 'gsp_password');
SET @query = IF(@columnexists = 0, 
    'ALTER TABLE `branch` ADD COLUMN `gsp_password` VARCHAR(255) NULL DEFAULT NULL COMMENT "GSP Password (Chartered Info)"', 
    'SELECT 1');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- gsp_user_name
SET @columnexists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'branch' AND COLUMN_NAME = 'gsp_user_name');
SET @query = IF(@columnexists = 0, 
    'ALTER TABLE `branch` ADD COLUMN `gsp_user_name` VARCHAR(100) NULL DEFAULT NULL COMMENT "GSP API Username"', 
    'SELECT 1');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- eInvPwd
SET @columnexists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'branch' AND COLUMN_NAME = 'eInvPwd');
SET @query = IF(@columnexists = 0, 
    'ALTER TABLE `branch` ADD COLUMN `eInvPwd` VARCHAR(255) NULL DEFAULT NULL COMMENT "GSP E-Invoice API Password"', 
    'SELECT 1');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- authtoken
SET @columnexists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'branch' AND COLUMN_NAME = 'authtoken');
SET @query = IF(@columnexists = 0, 
    'ALTER TABLE `branch` ADD COLUMN `authtoken` TEXT NULL DEFAULT NULL COMMENT "GSP Auth Token (auto-managed)"', 
    'SELECT 1');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- authtokenrequest
SET @columnexists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'branch' AND COLUMN_NAME = 'authtokenrequest');
SET @query = IF(@columnexists = 0, 
    'ALTER TABLE `branch` ADD COLUMN `authtokenrequest` TEXT NULL DEFAULT NULL COMMENT "Full GSP auth token request URL (auto-built)"', 
    'SELECT 1');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- grnrequest
SET @columnexists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'branch' AND COLUMN_NAME = 'grnrequest');
SET @query = IF(@columnexists = 0, 
    'ALTER TABLE `branch` ADD COLUMN `grnrequest` TEXT NULL DEFAULT NULL COMMENT "Full GSP invoice request URL (auto-built)"', 
    'SELECT 1');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


-- 3. Seed IRN settings (all disabled by default)
INSERT IGNORE INTO `ret_settings` (`name`, `value`) VALUES ('is_auto_gen_irn', '0');
INSERT IGNORE INTO `ret_settings` (`name`, `value`) VALUES ('is_production', '0');
INSERT IGNORE INTO `ret_settings` (`name`, `value`) VALUES ('production_base_url', '');
INSERT IGNORE INTO `ret_settings` (`name`, `value`) VALUES ('gsp_auth_base_url', '');
INSERT IGNORE INTO `ret_settings` (`name`, `value`) VALUES ('gsp_einvoice_base_url', '');
INSERT IGNORE INTO `ret_settings` (`name`, `value`) VALUES ('irn_error_email', '');
INSERT IGNORE INTO `ret_settings` (`name`, `value`) VALUES ('usp_id', '');
INSERT IGNORE INTO `ret_settings` (`name`, `value`) VALUES ('ci_password', '');

-- is_auto_gen_irn: 0=disabled, 1=print JSON (debug), 2=generate IRN (live)
-- is_production: 0=non-production, 1=production (must be 1 for IRN generation)
-- production_base_url: Must match base_url() for IRN generation to work
