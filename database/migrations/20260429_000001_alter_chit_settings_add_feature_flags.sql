-- Migration: Add feature_flags column to chit_settings
-- Date: 2026-04-29
-- Description: Stores JSON feature flag configuration for controlling module-level feature toggles

ALTER TABLE `chit_settings`
ADD COLUMN `feature_flags` JSON NULL DEFAULT NULL
COMMENT 'JSON object storing feature flag toggles for module-level feature control';
