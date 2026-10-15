-- ============================================================
-- Patch: Cashfree auto debit — monthly debits through the manual payment pipeline
-- Date: 2026-10-15
-- Run AFTER auto_debit_auth_txnid.sql
-- Purpose: When the daily job requests a month's debit, it first creates that month's
--          pending payment (same fields as mobile_api/mobile_payment, ref_trans_id = txnid).
--          When Cashfree reports the debit, those rows are completed like a manual payment
--          (or marked failed). txnid links the monthly request to its payment rows.
-- ============================================================

ALTER TABLE `auto_debit_charge`
  ADD COLUMN `txnid` VARCHAR(50) NULL DEFAULT NULL COMMENT 'payment.ref_trans_id of this month''s pending payment' AFTER `payment_id`;
