-- Migration: Add missing est_id and related pos_req columns to ret_pos_requests
-- Date: 13-05-2026
-- Source: Sync with Kalson staging DB schema
-- Safe: ADD COLUMN — Duplicate column tolerated by runner

-- UP
ALTER TABLE `ret_pos_requests` ADD COLUMN `pos_req_est_id` INT DEFAULT NULL AFTER `pos_req_bill_id`;
ALTER TABLE `ret_pos_requests` ADD COLUMN `pos_req_bill_type` TINYINT DEFAULT NULL AFTER `pos_req_est_id`;
ALTER TABLE `ret_pos_requests` ADD COLUMN `pos_req_source_ref` VARCHAR(100) DEFAULT NULL AFTER `pos_req_bill_type`;
ALTER TABLE `ret_pos_requests` ADD COLUMN `pos_req_id_branch` INT DEFAULT NULL AFTER `pos_req_source_ref`;
ALTER TABLE `ret_pos_requests` ADD COLUMN `pos_req_createdon` DATETIME DEFAULT NULL AFTER `pos_req_id_branch`;
ALTER TABLE `ret_pos_requests` ADD COLUMN `pos_success_at` DATETIME DEFAULT NULL AFTER `pos_cancelled_at`;
ALTER TABLE `ret_pos_requests` ADD COLUMN `pos_success_by` INT DEFAULT NULL AFTER `pos_success_at`;
ALTER TABLE `ret_pos_requests` ADD COLUMN `pos_callback_at` DATETIME DEFAULT NULL AFTER `pos_success_by`;

-- DOWN
-- ALTER TABLE `ret_pos_requests` DROP COLUMN `pos_callback_at`;
-- ALTER TABLE `ret_pos_requests` DROP COLUMN `pos_success_by`;
-- ALTER TABLE `ret_pos_requests` DROP COLUMN `pos_success_at`;
-- ALTER TABLE `ret_pos_requests` DROP COLUMN `pos_req_createdon`;
-- ALTER TABLE `ret_pos_requests` DROP COLUMN `pos_req_id_branch`;
-- ALTER TABLE `ret_pos_requests` DROP COLUMN `pos_req_source_ref`;
-- ALTER TABLE `ret_pos_requests` DROP COLUMN `pos_req_bill_type`;
-- ALTER TABLE `ret_pos_requests` DROP COLUMN `pos_req_est_id`;
