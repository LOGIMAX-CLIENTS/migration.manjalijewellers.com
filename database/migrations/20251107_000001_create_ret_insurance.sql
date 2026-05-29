-- Migration: Create table ret_insurance
-- Source: database_queries.txt:469
-- Author: rudra
-- Safe: Uses IF NOT EXISTS

-- UP
CREATE TABLE IF NOT EXISTS `ret_insurance` (
  `insurance_id` int(11) NOT NULL AUTO_INCREMENT,
  `insurance_no` varchar(50) NOT NULL,
  `bill_id` int(11) NOT NULL,
  `insurance_amount` decimal(10,2) NOT NULL,
  `policy_from` date NOT NULL,
  `policy_to` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`insurance_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
