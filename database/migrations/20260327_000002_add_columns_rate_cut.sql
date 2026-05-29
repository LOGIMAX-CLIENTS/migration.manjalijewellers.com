-- Migration: Alter table ret_supplier_rate_cut — add is_tds, tds_percent and tds_tax_value columns
-- Author: Gokul
-- Safe: Non-destructive ALTER (adds nullable column)

-- UP

ALTER TABLE ret_supplier_rate_cut
ADD COLUMN is_tds TINYINT(1) DEFAULT 0 AFTER narration,
ADD COLUMN tds_percent DECIMAL(5,2) DEFAULT 0 AFTER is_tds,
ADD COLUMN tds_tax_value DECIMAL(10,2) DEFAULT 0.00 AFTER tds_percent;