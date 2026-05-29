-- ============================================================================
-- POS MODULE — COMPLETE DATABASE MIGRATION SCRIPT
-- Run this on any client that needs the full POS module
-- Last Updated: 11-05-2026
-- ============================================================================

-- ============================================================================
-- SECTION 1: TABLE CREATION (skip if tables already exist)
-- ============================================================================

-- 1A. Providers Table
CREATE TABLE IF NOT EXISTS `ret_pos_providers` (
  `id_provider` int NOT NULL AUTO_INCREMENT,
  `provider_name` varchar(50) NOT NULL,
  `provider_code` varchar(20) NOT NULL,
  `api_url_uat_init` varchar(255) DEFAULT NULL,
  `api_url_uat_status` varchar(255) DEFAULT NULL,
  `api_url_uat_cancel` varchar(255) DEFAULT NULL,
  `api_url_live_init` varchar(255) DEFAULT NULL,
  `api_url_live_status` varchar(255) DEFAULT NULL,
  `api_url_live_cancel` varchar(255) DEFAULT NULL,
  `auth_type` varchar(20) DEFAULT 'token' COMMENT 'token|sha256_header|basic_auth',
  `is_env_live` tinyint DEFAULT '0' COMMENT '0=UAT, 1=LIVE',
  `has_qr_display` tinyint DEFAULT '0' COMMENT '1=show QR on screen',
  `has_callback` tinyint DEFAULT '0' COMMENT '1=supports S2S callback',
  `status_method` varchar(10) DEFAULT 'POST' COMMENT 'POST or GET',
  `is_active` tinyint DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_provider`),
  UNIQUE KEY `uk_provider_code` (`provider_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 1B. Device List Table
CREATE TABLE IF NOT EXISTS `ret_pos_device_list` (
  `id_device` int NOT NULL AUTO_INCREMENT,
  `dispname` varchar(100) DEFAULT NULL,
  `devicetype` int DEFAULT '1' COMMENT '0=Card+UPI(Both), 1=Card only, 3=UPI QR only',
  `poscode` varchar(50) DEFAULT NULL COMMENT 'POS code (Pine Labs store code)',
  `merchantid` varchar(50) DEFAULT NULL,
  `securitytoken` varchar(100) DEFAULT NULL,
  `imei` varchar(50) DEFAULT NULL COMMENT 'Device IMEI (Pine Labs)',
  `is_default` tinyint DEFAULT '0' COMMENT '1=Default device (pre-selected)',
  `id_provider` int DEFAULT NULL COMMENT 'FK to ret_pos_providers',
  `store_id` varchar(50) DEFAULT NULL COMMENT 'PhonePe storeId',
  `terminal_id` varchar(50) DEFAULT NULL COMMENT 'PhonePe terminalId',
  `salt_key` varchar(100) DEFAULT NULL COMMENT 'PhonePe saltKey',
  `salt_index` varchar(10) DEFAULT NULL COMMENT 'PhonePe saltIndex',
  `provider_id` varchar(50) DEFAULT NULL COMMENT 'PhonePe X-PROVIDER-ID',
  `callback_url` varchar(255) DEFAULT NULL COMMENT 'S2S callback URL',
  `is_active` tinyint DEFAULT '1' COMMENT '0=inactive, 1=active',
  `id_pay_device` int DEFAULT NULL COMMENT 'Links to ret_bill_pay_device',
  `id_bank` int DEFAULT NULL COMMENT 'Links to bank — which bank receives money',
  PRIMARY KEY (`id_device`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 1C. POS Requests Table (full schema)
CREATE TABLE IF NOT EXISTS `ret_pos_requests` (
  `pos_req_id` int NOT NULL AUTO_INCREMENT,
  `pos_trans_no` varchar(100) DEFAULT NULL COMMENT 'Our generated transaction ID',
  `pos_store_pos_code` varchar(50) DEFAULT NULL COMMENT 'Store/POS code sent to provider',
  `pos_req_amount` decimal(12,2) DEFAULT NULL,
  `pos_usr_id` int DEFAULT NULL COMMENT 'User who initiated payment',
  `pos_mer_id` varchar(50) DEFAULT NULL,
  `pos_imie` varchar(50) DEFAULT NULL COMMENT 'Device IMEI sent with request',
  `pos_req_createdby` int DEFAULT NULL COMMENT 'Created by user ID',
  `pos_req_bill_cusid` int DEFAULT NULL COMMENT 'Customer ID from billing',
  `pos_req_bill_id` int DEFAULT NULL COMMENT 'Bill ID (FK to ret_billing/ret_estimation)',
  `pos_req_est_id` int DEFAULT NULL COMMENT 'Estimation ID (preserved even after bill save)',
  `pos_req_bill_type` tinyint DEFAULT NULL COMMENT 'Bill type: 1=Sales,2=S&P,3=S&P&R,5=OrdAdv,8=CreditColl,9=OrdDel,10=ChitPre,11=RepairDel,12=SupplierSales,15=ApprovalSales',
  `pos_req_source_ref` varchar(100) DEFAULT NULL COMMENT 'Source reference: order:F25/123, credit_bill:789',
  `pos_req_id_branch` int DEFAULT NULL COMMENT 'Branch ID where POS transaction was initiated',
  `pos_req_createdon` datetime DEFAULT CURRENT_TIMESTAMP COMMENT 'When the POS request was initiated',
  `pos_res_ref_id` varchar(100) DEFAULT NULL COMMENT 'Provider reference/transaction ID',
  `pos_res_trans_data` text COMMENT 'Full provider response data (JSON)',
  `pos_req_status` tinyint DEFAULT '0' COMMENT '0=INIT/PENDING, 1=SUCCESS, 2=CANCELLED, 3=FAILED',
  `pos_cancelled_by` int DEFAULT NULL COMMENT 'User who cancelled',
  `pos_cancelled_at` datetime DEFAULT NULL COMMENT 'When cancelled',
  `pos_success_at` datetime DEFAULT NULL COMMENT 'When payment confirmed successful',
  `pos_success_by` int DEFAULT NULL COMMENT 'UID who polled when success detected (NULL if S2S)',
  `pos_callback_at` datetime DEFAULT NULL COMMENT 'When S2S callback received from PhonePe',
  `pos_last_checked_by` int DEFAULT NULL COMMENT 'User who last checked status',
  `pos_last_checked_at` datetime DEFAULT NULL COMMENT 'When last status check',
  `settlement_id` int DEFAULT NULL COMMENT 'FK to pos_settlements',
  `settled_at` date DEFAULT NULL COMMENT 'Date settled',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `id_provider` int DEFAULT NULL COMMENT 'FK to ret_pos_providers',
  `id_device` int DEFAULT NULL COMMENT 'FK to ret_pos_device_list',
  `provider_code` varchar(30) DEFAULT NULL COMMENT 'pinelabs/phonepe_dqr/phonepe_iedc',
  `pos_qr_string` text COMMENT 'QR data for DQR flow',
  `pos_callback_data` text COMMENT 'S2S callback response',
  `idempotency_key` varchar(100) DEFAULT NULL COMMENT 'Prevents duplicate payments',
  `pos_utr` varchar(50) DEFAULT NULL COMMENT 'UPI UTR number',
  `payment_mode` varchar(20) DEFAULT NULL COMMENT 'CARD/UPI/DQR/NB',
  `card_last4` varchar(4) DEFAULT NULL COMMENT 'Last 4 digits of card',
  `approval_code` varchar(20) DEFAULT NULL COMMENT 'Bank approval/auth code',
  `pos_req_payload` text COMMENT 'Full API request payload (JSON)',
  `pos_res_payload` text COMMENT 'Full API response payload (JSON)',
  `pos_req_created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`pos_req_id`),
  UNIQUE KEY `idx_idempotency_key` (`idempotency_key`),
  KEY `idx_pos_device` (`id_device`),
  KEY `idx_pos_status_date` (`pos_req_status`,`created_at`),
  KEY `idx_pos_ref_id` (`pos_res_ref_id`),
  KEY `idx_pos_bill_id` (`pos_req_bill_id`),
  KEY `idx_pos_provider` (`provider_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- ============================================================================
-- SECTION 2: SAFE ALTER TABLE — adds columns ONLY if they don't exist
-- Uses a stored procedure to check information_schema before each ADD COLUMN
-- Fully idempotent: safe to run on fresh installs AND existing clients
-- ============================================================================

DELIMITER $$

DROP PROCEDURE IF EXISTS `sp_pos_migrate_columns`$$

CREATE PROCEDURE `sp_pos_migrate_columns`()
BEGIN
  DECLARE v_db VARCHAR(64) DEFAULT DATABASE();

  -- =========================================================================
  -- Helper: checks if column exists before adding
  -- Usage pattern:
  --   IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS
  --     WHERE TABLE_SCHEMA = v_db AND TABLE_NAME = 'xxx' AND COLUMN_NAME = 'yyy')
  --   THEN ALTER TABLE ... END IF;
  -- =========================================================================

  -- ── 2A. Business context columns ──────────────────────────────────────────

  IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = v_db AND TABLE_NAME = 'ret_pos_requests' AND COLUMN_NAME = 'pos_req_est_id')
  THEN
    ALTER TABLE `ret_pos_requests` ADD COLUMN `pos_req_est_id` INT DEFAULT NULL
      COMMENT 'Estimation ID (preserved even after bill save)' AFTER `pos_req_bill_id`;
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = v_db AND TABLE_NAME = 'ret_pos_requests' AND COLUMN_NAME = 'pos_req_bill_type')
  THEN
    ALTER TABLE `ret_pos_requests` ADD COLUMN `pos_req_bill_type` TINYINT DEFAULT NULL
      COMMENT 'Bill type: 1=Sales,2=S&P,3=S&P&R,5=OrdAdv,8=CreditColl,9=OrdDel,10=ChitPre,11=RepairDel,12=SupplierSales,15=ApprovalSales' AFTER `pos_req_est_id`;
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = v_db AND TABLE_NAME = 'ret_pos_requests' AND COLUMN_NAME = 'pos_req_source_ref')
  THEN
    ALTER TABLE `ret_pos_requests` ADD COLUMN `pos_req_source_ref` VARCHAR(100) DEFAULT NULL
      COMMENT 'Source reference: order:F25/123, credit_bill:789' AFTER `pos_req_bill_type`;
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = v_db AND TABLE_NAME = 'ret_pos_requests' AND COLUMN_NAME = 'pos_req_id_branch')
  THEN
    ALTER TABLE `ret_pos_requests` ADD COLUMN `pos_req_id_branch` INT DEFAULT NULL
      COMMENT 'Branch ID where POS transaction was initiated' AFTER `pos_req_source_ref`;
  END IF;

  -- ── 2B. Audit trail columns ───────────────────────────────────────────────

  IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = v_db AND TABLE_NAME = 'ret_pos_requests' AND COLUMN_NAME = 'pos_req_createdon')
  THEN
    ALTER TABLE `ret_pos_requests` ADD COLUMN `pos_req_createdon` DATETIME DEFAULT CURRENT_TIMESTAMP
      COMMENT 'When the POS request was initiated' AFTER `pos_req_id_branch`;
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = v_db AND TABLE_NAME = 'ret_pos_requests' AND COLUMN_NAME = 'pos_last_checked_by')
  THEN
    ALTER TABLE `ret_pos_requests` ADD COLUMN `pos_last_checked_by` INT DEFAULT NULL
      COMMENT 'User who last checked status' AFTER `pos_req_createdon`;
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = v_db AND TABLE_NAME = 'ret_pos_requests' AND COLUMN_NAME = 'pos_last_checked_at')
  THEN
    ALTER TABLE `ret_pos_requests` ADD COLUMN `pos_last_checked_at` DATETIME DEFAULT NULL
      COMMENT 'When last status check' AFTER `pos_last_checked_by`;
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = v_db AND TABLE_NAME = 'ret_pos_requests' AND COLUMN_NAME = 'pos_cancelled_by')
  THEN
    ALTER TABLE `ret_pos_requests` ADD COLUMN `pos_cancelled_by` INT DEFAULT NULL
      COMMENT 'User who cancelled' AFTER `pos_last_checked_at`;
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = v_db AND TABLE_NAME = 'ret_pos_requests' AND COLUMN_NAME = 'pos_cancelled_at')
  THEN
    ALTER TABLE `ret_pos_requests` ADD COLUMN `pos_cancelled_at` DATETIME DEFAULT NULL
      COMMENT 'When cancelled' AFTER `pos_cancelled_by`;
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = v_db AND TABLE_NAME = 'ret_pos_requests' AND COLUMN_NAME = 'pos_success_at')
  THEN
    ALTER TABLE `ret_pos_requests` ADD COLUMN `pos_success_at` DATETIME DEFAULT NULL
      COMMENT 'When payment confirmed successful' AFTER `pos_cancelled_at`;
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = v_db AND TABLE_NAME = 'ret_pos_requests' AND COLUMN_NAME = 'pos_success_by')
  THEN
    ALTER TABLE `ret_pos_requests` ADD COLUMN `pos_success_by` INT DEFAULT NULL
      COMMENT 'UID who polled when success detected (NULL if S2S)' AFTER `pos_success_at`;
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = v_db AND TABLE_NAME = 'ret_pos_requests' AND COLUMN_NAME = 'pos_callback_at')
  THEN
    ALTER TABLE `ret_pos_requests` ADD COLUMN `pos_callback_at` DATETIME DEFAULT NULL
      COMMENT 'When S2S callback received from PhonePe' AFTER `pos_success_by`;
  END IF;

  -- ── 2C. PhonePe / multi-provider columns ──────────────────────────────────

  IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = v_db AND TABLE_NAME = 'ret_pos_requests' AND COLUMN_NAME = 'id_provider')
  THEN
    ALTER TABLE `ret_pos_requests` ADD COLUMN `id_provider` INT DEFAULT NULL
      COMMENT 'FK to ret_pos_providers';
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = v_db AND TABLE_NAME = 'ret_pos_requests' AND COLUMN_NAME = 'id_device')
  THEN
    ALTER TABLE `ret_pos_requests` ADD COLUMN `id_device` INT DEFAULT NULL
      COMMENT 'FK to ret_pos_device_list';
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = v_db AND TABLE_NAME = 'ret_pos_requests' AND COLUMN_NAME = 'provider_code')
  THEN
    ALTER TABLE `ret_pos_requests` ADD COLUMN `provider_code` VARCHAR(30) DEFAULT NULL
      COMMENT 'pinelabs/phonepe_dqr/phonepe_iedc';
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = v_db AND TABLE_NAME = 'ret_pos_requests' AND COLUMN_NAME = 'pos_qr_string')
  THEN
    ALTER TABLE `ret_pos_requests` ADD COLUMN `pos_qr_string` TEXT
      COMMENT 'QR data for DQR flow';
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = v_db AND TABLE_NAME = 'ret_pos_requests' AND COLUMN_NAME = 'pos_callback_data')
  THEN
    ALTER TABLE `ret_pos_requests` ADD COLUMN `pos_callback_data` TEXT
      COMMENT 'S2S callback response';
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = v_db AND TABLE_NAME = 'ret_pos_requests' AND COLUMN_NAME = 'idempotency_key')
  THEN
    ALTER TABLE `ret_pos_requests` ADD COLUMN `idempotency_key` VARCHAR(100) DEFAULT NULL
      COMMENT 'Prevents duplicate payments';
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = v_db AND TABLE_NAME = 'ret_pos_requests' AND COLUMN_NAME = 'pos_utr')
  THEN
    ALTER TABLE `ret_pos_requests` ADD COLUMN `pos_utr` VARCHAR(50) DEFAULT NULL
      COMMENT 'UPI UTR number';
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = v_db AND TABLE_NAME = 'ret_pos_requests' AND COLUMN_NAME = 'payment_mode')
  THEN
    ALTER TABLE `ret_pos_requests` ADD COLUMN `payment_mode` VARCHAR(20) DEFAULT NULL
      COMMENT 'CARD/UPI/DQR/NB';
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = v_db AND TABLE_NAME = 'ret_pos_requests' AND COLUMN_NAME = 'card_last4')
  THEN
    ALTER TABLE `ret_pos_requests` ADD COLUMN `card_last4` VARCHAR(4) DEFAULT NULL
      COMMENT 'Last 4 digits of card';
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = v_db AND TABLE_NAME = 'ret_pos_requests' AND COLUMN_NAME = 'approval_code')
  THEN
    ALTER TABLE `ret_pos_requests` ADD COLUMN `approval_code` VARCHAR(20) DEFAULT NULL
      COMMENT 'Bank approval/auth code';
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = v_db AND TABLE_NAME = 'ret_pos_requests' AND COLUMN_NAME = 'pos_req_payload')
  THEN
    ALTER TABLE `ret_pos_requests` ADD COLUMN `pos_req_payload` TEXT
      COMMENT 'Full API request payload (JSON)';
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = v_db AND TABLE_NAME = 'ret_pos_requests' AND COLUMN_NAME = 'pos_res_payload')
  THEN
    ALTER TABLE `ret_pos_requests` ADD COLUMN `pos_res_payload` TEXT
      COMMENT 'Full API response payload (JSON)';
  END IF;

  -- ── 2D. Safe index creation ───────────────────────────────────────────────
  -- Check information_schema.STATISTICS before adding each index

  IF NOT EXISTS (SELECT 1 FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = v_db AND TABLE_NAME = 'ret_pos_requests' AND INDEX_NAME = 'idx_idempotency_key')
  THEN
    ALTER TABLE `ret_pos_requests` ADD UNIQUE KEY `idx_idempotency_key` (`idempotency_key`);
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = v_db AND TABLE_NAME = 'ret_pos_requests' AND INDEX_NAME = 'idx_pos_device')
  THEN
    ALTER TABLE `ret_pos_requests` ADD KEY `idx_pos_device` (`id_device`);
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = v_db AND TABLE_NAME = 'ret_pos_requests' AND INDEX_NAME = 'idx_pos_status_date')
  THEN
    ALTER TABLE `ret_pos_requests` ADD KEY `idx_pos_status_date` (`pos_req_status`, `created_at`);
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = v_db AND TABLE_NAME = 'ret_pos_requests' AND INDEX_NAME = 'idx_pos_ref_id')
  THEN
    ALTER TABLE `ret_pos_requests` ADD KEY `idx_pos_ref_id` (`pos_res_ref_id`);
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = v_db AND TABLE_NAME = 'ret_pos_requests' AND INDEX_NAME = 'idx_pos_bill_id')
  THEN
    ALTER TABLE `ret_pos_requests` ADD KEY `idx_pos_bill_id` (`pos_req_bill_id`);
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = v_db AND TABLE_NAME = 'ret_pos_requests' AND INDEX_NAME = 'idx_pos_provider')
  THEN
    ALTER TABLE `ret_pos_requests` ADD KEY `idx_pos_provider` (`provider_code`);
  END IF;

