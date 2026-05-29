-- Migration: Safety net — create print_template tables if they were missed
-- Module: Print Template Designer
-- Author: Antigravity
-- Date: 2026-05-09
-- Reason: Migrations 000001 and 000007 were empty (no SQL) when first deployed.
--         The migration runner marked them as "applied" but the tables were never created.
--         This safety migration ensures all print_template tables exist regardless of
--         whether the original migrations ran correctly or were no-ops.
-- Safe: All statements use IF NOT EXISTS — harmless on databases where tables already exist.

-- UP

-- 1. print_templates (originally in 20260429_000001 which was empty)
CREATE TABLE IF NOT EXISTS `print_templates` (
    `id_template`        INT(11) NOT NULL AUTO_INCREMENT,
    `template_code`      VARCHAR(100) DEFAULT NULL,
    `code`               VARCHAR(100) DEFAULT NULL,
    `template_name`      VARCHAR(255) DEFAULT NULL,
    `template_category`  VARCHAR(50) DEFAULT '0',
    `gjs_data`           LONGTEXT DEFAULT NULL COMMENT 'Konva JSON or GrapesJS data',
    `header_gjs_data`    LONGTEXT DEFAULT NULL,
    `body_gjs_data`      LONGTEXT DEFAULT NULL,
    `footer_gjs_data`    LONGTEXT DEFAULT NULL,
    `header_height`      VARCHAR(20) DEFAULT NULL,
    `template_html`      LONGTEXT DEFAULT NULL,
    `template_css`       TEXT DEFAULT NULL,
    `config_json`        LONGTEXT DEFAULT NULL,
    `paper_size`         VARCHAR(20) DEFAULT 'A4',
    `paper_width`        INT(11) DEFAULT NULL,
    `paper_height`       INT(11) DEFAULT NULL,
    `page_orientation`   VARCHAR(20) DEFAULT 'portrait',
    `margin_top`         VARCHAR(20) DEFAULT '0',
    `margin_right`       VARCHAR(20) DEFAULT '0',
    `margin_bottom`      VARCHAR(20) DEFAULT '0',
    `margin_left`        VARCHAR(20) DEFAULT '0',
    `is_default`         TINYINT(1) DEFAULT 0,
    `is_active`          TINYINT(1) DEFAULT 0,
    `version_number`     INT(11) DEFAULT 1,
    `parent_template_id` INT(11) DEFAULT NULL,
    `id_branch`          INT(11) DEFAULT NULL,
    `created_by`         INT(11) DEFAULT NULL,
    `created_at`         DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at`         DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_template`),
    KEY `idx_code` (`code`),
    KEY `idx_category` (`template_category`),
    KEY `idx_branch` (`id_branch`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Print template definitions for billing, estimation, etc.';

-- 2. print_template_placeholders (originally in 20260429_000002 which had ALTER instead of CREATE)
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

-- 3. print_template_custom_blocks (originally in 20260429_000007 which was empty)
CREATE TABLE IF NOT EXISTS `print_template_custom_blocks` (
    `id`            INT(11) NOT NULL AUTO_INCREMENT,
    `id_template`   INT(11) DEFAULT NULL COMMENT 'FK to print_templates.id_template, NULL = global block',
    `block_name`    VARCHAR(150) NOT NULL COMMENT 'Display name of the reusable block',
    `block_html`    LONGTEXT DEFAULT NULL COMMENT 'Saved HTML content',
    `block_css`     TEXT DEFAULT NULL COMMENT 'Saved CSS for the block',
    `block_json`    LONGTEXT DEFAULT NULL COMMENT 'Konva JSON for V2 blocks',
    `block_category` VARCHAR(50) DEFAULT 'custom' COMMENT 'Category grouping',
    `is_active`     TINYINT(1) DEFAULT 1,
    `created_by`    INT(11) DEFAULT NULL,
    `created_at`    DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_template` (`id_template`),
    KEY `idx_category` (`block_category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='User-saved reusable HTML/Konva blocks for print templates';
