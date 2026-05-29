-- Migration: Rebuild feedback tables to full production schema + add google_review_url to branch
-- Module: Customer Feedback (QR-Based)
-- Author: NAMBI MUTHU RAJA
-- Date: 2026-05-22
-- Reason: The original 20260512_000001 migration created a minimal feedback schema.
--         This migration drops and recreates all three feedback tables with the complete
--         production schema (branch linkage, staff ratings, purchase occasion, suggestions,
--         indexes, and proper comments). Also adds google_review_url to the branch table
--         for post-feedback Google Review redirect.

-- UP
SET @db_name = DATABASE();


-- ============================================================================
-- 0. Add google_review_url column to branch table (idempotent)
-- ============================================================================
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'branch' AND COLUMN_NAME = 'google_review_url');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `branch` ADD COLUMN `google_review_url` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL COMMENT ''Branch-specific Google Review URL for post-feedback redirect''', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;


-- ============================================================================
-- 1. Drop existing feedback tables (order matters — child first)
-- ============================================================================
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `feedback_value`;
DROP TABLE IF EXISTS `feedback`;
DROP TABLE IF EXISTS `feedback_header`;

SET FOREIGN_KEY_CHECKS = 1;


-- ============================================================================
-- 2. feedback_header — Dynamic questions shown on the feedback form
--    Managed via Admin → Catalog → Feedback Questions
-- ============================================================================
CREATE TABLE `feedback_header` (
  `feedback_header_id`  int(11)       NOT NULL AUTO_INCREMENT,
  `short_code`          varchar(100)  DEFAULT NULL    COMMENT 'Internal reference code',
  `feedback_question`   varchar(255)  DEFAULT NULL    COMMENT 'Question text shown on the form',
  `status`              tinyint(1)    NOT NULL DEFAULT 1  COMMENT '1=Active  0=Inactive',
  `date_add`            datetime      DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`feedback_header_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
  COMMENT='Dynamic feedback question bank — active rows appear on the form';


-- ============================================================================
-- 3. feedback — One row per submitted feedback form
-- ============================================================================
CREATE TABLE `feedback` (
  `feedback_id`          int(11)       NOT NULL AUTO_INCREMENT,

  -- ── Linkage ────────────────────────────────────────────────────────────────
  `branch_id`            int(11)       DEFAULT NULL
    COMMENT 'FK → branch.id_branch — branch whose QR was scanned',
  `cus_id`               int(11)       DEFAULT NULL
    COMMENT 'FK → customer.id_customer — NULL for walk-in / new customers',

  -- ── Personal Details ───────────────────────────────────────────────────────
  `firstname`            varchar(150)  DEFAULT NULL,
  `mobile`               varchar(20)   DEFAULT NULL,
  `mail_id`              varchar(150)  DEFAULT NULL,
  `date_of_birth`        date          DEFAULT NULL,
  `date_of_anniversary`  date          DEFAULT NULL,

  -- ── Address ────────────────────────────────────────────────────────────────
  `address`              text          DEFAULT NULL,
  `pincode`              varchar(10)   DEFAULT NULL,
  `state`                int(11)       DEFAULT NULL    COMMENT 'FK → state.id_state',
  `city`                 int(11)       DEFAULT NULL    COMMENT 'FK → city.id_city',

  -- ── Visit Info ─────────────────────────────────────────────────────────────
  `source`               tinyint(1)    DEFAULT NULL
    COMMENT '1=Social Media  2=Bill Board  3=Reference  4=Tele Calling',
  `purchase_occasion`    varchar(100)  DEFAULT NULL
    COMMENT 'Festival / Wedding / Engagement / Anniversary / Birthday / Gifting / Self-Use',
  `reason_not_purchase`  text          DEFAULT NULL
    COMMENT 'Optional — filled when customer did not make a purchase',

  -- ── Ratings (1–5 stars; 0 = not rated) ────────────────────────────────────
  `rating_ambiance`      tinyint(1)    NOT NULL DEFAULT 0,
  `rating_collection`    tinyint(1)    NOT NULL DEFAULT 0,
  `staff_id`             int(11)       DEFAULT NULL
    COMMENT 'FK → employee.id_employee — staff who assisted the customer',
  `rating_staff`         tinyint(1)    NOT NULL DEFAULT 0
    COMMENT 'Star rating 1–5 for the selected staff; 0 = not rated',

  -- ── Free Text ──────────────────────────────────────────────────────────────
  `suggestions`          text          DEFAULT NULL,

  -- ── Meta ───────────────────────────────────────────────────────────────────
  `created_on`           datetime      DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (`feedback_id`),
  KEY `idx_branch_id`  (`branch_id`),
  KEY `idx_cus_id`     (`cus_id`),
  KEY `idx_staff_id`   (`staff_id`),
  KEY `idx_created_on` (`created_on`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
  COMMENT='Main feedback submissions — one row per QR form submit';


-- ============================================================================
-- 4. feedback_value — Dynamic question answers
--    One row per active feedback_header question per submission
-- ============================================================================
CREATE TABLE `feedback_value` (
  `feedback_value_id`  int(11)  NOT NULL AUTO_INCREMENT,
  `feedback_id`        int(11)  NOT NULL  COMMENT 'FK → feedback.feedback_id',
  `header_id`          int(11)  NOT NULL  COMMENT 'FK → feedback_header.feedback_header_id',
  `answer`             text     DEFAULT NULL,
  PRIMARY KEY (`feedback_value_id`),
  KEY `idx_feedback_id` (`feedback_id`),
  KEY `idx_header_id`   (`header_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
  COMMENT='Dynamic question answers — one row per active feedback_header per submission';
