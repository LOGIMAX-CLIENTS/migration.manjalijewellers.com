-- Migration: Change column types/defaults
-- Safe: CHANGE COLUMN is idempotent (re-running produces same result)

-- UP

-- general.type (database_queries.sql:19 by unknown)
ALTER TABLE `general` CHANGE `type` `type` INT NULL DEFAULT NULL COMMENT '1-T&c,2-FAQ, 3- aboutus,4-privacy policy\r\n';

-- ret_old_metal_process.next_process_for (database_queries.sql:21 by unknown)
ALTER TABLE `ret_old_metal_process` CHANGE `next_process_for` `next_process_for` INT(11) NULL DEFAULT '0' COMMENT '1-> Process 2->Stock';

-- ret_old_metal_melting.rate (database_queries.sql:23 by unknown)
ALTER TABLE `ret_old_metal_melting` CHANGE `rate` `rate` DECIMAL(10,2) NULL DEFAULT NULL;

-- ret_old_metal_melting_recd_details.testing_completed_wt (database_queries.sql:25 by unknown)
ALTER TABLE `ret_old_metal_melting_recd_details` CHANGE `testing_completed_wt` `testing_completed_wt` DECIMAL(10,3) NULL DEFAULT NULL;

-- ret_old_metal_melting_recd_details.tested_purity (database_queries.sql:27 by unknown)
ALTER TABLE `ret_old_metal_melting_recd_details` CHANGE `tested_purity` `tested_purity` DECIMAL(10,3) NULL DEFAULT NULL;

-- ret_old_metal_refining.issue_weight (database_queries.sql:29 by unknown)
ALTER TABLE `ret_old_metal_refining` CHANGE `issue_weight` `issue_weight` DECIMAL(10,3) NULL DEFAULT NULL;

-- ret_old_metal_refining.id_melting (database_queries.sql:31 by unknown)
ALTER TABLE `ret_old_metal_refining` CHANGE `id_melting` `id_melting` INT(11) NULL DEFAULT NULL;

-- ret_old_metal_refining.id_metal_testing (database_queries.sql:33 by unknown)
ALTER TABLE `ret_old_metal_refining` CHANGE `id_metal_testing` `id_metal_testing` INT(11) NULL DEFAULT NULL;

-- ret_stone_discount_master.status (database_queries.sql:108 by unknown)
ALTER TABLE `ret_stone_discount_master` CHANGE `status` `status` TINYINT(1) NOT NULL DEFAULT '1' COMMENT '0 - Inactive, 1 -Active';

-- ret_insurance.insurance_id (database_queries.sql:2712 by DEVADHARSHINI)
ALTER TABLE ret_insurance CHANGE insurance_id insurance_id INT(11) NOT NULL AUTO_INCREMENT;

-- ret_issue_receipt.issue_type (database_queries.sql:5252 by DEVADHARSHINI)
ALTER TABLE `ret_issue_receipt` CHANGE `issue_type` `issue_type` TINYINT(1) NULL DEFAULT NULL COMMENT '1 - Petty Cash, 2 - Credit,3-Advance Refund,4-Existing Credit Sales, 6 - Non jewellery Expenses';

-- ret_supplier_rate_cut.amount_type (database_queries.sql:5278 by DEVADHARSHINI)
ALTER TABLE `ret_supplier_rate_cut` CHANGE `amount_type` `amount_type` TINYINT(1) NULL DEFAULT '1' COMMENT '1=CR, 2=DR';

-- ret_supplier_rate_cut.weight_type (database_queries.sql:5279 by DEVADHARSHINI)
ALTER TABLE `ret_supplier_rate_cut` CHANGE `weight_type` `weight_type` TINYINT(1) NULL DEFAULT '1' COMMENT '1=CR, 2=DR';

-- payment.due_type (database_queries.txt:17 by unknown)
ALTER TABLE payment CHANGE due_type due_type VARCHAR(5) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT 'N';

-- payment_mode_details.payment_mode (database_queries.txt:228 by Rahul J)
ALTER TABLE `payment_mode_details` CHANGE `payment_mode` `payment_mode` VARCHAR(15) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;

