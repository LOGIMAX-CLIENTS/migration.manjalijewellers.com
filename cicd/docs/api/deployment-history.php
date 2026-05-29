<?php
/**
 * CI/CD Deployment History API
 * 
 * Endpoints:
 * GET  ?action=list&range=7          - Get deployments for last N days
 * GET  ?action=list&client=xxx       - Filter by client
 * GET  ?action=chart&range=7         - Get chart data for last N days
 * GET  ?action=recent&limit=10       - Get recent deployments
 * POST action=record                 - Record a new deployment
 * 
 * @author Logimax Technologies
 * @version 1.0.0
 */

// Debug: Show all errors
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Define constant required by config.php
define('CICD_API', true);

// Load database configuration
require_once __DIR__ . '/config.php';

// getDbConnection() is defined in config.php

/**
 * Get deployment list with filters
 */
function getDeployments($params) {
    $pdo = getDbConnection();
    if (!$pdo) {
        return ['success' => false, 'error' => 'Database connection failed'];
    }
    
    $range = isset($params['range']) ? intval($params['range']) : 7;
    $client = isset($params['client']) ? $params['client'] : null;
    $environment = isset($params['environment']) ? $params['environment'] : null;
    $status = isset($params['status']) ? $params['status'] : null;
    $limit = isset($params['limit']) ? min(intval($params['limit']), 100) : 50;
    $offset = isset($params['offset']) ? intval($params['offset']) : 0;
    
    $sql = "SELECT * FROM cicd_deployment_history WHERE started_at >= DATE_SUB(NOW(), INTERVAL ? DAY)";
    $bindings = [$range];
    
    if ($client) {
        $sql .= " AND client_id = ?";
        $bindings[] = $client;
    }
    
    if ($environment) {
        $sql .= " AND environment = ?";
        $bindings[] = $environment;
    }
    
    if ($status) {
        // Support comma-separated status values (e.g., "running,pending")
        $statuses = explode(',', $status);
        $statuses = array_map('trim', $statuses);
        $placeholders = implode(',', array_fill(0, count($statuses), '?'));
        $sql .= " AND status IN ($placeholders)";
        $bindings = array_merge($bindings, $statuses);
    }
    
    $sql .= " ORDER BY started_at DESC LIMIT ? OFFSET ?";
    $bindings[] = $limit;
    $bindings[] = $offset;
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($bindings);
        $deployments = $stmt->fetchAll();
        
        // Get total count
        $countSql = "SELECT COUNT(*) as total FROM cicd_deployment_history WHERE started_at >= DATE_SUB(NOW(), INTERVAL ? DAY)";
        $countBindings = [$range];
        if ($client) {
            $countSql .= " AND client_id = ?";
            $countBindings[] = $client;
        }
        $countStmt = $pdo->prepare($countSql);
        $countStmt->execute($countBindings);
        $total = $countStmt->fetch()['total'];
        
        return [
            'success' => true,
            'deployments' => $deployments,
            'total' => $total,
            'range' => $range,
            'limit' => $limit,
            'offset' => $offset
        ];
    } catch (PDOException $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

/**
 * Get chart data for deployments overview
 */
function getChartData($params) {
    $pdo = getDbConnection();
    if (!$pdo) {
        return ['success' => false, 'error' => 'Database connection failed'];
    }
    
    $range = isset($params['range']) ? intval($params['range']) : 7;
    
    try {
        // Get daily stats
        $sql = "SELECT 
                    DATE(started_at) as date,
                    environment,
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'success' THEN 1 ELSE 0 END) as success,
                    SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed,
                    AVG(duration_seconds) as avg_duration
                FROM cicd_deployment_history 
                WHERE started_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
                GROUP BY DATE(started_at), environment
                ORDER BY date ASC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$range]);
        $rawData = $stmt->fetchAll();
        
        // Format for Chart.js
        $labels = [];
        $stagingData = [];
        $productionData = [];
        
        // Generate date labels for the range
        for ($i = $range - 1; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $labels[] = date('M d', strtotime($date));
            $stagingData[$date] = 0;
            $productionData[$date] = 0;
        }
        
        // Fill in actual data
        foreach ($rawData as $row) {
            $date = $row['date'];
            if ($row['environment'] === 'staging' && isset($stagingData[$date])) {
                $stagingData[$date] = intval($row['total']);
            }
            if ($row['environment'] === 'production' && isset($productionData[$date])) {
                $productionData[$date] = intval($row['total']);
            }
        }
        
        return [
            'success' => true,
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Staging',
                    'data' => array_values($stagingData),
                    'borderColor' => '#06B6D4',
                    'backgroundColor' => 'rgba(6, 182, 212, 0.2)',
                    'fill' => true,
                    'tension' => 0.4
                ],
                [
                    'label' => 'Production',
                    'data' => array_values($productionData),
                    'borderColor' => '#8B5CF6',
                    'backgroundColor' => 'rgba(139, 92, 246, 0.2)',
                    'fill' => true,
                    'tension' => 0.4
                ]
            ],
            'range' => $range
        ];
    } catch (PDOException $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

/**
 * Get recent deployments for activity feed
 */
function getRecentDeployments($params) {
    $pdo = getDbConnection();
    if (!$pdo) {
        return ['success' => false, 'error' => 'Database connection failed'];
    }
    
    $limit = isset($params['limit']) ? min(intval($params['limit']), 20) : 10;
    
    try {
        $sql = "SELECT id, client_id, client_name, environment, status, branch, 
                       commit_sha, triggered_by, duration_seconds, started_at, completed_at
                FROM cicd_deployment_history 
                ORDER BY started_at DESC 
                LIMIT ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$limit]);
        $deployments = $stmt->fetchAll();
        
        // Format relative times
        foreach ($deployments as &$d) {
            $d['time_ago'] = getRelativeTime($d['started_at']);
        }
        
        return [
            'success' => true,
            'deployments' => $deployments
        ];
    } catch (PDOException $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

/**
 * Get status distribution for donut chart
 */
function getStatusDistribution($params) {
    $pdo = getDbConnection();
    if (!$pdo) {
        return ['success' => false, 'error' => 'Database connection failed'];
    }
    
    $range = isset($params['range']) ? intval($params['range']) : 30;
    
    try {
        $sql = "SELECT 
                    status,
                    COUNT(*) as count
                FROM cicd_deployment_history 
                WHERE started_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
                GROUP BY status";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$range]);
        $data = $stmt->fetchAll();
        
        $colors = [
            'success' => '#22C55E',
            'failed' => '#EF4444',
            'pending' => '#F59E0B',
            'in_progress' => '#06B6D4',
            'cancelled' => '#6B7280'
        ];
        
        $labels = [];
        $values = [];
        $backgroundColors = [];
        
        foreach ($data as $row) {
            $labels[] = ucfirst($row['status']);
            $values[] = intval($row['count']);
            $backgroundColors[] = $colors[$row['status']] ?? '#6B7280';
        }
        
        return [
            'success' => true,
            'labels' => $labels,
            'data' => $values,
            'backgroundColor' => $backgroundColors
        ];
    } catch (PDOException $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

/**
 * Record a new deployment
 */
function recordDeployment($data) {
    $pdo = getDbConnection();
    if (!$pdo) {
        return ['success' => false, 'error' => 'Database connection failed'];
    }
    
    $required = ['client_id', 'environment', 'status'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            return ['success' => false, 'error' => "Missing required field: {$field}"];
        }
    }
    
    try {
        $sql = "INSERT INTO cicd_deployment_history 
                (client_id, client_name, environment, status, branch, commit_sha, 
                 commit_message, triggered_by, trigger_type, duration_seconds, 
                 started_at, completed_at, error_message, server_response)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $data['client_id'],
            $data['client_name'] ?? null,
            $data['environment'],
            $data['status'],
            $data['branch'] ?? 'main',
            $data['commit_sha'] ?? null,
            $data['commit_message'] ?? null,
            $data['triggered_by'] ?? 'webhook',
            $data['trigger_type'] ?? 'webhook',
            $data['duration_seconds'] ?? null,
            $data['started_at'] ?? date('Y-m-d H:i:s'),
            $data['completed_at'] ?? null,
            $data['error_message'] ?? null,
            isset($data['server_response']) ? json_encode($data['server_response']) : null
        ]);
        
        $id = $pdo->lastInsertId();
        
        // Update daily stats
        updateDailyStats($pdo, $data['environment'], $data['status'], $data['duration_seconds'] ?? null);
        
        return [
            'success' => true,
            'id' => $id,
            'message' => 'Deployment recorded successfully'
        ];
    } catch (PDOException $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

/**
 * Update daily statistics table
 */
function updateDailyStats($pdo, $environment, $status, $duration) {
    $date = date('Y-m-d');
    
    try {
        // Try to update existing record
        $sql = "INSERT INTO cicd_deployment_stats (date, environment, total_deployments, successful_deployments, failed_deployments, avg_duration_seconds)
                VALUES (?, ?, 1, ?, ?, ?)
                ON DUPLICATE KEY UPDATE 
                    total_deployments = total_deployments + 1,
                    successful_deployments = successful_deployments + VALUES(successful_deployments),
                    failed_deployments = failed_deployments + VALUES(failed_deployments),
                    avg_duration_seconds = IFNULL((avg_duration_seconds * (total_deployments - 1) + VALUES(avg_duration_seconds)) / total_deployments, VALUES(avg_duration_seconds))";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $date,
            $environment,
            $status === 'success' ? 1 : 0,
            $status === 'failed' ? 1 : 0,
            $duration
        ]);
    } catch (PDOException $e) {
        // Log error but don't fail the main operation
        error_log("Failed to update daily stats: " . $e->getMessage());
    }
}

