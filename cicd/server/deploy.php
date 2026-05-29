<?php
/**
 * Source Version - Unified Webhook Handler
 * Routes deployments by branch to correct environment
 * Includes notifications via GitHub Actions dispatch
 * 
 * Branch → Environment Mapping:
 *   Retail_1.1.1.0001 → dev (Develop)
 *   QA                → qa (QA)
 *   support           → staging (Staging)
 *   PRODUCTION        → prod (Production)
 */

header('Content-Type: text/plain; charset=utf-8');

// =============================================================================
// ENVIRONMENT CONFIGURATION
// =============================================================================

$ENVIRONMENTS = [
    'Retail_1.1.1.0001' => [
        'folder' => 'dev',
        'name' => 'Develop',
        'maintenance' => false,
        'deploy_mode' => 'git_pull'
    ],
    'QA' => [
        'folder' => 'qa',
        'name' => 'QA',
        'maintenance' => false,
        'deploy_mode' => 'git_pull'
    ],
    'support' => [
        'folder' => 'staging',
        'name' => 'Staging',
        'maintenance' => false,
        'deploy_mode' => 'git_pull'
    ],
    'PRODUCTION' => [
        'folder' => 'prod',
        'name' => 'Production',
        'maintenance' => true,
        'deploy_mode' => 'symlink'  // Production uses zero-downtime
    ]
];

// =============================================================================
// CONFIGURATION (from environment variables)
// =============================================================================

$CONFIG = [
    'secret' => getenv('WEBHOOK_SECRET') ?: $_SERVER['WEBHOOK_SECRET'] ?? '',
    'base_path' => getenv('BASE_PATH') ?: '/var/www/retail',
    'log_file' => getenv('LOG_FILE') ?: '/var/www/retail/prod/webhooks/webhook.log',
    'repo_url' => 'git@github.com:Logimax-Technologies/etail_development_src.git',
    'github_token' => ''
];


// Load GitHub token from file (for notifications)
$token_file = getenv('GITHUB_TOKEN_FILE') ?: '/var/www/retail/prod/webhooks/.github_token';
if (file_exists($token_file)) {
    $CONFIG['github_token'] = trim(file_get_contents($token_file));
}

// Validate secret
if (empty($CONFIG['secret'])) {
    http_response_code(500);
    die('WEBHOOK_SECRET environment variable is not set');
}

// =============================================================================
// LOGGING
// =============================================================================

function log_event($level, $message) {
    global $CONFIG;
    $entry = "[" . date('Y-m-d H:i:s') . "] [$level] $message\n";
    if (!empty($CONFIG['log_file'])) {
        file_put_contents($CONFIG['log_file'], $entry, FILE_APPEND | LOCK_EX);
    }
}

