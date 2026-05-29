-- Migration: Create table print_template_computed_vars
-- Module: Print Template Designer — User-defined computed/formula variables
-- Author: Antigravity
-- Safe: Uses IF NOT EXISTS

-- UP
CREATE TABLE `print_template_computed_vars` (
    `id`            INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_template`   INT(11) UNSIGNED NOT NULL COMMENT 'FK → print_templates.id_template',
    `var_name`      VARCHAR(100) NOT NULL COMMENT 'Variable name (without braces), e.g. total_after_discount',
    `var_label`     VARCHAR(150) NOT NULL COMMENT 'Human-friendly label, e.g. Total After Discount',
    `formula`       TEXT NOT NULL COMMENT 'Arithmetic formula using {{var}} placeholders, e.g. {{sub_total}} - {{discount}}',
    `created_at`    DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_template_var` (`id_template`, `var_name`),
    KEY `idx_template` (`id_template`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='User-defined computed variables for print templates';
