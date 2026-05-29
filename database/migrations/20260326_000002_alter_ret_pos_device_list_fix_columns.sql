-- Migration: Fix column sizes + add missing columns on ret_pos_device_list
-- Date: 26-03-2026
-- Source: database_queries.sql lines 12564-12591
-- Safe: MODIFY is idempotent, ADD COLUMN uses IF NOT EXISTS check

-- UP
ALTER TABLE `ret_pos_device_list`
  MODIFY `dispname` VARCHAR(100) DEFAULT NULL,
  MODIFY `devicetype` INT DEFAULT 1 COMMENT '0=Card+UPI(Both), 1=Card only, 3=UPI QR only',
  MODIFY `merchantid` VARCHAR(50) DEFAULT NULL,
  MODIFY `securitytoken` VARCHAR(100) DEFAULT NULL,
  MODIFY `salt_key` VARCHAR(100) DEFAULT NULL COMMENT 'PhonePe saltKey',
  MODIFY `provider_id` VARCHAR(50) DEFAULT NULL COMMENT 'PhonePe X-PROVIDER-ID',
  MODIFY `store_id` VARCHAR(50) DEFAULT NULL COMMENT 'PhonePe storeId',
  MODIFY `terminal_id` VARCHAR(50) DEFAULT NULL COMMENT 'PhonePe terminalId',
  MODIFY `callback_url` VARCHAR(255) DEFAULT NULL COMMENT 'S2S callback URL',
  MODIFY `id_provider` INT DEFAULT NULL COMMENT 'FK to ret_pos_providers',
  MODIFY `is_active` TINYINT DEFAULT 1 COMMENT '0=inactive, 1=active';

ALTER TABLE `ret_pos_device_list` ADD COLUMN `id_pay_device` INT DEFAULT NULL COMMENT 'Links to ret_bill_pay_device' AFTER `is_active`;
ALTER TABLE `ret_pos_device_list` ADD COLUMN `id_bank` INT DEFAULT NULL COMMENT 'Links to bank table' AFTER `id_pay_device`;

-- DOWN
-- ALTER TABLE `ret_pos_device_list` DROP COLUMN `id_bank`;
-- ALTER TABLE `ret_pos_device_list` DROP COLUMN `id_pay_device`;
