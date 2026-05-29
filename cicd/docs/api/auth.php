<?php
/**
 * CI/CD Authentication API - File-based (no database required)
 * 
 * Stores users in a JSON file to avoid database permission issues
 * 
 * @author Logimax Technologies
 * @version 1.1.0
 */


set_exception_handler(function($e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'PHP Exception', 'debug' => $e->getMessage()]);
    exit;
});

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

header('Content-Type: application/json');
// CORS headers - use specific origin for credentials to work
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if ($origin) {
    header('Access-Control-Allow-Origin: ' . $origin);
} else {
    header('Access-Control-Allow-Origin: *');
}
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Credentials: true');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Version check to verify correct file is deployed
if (isset($_GET['version'])) {
    echo json_encode(['version' => '1.2.0-file-based', 'database' => false, 'time' => date('c')]);
    exit;
}

// Users file path
$USERS_FILE = __DIR__ . '/users.json';

/**
 * Get users from file, create default if not exists
 */
function getUsers() {
    global $USERS_FILE;
    
    if (!file_exists($USERS_FILE)) {
        // Create default users
        $defaultUsers = [
            'admin' => [
                'password_hash' => password_hash('admin123', PASSWORD_DEFAULT),
                'display_name' => 'Administrator',
                'role' => 'admin',
                'is_active' => true
            ],
            'support' => [
                'password_hash' => password_hash('support123', PASSWORD_DEFAULT),
                'display_name' => 'Support Team',
                'role' => 'deployer',
                'is_active' => true
            ],
            'viewer' => [
                'password_hash' => password_hash('viewer123', PASSWORD_DEFAULT),
                'display_name' => 'Viewer',
                'role' => 'viewer',
                'is_active' => true
            ]
        ];
        
        file_put_contents($USERS_FILE, json_encode($defaultUsers, JSON_PRETTY_PRINT));
        return $defaultUsers;
    }
    
    return json_decode(file_get_contents($USERS_FILE), true) ?: [];
}

/**
 * Login user
 */
function login($data) {
    $username = trim($data['username'] ?? '');
    $password = $data['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        return ['success' => false, 'error' => 'Username and password required'];
    }
    
    $users = getUsers();
    
    if (!isset($users[$username])) {
        logAuth($username, 'login_failed', 'User not found');
        return ['success' => false, 'error' => 'Invalid username or password'];
    }
    
    $user = $users[$username];
    
    if (!$user['is_active']) {
        return ['success' => false, 'error' => 'Account is disabled'];
    }
    
    if (!password_verify($password, $user['password_hash'])) {
        logAuth($username, 'login_failed', 'Wrong password');
        return ['success' => false, 'error' => 'Invalid username or password'];
    }
    
    // Create session
    $_SESSION['cicd_user'] = $username;
    $_SESSION['cicd_role'] = $user['role'];
    $_SESSION['cicd_display_name'] = $user['display_name'];
    $_SESSION['cicd_login_time'] = time();
    
    logAuth($username, 'login_success');
    
    return [
        'success' => true,
        'user' => [
            'username' => $username,
            'display_name' => $user['display_name'],
            'role' => $user['role']
        ]
    ];
}

/**
 * Logout user
 */
function logout() {
    $username = $_SESSION['cicd_user'] ?? 'unknown';
    logAuth($username, 'logout');
    
    // Unset all session variables
    $_SESSION = array();
    
    // Delete the session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // Destroy the session
    session_destroy();
    
    return ['success' => true, 'message' => 'Logged out'];
}


/**
 * Check if logged in
 */
function checkAuth() {
    if (isset($_SESSION['cicd_user'])) {
        return [
            'success' => true,
            'authenticated' => true,
            'user' => [
                'username' => $_SESSION['cicd_user'],
                'role' => $_SESSION['cicd_role'],
                'display_name' => $_SESSION['cicd_display_name'] ?? ''
            ]
        ];
    }
    return ['success' => true, 'authenticated' => false];
}

/**
 * Get current user
 */
function getCurrentUser() {
    if (!isset($_SESSION['cicd_user'])) {
        return ['success' => false, 'error' => 'Not authenticated'];
    }
    
    $users = getUsers();
    $username = $_SESSION['cicd_user'];
    
    if (!isset($users[$username])) {
        session_destroy();
        return ['success' => false, 'error' => 'User not found'];
    }
    
    $user = $users[$username];
    return [
        'success' => true,
        'user' => [
            'username' => $username,
            'display_name' => $user['display_name'],
            'role' => $user['role']
        ]
    ];
}

/**
 * Simple file-based auth log
 */
function logAuth($username, $event, $detail = '') {
    $logFile = __DIR__ . '/auth.log';
    $line = date('Y-m-d H:i:s') . " | $event | $username | " . ($_SERVER['REMOTE_ADDR'] ?? '-') . " | $detail\n";
    @file_put_contents($logFile, $line, FILE_APPEND);
}

// ============================================================================
// ROUTER - Only run when auth.php is called directly
// ============================================================================

// Skip router if included by another file
if (basename($_SERVER['SCRIPT_FILENAME']) !== 'auth.php') {
    return;
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) $data = $_POST;
    
    $action = $data['action'] ?? 'login';
    
    switch ($action) {
        case 'login':
            echo json_encode(login($data));
            break;
        case 'logout':
            echo json_encode(logout());
            break;
        default:
            echo json_encode(['success' => false, 'error' => 'Unknown action']);
    }
} elseif ($method === 'GET') {
    $action = $_GET['action'] ?? 'check';
    
    switch ($action) {
        case 'check':
            echo json_encode(checkAuth());
            break;
        case 'user':
            echo json_encode(getCurrentUser());
            break;
        default:
            echo json_encode(['success' => false, 'error' => 'Unknown action']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
}
