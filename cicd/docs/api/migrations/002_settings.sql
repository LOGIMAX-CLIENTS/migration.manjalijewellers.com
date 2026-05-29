-- ============================================================================
-- CI/CD Settings & Permissions Schema
-- Migration 002 - Run after 001_cicd_tables.sql
-- Does NOT delete any existing data
-- ============================================================================

-- Settings table for storing feature permissions and config
CREATE TABLE IF NOT EXISTS cicd_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    description VARCHAR(255),
    updated_by VARCHAR(50),
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default feature permissions by role (JSON format)
INSERT IGNORE INTO cicd_settings (setting_key, setting_value, description) VALUES
('feature_permissions', '{
    "admin": ["admin_dashboard", "support_dashboard", "sync_manager_full", "sync_manager_limited", "manage_clients", "settings", "clear_data", "view_history_full"],
    "support": ["support_dashboard", "sync_manager_limited", "view_history_limited"],
    "viewer": ["support_dashboard", "view_history_limited"]
}', 'Feature permissions by role'),
('default_source_branch', 'Production', 'Default source branch for syncs'),
('default_target_branch', 'staging', 'Default target branch for client syncs'),
('history_days_support', '7', 'Days of history visible to support users'),
('max_batch_size', '10', 'Maximum clients per batch sync');

-- Update existing cicd_users table to add support role if needed
-- This is safe to run multiple times
UPDATE cicd_users SET role = 'admin' WHERE role = 'admin';

-- ============================================================================
-- OPTIONAL: Manual cleanup commands (run manually when needed)
-- ============================================================================
-- DELETE FROM cicd_deployment_history WHERE started_at < DATE_SUB(NOW(), INTERVAL 30 DAY);
-- TRUNCATE TABLE cicd_deployment_history;
-- TRUNCATE TABLE cicd_deployments;
-- TRUNCATE TABLE cicd_auth_log;
