-- Migration: Create table ret_stone_discount_master
-- Source: database_queries.sql:116
-- Author: unknown
-- Safe: Uses IF NOT EXISTS

-- UP
CREATE TABLE IF NOT EXISTS `ret_stone_discount_master` (
  `id` int(11) NOT NULL,
  `id_stone_type` int(5) NOT NULL,
  `min_disc_per` decimal(5,2) DEFAULT NULL,
  `max_disc_per` decimal(5,2) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0 - Inactive, 1 -Active',
  `created_by` varchar(15) NOT NULL,
  `created_on` datetime DEFAULT NULL,
  `modified_by` varchar(15) DEFAULT NULL,
  `modified_on` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
