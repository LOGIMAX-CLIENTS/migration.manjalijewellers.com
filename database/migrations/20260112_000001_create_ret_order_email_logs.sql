-- Migration: Create table ret_order_email_logs
-- Source: database_queries.sql:2679
-- Author: DEVADHARSHINI
-- Safe: Uses IF NOT EXISTS

-- UP
CREATE TABLE IF NOT EXISTS `ret_order_email_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_customerorder` int(11) NOT NULL,
  `id_karigar` int(11) NOT NULL,
  `email_id` varchar(255) DEFAULT NULL,
  `token` varchar(100) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0: Not Sent, 1: Sent, 2: Failed, 3: Accepted',
  `due_date` date DEFAULT NULL,
  `error_msg` text,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `accepted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_customerorder` (`id_customerorder`),
  KEY `token` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
