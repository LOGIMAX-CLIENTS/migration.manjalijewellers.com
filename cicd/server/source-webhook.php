<?php
/**
 * Source Version Webhook Handler (AWS)
 * Routes deployments to correct environment based on branch name.
 *
 * Branch → Environment Mapping (AWS /var/www/retail/):
 *   Retail_1.1.1.0001 → dev       (git pull)
 *   QA                → qa        (git pull)
 *   support           → staging   (git pull)
 *   PRODUCTION        → prod+sales (deploy-production.sh — symlink releases)
 *
 * Called by: GitHub Actions (deploy-source.yml) via HTTP POST
 * Auth: X-Webhook-Secret header matched against WEBHOOK_SECRET env var
 *       Also supports X-Hub-Signature-256 (GitHub native) as fallback
 *
 * DevOps Tool Integration:
 *   - Git-pull envs (dev/qa/staging): report status directly via curl to /deploy-log
 *   - PRODUCTION: deploy-production.sh handles its own callbacks to /mono-deploy
 */

header('Content-Type: application/json; charset=utf-8');

// =============================================================================
// CONFIGURATION
// =============================================================================

$CONFIG = [
    'base_path' => '/var/www/retail',
    'log_file'  => '/var/www/retail/webhooks/source-deploy.log',

    // Branch → Folder mapping (AWS structure)
    'environments' => [
        'Retail_1.1.1.0001' => [
            'folder'  => 'dev',
            'name'    => 'Development',
            'type'    => 'git-pull',
        ],
        'QA' => [
            'folder'  => 'qa',
            'name'    => 'QA',
            'type'    => 'git-pull',
        ],
        'support' => [
            'folder'  => 'staging',
            'name'    => 'Staging',
            'type'    => 'git-pull',
        ],
        'PRODUCTION' => [
            'folder'  => 'prod',
            'name'    => 'Production',
            'type'    => 'deploy-script',   // Uses deploy-production.sh
        ],
    ],
];

// =============================================================================
// AUTH — supports both X-Webhook-Secret and X-Hub-Signature-256
// =============================================================================

$secret = getenv('WEBHOOK_SECRET') ?: (getenv('DEVOPS_WEBHOOK_SECRET') ?: ($_SERVER['WEBHOOK_SECRET'] ?? ''));

if (empty($secret)) {
    http_response_code(500);
    echo json_encode(['status' => false, 'msg' => 'WEBHOOK_SECRET not configured on server']);
    exit;
}

$input = file_get_contents('php://input');
if (empty($input)) {
    http_response_code(400);
    echo json_encode(['status' => false, 'msg' => 'Empty payload']);
    exit;
}

// Method 1: X-Webhook-Secret (plain string match — used by DevOps Tool / GitHub Actions)
$headerSecret = $_SERVER['HTTP_X_WEBHOOK_SECRET'] ?? '';
// Method 2: X-Hub-Signature-256 (HMAC — used by GitHub native webhooks)
$hubSignature = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';

$authenticated = false;
if (!empty($headerSecret) && hash_equals($secret, $headerSecret)) {
    $authenticated = true;
} elseif (!empty($hubSignature)) {
    $expected = 'sha256=' . hash_hmac('sha256', $input, $secret);
    if (hash_equals($expected, $hubSignature)) {
        $authenticated = true;
    }
}

if (!$authenticated) {
    http_response_code(401);
    echo json_encode(['status' => false, 'msg' => 'Invalid signature/secret']);
    exit;
}

// =============================================================================
// LOGGING
// =============================================================================

$logDir = dirname($CONFIG['log_file']);
if (!is_dir($logDir)) {
    @mkdir($logDir, 02775, true);
}

function wh_log($msg) {
    global $CONFIG;
    $ts = date('Y-m-d H:i:s');
    file_put_contents($CONFIG['log_file'], "[{$ts}] {$msg}\n", FILE_APPEND | LOCK_EX);
}

wh_log("=== Source Webhook Received ===");

// =============================================================================
// PARSE PAYLOAD
// =============================================================================

$data = json_decode($input, true);
if (!$data) {
    http_response_code(400);
    echo json_encode(['status' => false, 'msg' => 'Invalid JSON']);
    exit;
}

