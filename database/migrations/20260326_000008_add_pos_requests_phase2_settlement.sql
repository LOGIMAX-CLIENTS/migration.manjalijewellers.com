-- Migration: Phase 2 — Add settlement columns to ret_pos_requests
-- Date: 26-03-2026
-- Source: database_queries.sql lines 13125-13137
-- Safe: ADD COLUMN — Duplicate column tolerated by runner

-- UP
ALTER TABLE `ret_pos_requests` ADD COLUMN `settlement_id` INT DEFAULT NULL COMMENT 'FK to pos_settlements' AFTER `pos_last_checked_at`;
ALTER TABLE `ret_pos_requests` ADD COLUMN `settled_at` DATE DEFAULT NULL COMMENT 'Date this transaction was settled' AFTER `settlement_id`;

-- DOWN
-- ALTER TABLE `ret_pos_requests` DROP COLUMN `settled_at`;
-- ALTER TABLE `ret_pos_requests` DROP COLUMN `settlement_id`;
