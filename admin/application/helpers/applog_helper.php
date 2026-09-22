<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Admin-side loader for the shared applog helper.
 *
 * CodeIgniter 2 only looks for helpers under its own APPPATH, and the admin app and
 * the customer app are two separate CI instances. Rather than keep two copies of the
 * logging logic in sync, this shim requires the single canonical implementation in
 * <webroot>/application/helpers/applog_helper.php.
 */

$applog_candidates = array(
    // <webroot>/application/helpers/ — from the admin front controller (FCPATH = <webroot>/admin/)
    rtrim(str_replace('\\', '/', dirname(rtrim(FCPATH, '/\\'))), '/') . '/application/helpers/applog_helper.php',
    // Same target derived from APPPATH (<webroot>/admin/application/), as a second route
    rtrim(str_replace('\\', '/', APPPATH), '/\\') . '/../../application/helpers/applog_helper.php',
);

$applog_loaded = false;
foreach ($applog_candidates as $applog_file) {
    if (is_file($applog_file)) {
        require_once $applog_file;
        $applog_loaded = true;
        break;
    }
}
unset($applog_candidates, $applog_file);

if (!$applog_loaded) {
    // Never let logging break a request: degrade to no-ops that leave a trace in the
    // framework log so the broken deployment layout is visible.
    log_message('error', 'applog: shared helper not found; logging disabled for this request');

    if (!function_exists('applog_write')) {
        function applog_write($channel, $event, $data = null, $opts = array()) { return false; }
    }
    if (!function_exists('applog_source')) {
        function applog_source() { return 'From Unknown Trigger'; }
    }
    if (!function_exists('applog_path')) {
        function applog_path($channel, $file = null, $date = null) { return ''; }
    }
    if (!function_exists('applog_dir')) {
        function applog_dir($channel, $date = null) { return ''; }
    }
    if (!function_exists('applog_channel_label')) {
        function applog_channel_label($channel) { return $channel; }
    }
    if (!function_exists('applog_channels')) {
        function applog_channels() { return array(); }
    }
}
unset($applog_loaded);