// Support both GitHub push event format and DevOps Tool format
// GitHub push: { ref: "refs/heads/branch", head_commit: {...}, pusher: {...} }
// DevOps Tool: { branch: "PRODUCTION", commit: "abc123", environment: "production" }
$branch = '';
if (!empty($data['ref'])) {
    // GitHub push event
    $branch = str_replace('refs/heads/', '', $data['ref']);
} elseif (!empty($data['branch'])) {
    // DevOps Tool trigger
    $branch = $data['branch'];
}

$commit_id  = $data['head_commit']['id'] ?? ($data['commit'] ?? 'unknown');
$commit_msg = $data['head_commit']['message'] ?? ($data['commit_message'] ?? 'No message');
$author     = $data['pusher']['name'] ?? ($data['triggered_by'] ?? 'webhook');

wh_log("Branch: {$branch} | Commit: " . substr($commit_id, 0, 8) . " | By: {$author}");

// =============================================================================
// ROUTE BY BRANCH
// =============================================================================

if (empty($branch) || !isset($CONFIG['environments'][$branch])) {
    $known = implode(', ', array_keys($CONFIG['environments']));
    wh_log("Unknown branch: {$branch} — ignoring (known: {$known})");
    http_response_code(200);
    echo json_encode(['status' => true, 'msg' => "Branch '{$branch}' not configured for deployment"]);
    exit;
}

$env       = $CONFIG['environments'][$branch];
$folder    = $env['folder'];
$env_name  = $env['name'];
$env_type  = $env['type'];
$env_path  = $CONFIG['base_path'] . '/' . $folder;

wh_log("Deploying to: {$env_name} ({$folder}) via {$env_type}");

// =============================================================================
// SSH KEY DETECTION
// =============================================================================

$sshKey = '';
$sshSearchPaths = [
    '/var/www/.ssh/id_ed25519',
    '/home/ubuntu/.ssh/id_ed25519',
    '/home/ubuntu/.ssh/deploy_key',
    '/home/ubuntu/.ssh/id_rsa_deploy',
];
foreach ($sshSearchPaths as $keyPath) {
    if (file_exists($keyPath)) {
        $sshKey = $keyPath;
        break;
    }
}
if (empty($sshKey)) {
    $sshKey = '/var/www/.ssh/id_ed25519'; // fallback
}
wh_log("[DEBUG] SSH Key: {$sshKey} (exists: " . (file_exists($sshKey) ? 'YES' : 'NO') . ")");

// =============================================================================
// DEVOPS TOOL CREDENTIALS
// =============================================================================

$devopsUrl    = $data['devops_api_url'] ?? (getenv('DEVOPS_API_URL') ?: '');
$devopsSecret = $data['devops_webhook_secret'] ?? (getenv('DEVOPS_WEBHOOK_SECRET') ?: '');

wh_log("[DEBUG] DevOps URL: " . ($devopsUrl ?: '(empty)'));
wh_log("[DEBUG] DevOps Secret: " . (empty($devopsSecret) ? '(empty)' : strlen($devopsSecret) . ' chars'));

// =============================================================================
// DEPLOYMENT
// =============================================================================

