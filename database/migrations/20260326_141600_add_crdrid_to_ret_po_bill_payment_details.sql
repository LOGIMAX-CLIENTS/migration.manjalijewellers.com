-- Add crdrid column to ret_po_bill_payment_details for tracking CR/DR credit payments
-- UP
ALTER TABLE `ret_po_bill_payment_details`
ADD COLUMN `crdrid` INT(11) DEFAULT NULL COMMENT 'FK to ret_crdr_note.crdrid for CR payment entries' AFTER `id_supplier_rate_cut`;
