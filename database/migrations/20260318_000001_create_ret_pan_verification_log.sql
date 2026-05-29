-- Migration: Create table ret_pan_verification_log
-- Source: database_queries.sql:10557
-- Author: NAMBI MUTHU RAJA
-- Safe: Uses IF NOT EXISTS

-- UP
CREATE TABLE IF NOT EXISTS `ret_pan_verification_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pan_number` varchar(10) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `name_on_pan` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `response_json` longtext DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `pan_number` (`pan_number`),
  KEY `customer_id` (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
