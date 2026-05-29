-- Migration: Add rate_benefit columns for chit adjustment billing
-- Purpose: Store the rate benefit amount that is deducted from taxable value BEFORE GST calculation
-- Ticket: [2042] Chit Adjustment Bill – Apply Rate Benefit Before GST Calculation
-- Date: 2026-05-25

-- Add rate_benefit column to ret_billing_chit_utilization
-- Stores the calculated rate benefit per chit account utilization
-- rate_benefit = scheme benefit amount deducted from taxable value before GST
ALTER TABLE `ret_billing_chit_utilization`
    ADD COLUMN `rate_benefit` DECIMAL(12,2) NOT NULL DEFAULT 0.00
    COMMENT 'Scheme benefit amount deducted from taxable value BEFORE GST calculation'
    AFTER `rate_per_gram`;

-- Add total chit benefit deducted before GST on the main billing record
-- This is the sum of all rate_benefit values from ret_billing_chit_utilization for this bill
ALTER TABLE `ret_billing`
    ADD COLUMN `chit_benefit_before_gst` DECIMAL(12,2) NOT NULL DEFAULT 0.00
    COMMENT 'Total chit/scheme benefit deducted from taxable value BEFORE GST calculation'
    AFTER `tot_amt_received`;
