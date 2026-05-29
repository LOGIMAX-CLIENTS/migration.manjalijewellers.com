<?php
/**
 * CI/CD API - GitHub Integration
 * Fetch repositories from GitHub organization
 * 
 * GET /api/github-repos.php - List all repos in org
 * GET /api/github-repos.php?repo=name - Get specific repo details
 */

define('CICD_API', true);
require_once __DIR__ . '/config.php';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Get GitHub token
function getGitHubToken() {
    $token_file = '/home/retaillogimaxind/.github_token';
    if (file_exists($token_file)) {
        return trim(file_get_contents($token_file));
    }
    return null;
}

// GitHub API request
function githubRequest($url, $token) {
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
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'code' => $http_code,
        'data' => json_decode($response, true)
    ];
}

try {
    $token = getGitHubToken();
    
    if (!$token) {
        jsonResponse(['error' => 'GitHub token not configured on server'], 500);
    }
    
    $org = $_GET['org'] ?? 'Logimax-Technologies';
    $repo = $_GET['repo'] ?? null;
    $action = $_GET['action'] ?? null;
    
    // Action: List branches for a repository
    if ($action === 'branches' && $repo) {
        $result = githubRequest("https://api.github.com/repos/$org/$repo/branches?per_page=100", $token);
        
        if ($result['code'] !== 200) {
            jsonResponse(['success' => false, 'error' => 'Failed to fetch branches', 'code' => $result['code']], 500);
        }
        
        $branches = array_map(function($b) {
            return [
                'name' => $b['name'],
                'sha' => $b['commit']['sha'] ?? null,
                'protected' => $b['protected'] ?? false
            ];
        }, $result['data'] ?? []);
        
        jsonResponse([
            'success' => true,
            'repository' => "$org/$repo",
            'branches' => $branches,
            'count' => count($branches)
        ]);
    }
    
    if ($repo) {
        // Get specific repository details
        $result = githubRequest("https://api.github.com/repos/$org/$repo", $token);
        
        if ($result['code'] !== 200) {
            jsonResponse(['error' => 'Repository not found'], 404);
        }
        
        $repo_data = $result['data'];
        
        // Get branches
        $branches_result = githubRequest("https://api.github.com/repos/$org/$repo/branches?per_page=100", $token);
        
        // Get existing secrets (can only check if they exist, not read values)
        $secrets_result = githubRequest("https://api.github.com/repos/$org/$repo/actions/secrets", $token);
        
        $existing_secrets = [];
        if ($secrets_result['code'] === 200 && isset($secrets_result['data']['secrets'])) {
            foreach ($secrets_result['data']['secrets'] as $secret) {
                $existing_secrets[] = $secret['name'];
            }
        }
        
        jsonResponse([
            'success' => true,
            'repository' => [
                'name' => $repo_data['name'],
                'full_name' => $repo_data['full_name'],
                'description' => $repo_data['description'],
                'default_branch' => $repo_data['default_branch'],
                'private' => $repo_data['private'],
                'html_url' => $repo_data['html_url'],
                'clone_url' => $repo_data['clone_url'],
                'ssh_url' => $repo_data['ssh_url'],
                'created_at' => $repo_data['created_at'],
                'updated_at' => $repo_data['updated_at'],
                'pushed_at' => $repo_data['pushed_at']
            ],
            'branches' => array_map(function($b) {
                return $b['name'];
            }, $branches_result['data'] ?? []),
            'existing_secrets' => $existing_secrets,
            'has_webhook_configured' => in_array('WEBHOOK_URL', $existing_secrets) && in_array('WEBHOOK_SECRET', $existing_secrets)
        ]);
        
    } else {
        // List all repositories in organization
        $all_repos = [];
        $page = 1;
        $per_page = 100;
        
        do {
            $result = githubRequest(
                "https://api.github.com/orgs/$org/repos?per_page=$per_page&page=$page&sort=updated",
                $token
            );
            
            if ($result['code'] !== 200) {
                // Try user repos if org fails
                $result = githubRequest(
                    "https://api.github.com/users/$org/repos?per_page=$per_page&page=$page&sort=updated",
                    $token
                );
            }
            
            if ($result['code'] !== 200) {
                jsonResponse(['error' => 'Failed to fetch repositories', 'code' => $result['code']], 500);
            }
            
            $repos = $result['data'];
            
            foreach ($repos as $repo) {
                $all_repos[] = [
                    'name' => $repo['name'],
                    'full_name' => $repo['full_name'],
                    'description' => $repo['description'],
                    'default_branch' => $repo['default_branch'],
                    'private' => $repo['private'],
                    'updated_at' => $repo['updated_at'],
                    'html_url' => $repo['html_url']
                ];
            }
            
            $page++;
        } while (count($repos) === $per_page && $page <= 5); // Max 5 pages = 500 repos
        
        // Check which repos are already set up as clients
        $stmt = $pdo->query("SELECT repo_name, status FROM cicd_clients");
        $existing = [];
        while ($row = $stmt->fetch()) {
            $existing[$row['repo_name']] = $row['status'];
        }
        
        // Add setup status to repos
        foreach ($all_repos as &$repo) {
            $repo['setup_status'] = $existing[$repo['full_name']] ?? 'not_configured';
        }
        
        jsonResponse([
            'success' => true,
            'organization' => $org,
            'count' => count($all_repos),
            'repositories' => $all_repos
        ]);
    }
    
} catch (Exception $e) {
    jsonResponse(['error' => $e->getMessage()], 500);
}
