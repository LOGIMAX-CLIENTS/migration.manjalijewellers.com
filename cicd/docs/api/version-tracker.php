<?php
/**
 * CI/CD Dashboard - Version Tracker API
 * Tracks client sync versions and status
 * 
 * Endpoints:
 * GET  ?action=status               - Get all clients with version status
 * GET  ?action=compare&branch=X     - Compare clients to source branch
 * GET  ?action=client&id=X          - Get single client details
 * POST ?action=update               - Update client sync status
 * GET  ?action=branches             - Get source branches from GitHub
 */

define('CICD_API', true);
require_once __DIR__ . '/config.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Path to registry.json - use constant from config
$registryPath = defined('REGISTRY_PATH') ? REGISTRY_PATH : dirname(__DIR__, 2) . '/clients/registry.json';

// Load registry
function loadRegistry($path) {
    if (!file_exists($path)) {
        return null;
    }
    return json_decode(file_get_contents($path), true);
}

// Save registry
function saveRegistry($path, $data) {
    return file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
}

// Get GitHub branches via API
function getGitHubBranches($token = null) {
    $tokenFile = '/home/retaillogimaxind/.github_token';
    if (!$token && file_exists($tokenFile)) {
        $token = trim(file_get_contents($tokenFile));
    }
    
    if (!$token) {
        // Return default branches if no token
        return ['Production', 'QA', 'support', 'Retail_1.1.1.0001'];
    }
    
    $url = 'https://api.github.com/repos/Logimax-Technologies/etail_development_src/branches?per_page=100';
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
        $branches = json_decode($response, true);
        return array_column($branches, 'name');
    }
    
    return ['Production', 'QA', 'support', 'Retail_1.1.1.0001'];
}

// Get latest commit SHA for a branch
function getLatestCommit($branch, $token = null) {
    $tokenFile = '/home/retaillogimaxind/.github_token';
    if (!$token && file_exists($tokenFile)) {
        $token = trim(file_get_contents($tokenFile));
    }
    
    if (!$token) {
        return null;
    }
    
    $url = "https://api.github.com/repos/Logimax-Technologies/etail_development_src/commits/$branch";
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
        $data = json_decode($response, true);
        return [
            'sha' => $data['sha'],
            'short_sha' => substr($data['sha'], 0, 7),
            'message' => $data['commit']['message'] ?? '',
            'date' => $data['commit']['committer']['date'] ?? null
        ];
    }
    
    return null;
}

