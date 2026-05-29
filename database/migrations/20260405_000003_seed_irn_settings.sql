-- Migration: Seed IRN e-invoice settings into `ret_settings`
-- Feature: IRN E-Invoice Integration
-- Safe: Uses WHERE NOT EXISTS to avoid duplicates
-- Values: All disabled by default — configure via IRN Settings page

-- UP

-- is_auto_gen_irn: 0=disabled, 1=print JSON (debug), 2=generate IRN (live)
INSERT INTO `ret_settings` (`name`, `value`, `description`)
SELECT 'is_auto_gen_irn', '0', '0=disabled, 1=print JSON, 2=auto generate IRN'
WHERE NOT EXISTS (
    SELECT 1 FROM `ret_settings` WHERE `name` = 'is_auto_gen_irn'
);

-- is_production: 0=non-production, 1=production (must be 1 for IRN generation)
INSERT INTO `ret_settings` (`name`, `value`, `description`)
SELECT 'is_production', '0', '0=non-production, 1=production (IRN guard)'
WHERE NOT EXISTS (
    SELECT 1 FROM `ret_settings` WHERE `name` = 'is_production'
);

-- production_base_url: Must match base_url() for IRN generation to fire
INSERT INTO `ret_settings` (`name`, `value`, `description`)
SELECT 'production_base_url', '', 'Must match base_url() for IRN to fire'
WHERE NOT EXISTS (
    SELECT 1 FROM `ret_settings` WHERE `name` = 'production_base_url'
);

-- gsp_auth_base_url: Chartered Info auth endpoint (base URL without params)
INSERT INTO `ret_settings` (`name`, `value`, `description`)
SELECT 'gsp_auth_base_url', '', 'GSP auth endpoint base URL'
WHERE NOT EXISTS (
    SELECT 1 FROM `ret_settings` WHERE `name` = 'gsp_auth_base_url'
);

-- gsp_einvoice_base_url: Chartered Info invoice endpoint (base URL without params)
INSERT INTO `ret_settings` (`name`, `value`, `description`)
SELECT 'gsp_einvoice_base_url', '', 'GSP e-invoice endpoint base URL'
WHERE NOT EXISTS (
    SELECT 1 FROM `ret_settings` WHERE `name` = 'gsp_einvoice_base_url'
);

-- irn_error_email: Email for critical IRN failure alerts
INSERT INTO `ret_settings` (`name`, `value`, `description`)
SELECT 'irn_error_email', '', 'Email for IRN error alerts'
WHERE NOT EXISTS (
    SELECT 1 FROM `ret_settings` WHERE `name` = 'irn_error_email'
);

-- usp_id: USP ID (if using USP-based GSP integration)
INSERT INTO `ret_settings` (`name`, `value`, `description`)
SELECT 'usp_id', '', 'USP ID for GSP integration'
WHERE NOT EXISTS (
    SELECT 1 FROM `ret_settings` WHERE `name` = 'usp_id'
);

-- ci_password: Chartered Info global password (fallback)
INSERT INTO `ret_settings` (`name`, `value`, `description`)
SELECT 'ci_password', '', 'Chartered Info global password'
WHERE NOT EXISTS (
    SELECT 1 FROM `ret_settings` WHERE `name` = 'ci_password'
);

-- DOWN
-- DELETE FROM `ret_settings` WHERE `name` IN ('is_auto_gen_irn', 'is_production', 'production_base_url', 'gsp_auth_base_url', 'gsp_einvoice_base_url', 'irn_error_email', 'usp_id', 'ci_password');
