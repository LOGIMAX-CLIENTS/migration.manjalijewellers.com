-- Migration: Phase 1 — Add device tracking, audit trail, payment mode columns to ret_pos_requests
-- Date: 26-03-2026
-- Source: database_queries.sql lines 12908-12984
-- Safe: ADD COLUMN — Duplicate column tolerated by runner

-- UP
ALTER TABLE `ret_pos_requests` ADD COLUMN `id_device` INT DEFAULT NULL COMMENT 'FK to ret_pos_device_list' AFTER `id_provider`;
ALTER TABLE `ret_pos_requests` ADD COLUMN `provider_code` VARCHAR(30) DEFAULT NULL COMMENT 'pinelabs/phonepe_dqr/phonepe_iedc' AFTER `id_device`;
ALTER TABLE `ret_pos_requests` ADD COLUMN `payment_mode` VARCHAR(20) DEFAULT NULL COMMENT 'CARD/UPI/DQR/NB' AFTER `pos_utr`;
ALTER TABLE `ret_pos_requests` ADD COLUMN `card_last4` VARCHAR(4) DEFAULT NULL COMMENT 'Last 4 digits of card' AFTER `payment_mode`;
ALTER TABLE `ret_pos_requests` ADD COLUMN `approval_code` VARCHAR(20) DEFAULT NULL COMMENT 'Bank approval/auth code' AFTER `card_last4`;
ALTER TABLE `ret_pos_requests` ADD COLUMN `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT 'Last status change' AFTER `created_at`;
ALTER TABLE `ret_pos_requests` ADD COLUMN `pos_cancelled_by` INT DEFAULT NULL COMMENT 'User who cancelled' AFTER `pos_req_status`;
ALTER TABLE `ret_pos_requests` ADD COLUMN `pos_cancelled_at` DATETIME DEFAULT NULL COMMENT 'When cancelled' AFTER `pos_cancelled_by`;
ALTER TABLE `ret_pos_requests` ADD COLUMN `pos_last_checked_by` INT DEFAULT NULL COMMENT 'User who last checked status' AFTER `pos_cancelled_at`;
ALTER TABLE `ret_pos_requests` ADD COLUMN `pos_last_checked_at` DATETIME DEFAULT NULL COMMENT 'When last status check' AFTER `pos_last_checked_by`;

-- DOWN
-- ALTER TABLE `ret_pos_requests` DROP COLUMN `pos_last_checked_at`;
-- ALTER TABLE `ret_pos_requests` DROP COLUMN `pos_last_checked_by`;
-- ALTER TABLE `ret_pos_requests` DROP COLUMN `pos_cancelled_at`;
-- ALTER TABLE `ret_pos_requests` DROP COLUMN `pos_cancelled_by`;
-- ALTER TABLE `ret_pos_requests` DROP COLUMN `updated_at`;
-- ALTER TABLE `ret_pos_requests` DROP COLUMN `approval_code`;
-- ALTER TABLE `ret_pos_requests` DROP COLUMN `card_last4`;
-- ALTER TABLE `ret_pos_requests` DROP COLUMN `payment_mode`;
-- ALTER TABLE `ret_pos_requests` DROP COLUMN `provider_code`;
-- ALTER TABLE `ret_pos_requests` DROP COLUMN `id_device`;
