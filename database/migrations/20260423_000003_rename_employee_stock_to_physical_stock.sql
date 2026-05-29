-- ==========================================================
-- Migration: Rename Employee Stock menu items to Physical Stock
-- Date: 2026-04-23
-- Description: Renames 'Employee Stock Product' → 'Physical Stock Entry'
--              and 'Employee Product Report' → 'Physical Stock Report'
-- ==========================================================
-- UP

-- Rename the entry page menu item
UPDATE `menu` 
SET `label` = 'Physical Stock Entry' 
WHERE `link` = 'admin_ret_catalog/employee_stock_product/add' 
  AND `label` = 'Employee Stock Product';

-- Rename the report page menu item
UPDATE `menu` 
SET `label` = 'Physical Stock Report' 
WHERE `link` = 'admin_ret_reports/employee_product_report/list' 
  AND `label` = 'Employee Product Report';
