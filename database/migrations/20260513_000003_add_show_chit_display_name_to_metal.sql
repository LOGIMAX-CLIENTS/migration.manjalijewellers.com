-- Migration: Add show_chit and display_name columns to metal table
-- Date: 13-05-2026
-- Branch: feature/commodity-based-gateway
-- Safe: ADD COLUMN — Non-destructive; existing rows will default to 0 / NULL

-- UP
ALTER TABLE `metal`
  ADD COLUMN `show_chit`    TINYINT(1) NOT NULL DEFAULT 0 COMMENT '0 -> No, 1 -> Yes (Show in Chit / Scheme display)' AFTER `metal_status`,
  ADD COLUMN `display_name` VARCHAR(100) DEFAULT NULL COMMENT 'Custom display name shown when show_chit = 1' AFTER `show_chit`;

-- Enable show_chit for the first two metals (Gold & Silver by default)
UPDATE `metal` SET `show_chit` = 1
WHERE `id_metal` IN (
  SELECT `id_metal` FROM (
    SELECT `id_metal` FROM `metal` ORDER BY `id_metal` ASC LIMIT 2
  ) AS t
);

-- DOWN
-- ALTER TABLE `metal`
--   DROP COLUMN `display_name`,
--   DROP COLUMN `show_chit`;
