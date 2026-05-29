-- Migration: Fix columns + add missing columns on ret_pos_requests (base fixes)
-- Date: 26-03-2026
-- Source: database_queries.sql lines 12632-12700
-- Safe: MODIFY is idempotent, ADD COLUMN will skip if already exists (Duplicate column tolerated by runner)

-- UP
ALTER TABLE `ret_pos_requests`
  MODIFY `pos_mer_id` VARCHAR(50) DEFAULT NULL,
  MODIFY `pos_res_ref_id` VARCHAR(100) DEFAULT NULL,
  MODIFY `pos_utr` VARCHAR(50) DEFAULT NULL COMMENT 'UPI UTR number',
  MODIFY `pos_req_status` TINYINT DEFAULT 1 COMMENT '0=INIT/PENDING, 1=SUCCESS, 2=CANCELLED, 3=FAILED';

ALTER TABLE `ret_pos_requests` ADD COLUMN `pos_imie` VARCHAR(50) DEFAULT NULL COMMENT 'Device IMEI' AFTER `pos_mer_id`;
ALTER TABLE `ret_pos_requests` ADD COLUMN `pos_qr_string` TEXT DEFAULT NULL COMMENT 'QR data for DQR' AFTER `id_provider`;
ALTER TABLE `ret_pos_requests` ADD COLUMN `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP AFTER `pos_req_status`;
ALTER TABLE `ret_pos_requests` ADD COLUMN `pos_req_bill_id` INT DEFAULT NULL COMMENT 'FK to ret_estimation' AFTER `pos_req_bill_cusid`;
ALTER TABLE `ret_pos_requests` ADD COLUMN `pos_utr` VARCHAR(50) DEFAULT NULL COMMENT 'UPI UTR number' AFTER `idempotency_key`;
ALTER TABLE `ret_pos_requests` ADD COLUMN `pos_req_payload` TEXT DEFAULT NULL COMMENT 'Full API request payload (JSON)';
ALTER TABLE `ret_pos_requests` ADD COLUMN `pos_res_payload` TEXT DEFAULT NULL COMMENT 'Full API response payload (JSON)';
ALTER TABLE `ret_pos_requests` ADD COLUMN `idempotency_key` VARCHAR(100) DEFAULT NULL COMMENT 'Idempotency key';
ALTER TABLE `ret_pos_requests` ADD COLUMN `pos_req_created_at` DATETIME DEFAULT CURRENT_TIMESTAMP;

-- DOWN
-- No rollback — these columns are part of core POS functionality