END$$

DELIMITER ;

-- Execute the migration procedure, then clean up
CALL `sp_pos_migrate_columns`();
DROP PROCEDURE IF EXISTS `sp_pos_migrate_columns`;


-- ============================================================================
-- SECTION 3: SEED DATA — Default providers
-- ============================================================================

INSERT IGNORE INTO `ret_pos_providers` (`provider_name`, `provider_code`, `auth_type`, `status_method`, `has_qr_display`, `has_callback`) VALUES
  ('Pine Labs', 'pinelabs', 'token', 'POST', 0, 0),
  ('PhonePe Dynamic QR', 'phonepe_dqr', 'sha256_header', 'GET', 1, 1),
  ('PhonePe IEDC', 'phonepe_iedc', 'sha256_header', 'GET', 0, 1);


-- ============================================================================
-- SECTION 4: DATA FIX — Backfill NULL branches from linked billing records
-- ============================================================================
UPDATE ret_pos_requests r
  LEFT JOIN ret_billing bl ON bl.bill_id = r.pos_req_bill_id
SET r.pos_req_id_branch = bl.id_branch
WHERE r.pos_req_id_branch IS NULL
  AND bl.id_branch IS NOT NULL;


-- ============================================================================
-- SECTION 5: SIDEBAR MENU — Rename & Cleanup
-- Run the SELECT first to verify the IDs on the target client
-- ============================================================================

