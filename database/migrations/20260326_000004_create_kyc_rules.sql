-- Migration: Create table kyc_rules
-- Date: 26-03-2026
-- Description: KYC enforcement rules (scheme-wise or customer-wise triggers)
-- Safe: Uses IF NOT EXISTS

-- UP
CREATE TABLE IF NOT EXISTS `kyc_rules` (
    `id_kyc_rules`  INT(11) NOT NULL AUTO_INCREMENT,
    `id_scheme`     VARCHAR(50) DEFAULT NULL,
    `id_mas_kyc`    INT(11) DEFAULT NULL COMMENT 'from kyc_master',
    `rules`         TINYINT(4) DEFAULT NULL COMMENT 'scheme_wise: 0=on joining, 1=on payment, 2=on closing; customer_wise: 0=customer creation, 1=on overall amount',
    `type`          TINYINT(4) DEFAULT NULL COMMENT 'scheme_wise only: 0=amount, 1=installment',
    `amount`        INT(11) DEFAULT NULL COMMENT 'scheme_wise: particular scheme amount; customer_wise: overall amount',
    `status`        TINYINT(4) NOT NULL DEFAULT 1,
    `created_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `created_by`    SMALLINT(6) DEFAULT NULL,
    PRIMARY KEY (`id_kyc_rules`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


