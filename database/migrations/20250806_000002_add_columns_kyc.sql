-- Migration: Add columns to `kyc`
-- Source: database_queries.txt
-- Safe: Each column checked via INFORMATION_SCHEMA before adding

-- UP

-- bank_name (database_queries.txt:333 by Esakki Jothika.K)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'kyc' AND COLUMN_NAME = 'bank_name');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE kyc ADD bank_name VARCHAR(100) NULL DEFAULT NULL AFTER bank_branch', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- cheque_img (database_queries.txt:334 by Esakki Jothika.K)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'kyc' AND COLUMN_NAME = 'cheque_img');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE kyc ADD cheque_img VARCHAR(500) NULL DEFAULT NULL AFTER img_url', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- cheque_doc_url (database_queries.txt:335 by Esakki Jothika.K)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'kyc' AND COLUMN_NAME = 'cheque_doc_url');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE kyc ADD cheque_doc_url VARCHAR(500) NULL DEFAULT NULL AFTER document_url', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- proof_name (database_queries.txt:336 by Esakki Jothika.K)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'kyc' AND COLUMN_NAME = 'proof_name');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE kyc ADD proof_name INT(11) NULL DEFAULT NULL AFTER kyc_type', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
