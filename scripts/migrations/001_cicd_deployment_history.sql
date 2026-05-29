-- ============================================================================
-- CI/CD Deployment History Database Schema
-- Migration: 001_cicd_deployment_history
-- Created: 2026-01-23
-- ============================================================================

-- Main deployment history table
CREATE TABLE IF NOT EXISTS `cicd_deployment_history` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `client_id` VARCHAR(100) NOT NULL COMMENT 'Client repository identifier',
    `client_name` VARCHAR(255) DEFAULT NULL COMMENT 'Human-readable client name',
    `environment` ENUM('staging', 'production', 'develop', 'qa', 'support') NOT NULL DEFAULT 'staging',
    `status` ENUM('pending', 'in_progress', 'success', 'failed', 'cancelled') NOT NULL DEFAULT 'pending',
    `branch` VARCHAR(100) DEFAULT 'main',
    `commit_sha` VARCHAR(40) DEFAULT NULL COMMENT 'Git commit SHA',
    `commit_message` TEXT DEFAULT NULL,
    `triggered_by` VARCHAR(100) DEFAULT 'webhook' COMMENT 'User or system that triggered deployment',
    `trigger_type` ENUM('push', 'manual', 'rollback', 'webhook') DEFAULT 'webhook',
    `duration_seconds` INT DEFAULT NULL COMMENT 'Total deployment duration',
    `started_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `completed_at` DATETIME DEFAULT NULL,
    `error_message` TEXT DEFAULT NULL,
    `server_response` JSON DEFAULT NULL COMMENT 'Raw server response data',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Indexes for common queries
    INDEX `idx_client_id` (`client_id`),
    INDEX `idx_environment` (`environment`),
    INDEX `idx_status` (`status`),
    INDEX `idx_started_at` (`started_at`),
    INDEX `idx_client_env` (`client_id`, `environment`),
    INDEX `idx_date_range` (`started_at`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Daily statistics aggregation table (for faster chart queries)
CREATE TABLE IF NOT EXISTS `cicd_deployment_stats` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `date` DATE NOT NULL,
    `environment` ENUM('staging', 'production', 'develop', 'qa', 'support') NOT NULL,
    `total_deployments` INT NOT NULL DEFAULT 0,
    `successful_deployments` INT NOT NULL DEFAULT 0,
    `failed_deployments` INT NOT NULL DEFAULT 0,
    `avg_duration_seconds` INT DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    UNIQUE KEY `uk_date_env` (`date`, `environment`),
    INDEX `idx_date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Client configuration table
CREATE TABLE IF NOT EXISTS `cicd_clients` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `client_id` VARCHAR(100) NOT NULL UNIQUE,
    `client_name` VARCHAR(255) NOT NULL,
    `repo_org` VARCHAR(100) DEFAULT 'LOGIMAX-CLIENTS',
    `repo_name` VARCHAR(255) NOT NULL,
    `server_type` ENUM('cpanel', 'aws', 'vps', 'other') DEFAULT 'cpanel',
    `webhook_url` VARCHAR(500) DEFAULT NULL,
    `webhook_secret` VARCHAR(64) DEFAULT NULL,
    `staging_branch` VARCHAR(100) DEFAULT 'staging',
    `production_branch` VARCHAR(100) DEFAULT 'main',
    `web_root` VARCHAR(500) DEFAULT '/home/user/public_html',
    `status` ENUM('pending', 'active', 'inactive', 'error') DEFAULT 'pending',
    `last_deployment_at` DATETIME DEFAULT NULL,
    `setup_completed_at` DATETIME DEFAULT NULL,
    `notes` TEXT DEFAULT NULL,
    `config_json` JSON DEFAULT NULL COMMENT 'Additional configuration',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX `idx_status` (`status`),
    INDEX `idx_repo` (`repo_org`, `repo_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- Sample data for testing (remove in production)
-- ============================================================================

-- Insert sample deployment history
INSERT INTO `cicd_deployment_history` 
    (`client_id`, `client_name`, `environment`, `status`, `branch`, `commit_sha`, `triggered_by`, `duration_seconds`, `started_at`, `completed_at`) 
VALUES 
    ('example.com', 'Example Corp', 'production', 'success', 'main', 'abc123def456', 'github-actions', 45, DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_SUB(NOW(), INTERVAL 1 DAY) + INTERVAL 45 SECOND),
    ('example.com', 'Example Corp', 'staging', 'success', 'staging', 'def789abc012', 'github-actions', 32, DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_SUB(NOW(), INTERVAL 2 DAY) + INTERVAL 32 SECOND),
    ('client2.com', 'Client Two', 'production', 'failed', 'main', 'fff111222333', 'manual', 120, DATE_SUB(NOW(), INTERVAL 3 DAY), DATE_SUB(NOW(), INTERVAL 3 DAY) + INTERVAL 120 SECOND),
    ('client3.com', 'Client Three', 'staging', 'success', 'develop', 'aaa444555666', 'webhook', 28, DATE_SUB(NOW(), INTERVAL 4 DAY), DATE_SUB(NOW(), INTERVAL 4 DAY) + INTERVAL 28 SECOND),
    ('example.com', 'Example Corp', 'staging', 'success', 'staging', 'bbb777888999', 'github-actions', 35, DATE_SUB(NOW(), INTERVAL 5 DAY), DATE_SUB(NOW(), INTERVAL 5 DAY) + INTERVAL 35 SECOND);

-- Insert sample daily stats
INSERT INTO `cicd_deployment_stats` (`date`, `environment`, `total_deployments`, `successful_deployments`, `failed_deployments`, `avg_duration_seconds`)
VALUES 
    (CURDATE(), 'staging', 5, 4, 1, 38),
    (CURDATE(), 'production', 3, 3, 0, 42),
    (DATE_SUB(CURDATE(), INTERVAL 1 DAY), 'staging', 8, 7, 1, 35),
    (DATE_SUB(CURDATE(), INTERVAL 1 DAY), 'production', 2, 2, 0, 48),
    (DATE_SUB(CURDATE(), INTERVAL 2 DAY), 'staging', 6, 5, 1, 40),
    (DATE_SUB(CURDATE(), INTERVAL 2 DAY), 'production', 4, 3, 1, 55),
    (DATE_SUB(CURDATE(), INTERVAL 3 DAY), 'staging', 7, 7, 0, 33),
    (DATE_SUB(CURDATE(), INTERVAL 3 DAY), 'production', 1, 1, 0, 45),
    (DATE_SUB(CURDATE(), INTERVAL 4 DAY), 'staging', 4, 4, 0, 30),
    (DATE_SUB(CURDATE(), INTERVAL 4 DAY), 'production', 2, 2, 0, 50),
    (DATE_SUB(CURDATE(), INTERVAL 5 DAY), 'staging', 9, 8, 1, 36),
    (DATE_SUB(CURDATE(), INTERVAL 5 DAY), 'production', 3, 2, 1, 60),
    (DATE_SUB(CURDATE(), INTERVAL 6 DAY), 'staging', 5, 5, 0, 32),
    (DATE_SUB(CURDATE(), INTERVAL 6 DAY), 'production', 2, 2, 0, 44);
