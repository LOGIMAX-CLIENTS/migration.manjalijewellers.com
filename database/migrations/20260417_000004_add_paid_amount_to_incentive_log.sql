-- ============================================================
-- Migration: Add paid_amount to ret_emp_incentive_log
-- Description: Supports partial incentive payments
-- Date: 17-04-2026
-- ============================================================
-- UP
ALTER TABLE `ret_emp_incentive_log`
    ADD COLUMN `paid_amount` DECIMAL(12,4) NOT NULL DEFAULT 0.0000 COMMENT 'Partial/full amount paid' AFTER `incentive_amount`,
    ADD COLUMN `remarks` VARCHAR(255) DEFAULT NULL COMMENT 'Payment remarks' AFTER `paid_by`;
