-- ============================================================
-- Move "Incentive Report" menu item from Stock Aging parent 
-- to Sales Report parent menu
-- ============================================================
-- Step 1: Find the parent id_menu for "Sales Report" 
-- Step 2: Update the Incentive Report's parent to point to Sales Report

-- Find and update: Change parent of menu item with link containing 
-- 'incentive_report' or 'emp_sales_incentive' to the Sales Report parent

-- First, let's identify the correct IDs by running these SELECT queries:
-- SELECT id_menu, label, link, parent FROM menu WHERE link LIKE '%incentive%';
-- SELECT id_menu, label, link, parent FROM menu WHERE label LIKE '%Sales Report%' AND parent = 1;
-- SELECT id_menu, label, link, parent FROM menu WHERE label LIKE '%Stock Ag%';

-- Update: Move incentive report menu items to Sales Report parent
-- Replace {SALES_REPORT_PARENT_ID} with the actual id_menu of "Sales Report" parent
-- Replace {STOCK_AGING_PARENT_ID} with the actual id_menu of "Stock Aging" parent

-- Run this query first to find the IDs:
-- SELECT id_menu, label, link, parent FROM menu 
-- WHERE label LIKE '%Sales%Report%' OR label LIKE '%Incentive%' OR label LIKE '%Stock Ag%'
-- ORDER BY parent, id_menu;

-- Then update accordingly:
-- UPDATE menu SET parent = {SALES_REPORT_PARENT_ID} 
-- WHERE link LIKE '%incentive_report%' AND parent = {STOCK_AGING_PARENT_ID};