if ($env_type === 'deploy-script') {
    // ── PRODUCTION: invoke deploy-production.sh ──
    // deploy-production.sh handles: clone, symlink swap, maintenance, migrations,
    // and its own DevOps callbacks (start/end) to /mono-deploy
    $deployScript = "{$CONFIG['base_path']}/prod/shared/deploy-production.sh";
    if (!file_exists($deployScript)) {
        $deployScript = "{$CONFIG['base_path']}/prod/current/cicd/server/deploy-production.sh";
    }
    if (!file_exists($deployScript)) {
        http_response_code(500);
        $msg = "deploy-production.sh not found in shared/ or current/cicd/server/";
        wh_log("ERROR: {$msg}");
        echo json_encode(['status' => false, 'msg' => $msg]);
        exit;
    }

    wh_log("[DEBUG] Deploy script: {$deployScript}");

    // deploy-production.sh reads these from env or its own defaults
    // Sanitize branch name (prevent shell injection — only allow alphanumeric, underscore, dot, dash)
    $safeBranch = preg_replace('/[^a-zA-Z0-9._-]/', '', $branch);
    $safeDevopsUrl = preg_replace('/[^a-zA-Z0-9:\/._-]/', '', $devopsUrl);

    $cmd = "bash -c '"
        . "export BASE_PATH=\"{$CONFIG['base_path']}\" "
        . "&& export DEPLOY_BRANCH=\"{$safeBranch}\" "
        . "&& export GIT_REMOTE_URL=\"git@github.com:Logimax-Technologies/etail_development_src.git\" "
        . "&& export SSH_KEY=\"{$sshKey}\" "
        . "&& export DEVOPS_API_URL=\"{$safeDevopsUrl}\" "
        . "&& export DEVOPS_WEBHOOK_SECRET=\"{$devopsSecret}\" "
        . "&& bash {$deployScript}"
        . "' >> {$CONFIG['log_file']} 2>&1 &";

    wh_log("[DEBUG] Command: " . substr($cmd, 0, 500));
    exec($cmd);
    wh_log("[DEBUG] deploy-production.sh launched in background");

    $deploy_status = 'triggered';  // actual status comes from deploy-production.sh callbacks

} else {
    // ── DEV / QA / STAGING: simple git pull ──
    if (!is_dir($env_path)) {
        http_response_code(500);
        $msg = "Directory not found: {$env_path}";
        wh_log("ERROR: {$msg}");
        echo json_encode(['status' => false, 'msg' => $msg]);
        exit;
    }

    // Build git pull command with proper SSH and safe.directory config
    $git_cmd = sprintf(
        "cd %s && export GIT_SSH_COMMAND='ssh -i %s -o IdentitiesOnly=yes -o StrictHostKeyChecking=no' && export GIT_CONFIG_COUNT=1 && export GIT_CONFIG_KEY_0=safe.directory && export GIT_CONFIG_VALUE_0='%s' && git fetch origin %s 2>&1 && git reset --hard origin/%s 2>&1",
        escapeshellarg($env_path),
        escapeshellarg($sshKey),
        $env_path,
        escapeshellarg($branch),
        escapeshellarg($branch)
    );

    wh_log("[DEBUG] Git command: " . substr($git_cmd, 0, 500));

    $startTime = time();
    $output = shell_exec($git_cmd);
    $duration = time() - $startTime;

    wh_log("[DEBUG] Git output: " . substr($output ?? '', 0, 500));

    // Determine status from git output
    $deploy_status = 'success';
    if (empty($output) || strpos($output, 'fatal') !== false || strpos($output, 'error:') !== false) {
        $deploy_status = 'failed';
    }

    wh_log("Git pull {$deploy_status} for {$env_name} ({$duration}s)");

    // ── DevOps callback for git-pull envs ──
    if (!empty($devopsUrl) && !empty($devopsSecret)) {
        $callbackPayload = json_encode([
            'client_id'      => 'source',
            'client_name'    => 'Source Repository',
            'environment'    => strtolower($env_name),
            'status'         => $deploy_status,
            'branch'         => $branch,
            'commit_sha'     => $commit_id,
            'commit_message' => substr($commit_msg, 0, 200),
            'triggered_by'   => $author,
            'trigger_type'   => 'webhook',
            'deploy_type'    => 'source',
            'duration'       => $duration,
            'log_tail'       => substr($output ?? '', -500),
        ]);

        $ch = curl_init("{$devopsUrl}/api/webhooks/deploy-log");
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $callbackPayload,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                "X-Webhook-Secret: {$devopsSecret}",
            ],
        ]);
        $cbResponse = curl_exec($ch);
        $cbHttp     = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $cbError    = curl_error($ch);
        curl_close($ch);

        wh_log("[DEBUG] DevOps callback ({$deploy_status}): HTTP {$cbHttp} — " . ($cbError ?: $cbResponse));
    } else {
        wh_log("[DEBUG] DevOps callback SKIPPED — URL or secret not configured");
    }
}

// =============================================================================
// RESPONSE
// =============================================================================

$response = [
    'status'      => true,
    'deploy'      => $deploy_status,
    'environment' => $env_name,
    'branch'      => $branch,
    'commit'      => substr($commit_id, 0, 8),
    'message'     => "Deployment {$deploy_status} for {$env_name}",
];

wh_log("Response: " . json_encode($response));
wh_log("=== Source Webhook End ===");

http_response_code(200);
echo json_encode($response, JSON_PRETTY_PRINT);
