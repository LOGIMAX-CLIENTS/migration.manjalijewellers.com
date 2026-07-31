<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Log Viewer Controller
 * Provides interface and APIs to list, search, view, and download log files.
 */
class Log extends CI_Controller {

    public function __construct() {
        parent::__construct();
        
        // Ensure user is logged in
        if (!$this->session->userdata('is_logged')) {
            if ($this->input->is_ajax_request() || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')) {
                header('Content-Type: application/json');
                echo json_encode(['status' => false, 'message' => 'Session expired. Please refresh and log in again.']);
                exit;
            }
            redirect('admin/login');
        }
    }

    /**
     * Get root log directory (parent of FCPATH)
     */
    private function get_root_log_dir() {
        return dirname(rtrim(FCPATH, '/\\')) . DIRECTORY_SEPARATOR . 'log';
    }

    /**
     * Get admin log directory (FCPATH/log)
     */
    private function get_admin_log_dir() {
        return rtrim(FCPATH, '/\\') . DIRECTORY_SEPARATOR . 'log';
    }

    /**
     * Index action - loads the main log viewer interface
     */
    public function index() {
        $data['title'] = 'System Log Viewer';
        $data['main_content'] = 'log/file_viewer';
        $this->load->view('layout/template', $data);
    }

    /**
     * AJAX action - returns log parts and files for a specific date
     */
    public function ajax_get_logs_by_date() {
        $date = $this->input->post('date');
        if (empty($date)) {
            header('Content-Type: application/json');
            echo json_encode(['status' => false, 'message' => 'Date is required.']);
            return;
        }

        // Validate date format YYYY-MM-DD or DD-MM-YYYY
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) && !preg_match('/^\d{2}-\d{2}-\d{4}$/', $date)) {
            header('Content-Type: application/json');
            echo json_encode(['status' => false, 'message' => 'Invalid date format.']);
            return;
        }

        try {
            $result = $this->get_logs_for_date($date);
            header('Content-Type: application/json');
            echo json_encode(array_merge(['status' => true], $result));
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['status' => false, 'message' => 'Server error: ' . $e->getMessage()]);
        }
    }

    /**
     * AJAX action - returns contents of a specific log file
     */
    public function ajax_get_file_content() {
        $key = $this->input->post('key');
        if (empty($key)) {
            header('Content-Type: application/json');
            echo json_encode(['status' => false, 'message' => 'File key is missing.']);
            return;
        }

        $decoded = base64_decode($key);
        if ($decoded === FALSE || strpos($decoded, ':') === false) {
            header('Content-Type: application/json');
            echo json_encode(['status' => false, 'message' => 'Invalid file key.']);
            return;
        }

        list($source, $relative_path) = explode(':', $decoded, 2);

        // Resolve base path
        if ($source === 'Root') {
            $base_dir = $this->get_root_log_dir();
        } elseif ($source === 'Admin') {
            $base_dir = $this->get_admin_log_dir();
        } else {
            header('Content-Type: application/json');
            echo json_encode(['status' => false, 'message' => 'Invalid source.']);
            return;
        }

        $file_path = realpath($base_dir . DIRECTORY_SEPARATOR . $relative_path);

        if ($file_path === FALSE || !$this->is_valid_log_path($file_path)) {
            header('Content-Type: application/json');
            echo json_encode(['status' => false, 'message' => 'Access denied or file not found.']);
            return;
        }

        $size = @filesize($file_path);
        if ($size === FALSE) {
            $size = 0;
        }
        $content = '';
        $truncated = false;

        // Limit maximum size read directly into memory (2MB)
        if ($size > 2 * 1024 * 1024) {
            $content = $this->read_file_tail($file_path, 2000);
            $truncated = true;
        } else {
            $content = @file_get_contents($file_path);
            if ($content === FALSE) {
                $content = '';
            }
        }

        // UTF-8 encoding convert if needed
        if (!mb_check_encoding($content, 'UTF-8')) {
            $content = mb_convert_encoding($content, 'UTF-8', 'ISO-8859-1');
        }

        header('Content-Type: application/json');
        echo json_encode([
            'status' => true,
            'filename' => basename($file_path),
            'content' => htmlspecialchars($content, ENT_QUOTES, 'UTF-8'),
            'size' => $this->format_size($size),
            'truncated' => $truncated,
            'path' => $source . '/' . $relative_path
        ]);
    }

    /**
     * Download a log file securely
     */
    public function download_file($key) {
        if (empty($key)) {
            show_error('Missing file key.', 400);
        }

        $decoded = base64_decode($key);
        if ($decoded === FALSE || strpos($decoded, ':') === false) {
            show_error('Invalid file key.', 400);
        }

        list($source, $relative_path) = explode(':', $decoded, 2);

        if ($source === 'Root') {
            $base_dir = $this->get_root_log_dir();
        } elseif ($source === 'Admin') {
            $base_dir = $this->get_admin_log_dir();
        } else {
            show_error('Invalid source.', 400);
        }

        $file_path = realpath($base_dir . DIRECTORY_SEPARATOR . $relative_path);

        if ($file_path === FALSE || !$this->is_valid_log_path($file_path)) {
            show_error('Access denied or file not found.', 403);
        }

        $this->load->helper('download');
        force_download($file_path, NULL);
    }

    /**
     * Validate if file path is within authorized directories
     */
    private function is_valid_log_path($path) {
        $real_path = realpath($path);
        if ($real_path === FALSE) {
            return FALSE;
        }

        $root_log = realpath($this->get_root_log_dir());
        $admin_log = realpath($this->get_admin_log_dir());

        if (($root_log !== FALSE && strpos($real_path, $root_log) === 0) ||
            ($admin_log !== FALSE && strpos($real_path, $admin_log) === 0)) {
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Helper to retrieve and group logs for a specific date
     */
    private function get_logs_for_date($date) {
        $parts = [];
        $files_by_part = [];

        $root_base = $this->get_root_log_dir() . DIRECTORY_SEPARATOR . $date;
        $admin_base = $this->get_admin_log_dir() . DIRECTORY_SEPARATOR . $date;

        if (is_dir($root_base)) {
            $this->scan_date_subfolders($root_base, 'Root', $date, $parts, $files_by_part);
        }

        if (is_dir($admin_base)) {
            $this->scan_date_subfolders($admin_base, 'Admin', $date, $parts, $files_by_part);
        }

        $parts = array_unique($parts);
        sort($parts);

        return [
            'parts' => array_values($parts),
            'files' => $files_by_part
        ];
    }

    /**
     * Scans subdirectories (log parts) under a date directory
     */
    private function scan_date_subfolders($dir, $source, $date, &$parts, &$files_by_part) {
        if (!is_dir($dir) || !is_readable($dir)) {
            return;
        }

        $items = @scandir($dir);
        if ($items === FALSE) {
            return;
        }

        // 1. Check for log files directly in the date folder (e.g. log/2026-07-29/some_file.txt)
        $has_direct_files = false;
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;
            $item_path = $dir . DIRECTORY_SEPARATOR . $item;
            if (@is_file($item_path)) {
                $has_direct_files = true;
                break;
            }
        }

        if ($has_direct_files) {
            if (!in_array('general', $parts)) {
                $parts[] = 'general';
            }
            if (!isset($files_by_part['general'])) {
                $files_by_part['general'] = [];
            }
            foreach ($items as $item) {
                if ($item === '.' || $item === '..') continue;
                $item_path = $dir . DIRECTORY_SEPARATOR . $item;
                if (@is_file($item_path)) {
                    $ext = strtolower(pathinfo($item, PATHINFO_EXTENSION));
                    if (in_array($ext, ['txt', 'log', 'json'])) {
                        $relative_path = $date . '/' . $item;
                        $size = @filesize($item_path);
                        $mtime = @filemtime($item_path);

                        $files_by_part['general'][] = [
                            'name' => $item,
                            'relative_path' => $relative_path,
                            'key' => base64_encode($source . ':' . $relative_path),
                            'source' => $source,
                            'size' => $this->format_size($size !== FALSE ? $size : 0),
                            'modified_time' => date('Y-m-d H:i:s', $mtime !== FALSE ? $mtime : time()),
                            'modified_time_raw' => $mtime !== FALSE ? $mtime : 0,
                        ];
                    }
                }
            }
        }

        // 2. Scan subdirectory folders (e.g. log/2026-07-27/existing/)
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;

            $item_path = $dir . DIRECTORY_SEPARATOR . $item;
            if (@is_dir($item_path)) {
                if (!in_array($item, $parts)) {
                    $parts[] = $item;
                }
                if (!isset($files_by_part[$item])) {
                    $files_by_part[$item] = [];
                }

                $subfiles = @scandir($item_path);
                if ($subfiles === FALSE) continue;

                foreach ($subfiles as $file) {
                    if ($file === '.' || $file === '..') continue;

                    $file_path = $item_path . DIRECTORY_SEPARATOR . $file;
                    if (@is_file($file_path)) {
                        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                        if (in_array($ext, ['txt', 'log', 'json'])) {
                            $relative_path = $date . '/' . $item . '/' . $file;
                            $size = @filesize($file_path);
                            $mtime = @filemtime($file_path);

                            $files_by_part[$item][] = [
                                'name' => $file,
                                'relative_path' => $relative_path,
                                'key' => base64_encode($source . ':' . $relative_path),
                                'source' => $source,
                                'size' => $this->format_size($size !== FALSE ? $size : 0),
                                'modified_time' => date('Y-m-d H:i:s', $mtime !== FALSE ? $mtime : time()),
                                'modified_time_raw' => $mtime !== FALSE ? $mtime : 0,
                            ];
                        }
                    }
                }

                // Sort files in this part by modified time DESC
                if (!empty($files_by_part[$item])) {
                    usort($files_by_part[$item], function($a, $b) {
                        return $b['modified_time_raw'] - $a['modified_time_raw'];
                    });
                }
            }
        }

        if (isset($files_by_part['general']) && !empty($files_by_part['general'])) {
            usort($files_by_part['general'], function($a, $b) {
                return $b['modified_time_raw'] - $a['modified_time_raw'];
            });
        }
    }

    /**
     * Tail implementation for reading large files efficiently
     */
    private function read_file_tail($filepath, $lines = 1000) {
        $f = @fopen($filepath, "rb");
        if (!$f) return '';

        $buffer = 4096;
        fseek($f, 0, SEEK_END);
        $pos = ftell($f);
        $output = '';
        $line_count = 0;

        while ($pos > 0 && $line_count <= $lines) {
            $seek_pos = max(0, $pos - $buffer);
            $read_size = $pos - $seek_pos;
            fseek($f, $seek_pos);
            $chunk = fread($f, $read_size);
            
            $line_count += substr_count($chunk, "\n");
            $output = $chunk . $output;
            $pos = $seek_pos;
        }

        fclose($f);

        $all_lines = explode("\n", $output);
        if (count($all_lines) > $lines) {
            $all_lines = array_slice($all_lines, -$lines);
            $output = implode("\n", $all_lines);
        }

        return $output;
    }

    /**
     * Helper to format bytes into readable file size
     */
    private function format_size($bytes) {
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            return $bytes . ' bytes';
        } elseif ($bytes == 1) {
            return '1 byte';
        } else {
            return '0 bytes';
        }
    }
}

