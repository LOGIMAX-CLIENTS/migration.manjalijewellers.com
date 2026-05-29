-- =====================================================
-- SQL Script: Add Employee Stock Product Menu Items
-- =====================================================
-- This script adds two new menu items:
-- 1. "Employee Stock Product" under Retail Catalog parent menu
-- 2. "Employee Product Report" under Stock Report parent menu
-- 
-- INSTRUCTIONS:
-- 1. First, find the parent menu IDs by running:
--    SELECT id_menu, label, link, parent FROM menu WHERE label LIKE '%Catalog%' OR label LIKE '%Stock%' OR label LIKE '%Report%';
-- 2. Update the @catalog_parent and @report_parent values below with the correct parent IDs
-- 3. Run this script on your target database
-- =====================================================

-- Step 1: Find parent menu IDs (run this first to identify the correct values)
SELECT id_menu, label, link, parent FROM menu 
WHERE label LIKE '%Catalog%' OR label LIKE '%Stock Report%' OR label LIKE '%Report%'
ORDER BY parent, sort;

-- Step 2: Insert menu items (update parent IDs as needed)
-- NOTE: Replace the parent values with the actual id_menu from Step 1

-- Employee Stock Product under Retail Catalog
INSERT INTO menu (label, link, parent, sort, icon, active) 
VALUES ('Employee Stock Product', 'admin_ret_catalog/employee_stock_product/add', 
        (SELECT id_menu FROM (SELECT id_menu FROM menu WHERE label = 'Retail Catalog' LIMIT 1) AS t), 
        99, 'fa fa-users', 1);

-- Get the inserted menu id for access table
SET @emp_stock_menu_id = LAST_INSERT_ID();

-- Employee Product Report under Stock Report  
INSERT INTO menu (label, link, parent, sort, icon, active) 
VALUES ('Employee Product Report', 'admin_ret_reports/employee_product_report/list', 
        (SELECT id_menu FROM (SELECT id_menu FROM menu WHERE label = 'Stock Report' LIMIT 1) AS t), 
        99, 'fa fa-bar-chart', 1);

SET @emp_report_menu_id = LAST_INSERT_ID();

-- Step 3: Grant access to admin profile (profile_id = 1)
INSERT INTO access (id_profile, id_menu, `view`, `add`, `edit`, `delete`) 
VALUES (1, @emp_stock_menu_id, 1, 1, 1, 1);

INSERT INTO access (id_profile, id_menu, `view`, `add`, `edit`, `delete`) 
VALUES (1, @emp_report_menu_id, 1, 1, 1, 1);

-- Step 4: Create required tables if they don't exist
CREATE TABLE IF NOT EXISTS ret_employee_stock (
    id_emp_stock INT AUTO_INCREMENT PRIMARY KEY,
    id_emp INT NOT NULL,
    id_branch INT NOT NULL,
    date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS ret_employee_stock_product (
    id_emp_stock_product INT AUTO_INCREMENT PRIMARY KEY,
    id_emp_stock INT NOT NULL,
    id_product INT NOT NULL,
    id_section INT DEFAULT NULL,
    pcs INT DEFAULT 0,
    is_updated TINYINT(1) DEFAULT 0,
    updated_on DATE DEFAULT NULL,
    FOREIGN KEY (id_emp_stock) REFERENCES ret_employee_stock(id_emp_stock)
);

-- =====================================================
-- DONE! After running this script:
-- 1. Log out and log back in to see the new menu items
-- 2. Make sure to grant access to other profiles as needed
--    via Settings > User Rights
-- =====================================================
