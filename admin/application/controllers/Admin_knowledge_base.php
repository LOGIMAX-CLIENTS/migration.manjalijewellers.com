<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Admin_knowledge_base extends CI_Controller {

    private $kb_root;

    function __construct() {
        parent::__construct();
        
        if (!$this->session->userdata('is_logged')) {
            redirect('admin/login');
        }

        $this->kb_root = realpath(FCPATH . '../knowledge_base');
        
        if ($this->input->get('debug') == 1) {
            echo "<pre>";
            echo "FCPATH: " . FCPATH . "\n";
            echo "KB_ROOT Raw: " . FCPATH . '../knowledge_base' . "\n";
            echo "KB_ROOT Real: " . $this->kb_root . "\n";
            echo "UserDir: " . $this->input->get('dir') . "\n";
            echo "ActiveFile: " . $this->input->get('file') . "\n";
            echo "</pre>";
        }
    }

    /**
     * Dynamically fetch all profiles and map names to IDs.
     * Returns associative array ['PROFILE_NAME' => ID]
     */
    private function get_role_map() {
        $query = $this->db->query("SELECT id_profile, profile_name FROM profile");
        $map = [];
        if ($query) {
            foreach ($query->result() as $row) {
                // Normalize to Uppercase for consistent lookup
                $map[strtoupper(trim($row->profile_name))] = $row->id_profile;
            }
        }
        return $map;
    }

    /**
     * Barrier: Secure Gate for Developer-Only Tools.
     * Blocks access if not in Dev Env or Whitelisted IP.
     */
    private function _check_dev_access() {
        $whitelist = ['127.0.0.1', '::1', 'localhost'];
        // Allow Office VPN IPs if needed in future
        
        $isDevEnv = (defined('ENVIRONMENT') && ENVIRONMENT === 'development');
        $isLocal = in_array($_SERVER['REMOTE_ADDR'], $whitelist);
        
        if (!$isLocal && !$isDevEnv) {
            header('HTTP/1.0 403 Forbidden');
            echo json_encode(['error' => 'Access Denied: Developer Tools are locked in Production.']);
            exit;
        }
    }

    public function index() {
        $userDir = $this->input->get('dir');
        $activeFile = $this->input->get('file');
        $profile_id = $this->session->userdata('profile');

        // Dynamic Role Lookup
        
        // Dynamic Role Lookup
        $roles = $this->get_role_map();

        // Define Static Role Names (that exist in DB)
        $super_admin_id = $roles['SUPERADMIN'] ?? 0;
        $admin_id       = $roles['ADMIN'] ?? 0;

        $allowed_dirs = [];

        // 1. 'modules' - Base Knowledge Base - Accessible to EVERYONE
        $allowed_dirs[] = 'modules';

        // 2. Permission Rules
        
        // RULE 1: SUPERADMIN can access EVERYTHING
        if ($profile_id == $super_admin_id) {
            $allowed_dirs[] = 'management';
            $allowed_dirs[] = 'developer';
            $allowed_dirs[] = 'client_docs';
            $allowed_dirs[] = 'support_docs';
        }
        // RULE 2: ADMIN can access Support and Client
        elseif ($profile_id == $admin_id) {
            $allowed_dirs[] = 'client_docs';
            $allowed_dirs[] = 'support_docs';
            // Explicitly NOT adding management/developer
        }
        // RULE 3: OTHERS can access ONLY Client (and core modules)
        else {
            $allowed_dirs[] = 'client_docs';
        }

        // ---------------------------------------------------------

        // Path Sanitization & Access Check
        $userDir = str_replace('..', '', $userDir);
        
        // Ensure user is not trying to access a restricted top-level folder
        $topLevelDir = explode('/', trim($userDir, '/'))[0];
        if (!empty($userDir) && !in_array($topLevelDir, $allowed_dirs)) {
            // If trying to access a restricted folder, reset to root
            $userDir = '';
        }

        $currentPath = rtrim($this->kb_root . '/' . $userDir, '/');

        // Security check: Ensure we are inside KB root
        if (strpos(realpath($currentPath), $this->kb_root) !== 0) {
            $currentPath = $this->kb_root;
            $userDir = '';
        }

        // Scan Directory
        $files = scandir($currentPath);
        $dirs = [];
        $mdFiles = [];

        foreach ($files as $f) {
            if ($f === '.' || $f === '..') continue;
            $fullPath = $currentPath . '/' . $f;
            
            if (is_dir($fullPath)) {
                // If at root, only show allowed dirs
                if (empty($userDir)) {
                    if (in_array($f, $allowed_dirs)) {
                        $dirs[] = $f;
                    }
                } else {
                    // Inside a subfolder, show all sub-subfolders
                    $dirs[] = $f; 
                }
            } elseif (pathinfo($f, PATHINFO_EXTENSION) === 'md') {
                $mdFiles[] = $f;
            }
        }

        // Sort
        usort($mdFiles, function($a, $b) {
            if ($a === 'MASTER_INDEX.md') return -1;
            if ($b === 'MASTER_INDEX.md') return 1;
            return strcasecmp($a, $b);
        });

        // Get Content if file selected
        $content = '';
        $filePath = null;
        if ($activeFile) {
            $filePath = $currentPath . '/' . $activeFile;
            
            // Double check access before serving file
            $fileTopLevel = explode('/', trim($userDir, '/'))[0];
             if (!empty($userDir) && !in_array($fileTopLevel, $allowed_dirs)) {
                 $content = "Access Denied.";
             } elseif (file_exists($filePath) && strpos(realpath($filePath), $this->kb_root) === 0) {
                $content = file_get_contents($filePath);
            }
        }

        // Prepare Data for View
        $data = [
            'dirs' => $dirs,
            'mdFiles' => $mdFiles,
            'userDir' => $userDir,
            'activeFile' => $activeFile,
            'content' => $content,
            'base_url' => base_url(),
            // 'role_debug' => "Me: $profile_id | SA: $super_admin_id | AD: $admin_id" // Debug
        ];

        // Load View
        $this->load->view('knowledge_base/viewer', $data);
    }

    public function search_impact() {
        $this->_check_dev_access(); 

        $term = $this->input->get('term');
        if (!$term) {
            echo json_encode(['error' => 'No search term provided']);
            return;
        }

        // --- UNIVERSAL INDEXER INTEGRATION ---
        // Future: Detect module from term or context. For now, defaulting to 'estimation'.
        $indexFile = FCPATH . 'knowledge_base/indexes/estimation_index.json';
        $jsFile = FCPATH . 'assets/js/ret_estimation.js';

        if (!file_exists($indexFile)) {
             // Fallback or Error
             echo json_encode(['error' => 'Index not found. Please run: node admin/tools/indexer/engine.js --module=estimation']);
             return;
        }

        $index = json_decode(file_get_contents($indexFile), true);
        $fileContent = file_get_contents($jsFile); 
        $lines = explode("\n", $fileContent);

        $results = [
            'term' => $term,
            'functions' => [],
            'calls' => [], // To be implemented in Phase 2 of Indexer (Call Graph)
            'ui_matches' => []
        ];

        // 1. Check if Term is a Known Function (Exact Match in Index)
        // The index stores: "funcName": { file, line, type }
        if (isset($index['functions'][$term])) {
            $meta = $index['functions'][$term];
            $results['functions'][] = [
                'name' => $term,
                'line' => $meta['line'],
                'file' => $meta['file']
            ];
        }

        // 2. Scan Text for Usage (Context)
        // We still scan lines for the "Term", but we use the Index to identify "Where am I?"
        // Optimizing: We can iterate only the list of functions if we want, but we need line numbers.
        // Let's do a fast scan.
        foreach ($lines as $ln => $line) {
            if (stripos($line, $term) !== false) {
                $lineNum = $ln + 1;
                
                // Which function is this line in?
                // We need a "Line Map" for O(1) lookup. 
                // Since the JSON is structure-based, we can find the "closest previous function line".
                // Ideally, the Indexer should provide a "Ranges" map suitable for binary search.
                // For this POC, let's reverse-search the 'functions' array in RAM? No, purely structure.
                
                // Fast "Current Function" tracker using the Index
                // Actually, let's just do a simple greedy search on the sorted function list from the index?
                // Optimization: The index is Key-Value. We need Value-sorted.
                // Re-calculating "Current Function" here avoids complex Index logic for now.
                // But wait, the previous simple regex was doing exactly this.
                // Let's use the Index mainly for Definitive Function Locations and API Endpoints.
                
                $funcName = 'GLOBAL';
                // (Optional: Logic to find enclosing function using $index['functions'] ranges)
                
                $results['ui_matches'][] = [
                    'func' => $funcName, // We can enhance this later with Range Lookup
                    'line' => $lineNum,
                    'context' => trim(substr($line, 0, 100))
                ];
            }
        }
        
        // 3. Dependency Graph (Phase 2: Use the Index)
        // Find everything that Calls $term or is Called by $term
        if (isset($index['calls']) && is_array($index['calls'])) {
             foreach ($index['calls'] as $call) {
                 // Forward Link: Term -> Calls -> Target
                 if ($call['source'] === $term) {
                      $results['calls'][] = [
                        'type' => 'calls',
                        'target' => $call['target'],
                        'line' => $call['line']
                      ];
                 }
                 // Reverse Link: Source -> Calls -> Term
                 if ($call['target'] === $term) {
                     // For visualization, we might want to show "Called By"
                     // The viewer.php logic handles outgoing calls currently.
                     // Let's add them as 'called_by' or reuse 'calls' with a type?
                     // For now, let's just stick to explicit outgoing calls to keep the graph simple (Impact going Downstream)
                     // Actually, if I change Term, I want to know who calls ME too.
                     $results['calls'][] = [
                        'type' => 'called_by',
                        'target' => $call['source'], // In graph: Source --> Term
                        'line' => $call['line']
                      ];
                 }
             }
        }
        
        // 4. API Endpoints (PHP)
        if (isset($index['api_endpoints'][$term])) {
             $meta = $index['api_endpoints'][$term];
             $results['functions'][] = [ // Treat as function for visualization
                'name' => 'PHP: ' . $term,
                'line' => $meta['line'],
                'file' => $meta['file']
             ];
        }
        
        // 5. Level 2 Dependencies (2-hop depth)
        // For each direct dependency, find what IT calls/is called by
        $results['level2'] = [];
        $directFuncs = array_unique(array_column($results['calls'], 'target'));
        
        foreach ($directFuncs as $func) {
            $l2 = ['calls' => [], 'called_by' => []];
            
            if (isset($index['calls']) && is_array($index['calls'])) {
                foreach ($index['calls'] as $call) {
                    // What does $func call?
                    if ($call['source'] === $func && $call['target'] !== $term) {
                        $l2['calls'][] = $call['target'];
                    }
                    // What calls $func (besides $term)?
                    if ($call['target'] === $func && $call['source'] !== $term) {
                        $l2['called_by'][] = $call['source'];
                    }
                }
            }
            
            // Only include if has level 2 connections
            if (!empty($l2['calls']) || !empty($l2['called_by'])) {
                $results['level2'][$func] = [
                    'calls' => array_unique($l2['calls']),
                    'called_by' => array_unique($l2['called_by'])
                ];
            }
        }

        echo json_encode($results);

    }

    public function submit_change_request() {
        $this->_check_dev_access(); // LOCK THIS

        // Read JSON Input
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (!$data || !isset($data['title'])) {
            $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode(['error' => 'Invalid Request Data']));
            return;
        }

        // Create Ticket ID
        $ticketId = 'REQ-' . date('Ymd-His');
        $filename = FCPATH . '../knowledge_base/tickets/' . $ticketId . '.json';

        // Add Metadata
        $data['id'] = $ticketId;
        $data['status'] = 'PENDING_ANALYSIS';
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['ai_plan'] = null; // Placeholder for Analysis

        // Save to File
        if (file_put_contents($filename, json_encode($data, JSON_PRETTY_PRINT))) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => true, 
                    'ticket_id' => $ticketId,
                    'message' => 'Request saved. AI Agent notified.'
                ]));
        } else {
            $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode(['error' => 'Failed to save ticket file']));
        }
    }
}
