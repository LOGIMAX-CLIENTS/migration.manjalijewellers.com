-- Migration: Create table pos_payment_sessions
-- Source: database_queries.sql:11518
-- Author: NAMBI MUTHU RAJA
-- Safe: Uses IF NOT EXISTS

-- UP
CREATE TABLE IF NOT EXISTS `pos_payment_sessions` (
    `session_id`    INT NOT NULL AUTO_INCREMENT,
    `invoice_id`    VARCHAR(50) DEFAULT NULL COMMENT 'bill_est_id',
    `customer_id`   INT DEFAULT NULL COMMENT 'cusid',
    `device_id`     INT DEFAULT NULL,
    `provider_code` VARCHAR(50) DEFAULT NULL,
    `amount_paise`  BIGINT DEFAULT NULL,
    `status`        ENUM('INIT','PENDING','SUCCESS','FAILED','CANCELLED','EXPIRED') DEFAULT 'INIT',
    `pos_req_id`    INT DEFAULT NULL COMMENT 'links to ret_pos_requests.pos_req_id',
    `created_by`    INT DEFAULT NULL,
    `created_at`    DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `expires_at`    DATETIME DEFAULT NULL COMMENT 'auto-expire time',
    PRIMARY KEY (`session_id`),
    KEY `idx_invoice` (`invoice_id`),
    KEY `idx_status` (`status`),
    KEY `idx_customer` (`customer_id`),
    KEY `idx_expires` (`expires_at`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='POS payment session lock for bill safety';
