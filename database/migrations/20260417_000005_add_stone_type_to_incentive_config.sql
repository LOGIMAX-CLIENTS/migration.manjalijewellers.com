-- ============================================================
-- Migration: Add stone_type to incentive config
-- Description: Stores selected stone type for Carat Range rate type
-- Date: 17-04-2026
-- ============================================================
-- UP
ALTER TABLE `ret_emp_incentive_config`
    ADD COLUMN `stone_type` INT(11) DEFAULT NULL COMMENT 'Stone type ID from ret_stone_type. Required when rate_type = 3 (Carat Range)' AFTER `rate_type`;

-- DOWN (rollback)
-- ALTER TABLE `ret_emp_incentive_config` DROP COLUMN IF EXISTS `stone_type`;
