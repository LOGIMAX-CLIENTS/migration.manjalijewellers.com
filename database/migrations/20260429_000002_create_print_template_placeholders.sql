-- Migration: Create table print_template_placeholders
-- Module: Print Template Designer — Variable registry for field picker UI
-- Author: Antigravity
-- Safe: Uses IF NOT EXISTS

-- UP
CREATE TABLE IF NOT EXISTS `print_template_placeholders` (
    `id`            INT(11) NOT NULL AUTO_INCREMENT,
    `field_key`     VARCHAR(100) NOT NULL COMMENT 'Variable key e.g. customer_name, invoice_no',
    `field_label`   VARCHAR(255) DEFAULT NULL COMMENT 'Human-readable label',
    `category`      VARCHAR(50) DEFAULT NULL COMMENT 'Group: customer, billing, payment, etc.',
    `field_type`    VARCHAR(20) DEFAULT 'text' COMMENT 'text, number, date, image, boolean',
    `is_loop_field` TINYINT(1) DEFAULT 0 COMMENT '1 = belongs to a loop/table array',
    `parent_loop`   VARCHAR(100) DEFAULT NULL COMMENT 'Parent loop key e.g. items, purchase_items',
    `created_at`    DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_field_key` (`field_key`),
    KEY `idx_category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Variable registry for print template field picker UI';