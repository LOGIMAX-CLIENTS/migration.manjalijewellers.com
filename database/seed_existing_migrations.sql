-- =============================================================================
-- SEED SCRIPT: Mark all existing migrations as "already applied"
-- =============================================================================
-- PURPOSE: Run this on each environment's database BEFORE enabling auto-migrations.
--          This prevents re-running migrations that were already applied manually.
--
-- USAGE:
--   mysql -u<user> -p<pass> <database> < seed_existing_migrations.sql
--
-- IMPORTANT:
--   - Run this ONCE per database (dev, qa, support, production, each client)
--   - Uses INSERT IGNORE so it's safe to run multiple times
--   - Change the @env variable below to match the target environment
-- =============================================================================

-- Set the environment name (change per target: develop, qa, support, production)
SET @env = 'develop';

-- Create tracking table if it doesn't exist
CREATE TABLE IF NOT EXISTS `schema_migrations` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `migration` VARCHAR(255) NOT NULL UNIQUE,
    `applied_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `environment` VARCHAR(20) DEFAULT 'production'
) ENGINE=InnoDB;

-- Seed all existing migration filenames
INSERT IGNORE INTO `schema_migrations` (`migration`, `environment`) VALUES
('20250526_000004_change_columns.sql', @env),
('20250613_000003_add_indexes.sql', @env),
('20250718_000002_add_columns_chit_settings.sql', @env),
('20250718_000002_add_columns_payment.sql', @env),
('20250718_000002_add_columns_scheme.sql', @env),
('20250718_000002_add_columns_scheme_account.sql', @env),
('20250718_000002_add_columns_transaction.sql', @env),
('20250731_000002_add_columns_customer.sql', @env),
('20250731_000002_add_columns_profile.sql', @env),
('20250806_000002_add_columns_kyc.sql', @env),
('20250816_000002_add_columns_payment_mode_details.sql', @env),
('20250904_000002_add_columns_company.sql', @env),
('20250904_000002_add_columns_general.sql', @env),
('20250904_000002_add_columns_log_detail.sql', @env),
('20250930_000002_add_columns_ret_karigar_metal_issue.sql', @env),
('20251011_000002_add_columns_ret_bill_pay_device.sql', @env),
('20251016_000002_add_columns_address.sql', @env),
('20251016_000002_add_columns_configuration.sql', @env),
('20251107_000001_create_ret_insurance.sql', @env),
('20251111_000002_add_columns_ret_billing.sql', @env),
('20251118_000001_create_ret_est_sales_return_utilization.sql', @env),
('20251118_000001_create_ret_stone_discount_master.sql', @env),
('20251118_000001_create_ret_wastage_discount_master.sql', @env),
('20251118_000002_add_columns_employee.sql', @env),
('20251118_000002_add_columns_employee_settings.sql', @env),
('20251118_000002_add_columns_ret_est_sales_return_utilization.sql', @env),
('20251118_000002_add_columns_ret_estimation.sql', @env),
('20251118_000002_add_columns_ret_estimation_item_stones.sql', @env),
('20251118_000002_add_columns_ret_estimation_items.sql', @env),
('20251128_000002_add_columns_scheme_benefit_deduct_settings.sql', @env),
('20251219_000002_add_columns_branch.sql', @env),
('20251222_000001_create_ret_po_bill_payment_details.sql', @env),
('20251222_000002_add_columns_ret_crdr_note.sql', @env),
('20251222_000002_add_columns_ret_purchase_order.sql', @env),
('20260112_000001_create_ledger_mapping.sql', @env),
('20260112_000001_create_ledger_master.sql', @env),
('20260112_000001_create_ret_ledger_transfer.sql', @env),
('20260112_000001_create_ret_order_email_logs.sql', @env),
('20260112_000002_add_columns_metal_rates.sql', @env),
('20260112_000002_add_columns_ret_bill_old_metal_sale_details.sql', @env),
('20260112_000002_add_columns_ret_karigar.sql', @env),
('20260112_000002_add_columns_ret_old_metal_category.sql', @env),
('20260112_000002_add_columns_ret_order_email_logs.sql', @env),
('20260112_000002_add_columns_ret_taging.sql', @env),
('20260307_000001_create_ret_crdr_ledger.sql', @env),
('20260316_000000_legacy_001_supplier_approval_ledger_touch.sql', @env),
('20260316_000000_legacy_002_md_approval_dashboard.sql', @env),
('20260316_000000_legacy_003_mail_updation_17_03_2026.sql', @env),
('20260316_000000_legacy_003_order_Status_msg_16_03_2026.sql', @env),
('20260318_000001_create_pos_payment_sessions.sql', @env),
('20260318_000001_create_pos_settlements.sql', @env),
('20260318_000001_create_ret_pan_verification_log.sql', @env),
('20260318_000001_create_ret_pos_device_list.sql', @env),
('20260318_000001_create_ret_pos_providers.sql', @env),
('20260318_000001_create_ret_pos_requests.sql', @env),
('20260321_000001_alter_ret_po_qc_issue_process_add_id_reason.sql', @env),
('20260323_000001_added_columns_receipt.sql', @env),
('20260324_000001_pos_module_menu_setup.sql', @env),
('20260325_000001_pos_menu_dedup_fix.sql', @env),
('20260326_000001_alter_ret_pos_providers_fix_columns.sql', @env),
('20260326_000002_alter_ret_pos_device_list_fix_columns.sql', @env),
('20260326_000003_alter_ret_pos_requests_fix_columns.sql', @env),
('20260326_000004_seed_pos_providers.sql', @env),
('20260326_000005_add_pos_req_id_to_billing_payment.sql', @env),
('20260326_000006_add_pos_requests_phase1_columns.sql', @env),
('20260326_000007_add_pos_requests_phase1_indexes.sql', @env),
('20260326_000008_add_pos_requests_phase2_settlement.sql', @env);

SELECT CONCAT('✅ Seeded ', COUNT(*), ' migrations for environment: ', @env) AS result
FROM schema_migrations;
