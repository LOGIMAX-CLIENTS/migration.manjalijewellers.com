-- ==========================================================
-- Migration: Employee Stock Product Enhancements (Weight & Product Type)
-- Date: 2026-04-16
-- ==========================================================
-- UP

-- 1. Add stock_audit_by setting (1=Product Based, 2=Section Based)
INSERT INTO ret_settings (name, value, description) 
VALUES ('stock_audit_by', '1', 'Stock audit type: 1=Product Based, 2=Section Based')
ON DUPLICATE KEY UPDATE description = 'Stock audit type: 1=Product Based, 2=Section Based';

-- 2. Add weight column for non-tagged product weight entry
ALTER TABLE ret_employee_stock_product 
ADD COLUMN weight DECIMAL(10,3) DEFAULT 0.000 AFTER pcs;

-- 3. Add product_type column (1=tagged, 2=non-tagged)
ALTER TABLE ret_employee_stock_product 
ADD COLUMN product_type TINYINT(1) DEFAULT 1 AFTER weight;
