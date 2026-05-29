<?php
/**
 * CI/CD Dashboard - Sync History API
 * Tracks sync operations with full audit trail
 * 
 * Endpoints:
 * GET  ?action=list&limit=50         - Recent sync history
 * GET  ?action=client&id=X           - History for specific client
 * GET  ?action=pending               - Get pending/syncing operations
 * POST ?action=create                - Log new sync start
 * POST ?action=update                - Update sync status
 */

define('CICD_API', true);
require_once __DIR__ . '/config.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-GitHub-Event, X-Hub-Signature-256');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

try {
    $action = $_GET['action'] ?? '';
    $pdo = getDbConnection();
    
    // Auto-create table if not exists (may fail if user lacks CREATE permission)
    try {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS cicd_sync_history (
                id INT AUTO_INCREMENT PRIMARY KEY,
                client_id VARCHAR(100) NOT NULL,
                client_name VARCHAR(150),
                source_branch VARCHAR(50) NOT NULL,
                target_branch VARCHAR(50) NOT NULL,
                commit_sha VARCHAR(40),
                triggered_by VARCHAR(50),
                status ENUM('pending','syncing','success','failed','conflict') DEFAULT 'pending',
                pr_number INT,
                pr_url VARCHAR(255),
                error_message TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                completed_at TIMESTAMP NULL,
                INDEX idx_client (client_id),
                INDEX idx_status (status),
                INDEX idx_created (created_at DESC)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    } catch (Exception $e) {
        // Ignore - table may already exist or user lacks CREATE permission
        // User should create table manually via phpMyAdmin if needed
    }

    switch ($action) {
        // ========================================
        // GET: Recent sync history
        // ========================================
        case 'list':
            $limit = min((int)($_GET['limit'] ?? 50), 100);
            $offset = (int)($_GET['offset'] ?? 0);
            $status = $_GET['status'] ?? null;
            
            $sql = "SELECT * FROM cicd_sync_history";
            $params = [];
            
            if ($status && in_array($status, ['pending','syncing','success','failed','conflict'])) {
                $sql .= " WHERE status = ?";
                $params[] = $status;
            }
            
            $sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $history = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get total count
            $countSql = "SELECT COUNT(*) FROM cicd_sync_history";
            if ($status) {
                $countSql .= " WHERE status = ?";
                $countStmt = $pdo->prepare($countSql);
                $countStmt->execute([$status]);
            } else {
                $countStmt = $pdo->query($countSql);
            }
            $total = $countStmt->fetchColumn();
            
            jsonResponse([
                'success' => true,
                'history' => $history,
                'total' => (int)$total,
                'limit' => $limit,
                'offset' => $offset
            ]);
            break;
            
        // ========================================
        // GET: History for specific client
        // ========================================
        case 'client':
            $clientId = $_GET['id'] ?? '';
            if (!$clientId) {
                jsonResponse(['error' => 'Client ID required'], 400);
            }
            
            $limit = min((int)($_GET['limit'] ?? 20), 50);
            
            $stmt = $pdo->prepare("
                SELECT * FROM cicd_sync_history 
                WHERE client_id = ? 
                ORDER BY created_at DESC 
                LIMIT ?
            ");
            $stmt->execute([$clientId, $limit]);
            $history = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            jsonResponse([
                'success' => true,
                'client_id' => $clientId,
                'history' => $history
            ]);
            break;
            
        // ========================================
        // GET: Pending/syncing operations
        // ========================================
        case 'pending':
            $stmt = $pdo->query("
                SELECT * FROM cicd_sync_history 
                WHERE status IN ('pending', 'syncing') 
                ORDER BY created_at DESC
            ");
            $pending = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            jsonResponse([
                'success' => true,
                'pending' => $pending,
                'count' => count($pending)
            ]);
            break;
            
        // ========================================
        // GET: Stats summary
        // ========================================
        case 'stats':
            $days = min((int)($_GET['days'] ?? 7), 30);
            
            $stmt = $pdo->prepare("
                SELECT 
                    status,
                    COUNT(*) as count
                FROM cicd_sync_history 
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
                GROUP BY status
            ");
            $stmt->execute([$days]);
            $statusCounts = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
            
            $stmt = $pdo->prepare("
                SELECT COUNT(DISTINCT client_id) 
                FROM cicd_sync_history 
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
            ");
            $stmt->execute([$days]);
            $uniqueClients = $stmt->fetchColumn();
            
            jsonResponse([
                'success' => true,
                'period_days' => $days,
                'status_counts' => $statusCounts,
                'unique_clients' => (int)$uniqueClients,
                'total' => array_sum($statusCounts)
            ]);
            break;
            
        // ========================================
        // POST: Create new sync record
        // ========================================
        case 'create':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                jsonResponse(['error' => 'Method not allowed'], 405);
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            
            $required = ['client_id', 'source_branch', 'target_branch'];
            foreach ($required as $field) {
                if (empty($input[$field])) {
                    jsonResponse(['error' => "Missing required field: $field"], 400);
                }
            }
            
            // Validate status if provided
            $status = $input['status'] ?? 'pending';
            $validStatus = ['pending', 'syncing', 'success', 'failed', 'conflict'];
            if (!in_array($status, $validStatus)) {
                $status = 'pending';
            }
            
            $stmt = $pdo->prepare("
                INSERT INTO cicd_sync_history 
                (client_id, client_name, source_branch, target_branch, commit_sha, triggered_by, status, pr_number, pr_url, completed_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            // Set completed_at for terminal statuses
            $completedAt = in_array($status, ['success', 'failed', 'conflict']) ? date('Y-m-d H:i:s') : null;
            
            $stmt->execute([
                $input['client_id'],
                $input['client_name'] ?? null,
                $input['source_branch'],
                $input['target_branch'],
                $input['commit_sha'] ?? null,
                $input['triggered_by'] ?? 'dashboard',
                $status,
                !empty($input['pr_number']) ? (int)$input['pr_number'] : null,
                $input['pr_url'] ?? null,
                $completedAt
            ]);
            
            $id = $pdo->lastInsertId();
            
            jsonResponse([
                'success' => true,
                'id' => (int)$id,
                'message' => 'Sync record created'
            ]);
            break;
            
        // ========================================
        // POST: Update sync status
        // ========================================
        case 'update':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                jsonResponse(['error' => 'Method not allowed'], 405);
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            
            // Can update by ID or by client_id (latest pending)
            $id = $input['id'] ?? null;
            $clientId = $input['client_id'] ?? null;
            
            if (!$id && !$clientId) {
                jsonResponse(['error' => 'ID or client_id required'], 400);
            }
            
            // Build update
            $updates = [];
            $params = [];
            
            if (isset($input['status'])) {
                $validStatus = ['pending', 'syncing', 'success', 'failed', 'conflict'];
                if (!in_array($input['status'], $validStatus)) {
                    jsonResponse(['error' => 'Invalid status'], 400);
                }
                $updates[] = 'status = ?';
                $params[] = $input['status'];
                
                // Auto-set completed_at for terminal states
                if (in_array($input['status'], ['success', 'failed', 'conflict'])) {
                    $updates[] = 'completed_at = NOW()';
                }
            }
            
            if (isset($input['pr_number'])) {
                $updates[] = 'pr_number = ?';
                $params[] = $input['pr_number'];
            }
            
            if (isset($input['pr_url'])) {
                $updates[] = 'pr_url = ?';
                $params[] = $input['pr_url'];
            }
            
            if (isset($input['commit_sha'])) {
                $updates[] = 'commit_sha = ?';
                $params[] = $input['commit_sha'];
            }
            
            if (isset($input['error_message'])) {
                $updates[] = 'error_message = ?';
                $params[] = $input['error_message'];
            }
            
            if (empty($updates)) {
                jsonResponse(['error' => 'No fields to update'], 400);
            }
            
            $sql = "UPDATE cicd_sync_history SET " . implode(', ', $updates);
            
            if ($id) {
                $sql .= " WHERE id = ?";
                $params[] = $id;
            } else {
                // Update latest pending/syncing for this client
                $sql .= " WHERE client_id = ? AND status IN ('pending', 'syncing') ORDER BY created_at DESC LIMIT 1";
                $params[] = $clientId;
            }
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            
            jsonResponse([
                'success' => true,
                'affected' => $stmt->rowCount(),
                'message' => 'Sync record updated'
            ]);
            break;
            
        default:
            jsonResponse([
                'error' => 'Invalid action',
                'valid_actions' => ['list', 'client', 'pending', 'stats', 'create', 'update']
            ], 400);
    }
    
} catch (Exception $e) {
    jsonResponse(['error' => $e->getMessage()], 500);
}
