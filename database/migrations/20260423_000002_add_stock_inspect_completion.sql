-- ============================================================
-- Migration: Add Stock Audit Completion Status table
-- Date: 2026-04-23
-- Description: Tracks daily stock audit completion status per branch.
--              Used by Physical Stock Inspect Report page.
-- ============================================================
-- UP

CREATE TABLE IF NOT EXISTS `ret_stock_audit_status` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `audit_date` DATE NOT NULL,
    `id_branch` INT(11) NOT NULL,
    `status` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '0=Pending, 1=Completed',
    `completed_by` INT(11) DEFAULT NULL,
    `completed_on` DATETIME DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_date_branch` (`audit_date`, `id_branch`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DOWN (rollback)
-- DROP TABLE IF EXISTS `ret_stock_audit_status`;
