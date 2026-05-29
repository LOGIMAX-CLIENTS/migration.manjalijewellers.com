-- Migration: POS Module + Menu Setup (Dedup + Idempotent)
-- Date: 24-03-2026
-- Description: Registers POS module, cleans up duplicate menu entries,
--              creates menus if missing, sets access permissions.
-- Safe: All operations are idempotent — safe to run on fresh or existing databases.

-- UP
SET SQL_SAFE_UPDATES = 0;

-- 1. MODULE: Register POS in modules table (UNIQUE KEY on m_code prevents duplicates)
INSERT INTO modules (m_name, m_code, m_app, m_web, m_active, date_add, date_upd)
VALUES ('POS Integration', 'POS', 0, 1, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE m_name = 'POS Integration';

-- 2. SETTING: pay_by_pos flag (skip if already exists)
INSERT INTO `ret_settings` (`name`, `value`, `description`, `created_by`)
SELECT 'pay_by_pos', '0', 'Enable POS machine payment for card transactions (1=enable, 0=disable)', 1
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `ret_settings` WHERE `name` = 'pay_by_pos');

-- 3. CLEANUP: Remove duplicate sub-menus (keep oldest id_menu per link under POS Management)
SET @pos_parent = (SELECT MIN(id_menu) FROM menu WHERE label = 'POS Management' AND link = '#');

DELETE a FROM access a
INNER JOIN menu m ON a.id_menu = m.id_menu
WHERE @pos_parent IS NOT NULL
  AND m.parent = @pos_parent
  AND m.id_menu NOT IN (
    SELECT keep_id FROM (
      SELECT MIN(id_menu) AS keep_id FROM menu WHERE parent = @pos_parent GROUP BY link
    ) tmp
  );

DELETE FROM menu
WHERE @pos_parent IS NOT NULL
  AND parent = @pos_parent
  AND id_menu NOT IN (
    SELECT keep_id FROM (
      SELECT MIN(id_menu) AS keep_id FROM menu WHERE parent = @pos_parent GROUP BY link
    ) tmp
  );

-- 3b. Remove duplicate POS Management parent menus (keep oldest)
DELETE FROM access
WHERE @pos_parent IS NOT NULL
  AND id_menu IN (
    SELECT id_menu FROM (
      SELECT id_menu FROM menu WHERE label = 'POS Management' AND link = '#' AND id_menu != @pos_parent
    ) tmp
  );

DELETE FROM menu
WHERE @pos_parent IS NOT NULL
  AND parent IN (
    SELECT id_menu FROM (
      SELECT id_menu FROM menu WHERE label = 'POS Management' AND link = '#' AND id_menu != @pos_parent
    ) tmp
  );

DELETE FROM menu
WHERE @pos_parent IS NOT NULL
  AND label = 'POS Management' AND link = '#' AND id_menu != @pos_parent;

-- 4. CREATE MENUS: Parent + sub-menus (only if they don't exist)
SET @pos_menu_exists = (SELECT COUNT(*) FROM menu WHERE label = 'POS Management' AND link = '#');

SET @sql = IF(@pos_menu_exists = 0,
  'INSERT INTO menu (label, link, parent, sort, icon, active) VALUES (''POS Management'', ''#'', 1, 5, ''fa fa-credit-card'', 1)',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @pos_parent = (SELECT id_menu FROM menu WHERE label = 'POS Management' AND link = '#' LIMIT 1);

SET @sub_exists = (SELECT COUNT(*) FROM menu WHERE link = 'admin_pos/posSettings');
SET @sql = IF(@sub_exists = 0,
  CONCAT('INSERT INTO menu (label, link, parent, sort, icon, active) VALUES (''POS Settings'', ''admin_pos/posSettings'', ', @pos_parent, ', 1, ''fa fa-cog'', 1)'),
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @settings_menu = (SELECT id_menu FROM menu WHERE link = 'admin_pos/posSettings' LIMIT 1);

SET @sub_exists = (SELECT COUNT(*) FROM menu WHERE link = 'admin_pos/posTransactions');
SET @sql = IF(@sub_exists = 0,
  CONCAT('INSERT INTO menu (label, link, parent, sort, icon, active) VALUES (''Transaction Log'', ''admin_pos/posTransactions'', ', @pos_parent, ', 2, ''fa fa-list-alt'', 1)'),
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @trans_menu = (SELECT id_menu FROM menu WHERE link = 'admin_pos/posTransactions' LIMIT 1);

SET @sub_exists = (SELECT COUNT(*) FROM menu WHERE link = 'admin_pos/posSettlementPage');
SET @sql = IF(@sub_exists = 0,
  CONCAT('INSERT INTO menu (label, link, parent, sort, icon, active) VALUES (''Settlement Summary'', ''admin_pos/posSettlementPage'', ', @pos_parent, ', 3, ''fa fa-bar-chart'', 1)'),
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @settle_menu = (SELECT id_menu FROM menu WHERE link = 'admin_pos/posSettlementPage' LIMIT 1);

-- 5. ACCESS PERMISSIONS (INSERT IGNORE = skip if already exists)
INSERT IGNORE INTO access (id_profile, id_menu, `view`, `add`, `edit`, `delete`)
VALUES (1, @pos_parent, 1, 1, 1, 1), (1, @settings_menu, 1, 1, 1, 1),
       (1, @trans_menu, 1, 1, 1, 1), (1, @settle_menu, 1, 1, 1, 1);

INSERT IGNORE INTO access (id_profile, id_menu, `view`, `add`, `edit`, `delete`)
VALUES (2, @pos_parent, 1, 1, 1, 1), (2, @settings_menu, 1, 1, 1, 1),
       (2, @trans_menu, 1, 1, 1, 1), (2, @settle_menu, 1, 1, 1, 1);

INSERT IGNORE INTO access (id_profile, id_menu, `view`, `add`, `edit`, `delete`)
VALUES (3, @pos_parent, 1, 0, 0, 0), (3, @settings_menu, 1, 0, 0, 0),
       (3, @trans_menu, 1, 0, 0, 0), (3, @settle_menu, 1, 0, 0, 0);

SET SQL_SAFE_UPDATES = 1;

-- DOWN
-- To rollback: remove POS menus, access entries, module, and setting
-- DELETE a FROM access a INNER JOIN menu m ON a.id_menu = m.id_menu WHERE m.parent = (SELECT id_menu FROM menu WHERE label = 'POS Management' AND link = '#' LIMIT 1);
-- DELETE FROM access WHERE id_menu = (SELECT id_menu FROM menu WHERE label = 'POS Management' AND link = '#' LIMIT 1);
-- DELETE FROM menu WHERE parent = (SELECT id_menu FROM (SELECT id_menu FROM menu WHERE label = 'POS Management' AND link = '#' LIMIT 1) tmp);
-- DELETE FROM menu WHERE label = 'POS Management' AND link = '#';
-- DELETE FROM modules WHERE m_code = 'POS';
-- DELETE FROM ret_settings WHERE name = 'pay_by_pos';
