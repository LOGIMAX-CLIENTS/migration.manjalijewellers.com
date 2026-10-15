-- ============================================================
-- Patch: Cashfree auto debit — authorization payment through mobile_payment
-- Date: 2026-10-15
-- Run AFTER auto_debit_on_demand.sql
-- Purpose: When the authorization collects this month's installment, the app first calls
--          mobile_api/mobile_payment (same as a manual payment): it creates the pending
--          payment rows with ref_trans_id = txnid. That txnid is kept here; when Cashfree
--          reports the authorization SUCCESS, those rows are completed like any manual
--          payment (status, receipt no, account no, referral, sync, SMS).
-- ============================================================

ALTER TABLE `auto_debit_subscription`
  ADD COLUMN `auth_txnid` VARCHAR(50) NULL DEFAULT NULL COMMENT 'mobile_payment txnid (payment.ref_trans_id) of the authorization installment' AFTER `auth_is_installment`;
