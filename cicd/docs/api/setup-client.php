<?php
/**
 * CI/CD API - Client Setup Wizard
 * Handles client registration and GitHub secrets setup
 * 
 * POST /api/setup-client.php
 * - Creates client in database
 * - Sets up GitHub webhook secrets via API
 * - Returns setup instructions
 */

define('CICD_API', true);
require_once __DIR__ . '/config.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Get GitHub token from server config
function getGitHubToken() {
    // Read from server file
    $token_file = '/home/retaillogimaxind/.github_token';
    if (file_exists($token_file)) {
        return trim(file_get_contents($token_file));
    }
    return null;
}

// Set GitHub repository secret
function setGitHubSecret($org, $repo, $secret_name, $secret_value, $token) {
    // Step 1: Get repository public key
    $key_url = "https://api.github.com/repos/$org/$repo/actions/secrets/public-key";
    
    $ch = curl_init($key_url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Accept: application/vnd.github.v3+json',
            "Authorization: Bearer $token",
            'User-Agent: Logimax-CICD'
        ]
    ]);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($http_code !== 200) {
        return ['success' => false, 'error' => 'Failed to get public key', 'code' => $http_code];
    }
    
    $key_data = json_decode($response, true);
    $public_key = $key_data['key'];
    $key_id = $key_data['key_id'];
    
    // Step 2: Encrypt the secret using libsodium
    if (!function_exists('sodium_crypto_box_seal')) {
        return ['success' => false, 'error' => 'libsodium not available'];
    }
    
    $decoded_key = base64_decode($public_key);
    $encrypted = sodium_crypto_box_seal($secret_value, $decoded_key);
    $encrypted_value = base64_encode($encrypted);
    
    // Step 3: Create/update the secret
    $secret_url = "https://api.github.com/repos/$org/$repo/actions/secrets/$secret_name";
    
    $ch = curl_init($secret_url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => 'PUT',
        CURLOPT_POSTFIELDS => json_encode([
            'encrypted_value' => $encrypted_value,
            'key_id' => $key_id
        ]),
        CURLOPT_HTTPHEADER => [
            'Accept: application/vnd.github.v3+json',
            "Authorization: Bearer $token",
            'User-Agent: Logimax-CICD',
            'Content-Type: application/json'
        ]
    ]);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($http_code === 201 || $http_code === 204) {
        return ['success' => true, 'message' => "Secret $secret_name set successfully"];
    }
    
    return ['success' => false, 'error' => 'Failed to set secret', 'code' => $http_code];
}

// Generate webhook secret
function generateSecret($length = 32) {
    return bin2hex(random_bytes($length));
}

