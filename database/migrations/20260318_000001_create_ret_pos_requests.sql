-- Migration: Create table ret_pos_requests
-- Source: database_queries.sql:11264
-- Author: NAMBI MUTHU RAJA
-- Safe: Uses IF NOT EXISTS

-- UP
CREATE TABLE IF NOT EXISTS `ret_pos_requests` (
  `pos_req_id`          INT NOT NULL AUTO_INCREMENT,
  `pos_trans_no`        VARCHAR(100) DEFAULT NULL COMMENT 'Our generated transaction ID',
  `pos_store_pos_code`  VARCHAR(50) DEFAULT NULL COMMENT 'Store/POS code sent to provider',
  `pos_req_amount`      DECIMAL(12,2) DEFAULT NULL COMMENT 'Amount in paise (PhonePe) or rupees',
  `pos_usr_id`          INT DEFAULT NULL COMMENT 'User who initiated payment',
  `pos_mer_id`          VARCHAR(50) DEFAULT NULL COMMENT 'Merchant ID used',
  `pos_imie`            VARCHAR(50) DEFAULT NULL COMMENT 'Device IMEI sent with request',
  `pos_req_createdby`   INT DEFAULT NULL COMMENT 'Created by user ID',
  `pos_req_bill_cusid`  INT DEFAULT NULL COMMENT 'Customer ID from billing',
  `pos_req_bill_id`     INT DEFAULT NULL COMMENT 'Bill ID (FK to ret_estimation)',
  `pos_res_ref_id`      VARCHAR(100) DEFAULT NULL COMMENT 'Provider reference/transaction ID',
  `pos_res_trans_data`  TEXT DEFAULT NULL COMMENT 'Full provider response data (JSON)',
  `pos_req_status`      TINYINT DEFAULT 1 COMMENT '0=INIT/PENDING, 1=SUCCESS, 2=CANCELLED, 3=FAILED',
  `created_at`          DATETIME DEFAULT CURRENT_TIMESTAMP,
  `id_provider`         INT DEFAULT NULL COMMENT 'FK to ret_pos_providers',
  `pos_qr_string`       TEXT DEFAULT NULL COMMENT 'QR data for DQR flow',
  `pos_callback_data`   TEXT DEFAULT NULL COMMENT 'S2S callback response',
  `idempotency_key`     VARCHAR(100) DEFAULT NULL COMMENT 'Client-generated idempotency key to prevent duplicate payments',
  `pos_utr`             VARCHAR(50) DEFAULT NULL COMMENT 'UPI UTR number',
  `pos_req_payload`     TEXT DEFAULT NULL COMMENT 'Full API request payload (JSON)',
  `pos_res_payload`     TEXT DEFAULT NULL COMMENT 'Full API response payload (JSON)',
  `pos_req_created_at`  DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`pos_req_id`),
  UNIQUE KEY `idx_idempotency_key` (`idempotency_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
