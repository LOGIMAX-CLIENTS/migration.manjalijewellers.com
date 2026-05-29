-- ============================================================
-- Migration: Add id_metal column to ret_emp_incentive_config
-- Description: Adds Metal filter (multi-select) to incentive
--              config scope. Stored as comma-separated IDs.
-- Date: 17-04-2026
-- ============================================================

-- Add id_metal column after id_branch (multi-select, comma-separated)
-- UP
ALTER TABLE `ret_emp_incentive_config`
    ADD COLUMN `id_metal` VARCHAR(500) DEFAULT NULL COMMENT 'Comma-separated metal IDs. NULL = All Metals' AFTER `id_branch`;