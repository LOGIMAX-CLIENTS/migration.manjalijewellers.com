-- Adds the push-notification provider switch and credential store to chit_settings.
-- Idempotent: guarded by INFORMATION_SCHEMA so a re-run cannot abort a deploy.
-- notify_platform : 1 = One Signal (incumbent / rollback), 2 = FCM
-- notify_cred     : TEXT holding JSON for EVERY platform, so switching provider
--                   never destroys the other provider's credentials.
--                   { "onesignal":{"id":"","key":""}, "fcm":{"id":"","key":""} }
-- TEXT, not native JSON -- native JSON needs MySQL 5.7.8+/MariaDB 10.2+ and a
-- failed ALTER aborts the whole deploy.
-- No AFTER clause: the anchor column may not exist on every client.
-- No data back-fill here: the read accessor falls back instead.

-- UP
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'chit_settings' AND COLUMN_NAME = 'notify_platform');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `chit_settings` ADD `notify_platform` TINYINT(1) NOT NULL DEFAULT \'1\' COMMENT \'1 - One Signal, 2 - FCM\'', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'chit_settings' AND COLUMN_NAME = 'notify_cred');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `chit_settings` ADD `notify_cred` TEXT NULL DEFAULT NULL', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- registered_devices.token must hold a full FCM token (~142 chars).
-- Widen only if the client is still on the old narrow column.
SET @tok_len = (SELECT IFNULL(CHARACTER_MAXIMUM_LENGTH, 0) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'registered_devices' AND COLUMN_NAME = 'token');
SET @sql = IF(@tok_len > 0 AND @tok_len < 500, 'ALTER TABLE `registered_devices` MODIFY `token` VARCHAR(500) DEFAULT NULL', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- DOWN
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'chit_settings' AND COLUMN_NAME = 'notify_cred');
SET @sql = IF(@col_exists = 1, 'ALTER TABLE `chit_settings` DROP COLUMN `notify_cred`', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'chit_settings' AND COLUMN_NAME = 'notify_platform');
SET @sql = IF(@col_exists = 1, 'ALTER TABLE `chit_settings` DROP COLUMN `notify_platform`', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
