-- Migration: Create table ret_crdr_ledger
-- Source: database_queries.sql:10542
-- Author: DEVADHARSHINI
-- Safe: Uses IF NOT EXISTS

-- UP
CREATE TABLE IF NOT EXISTS ret_crdr_ledger (
  id_crdr_ledger int(11) NOT NULL AUTO_INCREMENT,
  ledger_name varchar(255) NOT NULL,
  status tinyint(4) NOT NULL DEFAULT 1,
  created_on datetime DEFAULT NULL,
  created_by int(11) DEFAULT NULL,
  updated_on datetime DEFAULT NULL,
  updated_by int(11) DEFAULT NULL,
  PRIMARY KEY (id_crdr_ledger)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
