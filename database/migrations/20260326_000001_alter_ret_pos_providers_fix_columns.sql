-- Migration: Fix column sizes on ret_pos_providers
-- Date: 26-03-2026
-- Source: database_queries.sql lines 12514-12528
-- Safe: ALTER MODIFY is idempotent — no error if already correct size

-- UP
ALTER TABLE `ret_pos_providers`
  MODIFY `provider_name` VARCHAR(50) NOT NULL,
  MODIFY `provider_code` VARCHAR(20) NOT NULL,
  MODIFY `api_url_uat_init` VARCHAR(255) DEFAULT NULL,
  MODIFY `api_url_uat_status` VARCHAR(255) DEFAULT NULL,
  MODIFY `api_url_uat_cancel` VARCHAR(255) DEFAULT NULL,
  MODIFY `api_url_live_init` VARCHAR(255) DEFAULT NULL,
  MODIFY `api_url_live_status` VARCHAR(255) DEFAULT NULL,
  MODIFY `api_url_live_cancel` VARCHAR(255) DEFAULT NULL,
  MODIFY `auth_type` VARCHAR(20) DEFAULT 'token' COMMENT 'token|sha256_header|basic_auth',
  MODIFY `is_env_live` TINYINT DEFAULT 0 COMMENT '0=UAT, 1=LIVE',
  MODIFY `has_qr_display` TINYINT DEFAULT 0 COMMENT '1=show QR on screen',
  MODIFY `has_callback` TINYINT DEFAULT 0 COMMENT '1=supports S2S callback',
  MODIFY `status_method` VARCHAR(10) DEFAULT 'POST' COMMENT 'POST or GET',
  MODIFY `is_active` TINYINT DEFAULT 1;

-- DOWN
-- No rollback needed — column sizes were wrong before
