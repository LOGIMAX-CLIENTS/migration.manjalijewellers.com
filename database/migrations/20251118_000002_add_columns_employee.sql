-- Migration: Add columns to `employee`
-- Source: database_queries.sql
-- Safe: Each column checked via INFORMATION_SCHEMA before adding

-- UP

-- is_store_manager (database_queries.sql:155 by NAMBI MUTHU RAJA)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'employee' AND COLUMN_NAME = 'is_store_manager');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `employee` ADD `is_store_manager` TINYINT(1) NOT NULL DEFAULT \'0\' COMMENT \'0 -> employee 1-> manager\' AFTER `enable_chit_collection`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
