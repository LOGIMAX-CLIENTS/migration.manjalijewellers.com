<?php
/**
 * POS Phase 2 Migration Runner — Settlement Table + Columns
 * Run via browser on both source and client DBs
 */

define('BASEPATH', realpath(dirname(__FILE__).'/../system/').'/');
define('APPPATH', realpath(dirname(__FILE__).'/../application/').'/');
define('ENVIRONMENT', 'development');
require(APPPATH . 'config/database.php');

$host = $db['default']['hostname'] ?? 'localhost';
$user = $db['default']['username'];
$pass = $db['default']['password'];
$dbname = $db['default']['database'];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<h2>POS Phase 2 Migration — Settlement Layer</h2>";
    echo "<p>Connected to: <b>$dbname</b> on $host</p><hr>";

    $statements = [
        // Settlement table
        ["CREATE TABLE IF NOT EXISTS pos_settlements (
            settlement_id INT NOT NULL AUTO_INCREMENT,
            provider_code VARCHAR(30) NOT NULL COMMENT 'pinelabs/phonepe_dqr/phonepe_iedc',
            provider_batch_id VARCHAR(100) DEFAULT NULL,
            settlement_date DATE NOT NULL,
            total_transactions INT DEFAULT 0,
            total_amount_paise BIGINT DEFAULT 0,
            commission_paise BIGINT DEFAULT 0,
            net_amount_paise BIGINT DEFAULT 0,
            bank_reference VARCHAR(100) DEFAULT NULL,
            id_bank INT DEFAULT NULL,
            status ENUM('PENDING','RECONCILED','DISPUTED') DEFAULT 'PENDING',
            notes TEXT DEFAULT NULL,
            created_by INT DEFAULT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (settlement_id),
            INDEX idx_settle_provider (provider_code, settlement_date),
            INDEX idx_settle_date (settlement_date),
            INDEX idx_settle_status (status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", "Create pos_settlements table"],
        
        // Settlement FK on ret_pos_requests
        ["ALTER TABLE ret_pos_requests ADD COLUMN settlement_id INT DEFAULT NULL COMMENT 'FK to pos_settlements'", "Add settlement_id column"],
        ["ALTER TABLE ret_pos_requests ADD COLUMN settled_at DATE DEFAULT NULL COMMENT 'Date settled'", "Add settled_at column"],
    ];
    
    $success = 0; $skipped = 0; $errors = 0;
    foreach ($statements as $stmt) {
        $sql = $stmt[0]; $desc = $stmt[1];
        try {
            $affected = $pdo->exec($sql);
            echo "<div style='color:green'>✅ $desc</div>";
            $success++;
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate column') !== false || strpos($e->getMessage(), 'Duplicate key') !== false || strpos($e->getMessage(), 'already exists') !== false) {
                echo "<div style='color:orange'>⚠️ $desc — already exists</div>";
                $skipped++;
            } else {
                echo "<div style='color:red'>❌ $desc — " . htmlspecialchars($e->getMessage()) . "</div>";
                $errors++;
            }
        }
    }
    
    echo "<hr><p><b>Results:</b> ✅ $success, ⚠️ $skipped, ❌ $errors</p>";
    
    echo "<h3>pos_settlements structure:</h3><pre>";
    $cols = $pdo->query("DESCRIBE pos_settlements")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($cols as $col) {
        echo "   " . str_pad($col['Field'], 22) . str_pad($col['Type'], 35) . $col['Null'] . "\n";
    }
    echo "</pre>";
    
} catch (PDOException $e) {
    echo "<div style='color:red'>Connection failed: " . htmlspecialchars($e->getMessage()) . "</div>";
}
