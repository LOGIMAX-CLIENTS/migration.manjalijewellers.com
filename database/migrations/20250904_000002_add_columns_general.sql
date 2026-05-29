-- Migration: Add columns to `general`
-- Source: database_queries.txt
-- Safe: Each column checked via INFORMATION_SCHEMA before adding

-- UP

-- lang (database_queries.txt:395 by Karthikai Kumaran T)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'general' AND COLUMN_NAME = 'lang');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `general` ADD `lang` TINYINT(1) NOT NULL DEFAULT \'0\' COMMENT \'1 => Tamil , 2=> English\' AFTER `img`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
