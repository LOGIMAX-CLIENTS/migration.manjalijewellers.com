<?php
/**
 * Mono-Repo Deploy Webhook Handler
 * Staging: fetch → PHP syntax check → merge (with maintenance page)
 * Production: deploy-mono.sh (releases, symlink, rollback)
 *
 * Called by: GitHub Actions (deploy-mono-client.yml)
 * Auth: X-Webhook-Secret header matched against WEBHOOK_SECRET env var
 *
 * Phase 2.4a: Dynamic client paths + DevOps Tool status callbacks
 * Phase 2.5:  Server-side PHP syntax check gate for staging
 */

header('Content-Type: application/json; charset=utf-8');

// — Auth —
$secret = getenv('WEBHOOK_SECRET') ?: (getenv('DEVOPS_WEBHOOK_SECRET') ?: '');
$signature = $_SERVER['HTTP_X_WEBHOOK_SECRET'] ?? '';

if (empty($secret)) {
    http_response_code(500);
    echo json_encode(['status' => false, 'msg' => 'WEBHOOK_SECRET not configured on server']);
    exit;
}

if (!hash_equals($secret, $signature)) {
    http_response_code(401);
    echo json_encode(['status' => false, 'msg' => 'Invalid secret']);
    exit;
}

// — Parse input —
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    http_response_code(400);
    echo json_encode(['status' => false, 'msg' => 'Invalid JSON']);
    exit;
}

$environment = $input['environment'] ?? 'staging';
$branch = $input['branch'] ?? null;
$commit = $input['commit'] ?? 'unknown';

// — DevOps Tool API credentials (forwarded from GitHub Actions payload) —
$devopsUrl = $input['devops_api_url'] ?? '';
$devopsSecret = $input['devops_webhook_secret'] ?? '';

// — Dynamic client ID detection —
// Priority: env var > directory name auto-detection
// On client servers, CLIENT_ID env var is set by setup-mono-client.sh
// Fallback: derive from the webhook-deploy.php file path
//   /var/www/{client}/prod/current/cicd/server/webhook-deploy.php → client = basename 5 levels up
$clientId = getenv('CLIENT_ID') ?: '';
if (empty($clientId)) {
    // Auto-detect from file path: go up from cicd/server/ to find the client dir
    $scriptDir = dirname(__FILE__); // .../cicd/server
    $currentDir = dirname(dirname($scriptDir)); // .../current or .../staging
    $prodDir = dirname($currentDir); // .../prod or /var/www/{client}
    $clientDir = dirname($prodDir);  // /var/www/{client}
    $clientId = basename($clientDir);
    // Validate: if we ended up with a system dir, try one level closer
    if (in_array($clientId, ['www', 'var', ''], true)) {
        $clientId = basename($prodDir);
    }
}

// — SSH Key (dynamic: prefer client-specific, fall back to generic) —
// Keys must be owned by www-data with 0600 permissions (SSH rejects group-readable keys)
// One-time server setup: sudo chown www-data:www-data /var/www/.ssh/id_ed25519_*
//                        sudo chmod 0600 /var/www/.ssh/id_ed25519_*
$sshKey = '';
$sshSearchDirs = ['/var/www/.ssh', '/home/ubuntu/.ssh'];
foreach ($sshSearchDirs as $sshDir) {
    if (is_dir($sshDir)) {
        // NOTE: SSH key permissions (0600, owned by www-data) must be set once during
        // server setup via: sudo chown www-data:www-data /var/www/.ssh/id_ed25519_*
        //                   sudo chmod 0600 /var/www/.ssh/id_ed25519_*
        // Do NOT chmod here — www-data calling chmod on ubuntu-owned keys corrupts
        // permissions and causes "Permission denied" on subsequent deploys.
    }
    // 1. Client-specific key (e.g., id_ed25519_sarangapani)
    if (file_exists("{$sshDir}/id_ed25519_{$clientId}")) {
        $sshKey = "{$sshDir}/id_ed25519_{$clientId}";
        break;
    }
    // 2. Generic key
    if (file_exists("{$sshDir}/id_ed25519")) {
        $sshKey = "{$sshDir}/id_ed25519";
        break;
    }
}
if (empty($sshKey)) {
    $sshKey = '/etc/deploy-keys/id_ed25519'; // last resort
}
$logFile = "/var/www/{$clientId}/prod/shared/logs/webhook.log";

// — Ensure log directory exists —
$logDir = dirname($logFile);
if (!is_dir($logDir)) {
    @mkdir($logDir, 02775, true);
}