log_event('INFO', '=== Webhook received ===');
log_event('INFO', 'Method: ' . ($_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN'));

// =============================================================================
// REQUEST VALIDATION
// =============================================================================

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    log_event('ERROR', 'Method not allowed');
    die('Method not allowed');
}

$input = file_get_contents('php://input');
if (empty($input)) {
    http_response_code(400);
    log_event('ERROR', 'Empty payload');
    die('Empty payload');
}

log_event('INFO', 'Payload size: ' . strlen($input) . ' bytes');

// =============================================================================
// SIGNATURE VERIFICATION
// =============================================================================

$signature = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';
if (empty($signature)) {
    http_response_code(403);
    log_event('ERROR', 'Missing signature');
    die('Missing signature');
}

$expected = 'sha256=' . hash_hmac('sha256', $input, $CONFIG['secret']);
if (!hash_equals($expected, $signature)) {
    http_response_code(403);
    log_event('ERROR', 'Invalid signature');
    die('Invalid signature');
}

log_event('INFO', 'Signature verified ✓');

// =============================================================================
// PARSE PAYLOAD
// =============================================================================

$data = json_decode($input, true);
if (!$data) {
    http_response_code(400);
    log_event('ERROR', 'Invalid JSON');
    die('Invalid JSON');
}

// Handle ping event
$event = $_SERVER['HTTP_X_GITHUB_EVENT'] ?? 'push';
if ($event === 'ping') {
    log_event('INFO', 'Ping received - OK');
    echo 'pong';
    exit;
}

// =============================================================================
// DETERMINE BRANCH & ENVIRONMENT
// =============================================================================

$ref = $data['ref'] ?? '';
$branch = str_replace('refs/heads/', '', $ref);

log_event('INFO', "Branch: $branch");

if (!isset($ENVIRONMENTS[$branch])) {
    log_event('WARNING', "Branch '$branch' not configured - ignoring");
    echo "Branch $branch not configured for deployment";
    exit;
}

$env = $ENVIRONMENTS[$branch];
$folder = $env['folder'];
$env_name = $env['name'];
$maintenance = $env['maintenance'];
$deploy_mode = $env['deploy_mode'];

$env_path = $CONFIG['base_path'] . '/' . $folder;
$deploy_script = $env_path . '/deploy.sh';
$unified_script = $env_path . '/unified-deploy.sh';
// Production uses deploy-production.sh in shared/ (handles dual-target: prod + sales)
$production_script = $env_path . '/shared/deploy-production.sh';

log_event('INFO', "Environment: $env_name ($folder)");
log_event('INFO', "Deploy mode: $deploy_mode");

// =============================================================================
// EXTRACT COMMIT INFO
// =============================================================================

$commit_id = $data['head_commit']['id'] ?? substr(uniqid(), 0, 7);
$commit_msg = $data['head_commit']['message'] ?? 'No message';
$author = $data['head_commit']['author']['name'] ?? $data['pusher']['name'] ?? 'Unknown';
$repo_name = $data['repository']['full_name'] ?? 'Logimax-Technologies/etail_development_src';


// Clean commit message for shell
$commit_msg_clean = str_replace(["\r\n", "\r", "\n", '"', "'"], [' ', ' ', ' ', '', ''], $commit_msg);

log_event('INFO', "Commit: $commit_id by $author");
log_event('INFO', "Message: " . substr($commit_msg_clean, 0, 100));

// =============================================================================
// EXECUTE DEPLOYMENT
// =============================================================================

$output = '';
$success = false;

// DevOps env vars for deploy scripts
$devops_api_url = getenv('DEVOPS_API_URL') ?: '';
$devops_secret  = getenv('DEVOPS_WEBHOOK_SECRET') ?: (getenv('WEBHOOK_SECRET') ?: '');
$devops_env_vars = '';
if (!empty($devops_api_url)) {
    $devops_env_vars = sprintf(
        'DEVOPS_API_URL=%s DEVOPS_WEBHOOK_SECRET=%s',
        escapeshellarg($devops_api_url),
        escapeshellarg($devops_secret)
    );
}

if ($deploy_mode === 'symlink' && file_exists($production_script)) {
    // Production: deploy-production.sh (dual-target: prod + sales)
    $command = sprintf(
        '%s DEPLOY_BRANCH=%s nohup bash %s > /tmp/%s-deploy.log 2>&1 & echo $!',
        $devops_env_vars,
        escapeshellarg($branch),
        escapeshellarg($production_script),
        $folder
    );
    $pid = trim(shell_exec($command));
    log_event('INFO', "Production deploy started via deploy-production.sh (PID: $pid)");
    $output = "Symlink deployment started with PID: $pid";
    $success = true;

} elseif ($deploy_mode === 'symlink' && file_exists($unified_script)) {
    // Legacy: unified-deploy.sh
    $maintenance_flag = $maintenance ? '--maintenance' : '';
    $command = sprintf(
        'cd %s && nohup bash %s --branch %s %s > /tmp/%s-deploy.log 2>&1 & echo $!',
        escapeshellarg($env_path),
        escapeshellarg($unified_script),
        escapeshellarg($branch),
        $maintenance_flag,
        $folder
    );
    $pid = trim(shell_exec($command));
    log_event('INFO', "Symlink deploy started (PID: $pid)");
    $output = "Symlink deployment started with PID: $pid";
    $success = true;
    
} elseif (file_exists($deploy_script)) {
    // Use existing deploy.sh (git pull with logging)
    $command = sprintf(
        'cd %s && bash %s %s 2>&1',
        escapeshellarg($env_path),
        escapeshellarg($deploy_script),
        escapeshellarg($branch)
    );
    $output = shell_exec($command);
    log_event('INFO', "Deploy.sh output: " . substr($output, 0, 500));
    $success = strpos($output, 'Deployment complete') !== false || strpos($output, '✅') !== false;
    
} else {
    // Fallback: simple git pull
    log_event('INFO', 'Using fallback git pull');
    $command = sprintf(
        'cd %s && GIT_SSH_COMMAND="ssh -i ~/.ssh/id_rsa" git fetch --all && git checkout %s && git reset --hard origin/%s 2>&1',
        escapeshellarg($env_path),
        escapeshellarg($branch),
        escapeshellarg($branch)
    );
    $output = shell_exec($command);
    log_event('INFO', "Git pull output: " . substr($output, 0, 500));
    $success = strpos($output, 'HEAD is now at') !== false;
}

// =============================================================================
// SEND NOTIFICATION (via GitHub Actions dispatch)
// =============================================================================

if (!empty($CONFIG['github_token'])) {
    $notification_data = [
        'event_type' => 'staging-deployed',
        'client_payload' => [
            'branch' => $branch,

            'commit' => $commit_id,
            'message' => $commit_msg_clean,
            'user' => $author,
            'server' => $env_name,
            'status' => $success ? 'SUCCESS' : 'FAILED',
            'timestamp' => date('Y-m-d H:i:s'),
            'repository' => $repo_name
        ]
    ];
    
    $ch = curl_init("https://api.github.com/repos/$repo_name/dispatches");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($notification_data),
        CURLOPT_HTTPHEADER => [
            'Accept: application/vnd.github.v3+json',
            'Authorization: Bearer ' . $CONFIG['github_token'],
            'User-Agent: Logimax-Deploy-Webhook',
            'Content-Type: application/json'
        ]
    ]);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($http_code === 204) {
        log_event('INFO', 'Notification sent ✓');
    } else {
        log_event('WARNING', "Notification failed (HTTP $http_code)");
    }
}

