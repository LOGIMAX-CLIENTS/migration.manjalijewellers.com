<?php
// Quick Phase 1-2-3 Verification (CLI)
$databases = array(
    'retail_dev' => 'SOURCE',
    'klson_staging' => 'CLIENT'
);

foreach ($databases as $dbname => $label) {
    echo "==============================\n";
    echo "$label DB: $dbname\n";
    echo "==============================\n";
    
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=$dbname;charset=utf8mb4", 'root', '1234');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "Connected OK\n\n";
        
        // Tables
        echo "--- TABLES ---\n";
        $tables = array('ret_pos_providers','ret_pos_device_list','ret_pos_requests','pos_payment_sessions','pos_settlements');
        foreach ($tables as $t) {
            $r = $pdo->query("SHOW TABLES LIKE '$t'")->rowCount();
            echo ($r ? "  OK" : "  MISS") . " $t\n";
        }
        
        // Phase 1 + 2 Columns
        echo "\n--- COLUMNS on ret_pos_requests ---\n";
        $cols = array_column($pdo->query("SHOW COLUMNS FROM ret_pos_requests")->fetchAll(PDO::FETCH_ASSOC), 'Field');
        $checkCols = array(
            'id_device','provider_code','payment_mode','card_last4','approval_code',
            'updated_at','pos_cancelled_by','pos_cancelled_at',
            'pos_last_checked_by','pos_last_checked_at',
            'settlement_id','settled_at'
        );
        foreach ($checkCols as $c) {
            echo (in_array($c, $cols) ? "  OK" : "  MISS") . " $c\n";
        }
        
        // Indexes
        echo "\n--- INDEXES ---\n";
        $indexes = array_column($pdo->query("SHOW INDEX FROM ret_pos_requests")->fetchAll(PDO::FETCH_ASSOC), 'Key_name');
        $indexes = array_unique($indexes);
        $reqIdx = array('idx_pos_device','idx_pos_status_date','idx_pos_ref_id','idx_pos_bill_id','idx_pos_provider');
        foreach ($reqIdx as $i) {
            echo (in_array($i, $indexes) ? "  OK" : "  MISS") . " $i\n";
        }
        
        // pos_req_id on ret_billing_payment
        echo "\n--- FK LINKAGE ---\n";
        $bpCols = array_column($pdo->query("SHOW COLUMNS FROM ret_billing_payment")->fetchAll(PDO::FETCH_ASSOC), 'Field');
        echo (in_array('pos_req_id', $bpCols) ? "  OK" : "  MISS") . " pos_req_id on ret_billing_payment\n";
        
        // Stats
        echo "\n--- STATS ---\n";
        $s = $pdo->query("SELECT COUNT(*) as total, SUM(CASE WHEN pos_req_status=1 THEN 1 ELSE 0 END) as success, SUM(CASE WHEN id_device IS NOT NULL THEN 1 ELSE 0 END) as has_device FROM ret_pos_requests")->fetch();
        echo "  Total transactions: " . $s['total'] . "\n";
        echo "  Successful: " . $s['success'] . "\n";
        echo "  With device linked: " . $s['has_device'] . "\n";
        
        // pos_settlements columns
        echo "\n--- pos_settlements columns ---\n";
        $sc = $pdo->query("SHOW COLUMNS FROM pos_settlements")->fetchAll(PDO::FETCH_ASSOC);
        echo "  " . count($sc) . " columns: " . implode(', ', array_column($sc, 'Field')) . "\n";
        
    } catch (PDOException $e) {
        echo "ERROR: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
}

// PHP file checks
echo "==============================\n";
echo "PHP FILE CHECKS\n";
echo "==============================\n";

$checks = array(
    array('c:/xampp/htdocs/etail_development_src/admin/application/controllers/admin_pos.php', 'SOURCE admin_pos.php', array('orphanDetection','getUnsettledTransactions','runReconciliation','checkSessionLock')),
    array('c:/xampp/htdocs/erp.lakshmanaacharison.in/admin/application/controllers/admin_pos.php', 'CLIENT admin_pos.php', array('orphanDetection','getUnsettledTransactions','runReconciliation','checkSessionLock')),
    array('c:/xampp/htdocs/etail_development_src/admin/application/models/pos_model.php', 'SOURCE pos_model.php', array('findOrphanPayments','findOrphanBillingPayments','getStaleExpiredSessions','markTransactionsSettled','getUnsettledTransactions','createPaymentSession')),
    array('c:/xampp/htdocs/erp.lakshmanaacharison.in/admin/application/models/pos_model.php', 'CLIENT pos_model.php', array('findOrphanPayments','findOrphanBillingPayments','getStaleExpiredSessions','markTransactionsSettled','getUnsettledTransactions','createPaymentSession')),
);

foreach ($checks as $chk) {
    $path = $chk[0]; $label = $chk[1]; $fns = $chk[2];
    echo "\n$label:\n";
    if (file_exists($path)) {
        $content = file_get_contents($path);
        foreach ($fns as $fn) {
            echo (strpos($content, "function $fn") !== false ? "  OK" : "  MISS") . " $fn()\n";
        }
    } else {
        echo "  FILE NOT FOUND\n";
    }
}

echo "\n--- PROVIDER PLUGINS ---\n";
$envs = array('etail_development_src' => 'SOURCE', 'erp.lakshmanaacharison.in' => 'CLIENT');
$plugins = array('POS_Pinelabs.php','POS_Phonepe_DQR.php','POS_Phonepe_IEDC.php');
foreach ($envs as $dir => $lbl) {
    foreach ($plugins as $p) {
        $pp = "c:/xampp/htdocs/$dir/admin/application/libraries/pos_providers/$p";
        if (file_exists($pp)) {
            $c = file_get_contents($pp);
            $has = (strpos($c,'id_device') !== false && strpos($c,'provider_code') !== false && strpos($c,'payment_mode') !== false);
            echo ($has ? "  OK" : "  MISS") . " $lbl $p\n";
        } else {
            echo "  N/A  $lbl $p\n";
        }
    }
}

echo "\nDONE.\n";
