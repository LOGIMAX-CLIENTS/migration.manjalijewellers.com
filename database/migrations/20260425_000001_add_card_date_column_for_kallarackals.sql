-- Migration: Add card_date  column to ret_billing_payment & ret_issue_rcpt_payment
-- Author: Nambi Muthu Raja 
-- Date: 2026-04-25
-- Description: Stores the card_date for Kallarackals

ALTER TABLE `ret_billing_payment` ADD `card_date` DATE NULL DEFAULT NULL AFTER `payment_ref_number`;
ALTER TABLE `ret_issue_rcpt_payment` ADD `card_date` DATE NULL DEFAULT NULL AFTER `payment_ref_number`;