try {
    $action = $_GET['action'] ?? '';
    $registry = loadRegistry($registryPath);
    
    if (!$registry) {
        jsonResponse(['error' => 'Registry file not found'], 500);
    }
    
    switch ($action) {
        // ========================================
        // GET: All clients with version status
        // ========================================
        case 'status':
            $clients = $registry['clients'] ?? [];
            
            $stats = [
                'total' => count($clients),
                'synced' => 0,
                'outdated' => 0,
                'pending_merge' => 0,
                'not_synced' => 0,
                'pinned' => 0
            ];
            
            $clientList = [];
            foreach ($clients as $client) {
                $status = $client['sync_status'] ?? 'not_synced';
                $policy = $client['version_policy'] ?? 'latest';
                
                if ($status === 'synced') $stats['synced']++;
                elseif ($status === 'outdated') $stats['outdated']++;
                elseif ($status === 'pending_merge') $stats['pending_merge']++;
                else $stats['not_synced']++;
                
                if ($policy === 'pinned') $stats['pinned']++;
                
                $clientList[] = [
                    'id' => $client['id'],
                    'name' => $client['name'],
                    'repo' => $client['repo'],
                    'url' => $client['url'] ?? '',
                    'source_branch' => $client['source_branch'] ?? null,
                    'last_sync_sha' => $client['last_sync_sha'] ?? null,
                    'last_sync_date' => $client['last_sync_date'] ?? null,
                    'sync_status' => $status,
                    'version_policy' => $policy
                ];
            }
            
            jsonResponse([
                'success' => true,
                'stats' => $stats,
                'clients' => $clientList
            ]);
            break;
            
        // ========================================
        // GET: Compare clients to source branch
        // ========================================
        case 'compare':
            $sourceBranch = $_GET['branch'] ?? 'Production';
            $latestCommit = getLatestCommit($sourceBranch);
            
            $clients = $registry['clients'] ?? [];
            $comparison = [];
            
            foreach ($clients as $client) {
                $clientSha = $client['last_sync_sha'] ?? null;
                $isOutdated = $latestCommit && $clientSha && $clientSha !== $latestCommit['sha'];
                $isSynced = $latestCommit && $clientSha && $clientSha === $latestCommit['sha'];
                
                $comparison[] = [
                    'id' => $client['id'],
                    'name' => $client['name'],
                    'repo' => $client['repo'],
                    'client_sha' => $clientSha ? substr($clientSha, 0, 7) : null,
                    'latest_sha' => $latestCommit ? $latestCommit['short_sha'] : null,
                    'is_outdated' => $isOutdated,
                    'is_synced' => $isSynced,
                    'version_policy' => $client['version_policy'] ?? 'latest',
                    'last_sync_date' => $client['last_sync_date'] ?? null
                ];
            }
            
            jsonResponse([
                'success' => true,
                'source_branch' => $sourceBranch,
                'latest_commit' => $latestCommit,
                'clients' => $comparison
            ]);
            break;
            
        // ========================================
        // GET: Single client details
        // ========================================
        case 'client':
            $clientId = $_GET['id'] ?? '';
            if (!$clientId) {
                jsonResponse(['error' => 'Client ID required'], 400);
            }
            
            $clients = $registry['clients'] ?? [];
            $found = null;
            foreach ($clients as $client) {
                if ($client['id'] === $clientId || $client['repo'] === $clientId) {
                    $found = $client;
                    break;
                }
            }
            
            if (!$found) {
                jsonResponse(['error' => 'Client not found'], 404);
            }
            
            jsonResponse([
                'success' => true,
                'client' => $found
            ]);
            break;
            
        // ========================================
        // POST: Update client sync status
        // ========================================
        case 'update':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                jsonResponse(['error' => 'Method not allowed'], 405);
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            $clientId = $input['client_id'] ?? '';
            
            if (!$clientId) {
                jsonResponse(['error' => 'Client ID required'], 400);
            }
            
            $clients = &$registry['clients'];
            $updated = false;
            
            foreach ($clients as &$client) {
                if ($client['id'] === $clientId || $client['repo'] === $clientId) {
                    // Update version tracking fields
                    if (isset($input['source_branch'])) {
                        $client['source_branch'] = $input['source_branch'];
                    }
                    if (isset($input['last_sync_sha'])) {
                        $client['last_sync_sha'] = $input['last_sync_sha'];
                    }
                    if (isset($input['sync_status'])) {
                        $client['sync_status'] = $input['sync_status'];
                    }
                    if (isset($input['version_policy'])) {
                        $client['version_policy'] = $input['version_policy'];
                    }
                    
                    // Always update sync date on update
                    $client['last_sync_date'] = date('c');
                    
                    $updated = true;
                    break;
                }
            }
            
            if (!$updated) {
                jsonResponse(['error' => 'Client not found'], 404);
            }
            
            // Save registry
            if (!saveRegistry($registryPath, $registry)) {
                jsonResponse(['error' => 'Failed to save registry'], 500);
            }
            
            jsonResponse([
                'success' => true,
                'message' => "Client '$clientId' updated"
            ]);
            break;
            
        // ========================================
        // POST: Bulk update clients
        // ========================================
        case 'bulk_update':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                jsonResponse(['error' => 'Method not allowed'], 405);
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            $updates = $input['updates'] ?? [];
            
            if (empty($updates)) {
                jsonResponse(['error' => 'No updates provided'], 400);
            }
            
            $clients = &$registry['clients'];
            $updatedCount = 0;
            
            foreach ($updates as $update) {
                $clientId = $update['client_id'] ?? '';
                foreach ($clients as &$client) {
                    if ($client['id'] === $clientId || $client['repo'] === $clientId) {
                        if (isset($update['source_branch'])) $client['source_branch'] = $update['source_branch'];
                        if (isset($update['last_sync_sha'])) $client['last_sync_sha'] = $update['last_sync_sha'];
                        if (isset($update['sync_status'])) $client['sync_status'] = $update['sync_status'];
                        if (isset($update['version_policy'])) $client['version_policy'] = $update['version_policy'];
                        $client['last_sync_date'] = date('c');
                        $updatedCount++;
                        break;
                    }
                }
            }
            
            if (!saveRegistry($registryPath, $registry)) {
                jsonResponse(['error' => 'Failed to save registry'], 500);
            }
            
            jsonResponse([
                'success' => true,
                'message' => "Updated $updatedCount clients"
            ]);
            break;
            
        // ========================================
        // GET: Source branches from GitHub
        // ========================================
        case 'branches':
            $branches = getGitHubBranches();
            
            jsonResponse([
                'success' => true,
                'branches' => $branches
            ]);
            break;
            
        default:
            jsonResponse(['error' => 'Invalid action', 'valid_actions' => [
                'status', 'compare', 'client', 'update', 'bulk_update', 'branches'
            ]], 400);
    }
    
} catch (Exception $e) {
    jsonResponse(['error' => $e->getMessage()], 500);
}
