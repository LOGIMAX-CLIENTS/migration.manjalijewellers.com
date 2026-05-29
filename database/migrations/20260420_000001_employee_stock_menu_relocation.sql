-- Employee Product Stock Entry UI Refinement - Menu Relocation
-- Date: 20-04-2026
-- Description: Moves the 'Add Product Stock Entry' menu item from 'Retail Catalog' to 'Stock Report'
-- UP
UPDATE `menu` SET `parent` = 166 WHERE `link` = 'admin_ret_catalog/employee_stock_product/add';
