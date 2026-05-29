-- Migration: Phase 1 — Performance indexes + backfill data on ret_pos_requests
-- Date: 26-03-2026
-- Source: database_queries.sql lines 12994-13044
-- Safe: Uses procedure to skip existing indexes. Backfill uses WHERE IS NULL.

-- UP

-- Safe index creation: skip if index already exists
DROP PROCEDURE IF EXISTS _pos_add_index_if_not_exists;
DELIMITER //
CREATE PROCEDURE _pos_add_index_if_not_exists(IN tbl VARCHAR(64), IN idx VARCHAR(64), IN cols VARCHAR(255))
BEGIN
  IF NOT EXISTS (
    SELECT 1 FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = tbl AND INDEX_NAME = idx
  ) THEN
    SET @sql = CONCAT('CREATE INDEX `', idx, '` ON `', tbl, '` (', cols, ')');
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
  END IF;
END //
DELIMITER ;

CALL _pos_add_index_if_not_exists('ret_pos_requests', 'idx_pos_device', '`id_device`');
CALL _pos_add_index_if_not_exists('ret_pos_requests', 'idx_pos_status_date', '`pos_req_status`, `created_at`');
CALL _pos_add_index_if_not_exists('ret_pos_requests', 'idx_pos_ref_id', '`pos_res_ref_id`');
CALL _pos_add_index_if_not_exists('ret_pos_requests', 'idx_pos_bill_id', '`pos_req_bill_id`');
CALL _pos_add_index_if_not_exists('ret_pos_requests', 'idx_pos_provider', '`provider_code`');

DROP PROCEDURE IF EXISTS _pos_add_index_if_not_exists;

-- Backfill: populate id_device and provider_code from existing data
UPDATE `ret_pos_requests` r
  JOIN `ret_pos_device_list` d ON d.merchantid = r.pos_mer_id
SET r.id_device = d.id_device
WHERE r.id_device IS NULL;

UPDATE `ret_pos_requests` r
  JOIN `ret_pos_providers` p ON p.id_provider = r.id_provider
SET r.provider_code = p.provider_code
WHERE r.provider_code IS NULL;

-- DOWN
-- DROP INDEX `idx_pos_provider` ON `ret_pos_requests`;
-- DROP INDEX `idx_pos_bill_id` ON `ret_pos_requests`;
-- DROP INDEX `idx_pos_ref_id` ON `ret_pos_requests`;
-- DROP INDEX `idx_pos_status_date` ON `ret_pos_requests`;
-- DROP INDEX `idx_pos_device` ON `ret_pos_requests`;
