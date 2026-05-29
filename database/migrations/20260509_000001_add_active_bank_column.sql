-- Migration: Add active_bank column to bank table
-- Date: 2026-05-09
-- UP

SET @dbname = DATABASE();
SET @tablename = 'bank';
SET @columnname = 'active_bank';
SET @preparedStatement = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
     WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = @columnname) > 0,
    'SELECT 1',
    'ALTER TABLE `bank` ADD COLUMN `active_bank` TINYINT(1) NOT NULL DEFAULT 1 AFTER `bank_name`'
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;