// — Helper: log to webhook.log with timestamp —
function wh_log($logFile, $msg) {
    $ts = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[{$ts}] {$msg}\n", FILE_APPEND);
}

// — Log —
$logEntry = date('Y-m-d H:i:s') . " | Webhook: env=$environment"
    . ' branch=' . ($branch ?: 'auto')
    . " commit=$commit"
    . ' client=' . $clientId
    . ' ip=' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown') . "\n";
file_put_contents($logFile, $logEntry, FILE_APPEND);

// — Debug: dump all resolved configuration —
wh_log($logFile, "[DEBUG] === Webhook Request Start ===");
wh_log($logFile, "[DEBUG] Client ID:     {$clientId}");
wh_log($logFile, "[DEBUG] Environment:   {$environment}");
wh_log($logFile, "[DEBUG] Branch:        " . ($branch ?: '(auto-detect)'));
wh_log($logFile, "[DEBUG] Commit:        {$commit}");
wh_log($logFile, "[DEBUG] SSH Key:       {$sshKey} (exists: " . (file_exists($sshKey) ? 'YES' : 'NO') . ")");
wh_log($logFile, "[DEBUG] Log File:      {$logFile}");
wh_log($logFile, "[DEBUG] DevOps URL:    " . ($devopsUrl ?: '(empty)'));
wh_log($logFile, "[DEBUG] DevOps Secret: " . (empty($devopsSecret) ? '(empty)' : strlen($devopsSecret) . ' chars'));
wh_log($logFile, "[DEBUG] Secret match:  " . (hash_equals($secret, $signature) ? 'YES' : 'NO'));

// — Helper: Build DevOps callback curl command (one-shot deploy-log) —
function buildDevopsCallback($devopsUrl, $devopsSecret, $clientId, $environment, $status, $commit, $branch = 'support', $logFile = '') {
    if (empty($devopsUrl)) {
        return 'true  # no DevOps URL configured';
    }
    // If logFile is provided, capture last 20 lines at shell runtime
    if (!empty($logFile)) {
        // Build payload with jq for safe JSON encoding (handles special chars in log output)
        // Strip control characters from log_tail to prevent JSON parse errors
        return "LOG_TAIL=\$(tail -20 '{$logFile}' 2>/dev/null | tr -d '\\000-\\037' | sed 's/\"/\\\\\"/g' | tr '\\n' '|')"
            . " && DEVOPS_RESP=\$(curl -s -w '|%{http_code}' -X POST '{$devopsUrl}/api/webhooks/deploy-log'"
            . " -H 'Content-Type: application/json'"
            . " -H 'X-Webhook-Secret: {$devopsSecret}'"
            . " -d '{\"client_id\":\"{$clientId}\",\"client_name\":\"{$clientId}\",\"environment\":\"{$environment}\",\"status\":\"{$status}\",\"branch\":\"{$branch}\",\"commit_sha\":\"{$commit}\",\"triggered_by\":\"webhook\",\"trigger_type\":\"github_action\",\"deploy_type\":\"mono-client\",\"log_tail\":\"'\"\$LOG_TAIL\"'\"}'"
            . " --max-time 10 2>&1)"
            . " && echo \"[\$(date '+%Y-%m-%d %H:%M:%S')] [DEBUG] DevOps callback ({$status}): \$DEVOPS_RESP\" >> {$logFile}"
            . " || echo \"[\$(date '+%Y-%m-%d %H:%M:%S')] [DEBUG] DevOps callback ({$status}) FAILED: \$DEVOPS_RESP\" >> {$logFile}";
    }
    $payload = json_encode([
        'client_id'    => $clientId,
        'client_name'  => $clientId,
        'environment'  => $environment,
        'status'       => $status,
        'branch'       => $branch,
        'commit_sha'   => $commit,
        'triggered_by' => 'webhook',
        'trigger_type' => 'github_action',
        'deploy_type'  => 'mono-client'
    ]);
    // Escape for shell
    $payload = str_replace("'", "'\\''", $payload);
    return "DEVOPS_RESP=\$(curl -s -w '|%{http_code}' -X POST '{$devopsUrl}/api/webhooks/deploy-log'"
        . " -H 'Content-Type: application/json'"
        . " -H 'X-Webhook-Secret: {$devopsSecret}'"
        . " -d '{$payload}'"
        . " --max-time 10 2>&1)"
        . " && echo \"[\$(date '+%Y-%m-%d %H:%M:%S')] [DEBUG] DevOps callback ({$status}): \$DEVOPS_RESP\" >> {$logFile}"
        . " || echo \"[\$(date '+%Y-%m-%d %H:%M:%S')] [DEBUG] DevOps callback ({$status}) FAILED: \$DEVOPS_RESP\" >> {$logFile}";
}

