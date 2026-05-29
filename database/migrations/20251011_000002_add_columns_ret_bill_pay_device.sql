-- Migration: Add columns to `ret_bill_pay_device`
-- Source: database_queries.txt
-- Safe: Each column checked via INFORMATION_SCHEMA before adding

-- UP

-- sort_order (database_queries.txt:438 by Esakki Jothika)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ret_bill_pay_device' AND COLUMN_NAME = 'sort_order');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE ret_bill_pay_device ADD COLUMN sort_order INT(11) AFTER device_name', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
