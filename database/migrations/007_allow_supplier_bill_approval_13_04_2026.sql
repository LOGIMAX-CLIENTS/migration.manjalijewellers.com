-- Migration: Add allow_supplier_bill_approval column to `profile`
-- Date: 13-04-2026
-- Purpose: Profile-based control for supplier bill approval checkbox visibility on Purchase Entry list page
-- Safe: Column checked via INFORMATION_SCHEMA before adding

-- UP

ALTER TABLE `profile`
ADD `allow_supplier_bill_approval` TINYINT(1) NOT NULL DEFAULT 0
COMMENT '0 -> No, 1 -> Yes';