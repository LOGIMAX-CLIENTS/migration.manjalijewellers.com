-- Migration for adding card_date column to payment tables
-- Task-ID: 05d28ff631f2
-- Safe: Non-destructive ALTER (adds nullable columns)

-- UP
ALTER TABLE `ret_billing_payment` ADD COLUMN `card_date` DATE NULL DEFAULT NULL AFTER `payment_ref_number`;
ALTER TABLE `ret_issue_rcpt_payment` ADD COLUMN `card_date` DATE NULL DEFAULT NULL AFTER `payment_ref_number`;
ALTER TABLE `ret_service_bill_payment` ADD COLUMN `card_date` DATE NULL DEFAULT NULL AFTER `payment_ref_number`;
