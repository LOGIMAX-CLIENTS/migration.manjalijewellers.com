-- Make po_id nullable in both adjustment tables since adjustments no longer require PO assignment
-- UP
ALTER TABLE `ret_crdr_note_po_adj`
MODIFY COLUMN `po_id` INT(11) DEFAULT NULL COMMENT 'FK to ret_purchase_order.po_id (NULL when no PO assigned)';

ALTER TABLE `ret_purchase_return_po_adj`
MODIFY COLUMN `po_id` INT(11) DEFAULT NULL COMMENT 'FK to ret_purchase_order.po_id (NULL when no PO assigned)';
