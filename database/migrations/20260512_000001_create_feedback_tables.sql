-- ========================================================================
-- Migration: Create Feedback Tables
-- Task: QR-Based Customer Feedback Form with Admin Reports
-- Created: 2026-05-12
-- ========================================================================

-- 1. feedback_header — Dynamic feedback question headers
CREATE TABLE IF NOT EXISTS `feedback_header` (
  `feedback_header_id` int(11) NOT NULL AUTO_INCREMENT,
  `short_code` varchar(100) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `date_add` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`feedback_header_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. feedback — Main feedback submissions
CREATE TABLE IF NOT EXISTS `feedback` (
  `feedback_id` int(11) NOT NULL AUTO_INCREMENT,
  `cus_id` int(11) DEFAULT NULL,
  `firstname` varchar(150) DEFAULT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `mail_id` varchar(150) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `date_of_anniversary` date DEFAULT NULL,
  `pincode` varchar(10) DEFAULT NULL,
  `city` int(11) DEFAULT NULL,
  `state` int(11) DEFAULT NULL,
  `source` tinyint(1) DEFAULT NULL COMMENT '1=Instagram,2=Facebook,3=Other Social Media,4=Bill Boards',
  `address` text DEFAULT NULL,
  `rating_support` tinyint(1) DEFAULT 0,
  `rating_ambiance` tinyint(1) DEFAULT 0,
  `rating_collection` tinyint(1) DEFAULT 0,
  `created_on` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`feedback_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. feedback_value — Dynamic feedback answers per submission
CREATE TABLE IF NOT EXISTS `feedback_value` (
  `feedback_value_id` int(11) NOT NULL AUTO_INCREMENT,
  `feedback_id` int(11) NOT NULL,
  `header_id` int(11) NOT NULL,
  `answer` text DEFAULT NULL,
  PRIMARY KEY (`feedback_value_id`),
  KEY `idx_feedback_id` (`feedback_id`),
  KEY `idx_header_id` (`header_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
