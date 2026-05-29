-- Migration: Create table ret_po_bill_payment_details
-- Source: database_queries.txt:551
-- Author: RUDRA
-- Safe: Uses IF NOT EXISTS

-- UP
CREATE TABLE IF NOT EXISTS `ret_po_bill_payment_details` (
  `pay_bill_id` int(11) NOT NULL,
  `pay_id` int(11) NOT NULL,
  `po_id` int(11) NOT NULL,
  `opening_id` int(11) NOT NULL,
  `id_supplier_rate_cut` int(11) NOT NULL,
  `bill_amount` double(15,2) NOT NULL,
  `total_amount` double(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
