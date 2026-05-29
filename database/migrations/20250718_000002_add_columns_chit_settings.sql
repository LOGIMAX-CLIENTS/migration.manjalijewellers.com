-- Migration: Add columns to `chit_settings`
-- Source: database_queries.txt
-- Safe: Each column checked via INFORMATION_SCHEMA before adding

-- UP

-- enable_digi_gold (database_queries.txt:259 by Abinaya M)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'chit_settings' AND COLUMN_NAME = 'enable_digi_gold');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `chit_settings` ADD `enable_digi_gold` TINYINT(2) NOT NULL DEFAULT \'0\' COMMENT \'Whether digi gold module available? 0 - No, 1 - Yes\' AFTER `enable_dth`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- show_amt_history (database_queries.txt:382 by Karthikai Kumaran T)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'chit_settings' AND COLUMN_NAME = 'show_amt_history');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `chit_settings` ADD `show_amt_history` TINYINT(1) NOT NULL DEFAULT \'0\' COMMENT \'For app to show the paid amounts history\' AFTER `lock_metal`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- show_wgt_history (database_queries.txt:383 by Karthikai Kumaran T)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'chit_settings' AND COLUMN_NAME = 'show_wgt_history');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `chit_settings` ADD `show_wgt_history` TINYINT(1) NOT NULL DEFAULT \'0\' COMMENT \'For app to show the paid weights history\' AFTER `show_amt_history`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- show_close (database_queries.txt:384 by Karthikai Kumaran T)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'chit_settings' AND COLUMN_NAME = 'show_close');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `chit_settings` ADD `show_close` TINYINT(1) NOT NULL DEFAULT \'0\' COMMENT \'For app to show the closed acoounts or not \' AFTER `show_wgt_history`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- showlanguage (database_queries.txt:393 by Karthikai Kumaran T)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'chit_settings' AND COLUMN_NAME = 'showlanguage');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `chit_settings` ADD `showlanguage` TINYINT(1) NOT NULL DEFAULT \'0\' COMMENT \'For app to show the language option or not \' AFTER `show_close`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- show_social_media (database_queries.txt:397 by Karthikai Kumaran T)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'chit_settings' AND COLUMN_NAME = 'show_social_media');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `chit_settings` ADD `show_social_media` TINYINT(1) NOT NULL DEFAULT \'0\' COMMENT \'For app to show the social media option or not \' AFTER `showlanguage`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- enable_old_sch_acc_no (database_queries.txt:506 by NAMBI MUTHU RAJA)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'chit_settings' AND COLUMN_NAME = 'enable_old_sch_acc_no');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE chit_settings ADD enable_old_sch_acc_no TINYINT(1) NOT NULL DEFAULT \'0\' COMMENT \'For admin to show the old sch acc no or not\' AFTER lock_metal', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
