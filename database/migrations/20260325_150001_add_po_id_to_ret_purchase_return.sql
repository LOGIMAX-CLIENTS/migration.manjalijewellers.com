-- Add po_id column to ret_purchase_return for linking returns to specific POs
-- UP
ALTER TABLE `ret_purchase_return`
ADD COLUMN `po_id` INT(11) DEFAULT NULL COMMENT 'Linked Purchase Order ID' AFTER `pur_ret_supplier_id`;
