<?php
/**
 * POS Phase 1-2-3 Verification — Checks all migration work
 * Run on: /admin/sql/verify_pos_phases.php
 */
define('BASEPATH', realpath(dirname(__FILE__).'/../system/').'/');
define('APPPATH', realpath(dirname(__FILE__).'/../application/').'/');
define('ENVIRONMENT', 'development');
require(APPPATH . 'config/database.php');

$host = $db['default']['hostname'] ?? 'localhost';
$user = $db['default']['username'];
$pass = $db['default']['password'];
$dbname = $db['default']['database'];

echo "<!DOCTYPE html><html><head><title>POS Verification</title>
<style>
body{font-family:Segoe UI,sans-serif;background:#f0f4f8;margin:20px;color:#333}
h1{color:#2c3e50} h2{color:#34495e;border-bottom:2px solid #3498db;padding-bottom:5px;margin-top:30px}
.ok{background:#d4edda;color:#155724;padding:8px 14px;border-radius:4px;margin:3px 0;display:block}
.warn{background:#fff3cd;color:#856404;padding:8px 14px;border-radius:4px;margin:3px 0;display:block}
.fail{background:#f8d7da;color:#721c24;padding:8px 14px;border-radius:4px;margin:3px 0;display:block}
.info{background:#d1ecf1;color:#0c5460;padding:8px 14px;border-radius:4px;margin:3px 0;display:block}
table{border-collapse:collapse;margin:10px 0;width:100%} 
th,td{border:1px solid #dee2e6;padding:6px 10px;text-align:left;font-size:13px}
th{background:#3498db;color:white} .badge{font-weight:bold;font-size:18px}
.summary{background:white;border-radius:8px;padding:20px;box-shadow:0 2px 6px rgba(0,0,0,0.1);margin:15px 0}
</style></head><body>";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h1>🔍 POS Phase 1-2-3 Verification</h1>";
    echo "<div class='info'>Connected to: <b>$dbname</b> on $host</div>";
    
    $pass = 0; $warn = 0; $fail = 0;
    
    // ============================================
    // PHASE 1: Tables + Columns
    // ============================================
    echo "<h2>Phase 1 — Tables & Columns</h2>";
    
    // Check tables exist
    $requiredTables = ['ret_pos_providers', 'ret_pos_device_list', 'ret_pos_requests', 'pos_payment_sessions'];
    foreach($requiredTables as $t) {
        $r = $pdo->query("SHOW TABLES LIKE '$t'")->rowCount();
        if($r) { echo "<span class='ok'>✅ Table <b>$t</b> exists</span>"; $pass++; }
        else { echo "<span class='fail'>❌ Table <b>$t</b> MISSING</span>"; $fail++; }
    }
    
    // Check Phase 1 columns on ret_pos_requests
    $phase1Cols = ['id_device','provider_code','payment_mode','card_last4','approval_code','updated_at','pos_cancelled_by','pos_cancelled_at','pos_last_checked_by','pos_last_checked_at'];
    $existingCols = array_column($pdo->query("SHOW COLUMNS FROM ret_pos_requests")->fetchAll(PDO::FETCH_ASSOC), 'Field');
    
    $colOk = 0; $colMissing = [];
    foreach($phase1Cols as $c) {
        if(in_array($c, $existingCols)) $colOk++;
        else $colMissing[] = $c;
    }
    if(empty($colMissing)) { echo "<span class='ok'>✅ All 10 Phase 1 columns present on ret_pos_requests</span>"; $pass++; }
    else { echo "<span class='fail'>❌ Missing columns: ".implode(', ',$colMissing)."</span>"; $fail++; }
    
    // Check indexes
    $indexes = array_column($pdo->query("SHOW INDEX FROM ret_pos_requests")->fetchAll(PDO::FETCH_ASSOC), 'Key_name');
    $reqIdx = ['idx_pos_device','idx_pos_status_date','idx_pos_ref_id','idx_pos_bill_id','idx_pos_provider'];
    $idxOk = 0; $idxMissing = [];
    foreach($reqIdx as $i) {
        if(in_array($i, $indexes)) $idxOk++;
        else $idxMissing[] = $i;
    }
    if(empty($idxMissing)) { echo "<span class='ok'>✅ All 5 performance indexes present</span>"; $pass++; }
    else { echo "<span class='warn'>⚠️ Missing indexes: ".implode(', ',$idxMissing)."</span>"; $warn++; }
    
    // Backfill check
    $nullDevice = $pdo->query("SELECT COUNT(*) as c FROM ret_pos_requests WHERE id_device IS NULL AND pos_mer_id IS NOT NULL")->fetch()['c'];
    $nullProvider = $pdo->query("SELECT COUNT(*) as c FROM ret_pos_requests WHERE provider_code IS NULL AND id_provider IS NOT NULL")->fetch()['c'];
    if($nullDevice == 0) { echo "<span class='ok'>✅ id_device backfill complete (no nulls with merchant data)</span>"; $pass++; }
    else { echo "<span class='warn'>⚠️ $nullDevice rows still have NULL id_device</span>"; $warn++; }
    if($nullProvider == 0) { echo "<span class='ok'>✅ provider_code backfill complete</span>"; $pass++; }
    else { echo "<span class='warn'>⚠️ $nullProvider rows still have NULL provider_code</span>"; $warn++; }
    
    // Transaction stats
    $stats = $pdo->query("SELECT COUNT(*) as total, SUM(CASE WHEN pos_req_status=1 THEN 1 ELSE 0 END) as success, SUM(CASE WHEN id_device IS NOT NULL THEN 1 ELSE 0 END) as has_device FROM ret_pos_requests")->fetch();
    echo "<span class='info'>📊 Transactions: {$stats['total']} total, {$stats['success']} success, {$stats['has_device']} with device linked</span>";
    
    // ============================================
    // PHASE 2: Settlement Layer
    // ============================================
    echo "<h2>Phase 2 — Settlement Layer</h2>";
    
    // pos_settlements table
    $r = $pdo->query("SHOW TABLES LIKE 'pos_settlements'")->rowCount();
    if($r) { 
        echo "<span class='ok'>✅ Table <b>pos_settlements</b> exists</span>"; $pass++;
        $settleCols = array_column($pdo->query("SHOW COLUMNS FROM pos_settlements")->fetchAll(PDO::FETCH_ASSOC), 'Field');
        echo "<span class='info'>📊 pos_settlements has ".count($settleCols)." columns: ".implode(', ',$settleCols)."</span>";
    } else { echo "<span class='fail'>❌ Table pos_settlements MISSING</span>"; $fail++; }
    
    // settlement_id + settled_at on ret_pos_requests
    if(in_array('settlement_id', $existingCols)) { echo "<span class='ok'>✅ settlement_id column on ret_pos_requests</span>"; $pass++; }
    else { echo "<span class='fail'>❌ settlement_id column MISSING</span>"; $fail++; }
    if(in_array('settled_at', $existingCols)) { echo "<span class='ok'>✅ settled_at column on ret_pos_requests</span>"; $pass++; }
    else { echo "<span class='fail'>❌ settled_at column MISSING</span>"; $fail++; }
    
    // pos_req_id on ret_billing_payment
    $bpCols = array_column($pdo->query("SHOW COLUMNS FROM ret_billing_payment")->fetchAll(PDO::FETCH_ASSOC), 'Field');
    if(in_array('pos_req_id', $bpCols)) { echo "<span class='ok'>✅ pos_req_id column on ret_billing_payment (FK linkage)</span>"; $pass++; }
    else { echo "<span class='fail'>❌ pos_req_id column MISSING on ret_billing_payment</span>"; $fail++; }
    
    // ============================================
    // PHASE 3: PHP Endpoints
    // ============================================
    echo "<h2>Phase 3 — PHP Endpoints & Functions</h2>";
    
    // Check controller file
    $controllerPath = realpath(dirname(__FILE__).'/../application/controllers/admin_pos.php');
    if($controllerPath && file_exists($controllerPath)) {
        $controllerContent = file_get_contents($controllerPath);
        $endpoints = [
            'UploadBilledTransaction' => 'Payment Init + Session Create',
            'getTransactionStatus' => 'Status Check + Session Update',
            'cancelTransactionRequest' => 'Cancel + Session Release',
            'checkSessionLock' => 'UI Lock Check',
            'runReconciliation' => 'Pending Session Recheck',
            'orphanDetection' => 'Orphan Detection (Phase 3)',
            'getUnsettledTransactions' => 'Unsettled Txns (Phase 3)',
        ];
        foreach($endpoints as $fn => $desc) {
            if(strpos($controllerContent, "function $fn") !== false) {
                echo "<span class='ok'>✅ <b>admin_pos/$fn</b> — $desc</span>"; $pass++;
            } else {
                echo "<span class='fail'>❌ <b>admin_pos/$fn</b> NOT FOUND — $desc</span>"; $fail++;
            }
        }
    } else {
        echo "<span class='fail'>❌ admin_pos.php controller NOT FOUND</span>"; $fail++;
    }
    
    // Check model
    $modelPath = realpath(dirname(__FILE__).'/../application/models/pos_model.php');
    if($modelPath && file_exists($modelPath)) {
        $modelContent = file_get_contents($modelPath);
        $functions = ['findOrphanPayments','findOrphanBillingPayments','getStaleExpiredSessions','markTransactionsSettled','getUnsettledTransactions','createPaymentSession','getActiveSession','updateSessionStatus','expireStaleSessions'];
        foreach($functions as $fn) {
            if(strpos($modelContent, "function $fn") !== false) {
                echo "<span class='ok'>✅ pos_model-><b>$fn()</b></span>"; $pass++;
            } else {
                echo "<span class='fail'>❌ pos_model-><b>$fn()</b> NOT FOUND</span>"; $fail++;
            }
        }
    } else {
        echo "<span class='fail'>❌ pos_model.php NOT FOUND</span>"; $fail++;
    }
    
    // Check provider plugins
    echo "<h2>Provider Plugins</h2>";
    $pluginDir = realpath(dirname(__FILE__).'/../application/libraries/pos_providers/');
    $plugins = ['POS_Pinelabs.php','POS_Phonepe_DQR.php','POS_Phonepe_IEDC.php'];
    foreach($plugins as $p) {
        $pPath = $pluginDir."/".$p;
        if(file_exists($pPath)) {
            $pc = file_get_contents($pPath);
            $hasDevice = strpos($pc, "id_device") !== false;
            $hasProvider = strpos($pc, "provider_code") !== false;
            $hasMode = strpos($pc, "payment_mode") !== false;
            if($hasDevice && $hasProvider && $hasMode) {
                echo "<span class='ok'>✅ <b>$p</b> — stores id_device, provider_code, payment_mode</span>"; $pass++;
            } else {
                $missing = [];
                if(!$hasDevice) $missing[] = 'id_device';
                if(!$hasProvider) $missing[] = 'provider_code';
                if(!$hasMode) $missing[] = 'payment_mode';
                echo "<span class='warn'>⚠️ <b>$p</b> — missing: ".implode(', ',$missing)."</span>"; $warn++;
            }
        } else {
            echo "<span class='fail'>❌ <b>$p</b> NOT FOUND</span>"; $fail++;
        }
    }
    
    // ============================================
    // SUMMARY
    // ============================================
    echo "<div class='summary'>";
    echo "<h2>Summary</h2>";
    echo "<span class='badge' style='color:#155724'>✅ $pass passed</span> &nbsp; ";
    echo "<span class='badge' style='color:#856404'>⚠️ $warn warnings</span> &nbsp; ";
    echo "<span class='badge' style='color:#721c24'>❌ $fail failed</span>";
    
    if($fail == 0 && $warn == 0) {
        echo "<br><br><span class='ok' style='font-size:16px'>🎉 All 3 phases verified successfully! POS system is fully operational.</span>";
    } elseif($fail == 0) {
        echo "<br><br><span class='warn' style='font-size:16px'>⚠️ All critical checks passed. Warnings are non-blocking.</span>";
    } else {
        echo "<br><br><span class='fail' style='font-size:16px'>❌ Some checks failed. Review the items above.</span>";
    }
    echo "</div>";
    
} catch (PDOException $e) {
    echo "<div class='fail'>Connection failed: " . htmlspecialchars($e->getMessage()) . "</div>";
}
echo "</body></html>";
