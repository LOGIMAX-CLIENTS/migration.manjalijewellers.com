-- Migration: Create table ledger_master
-- Source: database_queries.sql:692
-- Author: DEVADHARSHINI
-- Safe: Uses IF NOT EXISTS

-- UP
CREATE TABLE IF NOT EXISTS `ledger_master` (
  `id_ledger` int(11) NOT NULL AUTO_INCREMENT,
  `ledger_name` varchar(255) NOT NULL,
  `status` int(11) DEFAULT '1',
  `created_date` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_ledger`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
