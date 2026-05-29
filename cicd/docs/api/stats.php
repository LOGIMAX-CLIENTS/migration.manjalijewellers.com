<?php
/**
 * CI/CD API - Dashboard Statistics
 * 
 * Endpoints:
 * GET ?action=overview     - Overall dashboard stats
 * GET ?action=sparklines   - Mini chart data for stat cards
 * GET ?action=weekly       - Weekly summary
 * GET ?action=clients      - Client status distribution
 * 
 * @author Logimax Technologies
 * @version 2.0.0
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Define constant required by config.php
define('CICD_API', true);

require_once __DIR__ . '/config.php';

// getDbConnection() is now defined in config.php

/**
 * Get overview statistics for dashboard cards
 */
function getOverviewStats() {
    $pdo = getDbConnection();
    
    if (!$pdo) {
        // Return mock data if no database
        return getMockOverviewStats();
    }
    
    try {
        // Total deployments (last 30 days)
        $total = $pdo->query("
            SELECT COUNT(*) FROM cicd_deployment_history 
            WHERE started_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        ")->fetchColumn();
        
        // Previous period for comparison
        $prevTotal = $pdo->query("
            SELECT COUNT(*) FROM cicd_deployment_history 
            WHERE started_at BETWEEN DATE_SUB(NOW(), INTERVAL 60 DAY) AND DATE_SUB(NOW(), INTERVAL 30 DAY)
        ")->fetchColumn();
        
        // Success rate
        $success = $pdo->query("
            SELECT COUNT(*) FROM cicd_deployment_history 
            WHERE status = 'success' AND started_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        ")->fetchColumn();
        
        $successRate = $total > 0 ? round(($success / $total) * 100, 1) : 0;
        
        // Average deployment time
        $avgTime = $pdo->query("
            SELECT AVG(duration_seconds) FROM cicd_deployment_history 
            WHERE duration_seconds IS NOT NULL AND started_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        ")->fetchColumn();
        
        // Active clients count
        $activeClients = $pdo->query("
            SELECT COUNT(DISTINCT client_id) FROM cicd_deployment_history 
            WHERE started_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        ")->fetchColumn();
        
        // Calculate percentage changes
        $totalChange = $prevTotal > 0 ? round((($total - $prevTotal) / $prevTotal) * 100) : 0;
        
        return [
            'success' => true,
            'stats' => [
                'total_deployments' => [
                    'value' => formatNumber($total),
                    'raw' => (int)$total,
                    'change' => $totalChange,
                    'trend' => $totalChange >= 0 ? 'up' : 'down'
                ],
                'success_rate' => [
                    'value' => $successRate . '%',
                    'raw' => $successRate,
                    'change' => 0,
                    'trend' => 'up'
                ],
                'avg_time' => [
                    'value' => formatDuration($avgTime),
                    'raw' => round($avgTime, 0),
                    'change' => 0,
                    'trend' => 'down'
                ],
                'active_clients' => [
                    'value' => (int)$activeClients,
                    'raw' => (int)$activeClients,
                    'change' => 0,
                    'trend' => 'up'
                ]
            ]
        ];
    } catch (PDOException $e) {
        return getMockOverviewStats();
    }
}

/**
 * Get sparkline data for stat cards (7-day trend)
 */
function getSparklineData() {
    $pdo = getDbConnection();
    
    if (!$pdo) {
        return getMockSparklines();
    }
    
    try {
        $sql = "SELECT 
                    DATE(started_at) as date,
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'success' THEN 1 ELSE 0 END) as success,
                    AVG(duration_seconds) as avg_duration
                FROM cicd_deployment_history 
                WHERE started_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                GROUP BY DATE(started_at)
                ORDER BY date ASC";
        
        $stmt = $pdo->query($sql);
        $data = $stmt->fetchAll();
        
        // Build arrays for each metric
        $totals = [];
        $successRates = [];
        $durations = [];
        
        // Fill in all 7 days
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $totals[$date] = 0;
            $successRates[$date] = 0;
            $durations[$date] = 0;
        }
        
        foreach ($data as $row) {
            $date = $row['date'];
            if (isset($totals[$date])) {
                $totals[$date] = (int)$row['total'];
                $successRates[$date] = $row['total'] > 0 ? round($row['success'] / $row['total'] * 100) : 0;
                $durations[$date] = round($row['avg_duration'] ?? 0);
            }
        }
        
        return [
            'success' => true,
            'sparklines' => [
                'deployments' => array_values($totals),
                'success_rate' => array_values($successRates),
                'duration' => array_values($durations),
                'clients' => [5, 6, 5, 7, 8, 6, 7] // Mock for now
            ]
        ];
    } catch (PDOException $e) {
        return getMockSparklines();
    }
}

/**
 * Get weekly summary
 */
function getWeeklySummary() {
    $pdo = getDbConnection();
    
    if (!$pdo) {
        return [
            'success' => true,
            'summary' => [
                'total' => 156,
                'success' => 142,
                'failed' => 14,
                'avg_per_day' => 22.3,
                'busiest_day' => 'Wednesday'
            ]
        ];
    }
    
    try {
        $stats = $pdo->query("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'success' THEN 1 ELSE 0 END) as success,
                SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed,
                AVG(duration_seconds) as avg_duration
            FROM cicd_deployment_history 
            WHERE started_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        ")->fetch();
        
        $byDay = $pdo->query("
            SELECT 
                DAYNAME(started_at) as day_name,
                COUNT(*) as count
            FROM cicd_deployment_history 
            WHERE started_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            GROUP BY DAYNAME(started_at)
            ORDER BY count DESC
            LIMIT 1
        ")->fetch();
        
        return [
            'success' => true,
            'summary' => [
                'total' => (int)$stats['total'],
                'success' => (int)$stats['success'],
                'failed' => (int)$stats['failed'],
                'avg_per_day' => round($stats['total'] / 7, 1),
                'busiest_day' => $byDay['day_name'] ?? 'N/A'
            ]
        ];
    } catch (PDOException $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

/**
 * Get client status distribution
 */
function getClientDistribution() {
    $pdo = getDbConnection();
    
    if (!$pdo) {
        return [
            'success' => true,
            'distribution' => [
                ['status' => 'Completed', 'count' => 45, 'color' => '#22C55E'],
                ['status' => 'In Progress', 'count' => 12, 'color' => '#F59E0B'],
                ['status' => 'Pending', 'count' => 23, 'color' => '#6B7280']
            ]
        ];
    }
    
    try {
        // This would query the cicd_clients table
        $data = $pdo->query("
            SELECT status, COUNT(*) as count 
            FROM cicd_clients 
            GROUP BY status
        ")->fetchAll();
        
        $colors = [
            'active' => '#22C55E',
            'pending' => '#F59E0B',
            'inactive' => '#6B7280',
            'error' => '#EF4444'
        ];
        
        $distribution = [];
        foreach ($data as $row) {
            $distribution[] = [
                'status' => ucfirst($row['status']),
                'count' => (int)$row['count'],
                'color' => $colors[$row['status']] ?? '#6B7280'
            ];
        }
        
        return ['success' => true, 'distribution' => $distribution];
    } catch (PDOException $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

/**
 * Format large numbers (e.g., 1000 -> 1k)
 */
function formatNumber($num) {
    if ($num >= 1000) {
        return round($num / 1000, 1) . 'k+';
    }
    return (string)$num;
}

/**
 * Format duration in human readable format
 */
function formatDuration($seconds) {
    if (!$seconds) return '0s';
    
    if ($seconds < 60) {
        return round($seconds) . 's';
    }
    
    $minutes = floor($seconds / 60);
    $secs = $seconds % 60;
    
    if ($minutes < 60) {
        return $minutes . 'm ' . round($secs) . 's';
    }
    
    $hours = floor($minutes / 60);
    $mins = $minutes % 60;
    return $hours . 'h ' . $mins . 'm';
}

/**
 * Mock data for when database is not available
 */
function getMockOverviewStats() {
    return [
        'success' => true,
        'mock' => true,
        'stats' => [
            'total_deployments' => [
                'value' => '2.4k+',
                'raw' => 2400,
                'change' => 17,
                'trend' => 'up'
            ],
            'success_rate' => [
                'value' => '94.2%',
                'raw' => 94.2,
                'change' => 3,
                'trend' => 'up'
            ],
            'avg_time' => [
                'value' => '45s',
                'raw' => 45,
                'change' => -8,
                'trend' => 'down'
            ],
            'active_clients' => [
                'value' => '82',
                'raw' => 82,
                'change' => 5,
                'trend' => 'up'
            ]
        ]
    ];
}

function getMockSparklines() {
    return [
        'success' => true,
        'mock' => true,
        'sparklines' => [
            'deployments' => [45, 52, 38, 65, 48, 72, 58],
            'success_rate' => [92, 95, 88, 97, 94, 96, 93],
            'duration' => [42, 38, 55, 45, 40, 35, 48],
            'clients' => [5, 6, 5, 7, 8, 6, 7]
        ]
    ];
}

/**
 * Get top clients by various metrics
 */
function getTopClients() {
    $pdo = getDbConnection();
    
    if (!$pdo) {
        return [
            'success' => true,
            'mock' => true,
            'top_clients' => [
                'most_deploys' => ['name' => 'Demo Client', 'count' => '25'],
                'fastest_avg' => ['name' => 'Demo Client', 'avg_time' => '32s'],
                'best_success' => ['name' => 'Demo Client', 'success_rate' => '100%']
            ]
        ];
    }
    
    try {
        // Most deploys in last 7 days
        $mostDeploys = $pdo->query("
            SELECT client_name, COUNT(*) as deploy_count 
            FROM cicd_deployment_history 
            WHERE started_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) 
            AND client_name IS NOT NULL AND client_name != ''
            GROUP BY client_name 
            ORDER BY deploy_count DESC 
            LIMIT 1
        ")->fetch();
        
        // Fastest average deploy time
        $fastestAvg = $pdo->query("
            SELECT client_name, AVG(duration_seconds) as avg_time 
            FROM cicd_deployment_history 
            WHERE started_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) 
            AND duration_seconds IS NOT NULL 
            AND client_name IS NOT NULL AND client_name != ''
            GROUP BY client_name 
            ORDER BY avg_time ASC 
            LIMIT 1
        ")->fetch();
        
        // Best success rate
        $bestSuccess = $pdo->query("
            SELECT client_name, 
                   COUNT(*) as total,
                   SUM(CASE WHEN status = 'success' THEN 1 ELSE 0 END) as success_count
            FROM cicd_deployment_history 
            WHERE started_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            AND client_name IS NOT NULL AND client_name != ''
            GROUP BY client_name 
            HAVING total >= 3
            ORDER BY (success_count / total) DESC 
            LIMIT 1
        ")->fetch();
        
        return [
            'success' => true,
            'top_clients' => [
                'most_deploys' => [
                    'name' => $mostDeploys['client_name'] ?? 'N/A',
                    'count' => $mostDeploys['deploy_count'] ?? 0
                ],
                'fastest_avg' => [
                    'name' => $fastestAvg['client_name'] ?? 'N/A',
                    'avg_time' => $fastestAvg['avg_time'] ? round($fastestAvg['avg_time']) . 's' : 'N/A'
                ],
                'best_success' => [
                    'name' => $bestSuccess['client_name'] ?? 'N/A',
                    'success_rate' => $bestSuccess['total'] > 0 
                        ? round(($bestSuccess['success_count'] / $bestSuccess['total']) * 100) . '%' 
                        : 'N/A'
                ]
            ]
        ];
    } catch (PDOException $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

/**
 * Get environment distribution for polar chart
 */
function getEnvironmentDistribution() {
    $pdo = getDbConnection();
    
    if (!$pdo) {
        return [
            'success' => true,
            'mock' => true,
            'distribution' => [
                'labels' => ['Develop', 'QA', 'Support', 'Production'],
                'data' => [45, 25, 20, 10],
                'colors' => ['#7c3aed', '#f59e0b', '#22c55e', '#ef4444']
            ]
        ];
    }
    
    try {
        $data = $pdo->query("
            SELECT environment, COUNT(*) as count 
            FROM cicd_deployment_history 
            WHERE started_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            GROUP BY environment
        ")->fetchAll();
        
        $envColors = [
            'develop' => '#7c3aed',
            'qa' => '#f59e0b', 
            'support' => '#22c55e',
            'staging' => '#06b6d4',
            'production' => '#ef4444'
        ];
        
        $labels = [];
        $values = [];
        $colors = [];
        
        foreach ($data as $row) {
            $labels[] = ucfirst($row['environment']);
            $values[] = (int)$row['count'];
            $colors[] = $envColors[$row['environment']] ?? '#6b7280';
        }
        
        return [
            'success' => true,
            'distribution' => [
                'labels' => $labels,
                'data' => $values,
                'colors' => $colors
            ]
        ];
    } catch (PDOException $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

// ============================================================================
// ROUTER
// ============================================================================

$action = $_GET['action'] ?? 'overview';

switch ($action) {
    case 'overview':
        echo json_encode(getOverviewStats());
        break;
    case 'sparklines':
        echo json_encode(getSparklineData());
        break;
    case 'weekly':
        echo json_encode(getWeeklySummary());
        break;
    case 'clients':
        echo json_encode(getClientDistribution());
        break;
    case 'top_clients':
        echo json_encode(getTopClients());
        break;
    case 'environment_distribution':
        echo json_encode(getEnvironmentDistribution());
        break;
    default:
        echo json_encode(['success' => false, 'error' => 'Unknown action']);
}

