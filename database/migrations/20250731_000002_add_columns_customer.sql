-- Migration: Add columns to `customer`
-- Source: database_queries.txt
-- Safe: Each column checked via INFORMATION_SCHEMA before adding

-- UP

-- marital_status (database_queries.txt:323 by Esakkiraja. N)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'customer' AND COLUMN_NAME = 'marital_status');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `customer` ADD `marital_status` INT(2) NOT NULL DEFAULT \'0\' COMMENT \'0 - Rather not to say, 1 - single, 2 - married\' AFTER `vip_up_time`, ADD `languages_known` VARCHAR(50) NULL DEFAULT NULL COMMENT \'From languages master\' AFTER `marital_status`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- allocated_on (database_queries.txt:362 by Esakki Jothika.K)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'customer' AND COLUMN_NAME = 'allocated_on');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `customer` ADD `allocated_on` DATETIME NULL DEFAULT NULL AFTER `id_agent`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- allocated_employee (database_queries.txt:363 by Esakki Jothika.K)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'customer' AND COLUMN_NAME = 'allocated_employee');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `customer` ADD `allocated_employee` int(10) NULL DEFAULT NULL AFTER `allocated_on`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
