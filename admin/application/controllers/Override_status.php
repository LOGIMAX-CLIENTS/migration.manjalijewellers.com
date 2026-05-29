<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Override Status — Diagnostic Controller
 *
 * Endpoint: /admin/index.php/override_status
 * Shows override system status, active client config, feature packs,
 * and file resolution paths for debugging.
 */
class Override_status extends CI_Controller
{
    public function index()
    {
        $client_id   = get_client_id();
        $config      = get_client_config();
        $repo_root   = realpath(FCPATH . '..') . DIRECTORY_SEPARATOR;
        $overrides_enabled = defined('ENABLE_OVERRIDES') && ENABLE_OVERRIDES;

        // Scan for override files
        $override_files = $this->_scan_overrides($client_id, $repo_root);
        $feature_files  = $this->_scan_features($config, $repo_root);

        // Build the output
        $html = $this->_render_page($client_id, $config, $overrides_enabled, $override_files, $feature_files, $repo_root);
        echo $html;
    }

    /**
     * Resolve a specific file path and show which layer it comes from
     * Usage: /override_status/resolve?file=views/welcome_message.php
     */
    public function resolve()
    {
        $file = $this->input->get('file');
        if (empty($file)) {
            echo json_encode(['status' => false, 'msg' => 'Missing ?file= parameter']);
            return;
        }

        $client_id = get_client_id();
        $config    = get_client_config();
        $repo_root = realpath(FCPATH . '..') . DIRECTORY_SEPARATOR;

        $resolution = [];

        // Layer 1: Client
        $client_path = $repo_root . 'clients/' . $client_id . '/' . $file;
        $resolution[] = [
            'layer'  => 'Client (' . $client_id . ')',
            'path'   => 'clients/' . $client_id . '/' . $file,
            'exists' => file_exists($client_path),
            'winner' => false
        ];

        // Layer 2: Features
        if ( ! empty($config['features'])) {
            foreach ($config['features'] as $feature) {
                $feature_path = $repo_root . 'features/' . $feature . '/' . $file;
                $resolution[] = [
                    'layer'  => 'Feature (' . $feature . ')',
                    'path'   => 'features/' . $feature . '/' . $file,
                    'exists' => file_exists($feature_path),
                    'winner' => false
                ];
            }
        }

        // Layer 3: Core
        $core_path = APPPATH . $file;
        $resolution[] = [
            'layer'  => 'Core',
            'path'   => 'admin/application/' . $file,
            'exists' => file_exists($core_path),
            'winner' => false
        ];

        // Mark the winner (first found)
        foreach ($resolution as &$entry) {
            if ($entry['exists']) {
                $entry['winner'] = true;
                break;
            }
        }
        unset($entry);

        echo json_encode(['status' => true, 'file' => $file, 'resolution' => $resolution], JSON_PRETTY_PRINT);
    }

    /**
     * Scan client override directory for files
     */
    private function _scan_overrides($client_id, $repo_root)
    {
        $files = [];
        $client_dir = $repo_root . 'clients/' . $client_id;

        if ( ! is_dir($client_dir)) {
            return $files;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($client_dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $rel_path = str_replace($client_dir . DIRECTORY_SEPARATOR, '', $file->getPathname());
                $rel_path = str_replace('\\', '/', $rel_path);
                $files[] = $rel_path;
            }
        }

        sort($files);
        return $files;
    }

    /**
     * Scan feature pack directories for files
     */
    private function _scan_features($config, $repo_root)
    {
        $features = [];
        if (empty($config['features'])) {
            return $features;
        }

        foreach ($config['features'] as $feature) {
            $feature_dir = $repo_root . 'features/' . $feature;
            $files = [];

            if (is_dir($feature_dir)) {
                $iterator = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($feature_dir, RecursiveDirectoryIterator::SKIP_DOTS),
                    RecursiveIteratorIterator::LEAVES_ONLY
                );

                foreach ($iterator as $file) {
                    if ($file->isFile()) {
                        $rel_path = str_replace($feature_dir . DIRECTORY_SEPARATOR, '', $file->getPathname());
                        $rel_path = str_replace('\\', '/', $rel_path);
                        $files[] = $rel_path;
                    }
                }
                sort($files);
            }

            $features[$feature] = $files;
        }