// — Helper: Build staging deploy script file —
// Writing to a temp script avoids the fragile bash -c '...' + str_replace escaping
// that breaks when compound commands contain single quotes (SSH flags, log messages).
// PHP exec() runs via /bin/sh (dash on Ubuntu), which doesn't support 'exec 200>'
// file descriptor syntax needed for flock-based deploy locking.
function buildStagingScript($clientId, $branch, $stagingDir, $maintenanceFlag, $sshKey, $logFile, $devopsCallbackSuccess, $devopsCallbackFailed, $commit) {
    $script = "#!/bin/bash\n";
    $script .= "# Auto-generated staging deploy script for {$clientId}\n\n";

    // Deploy lock
    $script .= "LOCK_FILE='/tmp/deploy-{$clientId}-staging.lock'\n";
    $script .= "exec 200>\"\$LOCK_FILE\"\n";
    $script .= "if ! flock -n 200; then\n";
    $script .= "    echo \"Another staging deploy running, waiting...\" >> {$logFile}\n";
    $script .= "    flock 200\n";
    $script .= "fi\n\n";

    // Maintenance page
    $script .= "touch {$maintenanceFlag}\n\n";

    // Fix .git AND working tree permissions — prevents 'unable to unlink' / 'Permission denied'
    // Mono-repo staging dirs have 15k+ files; PHP-FPM (www-data) may create cached/temp files
    // with restrictive perms that block git reset --hard from unlinking them.
    // Requires one-time server setup:
    //   sudo chown -R www-data:www-data /var/www/{client}/staging
    //   sudo find /var/www/{client}/staging -type d -exec chmod 2775 {} \;
    //   sudo find /var/www/{client}/staging -type f -exec chmod 0664 {} \;
    $script .= "# --- Permission self-healing (prevents 'unable to unlink' on git reset) ---\n";
    $script .= "DEPLOY_USER=\$(stat -c '%U' {$stagingDir} 2>/dev/null || echo 'www-data')\n";
    $script .= "DEPLOY_GROUP=\$(stat -c '%G' {$stagingDir} 2>/dev/null || echo 'www-data')\n";
    $script .= "CURRENT_USER=\$(whoami)\n";
    $script .= "echo \"[\$(date '+%Y-%m-%d %H:%M:%S')] [DEBUG] Perm fix: user=\$CURRENT_USER dir_owner=\$DEPLOY_USER:\$DEPLOY_GROUP\" >> {$logFile}\n";
    $script .= "# Fix .git internals (critical for index reset)\n";
    $script .= "chmod -R u+w {$stagingDir}/.git 2>/dev/null || true\n";
    $script .= "# Fix working tree — ensure all dirs are writable (required for unlink)\n";
    $script .= "find {$stagingDir} -type d ! -writable -exec chmod u+w {} + 2>/dev/null || true\n";
    $script .= "# If running as different user, attempt ownership fix\n";
    $script .= "if [ \"\$CURRENT_USER\" != \"\$DEPLOY_USER\" ]; then\n";
    $script .= "    chown -R \$CURRENT_USER:\$DEPLOY_GROUP {$stagingDir}/.git 2>/dev/null || true\n";
    $script .= "    # Fix non-writable files in working tree\n";
    $script .= "    find {$stagingDir} -not -writable -exec chmod u+w {} + 2>/dev/null || true\n";
    $script .= "fi\n\n";

    // Git operations — fix safe.directory for www-data running in another user's repo
    // Use env vars instead of git config --global (www-data has no writable home dir)
    $script .= "export GIT_CONFIG_COUNT=1\n";
    $script .= "export GIT_CONFIG_KEY_0=safe.directory\n";
    $script .= "export GIT_CONFIG_VALUE_0='{$stagingDir}'\n";
    $script .= "cd {$stagingDir}\n";
    $script .= "export GIT_SSH_COMMAND='ssh -i {$sshKey} -o IdentitiesOnly=yes -o StrictHostKeyChecking=no'\n";
    $script .= "echo \"[\$(date '+%Y-%m-%d %H:%M:%S')] [DEBUG] git fetch origin {$branch}...\" >> {$logFile}\n";
    $script .= "git fetch origin {$branch} >> {$logFile} 2>&1\n";
    $script .= "FETCH_EXIT=\$?\n";
    $script .= "if [ \$FETCH_EXIT -ne 0 ]; then\n";
    $script .= "    echo \"[\$(date '+%Y-%m-%d %H:%M:%S')] git fetch FAILED (exit \$FETCH_EXIT)\" >> {$logFile}\n";
    $script .= "    rm -f {$maintenanceFlag}\n";
    $script .= "    {$devopsCallbackFailed}\n";
    $script .= "    exit 1\n";
    $script .= "fi\n\n";

    // PHP syntax check on changed files
    $script .= "CHANGED_PHP=\$(git diff --name-only HEAD..FETCH_HEAD -- '*.php' 2>/dev/null || true)\n";
    $script .= "SYNTAX_FAIL=0\n";
    $script .= "if [ -n \"\$CHANGED_PHP\" ]; then\n";
    $script .= "    for file in \$CHANGED_PHP; do\n";
    $script .= "        git show FETCH_HEAD:\"\$file\" > /tmp/_syntax_check.php 2>/dev/null || true\n";
    $script .= "        if ! php -l /tmp/_syntax_check.php > /dev/null 2>&1; then\n";
    $script .= "            SYNTAX_FAIL=\$((SYNTAX_FAIL + 1))\n";
    $script .= "            echo \"[\$(date '+%Y-%m-%d %H:%M:%S')] Syntax error in \$file\" >> {$logFile}\n";
    $script .= "        fi\n";
    $script .= "    done\n";
    $script .= "    rm -f /tmp/_syntax_check.php\n";
    $script .= "fi\n\n";

    // Abort if syntax errors
    $script .= "if [ \"\$SYNTAX_FAIL\" -gt 0 ]; then\n";
    $script .= "    echo \"[\$(date '+%Y-%m-%d %H:%M:%S')] PHP syntax check FAILED -- \$SYNTAX_FAIL file(s)\" >> {$logFile}\n";
    $script .= "    rm -f {$maintenanceFlag}\n";
    $script .= "    {$devopsCallbackFailed}\n";
    $script .= "    exit 1\n";
    $script .= "fi\n\n";

    // Merge — with retry fallback for permission-denied scenarios
    $script .= "git reset --hard origin/{$branch} >> {$logFile} 2>&1\n";
    $script .= "RESET_EXIT=\$?\n";
    $script .= "if [ \$RESET_EXIT -ne 0 ]; then\n";
    $script .= "    echo \"[\$(date '+%Y-%m-%d %H:%M:%S')] git reset --hard FAILED (exit \$RESET_EXIT), attempting recovery...\" >> {$logFile}\n";
    $script .= "    # Recovery: force-fix all file permissions, then retry with checkout -f\n";
    $script .= "    find {$stagingDir} -not -path '{$stagingDir}/.git/*' -not -writable -exec chmod u+w {} + 2>/dev/null || true\n";
    $script .= "    find {$stagingDir} -type d -not -writable -exec chmod u+w {} + 2>/dev/null || true\n";
    $script .= "    git checkout -f origin/{$branch} >> {$logFile} 2>&1\n";
    $script .= "    CHECKOUT_EXIT=\$?\n";
    $script .= "    if [ \$CHECKOUT_EXIT -ne 0 ]; then\n";
    $script .= "        echo \"[\$(date '+%Y-%m-%d %H:%M:%S')] git checkout -f also FAILED (exit \$CHECKOUT_EXIT)\" >> {$logFile}\n";
    $script .= "        rm -f {$maintenanceFlag}\n";
    $script .= "        {$devopsCallbackFailed}\n";
    $script .= "        exit 1\n";
    $script .= "    fi\n";
    $script .= "    echo \"[\$(date '+%Y-%m-%d %H:%M:%S')] Recovery succeeded via git checkout -f\" >> {$logFile}\n";
    $script .= "fi\n";
    $script .= "git clean -fd >> {$logFile} 2>&1 || true\n\n";

    // Database migrations
    $script .= "MIGRATE_SCRIPT='{$stagingDir}/scripts/run-migrations.sh'\n";
    $script .= "MIGRATE_DIR='{$stagingDir}/database/migrations'\n";
    $script .= "MIGRATE_CONFIG='{$stagingDir}/global_configs.php'\n";
    $script .= "if [ -f \"\$MIGRATE_SCRIPT\" ] && [ -d \"\$MIGRATE_DIR\" ] \\\n";
    $script .= "   && [ \$(find \"\$MIGRATE_DIR\" -maxdepth 1 -name '*.sql' -type f 2>/dev/null | wc -l) -gt 0 ]; then\n";
    $script .= "    echo \"[\$(date '+%Y-%m-%d %H:%M:%S')] Running staging migrations...\" >> {$logFile}\n";
    $script .= "    MIGRATE_RESULT=\$(bash \"\$MIGRATE_SCRIPT\" --config \"\$MIGRATE_CONFIG\" --migrations \"\$MIGRATE_DIR\" --env staging --json 2>&1)\n";
    $script .= "    MIGRATE_EXIT=\$?\n";
    $script .= "    if [ \$MIGRATE_EXIT -eq 1 ]; then\n";
    $script .= "        echo \"[\$(date '+%Y-%m-%d %H:%M:%S')] Staging migration FAILED: \$MIGRATE_RESULT\" >> {$logFile}\n";
    $script .= "        rm -f {$maintenanceFlag}\n";
    $script .= "        {$devopsCallbackFailed}\n";
    $script .= "        exit 1\n";
    $script .= "    else\n";
    $script .= "        echo \"[\$(date '+%Y-%m-%d %H:%M:%S')] Staging migrations done: \$MIGRATE_RESULT\" >> {$logFile}\n";
    $script .= "    fi\n";
    $script .= "fi\n\n";

    // Cleanup
    $script .= "rm -f {$maintenanceFlag}\n";
    $script .= "echo \"[\$(date '+%Y-%m-%d %H:%M:%S')] Staging deploy complete: {$commit}\" >> {$logFile}\n";
    $script .= "{$devopsCallbackSuccess}\n";

    return $script;
}

