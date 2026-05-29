-- ============================================================
-- Migration: Add performance indexes to ret_emp_incentive_log
-- Description: Unique constraint + covering indexes for reports
-- Date: 09-05-2026
-- ============================================================
-- UP
ALTER TABLE `ret_emp_incentive_log`
    ADD UNIQUE KEY `uk_bill_item_emp` (`bill_id`, `bill_det_id`, `id_employee`);

ALTER TABLE `ret_emp_incentive_log`
    ADD KEY `idx_date_branch_paid` (`bill_date`, `id_branch`, `is_paid`);

ALTER TABLE `ret_emp_incentive_log`
    ADD KEY `idx_report_filter` (`id_branch`, `id_employee`, `bill_date`, `is_paid`);
