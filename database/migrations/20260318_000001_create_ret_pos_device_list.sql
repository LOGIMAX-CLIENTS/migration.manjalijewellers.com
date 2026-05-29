-- Migration: Create table ret_pos_device_list
-- Source: database_queries.sql:11201
-- Author: NAMBI MUTHU RAJA
-- Safe: Uses IF NOT EXISTS

-- UP
CREATE TABLE IF NOT EXISTS `ret_pos_device_list` (
  `id_device`       INT NOT NULL AUTO_INCREMENT,
  `dispname`        VARCHAR(100) DEFAULT NULL COMMENT 'Display name for dropdown',
  `devicetype`      INT DEFAULT 1 COMMENT '0=Card+UPI(Both), 1=Card only, 3=UPI QR only',
  `poscode`         VARCHAR(50) DEFAULT NULL COMMENT 'POS code (Pine Labs store code)',
  `merchantid`      VARCHAR(50) DEFAULT NULL COMMENT 'Merchant ID from provider',
  `securitytoken`   VARCHAR(100) DEFAULT NULL COMMENT 'API key / security token',
  `imei`            VARCHAR(50) DEFAULT NULL COMMENT 'Device IMEI (Pine Labs)',
  `is_default`      TINYINT DEFAULT 0 COMMENT '1=Default device (pre-selected)',
  `id_provider`     INT DEFAULT NULL COMMENT 'FK to ret_pos_providers',
  `store_id`        VARCHAR(50) DEFAULT NULL COMMENT 'PhonePe storeId',
  `terminal_id`     VARCHAR(50) DEFAULT NULL COMMENT 'PhonePe terminalId',
  `salt_key`        VARCHAR(100) DEFAULT NULL COMMENT 'PhonePe saltKey',
  `salt_index`      VARCHAR(10) DEFAULT NULL COMMENT 'PhonePe saltIndex',
  `provider_id`     VARCHAR(50) DEFAULT NULL COMMENT 'PhonePe X-PROVIDER-ID',
  `callback_url`    VARCHAR(255) DEFAULT NULL COMMENT 'S2S callback URL',
  `is_active`       TINYINT DEFAULT 1 COMMENT '0=inactive, 1=active',
  `id_pay_device`   INT DEFAULT NULL COMMENT 'Links to ret_bill_pay_device — which bank swipe device this POS maps to',
  `id_bank`         INT DEFAULT NULL COMMENT 'Links to bank — which client bank account receives money from this POS device',
  PRIMARY KEY (`id_device`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
