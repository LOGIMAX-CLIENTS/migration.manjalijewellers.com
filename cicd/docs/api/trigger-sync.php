<?php
/**
 * CI/CD Dashboard - Trigger Sync API
 * Triggers GitHub workflow to sync source to clients
 * 
 * POST /api/trigger-sync.php
 * Body: {
 *   "clients": ["repo1", "repo2"],
 *   "source_branch": "Production",
 *   "target_branch": "staging",
 *   "auto_merge": true
 * }
 */

// Start session with proper settings
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 86400,
        'path' => '/',
        'secure' => isset($_SERVER['HTTPS']),
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

define('CICD_API', true);
require_once __DIR__ . '/config.php';

// CORS headers
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if ($origin) {
    header('Access-Control-Allow-Origin: ' . $origin);
} else {
    header('Access-Control-Allow-Origin: *');
}
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Get GitHub token
function getGitHubToken() {
    $tokenFile = '/home/retaillogimaxind/.github_token';
    if (file_exists($tokenFile)) {
        return trim(file_get_contents($tokenFile));
    }
    return null;
}

// Trigger workflow via GitHub API
function triggerWorkflow($token, $clients, $sourceBranch, $targetBranch, $autoMerge = true) {
    $url = 'https://api.github.com/repos/Logimax-Technologies/etail_development_src/actions/workflows/sync-batch.yml/dispatches';
    
    // The ref must be a branch that contains the workflow file
    // Default branch is Retail_1.1.1.0001 where .github/workflows are located
    $workflowRef = 'Retail_1.1.1.0001';
    
    $data = [
        'ref' => $workflowRef,
        'inputs' => [
            'clients' => implode(',', $clients),
            'source_branch' => $sourceBranch,
            'target_branch' => $targetBranch,
            'auto_merge' => $autoMerge ? 'true' : 'false'
        ]
    ];
    
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => [
            'Accept: application/vnd.github.v3+json',
            "Authorization: Bearer $token",
            'User-Agent: Logimax-CICD',
            'Content-Type: application/json'
        ]
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'success' => $httpCode === 204, // GitHub returns 204 No Content on success
        'http_code' => $httpCode,
        'response' => $response
    ];
}

// Get latest workflow runs
function getWorkflowRuns($token, $limit = 5) {
    $url = 'https://api.github.com/repos/Logimax-Technologies/etail_development_src/actions/workflows/sync-batch.yml/runs?per_page=' . $limit;
    
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Accept: application/vnd.github.v3+json',
            "Authorization: Bearer $token",
            'User-Agent: Logimax-CICD'
        ]
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200) {
        return json_decode($response, true);
    }
    
    return null;
}

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        jsonResponse(['error' => 'Method not allowed'], 405);
    }
    
    $token = getGitHubToken();
    if (!$token) {
        jsonResponse([
            'success' => false,
            'error' => 'GitHub token not configured',
            'manual_trigger' => true,
            'instructions' => 'Go to GitHub Actions and manually run the sync-batch workflow'
        ], 500);
    }
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    $clients = $input['clients'] ?? [];
    $sourceBranch = $input['source_branch'] ?? 'Production';
    $targetBranch = $input['target_branch'] ?? 'staging';
    $autoMerge = $input['auto_merge'] ?? true;
    
    // Validate clients
    if (empty($clients)) {
        jsonResponse(['error' => 'At least one client required'], 400);
    }
    
    if (count($clients) > 10) {
        jsonResponse(['error' => 'Maximum 10 clients per batch'], 400);
    }
    
    // Validate branches
    $validTargetBranches = ['staging', 'STAGING', 'production', 'main', 'master'];
    
    // Source branch validation removed to allow any branch (e.g. feature branches)
    
    if (!in_array($targetBranch, $validTargetBranches)) {
        jsonResponse(['error' => 'Invalid target branch. Must be: ' . implode(', ', $validTargetBranches)], 400);
    }
    
    // Trigger the workflow
    $result = triggerWorkflow($token, $clients, $sourceBranch, $targetBranch, $autoMerge);
    
    if ($result['success']) {
        // Log this action
        try {
            $stmt = $pdo->prepare("
                INSERT INTO cicd_deployment_history 
                (client_id, client_name, environment, branch, status, triggered_by, trigger_type, started_at)
                VALUES (?, ?, 'sync', ?, 'pending', ?, 'dashboard_sync', NOW())
            ");
            
            $triggeredBy = $_SESSION['cicd_user']['username'] ?? 'dashboard';
            
            foreach ($clients as $client) {
                $stmt->execute([
                    $client,
                    $client,
                    $sourceBranch,
                    $triggeredBy
                ]);
            }
        } catch (Exception $e) {
            // Log error but don't fail
        }
        
        jsonResponse([
            'success' => true,
            'message' => 'Sync workflow triggered successfully',
            'clients' => $clients,
            'source_branch' => $sourceBranch,
            'target_branch' => $targetBranch,
            'auto_merge' => $autoMerge,
            'actions_url' => 'https://github.com/Logimax-Technologies/etail_development_src/actions/workflows/sync-batch.yml'
        ]);
    } else {
        jsonResponse([
            'success' => false,
            'error' => 'Failed to trigger workflow',
            'http_code' => $result['http_code'],
            'response' => $result['response'],
            'manual_trigger' => true,
            'instructions' => 'Go to GitHub Actions and manually run the sync-batch workflow'
        ], 500);
    }
    
} catch (Exception $e) {
    jsonResponse(['error' => $e->getMessage()], 500);
}
