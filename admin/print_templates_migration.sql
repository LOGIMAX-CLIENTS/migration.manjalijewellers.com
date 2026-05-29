-- Database Schema for Print Format Designer

-- Table 1: print_templates
CREATE TABLE IF NOT EXISTS `print_templates` (
  `id_template` INT AUTO_INCREMENT PRIMARY KEY,
  `template_code` VARCHAR(50) NOT NULL UNIQUE,
  `template_name` VARCHAR(100) NOT NULL,
  `template_category` ENUM('billing','receipt','bond','estimation','order','branch_transfer','gift_voucher','qr_tag','purchase','custom') NOT NULL,
  `gjs_data` LONGTEXT COMMENT 'GrapesJS JSON data for editor state',
  `template_html` LONGTEXT COMMENT 'Compiled HTML output',
  `template_css` TEXT COMMENT 'Compiled CSS output',
  `paper_size` ENUM('A4','A5','Letter','Thermal-58mm','Thermal-80mm','Custom') DEFAULT 'A4',
  `paper_width` VARCHAR(20) DEFAULT NULL COMMENT 'Custom width e.g. 80mm',
  `paper_height` VARCHAR(20) DEFAULT NULL COMMENT 'Custom height e.g. 297mm',
  `page_orientation` ENUM('portrait','landscape') DEFAULT 'portrait',
  `margin_top` INT DEFAULT 10,
  `margin_right` INT DEFAULT 10,
  `margin_bottom` INT DEFAULT 10,
  `margin_left` INT DEFAULT 10,
  `is_default` TINYINT(1) DEFAULT 0,
  `is_active` TINYINT(1) DEFAULT 1,
  `id_branch` INT DEFAULT NULL COMMENT 'NULL = all branches',
  `created_by` INT,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_category` (`template_category`),
  INDEX `idx_branch` (`id_branch`),
  INDEX `idx_default` (`template_category`, `is_default`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table 2: print_template_placeholders
CREATE TABLE IF NOT EXISTS `print_template_placeholders` (
  `id_placeholder` INT AUTO_INCREMENT PRIMARY KEY,
  `category` VARCHAR(50) NOT NULL,
  `placeholder_key` VARCHAR(100) NOT NULL,
  `placeholder_label` VARCHAR(150) NOT NULL,
  `placeholder_group` VARCHAR(50) DEFAULT 'General',
  `sample_value` VARCHAR(255),
  `description` VARCHAR(255),
  `display_order` INT DEFAULT 0,
  UNIQUE KEY `unique_placeholder` (`category`, `placeholder_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table 3: print_template_settings
CREATE TABLE IF NOT EXISTS `print_template_settings` (
  `id_setting` INT AUTO_INCREMENT PRIMARY KEY,
  `id_template` INT NOT NULL,
  `setting_key` VARCHAR(100) NOT NULL,
  `setting_value` TEXT,
  `setting_label` VARCHAR(100),
  `setting_type` ENUM('boolean','text','number','color','image') DEFAULT 'text',
  FOREIGN KEY (`id_template`) REFERENCES `print_templates`(`id_template`) ON DELETE CASCADE,
  UNIQUE KEY `unique_setting` (`id_template`, `setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed Data: Placeholders for Billing
INSERT IGNORE INTO `print_template_placeholders` (`category`, `placeholder_key`, `placeholder_label`, `placeholder_group`, `sample_value`, `display_order`) VALUES
('billing', 'company_name', 'Company Name', 'Company', 'ABC Jewellers', 1),
('billing', 'company_logo', 'Company Logo', 'Company', '/assets/images/logo.png', 2),
('billing', 'branch_name', 'Branch Name', 'Branch', 'Main Branch', 3),
('billing', 'branch_address', 'Branch Address', 'Branch', '123 Main Street', 4),
('billing', 'branch_phone', 'Branch Phone', 'Branch', '+91 9876543210', 5),
('billing', 'branch_gstin', 'Branch GSTIN', 'Branch', '29ABCDE1234F1ZK', 6),
('billing', 'bill_no', 'Bill Number', 'Invoice', 'INV-2024-001', 7),
('billing', 'bill_date', 'Bill Date', 'Invoice', '24-Dec-2024', 8),
('billing', 'customer_name', 'Customer Name', 'Customer', 'John Doe', 9),
('billing', 'customer_mobile', 'Customer Mobile', 'Customer', '9876543210', 10),
('billing', 'customer_address', 'Customer Address', 'Customer', '456 Customer Street', 11),
('billing', 'items_table', 'Items Table', 'Items', '[TABLE]', 12),
('billing', 'subtotal', 'Subtotal', 'Totals', '50,000.00', 13),
('billing', 'cgst', 'CGST Amount', 'Totals', '750.00', 14),
('billing', 'sgst', 'SGST Amount', 'Totals', '750.00', 15),
('billing', 'grand_total', 'Grand Total', 'Totals', '51,500.00', 16),
('billing', 'amount_in_words', 'Amount in Words', 'Totals', 'Fifty One Thousand Five Hundred Only', 17);
