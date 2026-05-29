<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Client Config Helper
 *
 * Standalone functions for override system config access.
 * Available everywhere (controllers, models, views) via autoload.
 *
 * Part of the Three-Layer Override System.
 */

// ---------------------------------------------------------------------------
// CLIENT_ID Access
// ---------------------------------------------------------------------------

/**
 * Get current CLIENT_ID constant
 *
 * @return string  CLIENT_ID or 'default'
 */
function get_client_id()
{
    return defined('CLIENT_ID') ? CLIENT_ID : 'default';
}

// ---------------------------------------------------------------------------
// Client Config (JSON) Access — with static cache
// ---------------------------------------------------------------------------

/**
 * Load and cache client config from config/clients/{CLIENT_ID}.json
 *
 * @return array  Full config array (empty array if file missing)
 */
function get_client_config()
{
    static $config_cache = null;

    if ($config_cache !== null) {
        return $config_cache;
    }

    $client_id = get_client_id();
    // FCPATH points to admin/ — config dir is one level up in the repo root
    $config_file = realpath(FCPATH . '..') . '/config/clients/' . $client_id . '.json';

    if ( ! file_exists($config_file)) {
        log_message('debug', 'Override: No config file found at ' . $config_file . ', using empty config');
        $config_cache = [];
        return $config_cache;
    }

    $json = file_get_contents($config_file);
    $config_cache = json_decode($json, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        log_message('error', 'Override: JSON parse error in ' . $config_file . ': ' . json_last_error_msg());
        $config_cache = [];
        return $config_cache;
    }

    log_message('debug', 'Override: Loaded config for client "' . $client_id . '" from ' . $config_file);
    return $config_cache;
}

// ---------------------------------------------------------------------------
// Flag Access
// ---------------------------------------------------------------------------

/**
 * Read a specific flag value from the client config
 *
 * @param  string $key      Flag key name
 * @param  mixed  $default  Default value if flag not set
 * @return mixed
 */
function get_flag($key, $default = null)
{
    $config = get_client_config();

    if (isset($config['flags']) && array_key_exists($key, $config['flags'])) {
        return $config['flags'][$key];
    }

    return $default;
}

// ---------------------------------------------------------------------------
// Feature Pack Check
// ---------------------------------------------------------------------------

/**
 * Check if a feature pack is enabled for current client
 *
 * @param  string $name  Feature pack name (e.g. 'wastage_display')
 * @return bool
 */
function has_feature($name)
{
    $config = get_client_config();
    return isset($config['features']) && in_array($name, $config['features']);
}

// ---------------------------------------------------------------------------
// Client Override Path
// ---------------------------------------------------------------------------

/**
 * Get the client override directory path for a given type
 *
 * @param  string $type  One of: controllers, models, views, helpers
 * @return string        Absolute path (may or may not exist)
 */
function get_client_path($type)
{
    $client_id = get_client_id();
    return realpath(FCPATH . '..') . '/clients/' . $client_id . '/' . $type . '/';
}

/**
 * Get the feature pack directory path for a given feature and type
 *
 * @param  string $feature  Feature name (e.g. 'wastage_display')
 * @param  string $type     One of: controllers, models, views, helpers
 * @return string           Absolute path (may or may not exist)
 */
function get_feature_path($feature, $type)
{
    return realpath(FCPATH . '..') . '/features/' . $feature . '/' . $type . '/';
}

// ---------------------------------------------------------------------------
// Asset Override URL (JS, CSS, Images)
// ---------------------------------------------------------------------------

/**
 * Resolve an asset path through the three-layer override system
 *
 * Checks:  1. clients/{CLIENT_ID}/assets/{path}
 *          2. features/{feature}/assets/{path}  (if features enabled)
 *          3. admin/assets/{path}               (core — default)
 *
 * @param  string $path  Relative asset path, e.g. 'js/ret_billing.js' or 'css/style.css'
 * @return string        Full URL to the resolved asset
 */
function get_asset_url($path)
{
    // Kill switch check — skip override resolution
    if ( ! defined('ENABLE_OVERRIDES') || ! ENABLE_OVERRIDES) {
        return base_url('assets/' . $path);
    }

    $client_id = get_client_id();
    $repo_root = realpath(FCPATH . '..') . '/';

    // Layer 1: Client-specific asset
    if ($client_id !== 'default') {
        $client_file = $repo_root . 'clients/' . $client_id . '/assets/' . $path;
        if (file_exists($client_file)) {
            log_message('debug', 'Override resolved: asset "' . $path . '" → clients/' . $client_id . '/assets/' . $path);
            return base_url('../clients/' . $client_id . '/assets/' . $path);
        }
    }

    // Layer 2: Feature pack assets
    $config = function_exists('get_client_config') ? get_client_config() : [];
    if ( ! empty($config['features'])) {
        foreach ($config['features'] as $feature) {
            $feature_file = $repo_root . 'features/' . $feature . '/assets/' . $path;
            if (file_exists($feature_file)) {
                log_message('debug', 'Override resolved: asset "' . $path . '" → features/' . $feature . '/assets/' . $path);
                return base_url('../features/' . $feature . '/assets/' . $path);
            }
        }
    }

    // Layer 3: Core (default)
    return base_url('assets/' . $path);
}

/* End of file client_config_helper.php */
/* Location: ./application/helpers/client_config_helper.php */