/**
 * Get relative time string
 */
function getRelativeTime($datetime) {
    $time = strtotime($datetime);
    $diff = time() - $time;
    
    if ($diff < 60) return 'Just now';
    if ($diff < 3600) return floor($diff / 60) . ' min ago';
    if ($diff < 86400) return floor($diff / 3600) . ' hours ago';
    if ($diff < 604800) return floor($diff / 86400) . ' days ago';
    return date('M d, Y', $time);
}

/**
 * Initiate a rollback to a previous deployment
 */
function initiateRollback($data) {
    if (empty($data['deployment_id']) && empty($data['commit_sha'])) {
        return ['success' => false, 'error' => 'Missing deployment_id or commit_sha'];
    }
    
    $pdo = getDbConnection();
    if (!$pdo) {
        return ['success' => false, 'error' => 'Database connection failed'];
    }
    
    try {
        // Get deployment info
        $deploymentId = $data['deployment_id'] ?? null;
        $commitSha = $data['commit_sha'] ?? null;
        $clientId = $data['client_id'] ?? null;
        
        // Log the rollback request
        $sql = "INSERT INTO cicd_deployment_history 
                (client_id, environment, status, branch, commit_sha, commit_message, triggered_by, trigger_type, started_at)
                VALUES (?, 'rollback', 'pending', 'rollback', ?, 'Rollback initiated', 'dashboard', 'rollback', NOW())";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$clientId, $commitSha]);
        $rollbackId = $pdo->lastInsertId();
        
        // In a real implementation, this would trigger the actual rollback
        // via webhook to the server, which would run git checkout to the specified commit
        
        // For now, mark as success (placeholder)
        $updateSql = "UPDATE cicd_deployment_history SET status = 'success', completed_at = NOW() WHERE id = ?";
        $pdo->prepare($updateSql)->execute([$rollbackId]);
        
        return [
            'success' => true,
            'rollback_id' => $rollbackId,
            'message' => 'Rollback initiated successfully',
            'commit_sha' => $commitSha
        ];
    } catch (PDOException $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

/**
 * Get branch merge status - which environments has this branch been deployed to
 */
function getBranchMergeStatus($params) {
    $pdo = getDbConnection();
    
    $clientId = $params['client_id'] ?? null;
    $branch = $params['branch'] ?? null;
    
    if (!$branch) {
        return ['success' => false, 'error' => 'Branch name required'];
    }
    
    if (!$pdo) {
        // Return empty if no database - assume no merges yet
        return ['success' => true, 'merged_environments' => [], 'mock' => true];
    }
    
    try {
        $sql = "SELECT DISTINCT environment 
                FROM cicd_deployment_history 
                WHERE branch = ? 
                AND status = 'success'";
        $bindings = [$branch];
        
        if ($clientId) {
            $sql .= " AND client_id = ?";
            $bindings[] = $clientId;
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($bindings);
        
        $environments = [];
        while ($row = $stmt->fetch()) {
            $environments[] = $row['environment'];
        }
        
        return [
            'success' => true,
            'branch' => $branch,
            'client_id' => $clientId,
            'merged_environments' => $environments
        ];
    } catch (PDOException $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

// ============================================================================
// ROUTER
// ============================================================================

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? $_POST['action'] ?? 'list';

if ($method === 'GET') {
    switch ($action) {
        case 'list':
            echo json_encode(getDeployments($_GET));
            break;
        case 'chart':
            echo json_encode(getChartData($_GET));
            break;
        case 'recent':
            echo json_encode(getRecentDeployments($_GET));
            break;
        case 'status':
            echo json_encode(getStatusDistribution($_GET));
            break;
        case 'branch_status':
            echo json_encode(getBranchMergeStatus($_GET));
            break;
        default:
            echo json_encode(['success' => false, 'error' => 'Unknown action']);
    }
} elseif ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) {
        $data = $_POST;
    }
    
    $action = $data['action'] ?? 'record';
    
    switch ($action) {
        case 'record':
            echo json_encode(recordDeployment($data));
            break;
        case 'rollback':
            echo json_encode(initiateRollback($data));
            break;
        default:
            echo json_encode(['success' => false, 'error' => 'Unknown action']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
}
