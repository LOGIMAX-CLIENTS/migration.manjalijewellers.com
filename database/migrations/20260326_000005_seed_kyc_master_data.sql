-- Migration: Seed KYC master data, attributes, and default settings
-- Date: 26-03-2026
-- Description: Inserts default KYC document types (PAN, Aadhar, DL, Voter ID),
--              their field definitions, and initial kyc_settings row.
-- Safe: Uses INSERT IGNORE to skip rows that already exist.
-- Depends: 20260326_000001 through 20260326_000003

-- UP

-- 1. Seed kyc_master document types
INSERT IGNORE INTO `kyc_master` (`id_mas_kyc`, `name`, `short_code`, `doc_type`, `is_attachment_req`, `is_mandatory`, `created_by`, `created_on`, `updated_by`, `updated_on`, `status`, `sort`) VALUES
(1, 'PAN',              'PAN',    4, 0, 0, 1, '2023-02-13 13:38:56', NULL, NULL, 1, 4),
(2, 'AADHAR',           'ADR_ID', 2, 0, 0, 1, '2023-02-13 13:38:56', NULL, NULL, 1, 1),
(3, 'DRIVING LICENCE',  'DL',     2, 0, 0, 1, '2023-02-13 13:38:56', NULL, NULL, 1, 2),
(4, 'VOTER ID',         'V_ID',   2, 0, 0, 1, '2023-02-13 13:38:56', NULL, NULL, 1, 3);

-- 2. Seed kyc_attribute field definitions
INSERT IGNORE INTO `kyc_attribute` (`id_kyc_attribute`, `id_mas_kyc`, `attribute`, `attr_label`, `attr_input`, `attr_length`, `is_mandatory`, `reg_expression`, `position`, `status`) VALUES
(1,  1, 'pan_no',          'PAN NO',               'varchar', '10', 1, '^[a-zA-Z]{5}\\d{4}[a-zA-Z]{1}$',                                                              1, 1),
(2,  1, 'pan_front_img',   'PAN FRONT IMAGE',      'image',   NULL, 1, NULL,                                                                                            2, 1),
(3,  1, 'pan_back_img',    'PAN BACK IMAGE',       'image',   NULL, 0, NULL,                                                                                            3, 1),
(4,  2, 'aadhar_no',       'AADHAR NO.',           'varchar', '12', 1, '^\\d{4}\\s\\d{4}\\s\\d{4}$',                                                                    1, 1),
(5,  2, 'aadhar_front_img','AADHAR FRONT IMAGE',   'image',   NULL, 1, NULL,                                                                                            2, 1),
(6,  2, 'aadhar_back_img', 'AADHAR BACK IMAGE',    'image',   NULL, 1, NULL,                                                                                            3, 1),
(7,  3, 'dl_no',           'DRIVING LICENCE NO.',  'varchar', '16', 1, '^(([A-Z]{2}[0-9]{2})( )|([A-Z]{2}-[0-9]{2}))((19|20)[0-9][0-9])[0-9]{7}$',                      1, 1),
(8,  3, 'dl_front_img',    'DL FRONT IMAGE',       'image',   NULL, 1, NULL,                                                                                            2, 1),
(9,  3, 'dl_back_img',     'DL BACK IMAGE',        'image',   NULL, 1, NULL,                                                                                            3, 1),
(10, 4, 'vi_no',           'VOTER ID NO.',         'varchar', '10', 1, '^[A-Z]{3}[0-9]{7}$',                                                                            1, 1),
(11, 4, 'vi_front_img',    'VOTERID FRONT IMAGE',  'image',   NULL, 1, NULL,                                                                                            2, 1),
(12, 4, 'vi_back_img',     'VOTERID BACK IMAGE',   'image',   NULL, 1, NULL,                                                                                            3, 1);

-- 3. Seed default kyc_settings
INSERT IGNORE INTO `kyc_settings` (`id_kyc_settings`, `kyc_required`, `kyc_mode`, `kyc_verification_type`, `kyc_allow_type`) VALUES
(1, 1, 0, 0, 0);


