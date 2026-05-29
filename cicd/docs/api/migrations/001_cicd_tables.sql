-- CI/CD Dashboard Database Schema
-- Run this script via phpMyAdmin or MySQL client
-- Safe to run multiple times (uses IF NOT EXISTS, IGNORE, etc.)

-- ============================================================================
-- DEPLOYMENT HISTORY
-- ============================================================================
CREATE TABLE IF NOT EXISTS cicd_deployment_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id VARCHAR(100),
    client_name VARCHAR(200),
    environment VARCHAR(50) DEFAULT 'develop',
    branch VARCHAR(100),
    commit_hash VARCHAR(40),
    commit_message TEXT,
    status VARCHAR(20) DEFAULT 'pending',
    triggered_by VARCHAR(100),
    trigger_type VARCHAR(20) DEFAULT 'webhook',
    duration_seconds INT,
    error_message TEXT,
    rollback_of INT DEFAULT NULL,
    started_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    completed_at DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_client (client_id),
    INDEX idx_environment (environment),
    INDEX idx_status (status),
    INDEX idx_started (started_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================================
-- CLIENTS
-- ============================================================================
CREATE TABLE IF NOT EXISTS cicd_clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id VARCHAR(100) UNIQUE NOT NULL,
    client_name VARCHAR(200),
    repo_name VARCHAR(200),
    folder VARCHAR(200),
    branch VARCHAR(100) DEFAULT 'main',
    deploy_mode VARCHAR(20) DEFAULT 'git_pull',
    url VARCHAR(500),
    webhook_url VARCHAR(500),
    webhook_secret VARCHAR(100),
    status VARCHAR(20) DEFAULT 'pending',
    last_deploy_at DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================================
-- ENVIRONMENTS (simple version - just name & color)
-- ============================================================================
CREATE TABLE IF NOT EXISTS cicd_environments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) UNIQUE NOT NULL,
    color VARCHAR(20),
    sort_order INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default environments (uses only columns that exist)
-- Source repo: develop, qa, support, production
-- Clients: staging, production
INSERT IGNORE INTO cicd_environments (name, color, sort_order) VALUES
('develop', '#3b82f6', 1),
('qa', '#f59e0b', 2),
('support', '#22c55e', 3),
('staging', '#06b6d4', 4),
('production', '#ef4444', 5);

-- ============================================================================
-- DEPLOYMENTS LOG (simple)
-- ============================================================================
CREATE TABLE IF NOT EXISTS cicd_deployments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    environment VARCHAR(50),
    branch VARCHAR(100),
    commit_hash VARCHAR(40),
    commit_message TEXT,
    deployed_by VARCHAR(100),
    status VARCHAR(20) DEFAULT 'success',
    deploy_mode VARCHAR(50),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================================
-- AUTH LOG
-- ============================================================================
CREATE TABLE IF NOT EXISTS cicd_auth_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50),
    event VARCHAR(50),
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================================
-- USERS (optional - file-based auth is used by default)
-- ============================================================================
CREATE TABLE IF NOT EXISTS cicd_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    display_name VARCHAR(100),
    email VARCHAR(100),
    role VARCHAR(20) DEFAULT 'viewer',
    is_active TINYINT(1) DEFAULT 1,
    last_login DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default admin (password: admin123) - uses bcrypt
INSERT IGNORE INTO cicd_users (username, password_hash, display_name, role) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin');

-- ============================================================================
-- DONE! Dashboard should now show real data
-- ============================================================================
