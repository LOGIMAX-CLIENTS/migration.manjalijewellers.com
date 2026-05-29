-- Migration: Create table pos_settlements
-- Source: database_queries.sql:11758
-- Author: NAMBI MUTHU RAJA
-- Safe: Uses IF NOT EXISTS

-- UP
CREATE TABLE IF NOT EXISTS `pos_settlements` (
    `settlement_id`      INT NOT NULL AUTO_INCREMENT,
    `provider_code`      VARCHAR(30) NOT NULL COMMENT 'pinelabs/phonepe_dqr/phonepe_iedc',
    `provider_batch_id`  VARCHAR(100) DEFAULT NULL COMMENT 'Provider settlement batch reference',
    `settlement_date`    DATE NOT NULL COMMENT 'Date funds received',
    `total_transactions` INT DEFAULT 0,
    `total_amount_paise` BIGINT DEFAULT 0 COMMENT 'Total settlement amount in paise',
    `commission_paise`   BIGINT DEFAULT 0 COMMENT 'Provider commission/MDR deducted',
    `net_amount_paise`   BIGINT DEFAULT 0 COMMENT 'Net amount received in bank',
    `bank_reference`     VARCHAR(100) DEFAULT NULL COMMENT 'Bank UTR/NEFT reference',
    `id_bank`            INT DEFAULT NULL COMMENT 'FK to bank table — which account received',
    `status`             ENUM('PENDING','RECONCILED','DISPUTED') DEFAULT 'PENDING',
    `notes`              TEXT DEFAULT NULL,
    `created_by`         INT DEFAULT NULL,
    `created_at`         DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at`         DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`settlement_id`),
    INDEX `idx_settle_provider` (`provider_code`, `settlement_date`),
    INDEX `idx_settle_date` (`settlement_date`),
    INDEX `idx_settle_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='POS settlement tracking — links provider payouts to bank';
