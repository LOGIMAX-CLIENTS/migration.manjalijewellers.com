-- ============================================================
-- Dynamic Print Designer — Database Migration
-- Run this SQL in your retail_dev2 database
-- ============================================================

CREATE TABLE IF NOT EXISTS `print_designer_templates` (
  `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(150) NOT NULL,
  `table_name` VARCHAR(150) NOT NULL,
  `layout_json` LONGTEXT COMMENT 'Fabric.js canvas JSON data',
  `page_size` VARCHAR(30) NOT NULL DEFAULT 'A4',
  `orientation` VARCHAR(15) NOT NULL DEFAULT 'portrait',
  `page_width_mm` DECIMAL(8,2) DEFAULT NULL COMMENT 'Custom width in mm',
  `page_height_mm` DECIMAL(8,2) DEFAULT NULL COMMENT 'Custom height in mm',
  `margin_top` INT(4) DEFAULT 5,
  `margin_right` INT(4) DEFAULT 5,
  `margin_bottom` INT(4) DEFAULT 5,
  `margin_left` INT(4) DEFAULT 5,
  `is_default` TINYINT(1) NOT NULL DEFAULT 0,
  `thumbnail` LONGTEXT DEFAULT NULL COMMENT 'Base64 canvas thumbnail',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_table` (`table_name`),
  INDEX `idx_default` (`table_name`, `is_default`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