-- customerorderdetails.orderno (database_queries.txt:268 by Abinaya M)
ALTER TABLE `customerorderdetails` CHANGE `orderno` `orderno` VARCHAR(55) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;

-- payment.is_editing_enabled (database_queries.txt:328 by Esakkiraja. N)
ALTER TABLE `payment` CHANGE `is_editing_enabled` `is_editing_enabled` TINYINT(4) UNSIGNED NULL DEFAULT '0';

-- scheme.closing_maturity_days (database_queries.txt:353 by RAHUL)
ALTER TABLE scheme CHANGE closing_maturity_days closing_maturity_days INT(3) NULL DEFAULT '0' COMMENT 'in days. [ benefits will be applied this field] 0 - Shall close on same day';

-- scheme.maturity_days (database_queries.txt:354 by RAHUL)
ALTER TABLE scheme CHANGE maturity_days maturity_days INT(10) NULL DEFAULT '0' COMMENT 'in days. (If null no fixed maturity)';

-- scheme.topup_min_value (database_queries.txt:372 by Karthikai Kumaran T)
ALTER TABLE `scheme` CHANGE `topup_min_value` `topup_min_value` DECIMAL(8,2) NOT NULL DEFAULT '0.000' COMMENT 'Booking minimum value', CHANGE `topup_max_value` `topup_max_value` DECIMAL(8,2) NOT NULL DEFAULT '0.000' COMMENT 'Booking maximum value', CHANGE `topup_denomination` `topup_denomination` DECIMAL(8,2) NOT NULL DEFAULT '0.000' COMMENT 'Booking denomination value';

-- log_detail.form_data (database_queries.txt:374 by Karthikai Kumaran T)
ALTER TABLE `log_detail` CHANGE `form_data` `form_data` LONGTEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL COMMENT 'In this we store a whole form content in json format';

-- scheme_custom_payable_settings.created_on (database_queries.txt:375 by Karthikai Kumaran T)
ALTER TABLE `scheme_custom_payable_settings` CHANGE `created_on` `created_on` VARCHAR(30) NULL DEFAULT NULL COMMENT 'entry created datetime', CHANGE `updated_on` `updated_on` VARCHAR(30) NULL DEFAULT NULL COMMENT 'Update entry current date time';

-- ret_karigar_metal_issue_details.calc_type (database_queries.txt:406 by Jothish)
ALTER TABLE `ret_karigar_metal_issue_details` CHANGE `calc_type` `calc_type` TINYINT(1) NULL DEFAULT NULL;

-- ret_billing.customer_name (database_queries.txt:413 by Jothish)
ALTER TABLE ret_billing CHANGE customer_name customer_name VARCHAR(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;

-- company.show_social_media (database_queries.txt:456 by Karthikai Kumaran)
ALTER TABLE `company` CHANGE `show_social_media` `show_social_media` TINYINT(4) NOT NULL DEFAULT '0' COMMENT ' For app to show the social media option or not';

-- chit_settings.pan_req_amt (database_queries.txt:464 by Karthikai Kumaran)
ALTER TABLE `chit_settings` CHANGE `pan_req_amt` `pan_req_amt` DECIMAL(10,2) NOT NULL DEFAULT '0.00' COMMENT 'get PAN by amount';

-- payment.saved_benefits (database_queries.txt:496 by NAMBI MUTHU RAJA)
ALTER TABLE payment CHANGE saved_benefits saved_benefits DECIMAL(10,4) NULL DEFAULT NULL COMMENT 'For digi gold(weight) ';

-- scheme_account.closing_interest_val (database_queries.txt:499 by NAMBI MUTHU RAJA)
ALTER TABLE `scheme_account` CHANGE `closing_interest_val` `closing_interest_val` DECIMAL(10,3) NULL DEFAULT NULL;

-- agent.id_branch (database_queries.txt:564 by RUDRA)
ALTER TABLE `agent` CHANGE `id_branch` `id_branch` VARCHAR(45) NULL DEFAULT NULL COMMENT 'cus register branch';
