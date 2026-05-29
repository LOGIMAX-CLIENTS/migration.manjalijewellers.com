-- Stock Age Master Table
-- This table stores age ranges for inventory aging
-- UP
CREATE TABLE IF NOT EXISTS `ret_stock_age_master` (
  `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `age_from` INT(11) NOT NULL COMMENT 'Age From (in days)',
  `age_to` INT(11) NOT NULL COMMENT 'Age To (in days)',
  `value` VARCHAR(255) NOT NULL COMMENT 'Display value or label',
  `status` TINYINT(1) DEFAULT 1 COMMENT '1=Active, 0=Inactive',
  `created_by` INT(11),
  `created_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_by` INT(11),
  `updated_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_status` (`status`),
  UNIQUE KEY `unique_age_range` (`age_from`, `age_to`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
