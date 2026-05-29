-- Migration: Create table ret_ledger_transfer
-- Source: database_queries.sql:719
-- Author: DEVADHARSHINI
-- Safe: Uses IF NOT EXISTS

-- UP
CREATE TABLE IF NOT EXISTS `ret_ledger_transfer` (
  `transfer_id` int(11) NOT NULL AUTO_INCREMENT,
  `from_ledger_id` int(11) NOT NULL,
  `to_ledger_id` int(11) DEFAULT NULL,
  `amount` decimal(15,2) DEFAULT '0.00',
  `narration` text DEFAULT NULL,
  `transfer_type` int(11) DEFAULT '1' COMMENT '1 for Transfer, 2 for Manual',
  `transaction_type` int(11) DEFAULT NULL COMMENT '1 - Credit, 2 - Debit',
  `created_by` int(11) DEFAULT NULL,
  `transfer_date` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`transfer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
