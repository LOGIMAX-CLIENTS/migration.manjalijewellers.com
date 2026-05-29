-- Migration: Add columns to `branch`
-- Source: database_queries.sql
-- Safe: Each column checked via INFORMATION_SCHEMA before adding

-- UP

-- id_village (database_queries.sql:167 by NAMBI MUTHU RAJA)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'branch' AND COLUMN_NAME = 'id_village');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `branch` ADD `id_village` INT(11) NULL DEFAULT NULL AFTER `pincode`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
