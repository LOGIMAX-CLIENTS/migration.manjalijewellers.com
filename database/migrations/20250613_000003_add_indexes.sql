-- Migration: Add indexes across multiple tables
-- Source: database_queries.txt (Rahul indexing batch + others)
-- Safe: Each index checked via INFORMATION_SCHEMA before adding

-- UP

-- === company ===
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'company' AND COLUMN_NAME = 'id_country');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE company ADD INDEX (id_country)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'company' AND COLUMN_NAME = 'id_state');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE company ADD INDEX (id_state)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'company' AND COLUMN_NAME = 'id_city');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE company ADD INDEX (id_city)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- === metal_rates ===
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'metal_rates' AND COLUMN_NAME = 'id_metalrates');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE metal_rates ADD INDEX (id_metalrates)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'metal_rates' AND COLUMN_NAME = 'id_employee');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE metal_rates ADD INDEX (id_employee)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- === customer ===
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'customer' AND COLUMN_NAME = 'mobile');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE customer ADD INDEX (mobile)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'customer' AND COLUMN_NAME = 'id_customer');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE customer ADD INDEX (id_customer)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'customer' AND COLUMN_NAME = 'passwd');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE customer ADD INDEX (passwd)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'customer' AND COLUMN_NAME = 'active');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE customer ADD INDEX (active)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'customer' AND COLUMN_NAME = 'email');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE customer ADD INDEX (email)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'customer' AND COLUMN_NAME = 'date_add');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE customer ADD INDEX (date_add)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'customer' AND COLUMN_NAME = 'firstname');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE customer ADD INDEX (firstname)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'customer' AND COLUMN_NAME = 'lastname');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE customer ADD INDEX (lastname)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'customer' AND COLUMN_NAME = 'username');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE customer ADD INDEX (username)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'customer' AND COLUMN_NAME = 'added_by');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE customer ADD INDEX (added_by)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- === wallet_account ===
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'wallet_account' AND COLUMN_NAME = 'id_wallet_account');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE wallet_account ADD INDEX (id_wallet_account)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'wallet_account' AND COLUMN_NAME = 'id_customer');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE wallet_account ADD INDEX (id_customer)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'wallet_account' AND COLUMN_NAME = 'id_employee');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE wallet_account ADD INDEX (id_employee)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- === wallet_transaction ===
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'wallet_transaction' AND COLUMN_NAME = 'id_wallet_account');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE wallet_transaction ADD INDEX (id_wallet_account)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- === weight ===
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'weight' AND COLUMN_NAME = 'active');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE weight ADD INDEX (active)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'weight' AND COLUMN_NAME = 'id_weight');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE weight ADD INDEX (id_weight)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- === country ===
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'country' AND COLUMN_NAME = 'id_country');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE country ADD INDEX (id_country)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- === state ===
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'state' AND COLUMN_NAME = 'id_country');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE state ADD INDEX (id_country)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'state' AND COLUMN_NAME = 'id_state');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE state ADD INDEX (id_state)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'state' AND COLUMN_NAME = 'name');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE state ADD INDEX (name)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- === city ===
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'city' AND COLUMN_NAME = 'id_state');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE city ADD INDEX (id_state)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'city' AND COLUMN_NAME = 'id_city');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE city ADD INDEX (id_city)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'city' AND COLUMN_NAME = 'name');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE city ADD INDEX (name)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- === scheme_group ===
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'scheme_group' AND COLUMN_NAME = 'id_scheme');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE scheme_group ADD INDEX (id_scheme)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- === scheme ===
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'scheme' AND COLUMN_NAME = 'id_classification');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE scheme ADD INDEX (id_classification)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'scheme' AND COLUMN_NAME = 'active');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE scheme ADD INDEX (active)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'scheme' AND COLUMN_NAME = 'visible');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE scheme ADD INDEX (visible)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'scheme' AND COLUMN_NAME = 'id_scheme');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE scheme ADD INDEX (id_scheme)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- === scheme_account ===
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'scheme_account' AND COLUMN_NAME = 'id_scheme');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE scheme_account ADD INDEX (id_scheme)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'scheme_account' AND COLUMN_NAME = 'id_scheme_account');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE scheme_account ADD INDEX (id_scheme_account)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'scheme_account' AND COLUMN_NAME = 'id_customer');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE scheme_account ADD INDEX (id_customer)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'scheme_account' AND COLUMN_NAME = 'scheme_acc_number');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE scheme_account ADD INDEX (scheme_acc_number)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'scheme_account' AND COLUMN_NAME = 'group_code');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE scheme_account ADD INDEX (group_code)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'scheme_account' AND COLUMN_NAME = 'is_closed');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE scheme_account ADD INDEX (is_closed)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'scheme_account' AND COLUMN_NAME = 'closed_by');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE scheme_account ADD INDEX (closed_by)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'scheme_account' AND COLUMN_NAME = 'closing_date');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE scheme_account ADD INDEX (closing_date)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'scheme_account' AND COLUMN_NAME = 'id_branch');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE scheme_account ADD INDEX (id_branch)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- === payment ===
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payment' AND COLUMN_NAME = 'id_scheme_account');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE payment ADD INDEX (id_scheme_account)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payment' AND COLUMN_NAME = 'payment_status');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE payment ADD INDEX (payment_status)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payment' AND COLUMN_NAME = 'date_payment');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE payment ADD INDEX (date_payment)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payment' AND COLUMN_NAME = 'date_add');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE payment ADD INDEX (date_add)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payment' AND COLUMN_NAME = 'added_by');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE payment ADD INDEX (added_by)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payment' AND COLUMN_NAME = 'payment_mode');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE payment ADD INDEX (payment_mode)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payment' AND COLUMN_NAME = 'id_transaction');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE payment ADD INDEX (id_transaction)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payment' AND COLUMN_NAME = 'ref_trans_id');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE payment ADD INDEX (ref_trans_id)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- === postdate_payment ===
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'postdate_payment' AND COLUMN_NAME = 'payee_bank');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE postdate_payment ADD INDEX (payee_bank)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'postdate_payment' AND COLUMN_NAME = 'payment_status');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE postdate_payment ADD INDEX (payment_status)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'postdate_payment' AND COLUMN_NAME = 'id_scheme_account');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE postdate_payment ADD INDEX (id_scheme_account)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'postdate_payment' AND COLUMN_NAME = 'date_payment');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE postdate_payment ADD INDEX (date_payment)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- === wallet_category_settings ===
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'wallet_category_settings' AND COLUMN_NAME = 'active');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE wallet_category_settings ADD INDEX (active)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'wallet_category_settings' AND COLUMN_NAME = 'id_category');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE wallet_category_settings ADD INDEX (id_category)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- === address ===
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'address' AND COLUMN_NAME = 'id_customer');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE address ADD INDEX (id_customer)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'address' AND COLUMN_NAME = 'id_country');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE address ADD INDEX (id_country)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'address' AND COLUMN_NAME = 'id_state');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE address ADD INDEX (id_state)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'address' AND COLUMN_NAME = 'id_city');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE address ADD INDEX (id_city)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- === sms_api_settings ===
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'sms_api_settings' AND COLUMN_NAME = 'id_sms_api');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE sms_api_settings ADD INDEX (id_sms_api)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'sms_api_settings' AND COLUMN_NAME = 'debit_sms');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE sms_api_settings ADD INDEX (debit_sms)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- === new_arrivals ===
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'new_arrivals' AND COLUMN_NAME = 'active');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE new_arrivals ADD INDEX (active)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'new_arrivals' AND COLUMN_NAME = 'new_type');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE new_arrivals ADD INDEX (new_type)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'new_arrivals' AND COLUMN_NAME = 'id_new_arrivals');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE new_arrivals ADD INDEX (id_new_arrivals)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- === offers ===
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'offers' AND COLUMN_NAME = 'type');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE offers ADD INDEX (type)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'offers' AND COLUMN_NAME = 'active');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE offers ADD INDEX (active)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'offers' AND COLUMN_NAME = 'id_offer');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE offers ADD INDEX (id_offer)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- === sch_classify ===
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'sch_classify' AND COLUMN_NAME = 'active');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE sch_classify ADD INDEX (active)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'sch_classify' AND COLUMN_NAME = 'id_classification');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE sch_classify ADD INDEX (id_classification)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- === registered_devices ===
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'registered_devices' AND COLUMN_NAME = 'id_customer');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE registered_devices ADD INDEX (id_customer)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- === branch ===
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'branch' AND COLUMN_NAME = 'show_to_all');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE branch ADD INDEX (show_to_all)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'branch' AND COLUMN_NAME = 'id_branch');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE branch ADD INDEX (id_branch)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- === inter_wallet_account ===
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'inter_wallet_account' AND COLUMN_NAME = 'mobile');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE inter_wallet_account ADD INDEX (mobile)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- === scheme_reg_request ===
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'scheme_reg_request' AND COLUMN_NAME = 'id_scheme');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE scheme_reg_request ADD INDEX (id_scheme)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'scheme_reg_request' AND COLUMN_NAME = 'id_scheme_group');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE scheme_reg_request ADD INDEX (id_scheme_group)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- === gateway ===
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'gateway' AND COLUMN_NAME = 'id_pg');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE gateway ADD INDEX (id_pg)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'gateway' AND COLUMN_NAME = 'id_branch');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE gateway ADD INDEX (id_branch)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'gateway' AND COLUMN_NAME = 'is_default');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE gateway ADD INDEX (is_default)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'gateway' AND COLUMN_NAME = 'active');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE gateway ADD INDEX (active)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- === pending_payment ===
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'pending_payment' AND COLUMN_NAME = 'id_transaction');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE pending_payment ADD INDEX (id_transaction)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- === otp ===
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'otp' AND COLUMN_NAME = 'id_sch_acc');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE otp ADD INDEX (id_sch_acc)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- === registration ===
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'registration' AND COLUMN_NAME = 'id_customer');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE registration ADD INDEX (id_customer)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'registration' AND COLUMN_NAME = 'id_scheme');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE registration ADD INDEX (id_scheme)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'registration' AND COLUMN_NAME = 'is_approved');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE registration ADD INDEX (is_approved)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- === menu ===
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'menu' AND COLUMN_NAME = 'id_menu');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE menu ADD INDEX (id_menu)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'menu' AND COLUMN_NAME = 'active');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE menu ADD INDEX (active)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'menu' AND COLUMN_NAME = 'parent');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE menu ADD INDEX (parent)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- === access ===
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'access' AND COLUMN_NAME = 'id_menu');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE access ADD INDEX (id_menu)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'access' AND COLUMN_NAME = 'id_profile');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE access ADD INDEX (id_profile)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- === profile ===
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'profile' AND COLUMN_NAME = 'id_profile');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE profile ADD INDEX (id_profile)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- === bank ===
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'bank' AND COLUMN_NAME = 'id_bank');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE bank ADD INDEX (id_bank)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- === payment_mode ===
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payment_mode' AND COLUMN_NAME = 'id_mode');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE payment_mode ADD INDEX (id_mode)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- === department ===
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'department' AND COLUMN_NAME = 'id_dept');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE department ADD INDEX (id_dept)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- === services ===
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'services' AND COLUMN_NAME = 'id_services');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE services ADD INDEX (id_services)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- === employee ===
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'employee' AND COLUMN_NAME = 'email');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE employee ADD INDEX (email)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'employee' AND COLUMN_NAME = 'mobile');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE employee ADD INDEX (mobile)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'employee' AND COLUMN_NAME = 'passwd');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE employee ADD INDEX (passwd)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'employee' AND COLUMN_NAME = 'username');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE employee ADD INDEX (username)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'employee' AND COLUMN_NAME = 'id_profile');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE employee ADD INDEX (id_profile)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'employee' AND COLUMN_NAME = 'emp_code');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE employee ADD INDEX (emp_code)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- === daily_collection ===
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'daily_collection' AND COLUMN_NAME = 'date');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE daily_collection ADD INDEX (date)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- === purchase_customer ===
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'purchase_customer' AND COLUMN_NAME = 'firstname');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE purchase_customer ADD INDEX (firstname)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'purchase_customer' AND COLUMN_NAME = 'mobile');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE purchase_customer ADD INDEX (mobile)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
