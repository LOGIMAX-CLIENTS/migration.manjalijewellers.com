<?php
/**
 * CI/CD API - Deployments Endpoint
 * GET /api/deployments.php - List deployments
 * POST /api/deployments.php - Log new deployment
 */

define('CICD_API', true);
require_once __DIR__ . '/config.php';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        $environment = $_GET['environment'] ?? null;
        $limit = min((int)($_GET['limit'] ?? 50), 100);
        
        $sql = "SELECT * FROM cicd_deployments";
        $params = [];
        
        if ($environment) {
            $sql .= " WHERE environment = ?";
            $params[] = $environment;
        }
        $sql .= " ORDER BY created_at DESC LIMIT ?";
        $params[] = $limit;
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        jsonResponse([
            'success' => true,
            'deployments' => $stmt->fetchAll()
        ]);
        
    } elseif ($method === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            jsonResponse(['error' => 'Invalid JSON'], 400);
        }
        
        $stmt = $pdo->prepare("
            INSERT INTO cicd_deployments 
            (environment, branch, commit_hash, commit_message, deployed_by, status, deploy_mode)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $input['environment'] ?? 'Unknown',
            $input['branch'] ?? 'Unknown',
            $input['commit_hash'] ?? null,
            $input['commit_message'] ?? null,
            $input['deployed_by'] ?? 'webhook',
            $input['status'] ?? 'success',
            $input['deploy_mode'] ?? 'git_pull'
        ]);
        
        jsonResponse(['success' => true, 'id' => $pdo->lastInsertId()], 201);
    }
    
} catch (Exception $e) {
    jsonResponse(['error' => $e->getMessage()], 500);
}
