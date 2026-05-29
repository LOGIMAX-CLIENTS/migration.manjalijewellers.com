-- Migration: Add id_metal (Commodity) column to gateway table
-- Date: 14-05-2026
-- Branch: feature/commodity-based-gateway
-- Safe: ADD COLUMN — Non-destructive; existing rows will default to NULL

-- UP
ALTER TABLE `gateway`
  ADD COLUMN `id_metal` INT(11) DEFAULT NULL COMMENT 'FK -> metal.id_metal (NULL = not commodity-specific)' AFTER `description`;

-- DOWN
-- ALTER TABLE `gateway`
--   DROP COLUMN `id_metal`;
