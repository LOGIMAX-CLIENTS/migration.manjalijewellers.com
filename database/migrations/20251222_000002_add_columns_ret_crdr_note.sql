-- Migration: Add columns to `ret_crdr_note`
-- Source: database_queries.sql, database_queries.txt
-- Safe: Each column checked via INFORMATION_SCHEMA before adding

-- UP

-- id_cr_dr_ledger (database_queries.sql:10554 by DEVADHARSHINI)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ret_crdr_note' AND COLUMN_NAME = 'id_cr_dr_ledger');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE ret_crdr_note ADD COLUMN id_cr_dr_ledger int(11) DEFAULT NULL AFTER supid', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- id_supplier_rate_cut (database_queries.txt:547 by RUDRA)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ret_crdr_note' AND COLUMN_NAME = 'id_supplier_rate_cut');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `ret_crdr_note` ADD `id_supplier_rate_cut` INT NOT NULL AFTER `po_id`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- po_id (database_queries.txt:548 by RUDRA)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ret_crdr_note' AND COLUMN_NAME = 'po_id');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE ret_crdr_note ADD `po_id` INT NOT NULL AFTER `supid`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- weight (database_queries.txt:549 by RUDRA)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ret_crdr_note' AND COLUMN_NAME = 'weight');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE ret_crdr_note ADD `weight` DOUBLE(12, 3) NOT NULL AFTER `transamount`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
