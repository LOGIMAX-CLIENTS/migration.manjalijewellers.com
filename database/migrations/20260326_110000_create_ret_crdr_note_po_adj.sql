-- CR/DR Note PO Adjustment tracking table
-- Tracks partial adjustments of CR/DR debit notes against specific PO bills
-- Remaining balance = ret_crdr_note.transamount - SUM(ret_crdr_note_po_adj.adjusted_amount)
-- UP
CREATE TABLE IF NOT EXISTS `ret_crdr_note_po_adj` (
  `adj_id` INT(11) NOT NULL AUTO_INCREMENT,
  `crdrid` INT(11) NOT NULL COMMENT 'FK to ret_crdr_note.crdrid',
  `po_id` INT(11) NOT NULL COMMENT 'FK to ret_purchase_order.po_id',
  `pay_id` INT(11) DEFAULT NULL COMMENT 'FK to ret_po_payment.pay_id (payment where adj was made)',
  `adjusted_amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `created_by` INT(11) DEFAULT NULL,
  `created_on` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`adj_id`),
  KEY `idx_crdrid` (`crdrid`),
  KEY `idx_po_id` (`po_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
