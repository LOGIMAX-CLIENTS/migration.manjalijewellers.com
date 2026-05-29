<?php
/**
 * CI/CD API - Environments Endpoint
 * GET /api/environments.php - List all environments
 */

define('CICD_API', true);
require_once __DIR__ . '/config.php';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

try {
    if (isset($_GET['name'])) {
        $stmt = $pdo->prepare("SELECT * FROM cicd_environments WHERE name = ?");
        $stmt->execute([$_GET['name']]);
        $env = $stmt->fetch();
        
        if (!$env) {
            jsonResponse(['error' => 'Environment not found'], 404);
        }
        jsonResponse($env);
    } else {
        $stmt = $pdo->query("
            SELECT e.*, 
                   (SELECT COUNT(*) FROM cicd_deployments d WHERE d.environment = e.name) as deploy_count
            FROM cicd_environments e 
            ORDER BY FIELD(e.name, 'Develop', 'QA', 'Support', 'Production')
        ");
        jsonResponse([
            'success' => true,
            'count' => $stmt->rowCount(),
            'environments' => $stmt->fetchAll()
        ]);
    }
} catch (Exception $e) {
    jsonResponse(['error' => $e->getMessage()], 500);
}
