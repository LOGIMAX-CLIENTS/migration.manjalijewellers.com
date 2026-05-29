-- Migration: Create table kyc_settings
-- Date: 26-03-2026
-- Description: Global KYC configuration (required, mode, verification type, allow type)
-- Safe: Uses IF NOT EXISTS

-- UP
CREATE TABLE IF NOT EXISTS `kyc_settings` (
    `id_kyc_settings`       INT(11) NOT NULL AUTO_INCREMENT,
    `kyc_required`          TINYINT(4) NOT NULL DEFAULT 0 COMMENT '0= not required 1= required',
    `kyc_mode`              TINYINT(4) NOT NULL DEFAULT 0 COMMENT '0 = scheme_wise 1 = customer_wise',
    `kyc_verification_type` TINYINT(4) NOT NULL DEFAULT 0 COMMENT '0= manual 1= auto',
    `kyc_allow_type`        TINYINT(4) NOT NULL DEFAULT 0 COMMENT '0 = allow to pay 1 = block_payment',
    PRIMARY KEY (`id_kyc_settings`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


