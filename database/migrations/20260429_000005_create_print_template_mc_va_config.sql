-- Migration: Create table print_template_mc_va_config
-- Module: Print Template Designer — MC/VA display configuration per document type
-- Author: Antigravity
-- Safe: Uses IF NOT EXISTS

-- UP
CREATE TABLE `print_template_mc_va_config` (
    `id`              INT(11) NOT NULL AUTO_INCREMENT,
    `id_branch`       INT(11) DEFAULT NULL COMMENT 'NULL = global/all branches',
    `document_type`   VARCHAR(50) NOT NULL COMMENT 'e.g. billing, estimation, order',
    `mc_label`        VARCHAR(100) DEFAULT 'MC' COMMENT 'Making Charge column label',
    `mc_visible`      TINYINT(1) DEFAULT 1 COMMENT '1 = show MC column',
    `va_label`        VARCHAR(100) DEFAULT 'VA' COMMENT 'Value Addition column label',
    `va_visible`      TINYINT(1) DEFAULT 1 COMMENT '1 = show VA column',
    `mc_type`         TINYINT(1) DEFAULT 1 COMMENT '1 = per-piece, 2 = per-gram',
    `va_type`         TINYINT(1) DEFAULT 1 COMMENT '1 = percentage, 2 = weight',
    `created_at`      DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at`      DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_branch_doctype` (`id_branch`, `document_type`),
    KEY `idx_doctype` (`document_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='MC/VA display config per branch and document type';
