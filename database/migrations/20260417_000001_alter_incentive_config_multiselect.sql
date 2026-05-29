-- ============================================================
-- Migration: Alter ret_emp_incentive_config for multi-select scope
-- Description: Changes scope columns (id_branch, id_category,
--              id_product, id_design, id_sub_design) from INT
--              to VARCHAR(500) to support comma-separated
--              multi-select values. Also adds id_stock_age_master
--              column for Age Based rate type.
-- Date: 17-04-2026
-- ============================================================

-- Step 1: Alter scope columns to VARCHAR for multi-select
-- UP
ALTER TABLE `ret_emp_incentive_config`
    MODIFY COLUMN `id_branch`     VARCHAR(500) DEFAULT NULL COMMENT 'Comma-separated branch IDs. NULL = All Branches',
    MODIFY COLUMN `id_category`   VARCHAR(500) DEFAULT NULL COMMENT 'Comma-separated category IDs. NULL = All',
    MODIFY COLUMN `id_product`    VARCHAR(500) DEFAULT NULL COMMENT 'Comma-separated product IDs. NULL = All',
    MODIFY COLUMN `id_design`     VARCHAR(500) DEFAULT NULL COMMENT 'Comma-separated design IDs. NULL = All',
    MODIFY COLUMN `id_sub_design` VARCHAR(500) DEFAULT NULL COMMENT 'Comma-separated sub-design IDs. NULL = All';

-- Step 2: Add id_stock_age_master column if not exists
-- Used when rate_type = 4 (Age Based)
ALTER TABLE `ret_emp_incentive_config`
    ADD COLUMN `id_stock_age_master` INT(11) DEFAULT NULL COMMENT 'FK ret_stock_age_master.id - for Age Based rate type' AFTER `id_sub_design`;
