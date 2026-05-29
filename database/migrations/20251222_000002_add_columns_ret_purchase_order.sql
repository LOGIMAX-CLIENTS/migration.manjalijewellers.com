-- Migration: Add columns to `ret_purchase_order`
-- Source: database_queries.txt
-- Safe: Each column checked via INFORMATION_SCHEMA before adding

-- UP

-- po_delivery_date (database_queries.txt:573 by RUDRA)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ret_purchase_order' AND COLUMN_NAME = 'po_delivery_date');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `ret_purchase_order` ADD `po_delivery_date` DATE NOT NULL AFTER `po_date`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
