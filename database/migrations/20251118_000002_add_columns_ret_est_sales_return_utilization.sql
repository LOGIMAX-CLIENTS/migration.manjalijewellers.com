-- Migration: Add columns to `ret_est_sales_return_utilization`
-- Source: database_queries.sql
-- Safe: Each column checked via INFORMATION_SCHEMA before adding

-- UP

-- esti_date (database_queries.sql:81 by unknown)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ret_est_sales_return_utilization' AND COLUMN_NAME = 'esti_date');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `ret_est_sales_return_utilization` ADD `esti_date` DATE NULL DEFAULT NULL AFTER `bill_det_id`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
