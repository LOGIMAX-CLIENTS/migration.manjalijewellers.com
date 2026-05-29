-- Migration: Create table print_templates
-- Module: Print Template Designer (V1 GrapesJS + V2 Konva)
-- Author: Antigravity
-- Safe: Uses IF NOT EXISTS

-- UP
CREATE TABLE IF NOT EXISTS `print_templates` (
  `id_print_template` INT(11) NOT NULL AUTO_INCREMENT,
  `template_code` VARCHAR(50) DEFAULT NULL COMMENT 'Short code e.g. SALES_BILL, ORDER_RECEIPT',
  `template_name` VARCHAR(150) NOT NULL COMMENT 'User-friendly template name',
  `template_category` VARCHAR(50) DEFAULT 'billing' COMMENT 'Category: billing, purchase, report, etc.',
  `paper_size` VARCHAR(20) DEFAULT 'A4' COMMENT 'A4, A5, Letter, Thermal-58mm, Thermal-80mm, Custom',
  `page_orientation` VARCHAR(20) DEFAULT 'portrait' COMMENT 'portrait or landscape',
  `id_branch` INT(11) DEFAULT NULL COMMENT 'Branch-specific template (NULL = all branches)',
  `template_html` LONGTEXT DEFAULT NULL COMMENT 'V1: Raw HTML template content',
  `template_css` TEXT DEFAULT NULL COMMENT 'V1: Custom CSS for HTML template',
  `gjs_data` LONGTEXT DEFAULT NULL COMMENT 'V2: Konva designer JSON (blocks, canvas, elements)',
  `is_default` TINYINT(1) DEFAULT 0 COMMENT '1 = Default template for its category',
  `is_active` TINYINT(1) DEFAULT 1 COMMENT '1 = Active, 0 = Archived/versioned',
  `version` INT(11) DEFAULT 1 COMMENT 'Version number (incremented on save)',
  `parent_template_id` INT(11) DEFAULT NULL COMMENT 'Points to original template if versioned',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_print_template`),
  KEY `idx_category` (`template_category`),
  KEY `idx_branch` (`id_branch`),
  KEY `idx_active_default` (`is_active`, `is_default`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Print template definitions for V1 GrapesJS and V2 Konva designer';

-- DOWN
-- DROP TABLE IF EXISTS `print_templates`;
