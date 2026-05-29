-- Migration: Add GSP credential columns to `branch` for per-branch IRN config
-- Feature: IRN E-Invoice Integration
-- Safe: Each column checked via INFORMATION_SCHEMA before adding

-- UP

-- aspid — GSP ASP ID (Chartered Info)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'branch' AND COLUMN_NAME = 'aspid');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `branch` ADD `aspid` VARCHAR(100) NULL DEFAULT NULL COMMENT "GSP ASP ID (Chartered Info)"', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- gsp_password — GSP Password (Chartered Info)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'branch' AND COLUMN_NAME = 'gsp_password');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `branch` ADD `gsp_password` VARCHAR(255) NULL DEFAULT NULL COMMENT "GSP Password (Chartered Info)"', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- gsp_user_name — GSP API Username
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'branch' AND COLUMN_NAME = 'gsp_user_name');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `branch` ADD `gsp_user_name` VARCHAR(100) NULL DEFAULT NULL COMMENT "GSP API Username"', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- eInvPwd — GSP E-Invoice API Password
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'branch' AND COLUMN_NAME = 'eInvPwd');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `branch` ADD `eInvPwd` VARCHAR(255) NULL DEFAULT NULL COMMENT "GSP E-Invoice API Password"', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- authtoken — GSP Auth Token (auto-managed by code)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'branch' AND COLUMN_NAME = 'authtoken');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `branch` ADD `authtoken` TEXT NULL DEFAULT NULL COMMENT "GSP Auth Token (auto-managed)"', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- authtokenrequest — Full GSP auth token request URL (auto-built by code)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'branch' AND COLUMN_NAME = 'authtokenrequest');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `branch` ADD `authtokenrequest` TEXT NULL DEFAULT NULL COMMENT "Full GSP auth token request URL (auto-built)"', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- grnrequest — Full GSP invoice request URL (auto-built by code)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'branch' AND COLUMN_NAME = 'grnrequest');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `branch` ADD `grnrequest` TEXT NULL DEFAULT NULL COMMENT "Full GSP invoice request URL (auto-built)"', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- DOWN
-- ALTER TABLE `branch` DROP COLUMN `aspid`;
-- ALTER TABLE `branch` DROP COLUMN `gsp_password`;
-- ALTER TABLE `branch` DROP COLUMN `gsp_user_name`;
-- ALTER TABLE `branch` DROP COLUMN `eInvPwd`;
-- ALTER TABLE `branch` DROP COLUMN `authtoken`;
-- ALTER TABLE `branch` DROP COLUMN `authtokenrequest`;
-- ALTER TABLE `branch` DROP COLUMN `grnrequest`;
