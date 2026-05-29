<?php
/**
 * Source Version - Unified Webhook Handler
 * Routes deployments by branch to correct environment
 * Includes notifications via GitHub Actions dispatch
 * 
 * Branch → Environment Mapping:
 *   develop  → test_etail_v3 (Develop)
 *   qa       → QA
 *   support  → Support
 *   main     → etail (Production)
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
        'deploy_mode' => 'dual_symlink',  // Zero-downtime: deploys to prod + sales
        'targets' => ['prod', 'sales']
    ]
];

// =============================================================================
// CONFIGURATION (from environment variables)
// =============================================================================

$CONFIG = [
    'secret' => getenv('WEBHOOK_SECRET') ?: $_SERVER['WEBHOOK_SECRET'] ?? '',
    'base_path' => getenv('BASE_PATH') ?: '/var/www/retail',
    'log_file' => getenv('LOG_FILE') ?: '/var/www/retail/prod/shared/logs/webhook.log',
    'repo_url' => 'git@github.com:Logimax-Technologies/etail_development_src.git',
    'github_token' => ''
];

// Load GitHub token from file (check multiple locations)
$token_paths = [
    '/var/www/retail/prod/webhooks/.github_token',
    '/home/ubuntu/.github_token'
];
foreach ($token_paths as $token_file) {
    if (file_exists($token_file)) {
        $CONFIG['github_token'] = trim(file_get_contents($token_file));
        break;
    }
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
log_event("DEBUG", "Full ref: " . ($data["ref"] ?? "none") . " | base_ref: " . ($data["base_ref"] ?? "none"));
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
$production_script = $CONFIG['base_path'] . '/prod/current/cicd/server/deploy-production.sh';
$production_script_shared = $CONFIG['base_path'] . '/prod/shared/deploy-production.sh';

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

if ($deploy_mode === 'dual_symlink') {
    // Dual-target symlink deployment (prod + sales)
    // Find deploy-production.sh: check shared/ first, then current release
    $deploy_prod_script = '';
    if (file_exists($production_script_shared)) {
        $deploy_prod_script = $production_script_shared;
    } elseif (file_exists($production_script)) {
        $deploy_prod_script = $production_script;
    }

    if (!empty($deploy_prod_script)) {
        $command = sprintf(
            'export BASE_PATH=%s DEPLOY_BRANCH=%s'
            . ' && nohup bash %s > /tmp/deploy-production.log 2>&1 & echo $!',
            escapeshellarg($CONFIG['base_path']),
            escapeshellarg($branch),
            escapeshellarg($deploy_prod_script)
        );
        $pid = trim(shell_exec($command));
        log_event('INFO', "Dual symlink deploy started (PID: $pid) — targets: prod, sales");
        $output = "Dual symlink deployment started with PID: $pid";
        $success = true;
    } else {
        log_event('ERROR', 'deploy-production.sh not found in shared/ or current/cicd/server/');
        $output = 'deploy-production.sh not found';
        $success = false;
    }

} elseif ($deploy_mode === 'symlink' && file_exists($unified_script)) {
    // Single-target symlink deployment (legacy — for mono-clients)
    $maintenance_flag = $maintenance ? 'true' : 'false';
    $devopsApiUrl = $devops_api_url ?? (getenv('DEVOPS_API_URL') ?: '');
    $devopsWebhookSecret = $devops_secret ?? (getenv('DEVOPS_WEBHOOK_SECRET') ?: (getenv('WEBHOOK_SECRET') ?: ''));
    $sharedDir = $CONFIG['base_path'] . '/shared';

    $command = sprintf(
        'cd %s && export REPO_PATH=%s DEPLOY_BRANCH=%s DEPLOY_MODE=symlink ENVIRONMENT=%s'
        . ' MAINTENANCE_MODE=%s SHARED_DIR=%s DEPLOY_LOG=/tmp/%s-deploy.log'
        . ' DEVOPS_API_URL=%s DEVOPS_WEBHOOK_SECRET=%s'
        . ' && nohup bash %s > /tmp/%s-deploy.log 2>&1 & echo $!',
        escapeshellarg($env_path),
        escapeshellarg($env_path),
        escapeshellarg($branch),
        escapeshellarg(strtolower($env_name)),
        escapeshellarg($maintenance_flag),
        escapeshellarg($sharedDir),
        $folder,
        escapeshellarg($devopsApiUrl),
        escapeshellarg($devopsWebhookSecret),
        escapeshellarg($unified_script),
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
    log_event('INFO', "Deploy.sh output: " . substr($output, 0, 2000));
    
    // Check for explicit failure indicators first
    $has_error = strpos($output, '❌') !== false
             || strpos($output, 'fatal:') !== false
             || strpos($output, 'Permission denied') !== false
             || strpos($output, 'FAILED') !== false;
    
    $has_success = strpos($output, 'Deployment complete') !== false 
                || strpos($output, '✅') !== false 
                || strpos($output, 'HEAD is now at') !== false
                || strpos($output, 'Already up to date') !== false;
    
    $success = $has_success && !$has_error;
    
    if (!$success) {
        log_event('ERROR', 'Deploy.sh did not complete successfully. Full output: ' . substr($output, 0, 3000));
    }

    // Run Port_Max deploy ONLY if port_max/ files changed
    $portmax_script = $env_path . '/port_max/deploy.sh';
    if ($success && file_exists($portmax_script)) {
        // Check if any port_max files were modified in this push
        $portmax_changed = false;

        // Method 1: Check individual commits
        $commits = $data['commits'] ?? [];
        foreach ($commits as $commit) {
            $all_files = array_merge(
                $commit['added'] ?? [],
                $commit['modified'] ?? [],
                $commit['removed'] ?? []
            );
            foreach ($all_files as $file) {
                if (strpos($file, 'port_max/') === 0) {
                    $portmax_changed = true;
                    break 2;
                }
            }
        }

        // Method 2: Fallback — check head_commit (for merge PRs)
        if (!$portmax_changed && isset($data['head_commit'])) {
            $head_files = array_merge(
                $data['head_commit']['added'] ?? [],
                $data['head_commit']['modified'] ?? [],
                $data['head_commit']['removed'] ?? []
            );
            foreach ($head_files as $file) {
                if (strpos($file, 'port_max/') === 0) {
                    $portmax_changed = true;
                    break;
                }
            }
        }

        log_event('INFO', 'Port_Max detection: changed=' . ($portmax_changed ? 'YES' : 'NO') . ', commits=' . count($commits));

        if ($portmax_changed) {
            log_event('INFO', 'Port_Max files changed — running deploy script...');
            $portmax_cmd = sprintf(
                'cd %s && bash %s 2>&1',
                escapeshellarg($env_path),
                escapeshellarg($portmax_script)
            );
            $portmax_output = shell_exec($portmax_cmd);
            log_event('INFO', 'Port_Max deploy output: ' . substr($portmax_output, 0, 2000));

            if (strpos($portmax_output, 'Port_Max Deployment Complete') !== false) {
                log_event('INFO', 'Port_Max deploy completed ✓');
            } else {
                log_event('WARNING', 'Port_Max deploy may have issues — check port_max/deploy.log');
            }
            $output .= "\n[PORT_MAX] " . $portmax_output;
        } else {
            log_event('INFO', 'No port_max changes detected — skipping port_max deploy');
        }
    }

} else {
    // Fallback: simple git pull
    log_event('INFO', 'Using fallback git pull');
    $command = sprintf(
        'cd %s && GIT_SSH_COMMAND="ssh -i /var/www/.ssh/id_ed25519 -o StrictHostKeyChecking=no" git fetch --all && git checkout %s && git reset --hard origin/%s 2>&1',
        escapeshellarg($env_path),
        escapeshellarg($branch),
        escapeshellarg($branch)
    );
    $output = shell_exec($command);
    log_event('INFO', "Git pull output: " . substr($output, 0, 500));
    $success = strpos($output, 'HEAD is now at') !== false;
}

// =============================================================================
// SQL MIGRATIONS (run after successful deploy)
// =============================================================================

if ($success && $deploy_mode === 'git_pull') {
    $migrate_script = $env_path . '/scripts/run-migrations.sh';
    $migrate_dir    = $env_path . '/database/migrations';
    
    // Try environment-specific config first, then fall back to default
    $config_file = "/var/www/retail/prod/shared/config/global_configs_{$folder}.php";
    if (!file_exists($config_file)) {
        $config_file = '/var/www/retail/prod/shared/config/global_configs.php';
    }
    
    if (file_exists($migrate_script) && is_dir($migrate_dir)) {
        // Check if there are any pending .sql files
        $sql_files = glob($migrate_dir . '/*.sql');
        if (!empty($sql_files)) {
            log_event('INFO', 'Running SQL migrations (' . count($sql_files) . ' files found)...');
            $migrate_cmd = sprintf(
                'bash %s --config %s --migrations %s --env %s --json 2>&1',
                escapeshellarg($migrate_script),
                escapeshellarg($config_file),
                escapeshellarg($migrate_dir),
                escapeshellarg($folder)
            );
            $migrate_output = shell_exec($migrate_cmd);
            $migrate_exit = 0; // shell_exec doesn't return exit code, check output
            
            if (strpos($migrate_output, '"status":"error"') !== false 
                || strpos($migrate_output, 'FAILED') !== false) {
                log_event('ERROR', 'SQL migration FAILED: ' . substr($migrate_output, 0, 2000));
                $output .= "\n[MIGRATION FAILED] " . $migrate_output;
                // Don't flip $success to false — code deployed OK, just migrations had issues
            } else {
                log_event('INFO', 'SQL migrations completed: ' . substr($migrate_output, 0, 500));
                $output .= "\n[MIGRATIONS OK] " . $migrate_output;
            }
        } else {
            log_event('INFO', 'No pending SQL migrations');
        }
    }
}


// =============================================================================
// SEND NOTIFICATION (via GitHub Actions dispatch)
// =============================================================================

if (!empty($CONFIG['github_token'])) {
    // Send environment-specific event type
    $event_type_map = [
        'dev' => 'staging-deployed',
        'qa' => 'staging-deployed',
        'staging' => 'staging-deployed',
        'prod' => 'production-deployed'
    ];
    $event_type = $event_type_map[$folder] ?? 'staging-deployed';
    
    // Debug: Log notification attempt details
    log_event('DEBUG', "Notification: repo=$repo_name, event=$event_type, token_len=" . strlen($CONFIG['github_token']));
    
    $notification_data = [
        'event_type' => $event_type,
        'client_payload' => [
            'branch' => $branch,
            'commit' => $commit_id,
            'message' => $commit_msg_clean,
            'user' => $author,
            'server' => $env_name,
            'status' => $success ? 'SUCCESS' : 'FAILED',
            'timestamp' => date('Y-m-d H:i:s'),
            'repository' => $repo_name,
            'deploy_mode' => $deploy_mode,
            'release_id' => isset($release_id) ? $release_id : 'N/A'
        ]
    ];
    
    $api_endpoint = "https://api.github.com/repos/$repo_name/dispatches";
    log_event('DEBUG', "API URL: $api_endpoint");
    
    $ch = curl_init($api_endpoint);
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
    $curl_error = curl_error($ch);
    curl_close($ch);
    
    if ($http_code === 204) {
        log_event('INFO', 'Notification sent ✓');
    } else {
        log_event('WARNING', "Notification failed (HTTP $http_code)");
        log_event('DEBUG', "Response: $response");
        if ($curl_error) {
            log_event('DEBUG', "CURL Error: $curl_error");
        }
    }
} else {
    log_event('WARNING', 'No GitHub token configured - skipping notification');
}

// =============================================================================
// LOG TO DEVOPS TOOL (Node.js Dashboard)
// =============================================================================

$devops_api_url = getenv('DEVOPS_API_URL') ?: '';
$devops_secret  = getenv('DEVOPS_WEBHOOK_SECRET') ?: (getenv('WEBHOOK_SECRET') ?: '');

if (!empty($devops_api_url)) {
    // Capture last 2000 chars of output as log_tail (pipe-delimited lines)
    $log_tail = '';
    if (!empty($output)) {
        $log_lines = explode("\n", substr($output, -2000));
        $log_tail = implode('|', array_filter($log_lines));
    }

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
        'deploy_type'    => 'source',
        'log_tail'       => $log_tail
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

$status = $success ? 'success' : 'completed';
$response = [
    'status' => $status,
    'environment' => $env_name,
    'branch' => $branch,
    'commit' => $commit_id,
    'deploy_mode' => $deploy_mode,
    'message' => "Deployment $status for $env_name"
];

log_event('INFO', "Deployment $status for $env_name");
log_event('INFO', '=== Webhook complete ===');

echo json_encode($response, JSON_PRETTY_PRINT);