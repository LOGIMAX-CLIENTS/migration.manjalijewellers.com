-- Migration: Seed default POS providers
-- Date: 26-03-2026
-- Source: database_queries.sql lines 12790-12833
-- Safe: WHERE NOT EXISTS prevents duplicates

-- UP
INSERT INTO `ret_pos_providers` (`provider_name`, `provider_code`, `auth_type`, `status_method`,
  `api_url_uat_init`, `api_url_uat_status`, `api_url_uat_cancel`,
  `api_url_live_init`, `api_url_live_status`, `api_url_live_cancel`,
  `is_env_live`, `has_qr_display`, `has_callback`)
SELECT 'Pine Labs', 'pinelabs', 'token', 'POST',
  'https://www.plutuscloudserviceuat.in/API/CloudBasedIntegration/V1/UploadBilledTransaction',
  'https://www.plutuscloudserviceuat.in/API/CloudBasedIntegration/V1/GetCloudBasedTxnStatus',
  'https://www.plutuscloudserviceuat.in/API/CloudBasedIntegration/V1/CancelTransaction',
  'https://www.plutuscloudservice.in/API/CloudBasedIntegration/V1/UploadBilledTransaction',
  'https://www.plutuscloudservice.in/API/CloudBasedIntegration/V1/GetCloudBasedTxnStatus',
  'https://www.plutuscloudservice.in/API/CloudBasedIntegration/V1/CancelTransaction',
  0, 0, 0
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `ret_pos_providers` WHERE `provider_code` = 'pinelabs');

INSERT INTO `ret_pos_providers` (`provider_name`, `provider_code`, `auth_type`, `status_method`,
  `api_url_uat_init`, `api_url_uat_status`, `api_url_uat_cancel`,
  `api_url_live_init`, `api_url_live_status`, `api_url_live_cancel`,
  `is_env_live`, `has_qr_display`, `has_callback`)
SELECT 'PhonePe DQR', 'phonepe_dqr', 'x_verify', 'GET',
  'https://mercury-uat.phonepe.com/enterprise-sandbox/v3/qr/init',
  'https://mercury-uat.phonepe.com/enterprise-sandbox/v3/transaction/{merchantId}/{transactionId}/status',
  'https://mercury-uat.phonepe.com/enterprise-sandbox/v3/charge/{merchantId}/{transactionId}/cancel',
  'https://mercury-t2.phonepe.com/v3/qr/init',
  'https://mercury-t2.phonepe.com/v3/transaction/{merchantId}/{transactionId}/status',
  'https://mercury-t2.phonepe.com/v3/charge/{merchantId}/{transactionId}/cancel',
  0, 1, 1
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `ret_pos_providers` WHERE `provider_code` = 'phonepe_dqr');

INSERT INTO `ret_pos_providers` (`provider_name`, `provider_code`, `auth_type`, `status_method`,
  `api_url_uat_init`, `api_url_uat_status`, `api_url_uat_cancel`,
  `api_url_live_init`, `api_url_live_status`, `api_url_live_cancel`,
  `is_env_live`, `has_qr_display`, `has_callback`)
SELECT 'PhonePe IEDC', 'phonepe_iedc', 'x_verify', 'GET',
  'https://mercury-uat.phonepe.com/enterprise-sandbox/v1/edc/transaction/init',
  'https://mercury-uat.phonepe.com/enterprise-sandbox/v1/edc/transaction/{merchantId}/{transactionId}/status',
  'https://mercury-uat.phonepe.com/enterprise-sandbox/v3/charge/{merchantId}/{transactionId}/cancel',
  'https://mercury-t2.phonepe.com/v1/edc/transaction/init',
  'https://mercury-t2.phonepe.com/v1/edc/transaction/{merchantId}/{transactionId}/status',
  'https://mercury-t2.phonepe.com/v3/charge/{merchantId}/{transactionId}/cancel',
  0, 0, 1
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `ret_pos_providers` WHERE `provider_code` = 'phonepe_iedc');

-- DOWN
-- DELETE FROM `ret_pos_providers` WHERE `provider_code` IN ('pinelabs', 'phonepe_dqr', 'phonepe_iedc');
