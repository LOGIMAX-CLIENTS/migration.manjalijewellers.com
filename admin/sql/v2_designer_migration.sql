-- =====================================================================
-- Print Template Designer V2 — Database Migration
-- Run this SQL against your development database
-- =====================================================================

-- 1. MC/VA Configuration per client/branch
CREATE TABLE IF NOT EXISTS `print_template_mc_va_config` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `id_branch` INT DEFAULT NULL COMMENT 'NULL = All branches',
    `document_type` TINYINT NOT NULL COMMENT 'Template category ID (1=Sales, 2=Sales&Purchase, etc.)',
    `mc_mode` ENUM('per_gram','per_piece','total','percentage','none') DEFAULT 'per_gram',
    `mc_label` VARCHAR(50) DEFAULT 'MC',
    `va_mode` ENUM('percentage','weight','per_gram','total','none') DEFAULT 'percentage',
    `va_label` VARCHAR(50) DEFAULT 'VA',
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_branch_doctype` (`id_branch`, `document_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Default templates per category (pre-built JSON block configs)
CREATE TABLE IF NOT EXISTS `print_template_defaults` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `template_category` TINYINT NOT NULL COMMENT 'Category ID',
    `template_name` VARCHAR(100) NOT NULL,
    `blocks_json` LONGTEXT NOT NULL COMMENT 'Full block config JSON (same format as gjs_data)',
    `preview_thumbnail` VARCHAR(255) DEFAULT NULL,
    `is_system` TINYINT(1) DEFAULT 1 COMMENT '1=System default, 0=User-created',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Enhance placeholders table for richer field metadata (safe ALTERs - skip if cols already exist)
-- Check if columns exist before adding them:
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_NAME = 'print_template_placeholders' AND COLUMN_NAME = 'field_type');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `print_template_placeholders` ADD COLUMN `field_type` ENUM(''text'',''number'',''currency'',''date'',''boolean'',''image'',''array'') DEFAULT ''text'' AFTER `placeholder_label`',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_NAME = 'print_template_placeholders' AND COLUMN_NAME = 'is_loop_field');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `print_template_placeholders` ADD COLUMN `is_loop_field` TINYINT(1) DEFAULT 0 AFTER `field_type`',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_NAME = 'print_template_placeholders' AND COLUMN_NAME = 'parent_loop');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `print_template_placeholders` ADD COLUMN `parent_loop` VARCHAR(50) DEFAULT NULL AFTER `is_loop_field`',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 4. Ensure print_templates has margin columns (safe ALTERs)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_NAME = 'print_templates' AND COLUMN_NAME = 'margin_top');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `print_templates` ADD COLUMN `margin_top` INT DEFAULT 10 AFTER `page_orientation`, ADD COLUMN `margin_right` INT DEFAULT 10 AFTER `margin_top`, ADD COLUMN `margin_bottom` INT DEFAULT 10 AFTER `margin_right`, ADD COLUMN `margin_left` INT DEFAULT 10 AFTER `margin_bottom`',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 5. Seed default MC/VA config for Sales category
INSERT IGNORE INTO `print_template_mc_va_config` (`id_branch`, `document_type`, `mc_mode`, `mc_label`, `va_mode`, `va_label`) VALUES
(NULL, 1, 'per_gram', 'MC', 'percentage', 'VA %'),
(NULL, 2, 'per_gram', 'MC', 'percentage', 'VA %'),
(NULL, 3, 'per_gram', 'MC', 'percentage', 'VA %'),
(NULL, 5, 'per_gram', 'MC', 'percentage', 'VA %'),
(NULL, 9, 'per_gram', 'MC', 'percentage', 'VA %');

-- 6. Seed loop field placeholders for items table
INSERT IGNORE INTO `print_template_placeholders` (`category`, `placeholder_key`, `placeholder_label`, `placeholder_group`, `sample_value`, `display_order`) VALUES
-- Item loop fields (billing category)
('billing', 'sno',         'S.No',         'Items', '1', 20),
('billing', 'description', 'Description',  'Items', 'Gold Ring 22KT', 21),
('billing', 'hsn_code',    'HSN Code',     'Items', '711319', 22),
('billing', 'purity',      'Purity',       'Items', '916', 23),
('billing', 'qty',         'Qty/PCS',      'Items', '1', 24),
('billing', 'gross_wt',    'Gross Wt',     'Items', '5.200', 25),
('billing', 'net_wt',      'Net Wt',       'Items', '5.000', 26),
('billing', 'va_percent',  'VA %',         'Items', '5.00', 27),
('billing', 'va_content',  'VA Content',   'Items', '916', 28),
('billing', 'mc',          'MC',           'Items', '500.00', 29),
('billing', 'rate',        'Rate',         'Items', '5800', 30),
('billing', 'amount',      'Amount',       'Items', '29500.00', 31),
-- Payment fields
('billing', 'cash_amount',    'Cash Amount',    'Payment', '5000.00', 40),
('billing', 'card_amount',    'Card Amount',    'Payment', '5300.00', 41),
('billing', 'upi_amount',     'UPI Amount',     'Payment', '0.00', 42),
('billing', 'cheque_amount',  'Cheque Amount',  'Payment', '0.00', 43),
('billing', 'balance_amount', 'Balance Amount', 'Payment', '0.00', 44),
('billing', 'paid_amount',    'Paid Amount',    'Payment', '10300.00', 45),
-- Tax fields
('billing', 'sgst_amount',    'SGST Amount',    'Tax', '150.00', 50),
('billing', 'cgst_amount',    'CGST Amount',    'Tax', '150.00', 51),
('billing', 'igst_amount',    'IGST Amount',    'Tax', '0.00', 52),
('billing', 'round_off',      'Round Off',      'Tax', '0.50', 53),
('billing', 'net_payable',    'Net Payable',    'Tax', '10300.00', 54),
('billing', 'amount_in_words','Amount in Words','Tax', 'Ten Thousand Three Hundred Only', 55),
-- Company fields
('billing', 'company_name',    'Company Name',    'Company', 'Demo Jewellers', 60),
('billing', 'company_address', 'Company Address', 'Company', '456 Gold St', 61),
('billing', 'company_mobile',  'Company Mobile',  'Company', '9988776655', 62),
('billing', 'company_gstin',   'Company GSTIN',   'Company', '33XYZCO1234E1Z6', 63);
