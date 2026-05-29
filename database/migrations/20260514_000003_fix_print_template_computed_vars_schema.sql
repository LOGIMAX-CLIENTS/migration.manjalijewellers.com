-- Migration: Fix UNSIGNED mismatch in print_template_computed_vars
-- Module: Print Template Designer
-- Author: Antigravity
-- Date: 2026-05-14
-- Reason: Synchronizes the id_template column type with the primary table to ensure join compatibility and potential foreign key support.

-- UP
ALTER TABLE `print_template_computed_vars` MODIFY `id_template` INT(11) NOT NULL COMMENT 'FK → print_templates.id_template';
