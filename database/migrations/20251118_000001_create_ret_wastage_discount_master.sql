-- Migration: Create table ret_wastage_discount_master
-- Source: database_queries.sql:131
-- Author: unknown
-- Safe: Uses IF NOT EXISTS

-- UP
CREATE TABLE IF NOT EXISTS `ret_wastage_discount_master` (
  `id_wastage` int(11) NOT NULL,
  `id_metal` int(5) NOT NULL,
  `from_range` decimal(5,2) DEFAULT NULL,
  `to_range` decimal(5,2) DEFAULT NULL,
  `value` decimal(10,2) DEFAULT NULL,
  `created_by` varchar(15) NOT NULL,
  `created_on` datetime DEFAULT NULL,
  `modified_by` varchar(15) NOT NULL,
  `modified_on` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
