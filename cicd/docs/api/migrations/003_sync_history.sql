-- CI/CD Sync History Table
-- Tracks all sync operations with full audit trail

CREATE TABLE IF NOT EXISTS cicd_sync_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id VARCHAR(100) NOT NULL,
    client_name VARCHAR(150),
    source_branch VARCHAR(50) NOT NULL,
    target_branch VARCHAR(50) NOT NULL,
    commit_sha VARCHAR(40),
    triggered_by VARCHAR(50),
    status ENUM('pending','syncing','success','failed','conflict') DEFAULT 'pending',
    pr_number INT,
    pr_url VARCHAR(255),
    error_message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_at TIMESTAMP NULL,
    INDEX idx_client (client_id),
    INDEX idx_status (status),
    INDEX idx_created (created_at DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add sync_status to existing clients if missing (for version tracking)
-- This is informational; the workflow will populate these fields
