-- ============================================================
-- Patch: Cashfree auto debit — authorization (Rs.1) payment result
-- Date: 2026-10-14
-- Purpose: Auto pay is enabled only after the Rs.1 AUTH payment of the
--          subscription succeeds (Cashfree SUBSCRIPTION_AUTH_STATUS /
--          AUTH payment in Fetch Subscription Payments). Until then the
--          customer stays on manual payment.
--          first_charge_on keeps the first debit date sent to Cashfree.
-- Used by: application/controllers/mobile_api.php, cf_autodebit.php,
--          application/models/mobileapi_model.php (get_autopay_block)
-- ============================================================

ALTER TABLE `auto_debit_subscription`
  ADD COLUMN `auth_payment_status` VARCHAR(20) NULL DEFAULT NULL COMMENT 'Rs.1 AUTH payment status from Cashfree: SUCCESS / FAILED / PENDING' AFTER `auth_link`,
  ADD COLUMN `auth_payment_id` VARCHAR(100) NULL DEFAULT NULL COMMENT 'Cashfree cf_payment_id of the AUTH payment' AFTER `auth_payment_status`,
  ADD COLUMN `auth_paid_on` DATETIME NULL DEFAULT NULL COMMENT 'When the AUTH payment succeeded (auto pay enabled from here)' AFTER `auth_payment_id`,
  ADD COLUMN `first_charge_on` DATE NULL DEFAULT NULL COMMENT 'First debit date sent as subscription_first_charge_time' AFTER `auth_paid_on`;

-- Block manual app payment while auto pay is enabled (0 = Block, 1 = Allow, 2 = block only while ACTIVE).
-- Required: the app / API manual-payment block (mobileapi_model::get_autopay_block) applies only with 0.
UPDATE `chit_settings` SET `auto_debit_allow_app_pay` = 0;

-- Backfill: open subscriptions already past authorization (BANK_APPROVAL_PENDING 2 / ACTIVE 3 / ON_HOLD 4)
-- can only get there after a successful Rs.1 AUTH, so mark them SUCCESS.
UPDATE `auto_debit_subscription`
   SET `auth_payment_status` = 'SUCCESS',
       `auth_paid_on` = IFNULL(`last_update`, `created_on`)
 WHERE `status` = 1
   AND `auth_status` IN (2, 3, 4)
   AND `auth_payment_status` IS NULL;
