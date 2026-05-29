ALTER TABLE kyc_settings ADD COLUMN kyc_integration_type TINYINT(1) DEFAULT 0 AFTER kyc_mode;

ALTER TABLE kyc_settings ADD COLUMN kyc_scheme_rule_logic TINYINT(1) NOT NULL DEFAULT 0;
ALTER TABLE kyc_settings ADD COLUMN kyc_customer_rule_logic TINYINT(1) NOT NULL DEFAULT 0;



ALTER TABLE `kyc_rules` ADD `logic` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '0: All Required, 1: At Least One' AFTER `amount`;