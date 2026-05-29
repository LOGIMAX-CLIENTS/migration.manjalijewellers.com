-- ============================================================================
-- CI/CD Dashboard Database Schema
-- Run this in test_etail_v3 database
-- ============================================================================

-- Deployment history
CREATE TABLE IF NOT EXISTS cicd_deployments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    environment VARCHAR(50) NOT NULL,
    branch VARCHAR(100) NOT NULL,
    commit_hash VARCHAR(40),
    commit_message TEXT,
    deployed_by VARCHAR(100),
    status ENUM('success', 'failed', 'rollback') DEFAULT 'success',
    deploy_mode ENUM('git_pull', 'symlink') DEFAULT 'git_pull',
    duration_seconds INT DEFAULT 0,
    release_id VARCHAR(50) DEFAULT NULL,
    previous_release VARCHAR(50) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_environment (environment),
    INDEX idx_status (status),
    INDEX idx_created (created_at DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Environment configuration
CREATE TABLE IF NOT EXISTS cicd_environments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) UNIQUE NOT NULL,
    folder VARCHAR(100) NOT NULL,
    branch VARCHAR(100) NOT NULL,
    deploy_mode ENUM('git_pull', 'symlink') DEFAULT 'git_pull',
    url VARCHAR(255),
    current_commit VARCHAR(40),
    last_deploy TIMESTAMP NULL,
    status ENUM('active', 'maintenance', 'inactive') DEFAULT 'active',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert source environments
INSERT INTO cicd_environments (name, folder, branch, deploy_mode, url, status) VALUES
('Develop', 'test_etail_v3', 'Retail_1.1.1.0001', 'git_pull', 'https://retail.logimaxindia.com/test_etail_v3', 'active'),
('QA', 'QA', 'QA', 'git_pull', 'https://retail.logimaxindia.com/QA', 'active'),
('Support', 'Support', 'support', 'git_pull', 'https://retail.logimaxindia.com/Support', 'active'),
('Production', 'etail', 'Production', 'symlink', 'https://retail.logimaxindia.com/etail', 'active')
ON DUPLICATE KEY UPDATE 
    folder = VALUES(folder),
    branch = VALUES(branch);

-- Client repositories (for future client deployments)
CREATE TABLE IF NOT EXISTS cicd_clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id VARCHAR(50) UNIQUE NOT NULL,
    client_name VARCHAR(100) NOT NULL,
    repo_name VARCHAR(150),
    folder VARCHAR(100),
    branch VARCHAR(100) DEFAULT 'main',
    deploy_mode ENUM('git_pull', 'symlink') DEFAULT 'git_pull',
    url VARCHAR(255),
    webhook_secret VARCHAR(100),
    status ENUM('active', 'pending', 'inactive') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_deploy TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
