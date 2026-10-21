-- ============================================================
-- Patch: Cashfree auto debit — DigiGold daily auto save
-- Date: 2026-10-17
-- Run AFTER auto_debit_charge_txnid.sql
-- Purpose: DigiGold plans (Is Digi + Flexible + "Amount to Weight (based on Amount)") can be debited
--          every day with a fixed amount the customer chooses. Monthly auto debit is unchanged.
--          - auto_debit_subscription.debit_frequency: MONTHLY (existing rows) / DAILY
--          - auto_debit_subscription.debit_end_on: last day a daily debit may fall on (plan pay window end)
--          - auto_debit_charge.due_day: day of the month for a daily debit (0 = monthly debit), so one
--            row per subscription per DAY for daily and still one per MONTH for monthly (unique key).
-- Used by: application/controllers/mobile_api.php, cf_autodebit.php,
--          application/models/cf_autodebit_model.php, digigold_modal.php
-- ============================================================

ALTER TABLE `auto_debit_subscription`
  ADD COLUMN `debit_frequency` VARCHAR(10) NOT NULL DEFAULT 'MONTHLY' COMMENT 'MONTHLY / DAILY (DigiGold daily auto save)' AFTER `debit_day`,
  ADD COLUMN `debit_end_on` DATE NULL DEFAULT NULL COMMENT 'DAILY: last debit date (end of the plan pay window)' AFTER `debit_frequency`;

ALTER TABLE `auto_debit_charge`
  ADD COLUMN `due_day` TINYINT NOT NULL DEFAULT 0 COMMENT 'DAILY: day of month of the debit; 0 = monthly debit' AFTER `due_month`,
  DROP INDEX `uniq_sub_month`,
  ADD UNIQUE KEY `uniq_sub_month` (`id_auto_debit_subscription`, `due_year`, `due_month`, `due_day`);