-- Step 1: Find POS menu items
-- SELECT id_menu, label, link, parent, active, sort FROM menu
--   WHERE link LIKE '%admin_pos%' OR (label LIKE '%POS%' AND parent = 1)
--   ORDER BY parent, sort;

-- Step 2: Rename "Transaction Log" → "Payment Ledger"
UPDATE menu SET label = 'Payment Ledger'
  WHERE link = 'admin_pos/posTransactions';

-- Step 3: Deactivate "Settlement Summary" (dead page — no backend)
UPDATE menu SET active = 0
  WHERE link = 'admin_pos/posSettlementPage';


-- ============================================================================
-- SECTION 6: VERIFICATION QUERIES (run after all changes)
-- ============================================================================

-- 1. Verify all three POS tables exist
-- SHOW TABLES LIKE 'ret_pos_%';

-- 2. Verify ret_pos_requests has all 41 columns
-- SELECT COUNT(*) as col_count FROM information_schema.COLUMNS
--   WHERE TABLE_NAME = 'ret_pos_requests' AND TABLE_SCHEMA = DATABASE();

-- 3. Verify branch backfill worked (should return 0)
-- SELECT COUNT(*) AS missing_branches FROM ret_pos_requests
--   WHERE pos_req_id_branch IS NULL AND pos_req_bill_id IS NOT NULL;

-- 4. Verify menu changes
-- SELECT id_menu, label, link, active FROM menu
--   WHERE link LIKE '%admin_pos%' ORDER BY sort;
-- Expected: POS Settings (active=1), Payment Ledger (active=1), Settlement Summary (active=0)

-- 5. Verify providers are seeded
-- SELECT * FROM ret_pos_providers;


-- ============================================================================
-- SESSION/CODE USAGE REFERENCE (developer notes)
-- ============================================================================
-- Branch: saved via $CI->session->userdata('id_branch')
--   → POS_Phonepe_DQR.php, POS_Phonepe_IEDC.php
--
-- Audit columns set by:
--   pos_last_checked_by/at → admin_pos.php (status poll)
--   pos_cancelled_by/at   → admin_pos.php (cancel action)
--   pos_success_at/by     → POS_Phonepe_DQR.php, POS_Phonepe_IEDC.php (status check)
--   pos_callback_at       → admin_pos.php (S2S callback endpoint)
--   pos_req_createdon     → AUTO (DEFAULT CURRENT_TIMESTAMP)
--   created_at            → AUTO (DEFAULT CURRENT_TIMESTAMP)
--   updated_at            → AUTO (ON UPDATE CURRENT_TIMESTAMP)
