-- Migration: Add columns to `ret_bill_old_metal_sale_details`
-- Source: database_queries.sql
-- Safe: Each column checked via INFORMATION_SCHEMA before adding

-- UP

-- id_employee (database_queries.sql:2675 by DEVADHARSHINI)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ret_bill_old_metal_sale_details' AND COLUMN_NAME = 'id_employee');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `ret_bill_old_metal_sale_details` ADD `id_employee` INT NOT NULL', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
