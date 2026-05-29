-- ============================================================
-- Migration: Add Employee Incentive Report menu under Sales Report
-- Description: Inserts menu under Sales Report (id_menu=136)
--              and grants full access to all profiles.
-- ============================================================
-- UP

-- Step 1: Insert menu entry under Sales Report
INSERT INTO `menu` (`label`, `link`, `parent`, `sort`, `icon`, `active`)
VALUES ('Emp Incentive Report', 'reports/emp_sales_incentive', 136, 25, 'fa fa-circle-o', 1);

-- Step 2: Grant access to all profiles
SET @new_menu_id = LAST_INSERT_ID();

INSERT INTO `access` (`id_profile`, `id_menu`, `view`, `add`, `edit`, `delete`)
SELECT p.id_profile, @new_menu_id, 1, 1, 1, 1
FROM `profile` p
WHERE NOT EXISTS (
    SELECT 1 FROM `access` a
    WHERE a.id_profile = p.id_profile AND a.id_menu = @new_menu_id
);
