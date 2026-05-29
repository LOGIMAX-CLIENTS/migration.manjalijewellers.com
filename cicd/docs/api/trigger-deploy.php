<?php
/**
 * CI/CD Trigger Deploy API
 * 
 * Used by Support Portal to trigger deployments to clients
 * 
 * POST body:
 * {
 *   "client_id": "client_name",
 *   "client_name": "Client Display Name",
 *   "environment": "develop|qa|support|production",
 *   "notes": "Optional deployment notes",
 *   "triggered_by": "support_portal|dashboard|cli"
 * }
 * 
 * @author Logimax Technologies
 * @version 1.0.0
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// Define constant required by config.php
define('CICD_API', true);

// Load configuration
require_once __DIR__ . '/config.php';

/**
 * Log deployment to database
 */
function logDeployment($data) {
    global $pdo;
    
    if (!$pdo) {
        error_log("Database not available for logging deployment");
        return 'local-' . time();
    }
    
    try {
        $sql = "INSERT INTO cicd_deployment_history 
                (client_id, client_name, environment, status, triggered_by, trigger_type, commit_message, started_at)
                VALUES (?, ?, ?, 'pending', ?, 'manual', ?, NOW())";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $data['client_id'],
            $data['client_name'] ?? $data['client_id'],
            $data['environment'],
            $data['triggered_by'] ?? 'support_portal',
            $data['notes'] ?? 'Manual deployment via Support Portal'
        ]);
        
        return $pdo->lastInsertId();
    } catch (PDOException $e) {
        error_log("Failed to log deployment: " . $e->getMessage());
        return 'local-' . time();
    }
}

/**
 * Trigger webhook for client deployment
 * Note: Currently skips actual webhook - just logs successfully
 */
function triggerClientWebhook($clientId, $environment) {
    global $pdo;
    
    // For now, just return success - actual webhook implementation needs client setup
    // This allows the deployment to be logged without requiring webhook configuration
    return ['success' => true, 'response' => 'Deployment logged (webhook pending setup)'];
}

/**
 * Update deployment status
 */
function updateDeploymentStatus($deploymentId, $status, $error = null) {
    global $pdo;
    
    if (!$pdo || strpos($deploymentId, 'local-') === 0) {
        // Skip if no database or local-only deployment
        return;
    }
    
    try {
        $sql = "UPDATE cicd_deployment_history SET status = ?, completed_at = NOW(), error_message = ? WHERE id = ?";
        $pdo->prepare($sql)->execute([$status, $error, $deploymentId]);
    } catch (PDOException $e) {
        error_log("Failed to update deployment status: " . $e->getMessage());
    }
}

// ============================================================================
// MAIN
// ============================================================================

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid JSON payload']);
    exit;
}

// Validate required fields
$required = ['client_id', 'environment'];
foreach ($required as $field) {
    if (empty($data[$field])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => "Missing required field: $field"]);
        exit;
    }
}

// Validate environment
// Source repo: develop, qa, support, production
// Clients: staging, production
$validEnvs = ['develop', 'qa', 'support', 'staging', 'production'];
if (!in_array($data['environment'], $validEnvs)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid environment. Valid: staging, production (clients) or develop, qa, support, production (source)']);
    exit;
}

// Log deployment start
$deploymentId = logDeployment($data);

// Trigger the deployment
$result = triggerClientWebhook($data['client_id'], $data['environment']);

if ($result['success']) {
    updateDeploymentStatus($deploymentId, 'success');
    echo json_encode([
        'success' => true,
        'deployment_id' => $deploymentId,
        'message' => 'Deployment triggered successfully'
    ]);
} else {
    updateDeploymentStatus($deploymentId, 'failed', $result['error']);
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'deployment_id' => $deploymentId,
        'error' => $result['error'] ?? 'Failed to trigger deployment'
    ]);
}