// — Deploy —
if ($environment === 'staging') {
    $branch = $branch ?: 'support';
    $stagingDir = "/var/www/{$clientId}/staging";
    $maintenanceFlag = "{$stagingDir}/.maintenance_active";

    // Phase 2.5: Staging syntax gate — fetch → check changed PHP → merge
    $devopsCallbackSuccess = buildDevopsCallback($devopsUrl, $devopsSecret, $clientId, 'staging', 'success', $commit, 'support', $logFile);
    $devopsCallbackFailed = buildDevopsCallback($devopsUrl, $devopsSecret, $clientId, 'staging', 'failed', $commit, 'support', $logFile);

    // Phase 2.5b: Write deploy script to temp file and run with bash.
    // This avoids the bash -c '...' + str_replace escaping that breaks under
    // /bin/sh (dash on Ubuntu) — dash doesn't support 'exec 200>' fd syntax.
    $tmpScript = "/tmp/deploy-{$clientId}-staging.sh";
    $scriptContent = buildStagingScript($clientId, $branch, $stagingDir, $maintenanceFlag, $sshKey, $logFile, $devopsCallbackSuccess, $devopsCallbackFailed, $commit);
    file_put_contents($tmpScript, $scriptContent);
    chmod($tmpScript, 0755);
    $cmd = "bash {$tmpScript} >> {$logFile} 2>&1 &";

    wh_log($logFile, "[DEBUG] Staging script written to: {$tmpScript}");
    wh_log($logFile, "[DEBUG] Staging script size: " . strlen($scriptContent) . " bytes");
    wh_log($logFile, "[DEBUG] Staging dir exists: " . (is_dir($stagingDir) ? 'YES' : 'NO'));
    wh_log($logFile, "[DEBUG] Staging .git exists: " . (is_dir($stagingDir . '/.git') ? 'YES' : 'NO'));

} elseif ($environment === 'production') {
    $branch = $branch ?: 'PRODUCTION';
    // Prefer shared/ (self-updated by deploy-mono.sh) over current/ (may be stale)
    $prodScript = "/var/www/{$clientId}/prod/shared/deploy-mono.sh";
    if (!file_exists($prodScript)) {
        $prodScript = "/var/www/{$clientId}/prod/current/cicd/server/deploy-mono.sh";
    }

    if (!file_exists($prodScript)) {
        http_response_code(500);
        $msg = "Deploy script not found in shared/ or current/cicd/server/";
        file_put_contents($logFile, date('Y-m-d H:i:s') . " | ERROR: $msg\n", FILE_APPEND);
        echo json_encode(['status' => false, 'msg' => $msg]);
        exit;
    }


    // Derive repo URL so deploy-mono.sh works even on first deploy
    // (when the current symlink has no .git to auto-detect from)
    // Priority: 1. deploy.env  2. auto-detect from current/.git  3. mono-repo fallback
    $repoUrl = '';
    $repoUrlSource = 'none';
    $currentLink = "/var/www/{$clientId}/prod/current";

    // 1. Read from deploy.env (most reliable — set by setup-mono-client.sh)
    $deployEnvForRepo = "/var/www/{$clientId}/prod/shared/deploy.env";
    if (file_exists($deployEnvForRepo)) {
        $envLines = file_get_contents($deployEnvForRepo);
        if (preg_match('/^REPO_URL=(.+)$/m', $envLines, $rm)) {
            $repoUrl = trim($rm[1]);
            if (!empty($repoUrl)) $repoUrlSource = 'deploy.env';
        }
    }

    // 2. Auto-detect from current/.git
    if (empty($repoUrl) && is_link($currentLink) && is_dir($currentLink . '/.git')) {
        // Set safe.directory via env so www-data can read origin URL from ubuntu-owned release dirs
        $repoUrl = trim(shell_exec("GIT_CONFIG_COUNT=1 GIT_CONFIG_KEY_0=safe.directory GIT_CONFIG_VALUE_0='{$currentLink}' git -C '{$currentLink}' remote get-url origin 2>/dev/null") ?: '');
        if (!empty($repoUrl)) $repoUrlSource = 'auto-detect from current/.git';
    }

    // 3. Fallback to mono-repo convention (not individual-repo convention)
    if (empty($repoUrl)) {
        $repoUrl = "git@github.com:Logimax-Technologies/etail_development_src.git";
        $repoUrlSource = 'fallback convention (mono-repo)';
    }
    wh_log($logFile, "[DEBUG] REPO_URL: {$repoUrl} (source: {$repoUrlSource})");
    wh_log($logFile, "[DEBUG] current symlink: " . (is_link($currentLink) ? readlink($currentLink) : 'NOT A SYMLINK'));
    wh_log($logFile, "[DEBUG] current/.git exists: " . (is_dir($currentLink . '/.git') ? 'YES' : 'NO'));
    wh_log($logFile, "[DEBUG] prodScript exists: " . (file_exists($prodScript) ? 'YES' : 'NO'));

    // deploy-mono.sh handles its own maintenance page, locking, syntax checks, and DevOps callbacks
    // HEALTH_CHECK_URL: derive from current Apache ServerName or config — deploy.env holds this,
    // but also pass explicitly to cover cases where deploy.env doesn't exist yet.
    $healthCheckUrl = "https://{$clientId}";  // placeholder — deploy.env overrides this
    // Try to read from deploy.env if it exists
    $deployEnvFile = "/var/www/{$clientId}/prod/shared/deploy.env";
    if (file_exists($deployEnvFile)) {
        $envContent = file_get_contents($deployEnvFile);
        if (preg_match('/^HEALTH_CHECK_URL=(.+)$/m', $envContent, $m)) {
            $healthCheckUrl = trim($m[1]);
        }
    }

    $cmd = "bash -c 'export BASE_DIR=\"/var/www/{$clientId}/prod\""
        . " && export CLIENT_ID=\"{$clientId}\""
        . " && export DEPLOY_ENV=\"production\""
        . " && export SSH_KEY=\"{$sshKey}\""
        . " && export REPO_URL=\"{$repoUrl}\""
        . " && export HEALTH_CHECK_URL=\"{$healthCheckUrl}\""
        . " && export DEVOPS_API_URL=\"{$devopsUrl}\""
        . " && export DEVOPS_WEBHOOK_SECRET=\"{$devopsSecret}\""
        . " && bash {$prodScript} {$branch}'"
        . " >> {$logFile} 2>&1 &";

} else {
    http_response_code(400);
    echo json_encode(['status' => false, 'msg' => "Unknown environment: $environment"]);
    exit;
}

wh_log($logFile, "[DEBUG] Command to execute: " . substr($cmd, 0, 500));
exec($cmd);
wh_log($logFile, "[DEBUG] exec() returned — deploy running in background");
wh_log($logFile, "[DEBUG] === Webhook Request End ===");

http_response_code(200);
echo json_encode([
    'status' => true,
    'msg' => 'Deploy triggered',
    'environment' => $environment,
    'branch' => $branch,
    'client_id' => $clientId,
    'debug' => [
        'ssh_key' => $sshKey,
        'ssh_key_exists' => file_exists($sshKey),
        'log_file' => $logFile,
        'devops_url' => !empty($devopsUrl) ? 'set' : 'empty',
    ]
]);
