-- Migration: Add pos_req_id FK column to ret_billing_payment
-- Date: 26-03-2026
-- Source: database_queries.sql lines 12840-12845
-- Safe: ADD COLUMN — Duplicate column tolerated by runner

-- UP
ALTER TABLE `ret_billing_payment` ADD COLUMN `pos_req_id` INT DEFAULT NULL COMMENT 'FK to ret_pos_requests' AFTER `NB_type`;

-- DOWN
-- ALTER TABLE `ret_billing_payment` DROP COLUMN `pos_req_id`;
