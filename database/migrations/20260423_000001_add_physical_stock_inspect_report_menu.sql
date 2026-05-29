-- ============================================================
-- Migration: Add Physical Stock Inspect Report menu entry
-- Date: 2026-04-23
-- Description: Inserts menu item under 'Stock Report' parent
--              and grants full access (view/add/edit/delete) to
--              all existing profiles.
-- ============================================================
-- UP

-- Step 1: Insert menu entry under Stock Report (safe subquery for parent)
INSERT INTO `menu` (`label`, `link`, `parent`, `sort`, `icon`, `active`)
SELECT 'Physical Stock Inspect',
       'admin_ret_reports/physical_stock_inspect_report/list',
       (SELECT id_menu FROM (SELECT id_menu FROM `menu` WHERE `label` = 'Stock Report' LIMIT 1) AS t),
       99,
       'fa fa-search',
       1
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM `menu` WHERE `link` = 'admin_ret_reports/physical_stock_inspect_report/list'
);

-- Step 2: Get the newly inserted menu ID and grant access to all profiles
SET @new_menu_id = (SELECT id_menu FROM `menu` WHERE `link` = 'admin_ret_reports/physical_stock_inspect_report/list' LIMIT 1);

INSERT INTO `access` (`id_profile`, `id_menu`, `view`, `add`, `edit`, `delete`)
SELECT p.id_profile, @new_menu_id, 1, 1, 1, 1
FROM `profile` p
WHERE @new_menu_id IS NOT NULL
  AND NOT EXISTS (
    SELECT 1 FROM `access` a
    WHERE a.id_profile = p.id_profile AND a.id_menu = @new_menu_id
);

-- DOWN (rollback)
-- DELETE FROM `access` WHERE `id_menu` = (SELECT id_menu FROM `menu` WHERE `link` = 'admin_ret_reports/physical_stock_inspect_report/list' LIMIT 1);
-- DELETE FROM `menu` WHERE `link` = 'admin_ret_reports/physical_stock_inspect_report/list';