// =============================================================================
// LOG TO DEVOPS TOOL (Node.js Dashboard)
// =============================================================================

$devops_api_url = getenv('DEVOPS_API_URL') ?: '';
$devops_secret  = getenv('DEVOPS_WEBHOOK_SECRET') ?: (getenv('WEBHOOK_SECRET') ?: '');

if (!empty($devops_api_url)) {
    $devops_data = json_encode([
        'client_id'      => 'source',
        'client_name'    => $repo_name,
        'environment'    => strtolower($env_name),
        'status'         => $success ? 'success' : 'failed',
        'branch'         => $branch,
        'commit_sha'     => $commit_id,
        'commit_message' => $commit_msg_clean,
        'triggered_by'   => $author,
        'trigger_type'   => 'webhook',
        'deploy_type'    => 'source'
    ]);

    $ch = curl_init($devops_api_url . '/api/webhooks/deploy-log');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $devops_data,
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json',
            'X-Webhook-Secret: ' . $devops_secret
        ],
        CURLOPT_TIMEOUT => 10,
        CURLOPT_CONNECTTIMEOUT => 5
    ]);
    $devops_response = curl_exec($ch);
    $devops_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $devops_error = curl_error($ch);
    curl_close($ch);

    if ($devops_code >= 200 && $devops_code < 300) {
        log_event('INFO', 'Logged to DevOps Tool ✓');
    } else {
        log_event('WARNING', "DevOps Tool log failed (HTTP $devops_code): $devops_error");
        log_event('DEBUG', "DevOps response: $devops_response");
    }
} else {
    log_event('WARNING', 'DEVOPS_API_URL not configured — skipping DevOps Tool logging');
}

// =============================================================================
// RESPONSE
// =============================================================================

$status = $success ? 'success' : 'failed';


if (!$success) {
    http_response_code(500);
}

$response = [
    'status' => $status,
    'environment' => $env_name,
    'branch' => $branch,
    'commit' => $commit_id,
    'deploy_mode' => $deploy_mode,
    'message' => "Deployment $status for $env_name"
];

log_event($success ? 'INFO' : 'ERROR', "Deployment $status for $env_name");
log_event('INFO', '=== Webhook complete ===');

echo json_encode($response, JSON_PRETTY_PRINT);