<?php
/**
 * PhonePe POS — Postman Test Script
 * Use this from Postman to test the DQR flow without browser login.
 * 
 * HOW TO USE:
 * 1. INIT:   POST http://localhost/etail_development_src/admin/phonepe_test.php?action=init&amount=500
 * 2. STATUS: POST http://localhost/etail_development_src/admin/phonepe_test.php?action=status&refid=TX...
 * 3. CANCEL: POST http://localhost/etail_development_src/admin/phonepe_test.php?action=cancel&refid=TX...
 * 
 * ⚠️ DELETE THIS FILE BEFORE GOING LIVE — it has no authentication!
 */

header('Content-Type: application/json');

// Bootstrap CodeIgniter
$_SERVER['CI_ENV'] = 'development';
define('BASEPATH', __DIR__ . '/system/');
define('APPPATH', __DIR__ . '/application/');
define('ENVIRONMENT', 'development');

// Direct database connection
$dbConfig = parse_ini_file(__DIR__ . '/application/config/database.php') ?: null;

// Simpler: use direct MySQL
$host = 'localhost';
$user = 'root';
$pass = '1234';

// Read database name from CI config
$dbFile = file_get_contents(__DIR__ . '/application/config/database.php');
preg_match("/\['database'\]\s*=\s*['\"](.+?)['\"]/", $dbFile, $matches);
$dbname = isset($matches[1]) ? $matches[1] : '';

if(empty($dbname)){
    echo json_encode(array('error' => 'Cannot detect database name from config'));
    exit;
}

$conn = new mysqli($host, $user, $pass, $dbname);
if($conn->connect_error){
    echo json_encode(array('error' => 'DB connection failed: ' . $conn->connect_error));
    exit;
}

$action = isset($_GET['action']) ? $_GET['action'] : '';

// ========== INIT ==========
if($action == 'init'){
    $amount   = isset($_GET['amount']) ? intval($_GET['amount']) : 100;
    $cusid    = isset($_GET['cusid']) ? $_GET['cusid'] : '1001';
    $transno  = 'TX' . $cusid . '_' . time() . '_' . random_int(1000, 9999);
    
    // Get PhonePe DQR device
    $res = $conn->query("SELECT d.*, p.provider_code, p.provider_name 
                         FROM ret_pos_device_list d 
                         LEFT JOIN ret_pos_providers p ON d.id_provider = p.id_provider 
                         WHERE p.provider_code = 'phonepe_dqr' 
                         AND (d.is_active = 1 OR d.is_active IS NULL) 
                         LIMIT 1");
    $device = $res->fetch_assoc();
    
    if(!$device){
        echo json_encode(array('error' => 'No PhonePe DQR device found. Add one in POS Settings first.'));
        exit;
    }
    
    // MOCK MODE (no salt_key)
    if(empty($device['salt_key'])){
        $mockQR = 'upi://pay?pa=test.merchant@phonepe&pn=TestMerchant&am=' . $amount . '&tr=' . $transno . '&cu=INR&mc=1234';
        
        $stmt = $conn->prepare("INSERT INTO ret_pos_requests 
            (pos_trans_no, pos_store_pos_code, pos_req_amount, pos_mer_id, pos_req_bill_cusid, id_provider, pos_res_ref_id, pos_qr_string, pos_res_trans_data) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $amountPaise = $amount * 100;
        $storeId = !empty($device['store_id']) ? $device['store_id'] : 'MOCK';
        $merId = !empty($device['merchantid']) ? $device['merchantid'] : 'MOCK';
        $providerId = $device['id_provider'];
        $mockData = json_encode(array('mock' => true, 'created_at' => time()));
        $stmt->bind_param('ssississs', $transno, $storeId, $amountPaise, $merId, $cusid, $providerId, $transno, $mockQR, $mockData);
        $stmt->execute();
        
        echo json_encode(array(
            'status'       => 'success',
            'mode'         => 'MOCK (no salt_key)',
            'responsecode' => 0,
            'refid'        => $transno,
            'qrdata'       => $mockQR,
            'amount'       => $amount,
            'message'      => 'QR generated. Call status endpoint after 15s to see COMPLETED.'
        ), JSON_PRETTY_PRINT);
        exit;
    }
    
    // REAL MODE — would call PhonePe API here
    echo json_encode(array('error' => 'Real mode not available in test script. Use browser for real API calls.'));
    exit;
}

// ========== STATUS ==========
if($action == 'status'){
    $refid = isset($_GET['refid']) ? $_GET['refid'] : '';
    if(empty($refid)){
        echo json_encode(array('error' => 'refid is required'));
        exit;
    }
    
    $stmt = $conn->prepare("SELECT * FROM ret_pos_requests WHERE pos_res_ref_id = ?");
    $stmt->bind_param('s', $refid);
    $stmt->execute();
    $result = $stmt->get_result();
    $txn = $result->fetch_assoc();
    
    if(!$txn){
        echo json_encode(array('error' => 'Transaction not found: ' . $refid));
        exit;
    }
    
    $mockData = json_decode($txn['pos_res_trans_data'], true);
    $elapsed = 999;
    if(isset($mockData['mock']) && isset($mockData['created_at'])){
        $elapsed = time() - $mockData['created_at'];
    }
    
    if($elapsed < 15){
        echo json_encode(array(
            'status'       => 'PENDING',
            'responsecode' => 2,
            'elapsed'      => $elapsed . 's (completes at 15s)',
            'message'      => 'Customer has not scanned yet. Poll again.'
        ), JSON_PRETTY_PRINT);
    } else {
        // Auto-complete
        $mockUtr = 'MOCK' . date('YmdHis') . random_int(1000,9999);
        $conn->query("UPDATE ret_pos_requests SET pos_req_status = 1, pos_utr = '$mockUtr' WHERE pos_res_ref_id = '$refid'");
        
        echo json_encode(array(
            'status'       => 'COMPLETED',
            'responsecode' => 0,
            'utr'          => $mockUtr,
            'amount'       => $txn['pos_req_amount'] / 100,
            'message'      => 'Payment successful!'
        ), JSON_PRETTY_PRINT);
    }
    exit;
}

// ========== CANCEL ==========
if($action == 'cancel'){
    $refid = isset($_GET['refid']) ? $_GET['refid'] : '';
    if(empty($refid)){
        echo json_encode(array('error' => 'refid is required'));
        exit;
    }
    $conn->query("UPDATE ret_pos_requests SET pos_req_status = 2 WHERE pos_res_ref_id = '$refid'");
    echo json_encode(array(
        'status'  => 'CANCELLED',
        'message' => 'Payment cancelled successfully'
    ), JSON_PRETTY_PRINT);
    exit;
}

// Default — show help
echo json_encode(array(
    'help' => 'PhonePe POS Test Endpoint',
    'actions' => array(
        'init'   => '?action=init&amount=500',
        'status' => '?action=status&refid=TX...',
        'cancel' => '?action=cancel&refid=TX...'
    )
), JSON_PRETTY_PRINT);

$conn->close();
