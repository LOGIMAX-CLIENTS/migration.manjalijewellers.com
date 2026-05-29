<?php
/**
 * GITEA Webhook Handler for Retail Deployment
 * Dedicated file for Gitea Actions integration
 */

header('Content-Type: text/plain; charset=utf-8');

// ================= CONFIGURATION =================
$CONFIG = [
    // ⚠️ GENERATE A NEW SECRET FOR GITEA (different from GitHub!)
    // Run: openssl rand -hex 32
    'secret' => 'fa7c7fa5d2ec3dee44b56ad34b165274be60e5d24744f51adbad11ab94b8c317',
    
    'branch' => 'Retail_1.1.1.0001',
    'repo_path' => '/var/www/retail/dev/',
    'deploy_script' => '/var/www/retail/dev/deploy.sh',
    'log_file' => '/var/www/retail/prod/webhooks/webhook-gitea.log',
    
    // GITEA-SPECIFIC IP WHITELIST
    'allowed_ips' => [
        // Your Gitea server IP (REQUIRED)
        '13.126.132.86',
        
        // Your internal network
        '192.168.1.0/24',
        
        // For testing - REMOVE IN PRODUCTION
        // '0.0.0.0/0'
    ],
    
    // Gitea-specific settings
    'timeout' => 30, // seconds
    'max_payload_size' => 10485760, // 10MB
];

// ================= SIMPLE LOGGING =================
function gitea_log($message, $level = 'INFO') {
    global $CONFIG;
    $timestamp = date('Y-m-d H:i:s');
    $log = "[$timestamp] [$level] $message\n";
    
    // Log to file
    file_put_contents($CONFIG['log_file'], $log, FILE_APPEND);
    
    // Also echo for immediate feedback in webhook response
    if ($level === 'ERROR' || $level === 'INFO') {
        echo "[$level] $message\n";
    }
}

// Start
gitea_log('=== GITEA WEBHOOK STARTED ===');

// ================= BASIC VALIDATION =================

// 1. Only POST allowed
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    gitea_log('Method not allowed (only POST accepted)', 'ERROR');
    exit;
}

// 2. Check content type
if (!isset($_SERVER['CONTENT_TYPE']) || $_SERVER['CONTENT_TYPE'] !== 'application/json') {
    http_response_code(415);
    gitea_log('Content-Type must be application/json', 'ERROR');
    exit;
}

// 3. Get payload
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!$data) {
    http_response_code(400);
    gitea_log('Invalid JSON payload', 'ERROR');
    exit;
}

// ================= SECURITY: IP CHECK =================
$client_ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
gitea_log("Request from IP: $client_ip");

$ip_allowed = false;
foreach ($CONFIG['allowed_ips'] as $range) {
    if ($range === '0.0.0.0/0') {
        $ip_allowed = true; // Warning: Allows ALL IPs!
        break;
    }
    
    if (strpos($range, '/') !== false) {
        // CIDR check
        list($subnet, $bits) = explode('/', $range);
        $ip = ip2long($client_ip);
        $subnet = ip2long($subnet);
        $mask = -1 << (32 - $bits);
        
        if (($ip & $mask) == ($subnet & $mask)) {
            $ip_allowed = true;
            break;
        }
    } elseif ($client_ip === $range) {
        $ip_allowed = true;
        break;
    }
}

if (!$ip_allowed) {
    http_response_code(403);
    gitea_log("IP not allowed: $client_ip", 'ERROR');
    exit;
}

// ================= SECURITY: SIGNATURE VERIFICATION =================
$signature = $_SERVER['HTTP_X_GITEA_SIGNATURE'] ?? '';
if (empty($signature)) {
    // Try GitHub header for compatibility
    $signature = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';
}

if (empty($signature)) {
    http_response_code(403);
    gitea_log('Missing X-Gitea-Signature header', 'ERROR');
    exit;
}

// Calculate expected signature
$expected_sig = 'sha256=' . hash_hmac('sha256', $json, $CONFIG['secret']);

if (!hash_equals($expected_sig, $signature)) {
    http_response_code(403);
    gitea_log('Invalid signature', 'ERROR');
    gitea_log("Expected: sha256=..." . substr(hash_hmac('sha256', $json, $CONFIG['secret']), -10), 'DEBUG');
    gitea_log("Received: $signature", 'DEBUG');
    exit;
}

gitea_log('✓ Signature verified');

// ================= PROCESS GITEA PUSH EVENT =================

// Check event type
$event = $_SERVER['HTTP_X_GITEA_EVENT'] ?? 'push';
if ($event !== 'push') {
    gitea_log("Ignoring non-push event: $event");
    echo "✅ Webhook received (non-push event ignored)";
    exit;
}

// Get branch
$branch = isset($data['ref']) ? str_replace('refs/heads/', '', $data['ref']) : '';
if ($branch !== $CONFIG['branch']) {
    gitea_log("Ignoring branch: $branch (expected: {$CONFIG['branch']})");
    echo "✅ Ignored push to branch: $branch";
    exit;
}

// Get commit details
$commit_id = $data['head_commit']['id'] ?? 'unknown';
$commit_msg = $data['head_commit']['message'] ?? 'No message';
$commit_author = $data['head_commit']['author']['name'] ?? 'Unknown';
$repo = $data['repository']['full_name'] ?? 'unknown';

gitea_log("Processing push to $branch by $commit_author");
gitea_log("Commit: $commit_id - $commit_msg");

// ================= EXECUTE DEPLOYMENT =================

// Verify paths exist
if (!file_exists($CONFIG['repo_path'])) {
    gitea_log("Repo path not found: {$CONFIG['repo_path']}", 'ERROR');
    http_response_code(500);
    exit;
}

if (!file_exists($CONFIG['deploy_script'])) {
    gitea_log("Deploy script not found: {$CONFIG['deploy_script']}", 'ERROR');
    http_response_code(500);
    exit;
}

// Change to repo directory
chdir($CONFIG['repo_path']);

// Execute deploy.sh in background
$command = "cd " . escapeshellarg($CONFIG['repo_path']) . 
           " && nohup bash " . escapeshellarg($CONFIG['deploy_script']) . 
           " " . escapeshellarg($branch) . 
           " > /tmp/deploy_gitea_" . time() . ".log 2>&1 & echo $!";
           
gitea_log("Executing: $command");

$pid = shell_exec($command);
$pid = trim($pid);

if (empty($pid) || !is_numeric($pid)) {
    gitea_log("Failed to start deployment process", 'ERROR');
    http_response_code(500);
    echo "❌ Deployment failed to start";
    exit;
}

gitea_log("✓ Deployment started with PID: $pid");

// ================= SUCCESS RESPONSE =================
echo "✅ DEPLOYMENT TRIGGERED SUCCESSFULLY\n";
echo "=================================\n";
echo "Branch:    $branch\n";
echo "Commit:    " . substr($commit_id, 0, 8) . "...\n";
echo "Author:    $commit_author\n";
echo "Process:   PID $pid\n";
echo "Logs:      /tmp/deploy_gitea_*.log\n";
echo "\nDeployment is running in background.\n";
echo "Check server logs for completion status.\n";

gitea_log('=== Webhook completed successfully ===');
?>