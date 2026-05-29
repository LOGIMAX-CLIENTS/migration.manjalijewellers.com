-- ============================================================
-- Migration: Add Employee Sales Incentive menu entry
-- Description: Inserts menu item under Retail Catalog (id_menu=102)
--              and grants full access (view/add/edit/delete) to
--              all existing profiles.
-- ============================================================
-- UP
-- Step 1: Insert menu entry under Retail Catalog
INSERT INTO `menu` (`label`, `link`, `parent`, `sort`, `icon`, `active`)
VALUES ('Emp Sales Incentive', 'emp_incentive', 102, 12, 'fa fa-circle-o', 1);

-- Step 2: Get the newly inserted menu ID and grant access to all profiles
SET @new_menu_id = LAST_INSERT_ID();

INSERT INTO `access` (`id_profile`, `id_menu`, `view`, `add`, `edit`, `delete`)
SELECT p.id_profile, @new_menu_id, 1, 1, 1, 1
FROM `profile` p
WHERE NOT EXISTS (
    SELECT 1 FROM `access` a
    WHERE a.id_profile = p.id_profile AND a.id_menu = @new_menu_id
);
