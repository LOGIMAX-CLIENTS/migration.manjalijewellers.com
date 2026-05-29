<?php
/**
 * GitHub Webhook Handler for Auto-Deployment
 * 
 * @deprecated Use deploy.php instead. This file is kept for backward compatibility.
 * 
 * deploy.php provides:
 * - Multi-environment routing by branch
 * - Both staging (git pull) and production (symlink) modes
 * - GitHub Actions notification dispatch
 * - Proper error handling and HTTP status codes
 * 
 * MIGRATION: Update your webhook URLs to point to deploy.php
 * 
 * REQUIRED ENVIRONMENT VARIABLES:
 * - WEBHOOK_SECRET: HMAC secret for signature verification
 * - REPO_PATH: Path to repository
 * - DEPLOY_SCRIPT: Path to deploy script
 */

// Log deprecation warning
error_log('[DEPRECATED] webhook-handler.php is deprecated. Migrate to deploy.php');


header('Content-Type: application/json; charset=utf-8');

// =============================================================================
// CONFIGURATION
// =============================================================================
$CONFIG = [
    'secret' => getenv('WEBHOOK_SECRET') ?: '',
    'repo_path' => getenv('REPO_PATH') ?: '',
    'deploy_script' => getenv('DEPLOY_SCRIPT') ?: '',
    'log_file' => getenv('LOG_FILE') ?: '/tmp/webhook.log',
    'allowed_ips' => parse_allowed_ips(getenv('ALLOWED_IPS') ?: ''),
    'github_token' => getenv('GITHUB_TOKEN') ?: ''
];

// Validate required config
if (empty($CONFIG['secret'])) {
    http_response_code(500);
    die(json_encode(['error' => 'WEBHOOK_SECRET not configured']));
}

// =============================================================================
// HELPER FUNCTIONS
// =============================================================================
function parse_allowed_ips($ips_string) {
    if (empty($ips_string)) {
        return [
            '192.30.252.0/22',   // GitHub
            '185.199.108.0/22',  // GitHub
            '140.82.112.0/20',   // GitHub
            '143.55.64.0/20',    // GitHub
            '127.0.0.1/32',      // Localhost
        ];
    }
    return array_map('trim', explode(',', $ips_string));
}

function log_event($level, $message) {
    global $CONFIG;
    $timestamp = date('Y-m-d H:i:s');
    $entry = "[$timestamp] [$level] $message\n";
    if (!empty($CONFIG['log_file'])) {
        file_put_contents($CONFIG['log_file'], $entry, FILE_APPEND | LOCK_EX);
    }
}

function ip_in_range($ip, $range) {
    if (strpos($range, '/') === false) {
        return $ip === $range;
    }
    list($subnet, $bits) = explode('/', $range);
    $ip = ip2long($ip);
    $subnet = ip2long($subnet);
    $mask = -1 << (32 - $bits);
    return ($ip & $mask) == ($subnet & $mask);
}

function is_ip_allowed($ip, $ranges) {
    foreach ($ranges as $range) {
        if (ip_in_range($ip, $range)) return true;
    }
    return false;
}

// =============================================================================
// REQUEST VALIDATION
// =============================================================================
log_event('INFO', '=== Webhook Request ===');
log_event('INFO', 'Method: ' . ($_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN'));

// Only POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die(json_encode(['error' => 'Method not allowed']));
}

// Get payload
$input = file_get_contents('php://input');
if (empty($input)) {
    http_response_code(400);
    die(json_encode(['error' => 'Empty payload']));
}

// Verify signature
$hub_signature = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';
if (empty($hub_signature)) {
    http_response_code(403);
    log_event('ERROR', 'Missing signature');
    die(json_encode(['error' => 'Missing signature']));
}

$calculated = 'sha256=' . hash_hmac('sha256', $input, $CONFIG['secret']);
if (!hash_equals($calculated, $hub_signature)) {
    http_response_code(403);
    log_event('ERROR', 'Invalid signature');
    die(json_encode(['error' => 'Invalid signature']));
}

// Verify IP
if (!empty($CONFIG['allowed_ips'])) {
    $client_ip = $_SERVER['REMOTE_ADDR'];
    if (!is_ip_allowed($client_ip, $CONFIG['allowed_ips'])) {
        http_response_code(403);
        log_event('ERROR', "Blocked IP: $client_ip");
        die(json_encode(['error' => 'IP not allowed']));
    }
}

log_event('INFO', 'Signature verified');

// =============================================================================
// PROCESS PAYLOAD
// =============================================================================
$data = json_decode($input, true);
if (!$data) {
    http_response_code(400);
    die(json_encode(['error' => 'Invalid JSON']));
}

$event = $_SERVER['HTTP_X_GITHUB_EVENT'] ?? 'unknown';
log_event('INFO', "Event: $event");

// Handle ping
if ($event === 'ping') {
    echo json_encode(['status' => 'pong']);
    exit;
}

// Extract deployment config
$environment = $data['environment'] ?? 'staging';
$deploy_mode = $data['deploy_mode'] ?? 'simple';
$maintenance = $data['maintenance_mode'] ?? false;
$branch = basename($data['ref'] ?? 'main');
$commit = $data['head_commit']['id'] ?? 'unknown';
$author = $data['head_commit']['author'] ?? $data['triggered_by'] ?? 'unknown';
$client = $data['client_name'] ?? 'Unknown Client';
$run_id = $data['run_id'] ?? '';

log_event('INFO', "Environment: $environment, Mode: $deploy_mode, Branch: $branch");

// =============================================================================
// TRIGGER DEPLOYMENT
// =============================================================================
$env_vars = [
    "REPO_PATH={$CONFIG['repo_path']}",
    "DEPLOY_BRANCH=$branch",
    "DEPLOY_MODE=$deploy_mode",
    "MAINTENANCE_MODE=" . ($maintenance ? 'true' : 'false'),
    "ENVIRONMENT=$environment"
];

$env_string = implode(' ', $env_vars);
$script = escapeshellarg($CONFIG['deploy_script']);

$start_time = microtime(true);

// Execute deployment
$command = "$env_string bash $script 2>&1";
log_event('INFO', "Executing: $command");

$output = shell_exec($command);
$duration = round(microtime(true) - $start_time, 2);

log_event('INFO', "Duration: {$duration}s");
log_event('INFO', "Output: $output");

// =============================================================================
// SEND NOTIFICATION
// =============================================================================
$status = (strpos($output, '"status":"success"') !== false) ? 'success' : 'failed';

$report = [
    'status' => $status,
    'duration' => "{$duration}s",
    'environment' => $environment,
    'branch' => $branch,
    'commit' => substr($commit, 0, 7),
    'client' => $client,
    'author' => $author,
    'timestamp' => date('Y-m-d H:i:s T')
];

// Trigger notification workflow if token available
if (!empty($CONFIG['github_token']) && !empty($data['repository']['full_name'])) {
    $repo = $data['repository']['full_name'];
    $payload = [
        'event_type' => 'deployment-completed',
        'client_payload' => $report
    ];
    
    $ch = curl_init("https://api.github.com/repos/$repo/dispatches");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_HTTPHEADER => [
            'Accept: application/vnd.github.v3+json',
            'Authorization: Bearer ' . $CONFIG['github_token'],
            'User-Agent: Webhook-Deploy',
            'Content-Type: application/json'
        ]
    ]);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    log_event('INFO', "Notification sent (HTTP $http_code)");
}

// =============================================================================
// RESPONSE
// =============================================================================
echo json_encode($report, JSON_PRETTY_PRINT);
