-- ==========================================================
-- Migration: Add Employee Stock Product & Report
-- Date: 2026-04-13
-- ==========================================================
-- UP
-- 1. Create ret_employee_stock table
CREATE TABLE IF NOT EXISTS `ret_employee_stock` (
    `id_emp_stock` INT(11) NOT NULL AUTO_INCREMENT,
    `id_emp` INT(11) NOT NULL,
    `id_branch` INT(11) NOT NULL,
    `date` DATE NOT NULL,
    `created_on` DATETIME DEFAULT NULL,
    `created_by` INT(11) DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_emp_stock`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 2. Create ret_employee_stock_product table
CREATE TABLE IF NOT EXISTS `ret_employee_stock_product` (
    `id_emp_stock_product` INT(11) NOT NULL AUTO_INCREMENT,
    `id_emp_stock` INT(11) NOT NULL,
    `id_product` INT(11) NOT NULL,
    `id_section` INT(11) DEFAULT NULL,
    `pcs` INT(11) DEFAULT 0,
    `is_updated` TINYINT(1) DEFAULT 0,
    `updated_on` DATE DEFAULT NULL,
    `created_on` DATETIME DEFAULT NULL,
    `created_by` INT(11) DEFAULT NULL,
    PRIMARY KEY (`id_emp_stock_product`),
    KEY `id_emp_stock` (`id_emp_stock`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 3. Add Menu Items

-- Add "Employee Stock Product" under "Retail Catalog"
INSERT INTO menu (label, link, parent, sort, icon, active) 
VALUES (
    'Employee Stock Product', 
    'admin_ret_catalog/employee_stock_product/add', 
    (SELECT id_menu FROM (SELECT id_menu FROM menu WHERE label = 'Retail Catalog' LIMIT 1) AS t), 
    99, 
    'fa fa-users', 
    1
);

SET @menu_id_1 = LAST_INSERT_ID();

-- Add "Employee Product Report" under "Stock Report"
INSERT INTO menu (label, link, parent, sort, icon, active) 
VALUES (
    'Employee Product Report', 
    'admin_ret_reports/employee_product_report/list', 
    (SELECT id_menu FROM (SELECT id_menu FROM menu WHERE label = 'Stock Report' LIMIT 1) AS t), 
    99, 
    'fa fa-bar-chart', 
    1
);

SET @menu_id_2 = LAST_INSERT_ID();

-- 4. Grant Access (Admin Profile ID: 1)
INSERT INTO access (id_profile, id_menu, `view`, `add`, `edit`, `delete`) 
VALUES (1, @menu_id_1, 1, 1, 1, 1);

INSERT INTO access (id_profile, id_menu, `view`, `add`, `edit`, `delete`) 
VALUES (1, @menu_id_2, 1, 1, 1, 1);
