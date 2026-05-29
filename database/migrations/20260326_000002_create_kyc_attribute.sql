-- Migration: Create table kyc_attribute
-- Date: 26-03-2026
-- Description: KYC document attributes (field definitions per KYC type)
-- Safe: Uses IF NOT EXISTS
-- Depends: 20260326_000001_create_kyc_master.sql

-- UP
CREATE TABLE IF NOT EXISTS `kyc_attribute` (
    `id_kyc_attribute`  INT(10) NOT NULL AUTO_INCREMENT,
    `id_mas_kyc`        INT(11) NOT NULL,
    `attribute`         VARCHAR(250) COLLATE utf8mb4_unicode_ci NOT NULL,
    `attr_label`        VARCHAR(250) COLLATE utf8mb4_unicode_ci NOT NULL,
    `attr_input`        VARCHAR(45) COLLATE utf8mb4_unicode_ci NOT NULL,
    `attr_length`       VARCHAR(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `is_mandatory`      TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1-Mandatory,0-Non Mandatory',
    `reg_expression`    TEXT COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `position`          INT(10) DEFAULT NULL,
    `status`            TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (`id_kyc_attribute`),
    UNIQUE KEY `id_mas_kyc_2` (`id_mas_kyc`, `position`),
    KEY `id_mas_kyc` (`id_mas_kyc`),
    CONSTRAINT `fk_id_mas_kyc` FOREIGN KEY (`id_mas_kyc`) REFERENCES `kyc_master` (`id_mas_kyc`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Donot change attribute';


