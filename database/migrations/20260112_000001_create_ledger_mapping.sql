-- Migration: Create table ledger_mapping
-- Source: database_queries.sql:701
-- Author: DEVADHARSHINI
-- Safe: Uses IF NOT EXISTS

-- UP
CREATE TABLE IF NOT EXISTS `ledger_mapping` (
  `id_mapping` int(11) NOT NULL AUTO_INCREMENT,
  `id_ledger` int(11) NOT NULL,
  `type` enum('BANK','PAYMODE') NOT NULL,
  `reference_id` int(11) NOT NULL,
  `opening_balance` decimal(15,2) DEFAULT '0.00',
  PRIMARY KEY (`id_mapping`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
