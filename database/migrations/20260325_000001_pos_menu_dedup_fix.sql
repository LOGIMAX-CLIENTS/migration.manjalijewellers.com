-- Migration: POS Menu Dedup + Clean Recreate
-- Date: 25-03-2026
-- Description: Removes ALL duplicate POS Management menus and access rows,
--              then recreates exactly 1 parent + 3 sub-menus with correct access.
-- Safe: Fully idempotent — works on fresh DB or one with any number of duplicates.

-- UP
SET SQL_SAFE_UPDATES = 0;

-- 1. Delete access for POS sub-menus (by exact link)
DELETE FROM access WHERE id_menu IN (
  SELECT id_menu FROM menu WHERE link IN ('admin_pos/posSettings','admin_pos/posTransactions','admin_pos/posSettlementPage','admin_ret_billing/posSettings','admin_ret_billing/posTransactions')
);

-- 2. Delete access for children of any POS Management parent
DELETE a FROM access a
INNER JOIN menu child ON a.id_menu = child.id_menu
INNER JOIN menu parent ON child.parent = parent.id_menu
WHERE parent.label = 'POS Management' AND parent.link = '#';

-- 3. Delete access for POS Management parent menus themselves
DELETE FROM access WHERE id_menu IN (
  SELECT id_menu FROM menu WHERE label = 'POS Management' AND link = '#'
);

-- 4. Delete POS sub-menus (by exact link)
DELETE FROM menu WHERE link IN ('admin_pos/posSettings','admin_pos/posTransactions','admin_pos/posSettlementPage','admin_ret_billing/posSettings','admin_ret_billing/posTransactions');

-- 5. Delete children of any POS Management parent (catch-all)
DELETE child FROM menu child
INNER JOIN menu parent ON child.parent = parent.id_menu
WHERE parent.label = 'POS Management' AND parent.link = '#';

-- 6. Delete all POS Management parent menus
DELETE FROM menu WHERE label = 'POS Management' AND link = '#';

-- 7. Recreate clean: 1 parent + 3 sub-menus
INSERT INTO menu (label, link, parent, sort, icon, active) VALUES ('POS Management', '#', 1, 5, 'fa fa-credit-card', 1);
SET @p = LAST_INSERT_ID();
INSERT INTO menu (label, link, parent, sort, icon, active) VALUES ('POS Settings', 'admin_pos/posSettings', @p, 1, 'fa fa-cog', 1);
SET @s1 = LAST_INSERT_ID();
INSERT INTO menu (label, link, parent, sort, icon, active) VALUES ('Transaction Log', 'admin_pos/posTransactions', @p, 2, 'fa fa-list-alt', 1);
SET @s2 = LAST_INSERT_ID();
INSERT INTO menu (label, link, parent, sort, icon, active) VALUES ('Settlement Summary', 'admin_pos/posSettlementPage', @p, 3, 'fa fa-bar-chart', 1);
SET @s3 = LAST_INSERT_ID();

-- 8. Access permissions
INSERT INTO access (id_profile, id_menu, `view`, `add`, `edit`, `delete`) VALUES
(1,@p,1,1,1,1),(1,@s1,1,1,1,1),(1,@s2,1,1,1,1),(1,@s3,1,1,1,1),
(2,@p,1,1,1,1),(2,@s1,1,1,1,1),(2,@s2,1,1,1,1),(2,@s3,1,1,1,1),
(3,@p,1,0,0,0),(3,@s1,1,0,0,0),(3,@s2,1,0,0,0),(3,@s3,1,0,0,0);

SET SQL_SAFE_UPDATES = 1;

-- DOWN
-- DELETE a FROM access a INNER JOIN menu m ON a.id_menu = m.id_menu WHERE m.link LIKE 'admin_pos/%' OR (m.label = 'POS Management' AND m.link = '#');
-- DELETE FROM menu WHERE link LIKE 'admin_pos/%' OR (label = 'POS Management' AND link = '#');
