-- Migration: Create table print_template_defaults
-- Module: Print Template Designer — Default template block definitions per category
-- Author: Antigravity
-- Safe: Uses IF NOT EXISTS

-- UP
CREATE TABLE `print_template_defaults` (
    `id`                 INT(11) NOT NULL AUTO_INCREMENT,
    `template_category`  VARCHAR(50) NOT NULL COMMENT 'Category: 1=Billing, 2=Estimation, etc.',
    `template_label`     VARCHAR(150) NOT NULL COMMENT 'Display name e.g. Standard Sales Invoice',
    `template_json`      LONGTEXT DEFAULT NULL COMMENT 'Default Konva canvas JSON',
    `template_html`      LONGTEXT DEFAULT NULL COMMENT 'Default GrapesJS HTML',
    `template_css`       TEXT DEFAULT NULL COMMENT 'Default GrapesJS CSS',
    `is_system`          TINYINT(1) DEFAULT 0 COMMENT '1 = system-provided, 0 = user-created',
    `created_at`         DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at`         DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_category` (`template_category`),
    KEY `idx_system` (`is_system`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Default template blocks for the print template designer';
