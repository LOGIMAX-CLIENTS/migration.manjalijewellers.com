<?php
/**
 * CI/CD Dashboard - Database Configuration
 * Reads database credentials directly without triggering CodeIgniter
 */

// Prevent direct access
if (!defined('CICD_API')) {
    header('HTTP/1.1 403 Forbidden');
    header('Content-Type: application/json');
    exit(json_encode(['error' => 'Direct access not allowed']));
}

// Read database config by parsing the file (avoid CI's BASEPATH check)
// Detect environment and use correct path
if (strpos(__DIR__, '/home/retaillogimaxind') !== false) {
    // Production server
    $config_path = '/home/retaillogimaxind/public_html/develop/admin/application/config/database.php';
} else {
    // Local development
    $config_path = dirname(__DIR__, 3) . '/admin/application/config/database.php';
}

$DB_HOST = 'localhost';
$DB_PORT = 3306;
$DB_NAME = '';
$DB_USER = 'root';
$DB_PASS = '';

if (file_exists($config_path)) {
    $content = file_get_contents($config_path);
    
    // Extract hostname
    if (preg_match("/\['hostname'\]\s*=\s*['\"]([^'\"]+)['\"]/", $content, $m)) {
        $DB_HOST = $m[1];
    }
    // Extract database
    if (preg_match("/\['database'\]\s*=\s*['\"]([^'\"]+)['\"]/", $content, $m)) {
        $DB_NAME = $m[1];
    }
    // Extract username
    if (preg_match("/\['username'\]\s*=\s*['\"]([^'\"]+)['\"]/", $content, $m)) {
        $DB_USER = $m[1];
    }
    // Extract password (can be empty)
    if (preg_match("/\['password'\]\s*=\s*['\"]([^'\"]*)['\"]/", $content, $m)) {
        $DB_PASS = $m[1];
    }
} else {
    // Config not found - $pdo will be null, APIs should handle gracefully
}

// Parse host and port (e.g., "localhost:3307")
if (strpos($DB_HOST, ':') !== false) {
    list($DB_HOST, $DB_PORT) = explode(':', $DB_HOST);
}

try {
    $pdo = new PDO(
        "mysql:host=$DB_HOST;port=$DB_PORT;dbname=$DB_NAME;charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'error' => 'Database connection failed',
        'debug' => $e->getMessage()
    ]);
    exit;
}

// Helper function
function jsonResponse($data, $code = 200) {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

// Get database connection (re-usable)
function getDbConnection() {
    global $pdo;
    return $pdo;
}

// Registry path constant
define('REGISTRY_PATH', strpos(__DIR__, '/home/retaillogimaxind') !== false 
    ? '/home/retaillogimaxind/public_html/develop/clients/registry.json'
    : dirname(__DIR__, 2) . '/clients/registry.json'
);
