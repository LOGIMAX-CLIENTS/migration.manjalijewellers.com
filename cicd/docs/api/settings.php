<?php
/**
 * CI/CD Dashboard - Settings API
 * Manages users, permissions, and data cleanup
 * 
 * Endpoints:
 * GET  ?action=users          - List all users
 * GET  ?action=permissions    - Get current user permissions
 * GET  ?action=settings       - Get all settings
 * POST ?action=user_add       - Add new user
 * POST ?action=user_update    - Update user role/status
 * POST ?action=clear_history  - Clear old deployment data
 * POST ?action=reset_all      - Truncate tables (admin only)
 */

// Start session first with proper settings
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
// Note: Don't include auth.php - it has router that we don't want

// CORS headers
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if ($origin) {
    header('Access-Control-Allow-Origin: ' . $origin);
} else {
    header('Access-Control-Allow-Origin: *');
}
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Get current user from session (auth.php stores these)
function getSessionUser() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['cicd_user'])) {
        return null;
    }
    return [
        'username' => $_SESSION['cicd_user'],
        'role' => $_SESSION['cicd_role'] ?? 'viewer',
        'display_name' => $_SESSION['cicd_display_name'] ?? ''
    ];
}

// Check if user has admin role
function isAdmin() {
    $user = getSessionUser();
    return $user && $user['role'] === 'admin';
}

// Get feature permissions from settings
function getFeaturePermissions($pdo) {
    try {
        $stmt = $pdo->prepare("SELECT setting_value FROM cicd_settings WHERE setting_key = 'feature_permissions'");
        $stmt->execute();
        $result = $stmt->fetch();
        if ($result) {
            return json_decode($result['setting_value'], true);
        }
    } catch (Exception $e) {
        // Return default permissions
    }
    
    return [
        'admin' => ['admin_dashboard', 'support_dashboard', 'sync_manager_full', 'sync_manager_limited', 'manage_clients', 'settings', 'clear_data', 'view_history_full'],
        'support' => ['support_dashboard', 'sync_manager_limited', 'view_history_limited'],
        'viewer' => ['support_dashboard', 'view_history_limited']
    ];
}

