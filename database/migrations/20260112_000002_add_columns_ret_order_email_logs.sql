-- Migration: Add columns to `ret_order_email_logs`
-- Source: database_queries.sql
-- Safe: Each column checked via INFORMATION_SCHEMA before adding

-- UP

-- rejected_at (database_queries.sql:2696 by DEVADHARSHINI)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ret_order_email_logs' AND COLUMN_NAME = 'rejected_at');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE ret_order_email_logs ADD COLUMN rejected_at DATETIME NULL AFTER accepted_at; ALTER TABLE ret_order_email_logs ADD COLUMN rejection_reason TEXT NULL AFTER rejected_at', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
