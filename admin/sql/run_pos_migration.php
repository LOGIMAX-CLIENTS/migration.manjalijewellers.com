<?php
/**
 * POS Data Model Migration Runner
 * Runs the Phase 1 SQL migration using CodeIgniter's DB connection
 * 
 * Usage: Open in browser: http://localhost/etail_development_src/admin/sql/run_pos_migration.php
 */

// Bootstrap minimal CI
define('BASEPATH', realpath(dirname(__FILE__).'/../system/').'/');
define('APPPATH', realpath(dirname(__FILE__).'/../application/').'/');
define('ENVIRONMENT', 'development');

// Load database config
require(APPPATH . 'config/database.php');

$host = $db['default']['hostname'] ?? 'localhost';
$user = $db['default']['username'];
$pass = $db['default']['password'];
$dbname = $db['default']['database'];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<h2>POS Data Model Migration — Phase 1</h2>";
    echo "<p>Connected to: <b>$dbname</b> on $host</p><hr>";

    $statements = [
        // Step 1: Add missing columns
        ["ALTER TABLE ret_pos_requests ADD COLUMN id_device INT DEFAULT NULL COMMENT 'FK to ret_pos_device_list'", "Add id_device column"],
        ["ALTER TABLE ret_pos_requests ADD COLUMN provider_code VARCHAR(30) DEFAULT NULL COMMENT 'pinelabs/phonepe_dqr/phonepe_iedc'", "Add provider_code column"],
        ["ALTER TABLE ret_pos_requests ADD COLUMN payment_mode VARCHAR(20) DEFAULT NULL COMMENT 'CARD/UPI/DQR/NB'", "Add payment_mode column"],
        ["ALTER TABLE ret_pos_requests ADD COLUMN card_last4 VARCHAR(4) DEFAULT NULL COMMENT 'Last 4 digits of card'", "Add card_last4 column"],
        ["ALTER TABLE ret_pos_requests ADD COLUMN approval_code VARCHAR(20) DEFAULT NULL COMMENT 'Bank approval/auth code'", "Add approval_code column"],
        ["ALTER TABLE ret_pos_requests ADD COLUMN updated_at DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT 'Last status change'", "Add updated_at column"],
        ["ALTER TABLE ret_pos_requests ADD COLUMN pos_cancelled_by INT DEFAULT NULL COMMENT 'User who cancelled'", "Add pos_cancelled_by column"],
        ["ALTER TABLE ret_pos_requests ADD COLUMN pos_cancelled_at DATETIME DEFAULT NULL COMMENT 'When cancelled'", "Add pos_cancelled_at column"],
        ["ALTER TABLE ret_pos_requests ADD COLUMN pos_last_checked_by INT DEFAULT NULL COMMENT 'User who last checked status'", "Add pos_last_checked_by column"],
        ["ALTER TABLE ret_pos_requests ADD COLUMN pos_last_checked_at DATETIME DEFAULT NULL COMMENT 'When last status check'", "Add pos_last_checked_at column"],
        
        // Step 2: Indexes
        ["CREATE INDEX idx_pos_device ON ret_pos_requests (id_device)", "Add index on id_device"],
        ["CREATE INDEX idx_pos_status_date ON ret_pos_requests (pos_req_status, created_at)", "Add index on status+date"],
        ["CREATE INDEX idx_pos_ref_id ON ret_pos_requests (pos_res_ref_id)", "Add index on ref_id"],
        ["CREATE INDEX idx_pos_bill_id ON ret_pos_requests (pos_req_bill_id)", "Add index on bill_id"],
        ["CREATE INDEX idx_pos_provider ON ret_pos_requests (provider_code)", "Add index on provider_code"],
        
        // Step 3: Payment sessions table
        ["CREATE TABLE IF NOT EXISTS pos_payment_sessions (
            session_id INT AUTO_INCREMENT PRIMARY KEY,
            invoice_id VARCHAR(50) NOT NULL COMMENT 'Bill/estimate ID',
            customer_id INT NOT NULL,
            device_id INT NOT NULL COMMENT 'FK to ret_pos_device_list',
            provider_code VARCHAR(30) NOT NULL,
            amount_paise INT NOT NULL DEFAULT 0,
            status ENUM('INIT','PENDING','SUCCESS','FAILED','CANCELLED','EXPIRED') DEFAULT 'INIT',
            pos_req_id INT DEFAULT NULL COMMENT 'FK to ret_pos_requests',
            created_by INT DEFAULT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            expires_at DATETIME DEFAULT NULL,
            updated_at DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_sess_invoice (invoice_id, status),
            INDEX idx_sess_customer (customer_id, status),
            INDEX idx_sess_expires (expires_at, status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", "Create pos_payment_sessions table"],
        
        // Step 4: Backfill id_device
        ["UPDATE ret_pos_requests r
            JOIN ret_pos_device_list d ON d.merchantid = r.pos_mer_id
        SET r.id_device = d.id_device
        WHERE r.id_device IS NULL", "Backfill id_device from merchant_id match"],
        
        // Step 5: Backfill provider_code
        ["UPDATE ret_pos_requests r
            JOIN ret_pos_providers p ON p.id_provider = r.id_provider
        SET r.provider_code = p.provider_code
        WHERE r.provider_code IS NULL", "Backfill provider_code from provider FK"],
    ];
    
    $success = 0;
    $skipped = 0;
    $errors = 0;
    
    foreach ($statements as $stmt) {
        $sql = $stmt[0];
        $desc = $stmt[1];
        try {
            $affected = $pdo->exec($sql);
            echo "<div style='color:green'>✅ $desc" . ($affected > 0 ? " ($affected rows affected)" : "") . "</div>";
            $success++;
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate column') !== false || strpos($e->getMessage(), 'Duplicate key') !== false) {
                echo "<div style='color:orange'>⚠️ $desc — already exists (skipped)</div>";
                $skipped++;
            } else {
                echo "<div style='color:red'>❌ $desc — " . htmlspecialchars($e->getMessage()) . "</div>";
                $errors++;
            }
        }
    }
    
    echo "<hr>";
    echo "<p><b>Results:</b> ✅ $success succeeded, ⚠️ $skipped skipped, ❌ $errors errors</p>";
    
    // Verification
    echo "<h3>Verification — ret_pos_requests columns:</h3><pre>";
    $cols = $pdo->query("DESCRIBE ret_pos_requests")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($cols as $col) {
        $mark = in_array($col['Field'], ['id_device','provider_code','payment_mode','card_last4','approval_code','updated_at','pos_cancelled_by','pos_cancelled_at','pos_last_checked_by','pos_last_checked_at']) ? '🆕 ' : '   ';
        echo $mark . str_pad($col['Field'], 25) . str_pad($col['Type'], 25) . $col['Null'] . "\n";
    }
    echo "</pre>";
    
    echo "<h3>Verification — pos_payment_sessions:</h3><pre>";
    $cols = $pdo->query("DESCRIBE pos_payment_sessions")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($cols as $col) {
        echo "   " . str_pad($col['Field'], 20) . str_pad($col['Type'], 40) . $col['Null'] . "\n";
    }
    echo "</pre>";
    
} catch (PDOException $e) {
    echo "<div style='color:red'>Connection failed: " . htmlspecialchars($e->getMessage()) . "</div>";
}
