-- ============================================================
-- POS Data Model Upgrade — Phase 1 Migration
-- Created: 2026-03-14
-- Architecture: 4-Layer (Devices → Sessions → Transactions → Bill Payments)
-- ============================================================

-- -----------------------------------------------------------
-- STEP 1: Add missing columns to ret_pos_requests
-- -----------------------------------------------------------

-- Device tracking (critical for multi-terminal reporting)
ALTER TABLE ret_pos_requests
  ADD COLUMN IF NOT EXISTS id_device INT DEFAULT NULL COMMENT 'FK → ret_pos_device_list.id_device';

-- Quick provider filter (avoids JOIN for reports)
ALTER TABLE ret_pos_requests
  ADD COLUMN IF NOT EXISTS provider_code VARCHAR(30) DEFAULT NULL COMMENT 'pinelabs/phonepe_dqr/phonepe_iedc';

-- Payment mode extraction (searchable, not buried in JSON)
ALTER TABLE ret_pos_requests
  ADD COLUMN IF NOT EXISTS payment_mode VARCHAR(20) DEFAULT NULL COMMENT 'CARD/UPI/DQR/NB';

-- Card receipt fields (extracted from JSON for quick access)
ALTER TABLE ret_pos_requests
  ADD COLUMN IF NOT EXISTS card_last4 VARCHAR(4) DEFAULT NULL COMMENT 'Last 4 digits of card';

ALTER TABLE ret_pos_requests
  ADD COLUMN IF NOT EXISTS approval_code VARCHAR(20) DEFAULT NULL COMMENT 'Bank approval/auth code';

-- Status change tracking
ALTER TABLE ret_pos_requests
  ADD COLUMN IF NOT EXISTS updated_at DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT 'Last status change';

-- Cancel audit trail
ALTER TABLE ret_pos_requests
  ADD COLUMN IF NOT EXISTS pos_cancelled_by INT DEFAULT NULL COMMENT 'User who cancelled';

ALTER TABLE ret_pos_requests
  ADD COLUMN IF NOT EXISTS pos_cancelled_at DATETIME DEFAULT NULL COMMENT 'When cancelled';

-- Status check audit trail
ALTER TABLE ret_pos_requests
  ADD COLUMN IF NOT EXISTS pos_last_checked_by INT DEFAULT NULL COMMENT 'User who last checked status';

ALTER TABLE ret_pos_requests
  ADD COLUMN IF NOT EXISTS pos_last_checked_at DATETIME DEFAULT NULL COMMENT 'When last status check';


-- -----------------------------------------------------------
-- STEP 2: Add indexes for reporting performance
-- -----------------------------------------------------------

-- Only create if not already present (MariaDB/MySQL 10.5+ supports IF NOT EXISTS)
-- For older versions, wrap in a procedure or ignore errors
CREATE INDEX IF NOT EXISTS idx_pos_device ON ret_pos_requests (id_device);
CREATE INDEX IF NOT EXISTS idx_pos_status_date ON ret_pos_requests (pos_req_status, created_at);
CREATE INDEX IF NOT EXISTS idx_pos_ref_id ON ret_pos_requests (pos_res_ref_id);
CREATE INDEX IF NOT EXISTS idx_pos_bill_id ON ret_pos_requests (pos_req_bill_id);
CREATE INDEX IF NOT EXISTS idx_pos_provider ON ret_pos_requests (provider_code);


-- -----------------------------------------------------------
-- STEP 3: Create payment sessions table (Layer 2)
-- -----------------------------------------------------------

CREATE TABLE IF NOT EXISTS pos_payment_sessions (
  session_id INT AUTO_INCREMENT PRIMARY KEY,
  invoice_id VARCHAR(50) NOT NULL COMMENT 'Bill/estimate ID being paid',
  customer_id INT NOT NULL,
  device_id INT NOT NULL COMMENT 'FK → ret_pos_device_list.id_device',
  provider_code VARCHAR(30) NOT NULL COMMENT 'pinelabs/phonepe_dqr/phonepe_iedc',
  amount_paise INT NOT NULL DEFAULT 0,
  status ENUM('INIT','PENDING','SUCCESS','FAILED','CANCELLED','EXPIRED') DEFAULT 'INIT',
  pos_req_id INT DEFAULT NULL COMMENT 'FK → ret_pos_requests.pos_req_id',
  created_by INT DEFAULT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  expires_at DATETIME DEFAULT NULL,
  updated_at DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_sess_invoice (invoice_id, status),
  INDEX idx_sess_customer (customer_id, status),
  INDEX idx_sess_expires (expires_at, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Payment session lock — prevents concurrent/duplicate payments';


-- -----------------------------------------------------------
-- STEP 4: Backfill existing data
-- -----------------------------------------------------------

-- Backfill id_device from merchant+store code match
UPDATE ret_pos_requests r
  JOIN ret_pos_device_list d 
    ON d.merchantid = r.pos_mer_id 
    AND (d.poscode = r.pos_store_pos_code OR d.store_id = r.pos_store_pos_code)
SET r.id_device = d.id_device
WHERE r.id_device IS NULL;

-- Backfill provider_code from provider FK
UPDATE ret_pos_requests r
  JOIN ret_pos_providers p ON p.id_provider = r.id_provider
SET r.provider_code = p.provider_code
WHERE r.provider_code IS NULL;


-- -----------------------------------------------------------
-- Verification query: check new columns exist
-- -----------------------------------------------------------
-- Run this to confirm:
-- DESCRIBE ret_pos_requests;
-- SHOW TABLES LIKE 'pos_payment_sessions';