        return $features;
    }

    /**
     * Render the diagnostic HTML page
     */
    private function _render_page($client_id, $config, $enabled, $override_files, $feature_files, $repo_root)
    {
        $client_name = isset($config['client_name']) ? $config['client_name'] : 'Unknown';
        $flags_json  = isset($config['flags']) ? json_encode($config['flags'], JSON_PRETTY_PRINT) : '{}';
        $features    = isset($config['features']) ? $config['features'] : [];

        $status_color = $enabled ? '#00e676' : '#ff5252';
        $status_text  = $enabled ? 'ENABLED' : 'DISABLED';

        $html = '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Override Status — ' . htmlspecialchars($client_id) . '</title>';
        $html .= '<style>
            body { background:#0d1117; color:#c9d1d9; font-family:"Segoe UI",sans-serif; margin:0; padding:20px; }
            .container { max-width:900px; margin:0 auto; }
            h1 { color:#58a6ff; font-size:24px; border-bottom:1px solid #30363d; padding-bottom:12px; }
            h2 { color:#79c0ff; font-size:16px; margin-top:28px; }
            .card { background:#161b22; border:1px solid #30363d; border-radius:8px; padding:16px; margin:12px 0; }
            .status-badge { display:inline-block; padding:4px 12px; border-radius:12px; font-weight:bold; font-size:13px; }
            .grid { display:grid; grid-template-columns:160px 1fr; gap:6px 16px; }
            .label { color:#8b949e; font-size:13px; }
            .value { color:#58a6ff; font-size:13px; }
            .file-list { list-style:none; padding:0; margin:0; }
            .file-list li { padding:4px 8px; border-bottom:1px solid #21262d; font-family:Consolas,monospace; font-size:12px; color:#7ee787; }
            .file-list li:last-child { border-bottom:none; }
            pre { background:#0d1117; border:1px solid #30363d; padding:12px; border-radius:4px; overflow-x:auto; font-size:12px; color:#ffd740; }
            .empty { color:#8b949e; font-style:italic; }
            .feature-tag { display:inline-block; background:#1f6feb; color:white; padding:2px 8px; border-radius:10px; font-size:11px; margin:2px; }
            .resolve-form { margin-top:16px; }
            .resolve-form input { background:#0d1117; border:1px solid #30363d; color:#c9d1d9; padding:8px 12px; border-radius:4px; width:60%; font-family:Consolas,monospace; }
            .resolve-form button { background:#238636; color:white; border:none; padding:8px 16px; border-radius:4px; cursor:pointer; margin-left:8px; }
            .resolve-form button:hover { background:#2ea043; }
        </style></head><body><div class="container">';

        // Header
        $html .= '<h1>🔍 Override System Status</h1>';

        // System Status card
        $html .= '<div class="card">';
        $html .= '<div class="grid">';
        $html .= '<span class="label">Override System</span>';
        $html .= '<span><span class="status-badge" style="background:' . $status_color . ';color:#000;">' . $status_text . '</span></span>';
        $html .= '<span class="label">Client ID</span><span class="value">' . htmlspecialchars($client_id) . '</span>';
        $html .= '<span class="label">Client Name</span><span class="value">' . htmlspecialchars($client_name) . '</span>';
        $html .= '<span class="label">Config File</span><span class="value">config/clients/' . htmlspecialchars($client_id) . '.json</span>';
        $html .= '</div></div>';

        // Feature Packs
        $html .= '<h2>📦 Feature Packs</h2><div class="card">';
        if (empty($features)) {
            $html .= '<span class="empty">No feature packs enabled</span>';
        } else {
            foreach ($features as $f) {
                $html .= '<span class="feature-tag">' . htmlspecialchars($f) . '</span> ';
            }
        }
        $html .= '</div>';

        // Flags
        $html .= '<h2>🏷️ Config Flags</h2><div class="card">';
        if (empty($config['flags'])) {
            $html .= '<span class="empty">No flags set</span>';
        } else {
            $html .= '<pre>' . htmlspecialchars($flags_json) . '</pre>';
        }
        $html .= '</div>';

        // Override Files
        $html .= '<h2>📂 Client Override Files (' . count($override_files) . ')</h2><div class="card">';
        if (empty($override_files)) {
            $html .= '<span class="empty">No override files for this client</span>';
        } else {
            $html .= '<ul class="file-list">';
            foreach ($override_files as $f) {
                $html .= '<li>clients/' . htmlspecialchars($client_id) . '/' . htmlspecialchars($f) . '</li>';
            }
            $html .= '</ul>';
        }
        $html .= '</div>';

        // Feature Pack Files
        if ( ! empty($feature_files)) {
            foreach ($feature_files as $feature_name => $files) {
                $html .= '<h2>📦 Feature: ' . htmlspecialchars($feature_name) . ' (' . count($files) . ' files)</h2><div class="card">';
                $html .= '<ul class="file-list">';
                foreach ($files as $f) {
                    $html .= '<li>features/' . htmlspecialchars($feature_name) . '/' . htmlspecialchars($f) . '</li>';
                }
                $html .= '</ul></div>';
            }
        }

        // Resolution Tester
        $html .= '<h2>🔎 File Resolution Tester</h2><div class="card">';
        $html .= '<p style="font-size:13px;color:#8b949e;">Enter a relative file path to see which layer it resolves from:</p>';
        $html .= '<div class="resolve-form">';
        $html .= '<input type="text" id="resolve-input" placeholder="views/welcome_message.php" />';
        $html .= '<button onclick="resolveFile()">Resolve</button>';
        $html .= '</div>';
        $html .= '<div id="resolve-result" style="margin-top:12px;"></div>';
        $html .= '</div>';

        // JavaScript for resolution tester
        $html .= '<script>
        function resolveFile() {
            var file = document.getElementById("resolve-input").value;
            if (!file) return;
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "' . site_url('override_status/resolve') . '?file=" + encodeURIComponent(file));
            xhr.onload = function() {
                var data = JSON.parse(xhr.responseText);
                var html = "";
                if (data.resolution) {
                    data.resolution.forEach(function(r) {
                        var icon = r.exists ? (r.winner ? "✅" : "📄") : "❌";
                        var color = r.winner ? "#7ee787" : (r.exists ? "#ffd740" : "#8b949e");
                        var label = r.winner ? " ← WINNER" : "";
                        html += "<div style=\"padding:4px 0;color:" + color + ";font-family:Consolas,monospace;font-size:12px;\">" + icon + " " + r.layer + ": " + r.path + (r.exists ? " (exists)" : " (not found)") + label + "</div>";
                    });
                }
                document.getElementById("resolve-result").innerHTML = html;
            };
            xhr.send();
        }
        document.getElementById("resolve-input").addEventListener("keyup", function(e) { if (e.key === "Enter") resolveFile(); });
        </script>';

        $html .= '<p style="text-align:center;color:#30363d;margin-top:40px;font-size:11px;">Three-Layer Override System — Diagnostic Endpoint</p>';
        $html .= '</div></body></html>';

        return $html;
    }
}

/* End of file Override_status.php */
/* Location: ./application/controllers/Override_status.php */
