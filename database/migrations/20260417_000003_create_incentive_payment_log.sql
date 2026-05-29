-- ============================================================
-- Incentive Payment Log Table
-- Tracks each partial/full payment made against employee incentives
-- ============================================================
-- UP
CREATE TABLE IF NOT EXISTS `ret_emp_incentive_payment_log` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `id_employee` INT(11) NOT NULL,
  `id_branch` INT(11) DEFAULT NULL,
  `date_from` DATE NOT NULL COMMENT 'Report period start',
  `date_to` DATE NOT NULL COMMENT 'Report period end',
  `original_amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Total calculated incentive for the period',
  `given_amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Amount given in this payment',
  `remarks` VARCHAR(500) DEFAULT NULL,
  `created_by` INT(11) NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `idx_emp_date` (`id_employee`, `date_from`, `date_to`),
  KEY `idx_branch` (`id_branch`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
