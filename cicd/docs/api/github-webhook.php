<?php
/**
 * CI/CD Dashboard - GitHub Webhook Handler
 * Receives PR events and updates sync status
 * 
 * Events handled:
 * - pull_request (opened, closed, merged)
 * - pull_request_review (for conflict detection)
 * 
 * Setup:
 * 1. Add webhook in GitHub repo settings
 * 2. URL: https://retail.logimaxindia.com/develop/cicd/docs/api/github-webhook.php
 * 3. Content type: application/json
 * 4. Secret: (set in config or .github_webhook_secret file)
 * 5. Events: Pull requests
 */

define('CICD_API', true);
require_once __DIR__ . '/config.php';

// Log webhook calls
$logFile = '/tmp/github_webhook.log';
$logData = date('Y-m-d H:i:s') . " - Webhook received\n";

// Get headers
$event = $_SERVER['HTTP_X_GITHUB_EVENT'] ?? '';
$signature = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';
$delivery = $_SERVER['HTTP_X_GITHUB_DELIVERY'] ?? '';

// Get payload
$payload = file_get_contents('php://input');
$data = json_decode($payload, true);

// Log basic info
$logData .= "Event: $event, Delivery: $delivery\n";

// Verify signature (optional but recommended)
$secretFile = '/home/retaillogimaxind/.github_webhook_secret';
if (file_exists($secretFile)) {
    $secret = trim(file_get_contents($secretFile));
    $expectedSig = 'sha256=' . hash_hmac('sha256', $payload, $secret);
    
    if (!hash_equals($expectedSig, $signature)) {
        $logData .= "ERROR: Invalid signature\n";
        file_put_contents($logFile, $logData, FILE_APPEND);
        http_response_code(403);
        exit('Invalid signature');
    }
}

// Only handle pull_request events
if ($event !== 'pull_request') {
    $logData .= "Ignored: Not a pull_request event\n";
    file_put_contents($logFile, $logData, FILE_APPEND);
    echo json_encode(['status' => 'ignored', 'reason' => 'not pull_request event']);
    exit;
}

// Extract PR info
$action = $data['action'] ?? '';
$pr = $data['pull_request'] ?? [];
$repo = $data['repository']['name'] ?? '';
$prNumber = $pr['number'] ?? 0;
$prUrl = $pr['html_url'] ?? '';
$prState = $pr['state'] ?? '';
$merged = $pr['merged'] ?? false;
$mergeable = $pr['mergeable'] ?? null;
$mergeableState = $pr['mergeable_state'] ?? '';

$logData .= "Repo: $repo, PR #$prNumber, Action: $action, State: $prState, Merged: " . ($merged ? 'yes' : 'no') . "\n";

// Determine sync status based on PR state
$syncStatus = null;
$errorMessage = null;

switch ($action) {
    case 'opened':
    case 'reopened':
        $syncStatus = 'syncing';
        break;
        
    case 'closed':
        if ($merged) {
            $syncStatus = 'success';
        } else {
            $syncStatus = 'failed';
            $errorMessage = 'PR closed without merge';
        }
        break;
        
    case 'synchronize':
        // PR updated with new commits
        $syncStatus = 'syncing';
        break;
        
    case 'labeled':
        // Check for conflict labels
        $labels = array_column($pr['labels'] ?? [], 'name');
        if (in_array('conflict', $labels) || in_array('merge-conflict', $labels)) {
            $syncStatus = 'conflict';
            $errorMessage = 'Merge conflict detected';
        }
        break;
}

// Check mergeable state for conflicts
if ($mergeableState === 'dirty' || $mergeable === false) {
    $syncStatus = 'conflict';
    $errorMessage = 'Merge conflict detected';
}

$logData .= "Determined status: $syncStatus\n";

// Update database if we have a status
if ($syncStatus) {
    try {
        $pdo = getDbConnection();
        
        // Find matching sync record by client_id (repo name) and pending/syncing status
        $stmt = $pdo->prepare("
            UPDATE cicd_sync_history 
            SET status = ?,
                pr_number = ?,
                pr_url = ?,
                error_message = ?,
                completed_at = CASE WHEN ? IN ('success', 'failed', 'conflict') THEN NOW() ELSE completed_at END
            WHERE client_id = ? 
              AND status IN ('pending', 'syncing')
            ORDER BY created_at DESC 
            LIMIT 1
        ");
        
        $stmt->execute([
            $syncStatus,
            $prNumber,
            $prUrl,
            $errorMessage,
            $syncStatus,
            $repo
        ]);
        
        $affected = $stmt->rowCount();
        $logData .= "Updated $affected record(s)\n";
        
    } catch (Exception $e) {
        $logData .= "DB Error: " . $e->getMessage() . "\n";
    }
}

file_put_contents($logFile, $logData, FILE_APPEND);

header('Content-Type: application/json');
echo json_encode([
    'status' => 'processed',
    'event' => $event,
    'action' => $action,
    'repo' => $repo,
    'pr_number' => $prNumber,
    'sync_status' => $syncStatus
]);
