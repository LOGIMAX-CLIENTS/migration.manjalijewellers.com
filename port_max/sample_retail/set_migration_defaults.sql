-- ============================================================
-- Port Max Migration: Set DB Column Defaults
-- Purpose: Ensure missing fields auto-fill during import
-- Date: 2026-03-28
-- ============================================================

-- ============================================================
-- SECTION 1: CRITICAL FIXES (NOT NULL columns that will fail)
-- ============================================================

-- ret_purity: created_by is NOT NULL with no default
ALTER TABLE `ret_purity` ALTER COLUMN `created_by` SET DEFAULT 1;

-- ret_taging: id_lot_inward_detail is NOT NULL with no default
-- Making nullable since migration data won't have lot linkage
ALTER TABLE `ret_taging` MODIFY COLUMN `id_lot_inward_detail` INT NULL DEFAULT NULL;

-- ============================================================
-- SECTION 2: BEST PRACTICE (auto-fill created_on / created_by)
-- ============================================================

-- ret_category
ALTER TABLE `ret_category` ALTER COLUMN `created_on` SET DEFAULT (CURRENT_TIMESTAMP);
ALTER TABLE `ret_category` ALTER COLUMN `created_by` SET DEFAULT 1;

-- ret_product_master
ALTER TABLE `ret_product_master` ALTER COLUMN `created_time` SET DEFAULT (CURRENT_TIMESTAMP);
ALTER TABLE `ret_product_master` ALTER COLUMN `create_by` SET DEFAULT 1;

-- ret_design_master
ALTER TABLE `ret_design_master` ALTER COLUMN `created_on` SET DEFAULT (CURRENT_TIMESTAMP);
ALTER TABLE `ret_design_master` ALTER COLUMN `created_by` SET DEFAULT 1;

-- ret_sub_design_master
ALTER TABLE `ret_sub_design_master` ALTER COLUMN `created_on` SET DEFAULT (CURRENT_TIMESTAMP);
ALTER TABLE `ret_sub_design_master` ALTER COLUMN `created_by` SET DEFAULT 1;

-- ret_section
ALTER TABLE `ret_section` ALTER COLUMN `date_add` SET DEFAULT (CURRENT_TIMESTAMP);
ALTER TABLE `ret_section` ALTER COLUMN `created_by` SET DEFAULT 1;

-- ret_purity (created_on)
ALTER TABLE `ret_purity` ALTER COLUMN `created_on` SET DEFAULT (CURRENT_TIMESTAMP);

-- ret_size
ALTER TABLE `ret_size` ALTER COLUMN `created_on` SET DEFAULT (CURRENT_TIMESTAMP);
ALTER TABLE `ret_size` ALTER COLUMN `created_by` SET DEFAULT 1;

-- ret_stone
ALTER TABLE `ret_stone` ALTER COLUMN `created_on` SET DEFAULT (CURRENT_TIMESTAMP);
ALTER TABLE `ret_stone` ALTER COLUMN `created_by` SET DEFAULT 1;

-- ret_charges
ALTER TABLE `ret_charges` ALTER COLUMN `created_on` SET DEFAULT (CURRENT_TIMESTAMP);
ALTER TABLE `ret_charges` ALTER COLUMN `created_by` SET DEFAULT 1;

-- ret_karigar
ALTER TABLE `ret_karigar` ALTER COLUMN `createdon` SET DEFAULT (CURRENT_TIMESTAMP);
ALTER TABLE `ret_karigar` ALTER COLUMN `createdby` SET DEFAULT 1;

-- ret_taging
ALTER TABLE `ret_taging` ALTER COLUMN `created_time` SET DEFAULT (CURRENT_TIMESTAMP);
ALTER TABLE `ret_taging` ALTER COLUMN `created_by` SET DEFAULT 1;

-- ============================================================
-- VERIFICATION: Check that defaults are set correctly
-- ============================================================
SELECT TABLE_NAME, COLUMN_NAME, COLUMN_DEFAULT, IS_NULLABLE
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME IN (
    'ret_category','ret_product_master','ret_design_master',
    'ret_sub_design_master','ret_section','ret_purity',
    'ret_size','ret_stone','ret_charges','ret_karigar','ret_taging'
)
AND COLUMN_NAME IN (
    'created_on','created_time','created_by','create_by','createdby',
    'createdon','date_add','id_lot_inward_detail'
)
ORDER BY TABLE_NAME, COLUMN_NAME;
