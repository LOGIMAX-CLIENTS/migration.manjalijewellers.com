-- Migration: Create table ret_est_sales_return_utilization
-- Source: database_queries.sql:67
-- Author: unknown
-- Safe: Uses IF NOT EXISTS

-- UP
CREATE TABLE IF NOT EXISTS `ret_est_sales_return_utilization` (
  `sr_ut_id` int(11) NOT NULL,
  `est_id` int(11) DEFAULT NULL,
  `bill_id` int(11) DEFAULT NULL,
  `bill_det_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
