-- ============================================================
-- Patch: Cashfree auto debit — customer-chosen debit day (On-Demand mandate)
-- Date: 2026-10-15
-- Run AFTER auto_debit_subscription_auth_payment.sql
-- Purpose: The customer picks a monthly debit day (1-28) when subscribing.
--          This month's installment is paid as the authorization amount
--          (or Rs.1, refunded, when this month is already paid).
--          Each later installment is requested from Cashfree by the daily
--          job (cf_autodebit/cron_reconcile) a few days before the debit day.
-- Used by: application/controllers/mobile_api.php, cf_autodebit.php,
--          application/models/mobileapi_model.php
-- ============================================================

ALTER TABLE `auto_debit_subscription`
  ADD COLUMN `mandate_type` VARCHAR(20) NOT NULL DEFAULT 'PERIODIC' COMMENT 'PERIODIC: Cashfree debits by itself (older subscriptions) / ON_DEMAND: our daily job requests each debit' AFTER `first_charge_on`,
  ADD COLUMN `debit_day` TINYINT NULL DEFAULT NULL COMMENT 'Monthly debit day chosen by the customer (1-28), ON_DEMAND only' AFTER `mandate_type`,
  ADD COLUMN `installment_amount` DECIMAL(12,2) NULL DEFAULT NULL COMMENT 'Amount requested for each monthly debit' AFTER `debit_day`,
  ADD COLUMN `auth_amount` DECIMAL(12,2) NULL DEFAULT NULL COMMENT 'Amount charged at authorization' AFTER `installment_amount`,
  ADD COLUMN `auth_is_installment` TINYINT NOT NULL DEFAULT 0 COMMENT '1 = the authorization payment is this month''s installment (saved as a payment)' AFTER `auth_amount`;

-- One row per subscription per installment month: written BEFORE the debit is requested from Cashfree,
-- so the same month can never be requested twice (UNIQUE key), even if the job runs twice or times out.
CREATE TABLE IF NOT EXISTS `auto_debit_charge` (
  `id_auto_debit_charge` INT(11) NOT NULL AUTO_INCREMENT,
  `id_auto_debit_subscription` INT(11) NOT NULL,
  `id_scheme_account` INT(11) NOT NULL,
  `due_month` TINYINT NOT NULL,
  `due_year` SMALLINT NOT NULL,
  `payment_id` VARCHAR(100) NOT NULL COMMENT 'Merchant payment_id sent to Cashfree (fixed per month)',
  `payment_amount` DECIMAL(12,2) NOT NULL,
  `schedule_date` DATE NOT NULL,
  `status` VARCHAR(20) NOT NULL DEFAULT 'REQUESTING' COMMENT 'REQUESTING / REQUESTED / FAILED_REQUEST / SKIPPED',
  `message` VARCHAR(255) NULL DEFAULT NULL,
  `created_on` DATETIME NOT NULL,
  `last_update` DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`id_auto_debit_charge`),
  UNIQUE KEY `uniq_sub_month` (`id_auto_debit_subscription`, `due_year`, `due_month`),
  UNIQUE KEY `uniq_payment_id` (`payment_id`),
  KEY `idx_scheme_account` (`id_scheme_account`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
