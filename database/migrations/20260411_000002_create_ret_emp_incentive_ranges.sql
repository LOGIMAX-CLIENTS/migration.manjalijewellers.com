-- ============================================================
-- Migration: Create ret_emp_incentive_ranges table
-- Description: Range slabs for incentive configs that use
--              rate_type = 2 (Weight Range) or 3 (Carat Range).
--              Each row defines a From-To range with its own
--              incentive value.
-- ============================================================
-- UP
CREATE TABLE IF NOT EXISTS `ret_emp_incentive_ranges` (
    `id`              INT(11)        NOT NULL AUTO_INCREMENT,
    `config_id`       INT(11)        NOT NULL COMMENT 'FK ret_emp_incentive_config.id',
    `range_from`      DECIMAL(12,4)  NOT NULL DEFAULT 0.0000 COMMENT 'Start of range (grams or carats)',
    `range_to`        DECIMAL(12,4)  NOT NULL DEFAULT 0.0000 COMMENT 'End of range (grams or carats)',
    `incentive_value` DECIMAL(12,4)  NOT NULL DEFAULT 0.0000 COMMENT 'Rate for this slab',
    PRIMARY KEY (`id`),
    KEY `idx_config` (`config_id`),
    CONSTRAINT `fk_incentive_range_config` FOREIGN KEY (`config_id`)
        REFERENCES `ret_emp_incentive_config` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
