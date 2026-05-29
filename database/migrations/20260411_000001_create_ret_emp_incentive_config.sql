-- ============================================================
-- Migration: Create ret_emp_incentive_config table
-- Description: Master table for employee sales incentive rules.
--              Admin configures rules based on Branch, Category,
--              Product, Design, Sub-Design with calculation basis
--              (Per Gram / Per Carat / % of Sales Value) and
--              rate types (Fixed / Weight Range / Carat Range).
-- ============================================================
-- UP
CREATE TABLE IF NOT EXISTS `ret_emp_incentive_config` (
    `id`              INT(11)        NOT NULL AUTO_INCREMENT,
    `id_branch`       INT(11)        DEFAULT NULL COMMENT 'NULL = All Branches',
    `id_category`     INT(11)        DEFAULT NULL COMMENT 'FK ret_category.id_ret_category. NULL = All',
    `id_product`      INT(11)        DEFAULT NULL COMMENT 'FK ret_product_master.pro_id. NULL = All',
    `id_design`       INT(11)        DEFAULT NULL COMMENT 'FK ret_design_master.design_no. NULL = All',
    `id_sub_design`   INT(11)        DEFAULT NULL COMMENT 'FK ret_sub_design_master.id. NULL = All',
    `calc_basis`      TINYINT(1)     NOT NULL DEFAULT 1 COMMENT '1=Per Gram, 2=Per Carat, 3=Percentage of Total Sales Value',
    `rate_type`       TINYINT(1)     NOT NULL DEFAULT 1 COMMENT '1=Fixed, 2=Weight Range, 3=Carat Range',
    `incentive_value` DECIMAL(12,4)  NOT NULL DEFAULT 0.0000 COMMENT 'Used when rate_type=1 (Fixed). Rate per gram / carat / percentage',
    `status`          TINYINT(1)     NOT NULL DEFAULT 1 COMMENT '1=Active, 0=Inactive',
    `created_by`      INT(11)        NOT NULL,
    `created_on`      DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_by`      INT(11)        DEFAULT NULL,
    `updated_on`      DATETIME       DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_branch`      (`id_branch`),
    KEY `idx_category`    (`id_category`),
    KEY `idx_product`     (`id_product`),
    KEY `idx_design`      (`id_design`),
    KEY `idx_sub_design`  (`id_sub_design`),
    KEY `idx_status`      (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;