try {
    $method = $_SERVER['REQUEST_METHOD'];
    
    if ($method === 'GET') {
        // List all clients
        $stmt = $pdo->query("SELECT * FROM cicd_clients ORDER BY created_at DESC");
        jsonResponse([
            'success' => true,
            'clients' => $stmt->fetchAll()
        ]);
    }
    
    if ($method !== 'POST') {
        jsonResponse(['error' => 'Method not allowed'], 405);
    }
    
    $input = json_decode(file_get_contents('php://input'), true);
    $action = $input['action'] ?? 'create';
    
    switch ($action) {
        case 'create':
            // Validate required fields
            $required = ['client_id', 'client_name', 'repo_org', 'repo_name'];
            foreach ($required as $field) {
                if (empty($input[$field])) {
                    jsonResponse(['error' => "Missing required field: $field"], 400);
                }
            }
            
            // Generate webhook secret
            $webhook_secret = generateSecret();
            // Use custom webhook URL if provided, otherwise fallback to client domain
            $webhook_url = $input['webhook_url'] ?? 'https://' . $input['client_id'] . '/webhooks/deploy.php';
            
            // Insert client
            $stmt = $pdo->prepare("
                INSERT INTO cicd_clients 
                (client_id, client_name, repo_name, folder, branch, deploy_mode, url, webhook_secret, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')
                ON DUPLICATE KEY UPDATE
                    client_name = VALUES(client_name),
                    repo_name = VALUES(repo_name),
                    folder = VALUES(folder)
            ");
            
            $full_repo = $input['repo_org'] . '/' . $input['repo_name'];
            $stmt->execute([
                $input['client_id'],
                $input['client_name'],
                $full_repo,
                $input['folder'] ?? $input['client_id'],
                $input['branch'] ?? 'main',
                $input['deploy_mode'] ?? 'git_pull',
                $input['url'] ?? '',
                $webhook_secret
            ]);
            
            jsonResponse([
                'success' => true,
                'message' => 'Client created',
                'client_id' => $input['client_id'],
                'webhook_url' => $webhook_url,
                'webhook_secret' => $webhook_secret,
                'next_step' => 'setup_secrets'
            ], 201);
            break;
            
        case 'setup_secrets':
            // Set up GitHub secrets
            $client_id = $input['client_id'] ?? '';
            
            // Get client from DB
            $stmt = $pdo->prepare("SELECT * FROM cicd_clients WHERE client_id = ?");
            $stmt->execute([$client_id]);
            $client = $stmt->fetch();
            
            if (!$client) {
                jsonResponse(['error' => 'Client not found'], 404);
            }
            
            // Check if libsodium is available
            if (!function_exists('sodium_crypto_box_seal')) {
                // Get webhook URL from client or use default based on client ID
                $webhook_url = $client['url'] ? $client['url'] . '/webhooks/deploy.php' : 'https://' . explode('/', $client['repo_name'])[1] . '/webhooks/deploy.php';
                
                // Return manual setup instructions
                list($org, $repo) = explode('/', $client['repo_name']);
                jsonResponse([
                    'success' => false,
                    'manual_setup_required' => true,
                    'error' => 'libsodium not available - manual setup required',
                    'instructions' => [
                        'Go to: https://github.com/' . $client['repo_name'] . '/settings/secrets/actions',
                        'Click "New repository secret"',
                        'Add WEBHOOK_URL with value: ' . $webhook_url,
                        'Add WEBHOOK_SECRET with value: ' . $client['webhook_secret']
                    ],
                    'secrets' => [
                        'WEBHOOK_URL' => $webhook_url,
                        'WEBHOOK_SECRET' => $client['webhook_secret']
                    ],
                    'github_url' => 'https://github.com/' . $client['repo_name'] . '/settings/secrets/actions'
                ]);
            }
            
            $github_token = getGitHubToken();
            if (!$github_token) {
                jsonResponse(['error' => 'GitHub token not configured on server'], 500);
            }
            
            list($org, $repo) = explode('/', $client['repo_name']);
            
            // Get webhook URL from client config
            $webhook_url = $client['url'] ? $client['url'] . '/webhooks/deploy.php' : 'https://' . $repo . '/webhooks/deploy.php';
            
            // Set WEBHOOK_URL secret
            $url_result = setGitHubSecret(
                $org, $repo, 
                'WEBHOOK_URL', 
                $webhook_url,
                $github_token
            );
            
            // Set WEBHOOK_SECRET
            $secret_result = setGitHubSecret(
                $org, $repo,
                'WEBHOOK_SECRET',
                $client['webhook_secret'],
                $github_token
            );
            
            if ($url_result['success'] && $secret_result['success']) {
                // Update client status
                $pdo->prepare("UPDATE cicd_clients SET status = 'active' WHERE client_id = ?")->execute([$client_id]);
                
                jsonResponse([
                    'success' => true,
                    'message' => 'GitHub secrets configured successfully',
                    'results' => [
                        'WEBHOOK_URL' => $url_result,
                        'WEBHOOK_SECRET' => $secret_result
                    ]
                ]);
            } else {
                jsonResponse([
                    'success' => false,
                    'error' => 'Failed to set some secrets',
                    'results' => [
                        'WEBHOOK_URL' => $url_result,
                        'WEBHOOK_SECRET' => $secret_result
                    ]
                ], 500);
            }
            break;
            
        case 'verify':
            // Verify client setup
            $client_id = $input['client_id'] ?? '';
            
            $stmt = $pdo->prepare("SELECT * FROM cicd_clients WHERE client_id = ?");
            $stmt->execute([$client_id]);
            $client = $stmt->fetch();
            
            if (!$client) {
                jsonResponse(['error' => 'Client not found'], 404);
            }
            
            jsonResponse([
                'success' => true,
                'client' => $client,
                'setup_complete' => $client['status'] === 'active'
            ]);
            break;
            
        default:
            jsonResponse(['error' => 'Unknown action'], 400);
    }
    
} catch (Exception $e) {
    jsonResponse(['error' => $e->getMessage()], 500);
}
