-- Migration: Add purity_type column to ret_purchase_order_items
-- Date: 2026-04-04
-- Description: Stores the purity type selection (999 or 995) for pure weight calculation

ALTER TABLE `ret_purchase_order_items` 
ADD COLUMN `purity_type` INT(4) NOT NULL DEFAULT 999 
COMMENT 'Purity type for pure weight calculation: 999=standard, 995=adjusted (÷0.995)' 
AFTER `pure_wt_calc_type`;
