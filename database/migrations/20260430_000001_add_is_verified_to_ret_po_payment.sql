-- Migration: Add is_verified column to ret_po_payment
-- Date: 2026-04-30
-- Description: The supplier payment listing page (admin_ret_purchase/supplier_po_payment/list)
--              fails on initial load because the query in get_PurchaseSupplierPaymentList()
--              selects pay.is_verified, which doesn't exist on older databases.
--              This column tracks whether a supplier payment has been verified.
-- Affected: ret_purchase_order_model.php line 1581, ret_reports_model.php line 20592
-- Rollback: ALTER TABLE `ret_po_payment` DROP COLUMN `is_verified`;
-- UP
ALTER TABLE `ret_po_payment`
ADD COLUMN `is_verified` INT(11) NOT NULL DEFAULT 0 AFTER `id_supplier_rate_cut`;
