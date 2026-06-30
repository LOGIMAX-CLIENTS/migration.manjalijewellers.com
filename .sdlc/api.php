<?php
/**
 * SDLC Pipeline API v1.1 — Multi-Task Support
 * Returns pipeline state as JSON for the dashboard.
 * 
 * Endpoints:
 *   ?action=state           - Registry (all active tasks summary)
 *   ?action=state&task=ID   - Single task's full state
 *   ?action=subtasks        - Sub-task list with progress (requires task= param)
 *   ?action=history         - Archived task history
 *   ?action=context         - Context health estimate
 *   ?action=all             - Everything combined
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Cache-Control: no-cache');

$sdlc_dir = __DIR__;
$state_file = $sdlc_dir . '/pipeline.json';
$history_dir = $sdlc_dir . '/history';
$tasks_dir = $sdlc_dir . '/tasks';
$roles_file = $sdlc_dir . '/roles.json';

$action = isset($_GET['action']) ? $_GET['action'] : 'all';
$task_id = isset($_GET['task']) ? $_GET['task'] : null;

function load_json($path) {
    if (!file_exists($path)) return null;
    $content = file_get_contents($path);
    return json_decode($content, true);
}

function get_registry() {
    global $state_file;
    $data = load_json($state_file);
    if (!$data || !isset($data['active_tasks'])) {
        return ['_version' => '2.0', 'active_tasks' => [], 'default_task' => null];
    }
    return $data;
}

function get_task_state($task_id) {
    global $tasks_dir;
    $path = $tasks_dir . '/' . $task_id . '.json';
    $state = load_json($path);
    if (!$state) {
        return ['phase' => ['current' => 'IDLE'], 'task' => ['id' => null]];
    }
    return $state;
}

function get_all_task_states() {
    $registry = get_registry();
    $states = [];
    foreach ($registry['active_tasks'] as $entry) {
        $tid = $entry['id'];
        $states[$tid] = get_task_state($tid);
    }
    return $states;
}

function get_roles() {
    global $roles_file;
    return load_json($roles_file);
}

function get_subtasks($parent_id) {
    global $tasks_dir;
    $result = [];
    $dir = $tasks_dir . '/' . $parent_id;
    if (!is_dir($dir)) return $result;
    
    foreach (glob($dir . '/*.json') as $file) {
        $data = load_json($file);
        if ($data) $result[] = $data;
    }
    return $result;
}

function get_history() {
    global $history_dir;
    $result = [];
    if (!is_dir($history_dir)) return $result;
    
    $files = glob($history_dir . '/*.json');
    rsort($files); // newest first
    
    foreach (array_slice($files, 0, 20) as $file) {
        $data = load_json($file);
        if ($data) {
            $result[] = [
                'file' => basename($file),
                'task_id' => $data['task']['id'] ?? 'unknown',
                'summary' => $data['task']['summary'] ?? '',
                'type' => $data['task']['type'] ?? '',
                'module' => $data['task']['module'] ?? '',
                'final_phase' => $data['phase']['current'] ?? 'UNKNOWN',
                'created_at' => $data['task']['created_at'] ?? null,
                'updated_at' => $data['_updated_at'] ?? null,
            ];
        }
    }
    return $result;
}

// Build response
$response = [];

if ($action === 'state' || $action === 'all') {
    $response['registry'] = get_registry();
    
    if ($task_id) {
        // Single task detail
        $response['state'] = get_task_state($task_id);
    } else {
        // All task states
        $response['task_states'] = get_all_task_states();
        // Backward compat: provide 'state' as the first active task
        $registry = $response['registry'];
        if (!empty($registry['active_tasks'])) {
            $first_id = $registry['active_tasks'][0]['id'];
            $response['state'] = get_task_state($first_id);
        } else {
            $response['state'] = ['phase' => ['current' => 'IDLE'], 'task' => ['id' => null]];
        }
    }
}

if ($action === 'roles' || $action === 'all') {
    $roles = get_roles();
    if ($roles) {
        $response['roles'] = $roles['roles'] ?? [];
        $response['phase_order'] = $roles['phase_order'] ?? [];
        $response['file_permissions'] = $roles['file_permissions'] ?? [];
    }
}

if ($action === 'subtasks' || $action === 'all') {
    // Need a specific task for subtasks
    $tid = $task_id;
    if (!$tid) {
        $registry = get_registry();
        $tid = $registry['active_tasks'][0]['id'] ?? null;
    }
    
    if ($tid) {
        $state = get_task_state($tid);
        $response['subtasks'] = $state['subtasks'] ?? [];
        $response['subtask_details'] = get_subtasks($tid);
    } else {
        $response['subtasks'] = [];
        $response['subtask_details'] = [];
    }

    // Calculate progress
    $total = count($response['subtasks']);
    $completed = 0;
    foreach ($response['subtasks'] as $st) {
        if (($st['status'] ?? '') === 'completed') $completed++;
    }
    $response['progress'] = [
        'total' => $total,
        'completed' => $completed,
        'percent' => $total > 0 ? round(($completed / $total) * 100) : 0,
    ];
}

if ($action === 'history' || $action === 'all') {
    $response['history'] = get_history();
}

echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
