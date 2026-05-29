-- Migration: Create table kyc_master
-- Date: 26-03-2026
-- Description: Master table for KYC document types (PAN, Aadhar, DL, Voter ID)
-- Safe: Uses IF NOT EXISTS

-- UP
CREATE TABLE IF NOT EXISTS `kyc_master` (
    `id_mas_kyc`        INT(11) NOT NULL AUTO_INCREMENT,
    `name`              VARCHAR(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Document Name',
    `short_code`        VARCHAR(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `doc_type`          TINYINT(1) DEFAULT NULL COMMENT '1- Identity Proof, 2 - Address Proof, 3 - ID,Address proof, 4-> Transaction Proof',
    `is_attachment_req`  TINYINT(1) DEFAULT 0,
    `is_mandatory`      INT(2) NOT NULL DEFAULT 0 COMMENT '0-> No, 1->Yes',
    `created_by`        INT(11) DEFAULT NULL,
    `created_on`        DATETIME DEFAULT NULL,
    `updated_by`        INT(11) DEFAULT NULL,
    `updated_on`        DATETIME DEFAULT NULL,
    `status`            TINYINT(4) NOT NULL DEFAULT 1 COMMENT '1-Active,0-Inactive',
    `sort`              INT(5) DEFAULT NULL,
    PRIMARY KEY (`id_mas_kyc`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


