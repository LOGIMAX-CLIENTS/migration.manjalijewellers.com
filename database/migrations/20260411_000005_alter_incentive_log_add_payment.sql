-- ============================================================
-- Migration: Add payment tracking columns to ret_emp_incentive_log
-- Description: Adds is_paid, paid_date, paid_by columns for
--              tracking whether incentive has been given to employee.
-- ============================================================
-- UP
ALTER TABLE `ret_emp_incentive_log`
  ADD COLUMN `is_paid` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '0=Pending, 1=Given' AFTER `created_on`,
  ADD COLUMN `paid_date` DATETIME DEFAULT NULL COMMENT 'Date incentive was given' AFTER `is_paid`,
  ADD COLUMN `paid_by` INT(11) DEFAULT NULL COMMENT 'User who marked as given' AFTER `paid_date`;
