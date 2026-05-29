-- ============================================================
-- Migration: Add versioning columns to ret_emp_incentive_config
-- Description: Adds parent_id, deleted_on, deleted_by to support
--              version-based editing. When a config is modified,
--              the old record is soft-deleted (status=2) and a
--              new record is created with parent_id pointing to
--              the original. This preserves historical config_id
--              references in ret_emp_incentive_log.
-- Date: 09-05-2026
-- ============================================================
-- UP
ALTER TABLE `ret_emp_incentive_config`
    ADD COLUMN `parent_id`  INT(11)  DEFAULT NULL COMMENT 'Previous version config ID (for version-based edit)' AFTER `id`,
    ADD COLUMN `deleted_on` DATETIME DEFAULT NULL COMMENT 'Soft-delete timestamp when config is versioned' AFTER `updated_on`,
    ADD COLUMN `deleted_by` INT(11)  DEFAULT NULL COMMENT 'User who versioned/deleted this config' AFTER `deleted_on`;

-- Add index for parent lookup (find lineage of a config)
ALTER TABLE `ret_emp_incentive_config`
    ADD KEY `idx_parent_id` (`parent_id`);

-- DOWN (rollback)
-- ALTER TABLE `ret_emp_incentive_config`
--     DROP COLUMN `parent_id`,
--     DROP COLUMN `deleted_on`,
--     DROP COLUMN `deleted_by`,
--     DROP KEY `idx_parent_id`;
