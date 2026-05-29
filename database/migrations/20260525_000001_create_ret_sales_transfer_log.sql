-- Deemed Sales Transfer — Audit Log Table
-- Creates ret_sales_transfer_log for tracking create/download/cancel/update actions
-- Related to: ret_sales_transfer_model::log_transfer_action()

CREATE TABLE IF NOT EXISTS `ret_sales_transfer_log` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `bill_id` INT(11) NOT NULL COMMENT 'FK to ret_billing.bill_id',
  `action` VARCHAR(50) NOT NULL COMMENT 'create|download|cancel|update',
  `user_id` INT(11) DEFAULT NULL COMMENT 'FK to employee.id_employee',
  `ip_address` VARCHAR(50) DEFAULT NULL,
  `details` TEXT DEFAULT NULL COMMENT 'JSON payload with action context',
  `created_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_bill_id` (`bill_id`),
  KEY `idx_action` (`action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Audit log for Sales Transfer actions';
