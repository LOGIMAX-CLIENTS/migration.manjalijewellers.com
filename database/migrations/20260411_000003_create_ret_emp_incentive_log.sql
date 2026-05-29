-- ============================================================
-- Migration: Create ret_emp_incentive_log table
-- Description: Transaction-level log of calculated employee
--              sales incentives. One row per sale item that
--              matched an incentive rule. Used for reporting.
-- ============================================================
-- UP
CREATE TABLE IF NOT EXISTS `ret_emp_incentive_log` (
    `id`               INT(11)        NOT NULL AUTO_INCREMENT,
    `bill_id`          INT(11)        NOT NULL COMMENT 'FK ret_billing.bill_id',
    `bill_det_id`      INT(11)        NOT NULL COMMENT 'FK ret_bill_details.bill_det_id',
    `id_employee`      INT(11)        NOT NULL COMMENT 'Selling employee',
    `config_id`        INT(11)        DEFAULT NULL COMMENT 'FK ret_emp_incentive_config.id (NULL if fallback)',
    `id_branch`        INT(11)        NOT NULL,
    `id_category`      INT(11)        DEFAULT NULL,
    `id_product`       INT(11)        DEFAULT NULL,
    `id_design`        INT(11)        DEFAULT NULL,
    `id_sub_design`    INT(11)        DEFAULT NULL,
    `calc_basis`       TINYINT(1)     NOT NULL COMMENT '1=Per Gram, 2=Per Carat, 3=Percentage',
    `base_qty`         DECIMAL(12,4)  NOT NULL DEFAULT 0.0000 COMMENT 'Gram / Carat / Sales Value used',
    `incentive_rate`   DECIMAL(12,4)  NOT NULL DEFAULT 0.0000 COMMENT 'Rate applied',
    `incentive_amount` DECIMAL(12,4)  NOT NULL DEFAULT 0.0000 COMMENT 'Final calculated incentive',
    `bill_date`        DATE           NOT NULL,
    `created_on`       DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_bill`     (`bill_id`),
    KEY `idx_bill_det` (`bill_det_id`),
    KEY `idx_employee` (`id_employee`),
    KEY `idx_config`   (`config_id`),
    KEY `idx_branch`   (`id_branch`),
    KEY `idx_bill_date`(`bill_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