// Get a specific setting value
function getSetting($pdo, $key, $default = null) {
    try {
        $stmt = $pdo->prepare("SELECT setting_value FROM cicd_settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        $result = $stmt->fetch();
        return $result ? $result['setting_value'] : $default;
    } catch (Exception $e) {
        return $default;
    }
}

try {
    $action = $_GET['action'] ?? '';
    
    switch ($action) {
        // ========================================
        // GET: List all users (admin only)
        // ========================================
        case 'users':
            if (!isAdmin()) {
                jsonResponse(['error' => 'Unauthorized'], 403);
            }
            
            // Read from users.json file (same as auth.php)
            $usersFile = __DIR__ . '/users.json';
            $users = [];
            
            if (file_exists($usersFile)) {
                $usersData = json_decode(file_get_contents($usersFile), true) ?: [];
                $id = 1;
                foreach ($usersData as $username => $userData) {
                    $users[] = [
                        'id' => $id++,
                        'username' => $username,
                        'display_name' => $userData['display_name'] ?? $username,
                        'email' => $userData['email'] ?? '',
                        'role' => $userData['role'] ?? 'viewer',
                        'is_active' => $userData['is_active'] ? 1 : 0,
                        'last_login' => $userData['last_login'] ?? null,
                        'created_at' => $userData['created_at'] ?? null
                    ];
                }
            }
            
            jsonResponse([
                'success' => true,
                'users' => $users
            ]);
            break;
            
        // ========================================
        // GET: Current user permissions
        // ========================================
        case 'permissions':
            $user = getCurrentUser();
            if (!$user) {
                jsonResponse(['error' => 'Not authenticated'], 401);
            }
            
            $permissions = getFeaturePermissions($pdo);
            $role = $user['role'] ?? 'viewer';
            $userPermissions = $permissions[$role] ?? $permissions['viewer'];
            
            jsonResponse([
                'success' => true,
                'user' => [
                    'username' => $user['username'],
                    'role' => $role,
                    'display_name' => $user['display_name'] ?? $user['username']
                ],
                'permissions' => $userPermissions,
                'can_access' => [
                    'admin_dashboard' => in_array('admin_dashboard', $userPermissions),
                    'support_dashboard' => in_array('support_dashboard', $userPermissions),
                    'sync_manager_full' => in_array('sync_manager_full', $userPermissions),
                    'sync_manager_limited' => in_array('sync_manager_limited', $userPermissions),
                    'manage_clients' => in_array('manage_clients', $userPermissions),
                    'settings' => in_array('settings', $userPermissions),
                    'clear_data' => in_array('clear_data', $userPermissions)
                ]
            ]);
            break;
            
        // ========================================
        // GET: All settings (admin only)
        // ========================================
        case 'settings':
            if (!isAdmin()) {
                jsonResponse(['error' => 'Unauthorized'], 403);
            }
            
            $stmt = $pdo->query("SELECT * FROM cicd_settings ORDER BY setting_key");
            $settings = $stmt->fetchAll();
            
            jsonResponse([
                'success' => true,
                'settings' => $settings
            ]);
            break;
            
        // ========================================
        // POST: Add new user (admin only)
        // ========================================
        case 'user_add':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                jsonResponse(['error' => 'Method not allowed'], 405);
            }
            if (!isAdmin()) {
                jsonResponse(['error' => 'Unauthorized'], 403);
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            $username = trim($input['username'] ?? '');
            $password = $input['password'] ?? '';
            $displayName = $input['display_name'] ?? $username;
            $role = $input['role'] ?? 'viewer';
            
            if (empty($username) || empty($password)) {
                jsonResponse(['error' => 'Username and password required'], 400);
            }
            
            if (!in_array($role, ['admin', 'support', 'deployer', 'viewer'])) {
                jsonResponse(['error' => 'Invalid role'], 400);
            }
            
            // Read and update users.json file
            $usersFile = __DIR__ . '/users.json';
            $users = file_exists($usersFile) ? json_decode(file_get_contents($usersFile), true) : [];
            
            if (isset($users[$username])) {
                jsonResponse(['error' => 'Username already exists'], 400);
            }
            
            $users[$username] = [
                'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                'display_name' => $displayName,
                'role' => $role,
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));
            
            jsonResponse([
                'success' => true,
                'message' => "User '$username' created successfully"
            ]);
            break;
            
        // ========================================
        // POST: Update user (admin only)
        // ========================================
        case 'user_update':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                jsonResponse(['error' => 'Method not allowed'], 405);
            }
            if (!isAdmin()) {
                jsonResponse(['error' => 'Unauthorized'], 403);
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            $username = $input['username'] ?? '';
            
            if (empty($username)) {
                jsonResponse(['error' => 'Username required'], 400);
            }
            
            // Read users.json file
            $usersFile = __DIR__ . '/users.json';
            $users = file_exists($usersFile) ? json_decode(file_get_contents($usersFile), true) : [];
            
            if (!isset($users[$username])) {
                jsonResponse(['error' => 'User not found'], 404);
            }
            
            // Update fields
            if (isset($input['role']) && in_array($input['role'], ['admin', 'support', 'deployer', 'viewer'])) {
                $users[$username]['role'] = $input['role'];
            }
            if (isset($input['is_active'])) {
                $users[$username]['is_active'] = (bool)$input['is_active'];
            }
            if (isset($input['display_name'])) {
                $users[$username]['display_name'] = $input['display_name'];
            }
            if (!empty($input['password'])) {
                $users[$username]['password_hash'] = password_hash($input['password'], PASSWORD_DEFAULT);
            }
            
            file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));
            
            jsonResponse([
                'success' => true,
                'message' => 'User updated successfully'
            ]);
            break;
            
        // ========================================
        // POST: Clear old deployment history (admin only)
        // ========================================
        case 'clear_history':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                jsonResponse(['error' => 'Method not allowed'], 405);
            }
            if (!isAdmin()) {
                jsonResponse(['error' => 'Unauthorized'], 403);
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            $days = intval($input['days'] ?? 30);
            
            $stmt = $pdo->prepare("DELETE FROM cicd_deployment_history WHERE started_at < DATE_SUB(NOW(), INTERVAL ? DAY)");
            $stmt->execute([$days]);
            $deletedHistory = $stmt->rowCount();
            
            $stmt = $pdo->prepare("DELETE FROM cicd_deployments WHERE created_at < DATE_SUB(NOW(), INTERVAL ? DAY)");
            $stmt->execute([$days]);
            $deletedDeployments = $stmt->rowCount();
            
            $stmt = $pdo->prepare("DELETE FROM cicd_auth_log WHERE created_at < DATE_SUB(NOW(), INTERVAL ? DAY)");
            $stmt->execute([$days]);
            $deletedAuthLog = $stmt->rowCount();
            
            jsonResponse([
                'success' => true,
                'message' => "Cleared data older than $days days",
                'deleted' => [
                    'deployment_history' => $deletedHistory,
                    'deployments' => $deletedDeployments,
                    'auth_log' => $deletedAuthLog
                ]
            ]);
            break;
            
        // ========================================
        // POST: Reset all data (admin only, dangerous!)
        // ========================================
        case 'reset_all':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                jsonResponse(['error' => 'Method not allowed'], 405);
            }
            if (!isAdmin()) {
                jsonResponse(['error' => 'Unauthorized'], 403);
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            if (($input['confirm'] ?? '') !== 'RESET_ALL_DATA') {
                jsonResponse(['error' => 'Confirmation required: send confirm="RESET_ALL_DATA"'], 400);
            }
            
            // Use DELETE instead of TRUNCATE because shared hosting often restricts DROP privilege
            // TRUNCATE requires DROP privilege, DELETE only requires DELETE privilege
            // Clear all transaction/entry tables, keep masters (users, clients, environments, settings)
            $pdo->exec("DELETE FROM cicd_deployment_history");
            $pdo->exec("DELETE FROM cicd_deployments");
            $pdo->exec("DELETE FROM cicd_deployment_stats");
            $pdo->exec("DELETE FROM cicd_sync_history");
            $pdo->exec("DELETE FROM cicd_auth_log");
            
            jsonResponse([
                'success' => true,
                'message' => 'All deployment and sync history data has been reset',
                'tables_cleared' => ['cicd_deployment_history', 'cicd_deployments', 'cicd_deployment_stats', 'cicd_sync_history', 'cicd_auth_log'],
                'tables_preserved' => ['cicd_users', 'cicd_clients', 'cicd_environments', 'cicd_settings'],
                'warning' => 'This action cannot be undone'
            ]);
            break;
            
        // ========================================
        // POST: Update a setting (admin only)
        // ========================================
        case 'update_setting':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                jsonResponse(['error' => 'Method not allowed'], 405);
            }
            if (!isAdmin()) {
                jsonResponse(['error' => 'Unauthorized'], 403);
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            $key = $input['key'] ?? '';
            $value = $input['value'] ?? '';
            
            if (empty($key)) {
                jsonResponse(['error' => 'Setting key required'], 400);
            }
            
            $user = getCurrentUser();
            $stmt = $pdo->prepare("
                INSERT INTO cicd_settings (setting_key, setting_value, updated_by) 
                VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), updated_by = VALUES(updated_by)
            ");
            $stmt->execute([$key, $value, $user['username'] ?? 'admin']);
            
            jsonResponse([
                'success' => true,
                'message' => "Setting '$key' updated"
            ]);
            break;
            
        default:
            jsonResponse(['error' => 'Invalid action', 'valid_actions' => [
                'users', 'permissions', 'settings', 'user_add', 'user_update', 
                'clear_history', 'reset_all', 'update_setting'
            ]], 400);
    }
    
} catch (Exception $e) {
    jsonResponse(['error' => $e->getMessage()], 500);
}
