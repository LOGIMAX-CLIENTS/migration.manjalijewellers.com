-- Migration: Create table ret_pos_providers
-- Source: database_queries.sql:11152
-- Author: NAMBI MUTHU RAJA
-- Safe: Uses IF NOT EXISTS

-- UP
CREATE TABLE IF NOT EXISTS `ret_pos_providers` (
  `id_provider`         INT NOT NULL AUTO_INCREMENT,
  `provider_name`       VARCHAR(50) NOT NULL COMMENT 'Display name (e.g. Pine Labs, PhonePe DQR)',
  `provider_code`       VARCHAR(20) NOT NULL COMMENT 'Code used in routing logic (e.g. pinelabs, phonepe_dqr)',
  `api_url_uat_init`    VARCHAR(255) DEFAULT NULL COMMENT 'UAT URL for payment init',
  `api_url_uat_status`  VARCHAR(255) DEFAULT NULL COMMENT 'UAT URL for status check',
  `api_url_uat_cancel`  VARCHAR(255) DEFAULT NULL COMMENT 'UAT URL for payment cancel',
  `api_url_live_init`   VARCHAR(255) DEFAULT NULL COMMENT 'Live/Production URL for payment init',
  `api_url_live_status` VARCHAR(255) DEFAULT NULL COMMENT 'Live/Production URL for status check',
  `api_url_live_cancel` VARCHAR(255) DEFAULT NULL COMMENT 'Live/Production URL for payment cancel',
  `auth_type`           VARCHAR(20) DEFAULT 'token' COMMENT 'token|sha256_header|basic_auth',
  `is_env_live`         TINYINT DEFAULT 0 COMMENT '0=UAT, 1=LIVE',
  `has_qr_display`      TINYINT DEFAULT 0 COMMENT '1=show QR on screen',
  `has_callback`        TINYINT DEFAULT 0 COMMENT '1=supports S2S callback',
  `status_method`       VARCHAR(10) DEFAULT 'POST' COMMENT 'POST or GET',
  `is_active`           TINYINT DEFAULT 1,
  `created_at`          DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_provider`),
  UNIQUE KEY `uk_provider_code` (`provider_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
